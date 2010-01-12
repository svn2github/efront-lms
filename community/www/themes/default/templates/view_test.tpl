{include file = "includes/header.tpl"}
{if $T_MESSAGE}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
{/if}

{if $smarty.get.test_analysis}
    {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=content&view_unit=`$smarty.get.view_unit`&test_analysis=1'>`$smarty.const._TESTANALYSISFORTEST` &quot;`$T_TEST_DATA->test.name`&quot;</a>"}

    {capture name = "t_test_analysis_code"}
        <div class = "headerTools">
        	<span>
                <img src = "images/16x16/arrow_left.png" alt = "{$smarty.const._VIEWSOLVEDTEST}" title = "{$smarty.const._VIEWSOLVEDTEST}">
                <a href = "{$smarty.server.PHP_SELF}?test_id={$T_TEST_DATA->test.id}&user={$T_TEST_DATA->completedTest.login}&show_solved_test={$smarty.get.show_solved_test}">{$smarty.const._VIEWSOLVEDTEST}</a>
            </span>
			{if $T_TEST_STATUS.testIds|@sizeof > 1}
            <span>
                <img src = "images/16x16/go_into.png" alt = "{$smarty.const._JUMPTOEXECUTION}" title = "{$smarty.const._JUMPTOEXECUTION}">
				{$smarty.const._JUMPTOEXECUTION}
				<select  style = "vertical-align:middle" onchange = "location.toString().match(/show_solved_test/) ? location = location.toString().replace(/show_solved_test=\d+/, 'show_solved_test='+this.options[this.selectedIndex].value) : location = location + '&show_solved_test='+this.options[this.selectedIndex].value">
					{foreach name = "test_analysis_list" item = "item" key = "key" from = $T_TEST_STATUS.testIds}
						<option value = "{$item}" {if $smarty.get.show_solved_test == $item}selected{/if}>#{$smarty.foreach.test_analysis_list.iteration} - #filter:timestamp_time-{$T_TEST_STATUS.timestamps[$key]}#</option>
					{/foreach}
				</select>
            </span>
            {/if}
        </div>
        <table style = "width:100%">
            <tr><td style = "vertical-align:top">{$T_CONTENT_ANALYSIS}</td></tr>
            <tr><td style = "vertical-align:top"><iframe width = "750px" id = "analysis_frame" height = "550px" frameborder = "no" src = "view_test.php?test_id={$T_TEST_DATA->test.id}&display_chart=1&user={$T_TEST_DATA->completedTest.login}&selected_unit={$smarty.get.selected_unit}&test_analysis=1&show_solved_test={$smarty.get.show_solved_test}"></iframe></td></tr>
        </table>
    {/capture}

    {eF_template_printBlock title = "`$smarty.const._TESTANALYSIS` `$smarty.const._FORTEST` <span class = "innerTableName">&quot;`$T_TEST_DATA->test.name`&quot;</span> `$smarty.const._ANDUSER` <span class = "innerTableName">&quot;#filter:login-`$T_TEST_DATA->completedTest.login`#&quot;</span>" data = $smarty.capture.t_test_analysis_code image='32x32/tests.png'}
{else}
	{$T_SOLVED_TEST}
{/if}


{include file = "includes/closing.tpl"}