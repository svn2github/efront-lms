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
try {
 $currentUser = EfrontUser :: checkUserAccess();
} catch (Exception $e) {
 echo "<script>parent.location = 'index.php?message=".urlencode($e -> getMessage().' ('.$e -> getCode().')')."&message_type=failure'</script>"; //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
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
