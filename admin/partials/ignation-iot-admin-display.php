<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://ignation.io/
 * @since      1.0.0
 *
 * @package    Ignation_Iot
 * @subpackage Ignation_Iot/admin/partials
 */

 // Alle modules en zijn sensoren ophalen van Jarvis
   settings_fields($this->plugin_name);
   do_settings_sections($this->plugin_name);

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

   <?php

   function page_tabs($current = 'first') {
    $tabs = array(
      'first'   => __("Sensors", 'plugin-textdomain'),
      'second'  => __("Settings", 'plugin-textdomain'),
      'third'  => __("About", 'plugin-textdomain')
    );
    $html =  '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
      $class = ($tab == $current) ? 'nav-tab-active' : '';
      $html .=  '<a class="nav-tab ' . $class . '" href="?page=ignation-iot&tab=' . $tab . '">' . $name . '</a>';
    }
    $html .= '</h2>';
    echo $html;
  }
  if($json_array || $json_array != ""){
  ?>
    <script type="text/javascript">
      var modules = JSON.parse('<?php echo $json_array; ?>');
    </script>
  <?php } ?>

<div class="wrap ignation">
  <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

  <?php
    $tab = (!empty($_GET['tab']))? esc_attr($_GET['tab']) : 'first';
    page_tabs($tab);

// --------------------------------- //
// FIRST TAB //
    if($tab == 'first' ) {
      if(!$json_array || $json_array == ""){
        echo "<div id='setting-error-settings_updated' class='error settings-error notice'> <p><strong>The server can't be reached, check the <a href='" . admin_url( 'options-general.php?page=' . $this->plugin_name .'&tab=second' ) . " ' target='blank'>" . __('link', $this->plugin_name) . "</a> to your JSON file.</strong></p></div>";
			} else{
		    $allSensors = json_decode($json_array);
    ?>
    <form id="ignation_form" method="post" name="ignation-iot_options" action="options.php">
        <?php
          settings_fields($this->plugin_name);
          do_settings_sections($this->plugin_name);

          //Grab all options
          $options = get_option($this->plugin_name);
        ?>
      <fieldset>
        <input id="tabSelector" name="<?php echo $this->plugin_name;?>[tabSelector]" value="tab1" style="display: none" />

        <div id="module_select" class="postbox seedprod-postbox">
          <h3 class="hndle">Add sensors</h3>
          <div class="inside">
            <table name="<?php echo $this->plugin_name;?>[module]" class="form-table">
              <tbody>
                <tr>
                  <th scope="row">
                    <p>Module</p>
                  </th>
                  <td>
                    <select id="moduleSelector" name="<?php echo $this->plugin_name;?>[module]">
                      <option value="null">Choose module</option>
                      <?php
                      foreach ($allSensors as $sen){
                        $_moduleid = $sen->id;
                        $_modulename = $sen->description;

                        if ($sensor_module_id == $_moduleid) {
                          $sensor_module_name = $_modulename;
                        }
                      ?>
                        <option value="<?php echo $_moduleid;?>"><?php echo $_modulename;?></option>
                      <?php } ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <p>Sensor</p>
                  </th>
                  <td>
                    <select id="sensorSelector" name="<?php echo $this->plugin_name ?>[module_sensors]">
                      <option value="null" selected="">Choose Sensor</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <?php submit_button('Add sensor', 'button-primary','submit', TRUE); ?>
        </div>
      </fieldset>
    </form>

    <h2>Activated sensors</h2>
    <table class="wp-list-table widefat fixed striped users selected_sensors">
    	<thead>
    		<tr>
    			<th scope="col" id="module" class="manage-column column-module">
    				Module
    			</th>
    			<th scope="col" id="sensor" class="manage-column column-sensor">
    				Sensor
    			</th>
    			<th scope="col" id="delete" class="manage-column column-delete">
    			</th>
    		</tr>
    	</thead>

    	<tbody>
        <?php
          if (count($options) > 0) {
            $module_count = 0;

            for ($i = 0; $i < count($options); $i++) {
              $module = $options[$i];

              if(isset($module['module'])){
                $module_count ++;
              }
            }

            if($module_count > 0){

              for ($i = 0; $i < count($options); $i++) {
                $module = $options[$i];

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
                  echo "<tr><td scope='col' class='module column-module'>". $sensor_module_name ."</td><td scope='col' class='sensor column-sensor'>". $sensor_sensor_id ."</td><td scope='col' class='delete column-delete'><a href='?page=".$_GET['page']."&action=removeSensor&moduleId=$sensor_module_id&sensorId=$sensor_sensor_id'>Delete</a></td></tr>";
                }
              }
            } else {
              echo "<tr><td class='module column-module'>No sensors selected</td><td class='sensor column-name'></td><td class='delete column-delete'></td></tr>";
            }
          } else {
            echo "<tr><td class='module column-module'>No sensors selected</td><td class='sensor column-name'></td><td class='delete column-delete'></td></tr>";
          }
        ?>
    	</tbody>
    </table>
      <?php
  			}

  // END FIRST TAB //
// --------------------------------- //



// --------------------------------- //
  // SECOND TAB //
      } elseif($tab == 'second' ) {
        if(!$json_array || $json_array == ""){
          echo "<div id='setting-error-settings_updated' class='error settings-error notice'> <p><strong>The server can't be reached, check the <a href='" . admin_url( 'options-general.php?page=' . $this->plugin_name .'&tab=second' ) . " ' target='blank'>" . __('link', $this->plugin_name) . "</a> to your JSON file.</strong></p></div>";
  			} else{
  		    $allSensors = json_decode($json_array);
        }
    ?>
    <form id="ignation_form" method="post" name="ignation-iot_options" action="options.php">
      <h2>Plugin settings</h2>

      <?php
        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);

        //Grab all options
        $options = get_option($this->plugin_name);

        if (count($options) > 0) {
          $exists_description = false;
          $exists_json = false;

          for ($i = 0; $i < count($options); $i++) {
            $module = $options[$i];

            if(array_key_exists('description', $module)){
              $exists_description = true;
            } if(array_key_exists('json', $module)){
              $exists_json = true;
            }
          }
        }
      ?>
        <fieldset>
          <input id="tabSelector" name="<?php echo $this->plugin_name;?>[tabSelector]" value="tab2" style="display: none" />

          <div id="settings-description" class="postbox seedprod-postbox">
            <h3 class="hndle">Widget description text</h3>
            <div class="inside">
              <p>The description text is visible above the widget on your website. This should contain an explanation about the widget and what people can do with it.</p>
              <?php
                if($exists_description == true){
                  for ($i = 0; $i < count($options); $i++) {
                    $module = $options[$i];

                    if(isset($module['description'])){
                      $ignation_description = $module['description'];
                    }
                  }
              ?>
                <textarea name='<?php echo $this->plugin_name ?>[description]' value='<?php echo $ignation_description; ?>'><?php echo $ignation_description; ?></textarea>
              <?php
                } else{
                  $ignation_description = "Put the description text here!";
              ?>
                  <textarea name='<?php echo $this->plugin_name ?>[description]' placeholder='<?php echo $ignation_description; ?>'></textarea>
              <?php
                }
              ?>
            </div>
          </div>
        </fieldset>

        <fieldset>
          <div id="settings-data-link" class="postbox seedprod-postbox">
            <h3 class="hndle">Link to JSON file</h3>
            <div class="inside">
              <p>The JSON url links all the modules and sensor to the plugin. Make sure this is correct, otherwise the plugin won't work! (It has to contain <strong>http://</strong> or <strong>https://</strong> and <strong>.json</strong>.)</p>
              <table class="form-table settings-type" style="margin-top: 10px;">
              	<tbody>
                  <tr>
                    <?php
                      if($exists_json == true){
                        for ($i = 0; $i < count($options); $i++) {
                          $module = $options[$i];

                          if(isset($module['json'])){
                            $ignation_json = $module['json'];
                          }
                        }
                    ?>
                      <td><input type="text" name="<?php echo $this->plugin_name ?>[jsonlink]" value="<?php echo $ignation_json; ?>"></td>
                    <?php
                      } else{
                        $ignation_json = "Put the JSON url here!";
                    ?>
                        <td><input type="text" name="<?php echo $this->plugin_name ?>[jsonlink]" placeholder="<?php echo $ignation_json; ?>"></td>
                    <?php
                      }
                    ?>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </fieldset>
        <?php submit_button('Save Changes', 'button-primary','submit', TRUE); ?>
      </form>

    <?php
      }
  // END SECOND TAB //
  // --------------------------------- //



  // --------------------------------- //
  // THIRD TAB //
      elseif($tab == 'third' ) {
        if(!$json_array || $json_array == ""){
          echo "<div id='setting-error-settings_updated' class='error settings-error notice'> <p><strong>The server can't be reached, check the <a href='" . admin_url( 'options-general.php?page=' . $this->plugin_name .'&tab=second' ) . " ' target='blank'>" . __('link', $this->plugin_name) . "</a> to your JSON file.</strong></p></div>";
  			} else{
  		    $allSensors = json_decode($json_array);
        }
    ?>
          <h2>About</h2>
          <p>Version: <?php echo $this->version; ?></p>
          <p>Created by: Daimy van Rhenen</p>
          <br />
          <p>Copyright &copy; 2017 <a href="https://ignation.io" target="_blank">Ignation</a> All Rights Reserved.</p>
    <?php
      }
  // END THIRD TAB //
  // --------------------------------- //
    ?>
  </div>
