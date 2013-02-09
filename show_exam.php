<?php
if(isset($_REQUEST['action']) and $_REQUEST['action']=='show_exam_result' ) { // Initial setup for ajax.
	if (!function_exists('add_action')) {
		$wp_root = '../../..';
		if (file_exists($wp_root.'/wp-load.php')) {
			require_once($wp_root.'/wp-load.php');
		} else {
			require_once($wp_root.'/wp-config.php');
		}
	}
	$exam_id = $_REQUEST['quiz_id'];
}

require_once('wpframe.php');

if(!is_single() and isset($GLOBALS['watu_client_includes_loaded'])) { #If this is in the listing page - and a quiz is already shown, don't show another.
	printf(t("Please go to <a href='%s'>%s</a> to view the test"), get_permalink(), get_the_title());
} else {

global $wpdb;
$GLOBALS['wpframe_plugin_name'] = basename(dirname(__FILE__));
$GLOBALS['wpframe_plugin_folder'] = $GLOBALS['wpframe_wordpress'] . '/wp-content/plugins/' . $GLOBALS['wpframe_plugin_name'];

$answer_display = get_option('watu_show_answers');

// select exam
$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watu_master WHERE ID=%d", $exam_id));

$order_sql = $exam->randomize?"ORDER BY RAND()":"ORDER BY ID";

$questions = $wpdb->get_results($wpdb->prepare("SELECT ID,question, answer_type 
		FROM {$wpdb->prefix}watu_question 
		WHERE exam_id=%d $order_sql", $exam_id));
		
if($questions) {
	if(!isset($GLOBALS['watu_client_includes_loaded']) and !isset($_REQUEST['action']) ) {
?>
<link type="text/css" rel="stylesheet" href="<?php echo $GLOBALS['wpframe_plugin_folder']?>/style.css" />
<?php
	$GLOBALS['watu_client_includes_loaded'] = true; // Make sure that this code is not loaded more than once.
}


if(isset($_REQUEST['action']) and $_REQUEST['action']) { // Quiz Reuslts.
	$score = 0;
	$achieved = 0;
	$total = 0;
	$result = '';
	$result .= "<p>" . __('All the questions in the exam along with their answers are shown below. Your answers are bolded. The correct answers have a green background while the incorrect ones have a red background.', 'watu') . "</p>";

	// we should reorder the questions in the same way they came from POST because exam might be randomized
	require_once(WATU_PATH."/models/exam.php");
	$_exam = new WatuExam();
	$questions = $_exam->reorder_questions($questions, $_POST['question_id']);

	foreach ($questions as $ques) {
		$result .= "<div class='show-question'>";
		$result .= "<div class='show-question-content'>". stripslashes(wpautop($ques->question)) . "</div>\n";
		$all_answers = $wpdb->get_results("SELECT ID,answer,correct, point FROM {$wpdb->prefix}watu_answer WHERE question_id={$ques->ID} ORDER BY sort_order");

		$correct = false;
		$result .= "<ul>";
		$ansArr = is_array( $_REQUEST["answer-" . $ques->ID] )? $_REQUEST["answer-" . $ques->ID] : array();
		foreach ($all_answers as $ans) {
			$class = 'answer';
			if(  in_array($ans->ID , $ansArr) ) $class .= ' user-answer';
			if($ans->correct == 1) $class .= ' correct-answer';
			if( in_array($ans->ID , $ansArr ) and $ans->correct == 1) {$correct = true; $score+=$ans->point;}
			if( in_array($ans->ID , $ansArr ) ) $achieved+=$ans->point; 
			$result .= "<li class='$class'><span class='answer'>" . stripslashes($ans->answer) . "</span></li>\n";
		}

		// textareas
		if($ques->answer_type=='textarea')
		{
			$result.="<li class='user-answer'>".wpautop($_REQUEST["answer-" . $ques->ID][0])."</li>";
		}		
		
		$result .= "</ul>";
		if(!$_REQUEST["answer-" . $ques->ID]) $result .= "<p class='unanswered'>" . __('Question was not answered', 'watu') . "</p>";

		$result .= "</div>";
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
	$allGrades = $wpdb->get_results(" SELECT * FROM `{$wpdb->prefix}watu_grading` WHERE exam_id=$exam_id ");
	if( count($allGrades) ){
		foreach($allGrades as $grow ) {

			if( $grow->gfrom <= $achieved and $achieved <= $grow->gto ) {
				$grade = $grow->gtitle;
				if(!empty($grow->gdescription)) $grade.="<p>".stripslashes($grow->gdescription)."</p>";
				//
				break;
			}
		}
	}
	$score = $achieved;

	$quiz_details = $wpdb->get_row($wpdb->prepare("SELECT name,final_screen, description FROM {$wpdb->prefix}watu_master WHERE ID=%d", $exam_id));

	$replace_these	= array('%%SCORE%%', '%%TOTAL%%', '%%PERCENTAGE%%', '%%GRADE%%', '%%RATING%%', '%%CORRECT_ANSWERS%%', '%%WRONG_ANSWERS%%', '%%QUIZ_NAME%%',	  '%%DESCRIPTION%%');
	$with_these		= array($score,		 $total,	  $percent,			$grade,		 $rating,		$score,					$total-$score,	   stripslashes($quiz_details->name), stripslashes($quiz_details->description));

	// Show the results

	print str_replace($replace_these, $with_these, stripslashes($quiz_details->final_screen));
	if($answer_display == 1) print '<hr />' . $result;
	exit;// Exit due to ajax call

} else { // Show The Test
	$single_page = $exam->single_page;
?>

<div id="watu_quiz" class="quiz-area <?php if($single_page) echo 'single-page-quiz'; ?>">
<form action="" method="post" class="quiz-form" id="quiz-<?php echo $exam_id?>">
<?php
$question_count = 1;
$question_ids = '';
foreach ($questions as $ques) {
	echo "<div class='watu-question' id='question-$question_count'>";
	echo "<div class='question-content'>". stripslashes(wpautop($ques->question)) . "</div>";
	echo "<input type='hidden' name='question_id[]' value='{$ques->ID}' />";
	$question_ids .= $ques->ID.',';
	$dans = $wpdb->get_results("SELECT ID,answer,correct FROM {$wpdb->prefix}watu_answer WHERE question_id={$ques->ID} ORDER BY sort_order");
	$ans_type = $ques->answer_type;
	
	// display textarea
	if($ans_type=='textarea')
	{
		echo "<textarea name='answer-{$ques->ID}[]' rows='5' cols='40' id='textarea_q_{$ques->ID}'></textarea>"; 
	}	
	
	foreach ($dans as $ans) {
		if($answer_display == 2) {
			$answer_class = 'wrong-answer-label';
			if($ans->correct) $answer_class = 'correct-answer-label';
		}
		echo "<input type='$ans_type' name='answer-{$ques->ID}[]' id='answer-id-{$ans->ID}' class='answer answer-$question_count $answer_class answerof-{$ques->ID}' value='{$ans->ID}' />";
		echo "&nbsp;<label for='answer-id-{$ans->ID}' id='answer-label-{$ans->ID}' class='$answer_class answer label-$question_count'><span>" . stripslashes($ans->answer) . "</span></label><br />";
	}

	echo "<input type='hidden' id='questionType".$question_count."' value='{$ques->answer_type}'>";
	echo "</div>";
	$question_count++;
}
echo "<div style='display:none' id='question-$question_count'>";
echo "<br /><div class='question-content'><img src=\"".plugins_url('watu/loading.gif')."\" width=\"16\" height=\"16\" alt=\"".__('Loading', 'watu')." ...\" title=\"".__('Loading', 'watu')." ...\" />&nbsp;".__('Loading', 'watu')." ...</div><br />";
echo "</div>";
$question_ids = preg_replace('/,$/', '', $question_ids );
?><br />
<?php if($answer_display == 2) { ?>
<input type="button" id="show-answer" value="<?php _e('Show Answer', 'watu') ?>"  /><br />
<?php } else { ?>
<input type="button" id="next-question" value="<?php _e('Next', 'watu') ?> &gt;"  /><br />
<?php } ?>

<input type="button" name="action" onclick="Watu.submitResult()" id="action-button" value="<?php _e('Show Results', 'watu') ?>"  />
<input type="hidden" name="quiz_id" value="<?php echo  $exam_id ?>" />
</form>
</div>
<script type="text/javascript">
var question_ids = "<?php print $question_ids ?>";
var exam_id = <?php print $exam_id ?>;
Watu.qArr = question_ids.split(',');
var watuURL = "<?php print plugins_url('watu/'.basename(__FILE__) ) ?>";
</script>
<?php }
}
}
?>