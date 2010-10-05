{*Smarty template*}

{capture name = 't_change_login_code'}
 {eF_template_printForm form = $T_TOOLS_FORM}
    <div id = "module_administrator_tools_autocomplete_users_div" class = "autocomplete"></div>
{/capture}

{capture name = 't_global_settings_code'}
        {eF_template_printBlock title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_GLOBALLESSONSETTINGS columns = 4 links = $T_LESSON_SETTINGS image='32x32/lessons.png' main_options = $T_TABLE_OPTIONS groups = $T_LESSON_SETTINGS_GROUPS}
{/capture}

{capture name = 't_sql_code'}
 {eF_template_printForm form = $T_SQL_FORM}
 <div id = "sql_output_area" style = "width:100%;border:1px dotted black;height:400px">
 {if isset($T_SQL_RESULT)}
 <table>
 {foreach name = 'sql_results_loop' item = "row" key = "key" from = $T_SQL_RESULT}
  {if $smarty.foreach.sql_results_loop.first}
  <tr class = "topTitle" style = "border-top:0px">
  {else}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
  {/if}
   {foreach name = "row_loop" item = "column" key = "foo" from = $row}
    <td style = "padding:0px 3px 0px 3px">{$column}</td>
   {/foreach}
  </tr>
  {if $smarty.foreach.sql_results_loop.last}
  <tr><td colspan = "100%">{$smarty.foreach.sql_results_loop.total} {$smarty.const._MODULE_ADMINISTRATOR_TOOLS_ROWSINSET}</td></tr>
  {/if}
 {foreachelse}
   {if isset($T_SQL_AFFECTED_ROWS)}
    {$smarty.const._MODULE_ADMINISTRATOR_TOOLS_QUERYOK}, {$T_SQL_AFFECTED_ROWS} {$smarty.const._MODULE_ADMINISTRATOR_TOOLS_ROWSAFFECTED}
   {else}
    {$smarty.const._MODULE_ADMINISTRATOR_TOOLS_EMPTYSET}
   {/if}
 {/foreach}
 </table>
 {/if}
 </div>
{/capture}

{capture name = 't_bulk_courses_code'}

{/capture}

{capture name = 't_administrator_tools_code'}
 <div class = "tabber">
  {eF_template_printBlock tabber = "change_login" title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_CHANGELOGIN data = $smarty.capture.t_change_login_code absoluteImagePath=1 image=$T_MODULE_ADMINISTRATOR_TOOLS_BASELINK|cat:'images/tools.png'}
  {eF_template_printBlock tabber = "global_settings" title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_GLOBALLESSONSETTINGS data = $smarty.capture.t_global_settings_code absoluteImagePath=1 image=$T_MODULE_ADMINISTRATOR_TOOLS_BASELINK|cat:'images/tools.png'}
        {eF_template_printBlock tabber = "sql" title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_SQLINTERFACE data = $smarty.capture.t_sql_code image='32x32/generic.png'}
        {eF_template_printBlock tabber = "bulk_completion" title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_BULKCOMPLETECOURSES data = $smarty.capture.t_bulk_courses_code image='32x32/courses.png'}
 </div>
{/capture}
{eF_template_printBlock title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS data = $smarty.capture.t_administrator_tools_code absoluteImagePath=1 image=$T_MODULE_ADMINISTRATOR_TOOLS_BASELINK|cat:'images/tools.png'}
