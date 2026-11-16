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

// Running total: one entry per report date, so re-running the same report never double-counts.
$history = (array) get_option( 'yroc_ai_history', array() );
$key     = sanitize_text_field( $data['report_date'] ?? gmdate( 'Y-m-d' ) );
$history[ $key ] = (int) $data['appearances'];
ksort( $history );
update_option( 'yroc_ai_history', $history, false );
$s['total'] = array_sum( $history );
$s['report_date'] = sanitize_text_field( $data['report_date'] ?? '' );
$s['source']      = 'prt-report';
$s['updated']     = time();
update_option( 'yroc_ai_stats', $s, false );
WP_CLI::success( "AI stats updated: {$s['appearances']} this week, {$s['total']} running total across " . count( $history ) . " reports ({$s['report_date']})." );
