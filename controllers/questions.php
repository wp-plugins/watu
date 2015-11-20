<?php
function watu_questions() {
	global $wpdb;
	
	$action = 'new';
	if(!empty($_GET['action']) and $_GET['action'] == 'edit') $action = 'edit';
	$quiz_id = intval($_GET['quiz']);
	
	if(isset($_REQUEST['submit']) and check_admin_referer('watu_questions')) {
		$content = $_POST['content'];		
		$answer_type = $_POST['answer_type'];
		$is_required = empty($_POST['is_required']) ? 0 : 1;
		$feedback = $_POST['feedback'];
		
		if($action == 'edit'){ //Update goes here
			$question_id = intval($_POST['question']);
			
			$wpdb->query($wpdb->prepare("UPDATE ".WATU_QUESTIONS." 
			SET question=%s, answer_type=%s, is_required=%d, feedback=%s 
			WHERE ID=%d", $content, $answer_type, $is_required, 
			$feedback, $question_id));
			$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}watu_answer WHERE question_id=%d", $question_id));
				
		} else {	
			// select max sort order in this quiz
			$sort_order = $wpdb->get_var($wpdb->prepare("SELECT MAX(sort_order) FROM ".WATU_QUESTIONS." WHERE exam_id=%d", $quiz_id));
			$sort_order++;			
			
			$sql = $wpdb->prepare("INSERT INTO ".WATU_QUESTIONS." (exam_id, question, answer_type, is_required, feedback, sort_order) 
			VALUES(%d, %s, %s, %d, %s, %d)", $quiz_id, $content, $answer_type, 
				$is_required, $feedback, $sort_order);
			$wpdb->query($sql);//Inserting the questions
	
			$_POST['question'] = $wpdb->insert_id;
			$action='edit';
		}
		
		$question_id = intval($_POST['question']);
		if($question_id>0) {
			// the $counter will skip over empty answers, $sort_order_counter will track the provided answers order.
			$counter = 1;
			$sort_order_counter = 1;
			$correctArry = @$_POST['correct_answer'];
			$pointArry = $_POST['point'];
			
			if(is_array($_POST['answer']) and !empty($_POST['answer'])) {
				
				foreach ($_POST['answer'] as $key => $answer_text) {
					$correct=0;
					if( @in_array($counter, $correctArry) ) $correct=1;
					$point = $pointArry[$key];
					if($answer_text!='') {
						$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_ANSWERS." (question_id,answer,correct,point, sort_order)
							VALUES(%d, %s, %s, %d, %d)", $question_id, $answer_text, $correct, $point, $sort_order_counter));
						$sort_order_counter++;
					}
					$counter++;
				}
			} 	// end if(is_array($_POST['answer']) and !empty($_POST['answer']))
		}
	}
	
	if(!empty($_GET['action']) and $_GET['action'] == 'delete') {
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_ANSWERS." WHERE question_id=%d", $_GET['question']));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_QUESTIONS." WHERE ID=%d", $_GET['question']));		
	}
	$exam_name = stripslashes($wpdb->get_var($wpdb->prepare("SELECT name FROM ".WATU_EXAMS." WHERE ID=%d", $quiz_id)));
	
	// reorder questions
		if(!empty($_GET['move'])) {
			WatuQuestion::reorder($_GET['move'], $_GET['quiz'], $_GET['dir']);
			watu_redirect("admin.php?page=watu_questions&quiz=".$_GET['quiz']);
		}		
		
		$offset = 0; // for now initialize as 0
		
		// Retrieve the questions
		$all_question = $wpdb->get_results("SELECT Q.ID,Q.question,(SELECT COUNT(*) FROM {$wpdb->prefix}watu_answer WHERE question_id=Q.ID) AS answer_count
												FROM `{$wpdb->prefix}watu_question` AS Q
												WHERE Q.exam_id=$quiz_id ORDER BY Q.sort_order, Q.ID");
												
		if(empty($filter_sql)) WatuQuestion::fix_sort_order($all_question);		
		$num_questions = sizeof($all_question);	
		
		if(@file_exists(get_stylesheet_directory().'/watu/questions.html.php')) include get_stylesheet_directory().'/watu/questions.html.php';
		else include(WATU_PATH . '/views/questions.html.php');  
} 

function watu_question() {
	global $wpdb;	
	
	$action = 'new';
	if($_REQUEST['action'] == 'edit') $action = 'edit';
	$question_id = intval(@$_GET['question']);
	
	$all_answers = array();
	
	if(!empty($question_id)) {
		$question= $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_QUESTIONS." WHERE ID=%d", $question_id));
		$all_answers = $wpdb->get_results($wpdb->prepare("SELECT answer, correct, point FROM {$wpdb->prefix}watu_answer 
			WHERE question_id=%d ORDER BY sort_order", $question_id));	
	}
	
	$ans_type = $action =='new'? get_option('watu_answer_type'): $question->answer_type;
	$answer_count = 4;
	if($action == 'edit' and $answer_count < count($all_answers)) $answer_count = count($all_answers) ;
	
	if(@file_exists(get_stylesheet_directory().'/watu/question-form.html.php')) include get_stylesheet_directory().'/watu/question-form.html.php';
	else include(WATU_PATH . '/views/question-form.html.php');  
}