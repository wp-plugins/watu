<?php
if(isset($_REQUEST['do']) and $_REQUEST['do']=='show_exam_result' ) $exam_id = $_REQUEST['quiz_id'];

if(!is_singular() and isset($GLOBALS['watu_client_includes_loaded'])) { #If this is in the listing page - and a quiz is already shown, don't show another.
	printf(__("Please go to <a href='%s'>%s</a> to view the test", 'watu'), get_permalink(), get_thfe_title());
	return false;
} 

global $wpdb, $user_ID;

// select exam
$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_EXAMS." WHERE ID=%d", $exam_id));

$answer_display = get_option('watu_show_answers');
if(!isset($exam->show_answers) or $exam->show_answers == 100) $answer_display = $answer_display; // assign the default
else $answer_display = $exam->show_answers;

$order_sql = $exam->randomize?"ORDER BY RAND()":"ORDER BY ID";

$questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATU_QUESTIONS." 
		WHERE exam_id=%d $order_sql", $exam_id));
$num_questions = sizeof($questions);		
		
if($questions) {
	if(!isset($GLOBALS['watu_client_includes_loaded']) and !isset($_REQUEST['do']) ) {
		$GLOBALS['watu_client_includes_loaded'] = true; // Make sure that this code is not loaded more than once.
}


if(isset($_REQUEST['do']) and $_REQUEST['do']) { // Quiz Reuslts.
	$score = $achieved = $total = $num_correct = 0;
	$result = '';
	$result .= "<p>" . __('All the questions in the exam along with their answers are shown below. Your answers are bolded. The correct answers have a green background while the incorrect ones have a red background.', 'watu') . "</p>";

	// we should reorder the questions in the same way they came from POST because exam might be randomized
	require_once(WATU_PATH."/models/exam.php");
	$_exam = new WatuExam();
	$questions = $_exam->reorder_questions($questions, $_POST['question_id']);

	foreach ($questions as $qct => $ques) {
		$result .= "<div class='show-question'>";
		$result .= "<div class='show-question-content'>". stripslashes(wpautop($ques->question)) . "</div>\n";
		$all_answers = $wpdb->get_results("SELECT ID,answer,correct, point FROM {$wpdb->prefix}watu_answer WHERE question_id={$ques->ID} ORDER BY sort_order");

		$correct = false;
		$result .= "<ul>";
		$ansArr = is_array( @$_REQUEST["answer-" . $ques->ID] )? $_REQUEST["answer-" . $ques->ID] : array();
		foreach ($all_answers as $ans) {
			$class = 'answer';
			if(  in_array($ans->ID , $ansArr) ) $class .= ' user-answer';
			if($ans->correct == 1) {$class .= ' correct-answer';}
			if( in_array($ans->ID , $ansArr ) and $ans->correct == 1) {$correct = true; $score+=$ans->point;}
			if( in_array($ans->ID , $ansArr ) ) $achieved+=$ans->point; 
			$result .= "<li class='$class'><span class='answer'>" . stripslashes($ans->answer) . "</span></li>\n";
		}

		// textareas
		if($ques->answer_type=='textarea') {
			$result.="<li class='user-answer'>".wpautop($_REQUEST["answer-" . $ques->ID][0])."</li>";
		}		
		
		$result .= "</ul>";
		if(empty($_REQUEST["answer-" . $ques->ID])) $result .= "<p class='unanswered'>" . __('Question was not answered', 'watu') . "</p>";

		$result .= "</div>";
		if($correct) $num_correct++;
		//$total++;
	}
	$total = $wpdb->get_var($wpdb->prepare("SELECT sum(point) FROM `{$wpdb->prefix}watu_question` as q inner join `{$wpdb->prefix}watu_answer` as a on question_id=q.ID WHERE `exam_id`=%d and correct='1' ", $exam_id));

	// Find scoring details
	if($total == 0) $percent = 0;
	else $percent = number_format($score / $total * 100, 2);
						//0-9			10-19%,	 	20-29%, 	30-39%			40-49%
	$all_rating = array(__('Failed', 'watu'), __('Failed', 'watu'), __('Failed', 'watu'), __('Failed', 'watu'), __('Just Passed', 'watu'),
						//																			100%			More than 100%?!
					__('Satisfactory', 'watu'), __('Competent', 'watu'), __('Good', 'watu'), __('Very Good', 'watu'), __('Excellent', 'watu'), __('Unbeatable', 'watu'), __('Cheater', 'watu'));
	$rate = intval($percent / 10);
	if($percent == 100) $rate = 9;
	if($score == $total) $rate = 10;
	if($percent>100) $rate = 11;
	$rating = $all_rating[$rate];
	
	$grade = 'None';
	$gtitle = $gdescription="";
	$g_id = 0;
	$allGrades = $wpdb->get_results(" SELECT * FROM `".WATU_GRADES."` WHERE exam_id=$exam_id ");
	if( count($allGrades) ){
		foreach($allGrades as $grow ) {

			if( $grow->gfrom <= $achieved and $achieved <= $grow->gto ) {
				$grade = $gtitle = $grow->gtitle;
				$gdescription = stripslashes($grow->gdescription);
				$g_id = $grow->ID;
				if(!empty($grow->gdescription)) $grade.="<p>".stripslashes($grow->gdescription)."</p>";
				break;
			}
		}
	}
	$score = $achieved;

	$quiz_details = $wpdb->get_row($wpdb->prepare("SELECT name,final_screen, description FROM {$wpdb->prefix}watu_master WHERE ID=%d", $exam_id));

	$quiz_details->final_screen = str_replace('%%TOTAL%%', '%%MAX-POINTS%%', $quiz_details->final_screen);
	$replace_these	= array('%%SCORE%%', '%%MAX-POINTS%%', '%%PERCENTAGE%%', '%%GRADE%%', '%%RATING%%', '%%CORRECT%%', '%%WRONG_ANSWERS%%', '%%QUIZ_NAME%%',	'%%DESCRIPTION%%', '%%GRADE-TITLE%%', '%%GRADE-DESCRIPTION%%', '%%POINTS%%');
	$with_these		= array($score,		 $total,	  $percent,			$grade,		 $rating,		$num_correct,					$num_questions-$num_correct,	   stripslashes($quiz_details->name), stripslashes($quiz_details->description), $gtitle, $gdescription, $score);
	
	// insert taking
	$uid = $user_ID ? $user_ID : 0;
	$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_TAKINGS." SET exam_id=%d, user_id=%d, ip=%s, date=CURDATE(), 
		points=%d, grade_id=%d, result=%s", $exam_id, $uid, $_SERVER['REMOTE_ADDR'], $achieved, $g_id, $grade));
	$taking_id = $wpdb->insert_id;	

	// Show the results
	$output = str_replace($replace_these, $with_these, stripslashes($quiz_details->final_screen));
	$final_output = apply_filters(WATU_CONTENT_FILTER, $output); 
	
	if($answer_display == 1) $final_output .= '<hr />' . apply_filters(WATU_CONTENT_FILTER,$result);
	
	echo $final_output;
		
	// update snapshot
	$wpdb->query($wpdb->prepare("UPDATE ".WATU_TAKINGS." SET snapshot=%s WHERE ID=%d", $final_output, $taking_id)); 
	
	do_action('watu_exam_submitted', $taking_id);
	exit;// Exit due to ajax call

} else { // Show The Test
	$single_page = $exam->single_page;
?>

<div id="watu_quiz" class="quiz-area <?php if($single_page) echo 'single-page-quiz'; ?>">
<?php if(!empty($exam->description)):?><p><?php echo apply_filters(WATU_CONTENT_FILTER,$exam->description);?></p><?php endif;?>
<form action="" method="post" class="quiz-form" id="quiz-<?php echo $exam_id?>">
<?php
$question_count = 1;
$question_ids = '';
$output = $answer_class = '';
foreach ($questions as $qct => $ques) {
	$output .= "<div class='watu-question' id='question-$question_count'>";
	$output .= "<div class='question-content'>". stripslashes(wpautop($ques->question)) . "</div>";
	$output .= "<input type='hidden' name='question_id[]' value='{$ques->ID}' />";
	$question_ids .= $ques->ID.',';
	$dans = $wpdb->get_results("SELECT ID,answer,correct FROM {$wpdb->prefix}watu_answer WHERE question_id={$ques->ID} ORDER BY sort_order");
	$ans_type = $ques->answer_type;
	
	// display textarea
	if($ans_type=='textarea') {
		$output .= "<textarea name='answer-{$ques->ID}[]' rows='5' cols='40' id='textarea_q_{$ques->ID}' class='watu-textarea-$question_count'></textarea>"; 
	}	
	
	foreach ($dans as $ans) {
		if($answer_display == 2) {
			$answer_class = 'wrong-answer-label';
			if($ans->correct) $answer_class = 'correct-answer-label';
		}
		$output .= "<div><input type='$ans_type' name='answer-{$ques->ID}[]' id='answer-id-{$ans->ID}' class='answer answer-$question_count $answer_class answerof-{$ques->ID}' value='{$ans->ID}' />";
		$output .= "&nbsp;<label for='answer-id-{$ans->ID}' id='answer-label-{$ans->ID}' class='$answer_class answer label-$question_count'><span>" . stripslashes($ans->answer) . "</span></label></div>";
	}

	$output .= "<input type='hidden' id='questionType".$question_count."' value='{$ques->answer_type}' class='".($ques->is_required?'required':'')."'>";
	$output .= "</div>";
	$question_count++;
}
$output .= "<div style='display:none' id='question-$question_count'>";
$output .= "<br /><div class='question-content'><img src=\"".plugins_url('watu/loading.gif')."\" width=\"16\" height=\"16\" alt=\"".__('Loading', 'watu')." ...\" title=\"".__('Loading', 'watu')." ...\" />&nbsp;".__('Loading', 'watu')." ...</div>";
$output .= "</div>";
echo apply_filters(WATU_CONTENT_FILTER,$output);
$question_ids = preg_replace('/,$/', '', $question_ids );
?><br />
<?php 
if($answer_display == 2 and $single_page != 1) : ?>
<input type="button" id="show-answer" value="<?php _e('Show Answer', 'watu') ?>"  /><br />
<?php endif;
if($single_page != 1 and $answer_display!=2): ?>
<p><?php _e('Question', 'watu')?> <span id='numQ'>1</span> <?php _e('of', 'watu')?> <?php echo $num_questions;?></p>
<input type="button" id="next-question" value="<?php _e('Next', 'watu') ?> &gt;"  /><br />
<?php endif; ?>

<input type="button" name="action" onclick="Watu.submitResult()" id="action-button" value="<?php _e('Show Results', 'watu') ?>"  />
<input type="hidden" name="quiz_id" value="<?php echo  $exam_id ?>" />
</form>
</div>
<script type="text/javascript">
var exam_id=0;
var question_ids='';
var watuURL='';
jQuery(function($){
question_ids = "<?php print $question_ids ?>";
exam_id = <?php print $exam_id ?>;
Watu.qArr = question_ids.split(',');
Watu.singlePage = '<?php echo $exam->single_page?>';
watuURL = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
});
</script>
<?php }
}