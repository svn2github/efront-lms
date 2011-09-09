{* Smarty Template for GradeBook module (Student) *}

{capture name = 't_gradebook_one_lesson'}
<div style="float: right; margin-bottom: 10px;">
 <img src="{$T_GRADEBOOK_BASELINK|cat:'images/xls.png'}" alt="{$smarty.const._GRADEBOOK_EXPORT_EXCEL}" title="{$smarty.const._GRADEBOOK_EXPORT_EXCEL}" style="vertical-align:middle">
 <a href="{$T_GRADEBOOK_BASEURL}&export_student_excel=current">{$smarty.const._GRADEBOOK_EXPORT_EXCEL}</a>
</div>

<table class="sortedTable" style="width:100%">
 <tr>
{foreach name = 'columns_loop' key = "id" item = "column" from = $T_GRADEBOOK_LESSON_COLUMNS}
  <td class="topTitle centerAlign noSort">{$column.name} ({$smarty.const._GRADEBOOK_COLUMN_WEIGHT_DISPLAY}: {$column.weight})</td>
{/foreach}
  <td class="topTitle centerAlign noSort">{$smarty.const._GRADEBOOK_SCORE}</td>
  <td class="topTitle centerAlign noSort">{$smarty.const._GRADEBOOK_GRADE}</td>
 </tr>
 <tr class="oddRowColor">
{foreach from=$T_GRADEBOOK_STUDENT_GRADES item = i}
  <td class="centerAlign">{$i}</td>
{foreachelse}
  <td class="emptyCategory" colspan="100%">{$smarty.const._GRADEBOOK_NOT_PUBLISHED}</td>
{/foreach}
 </tr>
</table>
{/capture}

{foreach from=$T_GRADEBOOK_STUDENT_LESSON_NAMES item = lesson_name}
{capture name = $lesson_name}
<table class="sortedTable" style="width:100%">
 <tr>
{foreach name = 'columns_loop' key = "id" item = "column" from = $T_GRADEBOOK_STUDENT_LESSON_COLUMNS.$lesson_name}
  <td class="topTitle centerAlign noSort">{$column.name} ({$smarty.const._GRADEBOOK_COLUMN_WEIGHT_DISPLAY}: {$column.weight})</td>
{/foreach}
  <td class="topTitle centerAlign noSort">{$smarty.const._GRADEBOOK_SCORE}</td>
  <td class="topTitle centerAlign noSort">{$smarty.const._GRADEBOOK_GRADE}</td>
 </tr>
 <tr class="oddRowColor">
{foreach from=$T_GRADEBOOK_STUDENT_LESSON_GRADES.$lesson_name item = i}
  <td class="centerAlign">{$i}</td>
{foreachelse}
  <td class="emptyCategory" colspan="100%">{$smarty.const._GRADEBOOK_NOT_PUBLISHED}</td>
{/foreach}
 </tr>
</table>
{/capture}
{/foreach}

{capture name = 't_gradebook_student_tabs'}
<div class="tabber">
 <div class="tabbertab tabbertabdefault">
  <h3>{$T_GRADEBOOK_CURRENT_LESSON_NAME}</h3>
{eF_template_printBlock title=$T_GRADEBOOK_CURRENT_LESSON_NAME data=$smarty.capture.t_gradebook_one_lesson image=$T_GRADEBOOK_BASELINK|cat:'images/gradebook_logo.png' absoluteImagePath = 1}
 </div>
 <div class="tabbertab">
  <h3>{$smarty.const._GRADEBOOK_ALL_LESSONS}</h3>
  <div style="float: right; margin-top: 10px;">
   <img src="{$T_GRADEBOOK_BASELINK|cat:'images/xls.png'}" alt="{$smarty.const._GRADEBOOK_EXPORT_EXCEL}" title="{$smarty.const._GRADEBOOK_EXPORT_EXCEL}" style="vertical-align:middle">
   <a href="{$T_GRADEBOOK_BASEURL}&export_student_excel=all">{$smarty.const._GRADEBOOK_EXPORT_EXCEL}</a>
  </div><br/><br/>

{foreach from=$T_GRADEBOOK_STUDENT_LESSON_NAMES item = lesson_name}
{eF_template_printBlock title=$lesson_name data=$smarty.capture.$lesson_name image=$T_GRADEBOOK_BASELINK|cat:'images/gradebook_logo.png' absoluteImagePath=1}
{/foreach}
 </div>
</div>
{/capture}
{eF_template_printBlock title=$smarty.const._GRADEBOOK_NAME data=$smarty.capture.t_gradebook_student_tabs image=$T_GRADEBOOK_BASELINK|cat:'images/gradebook_logo.png' absoluteImagePath=1 help = 'Gradebook'}
