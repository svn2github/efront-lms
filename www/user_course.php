<?php
session_cache_limiter('none');          //Initialize session
session_start();
$path = "../libraries/";
require_once $path."configuration.php";
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

if (!eF_checkUser($_SESSION['s_login'], $_SESSION['s_password'])) {                                       //Any logged-in user may view an announcement
    eF_printMessage("You must login to access this page");
    exit;
}

$loadScripts[] = 'scriptaculous/scriptaculous';
$css = $GLOBALS['configuration']['css'];

if (strlen($css) > 0 && is_file(G_CUSTOMCSSPATH.$css)){
    $smarty->assign("T_CUSTOM_CSS", $css);
}
$smarty -> load_filter('output', 'eF_template_formatTimestamp');

if (isset($_GET['user']) && eF_checkParameter($_GET['user'], 'login') && isset($_GET['course'])){
    $course  = new EfrontCourse($_GET['course']);
    $lessons = $course -> getLessons(true);
    $lessonNames     = array();
    $lessonContent   = array();
    $lessonTests     = array();
    $lessonProjects  = array();
    $lessonCompleted = array();
    foreach($lessons as $id => $lesson){
        $status = EfrontStats::getUsersLessonStatus($lesson, $user);
        $status = $status[$id][$_GET['user']];
        $lessonNames[$id]     = $lesson -> lesson['name'];
        $lessonContent[$id]   = $status['overall_progress'];
        $lessonTests[$id]     = $status['tests_avg_score'];
        $lessonProjects[$id]  = $status['projects_avg_score'];
        $lessonCompleted[$id] = $status['completed'] == 1 ? _YES : _NO;
    }

    $smarty -> assign("T_LESSON_NAMES",     $lessonNames);
    $smarty -> assign("T_LESSON_CONTENT",   $lessonContent);
    $smarty -> assign("T_LESSON_TESTS",     $lessonTests);
    $smarty -> assign("T_LESSON_PROJECTS",  $lessonProjects);
    $smarty -> assign("T_LESSON_COMPLETED", $lessonCompleted);
}
$smarty -> display('user_course.tpl');
?>