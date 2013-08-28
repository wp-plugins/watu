<div class="wrap">
	<h2><?php printf(__("Users who submitted the exam '%s'", 'watu'), $exam->name); ?></h2>
	
	<p><?php _e("A lot more detailed reports, filters, exports, and other important features are available in", 'watu')?> <a href="http://calendarscripts.info/watupro" target="_blank">WatuPRO</a></p>
	
	<p><a href="admin.php?page=watu_exams"><?php _e('Back to exams list', 'watu')?></a></p>	
	
	<div class="postbox-container" style="width:73%;margin-right:2%;">	
		<table class="widefat">
			<tr><th><?php _e('User or IP', 'watu')?></th><th><?php _e('Date', 'watu')?></th>
			<th><?php _e('Points', 'watu')?></th><th><?php _e('Result', 'watu')?></th></tr>
			
			<?php foreach($takings as $taking):?>
				<tr><td><?php echo $taking->user_id?'<a href="user-edit.php?user_id='.$taking->user_id.'">'.$taking->user_login.'</a>':$taking->ip?></td>
				<td><?php echo date(get_option('date_format'), strtotime($taking->date));?></td>
				<td><?php echo $taking->points?></td>
				<td><?php echo apply_filters('watu_content', $taking->result)?></td></tr>
			<?php endforeach;?>
		</table>
		
		<p align="center"><?php if($offset>0):?><a href="admin.php?page=watu_takings&exam_id=<?php echo $exam->ID?>&offset=<?php echo ($offset-10)?>"><?php _e('Previous page', 'watu')?></a><?php endif;?>
		
		<?php if($offset + 10 < $count):?> <a href="admin.php?page=watu_takings&exam_id=<?php echo $exam->ID?>&offset=<?php echo ($offset+10)?>"><?php _e('Next page', 'watu')?></a> <?php endif;?></p>
	</div>
	
	<div id="watu-sidebar">
			<?php include(WATU_PATH."/views/sidebar.php");?>
	</div>
</div>