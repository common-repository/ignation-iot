<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ignation.io
 * @since      1.0.0
 *
 * @package    Ignation_Iot
 * @subpackage Ignation_Iot/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ignation_Iot
 * @subpackage Ignation_Iot/includes
 * @author     Daimy van Rhenen <daimy.van.rhenen@ignation.io>
 */


class Ignation_Iot extends WP_Widget {

	// WIDGET

		//enqueues our locally supplied font awesome stylesheet
			public function enqueue_our_required_stylesheets(){
				wp_enqueue_style('font-awesome', get_stylesheet_directory_uri() . '../public/fonts/font-awesome-4.6.3/css/font-awesome.css');
			}

		/**
		 * Sets up the widgets name etc
		 */
		public function __construct() {
			/**
			 * Define the core functionality of the plugin.
			 *
			 * Set the plugin name and the plugin version that can be used throughout the plugin.
			 * Load the dependencies, define the locale, and set the hooks for the admin area and
			 * the public-facing side of the site.
			 *
			 * @since    1.0.0
			 */

				$this->plugin_name = 'ignation-iot';
				$this->version = '1.0';

				$this->load_dependencies();
				$this->set_locale();
				$this->define_admin_hooks();
				$this->define_public_hooks();

			$widget_ops = array(
				'classname' => 'Ignation_IOT',
				'description' => 'Project Narnia in its full glory.',
			);
			parent::__construct( 'Ignation_IOT', 'Ignation IOT', $widget_ops );

		}

		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			$options = get_option($this->plugin_name);

			// CHECK IF JSON FILE AND DESCRIPTION EXISTS
				if (count($options) > 0) {
					for ($j = 0; $j < count($options); $j++) {
						$module = $options[$j];

						if(array_key_exists('description', $module)){
							if(isset($module['description'])){
								$ignation_description = $module['description'];
							} else{
								$ignation_description = "";
							}
						} else{
							$ignation_description = "";
						}
						if(array_key_exists('json', $module)){
							if(isset($module['json'])){
								$ignation_json = $module['json'];
							}
						}
					}
				}

			$json_array = file_get_contents($ignation_json);

			if(!$json_array){
				echo "<p class='ignation-connection-error'>Unfortunately, the server can't be reached to show you our awesome IoT-sensors...</p>";
			} else{
		    $allSensors = json_decode($json_array);

		?>
				<div id="ignation-iot-front-wrap">
					<div class="ignation-iot-front-description">
						<p class="description"><?php echo $ignation_description; ?></p>
					</div>
						<div id="ignation-iot-front-container">
							<?php
								// Current timestamp
									date_default_timezone_set('Europe/Amsterdam');
									$current_timestamp = date("H:i:s",time());
							?>
							<p class="timestamp">Last update on <?php echo $current_timestamp; ?></p>

						<?php
						if (count($options) > 0) {

							foreach ($options as $opt => $value) {
								for ($i=0; $i < count($options); $i++) {
									$sensor_position = $i;
									if($options[$i] == $value){
										$module = $options[$i];
										$option_id = $i;

										if(isset($module['module'])){
											$sensor_module_id = $module['module'];
											$sensor_sensor_id = $module['sensors'];

											if(isset($instance[$sensor_module_id .'_'. $sensor_sensor_id])){

												for ($a=0; $a < count($allSensors); $a++) {
													if ($sensor_module_id == $allSensors[$a]->id) {
														$allSensors_sensors = $allSensors[$a]->sensors;

														// Sensor type koppelen aan data-formaat
														for ($b=0; $b < count($allSensors_sensors); $b++) {
															if ($sensor_sensor_id == $allSensors_sensors[$b]->id) {
																if($allSensors_sensors[$b]->lastValue != 0){
																	$sensor_value = $allSensors_sensors[$b]->lastValue;
																} else{
																	$sensor_value = '0';
																}
															}
														}
													}
												}

												// Assign saved values to variables
													if($instance) {
														if(isset($instance[$sensor_module_id .'_'. $sensor_sensor_id]['label'])) {
															$sensor_label = esc_attr($instance[$sensor_module_id .'_'. $sensor_sensor_id]['label']);
														} else{
															$sensor_label = $sensor_module_id .', sensor '. $sensor_sensor_id;
														}
														if(isset($instance[$sensor_module_id .'_'. $sensor_sensor_id]['icon'])) {
															$sensor_icon = esc_attr($instance[$sensor_module_id .'_'. $sensor_sensor_id]['icon']);
														} else{
															$sensor_icon = 'question';
														}
														if(isset($instance[$sensor_module_id .'_'. $sensor_sensor_id]['color'])) {
															$sensor_color = esc_attr($instance[$sensor_module_id .'_'. $sensor_sensor_id]['color']);
														} else{
															$sensor_color = '#000';
														}
													}
												?>
												<div class="ignation-iot-front-column">
													<div class="column-left">
														<h1 style="color: <?php echo $sensor_color; ?>"><?php echo $sensor_value; ?></h1>
														<p><?php echo $sensor_label; ?></p>
													</div>
													<div class="column-right" style="color: <?php echo $sensor_color; ?>">
														<i class="fa fa-<?php echo $sensor_icon; ?>" aria-hidden="true"></i>
													</div>
												</div>
											<?php
											}
										}
									}
								}
							}
						} else{
					 ?>
								<p class="no_sensors">Sorry, we haven't activated any sensors... :(</p>
					<?php
						}
					 ?>
					</div>
				</div>
	<?php
			}

			extract( $args );
		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$options = get_option($this->plugin_name);

			// CHECK IF JSON FILE AND DESCRIPTION EXISTS
				if (count($options) > 0) {
					$exists_json = false;

					for ($j = 0; $j < count($options); $j++) {
						$module = $options[$j];

						if(array_key_exists('json', $module)){
							$exists_json = true;
						}
					}
					if($exists_json == true){
						for ($s = 0; $s < count($options); $s++) {
							$module = $options[$s];

							if(isset($module['json'])){
								$ignation_json = $module['json'];
							}
						}
					}
				}

			$json_array = file_get_contents($ignation_json);

	?>

			<div id="ignation-iot-wrap">
				<div id="ignation-iot-container">

				<?php
				if(!$json_array){
					echo "<p class='ignation-connection-error'>The server can't be reached, check the your <a href='" . admin_url( 'options-general.php?page=' . $this->plugin_name .'&tab=second' ) . " ' target='blank'>" . __('settings', $this->plugin_name) . "</a>.</p>";
				} else{
			    $allSensors = json_decode($json_array);

					$sensor_count = 0;
					if (count($options) > 0) {
						foreach ($options as $opt => $value) {
							if($value['module']){
								$sensor_count ++;
							}

							for ($i=0; $i < count($options); $i++) {
								if($options[$i] == $value){
									$module = $options[$i];
									$option_id = $i;

									if(isset($module['module'])){
										$sensor_module_id = $module['module'];
										$sensor_sensor_id = $module['sensors'];

										foreach ($allSensors as $sen){
				              $_moduleid = $sen->id;
				              $_modulename = $sen->description;

				              if ($sensor_module_id == $_moduleid) {
				                $sensor_module_name = $_modulename;
				              }
				            }

										if($instance) {
											if(isset($instance[$sensor_module_id .'_'. $sensor_sensor_id]['label'])) {
												$label = esc_attr($instance[$sensor_module_id .'_'. $sensor_sensor_id]['label']);
											} else{
												$label = '';
											}
											if(isset($instance[$sensor_module_id .'_'. $sensor_sensor_id]['icon'])) {
												$icon = esc_attr($instance[$sensor_module_id .'_'. $sensor_sensor_id]['icon']);
											} else{
												$icon = '';
											}
											if(isset($instance[$sensor_module_id .'_'. $sensor_sensor_id]['color'])) {
												$color = esc_attr($instance[$sensor_module_id .'_'. $sensor_sensor_id]['color']);
											} else{
												$color = '';
											}
								    } else {
											$select = '';
											$label = $sensor_module_name .', sensor '. $sensor_sensor_id;
											$icon = '';
											$color = '';
								    }
					 ?>
						<div class="ignation-iot-back-column postbox">
							<div class="col-wrap">
								<div class="sensor_module">
									<h1><?php esc_attr_e($sensor_module_name, 'wp_admin_style' ); ?></h1>
									<h2><?php esc_attr_e('Sensor '. $sensor_sensor_id , 'wp_admin_style' ); ?></h2>
								</div>

								<div class="sensor_label">
									<label for="<?php echo $this->get_field_name($sensor_module_id .'_'. $sensor_sensor_id .'_label'); ?>"><?php _e('Label:', 'wp_widget_plugin'); ?></label>
									<input type="text" name="<?php echo $this->get_field_name($sensor_module_id .'_'. $sensor_sensor_id .'_label'); ?>" value="<?php echo $label ?>" />
								</div>

								<div class="sensor_icon_picker">
									<input class="regular-text" type="hidden" id="icon_picker_example_icon<?php echo $sensor_module_id .'_'. $sensor_sensor_id ?>" name="<?php echo $this->get_field_name($sensor_module_id .'_'. $sensor_sensor_id .'_icon'); ?>" value="<?php if( isset( $icon ) ) { echo esc_attr( $icon ); } ?>"/>
									<div id="preview_icon_picker_example_icon<?php echo $sensor_module_id .'_'. $sensor_sensor_id ?>" data-target="#icon_picker_example_icon<?php echo $sensor_module_id .'_'. $sensor_sensor_id ?>" class="button icon-picker fa fa-<?php if( isset( $icon ) ) { $v=explode('|',$icon); echo $v[0].' '.$v[1]; } ?>"></div>
								</div>

								<div class="sensor_color_picker">
									<input type="text" value="<?php echo $color ?>" class="wp-color-picker-field" data-default-color="#ffffff" data-current="<?php echo $color ?>" name="<?php echo $this->get_field_name($sensor_module_id .'_'. $sensor_sensor_id .'_color'); ?>" />
								</div>
							</div>
						</div>
				<?php
									}
								}
							}
						}
					} else{
						echo "<p>No sensors selected. <a href='" . admin_url( 'options-general.php?page=' . $this->plugin_name ) . " ' target='blank'>" . __('Add sensors', $this->plugin_name) . "</a></p>";
					}
				}
			 ?>
				</div>
			</div>
			<script>
				jQuery(document).ready(function($){
					jQuery('.wp-color-picker-field').wpColorPicker();
				});
				jQuery('.icon-picker').iconPicker();
			</script>
		<?php
		}

		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			// processes widget options to be saved

			$instance = $old_instance;
			$options = get_option($this->plugin_name);

			if (count($options) > 0) {
				for ($j = 0; $j < count($options); $j++) {
          $module = $options[$j];

          if(array_key_exists('json', $module)){
						if(isset($module['json'])){
              $ignation_json = $module['json'];
            }
          }
        }
      }

	    $json_array = file_get_contents($ignation_json);
	    $allSensors = json_decode($json_array);

			if (count($options) > 0) {
				foreach ($options as $opt => $value) {
					for ($z=0; $z < count($options); $z++) {
						if($options[$z] == $value){
							$module = $options[$z];

							$sensor_module_id = $module['module'];
							$sensor_sensor_id = $module['sensors'];

							foreach ($allSensors as $sen){
								$_moduleid = $sen->id;
								$_modulename = $sen->description;

								if ($sensor_module_id == $_moduleid) {
									$sensor_module_name = $_modulename;
								}
							}

							// LABEL
								if(isset($new_instance[$sensor_module_id .'_'. $sensor_sensor_id .'_label'])) {
									if(strip_tags($new_instance[$sensor_module_id .'_'. $sensor_sensor_id .'_label']) == ""){
											$instance[$sensor_module_id .'_'. $sensor_sensor_id .'']['label'] = $sensor_module_name .', sensor '. $sensor_sensor_id;
									} else{
										$instance[$sensor_module_id .'_'. $sensor_sensor_id .'']['label'] = strip_tags($new_instance[$sensor_module_id .'_'. $sensor_sensor_id .'_label']);
									}
								}

							// ICON
								if(isset($new_instance[$sensor_module_id .'_'. $sensor_sensor_id .'_icon'])) {
									$instance[$sensor_module_id .'_'. $sensor_sensor_id .'']['icon'] = strip_tags($new_instance[$sensor_module_id .'_'. $sensor_sensor_id .'_icon']);
								}

							// COLOR
								if(isset($new_instance[$sensor_module_id .'_'. $sensor_sensor_id .'_color'])) {
									$instance[$sensor_module_id .'_'. $sensor_sensor_id .'']['color'] = strip_tags($new_instance[$sensor_module_id .'_'. $sensor_sensor_id .'_color']);
								}
						}
					}
				}
			}

	    return $instance;
		}
	// END WIDGET

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ignation_Iot_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ignation_Iot_Loader. Orchestrates the hooks of the plugin.
	 * - Ignation_Iot_i18n. Defines internationalization functionality.
	 * - Ignation_Iot_Admin. Defines all hooks for the admin area.
	 * - Ignation_Iot_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ignation-iot-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ignation-iot-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ignation-iot-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ignation-iot-public.php';


		$this->loader = new Ignation_Iot_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ignation_Iot_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ignation_Iot_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ignation_Iot_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add menu item
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );


		// Add Settings link to the plugin
			$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
			$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );


		// Save/Update our plugin options
			$this->loader->add_action('admin_init', $plugin_admin, 'options_update');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ignation_Iot_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ignation_Iot_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Ignation_IOT' );
});
?>
