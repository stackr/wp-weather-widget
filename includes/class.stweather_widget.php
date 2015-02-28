<?php
class STWeather_Widget extends WP_Widget {
	public function STWeather_Widget(){

		$this->defaults['title'] = __('날씨 위젯','hotpress');
		
		$widget_ops = array('classname'=>'stweather_widget','description'=>__('사이드바에 날씨정보를 보여줍니다.','hotpress'));

		// widget control settings
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'stweather_widget' );

		// create the widget
		parent::__construct('stweather_widget',$this->defaults['title'],$widget_ops,$control_ops);
	}
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( '날씨정보', 'hotpress' );
		}
		$location = $instance['location'];
		$nonce = wp_create_nonce('st-token');
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'location' ); ?>"><?php _e( '지역설정:','hotpack' ); ?></label> 
		<input class="widefat stweather_location" id="<?php echo $this->get_field_id( 'location' ); ?>" name="<?php echo $this->get_field_name( 'location' ); ?>" type="text" value="<?php echo esc_attr( $location ); ?>" />
		<button type="submit" class="stweather_location_search"><?php _e('Search','hotpack');?></button>
		<input type="hidden" name="token" value="<?php echo $nonce;?>"/>
		</p>
		<div class="weather_preview">

		</div>
		<?php
	}
	public function widget( $args, $instance ){
		global $stweather;
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		?>
		<input type="hidden" name="cityname" value="<?php echo $instance['location'];?>"/>
		<div class="st_weather_widget">
			<p><?php _e('날씨를 가져오는 중입니다.','hotpack');?></p>
		</div>
		<?php
		echo $after_widget;
	}
}
?>