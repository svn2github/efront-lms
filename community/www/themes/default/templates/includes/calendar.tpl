{capture name = calendar_list}
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'calendarTable'}
<!--ajax:calendarTable-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" order="desc" id = "calendarTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=calendar&view_calendar={$smarty.get.view_calendar}&show_interval={$smarty.get.show_interval}&">
   <tr class = "topTitle">
    <td name = "timestamp" class = "topTitle">{$smarty.const._DATE}</td>
    <td name = "data" class = "topTitle">{$smarty.const._EVENT}</td>
    <td name = "type" class = "topTitle">{$smarty.const._TYPE}</td>
    <td name = "users_LOGIN" class = "topTitle">{$smarty.const._CREATOR}</td>
   {if $_change_}
    <td class = "topTitle centerAlign noSort">{$smarty.const._TOOLS}</td>
   {/if}
   </tr>
  {foreach name = 'calendar_events_list' key = id item = event from = $T_DATA_SOURCE}
    <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
    <td><span style = "display:none">{$event.timestamp}</span>#filter:timestamp_time_nosec-{$event.timestamp}#</td>
    <td>{$event.data}</td>
    <td>{if $event.type}{$event.type}{else}{$smarty.const._GLOBAL}{/if}</td>
    <td>#filter:login-{$event.users_LOGIN}#</td>
    <td class = "centerAlign nowrap">
   {if ($smarty.session.s_type == 'administrator' || $smarty.session.s_login == $event.users_LOGIN) && $_change_}
     <a href = "{$smarty.server.PHP_SELF}?ctg=calendar&edit={$id}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._EDITEVENT}', 2)" target = "POPUP_FRAME"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDITEVENT}" title = "{$smarty.const._EDITEVENT}" class = "hande"></a>
     <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEEVENT}" title = "{$smarty.const._DELETEEVENT}" class = "ajaxHandle" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteEntity(this, '{$id}')">
   {/if}
    </td>
   </tr>
  {foreachelse}
   <tr class = "defaultRowHeight oddRowColor"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
  </table>
<!--/ajax:calendarTable-->
 {/if}
{/capture}

{capture name = "moduleCalendarPage"}
 <tr><td class = "moduleCell">
 {if $smarty.get.add || $smarty.get.edit}
     {capture name = 't_add_code'}
   {eF_template_printForm form = $T_ENTITY_FORM_ARRAY}

   {if $T_MESSAGE_TYPE == 'success' && !$smarty.post.submit_another}
       <script>parent.location = parent.location;</script>
   {/if}
  {/capture}
  {eF_template_printBlock title = $smarty.const._ADDCALENDAR data = $smarty.capture.t_add_code image = '32x32/calendar.png' help ='calendar'}
  <div id = "autocomplete_calendar" class = "autocomplete"></div>
 {else}
  {capture name = 't_calendar_page_code'}
   {if $_change_}
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" title="{$smarty.const._ADDEVENT}" alt="{$smarty.const._ADDEVENT}"/>
     <a href = "{$smarty.server.PHP_SELF}?ctg=calendar&view_calendar={$smarty.get.view_calendar}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&add=1&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._ADDEVENT}', 2)" target = "POPUP_FRAME">{$smarty.const._ADDEVENT}</a>
    </span>
   </div>
   {/if}
   <table style = "width:100%">
    <tr>
     <td style = "vertical-align:top">{eF_template_printCalendar events=$T_SORTED_CALENDAR_EVENTS timestamp=$T_VIEW_CALENDAR }</td>
     <td style = "width:100%;vertical-align:top">{$smarty.capture.calendar_list}</td>
    </tr>
   </table>
  {/capture}
  {eF_template_printBlock title = $smarty.const._CALENDAR data = $smarty.capture.t_calendar_page_code image = '32x32/calendar.png' main_options=$T_CALENDAR_OPTIONS help ='calendar'}
 {/if}
 </td></tr>
{/capture}
