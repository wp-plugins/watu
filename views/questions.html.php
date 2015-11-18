<div class="wrap">
	<h2><?php echo __("Manage Questions in", 'watu') . ' ' . $exam_name; ?></h2>
	
		<div class="postbox-container" style="width:73%;margin-right:2%;">
		
		<p><a href="tools.php?page=watu_exams"><?php _e('Back to quizzes', 'watu')?></a> &nbsp; <a href="admin.php?page=watu_exam&quiz=<?php echo $_GET['quiz']?>&action=edit"><?php _e('Edit this quiz', 'watu')?></a> &nbsp;
		<a href="admin.php?page=watu_grades&quiz_id=<?php echo $_GET['quiz']?>"><?php _e('Manage Grades / Results', 'watu')?></a></p>
		
		<?php
		wp_enqueue_script( 'listman' );
		wp_print_scripts();
		?>
		
		<p style="color:green;"><?php _e('To add this exam to your blog, insert the code ', 'watu') ?> <input type="text" readonly size="8" onclick="this.select();" value="[WATU <?php echo $_REQUEST['quiz'] ?>]"> <?php _e('into any post or page.', 'watu') ?></p>
		
		<table class="widefat">
			<thead>
			<tr>
				<th scope="col"><div style="text-align: center;">#</div></th>
				<th scope="col"><?php _e('Question', 'watu') ?></th>
				<th scope="col"><?php _e('Number Of Answers', 'watu') ?></th>
				<th scope="col" colspan="3"><?php _e('Action', 'watu') ?></th>
			</tr>
			</thead>
		
			<tbody id="the-list">
		<?php
									
		
		if (count($all_question)) {
			$bgcolor = '';			
			$question_count = 0;
			foreach($all_question as $question) {
				$class = ('alternate' == @$class) ? '' : 'alternate';
				$question_count++;
				print "<tr id='question-{$question->ID}' class='$class'>\n";
				?>
				<td style="text-align: center;">
				<div style="float:left;<?php if(!empty($_POST['filter_cat_id'])) echo 'display:none;'?>">
				<?php if(($question_count+$offset)>1):?>
					<a href="admin.php?page=watu_questions&quiz=<?php echo $_GET['quiz']?>&move=<?php echo $question->ID?>&dir=up"><img src="<?php echo  WATU_URL.'/img/arrow-up.png'?>" alt="<?php _e('Move Up', 'watu')?>" border="0"></a>
				<?php else:?>&nbsp;<?php endif;?>
				<?php if(($question_count+$offset) < $num_questions):?>	
					<a href="admin.php?page=watu_questions&quiz=<?php echo $_GET['quiz']?>&move=<?php echo $question->ID?>&dir=down"><img src="<?php echo  WATU_URL.'/img/arrow-down.png'?>" alt="<?php _e('Move Down', 'watu')?>"></a>
				<?php else:?>&nbsp;<?php endif;?>
			</div>							
				<?php echo $question_count ?></td>
				<td><?php echo stripslashes($question->question) ?></td>
				<td><?php echo $question->answer_count ?></td>
				<td><a href='admin.php?page=watu_question&amp;question=<?php echo $question->ID?>&amp;action=edit&amp;quiz=<?php echo $_REQUEST['quiz']?>' class='edit'><?php _e('Edit', 'watu'); ?></a></td>
				<td><a href='admin.php?page=watu_questions&amp;action=delete&amp;question=<?php echo $question->ID?>&amp;quiz=<?php echo $_REQUEST['quiz']?>' class='delete' onclick="return confirm('<?php echo addslashes(__("You are about to delete this question. This will delete the answers to this question. Press 'OK' to delete and 'Cancel' to stop.", 'watu'))?>');"><?php _e('Delete', 'watu')?></a></td>
				</tr>
		<?php
				}
			} else {
		?>
			<tr style='background-color: <?php echo @$bgcolor; ?>;'>
				<td colspan="4"><?php _e('No questions found.', 'watu') ?></td>
			</tr>
		<?php
		}
		?>
			</tbody>
		</table>
		
		<a href="admin.php?page=watu_question&amp;action=new&amp;quiz=<?php echo $_REQUEST['quiz'] ?>"><?php _e('Create New Question', 'watu')?></a>
		</div>
		<div id="watu-sidebar">
				<?php include(WATU_PATH."/views/sidebar.php");?>
		</div>
	</div>	