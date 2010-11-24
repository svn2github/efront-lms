<?php
/**

 * Set positions of control panel elements

 *

 * This page is used to store the positions of the users' control panel elements

 *

 * @package eFront

 * @version 1.0

 */
session_cache_limiter('none');
session_start();
$path = "../libraries/";
/** Configuration file.*/
include_once $path."configuration.php";
try {
 $currentUser = EfrontUser :: checkUserAccess();
} catch (Exception $e) {
 echo "<script>parent.location = 'index.php?message=".urlencode($e -> getMessage().' ('.$e -> getCode().')')."&message_type=failure'</script>"; //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
 exit;
}
try {
    if ($_SESSION['s_lessons_ID']) {
        $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
    } elseif ($_POST['lessons_ID']) {
        $currentLesson = new EfrontLesson($_POST['lessons_ID']);
    }
    if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'] != 'change') {
        throw new Exception();
    }
} catch (Exception $e) {
    eF_redirect("index.php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    exit;
}
try {
    if (isset($_POST['firstlist']) && isset($_POST['secondlist'])) {
        parse_str($_POST['firstlist']);
        parse_str($_POST['secondlist']);
        parse_str($_POST['visibility']);
        mb_internal_encoding('utf-8'); //This must be put here due to PHP bug #48697
        if ($visibility) {
            $positions = serialize(array('first' => array_unique($firstlist), 'second' => array_unique($secondlist), 'visibility' => $visibility));
        } else {
            $positions = serialize(array('first' => array_unique($firstlist), 'second' => array_unique($secondlist)));
        }

        //Dashboard positions
        if ($_POST['dashboard']) {
            eF_updateTableData("users", array('dashboard_positions' => $positions), "login='".($currentUser -> user['login'])."'");
        }
        //administrator control panel positions
        else if ($currentUser -> user['user_type'] == 'administrator' && !isset($_POST['lessons_ID'])) {
            $result = eF_getTableData("configuration", "value", "name = '".$currentUser -> user['login']."_positions'");
            if (sizeof($result) > 0) {
                $result = eF_updateTableData("configuration", array('value' => $positions), "name = '".$currentUser -> user['login']."_positions'");
            } else {
                $result = eF_insertTableData("configuration", array('name' => $currentUser -> user['login'].'_positions', 'value' => $positions));
            }
        }
        //lesson control panel positions
        else {
            if (isset($_POST['set_default']) && ($currentUser -> user['user_type'] == 'administrator' || $currentLesson -> getRole($currentUser -> user['login']) == 'professor')) {
                $currentLesson -> setOptions(array("default_positions" => $positions));
                $positions = serialize(array('first' => array_unique($firstlist), 'second' => array_unique($secondlist), 'visibility' => $visibility, 'update' => true));
                $lessonStudents = $currentLesson -> getUsers('student');
                if (sizeof($lessonStudents) > 0) {
                    $users = implode("','", array_keys($lessonStudents));
                    eF_updateTableData("users_to_lessons", array('positions' => $positions), "users_LOGIN in ('".$users."') and lessons_ID=".$currentLesson -> lesson['id']);
                }
            } else {
                if (!$visibility) {
                    $result = eF_getTableData("users_to_lessons", "positions", "lessons_ID=".$currentLesson -> lesson['id']." AND users_LOGIN='".$currentUser -> user['login']."'");
                    $result = unserialize($result[0]['positions']);
                    $visibility = $result['visibility'];
                    if (isset($result['visibility'])) {
                        $positions = serialize(array('first' => array_unique($firstlist), 'second' => array_unique($secondlist), 'visibility' => $visibility));
                    }

                }
                eF_updateTableData("users_to_lessons", array('positions' => $positions), "lessons_ID=".$currentLesson -> lesson['id']." AND users_LOGIN='".$currentUser -> user['login']."'");
            }
        }

    }
} catch (Exception $e) {
 handleAjaxExceptions($e);
}
?>
