<?php

// For ajax calls just create the filters
$stats_filters = array();
if (isset($_GET['group_filter']) && $_GET['group_filter'] && $_GET['group_filter'] != -1) {
 $stats_filters[] = array("table" => "users_to_groups as filter_ug",
        "joinField" => "filter_ug.users_LOGIN",
        "condition" => "filter_ug.groups_ID = " . $_GET['group_filter']);
}

if (isset($_GET['user_filter'])) {
 if ($_GET['user_filter'] != 3) {
  $stats_filters[] = array("condition" => ($_GET['user_filter'] == 1)?"u.active = 1":"u.active = 0");
 }
} else {
 $stats_filters[] = array("condition" => "u.active = 1");
}

if (isset($_GET['branch_filter']) && $_GET['branch_filter'] != 0) {
 if (!$_GET['subbranches']) {
  $stats_filters[] = array("table" => "module_hcd_employee_works_at_branch as filter_eb",
         "joinField" => "filter_eb.users_LOGIN",
         "condition" => "(filter_eb.branch_ID = " . $_GET['branch_filter'] . " AND filter_eb.assigned = 1)");
 } else {
  $branches = array($_GET['branch_filter']);
  $branchesTree = new EfrontBranchesTree();
  $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($branchesTree -> getNodeChildren($_GET['branch_filter'])), RecursiveIteratorIterator :: SELF_FIRST));
  foreach($iterator as $key => $value) {
   $branches[] = $key;
  }

  $stats_filters[] = array("table" => "module_hcd_employee_works_at_branch as filter_eb",
         "joinField" => "filter_eb.users_LOGIN",
         "condition" => "(filter_eb.branch_ID in (" . implode(",", $branches) . ") AND filter_eb.assigned = 1)");
 }
}

if (!isset($_GET['ajax'])) {

 if (isset($_GET['group_filter']) && $_GET['group_filter'] != -1) {
  try {
   $selectedGroup = new EfrontGroup($_GET['group_filter']);
   $groupUsers = $selectedGroup -> getUsers();
  } catch (Exception $e) {
   $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
   $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
   $message_type = 'failure';
  }
 }
}
// Create url for ajax tables
$stats_url = "";
if (isset($_GET['group_filter']) && $_GET['group_filter'] != -1) {
 $stats_url .= "&group_filter=". $_GET['group_filter'];
}
if (isset($_GET['user_filter']) && $_GET['user_filter'] != 0) {
 $stats_url .= "&user_filter=". $_GET['user_filter'];
}
if (isset($_GET['subbranches'])) {
 $stats_url .= "&subbranches=". $_GET['subbranches'];
}
$smarty -> assign("T_STATS_FILTERS_URL", $stats_url);
