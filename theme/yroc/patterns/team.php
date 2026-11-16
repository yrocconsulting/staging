<?php
/**
 * Title: Team (departments, no names)
 * Slug: yroc/team
 * Categories: yroc
 */
?>
<!-- wp:html -->
<section class="yr-section" id="team">
	<div class="yr-wrap">
		<div class="yr-sechead">
			<div><p class="yr-eyebrow">Our team</p><h2>A full team. <em>A real office.</em> Accountable for your results.</h2></div>
			<p class="yr-lead">You won't be handed off to a call center or a freelancer. Every client is supported by specialists in our Sherman, Texas office: strategy, technology, design, content, reputation and account management under one roof.</p>
		</div>
		<div class="yr-depts-grid">
			<?php foreach ( yroc_departments() as $d ) : ?>
				<div class="yr-dept yr-reveal">
					<span class="yr-dept__ico"><?php echo esc_html( $d[0] ); ?></span>
					<h3><?php echo esc_html( $d[1] ); ?></h3>
					<p><?php echo esc_html( $d[2] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<!-- /wp:html -->
