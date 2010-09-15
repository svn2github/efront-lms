<?php
/**

* View file

*

* This file offers the user the ability to view and/or download a file.

*

* @package eFront

* @version 1.0

*/
//General initialization and parameters
session_cache_limiter('none');
session_start();
$path = "../libraries/";
//Turn output buffering off, since it messes up files
define("NO_OUTPUT_BUFFERING", true);
/** Configuration file.*/
require_once $path."configuration.php";
try {
 $currentUser = EfrontUser :: checkUserAccess();
} catch (Exception $e) {
 eF_redirect("index.php?message=".urlencode($message = $e -> getMessage().' ('.$e -> getCode().')')."&message_type=failure", true);
 exit;
}
//pr($_SERVER);pr($_GET);exit;
try {
 if (isset($_GET['server'])) {
  $urlParts = parse_url($_SERVER['REQUEST_URI']);
  $filePath = G_ROOTPATH.'www/'.str_replace(G_SERVERNAME, '', G_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].$urlParts['path']);
  $file = new EfrontFile($filePath);
 } else {
     $file = new EfrontFile($_GET['file']);
 }
 if (strpos($file['path'], G_ROOTPATH.'libraries') !== false && strpos($file['path'], G_ROOTPATH.'libraries/language') === false && $file['mime_type'] != "application/inc") {
  throw new EfrontFileException(_ILLEGALPATH.': '.$file['path'], EfrontFileException :: ILLEGAL_PATH);
 }
    if (isset($_GET['action']) && $_GET['action'] == 'download') {
     $file -> sendFile(true);
    } else {
     $file -> sendFile(false);
    }
} catch (EfrontFileException $e) {
    header('content-disposition: attachment; filename= "error.txt"');
    pr($e);
}
?>
