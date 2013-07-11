=== Plugin Name ===
Contributors: DvanKooten
Donate link: http://dannyvankooten.com/donate/
Tags: mailchimp, newsletter, mailinglist, email, email list, form, widget form, sign-up form, subscribe form, comments, comment form, mailchimp widget, buddypress, multisite
Requires at least: 3.1
Tested up to: 3.5.2
Stable tag: 0.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Thé ultimate MailChimp plugin! Includes a form shortcode, form widget and a comment form checkbox to grow your MailChimp list(s).

== Description ==

= MailChimp for WordPress =

This plugin provides you with various ways to grow your MailChimp list(s). Add a form to your posts or pages by using the `[mc4wp-form]` shortcode, use this shortcode in your text widgets to show a form in your widget areas or add a "Sign-me up to our newsletter" checkbox to your comment and/or registration forms. 

Configuring is easy, all you need is your MailChimp API key!

**Features:**

* Embed sign-up forms to your pages or posts by using a simple shortcode `[mc4wp-form]`
* Add a MailChimp sign-up form to your widget areas by using the shortcode in a text widget
* Adds a "sign-up to our newsletter" checkbox to your comment form or registration form
* Sign-up requests from bots will be ignored (honeypot, Akismet, default spam protection).
* Includes a simple way to design forms, add as many fields as you like.
* Uses the MailChimp API, blazingly fast and reliable.
* Configuring is extremely easy because of the way this plugin is set-up, all you need is your MailChimp API key.
* The checkbox is compatible with BuddyPress and MultiSite registration forms

**More info:**

* [MailChimp for WordPress](http://dannyvankooten.com/wordpress-plugins/mailchimp-for-wordpress/)
* Check out more [WordPress plugins](http://dannyvankooten.com/wordpress-plugins/) by Danny van Kooten
* You should follow [Danny on Twitter](http://twitter.com/DannyvanKooten) for lightning fast support and updates.

**MailChimp Sign-Up Form**
The plugin comes packed with an easy to way to build a form just like you want it. You have the possibility to add as many fields as you like and customize labels, placeholders, initial values etc..

Use the `[mc4wp-form]` shortcode to use this form in your posts, pages or text widgets.

**Sign-Up Checkbox**
Commenters and subscribers are valuable visitors who are most likely interested to be on your mailinglist. This plugin makes it easy for them, all they have to do is check a single checkbox when commenting or registering on your website!

== Installation ==

1. In your WordPress admin panel, go to Plugins > New Plugin, search for "MailChimp for WP" and click "Install now"
1. Alternatively, download the plugin and upload the contents of mailchimp-for-wp.zip to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin
1. Fill in your MailChimp API key in the plugin's options.
1. Select at least one list to subscribe visitors to.
1. (Optional) Select where the checkbox should show up.
1. (Optional) Design a form and include it in your posts, pages or text widgets.

== Frequently Asked Questions ==

= What does this plugin do? =
This plugin gives you the possibility to easily create a sign-up form and show this form in various places on your website. Also, this plugin can add a checkbox to your comment form that makes it easy for commenters to subscribe to your MailChimp newsletter. All they have to do is check one checkbox and they will be added to your mailinglist(s).

For a complete list of plugin features, take a look here: [MailChimp for WordPress](http://dannyvankooten.com/wordpress-plugins/mailchimp-for-wordpress/).

= Why does the checkbox not show up at my comment form? =
Your theme probably does not support the necessary comment hook this plugin uses to add the checkbox to your comment form. You can manually place the checkbox by calling `<?php do_action('comment_form') ?>` inside the form tags of your theme's comment form. Usually this file can be found in your theme folder as `comments.php`. Your theme folder can be found by browsing to `/wp-content/themes/your-theme-name/`.

= Where can I find my MailChimp API key? =
[http://kb.mailchimp.com/article/where-can-i-find-my-api-key](http://kb.mailchimp.com/article/where-can-i-find-my-api-key)

== Screenshots ==

1. The MC4WP options page.
1. The MC4WP form options page.

== Changelog ==
= 0.4.2 =
* Fixed W3C invalid value "true" for attribute "required"

= 0.4.1 =
* Fixed correct and more specific error messages
* Fixed form designer, hidden fields no longer wrapped in paragraph tags
* Added text fields to form designer
* Added error message when email address was already on the list
* Added debug message when there is a problem with one of the (required) merge fields

= 0.4 =
* Improved dashboard, it now has different tabs for the different settings.
* Improved guessing of first and last name.
* Fixed debugging statements on settings page
* Added settings link on plugins overview page
* Added form functionality
* Added form shortcode
* Added necessary filters for shortcodes to work inside text widgets
* Added spam honeypot to form to ignore bot sign-ups
* Added error & success messages to form
* Added Freddy icon to menu

= 0.3 =
* Fixed the missing argument bug when submitting a comment for some users.
* Added support for regular, BuddyPress and MultiSite registration forms.

= 0.2 =
* Fixed small bug where name of comment author was not correctly assigned
* Improved CSS reset for checkbox

= 0.1 =
* BETA release