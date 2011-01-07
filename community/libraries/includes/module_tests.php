<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}


/**

 * @todo for pending:

 * - Update completed tests list inside tests, so it's ajax and contains only pending

 * - Remove unserializations of completed tests where unnecessary

 */
if (($GLOBALS['configuration']['disable_tests'] == 1 && $_GET['ctg'] == 'tests') || ($GLOBALS['configuration']['disable_feedback'] == 1 && $_GET['ctg'] == 'feedback')|| (isset($currentUser -> coreAccess['tests']) && $currentUser -> coreAccess['tests'] == 'hidden')) {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}
//Create shorthands for user access rights, to avoid long variable names
!isset($currentUser -> coreAccess['tests']) || $currentUser -> coreAccess['tests'] == 'change' ? $_change_ = 1 : $_change_ = 0;
$smarty -> assign("_change_", $_change_);
$loadScripts[] = 'scriptaculous/dragdrop';
$loadScripts[] = 'includes/tests';
if ($configuration['math_content'] && $configuration['math_images']) {
 $loadScripts[] = 'ASCIIMath2Tex';
} elseif ($configuration['math_content']) {
 $loadScripts[] = 'ASCIIMathML';
}
try {
 $_admin_ ? $skillgap_tests = 1 : $skillgap_tests = 0;

 //An array of legal ids for editing entries
 if (!$_admin_) {
     if (!isset($currentContent)) {
         $currentContent = new EfrontContentTree($currentLesson);
     }
     $lessonTests = $legalValues = $currentLesson -> getTestsAndFeedbacks(); //Lesson's tests
  $legalQuestions = eF_getTableDataFlat('questions', "id", 'lessons_ID='.$currentLesson -> lesson['id']);
  $legalUnits = array(); //Lesson's units
  foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
      $legalUnits[] = $key;
  }
  if (!empty($legalValues)) {
   $legalSolvedValues = eF_getTableDataFlat("completed_tests", "id", "tests_ID in (".implode(",", $legalValues).")");
   $legalSolvedValues = $legalSolvedValues['id'];
  }
 } else {
     $result = eF_getTableDataFlat("tests", "id", "lessons_ID=0");
     $legalValues = $result['id'];
     if (!empty($legalValues)) {

      $legalSolvedValues = eF_getTableDataFlat("completed_tests JOIN users_to_skillgap_tests ON completed_tests.tests_ID = users_to_skillgap_tests.tests_ID AND users_to_skillgap_tests.solved = 1", "completed_tests.id", "users_to_skillgap_tests.tests_ID in (".implode(",", $legalValues).")");
      $legalSolvedValues = $legalSolvedValues['id'];
     }
  $legalQuestions = eF_getTableDataFlat('questions', "id");
 }

 $legalQuestions = $legalQuestions['id']; //Lesson's questions
/*

	//If we asked to edit a test with its corresponding unit id, then find the equivalent test

	if (isset($_GET['edit_test']) && in_array($_GET['edit'], $legalUnits)) {

	    $currentTest = new EfrontTest($_GET['edit'], true);

	    $_GET['edit_test'] = $currentTest -> test['id'];

	}

*/
 $smarty -> assign("T_SKILLGAP_TEST", $skillgap_tests);
 //If we asked to edit the test using the Unit id, then convert to Test id and assign to $_GET['edit_test']
 if (isset($_GET['edit']) && in_array($_GET['edit'], $legalUnits)) {
     $unit = new EfrontUnit($_GET['edit']);
     if ($unit -> isTest()) {
         $test = new EfrontTest($_GET['edit'], true);
         $_GET['edit_test'] = $test -> test['id'];
     }
 }
    if (isset($_GET['delete_test']) && in_array($_GET['delete_test'], $legalValues)) {
        try {
            if (!$_change_) {
                throw new EfrontUserException(_UNAUTHORIZEDACCESS, EfrontUserException::RESTRICTED_USER_TYPE);
            }
            $currentTest = new EfrontTest($_GET['delete_test']);
            $currentTest -> delete();
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    } elseif (isset($_GET['publish_test']) && in_array($_GET['publish_test'], $legalValues)) {
        try {
            if (!$_change_) {
                throw new EfrontUserException(_UNAUTHORIZEDACCESS, EfrontUserException::RESTRICTED_USER_TYPE);
            }
            $currentTest = new EfrontTest($_GET['publish_test']);
            $currentTest -> test['publish'] == true ? $currentTest -> test['publish'] = 0 : $currentTest -> test['publish'] = 1;
            $currentTest -> persist();
            echo $currentTest -> test['publish'];
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    } elseif (isset($_GET['delete_question']) && in_array($_GET['delete_question'], $legalQuestions)) {
        try {
            if (!$_change_) {
                throw new EfrontUserException(_UNAUTHORIZEDACCESS, EfrontUserException::RESTRICTED_USER_TYPE);
            }
            $currentQuestion = QuestionFactory :: factory($_GET['delete_question']);
            $currentQuestion -> delete();
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    } elseif ((isset($_GET['show_test']) && in_array($_GET['show_test'], $legalValues)) || (isset($_GET['view_unit']) && in_array($_GET['view_unit'], $legalUnits))) {
        if (isset($_GET['view_unit'])) {
            $showTest = new EfrontTest($_GET['view_unit'], true);
            $smarty -> assign("T_UNIT", $currentUnit);
            $smarty -> assign("T_NEXT_UNIT", $currentContent -> getNextNode($currentUnit, $visitableIterator));
            $smarty -> assign("T_PREVIOUS_UNIT", $currentContent -> getPreviousNode($currentUnit, $visitableIterator)); //Next and previous units are needed for navigation buttons
            $smarty -> assign("T_PARENT_LIST", $currentContent -> getNodeAncestors($currentUnit)); //Parents are needed for printing the title
            $smarty -> assign("T_SHOW_TOOLS", true); //Tools is the right upper corner table box, that lists tools such as 'upload files', 'copy content' etc
            if ($GLOBALS['configuration']['disable_comments'] != 1) {
                $smarty -> assign("T_COMMENTS", comments :: getComments($_SESSION['s_lessons_ID'], false, $currentUnit['id'])); //Retrieve any comments regarding this unit
            }
            $smarty -> assign("T_SHOW_TOOLS", true); //Tools is the right upper corner table box, that lists tools such as 'upload files', 'copy content' etc
        } else {
            $showTest = new EfrontTest($_GET['show_test']);
        }
        $smarty -> assign ("T_CURRENT_TEST", $showTest);

        if (isset($_GET['print'])) {
            $printTest = $showTest;
            $printTest -> options['onebyone'] = 0;

            $testString = $printTest -> toHTML($printTest -> toHTMLQuickForm(new HTML_QuickForm(), false, false, false, true), false, true);//This way, even 1-1 tests are printed in a single page
        } else {
            $testString = $showTest -> toHTML($showTest -> toHTMLQuickForm(), false);
        }

        $smarty -> assign ("T_TEST_UNSOLVED", $testString);
        if (!$skillgap_tests) {
            //$smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator,       'dhtmlTargetTree', array('noclick' => true, 'drag' => false, 'selectedNode' => $currentUnit['id'])));
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1));
            if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
                $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator, 'dhtmlContentTree', array('truncateNames' => 20, 'edit' => false, 'selectedNode' => $currentUnit['id'])));
            } else {
                $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator, 'dhtmlContentTree', array('truncateNames' => 20, 'edit' => true, 'selectedNode' => $currentUnit['id'])));
            }
        }
    } elseif (isset($_GET['questions_order']) && in_array($_GET['questions_order'], $legalValues)) {
        if (!$_change_) {
            throw new EfrontUserException(_UNAUTHORIZEDACCESS, EfrontUserException::RESTRICTED_USER_TYPE);
        }
        $currentTest = new EfrontTest($_GET['questions_order']);
        $questions = $currentTest -> getQuestions();

        foreach ($questions as $key => $question) {
            $questions[$key]['text'] = strip_tags($question['text']);
        }
        $smarty -> assign("T_QUESTIONS", $questions);

        if (isset($_GET['ajax'])) {
            try {
                $order = explode(",", $_GET['order']);
                $previous = 0;
                foreach ($order as $value) {
                    $result = explode("-", $value);
                    if (in_array($value, array_keys($questions))) {
                        eF_updateTableData("tests_to_questions", array("previous_question_ID" => $previous), "tests_ID=".$currentTest -> test['id']." and questions_ID=".$result[0]);
                    }
                    $previous = $result[0];
                }
                echo _TREESAVEDSUCCESSFULLY;
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    } elseif (isset($_GET['show_question']) && in_array($_GET['show_question'], $legalQuestions)) {
        $showQuestion = QuestionFactory :: factory($_GET['show_question']);
        $smarty -> assign("T_QUESTION", $showQuestion -> question);
        $smarty -> assign ("T_QUESTION_PREVIEW", $showQuestion -> toHTML(new HTML_Quickform()));
    } elseif (isset($_GET['test_results']) && in_array($_GET['test_results'], $legalValues)) {
        $currentTest = new EfrontTest($_GET['test_results']);
        $doneTests = EfrontStats :: getDoneTestsPerTest(false, $currentTest -> test['id']);
        unset($doneTests[$currentTest -> test['id']]['average_score']);

        // Get all user names
        $result = eF_getTableData("users", "login, surname, name" , "login in ('".implode("','", array_keys($doneTests[$currentTest -> test['id']]))."')");

        // Set the table to have key their login
        $all_users = array();
        foreach ($result as $user) {
            $all_users[$user['login']] = $user;
        }

        // Get users names from their logins for each record in the doneTests table
        foreach ($doneTests[$currentTest -> test['id']] as $user => $done_test) {
            $doneTests[$currentTest -> test['id']][$user]['surname'] = $all_users[$user]['surname'];
            $doneTests[$currentTest -> test['id']][$user]['name'] = $all_users[$user]['name'];
        }

        $smarty -> assign("T_DONE_TESTS", $doneTests[$currentTest -> test['id']]);
        $smarty -> assign("T_TEST", $currentTest);

        if (isset($_GET['ajax']) && $_GET['reset_all'] == 1) {
            try {
                if (!in_array($_GET['login'], array_keys($doneTests[$currentTest -> test['id']]))) {
                    throw new EfrontTestException(_INVALIDLOGIN.': '.$_GET['login'], EfrontTestException :: INVALID_LOGIN);
                }
                $currentTest -> undo($_GET['login']);
            } catch (Exception $e) {
             handleAjaxExceptions($e);
            }
            exit;
        } else if (isset($_GET['ajax']) && $_GET['reset_all_for_all'] == 1) {
            try {
          foreach ($doneTests[$currentTest -> test['id']] as $user => $done_test) {
           $currentTest -> undo($user);
          }

            } catch (Exception $e) {
             handleAjaxExceptions($e);
            }
            exit;
        }
    } elseif (isset($_GET['show_solved_test']) && in_array($_GET['show_solved_test'], $legalSolvedValues)) {
     /***/
        require_once("tests/show_solved_test.php");
    } elseif ((isset($_GET['add_test']) && !isset($_GET['create_quick_test'])) || (isset($_GET['edit_test']) && in_array($_GET['edit_test'], $legalValues))) {
        /***/
        require_once("tests/add_test.php");
    } elseif (isset($_GET['add_question']) || (isset($_GET['edit_question']) && in_array($_GET['edit_question'], $legalQuestions))) {
        /***/
        require_once("tests/add_question.php");
    } elseif (isset($_GET['solved_tests'])) {
/*

        // Get skillgap test related information

        $tests     = eF_getTableData("tests", "*", "lessons_ID=0");



        // Get all recently completed skill gap tests

        $test_ids = array();

        foreach ($tests as $test) {

            $test_ids[] = $test['id'];

        }

        if (!empty($test_ids)) {

            $recentTests = eF_getTableData("completed_tests JOIN tests ON tests_id = tests.id JOIN users ON completed_tests.users_LOGIN = users.login JOIN users_to_skillgap_tests ON completed_tests.users_LOGIN = users_to_skillgap_tests.users_LOGIN AND users_to_skillgap_tests.tests_ID = tests.id AND users_to_skillgap_tests.solved = 1", "completed_tests.id, completed_tests.test, users.name as username, users.surname, completed_tests.tests_ID, tests.name, completed_tests.timestamp, completed_tests.users_LOGIN", "completed_tests.tests_id IN ('". implode("','", $test_ids) ."')", "timestamp DESC");



            foreach ($recentTests as $rtid => $rtest) {

                $completedRecentTest = unserialize($rtest['test']);

                $recentTests[$rtid]['score'] = $completedRecentTest -> completedTest['score'];

            }

        }



        $smarty -> assign("T_RECENT_TESTS" , $recentTests);

*/
    } else {
        if (!$skillgap_tests) {
            //Get the available questions (only questions from the selected unit, if there is one)
            try {
                isset($_GET['from_unit']) && eF_checkParameter($_GET['from_unit'], 'id') ? $selectedUnit = $_GET['from_unit'] : $selectedUnit = 0;
                $siblings = $currentContent -> getNodeChildren($selectedUnit);
                $children[] = $siblings['id'];
                foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($siblings), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
                    $children[] = $key;
                }
                if (sizeof($children) > 0) {
                    $questions = eF_getTableData("questions", "*", "content_ID in (".implode(",", $children).") and lessons_ID=".$currentLesson -> lesson['id'], "content_ID ASC"); //Retrieve all questions that belong to this unit or its subunits
                } else {
                    throw new Exception();//This jumps to the catch block right below
                }
            } catch (Exception $e) {
                $questions = eF_getTableData("questions", "*", "lessons_ID = ".$currentLesson -> lesson['id'], "content_ID ASC"); //Retrieve all questions that belong to this lesson
            }
            //Assign the content units so that we can build the units select box for the "from_unit" option
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)); //Default iterator excludes non-active units
            $contentUnits = $currentContent -> toHTMLSelectOptions($iterator);
            $smarty -> assign("T_UNITS", $contentUnits);
            //Fix questions if their corresponding content is missing
            $contentUnits = array_keys($contentUnits);
            foreach ($questions as $key => $value) {
                $names = array();
                if (!in_array($value['content_ID'], $contentUnits)) {
                    $question = QuestionFactory :: factory($value);
                    $question -> question['content_ID'] = 0;
                    $question -> persist();
                }
            }
            $selectedUnit ? $units = $currentContent -> getNodeChildren($selectedUnit) : $units = $currentContent -> tree;
            if (sizeof($units) > 0) {
             foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($units)), array('id', 'name')) as $key => $value) {
                 $key == 'id' ? $ids[] = $value : $names[] = $value;
             }
             $tests = eF_getTableData("content c,tests t", "c.id as content_ID, c.name, t.id, t.active, t.publish, t.mastery_score, t.description, t.options", "ctg_type='".$_GET['ctg']."' AND c.id IN (".implode(",", $ids).") AND c.active=1 and c.id=t.content_ID", "c.id ASC");
            }
            $result = eF_getTableData("tests_to_questions", "tests_ID, count(*)", "", "", "tests_ID");
            foreach ($result as $value) {
                $testQuestions[$value['tests_ID']] = $value['count(*)'];
            }
            foreach ($tests as $key => $test) {
                $names = array();
                foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> getNodeAncestors($test['content_ID']))), array('name')) as $k => $v) {
                    $names[] = $v;
                }
                $tests[$key]['parent_unit'] = implode("&nbsp;&raquo;&nbsp;", array_reverse(array_slice($names, 1)));
                $tests[$key]['questions_num'] = $testQuestions[$test['id']] ? $testQuestions[$test['id']] : 0;
            }
        } else {
            $questions = eF_getTableData("questions LEFT OUTER JOIN lessons ON lessons.id = lessons_ID", "questions.*, lessons.name", "type <> 'raw_text'", ""); //Retrieve all questions that belong to this unit or its subunits
            // If no lesson then define the current lesson name => _SKILLGAPTESTS (used for correct filtering)
            foreach ($questions as $qid => $question) {
                if ($question['lessons_ID'] == 0) {
                    $questions[$qid]['name'] = _SKILLGAPTESTS;
                } else {
                    $questions[$qid]['name'] = _LESSON . ': "' . $question['name'] . '"';
                }
            }
            // The test name requirement is to help avoid problems with databases where tests wiht lessons_ID=0 somehow exist.
            // Skillgap tests have mandatory name so the condition is correct
            $tests = eF_getTableData("tests LEFT OUTER JOIN tests_to_questions ON tests.id = tests_to_questions.tests_ID", "tests.*, count(questions_ID) as questions_num", "lessons_ID=0 AND tests.name <> '' GROUP BY tests.id");
            //$smarty -> assign("T_RECENTLY_SKILLGAP_OPTIONS", array(array('text' => _SHOWALLSOLVEDSKILLGAPTESTS,   'image' => "16x16/search.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=tests&solved_tests=1")));
        }
        $testIds = array();
        foreach ($tests as $key => $test) {
            $testIds[] = $test['id'];
//@todo: change this call
            $doneTests = EfrontStats :: getDoneTestsPerTest(false, $test['id']);
            $tests[$key]['average_score'] = $doneTests[$test['id']]['average_score'];
            $tests[$key]['options'] = unserialize($test['options']);
            if ($tests[$key]['options']['random_pool'] > 0) {
                if ($tests[$key]['questions_num'] > $tests[$key]['options']['random_pool']) {
                    $tests[$key]['questions_num'] = $tests[$key]['options']['random_pool'];
                }
            }
            // If somehow the general threshold value is not set
            if (!isset($tests[$key]['options']['general_threshold'])) {
                $tests[$key]['options']['general_threshold'] = 50;
                $newOptions = serialize($tests[$key]['options']);
                eF_updateTableData("tests", array("options" => $newOptions), "id = '".$test['id']."'");
            }
        }
        $smarty -> assign("T_QUESTIONTYPESTRANSLATIONS", Question :: $questionTypes);
        $smarty -> assign("T_TESTS", $tests);
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'questionsTable') {
         isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
            if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                $sort = $_GET['sort'];
                isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
            } else {
                $sort = 'text';
            }
            foreach ($questions as $key => $question) {
                $names = array();
                if ($question['content_ID'] && isset($currentContent)) {
                    if (!isset($names[$question['content_ID']])) {
                        foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> getNodeAncestors($question['content_ID']))), array('name')) as $k => $v) {
                            $names[$question['content_ID']][] = $v;
                        }
                    }
                    $questions[$key]['parent_unit'] = implode("&nbsp;&raquo;&nbsp;", array_reverse($names[$question['content_ID']]));
                } else {
                    $questions[$key]['parent_unit'] = "";
                }
                $questions[$key]['text'] = strip_tags($question['text']); //Strip tags from the question text, so they do not display in the list
                $questions[$key]['estimate_interval'] = eF_convertIntervalToTime($questions[$key]['estimate']);
            }
   //remove questions from inactive and archived lessons
   if ($skillgap_tests) {
     $questionsTemp = array();
        //remove inactive and archived lessons
        $result = eF_getTableDataFlat("lessons","id","active=0 OR archive!=''");
        if (!empty($result['id'])) {
         foreach($questions as $key => $value) {
          if (in_array($value['lessons_ID'],$result['id']) === false) {
           $questionsTemp[] = $questions[$key];
          }
         }
        }
      $questions = $questionsTemp;
   }
            $questions = eF_multiSort($questions, $sort, $order);
            if (isset($_GET['filter'])) {
                $questions = eF_filterData($questions, $_GET['filter']);
            }
            $smarty -> assign("T_QUESTIONS_SIZE", sizeof($questions));
            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $questions = array_slice($questions, $offset, $limit, true);
            }
            $smarty -> assign('T_QUESTIONS', $questions);
            !$skillgap_tests ? $smarty -> display('professor.tpl') : $smarty -> display('administrator.tpl');
            exit;
        }
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'pendingTable') {
            if (!empty($testIds)) {
                if (!$skillgap_tests) {
                    $recentTests = eF_getTableData("completed_tests ct, tests t, users u, users_to_lessons ul", "t.name, u.name as username, u.surname, ct.id, ct.status, ct.tests_ID, ct.score, ct.time_end, ct.users_LOGIN, ct.pending", "u.login=ul.users_login and ul.archive=0 and ul.lessons_ID=t.lessons_ID and ct.status != 'deleted' and ct.status != 'incomplete' and t.id = ct.tests_ID AND ct.users_login = u.login AND u.archive=0 and ct.tests_id IN ('". implode("','", $testIds) ."')", "ct.pending DESC");
                } else {
                    $recentTests = eF_getTableData("completed_tests JOIN tests ON tests_id = tests.id JOIN users ON completed_tests.users_LOGIN = users.login JOIN users_to_skillgap_tests ON completed_tests.users_LOGIN = users_to_skillgap_tests.users_LOGIN AND users_to_skillgap_tests.tests_ID = tests.id AND users_to_skillgap_tests.solved = 1", "completed_tests.id, completed_tests.test, completed_tests.score, users.name as username, users.surname, completed_tests.tests_ID, tests.name, completed_tests.timestamp, completed_tests.users_LOGIN", "completed_tests.status != 'deleted' and completed_tests.tests_id IN ('". implode("','", $testIds) ."')", "timestamp DESC");
                }
            }
            isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
            if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                $sort = $_GET['sort'];
                isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
            } else {
                $sort = 'text';
            }
            $recentTests = eF_multiSort($recentTests, $sort, $order);

            if (isset($_GET['filter'])) {
                $recentTests = eF_filterData($recentTests, $_GET['filter']);
            }

            $smarty -> assign("T_PENDING_SIZE", sizeof($recentTests));
            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $recentTests = array_slice($recentTests, $offset, $limit, true);
            }

            $smarty -> assign("T_PENDING_TESTS" , $recentTests);
            !$skillgap_tests ? $smarty -> display('professor.tpl') : $smarty -> display('administrator.tpl');
            exit;
        }
    }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}




if (true) {


try {
    if ($_student_) {
        $seenContent = EfrontStats :: getStudentsSeenContent($currentLesson -> lesson['id'], $currentUser -> user['login']);
        $seenContent = $seenContent[$currentLesson -> lesson['id']][$currentUser -> user['login']];
        if ($currentLesson -> options['rules']) {
            $ruleCheck = $currentContent -> checkRules($currentUnit['id'], $seenContent);
        }
        if (isset($_GET['view_unit']) && eF_checkParameter($_GET['view_unit'], 'id') && (!($GLOBALS['currentLesson'] -> options['rules']) || $ruleCheck === true)) {
            $visitableIterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)));
            $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML(false, 'dhtmlContentTree', array('truncateNames' => 25, 'selectedNode' => $currentUnit['id'])));
            $smarty -> assign("T_UNIT", $currentUnit);
            $smarty -> assign("T_NEXT_UNIT", $currentContent -> getNextNode($currentUnit, $visitableIterator));
            $smarty -> assign("T_PREVIOUS_UNIT", $currentContent -> getPreviousNode($currentUnit, $visitableIterator)); //Next and previous units are needed for navigation buttons
            $smarty -> assign("T_PARENT_LIST", $currentContent -> getNodeAncestors($currentUnit)); //Parents are needed for printing the titles

            $test = new EfrontTest($currentUnit['id'], true);
            $status = $test -> getStatus($currentUser, $_GET['show_solved_test']);
            $form = new HTML_QuickForm("test_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=tests&view_unit='.$_GET['view_unit'], "", null, true);
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
                        $currentUser -> setSeenUnit($currentUnit, $currentLesson, 1);
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=tests&view_unit=".$_GET['view_unit']);
                        exit; //<-- This exit is necessary here, otherwise test might be counted twice
                    }

                    //pr($remainingTime);
                    break;
                case 'completed':case 'passed':case 'failed':case 'pending':
                    if (!$testInstance = unserialize($status['completedTest']['test'])) {
                        throw new EfrontTestException(_TESTCORRUPTEDASKRESETEXECUTION, EfrontTestException::CORRUPTED_TEST);
                    }

                    //$url          = basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$_GET['view_unit'];
                    $testString = $testInstance -> toHTMLQuickForm($form, false, true);
                    $testString = $testInstance -> toHTMLSolved($testString, false);

                    if (isset($_GET['test_analysis'])) {
                     $loadScripts[] = 'scriptaculous/excanvas';
                     $loadScripts[] = 'scriptaculous/flotr';
                     $loadScripts[] = 'scriptaculous/controls';
                     $loadScripts[] = 'includes/graphs';

                     list($parentScores, $analysisCode) = $completedTest -> analyseTest();

                     $smarty -> assign("T_CONTENT_ANALYSIS", $analysisCode);
                     $smarty -> assign("T_TEST_DATA", $completedTest);

                     $status = $completedTest -> getStatus($result[0]['users_LOGIN']);
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
                           $graph -> meanValue[] = array($count, $completedTest -> completedTest['score']);
                           $graph -> data[] = array($count, $value['this_percentage']);
                           $graph -> xLabels[] = array($count++, $value['name']);
                          }
                         } else {
                          // Only the top level chapters should appear on the basic lesson test graph
                          if ($value['top_level'] == 1) {
                           $graph -> meanValue[] = array($count, $completedTest -> completedTest['score']);
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
                        $testInstance = $test -> start($currentUser -> user['login']);
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=tests&view_unit=".$_GET['view_unit']);
                        exit;
                    } else {
                        $testInstance = $test;
                        $test -> getQuestions(); //This way the test's questions are populated, and we will be needing this information
                        $testInstance -> options['random_pool'] && $testInstance -> options['random_pool'] >= sizeof($testIn) ? $questionsNumber = $testInstance -> options['random_pool'] : $questionsNumber = sizeof($testInstance -> questions);
                    }
                    break;
            }

            if (isset($_GET['ajax'])) {
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
                if ($testInstance -> options['answer_all'] != 1) {
     $form -> addElement('submit', 'submit_test', _SUBMITTEST, 'class = "flatButton" onclick = "if (typeof(checkedQuestions) != \'undefined\' && (unfinished = checkQuestions())) return confirm(\''._YOUHAVENOTCOMPLETEDTHEFOLLOWINGQUESTIONS.': \'+unfinished+\'. '._AREYOUSUREYOUWANTTOSUBMITTEST.'\');"');
                } else {
     $form -> addElement('submit', 'submit_test', _SUBMITTEST, 'class = "flatButton" onclick = "if (typeof(checkedQuestions) != \'undefined\' && (unfinished = checkQuestions())) {alert(\''._YOUHAVENOTCOMPLETEDTHEFOLLOWINGQUESTIONS.': \'+unfinished+\'. '._YOUHAVETOANSWERALLQUESTIONS.'\');return false;}"');
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
                        $currentUser -> setSeenUnit($currentUnit, $currentLesson, 1);
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=tests&view_unit=".$_GET['view_unit']);
                    }
                }

                $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
                $form -> accept($renderer);
                $smarty -> assign('T_TEST_FORM', $renderer -> toArray());
            }

        } else { //The user sees the list of tests
            $visitableIterator = new EfrontTestsFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST))));

            $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator, 'dhtmlContentTree', array('truncateNames' => 25, 'selectedNode' => $currentUnit['id'])));
            $smarty -> assign("T_UNIT", $currentUnit);
            $smarty -> assign("T_NEXT_UNIT", $currentContent -> getNextNode($currentUnit, $visitableIterator));
            $smarty -> assign("T_PREVIOUS_UNIT", $currentContent -> getPreviousNode($currentUnit, $visitableIterator)); //Next and previous units are needed for navigation buttons
            $smarty -> assign("T_PARENT_LIST", $currentContent -> getNodeAncestors($currentUnit)); //Parents are needed for printing the titles
            $smarty -> assign("T_NO_TEST", true);
            if ($ruleCheck !== true) {
                $message = $ruleCheck;
                $message_type = false;
                $smarty -> assign("T_RULE_CHECK_FAILED", true);
            }
        }
    } else {


        // Basic check to distinguish between skillgap and normal lesson tests

        // Delete all questions from the posted form
        if (isset($_POST['selected_action']) && $_POST['selected_action'] == 'delete') { //Mass deletion of questions
            if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            }
            if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            }

            foreach ($_POST['questions'] as $key => $value) {
                try {
                    $question = QuestionFactory :: factory($value);
                    $question -> delete();
                } catch (Exception $e) {
                    $messageString .= $e -> getMessage();
                }
            }

            $message = _OPERATIONSUCCESFULLYAPPLIEDON.' '.sizeof($_POST['questions']).' '._QUESTIONS;
            $message_type = 'success';

            if (isset($messageString)) {
                $message = implode("<br/>", $messageString);
                $message_type = 'failure';
            }
        }

        // Optionally ajaxed request - if not ajaxed then it should show the tests list
        if( isset($_GET['delete_solved_test']) && eF_checkParameter($_GET['delete_solved_test'], 'id')) {
            if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                exit;
            }
            try {
            //eF_deleteTableData("completed_tests", "id = " . $_GET['delete_solved_test']);
             $currentTest = new EfrontTest($_GET['test_id']);

             $currentTest -> undo($_GET['users_login'], $_GET['delete_solved_test']);
             if ($skillgap_tests) {
                 // Remove a solved test from the users_to_skillgap list
                 eF_updateTableData("users_to_skillgap_tests" , array("solved" => 0), "tests_id = " . $_GET['test_id']. " AND users_login = '".$_GET['users_login']."'");
             }
            } catch (Exception $e) {
             if ($_GET['postAjaxRequest']) {
              header("HTTP/1.0 500 ");
              echo $e -> getMessage().' ('.$e -> getCode().')';
             } else {
                 throw ($e);
             }

            }
            if ($_GET['postAjaxRequest']) {
                exit;
            }

            $message = _SKILLGAPTESTRESULTSREMOVEDFROMUSERTHETESTCANBEREPEATED;
            $message_type = 'success';


        }

        //Get the list of valid tests for the current lesson.
        if (isset($currentContent)) {
            $result = eF_getTableData("tests t, content c", "t.*", "t.content_ID=c.id and c.lessons_ID=".$currentLesson -> lesson['id']);
            foreach ($result as $value) {
                $allTests[$value['content_ID']] = $value;
            }
            $testsIterator = new EfrontTestsFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)));
            foreach ($testsIterator as $key => $value) {
                if ($value['ctg_type'] == 'tests') {
                    $availableTests[$key] = $allTests[$key]['id'];
                }
            }
            $smarty -> assign("T_SET_CONTENT", true);
        } else {
            // Get skillgap tests
            $result = eF_getTableData("tests", "*", "lessons_ID = 0 AND name <> ''");

            foreach ($result as $value) {
                $availableTests[] = $value['id'];
            }
            $smarty -> assign("T_SET_CONTENT", false);
        }

        if ($skillgap_tests && isset($_GET['create_random_test']) && isset($_GET['create_random_test']) && eF_checkParameter($_GET['create_random_test'], 'id') && in_array($_GET['create_random_test'], $availableTests)) {
            if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                exit;
            }

            if (isset($_GET['from_skills'])) {
                //11111111111
                $skills = eF_getTableData("module_hcd_skills", "skill_ID, description", "");
                $smarty -> assign('T_QUESTION_SKILLS', $skills);
            }
        } elseif ($skillgap_tests && isset($_GET['add_test']) && isset($_GET['create_quick_test'])) {
            // Quick test generator code
            if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                exit;
            }

            $form = new HTML_QuickForm("question_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=tests&add_test=1&create_quick_test=1", "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter

            $form -> addElement('text', 'name', null, 'class = "inputText" id= "testName"');
            //        $form -> addRule('name', _THEFIELD.' "'._NAME.'" '._ISMANDATORY, 'required', null, 'client');
            //        $form -> addRule('name', _INVALIDFIELDDATAFORFIELD.' "'._NAME.'"', 'checkParameter', 'text');

            //$form -> addElement('text', 'total_questions', null, 'class = "inputText"');

            //$form->registerRule('onlydigits','regex','/^\d*/');
            //$form->addRule('total_questions',_INVALIDFIELDDATAFORFIELD.' "'._TOTALQUESTIONS,'onlydigits');
            //$form -> addRule('total_questions', _INVALIDFIELDDATAFORFIELD.' "'._TOTALQUESTIONS, 'callback', create_function('$a', 'return ($a > 0);'));


            // Creating select for directions-courses-lessons
            $directionsTree = new EfrontDirectionsTree();
            $selectArray = $directionsTree ->toSelect(true,true, true); //return in HTML coloured format with SKILLGAPTESTS option and including questions number
            $smarty -> assign("T_QUICKFORM_LESSON_COURSES_SELECT" , $selectArray);
//echo "A";exit;
            //$form -> addElement('select', 'lesson_courses_row' , null, $selectArray ,'id="lesson_courses_row"');
            //$form -> addElement('select', 'system_avatar' , _ORSELECTONEFROMLIST, $systemAvatars, "id = 'select_avatar'");
            //$start = strpos($selectArray,"(") + 1;
            $end = strpos($selectArray,")");
            $ignore_first_par = substr($selectArray,$end+1);
            $start = strpos($ignore_first_par, "(") + 1;
            $end = strpos($ignore_first_par, ")");
            $init_lesson_questions_max = (integer)substr($ignore_first_par, $start, $end) ;
            $lesson_questions = array();
            for ($i =1 ; $i <= $init_lesson_questions_max; $i++) {
                $lesson_questions[$i] = $i;
            }
            $form -> addElement('select', 'educational_questions_count_row', null, $lesson_questions,'class = "inputText" id="educational_questions_row"');
            $form -> addElement('advcheckbox', 'assign_to_new', null, null, null, array(0, 1));
            $form -> addElement('advcheckbox', 'automatic_assignment', null, null, null, array(0, 1));
            $form -> addElement('textarea', 'description', null, 'class = "inputTestTextarea" style = "width:100%;height:6em;"');
            $form -> addElement('submit', 'submit_test', _CREATETEST, 'class = "flatButton"');
            if ($form -> isSubmitted()) {
                //pr($_POST);
                $lessons_to_cover = array();
                $courses_to_cover = array();
                $directions_to_cover = array();
                // Read posted educational criteria
                foreach ($_POST as $postKey => $postValue) {
                    if (strpos($postKey, "educational_criteria") === 0) {
                        $row = substr(strrchr($postKey,"_"),1); // the id is educational_criteria_row (row = 1...N)
                        $key = substr(strrchr($postValue, "_"), 1) ; // we do not want the "_" itself
                        if (strpos($postValue, "lesson") === 0) {
                            // array in the form $lesson[62] = array(id=>62, questions (asked for) =>5, total_questions (for this lesson)=>20);
                            $lessons_to_cover[$key] = array("id" => $key, "questions_asked" => $_POST['educational_questions_count_' . $row], "total_questions" => 0, "questions" => array());
                            // the total_questions and questions fields of the array will be completed correctly in step 2
                        } else if (strpos($postValue, "course") === 0) {
                            $courses_to_cover[$key] = array("id" => $key, "questions_asked" => $_POST['educational_questions_count_' . $row]);
                        } else {
                            $directions_to_cover[$key] = array("id" => $key, "questions_asked" => $_POST['educational_questions_count_' . $row]);
                        }
                    } else if (strpos($postKey, "skills_criteria") === 0) {
                        $row = substr(strrchr($postKey,"_"),1); // the id is educational_criteria_row (row = 1...N)
                        $key = substr(strrchr($postValue, "_"), 1) ; // we do not want the "_" itself
                        if (strpos($postValue, "category") === 0) {
                            // array in the form $lesson[62] = array(id=>62, questions (asked for) =>5, total_questions (for this lesson)=>20);
                            $skill_categories_to_cover[$key] = array("id" => $key, "questions_asked" => $_POST['skill_questions_count_' . $row], "total_questions" => 0, "questions" => array());
                            // the total_questions and questions fields of the array will be completed correctly in step 2
                        } else {
                            $skills_to_cover[$key] = array("id" => $key, "questions_asked" => $_POST['skill_questions_count_' . $row], "total_questions" => 0, "questions" => array());
                        }
                    }
                }
                // Three steps algorithm:
                // 1) get all lessons involved (from directions-> lessons, from directions->courses->lessons
                //    and from courses->lessons) posted. If no lessons posted then use all system lessons
                // 2) get the count for the questions of each lesson
                // 3) create a random assignment of questions according to posted values and availability
                /********** STEP 1 **********/
                // Get all lessons array for the courses and directions involved
                foreach ($directions_to_cover as $directionId => $directionInfo) {
                    $direction = new EfrontDirection($directionId);
                    // Get all direction lessons
                    $directionLessons = $direction -> getLessons();
                    $directions_to_cover[$directionId]['lessons'] = array();
                    $directions_to_cover[$directionId]['lessonsCount'] = sizeof($directionLessons); // used with rand()
                    foreach ($directionLessons as $lessonId => $lesson) {
                        $directions_to_cover[$directionId]['lessons'][] = $lessonId;
                        if (!isset($lessons_to_cover[$lessonId])) {
                            $lessons_to_cover[$lessonId] = array("id" => $lessonId, "questions_asked" => 0, "total_questions" => 0, "questions" => array());
                        }
                    }
                    // Get all direction courses lessons
                    $directionCourses = $direction -> getCourses();
                    foreach ($directionCourses as $courseId => $courseInfo) {
                        $course = new EfrontCourse($courseId);
                        $courseLessons = EfrontCourse::convertLessonObjectsToArrays($course -> getCourseLessons());
                        // Direction lessons and direction courses lessons overlapping is ommitted here
                        // but will be correctly handled by the random questions assignment
                        $directions_to_cover[$directionId]['lessonsCount'] += sizeof($courseLessons);
                        foreach ($courseLessons as $lessonId => $lesson) {
                            $directions_to_cover[$directionId]['lessons'][] = $lessonId;
                            if (!isset($lessons_to_cover[$lessonId])) {
                                $lessons_to_cover[$lessonId] = array("id" => $lessonId, "questions_asked" => 0, "total_questions" => 0, "questions" => array());
                            }
                        }
                    }
                }
                // Get all lessons array for the courses and directions involved
                foreach ($courses_to_cover as $courseId => $courseInfo) {
                    $course = new EfrontCourse($courseId);
                    $courseLessons = EfrontCourse::convertLessonObjectsToArrays($course -> getCourseLessons());
                    // The copying into the $courses_to_cover[$courseId]['lessons'] array is made to facilitate random selection
                    $courses_to_cover[$courseId]['lessons'] = array();
                    $courses_to_cover[$courseId]['lessonsCount'] = sizeof($courseLessons);
                    foreach ($courseLessons as $lessonId => $lesson) {
                        $courses_to_cover[$courseId]['lessons'][] = $lessonId;
                        if (!isset($lessons_to_cover[$lessonId])) {
                            $lessons_to_cover[$lessonId] = array("id" => $lessonId, "questions_asked" => 0, "total_questions" => 0, "questions" => array());
                        }
                    }
                }
                // End of step 1: we have created the lessons_to_cover array with all lessons that will be implicated
                /********* STEP 2: Get questions for each implicated lesson ***********/
                $all_implicated_questions = eF_getTableData("questions", "id, lessons_ID", "lessons_ID in ('".implode("','", array_keys($lessons_to_cover)) ."') AND type <> 'raw_text'");
                foreach ($all_implicated_questions as $question) {
                    $lessons_to_cover[$question['lessons_ID']]['total_questions']++;
                    $lessons_to_cover[$question['lessons_ID']]['questions'][] = $question['id'];
                }
                /* Find out which lesson has questions

                 foreach ($lessons_to_cover as $lesson) {

                 if ($lesson['total_questions']>0){

                 echo $lesson['id']."<BR>";

                 }

                 }

                 */
                function getRandomLessonQuestion($lessonId) {
                    global $lessons_to_cover;
                    global $questions_to_assign;
                    $selected_question = rand() % $lessons_to_cover[$lessonId]['total_questions'];
                    $questions_id = $lessons_to_cover[$lessonId]['questions'][$selected_question];
                    $questions_to_assign[$questions_id] = $questions_id;
                    // We need to maintain the questions list ordered from 0...total_questions -1 to facilitate random requests
                    // Therefore we swap the found question with the last one
                    $lessons_to_cover[$lessonId]['questions'][$selected_question] = $lessons_to_cover[$lessonId]['questions'][$lessons_to_cover[$lessonId]['total_questions']-1];
                    $lessons_to_cover[$lessonId]['total_questions']--;
                }
                /********* STEP 3: Assign questions to the test according to the user requests ***********/
                // Bottom-up approach: 1. lessons, 2. courses->lessons, 3. directions->(courses->lessons)|lessons
                $questions_to_assign = array();
                // 1. First assign questions that were directly asked for each lesson
                foreach ($lessons_to_cover as $lessonId => $lesson) {
                    // If more questions asked than existing, get all of them
                    if ($lessons_to_cover[$lessonId]['questions_asked'] >= $lessons_to_cover[$lessonId]['total_questions']) {
                        foreach($lessons_to_cover[$lessonId]['questions'] as $question) {
                            $questions_to_assign[$question] = $question;
                        }
                        $lessons_to_cover[$lessonId]['total_questions'] = 0; // let the top levels know that no questions are left for this lesson
                    } else {
                        // Random assignment of the questions which we know (since we are at the else section) that can cover the requested needs
                        while ($lessons_to_cover[$lessonId]['questions_asked']) {
                            getRandomLessonQuestion($lessonId);
                            $lessons_to_cover[$lessonId]['questions_asked']--;
                        }
                    }
                }
                // 2. Then get for course lessons in random
                foreach ($courses_to_cover as $courseId => $course) {
                    // Find all questions remaining in all course implicated lessons
                    $total_remaining = 0;
                    foreach ($course['lessons'] as $lesson) {
                        $total_remaining += $lessons_to_cover[$lesson]['total_questions'];
                    }
                    // If more asked than the available get them all and finish
                    if ($course['questions_asked'] > $total_remaining) {
                        foreach ($course['lessons'] as $lesson) {
                            foreach($lessons_to_cover[$lesson]['questions'] as $question) {
                                $questions_to_assign[$question] = $question;
                            }
                            $lessons_to_cover[$lesson]['total_questions'] = 0; // let the other levels know that no questions are left for this lesson
                        }
                    } else {
                        // Random assignment
                        while ($courses_to_cover[$courseId]['questions_asked']) {
                            $selected_lesson = rand() % $courses_to_cover[$courseId]['lessonsCount'];
                            $lessonId = $courses_to_cover[$courseId]['lessons'][$selected_lesson];
                            if ($lessons_to_cover[$lessonId]['total_questions']) {
                                getRandomLessonQuestion($lessonId);
                                $courses_to_cover[$courseId]['questions_asked']--;
                            } else {
                                // Remove that lesson from the list by swapping it with the last lesson - no questions left to dig
                                $courses_to_cover[$courseId]['lessons'][$selected_lesson] = $courses_to_cover[$courseId]['lessons'][$courses_to_cover[$courseId]['lessonsCount']-1];
                                $courses_to_cover[$courseId]['lessonsCount']--;
                                //Do not decrease the questions - none was assigned
                            }
                        }
                    }
                }
                // 3. Then get for direction courses and lessons in random
                foreach ($directions_to_cover as $directionId => $direction) {
                    // Find all questions remaining in all direction implicated lessons
                    $total_remaining = 0;
                    foreach ($direction['lessons'] as $lesson) {
                        $total_remaining += $lessons_to_cover[$lesson]['total_questions'];
                    }
                    // If more asked than the available get them all and finish
                    if ($direction['questions_asked'] > $total_remaining) {
                        foreach ($direction['lessons'] as $lesson) {
                            foreach($lessons_to_cover[$lesson]['questions'] as $question) {
                                $questions_to_assign[$question] = $question;
                            }
                        }
                    } else {
                        // Random assignment
                        while ($directions_to_cover[$directionId]['questions_asked']) {
                            $selected_lesson = rand() % $directions_to_cover[$directionId]['lessonsCount'];
                            $lessonId = $directions_to_cover[$directionId]['lessons'][$selected_lesson];
                            if ($lessons_to_cover[$lessonId]['total_questions']) {
                                getRandomLessonQuestion($lessonId);
                                $directions_to_cover[$directionId]['questions_asked']--;
                            } else {
                                // Remove that lesson from the list by swapping it with the last lesson - no questions left to dig
                                $directions_to_cover[$directionId]['lessons'][$selected_lesson] = $directions_to_cover[$directionId]['lessons'][$directions_to_cover[$directionId]['lessonsCount']-1];
                                $directions_to_cover[$directionId]['lessonsCount']--;
                                // Do not decrease the questions - none was assigned
                            }
                        }
                    }
                }
                // After finding out the questions to use - insert finally the test
                $testOptions = array('redoable' => 0,
                                         'onebyone' => 1,
                                         'given_answers' => 0,
                                         'answers' => 0,
                                         'shuffle_questions' => 0,
                                         'shuffle_answers' => 0,
                                         'random_pool' => 0,
                                         'pause_test' => 0,
                                         'display_list' => 0,
                                         'display_weights' => 0,
                          'general_threshold' => "50.00",
                                         'assign_to_new' => 0,
                             'automatic_assignment' => 0);
                $testFields = array('active' => 1,
                                        'lessons_ID' => 0,
                                        'content_ID' => $test_content_ID,
                                        'description' => $form -> exportValue('description'),
                                        'options' => serialize($testOptions),
                                        'name' => $form -> exportValue('name'),
                                        'publish' => 0,
                                        'mastery_score' => 0);
                try {
                    $newTest = EfrontTest :: createTest(false, $testFields);
                    //                        pr($questions_to_assign);
                    foreach($questions_to_assign as $id => $quest) {
                        $questions_to_assign[$id] = 1;
                    }
                    $newTest ->addQuestions($questions_to_assign);
                    if (isset($_GET['redirect_to_edit'])) {
                        eF_redirect("".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&edit_test=".$newTest ->test['id']."&message=".urlencode(_SUCCESFULLYADDEDTEST)."&message_type=success");
                    } else {
                        eF_redirect("".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&message=".urlencode(_SUCCESFULLYADDEDTEST)."&message_type=success");
                    }
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
                // The list according to the
                //pr($questions_to_assign);
                /*

                 $all_implicated_questions = eF_getTableData("questions", "id, lessons_ID", "id in ('".implode("','", $questions_to_assign) ."')");

                 pr($all_implicated_questions);

                 pr(array_keys($lessons_to_cover));

                 */
                //pr($lessons_to_cover);
                //pr($cirections_to_cover);
                //pr($directions_to_cover);
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
            $form -> setRequiredNote(_REQUIREDNOTE);
            $form -> accept($renderer);
            $smarty -> assign('T_QUICKTEST_FORM', $renderer -> toArray());
        } else {
            try {
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        }
    }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
}
