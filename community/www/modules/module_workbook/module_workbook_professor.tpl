{* Smarty Template for Workbook Module (Professor) *}

{if $T_WORKBOOK_MESSAGE && $T_WORKBOOK_MESSAGE_TYPE}
 <script>
  re = /\?/;
  !re.test(parent.location) ? parent.location = parent.location + '?message={$T_WORKBOOK_MESSAGE}&message_type={$T_WORKBOOK_MESSAGE_TYPE}' : parent.location = parent.location + '&message={$T_WORKBOOK_MESSAGE}&message_type={$T_WORKBOOK_MESSAGE_TYPE}';
 </script>
{/if}

{if isset($smarty.get.add_item) || isset($smarty.get.edit_item)}

{capture name = 't_add_edit_item_code'}
 {$T_WORKBOOK_ADD_EDIT_ITEM_FORM.javascript}
<form {$T_WORKBOOK_ADD_EDIT_ITEM_FORM.attributes}>
 {$T_WORKBOOK_ADD_EDIT_ITEM_FORM.hidden}
 <table class="formElements" style="width:100%">
  <tr>
   <td class="labelCell">{$T_WORKBOOK_ADD_EDIT_ITEM_FORM.item_title.label}:&nbsp;</td>
   <td class="elementCell">{$T_WORKBOOK_ADD_EDIT_ITEM_FORM.item_title.html}</td>
  </tr>
  <tr>
   <td></td>
   <td>
    <div class="headerTools">
     <span>
      <img src="images/16x16/navigate_down.png" alt="{$smarty.const._TOGGLEFILEMANAGER}" title="{$smarty.const._TOGGLEFILEMANAGER}" style="vertical-align: middle; border: 0px" id="arrow_down" />
      <a href="javascript:void(0)" onclick="toggleFileManager(this);">{$smarty.const._TOGGLEFILEMANAGER}</a>
     </span>
     <span>
      <img src="{$T_WORKBOOK_BASELINK|cat:'images/order.png'}" title="{$smarty.const._TOGGLEHTMLEDITORMODE}" alt="{$smarty.const._TOGGLEHTMLEDITORMODE}" style="vertical-align: middle; border: 0px" />
      <a href="javascript:toggleEditor('editor_content_data', 'mceEditor');" id="toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
     </span>
    </div>
   </td>
  </tr>
  <tr><td colspan="2" id="filemanager_cell"></td></tr>
  <tr>
   <td class="labelCell">{$T_WORKBOOK_ADD_EDIT_ITEM_FORM.item_text.label}:&nbsp;</td>
   <td class="elementCell">{$T_WORKBOOK_ADD_EDIT_ITEM_FORM.item_text.html}</td>
  </tr>
  <tr height="10px"></tr>
  <tr>
   <td class="labelCell">{$T_WORKBOOK_ADD_EDIT_ITEM_FORM.item_question.label}:&nbsp;</td>
   <td class="elementCell">{if $T_WORKBOOK_IS_PUBLISHED == 0}{$T_WORKBOOK_ADD_EDIT_ITEM_FORM.item_question.html}{else}{$T_WORKBOOK_EDIT_ITEM_DETAILS.question_title}{/if}</td>
  </tr>
  <tr height="10px"></tr>
  <tr id="question_preview_tr" {if isset($smarty.get.add_item) || $T_WORKBOOK_EDIT_ITEM_DETAILS.item_question == -1}style="display: none;"{/if}>
   <td></td>
   <td class="elementCell"><div id="question_preview">{if isset($smarty.get.edit_item) && $T_WORKBOOK_EDIT_ITEM_DETAILS.item_question != -1}{$T_WORKBOOK_EDIT_ITEM_DETAILS.question_text}{/if}</div></td>
  </tr>
  <tr height="10px"></tr>
  <tr id="check_answer_tr" {if isset($smarty.get.add_item) || $T_WORKBOOK_EDIT_ITEM_DETAILS.item_question == -1}style="display: none;"{/if}>
   <td class="labelCell">{$T_WORKBOOK_ADD_EDIT_ITEM_FORM.check_answer.label}:&nbsp;</td>
   <td class="elementCell" id="check_answer_td">{if $T_WORKBOOK_IS_PUBLISHED == 0}{$T_WORKBOOK_ADD_EDIT_ITEM_FORM.check_answer.html}{else}{$T_WORKBOOK_EDIT_ITEM_DETAILS.check_answer_text}{/if}</td>
  </tr>
  <tr>
   <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
   <td></td>
   <td class="elementCell">{$T_WORKBOOK_ADD_EDIT_ITEM_FORM.submit.html}</td>
  </tr>
 </table>
</form>
<div id="fmInitial"><div id="filemanager_div" style="display:none;">{$T_FILE_MANAGER}</div></div>
{/capture}

{capture name = 't_workbook_tab_code'}
<div class="tabber">
 <div class="tabbertab">
{if isset($smarty.get.add_item)}
  <h3>{$smarty.const._WORKBOOK_ADD_ITEM}</h3>
  {eF_template_printBlock title=$smarty.const._WORKBOOK_ADD_ITEM_FORM data=$smarty.capture.t_add_edit_item_code image=$T_WORKBOOK_BASELINK|cat:'images/add32x32.png' absoluteImagePath=1}
{else}
  <h3>{$smarty.const._WORKBOOK_EDIT_ITEM}</h3>
  {eF_template_printBlock title=$smarty.const._WORKBOOK_EDIT_ITEM_FORM data=$smarty.capture.t_add_edit_item_code image=$T_WORKBOOK_BASELINK|cat:'images/edit32x32.png' absoluteImagePath=1}
{/if}
 </div>
</div>
{/capture}
{eF_template_printBlock title=$T_WORKBOOK_LESSON_NAME data=$smarty.capture.t_workbook_tab_code image=$T_WORKBOOK_BASELINK|cat:'images/workbook_logo.png' absoluteImagePath=1}

{elseif isset($smarty.get.edit_settings)}

{capture name = 't_edit_settings_code'}
 {$T_WORKBOOK_EDIT_SETTINGS_FORM.javascript}
<form {$T_WORKBOOK_EDIT_SETTINGS_FORM.attributes}>
 {$T_WORKBOOK_EDIT_SETTINGS_FORM.hidden}
        <table class="formElements" style="margin-left:100px">
  <tr>
   <td class="labelCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.lesson_name.label}:&nbsp;</td>
   <td class="elementCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.lesson_name.html}</td>
  </tr>
  <tr>
   <td class="labelCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.allow_print.label}:&nbsp;</td>
   <td class="elementCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.allow_print.html}</td>
  </tr>
  <tr>
   <td class="labelCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.allow_export.label}:&nbsp;</td>
   <td class="elementCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.allow_export.html}</td>
  </tr>
  <tr>
   <td class="labelCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.edit_answers.label}:&nbsp;</td>
   <td class="elementCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.edit_answers.html}</td>
  </tr>
  <tr>
   <td class="labelCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.unit_to_complete.label}:&nbsp;</td>
   <td class="elementCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.unit_to_complete.html}</td>
  </tr>
  <tr>
   <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
   <td></td>
   <td class="elementCell">{$T_WORKBOOK_EDIT_SETTINGS_FORM.submit.html}</td>
  </tr>
 </table>
</form>
{/capture}
{eF_template_printBlock title=$smarty.const._WORKBOOK_NAME data=$smarty.capture.t_edit_settings_code image=$T_WORKBOOK_BASELINK|cat:'images/workbook_logo.png' absoluteImagePath = 1}

{elseif isset($smarty.get.reuse_item)}

{capture name = 't_reuse_item_code'}
 {$T_WORKBOOK_REUSE_ITEM_FORM.javascript}
<form {$T_WORKBOOK_REUSE_ITEM_FORM.attributes}>
 {$T_WORKBOOK_REUSE_ITEM_FORM.hidden}
        <table class="formElements" style="margin-left:100px">
  <tr>
   <td class="labelCell">{$T_WORKBOOK_REUSE_ITEM_FORM.item_id.label}:&nbsp;</td>
   <td class="elementCell">{$T_WORKBOOK_REUSE_ITEM_FORM.item_id.html}</td>
  </tr>
  <tr>
   <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
   <td></td>
   <td class="elementCell">{$T_WORKBOOK_REUSE_ITEM_FORM.submit.html}</td>
  </tr>
 </table>
</form>
{/capture}
{eF_template_printBlock title=$smarty.const._WORKBOOK_NAME data=$smarty.capture.t_reuse_item_code image=$T_WORKBOOK_BASELINK|cat:'images/workbook_logo.png' absoluteImagePath = 1}

{elseif isset($smarty.get.move_item)}

{capture name = 't_move_item_code'}
{if $T_WORKBOOK_ITEMS_COUNT == 1}
<div style="text-align: center;">{$smarty.const._WORKBOOK_CANNOT_MOVE_ITEM}</div>
{else}
 {$T_WORKBOOK_MOVE_ITEM_FORM.javascript}
<form {$T_WORKBOOK_MOVE_ITEM_FORM.attributes}>
 {$T_WORKBOOK_MOVE_ITEM_FORM.hidden}
        <table class="formElements" style="margin-left:100px">
  <tr>
   <td class="labelCell">{$T_WORKBOOK_MOVE_ITEM_FORM.item_position.label}:&nbsp;</td>
   <td class="elementCell">{$T_WORKBOOK_MOVE_ITEM_FORM.item_position.html}</td>
  </tr>
  <tr>
   <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
   <td></td>
   <td class="elementCell">{$T_WORKBOOK_MOVE_ITEM_FORM.submit.html}</td>
  </tr>
 </table>
</form>
{/if}
{/capture}
{eF_template_printBlock title=$smarty.const._WORKBOOK_NAME data=$smarty.capture.t_move_item_code image=$T_WORKBOOK_BASELINK|cat:'images/workbook_logo.png' absoluteImagePath = 1}

{elseif isset($smarty.get.popup_info)}

{capture name = 't_popup_info'}

<table class="contentArea">
 <tr>
  <td>{$smarty.const._WORKBOOK_HOW_TO_OPEN_POPUP1}</td>
 </tr>
 <tr>
  <td>&nbsp;</td>
 </tr>
 <tr>
  <td>{$smarty.const._WORKBOOK_HOW_TO_OPEN_POPUP2}</td>
 </tr>
 <tr>
  <td>
   <pre>&lt;a href="javascript:void(0);" onclick="openPopup('student.php?ctg=module&amp;op=module_workbook&amp;popup=1&amp;workbook_popup=1');"&gt;&lt;img src="{$T_WORKBOOK_BASELINK|cat:'images/popup.png'}" alt="{$smarty.const._WORKBOOK_OPEN_POPUP}" title="{$smarty.const._WORKBOOK_OPEN_POPUP}" style="border:0px;" /&gt;&lt;/a&gt;</pre>
  </td>
 </tr>
 <tr>
  <td>{$smarty.const._WORKBOOK_HOW_TO_OPEN_POPUP4}</td>
 </tr>
 <tr>
  <td>
   <pre>&lt;a href="javascript:void(0);" onclick="openPopup('professor.php?ctg=module&amp;op=module_workbook&amp;popup=1&amp;workbook_popup=1');"&gt;&lt;img src="{$T_WORKBOOK_BASELINK|cat:'images/popup.png'}" alt="{$smarty.const._WORKBOOK_OPEN_POPUP}" title="{$smarty.const._WORKBOOK_OPEN_POPUP}" style="border:0px;" /&gt;&lt;/a&gt;</pre>
  </td>
 </tr>
 <tr>
  <td>{$smarty.const._WORKBOOK_HOW_TO_OPEN_POPUP3}</td>
 </tr>
 <tr>
  <td>
   <div id="copy_js">{$smarty.const._WORKBOOK_COPY_TO_CLIPBOARD}</div>
   <pre id="js_code">
&#60;script&#62;
 function openPopup(url)&#123;

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
 &#125;
&#60;/script&#62;
   </pre>
  </td>
 </tr>
</table>

<script src="{$T_WORKBOOK_BASELINK}scripts/ZeroClipboard.js"></script>
<script>
{literal}
 ZeroClipboard.setMoviePath('{/literal}{$T_WORKBOOK_BASELINK}{literal}scripts/ZeroClipboard.swf');
 var clip = new ZeroClipboard.Client();
 clip.setText('');

 clip.addEventListener('mouseDown', function(){
  var pre = document.getElementById('js_code');
  var html = pre.innerHTML;
  html = html.replace(/&lt;/g, '<');
  html = html.replace(/&gt;/g, '>');
  clip.setText(html);
 });

 clip.glue('copy_js');
{/literal}
</script>

{/capture}
{eF_template_printBlock title=$smarty.const._WORKBOOK_NAME data=$smarty.capture.t_popup_info image=$T_WORKBOOK_BASELINK|cat:'images/workbook_logo.png' absoluteImagePath = 1}

{elseif isset($smarty.get.check_workbook_progress)}

{capture name = 't_check_workbook_progress_info'}

<table class="sortedTable" style="width:100%">
 <tr>
  <td class="topTitle">{$smarty.const._WORKBOOK_STUDENT_NAME}</td>
  <td class="topTitle">{$smarty.const._PROGRESS}</td>
  <td class="topTitle centerAlign noSort">{$smarty.const._PREVIEW}</td>
 </tr>
{foreach name = "students_loop" key = "login" item = "student" from = $T_WORKBOOK_STUDENTS}
 <tr id="row_{$student.login}" class="{cycle values = "oddRowColor, evenRowColor"}">
  <td>#filter:login-{$student.login}#</td>
  <td>{$student.progress}</td>
  <td class="centerAlign"><a href="{$T_WORKBOOK_BASEURL}&preview_workbook=1&student={$student.login}&popup=1" onclick="eF_js_showDivPopup('{$smarty.const._PREVIEW}', 3)" target="POPUP_FRAME"><img src="{$T_WORKBOOK_BASELINK|cat:'images/info.png'}" alt="{$smarty.const._PREVIEW}" title="{$smarty.const._PREVIEW}" style="vertical-align:middle" /></a></td>
 </tr>
{foreachelse}
 <tr class="defaultRowHeight oddRowColor">
  <td class="emptyCategory" colspan="100%">{$smarty.const._WORKBOOK_NO_STUDENTS_FOUND}</td>
 </tr>
{/foreach}
</table>

{/capture}
{eF_template_printBlock title=$T_WORKBOOK_LESSON_NAME data=$smarty.capture.t_check_workbook_progress_info image=$T_WORKBOOK_BASELINK|cat:'images/workbook_logo.png' absoluteImagePath = 1}

{elseif isset($smarty.get.preview_workbook)}

{capture name = 't_preview_workbook_code'}

{if $T_WORKBOOK_NON_OPTIONAL_QUESTIONS_NR != 0}
<table style="width:100%">
 <tr>
  <td>
   <div id="progress_bar" class="workbook_bar">
    {$smarty.const._COMPLETED}:&nbsp;
    <span class="progressNumber" id="progressNumberWorkbook">{$T_WORKBOOK_PREVIEW_STUDENT_PROGRESS}%</span>
    <span class="progressBar" id="progressBarWorkbook" style="width:{$T_WORKBOOK_PREVIEW_STUDENT_PROGRESS}px;">&nbsp;</span>&nbsp;
   </div>
  </td>
 </tr>
</table>
{/if}

<div class="separator"></div>

{foreach name = 'items_loop' key = "id" item = "item" from = $T_WORKBOOK_ITEMS}
{assign var='html_solved' value=$T_WORKBOOK_PREVIEW_ANSWERS.$id}
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
{eF_template_printBlock title=$smarty.const._WORKBOOK_NAME data=$smarty.capture.t_preview_workbook_code image=$T_WORKBOOK_BASELINK|cat:'images/workbook_logo.png' absoluteImagePath = 1}

{else}
{capture name = 't_workbook_professor_code'}
<table>
 <tr>
  <td>
{if $T_WORKBOOK_IS_PUBLISHED == 0}
   <img src="{$T_WORKBOOK_BASELINK|cat:'images/add.png'}" alt="{$smarty.const._WORKBOOK_ADD_ITEM}" title="{$smarty.const._WORKBOOK_ADD_ITEM}" style="vertical-align:middle" />&nbsp;<a href="{$T_WORKBOOK_BASEURL}&add_item=1">{$smarty.const._WORKBOOK_ADD_ITEM}</a>
   &nbsp;<div class="options_separator"></div>&nbsp;
   <img src="{$T_WORKBOOK_BASELINK|cat:'images/reuse.png'}" alt="{$smarty.const._WORKBOOK_REUSE_ITEM}" title="{$smarty.const._WORKBOOK_REUSE_ITEM}" style="vertical-align:middle" />&nbsp;<a href="{$T_WORKBOOK_BASEURL}&reuse_item=1&popup=1" onclick="eF_js_showDivPopup('{$smarty.const._WORKBOOK_REUSE_ITEM}', 0)" target="POPUP_FRAME">{$smarty.const._WORKBOOK_REUSE_ITEM}</a>
{/if}
{if sizeof($T_WORKBOOK_LESSONS) != 2}
   {if $T_WORKBOOK_IS_PUBLISHED == 0}&nbsp;<div class="options_separator"></div>&nbsp;{/if}
   <img src="{$T_WORKBOOK_BASELINK|cat:'images/arrow_right.png'}" alt="{$smarty.const._WORKBOOK_SWITCH_TO}" title="{$smarty.const._WORKBOOK_SWITCH_TO}" style="vertical-align:middle" />
   <select id="switch_lesson" name="switch_lesson" onchange="switchLesson(this)">
{foreach name = 'switch_lessons_loop' key = "id" item = "lesson" from = $T_WORKBOOK_LESSONS}
    <option value="{$lesson.id}">{$lesson.name}</option>
{/foreach}
   </select>
{/if}
{if $T_WORKBOOK_ITEMS|@count != 0}
{if $T_WORKBOOK_IS_PUBLISHED == 0}
   &nbsp;<div class="options_separator"></div>&nbsp;
   <img src="{$T_WORKBOOK_BASELINK|cat:'images/success.png'}" alt="{$smarty.const._PUBLISH}" title="{$smarty.const._PUBLISH}" style="vertical-align:middle" />&nbsp;<a href="{$T_WORKBOOK_BASEURL}&publish_workbook=1" >{$smarty.const._PUBLISH}</a>
{else}
   {if sizeof($T_WORKBOOK_LESSONS) != 2}&nbsp;<div class="options_separator"></div>&nbsp;{/if}
   <img src="{$T_WORKBOOK_BASELINK|cat:'images/forbidden.png'}" alt="{$smarty.const._RESET}" title="{$smarty.const._RESET}" style="vertical-align:middle" />&nbsp;<a href="{$T_WORKBOOK_BASEURL}&reset_workbook_professor=1" onclick="var msg = getResetMessage(); return confirm(msg);">{$smarty.const._RESET}</a>
{/if}
   &nbsp;<div class="options_separator"></div>&nbsp;
   <img src="{$T_WORKBOOK_BASELINK|cat:'images/analysis.png'}" alt="{$smarty.const._WORKBOOK_CHECK_PROGRESS}" title="{$smarty.const._WORKBOOK_CHECK_PROGRESS}" style="vertical-align:middle" />&nbsp;<a href="{$T_WORKBOOK_BASEURL}&check_workbook_progress=1">{$smarty.const._WORKBOOK_CHECK_PROGRESS}</a>
{/if}
{if $T_POPUP_MODE == false}
   &nbsp;<div class="options_separator"></div>&nbsp;
   <img src="{$T_WORKBOOK_BASELINK|cat:'images/popup.png'}" alt="{$smarty.const._WORKBOOK_OPEN_POPUP}" title="{$smarty.const._WORKBOOK_OPEN_POPUP}" style="vertical-align:middle" />&nbsp;<a href="javascript:void(0);" onclick="openPopup('{$T_WORKBOOK_BASEURL}&popup=1&workbook_popup=1');">{$smarty.const._WORKBOOK_OPEN_POPUP}</a>
{/if}
  </td>
 </tr>
</table>

<div class="separator"></div>

{foreach name = 'items_loop' key = "id" item = "item" from = $T_WORKBOOK_ITEMS}
<div class="workbook_item">
 <div class="item_header">
  <div class="item_actions">
   <b>{$smarty.const._WORKBOOK_ITEMS_COUNT}{$item.position}</b>&nbsp;&nbsp;
{if $T_WORKBOOK_IS_PUBLISHED == 0}
   <img src="{$T_WORKBOOK_BASELINK|cat:'images/delete.png'}" alt="{$smarty.const._REMOVE}" title="{$smarty.const._REMOVE}" style="vertical-align:middle" />&nbsp;<a href="{$T_WORKBOOK_BASEURL}&delete_item={$item.id}" onclick="return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}');">{$smarty.const._REMOVE}</a>
   &nbsp;<div class="actions_separator"></div>&nbsp;
{/if}
   <img src="{$T_WORKBOOK_BASELINK|cat:'images/move.png'}" alt="{$smarty.const._MOVE}" title="{$smarty.const._MOVE}" style="vertical-align:middle" />&nbsp;<a href="{$T_WORKBOOK_BASEURL}&move_item={$item.id}&popup=1" onclick="eF_js_showDivPopup('{$smarty.const._WORKBOOK_MOVE_ITEM}', 0)" target="POPUP_FRAME">{$smarty.const._MOVE}</a>
   &nbsp;<div class="actions_separator"></div>&nbsp;
   <img src="{$T_WORKBOOK_BASELINK|cat:'images/edit.png'}" alt="{$smarty.const._EDIT}" title="{$smarty.const._EDIT}" style="vertical-align:middle" />&nbsp;<a href="{$T_WORKBOOK_BASEURL}&edit_item={$item.id}">{$smarty.const._EDIT}</a>
  </div>
  <div class="unique_id">{$smarty.const._WORKBOOK_ITEM_ID2}{$item.unique_ID}</div>
 </div>
 <div class="separator"></div>
{if $item.item_title != ''}
 <div class="item_title">{$item.item_title}</div>
 <div class="separator" style="height: 5px;"></div>
{/if}
{if $item.item_text != ''}
 <div class="item_text">{$item.item_text}</div>
 <div class="separator" style="height: 5px;"></div>
{/if}
{if $item.item_question != -1}
 <div class="item_question">{$item.question_text}</div>
 <div class="separator" style="height: 5px;"></div>
 <div class="item_check_answer">{$smarty.const._WORKBOOK_ITEM_GRADE_ANSWER}: {if $item.check_answer == 1}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</div>
{/if}
</div>
<div class="items_separator"></div>
{foreachelse}
<img src="{$T_WORKBOOK_BASELINK|cat:'images/warning.png'}" alt="{$smarty.const._WORKBOOK_NO_ITEMS_FOUND}" title="{$smarty.const._WORKBOOK_NO_ITEMS_FOUND}" style="vertical-align:middle" />&nbsp;<div style="display: inline; font-style: italic;">{$smarty.const._WORKBOOK_NO_ITEMS_FOUND}</div>
{/foreach}

{/capture}
{eF_template_printBlock title=$T_WORKBOOK_LESSON_NAME data=$smarty.capture.t_workbook_professor_code image=$T_WORKBOOK_BASELINK|cat:'images/workbook_logo.png' absoluteImagePath = 1 options=$T_WORKBOOK_OPTIONS}
{/if}

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

 function questionPreview(el){

  var id = Element.extend(el).options[el.options.selectedIndex].value;
  var url = '{/literal}{$T_WORKBOOK_BASEURL}{literal}&question_preview=1&question_id=' + id;

  if(id != '-1'){

   new Ajax.Request(url, {
    method: 'post',
    asynchronous: true,
    onFailure: function(transport){
     alert(decodeURIComponent(transport.responseText));
    },
    onSuccess: function(transport){
     var question_preview_tr = document.getElementById('question_preview_tr');
     var check_answer_tr = document.getElementById('check_answer_tr');
     var question_preview = document.getElementById('question_preview');
     question_preview_tr.style.display = 'table-row';
     check_answer_tr.style.display = 'table-row';
     question_preview.innerHTML = transport.responseText;
    }
   });
  }
  else{
   var question_preview_tr = document.getElementById('question_preview_tr');
   var check_answer_tr = document.getElementById('check_answer_tr');
   question_preview_tr.style.display = 'none';
   check_answer_tr.style.display = 'none';
  }
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

 function getResetMessage(){

  var url = '{/literal}{$T_WORKBOOK_BASEURL}{literal}&get_reset_message=1';
  var message = '';

  new Ajax.Request(url, {
   method: 'get',
   asynchronous: false,
   onFailure: function(transport){
    alert(decodeURIComponent(transport.responseText));
   },
   onSuccess: function(transport){
    message = decodeURIComponent(transport.responseText);
   }
  });

  return message;
 }

{/literal}
</script>
