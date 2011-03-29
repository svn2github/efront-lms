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
/*

 *

 * URL: <url_to_test>?aicc_sid={CMI generated session ID}&aicc_url={URL to receive AU messages}&[AU specific launch parameters }

 */
try {
 $currentUser = EfrontUser :: checkUserAccess();
} catch (Exception $e) {
 echo "<script>parent.location = 'index.php?logout=true&message=".urlencode($e -> getMessage().' ('.$e -> getCode().')')."&message_type=failure'</script>"; //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
 exit;
}
try {
    if (isset($_GET['test_id'])) {
        $test = new EfrontTest($_GET['test_id']);
        $doneTests = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "completed_tests", "*", "status != 'deleted' and users_LOGIN = '".$_GET['user']."' and tests_ID=".$test -> test['id']);
//        $test -> setDone($_GET['user']);
    } else if (isset($_GET['content_id'])) {
        $test = new EfrontTest($_GET['content_id'], true);
        $doneTests = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "*", "status != 'deleted' and users_LOGIN = '".$_GET['user']."' and tests_ID=".$test -> test['id']);
//        $test -> setDone($_GET['user']);
    } else if (isset($_GET['done_test_id'])) {
        $result = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "*", "status != 'deleted' and id=".$_GET['done_test_id']);
        $test = new EfrontTest($result[0]['tests_ID']);
        $doneTests = EfrontCompletedTest::retrieveCompletedTest("completed_tests", "*", "status != 'deleted' and users_LOGIN = '".$result[0]['users_LOGIN']."' and tests_ID=".$test -> test['id']);

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
 $result = eF_getTableData("content", "ctg_type","id=".$showTest -> test['content_ID']);
 $testType = $result[0]['ctg_type'];
    $smarty -> assign("T_TEST_TYPE", $testType);

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
        $showTest -> options['given_answers'] = 1;
        if ($testType != "feedback") {
         $showTest -> options['answers'] = 1;
         $editHanles = true;
        }
    }


    if ($testType == "feedback") {
     $testString = $showTest -> toHTMLQuickForm(new HTML_Quickform(), false, true, $editHanles, false, true);
     $testString = $showTest -> toHTMLSolved($testString, $editHanles, true);
    } else{
     $testString = $showTest -> toHTMLQuickForm(new HTML_Quickform(), false, true, $editHanles);
     $testString = $showTest -> toHTMLSolved($testString, $editHanles);
    }

    if (isset($_GET['test_analysis'])) {
     $loadScripts[] = 'scriptaculous/excanvas';
     $loadScripts[] = 'scriptaculous/flotr';
     $loadScripts[] = 'scriptaculous/controls';
     $loadScripts[] = 'includes/graphs';

     list($parentScores, $analysisCode) = $showTest -> analyseTest();

     $smarty -> assign("T_CONTENT_ANALYSIS", $analysisCode);
     $smarty -> assign("T_TEST_DATA", $showTest);

     $status = $showTest -> getStatus($result[0]['users_LOGIN']);
     $smarty -> assign("T_TEST_STATUS", $status);

     try {
      if (isset($_GET['ajax']) && $_GET['ajax'] == 'graph_test_analysis') {
       $graph = new EfrontGraph();
       $graph -> type = 'line';
       $graph -> max = '100';
       $graph -> min = '0';
       $graph -> fill = false;

       $count = 0;

       foreach ($parentScores as $key => $value) {
        if (isset($value['percentage'])) {
         if (isset($_GET['entity']) && $_GET['entity']) {
          if ($value['name']) {
           $graph -> meanValue[] = array($count, $showTest -> completedTest['score']);
           $graph -> data[] = array($count, $value['this_percentage']);
           $graph -> xLabels[] = array($count++, $value['name']);
          }
         } else {
          // Only the top level chapters should appear on the basic lesson test graph
          if ($value['top_level'] == 1) {
           $graph -> meanValue[] = array($count, $showTest -> completedTest['score']);
           $graph -> data[] = array($count, $value['percentage']);
           $graph -> xLabels[] = array($count++, $value['name']);
          }
         }
        }
       }
       //The lines below are used when the graph has a single value: It creates 2 additional values, in order to appear correctly (otherwise a single point appears, rather than a line)
       if (sizeof($graph -> data) == 1) {
        $graph -> meanValue = array(array(0, $graph -> meanValue[0][1]), array(1, $graph -> meanValue[0][1]), array(2, $graph -> meanValue[0][1]));
        $graph -> data = array(array(0, $graph -> data[0][1]), array(1, $graph -> data[0][1]), array(2, $graph -> data[0][1]));
        $graph -> xLabels = array(array(0, ''), array(1, $graph -> xLabels[0][1]), array(2, ''));
       }

       $graph -> xTitle = _UNIT;
       $graph -> yTitle = _SCORE;
       $graph -> label = _SCOREINUNIT;
       $graph -> meanValueLabel = _SCOREINTEST;

       echo json_encode($graph);
       exit;
      }
     } catch (Exception $e) {
      handleAjaxExceptions($e);
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

//Main scripts, such as prototype
$mainScripts = getMainScripts();

$smarty -> assign("T_HEADER_MAIN_SCRIPTS", implode(",", $mainScripts));

$smarty -> display('view_test.tpl');


?>
