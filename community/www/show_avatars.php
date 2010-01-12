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
    eF_redirect("index.php");
    exit;
}

$current_dir = getcwd();
chdir(G_SYSTEMAVATARSPATH);
$avatar_files = eF_getDirContents(false, 'png');
chdir($current_dir);

$smarty -> assign("T_SYSTEM_AVATARS", $avatar_files);

if ($GLOBALS['configuration']['social_modules_activated'] > 0) {
	$smarty -> assign ("T_SOCIAL_INTERFACE", 1);
}
	
$smarty -> display("show_avatars.tpl")

?>