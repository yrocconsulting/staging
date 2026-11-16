<?php
/**
 * 301 redirects for URLs from the previous yrocconsulting.com that are not on the new site.
 *
 * Only runs when WordPress would otherwise show a 404, so real pages always win.
 * Blog posts, categories and tags kept their original URLs and need no redirect.
 * To add one: put the old path (with trailing slash) and the new path below.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yroc_redirect_map() {
	return array(
		// Services that moved or merged.
		'/ai-seo-optimization/'                  => '/ai-search-optimization/',
		'/texas-ai-marketing-agency/'            => '/locations/',
		'/search-engine-optimization/'           => '/ai-search-optimization/',
		'/what-is-search-engine-optimization-seo/' => '/kb/ai-search-vs-traditional-seo/',
		'/local-seo-services/'                   => '/locations/',
		'/local-seo-services/plumbing/'          => '/ai-search-optimization/',
		'/lubbock-seo-services/'                 => '/locations/lubbock-tx/',
		'/lubbock-lead-generation/'              => '/locations/lubbock-tx/',
		'/sherman/'                              => '/locations/sherman-tx/',
		'/san-angelo/'                           => '/locations/san-angelo-tx/',
		'/lubbock/'                              => '/locations/lubbock-tx/',
		'/lead-generation/'                      => '/ai-search-optimization/',
		'/organic-traffic/'                      => '/ai-search-optimization/',
		'/social-media-marketing/'               => '/ai-search-optimization/',
		'/email-marketing/'                      => '/ai-search-optimization/',
		'/services/'                             => '/ai-search-optimization/',
		'/ai-website-audit/'                     => '/ai-visibility-audit/',
		'/seo-audit-services/'                   => '/ai-visibility-audit/',
		'/seo-checker/'                          => '/ai-visibility-audit/',
		'/free-consultation-and-site-analysis/'  => '/ai-visibility-audit/',
		'/schema-markup-generator/'              => '/schema-structured-data/',
		'/website-design/'                       => '/website-packages/',
		'/lead-generation-websites/'             => '/website-packages/',
		'/built-for-you-lead-gen-wordpress-websites/' => '/website-packages/',
		'/page-speed-optimization-services/'     => '/website-packages/',
		'/portfolio/'                            => '/website-packages/',
		'/website-portfolio/'                    => '/website-packages/',
		'/website-portfolio-2/'                  => '/website-packages/',
		'/get-started/'                          => '/book-a-strategy-call/',
		'/results2/'                             => '/case-studies/',
		// Company and resources.
		'/yroc-team/'                            => '/about-us/',
		'/job-opportunities/'                    => '/about-us/',
		'/referrals/'                            => '/contact/',
		'/resources/'                            => '/kb/',
		'/helpdesk/'                             => '/kb/',
		'/video-tutorials/'                      => '/kb/',
		'/keyword-list/'                         => '/kb/',
		'/flywheel-software-setup/'              => '/kb/',
		'/ufaqs/'                                => '/kb/',
		'/author/yrocsupport/'                   => '/blog/',
		// Client tools that are being replaced.
		'/client-portal-login/'                  => '/contact/',
		'/client-payments/'                      => '/contact/',
		'/client-intake-form/'                   => '/contact/',
		'/clinic-form/'                          => '/contact/',
		'/reset-your-password/'                  => '/contact/',
		// Retired pages, campaigns and test pages.
		'/homepage/'                             => '/',
		'/2020-seo-christmas-catalog/'           => '/',
		'/songs/'                                => '/',
		'/ministry-submission/'                  => '/',
		'/ministry-submission-ty/'               => '/',
		'/ministry-missions-giveaway-terms-and-conditions/' => '/',
		'/thank-you-dsf/'                        => '/',
		'/conditional-test/'                     => '/',
		'/form-test/'                            => '/',
		'/test-form/'                            => '/',
		'/test-blog/'                            => '/',
		'/seo-mega-menu/'                        => '/',
		'/services-mega-menu/'                   => '/',
		'/support-mega-menu/'                    => '/',
		'/support-mega-menu3/'                   => '/',
	);
}

/** Whole sections of the old site (old path prefix => new path). */
function yroc_redirect_prefixes() {
	return array(
		'/order-forms-invoice/' => '/website-packages/',
		'/order-forms/'         => '/website-packages/',
		'/dsf2021/'             => '/',
		'/account/'             => '/contact/',
		'/ufaqs/'               => '/kb/',
		'/product/'             => '/website-packages/',
		'/product-category/'    => '/website-packages/',
		'/cart/'                => '/website-packages/',
		'/basket/'              => '/website-packages/',
		'/checkout/'            => '/website-packages/',
		'/wishlist/'            => '/website-packages/',
		'/my-account/'          => '/contact/',
	);
}

function yroc_find_redirect( $path ) {
	$path = '/' . trim( strtolower( $path ), '/' ) . '/';
	if ( '//' === $path ) {
		return null;
	}
	$map = yroc_redirect_map();
	if ( isset( $map[ $path ] ) ) {
		return $map[ $path ];
	}
	foreach ( yroc_redirect_prefixes() as $prefix => $to ) {
		if ( 0 === strpos( $path, $prefix ) ) {
			return $to;
		}
	}
	return null;
}

add_action( 'template_redirect', function () {
	if ( ! is_404() ) {
		return;
	}
	$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
	$to   = yroc_find_redirect( $path );
	if ( $to ) {
		wp_safe_redirect( home_url( $to ), 301, 'Yroc redirects' );
		exit;
	}
}, 1 );
