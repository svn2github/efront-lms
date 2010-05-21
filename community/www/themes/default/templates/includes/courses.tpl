{capture name = "moduleCourses"}
 <tr><td class = "moduleCell">

 {if $smarty.get.course}
  {include file = "includes/course_settings.tpl"}

 {elseif $smarty.get.add_course || $smarty.get.edit_course}
  {capture name = "t_course_form_code"}
   {$T_COURSE_FORM.javascript}
   <form {$T_COURSE_FORM.attributes}>
    {$T_COURSE_FORM.hidden}
    <table class = "formElements">
{* <tr><td class = "labelCell">{$T_COURSE_FORM.course_code.label}:&nbsp;</td>
      <td class = "elementCell">{$T_COURSE_FORM.course_code.html}</td></tr>*}
     <tr><td class = "labelCell">{$T_COURSE_FORM.name.label}:&nbsp;</td>
      <td class = "elementCell">{$T_COURSE_FORM.name.html}</td></tr>
     <tr><td class = "labelCell">{$T_COURSE_FORM.directions_ID.label}:&nbsp;</td>
      <td class = "elementCell">{$T_COURSE_FORM.directions_ID.html}</td></tr>
   {if !$T_CONFIGURATION.onelanguage}
     <tr><td class = "labelCell">{$T_COURSE_FORM.languages_NAME.label}:&nbsp;</td>
      <td class = "elementCell">{$T_COURSE_FORM.languages_NAME.html}</td></tr>
   {/if}
     <tr><td class = "labelCell">{$T_COURSE_FORM.active.label}:&nbsp;</td>
      <td class = "elementCell">{$T_COURSE_FORM.active.html}</td></tr>
     <tr><td class = "labelCell">{$T_COURSE_FORM.show_catalog.label}:&nbsp;</td>
      <td class = "elementCell">{$T_COURSE_FORM.show_catalog.html}</td></tr>
     <tr><td class = "labelCell">{$T_COURSE_FORM.price.label}:&nbsp;</td>
      <td class = "elementCell">{$T_COURSE_FORM.price.html} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</td></tr>
     <tr><td></td>
      <td class = "submitCell">{$T_COURSE_FORM.submit_course.html}</td></tr>
    </table>
   </form>
  {/capture}
  {capture name = 't_lessons_to_courses_code'}
  {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'lessonsTable'}
<!--ajax:lessonsTable-->
   <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "3" order = "desc" useAjax = "1" id = "lessonsTable" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$smarty.get.edit_course}&">
    <tr class = "topTitle defaultRowHeight">
     <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
     <td class = "topTitle noSort">{$smarty.const._DIRECTION}</td>
     <td class = "topTitle" name = "created">{$smarty.const._CREATED}</td>
     <td class = "topTitle noSort">{$smarty.const._MODE}</td>
     <td class = "topTitle centerAlign" name = "has_lesson" >{$smarty.const._SELECT}</td>
    </tr>
   {foreach name = 'lessons_list2' key = 'key' item = 'lesson' from = $T_DATA_SOURCE}
    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
     <td>{$lesson.name}</td>
     <td>{$lesson.directionsPath}</td>
     <td>#filter:timestamp-{$lesson.created}#</td>
     <td><select id = "lesson_mode_{$lesson.id}" name = "lesson_mode" onchange = "setLessonMode(this, '{$lesson.id}', this.options[this.options.selectedIndex].value)" {if !$lesson.has_lesson}style = "display:none"{/if}>
       <option value = "shared" {if $lesson.mode == "shared"}selected{/if}>{$smarty.const._SHARED}</option>
       <option value = "unique" {if $lesson.mode == "unique"}selected{/if}>{$smarty.const._UNIQUE}</option>
      </select></td>
     <td class = "centerAlign">
    {if $_change_}
      <input type = "checkbox" id = "{$lesson.id}" onclick = "lessonsAjaxPost('{$lesson.id}', this);" {if $lesson.has_lesson}checked{/if}>{if $lesson.has_lesson}<span style = "display:none">checked</span>{/if} {*Span is for sorting here*}
    {else}
     {if $lesson.has_lesson}<img src = "images/16x16/success.png" alt = "{$smarty.const._COURSELESSON}" title = "{$smarty.const._COURSELESSON}"><span style = "display:none">checked</span>{/if}
    {/if}
     </td>
    </tr>
   {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
   </table>
<!--/ajax:lessonsTable-->
  {/if}
  {/capture}
  {capture name = 't_users_to_courses_code'}
  <script>translationsToJS['_USERHASTHECOURSE'] = '{$smarty.const._USERHASTHECOURSE}'; translationsToJS['_APPLICATIONPENDING'] = '{$smarty.const._APPLICATIONPENDING}';</script>
  {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'usersTable'}
<!--ajax:usersTable-->
   <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "6" order="desc" useAjax = "1" id = "usersTable" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" {if isset($T_BRANCHES_FILTER)}branchFilter="{$T_BRANCHES_FILTER}"{/if} {if isset($T_JOBS_FILTER)}jobFilter="{$T_JOBS_FILTER}"{/if} url = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$smarty.get.edit_course}&">
    <tr class = "topTitle">
     <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
     <td class = "topTitle" name = "role">{$smarty.const._USERROLE}</td>
           <td class = "topTitle" name = "active_in_course" style = "width:10%">{$smarty.const._ENROLLEDON}</td>
           <td class = "topTitle" name = "timestamp_completed" style = "width:10%">{$smarty.const._COMPLETEDON}</td>
           <td class = "topTitle centerAlign" name = "from_timestamp" style = "width:5%">{$smarty.const._STATUS}</td>
           <td class = "topTitle centerAlign" name = "completed" style = "width:5%">{$smarty.const._COMPLETED}</td>
           <td class = "topTitle centerAlign" name = "score" style = "width:5%">{$smarty.const._SCORE}</td>
     <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
     <td class = "topTitle centerAlign" name = "active_in_course">{$smarty.const._CHECK}</td>
    </tr>
   {foreach name = 'users_to_lessons_list' key = 'login' item = 'user' from = $T_DATA_SOURCE}
    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
     <td><a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}" class = "editLink">#filter:login-{$user.login}#</a></td>
     <td>
    {if $_change_}
      <select name="type_{$user.login}" id = "type_{$user.login}" onchange = "$('checked_{$user.login}').checked=true;usersAjaxPost('{$user.login}', this);">
     {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES}
       <option value="{$role_key}" {if !$user.role && $user.user_type == $role_key}selected{elseif ($user.role == $role_key)}selected{/if} {if $user.user_types_ID == $role_key || $user.user_type == $role_key}style = "font-weight:bold"{/if}>{$role_item}</option>
     {/foreach}
      </select>
    {else}
      {$T_ROLES[$user.user_type]}
    {/if}
     </td>
     <td style = "white-space:nowrap">#filter:timestamp-{$user.active_in_course}#</td>
     <td style = "white-space:nowrap">#filter:timestamp-{$user.timestamp_completed}#</td>
           <td class = "centerAlign">
     {if $user.active_in_course}
               <img class = "ajaxHandle" src = "images/16x16/success.png" title = "{$smarty.const._USERHASTHECOURSE}" alt = "{$smarty.const._USERHASTHECOURSE}" onclick = "unConfirmUser(this, '{$user.login}')"/>
                 {elseif !$user.active_in_course|@is_null}
               <img class = "ajaxHandle" src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}" onclick = "confirmUser(this, '{$user.login}')"/>
              {/if}
           </td>
           <td class = "centerAlign">
     {if !$user.active_in_course|@is_null}
      {if $T_BASIC_ROLES_ARRAY[$user.user_type] == 'student'}{if $user.completed}<img src = "images/16x16/success.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}">{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">{/if}{/if}
     {/if}
     </td>
           <td class = "centerAlign">
     {if !$user.active_in_course|@is_null}
      {if $T_BASIC_ROLES_ARRAY[$user.user_type] == 'student'}#filter:score-{$user.score}#%{/if}
     {/if}
     </td>
     <td class = "centerAlign">
     </td>
     <td class = "centerAlign">
    {if $_change_}
      <input class = "inputCheckbox" type = "checkbox" name = "checked_{$login}" id = "checked_{$login}" onclick = "usersAjaxPost('{$login}', this);" {if !$user.active_in_course|@is_null}checked = "checked"{/if} />{if !$user.active_in_course|@is_null}<span style = "display:none">checked</span>{/if}
    {else}
      {if !$user.active_in_course|@is_null}<img src = "images/16x16/success.png" alt = "{$smarty.const._COURSEUSER}" title = "{$smarty.const._COURSEUSER}"><span style = "display:none">checked</span>{/if}
    {/if}
     </td>
    </tr>
   {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
   </table>
<!--/ajax:usersTable-->
  {/if}
  {/capture}
  {capture name = 't_course_code'}
   {if $T_EDIT_COURSE->course.instance_source}
    {assign var = "edit_block_title" value = $smarty.const._EDITCOURSEINSTANCE}
    {assign var = "lessons_block_title" value = $smarty.const._EDITLESSONSCOURSEINSTANCE}
    {assign var = "users_block_title" value = $smarty.const._EDITUSERSCOURSEINSTANCE}
    {assign var = "skills_block_title" value = $smarty.const._SKILLSOFFEREDINSTANCE}
    {assign var = "course_options_title" value = $smarty.const._COURSEOPTIONSFORINSTANCE}
   {else}
    {assign var = "edit_block_title" value = $smarty.const._EDITCOURSE}
    {assign var = "lessons_block_title" value = $smarty.const._EDITLESSONSCOURSE}
    {assign var = "users_block_title" value = $smarty.const._EDITUSERSCOURSE}
    {assign var = "skills_block_title" value = $smarty.const._SKILLSOFFERED}
    {assign var = "course_options_title" value = $smarty.const._COURSEOPTIONSFOR}
   {/if}
   {if $T_COURSE_INSTANCES}
   <div class = "headerTools" style = "float:right">
    {$smarty.const._JUMPTO}:
    <select onchange = "if (sel = this.options[this.options.selectedIndex].value) location='{$smarty.server.PHP_SELF}?ctg=courses&edit_course='+sel">
     <option value = "{$T_INSTANCE_SOURCE}">{$smarty.const._PARENTCOURSE}</option>
     <option value = "">----------------</option>
     {foreach name = 't_course_instances_list' item = "item" key = "key" from = $T_COURSE_INSTANCES}
     <option value = "{$key}" {if $item->course.id==$T_EDIT_COURSE->course.id}selected{/if}>{$item->course.name}</option>
     {/foreach}
    </select>
   </div>
   {/if}
   <div class = "tabber">
    {eF_template_printBlock tabber = "courses" title ="`$edit_block_title`" data = $smarty.capture.t_course_form_code image = '32x32/courses.png'}
   {if $smarty.get.edit_course}
    <script>var editCourse = '{$smarty.get.edit_course}';</script>
    {eF_template_printBlock tabber = "lessons" title ="`$lessons_block_title`" data = $smarty.capture.t_lessons_to_courses_code image = '32x32/lessons.png'}
    {eF_template_printBlock tabber = "users" title ="`$users_block_title`" data = $smarty.capture.t_users_to_courses_code image = '32x32/users.png'}
   {/if}
   </div>
  {/capture}
  {if $smarty.get.add_course}
   {eF_template_printBlock title = $smarty.const._NEWCOURSEOPTIONS data = $smarty.capture.t_course_code image = '32x32/courses.png'}
  {else}
   {eF_template_printBlock title ="`$course_options_title` <span class = 'innerTableName'>&quot;`$T_EDIT_COURSE->course.name`&quot;</span>" data = $smarty.capture.t_course_code image = '32x32/courses.png' options = $T_COURSE_OPTIONS}
  {/if}
 {else} {*Main Courses List*}
  {capture name = 't_courses_code'}
   <script>translationsToJS['_ACTIVATE'] = '{$smarty.const._ACTIVATE}'; translationsToJS['_DEACTIVATE'] = '{$smarty.const._DEACTIVATE}';</script>
   {if $_change_}
    <div class = "headerTools">
     <span>
      <img src = "images/16x16/add.png" title = "{$smarty.const._NEWCOURSE}" alt = "{$smarty.const._NEWCOURSE}">
      <a href = "{$smarty.server.PHP_SELF}?ctg=courses&add_course=1" title = "{$smarty.const._NEWCOURSE}" >{$smarty.const._NEWCOURSE}</a>
     </span>
     <span>
      <img src = "images/16x16/import.png" title = "{$smarty.const._IMPORTCOURSE}" alt = "{$smarty.const._IMPORTCOURSE}">
      <a href = "javascript:void(0)" title = "{$smarty.const._IMPORTCOURSE}" onclick = "eF_js_showDivPopup('', 0, 'import_course_popup')">{$smarty.const._IMPORTCOURSE}</a></a>
     </span>
    </div>
    <div id = "import_course_popup" style = "display:none">
     {capture name = "t_import_course_code"}
      {$T_IMPORT_COURSE_FORM.javascript}
      <form {$T_IMPORT_COURSE_FORM.attributes}>
      {$T_IMPORT_COURSE_FORM.hidden}
      <table class = "formElements">
       <tr><td class = "labelCell">{$T_IMPORT_COURSE_FORM.import_content.label}:&nbsp;</td>
        <td class = "elementCell">{$T_IMPORT_COURSE_FORM.import_content.html}</td></tr>
       <tr><td></td>
        <td class = "submitCell">{$T_IMPORT_COURSE_FORM.submit_course.html}</td></tr>
      </table>
      </form>
     {/capture}
     {eF_template_printBlock title = $smarty.const._IMPORTCOURSE data = $smarty.capture.t_import_course_code image = '32x32/import.png'}
    </div>
   {/if}
  {assign var = "courses_url" value = "`$smarty.server.PHP_SELF`?ctg=courses&"}
  {assign var = "_change_handles_" value = $_change_}
  {include file = "includes/common/courses_list.tpl"}
  {/capture}
  {eF_template_printBlock title = $smarty.const._UPDATECOURSES data = $smarty.capture.t_courses_code image = '32x32/courses.png' help = 'Courses'}
 {/if}
 </td></tr>
{/capture}
