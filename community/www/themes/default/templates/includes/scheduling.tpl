{capture name = "moduleInsertPeriod"}
    <tr><td class = "moduleCell">
        {capture name='t_insert_period_code'}
        	<script>var noscheduleset = '{$smarty.const._NOSCHEDULESET}';</script>
            {$T_ADD_PERIOD_FORM.javascript}
            <form {$T_ADD_PERIOD_FORM.attributes}>
                {$T_ADD_PERIOD_FORM.hidden}
                <table class = "formElements">
                    <tr><td class = "labelCell">{$smarty.const._CURRENTSCHEDULE}:&nbsp;</td>
                    {if $T_CURRENT_LESSON->lesson.from_timestamp}
                        <td class = "elementCell">
                            {$smarty.const._FROM} #filter:timestamp_time-{$T_CURRENT_LESSON->lesson.from_timestamp}# {$smarty.const._TO} #filter:timestamp_time-{$T_CURRENT_LESSON->lesson.to_timestamp}# &nbsp;&nbsp;&nbsp;
                            {if !isset($T_CURRENT_USER->coreAccess.settings) || $T_CURRENT_USER->coreAccess.settings == 'change'}<img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETESCHEDULE}" alt = "{$smarty.const._DELETESCHEDULE}" onclick = "deleteSchedule(this)">{/if}
                        </td>
                    {else}
                        <td class = "elementCell emptyCategory">{$smarty.const._NOSCHEDULESET}</td>
                    {/if}
                    </tr>
                {if !isset($T_CURRENT_USER->coreAccess.settings) || $T_CURRENT_USER->coreAccess.settings == 'change'}
                    <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
                        <td class = "elementCell">{eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP start_year="-2" end_year="+2" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $T_FROM_TIMESTAMP display_seconds = false}</td></tr>
                    <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
                        <td class = "elementCell">{eF_template_html_select_date prefix="to_"   time=$T_TO_TIMESTAMP   start_year="-2" end_year="+2" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="to_"   time = $T_TO_TIMESTAMP   display_seconds = false}</td></tr>
{*                    <tr><td class = "labelCell">{$T_ADD_PERIOD_FORM.shift.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_ADD_PERIOD_FORM.shift.html}</td></tr>*}
                    <tr><td></td>
                    	<td class = "submitCell">{$T_ADD_PERIOD_FORM.submit_add_period.html}</td></tr>
                {/if}
                </table>
            </form>
            {/capture}
            {eF_template_printBlock title=$smarty.const._ADDPERIOD data=$smarty.capture.t_insert_period_code image='32x32/schedule.png'}
    </td></tr>

{/capture}
