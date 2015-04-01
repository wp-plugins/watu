<script type="text/javascript">
	function validate() {
		var ret= true;
		return ret;
	}
</script>

<div class="wrap">
<h2><?php _e(ucfirst($action) . " Quiz", 'watu'); ?></h2>

<div class="postbox-container" style="width:73%;margin-right:2%;">	

	<p><a href="tools.php?page=watu_exams"><?php _e('Back to quizzes', 'watu')?></a>
	<?php if(!empty($dquiz->ID)):?>
		| <a href="admin.php?page=watu_questions&quiz=<?php echo $dquiz->ID?>"><?php _e('Manage Questions', 'watu')?></a>
		| <a href="admin.php?page=watu_grades&quiz_id=<?php echo $dquiz->ID?>"><?php _e('Manage Grades / Results', 'watu')?></a>
	<?php endif;?>	
	</p>
	
	<form name="post" action="admin.php?page=watu_exam" method="post" id="post" onsubmit="return validate()">
	<div>	
	<div class="postbox wrap" id="titlediv">
		<h3>&nbsp;<?php printf(__('%s Name and Settings', 'watu'), __('Quiz', 'watu'))?></h3>
		<div class="inside wrap">
			<input type='text' name='name' id="title" value='<?php echo stripslashes(@$dquiz->name); ?>' />
		</div>
		<div class="inside">
			<?php if(!$is_published):?>
			 	<p><input type="checkbox" name="auto_publish" value="1"> <?php _e('Automatically publish this quiz in new post once I hit the "Save" button. (The new post will be auto-generated with the quiz title used for post title.)', 'watu')?></p>
			 <?php endif;?>		
			<p><input id="watuRandomize" type="checkbox" name="randomize" value="1" <?php if(!empty($dquiz->randomize)) echo 'checked'?>> <label for="watuRandomize"><?php _e("Randomize questions", 'watu');?></label> <br> <?php _e('and pull random', 'watu')?>
			<input type="text" name="pull_random" value="<?php echo intval(@$dquiz->pull_random)?>" size="4" onkeyup="if(this.value > 0) { jQuery('#watuRandomize').attr('checked', true);}"> <?php _e('questions from the quiz. Leave 0 to pull all the questions (default behavior).','watu')?></p>
			
			<p><input id="watuRandomize" type="checkbox" name="randomize_answers" value="1" <?php if(!empty($dquiz->randomize_answers)) echo 'checked'?>> <label for="watuRandomize"><?php _e("Randomize answers", 'watu');?></label></p>
			
			<p><?php _e('Note: randomization will not work well if you are caching the page where your quiz is published!', 'watu')?></p>
			
			<p><input id="watuSingle" type="checkbox" name="single_page" value="1" <?php if(!empty($dquiz->single_page)) echo 'checked'?> onclick="this.checked ? jQuery('#watuPrevButton').hide() : jQuery('#watuPrevButton').show();"> <label for="watuSingle"><?php _e("Show all questions on single page", 'watu');?></label></p>
			
			<p style="display:<?php echo empty($dquiz->single_page) ? 'block' : 'none';?>" id="watuPrevButton"><input id="watuPrev" type="checkbox" name="show_prev_button" value="1" <?php if(!empty($dquiz->show_prev_button)) echo 'checked'?>> <label for="watuPrev"><?php _e("Show 'previous question' button", 'watu');?></label></p>
			
			<p><input type="checkbox" name="require_login" value="1" <?php if(!empty($dquiz->require_login)) echo 'checked'?> id="watuReqLogin"> <label for="watuReqLogin"><?php _e('Require user login (displays login and / or register link depending on your blog settings.)', 'watu')?></label></p>
			
			<p><input type="checkbox" name="notify_admin" value="1" <?php if(!empty($dquiz->notify_admin)) echo 'checked'?> id="watuNotifyAdmin"> <label for="watuNotifyAdmin"><?php _e('Notify me when someone takes this quiz (the email goes to the address given in your WordPress Settings page).', 'watu')?></label></p>
			
			<p><input type="checkbox" name="dont_store_data" value="1" <?php if(!empty($dquiz->dont_store_data)) echo 'checked'?> id="watuDontStoreData"> <label for="watuDontStoreData"><?php _e('Do not store user answers in the database to save DB space.', 'watu')?></label></p>
			
			<p><input type="checkbox" name="dont_display_question_numbers" value="1" <?php if(!empty($dquiz->dont_display_question_numbers)) echo 'checked'?> id="watuDontDisplayNumbers"> <label for="watuDontDisplayNumbers"><?php _e('Do not display question numbers.', 'watu')?></label></p>
		</div>
	</div>
	
	<div class="postbox">
		<h3>&nbsp;<?php _e('Correct Answer Display', 'watu') ?></h3>
		<div class="inside">				
			<input type="checkbox" name="show_answers" <?php if($answer_display == '2') echo 'checked="checked"'; ?> value="2" id="show-between" /> <label for="show-between"><?php _e("Show the answer of a question immediately after the user have selected an answer. (Will not work in single page mode and <b>is not secure</b> - use it only for fun quizzes, not exams. You can handle exams in this mode with WatuPRO.)", 'watu') ?></label><br />
			<label for="show-end"><?php _e('"<b>Show answers at the end of the Quiz" is now obsolete option.</b> Instead of using it, enter the %%ANSWERS%% variable where you want the results to appear.', 'watu') ?></label><br />
		</div>
	</div>
	
	<div class="postbox">
	<h3>&nbsp;<?php _e('Description', 'watu') ?></h3>
	<div class="inside">
	<textarea name='description' rows='5' cols='50' style='width:100%'><?php echo stripslashes(@$dquiz->description); ?></textarea>
	<p><?php _e('If provided, description shows on top of the quiz. It can optionally be included in the final output as well.', 'watu')?></p>
	</div></div>
	
	
	<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea postbox">
	<h3>&nbsp;<?php _e('Final Screen', 'watu') ?></h3>
	<div class="inside">
	<?php wp_editor($final_screen, 'content'); ?>
	
	<p><strong><?php _e('Usable Variables:', 'watu') ?></strong></p>
	<table>
	<tr><th style="text-align:left;"><?php _e('Variable', 'watu') ?></th><th style="text-align:left;"><?php _e('Value', 'watu') ?></th></tr>
	<tr><td>%%ANSWERS%%</td><td><?php _e('This will display the questions along with the answers that user gave to them marked as correct or incorrect.', 'watu') ?></td></tr>
	<tr><td>%%POINTS%%</td><td><?php _e('The number of points collected. (The old %%SCORE%% tag also works)', 'watu') ?></td></tr>
	<tr><td>%%MAX-POINTS%%</td><td><?php _e('Maximum number of points', 'watu') ?></td></tr>
	<tr><td>%%GRADE%%</td><td><?php _e('Shows the achieved grade - title and description together. If you want to design this better, you can use %%GRADE-TITLE%% for grade title and %%GRADE-DESCRIPTION%% for grade description', 'watu') ?>.</td></tr>
	<tr><td>%%CORRECT%%</td><td><?php _e('Number of correct answers. In multiple-select questions even one correct answer makes the question correct. In WatuPRO this is configurable', 'watu')?></td></tr>
	<tr><td>%%WRONG_ANSWERS%%</td><td><?php _e('Number of answers you got wrong', 'watu') ?></td></tr>
	<tr><td>%%RATING%%</td><td><?php _e("A rating of your performance - it could be 'Failed'(0-39%), 'Just Passed'(40%-50%), 'Satisfactory', 'Competent', 'Good', 'Excellent' and 'Unbeatable'(100%).", 'watu') ?> <?php printf(__('The rating can be changed only by <a href="%s" target="_blank">translating the plugin</a>. So we recommend you to use the grades instead. They are fully configurable right from this page.', 'watu'), 'http://blog.calendarscripts.info/how-to-translate-a-wordpress-plugin/')?></td></tr>
	<tr><td>%%QUIZ_NAME%%</td><td><?php _e('The name of the quiz', 'watu') ?></td></tr>
	<tr><td>%%DESCRIPTION%%</td><td><?php _e('The text entered in the description field.', 'watu') ?></td></tr>
	<tr><td>%%AVG_POINTS%%</td><td><?php _e('The average points earned by the other quiz participants.', 'watu') ?></td></tr>
	<tr><td>%%BETTER-THAN%%</td><td><?php _e('Shows the percentage of users collected less points on the quiz.', 'watu'); ?></td></tr>
	</table>
	</div>
	</div>
	
	<p class="submit">
	<?php wp_nonce_field('watu_create_edit_quiz'); ?>
	<input type="hidden" name="action" value="<?php echo $action; ?>" />
	<input type="hidden" name="quiz" value="<?php echo $_REQUEST['quiz']; ?>" />
	<input type="hidden" id="user-id" name="user_ID" value="<?php echo $user_ID ?>" />
	<span id="autosave"></span>
	<input type="submit" name="submit" value="<?php _e('Save', 'watu') ?>" style="font-weight: bold;" tabindex="4" />
	</p>
	
	</div>
	</form>
	
	</div>
	
	<div id="watu-sidebar">
			<?php include(WATU_PATH."/views/sidebar.php");?>
	</div>
</div>