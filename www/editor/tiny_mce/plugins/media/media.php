<?php

session_cache_limiter('none');
session_start();

$path = "../../../../../libraries/";

/** The configuration file.*/
include_once $path."configuration.php";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv = "Content-Type"     content = "text/html; charset = utf-8"/>
	<title>{$lang_media_title}</title>
	<script language="javascript" type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="jscripts/media.js"></script>
	<script language="javascript" type="text/javascript" src="../../utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="../../utils/validate.js"></script>
	<script language="javascript" type="text/javascript" src="../../utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="../../utils/editable_selects.js"></script>
	<link href="css/media.css" rel="stylesheet" type="text/css" />
	<base target="_self" />
</head>
<body onload="tinyMCEPopup.executeOnLoad('init();');" style="display: none">

<div class="title"><?php echo _INSERTMEDIA?></div>
<!--- new stuff --->
<?php  if ($_SESSION['s_lessons_ID'] != "") { ?>
<?php echo _SELECTFILE?> :<br>
<iframe name="IMGPICK" src="<?php echo G_SERVERNAME?>/editor/browse.php?lessons_ID=<?php  echo $_SESSION['s_lessons_ID'];?>&for_type=media&dir=<?php echo urlencode($_SESSION['s_lessons_ID']);?>" style="border: solid black 1px;  width: 370px; height:170px; z-index:1"></iframe>
<?php  }elseif($_SESSION['s_type'] == "administrator"){ ?>
<?php echo _SELECTFILE?> :<br>
<iframe name="IMGPICK" src="<?php echo G_SERVERNAME?>/editor/browse.php?for_type=media" style="border: solid black 1px;  width: 370px; height:170px; z-index:1"></iframe>
<?php	}?>
<br/><br/>
    <form onsubmit="insertMedia();return false;" action="#">
		<div class="tabs">
			<ul>
				<li id="general_tab" class="current"><span><a href="javascript:mcTabs.displayTab('general_tab','general_panel');generatePreview();" onmousedown="return false;">{$lang_media_general}</a></span></li>
				<li id="advanced_tab"><span><a href="javascript:mcTabs.displayTab('advanced_tab','advanced_panel');" onmousedown="return false;">{$lang_media_advanced}</a></span></li>
			</ul>
		</div>

		<div class="panel_wrapper">
			<div id="general_panel" class="panel current">
		<!--	<div id="general_panel" class="panel current" onmouseover="generatePreview();"> -->
				<fieldset>
					<legend>{$lang_media_general}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
							<tr>
								<td><label for="media_type">{$lang_media_type}</label></td>
								<td>
									<select id="media_type" name="media_type" onchange="changedType(this.value);generatePreview();">
										<option value="flash">Flash</option>
										<option value="audio">MP3/Audio</option>
										<option value="qt">Quicktime</option>
										<option value="shockwave">Shockware</option>
										<option value="wmp">Windows Media</option>
										<option value="rmp">Real Media</option>
									</select>
								</td>
							</tr>
							<tr>
							<td><label for="src">{$lang_media_file}</label></td>
							  <td>
									<table border="0" cellspacing="0" cellpadding="0">
									  <tr>
										<td><input id="src" name="src" type="text" value="" onchange="switchType(this.value);generatePreview();" /></td>
										<td id="filebrowsercontainer">&nbsp;</td>
									<td>&nbsp;&nbsp;&nbsp;<button type="button" onclick = "generatePreview();">{$lang_advimage_preview}</button></td>
									  </tr>
									</table>
								</td>
							</tr>
							<tr id="linklistrow">
								<td><label for="linklist">{$lang_media_list}</label></td>
								<td id="linklistcontainer">&nbsp;</td>
							</tr>
							<tr>
								<td><label for="width">{$lang_media_size}</label></td>
								<td>
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="text" id="width" name="width" value="" class="size" onchange="generatePreview('width');" /> x <input type="text" id="height" name="height" value="" class="size"  onchange="generatePreview('height');" /></td>
										<td>&nbsp;&nbsp;<input id="constrain" type="checkbox" name="constrain" class="checkbox" /></td>
										<td><label id="constrainlabel" for="constrain">{$lang_media_constrain_proportions}</label></td>
									</tr>
								</table>
							</tr>
					</table>
				</fieldset>

				<fieldset>
					<legend>{$lang_media_preview}</legend>
					<div id="prev" onclick="generatePreview();"></div>
				</fieldset>
			</div>

			<div id="advanced_panel" class="panel">
				<fieldset>
					<legend>{$lang_media_advanced}</legend>

					<table border="0" cellpadding="4" cellspacing="0" width="100%">
						<tr>
							<td><label for="id">{$lang_media_id}</label></td>
							<td><input type="text" id="id" name="id" onchange="generatePreview();" /></td>
							<td><label for="name">{$lang_media_name}</label></td>
							<td><input type="text" id="name" name="name" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="align">{$lang_media_align}</label></td>
							<td>
								<select id="align" name="align" onchange="generatePreview();">
									<option value="">{$lang_not_set}</option> 
									<option value="top">top</option>
									<option value="right">right</option>
									<option value="bottom">bottom</option>
									<option value="left">left</option>
								</select>
							</td>

							<td><label for="bgcolor">{$lang_media_bgcolor}</label></td>
							<td>
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input id="bgcolor" name="bgcolor" type="text" value="" size="9" onchange="updateColor('bgcolor_pick','bgcolor');generatePreview();" /></td>
										<td id="bgcolor_pickcontainer">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td><label for="vspace">{$lang_media_vspace}</label></td>
							<td><input type="text" id="vspace" name="vspace" class="number" onchange="generatePreview();" /></td>
							<td><label for="hspace">{$lang_media_hspace}</label></td>
							<td><input type="text" id="hspace" name="hspace" class="number" onchange="generatePreview();" /></td>
						</tr>
					</table>
				</fieldset>

				<fieldset id="flash_options">
					<legend>{$lang_media_flash_options}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td><label for="flash_quality">{$lang_media_quality}</label></td>
							<td>
								<select id="flash_quality" name="flash_quality" onchange="generatePreview();">
									<option value="">{$lang_not_set}</option> 
									<option value="high">high</option>
									<option value="low">low</option>
									<option value="autolow">autolow</option>
									<option value="autohigh">autohigh</option>
									<option value="best">best</option>
								</select>
							</td>

							<td><label for="flash_scale">{$lang_media_scale}</label></td>
							<td>
								<select id="flash_scale" name="flash_scale" onchange="generatePreview();">
									<option value="">{$lang_not_set}</option> 
									<option value="showall">showall</option>
									<option value="noborder">noborder</option>
									<option value="exactfit">exactfit</option>
								</select>
							</td>
						</tr>

						<tr>
							<td><label for="flash_wmode">{$lang_media_wmode}</label></td>
							<td>
								<select id="flash_wmode" name="flash_wmode" onchange="generatePreview();">
									<option value="">{$lang_not_set}</option> 
									<option value="window">window</option>
									<option value="opaque">opaque</option>
									<option value="transparent">transparent</option>
								</select>
							</td>

							<td><label for="flash_salign">{$lang_media_salign}</label></td>
							<td>
								<select id="flash_salign" name="flash_salign" onchange="generatePreview();">
									<option value="">{$lang_not_set}</option> 
									<option value="l">left</option>
									<option value="t">top</option>
									<option value="r">right</option>
									<option value="b">bottom</option>
									<option value="tl">top-left</option>
									<option value="tr">top_right</option>
									<option value="bl">bottom-left</option>
									<option value="br">bottom-right</option>
								</select>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="flash_play" name="flash_play" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="flash_play">{$lang_media_play}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="flash_loop" name="flash_loop" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="flash_loop">{$lang_media_loop}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="flash_menu" name="flash_menu" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="flash_menu">{$lang_media_menu}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="flash_swliveconnect" name="flash_swliveconnect" onchange="generatePreview();" /></td>
										<td><label for="flash_swliveconnect">{$lang_media_liveconnect}</label></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>

					<table>
						<tr>
							<td><label for="flash_base">{$lang_media_base}</label></td>
							<td><input type="text" id="flash_base" name="flash_base" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="flash_flashvars">{$lang_media_flashvars}</label></td>
							<td><input type="text" id="flash_flashvars" name="flash_flashvars" onchange="generatePreview();" /></td>
						</tr>
					</table>
				</fieldset>

                <fieldset id="audio_options">
					<legend>{$lang_media_audio_options}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td><label for="audio_quality">{$lang_media_quality}</label></td>
							<td>
								<select id="audio_quality" name="audio_quality" onchange="generatePreview();">
									<option value="">{$lang_not_set}</option> 
									<option value="high">high</option>
									<option value="low">low</option>
									<option value="autolow">autolow</option>
									<option value="autohigh">autohigh</option>
									<option value="best">best</option>
								</select>
							</td>

							<td><label for="audio_scale">{$lang_media_scale}</label></td>
							<td>
								<select id="audio_scale" name="audio_scale" onchange="generatePreview();">
									<option value="">{$lang_not_set}</option> 
									<option value="showall">showall</option>
									<option value="noborder">noborder</option>
									<option value="exactfit">exactfit</option>
								</select>
							</td>
						</tr>

						<tr>
							<td><label for="audio_wmode">{$lang_media_wmode}</label></td>
							<td>
								<select id="audio_wmode" name="audio_wmode" onchange="generatePreview();">
									<option value="">{$lang_not_set}</option> 
									<option value="window">window</option>
									<option value="opaque">opaque</option>
									<option value="transparent">transparent</option>
								</select>
							</td>

							<td><label for="audio_salign">{$lang_media_salign}</label></td>
							<td>
								<select id="audio_salign" name="audio_salign" onchange="generatePreview();">
									<option value="">{$lang_not_set}</option> 
									<option value="l">left</option>
									<option value="t">top</option>
									<option value="r">right</option>
									<option value="b">bottom</option>
									<option value="tl">top-left</option>
									<option value="tr">top_right</option>
									<option value="bl">bottom-left</option>
									<option value="br">bottom-right</option>
								</select>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="audio_play" name="audio_play" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="audio_play">{$lang_media_play}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="audio_loop" name="audio_loop" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="audio_loop">{$lang_media_loop}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="audio_menu" name="audio_menu" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="audio_menu">{$lang_media_menu}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="audio_swliveconnect" name="audio_swliveconnect" onchange="generatePreview();" /></td>
										<td><label for="audio_swliveconnect">{$lang_media_liveconnect}</label></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>

					<table>
						<tr>
							<td><label for="audio_base">{$lang_media_base}</label></td>
							<td><input type="text" id="audio_base" name="audio_base" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="audio_flashvars">{$lang_media_flashvars}</label></td>
							<td><input type="text" id="audio_flashvars" name="audio_flashvars" onchange="generatePreview();" /></td>
						</tr>
					</table>
				</fieldset>


				<fieldset id="qt_options">
					<legend>{$lang_media_qt_options}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="qt_loop" name="qt_loop" onchange="generatePreview();" /></td>
										<td><label for="qt_loop">{$lang_media_loop}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="qt_autoplay" name="qt_autoplay" onchange="generatePreview();" /></td>
										<td><label for="qt_autoplay">{$lang_media_play}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="qt_cache" name="qt_cache" onchange="generatePreview();" /></td>
										<td><label for="qt_cache">{$lang_media_cache}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="qt_controller" name="qt_controller" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="qt_controller">{$lang_media_controller}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="qt_correction" name="qt_correction" onchange="generatePreview();" /></td>
										<td><label for="qt_correction">{$lang_media_correction}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="qt_enablejavascript" name="qt_enablejavascript" onchange="generatePreview();" /></td>
										<td><label for="qt_enablejavascript">{$lang_media_enablejavascript}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="qt_kioskmode" name="qt_kioskmode" onchange="generatePreview();" /></td>
										<td><label for="qt_kioskmode">{$lang_media_kioskmode}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="qt_autohref" name="qt_autohref" onchange="generatePreview();" /></td>
										<td><label for="qt_autohref">{$lang_media_autohref}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="qt_playeveryframe" name="qt_playeveryframe" onchange="generatePreview();" /></td>
										<td><label for="qt_playeveryframe">{$lang_media_playeveryframe}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="qt_targetcache" name="qt_targetcache" onchange="generatePreview();" /></td>
										<td><label for="qt_targetcache">{$lang_media_targetcache}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td><label for="qt_scale">{$lang_media_scale}</label></td>
							<td><select id="qt_scale" name="qt_scale" class="mceEditableSelect" onchange="generatePreview();">
									<option value="">{$lang_not_set}</option> 
									<option value="tofit">tofit</option>
									<option value="aspect">aspect</option>
								</select>
							</td>

							<td colspan="2">&nbsp;</td>
						</tr>

						<tr>
							<td><label for="qt_starttime">{$lang_media_starttime}</label></td>
							<td><input type="text" id="qt_starttime" name="qt_starttime" onchange="generatePreview();" /></td>

							<td><label for="qt_endtime">{$lang_media_endtime}</label></td>
							<td><input type="text" id="qt_endtime" name="qt_endtime" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="qt_target">{$lang_media_target}</label></td>
							<td><input type="text" id="qt_target" name="qt_target" onchange="generatePreview();" /></td>

							<td><label for="qt_href">{$lang_media_href}</label></td>
							<td><input type="text" id="qt_href" name="qt_href" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="qt_qtsrcchokespeed">{$lang_media_qtsrcchokespeed}</label></td>
							<td><input type="text" id="qt_qtsrcchokespeed" name="qt_qtsrcchokespeed" onchange="generatePreview();" /></td>

							<td><label for="qt_volume">{$lang_media_volume}</label></td>
							<td><input type="text" id="qt_volume" name="qt_volume" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="qt_qtsrc">{$lang_media_qtsrc}</label></td>
							<td colspan="4">
							<table border="0" cellspacing="0" cellpadding="0">
								  <tr>
									<td><input type="text" id="qt_qtsrc" name="qt_qtsrc" onchange="generatePreview();" /></td>
									<td id="qtsrcfilebrowsercontainer">&nbsp;</td>
								  </tr>
							</table>
							</td>
						</tr>
					</table>
				</fieldset>

				<fieldset id="wmp_options">
					<legend>{$lang_media_wmp_options}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="wmp_autostart" name="wmp_autostart" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="wmp_autostart">{$lang_media_autostart}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="wmp_enabled" name="wmp_enabled" onchange="generatePreview();" /></td>
										<td><label for="wmp_enabled">{$lang_media_enabled}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="wmp_enablecontextmenu" name="wmp_enablecontextmenu" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="wmp_enablecontextmenu">{$lang_media_menu}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="wmp_fullscreen" name="wmp_fullscreen" onchange="generatePreview();" /></td>
										<td><label for="wmp_fullscreen">{$lang_media_fullscreen}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="wmp_invokeurls" name="wmp_invokeurls" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="wmp_invokeurls">{$lang_media_invokeurls}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="wmp_mute" name="wmp_mute" onchange="generatePreview();" /></td>
										<td><label for="wmp_mute">{$lang_media_mute}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="wmp_stretchtofit" name="wmp_stretchtofit" onchange="generatePreview();" /></td>
										<td><label for="wmp_stretchtofit">{$lang_media_stretchtofit}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="wmp_windowlessvideo" name="wmp_windowlessvideo" onchange="generatePreview();" /></td>
										<td><label for="wmp_windowlessvideo">{$lang_media_windowlessvideo}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td><label for="wmp_balance">{$lang_media_balance}</label></td>
							<td><input type="text" id="wmp_balance" name="wmp_balance" onchange="generatePreview();" /></td>

							<td><label for="wmp_baseurl">{$lang_media_baseurl}</label></td>
							<td><input type="text" id="wmp_baseurl" name="wmp_baseurl" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="wmp_captioningid">{$lang_media_captioningid}</label></td>
							<td><input type="text" id="wmp_captioningid" name="wmp_captioningid" onchange="generatePreview();" /></td>

							<td><label for="wmp_currentmarker">{$lang_media_currentmarker}</label></td>
							<td><input type="text" id="wmp_currentmarker" name="wmp_currentmarker" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="wmp_currentposition">{$lang_media_currentposition}</label></td>
							<td><input type="text" id="wmp_currentposition" name="wmp_currentposition" onchange="generatePreview();" /></td>

							<td><label for="wmp_defaultframe">{$lang_media_defaultframe}</label></td>
							<td><input type="text" id="wmp_defaultframe" name="wmp_defaultframe" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="wmp_playcount">{$lang_media_playcount}</label></td>
							<td><input type="text" id="wmp_playcount" name="wmp_playcount" onchange="generatePreview();" /></td>

							<td><label for="wmp_rate">{$lang_media_rate}</label></td>
							<td><input type="text" id="wmp_rate" name="wmp_rate" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="wmp_uimode">{$lang_media_uimode}</label></td>
							<td><input type="text" id="wmp_uimode" name="wmp_uimode" onchange="generatePreview();" /></td>

							<td><label for="wmp_volume">{$lang_media_volume}</label></td>
							<td><input type="text" id="wmp_volume" name="wmp_volume" onchange="generatePreview();" /></td>
						</tr>

					</table>
				</fieldset>

				<fieldset id="rmp_options">
					<legend>{$lang_media_rmp_options}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="rmp_autostart" name="rmp_autostart" onchange="generatePreview();" /></td>
										<td><label for="rmp_autostart">{$lang_media_autostart}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="rmp_loop" name="rmp_loop" onchange="generatePreview();" /></td>
										<td><label for="rmp_loop">{$lang_media_loop}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="rmp_autogotourl" name="rmp_autogotourl" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="rmp_autogotourl">{$lang_media_autogotourl}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="rmp_center" name="rmp_center" onchange="generatePreview();" /></td>
										<td><label for="rmp_center">{$lang_media_center}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="rmp_imagestatus" name="rmp_imagestatus" checked="checked" onchange="generatePreview();" /></td>
										<td><label for="rmp_imagestatus">{$lang_media_imagestatus}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="rmp_maintainaspect" name="rmp_maintainaspect" onchange="generatePreview();" /></td>
										<td><label for="rmp_maintainaspect">{$lang_media_maintainaspect}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="rmp_nojava" name="rmp_nojava" onchange="generatePreview();" /></td>
										<td><label for="rmp_nojava">{$lang_media_nojava}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="rmp_prefetch" name="rmp_prefetch" onchange="generatePreview();" /></td>
										<td><label for="rmp_prefetch">{$lang_media_prefetch}</label></td>
									</tr>
								</table>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="rmp_shuffle" name="rmp_shuffle" onchange="generatePreview();" /></td>
										<td><label for="rmp_shuffle">{$lang_media_shuffle}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								&nbsp;
							</td>
						</tr>

						<tr>
							<td><label for="rmp_console">{$lang_media_console}</label></td>
							<td><input type="text" id="rmp_console" name="rmp_console" onchange="generatePreview();" /></td>

							<td><label for="rmp_controls">{$lang_media_controls}</label></td>
							<td><input type="text" id="rmp_controls" name="rmp_controls" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="rmp_numloop">{$lang_media_numloop}</label></td>
							<td><input type="text" id="rmp_numloop" name="rmp_numloop" onchange="generatePreview();" /></td>

							<td><label for="rmp_scriptcallbacks">{$lang_media_scriptcallbacks}</label></td>
							<td><input type="text" id="rmp_scriptcallbacks" name="rmp_scriptcallbacks" onchange="generatePreview();" /></td>
						</tr>
					</table>
				</fieldset>

				<fieldset id="shockwave_options">
					<legend>{$lang_media_shockwave_options}</legend>

					<table border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td><label for="shockwave_swstretchstyle">{$lang_media_swstretchstyle}</label></td>
							<td>
								<select id="shockwave_swstretchstyle" name="shockwave_swstretchstyle" onchange="generatePreview();">
									<option value="none">None</option>
									<option value="meet">Meet</option>
									<option value="fill">Fill</option>
									<option value="stage">Stage</option>
								</select>
							</td>

							<td><label for="shockwave_swvolume">{$lang_media_volume}</label></td>
							<td><input type="text" id="shockwave_swvolume" name="shockwave_swvolume" onchange="generatePreview();" /></td>
						</tr>

						<tr>
							<td><label for="shockwave_swstretchhalign">{$lang_media_swstretchhalign}</label></td>
							<td>
								<select id="shockwave_swstretchhalign" name="shockwave_swstretchhalign" onchange="generatePreview();">
									<option value="none">None</option>
									<option value="left">left</option>
									<option value="center">center</option>
									<option value="right">right</option>
								</select>
							</td>

							<td><label for="shockwave_swstretchvalign">{$lang_media_swstretchvalign}</label></td>
							<td>
								<select id="shockwave_swstretchvalign" name="shockwave_swstretchvalign" onchange="generatePreview();">
									<option value="none">None</option>
									<option value="meet">Top</option>
									<option value="fill">Center</option>
									<option value="stage">Bottom</option>
								</select>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="shockwave_autostart" name="shockwave_autostart" onchange="generatePreview();" checked="checked" /></td>
										<td><label for="shockwave_autostart">{$lang_media_autostart}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="shockwave_sound" name="shockwave_sound" onchange="generatePreview();" checked="checked" /></td>
										<td><label for="shockwave_sound">{$lang_media_sound}</label></td>
									</tr>
								</table>
							</td>
						</tr>


						<tr>
							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="shockwave_swliveconnect" name="shockwave_swliveconnect" onchange="generatePreview();" /></td>
										<td><label for="shockwave_swliveconnect">{$lang_media_liveconnect}</label></td>
									</tr>
								</table>
							</td>

							<td colspan="2">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><input type="checkbox" class="checkbox" id="shockwave_progress" name="shockwave_progress" onchange="generatePreview();" checked="checked" /></td>
										<td><label for="shockwave_progress">{$lang_media_progress}</label></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
		</div>

		<div class="mceActionPanel">
			<div style="float: left">
				<input type="button" id="insert" name="insert" value="{$lang_insert}" onclick="insertMedia();" />
			</div>

			<div style="float: right">
				<input type="button" id="cancel" name="cancel" value="{$lang_cancel}" onclick="tinyMCEPopup.close();" />
			</div>
		</div>
	</form>
</body>
</html>
