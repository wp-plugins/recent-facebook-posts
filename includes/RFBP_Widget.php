<?php

class RFBP_Widget extends WP_Widget {

	private $defaults = array(
		'title' => 'Recent Facebook posts',
		'number_of_posts' => 5,
		'excerpt_length' => 140,
		'show_comment_count' => false,
		'show_like_count' => false,
		'show_link' => false
	);

	public function __construct() {
		parent::__construct(
	 		'rfb_widget', // Base ID
			'Recent Facebook Posts', // Name
			array( 'description' => 'Lists a number of your most recent Facebook posts.' )
		);
	}

 	public function form( $instance ) {

 		$instance = array_merge($this->defaults, $instance);
 		extract($instance);

 		$rfb_options = RFBP::instance()->get_settings();

 		if(empty($rfb_options['app_id'])) { ?>
 		<p style="color:red;">You'll need to <a href="<?php echo get_admin_url(null, 'options-general.php?page=rfb-settings'); ?>">configure Recent Facebook Posts</a> in order for it to work.</p>
 		<?php } ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number_of_posts' ); ?>"><?php _e( 'Number of posts:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'number_of_posts' ); ?>" name="<?php echo $this->get_field_name( 'number_of_posts' ); ?>" type="text" value="<?php echo esc_attr( $number_of_posts ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt length:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="text" value="<?php echo esc_attr( $excerpt_length ); ?>" />
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'show_like_count' ); ?>" name="<?php echo $this->get_field_name( 'show_like_count' ); ?>" value="1" <?php if($show_like_count) { ?>checked="1"<?php } ?> />
			<label for="<?php echo $this->get_field_id( 'show_like_count' ); ?>"><?php _e( 'Show Like count?' ); ?></label> 
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'show_comment_count' ); ?>" name="<?php echo $this->get_field_name( 'show_comment_count' ); ?>" value="1" <?php if($show_comment_count) { ?>checked="1"<?php } ?> />
			<label for="<?php echo $this->get_field_id( 'show_comment_count' ); ?>"><?php _e( 'Show Comment count?' ); ?></label> 
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'show_link' ); ?>" name="<?php echo $this->get_field_name( 'show_link' ); ?>" value="1" <?php if($show_link) { ?>checked="1"<?php } ?> />
			<label for="<?php echo $this->get_field_id( 'show_link' ); ?>"><?php _e( 'Show a link to Facebook page?' ); ?></label> 
		</p>

		<p style="border: 2px solid green; font-weight:bold; background: #CFC; padding:5px; ">I spent countless hours developing (and offering support) for this plugin for FREE. If you like it, consider <a href="http://dannyvankooten.com/donate/">donating $10, $20 or $50</a> as a token of your appreciation.</p>       

		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number_of_posts'] = (int) strip_tags( $new_instance['number_of_posts'] );
		$instance['excerpt_length'] = (int) strip_tags($new_instance['excerpt_length']);
		$instance['show_like_count'] = isset($new_instance['show_like_count']);
		$instance['show_comment_count'] = isset($new_instance['show_comment_count']);
		$instance['show_link'] = isset($new_instance['show_link']);
		return $instance;
	}

	public function widget( $args, $instance = array()) {

		$opts = RFBP::instance()->get_settings();
		$posts = RFBP::instance()->get_posts();

		// slice posts to number set by user
		$posts = array_slice($posts, 0, $instance['number_of_posts']);

		// set link target
		$link_target = ($opts['link_new_window']) ? "_blank" : ''; 
		
		$instance = array_merge($this->defaults, $instance);

		extract($instance);
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title; ?>

			<!-- Recent Facebook Posts v<?php echo RFBP_VERSION; ?> - http://wordpress.org/plugins/recent-facebook-posts/ -->

			<ul class="rfb_posts">
			<?php foreach($posts as $post) { ?>
				<?php 
					$content = $post['content'];
					$shortened = false;

					if(strlen($content) > $instance['excerpt_length']) {
						$limit = strpos($post['content'], ' ',$instance['excerpt_length']); 
						if($limit) {
							$content = substr($post['content'], 0, $limit);
							$shortened = true;
						}
					}
				
				?>
				<li>
					<p class="rfb_text"><?php echo nl2br(rfbp_make_clickable($content, $link_target)); if($shortened) echo '..'; ?></p>
					<?php 
					if($opts['img_size'] != 'dont_show' && isset($post['image']) && $post['image']) { 

						if(isset($post['type']) && $post['type'] == 'photo') {
							$img_atts = "src=\"{$post['image']}\" style=\"max-height: {$opts['img_height']}px;\"";
						} else {
							$img_atts = "src=\"{$post['image']}\" style=\"\"";
						}
						?>
						<p class="rfb_image">
							<a target="<?php echo $link_target; ?>" href="<?php echo $post['link']; ?>" rel="nofollow">

								<img <?php echo $img_atts; ?> alt="" />
							</a>
						</p>
					<?php } ?>

					
					<p><a target="<?php echo $link_target; ?>" class="rfb_link" href="<?php echo $post['link']; ?>" rel="nofollow">
						<?php if($show_like_count || $show_comment_count) { ?><span class="like_count_and_comment_count"><?php } ?>
						<?php if($show_like_count) { ?><span class="like_count"><?php echo $post['like_count']; ?> <span>likes</span></span> <?php } ?>
						<?php if($show_comment_count) { ?><span class="comment_count"><?php echo $post['comment_count']; ?> <span>comments</span></span> <?php } ?>
						<?php if($show_like_count || $show_comment_count) { ?></span><?php } ?>
						<span class="timestamp" title="<?php echo date('l, F j, Y', $post['timestamp']) . ' at ' . date('G:i', $post['timestamp']); ?>" ><?php if($show_like_count || $show_comment_count) { ?> · <?php } ?><span><?php echo rfbp_time_ago($post['timestamp']); ?></span></span>
					</a></p>
				</li>
			<?php } 

			if(empty($posts)) { ?>
				<li>
					<p>No recent Facebook status updates to show.</p>
					<?php if(current_user_can('manage_options')) { ?><p><strong>Admins only notice:</strong> Did you <a href="<?php echo get_admin_url(null,'options-general.php?page=rfb-settings'); ?>">configure the plugin</a> properly?<?php } ?></p>
				</li>

			<?php } ?>
			</ul>

			<?php if($show_link) { ?>
				<p><a href="http://www.facebook.com/<?php echo $opts['fb_id']; ?>/" rel="external nofollow" target="<?php echo $link_target; ?>"><?php echo strip_tags($opts['link_text']); ?></a>.</p>
			<?php } ?>

			<!-- / Recent Facebook Posts -->

			<?php 

		echo $after_widget;
	}

	

}