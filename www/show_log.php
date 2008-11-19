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

if (isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id')) {
    $result = eF_getTableData("logs", "*", "id=".$_GET['id']);    
    if ($result[0]['lessons_ID']) {
        $lessonName = eF_getTableData("lessons", "name", "id=".$result[0]['lessons_ID']);
    }    
    if ($result[0]['action'] == 'content' && $result[0]['comments']) {
        $contentName = eF_getTableData("content", "name", "id=".$result[0]['comments']);
    }
    $logInfo = array('login'      => $result[0]['users_LOGIN'],
                     'timestamp'  => $result[0]['timestamp'],
                     'action'     => $result[0]['action'],
                     'session_ip' => eF_decodeIP($result[0]['session_ip']),
                     'lesson'     => $lessonName[0]['name'],
                     'content'    => $contentName[0]['name']);
    $smarty -> assign("T_LOG_INFO", $logInfo);
}
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> display("show_log.tpl");


?>