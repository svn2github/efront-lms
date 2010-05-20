function deleteMessage(el, id) {
 parameters = {'delete':id, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onDeleteMessage);
}
function onDeleteMessage(el, response) {
 if (location.toString().match('view')) {
  location = location.toString().replace(/&view=\d*/, '');
 } else {
  new Effect.Fade(el.up().up());
 }
}
function deleteFolder(el, id) {
 parameters = {'delete':id, folders: true, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onDeleteFolder);
}
function onDeleteFolder(el, response) {
 new Effect.Fade(el.up().up());
}
function flag_unflag(el, id) {
 parameters = {flag:id, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onFlagUnflag);
}
function onFlagUnflag(el, response) {
 if (response == '1') {
  setImageSrc(el, 16, 'flag_red');
 } else {
  setImageSrc(el, 16, 'flag_green');
 }
}
function moveMessage(el, id) {
 folder = $('target_message_folder').options[$('target_message_folder').options.selectedIndex].value;
 parameters = {move:id, folder:folder, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onMoveMessage);
}
function onMoveMessage(el, response) {
 if (location.toString().match('view')) {
  //location = location.toString().replace(/&view=\d*/, '');
  location.reload();
 } else {
  //new Effect.Fade(el.up().up());
 }
}


/**

Function to check whether recipients have been selected and whether a subject has been defined

Did not use rules of Quickform due to the fact that the first rule is a composite one

*/
/*

function showMessage(folder_id, p_message_id) {

            ajaxUrl[0] = 'forum/messages_index.php?ajax=messagesTable&limit=20&offset=0&sort=null&order=desc&folder='+folder_id+'&';

            eF_js_rebuildTable(0, 0, null, 'desc');

    //eF_js_rebuildTable(1, 0, 'null', 'desc');

}

*/
function eF_js_checkRecipients() {
    if (document.forms[0].recipients[0].checked && document.getElementById('autocomplete').value == "") {
        alert(norecipients);
        return false;
    } else {
        if (document.getElementById('msg_subject').value == "") {
            alert(thefield + " " + '"' + subject + '"' + " " + ismandatory);
            return false;
        }
    }
}
var additional_recipients_hidden = 1;
var additional_recipients_lock = 1;
function show_hide_additional_recipients() {
    var is_ie;
    var detect = navigator.userAgent.toLowerCase();
    detect.indexOf("msie") > 0 ? is_ie = "true" : is_ie = "false";
    if (additional_recipients_lock) {
        additional_recipients_lock = 0;
        if (additional_recipients_hidden) {
            additional_recipients_hidden = 0;
            new Effect.toggle($('additional_recipients_categories'), 'BLIND', {queue:{scope:'myscope', position:'end', limit: 2}, duration:1.0});
            $('autocomplete').value = "";
   //$('recipient').value = "";	//Added for the hidden input that stores logins
   $('arrow_down').writeAttribute({alt:hiderecipients, title:hiderecipients});
   setImageSrc($('arrow_down'), 16, 'navigate_up.png');
      if (!is_ie) {
    $('all_active_users').focus();
   }
        } else {
            additional_recipients_hidden = 1;
            new Effect.toggle( $('additional_recipients_categories'), 'BLIND', {queue:{scope:'myscope', position:'end', limit: 2}, duration:1.0});
            $('only_specific_users').checked = "true";
   $('arrow_down').writeAttribute({alt:showrecipients, title:showrecipients});
   setImageSrc($('arrow_down'), 16, 'navigate_down.png');
   eF_js_selectRecipients('only_specific_users');
   if (!is_ie) {
          $('all_active_users').focus();
         }
        }
    }
    setTimeout(function(){ additional_recipients_lock = 1;}, 1001);
}
function eF_js_selectRecipients(recipient) {
    if (enterprise) {
        $('lesson_recipients').disabled = 'disabled';
        $('user_type_recipients').disabled = 'disabled';
        $('branch_recipients').disabled = 'disabled';
        $('include_subbranches').selected = 'false';
        $('include_subbranches').style.visibility = 'hidden';
        $('include_subbranches_label').style.visibility = 'hidden';
        $('job_description_recipients').disabled = 'disabled';
        $('skill_recipients').disabled = 'disabled';
        $('group_recipients').disabled = 'disabled';
    }
    $('course_recipients').disabled = 'disabled';
    $('specific_course_completed_check').selected = 'false';
    $('specific_course_completed_check').style.visibility = 'hidden';
    $('specific_course_completed_label').style.visibility = 'hidden';
    $('group_recipients').disabled = 'disabled';
    $('lesson_recipients').disabled = 'disabled';
    $('user_type_recipients').disabled = 'disabled';
    $('lesson_professor_recipients').disabled = 'disabled';
    switch (recipient) {
        case 'specific_lesson':
            $('lesson_recipients').disabled = '';
            break;
        case 'specific_course':
            $('course_recipients').disabled = '';
            $('specific_course_completed_label').style.visibility = 'visible';
            $('specific_course_completed_check').style.visibility = 'visible';
            break;
        case 'specific_lesson_professor':
            $('lesson_professor_recipients').disabled = '';
            break;
        case 'specific_type':
            $('user_type_recipients').disabled = '';
            break;
        case 'specific_group':
            $('group_recipients').disabled = '';
            break;
         case 'specific_branch_job_description':
            // Both branch and job description will be enabled in case a combination is required
            $('branch_recipients').disabled = '';
            $('job_description_recipients').disabled = '';
            $('include_subbranches').style.visibility = 'visible';
            $('include_subbranches_label').style.visibility = 'visible';
            break;
         case 'specific_job_description':
            $('job_description_recipients').disabled = '';
            break;
         case 'specific_skill':
            $('skill_recipients').disabled = '';
            break;
    }
}
/*



if (this.name == "POPUP_FRAME") {

	$('titleBar').setStyle("display:none;");

	$('titleBar2').setStyle("display:block;");

	$('new_message_form').target = "_parent";

}

*/
function updateField(item) {
 var new_value = item.innerHTML;
 //var field 		  	= $('recipient');
 //var field_shown		= $('autocomplete');
 var field = $('autocomplete');
 var question_mark = '';
 if (field.value != "") {
  question_mark = field.value.substr(0,field.value.lastIndexOf(';') + 1);
 }
 var ending = item.innerHTML;
 if (question_mark == "") {
  field.value = ending;
 } else {
  field.value = question_mark + ending;
 }
//alert(field.value);
}
if ($('autocomplete_choices')) {
 new Ajax.Autocompleter("autocomplete",
         "autocomplete_choices",
         "ask.php?ask_type=users&messaging=1", {paramName: "preffix",
            updateElement : updateField,
            indicator : "busy"});
}
