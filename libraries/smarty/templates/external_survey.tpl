<html>
<head>
    <meta http-equiv = "Content-Language" content = "{$smarty.const._HEADERLANGUAGETAG}" />
    <meta http-equiv = "keywords"         content = "education" />
    <meta http-equiv = "description"      content = "Collaborative Elearning Platform" />
    <meta http-equiv = "Content-Type"     content = "text/html; charset = utf-8"/>
    <link href = "images/32x32/book_open.png" rel = "shortcut icon" />
    <link rel = "stylesheet" type = "text/css" href = "css/css_global.css" />   
    <title>eFront - {$smarty.const._THENEWFORMOFADDITIVELEARNING}::{$smarty.const._SURVEY}</title>
</head>
    <body>
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
                        <tr><td class="externalSurvey"><img src='images/32x32/form_green.png' border='0px'></td><td class="externalSurvey" align="left"><h2>{$smarty.const._SURVEY}</h2></td>
                        <tr>
                        <td class="externalSurvey"></td><td class="externalSurvey" align="left"><h3>{$T_SURVEY_INFO[survey_screen_1].survey_name}</h3>
                                                <h4>{$T_SURVEY_INFO[survey_screen_1].survey_info}</h4></td>
                                                </tr>
                        <tr><td class="externalSurvey" colspan="2"><hr size="2px" colour="#99ff00"></td></tr>
                        <tr><td class="externalSurvey" colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td class="externalSurvey" colspan="2" align="center"><input class="flatButton" type="button" value="{$smarty.const._STARTSURVEY}" onclick="Javascript:self.location='/external_survey.php?email={$smarty.get.email}&coupon={$smarty.get.coupon}&surveys_ID={$smarty.get.surveys_ID}&screen=2'"></td>
                        </tr>
                        <tr><td class="externalSurvey" colspan="2">&nbsp;</td></tr>
                    {/section}
                    </table>
          {/if}
          {if ($T_SCREEN == '2')}
            {assign var = "title" value = '<a class="titleLink" href ="#">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$T_SURVEYNAME|cat:'</a>'}
                    <center>
                    <table width="98%" border="0px">
                    <tr><td colspan="2" class="externalSurvey">{$smarty.const._SURVEY}</td></tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr class="handle">
                        <td class="tableImage" width="2%"><img src='images/32x32/form_green.png' border='0px'></td>
                        <td class="innerTableHeader" width="96%"><b><h3>{$T_SURVEYNAME}</h3><br>{$T_SURVEY_INFOTEXT}</b><br></td></tr>
                    </tr>
                    <tr><td colspan="2" class="horizontalSeparator"></td></tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    </table>
                    {eF_template_printSurvey questions=$T_SURVEY_QUESTIONS intro=$T_SURVEY_STARTTEXT user_type='external' coupon=$smarty.get.coupon email=$smarty.get.email surveys_ID=$smarty.get.surveys_ID}
         {/if}
         {if ($T_SCREEN == '3')}
            {assign var = "title" value = '<a class="titleLink" href ="#">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$T_SURVEYNAME|cat:'</a>'}
                {eF_template_printMessage message=$smarty.const._SURVEYSUBMISSIONSUCCESSFUL type='success'}
                <table align="center" widht="100%">    
                    {section name='survey_screen_3' loop=$T_SURVEY_INFO}
                        <tr>
                            <td class="externalSurve" align="center">{$T_SURVEY_INFO[survey_screen_3].end_text}</td>
                        </tr>   
                    {/section}
                </table>
          {/if}
        {/if}
    </body>
 </html>
