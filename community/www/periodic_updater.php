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
define("NO_OUTPUT_BUFFERING", true);
/** Configuration file.*/
require_once $path."configuration.php";
if (!isset($_SESSION['s_login']) || !eF_checkParameter($_SESSION['s_login'], 'login')) {
 echo "No active session found";
 exit;
}

try {

 if ($_SESSION['timestamp']) {
  $entity = getUserTimeTarget($_SERVER['HTTP_REFERER']);
  $fields = array('timestamp_now' => time(),
      'time' => $_SESSION['time'] + time() - $_SESSION['timestamp']);
  eF_updateTableData("user_times", $fields, "session_id = '".session_id()."' and users_LOGIN='".$_SESSION['s_login']."' and entity='".current($entity)."' and entity_id='".key($entity)."'");
 }

 $onlineUsers = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);
 $messages = eF_getTableData("f_personal_messages pm, f_folders ff", "count(*)", "pm.users_LOGIN='".$_SESSION['s_login']."' and viewed='no' and f_folders_ID=ff.id and ff.name='Incoming'");
 $messages = $messages[0]['count(*)'];

 echo json_encode(array("messages" => $messages, "online" => $onlineUsers, "status" => 1));

} catch (Exception $e) {
 handleAjaxExceptions($e);
}

?>
