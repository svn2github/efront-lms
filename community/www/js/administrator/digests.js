/**

	Function to check whether recipients have been selected and whether a subject has been defined

	Did not use rules of Quickform due to the fact that the first rule is a composite one

	*/
 function eF_js_checkRecipients() {
     //if (document.forms[0].recipients[0].checked && document.getElementById('autocomplete').value == "") {
     if (document.forms[0].recipients[0].checked) {
         alert(noRecipientsHaveBeenSelected);
         return false;
     } else {
         if (document.getElementById('msg_subject').value == "") {
             alert(theFieldConst + "'"+ subjectConst+"'" + isMandatoryConst);
             return false;
         } else {
             return true;
         }
     }
 }
 var additional_recipients_hidden = 1;
 var additional_recipients_lock = 1;
 function show_hide_additional_recipients() {
     if(additional_recipients_lock) {
         additional_recipients_lock = 0;
         if (additional_recipients_hidden) {
             additional_recipients_hidden = 0;
             $('arrow_down').setStyle("display:none;");
             $('arrow_up').setStyle("display:block;");
             new Effect.toggle( $('additional_recipients_categories'),'BLIND',{queue:{scope:'myscope', position:'end', limit: 2}, duration:1.0});
             //$('autocomplete').value = "";
         } else {
             additional_recipients_hidden = 1;
             $('arrow_up').setStyle("display:none;");
             $('arrow_down').setStyle("display:block;");
             new Effect.toggle( $('additional_recipients_categories'),'BLIND',{queue:{scope:'myscope', position:'end', limit: 2}, duration:1.0});
             //$('only_specific_users').checked = "true";
             eF_js_selectRecipients('active_users');
         }
     }
     setTimeout(function(){ additional_recipients_lock = 1;}, 1001);

 }


 function eF_js_selectRecipients(recipient) {

     // enterprise version: Initially disable all new HCD related recipient selects - the one needed will be enabled later
     document.getElementById('course_recipients').disabled = 'disabled';
     document.getElementById('specific_course_completed_check').selected = 'false';
     document.getElementById('specific_course_completed_check').style.visibility = 'hidden';
     document.getElementById('specific_course_completed_label').style.visibility = 'hidden';
     document.getElementById('group_recipients').disabled = 'disabled';
     document.getElementById('lesson_recipients').disabled = 'disabled';
     document.getElementById('user_type_recipients').disabled = 'disabled';
     document.getElementById('lesson_professor_recipients').disabled = 'disabled';
     switch (recipient) {
         case 'specific_lesson':
             document.getElementById('lesson_recipients').disabled = '';
             break;
         case 'specific_course':
             document.getElementById('course_recipients').disabled = '';
             document.getElementById('specific_course_completed_label').style.visibility = 'visible';
             document.getElementById('specific_course_completed_check').style.visibility = 'visible';
             break;
         case 'specific_lesson_professor':
             document.getElementById('lesson_professor_recipients').disabled = '';
             break;
         case 'specific_type':
             document.getElementById('user_type_recipients').disabled = '';
             break;
         case 'specific_group':
             document.getElementById('group_recipients').disabled = '';
             break;
         // enterprise version: Enable/disable new HCD related recipient selects 
     }
 }
 // Function returning the position of the cursor within an element
 function getCursor(el){
  Element.extend(el);
  el.focus();
  var cursorPos = 0;
      if (document.selection) {
   // IE code
   // make sure it's the textarea's selection
   var range = document.selection.createRange();
   // create a selection of the whole textarea
   var range_all = document.body.createTextRange();
   range_all.moveToElementText(el);
   // calculate selection start point by moving beginning of range_all to beginning of range
   for (var sel_start = 0; range_all.compareEndPoints('StartToStart', range) < 0; sel_start ++) {
    range_all.moveStart('character', 1);
   }
      // get number of line breaks from textarea start to selection start and add them to sel_start
      for (var i = 0; i <= sel_start; i ++) {
         if (el.value.charAt(i) == '\n') {
           sel_start ++;
         }
      }
      el.sel_start = sel_start;
      // create a selection of the whole el
      var range_all = document.body.createTextRange();
       range_all.moveToElementText(el);
      // calculate selection end point by moving beginning of range_all to end of range
      for (var sel_end = 0; range_all.compareEndPoints('StartToEnd', range) < 0; sel_end ++) {
       range_all.moveStart('character', 1);
      }
       // get number of line breaks from el start to selection end and add them to sel_end
       for (var i = 0; i <= sel_end; i ++) {
          if (el.value.charAt(i) == '\n') {
            sel_end ++;
       }
   }
      el.sel_end = sel_end;
      // get selected and surrounding text
      el.sel_text = range.text;
     cursorPos = sel_start;
   } else {
    // Good-browser code
    if (el.selectionStart || el.selectionStart == '0') {
    cursorPos = el.selectionStart;
    sel_start = el.selectionStart;
    sel_end = el.selectionEnd;
   }
  }
  // Remove the selected text 
  if (sel_start != sel_end) {
   var text_before = el.value.substring(0, sel_start);
       var text_after = el.value.substring(sel_end, el.value.length);
    el.value = text_before + text_after;
  }
  return cursorPos;
   }
 var editorCursorPosition = 0;
 myActiveElement="";
 if ($('messageBody')) {
  /*

		var ed = new tinymce.Editor('messageBody');

		ed.onActivate.add(function(ed) {

		  //alert("ksypna reee");

		  myActiveElement="";

		});

		ed.render();

		*/
 }
 // Functions to templatize the message body	 
 function addTemplatizedText(el) {
  if (myActiveElement != "") {
   var textAreaElement = $(myActiveElement);
   var cursor = getCursor(textAreaElement);
   textAreaElement.value = textAreaElement.value.substr(0, cursor) + "###" + el.value + "###" + textAreaElement.value.substr(cursor);
  } else {
      if (editorCursorPosition) {
          tinyMCE.selectedInstance.selection.moveToBookmark(editorCursorPosition);
          editorCursorPosition = 0;
      }
   tinyMCE.execInstanceCommand("messageBody","mceInsertContent",false, "###" + el.value + "###");
  }
  // Special treatment for unit content management
  if (el.value == "unit_content") {
   $('html_message_id').checked = true;
  }
 }
 function addLanguageTag(el) {
     if (editorCursorPosition) {
         tinyMCE.selectedInstance.selection.moveToBookmark(editorCursorPosition);
         editorCursorPosition = 0;
     }
  tinyMCE.execInstanceCommand("messageBody","mceInsertContent",false, "<br><------------------------" + el.value + "------------------------><br><br>");
 }
 // Appends pair(value-text) to the select_item
 function addValueToSelect(value, text, select_item) {
  var elOptNew = document.createElement('option');
  elOptNew.value = value;
  elOptNew.text = text.replace('&#039;', "'");
  try {
      select_item.add(elOptNew,null);
  } catch(ex) {
      select_item.add(elOptNew); // IE only
  }
 }
 // Change the templated texts offered by the templates select	
 function changeTemplates(mode) {
     var select_item = document.getElementById('template_add');
     // Delete all but the basic events
     while(select_item.length > basicTemplated) {
         select_item.remove(basicTemplated);
     }
  // Delete all but basic recipients categories
  var recipients_select_item = document.getElementById('event_recipients');
  while(recipients_select_item.length > basicEventRecipients) {
         recipients_select_item.remove(basicEventRecipients);
     }
     var elOptNew;
     if (mode != "basic") {
         addValueToSelect ("triggering_users_name" , trigUserNameConst, select_item);
      addValueToSelect ("triggering_users_surname", trigUserSurnConst, select_item);
      addValueToSelect ("triggering_users_login" , trigUserLogiConst, select_item);
      addValueToSelect ("triggering_user_type" , trigUserTypeConst, select_item);
      addValueToSelect ("triggering_users_email" , trigUserEmailConst, select_item);
     }
  if (mode != "system" && mode != "courses" && mode != "branch" && mode != "job") {
   addValueToSelect ("lessons_name", lessonsNameConst, select_item);
   addValueToSelect (allLessonEventRecipients, allLessonUsersConst, recipients_select_item);
   addValueToSelect (lessonProf, lessonProfessorsConst, recipients_select_item);
   addValueToSelect (lessonNotCompleted, lessonNotCompletedConst, recipients_select_item);
  }
  if (mode == "courses") {
   addValueToSelect ("courses_name", courseNameConst, select_item);
   addValueToSelect (courseProf, courseProfessorsConst, recipients_select_item);
  }
  if (mode == "tests") {
   addValueToSelect("tests_name", testNameConst, select_item);
  }
  if (mode == "news") {
   addValueToSelect("announcement_title", announcementTitleConst, select_item);
   addValueToSelect("announcement_body", announcementBodyConst, select_item);
  }
  if (mode == "content") {
   addValueToSelect("unit_title", unitNameConst, select_item);
   addValueToSelect("unit_content", unitContentConst, select_item);
  }
  if (mode == "survey") {
   addValueToSelect("survey_name", surveyNameConst, select_item);
   addValueToSelect("survey_id", surveyIdConst, select_item);
   addValueToSelect("survey_message", surveyMessageConst, select_item);
  }
  if (mode == "branch" || mode == "job") {
   addValueToSelect("branch_name", branchNameConst, select_item);
  }
  if (mode == "job") {
   addValueToSelect("job_description_name", jobNameConst, select_item);
  }
 }
 function changeMessageType(el, shouldChangeTemplates) {
   if (el.value == "0") {
    $('on_after_event').setStyle({display:'none'});
    $('on_date').setStyle({display:'block'});
    if ($('message_frequency').value == "0") {
     $('send_interval_div').setStyle({display:'none'});
    } else {
     $('send_interval_div').setStyle({display:'block'});
    }
    $('recipients').setStyle({display:'block'});
    $('event_recipients_div').setStyle({display:'none'});
    $('send_immediately_row').setStyle({display:'none'});
    if (shouldChangeTemplates) {
     changeTemplates("basic");
    }
   } else {
    if (_currentEventCategory != "") {
     if ($('av_' + _currentEventCategory + '_div')) {
      $('av_' + _currentEventCategory + '_div').setStyle({display:'none'});
     } else if (_currentEventCategory != "system" && _currentEventCategory != "branch" && _currentEventCategory != "job") {
      $('av_lessons_div').setStyle({display:'none'});
     }
    }
    $('recipients').setStyle({display:'none'});
    if (el.value == "2") {
     $('send_interval_div').setStyle({display:'block'});
     $('send_interval_label').innerHTML = timeAfterEvent + ":&nbsp;";
     $('event_types').setStyle({display:'none'});
     $('event_types_before').setStyle({display:'none'});
     $('event_types_after').setStyle({display:'block'});
     $('send_immediately_row').setStyle({display:'none'});
     parts = $('event_types_after').value.split("_");
     _currentEventCategory = parts[1];
    } else if (el.value == "3") {
     $('send_interval_div').setStyle({display:'block'});
     $('send_interval_label').innerHTML = timeBeforeEvent + ":&nbsp;";
     $('event_types').setStyle({display:'none'});
     $('event_types_after').setStyle({display:'none'});
     $('event_types_before').setStyle({display:'block'});
     $('send_immediately_row').setStyle({display:'none'});
     parts = $('event_types_before').value.split("_");
     _currentEventCategory = parts[1];
    } else {
     $('send_interval_div').setStyle({display:'none'});
     $('event_types_after').setStyle({display:'none'});
     $('event_types_before').setStyle({display:'none'});
     $('event_types').setStyle({display:'block'});
     $('send_immediately_row').setStyle({display:'block'});
     parts = $('event_types').value.split("_");
     _currentEventCategory = parts[1];
    }
    // changing the *templates* for the notification body
    if (shouldChangeTemplates) {
     changeTemplates(_currentEventCategory);
    }
    if (_currentEventCategory != "") {
     if ($('av_' + _currentEventCategory + '_div')) {
      $('av_' + _currentEventCategory + '_div').setStyle({display:'block'});
     } else if (_currentEventCategory != "system" && _currentEventCategory != "branch" && _currentEventCategory != "job") {
      $('av_lessons_div').setStyle({display:'block'});
     }
    }
    $('on_date').setStyle({display:'none'});
    $('on_after_event').setStyle({display:'block'});
    $('event_recipients_div').setStyle({display:'block'});
    //mchangeEventCategory($('event_category'));
   }
 }
 function changeMessageFrequency() {
  if ($('send_interval_div').style.display == "none") {
   $('send_interval_div').setStyle({display:'block'});
   $('send_interval_label').innerHTML = everyConst + ":&nbsp;";
   $('specific_date_label').innerHTML = startingFrom + ":&nbsp;";
  } else {
   $('send_interval_div').setStyle({display:'none'});
   $('specific_date_label').innerHTML = onConst + ":&nbsp;";
  }
 }
 var _currentEventCategory = "";
 function changeEventCategory(el) {
  if (_currentEventCategory != "") {
   if ($('av_' + _currentEventCategory + '_div')) {
    $('av_' + _currentEventCategory + '_div').setStyle({display:'none'});
   } else if (_currentEventCategory != "system" && _currentEventCategory != "branch" && _currentEventCategory != "job") {
    $('av_lessons_div').setStyle({display:'none'});
   }
  }
  parts = el.value.split("_");
  _currentEventCategory = parts[1];
  changeTemplates(_currentEventCategory);
  if (_currentEventCategory != "") {
   if ($('av_' + _currentEventCategory + '_div')) {
    $('av_' + _currentEventCategory + '_div').setStyle({display:'block'});
   } else if (_currentEventCategory != "system" && _currentEventCategory != "branch" && _currentEventCategory != "job") {
    $('av_lessons_div').setStyle({display:'block'});
   }
  }
 }
 var willBecomeActive;
 var thisNotificationId;
 var isEvent;
    function activate(el, notification_id, is_event) {
        Element.extend(el);
        // Change from activated to deactivated row
        if (is_event != '') {
         row_element = $('notification_row_'+ notification_id + '_1');
         isEvent = true;
        } else {
         row_element = $('notification_row_'+ notification_id + '_0');
         isEvent = false;
        }
        if (row_element.className.match("deactivated")) {
            url = sessionType + '.php?ctg=digests&postAjaxRequest=1&activate_notification='+notification_id+'&event='+is_event;
            willBecomeActive = true;
        } else {
            url = sessionType + '.php?ctg=digests&postAjaxRequest=1&deactivate_notification='+notification_id+'&event='+is_event;
            willBecomeActive = false;
        }
        parameters ={method:'get'};
  thisNotificationId = notification_id;
  ajaxRequest(el.down(), url, parameters, onActivateSuccess);
        //var img = new Element('img', {id: 'img_'+notification_id+is_event, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
        //el.getOffsetParent().insert(img);
        //el.down().src = 'images/16x16/trafficlight_yellow.png';
        /*

        new Ajax.Request(url, {

            method:'get',

            asynchronous:true,

            onSuccess: 

            });

            */
    }
 function onActivateSuccess(el, responseText) {
  if (willBecomeActive) {
   el.removeClassName('sprite16-trafficlight_red').addClassName('sprite16-trafficlight_green');
   imageText = deactivateConst;
        } else {
            el.removeClassName('sprite16-trafficlight_green').addClassName('sprite16-trafficlight_red');
   imageText = activateConst;
  }
        tables = sortedTables.size();
        var i;
        for (i = 0; i < tables; i++) {
            if (sortedTables[i].id.match("msgQueueTable")) {
                eF_js_rebuildTable(i, 0, 'null', 'desc');
            }
        }
        // Change from activated to deactivated row
        if (isEvent) {
          row_element = $('notification_row_'+ thisNotificationId + '_1');
          status_element = $('notification_status_' + thisNotificationId + '_1');
        } else {
          row_element = $('notification_row_'+ thisNotificationId + '_0');
          status_element = $('notification_status_' + thisNotificationId + '_0');
        }
        row_class = row_element.className.split(" ");
  if (row_class.length > 1) {
         row_element.className = row_class[0]; // remove the "deactivatedTableElement"
         status_element.innerHTML = "1";
        } else {
         row_element.className = row_element.className + " deactivatedTableElement";
         status_element.innerHTML = "0";
        }
 }
    function onUseCron() {
     if ($('notifications_use_cron')) {
      if ($('notifications_use_cron').checked) {
       $('notificatoins_maximum_inter_time').disabled = "disabled";
       $('notificatoins_pageloads').disabled = "disabled";
      } else {
       $('notificatoins_maximum_inter_time').disabled = "";
       $('notificatoins_pageloads').disabled = "";
      }
     }
    }
    onUseCron();
 if (addEditNotification) {
  if (eventForm != "") {
   if ($('av_'+ eventCategory + '_div')) {
    $('av_'+ eventCategory + '_div').setStyle({display:'block'});
   }
   _currentEventCategory = eventCategory;
   if ($('type_when')) {
    changeMessageType($('type_when'), false);
   }
  } else {
   if (recipientsCategory != "active_users") {
    eF_js_selectRecipients(recipientsCategory);
    show_hide_additional_recipients();
   }
  }
 }
