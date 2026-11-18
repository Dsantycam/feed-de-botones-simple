<?php
/**
 * Plugin Name: Feed de Botones Simple
 * Plugin URI:  https://santiagocamachomkt.com
 * Description: Crea feeds de botones tipo bio link mediante shortcode.
 * Version:     1.1.0
 * Author:      Santiago Camacho Teatino
 * Author URI:  https://santiagocamachomkt.com
 * Text Domain: feed-de-botones-simple
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL2+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FDBS_VERSION', '1.0.0' );
define( 'FDBS_PLUGIN_FILE', __FILE__ );
define( 'FDBS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'FDBS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FDBS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once FDBS_PLUGIN_PATH . 'includes/class-fdbs-activator.php';
require_once FDBS_PLUGIN_PATH . 'includes/class-fdbs-deactivator.php';
require_once FDBS_PLUGIN_PATH . 'includes/class-fdbs-helpers.php';
require_once FDBS_PLUGIN_PATH . 'admin/class-fdbs-post-type.php';
require_once FDBS_PLUGIN_PATH . 'admin/class-fdbs-admin.php';
require_once FDBS_PLUGIN_PATH . 'public/class-fdbs-public.php';
require_once FDBS_PLUGIN_PATH . 'includes/class-fdbs-plugin.php';
require_once FDBS_PLUGIN_PATH . 'admin/class-fdbs-metaboxes.php';
require_once FDBS_PLUGIN_PATH . 'admin/class-fdbs-save-feed.php';
require_once FDBS_PLUGIN_PATH . 'admin/class-fdbs-admin-columns.php';

register_activation_hook( __FILE__, array( 'FDBS_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'FDBS_Deactivator', 'deactivate' ) );

function fdbs_run_plugin() {
	$plugin = new FDBS_Plugin();
	$plugin->run();
}

fdbs_run_plugin();