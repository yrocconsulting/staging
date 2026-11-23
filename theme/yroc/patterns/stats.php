<?php
/**
 * Title: Stats Bar (live AI visibility by platform, editable fallback)
 * Slug: yroc/stats
 * Categories: yroc
 */
$s     = yroc_ai_stats();
$live  = yroc_ai_stats_live( $s );
$asof  = $s['report_date'] ? date_i18n( 'M j', strtotime( $s['report_date'] ) ) : '';
$ai    = $s['appearances'] > 0;
$names = yroc_ai_platforms();

// Platforms that have appeared at least once, biggest first.
$plats = array();
if ( $ai ) {
	foreach ( $names as $k => $label ) {
		$total = (int) ( $s['platform_totals'][ $k ] ?? 0 );
		$week  = (int) ( $s['platforms'][ $k ] ?? 0 );
		if ( $total || $week ) {
			$plats[ $k ] = array( $label, max( $total, $week ), $week );
		}
	}
	uasort( $plats, function ( $a, $b ) { return $b[1] <=> $a[1]; } );
}
$big   = max( (int) $s['total'], (int) $s['appearances'] );
$label = $live ? 'Live · updated ' . ( $asof ? $asof : human_time_diff( $s['updated'] ) . ' ago' ) : 'Tracked weekly';
?>
<!-- wp:html -->
<section class="yr-stats" aria-label="<?php echo $ai ? 'Client visibility in AI answers' : 'Yroc Consulting by the numbers'; ?>">
  <div class="yr-wrap">
  <?php if ( $ai ) : ?>
    <div class="yr-aistats" style="--n: <?php echo (int) count( $plats ) + 1; ?>">
      <div class="yr-aistat yr-aistat--total" data-reveal>
        <p class="yr-stat__live"><span class="yr-stat__dot"></span><?php echo esc_html( $label ); ?></p>
        <p class="yr-stat__n"><span data-count="<?php echo esc_attr( $big ); ?>"><?php echo esc_html( number_format( $big ) ); ?></span>+</p>
        <p class="yr-stat__l">Client appearances in AI answers, and counting</p>
        <p class="yr-aistat__split">
          <?php if ( $s['total_mentions'] || $s['total_links'] ) : ?>
            <span><b><?php echo esc_html( number_format( max( $s['total_mentions'], $s['mentions'] ) ) ); ?></b> mentions</span><span><b><?php echo esc_html( number_format( max( $s['total_links'], $s['links'] ) ) ); ?></b> citations</span>
          <?php endif; ?>
          <span><b>4</b> AI platforms tracked</span>
        </p>
      </div>
      <?php foreach ( $plats as $k => $pl ) : ?>
        <div class="yr-aistat yr-aistat--<?php echo esc_attr( $k ); ?>" data-reveal>
          <p class="yr-stat__live"><span class="yr-aistat__logo" aria-hidden="true"></span><?php echo esc_html( $pl[0] ); ?></p>
          <p class="yr-stat__n"><span data-count="<?php echo esc_attr( $pl[1] ); ?>"><?php echo esc_html( number_format( $pl[1] ) ); ?></span>+</p>
          <p class="yr-stat__l">Client appearances in <?php echo esc_html( $pl[0] ); ?> answers</p>
          <p class="yr-aistat__wk"><?php echo esc_html( number_format( $pl[2] ) ); ?> this week</p>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else : ?>
    <div class="yr-stats__grid">
      <?php foreach ( $s['slots'] as $slot ) : list( $pre, $num, $suf, $dec ) = yroc_stat_parts( $slot[0] ); ?>
        <div class="yr-stat" data-reveal>
          <p class="yr-stat__n"><?php if ( null === $num ) : ?><?php echo esc_html( $slot[0] ); ?><?php else : ?><?php echo esc_html( $pre ); ?><span data-count="<?php echo esc_attr( $num ); ?>" data-decimals="<?php echo (int) $dec; ?>"><?php echo esc_html( number_format( $num, $dec ) ); ?></span><?php echo esc_html( $suf ); ?><?php endif; ?></p>
          <p class="yr-stat__l"><?php echo esc_html( $slot[1] ); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  </div>
</section>
<!-- /wp:html -->
