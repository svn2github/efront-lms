{* Outer template - header *}

<table border = "0" cellspacing = "0" cellpadding = "0" valign = "top">
    <tr height = "0">
        <td></td>
        <td></td>
        <td width = "80%"></td>
        <td width = "230" nowrap></td></tr>
    <tr><td>
            <table align = "right" border = "0" cellspacing = "0" cellpadding = "0">
                <tr><td><a href = "{$smarty.session.s_type}.php?lessons_ID={$smarty.session.s_lessons_ID}" target = "_parent">
                        <img width = "{$smarty.const.G_LEFT_WIDTH}" src = "images/logo.jpg" border = "0" height = "80"></a>
                    </td></tr>
                <tr height = "16">
                    <td></td></tr>
            </table>
        </td>
        <td rowspan = "3" background = "images/new_v_line.jpg">
            <img src = "images/spacer.gif" width = "2"></td>
        <td width = "100%">
            <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
                <tr height = "80" valign = "top">
                    <td width = "100%" style = "background-color:#93b4d7">
                    {$T_CTGINFO}
                    </td>
                </tr>
                <tr height = "16">
                    <td></td></tr>
            </table>
        </td>
        <td valign = "top" background = "images/new_side.jpg">
            <table align = "center">
                <tr height = "60">
                    <td></td></tr>
                <tr><td class = "ctg_title">{$smarty.const._ONLINEUSERS} | {$T_ONLINENUMBER}</td></tr>
            </table>
        </td>
    </tr>
    <!-- Main Row -->
    <tr height = "100%" valign = "top">
        <td width = "{$smarty.const.G_LEFT_WIDTH}" align = "right">
            {$T_MENU}<br />{$T_SEARCH}</td>
        <td colspan = "2">
            <table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">                
                <tr><td colspan = "3" valign = "top">
                        <table width = "100%" border = "0" valign = "top">
                            <tr height = "22">
                                <td nowrap class = "top_title" colspan = "2">{$T_TITLE}&nbsp;</td></tr>
                        </table>
                    </td></tr>
                <tr><td>
                        <img src = "images/spacer.gif" height = "5"></td>
                    <td align = "left" width = "50%"></td>
                    <td width = "50%"></td></tr>
                <tr><td><img src = "images/spacer.gif" width = "5"></td>
                    <td colspan = "2">
                        <table border = "0" width = "100%" cellspacing = "0" height = "100%">
                            <tr><td valign = "top">
                                    {$T_MESSAGE}