<?php
// select taking records for an exam
function watu_takings() {
	global $wpdb;
	
	// select exam
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATU_EXAMS." WHERE ID=%d", $_GET['exam_id']));
	
	// delete a taking
	if(!empty($_GET['del_taking'])) {
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_TAKINGS." WHERE ID=%d", $_GET['id']));
		watu_redirect("admin.php?page=watu_takings&exam_id=".$exam->ID);
	}
	
	// mass cleanup
	if(!empty($_POST['delete_all_takings'])) {
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_TAKINGS." WHERE exam_id=%d", $exam->ID));
	}
	
	// select taking records
	$offset = empty($_GET['offset'])?0:intval($_GET['offset']);
	$limit_sql = empty($_GET['watu_export']) ? "Limit $offset, 10" : "";
	
	$takings = $wpdb->get_results($wpdb->prepare("SELECT tT.*, tU.user_login as user_login 
		FROM ".WATU_TAKINGS." tT LEFT JOIN {$wpdb->users} tU ON tU.ID = tT.user_id
		WHERE exam_id=%d ORDER BY tT.ID DESC $limit_sql", $exam->ID));
		
	// export CSV
	if(!empty($_GET['watu_export'])) {
		$newline=watu_define_newline();		
		
		$rows=array();
		$rows[]=__("User or IP;Date;Points;Result/Grade", 'watu');
		foreach($takings as $taking) {
			$row = ($taking->user_id ? $taking->user_login : $taking->ip).";".date(get_option('date_format'), strtotime($taking->date)).";".
				$taking->points.";".$taking->result;
			$rows[] = $row;		
		} // end foreach taking
		$csv=implode($newline,$rows);		
		
		$now = gmdate('D, d M Y H:i:s') . ' GMT';	
		$filename = 'exam-'.$exam->ID.'-results.csv';	
		header('Content-Type: ' . watu_get_mime_type());
		header('Expires: ' . $now);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Pragma: no-cache');
		echo $csv;
		exit;
	}	
		
	$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATU_TAKINGS." WHERE exam_id=%d", $exam->ID));	
		
	require(WATU_PATH."/views/takings.php");	
}