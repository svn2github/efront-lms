{* template functions for inner table *}
{capture name = 't_BBB_list_code'}
    <table border = "0" width = "100%">
        <tr class = "topTitle">
            <td class = "topTitle">{$smarty.const._BBB_NAME}</td>
            <td class = "topTitle" width="20%">{$smarty.const._BBB_DATE}</td>
            <td class = "topTitle" width="10%">{$smarty.const._BBBDURATION}</td>
            <td class = "topTitle">{$smarty.const._BBB_STATUS}</td>
            <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
        </tr>

        {foreach name =BBB item =meeting from = $T_BBB_INNERTABLE}
        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
            <td>{if $T_BBB_CURRENTLESSONTYPE != "student"}<a href = "{$T_BBB_MODULE_BASEURL}&edit_BBB={$meeting.id}" class = "editLink">{$meeting.name}</a>{else}{$meeting.name}{/if}</td>
            <td><span title = " #filter:timestamp_time-{$meeting.timestamp}#">{$meeting.time_remaining}</span></td>
            <td>{$meeting.durationHours}:{if $meeting.durationMinutes == 0}00{else}{$meeting.durationMinutes}{/if}</td>
            <td>{if $meeting.status == "0"}{$smarty.const._BBBNOTSTARTED}{elseif $meeting.status == "1"}{$smarty.const._BBBSTARTED}{else}{$smarty.const._BBBFINISHED}{/if}</td>
            <td align = "center">

            {if $meeting.status == 2}
                <img border = "0" src = "{$T_BBB_MODULE_BASELINK}images/server_client_exchange.png" class = "inactiveImage" title = "{$smarty.const._BBBFINISHED}" alt = "{$smarty.const._BBBFINISHED}" />
            {else}
                {if $T_BBB_CURRENTLESSONTYPE == "student"}
                    {if $meeting.status == "0"}
                     <img border = "0" src = "{$T_BBB_MODULE_BASELINK}images/server_client_exchange.png" class = "inactiveImage" title = "{$smarty.const._BBBJOINMEETING}" alt = "{$smarty.const._BBBJOINMEETING}" />
                    {elseif $meeting.status == "1" }
                     <a href = "{$meeting.joiningUrl}" class = "editLink"><img border = "0" src = "{$T_BBB_MODULE_BASELINK}images/server_client_exchange.png" title = "{$smarty.const._BBBJOINMEETING}" alt = "{$smarty.const._BBBJOINMEETING}" /></a>
                    {/if}
                {else}
                    {if $meeting.status == "0" && !$meeting.mayStart}
                     <img border = "0" src = "{$T_BBB_MODULE_BASELINK}images/server_client_exchange.png" class = "inactiveImage" title = "{$smarty.const._BBBJOINMEETING}" alt = "{$smarty.const._BBBJOINMEETING}" />
                    {elseif $meeting.mayStart}
                     <a href = "{$T_BBB_CREATEMEETINGURL}" class = "editLink" onClick="return confirm('{$smarty.const._BBB_AREYOUSUREYOUWANTTOSTARTTHECONFERENCE}')"><img border = "0" src = "{$T_BBB_MODULE_BASELINK}images/server_client_exchange.png" title = "{$smarty.const._BBBSTARTMEETING}" alt = "{$smarty.const._BBBSTARTMEETING}" /></a>
                    {/if}
                {/if}
            {/if}
            </td>

        </tr>
        {foreachelse}
        <tr><td colspan="5" class = "emptyCategory">{$smarty.const._BBBNOMEETINGSCHEDULED}</td></tr>
        {/foreach}
    </table>
{/capture}


{eF_template_printBlock title=$smarty.const._BBB_BBBLIST data=$smarty.capture.t_BBB_list_code absoluteImagePath=1 image=$T_BBB_MODULE_BASELINK|cat:'images/BBB32.png' options=$T_MODULE_BBB_INNERTABLE_OPTIONS}
