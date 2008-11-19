<?php
/**
* Show avatars
* 
* This file is the page which shows the avatars list
* @package eFront
* @version 1.0
*/

session_cache_limiter('none');
session_start();

$path = "../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";


if (!eF_checkUser($_SESSION['s_login'], $_SESSION['s_password'])) {
    header("location:index.php");
    exit;
}

$current_dir = getcwd();
chdir('images/avatars/system_avatars/');
$avatar_files = eF_getDirContents(false, 'png');
chdir($current_dir);

$smarty -> assign("T_SYSTEM_AVATARS", $avatar_files);

$smarty -> display("show_avatars.tpl")

?>