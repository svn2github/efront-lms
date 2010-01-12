{* template functions for inner table *}
{capture name = 't_dimdim_list_code'}
    <table border = "0" width = "100%">
        <tr class = "topTitle">
            <td class = "topTitle">{$smarty.const._DIMDIM_NAME}</td>
            <td class = "topTitle" width="20%">{$smarty.const._DIMDIM_DATE}</td>
            <td class = "topTitle" width="10%">{$smarty.const._DIMDIMDURATION}</td>
            <td class = "topTitle">{$smarty.const._DIMDIM_STATUS}</td>
            <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
        </tr>

        {foreach name =dimdim item =meeting from = $T_DIMDIM_INNERTABLE}
        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
            <td>{if $T_DIMDIM_CURRENTLESSONTYPE != "student"}<a href = "{$T_DIMDIM_MODULE_BASEURL}&edit_dimdim={$meeting.id}" class = "editLink">{$meeting.name}</a>{else}{$meeting.name}{/if}</td>
            <td><span title = " #filter:timestamp_time-{$meeting.timestamp}#">{$meeting.time_remaining}</span></td>
            <td>{$meeting.durationHours}:{if $meeting.durationMinutes == 0}00{else}{$meeting.durationMinutes}{/if}</td>
            <td>{if $meeting.status == "0"}{$smarty.const._DIMDIMNOTSTARTED}{elseif $meeting.status == "1"}{$smarty.const._DIMDIMSTARTED}{else}{$smarty.const._DIMDIMFINISHED}{/if}</td>
            <td align = "center">

            {if $meeting.status == 2}
                <img border = "0" src = "{$T_DIMDIM_MODULE_BASELINK}images/server_client_exchange.png" class = "inactiveImage" title = "{$smarty.const._DIMDIMFINISHED}" alt = "{$smarty.const._DIMDIMFINISHED}" />
            {else}
                {if $T_DIMDIM_CURRENTLESSONTYPE == "student"}
                    {if $meeting.status == "0"}
                    	<img border = "0" src = "{$T_DIMDIM_MODULE_BASELINK}images/server_client_exchange.png" class = "inactiveImage" title = "{$smarty.const._DIMDIMJOINMEETING}" alt = "{$smarty.const._DIMDIMJOINMEETING}" />
                    {elseif $meeting.status == "1" }
                    	<a href = "{$meeting.joiningUrl}" class = "editLink"><img border = "0" src = "{$T_DIMDIM_MODULE_BASELINK}images/server_client_exchange.png" title = "{$smarty.const._DIMDIMJOINMEETING}" alt = "{$smarty.const._DIMDIMJOINMEETING}" /></a>
                    {/if}
                {else}
                    {if $meeting.status == "0" && !$meeting.mayStart}
                    	<img border = "0" src = "{$T_DIMDIM_MODULE_BASELINK}images/server_client_exchange.png" class = "inactiveImage" title = "{$smarty.const._DIMDIMJOINMEETING}" alt = "{$smarty.const._DIMDIMJOINMEETING}" />
                    {elseif $meeting.mayStart}
                    	<a href = "{$T_DIMDIM_MODULE_BASEURL}&start_meeting={$meeting.id}" class = "editLink" onClick="return confirm('{$smarty.const._DIMDIM_AREYOUSUREYOUWANTTOSTARTTHECONFERENCE}')"><img border = "0" src = "{$T_DIMDIM_MODULE_BASELINK}images/server_client_exchange.png" title = "{$smarty.const._DIMDIMSTARTMEETING}" alt = "{$smarty.const._DIMDIMSTARTMEETING}" /></a>
                    {/if}
                {/if}
            {/if}
            </td>

        </tr>
        {foreachelse}
        <tr><td colspan="5" class = "emptyCategory">{$smarty.const._DIMDIMNOMEETINGSCHEDULED}</td></tr>
        {/foreach}
    </table>
{/capture}


{eF_template_printBlock title=$smarty.const._DIMDIM_DIMDIMLIST data=$smarty.capture.t_dimdim_list_code absoluteImagePath=1 image=$T_DIMDIM_MODULE_BASELINK|cat:'images/dimdim32.png' options=$T_MODULE_DIMDIM_INNERTABLE_OPTIONS}
