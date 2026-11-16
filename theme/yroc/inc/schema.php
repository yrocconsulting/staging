<?php
/**
 * Organization schema: the entity foundation we sell, on our own site.
 *
 * One ProfessionalService entity (#organization) with founder, locations (departments), contact points,
 * services and profiles. Page-level schema (WebPage, Service, Article, FAQPage, ...) is in inc/seo.php and
 * refers back to these @ids.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yroc_schema_ids() {
	$home = home_url( '/' );
	return array(
		'org'     => $home . '#organization',
		'founder' => $home . '#cory-long',
		'website' => $home . '#website',
	);
}

/** Founder as a schema.org Person (full details; referenced by @id elsewhere). */
function yroc_schema_founder() {
	$ids = yroc_schema_ids();
	return array(
		'@type'       => 'Person',
		'@id'         => $ids['founder'],
		'name'        => 'Cory Long',
		'jobTitle'    => 'Founder & CEO',
		'worksFor'    => array( '@id' => $ids['org'] ),
		'url'         => home_url( '/about-us/' ),
		'image'       => get_theme_file_uri( 'assets/img/about/founder.jpg' ),
		'description' => 'Founder of Yroc Consulting and creator of the AI Authority Engine certification. Advising businesses on growth and marketing since 2005 and a frequent speaker on AI search visibility.',
		'knowsAbout'  => array( 'AI Search Optimization', 'Answer Engine Optimization', 'Entity SEO', 'Local SEO', 'Lead Generation', 'Online Reputation Management' ),
		'sameAs'      => array( 'https://aiauthorityengine.org/cory-long/', 'https://corylong.org/' ),
	);
}

add_action( 'wp_head', function () {
	$i    = yroc_info();
	$ids  = yroc_schema_ids();
	$home = home_url( '/' );

	$departments = array();
	$contacts    = array();
	$maps        = array();
	foreach ( yroc_locations() as $slug => $l ) {
		$maps[] = $l['gbp'];
		$dept   = array(
			'@type'     => 'ProfessionalService',
			'@id'       => home_url( '/locations/' . $slug . '/' ) . '#localbusiness',
			'name'      => $l['name'] . ' | ' . $l['city'],
			'url'       => home_url( '/locations/' . $slug . '/' ),
			'telephone' => $l['tel'],
			'hasMap'    => $l['gbp'],
			'address'   => $l['street']
				? array( '@type' => 'PostalAddress', 'streetAddress' => $l['street'], 'addressLocality' => $l['city'], 'addressRegion' => $l['region'], 'postalCode' => $l['zip'], 'addressCountry' => 'US' )
				: array( '@type' => 'PostalAddress', 'addressLocality' => $l['city'], 'addressRegion' => $l['region'], 'addressCountry' => 'US' ),
		);
		if ( empty( $l['main'] ) ) {
			$departments[] = $dept;
		}
		$contacts[] = array(
			'@type'             => 'ContactPoint',
			'telephone'         => $l['tel'],
			'contactType'       => 'sales',
			'areaServed'        => $l['city'] . ', ' . $l['region'],
			'availableLanguage' => 'English',
		);
	}

	$catalog = array();
	foreach ( array(
		'/ai-search-optimization/' => 'AI Search Optimization',
		'/ai-visibility-audit/'    => 'AI Visibility Audit',
		'/entity-optimization/'    => 'Entity Optimization',
		'/schema-structured-data/' => 'Schema & Structured Data',
		'/ai-content-optimization/' => 'AI Content Optimization',
		'/ai-search-reporting/'    => 'AI Search Reporting',
		'/reputation-management/'  => 'AI Reputation Management',
		'/website-packages/'       => 'AI-Ready Website Design',
		'/white-label-services/'   => 'White Label AI Search Optimization',
	) as $path => $name ) {
		$catalog[] = array( '@type' => 'Offer', 'itemOffered' => array( '@type' => 'Service', '@id' => home_url( $path ) . '#service', 'name' => $name, 'url' => home_url( $path ) ) );
	}

	$org = array(
		'@type'               => 'ProfessionalService',
		'@id'                 => $ids['org'],
		'name'                => $i['name'],
		'legalName'           => 'Yroc Consulting, LLC',
		'url'                 => $home,
		'logo'                => array( '@type' => 'ImageObject', 'url' => get_theme_file_uri( 'assets/img/logo.png' ), 'width' => 740, 'height' => 224 ),
		'image'               => get_theme_file_uri( 'assets/img/og-default.jpg' ),
		'description'         => 'Yroc Consulting is an AI search optimization firm headquartered in Sherman, Texas, that helps businesses become easier for ChatGPT, Gemini, Perplexity and Google AI Overviews to understand, trust and recommend.',
		'slogan'              => 'AI is deciding who your customers call. We make sure it chooses you.',
		'foundingDate'        => '2014',
		'founder'             => array( '@id' => $ids['founder'] ),
		'telephone'           => $i['tel'],
		'email'               => $i['email'],
		'priceRange'          => '$$$',
		'address'             => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $i['street'],
			'addressLocality' => $i['city'],
			'addressRegion'   => $i['region'],
			'postalCode'      => $i['zip'],
			'addressCountry'  => 'US',
		),
		'geo'                 => array( '@type' => 'GeoCoordinates', 'latitude' => 33.6566788, 'longitude' => -96.6002804 ),
		'hasMap'              => yroc_locations()['sherman-tx']['gbp'],
		'openingHoursSpecification' => array( array( '@type' => 'OpeningHoursSpecification', 'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' ), 'opens' => '09:00', 'closes' => '17:00' ) ),
		'department'          => $departments,
		'contactPoint'        => $contacts,
		'areaServed'          => array( '@type' => 'Country', 'name' => 'United States' ),
		'knowsAbout'          => array( 'AI Search Optimization', 'Generative Engine Optimization', 'Answer Engine Optimization', 'Entity SEO', 'Schema Markup', 'Online Reputation Management', 'Local SEO', 'Website Design' ),
		'hasOfferCatalog'     => array( '@type' => 'OfferCatalog', 'name' => 'AI search optimization services', 'itemListElement' => $catalog ),
		'subOrganization'     => array( '@type' => 'EducationalOrganization', 'name' => 'AI Authority Engine', 'url' => 'https://aiauthorityengine.org/', 'founder' => array( '@id' => $ids['founder'] ) ),
		'sameAs'              => array_values( array_unique( array_merge( array_values( $i['social'] ), array( $i['reviews']['clutch'], $i['reviews']['trustpilot'], $i['reviews']['trustindex'] ), $maps ) ) ),
	);

	$graph = array( $org );
	if ( is_page( 'about-us' ) || is_front_page() ) {
		$graph[] = yroc_schema_founder();
	} else {
		$graph[] = array( '@type' => 'Person', '@id' => $ids['founder'], 'name' => 'Cory Long', 'url' => home_url( '/about-us/' ) );
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}, 5 );
