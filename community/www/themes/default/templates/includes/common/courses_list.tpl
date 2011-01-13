{* INSTRUCTIONS FOR THIS FILE
 Define $T_DATASOURCE_COLUMNS to specify which columns to show
 Define $T_DATASOURCE_OPERATIONS to specify which handles to show inside the 'operations' column
 Define $T_DATASOURCE_SORT_BY to specify which column to sort by
 Define $T_DATASOURCE_SORT_ORDER to specify the default ordering of the above sort column
 Example:
 $sortedColumns = array('name', 'location', 'num_students', 'num_lessons', 'num_skills', 'start_date', 'end_date', 'price', 'created', 'active', 'operations');
 $smarty -> assign("T_DATASOURCE_SORT_BY", array_search('active', $sortedColumns));
 $smarty -> assign("T_DATASOURCE_SORT_ORDER", 'desc');
 $smarty -> assign("T_DATASOURCE_COLUMNS", $sortedColumns);
 $smarty -> assign("T_DATASOURCE_OPERATIONS", array('progress', 'delete'));
*}
{capture name = 'courses_list'}
<style>
{literal}
table#coursesTable,table#instancesTable {width:100%;}
table#coursesTable td.name,table#instancesTable td.name{width:30%;}
table#coursesTable td.location,table#instancesTable td.location{width:15%;}
table#coursesTable td.directions_name,table#instancesTable td.directions_name{width:15%;}
table#coursesTable td.directions_ID,table#instancesTable td.directions_ID{width:15%;}
table#coursesTable td.user_type, table#instancesTable td.user_type{width:15%;}
table#coursesTable td.num_students,table#instancesTable td.num_students{width:5%;text-align:center;}
table#coursesTable td.num_lessons,table#instancesTable td.num_lessons{width:5%;text-align:center;}
table#coursesTable td.num_skills,table#instancesTable td.num_skills{width:5%;text-align:center;}
table#coursesTable td.price,table#instancesTable td.price{width:10%;text-align:center;}
table#coursesTable td.start_date,table#instancesTable td.start_date{width:10%;text-align:center;}
table#coursesTable td.end_date,table#instancesTable td.end_date{width:10%;text-align:center;}
table#coursesTable td.created,table#instancesTable td.created{width:10%;text-align:center;}
table#coursesTable td.active,table#instancesTable td.active{width:5%;text-align:center;}
table#coursesTable td.active_in_course,table#instancesTable td.active_in_course{width:10%;text-align:center;}
table#coursesTable td.completed,table#instancesTable td.completed{width:5%;text-align:center;}
table#coursesTable td.to_timestamp,table#instancesTable td.to_timestamp{width:10%;text-align:center;}
table#coursesTable td.score,table#instancesTable td.score{width:5%;text-align:center;}
table#coursesTable td.operations,table#instancesTable td.operations{width:5%;text-align:center;white-space:nowrap}
table#coursesTable td.has_course,table#instancesTable td.has_course{width:10%;text-align:center;}
{/literal}
</style>

     <tr class = "topTitle">
{if in_array('name', $T_DATASOURCE_COLUMNS)} <td class = "topTitle name" name = "name">{$smarty.const._NAME}</td>{/if}



{if in_array('directions_name', $T_DATASOURCE_COLUMNS)} <td class = "topTitle directions_name" name = "directions_name">{$smarty.const._PARENTDIRECTIONS}</td>{/if}
{if in_array('directions_ID', $T_DATASOURCE_COLUMNS)} <td class = "topTitle directions_ID" name = "directions_ID">{$smarty.const._PARENTDIRECTIONS}</td>{/if}
{if in_array('user_type', $T_DATASOURCE_COLUMNS)} <td class = "topTitle user_type" name = "user_type">{$smarty.const._USERTYPE}</td>{/if}
{if in_array('num_students', $T_DATASOURCE_COLUMNS)} <td class = "topTitle num_students" name = "num_students">{$smarty.const._PARTICIPATION}</td>{/if}
{if in_array('num_lessons', $T_DATASOURCE_COLUMNS)} <td class = "topTitle num_lessons" name = "num_lessons">{$smarty.const._LESSONS}</td>{/if}



{if in_array('price', $T_DATASOURCE_COLUMNS) && $T_CONFIGURATION.disable_payments != 1} <td class = "topTitle price" name = "price">{$smarty.const._PRICE}</td>{/if}
{if in_array('start_date', $T_DATASOURCE_COLUMNS)} <td class = "topTitle start_date" name = "start_date">{$smarty.const._STARTDATE}</td>{/if}
{if in_array('end_date', $T_DATASOURCE_COLUMNS)} <td class = "topTitle end_date" name = "end_date">{$smarty.const._ENDDATE}</td>{/if}
{if in_array('created', $T_DATASOURCE_COLUMNS)} <td class = "topTitle created" name = "created">{$smarty.const._CREATED}</td>{/if}
{if in_array('active', $T_DATASOURCE_COLUMNS)} <td class = "topTitle active" name = "active" >{$smarty.const._ACTIVE2}</td>{/if}
{if in_array('active_in_course', $T_DATASOURCE_COLUMNS)}<td class = "topTitle active_in_course" name = "active_in_course">{$smarty.const._ENABLED}</td>{/if}
{if in_array('completed', $T_DATASOURCE_COLUMNS)} <td class = "topTitle completed" name = "completed">{$smarty.const._COMPLETED}</td>{/if}
{if in_array('to_timestamp', $T_DATASOURCE_COLUMNS)} <td class = "topTitle to_timestamp" name = "to_timestamp">{$smarty.const._COMPLETEDON}</td>{/if}
{if in_array('score', $T_DATASOURCE_COLUMNS)} <td class = "topTitle score" name = "score">{$smarty.const._SCORE}</td>{/if}
{if in_array('operations', $T_DATASOURCE_COLUMNS)} <td class = "topTitle operations noSort">{$smarty.const._OPERATIONS}</td>{/if}
{if in_array('has_course', $T_DATASOURCE_COLUMNS)} <td class = "topTitle has_course" name = "has_course">{$smarty.const._STATUS}</td>{/if}
     </tr>
  {foreach name = 'users_to_courses_list' key = 'key' item = 'course' from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$course.active}deactivatedTableElement{/if}">
{if in_array('name', $T_DATASOURCE_COLUMNS)}
         <td class = "name">
          {if $course.has_instances && $T_SORTED_TABLE == 'coursesTable'}
     <img src = "images/16x16/plus.png" class = "ajaxHandle" alt = "{$smarty.const._COURSEINSTANCES}" title = "{$smarty.const._COURSEINSTANCES}" onclick = "toggleSubSection(this, '{$course.id}', 'instancesTable')"/>
    {elseif $course.num_lessons && $T_SHOW_COURSE_LESSONS}
     <img src = "images/16x16/plus2.png" class = "ajaxHandle" alt = "{$smarty.const._COURSELESSONS}" title = "{$smarty.const._COURSELESSONS}" onclick = "toggleSubSection(this, '{$course.id}', 'courseLessonsTable')"/>
    {/if}
    {if $_change_handles_ && !$T_IS_SUPERVISOR}
    <a href = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$course.id}" class = "editLink" title = "{$smarty.const._EDIT}">{$course.name}</a>
    {else}
    <span>{$course.name}</span>
    {/if}
   </td>
{/if}







{if in_array('directions_name', $T_DATASOURCE_COLUMNS)}
         <td class = "directions_name">
          {$course.directions_name}
         </td>
{/if}
{if in_array('directions_ID', $T_DATASOURCE_COLUMNS)}
         <td class = "directions_ID">
          {$T_DIRECTION_PATHS[$course.directions_ID]}
         </td>
{/if}
{if in_array('user_type', $T_DATASOURCE_COLUMNS)}
         <td class = "user_type">
       {if $_change_handles_}
        {if (($course.has_course && $course.has_instances)) && $T_SORTED_TABLE != 'instancesTable'}
         {$T_ROLES_ARRAY[$course.user_type]}
        {elseif $T_SORTED_TABLE == 'instancesTable' || !$course.has_instances}
         <span style = "display:none">{$T_ROLES_ARRAY[$course.user_type]}</span>
               <select name = "course_type_{$course.id}" id = "course_type_{$course.id}" onchange = "$('course_{$course.id}').checked = true;ajaxUserPost('course', '{$course.id}', this, '{$T_SORTED_TABLE}');">
             {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES_ARRAY}
                   <option value = "{$role_key}" {if !$course.user_type}{if ($T_EDITED_USER_TYPE == $role_key)}selected{/if}{else}{if ($course.user_type == $role_key)}selected{/if}{/if}>{$role_item}</option>
             {/foreach}
               </select>
           {/if}
       {else}
           {$T_ROLES_ARRAY[$course.user_type]}
       {/if}
         </td>
{/if}
{if in_array('num_students', $T_DATASOURCE_COLUMNS)}
   <td class = "num_students">
    {if $course.max_users}{$course.num_students}/{$course.max_users}{else}{$course.num_students}{/if}
   </td>
{/if}
{if in_array('num_lessons', $T_DATASOURCE_COLUMNS)}
   <td class = "num_lessons">
    {$course.num_lessons}
   </td>
{/if}







{if in_array('price', $T_DATASOURCE_COLUMNS) && $T_CONFIGURATION.disable_payments != 1}
   <td class = "price">
    {if $course.price == 0}-{else}{$course.price_string}{/if}
   </td>
{/if}
{if in_array('start_date', $T_DATASOURCE_COLUMNS)}
   <td class = "start_date">
    #filter:timestamp-{$course.start_date}#
   </td>
{/if}
{if in_array('end_date', $T_DATASOURCE_COLUMNS)}
   <td class = "end_date">
    #filter:timestamp-{$course.end_date}#
   </td>
{/if}
{if in_array('created', $T_DATASOURCE_COLUMNS)}
   <td class = "created">
    #filter:timestamp-{$course.created}#
   </td>
{/if}
{if in_array('active', $T_DATASOURCE_COLUMNS)}
   <td class = "active">
    {if $course.active == 1}
    <img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $_change_handles_}class = "ajaxHandle" onclick = "activateCourse(this, '{$course.id}')"{/if}>
    {else}
    <img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" {if $_change_handles_}class = "ajaxHandle" onclick = "activateCourse(this, '{$course.id}')"{/if}>
    {/if}
   </td>
{/if}
{if in_array('active_in_course', $T_DATASOURCE_COLUMNS)}
         <td class = "active_in_course">
    {if !$course.active_in_course && $course.has_course}
              <img src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}" {if $_change_handles_}class = "ajaxHandle" onclick = "toggleUserAccess(this, '{$course.id}', 'course')"{/if}/>
             {elseif $course.has_course}
              <img src = "images/16x16/success.png" title = "{$smarty.const._USERACCESSGRANTED}" alt = "{$smarty.const._USERACCESSGRANTED}" {if $_change_handles_}class = "ajaxHandle" onclick = "toggleUserAccess(this, '{$course.id}', 'course')"{/if}/>
             {/if}
         </td>
{/if}
{if in_array('completed', $T_DATASOURCE_COLUMNS)}
         <td class = "completed">
   {if $course.has_course && (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$course.user_type] == 'student')}
    {if $course.completed}
     <img src = "images/16x16/success.png" alt = "#filter:timestamp_time-{$course.to_timestamp}#" title = "#filter:timestamp_time-{$course.to_timestamp}#">
    {else}
     <img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">
    {/if}
   {/if}
   </td>
{/if}
{if in_array('to_timestamp', $T_DATASOURCE_COLUMNS)}
   <td class = "to_timestamp">{if $user.has_course}#filter:timestamp_time-{$user.to_timestamp}#{/if}</td>
{/if}
{if in_array('score', $T_DATASOURCE_COLUMNS)}
         <td class = "score">{if $course.has_course && (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$course.user_type] == 'student')}{if $course.completed}#filter:score-{$course.score}#%{else}-{/if}{/if}</td>
{/if}
{if in_array('operations', $T_DATASOURCE_COLUMNS)}
   <td class = "operations">{strip}
 {if !isset($T_DATASOURCE_OPERATIONS) || in_array('progress', $T_DATASOURCE_OPERATIONS)}
   {if !$course.has_instances || $T_SORTED_TABLE == 'instancesTable'}
    <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=user&sel_user={$smarty.get.sel_user}&specific_course_info=1&course={$course.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._DETAILS}', 2)"><img class = "handle" src = "images/16x16/information.png" title = "{$smarty.const._DETAILS}" alt = "{$smarty.const._DETAILS}" /></a>&nbsp;
   {/if}
 {/if}
 {if !isset($T_DATASOURCE_OPERATIONS) || in_array('statistics', $T_DATASOURCE_OPERATIONS)}
  {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
    <a href="{$smarty.server.PHP_SELF}?ctg=statistics&option=course&sel_course={$course.id}"><img class = "handle" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a>&nbsp;
  {/if}
 {/if}
 {if !isset($T_DATASOURCE_OPERATIONS) || in_array('settings', $T_DATASOURCE_OPERATIONS)}
    <a href = "{$smarty.server.PHP_SELF}?ctg={if $smarty.session.s_type == 'administrator'}courses{else}lessons{/if}&course={$course.id}&op=course_info"><img class = "handle" src = "images/16x16/generic.png" title = "{$smarty.const._COURSEINFORMATION}" alt = "{$smarty.const._COURSEINFORMATION}" /></a>
 {/if}
 {if !isset($T_DATASOURCE_OPERATIONS) || in_array('propagate', $T_DATASOURCE_OPERATIONS)}
    <img class = "ajaxHandle" src = "images/16x16/arrow_right.png" title = "{$smarty.const._PROPAGATECOURSE}" alt = "{$smarty.const._PROPAGATECOURSE}" onclick = "propagateCourse(this, '{$course.id}')"/>
 {/if}
 {if !isset($T_DATASOURCE_OPERATIONS) || in_array('delete', $T_DATASOURCE_OPERATIONS)}
  {if $_change_handles_}







    <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETECOURSE}')) deleteCourse(this, '{$course.id}');"/>


  {/if}
 {/if}
   {/strip}</td>
{/if}
{if in_array('has_course', $T_DATASOURCE_COLUMNS)}
         <td class = "has_course">
       {if $_change_handles_}
        {if (($course.has_course && $course.has_instances)) && $T_SORTED_TABLE != 'instancesTable'}
        <input class = "inputCheckBox" type="checkbox" name="{$course.id}" checked disabled">
        {elseif $T_SORTED_TABLE == 'instancesTable' || !$course.has_instances}
              <input class = "inputCheckBox" type="checkbox" id="course_{$course.id}" name="{$course.id}" {if $course.has_course == 1}checked{/if} onclick ="ajaxPost('{$course.id}', this, 'coursesTable');">
              {/if}
       {elseif $course.has_course == 1}
        {if (($course.has_course && $course.has_instances)) && $T_SORTED_TABLE != 'instancesTable'}
        <img src = "images/16x16/success.png" class = "inactiveImage" alt = "{$smarty.const._COURSEUSER}" title = "{$smarty.const._COURSEUSER}">
        {elseif $T_SORTED_TABLE == 'instancesTable' || !$course.has_instances}
        <img src = "images/16x16/success.png" alt = "{$smarty.const._COURSEUSER}" title = "{$smarty.const._COURSEUSER}">
              {/if}
       {/if}

         </td>
{/if}
     </tr>
  {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "{$T_DATASOURCE_COLUMNS|@sizeof}">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
{/capture}

{capture name = 'lessons_list'}
<style>
{literal}
table#lessonsTable,table#courseLessonsTable {width:100%;}
table#lessonsTable td.name, table#courseLessons td.name{width:50%;}
table#lessonsTable td.directions_ID,table#courseLessons td.directions_ID{width:15%;}
table#lessonsTable td.active_in_lesson,table#courseLessonsTable td.active_in_lesson{width:5%;text-align:center;}
table#lessonsTable td.user_type, table#courseLessonsTable td.user_type{width:25%;}
table#lessonsTable td.time_in_lesson, table#courseLessons td.time_in_lesson{width:25%;}
table#lessonsTable td.overall_progress,table#courseLessons td.overall_progress{width:5%;text-align:center;}
table#lessonsTable td.test_status, table#courseLessons td.test_status{width:5%;text-align:center;}
table#lessonsTable td.project_status,table#courseLessons td.project_status{width:5%;text-align:center;}
table#lessonsTable td.completed,table#courseLessons td.completed{width:5%;text-align:center;}
table#lessonsTable td.score,table#courseLessons td.score{width:5%;text-align:center;}
table#lessonsTable td.operations,table#courseLessons td.operations{width:5%;text-align:center;}
table#lessonsTable td.has_lesson,table#courseLessons td.has_lesson{width:5%;text-align:center;}
{/literal}
</style>
     <tr class = "topTitle">
{if in_array('name', $T_DATASOURCE_COLUMNS)} <td class = "topTitle name" name = "name">{$smarty.const._LESSON}</td>{/if}
{if in_array('directions_ID', $T_DATASOURCE_COLUMNS)} <td class = "topTitle directions_ID" name = "directions_ID">{$smarty.const._PARENTDIRECTIONS}</td>{/if}
{if in_array('user_type', $T_DATASOURCE_COLUMNS)} <td class = "topTitle user_type" name = "user_type">{$smarty.const._USERTYPE}</td>{/if}
{if in_array('active_in_lesson', $T_DATASOURCE_COLUMNS)}<td class = "topTitle active_in_lesson" name = "active_in_lesson">{$smarty.const._ENABLED}</td>{/if}
{if in_array('time_in_lesson', $T_DATASOURCE_COLUMNS)} <td class = "topTitle time_in_lesson" name = "time_in_lesson">{$smarty.const._TIMEINLESSON}</td>{/if}
{if in_array('overall_progress', $T_DATASOURCE_COLUMNS)}<td class = "topTitle overall_progress" name = "overall_progress">{$smarty.const._OVERALLPROGRESS}</td>{/if}
  {if !$T_CONFIGURATION.disable_tests}
{if in_array('test_status', $T_DATASOURCE_COLUMNS)} <td class = "topTitle test_status" name = "test_status">{$smarty.const._TESTSSCORE}</td>{/if}
  {/if}
  {if !$T_CONFIGURATION.disable_projects}
{if in_array('project_status', $T_DATASOURCE_COLUMNS)} <td class = "topTitle project_status" name = "project_status">{$smarty.const._PROJECTSSCORE}</td>{/if}
  {/if}
{if in_array('completed', $T_DATASOURCE_COLUMNS)} <td class = "topTitle completed" name = "completed">{$smarty.const._COMPLETED}</td>{/if}
{if in_array('score', $T_DATASOURCE_COLUMNS)} <td class = "topTitle score" name = "score">{$smarty.const._SCORE}</td>{/if}
{if in_array('operations', $T_DATASOURCE_COLUMNS)} <td class = "topTitle operations noSort">{$smarty.const._OPERATIONS}</td>{/if}
{if in_array('has_lesson', $T_DATASOURCE_COLUMNS)} <td class = "topTitle has_lesson" name = "has_lesson">{$smarty.const._STATUS}</td>{/if}
     </tr>
  {foreach name = 'users_to_lessons_list' key = 'key' item = 'lesson' from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
{if in_array('name', $T_DATASOURCE_COLUMNS)}
   <td class = "name">
    {if $_change_handles_ && !$T_IS_SUPERVISOR}
    <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}" class = "editLink" title = "{$smarty.const._EDIT}">{$lesson.name}</a>
    {else}
    <span>{$lesson.name}</span>
    {/if}
   </td>
{/if}
{if in_array('directions_ID', $T_DATASOURCE_COLUMNS)}
         <td class = "directions_ID">
          {$T_DIRECTION_PATHS[$lesson.directions_ID]}
         </td>
{/if}
{if in_array('user_type', $T_DATASOURCE_COLUMNS)}
         <td class = "user_type">
       {if $_change_handles_}
        <span style = "display:none">{$T_ROLES_ARRAY[$lesson.user_type]}</span>
              <select name = "lesson_type_{$lesson.id}" id = "lesson_type_{$lesson.id}" onchange = "$('lesson_{$lesson.id}').checked = true;ajaxUserPost('lesson', '{$lesson.id}', this);">
            {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES_ARRAY}
                  <option value = "{$role_key}" {if !$lesson.user_type}{if ($T_EDITED_USER_TYPE == $role_key)}selected{/if}{else}{if ($lesson.user_type == $role_key)}selected{/if}{/if}>{$role_item}</option>
            {/foreach}
              </select>
       {else}
           {$T_ROLES_ARRAY[$lesson.user_type]}
       {/if}
         </td>
{/if}
{if in_array('active_in_lesson', $T_DATASOURCE_COLUMNS)}
   <td class = "active_in_lesson">
    {if !$lesson.active_in_lesson && $lesson.has_lesson}
              <img src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}" {if $_change_handles_}class = "ajaxHandle" onclick = "toggleUserAccess(this, '{$lesson.id}', 'lesson')"{/if}/>
             {elseif $lesson.has_lesson}
              <img src = "images/16x16/success.png" title = "{$smarty.const._USERACCESSGRANTED}" alt = "{$smarty.const._USERACCESSGRANTED}" {if $_change_handles_}class = "ajaxHandle" onclick = "toggleUserAccess(this, '{$lesson.id}', 'lesson')"{/if}/>
             {/if}
   </td>
{/if}
{if in_array('time_in_lesson', $T_DATASOURCE_COLUMNS)}
   <td class = "time_in_lesson"><span style = "display:none">{$lesson.time_in_lesson.total_seconds}&nbsp;</span>{$lesson.time_in_lesson.time_string}</td>
{/if}
{if in_array('overall_progress', $T_DATASOURCE_COLUMNS)}
   <td class = "progressCell overall_progress">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$lesson.user_type] == 'student')}
    <span style = "display:none">{$lesson.overall_progress.completed+1000}</span>
    <span class = "progressNumber">#filter:score-{$lesson.overall_progress.percentage}#%</span>
    <span class = "progressBar" style = "width:{$lesson.overall_progress.percentage}px;">&nbsp;</span>&nbsp;&nbsp;
   {/if}
   </td>
{/if}
   {if !$T_CONFIGURATION.disable_tests}
{if in_array('test_status', $T_DATASOURCE_COLUMNS)}
    <td class = "progressCell test_status">
    {if $lesson.test_status && (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$lesson.user_type] == 'student')}
     <span style = "display:none">{$lesson.test_status.mean_score+1000}</span>
     <span class = "progressNumber">#filter:score-{$lesson.test_status.mean_score}#% ({$lesson.test_status.completed}/{$lesson.test_status.total})</span>
     <span class = "progressBar" style = "width:{$lesson.test_status.mean_score}px;">&nbsp;</span>&nbsp;&nbsp;
    {else}<div class = "centerAlign">-</div>{/if}
    </td>
{/if}
   {/if}
   {if !$T_CONFIGURATION.disable_projects}
{if in_array('project_status', $T_DATASOURCE_COLUMNS)}
    <td class = "progressCell project_status">
    {if $lesson.project_status && (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$lesson.user_type] == 'student')}
     <span style = "display:none">{$lesson.project_status.mean_score+1000}</span>
     <span class = "progressNumber">#filter:score-{$lesson.project_status.mean_score}#% ({$lesson.project_status.completed}/{$lesson.project_status.total})</span>
     <span class = "progressBar" style = "width:{$lesson.project_status.mean_score}px;">&nbsp;</span>&nbsp;&nbsp;
    {else}<div class = "centerAlign">-</div>{/if}
    </td>
{/if}
   {/if}
{if in_array('completed', $T_DATASOURCE_COLUMNS)}
   <td class = "completed">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$lesson.user_type] == 'student')}
    {if $lesson.completed}<img src = "images/16x16/success.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}"/>{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}"/>{/if}
   {/if}
   </td>
{/if}
{if in_array('score', $T_DATASOURCE_COLUMNS)}
   <td class = "score">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$lesson.user_type] == 'student')}
    {if $lesson.completed}#filter:score-{$lesson.score}#%{else}-{/if}
   {/if}
   </td>
{/if}
{if in_array('operations', $T_DATASOURCE_COLUMNS)}
   <td class = "operations">{strip}
 {if !isset($T_DATASOURCE_OPERATIONS) || in_array('progress', $T_DATASOURCE_OPERATIONS)}
    <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=user&sel_user={$smarty.get.sel_user}&specific_lesson_info=1&lesson={$lesson.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._DETAILS}', 2)"><img class = "handle" src = "images/16x16/information.png" title = "{$smarty.const._DETAILS}" alt = "{$smarty.const._DETAILS}" /></a>
 {/if}
   {/strip}</td>
{/if}
{if in_array('has_lesson', $T_DATASOURCE_COLUMNS)}
         <td class = "has_lesson">
       {if $_change_handles_}
              <input class = "inputCheckBox" type="checkbox" id="lesson_{$lesson.id}" name="{$lesson.id}" {if $lesson.has_lesson == 1}checked{/if} onclick ="ajaxPost('{$lesson.id}', this, 'lessonsTable');">
       {else}
        <img src = "images/16x16/success.png" alt = "{$smarty.const._LESSONUSER}" title = "{$smarty.const._LESSONUSER}">
       {/if}

         </td>
{/if}
  </tr>
  {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "{$T_DATASOURCE_COLUMNS|@sizeof}">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
{/capture}
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'coursesTable'}
<!--ajax:coursesTable-->
 <table size = "{$T_TABLE_SIZE}" sortBy = "{$T_DATASOURCE_SORT_BY}" order = "{$T_DATASOURCE_SORT_ORDER}" activeFilter = "1" id = "coursesTable" class = "sortedTable" useAjax = "1" url = "{$courses_url}">
 {$smarty.capture.courses_list}
 </table>
<!--/ajax:coursesTable-->
{/if}
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'instancesTable'}
<div id = "filemanager_div" style = "display:none;">
<!--ajax:instancesTable-->
 <table size = "{$T_TABLE_SIZE}" sortBy = "{$T_DATASOURCE_SORT_BY}" order = "{$T_DATASOURCE_SORT_ORDER}" activeFilter = "1" id = "instancesTable" class = "sortedTable subSection" no_auto = "1" useAjax = "1" url = "{$courses_url}">
  {$smarty.capture.courses_list}
 </table>
<!--/ajax:instancesTable-->
</div>
{/if}
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'courseLessonsTable'}
<div id = "filemanager_div" style = "display:none;">
<!--ajax:courseLessonsTable-->
  <table id = "courseLessonsTable" no_auto = "1" size = "{$T_TABLE_SIZE}" class = "sortedTable subSection" useAjax = "1" url = "{$courses_url}">
  {$smarty.capture.lessons_list}
  </table>
<!--/ajax:courseLessonsTable-->
</div>
{/if}
