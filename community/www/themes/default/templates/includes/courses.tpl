{*moduleCourses: The Courses list*}
 {capture name = "moduleCourses"}
     <tr><td class = "moduleCell">
    {if $smarty.get.add_course || $smarty.get.edit_course}
  {capture name = "t_course_form_code"}
   <table width = "100%">
             <tr><td class = "topAlign" width = "50%">
     {$T_COURSE_FORM.javascript}
                    <form {$T_COURSE_FORM.attributes}>
                     {$T_COURSE_FORM.hidden}
                        <table class = "formElements">
                         <tr><td class = "labelCell">{$T_COURSE_FORM.name.label}:&nbsp;</td>
                             <td>{$T_COURSE_FORM.name.html}</td></tr>
                            {if $T_COURSE_FORM.name.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.name.error}</td></tr>{/if}
      {if isset($T_COURSE_FORM.languages_NAME.label)}
                            <tr><td class = "labelCell">{$T_COURSE_FORM.languages_NAME.label}:&nbsp;</td>
                             <td>{$T_COURSE_FORM.languages_NAME.html}</td></tr>
                            {if $T_COURSE_FORM.languages_NAME.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.languages_NAME.error}</td></tr>{/if}
                        {/if}
       <tr><td class = "labelCell">{$T_COURSE_FORM.directions_ID.label}:&nbsp;</td>
                             <td>{$T_COURSE_FORM.directions_ID.html}</td></tr>

{*
                        <tr><td class = "labelCell">{$T_COURSE_FORM.location.label}:&nbsp;</td>
                            <td class = "elementCell">{$T_COURSE_FORM.location.html}&nbsp;
                            {if $smarty.session.employee_type != _EMPLOYEE}
                            <a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&add_branch=1&returntab=basic" title = "{$smarty.const._NEWBRANCH}" ><img src = "images/16x16/add.png" title = "{$smarty.const._NEWBRANCH}" alt = "{$smarty.const._NEWBRANCH}" ></a></td>
       {/if}
                            </td></tr>
*}

                            {if $T_COURSE_FORM.directions_ID.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.directions_ID.error}</td></tr>{/if}
                            <tr><td class = "labelCell">{$T_COURSE_FORM.active.label}:&nbsp;</td>
                             <td class = "elementCell">{$T_COURSE_FORM.active.html}</td></tr>
                            {if $T_COURSE_FORM.active.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.active.error}</td></tr>{/if}
                            <tr><td class = "labelCell">{$T_COURSE_FORM.show_catalog.label}:&nbsp;</td>
                                <td class = "elementCell">{$T_COURSE_FORM.show_catalog.html}</td></tr>
                            {if $T_COURSE_FORM.show_catalog.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.show_catalog.error}</td></tr>{/if}
                            <tr><td class = "labelCell">{$T_COURSE_FORM.price.label}:&nbsp;</td>
                             <td>{$T_COURSE_FORM.price.html} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</td></tr>
                            {if $T_COURSE_FORM.price.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.price.error}</td></tr>{/if}
                            <tr><td></td>
                             <td class = "submitCell">{$T_COURSE_FORM.submit_course.html}</td></tr>
                        </table>
                    </form>
                </td></tr>
            </table>
        {/capture}
  {capture name = 't_lessons_to_courses_code'}
<!--ajax:lessonsTable-->
        <table style = "width:100%" class = "sortedTable" size = "{$T_LESSONS_SIZE}" sortBy = "0" useAjax = "1" id = "lessonsTable" rowsPerPage = "20" url = "administrator.php?ctg=courses&edit_course={$smarty.get.edit_course}&">
            <tr class = "topTitle">
                <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                <td class = "topTitle" name = "course_only">{$smarty.const._COURSEONLY}</td>
                <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                <td class = "topTitle" name = "directionsPath">{$smarty.const._DIRECTION}</td>
                <td class = "topTitle centerAlign" name = "course_assigned" >{$smarty.const._SELECT}</td>
            </tr>
  {foreach name = 'lessons_list2' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
            <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
                <td>{$lesson.name}</td>
                <td>{if $lesson.course_only}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
                <td>{$lesson.languages_NAME}</td>
                <td>{$lesson.directionsPath}</td>
                <td class = "centerAlign">
            {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                    <input type = "checkbox" id = "{$lesson.id}" onclick = "lessonsAjaxPost('{$lesson.id}', this);" {if $lesson.course_assigned == $lesson.id}checked{/if}>{if $lesson.course_assigned == $lesson.id}<span style = "display:none">checked</span>{/if} {*Span is for sorting here*}
            {else}
          {if $lesson.course_assigned == $lesson.id}<img src = "images/16x16/success.png" alt = "{$smarty.const._COURSELESSON}" title = "{$smarty.const._COURSELESSON}"><span style = "display:none">checked</span>{/if}
            {/if}
                </td>
  {foreachelse}
         <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
        </table>
<!--/ajax:lessonsTable-->
        {/capture}
                                {capture name = 't_users_to_courses_code'}
<!--ajax:usersTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$smarty.get.edit_course}&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
                                                            <td class = "topTitle" name = "role">{$smarty.const._USERROLE}</td>
                                                            <td class = "topTitle centerAlign" name = "in_course">{$smarty.const._CHECK}</td>
                                                        </tr>
                                {foreach name = 'users_to_lessons_list' key = 'login' item = 'user' from = $T_ALL_USERS}
                                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                                            <td>#filter:login-{$user.login}#</td>
                                                            <td>
                                    {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                                <select name="type_{$user.login}" id = "type_{$user.login}" onchange = "$('checked_{$user.login}').checked=true;usersAjaxPost('{$user.login}', this);">
                                        {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES}
                                                                    <option value="{$role_key}" {if !$user.role && $user.basic_user_type == $role_key}selected{elseif ($user.role == $role_key)}selected{/if} {if $user.user_types_ID == $role_key || $user.basic_user_type == $role_key}style = "font-weight:bold"{/if}>{$role_item}</option>
                                        {/foreach}
                                                                </select>
                                    {else}
                                                                {$T_ROLES[$user.user_type]}
                                    {/if}
                                                            </td>
                                                            <td class = "centerAlign">
                                                        {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                                <input class = "inputCheckbox" type = "checkbox" name = "checked_{$login}" id = "checked_{$login}" onclick = "usersAjaxPost('{$login}', this);" {if in_array($login, $T_COURSE_USERS, true)}checked = "checked"{/if} />{if in_array($login, $T_COURSE_USERS, true)}<span style = "display:none">checked</span>{/if}
                                                        {else}
                                                                {if in_array($login, $T_COURSE_USERS, true)}<img src = "images/16x16/success.png" alt = "{$smarty.const._COURSEUSER}" title = "{$smarty.const._COURSEUSER}"><span style = "display:none">checked</span>{/if}
                                                        {/if}
                                                            </td>
                                                    </tr>
                                {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                {/foreach}
                                </table>
<!--/ajax:usersTable-->
                                {/capture}
       {capture name = 't_course_code'}
       <div class = "tabber">
        {eF_template_printBlock tabber = "courses" title ="`$smarty.const._EDITCOURSE`" data = $smarty.capture.t_course_form_code image = '32x32/courses.png'}
                {if $smarty.get.edit_course}
                 <script>var editCourse = '{$smarty.get.edit_course}';</script>
         {eF_template_printBlock tabber = "lessons" title ="`$smarty.const._EDITLESSONSCOURSE`" data = $smarty.capture.t_lessons_to_courses_code image = '32x32/lessons.png'}
         {eF_template_printBlock tabber = "users" title ="`$smarty.const._EDITUSERSCOURSE`" data = $smarty.capture.t_users_to_courses_code image = '32x32/users.png'}
                   {/if}
       </div>
               {/capture}
               {if $smarty.get.add_course}
                   {eF_template_printBlock title = $smarty.const._NEWCOURSEOPTIONS data = $smarty.capture.t_course_code image = '32x32/courses.png'}
               {else}
                   {eF_template_printBlock title ="`$smarty.const._COURSEOPTIONSFOR` <span class = 'innerTableName'>&quot;`$T_EDIT_COURSE->course.name`&quot;</span>" data = $smarty.capture.t_course_code image = '32x32/courses.png' options = $T_COURSE_OPTIONS}
               {/if}
                    {elseif $smarty.get.course}
                            {include file = "includes/course_settings.tpl"}
                    {else}
      {capture name = 't_courses_code'}
                         <script>var activate = '{$smarty.const._ACTIVATE}';var deactivate = '{$smarty.const._DEACTIVATE}';</script>
                            {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
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
                                                    {assign var = "change_courses" value = 1}
                                        {/if}
                                                    <table border = "0" width = "100%" class = "sortedTable" sortBy = "0">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle">{$smarty.const._NAME} </td>
               <td class = "topTitle">{$smarty.const._DIRECTION}</td>
                                                            <td class = "topTitle">{$smarty.const._LANGUAGE}</td>
               <td class = "topTitle centerAlign" name = "students">{$smarty.const._PARTICIPATION}</td>
                                                            <td class = "topTitle centerAlign">{$smarty.const._LESSONS}</td>
                                                            <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                                            <td class = "topTitle" name = "created">{$smarty.const._CREATED}</td>
               <td class = "topTitle centerAlign">{$smarty.const._ACTIVE2}</td>
                                                            <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                                                        </tr>
                                        {foreach name = 'courses_list' key = 'key' item = 'course' from = $T_COURSES_DATA}
                                                        <tr id="row_{$course.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$course.active}deactivatedTableElement{/if}">
                                                            <td class = "editLink"><span style = "display:none">{$course.name}</span>{$course.link}</td>
                                                            <td>{$course.directionsPath}</td>
               <td>{$course.languages_NAME}</td>
               <td class = "centerAlign">{if $course.max_users}{$course.students}/{$course.max_users}{else}{$course.students}{/if}</td>
                                                            <td class = "centerAlign">{$course.lessons_num}</td>
                                                            <td class = "centerAlign">{if $course.price == 0}{$smarty.const._FREECOURSE}{else}{$course.price_string}{/if}</td>
                                                            <td>#filter:timestamp-{$course.created}#</td>
               <td class = "centerAlign"">
                                                            {if $course.active == 1}
                                                                <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $change_courses}onclick = "activateCourse(this, '{$course.id}')"{/if}>
                                                            {else}
                                                                <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" {if $change_courses}onclick = "activateCourse(this, '{$course.id}')"{/if}>
                                                            {/if}
                                                            </td>
                                                            <td class = "centerAlign">
                {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                 <a href="administrator.php?ctg=statistics&option=course&sel_course={$course.id}"><img border = "0" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a>
                {/if}
                                                                <a href = "administrator.php?ctg=courses&course={$course.id}&op=course_info"><img border = "0" src = "images/16x16/generic.png" title = "{$smarty.const._COURSEINFORMATION}" alt = "{$smarty.const._COURSEINFORMATION}" /></a>
                                            {if $change_courses}
                                                                <a href = "administrator.php?ctg=courses&edit_course={$course.id}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                                <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETECOURSE}')) deleteCourse(this, '{$course.id}');"/>
                                            {/if}
                                                            </td>
                                                        </tr>
                                        {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                        {/foreach}
                                                    </table>
                                    {/capture}
                                    {eF_template_printBlock title = $smarty.const._UPDATECOURSES data = $smarty.capture.t_courses_code image = '32x32/courses.png' help = 'Courses'}
    {/if}
                            </td></tr>
    {/capture}
