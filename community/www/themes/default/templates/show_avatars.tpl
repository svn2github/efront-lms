{* smarty file for show_avatars.php *}
{include file = "includes/header.tpl"}

<table width = "100%" cellpadding = "5"> 
    <tr>
{section name = 'avatars_list' loop = $T_SYSTEM_AVATARS}
        <td align = "center">
            <a href = "javascript:void(0)" onclick = "top.mainframe.document.getElementById('select_avatar').selectedIndex = {$smarty.section.avatars_list.index}{if $T_SOCIAL_INTERFACE}+1{/if};top.mainframe.document.getElementById('popup_close').onclick();window.close();">
            <img src = "{$smarty.const.G_SYSTEMAVATARSURL}{$T_SYSTEM_AVATARS[avatars_list]}" border = "0" / >
            <br/>{$T_SYSTEM_AVATARS[avatars_list]}</a>
        </td>
    {if $smarty.section.avatars_list.iteration % 4 == 0}
        </tr><tr>
    {/if}
{/section}
    </tr>
</table>

</body>
</html>