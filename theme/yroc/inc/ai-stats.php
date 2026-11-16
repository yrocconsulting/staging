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
		'aio'        => 'Google AI Overviews',
		'ai_mode'    => 'Google AI Mode',
		'chatgpt'    => 'ChatGPT',
		'gemini'     => 'Gemini',
		'perplexity' => 'Perplexity',
		'copilot'    => 'Copilot',
	);
}

/** The four homepage stats (value can include a prefix/suffix, e.g. "$94.5M", "400+"). */
function yroc_stat_slot_defaults() {
	return array(
		array( '6', 'AI platforms tracked for our clients' ),
		array( '400+', 'Companies advised since 2005' ),
		array( '$94.5M', 'Client revenue generated' ),
		array( '15+', 'Years in search & visibility' ),
	);
}

/** Splits "$94.5M" into array( prefix, number, suffix, decimals ) for the count-up animation. */
function yroc_stat_parts( $value ) {
	if ( preg_match( '/^(\D*?)([\d,]+(?:\.(\d+))?)(.*)$/', trim( $value ), $m ) ) {
		return array( $m[1], (float) str_replace( ',', '', $m[2] ), $m[4], isset( $m[3] ) ? strlen( $m[3] ) : 0 );
	}
	return array( '', null, $value, 0 );
}

/** True when the AI numbers came from an automatic source within the last 8 days (weekly report). */
function yroc_ai_stats_live( $s ) {
	return in_array( $s['source'], array( 'proranktracker', 'prt-report' ), true ) && $s['updated'] > time() - 8 * DAY_IN_SECONDS;
}

/** Client display names (theme/yroc/data/clients.json). Only listed domains are ever shown. */
function yroc_clients() {
	static $c = null;
	if ( null === $c ) {
		$c = json_decode( (string) file_get_contents( get_theme_file_path( 'data/clients.json' ) ), true );
		$c = is_array( $c ) ? $c : array();
		unset( $c['_note'] );
	}
	return $c;
}

/** Weekly showcase rows with client names, only for clients in clients.json. */
function yroc_ai_showcase() {
	$s   = yroc_ai_stats();
	$c   = yroc_clients();
	$out = array();
	foreach ( (array) ( $s['showcase'] ?? array() ) as $r ) {
		if ( isset( $c[ $r['domain'] ] ) ) {
			$out[] = $r + $c[ $r['domain'] ];
		}
	}
	return $out;
}

function yroc_ai_stats_defaults() {
	return array(
		'appearances'   => 0,
		'total'           => 0,
		'total_mentions'  => 0,
		'total_links'     => 0,
		'platform_totals' => array(),
		'prompts'       => 0,
		'clients'       => 0,
		'mentions'      => 0,
		'links'         => 0,
		'visibility'    => 0,
		'report_date'   => '',
		'platforms'     => array_fill_keys( array_keys( yroc_ai_platforms() ), 0 ),
		'slots'         => yroc_stat_slot_defaults(),
		'source'        => 'manual',
		'updated'       => 0,
	);
}

function yroc_ai_stats() {
	$s = get_option( YROC_STATS_OPTION, array() );
	$s = wp_parse_args( is_array( $s ) ? $s : array(), yroc_ai_stats_defaults() );
	$s['platforms'] = wp_parse_args( (array) $s['platforms'], yroc_ai_stats_defaults()['platforms'] );
	$slots = array_values( (array) $s['slots'] );
	if ( count( $slots ) !== 4 ) {
		$slots = yroc_stat_slot_defaults();
	}
	$s['slots'] = $slots;
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
 * Low-level ProRankTracker API v3 request (auth: X-TOKEN header).
 * Returns decoded JSON array, or WP_Error.
 */
function yroc_prt_request( $path, $args = array() ) {
	$url = 'https://api.proranktracker.com/v3/' . ltrim( $path, '/' );
	if ( $args ) {
		$url = add_query_arg( $args, $url );
	}
	$r = wp_remote_get( $url, array(
		'timeout' => 20,
		'headers' => array( 'X-TOKEN' => YROC_PRT_API_KEY, 'Accept' => 'application/json' ),
	) );
	if ( is_wp_error( $r ) ) {
		return $r;
	}
	$data = json_decode( wp_remote_retrieve_body( $r ), true );
	if ( 200 !== wp_remote_retrieve_response_code( $r ) || ! is_array( $data ) || ( isset( $data['success'] ) && ! $data['success'] ) ) {
		return new WP_Error( 'prt_error', 'ProRankTracker API error: ' . wp_remote_retrieve_body( $r ) );
	}
	return $data;
}

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
	add_options_page( 'Yroc Site Settings', 'Yroc Site', 'manage_options', 'yroc-ai-stats', 'yroc_ai_stats_page' );
} );

function yroc_ai_stats_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['yroc_stats_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['yroc_stats_nonce'] ), 'yroc_stats' ) ) {
		$s = yroc_ai_stats();
		$before = array( $s['appearances'], $s['prompts'], $s['platforms'] );
		foreach ( array( 'appearances', 'prompts' ) as $k ) {
			$s[ $k ] = absint( $_POST[ $k ] ?? 0 );
		}
		$slots = array();
		for ( $n = 0; $n < 4; $n++ ) {
			$slots[] = array(
				sanitize_text_field( wp_unslash( $_POST['slot_value'][ $n ] ?? '' ) ),
				sanitize_text_field( wp_unslash( $_POST['slot_label'][ $n ] ?? '' ) ),
			);
		}
		$s['slots'] = $slots;
		foreach ( yroc_ai_platforms() as $k => $label ) {
			$s['platforms'][ $k ] = absint( $_POST['p'][ $k ] ?? 0 );
		}
		update_option( 'yroc_hero_image_id', absint( $_POST['hero_image_id'] ?? 0 ) );
		if ( array( $s['appearances'], $s['prompts'], $s['platforms'] ) !== $before ) {
			$s['source']  = 'manual'; // AI numbers were edited by hand
			$s['updated'] = time();
		}
		update_option( YROC_STATS_OPTION, $s, false );
		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache();
		}
		echo '<div class="notice notice-success"><p>Saved.</p></div>';
	}
	$s   = yroc_ai_stats();
	$prt = defined( 'YROC_PRT_API_KEY' ) && YROC_PRT_API_KEY;
	$hid = (int) get_option( 'yroc_hero_image_id', 0 );
	wp_enqueue_media();
	?>
	<div class="wrap">
		<h1>Yroc Site Settings</h1>
		<form method="post">
		<h2>Homepage hero image</h2>
		<p>Shown on the right side of the homepage hero (e.g. a photo of the building). Leave empty to show the designed HQ map card.</p>
		<p><img id="yroc-hero-prev" src="<?php echo esc_url( $hid ? wp_get_attachment_image_url( $hid, 'medium' ) : '' ); ?>" style="max-width:320px;border-radius:12px;<?php echo $hid ? '' : 'display:none'; ?>"></p>
		<input type="hidden" name="hero_image_id" id="yroc-hero-id" value="<?php echo esc_attr( $hid ); ?>">
		<button type="button" class="button" id="yroc-hero-pick">Choose image</button>
		<button type="button" class="button-link-delete" id="yroc-hero-clear" style="margin-left:8px">Remove</button>
		<script>
		jQuery(function ($) {
			var frame;
			$('#yroc-hero-pick').on('click', function () {
				frame = frame || wp.media({ title: 'Homepage hero image', library: { type: 'image' }, multiple: false });
				frame.off('select').on('select', function () { var a = frame.state().get('selection').first().toJSON(); $('#yroc-hero-id').val(a.id); $('#yroc-hero-prev').attr('src', (a.sizes && a.sizes.medium ? a.sizes.medium.url : a.url)).show(); });
				frame.open();
			});
			$('#yroc-hero-clear').on('click', function () { $('#yroc-hero-id').val(0); $('#yroc-hero-prev').hide(); });
		});
		</script>
		<hr>
		<h2>Homepage stats</h2>
		<p>Numbers shown in the homepage stats bar.
			<?php if ( $prt ) : ?>
				A ProRankTracker key is configured; synced values overwrite AI appearances, prompts and platform counts hourly.
			<?php elseif ( 'prt-report' === $s['source'] ) : ?>
				AI numbers update automatically every Monday from the ProRankTracker weekly report (report date <?php echo esc_html( $s['report_date'] ); ?>). Editing them here overrides that until the next report.
			<?php else : ?>
				No ProRankTracker key yet. Values below are entered manually.
			<?php endif; ?>
			Last updated: <?php echo $s['updated'] ? esc_html( human_time_diff( $s['updated'] ) . ' ago (' . $s['source'] . ')' ) : 'never'; ?>.
		</p>
			<?php wp_nonce_field( 'yroc_stats', 'yroc_stats_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tr><th><label for="appearances">AI answer appearances</label></th><td><input name="appearances" id="appearances" type="number" min="0" value="<?php echo esc_attr( $s['appearances'] ); ?>" class="regular-text"><p class="description">Total times client brands appeared (mentioned or cited) in AI answers.</p></td></tr>
				<tr><th><label for="prompts">AI prompts tracked</label></th><td><input name="prompts" id="prompts" type="number" min="0" value="<?php echo esc_attr( $s['prompts'] ); ?>" class="regular-text"></td></tr>
				<?php foreach ( yroc_ai_platforms() as $k => $label ) : ?>
					<tr><th><label for="p-<?php echo esc_attr( $k ); ?>">&nbsp;&nbsp;↳ <?php echo esc_html( $label ); ?></label></th><td><input name="p[<?php echo esc_attr( $k ); ?>]" id="p-<?php echo esc_attr( $k ); ?>" type="number" min="0" value="<?php echo esc_attr( $s['platforms'][ $k ] ); ?>" class="small-text"></td></tr>
				<?php endforeach; ?>
				<tr><th colspan="2"><h2 style="margin:1rem 0 0">Stats bar</h2><p style="font-weight:400">Four numbers under the homepage hero. Values can include symbols, e.g. <code>$94.5M</code>, <code>400+</code>, <code>6</code>. When ProRankTracker is connected, the first slot is replaced by live AI answer appearances.</p></th></tr>
				<?php foreach ( $s['slots'] as $n => $slot ) : ?>
					<tr><th>Stat <?php echo (int) $n + 1; ?></th><td><input name="slot_value[<?php echo (int) $n; ?>]" value="<?php echo esc_attr( $slot[0] ); ?>" class="small-text" style="width:8em" aria-label="Value"> <input name="slot_label[<?php echo (int) $n; ?>]" value="<?php echo esc_attr( $slot[1] ); ?>" class="regular-text" aria-label="Label"></td></tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
