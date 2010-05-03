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
        if (isset($_GET['delete_user']) && eF_checkParameter($_GET['delete_user'], 'login') && !$unprivileged) { //The administrator asked to delete a user
            if (eF_deleteUser($_GET['delete_user'])) {
                $message = _USERDELETED;
                $message_type = 'success';
            } else {
                $message = _SOMEORALLOFTHEUSERELEMENTSCOULDNOTBEDELETED;
                $message_type = "failure";
            }
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=users&message=".$message."&message_type=".$message_type);
        } elseif (isset($_GET['deactivate_user']) && eF_checkParameter($_GET['deactivate_user'], 'login') && !$unprivileged) { //The administrator asked to deactivate a user
            if (eF_updateTableData("users", array('active' => 0), "login='".$_GET['deactivate_user']."'")) {
                $message = _USERDEACTIVATED;
                $message_type = 'success';
            } else {
                $message = _SOMEPROBLEMEMERGED;
                $message_type = "failure";
            }
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=users&message=".$message."&message_type=".$message_type);
        } elseif (isset($_GET['activate_user']) && eF_checkParameter($_GET['activate_user'], 'login') && !$unprivileged) { //The administrator asked to activate a user
            if (eF_updateTableData("users", array('active' => 1, 'pending' => 0), "login='".$_GET['activate_user']."'")) {
                $message = _USERACTIVATED;
                $message_type = 'success';
            } else {
                $message = _SOMEPROBLEMEMERGED;
                $message_type = "failure";
            }
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=users&message=".$message."&message_type=".$message_type);
        } elseif (isset($_GET['add_user']) || (isset($_GET['edit_user']) && $login = eF_checkParameter($_GET['edit_user'], 'login')) && !$unprivileged) { //The administrator asked to add a new user or to edit a user
            $smarty -> assign("T_PERSONAL", true);
            /**Include the personal settings file*/
            include "includes/personal.php"; //User addition and manipulation is done through personal.
        } else { //The professor just asked to view the users
            $_GET['op'] = "employees";
            include "module_hcd.php";
        }
   }
?>
