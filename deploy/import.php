<?php
/**
 * Content importer. Run with WP-CLI from the WordPress root:
 *   wp eval-file /path/to/import.php /path/to/content
 *
 * Creates/updates pages listed in content/manifest.json.
 * SAFE BY DEFAULT: if a page was edited in wp-admin after the last import,
 * it is NOT overwritten (logged as "skipped"). Set YROC_FORCE=1 to overwrite.
 */
if ( ! defined( 'WP_CLI' ) ) {
	exit( "Run via WP-CLI.\n" );
}

$dir = rtrim( $args[0] ?? '', '/' );
if ( ! $dir || ! is_file( "$dir/manifest.json" ) ) {
	WP_CLI::error( "manifest.json not found in '$dir'" );
}
$manifest = json_decode( file_get_contents( "$dir/manifest.json" ), true );
if ( ! is_array( $manifest ) ) {
	WP_CLI::error( 'manifest.json is not valid JSON' );
}
$force = getenv( 'YROC_FORCE' ) === '1';

// Run as an administrator so block markup (SVG, canvas, inline HTML) isn't stripped by KSES.
$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
if ( $admins ) {
	wp_set_current_user( (int) $admins[0] );
}
kses_remove_filters();

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';


// Old-site image URL => attachment ID, so images are only downloaded once.
$map = get_option( 'yroc_media_map', array() );
$sideload = function ( $url, $post_id ) use ( &$map ) {
	if ( ! $url ) {
		return 0;
	}
	if ( ! empty( $map[ $url ] ) && get_post( $map[ $url ] ) ) {
		return (int) $map[ $url ];
	}
	$id = media_sideload_image( $url, $post_id, null, 'id' );
	usleep( 250000 ); // be gentle with the old site's firewall
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "image failed $url: " . $id->get_error_message() );
		return 0;
	}
	$map[ $url ] = (int) $id;
	update_option( 'yroc_media_map', $map, false );
	return (int) $id;
};


/** Copy old-site images referenced in content into the media library and rewrite the URLs. */
$localize_images = function ( $content, $post_id ) use ( $sideload ) {
	$pending = false;
	if ( preg_match_all( '#https?://(?:www\.)?yrocconsulting\.com/wp-content/uploads/[^"\'\s)]+\.(?:jpe?g|png|gif|webp)#i', $content, $m ) ) {
		foreach ( array_unique( $m[0] ) as $url ) {
			$aid = $sideload( $url, $post_id );
			if ( $aid ) {
				$content = str_replace( $url, wp_get_attachment_url( $aid ), $content );
			} else {
				$pending = true;
			}
		}
	}
	return array( $content, $pending );
};

$ids = array();

foreach ( $manifest['pages'] ?? array() as $p ) {
	$slug    = $p['slug'];
	$parent  = $p['parent'] ?? '';
	$path    = $parent ? "$parent/$slug" : $slug;
	$content = isset( $p['file'] ) ? file_get_contents( "$dir/{$p['file']}" ) : ( $p['content'] ?? '' );
	if ( false === $content ) {
		WP_CLI::warning( "Missing file for $path" );
		continue;
	}
	$content   = trim( $content );
	$template  = $p['template'] ?? '';
	$src_hash  = md5( $content . '|' . $p['title'] . '|' . $template . '|' . ( $p['excerpt'] ?? '' ) );
	$parent_id = 0;
	if ( $parent ) {
		$pp = get_page_by_path( $parent, OBJECT, 'page' );
		if ( ! $pp ) {
			WP_CLI::warning( "Parent '$parent' not found for $path (list parents first)" );
			continue;
		}
		$parent_id = $pp->ID;
	}

	$existing = get_page_by_path( $path, OBJECT, 'page' );
	$postarr  = array(
		'post_type'    => 'page',
		'post_status'  => $p['status'] ?? 'publish',
		'post_title'   => $p['title'],
		'post_name'    => $slug,
		'post_parent'  => $parent_id,
		'post_content' => wp_slash( $content ),
		'post_excerpt' => wp_slash( $p['excerpt'] ?? '' ),
		'menu_order'   => (int) ( $p['order'] ?? 0 ),
	);

	if ( $existing ) {
		$id = $existing->ID;
		if ( get_post_meta( $id, '_yroc_src_hash', true ) === $src_hash ) {
			$ids[ $path ] = $id;
			if ( false !== strpos( $existing->post_content, 'yrocconsulting.com/wp-content/uploads' ) ) {
				list( $localized ) = $localize_images( $existing->post_content, $id );
				if ( $localized !== $existing->post_content ) {
					wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( $localized ) ) );
					update_post_meta( $id, '_yroc_db_hash', md5( get_post_field( 'post_content', $id, 'raw' ) ) );
					WP_CLI::log( "images     /$path/" );
					continue;
				}
			}
			WP_CLI::log( "unchanged  /$path/" );
			continue;
		}
		$last_import = get_post_meta( $id, '_yroc_db_hash', true );
		$edited      = $last_import && md5( $existing->post_content ) !== $last_import;
		$wp_default  = ! $last_import && 'draft' === $existing->post_status; // e.g. WordPress's sample Privacy Policy draft
		if ( ( $edited || ( ! $last_import && ! $wp_default ) ) && ! $force ) {
			WP_CLI::warning( "skipped    /$path/ (edited in WordPress — run with YROC_FORCE=1 to overwrite)" );
			$ids[ $path ] = $id;
			continue;
		}
		$postarr['ID'] = $id;
		$r = wp_update_post( $postarr, true );
		$verb = 'updated   ';
	} else {
		$r = wp_insert_post( $postarr, true );
		$verb = 'created   ';
	}
	if ( is_wp_error( $r ) ) {
		WP_CLI::warning( "failed     /$path/: " . $r->get_error_message() );
		continue;
	}
	$id = (int) $r;
	list( $localized, $img_pending ) = $localize_images( get_post_field( 'post_content', $id, 'raw' ), $id );
	if ( $localized !== get_post_field( 'post_content', $id, 'raw' ) ) {
		wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( $localized ) ) );
	}
	update_post_meta( $id, '_wp_page_template', $template ? $template : 'default' );
	update_post_meta( $id, '_yroc_src_hash', $src_hash );
	update_post_meta( $id, '_yroc_db_hash', md5( get_post_field( 'post_content', $id, 'raw' ) ) );
	$ids[ $path ] = $id;
	WP_CLI::log( "$verb /$path/" );
}

// Site options.
foreach ( $manifest['options'] ?? array() as $opt => $val ) {
	update_option( $opt, $val );
}

// SEO titles and descriptions (content/seo.json, keyed by URL path). Meta only; never touches content.
if ( is_file( "$dir/seo.json" ) ) {
	$seo = json_decode( file_get_contents( "$dir/seo.json" ), true );
	$n   = 0;
	foreach ( (array) $seo as $path => $vals ) {
		$id = '/' === $path ? ( $ids['home'] ?? 0 ) : ( $ids[ trim( $path, '/' ) ] ?? 0 );
		if ( ! $id ) {
			$pg = get_page_by_path( trim( $path, '/' ), OBJECT, 'page' );
			$id = $pg ? $pg->ID : 0;
		}
		if ( ! $id ) {
			WP_CLI::warning( "seo: no page for $path" );
			continue;
		}
		update_post_meta( $id, '_yroc_seo_title', sanitize_text_field( $vals[0] ) );
		update_post_meta( $id, '_yroc_seo_desc', sanitize_text_field( $vals[1] ) );
		$n++;
	}
	WP_CLI::log( "seo        $n pages" );
}

// Reading settings.
if ( ! empty( $manifest['front_page'] ) && isset( $ids[ $manifest['front_page'] ] ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids[ $manifest['front_page'] ] );
}
if ( ! empty( $manifest['posts_page'] ) && isset( $ids[ $manifest['posts_page'] ] ) ) {
	update_option( 'page_for_posts', $ids[ $manifest['posts_page'] ] );
}

// Remove WordPress default sample content (only if untouched).
$sample = get_page_by_path( 'sample-page', OBJECT, 'page' );
if ( $sample && false !== strpos( $sample->post_content, 'This is an example page' ) ) {
	wp_trash_post( $sample->ID );
	WP_CLI::log( 'trashed    /sample-page/' );
}
$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
if ( $hello && false !== strpos( $hello->post_content, 'Welcome to WordPress' ) ) {
	wp_trash_post( $hello->ID );
	WP_CLI::log( 'trashed    /hello-world/' );
}

// Never let the staging site get indexed.
if ( false !== strpos( home_url(), 'staging' ) || false !== strpos( home_url(), '.yroc.host' ) ) {
	update_option( 'blog_public', '0' );
}

WP_CLI::success( count( $ids ) . ' pages processed.' );

/* =====================================================================
 * Blog posts (content/posts.json)
 * ===================================================================== */
if ( is_file( "$dir/posts.json" ) ) {
	$blog = json_decode( file_get_contents( "$dir/posts.json" ), true );
	foreach ( $blog['categories'] ?? array() as $c ) {
		if ( ! term_exists( $c['slug'], 'category' ) ) {
			wp_insert_term( $c['name'], 'category', array( 'slug' => $c['slug'], 'description' => $c['description'] ) );
			WP_CLI::log( "category   {$c['slug']}" );
		}
	}

	$n = 0;
	foreach ( $blog['posts'] ?? array() as $p ) {
		$content  = trim( (string) file_get_contents( "$dir/{$p['file']}" ) );
		$src_hash = md5( $content . '|' . $p['title'] . '|' . implode( ',', $p['categories'] ) . '|' . implode( ',', $p['tags'] ) . '|' . $p['featured'] );
		$found    = get_posts( array( 'name' => $p['slug'], 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => 1 ) );
		$existing = $found ? $found[0] : null;

		if ( $existing ) {
			if ( get_post_meta( $existing->ID, '_yroc_src_hash', true ) === $src_hash && ! get_post_meta( $existing->ID, '_yroc_media_pending', true ) ) {
				continue;
			}
			$last = get_post_meta( $existing->ID, '_yroc_db_hash', true );
			if ( $last && md5( $existing->post_content ) !== $last && ! $force ) {
				WP_CLI::warning( "skipped    post {$p['slug']} (edited in WordPress)" );
				continue;
			}
		}

		$cat_ids = array();
		foreach ( $p['categories'] as $cs ) {
			$t = get_term_by( 'slug', $cs, 'category' );
			if ( $t ) {
				$cat_ids[] = (int) $t->term_id;
			}
		}
		$arr = array(
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => $p['title'],
			'post_name'     => $p['slug'],
			'post_date'     => str_replace( 'T', ' ', $p['date'] ),
			'post_content'  => wp_slash( $content ),
			'post_excerpt'  => wp_slash( $p['excerpt'] ),
			'post_category' => $cat_ids,
			'tags_input'    => $p['tags'],
		);
		if ( $existing ) {
			$arr['ID'] = $existing->ID;
		}
		$id = wp_insert_post( $arr, true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( "failed     post {$p['slug']}: " . $id->get_error_message() );
			continue;
		}

		// Featured image.
		$pending = false;
		if ( $p['featured'] ) {
			$fid = $sideload( $p['featured'], $id );
			$fid ? set_post_thumbnail( $id, $fid ) : ( $pending = true );
		}

		// Inline images: download and point at the new media library copy.
		if ( preg_match_all( '#https?://(?:www\.)?yrocconsulting\.com/wp-content/uploads/[^"\'\s)]+\.(?:jpe?g|png|gif|webp)#i', $content, $m ) ) {
			$new = $content;
			foreach ( array_unique( $m[0] ) as $url ) {
				$aid = $sideload( $url, $id );
				if ( $aid ) {
					$new = str_replace( $url, wp_get_attachment_url( $aid ), $new );
				} else {
					$pending = true;
				}
			}
			if ( $new !== $content ) {
				wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( $new ) ) );
			}
		}

		update_post_meta( $id, '_yroc_src_hash', $src_hash );
		update_post_meta( $id, '_yroc_db_hash', md5( get_post_field( 'post_content', $id, 'raw' ) ) );
		$pending ? update_post_meta( $id, '_yroc_media_pending', 1 ) : delete_post_meta( $id, '_yroc_media_pending' );
		WP_CLI::log( ( $existing ? 'updated    ' : 'created    ' ) . "post {$p['slug']}" . ( $pending ? ' (some images pending)' : '' ) );
		$n++;
	}
	WP_CLI::success( "$n posts imported/updated." );
}
