<h1>Recent Facebook Posts</h1>

<div id="rfb" class="wrap">
	<div class="column" style="width:70%;">

		<h3>Configuration</h3>
		<form method="post" action="options.php">
				<?php settings_fields( 'rfb_settings_group' ); ?>

				<table class="form-table">

					<tr valign="top">
					    <th scope="row"><label for="rfb_app_id" <?php if(empty($opts['app_id'])) echo 'class="error"'; ?>>App ID</label></th>
					    <td><input type="text" size="50" id="rfb_app_id" name="rfb_settings[app_id]" value="<?php echo $opts['app_id']; ?>" /></td>
					</tr>

					<tr valign="top">
					    <th scope="row"><label for="rfb_app_secret" <?php if(empty($opts['app_secret'])) echo 'class="error"'; ?>>App Secret</label></th>
					    <td><input type="text" size="50" id="rfb_app_secret" name="rfb_settings[app_secret]" value="<?php echo $opts['app_secret']; ?>" /></td>
					</tr>

					<tr valign="top">
					    <th scope="row"><label for="rfb_fb_id" <?php if(empty($opts['fb_id'])) echo 'class="error"'; ?>>Facebook user id, page id or slug</label></th>
					    <td><input type="text" size="50" id="rfb_fb_id" name="rfb_settings[fb_id]" value="<?php echo $opts['fb_id']; ?>" /></td>
					</tr>

					<tr valign="top">
					    <th scope="row"><label for="rfb_cache_time">Cache expiry time <small>(in seconds)</small></label></th>
					    <td><input type="text" size="50" id="rfb_cache_time" name="rfb_settings[cache_time]" value="<?php echo $opts['cache_time']; ?>" /></td>
					</tr>

				</table>

				<p class="submit">
			    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			    </p>

		</form>

		<h3 title="<?php echo get_option('rfb_access_token'); ?>">Access Token</h3>
		<p>Although this should be taken care of automatically every time you log into WordPress, it can't hurt to hit this button every month or so. Tokens are valid for 2 months by default.</p>
		<a class="button-primary" href="<?php echo $fb->getLoginUrl(array('redirect_uri' => get_admin_url() . 'options-general.php?page=rfb-settings&rfb_renew_access_token')); ?>">Renew Access Token</a>

		<h3>Cache</h3>
		<p>Because fetching posts from Facebook is "expensive", this will only happen every <?php echo $opts['cache_time']; ?> seconds (as configured above). You can manually renew the cache using the button below.</p>
		<form action="" method="post"><input type="submit" name="renew_cache" class="button-primary" value="<?php _e('Renew Cache'); ?>" /></form>
	</div>

	<div class="column clearfix" style="width:26%;  margin-left:3%;">
		
		<div id="rfb-donatebox" class="box">
			<h3>Donate $10, $20 or $50</h3>
			<p>I spent countless hours developing this plugin for <b>FREE</b>. If you like it, consider donating a token of your appreciation.</p>
					
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
			">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
			</form>

			<p>Or you can: </p>
            <ul>
                <li><a href="http://wordpress.org/extend/plugins/recent-facebook-posts/">Give a 5&#9733; rating on WordPress.org</a></li>
                <li><a href="http://dannyvankooten.com/wordpress-plugins/recent-facebook-posts/">Blog about it and link to the plugin page</a></li>
                <li style="vertical-align:bottom;"><a href="http://twitter.com/share" class="twitter-share-button" data-url="http://dannyvankooten.com/wordpress-plugins/recent-facebook-posts/" data-text="Showing my appreciation to @DannyvanKooten for his #WordPress plugin: Recent Facebook Posts" data-count="none">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></li>
            </ul>
        </div>

        <div class="box">
        	<h3>Looking for support?</h3>
        	<p>Having trouble configuring Recent Facebook Posts? Experiencing an error or just having a great idea on how to further
        		improve the plugin? Please post your question / idea / problem in the <a href="http://wordpress.org/tags/recent-facebook-posts">support forums</a> on WordPress.org.</p>
        </div>

        <div class="box">
        	<h3>Looking for more WordPress goodness?</h3>
        	<p>Have a look around on my personal website, <a href="http://dannyvankooten.com/">DannyvanKooten.com</a>.</p>
        </div>
	</div>
</div>