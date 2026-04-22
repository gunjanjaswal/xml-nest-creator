<?php
/**
 * Compatibility formatting to disable other sitemaps.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class XMLNC_Compat {

	public function __construct() {
		// Disable core WordPress sitemap (available in WP 5.5+).
		add_filter( 'wp_sitemaps_enabled', '__return_false' );

		// Disable Yoast SEO sitemap functionality.
		add_filter( 'wpseo_xml_sitemap_enable', '__return_false' );

		// Disable Rank Math sitemap functionality.
		add_filter( 'rank_math/sitemap/enable', '__return_false' );
	}
}
