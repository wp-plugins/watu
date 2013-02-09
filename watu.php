<?php
/*
Plugin Name: Watu
Plugin URI: http://calendarscripts.info/watu-wordpress.html
Description: Create exams and quizzes and display the result immediately after the user takes the exam. Watu for Wordpress is a light version of <a href="http://calendarscripts.info/watupro/" target="_blank">WatuPRO</a>. Check it if you want to run fully featured exams with data exports, student logins, timers, random questions and more. Free support and upgrades are available. Go to <a href="options-general.php?page=watu.php">Watu Settings</a> or <a href="tools.php?page=watu_exams">Manage Your Exams</a> 

Version: 1.8.1
Author: CalendarScripts
License: GPLv2 or later

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define( 'WATU_PATH', dirname( __FILE__ ) );
include( WATU_PATH.'/controllers/exam.php');
require_once(WATU_PATH.'/wpframe.php');

/// Initialize this plugin. Called by 'init' hook.
add_action('init', 'watu_init');

function watu_init() {
	load_plugin_textdomain('watu', false, dirname( plugin_basename( __FILE__ )).'/langs/' );	
}

/**
 * Add a new menu under Manage, visible for all users with template viewing level.
 */
add_action( 'admin_menu', 'watu_add_menu_links' );
add_action ( 'watu_exam', 'watu_exam' );
function watu_add_menu_links() {
	global $wp_version, $_registered_pages;
	$view_level= 'manage_options';
	$page = 'edit.php';
	if($wp_version >= '2.7') $page = 'tools.php';
	
	//add_menu_page('Watu Settings Page', 'Watu Settings', $view_level, 'watu', 'watu_options');	$page = 'watu';
	
	add_submenu_page($page, __('Manage Exams', 'watu'), __('Watu Exams', 'watu'), $view_level , 'watu_exams', 'watu_exams');
	
	// hidden pages
	add_submenu_page(NULL, __('Manage Exams', 'watu'), __('Watu Exams', 'watu'), $view_level , 'watu_exam', 'watu_exam');
	
	$code_pages = array('question_form.php', 'question.php');
	foreach($code_pages as $code_page) {
		$hookname = get_plugin_page_hookname("watu/$code_page", '' );
		$_registered_pages[$hookname] = true;
	}
}

/// Add an option page for watu
add_action('admin_menu', 'watu_option_page');
function watu_option_page() {
	add_options_page(__('Watu Settings', 'watu'), __('Watu Settings', 'watu'), 'administrator', basename(__FILE__), 'watu_options');
}
function watu_options() {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) die(__("Your are not allowed to to perform this operation", 'watu'));
	if (! user_can_access_admin_page()) wp_die( __('You do not have sufficient permissions to access this page', 'watu') );

	require(ABSPATH. '/wp-content/plugins/watu/options.php');
}

/**
 * This will scan all the content pages that wordpress outputs for our special code. If the code is found, it will replace the requested quiz.
 */
 add_shortcode( 'WATU', 'watu_shortcode' );
function watu_shortcode( $attr ) {
	$exam_id = $attr[0];

	$contents = '';
	if(is_numeric($exam_id)) { // Basic validiation - more on the show_quiz.php file.
		ob_start();
		include(ABSPATH . 'wp-content/plugins/watu/show_exam.php');
		$contents = ob_get_contents();
		ob_end_clean();
	}
	return $contents;
}

add_action('activate_watu/watu.php','watu_activate');
function watu_activate() {
	global $wpdb;
	$wpdb-> show_errors ();
	
	// Initial options.
	add_option('watu_show_answers', 1);
	add_option('watu_single_page', 0);
	add_option('watu_answer_type', 'radio');
	$version = get_option('watu_version');

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix. "watu_master"."'") != $wpdb->prefix. "watu_master") {
		$sql = "CREATE TABLE {$wpdb->prefix}watu_master(
					ID int(11) unsigned NOT NULL auto_increment,
					name varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					description mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					final_screen mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					added_on datetime NOT NULL,
					PRIMARY KEY  (ID)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 ";
		dbDelta($sql);
	}		
	
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix. "watu_question"."'") != $wpdb->prefix. "watu_question") {
		$sql = "CREATE TABLE {$wpdb->prefix}watu_question (
					ID int(11) unsigned NOT NULL auto_increment,
					exam_id int(11) unsigned NOT NULL,
					question mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					answer_type char(15) COLLATE utf8_unicode_ci NOT NULL,
					sort_order int(3) NOT NULL default 0,
					PRIMARY KEY  (ID),
					KEY quiz_id (exam_id)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		dbDelta($sql);
	}		
	
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix. "watu_answer"."'") != $wpdb->prefix. "watu_answer") {
		$sql = "CREATE TABLE {$wpdb->prefix}watu_answer (
					ID int(11) unsigned NOT NULL auto_increment,
					question_id int(11) unsigned NOT NULL,
					answer varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
					correct enum('0','1') NOT NULL default '0',
					point int(11) NOT NULL,
					sort_order int(3) NOT NULL default 0,
					PRIMARY KEY  (ID)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		dbDelta($sql);
	}					
			
	if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix. "watu_grading"."'") != $wpdb->prefix. "watu_grading") {
		$sql = "CREATE TABLE `{$wpdb->prefix}watu_grading` (
				 `ID` int(11) NOT NULL AUTO_INCREMENT,
				 `exam_id` int(11) NOT NULL,
				 `gtitle` varchar (255) NOT NULL,
				 `gdescription` mediumtext COLLATE utf8_unicode_ci NOT NULL,
				 `gfrom` int(11) NOT NULL,
				 `gto` int(11) NOT NULL,
				 PRIMARY KEY (`ID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		dbDelta($sql);
	}					
	
	// db updates in 1.7
	if(empty($version) or $version < 1.7) {
		 $sql = "ALTER TABLE {$wpdb->prefix}watu_master ADD randomize TINYINT NOT NULL";
		 $wpdb->query($sql);
	}
	
	// db updates in 1.8
	if(empty($version) or $version < 1.8) {
		 $sql = "ALTER TABLE {$wpdb->prefix}watu_master ADD single_page TINYINT NOT NULL";
		 $wpdb->query($sql);
		 
		 // let all existing exams follow the default option
		 $sql = "UPDATE {$wpdb->prefix}watu_master SET single_page = '".get_option('watu_single_page')."'";
		 $wpdb->query($sql);
	}
						
	update_option( "watu_delete_db", '' );
	update_option( "watu_version", '1.8' );
}

add_action('deactivate_watu/watu.php','watu_deactivate');
function watu_deactivate() {
	$delDb = get_option('watu_delete_db');
	
	global $wpdb;
	delete_option('watu_show_answers');
	delete_option('watu_single_page');
	delete_option('watu_answer_type');
	delete_option( 'watu_db_tables' );
	if( $delDb == 'checked="checked"' ) {
		$wpdb->query(" DROP TABLE IF EXISTS {$wpdb->prefix}watu_master ");
		$wpdb->query(" DROP TABLE IF EXISTS {$wpdb->prefix}watu_question ");
		$wpdb->query(" DROP TABLE IF EXISTS {$wpdb->prefix}watu_answer ");
		$wpdb->query(" DROP TABLE IF EXISTS {$wpdb->prefix}watu_grading ");
	}
}

function watu_vc_scripts() {
     wp_enqueue_script('jquery');	
		  
      wp_enqueue_style(
			'watu-style',
			plugins_url().'/watu/style.css',
			array(),
			'1.6'
		);
		
		wp_enqueue_script(
			'watu-script',
			plugins_url().'/watu/script.js',
			array(),
			'1.6.0'
		);
}

add_action('wp_enqueue_scripts', 'watu_vc_scripts');
add_action('admin_enqueue_scripts', 'watu_vc_scripts');