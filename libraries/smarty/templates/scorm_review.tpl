{* Smarty template for scorm_review.php *}

{include file = "includes/header.tpl"}
<br/>
<form name = "scorm_review_form" action = "" method = "post">
<table align = "center">
    <tr><td>{$smarty.const._CHOOSESTUDENT}:</td>
        <td>
            <select name = "student_select">
                {html_options values = $T_STUDENTS selected = $smarty.post.student_select output = $T_STUDENT_NAMES}
            </select>
        </td></tr>
    <tr><td colspan = "2" align = "center"><input class = "flatButton" type = "submit" name = "student_submit" value = "OK"/></td></tr>
</table>
</form>

{if $smarty.post.student_submit}
    <table align = "center" border = "0" cellspacing = "4" cellpadding = "4">
    {if $T_SCORM_DATA}
        <tr><td align = "center">
            <form name = "reset_all_data_form" method = "post" action = "">
            <input type = "hidden" name = "student_select" value = "{$smarty.post.student_select}" />
            <input class = "flatButton" type = "submit" name = "reset_all_data" value = "Reset ALL Data" style = "border: 1px solid black">
            </form>
        </td></tr>
    {/if}
    {section name = 'scorm_data_list' loop = $T_SCORM_DATA}
        <tr><td>
            <form name = "scorm_unit_form" method = "post" action = "">
            <table align = "center" border = "1" rules = "none" cellspacing = "0" cellpadding = "4" width = "100%">
                <tr style = "border-bottom:1px solid black">
                    <th colspan = "6" align = "center">SCORM data for unit: {$T_SCORM_DATA[scorm_data_list].name}</th>
                </tr>
                <tr style = "background-color:#eeeeee">
                    <td align = "center"><b>{$smarty.const._ENTRY} <br />(entry)</td>
                    <td align = "center"><b>{$smarty.const._TOTALTIME} <br />(total_time)</td>
                    <td align = "center"><b>{$smarty.const._LESSONSTATUS} <br />(lesson_status)</td>
                    <td align = "center"><b>{$smarty.const._MINSCORE} <br />(minscore)</td>
                    <td align = "center"><b>{$smarty.const._MAXSCORE} <br />(maxscore)</td>
                    <td align = "center"><b>{$smarty.const._FINALSCORE} <br />(score)</td></tr>
                </tr>
                <tr style = "background-color:#eeeeee">
                    <td align = "center">{$T_SCORM_DATA[scorm_data_list].entry}</td>
                    <td align = "center">{$T_SCORM_DATA[scorm_data_list].total_time}</td>
                    <td align = "center">{$T_SCORM_DATA[scorm_data_list].lesson_status}</td>
                    <td align = "center">{$T_SCORM_DATA[scorm_data_list].minscore}</td>
                    <td align = "center">{$T_SCORM_DATA[scorm_data_list].maxscore}</td>
                    <td align = "center">{$T_SCORM_DATA[scorm_data_list].score}</td></tr>
                </tr>
                <tr style = "background-color:#eeeeee;border-top:1px solid black;"><td colspan = "6" align = "center">
                    <input class = "flatButton" type = "submit" name = "student_submit" value = "Reset Data" style = "border: 1px solid black">
                </td></tr>
            </table>
            <input type = "hidden" name = "student_select" value = "{$smarty.post.student_select}" />
            <input type = "hidden" name = "reset_content_ID" value = "{$T_SCORM_DATA[scorm_data_list].content_ID}" />
            </form>
        </td></tr>
        <tr><td>&nbsp;</td></tr>
    {sectionelse}
                <tr><td class = "empty_category">{$smarty.const._NOSCORMDATAFOUNDFORTHISSTUDENT}!</td></tr>
    {/section}
    </table>

{/if}