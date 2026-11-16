<?php
/**
 * Title: Speaking & Media
 * Slug: yroc/speaking
 * Categories: yroc
 */
?>
<!-- wp:html -->
<section class="yr-speak" id="speaking" aria-labelledby="yr-speak-h">
	<div class="yr-wrap yr-speak__in">
		<div class="yr-speak__head">
			<p class="yr-eyebrow">Speaking &amp; media</p>
			<h2 id="yr-speak-h">The experts <em>other experts</em> invite to speak.</h2>
			<p>Our team regularly teaches AI search, local marketing and lead generation to business owners and industry associations across the country.</p>
		</div>
		<ul class="yr-speak__list">
			<?php foreach ( yroc_speaking() as $s ) : ?>
				<li class="yr-reveal"><b><?php echo esc_html( $s[0] ); ?></b><span><?php echo esc_html( $s[1] ); ?></span></li>
			<?php endforeach; ?>
		</ul>
		<a class="yr-speak__cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Book us to speak at your event →</a>
	</div>
</section>
<!-- /wp:html -->
