{include file = "includes/header.tpl"}
        {if ( $T_ACCESS == '-1')}
            {eF_template_printMessage message=$smarty.const._YOUCANTDOTHESURVEYCONTACTLESSONPROFESSOR type='failure'}
        {else}
            {if ($T_SCREEN == '1')}
                {assign var = "title" value = '<a class="titleLink" href ="#">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$T_SURVEYNAME|cat:'</a>'}
                <table><tr><td>&nbsp;</td></tr></table>
                <table><tr><td>&nbsp;</td></tr></table>
                <table><tr><td>&nbsp;</td></tr></table>
                <table><tr><td>&nbsp;</td></tr></table>
                <table><tr><td>&nbsp;</td></tr></table>
                <table><tr><td>&nbsp;</td></tr></table>
                <table class="indexTable" valign="bottom" align="center">
                    {section name='survey_screen_1' loop=$T_SURVEY_INFO}
                        <tr><td class="externalSurvey"><img src='images/32x32/surveys.png' border='0px'></td><td class="externalSurvey" align="left"><h2>{$smarty.const._SURVEY}</h2></td>
                        <tr>
                        <td class="externalSurvey"></td><td class="externalSurvey" align="left"><h3>{$T_SURVEY_INFO[survey_screen_1].survey_name}</h3>
                                                <h4>{$T_SURVEY_INFO[survey_screen_1].survey_info}</h4></td>
                                                </tr>
                        <tr><td class="externalSurvey" colspan="2"><hr size="2px" colour="#99ff00"></td></tr>
                        <tr><td class="externalSurvey" colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td class="externalSurvey" colspan="2" align="center"><input class="flatButton" type="button" value="{$smarty.const._STARTSURVEY}" onclick="Javascript:self.location='{$smarty.const.G_SERVERNAME}external_survey.php?username={$smarty.get.username}&coupon={$smarty.get.coupon}&surveys_ID={$smarty.get.surveys_ID}&screen=2'"></td>
                        </tr>
                        <tr><td class="externalSurvey" colspan="2">&nbsp;</td></tr>
                    {/section}
                    </table>
          {/if}
          {if ($T_SCREEN == '2')}
            {assign var = "title" value = '<a class="titleLink" href ="#">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$T_SURVEYNAME|cat:'</a>'}
    {capture name = "t_survey_code"}
                    <center>
                    <table width="98%" border="0px">
                    <tr><td colspan="2" class="externalSurvey">{$smarty.const._SURVEY}</td></tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr class="handle">
                        <td class="tableImage" width="2%"><img src='images/32x32/surveys.png' border='0px'></td>
                        <td class="innerTableHeader" width="96%"><b><h3>{$T_SURVEYNAME}</h3><br>{$T_SURVEY_INFOTEXT}</b><br></td></tr>
                    </tr>
                    <tr><td colspan="2" class="horizontalSeparator"></td></tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    </table>
                    {eF_template_printSurvey questions=$T_SURVEY_QUESTIONS intro=$T_SURVEY_STARTTEXT user_type='external' coupon=$smarty.get.coupon username=$smarty.get.username surveys_ID=$smarty.get.surveys_ID}
    {/capture}
    {eF_template_printBlock title = $smarty.const._SURVEY data = $smarty.capture.t_survey_code image = '32x32/survey.png'}
         {/if}
         {if ($T_SCREEN == '3')}
            {assign var = "title" value = '<a class="titleLink" href ="#">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$T_SURVEYNAME|cat:'</a>'}
                {eF_template_printMessage message=$smarty.const._SURVEYSUBMISSIONSUCCESSFUL type='success'}
                {capture name = "t_survey_code_3"}
                 <table align="center" widht="100%">
                     {section name='survey_screen_3' loop=$T_SURVEY_INFO}
                         <tr>
                             <td class="externalSurve" align="center">{$T_SURVEY_INFO[survey_screen_3].end_text}</td>
                         </tr>
                     {/section}
                 </table>
                {/capture}
             {eF_template_printBlock title = $smarty.const._SURVEY data = $smarty.capture.t_survey_code_3 image = '32x32/survey.png'}
          {/if}
        {/if}

    {include file = "includes/closing.tpl"}
    </body>
 </html>
