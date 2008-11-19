<?php
error_reporting(E_ERROR);
session_start();
?>
<html>
<head>
 <meta http-equiv = "Content-Type" content = "text/html; charset = utf-8"/>

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
include_once "../../libraries/language/lang-".$_SESSION['s_language'].".php.inc";
?>
<table width = "100%" height = "100%">
    <tr><td><image src = "../images/smilies/icon_smile.gif"     onclick = "putSmiley(':)')" /></td>
        <td><image src = "../images/smilies/icon_sad.gif"       onclick = "putSmiley(':(')" /></td>
        <td><image src = "../images/smilies/icon_cry.gif"       onclick = "putSmiley(':cry:')" /></td>
        <td><image src = "../images/smilies/icon_cool.gif"      onclick = "putSmiley('8)')" /></td>
    </tr><tr>
        <td><image src = "../images/smilies/icon_biggrin.gif"   onclick = "putSmiley(':D')" /></td>
        <td><image src = "../images/smilies/icon_confused.gif"  onclick = "putSmiley(':?')" /></td>
        <td><image src = "../images/smilies/icon_eek.gif"       onclick = "putSmiley('8O')" /></td>
        <td><image src = "../images/smilies/icon_evil.gif"      onclick = "putSmiley(':evil:')" /></td>
    </tr><tr>
        <td><image src = "../images/smilies/icon_wink.gif"      onclick = "putSmiley(';)')" /></td>
        <td><image src = "../images/smilies/icon_lol.gif"       onclick = "putSmiley(':lol:')" /></td>
        <td><image src = "../images/smilies/icon_mad.gif"       onclick = "putSmiley(':x')" /></td>
        <td><image src = "../images/smilies/icon_surprised.gif" onclick = "putSmiley(':o')" /></td>
    </tr><tr>
        <td><image src = "../images/smilies/icon_razz.gif"      onclick = "putSmiley(':P')" /></td>
        <td><image src = "../images/smilies/icon_redface.gif"   onclick = "putSmiley(':oops:')" /></td>
        <td><image src = "../images/smilies/icon_rolleyes.gif"  onclick = "putSmiley(':roll:')" /></td>
        <td><image src = "../images/smilies/icon_twisted.gif"   onclick = "putSmiley(':twisted:')" /></td>
    </tr><tr>
        <td></td>
        <td></td>
    </tr>
</table>
</body>
</html>