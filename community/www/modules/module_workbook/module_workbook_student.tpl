{* Smarty Template for Workbook Module (Student) *}

{if isset($smarty.get.print)}

{capture name = 't_print_code'}
<table class="contentArea">
 <tr>
  <td id="centerColumn">
   <p style="text-align:center">
    <input class="flatButton" type="submit" onClick="window.print();" value="{$smarty.const._PRINTIT}"/>
   </p>
  </td>
 </tr>
</table>

{if $T_WORKBOOK_NON_OPTIONAL_QUESTIONS_NR != 0}
<table style="width:100%">
 <tr>
  <td>
   <div id="progress_bar" class="workbook_bar">
    {$smarty.const._COMPLETED}:&nbsp;
    <span class="progressNumber" id="progressNumberWorkbook">{$T_WORKBOOK_STUDENT_PROGRESS}%</span>
    <span class="progressBar" id="progressBarWorkbook" style="width:{$T_WORKBOOK_STUDENT_PROGRESS}px;">&nbsp;</span>&nbsp;
   </div>
  </td>
 </tr>
</table>
{/if}

<div class="separator"></div>

{foreach name = 'items_loop' key = "id" item = "item" from = $T_WORKBOOK_ITEMS}
{assign var='html_solved' value=$T_WORKBOOK_ANSWERS.$id}
<div class="workbook_item">
 <div class="item_header_student">
  <img src="{$T_WORKBOOK_BASELINK|cat:'images/item_logo.png'}" alt="{$item.item_title}" title="{$item.item_title}" style="vertical-align:middle; border: 0px;" />&nbsp;
  {$smarty.const._WORKBOOK_ITEMS_COUNT}{$item.position}{if $item.item_title != ''}&nbsp;-&nbsp;{$item.item_title}{/if}
 </div>
 <div class="separator" style="height: 5px;"></div>
{if $item.item_text != ''}
 <div class="item_text_student">{$item.item_text}</div>
 <div class="separator" style="height: 5px;"></div>
{/if}
{if $item.item_question != -1}
{if $html_solved == ''}
 <div class="item_question_student" id="item_{$id}">{$item.question_text}</div>
{else}
 <div class="item_question_student" id="item_{$id}">{$html_solved}</div>
{/if}
{/if}
</div>
<div class="items_separator"></div>
{foreachelse}
<img src="{$T_WORKBOOK_BASELINK|cat:'images/warning.png'}" alt="{$smarty.const._WORKBOOK_NO_ITEMS_FOUND}" title="{$smarty.const._WORKBOOK_NO_ITEMS_FOUND}" style="vertical-align:middle" />&nbsp;<div style="display: inline; font-style: italic;">{$smarty.const._WORKBOOK_NO_ITEMS_FOUND}</div>
{/foreach}

{/capture}
{eF_template_printBlock title=$smarty.const._WORKBOOK_NAME data=$smarty.capture.t_print_code image=$T_WORKBOOK_BASELINK|cat:'images/workbook_logo.png' absoluteImagePath = 1}

{else}

{capture name = 't_workbook_student_code'}
<table style="width:100%">
 <tr>
  <td style="width:25%">
{if $T_WORKBOOK_NON_OPTIONAL_QUESTIONS_NR != 0}
   <div id="progress_bar" class="workbook_bar">
    {$smarty.const._COMPLETED}:&nbsp;
    <span class="progressNumber" id="progressNumberWorkbook">{$T_WORKBOOK_STUDENT_PROGRESS}%</span>
    <span class="progressBar" id="progressBarWorkbook" style="width:{$T_WORKBOOK_STUDENT_PROGRESS}px;">&nbsp;</span>&nbsp;
   </div>
   <div class="separator" style="height: 5px;"></div>
   <div id="reset_workbook" {if $T_WORKBOOK_IS_COMPLETED.is_completed == 0}style="display: none;"{/if}>
    <img src="{$T_WORKBOOK_BASELINK|cat:'images/reset.png'}" alt="{$smarty.const._RESET} {$smarty.const._WORKBOOK_NAME}" title="{$smarty.const._RESET} {$smarty.const._WORKBOOK_NAME}" style="vertical-align:middle" />&nbsp;<a href="{$T_WORKBOOK_BASEURL}&reset_workbook_student={$T_WORKBOOK_IS_COMPLETED.id}" onclick="return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}');" id="reset_workbook_link">{$smarty.const._RESET} {$smarty.const._WORKBOOK_NAME}</a>
   </div>
{/if}
  </td>

  <td style="width:75%">
   <div id="student_options_right">
{if sizeof($T_WORKBOOK_LESSONS) != 2}
    <img src="{$T_WORKBOOK_BASELINK|cat:'images/arrow_right.png'}" alt="{$smarty.const._WORKBOOK_SWITCH_TO}" title="{$smarty.const._WORKBOOK_SWITCH_TO}" style="vertical-align:middle" />
    <select id="switch_lesson" name="switch_lesson" onchange="switchLesson(this)">
{foreach name = 'switch_lessons_loop' key = "id" item = "lesson" from = $T_WORKBOOK_LESSONS}
     <option value="{$lesson.id}">{$lesson.name}</option>
{/foreach}
    </select>
    &nbsp;<div class="options_separator"></div>&nbsp;
{/if}

{if $T_WORKBOOK_IS_PUBLISHED == 1}
{if $T_WORKBOOK_SETTINGS.allow_export == '1'}
    <img src="{$T_WORKBOOK_BASELINK|cat:'images/download.png'}" alt="{$smarty.const._WORKBOOK_DOWNLOAD_AS}" title="{$smarty.const._WORKBOOK_DOWNLOAD_AS}" style="vertical-align:middle" />&nbsp;
    <select id="download_as" name="download_as" onchange="downloadWorkbook(this)">
     <option value="-1">{$smarty.const._WORKBOOK_DOWNLOAD_AS}</option>
     <option value="-2">------------------</option>
     <option value="pdf">{$smarty.const._PDF}</option>
     <option value="doc">{$smarty.const._DOC}</option>
    </select>
    &nbsp;<div class="options_separator"></div>&nbsp;
{/if}

{if $T_WORKBOOK_SETTINGS.allow_print == '1'}
    <img src="{$T_WORKBOOK_BASELINK|cat:'images/printer.png'}" alt="{$smarty.const._PRINT}" title="{$smarty.const._PRINT}" style="vertical-align:middle" />&nbsp;<a href="{$T_WORKBOOK_BASEURL}&print=1&popup=1" onclick="eF_js_showDivPopup('{$smarty.const._PRINT} {$smarty.const._WORKBOOK_NAME}', 3)" target="POPUP_FRAME">{$smarty.const._PRINT}</a>
{/if}
{/if}

{if $T_POPUP_MODE == false}
   {if $T_WORKBOOK_SETTINGS.allow_print == '1' && $T_WORKBOOK_IS_PUBLISHED == 1}&nbsp;<div class="options_separator"></div>&nbsp;{/if}
    <img src="{$T_WORKBOOK_BASELINK|cat:'images/popup.png'}" alt="{$smarty.const._WORKBOOK_OPEN_POPUP}" title="{$smarty.const._WORKBOOK_OPEN_POPUP}" style="vertical-align:middle" />&nbsp;<a href="javascript:void(0);" onclick="openPopup('{$T_WORKBOOK_BASEURL}&popup=1&workbook_popup=1');">{$smarty.const._WORKBOOK_OPEN_POPUP}</a>
{/if}
   </div>
  </td>
 </tr>
</table>

<div class="separator"></div>

{if $T_WORKBOOK_IS_PUBLISHED == 1}
{foreach name = 'items_loop' key = "id" item = "item" from = $T_WORKBOOK_ITEMS}
{assign var='html_solved' value=$T_WORKBOOK_ANSWERS.$id}
{assign var='autosave_text' value=$T_WORKBOOK_AUTOSAVE_ANSWERS.$id}
<div class="workbook_item">
 <div class="item_header_student">
  <img src="{$T_WORKBOOK_BASELINK|cat:'images/item_logo.png'}" alt="{$item.item_title}" title="{$item.item_title}" style="vertical-align:middle; border: 0px;" />&nbsp;
  {$smarty.const._WORKBOOK_ITEMS_COUNT}{$item.position}{if $item.item_title != ''}&nbsp;-&nbsp;{$item.item_title}{/if}
{if $item.question_type == 'raw_text' && $T_WORKBOOK_SETTINGS.edit_answers == '1'}
  <a href="javascript:void(0);" id="edit_answer_{$id}" onclick="editAnswer(document.getElementById('item_{$id}'), {$id});" {if $html_solved == ''}style="display: none;"{/if}><img src="{$T_WORKBOOK_BASELINK|cat:'images/edit.png'}" alt="{$smarty.const._EDIT}" title="{$smarty.const._EDIT}" style="vertical-align:middle; border: 0px; margin-left: 10px;" /></a>
{/if}
 </div>
 <div class="separator" style="height: 5px;"></div>
{if $item.item_text != ''}
 <div class="item_text_student">{$item.item_text}</div>
 <div class="separator" style="height: 5px;"></div>
{/if}
{if $item.item_question != -1}
{if $html_solved == ''}
 <div class="item_question_student" id="item_{$id}">
  <form action="javascript:submitForm(document.getElementById('answer_item_form_{$id}'), {$id}, '{$item.question_type}');" name="answer_item_form_{$id}" id="answer_item_form_{$id}">
   {if $autosave_text == ''}{$item.question_text}{else}{$autosave_text}{/if}
   <div class="separator" style="height: 5px;"></div>
   <input class="flatButton" name="submit" value="{$smarty.const._WORKBOOK_ITEM_CHECK_ANSWER}" type="button" onclick="javascript:submitForm(document.getElementById('answer_item_form_{$id}'), {$id}, '{$item.question_type}');"/>
   <div class="wrong_answer" id="wrong_empty_answer_{$id}"></div>
  </form>
 </div>
{else}
 <div class="item_question_student" id="item_{$id}">{$html_solved}</div>
{/if}
{/if}
</div>
<div class="items_separator"></div>
{foreachelse}
<img src="{$T_WORKBOOK_BASELINK|cat:'images/warning.png'}" alt="{$smarty.const._WORKBOOK_NO_ITEMS_FOUND}" title="{$smarty.const._WORKBOOK_NO_ITEMS_FOUND}" style="vertical-align:middle" />&nbsp;<div style="display: inline; font-style: italic;">{$smarty.const._WORKBOOK_NO_ITEMS_FOUND}</div>
{/foreach}
{else}
<img src="{$T_WORKBOOK_BASELINK|cat:'images/warning.png'}" alt="{$smarty.const._WORKBOOK_UNDER_DEVELOPMENT}" title="{$smarty.const._WORKBOOK_UNDER_DEVELOPMENT}" style="vertical-align:middle" />&nbsp;<div style="display: inline; font-style: italic;">{$smarty.const._WORKBOOK_UNDER_DEVELOPMENT}</div>
{/if}

{/capture}
{eF_template_printBlock title=$T_WORKBOOK_LESSON_NAME data=$smarty.capture.t_workbook_student_code image=$T_WORKBOOK_BASELINK|cat:'images/workbook_logo.png' absoluteImagePath = 1}
{/if}

<script>
{literal}

 setInterval('autoSave()', 1000);

{/literal}
</script>

<script>
{literal}

 function openPopup(url){

  var width = 900;
  var height = 700;
  var left = (screen.width - width) / 2;
  var top = (screen.height - height) / 2;

  var params = 'width=' + width + ', height=' + height;
  params += ', top=' + top + ', left=' + left;
  params += ', directories=no';
  params += ', location=no';
  params += ', menubar=no';
  params += ', resizable=yes';
  params += ', scrollbars=yes';
  params += ', status=yes';
  params += ', toolbar=no';

  popupWindow = window.open(url, 'workbook_popup', params);

  if(window.focus)
   popupWindow.focus()

  return false;
 }

 function switchLesson(el){

  var lesson_id = Element.extend(el).options[el.options.selectedIndex].value;
  var url = '{/literal}{$T_WORKBOOK_BASEURL}{literal}&switch_lesson=' + lesson_id;

  {/literal}{if $T_POPUP_MODE == true}{assign var='popup_mode' value='1'}{else}{assign var='popup_mode' value='0'}{/if}{literal}
  var popup_mode = {/literal}{$popup_mode}{literal};

  if(popup_mode == '1')
   url += '&popup=1';

  if(lesson_id != '-1' && lesson_id != '-2')
   location = url;
 }

 function downloadWorkbook(el){

  var download_as = Element.extend(el).options[el.options.selectedIndex].value;
  var url = '{/literal}{$T_WORKBOOK_BASEURL}{literal}&download_as=' + download_as;

  {/literal}{if $T_POPUP_MODE == true}{assign var='popup_mode' value='1'}{else}{assign var='popup_mode' value='0'}{/if}{literal}
  var popup_mode = {/literal}{$popup_mode}{literal};

  if(popup_mode == '1')
   url += '&popup=1';

  if(download_as != '-1' && download_as != '-2')
   location = url;
 }

 function submitForm(obj, item_id, question_type){

  var url = '{/literal}{$T_WORKBOOK_BASEURL}{literal}&item_submitted=' + item_id;
  var parameters = "&";
  var checkbox_flag = false;
  var checkbox_checked = false;

  for(i = 0; i < obj.getElementsByTagName("input").length; i++){

   if(obj.getElementsByTagName("input")[i].type == "checkbox"){

    checkbox_flag = true;

    if(obj.getElementsByTagName("input")[i].checked){
     parameters += obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value + "&";
     checkbox_checked = true;
    }
    else{
     parameters += obj.getElementsByTagName("input")[i].name + "=&";
    }
   }

   if(obj.getElementsByTagName("input")[i].type == "radio"){

    if(obj.getElementsByTagName("input")[i].checked)
     parameters += obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value + "&";
   }

   if(obj.getElementsByTagName("input")[i].type == "text"){

    if(obj.getElementsByTagName("input")[i].value != '')
     parameters += obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value + "&";
   }
  }

  for(i = 0; i < obj.getElementsByTagName("textarea").length; i++){

   if(obj.getElementsByTagName("textarea")[i].value != '')
    parameters += obj.getElementsByTagName("textarea")[i].name + "=" + obj.getElementsByTagName("textarea")[i].value + "&";
  }

  for(i = 0; i < obj.getElementsByTagName("select").length; i++){

   parameters += obj.getElementsByTagName("select")[i].name + "=" + obj.getElementsByTagName("select")[i].value + "&";
  }

  if(parameters == '&' || (checkbox_flag == true && checkbox_checked == false)){
   var empty_answer = document.getElementById('wrong_empty_answer_' + item_id);
   empty_answer.style.display = 'inline-block';
   empty_answer.innerHTML = '{/literal}{$smarty.const._WORKBOOK_QUESTION_NOT_ANSWERED}{literal}';
   window.setTimeout('Effect.Fade("wrong_empty_answer_' + item_id + '")', 2000);
  }
  else
   makeRequest(url, parameters, item_id, question_type);
 }

 function makeRequest(url, parameters, item_id, question_type){

  var editAnswers = '{/literal}{$T_WORKBOOK_SETTINGS.edit_answers}{literal}';
  http_request = false;

  if(window.XMLHttpRequest){ // Mozilla, Safari, ...

   http_request = new XMLHttpRequest();

   if(http_request.overrideMimeType){
    http_request.overrideMimeType('text/html');
   }
  }
  else if(window.ActiveXObject){ // IE

   try{
    http_request = new ActiveXObject("Msxml2.XMLHTTP");
   }
   catch(e){
    try{
     http_request = new ActiveXObject("Microsoft.XMLHTTP");
    }
    catch(e){}
   }
  }

  if(!http_request){
   alert('Cannot create XMLHTTP instance');
   return false;
  }

  http_request.onreadystatechange = function(){

   if(http_request.readyState == 4){

    if(http_request.status == 200){

     result = http_request.responseText;

     if(result == '-1'){
      var wrong_answer = document.getElementById('wrong_empty_answer_' + item_id);
      wrong_answer.style.display = 'inline-block';
      wrong_answer.innerHTML = '{/literal}{$smarty.const._WORKBOOK_INVALID_ANSWER}{literal}';
      window.setTimeout('Effect.Fade("wrong_empty_answer_' + item_id + '")', 2000);
     }
     else{
      document.getElementById('item_' + item_id).innerHTML = result;

      var url_ = '{/literal}{$T_WORKBOOK_BASEURL}{literal}&get_progress=1';

      new Ajax.Request(url_, {
       method: 'post',
       asynchronous: true,
       onFailure: function(transport){
        alert(decodeURIComponent(transport.responseText));
       },
       onSuccess: function(transport){
        var progress_number_workbook = document.getElementById('progressNumberWorkbook');
        var progress_bar_workbook = document.getElementById('progressBarWorkbook');
        var reset_workbook = document.getElementById('reset_workbook');
        var reset_workbook_link = document.getElementById('reset_workbook_link');
        var tmp = transport.responseText.split('-');
        progress_number_workbook.innerHTML = tmp[0] + '%';
        progress_bar_workbook.style.width = tmp[0] + 'px';

        if(tmp[0] == 100){
         reset_workbook.style.display = 'block';
         reset_workbook_link.href = '{/literal}{$T_WORKBOOK_BASEURL}{literal}&reset_workbook_student=' + tmp[1];
        }
       }
      });

      if(question_type == 'raw_text' && editAnswers == '1'){
       var edit_answer = document.getElementById('edit_answer_' + item_id);
       edit_answer.style.display = 'inline';
      }
     }
    }
    else{
     alert('There was a problem with the request.');
    }
   }
  }

  http_request.open('GET', url + parameters, true);
  http_request.send(null);
 }

 function autoSave(){

  var forms = document.getElementsByTagName("form");

  for(var i = 0; i < forms.length; i++){

   var form_action = forms[i].action;

   if(form_action.search('submitForm') != -1 || form_action.search('submitEditForm') != -1){

    var form_id = forms[i].id;
    var form_obj = document.getElementById(form_id);
    var tmp = form_id.split('_');
    var item_id = tmp[3];

    submitFormAutoSave(form_obj, item_id);
   }
  }
 }

 function submitFormAutoSave(obj, item_id){

  var url = '{/literal}{$T_WORKBOOK_BASEURL}{literal}&item_submitted_autosave=' + item_id;
  var parameters = "&";
  var checkbox_flag = false;
  var checkbox_checked = false;

  for(i = 0; i < obj.getElementsByTagName("input").length; i++){

   if(obj.getElementsByTagName("input")[i].type == "checkbox"){

    checkbox_flag = true;

    if(obj.getElementsByTagName("input")[i].checked){
     parameters += obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value + "&";
     checkbox_checked = true;
    }
    else{
     parameters += obj.getElementsByTagName("input")[i].name + "=&";
    }
   }

   if(obj.getElementsByTagName("input")[i].type == "radio"){

    if(obj.getElementsByTagName("input")[i].checked){
     parameters += obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value + "&";
    }
   }

   if(obj.getElementsByTagName("input")[i].type == "text"){

    if(obj.getElementsByTagName("input")[i].value != '')
     parameters += obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value + "&";
   }
  }

  for(i = 0; i < obj.getElementsByTagName("textarea").length; i++){

   if(obj.getElementsByTagName("textarea")[i].value != '')
    parameters += obj.getElementsByTagName("textarea")[i].name + "=" + obj.getElementsByTagName("textarea")[i].value + "&";
  }

  for(i = 0; i < obj.getElementsByTagName("select").length; i++){

   parameters += obj.getElementsByTagName("select")[i].name + "=" + obj.getElementsByTagName("select")[i].value + "&";
  }

  if(!(parameters == '&' || (checkbox_flag == true && checkbox_checked == false))){

   makeRequestAutoSave(url, parameters, item_id);
  }
 }

 function makeRequestAutoSave(url, parameters, item_id){

  http_request = false;

  if(window.XMLHttpRequest){ // Mozilla, Safari, ...

   http_request = new XMLHttpRequest();

   if(http_request.overrideMimeType){
    http_request.overrideMimeType('text/html');
   }
  }
  else if(window.ActiveXObject){ // IE

   try{
    http_request = new ActiveXObject("Msxml2.XMLHTTP");
   }
   catch(e){
    try{
     http_request = new ActiveXObject("Microsoft.XMLHTTP");
    }
    catch(e){}
   }
  }

  if(!http_request){
   alert('Cannot create XMLHTTP instance');
   return false;
  }

  http_request.open('GET', url + parameters, true);
  http_request.send(null);
 }

 function editAnswer(obj, item_id){

  var url = '{/literal}{$T_WORKBOOK_BASEURL}{literal}&item_to_update=' + item_id;
  var parameters = "&";

  var edit_answer = document.getElementById('edit_answer_' + item_id);
  edit_answer.style.display = 'none';

  for(i = 0; i < obj.getElementsByTagName("input").length; i++){

   if(obj.getElementsByTagName("input")[i].type == "hidden"){

    if(obj.getElementsByTagName("input")[i].value != '')
     parameters += obj.getElementsByTagName("input")[i].name + "=" + obj.getElementsByTagName("input")[i].value + "&";
   }
  }

  makeRequestEditAnswer(url, parameters, item_id);
 }

 function makeRequestEditAnswer(url, parameters, item_id){

  http_request = false;

  if(window.XMLHttpRequest){ // Mozilla, Safari, ...

   http_request = new XMLHttpRequest();

   if(http_request.overrideMimeType){
    http_request.overrideMimeType('text/html');
   }
  }
  else if(window.ActiveXObject){ // IE

   try{
    http_request = new ActiveXObject("Msxml2.XMLHTTP");
   }
   catch(e){
    try{
     http_request = new ActiveXObject("Microsoft.XMLHTTP");
    }
    catch(e){}
   }
  }

  if(!http_request){
   alert('Cannot create XMLHTTP instance');
   return false;
  }

  http_request.onreadystatechange = function(){

   if(http_request.readyState == 4){

    if(http_request.status == 200){

     var result = http_request.responseText;
     var editForm = '<form action="javascript:submitEditForm(document.getElementById(\'edit_answer_form_' + item_id + '\'), ' + item_id + ');" name="edit_answer_form_' + item_id + '" id="edit_answer_form_' + item_id + '">';
     var editForm2 = '<input class="flatButton" name="submit" value="{/literal}{$smarty.const._WORKBOOK_ITEM_CHECK_ANSWER}{literal}" type="button" onclick="javascript:submitEditForm(document.getElementById(\'edit_answer_form_' + item_id + '\'), ' + item_id + ');"/>';

     var all = editForm + result + '<div class="separator" style="height: 5px;"></div>' + editForm2 +
       '<div class="wrong_answer" id="wrong_empty_answer_' + item_id + '"></div>' + '</form>';

     document.getElementById('item_' + item_id).innerHTML = all;
    }
   }
  }

  http_request.open('GET', url + parameters, true);
  http_request.send(null);
 }

 function submitEditForm(obj, item_id){

  var url = '{/literal}{$T_WORKBOOK_BASEURL}{literal}&item_updated=' + item_id;
  var parameters = "&";

  for(i = 0; i < obj.getElementsByTagName("textarea").length; i++){

   if(obj.getElementsByTagName("textarea")[i].value != '')
    parameters += obj.getElementsByTagName("textarea")[i].name + "=" + obj.getElementsByTagName("textarea")[i].value + "&";
  }

  if(parameters == '&'){
   var empty_answer = document.getElementById('wrong_empty_answer_' + item_id);
   empty_answer.style.display = 'inline-block';
   empty_answer.innerHTML = '{/literal}{$smarty.const._WORKBOOK_QUESTION_NOT_ANSWERED}{literal}';
   window.setTimeout('Effect.Fade("wrong_empty_answer_' + item_id + '")', 2000);
  }
  else
   makeRequestSubmitEditedAnswer(url, parameters, item_id);
 }

 function makeRequestSubmitEditedAnswer(url, parameters, item_id){

  http_request = false;

  if(window.XMLHttpRequest){ // Mozilla, Safari, ...

   http_request = new XMLHttpRequest();

   if(http_request.overrideMimeType){
    http_request.overrideMimeType('text/html');
   }
  }
  else if(window.ActiveXObject){ // IE

   try{
    http_request = new ActiveXObject("Msxml2.XMLHTTP");
   }
   catch(e){
    try{
     http_request = new ActiveXObject("Microsoft.XMLHTTP");
    }
    catch(e){}
   }
  }

  if(!http_request){
   alert('Cannot create XMLHTTP instance');
   return false;
  }

  http_request.onreadystatechange = function(){

   if(http_request.readyState == 4){

    if(http_request.status == 200){

     result = http_request.responseText;
     document.getElementById('item_' + item_id).innerHTML = result;

     var edit_answer = document.getElementById('edit_answer_' + item_id);
     edit_answer.style.display = 'inline';
    }
    else{
     alert('There was a problem with the request.');
    }
   }
  }

  http_request.open('GET', url + parameters, true);
  http_request.send(null);
 }

{/literal}
</script>
