<?php
$max_unit_length = 50;

    // Basic check to distinguish between skillgap and normal lesson tests
    if ($currentUser -> getType() == "administrator") {
        if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $skillgap_tests = 1;
        $smarty -> assign("T_SKILLGAP_TEST", 1);
    }

    // Delete all questions from the posted form
    if (isset($_POST['selected_action']) && $_POST['selected_action'] == 'delete') {          //Mass deletion of questions
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
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
            $message      = implode("<br/>", $messageString);
            $message_type = 'failure';
        }
    }

    // Optionally ajaxed request - if not ajaxed then it should show the tests list
    if( isset($_GET['delete_solved_test']) && eF_checkParameter($_GET['delete_solved_test'], 'id')) {

        if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            exit;
        } 
        // Remove a solved test from the users_to_skillgap list
        eF_deleteTableData("completed_tests", "id = " . $_GET['delete_solved_test']);
        eF_updateTableData("users_to_skillgap_tests" , array("solved" => 0), "tests_id = " . $_GET['test_id']. " AND users_login = '".$_GET['users_login']."'");
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
        $result = eF_getTableData("tests ", "*", "lessons_ID = 0");
        foreach ($result as $value) {
            $availableTests[] = $value['id'];
        }
        $smarty -> assign("T_SET_CONTENT", false);
    }

    // Test managements: delete, insert, update
    if (isset($_GET['delete_test']) && eF_checkParameter($_GET['delete_test'], 'id') && in_array($_GET['delete_test'], $availableTests)) {
        try {
            if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
                throw new EfrontUserException(_UNAUTHORIZEDACCESS, EfrontUserException::RESTRICTED_USER_TYPE);
            }
            
	        if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
                throw new EfrontUserException(_UNAUTHORIZEDACCESS, EfrontUserException::RESTRICTED_USER_TYPE);
	        } 
            $currentTest = new EfrontTest($_GET['delete_test']);
            $currentTest -> delete();
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    } elseif (isset($_GET['publish_test']) && eF_checkParameter($_GET['publish_test'], 'id') && in_array($_GET['publish_test'], $availableTests)) {
        try {
            if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
                throw new EfrontUserException(_UNAUTHORIZEDACCESS, EfrontUserException::RESTRICTED_USER_TYPE);
            }
            if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
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
    } elseif (isset($_GET['add_test']) || (isset($_GET['edit_test']) && eF_checkParameter($_GET['edit_test'], 'id') && in_array($_GET['edit_test'], $availableTests)) || (isset($_GET['edit_unit']) && eF_checkParameter($_GET['edit_unit'], 'id'))) {
        if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            exit;
        }         
        
        if (isset($_GET['edit_unit'])) {
            $currentTest        = new EfrontTest($_GET['edit_unit'], true);
            $_GET['edit_test'] = $currentTest -> test['id'];
        }

        if (isset($_GET['postAjaxRequest_tests_insert'])) {
            $file_id = urldecode($_GET['file_id']);
            $file_insert = new EfrontFile($file_id);

                if (strpos($file_insert['mime_type'] , "image") !== false) {
                    $img_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                    echo "<img src=\"".$img_return."\" border=0 />";
                    exit;
                } elseif (strpos($file_insert['mime_type'] , "flash") !== false) {
                    $flash_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                    if ($_GET['editor_mode'] == "true") {
                        echo '<img width="400" height="400" src="editor/tiny_mce/themes/advanced/images/spacer.gif"  title="'.$flash_return.'" alt="'.$flash_return.'" class="mceItemFlash" />';
                        exit;
                    } else {
                        echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="400" height="400">
                        <param name="src" value="'.$flash_return.'" />
                        <param name="width" value="400" />
                        <param name="height" value="400" />
                        <embed type="application/x-shockwave-flash" src="'.$flash_return.'" width="400" height="400"></embed>
                        </object>';
                        exit;
                    }
                } else {
                    echo "<a href=view_file.php?action=download&file=".$file_id.">".$file_insert['physical_name']."</a>";
                    exit;

                }
        }
        $smarty -> assign('T_BASENAME_PHPSELF', basename($_SERVER['PHP_SELF']));
        if (isset($currentContent)) {
            try {
                $optionsArray = $currentContent -> toHTMLSelectOptions();    //Get the units as an array of formated strings, that can be used to form an HTML select list
                isset($_GET['edit_test']) ? $postTarget = basename($_SERVER['PHP_SELF'])."?ctg=tests&edit_test=".$_GET['edit_test'] : $postTarget = basename($_SERVER['PHP_SELF'])."?ctg=tests&add_test=1&from_unit=".$_GET['from_unit'];
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        } else {
            isset($_GET['edit_test']) ? $postTarget = basename($_SERVER['PHP_SELF'])."?ctg=tests&edit_test=".$_GET['edit_test'] : $postTarget = basename($_SERVER['PHP_SELF'])."?ctg=tests&add_test=1";
        }
        $load_editor = true;

        $form = new HTML_QuickForm("question_form", "post", $postTarget, "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter


        //Check if the test's parent unit exists or is inactive
        if (isset($_GET['edit_test'])) {
            $currentTest = new EfrontTest($_GET['edit_test']);
            if (isset($currentContent)) {
                $testUnit    = $currentTest -> getUnit();
                if ($testUnit && !in_array($testUnit['parent_content_ID'], array_keys($optionsArray))) {
                    $result = eF_getTableData("content", "name", "id=".$testUnit['parent_content_ID']);
                    //If it doesn't exist, update the parent unit to root
                    if (sizeof($result) == 0) {
                        $testUnit['parent_content_ID'] = 0;
                        //$testUnit -> persist();
                    } else {
                        $optionsArray[$testUnit['parent_content_ID']] = $result[0]['name'];            //Append the inactive unit's name to the select box (which will be freezed below)
                    }
                }
            }
        }
        if (isset($currentContent)) {
            $select_units = & HTML_QuickForm :: createElement('select', 'parent_content', _UNITPARENT, null, 'class = "inputSelect"');
            $select_units -> addOption(_ROOTUNIT, 0);
            $select_units -> loadArray($optionsArray);
            $form -> addElement($select_units);

            $form -> addRule('parent_content', _THEFIELD.' '._UNITPARENT.' '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('parent_content', _INVALIDID, 'numeric');
        }
        $form -> addElement('text', 'name', null, 'class = "inputText"');
        $form -> addRule('name', _THEFIELD.' "'._NAME.'" '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('name', _INVALIDFIELDDATAFORFIELD.' "'._NAME.'"', 'checkParameter', 'text');

        $form -> addElement('text', 'duration', null, 'size = "5"');
        $form -> addRule('duration', _THEFIELD.' "'._DURATIONINMINUTES.'" '._MUSTBENUMERIC, 'numeric', null, client);

        $form -> addElement('text', 'redoable', null, 'size = "5"');
        $form -> addRule('redoable', _THEFIELD.' "'._REDOABLE.'" '._MUSTBENUMERIC, 'numeric', null, client);
        $form -> addElement('text', 'mastery_score', _MASTERYSCORE, 'size = "5"');
        $form -> addRule('mastery_score', _RATEMUSTBEBETWEEN0100, 'callback', create_function('&$a', 'return ($a >= 0 && $a <= 100);'));    //The score must be between 0 and 100
        $form -> addRule('mastery_score', _THEFIELD.' "'._MASTERYSCORE.'" '._MUSTBENUMERIC, 'numeric', null, client);
        $form -> addElement('text', 'random_pool', _RANDOMPOOL, 'size = "5" onChange = "ajaxSetRandomPool(\''.$id.'\', this);"');
        $form -> addRule('random_pool', _THEFIELD.' "'._RANDOMPOOL.'" '._MUSTBENUMERIC, 'numeric', null, client);

        if ($skillgap_tests) {

            $form -> addElement('text', 'general_threshold', null, 'class = "inputText"');
            $form->registerRule('decimal2digits','regex','/^\d{1,2}(\.\d{1,2})?$/');
            $form->addRule('general_threshold',_INVALIDFIELDDATAFORFIELD.' "'._GENERALTHRESHOLD.'": '. _NUMBERFROM000TO9999REQUIRED,'decimal2digits');
            // Set default value and if it is defined it will be overwritten - @hardcoded value 50 - could be set by admin in general
            $form -> setDefaults(array('general_threshold'    => "50.00"));

            $form -> addElement('advcheckbox', 'assign_to_new',          null, null, null, array(0, 1));
            $form -> addElement('advcheckbox', 'automatic_assignment',          null, null, null, array(0, 1));
        }
        $form -> addElement('advcheckbox', 'onebyone',          null, null, null, array(0, 1));
        $form -> addElement('advcheckbox', 'given_answers',     null, null, null, array(0, 1));
        $form -> addElement('advcheckbox', 'answers',           null, null, null, array(0, 1));
        $form -> addElement('advcheckbox', 'shuffle_answers',   null, null, null, array(0, 1));
        $form -> addElement('advcheckbox', 'shuffle_questions', null, null, null, array(0, 1));
        $form -> addElement('advcheckbox', 'pause_test',        null, null, null, array(0, 1));
        $form -> addElement('advcheckbox', 'publish',           null, null, null, array(0, 1));
        $form -> addElement('advcheckbox', 'display_list',      null, null, null, array(0, 1));

        $form -> addElement('advcheckbox', 'display_weights',   null, null, null, array(0, 1));
        $form -> addElement('textarea',    'description',       null, 'id="editor_test_data" class = "inputTestTextarea mceEditor" style = "width:100%;height:16em;"');

        if (isset($currentContent)) {
            isset($_GET['from_unit']) && eF_checkParameter($_GET['from_unit'], 'id') ? $selectedUnit = $_GET['from_unit'] : $selectedUnit = 0;
            $selectedUnit ? $units = $currentContent -> getNodeChildren($selectedUnit) : $units = $currentContent -> tree;
            foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($units)), array('id', 'name')) as $key => $value) {
                $key == 'id' ? $ids[] = $value : $names[] = $value;
            }
            $unitNames    = array_combine($ids, $names);
            $unitNames[0] = _NONE;
            $result       = eF_getTableData("questions", "*", "lessons_ID=".$currentLesson -> lesson['id'], "content_ID ASC");     //Retrieve all questions that belong to this unit or its subunits
        } else {
            // Skillgap questions data - with the lesson name
            $result = eF_getTableData("questions LEFT OUTER JOIN lessons ON lessons.id = lessons_ID", "questions.*, lessons.name", "");
        }
        foreach ($result as $value) {
            $questions[$value['id']] = $value;
        }

        if (isset($_GET['add_test'])) {
            $form -> setDefaults(array('given_answers' => 1,
                                       'answers'       => 1,
                                       'publish'       => 1));
        } else if (isset($_GET['edit_test'])) {
            $form -> freeze('parent_content');
            $form -> setDefaults(array('parent_content'    => $testUnit['parent_content_ID'],
                                       'name'              => $currentTest -> test['name'],
                                       'duration'          => $currentTest -> options['duration'] ? round($currentTest -> options['duration'] / 60) : '',   //Duration is displayed in minutes, but is stored in seconds
                                       'redoable'          => $currentTest -> options['redoable'] ? $currentTest -> options['redoable'] : '',
                                       'onebyone'          => $currentTest -> options['onebyone'],
                                       'given_answers'     => $currentTest -> options['given_answers'],
                                       'answers'           => $currentTest -> options['answers'],
                                       'shuffle_answers'   => $currentTest -> options['shuffle_answers'],
                                       'shuffle_questions' => $currentTest -> options['shuffle_questions'],
                                       'random_pool'       => $currentTest -> options['random_pool'],
                                       'pause_test'        => $currentTest -> options['pause_test'],
                                       'display_list'      => $currentTest -> options['display_list'],
                                       'publish'           => $currentTest -> test['publish'],
                                       'display_weights'   => $currentTest -> options['display_weights'],
                                       'description'       => $currentTest -> test['description'],
                                       'mastery_score'     => $currentTest -> test['mastery_score']));


            if ($skillgap_tests) {
                $form -> setDefaults(array('general_threshold'      => $currentTest -> options['general_threshold'],
                                           'assign_to_new'          => $currentTest -> options['assign_to_new'],
                                           'automatic_assignment'   => $currentTest -> options['automatic_assignment']));
            }

            $smarty -> assign("T_CURRENT_TEST", $currentTest -> test);

            $difficulties  = array('low' => array(), 'medium' => array(), 'high' => array());

            if (!skillgap_tests) {        
                $testQuestions = $currentTest -> getQuestions();
            } else {
                // The getQuestions does not work 
                $testQuestions = $currentTest -> getQuestions();
                
                //$testQuestions = $currentTest -> getSkillgapQuestions();
            }

            foreach ($questions as $id => $question) {
                $difficulties[$question['difficulty']][] = $id;         //Gather the number of questions per difficulty
            }

            $poolForm = new HTML_QuickForm("question_form", "post", null);
            $poolForm -> addElement('select', 'random_low', null, range(0, sizeof($difficulties['low'])), 'id = "select_low"');
            $poolForm -> addElement('select', 'random_medium', null, range(0, sizeof($difficulties['medium'])), 'id = "select_medium"');
            $poolForm -> addElement('select', 'random_high', null, range(0, sizeof($difficulties['high'])), 'id = "select_high"');
            $poolForm -> setDefaults(array('random_low'    => sizeof($difficulties['low']),
                                           'random_medium' => sizeof($difficulties['medium']),
                                           'random_high'   => sizeof($difficulties['high'])));
            $poolForm -> addElement('button', 'randomize', _RANDOMIZE, 'class = "flatButton" onclick = "randomize(this)"');
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $poolForm -> accept($renderer);
            $smarty -> assign('T_RANDOM_FORM', $renderer -> toArray());

            if (isset($_GET['postAjaxRequest'])) {

                // Ajax assignment of a skill gap test to a user
                if (isset($_GET['login'])) {

                    if ($_GET['insert'] == "true") {
                        eF_insertTableData("users_to_skillgap_tests", array( "users_LOGIN" => $_GET['login'], "tests_ID" => $_GET['edit_test']));
                    } else if ($_GET['insert'] == "false") {
                        eF_deleteTableData("users_to_skillgap_tests", "users_LOGIN = '". $_GET['login'] ."' AND tests_ID = '" .$_GET['edit_test'] . "'");

                    } else if (isset($_GET['addAll'])) {
                        $existing_test_users = eF_getTableDataFlat("users_to_skillgap_tests", "users_LOGIN", "tests_ID = '".$_GET['edit_test']."'");

                        $all_users = eF_getTableDataFlat("users", "login", "user_type = 'student'");
                        if (empty($existing_test_users)) {
                            $non_existing_users = $all_users['login'];
                        } else {
                            $non_existing_users = array_diff($all_users['login'], $existing_test_users['users_LOGIN']);
                        }

                        foreach ($non_existing_users as $user_to_add) {
                            if (!$all_users_to_add) {
                                $all_users_to_add = "('".$_GET['edit_test'] . "','". $user_to_add . "' , '0')";
                            } else {
                                $all_users_to_add .= ",('".$_GET['edit_test'] . "','". $user_to_add. "' , '0')";
                            }
                        }

                        if (isset($all_users_to_add)) {
                            eF_execute("INSERT INTO users_to_skillgap_tests (tests_ID, users_LOGIN, solved) VALUES " . $all_users_to_add);
                        }
                    } else if (isset($_GET['removeAll'])) {
                        eF_deleteTableData("users_to_skillgap_tests", "tests_ID = '".$_GET['edit_test'] . "'");
                    }

                } else {
                    try {
                        $nonTestQuestions = $currentTest -> getNonQuestions();
                        if (isset($_GET['question']) && eF_checkParameter($_GET['question'], 'id')) {
                            if ($_GET['remove'] && in_array($_GET['question'], array_keys($testQuestions))) {                    //The user has the project, so remove him
                                $currentTest -> removeQuestions(array($_GET['question']));
                            } else {                     //The user doesn't have the project, so add him
                                $currentTest -> addQuestions(array($_GET['question'] => $_GET['weight']));
                            }
                        } else if (isset($_GET['addAll'])) {
                            // Do not add development questions to skill gap tests
                            if ($skillgap_tests) {
                                foreach($nonTestQuestions as $qid => $nonTestQuestion) {
                                    if ($nonTestQuestion['type'] == 'raw_text') {                                       
                                        unset($nonTestQuestions[$qid]);                                
                                    }
                                }
                            }
                            
                            $currentTest -> addQuestions(array_combine(array_keys($nonTestQuestions), array_fill(0, sizeof($nonTestQuestions), 1)));
                        } else if (isset($_GET['removeAll'])) {
                            $currentTest -> removeQuestions(false);
                        }
                    } catch (Exception $e) {
                        header("HTTP/1.0 500 ");
                        echo $e -> getMessage().' ('.$e -> getCode().')';
                    }
                }
                exit;
            }
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'randomize') {
                try {
                    is_numeric($_GET['low'])    ? $low    = $_GET['low']    : $low    = 0;
                    is_numeric($_GET['medium']) ? $medium = $_GET['medium'] : $medium = 0;
                    is_numeric($_GET['high'])   ? $high   = $_GET['high']   : $high   = 0;

                    $low    > sizeof($difficulties['low'])    ? $low    = sizeof($difficulties['low'])    : null;
                    $medium > sizeof($difficulties['medium']) ? $medium = sizeof($difficulties['medium']) : null;
                    $high   > sizeof($difficulties['high'])   ? $high   = sizeof($difficulties['high'])   : null;

                    shuffle($difficulties['low']);
                    shuffle($difficulties['medium']);
                    shuffle($difficulties['high']);

                    $lowQuestions    = array_slice($difficulties['low'], 0, $low);
                    $mediumQuestions = array_slice($difficulties['medium'], 0, $medium);
                    $highQuestions   = array_slice($difficulties['high'], 0, $high);

                    $currentTest -> removeQuestions(false);
                    $currentTest -> addQuestions(array_combine($lowQuestions, array_fill(0, sizeof($lowQuestions), 1)));
                    $currentTest -> addQuestions(array_combine($mediumQuestions, array_fill(0, sizeof($mediumQuestions), 1)));
                    $currentTest -> addQuestions(array_combine($highQuestions, array_fill(0, sizeof($highQuestions), 1)));
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 'set_random_pool') {
                try {
                    is_numeric($_GET['random_questions'])    ? $random_questions    = $_GET['random_questions']    : $random_questions    = 0;
                    $currentTest -> options['random_pool'] = $random_questions;
                    $currentTest -> persist();
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            }            
            //jax=&='+el.value
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'questionsTable') {                
                // If no lesson then define the current lesson name => _SKILLGAPTESTS (used for correct filtering)
                foreach ($questions as $qid => $question) {
                    $questions[$qid]['text']           = strip_tags($question['text']);        //If we ommit this line, then the questions list is html formatted, images are displayed etc, which is *not* the intended behaviour
                    $questions[$qid]['parent_name']    = $unitNames[$question['content_ID']];
                    $questions[$qid]['weight']         = $testQuestions[$qid]['weight'];
                    $questions[$qid]['partof']         = 0;                
                    if ($question['lessons_ID'] == 0) {
                        $questions[$qid]['name'] = _SKILLGAPTESTS;
                    }
                    if ($skillgap_tests && $question['type'] == 'raw_text') {
                        unset($questions[$qid]);    
                    }
                }

                foreach ($testQuestions as $testQuestion) {                                     //Set to selected the questions that the test includes, along with their weights
                    $form -> setDefaults(array('questions['.$testQuestion['id'].']'       => 1,
                                               'question_weight['.$testQuestion['id'].']' => $testQuestion['weight']));
                    $questions[$testQuestion['id']]['partof'] = 1;
                }
                
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'text';
                }
                $questions = eF_multiSort($questions, $sort, $order);
                $smarty -> assign("T_QUESTIONS_SIZE", sizeof($questions));
                if (isset($_GET['filter'])) {
                    $questions = eF_filterData($questions, $_GET['filter']);
                }

                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $questions = array_slice($questions, $offset, $limit, true);
                }

                foreach ($questions as $id => $question) {
                    $form -> addElement("checkbox", "questions[".$id."]", null, null, 'id = "checked_'.$id.'" onclick = "ajaxPost(\''.$id.'\', this, \'questionsTable\');"');
                    $form -> addElement('select', 'question_weight['.$id.']', null, array_combine(range(1,10), range(1,10)), 'id = "weight_'.$id.'" onchange = "$(\'checked_'.$id.'\').checked=true;ajaxPost(\''.$id.'\', this);"');                    
                }
                $smarty -> assign('T_UNIT_QUESTIONS', $questions);
                $smarty -> assign("T_QUESTIONTYPESTRANSLATIONS", Question :: $questionTypes);//pr($question_types_translations);
                $smarty -> assign("T_QUESTIONDIFFICULTYTRANSLATIONS", array('low' => _LOW, 'medium' => _MEDIUM, 'high' => _HIGH));//pr($question_types_translations);
                $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
                $form -> accept($renderer);
                $smarty -> assign('T_TEST_FORM', $renderer -> toArray());
                $smarty -> display( $currentUser->getType() . '.tpl');
                exit;
            }

        }

        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            $form -> freeze();
        } else {
            if (isset($_GET['edit_test'])) {
                $form -> addElement('submit', 'submit_test', _SAVETEST, 'class = "flatButton"');
                $form -> addElement('submit', 'submit_test_new', _SAVEASNEWTEST, 'class = "flatButton"');
/*
                $form -> addElement('image', 'submit_stay', 'images/32x32/check.png', 'alt = "'._SUBMITANDCONTINUEEDITING.'" title = "'._SUBMITANDCONTINUEEDITING.'"');
                $form -> addElement('image', 'submit_return', 'images/32x32/check_return.png', 'alt = "'._SUBMITANDRETURN.'" title = "'._SUBMITANDRETURN.'"');
                $form -> addElement('image', 'submit_new_stay', 'images/32x32/add.png', 'alt = "'._SUBMITASNEWANDCONTINUEEDITING.'" title = "'._SUBMITASNEWANDCONTINUEEDITING.'"');
                $form -> addElement('image', 'submit_new_return', 'images/32x32/add_return.png', 'alt = "'._SUBMITASNEWANDRETURN.'" title = "'._SUBMITASNEWANDRETURN.'"');
*/
            } else {
                $form -> addElement('submit', 'submit_test', _SAVETESTANDADDQUESTIONS, 'class = "flatButton"');
/*
                $form -> addElement('image', 'submit_stay', 'images/32x32/check.png', 'alt = "'._SUBMITANDCONTINUEEDITING.'" title = "'._SUBMITANDCONTINUEEDITING.'"');
                $form -> addElement('image', 'submit_return', 'images/32x32/check_return.png', 'alt = "'._SUBMITANDRETURN.'" title = "'._SUBMITANDRETURN.'"');
*/
            }

            if ($form -> isSubmitted() && $form -> validate()) {
                $values = eF_addSlashes($form -> exportValues(), false);

//                if (isset($_GET['edit_test']) && !isset($_POST['submit_new_stay_x']) && !isset($_POST['submit_new_return_x'])) {                                                            //This means we are updating an existing test; Assign existing values to form elements
                if (isset($_GET['edit_test']) && !isset($values['submit_test_new'])) {                                                            //This means we are updating an existing test; Assign existing values to form elements
                    try {
                        if (isset($currentContent)) {
                            $testUnit['name']              = $values['name'];
                            $testUnit['parent_content_ID'] = $values['parent_content'];
                            $testUnit -> persist();
                        }
                        $testOptions = array('duration'          => $values['duration'] * 60,              //Duration is displayed in minutes, but is stored in seconds
                                             'redoable'          => $values['redoable'] ? $values['redoable'] : 0,
                                             'onebyone'          => $values['onebyone'],
                                             'given_answers'     => $values['given_answers'],
                                             'answers'           => $values['answers'],
                                             'shuffle_answers'   => $values['shuffle_answers'],
                                             'shuffle_questions' => $values['shuffle_questions'],
                                             'random_pool'       => (integer)$values['random_pool'],
                                             'pause_test'        => $values['pause_test'],
                                             'display_list'      => $values['display_list'],
                                             'display_weights'   => $values['display_weights']);
                        $currentTest -> test['publish']       = $values['publish'];
                        $currentTest -> test['description']   = $values['description'];
                        $currentTest -> test['mastery_score'] = $values['mastery_score'] ? $values['mastery_score'] : 0;
                        $currentTest -> test['name']          = $values['name'];

                        // Add the skillgap specific options
                        if ($skillgap_tests) {
                            $testOptions['general_threshold'] = $values['general_threshold'];
                            $testOptions['assign_to_new'] = $values['assign_to_new'];
                            $testOptions['automatic_assignment'] = $values['automatic_assignment'];
                        }

                        $currentTest -> options = array_merge($currentTest -> options, $testOptions);
                        $currentTest -> persist();

                        header("location:".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&message=".urlencode(_SUCCESFULLYMODIFIEDTEST)."&message_type=success");
/*
                        if (isset($_POST['submit_return_x'])) {
                            header("location:".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&message=".urlencode(_SUCCESFULLYMODIFIEDTEST)."&message_type=success");
                        }
*/
                    } catch (Exception $e) {
                        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                        $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                        $message_type = 'failure';
                    }
                } else {
                    if (isset($currentContent)) {
                                $contentFields = array('data'              => '',
                                                       'name'              => $values['name'],
                                                       'lessons_ID'        => $currentLesson -> lesson['id'],
                                                       'ctg_type'          => "tests",
                                                       'active'            => 1,
                                                       'timestamp'         => time(),
                                                       'parent_content_ID' => $values['parent_content']);
                    }

                    $testOptions = array('duration'          => ($values['duration'] * 60),  //Duration is displayed in minutes, but is stored in seconds
                                         'redoable'          => $values['redoable'] ? $values['redoable'] : 0,
                                         'onebyone'          => $values['onebyone'],
                                         'given_answers'     => $values['given_answers'],
                                         'answers'           => $values['answers'],
                                         'shuffle_questions' => $values['shuffle_questions'],
                                         'shuffle_answers'   => $values['shuffle_answers'],
                                         'random_pool'       => $values['random_pool'],
                                         'pause_test'        => $values['pause_test'],
                                         'display_list'      => $values['display_list'],
                                         'display_weights'   => $values['display_weights']);
                    // Add the skillgap specific options
                    if ($skillgap_tests) {
                        $testOptions['general_threshold'] = $values['general_threshold'];
                        $testOptions['assign_to_new'] = $values['assign_to_new'];
                        $testOptions['automatic_assignment'] = $values['automatic_assignment'];
                    }
                    
                    $testFields = array('active'            => 1,
                                        'lessons_ID'        => (isset($currentLesson -> lesson['id']))?$currentLesson -> lesson['id']:0,
                                        'content_ID'        => $test_content_ID,
                                        'description'       => $values['description'],
                                        'options'           => serialize($testOptions),
                                        'name'              => $values['name'],
                                        'publish'           => $values['publish'],
                                        'mastery_score'     => $values['mastery_score'] ? $values['mastery_score'] : 0);
                    

                    
                    try {
                        if (isset($currentContent)) {
                            $newUnit = $currentContent -> insertNode($contentFields);
                            $newTest = EfrontTest :: createTest($newUnit, $testFields);
                        } else {
                            $newTest = EfrontTest :: createTest(false, $testFields);
                        }
                        
                        
                        // If the new test comes from an existing one we should also copy its questions...
                        if ($_GET['edit_test']) {
                            $testQuestions = $currentTest ->getQuestions();
                            $newTest -> addQuestions($testQuestions);                          
                            // ... and its users if it is a skillgap test
                            if ($skillgap_tests) {
                                $testUsers = eF_getTableDataFlat("users_to_skillgap_tests", "users_LOGIN", "tests_ID = '".$_GET['edit_test']."'");
                                if (sizeof ($testUsers)> 0) {
                                    $insertString = "('" . $newTest->test['id'] . "', '" . implode("'),('" . $newTest -> test['id'] . "', '", $testUsers['users_LOGIN']) . "')";
                                    eF_execute("INSERT INTO users_to_skillgap_tests (tests_ID,users_LOGIN) VALUES $insertString");
                                }
                            }
                        }                        
                        header("location:".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&edit_test=".($newTest -> test['id'])."&tab=question&message=".urlencode(_SUCCESFULLYADDEDTEST)."&message_type=success");
/*
                        if (isset($_POST['submit_new_stay_x']) || isset($_POST['submit_stay_x'])) {
                            header("location:".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&edit_test=".($newTest -> test['id'])."&tab=questions&message=".urlencode(_SUCCESFULLYADDEDTEST)."&message_type=success");
                        } else {
                            header("location:".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&message=".urlencode(_SUCCESFULLYADDEDTEST)."&message_type=success");
                        }
*/
                    } catch (Exception $e) {
                        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                        $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                        $message_type = 'failure';
                    }
                }
            }
        }
        $loadScripts[] = 'scriptaculous/effects';

        // Code to find users to who a skillgap tests has been assigned
        if ($skillgap_tests) {

           // AJAX CODE TO RELOAD SKILL-GAP TEST USERS
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'testUsersTable') {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }


                $testUsers = eF_getTableData("users LEFT OUTER JOIN users_to_skillgap_tests ON login = users_login AND tests_ID = '".$_GET['edit_test']."'", "distinct login, name,surname,tests_ID,solved", "users.user_type = 'student'");
                $test_info = eF_getTableData("completed_tests", "id, users_LOGIN", "tests_ID = " . $_GET['edit_test']);

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $testUsers = eF_multiSort($testUsers, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $testUsers = eF_filterData($testUsers, $_GET['filter']);
                }

                $smarty -> assign("T_PROPOSED_LESSONS_SIZE", sizeof($testUsers));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $testUsers = array_slice($testUsers, $offset, $limit);
                }

                // Find the completed test for each user
                foreach ($testUsers as $uid => $user) {
                    foreach($test_info as $info) {
                        if ($info['users_LOGIN']  == $user['login']) {
                            $testUsers[$uid]['completed_test_id'] = $info['id'];
                        }
                    }
                }
                $smarty -> assign('T_ALL_USERS', $testUsers);
                $smarty -> display('administrator.tpl');
                exit;
            } else {

                $testUsers = eF_getTableData("users LEFT OUTER JOIN users_to_skillgap_tests ON login = users_login AND tests_ID = '".$_GET['edit_test']."'", "distinct login, name,surname,tests_ID,solved", "users.user_type = 'student'");
                $test_info = eF_getTableData("completed_tests", "id, users_LOGIN", "tests_ID = " . $_GET['edit_test']);

                // Find the completed test for each user
                foreach ($testUsers as $uid => $user) {
                    foreach($test_info as $info) {
                        if ($info['users_LOGIN']  == $user['login']) {
                            $testUsers[$uid]['completed_test_id'] = $info['id'];
                        }
                    }
                }

                $smarty -> assign('T_ALL_USERS', $testUsers);
            }
        }

        try {
            if ($currentUser -> getType() == "administrator") {
                $basedir    = G_ADMINPATH;
            } else {
                $basedir    = $currentLesson -> getDirectory();
            }
            $filesystem = new FileSystemTree($basedir);
            $filesystem -> handleAjaxActions($currentUser);

            if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
                $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
            } else {
                $options = array('delete' => false, 'edit' => false, 'share' => false, 'upload' => false, 'create_folder' => false, 'zip' => false, 'lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
            }

            if (isset($_GET['edit_test'])) {
                $url = basename($_SERVER['PHP_SELF']).'?ctg=tests&edit_test='.$_GET['edit_test'];
            }else{
                $url = basename($_SERVER['PHP_SELF']).'?ctg=tests&add_test=1';
            }

            if (isset($_GET['ajax'])){
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }

                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                }
                isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                $extraFileTools = array(array('image' => 'images/16x16/arrow_right_green.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
                echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, $extraFileTools, '', '', '', false);
                exit;
            }
            $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, $extraFileTools, '', '', '', false));
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }



        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);

        $smarty -> assign('T_TEST_FORM', $renderer -> toArray());
    } elseif ((isset($_GET['show_test']) && eF_checkParameter($_GET['show_test'], 'id') && in_array($_GET['show_test'], $availableTests)) || (isset($_GET['view_unit']) && in_array($_GET['view_unit'], array_keys($availableTests)))) {
        try {
            if (isset($_GET['view_unit'])) {
                $showTest = new EfrontTest($_GET['view_unit'], true);
                $smarty -> assign("T_UNIT",          $currentUnit);
                $smarty -> assign("T_NEXT_UNIT",     $currentContent -> getNextNode($currentUnit, $visitableIterator));
                $smarty -> assign("T_PREVIOUS_UNIT", $currentContent -> getPreviousNode($currentUnit, $visitableIterator));        //Next and previous units are needed for navigation buttons
                $smarty -> assign("T_PARENT_LIST",   $currentContent -> getNodeAncestors($currentUnit));       //Parents are needed for printing the title
                $smarty -> assign("T_COMMENTS",      eF_getComments($_SESSION['s_lessons_ID'], false, $currentUnit['id']));        //Retrieve any comments regarding this unit
                $smarty -> assign("T_SHOW_TOOLS",    true);                                                    //Tools is the right upper corner table box, that lists tools such as 'upload files', 'copy content' etc
            } else {
                $showTest = new EfrontTest($_GET['show_test']);
            }

            $smarty -> assign ("T_CURRENT_TEST", $showTest -> test);
            if (isset($_GET['print'])) {
                $testString = $showTest -> toHTML($showTest -> toHTMLQuickForm(), false, true);
            } else {
                $testString = $showTest -> toHTML($showTest -> toHTMLQuickForm(), false);
            }
            $smarty -> assign ("T_TEST_UNSOLVED", $testString);
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    } elseif (isset($_GET['show_solved_test']) && eF_checkParameter($_GET['show_solved_test'], 'id')) {
        try {
            $result   = eF_getTableData("completed_tests", "*", "id=".$_GET['show_solved_test']);
            if (sizeof($result) == 0) {
                throw new EfrontTestException(_NONEXISTENTTEST.': '.$_GET['show_solved_test'], EfrontTestException :: NOT_DONE_TEST);
            }
            $completedTest = unserialize($result[0]['test']);

            if (!isset($_GET['test_analysis'])) {

                $status        = $completedTest -> getStatus($result[0]['users_LOGIN']);

                $completedTest -> options['answers']       = true;
                $completedTest -> options['given_answers'] = true;

                $url     = basename($_SERVER['PHP_SELF']).'?ctg=tests&show_solved_test='.$completedTest -> completedTest['id'];
                $baseUrl = basename($_SERVER['PHP_SELF']).'?ctg=tests';

                // We do not want all handles for test editing for skillgap tests - the students do not see the tests
                if ($skillgap_tests) {
                    $testString = $completedTest -> toHTMLQuickForm(new HTML_Quickform(), false, true, false);
                    $testString = $completedTest -> toHTMLSolved($testString, false);
                } else {
                    $testString = $completedTest -> toHTMLQuickForm(new HTML_Quickform(), false, true, true);
                    $testString = $completedTest -> toHTMLSolved($testString, true);
                }

                $smarty -> assign("T_TEST_SOLVED", $testString);
                $smarty -> assign("T_TEST_DATA", $completedTest);

                if (isset($_GET['ajax'])) {
                    $completedTest -> handleAjaxActions();
                }
            } else {

                if ($skillgap_tests) {
                    // Per-user analysis of the tests => skill gap analysis

                    // AJAX CODE TO RELOAD SKILL-GAP ANALYSIS PROPOSED LESSONS
                    if (isset($_GET['ajax']) && $_GET['ajax'] == 'proposedLessonsTable') {
                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                        $directionsTree = new EfrontDirectionsTree();
                        $directionsPaths = $directionsTree -> toPathString();
                        $languages       = EfrontSystem :: getLanguages(true);

                        $skills_missing = array();
                        $all_skills = "";

                        foreach ($_GET as $key => $value) {
                            // all skill-related posted values are just the skill_ID ~ a uint value
                            if (eF_checkParameter($key, 'unit')) {
                                if ($value == 1) {
                                    $skills_missing[] = $key;
                                    $all_skills .= "&".$skill_item['id'] . "=1";
                                } else {
                                    $all_skills .= "&".$skill_item['id'] . "=0";
                                }
                            }
                        }
                        // This smarty variable will denote all missing and existing skills
                        $smarty -> assign("T_MISSING_SKILLS_URL", $all_skills);

                        // check what you GET and keep only the skills
                        $skills_missing = implode("','",  $skills_missing);

                        $user = EfrontUserFactory :: factory($_GET['user']);
                        $alredy_attending = implode("','",  array_keys($user -> getLessons()));

                        $lessons_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID JOIN lessons ON lessons.id = module_hcd_lesson_offers_skill.lesson_ID","module_hcd_lesson_offers_skill.lesson_ID, lessons.*, count(module_hcd_lesson_offers_skill.skill_ID) as skills_offered", "module_hcd_lesson_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_lesson_offers_skill.lesson_ID NOT IN ('".$alredy_attending."')","","module_hcd_lesson_offers_skill.lesson_ID ORDER BY skills_offered DESC");

                        if (isset($_GET['sort'])) {
                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                            $lessons_proposed = eF_multiSort($lessons_proposed, $_GET['sort'], $order);
                        }
                        if (isset($_GET['filter'])) {
                            $lessons_proposed = eF_filterData($lessons_proposed, $_GET['filter']);
                        }
                        $smarty -> assign("T_PROPOSED_LESSONS_SIZE", sizeof($lessons_proposed));
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $lessons_proposed = array_slice($lessons_proposed, $offset, $limit);
                        }
                        foreach ($lessons_proposed as $key => $proposed_lesson) {
                            $obj = new EfrontLesson($proposed_lesson['lesson_ID']);
                            $lessons_proposed[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=lessons&edit_lesson='.$proposed_lesson['id']);
                            $lessons_proposed[$key]['direction_name'] = $directionsPaths[$proposed_lesson['directions_ID']];
                            $lessons_proposed[$key]['languages_NAME'] = $languages[$proposed_lesson['languages_NAME']];
                        }

                        $smarty -> assign("T_PROPOSED_LESSONS_DATA", $lessons_proposed);

                        $smarty -> display('administrator.tpl');
                        exit;
                    }


                    // AJAX CODE TO RELOAD SKILL-GAP ANALYSIS PROPOSED COURSES
                    if (isset($_GET['ajax']) && $_GET['ajax'] == 'proposedCoursesTable') {
                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                        $directionsTree = new EfrontDirectionsTree();
                        $directionsPaths = $directionsTree -> toPathString();
                        $languages       = EfrontSystem :: getLanguages(true);

                        $skills_missing = array();
                        $all_skills = "";

                        foreach ($_GET as $key => $value) {
                            // all skill-related posted values are just the skill_ID ~ a uint value
                            if (eF_checkParameter($key, 'unit')) {
                                if ($value == 1) {
                                    $skills_missing[] = $key;
                                    $all_skills .= "&".$skill_item['id'] . "=1";
                                } else {
                                    $all_skills .= "&".$skill_item['id'] . "=0";
                                }
                            }
                        }
                        // This smarty variable will denote all missing and existing skills
                        $smarty -> assign("T_MISSING_SKILLS_URL", $all_skills);

                        // check what you GET and keep only the skills
                        $skills_missing = implode("','",  $skills_missing);

                        $user = EfrontUserFactory :: factory($_GET['user']);

                        $alredy_attending = implode("','",  array_keys($user -> getCourses()));
                        $courses_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID JOIN courses ON courses.id = module_hcd_course_offers_skill.courses_ID","module_hcd_course_offers_skill.courses_ID, courses.*, count(module_hcd_course_offers_skill.skill_ID) as skills_offered", "module_hcd_course_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_course_offers_skill.courses_ID NOT IN ('".$alredy_attending."')","","module_hcd_course_offers_skill.courses_ID ORDER BY skills_offered DESC");

                        if (isset($_GET['sort'])) {
                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                            $courses_proposed = eF_multiSort($courses_proposed, $_GET['sort'], $order);
                        }

                        if (isset($_GET['filter'])) {
                            $courses_proposed = eF_filterData($courses_proposed, $_GET['filter']);
                        }

                        $smarty -> assign("T_PROPOSED_COURSES_SIZE", sizeof($courses_proposed));
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $courses_proposed = array_slice($courses_proposed, $offset, $limit);
                        }

                        foreach ($courses_proposed as $key => $proposed_course) {
                            $obj = new EfrontCourse($proposed_course['courses_ID']);
                            $courses_proposed[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=courses&edit_course='.$proposed_course['id']);
                            $courses_proposed[$key]['direction_name'] = $directionsPaths[$proposed_course['directions_ID']];
                            $courses_proposed[$key]['languages_NAME'] = $languages[$proposed_course['languages_NAME']];
                        }

                        $smarty -> assign("T_PROPOSED_COURSES_DATA", $courses_proposed);

                        $smarty -> display('administrator.tpl');
                        exit;
                    }

                    // AJAX CODE TO RELOAD ALREADY ASSIGNED LESSONS
                    if (isset($_GET['ajax'])  && $_GET['ajax'] == 'assignedLessonsTable') {
                        $directionsTree = new EfrontDirectionsTree();
                        $directionPaths = $directionsTree -> toPathString();
                        $lessons        = EfrontLesson :: getLessons();

                        $editedUser = EfrontUserFactory :: factory($_GET['user']);
                        $userLessons    = $editedUser -> getLessons(true);
                        foreach ($lessons as $key => $lesson) {
                            $lessons[$key]['directions_name'] = $directionPaths[$lesson['directions_ID']];
                            $lessons[$key]['user_type']       = $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type'];
                            $lessons[$key]['partof']          = 0;
                            if (in_array($lesson['id'], array_keys($userLessons))) {
                                $lessons[$key]['from_timestamp']  = $userLessons[$key] -> userStatus['from_timestamp'];
                                $lessons[$key]['partof']          = 1;
                                $lessons[$key]['user_type']       = $userLessons[$key] -> userStatus['user_type'];
                                $lessons[$key]['completed']       = $userLessons[$key] -> userStatus['completed'];
                                $lessons[$key]['score']           = $userLessons[$key] -> userStatus['score'];
                            } else if ($currentUser -> user['user_type'] != 'administrator' || !$lesson['active']) {
                                unset($lessons[$key]);
                            } else if ($lesson['languages_NAME'] != $editedUser -> user['languages_NAME']) {
                                unset($lessons[$key]);
                            }
                            if ($lesson['course_only']) {
                                unset($lessons[$key]);
                            }
                        }

                        $roles = EfrontLessonUser :: getLessonsRoles(true);
                        $smarty -> assign("T_ROLES_ARRAY", $roles);

                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                        if (isset($_GET['sort'])) {
                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                            $lessons = eF_multiSort($lessons, $_GET['sort'], $order);
                        }
                        if (isset($_GET['filter'])) {
                            $lessons = eF_filterData($lessons, $_GET['filter']);
                        }
                        $smarty -> assign("T_ASSIGNED_LESSONS_SIZE", sizeof($lessons));
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $lessons = array_slice($lessons, $offset, $limit);
                        }
                        //foreach ($lessons as $key => $lesson) {
                            //$lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
                        //}
                        foreach ($lessons as $key => $lesson) {
                            if (!$lesson['partof']) {
                                unset($lessons[$key]);
                            } else {
                                $obj = new EfrontLesson($lesson['id']);
                                $lessons[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=lessons&edit_lesson='.$lesson['id']);
                                $lessons[$key]['skills_offered'] = sizeof($obj -> getSkills(true));
                            }
                        }
                        $smarty -> assign("T_ASSIGNED_LESSONS_DATA", $lessons);
                        $smarty -> display('administrator.tpl');
                        exit;
                    }

                    // AJAX CODE TO RELOAD ALREADY ASSIGNED COURSES
                    if (isset($_GET['ajax'])  && $_GET['ajax'] == 'assignedCoursesTable') {
                        $directionsTree = new EfrontDirectionsTree();
                        $directionPaths = $directionsTree -> toPathString();
                        $courses        = EfrontCourse :: getCourses();

                        $editedUser = EfrontUserFactory :: factory($_GET['user']);
                        $userCourses    = $editedUser -> getCourses(true);
                        foreach ($courses as $key => $course) {
                            $courses[$key]['directions_name'] = $directionPaths[$course['directions_ID']];
                            $courses[$key]['user_type']       = $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type'];
                            $courses[$key]['partof']          = 0;
                            if (in_array($course['id'], array_keys($userCourses))) {
                                $courses[$key]['from_timestamp']  = $userCourses[$key] -> userStatus['from_timestamp'];
                                $courses[$key]['partof']          = 1;
                                $courses[$key]['user_type']       = $userCourses[$key] -> userStatus['user_type'];
                                $courses[$key]['completed']       = $userCourses[$key] -> userStatus['completed'];
                                $courses[$key]['score']           = $userCourses[$key] -> userStatus['score'];
                            } else if ($currentUser -> user['user_type'] != 'administrator' || !$course['active']) {
                                unset($courses[$key]);
                            } else if ($course['languages_NAME'] != $editedUser -> user['languages_NAME']) {
                                unset($courses[$key]);
                            }
                            if ($course['course_only']) {
                                unset($courses[$key]);
                            }
                        }

                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                        if (isset($_GET['sort'])) {
                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                            $courses = eF_multiSort($courses, $_GET['sort'], $order);
                        }
                        if (isset($_GET['filter'])) {
                            $courses = eF_filterData($courses, $_GET['filter']);
                        }
                        $smarty -> assign("T_ASSIGNED_COURSES_SIZE", sizeof($courses));
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $courses = array_slice($courses, $offset, $limit);
                        }
                        //foreach ($courses as $key => $course) {
                            //$courses[$key]['languages_NAME'] = $languages[$course['languages_NAME']];
                        //}
                        foreach ($courses as $key => $course) {
                            if (!$course['partof']) {
                                unset($courses[$key]);
                            } else {
                                $obj = new EfrontCourse($course['id']);
                                $courses[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=courses&edit_course='.$course['id']);
                                $courses[$key]['skills_offered'] = sizeof($obj -> getSkills(true));
                            }
                        }
                        $smarty -> assign("T_ASSIGNED_COURSES_DATA", $courses);

                        $smarty -> display('administrator.tpl');
                        exit;
                    }


                    if (isset($_GET['ajax'])  && $_GET['ajax'] == 'coursesTable') {
                        $directionsTree = new EfrontDirectionsTree();
                        $directionPaths = $directionsTree -> toPathString();
                        $courses        = EfrontCourse :: getCourses();

                        $editedUser = EfrontUserFactory :: factory($_GET['user']);
                        $userCourses    = $editedUser -> getCourses(true);
                        foreach ($courses as $key => $course) {
                            $courses[$key]['partof']          = 0;
                            $courses[$key]['directions_name'] = $directionPaths[$course['directions_ID']];
                            $courses[$key]['user_type']       = $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type'];
                            if (in_array($course['id'], array_keys($userCourses))) {
                                $courses[$key]['from_timestamp']  = $userCourses[$key] -> userStatus['from_timestamp'];
                                $courses[$key]['partof']          = 1;
                                $courses[$key]['user_type']       = $userCourses[$key] -> userStatus['user_type'];
                                $courses[$key]['completed']       = $userCourses[$key] -> userStatus['completed'];
                                $courses[$key]['score']           = $userCourses[$key] -> userStatus['score'];
                            } else if ($currentUser -> user['user_type'] != 'administrator' || !$course['active']) {
                                unset($courses[$key]);
                            } else if ($course['languages_NAME'] != $editedUser -> user['languages_NAME']) {
                                unset($courses[$key]);
                            }
                        }
                        $courses = array_values($courses); //Reindex so that sorting works

                        $roles = EfrontLessonUser :: getLessonsRoles(true);
                        $smarty -> assign("T_ROLES_ARRAY", $roles);


                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                        if (isset($_GET['sort'])) {
                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                            $courses = eF_multiSort($courses, $_GET['sort'], $order);
                        }
                        if (isset($_GET['filter'])) {
                            $courses = eF_filterData($courses, $_GET['filter']);
                        }
                        $smarty -> assign("T_COURSES_SIZE", sizeof($courses));
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $courses = array_slice($courses, $offset, $limit);
                        }
                        //foreach ($courses as $key => $course) {
                            //$courses[$key]['languages_NAME'] = $languages[$course['languages_NAME']];
                        //}

                        $smarty -> assign("T_COURSES_DATA", $courses);

                        $smarty -> display($_SESSION['s_type'].'.tpl');
                        exit;
                    }

                    // We change a bit the following typical query to acquire the latest options values for the test - in case a threshold has been changed
                    $result = eF_getTableData("completed_tests JOIN tests ON tests.id = completed_tests.tests_ID", "completed_tests.*, tests.options", "completed_tests.id = '".$_GET['show_solved_test']."'");
                    $completedTest = unserialize($result[0]['test']);

                    // Take the most recent set general threshold for this test
                    $temp = unserialize($result[0]['options']);
                    $completedTest -> options['general_threshold'] = $temp['general_threshold'];

                    $smarty -> assign("T_TEST_DATA",$completedTest);

                    $user = eF_getTableData("users", "*", "login = '".$_GET['user']."'");
                    $smarty -> assign("T_USER_INFO", $user[0]);
                    $analysisResults = $completedTest -> analyseSkillGapTest();

                    if (!empty($analysisResults['testSkills'])) {
                        $smarty -> assign("T_SKILLSGAP",$analysisResults['testSkills']);
                    }
                    $smarty -> assign("T_MISSING_SKILLS_URL", $analysisResults['missingSkills']);
                    $lessons_proposed = $analysisResults['lessons'];
                    $courses_proposed = $analysisResults['courses'];
                } else {
                    require_once 'charts/php-ofc-library/open-flash-chart.php';
                    list($parentScores, $analysisCode) = $completedTest -> analyseTest();

                    $smarty -> assign("T_CONTENT_ANALYSIS", $analysisCode);
                    $smarty -> assign("T_TEST_DATA", $completedTest);

                    $status = $completedTest -> getStatus($result[0]['users_LOGIN']);
                    $smarty -> assign("T_TEST_STATUS", $status);

                    if (isset($_GET['display_chart'])) {
                        $url = basename($_SERVER['PHP_SELF']).'?ctg=tests&show_solved_test='.$completedTest -> completedTest['id'].'&test_analysis=1&selected_unit='.$_GET['selected_unit'].'&show_chart=1';
                        echo $completedTest -> displayChart($url);
                        exit;
                    } elseif (isset($_GET['show_chart'])) {
                        echo $completedTest -> calculateChart($parentScores);
                        exit;
                    }
                }
            }
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }

    } elseif (isset($_GET['questions_order']) && eF_checkParameter($_GET['questions_order'], 'id')) {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            exit;
        }         
        $currentTest = new EfrontTest($_GET['questions_order']);
        $questions   = $currentTest -> getQuestions();

        foreach ($questions as $key => $question) {
            $questions[$key]['text'] = strip_tags($question['text']);
        }
        $smarty -> assign("T_QUESTIONS", $questions);

        if (isset($_GET['ajax'])) {
            $order    = explode(",", $_GET['order']);
            $previous = 0;
            foreach ($order as $value) {
                $result = explode("-", $value);
                if (in_array($value, array_keys($questions))) {
                    eF_updateTableData("tests_to_questions", array("previous_question_ID" => $previous), "tests_ID=".$currentTest -> test['id']." and questions_ID=".$result[0]);
                }
                $previous = $result[0];
            }
            echo _TREESAVEDSUCCESSFULLY;
            exit;
        } else {
            $loadScripts[] = 'drag-drop-folder-tree';
        }

    } elseif (isset($_GET['show_question']) && eF_checkParameter($_GET['show_question'], 'id')) {
        try {
            $showQuestion = QuestionFactory :: factory($_GET['show_question']);
            $smarty -> assign("T_QUESTION", $showQuestion -> question);
            $smarty -> assign ("T_QUESTION_PREVIEW", $showQuestion -> toHTML(new HTML_Quickform()));
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    } elseif (isset($_GET['delete_question']) && eF_checkParameter($_GET['delete_question'], 'id')) {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            exit;
        }         
        try {
            $currentQuestion = QuestionFactory :: factory($_GET['delete_question']);
            $currentQuestion -> delete();

            $message = _QUESTIONDELETEDSUCCESSFULLY;
            $message_type = 'success';

            header("location:".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&message=$message&message_type=$message_type&tab=question");             //&tab=question is used for the tabber to enable the correct tab
        } catch (Exeption $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    } elseif (isset($_GET['add_question']) || (isset($_GET['edit_question']) && eF_checkParameter($_GET['edit_question'], 'id'))) {
        if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        if (isset($currentUser -> coreAccess['skillgaptests']) && $currentUser -> coreAccess['skillgaptests'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            exit;
        }         
        if (isset($_GET['postAjaxRequest_questions_insert'])) {
            $file_id = urldecode($_GET['file_id']);
            $file_insert = new EfrontFile($file_id);

            if (strpos($file_insert['mime_type'] , "image") !== false) {
                $img_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                echo "<img src=\"".$img_return."\" border=0 />";
                exit;
            } elseif (strpos($file_insert['mime_type'] , "flash") !== false) {
                $flash_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                if ($_GET['editor_mode'] == "true") {
                    echo '<img width="400" height="400" src="editor/tiny_mce/themes/advanced/images/spacer.gif"  title="'.$flash_return.'" alt="'.$flash_return.'" class="mceItemFlash" />';
                    exit;
                } else {
                    echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="400" height="400">
                    <param name="src" value="'.$flash_return.'" />
                    <param name="width" value="400" />
                    <param name="height" value="400" />
                    <embed type="application/x-shockwave-flash" src="'.$flash_return.'" width="400" height="400"></embed>
                    </object>';
                    exit;
                }
            } else {
                echo "<a href=view_file.php?action=download&file=".$file_id.">".$file_insert['physical_name']."</a>";
                exit;

            }
        }
        $smarty -> assign('T_BASENAME_PHPSELF', basename($_SERVER['PHP_SELF']));

        /** Post skill to questions - Ajax skill **/
        if (isset($_GET['postAjaxRequest']) && isset($_GET['skill'])) {

            if ($_GET['insert'] == "true") {
                eF_insertTableData("questions_to_skills", array("skills_ID" => $_GET['skill'], "questions_ID" => $_GET['edit_question'], "relevance" => $_GET['relevance']));
            } else if ($_GET['insert'] == "update") {
                eF_updateTableData("questions_to_skills", array("relevance" => $_GET['relevance']), "skills_ID = '". $_GET['skill'] . "' AND questions_ID = '" . $_GET['edit_question'] . "'");
            } else if ($_GET['insert'] == "false") {
                eF_deleteTableData("questions_to_skills", "skills_ID = '" . $_GET['skill']. "' AND questions_ID = '" . $_GET['edit_question'] . "'");
            } else if (isset($_GET['addAll'])) {
                $existing_question_skills = eF_getTableDataFlat("questions_to_skills", "skills_ID", "questions_ID = '".$_GET['edit_question']."'");

                $all_skills = eF_getTableDataFlat("module_hcd_skills", "skill_ID", "categories_ID = -1");
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
                eF_deleteTableData("questions_to_skills", "questions_ID = '".$_GET['edit_question'] . "'");
            }
            exit;
        }

        $load_editor   = true;
        $questionTypes = Question :: $questionTypes;

        // Remove development questions from automatically corrected skillgap tests
        if ($skillgap_tests) {
            unset($questionTypes['raw_text']);
        }

        isset($_GET['question_type']) && in_array($_GET['question_type'], array_keys($questionTypes)) ? $question_type = $_GET['question_type'] : $question_type = 'multiple_one';
        if (isset($_GET['edit_question'])) {                                                        //We are changing an existing question.
            try {
                $currentQuestion = QuestionFactory :: factory($_GET['edit_question']);
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }

            $postTarget = basename($_SERVER['PHP_SELF'])."?ctg=tests&edit_question=".$currentQuestion -> question['id']."&question_type=".$currentQuestion -> question['type'];
            if (strpos($_SERVER['HTTP_REFERER'], 'edit_test') !== false) {                    //We asked to edit a question through the tests interface, so we must return there after submission
                preg_match("/edit_test=(\d+)/", $_SERVER['HTTP_REFERER'], $matches);
                if (sizeof($matches) > 0) {
                    $postTarget .= '&from_test='.$matches[1];
                }
            }
        } else {
            $postTarget = basename($_SERVER['PHP_SELF'])."?ctg=tests&add_question=1&question_type=".$question_type;
        }

        $form = new HTML_QuickForm("question_form", "post", $postTarget, "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                          //Register this rule for checking user input with our function, eF_checkParameter

        if (isset($currentContent)) {
            $optionsArray = $currentContent -> toHTMLSelectOptions();
            $optionsArray = array(0 => _NOPARENT) + $optionsArray;

            $form -> addElement('select', 'content_ID', _UNITPARENT, $optionsArray);  //Build a select box with all content units
            $form -> addRule('content_ID', _THEFIELD.' '._UNITPARENT.' '._ISMANDATORY, 'required');            //The content id must be present and a numeric value.
            $form -> addRule('content_ID', _INVALIDID, 'numeric');
            if (isset($_GET['content_ID'])) {
                $form -> setDefaults(array('content_ID' => $_GET['content_ID']));                              //If a content is specified, then set it to be selected as well
            }
        }
        $form -> addElement('select', 'question_type', _QUESTIONTYPE, $questionTypes, 'id = "question_type" onchange = "window.location = \''.basename($_SERVER['PHP_SELF']).'?ctg=tests&add_question=1&question_type=\'+this.options[this.selectedIndex].value"');     //Depending on user selection, changing the question type reloads the page with the corresponding form fields
        $form -> addRule('question_type', _THEFIELD.' '._QUESTIONTYPE.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('question_type', _INVALIDFIELDDATA, 'callback', 'text');
        $form -> setDefaults(array('question_type' => $question_type));                                             //Set the default selected question type to be 'multiple_one'

        $difficulties = array('high' => _HIGH, 'medium' => _MEDIUM, 'low' => _LOW);
        $form -> addElement('select', 'difficulty', _DIFFICULTY, $difficulties);
        $form -> addRule('difficulty', _THEFIELD.' '._DIFFICULTY.' '._ISMANDATORY, 'required', null, 'client');     //Difficulty is mandatory and can be only a plain string
        $form -> addRule('difficulty', _INVALIDFIELDDATA, 'lettersonly');
        if (isset($_GET['difficulty'])) {
            $form -> setDefaults(array('difficulty' => $_GET['difficulty']));                              //If a difiiculty is specified, then set it to be selected
        } else {
            $form -> setDefaults(array('difficulty' => 'medium'));                                                      //else, set the default selected difficulty to be 'medium'
        }

        $form -> addElement('textarea', 'question_text', _QUESTIONTEXT, 'class = "mceEditor inputTextarea_QuestionText" style = "width:100%;height:250px;" id = "question_text"');
        $form -> addRule('question_text', _THEFIELD.' '._ISMANDATORY, 'required', null);
        //$form -> addRule('question_text', _INVALIDFIELDDATA, 'checkParameter', 'text');

        $form -> addElement('textarea', 'explanation', _EXPLANATION, 'class = "simpleEditor" style = "width:99%;height:100px;"');    //The style needs to be here, since when a textarea is in "display:none" mode, the tinymce does not render the class correctly

        $form -> addElement('submit', 'submit_question', _SAVEQUESTION, 'class = "flatButton"');
        $form -> addElement('submit', 'submit_new_question', _SAVEASNEWQUESTION, 'class = "flatButton"');
        $form -> addElement('submit', 'submit_question_another', _SAVEQUESTIONANDCREATENEW, 'class = "flatButton"');

        if (isset($currentQuestion)) {                                                                         //If we are changing an existing question
            $form -> setDefaults(array('content_ID'    => $currentQuestion -> question['content_ID'],          //Set form values to the stored ones.
                                       'question_type' => $currentQuestion -> question['type'],
                                       'difficulty'    => $currentQuestion -> question['difficulty'],
                                       'question_text' => $currentQuestion -> question['text'],
                                       'explanation'   => $currentQuestion -> question['explanation']));
            $form -> freeze('question_type');                                                                  //The question type cannot be changed
            $smarty -> assign("T_HAS_EXPLANATION", $currentQuestion -> question['explanation']);               //If the question has an explanation, use this smarty tag to set explanation field to be visible by default.
        }

        switch ($_GET['question_type']) { //Depending on the question type, the user might have added new form fields. We need to recreate the form, in order to be able to handle them both in case of succes or failure.
            case 'multiple_one':
                if ($form -> isSubmitted() || isset($currentQuestion)) {
                    if (isset($currentQuestion) && !$form -> isSubmitted()) {
                        $values['multiple_one']         = array_values(unserialize($currentQuestion -> question['options']));      //We put array_values to make sure that the array starts from zero
                        $values['correct_multiple_one'] = unserialize($currentQuestion -> question['answer']);
                        $values['correct_multiple_one'] = $values['correct_multiple_one'][0];                          //In multiple_one, only one value is valid. Get this out of the array
                    } else {
                        $values = $form -> getSubmitValues();
                    }

                    //Create each multiple choice from the beginning, this way including any choices the user added himself
                    foreach ($values['multiple_one'] as $key => $value) {
                        $form -> addElement('text', 'multiple_one['.$key.']', null, 'class = "inputText inputText_QuestionChoice"');
                        $form -> addRule('multiple_one['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                        //$form -> addRule('multiple_one['.$key.']', _INVALIDFIELDDATA, 'checkParameter', 'text');
                        $form -> setDefaults(array('multiple_one['.$key.']' => $value));
                    }

                    $form -> addElement('select', 'correct_multiple_one', _RIGHTANSWER, range(1, sizeof($values['multiple_one'])), 'id = "correct_multiple_one"');//Rebuild the correct options list, to be as large as the options the user added
                    $form -> setDefaults(array('correct_multiple_one' => $values['correct_multiple_one']));     //Set the selected correct option to be the one the user selected

                    if ($form -> validate()) {
                        $question_values = array('type'    => 'multiple_one',
                                                 'options' => addslashes(serialize($values['multiple_one'])),
                                                 'answer'  => serialize(array($values['correct_multiple_one'])));
                    }
                } else {
                    //By default, only 2 options are displayed
                    $form -> addElement('text', 'multiple_one[0]', _INSERTMULTIPLEQUESTIONS, 'class = "inputText inputText_QuestionChoice"');
                    $form -> addRule('multiple_one[0]', _THEFIELD.' "'._INSERTMULTIPLEQUESTIONS.'" '._ISMANDATORY, 'required', null, 'client');
                    $form -> addRule('multiple_one[0]', _INVALIDFIELDDATA, 'checkParameter', 'text');

                    $form -> addElement('text', 'multiple_one[1]', '', 'class = "inputText inputText_QuestionChoice"');
                    $form -> addRule('multiple_one[1]', _THEFIELD.' "'._INSERTMULTIPLEQUESTIONS.'" '._ISMANDATORY, 'required', null, 'client');
                    $form -> addRule('multiple_one[1]', _INVALIDFIELDDATA, 'checkParameter', 'text');

                    $form -> addElement('select', 'correct_multiple_one', _RIGHTANSWER, array(1, 2), 'id = "correct_multiple_one"');
                }
                break;

            case 'multiple_many':
                if ($form -> isSubmitted() || isset($currentQuestion)) {
                    if (isset($currentQuestion) && !$form -> isSubmitted()) {
                        $values['multiple_many']         = unserialize($currentQuestion -> question['options']);
                        $values['correct_multiple_many'] = unserialize($currentQuestion -> question['answer']);
                    } else {
                        $values = $form -> getSubmitValues();
                    }

                    //Create each multiple choice from the beginning, this way including any choices the user added himself
                    foreach ($values['multiple_many'] as $key => $value) {
                        $form -> addElement('text', 'multiple_many['.$key.']', 'Insert Questions', 'class = "inputText inputText_QuestionChoice"');
                        $form -> addRule('multiple_many['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                        //$form -> addRule('multiple_many['.$key.']', _INVALIDFIELDDATA, 'checkParameter', 'text');
                        $form -> setDefaults(array('multiple_many['.$key.']' => $value));

                        $form -> addElement('checkbox', 'correct_multiple_many['.$key.']');
                        $form -> setDefaults(array('correct_multiple_many['.$key.']' => $values['correct_multiple_many'][$key]));
                    }

                    if ($form -> validate()) {
                        $question_values = array('type'    => 'multiple_many',
                                                 'options' => addslashes(serialize($values['multiple_many'])),
                                                 'answer'  => serialize($values['correct_multiple_many']));
                    }
                } else {
                    //By default, only 2 options are displayed
                    $form -> addElement('text', 'multiple_many[0]', 'Insert Multiple Questions (many)', 'class = "inputText inputText_QuestionChoice"');
                    $form -> addRule('multiple_many[0]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                    $form -> addRule('multiple_many[0]', _INVALIDFIELDDATA, 'checkParameter', 'text');

                    $form -> addElement('text', 'multiple_many[1]', null, 'class = "inputText inputText_QuestionChoice"');
                    $form -> addRule('multiple_many[1]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                    $form -> addRule('multiple_many[1]', _INVALIDFIELDDATA, 'checkParameter', 'text');

                    $form -> addElement('checkbox', 'correct_multiple_many[0]');
                    $form -> addElement('checkbox', 'correct_multiple_many[1]');
                }
                break;

            case 'raw_text':
                $form -> addElement('textarea', 'example_answer', _EXAMPLEANSWER, 'class = "inputTextarea_QuestionExample" style = "width:100%" ');
                //$form -> addRule('example_answer', _INVALIDFIELDDATA, 'checkParameter', 'text');

                if ($form -> isSubmitted() || isset($currentQuestion)) {
                    if (isset($currentQuestion) && !$form -> isSubmitted()) {
                        $form -> setDefaults(array('example_answer' => $currentQuestion -> question['answer']));
                    }

                    if ($form -> validate()) {
                        $question_values = array('type'    => 'raw_text',
                                                 'options' => '',
                                                 'answer'  => $form -> exportValue('example_answer'));
                    }
                }

                break;

            case 'match':
                if ($form -> isSubmitted() || isset($currentQuestion)) {
                    if (isset($currentQuestion) && !$form -> isSubmitted()) {
                        $values['match']         = unserialize($currentQuestion -> question['options']);
                        $values['correct_match'] = unserialize($currentQuestion -> question['answer']);
                    } else {
                        $values = $form -> getSubmitValues();
                    }

                    foreach ($values['match'] as $key => $value) {
                        $form -> addElement('text', 'match['.$key.']', 'Insert Questions', 'class = "inputText inputText_QuestionChoice"');
                        $form -> addRule('match['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                        //$form -> addRule('match['.$key.']', _INVALIDFIELDDATA, 'checkParameter', 'text');
                        $form -> setDefaults(array('match['.$key.']' => $value));

                        $form -> addElement('text', 'correct_match['.$key.']', 'Insert Questions', 'class = "inputText inputText_QuestionChoice"');
                        $form -> addRule('correct_match['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                        //$form -> addRule('correct_match['.$key.']', _INVALIDFIELDDATA, 'checkParameter', 'text');
                        $form -> setDefaults(array('correct_match['.$key.']' => $values['correct_match'][$key]));
                    }

                    if ($form -> validate()) {
                        $question_values = array('type'    => 'match',
                                                 'options' => addslashes(serialize($values['match'])),
                                                 'answer'  => addslashes(serialize($values['correct_match'])));
                    }
                } else {
                    //By default, only 2 pairs of choices given.
                    $form -> addElement('text', 'match[0]', 'Insert Match Questions', 'class = "inputText inputText_QuestionChoice"');
                    $form -> addRule('match[0]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                    $form -> addRule('match[0]', _INVALIDFIELDDATA, 'checkParameter', 'text');

                    $form -> addElement('text', 'correct_match[0]', 'nsert Match Questions', 'class = "inputText inputText_QuestionChoice"');
                    $form -> addRule('correct_match[0]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                    $form -> addRule('correct_match[0]', _INVALIDFIELDDATA, 'checkParameter', 'text');

                    $form -> addElement('text', 'match[1]', 'sert Match Questions', 'class = "inputText inputText_QuestionChoice"');
                    $form -> addRule('match[1]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                    $form -> addRule('match[1]', _INVALIDFIELDDATA, 'checkParameter', 'text');

                    $form -> addElement('text', 'correct_match[1]', 'ert Match Questions', 'class = "inputText inputText_QuestionChoice"');
                    $form -> addRule('correct_match[1]', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
                    $form -> addRule('correct_match[1]', _INVALIDFIELDDATA, 'checkParameter', 'text');
                }
                break;

            case 'empty_spaces':
                $form -> addElement('button', 'generate_empty_spaces', _CREATEEMPTYSPACES, 'class = "flatButton" onclick = "eF_js_createEmptySpaces()"');
                if ($form -> isSubmitted() || isset($currentQuestion)) {
                    if (isset($currentQuestion) && !$form -> isSubmitted()) {
                        $values['empty_spaces'] = unserialize($currentQuestion -> question['answer']);
                        //$values['correct_match'] = unserialize($currentQuestion -> question['answer']);
                    } else {
                        $values = $form -> getSubmitValues();
                    }

                    $excerpts = explode('###', $currentQuestion -> question['text']);
                    $smarty -> assign("T_EXCERPTS", $excerpts);

                    foreach ($values['empty_spaces'] as $key => $value) {
                        $form -> addElement('text', 'empty_spaces['.$key.']', null, 'class = "inputText"');
                        $form -> addRule('empty_spaces['.$key.']', _INVALIDFIELDDATA, 'checkParameter', 'text');
                        $form -> setDefaults(array('empty_spaces['.$key.']' => $value));
                    }

                    if ($form -> validate()) {
                        $question_values = array('type'    => 'empty_spaces',
                                                 'options' => '',
                                                 'answer'  => serialize($values['empty_spaces']));
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
                        $question_values = array('type'    => 'true_false',
                                                 'options' => '',
                                                 'answer'  => serialize($values['correct_true_false']));
                    }
                }
                break;

            default: break;
        }

        //Common fields and actions for all question types
        if ($form -> isSubmitted() && $form -> validate()) {
                $form_values                    = eF_addSlashes($form -> exportValues(), false);
                $question_values['text']        = $form_values['question_text'];
                $question_values['content_ID']  = $form_values['content_ID'] ? $form_values['content_ID'] : 0;
                $question_values['difficulty']  = $form_values['difficulty'];
                $question_values['explanation'] = $form_values['explanation'];
                $question_values['lessons_ID']  = $currentLesson -> lesson['id'] ? $currentLesson -> lesson['id'] : 0;

                if (isset($currentQuestion)) {                                                                //If we are changing an existing question
                    isset($_GET['from_test']) ? $location = '&edit_test='.$_GET['from_test'] : $location = '';
                    if (isset($form_values['submit_new_question'])) {
                        if ($new_question_id = eF_insertTableData("questions", $question_values)) {
                            // Code to maintain consistent state in questions_to_skills:
                            // -- add either question to lesson specific skill if lesson['course_only'] == 0
                            // -- or question to course specific skill if lesson['course_only'] == 1     
                            
                            // If we edited a question and resulted here then we decided to save as a new question
                            if ($_GET['edit_question']) {
                                // Then just copy all skills from existing question to the new one
                                $questionSkills = eF_getTableDataFlat("questions_to_skills","skills_ID", "questions_id = '".$_GET['edit_question']."'");
                                if (sizeof ($questionSkills)> 0) {
                                    $insertString = "('" . $new_question_id . "', '" . implode("'),('" . $new_question_id . "', '", $questionSkills['skills_ID']) . "')";
                                    eF_execute("INSERT INTO questions_to_skills (questions_id,skills_ID) VALUES $insertString");
                                }                                
                            } else {
	                            if ($question_values['lessons_ID']) {
	                                // then the currentLesson object exists
	                                if ($currentLesson -> lesson['course_only']) {
	                                    $courses = $currentLesson -> getCourses();
	                                    //OPTIMIZE
	                                    foreach ($courses as $cid => $course) {
	                                        $course_specific_skill = eF_getTableData("module_hcd_course_offers_skill JOIN module_hcd_skills ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID", "module_hcd_course_offers_skill.skill_ID", "module_hcd_skills.categories_ID = -1 AND module_hcd_course_offers_skill.courses_ID = '".$cid ."'");
	                                        eF_insertTableData("questions_to_skills", array("questions_ID" => $new_question_id, "skills_ID" => $course_specific_skill[0]['skill_ID']));
	                                    }
	                                } else {
	                                    $lesson_skill = $currentLesson -> getLessonSkill();
	                                    eF_insertTableData("questions_to_skills", array("questions_ID" => $new_question_id, "skills_ID" => $lesson_skill['skill_ID'], "relevance" => 2));
	                                }
	                            }
                            }

    
                            $message      = _SUCCESFULLYADDEDQUESTION;
                            $message_type = 'success';

                            header("location:".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests$location&message=$message&message_type=$message_type&tab=question");             //&question is used for the tabber to enable the correct tab
                        } else {
                            $message      = _SOMEPROBLEMEMERGED;
                            $message_type = 'failure';
                        }
                    } else {
                        if (eF_updateTableData("questions", $question_values, "id=".$currentQuestion -> question['id'])) {          //Update the question
                            $message      = _SUCCESFULLYUPDATEDQUESTION;
                            $message_type = 'success';

                            header("location:".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests$location&message=$message&message_type=$message_type&tab=question");             //&question is used for the tabber to enable the correct tab
                        } else {
                            $message      = _SOMEPROBLEMEMERGED;
                            $message_type = 'failure';
                        }
                    }
                } else {                                                                                    //We are inserting a new question
                    if ($new_question_id = eF_insertTableData("questions", $question_values)) {
                        // Code to maintain consistent state in questions_to_skills:
                        // -- add either question to lesson specific skill if lesson['course_only'] == 0
                        // -- or question to course specific skill if lesson['course_only'] == 1
                        if ($question_values['lessons_ID']) {
                            // then the currentLesson object exists
                            if ($currentLesson -> lesson['course_only']) {
                                $courses = $currentLesson -> getCourses();
                                //OPTIMIZE
                                foreach ($courses as $cid => $course) {
                                    $course_specific_skill = eF_getTableData("module_hcd_course_offers_skill JOIN module_hcd_skills ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID", "module_hcd_course_offers_skill.skill_ID", "module_hcd_skills.categories_ID = -1 AND module_hcd_course_offers_skill.courses_ID = '".$cid ."'");
                                    eF_insertTableData("questions_to_skills", array("questions_ID" => $new_question_id, "skills_ID" => $course_specific_skill[0]['skill_ID']));
                                }
                            } else {
                                $lesson_skill = $currentLesson -> getLessonSkill();
                                eF_insertTableData("questions_to_skills", array("questions_ID" => $new_question_id, "skills_ID" => $lesson_skill['skill_ID'], "relevance" => 2));
                            }
                        }
                        $message      = _SUCCESFULLYADDEDQUESTION;
                        $message_type = 'success';

                        if ($form -> exportValue('submit_question')) {
                            header("location:".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&message=$message&message_type=$message_type&tab=question");             //&question is used for the tabber to enable the correct tab
                        } else {
                            header("location:".ltrim(basename($_SERVER['PHP_SELF']), "/")."?ctg=tests&add_question=1&difficulty=".$question_values['difficulty']."&content_ID=".$question_values['content_ID']."&question_type=".$_GET['question_type']."&message=$message&message_type=$message_type");
                        }
                    } else {
                        $message      = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                }

        }

        if ($skillgap_tests) {

            // Get the text of the question
            $smarty -> assign("T_QUESTION_TEXT", strip_tags($currentQuestion -> question['text']));
            $skills = eF_getTableData("module_hcd_skills LEFT OUTER JOIN questions_to_skills ON skill_ID = skills_ID AND questions_ID = ".$currentQuestion -> question['id'], "distinct skill_ID, description, relevance, questions_ID", "");

            $smarty -> assign('T_QUESTION_SKILLS', $skills);
        }




        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);


        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);

        $smarty -> assign('T_QUESTION_FORM', $renderer -> toArray());

        $loadScripts[] = 'scriptaculous/effects';
            try {
                if ($currentUser -> getType() == "administrator") {
                    $basedir    = G_ADMINPATH;
                } else {
                    $basedir    = $currentLesson -> getDirectory();
                }
                $filesystem = new FileSystemTree($basedir);
                $filesystem -> handleAjaxActions($currentUser);

                if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
                    $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
                } else {
                    $options = array('delete' => false, 'edit' => false, 'share' => false, 'upload' => false, 'create_folder' => false, 'zip' => false, 'lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
                }

                if (isset($_GET['edit_question'])) {
                    $url = basename($_SERVER['PHP_SELF']).'?ctg=tests&edit_question='.$_GET['edit_question'].'&question_type='.$_GET['question_type'];
                }else{
                    $url = basename($_SERVER['PHP_SELF']).'?ctg=tests&add_question=1&question_type='.$_GET['question_type'];
                }

                if (isset($_GET['ajax'])) {
                    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                        $sort = $_GET['sort'];
                        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                    } else {
                        $sort = 'login';
                    }

                    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    }
                    isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                    isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                    $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                    $extraFileTools = array(array('image' => 'images/16x16/arrow_right_green.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
                    echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, $extraFileTools, '', '', '', false);
                    exit;
                }
                $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, $extraFileTools, '', '', '', false));
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }

    } elseif (isset($_GET['test_results']) && eF_checkParameter($_GET['test_results'], 'id')) {
        try {
            $currentTest = new EfrontTest($_GET['test_results']);
            $doneTests   = EfrontStats :: getDoneTestsPerTest(false, $currentTest -> test['id']);
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
                $doneTests[$currentTest -> test['id']][$user]['surname'] =  $all_users[$user]['surname'];
                $doneTests[$currentTest -> test['id']][$user]['name'] =  $all_users[$user]['name'];
            }
            
            $smarty -> assign("T_DONE_TESTS", $doneTests[$currentTest -> test['id']]);
            
            if (isset($_GET['ajax']) && $_GET['reset_all'] == 1) {
                try {
                    if (!in_array($_GET['login'], array_keys($doneTests[$currentTest -> test['id']]))) {
                        throw new EfrontTestException(_INVALIDLOGIN.': '.$_GET['login'], EfrontTestException :: INVALID_LOGIN);
                    }
                    $currentTest -> undo($_GET['login']);
                    exit;
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
            }
            //$smarty -> assign("T_CURRENT_TEST", $currentTest -> test['id']);
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    } elseif (isset($_GET['solved_tests'])) {
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
                $recentTests[$rtid]['score'] = $completedRecentTest->completedTest['score'];
            }
        }

        $smarty -> assign("T_RECENT_TESTS" , $recentTests);
    } else {
        try {
            $smarty -> assign("T_QUESTIONTYPESTRANSLATIONS", Question :: $questionTypes);
            $smarty -> assign("T_QUESTIONDIFFICULTYTRANSLATIONS", array('low' => _LOW, 'medium' => _MEDIUM, 'high' => _HIGH));
            $select_units = & HTML_QuickForm :: createElement('select', 'question_type', _QUESTIONTYPE, null, 'class = "inputSelect" id = "question_type" onchange = "window.location = \''.basename($_SERVER['PHP_SELF']).'?ctg=tests&add_question=1&question_type=\'+this.options[this.selectedIndex].value"');
            $select_units -> addOption(_ADDQUESTIONOFTYPE, 0);
            $select_units -> addOption('-------------', -1);

            // Remove development questions from automatically corrected skillgap tests
            if ($skillgap_tests) {
                $question_types = Question :: $questionTypes;
                unset($question_types['raw_text']);
                $select_units -> loadArray($question_types);
            } else {
                $select_units -> loadArray(Question :: $questionTypes);
            }

            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $select_units -> accept($renderer);
            $smarty -> assign('T_QUESTION_TYPE', $renderer -> toArray());

            //Calculate available questions for normal or skill gap tests
            if ($currentContent) {
                try {
                    isset($_GET['from_unit']) && eF_checkParameter($_GET['from_unit'], 'id') ? $selectedUnit = $_GET['from_unit'] : $selectedUnit = 0;                    
                    $siblings   = $currentContent -> getNodeChildren($selectedUnit);
                    $children[] = $siblings['id'];
                    foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($siblings), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
                        $children[] = $key;
                    }

                    if (sizeof($children) > 0) {
                        $questions = eF_getTableData("questions", "*", "content_ID in (".implode(",", $children).") and lessons_ID=".$currentLesson -> lesson['id'], "content_ID ASC");     //Retrieve all questions that belong to this unit or its subunits
                    } else {
                        throw new Exception();//This jumps to the catch block right below
                    }
                } catch (Exception $e) {
                    $questions = eF_getTableData("questions", "*", "lessons_ID = ".$currentLesson -> lesson['id'], "content_ID ASC");     //Retrieve all questions that belong to this lesson                    
                }

                foreach ($questions as $key => $question) {
                    $names = array();
                    try {
                        $currentContent -> seekNode($question['content_ID']);                    //Check that the question's unit actually exists
                    } catch (EfrontTreeException $e) {
                        if ($e -> getCode() == EfrontTreeException :: NODE_NOT_EXISTS ) {        //If the unit that this question is attached to does not exist, then unattach the question
                            eF_updateTableData("questions", array("content_ID" => 0), "id=".$question['id']);
                            $question['content_ID'] = 0;
                        } else {
                            throw $e;
                        }
                    }
                }
            } else {
                $questions = eF_getTableData("questions LEFT OUTER JOIN lessons ON lessons.id = lessons_ID", "questions.*, lessons.name", "type <> 'raw_text'", "");     //Retrieve all questions that belong to this unit or its subunits

                // If no lesson then define the current lesson name => _SKILLGAPTESTS (used for correct filtering)
                foreach ($questions as $qid => $question) {
                    if ($question['lessons_ID'] == 0) {
                        $questions[$qid]['name'] = _SKILLGAPTESTS;
                    }
                }
            }                       

            //Display questions list through ajax in a sortedTable
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
                        foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> getNodeAncestors($question['content_ID']))), array('name')) as $k => $v) {
                            $names[] = $v;
                        }
                        $questions[$key]['parent_unit'] = implode("&nbsp;&raquo;&nbsp;", array_reverse($names));
                    } else {
                        $questions[$key]['parent_unit'] = "";
                    }
                    $questions[$key]['text']        = strip_tags($question['text']);                            //Strip tags from the question text, so they do not display in the list
                }

                $questions = eF_multiSort($questions, $sort, $order);
                $smarty -> assign("T_QUESTIONS_SIZE", sizeof($questions));
                if (isset($_GET['filter'])) {
                    $questions = eF_filterData($questions, $_GET['filter']);
                }

                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $questions = array_slice($questions, $offset, $limit, true);
                }

                $smarty -> assign('T_QUESTIONS', $questions);
                $currentContent ? $smarty -> display('professor.tpl') : $smarty -> display('administrator.tpl');
                exit;
            }

            //Calculate available normal or skill gap tests
            if ($currentContent) {
                $smarty -> assign("T_UNITS", $currentContent -> toHTMLSelectOptions());

                isset($_GET['from_unit']) && eF_checkParameter($_GET['from_unit'], 'id') ? $selectedUnit = $_GET['from_unit'] : $selectedUnit = 0;
                $selectedUnit ? $units = $currentContent -> getNodeChildren($selectedUnit) : $units = $currentContent -> tree;
                foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($units)), array('id', 'name')) as $key => $value) {
                    $key == 'id' ? $ids[] = $value : $names[] = $value;
                }
                $tests     = eF_getTableData("content,tests", "content.id as content_ID, content.name, tests.id, tests.active, tests.publish, tests.mastery_score, tests.description, tests.options", "ctg_type='tests' AND content.id IN (".implode(",", $ids).") AND content.active=1 and content.id=tests.content_ID", "content.id ASC");

                $result    = eF_getTableData("tests_to_questions", "tests_ID, count(*)", "", "", "tests_ID");
                foreach ($result as $value) {
                    $testQuestions[$value['tests_ID']] = $value['count(*)'];
                }

                foreach ($tests as $key => $test) {
                    $names = array();
                    foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> getNodeAncestors($test['content_ID']))), array('name')) as $k => $v) {
                        $names[] = $v;
                    }
                    $tests[$key]['parent_unit']    = implode("&nbsp;&raquo;&nbsp;", array_reverse(array_slice($names, 1)));
                    $tests[$key]['questions_num'] = $testQuestions[$test['id']];
                }
            } else {
                $tests = eF_getTableData("tests LEFT OUTER JOIN tests_to_questions ON tests.id = tests_to_questions.tests_ID", "tests.*, count(questions_ID) as questions_num", "lessons_ID=0 GROUP BY tests.id");
                $smarty -> assign("T_RECENTLY_SKILLGAP_OPTIONS", array(array('text' => _SHOWALLSOLVEDSKILLGAPTESTS,   'image' => "16x16/view.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=tests&solved_tests=1")));
            }

            //Caclulate done tests list
            $testIds = array();
            foreach ($tests as $key => $test) {
                $testIds[] = $test['id'];
                $doneTests = EfrontStats :: getDoneTestsPerTest(false, $test['id']);
                $tests[$key]['average_score'] = $doneTests[$test['id']]['average_score'];
                $tests[$key]['options']       = unserialize($test['options']);
                if ($tests[$key]['options']['random_pool'] > 0) {
                    if ($tests[$key]['questions_num'] > $tests[$key]['options']['random_pool']) {
                        $tests[$key]['questions_num'] =  $tests[$key]['options']['random_pool'];
                    }
                }
            }

            $smarty -> assign("T_TESTS", $tests);

            if (!empty($testIds)) {
                if ($currentContent) {
                    $recentTests = eF_getTableData("completed_tests ct, tests t, users u, content c", "c.name, ct.id, ct.test, u.name as username, u.surname, ct.tests_ID, ct.timestamp, ct.users_LOGIN", "t.content_ID = c.id and ct.tests_ID = t.id AND ct.users_login = u.login AND ct.status != 'incomplete' AND ct.tests_id IN ('". implode("','", $testIds) ."')", "timestamp DESC");
                } else {
                    $recentTests = eF_getTableData("completed_tests JOIN tests ON tests_id = tests.id JOIN users ON completed_tests.users_LOGIN = users.login JOIN users_to_skillgap_tests ON completed_tests.users_LOGIN = users_to_skillgap_tests.users_LOGIN AND users_to_skillgap_tests.tests_ID = tests.id AND users_to_skillgap_tests.solved = 1", "completed_tests.id, completed_tests.test, users.name as username, users.surname, completed_tests.tests_ID, tests.name, completed_tests.timestamp, completed_tests.users_LOGIN", "completed_tests.tests_id IN ('". implode("','", $testIds) ."')", "timestamp DESC");
                }
                foreach ($recentTests as $rtid => $rtest) {
                    $completedRecentTest = unserialize($rtest['test']);
                    $recentTests[$rtid]['score'] = $completedRecentTest->completedTest['score'];
                }
            }

            $smarty -> assign("T_RECENT_TESTS" , $recentTests);

        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    }





?>