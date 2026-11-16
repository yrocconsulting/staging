<?php
/**
 * Apply weekly AI visibility stats (JSON from tools/prt_report.py) to the homepage stats.
 * Run with WP-CLI: wp eval-file apply_ai_stats.php /path/to/stats.json
 */
if ( ! defined( 'WP_CLI' ) ) {
	exit( "Run via WP-CLI.\n" );
}
$data = json_decode( (string) file_get_contents( $args[0] ?? '' ), true );
if ( ! is_array( $data ) || empty( $data['appearances'] ) ) {
	WP_CLI::error( 'No usable stats in ' . ( $args[0] ?? '(none)' ) );
}
$s = function_exists( 'yroc_ai_stats' ) ? yroc_ai_stats() : (array) get_option( 'yroc_ai_stats', array() );
foreach ( array( 'appearances', 'mentions', 'links', 'prompts', 'clients', 'visibility' ) as $k ) {
	$s[ $k ] = (int) ( $data[ $k ] ?? 0 );
}
$s['platforms']   = array_map( 'intval', (array) ( $data['platforms'] ?? array() ) );

// Running totals: one entry per report date, so re-running the same report never double-counts.
$history = (array) get_option( 'yroc_ai_history', array() );
$key     = sanitize_text_field( $data['report_date'] ?? gmdate( 'Y-m-d' ) );
$history[ $key ] = array(
	'appearances' => (int) $data['appearances'],
	'mentions'    => (int) ( $data['mentions'] ?? 0 ),
	'links'       => (int) ( $data['links'] ?? 0 ),
	'platforms'   => $s['platforms'],
);
ksort( $history );
update_option( 'yroc_ai_history', $history, false );
$s['total']           = 0;
$s['total_mentions']  = 0;
$s['total_links']     = 0;
$s['platform_totals'] = array();
foreach ( $history as $h ) {
	$h = is_array( $h ) ? $h : array( 'appearances' => (int) $h ); // older single-number entries
	$s['total']          += (int) ( $h['appearances'] ?? 0 );
	$s['total_mentions'] += (int) ( $h['mentions'] ?? 0 );
	$s['total_links']    += (int) ( $h['links'] ?? 0 );
	foreach ( (array) ( $h['platforms'] ?? array() ) as $k => $v ) {
		$s['platform_totals'][ $k ] = ( $s['platform_totals'][ $k ] ?? 0 ) + (int) $v;
	}
}
$s['report_date'] = sanitize_text_field( $data['report_date'] ?? '' );
$s['showcase']    = array();
foreach ( (array) ( $data['showcase'] ?? array() ) as $r ) {
	$s['showcase'][] = array(
		'domain'      => sanitize_text_field( $r['domain'] ),
		'prompt'      => sanitize_text_field( $r['prompt'] ),
		'platforms'   => array_map( 'sanitize_key', (array) $r['platforms'] ),
		'visibility'  => (int) $r['visibility'],
		'appearances' => (int) $r['appearances'],
	);
}
$s['sites'] = array();
foreach ( (array) ( $data['sites'] ?? array() ) as $dom => $v ) {
	$s['sites'][ sanitize_text_field( $dom ) ] = array_map( 'intval', (array) $v );
}
$s['source']      = 'prt-report';
$s['updated']     = time();
update_option( 'yroc_ai_stats', $s, false );
WP_CLI::success( "AI stats updated: {$s['appearances']} this week, {$s['total']} running total across " . count( $history ) . " reports ({$s['report_date']})." );
