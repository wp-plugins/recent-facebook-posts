<?php

class RFB {

	private static $instance;
	private static $fb_instance;
	private $defaults = array(
		'app_id' => '',
		'app_secret' => '',
		'fb_id' => 'DannyvanKootenCOM',
		'cache_time' => 1800,
		'load_css' => 0,
		'link_text' => 'Find us on Facebook'
	);
	private $options;

	public static function get_instance() {
		if(!self::$instance) self::$instance = new RFB();
		return self::$instance;
	}

	public function __construct() {
		add_action('wp_login', array($this, 'renew_access_token'));
		add_action('init', array(&$this, 'on_init'));

		// only on frontend
		if(!is_admin()) {
			$opts = $this->get_options();
			if($opts['load_css']) {
				add_action( 'wp_enqueue_scripts', array(&$this, 'load_css'));
			}
		}
	}

	public function on_init() {
		if(!session_id() && !headers_sent()) {
			session_start();
		}
	}

	public function load_css() {
		wp_register_style('rfb_css', plugins_url('recent-facebook-posts/css/rfb.css') );
        wp_enqueue_style('rfb_css' );
	}

	public function get_options() {
		if(!$this->options) {
			$this->options = array_merge($this->defaults, (array) get_option('rfb_settings'));
		}

		return $this->options;
	}

	public function renew_access_token() {
		// only run if this is an administrator.
		$user = wp_get_current_user();
		
		if(!user_can($user, 'manage_options')) return false;

		$fb = $this->get_fb_instance();

		// Get Facebook User ID
		$fb_user = $fb->getUser();

		if(!$fb_user) {

			if(!headers_sent()) {
		    	header("Location: ".$fb->getLoginUrl(array('redirect_uri' => get_admin_url() . '?rfb_renew_access_token')));
		    	exit;
			} else {
				echo '<script type="text/javascript">window.location = \''. $fb->getLoginUrl(array('redirect_uri' => get_admin_url() . '?rfb_renew_access_token')) .'\';</script>';
				exit;
			}
		}

		return;
	}

	public function get_fb_instance() {
		if(!self::$fb_instance) {
			require dirname(__FILE__) . '/facebook-php-sdk/facebook.php';
			$opts = $this->get_options();
			self::$fb_instance = new Facebook(array(
		   	 	'appId'  => $opts['app_id'],
		    	'secret' => $opts['app_secret'],
			));
		}
		return self::$fb_instance;
	}

	public function get_posts() {
		$cache_file = dirname(__FILE__) . '/../cache/posts.cache';
		$opts = $this->get_options();

		// check if cache file exists 
		// check if cache file is exists for longer then the given expiration time
		if(!file_exists($cache_file) || (filemtime($cache_file) < (time() - $opts['cache_time']))) {
			$this->renew_cache_file();

			if(!file_exists($cache_file)) return array();
		}

		$posts = json_decode(file_get_contents($cache_file), true);
		return $posts;
	}

	public function renew_cache_file() {
		$opts = $this->get_options();
		if(empty($opts['app_id']) || empty($opts['fb_id'])) return false;

		$access_token = get_option('rfb_access_token'); 
		if(!$access_token) {

			$access_token = $this->renew_access_token();
			if(!$access_token) return false;
		}

		$fb = $this->get_fb_instance();
		$fb->setAccessToken($access_token);

		if(!$fb->getUser()) return false;

		$apiResult = $fb->api($opts['fb_id'].'/posts', "GET", array(
				'limit' => 250
			)
		);
		
		if(!$apiResult or !is_array($apiResult) or !isset($apiResult['data']) or !is_array($apiResult['data'])) { return false; }

		$data = array();
		foreach($apiResult['data'] as $p) {
			if(!isset($p['message']) || empty($p['message'])) continue;

			//split user and post ID (userID_postID)
			$idArray = explode("_", $p['id']);
			
			$post = array();
			$post['author'] = $p['from'];
			$post['content'] = $p['message'];
			

			if($p['type'] == 'photo') { 
				$post['image'] = $p['picture'];
			} else {
				$post['image'] = null;
			}

			$post['timestamp'] = strtotime($p['created_time']);
			$post['like_count'] = (isset($p['likes'])) ? $p['likes']['count'] : 0;
			$post['comment_count'] = (isset($p['comments'])) ? $p['comments']['count'] : 0;
			$post['link'] = "http://www.facebook.com/".$opts['fb_id']."/posts/".$idArray[1];
			$data[] = $post;

		}

		$data = json_encode($data);
		$cache_file = dirname(__FILE__) . '/../cache/posts.cache';
		
		if(!is_writable(dirname(__FILE__) . '/../cache/')) return false;

		file_put_contents($cache_file, $data);

		return true;
	}	
	
}