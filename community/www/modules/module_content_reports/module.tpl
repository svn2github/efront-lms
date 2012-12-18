{capture name = "t_grid_code"}

  <table class = "statisticsSelectList">
   <tr><td class = "labelCell">{$smarty.const._MODULE_CONTENTREPORTS_FILTERBYUSER}:</td>
    <td class = "elementCell">
     <input type = "text" id = "autocomplete" class = "autoCompleteTextBox"/>
     <img id = "busy" src = "images/16x16/clock.png" style = "display:none;" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}"/>
     <div id = "autocomplete_users" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
    </td>
   </tr>
   <tr><td></td>
    <td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td>
   </tr>
  </table>

<!--ajax:content_reportsTable-->
     <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "2" id = "content_reportsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_BASEURL}&sel_user={$smarty.get.sel_user}&">
      <tr class = "topTitle">
       <td class = 'topTitle' name = 'lesson_name'>{$smarty.const._LESSON}</td>
       <td class = 'topTitle' name = 'name'>{$smarty.const._UNIT}</td>
       <td class = 'topTitle centerAlign' name = 'count'>{$smarty.const._MODULE_CONTENTREPORTS_TIMESVIEWED}</td>
      </tr>
 {foreach name = 'demo_data_list' key = 'key' item = 'item' from = $T_DATA_SOURCE}
      <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
       <td>{$item.lesson_name}</td>
       <td>{$item.name}</td>
       <td class = "centerAlign">{$item.count}</td>
      </tr>
 {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
 {/foreach}
    </table>

<!--/ajax:content_reportsTable-->

{/capture}
{eF_template_printBlock title = "Data" data = $smarty.capture.t_grid_code}
