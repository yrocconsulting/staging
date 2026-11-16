<?php
/**
 * AI visibility stats shown on the homepage.
 *
 * Data source today: Settings → AI Stats (manual entry).
 * Data source next:  ProRankTracker AI Tracking API, pulled hourly by WP-Cron.
 *   The API key lives in wp-config.php (never in git):
 *     define( 'YROC_PRT_API_KEY', '...' );
 *   yroc_prt_fetch() is wired in but intentionally returns null until we have
 *   PRT's (gated) API documentation and can map real endpoints/fields.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const YROC_STATS_OPTION = 'yroc_ai_stats';

function yroc_ai_platforms() {
	return array(
		'aio'        => 'AI Overviews',
		'ai_mode'    => 'AI Mode',
		'chatgpt'    => 'ChatGPT',
		'gemini'     => 'Gemini',
		'perplexity' => 'Perplexity',
		'copilot'    => 'Copilot',
	);
}

function yroc_ai_stats_defaults() {
	return array(
		'appearances'   => 0,
		'prompts'       => 0,
		'platforms'     => array_fill_keys( array_keys( yroc_ai_platforms() ), 0 ),
		'leads'         => 10517230,
		'rank'          => 2.2,
		'source'        => 'manual',
		'updated'       => 0,
	);
}

function yroc_ai_stats() {
	$s = get_option( YROC_STATS_OPTION, array() );
	$s = wp_parse_args( is_array( $s ) ? $s : array(), yroc_ai_stats_defaults() );
	$s['platforms'] = wp_parse_args( (array) $s['platforms'], yroc_ai_stats_defaults()['platforms'] );
	return $s;
}

/* ---------- ProRankTracker sync (hourly) ---------- */

add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'yroc_prt_sync' ) ) {
		wp_schedule_event( time() + 60, 'hourly', 'yroc_prt_sync' );
	}
} );

add_action( 'yroc_prt_sync', function () {
	if ( ! defined( 'YROC_PRT_API_KEY' ) || ! YROC_PRT_API_KEY ) {
		return;
	}
	$data = yroc_prt_fetch( YROC_PRT_API_KEY );
	if ( ! is_array( $data ) ) {
		return; // Keep last good numbers on any failure.
	}
	$s = yroc_ai_stats();
	$s = array_merge( $s, $data, array( 'source' => 'proranktracker', 'updated' => time() ) );
	update_option( YROC_STATS_OPTION, $s, false );
} );

/**
 * Fetch AI visibility totals from ProRankTracker.
 * Must return array( 'appearances' => int, 'prompts' => int, 'platforms' => array( key => int ) ) or null.
 * TODO: implement once PRT sends the AI Tracking API docs (endpoints, auth, field names).
 */
function yroc_prt_fetch( $api_key ) {
	return null;
}

/* ---------- Settings → AI Stats ---------- */

add_action( 'admin_menu', function () {
	add_options_page( 'AI Stats', 'AI Stats', 'manage_options', 'yroc-ai-stats', 'yroc_ai_stats_page' );
} );

function yroc_ai_stats_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['yroc_stats_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['yroc_stats_nonce'] ), 'yroc_stats' ) ) {
		$s = yroc_ai_stats();
		foreach ( array( 'appearances', 'prompts', 'leads' ) as $k ) {
			$s[ $k ] = absint( $_POST[ $k ] ?? 0 );
		}
		$s['rank'] = round( (float) ( $_POST['rank'] ?? 0 ), 1 );
		foreach ( yroc_ai_platforms() as $k => $label ) {
			$s['platforms'][ $k ] = absint( $_POST['p'][ $k ] ?? 0 );
		}
		$s['source']  = 'manual';
		$s['updated'] = time();
		update_option( YROC_STATS_OPTION, $s, false );
		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache();
		}
		echo '<div class="notice notice-success"><p>Saved.</p></div>';
	}
	$s   = yroc_ai_stats();
	$prt = defined( 'YROC_PRT_API_KEY' ) && YROC_PRT_API_KEY;
	?>
	<div class="wrap">
		<h1>AI Stats</h1>
		<p>Numbers shown in the homepage stats bar.
			<?php if ( $prt ) : ?>
				A ProRankTracker key is configured; synced values overwrite AI appearances, prompts and platform counts hourly.
			<?php else : ?>
				No ProRankTracker key yet — values below are entered manually.
			<?php endif; ?>
			Last updated: <?php echo $s['updated'] ? esc_html( human_time_diff( $s['updated'] ) . ' ago (' . $s['source'] . ')' ) : 'never'; ?>.
		</p>
		<form method="post">
			<?php wp_nonce_field( 'yroc_stats', 'yroc_stats_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tr><th><label for="appearances">AI answer appearances</label></th><td><input name="appearances" id="appearances" type="number" min="0" value="<?php echo esc_attr( $s['appearances'] ); ?>" class="regular-text"><p class="description">Total times client brands appeared (mentioned or cited) in AI answers.</p></td></tr>
				<tr><th><label for="prompts">AI prompts tracked</label></th><td><input name="prompts" id="prompts" type="number" min="0" value="<?php echo esc_attr( $s['prompts'] ); ?>" class="regular-text"></td></tr>
				<?php foreach ( yroc_ai_platforms() as $k => $label ) : ?>
					<tr><th><label for="p-<?php echo esc_attr( $k ); ?>">&nbsp;&nbsp;↳ <?php echo esc_html( $label ); ?></label></th><td><input name="p[<?php echo esc_attr( $k ); ?>]" id="p-<?php echo esc_attr( $k ); ?>" type="number" min="0" value="<?php echo esc_attr( $s['platforms'][ $k ] ); ?>" class="small-text"></td></tr>
				<?php endforeach; ?>
				<tr><th><label for="leads">Leads generated</label></th><td><input name="leads" id="leads" type="number" min="0" value="<?php echo esc_attr( $s['leads'] ); ?>" class="regular-text"></td></tr>
				<tr><th><label for="rank">Avg. page-one rank</label></th><td><input name="rank" id="rank" type="number" step="0.1" min="0" value="<?php echo esc_attr( $s['rank'] ); ?>" class="small-text"></td></tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
