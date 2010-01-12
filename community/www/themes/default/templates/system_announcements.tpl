{* smarty template for system_announcements.php *}

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

{if !$T_MESSAGE_TYPE == 'success' && isset($smarty.get.op)}
{$T_NEWS_FORM.javascript}
<form {$T_NEWS_FORM.attributes}>
    {$T_NEWS_FORM.hidden}
    <table class = "formElements">
        <tr><td class = "labelCell">{$T_NEWS_FORM.title.label}:&nbsp;</td>
            <td>{$T_NEWS_FORM.title.html}</td></tr>
        {if $T_NEWS_FORM.title.error}<tr><td></td><td class = "formError">{$T_NEWS_FORM.title.error}</td></tr>{/if}

        <tr><td class = "labelCell">{$T_NEWS_FORM.data.label}:&nbsp;</td>
            <td>{$T_NEWS_FORM.data.html}</td></tr>
        {if $T_NEWS_FORM.data.error}<tr><td></td><td class = "formError">{$T_NEWS_FORM.data.error}</td></tr>{/if}

        <tr><td colspan = "100%" class = "submitCell">
                {$T_NEWS_FORM.submit_news.html}</td></tr>
    </table>
</form>
{elseif !isset($smarty.get.op) && $T_MESSAGE_TYPE != 'failure'}
    {eF_template_printBlock title=$T_ANNOUNCEMENT.title data=$T_ANNOUNCEMENT.data image='32x32/announcements.png'}
{/if}