<?php

session_cache_limiter('none');
session_start();

$path = "../../../../../libraries/";

/** The configuration file.*/
include_once $path."configuration.php";
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{$lang_java_title}</title>
    <script language="javascript" type="text/javascript" src="../../tiny_mce_popup.js"></script>
    <script language="javascript" type="text/javascript" src="jscripts/java.js"></script>
    <script language="javascript" type="text/javascript" src="../../utils/mctabs.js"></script>
    <script language="javascript" type="text/javascript" src="../../utils/form_utils.js"></script>
    <link href="css/java.css" rel="stylesheet" type="text/css" />
    <base target="_self" />
</head>
<body onload="tinyMCEPopup.executeOnLoad('init();');" style="display: none">

<div class="title"><?php echo _INSERTJAVA?></div>
<!--- new stuff --->
<?php  if ($_SESSION['s_lessons_ID'] != "") { ?>
<?php echo _SELECTFILE?> :<br>
<iframe name="IMGPICK" src="<?php echo G_SERVERNAME?>/editor/browse.php?lessons_ID=<?php  echo $_SESSION['s_lessons_ID'];?>&for_type=java&dir=<?php echo urlencode($_SESSION['s_lessons_ID']);?>" style="border: solid black 1px;  width: 370px; height:240px; z-index:1"></iframe>
<?php } elseif($_SESSION['s_type'] == "administrator"){ ?>
<?php echo _SELECTFILE?> :<br>
<iframe name="IMGPICK" src="<?php echo G_SERVERNAME?>/editor/browse.php?for_type=java" style="border: solid black 1px;  width: 370px; height:240px; z-index:1"></iframe>
<?php  } ?>
<br/><br/>
    <form onsubmit="insertJava();return false;" action="#">
        <div class="tabs">
            <ul>
                <li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');" onmousedown="return false;">{$lang_java_general}</a></span></li>
            </ul>
        </div>

        <div class="panel_wrapper">
            <div id="general_panel" class="panel current">
                <fieldset>
                    <legend>{$lang_java_general}</legend>

                    <table border="0" cellpadding="4" cellspacing="0">
                            <tr>
                            <td nowrap="nowrap"><label for="file">{$lang_java_file}</label></td>
                              <td nowrap="nowrap">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td><input id="file" name="file" type="text" value="" onfocus="this.select();" /></td>
                                        <td id="filebrowsercontainer">&nbsp;</td>
                                      </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                            <td nowrap="nowrap"><label for="code">{$lang_java_codebase}</label></td>
                              <td nowrap="nowrap">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td><input id="codebase" name="codebase" type="text" value="" onfocus="this.select();" /></td>
                                        <td id="codebasebrowsercontainer">&nbsp;</td>
                                      </tr>
                                    </table>
                                </td>
                            </tr>
                            
                            <tr id="linklistrow">
                                <td><label for="linklist">{$lang_java_list}</label></td>
                                <td id="linklistcontainer">&nbsp;</td>
                            </tr>
                            <tr>
                                <td nowrap="nowrap"><label>{$lang_java_size}</label></td>
                                <td nowrap="nowrap">
                                    <input type="text" id="width" name="width" value="" onfocus="this.select();" />
                                    <select name="width2" id="width2" style="width: 50px">
                                        <option value="">px</option>
                                        <option value="%">%</option>
                                    </select>&nbsp;x&nbsp;<input id="height" name="height" type="text"  value="" onfocus="this.select();" />
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
                <input type="button" id="insert" name="insert" value="{$lang_insert}" onclick="insertJava();" />
            </div>

            <div style="float: right">
                <input type="button" id="cancel" name="cancel" value="{$lang_cancel}" onclick="tinyMCEPopup.close();" />
            </div>
        </div>
    </form>
</body>
</html>
