<?php
/**
 * Title: Case Studies Grid (filterable)
 * Slug: yroc/case-grid
 * Categories: yroc
 */
$cats = yroc_case_categories();
?>
<!-- wp:html -->
<section class="yr-section yr-section--tight" id="all-cases">
	<div class="yr-wrap">
		<div class="yr-filter" role="toolbar" aria-label="Filter case studies" data-yr-filter>
			<button type="button" aria-pressed="true" data-cat="all">All results</button>
			<?php foreach ( $cats as $k => $label ) : ?>
				<button type="button" aria-pressed="false" data-cat="<?php echo esc_attr( $k ); ?>" id="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>
		<div class="yr-cases" data-yr-cases>
			<?php foreach ( yroc_cases() as $c ) : ?>
				<a class="yr-casecard yr-reveal" data-cat="<?php echo esc_attr( $c[3] ); ?>" href="<?php echo esc_url( home_url( '/case-studies/' . $c[0] . '/' ) ); ?>">
					<span class="yr-casecard__tag"><?php echo esc_html( $cats[ $c[3] ] . ' · ' . $c[2] ); ?></span>
					<span class="yr-casecard__m"><?php echo esc_html( $c[4] ); ?><small><?php echo esc_html( $c[5] ); ?></small></span>
					<span class="yr-casecard__l"><?php echo esc_html( $c[6] ); ?></span>
					<p><strong><?php echo esc_html( $c[1] ); ?>.</strong> <?php echo esc_html( $c[7] ); ?></p>
					<span class="yr-casecard__go">Read the case study →</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<!-- /wp:html -->
