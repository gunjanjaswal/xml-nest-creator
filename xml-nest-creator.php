<?php
/**
 * Plugin Name: XML Nest Creator
 * Plugin URI:  https://github.com/gunjanjaswal/xml-nest-creator
 * Description: A simple XML creator for posts, pages, categories (or others) that generates a sitemap.xml and overrides popular SEO plugins specifically for the XML sitemap.
 * Version:     1.1.0
 * Author:      Gunjan Jaswal
 * Author URI:  https://www.gunjanjaswal.me
 * Text Domain: xml-nest-creator
 * Requires at least: 5.5
 * Tested up to: 7.1
 * Requires PHP: 7.4
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'XMLNC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'XMLNC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'XMLNC_VERSION', '1.1.0' );

// Maximum number of URLs per sub-sitemap before pagination kicks in.
define( 'XMLNC_PER_PAGE', 2000 );

// Include necessary files.
require_once XMLNC_PLUGIN_DIR . 'includes/class-xmlnc-compat.php';
require_once XMLNC_PLUGIN_DIR . 'includes/class-xmlnc-core.php';
require_once XMLNC_PLUGIN_DIR . 'includes/class-xmlnc-ping.php';
require_once XMLNC_PLUGIN_DIR . 'includes/class-xmlnc-settings.php';
require_once XMLNC_PLUGIN_DIR . 'includes/class-xmlnc-metabox.php';

// Initialize the plugin classes.
function xmlnc_init_plugin() {
	new XMLNC_Compat();
	new XMLNC_Core();
	new XMLNC_Ping();

	if ( is_admin() ) {
		new XMLNC_Settings();
		new XMLNC_Metabox();
	}
}
add_action( 'plugins_loaded', 'xmlnc_init_plugin' );

// Flush rewrite rules once after an upgrade so new sitemap routes register.
add_action( 'plugins_loaded', 'xmlnc_maybe_upgrade', 5 );
function xmlnc_maybe_upgrade() {
	if ( get_option( 'xmlnc_version' ) !== XMLNC_VERSION ) {
		set_transient( 'xmlnc_flush_rewrite_rules', 1 );
		update_option( 'xmlnc_version', XMLNC_VERSION );
	}
}

// Activation hook: set a transient to flush rules on next load.
register_activation_hook( __FILE__, 'xmlnc_plugin_activate' );
function xmlnc_plugin_activate() {
	set_transient( 'xmlnc_flush_rewrite_rules', 1 );
}

// Deactivation hook: flush rules immediately.
register_deactivation_hook( __FILE__, 'xmlnc_plugin_deactivate' );
function xmlnc_plugin_deactivate() {
	flush_rewrite_rules();
}

/**
 * Add Settings + Support on Ko-fi links to plugin action links (next to Deactivate).
 *
 * @param array $links Existing plugin action links.
 * @return array Modified action links.
 */
function xmlnc_plugin_action_links( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=xml-nest-creator' ) ) . '">' . esc_html__( 'Settings', 'xml-nest-creator' ) . '</a>';
	$kofi_link     = '<a href="https://ko-fi.com/gunjanjaswal" target="_blank" style="color:#0073aa; font-weight:bold;">' . esc_html__( 'Support on Ko-fi', 'xml-nest-creator' ) . '</a>';
	array_unshift( $links, $settings_link, $kofi_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'xmlnc_plugin_action_links' );

/**
 * Add Contact Developer link to plugin row meta on the Plugins screen.
 *
 * @param array  $links Existing plugin row meta links.
 * @param string $file  Plugin file name.
 * @return array Modified row meta links.
 */
function xmlnc_plugin_row_meta( $links, $file ) {
	if ( plugin_basename( __FILE__ ) === $file ) {
		$links[] = '<a href="https://wordpress.org/support/plugin/xml-nest-creator/" target="_blank">' . esc_html__( 'Plugin Support', 'xml-nest-creator' ) . '</a>';
		$links[] = '<a href="mailto:hello@gunjanjaswal.me">' . esc_html__( 'Contact Developer', 'xml-nest-creator' ) . '</a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'xmlnc_plugin_row_meta', 10, 2 );
