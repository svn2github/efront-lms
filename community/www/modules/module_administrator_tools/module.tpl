{*Smarty template*}

{capture name = 't_change_login_code'}
 {eF_template_printForm form = $T_TOOLS_FORM}
 <div id = "module_administrator_tools_autocomplete_users_div" class = "autocomplete"></div>
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_OTHEROPTIONS}</legend>
  <input type = "submit" onclick = "fixCase(this)" value = "{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_SYNCHRONIZECASE}" class = "flatButton">
 </fieldset>
{/capture}

{capture name = 't_global_settings_code'}
  {eF_template_printBlock title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_GLOBALLESSONSETTINGS columns = 4 links = $T_LESSON_SETTINGS image='32x32/lessons.png' main_options = $T_TABLE_OPTIONS groups = $T_LESSON_SETTINGS_GROUPS}
{/capture}

{capture name = 't_sql_code'}
 {eF_template_printForm form = $T_SQL_FORM}
 <div id = "sql_output_area" style = "width:100%;border:1px dotted black;height:400px">
 {if isset($T_SQL_RESULT)}
 <table>
 {foreach name = 'sql_results_loop' item = "row" key = "key" from = $T_SQL_RESULT}
  {if $smarty.foreach.sql_results_loop.first}
  <tr class = "topTitle" style = "border-top:0px">
  {else}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
  {/if}
   {foreach name = "row_loop" item = "column" key = "foo" from = $row}
    <td style = "padding:0px 3px 0px 3px">{$column}</td>
   {/foreach}
  </tr>
  {if $smarty.foreach.sql_results_loop.last}
  <tr><td colspan = "100%">{$smarty.foreach.sql_results_loop.total} {$smarty.const._MODULE_ADMINISTRATOR_TOOLS_ROWSINSET}</td></tr>
  {/if}
 {foreachelse}
   {if isset($T_SQL_AFFECTED_ROWS)}
    {$smarty.const._MODULE_ADMINISTRATOR_TOOLS_QUERYOK}, {$T_SQL_AFFECTED_ROWS} {$smarty.const._MODULE_ADMINISTRATOR_TOOLS_ROWSAFFECTED}
   {else}
    {$smarty.const._MODULE_ADMINISTRATOR_TOOLS_EMPTYSET}
   {/if}
 {/foreach}
 </table>
 {/if}
 </div>
{/capture}

{capture name = 't_set_course_users_code'}
            <table class = "statisticsTools statisticsSelectList" style = "margin-bottom:50px">
                <tr><td class = "labelCell">{$smarty.const._CHOOSELESSON}:</td>
                    <td class = "elementCell" colspan = "4">
                        <input type = "text" id = "autocomplete" class = "autoCompleteTextBox" value = "{$T_CURRENT_LESSON->lesson.name}"/>
                        <img id = "busy" src = "images/16x16/clock.png" style="display:none;" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}"/>
                        <div id = "module_administrator_tools_autocomplete_lessons_div" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr><td></td>
                 <td class = "infoCell" colspan = "4">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td></tr>
         </table>
{if $smarty.get.lessons_ID}

<!--ajax:usersTable-->

     <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_ADMINISTRATOR_TOOLS_BASEURL}&lessons_ID={$smarty.get.lessons_ID}&">
      <tr class = "topTitle">
       <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
       <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
       <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
       <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
       <td class = "topTitle" name = "role">{$smarty.const._USERROLEINLESSON}</td>
       <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
       <td class = "topTitle centerAlign" name = "has_lesson">{$smarty.const._STATUS}</td>
      </tr>
 {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
      <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
       <td>#filter:login-{$user.login}#</td>
       <td>{$user.name}</td>
       <td>{$user.surname}</td>
       <td>{$T_ROLES[$user.basic_user_type]}</td>
       <td>
  {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
        <select name="type_{$user.login}" id = "type_{$user.login}" onchange = "$('checked_{$user.login}').checked=true;ajaxPost('{$user.login}', this);">
   {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES}
         <option value="{$role_key}" {if !$user.role}{if $user.user_types_ID && $user.user_types_ID == $role_key}selected{elseif !$user.user_types_ID && $user.user_type == $role_key}selected{/if}{elseif ($user.role == $role_key)}selected{/if} {if $user.user_types_ID == $role_key || $user.user_type == $role_key}style = "font-weight:bold"{/if}>{$role_item}</option>
   {/foreach}
        </select>
  {else}
        {$T_ROLES[$user.role]}
  {/if}
       </td>
       <td class = "centerAlign">
       {if $user.basic_user_type == 'student'}
         <img class = "ajaxHandle" src="images/16x16/refresh.png" title="{$smarty.const._RESETPROGRESSDATA}" alt="{$smarty.const._RESETPROGRESSDATA}" onclick = "resetProgress(this, '{$user.login}');">
       {/if}
       </td>
       <td class = "centerAlign">
  {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
        <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this);" {if $user.has_lesson}checked = "checked"{/if} />
  {else}
         {if $user.has_lesson}<img src = "images/16x16/success.png" title = "{$smarty.const._LESSONUSER}" alt = "{$smarty.const._LESSONUSER}" >{/if}
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

{capture name = 't_unenroll_courses_code'}
<table class = "formElements">
 <tr><td class = "labelCell">{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_ENTITYTYPE}:&nbsp;</td>
  <td class = "elementCell">
   <select onchange = "window.location='{$T_MODULE_ADMINISTRATOR_TOOLS_BASEURL}&tab=unenroll_courses&type='+this.options[this.options.selectedIndex].value">
    <option value = "0">{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_SELECTASSIGNMENTTYPE}</option>
    <option value = "group" {if $smarty.get.type=='group'}selected{/if}>{$smarty.const._GROUP}</option>
    <option value = "branch" {if $smarty.get.type=='branch'}selected{/if}>{$smarty.const._BRANCH}</option>
    <option value = "job" {if $smarty.get.type=='job'}selected{/if}>{$smarty.const._JOBDESCRIPTIONS}</option>
   </select>
  </td></tr>
 {if $T_ENTITIES_LIST}
 <tr><td class = "labelCell">{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_ENTITYENTRY}:&nbsp;</td>
  <td class = "elementCell">
   <select onchange = "window.location='{$T_MODULE_ADMINISTRATOR_TOOLS_BASEURL}&tab=unenroll_courses&type={$smarty.get.type}&entry='+this.options[this.options.selectedIndex].value">
    <option value = "0">{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_SELECTANENTRY}</option>
   {foreach name = 'jobs_list' item = "item" key = "key" from = $T_ENTITIES_LIST}
    <option value = "{$key}" {if $smarty.get.entry==$key}selected{/if}>{$item}</option>
   {/foreach}
   </select>
  </td></tr>
  {if $smarty.get.entry}
  <tr><td></td>
   <td class = "submitCell"><input type = "submit" class = "flatButton" onclick = "if (confirm('{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_AREYOUSUREYOUWANTTOREMOVEENTITYUSERSFROMENTITYCOURSES}')) removeUsersFromEntity(this)" value = "{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_REMOVECOURSESFROMUSERS}"/>
   </td></tr>
  {/if}
 {/if}
</table>
{/capture}

{capture name = "t_category_reports_code"}
 {eF_template_printForm form = $T_CATEGORY_FORM}
 {if $T_SHOW_TABLE}
  {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'categoryUsersTable'}
<!--ajax:categoryUsersTable-->
 <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" order="desc" id = "categoryUsersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_ADMINISTRATOR_TOOLS_BASEURL}&">
  <tr><td class="topTitle" name = "course">{$smarty.const._COURSE}</td>
   <td class="topTitle" name = "category">{$smarty.const._CATEGORY}</td>
   <td class="topTitle" name = "login">{$smarty.const._USER}</td>
   {*<td class="topTitle">{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_EMPLOYEEID}</td>*}
   <td class="topTitle centerAlign" name = "to_timestamp">{$smarty.const._COMPLETED}</td>
   <td class="topTitle centerAlign" name = "score">{$smarty.const._SCORE}</td>
   <td class="topTitle" name = "supervisor">{$smarty.const._SUPERVISOR}</td>
   <td class="topTitle" name = "branch">{$smarty.const._BRANCH}</td>
   <td class="topTitle centerAlign" name = "historic">{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_HISTORICENTRY}</td>
   </tr>
  {foreach name = 'course_users_list' item = "item" key = "key" from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$item.active}deactivatedTableElement{/if}">
   <td ><a style = "{if !$item.course_active}color:red{/if}" class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$item.course_id}">{$item.course}</a></td>
   <td><a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=directions&edit_direction={$item.directions_ID}">{$item.category}</a></td>
   <td><a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$item.login}">#filter:login-{$item.login}#</a></td>
   {*<td>{$item.login}!!</td>*}
   <td class = "centerAlign">#filter:timestamp-{$item.to_timestamp}#</td>
   <td class = "centerAlign">{if !$item.historic}#filter:score-{$item.score}#%{/if}</td>
   <td><a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$item.login}">#filter:login-{$item.supervisor}#</a></td>
   <td><a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=branches&edit_branch={$item.branch_ID}">{$item.branch}</a></td>
   <td class = "centerAlign">{if $item.historic}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
  </tr>
  {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
 </table>
<!--/ajax:categoryUsersTable-->
  {/if}
 <div class = ""><span>{$smarty.const._OPERATIONS}:</span>
  <img class = "ajaxHandle" src = "images/file_types/xls.png" alt = "{$smarty.const._EXPORTTOXLS}" title = "{$smarty.const._EXPORTTOXLS}" onclick = "exportUsersToXls(this);"/>
 </div>
 {/if}
{/capture}

{capture name = "t_idle_users_code"}
 {eF_template_printForm form = $T_IDLE_USER_FORM}
<!--ajax:idleUsersTable-->
     <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "idleUsersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_ADMINISTRATOR_TOOLS_BASEURL}&">
      <tr class = "topTitle">
       <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
       <td class = "topTitle" name = "last_action">{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_LASTACTION}</td>
       <td class = "topTitle centerAlign" name = "active">{$smarty.const._STATUS}</td>
       <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
      </tr>
 {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
      <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
       <td><a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}" class = "editLink">#filter:login-{$user.login}#</a></td>
       <td>{if $user.last_action}#filter:timestamp_time-{$user.last_action}#{else}{$smarty.const._NEVER}{/if}</td>
       <td class = "centerAlign">
        <img class = "ajaxHandle" src="images/16x16/trafficlight_{if $user.active}green{else}red{/if}.png" title="{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_TOGGLESTATUS}" alt="{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_TOGGLESTATUS}" onclick = "toggleUser(this, '{$user.login}');">
       </td>
       <td class = "centerAlign">
       {if $user.login != $smarty.session.s_login}
        <img class = "ajaxHandle" src="images/16x16/error_delete.png" title="{$smarty.const._ARCHIVE}" alt="{$smarty.const._ARCHIVE}" onclick = "archiveUser(this, '{$user.login}');">
       {/if}
       </td>
     </tr>
 {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
 {/foreach}
    </table>
<!--/ajax:idleUsersTable-->
    <div class = ""><span>{$smarty.const._OPERATIONS}:</span>
     <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_DEACTIVATEALLUSERS}" title = "{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_DEACTIVATEALLUSERS}" onclick = "if (confirm('{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_THISWILLDEACTIVATEALLUSERSAREYOUSURE}')) deactivateAllIdleUsers(this)"/>
     <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_ARCHIVEALLUSERS}" title = "{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_ARCHIVEALLUSERS}" onclick = "if (confirm('{$smarty.const._MODULE_ADMINISTRATOR_TOOLS_THISWILLARCHIVEALLUSERSAREYOUSURE}')) archiveAllIdleUsers(this)"/>
    </div>
{/capture}

{capture name = "t_job_courses_code"}
 {eF_template_printForm form = $T_JOB_COURSES_FORM}
{/capture}


{capture name = 't_administrator_tools_code'}
 <div class = "tabber">
  {eF_template_printBlock tabber = "change_login" title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_CHANGELOGIN data = $smarty.capture.t_change_login_code absoluteImagePath=1 image=$T_MODULE_ADMINISTRATOR_TOOLS_BASELINK|cat:'images/tools.png'}
  {eF_template_printBlock tabber = "global_settings" title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_GLOBALLESSONSETTINGS data = $smarty.capture.t_global_settings_code absoluteImagePath=1 image=$T_MODULE_ADMINISTRATOR_TOOLS_BASELINK|cat:'images/tools.png'}
  {eF_template_printBlock tabber = "sql" title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_SQLINTERFACE data = $smarty.capture.t_sql_code image='32x32/generic.png'}
  {eF_template_printBlock tabber = "set_course_lesson_users" title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_SETCOURSELESSONUSERSCODE data = $smarty.capture.t_set_course_users_code image='32x32/users.png'}
 </div>
{/capture}
{eF_template_printBlock title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS data = $smarty.capture.t_administrator_tools_code absoluteImagePath=1 image=$T_MODULE_ADMINISTRATOR_TOOLS_BASELINK|cat:'images/tools.png'}
