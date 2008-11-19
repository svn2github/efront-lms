<?php
/**
* Replaces occurences of the form #filter:user_login-asdfas# with a personal message link
*/

function smarty_outputfilter_eF_template_formatLogins($compiled, &$smarty) {
    if (!$GLOBALS['currentUser'] -> coreAccess['personal_messages'] || $GLOBALS['currentUser'] -> coreAccess['personal_messages'] != 'hidden') {
        $new = preg_replace("/#filter:user_login-(.*)#/U", "<span style = \"white-space:nowrap;font-weight:bold\"><a href = \"forum/new_message.php?recipient=\$1\" onclick = \"eF_js_showDivPopup('"._NEWMESSAGE."', 2)\" title=\"\\1\" target = \"POPUP_FRAME\">\$1</a></span>", $compiled);
        $new = preg_replace("/#filter:user_loginNoIcon-(.*)#/U", "<a href = \"forum/new_message.php?recipient=\$1\" onclick = \"eF_js_showDivPopup('"._NEWMESSAGE."', 2)\" title=\"\\1\" target = \"POPUP_FRAME\">\\1</a>", $new);
        $compiled = $new;
    } else {
        $new = preg_replace("/#filter:user_login-(.*)#/U", "<span style = \"white-space:nowrap\"><a href = \"javascript:void(0)\"  title=\"\\1\" target = \"POPUP_FRAME\"><img alt=\"\\1\" border = \"0\" src=\"images/16x16/user1.png\" style = \"vertical-align:middle\"/></a></span>", $compiled);
        $new = preg_replace("/#filter:user_loginNoIcon-(.*)#/U", "<a href = \"javascript:void(0)\" title=\"\\1\" target = \"POPUP_FRAME\">\\1</a>", $new);
        $compiled = $new;
    }
    return $compiled;
}

?>