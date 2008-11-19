{* smarty template for comments.php *}

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

{if !$T_MESSAGE_TYPE == 'success' && isset($smarty.get.op)}
{$T_COMMENTS_FORM.javascript}

<form {$T_COMMENTS_FORM.attributes}>
    {$T_COMMENTS_FORM.hidden}
    <table align="center">
        <tr><td align="center">{$T_COMMENTS_FORM.data.label}:&nbsp;</td></tr>
        <tr><td align="center">{$T_COMMENTS_FORM.data.html}</td></tr>
        {if $T_COMMENTS_FORM.data.error}<tr><td></td><td class = "formError">{$T_COMMENTS_FORM.data.error}</td></tr>{/if}

        <tr><td colspan = "100%" class = "submitCell" align="center">
                {$T_COMMENTS_FORM.submit_comments.html}</td></tr>
    </table>
</form>
{/if}
