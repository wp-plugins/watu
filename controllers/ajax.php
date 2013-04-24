<?php
// ajax calls
function watu_submit() {
	// fix paid membership pro problem
	remove_filter( 'the_content', 'pmpro_membership_content_filter', 5 );
	require_once(WATU_PATH."/show_exam.php");
}