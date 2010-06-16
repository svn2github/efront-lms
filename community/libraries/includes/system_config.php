<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

$loadScripts[] = 'includes/system_config';
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] == 'hidden') {
 eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}

$options = array();
$options[] = array('image' => '16x16/tools.png', 'title' => _GENERALSETTINGS, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=system_config&op=general', 'selected' => isset($_GET['op']) && $_GET['op'] != 'general' ? false : true);
$options[] = array('image' => '16x16/user.png', 'title' => _USERSETTINGS, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=system_config&op=user', 'selected' => $_GET['op'] != 'user' ? false : true);
$options[] = array('image' => '16x16/layout.png', 'title' => _APPEARANCE, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=system_config&op=appearance', 'selected' => $_GET['op'] != 'appearance' ? false : true);
$options[] = array('image' => '16x16/home.png', 'title' => _EXTERNALTOOLS, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=system_config&op=external', 'selected' => $_GET['op'] != 'external' ? false : true);
$options[] = array('image' => '16x16/generic.png', 'title' => _CUSTOMIZATION, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=system_config&op=customization', 'selected' => $_GET['op'] != 'customization' ? false : true);
$smarty -> assign("T_TABLE_OPTIONS", $options);

if (!isset($_GET['op']) || $_GET['op'] == 'general') {
 require_once("system_config/general.php");
} elseif ($_GET['op'] == 'user') {
 require_once("system_config/user.php");
} elseif ($_GET['op'] == 'appearance') {
 require_once("system_config/appearance.php");
} elseif ($_GET['op'] == 'external') {
 require_once("system_config/external.php");
} elseif ($_GET['op'] == 'customization') {
 require_once("system_config/customization.php");
}

?>
