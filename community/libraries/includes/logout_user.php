<?php
/**

* Logs out a user.

*

* This page provides a list of logged in users, where the administrator may pick one to log out.

*/
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
$onlineUsers = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);
$smarty -> assign("T_ONLINE_USERS", $onlineUsers);
$smarty -> assign("T_ROLES", EfrontUser::getRoles(true));
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
