<?php

if( ! defined( 'RFBP_VERSION' ) ) {
	exit;
}

function recent_facebook_posts( $args = array() )
{
	$rfbp = rfbp_get_class();
	echo $rfbp->output($args);
}