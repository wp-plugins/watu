<?php
if(isset($_REQUEST['submit']) and $_REQUEST['submit']) {
	
	update_option( "watu_delete_db", @$_POST['delete_db'] );
	update_option('watu_delete_db_confirm', $_POST['delete_db_confirm']);
		
	$options = array('answer_type', 'use_the_content', 'text_captcha');
	foreach($options as $opt) {
		if(!empty($_POST[$opt])) update_option('watu_' . $opt, $_POST[$opt]);
		else update_option('watu_' . $opt, 0);
	}
	print '<div id="message" class="updated fade"><p>' . __('Options updated', 'watu') . '</p></div>';	
}
$answer_display = get_option('watu_show_answers');
$delete_db = get_option('watu_delete_db');

$text_captcha = get_option('watu_text_captcha');
// load 3 default questions in case nothing is loaded
if(empty($text_captcha)) {
	$text_captcha = __('What is the color of the snow? = white', 'watu').PHP_EOL.__('Is fire hot or cold? = hot', 'watu') 
		.PHP_EOL. __('In which continent is Norway? = Europe', 'watu'); 
}
?>
<div class="wrap">

	<h2><?php _e("Watu Settings", 'watu'); ?></h2>

	<div class="postbox-container" style="width:73%;margin-right:2%;">	
	
	<p><?php _e('Go to', 'watu')?> <a href="tools.php?page=watu_exams"><?php _e('Manage Your Exams', 'watu')?></a>
	&nbsp;|&nbsp; <a href="admin.php?page=watu_social_sharing"><?php _e('Social Sharing Options', 'watu');?></a></p>
	
	<form name="post" action="" method="post" id="post">
	<div>
		<div class="postarea">
			<div class="postbox">
			<h3 class="hndle">&nbsp;<span><?php _e('Default Answer Type', 'watu') ?></span></h3>
			<div class="inside" style="padding:8px">
			<?php 
				$single = $multi = '';
				if( get_option('watu_answer_type') =='radio') $single='checked="checked"';
				else $multi = 'checked="checked"';
			?>
			<label>&nbsp;<input type='radio' name='answer_type' <?php print $single?> id="answer_type_r" value='radio' /> <?php _e('Single Answer', 'watu')?> </label>
			&nbsp;&nbsp;&nbsp;
			<label>&nbsp;<input type='radio' name='answer_type' <?php print $multi?> id="answer_type_c" value='checkbox' /> <?php _e('Multiple Answers', 'watu')?></label>
			</div></div>
			
	
	<div class="postbox wp-admin" style="padding:5px;">
	<h3><?php _e('Question based captcha', 'watu')?></h3>
	
	<p><?php _e("You can use a simple text-based captcha. We have loaded 3 basic questions but you can edit them and load your own. Make sure to enter only one question per line and use = to separate question from answer.", 'watu')?></p>
	
	<p><textarea name="text_captcha" rows="10" cols="70"><?php echo stripslashes($text_captcha);?></textarea></p>
	<div class="help"><?php _e('This question-based captcha can be enabled individually by selecting a checkbox in the quiz settings form. If you do not check the checkbox, the captcha question will not be generated.', 'watu');?></div>	
</div>		
	
	<div class="postbox">
		<h3 class="hndle">&nbsp;<span><?php _e('Other settings', 'watu') ?></span></h3>
		<div class="inside" style="padding:8px">
			<label>&nbsp;<input type='checkbox' value="1" name='use_the_content' <?php if(get_option('watu_use_the_content')) echo 'checked'?>  />&nbsp;<?php _e('Use "the_content" instead of our custom content filter (do not select this unless adviced so)', 'watu')?> </label>
		</div>
	</div>
	
	<div class="postbox">
		<h3 class="hndle">&nbsp;<span><?php _e('Database Option', 'watu') ?></span></h3>
		<div class="inside" style="padding:8px">
		<?php 
			$check = get_option('watu_delete_db');
		?>
		<label>&nbsp;<input type='checkbox' value="1" name='delete_db' <?php if($delete_db) echo 'checked'?> onclick="this.checked ? jQuery('#deleteDBConfirm').show() : jQuery('#deleteDBConfirm').hide();" />&nbsp;<?php _e('Delete stored Watu data when deinstalling the plugin.', 'watu')?> </label>
		
			<span id="deleteDBConfirm" style="display: <?php echo empty($delete_db) ? 'none' : 'inline';?>">
				<?php _e('Please confirm by typing "yes" in the box:', 'watu')?> <input type="text" name="delete_db_confirm" value="<?php echo get_option('watu_delete_db_confirm')?>">		
			</span>
		</div>
	</div>
		
	<p class="submit">
	<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
	<span id="autosave"></span>
	<input type="submit" name="submit" value="<?php _e('Save Options', 'watu') ?>" style="font-weight: bold;" />
	</p>
	
	</div>
</div>
	
	
	</form>
	
	</div>
	<div id="watu-sidebar">
			<?php include(WATU_PATH."/views/sidebar.php");?>
	</div>
</div>	

<?php
function showOption($option, $title) {
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option?>" <?php if(get_option('watu_'.$option)) print " checked='checked'"; ?> />
<label for="<?php echo $option?>"><?php e($title) ?></label><br />

<?php
}
