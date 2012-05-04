<?php
require('wpframe.php');

if(isset($_REQUEST['submit']) and $_REQUEST['submit']) {
	if( isset( $_REQUEST['watu_delete_db'] ) ) update_option( "watu_delete_db", 'checked="checked"' );
	$options = array('show_answers', 'single_page', 'answer_type');
	foreach($options as $opt) {
		if(!empty($_POST[$opt])) update_option('watu_' . $opt, $_POST[$opt]);
		else update_option('watu_' . $opt, 0);
	}
	wpframe_message("Options updated");
}
$answer_display = get_option('watu_show_answers');
?>
<div class="wrap">
<h2><?php e("Watu Settings"); ?></h2>

<p>Watu for Wordpress is a light version of <a href="http://calendarscripts.info/watupro/" target="_blank">WatuPRO</a>. Check it if you want to run fully featured exams with data exports, student logins, categories etc.</p>

<p>Go to <a href="tools.php?page=watu/exam.php">Manage Your Exams</a></p>

<form name="post" action="" method="post" id="post">
<div id="poststuff">
<div id="postdiv" class="postarea">

<?php showOption('single_page', 'Show all questions in a <strong>single page</strong>'); ?><br />

<div class="postbox">
<h3 class="hndle"><span><?php e('Correct Answer Display') ?></span></h3>
<div class="inside">
<input type="radio" name="show_answers" <?php if($answer_display == '0') echo 'checked="checked"'; ?> value="0" id="no-show" /> <label for="no-show"><?php e("Don't show answers") ?></label><br />
<input type="radio" name="show_answers" <?php if($answer_display == '1') echo 'checked="checked"'; ?> value="1" id="show-end" /> <label for="show-end"><?php e("Show answers at the end of the Quiz") ?></label><br />
<input type="radio" name="show_answers" <?php if($answer_display == '2') echo 'checked="checked"'; ?> value="2" id="show-between" /> <label for="show-between"><?php e("Show the answer of a question immidiatly after the user have selected an answer(Will not work in single page mode).") ?></label><br />
</div>
</div>

<div class="postbox">
<h3 class="hndle"><span><?php e('Answer Type') ?></span></h3>
<div class="inside" style="padding:8px">
<?php 
	$single = $multi = '';
	if( get_option('watu_answer_type') =='radio') $single='checked="checked"';
	else $multi = 'checked="checked"';
?>
<label>&nbsp;<input type='radio' name='answer_type' <?php print $single?> id="answer_type_r" value='radio' />Single Answer </label>
&nbsp;&nbsp;&nbsp;
<label>&nbsp;<input type='radio' name='answer_type' <?php print $multi?> id="answer_type_c" value='checkbox' />Multiple Answers</label>
</div></div>

<div class="postbox">
<h3 class="hndle"><span><?php e('Database Option') ?></span></h3>
<div class="inside" style="padding:8px">
<?php 
	$check = get_option('watu_delete_db');
?>
<label>&nbsp;<input type='checkbox' name='watu_delete_db' <?php print get_option('watu_delete_db') ?> value='checked="checked"' />&nbsp;Delete all databases when deactivating the plugin </label>
</div></div>

<p class="submit">
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<span id="autosave"></span>
<input type="submit" name="submit" value="<?php e('Save Options') ?>" style="font-weight: bold;" />
</p>

</div>
</div>


</form>

</div>

<?php
function showOption($option, $title) {
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option?>" <?php if(get_option('watu_'.$option)) print " checked='checked'"; ?> />
<label for="<?php echo $option?>"><?php e($title) ?></label><br />

<?php
}
