<?php
/**
 * Central business info + navigation (single source of truth for header, footer, schema).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yroc_info() {
	return array(
		'name'      => 'Yroc Consulting',
		'phone'     => '903.300.LEADS',
		'phone_alt' => '903.300.5323',
		'tel'       => '+19033005323',
		'email'     => 'info@yrocconsulting.com',
		'support'   => 'support@yrocconsulting.com',
		'street'    => '1800 Teague Dr. Ste. 416',
		'city'      => 'Sherman',
		'region'    => 'TX',
		'zip'       => '75090',
		'social'    => array(
			'facebook'  => 'https://www.facebook.com/pages/Yroc-Consulting/739790009428044',
			'x'         => 'https://twitter.com/YrocConsulting',
			'youtube'   => 'https://www.youtube.com/channel/UCvHnUwVgj4r8j-0oRO-d1aQ',
			'pinterest' => 'https://www.pinterest.com/yrocconsulting/',
		),
		'reviews'   => array(
			'clutch'     => 'https://clutch.co/profile/yroc-consulting',
			'trustpilot' => 'https://www.trustpilot.com/review/yrocconsulting.com',
			'google'     => 'https://g.co/kgs/h95KT8t',
			'trustindex' => 'https://www.trustindex.io/reviews/yrocconsulting.com',
		),
	);
}

/**
 * Services mega menu. Each column: title, blurb, links [label, url, description].
 */
function yroc_mega_menu() {
	return array(
		array(
			'title' => 'AI Optimization',
			'icon'  => 'spark',
			'links' => array(
				array( 'AI Search Optimization', '/ai-search-optimization/', 'Get recommended by ChatGPT, Gemini & AI Overviews' ),
				array( 'AI Visibility Audits', '/ai-visibility-audit/', 'See exactly how AI describes your brand' ),
				array( 'Entity Optimization', '/entity-optimization/', 'Make AI understand who you are' ),
				array( 'Schema & Structured Data', '/schema-structured-data/', 'Machine-readable trust signals' ),
				array( 'AI Content Optimization', '/ai-content-optimization/', 'Content AI wants to quote' ),
				array( 'AI Search Reporting', '/ai-search-reporting/', 'Track mentions across AI platforms' ),
			),
		),
		array(
			'title' => 'AI Authority Engine',
			'icon'  => 'engine',
			'links' => array(
				array( 'AI Authority Engine', 'https://aiauthorityengine.org/', 'AI SEO certification & training' ),
				array( 'AI SEO Certification', 'https://aiauthorityengine.org/ai-seo-certification/', 'Become a Certified AI Authority Specialist' ),
				array( 'Start an AI SEO Agency', 'https://aiauthorityengine.org/ai-seo-agency-training/', 'Agency training & client delivery' ),
				array( 'Certified Specialists', 'https://aiauthorityengine.org/certified/', 'Verify a certified professional' ),
				array( 'AI Visibility Score', 'https://aiauthorityengine.org/ai-visibility-calculator/', 'Free score for your business' ),
				array( 'Book a Strategy Call', '/book-a-strategy-call/', 'Talk with an AI specialist' ),
			),
		),
		array(
			'title' => 'Reputation Mgmt',
			'icon'  => 'shield',
			'links' => array(
				array( 'AI Reputation Management', '/reputation-management/', 'Control how AI talks about you' ),
				array( 'Negative Search Results', '/reputation-management/negative-search-results/', 'Push down what hurts you' ),
				array( 'Personal Reputation Mgmt', '/reputation-management/personal-reputation-management/', 'Protect your name' ),
				array( 'Business Reputation Mgmt', '/reputation-management/business-reputation-management/', 'Protect your brand' ),
				array( 'Knowledge Panel & Repair', '/reputation-management/knowledge-panel-repair/', 'Claim, fix & enhance' ),
				array( 'Rebrand & Change Strategy', '/reputation-management/rebrand-strategy/', 'Transition without losing trust' ),
			),
		),
		array(
			'title' => 'Case Studies',
			'icon'  => 'chart',
			'links' => array(
				array( 'AI Visibility Results', '/case-studies/#ai-visibility', 'Brands AI now recommends' ),
				array( 'Reputation Results', '/case-studies/#reputation', 'Reputations rebuilt' ),
				array( 'Local Authority Results', '/case-studies/#local', 'Local businesses winning' ),
				array( 'Client Reviews', '/reviews/', 'Hear it from our clients' ),
			),
		),
	);
}

/** In-house departments (no individual names on the site). */
function yroc_departments() {
	return array(
		array( 'ST', 'Strategy & Leadership', 'AI visibility strategy, audits and roadmaps for every client engagement.' ),
		array( 'SEO', 'SEO & Technology', 'Technical SEO, schema, entity optimization, automation and development.' ),
		array( 'WEB', 'Web Design & Development', 'AI-ready websites, landing pages, speed and accessibility.' ),
		array( 'CNT', 'Content & Authority', 'Answer-first content, PR mentions, citations and the AI Authority Engine.' ),
		array( 'REP', 'Reputation Management', 'Review strategy, suppression, knowledge panels and brand monitoring.' ),
		array( 'CR', 'Client Relations', 'Onboarding, account management, reporting and day-to-day support.' ),
	);
}

/**
 * Case studies (single source for the homepage grid and the Case Studies page).
 * slug, card title, tag, category (ai-visibility|reputation|local), metric, metric suffix, metric label, summary.
 */
function yroc_cases() {
	return array(
		array( 'fractional-cfo-reputation-management-case-study', 'Fractional CFO', 'Reputation Management', 'reputation', '80', '%', 'of negative results suppressed', 'Negative press buried a financial professional\'s brand. Within 90 days he owned page one and controlled his narrative.' ),
		array( 'blacksmith-roofing-case-study', 'Blacksmith Roofing', 'Roofing · Oklahoma', 'local', '8', '×', 'phone calls in one month', 'A roofing contractor\'s inbound calls octupled within a single month as maps visibility jumped 4,900%.' ),
		array( 'irs-trouble-solvers-case-study', 'IRS Trouble Solvers', 'Tax Resolution · National', 'local', '604', '%', 'cash-on-cash return', 'A tax firm outranked the IRS on Google, then turned that traffic into high-value leads.' ),
		array( 'thai-ginger-case-study', 'Thai Ginger', 'Restaurant · Bellevue, WA', 'ai-visibility', '#1', '', 'in ChatGPT & Google AI Overviews', 'An AI SEO strategy made a beloved Seattle-area restaurant the top recommendation in ChatGPT, Google AI Overviews, the map pack and organic search.' ),
		array( 'the-bug-guys', 'The Bug Guys', 'Pest Control · San Angelo, TX', 'local', '+285', '%', 'organic traffic in one month', 'A newly formed pest control company went from page 8 to page one and nearly quadrupled organic traffic.' ),
		array( 'medical-weight-loss-case-study', 'Wake Up Skinny', 'Medical Weight Loss', 'local', '+84', '%', 'new users in 30 days', 'A bold SEO strategy doubled new users and produced a steady flow of patients without spending on ads.' ),
		array( 'hormone-replacement-therapy-clinic-case-study', 'Hormone Clinic', 'Hormone Replacement Therapy', 'local', '$20', 'K', 'new revenue in 60 days', 'SEO, paid social and local marketing drove a 70% traffic surge and $20K in new bookings.' ),
	);
}

function yroc_case_categories() {
	return array( 'ai-visibility' => 'AI Visibility', 'reputation' => 'Reputation', 'local' => 'Local & SEO' );
}

/** Where Yroc has spoken / been featured. */
function yroc_speaking() {
	return array(
		array( 'Magnify Your Wealth', 'Featured speaker' ),
		array( 'Realtor Associations', 'Keynotes & workshops' ),
		array( 'Apartment Associations', 'Industry sessions' ),
		array( 'eForge Podcast', 'Guest expert' ),
	);
}

/** Hero image chosen in Settings → Yroc Site (empty = designed HQ map card). */
function yroc_hero_image() {
	$id = (int) get_option( 'yroc_hero_image_id', 0 );
	return $id ? wp_get_attachment_image_url( $id, 'large' ) : '';
}

/** Absolute or site-relative URL, plus attributes for external links (open in a new tab). */
function yroc_href( $url ) {
	if ( preg_match( '#^https?://#', $url ) ) {
		return 'href="' . esc_url( $url ) . '" target="_blank" rel="noopener"';
	}
	return 'href="' . esc_url( home_url( $url ) ) . '"';
}
function yroc_is_external( $url ) {
	return (bool) preg_match( '#^https?://#', $url );
}

/**
 * Old AI Authority Engine paths on this site now live on aiauthorityengine.org.
 * Any request under /ai-authority-engine/ is 301-redirected there.
 */
add_action( 'template_redirect', function () {
	$path = isset( $_SERVER['REQUEST_URI'] ) ? strtok( wp_unslash( $_SERVER['REQUEST_URI'] ), '?' ) : '';
	if ( 0 !== strpos( $path, '/ai-authority-engine' ) ) {
		return;
	}
	$map = array(
		'/ai-authority-engine/ai-authority-challenge/' => 'https://aiauthorityengine.org/ai-seo-training/',
		'/ai-authority-engine/ai-authority-sprint/'    => 'https://aiauthorityengine.org/ai-seo-training/',
	);
	wp_redirect( $map[ trailingslashit( $path ) ] ?? 'https://aiauthorityengine.org/', 301 ); // phpcs:ignore WordPress.Security.SafeRedirect
	exit;
} );

/** Primary nav (top level). 'mega' => true opens the services panel. */
function yroc_primary_nav() {
	return array(
		array( 'label' => 'Services', 'mega' => true ),
		array( 'label' => 'AI Authority Engine', 'url' => 'https://aiauthorityengine.org/' ),
		array( 'label' => 'Websites', 'url' => '/website-packages/' ),
		array( 'label' => 'Results', 'url' => '/case-studies/' ),
		array(
			'label' => 'Resources',
			'children' => array(
				array( 'Blog', '/blog/' ),
				array( 'Knowledge Base', '/kb/' ),
				array( 'Podcast', '/podcasts/how-ai-is-changing-marketing/' ),
				array( 'Speaking', '/about-us/#speaking' ),
			),
		),
		array(
			'label' => 'About',
			'children' => array(
				array( 'About Yroc', '/about-us/' ),
				array( 'Client Reviews', '/reviews/' ),
				array( 'White Label Partners', '/white-label-services/' ),
				array( 'Contact', '/contact/' ),
			),
		),
	);
}

function yroc_icon( $name ) {
	$p = array(
		'spark'  => '<path d="M12 2l2.4 6.9L21 11l-6.6 2.1L12 20l-2.4-6.9L3 11l6.6-2.1z"/>',
		'engine' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/>',
		'shield' => '<path d="M12 2l8 3v6c0 5-3.4 9.4-8 11-4.6-1.6-8-6-8-11V5z"/><path d="M8.5 12l2.5 2.5 4.5-5"/>',
		'chart'  => '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 6-7"/>',
		'phone'  => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
		'mail'   => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/>',
		'pin'    => '<path d="M12 22s-8-7.5-8-13a8 8 0 0 1 16 0c0 5.5-8 13-8 13z"/><circle cx="12" cy="9" r="3"/>',
		'arrow'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'chev'   => '<path d="M6 9l6 6 6-6"/>',
	);
	$d = $p[ $name ] ?? '';
	return '<svg class="yi yi-' . esc_attr( $name ) . '" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
}

function yroc_social_svg( $net ) {
	$p = array(
		'facebook'  => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H8v4h2v8h4v-8h3l1-4h-4V8z"/>',
		'x'         => '<path d="M4 4l16 16M20 4L4 20"/>',
		'youtube'   => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="M10 9l5 3-5 3z"/>',
		'pinterest' => '<circle cx="12" cy="12" r="10"/><path d="M11 8c3-1 6 1 5 4s-4 3-5 1M10 21l2-9"/>',
	);
	return '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ( $p[ $net ] ?? '' ) . '</svg>';
}
