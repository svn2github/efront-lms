{* Smarty template for import_export.php *}

{if $T_MESSAGE_TYPE == 'success'}
    <script>
        re = /\?/;
        !re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';            
    </script>
{/if}

{include file = "includes/header.tpl"}          {*The inclusion is put here instead of the beginning in order to speed up reloading, in case of success*}

{if $T_MESSAGE}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
{/if}
                                   


{if !$T_FINISH && $smarty.get.op == 'export'}
    {if isset($smarty.post.submit_export_settings)}     
        <table align = "center">
            <tr><td align = "center">
                {$smarty.const._SAVETHE} <br/><br/>
                <a href = "downloadfile.php?action=lesson_export&offset={$smarty.session.s_lessons_ID}&filename=data.tar.gz&lesson_name={$T_LESSON_NAME}.tar.gz" style = "font-weight:bold">{$smarty.const._DATAFILE}</a>
                <br/><br/>{$smarty.const._OFTHELESSONATYOURCOMPUTER}
            </td></tr>
        </table>
    {else}
        <table align = "center">
        <tr><td>
         <form action ="" method = "post">
                                        {$smarty.const._CHOOSEWHATTOEXPORT}
                                             <br/><input type = "checkbox" name = "export_periods"  checked /> {$smarty.const._LESSONPERIODS}
                                             <br/><input type = "checkbox" name = "export_comments"  checked /> {$smarty.const._COMMENTS}
                                             <br/><input type = "checkbox" name = "export_announcements"  checked /> {$smarty.const._ANNOUNCEMENTS}
                                             <br/><input type = "checkbox" name = "export_rules"  checked /> {$smarty.const._ACCESSRULES}
                                             <br/><input type = "checkbox" name = "export_calendar"  checked /> {$smarty.const._CALENDAR}
                                             <br/><input type = "checkbox" name = "export_glossary"  checked /> {$smarty.const._GLOSSARY}
                                             <br/><input type = "checkbox" name = "export_surveys"  checked /> {$smarty.const._SURVEYS}
                                        <br/><br/><input class = "flatButton" type = "submit" name = "submit_export_settings" value = "{$smarty.const._SUBMIT}" />
                                    </form>
        </td></tr></table>       
                                    
    {/if}
{elseif !$T_FINISH && $smarty.get.op == 'import'}

    <form action = "{$smarty.server.PHP_SELF}?op=import" method = "post" enctype = "multipart/form-data">
        <table align = "center">
            <tr><td colspan="2">{$smarty.const._DELETEEXISTINGDATAFROM}</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td><input type = "checkbox" name = "delete_content"        checked /></td><td> {$smarty.const._LESSONCONTENT}</td></tr>
            <tr><td><input type = "checkbox" name = "delete_periods"        checked /></td><td> {$smarty.const._LESSONPERIODS}</td></tr>
            <tr><td><input type = "checkbox" name = "delete_comments"       checked /></td><td> {$smarty.const._COMMENTS}     </td></tr>
            <tr><td><input type = "checkbox" name = "delete_announcements"  checked /></td><td> {$smarty.const._ANNOUNCEMENTS}</td></tr>
            <tr><td><input type = "checkbox" name = "delete_rules"          checked /></td><td> {$smarty.const._ACCESSRULES}  </td></tr>
            <tr><td><input type = "checkbox" name = "delete_calendar"       checked /></td><td> {$smarty.const._CALENDAR}     </td></tr>
            <tr><td><input type = "checkbox" name = "delete_glossary"       checked /></td><td> {$smarty.const._GLOSSARY}     </td></tr>
            <tr><td><input type = "checkbox" name = "delete_files"          checked /></td><td> {$smarty.const._LESSONFILES}  </td></tr>
            <tr><td><input type = "checkbox" name = "delete_surveys"        checked /></td><td> {$smarty.const._SURVEYS}      </td></tr>
        </table>
        <br/><br/>
        <table align = "center">
            <input type = "hidden" name = "lessons_ID" value = "{$T_LESSONS_ID}" />
            <tr><td>{$smarty.const._LESSONDATAFILE}</td>
                <td><input type = "file" name = "fileupload[0]" /></td></tr>
            <tr><td colspan = "2" align = "center">
                    <input class = "flatButton" type = "submit" name = "submit" value = "{$smarty.const._INSERTDATA}" onClick = "window.frames['upload'].location='upload_indicator.php'" />
                </td></tr>
        </table>
    </form>
{/if}

<center>
<iframe name = "upload" height = "60" frameborder = "0"></iframe>
</center>
</body>
</html>
