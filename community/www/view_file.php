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

    header("content-type:".$file['mime_type']);
    if (isset($_GET['action']) && $_GET['action'] == 'download') {
    	if (stripos($_SERVER['HTTP_USER_AGENT'], 'firefox') === false) {
    		header('content-disposition: attachment; filename= "'.urlencode($file['name']).'"');
    	} else {
    		header('content-disposition: attachment; filename= "'.($file['name']).'"');
    	}
    	header("Content-Transfer-Encoding: binary");
    	if (!$GLOBALS['configuration']['gz_handler']) {
    		//This does not cooperate well with gzhandler
    		header("Content-Length: ".filesize($file['path']));
    	}
    } else {
    	header('content-disposition: inline; filename= "'.$file['name'].'"');
    }
    readfile($file['path']);

} catch (EfrontFileException $e) {
    header('content-disposition: attachment; filename= "error.txt"');
    pr($e);
}

?>