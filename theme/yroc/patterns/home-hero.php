<?php
/**
 * Title: Home Hero (AI check + HQ visual)
 * Slug: yroc/home-hero
 * Categories: yroc
 */
$img = function_exists( 'yroc_hero_image' ) ? yroc_hero_image() : '';
$rv  = yroc_reviews();
?>
<!-- wp:html -->
<section class="yr-hero" aria-labelledby="yr-hero-h">
	<div class="yr-wrap yr-hero__grid">
		<div>
			<p class="yr-eyebrow">AI Search Optimization Firm</p>
			<h1 id="yr-hero-h">AI is deciding who your customers call. <em>We make sure</em> it <mark class="yr-hl">chooses you.</mark></h1>
			<p class="yr-lead">Yroc Consulting is a team of strategists, engineers and content specialists helping businesses get recommended by ChatGPT, Gemini, Perplexity and Google AI — backed by more than 15 years of search results.</p>
			<form class="yr-check" data-yr-check action="<?php echo esc_url( home_url( '/ai-visibility-audit/' ) ); ?>" method="get" autocomplete="off">
				<label><span>Your business</span><input name="business" placeholder="Your Business Name" aria-label="Your business name"></label>
				<label><span>City</span><input name="market" placeholder="Dallas, TX" aria-label="City"></label>
				<button class="yr-btn yr-btn--green" type="submit">See what AI says →</button>
			</form>
			<p class="yr-check__note">Instant preview — your full AI Visibility Audit is prepared by our team.</p>
			<div class="yr-trust">
				<div><b><span class="yr-stars" aria-hidden="true">★★★★★</span> <?php echo esc_html( number_format( (float) $rv['rating'], 1 ) ); ?></b><span><?php echo esc_html( $rv['count'] ); ?> verified client reviews</span></div>
				<div><b>400+</b><span>companies advised</span></div>
				<div><b>$94.5M</b><span>client revenue generated</span></div>
			</div>
		</div>
		<div class="yr-stage">
			<figure class="yr-visual">
				<?php if ( $img ) : ?>
					<img src="<?php echo esc_url( $img ); ?>" alt="Yroc Consulting headquarters in Sherman, Texas" fetchpriority="high">
				<?php else : ?>
					<svg class="yr-visual__map" viewBox="0 0 600 700" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
						<defs><radialGradient id="yrglow" cx="54%" cy="38%" r="45%"><stop offset="0" stop-color="#7ED957" stop-opacity=".22"/><stop offset="1" stop-color="#7ED957" stop-opacity="0"/></radialGradient></defs>
						<rect width="600" height="700" fill="#0B1220"/>
						<g stroke="#547590" stroke-opacity=".25" fill="none">
							<path d="M-20 120 L620 90" stroke-width="10"/><path d="M-20 300 C 200 280, 380 330, 620 300" stroke-width="14"/><path d="M-20 520 L620 560" stroke-width="9"/>
							<path d="M120 -20 L150 720" stroke-width="9"/><path d="M330 -20 C 310 200, 350 420, 320 720" stroke-width="16"/><path d="M500 -20 L470 720" stroke-width="8"/>
							<path d="M-20 420 L620 380" stroke-width="4"/><path d="M230 -20 L250 720" stroke-width="4"/><path d="M-20 200 L620 220" stroke-width="4"/><path d="M420 -20 L410 720" stroke-width="4"/><path d="M-20 640 L620 620" stroke-width="4"/>
						</g>
						<g fill="#132036"><rect x="170" y="140" width="45" height="70" rx="4"/><rect x="360" y="130" width="90" height="60" rx="4"/><rect x="260" y="330" width="50" height="40" rx="4"/><rect x="360" y="340" width="95" height="30" rx="4"/><rect x="170" y="440" width="140" height="60" rx="4"/><rect x="520" y="230" width="60" height="50" rx="4"/><rect x="30" y="330" width="80" height="70" rx="4"/></g>
						<rect width="600" height="700" fill="url(#yrglow)"/>
					</svg>
					<span class="yr-pin" aria-hidden="true"><i></i></span>
				<?php endif; ?>
				<figcaption><b>Yroc Consulting Headquarters</b>1800 Teague Dr. Ste. 416 · Sherman, Texas</figcaption>
			</figure>
			<div class="yr-float yr-chip yr-reveal"><span class="yr-live">Live tracking</span><b data-count="10517230">10,517,230</b><span>leads generated for clients</span></div>
			<div class="yr-float yr-aicard" data-yr-aicard aria-live="polite">
				<div class="yr-aicard__top"><span class="yr-aicard__av"></span><span data-src>ChatGPT · answer preview</span></div>
				<p class="yr-aicard__q" data-q>› Who's the most trusted roofing company in Tulsa?</p>
				<p class="yr-aicard__a" data-a>The most consistently recommended option is <b>Blacksmith Roofing</b> — 4.9★ across hundreds of reviews and frequently cited by local sources.</p>
				<a class="yr-aicard__go" data-go href="<?php echo esc_url( home_url( '/ai-visibility-audit/' ) ); ?>">Get my real AI Visibility Audit →</a>
			</div>
		</div>
	</div>
</section>
<!-- /wp:html -->
