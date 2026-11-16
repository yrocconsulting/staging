<?php
/**
 * Title: Site Header (mega menu)
 * Slug: yroc/header
 * Categories: header
 * Inserter: no
 */
$i   = yroc_info();
$req = isset( $_SERVER['REQUEST_URI'] ) ? strtok( wp_unslash( $_SERVER['REQUEST_URI'] ), '?' ) : '/';
?>
<!-- wp:html -->
<div class="yr-topbar">
	<div class="yr-wrap yr-topbar__in">
		<span class="yr-topbar__hq">Headquartered in <b>Sherman, Texas</b> &middot; Serving businesses nationwide &middot; Advising companies since 2005</span>
		<span class="yr-topbar__links">
			<a href="tel:<?php echo esc_attr( $i['tel'] ); ?>"><?php echo esc_html( $i['phone'] ); ?></a>
			<a href="mailto:<?php echo esc_attr( $i['email'] ); ?>"><?php echo esc_html( $i['email'] ); ?></a>
		</span>
	</div>
</div>
<header class="yr-header" data-yr-header>
	<div class="yr-wrap yr-header__in">
		<a class="yr-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Yroc Consulting home">
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo.png' ) ); ?>" alt="Yroc Consulting" width="185" height="56" fetchpriority="high">
		</a>
		<button class="yr-burger" type="button" aria-expanded="false" aria-controls="yr-nav" data-yr-burger>
			<span></span><span></span><span></span><span class="screen-reader-text">Menu</span>
		</button>
		<nav class="yr-nav" id="yr-nav" aria-label="Primary">
			<ul class="yr-nav__list">
				<?php foreach ( yroc_primary_nav() as $n => $item ) : ?>
					<?php if ( ! empty( $item['mega'] ) ) : ?>
						<li class="yr-nav__item has-mega">
							<button class="yr-nav__link" type="button" aria-expanded="false" aria-controls="yr-mega" data-yr-drop><?php echo esc_html( $item['label'] ); ?> <?php echo yroc_icon( 'chev' ); ?></button>
							<div class="yr-mega" id="yr-mega">
								<div class="yr-wrap yr-mega__grid">
									<?php foreach ( yroc_mega_menu() as $col ) : ?>
										<div class="yr-mega__col">
											<p class="yr-mega__title"><?php echo yroc_icon( $col['icon'] ); ?> <?php echo esc_html( $col['title'] ); ?></p>
											<ul>
												<?php foreach ( $col['links'] as $l ) : ?>
													<li><a <?php echo yroc_href( $l[1] ); // phpcs:ignore ?>><strong><?php echo esc_html( $l[0] ); ?><?php echo yroc_is_external( $l[1] ) ? ' <i class="yr-ext" aria-hidden="true">↗</i><span class="yr-sr"> (opens aiauthorityengine.org)</span>' : ''; ?></strong><span><?php echo esc_html( $l[2] ); ?></span></a></li>
												<?php endforeach; ?>
											</ul>
										</div>
									<?php endforeach; ?>
									<div class="yr-mega__promo">
										<p class="yr-eyebrow">Free for a limited time</p>
										<p class="yr-mega__promo-h">How does AI describe your business?</p>
										<p>Get an AI Visibility Audit and see where you stand on ChatGPT, Gemini, Perplexity and Google AI.</p>
										<a class="yr-btn yr-btn--sm" href="<?php echo esc_url( home_url( '/ai-visibility-audit/' ) ); ?>">Get my audit <?php echo yroc_icon( 'arrow' ); ?></a>
									</div>
								</div>
							</div>
						</li>
					<?php elseif ( ! empty( $item['children'] ) ) : ?>
						<li class="yr-nav__item has-drop">
							<button class="yr-nav__link" type="button" aria-expanded="false" data-yr-drop><?php echo esc_html( $item['label'] ); ?> <?php echo yroc_icon( 'chev' ); ?></button>
							<ul class="yr-drop">
								<?php foreach ( $item['children'] as $c ) : ?>
									<li><a href="<?php echo esc_url( home_url( $c[1] ) ); ?>"><?php echo esc_html( $c[0] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php else : ?>
						<li class="yr-nav__item"><a class="yr-nav__link<?php echo ( ! yroc_is_external( $item['url'] ) && 0 === strpos( $req, $item['url'] ) ) ? ' is-current' : ''; ?>" <?php echo yroc_href( $item['url'] ); // phpcs:ignore ?>><?php echo esc_html( $item['label'] ); ?><?php echo yroc_is_external( $item['url'] ) ? ' <i class="yr-ext" aria-hidden="true">↗</i>' : ''; ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<a class="yr-header__phone" href="tel:<?php echo esc_attr( $i['tel'] ); ?>"><?php echo esc_html( $i['phone'] ); ?></a>
			<a class="yr-btn yr-header__cta" href="<?php echo esc_url( home_url( '/book-a-strategy-call/' ) ); ?>">Book a strategy call</a>
		</nav>
	</div>
</header>
<!-- /wp:html -->
