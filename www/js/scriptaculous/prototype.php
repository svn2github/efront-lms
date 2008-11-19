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
    
    include ("prototype.js");
?>
