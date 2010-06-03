

{capture name = "t_calendar_code"}
    {if $smarty.get.add_calendar || $smarty.get.edit_calendar}
            {$T_ADD_EVENT_FORM.javascript}
            <form {$T_ADD_EVENT_FORM.attributes}>
                {$T_ADD_EVENT_FORM.hidden}
                <table class = "formElements" style = "margin-left:0px;width:100%">
                    <tr><td class = "labelCell">{$T_ADD_EVENT_FORM.event_date.label}:&nbsp;</td>
                        <td style = "width:100%">{$T_ADD_EVENT_FORM.event_date.html}</td></tr>
     <tr><td></td>
      <td><span>
       <img style="vertical-align:middle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
       <a href = "javascript:toggleEditor('event','simpleEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
      </span></td></tr>
                    <tr><td class = "labelCell">{$T_ADD_EVENT_FORM.event.label}:&nbsp;</td>
                        <td style = "width:100%">{$T_ADD_EVENT_FORM.event.html}</td></tr>

                    {if $smarty.const.G_VERSIONTYPE == 'enterprise' || $smarty.session.s_type != "student" }
                    <tr><td class = "labelCell">{$T_ADD_EVENT_FORM.lesson.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_ADD_EVENT_FORM.lesson.html}</td></tr>
                    {/if}
                    {if $T_ADD_EVENT_FORM.event.error}<tr><td></td><td class = "formError">{$T_ADD_EVENT_FORM.event.error}</td></tr>{/if}
                    <tr><td colspan = "100%">&nbsp;</td></tr>
                    <tr><td></td>
                        <td align="center">
                            <table><tr><td align="center">{$T_ADD_EVENT_FORM.submit_event.html}</td>{if !isset($smarty.get.edit_calendar)}<td align="center">{$T_ADD_EVENT_FORM.submit_event_add_another.html}</td>{/if}</tr></table>
                        </td>
                    </tr>
                </table>
            </form>
  {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location;</script>
  {/if}

    {else}

        <table width = "100%">
            <tr><td colspan = "100%">
    {if !$T_POPUP_MODE && !$smarty.get.popup}
                    <table style = "width:100%">
                        <tr>
                        {if !isset($T_CURRENT_USER->coreAccess.calendar) || $T_CURRENT_USER->coreAccess.calendar == 'change'}
                            {if $smarty.const.G_VERSIONTYPE == 'enterprise' || $smarty.session.s_type != "student"}
                                <td style = "width:1%"><a href = "{$smarty.session.s_type}.php?ctg=calendar&view_calendar={$T_VIEW_CALENDAR}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&add_calendar=1{$T_CALENDAR_TYPE_LINK}" onclick = "eF_js_showDivPopup('{$smarty.const._ADDEVENT}', 2)" target = "POPUP_FRAME"><img border="0" src = "images/16x16/add.png" title="{$smarty.const._ADDEVENT}" alt="{$smarty.const._ADDEVENT}"/></a></td>
                                <td>&nbsp;<a href = "{$smarty.session.s_type}.php?ctg=calendar&view_calendar={$T_VIEW_CALENDAR}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&add_calendar=1{$T_CALENDAR_TYPE_LINK}" id="add_new_event_link" onclick = "eF_js_showDivPopup('{$smarty.const._ADDEVENT}', 2)" target = "POPUP_FRAME">{$smarty.const._ADDEVENT}</a></td>
                            {/if}
                        {/if}
                        </tr>
                    </table>
    {/if}
                </td></tr>
        </table>
    <table width="100%">
        <tr><td style = "vertical-align:top;" width="148px">
            <table>
                <tr><td>
                    {eF_template_printCalendar events=$T_CALENDAR_EVENTS timestamp=$T_VIEW_CALENDAR ctg = 'calendar'}
                </td></tr>
            </table>
            </td>
            <td style = "vertical-align:top;">
        <table class = "sortedTable" width = "100%" >
            <tr class = "topTitle">
                <td class = "topTitle" width="15%">{$smarty.const._DATE}</td>
                <td class = "topTitle" width="{if ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_TYPE != "0") || ($smarty.const.G_VERSIONTYPE != 'enterprise')}50{else}75{/if}%">{$smarty.const._EVENT}</td>
        {if ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_TYPE != "4") || ($smarty.const.G_VERSIONTYPE != 'enterprise')}
                <td class = "topTitle" width="25%">{$smarty.const._LESSON}</td>
        {/if}
        {if $smarty.const.G_VERSIONTYPE == 'enterprise' && ($T_TYPE == "0" || $T_TYPE == "4") || $smarty.session.s_type != "student"}
                <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
  {/if}
   </tr>
        {foreach name = 'timestamps_list' key = timestamp item = events from = $T_INTERVAL_CALENDAR_EVENTS}
            {section name='events_list' loop=$events.id}
             <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
                <td style = "white-space:nowrap;"><span style = "display:none">{$timestamp}</span>#filter:timestamp_time_nosec-{$timestamp}#</td>
                <td>{$events.data[events_list]}</td>
                {if ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_TYPE != "4") || ($smarty.const.G_VERSIONTYPE != 'enterprise')}
                <td>{$events.lesson_name[events_list]}</td>
                {/if}
                {if $smarty.const.G_VERSIONTYPE == 'enterprise' && ($T_TYPE == "0" || $T_TYPE == "4") || $smarty.session.s_type != "student"}
                <td style = "text-align:center">
                {if $smarty.session.s_type != "student" || $smarty.session.s_login == $events.users_login[events_list]}
                    <a href = "{$smarty.server.PHP_SELF}?ctg=calendar&view_calendar={$T_VIEW_CALENDAR}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&edit_calendar={$events.id[events_list]}{$T_CALENDAR_TYPE_LINK}" onclick = "eF_js_showDivPopup('{$smarty.const._EDITEVENT}', 2)" target = "POPUP_FRAME"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDITEVENT}" title = "{$smarty.const._EDITEVENT}" border = "0"></a>
                    <a href = "{$smarty.server.PHP_SELF}?ctg=calendar&view_calendar={$T_VIEW_CALENDAR}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&delete_calendar={$events.id[events_list]}{$T_CALENDAR_TYPE_LINK}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEEVENT}" title = "{$smarty.const._DELETEEVENT}" border = "0"></a>
                {/if}
                </td>
                {/if}
            </tr>
            {/section}
        {foreachelse}
            <tr class = "defaultRowHeight oddRowColor"><td colspan = "100%" class = "emptyCategory">
                {if $smarty.get.show_interval == 'week'}
                    {$smarty.const._NOEVENTSPLANNEDTHISWEEK}
                {elseif $smarty.get.show_interval == 'month'}
                    {$smarty.const._NOEVENTSPLANNEDTHISMONTH}
                {elseif $smarty.get.show_interval == 'all'}
                    {$smarty.const._NOEVENTSPLANNED}
                {else}
                    {$smarty.const._NOEVENTSPLANNEDTODAY}
                {/if}
            </td></tr>
        {/foreach}
        </table>
        </td></tr>
    </table>
    {/if}
{/capture}
