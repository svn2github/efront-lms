<?php
/**

* Logs out a user.

*

* This page provides a list of logged in users, where the administrator may pick one to log out.

*/
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
$loadScripts[] = 'includes/logout_user';
if (isset($_GET['ajax']) && isset($_GET['logout']) && $_GET['logout'] != $currentUser -> user['login']) {
 try {
  $user = EfrontUserFactory :: factory($_GET['logout']);
  $user -> logout();
  echo json_encode(array('status' => 1));
  exit;
 } catch (Exception $e) {
  handleAjaxExceptions($e);
 }
}
$smarty -> assign("T_ROLES", EfrontUser::getRoles(true));
if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
 $dataSource = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);
 foreach ($dataSource as $key => $value) {
  $dataSource[$key]['total_seconds'] = $value['time']['total_seconds'];
 }
    $tableName = 'usersTable';

 include "sorted_table.php";
}
