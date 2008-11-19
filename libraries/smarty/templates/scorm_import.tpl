{* Smarty template for scorm_import.php *}

{if $T_MESSAGE_TYPE == 'success'}
    <script>
        re = /\?/;
        //!re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';            
    </script>
{/if}

{include file = "includes/header.tpl"}          {*The inclusion is put here instead of the beginning in order to speed up reloading, in case of success*}

{if $T_MESSAGE}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
{/if}

{if $smarty.post.submit_upload_scorm}
    {if $smarty.post.SCOonly}
        <form name = "SCOform" action="" method = "post">
        <table align = "center">
            <tr><td>{$smarty.const._UNITNAME}:</td>
                <td><input type = "text" name = "SCOname" value = ""/></td></tr>
            <tr><td>{$smarty.const._SELECTFILE}</td>
                <td>
                    <select name = "filename">
                        {html_options values = $T_FILENAMES output = $T_FILENAMES selected = $T_SUGGESTED_FILE}
                    </select>
                </td></tr>
            <tr><td>{$smarty.const._THESYSTEMSUGGESTSTHEFILES}:</td>
                <td>
        {section name = suggested_files_list loop = $T_SUGGESTED_FILENAMES}
                    {$T_SUGGESTED_FILENAMES[suggested_files_list]}<br/>
        {/section}
                </td></tr>
            <tr><td colspan = "2" align = "center">
                    <input class = "flatButton" type = "submit" name = "SCOsubmit" value = "OK"/>
                    <input type = "hidden" name = "lessons_ID" value = "{$smarty.get.lessons_ID}"/>
                    <input type = "hidden" name = "timestamp" value = "{$T_TIMESTAMP}"/>
                </td></tr>
        </table>
        </form>    
    {/if}

{elseif $smarty.post.SCOsubmit}
            <table align = "center">
                <tr><td align = "center">{$smarty.const._IMPORTOFUNIT} {$smarty.post.SCOname} {$smarty.const._COMPLETEDSUCCESFULLY}</td></tr>
            </table>    
{else}
            <br/>
            <form enctype = "multipart/form-data" name = "import_scorm_form" method = "post" action = "" >
            <table align = "center">
                <tr><td align = "center">
                    {$smarty.const._UPLOADTHESCORMFILEINZIPFORMAT}: <input name = "scorm_file[0]" type = "file"/> <br/>
                </td></tr>
                <!--<tr><td>{$smarty.const._CHECKIFITISASINGLESCO}: <input type = "checkbox" name = "SCOonly"/></td></tr>-->
                <tr><td align = "center">
                    <input class = "flatButton" type = "submit" name = "submit_upload_scorm" value = "{$smarty.const._SENDFILE}" /> 
                </td></tr>
            </table>
            </form>
{/if}