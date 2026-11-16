<?php
/**
 * SEO: titles, meta descriptions, Open Graph / Twitter cards, robots, structured data and the XML sitemap.
 *
 * Per-page titles and descriptions come from post meta (_yroc_seo_title, _yroc_seo_desc), which the
 * deploy sets from content/seo.json. Pages without them fall back to the page title and excerpt.
 * Site-wide Organization schema lives in inc/schema.php.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const YROC_SEP = ' | ';

/** Current singular post, front page or posts page, else null. */
function yroc_seo_post() {
	if ( is_front_page() ) {
		$id = (int) get_option( 'page_on_front' );
		return $id ? get_post( $id ) : null;
	}
	if ( is_home() ) {
		$id = (int) get_option( 'page_for_posts' );
		return $id ? get_post( $id ) : null;
	}
	return is_singular() ? get_queried_object() : null;
}

function yroc_seo_clean( $text, $max = 158 ) {
	$text = wp_strip_all_tags( strip_shortcodes( (string) $text ), true );
	$text = trim( preg_replace( '/\s+/', ' ', html_entity_decode( $text, ENT_QUOTES, 'UTF-8' ) ) );
	$text = preg_replace( '/\s*\x{2014}\s*/u', ', ', $text );
	if ( mb_strlen( $text ) > $max ) {
		$text = rtrim( mb_substr( $text, 0, $max - 1 ), " ,.;:" ) . '…';
	}
	return $text;
}

function yroc_seo_title() {
	$site = get_bloginfo( 'name' );
	$post = yroc_seo_post();
	if ( $post ) {
		$custom = get_post_meta( $post->ID, '_yroc_seo_title', true );
		if ( $custom ) {
			return $custom;
		}
		return wp_strip_all_tags( get_the_title( $post ) ) . YROC_SEP . $site;
	}
	if ( is_category() || is_tag() ) {
		return single_term_title( '', false ) . ' Articles' . YROC_SEP . $site;
	}
	if ( is_search() ) {
		return 'Search results for "' . get_search_query() . '"' . YROC_SEP . $site;
	}
	if ( is_404() ) {
		return 'Page not found' . YROC_SEP . $site;
	}
	return '';
}

function yroc_seo_description() {
	$post = yroc_seo_post();
	if ( $post ) {
		$d = get_post_meta( $post->ID, '_yroc_seo_desc', true );
		if ( ! $d ) {
			$d = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;
		}
		return yroc_seo_clean( $d );
	}
	if ( is_category() || is_tag() ) {
		$d = term_description();
		return yroc_seo_clean( $d ? $d : 'Articles on ' . single_term_title( '', false ) . ' from Yroc Consulting, an AI search optimization firm in Sherman, Texas.' );
	}
	return yroc_seo_clean( get_bloginfo( 'description' ) );
}

function yroc_seo_image() {
	$post = yroc_seo_post();
	if ( $post && has_post_thumbnail( $post ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'large' );
		if ( $src ) {
			return array( $src[0], $src[1], $src[2] );
		}
	}
	return array( get_theme_file_uri( 'assets/img/og-default.jpg' ), 1200, 630 );
}

function yroc_seo_url() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	$post = yroc_seo_post();
	if ( $post ) {
		return get_permalink( $post );
	}
	if ( is_category() || is_tag() ) {
		return get_term_link( get_queried_object() );
	}
	return '';
}

/* ---------- <title> ---------- */
add_filter( 'pre_get_document_title', function ( $title ) {
	$t = yroc_seo_title();
	return $t ? $t : $title;
} );
add_filter( 'document_title_separator', function () {
	return '|';
} );

/* ---------- Robots ---------- */
add_filter( 'wp_robots', function ( $robots ) {
	if ( is_tag() || is_author() || is_date() || is_search() || ( is_paged() && ! is_home() ) || is_attachment() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	$robots['max-image-preview'] = 'large';
	return $robots;
} );

/* ---------- Meta description, Open Graph, Twitter ---------- */
add_action( 'wp_head', function () {
	if ( is_404() ) {
		return;
	}
	$title = wp_get_document_title();
	$desc  = yroc_seo_description();
	$url   = yroc_seo_url();
	list( $img, $w, $h ) = yroc_seo_image();
	$type  = is_singular( 'post' ) ? 'article' : 'website';
	$out   = array();
	if ( $desc ) {
		$out[] = '<meta name="description" content="' . esc_attr( $desc ) . '">';
	}
	$out[] = '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
	$out[] = '<meta property="og:locale" content="en_US">';
	$out[] = '<meta property="og:type" content="' . esc_attr( $type ) . '">';
	$out[] = '<meta property="og:title" content="' . esc_attr( $title ) . '">';
	if ( $desc ) {
		$out[] = '<meta property="og:description" content="' . esc_attr( $desc ) . '">';
	}
	if ( $url && ! is_wp_error( $url ) ) {
		$out[] = '<meta property="og:url" content="' . esc_url( $url ) . '">';
	}
	$out[] = '<meta property="og:image" content="' . esc_url( $img ) . '">';
	$out[] = '<meta property="og:image:width" content="' . (int) $w . '">';
	$out[] = '<meta property="og:image:height" content="' . (int) $h . '">';
	if ( 'article' === $type ) {
		$p     = get_queried_object();
		$out[] = '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c', $p ) ) . '">';
		$out[] = '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c', $p ) ) . '">';
	}
	$out[] = '<meta name="twitter:card" content="summary_large_image">';
	$out[] = '<meta name="twitter:site" content="@YrocConsulting">';
	echo "\n" . implode( "\n", $out ) . "\n";
}, 2 );

/* ---------- Page-level structured data ---------- */
add_action( 'wp_head', function () {
	if ( is_404() || is_search() ) {
		return;
	}
	$home  = home_url( '/' );
	$org   = array( '@id' => $home . '#organization' );
	$graph = array(
		array(
			'@type'     => 'WebSite',
			'@id'       => $home . '#website',
			'url'       => $home,
			'name'      => get_bloginfo( 'name' ),
			'publisher' => $org,
		),
	);
	$post = yroc_seo_post();
	$url  = yroc_seo_url();

	if ( $post && $url ) {
		// Breadcrumbs from the page hierarchy.
		$crumbs = array( array( 'Home', $home ) );
		if ( 'post' === $post->post_type ) {
			$blog     = (int) get_option( 'page_for_posts' );
			$crumbs[] = array( 'Blog', $blog ? get_permalink( $blog ) : home_url( '/blog/' ) );
		} else {
			foreach ( array_reverse( get_post_ancestors( $post ) ) as $a ) {
				$crumbs[] = array( wp_strip_all_tags( get_the_title( $a ) ), get_permalink( $a ) );
			}
		}
		if ( ! is_front_page() ) {
			$crumbs[] = array( wp_strip_all_tags( get_the_title( $post ) ), $url );
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $url . '#breadcrumb',
			'itemListElement' => array_map( function ( $c, $i ) {
				return array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1] );
			}, $crumbs, array_keys( $crumbs ) ),
		);

		$page = array(
			'@type'       => is_page( 'about-us' ) ? 'AboutPage' : ( is_page( array( 'contact', 'book-a-strategy-call' ) ) ? 'ContactPage' : ( ( is_home() || is_page( array( 'case-studies', 'locations', 'kb', 'reviews' ) ) ) ? 'CollectionPage' : 'WebPage' ) ),
			'@id'         => $url . '#webpage',
			'url'         => $url,
			'name'        => wp_get_document_title(),
			'description' => yroc_seo_description(),
			'isPartOf'    => array( '@id' => $home . '#website' ),
			'about'       => $org,
			'breadcrumb'  => array( '@id' => $url . '#breadcrumb' ),
			'inLanguage'  => 'en-US',
		);
		list( $img ) = yroc_seo_image();
		$page['primaryImageOfPage'] = array( '@type' => 'ImageObject', 'url' => $img );
		$graph[] = $page;

		// Blog posts.
		if ( 'post' === $post->post_type ) {
			$graph[] = array(
				'@type'            => 'BlogPosting',
				'@id'              => $url . '#article',
				'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
				'description'      => yroc_seo_description(),
				'datePublished'    => get_the_date( 'c', $post ),
				'dateModified'     => get_the_modified_date( 'c', $post ),
				'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
				'image'            => $img,
				'author'           => $org,
				'publisher'        => $org,
			);
		}

		// Service pages.
		$services = yroc_seo_services();
		$path     = trailingslashit( wp_parse_url( $url, PHP_URL_PATH ) );
		if ( isset( $services[ $path ] ) ) {
			$svc = array(
				'@type'       => 'Service',
				'@id'         => $url . '#service',
				'name'        => $services[ $path ],
				'serviceType' => $services[ $path ],
				'description' => yroc_seo_description(),
				'provider'    => $org,
				'areaServed'  => array( '@type' => 'Country', 'name' => 'United States' ),
				'url'         => $url,
			);
			if ( '/website-packages/' === $path ) {
				$svc['offers'] = array_map( function ( $o ) use ( $url ) {
					return array( '@type' => 'Offer', 'name' => $o[0], 'description' => $o[2], 'url' => $url . '#packages', 'priceCurrency' => 'USD', 'price' => $o[1],
						'priceSpecification' => array( '@type' => 'PriceSpecification', 'price' => $o[1], 'priceCurrency' => 'USD', 'minPrice' => $o[1] ) );
				}, array(
					array( 'Essential Website', '4000', 'A professional, AI-ready website with up to 12 pages and AI search optimization included.' ),
					array( 'Growth Website', '7500', 'Up to 25 pages with advanced UI/UX, lead forms, SEO and AI search optimization.' ),
					array( 'Enterprise Website', '14500', 'A fully custom platform with 50+ pages, integrations and advanced AI search architecture.' ),
				) );
			}
			if ( 0 === strpos( $path, '/reputation-management/' ) && '/reputation-management/' !== $path ) {
				$svc['isRelatedTo'] = array( '@id' => home_url( '/reputation-management/' ) . '#service' );
			}
			$graph[] = $svc;
		}

		$parent = $post->post_parent ? get_post_field( 'post_name', $post->post_parent ) : '';

		// Learning Center guides and glossary.
		if ( 'kb' === $parent ) {
			if ( 'ai-search-glossary' === $post->post_name ) {
				preg_match_all( '#<dt>(.*?)</dt><dd>(.*?)</dd>#s', $post->post_content, $gm, PREG_SET_ORDER );
				$graph[] = array(
					'@type'          => 'DefinedTermSet',
					'@id'            => $url . '#glossary',
					'name'           => 'AI Search Glossary',
					'url'            => $url,
					'publisher'      => $org,
					'hasDefinedTerm' => array_map( function ( $g ) use ( $url ) {
						$name = yroc_seo_clean( $g[1], 120 );
						return array( '@type' => 'DefinedTerm', 'name' => $name, 'description' => yroc_seo_clean( $g[2], 400 ), 'url' => $url . '#' . sanitize_title( $name ), 'inDefinedTermSet' => array( '@id' => $url . '#glossary' ) );
					}, $gm ),
				);
			} else {
				$graph[] = array(
					'@type'            => 'Article',
					'@id'              => $url . '#article',
					'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
					'description'      => yroc_seo_description(),
					'datePublished'    => get_the_date( 'c', $post ),
					'dateModified'     => get_the_modified_date( 'c', $post ),
					'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
					'image'            => $img,
					'author'           => $org,
					'publisher'        => $org,
					'isPartOf'         => array( '@type' => 'CreativeWorkSeries', 'name' => 'AI Search Learning Center', 'url' => home_url( '/kb/' ) ),
					'learningResourceType' => 'Guide',
					'audience'         => array( '@type' => 'BusinessAudience', 'audienceType' => 'Business owners and marketers' ),
				);
			}
		}

		// Case studies.
		if ( 'case-studies' === $parent ) {
			$client = null;
			foreach ( yroc_cases() as $c ) {
				if ( $c[0] === $post->post_name ) {
					$client = array( '@type' => 'Organization', 'name' => $c[1] );
					foreach ( yroc_clients() as $domain => $cl ) {
						if ( $cl['name'] === $c[1] ) {
							$client['url'] = 'https://' . $domain . '/';
						}
					}
				}
			}
			$art = array(
				'@type'            => 'Article',
				'@id'              => $url . '#article',
				'articleSection'   => 'Case Studies',
				'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
				'description'      => yroc_seo_description(),
				'datePublished'    => get_the_date( 'c', $post ),
				'dateModified'     => get_the_modified_date( 'c', $post ),
				'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
				'image'            => $img,
				'author'           => $org,
				'publisher'        => $org,
			);
			if ( $client ) {
				$art['about']    = $client;
				$art['mentions'] = array( $client );
			}
			$graph[] = $art;
		}

		// Hub pages list what they contain.
		$list = array();
		if ( is_page( 'case-studies' ) ) {
			foreach ( yroc_cases() as $c ) {
				$list[] = array( $c[1] . ': ' . $c[6], home_url( '/case-studies/' . $c[0] . '/' ) );
			}
		} elseif ( is_page( 'locations' ) ) {
			foreach ( yroc_locations() as $slug => $l ) {
				$list[] = array( 'Yroc Consulting ' . $l['city'] . ', ' . $l['region'], home_url( '/locations/' . $slug . '/' ) );
			}
		} elseif ( is_page( 'kb' ) ) {
			foreach ( get_pages( array( 'parent' => $post->ID, 'sort_column' => 'menu_order' ) ) as $kp ) {
				$list[] = array( wp_strip_all_tags( $kp->post_title ), get_permalink( $kp ) );
			}
		} elseif ( is_home() ) {
			foreach ( get_posts( array( 'numberposts' => 10 ) ) as $bp ) {
				$list[] = array( wp_strip_all_tags( $bp->post_title ), get_permalink( $bp ) );
			}
		}
		if ( $list ) {
			$graph[] = array(
				'@type'           => 'ItemList',
				'@id'             => $url . '#list',
				'numberOfItems'   => count( $list ),
				'itemListElement' => array_map( function ( $x, $n ) {
					return array( '@type' => 'ListItem', 'position' => $n + 1, 'name' => $x[0], 'url' => $x[1] );
				}, $list, array_keys( $list ) ),
			);
			foreach ( $graph as &$g ) {
				if ( isset( $g['@id'] ) && $g['@id'] === $url . '#webpage' ) {
					$g['mainEntity'] = array( '@id' => $url . '#list' );
				}
			}
			unset( $g );
		}

		// About page: the founder.
		if ( is_page( 'about-us' ) ) {
			foreach ( $graph as &$g ) {
				if ( isset( $g['@id'] ) && $g['@id'] === $url . '#webpage' ) {
					$g['mentions'] = array( '@id' => $home . '#cory-long' );
				}
			}
			unset( $g );
		}

		// FAQs on the page (from FAQ accordions).
		$faq = yroc_seo_faqs( has_shortcode( $post->post_content, 'yroc_location' ) ? do_shortcode( $post->post_content ) : $post->post_content );
		if ( $faq ) {
			$graph[] = array(
				'@type'      => 'FAQPage',
				'@id'        => $url . '#faq',
				'mainEntity' => $faq,
			);
		}
	} elseif ( is_category() && $url && ! is_wp_error( $url ) ) {
		$term  = get_queried_object();
		$blog  = (int) get_option( 'page_for_posts' );
		$items = array();
		foreach ( get_posts( array( 'numberposts' => 20, 'category' => $term->term_id ) ) as $bp ) {
			$items[] = array( '@type' => 'ListItem', 'position' => count( $items ) + 1, 'name' => wp_strip_all_tags( $bp->post_title ), 'url' => get_permalink( $bp ) );
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $url . '#breadcrumb',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $blog ? get_permalink( $blog ) : home_url( '/blog/' ) ),
				array( '@type' => 'ListItem', 'position' => 3, 'name' => $term->name, 'item' => $url ),
			),
		);
		$graph[] = array(
			'@type'       => 'CollectionPage',
			'@id'         => $url . '#webpage',
			'url'         => $url,
			'name'        => wp_get_document_title(),
			'description' => yroc_seo_description(),
			'isPartOf'    => array( '@id' => $home . '#website' ),
			'breadcrumb'  => array( '@id' => $url . '#breadcrumb' ),
			'mainEntity'  => array( '@type' => 'ItemList', 'itemListElement' => $items ),
		);
	}

	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}, 6 );

function yroc_seo_services() {
	return array(
		'/ai-search-optimization/'                               => 'AI Search Optimization',
		'/ai-visibility-audit/'                                  => 'AI Visibility Audit',
		'/entity-optimization/'                                  => 'Entity Optimization',
		'/schema-structured-data/'                               => 'Schema & Structured Data',
		'/ai-content-optimization/'                              => 'AI Content Optimization',
		'/ai-search-reporting/'                                  => 'AI Search Reporting',
		'/reputation-management/'                                => 'AI Reputation Management',
		'/reputation-management/negative-search-results/'        => 'Negative Search Result Suppression',
		'/reputation-management/personal-reputation-management/' => 'Personal Reputation Management',
		'/reputation-management/business-reputation-management/' => 'Business Reputation Management',
		'/reputation-management/knowledge-panel-repair/'         => 'Knowledge Panel Claim & Repair',
		'/reputation-management/rebrand-strategy/'               => 'Rebrand Reputation Strategy',
		'/website-packages/'                                     => 'AI-Ready Website Design',
		'/white-label-services/'                                 => 'White Label AI Search Optimization',
	);
}

/** Extract Question/Answer pairs from FAQ <details> blocks in post content. */
function yroc_seo_faqs( $content ) {
	if ( false === strpos( $content, 'yr-faq__item' ) ) {
		return array();
	}
	preg_match_all( '#<details[^>]*yr-faq__item[^>]*>\s*<summary>(.*?)</summary>(.*?)</details>#s', $content, $m, PREG_SET_ORDER );
	$out = array();
	foreach ( $m as $x ) {
		$q = yroc_seo_clean( $x[1], 300 );
		$a = yroc_seo_clean( $x[2], 1000 );
		if ( $q && $a ) {
			$out[] = array( '@type' => 'Question', 'name' => $q, 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $a ) );
		}
	}
	return $out;
}

/* ---------- Head cleanup ---------- */
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'feed_links_show_comments_feed', '__return_false' );

/* ---------- XML sitemap (WordPress core: /wp-sitemap.xml) ---------- */
// Keep the sitemap available on staging too (robots meta still blocks indexing there).
add_filter( 'wp_sitemaps_enabled', '__return_true' );
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return 'users' === $name ? false : $provider; // no author archives
}, 10, 2 );
add_filter( 'wp_sitemaps_taxonomies', function ( $taxonomies ) {
	unset( $taxonomies['post_tag'], $taxonomies['post_format'] ); // tag pages are noindex
	return $taxonomies;
} );
add_filter( 'wp_sitemaps_post_types', function ( $types ) {
	return array_intersect_key( $types, array_flip( array( 'page', 'post' ) ) );
} );
add_filter( 'wp_sitemaps_posts_entry', function ( $entry, $post ) {
	$entry['lastmod'] = get_post_modified_time( 'c', true, $post );
	return $entry;
}, 10, 2 );

/* Old Yoast sitemap URLs (submitted to Search Console on the previous site) point to the new one. */
add_action( 'init', function () {
	$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
	if ( preg_match( '#^/(sitemap_index|sitemap|page-sitemap|post-sitemap|category-sitemap|post_tag-sitemap|author-sitemap|ufaq-sitemap)\.xml$#', $path ) ) {
		wp_safe_redirect( home_url( '/wp-sitemap.xml' ), 301, 'Yroc SEO' );
		exit;
	}
} );
