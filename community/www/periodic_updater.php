<?php
/**

 * Periodic updater

 * 

 * This page is used to periodically revive the current user, as well as check for unread messages etc   

 *

 * @package eFront

 */
session_cache_limiter('none');
session_start();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$path = "../libraries/";
$loadLanguage = false;
/** Configuration file.*/
require_once $path."configuration.php";
try {
    $user = EfrontUserFactory :: factory($_SESSION['s_login']);
 $user -> refreshLogin();

 $messages = eF_getTableData("f_personal_messages pm, f_folders ff", "count(*)", "pm.users_LOGIN='".$user -> user['login']."' and viewed='no' and f_folders_ID=ff.id and ff.name='Incoming'");
 $messages = $messages[0]['count(*)'];

 $onlineUsers = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);

 echo json_encode(array("messages" => $messages, "online" => $onlineUsers));

} catch (Exception $e) {
 handleAjaxExceptions($e);
}

?>
