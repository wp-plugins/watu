<?php
require('wpframe.php');
wpframe_stop_direct_call(__FILE__);

$action = 'new';
if($_REQUEST['action'] == 'edit') $action = 'edit';

$dquiz = array();
$grades = array();
if($action == 'edit') {
	$dquiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watu_master WHERE ID=%d", $_REQUEST['quiz']));
	$grades = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watu_grading WHERE  exam_id=%d order by ID ", $_REQUEST['quiz']) );
	$final_screen = stripslashes($dquiz->final_screen);
} else {
	$final_screen = __("<p>Congratulations - you have completed %%QUIZ_NAME%%.</p>\n\n<p>You scored %%SCORE%% points out of %%TOTAL%%.</p>\n\n<p>Your performance have been rated as '%%RATING%%'</p>\n\n<p>Your obtained grade is '%%GRADE%%'</p>", 'watu');
}

require(WATU_PATH."/views/exam_form.html");