<?php
/**
 * Organization / ProfessionalService JSON-LD: the entity foundation we sell, on our own site.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', function () {
	$i    = yroc_info();
	$home = home_url( '/' );
	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'ProfessionalService',
		'@id'         => $home . '#organization',
		'name'        => $i['name'],
		'url'         => $home,
		'logo'        => get_theme_file_uri( 'assets/img/logo.png' ),
		'image'       => get_theme_file_uri( 'assets/img/logo.png' ),
		'description' => 'Yroc Consulting is an AI search optimization agency that helps businesses become easier for AI platforms like ChatGPT, Gemini, Perplexity and Google AI Overviews to understand, trust and recommend.',
		'telephone'   => $i['tel'],
		'email'       => $i['email'],
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $i['street'],
			'addressLocality' => $i['city'],
			'addressRegion'   => $i['region'],
			'postalCode'      => $i['zip'],
			'addressCountry'  => 'US',
		),
		'areaServed'  => 'US',
		'knowsAbout'  => array( 'AI Search Optimization', 'Generative Engine Optimization', 'Entity SEO', 'Schema Markup', 'Online Reputation Management', 'Local SEO', 'Website Design' ),
		'sameAs'      => array_values( array_merge( $i['social'], array( $i['reviews']['clutch'], $i['reviews']['trustpilot'] ) ) ),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES ) . "</script>\n";
}, 5 );
