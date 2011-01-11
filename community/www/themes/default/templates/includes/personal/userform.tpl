{*This is the form that contains the user personal data - to be shown in educational (T_SHOW_USER_FORM==1) and enterprise (T_ENTERPRISE) *}
{if $smarty.const.G_VERSIONTYPE == 'enterprise' || $T_SHOW_USER_FORM}


{capture name = 't_personal_form_data_code'}
{if $T_EDITEDUSER->user.login != $T_CURRENT_USER->user.login && !$smarty.get.printable}
 <table class = "statisticsTools statisticsSelectList">
  <tr>
   <td id = "right">{$smarty.const._TOOLS}:&nbsp;
                        <a href = "javascript:void(0)" onclick = 'win = window.open("{$smarty.server.PHP_SELF}?ctg=users&edit_user={$T_EDITEDUSER->user.login}&op=status&tab=user_form&popup=1&printable=1", "printable", "width=800,height=600,scrollbars=yes,resizable=yes,status=yes,toolbar=no,location=no,menubar=yes,top="+(parseInt(parseInt(screen.height)/2) - 300)+",left="+(parseInt(parseInt(screen.width)/2) - 400)+"");'>
                            <img src = "images/16x16/printer.png" title = "{$smarty.const._PRINTEMPLOYEEFORM}" alt = "{$smarty.const._PRINTEMPLOYEEFORM}" />
                        </a>
                        <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$T_EDITEDUSER->user.login}&pdf=1&op=status">
                            <img src = "images/file_types/pdf.png" title = "{$smarty.const._PDFFORMAT}" alt = "{$smarty.const._PDFFORMAT}" />
                        </a>
   </td></tr>

 </table>
{/if}
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
    <table class = "sortedTable" id = "formCoursesTable" noFooter = "true" style = "width:100%">
    {*<table class = "sortedTable" style = "width:100%" size = "{$T_TABLE_SIZE}" sortBy = "0" order = "desc" useAjax = "1" id = "formCoursesTable" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}&op=status&">*}
     <tr style = "font-weight:bold;white-space:nowrap">
      <td name = "name" style = "padding:0px 3px 0px 0px;width:40%" onclick = "resetFormRows(this)">{$smarty.const._NAME}</td>
      <td name = "directions_ID" style = "padding:0px 3px 0px 3px;width:25%" onclick = "resetFormRows(this)">{$smarty.const._CATEGORY}</td>
      <td name = "active_in_course" style = "padding:0px 3px 0px 3px;text-align:center;width:13%" onclick = "resetFormRows(this)">{$smarty.const._REGISTRATIONDATE}</td>
      <td name = "completed" style = "padding:0px 3px 0px 3px;text-align:center;width:13%" onclick = "resetFormRows(this)">{$smarty.const._COMPLETED}</td>
      <td name = "score" style = "padding:0px 0px 0px 3px;text-align:center;width:9%" onclick = "resetFormRows(this)">{$smarty.const._SCORE}</td>
     </tr>
   {foreach name = 'courses_list' key = 'key' item = 'course' from = $T_USER_COURSES}
     <tr id = "form_tr_{$course.id}_previous">
      <td style = "padding:0px 3px 0px 0px">
      {if $T_COURSE_LESSONS[$course.id]}
       <img class = "ajaxHandle" src = "images/16x16/plus2.png" onclick = "showFormAdditionalDetails(this, '{$course.id}')" alt = "{$smarty.const._SHOWDETAILS}" title = "{$smarty.const._SHOWDETAILS}"/>
      {/if}
      {if $_change_handles_ && !$T_IS_SUPERVISOR}
       <a class = "{if !$course.active}deactivatedElement{else}editLink{/if}" href = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$course.id}">{$course.name}</a></td>
      {else}
       {$course.name}
      {/if}
      <td style = "padding:0px 3px 0px 3px">{$T_DIRECTIONS_TREE[$course.directions_ID]}</td>
      <td style = "padding:0px 3px 0px 3px;text-align:center"><span style = "display:none">{$course.active_in_course}</span>#filter:timestamp-{$course.active_in_course}#</td>
      <td style = "padding:0px 3px 0px 3px;white-space:nowrap;text-align:center"><span style = "display:none">{$course.to_timestamp}</span>{if $course.completed}#filter:timestamp-{$course.to_timestamp}#{else}-{/if}</td>
      <td style = "padding:0px 0px 0px 3px;white-space:nowrap;text-align:center"><span style = "display:none">{$course.score}</span>{if $course.score}#filter:score-{$course.score}#%{else}-{/if}</td>
     </tr>
    {if $T_COURSE_LESSONS[$course.id]}
     <tr id = "form_tr_{$course.id}" class = "form_additional_info" >
      <td colspan = "100%" style = "display:none;">
      <fieldset class = "fieldsetSeparator">
       <legend class = "smallLegend">{$smarty.const._LESSONSFORCOURSE} {$course.name}</legend>
       <table style = "width:100%">
        <tr style = "font-weight:bold;white-space:nowrap">
         <td style = "width:78%">{$smarty.const._LESSONNAME}</td>
         <td style = "text-align:center;width:13%">{$smarty.const._COMPLETED}</td>
         <td style = "text-align:center;width:9%">{$smarty.const._SCORE}</td>
        </tr>
       {foreach name = 'course_lessons_list' item = 'lesson' from = $T_COURSE_LESSONS[$course.id]}
        <tr>
         <td>
         {if $_change_handles_ && !$T_IS_SUPERVISOR}
          <a class = "{if !$lesson.active}deactivatedElement{else}editLink{/if}" href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}">{$lesson.name}</a>
         {else}
          {$lesson.name}
         {/if}
         </td>
         <td style = "white-space:nowrap;text-align:center">{if $lesson.completed}#filter:timestamp-{$lesson.timestamp_completed}#{else}-{/if}</td>
         <td style = "white-space:nowrap;text-align:center">{if $lesson.completed}#filter:score-{$lesson.score}#%{/if}</td>
        </tr>
        {if $T_USER_TESTS[$lesson.id]}
        <tr><td colspan = "100%">
         <fieldset class = "fieldsetSeparator">
          <legend class = "smallLegend">{$smarty.const._TESTSFORLESSON} {$lesson.name}</legend>
          <table style = "width:100%">
           <tr style = "font-weight:bold;white-space:nowrap">
            <td style = "width:78%">{$smarty.const._TESTNAME}</td>
            <td style = "text-align:center;width:13%" >{$smarty.const._STATUS}</td>
            <td style = "text-align:center;width:9%" >{$smarty.const._SCORE}</td>
           </tr>
           {foreach name = 'tests_list' item = 'test' from = $T_USER_TESTS[$lesson.id]}
           <tr>
            <td>{$test.name}</td>
            <td style = "text-align:center;">{$test.status}</td>
            <td style = "text-align:center;">#filter:score-{$test.score}#%</td>
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
    <table id = "formLessonsTable" noFooter = "true" style = "width:100%">
     <tr style = "font-weight:bold;white-space:nowrap">
      <td name = "name" style = "padding:0px 3px 0px 0px;width:40%" onclick = "resetFormRows(this)">{$smarty.const._NAME}</td>
      <td name = "directions_ID" style = "padding:0px 3px 0px 3px;width:25%" onclick = "resetFormRows(this)">{$smarty.const._CATEGORY}</td>
      <td name = "active_in_course" style = "padding:0px 3px 0px 3px;width:13%;text-align:center;" onclick = "resetFormRows(this)">{$smarty.const._REGISTRATIONDATE}</td>
      <td name = "completed" style = "text-align:center;padding:0px 3px 0px 3px;width:13%" onclick = "resetFormRows(this)">{$smarty.const._COMPLETED}</td>
      <td name = "score" style = "text-align:center;padding:0px 0px 0px 3px;width:9%" onclick = "resetFormRows(this)" >{$smarty.const._SCORE}</td>
     </tr>
     {foreach name = 'lessons_list' item = 'lesson' from = $T_USER_LESSONS}
     <tr>
      <td style = "padding:0px 3px 0px 0px">
      {if $_change_handles_ && !$T_IS_SUPERVISOR}
       <a class = "{if !$lesson.active}deactivatedElement{else}editLink{/if}" href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}">{$lesson.name}</a>
      {else}
       {$lesson.name}
      {/if}
      </td>
      <td style = "padding:0px 3px 0px 3px">{$T_DIRECTIONS_TREE[$lesson.directions_ID]}</td>
      <td style = "padding:0px 3px 0px 3px;text-align:center;"><span style = "display:none">{$lesson.active_in_lesson}</span>#filter:timestamp-{$lesson.active_in_lesson}#</td>
      <td style = "text-align:center;padding:0px 3px 0px 3px"><span style = "display:none">{$lesson.timestamp_completed}</span>{if $lesson.completed}#filter:timestamp-{$lesson.timestamp_completed}#{else}-{/if}</td>
      <td style = "text-align:center;padding:0px 0px 0px 3px"><span style = "display:none">0{$lesson.score}</span>{if $lesson.score}#filter:score-{$lesson.score}#%{/if}</td>
     </tr>
     {if $T_USER_TESTS[$lesson.id]}
     <tr><td colspan = "100%">
      <fieldset class = "fieldsetSeparator">
       <legend class = "smallLegend">{$smarty.const._TESTSFORLESSON} {$lesson.name}</legend>
       <table style = "width:100%">
        <tr style = "font-weight:bold;white-space:nowrap">
         <td style = "width:78%">{$smarty.const._TESTNAME}</td>
         <td style = "text-align:center;width:13%" >{$smarty.const._STATUS}</td>
         <td style = "text-align:center;width:9%" >{$smarty.const._SCORE}</td>
        </tr>
        {foreach name = 'tests_list' item = 'test' from = $T_USER_TESTS[$lesson.id]}
        <tr>
         <td>{$test.name}</td>
         <td style = "text-align:center;">{$test.status}</td>
         <td style = "text-align:center;">#filter:score-{$test.score}#%</td>
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
