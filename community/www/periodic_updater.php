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
 $newTime = '';
 if ($_SESSION['s_login']) {
  //$entity = getUserTimeTarget($_SERVER['HTTP_REFERER']);
  $entity = $_SESSION['s_time_target'];
   //Update times for this entity
  $result = eF_executeNew("update user_times set time=time+(".time()."-timestamp_now),timestamp_now=".time()."
        where session_expired = 0 and session_custom_identifier = '".$_SESSION['s_custom_identifier']."' and users_LOGIN = '".$_SESSION['s_login']."'
         and entity = '".current($entity)."' and entity_id = '".key($entity)."'");
  if ($_SESSION['s_lesson_user_type'] == 'student' && isset($_POST['user_total_time_in_unit']) && current($entity) == 'unit' && eF_checkParameter(key($entity), 'id')) {
   $newTime = $_POST['user_total_time_in_unit'];
   if ($newTime && is_numeric($newTime)) {
    $result = eF_executeNew("insert into users_to_content (users_LOGIN, content_ID, lessons_ID) values('".$_SESSION['s_login']."', ".key($entity).", ".$_SESSION['s_lessons_ID'].") on duplicate key update total_time=$newTime");
/*				

				$result = eF_executeNew("update user_times set time=$newTime,timestamp_now=".time()."

										where session_expired = 0 and session_custom_identifier = '".$_SESSION['s_custom_identifier']."' and users_LOGIN = '".$_SESSION['s_login']."'

										and entity = '".current($entity)."' and entity_id = '".key($entity)."'");

*/
   }
  }
 }
 $onlineUsers = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);
 $messages = eF_getTableData("f_personal_messages pm, f_folders ff", "count(*)", "pm.users_LOGIN='".$_SESSION['s_login']."' and viewed='no' and f_folders_ID=ff.id and ff.name='Incoming'");
 $messages = $messages[0]['count(*)'];
 echo json_encode(array("messages" => $messages, "online" => $onlineUsers, "status" => 1));
} catch (Exception $e) {
 handleAjaxExceptions($e);
}
?>
