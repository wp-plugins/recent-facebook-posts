<h1>Recent Facebook Posts</h1>

<div id="rfb" class="wrap">
	<div class="column" style="width:70%;">

		<?php 
		if(isset($errorMessage)) { ?>
			<div id="setting-error" class="error settings-error">
				<p>
					<strong>Error:</strong> <?php echo $errorMessage; ?>
				</p>
			</div>
		<?php
		} 

		if(isset($cacheError)) { ?>
			<div id="setting-error" class="error settings-error">
				<p>
					<strong>Cache error:</strong>
					<?php echo $cacheError; ?>
				</p>
			</div>
		<?php }

		if(isset($apiError)) {
			?>
			<div id="setting-error" class="error settings-error">
				<p>Facebook returned the following error.</p>
				<p>
					<strong><?php echo $apiError->getType(); ?>:</strong>
					<?php echo $apiError->getMessage(); ?>
				</p>
			</div>	
			<?php
		}

		if(isset($notice)) { ?>
			<div id="setting-error-settings_updated" class="updated settings-error"> 
				<p>
					<?php echo $notice; ?>
				</p>
			</div>
		<?php 
		}
		 ?>

		<h2 style="display:none;"></h2>
		<?php settings_errors(); ?>

		<h3>Configuration</h3>
		<form method="post" action="options.php">
				<?php settings_fields( 'rfb_settings_group' ); ?>

				<p class="status">Facebook API status: <span class="<?php echo ($connected) ? 'connected' : 'disconnected'; ?>"><?php echo ($connected) ? 'Connected' : 'Not Connected'; ?></span></p>
				
				<table class="form-table">
					<tbody>
						<tr valign="top">
						    <th scope="row"><label for="rfb_app_id" <?php if(empty($opts['app_id'])) echo 'class="error"'; ?>>Facebook App ID</label></th>
						    <td><input type="text" size="50" id="rfb_app_id" name="rfb_settings[app_id]" value="<?php echo $opts['app_id']; ?>" /></td>
						</tr>

						<tr valign="top">
						    <th scope="row"><label for="rfb_app_secret" <?php if(empty($opts['app_secret'])) echo 'class="error"'; ?>>Facebook App Secret</label></th>
						    <td><input type="text" size="50" id="rfb_app_secret" name="rfb_settings[app_secret]" value="<?php echo $opts['app_secret']; ?>" /></td>
						</tr>

						<tr valign="top">
						    <th scope="row"><label for="rfb_fb_id" <?php if(empty($opts['fb_id'])) echo 'class="error"'; ?>>Facebook page ID <small><a target="_blank" href="http://findmyfacebookid.com/">(use this)</a></small></label></th>
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
					</tbody>
					<tbody id="rfb_img_options" <?php if($opts['img_size'] == 'dont_show') echo 'style="display:none;"'; ?>>
						<tr valign="top">
						    <th scope="row"><label for="rfb_img_width">Image width <small>(in px)</small></label></th>
						    <td><input type="text" size="50" id="rfb_img_width" name="rfb_settings[img_width]" value="<?php echo $opts['img_width']; ?>" /></td>
						</tr>

						<tr valign="top">
						    <th scope="row"><label for="rfb_img_width">Image height <small>(in px)</small></label></th>
						    <td><input type="text" size="50" id="rfb_img_height" name="rfb_settings[img_height]" value="<?php echo $opts['img_height']; ?>" /></td>
						</tr>
					</tbody>
					<tbody>
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

					</tbody>

				</table>

				<p class="submit">
			    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			    </p>

		</form>

			<?php if($curl && !empty($opts['app_id']) && !empty($opts['app_secret'])) { ?>
				<h3 title="<?php echo get_option('rfb_access_token'); ?>">Access Token</h3>
				<p>Use this button to receive a new access token from Facebook.</p>
				<p><strong>Important: </strong> you should make it a habit to click this button once every few weeks.</p>
				<p><a class="button-primary" href="<?php echo admin_url('admin.php?page=rfb-settings&login_to_fb'); ?>">Get Access Token</a></p>
			<?php }

			if($connected) { ?>
				<h3>Cache</h3>
				<p>Because fetching posts from Facebook is "expensive", this will only happen every <?php echo $opts['cache_time']; ?> seconds (as configured above). You can manually renew the cache using the button below.</p>
				<p>
					<form action="<?php echo admin_url('admin.php?page=rfb-settings'); ?>" method="post">
						<input type="hidden" name="renew_cache" value="1" />
						<input type="submit" class="button-primary" value="Renew cache file" />
					</form>
				</p>
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

			<p>Alternatively, you can: </p>
            <ul>
                <li><a href="http://wordpress.org/support/view/plugin-reviews/recent-facebook-posts?rate=5#postform" target="_blank">Give a 5&#9733; rating on WordPress.org</a></li>
                <li><a href="http://dannyvankooten.com/wordpress-plugins/recent-facebook-posts/" target="_blank">Blog about it and link to the plugin page</a></li>
                <li><a href="http://twitter.com/?status=I%20show%20my%20recent%20facebook%20posts%20on%20my%20%23wordpress%20site%20using%20the%20Recent%20Facebook%20Posts%20plugin%20-%20check%20it%20out!%20http%3A%2F%2Fwordpress.org%2Fplugins%2Frecent-facebook-posts%2F" target="_blank">Tweet about Recent Facebook Posts</a></li>
            </ul>
        </div>

        <div class="box">
        	<h3>Looking for support?</h3>
        	<p>Having trouble? Please use the <a href="http://wordpress.org/support/plugin/recent-facebook-posts">support forums</a> on WordPress.org.</p>
        </div>

        <div class="box">
        	<h3>About the developer</h3>
        	<p>I am <a href="http://dannyvankooten.com/">Danny</a>, a young Dutch nerd.</p>
        	<p>Besides this one I developed a few other <a href="http://dannyvankooten.com/wordpress-plugins/">WordPress plugins</a>. Have a look, you might like them.</p>
        	<p>I enjoy developing plugins for WordPress but I'd like to remind you that I don't get paid for it while I spend <strong>a lot</strong> of my free time developing, chasing bugs, offering support, etcetera. That's why <a href="http://dannyvankooten.com/donate/">donations</a> are very much appreciated!</p>
        	<p>Thank you for using this plugin. Enjoy!</p>
        </div>
	</div>
</div>