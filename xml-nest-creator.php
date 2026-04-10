<?php
/**
 * Plugin Name: XML Nest Creator
 * Plugin URI:  https://github.com/gunjanjaswal/xml-nest-creator
 * Description: A simple XML creator for posts, pages, categories (or others) that generates a sitemap.xml and overrides popular SEO plugins specifically for the XML sitemap.
 * Version:     1.0.1
 * Author:      Gunjan Jaswal
 * Author URI:  https://www.gunjanjaswal.me
 * Text Domain: xml-nest-creator
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'XNC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'XNC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'XNC_VERSION', '1.0.1' );

// Include necessary files.
require_once XNC_PLUGIN_DIR . 'includes/class-xnc-compat.php';
require_once XNC_PLUGIN_DIR . 'includes/class-xnc-core.php';
require_once XNC_PLUGIN_DIR . 'includes/class-xnc-settings.php';

// Initialize the plugin classes.
function xnc_init_plugin() {
	new XNC_Compat();
	new XNC_Core();

	if ( is_admin() ) {
		new XNC_Settings();
	}
}
add_action( 'plugins_loaded', 'xnc_init_plugin' );

// Activation hook: set a transient to flush rules on next load.
register_activation_hook( __FILE__, 'xnc_plugin_activate' );
function xnc_plugin_activate() {
	set_transient( 'xnc_flush_rewrite_rules', 1 );
}

// Deactivation hook: flush rules immediately.
register_deactivation_hook( __FILE__, 'xnc_plugin_deactivate' );
function xnc_plugin_deactivate() {
	flush_rewrite_rules();
}
