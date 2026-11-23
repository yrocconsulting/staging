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
				array( 'AI Search Optimization', '/ai-search-optimization/', 'Get recommended by AI assistants' ),
				array( 'AI Visibility Audits', '/ai-visibility-audit/', 'See what AI says about you' ),
				array( 'Entity Optimization', '/entity-optimization/', 'Make AI certain who you are' ),
				array( 'Schema & Structured Data', '/schema-structured-data/', 'Facts AI reads first' ),
				array( 'AI Content Optimization', '/ai-content-optimization/', 'Content AI quotes and cites' ),
				array( 'AI Search Reporting', '/ai-search-reporting/', 'Weekly AI visibility tracking' ),
			),
		),
		array(
			'title' => 'AI Authority Engine',
			'icon'  => 'engine',
			'links' => array(
				array( 'AI Authority Engine', 'https://aiauthorityengine.org/', 'Certification & training' ),
				array( 'AI SEO Certification', 'https://aiauthorityengine.org/ai-seo-certification/', 'Become a certified specialist' ),
				array( 'Start an AI SEO Agency', 'https://aiauthorityengine.org/ai-seo-agency-training/', 'Build an AI SEO agency' ),
				array( 'Certified Specialists', 'https://aiauthorityengine.org/certified/', 'Verify a credential' ),
				array( 'AI Visibility Score', 'https://aiauthorityengine.org/ai-visibility-calculator/', 'Free readiness score' ),
				array( 'Book a Strategy Call', '/book-a-strategy-call/', 'Talk with a strategist' ),
			),
		),
		array(
			'title' => 'Reputation',
			'icon'  => 'shield',
			'links' => array(
				array( 'AI Reputation Management', '/reputation-management/', 'Control how AI describes you' ),
				array( 'Negative Search Results', '/reputation-management/negative-search-results/', 'Push down what hurts you' ),
				array( 'Personal Reputation', '/reputation-management/personal-reputation-management/', 'Protect your name' ),
				array( 'Business Reputation', '/reputation-management/business-reputation-management/', 'Protect your brand' ),
				array( 'Knowledge Panel & Repair', '/reputation-management/knowledge-panel-repair/', 'Claim, fix & enhance' ),
				array( 'Rebrand Strategy', '/reputation-management/rebrand-strategy/', 'Change without losing trust' ),
				array( 'Who We Help', '/reputation-management/who-we-help/', 'Authors, athletes, leaders & more' ),
			),
		),
		array(
			'title' => 'Results',
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
		array( 'frigiserve-ai-visibility-case-study', 'FrigiServe Cold Storage', 'Cold Storage · Fort Worth, TX', 'ai-visibility', '90', '%', 'AI visibility across buyer prompts', 'A long-time San Angelo client expanded to Fort Worth. Now ChatGPT and Gemini recommend FrigiServe for cold storage and blast freezing across DFW.' ),
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

/**
 * Office locations (match the Google Business Profiles exactly; NAP consistency matters for AI).
 * 'main' marks headquarters. A location with no street address is a service-area location.
 */
function yroc_locations() {
	return array(
		'sherman-tx' => array(
			'city' => 'Sherman', 'region' => 'TX', 'main' => true, 'name' => 'Yroc Consulting, LLC',
			'street' => '1800 Teague Dr. Ste. 416', 'zip' => '75090',
			'phone' => '903.300.5323', 'tel' => '+19033005323', 'phone_vanity' => '903.300.LEADS',
			'hours' => 'Monday – Friday, 9am – 5pm CT', 'lat' => 33.6566788, 'lng' => -96.6002804,
			'gbp' => 'https://maps.app.goo.gl/PTnssGMcUZp3hshi8',
			'region_name' => 'Texoma & North Texas',
			'areas' => array( 'Sherman', 'Denison', 'Pottsboro', 'Van Alstyne', 'Howe', 'Whitesboro', 'Gainesville', 'McKinney', 'Allen', 'Frisco', 'Plano', 'Durant, OK' ),
			'intro' => 'Our headquarters in Sherman is where strategy, technical SEO, content, reputation and client relations work side by side. Businesses across Grayson County, Texoma and the North Texas corridor can meet our team in person, and clients nationwide are served from here.',
			'local' => array(
				array( 'Built on the US-75 corridor', 'Sherman and Denison sit between the Dallas metro and southern Oklahoma, so local businesses compete with both small-town neighbors and big-city brands. AI answers are where that competition is now won.' ),
				array( 'In-person strategy sessions', 'Prefer to talk face to face? Schedule a meeting at our Teague Drive office to walk through your AI Visibility Audit and roadmap with our team.' ),
				array( 'Home of the Yroc Method', 'Every engagement, wherever the client is located, is planned and managed from our Sherman office using the same proven system.' ),
			),
			'cases' => array( 'frigiserve-ai-visibility-case-study', 'fractional-cfo-reputation-management-case-study' ),
			'proof' => array( 'Ask AI for an AI search company in Sherman', 'In our own weekly tracking, Yroc Consulting appears in AI answers for "AI search company in Sherman, TX" with 75% visibility across ChatGPT, Google AI Overviews and Gemini. We practice what we sell.' ),
			'faq' => array(
				array( 'Can I meet with Yroc in person in Sherman?', 'Yes. Our headquarters is at 1800 Teague Dr. Ste. 416 in Sherman. Book a strategy call and choose "In person in Sherman, TX" as your meeting type.' ),
				array( 'Do you only work with businesses in Sherman and Denison?', 'No. Sherman is our headquarters, and we work with businesses across Texoma, the Dallas–Fort Worth area and nationwide.' ),
				array( 'What does AI search optimization cost for a Texoma business?', 'It depends on your market and goals. Most engagements start with an AI Visibility Audit so you know exactly what to fix before you spend more.' ),
			),
		),
		'san-angelo-tx' => array(
			'city' => 'San Angelo', 'region' => 'TX', 'main' => false, 'name' => 'Yroc Consulting',
			'street' => '133 W Concho Ave #301', 'zip' => '76903',
			'phone' => '325.340.4358', 'tel' => '+13253404358',
			'hours' => 'Monday – Friday, 8am – 5pm CT', 'lat' => 31.4590302, 'lng' => -100.4378397,
			'gbp' => 'https://maps.app.goo.gl/v5C7SjGGKdPpe14ZA',
			'region_name' => 'the Concho Valley & West Texas',
			'areas' => array( 'San Angelo', 'Wall', 'Christoval', 'Miles', 'Ballinger', 'Robert Lee', 'Eden', 'Sonora', 'Big Lake', 'Brady', 'Mertzon', 'Abilene' ),
			'intro' => 'Our downtown San Angelo office on West Concho Avenue serves businesses across the Concho Valley and West Texas. Many of our longest-running client relationships started here, and some of them have grown far beyond San Angelo.',
			'local' => array(
				array( 'Where our clients grow', 'FrigiServe Cold Storage started with SEO services here in San Angelo. As the company grew and opened a new warehouse in Fort Worth, we built the AI visibility that now has ChatGPT recommending them across Dallas–Fort Worth.' ),
				array( 'Local wins, measured', 'San Angelo\'s The Bug Guys went from page eight to page one and nearly quadrupled organic traffic in a single month.' ),
				array( 'Small market, big competition', 'In regional markets, a few businesses capture most of the calls. Being the name AI recommends for "near me" questions makes the difference.' ),
			),
			'cases' => array( 'frigiserve-ai-visibility-case-study', 'the-bug-guys' ),
			'proof' => array( 'From San Angelo to statewide', 'Our San Angelo clients include FrigiServe Cold Storage, now recommended by ChatGPT and Gemini for cold storage across Dallas–Fort Worth with 90% AI visibility.' ),
			'faq' => array(
				array( 'Where is Yroc\'s San Angelo office?', 'We\'re downtown at 133 W Concho Ave #301, San Angelo, TX 76903. Call 325.340.4358 to set up a time to meet.' ),
				array( 'Do you work with businesses outside San Angelo?', 'Yes. We serve the whole Concho Valley and West Texas, and our team works with businesses across Texas and the country.' ),
				array( 'Can a small-town business get recommended by ChatGPT?', 'Absolutely. In smaller markets AI often has fewer strong sources to choose from, so a clear entity, good reviews and answer-first content can win recommendations quickly.' ),
			),
		),
		'lubbock-tx' => array(
			'city' => 'Lubbock', 'region' => 'TX', 'main' => false, 'name' => 'Yroc Consulting, LLC',
			'street' => '', 'zip' => '',
			'phone' => '806.808.0619', 'tel' => '+18068080619',
			'hours' => 'Monday – Friday, 8:30am – 5pm CT', 'lat' => 33.6100185, 'lng' => -101.821326,
			'gbp' => 'https://maps.app.goo.gl/dBbTkcg7rXU3xUVk8',
			'region_name' => 'Lubbock & the South Plains',
			'areas' => array( 'Lubbock', 'Wolfforth', 'Shallowater', 'Idalou', 'Slaton', 'Levelland', 'Lamesa', 'Brownfield', 'Plainview', 'Post', 'Littlefield', 'Snyder' ),
			'intro' => 'Yroc serves Lubbock and the South Plains as a service-area team: we come to you, meet by video, and teach AI visibility to local business owners and professionals across the region.',
			'local' => array(
				array( 'Lubbock businesses AI recommends', 'Our Lubbock clients are named by ChatGPT, Google AI Overviews, AI Mode and Gemini for questions like emergency HVAC repair, BBQ catering and the best real estate agents in Lubbock.' ),
				array( 'Training Lubbock professionals', 'Our founder Cory Long has taught AI visibility to Lubbock real estate teams through Keller Williams, helping agents become the names AI recommends in West Texas.' ),
				array( 'We come to you', 'As a service-area team we meet at your business or by video, so you get the same strategy and support as our office clients.' ),
			),
			'proof' => array( 'Lubbock results', 'Lubbock clients like Bigham\'s Smokehouse and The Scott Toman Team are recommended in AI answers for their most valuable local questions, tracked every week.' ),
			'faq' => array(
				array( 'Does Yroc have an office in Lubbock?', 'We serve Lubbock as a service-area business. Our team meets clients at their location or by video. Call 806.808.0619 to set up a meeting.' ),
				array( 'Which Lubbock businesses do you work with?', 'Home services, restaurants, real estate teams, professional services and more. If your customers ask AI who to call in Lubbock, we can help you be the answer.' ),
				array( 'Do you offer AI training for Lubbock teams?', 'Yes. Our founder teaches AI visibility to local teams and associations, and agencies can get certified through the AI Authority Engine.' ),
			),
		),
	);
}

/**
 * Signals in the Yroc Method entity graph: label => array( phase 0|1|2, why it matters for AI ).
 * Phase 0 = Identify, 1 = Verify, 2 = Recommend. Labels must match the graph in assets/js/main.js.
 */
function yroc_signals() {
	return array(
		'Google Business Profile' => array( 0, 'The single most important local source. Google\'s AI Overviews and AI Mode pull your categories, services, hours and reviews straight from it, and other AI tools use it to confirm you are a real, active business.' ),
		'Website'                 => array( 0, 'Your official source of truth. AI reads it to learn exactly what you do, where and for whom. Clear service pages and answer-first content give it something accurate to repeat.' ),
		'Schema.org'              => array( 0, 'Structured data labels your business, services, locations and reviews in a language machines read first. It removes guesswork, and less guesswork means more confident recommendations.' ),
		'Wikidata'                => array( 0, 'A public knowledge base that feeds Google\'s Knowledge Graph and many AI models. An accurate entry helps AI recognize you as a distinct, verified entity.' ),
		'LinkedIn'                => array( 0, 'Connects your business to real people, leadership and expertise. AI uses these relationships to judge credibility and to tell similar businesses apart.' ),
		'Bing Places'             => array( 0, 'Microsoft Copilot and ChatGPT search lean heavily on Bing data. A complete Bing Places listing helps you show up in their answers, not just Google\'s.' ),
		'Apple Maps'              => array( 0, 'Siri, Apple Maps and Apple Intelligence draw on Apple Business Connect. Consistent details here extend your visibility to every iPhone user asking for a recommendation.' ),
		'Founder bio'             => array( 0, 'AI weighs who is behind a business. A clear bio with credentials and experience links your company to a trusted person, which strengthens both entities.' ),
		'NAP citations'           => array( 0, 'Your name, address and phone listed identically across directories. Consistency tells AI every mention belongs to the same business; conflicts create doubt.' ),
		'Google reviews'          => array( 1, 'Volume, rating, recency and what customers actually say all feed AI summaries. Reviews that mention specific services help AI match you to those exact questions.' ),
		'Yelp'                    => array( 1, 'A major review source for ChatGPT and Apple. Strong, recent Yelp reviews independently confirm your quality outside of Google.' ),
		'BBB'                     => array( 1, 'An independent trust and accreditation signal. AI treats third-party verification like the Better Business Bureau as evidence you are established and accountable.' ),
		'Press mention'           => array( 1, 'Coverage in news or industry publications is a strong authority signal. AI gives extra weight to what reputable outside sources say about you.' ),
		'Podcast'                 => array( 1, 'Interviews show expertise in your own words and create transcripts and show notes that AI can index, quote and associate with your name.' ),
		'YouTube'                 => array( 1, 'The second-largest search engine. Videos and their transcripts are increasingly used as sources in AI answers, especially for how-to and comparison questions.' ),
		'Industry directory'      => array( 1, 'Trade associations and niche directories confirm what you do and that peers recognize you, which helps AI place you in the right category.' ),
		'Case study'              => array( 1, 'Documented results with numbers give AI concrete proof to cite when someone asks who is good at a specific job.' ),
		'Clutch'                  => array( 1, 'Verified B2B reviews from a respected platform. AI often references Clutch when recommending agencies and professional service firms.' ),
		'Local news'              => array( 1, 'Local coverage ties your business to your community and location, a strong signal for "near me" and city-specific AI questions.' ),
		'FAQ answers'             => array( 2, 'Short, direct answers to real customer questions are exactly what AI extracts and quotes. Well-structured FAQs are among the most-cited content on the web.' ),
		'Knowledge panel'         => array( 2, 'When Google shows a Knowledge Panel for you, it means it has confidently identified your entity. That confidence carries into AI Overviews and AI Mode.' ),
		'Expert article'          => array( 2, 'In-depth content on your specialty builds topical authority, so AI sees you as a go-to source rather than one name among many.' ),
		'Comparison page'         => array( 2, 'People ask AI to compare options. Honest comparison content gives AI the context it needs to explain why you are the right choice.' ),
		'Awards'                  => array( 2, 'Recognition from third parties is a shortcut to trust. AI often mentions awards and "best of" honors when explaining a recommendation.' ),
	);
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
		array(
			'label' => 'Industries',
			'children' => array(
				array( 'All Industries', '/industries/' ),
				array( 'Home & Construction', '/industries/#home' ),
				array( 'Health & Wellness', '/industries/#health' ),
				array( 'Legal & Financial', '/industries/#legal' ),
				array( 'Real Estate & Property', '/industries/#property' ),
				array( 'Restaurants & Automotive', '/industries/#retail' ),
				array( 'B2B & Agencies', '/industries/#b2b' ),
			),
		),
		array( 'label' => 'AI Authority Engine', 'url' => 'https://aiauthorityengine.org/' ),
		array( 'label' => 'Websites', 'url' => '/website-packages/' ),
		array( 'label' => 'Results', 'url' => '/case-studies/' ),
		array(
			'label' => 'Resources',
			'children' => array(
				array( 'Blog', '/blog/' ),
				array( 'AI Learning Center', '/kb/' ),
				array( 'Podcast', '/podcasts/how-ai-is-changing-marketing/' ),
				array( 'Speaking', '/about-us/#speaking' ),
			),
		),
		array(
			'label' => 'About',
			'children' => array(
				array( 'About Yroc', '/about-us/' ),
				array( 'Locations', '/locations/' ),
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
