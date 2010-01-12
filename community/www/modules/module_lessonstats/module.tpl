{*Smarty template*}
{capture name = 't_logins_list_code'}
            <div class = "blockHeader" style = "margin-top:10px">{$smarty.const._LESSONSTATS_LASTLOGINS}</div>
            <table>
            <tr>
                <th class = "topTitle leftAlign">{$smarty.const._LESSONSTATS_LOGIN}</th>
                <th class = "topTitle centerAlign">{$smarty.const._LESSONSTATS_LOGINTIME}</th>
                <th class = "topTitle centerAlign">{$smarty.const._LESSONSTATS_LOGINDURATION}</th>
            </tr>
            {section name = 'logins_list' loop = $T_USERLOGINS}
                <tr>
                    <td class = "leftAlign">{$T_USERLOGINS[logins_list].users_LOGIN}</td>
                    <td class = "centerAlign">#filter:timestamp_time-{$T_USERLOGINS[logins_list].timestamp}#</td>
                    <td class = "centerAlign">
                        {$T_USERLOGINS[logins_list].time.hours}h {$T_USERLOGINS[logins_list].time.minutes}' {$T_USERLOGINS[logins_list].time.seconds}''
                    </td>
                </tr>
            {/section}
            </table>
{/capture}
{eF_template_printBlock title=$smarty.const._LESSONSTATS data=$smarty.capture.t_logins_list_code absoluteImagePath = 1 image = $T_LESSONSTATS_BASELINK|cat:'images/column-reports.png'}



