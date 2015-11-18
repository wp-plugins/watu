<?php
global $wpdb;
if(isset($_REQUEST['submit']) and $_REQUEST['submit']) {
	
	update_option( "watu_delete_db", @$_POST['delete_db'] );
	update_option('watu_delete_db_confirm', $_POST['delete_db_confirm']);
		
	$options = array('answer_type', 'use_the_content', 'text_captcha');
	foreach($options as $opt) {
		if(!empty($_POST[$opt])) update_option('watu_' . $opt, $_POST[$opt]);
		else update_option('watu_' . $opt, 0);
	}
	print '<div id="message" class="updated fade"><p>' . __('Options updated', 'watu') . '</p></div>';	
}

// save no_ajax
if(!empty($_POST['save_ajax_settings'])) {
	$ids = empty($_POST['no_ajax']) ? array(0) : $_POST['no_ajax'];
	
	$wpdb->query("UPDATE ".WATU_EXAMS." SET no_ajax=1 WHERE ID IN (".implode(', ', $ids).")");
	$wpdb->query("UPDATE ".WATU_EXAMS." SET no_ajax=0 WHERE ID NOT IN (".implode(', ', $ids).")");
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