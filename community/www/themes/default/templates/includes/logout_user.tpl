{capture name = 'moduleLogoutUser'}
    <tr><td class = "moduleCell">
  {capture name = 't_logout_user_code'}
<!--ajax:usersTable-->
    <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "2" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=logout_user&">
     <tr class = "topTitle">
      <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
      <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
      <td class = "topTitle" name = "total_seconds">{$smarty.const._ONLINETIME}</td>
      <td class = "topTitle" name = "session_timestamp">{$smarty.const._ONLINESINCE}</td>
      <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
     </tr>
   {foreach name = "online_users_list" item = "item" key = "key" from = $T_DATA_SOURCE}
     <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
      <td><a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$item.login}" class = "editLink">#filter:login-{$item.login}#</a></td>
      <td>{$T_ROLES[$item.user_type]}</td>
      <td><span style = "display:none">{$item.time.total_seconds}</span>{$item.time.time_string}</td>
      <td>#filter:timestamp_time-{$item.session_timestamp}#</td>
      <td class = "centerAlign">
       {if $item.login != $smarty.session.s_login}
       <img class = "ajaxHandle" src = "images/16x16/logout.png" alt = "{$smarty.const._LOGOUTUSER}" title = "{$smarty.const._LOGOUTUSER}" onclick = "logoutUser(this, '{$item.login}');"/>
       {/if}
      </td>
     </tr>

   {/foreach}
    </table>
<!--/ajax:usersTable-->
  {/capture}

  {eF_template_printBlock title = $smarty.const._CONNECTEDUSERS data = $smarty.capture.t_logout_user_code image = '32x32/logout.png'}
    </td></tr>
{/capture}
