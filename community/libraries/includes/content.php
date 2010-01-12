<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

if (!$currentLesson) {
    eF_redirect("".basename($_SERVER['PHP_SELF']));
}
if ($configuration['math_content'] && $configuration['math_images']) {
	$loadScripts[] = 'ASCIIMath2Tex';
} elseif ($configuration['math_content']) {
	$loadScripts[] = 'ASCIIMathML';
}

$smarty -> assign("T_LESSON_NAME", $currentLesson -> lesson['name']);

if (!$currentUnit) {
    if ($_GET['type'] == 'tests') {
        $iterator = new EfrontTestsFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1))));
    } else if ($_GET['type'] == 'theory') {
        $iterator = new EfrontTheoryFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1))));
    } else if ($_GET['type'] == 'examples') {
        $iterator = new EfrontExampleFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1))));
    }

    $smarty  -> assign("T_THEORY_TREE", $currentContent -> toHTML($iterator, 'dhtmlContentTree'));
} else {
    try {
        !isset($currentUnit) ? $currentUnit = $currentContent -> getFirstNode() : null;                                               //If a unit is not specified, then consider the first content unit by default
        $visitableAndEmptyIterator = new EfrontVisitableAndEmptyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)));
        $smarty  -> assign("T_CONTENT_TREE", $currentContent -> toHTML($visitableAndEmptyIterator, 'dhtmlContentTree', array('truncateNames' => 25, 'selectedNode' => $currentUnit['id'])));

        if ($currentUnit['ctg_type'] == 'scorm' || $currentUnit['ctg_type'] == 'scorm_test') {
            $scorm_unit = true;
            $smarty -> assign("T_SCORM", $scorm_unit);
        }
        if ($currentLesson -> options['glossary']) {
            $currentUnit['data'] = eF_applyGlossary($currentUnit['data']);        //If glossary is activated, transform content data accordingly
        }
        $currentUnit['data'] = str_replace("##EFRONTINNERLINK##", $_SESSION['s_type'], $currentUnit['data']);    //Replace inner links. Inner links are created when linking from one unit to another, so they must point either to professor.php or student.php, depending on the user viewing the content

        $visitableIterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)));
        $smarty -> assign("T_UNIT",          $currentUnit);
        $smarty -> assign("T_NEXT_UNIT",     $currentContent -> getNextNode($currentUnit, $visitableIterator));
        $smarty -> assign("T_PREVIOUS_UNIT", $currentContent -> getPreviousNode($currentUnit, $visitableIterator));        //Next and previous units are needed for navigation buttons
        $smarty -> assign("T_PARENT_LIST",   $currentContent -> getNodeAncestors($currentUnit));       //Parents are needed for printing the titles
        if ($GLOBALS['configuration']['disable_comments'] != 1) {
            $smarty -> assign("T_COMMENTS",      comments :: getComments($_SESSION['s_lessons_ID'], false, $currentUnit['id']));        //Retrieve any comments regarding this unit
        }
        $smarty -> assign("T_SHOW_TOOLS",    true);                                                    //Tools is the right upper corner table box, that lists tools such as 'upload files', 'copy content' etc

        if ($currentLesson -> options['tracking'] && (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change')) {
            $userProgress = EfrontStats :: getUsersLessonStatus($currentLesson, $currentUser -> user['login']);
            $userProgress = $userProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']];
            $seenContent  = EfrontStats :: getStudentsSeenContent($currentLesson -> lesson['id'], $currentUser -> user['login']);
            $seenContent  = $seenContent[$currentLesson -> lesson['id']][$currentUser -> user['login']];

            $smarty -> assign("T_SEEN_UNIT", in_array($currentUnit['id'], array_keys($seenContent)));    //Notify smarty whether the student has seen the current unit
            $ruleCheck = $currentContent -> checkRules($currentUnit['id'], $seenContent);

            if ($ruleCheck !== true) {
                $message      = $ruleCheck;
                $message_type = false;
                $smarty -> assign("T_RULE_CHECK_FAILED", true);
            }

            $smarty -> assign("T_USER_PROGRESS", $userProgress);

            if ($currentUnit['options']['complete_question'] && !in_array($currentUnit['id'], array_keys($seenContent))) {
                $loadScripts[] = 'scriptaculous/effects';
                $lessonQuestions = $currentLesson -> getQuestions();
                if (in_array($currentUnit['options']['complete_question'], array_keys($lessonQuestions))) {
                    $question = QuestionFactory::factory($currentUnit['options']['complete_question']);
                    $smarty -> assign("T_QUESTION", $question -> toHTML(new HTML_QuickForm()));
                    if (sizeof($_POST) > 0) {
                        try {
                            //$question = QuestionFactory::factory($unitQuestions[key($_POST['question'])]);
                            $question -> setDone($_POST['question'][$question -> question['id']]);
                            $results  = $question -> correct();
                            if ($results['score'] > 0.5) {                                        //50% is considered success
                                $currentUser -> setSeenUnit($currentUnit, $currentLesson, true);
                                echo 'correct';
                            }
                        } catch (Exception $e) {
                            header("HTTP/1.0 500 ");
                            echo $e -> getMessage().' ('.$e -> getCode().')';
                        }
                        exit;
                    }
                } else {
                    //Remove non-existant question
                    $currentUnit -> options['complete_question'] = false;
                    $currentUnit -> persist();
                }
            }

            if (isset($_GET['ajax'])) {
                try {
                    $currentUser -> setSeenUnit($currentUnit, $currentLesson, $_GET['set_seen']);
                    $newUserProgress     = EfrontStats :: getUsersLessonStatus($currentLesson, $currentUser -> user['login']);
                    $newPercentage       = $newUserProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']]['overall_progress'];
                    $newConditionsPassed = $newUserProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']]['conditions_passed'];
                    $newLessonPassed     = $newUserProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']]['lesson_passed'];
                    echo json_encode(array($newPercentage, $newConditionsPassed, $newLessonPassed));
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            }

        }
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = _ERRORLOADINGCONTENT.": ".$_SESSION['s_lessons_ID'].": ".$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    }

}
?>