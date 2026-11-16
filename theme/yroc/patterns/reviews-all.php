<?php
/**
 * Title: All Client Reviews (Trustindex grid)
 * Slug: yroc/reviews-all
 * Categories: yroc
 */
$rv = yroc_reviews();
?>
<!-- wp:html -->
<section class="yr-section yr-section--tight" id="client-reviews">
	<div class="yr-wrap">
		<div class="yr-sechead">
			<div><p class="yr-eyebrow">Client reviews</p><h2>From the businesses <em>we work with.</em></h2></div>
			<p class="yr-lead">Verified 5-star reviews collected across Google, Trustindex, Yelp and Facebook. <a href="<?php echo esc_url( YROC_TI_URL ); ?>" target="_blank" rel="noopener">See every review on Trustindex ↗</a></p>
		</div>
		<div class="yr-rgrid">
			<?php foreach ( $rv['items'] as $r ) : ?>
				<figure class="yr-rcard yr-rcard--light yr-reveal">
					<span class="yr-stars" aria-label="5 out of 5 stars">★★★★★</span>
					<blockquote><?php echo esc_html( $r['body'] ); ?></blockquote>
					<figcaption><span class="yr-rcard__av"><?php echo esc_html( strtoupper( substr( $r['author'], 0, 1 ) ) ); ?></span><span><b><?php echo esc_html( $r['author'] ); ?></b><?php echo esc_html( ! empty( $r['date'] ) ? date_i18n( 'F Y', strtotime( $r['date'] ) ) : 'Verified review' ); ?></span></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<!-- /wp:html -->
