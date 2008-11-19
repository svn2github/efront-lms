{* smarty template for news.php *}

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
{$T_NEWS_FORM.javascript}
<form {$T_NEWS_FORM.attributes}>
    {$T_NEWS_FORM.hidden}
    <table class = "formElements">
        <tr><td class = "labelCell">{$T_NEWS_FORM.title.label}:&nbsp;</td>
            <td class = "elementCell">{$T_NEWS_FORM.title.html}</td></tr>
        {if $T_NEWS_FORM.title.error}<tr><td></td><td class = "formError">{$T_NEWS_FORM.title.error}</td></tr>{/if}

        <tr><td class = "labelCell">{$T_NEWS_FORM.data.label}:&nbsp;</td>
            <td class = "elementCell">{$T_NEWS_FORM.data.html}</td></tr>
        {if $T_NEWS_FORM.data.error}<tr><td></td><td class = "formError">{$T_NEWS_FORM.data.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
            <td class = "elementCell">{eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP start_year="-2" end_year="+2" field_order = 'YMD'} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $T_FROM_TIMESTAMP display_seconds = false}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._SENDASEMAILALSO}:&nbsp;</td>
                        <td class = "elementCell">{$T_NEWS_FORM.email.html}</td></tr>
                        {if $T_NEWS_FORM.email.error}<tr><td></td><td class = "formError">{$T_NEWS_FORM.email.error}</td></tr>{/if}    
            
        <tr><td colspan = "100%">
                &nbsp;</td></tr>
        <tr><td></td>
            <td class = "submitCell">
                {$T_NEWS_FORM.submit_news.html}</td></tr>
    </table>
</form>
{elseif !isset($smarty.get.op) && $T_MESSAGE_TYPE != 'failure'}
    {eF_template_printInnerTable title=$T_ANNOUNCEMENT.title data=$T_ANNOUNCEMENT.data image='/32x32/news.png' options=$T_NEWS_OPTIONS}
{/if}


