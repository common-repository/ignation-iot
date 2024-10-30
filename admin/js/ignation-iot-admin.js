(function( $ ) {
	'use strict';

	 $(document).ready(function() {
			$("#moduleSelector").change(function() {
				loadSensorsForModule($(this).val());
			});
		});

		$( window ).resize(function() {
			var container_width = $('body').find('.icon-picker-container').width() - 15;
			$('.icon-picker-container').find('.icon-picker-control').css("width", container_width);
 		});

	 function loadSensorsForModule(moduleId) {
		 //loop over 'modules
		 $('#sensorSelector')[0].options.length = 0;
		 $('#sensorSelector').append(new Option("Choose Sensor", null, false, false));

		 for (var i = 0; i < modules.length; i++){

				//check if moduleId == id
				if (modules[i].id === moduleId) {
					var module_content = modules[i].sensors;

					for (var x = 0; x < module_content.length; x++){

						//Sensorsid
							var sensor_id = module_content[x].id;
							var sensor_description = module_content[x].description;

						//Id's toevoegen aan #sensorSelector
							$('#sensorSelector').append(new Option(sensor_id, sensor_id, false, false));
					}
				}
			}
	 }

})( jQuery );
