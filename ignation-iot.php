<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ignation.io/
 * @since             1.0.0
 * @package           Ignation_Iot
 *
 * @wordpress-plugin
 * Plugin Name:       Ignation IOT
 * Plugin URI:				https://ignation.io/
 * Description:       Project Narnia in its full glory.
 * Version:           1.0
 * Author:            Daimy van Rhenen
 * Author URI:        https://ignation.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ignation-iot
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ignation-iot-activator.php
 */
function activate_ignation_iot() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ignation-iot-activator.php';
	Ignation_Iot_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ignation-iot-deactivator.php
 */
function deactivate_ignation_iot() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ignation-iot-deactivator.php';
	Ignation_Iot_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ignation_iot' );
register_deactivation_hook( __FILE__, 'deactivate_ignation_iot' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ignation-iot.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ignation_iot() {

	$plugin = new Ignation_Iot();
	$plugin->run();

}

run_ignation_iot();
