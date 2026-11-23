<?php
/**
 * Title: Home Hero (live AI results)
 * Slug: yroc/home-hero
 * Categories: yroc
 *
 * The answer window cycles through real prompts from the weekly tracking report where a client
 * was recommended (see yroc_ai_showcase()). Visitors can also type their own business for a preview.
 */
$rv    = yroc_reviews();
$ais   = yroc_ai_stats();
$names = yroc_ai_platforms();
$show  = yroc_ai_showcase();
$big   = max( (int) $ais['total'], (int) $ais['appearances'] );
$asof  = $ais['report_date'] ? date_i18n( 'F j', strtotime( $ais['report_date'] ) ) : '';
$plats = array( 'chatgpt' => 'ChatGPT', 'aio' => 'AI Overviews', 'ai_mode' => 'AI Mode', 'gemini' => 'Gemini' ); // the four platforms we track
$data  = array_map( function ( $r ) {
	return array( 'q' => $r['prompt'], 'n' => $r['name'], 'i' => $r['industry'], 'c' => $r['city'], 'p' => $r['platforms'], 'v' => $r['visibility'] );
}, $show );
$first = $data ? $data[0] : null;
?>
<!-- wp:html -->
<section class="yr-hero yr-hero--live" aria-labelledby="yr-hero-h">
	<div class="yr-hero__bg" aria-hidden="true"><span></span><span></span></div>
	<div class="yr-wrap yr-hero__grid">
		<div>
			<p class="yr-eyebrow">AI Search Optimization Firm</p>
			<h1 id="yr-hero-h">AI is deciding who your customers call. <em>We make sure it chooses you.</em></h1>
			<p class="yr-lead">Yroc Consulting is a team of strategists, engineers and content specialists helping businesses get recommended by ChatGPT, Gemini, Perplexity and Google AI, backed by more than 15 years of search results.</p>
			<form class="yr-check" data-yr-check action="<?php echo esc_url( home_url( '/ai-visibility-audit/' ) ); ?>" method="get" autocomplete="off">
				<label><span>Your business</span><input name="business" placeholder="Your Business Name" aria-label="Your business name"></label>
				<label><span>City</span><input name="market" placeholder="Dallas, TX" aria-label="City"></label>
				<button class="yr-btn yr-btn--green" type="submit">See what AI says →</button>
			</form>
			<p class="yr-check__note">Instant preview. Your full AI Visibility Audit is prepared by our team.</p>
			<div class="yr-trust">
				<div><b><span class="yr-stars" aria-hidden="true">★★★★★</span> <?php echo esc_html( number_format( (float) $rv['rating'], 1 ) ); ?></b><span><?php echo esc_html( $rv['count'] ); ?> verified client reviews</span></div>
				<?php if ( $big ) : ?><div><b><?php echo esc_html( number_format( $big ) ); ?>+</b><span>client appearances in AI answers</span></div><?php endif; ?>
				<div><b>15+ years</b><span>in search &amp; visibility</span></div>
			</div>
		</div>

		<div class="yr-lv" data-yr-live data-items="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">
			<div class="yr-lv__win">
				<div class="yr-lv__bar">
					<span class="yr-lv__dots" aria-hidden="true"><i></i><i></i><i></i></span>
					<span class="yr-lv__tag"><span class="yr-stat__dot"></span><span data-l-mode>Live client result<?php echo $asof ? ' · week of ' . esc_html( $asof ) : ''; ?></span></span>
				</div>
				<ul class="yr-lv__plats" aria-label="AI platforms">
					<?php foreach ( $plats as $k => $label ) : ?>
						<li data-p="<?php echo esc_attr( $k ); ?>" class="<?php echo $first && in_array( $k, $first['p'], true ) ? 'is-on' : ''; ?>"><span class="yr-lv__pdot"></span><?php echo esc_html( $label ); ?></li>
					<?php endforeach; ?>
				</ul>
				<div class="yr-lv__q"><span class="yr-lv__you">You</span><p data-l-q><?php echo esc_html( $first ? $first['q'] : 'Who is the most trusted roofing company near me?' ); ?></p></div>
				<div class="yr-lv__a" aria-live="polite">
					<span class="yr-lv__ai" aria-hidden="true"></span>
					<div class="yr-lv__body">
						<span class="yr-lv__line" style="width:92%"></span>
						<span class="yr-lv__line" style="width:78%"></span>
						<div class="yr-lv__pick" data-l-pick>
							<span class="yr-lv__rank">1</span>
							<span><b data-l-name><?php echo esc_html( $first ? $first['n'] : 'Your Business' ); ?></b><small data-l-meta><?php echo esc_html( $first ? $first['i'] . ' · ' . $first['c'] : 'Recommended by AI' ); ?></small></span>
							<span class="yr-lv__chip">Recommended</span>
						</div>
						<span class="yr-lv__line" style="width:85%"></span>
						<span class="yr-lv__line yr-lv__line--dim" style="width:64%"></span>
					</div>
				</div>
				<div class="yr-lv__foot">
					<span data-l-foot><?php echo $first ? 'Named by ' . esc_html( implode( ', ', array_map( function ( $k ) use ( $plats ) { return $plats[ $k ] ?? $k; }, $first['p'] ) ) ) : 'Example AI answer'; ?></span>
					<span class="yr-lv__nav" data-l-nav aria-hidden="true"></span>
				</div>
			</div>
			<p class="yr-lv__note">Real prompts from our weekly AI tracking. AI wording varies; the client shown was recommended for this question.</p>
		</div>
	</div>
</section>
<!-- /wp:html -->
