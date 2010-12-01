<?php

// For ajax calls just create the filters
$stats_filters = array();
if (isset($_GET['group_filter']) && eF_checkParameter($_GET['group_filter'], 'id') && $_GET['group_filter'] != -1) {
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
if (!isset($_GET['ajax'])) {
 $groups = EfrontGroup :: getGroups();
 $smarty -> assign("T_GROUPS", $groups);
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
