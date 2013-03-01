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
		'link_text' => 'Find us on Facebook',
		'link_new_window' => 0
	);
	private $options;

	public static function get_instance() {
		if(!self::$instance) self::$instance = new RFB();
		return self::$instance;
	}

	public function __construct() {

		add_action('wp_login', array($this, 'renew_access_token'));
		add_action('init', array($this, 'on_init'));

		add_shortcode('recent-facebook-posts', array($this, 'shortcode_output'));

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
		   	 	'appId'  => trim($opts['app_id']),
		    	'secret' => trim($opts['app_secret']),
			));
		}
		return self::$fb_instance;
	}

	public function get_posts() {
		$cache_file = dirname(__FILE__) . '/../cache/posts.cache';
		$opts = $this->get_options();

		// check if cache file exists 
		// check if cache file exists for longer then the given expiration time
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

		$apiResult = $fb->api(trim($opts['fb_id']) . '/feed?with=message&limit=250');
		
		if(!$apiResult or !is_array($apiResult) or !isset($apiResult['data']) or !is_array($apiResult['data'])) { return false; }

		$data = array();
		foreach($apiResult['data'] as $p) {
			if(!in_array($p['type'], array('status', 'photo', 'video'))) { continue; }
			//split user and post ID (userID_postID)
			$idArray = explode("_", $p['id']);
			
			$post = array();
			$post['author'] = $p['from'];
			$post['content'] = isset($p['message']) ? $p['message'] : '';
			
			if($p['type'] == 'photo') { 
				$post['image'] = $p['picture'];
			} elseif($p['type'] == 'video') {
				$post['image'] = $p['picture'];
				$post['content'] .= "\n\n {$p['link']}";
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
		$cache_dir = dirname(__FILE__) . '/../cache/';
		$cache_file = dirname(__FILE__) . '/../cache/posts.cache';
		
		if(!file_exists($cache_dir)) {
			@mkdir($cache_dir, 0777);
		}

		if(!is_writable(dirname(__FILE__) . '/../cache/')) {
			return false;
		}

		file_put_contents($cache_file, $data);

		return true;
	}

	public function shortcode_output($atts)
	{
		 extract(shortcode_atts(array(
	      'number' => '5',
	      'likes' => 1,
	      'comments' => 1,
	      'excerpt_length' => 140
    	 ), $atts));

		$posts = $this->get_posts();
		$posts = array_slice($posts, 0, $number);

		$output = '<div class="recent-facebook-posts rfb_posts shortcode">';
		foreach($posts as $post) { 
			$content = $post['content'];
			$shortened = false;

			if(strlen($content) > $excerpt_length) {
				$limit = strpos($post['content'], ' ',$excerpt_length); 
				if($limit) {
					$content = substr($post['content'], 0, $limit);
					$shortened = true;
				}
			}
		
		
			$output .= '<div class="rfb-post">';
			$output .= '<p class="rfb_text">'. nl2br(make_clickable($content));
				if ($shortened) $output .= '..';
			$output .= '</p>';
			if(isset($post['image']) && $post['image']) { 
				$output .= '<p class="rfb_image"><a target="_blank" href="'. $post['link'] . '" rel="nofollow"><img src="'. $post['image'] . '" alt="" /></a></p>';
			}
			$output .= '<p><a target="_blank" class="rfb_link" href="'. $post['link'] .'" rel="nofollow">';
			if($likes || $comments) { $output .= '<span class="like_count_and_comment_count">'; }
			if($likes) { $output .= '<span class="like_count">'. $post['like_count'] . ' <span>likes</span></span> '; }
			if($comments) { $output .= '<span class="comment_count">' . $post['comment_count'] . ' <span>comments</span></span> '; }
			if($likes || $comments) { $output .= '</span>'; }
			$output .= '<span class="timestamp" title="'. date('l, F j, Y', $post['timestamp']) . ' at ' . date('G:i', $post['timestamp']) . '" >';
			if($likes || $comments) { $output .= ' · '; }
			$output .= '<span>' . $this->time_ago($post['timestamp']) . '</span></span>';
			$output .= '</a></p></div>' ;
		
		} 

		if(empty($posts)) {
			$output .= '<p>No recent Facebook status updates to show.</p>';
			if(current_user_can('manage_options')) { 
				$output .= '<p><strong>Admins only notice:</strong> Did you <a href="' . get_admin_url(null,'options-general.php?page=rfb-settings') . '">configure the plugin</a> properly?</p>';
			}
		}

		$output .= "</div>";
		return $output;
	}

	public function time_ago($timestamp) {
		$diff = time() - (int) $timestamp;

	    if ($diff == 0) 
	         return 'just now';

	    $intervals = array
	    (
	        1                   => array('year',    31556926),
	        $diff < 31556926    => array('month',   2628000),
	        $diff < 2629744     => array('week',    604800),
	        $diff < 604800      => array('day',     86400),
	        $diff < 86400       => array('hour',    3600),
	        $diff < 3600        => array('minute',  60),
	        $diff < 60          => array('second',  1)
	    );

	    $value = floor($diff/$intervals[1][1]);
	    return $value.' '.$intervals[1][0].($value > 1 ? 's' : '').' ago';
		    
	}	
	
}