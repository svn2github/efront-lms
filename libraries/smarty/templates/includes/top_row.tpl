{* Smarty template for top row *}

<table width = "100%" border = "0" style = "background-color:#93b4d7;border:1px solid black;">
    <tr><td width = "5%" ><a href = "{$smarty.server.PHP_SELF}"><img src = "images/logo.jpg" height = "60" border = "0"></a></td>
        <td valign = "middle">
            <table width = "100%">
                <tr>
                    <td align = "right" nowrap>
                        {$smarty.session.s_login} [{$smarty.session.s_type}]: <a href = "index.php?logout=true">{$smarty.const._EXIT}</a> <br/>
                        {$T_NUMOF_ONLINE_USERS} {$smarty.const._ONLINEUSERS}
                </td></tr>
            </table>
    </td></tr>
</table><br/>
