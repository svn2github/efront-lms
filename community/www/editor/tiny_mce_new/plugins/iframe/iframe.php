<?php
session_cache_limiter('none');
session_start();

$path = "../../../../../libraries/";

/** The configuration file.*/
include_once $path."configuration.php";

if ($_SESSION['s_lessons_ID']) {
    $iframeUrl = G_SERVERNAME.'editor/browse.php?for_type=document&mode=lesson';
} elseif (strpos($_SERVER['HTTP_REFERER'], "themes") !== false) {
    $iframeUrl = G_SERVERNAME.'editor/browse.php?for_type=document&mode=external';
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="whitebg">
<head>
 <title>{#iframe.iframe_desc}</title>
 <meta http-equiv = "Content-Type" content = "text/html; charset = utf-8">
 <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
 <script type="text/javascript" src="js/iframe.js"></script>
 <script type="text/javascript" src="../../utils/mctabs.js"></script>
 <script type="text/javascript" src="../../utils/form_utils.js"></script>
 <link href="css/iframe.css" rel="stylesheet" type="text/css" />
 <base target="_self" />
</head>
<body>
<table><tr><td valign="top">
<?php echo _SELECT ?>:<br>
<iframe name="IMGPICK" src="<?php echo $iframeUrl;?>" style="border: solid black 1px;  width: 400px; height:365px; z-index:1"></iframe>
</td><td valign="top">
<form onsubmit="IframeDialog.update();return false;" action="#">
 <div class="tabs">
  <ul>
   <li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onmousedown="return false;">{#iframe.iframe_desc}</a></span></li>
  </ul>
 </div>

 <div class="panel_wrapper">
  <div id="general_panel" class="panel current">
   <table border="0" cellpadding="4" cellspacing="0">

            <tr>
                        <td><label for="file">{#iframe_dlg.file}</label></td>
                        <td nowrap="nowrap">
                           <input id="document" size="50px" name="document" type="text" value="" onfocus="this.select();" class="mceFocus" />
                        </td>
                    </tr>
                    <tr>
                        <td><label for="name">{#iframe_dlg.name}</label></td>
                        <td nowrap="nowrap">
                            <input id="name" name="name" type="text" value="" class="mceFocus" />
                        </td>
                    </tr>
                    <tr>
                        <td><label for="width">{#iframe_dlg.width}</label></td>
                        <td nowrap="nowrap">
                            <input id="width" name="width" type="text" value="" class="mceFocus" />
                            <select name="width2" id="width2">
                                <option value="">px</option>
                                <option value="%">%</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="height">{#iframe_dlg.height}</label></td>
                        <td><input id="height" name="height" type="text" value="" class="mceFocus" />
                            <select name="height2" id="height2">
                                <option value="">px</option>
                                <option value="%">%</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="scroll">{#iframe_dlg.scroll}</label></td>
                        <td><select id="scroll" name="scroll">
                            <option value="auto">{#iframe_dlg.auto}</option>
                            <option value="yes">{#iframe_dlg.yes}</option>
                            <option value="no">{#iframe_dlg.no}</option>
                        </select></td>
                    </tr>
                    <tr>
                        <td><label for="border">{#iframe_dlg.border}</label></td>
                        <td><select id="border" name="border">
                            <option value="1">{#iframe_dlg.yes}</option>
                            <option value="0">{#iframe_dlg.no}</option>
                        </select></td>
                    </tr>
            </table>
  </div>
 </div>

 <div class="mceActionPanel">
  <div style="float: left">
   <input type="submit" id="insert" name="insert" value="{#insert}" />
  </div>

  <div style="float: right">
   <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
  </div>
 </div>
</form>
</td></tr></table>
</body>
</html>
