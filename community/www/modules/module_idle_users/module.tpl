 {capture name = "t_idle_users_code"}
  {eF_template_printForm form = $T_IDLE_USER_FORM}
  <br/>
<!--ajax:idleUsersTable-->
      <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "idleUsersTable" useAjax = "1" activeFilter = 1 rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_BASEURL}&">
       <tr class = "topTitle">
        <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
        <td class = "topTitle" name = "last_action">{$smarty.const._MODULE_IDLE_USERS_LASTACTION}</td>
        <td class = "topTitle" name = "last_action_since">{$smarty.const._MODULE_IDLE_USERS_LASTACTIONSINCE}</td>
        <td class = "topTitle centerAlign" name = "active">{$smarty.const._STATUS}</td>
        <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
       </tr>
  {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
       <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
        <td><a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$user.login}&op=profile" class = "editLink">#filter:login-{$user.login}#</a></td>
        <td>{if $user.last_action}#filter:timestamp_time-{$user.last_action}#{else}{$smarty.const._NEVER}{/if}</td>
        <td>{if $user.last_action_since}{$user.last_action_since} {$smarty.const._AGO}{else}-{/if}</td>
        <td class = "centerAlign">
         <img class = "ajaxHandle" src="images/16x16/trafficlight_{if $user.active}green{else}red{/if}.png" title="{$smarty.const._MODULE_IDLE_USERS_TOGGLESTATUS}" alt="{$smarty.const._MODULE_IDLE_USERS_TOGGLESTATUS}" onclick = "toggleUser(this, '{$user.login}');">
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
      <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._MODULE_IDLE_USERS_DEACTIVATEALLUSERS}" title = "{$smarty.const._MODULE_IDLE_USERS_DEACTIVATEALLUSERS}" onclick = "if (confirm('{$smarty.const._MODULE_IDLE_USERS_THISWILLDEACTIVATEALLUSERSAREYOUSURE}')) deactivateAllIdleUsers(this)"/>
      <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._MODULE_IDLE_USERS_ARCHIVEALLUSERS}" title = "{$smarty.const._MODULE_IDLE_USERS_ARCHIVEALLUSERS}" onclick = "if (confirm('{$smarty.const._MODULE_IDLE_USERS_THISWILLARCHIVEALLUSERSAREYOUSURE}')) archiveAllIdleUsers(this)"/>
      <a href = "{$T_MODULE_BASEURL}&excel=1" target = "_new"><img class = "handle" src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}" /></a>
     </div>
 {/capture}
{eF_template_printBlock title = "Idle users" data = $smarty.capture.t_idle_users_code}
