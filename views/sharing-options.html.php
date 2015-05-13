<div class="wrap">
	<h1><?php _e('Watu Social Sharing', 'watu')?></h1>
	
	<p><a href="options-general.php?page=watu.php"><?php _e('Back to Watu Options','watu');?></a></p>
	
	<p><?php _e('You can use the shortcode [watushare-buttons] to display Facebook share button on the "Final screen" on your quiz.', 'watu')?></b></p>
	<p><?php printf(__('The social media buttons are provided by <a href="%s" target="_blank">Arbenting</a>. Feel free to replace them with other icons.', 'watu'), 'http://arbent.net/blog/social-media-circles-icon-set')?></p>
	
	<form method="post">
		<h2><?php _e('Facebook Sharing', 'watu')?></h2>
		
		<p><label><?php _e('Your Facebook App ID:', 'watu')?></label> <input type="text" name="facebook_appid" value="<?php echo $appid?>"> <a href="https://developers.facebook.com/apps" target="_blank"><?php _e('Get one here', 'watu')?></a></p>
		<p><?php _e('If you leave it empty, no Facebook share button will be generated.', 'watu')?></p>
		
			<p><?php _e('Title:', 'watu')?> <input type="text" name="linkedin_title" value="<?php echo stripslashes(@$linkedin_options['title'])?>" size="40">
			<p><?php _e('Text:', 'watu')?> <textarea name="linkedin_msg" rows="4" cols="60"><?php echo stripslashes(@$linkedin_options['msg'])?></textarea>
			<br> <?php _e('You can use the variables {{{quiz-name}}}, {{{url}}}, {{{grade-title}}} and {{{grade-description}}}.', 'watu')?>
			<br>					
			<p><?php _e('If you leave title and text empty, grade title and grade description will be used respectively.', 'watu')?></p>	
			
			<p><b><?php _e('IMPORTANT: Facebook needs to be able to access your site to retrieve the social sharing data. If the site is on localhost or behind a htaccess login box sharing will not work properly.', 'watu')?></b></p>
			
			<p><?php printf(__('More social sharing options like Google Plus, LinkedIn, Twitter and Email are available in <a href="%s" target="_blank">WatuPRO</a>', 'watu'), 'http://calendarscripts.info/watupro');?></p>
		
		<p><input type="submit" value="<?php _e('Save All Settings', 'watu')?>"></p>
		<input type="hidden" name="ok" value="1">
	</form>
</div>