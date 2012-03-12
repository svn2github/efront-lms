{capture name = 'course_users_list'}
<style>
{literal}
table#courseUsersTable,table#instanceUsersTable {width:100%;}
table#courseUsersTable td.login,table#instanceUsersTable td.login{width:20%;}
table#courseUsersTable td.name,table#instanceUsersTable td.name{width:20%;}
table#coursesTable td.location,table#instanceUsersTable td.location{width:20%;}
table#courseUsersTable td.user_type, table#instanceUsersTable td.user_type{width:25%;}
table#courseUsersTable td.active_in_course,table#instanceUsersTable td.active_in_course{width:5%;text-align:center;}
table#courseUsersTable td.completed,table#instanceUsersTable td.completed{width:5%;text-align:center;}
table#courseUsersTable td.enrolled_on,table#instanceUsersTable td.enrolled_on{width:10%;text-align:center;}
table#courseUsersTable td.to_timestamp,table#instanceUsersTable td.to_timestamp{width:10%;text-align:center;}
table#courseUsersTable td.score,table#instanceUsersTable td.score{width:5%;text-align:center;}
table#courseUsersTable td.issued_certificate,table#instanceUsersTable td.issued_certificate{width:5%;text-align:center;}
table#courseUsersTable td.expire_certificate,table#instanceUsersTable td.expire_certificate{width:5%;text-align:center;}
table#courseUsersTable td.operations,table#instanceUsersTable td.operations{width:5%;text-align:center;white-space:nowrap;}
table#courseUsersTable td.has_course,table#instanceUsersTable td.has_course{width:10%;text-align:center;}
{/literal}
</style>
<script>
if (typeof(currentUserLogin) == 'undefined') var currentUserLogin ='';
</script>
  <tr class = "topTitle">
{if in_array('login', $T_DATASOURCE_COLUMNS)} <td class = "topTitle login" name = "login">{$smarty.const._USER}</td>{/if}
{if in_array('name', $T_DATASOURCE_COLUMNS)} <td class = "topTitle name" name = "name">{$smarty.const._NAME}</td>{/if}
{if in_array('location', $T_DATASOURCE_COLUMNS)} <td class = "topTitle location" name = "location">{$smarty.const._LOCATION}</td>{/if}
{if in_array('user_type', $T_DATASOURCE_COLUMNS)} <td class = "topTitle user_type" name = "user_type">{$smarty.const._USERTYPE}</td>{/if}
{if in_array('active_in_course', $T_DATASOURCE_COLUMNS)}<td class = "topTitle active_in_course" name = "active_in_course">{$smarty.const._STATUS}</td>{/if}
{if in_array('lesson_percentage',$T_DATASOURCE_COLUMNS)}<td class = "topTitle lesson_percentage noSort" name = "lesson_percentage">{$smarty.const._PERCENTAGE}</td>{/if}
{if in_array('completed', $T_DATASOURCE_COLUMNS)} <td class = "topTitle completed" name = "completed">{$smarty.const._COMPLETED}</td>{/if}
{if in_array('enrolled_on', $T_DATASOURCE_COLUMNS)} <td class = "topTitle enrolled_on" name = "enrolled_on">{$smarty.const._ENROLLEDON}</td>{/if}
{if in_array('to_timestamp', $T_DATASOURCE_COLUMNS)} <td class = "topTitle to_timestamp" name = "to_timestamp">{$smarty.const._COMPLETEDON}</td>{/if}
{if in_array('score', $T_DATASOURCE_COLUMNS)} <td class = "topTitle score" name = "score">{$smarty.const._SCORE}</td>{/if}




{if in_array('operations', $T_DATASOURCE_COLUMNS)} <td class = "topTitle operations noSort">{$smarty.const._OPERATIONS}</td>{/if}
{if in_array('has_course', $T_DATASOURCE_COLUMNS)} <td class = "topTitle has_course" name = "has_course">{$smarty.const._CHECK}</td>{/if}
  </tr>
  {foreach name = 'users_to_courses_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
{if in_array('name', $T_DATASOURCE_COLUMNS)}
   <td class = "name">
    {if $T_CURRENT_COURSE->course.num_lessons && $T_SHOW_COURSE_LESSONS}
     <img src = "images/16x16/plus2.png" class = "ajaxHandle" alt = "{$smarty.const._COURSELESSONS}" title = "{$smarty.const._COURSELESSONS}" onclick = "toggleSubSection(this, '{$T_CURRENT_COURSE->course.id}', 'courseLessonsUsersTable', 'courseLessonsUsersTable_login='+currentUserLogin);"/>
    {/if}
    {$user.name}
   </td>
{/if}
{if in_array('login', $T_DATASOURCE_COLUMNS)}
   <td class = "login">
    {if $T_COURSE_HAS_INSTANCES && $T_SORTED_TABLE == 'courseUsersTable'}
     <img src = "images/16x16/plus.png" class = "ajaxHandle" alt = "{$smarty.const._COURSEINSTANCES}" title = "{$smarty.const._COURSEINSTANCES}" onclick = "currentUserLogin = '{$user.login}';toggleSubSection(this, '{$user.login}', 'instanceUsersTable')"/>
    {/if}
    <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=user&sel_user={$user.login}" class = "editLink" title = "{$smarty.const._EDIT}">#filter:login-{$user.login}#</a>
   </td>
{/if}
{if in_array('location', $T_DATASOURCE_COLUMNS)}
   <td class = "location" name = "location">{if !$course.has_instances || $T_SORTED_TABLE == 'instanceUsersTable'}{$course.location}{/if}</td>
{/if}
{if in_array('user_type', $T_DATASOURCE_COLUMNS)}
   <td class = "user_type">
   {if !$T_COURSE_HAS_INSTANCES || $T_SORTED_TABLE == 'instanceUsersTable'}
    {if $_change_handles_}
     <span style = "display:none">{$T_ROLES_ARRAY[$user.user_type]}</span>
     <select name = "user_type_{$user.login}" id = "user_type_{$user.login}" onchange = "$('user_{$user.login}').checked = true;ajaxUserPost('user', '{$user.login}', this);">
      {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES_ARRAY}
      <option value = "{$role_key}" {if !$user.user_type}{if ($T_EDITED_USER_TYPE == $role_key)}selected{/if}{else}{if ($user.user_type == $role_key)}selected{/if}{/if}>{$role_item}</option>
      {/foreach}
     </select>
    {else}
     {$T_ROLES_ARRAY[$user.role]}
    {/if}
   {/if}
   </td>
{/if}
{if in_array('active_in_course', $T_DATASOURCE_COLUMNS)}
   <td class = "active_in_course">
   {if !$T_COURSE_HAS_INSTANCES || $T_SORTED_TABLE == 'instanceUsersTable'}
    {if !$user.active_in_course && $user.has_course}
     <img src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}" {if $_change_handles_}class = "ajaxHandle" onclick = "toggleUserAccess(this, '{$user.login}', 'user')"{/if}/>
    {elseif $user.has_course}
     <img src = "images/16x16/success.png" title = "{$smarty.const._USERHASTHECOURSE}" alt = "{$smarty.const._USERHASTHECOURSE}" {if $_change_handles_}class = "ajaxHandle" onclick = "toggleUserAccess(this, '{$user.login}', 'user')"{/if}/>
    {/if}
   {/if}
   </td>
{/if}
{if in_array('lesson_percentage', $T_DATASOURCE_COLUMNS)}
   <td class = "lesson_percentage">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$user.role] == 'student')}
     <span style = "display:none">{$user.lesson_percentage+1000}</span>
     <span class = "progressNumber">#filter:score-{$user.lesson_percentage}#%</span>
     <span class = "progressBar" style = "width:{","|str_replace:".":$user.lesson_percentage}px;">&nbsp;</span>&nbsp;&nbsp;
   {/if}
   </td>
{/if}
{if in_array('completed', $T_DATASOURCE_COLUMNS)}
   <td class = "completed">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$user.role] == 'student')}
    {if $user.has_course}
     {if $user.completed}
      <img src = "images/16x16/success.png" alt = "#filter:timestamp_time-{$user.to_timestamp}#" title = "#filter:timestamp_time-{$user.to_timestamp}#">
     {else}
      <img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">
     {/if}
    {/if}
   {/if}
   </td>
{/if}
{if in_array('enrolled_on', $T_DATASOURCE_COLUMNS)}
   <td class = "enrolled_on">{if $user.has_course}#filter:timestamp-{$user.enrolled_on}#{/if}</td>
{/if}
{if in_array('to_timestamp', $T_DATASOURCE_COLUMNS)}
   <td class = "to_timestamp">{if $user.has_course}#filter:timestamp-{$user.to_timestamp}#{/if}</td>
{/if}
{if in_array('score', $T_DATASOURCE_COLUMNS)}
   <td class = "score">{if $user.has_course && (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$user.role] == 'student')}{if $user.completed}#filter:score-{$user.score}#%{else}-{/if}{/if}</td>
{/if}
{if in_array('operations', $T_DATASOURCE_COLUMNS)}
   <td class = "operations">{strip}
 {if !isset($T_DATASOURCE_OPERATIONS) || in_array('statistics', $T_DATASOURCE_OPERATIONS)}
  {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
    <a href="{$smarty.server.PHP_SELF}?ctg=statistics&option=user&sel_user={$user.login}"><img class = "handle" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a>&nbsp;
  {/if}
 {/if}
 {if !isset($T_DATASOURCE_OPERATIONS) || in_array('certificate', $T_DATASOURCE_OPERATIONS)}
 {/if}
 {if !isset($T_DATASOURCE_OPERATIONS) || in_array('progress', $T_DATASOURCE_OPERATIONS)}
  {if (!isset($T_CURRENT_USER->coreAccess.course_settings) || $T_CURRENT_USER->coreAccess.course_settings == 'change')}
    <a href = "{$smarty.server.PHP_SELF}?{$T_BASE_URL}&op=course_certificates&edit_user={$user.login}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PROGRESS}', 2)" title = "{$smarty.const._VIEWUSERLESSONPROGRESS}">
     <img src = "images/16x16/users.png" title = "{$smarty.const._VIEWUSERCOURSEPROGRESS}" alt = "{$smarty.const._VIEWUSERCOURSEPROGRESS}"/>
    </a>
  {/if}
 {/if}
   {/strip}</td>
{/if}
{if in_array('has_course', $T_DATASOURCE_COLUMNS)}
   <td class = "has_course">
    {if $_change_handles_}
     {if (($user.has_course && $T_COURSE_HAS_INSTANCES)) && $T_SORTED_TABLE != 'instanceUsersTable'}
     <input class = "inputCheckBox" type="checkbox" name="{$user.login}" checked disabled">
     {elseif $T_SORTED_TABLE == 'instanceUsersTable' || !$T_COURSE_HAS_INSTANCES}
     <input class = "inputCheckBox" type="checkbox" id="user_{$user.login}" name="{$user.login}" {if $user.has_course == 1}checked{/if} onclick ="ajaxPost('{$user.login}', this, 'userUsersTable');">
     {/if}
    {elseif $user.has_course == 1}
     {if (($user.has_course && $T_COURSE_HAS_INSTANCES)) && $T_SORTED_TABLE != 'instanceUsersTable'}
     <img src = "images/16x16/success.png" class = "inactiveImage" alt = "{$smarty.const._COURSEUSER}" title = "{$smarty.const._COURSEUSER}">
     {elseif $T_SORTED_TABLE == 'instanceUsersTable' || !$T_COURSE_HAS_INSTANCES}
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
table#lessonsTable,table#courseLessonsUsersTable {width:100%;}
table#lessonsTable td.name, table#courseLessons td.name{width:50%;}
table#lessonsTable td.time_in_lesson, table#courseLessons td.time_in_lesson{width:25%;}
table#lessonsTable td.overall_progress,table#courseLessons td.overall_progress{width:5%;text-align:center;}
table#lessonsTable td.test_status, table#courseLessons td.test_status{width:5%;text-align:center;}
table#lessonsTable td.project_status,table#courseLessons td.project_status{width:5%;text-align:center;}
table#lessonsTable td.completed,table#courseLessons td.completed{width:5%;text-align:center;}
table#lessonsTable td.score,table#courseLessons td.score{width:5%;text-align:center;}
{/literal}
</style>
  <tr class = "topTitle">
{if in_array('name', $T_DATASOURCE_COLUMNS)} <td class = "topTitle name" name = "name">{$smarty.const._LESSON}</td>{/if}
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
  </tr>
  {foreach name = 'users_to_lessons_list' key = 'key' item = 'lesson' from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
{if in_array('name', $T_DATASOURCE_COLUMNS)}
   <td class = "name">{$lesson.name}{* ({$T_ROLES[$lesson.user_type]})*}</td>
{/if}
{if in_array('time_in_lesson', $T_DATASOURCE_COLUMNS)}
   <td class = "time_in_lesson"><span style = "display:none">{$lesson.time_in_lesson.total_seconds}&nbsp;</span>{$lesson.time_in_lesson.time_string}</td>
{/if}
{if in_array('overall_progress', $T_DATASOURCE_COLUMNS)}
   <td class = "progressCell overall_progress">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$user.user_type] == 'student')}
    <span style = "display:none">{$lesson.overall_progress.completed+1000}</span>
    <span class = "progressNumber">#filter:score-{$lesson.overall_progress.percentage}#%</span>
    <span class = "progressBar" style = "width:{$lesson.overall_progress.percentage}px;">&nbsp;</span>&nbsp;&nbsp;
   {/if}
   </td>
{/if}
   {if !$T_CONFIGURATION.disable_tests}
{if in_array('test_status', $T_DATASOURCE_COLUMNS)}
   <td class = "progressCell test_status">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$user.user_type] == 'student')}
    {if $lesson.test_status}
     <span style = "display:none">{$lesson.test_status.mean_score+1000}</span>
     <span class = "progressNumber">#filter:score-{$lesson.test_status.mean_score}#% ({$lesson.test_status.completed}/{$lesson.test_status.total})</span>
     <span class = "progressBar" style = "width:{$lesson.test_status.mean_score}px;">&nbsp;</span>&nbsp;&nbsp;
    {else}
     <div class = "centerAlign">-</div>
    {/if}
   {/if}
   </td>
{/if}
   {/if}
   {if !$T_CONFIGURATION.disable_projects}
{if in_array('project_status', $T_DATASOURCE_COLUMNS)}
   <td class = "progressCell project_status">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$user.user_type] == 'student')}
    {if $lesson.project_status}
     <span style = "display:none">{$lesson.project_status.mean_score+1000}</span>
     <span class = "progressNumber">#filter:score-{$lesson.project_status.mean_score}#% ({$lesson.project_status.completed}/{$lesson.project_status.total})</span>
     <span class = "progressBar" style = "width:{$lesson.project_status.mean_score}px;">&nbsp;</span>&nbsp;&nbsp;
    {else}
     <div class = "centerAlign">-</div>
    {/if}
   {/if}
   </td>
{/if}
   {/if}
{if in_array('completed', $T_DATASOURCE_COLUMNS)}
   <td class = "completed">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$user.user_type] == 'student')}
    {if $lesson.completed}<img src = "images/16x16/success.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}"/>{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}"/>{/if}
   {/if}
   </td>
{/if}
{if in_array('score', $T_DATASOURCE_COLUMNS)}
   <td class = "score">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$user.user_type] == 'student')}
    {if $lesson.completed}#filter:score-{$lesson.score}#%{else}-{/if}
   {/if}
   </td>
{/if}
  </tr>
  {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "{$T_DATASOURCE_COLUMNS|@sizeof}">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
{/capture}
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'courseUsersTable'}
<!--ajax:courseUsersTable-->
 <table size = "{$T_TABLE_SIZE}" sortBy = "{$T_DATASOURCE_SORT_BY}" id = "courseUsersTable" class = "sortedTable" useAjax = "1" url = "{$courseUsers_url}">
  {$smarty.capture.course_users_list}
 </table>
<!--/ajax:courseUsersTable-->
 {/if}
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'instanceUsersTable'}
<div id = "filemanager_div" style = "display:none;">
<!--ajax:instanceUsersTable-->
 <table size = "{$T_TABLE_SIZE}" sortBy = "{$T_DATASOURCE_SORT_BY}" id = "instanceUsersTable" class = "sortedTable subSection" no_auto = "1" useAjax = "1" url = "{$courseUsers_url}">
  {$smarty.capture.course_users_list}
 </table>
<!--/ajax:instanceUsersTable-->
</div>
 {/if}
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'courseLessonsUsersTable'}
<div id = "filemanager_div" style = "display:none;">
<!--ajax:courseLessonsUsersTable-->
  <table id = "courseLessonsUsersTable" no_auto = "1" size = "{$T_TABLE_SIZE}" class = "sortedTable subSection" useAjax = "1" url = "{$courseUsers_url}">
  {$smarty.capture.lessons_list}
  </table>
<!--/ajax:courseLessonsUsersTable-->
</div>
 {/if}
