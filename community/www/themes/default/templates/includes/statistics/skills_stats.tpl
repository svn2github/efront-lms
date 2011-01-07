 {capture name = 'skill_statistics'}
<table class="statisticsTools statisticsSelectList">
 <tr>
  <td class="labelCell">{$smarty.const._CHOOSESKILL}:</td>
  <td class="elementCell" colspan="4"><input type="text"
   id="autocomplete" class="autoCompleteTextBox" /> <img id="busy"
   src="images/16x16/clock.png" style="display: none;"
   alt="{$smarty.const._LOADING}" title="{$smarty.const._LOADING}" />
  <div id="autocomplete_skills" class="autocomplete"></div>
  &nbsp;&nbsp;&nbsp;</td>
 </tr>
 <tr>
  <td></td>
  <td class="infoCell" colspan="4">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td>
 </tr>
 {if !isset($T_CURRENT_SKILL_INFO)}
</table>
{else}

<tr>
 {include file = "includes/statistics/stats_filters.tpl"}
 <td id="right">{$smarty.const._EXPORTSTATS} <a
  href="{$smarty.server.PHP_SELF}?ctg=statistics&option=skill&sel_skill={$smarty.get.sel_skill}&group_filter={$smarty.get.group_filter}&excel=skill&branch_filter={$smarty.get.branch_filter}">
 <img src="images/file_types/xls.png" title="{$smarty.const._XLSFORMAT}"
  alt="{$smarty.const._XLSFORMAT}" /> </a> <a
  href="{$smarty.server.PHP_SELF}?ctg=statistics&option=skill&sel_skill={$smarty.get.sel_skill}&group_filter={$smarty.get.group_filter}&pdf=skill&branch_filter={$smarty.get.branch_filter}">
 <img src="images/file_types/pdf.png" title="{$smarty.const._PDFFORMAT}"
  alt="{$smarty.const._PDFFORMAT}" /> </a></td>
</tr>
</table>

<br />
<table class="statisticsGeneralInfo">
 <tr
  class="{cycle name = 'common_skill_info' values = 'oddRowColor, evenRowColor'}">
  <td class="labelCell">{$smarty.const._NAME}:</td>
  <td class="elementCell">{$T_CURRENT_SKILL_INFO->skill.description}</td>
 </tr>
 <tr
  class="{cycle name = 'common_skill_info' values = 'oddRowColor, evenRowColor'}">
  <td class="labelCell">{$smarty.const._CATEGORY}:</td>
  <td class="elementCell">{$T_CURRENT_SKILL_INFO->skill.category}</td>
 </tr>
</table>

<br />
<table class="statisticsTools">
 <tr>
  <td id="right">{$smarty.const._SKILLGRAPH}: <img class="handle"
   src="images/16x16/reports.png" alt="{$smarty.const._SKILLGRAPH}"
   title="{$smarty.const._SKILLGRAPH}"
   onclick="showGraph($('proto_chart'), 'graph_skill');eF_js_showDivPopup('{$smarty.const._SKILLGRAPH}', 2, 'graph_table');" />
  </td>
 </tr>
</table>
<div id="graph_table" style="display: none">
<div id="proto_chart" class="proto_graph"></div>
</div>
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'skillUsersTable'}
<!--ajax:skillUsersTable-->
<table id="skillUsersTable" sortBy=0 size="{$T_TABLE_SIZE}"
 activeFilter="1" class="sortedTable" useAjax="1"
 url="{$smarty.server.PHP_SELF}?ctg=statistics&option=skill&sel_skill={$smarty.get.sel_skill}{$T_STATS_FILTERS_URL}&"
 style="width: 100%">
 <tr class="topTitle">
  <td class="topTitle" name="login">{$smarty.const._USER}</td>
  <td class="topTitle" name="specification">{$smarty.const._SPECIFICATION}</td>
  <td class="topTitle centerAlign" name="score">{$smarty.const._SCORE}</td>
 </tr>
 {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from
 = $T_DATA_SOURCE}
 <tr class="defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
  <td>#filter:login-{$user.login}#{* ({$T_ROLES[$user.user_type]})*}</td>
  <td>{$user.specification}</td>
  <td class="centerAlign">{if $user.score}{$user.score}%{/if}</td>
 </tr>
 {foreachelse}
 <tr class="defaultRowHeight oddRowColor">
  <td class="emptyCategory" colspan="100%">{$smarty.const._NODATAFOUND}</td>
 </tr>
 {/foreach}
</table>
<!--/ajax:skillUsersTable-->
{/if} {/if}
<div id="graph_table" style="display: none">
<div id="proto_chart" class="proto_graph"></div>
</div>
{/capture} {if $T_CURRENT_SKILL_INFO} {eF_template_printBlock title =
"`$smarty.const._STATISTICSFORSKILL` <span
class='innerTableName'>&quot;`$T_CURRENT_SKILL_INFO->skill.description`&quot;</span>"
data = $smarty.capture.skill_statistics image = '32x32/reports.png' help
= 'Reports'} {else} {eF_template_printBlock title =
"`$smarty.const._SKILLSSTATISTICS`" data =
$smarty.capture.skill_statistics image = '32x32/reports.png' help =
'Reports'} {/if}
