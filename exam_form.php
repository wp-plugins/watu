<?php
require('wpframe.php');
wpframe_stop_direct_call(__FILE__);

$action = 'new';
if($_REQUEST['action'] == 'edit') $action = 'edit';

$dquiz = array();
$grades = array();
if($action == 'edit') {
	$dquiz = $wpdb->get_row($wpdb->prepare("SELECT name,description,final_screen FROM {$wpdb->prefix}watu_master WHERE ID=%d", $_REQUEST['quiz']));
	$grades = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watu_grading WHERE  exam_id=%d order by ID ", $_REQUEST['quiz']) );
	$final_screen = stripslashes($dquiz->final_screen);
} else {
	$final_screen = __("<p>Congratulations - you have completed %%QUIZ_NAME%%.</p>\n\n<p>You scored %%SCORE%% points out of %%TOTAL%%.</p>\n\n<p>Your performance have been rated as '%%RATING%%'</p>\n\n<p>Your obtained grade is '%%GRADE%%'</p>");
}

?>
<script type="text/javascript">
	function addGrade(){
		var newGrade = "<p><img class='gradeclose' onclick='jQuery(this).parent().remove();' src='<?php echo $GLOBALS['wpframe_plugin_folder'] ?>/close.jpg' alt='close' /><label><?php _e('Grade Title:')?></label><input type='text' name='gradetitle[]' id='title' value='' /><br /><label><?php _e('Description:')?></label><br /><textarea name='grade_description[]' rows='5'></textarea><br /><label><?php _e('From (points):')?> <input type='text' class='numeric' name='grade_from[]' value='' /></label><label><?php _e('To (points):')?> <input type='text'  name='grade_to[]' class='numeric' value='' /></label></p>";
		
		jQuery('#gradecontent').append(newGrade);		
	}
	function validate() {
		var ret= true;

		jQuery('.numeric').each(function(){			
			var valid = ( (this.value>=0 || this.value<0) && !/^\s*$/.test(this.value) );
			if(!valid) jQuery(this).css({'background-color': '#faa'});
			if(ret) ret=valid;
		});
		if(!ret) alert('<?php _e("Please provide numeric values for the Grade from/to.")?>');
		return ret;
	}
</script>
<div class="wrap">
<h2><?php _e(ucfirst($action) . " Exam"); ?></h2>

<p><a href="tools.php?page=watu/exam.php"><?php _e('Back to exams')?></a></p>

<?php
wpframe_add_editor_js();
?>

<form name="post" action="<?php echo $GLOBALS['wpframe_plugin_folder'] ?>/exam_action.php" method="post" id="post" onsubmit="return validate()">
<div id="poststuff">

<div class="postbox" id="titlediv">
<h3 class="hndle"><span><?php _e('Exam Name') ?></span></h3>
<div class="inside">
<input type='text' name='name' id="title" value='<?php echo stripslashes($dquiz->name); ?>' />
</div></div>

<div class="postbox">
<h3 class="hndle"><span><?php _e('Description') ?></span></h3>
<div class="inside">
<textarea name='description' rows='5' cols='50' style='width:100%'><?php echo stripslashes($dquiz->description); ?></textarea>
</div></div>

<style type="text/css"> #gradecontent p{border-bottom:1px dotted #ccc;padding-bottom:3px;} #gradecontent label{padding: 5px 10px;} #gradecontent textarea{width:96%;margin-left:10px;} #gradecontent p img.gradeclose{ border:0 none; float:right; } </style>
<div class="postbox" id="gradediv">
<h3 class="hndle"><span><?php _e('Grading') ?></span></h3>
<div class="inside" id="gradecontent">
<?php 
	foreach($grades as $row ) {
?>
<p><img class="gradeclose" onclick="jQuery(this).parent().remove();" src="<?php echo $GLOBALS['wpframe_plugin_folder'] ?>/close.jpg" alt='close' /><label><?php _e('Grade Title:')?></label><input type='text' name='gradetitle[]' id="title" value='<?php echo stripslashes($row->gtitle); ?>' /><br /><label><?php _e('Description:')?></label><br /><textarea name='grade_description[]' rows='5'><?php echo stripslashes($row->gdescription); ?></textarea><br /><label><?php _e('Grade from:')?> <input type='text' class='numeric' name='grade_from[]' value='<?php echo stripslashes($row->gfrom); ?>' /></label><label><?php _e('Grade to:')?> <input type='text' class='numeric' name='grade_to[]' value='<?php echo stripslashes($row->gto); ?>' /></label></p>
<?php }
if( count($grades)==0 ){
 ?>
 <p><img class="gradeclose" onclick="jQuery(this).parent().remove();" src="<?php echo $GLOBALS['wpframe_plugin_folder'] ?>/close.jpg" alt='close' /><label><?php _e('Grade Title:')?></label><input type='text' name='gradetitle[]' id="title" value='' /><br /><label><?php _e('Description:')?></label><br /><textarea name='grade_description[]' rows='5'></textarea><br /><label>Grade from:<input type='text' class='numeric' name='grade_from[]' value='' /></label><label><?php _e('Grade to:')?><input type='text' class='numeric' name='grade_to[]' value='' /></label></p>
 <?php } ?>
</div><p><a href="javascript:;" onclick="addGrade()" style="padding:4px; margin-left:10px;"><?php _e('Add another grade')?></a></p></div>

<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea postbox">
<h3 class="hndle"><span><?php _e('Final Screen') ?></span></h3>
<div class="inside">
<?php wp_editor($final_screen, 'content'); ?>

<p><strong><?php _e('Usable Variables:') ?></strong></p>
<table>
<tr><th style="text-align:left;"><?php _e('Variable') ?></th><th style="text-align:left;"><?php _e('Value') ?></th></tr>
<tr><td>%%SCORE%%</td><td><?php _e('The number of points collected.') ?></td></tr>
<tr><td>%%TOTAL%%</td><td><?php _e('Maximum number of points') ?></td></tr>
<tr><td>%%GRADE%%</td><td><?php _e('1-10 value. 1 is 10% or less, 2 is 20% or less, and so on') ?>.</td></tr>
<tr><td>%%WRONG_ANSWERS%%</td><td><?php _e('Number of answers you got wrong') ?></td></tr>
<tr><td>%%RATING%%</td><td><?php _e("A rating of your performance - it could be 'Failed'(0-39%), 'Just Passed'(40%-50%), 'Satisfactory', 'Competent', 'Good', 'Excellent' and 'Unbeatable'(100%)") ?></td></tr>
<tr><td>%%QUIZ_NAME%%</td><td><?php _e('The name of the quiz') ?></td></tr>
<tr><td>%%DESCRIPTION%%</td><td><?php _e('The text entered in the description field.') ?></td></tr>
</table>
</div>
</div>


<?php
// I'll put 2 editors here - as soon as 'http://wordpress.org/support/topic/179110?replies=2' bug is fixed.
?>


<p class="submit">
<?php wp_nonce_field('watu_create_edit_quiz'); ?>
<input type="hidden" name="action" value="<?php echo $action; ?>" />
<input type="hidden" name="quiz" value="<?php echo $_REQUEST['quiz']; ?>" />
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<span id="autosave"></span>
<input type="submit" name="submit" value="<?php _e('Save') ?>" style="font-weight: bold;" tabindex="4" />
</p>

</div>
</form>

</div>
