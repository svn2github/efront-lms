{*This is the form that contains the user personal data - to be shown in educational (T_SHOW_USER_FORM==1) and enterprise (T_ENTERPRISE) *}
{if $smarty.const.G_VERSIONTYPE == 'enterprise' || $T_SHOW_USER_FORM}


{capture name = 't_personal_form_data_code'}

 <table class = "statisticsTools statisticsSelectList">
  <tr>
{*
   <td >{$smarty.const._SORTBY}:</td>
   <td class = "filter">
    <select onchange = "reloadAjaxTab('{$T_TABBERAJAX.form}');setCookie('form_cookie', this.options[this.options.selectedIndex].value);">
     <option {if $smarty.cookies.form_cookie == ''}selected{/if} value = ""></option>
     <option {if $smarty.cookies.form_cookie == 'name'}selected{/if} value = "name">{$smarty.const._COURSENAME}</option>
     <option {if $smarty.cookies.form_cookie == 'completion_date'}selected{/if} value = "completion_date">{$smarty.const._COMPLETIONDATE}</option>
     <option {if $smarty.cookies.form_cookie == 'category'}selected{/if} value = "category">{$smarty.const._CATEGORY}</option>
     <option {if $smarty.cookies.form_cookie == 'score'}selected{/if} value = "score">{$smarty.const._SCORE}</option>
    </select>
   </td>
*}
   <td id = "right">{$smarty.const._TOOLS}:&nbsp;
                        <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$T_EDITEDUSER->user.login}&op=status&print=1&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._PRINTEMPLOYEEFORM}', 2)" target = "POPUP_FRAME">
                            <img src = "images/16x16/printer.png" title = "{$smarty.const._PRINTEMPLOYEEFORM}" alt = "{$smarty.const._PRINTEMPLOYEEFORM}" />
                        </a>
                        <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$T_EDITEDUSER->user.login}&pdf=1&op=status">
                            <img src = "images/file_types/pdf.png" title = "{$smarty.const._PDFFORMAT}" alt = "{$smarty.const._PDFFORMAT}" />
                        </a>
   </td></tr>

 </table>
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._GENERALUSERINFO}</legend>
  <table>
   <tr><td rowspan = "7" style = "padding-right:5px;width:1px;">
     <img src = "{if ($T_AVATAR)}view_file.php?file={$T_AVATAR}{else}{$smarty.const.G_SYSTEMAVATARSPATH}unknown_small.{$globalImageExtension}{/if}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" />
    </td></tr>
   <tr><td >{$smarty.const._NAME}:&nbsp;</td>
    <td >#filter:login-{$T_EDITEDUSER->user.login}#</td></tr>
   {if $T_EMPLOYEE.birthday}
   <tr><td >{$smarty.const._BIRTHDAY}:&nbsp;</td>
    <td >#filter:timestamp-{$T_EMPLOYEE.birthday}#</td></tr>
   {/if}
   {if $T_EMPLOYEE.address}
   <tr><td >{$smarty.const._ADDRESS}:&nbsp;</td>
    <td >{$T_EMPLOYEE.address}</td></tr>
   {/if}
   {if $T_EMPLOYEE.city}
   <tr><td >{$smarty.const._CITY}:&nbsp;</td>
    <td >{$T_EMPLOYEE.city}</td></tr>
   {/if}
   {if $T_EMPLOYEE.hired_on}
   <tr><td >{$smarty.const._HIREDON}:&nbsp;</td>
    <td >#filter:timestamp-{$T_EMPLOYEE.hired_on}#</td></tr>
   {/if}
   {if $T_EMPLOYEE.left_on}
   <tr><td >{$smarty.const._LEFTON}:&nbsp;</td>
    <td >#filter:timestamp-{$T_EMPLOYEE.left_on}#</td></tr>
   {/if}
  </table>
 </fieldset>
{if $T_USER_COURSES || $T_USER_LESSONS}
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._TRAINING}</legend>
  <div><a href = "javascript:void(0)" onclick = "ExpandCollapseFormRows()">{$smarty.const._EXPANDCOLLAPSE}</a></div>
   {if $T_USER_COURSES}
   <fieldset class = "fieldsetSeparator">
    <legend class = "smallLegend">{$smarty.const._COURSES}</legend>
    <table class = "sortedTable" noFooter = "true">
     <tr style = "font-weight:bold;white-space:nowrap">
      <td style = "padding:0px 3px 0px 0px" onclick = "resetFormRows()">{$smarty.const._NAME}</td>
      <td style = "padding:0px 3px 0px 3px">{$smarty.const._CATEGORY}</td>
      <td style = "padding:0px 3px 0px 3px">{$smarty.const._REGISTRATIONDATE}</td>
      <td style = "padding:0px 3px 0px 3px">{$smarty.const._COMPLETED}</td>
      <td style = "padding:0px 0px 0px 3px">{$smarty.const._SCORE}</td>
     </tr>
   {foreach name = 'courses_list' key = 'key' item = 'course' from = $T_USER_COURSES}
     <tr id = "form_tr_{$course.id}_previous">
      <td style = "padding:0px 3px 0px 0px">
      {if $T_COURSE_LESSONS[$course.id]}
       <img class = "ajaxHandle" src = "images/16x16/plus2.png" onclick = "showFormAdditionalDetails(this, '{$course.id}')" alt = "{$smarty.const._SHOWDETAILS}" title = "{$smarty.const._SHOWDETAILS}"/>
      {/if}
       <a class = "{if !$course.active}deactivatedElement{else}editLink{/if}" href = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$course.id}">{$course.name}</a></td>
      <td style = "padding:0px 3px 0px 3px">{$T_DIRECTIONS_TREE[$course.directions_ID]}</td>
      <td style = "padding:0px 3px 0px 3px">#filter:timestamp-{$course.active_in_course}#</td>
      <td style = "padding:0px 3px 0px 3px">{if $course.completed}#filter:timestamp-{$course.to_timestamp}#{else}-{/if}</td>
      <td style = "padding:0px 0px 0px 3px">{if $course.completed}#filter:score-{$course.score}#%{else}-{/if}</td>
     </tr>
    {if $T_COURSE_LESSONS[$course.id]}
     <tr id = "form_tr_{$course.id}" class = "form_additional_info" >
      <td colspan = "100%" style = "display:none">
      <fieldset class = "fieldsetSeparator">
       <legend class = "smallLegend">{$smarty.const._LESSONSFORCOURSE} {$course.name}</legend>
       <table style = "width:100%">
        <tr style = "font-weight:bold;white-space:nowrap">
         <td>{$smarty.const._LESSONNAME}</td>
         <td>{$smarty.const._CATEGORY}</td>
         <td>{$smarty.const._COMPLETED}</td>
         <td>{$smarty.const._SCORE}</td>
        </tr>
       {foreach name = 'course_lessons_list' item = 'lesson' from = $T_COURSE_LESSONS[$course.id]}
        <tr>
         <td><a class = "{if !$lesson.active}deactivatedElement{else}editLink{/if}" href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}">{$lesson.name}</a></td>
         <td>{$T_DIRECTIONS_TREE[$lesson.directions_ID]}</td>
         <td>{if $lesson.completed}#filter:timestamp-{$lesson.timestamp_completed}#{else}{$smarty.const._NO}{/if}</td>
         <td>{if $lesson.completed}#filter:score-{$lesson.score}#%{/if}</td>
        </tr>
        {if $T_USER_TESTS[$lesson.id]}
        <tr><td colspan = "100%">
         <fieldset class = "fieldsetSeparator">
          <legend class = "smallLegend">{$smarty.const._TESTSFORLESSON} {$lesson.name}</legend>
          <table style = "width:100%">
           <tr style = "font-weight:bold;white-space:nowrap">
            <td>{$smarty.const._TESTNAME}</td>
            <td >{$smarty.const._STATUS}</td>
            <td >{$smarty.const._SCORE}</td>
           </tr>
           {foreach name = 'tests_list' item = 'test' from = $T_USER_TESTS[$lesson.id]}
           <tr>
            <td>{$test.name}</td>
            <td>{$test.status}</td>
            <td>#filter:score-{$test.score}#%</td>
           </tr>
           {/foreach}
          </table>
         </fieldset>
         </td></tr>
        {/if}
       {/foreach}
       </table>
      </fieldset>
     </td></tr>
    {/if}
   {/foreach}
    </table>
   </fieldset>
   {/if}
   {if $T_USER_LESSONS}
   <fieldset class = "fieldsetSeparator">
    <legend class = "smallLegend">{$smarty.const._LESSONS}</legend>
    <table class = "sortedTable" noFooter = "true">
     <tr style = "font-weight:bold;white-space:nowrap">
      <td style = "padding:0px 3px 0px 0px">{$smarty.const._NAME}</td>
      <td style = "padding:0px 3px 0px 3px">{$smarty.const._CATEGORY}</td>
      <td style = "padding:0px 3px 0px 3px">{$smarty.const._REGISTRATIONDATE}</td>
      <td style = "padding:0px 3px 0px 3px">{$smarty.const._COMPLETED}</td>
      <td style = "padding:0px 0px 0px 3px">{$smarty.const._SCORE}</td>
     </tr>
     {foreach name = 'lessons_list' item = 'lesson' from = $T_USER_LESSONS}
     <tr>
      <td style = "padding:0px 3px 0px 0px"><a class = "{if !$lesson.active}deactivatedElement{else}editLink{/if}" href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}">{$lesson.name}</a></td>
      <td style = "padding:0px 3px 0px 3px">{$T_DIRECTIONS_TREE[$lesson.directions_ID]}</td>
      <td style = "padding:0px 3px 0px 3px">#filter:timestamp-{$lesson.active_in_lesson}#</td>
      <td style = "padding:0px 3px 0px 3px">{if $lesson.completed}#filter:timestamp-{$lesson.timestamp_completed}#{else}{$smarty.const._NO}{/if}</td>
      <td style = "padding:0px 0px 0px 3px">{if $lesson.completed}#filter:score-{$lesson.score}#%{/if}</td>
     </tr>
     {if $T_USER_TESTS[$lesson.id]}
     <tr><td colspan = "100%">
      <fieldset class = "fieldsetSeparator">
       <legend class = "smallLegend">{$smarty.const._TESTSFORLESSON} {$lesson.name}</legend>
       <table style = "width:100%">
        <tr style = "font-weight:bold;white-space:nowrap">
         <td>{$smarty.const._TESTNAME}</td>
         <td >{$smarty.const._STATUS}</td>
         <td >{$smarty.const._SCORE}</td>
        </tr>
        {foreach name = 'tests_list' item = 'test' from = $T_USER_TESTS[$lesson.id]}
        <tr>
         <td>{$test.name}</td>
         <td>{$test.status}</td>
         <td>#filter:score-{$test.score}#%</td>
        </tr>
        {/foreach}
       </table>
      </fieldset>
     </tr></td>
     {/if}
     {/foreach}
    </table>
   </fieldset>
   {/if}
   {if $T_AVERAGES}
   <fieldset class = "fieldsetSeparator">
    <legend class = "smallLegend">{$smarty.const._OVERALL}</legend>
    <table>
     {if $T_AVERAGES.courses}
     <tr>
      <td >{$smarty.const._COURSESAVERAGE}:&nbsp;<td>
      <td >#filter:score-{$T_AVERAGES.courses}#%</td>
     </tr>
     {/if}
     {if $T_AVERAGES.lessons}
     <tr>
      <td >{$smarty.const._LESSONSAVERAGE}:&nbsp;<td>
      <td >#filter:score-{$T_AVERAGES.lessons}#%</td>
     </tr>
     {/if}
    </table>
   </fieldset>
   {/if}
 </fieldset>
{/if}
{/capture}
{/if}
