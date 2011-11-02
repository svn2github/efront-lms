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
 header("HTTP/1.0 500");
 echo EfrontSystem :: printErrorMessage("Please login to access this resource");
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

 if (preg_match("#content/lessons/(\d+)/#", $file['path'], $matches)) { //the file is a content file. Available to any user enrolled to this lesson.
  $result = eF_getTableDataFlat("lessons l, users_to_lessons ul", "id, share_folder", "l.archive=0 and l.id=ul.lessons_ID and ul.archive=0 and ul.users_LOGIN='".$currentUser->user['login']."'");
  $legalFolders = array_unique(array_merge($result['id'], $result['share_folder']));
  if ($currentUser->user['user_type'] != 'administrator' && $matches[1] && !in_array($matches[1], $legalFolders)) {
   throw new EfrontFileException(_YOUCANNOTACCESSTHEREQUESTEDRESOURCE, EfrontFileException::UNAUTHORIZED_ACTION);
  }
 } else if (preg_match("#content/lessons/scorm_uploaded_files/#", $file['path'], $matches)) { //the file is a temporary scorm exported file
  //proceed
 } else if (preg_match("#".G_UPLOADPATH."(.*)/projects/#", $file['path'], $matches) || preg_match("#".G_UPLOADPATH."(.*)/tests/#", $file['path'], $matches)) { //this is a project or test file. Check whether the current user has access to it
  if ($_SESSION['s_lesson_user_type'] != 'professor' || !$_SESSION['s_lessons_ID']) {
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

  if (strpos($file['path'], G_ROOTPATH.'libraries') !== false && strpos($file['path'], G_ROOTPATH.'libraries/language') === false && $file['mime_type'] != "application/inc") {
  throw new EfrontFileException(_ILLEGALPATH.': '.$file['path'], EfrontFileException :: ILLEGAL_PATH);
 }

    if (isset($_GET['action']) && $_GET['action'] == 'download') {
     $file -> sendFile(true);
    } else {
     $file -> sendFile(false);
    }
} catch (EfrontFileException $e) {
    echo EfrontSystem :: printErrorMessage($e -> getMessage());
}

?>
