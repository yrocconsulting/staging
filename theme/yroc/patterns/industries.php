<?php
/**
 * Title: Industries Strip
 * Slug: yroc/industries
 * Categories: yroc
 */
$ind = array( 'Roofing & Construction', 'Tax Resolution', 'Medical & Wellness', 'Med Spas', 'Pest Control', 'Law Firms', 'Home Services', 'Real Estate', 'Professional Services', 'Restaurants', 'Marketing Agencies' );
?>
<!-- wp:html -->
<div class="yr-strip"><div class="yr-wrap">
	<p>Trusted by businesses across every kind of industry</p>
	<div class="yr-marq-box" aria-label="Industries we serve"><div class="yr-marq">
		<?php foreach ( array_merge( $ind, $ind ) as $k => $n ) : ?><span<?php echo $k >= count( $ind ) ? ' aria-hidden="true"' : ''; ?>><?php echo esc_html( $n ); ?></span><?php endforeach; ?>
	</div></div>
</div></div>
<!-- /wp:html -->
