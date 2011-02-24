{*** User groups ***}
{capture name = 't_users_to_groups_code'}
<!--ajax:groupsTable-->
 <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "groupsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.REQUEST_URI}&">
  <tr class = "topTitle">
   <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
   <td class = "topTitle" name = "description">{$smarty.const._DESCRIPTION}</td>
   <td class = "topTitle centerAlign" name = "partof">{$smarty.const._CHECK}</td>
  </tr>
 {foreach name = 'users_to_groups_list' key = 'key' item = 'group' from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$group.active}deactivatedTableElement{/if}">
   <td>
    {if $_change_groups_}
     <a href = "{$smarty.server.PHP_SELF}?ctg=user_groups&edit_user_group={$group.id}" class = "editLink">{$group.name}</a>
    {else}
     {$group.name}
    {/if}
   </td>
   <td>{$group.description}</td>
   <td class = "centerAlign">
   {if !$_change_groups_}
    {if $group.partof == 1}
     <img src = "images/16x16/success.png" alt = "{$smarty.const._PARTOFTHISGROUP}" title = "{$smarty.const._PARTOFTHISGROUP}" />
    {/if}
   {else}
    <input class = "inputCheckBox" type = "checkbox" id = "group_{$group.id}" name = "{$group.id}" onclick ="ajaxUserPost('group', '{$group.id}', this);" {if $group.partof == 1}checked{/if}>
   {/if}
   </td>
  </tr>
 {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "3">{$smarty.const._NODATAFOUND}</td></tr>
 {/foreach}
 </table>
<!--/ajax:groupsTable-->
{/capture}
{eF_template_printBlock title = $smarty.const._GROUPS data = $smarty.capture.t_users_to_groups_code image = '32x32/users.png'}
