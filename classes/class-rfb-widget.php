<?php

class RFB_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'rfb_widget', // Base ID
			'Recent Facebook Posts', // Name
			array( 'description' => 'List your # most recent Facebook posts with this widget.' )
		);
	}

 	public function form( $instance ) {

 		$title = (isset($instance['title'])) ? $instance['title'] : 'Recent Facebook posts';
 		$number_of_posts = (isset($instance['number_of_posts'])) ? $instance['number_of_posts'] : 5;
 		$excerpt_length = (isset($instance['excerpt_length'])) ? $instance['excerpt_length'] : 140;

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
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number_of_posts'] = (int) strip_tags( $new_instance['number_of_posts'] );
		$instance['excerpt_length'] = (int) strip_tags($new_instance['excerpt_length']);
		return $instance;
	}

	public function widget( $args, $instance ) {
		global $RFB;

		$posts = $RFB->get_posts();
		$posts = array_slice($posts, 0, $instance['number_of_posts']);

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title; ?>
			<ul>
			<?php foreach($posts as $post) { ?>
				<li><?php echo make_clickable(substr($post['content'], 0, $instance['excerpt_length'])); if(strlen($post['content']) > $instance['excerpt_length']) echo '..'; ?> <a target="_blank" class="timestamp" href="<?php echo $post['link']; ?>"><?php echo $this->time_ago($post['timestamp']); ?></a></li>
			<?php } 

			if(empty($posts)) { ?>
				<li>No recent Facebook posts to show.<?php if(current_user_can('manage_options')) { ?> Did you <a href="<?php echo get_admin_url(null,'options-general.php?page=rfb-settings'); ?>">configure the plugin</a> properly? (only admins see this sentence)<?php } ?></li>

			<?php } ?>
			</ul>
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