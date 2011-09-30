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
 $jsonValues = array();
 if ($_SESSION['s_login']) {
  $entity = getUserTimeTarget($_GET['HTTP_REFERER']);
  //$entity = $_SESSION['s_time_target'];
   //Update times for this entity
  $result = eF_executeNew("update user_times set time=time+(".time()."-timestamp_now),timestamp_now=".time()."
        where session_expired = 0 and session_custom_identifier = '".$_SESSION['s_custom_identifier']."' and users_LOGIN = '".$_SESSION['s_login']."'
         and entity = '".current($entity)."' and entity_id = '".key($entity)."'");
  if ($_SESSION['s_lesson_user_type'] == 'student' && isset($_POST['user_total_time_in_unit']) && current($entity) == 'unit' && eF_checkParameter(key($entity), 'id')) {
   $newTime = $_POST['user_total_time_in_unit'];
   $jsonValues['entity'] = current($entity);
   $jsonValues['entity_id'] = current($entity);
   if ($newTime && is_numeric($newTime)) {

    //debug();
    $result = eF_executeNew("insert into users_to_content (users_LOGIN, content_ID, lessons_ID) values('".$_SESSION['s_login']."', ".key($entity).", ".$_SESSION['s_lessons_ID'].") on duplicate key update total_time=$newTime");
    $jsonValues['time_in_unit'] = EfrontTimes::formatTimeForReporting($newTime);
    //$jsonValues['time_in_lesson'] = EfrontTimes::formatTimeForReporting((EfrontLesson::getUserActiveTimeInLesson($_SESSION['s_login'], $_SESSION['s_lessons_ID'])));

    //debug(false);
/*				

				$result = eF_executeNew("update user_times set time=$newTime,timestamp_now=".time()."

										where session_expired = 0 and session_custom_identifier = '".$_SESSION['s_custom_identifier']."' and users_LOGIN = '".$_SESSION['s_login']."'

										and entity = '".current($entity)."' and entity_id = '".key($entity)."'");

*/
   } else {
    $jsonValues['old_time_in_unit'] = EfrontTimes::formatTimeForReporting((EfrontLesson::getUserActiveTimeInUnit($_SESSION['s_login'], key($entity))));
    $jsonValues['old_time_in_lesson'] = EfrontTimes::formatTimeForReporting((EfrontLesson::getUserActiveTimeInLesson($_SESSION['s_login'], $_SESSION['s_lessons_ID'])));
   }
  }
  try {
   $currentUser = EfrontUser :: checkUserAccess();
  } catch (Exception $e) {
   if ($e -> getCode() == EfrontUserException :: USER_NOT_LOGGED_IN) {
    echo json_encode(array('status' => 0, 'code' => -1));
    exit;
   }
  }
 }
 $jsonValues['online'] = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);
 $messages = eF_getTableData("f_personal_messages pm, f_folders ff", "count(*)", "pm.users_LOGIN='".$_SESSION['s_login']."' and viewed='no' and f_folders_ID=ff.id and ff.name='Incoming'");
 $jsonValues['messages'] = $messages[0]['count(*)'];
 $jsonValues['status'] = 1;
 echo json_encode($jsonValues);
} catch (Exception $e) {
 handleAjaxExceptions($e);
}
?>
