<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ignation.io
 * @since      1.0.0
 *
 * @package    Ignation_Iot
 * @subpackage Ignation_Iot/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ignation_Iot
 * @subpackage Ignation_Iot/admin
 * @author     Daimy van Rhenen <daimy.van.rhenen@ignation.io>
 */
class Ignation_Iot_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if(isset($_GET['action'])) {
			if($_GET['action'] == "removeSensor") {
				$this->removeSensor();
			}
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ignation_Iot_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ignation_Iot_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ignation-iot-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'fonts/font-awesome/css/font-awesome.css', array(), '' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ignation_Iot_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ignation_Iot_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ignation-iot-admin.js', array( 'jquery' ), $this->version, false );
    wp_enqueue_script( 'icon-picker', plugin_dir_url( __FILE__ ) . 'js/icon-picker.js', array( 'jquery' ), '1.0' );
		wp_enqueue_script( 'wp-color-picker');

	}

	public function add_plugin_admin_menu() {
		add_options_page( 'Ignation IOT', 'Ignation IOT', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
	}

	public function add_action_links( $links ) {
		$settings_link = array( '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>', );
		return array_merge( $settings_link, $links );
	}

	public function display_plugin_setup_page() {
		include_once( 'partials/ignation-iot-admin-display.php' );
	}

	public function options_update() {
    register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
	}
	public function validate($input) {
		$options = get_option($this->plugin_name) ? get_option($this->plugin_name) : array();

		$message = null;
    $type = null;

		$tabSelector = sanitize_text_field($input['tabSelector']);

		if($tabSelector == 'tab1'){

			$input_module_id = sanitize_text_field($input['module']);
			$input_sensor_id = sanitize_text_field($input['module_sensors']);

			if($input_module_id != 'null' && $input_sensor_id != 'null'){
				$exists = false;

				for ($i = 0; $i < count($options); $i++) {
					$module = $options[$i];

					if(isset($module['module'])){
						$module_id = $module['module'];
						$sensor_id = $module['sensors'];

						if ($module_id == $input_module_id && $sensor_id == $input_sensor_id) {
							$exists = true;
						}
					}
				}
				if(!$exists) {
					$module = array('module' => sanitize_text_field($input['module']), 'sensors' => sanitize_text_field($input['module_sensors']));

					array_push($options, $module);
				} else {
					$type = 'error';
					$message = __( 'This sensor is already activated', 'my-text-domain' );

					add_settings_error('form_error', esc_attr( 'settings_updated' ), $message, $type);
					return $options;
				}
				return $options;
			} elseif ($input_module_id == 'null') {
				$type = 'error';
				$message = __( 'You haven&#39;t selected a module', 'my-text-domain' );

				add_settings_error('form_error', esc_attr( 'settings_updated' ), $message, $type);
				return $options;

			} elseif ($input_sensor_id == 'null') {
				$type = 'error';
				$message = __( 'You haven&#39;t selected a sensor', 'my-text-domain' );

				add_settings_error('form_error', esc_attr( 'settings_updated' ), $message, $type);
				return $options;
			}
		} elseif ($tabSelector == 'tab2'){
			$form_description = sanitize_text_field($input['description']);
			$form_json = sanitize_text_field($input['jsonlink']);

			if(!empty($form_description)){
				for ($i = 0; $i < count($options); $i++) {
					$module = $options[$i];

					if(isset($module['description']) == true){
						array_splice($options, $i, 1);
					}
				}

				$description = array('description' => sanitize_text_field($input['description']));
				array_push($options, $description);
			} else{
				$type = 'error';
				$message = __( 'Fill a description in', 'my-text-domain' );

				add_settings_error('form_error', esc_attr( 'settings_updated' ), $message, $type);
				return $options;
			}

			if(!empty($form_json)){
				if (filter_var($form_json, FILTER_VALIDATE_URL) !== false && strpos($form_json,'.json') !== false) {
					for ($i = 0; $i < count($options); $i++) {
						$module = $options[$i];

						if(isset($module['json']) == true){
							array_splice($options, $i, 1);
						}
					}

					$json = array('json' => sanitize_text_field($input['jsonlink']));
					array_push($options, $json);
				} else{
					$type = 'error';
					$message = __( 'Fill a valid url to json file in', 'my-text-domain' );

					add_settings_error('form_error', esc_attr( 'settings_updated' ), $message, $type);
					return $options;
				}
			} else{
				$type = 'error';
				$message = __( 'Fill a url in', 'my-text-domain' );

				add_settings_error('form_error', esc_attr( 'settings_updated' ), $message, $type);
				return $options;
			}

			return $options;
		}
	}

	public function removeSensor() {
		if(isset($_GET['moduleId']) && isset($_GET['sensorId'])) {
			$options = get_option($this->plugin_name);

			$moduleId = $_GET['moduleId'];
			$sensorId = $_GET['sensorId'];

			for ($i = 0; $i < count($options); $i++) {
				$module = $options[$i];

				if(isset($module['module'])){

					if ($moduleId == $module['module'] && $sensorId == $module['sensors']) {
						$options = get_option($this->plugin_name);

						array_splice($options, $i, 1);
						update_option($this->plugin_name, $options);

						$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
						$url = $protocol . $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page='.$_GET['page'];

						header("Location: $url");
					}
				}
			}

			die();
		} else {
			$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
			$url = $protocol . $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page='.$_GET['page'];

			header("Location: $url");
			die();
		}
	}
}

?>
