<?php
/**
 * IndexNow: tell search engines about new, updated and removed pages the moment they change.
 *
 * IndexNow is shared by Bing (which also feeds ChatGPT search and Microsoft Copilot), Yandex, Seznam,
 * Naver and others: one submission reaches all of them. Google does not take IndexNow submissions; it
 * finds new pages through the sitemap at /wp-sitemap.xml (submit that once in Google Search Console).
 *
 * Runs on the live site only (never on staging or when search engines are discouraged).
 * The verification key file is served at /<key>.txt. On first run every published page and post is
 * submitted once. WP-CLI: `wp yroc-indexnow status`, `wp yroc-indexnow submit <url>...`, `wp yroc-indexnow all`.
 */

const YROC_INDEXNOW_HOST     = 'yrocconsulting.com';
const YROC_INDEXNOW_ENDPOINT = 'https://api.indexnow.org/indexnow';

function yroc_indexnow_enabled() {
	return YROC_INDEXNOW_HOST === wp_parse_url( home_url(), PHP_URL_HOST ) && '1' === (string) get_option( 'blog_public' );
}

function yroc_indexnow_key() {
	$key = (string) get_option( 'yroc_indexnow_key', '' );
	if ( ! preg_match( '/^[a-f0-9]{32}$/', $key ) ) {
		$key = md5( wp_generate_password( 64, true, true ) );
		update_option( 'yroc_indexnow_key', $key, true );
	}
	return $key;
}

// Serve the key file so search engines can verify the submissions are ours.
add_action( 'parse_request', function () {
	if ( ! yroc_indexnow_enabled() ) {
		return;
	}
	$path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	if ( '/' . yroc_indexnow_key() . '.txt' === $path ) {
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );
		echo yroc_indexnow_key(); // phpcs:ignore WordPress.Security.EscapeOutput
		exit;
	}
}, 0 );

/** Queue a URL; everything queued during one request is sent together at the end of it. */
function yroc_indexnow_queue( $url ) {
	$GLOBALS['yroc_indexnow_queue'][ $url ] = true;
}

// Published, updated, unpublished or trashed pages and posts.
add_action( 'transition_post_status', function ( $new, $old, $post ) {
	if ( ! yroc_indexnow_enabled() || wp_is_post_revision( $post ) || wp_is_post_autosave( $post ) ) {
		return;
	}
	if ( ! in_array( $post->post_type, array( 'post', 'page' ), true ) || ( 'publish' !== $new && 'publish' !== $old ) ) {
		return;
	}
	if ( 'publish' === $new ) {
		yroc_indexnow_queue( get_permalink( $post ) );
		if ( 'post' === $post->post_type ) {
			yroc_indexnow_queue( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) );
		}
	} else {
		// No longer public: submit its old address so engines drop it (the URL now 404s or redirects).
		$was              = clone $post;
		$was->post_status = 'publish';
		$was->post_name   = preg_replace( '/__trashed(-\d+)?$/', '', $was->post_name );
		yroc_indexnow_queue( get_permalink( $was ) );
	}
}, 10, 3 );

add_action( 'shutdown', function () {
	if ( ! empty( $GLOBALS['yroc_indexnow_queue'] ) ) {
		yroc_indexnow_send( array_keys( $GLOBALS['yroc_indexnow_queue'] ) );
		$GLOBALS['yroc_indexnow_queue'] = array();
	}
} );

/** Submit URLs (on this host only) to IndexNow and keep a short log. Returns the HTTP status code. */
function yroc_indexnow_send( array $urls ) {
	if ( ! yroc_indexnow_enabled() ) {
		return 0;
	}
	$urls = array_values( array_unique( array_filter( $urls, function ( $u ) {
		return is_string( $u ) && YROC_INDEXNOW_HOST === wp_parse_url( $u, PHP_URL_HOST );
	} ) ) );
	if ( ! $urls ) {
		return 0;
	}
	$code = 0;
	foreach ( array_chunk( $urls, 10000 ) as $chunk ) {
		$r    = wp_remote_post( YROC_INDEXNOW_ENDPOINT, array(
			'timeout' => 15,
			'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			'body'    => wp_json_encode( array(
				'host'        => YROC_INDEXNOW_HOST,
				'key'         => yroc_indexnow_key(),
				'keyLocation' => home_url( '/' . yroc_indexnow_key() . '.txt' ),
				'urlList'     => $chunk,
			) ),
		) );
		$code = is_wp_error( $r ) ? 0 : (int) wp_remote_retrieve_response_code( $r );
		$log  = (array) get_option( 'yroc_indexnow_log', array() );
		array_unshift( $log, array( 'time' => gmdate( 'c' ), 'code' => $code, 'count' => count( $chunk ), 'first' => $chunk[0], 'error' => is_wp_error( $r ) ? $r->get_error_message() : '' ) );
		update_option( 'yroc_indexnow_log', array_slice( $log, 0, 30 ), false );
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::log( sprintf( 'indexnow   %d URL(s) submitted, HTTP %d%s', count( $chunk ), $code, is_wp_error( $r ) ? ' (' . $r->get_error_message() . ')' : '' ) );
		}
	}
	return $code;
}

/** Every published page and post, plus the homepage and blog. */
function yroc_indexnow_all_urls() {
	$ids  = get_posts( array( 'post_type' => array( 'page', 'post' ), 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' ) );
	$urls = array_map( 'get_permalink', $ids );
	array_unshift( $urls, home_url( '/' ) );
	return array_values( array_unique( $urls ) );
}

// First run on the live site: submit everything once.
add_action( 'init', function () {
	if ( yroc_indexnow_enabled() && ! get_option( 'yroc_indexnow_initial' ) && ! wp_next_scheduled( 'yroc_indexnow_initial' ) ) {
		wp_schedule_single_event( time() + 60, 'yroc_indexnow_initial' );
	}
} );
add_action( 'yroc_indexnow_initial', function () {
	if ( yroc_indexnow_enabled() && ! get_option( 'yroc_indexnow_initial' ) ) {
		$code = yroc_indexnow_send( yroc_indexnow_all_urls() );
		if ( $code >= 200 && $code < 300 ) {
			update_option( 'yroc_indexnow_initial', gmdate( 'c' ), false );
		}
	}
} );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'yroc-indexnow', new class() {
		/** Show whether IndexNow is active and the latest submissions. */
		public function status() {
			WP_CLI::log( 'enabled: ' . ( yroc_indexnow_enabled() ? 'yes' : 'no (not the live site, or search engines discouraged)' ) );
			if ( yroc_indexnow_enabled() ) {
				WP_CLI::log( 'key file: ' . home_url( '/' . yroc_indexnow_key() . '.txt' ) );
			}
			WP_CLI::log( 'initial submission: ' . ( get_option( 'yroc_indexnow_initial' ) ?: 'not yet' ) );
			foreach ( array_slice( (array) get_option( 'yroc_indexnow_log', array() ), 0, 10 ) as $e ) {
				WP_CLI::log( sprintf( '%s  HTTP %d  %d URL(s)  %s %s', $e['time'], $e['code'], $e['count'], $e['first'], $e['error'] ) );
			}
		}

		/** Submit specific URLs. */
		public function submit( $args ) {
			yroc_indexnow_send( $args );
		}

		/** Submit every published page and post. */
		public function all() {
			$code = yroc_indexnow_send( yroc_indexnow_all_urls() );
			if ( $code >= 200 && $code < 300 ) {
				update_option( 'yroc_indexnow_initial', gmdate( 'c' ), false );
			}
		}
	} );
}
