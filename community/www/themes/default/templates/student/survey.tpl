    {assign var = "category" value = 'lessons'}

    {*moduleSurvey: The survey page*}
{capture name = "moduleSurvey"}
                <tr><td class="moduleCell">
    {if ( $T_DO_TEST == '-1')}
            {eF_template_printMessageSurvey message=$smarty.const._YOUCANTDOTHESURVEYCONTACTLESSONPROFESSOR type='failure'}
			{capture name = "t_survey_code"}
				<br /><br />
				<div class = "centerAlign"><input class="flatButton" type="button" value="{$smarty.const._RETURN}" onclick="Javascript:self.location='student.php'"></div>
			{/capture}
			{eF_template_printBlock title = $smarty.const._SURVEY data = $smarty.capture.t_survey_code image = '32x32/survey.png'}
        
	{else}
        {if ($smarty.get.screen_survey == '1')}
		{capture name = "t_survey_code"}		
            <br><br><br><br>
            <table class = "testHeader" width="50%">
             <tr><td>
            <table class = "surveyInfo" align="center" valign="baseline">
             <tr><td rowspan = "3"><img src = "images/32x32/surveys.png" alt = "{$smarty.const._SURVEY}" title = "{$smarty.const._SURVEY}"/></td>
                  <td id = "testInfoLabels"></td></tr>
                   {section name='survey_screen_1' loop=$T_SURVEY_INFO}
                       <tr><td align="center"><h3>{$T_SURVEY_INFO[survey_screen_1].survey_name}&nbsp;</h3></td></tr>
                       <tr><td align="center"><h4>{$T_SURVEY_INFO[survey_screen_1].survey_info}&nbsp;</h4></td></tr>
                    {/section}
            </table></td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                    <td align="center"><input class="flatButton" type="button" value="{$smarty.const._STARTSURVEY}" onclick="Javascript:self.location='student.php?ctg=survey&screen_survey=2&surveys_ID={$smarty.get.surveys_ID}'"></td>
                </tr>
             </table>
		{/capture}	
		{eF_template_printBlock title = $smarty.const._SURVEY data = $smarty.capture.t_survey_code image = '32x32/survey.png'}   		
        {/if}
        {if ($smarty.get.screen_survey == '2')}
		{capture name = "t_survey_code"}
            <table class="innerTable" width="100%">
                <tr class="handle">
                    <td class="tableImage"><img src='images/32x32/surveys.png' border='0px'></td>
                    <td><b>{$T_SURVEYNAME}<br>{$T_SURVEY_INFOTEXT}</b><br></td>
                </tr>
            </table>
            {eF_template_printSurvey questions=$T_SURVEY_QUESTIONS user_type=$T_USER intro=$T_SURVEY_STARTTEXT}
		{/capture}
			{eF_template_printBlock title = $smarty.const._SURVEY data = $smarty.capture.t_survey_code image = '32x32/survey.png'}
            {*{eF_template_printSurvey data=$T_SURVEY_INFO questions=$T_SURVEY_QUESTIONS user_type=$T_USER} *}
        {/if}
        {if ($smarty.get.screen_survey == '3')}
            {eF_template_printMessageSurvey message=$smarty.const._SURVEYSUBMISSIONSUCCESSFUL type='success'}
        {capture name = "t_survey_code"}
			<br><br><br><br>
            <table class = "testHeader" width="50%">
             <tr><td>
            <table class = "surveyInfo" align="center" valign="baseline">
             <tr><td rowspan = "2"><img src = "images/32x32/surveys.png" alt = "{$smarty.const._SURVEY}" title = "{$smarty.const._SURVEY}"/></td>
                  <td id = "testInfoLabels"></td></tr>
                  {section name='survey_screen_3' loop=$T_SURVEY_INFO}
                       <tr> <td>{$T_SURVEY_INFO[survey_screen_3].end_text}</td></tr>
                    {/section}
            </table></td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                    <td><input class="flatButton" type="button" value="{$smarty.const._RETURN}" onclick="Javascript:self.location='student.php'"></td>
                </tr>
             </table>
            </table>
		{/capture}	
		{eF_template_printBlock title = $smarty.const._SURVEY data = $smarty.capture.t_survey_code image = '32x32/survey.png'}
        {/if}
	{/if}
                </td></tr>
	{/capture}
   
