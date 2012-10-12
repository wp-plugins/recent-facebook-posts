<?php

class RFB_Widget extends WP_Widget {

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

 		global $RFB;
 		$rfb_options = $RFB->get_options();

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

	public function widget( $args, $instance ) {
		global $RFB;

		$opts = $RFB->get_options();
		$posts = $RFB->get_posts();
		$posts = array_slice($posts, 0, $instance['number_of_posts']);

		extract( $args );
		$instance = array_merge($this->defaults, $instance);
		extract($instance);

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title; ?>
			<ul class="rfb_posts">
			<?php foreach($posts as $post) { ?>
				<li><?php echo nl2br(make_clickable(substr($post['content'], 0, $instance['excerpt_length']))); if(strlen($post['content']) > $instance['excerpt_length']) echo '..'; ?> 
					<a target="_blank" class="fb_link" href="<?php echo $post['link']; ?>" rel="nofollow">
						<?php if($show_like_count || $show_comment_count) { ?><span class="like_count_and_comment_count"><?php } ?>
						<?php if($show_like_count) { ?><span class="like_count"><?php echo $post['like_count']; ?> <span>likes</span></span> <?php } ?>
						<?php if($show_comment_count) { ?><span class="comment_count"><?php echo $post['comment_count']; ?> <span>comments</span></span> <?php } ?>
						<?php if($show_like_count || $show_comment_count) { ?></span><?php } ?>
						<span class="timestamp" title="<?php echo date('l, F j, Y', $post['timestamp']) . ' at ' . date('G:i', $post['timestamp']); ?>" ><?php if($show_like_count || $show_comment_count) { ?> · <?php } ?><span><?php echo $this->time_ago($post['timestamp']); ?></span></span>
					</a>
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
				<p><a href="http://www.facebook.com/<?php echo $opts['fb_id']; ?>/" rel="external nofollow"><?php echo strip_tags($opts['link_text']); ?></a>.</p>
			<?php } ?>

			<?php 

		echo $after_widget;
	}

	private function time_ago($timestamp) {
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