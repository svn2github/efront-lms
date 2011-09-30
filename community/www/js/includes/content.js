/**
 * Set unit seen status
 * This function makes an ajax call to the server to set the current unit's status to either 'seen' or 'unseen', based on
 * the status parameter 
 */
function setSeenUnit(status) {
    if (typeof(status) == 'undefined') {        //If "status" parameter is not set, then toggle the seen status based on whether the user has seen the lesson
        hasSeen ? status = 0 : status = 1;
    } else {            //If "status" parameter is set, then toggle the seen status based on this parameter
    	status ? status = 1 : status = 0;
    	hasSeen = !status;
    }
    //el = $('seenLink');					//For backwards-compatibility, we don't specify el in the parameters list
    if ($('seenLink')) {
    	$('seenLink').blur();
    	el = $('seenLink').down();
    } else {
    	el = new Element('div');
    }

	parameters = {set_seen:status, method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onSetSeenUnit);	
}
/*
 * This function is executed when the ajax call of setSeen returns, to set the appropriate text and
 * depict the unit status, both in the 'set seen' icon and the content tree as well 
 */ 
function onSetSeenUnit(el, response) {
	try {
		
		results = response.evalJSON();
		if (unitType == 'scorm') {
			unitType = 'theory';		//scorm does not have icons of its own any more 
		} else if (unitType == 'scorm_test') {
			unitType = 'tests';		 
		}

        if (hasSeen) {
        	if ($('seenLink')) {
	        	setImageSrc($('seenLink').down(), 32, 'unit.png');            
	        	$('seenLink').down().next().update(sawunit);
        	}
        	if ($('tree_image_'+unitId)) {
        		setImageSrc($('tree_image_'+unitId), 16, unitType+'.png');
        	}
        } else {
        	if ($('seenLink')) {
	        	setImageSrc($('seenLink').down(), 32, 'unit_completed.png');
	        	$('seenLink').down().next().update(notsawunit);
        	}
        	if ($('tree_image_'+unitId)) {
        		setImageSrc($('tree_image_'+unitId), 16, unitType+'_passed.png');
        	}
        }
        if ($('seenLink')) {
        	new Effect.Appear($('seenLink'));
        }
        
        hasSeen = !hasSeen;
        if ($('progress_bar')) {
            $('progress_bar').select('span.progressNumber')[0].update(parseFloat(results[0]) + '%');
            $('progress_bar').select('span.progressBar')[0].setStyle({width:parseFloat(results[0]) + 'px'});
            if ($('passed_conditions')) {
            	$('passed_conditions').update(parseInt(results[1]));
            }
            if ($('lesson_passed')) {
            	if (results[2] == true) {
            		$('lesson_passed').down().removeClassName('failure').addClassName('success');
            		$('completed_block').show();
					Effect.ScrollTo('completed_block');
            	} else {
            		$('lesson_passed').down().removeClassName('success').addClassName('failure');
            		//$('completed_block').hide();
            	}
            }
        }
	} catch (e) {}
}

function nextLesson(el) {
	parameters = {ajax:'next_lesson', method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onNextLesson);	
}
function onNextLesson(el, response) {
	if (response.evalJSON(true)) {
		if (response.evalJSON(true).url) {
			window.location = response.evalJSON(true).url;
		} else {
			alert(translations['_YOUAREATTHELASTLESSONYOUMAYVISIT']);
		}
	}
	////'{$smarty.server.PHP_SELF}?lessons_ID={$T_NEXT_LESSON}{if $smarty.session.s_courses_ID}&from_course={$smarty.session.s_courses_ID}{/if}'
}

/**
 * This function automatically navigates to the next unit, if any
 */
function nextUnit() {
	if (typeof(nextId) != 'undefined') {
		window.location = window.location.toString().replace(/view_unit=\d*/, "view_unit="+nextId);
	}
}
/**
 * This function automatically navigates to the previous unit, if any
 */
function previousUnit() {
	if (typeof(previousId) != 'undefined') {
		window.location = window.location.toString().replace(/view_unit=\d*/, "view_unit="+previousId);
	}
}


function insertatcursor(myField, myValue) {

    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    }
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)+ myValue+ myField.value.substring(endPos, myField.value.length);
    } else {
        myField.value += myValue;
    }
}
var checkToggle;
function togglePdf() {
	$('pdf_upload').toggle();
	$('pdf_upload_max_size').toggle();
	$('pdf_content').toggle();
	$('nonPdfTable').toggle();
	$('toggleTools').toggle();
	if (checkToggle == true) {
		if ($('editor_content_data').value != '') {
			$('content_toggle').value = $('editor_content_data').value;
			$('editor_content_data').value='';
		} else {
			$('editor_content_data').value = $('content_toggle').value;
		}
	}
	
}
function toggleAdvancedParameters() {

	if ($('scorm_asynchronous')) {
		$('scorm_asynchronous').toggle();
		$('scorm_asynchronous_explanation').toggle();
	}
	$('maximize_viewport').toggle();
	$('object_ids').toggle();
	$('no_before_unload').toggle();
	$('indexed').toggle();
	$('accessible_explanation').toggle();
	if ($('advenced_parameter_image').className.match("down")) {
		setImageSrc($('advenced_parameter_image'), 16, 'navigate_up.png');
	} else {
		setImageSrc($('advenced_parameter_image'), 16, 'navigate_down.png');
	}
}

function answerQuestion(el) {
	Element.extend(el);
	$('correct_answer').hide();
	$('wrong_answer').hide();
	el.up().insert(new Element('img', {src:'themes/default/images/others/progress1.gif', id:'progress_image'}).setStyle({verticalAlign:'middle', marginLeft:'5px'}));
	$('question_form').request({
		onFailure: function(transport) {
		$('progress_image').remove();
		showMessage(transport.responseText, 'failure');
	},
	onSuccess:function(transport) {
		if (transport.responseText == 'correct') {
			new Effect.Appear($('correct_answer'));
			setSeenUnit(1);
		} else {
			new Effect.Appear($('wrong_answer'));
			setSeenUnit(0);
		}
		$('progress_image').remove();
		
	}
	});
}



/**
* This function prevents a link from loading upon click.
*/
function disableLink (s)
{
	s.stop();
}

function updateProgress(obj) {
	if (!(w = findFrame(top, 'mainframe'))) {
		w = window;
	}
	
	try {
		var progress 	  = obj[0];
		var conditions 	  = obj[1];
		var lesson_passed = obj[2];
		var unitStatus	  = obj[5];
		
	    if (w.$('progress_bar')) {
	    	w.$('progress_bar').select('span.progressNumber')[0].update(parseFloat(progress) + '%');
	    	w.$('progress_bar').select('span.progressBar')[0].setStyle({width:parseFloat(progress) + 'px'});
	    	if (w.$('passed_conditions')) {
	    		w.$('passed_conditions').update(parseInt(conditions));
	    	}
	    	if (w.$('lesson_passed')) {
				if (lesson_passed == true) {
					w.$('lesson_passed').down().removeClassName('failure').addClassName('success')
					w.$('completed_block').show();
					//Effect.ScrollTo('completed_block');
				} else {
					w.$('lesson_passed').down().removeClassName('success').addClassName('failure');
					//$('completed_block').hide();
				}
	    	}
	    }
	    
	    
	    var status = '';
	    for (var i in unitStatus) {
	    	var status = '';
	    	if (unitStatus[i].completion_status == 'completed' || unitStatus[i].success_status == 'passed') {
	    		status = '_passed';
	    	} else if (unitStatus[i].completion_status == 'incomplete' && unitStatus[i].success_status == 'unknown') {
	    		status = '_incomplete';
	    	} else if (unitStatus[i].success_status == 'failed') {
	    		status = '_failed';
	    	} else {
	    		status = '';
	    	}
	    	
	    	if (w.$('tree_image_'+i)) {
	    		w.$('tree_image_'+i).className.match(/tests/) ? type = 'tests' : type = 'theory';
	    		setImageSrc(w.$('tree_image_'+i), 16, type+status);
	    	}	
	    }
	    
	    if (nodesStatus = obj[3]) {
		    if ($('navigate_continue')) {
			    if (nodesStatus['continue'] == 'enabled') {
			    	$('navigate_continue').removeClassName('inactiveImage');
					$('navigate_continue').onclick = '';
					$('navigate_continue').show();

			    } else if (nodesStatus['continue'] == 'disabled') {
					$('navigate_continue').addClassName('inactiveImage');
					$('navigate_continue').onclick = function () {return false;};
					$('navigate_continue').show();

			    } else if (nodesStatus['continue'] == 'hidden') {
			    	$('navigate_continue').hide();
			    }
		    }
		    if ($('navigate_previous')) {
			    if (nodesStatus['previous'] == 'enabled') {
			    	$('navigate_previous').removeClassName('inactiveImage');
					$('navigate_previous').onclick = '';					
					$('navigate_previous').show();
					
			    } else if (nodesStatus['previous'] == 'disabled') {
			    	$('navigate_previous').addClassName('inactiveImage');
					$('navigate_previous').onclick = function () {return false;};					
					$('navigate_previous').show();
					
			    } else if (nodesStatus['previous'] == 'hidden') {
			    	$('navigate_previous').hide();
			    }
		    }
		    if ($('navigate_exitAll')) {
			    if (nodesStatus['exitAll'] == 'enabled') {
			    	$('navigate_exitAll').removeClassName('inactiveImage');
					$('navigate_exitAll').onclick = '';	
					$('navigate_exitAll').show();
					
			    } else if (nodesStatus['exitAll'] == 'disabled') {
			    	$('navigate_exitAll').addClassName('inactiveImage');
					$('navigate_exitAll').onclick = function () {return false;};	
					$('navigate_exitAll').show();
					
			    } else if (nodesStatus['exitAll'] == 'hidden') {
			    	$('navigate_exitAll').hide();
			    }
		    }
		    if ($('navigate_suspendAll')) {
			    if (nodesStatus['suspendAll'] == 'enabled') {
			    	$('navigate_suspendAll').removeClassName('inactiveImage');
					$('navigate_suspendAll').onclick = '';
					$('navigate_suspendAll').show();
					
			    } else if (nodesStatus['suspendAll'] == 'disabled') {
			    	$('navigate_suspendAll').addClassName('inactiveImage');
					$('navigate_suspendAll').onclick = function () {return false;};	
					$('navigate_suspendAll').show();
										
			    } else if (nodesStatus['suspendAll'] == 'hidden') {
			    	$('navigate_suspendAll').hide();
			    }
		    }
		    if ($('navigate_abandon')) {
			    if (nodesStatus['abandon'] == 'enabled') {
			    	$('navigate_abandon').removeClassName('inactiveImage');
					$('navigate_abandon').onclick = '';
					$('navigate_abandon').show();
					
			    } else if (nodesStatus['abandon'] == 'disabled') {
			    	$('navigate_abandon').addClassName('inactiveImage');
					$('navigate_abandon').onclick = function () {return false;};
					$('navigate_abandon').show();

			    } else if (nodesStatus['abandon'] == 'hidden') {
			    	$('navigate_abandon').hide();
			    }
		    }
		    if ($('navigate_abandonAll')) {
			    if (nodesStatus['abandonAll'] == 'enabled') {
			    	$('navigate_abandonAll').removeClassName('inactiveImage');
					$('navigate_abandonAll').onclick = '';	
					$('navigate_abandonAll').show();
					
			    } else if (nodesStatus['abandonAll'] == 'disabled') {
			    	$('navigate_abandonAll').addClassName('inactiveImage');
					$('navigate_abandonAll').onclick = function () {return false;};
					$('navigate_abandonAll').show();

			    } else if (nodesStatus['abandonAll'] == 'hidden') {
			    	$('navigate_abandonAll').hide();
			    }
		    }
			
		    for (var i in nodesStatus['choice']) {

		    	if (nodesStatus['choice'][i] == 'enabled') {
		    		$('node'+i).select('a')[0].removeClassName('inactiveLink');
					$('node'+i).select('a')[0].onclick = '';
		    		$('node'+i).show();

				} else if (nodesStatus['choice'][i] == 'disabled') {
		    		$('node'+i).select('a')[0].addClassName('inactiveLink');
					 $('node'+i).select('a')[0].onclick = function () {return false;};

		    		$('node'+i).show();

		    	} else if (nodesStatus['choice'][i] == 'hidden') {
		    		$('node'+i).hide();
		    	}
		    }
	    }
	} catch (e) {
		alert(e);
	}
}

if (top.sideframe && !usingHorizontalInterface) {
    if (typeof(show_left_bar) == 'undefined' || parseInt(show_left_bar)) {
    	showLeftSidebar();        
    } else {
    	hideLeftSidebar();
    }
}
/**
* This function is used to resize scorm iframe, so that it spans through the entire page
*/
function eF_js_setCorrectIframeSize(setHeight)
{
	//Event.observe($('scormFrameID').contentWindow, 'beforeunload', function (s) {alert('b');});
	
    if (frame = window.document.getElementById('scormFrameID')) {
        innerDoc    = (frame.contentDocument) ? frame.contentDocument : frame.contentWindow.document;
        
        //Some contents send the final commit after the page closes, thus causing the tree and progress to not be updated on time.
        //For this reason, we copy the onunload event to a beforeunload event. In order to make for some weird contents that use the
        //onunload in other circumstances as well, there is a flag called "noBeforeUnload" that disables this event copying               
        if (typeof(noBeforeUnload) == 'undefined' || !noBeforeUnload) {
            //Firefox
	        if ($('scormFrameID').contentWindow.onunload) {
	        	Event.observe($('scormFrameID').contentWindow, 'beforeunload', $('scormFrameID').contentWindow.onunload);
	        }
	        //IE
	        else if ($('scormFrameID').contentWindow.document.body.onunload) {
	        	Event.observe($('scormFrameID').contentWindow, 'beforeunload', $('scormFrameID').contentWindow.document.body.onunload);
	        }
	        //Sub frames: in case the main doc is frameset, we must go through its frames and apply the same
	        else if ($('scormFrameID').contentWindow.frames && $('scormFrameID').contentWindow.frames.length > 0) {
	        	for (var i = 0; i < $('scormFrameID').contentWindow.frames.length; i++) {
	        		w = $('scormFrameID').contentWindow.frames[i];
	        		//FF
	        		if (w.onunload) {
	        			Event.observe(w, 'beforeunload', w.onunload);
	        		}
	        		//IE
	        		else if (w.document.body.onunload) {
	        			Event.observe($('scormFrameID').contentWindow, 'beforeunload', w.document.body.onunload);
	        		}
	        	}
	        } 
        }

        objToResize = (frame.style) ? frame.style : frame;
        if (setHeight) {
        	objToResize.height = setHeight + 'px';
        } else {
	        if (frame.document) {
	            objToResize.height = Math.max(innerDoc.body.scrollHeight, frame.document.body.scrollHeight) + 500 + 'px';
	        } else {
	            objToResize.height = innerDoc.body.scrollHeight + 500 + 'px';
	        }
        }   
    }
	Event.observe($('scormFrameID').contentWindow, 'beforeunload', function (s) {setTimeout('', 1000);}); 
}

if (typeof(editPdfContent) != 'undefined' && editPdfContent) {
    togglePdf();
}

if (typeof(setIframeSize) != 'undefined' && setIframeSize) {
	eF_js_setCorrectIframeSize();
}

if (typeof(matchscreenobjectid) != 'undefined' && matchscreenobjectid) {
	var ids = matchscreenobjectid.split(','); //for more than one ids specified
	var viewport = document.viewport.getDimensions(); 
	var height = viewport.height; 

	for(i = 0; i < ids.length; i++){
	var idString = ids[i].toString();		
		if ($(idString)) {		
			$(idString).setStyle({height:height+'px'});
		} 
	}
}
if (typeof(autoSetSeenUnit) != 'undefined' && autoSetSeenUnit) {
	setSeenUnit(1);
}

function checkLessonConditions() {
	el = document.body;
	parameters = {check_conditions:true, method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onCheckLessonConditions);	
}
function onCheckLessonConditions(el, response) {
	results = response.evalJSON();
    if ($('passed_conditions')) {
    	$('passed_conditions').update(parseInt(results[1]));
    }
    if ($('lesson_passed')) {
    	if (results[2] == true) {
    		$('lesson_passed').down().removeClassName('failure').addClassName('success');
    		$('completed_block').show();
			Effect.ScrollTo('completed_block');
    	} 
    }	
}

function startContentTimer() {
    this.formatTime = function (v) {
    	if (v == 0) {
    		v = '00';
    	} else if (v.length == 1) {
    		v = "0" + v;
    	}
    	return v;
    }
	
    if (typeof(seconds) != 'undefined') {
    
	    if (seconds < 59) {seconds++;}
	    else {
	        if (seconds == 59 ) {seconds = 0;}
	        if (minutes < 59)  {minutes++;}
	        else {
	            if (minutes == 59) {minutes = 0;}
	            hours++;
	        }
	    }
	    min = this.formatTime(minutes.toString());
	    sec = this.formatTime(seconds.toString());
	    hours = this.formatTime(hours);
	        
	    
	    //if (hours.length == 1) {hours = "0" + hours;}
	    //if (min.length == 1) {min = "0" + min;}
	    //if (sec.length == 1) {sec = "0" + sec;}
	
	    $("user_time_in_unit_display").update(hours + ":" + min + ":" + sec);    
	    //$("user_current_time_in_unit").update(parseInt($("user_current_time_in_unit").innerHTML)+1);
	    
	    var newUserTotalTimeInUnit = parseInt($("user_total_time_in_unit").innerHTML)+1;
	    $('user_total_time_in_unit').update(newUserTotalTimeInUnit);
	    if (newUserTotalTimeInUnit == parseInt($('required_time_in_unit').innerHTML)) {
	    	setSeenUnit(true);
	    }    
	
	    if (lesson_seconds < 59) {lesson_seconds++;}
	    else {
	        if (lesson_seconds == 59 ) {lesson_seconds = 0;}
	        if (lesson_minutes < 59)  {lesson_minutes++;}
	        else {
	            if (lesson_minutes == 59) {lesson_minutes = 0;}
	            lesson_hours++;
	        }
	    }
	    lesson_min = this.formatTime(lesson_minutes.toString());
	    lesson_sec = this.formatTime(lesson_seconds.toString());
	    lesson_hours = this.formatTime(lesson_hours);
	    		
	    $("user_time_in_lesson_display").update(lesson_hours + ":" + lesson_min + ":" + lesson_sec);    
	    var newUserTimeInLesson = parseInt($("user_time_in_lesson").innerHTML)+1;
	    $("user_time_in_lesson").update(newUserTimeInLesson);
	    if (newUserTimeInLesson == parseInt($('required_time_in_lesson').innerHTML)) {
	    	checkLessonConditions();
	    }
    }
    
	//contentTimer = setTimeout("startContentTimer()", 1000);	
}
if (typeof(start_timer) != 'undefined' && start_timer) {
	pe = false;

	if (start_timer != 'flash') {
		var isIE = (navigator.appName == "Microsoft Internet Explorer");
		if (isIE) {
			document.onfocusout = function () {
				if (pe) {
					pe.stop();
				}
		    };    
		    document.onfocusin = function() {
				if (pe) {
					pe.stop();
				}
		    	pe = new PeriodicalExecuter(startContentTimer, 1);
		    };
		} else {
		    window.onblur  = function () {
				if (pe) {
					pe.stop();
				}
		    };    
		    window.onfocus = function() {
				if (pe) {
					pe.stop();
				}
		    	pe = new PeriodicalExecuter(startContentTimer, 1);
		    };
		}
	}
	if (!pe) {
		pe = new PeriodicalExecuter(startContentTimer, 1);
	}
}

function handleDrop(s,d, e) {	        
	s.setStyle({left:'auto', top:'auto'});
	d.next().insert(s.remove());
	s.down().value = d.down().value;
	dragdrop[s.id]=d.id
	Droppables.remove(d);
}
function handleDrag(s, e,questionId) {	        
	if (dragdrop[s.element.id]) {
		Droppables.add($(dragdrop[s.element.id]), {accept:'draggable', onDrop:handleDrop});
		$('source_'+questionId+'_'+s.element.id.match(/firstlist_\d+_(\d+)/)[1]).insert(s.element.remove());
	}
}
function initDragDrop(questionId, keys) {
	for (var i = 0; i < keys.length; i++) {
		Droppables.add('secondlist_'+questionId+'_'+keys[i], {accept:'draggable', onDrop:handleDrop});
		new Draggable('firstlist_'+questionId+'_'+keys[i], {revert:'failure', onStart:function (s,e) {handleDrag(s,e,questionId);}});
	}
}
dragdrop = new Object();
if (typeof(dragDropQuestions) != 'undefined') {
	dragDropQuestions.each(function (s) {initDragDrop(s, dragDropQuestionKeys[s]);})
}

function retrieveInformation() {
	return _information_json;
}

function setUnitCompletionOptions(el) {
	Element.extend(el);
	if (el.options[el.options.selectedIndex].value == 2) {
		if ($('complete_question_row')) {
			$('complete_question_row').show();
		}
		if ($('complete_time_row')) {
			$('complete_time_row').hide();
		}
	} else if (el.options[el.options.selectedIndex].value == 3) {
		if ($('complete_question_row')) {
			$('complete_question_row').hide();
		}
		if ($('complete_time_row')) {
			$('complete_time_row').show();
		}		
	} else {
		if ($('complete_question_row')) {
			$('complete_question_row').hide();
		}
		if ($('complete_time_row')) {
			$('complete_time_row').hide();
		}
	}
	
			
}

function setAllUnitsProperties(el, scorm) {
	Element.extend(el);
	if (el.previous().type == 'checkbox') {
		value = el.previous().checked ? 1 : 0;
	} else {
		value = el.previous().value;
	}

	ajaxRequest(el, location.toString(), {ajax:1, method:'get', option:el.previous().name, value:value, scorm:scorm});
}