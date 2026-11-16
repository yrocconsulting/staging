<?php
/**
 * Title: Reviews Page Hero (ratings by platform)
 * Slug: yroc/reviews-hero
 * Categories: yroc
 */
$rv  = yroc_reviews();
$aae = yroc_aae_reviews();
$i   = yroc_info();
?>
<!-- wp:html -->
<section class="yr-pagehero yr-casehero">
	<div class="yr-wrap">
		<p class="yr-eyebrow">Reviews</p>
		<h1>Don't take <em>our word</em> for it.</h1>
		<p class="yr-lead">Independent reviews from the businesses we work with and the agencies we train, published on Trustindex, Clutch and Trustpilot.</p>
		<div class="yr-ragg">
			<a href="<?php echo esc_url( YROC_TI_URL ); ?>" target="_blank" rel="noopener"><span>Trustindex</span><b><?php echo esc_html( number_format( (float) $rv['rating'], 1 ) ); ?> <small class="yr-stars">★★★★★</small></b><?php echo esc_html( $rv['count'] ); ?> verified client reviews across Google, Yelp &amp; Facebook ↗</a>
			<a href="<?php echo esc_url( $i['reviews']['clutch'] ); ?>" target="_blank" rel="noopener"><span>Clutch</span><b>5.0 <small class="yr-stars">★★★★★</small></b>Verified B2B client reviews on Clutch ↗</a>
			<a href="<?php echo esc_url( $aae['source'] ); ?>" target="_blank" rel="noopener"><span>Trustpilot · AI Authority Engine</span><b><?php echo esc_html( count( $aae['reviews'] ) ); ?> <small class="yr-stars">★★★★★</small></b>Five-star reviews of our AI SEO training ↗</a>
		</div>
	</div>
</section>
<!-- /wp:html -->
