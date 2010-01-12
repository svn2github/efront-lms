<?php 
/**
* 
* @package eFront
* @version 3.6.0
*/

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

//Create shorthands for user type, to avoid long variable names
//Create shorthands for user access rights, to avoid long variable names
$_change_ = 0;
if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
    $_change_ = 1;
} elseif (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}

if (!isset($GLOBALS['currentLesson'])) {
    if (isset($_GET['lesson_info'])) {
        $currentLesson = new EfrontLesson($_GET['lesson_info']);
        $currentContent = new EfrontContentTree($currentLesson);
        $smarty -> assign("T_CURRENT_LESSON", $currentLesson);
    } else {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".rawurlencode(_INVALIDID)."&message_type=failure");
    }
}

if ($_GET['edit_info'] && $_change_ && !$_student_) {
        $form = new HTML_QuickForm("empty_form", "post", null, null, null, true);
        try {
            $lessonInformation = unserialize($currentLesson -> lesson['info']);
            $information       = new LearningObjectInformation($lessonInformation);
            if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
                $smarty -> assign("T_LESSON_INFO_HTML", $information -> toHTML($form, false));
            } else {
                $smarty -> assign("T_LESSON_INFO_HTML", $information -> toHTML($form, false, false));
            }

            $lessonMetadata = unserialize($currentLesson -> lesson['metadata']);
            $metadata       = new DublinCoreMetadata($lessonMetadata);
            if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
                $smarty -> assign("T_LESSON_METADATA_HTML", $metadata -> toHTML($form));
            } else {
                $smarty -> assign("T_LESSON_METADATA_HTML", $metadata -> toHTML($form, true, false));
            }
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = "failure";
        }
/*
        $lessonAvatarForm = new HTML_QuickForm("lesson_avatar_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lesson_info', "", null, true);
        $lessonAvatarForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
        $lessonAvatarForm -> addElement('file', 'file_upload', _IMAGEFILE, 'class = "inputText"');
        $lessonAvatarForm -> addElement('advcheckbox', 'delete_avatar', _DELETECURRENTAVATAR, null, 'class = "inputCheckbox"', array(0, 1));
        $lessonAvatarForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);            //getUploadMaxSize returns size in KB
        $lessonAvatarForm -> addElement('submit', 'submit_upload_file', _APPLYAVATARCHANGES, 'class = "flatButton"');
        if ($lessonAvatarForm -> isSubmitted() && $lessonAvatarForm -> validate()) {

        }
*/
        if (isset($_GET['postAjaxRequest'])) {
            if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
                header("HTTP/1.0 500 ");
                echo (_UNAUTHORIZEDACCESS);
                exit;
            }
            if (in_array($_GET['dc'], array_keys($information -> metadataAttributes))) {
                if ($_GET['value']) {
                    $lessonInformation[$_GET['dc']] = htmlspecialchars(rawurldecode($_GET['value']));
                } else {
                    unset($lessonInformation[$_GET['dc']]);
                }
                $currentLesson -> lesson['info'] = serialize($lessonInformation);
            } elseif (in_array($_GET['dc'], array_keys($metadata -> metadataAttributes))) {
                if ($_GET['value']) {
                    $lessonMetadata[$_GET['dc']] = htmlspecialchars(rawurldecode($_GET['value']));
                } else {
                    unset($lessonMetadata[$_GET['dc']]);
                }
                $currentLesson -> lesson['metadata'] = serialize($lessonMetadata);
            }

            try {
                $currentLesson -> persist();
                echo htmlspecialchars(rawurldecode($_GET['value']));
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    
} else {
    $currentContent = new EfrontContentTree($currentLesson);
            
    $lesson_info_categories = array('general_description' => _GENERALDESCRIPTION,
                                    'objectives'          => _OBJECTIVES,
                                    'assessment'          => _ASSESSMENT,
                                    'lesson_topics'       => _LESSONTOPICS,
                                    'resources'           => _RESOURCES,
                                    'other_info'          => _OTHERINFO);
    $smarty -> assign("T_LESSON_INFO_CATEGORIES", $lesson_info_categories);    
    $conditions = $currentLesson -> getConditions();
    foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST))) as $key => $value) {
        $visitableContentIds[$key] = $key;                                                    //Get the not-test unit ids for this content
    }
    foreach ($iterator = new EfrontTestsFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)))) as $key => $value) {
        $testsIds[$key] = $key;                                                    //Get the not-test unit ids for this content
    }
    
    $lessonInformation = $currentLesson -> getInformation($currentUser -> user['login']);
    $smarty -> assign("T_LESSON_INFO", $lessonInformation);

    if (!$_admin_) {
        $seenContent = EfrontStats :: getStudentsSeenContent($currentLesson -> lesson['id'], $currentUser -> user['login']);

        //Get the passing score for each "specific_test" rule
        $allTestsScore = eF_getTableDataFlat("tests", "content_ID,mastery_score");
        if (sizeof($allTestsScore) > 0) {
            $allTestsScore = array_combine($allTestsScore['content_ID'], $allTestsScore['mastery_score']);
        } else {
            $allTestsScore = array();
        }
        foreach ($conditions as $key => $condition) {
            if ($condition['type'] == 'specific_test') {
                $conditions[$key]['test_passing_score'] = $allTestsScore[$condition['options'][0]];
            }
        }
        
        list($conditionsStatus, $lessonPassed) = EfrontStats :: checkConditions($seenContent[$currentLesson -> lesson['id']][$currentUser -> user['login']], $conditions, $visitableContentIds, $testsIds);
        $smarty -> assign("T_CONDITIONS", $conditions);
        $smarty -> assign("T_CONDITIONS_STATUS", $conditionsStatus);

        //$smarty -> assign("T_LESSON_PASSED", $lessonPassed);

        foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree)), array('id', 'name')) as $key => $value) {
            $key == 'id' ? $ids[] = $value : $names[] = $value;
        }
        $smarty -> assign("T_TREE_NAMES", array_combine($ids, $names));
        $smarty -> assign("T_BASE_URL", "ctg=lesson_information");
    } else {
        $smarty -> assign("T_BASE_URL", "ctg=lessons&lesson_info=".$currentLesson -> lesson['id']);
    }
}         
        
        
?>