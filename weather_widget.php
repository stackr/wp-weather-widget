<?php
/*
 * Plugin Name: 날씨 위젯
 * Plugin URI: http://wordpress.org/extend/plugins/stackrpack/
 * Description: 사이트에 날씨 위젯을 사용 할 수 있도록 도와줍니다.
 * Author: Stackr Inc.
 * Version: 1.0
 * Author URI: http://stackr.co.kr
 * License: GPL2+
 * Text Domain: stackrpack
 * Domain Path: /languages/
 */

require_once(dirname(__FILE__).'/includes/class.stweather.php');
require_once(dirname(__FILE__).'/includes/class.stweather_widget.php');
if(class_exists('STWeather')){
	global $stweather;
	$stweather = new STWeather();
}
add_action('widgets_init', 'stweather_widget_init');

function stweather_widget_init() {
	register_widget('STWeather_Widget');
}
?>