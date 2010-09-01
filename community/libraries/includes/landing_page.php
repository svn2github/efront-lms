<?php
/**

* eFront social

*

* This page is used for the functionalities of the eFront social infrastructure

* @package eFront

* @version 3.6.0

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}
$redirectPage = $GLOBALS['configuration']['login_redirect_page'];
//$centerLinkInfo = $module -> getCenterLinkInfo();
$InnertableHTML = $loadedModules[$redirectPage] -> getLandingPageModule();
($InnertableHTML === true) ? $module_smarty_file = $loadedModules[$redirectPage] -> getLandingPageSmartyTpl() : $module_smarty_file = false;

// If the module has a lesson innertable
if ($InnertableHTML) {
 // Get module html - two ways: pure HTML or PHP+smarty
 // If no smarty file is defined then false will be returned
 if ($module_smarty_file) {
  // Execute the php code -> The code has already been executed by above (**HERE**)
  // Let smarty know to include the module smarty file
  $innertable_module[$redirectPage] = array('smarty_file' => $module_smarty_file);
 } else {
  // Present the pure HTML cod
  $innertable_module[$redirectPage] = array('html_code' => $InnertableHTML);
 }
}

//pr($innertable_module);

$smarty -> assign("T_INNERTABLE_MODULE", $innertable_module);




?>
