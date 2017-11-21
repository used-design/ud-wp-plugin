<?php

/**
 *
 * @link              https://www.used-design.com
 * @since             3.0.0
 * @package           UsedDesign
 *
 * @wordpress-plugin
 * Plugin Name:       used-design
 * Plugin URI:        https://github.com/used-design/ud-wp-plugin
 * Description:       Zeigen Sie Ihre used-design Angebote auf Ihrer eigenen Webseite
 * Version:           0.2.4
 * Author:            used-design
 * Author URI:        https://www.used-design.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       usedDesign
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define('USEDDESIGN_API_URL', 'https://www.used-design.com/api/v2');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/UsedDesignActivator
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/UsedDesignActivator.php';
	UsedDesignActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/UsedDesignDeactivator
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/UsedDesignDeactivator.php';
	UsedDesignDeactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/UsedDesign.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_plugin_name() {

	$plugin = new UsedDesign();
	$plugin->run();
}
run_plugin_name();


/**
 * Display Settings link in plugin list view
 */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

function add_action_links ( $links ) {
    $mylinks = array(
        '<a href="' . admin_url( 'options-general.php?page=useddesign-options' ) . '">Settings</a>',
    );
    return array_merge( $links, $mylinks );
}
