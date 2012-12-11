<?php
class WatuExam {
	// keep the questions after submit in the same order they were show to the user
	// and only the questions that were shown
	function reorder_questions($questions, $orders) {
		$new_questions = array();
		
		foreach($orders as $order) {
			foreach($questions as $question) {
				if($question->ID == $order) $new_questions[] = $question;
			}
		}
		
		return $new_questions;
	}
}