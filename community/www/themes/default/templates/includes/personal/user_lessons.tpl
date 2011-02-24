{capture name = 'lessons_list'}
     <tr class = "topTitle">
   <td class = "topTitle" name = "name">{$smarty.const._LESSON}</td>
   <td class = "topTitle" name = "directions_ID">{$smarty.const._PARENTDIRECTIONS}</td>
   <td class = "topTitle centerAlign" name = "active_in_lesson">{$smarty.const._ENABLED}</td>
   <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
   <td class = "topTitle centerAlign" name = "completed">{$smarty.const._COMPLETED}</td>
   <td class = "topTitle centerAlign" name = "score">{$smarty.const._SCORE}</td>
   <td class = "topTitle centerAlign" name = "has_lesson">{$smarty.const._STATUS}</td>
     </tr>
  {foreach name = 'users_to_lessons_list' key = 'key' item = 'lesson' from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
   <td>
    {if $_change_lessons_ && !$T_IS_SUPERVISOR}
    <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}" class = "editLink" title = "{$smarty.const._EDIT}">{$lesson.name}</a>
    {else}
    <span>{$lesson.name}</span>
    {/if}
   </td>
         <td>
          {$T_DIRECTION_PATHS[$lesson.directions_ID]}
         </td>
   <td class = "centerAlign">
    {if !$lesson.active_in_lesson && $lesson.has_lesson}
              <img src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}" {if $_change_lessons_}class = "ajaxHandle" onclick = "toggleUserAccess(this, '{$lesson.id}', 'lesson')"{/if}/>
             {elseif $lesson.has_lesson}
              <img src = "images/16x16/success.png" title = "{$smarty.const._USERACCESSGRANTED}" alt = "{$smarty.const._USERACCESSGRANTED}" {if $_change_lessons_}class = "ajaxHandle" onclick = "toggleUserAccess(this, '{$lesson.id}', 'lesson')"{/if}/>
             {/if}
   </td>
         <td>
         {if $_change_lessons_}
          <span style = "display:none">{$T_ROLES_ARRAY[$lesson.user_type]}</span>
                <select name = "lesson_type_{$lesson.id}" id = "lesson_type_{$lesson.id}" onchange = "$('lesson_{$lesson.id}').checked = true;ajaxPost('{$lesson.id}', this, 'lessonsTable');">
              {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES_ARRAY}
                    <option value = "{$role_key}" {if !$lesson.user_type}{if ($T_EDITED_USER_TYPE == $role_key)}selected{/if}{else}{if ($lesson.user_type == $role_key)}selected{/if}{/if}>{$role_item}</option>
              {/foreach}
                </select>
         {else}
             {$T_ROLES_ARRAY[$lesson.user_type]}
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
         <td class = "centerAlign">
       {if $_change_lessons_}
              <input class = "inputCheckBox" type="checkbox" id="lesson_{$lesson.id}" name="{$lesson.id}" {if $lesson.has_lesson == 1}checked{/if} onclick ="ajaxPost('{$lesson.id}', this, 'lessonsTable');">
       {else}
        <img src = "images/16x16/success.png" alt = "{$smarty.const._LESSONUSER}" title = "{$smarty.const._LESSONUSER}">
       {/if}
         </td>
  </tr>
  {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "{$T_DATASOURCE_COLUMNS|@sizeof}">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
{/capture}

{capture name = "t_lessons_code"}
 <script>
  translationsToJS['_USERACCESSGRANTED'] = '{$smarty.const._USERACCESSGRANTED}';
  translationsToJS['_APPLICATIONPENDING'] = '{$smarty.const._APPLICATIONPENDING}';
 </script>
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'lessonsTable'}
<!--ajax:lessonsTable-->
  <table style = "width:100%" id = "lessonsTable" size = "{$T_TABLE_SIZE}" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=personal&user={$smarty.get.user}&op=user_lessons&">
  {$smarty.capture.lessons_list}
  </table>
<!--/ajax:lessonsTable-->
 {/if}

{/capture}

{eF_template_printBlock title = $smarty.const._LESSONS data = $smarty.capture.t_lessons_code image = '32x32/lessons.png'}
