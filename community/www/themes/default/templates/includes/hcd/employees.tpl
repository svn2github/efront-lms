  {assign var = "title" value = '<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?op=employees">'|cat:$smarty.const._EMPLOYEES|cat:'</a>'}

 {*moduleShowemployees: Show employees*}
 {capture name = 't_employees_code'}
  <script>var activate = '{$smarty.const._ACTIVATE}';var deactivate = '{$smarty.const._DEACTIVATE}';</script>
  {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" title = "{$smarty.const._NEWUSER}" alt = "{$smarty.const._NEWUSER}">
     <a href = "{$smarty.session.s_type}.php?ctg=personal&user={$smarty.session.s_login}&op=profile&add_user=1">{$smarty.const._NEWUSER}</a>
    </span>
   </div>
   {assign var = "_change_" value = 1}
  {/if}


{if !isset($T_SORTED_TABLE) || $T_SORTED_TABLE == "usersTable"}

<!--ajax:usersTable-->

 <table style = "width:100%" class = "sortedTable" sortBy = "0" size = "{$T_TABLE_SIZE}" id = "usersTable" useAjax = "1" branchFilter="{$T_BRANCHES_FILTER}" jobFilter="{$T_JOBS_FILTER}" activeFilter = 1 rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=users&">
  <tr class = "topTitle">
   <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
   <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
   <td class = "topTitle" name = "timestamp">{$smarty.const._REGISTRATIONDATE}</td>
   <td class = "topTitle" name = "last_login">{$smarty.const._LASTLOGIN}</td>
        {if $T_CURRENT_USER->user.user_type != 'administrator'}
            <td class = "topTitle" name="branch_name">{$smarty.const._BRANCHNAME}</td>
  {/if}
   <td class = "topTitle centerAlign" name = "jobs_num">{$smarty.const._JOBSASSIGNED}</td>
  {if $smarty.session.s_type == "administrator"}
   <td class = "topTitle centerAlign" name = "active">{$smarty.const._ACTIVE2}</td>
  {/if}
   <td class = "topTitle noSort centerAlign">
    {$smarty.const._OPERATIONS}
   </td>
  </tr>

  {foreach name = 'users_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
  <tr id="row_{$user.login}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
   <td id="column_{$user.login}">
    {if !isset($T_SUPERVISED_EMPLOYEES) || in_array($user.login, $T_SUPERVISED_EMPLOYEES)}
    <a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$user.login}&op=profile" class = "{if $user.active == 1}editLink{/if} {if !$T_CONFIGURATION.disable_tooltip}info{/if}" url = "ask_information.php?users_LOGIN={$user.login}&type=user">#filter:login-{$user.login}#</a>
    {else}
    #filter:login-{$user.login}#
    {/if}
   </td>
   <td>{if $user.user_types_ID}{$T_ROLES[$user.user_types_ID]}{else}{$T_ROLES[$user.user_type]}{/if}</td>
   <td>#filter:timestamp-{$user.timestamp}#</td>
   <td>{if $user.last_login}#filter:timestamp_time_nosec-{$user.last_login}#{else}{$smarty.const._NEVER}{/if}</td>
        {if $T_CURRENT_USER->user.user_type != 'administrator'}
         <td>{$user.branch_name}</td>
  {/if}
   <td class = "centerAlign">{$user.jobs_num}</td>
  {if $smarty.session.s_type == "administrator"}
   <td class = "centerAlign">
   {if $user.login != $smarty.session.s_login}
    {if !($user.user_type == 'administrator' && $user.user_types_ID == 0 && $T_CURRENT_USER->user.user_type == 'administrator' && $T_CURRENT_USER->user.user_types_ID != 0)}
     {if $user.active == 1}
      <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $_change_}onclick = "activateUser(this, '{$user.login}')"{/if}>
     {else}
      <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" {if $_change_}onclick = "activateUser(this, '{$user.login}')"{/if}>
     {/if}
    {/if}
   {/if}
   </td>
  {/if}
   <td class = "centerAlign nowrap">
  {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
    <a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img class = "handle" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a>
  {/if}
  {if (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change') && (!isset($T_SUPERVISED_EMPLOYEES) || in_array($user.login, $T_SUPERVISED_EMPLOYEES))}
    <a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$user.login}&op=profile" class = "editLink"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
    {if $smarty.session.s_login != $user.login}
     <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._ARCHIVEENTITY}" alt = "{$smarty.const._ARCHIVEENTITY}" onclick = "archiveUser(this, '{$user.login}')"/>
    {/if}
  {/if}
   </td>
  </tr>
  {foreachelse}
   <tr class = "emptyCategory DefaultRowHeight"><td colspan="10" class = "emptyCategory">{$smarty.const._NOUSERSFOUND}</td></tr>
  {/foreach}

 </table>

<!--/ajax:usersTable-->
{/if}

 {/capture}
 {* end of t_employees_code capture *}

 <tr>
  <td style = "vertical-align:top">
  {if $smarty.session.s_type == "administrator"}
   {eF_template_printBlock title = $smarty.const._UPDATEEMPLOYEES data = $smarty.capture.t_employees_code image = '32x32/user.png' help = 'Users'}
  {else}

   {capture name = "t_supervisor_employees"}
   {if $T_CONFIGURATION.show_unassigned_users_to_supervisors == 1}
   <div class="tabber">
    <div class="tabbertab">
     <h3>{$smarty.const._SUPERVISEDEMPLOYEES}</h3>
   {/if}
     {$smarty.capture.t_employees_code}
   {if $T_CONFIGURATION.show_unassigned_users_to_supervisors == 1}
    </div>

    <div class="tabbertab {if ($smarty.get.tab == "assign_employees"  || isset($smarty.post.employees_to_branches)) } tabbertabdefault {/if}">
     <h3>{$smarty.const._UNATTACHEDEMPLOYEES}</h3>

      {if !$T_CURRENT_USER->coreAccess.users || $T_CURRENT_USER->coreAccess.users == 'change'}
      <div class = "headerTools">
       <span>
        <img src = "images/16x16/add.png" title = "{$smarty.const._NEWUSER}" alt = "{$smarty.const._NEWUSER}">
        <a href = "{$smarty.session.s_type}.php?ctg=personal&user={$smarty.session.s_login}&op=profile&add_user=1">{$smarty.const._NEWUSER}</a>
       </span>
      </div>
      {/if}

<!--ajax:unattachedUsersTable-->
     <table style = "width:100%" class = "sortedTable" size = "{$T_UNATTACHED_EMPLOYEES_SIZE}" sortBy = "0" id = "unattachedUsersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.session.s_type}.php?ctg=users&">
     <tr class = "topTitle">
      <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
      <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
      <td class = "topTitle" name = "timestamp">{$smarty.const._REGISTRATIONDATE}</td>
      <td class = "topTitle" name = "last_login">{$smarty.const._LASTLOGIN}</td>
      <td class = "topTitle noSort centerAlign">{$smarty.const._OPERATIONS}</td>
     </tr>

     {foreach name = 'users_list' key = 'key' item = 'user' from = $T_UNATTACHED_EMPLOYEES}
     <tr id="row_{$user.login}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
      <td id="column_{$user.login}">
       {if $user.active == 1}
        <a href = "{$smarty.session.s_type}.php?ctg=personal&user={$user.login}&op=profile" class = "editLink">#filter:login-{$user.login}#</a>
       {else}
        #filter:login-{$user.login}#
       {/if}
      </td>
      <td>{if $user.user_types_ID}{$T_ROLES[$user.user_types_ID]}{else}{$T_ROLES[$user.user_type]}{/if}</td>
      <td>#filter:timestamp-{$user.timestamp}#</td>
      <td>{if $user.last_login}#filter:timestamp_time_nosec-{$user.last_login}#{else}{$smarty.const._NEVER}{/if}</td>
      <td class = "centerAlign">
       {if $user.login != $smarty.session.s_login && $user.user_type != 'administrator'}
        <a href="{$smarty.session.s_type}.php?ctg=personal&user={$user.login}&op=profile&op=status&print_preview=1&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._EMPLOYEEFORMPRINTPREVIEW}', 2)" target = "POPUP_FRAME"><img src='images/16x16/printer.png' title= '{$smarty.const._PRINTPREVIEW}' alt = '{$smarty.const._PRINTPREVIEW}' border='0' /></a>
       {else}
        <img class="handle" src='images/16x16/printer.png' title= '{$smarty.const._PRINTPREVIEW}' alt = '{$smarty.const._PRINTPREVIEW}' border='0' />
       {/if}

       {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
        <a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img class="handle" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a>
       {/if}

       {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
        <a href = "{$smarty.session.s_type}.php?ctg=personal&user={$user.login}&op=profile" class = "editLink"><img class="handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
       {/if}
      </td>
     </tr>
     {foreachelse}
      <tr><td colspan="10" align="center">{$smarty.const._NOUSERSFOUND}</td></tr>
     {/foreach}
    </table>
<!--/ajax:unattachedUsersTable-->

    </div>
   </div>
   {/if}
   {/capture}
   {eF_template_printBlock title = $smarty.const._EMPLOYEES data = $smarty.capture.t_supervisor_employees image = '32x32/user.png' options = $T_SUBBRANCHES_LINK}


  {/if}



  </td>
 </tr>
