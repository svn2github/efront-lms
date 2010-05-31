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
<script>var currentUserLogin ='';</script>
  <tr class = "topTitle">
{if in_array('login', $T_DATASOURCE_COLUMNS)} <td class = "topTitle login" name = "login">{$smarty.const._USER}</td>{/if}
{if in_array('name', $T_DATASOURCE_COLUMNS)} <td class = "topTitle name" name = "name">{$smarty.const._NAME}</td>{/if}
{if in_array('location', $T_DATASOURCE_COLUMNS)} <td class = "topTitle location" name = "location">{$smarty.const._LOCATION}</td>{/if}
{if in_array('user_type', $T_DATASOURCE_COLUMNS)} <td class = "topTitle user_type" name = "user_type">{$smarty.const._USERTYPE}</td>{/if}
{if in_array('active_in_course', $T_DATASOURCE_COLUMNS)}<td class = "topTitle active_in_course" name = "active_in_course">{$smarty.const._STATUS}</td>{/if}
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
     {$T_ROLES_ARRAY[$user.user_type]}
    {/if}
   {/if}
   </td>
{/if}
{if in_array('active_in_course', $T_DATASOURCE_COLUMNS)}
   <td class = "active_in_course">
   {if !$T_COURSE_HAS_INSTANCES || $T_SORTED_TABLE == 'instanceUsersTable'}
    {if !$user.active_in_course && $user.has_course}
     <img src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}" {if $_change_handles_}class = "ajaxHandle" onclick = "confirmUser(this, '{$user.login}', 'user')"{/if}/>
    {elseif $user.has_course}
     <img src = "images/16x16/success.png" title = "{$smarty.const._USERHASTHECOURSE}" alt = "{$smarty.const._USERHASTHECOURSE}" {if $_change_handles_}class = "ajaxHandle" onclick = "unConfirmUser(this, '{$user.login}', 'user')"{/if}/>
    {/if}
   {/if}
   </td>
{/if}
{if in_array('completed', $T_DATASOURCE_COLUMNS)}
   <td class = "completed">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$user.user_type] == 'student')}
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
   <td class = "score">{if $user.has_course && (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$user.user_type] == 'student')}#filter:score-{$user.score}#%{/if}</td>
{/if}
