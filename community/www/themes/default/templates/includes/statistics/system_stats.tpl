        {capture name='display_system_statistics'}
             <div class = "tabber">
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'system_traffic')} tabbertabdefault{/if}" title = "{$smarty.const._TRAFFIC}">
                    <form name = "systemperiod">
                    <table class = "statisticsSelectDate">
                        <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP start_year="-5" end_year="+0" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $T_FROM_TIMESTAMP display_seconds = false}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix="to_" time=$T_TO_TIMESTAMP start_year="-5" end_year="+0" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="to_" time = $T_TO_TIMESTAMP display_seconds = false}</td></tr>
                        <tr><td class = "labelCell"></td>
                            <td class = "elementCell"><a href = "javascript:void(0)" onclick = "showSystemStats('day')">{$smarty.const._LAST24HOURS}</a> - <a href = "javascript:void(0)" onclick = "showSystemStats('week')">{$smarty.const._LASTWEEK}</a> - <a href = "javascript:void(0)" onclick = "showSystemStats('month')">{$smarty.const._LASTMONTH}</a></td></tr>
                        <tr><td></td>
                         <td class = "elementCell"><input class = "inputCheckbox" type = "checkbox" id = "showLog" {if ( isset($T_SYSTEM_LOG))} "checked" {/if}>{$smarty.const._SHOWANALYTICLOG}</td></tr>
                        <tr><td></td>
                         <td class = "elementCell"><input class = "inputCheckbox" type = "checkbox" id = "showUsers" {if ( isset($smarty.get.showusers))} "checked"{/if}>{$smarty.const._SHOWALLUSERS}</td></tr>
                        <tr><td></td>
                         <td class = "elementCell"><input class = "inputCheckbox" type = "checkbox" id = "showLessons" {if ( isset($smarty.get.showlessons))} "checked"{/if}>{$smarty.const._SHOWALLLESSONS}</td></tr>
                        <tr><td></td>
                            <td class = "elementCell"><input type = "button" value = "{$smarty.const._SHOW}" class = "flatButton" onclick = "document.location='administrator.php?ctg=statistics&option=system&tab=system_traffic&from_year='+document.systemperiod.from_Year.value+'&from_month='+document.systemperiod.from_Month.value+'&from_day='+document.systemperiod.from_Day.value+'&from_hour='+document.systemperiod.from_Hour.value+'&from_min='+document.systemperiod.from_Minute.value+'&to_year='+document.systemperiod.to_Year.value+'&to_month='+document.systemperiod.to_Month.value+'&to_day='+document.systemperiod.to_Day.value+'&to_hour='+document.systemperiod.to_Hour.value+'&to_min='+document.systemperiod.to_Minute.value+'&showlog='+document.systemperiod.showLog.checked+'&showusers='+document.systemperiod.showUsers.checked+'&showlessons='+document.systemperiod.showLessons.checked"></td>
                        </tr>
                    </table>
                    </form>
                    <table class = "statisticsTools">
                       <tr><td id = "right">
                             {$smarty.const._ACCESSSTATISTICS}: <img class = "ajaxHandle" src = "images/16x16/reports.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2, 'graph_table');showGraph($('proto_chart'), 'graph_system_access');"/>
                             <div id = "graph_table" style = "display:none"><div id = "proto_chart" class = "proto_graph"></div></div>
                            </td></tr>
                    </table>
                    <table class = "statisticsGeneralInfo">
                        <tr><td class = "topTitle" colspan = "2">{$smarty.const._TOTALSTATISTICS}</td></tr>
                        <tr class = "{cycle name = 'active_users' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._TOTALLOGINS}:</td>
                            <td class = "elementCell">{$T_TOTAL_USER_ACCESSES}</td>
                        </tr>
                        <tr class = "{cycle name = 'active_users' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._TOTALACCESSTIME}:</td>
                            <td class = "elementCell">
                                {if $T_TOTAL_USER_TIME}
                                 {if $T_TOTAL_USER_TIME.hours}{$T_TOTAL_USER_TIME.hours}{$smarty.const._HOURSSHORTHAND} {/if}
                                 {if $T_TOTAL_USER_TIME.minutes}{$T_TOTAL_USER_TIME.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}
                                 {if $T_TOTAL_USER_TIME.seconds}{$T_TOTAL_USER_TIME.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}
                                {else}
                                 {$smarty.const._NODATAFOUND}
                                {/if}
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <table class = "statisticsTools">
                     <tr><td>{if $smarty.get.showusers}{$smarty.const._USERSACTIVITY}{else}{$smarty.const._MOSTACTIVEUSERS}{/if}</td>
                            <td id = "right">
         {$smarty.const._MOSTACTIVEUSERS}:<img class = "ajaxHandle" src = "images/16x16/reports.png" alt = "{$smarty.const._MOSTACTIVEUSERS}" title = "{$smarty.const._MOSTACTIVEUSERS}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2, 'graph_table');showGraph($('proto_chart'), 'graph_system_users_access');"/>
                            </td></tr>
                    </table>
                    <table class = "sortedTable">
                        <tr>
                            <td style = "width:40%;" class = "topTitle">{$smarty.const._USER}</td>
                            <td style = "width:30%;" class = "topTitle centerAlign">{$smarty.const._ACCESSNUMBER}</td>
                            <td style = "width:30%;" class = "topTitle centerAlign">{$smarty.const._TOTALACCESSTIME}</td>
                         </tr>
                        {foreach name='active_users' key = "login" item = "info" from=$T_ACTIVE_USERS}
                            <tr class = "{cycle name = 'active_users' values = 'oddRowColor, evenRowColor'} {if !$info.active}deactivatedTableElement{/if}">
                                <td><a class="editLink" href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$login}">#filter:login-{$login}#</a></td>
                                <td class = "centerAlign">{$info.accesses}</td>
                                <td class = "centerAlign">{strip}
                                 <span style = "display:none">{$info.seconds}&nbsp;</span>
                                    {if $info.seconds}
                                     {if $info.time.hours}{$info.time.hours}{$smarty.const._HOURSSHORTHAND} {/if}
                                     {if $info.time.minutes}{$info.time.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}
                                     {if $info.time.seconds}{$info.time.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}
                                    {else}
                                     {$smarty.const._NOACCESSDATA}
                                    {/if}
                                {/strip}</td>
                            </tr>
                        {/foreach}
                    </table>
{*
Commented out until we convert old log-based stats to time-based
                    <br/>
                    <table class = "statisticsTools">
                        <tr><td>{if $smarty.get.showlessons}{$smarty.const._LESSONSACTIVITY}{else}{$smarty.const._MOSTACTIVELESSONS}{/if}</td></tr>
     </table>
                    <table class = "sortedTable">
                        <tr>
                            <td style = "width:40%;" class = "topTitle">{$smarty.const._LESSON}</td>
                            <td style = "width:30%;" class = "topTitle centerAlign">{$smarty.const._TOTALACCESSTIME}</td>
                         </tr>
                        {foreach name='active_lessons' key = "id" item = "info" from=$T_ACTIVE_LESSONS}
                            <tr class = "{cycle name = 'active_lessons' values = 'oddRowColor, evenRowColor'} {if !$info.active}deactivatedTableElement{/if}">
                                <td>{$info.name}</td>
                                <td class = "centerAlign">{strip}
                                 <span style = "display:none">{$info.seconds}&nbsp;</span>
                                    {if $info.seconds}
                                     {if $info.time.hours}{$info.time.hours}{$smarty.const._HOURSSHORTHAND} {/if}
                                     {if $info.time.minutes}{$info.time.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}
                                     {if $info.time.seconds}{$info.time.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}
                                    {else}
                                     {$smarty.const._NOACCESSDATA}
                                    {/if}
                                {/strip}</td>
                            </tr>
                        {foreachelse}
                         <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                        {/foreach}
                    </table>
*}
    {if isset($T_SYSTEM_LOG)}
     <br/>
                    <table class = "statisticsTools">
                        <tr><td>{$smarty.const._ANALYTICLOG}</td></tr>
                    </table>
                    <table>
                  <tr>
                            <td class = "topTitle">{$smarty.const._LOGIN}</td>
                            <td class = "topTitle">{$smarty.const._LESSON}</td>
                            <td class = "topTitle">{$smarty.const._UNIT}</td>
                            <td class = "topTitle">{$smarty.const._ACTION}</td>
                            <td class = "topTitle">{$smarty.const._TIME}</td>
                            <td class = "topTitle">{$smarty.const._IPADDRESS}</td>
                        </tr>
                    {foreach name = 'lesson_log_loop' key = "key" item = "info" from = $T_SYSTEM_LOG}
                        <tr class = "{cycle name = 'lesson_log_list' values = 'oddRowColor, evenRowColor'}">
                            <td>#filter:login-{$info.users_LOGIN}#</td>
                            <td>{$info.lesson_name}</td>
                            <td>{$info.content_name}</td>
                            <td>{$T_ACTIONS[$info.action]}</td>
                            <td>#filter:timestamp_time-{$info.timestamp}#</td>
                            <td>{$info.session_ip|eF_decodeIp}</td>
                        </tr>
                    {/foreach}
                    </table>
    {/if}
                </div>

                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'user_types')} tabbertabdefault{/if}" title = "{$smarty.const._USERTYPES}">
                    <table class = "statisticsTools">
                        <tr><td id = "right">
         {$smarty.const._USERSKIND}:<img class = "ajaxHandle" src = "images/16x16/reports.png" alt = "{$smarty.const._USERSKIND}" title = "{$smarty.const._USERSKIND}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2, 'graph_table');showGraph($('proto_chart'), 'graph_system_user_types');"/>
                            </td></tr>
                    </table>
                    <table>
                     <tr>
                            <td class = "topTitle">{$smarty.const._USERTYPE}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._OVERALL}</tD>
                     </tr>
                    {foreach name = 'user_types' key = 'key' item = 'usertype' from = $T_USER_TYPES}
                        <tr class = "{cycle name = 'userkinds_info' values = 'oddRowColor, evenRowColor'}">
                            <td>
                             {if $usertype.user_type == 'administrator'}{$smarty.const._ADMINISTRATOR}
                             {elseif $usertype.user_type == 'professor'}{$smarty.const._PROFESSOR}
                             {elseif $usertype.user_type == 'student'}{$smarty.const._STUDENT}
                             {/if}
                            </td>
                            <td class = "centerAlign">{$usertype.num}</td>
                        </tr>
                    {/foreach}
                    </table>
                </div>
            </div>
        {/capture}
        {eF_template_printBlock title = $smarty.const._SYSTEMSTATISTICS data = $smarty.capture.display_system_statistics image = '32x32/reports.png' help = 'Reports'}
