<?php
/**
 * Title: Client Results Grid
 * Slug: yroc/cases
 * Categories: yroc
 */
$cases = array_slice( yroc_cases(), 0, 6 );
?>
<!-- wp:html -->
<section class="yr-section yr-bg" id="results">
	<div class="yr-wrap">
		<div class="yr-sechead">
			<div><p class="yr-eyebrow">Client results</p><h2>Results across <em>every industry</em> we touch.</h2></div>
			<p class="yr-lead">From AI search visibility to reputation recovery and local growth, our work is measured in real business outcomes. <a href="<?php echo esc_url( home_url( '/case-studies/' ) ); ?>">See all case studies →</a></p>
		</div>
		<div class="yr-cases">
			<?php foreach ( $cases as $c ) : ?>
				<a class="yr-casecard yr-reveal" href="<?php echo esc_url( home_url( '/case-studies/' . $c[0] . '/' ) ); ?>">
					<span class="yr-casecard__tag"><?php echo esc_html( $c[2] ); ?></span>
					<span class="yr-casecard__m"><?php echo esc_html( $c[4] ); ?><small><?php echo esc_html( $c[5] ); ?></small></span>
					<span class="yr-casecard__l"><?php echo esc_html( $c[6] ); ?></span>
					<p><strong><?php echo esc_html( $c[1] ); ?>.</strong> <?php echo esc_html( $c[7] ); ?></p>
					<span class="yr-casecard__go">Read case study →</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<!-- /wp:html -->
