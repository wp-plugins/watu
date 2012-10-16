<?php
require('wpframe.php');

if(isset($_REQUEST['submit']) and $_REQUEST['submit']) {
	if( isset( $_REQUEST['watu_delete_db'] ) ) update_option( "watu_delete_db", 'checked="checked"' );
	$options = array('show_answers', 'single_page', 'answer_type');
	foreach($options as $opt) {
		if(!empty($_POST[$opt])) update_option('watu_' . $opt, $_POST[$opt]);
		else update_option('watu_' . $opt, 0);
	}
	wpframe_message(__("Options updated", 'watu'));
}
$answer_display = get_option('watu_show_answers');
?>
<div class="wrap">

	<h2><?php _e("Watu Settings", 'watu'); ?></h2>

	<div class="postbox-container" style="width:73%;margin-right:2%;">	
	
	<p><?php _e('Go to', 'watu')?> <a href="tools.php?page=watu/exam.php"><?php _e('Manage Your Exams', 'watu')?></a></p>
	
	<form name="post" action="" method="post" id="post">
	<div id="poststuff">
	<div id="postdiv" class="postarea">
	
	<?php showOption('single_page', __('Show all questions in a <strong>single page</strong>', 'watu')); ?><br />
	
	<div class="postbox">
	<h3 class="hndle"><span><?php _e('Correct Answer Display', 'watu') ?></span></h3>
	<div class="inside">
	<input type="radio" name="show_answers" <?php if($answer_display == '0') echo 'checked="checked"'; ?> value="0" id="no-show" /> <label for="no-show"><?php _e("Don't show answers", 'watu') ?></label><br />
	<input type="radio" name="show_answers" <?php if($answer_display == '1') echo 'checked="checked"'; ?> value="1" id="show-end" /> <label for="show-end"><?php _e("Show answers at the end of the Quiz", 'watu') ?></label><br />
	<input type="radio" name="show_answers" <?php if($answer_display == '2') echo 'checked="checked"'; ?> value="2" id="show-between" /> <label for="show-between"><?php _e("Show the answer of a question immediately after the user have selected an answer(Will not work in single page mode).", 'watu') ?></label><br />
	</div>
	</div>
	
	<div class="postbox">
	<h3 class="hndle"><span><?php _e('Answer Type', 'watu') ?></span></h3>
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
	
	<div class="postbox">
	<h3 class="hndle"><span><?php _e('Database Option', 'watu') ?></span></h3>
	<div class="inside" style="padding:8px">
	<?php 
		$check = get_option('watu_delete_db');
	?>
	<label>&nbsp;<input type='checkbox' name='watu_delete_db' <?php print get_option('watu_delete_db') ?> value='checked="checked"' />&nbsp;<?php _e('Delete stored Watu data when deactivating the plugin', 'watu')?> </label>
	</div></div>
	
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
			<?php require("sidebar.php");?>
	</div>
</div>	

<?php
function showOption($option, $title) {
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option?>" <?php if(get_option('watu_'.$option)) print " checked='checked'"; ?> />
<label for="<?php echo $option?>"><?php e($title) ?></label><br />

<?php
}
