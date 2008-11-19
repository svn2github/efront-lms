<?php
    error_reporting(E_ERROR);
    ob_start ("ob_gzhandler");
    header("Content-type: text/javascript; charset: UTF-8");
    header("Cache-Control: must-revalidate");
    $offset = 60 * 60 ;
    $ExpStr = "Expires: " .
    gmdate("D, d M Y H:i:s",
    time() + $offset) . " GMT";
    header($ExpStr);
	define("_CHATROOMDOESNOTEXIST_ERROR", "-2");
	define("_CHATROOMISNOTENABLED_ERROR", "-3");
	session_start();
    include_once "../../libraries/language/lang-".$_SESSION['s_language'].".php.inc";
	
    include("print-script.js");
?>
