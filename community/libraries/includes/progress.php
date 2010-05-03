<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

if (isset($currentUser -> coreAccess['progress']) && $currentUser -> coreAccess['progress'] == 'hidden') {
    eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");exit;
}

if ($_student_) {
    $_GET['edit_user'] = $currentUser -> user['login'];
    $currentUser -> coreAccess['progress'] = 'view';
}

$load_editor = true;
if (isset($_GET['edit_user']) && eF_checkParameter($_GET['edit_user'], 'login')) {
    //$lessonUser  = EfrontUserFactory :: factory($_GET['edit_user']);

    //Check conditions
    $currentContent = new EfrontContentTree($currentLesson);
    $seenContent = EfrontStats :: getStudentsSeenContent($currentLesson -> lesson['id'], $_GET['edit_user']);
    $conditions = $currentLesson -> getConditions();
    foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST))) as $key => $value) {
        $visitableContentIds[$key] = $key; //Get the not-test unit ids for this content
    }
    foreach ($iterator = new EfrontTestsFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)))) as $key => $value) {
        $testsIds[$key] = $key; //Get the not-test unit ids for this content
    }

    list($conditionsStatus, $lessonPassed) = EfrontStats :: checkConditions($seenContent[$currentLesson -> lesson['id']][$_GET['edit_user']], $conditions, $visitableContentIds, $testsIds);
    $smarty -> assign("T_CONDITIONS", $conditions);
    $smarty -> assign("T_CONDITIONS_STATUS", $conditionsStatus);
    foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree)), array('id', 'name')) as $key => $value) {
        $key == 'id' ? $ids[] = $value : $names[] = $value;
    }
    $smarty -> assign("T_TREE_NAMES", array_combine($ids, $names));

    $form = new HTML_QuickForm("edit_user_complete_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=progress&edit_user='.$_GET['edit_user'], "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter

    $form -> addElement('advcheckbox', 'completed', _COMPLETED, null, 'class = "inputCheckbox"'); //Whether the user has completed the lesson
    $form -> addElement('text', 'score', _SCORE, 'class = "inputText"'); //The user lesson score
    $form -> addRule('score', _THEFIELD.' "'._SCORE.'" '._MUSTBENUMERIC, 'numeric', null, 'client'); //The score must be numeric
    $form -> addRule('score', _RATEMUSTBEBETWEEN0100, 'callback', create_function('$a', 'return ($a >= 0 && $a <= 100);')); //The score must be between 0 and 100

    $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputContentTextarea simpleEditor" style = "width:100%;height:5em;"'); //Comments on student's performance

    $user_data = eF_getTableData("users_to_lessons", "*", "users_LOGIN='".$_GET['edit_user']."' and lessons_ID=".$_SESSION['s_lessons_ID']);
    $userStats = EfrontStats::getUsersLessonStatus($currentLesson, $_GET['edit_user']);
    $form -> setDefaults(array("completed" => $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['completed'],
                               "score" => $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['score'],
                               "comments" => $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['comments'] ? $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['comments'] : ''));

    if (isset($currentUser -> coreAccess['progress']) && $currentUser -> coreAccess['progress'] != 'change') {
        $form -> freeze();
    } else {
        $form -> addElement('submit', 'submit_lesson_complete', _SUBMIT, 'class = "flatButton"'); //The submit button
        if ($form -> isSubmitted() && $form -> validate()) {
            if ($form -> exportValue('completed')) {
                $lessonUser = EfrontUserFactory :: factory($_GET['edit_user'], false, 'student');
                $lessonUser -> completeLesson($currentLesson -> lesson['id'], $form -> exportValue('score'), $form -> exportValue('comments'));
            } else {
                eF_updateTableData("users_to_lessons", array('completed' => 0, 'score' => 0, 'to_timestamp' => null), "users_LOGIN = '".$_GET['edit_user']."' and lessons_ID=".$currentLesson -> lesson['id']);
          $cacheKey = "user_lesson_status:lesson:".$currentLesson -> lesson['id']."user:".$_GET['edit_user'];
          Cache::resetCache($cacheKey);
            }

            eF_redirect(basename($_SERVER['PHP_SELF']).'?ctg=progress&message='.urlencode(_STUDENTSTATUSCHANGED).'&message_type=success');
        }
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_COMPLETE_LESSON_FORM', $renderer -> toArray());
    $doneTests = EfrontStats :: getDoneTestsPerUser($_GET['edit_user']);

    $result = EfrontStats :: getStudentsDoneTests($currentLesson -> lesson['id'], $_GET['edit_user']);
    foreach ($result[$_GET['edit_user']] as $key => $value) {
        if ($value['scorm']) {
            $scormDoneTests[$key] = $value;
        }
    }

    $testNames = eF_getTableDataFlat("tests t, content c", "t.id, c.name", "c.id=t.content_ID and c.lessons_ID=".$currentLesson -> lesson['id']);
    $testNames = array_combine($testNames['id'], $testNames['name']);


    foreach($doneTests[$_GET['edit_user']] as $key => $value) {
        if (in_array($key, array_keys($testNames))) {
            $lastTest = unserialize($doneTests[$_GET['edit_user']][$value['last_test_id']]);
            $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['done_tests'][$key] = array('name' => $testNames[$key], 'score' => $value['average_score'], 'last_test_id' => $value['last_test_id'], 'last_score' => $value['scores'][$value['last_test_id']], 'times_done' => $value['times_done'], 'content_ID' => $value[$value['last_test_id']]['content_ID']);
        }
    }
    foreach($scormDoneTests as $key => $value) {
        $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['scorm_done_tests'][$key] = array('name' => $value['name'], 'score' => $value['score'], 'content_ID' => $key);
    }

    $notDoneTests = array_diff(array_keys($testNames), array_keys($doneTests[$_GET['edit_user']]));
    $smarty -> assign("T_PENDING_TESTS", $notDoneTests);

    unset($userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]['done_tests']['average_score']);
    //pr($userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]);
    $userTime = EfrontStats :: getUsersTime($currentLesson -> lesson['id'], $_GET['edit_user']);
    $smarty -> assign("T_USER_LESSONS_INFO", $userStats[$currentLesson -> lesson['id']][$_GET['edit_user']]);
    $smarty -> assign("T_USER_TIME", $userTime[$_GET['edit_user']]);






}

//Get users list through ajax
if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {

    $users = EfrontStats::getUsersLessonStatus($currentLesson);
    $users = $users[$currentLesson -> lesson['id']];
    $result = eF_getTableDataFlat("user_types", "id", "basic_user_type='student'");
    $studentTypes = $result["id"];
    $studentTypes[] = "student";

    foreach ($users as $key => $user) {
        if (is_array($studentTypes) && array_search($user['user_type'], $studentTypes) === false){ //keep only students and sub-students
            unset($users[$key]);
        }
    }

    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
        $sort = $_GET['sort'];
        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
    } else {
        $sort = 'login';
    }
    $users = eF_multiSort($users, $sort, $order);
    $smarty -> assign("T_USERS_SIZE", sizeof($users));
    if (isset($_GET['filter'])) {
        $users = eF_filterData($users, $_GET['filter']);
    }
    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
        $users = array_slice($users, $offset, $limit);
    }
    foreach ($users as $key => $value) {
        $users[$key]['issued_certificate'] = unserialize($value['issued_certificate']);
    }
    $smarty -> assign("T_USERS_PROGRESS", $users);
    $smarty -> display('professor.tpl');
    exit;
}

//$smarty -> assign("AUTO_COMPLETE", $currentLesson -> lesson['auto_complete']);
?>
