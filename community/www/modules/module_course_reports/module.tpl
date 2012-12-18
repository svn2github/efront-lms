{capture name = "t_course_reports_code"}
         <table class = "statisticsTools statisticsSelectList">
    <tr>
                 <td id = "right">
                        {$smarty.const._EXPORT}:&nbsp;
                        <a href = "{$T_MODULE_BASEURL}&export=course" target = "POPUP_FRAME">
                            <img src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}" />
                        </a>
                    </td></tr>
   </table>

<!--ajax:courseReportsTable-->

     <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "courseReportsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_BASEURL}&">
      <tr class = "topTitle">
       <td class = "topTitle" name = "name">{$smarty.const._COURSE}</td>
       <td class = "topTitle" name = "users_LOGIN">{$smarty.const._USER}</td>
       <td class = "topTitle centerAlign" name = "completed">{$smarty.const._COMPLETED}</td>
       <td class = "topTitle centerAlign" name = "score">{$smarty.const._SCORE}</td>
       <td class = "topTitle centerAlign" name = "to_timestamp">{$smarty.const._COMPLETIONDATE}</td>
       <td class = "topTitle centerAlign" name = "issued_certificate">{$smarty.const._CERTIFICATE}</td>
      </tr>
 {foreach name = 'data_list' key = 'key' item = 'item' from = $T_DATA_SOURCE}
      <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
       <td>{$item.name}</td>
       <td>#filter:login-{$item.users_LOGIN}#</td>
       <td class = "centerAlign">{if $item.completed}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
       <td class = "centerAlign">{if $item.completed}#filter:score-{$item.score}#%{else}-{/if}</td>
       <td class = "centerAlign">{if $item.to_timestamp}#filter:timestamp-{$item.to_timestamp}#{else}-{/if}</td>
       <td class = "centerAlign">{if $item.issued_certificate}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
     </tr>
 {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
 {/foreach}
    </table>

<!--/ajax:courseReportsTable-->

{/capture}

{capture name = "t_course_lesson_reports_code"}
         <table class = "statisticsTools statisticsSelectList">
    <tr>
                 <td id = "right">
                        {$smarty.const._EXPORT}:&nbsp;
                        <a href = "{$T_MODULE_BASEURL}&export=course_lesson" target = "POPUP_FRAME">
                            <img src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}" />
                        </a>
                    </td></tr>
   </table>

<!--ajax:courselessonReportsTable-->

     <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "courselessonReportsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_BASEURL}&">
      <tr class = "topTitle">
       <td class = "topTitle" name = "course_name">{$smarty.const._COURSE}</td>
       <td class = "topTitle" name = "name">{$smarty.const._LESSON}</td>
       <td class = "topTitle" name = "users_LOGIN">{$smarty.const._USER}</td>
       <td class = "topTitle centerAlign" name = "completed">{$smarty.const._COMPLETED}</td>
       <td class = "topTitle centerAlign" name = "score">{$smarty.const._SCORE}</td>
       <td class = "topTitle centerAlign" name = "to_timestamp">{$smarty.const._COMPLETIONDATE}</td>
      </tr>
 {foreach name = 'data_list' key = 'key' item = 'item' from = $T_DATA_SOURCE}
      <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
       <td>{$item.course_name}</td>
       <td>{$item.name}</td>
       <td>#filter:login-{$item.users_LOGIN}#</td>
       <td class = "centerAlign">{if $item.completed}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
       <td class = "centerAlign">{if $item.completed}#filter:score-{$item.score}#%{else}-{/if}</td>
       <td class = "centerAlign">{if $item.to_timestamp}#filter:timestamp-{$item.to_timestamp}#{else}-{/if}</td>
     </tr>
 {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
 {/foreach}
    </table>

<!--/ajax:courselessonReportsTable-->

{/capture}

{capture name = "t_lesson_reports_code"}
         <table class = "statisticsTools statisticsSelectList">
    <tr>
                 <td id = "right">
                        {$smarty.const._EXPORT}:&nbsp;
                        <a href = "{$T_MODULE_BASEURL}&export=lesson" target = "POPUP_FRAME">
                            <img src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}" />
                        </a>
                    </td></tr>
   </table>

<!--ajax:lessonReportsTable-->

     <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "lessonReportsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_BASEURL}&">
      <tr class = "topTitle">
       <td class = "topTitle" name = "name">{$smarty.const._LESSON}</td>
       <td class = "topTitle" name = "users_LOGIN">{$smarty.const._USER}</td>
       <td class = "topTitle centerAlign" name = "completed">{$smarty.const._COMPLETED}</td>
       <td class = "topTitle centerAlign" name = "score">{$smarty.const._SCORE}</td>
       <td class = "topTitle centerAlign" name = "to_timestamp">{$smarty.const._COMPLETIONDATE}</td>
      </tr>
 {foreach name = 'data_list' key = 'key' item = 'item' from = $T_DATA_SOURCE}
      <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
       <td>{$item.name}</td>
       <td>#filter:login-{$item.users_LOGIN}#</td>
       <td class = "centerAlign">{if $item.completed}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
       <td class = "centerAlign">{if $item.completed}#filter:score-{$item.score}#%{else}-{/if}</td>
       <td class = "centerAlign">{if $item.to_timestamp}#filter:timestamp-{$item.to_timestamp}#{else}-{/if}</td>
     </tr>
 {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
 {/foreach}
    </table>

<!--/ajax:lessonReportsTable-->

{/capture}


{capture name = "t_module_code"}
<div class = "tabber">
 {eF_template_printBlock tabber = "course_reports" title = $smarty.const._MODULE_COURSE_REPORTS_COURSEREPORTS data = $smarty.capture.t_course_reports_code}
 {eF_template_printBlock tabber = "course_lesson_reports" title = $smarty.const._MODULE_COURSE_REPORTS_COURSELESSONREPORTS data = $smarty.capture.t_course_lesson_reports_code}
 {eF_template_printBlock tabber = "lesson_reports" title = $smarty.const._MODULE_COURSE_REPORTS_LESSONREPORTS data = $smarty.capture.t_lesson_reports_code}
</div>
{/capture}

{eF_template_printBlock title = $smarty.const._MODULE_COURSE_REPORTS_COURSEREPORTS data = $smarty.capture.t_module_code}
