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

/** Configuration file.*/
include_once $path."configuration.php";

if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        header("location:index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    header("location:index.php?message=".urlencode(_YOUCANNOTACCESSTHISPAGE)."&message_type=failure");
    exit;
}

try {
    $file = new EfrontFile($_GET['file']);
    //if ($file['id'] != -1) {
        header("content-type:".$file['mime_type']);
        if (isset($_GET['action']) && $_GET['action'] == 'download') {
            if (stripos($_SERVER['HTTP_USER_AGENT'], 'firefox') === false) {
                header('content-disposition: attachment; filename= "'.urlencode($file['name']).'"');
            } else {
                header('content-disposition: attachment; filename= "'.($file['name']).'"');
            }
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".filesize($file['path']));
        } else {
            header('content-disposition: inline; filename= "'.$file['name'].'"');
        }
        readfile($file['path']);
        exit;
    //}

} catch (EfrontFileException $e) {
    header('content-disposition: attachment; filename= "error.txt"');
    pr($e);
    //echo $e -> getMessage();
}

?>