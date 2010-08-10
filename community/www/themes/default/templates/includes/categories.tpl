{capture name = "moduleDirections"}
 <tr><td class = "moduleCell">

 {if $smarty.get.add_direction || $smarty.get.edit_direction}

  {capture name = 't_direction_settings_code'}
   {$T_DIRECTIONS_FORM.javascript}
   <form {$T_DIRECTIONS_FORM.attributes}>
    {$T_DIRECTIONS_FORM.hidden}
    <table class = "formElements">
     <tr><td class = "labelCell">{$T_DIRECTIONS_FORM.name.label}:&nbsp;</td>
      <td>{$T_DIRECTIONS_FORM.name.html}</td></tr>
     {if $T_DIRECTIONS_FORM.name.error}<tr><td></td><td class = "formError">{$T_DIRECTIONS_FORM.name.error}</td></tr>{/if}
      <tr><td class = "labelCell">{$T_DIRECTIONS_FORM.parent_direction_ID.label}:&nbsp;</td>
      <td>{$T_DIRECTIONS_FORM.parent_direction_ID.html}</td></tr>
     {if $T_DIRECTIONS_FORM.parent_direction_ID.error}<tr><td></td><td class = "formError">{$T_DIRECTIONS_FORM.parent_direction_ID.error}</td></tr>{/if}
     <tr><td></td><td class = "submitCell">{$T_DIRECTIONS_FORM.submit_direction.html}</td></tr>
    </table>
   </form>
  {/capture}

  {if $smarty.get.edit_direction}
   <script>var editCategory = '{$smarty.get.edit_direction}';</script>

   {capture name = 't_lessons_to_directions_code'}
<!--ajax:lessonsTable-->
    <table style = "width:100%" class = "sortedTable" size = "{$T_LESSONS_SIZE}" sortBy = "0" useAjax = "1" id = "lessonsTable" rowsPerPage = "20" url = "administrator.php?ctg=directions&edit_direction={$smarty.get.edit_direction}&">
     <tr class = "topTitle">
      <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
      <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
      <td class = "topTitle centerAlign" >{$smarty.const._SELECT}</td>
     </tr>
    {foreach name = 'lessons_list2' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
     <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
      <td>{$lesson.name}</td>
      <td>{$lesson.languages_NAME}</td>
      <td class = "centerAlign">
     {if $_change_}
       <select name = "directions" id = "lesson_{$lesson.id}" onchange = "ajaxPost('lesson_{$lesson.id}', this, 'lessonsTable');">
      {foreach name = 'directions_list' key = "key" item = "item" from = $T_DIRECTIONS_PATHS}
        <option value = "{$key}" {if $lesson.directions_ID == $key}selected{/if}>{$item}</option>
      {/foreach}
       </select>
     {else}
          {$T_DIRECTIONS_PATHS[$lesson.directions_ID]}
     {/if}
      </td>
    {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
    </table>
<!--/ajax:lessonsTable-->
   {/capture}

   {capture name = 't_courses_to_directions_code'}
<!--ajax:coursesTable-->
    <table style = "width:100%" class = "sortedTable" size = "{$T_COURSES_SIZE}" sortBy = "0" useAjax = "1" id = "coursesTable" rowsPerPage = "20" url = "administrator.php?ctg=directions&edit_direction={$smarty.get.edit_direction}&">
     <tr class = "topTitle">
      <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
      <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
      <td class = "topTitle centerAlign" >{$smarty.const._SELECT}</td>
     </tr>
    {foreach name = 'courses_list2' key = 'key' item = 'course' from = $T_COURSES_DATA}
     <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$course.active}deactivatedTableElement{/if}">
      <td>{$course.name}</td>
      <td>{$course.languages_NAME}</td>
      <td class = "centerAlign">
     {if $_change_}
       <select name = "directions" id = "course_{$course.id}" onchange = "ajaxPost('course_{$course.id}', this, 'coursesTable');">
      {foreach name = 'directions_list' key = "key" item = "item" from = $T_DIRECTIONS_PATHS}
        <option value = "{$key}" {if $course.directions_ID == $key}selected{/if}>{$item}</option>
      {/foreach}
       </select>
     {else}
       {$T_DIRECTIONS_PATHS[$course.directions_ID]}
     {/if}
      </td>
    {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
    </table>
<!--/ajax:coursesTable-->
   {/capture}
  {/if}

  {capture name = 't_direction_code'}
   <div class = "tabber">
    {eF_template_printBlock tabber = "settings" title ="`$smarty.const._CATEGORYSETTINGS`" data = $smarty.capture.t_direction_settings_code image = '32x32/categories.png'}
   {if $smarty.get.edit_direction}
    {eF_template_printBlock tabber = "lessons" title ="`$smarty.const._EDITLESSONSDIRECTION`" data = $smarty.capture.t_lessons_to_directions_code image = '32x32/lessons.png'}
    {eF_template_printBlock tabber = "courses" title ="`$smarty.const._EDITCOURSESDIRECTION`" data = $smarty.capture.t_courses_to_directions_code image = '32x32/courses.png'}
   {/if}
   </div>
  {/capture}

  {if $smarty.get.add_direction}
   {eF_template_printBlock title = $smarty.const._NEWDIRECTIONOPTIONS data = $smarty.capture.t_direction_code image = '32x32/categories.png'}
  {else}
   {eF_template_printBlock title ="`$smarty.const._DIRECTIONOPTIONSFOR` <span class = 'innerTableName'>&quot;`$T_DIRECTIONS_FORM.name.value`&quot;</span>" data = $smarty.capture.t_direction_code image = '32x32/categories.png'}
  {/if}

 {else}
  {capture name = 't_directions_code'}
   {if $_change_}
    <div class = "headerTools">
     <span>
      <img src = "images/16x16/add.png" title = "{$smarty.const._NEWDIRECTION}" alt = "{$smarty.const._NEWDIRECTION}">
      <a href = "{$smarty.server.PHP_SELF}?ctg=directions&add_direction=1" title = "{$smarty.const._NEWDIRECTION}" >{$smarty.const._NEWDIRECTION}</a>
     </span>
    </div>
   {/if}
    <table border = "0" width = "100%" class = "sortedTable" sortBy = "0">
     <tr class = "topTitle">
      <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
      <td class = "topTitle" name = "pathString">{$smarty.const._PARENTDIRECTIONS}</td>
      <td class = "topTitle centerAlign" name = "lessons">{$smarty.const._LESSONS}</td>
      <td class = "topTitle centerAlign" name = "lessons">{$smarty.const._COURSES}</td>
      <td class = "topTitle centerAlign">{$smarty.const._ACTIVE2}</td>
     {if $_change_}
      <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
     {/if}
     </tr>
   {foreach name = 'directions_list' key = 'key' item = 'direction' from = $T_DIRECTIONS_DATA}
     <tr id="row_{$direction.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$direction.active}deactivatedTableElement{/if}">
      <td><a href = "{$smarty.server.PHP_SELF}?ctg=directions&edit_direction={$direction.id}" class = "editLink"><span id="column_{$direction.id}" {if !$direction.active}style="color:red"{/if}>{$direction.name}</span></a></td>
      <td>{$direction.pathString}</td>
      <td class = "centerAlign">{$direction.lessons}</td>
      <td class = "centerAlign">{$direction.courses}</td>
      <td class = "centerAlign">
     {if $direction.active == 1}
      {if $direction.lessons > 0 || $direction.courses > 0}
       <img class = "ajaxHandle inactiveImage" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._YOUCANNOTDEACTIVATEANONEMPTYDIRECTION}" title = "{$smarty.const._YOUCANNOTDEACTIVATEANONEMPTYDIRECTION}">
      {else}
       <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $_change_}onclick = "activateCategory(this, '{$direction.id}')"{/if}>
      {/if}
     {else}
       <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" {if $_change_}onclick = "activateCategory(this, '{$direction.id}')"{/if}>
     {/if}
      </td>
     {if $_change_}
      <td class = "centerAlign">
       <a href = "{$smarty.server.PHP_SELF}?ctg=directions&edit_direction={$direction.id}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
      {if $direction.lessons > 0 || $direction.courses > 0}
       <img class = "ajaxHandle inactiveImage" src = "images/16x16/error_delete.png" title = "{$smarty.const._YOUCANNOTDELETEANONEMPTYDIRECTION}" alt = "{$smarty.const._YOUCANNOTDELETEANONEMPTYDIRECTION}" onclick = "alert('{$smarty.const._YOUCANNOTDELETEANONEMPTYDIRECTION}')"/>
      {else}
       <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEDIRECTION}')) deleteCategory(this, '{$direction.id}')"/>
      {/if}
      </td>
     {/if}
     </tr>
   {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
    </table>
  {/capture}
  {eF_template_printBlock title = $smarty.const._UPDATEDIRECTIONS data = $smarty.capture.t_directions_code image = '32x32/categories.png' help = 'Categories'}
 {/if}


 </td></tr>
{/capture}
