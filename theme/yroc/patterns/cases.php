<?php
/**
 * Title: Client Results Grid
 * Slug: yroc/cases
 * Categories: yroc
 */
$cases = array(
	array( 'Roofing · Oklahoma', '8', '×', 'phone calls in one month', 'A roofing contractor\'s inbound calls octupled within a single month.', '/case-studies/blacksmith-roofing-case-study/' ),
	array( 'Tax Resolution · National', '604', '%', 'return on investment', 'We helped a tax firm outrank the IRS on Google, then turned that traffic into high-value leads.', '/case-studies/irs-trouble-solvers-case-study/' ),
	array( 'Pest Control · Local', '+285', '%', 'organic traffic in one month', 'A locally owned pest control company nearly quadrupled its organic traffic in 30 days.', '/case-studies/the-bug-guys/' ),
	array( 'Weight Loss Clinic', '50–100', '', 'qualified leads every month', 'A bold SEO strategy doubled new users and produced steady leads without spending on ads.', '/case-studies/medical-weight-loss-case-study/' ),
	array( 'Hormone Clinic', '$20', 'K', 'new revenue in 60 days', 'SEO, paid social and local marketing drove a 70% traffic surge and $20K in new bookings.', '/case-studies/hormone-replacement-therapy-clinic-case-study/' ),
	array( 'Reputation Management', '90', ' days', 'to own page one', 'Negative press buried a brand. Ninety days later, the client controlled their search narrative.', '/case-studies/fractional-cfo-reputation-management-case-study/' ),
);
?>
<!-- wp:html -->
<section class="yr-section yr-bg" id="results">
	<div class="yr-wrap">
		<div class="yr-sechead">
			<div><p class="yr-eyebrow">Client results</p><h2>Results across <em>every industry</em> we touch.</h2></div>
			<p class="yr-lead">From local service companies to multi-location clinics and national brands, our work is measured in real business outcomes.</p>
		</div>
		<div class="yr-cases">
			<?php foreach ( $cases as $c ) : ?>
				<a class="yr-casecard yr-reveal" href="<?php echo esc_url( home_url( $c[5] ) ); ?>">
					<span class="yr-casecard__tag"><?php echo esc_html( $c[0] ); ?></span>
					<span class="yr-casecard__m"><?php echo esc_html( $c[1] ); ?><small><?php echo esc_html( $c[2] ); ?></small></span>
					<span class="yr-casecard__l"><?php echo esc_html( $c[3] ); ?></span>
					<p><?php echo esc_html( $c[4] ); ?></p>
					<span class="yr-casecard__go">Read case study →</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<!-- /wp:html -->
