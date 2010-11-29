<?php

if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change' ? $_change_ = 1 : $_change_ = 0;
$smarty -> assign("_change_", $_change_);

$loadScripts[] = 'includes/content';
$loadScripts[] = 'includes/comments';

if (!isset($currentContent)) {
    if (!$currentLesson) {
        if ($_GET['view_unit']) {
            $unit = new EfrontUnit($_GET['view_unit']);
            $currentLesson = new EfrontLesson($unit['lessons_ID']);
        } elseif ($_GET['package_ID']) {
            $unit = new EfrontUnit($_GET['package_ID']);
            $currentLesson = new EfrontLesson($unit['lessons_ID']);
        }
        $_SESSION['s_lessons_ID'] = $currentLesson -> lesson['id'];
    }
    $currentContent = new EfrontContentTree($currentLesson);
    if ($_student_) {
        $currentContent -> markSeenNodes($currentUser);
    }
}

//Legal values are the array of entities that the current user may actually edit or change.
foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
    $legalValues[] = $key;
}

if (isset($_GET['add']) || (isset($_GET['edit']) && in_array($_GET['edit'], $legalValues)) && $_change_) {
 try {
     if ($_GET['edit']) {
         $currentUnit = $currentContent -> seekNode($_GET['edit']);
         //The content tree does not hold data, so assign this unit its data
         $unitData = new EfrontUnit($_GET['edit']);
         $currentUnit['data'] = $unitData['data'];
     } else {
         unset($currentUnit); //Needed because we might have the &view_unit specified in the parameters
     }

     //This page has a file manager, so bring it on with the correct options
     $basedir = $currentLesson -> getDirectory();
     //Default options for the file manager
        if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
            $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
        } else {
      $options = array('delete' => false,
                 'edit' => false,
                 'share' => false,
                 'upload' => false,
                 'create_folder' => false,
                 'zip' => false,
                 'lessons_ID' => $currentLesson -> lesson['id'],
                 'metadata' => 0);
        }
        //Default url for the file manager
        $url = basename($_SERVER['PHP_SELF']).'?ctg=content&'.(isset($_GET['edit']) ? 'edit='.$_GET['edit'] : 'add=1');
        $extraFileTools = array(array('image' => 'images/16x16/arrow_right.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
        /**The file manager*/
     include "file_manager.php";

     //This page also needs an editor and ASCIIMathML
  $load_editor = true;
  if ($configuration['math_content'] && $configuration['math_images']) {
   $loadScripts[] = 'ASCIIMath2Tex';
  } elseif ($configuration['math_content']) {
   $loadScripts[] = 'ASCIIMathML';
  }

     //Create form elements
  $form = new HTML_QuickForm("create_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=content".(isset($_GET['add']) ? '&add=1' : '&edit='.$_GET['edit']), "", null, true);
     $form -> addElement('text', 'name', _UNITNAME, 'class = "inputText"');
     $form -> addElement('text', 'pdf_content', _CURRENTPDFFILE, 'class = "inputText inactive" readonly');
     $form -> addElement('textarea', 'data', _CONTENT, 'id = "editor_content_data" class = "inputContentTextarea mceEditor" style = "width:100%;height:50em;"'); //The unit content itself
     $form -> addElement('advcheckbox', 'hide_complete_unit', _HIDECOMPLETEUNITICON, null, 'class = "inputCheckbox"', array(0, 1));
     $form -> addElement('advcheckbox', 'auto_complete', _AUTOCOMPLETE, null, 'class = "inputCheckbox"', array(0, 1));
     $form -> addElement('advcheckbox', 'indexed', _DIRECTLYACCESSIBLE, null, 'class = "inputCheckbox"', array(0, 1));
     $form -> addElement('advcheckbox', 'maximize_viewport', _MAXIMIZEVIEWABLEAREA, null, 'class = "inputCheckbox"', array(0, 1));
  $form -> addElement('text', 'object_ids', _SPECIFYIDFORSREENMATCHING, 'class = "inputText"');
     $form -> addElement('advcheckbox', 'no_before_unload', _NOBEFOREUPLOAD, null, 'class = "inputCheckbox"', array(0, 1));
     $form -> addElement('advcheckbox', 'pdf_check', _UPLOADPDFFORCONTENT, null, 'class = "inputCheckbox" onclick="togglePdf()"', array(0, 1));
     $form -> addElement('select', 'hide_navigation', _HIDENAVIGATION, array(0 => _NO, 1 => _ALLHANDLES, 2 => _UPPERHANDLES, 3 => _LOWERHANDLES));
     $form -> addElement('select', 'ctg_type', _CONTENTTYPE, array('theory' => _THEORY, 'examples'=> _EXAMPLES), 'class = "inputSelect"'); //A select drop down for content type.... Exercises went away in version 3 (2007/07/10) makriria
     $form -> addElement('select', 'parent_content_ID', _UNITPARENT, array(0 => _NOPARENT)+$currentContent -> toHTMLSelectOptions());
     $form -> addElement('file', 'pdf_upload', _PDFFILE, null);
     $form -> addElement('submit', 'submit_insert_content', _SAVECHANGES, 'class = "flatButton"');

     if (strpos($currentUnit['ctg_type'], 'scorm') !== false) {
         $form -> addElement('text', 'scorm_size', _EXPLICITIFRAMESIZE, 'class = "inputText" style = "width:50px"'); //Set an explicit size for the SCORM content
         $form -> addElement('select', 'reentry_action', _ACTIONONRENTRYCOMPLETED, array(0 => _LETCONTENTDECIDE, 1 => _DONTCHANGE), 'class = "inputText"'); //Set what action should be performed when a user re-enters a visited content
         if ($currentUnit['scorm_version'] == '1.2') {
          $form -> addElement('select', 'embed_type', _EMBEDTYPE, array('iframe' => _INLINEIFRAME, 'popup'=> _NEWWINDOWPOPUP), 'class = "inputSelect"');
          $form -> addElement('text', 'popup_parameters', _POPUPPARAMETERS, 'class = "inputText" style = "width:600px"');
    $form -> setDefaults(array('popup_parameters' => 'width=800,height=600,scrollbars=no,resizable=yes,status=yes,toolbar=no,location=no,menubar=no,top="+(parseInt(parseInt(screen.height)/2) - 300)+",left="+(parseInt(parseInt(screen.width)/2) - 400)+"'));
         }

   if (strpos($currentUnit['data'], 'iframe') !== false) {
    $form -> setDefaults(array('embed_type' => 'iframe'));
   } else {
    $form -> setDefaults(array('embed_type' => 'popup'));
   }

   $form -> addRule('scorm_size', _INVALIDFIELDDATA, 'checkParameter', 'id');
         $smarty -> assign("T_SCORM", true);
     }
     //Set elements rules
     $form -> addRule('name', _THEFIELD.' "'._UNITNAME.'" '._ISMANDATORY, 'required', null, 'client'); //The name is mandatory
     //$form -> addRule('ctg_type', _THEFIELD.' '._CONTENTTYPE.' '._ISMANDATORY, 'required', null, 'client');       //The content type is mandatry
     $form -> addRule('parent_content_ID', _THEFIELD.' '._UNITPARENT.' '._ISMANDATORY, 'required', null, 'client');
     $form -> addRule('parent_content_ID', _INVALIDID, 'numeric');

     //Add the content's questions, in order to setup "complete with question" field
     if (sizeof($currentLesson -> getQuestions()) > 0) {
      $pathStrings = $currentContent -> toPathStrings();
      foreach ($currentLesson -> getQuestions() as $key => $value) {
          if ($value['type'] != 'raw_text' || $value['id'] == $currentUnit['options']['complete_question']) {
           $plainText = trim(strip_tags($value['text']));
           if (mb_strlen($plainText) > Question :: maxQuestionText) {
               $plainText = mb_substr($plainText, 0, Question :: maxQuestionText).'...';
           }
           $pathStrings[$value['content_ID']]? $lessonQuestions[$value['id']] = $pathStrings[$value['content_ID']].'&nbsp;&raquo;&nbsp;'.$plainText : $lessonQuestions[$value['id']] = $plainText;
          }
      }
      if (!empty($lessonQuestions) || $currentUnit['options']['complete_question']) {
       $form -> addElement('advcheckbox', 'complete_question', _COMPLETEWITHQUESTION, null, 'class = "inputCheckbox" onclick = "$(\'complete_questions\').toggle()"', array(0, 1));
       $form -> addElement('select', 'questions', null, $lessonQuestions, 'id = "complete_questions" style = "display:none"');
      }
     }

     //Set elements default values
     $form -> setDefaults($currentUnit['options']);
     preg_match("/eF_js_setCorrectIframeSize\((.*)\)/", $currentUnit['data'], $matches);
     $form -> setDefaults(array('scorm_size' => isset($matches[1]) ? $matches[1] : null,
                                'data' => $currentUnit['data'],
              'name' => $currentUnit['name'],
              'ctg_type' => $currentUnit['ctg_type'],
                 'complete_question' => $currentUnit['options']['complete_question'] ? 1 : 0,
                                'questions' => $currentUnit['options']['complete_question'],
                                'parent_content_ID' => isset($_GET['view_unit']) ? $_GET['view_unit'] : 0));
     //If the "complete with question" option is set, show the selected question
     $currentUnit['options']['complete_question'] ? $form -> updateElementAttr(array('questions'), array('style' => 'display:""')) : null;



     //Check whether it is a pdf content and handle accordingly
     if (mb_strpos($currentUnit['data'], "<iframe") !== false && mb_strpos($currentUnit['data'], "pdfaccept") !== false) {
         $fileEnd = mb_strpos($currentUnit['data'], ".pdf");
         $contentParts = explode("/", mb_substr($currentUnit['data'], 0, $fileEnd));
         $form -> setDefaults(array('pdf_content' => EfrontFile :: decode(htmlspecialchars_decode(urldecode($contentParts[sizeof($contentParts)-1].'.pdf')))));
         $form -> setDefaults(array('pdf_check' => 1));
         $smarty -> assign("T_EDITPDFCONTENT", true);
     }

     //You can't change a unit's parent from this form. You must use the content tree management page
     if ($_GET['edit']) {
         $form -> setDefaults(array('parent_content_ID' => $currentUnit['parent_content_ID']));
         $form -> freeze(array('parent_content_ID'));
     }

     //If the form was submitted with pdf content, take special care
     if ($form -> isSubmitted() && $form -> validate()) {
         $values = $form -> exportValues();

         if ($_FILES['pdf_upload']['name'] != "") {
             if (strpos($_FILES['pdf_upload']['name'], ".pdf") !== false) {
                 $destinationDir = new EfrontDirectory(G_LESSONSPATH.$_SESSION['s_lessons_ID']);
                 $filesystem = new FileSystemTree(G_LESSONSPATH.$_SESSION['s_lessons_ID']);
                 try {
                     $uploadedFile = $filesystem -> uploadFile('pdf_upload', $destinationDir);
                     $values['data'] = '<iframe src="'.$currentLesson -> getDirectoryUrl().'/'.$uploadedFile["physical_name"].'"  name="pdfaccept" width="100%" height="600"></iframe>';
                 } catch (EfrontFileException $e) {
                     echo $e -> getMessage();
                 }
             } else {
                 $message = _YOUMUSTUPLOADAPDFFILE;
                 eF_redirect("".basename($_SERVER['PHP_SELF']).'?ctg=content&'.$post_target."&message=".urlencode($message)."&message_type=failure");
                 exit;
             }
         }

         $options = serialize(array('hide_complete_unit' => $values['hide_complete_unit'],
                                    'auto_complete' => $values['auto_complete'],
                  'hide_navigation' => $values['hide_navigation'],
                                    'indexed' => $values['indexed'],
            'maximize_viewport' => $values['maximize_viewport'],
            'object_ids' => $values['object_ids'],
                                    'no_before_unload' => $values['no_before_unload'],
                              'reentry_action' => isset($values['reentry_action']) ? $values['reentry_action'] : false,
                     'complete_question' => $values['complete_question'] ? $values['questions'] : 0));


   if (isset($_GET['edit'])) {
             //You can't edit data in scorm units
             if (strpos($currentUnit['ctg_type'], 'scorm') === false) {
                 $currentUnit['data'] = $values['data'];
             } else {
     if ($values['embed_type'] == 'iframe' && strpos($currentUnit['data'], 'window.open') !== false) {
      preg_match("/window.open\(.*,/U", $currentUnit['data'], $matches);
      $scormValue = str_replace(array('window.open("', '",'),"",$matches[0]);
      $currentUnit['data'] = '<iframe height = "100%"  width = "100%" frameborder = "no" name = "scormFrameName" id = "scormFrameID" src = "'.$scormValue. '" onload = "if (window.eF_js_setCorrectIframeSize) {eF_js_setCorrectIframeSize();} else {setIframeSize = true;}"></iframe>';
     } elseif ($values['embed_type'] == 'popup' && strpos($currentUnit['data'], 'iframe') !== false) {
      preg_match("/src.*onload/U", $currentUnit['data'], $matches);
      $scormValue = str_replace(array('src = "', '" onload'),"",$matches[0]);
      $currentUnit['data'] = '
                              <div style = "text-align:center;height:300px">
                               <span>'._CLICKTOSTARTUNIT.'</span><br/>
                            <input type = "button" value = "'._STARTUNIT.'" class = "flatButton" onclick = \'window.open("'.$scormValue.'", "scormFrameName", "'.$values['popup_parameters'].'")\' >
                           </div>';
     } elseif ($values['embed_type'] == 'popup' && strpos($currentUnit['data'], 'window.open') !== false) { //in case changing only popup parameters field
      preg_match("/\"scormFrameName\".*\"\)'/U", $currentUnit['data'], $matches);
      $currentUnit['data'] = preg_replace("/\"scormFrameName\".*\"\)'/U", '"scormFrameName", "'.$values['popup_parameters'].'")\'' , $currentUnit['data']);
     }
                 $currentUnit['data'] = preg_replace("/eF_js_setCorrectIframeSize\(.*\)/", "eF_js_setCorrectIframeSize(".$values['scorm_size'].")", $currentUnit['data']);
             }
             $values['ctg_type'] ? $currentUnit['ctg_type'] = $values['ctg_type'] : null;
             $values['name'] ? $currentUnit['name'] = $values['name'] : null;
             $currentUnit['options'] = $options;

             $currentUnit -> persist();
             $currentUnit -> setSearchKeywords();
         } else {
             $fields = array('name' => $values['name'],
                             'data' => $values['data'],
                             'parent_content_ID' => $values['parent_content_ID'],
                             'lessons_ID' => $_SESSION['s_lessons_ID'],
                             'ctg_type' => $values['ctg_type'],
                             'active' => 1,
                             'options' => $options);
             $currentUnit = $currentContent -> insertNode($fields);
         }

         $message = _OPERATIONCOMPLETEDSUCCESSFULLY;
         $message_type = 'success';

         eF_redirect(''.basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$currentUnit['id'].'&message='.urlencode($message).'&message_type=success');
     }

     $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
     $form -> setRequiredNote(_REQUIREDNOTE);

     $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
     $renderer->setRequiredTemplate(
         '{$html}{if $required}
              &nbsp;<span class = "formRequired">*</span>
          {/if}'
          );

          $renderer->setErrorTemplate(
         '{$html}{if $error}
              <span class = "formError">{$error}</span>
          {/if}'
          );
     $form -> accept($renderer);

     $smarty -> assign('T_ENTITY_FORM', $renderer -> toArray());

 } catch (Exception $e) {
     $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
     $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
     $message_type = 'failure';
 }

} else if (!$currentUnit && $_student_ && !isset($_GET['package_ID'])) {
    $basicIterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST));
    if (isset($_GET['type']) && $_GET['type'] == 'tests') {
        //if ($GLOBALS['configuration']['disable_tests'] == 1) {exit;}
        $iterator = new EfrontTestsFilterIterator(new EfrontVisitableFilterIterator($basicIterator));
    } else if (isset($_GET['type']) && $_GET['type'] == 'theory') {
        $iterator = new EfrontTheoryFilterIterator(new EfrontVisitableFilterIterator($basicIterator));
    } else if (isset($_GET['type']) && $_GET['type'] == 'examples') {
        $iterator = new EfrontExampleFilterIterator(new EfrontVisitableFilterIterator($basicIterator));
    } else {
        $iterator = new EfrontVisitableAndEmptyFilterIterator($basicIterator);
    }

    //Find the parents of each of these units, so that we can keep them in the tree
    foreach ($iterator as $key => $value) {
        foreach ($currentContent -> getNodeAncestors($key) as $parent) {
            $parents[$parent['id']] = $parent['id'];
        }
    }
    //This iterator keeps the special units (for example, tests or examples) plus their parents
    $iterator = new EfrontInArrayFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST))), $parents);

    $smarty -> assign("T_THEORY_TREE", $currentContent -> toHTML($iterator, 'dhtmlContentTree'));
} else {
 if ($configuration['math_content'] && $configuration['math_images']) {
  $loadScripts[] = 'ASCIIMath2Tex';
 } elseif ($configuration['math_content']) {
  $loadScripts[] = 'ASCIIMathML';
 }
    try {
  $log_comments = $currentUnit['id']; //in order to store unit into logs
        //This is the basic content iterator, including even inactive, unpublished or empty units
        $visitableIterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST));
        $treeOptions = array('truncateNames' => 25, 'selectedNode' => $currentUnit['id']);
        //$_professor_ ? $treeOptions['edit'] = 1 : $treeOptions['edit'] = 0;
        $ruleCheck = true;
        if ($_student_ && $_change_ && $currentLesson -> options['tracking']) {
            //$currentUser -> setSeenUnit($currentUnit, $currentLesson, 1);
            //$currentContent -> markSeenNodes($currentUser);
            $userProgress = EfrontStats :: getUsersLessonStatus($currentLesson, $currentUser -> user['login']);
            $userProgress = $userProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']];
            $seenContent = EfrontStats :: getStudentsSeenContent($currentLesson -> lesson['id'], $currentUser -> user['login']);
            $seenContent = $seenContent[$currentLesson -> lesson['id']][$currentUser -> user['login']];
            $smarty -> assign("T_SEEN_UNIT", in_array($currentUnit['id'], array_keys($seenContent))); //Notify smarty whether the student has seen the current unit
            if ($currentLesson -> options['rules']) {
                $ruleCheck = $currentContent -> checkRules($currentUnit['id'], $seenContent);
            }
            if ($ruleCheck !== true) {
                $message = $ruleCheck;
                $message_type = 'failure';
                $smarty -> assign("T_RULE_CHECK_FAILED", true);
                $ruleCheck = false;
            }
            $smarty -> assign("T_USER_PROGRESS", $userProgress);
        }
        if ($_student_) {
   //$smarty -> assign("T_NEXT_LESSON", $currentLesson -> getNextLesson());
         if ($_change_ && $currentLesson -> options['tracking'] && $currentUnit['options']['auto_complete'] && $ruleCheck) {
                //$currentUser    -> setSeenUnit($currentUnit, $currentLesson, 1);
                //$currentContent -> markSeenNodes($currentUser);
                $smarty -> assign("T_AUTO_SET_SEEN_UNIT", true);
            }
   /*$hideFeedback = false;

			foreach (new EfrontNoFeedbackFilterIterator(new EfrontVisitableAndEmptyFilterIterator($visitableIterator)) as $key => $value) {

				if (!$value['seen']) {

					$treeOptions['hideFeedback'] = true;

				}

			}		*/
            //This is an iterator with only valid units plus empty units, and is used for the navigation tree
            $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML(new EfrontVisitableAndEmptyFilterIterator($visitableIterator), 'dhtmlContentTree', $treeOptions, $scormState));
            //This is an iterator with only valid units, and is used for students to navigate back and forth
            $visitableIterator = new EfrontVisitableFilterIterator($visitableIterator);
        } else {
   if ($_change_){
    $treeOptions['edit'] = 1;
   }
            $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML($visitableIterator, 'dhtmlContentTree', $treeOptions, $scormState));
        }
        if ($_professor_ && !$currentUnit && $currentContent -> getFirstNode()) { //If a unit is not specified, then consider the first content unit by default
   $currentUnit = new EfrontUnit($currentContent -> getFirstNode() -> offsetGet('id'));
   $smarty -> assign("T_CURRENTUNITID", $currentUnit['id']);
  }
        if ($currentUnit) {
            //Let the template know that it is dealing with a SCORM unit
            if (strpos($currentUnit['ctg_type'], 'scorm') !== false) {
    $smarty -> assign("T_SCORM", true);
             $smarty -> assign("T_SCORM_VERSION", $scormVersion);
            }
            //If glossary is activated, transform content data accordingly
            if ($currentLesson -> options['glossary'] && $GLOBALS['configuration']['disable_glossary'] != 1 && !isset($_GET['print'])) {
    $currentUnit['data'] = glossary :: applyGlossary($currentUnit['data'], $currentLesson -> lesson['id']);
            }
            //Replace inner links. Inner links are created when linking from one unit to another, so they must point either to professor.php or student.php, depending on the user viewing the content
            $currentUnit['data'] = str_replace("##EFRONTINNERLINK##", $_SESSION['s_lesson_user_type'], $currentUnit['data']);
            if ($currentUnit['ctg_type'] == 'tests' || $currentUnit['ctg_type'] == 'feedback') {
                $loadScripts[] = 'scriptaculous/dragdrop';
                $loadScripts[] = 'includes/tests';
                include("tests/show_unsolved_test.php");
            }
   if (isset($_GET['print'])) {
    $currentUnit['data'] = preg_replace("#<script.*?>.*?</script>#", "", $currentUnit['data']);
    $currentUnit['data'] = strip_tags($currentUnit['data'],'<img><applet><iframe><div><br><p><ul><li>');
   }
   $smarty -> assign("T_UNIT", $currentUnit);
   //Next and previous units are needed for navigation buttons
   //package_ID denotes that a SCORM 2004 unit is active.
   if (!isset($_GET['package_ID'])) {
    $nextUnit = $currentContent -> getNextNode($currentUnit, $visitableIterator);
    $smarty -> assign("T_NEXT_UNIT", $nextUnit);
    $previousUnit = $currentContent -> getPreviousNode($currentUnit, $visitableIterator);
    $smarty -> assign("T_PREVIOUS_UNIT", $previousUnit);
             //Parents are needed for printing the titles
             $smarty -> assign("T_PARENT_LIST", $currentContent -> getNodeAncestors($currentUnit));
   } else {
       //SCORM 2004 content handles navigation on its own, so it's illegal to have additional navigation handles
       $smarty -> assign("T_PARENT_LIST", $currentContent -> getNodeAncestors($_GET['package_ID']));
       $smarty -> assign("T_SCORM_2004_TITLE", true);
   }
            $comments = array();
            $result = array_merge(comments::getComments($currentLesson -> lesson['id'], false, $currentUnit['id']),
                                    comments::getComments($currentLesson -> lesson['id'], $currentUser, $currentUnit['id'], false, false));
            foreach ($result as $value) {
                if (!isset($comments[$value['id']])) {
                    $comments[$value['id']] = $value;
                }
            }
   foreach($comments as $key => $value) {
       //$user = EfrontUserFactory :: factory($value['users_LOGIN']);
       //$comments[$key]['avatar'] = $user -> getAvatar();
   }
            $smarty -> assign("T_COMMENTS", array_values($comments));
        } else {
            $smarty -> assign("T_UNIT", array());
        }
        if ($_student_ && $_change_ && $currentLesson -> options['tracking']) {
            if ($currentUnit['options']['complete_question'] && (!in_array($currentUnit['id'], array_keys($seenContent)) || sizeof($_POST) > 0) ) {
                $lessonQuestions = $currentLesson -> getQuestions();
                if (in_array($currentUnit['options']['complete_question'], array_keys($lessonQuestions))) {
                    $question = QuestionFactory::factory($currentUnit['options']['complete_question']);
                    $smarty -> assign("T_QUESTION", $question -> toHTML(new HTML_QuickForm()));
                    if (sizeof($_POST) > 0) {
                        try {
                            $question -> setDone($_POST['question'][$question -> question['id']]);
                            $results = $question -> correct();
                            if ($results['score'] > 0.5) { //50% is considered success
                                $currentUser -> setSeenUnit($currentUnit, $currentLesson, true);
                                echo 'correct';
                            }
                        } catch (Exception $e) {
                         handleAjaxExceptions($e);
                        }
                        exit;
                    }
                }
            }
            if (isset($_GET['set_seen'])) {
                try {
                    $currentUser -> setSeenUnit($currentUnit, $currentLesson, $_GET['set_seen']);
                    $newUserProgress = EfrontStats :: getUsersLessonStatus($currentLesson, $currentUser -> user['login']);
                    $newPercentage = $newUserProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']]['overall_progress'];
                    $newConditionsPassed = $newUserProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']]['conditions_passed'];
                    $newLessonPassed = $newUserProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']]['lesson_passed'];
                    $nextLesson = $currentUser -> getNextLesson($currentLesson, $_SESSION['s_courses_ID']);
                    echo json_encode(array($newPercentage, $newConditionsPassed, $newLessonPassed, false, false, false));
                } catch (Exception $e) {
                 handleAjaxExceptions($e);
                }
                exit;
            }
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'next_lesson') {
             try {
              $nextLesson = $currentUser -> getNextLesson($currentLesson, $_SESSION['s_courses_ID']);
              if ($nextLesson) {
               $nextLessonUrl = $_SERVER['PHP_SELF'].'?lessons_ID='.$nextLesson;
               !$_SESSION['s_courses_ID'] OR $nextLessonUrl .= '&from_course='.$_SESSION['s_courses_ID'];
               echo json_encode(array('url' => $nextLessonUrl));
              } else {
               echo json_encode(array('url' => ''));
              }
             } catch (Exception $e) {
              handleAjaxExceptions($e);
             }
             exit;
            }
        }
        $options = array();
        if ($_student_ && $currentLesson -> options['content_report'] && $ruleCheck) {
            $options[] = array('text' => _CONTENTREPORT, 'image' => "16x16/warning.png", 'href' => "content_report.php?".http_build_query($_GET), 'onclick' => "eF_js_showDivPopup('"._CONTENTREPORT."', 1)", "target" => "POPUP_FRAME");
        }
        if ($currentLesson -> options['bookmarking'] && !$GLOBALS['configuration']['disable_bookmarks'] && $ruleCheck) {
         $options[] = array('text' => _ADDTHISPAGETOYOURBOOKMARKS, 'image' => "16x16/bookmark_add.png", 'onclick' => "addBookmark(this)");
        }
        if ($currentLesson -> options['comments'] && !$GLOBALS['configuration']['disable_comments'] && $ruleCheck) {
            $options[] = array('text' => _ADDCOMMENT, 'image' => "16x16/comment_add.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=comments&view_unit=".$_GET['view_unit']."&add=1&popup=1", 'onclick' => "eF_js_showDivPopup('"._ADDCOMMENT."', 1)", "target" => "POPUP_FRAME");
        }
        //$options[] = array('text' => "open window", 'image' => "16x16/add.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=content&view_unit=".$_GET['view_unit']."&bare=1&popup=1", 'onclick' => "window.open('about:blank', 'testme', 'width=800, height=600')", "target" => "testme");
        if (!$scorm2004) {
         if ($currentUnit['options']['hide_navigation'] != 1 && $currentUnit['options']['hide_navigation'] != 2) {
          if ($previousUnit) {
              $options[] = array('text' => strip_tags($previousUnit['name']),
                     'image' => "16x16/navigate_left.png",
                     'href' => $_SERVER['PHP_SELF']."?view_unit=".$previousUnit['id'],
                                 'id' => 'navigate_previous');
          }
          if ($nextUnit) {
              $options[] = array('text' => strip_tags($nextUnit['name']),
                     'image' => "16x16/navigate_right.png",
                     'href' => $_SERVER['PHP_SELF']."?view_unit=".$nextUnit['id'],
                                 'id' => 'navigate_continue');
          }
         }
        }
        $smarty -> assign("T_UNIT_OPTIONS", $options);
        $smarty -> assign("T_UNIT_SETTINGS", array('nohandle' => 1));
        $content_side_modules = array();
        foreach ($loadedModules as $module) {
            if (isset($currentLesson -> options[$module -> className]) && $currentLesson -> options[$module -> className] == 1) {
                unset($lessonContentSideHTML);
                $lessonContentSideHTML = $module -> getContentSideInfo();
                // If the module has a lesson innertable
                if ($lessonContentSideHTML) {
                    // Get module html - two ways: pure HTML or PHP+smarty
                    // If no smarty file is defined then false will be returned
                    if ($module_smarty_file = $module -> getContentSmartyTpl()) { //assignment not comparison
                        // Execute the php code -> The code has already been executed by above (**HERE**)
                        // Let smarty know to include the module smarty file
                        $content_side_modules[$module->className] = array('smarty_file' => $module_smarty_file);
                    } else {
                        // Present the pure HTML cod
                        $content_side_modules[$module->className] = array('html_code' => $lessonContentSideHTML);
                    }
                    $sideContentTitle = $module -> getContentSideTitle();
                    if ($sideContentTitle) {
                        $content_side_modules[$module->className]['title'] = $sideContentTitle;
                    } else {
                        $content_side_modules[$module->className]['title'] = $module -> getName();
                    }
                }
            }
        }
        $smarty -> assign("T_CONTENT_SIDE_MODULES", $content_side_modules);
        if ((!$currentLesson -> options['show_right_bar'] && $_student_) || $_COOKIE['rightSideBar'] == 'hidden') {
            $smarty -> assign("T_LAYOUT_CLASS", "centerFull hideLeft");
        } else {
            $smarty -> assign("T_LAYOUT_CLASS", $currentTheme -> options['toolbar_position'] == "left" ? "hideRight" : "hideLeft"); //Whether to show the sidemenu on the left or on the right
        }
        if ((!$currentLesson -> options['show_horizontal_bar'] && $_student_) || $_COOKIE['horizontalSideBar'] == 'hidden') {
            $smarty -> assign("T_HEADER_CLASS", "headerHidden");
        } else {
            $smarty -> assign("T_HEADER_CLASS", "header"); //$currentTheme -> options['toolbar_position'] == "left" ? "hideRight" : "hideLeft");    //Whether to show the sidemenu on the left or on the right
        }
        if (isset($currentUnit['options']['maximize_viewport']) && $currentUnit['options']['maximize_viewport'] && $currentUser -> getType($currentLesson) == "student") {
            $smarty -> assign("T_MAXIMIZE_VIEWPORT", 1);
        }
  if (isset($currentUnit['options']['object_ids']) && $currentUnit['options']['object_ids']) {
            $smarty -> assign("T_OBJECT_IDS", $currentUnit['options']['object_ids']);
        }
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
}
// Used for toggle horizontal sidebar
if ($GLOBALS['currentTheme'] -> options['sidebar_interface'] == 1 || $GLOBALS['currentTheme'] -> options['sidebar_interface'] == 2) {
 $smarty -> assign("T_HORIZONTAL_BAR", 1);
}
