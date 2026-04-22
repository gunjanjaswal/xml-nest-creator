<?php
/**
 * Plugin Name: XML Nest Creator
 * Plugin URI:  https://github.com/gunjanjaswal/xml-nest-creator
 * Description: A simple XML creator for posts, pages, categories (or others) that generates a sitemap.xml and overrides popular SEO plugins specifically for the XML sitemap.
 * Version:     1.0.2
 * Author:      Gunjan Jaswal
 * Author URI:  https://www.gunjanjaswal.me
 * Text Domain: xml-nest-creator
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'XMLNC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'XMLNC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'XMLNC_VERSION', '1.0.2' );

// Include necessary files.
require_once XMLNC_PLUGIN_DIR . 'includes/class-xmlnc-compat.php';
require_once XMLNC_PLUGIN_DIR . 'includes/class-xmlnc-core.php';
require_once XMLNC_PLUGIN_DIR . 'includes/class-xmlnc-settings.php';

// Initialize the plugin classes.
function xmlnc_init_plugin() {
	new XMLNC_Compat();
	new XMLNC_Core();

	if ( is_admin() ) {
		new XMLNC_Settings();
	}
}
add_action( 'plugins_loaded', 'xmlnc_init_plugin' );

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
