<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://daimyvanrhenen.nl/
 * @since      1.0.0
 *
 * @package    Ignation_Iot
 * @subpackage Ignation_Iot/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ignation_Iot
 * @subpackage Ignation_Iot/includes
 * @author     Daimy van Rhenen <daimy.van.rhenen@ignation.io>
 */
class Ignation_Iot_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ignation-iot',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
