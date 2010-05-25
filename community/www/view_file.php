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
include_once $path."configuration.php";
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        eF_redirect("index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    eF_redirect("index.php?message=".urlencode(_YOUCANNOTACCESSTHISPAGE)."&message_type=failure");
    exit;
}
try {
    $file = new EfrontFile($_GET['file']);
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
