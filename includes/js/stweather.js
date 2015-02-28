jQuery(document).ready(function($){
	$('.stweather_widget').each(function(){
		var location_name = $(this).find('input[name="cityname"]').val();
		var previewDiv = $(this).find('div.st_weather_widget');
		$.post(
			stweather.ajaxurl,
			{
				action:'get_stweather_location',
				location_name:location_name
			},
			function(res){
				if(res.success){
					
					html = "<h3>"+res.weather_data.location_name+"</h3>";
					html += "<div class='current'>현재:"+res.current_data.skytext+"("+res.current_data.temperature+"℃)</div>";
					html += "<div class='forecast'><ul>";
					for(var i = 0; i<res.forecast_data.length; i++){
						forecast = res.forecast_data[i];
						html += "<li>"+forecast.day+":"+forecast.skytextday+"("+forecast.high+"℃/"+forecast.low+"℃)</li>";
					}
					html += "</ul></div>";
					previewDiv.html(html);
				}
			},
			'json'
		);
	});
});