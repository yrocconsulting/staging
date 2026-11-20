<?php
/**
 * The Yroc Quarterly: an interactive, page-turning magazine sent to clients each quarter.
 *
 * Each issue is one JSON file in theme/yroc/data/quarterly/<slug>.json. Pages:
 *   [yroc_quarterly issue="2026-q4"]   one issue (flipbook on desktop, stacked pages on phones)
 *   [yroc_quarterly_list]              the archive of issues
 * Copy rules: no em dashes, no employee names except the owner, no client counts, white-label
 * clients never named. Stats need a source and URL.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yroc_quarterly_issues() {
	static $issues = null;
	if ( null !== $issues ) {
		return $issues;
	}
	$issues = array();
	foreach ( glob( get_theme_file_path( 'data/quarterly/*.json' ) ) ?: array() as $file ) {
		$d = json_decode( (string) file_get_contents( $file ), true );
		if ( is_array( $d ) && ! empty( $d['slug'] ) ) {
			$issues[ $d['slug'] ] = $d;
		}
	}
	krsort( $issues );
	return $issues;
}

function yroc_quarterly_issue( $slug ) {
	$all = yroc_quarterly_issues();
	return $all[ $slug ] ?? null;
}

/** Loads the magazine's CSS and JS only on pages that use it. */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_singular() ) {
		return;
	}
	$post = get_post();
	if ( ! $post || ( ! has_shortcode( $post->post_content, 'yroc_quarterly' ) && ! has_shortcode( $post->post_content, 'yroc_quarterly_list' ) ) ) {
		return;
	}
	$dir = get_theme_file_path( 'assets' );
	wp_enqueue_style( 'yroc-quarterly', get_theme_file_uri( 'assets/css/quarterly.css' ), array( 'yroc-main' ), YROC_VERSION . '.' . filemtime( $dir . '/css/quarterly.css' ) );
	if ( has_shortcode( $post->post_content, 'yroc_quarterly' ) ) {
		wp_enqueue_script( 'page-flip', get_theme_file_uri( 'assets/js/vendor/page-flip.browser.js' ), array(), '2.0.7', array( 'strategy' => 'defer', 'in_footer' => true ) );
		wp_enqueue_script( 'yroc-quarterly', get_theme_file_uri( 'assets/js/quarterly.js' ), array( 'page-flip' ), YROC_VERSION . '.' . filemtime( $dir . '/js/quarterly.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	}
} );

/** Allowed inline markup in copy: bold and links. */
function yroc_q_text( $s ) {
	return wp_kses( (string) $s, array( 'b' => array(), 'strong' => array(), 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ), 'br' => array() ) );
}

function yroc_q_link( $url, $label, $class = '' ) {
	$ext = yroc_is_external( $url );
	return sprintf(
		'<a class="%s" href="%s"%s>%s</a>',
		esc_attr( $class ),
		esc_url( $url ),
		$ext ? ' target="_blank" rel="noopener"' : '',
		esc_html( $label )
	);
}

/** One magazine page. $n is the printed page number (0 = no folio). */
function yroc_q_page( $issue, $n, $class, $inner, $attrs = '' ) {
	$folio = $n ? sprintf(
		'<footer class="yq-folio"><span>%s</span><span>%s</span><span>%d</span></footer>',
		esc_html( $issue['title'] ),
		esc_html( $issue['issue'] ),
		$n
	) : '';
	return sprintf( '<section class="yq-page %s" %s><div class="yq-in">%s</div>%s</section>', esc_attr( $class ), $attrs, $inner, $folio );
}

function yroc_q_kicker( $s ) {
	return '<p class="yq-kicker">' . esc_html( $s ) . '</p>';
}

add_shortcode( 'yroc_quarterly', function ( $atts ) {
	$atts  = shortcode_atts( array( 'issue' => '' ), $atts );
	$issue = yroc_quarterly_issue( $atts['issue'] );
	if ( ! $issue ) {
		return current_user_can( 'edit_posts' ) ? '<p>Quarterly issue not found: ' . esc_html( $atts['issue'] ) . '</p>' : '';
	}
	$info  = yroc_info();
	$pages = array();
	$toc   = array();
	$n     = 0;

	// 1. Cover.
	$c       = $issue['cover'];
	$lines   = '';
	foreach ( $c['lines'] as $l ) {
		$lines .= '<li>' . yroc_q_text( $l ) . '</li>';
	}
	$pages[] = yroc_q_page(
		$issue,
		0,
		'yq-cover yq-dark',
		'<div class="yq-mast"><span><span class="yq-mast-name">' . esc_html( $issue['title'] ) . '</span><span class="yq-mast-by">by Yroc Consulting</span></span><span class="yq-mast-issue">' . esc_html( $issue['issue'] ) . '</span></div>'
		. '<div class="yq-orbit" aria-hidden="true"><i></i><i></i><i></i><b></b></div>'
		. '<p class="yq-kicker">' . esc_html( $c['kicker'] ) . '</p>'
		. '<h1 class="yq-cover-h">' . yroc_q_text( $c['headline'] ) . '</h1>'
		. '<p class="yq-cover-dek">' . yroc_q_text( $c['dek'] ) . '</p>'
		. '<ul class="yq-cover-lines">' . $lines . '</ul>'
		. '<p class="yq-cover-foot">' . esc_html( $issue['date'] ) . ' · yrocconsulting.com</p>',
		'data-density="hard"'
	);

	// The rest of the pages are built first so the contents page can list their numbers.
	$body = array();

	// Letter from the owner.
	$l     = $issue['letter'];
	$paras = '';
	foreach ( $l['body'] as $p ) {
		$paras .= '<p>' . yroc_q_text( $p ) . '</p>';
	}
	$body[] = array( 'From the founder', 'yq-letter', yroc_q_kicker( 'From the founder' ) . '<h2>' . yroc_q_text( $l['title'] ) . '</h2><div class="yq-prose">' . $paras . '</div><p class="yq-sign"><b>' . esc_html( $l['sign'] ) . '</b><span>' . esc_html( $l['role'] ) . '</span></p>' );

	// The quarter in AI search: news, split across pages.
	// Two stories under the big headline on the first page, three per page after that.
	$news = array_merge( array( array_slice( $issue['news'], 0, 2 ) ), array_chunk( array_slice( $issue['news'], 2 ), 3 ) );
	foreach ( $news as $i => $chunk ) {
		$items = '';
		foreach ( $chunk as $item ) {
			$items .= '<article class="yq-news">'
				. '<p class="yq-tag">' . esc_html( $item['tag'] ) . '</p>'
				. '<h3>' . yroc_q_text( $item['headline'] ) . '</h3>'
				. '<p>' . yroc_q_text( $item['what'] ) . '</p>'
				. '<div class="yq-why"><button type="button" class="yq-why-btn" aria-expanded="false">Why it matters for your business</button><p hidden>' . yroc_q_text( $item['why'] ) . '</p></div>'
				. '<p class="yq-src">Source: ' . yroc_q_link( $item['url'], $item['source'] ) . ( ! empty( $item['date'] ) ? ', ' . esc_html( $item['date'] ) : '' ) . '</p>'
				. '</article>';
		}
		$head   = 0 === $i ? yroc_q_kicker( 'The quarter in AI search' ) . '<h2>' . yroc_q_text( $issue['news_title'] ) . '</h2>' : yroc_q_kicker( 'The quarter in AI search, continued' );
		$body[] = array( 0 === $i ? 'The quarter in AI search' : '', 'yq-newspage', $head . '<div class="yq-newslist">' . $items . '</div>' );
	}

	// By the numbers.
	$stats = '';
	foreach ( $issue['numbers'] as $s ) {
		list( $pre, $num, $suf, $dec ) = yroc_stat_parts( $s['value'] );
		$val    = null === $num ? esc_html( $s['value'] ) : '<span class="yq-count" data-to="' . esc_attr( $num ) . '" data-dec="' . esc_attr( $dec ) . '" data-pre="' . esc_attr( $pre ) . '" data-suf="' . esc_attr( $suf ) . '">' . esc_html( $s['value'] ) . '</span>';
		$stats .= '<div class="yq-stat"><p class="yq-stat-n">' . $val . '</p><p class="yq-stat-l">' . yroc_q_text( $s['label'] ) . '</p><p class="yq-src">' . yroc_q_link( $s['url'], $s['source'] ) . '</p></div>';
	}
	$body[] = array( 'By the numbers', 'yq-numbers yq-dark', yroc_q_kicker( 'By the numbers' ) . '<h2>' . yroc_q_text( $issue['numbers_title'] ) . '</h2><div class="yq-stats">' . $stats . '</div>' );

	// What we're seeing: Yroc's own tracking (totals only).
	$w     = $issue['seeing'];
	$facts = '';
	foreach ( $w['facts'] as $f ) {
		list( $pre, $num, $suf, $dec ) = yroc_stat_parts( $f['value'] );
		$facts .= '<div class="yq-fact"><p class="yq-fact-n"><span class="yq-count" data-to="' . esc_attr( $num ) . '" data-dec="' . esc_attr( $dec ) . '" data-pre="' . esc_attr( $pre ) . '" data-suf="' . esc_attr( $suf ) . '">' . esc_html( $f['value'] ) . '</span></p><p>' . yroc_q_text( $f['label'] ) . '</p></div>';
	}
	$bars  = '';
	$total = max( 1, array_sum( $w['platforms'] ) );
	$names = yroc_ai_platforms();
	arsort( $w['platforms'] );
	foreach ( $w['platforms'] as $k => $v ) {
		$pct   = round( 100 * $v / $total );
		$bars .= '<div class="yq-bar"><span class="yq-bar-l">' . esc_html( $names[ $k ] ?? $k ) . '</span><span class="yq-bar-t"><i style="--w:' . esc_attr( $pct ) . '%"></i></span><span class="yq-bar-n">' . esc_html( $pct ) . '%</span></div>';
	}
	$body[] = array(
		"What we're seeing",
		'yq-seeing',
		yroc_q_kicker( "What we're seeing" ) . '<h2>' . yroc_q_text( $w['title'] ) . '</h2><p class="yq-dek">' . yroc_q_text( $w['dek'] ) . '</p>'
		. '<div class="yq-facts">' . $facts . '</div>'
		. '<h3 class="yq-sub">Share of AI appearances by platform</h3><div class="yq-bars">' . $bars . '</div>'
		. '<p class="yq-take"><b>The takeaway:</b> ' . yroc_q_text( $w['takeaway'] ) . '</p>'
		. '<p class="yq-src">' . esc_html( $w['source'] ) . '</p>',
	);

	// Client spotlight (only a client who approved it; never white label).
	$s = $issue['spotlight'] ?? null;
	if ( $s ) {
		$inner = yroc_q_kicker( 'Client spotlight' );
		if ( ! empty( $s['placeholder'] ) ) {
			$inner .= '<h2>' . esc_html( $s['title'] ) . '</h2><div class="yq-placeholder"><p>' . yroc_q_text( $s['note'] ) . '</p></div>';
		} else {
			$sf = '';
			foreach ( $s['stats'] ?? array() as $st ) {
				$sf .= '<div class="yq-fact"><p class="yq-fact-n">' . esc_html( $st['value'] ) . '</p><p>' . yroc_q_text( $st['label'] ) . '</p></div>';
			}
			$inner .= '<h2>' . yroc_q_text( $s['title'] ) . '</h2><p class="yq-dek">' . yroc_q_text( $s['dek'] ) . '</p><div class="yq-facts">' . $sf . '</div>'
				. ( ! empty( $s['quote'] ) ? '<blockquote class="yq-quote"><p>&ldquo;' . yroc_q_text( $s['quote'] ) . '&rdquo;</p><cite>' . esc_html( $s['quote_by'] ) . '</cite></blockquote>' : '' )
				. ( ! empty( $s['url'] ) ? yroc_q_link( $s['url'], 'Read the full case study', 'yq-btn' ) : '' );
		}
		$body[] = array( 'Client spotlight', 'yq-spotlight', $inner );
	}

	// The playbook: tap to tick off (remembered on this device).
	$steps = '';
	foreach ( $issue['playbook'] as $i => $p ) {
		$steps .= '<li><button type="button" class="yq-step" aria-pressed="false" data-step="' . esc_attr( $issue['slug'] . '-' . $i ) . '"><span class="yq-step-box" aria-hidden="true"></span><span><b>' . yroc_q_text( $p['title'] ) . '</b> ' . yroc_q_text( $p['body'] ) . '</span></button></li>';
	}
	$body[] = array( 'The playbook', 'yq-playbook', yroc_q_kicker( 'The playbook' ) . '<h2>' . yroc_q_text( $issue['playbook_title'] ) . '</h2><ol class="yq-steps">' . $steps . '</ol><p class="yq-progress">Tap each one as you do it: <span class="yq-progress-n">0</span> of ' . count( $issue['playbook'] ) . ' done</p>' );

	// The quiz.
	$qs = '';
	foreach ( $issue['quiz']['questions'] as $i => $q ) {
		$opts = '';
		foreach ( $q['answers'] as $a ) {
			$opts .= '<button type="button" class="yq-ans" data-q="' . esc_attr( $i ) . '" data-points="' . esc_attr( $a[1] ) . '">' . esc_html( $a[0] ) . '</button>';
		}
		$qs .= '<div class="yq-q" data-q="' . esc_attr( $i ) . '"><p class="yq-q-t"><span>' . ( $i + 1 ) . '</span> ' . esc_html( $q['q'] ) . '</p><div class="yq-ans-row">' . $opts . '</div></div>';
	}
	$results = wp_json_encode( $issue['quiz']['results'] );
	$body[]  = array(
		'The 60-second AI visibility check',
		'yq-quiz',
		yroc_q_kicker( 'Interactive' ) . '<h2>' . yroc_q_text( $issue['quiz']['title'] ) . '</h2><p class="yq-dek">' . yroc_q_text( $issue['quiz']['dek'] ) . '</p>'
		. '<div class="yq-qs" data-results="' . esc_attr( $results ) . '" data-max="' . esc_attr( count( $issue['quiz']['questions'] ) * 2 ) . '">' . $qs . '</div>'
		. '<div class="yq-result" hidden><p class="yq-result-score"></p><p class="yq-result-t"></p><p class="yq-result-p"></p>' . yroc_q_link( $issue['quiz']['cta_url'], $issue['quiz']['cta'], 'yq-btn' ) . '<button type="button" class="yq-retake">Take it again</button></div>',
	);

	// On the cutting edge.
	$edge = '';
	foreach ( $issue['edge'] as $e ) {
		$edge .= '<div class="yq-edge"><h3>' . yroc_q_text( $e['title'] ) . '</h3><p>' . yroc_q_text( $e['body'] ) . '</p>' . ( ! empty( $e['url'] ) ? yroc_q_link( $e['url'], $e['label'] ?? 'Learn more', 'yq-more' ) : '' ) . '</div>';
	}
	$body[] = array( 'On the cutting edge', 'yq-edgepage yq-dark', yroc_q_kicker( 'On the cutting edge' ) . '<h2>' . yroc_q_text( $issue['edge_title'] ) . '</h2><div class="yq-edges">' . $edge . '</div>' );

	// Contents (page 2) and the numbered pages.
	$num = 3;
	foreach ( $body as $b ) {
		if ( $b[0] ) {
			$toc[] = array( $b[0], $num );
		}
		++$num;
	}
	$list = '';
	foreach ( $toc as $t ) {
		$list .= '<li><button type="button" class="yq-goto" data-page="' . esc_attr( $t[1] - 1 ) . '"><span>' . esc_html( $t[0] ) . '</span><i></i><b>' . esc_html( $t[1] ) . '</b></button></li>';
	}
	$pages[] = yroc_q_page( $issue, 2, 'yq-contents', yroc_q_kicker( 'In this issue' ) . '<h2>Contents</h2><ol class="yq-toc">' . $list . '</ol><p class="yq-about">' . yroc_q_text( $issue['about'] ) . '</p>' );
	$num     = 3;
	foreach ( $body as $b ) {
		$pages[] = yroc_q_page( $issue, $num++, $b[1], $b[2] );
	}

	// Pad to an even count so the back cover lands on the left, then the back cover.
	if ( 0 !== ( count( $pages ) + 1 ) % 2 ) {
		$pages[] = yroc_q_page( $issue, $num++, 'yq-notes', yroc_q_kicker( 'Notes' ) . '<h2>Your ideas for next quarter</h2><p class="yq-dek">Questions about anything in this issue? Bring them to your next call, or send them to us any time at ' . esc_html( $info['email'] ) . '.</p><div class="yq-lines" aria-hidden="true"></div>' );
	}
	$back    = $issue['back'];
	$pages[] = yroc_q_page(
		$issue,
		0,
		'yq-back yq-dark',
		'<div class="yq-orbit yq-orbit-sm" aria-hidden="true"><i></i><i></i><b></b></div><h2>' . yroc_q_text( $back['headline'] ) . '</h2><p class="yq-dek">' . yroc_q_text( $back['dek'] ) . '</p>'
		. yroc_q_link( $back['cta_url'], $back['cta'], 'yq-btn yq-btn-green' )
		. '<div class="yq-contact"><p><b>Yroc Consulting</b></p><p>' . esc_html( $info['street'] ) . ', Sherman, TX</p><p>' . esc_html( $info['phone'] ) . ' · ' . esc_html( $info['email'] ) . '</p></div>'
		. '<img class="yq-logo" src="' . esc_url( get_theme_file_uri( 'assets/img/logo-white.png' ) ) . '" alt="Yroc Consulting" width="160" height="40">',
		'data-density="hard"'
	);

	$pdf = ! empty( $issue['pdf'] ) ? get_theme_file_uri( 'assets/quarterly/' . $issue['pdf'] ) : '';
	return '<div class="yq" data-issue="' . esc_attr( $issue['slug'] ) . '">'
		. '<div class="yq-top"><a class="yq-home" href="' . esc_url( home_url( '/' ) ) . '"><img src="' . esc_url( get_theme_file_uri( 'assets/img/logo-white.png' ) ) . '" alt="Yroc Consulting" width="128" height="32"></a><a class="yq-all" href="' . esc_url( home_url( '/quarterly/' ) ) . '">All issues</a></div>'
		. '<div class="yq-stage"><div class="yq-book">' . implode( '', $pages ) . '</div></div>'
		. '<nav class="yq-nav" aria-label="Magazine pages">'
		. '<button type="button" class="yq-prev" aria-label="Previous page">&larr;</button>'
		. '<span class="yq-pos" aria-live="polite">Cover</span>'
		. '<button type="button" class="yq-next" aria-label="Next page">&rarr;</button>'
		. ( $pdf ? '<a class="yq-pdf" href="' . esc_url( $pdf ) . '" download>Download PDF</a>' : '' )
		. '</nav>'
		. '<p class="yq-hint">Drag a page corner, use the arrows, or your keyboard&rsquo;s left and right keys.</p>'
		. '</div>';
} );

add_shortcode( 'yroc_quarterly_list', function () {
	$out = '';
	foreach ( yroc_quarterly_issues() as $slug => $issue ) {
		if ( ( $issue['status'] ?? '' ) === 'hidden' ) {
			continue;
		}
		$out .= '<a class="yq-card" href="' . esc_url( home_url( '/quarterly/' . $slug . '/' ) ) . '">'
			. '<span class="yq-card-cover yq-dark"><span class="yq-mast-name">' . esc_html( $issue['title'] ) . '</span><span class="yq-card-issue">' . esc_html( $issue['issue'] ) . '</span><span class="yq-card-h">' . yroc_q_text( $issue['cover']['headline'] ) . '</span></span>'
			. '<span class="yq-card-meta">' . esc_html( $issue['date'] ) . '</span></a>';
	}
	return '<div class="yq-cards">' . $out . '</div>';
} );
