(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source.
	 */

		 $(document).ready(function() {
			setInterval(function(){
				$("#ignation-iot-front-container").load(location.href + " #ignation-iot-front-container");
			}, 2000);
		});

		 $(window).resize(function() {
			$('.ignation-iot-front-column').each(function(){
				var column_height = $(this).height();
				var column_i_height = $(this).find("i").height();

				var column_i_padding = (column_height - column_i_height) / 2;

				$(this).find(".column-right").css("padding", column_i_padding);
			});
		 });
})( jQuery );
