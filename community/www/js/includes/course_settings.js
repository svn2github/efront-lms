function issueCertificateAll(el) {
 Element.extend(el).insert(new Element('img', {src:'themes/default/images/others/progress1.gif'}).addClassName('handle'));

 parameters = {CertificateAll:1, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onIssueCertificateAll);
}
function onIssueCertificateAll(el, response) {
 location.reload();
}
function setAutoComplete(el) {
 Element.extend(el).insert(new Element('img', {src:'themes/default/images/others/progress1.gif'}).addClassName('handle'));

 parameters = {auto_complete:1, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onSetAutoComplete);
}
function onSetAutoComplete(el, response) {
 var status = response.evalJSON(true).response;

 if (status == 0) {
  el.update(autocompleteno);
  $('auto_certificates').down().next().update(autocertificateno);
  $('auto_certificates').hide();
 } else {
  el.update(autocompleteyes);
  $('auto_certificates').show();
 }
}
function setAutoCertificate(el) {
 Element.extend(el).insert(new Element('img', {src:'themes/default/images/others/progress1.gif'}).addClassName('handle'));

 parameters = {auto_certificate:1, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onSetAutoCertificate);
}
function onSetAutoCertificate(el, response) {
 if (response == 0) {
  el.update(autocertificateno);
 } else {
  el.update(autocertificateyes);
 }
}

function setAllUsersStatusCompleted(el) {
 Element.extend(el).insert(new Element('img', {src:'themes/default/images/others/progress1.gif'}).addClassName('handle'));

 parameters = {set_all_completed:1, method: 'get'};
 ajaxRequest(el, location.toString(), parameters, onSetAllUsersStatusCompleted);
}
function onSetAllUsersStatusCompleted(el, response) {
 if (response.evalJSON(true).status) {
  el.down().remove();
  eF_js_redrawPage('courseUsersTable', true);
 }
}


function showEdit(id) {
 $('add_schedule_link_'+id).hide();
 $('schedule_dates_'+id).hide();
 $('remove_schedule_link_'+id).show();
 $('schedule_dates_form_'+id).show();
 $('set_schedules_link_'+id).show();
}
function hideEdit(id) {
 $('add_schedule_link_'+id).show();
 $('schedule_dates_'+id).show();
 $('remove_schedule_link_'+id).hide();
 $('schedule_dates_form_'+id).hide();
 $('set_schedules_link_'+id).hide();
}
function setSchedule(el, id) {
 parameters = {method: 'get', set_schedule: id};
 var url = location.toString();
 $('schedule_dates_form_'+id).select('select').each(function (s) {url+='&'+s.name+'='+s.options[s.selectedIndex].value;});

 ajaxRequest(el, url, parameters, onSetSchedule);
}
function onSetSchedule(el, response) {
 id = el.id.match(/set_schedules_link_(\d+)/)[1];
 $('schedule_dates_'+id).update(response);
 hideEdit(id);
 setImageSrc($('add_schedule_link_'+id).down(), 16, 'edit');
 $('add_schedule_link_'+id).down().next().show();
}
function deleteSchedule(el, id) {
 parameters = {method: 'get', delete_schedule: id};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onDeleteSchedule);
}
function onDeleteSchedule(el, response) {
 id = el.up().id.match(/add_schedule_link_(\d+)/)[1];
 $('schedule_dates_'+id).update('<span class = "emptyCategory">'+noscheduleset+'</span>');
 el.hide();
 setImageSrc(el.previous(), 16, 'add');
}

function saveQuestionTree(el) {
 parameters = {ajax:1, order:treeObj.getNodeOrders(), method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters);
}

Array.prototype.inArray = function (value)
{
 var i;
 for (i = 0; i < this.length; i++) {
  if (this[i] === value) {
   return true;
  }
 }
 return false;
};

function eF_js_removeCourseRule(id) {
 var insertCell = document.getElementById('insert_node_' + id);
 var numConditions = Math.round(insertCell.parentNode.getElementsByTagName('select').length / 2);

 if (numConditions > 0) { //This means there are more than 1 conditions set
  child = document.getElementById('lessonCell['+id+']['+numConditions+']');
  child.parentNode.removeChild(child);

  if (numConditions % 5 == 0) { //This is for wrapping fields (since IE won't automatically wrap them)
   insertCell.removeChild(insertCell.lastChild);
  }
 }
 if (numConditions == 1) {
  document.getElementById('delete_icon_' + id).style.display = 'none';
  document.getElementById('label_' + id).innerHTML = generallyavailable; //Set the correct label
 }
}

function eF_js_addCourseRule(id, selectedLesson, selectedCondition) {

 if (!selectedLesson) {
  selectedLesson = 0;
 }
 if (!selectedCondition) {
  selectedCondition = 0;
 }
 var insertCell = document.getElementById('insert_node_' + id);
 var numConditions = Math.round(insertCell.parentNode.getElementsByTagName('select').length / 2 + 1);

 selectedValues = new Array();
 for (var i = 1; i < numConditions; i++) { //Calculate selected options, to remove them from the new selects
  previous_select = document.getElementById('rules['+id+'][lesson]['+(i)+']');
  selectedValues.push(previous_select.options[previous_select.options.selectedIndex].value);
 }

 if (selectedValues.length == lessonsIds.length - 1) { //This means no more options are left. so return without doing anything
  return false;
 }

 document.getElementById('label_' + id).innerHTML = dependson;'&nbsp;:&nbsp;'; //Set the correct label

 var lessonsSpan = document.createElement('span');
 lessonsSpan.id = 'lessonCell['+id+']['+numConditions+']';
 insertCell.appendChild(lessonsSpan);
 if (numConditions % 5 == 0) { //This is for wrapping fields (since IE won't automatically wrap them)
  insertCell.appendChild(document.createElement('br'));
 }
 if (numConditions > 1) { //This means there are other conditions set
  var conditionsSelect = document.getElementById('conditions').cloneNode(true);
  conditionsSelect.id = 'rules['+id+'][condition]['+numConditions+']';
  conditionsSelect.name = conditionsSelect.id;
  conditionsSelect.selectedIndex = selectedCondition;
  conditionsSelect.style.display = '';
  lessonsSpan.appendChild(conditionsSelect);
 }
 //var lessonsSelect  = document.getElementById('lessons_list').cloneNode(true);    //This is the right way to do it, but IE won't cloneNode correctly (sic) so we need to build the select list from scratch
 lessonsSelect = document.createElement('select');
 lessonsSelect.style.marginLeft = '5px';
 lessonsSelect.style.verticalAlign = 'middle';
 lessonsSelect.id = 'rules['+id+'][lesson]['+numConditions+']';
 lessonsSelect.name = lessonsSelect.id;

 for (var i = 0; i < lessonsIds.length; i++) {
  if (!selectedValues.inArray(lessonsIds[i])) {
   option = document.createElement('option');
   option.value = lessonsIds[i];
   option.innerHTML = lessonsNames[i];
   lessonsSelect.appendChild(option);
  }
 }

 for (i = 0; i < lessonsSelect.options.length; i++) { //Remove selected lesson from list
  if (lessonsSelect.options[i].value == selectedLesson) {
   lessonsSelect.options[i].selected = true;
  }
 }
 //In separate loop, because setting to null seems to reindex select options (in IE)
 for (i = 0; i < lessonsSelect.options.length; i++) { //Remove selected lesson from list
  if (lessonsSelect.options[i].value == id) {
   lessonsSelect.options[i] = null;
  }
 }
 lessonsSelect.style.display = '';
 lessonsSpan.appendChild(lessonsSelect);

 document.getElementById('delete_icon_' + id).style.display = '';

}

if ($('resetRow')) {
 if (document.edit_course_certificate_form.months.selectedIndex != 0 || document.edit_course_certificate_form.days.selectedIndex != 0) {
  $('resetRow').show();
 } else {
  $('resetRow').hide();
 }
}
function displayReset() {
 if (document.edit_course_certificate_form.months.selectedIndex != 0 || document.edit_course_certificate_form.days.selectedIndex != 0) {
  $('resetRow').show();
 } else {
  $('resetRow').hide();
 }
}

function setCertificateOperationsHref(defaultHref){

 var templateID = document.edit_course_certificate_form.existing_certificate.value;
 var tmp = templateID.split('-');
 templateID = tmp[0];

 document.getElementById("edit_certificate_template_href").setAttribute('href', defaultHref + templateID);
 document.getElementById("rename_certificate_template_href").setAttribute('href', defaultHref + templateID);
 document.getElementById("clone_certificate_template_href").setAttribute('href', defaultHref + templateID);
 document.getElementById("delete_certificate_template_href").setAttribute('href', defaultHref + templateID);
}

function showHideMainTemplatesOperations(){

 var templateType = document.edit_course_certificate_form.existing_certificate.value;
 var tmp = templateType.split('-');
 templateType = tmp[1];

 if(templateType == 'main'){

  document.getElementById('edit_certificate_template_href').style.display = 'none';
  document.getElementById('edit_certificate_template_separator').style.display = 'none';
  document.getElementById('rename_certificate_template_href').style.display = 'none';
  document.getElementById('rename_certificate_template_separator').style.display = 'none';
  document.getElementById('delete_certificate_template_href').style.display = 'none';
  document.getElementById('clone_certificate_template_separator').style.display = 'none';
 }
 else if(templateType == 'course'){

  document.getElementById('edit_certificate_template_href').style.display = 'inline';
  document.getElementById('edit_certificate_template_separator').style.display = 'inline';
  document.getElementById('rename_certificate_template_href').style.display = 'inline';
  document.getElementById('rename_certificate_template_separator').style.display = 'inline';
  document.getElementById('delete_certificate_template_href').style.display = 'inline';
  document.getElementById('clone_certificate_template_separator').style.display = 'inline';
 }
}

function setInterCourseRules(el) {
 if ($('autocomplete').value) {
  if ($('autocomplete_course_hidden').value) {
   parameters = {inter_course_rule:$('autocomplete_course_hidden').value, method: 'get'};
  } else {
   alert(translations['_COURSEDOESNOTEXIST']);
  }
 } else {
  parameters = {inter_course_rule:'', method: 'get'};
 }
 var url = location.toString();
 ajaxRequest(el, url, parameters);
}

//Initialize display for existing course rules
if (typeof(calls) != 'undefined') {
 calls.each(function (s) {eF_js_addCourseRule(s[0], s[1], s[2]);});
}
if ($('autocomplete_courses')) {
 new Ajax.Autocompleter("autocomplete",
         "autocomplete_courses",
         "ask.php?ask_type=courses", {paramName: "preffix",
              afterUpdateElement : function (t, li) {$('autocomplete_course_hidden').value = li.id;},
              indicator : "busy"});
}
