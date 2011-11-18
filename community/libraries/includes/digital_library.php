<?php
/**

* 

* @package eFront

* @version 3.6.0

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
$loadScripts[] = 'includes/news';
//Create shorthands for user type, to avoid long variable names
$_student_ = $_professor_ = $_admin_ = 0;
if ($_SESSION['s_lesson_user_type'] == 'student') {
    $_student_ = 1;
} else if ($_SESSION['s_lesson_user_type'] == 'professor') {
    $_professor_ = 1;
} else {
    $_admin_ = 1;
}
//Create shorthands for user access rights, to avoid long variable names
$_change_ = $_hidden_ = 0;
if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
    $_change_ = 1;
} elseif ($GLOBALS['configuration']['disable_news'] == 1 || (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden')) {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}
if ($currentUser -> coreAccess['content'] == 'hidden') {
 eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}

$basedir = $currentLesson -> getDirectory();
try {
 $folderId = $currentLesson -> lesson['share_folder'] ? $currentLesson -> lesson['share_folder'] : $currentLesson -> lesson['id'];
 $result = eF_getTableData("files", "*", "shared=".$folderId);
 foreach ($result as $value) {
  $sharedFiles[G_ROOTPATH.$value['path']] = new EfrontFile($value['id']);
 }
 if (sizeof($sharedFiles) > 0) {
  $filesystem = new FileSystemTree($basedir, true);
  //changed to take account subfolders in efficient way
  //$filesystemIterator = new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new EfrontDBOnlyFilterIterator(new EfrontFileOnlyFilterIterator(new RecursiveIteratorIterator($filesystem -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('shared' => $currentLesson -> lesson['id'])));
  $filesystemIterator = new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new EfrontDBOnlyFilterIterator(new EfrontFileOnlyFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($sharedFiles), RecursiveIteratorIterator :: SELF_FIRST))), array('shared' => $folderId)));

  $url = basename($_SERVER['PHP_SELF']).'?ctg=digital_library';
  $options = array('share' => false, 'zip' => false, 'folders' => false, 'delete' => false, 'edit' => false, 'create_folder' => false, 'upload' => false);

  include "file_manager.php";
 }
} catch (Exception $e) {
 $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
 $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
 $message_type = 'failure';
}
