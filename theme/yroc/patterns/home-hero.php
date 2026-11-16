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
<section class="yr-hero yr-hero--video" aria-labelledby="yr-hero-h">
	<div class="yr-hero__media" aria-hidden="true">
		<video data-yr-video autoplay muted loop playsinline preload="metadata" poster="<?php echo esc_url( $img ? $img : get_theme_file_uri( 'assets/video/hero-speaking-poster.jpg' ) ); ?>">
			<source src="<?php echo esc_url( get_theme_file_uri( 'assets/video/hero-speaking.mp4' ) ); ?>" type="video/mp4">
		</video>
	</div>
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
			<p class="yr-hero__cap"><span class="yr-live">On stage</span> Cory Long, founder, speaking at the Magnify Your Wealth Summit</p>
			<div class="yr-float yr-chip yr-reveal"><span class="yr-live">Live tracking</span><b data-count="10517230">10,517,230</b><span>leads generated for clients</span></div>
			<div class="yr-float yr-aicard" data-yr-aicard aria-live="polite">
				<div class="yr-aicard__top"><span class="yr-aicard__av"></span><span data-src>ChatGPT · answer preview</span></div>
				<p class="yr-aicard__q" data-q>› Who's the most trusted roofing company in Tulsa?</p>
				<p class="yr-aicard__a" data-a>The most consistently recommended option is <b>Blacksmith Roofing</b> — 4.9★ across hundreds of reviews and frequently cited by local sources.</p>
				<a class="yr-aicard__go" data-go href="<?php echo esc_url( home_url( '/ai-visibility-audit/' ) ); ?>">Get my real AI Visibility Audit →</a>
			</div>
		</div>
	</div>
	<button class="yr-hero__pause" type="button" data-yr-pause aria-pressed="false"><span class="yr-sr">Pause background video</span></button>
</section>
<!-- /wp:html -->
