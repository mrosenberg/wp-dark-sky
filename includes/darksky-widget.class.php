<?php

class DarkSky_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'DarkSky_Widget', // Base ID
			__( 'DarkSky Forecast', 'text_domain' ), // Name
			array( 'description' => __( 'A Weather Darkly', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		global $DarkSkies;

		$forecast    = $DarkSkies->forecast($instance['latitude'], $instance['longitude']);
		$icon        = $forecast['currently']['icon'];
		$temp        = $forecast['currently']['temperature'];
		$windSpeed   = $forecast['currently']['windSpeed'];
		$windBearing = $forecast['currently']['windBearing'];
		$visibility  = $forecast['currently']['visibility'];
		$pressure    = $forecast['currently']['pressure'];

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];	
		}	

		echo "<span class='dark-sky-metric dark-sky-temp'>Temp: {$temp}&#176;F</span>";
		echo "<span class='dark-sky-metric dark-sky-windspeed'>Windspeed: {$windSpeed} <span class='dark-sky-suffix'>mph</span></span>";
		echo "<span class='dark-sky-metric dark-sky-windbearing'>Direction: {$windBearing}&#176;</span>";
		echo "<span class='dark-sky-metric dark-sky-visibility'>Visibility: {$visibility} mi</span>";
		echo "<span class='dark-sky-metric dark-sky-pressure'>Pressure: {$pressure} mb</span>";
		echo "<canvas class='dark-sky-metric dark-sky-icon' data-icon='{$icon}' width='128' height='128'></canvas>";

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title     = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
		$latitude  = ! empty( $instance['latitude'] ) ? $instance['latitude'] : __( 'New latitude', 'text_domain' );
		$longitude = ! empty( $instance['longitude'] ) ? $instance['longitude'] : __( 'New longitude', 'text_domain' );

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'latitude' ); ?>"><?php _e( 'Latitude:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'latitude' ); ?>" name="<?php echo $this->get_field_name( 'latitude' ); ?>" type="text" value="<?php echo esc_attr( $latitude ); ?>">
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'longitude' ); ?>"><?php _e( 'longitude:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'longitude' ); ?>" name="<?php echo $this->get_field_name( 'longitude' ); ?>" type="text" value="<?php echo esc_attr( $longitude ); ?>">
		</p>		

		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['latitude'] = ( ! empty( $new_instance['latitude'] ) ) ? strip_tags( $new_instance['latitude'] ) : '';
		$instance['longitude'] = ( ! empty( $new_instance['longitude'] ) ) ? strip_tags( $new_instance['longitude'] ) : '';

		return $instance;
	}
}