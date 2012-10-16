// wrap in object to avoid polluting the javascript namespace
var Watu={};

Watu.current_question = 1;
Watu.total_questions = 0;
Watu.mode = "show";

Watu.checkAnswer = function(e) {	
	// don't check for textareas	
	if(jQuery('#questionType' + Watu.current_question).val() == 'textarea') return true; 	
	
	var answered = false;

	jQuery("#question-" + Watu.current_question + " .answer").each(function(i) {
		if(this.checked) {
			answered = true;
			return true;
		}
	});
	if(!answered) {
		if(!confirm("You did not select any answer. Are you sure you want to continue?")) {
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
	}
	return true;
}

Watu.nextQuestion = function(e) {
	if(!Watu.checkAnswer(e)) return;

	jQuery("#question-" + Watu.current_question).hide();
	Watu.current_question++;
	jQuery("#question-" + Watu.current_question).show();

	if(Watu.total_questions <= Watu.current_question) {
		jQuery("#next-question").hide();
		jQuery("#action-button").show();
	}
}

// This part is used only if the answers are show on a per question basis.
Watu.showAnswer = function(e) {
	if(!Watu.checkAnswer(e)) return;

	if(Watu.mode == "next") {
		Watu.mode = "show";

		jQuery("#question-" + Watu.current_question).hide();
		Watu.current_question++;
		jQuery("#question-" + Watu.current_question).show();

		jQuery("#show-answer").val("Show Answer");
		return;
	}

	Watu.mode = "next";

	jQuery(".correct-answer-label.label-"+Watu.current_question).addClass("correct-answer");
	jQuery(".answer-"+Watu.current_question).each(function(i) {
		if(this.checked && this.className.match(/wrong\-answer/)) {
			var number = this.id.toString().replace(/\D/g,"");
			if(number) {
				jQuery("#answer-label-"+number).addClass("user-answer");
			}
		}
	});

	if(Watu.total_questions <= Watu.current_question) {
		jQuery("#show-answer").hide();
		jQuery("#action-button").show();
	} else {
		jQuery("#show-answer").val("Next >");
	}
}

Watu.submitResult = function() {
	var data = {action:'show_exam_result', quiz_id: exam_id, 'question_id[]': Watu.qArr };
	
	for(x=0; x<Watu.qArr.length; x++) 
   {
      // qArr[x] is the question ID
		var ansgroup = '.answerof-'+Watu.qArr[x];
		var fieldName = 'answer-'+Watu.qArr[x];
		var ansvalues= Array();
		var i=0;
        
	    if(jQuery('#textarea_q_'+Watu.qArr[x]).length>0)
	    {
	        // open end question
	        // console.log(jQuery('#textarea_q_'+qArr[x]).val());
	        ansvalues[0]=jQuery('#textarea_q_'+Watu.qArr[x]).val();
	    }
	    else
	    {
	        jQuery(ansgroup).each(function(){
			if( jQuery(this).is(':checked') ) {
				ansvalues[i] = this.value;
				i++;
	  			}
	  		});    
	    }
		
		data[fieldName+'[]'] = ansvalues;
	}
	
	jQuery('#watu_quiz').html("<p>Loading...</p>");
    
	//var v=''; for(a in data) v+=data[a]+'\n'; alert(v);
	try{
	jQuery.ajax({ type: 'POST', url: watuURL, data: data, success: Watu.success, error: Watu.error  });
	}catch(e){ alert(e)}
}

Watu.success = function(r){ jQuery('#watu_quiz').html(r);}
Watu.error = function(){ jQuery('#watu_quiz').html('Error Occured');}

Watu.initWatu = function() {
	jQuery("#question-1").show();
	Watu.total_questions = jQuery(".watu-question").length;

	if(Watu.total_questions == 1) {
		jQuery("#action-button").show();
		jQuery("#next-question").hide();
		jQuery("#show-answer").hide();

	} else {
		jQuery("#next-question").click(Watu.nextQuestion);
		jQuery("#show-answer").click(Watu.showAnswer);
	}
	jQuery("#action-button").click(Watu.nextQuestion);
}

jQuery(document).ready(Watu.initWatu);
