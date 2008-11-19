{* smarty file for emails.php *}

{capture name = "t_calendar_code"}
    {if $smarty.get.add_calendar || $smarty.get.edit_calendar}
            {$T_ADD_EVENT_FORM.javascript}
            <form {$T_ADD_EVENT_FORM.attributes}>
                {$T_ADD_EVENT_FORM.hidden}
                <table class = "formElements" style = "margin-left:0px;width:100%">
                    <tr><td class = "labelCell">{$T_ADD_EVENT_FORM.event_date.label}:&nbsp;</td>
                        <td style = "width:100%">{$T_ADD_EVENT_FORM.event_date.html}</td></tr>
                    <tr><td class = "labelCell">{$T_ADD_EVENT_FORM.event.label}:&nbsp;</td>
                        <td style = "width:100%">{$T_ADD_EVENT_FORM.event.html}</td></tr>

                    {if $T_MODULE_HCD_INTERFACE || $smarty.session.s_type != "student" }
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
    {else}

        <table width = "100%">
            <tr><td colspan = "100%">
    {if !$T_POPUP_MODE && !$smarty.get.popup}
                    <table style = "width:100%">
                        <tr>
                        {if !isset($T_CURRENT_USER->coreAccess.calendar) || $T_CURRENT_USER->coreAccess.calendar == 'change'}
                            {if $T_MODULE_HCD_INTERFACE || $smarty.session.s_type != "student"}
                                <td style = "width:1%"><a href = "{$smarty.session.s_type}.php?ctg=calendar&view_calendar={$T_VIEW_CALENDAR}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&add_calendar=1{$T_CALENDAR_TYPE_LINK}" onclick = "eF_js_showDivPopup('{$smarty.const._ADDEVENT}', new Array('650px','350px'))" target = "POPUP_FRAME"><img border="0" src = "images/16x16/add2.png" title="{$smarty.const._ADDEVENT}" alt="{$smarty.const._ADDEVENT}"/></a></td>
                                <td>&nbsp;<a href = "{$smarty.session.s_type}.php?ctg=calendar&view_calendar={$T_VIEW_CALENDAR}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&add_calendar=1{$T_CALENDAR_TYPE_LINK}" id="add_new_event_link" onclick = "eF_js_showDivPopup('{$smarty.const._ADDEVENT}', new Array('650px','350px'))" target = "POPUP_FRAME">{$smarty.const._ADDEVENT}</a></td>
                            {/if}
                        {/if}

                        {if $T_MODULE_HCD_INTERFACE && $T_TYPE != "3"}
                        <td align = "right">
                            {$smarty.const._CALENDARTYPE}:&nbsp; {$T_CALENDAR_TYPE_SELECT}
                        </td>
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
                <td class = "topTitle" width="{if ($T_MODULE_HCD_INTERFACE && $T_TYPE != "0") || (!$T_MODULE_HCD_INTERFACE)}50{else}75{/if}%">{$smarty.const._EVENT}</td>
        {if ($T_MODULE_HCD_INTERFACE && $T_TYPE != "0") || (!$T_MODULE_HCD_INTERFACE)}
                <td class = "topTitle" width="25%">{$smarty.const._LESSON}</td>
        {/if}
                <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td></tr>
        {foreach name = 'timestamps_list' key = timestamp item = events from = $T_INTERVAL_CALENDAR_EVENTS}
            {section name='events_list' loop=$events.id}
             <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
                <td style = "white-space:nowrap;"><span style = "display:none">{$timestamp}</span>#filter:timestamp_time_nosec-{$timestamp}#</td>
                <td>{$events.data[events_list]}</td>
                {if ($T_MODULE_HCD_INTERFACE && $T_TYPE != "0") || (!$T_MODULE_HCD_INTERFACE)}
                <td>{$events.lesson[events_list]}</td>
                {/if}
                <td style = "text-align:center">
                    {if $T_MODULE_HCD_INTERFACE && $T_TYPE == "0" || $smarty.session.s_type != "student"}
                    <a href = "{$smarty.server.PHP_SELF}?ctg=calendar&view_calendar={$T_VIEW_CALENDAR}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&edit_calendar={$events.id[events_list]}{$T_CALENDAR_TYPE_LINK}" onclick = "eF_js_showDivPopup('{$smarty.const._EDITEVENT}', new Array('650px','350px'))" target = "POPUP_FRAME"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDITEVENT}" title = "{$smarty.const._EDITEVENT}" border = "0"></a>
                    <a href = "{$smarty.server.PHP_SELF}?ctg=calendar&view_calendar={$T_VIEW_CALENDAR}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&delete_calendar={$events.id[events_list]}{$T_CALENDAR_TYPE_LINK}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETEEVENT}" title = "{$smarty.const._DELETEEVENT}" border = "0"></a>
                    {else}
                        <img border = "0" src = "images/16x16/edit_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                        <img border = "0" src = "images/16x16/delete_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                    {/if}
                </td>
            </tr>
            {/section}
        {foreachelse}
            <tr class = "defaultRowHeight"><td colspan = "100%" class = "centerAlign oddRowColor emptyCategory">
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