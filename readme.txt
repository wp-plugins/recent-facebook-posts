=== Plugin Name ===
Contributors: DvanKooten
Donate link: http://dannyvankooten.com/donate/
Tags: facebook,posts,fanpage,recent posts,fb,like box alternative,widget,facebook widget,widgets,facebook updates,like button,fb posts
Requires at least: 3.1
Tested up to: 3.6
Stable tag: 1.5.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A faster, prettier and more customizable alternative to Facebooks Like Box. Lists most recent Facebook posts from public pages.

== Description ==

This plugin adds a widget and a shortcode `[recent-facebook-posts]` to your WordPress website which you can use to list your most recent Facebook posts. This plugin works with public pages and to a certain extent with personal profiles.

**Features**

* Highly customizable. By adding a few CSS rules to your theme's stylesheet you can make your Facebook updates blend in with your theme perfectly.
* SEO friendly. Your most recent Facebook updates can be indexed by search engines because the plugin doesn't use JavaScript or iframes to show the posts.
* Caching. The plugin uses caching to reduce round-trips to the Facebook servers.

**Demo**

There is a demo on [http://dannyvankooten.com/], I use the plugin to show my latest Facebook post in the footer.

**More info:**

* [Recent Facebook Posts for WordPress](http://dannyvankooten.com/wordpress-plugins/recent-facebook-posts/)
* Check out more [WordPress plugins](http://dannyvankooten.com/wordpress-plugins/) by Danny van Kooten
* You should follow [Danny on Twitter](http://twitter.com/DannyvanKooten) for lightning fast support and updates.

== Installation ==

1. Upload the contents of the .zip-file to your plugins directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. If you're not a Facebook developer yet, register as one [here](http://developers.facebook.com/apps).
1. [Create a new Facebook application](http://developers.facebook.com/apps). Fill in ANY valid name but leave the namespace field blank and the hosting checkbox unchecked.
1. In the Basic Application settings screen, scroll down a bit and select "Website with Facebook login" and set your `Site URL` to the URL of your WordPress installation. Example: `http://dannyvankooten.com/`
1. (optional) Fill in the App Domain field as well. Example: `dannyvankooten.com`
1. Open a new browser tab and go to your WordPress admin panel. Open the Recent FB Posts settings screen.
1. Copy paste your Facebook `App id` and `App Secret` into the setting fields.  [screenshot 1](http://wordpress.org/extend/plugins/recent-facebook-posts/screenshots/)
1. Find the numeric Facebook ID of your public Facebook page using [this website](http://findmyfacebookid.com/).
1. Fill in your Facebook ID in the RFB options screen.
1. Try to connect the plugin to Facebook by clicking the "Get access token" button.
1. Test if everything works by clicking the "Renew cache" button.

If you're still in doubt, have a look at the [screenshots](http://wordpress.org/extend/plugins/recent-facebook-posts/screenshots/).

== Frequently Asked Questions ==

= What does Recent Facebook Posts do? =
With this plugin you can show a list of your X most recent Facebook status updates in pages / posts or widget areas. Have a look at my [own WordPress website](http://dannyvankooten.com/) for an example, I have a widget with my latest Facebook update in my footer.

= How to configure this plugin? =
You need to create a Facebook application for this plugin to work. Have a **close** look at the [installation instructions](http://wordpress.org/plugins/recent-facebook-posts/installation/).

= Why do I need to create a Facebook application? =
Facebook doesn't allow their content publicly to anyone, they want to know "who's asking". That's why you need to create a Facebook application. This application does not have to be publicly visible though.

= Do you have a working demo I can take a look at? =
Sure, I use the plugin on my own [WordPress website](http://dannyvankooten.com/), in the footer widget.

= Facebook gives me this error when renewing the access token: The specified URL is not owned by the application =
You are running the plugin on a different (sub)domain then specified in your FB app configuration. Fix it by correctly setting your "Site URL" or by adding an App Domain if you are running the plugin on a subdomain. **Pay close attention to subdomains like www and trailing slashes, it has to be an exact match.**

= Where to add custom CSS =
In my opinion, appearance should be handled by your theme and not by plugins. This is why you can just add custom CSS rules to your theme's stylesheet. This file is usually located here: `/wp-content/themes/your-theme-name/style.css`.

= Does this plugin work with group posts? =
No, sorry. Recent Facebook Posts works with public pages and to a certain extent with personal profiles.

= Can I show a list of recent facebook updates in my posts or pages? =
Yes, you can use the `[recent-facebook-posts]` shortcode. Optionally, add the following attributes.

`
likes = 1 // show like count, 1 = yes, 0 = no
comments = 1 // show comment count, 1 = yes, 0 = no
excerpt_length = 140 // the number of characters to show from each post
number = 5 // number of posts to show
`

*Shortcode example*
`[recent-facebook-posts number=10 likes=1 comments=1 excerpt_length=250]`

= What about usage of your plugin in template files? =
Use `<?php recent-facebook-posts(array('likes' => 1, 'excerpt_length => 140')); ?>` in your theme files. The parameter is optional, it can be an array of the same values available for the shortcode.

== Screenshots ==

1. The black bordered circles are the fields you'll need to provide to Facebook, at a minimum. The green circled fields are the values you'll need to provide to Recent Facebook Posts.
2. The green circled fields are the fields where you'll need to provide your Facebook app id and app secret (as shown in screenshot 1).

== Changelog ==
= 1.5.3 =
* Improved: Code improvement
* Improved: UI improvement, implemented some HTML5 fields
* Improved: Moved options page back to sub-item of Settings.

= 1.5.2 =
* Fixed: max-width in older browsers

= 1.5.1 =
* Improved: a lot of refactoring, code clean-up, etc.
* Improved: "open link in new window" option now applies to ALL generated links

= 1.5 =
* Improved: huge performance improvement for retrieving posts from Facebook
* Improved: some code refactoring
* Improved: cache now automatically invalidated when updating settings
* Improved: settings are now sanitized before saving
* Fixed: like and comment count no longer capped at 25
* Changed links to show your appreciation for the plugin.

= 1.4 =
* Changed cache folder to the WP Content folder (outside of the plugin to prevent cache problems after updating the plugin).
* Added redirection fallbacks when headers have already been sent when trying to connect to Facebook.
* Fixed error message when cURL is not enabled.
* Improved some messages and field labels so things are more clear.
* Updated Facebook API class.

= 1.3 =
* Added Facebook icon to WP Admin menu item
* Changed the connecting to Facebook process
* Improved error messages
* Improved code, code clean-up
* Improved usability in admin area by showing notifications, removing unnecessary options, etc.
* Added notice when access token expires (starting 14 days in advance)
* Fixed: Cannot redeclare Facebook class.
* Fixed: Images not being shown when using "normal" as image source size
* Fixed: empty status updates (friends approved)

= 1.2.3 =
* Changed the way thumbnail and normal image links are generated, now works with shared photos as well.
* Added read_stream permission, please update your access token.
* Added cache succesfully updated notice

= 1.2.2 =
* Added option to hide images
* Added option to load either thumbnail or normal size images from Facebook's CDN
* Added border to image links

= 1.2.1 =
* Fixed parameter app_id is required notice before being able to enter it.

= 1.2 =
* Fixed: Reverted back to 'posts' instead of 'feed', to exclude posts from others.
* Fixed: undefined index 'count' when renewing cache file   
* Fixed: wrong comment or like count for some posts
* Improved: calculation of cache file modification time to prevent unnecessary cache renewal
* Improved: error message when cURL is not enabled
* Improved: access token and cache configuration options are now only available when connected

= 1.1.2 =
* Fixed: Added spaces after the like and comment counts in the shortcode output

= 1.1.1 =
* Updated: Expanded installation instructions.
* Changed: Some code improvements
* Added: Link to Facebook numeric ID helper website.
* Added: Check if cache directory exists. If not the plugin will now automatically try to create it with the right permissions.
* Added: option to open link to Facebook Page in a new window.

= 1.1 =
* Added: Shortcode to show a list of recent facebook updates in your posts: '[recent-facebook-posts]'

= 1.0.5 =
* Added: More user-friendly error message when cURL is not enabled on your server.

= 1.0.4 =
* Improved: The way the excerpt is created, words (or links) won't be cut off now
* Fixed: FB API Error for unknown fields.
* Added: Images from FB will now be shown too. Drop me a line if you think this should be optional.

= 1.0.3 = 
* Improved the way the link to the actual status update is created (thanks Nepumuk84).
* Improved: upped the limit of the call to Facebooks servers. 

= 1.0.2 =
* Fixed a PHP notice in the backend area when renewing cache and fetching shared status updates.
* Added option to show link to Facebook page, with customizable text.

= 1.0.1 =
* Added error messages for easier debugging.

= 1.0 =
* Added option to load some default CSS
* Added option to show like count
* Added option to show comment count
* Improved usability. Configuring Recent Facebook Posts should be much easier now due to testing options.

= 0.1 =
* Initial release

