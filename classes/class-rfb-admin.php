<?php

class RFB_Admin {

	private $RFB;
	private $options;
	
	public function __construct($RFB) {

		$this->RFB = $RFB;

		add_action('admin_init', array($this, 'register_settings'));
		add_action('admin_menu', array($this, 'build_menu'));
		
		add_filter("plugin_action_links_recent-facebook-posts/recent-facebook-posts.php", array($this, 'add_settings_link'));

		// check expiry date of access token
		$expiryDate = get_option('rfb_access_token_expiry_date');
		if($expiryDate && (date('Ymd', strtotime("+14 days")) >= $expiryDate)) {
			// access token expires in less than 7 days
			// add admin notice to request new access token
			add_action( 'admin_notices', array($this, 'show_admin_notice') );
		}

		if(isset($_GET['yolo'])) {
			update_option('rfb_access_token_expiry_date', date('Ymd', strtotime('+7 days')));
		}

		// handle requests early
		if(isset($_GET['page']) && $_GET['page'] == 'rfb-settings') {

			$this->options = $RFB->get_options();

			add_action('admin_enqueue_scripts', array($this, 'load_css') );

			// renew cache file
			if(isset($_POST['renew_cache'])) {
				add_action('init', array($this->RFB, 'renew_cache_file'));
			}

			// login to facebook
			if(isset($_GET['login_to_fb'])) {
				$fb = $this->getFB();
				$loginUrl = $fb->getLoginUrl(array('scope' => array('read_stream'), 'redirect_uri' => get_admin_url(null, 'admin.php?page=rfb-settings&logged_in')));
				
				// check if headers have beent sent, otherwise redirect via JS
				if(!headers_sent()) {
					header("Location: {$loginUrl}");
					exit;
				} else {
					?>
						<script type="text/javascript">
							window.location.href = "<?php echo $loginUrl; ?>";
						</script>
						<noscript>
							<meta http-equiv="refresh" content="0;url=<?php echo $loginUrl; ?>" />
						</noscript>
					<?php
				}
			}
		}
	}

	public function register_settings() {
		register_setting('rfb_settings_group', 'rfb_settings');
	}

	public function build_menu() {
		$page = add_menu_page('Recent Facebook Posts - Settings','Recent FB Posts','manage_options','rfb-settings', array($this, 'settings_page'), plugins_url('recent-facebook-posts/img/icon.png'));
	}

	public function load_css() {
		wp_enqueue_style( 'rfb_admin_css', plugins_url('recent-facebook-posts/css/admin.css') );
		wp_enqueue_script( 'rfb_admin_js', plugins_url('recent-facebook-posts/js/admin.js'), array('jquery'), null, true);
	}

	public function  settings_page () {

		$opts = $this->options;
		$curl = extension_loaded('curl');
		$connected = false;

		if($curl) {

			$fb = $this->getFB();
			$connected = $fb->getUser();

			if($connected) {
				try {
					$try = $fb->api('/me');
				} catch(Exception $e) {
					$connected = false;
					$apiError = $e;
				}
			}

		}

		// show user-friendly error message
		if(!$curl) { $errorMessage = "This plugin needs the PHP cURL extension installed on your server. Please ask your webhost to enable the php_curl extension."; }
		elseif(empty($opts['app_id'])) { $errorMessage = "This plugin needs a valid Application ID to work. Please fill it in below."; }
		elseif(empty($opts['app_secret'])) { $errorMessage = "This plugin needs a valid Application Secret to work. Please fill it in below."; }
		elseif(!$connected) { 
			$errorMessage = "The plugin is not connected to Facebook. Please <a href=\"". admin_url('admin.php?page=rfb-settings&login_to_fb') ."\">connect</a>."; 
		} else {
			// everything is fine!
			$accessToken = $fb->getAccessToken();
			update_option('rfb_access_token', $accessToken);

			if(isset($_GET['logged_in'])) { 
				update_option('rfb_access_token_expiry_date', date("Ymd", strtotime("+60 days")));
				$notice = "<strong>Login success!</strong> You succesfully connected the plugin with Facebook.";
			} elseif($this->getRFB()->cache_renewed) { $notice = "<strong>Cache renewed!</strong> You succesfully renewed the cache."; }
		}


		// check if cache directory is writable
		$cacheDir = WP_CONTENT_DIR;
		$cacheFile = WP_CONTENT_DIR . '/recent-facebook-posts.cache';

		if(!is_writable($cacheDir)) {
			$cacheError = 'The wp-content folder (<i>'. WP_CONTENT_DIR .'</i>) is not writable. Please set the folder permissions to 755.';
		} elseif(file_exists($cacheFile) && !is_writable($cacheFile)) {
			$cacheError = 'The cache file (<i>'. $cacheFile .'</i>) exists but is not writable. Please set the file permissions to 755.';
		}
		
		require dirname(__FILE__) . '/../views/settings_page.html.php';
	}

	public function getRFB()
	{
		return $this->RFB;
	}

	private function getFB()
	{
		return $this->getRFB()->getFBApi();
	}

	public function show_admin_notice()
	{
		?>
	    <div class="updated">
	        <p>Your Facebook access token for <a href="<?php echo admin_url('admin.php?page-rfb-settings'); ?>">Recent Facebook Posts</a> expires in less than 14 days. Please renew it. <a class="primary-button" href="<?php echo admin_url('admin.php?page=rfb-settings&login_to_fb'); ?>">Renew token</a></p>
	    </div>
	    <?php
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