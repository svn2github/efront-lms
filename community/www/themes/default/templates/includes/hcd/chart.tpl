{*moduleChart: Show the organization's chart *}
{capture name = 't_chart_code'}
 {if $T_CHART_TREE}
 <div class = "headerTools">
 {if !$T_POPUP_MODE && !$smarty.get.popup}
  {if !$smarty.cookies.orgChartMode}
   <span>
    <a href = "javascript:void(0)" onClick = "expandCollapse('dhtmlgoodies_branches_tree');">{$smarty.const._EXPANDCOLLAPSE}</a>
   </span>
   <span>
    <img src = "images/16x16/go_into.png" alt = "{$smarty.const._SWITCHTOSIMPLEVIEW}" title = "{$smarty.const._SWITCHTOSIMPLEVIEW}"/>
    <a href = "javascript:void(0)" onclick = "toggleOrgChartMode(this)">{$smarty.const._SWITCHTOSIMPLEVIEW}</a>
   </span>
  {else}
   <span>
    <img src = "images/16x16/question_type_multiple_correct.png" alt = "{$smarty.const._SWITCHTOTREEVIEW}" title = "{$smarty.const._SWITCHTOTREEVIEW}"/>
    <a href = "javascript:void(0)" onclick = "toggleOrgChartMode(this)">{$smarty.const._SWITCHTOTREEVIEW}</a>
   </span>
  {/if}
 {/if}
 </div>
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

{eF_template_printBlock title = $smarty.const._ORGANIZATIONCHARTTREE data = $smarty.capture.t_chart_code image = '32x32/organization.png' options = $T_CHART_OPTIONS}
