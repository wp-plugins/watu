<?php
// a bunch of helper functions
function watu_define_newline() {
	// credit to http://yoast.com/wordpress/users-to-csv/
	$unewline = "\r\n";
	if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'win')) {
	   $unewline = "\r\n";
	} else if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'mac')) {
	   $unewline = "\r";
	} else {
	   $unewline = "\n";
	}
	return $unewline;
}

function watu_get_mime_type()  {
	// credit to http://yoast.com/wordpress/users-to-csv/
	$USER_BROWSER_AGENT="";

			if (preg_match('/OPERA(\/| )([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='OPERA';
			} else if (preg_match('/MSIE ([0-9].[0-9]{1,2})/',strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='IE';
			} else if (preg_match('/OMNIWEB\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='OMNIWEB';
			} else if (preg_match('/MOZILLA\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='MOZILLA';
			} else if (preg_match('/KONQUEROR\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
		    	$USER_BROWSER_AGENT='KONQUEROR';
			} else {
		    	$USER_BROWSER_AGENT='OTHER';
			}

	$mime_type = ($USER_BROWSER_AGENT == 'IE' || $USER_BROWSER_AGENT == 'OPERA')
				? 'application/octetstream'
				: 'application/octet-stream';
	return $mime_type;
}

function watu_redirect($url) {
	echo "<meta http-equiv='refresh' content='0;url=$url' />"; 
	exit;
}

// escapes user input to be usable in preg_replace & other preg functions
function watu_preg_escape($input) {
	return str_replace(array('^', '.', '|', '(', ')', '[', ']', '*', '+', '?', '{', '}', '$', '/'), 
		array('\^', '\.', '\|', '\(', '\)', '\[', '\]', '\*', '\+', '\?', '\{', '\}', '\$', '\/' ), $input);
}

// notify admin / user about taken quiz
function watu_notify($exam, $uid, $output, $who = 'admin') {
	global $user_email;
	
	$admin_email = get_option('admin_email');
	$admin_emails = array();
	
	if(!empty($exam->notify_email)) {
		$emails = explode(',', $exam->notify_email);
		foreach($emails as $email) $admin_emails[] = trim($email);
	}
	else $admin_emails[] = $admin_email;
	
	// different output for user / admin?
	if(strstr($output, '{{{split}}}')) {
		$parts = explode('{{{split}}}', $output);
		if($who == 'admin') $output = $parts[1];
		else $output = $parts[0];
	} 
	
	// replace styles in the snapshot with the images
	$correct_style=' style="padding-right:20px;background:url('.WATU_URL.'correct.png) no-repeat right top;" ';
	$wrong_style=' style="padding-right:20px;background:url('.WATU_URL.'wrong.png) no-repeat right top;" ';
	$user_answer_style = ' style="font-weight:bold;" ';	
	
	
	$output=str_replace('><!--WATUEMAILanswerWATUEMAIL--','',$output);
	$output=str_replace('><!--WATUEMAILanswer correct-answer user-answerWATUEMAIL--', $correct_style, $output);
	$output=str_replace('><!--WATUEMAILanswer correct-answeruser-answerWATUEMAIL--', $correct_style, $output);
	$output=str_replace('><!--WATUEMAILanswer correct-answerWATUEMAIL--',$correct_style,$output);
	$output=str_replace('><!--WATUEMAILanswer user-answerWATUEMAIL--', $wrong_style,$output);
	
	$output = str_replace("<li class='answer user-answer'>", "<li ".$user_answer_style.">", $output);
	$output = str_replace("<li class='answer user-answer correct-answer'>", "<li ".$user_answer_style.">", $output);	
	$output = str_replace("<li class='answer correct-answer user-answer'>", "<li ".$user_answer_style.">", $output);
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: '. $admin_email . "\r\n";
		
	if($who == 'admin') {
		$subject = sprintf(__('User results on "%s"', 'watu'), stripslashes($exam->name));	
		$user_data = empty($uid) ? __('Guest', 'watupro') : $user_email;	
		$message = "Details of $user_data:<br><br>".$output;	
		
		foreach($admin_emails as $admin_email) {			
			wp_mail($admin_email, $subject, $message, $headers);
		}
	}  
	else {
		// email user
		if(empty($uid)) {			
			$user_email = $_POST['watu_taker_email'];
		}	
		
		if(empty($user_email)) return true;
		
		$subject = sprintf(__('Your results on "%s"', 'watu'), stripslashes($exam->name));	
		$message = $output;	
		
		wp_mail($user_email, $subject, $message, $headers);
	}
   // echo $message;   
} // end watu_notify