<div class="wrap">
	<h2><?php printf(__("Manage %s", 'watu'), __('Quizzes', 'watu')); ?></h2>
	
		<div class="postbox-container" style="width:73%;margin-right:2%;">
		
		<p><strong><?php _e('Watu for Wordpress is a light version of', 'watu')?> <a href="http://calendarscripts.info/watupro" target="_blank">WatuPRO</a>.</strong></p>
		
		<p><?php _e('Go to', 'watu')?> <a href="options-general.php?page=watu.php"><?php _e('Watu Settings', 'watu')?></a>
			&nbsp;|&nbsp;
		<a href="admin.php?page=watu_exam&amp;action=new"><?php _e("Create New Quiz", 'watu')?></a>
			&nbsp;|&nbsp;
		<a href="admin.php?page=watu_social_sharing"><?php _e('Social Sharing Options', 'watu');?></a></p>
		
		<p><b><?php _e('To publish a quiz copy its shortcode and place it in a post or page. Use only one quiz shortcode in each post or page.','watu')?></b></p>
			
		<table class="widefat">
			<thead>
			<tr>
				<th scope="col"><div style="text-align: center;"><?php _e('ID', 'watu') ?></div></th>
				<th scope="col"><?php _e('Title', 'watu') ?></th>
				<th scope="col"><?php _e('Shortcode', 'watu') ?></th>
				<th scope="col"><?php _e('No. Questions', 'watu') ?></th>				
				<th scope="col"><?php _e('View Results', 'watu') ?></th>
				<th scope="col" colspan="3"><?php _e('Action', 'watu') ?></th>
			</tr>
			</thead>
		
			<tbody id="the-list">
		<?php
		if(count($exams)):
			foreach($exams as $quiz):
				$class = ('alternate' == @$class) ? '' : 'alternate';
		
				print "<tr id='quiz-{$quiz->ID}' class='$class'>\n";
				?>
				<th scope="row" style="text-align: center;"><?php echo $quiz->ID ?></th>
				<td><?php if(!empty($quiz->post)) echo "<a href='".get_permalink($quiz->post->ID)."' target='_blank'>"; 
				echo stripslashes($quiz->name);
				if(!empty($quiz->post)) echo "</a>";?></td>
        <td><input type="text" size="8" readonly onclick="this.select()" value="[WATU <?php echo $quiz->ID ?>]"></td>
				<td><?php echo $quiz->question_count ?></td>
				<td><a href="admin.php?page=watu_takings&exam_id=<?php echo $quiz->ID?>"><?php printf(__('Taken %d times', 'watu'), $quiz->taken)?></a></td>
				<td><a href='admin.php?page=watu_questions&amp;quiz=<?php echo $quiz->ID?>' class='edit'><?php _e('Manage Questions', 'watu')?></a><br>
				<a href='admin.php?page=watu_grades&amp;quiz_id=<?php echo $quiz->ID?>' class='edit'><?php _e('Manage Grades', 'watu')?></a></td>
				<td><a href='admin.php?page=watu_exam&amp;quiz=<?php echo $quiz->ID?>&amp;action=edit' class='edit'><?php _e('Edit', 'watu'); ?></a></td>
				<td><a href='tools.php?page=watu_exams&amp;action=delete&amp;quiz=<?php echo $quiz->ID?>' class='delete' onclick="return confirm('<?php echo  addslashes(__("You are about to delete this quiz? This will delete all the questions and answers within this quiz. Press 'OK' to delete and 'Cancel' to stop.", 'watu'))?>');"><?php _e('Delete', 'watu')?></a></td>
				</tr>
		<?php endforeach;
			else:?>
			<tr>
				<td colspan="5"><?php _e('No Quizzes found.', 'watu') ?></td>
			</tr>
		<?php endif;?>
			</tbody>
		</table>
		
			<p><a href="admin.php?page=watu_exam&amp;action=new"><?php _e("Create New Quiz", 'watu')?></a></p>
			
			<p><?php _e('Get free traffic to your quizzes by submitting them to', 'watu')?> <a href="http://calendarscripts.info/quizzes/" target="_blank"><?php _e('our directory.')?></a></p>
		</div>
		<div id="watu-sidebar">
				<?php include(WATU_PATH."/views/sidebar.php");?>
		</div>
	</div>	