<?php
/**
 * Plugin Name: Feed de Botones Simple
 * Plugin URI:  https://github.com/Dsantycam/feed-de-botones-simple
 * Description: Crea feeds de botones tipo bio link mediante shortcode.
 * Version:     1.2.0
 * Author:      Santiago Camacho Teatino
 * Author URI:  https://santiagocamachomkt.com
 * Text Domain: feed-de-botones-simple
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 7.1
 * Requires PHP: 7.4
 * License: GPL2+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FDBS_VERSION', '1.2.0' );
define( 'FDBS_PLUGIN_FILE', __FILE__ );
define( 'FDBS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'FDBS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FDBS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Repositorio de GitHub del que se descargan las actualizaciones (repo público + Releases).
 */
define( 'FDBS_GITHUB', 'https://github.com/Dsantycam/feed-de-botones-simple/' );

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

/**
 * Actualizaciones automáticas desde GitHub (Plugin Update Checker v5).
 *
 * Para publicar una actualización: crea un "Release" en GitHub cuyo tag sea la
 * nueva versión (igual que "Version:" en esta cabecera y "Stable tag" en el readme)
 * y adjunta el zip limpio feed-de-botones-simple.zip.
 */
function fdbs_init_updater() {
	require_once FDBS_PLUGIN_PATH . 'includes/plugin-update-checker/plugin-update-checker.php';

	$checker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		FDBS_GITHUB,
		FDBS_PLUGIN_FILE,
		'feed-de-botones-simple'
	);

	// Instala el zip limpio adjunto al release; si no existe, usa el zip del código del tag.
	$checker->getVcsApi()->enableReleaseAssets( '/feed-de-botones-simple\.zip$/i' );
}

// Siempre (admin, cron y WP-CLI), para que las actualizaciones en segundo plano lo vean.
add_action( 'plugins_loaded', 'fdbs_init_updater' );