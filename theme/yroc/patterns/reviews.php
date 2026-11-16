<?php
/**
 * Title: Client Reviews (Trustindex wall)
 * Slug: yroc/reviews
 * Categories: yroc
 */
$rv    = yroc_reviews();
$links = yroc_info()['reviews'];
$items = $rv['items'];
$half  = (int) ceil( count( $items ) / 2 );
$rows  = array( array_slice( $items, 0, $half ), array_slice( $items, $half ) );
$card  = function ( $r, $hidden = false ) {
	$initial = strtoupper( substr( $r['author'], 0, 1 ) );
	?>
	<figure class="yr-rcard"<?php echo $hidden ? ' aria-hidden="true"' : ''; ?>>
		<span class="yr-stars" aria-label="5 out of 5 stars">★★★★★</span>
		<blockquote><?php echo esc_html( $r['body'] ); ?></blockquote>
		<figcaption><span class="yr-rcard__av"><?php echo esc_html( $initial ); ?></span><span><b><?php echo esc_html( $r['author'] ); ?></b><?php echo esc_html( $r['org'] ); ?></span></figcaption>
	</figure>
	<?php
};
?>
<!-- wp:html -->
<section class="yr-section yr-dark yr-reviews" id="reviews">
	<div class="yr-wrap">
		<div class="yr-sechead">
			<div><p class="yr-eyebrow">Client reviews</p><h2>Don't take <em>our word</em> for it.</h2></div>
			<div class="yr-reviews__agg">
				<b><?php echo esc_html( number_format( (float) $rv['rating'], 1 ) ); ?></b>
				<span><span class="yr-stars" aria-hidden="true">★★★★★</span><br><?php echo esc_html( $rv['count'] ); ?> verified reviews across Google, Trustindex, Yelp &amp; Facebook</span>
			</div>
		</div>
	</div>
	<div class="yr-rwall" aria-label="Client reviews">
		<?php foreach ( $rows as $n => $row ) : if ( ! $row ) { continue; } ?>
			<div class="yr-rrow<?php echo $n ? ' yr-rrow--rev' : ''; ?>">
				<div class="yr-rtrack">
					<?php foreach ( $row as $r ) { $card( $r ); } ?>
					<?php foreach ( $row as $r ) { $card( $r, true ); } ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="yr-wrap">
		<div class="yr-platforms">
			<a href="<?php echo esc_url( YROC_TI_URL ); ?>" target="_blank" rel="noopener">Read all reviews on Trustindex <span aria-hidden="true">★★★★★</span></a>
			<a href="<?php echo esc_url( $links['google'] ); ?>" target="_blank" rel="noopener">Google <span aria-hidden="true">★★★★★</span></a>
			<a href="<?php echo esc_url( $links['clutch'] ); ?>" target="_blank" rel="noopener">Clutch <span aria-hidden="true">★★★★★</span></a>
		</div>
	</div>
</section>
<!-- /wp:html -->
