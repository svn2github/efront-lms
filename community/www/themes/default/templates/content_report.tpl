{* smarty template for content_report.php *}


{include file = "includes/header.tpl"} {*The inclusion is put here instead of the beginning in order to speed up reloading, in case of success*}

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

{if !$T_MESSAGE_TYPE == 'success'}
{capture name = 't_report_code'}
{$T_REPORTS_FORM.javascript}
<form {$T_REPORTS_FORM.attributes}>
    {$T_REPORTS_FORM.hidden}
    <table align="center">
  <tr><td></td><td>
   <span>
   <img style="vertical-align:middle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
   <a href = "javascript:toggleEditor('notes','simpleEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
   </span>
  </td></tr>
  <tr><td align="right">{$T_REPORTS_FORM.notes.label}:&nbsp;</td>
  <td align="left">{$T_REPORTS_FORM.notes.html}</td></tr>
        {if $T_REPORTS_FORM.notes.error}<tr><td></td><td class = "formError">{$T_REPORTS_FORM.notes.error}</td></tr>{/if}

        <tr><td></td><td align="center">{$T_REPORTS_FORM.submit_report.html}</td></tr>
    </table>
</form>
{/capture}
 {eF_template_printBlock title = $smarty.const._CONTENTREPORT data = $smarty.capture.t_report_code image = '32x32/warning.png'}
{/if}


{include file = "includes/closing.tpl"}
