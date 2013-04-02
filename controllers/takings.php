<?php
// select taking records for an exam
function watu_takings() {
	global $wpdb;
	
	// select exam
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_EXAMS." WHERE ID=%d", $_GET['exam_id']));
	
	// select taking records
	$offset = empty($_GET['offset'])?0:intval($_GET['offset']);
	
	$takings = $wpdb->get_results($wpdb->prepare("SELECT tT.*, tU.user_login as user_login 
		FROM ".WATU_TAKINGS." tT LEFT JOIN {$wpdb->users} tU ON tU.ID = tT.user_id
		WHERE exam_id=%d ORDER BY tT.ID DESC LIMIT $offset, 10", $exam->ID));
		
	$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATU_TAKINGS." WHERE exam_id=%d", $exam->ID));	
		
	require(WATU_PATH."/views/takings.php");	
}