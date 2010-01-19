<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}


$loadScripts[] = 'includes/lessons';

if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}

if (isset($_GET['delete_lesson']) && eF_checkParameter($_GET['delete_lesson'], 'id')) { //The administrator asked to delete a lesson
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        exit;
    }
    try {
        $lesson = new EfrontLesson($_GET['delete_lesson']);
        $lesson -> delete();
    } catch (Exception $e) {
        $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
        header("HTTP/1.0 500 ");
        echo rawurlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['archive_lesson']) && eF_checkParameter($_GET['archive_lesson'], 'login')) { //The administrator asked to delete a lesson
    try {
     if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
         throw new Exception(_UNAUTHORIZEDACCESS);
     }
        $lesson = new EfrontLesson($_GET['archive_lesson']);
        $lesson -> archive();
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo rawurlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['deactivate_lesson']) && eF_checkParameter($_GET['deactivate_lesson'], 'id')) { //The administrator asked to deactivate a lesson
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'hidden') {
        echo rawurlencode(_UNAUTHORIZEDACCESS);
        exit;
    }
    try {
        $lesson = new EfrontLesson($_GET['deactivate_lesson']);
        $lesson -> deactivate();
        echo "0";
    } catch (Exception $e) {
        $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['activate_lesson']) && eF_checkParameter($_GET['activate_lesson'], 'id')) { //The administrator asked to activate a lesson
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
        echo urlencode(_UNAUTHORIZEDACCESS);
        exit;
    }
    try {
        $lesson = new EfrontLesson($_GET['activate_lesson']);
        $lesson -> activate();
        echo "1";
    } catch (Exception $e) {
        $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['unset_course_only']) && eF_checkParameter($_GET['unset_course_only'], 'id')) { //The administrator asked to deactivate a lesson
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
        echo urlencode(_UNAUTHORIZEDACCESS);
        exit;
    }
    try {
        $lesson = new EfrontLesson($_GET['unset_course_only']);






        $lesson -> lesson['course_only'] = 0;
        $lesson -> persist();
        echo "0";
    } catch (Exception $e) {
        $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['set_course_only']) && eF_checkParameter($_GET['set_course_only'], 'id')) { //The administrator asked to activate a lesson
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
        echo urlencode(_UNAUTHORIZEDACCESS);
        exit;
    }
    try {
        $lesson = new EfrontLesson($_GET['set_course_only']);
        $lesson -> lesson['course_only'] = 1;





        $lesson -> persist();
        echo "1";
    } catch (Exception $e) {
        $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['add_lesson']) || (isset($_GET['edit_lesson']) && eF_checkParameter($_GET['edit_lesson'], 'id'))) { //The administrator asked to add or edit a lesson

    //Set the form post target in correspondance to the current function we are performing
    if (isset($_GET['add_lesson'])) {
        $post_target = 'add_lesson=1';
    } else {
        $post_target = 'edit_lesson='.$_GET['edit_lesson'];
        $smarty -> assign("T_LESSON_OPTIONS", array(array('text' => _LESSONSETTINGS, 'image' => "16x16/generic.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=lessons&lesson_settings=".$_GET['edit_lesson'])));
    }

    $form = new HTML_QuickForm("add_lessons_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=lessons&".$post_target, "", null, true); //Build the form
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register our custom input check function
    $form -> addElement('text', 'name', _LESSONNAME, 'class = "inputText"'); //The lesson name, it is required and of type 'text'

    $form -> addRule('name', _THEFIELD.' "'._LESSONNAME.'" '._ISMANDATORY, 'required', null, 'client');
    //$form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');
    if ($GLOBALS['configuration']['onelanguage'] != true){
        $form -> addElement('select', 'languages_NAME', _LANGUAGE, EfrontSystem :: getLanguages(true, true)); //Add a language select box to the form
    }

    try { //If there are no direction set, redirect to add direction page
        $directionsTree = new EfrontDirectionsTree();
        if (sizeof($directionsTree -> tree) == 0) {
            eF_redirect("".basename($_SERVER['PHP_SELF']).'?ctg=directions&add_direction=1&message='.urlencode(_YOUMUSTFIRSTCREATEDIRECTION).'&message_type=failure');
        }
        $form -> addElement('select', 'directions_ID', _DIRECTION, $directionsTree -> toPathString()); //Append a directions select box to the form
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }

    $form -> addElement('text', 'price', _PRICE, 'class = "inputText" style = "width:50px"'); //Add the price, active and submit button to the form
    $form -> addElement('advcheckbox', 'active', _ACTIVENEUTRAL, null, null, array(0, 1));
    $form -> addElement('advcheckbox', 'show_catalog', _SHOWLESSONINCATALOG, null, null, array(0, 1));
    $form -> addElement('radio', 'course_only', _LESSONAVAILABLE, _COURSEONLY, 1, 'onclick = "$$(\'tr.only_lesson\').each(function(s) {s.hide()})"');
    $form -> addElement('radio', 'course_only', _LESSONAVAILABLE, _DIRECTLY, 0, 'onclick = "$$(\'tr.only_lesson\').each(function(s) {s.show()});if ($(\'recurring\').options[$(\'recurring\').selectedIndex].value == 0) {$(\'duration_row\').hide();}"');

     $recurringOptions = array(0 => _NO, 'D' => _DAILY, 'W' => _WEEKLY, 'M' => _MONTHLY, 'Y' => _YEARLY);
     $recurringDurations = array('D' => array_combine(range(1, 90), range(1, 90)),
                                     'W' => array_combine(range(1, 52), range(1, 52)),
                                     'M' => array_combine(range(1, 24), range(1, 24)),
                                     'Y' => array_combine(range(1, 5), range(1, 5))); //Imposed by paypal interface
     $form -> addElement('select', 'recurring', _SUBSCRIPTION, $recurringOptions, 'id = "recurring" onchange = "$(\'duration_row\').show();$$(\'span\').each(function (s) {if (s.id.match(\'_duration\')) {s.hide();}});if (this.selectedIndex) {$(this.options[this.selectedIndex].value+\'_duration\').show();} else {$(\'duration_row\').hide();}"');
     $form -> addElement('select', 'D_duration', _DAYSCONDITIONAL, $recurringDurations['D']);
     $form -> addElement('select', 'W_duration', _WEEKSCONDITIONAL, $recurringDurations['W']);
     $form -> addElement('select', 'M_duration', _MONTHSCONDITIONAL, $recurringDurations['M']);
     $form -> addElement('select', 'Y_duration', _YEARSCONDITIONAL, $recurringDurations['Y']);

     $lessons = EfrontLesson :: getLessons();
     $lessonsList = array(0 => _SELECTLESSON, -1 => '---------------');
     foreach ($lessons as $value) {
         $lessonsList[$value['id']] = $value['name'];
     }

     $form -> addElement('text', 'max_users', _MAXIMUMUSERS, 'class = "inputText" style = "width:50px"');
     $form -> addElement('select', 'copy_properties', _COPYPROPERTIESFROM, $lessonsList);
     $form -> addElement('select', 'share_folder', _SHAREFOLDERWITH, $lessonsList, 'id = "share_folder" onchange = "$(\'clone_lesson\').options.selectedIndex=0;this.options.selectedIndex ? $(\'clone_lesson\').disabled = \'disabled\' : $(\'clone_lesson\').disabled = \'\'"');
     $form -> addElement('select', 'clone_lesson', _CLONELESSON, $lessonsList, 'id = "clone_lesson" onchange = "$(\'share_folder\').options.selectedIndex=0;this.options.selectedIndex ? $(\'share_folder\').disabled = \'disabled\' : $(\'share_folder\').disabled = \'\'"');
     $form -> addElement('text', 'duration', _AVAILABLEFOR, 'style = "width:50px;"');
     $form -> addRule('duration', _THEFIELD.' "'._AVAILABLEFOR.'" '._MUSTBENUMERIC, 'numeric', null, 'client');

    if (isset($_GET['edit_lesson'])) { //If we are editing a lesson, we set the default form values to the ones stored in the database
        $editLesson = new EfrontLesson($_GET['edit_lesson']);
        $form -> setDefaults(array('name' => $editLesson -> lesson['name'],
                                   'active' => $editLesson -> lesson['active'],
           'show_catalog' => $editLesson -> lesson['show_catalog'],
                                   'course_only' => $editLesson -> lesson['course_only'],
                                   'directions_ID' => $editLesson -> lesson['directions_ID'],
                                   'languages_NAME' => $editLesson -> lesson['languages_NAME'],
                                   'duration' => $editLesson -> lesson['duration'] ? $editLesson -> lesson['duration'] : '',
                 'share_folder' => $editLesson -> lesson['share_folder'] ? $editLesson -> lesson['share_folder'] : 0,
                                   'max_users' => $editLesson -> lesson['max_users'] ? $editLesson -> lesson['max_users'] : null,
                                   'price' => $editLesson -> lesson['price'],
                                   'recurring' => $editLesson -> options['recurring'],
        $editLesson -> options['recurring'].'_duration' => $editLesson -> options['recurring_duration']));

        $smarty -> assign("T_EDIT_LESSON", $editLesson);
    } else {
        //$form -> addElement('file', 'import_content', _UPLOADLESSONFILE, 'class = "inputText"');
        $form -> setDefaults(array('active' => 1, //For a new lesson, by default active is set to 1 and price to 0
                                   'show_catalog' => 1,
           'price' => 0,
                                   'course_only' => 0,
                                   'languages_NAME' => $GLOBALS['configuration']['default_language']));
    }

    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
        $form -> freeze();
    } else {
        $form -> addElement('submit', 'submit_lesson', _SUBMIT, 'class = "flatButton"');

        if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
            $values = $form -> exportValues();
            if (!$values['share_folder'] || !is_numeric($values['share_folder']) || !is_dir(G_LESSONSPATH.$values['share_folder'])) {
                unset($values['share_folder']);
            }
            $GLOBALS['configuration']['onelanguage'] == true ? $languages_NAME = $GLOBALS['configuration']['default_language']: $languages_NAME = $form -> exportValue('languages_NAME');
            if (isset($_GET['add_lesson'])) { //The second case is when the administrator adds a new lesson
                $fields_insert = array('name' => $form -> exportValue('name'),
                                       'languages_NAME' => $languages_NAME,
                                       'directions_ID' => $form -> exportValue('directions_ID'),
                                       'active' => $form -> exportValue('active'),
                                       'duration' => $form -> exportValue('duration') ? $form -> exportValue('duration') : 0,
                                       'share_folder' => $form -> exportValue('share_folder') ? $form -> exportValue('share_folder') : 0,
                                       'max_users' => $form -> exportValue('max_users') ? $form -> exportValue('max_users') : null,
                        'show_catalog' => $form -> exportValue('show_catalog'),
                                       'course_only' => $form -> exportValue('course_only') == '' ? 0 : $form -> exportValue('course_only'),
                                       'price' => $form -> exportValue('price'));
                try {
                    //If we asked to copy properties for another lesson, initialize it and get its properties (except for recurring options, which are already defined in the same page)
                    if ($values['copy_properties']) {
                        $copyPropertiesLesson = new EfrontLesson($values['copy_properties']);
                        unset($copyPropertiesLesson -> options['recurring']);
                        unset($copyPropertiesLesson -> options['recurring_duration']);
                        $fields_insert['options'] = serialize($copyPropertiesLesson -> options);
                    }

                    //Create the new lesson
                    $newLesson = EfrontLesson :: createLesson($fields_insert);

                    //If a recurring payment is set, set this up to the lesson properties
                    if ($form -> exportValue('price') && $form -> exportValue('recurring') && in_array($form -> exportValue('recurring'), array_keys($recurringOptions))) {
                        $newLesson -> options['recurring'] = $form -> exportValue('recurring');
                        if ($newLesson -> options['recurring']) {
                            $newLesson -> options['recurring_duration'] = $form -> exportValue($newLesson -> options['recurring'].'_duration');
                        }
                        $newLesson -> persist();
                    }
                    //Import file, if any specified
                    if ($values['clone_lesson']) {
                        $cloneLesson = new EfrontLesson($values['clone_lesson']);
                        $file = $cloneLesson -> export();
                        $exportedFile = $file -> copy($newLesson -> getDirectory().'/'.$exportedFile['name']);
                    }
                    if (isset($exportedFile)) {
                        $newLesson -> import($exportedFile);
                    } else {
                        //There was no file imported, then it's safe to add a default completion condition
               $fields = array('lessons_ID' => $newLesson -> lesson['id'],
                               'type' => 'all_units',
                               'relation' => 'and');
                     eF_insertTableData('lesson_conditions', $fields);
                    }

                    if ($newLesson -> lesson['course_only']) { //For course-only lessons, redirect to lessons list, not to "edit lesson" page
                        eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=lessons&message=".urlencode(_SUCCESSFULLYCREATEDLESSON)."&message_type=success");
                    } else {
                        eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=lessons&edit_lesson=".($newLesson -> lesson['id'])."&tab=users&message=".urlencode(_SUCCESSFULLYCREATEDLESSON)."&message_type=success");
                    }
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            } elseif (isset($_GET['edit_lesson'])) { //The first case is when the administrator is editing a lesson
                $fields_update = array('name' => $form -> exportValue('name'),
                                       'directions_ID' => $form -> exportValue('directions_ID'),
                                       'languages_NAME' => $languages_NAME,
                                       'active' => $form -> exportValue('active'),
                                       'duration' => $form -> exportValue('duration') ? $form -> exportValue('duration') : 0,
                                       'share_folder' => $form -> exportValue('share_folder') ? $form -> exportValue('share_folder') : 0,
                                       'max_users' => $form -> exportValue('max_users') ? $form -> exportValue('max_users') : null,
            'show_catalog' => $form -> exportValue('show_catalog'),
                                       'course_only' => $form -> exportValue('course_only'),
                                       'price' => $form -> exportValue('price'));
                if ($values['copy_properties']) {
                    $copyPropertiesLesson = new EfrontLesson($values['copy_properties']);
                    unset($copyPropertiesLesson -> options['recurring']);
                    unset($copyPropertiesLesson -> options['recurring_duration']);
                    $fields_update['options'] = serialize($copyPropertiesLesson -> options);
                }
                $editLesson -> lesson = array_merge($editLesson -> lesson, $fields_update);

                if ($form -> exportValue('price') && $form -> exportValue('recurring') && in_array($form -> exportValue('recurring'), array_keys($recurringOptions))) {
                    $editLesson -> options['recurring'] = $form -> exportValue('recurring');
                    if ($editLesson -> options['recurring']) {
                        $editLesson -> options['recurring_duration'] = $form -> exportValue($editLesson -> options['recurring'].'_duration');
                    }
                } else {
                    unset($editLesson -> options['recurring']);
                }
                try {
                    $editLesson -> persist();

                    $lesson_forum = eF_getTableData("f_forums", "id", "lessons_ID=".$_GET['edit_lesson']); //update lesson's forum and chat names as well
                    if (sizeof($lesson_forum) > 0) {
                        eF_updateTableData("f_forums", array('title' => $form -> exportValue('name')), "id=".$lesson_forum[0]['id']);
                    }
                    $lesson_chat = eF_getTableData("chatrooms", "id", "lessons_ID=".$_GET['edit_lesson']);
                    if (sizeof($lesson_chat) > 0) {
                        eF_updateTableData("chatrooms", array('name' => $form -> exportValue('name')), "id=".$lesson_chat[0]['id']);
                    }
                    eF_redirect(basename(basename($_SERVER['PHP_SELF'])).'?ctg=lessons&message='.urlencode(_LESSONUPDATED).'&message_type=success');
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }
        }
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty); //Create a smarty renderer
    $renderer -> setRequiredTemplate (
           '{$html}{if $required}
             &nbsp;<span class = "formRequired">*</span>
            {/if}');
    $renderer->setErrorTemplate(
        '{$html}{if $error}
             <div class = "formError">{$error}</div>
         {/if}'
         );

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created

    $smarty -> assign('T_LESSON_FORM', $renderer -> toArray()); //Assign the form to the template
    if (isset($_GET['edit_lesson'])) { //If we are editing a lesson, get the information needed to build the users to lesson list
        try {
            if ($editLesson -> lesson['course_only']) {
                $smarty -> assign("T_STANDALONE_LESSON", 0);
            } else {
                $smarty -> assign("T_STANDALONE_LESSON", 1);
            }
            $lessonUsers = $editLesson -> getUsers(); //Get all users that have this lesson
            $nonLessonUsers = $editLesson -> getNonUsers(); //Get all the users that can, but don't, have this lesson
            $users = array_merge($lessonUsers, $nonLessonUsers); //Merge users to a single array, which will be useful for displaying them
            $roles = EfrontLessonUser :: getLessonsRoles(true);
            //$roles = eF_getTableDataFlat("user_types", "*", "active=1 AND basic_user_type!='administrator'");    //Get available roles
            //sizeof($roles) > 0 ? $roles = array_combine($roles['id'], $roles['name']) : $roles = array();                                             //Match keys with values, it's more practical this way
            $roles = array('student' => _STUDENT, 'professor' => _PROFESSOR) + $roles; //Append basic user types to the beginning of the array
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
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
                $smarty -> assign("T_ROLES", $roles);
                $smarty -> assign("T_ALL_USERS", $users);
                $smarty -> assign("T_LESSON_USERS", array_keys($lessonUsers)); //We assign separately the lesson's users, to know when to display the checkboxes as "checked"
                $smarty -> display('administrator.tpl');
                exit;
            }
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
        if (isset($_GET['postAjaxRequest'])) {
            try {
                if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
                    isset($_GET['user_type']) && in_array($_GET['user_type'], array_keys($roles)) ? $userType = $_GET['user_type'] : $userType = 'student';
                    if (in_array($_GET['login'], array_keys($nonLessonUsers))) {
                        $editLesson -> addUsers($_GET['login'], $userType);
                    }
                    if (in_array($_GET['login'], array_keys($lessonUsers))) {
                        $userType != $lessonUsers[$_GET['login']]['role'] ? $editLesson -> setRoles($_GET['login'], $userType) : $editLesson -> removeUsers($_GET['login']);
                    }
                } else if (isset($_GET['addAll'])) {
                    $userTypes = array();
                    isset($_GET['filter']) ? $nonLessonUsers = eF_filterData($nonLessonUsers, $_GET['filter']) : null;
                    foreach ($nonLessonUsers as $user) {
                        $user['user_types_ID'] ? $userTypes[] = $user['user_types_ID'] : $userTypes[] = $user['basic_user_type'];
                    }
                    $editLesson -> addUsers(array_keys($nonLessonUsers), $userTypes);
                } else if (isset($_GET['removeAll'])) {
                    isset($_GET['filter']) ? $lessonUsers = eF_filterData($lessonUsers, $_GET['filter']) : null;
                    $editLesson -> removeUsers(array_keys($lessonUsers));
                }
                exit;
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    }
} else if (isset($_GET['lesson_info']) && eF_checkParameter($_GET['lesson_info'], 'id')) {
    /***/
    require_once("lesson_information.php");
} else if (isset($_GET['lesson_settings']) && eF_checkParameter($_GET['lesson_settings'], 'id')) {
    $currentLesson = new EfrontLesson($_GET['lesson_settings']);
    $smarty -> assign("T_CURRENT_LESSON", $currentLesson);
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';
    $baseUrl = 'ctg=lessons&lesson_settings='.$currentLesson -> lesson['id'];
    $smarty -> assign("T_BASE_URL", $baseUrl);
    require_once "lesson_settings.php";
} else { //The default action is to just print a list with the lessons defined in the system
//    $filesystem = new FileSystemTree(G_LESSONSPATH, true);
    $form = new HTML_QuickForm("import_lesson_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=lessons", "", null, true); //Build the form
    $form -> addElement('file', 'import_content', _UPLOADLESSONFILE, 'class = "inputText"');
    $form -> addElement('submit', 'submit_lesson', _SUBMIT, 'class = "flatButton"');
    try {
        if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
            $newLesson = EfrontLesson :: createLesson();
            $filesystem = new FileSystemTree($newLesson -> getDirectory(), true);
            $file = $filesystem -> uploadFile('import_content', $newLesson -> getDirectory());
            $exportedFile = $file;
            $newLesson -> import($exportedFile, false, true);
        }
    } catch (EfrontFileException $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty); //Create a smarty renderer
    $renderer -> setRequiredTemplate (
           '{$html}{if $required}
             &nbsp;<span class = "formRequired">*</span>
            {/if}');
    $renderer->setErrorTemplate(
        '{$html}{if $error}
             <div class = "formError">{$error}</div>
         {/if}'
         );
    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created
    $smarty -> assign('T_IMPORT_LESSON_FORM', $renderer -> toArray()); //Assign the form to the template
    $lessons = EFrontLesson :: getLessons();
    $directionsTree = new EfrontDirectionsTree();
    $directionPaths = $directionsTree -> toPathString();
    $languages = EfrontSystem :: getLanguages(true);
    if (G_VERSIONTYPE == 'enterprise') {
        $result = eF_getTableDataFlat("lessons LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_lesson_offers_skill.lesson_ID = lessons.id","lessons.id, count(skill_ID) as skills_offered","lessons.archive=0","","id");
        foreach ($result['id'] as $key => $lesson_id) {
            $lessons[$lesson_id]['skills_offered'] = $result['skills_offered'][$key];
        }
    }
    //Perform a query to get all the 'student' and 'student-like' users of every lesson 
    $result = eF_getTableDataFlat("lessons l,users_to_lessons ul left outer join user_types ut on ul.user_type=ut.id", "l.id,count(*)", "l.id=ul.lessons_ID and (ul.user_type='student' or (ul.user_type = ut.id and ut.basic_user_type = 'student'))", "", "l.id" );
    if (sizeof($result) > 0) {
        $lessonUsers = array_combine($result['id'], $result['count(*)']);
    }
    foreach ($lessons as $key => $lesson) {
        if (isset($lessonUsers[$key])) {
            $lessons[$key]['students'] = $lessonUsers[$key];
        } else {
            $lessons[$key]['students'] = 0;
        }
    }
/*

    $tableName  = 'lessonsTable';

    $dataSource = $lessons;

    include "sorted_table.php";

*/
    if (isset($_GET['ajax']) && $_GET['ajax'] == 'lessonsTable') {
        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
        if (isset($_GET['sort'])) {
            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
            $lessons = eF_multiSort($lessons, $_GET['sort'], $order);
        }
        if (isset($_GET['filter'])) {
            $lessons = eF_filterData($lessons, $_GET['filter']);
        }
        $smarty -> assign("T_LESSONS_SIZE", sizeof($lessons));
        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
            $lessons = array_slice($lessons, $offset, $limit);
        }
        foreach ($lessons as $key => $lesson) {
            $obj = new EfrontLesson($lesson);
            //$lessons[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=lessons&edit_lesson='.$lesson['id']);
            $lessons[$key]['direction_name'] = $directionPaths[$lesson['directions_ID']];
            $lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
            $lessons[$key]['price_string'] = $obj -> lesson['price_string'];
            //$lessons[$key]['students']       = sizeof($obj -> getUsers('student'));            
        }
        $smarty -> assign("T_LESSONS_DATA", $lessons);
        $smarty -> display('administrator.tpl');
        exit;
    }
}
?>
