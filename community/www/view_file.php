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
//pr($_SERVER);
if (strpos($_SERVER['HTTP_REFERER'], "view_resource.php")){
 $bypass_check = true;
}
try {
 if(!$bypass_check) {
  $currentUser = EfrontUser :: checkUserAccess();
 }
} catch (Exception $e) {
 //header("HTTP/1.0 500");
 //echo EfrontSystem :: printErrorMessage(_RESOURCEREQUESTEDREQUIRESLOGIN);
 eF_redirect("index.php?message=".urlencode(_RESOURCEREQUESTEDREQUIRESLOGIN)."&message_type=failure");
 exit;
}
session_write_close();
//pr($_SERVER);pr($_GET);exit;
try {
 if (isset($_GET['server'])) {
  $url = $_SERVER['REQUEST_URI'];
  if (strpos($url, 'http') !== 0) { //Otherwise, depending on the QUERY_STRING, parse_url() may not work
   $url = G_PROTOCOL.'://'.$_SERVER["HTTP_HOST"].$url;
  }

  $urlParts = parse_url($url);

  $filePath = G_ROOTPATH.'www/'.str_replace(G_SERVERNAME, '', G_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].$urlParts['path']);

  try {
   $file = new EfrontFile(urldecode($filePath));
  } catch (Exception $e) {
   $file = new EfrontFile($filePath);
  }
 } else {
     $file = new EfrontFile($_GET['file']);
 }
 if(!$bypass_check) {
  if (preg_match("#content/lessons/(\d+)/#", $file['path'], $matches)) { //the file is a content file. Available to any user enrolled to this lesson.
   $result = eF_getTableDataFlat("lessons l, users_to_lessons ul", "id, share_folder", "l.archive=0 and l.id=ul.lessons_ID and ul.archive=0 and ul.users_LOGIN='".$currentUser->user['login']."'");
   $legalFolders = array_unique(array_merge($result['id'], $result['share_folder']));
   if ($currentUser->user['user_type'] != 'administrator' && $matches[1] && !in_array($matches[1], $legalFolders)) {
    throw new EfrontFileException(_YOUCANNOTACCESSTHEREQUESTEDRESOURCE, EfrontFileException::UNAUTHORIZED_ACTION);
   }
  } else if (preg_match("#content/lessons/scorm_uploaded_files/#", $file['path'], $matches)) { //the file is a temporary scorm exported file
   //proceed
  } else if (preg_match("#".G_UPLOADPATH."(.*)/projects/#", $file['path'], $matches) || preg_match("#".G_UPLOADPATH."(.*)/tests/#", $file['path'], $matches)) { //this is a project or test file. Check whether the current user has access to it
   if ($matches[1] == $_SESSION['s_login']) {
    //continue if a user is trying to view his/her own file
   } else if ($_SESSION['s_lesson_user_type'] != 'professor' || !$_SESSION['s_lessons_ID']) {
    throw new EfrontFileException(_YOUCANNOTACCESSTHEREQUESTEDRESOURCE, EfrontFileException::UNAUTHORIZED_ACTION);
   } else if (!eF_checkParameter($matches[1], 'login')) {
    throw new EfrontFileException(_YOUCANNOTACCESSTHEREQUESTEDRESOURCE, EfrontFileException::UNAUTHORIZED_ACTION);
   } else {
    $professorLessons = eF_getTableDataFlat("lessons l, users_to_lessons ul", "id", "l.archive=0 and l.id=ul.lessons_ID and ul.archive=0 and ul.users_LOGIN='".$currentUser->user['login']."'");
    $userLessons = eF_getTableDataFlat("lessons l, users_to_lessons ul", "id", "l.archive=0 and l.id=ul.lessons_ID and ul.archive=0 and ul.users_LOGIN='".$matches[1]."'");
    if (!in_array($_SESSION['s_lessons_ID'], array_intersect($professorLessons['id'], $userLessons['id']))) {
     throw new EfrontFileException(_YOUCANNOTACCESSTHEREQUESTEDRESOURCE, EfrontFileException::UNAUTHORIZED_ACTION);
    }
   }
  } else if (preg_match("#".G_UPLOADPATH."(.*)/avatars/#", $file['path'], $matches) || mb_strpos($file['path'], G_SYSTEMAVATARSPATH) !== false ) {
     //proceed
  } else if ( mb_strpos($file['path'], G_UPLOADPATH) !== false && mb_strpos($file['path'], G_UPLOADPATH.$currentUser->user['login']) === false) {
   throw new EfrontFileException(_YOUCANNOTACCESSTHEREQUESTEDRESOURCE, EfrontFileException::UNAUTHORIZED_ACTION);
  }
 }
  if (strpos($file['path'], G_ROOTPATH.'libraries') !== false && strpos($file['path'], G_ROOTPATH.'libraries/language') === false && $file['mime_type'] != "application/inc") {
  throw new EfrontFileException(_ILLEGALPATH.': '.$file['path'], EfrontFileException :: ILLEGAL_PATH);
 }

    if (isset($_GET['action']) && $_GET['action'] == 'download') {
     $file -> sendFile(true);
    } else {
  cacheHeaders(lastModificationTime(filemtime($file['path'])));

     $file -> sendFile(false);
    }
} catch (EfrontFileException $e) {
 if ($e->getCode() == EfrontFileException::FILE_NOT_EXIST) {
  header("HTTP/1.0 404");
 }
    echo EfrontSystem :: printErrorMessage($e -> getMessage());
}


function cacheHeaders($lastModifiedDate) {
 if ($lastModifiedDate) {
  if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModifiedDate) {
   if (php_sapi_name()=='CGI') {
    Header("Status: 304 Not Modified");
   } else {
    Header("HTTP/1.0 304 Not Modified");
   }
   exit;
  } else {
   $gmtDate = gmdate("D, d M Y H:i:s \G\M\T",$lastModifiedDate);
   header('Last-Modified: '.$gmtDate);
  }
 }
}

// This function uses a static variable to track the most recent
// last modification time
function lastModificationTime($time=0) {
 static $last_mod ;
 if (!isset($last_mod) || $time > $last_mod) {
  $last_mod = $time ;
 }
 return $last_mod ;
}


?>
