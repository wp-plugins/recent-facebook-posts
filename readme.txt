=== Plugin Name ===
Contributors: DvanKooten
Donate link: http://dannyvankooten.com/donate/
Tags: facebook,posts,fanpage,recent posts,fb,like box alternative,widget,facebook widget,widgets,facebook updates,like button,fb posts
Requires at least: 3.0.1
Tested up to: 3.5
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Widget that lists your recent Facebook page or profile updates / posts. A faster/prettier/more customizable alternative to Facebooks Like Box.

== Description ==

This plugin adds a widget (and a shortcode `[recent-facebook-posts]`) to your WordPress installation which you can use to list your recent Facebook posts / updates. This can be either posts from your personal profile or posts from your fanpage.

**Benefits of using Recent Facebook Posts**

* More freedom then Facebooks Like Box, easier to customize and/or style by using plain old CSS.
* Outputs valid HTML, no more iframes.
* Faster then Facebooks like box, because the result is properly cached on your server.
* SEO friendly, your Facebook page / profile content becomes part of your website content. No iframes or JavaScript is involved.

**Demo**

[Recent FB Posts demo](http://wpdemo.dannyvankooten.com/), the widget is located in the right sidebar. As you can see, you can make the widget blend in with your theme perfectly.

**Coming up**

* Option to show a Like button
* Suggestions, anyone? Drop me a line if you do.

**More info:**

* [Recent Facebook Posts for WordPress](http://dannyvankooten.com/wordpress-plugins/recent-facebook-posts/)
* Check out more [WordPress plugins](http://dannyvankooten.com/wordpress-plugins/) by Danny van Kooten
* You should follow [Danny on Twitter](http://twitter.com/DannyvanKooten) for lightning fast support and updates.

== Installation ==

1. Upload the contents of the .zip-file to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. [Create a new Facebook application here](http://developers.facebook.com/apps). Fill in ANY name you'd like (it won't be visible to the public), leave the namespace field blank and the hosting checkbox unchecked.
1. In the Basic Application settings screen, scroll down a bit and select "Website with Facebook login" and set your `Site URL` to the URL of your WordPress installation.
1. Copy your `App ID` and `App Secret` (see screenshot 1)
1. Go to your WordPress Admin panel and open the Recent Facebook Posts options screen. (Settings > Recent Facebook Posts)
1. Paste your Facebook `App id` and `App Secret`.  [screenshot 1](http://wordpress.org/extend/plugins/recent-facebook-posts/screenshots/)
1. Find the numeric Facebook ID of your public Facebook page with [this website](http://findmyfacebookid.com/).
1. Fill in your Facebook ID in the RFB options screen.
1. Test your configuration by clicking the button "Renew access token".
1. Test if caching works by clicking the "Renew cache" button.
1. Drag the 'Recent FB Posts Widget' to one of your widget areas.
1. (optional) Apply some custom CSS rules to style your recent FB posts widget. Just add them to your theme's CSS file.

If you're still in doubt, have a look at the [screenshots](http://wordpress.org/extend/plugins/recent-facebook-posts/screenshots/).

== Frequently Asked Questions ==

= What does this plugin do? =
This plugin adds a widget to your WordPress installation which you can use to list your X most recent Facebook posts. This can be either posts from your personal profile or from one of your fanpages. An example of the widget can be found [here](http://wpdemo.dannyvankooten.com/).

= Why not simply use FB's likebox? =
This plugin gives you the freedom to style your most recent facebook posts using plain old CSS, thus giving you much more freedom. Also, this plugin outputs valid HTML. Iframes are a thing of the past. Your Facebook posts are cached on your server so it is somewhat faster too.

= How to configure this plugin? =
Create a new Facebook application and fill in the field where it asks for your website URL. Then go to the configuration page of Recent Facebook Posts and copy-paste your App ID and App Secret. Have a look at the screenshots if you're not clear about which fields you need.

= Why do I need to create a Facebook application? =
In order to query content on Facebook an application is needed. Facebook doesn't allow their content publicly to anyone, they want to know "who is asking". You don't need to submit your app to the App Center in order for it to work though.

= Do you have a working demo I can take a look at? =
Sure, [here](http://wpdemo.dannyvankooten.com/). The widget is located in the right sidebar and shows posts from [my Facebook page](http://www.facebook.com/DannyvanKootenCOM). "Like" it, while you're at it!

= Facebook gives me this error when renewing the access token: The specified URL is not owned by the application =
You are running the plugin on a different (sub)domain then specified in your FB app configuration. Fix it by correctly setting your "Site URL" or by adding an App Domain if you are running the plugin on a subdomain.

= The plugin says it is connected, renewing my access token works but still there are no status updates to be shown. =
Please check if the page you are trying to fetch posts from has **publicly** available posts. The privacy setting of your status updates has to be "everyone" in order for the plugin to "see" your posts.

= Where to add custom CSS =
IMO, appearance should be handled by the theme you are using. This is why your custom CSS rules should be added to your theme's stylesheet file. You can find this file by browsing (over FTP) to `/wp-content/themes/your-theme-name/style.css`, or you can just use the WP Editor under Appearance > Editor.

= Does this plugin work with group posts? =
Currently, no. This plugin currently only works with pages and personal profiles.

= Can I show a list of recent facebook updates in my posts or pages? =
Yes, you can use the `[recent-facebook-posts]` shortcode. Optionally, add the following attributes: `likes`, `comments`, `excerpt_length`, `number`. Example: `[recent-facebook-posts number=10 likes=0 comments=0 excerpt_length=250]`

Valid attribute values are as follows:
`likes` : 0 (don't show like count), 1 (show like count)
`comments` : 0 (don't show comment count), 1 (show comment count)
`excerpt_length` : 1 - 9999 (sets the length of the excerpt)
`number` : 1 - 99 (set the number of posts to show)

== Screenshots ==

1. The black bordered circles are the fields you'll need to provide to Facebook, at a minimum. The green circled fields are the values you'll need to provide to Recent Facebook Posts.
2. The green circled fields are the fields where you'll need to provide your Facebook app id and app secret (as shown in screenshot 1).

== Changelog ==

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

