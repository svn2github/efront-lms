{capture name = 'lessons_list'}
     <tr class = "topTitle">
   <td class = "topTitle" name = "name" style = "width:84%">{$smarty.const._LESSON}</td>
   <td class = "topTitle centerAlign" name = "completed" style = "width:8%">{$smarty.const._COMPLETED}</td>
   <td class = "topTitle centerAlign" name = "score" style = "width:8%">{$smarty.const._SCORE}</td>
     </tr>
  {foreach name = 'users_to_lessons_list' key = 'key' item = 'lesson' from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
   <td>
    {if $_change_courses_ && !$T_IS_SUPERVISOR}
    <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}" class = "editLink" title = "{$smarty.const._EDIT}">{$lesson.name}</a>
    {else}
    <span>{$lesson.name}</span>
    {/if}
   </td>
   <td class = "centerAlign">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$lesson.user_type] == 'student')}
    {if $lesson.completed}<img src = "images/16x16/success.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}"/>{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}"/>{/if}
   {/if}
   </td>
   <td class = "centerAlign">
   {if (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$lesson.user_type] == 'student')}
    {if $lesson.completed}#filter:score-{$lesson.score}#%{else}-{/if}
   {/if}
   </td>
  </tr>
  {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "{$T_DATASOURCE_COLUMNS|@sizeof}">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
{/capture}

{capture name = 'courses_list'}
    <tr class = "topTitle">
  <td class = "topTitle" name = "name" style = "width:30%">{$smarty.const._NAME}</td>



  <td class = "topTitle" name = "directions_ID" style = "width:15%">{$smarty.const._PARENTDIRECTIONS}</td>
  <td class = "topTitle centerAlign" name = "active_in_course" style = "width:8%">{$smarty.const._ENABLED}</td>
  <td class = "topTitle" name = "user_type" style = "width:8%">{$smarty.const._USERTYPE}</td>
  <td class = "topTitle centerAlign" name = "completed" style = "width:8%">{$smarty.const._COMPLETED}</td>
  <td class = "topTitle centerAlign" name = "score" style = "width:8%">{$smarty.const._SCORE}</td>
  <td class = "topTitle centerAlign" name = "has_course" style = "width:8%">{$smarty.const._STATUS}</td>
    </tr>
  {foreach name = 'users_to_courses_list' key = 'key' item = 'course' from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$course.active}deactivatedTableElement{/if}">
         <td>
          {if $course.has_instances && $T_SORTED_TABLE == 'coursesTable'}
     <img src = "images/16x16/plus.png" class = "ajaxHandle" alt = "{$smarty.const._COURSEINSTANCES}" title = "{$smarty.const._COURSEINSTANCES}" onclick = "toggleSubSection(this, '{$course.id}', 'instancesTable')"/>
    {elseif $course.num_lessons && $T_SHOW_COURSE_LESSONS}
     <img src = "images/16x16/plus2.png" class = "ajaxHandle" alt = "{$smarty.const._COURSELESSONS}" title = "{$smarty.const._COURSELESSONS}" onclick = "toggleSubSection(this, '{$course.id}', 'courseLessonsTable')"/>
    {/if}
    {if $_change_courses_ && !$T_IS_SUPERVISOR}
    <a href = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$course.id}" class = "editLink" title = "{$smarty.const._EDIT}">{$course.name}</a>
    {else}
    <span>{$course.name}</span>
    {/if}
   </td>



         <td>{$T_DIRECTION_PATHS[$course.directions_ID]}</td>
         <td class = "centerAlign">
    {if !$course.active_in_course && $course.has_course}
              <img src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}" {if $course.has_instances && $T_SORTED_TABLE != 'instancesTable'}class = "inactiveImage" {else}{if $_change_courses_}class = "ajaxHandle" onclick = "toggleUserAccess(this, '{$course.id}', 'course')"{/if}{/if}/>
             {elseif $course.has_course}
              <img src = "images/16x16/success.png" title = "{$smarty.const._USERACCESSGRANTED}" alt = "{$smarty.const._USERACCESSGRANTED}" {if $course.has_instances && $T_SORTED_TABLE != 'instancesTable'}class = "inactiveImage" {else}{if $_change_courses_}class = "ajaxHandle" onclick = "toggleUserAccess(this, '{$course.id}', 'course')"{/if}{/if}/>
             {/if}
         </td>
         <td>
       {if $_change_courses_}
        {if (($course.has_course && $course.has_instances)) && $T_SORTED_TABLE != 'instancesTable'}
         {$T_ROLES_ARRAY[$course.user_type]}
        {elseif $T_SORTED_TABLE == 'instancesTable' || !$course.has_instances}
         <span style = "display:none">{$T_ROLES_ARRAY[$course.user_type]}</span>
               <select name = "course_type_{$course.id}" id = "course_type_{$course.id}" onchange = "$('course_{$course.id}').checked = true;ajaxPost('{$course.id}', this, 'coursesTable');">
             {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES_ARRAY}
                   <option value = "{$role_key}" {if !$course.user_type}{if ($T_EDITED_USER_TYPE == $role_key)}selected{/if}{else}{if ($course.user_type == $role_key)}selected{/if}{/if}>{$role_item}</option>
             {/foreach}
               </select>
           {/if}
       {else}
           {$T_ROLES_ARRAY[$course.user_type]}
       {/if}
         </td>
         <td class = "centerAlign">
   {if $course.has_course && (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$course.user_type] == 'student')}
    {if $course.completed}
     <img src = "images/16x16/success.png" alt = "#filter:timestamp_time-{$course.to_timestamp}#" title = "#filter:timestamp_time-{$course.to_timestamp}#">
    {else}
     <img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">
    {/if}
   {/if}
   </td>
         <td class = "centerAlign">{if $course.has_course && (!$T_BASIC_ROLES_ARRAY || $T_BASIC_ROLES_ARRAY[$course.user_type] == 'student')}{if $course.completed}#filter:score-{$course.score}#%{else}-{/if}{/if}</td>
         <td class = "centerAlign">
       {if $_change_courses_}
        {if (($course.has_course && $course.has_instances)) && $T_SORTED_TABLE != 'instancesTable'}
        <input class = "inputCheckBox" type="checkbox" name="{$course.id}" checked disabled>
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
     </tr>
  {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "{$T_DATASOURCE_COLUMNS|@sizeof}">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
{/capture}

{capture name = "t_courses_list_code"}
 <script>
  translationsToJS['_USERACCESSGRANTED'] = '{$smarty.const._USERACCESSGRANTED}';
  translationsToJS['_APPLICATIONPENDING'] = '{$smarty.const._APPLICATIONPENDING}';
 </script>

 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'coursesTable'}
<!--ajax:coursesTable-->
  <table style = "width:100%" size = "{$T_TABLE_SIZE}" sortBy = "{$T_DATASOURCE_SORT_BY}" order = "{$T_DATASOURCE_SORT_ORDER}" activeFilter = "1" id = "coursesTable" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=personal&user={$smarty.get.user}&op=user_courses&">
   {$smarty.capture.courses_list}
  </table>
<!--/ajax:coursesTable-->
 {/if}
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'instancesTable'}
 <div id = "filemanager_div" style = "display:none;">
<!--ajax:instancesTable-->
  <table style = "width:100%" size = "{$T_TABLE_SIZE}" sortBy = "{$T_DATASOURCE_SORT_BY}" order = "{$T_DATASOURCE_SORT_ORDER}" activeFilter = "1" id = "instancesTable" class = "sortedTable subSection" no_auto = "1" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=personal&user={$smarty.get.user}&op=user_courses&">
   {$smarty.capture.courses_list}
  </table>
<!--/ajax:instancesTable-->
 </div>
 {/if}
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'courseLessonsTable'}
 <div id = "filemanager_div" style = "display:none;">
<!--ajax:courseLessonsTable-->
  <table style = "width:100%" id = "courseLessonsTable" sortBy = "{$T_DATASOURCE_SORT_BY}" no_auto = "1" size = "{$T_TABLE_SIZE}" class = "sortedTable subSection" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=personal&user={$smarty.get.user}&op=user_courses&">
   {$smarty.capture.lessons_list}
  </table>
<!--/ajax:courseLessonsTable-->
 </div>
 {/if}
{/capture}

{eF_template_printBlock tabber="courses" title = $smarty.const._COURSES data = $smarty.capture.t_courses_list_code image = '32x32/courses.png'}
