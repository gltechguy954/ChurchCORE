<?php
/**
 * Plugin Name:       ChurchCore CMS
 * Plugin URI:        https://example.com/churchcore
 * Description:       A centralized church management system integrating with Fluent CRM, Fluent Boards, Fluent Forms, and WooCommerce.
 * Version:           0.1.0
 * Author:            ChurchCore Contributors
 * Author URI:        https://example.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       churchcore
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CHURCHCORE_PLUGIN_VERSION', '0.1.0' );
define( 'CHURCHCORE_PLUGIN_FILE', __FILE__ );
define( 'CHURCHCORE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CHURCHCORE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once CHURCHCORE_PLUGIN_DIR . 'includes/class-churchcore-plugin.php';

function churchcore_run_plugin() {
    $plugin = new ChurchCore_Plugin();
    $plugin->run();
}

register_activation_hook( __FILE__, array( 'ChurchCore_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ChurchCore_Plugin', 'deactivate' ) );

churchcore_run_plugin();
