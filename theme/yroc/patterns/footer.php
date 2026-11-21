<?php
/**
 * Title: Site Footer
 * Slug: yroc/footer
 * Categories: footer
 * Inserter: no
 */
$i = yroc_info();
$cols = yroc_mega_menu();
?>
<!-- wp:html -->
<footer class="yr-footer">
		<div class="yr-wrap">
		<div class="yr-footer__cta">
			<div>
				<p class="yr-eyebrow">Ready when you are</p>
				<p class="yr-footer__cta-h">Become the answer <em>AI recommends.</em></p>
			</div>
			<div class="yr-footer__cta-btns">
				<a class="yr-btn" href="<?php echo esc_url( home_url( '/ai-visibility-audit/' ) ); ?>">Get a free AI Visibility Audit</a>
				<a class="yr-btn yr-btn--ghost" href="tel:<?php echo esc_attr( $i['tel'] ); ?>">Call <?php echo esc_html( $i['phone'] ); ?></a>
			</div>
		</div>
		<div class="yr-footer__grid">
			<div class="yr-footer__brand">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo-white.png' ) ); ?>" alt="Yroc Consulting" width="185" height="56" loading="lazy">
				<p>Helping businesses become easier for AI platforms to understand, trust and recommend.</p>
				<address>
					<?php echo yroc_icon( 'pin' ); ?> <?php echo esc_html( $i['street'] ); ?><br><?php echo esc_html( $i['city'] . ', ' . $i['region'] . ' ' . $i['zip'] ); ?><br>
					<a href="tel:<?php echo esc_attr( $i['tel'] ); ?>"><?php echo yroc_icon( 'phone' ); ?> <?php echo esc_html( $i['phone'] ); ?> (<?php echo esc_html( $i['phone_alt'] ); ?>)</a><br>
					<a href="mailto:<?php echo esc_attr( $i['email'] ); ?>"><?php echo yroc_icon( 'mail' ); ?> <?php echo esc_html( $i['email'] ); ?></a>
				</address>
				<div class="yr-social">
					<?php foreach ( $i['social'] as $net => $url ) : ?>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $net ) ); ?>"><?php echo yroc_social_svg( $net ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
			<?php foreach ( array_slice( $cols, 0, 3 ) as $col ) : ?>
				<div class="yr-footer__col">
					<p class="yr-footer__h"><?php echo esc_html( $col['title'] ); ?></p>
					<ul>
						<?php foreach ( $col['links'] as $l ) : ?>
							<li><a <?php echo yroc_href( $l[1] ); // phpcs:ignore ?>><?php echo esc_html( $l[0] ); ?><?php echo yroc_is_external( $l[1] ) ? ' ↗' : ''; ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
			<div class="yr-footer__col">
				<p class="yr-footer__h">Company</p>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">About Yroc</a></li>
					<li><a href="<?php echo esc_url( home_url( '/website-packages/' ) ); ?>">Website Packages</a></li>
					<li><a href="<?php echo esc_url( home_url( '/white-label-services/' ) ); ?>">White Label Partners</a></li>
					<li><a href="<?php echo esc_url( home_url( '/case-studies/' ) ); ?>">Case Studies</a></li>
					<li><a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>">Reviews</a></li>
					<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Blog</a></li>
					<li><a href="<?php echo esc_url( home_url( '/kb/' ) ); ?>">AI Learning Center</a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
				</ul>
			</div>
		</div>
		<div class="yr-footer__locs">
			<?php foreach ( yroc_locations() as $slug => $l ) : ?>
				<div>
					<b><a href="<?php echo esc_url( home_url( '/locations/' . $slug . '/' ) ); ?>"><?php echo esc_html( $l['city'] . ', ' . $l['region'] ); ?></a></b>
					<?php echo $l['street'] ? esc_html( $l['street'] . ', ' . $l['city'] . ', ' . $l['region'] . ' ' . $l['zip'] ) : esc_html( 'Serving ' . $l['region_name'] ); ?><br>
					<a href="tel:<?php echo esc_attr( $l['tel'] ); ?>"><?php echo esc_html( $l['phone'] ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="yr-footer__base">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Yroc Consulting. All rights reserved.</p>
			<p class="yr-footer__legal">
				<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy</a>
				<a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>">Terms</a>
				<a href="<?php echo esc_url( home_url( '/accessibility-statement/' ) ); ?>">Accessibility</a>
				<a href="<?php echo esc_url( home_url( '/disclaimer/' ) ); ?>">Disclaimer</a>
			</p>
		</div>
	</div>
</footer>
<!-- /wp:html -->
