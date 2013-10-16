<div id="rfbp-options" class="wrap">

	<div class="rfbp-column" style="width:69%;">

	<h2>Recent Facebook Posts</h2>
	
		<?php 
		if(isset($errorMessage)) { ?>
			<div id="setting-error" class="updated settings-error">
				<p>
					<?php echo $errorMessage; ?>
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

		<h3>Configuration</h3>
		<form method="post" action="options.php">
				<?php settings_fields( 'rfb_settings_group' ); ?>

				<p class="status">Facebook API status: <span class="<?php echo ($connected) ? 'connected' : 'disconnected'; ?>"><?php echo ($connected) ? 'Connected' : 'Not Connected'; ?></span></p>
				
				<table class="form-table">
					<tbody>
						<tr valign="top">
						    <th scope="row"><label for="rfb_app_id" <?php if(empty($opts['app_id'])) echo 'class="error"'; ?>>Facebook App ID/API Key</label></th>
						    <td>
						    	<input type="text" class="widefat" placeholder="Eg: 123456789012345" id="rfb_app_id" name="rfb_settings[app_id]" value="<?php echo esc_attr($opts['app_id']); ?>" />
						    	<small class="help"><a href="https://developers.facebook.com/apps">get from developers.facebook.com/apps</a></small>
						    </td>
						</tr>

						<tr valign="top">
						    <th scope="row"><label for="rfb_app_secret" <?php if(empty($opts['app_secret'])) echo 'class="error"'; ?>>Facebook App Secret</label></th>
						    <td>
						    	<input type="text" class="widefat" placeholder="Eg: 16vgrz4hk45wvh29k2puk45wvk2h29pu"  id="rfb_app_secret" name="rfb_settings[app_secret]" value="<?php echo esc_attr($opts['app_secret']); ?>" />
						   	 	<small class="help"><a href="https://developers.facebook.com/apps">get from developers.facebook.com/apps</a></small>
						   	 </td>
						</tr>

						<tr valign="top">
						    <th scope="row"><label for="rfb_fb_id" <?php if(empty($opts['fb_id'])) echo 'class="error"'; ?>>Facebook Page ID</label></th>
						    <td>
						    	<input type="text" class="widefat" placeholder="Eg: DannyvanKootenCOM" id="rfb_fb_id" name="rfb_settings[fb_id]" value="<?php echo esc_attr($opts['fb_id']); ?>" />
						    	<small><a target="_blank" href="http://findmyfacebookid.com/">Use this tool to find the numeric ID of the Facebook page you want to fetch posts from</a></small>
						    </td>
						</tr>

						<tr valign="top">
						    <th scope="row"><label for="rfb_cache_time">Cache expiry time <small>(in seconds)</small></label></th>
						    <td><input type="number" min="60" max="99999999999" id="rfb_cache_time" name="rfb_settings[cache_time]" value="<?php echo esc_attr($opts['cache_time']); ?>" /></td>
						</tr>

						<tr valign="top">
						    <th scope="row"><label for="rfb_img_size">Image size</label></th>
						    <td>
						    	<select class="widefat" id="rfb_img_size" name="rfb_settings[img_size]">
						    		<option value="dont_show" <?php if($opts['img_size'] == 'dont_show') { echo 'selected'; } ?>>Don't show images</option>
						    		<option value="thumbnail" <?php if($opts['img_size'] == 'thumbnail') { echo 'selected'; } ?>>Thumbnail</option>
						    		<option value="normal" <?php if($opts['img_size'] == 'normal') { echo 'selected'; } ?>>Normal</option>
						    	</select>
						</tr>
					</tbody>
					<tbody id="rfb_img_options" <?php if($opts['img_size'] == 'dont_show') echo 'style="display:none;"'; ?>>
						<tr valign="top">
							<th>Image dimensions<br /><small>(in pixels)</small></th>
						    <td>
						    	<label style="float:left; margin-right:20px; ">
						    		Max Width<br />
						    		<input type="number" min="0" max="1600" size="3" id="rfb_img_width" name="rfb_settings[img_width]" value="<?php echo esc_attr($opts['img_width']); ?>" /> 
						    	</label>
						    	<label style="float:left; margin-right:20px;">
						    		Max Height<br />
						    		<input type="number" min="0" max="1600" size="3" id="rfb_img_height" name="rfb_settings[img_height]" value="<?php echo esc_attr($opts['img_height']); ?>" />
						   		</label>
						   		<small class="help"><br />Leave empty for default sizing (maximum of 100% of containing element)</small>
						    </td>
						</tr>
					</tbody>
					<tbody>
						<tr valign="top">
						    <th scope="row"><label for="rfb_link_text">Link text</label></th>
						    <td><input type="text" class="widefat" placeholder="Find us on Facebook" id="rfb_link_text" name="rfb_settings[link_text]" value="<?php echo esc_attr($opts['link_text']); ?>" /></td>
						</tr>

						<tr valign="top">
						    <th scope="row"><label for="rfb_link_new_window">Open links in new window?</label></th>
						    <td><input type="checkbox" id="rfb_link_new_window" name="rfb_settings[link_new_window]" value="1" <?php checked($opts['link_new_window'], 1); ?> /></td>
						</tr>

						<tr valign="top">
						    <th scope="row"><label for="rfb_load_css">Load some default CSS?</label></th>
						    <td><input type="checkbox" id="rfb_load_css" name="rfb_settings[load_css]" value="1" <?php checked($opts['load_css'], 1); ?> /></td>
						</tr>
					</tbody>
				</table>

				<p class="submit">
			    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			    </p>

		</form>

			<?php if($curl && !empty($opts['app_id']) && !empty($opts['app_secret'])) { ?>
				<h3 title="<?php echo get_option('rfb_access_token'); ?>">Access Token</h3>
				<p>Use this button to connect to Facebook. If you're already connected, this will give you a new access token.
				 I recommend clicking this button once every few weeks to make sure you always have a valid access token.</p>
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

	<div class="rfbp-sidebar clearfix">
		
		<div class="rfbp-box rfbp-well">
			<h3>Donate $10, $20 or $50</h3>
			<p>I spent countless hours developing this plugin, offering support, chasing bugs, etc. If you like it, consider showing me a token of your appreciation.</p>
					
			<div class="rfbp-donate">
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
			</div>

			<p>Alternatively, you can: </p>
            <ul class="ul-square">
                <li><a href="http://wordpress.org/support/view/plugin-reviews/recent-facebook-posts?rate=5#postform" target="_blank">Give a &#9733;&#9733;&#9733;&#9733;&#9733; rating on WordPress.org</a></li>
                <li><a href="http://dannyvankooten.com/wordpress-plugins/recent-facebook-posts/" target="_blank">Blog about it and link to the plugin page</a></li>
                <li><a href="http://twitter.com/?status=I%20show%20recent%20facebook%20posts%20on%20my%20%23WordPress%20site%20using%20Recent%20Facebook%20Posts%20by%20%40DannyvanKooten%20-%20love%20it!%20http%3A%2F%2Fwordpress.org%2Fplugins%2Frecent-facebook-posts%2F" target="_blank">Tweet about Recent Facebook Posts</a></li>
            	<li><a href="http://wordpress.org/plugins/recent-facebook-posts/#compatibility">Vote "works" on the WordPress.org plugin page</a></li>
            </ul>
        </div>

        <div class="rfbp-box">
        	<h3>Looking for support?</h3>
        	<p>Having trouble? Please use the <a href="http://wordpress.org/support/plugin/recent-facebook-posts">support forums</a> on WordPress.org.</p>
        	<p>Take a close look at the <a href="http://wordpress.org/plugins/recent-facebook-posts/installation/">installation instructions</a> for help configuring the plugin and registering your own Facebook application (required).</p>
        </div>

        <div class="rfbp-box">
        	<h3>Other Useful plugins</h3>
        	<ul class="ul-square">
        		<li><a href="http://wordpress.org/plugins/mailchimp-for-wp/">MailChimp for WordPress</a></li>
        		<li><a href="http://wordpress.org/plugins/wysiwyg-widgets/">WYSIWYG Widgets</a>
        		<li><a href="http://wordpress.org/plugins/newsletter-sign-up/">Newsletter Sign-Up</a></li>
        	</ul>
        </div>

        <div class="rfbp-box">
        	<h3>About the developer</h3>
        	<p>I am <a href="http://dannyvankooten.com/">Danny</a>, a young Dutch Computer Science student. I seriously enjoy coding, eating, sleeping, playing (sports) and happy clients.</p>
        	<p>I developed a few <a href="http://dannyvankooten.com/wordpress-plugins/">other WordPress plugins</a>, have a look.</p>
        	<p><em>PS: Donations are much appreciated!</em></p>
        </div>
	</div>
</div>