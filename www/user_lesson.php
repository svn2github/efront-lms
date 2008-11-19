<?php
/**
 *
 */
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

try {
    if (isset($_GET['user']) && eF_checkParameter($_GET['user'], 'login') && isset($_GET['lesson'])) {
        $status     = EfrontStats :: getUsersLessonStatus($_GET['lesson'], $_GET['user']);
        $userTimes  = EfrontStats :: getUsersTime($_GET['lesson'], $_GET['user']);
        $doneTests  = EfrontStats :: getStudentsDoneTests($_GET['lesson'], $_GET['user']);

        $smarty -> assign("T_USER_DONE_TESTS", $doneTests[$_GET['user']]);
        $smarty -> assign("T_USER_STATUS", $status[$_GET['lesson']][$_GET['user']]);
        $smarty -> assign("T_USER_TIMES", $userTimes[$_GET['user']]);
        $smarty -> assign("T_TUSER", $_GET['user']);
    }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> display('user_lesson.tpl');
?>