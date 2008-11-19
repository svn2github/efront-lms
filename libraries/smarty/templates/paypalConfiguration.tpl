{* Smarty template for paypalConfiguration.php *}

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

        {$T_CONFIG_FORM_DEFAULT.javascript}
        <form {$T_CONFIG_FORM_DEFAULT.attributes}>
            {$T_CONFIG_FORM_DEFAULT.hidden}
            <table class = "formElements" align="center">
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td class="labelCell">{$smarty.const._PAYPALBUSINESSMAIL}:&nbsp;</td>
                    <td>&nbsp;{$T_CONFIG_FORM_DEFAULT.paypalbusiness.html}</td>
                </tr>
                <tr>
                    <td class="labelCell">{$smarty.const._PAYPALMAILSTUDENTS}:&nbsp;</td>
                    <td>{$T_CONFIG_FORM_DEFAULT.mailstudents.html}</td>
                </tr>
                <tr>
                    <td class="labelCell">{$smarty.const._PAYPALMAILPROFESSORS}:&nbsp;</td>
                    <td>{$T_CONFIG_FORM_DEFAULT.mailprofessors.html}</td>
                </tr>
                <tr>
                    <td class="labelCell">{$smarty.const._PAYPALMAILADMINS}:&nbsp;</td>
                    <td>{$T_CONFIG_FORM_DEFAULT.mailadmins.html}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><br />{$T_CONFIG_FORM_DEFAULT.submit_config.html}</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </form>
</html>