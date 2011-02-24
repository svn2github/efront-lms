<?php
/*

 Users is the page that concerns EMPLOYEE administration for users with supervisor rights. It uses personal.php to perform most of the update functions,

 since the same functions need to be performed from the professor and student as well (for themseleves)

 There are 5 sub options in this page, denoted by an extra link part:

 - &add_user=1                   When we are adding a new user

 - &delete_user=<login>          When we want to delete user <login>

 - &edit_user=<login>            When we want to edit user <login>

 - &deactivate_user=<login>      When we deactivate user <login>

 - &activate_user=<login>        When we activate user <login>

 */
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}
$unprivileged = false; //This variable is used to check whether the current user is elegible (based on his role) to access this area
$currentEmployee = $currentUser -> aspects['hcd'];
if ($_SESSION['s_type'] != "administrator" && $currentEmployee -> getType() != _SUPERVISOR && !($currentEmployee -> getType() == _EMPLOYEE && (isset($_GET['add_evaluation'])||isset($_GET['edit_evaluation']) || isset($_GET['delete_evaluation'])) && $_SESSION['s_type']=="professor" )) {
 $message = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
 $message_type = "failure";
 eF_redirect("".$_SERVER['HTTP_REFERER']."&message=".$message."&message_type=".$message_type);
 exit;
} else {
 $loadScripts[] = 'includes/users';
 if (isset($_GET['delete_user']) && eF_checkParameter($_GET['delete_user'], 'login')) { //The administrator asked to delete a user
  try {
   if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
    throw new Exception(_UNAUTHORIZEDACCESS);
   }
   $user = EfrontUserFactory :: factory($_GET['delete_user']);
   if (G_VERSIONTYPE == 'enterprise') {
    $user -> aspects['hcd'] -> delete();
   }
   $user -> delete();
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
  exit;
 } elseif (isset($_GET['archive_user']) && eF_checkParameter($_GET['archive_user'], 'login')) { //The administrator asked to delete a user
  try {
   if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
    throw new Exception(_UNAUTHORIZEDACCESS);
   }
   $user = EfrontUserFactory :: factory($_GET['archive_user']);
   if (G_VERSIONTYPE == 'enterprise') {
    //$user -> aspects['hcd'] -> delete();
   }
   $user -> archive();
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
  exit;
 } elseif (isset($_GET['deactivate_user']) && eF_checkParameter($_GET['deactivate_user'], 'login') && ($_GET['deactivate_user'] != $_SESSION['s_login'])) { //The administrator asked to deactivate a user
  if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
   echo urlencode(_UNAUTHORIZEDACCESS);exit;
  }
  try {
   $user = EfrontUserFactory :: factory($_GET['deactivate_user']);
   $user -> deactivate();
   echo "0";
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
  exit;
 } elseif (isset($_GET['activate_user']) && eF_checkParameter($_GET['activate_user'], 'login')) { //The administrator asked to activate a user
  if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
   echo urlencode(_UNAUTHORIZEDACCESS);exit;
  }
  try {
   $user = EfrontUserFactory :: factory($_GET['activate_user']);
   $user -> activate();
   echo "1";
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
  exit;
 } else { //The professor just asked to view the users
  $_GET['op'] = "employees";
  include "module_hcd.php";
 }
}
