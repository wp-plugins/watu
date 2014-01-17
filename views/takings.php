<div class="wrap">
	<h2><?php printf(__("Users who submitted the exam '%s'", 'watu'), $exam->name); ?></h2>
	
	<p><?php _e("A lot more detailed reports, filters, exports, and other important features are available in", 'watu')?> <a href="http://calendarscripts.info/watupro" target="_blank">WatuPRO</a></p>
	
	<p><a href="admin.php?page=watu_exams"><?php _e('Back to exams list', 'watu')?></a>
	<?php if($count):?>&nbsp;|&nbsp;
	<a href="admin.php?page=watu_takings&exam_id=<?php echo $exam->ID?>&watu_export=1&noheader=1"><?php _e('Export as CSV (semicolon delimited)', 'watu');?></a>
	&nbsp;|&nbsp;
	<a href="#" onclick="WatuDelAll();return false;"><?php _e('Delete all user-submitted data for this exam', 'watu')?></a><?php endif;?></p>	

	<div class="postbox-container" style="width:73%;margin-right:2%;">	
		<?php if($count):?>
		<table class="widefat wp-list-table">
			<tr><th><?php _e('User or IP', 'watu')?></th><th><?php _e('Date', 'watu')?></th>
			<th><?php _e('Points', 'watu')?></th><th><?php _e('Result', 'watu')?></th>
			<th><?php _e('Delete', 'watu')?></th></tr>
			
			<?php foreach($takings as $taking):?>
				<tr><td><?php echo $taking->user_id?'<a href="user-edit.php?user_id='.$taking->user_id.'">'.$taking->user_login.'</a>':$taking->ip?></td>
				<td><?php echo date(get_option('date_format'), strtotime($taking->date));?></td>
				<td><?php echo $taking->points?></td>
				<td><?php echo apply_filters('watu_content', $taking->result)?></td>
				<td><a href="#" onclick="WatuDelTaking(<?php echo $taking->ID?>);return false;"><?php _e('Delete', 'watu')?></a></td></tr>
			<?php endforeach;?>
		</table>
		
		<p align="center"><?php if($offset>0):?><a href="admin.php?page=watu_takings&exam_id=<?php echo $exam->ID?>&offset=<?php echo ($offset-10)?>"><?php _e('Previous page', 'watu')?></a><?php endif;?>
		
		<?php if($offset + 10 < $count):?> <a href="admin.php?page=watu_takings&exam_id=<?php echo $exam->ID?>&offset=<?php echo ($offset+10)?>"><?php _e('Next page', 'watu')?></a> <?php endif;?></p>
		
		<?php else:?>
			<p><?php _e('No user has taken this quiz yet.','watu')?></p>
		<?php endif;?>
	</div>	
	
	<div id="watu-sidebar">
			<?php include(WATU_PATH."/views/sidebar.php");?>
	</div>
	
	<form id="cleanupTakingsForm" method="post">
		<input type="hidden" name="delete_all_takings" value="0">
	</form>
</div>

<script type="text/javascript" >
function WatuDelTaking(id) {
	if(confirm("<?php _e('Are you sure?', 'watu')?>")) {
		window.location = 'admin.php?page=watu_takings&exam_id=<?php echo $exam->ID?>&del_taking=1&id=' + id;
	} 
}

function WatuDelAll() {
	if(!confirm("<?php _e('Are you sure? This will delete ALL user results for this quiz!', 'watu')?>")) return false;
	
	jQuery('#cleanupTakingsForm input[name=delete_all_takings]').val("1");
	jQuery('#cleanupTakingsForm').submit();
}
</script>