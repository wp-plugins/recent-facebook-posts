<?php

function recent_facebook_posts($args = array())
{
	$defaults = array(
		'number' => '5',
		'likes' => 1,
		'comments' => 1,
		'excerpt_length' => 140
	);

	$args = wp_parse_args($args, $defaults);
	extract($args);

	echo do_shortcode("[recent-facebook-posts number={$number} likes={$likes} comments={$comments} excerpt_length={$excerpt_length}]");
}