{* Smarty template for show_print_friendly.tpl *}
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


{if $T_MESSAGE_TYPE != 'failure'}
<table style = "width:100%;">
    <tr><td style = "padding-top:10px;padding-bottom:15px;text-align:center">
        <input class = "flatButton" type = "submit" onClick = "window.print()" value = "{$smarty.const._PRINTIT}"/>
    </td></tr>
</table>    
{/if}



{foreach name = "content_list" item = item key = key from = $T_CONTENT}
<table style = "width:100%;">
    <tr><td>
        {eF_template_printInnerTable title = $T_PARENT_LIST[$key] data = $item image = '/32x32/printer.png'}
    </td></tr>
</table>    
<br/>
{/foreach}

