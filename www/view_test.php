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

    //@todo: fix check and message
    if (isset($_GET['user']) && !eF_checkParameter($_GET['user'], 'user')) {
        eF_printMessage("Invalid user");
        exit;
    }

try {    
    if (isset($_GET['test_id'])) {
        $test         = new EfrontTest($_GET['test_id']);
        $doneTests    = eF_getTableData("completed_tests", "*", "users_LOGIN = '".$_GET['user']."' and tests_ID=".$test -> test['id']);
        $showTest = unserialize($doneTests[0]['test']);
//        $test -> setDone($_GET['user']);
    } else if (isset($_GET['content_id'])) {
        $test         = new EfrontTest($_GET['content_id'], true);
        $doneTests    = eF_getTableData("completed_tests", "*", "users_LOGIN = '".$_GET['user']."' and tests_ID=".$test -> test['id']);
        $showTest = unserialize($doneTests[0]['test']);
//        $test -> setDone($_GET['user']);
    } else if (isset($_GET['done_test_id'])) {
        $result       = eF_getTableData("completed_tests", "*", "id=".$_GET['done_test_id']);
        $test         = new EfrontTest($result[0]['tests_ID']); 
        $doneTests    = eF_getTableData("completed_tests", "*", "users_LOGIN = '".$result[0]['users_LOGIN']."' and tests_ID=".$test -> test['id']);
        $showTest = unserialize($doneTests[0]['test']); 
        //        $test -> setDone($result[0]['users_LOGIN']);    
    } else {
        throw new Exception(_INVALIDID);
    }

    //Check if current user is eligible to see this test
    if ($_SESSION['s_type'] != 'administrator') {
        //$currentUser = EfrontUserFactory :: factory($_SESSION['s_login'], false); 
        $result      = eF_getTableData("content", "lessons_ID", "id=".$test -> test['content_ID']);
        $testLesson  = new EfrontLesson($result[0]['lessons_ID']);    
        $lessonUsers = $testLesson -> getUsers();
        if (!in_array($_SESSION['s_login'], array_keys($lessonUsers))) {
            throw new Exception(_YOUARENOTAUTHORISEDTOSEETHISTEST);
        } else if ($lessonUsers[$_SESSION['s_login']]['role'] == 'student' && $_SESSION['s_login'] != $testInstance -> completedTest['login']) {
            throw new Exception(_YOUARENOTAUTHORISEDTOSEETHISTEST);
        }
    }
    
    
    if ($_SESSION['s_type'] != 'student') {
        $showTest -> options['answers']       = 1;
        $showTest -> options['given_answers'] = 1;
    }
    $testString = $showTest -> toHTMLQuickForm(new HTML_Quickform(), false, true, true);
    $testString = $showTest -> toHTMLSolved($testString, false);
    $smarty -> assign("T_SOLVED_TEST", $testString);
    
    $smarty -> assign("T_TEST", $showTest);
    if (isset($_GET['ajax'])) {
        $showTest -> handleAjaxActions();           
    }
    
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> display('view_test.tpl');
    

?>