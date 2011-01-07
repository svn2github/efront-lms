<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

if (!$_student_) {
    $showTest = new EfrontTest($_GET['view_unit'], true);
    if (isset($_GET['print'])) {
        $testString = $showTest -> toHTML($showTest -> toHTMLQuickForm(), false, true);
    } else {
        $testString = $showTest -> toHTML($showTest -> toHTMLQuickForm(), false);
    }

    $smarty -> assign("T_TEST", $testString);
} else {
    $test = new EfrontTest($currentUnit['id'], true);
    $status = $test -> getStatus($currentUser, $_GET['show_solved_test']);
    $form = new HTML_QuickForm("test_form", "post", basename($_SERVER['PHP_SELF']).'?view_unit='.$_GET['view_unit'], "", 'onsubmit = "$(\'submit_test\').disabled=true;"', true);
 switch ($status['status']) {
        case 'incomplete':
            if (!$testInstance = unserialize($status['completedTest']['test'])) {
                throw new EfrontTestException(_TESTCORRUPTEDASKRESETEXECUTION, EfrontTestException::CORRUPTED_TEST);
            }
            if ($testInstance -> time['pause'] && isset($_GET['resume'])) {
                $testInstance -> time['pause'] = 0;
                $testInstance -> time['resume'] = time();
                //unset($testInstance -> currentQuestion);
                $testInstance -> save();
            }
            $remainingTime = $testInstance -> options['duration'] - $testInstance -> time['spent'] - (time() - $testInstance -> time['resume']);

            $nocache = false;
            if ($form -> isSubmitted() || ($testInstance -> options['duration'] && $remainingTime < 0) || $status['status'] == 'incomplete') {
                $nocache = true;
            }
            $testString = $testInstance -> toHTMLQuickForm($form, false, false, false, $nocache);
            $testString = $testInstance -> toHTML($testString, $remainingTime);

            if ($testInstance -> options['duration'] && $remainingTime < 0) {
                $values = $form -> exportValues();
                $testInstance -> complete($values['question']);
                if ($testInstance -> completedTest['status'] == 'failed') {
                    $currentUser -> setSeenUnit($currentUnit, $currentLesson, 0);
                } else {
                    $currentUser -> setSeenUnit($currentUnit, $currentLesson, 1);
                }
                eF_redirect(basename($_SERVER['PHP_SELF'])."?view_unit=".$_GET['view_unit']);
                exit; //<-- This exit is necessary here, otherwise test might be counted twice
            }
            $smarty -> assign("T_TEST_UNDERGOING", true);
            //$testUndergoing = true;
            //pr($remainingTime);
            break;
        case 'completed':case 'passed':case 'failed':case 'pending':

            if (!$testInstance = unserialize($status['completedTest']['test'])) {
                throw new EfrontTestException(_TESTCORRUPTEDASKRESETEXECUTION, EfrontTestException::CORRUPTED_TEST);
            }

   //$url          = basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$_GET['view_unit'];
   $testString = $testInstance -> toHTMLQuickForm($form, false, true);
   $testString = $testInstance -> toHTMLSolved($testString, false);

   //Added for test redirect option
   //$currentStatus not needed because he can not jump to previous execution
   $status = $testInstance -> getStatus($currentUser -> user['login']);
   $smarty -> assign("T_TEST_STATUS", $status);

   if ($testInstance -> options['maintain_history'] === '0') {
    $testInstance -> redo($testInstance->completedTest['login']);
   }

            if (isset($_GET['test_analysis'])) {
             $loadScripts[] = 'scriptaculous/excanvas';
             $loadScripts[] = 'scriptaculous/flotr';
             $loadScripts[] = 'scriptaculous/controls';
             $loadScripts[] = 'includes/graphs';

                list($parentScores, $analysisCode) = $testInstance -> analyseTest();

                $smarty -> assign("T_CONTENT_ANALYSIS", $analysisCode);
                $smarty -> assign("T_TEST_DATA", $testInstance);

                $status = $testInstance -> getStatus($currentUser -> user['login']);
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
                      $graph -> meanValue[] = array($count, $testInstance -> completedTest['score']);
                      $graph -> data[] = array($count, $value['this_percentage']);
                      $graph -> xLabels[] = array($count++, $value['name']);
                     }
                    } else {
                     // Only the top level chapters should appear on the basic lesson test graph
                     if ($value['top_level'] == 1) {
                      $graph -> meanValue[] = array($count, $testInstance -> completedTest['score']);
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

            break;
        default:
            if (isset($_GET['confirm'])) {
                //The user specified himself the size of the test
                if ($test -> options['user_configurable']) {
                    //Get the size of the test, so that we can verify that the value specified is at most equal to it
                 $test -> getQuestions(); //This way the test's questions are populated, and we will be needing this information
                 $test -> options['random_pool'] && $test -> options['random_pool'] <= sizeof($test -> questions) ? $questionsNumber = $test -> options['random_pool'] : $questionsNumber = sizeof($test -> questions);

                 //Assigning the 'user_configurable' value to the 'random_pool' option gives us a test instance with the appropriate number of questions
                 if (is_numeric($_GET['user_configurable']) && $_GET['user_configurable'] <= $questionsNumber && $_GET['user_configurable'] > 0) {
                        $test -> options['random_pool'] = $_GET['user_configurable'];
                 } else if (!isset($_GET['user_configurable']) || !$_GET['user_configurable']) {
                     eF_redirect(basename($_SERVER['PHP_SELF'])."?view_unit=".$_GET['view_unit'].'&message='.urlencode(_MUSTSPECIFYQUESTIONNUMBER));
                     exit;
                 } else if ($_GET['user_configurable'] > $questionsNumber || $_GET['user_configurable'] <= 0) {
                     eF_redirect(basename($_SERVER['PHP_SELF'])."?view_unit=".$_GET['view_unit'].'&message='.urlencode(_MUSTSPECIFYVALUEFROM.' 1 '._TO.' '.$questionsNumber));
                     exit;
                 } else {
                     eF_redirect(basename($_SERVER['PHP_SELF'])."?view_unit=".$_GET['view_unit'].'&message='.urlencode(_INVALIDFIELDDATA));
                     exit;
                 }
                }
                $testInstance = $test -> start($currentUser -> user['login']);
                eF_redirect(basename($_SERVER['PHP_SELF'])."?view_unit=".$_GET['view_unit']);
                exit;
            } else {
                $testInstance = $test;
                $test -> getQuestions(); //This way the test's questions are populated, and we will be needing this information
                $testInstance -> options['random_pool'] && $testInstance -> options['random_pool'] <= sizeof($testInstance -> questions) ? $questionsNumber = $testInstance -> options['random_pool'] : $questionsNumber = sizeof($testInstance -> questions);
            }
            break;
    }

    if (isset($_GET['ajax']) && ($testInstance instanceOf EfrontCompletedTest)) { //in case redo() was called from maintain_history === 0
        $testInstance -> handleAjaxActions();
    }

    //Calculate total questions. If it's already set, then we are visiting an unsolved test, and the questions number is already calculated (and may be different that the $testInstance -> questions size)
    if (!isset($questionsNumber)) {
        $questionsNumber = sizeof($testInstance -> questions);
    }

    //$smarty -> assign("T_REMAINING_TIME", $remainingTime);
    $smarty -> assign("T_TEST_QUESTIONS_NUM", $questionsNumber);
    $smarty -> assign("T_TEST_DATA", $testInstance);
    $smarty -> assign("T_TEST", $testString);
    $smarty -> assign("T_TEST_STATUS", $status);

    if (!$status['status'] || ($status['status'] == 'incomplete' && $testInstance -> time['pause'])) { //If the user hasn't confirmed he wants to do the test, display confirmation buttons
  $smarty -> assign("T_SHOW_CONFIRMATION", true);
    } else { //The user confirmed he wants to do the test, so display it

        $form -> addElement('hidden', 'time_start', $timeStart); //This element holds the time the test started, so we know the remaining time even if the user left the system
        if ($currentUnit['ctg_type'] !== 'feedback') {
   $form -> addElement('submit', 'submit_test', _SUBMITTEST, 'class = "flatButton" id = "submit_test" onclick = "return checkQuestions()"');
  } else {
   $form -> addElement('submit', 'submit_test', _SUBMITFEEDBACK, 'class = "flatButton"  id = "submit_test" onclick = "return checkQuestions()"');
  }
        if ($testInstance -> options['pause_test']) {
            $form -> addElement('submit', 'pause_test', _PAUSETEST, 'class = "flatButton"');
        }

        if ($form -> isSubmitted() && $form -> validate()) {
            $values = $form -> exportValues();

            $submitValues = $form -> getSubmitValues();

            foreach($testInstance -> questions as $id => $question) {
                $submitValues['question_time'][$id] || $submitValues['question_time'][$id] === 0 ? $question -> time = $submitValues['question_time'][$id] : null;
            }

            if (isset($values['pause_test'])) {
                $testInstance -> pause($values['question'], $_POST['goto_question']);
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=content&type=tests");
            } else {
                //Set the unit as "seen"
                $testInstance -> complete($values['question']);
                if ($testInstance -> completedTest['status'] == 'failed') {
                    $currentUser -> setSeenUnit($currentUnit, $currentLesson, 0);
                } else {
                 $currentUser -> setSeenUnit($currentUnit, $currentLesson, 1);
                }
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?view_unit=".$_GET['view_unit']);
            }
        }

        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> accept($renderer);
        $smarty -> assign('T_TEST_FORM', $renderer -> toArray());
    }
}
