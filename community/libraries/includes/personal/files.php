<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

$loadScripts[] = 'includes/filemanager';

$url = basename($_SERVER['PHP_SELF']).'?ctg=personal&user='.$editedUser->user['login']."&op=files";
$options = array('db_files_only' => false, 'share' => false);
$basedir = $editedUser -> getDirectory().'module_hcd/public/';
include "file_manager.php";
