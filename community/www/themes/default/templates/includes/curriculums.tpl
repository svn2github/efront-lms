{if !isset($T_CURRENT_USER->coreAccess.curriculums) || $T_CURRENT_USER->coreAccess.curriculums == 'change'}
 {assign var = "_change_" value = true}
{/if}

{if $smarty.get.add || $smarty.get.edit}
 {capture name = 't_add_code'}
  {$T_ENTITY_FORM.javascript}
  <form {$T_ENTITY_FORM.attributes}>
   {$T_ENTITY_FORM.hidden}
   <table class = "formElements">
    <tr><td class = "labelCell">{$T_ENTITY_FORM.name.label}:&nbsp;</td>
     <td class = "elementCell">{$T_ENTITY_FORM.name.html} </td></tr>
    <tr><td class = "labelCell">{$T_ENTITY_FORM.description.label}:&nbsp;</td>
     <td class = "elementCell">{$T_ENTITY_FORM.description.html}</td></tr>
    <tr><td class = "labelCell">{$T_ENTITY_FORM.active.label}:&nbsp;</td>
     <td class = "elementCell">{$T_ENTITY_FORM.active.html}</td></tr>
    <tr><td></td>
     <td class = "submitCell">{$T_ENTITY_FORM.submit.html}</td>
    </tr>
   </table>
  </form>
 {/capture}
 {capture name = 't_courses_code'}
  {assign var = "courses_url" value = "`$smarty.server.PHP_SELF`?ctg=curriculums&edit=`$smarty.get.edit`&"}
  {assign var = "_change_handles_" value = $_change_}
  {include file = "includes/common/courses_list.tpl"}
 {/capture}
 {capture name = 't_users_code'}
  {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'usersTable'}
<!--ajax:usersTable-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=curriculums&edit={$smarty.get.edit}&">
   <tr class = "topTitle">
    <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
    <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
    <td class = "topTitle centerAlign" name = "has_user">{$smarty.const._OPERATIONS}</td>
   </tr>
  {foreach name = 'users_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
   <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
    <td><a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}" class = "editLink" {if ($user.pending == 1)}style="color:red;"{/if}><span id="column_{$user.login}" {if !$user.active}style="color:red;"{/if}>#filter:login-{$user.login}#</span></a></td>
    <td>{$user.user_type}</td>
    <td align = "center">
   {if $_change_}
     <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this, 'usersTable');" {if $user.has_user}checked = "checked"{/if} />{if $user.has_user}<span style = "display:none">checked</span>{/if} {*Text for sorting*}
   {/if}
    </td>
   </tr>
  {/foreach}
  </table>
<!--/ajax:usersTable-->
  {/if}
 {/capture}

 {capture name = 't_groups_code'}
  {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'groupsTable'}
<!--ajax:groupsTable-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_GROUPS_SIZE}" sortBy = "0" id = "groupsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=curriculums&edit={$smarty.get.edit}&">
   <tr class = "topTitle">
    <td class = "topTitle" name = "name">{$smarty.const._GROUP}</td>
    <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
   </tr>
  {foreach name = 'groups_list' key = 'key' item = 'group' from = $T_DATA_SOURCE}
   <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$group.active}deactivatedTableElement{/if}">
    <td><a href = "{$smarty.server.PHP_SELF}?ctg=user_groups&edit_user_group={$group.id}" class = "editLink"><span id="column_{$group.ud}" {if !$group.active}style="color:red;"{/if}>{$group.name}</span></a></td>
    <td align = "center">
   {if $_change_}
     <img class = "ajaxHandle" src = "images/16x16/arrow_right.png" alt = "{$smarty.const._ASSIGNCURRICULUMTOGROUPUSERS}" title = "{$smarty.const._ASSIGNCURRICULUMTOGROUPUSERS}" onclick = "assignCurriculumToGroup(this, '{$group.id}');"/>
   {/if}
    </td>
   </tr>
  {/foreach}
  </table>
<!--/ajax:groupsTable-->
  {/if}
 {/capture}

 {capture name = "t_edit_curriculum_code"}
  <div class = "tabber">
  {eF_template_printBlock tabber = "properties" title = $smarty.const._CURRICULUM data = $smarty.capture.t_add_code image = '32x32/theory.png'}
  {if $smarty.get.edit}
   {eF_template_printBlock tabber = "courses" title = $smarty.const._COURSES data = $smarty.capture.t_courses_code image = '32x32/courses.png'}
   {eF_template_printBlock tabber = "users" title = $smarty.const._USERS data = $smarty.capture.t_users_code image = '32x32/users.png'}
 {*
   {eF_template_printBlock tabber = "groups" title = $smarty.const._GROUPS data = $smarty.capture.t_groups_code image = '32x32/groups.png'}
 *}
  {/if}
  </div>
 {/capture}
 {eF_template_printBlock title = $smarty.const._CURRICULUMPROPERTIES data = $smarty.capture.t_edit_curriculum_code image = '32x32/theory.png'}
{else}
 {capture name = "t_curriculums_code"}
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" title = "{$smarty.const._ADDCURRICULUM}" alt = "{$smarty.const._ADDCURRICULUM}">
     <a href = "{$smarty.server.PHP_SELF}?ctg=curriculums&add=1" title = "{$smarty.const._ADDCURRICULUM}">{$smarty.const._ADDCURRICULUM}</a>
    </span>
   </div>
<!--ajax:curriculumsTable-->
   <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "curriculumsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=curriculums&">
    <tr class = "topTitle defaultRowHeight">
     <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
     <td class = "topTitle" name = "description">{$smarty.const._DESCRIPTION}</td>
     <td class = "topTitle centerAlign" name = "active">{$smarty.const._ACTIVE}</td>
     <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
    </tr>
    {foreach name = 'users_list' key = 'key' item = 'curriculum' from = $T_DATA_SOURCE}
    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
     <td<a href = "{$smarty.server.PHP_SELF}?ctg=curriculums&edit={$curriculum.id}" class = "editLink">{$curriculum.name}</a></td>
     <td>{$curriculum.description|eF_truncate:300}</td>
     <td class = "centerAlign">
      <img {if $curriculum.active == 0}style = "display:none"{/if} class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" onclick = "deactivateEntity(this, '{$curriculum.id}', {ldelim}curriculums:1{rdelim});">
      <img {if $curriculum.active == 1}style = "display:none"{/if} class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" onclick = "activateEntity(this, '{$curriculum.id}', {ldelim}curriculums:1{rdelim})">
     </td>
     <td class = "centerAlign">
      <a href = "{$smarty.server.PHP_SELF}?ctg=curriculums&edit={$curriculum.id}"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}"/></a>
      <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteEntity(this, '{$curriculum.id}', {ldelim}curriculums:1{rdelim})"/>
     </td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
<!--/ajax:curriculumsTable-->
 {/capture}

 {eF_template_printBlock title = $smarty.const._CURRICULUMS data = $smarty.capture.t_curriculums_code image='32x32/theory.png'}
{/if}
