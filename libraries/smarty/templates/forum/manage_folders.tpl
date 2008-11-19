{* Smarty template for manage_folders.php *}

{include file = "includes/header.tpl"}

{if $T_MESSAGE}
    {if $T_MESSAGE_TYPE == 'success'}
        <script>
            parent.location = 'forum/messages_index.php?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';
        </script>
    {else}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
    {/if}
{/if}

{if $smarty.get.action == 'add'}
    <form name = "create_folder" method = "post" action = "{$smarty.server.PHP_SELF}?action=add">
    <table align = "center">
        <tr><td align = "center"><b>{$smarty.const._NEWFOLDERNAME}: </b><input type = "text" name = "folder_name" value = ""/></td></tr>
        <tr><td align = "center"><input class = "flatButton" type = "submit" name = "submit_create" value = {$smarty.const._CREATE} /></td></tr>
    </table>
    </form>

{elseif $smarty.get.action == 'edit'}
    <form name = "modify_folder" method = "post" action = "{$smarty.server.PHP_SELF}?action=add">
    <table align = "center">
        <tr><td align = "center"><b>{$smarty.const._FOLDERNAME}: </b><input type = "text" name = "folder_name" value = "{$T_FOLDER}"/></td></tr>
        <tr><td align = "center"><input class = "flatButton" type = "submit" name = "submit_modify" value = {$smarty.const._MODIFY} /></td></tr>
    </table>
    <input type = "hidden" name = "folder_id" value = "{$smarty.get.id}" />
    </form>

{elseif $smarty.get.action == 'statistics'}
    {capture name = 't_folder_statistics_code'}
        <table width = "100%">
            <tr class = "defaultRowHeight topTitle">
                <td class = "topTitle">{$smarty.const._FOLDER}</td>
                <td class = "topTitle">{$smarty.const._MESSAGES}</td>
                <td class = "topTitle">{$smarty.const._FILES}</td>
                <td class = "topTitle">{$smarty.const._SIZE}</td></tr>
            {section name = "folders_loop" loop = $T_FOLDERS}
            <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
                <td>{$T_FOLDERS[folders_loop].name}</td>
                <td>{$T_FOLDERS[folders_loop].msgs}</td>
                <td>{$T_FOLDERS[folders_loop].files_number}</td>
                <td>{$T_FOLDERS[folders_loop].files_size} KB</td></tr>
            {/section}
        </table>
    {/capture}
    {eF_template_printInnerTable title = $smarty.const._FOLDERSTATISTICS data = $smarty.capture.t_folder_statistics_code image = "32x32/chart.png"}
{/if}
<br/>

