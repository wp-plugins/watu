<?php
class WatuQuestion {
	// calculate points, correctness and return the class
	static function calculate($question, $ans, $ansArr, &$correct, &$class) {
		$points = 0;
				
		if($question->answer_type != 'textarea') {
			if($ans->correct == 1) $class .= ' correct-answer';
			if(  in_array($ans->ID , $ansArr) ) $class .= ' user-answer';
			if( in_array($ans->ID , $ansArr ) and $ans->correct == 1) $correct = true;
			if( in_array($ans->ID , $ansArr ) ) $points = $ans->point;
		}
		else {
			// textareas
			if( preg_grep("/".trim($ans->answer)."/i" , $ansArr) ) $class .= ' user-answer';
			if( preg_grep("/".trim($ans->answer)."/i" , $ansArr) and $ans->correct == 1) {$correct = true; $class .= ' correct-answer';}
			if( preg_grep("/".trim($ans->answer)."/i" , $ansArr) ) $points = $ans->point;
		}
		
		return $points;
	}
}