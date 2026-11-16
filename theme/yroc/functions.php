<?php
/**
 * Yroc theme bootstrap.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YROC_VERSION', '2.10.0' );

require_once __DIR__ . '/inc/site.php';
require_once __DIR__ . '/inc/schema.php';
require_once __DIR__ . '/inc/ai-stats.php';
require_once __DIR__ . '/inc/forms.php';
require_once __DIR__ . '/inc/reviews.php';
require_once __DIR__ . '/inc/redirects.php';
require_once __DIR__ . '/inc/seo.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_editor_style( 'assets/css/main.css' );
} );

add_action( 'wp_enqueue_scripts', function () {
	$ver = YROC_VERSION . '.' . filemtime( __DIR__ . '/assets/css/main.css' );
	wp_enqueue_style( 'yroc-main', get_theme_file_uri( 'assets/css/main.css' ), array(), $ver );
	wp_enqueue_script(
		'yroc-main',
		get_theme_file_uri( 'assets/js/main.js' ),
		array(),
		YROC_VERSION . '.' . filemtime( __DIR__ . '/assets/js/main.js' ),
		array( 'strategy' => 'defer', 'in_footer' => true )
	);
} );

// Pattern category for Yroc sections.
add_action( 'init', function () {
	register_block_pattern_category( 'yroc', array( 'label' => __( 'Yroc Sections', 'yroc' ) ) );
} );

// Remove emoji bloat.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
