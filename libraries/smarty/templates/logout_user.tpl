{* smarty template for logout user *}

{include file = "includes/header.tpl"}

{if $T_MESSAGE}
    {if $T_MESSAGE_TYPE == 'success'}
        <script>
            re = /\?/;
            !re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';            
        </script>
    {else}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
    {/if}
{/if}

{$T_LOGOUT_USER_FORM.javascript}
<form {$T_LOGOUT_USER_FORM.attributes}>
{$T_LOGOUT_USER_FORM.hidden}
<table class = "formElements">
    <tr><td class = "labelCell">{$smarty.const._CHOOSEUSERTODISCONNECT}:&nbsp;</td><td>{$T_LOGOUT_USER_FORM.user_type.html}</td></tr>
    <tr><td colspan = "2">&nbsp;</td></tr>
    <tr><td></td><td>{$T_LOGOUT_USER_FORM.submit_logout_user.html}</td></tr>    
</table>




