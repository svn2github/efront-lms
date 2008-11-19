{* Smarty template for scorm_export.php *}

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


<table style = "width: 100%;margin-top:15px">
    <tr><td style = "text-align:center">
        {$smarty.const._SAVETHE}<br/><br/><a href="{$smarty.const.SCORM_FOLDER}/{$T_SCORM_FILENAME}" title = "{$smarty.const._SCORMFILE}">{$smarty.const._SCORMFILE}</a> <br/><br/>{$smarty.const._OFTHELESSONATYOURCOMPUTER}
    </td></tr>
</table>
