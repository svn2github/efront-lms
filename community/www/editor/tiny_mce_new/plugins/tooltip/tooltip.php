<?php
session_cache_limiter('none');
session_start();
$path = "../../../../../libraries/";

/** The configuration file.*/
include_once $path."configuration.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <title>{#tooltip_dlg.tooltip_title}</title>
 <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
 <script type="text/javascript" src="js/tooltip.js"></script>
 <link href="css/tooltip.css" rel="stylesheet" type="text/css" />
 <base target="_self" />
</head>
<body style="display: none">
<form onsubmit="Tooltip.insert();return false;" action="#">
 <table border="0" cellpadding="4" cellspacing="0">
  <tr>
   <td class="title">{#tooltip_dlg.tooltip_term}:</td>
   <td><input name="term" type="text" class="mceFocus" id="term" value="" style="width: 200px" /></td>
  </tr>
  <tr>
   <td nowrap="nowrap">{#tooltip_dlg.tooltip_explanation}:</td>
  <!-- <td><input name="explanation" type="text" class="mceFocus" id="explanation" value="" style="width: 200px" /> -->
   <td><textarea rows="10" cols="60" class="mceFocus" id="explanation"></textarea>

   </td>
  </tr>
 </table>

 <div class="mceActionPanel">
  <div style="float: left">
   <input type="button" id="insert" name="insert" value="{#insert}" onclick="Tooltip.insert();"/>
  </div>

  <div style="float: right">
   <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
  </div>
 </div>
</form>
</body>
</html>
