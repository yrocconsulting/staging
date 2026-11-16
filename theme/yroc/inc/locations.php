<?php
/**
 * Location pages, rendered from yroc_locations() in inc/site.php.
 *
 *   [yroc_location slug="sherman-tx"]   full location page body
 *   [yroc_locations]                    hub grid of all locations
 *
 * Also outputs LocalBusiness structured data for each location page.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yroc_location_address( $l, $sep = ', ' ) {
	return $l['street'] ? $l['street'] . $sep . $l['city'] . ', ' . $l['region'] . ' ' . $l['zip'] : 'Serving ' . $l['city'] . ', ' . $l['region'] . ' and surrounding areas';
}

function yroc_location_map_src( $l ) {
	$q = $l['street'] ? $l['street'] . ', ' . $l['city'] . ', ' . $l['region'] . ' ' . $l['zip'] : $l['city'] . ', ' . $l['region'];
	return 'https://www.google.com/maps?q=' . rawurlencode( $q ) . '&output=embed';
}

/** Weekly AI tracking wins for clients in this city (from the ProRankTracker showcase). */
function yroc_location_wins( $l ) {
	if ( ! function_exists( 'yroc_ai_showcase' ) ) {
		return array();
	}
	return array_values( array_filter( yroc_ai_showcase(), function ( $r ) use ( $l ) {
		return false !== stripos( $r['city'], $l['city'] ) || false !== stripos( $r['prompt'], $l['city'] );
	} ) );
}

add_shortcode( 'yroc_location', function ( $atts ) {
	$atts = shortcode_atts( array( 'slug' => '' ), $atts );
	$all  = yroc_locations();
	if ( ! isset( $all[ $atts['slug'] ] ) ) {
		return '';
	}
	$l     = $all[ $atts['slug'] ];
	$wins  = yroc_location_wins( $l );
	$plats = array( 'chatgpt' => 'ChatGPT', 'aio' => 'AI Overviews', 'ai_mode' => 'AI Mode', 'gemini' => 'Gemini', 'perplexity' => 'Perplexity', 'copilot' => 'Copilot' );
	$label = $l['main'] ? 'Headquarters' : ( $l['street'] ? 'Office' : 'Service area' );
	ob_start();
	?>
	<section class="yr-pagehero yr-lochero">
		<div class="yr-wrap yr-lochero__grid">
			<div>
				<p class="yr-eyebrow"><a href="<?php echo esc_url( home_url( '/locations/' ) ); ?>">Locations</a> · <?php echo esc_html( $label ); ?></p>
				<h1>AI search optimization in <em><?php echo esc_html( $l['city'] . ', ' . ( 'TX' === $l['region'] ? 'Texas' : $l['region'] ) ); ?></em></h1>
				<p class="yr-lead"><?php echo esc_html( $l['intro'] ); ?></p>
				<div class="wp-block-buttons">
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/ai-visibility-audit/' ) ); ?>">Get a free AI Visibility Audit</a></div>
					<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="tel:<?php echo esc_attr( $l['tel'] ); ?>">Call <?php echo esc_html( $l['phone'] ); ?></a></div>
				</div>
			</div>
			<aside class="yr-loccard" aria-label="<?php echo esc_attr( $l['city'] ); ?> contact details">
				<p class="yr-loccard__tag"><?php echo esc_html( $label ); ?></p>
				<p class="yr-loccard__name"><?php echo esc_html( $l['name'] ); ?></p>
				<dl>
					<dt><?php echo $l['street'] ? 'Address' : 'Service area'; ?></dt>
					<dd><?php echo $l['street'] ? esc_html( $l['street'] ) . '<br>' . esc_html( $l['city'] . ', ' . $l['region'] . ' ' . $l['zip'] ) : esc_html( 'Serving ' . $l['region_name'] ); ?></dd>
					<dt>Phone</dt><dd><a href="tel:<?php echo esc_attr( $l['tel'] ); ?>"><?php echo esc_html( $l['phone'] ); ?></a></dd>
					<dt>Hours</dt><dd><?php echo esc_html( $l['hours'] ); ?></dd>
					<dt>Email</dt><dd><a href="mailto:info@yrocconsulting.com">info@yrocconsulting.com</a></dd>
				</dl>
				<a class="yr-loccard__gbp" href="<?php echo esc_url( $l['gbp'] ); ?>" target="_blank" rel="noopener">View on Google Maps ↗</a>
			</aside>
		</div>
	</section>

	<section class="yr-section yr-section--tight yr-locsec">
		<div class="yr-wrap">
			<div class="yr-sechead">
				<div><p class="yr-eyebrow">Why <?php echo esc_html( $l['city'] ); ?> businesses work with Yroc</p><h2>Local team. <em>AI-era results.</em></h2></div>
				<p class="yr-lead">When customers in <?php echo esc_html( $l['region_name'] ); ?> ask ChatGPT, Gemini or Google AI who to call, only a few businesses get named. We make sure yours is one of them.</p>
			</div>
			<div class="yr-cards wp-block-columns">
				<?php foreach ( $l['local'] as $i => $c ) : ?>
					<div class="yr-card wp-block-column"><p class="yr-card__ico"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p><h3><?php echo esc_html( $c[0] ); ?></h3><p><?php echo esc_html( $c[1] ); ?></p></div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="yr-section yr-dark">
		<div class="yr-wrap">
			<div class="yr-sechead">
				<div><p class="yr-eyebrow">Local proof</p><h2><?php echo esc_html( $l['proof'][0] ); ?></h2></div>
				<p class="yr-lead"><?php echo esc_html( $l['proof'][1] ); ?></p>
			</div>
			<?php if ( $wins ) : ?>
				<div class="yr-locwins">
					<?php foreach ( array_slice( $wins, 0, 4 ) as $w ) : ?>
						<div class="yr-locwin">
							<p class="yr-locwin__q">“<?php echo esc_html( $w['prompt'] ); ?>”</p>
							<p class="yr-locwin__a"><span class="yr-lv__rank">1</span><b><?php echo esc_html( $w['name'] ); ?></b> <small><?php echo esc_html( $w['industry'] ); ?></small></p>
							<p class="yr-locwin__p">Named by <?php echo esc_html( implode( ', ', array_map( function ( $k ) use ( $plats ) { return $plats[ $k ] ?? $k; }, $w['platforms'] ) ) ); ?> · <?php echo (int) $w['visibility']; ?>% visibility</p>
						</div>
					<?php endforeach; ?>
				</div>
				<p class="yr-locwins__src">From our weekly AI visibility tracking. Updated every Monday.</p>
			<?php elseif ( ! empty( $l['cases'] ) ) : ?>
				<div class="yr-cases yr-loccases">
					<?php foreach ( yroc_cases() as $c ) : if ( ! in_array( $c[0], $l['cases'], true ) ) { continue; } ?>
						<a class="yr-casecard" href="<?php echo esc_url( home_url( '/case-studies/' . $c[0] . '/' ) ); ?>">
							<span class="yr-casecard__tag"><?php echo esc_html( $c[2] ); ?></span>
							<span class="yr-casecard__m"><?php echo esc_html( $c[4] ); ?><small><?php echo esc_html( $c[5] ); ?></small></span>
							<span class="yr-casecard__l"><?php echo esc_html( $c[6] ); ?></span>
							<p><strong><?php echo esc_html( $c[1] ); ?>.</strong> <?php echo esc_html( $c[7] ); ?></p>
							<span class="yr-casecard__go">Read the case study →</span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="yr-section">
		<div class="yr-wrap">
			<div class="yr-sechead">
				<div><p class="yr-eyebrow">What we do in <?php echo esc_html( $l['city'] ); ?></p><h2>Everything AI needs to <em>choose you.</em></h2></div>
				<p class="yr-lead">The same services and in-house team we deliver from our Sherman headquarters, focused on your local market.</p>
			</div>
			<div class="yr-locsvcs">
				<?php
				$svcs = array(
					array( 'AI Search Optimization', '/ai-search-optimization/', 'Get recommended by ChatGPT, Gemini and Google AI.' ),
					array( 'AI Visibility Audit', '/ai-visibility-audit/', 'See exactly what AI says about you today.' ),
					array( 'Entity Optimization', '/entity-optimization/', 'Make AI certain who you are and where you serve.' ),
					array( 'AI Reputation Management', '/reputation-management/', 'Control how AI describes your business.' ),
					array( 'AI-Ready Websites', '/website-packages/', 'Websites built for Google and AI search.' ),
					array( 'AI Search Reporting', '/ai-search-reporting/', 'Weekly tracking across every AI platform.' ),
				);
				foreach ( $svcs as $sv ) :
					?>
					<a class="yr-locsvc" href="<?php echo esc_url( home_url( $sv[1] ) ); ?>"><b><?php echo esc_html( $sv[0] ); ?></b><span><?php echo esc_html( $sv[2] ); ?></span></a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="yr-section yr-bg">
		<div class="yr-wrap yr-locarea">
			<div>
				<p class="yr-eyebrow">Areas we serve</p>
				<h2>Serving <?php echo esc_html( $l['region_name'] ); ?></h2>
				<ul class="yr-locarea__list">
					<?php foreach ( $l['areas'] as $a ) : ?><li><?php echo esc_html( $a ); ?></li><?php endforeach; ?>
				</ul>
				<p class="yr-locarea__note">Plus businesses across Texas and nationwide.</p>
			</div>
			<iframe title="Yroc Consulting <?php echo esc_attr( $l['city'] ); ?> on Google Maps" src="<?php echo esc_url( yroc_location_map_src( $l ) ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
		</div>
	</section>

	<section class="yr-section yr-light yr-faq">
		<div class="yr-wrap" style="max-width:860px">
			<p class="yr-eyebrow">FAQ</p>
			<h2 class="yr-h-xl"><?php echo esc_html( $l['city'] ); ?> questions, answered.</h2>
			<?php foreach ( $l['faq'] as $f ) : ?>
				<details class="wp-block-details yr-faq__item"><summary><?php echo esc_html( $f[0] ); ?></summary><p><?php echo esc_html( $f[1] ); ?></p></details>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
} );

add_shortcode( 'yroc_locations', function () {
	ob_start();
	echo '<div class="yr-locgrid">';
	foreach ( yroc_locations() as $slug => $l ) {
		$label = $l['main'] ? 'Headquarters' : ( $l['street'] ? 'Office' : 'Service area' );
		?>
		<div class="yr-loctile">
			<iframe title="<?php echo esc_attr( $l['city'] ); ?> map" src="<?php echo esc_url( yroc_location_map_src( $l ) ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" tabindex="-1" aria-hidden="true"></iframe>
			<div class="yr-loctile__b">
				<p class="yr-loccard__tag"><?php echo esc_html( $label ); ?></p>
				<h2><?php echo esc_html( $l['city'] . ', ' . ( 'TX' === $l['region'] ? 'Texas' : $l['region'] ) ); ?></h2>
				<p><?php echo $l['street'] ? esc_html( $l['street'] ) . '<br>' . esc_html( $l['city'] . ', ' . $l['region'] . ' ' . $l['zip'] ) : esc_html( 'Serving ' . $l['region_name'] ); ?></p>
				<p><a href="tel:<?php echo esc_attr( $l['tel'] ); ?>"><?php echo esc_html( $l['phone'] ); ?></a> · <?php echo esc_html( $l['hours'] ); ?></p>
				<a class="yr-btn" href="<?php echo esc_url( home_url( '/locations/' . $slug . '/' ) ); ?>">Yroc in <?php echo esc_html( $l['city'] ); ?> →</a>
			</div>
		</div>
		<?php
	}
	echo '</div>';
	return ob_get_clean();
} );

/* ---------- LocalBusiness structured data on location pages ---------- */
add_action( 'wp_head', function () {
	if ( ! is_page() ) {
		return;
	}
	$post = get_queried_object();
	if ( ! $post || 'locations' !== get_post_field( 'post_name', $post->post_parent ) ) {
		return;
	}
	$all = yroc_locations();
	if ( ! isset( $all[ $post->post_name ] ) ) {
		return;
	}
	$l    = $all[ $post->post_name ];
	$url  = get_permalink( $post );
	$data = array(
		'@context'           => 'https://schema.org',
		'@type'              => 'ProfessionalService',
		'@id'                => $url . '#localbusiness',
		'name'               => $l['name'],
		'url'                => $url,
		'telephone'          => $l['tel'],
		'email'              => 'info@yrocconsulting.com',
		'image'              => get_theme_file_uri( 'assets/img/og-default.jpg' ),
		'logo'               => get_theme_file_uri( 'assets/img/logo.png' ),
		'parentOrganization' => array( '@id' => home_url( '/' ) . '#organization' ),
		'hasMap'             => $l['gbp'],
		'areaServed'         => array_map( function ( $a ) { return array( '@type' => 'City', 'name' => $a ); }, $l['areas'] ),
		'knowsAbout'         => array( 'AI Search Optimization', 'Entity Optimization', 'Reputation Management', 'Local SEO', 'Website Design' ),
	);
	if ( $l['street'] ) {
		$data['address'] = array( '@type' => 'PostalAddress', 'streetAddress' => $l['street'], 'addressLocality' => $l['city'], 'addressRegion' => $l['region'], 'postalCode' => $l['zip'], 'addressCountry' => 'US' );
		$data['geo']     = array( '@type' => 'GeoCoordinates', 'latitude' => $l['lat'], 'longitude' => $l['lng'] );
	} else {
		$data['address'] = array( '@type' => 'PostalAddress', 'addressLocality' => $l['city'], 'addressRegion' => $l['region'], 'addressCountry' => 'US' );
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}, 7 );
