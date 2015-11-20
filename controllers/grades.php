<?php
function watu_grades() {
   global $wpdb;
   
   // select quiz
   $quiz = $wpdb->get_row($wpdb->prepare("SELECT ID, name FROM ".WATU_EXAMS." WHERE ID=%d", $_GET['quiz_id']));
   if(empty($quiz->ID)) wp_die(__('Unrecognized quiz ID', 'watu'));
   
   if(!empty($_POST['add']) or !empty($_POST['save'])) {
   	$gtitle = sanitize_text_field($_POST['gtitle']);	   
	   $gfrom = intval($_POST['gfrom']);
	   $gto = intval($_POST['gto']);
   }
   
   if(!empty($_POST['add']) and check_admin_referer('watu_grades')) {
   	$gdesc = $_POST['gdesc'];
   	$wpdb->query($wpdb->prepare("INSERT INTO ".WATU_GRADES." SET
   		exam_id=%d, gtitle=%s, gdescription=%s, gfrom=%d, gto=%d",
   		$quiz->ID, $gtitle, $gdesc, $gfrom, $gto));
   	watu_redirect("admin.php?page=watu_grades&quiz_id=".$quiz->ID);	
   }
   
   if(!empty($_POST['save']) and check_admin_referer('watu_grades')) {
   	$gdesc = $_POST['gdesc'.$_POST['id']];
   	$wpdb->query($wpdb->prepare("UPDATE ".WATU_GRADES." SET
   		gtitle=%s, gdescription=%s, gfrom=%d, gto=%d WHERE ID=%d",
   		$gtitle, $gdesc, $gfrom, $gto, $_POST['id']));
   	watu_redirect("admin.php?page=watu_grades&quiz_id=".$quiz->ID);
   }
   
   if(!empty($_POST['del']) and check_admin_referer('watu_grades')) {
   	$wpdb->query($wpdb->prepare("DELETE FROM ".WATU_GRADES." WHERE ID=%d", $_POST['id']));
   }
   
   // select grades
   $grades = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATU_GRADES." WHERE exam_id=%d ORDER BY gto DESC", $quiz->ID));
   if(@file_exists(get_stylesheet_directory().'/watu/grades.html.php')) include get_stylesheet_directory().'/watu/grades.html.php';
	else include(WATU_PATH . '/views/grades.html.php');  
} // end manage grades