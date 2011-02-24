{capture name = 't_history_code'}
<!--ajax:historyFormTable-->
 <table width="100%" size = "{$T_TABLE_SIZE}" id = "historyFormTable" sortBy = "0" class = "sortedTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" order="asc" url = "{$smarty.server.PHP_SELF}?ctg=personal&user={$smarty.get.user}&op=history&">
  <tr class = "topTitle">
   <td class = "topTitle" name="timestamp" width = "15%">{$smarty.const._DATE}</td>
   <td class = "topTitle" name="message" width = "*">{$smarty.const._MESSAGE}</td>
  {if $_change_history_}
   <td class = "topTitle noSort centerAlign" width = "10%">{$smarty.const._OPERATIONS}</td>
  {/if}
  </tr>
  {foreach name = 'history_list' key = 'key' item = 'history' from = $T_DATA_SOURCE}
  <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
   <td><span style="display:none">{$history.timestamp}</span>#filter:timestamp_time-{$history.timestamp}#</td>
   <td>{$history.message}</td>
  {if $_change_history_}
   <td class = "centerAlign">
    <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETETHEHISOTYRECORD}')) deleteHistory(this, '{$history.event_ID}');" />
   </td>
  {/if}
  </tr>
  {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "3">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
 </table>
<!--/ajax:historyFormTable-->
{/capture}
{eF_template_printBlock title = "`$smarty.const._HISTORY`&nbsp;<span class = 'innerTableName'>#filter:login-`$smarty.get.user`#</span>" data = $smarty.capture.t_history_code image = '32x32/generic.png'}
