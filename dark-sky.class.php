<?php 

/** 
* Plugin Name: Dark Sky 
* Description: Get the latest weather. 
* Version: 0.1.0 
* Author: Matthew Rosenberg 
* Author URI: http://matthewrosenberg.com
* License: GPL2 
*/


class DarkSkies {

	const API_KEY = '0ee3b4a4c6b52530fa0cb0cfa0bee9ea';

	const TRANSIENT = 'darksky-forecast';

	private $plugin_dir;

	private $plugin_url;

	private $api;


	public function __construct() {

		/**
		 ** Setup Paths
		 ** http://codex.wordpress.org/Determining_Plugin_and_Content_Directories
		**/ 
		$this->plugin_dir = plugin_dir_path( __FILE__ );
		$this->plugin_url = plugin_dir_url( __FILE__ );

		// Include our externals.
		require_once $this->plugin_dir . 'includes/darksky.class.php';
		require_once $this->plugin_dir . 'includes/darksky-widget.class.php';

		// Instatiate our API
		$this->api = new DarkSky(self::API_KEY);

		// Add our WordPress hooks
		add_action( 'widgets_init', array($this, 'register_widget') );
		add_action( 'wp_enqueue_scripts', array($this, 'setup_frontend') );


	}

	public function setup_frontend() {
		if( !is_admin() ) {

			wp_register_script('dark-sky-js', $this->plugin_url . 'js/darksky.js', 'jquery', '1.0', true);
			wp_enqueue_script('dark-sky-js');	

			wp_register_style('dark-sky-css', $this->plugin_url . 'css/style.css', null, '1.0', false);
			wp_enqueue_style('dark-sky-css');						
		}
	}


	// Transient API http://codex.wordpress.org/Transients_API
	public function forecast($lat, $long) {
		$transient = SELF::TRANSIENT;

		// Check if our data has been cached
		if ( false === ( $forecast = get_transient( $transient ) ) ) {

		  // It wasn't there, so regenerate the data and save the transient
		  $forecast = $this->api->getConditions($lat, $long);

		  // Let's now save the new data
		  set_transient( $transient, $forecast, 60 * 60 );
		}		

		return $forecast;

	}


	public function register_widget() {
		register_widget( 'DarkSky_Widget' );
	}
}

// Make our class a GLOBAL object;
$DarkSkies = new DarkSkies;
