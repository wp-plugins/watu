<?php
require('wpframe.php');
wpframe_stop_direct_call(__FILE__);

if( isset($_REQUEST['message']) && $_REQUEST['message'] == 'updated') wpframe_message(__('Test Updated'));
if(isset($_REQUEST['message']) && $_REQUEST['message'] == 'fail') wpframe_message(__('Error occured!!! please try again'));
if( isset($_REQUEST['grade']) ) wpframe_message($_REQUEST['grade']);

if($_REQUEST['action'] == 'delete') {
	$wpdb->get_results("DELETE FROM {$wpdb->prefix}watu_master WHERE ID='$_REQUEST[quiz]'");
	$wpdb->get_results("DELETE FROM {$wpdb->prefix}watu_answer WHERE question_id=(SELECT ID FROM {$wpdb->prefix}quiz_question WHERE quiz_id='$_REQUEST[quiz]')");
	$wpdb->get_results("DELETE FROM {$wpdb->prefix}watu_question WHERE quiz_id='$_REQUEST[quiz]'");
	wpframe_message(__("Test Deleted"));
}
?>

<div class="wrap">
<h2><?php _e("Manage Exam"); ?></h2>

	<div class="postbox-container" style="width:73%;margin-right:2%;">
	
	<p><strong><?php _e('Watu for Wordpress is a light version of')?> <a href="http://calendarscripts.info/watupro" target="_blank">WatuPRO</a>.</strong></p>
	
	<p><?php _e('Go to')?> <a href="options-general.php?page=watu.php"><?php _e('Watu Settings')?></a></p>
	
	<?php
	wp_enqueue_script( 'listman' );
	wp_print_scripts();
	?>
	
	<table class="widefat">
		<thead>
		<tr>
			<th scope="col"><div style="text-align: center;"><?php _e('ID') ?></div></th>
			<th scope="col"><?php _e('Title') ?></th>
			<th scope="col"><?php _e('Shortcode') ?></th>
			<th scope="col"><?php _e('Number Of Questions') ?></th>
			<th scope="col"><?php _e('Created on') ?></th>
			<th scope="col" colspan="3"><?php _e('Action') ?></th>
		</tr>
		</thead>
	
		<tbody id="the-list">
	<?php
	// Retrieve the quizes
	$all_test = $wpdb->get_results("SELECT Q.ID,Q.name,Q.added_on,(SELECT COUNT(*) FROM {$wpdb->prefix}watu_question WHERE exam_id=Q.ID) AS question_count	FROM `{$wpdb->prefix}watu_master` AS Q ");
	
	if(count($all_test)) {
		foreach($all_test as $quiz) {
			$class = ('alternate' == $class) ? '' : 'alternate';
	
			print "<tr id='quiz-{$quiz->ID}' class='$class'>\n";
			?>
			<th scope="row" style="text-align: center;"><?php echo $quiz->ID ?></th>
			<td><?php echo stripslashes($quiz->name)?></td>
			<td>[WATU <?php echo $quiz->ID?>]</td>
			<td><?php echo $quiz->question_count ?></td>
			<td><?php echo date(get_option('date_format') . ' ' . get_option('time_format'), strtotime($quiz->added_on)) ?></td>
			<td><a href='edit.php?page=watu/question.php&amp;quiz=<?php echo $quiz->ID?>' class='edit'><?php _e('Manage Questions')?></a></td>
			<td><a href='edit.php?page=watu/exam_form.php&amp;quiz=<?php echo $quiz->ID?>&amp;action=edit' class='edit'><?php _e('Edit'); ?></a></td>
			<td><a href='edit.php?page=watu/exam.php&amp;action=delete&amp;quiz=<?php echo $quiz->ID?>' class='delete' onclick="return confirm('<?php echo  addslashes(__("You are about to delete this quiz? This will delete all the questions and answers within this quiz. Press 'OK' to delete and 'Cancel' to stop."))?>');"><?php e('Delete')?></a></td>
			</tr>
	<?php
			}
		} else {
	?>
		<tr>
			<td colspan="5"><?php _e('No Test found.') ?></td>
		</tr>
	<?php
	}
	?>
		</tbody>
	</table>
	
	<a href="edit.php?page=watu/exam_form.php&amp;action=new"><?php _e("Create New Exam")?></a>
	</div>
	<div id="watu-sidebar">
			<?php require("sidebar.php");?>
	</div>
</div>	