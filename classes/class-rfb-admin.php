<?php

class RFB_Admin {
	
	public function __construct($RFB) {
		global $pagenow;

		add_action('admin_init', array(&$this, 'register_settings'));
		add_action('admin_menu', array(&$this, 'build_menu'));

		add_filter("plugin_action_links_recent-facebook-posts/recent-facebook-posts.php", array(&$this, 'add_settings_link'));

		$this->RFB = $RFB;

		if(isset($_GET['rfb_renew_access_token'])) {
			$this->renew_access_token();
		}

		if(isset($_GET['page']) && $_GET['page'] == 'rfb-settings') {
			$this->handle_requests();
		}
	}

	private function renew_access_token() {
		$fb = $this->RFB->get_fb_instance();

		// Get Facebook User ID
		$user = $fb->getUser();

		if($user) {
			$access_token = $fb->getAccessToken();
		  	// store access token for later usage
		   	update_option('rfb_access_token', $access_token);
		}
	}

	public function register_settings() {
		register_setting('rfb_settings_group', 'rfb_settings');
	}

	public function build_menu() {
		$page = add_options_page('Recent Facebook Posts - Settings','Recent Facebook Posts','manage_options','rfb-settings',array($this, 'settings_page'));
		add_action( 'admin_print_styles-' . $page, array(&$this, 'load_css') );
	}

	public function load_css() {
		wp_enqueue_style( 'rfb_admin_css', plugins_url('recent-facebook-posts/css/admin.css') );
	}

	public function  settings_page () {

		$opts = $this->RFB->get_options();
		$curl = extension_loaded('curl');
		$fb = $this->RFB->get_fb_instance();
		//update_option('rfb_access_token', '');
		$access_token = get_option('rfb_access_token');
		$connected = false;

		// try to fetch a test post
		if($curl && $access_token) {
			$fb->setAccessToken($access_token);

			$connected = $fb->getUser();

			if($connected) {
				try {
					$try = $fb->api('/' . $opts['fb_id'] . '/posts?limit=1');
				} catch(Exception $e) {
					$connected = false;
					$error = $e;
				}
			}
		}

		// check if cache directory is writable
		$cache_dir = dirname(__FILE__) . '/../cache/';
		$cache_file = dirname(__FILE__) . '/../cache/posts.cache';

		if(!file_exists($cache_dir)) {
			$cache_error = 'The cache directory (<i>'. ABSPATH . 'wp-content/plugins/recent-facebook-posts/cache/</i>) does not exist.';
		} elseif(!is_writable($cache_dir)) {
			$cache_error = 'The cache directory (<i>'. ABSPATH . 'wp-content/plugins/recent-facebook-posts/cache/</i>) is not writable.';
		} elseif(file_exists($cache_file) && !is_writable($cache_file)) {
			$cache_error = 'The cache file (<i>'. ABSPATH . 'wp-content/plugins/recent-facebook-posts/cache/posts.cache</i>) exists but is not writable.';
		}
		

		require dirname(__FILE__) . '/../views/settings_page.html.php';
	}

	public function handle_requests() {
		if(isset($_POST['renew_cache'])) {
			add_action('init', array($this->RFB, 'renew_cache_file'));
		}
	}


    /**
    * Adds the settings link on the plugin's overview page
    * @param array $links Array containing all the settings links for the various plugins.
    * @return array The new array containing all the settings links
    */
    function add_settings_link($links) {
        $settings_link = '<a href="options-general.php?page=rfb-settings">Settings</a>';
        array_unshift($links, $settings_link);
        
        return $links;
    }
}