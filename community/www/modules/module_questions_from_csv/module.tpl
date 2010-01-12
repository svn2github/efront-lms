{*Smarty template*}
{literal}
<script>
function createQuestionsSelect(el) {
	category = el.value.split("_");
	
	if (category[0] != "lesson") {
		el.selectedIndex = 0;
		alert('{/literal}{$smarty.const._MODULE_QUESTIONS_PLEASESELECTALESSON}{literal}');
	}

}

</script>
{/literal}
{capture name = 't_history_xls_code'}
    {$T_HISTORY_XLS_IMPORT_FORM.javascript}
    <form {$T_HISTORY_XLS_IMPORT_FORM.attributes}>
    {$T_HISTORY_XLS_IMPORT_FORM.hidden}
    <table align="center">
    	{if isset($T_LESSON_SELECT)}
    	<tr><td class="labelCell">{$smarty.const._LESSON}:</td><td>{$T_LESSON_SELECT}</td></tr>
    	{/if}
        <tr><td class="labelCell">{$smarty.const._DATAFILE}:</td><td>{$T_HISTORY_XLS_IMPORT_FORM.hcd_file.html}</td></tr>
{*
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td class="labelCell">{$T_HISTORY_XLS_IMPORT_FORM.login_column_title.label}:</td><td>{$T_HISTORY_XLS_IMPORT_FORM.login_column_title.html}&nbsp;*</td></tr>
        <tr><td class="labelCell">{$T_HISTORY_XLS_IMPORT_FORM.date_column_title.label}:</td><td><table><tr><td>{$T_HISTORY_XLS_IMPORT_FORM.date_column_title.html}</td><td>&nbsp;({$smarty.const._MODULE_QUESTIONS_PLEASECONFIGUREDATE})</td></tr></table></td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
*}        
        <tr><td class="labelCell">{$T_HISTORY_XLS_IMPORT_FORM.hcd_ommit_users.label}:</td><td>{$T_HISTORY_XLS_IMPORT_FORM.hcd_ommit_users.html}</td></tr>
{*
        <tr><td class="labelCell">{$T_HISTORY_XLS_IMPORT_FORM.import_as.label}:</td><td>{$T_HISTORY_XLS_IMPORT_FORM.import_as.html}</td></tr>
*}
        <tr><td class="labelCell">{$T_HISTORY_XLS_IMPORT_FORM.report_existing.label}:</td><td>{$T_HISTORY_XLS_IMPORT_FORM.report_existing.html}</td></tr>

        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td></td><td>{$T_HISTORY_XLS_IMPORT_FORM.submit_hcd_import.html}</td>
        <tr><td colspan="2">&nbsp;</td></tr>

        <tr><td colspan="2" class="horizontalSeparator"></td></tr>
    </table>

    <table id = "branchInformation">
        <tr><td style="vertical-align:top"><b>{$smarty.const._NOTE}:</b>&nbsp;</td><td>{$smarty.const._MODULE_QUESTIONS_BOTTOMNOTE}</td></tr>
    </table>
    </form>

{/capture}

{eF_template_printBlock title=$smarty.const._MODULE_QUESTIONS_IMPORTUSERSHISTORYFROMXLSFILE data=$smarty.capture.t_history_xls_code absoluteImagePath=1 image=$T_XLS_HISTORY_MODULE_BASELINK|cat:'images/excel32.png'}


