<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$loadScripts[] = 'scriptaculous/controls';
$loadScripts[] = 'includes/statistics';

$smarty -> assign("T_CATEGORY", 'statistics');
$smarty -> assign("T_BASIC_TYPE", $currentUser -> user['user_type']);

$formatDate = eF_dateFormat(false);
$smarty -> assign("T_DATE_FORMATGENERAL", $formatDate);
$isProfessor = 0;
$isStudent = 0;

//check to see if the user has any lessons as a student and any lessons as professor
$lessonRoles = EfrontLessonUser::getLessonsRoles();
if ($currentUser -> user['user_type'] != 'administrator') {
    $lessons = $currentUser -> getLessons(false);
    foreach ($lessons as $key => $type) {
        if ($lessonRoles[$type] == 'professor') {
            $isProfessor = 1;
            $professorLessons[] = $key;
        } else if ($type == 'student') {
            $isStudent = 1;
            $studentLessons[] = $key;
        }
    }
}
$smarty -> assign("T_ISPROFESSOR", $isProfessor);
$smarty -> assign("T_ISSTUDENT", $isStudent);
// Only administrators and supervisors are allowed to see user reports
if ($currentUser -> user['user_type'] != 'administrator' && !$isSupervisor) {
    if ($isProfessor) {
        if (isset($currentLesson) && !in_array($currentLesson -> lesson['id'], $professorLessons)) {
            $_GET['option'] = 'user';
        } else if (!isset($currentLesson) && $currentUser -> user['user_type'] != 'professor') {
            $_GET['option'] = 'user';
        }
    } else {
        $_GET['option'] = 'user';
        if (!$_SESSION['s_lessons_ID']) {
            $_GET['sel_user'] = $_SESSION['s_login'];
        }
    }
}
$smarty -> assign("T_OPTION", $_GET['option']);
try {
    /*no option is set, so just show the available options*/
    if (!isset($_GET['option'])) {
        if ($currentUser -> user['user_type'] == 'administrator') {
            $options[] = array('text' => _USERSTATISTICS, 'image' => "32x32/user.png", 'href' => "administrator.php?ctg=statistics&option=user");
            $options[] = array('text' => _LESSONSTATISTICS, 'image' => "32x32/lessons.png", 'href' => "administrator.php?ctg=statistics&option=lesson");
            $options[] = array('text' => _COURSESTATISTICS, 'image' => "32x32/courses.png", 'href' => "administrator.php?ctg=statistics&option=course");
            $options[] = array('text' => _SYSTEMSTATISTICS, 'image' => "32x32/reports.png", 'href' => "administrator.php?ctg=statistics&option=system");
            $smarty -> assign("T_STATISTICS_OPTIONS", $options);
        } else if ($isProfessor) {
            $options[] = array('text' => _USERSTATISTICS, 'image' => "32x32/user.png", 'href' => $_SERVER['PHP_SELF']."?ctg=statistics&option=user");
            $options[] = array('text' => _LESSONSTATISTICS, 'image' => "32x32/lessons.png", 'href' => $_SERVER['PHP_SELF']."?ctg=statistics&option=lesson");
            $options[] = array('text' => _COURSESTATISTICS, 'image' => "32x32/courses.png", 'href' => $_SERVER['PHP_SELF']."?ctg=statistics&option=course");
            if ($GLOBALS['configuration']['disable_tests'] != 1) {
             $options[] = array('text' => _TESTSTATISTICS, 'image' => "32x32/tests.png", 'href' => $_SERVER['PHP_SELF']."?ctg=statistics&option=test");
            }
            $smarty -> assign("T_STATISTICS_OPTIONS", $options);
        } else if ($isSupervisor) {
            $options[] = array('text' => _USERSTATISTICS, 'image' => "32x32/user.png", 'href' => $_SERVER['PHP_SELF']."?ctg=statistics&option=user");
            $smarty -> assign("T_STATISTICS_OPTIONS", $options);
        }
    } else if ($_GET['option'] == 'user') {
        require_once("statistics/users_stats.php");
    } else if ($_GET['option'] == 'lesson') {
        require_once("statistics/lessons_stats.php");
    } else if ($_GET['option'] == 'course') {
        require_once("statistics/courses_stats.php");
    } else if ($_GET['option'] == 'test') {
        require_once("statistics/tests_stats.php");
    } else if ($_GET['option'] == 'system') {
        require_once("statistics/system_stats.php");
 } elseif ($_GET['option'] == 'custom') {
        require_once("statistics/custom_stats.php");
 } elseif ($_GET['option'] == 'certificate') {
        require_once("statistics/certificates_stats.php");
 } elseif ($_GET['option'] == 'events') {
        require_once("statistics/events_stats.php");
 } else if ($_GET['option'] == "groups") {
        require_once("statistics/groups_stats.php");
 } else if ($_GET['option'] == "branches") {
        require_once("statistics/branches_stats.php");
 } else if ($_GET['option'] == "participation") {
        require_once("statistics/participation_stats.php");
 }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
?>
