{*moduleGroups: The user groups list*}
{if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
 {assign var = "_change_" value = true}
{/if}

{capture name = "moduleGroups"}
 <tr><td class = "moduleCell">
 {if $smarty.get.add_user_group || $smarty.get.edit_user_group}
  {capture name = "t_group_form"}
   {eF_template_printForm form = $T_USERGROUPS_FORM}
  {/capture}

  {capture name = "t_group_users_code"}
  {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'usersTable'}
<!--ajax:usersTable-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "1" order="desc" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=user_groups&edit_user_group={$smarty.get.edit_user_group}&">
   <tr class = "topTitle">
    <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
    <td class = "topTitle centerAlign" name = "in_group">{$smarty.const._CHECK}</td>
   </tr>
  {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
   <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
    <td><a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}" class = "editLink" {if ($user.pending == 1)}style="color:red;"{/if}><span id="column_{$user.login}" {if !$user.active}style="color:red;"{/if}>#filter:login-{$user.login}#</span></a></td>
    <td class = "centerAlign">
   {if $_change_}
     <input class = "inputCheckbox" type = "checkbox" id = "checked_{$user.login}" name = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this, 'usersTable');" {if $user.in_group == 1}checked = "checked"{/if} />
   {else}
     {if $user.in_group == 1}<img src = "images/16x16/success.png" alt = "{$smarty.const._GROUPUSER}" title = "{$smarty.const._GROUPUSER}">{/if}
   {/if}
    </td>
   </tr>
  {/foreach}
  </table>
<!--/ajax:usersTable-->
  {/if}
  {/capture}

  {capture name = "t_group_lessons_code"}
  <div class = "headerTools">
   <span>
    <img src = "images/16x16/users.png" title = "{$smarty.const._ASSIGNLESSONSTOGROUPUSERS}" alt = "{$smarty.const._ASSIGNLESSONSTOGROUPUSERS}">
    <a href = "javascript:void(0)" onclick = "assignToGroupUsers(this, 'lessons')" title = "{$smarty.const._ASSIGNLESSONSTOGROUPUSERS}" >{$smarty.const._ASSIGNLESSONSTOGROUPUSERS}</a>
   </span>
  </div>
<!--ajax:lessonsTable-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "lessonsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "administrator.php?ctg=user_groups&edit_user_group={$smarty.get.edit_user_group}&">
   <tr class = "topTitle">
    <td name = "name" class = "topTitle">{$smarty.const._NAME}</td>
    <td name = "directions_ID" class = "topTitle">{$smarty.const._PARENTDIRECTIONS}</td>

    <td name = "price" class = "topTitle centerAlign">{$smarty.const._PRICE}</td>

  {if $_change_}
    <td name = "in_group" class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
  {/if}
   </tr>
  {foreach name = 'users_to_lessons_list' key = 'key' item = 'lesson' from = $T_DATA_SOURCE}
   <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
    <td>{$lesson.name}</td>
    <td>{$lesson.direction_name}</td>

    <td class = "centerAlign">{if $course.price == 0}{$smarty.const._FREECOURSE}{else}{$course.price_string}{/if}</td>

  {if ($_change_)}
    <td class = "centerAlign">
     <input class = "inputCheckBox" type = "checkbox" id = "lesson_{$lesson.id}" name = "lesson_{$lesson.id}" onclick ="ajaxPost('{$lesson.id}', this, 'lessonsTable');" {if $lesson.in_group}checked{/if}>
    </td>
  {/if}
   </tr>
  {foreachelse}
   <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
  </table>
<!--/ajax:lessonsTable-->
  {/capture}



 {capture name = "t_group_courses_code"}
  <div class = "headerTools">
   <span>
    <img src = "images/16x16/users.png" title = "{$smarty.const._ASSIGNCOURSESTOGROUPUSERS}" alt = "{$smarty.const._ASSIGNCOURSESTOGROUPUSERS}">
    <a href = "javascript:void(0)" onclick = "assignToGroupUsers(this, 'courses')" title = "{$smarty.const._ASSIGNCOURSESTOGROUPUSERS}" >{$smarty.const._ASSIGNCOURSESTOGROUPUSERS}</a>
   </span>
  </div>

  {assign var = "courses_url" value = "`$smarty.server.PHP_SELF`?ctg=user_groups&edit_user_group=`$smarty.get.edit_user_group`&"}
  {assign var = "_change_handles_" value = $_change_}
  {include file = "includes/common/courses_list.tpl"}
 {/capture}


  {capture name='t_new_group_code'}
   <div class = "tabber">
    {eF_template_printBlock tabber = "groups" title=$smarty.const._GROUPOPTIONS data=$smarty.capture.t_group_form image='32x32/generic.png' options = $T_STATS_LINK}

   {if $smarty.get.edit_user_group}
    <script>var editGroup = '{$smarty.get.edit_user_group}';</script>
    {eF_template_printBlock tabber = "users" title=$smarty.const._GROUPUSERS data=$smarty.capture.t_group_users_code image='32x32/users.png'}
    {if $T_CONFIGURATION.lesson_enroll}
    {eF_template_printBlock tabber = "lessons" title=$smarty.const._GROUPLESSONS data=$smarty.capture.t_group_lessons_code image='32x32/lessons.png'}
    {/if}
    {eF_template_printBlock tabber = "courses" title=$smarty.const._GROUPCOURSES data=$smarty.capture.t_group_courses_code image='32x32/courses.png'}
   {/if}
   </div>
  {/capture}
  {if $smarty.get.add_user_group}
    {eF_template_printBlock title = $smarty.const._NEWGROUP data = $smarty.capture.t_new_group_code image = '32x32/users.png'}
  {else}
    {eF_template_printBlock title = "`$smarty.const._OPTIONSFORGROUP` <span class = 'innerTableName'>&quot;`$T_CURRENT_GROUP->group.name`&quot;</span>" data = $smarty.capture.t_new_group_code image = '32x32/users.png'}
  {/if}

 {else}
  {capture name = 't_groups_code'}
  <script>var activate = '{$smarty.const._ACTIVATE}';var deactivate = '{$smarty.const._DEACTIVATE}';</script>
  {if $_change_}
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" title = "{$smarty.const._NEWGROUP}" alt = "{$smarty.const._NEWGROUP}">
     <a href = "administrator.php?ctg=user_groups&add_user_group=1" title = "{$smarty.const._NEWGROUP}" >{$smarty.const._NEWGROUP}</a>
    </span>






   </div>

   {assign var = "change_groups" value = 1}
  {/if}
   <table width = "100%" class = "sortedTable" sortBy = "0">
    <tr class = "topTitle">
     <td class = "topTitle">{$smarty.const._NAME}</td>
     <td class = "topTitle">{$smarty.const._DESCRIPTION}</td>
     <td class = "topTitle centerAlign">{$smarty.const._USERS}</td>
     <td class = "topTitle centerAlign">{$smarty.const._ACTIVE2}</td>
    {if $change_groups}
     <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
    {/if}
    </tr>
  {foreach name = 'group_list' key = 'key' item = 'group' from = $T_USERGROUPS}
    <tr id="row_{$group.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$group.active}deactivatedTableElement{/if}">
     <td><a href = "administrator.php?ctg=user_groups&edit_user_group={$group.id}" class = "editLink">
      <span id="column_{$group.id}" {if !$group.active}style="color:red"{/if}>



       {$group.name}

      </span></a></td>
     <td>{$group.description}</td>
     <td class = "centerAlign">{$group.num_users}</td>
     <td class = "centerAlign">
      {if $group.active == 1}
       <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $change_groups}onclick = "activateGroup(this, '{$group.id}')"{/if}>
      {else}
       <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" {if $change_groups}onclick = "activateGroup(this, '{$group.id}')"{/if}>
      {/if}
     </td>
    {if $change_groups}
     <td class = "centerAlign">
       <a href = "administrator.php?ctg=user_groups&edit_user_group={$group.id}" ><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
       <img class = "ajaxHandle" border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEGROUP}')) deleteGroup(this, '{$group.id}');"/>
     </td>
    {/if}
    </tr>
  {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
   </table>
  {/capture}
  {eF_template_printBlock title = $smarty.const._UPDATEGROUPS data = $smarty.capture.t_groups_code image = '32x32/users.png' help = 'User_groups'}
 {/if}
 </td></tr>
{/capture}
