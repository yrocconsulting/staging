<?php
/**
 * Title: AI Authority Engine Training Reviews (Trustpilot)
 * Slug: yroc/reviews-aae
 * Categories: yroc
 */
$aae = yroc_aae_reviews();
?>
<!-- wp:html -->
<section class="yr-section yr-dark" id="training-reviews">
	<div class="yr-wrap">
		<div class="yr-sechead">
			<div><p class="yr-eyebrow">Training &amp; certification</p><h2>What agencies say about <em>the AI Authority Engine.</em></h2></div>
			<p class="yr-lead">Our AI SEO certification and training program is run by Yroc Consulting and taught by founder Cory Long. These reviews are published on Trustpilot. <a href="<?php echo esc_url( $aae['source'] ); ?>" target="_blank" rel="noopener">Read them on Trustpilot ↗</a></p>
		</div>
		<div class="yr-rgrid yr-rgrid--dark">
			<?php foreach ( $aae['reviews'] as $r ) : ?>
				<figure class="yr-rcard yr-reveal">
					<span class="yr-stars" aria-label="5 out of 5 stars">★★★★★</span>
					<h3 class="yr-rcard__t"><?php echo esc_html( $r['title'] ); ?></h3>
					<blockquote><?php echo esc_html( $r['body'] ); ?></blockquote>
					<a class="yr-rcard__more" href="<?php echo esc_url( $r['url'] ); ?>" target="_blank" rel="noopener">Read the full review on Trustpilot ↗</a>
					<figcaption><span class="yr-rcard__av"><?php echo esc_html( strtoupper( substr( $r['author'], 0, 1 ) ) ); ?></span><span><b><?php echo esc_html( $r['author'] ); ?></b><?php echo ! empty( $r['verified'] ) ? 'Certified AI Authority Specialist' : esc_html( 'Trustpilot · ' . $r['date'] ); ?></span></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
		<div class="yr-platforms">
			<a href="https://aiauthorityengine.org/" target="_blank" rel="noopener">About the AI Authority Engine ↗</a>
			<a href="https://aiauthorityengine.org/certified/" target="_blank" rel="noopener">Certified specialist directory ↗</a>
		</div>
	</div>
</section>
<!-- /wp:html -->
