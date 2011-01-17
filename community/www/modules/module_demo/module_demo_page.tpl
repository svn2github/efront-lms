{capture name = "t_form_block_code"}
 {eF_template_printForm form = $T_DEMO_FORM}
{/capture}

{capture name = "t_demo_data_code"}
<!--ajax:demoTable-->

     <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "demoTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_BASEURL}&">
      <tr class = "topTitle">
       <td class = "topTitle" name = "timestamp">{$smarty.const._DATE}</td>
       <td class = "topTitle" name = "data">{$smarty.const._DATA}</td>
       <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
      </tr>
 {foreach name = 'demo_data_list' key = 'key' item = 'item' from = $T_DATA_SOURCE}
      <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
       <td>#filter:timestamp-{$item.timestamp}#</td>
       <td>{$item.data}</td>
       <td class = "centerAlign">
        <img class = "ajaxHandle" src="images/16x16/error_delete.png" title="{$smarty.const._MODULE_DEMO_DELETEDATA}" alt="{$smarty.const._MODULE_DEMO_DELETEDATA}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteModuleDemoData(this, '{$item.id}');"/>
       </td>
     </tr>
 {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
 {/foreach}
    </table>

<!--/ajax:demoTable-->

{/capture}

{capture name = "t_demo_module_code"}
This text and the data below appear from Demo module's getSmartyTpl() and module_demo_page.tpl file
<div class = "tabber">
 {eF_template_printBlock tabber = "demo_form" title = $smarty.const._MODULE_DEMO_DEMOFORM data = $smarty.capture.t_form_block_code}
 {eF_template_printBlock tabber = "demo_data" title = $smarty.const._MODULE_DEMO_DEMODATA data = $smarty.capture.t_demo_data_code}
</div>
{/capture}

{eF_template_printBlock title = $smarty.const._MODULE_DEMO data = $smarty.capture.t_demo_module_code}
