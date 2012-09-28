<?php
/*
Plugin Name: Recent Facebook Posts
Plugin URI: http://dannyvankooten.com/wordpress-plugins/recent-facebook-posts/
Description: Widget that lists your X most recent Facebook posts.
Version: 0.1
Author: Danny van Kooten
Author URI: http://dannyvankooten.com/
License: GPL2
*/

/*  Copyright 2012  Danny van Kooten  (email : hi@dannyvankooten.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once dirname(__FILE__) . '/classes/class-rfb.php';
require_once dirname(__FILE__) . '/classes/class-rfb-widget.php';

$RFB = RFB::get_instance();
add_action( 'widgets_init', create_function( '', 'register_widget( "RFB_Widget" );' ) );

if(is_admin()) {
	require_once dirname(__FILE__) . '/classes/class-rfb-admin.php';
	$RFB_Admin = new RFB_Admin($RFB);
}


