<?php
/**
 * Favicon and app icons (drawn by tools/make_favicon.py into assets/img/favicon/).
 * Used unless a Site Icon is set in Settings → General, which then takes over.
 */

function yroc_favicon_url( $file ) {
	return get_theme_file_uri( 'assets/img/favicon/' . $file ) . '?v=1';
}

add_action( 'wp_head', function () {
	if ( has_site_icon() ) {
		return;
	}
	printf( '<link rel="icon" href="%s" sizes="any">' . "\n", esc_url( yroc_favicon_url( 'favicon.ico' ) ) );
	printf( '<link rel="icon" type="image/png" sizes="32x32" href="%s">' . "\n", esc_url( yroc_favicon_url( 'favicon-32.png' ) ) );
	printf( '<link rel="icon" type="image/png" sizes="192x192" href="%s">' . "\n", esc_url( yroc_favicon_url( 'icon-192.png' ) ) );
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( yroc_favicon_url( 'apple-touch-icon.png' ) ) );
}, 2 );

// Browsers also ask for /favicon.ico directly; WordPress would otherwise answer with its own logo.
add_action( 'do_faviconico', function () {
	if ( ! has_site_icon() ) {
		wp_safe_redirect( yroc_favicon_url( 'favicon.ico' ), 301 );
		exit;
	}
} );
