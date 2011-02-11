<?php

session_cache_limiter('none');
session_start();

$path = "../../../../../libraries/";

/** The configuration file.*/
include_once $path."configuration.php";

if ($_SESSION['s_lessons_ID']) {
    $iframeUrl = G_SERVERNAME.'editor/browse.php?for_type=java&mode=lesson';
} elseif (strpos($_SERVER['HTTP_REFERER'], "themes") !== false) {
    $iframeUrl = G_SERVERNAME.'editor/browse.php?for_type=java&mode=external';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="whitebg">
<head>
    <title>{#java_dlg.title}</title>
    <meta http-equiv = "Content-Type" content = "text/html; charset = utf-8">
 <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
 <script type="text/javascript" src="../../utils/mctabs.js"></script>
 <script type="text/javascript" src="../../utils/form_utils.js"></script>
 <script type="text/javascript" src="../../utils/validate.js"></script>
 <script type="text/javascript" src="../../utils/editable_selects.js"></script>
 <script type="text/javascript" src="js/java.js"></script>
 <link href="css/java.css" rel="stylesheet" type="text/css" />
    <base target="_self" />
</head>
<body style="display: none">
<table><tr><td valign="top">
<?php echo _SELECT ?> :<br>
<iframe name="IMGPICK" src="<?php echo $iframeUrl?>" style="border: solid black 1px;  width: 450px; height:235px; z-index:1"></iframe>

</td><td valign="top">
    <form onsubmit="JavaDialog.insert();return false;" action="#">
        <div class="tabs">
            <ul>
                <li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onmousedown="return false;">{#java_dlg.general}</a></span></li>
            </ul>
        </div>

        <div class="panel_wrapper">
            <div id="general_panel" class="panel current">
                <fieldset>
                    <legend>{#java_dlg.general}</legend>

                    <table border="0" cellpadding="4" cellspacing="0">
                            <tr>
                            <td nowrap="nowrap"><label for="file">{#java_dlg.file}</label></td>
                              <td nowrap="nowrap">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td><input id="file" size="50px" name="file" type="text" value="" onfocus="this.select();" /></td>
                                        <td id="filebrowsercontainer">&nbsp;</td>
                                      </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                            <td nowrap="nowrap"><label for="code">{#java_dlg.codebase}</label></td>
                              <td nowrap="nowrap">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td><input size="50px" id="codebase" name="codebase" type="text" value="" onfocus="this.select();" /></td>
                                        <td id="codebasebrowsercontainer">&nbsp;</td>
                                      </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td nowrap="nowrap"><label>{#java_dlg.size}</label></td>
                                <td nowrap="nowrap">
                                    <input size="10px" type="text" id="width" name="width" value="" onfocus="this.select();" />
                                    <select name="width2" id="width2" style="width: 50px">
                                        <option value="">px</option>
                                        <option value="%">%</option>
                                    </select>&nbsp;x&nbsp;<input size="10px" id="height" name="height" type="text" value="" onfocus="this.select();" />
                                    <select name="height2" id="height2" style="width: 50px">
                                        <option value="">px</option>
                                        <option value="%">%</option>
                                    </select>
                                </td>
                            </tr>
                    </table>
                </fieldset>
            </div>
        </div>

        <div class="mceActionPanel">
            <div style="float: left">
                <input type="button" id="insert" name="insert" value="{#insert}" onclick="JavaDialog.insert();"/>
            </div>

            <div style="float: right">
                <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
            </div>
        </div>
    </form>
</td></tr></table>
</body>
</html>
