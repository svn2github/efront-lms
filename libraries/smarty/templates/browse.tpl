{* Template file for browse2.php *}

{include file = "includes/header.tpl"}

{if $T_MESSAGE}
    {if $T_MESSAGE_TYPE == 'success' || $T_RELOAD_PARENT}
        <script>
            re = /\?/;
            !re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';            
        </script>
    {else}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
    {/if}
{/if}

<table>
{if isset($T_PARENT_DIR)}
    <tr><td><img src = "images/16x16/folder_up.png" alt = "{$smarty.const._FOLDERUP}" title = "{$smarty.const._FOLDERUP}"/></td>
        <td><a href = "{$smarty.server.PHP_SELF}?for_type={$smarty.get.for_type}{if $T_PARENT_DIR}&directory={$T_PARENT_DIR}{/if}" alt = "{$smarty.const._UPONELEVEL}" title = "{$smarty.const._UPONELEVEL}">.. {$smarty.const._UPONELEVEL}</a></td></tr>
{/if}
{foreach name = 'files_list' key = key item = item from = $T_FILES}
    {if $item.type == 'directory'}
        <tr>
            <td><img src = "images/16x16/folder.png" alt = "{$smarty.const._FOLDER}" title = "{$smarty.const._FOLDER}"/></td>
            <td><a href = "{$smarty.server.PHP_SELF}?for_type={$smarty.get.for_type}&directory={$item.url_path}" title = "{$item.name}">{$item.name}</a></td>
            <td></td>
        </tr>
    {else}
        <tr>
            <td><img src = "{$item.image}" alt = "{$item.mime_type}" title = "{$item.mime_type}"/></td>
            <td><a href = "javascript:void(0)" onclick = "setValue('{if $item.id != -1}{$item.id}{else}{$item.url_path}{/if}', '{$item.physical_name}')" title = "{$item.name}">{$item.name}</a></td>
            <td style = "white-space:nowrap">&nbsp;({$item.size} {$smarty.const._KB})</td>
        </tr>
    {/if}
{foreachelse}
    <tr><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOFILESFOUND}</td></tr>
{/foreach}
</table>
<script>
{literal}
function setValue(id, name) {
    if (top.document.getElementById('src')) {
        //top.document.getElementById('src').value='view_file.php?file='+id;
        top.document.getElementById('src').value='{/literal}{$smarty.const.G_RELATIVECONTENTLINK}{$T_OFFSET}/{literal}'+name;
    } else if (top.document.getElementById('href')) {
        top.document.getElementById('href').value='{/literal}{$smarty.const.G_RELATIVECONTENTLINK}{$T_OFFSET}/{literal}'+name;
    } else if (top.document.getElementById('file')) {
        top.document.getElementById('file').value=name;
        top.document.getElementById('codebase').value='{/literal}{$smarty.const.G_RELATIVECONTENTLINK}{$T_OFFSET}/{literal}';
    }
}
{/literal}
</script>

{include file = "includes/closing.tpl"}