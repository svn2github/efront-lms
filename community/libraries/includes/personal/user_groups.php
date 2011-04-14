<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if (isset($currentUser->coreAccess['users']) && $currentUser->coreAccess['users'] != 'change') {
 $_change_groups_ = false;
} else if ($currentUser -> user['user_type'] == 'administrator') {
 $_change_groups_ = true;
} else if ($currentUser -> user['login'] == $editedUser -> user['login']) {
 $_change_groups_ = false;






} else {
 $_change_groups_ = false;
}
$smarty -> assign("_change_groups_", $_change_groups_);


try {
 if (isset($_GET['ajax']) && $_GET['ajax'] == "groupsTable") {
  $userGroups = $editedUser -> getGroups();
  $groups = eF_getTableData("groups", "*", "active=1");
  foreach ($groups as $key => $group) {
   $groups[$key]['partof'] = 0;
   if (in_array($group['id'], array_keys($userGroups))) {
    $groups[$key]['partof'] = 1;
   } else if (!$group['active'] || !$_change_groups_) {
    unset($groups[$key]);
   }
  }

  $dataSource = $groups;
  $tableName = 'groupsTable';
  include("sorted_table.php");
 }
} catch (Exception $e) {
 handleAjaxExceptions($e);
}


if (isset($_GET['postAjaxRequest']) && $_change_groups_) {
 try {
  if ($_GET['insert'] == "true") {
   $editedUser -> addGroups($_GET['add_group']);
  } else if ($_GET['insert'] == "false") {
   $editedUser -> removeGroups($_GET['add_group']);
  } else if (isset($_GET['addAll'])) {
   $groups = eF_getTableDataFlat("groups", "id", "active=1");
   isset($_GET['filter']) ? $groups = eF_filterData($groups, $_GET['filter']) : null;
   $editedUser -> addGroups($groups['id']);
  } else if (isset($_GET['removeAll'])) {
   $groups = eF_getTableDataFlat("groups", "id", "active=1");
   isset($_GET['filter']) ? $groups = eF_filterData($groups, $_GET['filter']) : null;
   $editedUser -> removeGroups($groups['id']);
  }
 } catch (Exception $e) {
  handleAjaxExceptions($e);
 }
 exit;
}
