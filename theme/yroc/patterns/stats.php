<?php
/**
 * Title: Live AI Stats Bar
 * Slug: yroc/stats
 * Categories: yroc
 */
$s     = yroc_ai_stats();
$plats = array_filter( $s['platforms'] );
$live  = 'proranktracker' === $s['source'] && $s['updated'] > time() - DAY_IN_SECONDS;
?>
<!-- wp:html -->
<section class="yr-stats" aria-label="Results">
  <div class="yr-wrap"><div class="yr-stats__grid<?php echo $s['appearances'] ? ' has-ai' : ''; ?>">
    <?php if ( ! $s['appearances'] ) : ?>
    <div class="yr-stat" data-reveal><p class="yr-stat__n">$<span data-count="94550000">94,550,000</span></p><p class="yr-stat__l">Generated in client revenue</p></div>
    <?php else : ?>
    <div class="yr-stat yr-stat--ai" data-reveal>
      <p class="yr-stat__live"><span class="yr-stat__dot"></span><?php echo $live ? 'Live · updated ' . esc_html( human_time_diff( $s['updated'] ) ) . ' ago' : 'Tracked across AI platforms'; ?></p>
      <p class="yr-stat__n"><span data-count="<?php echo esc_attr( $s['appearances'] ); ?>"><?php echo esc_html( number_format( $s['appearances'] ) ); ?></span></p>
      <p class="yr-stat__l">Client appearances in AI answers</p>
      <?php if ( $plats && $s['appearances'] ) : ?>
        <ul class="yr-stat__plats">
          <?php foreach ( $plats as $k => $n ) : ?>
            <li><?php echo esc_html( yroc_ai_platforms()[ $k ] ); ?> <b><?php echo esc_html( number_format( $n ) ); ?></b></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if ( $s['prompts'] ) : ?>
    <div class="yr-stat" data-reveal><p class="yr-stat__n"><span data-count="<?php echo esc_attr( $s['prompts'] ); ?>"><?php echo esc_html( number_format( $s['prompts'] ) ); ?></span></p><p class="yr-stat__l">AI prompts monitored daily</p></div>
    <?php else : ?>
    <div class="yr-stat" data-reveal><p class="yr-stat__n"><span data-count="15">15</span>+</p><p class="yr-stat__l">Years growing businesses online</p></div>
    <?php endif; ?>
    <div class="yr-stat" data-reveal><p class="yr-stat__n"><span data-count="<?php echo esc_attr( $s['leads'] ); ?>"><?php echo esc_html( number_format( $s['leads'] ) ); ?></span></p><p class="yr-stat__l">Leads generated for our clients</p></div>
    <div class="yr-stat" data-reveal><p class="yr-stat__n">#<span data-count="<?php echo esc_attr( $s['rank'] ); ?>" data-decimals="1"><?php echo esc_html( number_format( $s['rank'], 1 ) ); ?></span></p><p class="yr-stat__l">Average client page-one rank</p></div>
  </div></div>
</section>
<!-- /wp:html -->
