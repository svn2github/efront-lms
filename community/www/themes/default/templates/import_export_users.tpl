{* Smarty template for import_export_users.php *}

{if $smarty.get.close}
<script language = "JavaScript">
<!--
    self.opener.location.reload(); 
    window.close();
//-->
</script>
{/if}


{if $T_MESSAGE}                            
    {eF_template_printMessage message=$T_MESSAGE type = $T_MESSAGE_TYPE}
    <center>{eF_template_printCloseButton reload = false}</center>
    <meta http-equiv = "refresh" CONTENT = "5;URL=/import_export_users.php?close=true" />
{/if}                                    

{if $smarty.get.oper == 'export'}
    <center>
    <table>
        <tr><td align = "center">{$smarty.const._SAVETHE}
                <a href = "downloadfile.php?filename={$T_CSVNAME}&action=backup">{$smarty.const._DATAFILE}</a> {$smarty.const._ATYOURCOMPUTER}<br/><br/>
                <input class = "flatButton" type = "submit" onClick = "window.close()" value = "{$smarty.const._CLOSEWINDOW}"/>
            </td></tr>
    </table>
    </center>
{else if $smarty.get.oper == 'import' && $T_MESSAGE == ''}
    {$T_IMPORT_USERS_FORM.javascript}
        <center>
            <form {$T_IMPORT_USERS_FORM.attributes}>
                {$T_IMPORT_USERS_FORM.hidden}
                <table class = "formElements">
                    <tr><td class = "labelCell">{$smarty.const._DATAFILE}:&nbsp;</td><td>{$T_IMPORT_USERS_FORM.users_file.html}</td></tr>
                    {if $T_IMPORT_USERS_FORM.users_file.error}<tr><td></td><td class = "formError">{$T_IMPORT_USERS_FORM.users_file.error}</td></tr>{/if}
                    <tr><td class = "labelCell">{$smarty.const._KEEPEXISTINGUSERS}:&nbsp;</td><td>{$T_IMPORT_USERS_FORM.replace_users.keep.html}</td></tr>
                    <tr><td class = "labelCell">{$smarty.const._REPLACEEXISTINGUSERS}:&nbsp;</td><td>{$T_IMPORT_USERS_FORM.replace_users.replace.html}</td></tr>
                    <tr><td colspan = "100%" class = "submitCell">{$T_IMPORT_USERS_FORM.submit_import_users.html}</td></tr>        
                </table>
            </form>
        </center>
{/if}

<center>
    <iframe name = "upload" height = "60" frameborder = "0"></iframe>
</center>
