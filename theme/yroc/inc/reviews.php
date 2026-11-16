<?php
/**
 * Reviews pulled from Trustindex (https://www.trustindex.io/reviews/yrocconsulting.com).
 *
 * - Refreshed daily by WP-Cron from the public review page's structured data.
 * - Only 5-star reviews are shown.
 * - Reviews that mention staff by name are skipped (site policy: no employee names; the owner, Cory, is fine).
 * - Company-focused reviews are listed first; reviews about the owner's trainings follow.
 * - Falls back to the bundled snapshot in data/reviews.json if Trustindex is unreachable.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const YROC_TI_URL = 'https://www.trustindex.io/reviews/yrocconsulting.com';

/** Words that must not appear in a displayed review (staff names). */
function yroc_review_blocklist() {
	return apply_filters( 'yroc_review_blocklist', array( 'abigail', 'bradley', 'brad', 'everett', 'hayden' ) );
}

function yroc_reviews_parse( $html ) {
	if ( ! preg_match_all( '#<script[^>]*application/ld\+json[^>]*>(.*?)</script>#s', $html, $m ) ) {
		return null;
	}
	$reviews = array();
	$rating  = null;
	$count   = null;
	$walk    = function ( $o ) use ( &$walk, &$reviews ) {
		if ( ! is_array( $o ) ) {
			return;
		}
		if ( isset( $o['@type'] ) && 'Review' === $o['@type'] ) {
			$reviews[] = array(
				'author' => is_array( $o['author'] ?? null ) ? ( $o['author']['name'] ?? '' ) : (string) ( $o['author'] ?? '' ),
				'rating' => (int) round( (float) ( $o['reviewRating']['ratingValue'] ?? 0 ) ),
				'date'   => substr( (string) ( $o['datePublished'] ?? '' ), 0, 10 ),
				'body'   => trim( (string) ( $o['reviewBody'] ?? '' ) ),
			);
		}
		foreach ( $o as $v ) {
			$walk( $v );
		}
	};
	foreach ( $m[1] as $json ) {
		$d = json_decode( $json, true );
		$walk( $d );
		if ( is_array( $d ) && preg_match( '#([\d.]+)/5 from (\d+)#', wp_json_encode( $d ), $a ) ) {
			$rating = (float) $a[1];
			$count  = (int) $a[2];
		}
	}
	return $reviews ? array( 'rating' => $rating, 'count' => $count, 'fetched' => gmdate( 'Y-m-d' ), 'reviews' => $reviews ) : null;
}

add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'yroc_reviews_sync' ) ) {
		wp_schedule_event( time() + 120, 'daily', 'yroc_reviews_sync' );
	}
} );
add_action( 'yroc_reviews_sync', function () {
	$r = wp_remote_get( YROC_TI_URL, array( 'timeout' => 20, 'user-agent' => 'Mozilla/5.0 (compatible; YrocSite/1.0)' ) );
	if ( is_wp_error( $r ) || 200 !== wp_remote_retrieve_response_code( $r ) ) {
		return;
	}
	$data = yroc_reviews_parse( wp_remote_retrieve_body( $r ) );
	if ( $data ) {
		update_option( 'yroc_ti_reviews', $data, false );
	}
} );

/** Returns array( rating, count, items[] ) ready for display. */
function yroc_reviews() {
	$data = get_option( 'yroc_ti_reviews' );
	if ( ! $data ) {
		$data = json_decode( (string) file_get_contents( get_theme_file_path( 'data/reviews.json' ) ), true );
	}
	$block = '/\b(' . implode( '|', array_map( 'preg_quote', yroc_review_blocklist() ) ) . ')\b/i';
	$seen  = array();
	$items = array();
	foreach ( (array) ( $data['reviews'] ?? array() ) as $rv ) {
		$key = strtolower( preg_replace( '/[^a-z]/i', '', strtok( $rv['author'], ' ' ) ) );
		if ( 5 !== (int) $rv['rating'] || '' === $rv['body'] || preg_match( $block, $rv['body'] ) || isset( $seen[ $key ] ) ) {
			continue;
		}
		$seen[ $key ] = true;
		$rv['body']   = preg_replace( '/\s*\x{2014}\s*/u', ', ', $rv['body'] ); // site style: no em dashes
		$items[]      = $rv + array( 'org' => 'Verified review' );
	}
	// Company-focused reviews first, owner-focused (training) reviews after.
	$about_owner = function ( $r ) { return (int) (bool) preg_match( '/\bcor(e)?y\b/i', $r['body'] ); };
	usort( $items, function ( $a, $b ) use ( $about_owner ) { return $about_owner( $a ) <=> $about_owner( $b ); } );
	return array(
		'rating' => $data['rating'] ?? 4.8,
		'count'  => $data['count'] ?? count( $items ),
		'items'  => $items,
	);
}

/**
 * AI Authority Engine training reviews (Trustpilot: trustpilot.com/review/aiauthorityengine.org).
 * Trustpilot blocks automated fetching, so this is a curated snapshot in data/reviews-aae.json.
 * Refresh it when new reviews come in (see tools/README or ask Claude).
 */
function yroc_aae_reviews() {
	$d = json_decode( (string) file_get_contents( get_theme_file_path( 'data/reviews-aae.json' ) ), true );
	return is_array( $d ) ? $d : array( 'reviews' => array(), 'source' => 'https://www.trustpilot.com/review/aiauthorityengine.org' );
}
