<?php
/*** Evaluations are deleted either by administrators or by the users who wrote them ***/
/*
 // Check if you are changing your own data - every HCD type is allowed to do that
 if ($_GET['ctg'] != 'personal') {
 if ($currentUser -> getType() != "administrator") {      // Administrators are allowed to do anything - no need to check further

 // If you are a Supervisor...
 if ($currentEmployee -> isSupervisor() ) {
 $smarty -> assign("T_IS_SUPERVISOR", true);

 // Check if you can manage/see this employee`s data - if not, prevent access
 if (isset($_GET['edit_user']) && !$currentEmployee -> supervisesEmployee($_GET['edit_user']) ) {
 eF_redirect("".$_SERVER['HTTP_REFERER']."&message=".urlencode(_SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION)."&message_type=failure");
 }

 } else {

 // Only Employees with no supervisor rights reach this point
 // Simple employees who are professors are allowed to manage evaluations - if this is not the case, then prevent access
 if ( !($currentUser -> getType() == "professor" && (isset($_GET['add_evaluation']) || isset($_GET['edit_evaluation']) || isset($_GET['delete_evaluation'])))) {
 eF_redirect("".$_SERVER['HTTP_REFERER']."&message=".urlencode(_SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION)."&message_type=failure");
 }

 }
 }
 }
 */
