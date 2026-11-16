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
			WP_CLI::log( "unchanged  /$path/" );
			$ids[ $path ] = $id;
			continue;
		}
		$last_import = get_post_meta( $id, '_yroc_db_hash', true );
		$edited      = $last_import && md5( $existing->post_content ) !== $last_import;
		if ( ( $edited || ! $last_import ) && ! $force ) {
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
