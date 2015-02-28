<?php
class STWeather{
	function __construct(){
		/* 야후 
		//도시코드 가져오기 URL
		$this->get_citycode_url = "http://query.yahooapis.com/v1/public/yql?q=[query]&format=xml";
		$this->get_citycode_query = "select * from geo.places where text=\"[cityname]\"";
		//날씨 가져오기 URL 
		$this->get_weather_url = "http://weather.yahooapis.com/forecastrss?w=[citycode]&u=c ";
		*/
		//msn 에서 가져오기 
		$this->get_weather_url = "http://weather.service.msn.com/data.aspx?weadegreetype=C&culture=ko-KR&weasearchstr=";
		add_action('wp_head',array(&$this, 'register_script') );
		add_action('admin_print_scripts-widgets.php',array(&$this,'widget_scripts'));
		add_action('admin_footer',array(&$this,'admin_footer'));
		add_action('wp_ajax_nopriv_get_stweather_location', array(&$this, 'get_stweather_location'));
		add_action('wp_ajax_get_stweather_location', array(&$this, 'get_stweather_location'));
	}
	function get_stweather_location(){
		$nonce = $_POST['token'];
		//if (! wp_verify_nonce($nonce, 'st-token') ) die("비정상접근"); 저장구문 없으므로 주석
		
		$weather_data = array();
		//$this->get_citycode_query = str_replace('[cityname]',$_POST['location_name'],$this->get_citycode_query);
		//$this->get_citycode_url = str_replace('[query]',urlencode($this->get_citycode_query),$this->get_citycode_url);
		//$result1 = file_get_contents($this->get_citycode_url);
		
		//echo $this->get_citycode_url;
		
		$this->get_weather_url .= urlencode($_POST['location_name']);
		$citycode_xml = wp_remote_get($this->get_weather_url);
		$xml = simplexml_load_string($citycode_xml['body']);
		$success = false;
		//날씨정보가 있을 경우
		if(isset($xml->weather)){
			$weather = $xml->weather;
			$weather = (array)$weather;
		
			$attributes = $weather['@attributes'];
			$weather_data['location_name'] = $attributes['weatherlocationname'];
			$current = (array)$weather['current'];
			$current = $current['@attributes'];

			//현재 날씨 정보
			$current_data = array();
			$current_data['temperature']	= $current['temperature'];
			$current_data['skytext']		= $current['skytext'];

			//주간날씨 정보
			$forecast_data = array();
			foreach($weather['forecast'] as $fore){
				$temp = array();
				$fore = (array)$fore;
				$fore = $fore['@attributes'];
				$temp = $fore;
				$forecast_data[] = $temp;
			}
			$success = true;
		}
		echo json_encode(array(
			'success'=>$success,
			'weather_data'=>$weather_data,
			'current_data'=>$current_data,
			'forecast_data'=>$forecast_data));


		die();
	}
	function register_script(){
		wp_register_script('stweather-script', plugins_url('/js/stweather.js', __FILE__),'jquery','0.1' );
		wp_enqueue_script('stweather-script');
		$stweather_localize = array(
				'ajaxurl'	=> admin_url('admin-ajax.php')
			);
		wp_localize_script( 'stweather-script', 'stweather', $stweather_localize );
	}
	function widget_scripts(){
		$this->widget_scripts = true;
	}
	function admin_footer(){
		if($this->widget_scripts){
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			$('button.stweather_location_search').bind('click',function(){
				var previewDiv = $(this).parent().parent().find('.weather_preview');
				var location_name = $(this).parent().find('input.stweather_location').val();
				var token = $(this).parent().find('input[name="token"]').val();
				if(location_name == ''){
					alert('<?php _e('지역을 입력해주세요.','hotpack');?>');
					$(this).parent().find('input.stweather_location').focus();
					return false;
				}
				$.post(
						'<?php echo admin_url('admin-ajax.php');?>',
						{
							action:'get_stweather_location',
							token:token,
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
				return false;
			});
		});
		</script>
		<?php
		}
	}
}