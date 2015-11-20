<?php
global $wpdb;
if(!empty($_REQUEST['submit']) and check_admin_referer('watu_options')) {
	$delete_db = empty($_POST['delete_db']) ? 0 : 1;
	$delete_db_confirm = (empty($_POST['delete_db_confirm']) or $_POST['delete_db_confirm']!= 'yes') ? '' : 'yes';
	$answer_type = ($_POST['answer_type'] == 'radio') ? 'radio' : 'checkbox';
	$use_the_content = empty($_POST['use_the_content']) ? 1 : 0; 
	
	update_option( "watu_delete_db", $delete_db );
	update_option('watu_delete_db_confirm', $delete_db_confirm);
	update_option('watu_answer_type', $answer_type);
	update_option('watu_use_the_content', $use_the_content);
	update_option('watu_text_captcha', $_POST['text_captcha']);
		
	print '<div id="message" class="updated fade"><p>' . __('Options updated', 'watu') . '</p></div>';	
}

// save no_ajax
if(!empty($_POST['save_ajax_settings']) and check_admin_referer('watu_ajax_options')) {
	$ids = empty($_POST['no_ajax']) ? array(0) : $_POST['no_ajax'];
	// make sure IDs contains only exam IDs
	$id_sql = implode(', ', $ids);
	if(!preg_match("/^\d(?:,\d)*$/", $id_sql)) $id_sql = "0";
	
	$wpdb->query("UPDATE ".WATU_EXAMS." SET no_ajax=1 WHERE ID IN (".$id_sql.")");
	$wpdb->query("UPDATE ".WATU_EXAMS." SET no_ajax=0 WHERE ID NOT IN (".$id_sql.")");
}

$answer_display = get_option('watu_show_answers');
$delete_db = get_option('watu_delete_db');

$text_captcha = get_option('watu_text_captcha');
// load 3 default questions in case nothing is loaded
if(empty($text_captcha)) {
	$text_captcha = __('What is the color of the snow? = white', 'watu').PHP_EOL.__('Is fire hot or cold? = hot', 'watu') 
		.PHP_EOL. __('In which continent is Norway? = Europe', 'watu'); 
}

// select all quizzes for No Ajax option
$quizzes = $wpdb->get_results("SELECT ID, name, no_ajax FROM ".WATU_EXAMS." ORDER BY name");		

if(@file_exists(get_stylesheet_directory().'/watu/options.html.php')) include get_stylesheet_directory().'/watu/options.html.php';
else include(WATU_PATH . '/views/options.html.php');