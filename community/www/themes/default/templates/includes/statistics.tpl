{if !isset($T_OPTION)}
        <table class = "statisticsPanel">
            <tr><td>



                    {eF_template_printBlock title = $smarty.const._STATISTICS columns = 4 links = $T_STATISTICS_OPTIONS image = '32x32/options.png' help = 'Reports'}

            </td></tr>
        </table>
{elseif $T_OPTION == 'user'}
 {include file = "includes/statistics/users_stats.tpl"}

{elseif $T_OPTION == 'lesson'}
 {include file = "includes/statistics/lessons_stats.tpl"}

{elseif $T_OPTION == 'course'}
 {include file = "includes/statistics/courses_stats.tpl"}

{elseif $T_OPTION == 'test'}
 {include file = "includes/statistics/tests_stats.tpl"}

{elseif $T_OPTION == 'feedback'}
 {include file = "includes/statistics/feedback_stats.tpl"}

{elseif $T_OPTION == 'system'}
 {include file = "includes/statistics/system_stats.tpl"}

{elseif $T_OPTION == 'custom'}
 {include file = "includes/statistics/custom_stats.tpl"}

{elseif $T_OPTION == 'certificate'}
 {include file = "includes/statistics/certificate_stats.tpl"}

{elseif $T_OPTION == 'events'}
 {include file = "includes/statistics/events_stats.tpl"}

{elseif $T_OPTION == 'groups'}
 {include file = "includes/statistics/groups_stats.tpl"}

{elseif $T_OPTION == 'branches'}
 {include file = "includes/statistics/branches_stats.tpl"}

{elseif $T_OPTION == 'participation'}
 {include file = "includes/statistics/participation_stats.tpl"}

{elseif $T_OPTION == 'advanced_user_reports'}
 {include file = "includes/statistics/advanced_user_reports.tpl"}

{/if}
