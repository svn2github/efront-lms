<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}


$loadScripts[] = 'includes/questions';
if (!$_change_) {
    throw new EfrontUserException(_UNAUTHORIZEDACCESS, EfrontUserException::RESTRICTED_USER_TYPE);
}

//This page has a file manager, so bring it on with the correct options
!$skillgap_tests ? $basedir = $currentLesson -> getDirectory() : $basedir = G_EXTERNALPATH;
is_dir($basedir) OR mkdir($basedir, 0755);

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
$url = basename($_SERVER['PHP_SELF']).'?ctg=tests&'.(isset($_GET['edit_question']) ? 'edit_question='.$_GET['edit_question'] : 'add_question=1');
$filesystem = new FileSystemTree($basedir, true);
$filesystemIterator = new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new ArrayIterator($filesystem -> tree)));

foreach ($filesystemIterator as $key => $value) {
    $value['id'] == -1 ? $identifier = $value['path'] : $identifier = $value['id'];
  $value -> offsetSet(_INSERT, '<div style="text-align:center"><img src = "images/16x16/arrow_right.png" alt = "'._INSERTEDITOR.'" title = "'._INSERTEDITOR.'" class = "ajaxHandle" onclick = "insert_editor(this, $(\'span_'.urlencode($identifier).'\').innerHTML)" /></div>');
}
$extraColumns = array(_INSERT);
//$extraFileTools = array(array('image' => 'images/16x16/arrow_right.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));

/**The file manager*/
include "file_manager.php";

//This page also needs an editor and ASCIIMathML
$load_editor = true;
if ($configuration['math_content'] && $configuration['math_images']) {
    $loadScripts[] = 'ASCIIMath2Tex';
} elseif ($configuration['math_content']) {
    $loadScripts[] = 'ASCIIMathML';
}

$questionTypes = Question :: $questionTypes;
// Remove development questions from automatically corrected skillgap tests
if ($skillgap_tests) {
    unset($questionTypes['raw_text']);
}

isset($_GET['question_type']) && in_array($_GET['question_type'], array_keys($questionTypes)) ? $question_type = $_GET['question_type'] : $question_type = 'multiple_one';

if (isset($_GET['edit_question'])) { //We are changing an existing question.
    $currentQuestion = QuestionFactory :: factory($_GET['edit_question']);
    $postTarget = basename($_SERVER['PHP_SELF'])."?ctg=tests&from_unit=".$_GET['from_unit']."&edit_question=".$currentQuestion -> question['id']."&question_type=".$currentQuestion -> question['type'];
} else {
    $postTarget = basename($_SERVER['PHP_SELF'])."?ctg=tests&add_question=1&from_unit=".$_GET['from_unit']."&question_type=".$question_type;
}
//We asked to add/edit a question through the tests interface, so we must return there after submission
if (strpos($_SERVER['HTTP_REFERER'], 'edit_test') !== false) {
    preg_match("/edit_test=(\d+)/", $_SERVER['HTTP_REFERER'], $matches);
    if (sizeof($matches) > 0) {
        $postTarget .= '&from_test='.$matches[1];
    }
}
$form = new HTML_QuickForm("question_form", "post", $postTarget, "", null, true);
$form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter

if (!$skillgap_tests) {
    $optionsArray = $currentContent -> toHTMLSelectOptions();
    $optionsArray = array(0 => _NOPARENT) + $optionsArray;

    $form -> addElement('select', 'content_ID', _UNITPARENT, $optionsArray); //Build a select box with all content units
    $form -> addRule('content_ID', _THEFIELD.' '._UNITPARENT.' '._ISMANDATORY, 'required'); //The content id must be present and a numeric value.
    $form -> addRule('content_ID', _INVALIDID, 'numeric');
    if (isset($_GET['content_ID'])) {
        $form -> setDefaults(array('content_ID' => $_GET['content_ID'])); //If a content is specified, then set it to be selected as well
    } elseif (isset($_GET['from_unit'])) {
        $form -> setDefaults(array('content_ID' => $_GET['from_unit'])); //If a content is specified, then set it to be selected as well
    }
}
$form -> addElement('select', 'question_type', _QUESTIONTYPE, $questionTypes, 'id = "question_type" onchange = "window.location = \''.basename($_SERVER['PHP_SELF']).'?ctg=tests&add_question=1&from_unit='.$_GET['from_unit'].'&question_type=\'+this.options[this.selectedIndex].value"'); //Depending on user selection, changing the question type reloads the page with the corresponding form fields
$form -> addElement('select', 'difficulty', _DIFFICULTY, Question::$questionDifficulties);
$form -> addElement('text', 'estimate_min', _ESTIMATETIMETOCOMPLETE, 'size = "3"');
$form -> addElement('text', 'estimate_sec', null, 'size = "3"');
$form -> addElement('textarea', 'question_text', _QUESTIONTEXT, 'class = "mceEditor inputTextarea_QuestionText" style = "width:100%;height:250px;" id = "editor_content_data"');
$form -> addElement('textarea', 'explanation', _EXPLANATION, 'class = "mceEditor" style = "width:99%;height:100px;" id = "question_explanation_data"'); //The style needs to be here, since when a textarea is in "display:none" mode, the tinymce does not render the class correctly

$form -> addElement('submit', 'submit_question', _SAVEQUESTION, 'class = "flatButton"');
$form -> addElement('submit', 'submit_new_question', _SAVEASNEWQUESTION, 'class = "flatButton"');

$form -> addRule('estimate_min', _INVALIDFIELDDATA, 'numeric', null, 'client');
$form -> addRule('estimate_sec', _INVALIDFIELDDATA, 'numeric', null, 'client');
$form -> addRule('difficulty', _THEFIELD.' '._DIFFICULTY.' '._ISMANDATORY, 'required', null, 'client'); //Difficulty is mandatory and can be only a plain string
$form -> addRule('question_type', _THEFIELD.' '._QUESTIONTYPE.' '._ISMANDATORY, 'required', null, 'client');
//$form -> addRule('question_text', _THEFIELD.' '._ISMANDATORY, 'required', null);
$form -> setDefaults(array('question_type' => $question_type)); //Set the default selected question type to be 'multiple_one'
if (isset($_GET['difficulty'])) {
    $form -> setDefaults(array('difficulty' => $_GET['difficulty'])); //If a difficulty is specified, then set it to be selected
} else {
    $form -> setDefaults(array('difficulty' => 'medium')); //else, set the default selected difficulty to be 'medium'
}
if (strpos($postTarget, '&from_test') === false) {
    //This means that we got here by clicking on the "add new question" icon of a specific test. We don't want the "submit_question_another" button, since it will break the referer, and won't return back to the test
    //@todo: change the detection method, not to use referer, but rather a simple GET parameter
    $form -> addElement('submit', 'submit_question_another', _SAVEQUESTIONANDCREATENEW, 'class = "flatButton"');
}

if (isset($currentQuestion)) { //If we are changing an existing question
//pr($currentQuestion);
    $form -> setDefaults(array('content_ID' => $currentQuestion -> question['content_ID'], //Set form values to the stored ones.
                               //'code'		   => $currentQuestion -> question['code'],
                               'question_type' => $currentQuestion -> question['type'],
                               'difficulty' => $currentQuestion -> question['difficulty'],
                               'question_text' => $currentQuestion -> question['text'],
                               'explanation' => $currentQuestion -> question['explanation']));
    if ($currentQuestion -> question['estimate']) {
        $interval = eF_convertIntervalToTime($currentQuestion -> question['estimate']);
        $form -> setDefaults(array('estimate_min' => $interval['minutes'],
                                'estimate_sec' => $interval['seconds']));
    }
    /*

     if ($currentQuestion -> question['type'] == "raw_text" && strpos($currentQuestion -> question['answer'],"<a href") !== false) {

     $smarty -> assign("T_HTML_ANSWER",$currentQuestion -> question['answer']);

     }

     */
    $form -> freeze('question_type'); //The question type cannot be changed
    $smarty -> assign("T_HAS_EXPLANATION", $currentQuestion -> question['explanation']); //If the question has an explanation, use this smarty tag to set explanation field to be visible by default.
}
switch ($_GET['question_type']) { //Depending on the question type, the user might have added new form fields. We need to recreate the form, in order to be able to handle them both in case of succes or failure.
    case 'multiple_one':
        if ($form -> isSubmitted() || isset($currentQuestion)) {
            if (isset($currentQuestion) && !$form -> isSubmitted()) {
                $values['multiple_one'] = array_values(unserialize($currentQuestion -> question['options'])); //We put array_values to make sure that the array starts from zero
                // Types are from K1-K4 for multiple_one
                //$qtype_ans = (sizeof($values['multiple_one']) < 6)?sizeof($values['multiple_one']):5;
                //$smarty -> assign("T_QUESTION_TYPE_CODE", "K" . ($qtype_ans-1));
                $values['correct_multiple_one'] = unserialize($currentQuestion -> question['answer']);
                $values['correct_multiple_one'] = $values['correct_multiple_one'][0]; //In multiple_one, only one value is valid. Get this out of the array
                $values['answers_explanation'] = $currentQuestion -> answers_explanation;
            } else {
                $values = $form -> getSubmitValues();
            }
            //Create each multiple choice from the beginning, this way including any choices the user added himself
            foreach ($values['multiple_one'] as $key => $value) {
                $form -> addElement('text', 'multiple_one['.$key.']', null, 'class = "inputText inputText_QuestionChoice"');
                $form -> addElement('text', 'answers_explanation['.$key.']', null, 'class = "inputText inputText_QuestionChoice"'.(!$values['answers_explanation'][$key] ? 'style = "display:none"' : ''));
                $form -> addRule('multiple_one['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                $form -> setDefaults(array('multiple_one['.$key.']' => $value));
                $form -> setDefaults(array('answers_explanation['.$key.']' => $values['answers_explanation'][$key]));
            }

            $form -> addElement('select', 'correct_multiple_one', _RIGHTANSWER, range(1, sizeof($values['multiple_one'])), 'id = "correct_multiple_one"');//Rebuild the correct options list, to be as large as the options the user added
            $form -> setDefaults(array('correct_multiple_one' => $values['correct_multiple_one'])); //Set the selected correct option to be the one the user selected

            if ($form -> validate()) {
                $question_values = array('type' => 'multiple_one',
                                         'options' => serialize($values['multiple_one']),
                                         'answer' => serialize(array($values['correct_multiple_one'])));
            }
        } else {
            //By default, only 2 options are displayed
            $form -> addElement('text', 'multiple_one[0]', _INSERTMULTIPLEQUESTIONS, 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'multiple_one[1]', '', 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'answers_explanation[0]', null, 'class = "inputText inputText_QuestionChoice" style = "display:none"');
            $form -> addElement('text', 'answers_explanation[1]', null, 'class = "inputText inputText_QuestionChoice" style = "display:none"');

            $form -> addRule('multiple_one[0]', _THEFIELD.' "'._INSERTMULTIPLEQUESTIONS.'" '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('multiple_one[1]', _THEFIELD.' "'._INSERTMULTIPLEQUESTIONS.'" '._ISMANDATORY, 'required', null, 'client');
            $form -> addElement('select', 'correct_multiple_one', _RIGHTANSWER, array(1, 2), 'id = "correct_multiple_one"');
        }
        break;

    case 'multiple_many':
  $form -> addElement('advcheckbox', 'answers_or', _USEORLOGICTOCORRECTANSWERS, null, 'class = "inputCheckBox"', array(0, 1));
        if ($form -> isSubmitted() || isset($currentQuestion)) {
            if (isset($currentQuestion) && !$form -> isSubmitted()) {
                $values['multiple_many'] = unserialize($currentQuestion -> question['options']);

                // Types are from K5-K6 for multiple_one
                //$qtype_ans = sizeof($values['multiple_many']);
                //if ($qtype_ans <= 4) {
                //    $smarty -> assign("T_QUESTION_TYPE_CODE", "K5");
                //} else if ($qtype_ans >= 5) {
                //    $smarty -> assign("T_QUESTION_TYPE_CODE", "K6");
                //}

                $values['correct_multiple_many'] = unserialize($currentQuestion -> question['answer']);
                $values['answers_explanation'] = $currentQuestion -> answers_explanation;
            } else {
                $values = $form -> getSubmitValues();
            }

            //Create each multiple choice from the beginning, this way including any choices the user added himself
            foreach ($values['multiple_many'] as $key => $value) {
                $form -> addElement('text', 'multiple_many['.$key.']', 'Insert Questions', 'class = "inputText inputText_QuestionChoice"');
                $form -> addElement('checkbox', 'correct_multiple_many['.$key.']');
                $form -> addElement('text', 'answers_explanation['.$key.']', null, 'class = "inputText inputText_QuestionChoice"'.(!$values['answers_explanation'][$key] ? 'style = "display:none"' : ''));
                $form -> addRule('multiple_many['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                $form -> setDefaults(array('multiple_many['.$key.']' => $value));
                $form -> setDefaults(array('correct_multiple_many['.$key.']' => $values['correct_multiple_many'][$key]));
                $form -> setDefaults(array('answers_explanation['.$key.']' => $values['answers_explanation'][$key]));
    $form -> setDefaults(array('answers_or' => $currentQuestion -> settings['answers_or']));
            }

            if ($form -> validate()) {
                $question_values = array('type' => 'multiple_many',
                                         'options' => serialize($values['multiple_many']),
                                         'answer' => serialize($values['correct_multiple_many']),
           'settings' => serialize(array('answers_or' => $form -> exportValue('answers_or'))));
            }
        } else {
            //By default, only 2 options are displayed
            $form -> addElement('text', 'multiple_many[0]', 'Insert Multiple Questions (many)', 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'multiple_many[1]', null, 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'answers_explanation[0]', null, 'class = "inputText inputText_QuestionChoice" style = "display:none"');
            $form -> addElement('text', 'answers_explanation[1]', null, 'class = "inputText inputText_QuestionChoice" style = "display:none"');
            $form -> addElement('checkbox', 'correct_multiple_many[0]');
            $form -> addElement('checkbox', 'correct_multiple_many[1]');
            $form -> addRule('multiple_many[1]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('multiple_many[1]', _INVALIDFIELDDATA, 'checkParameter', 'text');
            $form -> addRule('multiple_many[0]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
        }
        break;

    case 'raw_text':
  $form -> addElement('select', 'force_correct', _QUESTIONCORRECTION, array('manual' => _MANUALLY, 'auto' => _AUTOMATIC, 'none' => _DONOTTAKEACCOUNTINCORRECTING), 'onchange = "if (this.options[this.options.selectedIndex].value==\'auto\') {$(\'autocorrect\').show();} else {$(\'autocorrect\').hide();}"');
        $form -> addElement('textarea', 'example_answer', _EXAMPLEANSWER, 'class = "inputTextarea_QuestionExample" style = "width:100%" ');

        if ($form -> isSubmitted() || isset($currentQuestion)) {

         if (isset($currentQuestion) && !$form -> isSubmitted()) {
                $form -> setDefaults(array('example_answer' => $currentQuestion -> question['answer'],
                         'force_correct' => $currentQuestion -> settings['force_correct']));
                if ($currentQuestion -> settings['force_correct'] == 'auto') {
                 $smarty -> assign("T_QUESTION_SETTINGS", $currentQuestion -> settings);
                }
            }

            foreach ($_POST['autocorrect_contains'] as $key => $value) {
    if ($_POST['autocorrect_words'][$key]) {
              $words = explode("|", $_POST['autocorrect_words'][$key]);
              array_walk($words, create_function('&$v, $k', '$v = trim($v);'));
              $autocorrect[] = array('contains' => $_POST['autocorrect_contains'][$key],
                      'score' => is_numeric($_POST['autocorrect_score'][$key]) ? $_POST['autocorrect_score'][$key] : 0,
                      'words' => $words);
    }
            }

            if ($form -> validate()) {
             if ($currentQuestion) {
              $settings = $currentQuestion -> settings;
             }
             $settings['force_correct'] = $form -> exportValue('force_correct');
             $settings['threshold'] = is_numeric($_POST['autocorrect_threshold']) ? $_POST['autocorrect_threshold'] : 0;
             $settings['autocorrect'] = $autocorrect;

                $question_values = array('type' => 'raw_text',
                                         'options' => '',
                                         'answer' => $form -> exportValue('example_answer'),
           'settings' => serialize($settings));
            }

        }

        break;

    case 'match':
        if ($form -> isSubmitted() || isset($currentQuestion)) {
            if (isset($currentQuestion) && !$form -> isSubmitted()) {
                $values['match'] = unserialize($currentQuestion -> question['options']);
                $values['correct_match'] = unserialize($currentQuestion -> question['answer']);
                $values['answers_explanation'] = $currentQuestion -> answers_explanation;
            } else {
                $values = $form -> getSubmitValues();
            }
            foreach ($values['match'] as $key => $value) {
                $form -> addElement('text', 'match['.$key.']', null, 'class = "inputText inputText_QuestionChoice"');
                $form -> addElement('text', 'correct_match['.$key.']', null, 'class = "inputText inputText_QuestionChoice"');
                $form -> addElement('text', 'answers_explanation['.$key.']', null, 'class = "inputText inputText_QuestionChoice"'.(!$values['answers_explanation'][$key] ? 'style = "display:none"' : ''));
                $form -> addRule('match['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                $form -> addRule('correct_match['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                $form -> setDefaults(array('match['.$key.']' => $value));
                $form -> setDefaults(array('correct_match['.$key.']' => $values['correct_match'][$key]));
                $form -> setDefaults(array('answers_explanation['.$key.']' => $values['answers_explanation'][$key]));
            }

            if ($form -> validate()) {
                $question_values = array('type' => 'match',
                                         'options' => serialize($values['match']),
                                         'answer' => serialize($values['correct_match']));
            }
        } else {
            //By default, only 2 pairs of choices given.
            $form -> addElement('text', 'match[0]', null, 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'correct_match[0]', null, 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'answers_explanation[0]', null, 'class = "inputText inputText_QuestionChoice" style = "display:none"');
            $form -> addElement('text', 'match[1]', null, 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'correct_match[1]', null, 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'answers_explanation[1]', null, 'class = "inputText inputText_QuestionChoice" style = "display:none"');
            $form -> addRule('match[0]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('correct_match[0]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('match[1]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('correct_match[1]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
        }
        break;
    case 'drag_drop':
        if ($form -> isSubmitted() || isset($currentQuestion)) {
            if (isset($currentQuestion) && !$form -> isSubmitted()) {
                $values['drag_drop'] = unserialize($currentQuestion -> question['options']);
                $values['correct_drag_drop'] = unserialize($currentQuestion -> question['answer']);
                $values['answers_explanation'] = $currentQuestion -> answers_explanation;
            } else {
                $values = $form -> getSubmitValues();
            }

            foreach ($values['drag_drop'] as $key => $value) {
                $form -> addElement('text', 'drag_drop['.$key.']', null, 'class = "inputText inputText_QuestionChoice"');
                $form -> addElement('text', 'correct_drag_drop['.$key.']', null, 'class = "inputText inputText_QuestionChoice"');
                $form -> addElement('text', 'answers_explanation['.$key.']', null, 'class = "inputText inputText_QuestionChoice"'.(!$values['answers_explanation'][$key] ? 'style = "display:none"' : ''));
                $form -> addRule('correct_drag_drop['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                $form -> addRule('drag_drop['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                $form -> setDefaults(array('drag_drop['.$key.']' => $value));
                $form -> setDefaults(array('correct_drag_drop['.$key.']' => $values['correct_drag_drop'][$key]));
                $form -> setDefaults(array('answers_explanation['.$key.']' => $values['answers_explanation'][$key]));
            }

            if ($form -> validate()) {
                $question_values = array('type' => 'drag_drop',
                                         'options' => serialize($values['drag_drop']),
                                         'answer' => serialize($values['correct_drag_drop']));
            }
        } else {
            //By default, only 2 pairs of choices given.
            $form -> addElement('text', 'drag_drop[0]', null, 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'drag_drop[1]', null, 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'correct_drag_drop[1]', null, 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'correct_drag_drop[0]', null, 'class = "inputText inputText_QuestionChoice"');
            $form -> addElement('text', 'answers_explanation[0]', null, 'class = "inputText inputText_QuestionChoice" style = "display:none"');
            $form -> addElement('text', 'answers_explanation[1]', null, 'class = "inputText inputText_QuestionChoice" style = "display:none"');
            $form -> addRule('drag_drop[1]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('drag_drop[0]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('correct_drag_drop[0]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('correct_drag_drop[1]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
        }
        break;
    case 'empty_spaces':
  $form -> addElement('advcheckbox', 'select_list', _DISPLAYALTERNATIVESINSELECTBOX, null, 'class = "inputCheckBox"', array(0, 1));
     $form -> addElement('button', 'generate_empty_spaces', _CREATEEMPTYSPACES, 'class = "flatButton" onclick = "eF_js_createEmptySpaces()"');

        if ($form -> isSubmitted() || isset($currentQuestion)) {
            if (isset($currentQuestion) && !$form -> isSubmitted()) {
                $values['empty_spaces'] = unserialize($currentQuestion -> question['answer']);
                $form -> setDefaults(array('select_list' => $currentQuestion -> settings['select_list']));
                //$smarty -> assign("T_QUESTION_TYPE_CODE", "K7");
            } else {
                $values = $form -> getSubmitValues();
            }

            $excerpts = preg_split('/###(\d*)/', $currentQuestion -> question['text']);
      preg_match_all('/###(\d*)/', $currentQuestion -> question['text'], $matches);
            $smarty -> assign("T_EXCERPTS", $excerpts);

            foreach ($values['empty_spaces'] as $key => $value) {
                $form -> addElement('text', 'empty_spaces['.$key.']', null, 'class = "inputText emptySpacesField" style = "width:'.($matches[1][$key] ? $matches[1][$key] : 250).'px"');
                $form -> setDefaults(array('empty_spaces['.$key.']' => $value));
            }

            if ($form -> validate()) {
                $question_values = array('type' => 'empty_spaces',
                                         'options' => '',
                                         'answer' => serialize($values['empty_spaces']),
                       'settings' => serialize(array('select_list' => $form -> exportValue('select_list'))));
            }
        }
        break;

    case 'true_false':
        $form -> addElement('select', 'correct_true_false', _RIGHTANSWER, array(0 => _FALSE, 1 => _TRUE));
        $form -> addRule('true_false', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
        if ($form -> isSubmitted() || isset($currentQuestion)) {
            if (isset($currentQuestion) && !$form -> isSubmitted()) {
                $values['correct_true_false'] = unserialize($currentQuestion -> question['answer']);
                $form -> setDefaults(array('correct_true_false' => $values['correct_true_false']));
            } else {
                $values = $form -> getSubmitValues();
            }

            if ($form -> validate()) {
                $question_values = array('type' => 'true_false',
                                         'options' => '',
                                         'answer' => serialize($values['correct_true_false']));
            }
        }
        break;

    default: break;
}

//Common fields and actions for all question types
if ($form -> isSubmitted() && $form -> validate()) {
    $form_values = $form -> exportValues();
    //$question_values['code']        = $form_values['code'];
    $question_values['text'] = $form_values['question_text'];
    $question_values['content_ID'] = $form_values['content_ID'] ? $form_values['content_ID'] : 0;
    $question_values['difficulty'] = $form_values['difficulty'];
    $question_values['explanation'] = $form_values['explanation'];
    $question_values['lessons_ID'] = $currentLesson -> lesson['id'] ? $currentLesson -> lesson['id'] : 0;
    $question_values['answers_explanation'] = serialize($form_values['answers_explanation']);

    if ($form_values['estimate_min'] || $form_values['estimate_sec']) {
        $estimate = $form_values['estimate_min']*60 + $form_values['estimate_sec'];
        $question_values['estimate'] = $estimate;
    } else {
        $question_values['estimate'] = null;
    }

    isset($_GET['from_test']) ? $location = '&edit_test='.$_GET['from_test'] : $location = '';
    if (isset($currentQuestion)) { //If we are changing an existing question
        if (isset($form_values['submit_new_question'])) {
            $newQuestion = Question :: createQuestion($question_values);
            $new_question_id = $newQuestion -> question['id'];
            // Code to maintain consistent state in questions_to_skills:
            // -- add either question to lesson specific skill if lesson['course_only'] == 0
            // -- or question to course specific skill if lesson['course_only'] == 1
            // Automatic skill injection only for educational version
            eF_redirect("".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests$location&message=".rawurlencode(_SUCCESFULLYADDEDQUESTION)."&message_type=success&tab=question");
        } else {
            $currentQuestion -> question = array_merge($currentQuestion -> question, $question_values); //This way, latter values (new ones) replace former (current ones);
            $currentQuestion -> persist(); //Update the question
            eF_redirect("".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests$location&message=".rawurlencode(_SUCCESFULLYUPDATEDQUESTION)."&message_type=success&tab=question"); //&question is used for the tabber to enable the correct tab
        }
    } else { //We are inserting a new question
        $newQuestion = Question :: createQuestion($question_values);
        $new_question_id = $newQuestion -> question['id'];
        // Code to maintain consistent state in questions_to_skills:
        // -- add either question to lesson specific skill if lesson['course_only'] == 0
        // -- or question to course specific skill if lesson['course_only'] == 1
        if ($form -> exportValue('submit_question')) {
            eF_redirect("".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&from_unit=".$_GET['from_unit']."$location&message=".rawurlencode(_SUCCESFULLYADDEDQUESTION)."&message_type=success&tab=question"); //&question is used for the tabber to enable the correct tab
        } else {
            eF_redirect("".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&from_unit=".$_GET['from_unit']."&add_question=1&difficulty=".$question_values['difficulty']."&content_ID=".$question_values['content_ID']."&question_type=".$_GET['question_type']."&message=".rawurlencode(_SUCCESFULLYADDEDQUESTION)."&message_type=success");
        }
    }
}
if ($skillgap_tests && $_GET['edit_question']) {
    // Get the text of the question
    $smarty -> assign("T_QUESTION_TEXT", strip_tags($currentQuestion -> question['text']));
    $skills = eF_getTableData("module_hcd_skills LEFT OUTER JOIN questions_to_skills ON skill_ID = skills_ID AND questions_ID = ".$currentQuestion -> question['id'], "distinct skill_ID, description, relevance, questions_ID", "");
    if ($currentQuestion -> question['lessons_ID'] != 0) {
        $suggest_skills = array(array('image' => '16x16/examples.png', 'text' => _SUGGESTSKILLSACCORDINGTOLESSONS, 'title' => _SUGGESTSKILLSACCORDINGTOLESSONS, 'href' => 'javascript:void(0)', 'onClick' => 'checkSuggestedSkills(this)', 'id' => 'suggestedSkillsImage'));
        $smarty -> assign('T_SUGGEST_QUESTION_SKILLS',$suggest_skills);
    }
    $smarty -> assign('T_QUESTION_SKILLS', $skills);
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
$smarty -> assign('T_QUESTION_FORM', $renderer -> toArray());
$smarty -> assign('T_QUESTION_FORM_SIMPLE', $form -> toArray());
//Filemanager settings and inclusion
if ($currentUser -> getType() == "administrator") {
    $basedir = G_ADMINPATH;
} else {
    $basedir = $currentLesson -> getDirectory();
}
if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
    $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
} else {
    $options = array('delete' => false, 'edit' => false, 'share' => false, 'upload' => false, 'create_folder' => false, 'zip' => false, 'lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
}
/** Get the suggested list in a form that javascript can then understand **/
if (isset($_GET['postAjaxRequest']) && isset($_GET['get_proposed_skills'])) {
    $question_lesson = eF_getTableData("questions", "lessons_ID", "id = ".$_GET['edit_question']);
    if (!empty($question_lesson) && $question_lesson[0]['lessons_ID'] != 0) {
        $lesson = new EfrontLesson($question_lesson[0]['lessons_ID']);
        $skills_to_propose = array();
        // If the lesson belongs only to courses, get all skills of its courses
        if ($lesson -> lesson['course_only']) {
            $lesson_belonging_courses = $lesson -> getCourses(true);
            foreach($lesson_belonging_courses as $course) {
                $course_skills = $course ->getSkills(true);
                foreach ($course_skills as $skillID => $skill) {
                    $skills_to_propose[] = $skillID;
                }
            }
        } else {
            // Else get only its own skills
            $lesson_skills = $lesson ->getSkills(true);
            foreach ($lesson_skills as $skillID => $skill) {
                $skills_to_propose[] = $skillID;
            }
        }
        if (!empty($skills_to_propose)) {
            echo implode(" ", $skills_to_propose);
        } else {
            header("HTTP/1.0 500 ");
        }
        exit;
    } else {
        header("HTTP/1.0 500 ");
        exit;
    }
}
/** Post skill to questions - Ajax skill **/
if (isset($_GET['postAjaxRequest']) && isset($_GET['skill'])) {
    if ($_GET['insert'] == "true") {
        eF_insertTableData("questions_to_skills", array("skills_ID" => $_GET['skill'], "questions_ID" => $_GET['edit_question'], "relevance" => $_GET['relevance']));
    } else if ($_GET['insert'] == "update") {
        eF_updateTableData("questions_to_skills", array("relevance" => $_GET['relevance']), "skills_ID = '". $_GET['skill'] . "' AND questions_ID = '" . $_GET['edit_question'] . "'");
    } else if ($_GET['insert'] == "false") {
        eF_deleteTableData("questions_to_skills", "skills_ID = '" . $_GET['skill']. "' AND questions_ID = '" . $_GET['edit_question'] . "'");
    } else if (isset($_GET['addAll'])) {
        // Different management if a users' filter is set or not
        if ($_GET['filter']) {
            $existing_question_skills_r = eF_getTableData("questions_to_skills", "*", "questions_ID = '".$_GET['edit_question']."'");
            if (!empty($existing_question_skills_r)) {
                $existing_question_skills_r = eF_filterData($existing_question_skills_r,$_GET['filter']);
                // Reversing the table
                $existing_question_skills['skills_ID'] = array();
                foreach ($existing_question_skills_r as $question_skill) {
                    $existing_question_skills['skills_ID'][] = $question_skill['skills_ID'];
                }
            } else {
                $existing_question_skills = array();
            }
            $all_skills_r = eF_getTableData("module_hcd_skills", "*", "");
            $all_skills_r = eF_filterData($all_skills_r,$_GET['filter']);
            // Reversing the table
            $all_skills['skill_ID'] = array();
            foreach ($all_skills_r as $question_skill) {
                $all_skills['skill_ID'][] = $question_skill['skill_ID'];
            }
        } else {
            $existing_question_skills = eF_getTableDataFlat("questions_to_skills", "skills_ID", "questions_ID = '".$_GET['edit_question']."'");
            $all_skills = eF_getTableDataFlat("module_hcd_skills", "skill_ID", "");
        }
        if (empty($existing_question_skills)) {
            $non_existing_skills = $all_skills['skill_ID'];
        } else {
            $non_existing_skills = array_diff($all_skills['skill_ID'], $existing_question_skills['skills_ID']);
        }
        foreach ($non_existing_skills as $skill_to_add) {
            if (!$all_skills_to_add) {
                $all_skills_to_add = "('".$_GET['edit_question'] . "','". $skill_to_add . "' , '2')";
            } else {
                $all_skills_to_add .= ",('".$_GET['edit_question'] . "','". $skill_to_add. "' , '2')";
            }
        }
        if (isset($all_skills_to_add)) {
            eF_execute("INSERT INTO questions_to_skills (questions_id, skills_ID, relevance) VALUES " . $all_skills_to_add);
        }
    } else if (isset($_GET['removeAll'])) {
        if ($_GET['filter']) {
            $all_related_skills = eF_getTableData("questions_to_skills JOIN module_hcd_skills ON skills_ID = skill_ID","skills_ID, description", "questions_ID = '".$_GET['edit_question'] . "'");
            if(!empty($all_related_skills)) {
                $all_related_skills = eF_filterData($all_related_skills,$_GET['filter']);
                $skills_to_remove = array();
                foreach ($all_related_skills as $skill) {
                    $skills_to_remove[] = $skill['skills_ID'];
                }
                if (!empty($skills_to_remove)) {
                    eF_deleteTableData("questions_to_skills", "questions_ID = '".$_GET['edit_question'] . "' AND skills_ID IN ('".implode("','",$skills_to_remove)."')");
                }
            }
        } else {
            // Remove all
            eF_deleteTableData("questions_to_skills", "questions_ID = '".$_GET['edit_question'] . "'");
        }
    }
    exit;
}
