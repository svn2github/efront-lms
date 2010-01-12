<?php

session_cache_limiter('none');
session_start();
//print_r($_SESSION);
$path = "../libraries/";

/** The configuration file.*/
require_once $path."configuration.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
if ($GLOBALS['currentTheme'] -> options['sidebar_interface']) {
    header("location:professor.php".($_SERVER['QUERY_STRING'] ? "?".$_SERVER['QUERY_STRING'] : ''));
    //$smarty -> assign("T_SIDEBAR_URL", "");		// set an empty source for horizontal sidebars
    //$smarty -> assign("T_SIDEFRAME_WIDTH", 0);
}
$smarty -> assign("T_SIDEBAR_MODE", $GLOBALS['currentTheme'] -> options['sidebar_interface']);
if ($GLOBALS['currentTheme'] -> options['sidebar_width']) {
    $smarty -> assign("T_SIDEFRAME_WIDTH", $GLOBALS['currentTheme'] -> options['sidebar_width']);
} else {
    $smarty -> assign("T_SIDEFRAME_WIDTH", 175);
}
if (isset($_SESSION['previousSideUrl'])) {
  $smarty -> assign("T_SIDEBAR_URL", $_SESSION['previousSideUrl']);
}
if (isset($_GET['dashboard'])) {
 $smarty -> assign("T_MAIN_URL", "professor.php?ctg=personal");
} else {
 if (isset($_SESSION['previousMainUrl'])) {
  $smarty -> assign("T_MAIN_URL", $_SESSION['previousMainUrl']);
 }
}
$smarty -> display("professorpage.tpl");
?>
