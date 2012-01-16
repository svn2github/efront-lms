{*moduleChart: Show the organization's chart *}
{capture name = 't_chart_code'}

<!--ajax:chart-->

{if $T_CHART_TREE}
 <table width = "100%">
  <tr><td id = "singleColumn">
      {$T_CHART_TREE}
  </td></tr>
 </table>
 {/if}

<!--/ajax:chart-->

 <div class = "loading" id = "loading_div" style = "padding-top:50px;width:300px;height:100px;opacity:0.9">
  <img src = "js/ajax_sorted_table/images/progress1.gif" style = "vertical-align:middle"/>
  <span style = "vertical-align:middle">{$smarty.const._LOADING}</span>
 </div>

 <div id = "chart_holder"></div>
{/capture}

{eF_template_printBlock title = $smarty.const._ORGANIZATIONCHARTTREE data = $smarty.capture.t_chart_code image = '32x32/organization.png' options = $T_CHART_OPTIONS}
