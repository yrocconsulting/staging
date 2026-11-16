<?php
/**
 * Title: Stats Bar (editable, live AI appearances when connected)
 * Slug: yroc/stats
 * Categories: yroc
 */
$s     = yroc_ai_stats();
$plats = array_filter( $s['platforms'] );
$live  = yroc_ai_stats_live( $s );
$asof  = $s['report_date'] ? date_i18n( 'M j', strtotime( $s['report_date'] ) ) : '';
$ai    = $s['appearances'] > 0;
$slots = $ai ? array_slice( $s['slots'], 1 ) : $s['slots'];
?>
<!-- wp:html -->
<section class="yr-stats" aria-label="Yroc Consulting by the numbers">
  <div class="yr-wrap"><div class="yr-stats__grid<?php echo $ai ? ' has-ai' : ''; ?>">
    <?php if ( $ai ) : ?>
      <div class="yr-stat yr-stat--ai" data-reveal>
        <p class="yr-stat__live"><span class="yr-stat__dot"></span><?php echo $live ? 'Live tracking · updated ' . esc_html( $asof ? $asof : human_time_diff( $s['updated'] ) . ' ago' ) : 'Tracked across AI platforms'; ?></p>
        <p class="yr-stat__n"><span data-count="<?php echo esc_attr( $s['appearances'] ); ?>"><?php echo esc_html( number_format( $s['appearances'] ) ); ?></span></p>
        <p class="yr-stat__l">Client appearances in AI answers this week<?php if ( $s['prompts'] ) : ?>, across <?php echo esc_html( $s['prompts'] ); ?> tracked prompts<?php endif; ?></p>
        <?php if ( $plats ) : ?>
          <ul class="yr-stat__plats">
            <?php foreach ( $plats as $k => $n ) : ?>
              <li><?php echo esc_html( yroc_ai_platforms()[ $k ] ); ?> <b><?php echo esc_html( number_format( $n ) ); ?></b></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <?php foreach ( $slots as $slot ) : list( $pre, $num, $suf, $dec ) = yroc_stat_parts( $slot[0] ); ?>
      <div class="yr-stat" data-reveal>
        <p class="yr-stat__n"><?php if ( null === $num ) : ?><?php echo esc_html( $slot[0] ); ?><?php else : ?><?php echo esc_html( $pre ); ?><span data-count="<?php echo esc_attr( $num ); ?>" data-decimals="<?php echo (int) $dec; ?>"><?php echo esc_html( number_format( $num, $dec ) ); ?></span><?php echo esc_html( $suf ); ?><?php endif; ?></p>
        <p class="yr-stat__l"><?php echo esc_html( $slot[1] ); ?></p>
      </div>
    <?php endforeach; ?>
  </div></div>
</section>
<!-- /wp:html -->
