<?php
// A separate file as a work around of redirect problem 
// this must be changed! note from Dec 11 2012
require('../../../wp-blog-header.php');
auth_redirect();
if($wp_version >= '2.6.5') check_admin_referer('watu_create_edit_quiz');
require('wpframe.php');

if(isset($_REQUEST['submit'])) {
	if($_REQUEST['action'] == 'edit') { //Update goes here
		$exam_id = $_REQUEST['quiz'];
		$wpdb->query("delete from {$wpdb->prefix}watu_grading where exam_id=".$_REQUEST['quiz']);
		$wpdb->get_results($wpdb->prepare("UPDATE {$wpdb->prefix}watu_master 
			SET name=%s, description=%s,final_screen=%s, randomize=%d  
			WHERE ID=%d", $_POST['name'], $_POST['description'], $_POST['content'], 
			$_POST['randomize'], $_REQUEST['quiz']));
		
		$wp_redirect = $wpframe_home . '/wp-admin/edit.php?page=watu/exam.php&message=updated';
	
	} else {
		$wpdb->get_results($wpdb->prepare("INSERT INTO {$wpdb->prefix}watu_master 
		(name, description, final_screen,  added_on, randomize) VALUES(%s, %s, %s, NOW(), %d)", 
		$_POST['name'], $_POST['description'], $_POST['content'], $_POST['randomize']));
		$exam_id = $wpdb->insert_id;
		if($exam_id == 0 ) $wp_redirect = $wpframe_home . '/wp-admin/edit.php?page=watu/exam.php&message=fail';
		$wp_redirect = $wpframe_home . '/wp-admin/edit.php?page=watu/question.php&message=new_quiz&quiz='.$exam_id;
	}
	
	if( $exam_id>0 and isset($_REQUEST['gradetitle']) and is_array($_REQUEST['gradetitle']) ) {
		$sql = "insert into {$wpdb->prefix}watu_grading (exam_id, gtitle, gdescription, gfrom, gto) values ";
		$saveGrade = false;
		$descArr = $_REQUEST['grade_description'];
		$fromArr = $_REQUEST['grade_from'];
		$toArr = $_REQUEST['grade_to'];
		
		foreach($_REQUEST['gradetitle'] as $key=>$title) {			
			$title = $wpdb->escape($title);
			$desc = $wpdb->escape( $descArr[$key] );
			$from =  $fromArr[$key];
			$to =  $toArr[$key];
			
			if( !empty($title)  && is_numeric($from) && is_numeric($to) ) {
				$saveGrade = true;
				$sql .= " ( $exam_id, '$title' , '$desc', $from, $to), ";
			} else { $errorPartial= true;}
		}
	
		if( $saveGrade) {
			$sql = preg_replace('/,\s$/', '', $sql);

			$out = $wpdb->query($sql);

			if( $out===false) $wp_redirect .= '&grade='.urlencode(__('The grading data can not be saved. Pleae try again', 'watu'));
			else if($errorPartial) $wp_redirect .= '&grade='.urlencode(__('Some grades can not be saved.', 'watu'));
			
		} else{			
			$wp_redirect .= '&grade='.urlencode(__('The grading data can not be saved. Please try again', 'watu'));
		}
	} //end grading block
	
	wp_redirect($wp_redirect);
}
exit;
