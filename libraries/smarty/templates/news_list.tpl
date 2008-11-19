    <table class = "sortedTable" width = "100%">
        <tr class = "defaultRowHeight">
            <td class = "topTitle">{$smarty.const._BODY}</td>
            <td class = "topTitle">{$smarty.const._TITLE}</td>
            <td class = "topTitle">{$smarty.const._DATE}</td>
            <td class = "topTitle">{$smarty.const._USERCAPITAL}</td>
    {if $smarty.session.s_type != 'student' && (!$T_CURRENT_USER->coreAccess.news || $T_CURRENT_USER->coreAccess.news == 'change')}
            <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td></tr>
    {/if}
    {section name = 'news_list' loop = $T_NEWS}
        <tr class = "defaultRowHeight {cycle values = "oddRowColor,evenRowColor"}">
            <td style = "padding-right:13px;">{$T_NEWS[news_list].title}</td>
            <td style = "padding-right:13px;">{$T_NEWS[news_list].data}</td>
            <td style = "padding-right:13px;white-space:nowrap"><span style = "display:none">{$T_NEWS[news_list].timestamp}</span>#filter:timestamp_time-{$T_NEWS[news_list].timestamp}#</td>
            <td style = "padding-right:13px;">#filter:user_login-{$T_NEWS[news_list].users_LOGIN}#</td>
        {if $smarty.session.s_type != 'student' && (!$T_CURRENT_USER->coreAccess.news || $T_CURRENT_USER->coreAccess.news == 'change')}
            <td class = "centerAlign">
            	{if $T_CURRENT_USER->user.login == $T_NEWS[news_list].users_LOGIN}
                <a href = "news.php?id={$T_NEWS[news_list].id}&op=change" target = "POPUP_FRAME" onClick = "eF_js_showDivPopup('{$smarty.const._EDITANNOUNCEMENT}', 2);"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" border = "0"/></a>&nbsp;
                <a href = "news.php?id={$T_NEWS[news_list].id}&op=delete" target = "POPUP_FRAME" onClick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"/></a>
                {/if}
            </td>
        {/if}
            </tr>
    {sectionelse}
        <tr class = "defaultRowHeight oddRowColor"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOANNOUNCEMENTSPOSTED}</td></tr>
    {/section}
    </table>