{*$smarty template for send_file.php*}

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



<form action="send_file.php" method="post" enctype="multipart/form-data" class = "formElements">
<table style = "width:100%">
    <tr>
        <td class = "labelCell"> {$smarty.const._FILE}:&nbsp;</td>
        <td class = "elementCell"><input type="file" name="fileupload" size="40">
        </td>
    </tr>
    <tr><td colspan = "2">&nbsp;</td></tr>
    <tr><td></td><td class = "submitCell">
        <input class = "flatButton" type = "submit" name="submit" value="{$smarty.const._SENDFILE}" onClick="window.frames['upload'].location='upload_indicator.php'">&nbsp;
    </td></tr>
</table>

<input type="hidden" name="lessons_ID" value="{$smarty.session.s_lessons_ID}">
<input type="hidden" name="to_dir" value="{$T_DIR}">

{ if ($T_TESTS_ID) }
    <input type="hidden" name="tests_ID" value="{$TESTS_ID}">
    <input type="hidden" name="q_ID" value="{$T_QUESTION_ID}">
{/if}

</form>
<iframe name="upload" height="60" frameborder="0"></iframe>
