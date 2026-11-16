<?php
/**
 * Title: Office / Visit Us
 * Slug: yroc/office
 * Categories: yroc
 */
$i = yroc_info();
?>
<!-- wp:html -->
<section class="yr-section yr-section--tight">
	<div class="yr-wrap">
		<div class="yr-office yr-reveal">
			<div class="yr-office__info">
				<p class="yr-eyebrow">Visit us</p>
				<h2>Come see us in <em>Sherman.</em></h2>
				<dl>
					<dt>Office</dt><dd><?php echo esc_html( $i['street'] ); ?><br><?php echo esc_html( $i['city'] . ', ' . $i['region'] . ' ' . $i['zip'] ); ?></dd>
					<dt>Phone</dt><dd><a href="tel:<?php echo esc_attr( $i['tel'] ); ?>"><?php echo esc_html( $i['phone'] . ' (' . $i['phone_alt'] . ')' ); ?></a></dd>
					<dt>Email</dt><dd><a href="mailto:<?php echo esc_attr( $i['email'] ); ?>"><?php echo esc_html( $i['email'] ); ?></a></dd>
					<dt>Hours</dt><dd>Monday – Friday, 9am – 5pm CT</dd>
				</dl>
				<a class="yr-btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Schedule an in-person meeting</a>
			</div>
			<iframe title="Yroc Consulting office on Google Maps" src="https://www.google.com/maps?q=1800+Teague+Dr+Ste+416,+Sherman,+TX+75090&amp;output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
		</div>
	</div>
</section>
<!-- /wp:html -->
