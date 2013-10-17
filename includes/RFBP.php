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

			add_shortcode('recent_facebook_posts', array($this, 'output'));
			add_shortcode('recent-facebook-posts', array($this, 'output'));
			
			if($opts['load_css']) {
				add_action( 'wp_enqueue_scripts', array($this, 'load_css'));
			}

		} else {
			// only in admin panel
			include_once RFBP_PLUGIN_DIR . 'includes/RFBP_Admin.php';
			new RFBP_Admin();
		}

		add_action('rfbp_renew_cache', array($this, 'delete_transients'), 10);
		add_action('rfbp_renew_cache', array($this, 'get_posts'), 11);
	}

	public function register_widget()
	{
		register_widget( "RFBP_Widget" );
	}

	public function load_css() {
		wp_register_style('recent-facebook-posts-css', plugins_url('recent-facebook-posts/assets/css/default.css'), array(), RFBP_VERSION );
		wp_enqueue_style('recent-facebook-posts-css' );
	}

	public function get_settings() {
		if(!$this->options) {

			$defaults = array(
				'app_id' => '',
				'app_secret' => '',
				'fb_id' => 'DannyvanKootenCOM',
				'cache_time' => 7200,
				'load_css' => 1,
				'link_text' => 'Find us on Facebook',
				'link_new_window' => 0,
				'img_size' => 'normal',
				'img_width' => '',
				'img_height' => ''
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
			require_once RFBP_PLUGIN_DIR . 'includes/RFBP_API.php';
			$opts = RFBP::instance()->get_settings();
			self::$api = new RFBP_API($opts['app_id'], $opts['app_secret'], $opts['fb_id']);
		}

		return self::$api;
	}

	public function get_fallback_posts()
	{
		$posts = get_transient('rfbp_posts_fallback');

		if(!$posts) {
			return array();
		}

		return $posts;
	}

	public function get_posts() {
		
		// try to get posts from cache
		if(($posts = get_transient('rfbp_posts'))) {
			return $posts;
		}

		$opts = $this->get_settings();

		$api = self::api();
		$data = $api->get_posts();
		
		// did api call succeed?
		if(!$data) { 
			return $this->get_fallback_posts();
		}

		$posts = array();
		foreach($data as $p) {

			// skip this "post" if it is not of one of the following types
			if(!in_array($p->type, array('status', 'photo', 'video', 'link'))) { 
				continue;
			}

			// skip empty status updates and friend approvals
			if($p->type == 'status' && (!isset($p->message) || empty($p->message))) { continue; }
			if($p->type == 'status' && $p->status_type == 'approved_friend') { continue; }

			//var_dump($p); echo '<br /><br />';

			//split user and post ID (userID_postID)
			$idArray = explode("_", $p->id);

			$post = array();
			$post['type'] = $p->type;
			$post['author'] = $p->from;
			$post['content'] = isset($p->message) ? $p->message : '';
			$post['image'] = null;

			// set post content and image
			if($p->type == 'photo') {

				$image = "//graph.facebook.com/". $p->object_id . '/picture?type=' . $opts['img_size'];
				$post['image'] = $image;

			} elseif($p->type == 'video') {

				$image = $p->picture;

				// hacky
				if($opts['img_size'] == 'normal') {
					$image = str_replace(array("_s.jpg", "_s.png"), array("_n.jpg", "_n.png"), $image);
				} 

				$post['image'] = $image;

			} elseif($p->type == 'link') {
				$post['content'] .= "\n\n" . $p->link;
			}

			// calculate post like and comment counts
			if(isset($p->likes->summary->total_count)) {
				$like_count = $p->likes->summary->total_count;
			} else {
				$like_count = 0;
			}

			if(isset($p->comments->summary->total_count)) {
				$comment_count = $p->comments->summary->total_count;
			} else {
				$comment_count = 0;
			}

			$post['timestamp'] = strtotime($p->created_time);
			$post['like_count'] = $like_count;
			$post['comment_count'] = $comment_count;
			$post['link'] = "http://www.facebook.com/".$opts['fb_id']."/posts/".$idArray[1];
			$posts[] = $post;

		}

		// store results in cache for later use
		if($posts) {
			set_transient('rfbp_posts', $posts, $opts['cache_time']); // user set cache time
			set_transient('rfbp_posts_fallback', $posts, 2629744); // 1 month
			$this->cache_renewed = true;
		}
		
		return $posts;
	}

	public function output($atts = array())
	{
		extract(shortcode_atts(array(
			'number' => '5',
			'likes' => 1,
			'comments' => 1,
			'excerpt_length' => 140,
			'el' => 'div',
			'origin' => 'shortcode',
			'show_link' => false
			), $atts));

		$opts = $this->get_settings();
		$posts = $this->get_posts();

		ob_start();
		?>
		<!-- Recent Facebook Posts v<?php echo RFBP_VERSION; ?> - http://wordpress.org/plugins/recent-facebook-posts/ -->
		<div class="recent-facebook-posts rfbp rfbp-container rfbp-<?php echo $origin; ?>">
			<?php

			if($posts && !empty($posts)) {

				if($el == 'li') { echo '<ul class="rfbp-posts-wrap">'; }

				$posts = array_slice($posts, 0, $number);
				$link_target = ($opts['link_new_window']) ? "_blank" : ''; 

				foreach($posts as $p) { 
					$content = $p['content'];
					$shortened = false;

					if(strlen($content) > $excerpt_length) {
						$limit = strpos($p['content'], ' ',$excerpt_length); 
						if($limit) {
							$content = substr($p['content'], 0, $limit);
							$shortened = true;
						}
					}
					?>

					<<?php echo $el; ?> class="rfbp-post">
					<div class="rfbp-text">

						<?php 
						$content = make_clickable($content, $link_target); 
						$content = ($shortened) ? $content . '..' : $content;
						echo wpautop($content); ?>

					</div>
					<?php if($opts['img_size'] != 'dont_show' && isset($p['image']) && !empty($p['image'])) { ?>
					<p class="rfbp-image-wrap">
						<a class="rfbp-image-link" target="<?php echo $link_target; ?>" href="<?php echo $p['link']; ?>" rel="nofollow">
							<?php $max_img_width = (!empty($opts['img_width'])) ? $opts['img_width'].'px' : '100%'; $max_img_height = (!empty($opts['img_height'])) ? $opts['img_height'].'px' : 'none'; ?>
							<img class="rfbp-image" src="<?php echo $p['image']; ?>" style="max-width: <?php echo $max_img_width; ?>; max-height: <?php echo $max_img_height; ?>" alt="" />
						</a>
					</p>
					<?php } ?>
					<p class="rfbp-post-link-wrap">
						<a target="<?php echo $link_target; ?>" class="rfbp-post-link" href="<?php echo $p['link']; ?>" rel="nofolloW">
							<?php if($likes) { ?><span class="rfbp-like-count"><?php echo $p['like_count']; ?> <span>likes<?php if($comments) { ?>, <?php } ?></span></span><?php } ?>
							<?php if($comments) { ?><span class="rfbp-comment-count"><?php echo $p['comment_count']; ?> <span>comments</span></span><?php } ?>
							<?php if($likes || $comments) { ?> · <?php } ?>
							<span class="rfbp-timestamp" title="<?php echo date('l, F j, Y', $p['timestamp']) ?> at <?php echo date('G:i', $p['timestamp']); ?>"><?php echo rfbp_time_ago($p['timestamp']); ?></span>
						</a>
					</p>
					</<?php echo $el; ?>>
				<?php 

				} // end foreach $posts

				if($el == 'li') { echo '</ul>'; }

			} else {
				?><p>No recent Facebook posts to show.</p><?php
				if(current_user_can('manage_options')) { 
					?><p><strong>Admins only notice:</strong> Did you <a href="<?php echo admin_url('options-general.php?page=rfbp'); ?>">configure the plugin</a> properly?</p><?php
				}
			} ?>

			<?php if($show_link) { ?>
				<p class="rfbp-page-link-wrap"><a class="rfbp-page-link" href="http://www.facebook.com/<?php echo $opts['fb_id']; ?>/" rel="external nofollow" target="<?php echo $link_target; ?>"><?php echo strip_tags($opts['link_text']); ?></a></p>
			<?php } ?>

			</div>
			<!-- / Recent Facebook Posts -->
			<?php
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}

	}