<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*

if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden' && $currentUser -> user['login'] != $_GET['edit_user']) {

    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");

}



!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] == 'change' ? $_change_ = 1 : $_change_ = 0;

$smarty -> assign("_change_", $_change_);



 */
if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}
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
} elseif (isset($_GET['add_user']) || (isset($_GET['edit_user']) && $login = eF_checkParameter($_GET['edit_user'], 'login'))) { //The administrator asked to add a new user or to edit a user
    $smarty -> assign("T_PERSONAL", true);
    /**Include the personal settings file*/
    include "includes/personal.php"; //User addition and manipulation is done through personal.
} else { //The admin just asked to view the users
        if (isset($_GET['ajax'])) {
            isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
            if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                $sort = $_GET['sort'];
                isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
            } else {
                $sort = 'login';
            }
            $languages = EfrontSystem :: getLanguages(true);
            $smarty -> assign("T_LANGUAGES", $languages);
            $users = eF_getTableData("users", "*", "archive = 0");
            //$user_lessons = eF_getTableDataFlat("users_to_lessons as ul, lessons as l", "ul.users_LOGIN, count(ul.lessons_ID) as lessons_num", "ul.lessons_ID=l.id AND l.archive=0 AND ul.archive=0", "", "ul.users_LOGIN");
            //$user_courses = eF_getTableDataFlat("users_to_courses as uc, courses as c", "uc.users_LOGIN, count(uc.courses_ID) as courses_num", "uc.courses_ID=c.id AND c.archive=0 AND uc.archive=0", "", "uc.users_LOGIN");
            $user_groups = eF_getTableDataFlat("users_to_groups", "users_LOGIN, count(groups_ID) as groups_num", "", "", "users_LOGIN");
            $user_lessons = array_combine($user_lessons['users_LOGIN'], $user_lessons['lessons_num']);
            $user_courses = array_combine($user_courses['users_LOGIN'], $user_courses['courses_num']);
            $user_groups = array_combine($user_groups['users_LOGIN'], $user_groups['groups_num']);
            array_walk($users, create_function('&$v, $k, $s', '$s[$v["login"]] ? $v["lessons_num"] = $s[$v["login"]] : $v["lessons_num"] = 0;'), $user_lessons); //Assign lessons number to users array (this way we eliminate the need for an expensive explicit loop)
            array_walk($users, create_function('&$v, $k, $s', '$s[$v["login"]] ? $v["courses_num"] = $s[$v["login"]] : $v["courses_num"] = 0;'), $user_courses);
            array_walk($users, create_function('&$v, $k, $s', '$s[$v["login"]] ? $v["groups_num"] = $s[$v["login"]] : $v["groups_num"] = 0;'), $user_groups);
            $result = eF_getTableDataFlat("logs", "users_LOGIN, timestamp", "action = 'login'", "timestamp");
            $lastLogins = array_combine($result['users_LOGIN'], $result['timestamp']);
            foreach ($users as $key => $value) {
                $users[$key]['last_login'] = $lastLogins[$value['login']];
    if (isset($_COOKIE['toggle_active'])) {
     if (($_COOKIE['toggle_active'] == 1 && !$value['active']) || ($_COOKIE['toggle_active'] == -1 && $value['active'])) {
      unset($users[$key]);
     }
    }
            }

            $users = eF_multiSort($users, $sort, $order);
            if (isset($_GET['filter'])) {
                $users = eF_filterData($users, $_GET['filter']);
            }
            $smarty -> assign("T_USERS_SIZE", sizeof($users));

            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $users = array_slice($users, $offset, $limit);
            }

            $smarty -> assign("T_USERS", $users);
            $smarty -> assign("T_ROLES", EfrontUser :: getRoles(true));
            $smarty -> display('administrator.tpl');
            exit;
        }




}
