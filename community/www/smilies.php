<?php
session_start();

$path = '../libraries/';
require_once($path."configuration.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
<script>
function putSmiley(str) {
    var obj = top.sideframe.document.chat_form.chat_message;
    obj.value = obj.value + str;
    top.mainframe.document.getElementById('popup_close').onclick();
}
</script>
</head>
<body>
<?php
$tableString = '<table width = "100%" height = "100%">
    <tr><td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_smile.gif" onclick = "putSmiley(\':)\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_sad.gif" onclick = "putSmiley(\':(\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_cry.gif" onclick = "putSmiley(\':cry:\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_cool.gif" onclick = "putSmiley(\'8)\')" /></td>
    </tr><tr>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_biggrin.gif" onclick = "putSmiley(\':D\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_confused.gif" onclick = "putSmiley(\':?\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_eek.gif" onclick = "putSmiley(\'8O\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_evil.gif" onclick = "putSmiley(\':evil:\')" /></td>
    </tr><tr>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_wink.gif" onclick = "putSmiley(\';)\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_lol.gif" onclick = "putSmiley(\':lol:\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_mad.gif" onclick = "putSmiley(\':x\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'images/smilies/icon_surprised.gif" onclick = "putSmiley(\':o\')" /></td>
    </tr><tr>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'/images/smilies/icon_razz.gif" onclick = "putSmiley(\':P\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'/images/smilies/icon_redface.gif" onclick = "putSmiley(\':oops:\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'/images/smilies/icon_rolleyes.gif" onclick = "putSmiley(\':roll:\')" /></td>
        <td><img src = "themes/'.$GLOBALS['currentTheme']->themes['path'].'/images/smilies/icon_twisted.gif" onclick = "putSmiley(\':twisted:\')" /></td>
    </tr><tr>
        <td></td>
        <td></td>
    </tr>
</table>';
echo $tableString;
?>
</body>
</html>
