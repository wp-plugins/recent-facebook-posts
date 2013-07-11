<h1>Recent Facebook Posts</h1>

<div id="rfb" class="wrap">
	<div class="column" style="width:70%;">

		<?php if(isset($fb) && $fb->getUser() && !$access_token) { ?>
			<div id="setting-error-settings_updated" class="updated settings-error"> 
				<p>
					<strong>Your API settings seem fine but there is no valid access token available. </strong>
					<a class="button-primary" href="<?php echo $fb->getLoginUrl(array('redirect_uri' => get_admin_url() . 'options-general.php?page=rfb-settings&rfb_renew_access_token')); ?>">Request Access Token</a>
				</p>
			</div>
		<?php }

		if($cache_renewed) { ?>
			<div id="setting-error-settings_updated" class="updated settings-error"> 
				<p>
					Your cache file has been succesfully renewed.
				</p>
			</div>
		<?php 
		}

		if(!$curl) { ?>
			<div id="setting-error" class="error settings-error">
				<p>
					<strong>Error:</strong> Recent Facebook Posts needs the PHP cURL extension to be installed on your server.
				</p>
			</div>
		<?php } 

		if(isset($error)) { ?>
			<div id="setting-error" class="error settings-error">
				<p>The following error occured when trying to fetch a test post from <a target="_blank" href="http://www.facebook.com/<?php echo $opts['fb_id']; ?>">facebook.com/<?php echo $opts['fb_id']; ?></a></p>
				<p>
					<strong><?php echo $error->getType(); ?>:</strong>
					<?php echo $error->getMessage(); ?>
				</p>
			</div>
		<?php } 

		if(isset($cache_error)) { ?>
			<div id="setting-error" class="error settings-error">
				<p>
					<strong>Cache error:</strong>
					<?php echo $cache_error; ?>
				</p>
			</div>
		<?php } ?>

		<h3>Configuration</h3>
		<form method="post" action="options.php">
				<?php settings_fields( 'rfb_settings_group' ); ?>

				<p class="status">Facebook API status: <span class="<?php echo ($connected) ? 'connected' : 'disconnected'; ?>"><?php echo ($connected) ? 'Connected' : 'Not Connected'; ?></span></p>
				
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
					    <th scope="row"><label for="rfb_fb_id" <?php if(empty($opts['fb_id'])) echo 'class="error"'; ?>>Facebook numeric ID <small><a target="_blank" href="http://findmyfacebookid.com/">(?)</a></small></label></th>
					    <td><input type="text" size="50" id="rfb_fb_id" name="rfb_settings[fb_id]" value="<?php echo $opts['fb_id']; ?>" /></td>
					</tr>

					<tr valign="top">
					    <th scope="row"><label for="rfb_cache_time">Cache expiry time <small>(in seconds)</small></label></th>
					    <td><input type="text" size="50" id="rfb_cache_time" name="rfb_settings[cache_time]" value="<?php echo $opts['cache_time']; ?>" /></td>
					</tr>

					<tr valign="top">
					    <th scope="row"><label for="rfb_img_size">Image source size</label></th>
					    <td>
					    	<select id="rfb_img_size" name="rfb_settings[img_size]">
					    		<option value="dont_show" <?php if($opts['img_size'] == 'dont_show') { echo 'selected'; } ?>>Don't show images</option>
					    		<option value="thumbnail" <?php if($opts['img_size'] == 'thumbnail') { echo 'selected'; } ?>>Thumbnail</option>
					    		<option value="normal" <?php if($opts['img_size'] == 'normal') { echo 'selected'; } ?>>Normal</option>
					    	</select>
					</tr>

					<tr valign="top">
					    <th scope="row"><label for="rfb_img_width">Image width <small>(in px)</small></label></th>
					    <td><input type="text" size="50" id="rfb_img_width" name="rfb_settings[img_width]" value="<?php echo $opts['img_width']; ?>" /></td>
					</tr>

					<tr valign="top">
					    <th scope="row"><label for="rfb_img_width">Image height <small>(in px)</small></label></th>
					    <td><input type="text" size="50" id="rfb_img_height" name="rfb_settings[img_height]" value="<?php echo $opts['img_height']; ?>" /></td>
					</tr>

					<tr valign="top">
					    <th scope="row"><label for="rfb_link_text">Link text</label></th>
					    <td><input type="text" size="50" id="rfb_link_text" name="rfb_settings[link_text]" value="<?php echo $opts['link_text']; ?>" /></td>
					</tr>

					<tr valign="top">
					    <th scope="row"><label for="rfb_link_new_window">Open link in new window?</label></th>
					    <td><input type="checkbox" id="rfb_link_new_window" name="rfb_settings[link_new_window]" value="1" <?php if(isset($opts['link_new_window']) && $opts['link_new_window']) { ?>checked="1" <?php } ?>/></td>
					</tr>

					<tr valign="top">
					    <th scope="row"><label for="rfb_load_css">Load some default CSS?</label></th>
					    <td><input type="checkbox" id="rfb_load_css" name="rfb_settings[load_css]" value="1" <?php if(isset($opts['load_css']) && $opts['load_css']) { ?>checked="1" <?php } ?>/></td>
					</tr>



				</table>

				<p class="submit">
			    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			    </p>

		</form>

			<?php if($curl && !empty($opts['app_id']) && !empty($opts['app_secret'])) { ?>
				<h3 title="<?php echo get_option('rfb_access_token'); ?>">Access Token</h3>
				<p>Use this button to test your configuration. It also won't hurt to hit this button once in a month or so to renew your access token, although this should be taken care of automatically everytime you log into WordPress.</p>
				<a class="button-primary" href="<?php echo $fb->getLoginUrl(array('scope' => array('read_stream'), 'redirect_uri' => get_admin_url() . 'options-general.php?page=rfb-settings&rfb_renew_access_token')); ?>">Test / Renew Access Token</a>
			<?php } ?>
			<?php if($connected) { ?>
				<h3>Cache</h3>
				<p>Because fetching posts from Facebook is "expensive", this will only happen every <?php echo $opts['cache_time']; ?> seconds (as configured above). You can manually renew the cache using the button below.</p>
			<form action="" method="post"><input type="submit" name="renew_cache" class="button-primary" value="<?php _e('Renew Cache'); ?>" /></form>
		<?php } ?>
	</div>

	<div class="column clearfix" style="width:26%;  margin-left:3%;">
		
		<div id="rfb-donatebox" class="box">
			<h3>Donate $10, $20 or $50</h3>
			<p>I spent countless hours developing this plugin for <b>FREE</b>. If you use it and like it, consider buying me a beer as a small token of your appreciation.</p>
					
			<form class="donate" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_donations">
				<input type="hidden" name="business" value="AP87UHXWPNBBU">
				<input type="hidden" name="lc" value="US">
				<input type="hidden" name="item_name" value="Danny van Kooten">
				<input type="hidden" name="item_number" value="Recent Facebook Posts">
				<input type="hidden" name="currency_code" value="USD">
				<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">
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
        		improve the plugin? Please post your question / idea / problem in the <a href="http://wordpress.org/support/plugin/recent-facebook-posts">support forums</a> on WordPress.org.</p>
        </div>

        <div class="box">
        	<h3>Looking for more WordPress goodness?</h3>
        	<p>Have a look around on my personal website, <a href="http://dannyvankooten.com/">DannyvanKooten.com</a>.</p>
        </div>
	</div>
</div>