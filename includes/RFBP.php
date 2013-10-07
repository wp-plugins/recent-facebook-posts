<?php

class RFBP {

	private static $instance = null;
	private static $api = null;
	private $options;
	public $cache_renewed = false;

	public static function instance() {
		return self::$instance;
	}

	public function __construct() {

		self::$instance = $this;

		// both frontend and backend
		include_once RFBP_PLUGIN_DIR . 'includes/RFBP_Widget.php';
		add_action( 'widgets_init', array($this, 'register_widget') );

		// finish if this is an AJAX request
		if(defined("DOING_AJAX") && DOING_AJAX) { return; }

		// starts sessions, fixes app token problem
		if(!session_id() && !headers_sent()) {
			session_start();
		}

		$opts = $this->get_settings();

		// only on frontend
		if(!is_admin()) {

			include_once RFBP_PLUGIN_DIR . 'includes/helper-functions.php';
			include_once RFBP_PLUGIN_DIR . 'includes/template-functions.php';

			add_shortcode('recent-facebook-posts', array($this, 'shortcode_output'));
			
			if($opts['load_css']) {
				add_action( 'wp_enqueue_scripts', array($this, 'load_css'));
			}

		} else {
			// only in admin panel
			include_once RFBP_PLUGIN_DIR . 'includes/RFBP_Admin.php';
			new RFBP_Admin();
		}
	}

	public function register_widget()
	{
		register_widget( "RFBP_Widget" );
	}

	public function load_css() {
		wp_register_style('recent-facebook-posts-css', plugins_url('recent-facebook-posts/assets/css/default.css') );
		wp_enqueue_style('recent-facebook-posts-css' );
	}

	public function get_settings() {
		if(!$this->options) {

			$defaults = array(
				'app_id' => '',
				'app_secret' => '',
				'fb_id' => 'DannyvanKootenCOM',
				'cache_time' => 1800,
				'load_css' => 0,
				'link_text' => 'Find us on Facebook',
				'link_new_window' => 0,
				'img_size' => 'thumbnail',
				'img_width' => 100,
				'img_height' => 100
				);

			// get user options
			$options = get_option('rfb_settings');

			// options did not exist yet, add option to database
			if(!$options) { add_option('rfb_settings', $defaults); }

			$this->options = array_merge($defaults, (array) $options);
		}

		return $this->options;
	}

	static public function api()
	{
		if(!self::$api) {

			if(!class_exists("Facebook")) {
				require_once RFBP_PLUGIN_DIR . 'includes/facebook-php-sdk/facebook.php';
			}

			$opts = RFBP::instance()->get_settings();
			self::$api = new Facebook(array(
				'appId'  => trim($opts['app_id']),
				'secret' => trim($opts['app_secret']),
			));
		}

		return self::$api;
	}

	public function get_posts() {
		
		$opts = $this->get_settings();

		// try to get posts from cache
		if(($posts = $this->get_cached_posts())) {
			return $posts;
		}

		if(empty($opts['app_id']) || empty($opts['fb_id'])) { return array(); }

		$accessToken = get_option('rfb_access_token'); 

		// if no access token has been stored, we can't make the API call.
		if(!$accessToken) { return array(); }

		$fb = self::api();
		$fb->setAccessToken($accessToken);

		// check if Facebook API has an identified user
		// if not, API is not connected
		if(!$fb->getUser()) { return array(); }

		$apiResult = $fb->api(trim($opts['fb_id']) . '/posts?fields=id,picture,type,from,message,status_type,object_id,link,created_time,comments.limit(1).summary(true),likes.limit(1).summary(true)');
		
		if(!$apiResult or !is_array($apiResult) or !isset($apiResult['data']) or !is_array($apiResult['data'])) { return array(); }

		$posts = array();
		foreach($apiResult['data'] as $p) {

			// skip this "post" if it is not of one of the following types
			if(!in_array($p['type'], array('status', 'photo', 'video', 'link'))) { 
				continue;
			}

			// skip empty status updates
			if($p['type'] == 'status' && (!isset($p['message']) || empty($p['message']))) { continue; }
			if($p['type'] == 'status' && $p['status_type'] == 'approved_friend') { continue; }

			//var_dump($p); echo '<br /><br />';

			//split user and post ID (userID_postID)
			$idArray = explode("_", $p['id']);

			$post = array();
			$post['type'] = $p['type'];
			$post['author'] = $p['from'];
			$post['content'] = isset($p['message']) ? $p['message'] : '';
			$post['image'] = null;

			// set post content and image
			if($p['type'] == 'photo') {

				$image = "//graph.facebook.com/". $p['object_id'] . '/picture?type=' . $opts['img_size'];
				$post['image'] = $image;

			} elseif($p['type'] == 'video') {

				$image = $p['picture'];

				if($opts['img_size'] == 'normal') {
					$image = str_replace(array("_s.jpg", "_s.png"), array("_n.jpg", "_n.png"), $image);
				} 

				$post['image'] = $image;

			} elseif($p['type'] == 'link') {
				$post['content'] .= "\n\n" . $p['link'];
			}

			// calculate post like and comment counts
			if(isset($p['likes']['summary']['total_count'])) {
				$like_count = $p['likes']['summary']['total_count'];
			} else {
				$like_count = 0;
			}

			if(isset($p['comments']['summary']['total_count'])) {
				$comment_count = $p['comments']['summary']['total_count'];
			} else {
				$comment_count = 0;
			}

			$post['timestamp'] = strtotime($p['created_time']);
			$post['like_count'] = $like_count;
			$post['comment_count'] = $comment_count;
			$post['link'] = "http://www.facebook.com/".$opts['fb_id']."/posts/".$idArray[1];
			$posts[] = $post;

		}

		// store results in cache for later use
		$this->set_cached_posts($posts);
		
		return $posts;
	}

	public function invalidate_cache()
	{
		$opts = $this->get_settings();
		$cache_file = WP_CONTENT_DIR . '/recent-facebook-posts.cache';
		$time = time() - ($opts['cache_time'] * 2);
		return touch($cache_file, $time);
	}

	private function get_cached_posts()
	{
		$opts = $this->get_settings();
		$cache_file = WP_CONTENT_DIR . '/recent-facebook-posts.cache';
		if(!file_exists($cache_file) || ($this->get_time_of_last_file_change($cache_file) < (time() - $opts['cache_time']))) {
			return false;
		}

		// by now, cache file exists.
		$posts = json_decode(file_get_contents($cache_file), true);
		return $posts;
		
	}

	private function set_cached_posts($posts)
	{
		$data = json_encode($posts);
		$cache_dir = WP_CONTENT_DIR . '/';
		$cache_file = WP_CONTENT_DIR . '/recent-facebook-posts.cache';
		
		// abandon if cache folder is not writable
		if(!is_writable(WP_CONTENT_DIR)) {
			return false;
		}

		file_put_contents($cache_file, $data);
		$this->cache_renewed = true;
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

		$opts = $this->get_settings();
		$posts = $this->get_posts();
		$posts = array_slice($posts, 0, $number);

		// set link target
		$link_target = ($opts['link_new_window']) ? "_blank" : ''; 

		$output = "\n<!-- Recent Facebook Posts v".RFBP_VERSION." - http://wordpress.org/plugins/recent-facebook-posts/ -->\n";

		$output .= '<div class="recent-facebook-posts rfb_posts shortcode">';

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
			$output .= '<p class="rfb_text">'. nl2br(rfbp_make_clickable($content, $link_target));
			if ($shortened) $output .= '..';
			$output .= '</p>';

			if($opts['img_size'] != 'dont_show' && isset($post['image']) && $post['image']) { 
				
				$img_atts = 'src="'. $post['image'] .'" style="max-width: '. $opts['img_width'] .'px; max-height: '. $opts['img_width'] .'px;"';	

				$output .= '<p class="rfb_image"><a target="'.$link_target.'" href="'. $post['link'] . '" rel="nofollow"><img '. $img_atts .' alt="" /></a></p>';
			}

			$output .= '<p><a target="'.$link_target.'"" class="rfb_link" href="'. $post['link'] .'" rel="nofollow">';
			if($likes || $comments) { $output .= '<span class="like_count_and_comment_count">'; }
			if($likes) { $output .= '<span class="like_count">'. $post['like_count'] . ' <span>likes</span></span> '; }
			if($comments) { $output .= '<span class="comment_count">' . $post['comment_count'] . ' <span>comments</span></span> '; }
			if($likes || $comments) { $output .= '</span>'; }
			$output .= '<span class="timestamp" title="'. date('l, F j, Y', $post['timestamp']) . ' at ' . date('G:i', $post['timestamp']) . '" >';
			if($likes || $comments) { $output .= ' · '; }
			$output .= '<span>' . rfbp_time_ago($post['timestamp']) . '</span></span>';
			$output .= '</a></p></div>' ;

		} 

		if(empty($posts)) {
			$output .= '<p>No recent Facebook status updates to show.</p>';
			if(current_user_can('manage_options')) { 
				$output .= '<p><strong>Admins only notice:</strong> Did you <a href="' . get_admin_url(null,'options-general.php?page=rfb-settings') . '">configure the plugin</a> properly?</p>';
			}
		}

		$output .= "</div>";
		$output .= "\n <!-- / Recent Facebook Posts -->\n";
		return $output;
	}

	private function get_time_of_last_file_change($filePath) 
	{ 
		clearstatcache();
		$time = filemtime($filePath); 

		$isDST = (date('I', $time) == 1); 
		$systemDST = (date('I') == 1); 

		$adjustment = 0; 

		if($isDST == false && $systemDST == true) 
			$adjustment = 3600; 

		else if($isDST == true && $systemDST == false) 
			$adjustment = -3600; 

		else 
			$adjustment = 0; 

		return ($time + $adjustment); 
	} 
	
}