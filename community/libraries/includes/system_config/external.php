<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

$externalMainForm = new Html_QuickForm("external_main_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=external&tab=main", "", null, true);
$externalMainForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$externalMainForm -> addElement("advcheckbox", "api", _ENABLEDAPI, null, 'class = "inputCheckBox"', array(0, 1));
$externalMainForm -> addElement("text", "api_ip", _CONSTRAINAPIIP, 'class = "inputText"');
$externalMainForm -> addElement("select", "editor_type", _EDITORTYPE, array('tinymce' => G_TINYMCE, 'tinymce_new' => G_NEWTINYMCE), 'class = "inputCheckBox"');
$externalMainForm -> addElement("advcheckbox", "virtual_keyboard", _ENABLEVIRTUALKEYBOARD, null, 'class = "inputCheckBox"', array(0, 1));
//If we are on a windows system, and the zip_method is already PHP, then don't display option to change it
if (stripos(php_uname(), 'windows') === false || $GLOBALS['configuration']['zip_method'] != "php") {
 $externalMainForm -> addElement("select", "zip_method", _ZIPHANDLING, array('php' => "PHP", 'system' => _SYSTEM), 'class = "inputSelect"');
} else {
 $externalMainForm -> addElement("select", "zip_method", _ZIPHANDLING, array('php' => "PHP"), 'class = "inputSelect"');
}
$externalMainForm -> setDefaults($GLOBALS['configuration']);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $externalMainForm -> freeze();
} else {
 $externalMainForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($externalMainForm -> isSubmitted() && $externalMainForm -> validate()) { //If the form is submitted and validated
  $values = $externalMainForm -> exportValues();
  unset($values['submit']);
  foreach ($values as $key => $value) {
   EfrontConfiguration :: setValue($key, $value);
  }
  //delete cache when changing editor type
  $cacheTree = new FileSystemTree(G_THEMECACHE, true);
  foreach (new EfrontDirectoryOnlyFilterIterator($cacheTree -> tree) as $value) {
   $value -> delete();
  }
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=external&tab=main&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
}
$smarty -> assign("T_EXTERNAL_MAIN_FORM", $externalMainForm -> toArray());

$externalMathForm = new Html_QuickForm("external_math_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=external&tab=math", "", null, true);
$externalMathForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$externalMathForm -> addElement("advcheckbox", "math_content", _ENABLEMATHCONTENT, null, 'class = "inputCheckBox"', array(0, 1));
$externalMathForm -> addElement("advcheckbox", "math_images", _LOADMATHTYPESASIMAGES, null, 'class = "inputCheckBox"', array(0, 1));
$externalMathForm -> addElement("static", "", _MATHIMAGESINFO);
$externalMathForm -> addElement("text", "math_server", _MATHSERVER, 'class = "inputText"');
$externalMathForm -> addElement("static", "", _MATHSERVERINFO);
$externalMathForm -> setDefaults($GLOBALS['configuration']);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $externalMathForm -> freeze();
} else {
 $externalMathForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($externalMathForm -> isSubmitted() && $externalMathForm -> validate()) { //If the form is submitted and validated
  $values = $externalMathForm -> exportValues();
  unset($values['submit']);
  foreach ($values as $key => $value) {
   EfrontConfiguration :: setValue($key, $value);
  }
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=external&tab=math&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
}
$smarty -> assign("T_EXTERNAL_MATH_FORM", $externalMathForm -> toArray());

$externalLiveDocxForm = new Html_QuickForm("external_livedocx_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=external&tab=livedocx", "", null, true);
$externalLiveDocxForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$externalLiveDocxForm -> addElement("text", "phplivedocx_server", _PHPLIVEDOCXSERVER, 'class = "inputText"');
$externalLiveDocxForm -> addElement("text", "phplivedocx_username",_USERNAME);
$externalLiveDocxForm -> addElement("password", "phplivedocx_password",_PASSWORD);
$externalLiveDocxForm -> addElement("static", "", _PHPLIVEDOCXINFO);
$externalLiveDocxForm -> setDefaults($GLOBALS['configuration']);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $externalLiveDocxForm -> freeze();
} else {
 $externalLiveDocxForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($externalLiveDocxForm -> isSubmitted() && $externalLiveDocxForm -> validate()) { //If the form is submitted and validated
  $values = $externalLiveDocxForm -> exportValues();
  unset($values['submit']);
  foreach ($values as $key => $value) {
   EfrontConfiguration :: setValue($key, $value);
  }
  $phplivedocxConfig = '<?php
define("PATH_ZF","'.G_ROOTPATH.'Zend/library/'.'");
define("USERNAME","'.$values['phplivedocx_username'].'");
define("PASSWORD","'.$values['phplivedocx_password'].'");
define("PHPLIVEDOCXAPI","'.$values['phplivedocx_server'].'");
?>';
  if (!file_exists($path."phplivedocx_config.php") || is_writable($path."phplivedocx_config.php")) {
   file_put_contents($path."phplivedocx_config.php", $phplivedocxConfig);
  } else {
   $message = _PHPLIVEDOCXCONFIGURATIONFILEISNOTWRITABLE;
  }
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=external&tab=livedocx&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
}
$smarty -> assign("T_EXTERNAL_LIVEDOCX_FORM", $externalLiveDocxForm -> toArray());
