{* Smarty template for forum_admin.php *}

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

{$T_CONFIGURATION_FORM.javascript}
<form {$T_CONFIGURATION_FORM.attributes}>
{$T_CONFIGURATION_FORM.hidden}
    <table class = "formElements">
        <tr><td class = "labelCell">{$smarty.const._ALLOWHTMLFPM}:&nbsp;</td>
            <td class = "elementCell">{$T_CONFIGURATION_FORM.allow_html.html}</td></tr>
            {if $T_CONFIGURATION_FORM.allow_html.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.allow_html.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._ACTIVATEPOLLS}:&nbsp;</td>
            <td class = "elementCell">{$T_CONFIGURATION_FORM.polls.html}</td></tr>
            {if $T_CONFIGURATION_FORM.polls.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.polls.error}</td></tr>{/if}
{*        <tr><td class = "labelCell">{$smarty.const._ALLOWATTACHMENTSINF}:&nbsp;</td>
            <td class = "elementCell">{$T_CONFIGURATION_FORM.forum_attachments.html}</td></tr>
            {if $T_CONFIGURATION_FORM.forum_attachments.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.forum_attachments.error}</td></tr>{/if}
*}
        <tr><td class = "labelCell">{$smarty.const._USERSMAYADDFORUMS}:&nbsp;</td>
            <td class = "elementCell">{$T_CONFIGURATION_FORM.students_add_forums.html}</td></tr>
            {if $T_CONFIGURATION_FORM.students_add_forums.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.students_add_forums.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._PMQUOTA}:&nbsp;</td>
            <td class = "elementCell">{$T_CONFIGURATION_FORM.pm_quota.html}</td></tr>
        <tr><td></td><td class = "infoCell">{$smarty.const._BLANKFORUNLIMITED}</td></tr>
            {if $T_CONFIGURATION_FORM.pm_quota.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.pm_quota.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._PMATTACHMENTSQUOTA}:&nbsp;</td>
            <td class = "elementCell">{$T_CONFIGURATION_FORM.pm_attach_quota.html}</td></tr>
        <tr><td></td><td class = "infoCell">{$smarty.const._BLANKFORUNLIMITED}</td></tr>
            {if $T_CONFIGURATION_FORM.pm_attach_quota.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.pm_attach_quota.error}</td></tr>{/if}
        <tr><td colspan = "2">&nbsp;</td></tr>
            <td></td><td class = "submitCell">{$T_CONFIGURATION_FORM.submit_settings.html}</td></tr>
    </table>
</form>
