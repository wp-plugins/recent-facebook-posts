<?php

function rfbp_get_settings() {
	static $settings;

	if ( !$settings ) {

		$defaults = array(
			'app_id' => '',
			'app_secret' => '',
			'fb_id' => '',
			'load_css' => 1,
			'page_link_text' => 'Find us on Facebook',
			'link_new_window' => 0,
			'img_size' => 'normal',
			'img_width' => '',
			'img_height' => ''
		);

		// get user options
		$options = get_option( 'rfb_settings' );

		// options did not exist yet, add option to database
		if ( !$options ) { add_option( 'rfb_settings', $defaults ); }

		$settings = array_merge( $defaults, (array) $options );
	}

	return $settings;
}

function rfbp_register_widget() {
	include_once RFBP_PLUGIN_DIR . 'includes/class-widget.php';
	register_widget( "RFBP_Widget" );
}

add_action('widgets_init', 'rfbp_register_widget');

function rfbp_load_textdomain() {
	load_plugin_textdomain( 'recent-facebook-posts', false, 'recent-facebook-posts/languages/' );
}

add_action('plugins_loaded', 'rfbp_load_textdomain');

function rfbp_get_class() {
	static $class;

	if(!$class) {
		require_once RFBP_PLUGIN_DIR . 'includes/class-public.php';
		$class = new RFBP_Public();
	}

	return $class;
}

function rfbp_get_api() {
	static $api;

	if(!$api) {
		$opts = rfbp_get_settings();
		require_once RFBP_PLUGIN_DIR . 'includes/class-api.php';
		$api = new RFBP_API( $opts['app_id'], $opts['app_secret'], $opts['fb_id'] );
	}

	return $api;
}

function rfbp_valid_config() {
	$opts = rfbp_get_settings();

	return (!empty($opts['fb_id']) && !empty($opts['app_id']) && !empty($opts['app_secret']) );
}