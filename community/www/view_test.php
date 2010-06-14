<?php
/**

 *

 */
session_cache_limiter('none'); //Initialize session
session_start();
$path = "../libraries/";
require_once $path."configuration.php";
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$loadScripts = array('scriptaculous/prototype', 'EfrontScripts');


/*

 * 

 * URL: <url_to_test>?aicc_sid={CMI generated session ID}&aicc_url={URL to receive AU messages}&[AU specific launch parameters }

 */
try {
 $currentUser = EfrontUser :: checkUserAccess();
} catch (Exception $e) {
 echo "<script>parent.location = 'index.php?message=".urlencode($e -> getMessage().' ('.$e -> getCode().')')."&message_type=failure'</script>"; //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
 exit;
}
try {
    if (isset($_GET['test_id'])) {
        $test = new EfrontTest($_GET['test_id']);
        $doneTests = eF_getTableData("completed_tests", "*", "status != 'deleted' and users_LOGIN = '".$_GET['user']."' and tests_ID=".$test -> test['id']);
//        $test -> setDone($_GET['user']);
    } else if (isset($_GET['content_id'])) {
        $test = new EfrontTest($_GET['content_id'], true);
        $doneTests = eF_getTableData("completed_tests", "*", "status != 'deleted' and users_LOGIN = '".$_GET['user']."' and tests_ID=".$test -> test['id']);
//        $test -> setDone($_GET['user']);
    } else if (isset($_GET['done_test_id'])) {
        $result = eF_getTableData("completed_tests", "*", "status != 'deleted' and id=".$_GET['done_test_id']);
        $test = new EfrontTest($result[0]['tests_ID']);
        $doneTests = eF_getTableData("completed_tests", "*", "status != 'deleted' and users_LOGIN = '".$result[0]['users_LOGIN']."' and tests_ID=".$test -> test['id']);
        $_GET['user'] = $result[0]['users_LOGIN'];
        //        $test -> setDone($result[0]['users_LOGIN']);    
    } else {
        throw new Exception(_INVALIDID);
    }

    //Reorder done tests in a per-id fashion
    $temp = array();
    foreach ($doneTests as $value) {
        $temp[$value['id']] = $value;
    }
    $doneTests = $temp;

    if (isset($_GET['show_solved_test']) && in_array($_GET['show_solved_test'], array_keys($doneTests))) {
        $showTest = unserialize($doneTests[$_GET['show_solved_test']]['test']);
    } else if (isset($_GET['done_test_id']) && in_array($_GET['done_test_id'], array_keys($doneTests))) {
        $showTest = unserialize($doneTests[$_GET['done_test_id']]['test']);
    } else {
        $showTest = unserialize($doneTests[key($doneTests)]['test']); //Take the first in the row
    }

    //Check if current user is eligible to see this test
    if ($_SESSION['s_type'] != 'administrator') {
        //$currentUser = EfrontUserFactory :: factory($_SESSION['s_login'], false); 
        $result = eF_getTableData("content", "lessons_ID", "id=".$test -> test['content_ID']);
        $testLesson = new EfrontLesson($result[0]['lessons_ID']);
        $lessonUsers = $testLesson -> getUsers();

        if (!in_array($_SESSION['s_login'], array_keys($lessonUsers))) {
            throw new Exception(_YOUARENOTAUTHORISEDTOSEETHISTEST);
        } else if ($lessonUsers[$_SESSION['s_login']]['role'] == 'student' && $_SESSION['s_login'] != $showTest -> completedTest['login']) {
            throw new Exception(_YOUARENOTAUTHORISEDTOSEETHISTEST);
        }
    }


    if ($_SESSION['s_type'] != 'student') {
        $showTest -> options['answers'] = 1;
        $showTest -> options['given_answers'] = 1;
        $editHanles = true;
    }

    $testString = $showTest -> toHTMLQuickForm(new HTML_Quickform(), false, true, $editHanles);
    $testString = $showTest -> toHTMLSolved($testString, $editHanles);
    if (isset($_GET['test_analysis'])) {
        require_once 'charts/php-ofc-library/open-flash-chart.php';

        list($parentScores, $analysisCode) = $showTest -> analyseTest();

        $smarty -> assign("T_CONTENT_ANALYSIS", $analysisCode);
        $smarty -> assign("T_TEST_DATA", $showTest);

        $status = $showTest -> getStatus($showTest -> completedTest['login']);
        $smarty -> assign("T_TEST_STATUS", $status);

        if (isset($_GET['display_chart'])) {
            $url = basename($_SERVER['PHP_SELF']).'?test_id='.$showTest -> test['id'].'&user='.$showTest -> completedTest['login'].'&test_analysis=1&selected_unit='.$_GET['selected_unit'].'&show_chart=1&show_solved_test='.$_GET['show_solved_test'];
            echo $showTest -> displayChart($url);
            exit;
        } elseif (isset($_GET['show_chart'])) {
            echo $showTest -> calculateChart($parentScores);
            exit;
        }
    }
    $smarty -> assign("T_SOLVED_TEST", $testString);

    $smarty -> assign("T_TEST", $showTest);
    if (isset($_GET['ajax'])) {
        $showTest -> handleAjaxActions();
    }

} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($loadScripts));
$smarty -> display('view_test.tpl');


?>
