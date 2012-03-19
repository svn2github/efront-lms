{if $smarty.get.add_lesson || $smarty.get.edit_lesson}
{*moduleNewLessonDirection: Create a new direction or lesson forms*}
 {capture name = "moduleNewLessonDirection"}
 <tr><td class = "moduleCell">
  {capture name = 't_lesson_code'}
  <script>var editLesson = '{$smarty.get.edit_lesson}';</script>
   {capture name = 't_edit_lesson_code'}
   <table width = "100%">
    <tr><td class = "topAlign" width = "50%">
     {$T_LESSON_FORM.javascript}
     <form {$T_LESSON_FORM.attributes}>
     {$T_LESSON_FORM.hidden}
     <table class = "formElements">
      <tr><td class = "labelCell">{$T_LESSON_FORM.name.label}:&nbsp;</td>
       <td>{$T_LESSON_FORM.name.html}</td></tr>
      {if isset($T_LESSON_FORM.languages_NAME.label)}
      <tr><td class = "labelCell">{$T_LESSON_FORM.languages_NAME.label}:&nbsp;</td>
       <td>{$T_LESSON_FORM.languages_NAME.html}</td></tr>
      {/if}
      <tr><td class = "labelCell">{$T_LESSON_FORM.directions_ID.label}:&nbsp;</td>
       <td>{$T_LESSON_FORM.directions_ID.html}</td></tr>
      <tr><td class = "labelCell">{$T_LESSON_FORM.course_only.1.label}:&nbsp;</td>
       <td>{$T_LESSON_FORM.course_only.1.html}</td></tr>
    {if $T_CONFIGURATION.lesson_enroll || (isset($smarty.get.edit_lesson) && !$T_EDIT_LESSON->lesson.course_only)}
      <tr><td class = "labelCell"></td>
       <td>{$T_LESSON_FORM.course_only.0.html}</td></tr>
    {/if}
      <tr><td class = "labelCell">{$T_LESSON_FORM.active.label}:&nbsp;</td>
       <td class = "elementCell">{$T_LESSON_FORM.active.html}</td></tr>
      <tr class = "only_lesson" {if !$T_EDIT_LESSON || $T_EDIT_LESSON->lesson.course_only}style = "display:none"{/if}><td class = "labelCell">{$T_LESSON_FORM.show_catalog.label}:&nbsp;</td>
       <td class = "elementCell">{$T_LESSON_FORM.show_catalog.html}</td></tr>
    {if $T_CONFIGURATION.disable_payments != 1}
      <tr id = "price_row" class = "only_lesson" {if !$T_EDIT_LESSON || $T_EDIT_LESSON->lesson.course_only}style = "display:none"{/if}><td class = "labelCell">{$T_LESSON_FORM.price.label}:&nbsp;</td>
       <td>{$T_LESSON_FORM.price.html} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</td></tr>
    {/if}
      <tr><td></td>
       <td class = "submitCell">{$T_LESSON_FORM.submit_lesson.html}</td></tr>
     </table>
     </form>
    </td></tr>
   </table>
   {/capture}
  <div class = "tabber">
   {eF_template_printBlock tabber="lessons" title = "`$smarty.const._EDITLESSON`" data = $smarty.capture.t_edit_lesson_code image = '32x32/lessons.png'}
   {capture name = 't_users_to_lessons_code'}
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/success.png" title = "{$smarty.const._SETALLUSERSSTATUSCOMPLETED}" alt = "{$smarty.const._SETALLUSERSSTATUSCOMPLETED}"/>
     <a href = "javascript:void(0)" {if !$T_CURRENT_USER->coreAccess.lessons == 'change' || $T_CURRENT_USER->coreAccess.lessons == 'change'} onclick = "setAllUsersStatusCompleted(this)" {/if}>{$smarty.const._SETALLUSERSSTATUSCOMPLETED}</a>
    </span>
   </div>
<!--ajax:usersTable-->
   <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "4" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" {if isset($T_BRANCHES_FILTER)}branchFilter="{$T_BRANCHES_FILTER}"{/if} {if isset($T_JOBS_FILTER)}jobFilter="{$T_JOBS_FILTER}"{/if} url = "{$smarty.server.PHP_SELF}?ctg=professor_lessons&edit_lesson={$smarty.get.edit_lesson}&">
    <tr class = "topTitle">
     <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
     <td class = "topTitle" name = "role">{$smarty.const._USERROLEINLESSON}</td>
     <td class = "topTitle centerAlign" name = "completed">{$smarty.const._COMPLETED}</td>
     <td class = "topTitle noSort centerAlign">{$smarty.const._OPERATIONS}</td>
     <td class = "topTitle centerAlign" name = "partof">{$smarty.const._CHECK}</td>
    </tr>
    {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_ALL_USERS}
    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
     <td>#filter:login-{$user.login}#</td>
     <td>
    {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons== 'change'}
      <select name="type_{$user.login}" id = "type_{$user.login}" onchange = "$('checked_{$user.login}').checked=true;ajaxPost('{$user.login}', this);">
    {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES}
       <option value="{$role_key}" {if ($user.role == $role_key)}selected{/if} {if $user.basic_user_type == $role_key}style = "font-weight:bold"{/if}>{$role_item}</option>
    {/foreach}
      </select>
    {else}
      {$T_ROLES[$user.role]}
    {/if}
     </td>
     <td class = "centerAlign">{if $user.partof}{if $user.completed}<img src = "images/16x16/success.png" alt = "{$smarty.const._COMPLETEDON} #filter:timestamp-{$user.timestamp_completed}#" title = "{$smarty.const._COMPLETEDON} #filter:timestamp-{$user.timestamp_completed}#"/>{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NOTCOMPLETED}" title = "{$smarty.const._NOTCOMPLETED}"/>{/if}{/if}</td>
     <td class = "centerAlign">
    {if $user.basic_user_type == 'student' && in_array($user.login, $T_LESSON_USERS)}
       <img class = "ajaxHandle" src="images/16x16/refresh.png" title="{$smarty.const._RESETPROGRESSDATA}" alt="{$smarty.const._RESETPROGRESSDATA}" onclick = "if (confirm(translations['_IRREVERSIBLEACTIONAREYOUSURE'])) resetProgress(this, '{$user.login}');">
    {/if}
     </td>
     <td class = "centerAlign">
    {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons== 'change'}
      <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this);" {if in_array($user.login, $T_LESSON_USERS)}checked = "checked"{/if} />{if in_array($user.login, $T_LESSON_USERS)}<span style = "display:none">checked</span>{/if} {*Text for sorting*}
    {else}
      {if in_array($user.login, $T_LESSON_USERS)}<img src = "images/16x16/success.png" alt = "{$smarty.const._LESSONUSER}" title = "{$smarty.const._LESSONUSER}"><span style = "display:none">checked</span>{/if}
    {/if}
     </td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
<!--/ajax:usersTable-->
  {/capture}
        {if $smarty.get.edit_lesson && !$T_EDIT_LESSON->lesson.course_only}
        <div class="tabbertab {if $smarty.get.tab=='users'}tabbertabdefault{/if}">
         <h3>{$smarty.const._EDITUSERSLESSON}</h3>
         {eF_template_printBlock title = $smarty.const._UPDATEUSERSTOLESSONS data = $smarty.capture.t_users_to_lessons_code image = '32x32/users.png'}
        </div>
        {/if}
          </div>
       {/capture}
   {if $smarty.get.add_lesson}
     {eF_template_printBlock title = $smarty.const._NEWLESSONOPTIONS data = $smarty.capture.t_lesson_code image = '32x32/lessons.png'}
   {else}
     {eF_template_printBlock title = "`$smarty.const._LESSONOPTIONSFOR` <span class = 'innerTableName'>&quot;`$T_LESSON_FORM.name.value`&quot;</span>" data = $smarty.capture.t_lesson_code image = '32x32/lessons.png' options = $T_LESSON_OPTIONS}
   {/if}
       </td></tr>
 {/capture}
 {else}
 {*moduleLessons: The lessons list*}
  {capture name = "moduleLessons"}
     {if $smarty.get.lesson_info}
      {include file = "includes/lesson_information.tpl"}
      {$smarty.capture.moduleLessonInformation}
     {elseif $smarty.get.lesson_settings}
       <tr><td class = "moduleCell">
      {include file = "includes/lesson_settings.tpl"}
          </td></tr>
     {else}
       <tr><td class = "moduleCell">
       <script>var activate = '{$smarty.const._ACTIVATE}';var deactivate = '{$smarty.const._DEACTIVATE}';var courseonly = '{$smarty.const._COURSEONLY}';var directly = '{$smarty.const._DIRECTLY}';</script>
      {capture name = 't_lessons_code'}
       {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons== 'change'}
        <div class = "headerTools">
         <span>
          <img src = "images/16x16/add.png" title = "{$smarty.const._NEWLESSON}" alt = "{$smarty.const._NEWLESSON}">
          <a href = "{$smarty.server.PHP_SELF}?ctg=professor_lessons&add_lesson=1" title = "{$smarty.const._NEWLESSON}" >{$smarty.const._NEWLESSON}</a>
         </span>
         <span>
          <img src = "images/16x16/import.png" title = "{$smarty.const._IMPORTLESSON}" alt = "{$smarty.const._IMPORTLESSON}">
          <a href = "javascript:void(0)" title = "{$smarty.const._IMPORTLESSON}" onclick = "eF_js_showDivPopup('', 0, 'import_lesson_popup')">{$smarty.const._IMPORTLESSON}</a></a>
         </span>
        </div>
        <div id = "import_lesson_popup" style = "display:none">
         {capture name = "t_import_lesson_code"}
          {$T_IMPORT_LESSON_FORM.javascript}
          <form {$T_IMPORT_LESSON_FORM.attributes}>
          {$T_IMPORT_LESSON_FORM.hidden}
          <table class = "formElements">
           <tr><td class = "labelCell">{$T_IMPORT_LESSON_FORM.import_content.label}:&nbsp;</td>
            <td class = "elementCell">{$T_IMPORT_LESSON_FORM.import_content.html}</td></tr>
           <tr><td></td>
            <td class = "infoCell">{$smarty.const._FILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILE_SIZE}</b> {$smarty.const._KB}</td></tr>
           <tr><td></td>
            <td class = "submitCell">{$T_IMPORT_LESSON_FORM.submit_lesson.html}</td></tr>
          </table>
          </form>
         {/capture}
         {eF_template_printBlock title = $smarty.const._IMPORTLESSON data = $smarty.capture.t_import_lesson_code image = '32x32/import.png'}
        </div>
        {assign var = "change_lessons" value = 1}
       {/if}
<!--ajax:lessonsTable-->
        <table style = "width:100%" class = "sortedTable" activeFilter = 1 size = "{$T_LESSONS_SIZE}" sortBy = "0" useAjax = "1" id = "lessonsTable" rowsPerPage = "20" url = "{$smarty.server.PHP_SELF}?ctg=professor_lessons&">
         <tr class = "topTitle">
          <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
          <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
          <td class = "topTitle centerAlign" name = "students">{$smarty.const._PARTICIPATION}</td>
          <td class = "topTitle centerAlign" name = "course_only">{$smarty.const._AVAILABLE}</td>
         {if $T_CONFIGURATION.disable_payments != 1}
          <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
         {/if}
          <td class = "topTitle" name = "created">{$smarty.const._CREATED}</td>
          <td class = "topTitle centerAlign" name = "active">{$smarty.const._ACTIVE2}</td>
          <td class = "topTitle noSort centerAlign">{$smarty.const._OPERATIONS}</td>
         </tr>
     {foreach name = 'lessons_list2' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
         <tr id = "row_{$lesson.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
          <td id = "column_{$lesson.id}" class = "editLink">
           <a class="editLink {if !$T_CONFIGURATION.disable_tooltip}info{/if}" url = "ask_information.php?lessons_ID={$lesson.id}&type=lesson" href= "{$smarty.server.PHP_SELF}?ctg=professor_lessons&edit_lesson={$lesson.id}">
            {$lesson.name}
           </a>
          <td>{$lesson.direction_name}</td>
          <td class = "centerAlign">{if !$lesson.course_only}{if $lesson.max_users}{$lesson.students}/{$lesson.max_users}{else}{$lesson.students}{/if}{else}-{/if}</td>
          <td class = "centerAlign">
         {if $lesson.course_only}
           <img class = "ajaxHandle" src = "images/16x16/courses.png" alt = "{$smarty.const._COURSEONLY}" title = "{$smarty.const._COURSEONLY}" {if $change_lessons}onclick = "setLessonAccess(this, '{$lesson.id}')"{/if}>
         {else}
           <img class = "ajaxHandle" src = "images/16x16/lessons.png" alt = "{$smarty.const._DIRECTLY}" title = "{$smarty.const._DIRECTLY}" {if $change_lessons}onclick = "setLessonAccess(this, '{$lesson.id}')"{/if}>
         {/if}
          </td>
         {if $T_CONFIGURATION.disable_payments != 1}
          <td class = "centerAlign">{if !$lesson.course_only}{if $lesson.price == 0}-{else}{$lesson.price_string}{/if}{else}-{/if}</td>
         {/if}
          <td>#filter:timestamp-{$lesson.created}#</td>
          <td class = "centerAlign">
         {if $lesson.active == 1}
           <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $change_lessons}onclick = "activateLesson(this, '{$lesson.id}');"{/if}>
         {else}
           <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" {if $change_lessons}onclick = "activateLesson(this, '{$lesson.id}')"{/if}>
         {/if}
          </td>
          <td class = "centerAlign" style = "white-space:nowrap">
         {if $change_lessons}
           <a href = "{$smarty.server.PHP_SELF}?ctg=professor_lessons&edit_lesson={$lesson.id}"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETELESSON}')) deleteLesson(this, '{$lesson.id}')"/>
         {/if}
          </td>
         </tr>
     {foreachelse}
        <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
     {/foreach}
        </table>
<!--/ajax:lessonsTable-->
         {/capture}
         {eF_template_printBlock title = $smarty.const._UPDATELESSONS data = $smarty.capture.t_lessons_code image = '32x32/lessons.png' help = 'Lessons'}
          </td></tr>
   {/if}
  {/capture}
 {/if}
