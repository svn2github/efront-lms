{*moduleChart: Show the organization's chart *}
{capture name = 't_chart_code'}
   {if $T_CHART_TREE != ''}
    {if !$T_POPUP_MODE && !$smarty.get.popup}
    <a href = "javascript:void(0)" onClick = "expandCollapse('dhtmlgoodies_branches_tree');">{$smarty.const._EXPANDCOLLAPSE}</a><br>
    {/if}
    <table width = "100%">
  <tr><td id = "singleColumn">
     {$T_CHART_TREE}
    </td></tr>
    </table>
   {else}
    <table width = "100%">
  <tr><td class = "emptyCategory">{$smarty.const._NOBRANCHESHAVEBEENREGISTERED}</td></tr>
    </table>
   {/if}
{/capture}

{if !$T_POPUP_MODE && !$smarty.get.popup}
 {eF_template_printBlock title = $smarty.const._ORGANIZATIONCHARTTREE data = $smarty.capture.t_chart_code image = '32x32/organization.png' options = $T_CHART_OPTIONS}
{else}
 {eF_template_printBlock title = $smarty.const._ORGANIZATIONCHARTTREE data = $smarty.capture.t_chart_code image = '32x32/organization.png'}
{/if}
