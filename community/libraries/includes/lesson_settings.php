<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$loadScripts[] = 'scriptaculous/dragdrop';
$loadScripts[] = 'includes/lesson_settings';

$options = array(
   0 => array('image' => '16x16/generic.png', 'title' => _LESSONOPTIONS, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl, 'selected' => !isset($_GET['op']) ? true : false),
   1 => array('image' => '16x16/layout.png', 'title' => _LAYOUT, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=lesson_layout', 'selected' => isset($_GET['op']) && $_GET['op'] == 'lesson_layout' ? true : false),
   2 => array('image' => '16x16/refresh.png', 'title' => _RESTARTLESSON, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=reset_lesson' , 'selected' => isset($_GET['op']) && $_GET['op'] == 'reset_lesson' ? true : false),
   3 => array('image' => '16x16/import.png', 'title' => _IMPORTLESSON, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=import_lesson', 'selected' => isset($_GET['op']) && $_GET['op'] == 'import_lesson' ? true : false),
   4 => array('image' => '16x16/export.png', 'title' => _EXPORTLESSON, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=export_lesson', 'selected' => isset($_GET['op']) && $_GET['op'] == 'export_lesson' ? true : false),
   );
if (!$currentLesson -> lesson['course_only']) {
 $options[] = array('image' => '16x16/users.png', 'title' => _LESSONUSERS, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=lesson_users', 'selected' => isset($_GET['op']) && $_GET['op'] == 'lesson_users' ? true : false);
}

   //Unset values based on user's type restrictions
if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
    unset($options[2]);
    unset($options[3]);
    unset($options[4]);
}
if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
    unset($options[5]);
}
//Reindex options so that indices are serial starting from 0 (this way they display correctly)
$options = array_values($options);

$smarty -> assign("T_TABLE_OPTIONS", $options);

if ($_GET['op'] == 'reset_lesson') {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    /*Reset lesson part*/
    $form = new HTML_QuickForm("reset_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=reset_lesson', "", null, true);

    $form -> addElement('checkbox', 'options[content]', _CONTENT, null, 'class = "inputCheckBox" id = "initialize_content" onclick = "$$(\'input.contentDerivative\').each(function (s) {s.checked = $(\'initialize_content\').checked})"');
    if ($GLOBALS['configuration']['disable_tests'] != 1) {
        $form -> addElement('checkbox', 'options[tests]', _TESTS, '<span class = "infoCell">('._DEPENDSONCONTENT.')</span>', 'class = "inputCheckBox contentDerivative" style = "vertical-align:middle" onclick = "if ($(\'initialize_content\').checked) this.checked = 1"');
    }
    $form -> addElement('checkbox', 'options[questions]', _QUESTIONS, '<span class = "infoCell">('._DEPENDSONCONTENT.')</span>', 'class = "inputCheckBox contentDerivative" style = "vertical-align:middle" onclick = "if ($(\'initialize_content\').checked) this.checked = 1"');
    $form -> addElement('checkbox', 'options[rules]', _ACCESSRULES, '<span class = "infoCell">('._DEPENDSONCONTENT.')</span>', 'class = "inputCheckBox contentDerivative" style = "vertical-align:middle" onclick = "if ($(\'initialize_content\').checked) this.checked = 1"');
    $form -> addElement('checkbox', 'options[conditions]', _LESSONCONDITIONS, '<span class = "infoCell">('._DEPENDSONCONTENT.')</span>', 'class = "inputCheckBox contentDerivative" style = "vertical-align:middle" onclick = "if ($(\'initialize_content\').checked) this.checked = 1"');
    if ($GLOBALS['configuration']['disable_comments'] != 1) {
        $form -> addElement('checkbox', 'options[comments]', _COMMENTS, '<span class = "infoCell">('._DEPENDSONCONTENT.')</span>', 'class = "inputCheckBox contentDerivative" style = "vertical-align:middle" onclick = "if ($(\'initialize_content\').checked) this.checked = 1"');
    }
    $form -> addElement('checkbox', 'options[users]', _USERS, null, 'class = "inputCheckBox"');
    if ($GLOBALS['configuration']['disable_news'] != 1) {
        $form -> addElement('checkbox', 'options[news]', _ANNOUNCEMENTS, null, 'class = "inputCheckBox"');
    }
    $form -> addElement('checkbox', 'options[files]', _FILES, null, 'class = "inputCheckBox"');
    if ($GLOBALS['configuration']['disable_calendar'] != 1) {
        $form -> addElement('checkbox', 'options[calendar]', _CALENDAR, null, 'class = "inputCheckBox"');
    }
    if ($GLOBALS['configuration']['disable_glossary'] != 1) {
        $form -> addElement('checkbox', 'options[glossary]', _GLOSSARY, null, 'class = "inputCheckBox"');
    }
    if ($GLOBALS['configuration']['disable_projects'] != 1) {
        $form -> addElement('checkbox', 'options[projects]', _PROJECTS, null, 'class = "inputCheckBox"');
    }
    $form -> addElement('checkbox', 'options[tracking]', _USERTRACKINGINFORMATION, null, 'class = "inputCheckBox"');
    $form -> addElement('checkbox', 'options[scheduling]', _SCHEDULING, null, 'class = "inputCheckBox"');





    $form -> addElement('checkbox', 'options[modules]', _MODULES, null, 'class = "inputCheckBox"');
 $form -> addElement('checkbox', 'options[events]', _EVENTS, null, 'class = "inputCheckBox"');

    $form -> addElement('submit', 'submit_reset_lesson', _SUBMIT, 'onclick = "return confirm(\''._AREYOUSUREYOUWANTTOINITIALIZELESSON.'\')" class = "flatButton"');

    if ($form -> isSubmitted() && $form -> validate()) {
        $values = $form -> exportValues();
        $currentLesson -> initialize(array_keys($values['options']));

        $message = _RESTARTLESSONCOMPLETED;
        $message_type = 'success';
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form -> accept($renderer);
    $smarty -> assign('T_RESET_LESSON_FORM', $renderer -> toArray());
} elseif ($_GET['op'] == 'import_lesson') {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
        eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    /* Import part */
    $form = new HTML_QuickForm("import_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=import_lesson', "", null, true);

    $form -> addElement('checkbox', 'options[content]', _CONTENT, null, 'class = "inputCheckBox" id = "import_content" onclick = "$$(\'input.contentDerivative\').each(function (s) {s.checked = $(\'import_content\').checked})"');
    if ($GLOBALS['configuration']['disable_tests'] != 1) {
        $form -> addElement('checkbox', 'options[tests]', _TESTS, '<span class = "infoCell">('._DEPENDSONCONTENT.')</span>', 'class = "inputCheckBox contentDerivative" style = "vertical-align:middle" onclick = "if ($(\'import_content\').checked) this.checked = 1"');
    }
    $form -> addElement('checkbox', 'options[questions]', _QUESTIONS, '<span class = "infoCell">('._DEPENDSONCONTENT.')</span>', 'class = "inputCheckBox contentDerivative" style = "vertical-align:middle" onclick = "if ($(\'import_content\').checked) this.checked = 1"');
    $form -> addElement('checkbox', 'options[rules]', _ACCESSRULES, '<span class = "infoCell">('._DEPENDSONCONTENT.')</span>', 'class = "inputCheckBox contentDerivative" style = "vertical-align:middle" onclick = "if ($(\'import_content\').checked) this.checked = 1"');
    $form -> addElement('checkbox', 'options[conditions]', _LESSONCONDITIONS, '<span class = "infoCell">('._DEPENDSONCONTENT.')</span>', 'class = "inputCheckBox contentDerivative" style = "vertical-align:middle" onclick = "if ($(\'import_content\').checked) this.checked = 1"');
    if ($GLOBALS['configuration']['disable_comments'] != 1) {
        $form -> addElement('checkbox', 'options[comments]', _COMMENTS, '<span class = "infoCell">('._DEPENDSONCONTENT.')</span>', 'class = "inputCheckBox contentDerivative" style = "vertical-align:middle" onclick = "if ($(\'import_content\').checked) this.checked = 1"');
    }
    $form -> addElement('checkbox', 'options[users]', _USERS, null, 'class = "inputCheckBox"');
    if ($GLOBALS['configuration']['disable_news'] != 1) {
        $form -> addElement('checkbox', 'options[news]', _ANNOUNCEMENTS, null, 'class = "inputCheckBox"');
    }
    $form -> addElement('checkbox', 'options[files]', _FILES, null, 'class = "inputCheckBox"');
    if ($GLOBALS['configuration']['disable_calendar'] != 1) {
        $form -> addElement('checkbox', 'options[calendar]', _CALENDAR, null, 'class = "inputCheckBox"');
    }
    if ($GLOBALS['configuration']['disable_glossary'] != 1) {
        $form -> addElement('checkbox', 'options[glossary]', _GLOSSARY, null, 'class = "inputCheckBox"');
    }
    if ($GLOBALS['configuration']['disable_projects'] != 1) {
        $form -> addElement('checkbox', 'options[projects]', _PROJECTS, null, 'class = "inputCheckBox"');
    }
    $form -> addElement('checkbox', 'options[tracking]', _USERTRACKINGINFORMATION, null, 'class = "inputCheckBox"');
    $form -> addElement('checkbox', 'options[scheduling]', _SCHEDULING, null, 'class = "inputCheckBox"');





    $form -> addElement('checkbox', 'options[modules]', _MODULES, null, 'class = "inputCheckBox"');

    $form -> addElement('file', 'file_upload', null, 'class = "inputText"'); //Lesson file
    $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024); //getUploadMaxSize returns size in KB

    $form -> addElement('text', 'url_upload', _UPLOADFILEFROMURL, 'class = "inputText"');
    $form -> addElement('submit', 'submit_import_lesson', _SUBMIT, 'class = "flatButton"');

    $smarty -> assign("T_MAX_FILESIZE", FileSystemTree :: getUploadMaxSize());

    if ($form -> isSubmitted() && $form -> validate()) {
        try {
            $values = $form -> exportValues();
            $currentLesson -> initialize(array_keys($values['options']));

            $filesystem = new FileSystemTree($currentLesson -> getDirectory());
            $urlUpload = $values['url_upload'];
            if ($urlUpload != "" ) {
             FileSystemTree :: checkFile($urlUpload);
             $urlArray = explode("/", $urlUpload);
                $urlFile = urldecode($urlArray[sizeof($urlArray) - 1]);
                if (!copy($urlUpload, $currentLesson -> getDirectory().$urlFile)) {
                    $error = error_get_last();
                    throw new Exception(_PROBLEMUPLOADINGFILE.': '.$error['message']);
                } else {
                 $uploadedFile = new EfrontFile($currentLesson -> getDirectory().$urlFile);
                    $currentLesson -> import($uploadedFile);
                }
            } else {
             $uploadedFile = $filesystem -> uploadFile('file_upload', $currentLesson -> getDirectory());
             $currentLesson -> import($uploadedFile);
            }
            $smarty -> assign("T_REFRESH_SIDE", 1);

            $message = _LESSONIMPORTEDSUCCESFULLY;
            $message_type = 'success';
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message = _PROBLEMIMPORTINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form -> accept($renderer);
    $smarty -> assign('T_IMPORT_LESSON_FORM', $renderer -> toArray());
} elseif ($_GET['op'] == 'export_lesson') {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }

    /* Export part */
    $form = new HTML_QuickForm("export_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=export_lesson', "", null, true);
    $form -> addElement('checkbox', 'export_files', _EXPORTFILES, null, 'class = "inputCheckBox"');
    $form -> addElement('submit', 'submit_export_lesson', _EXPORT, 'class = "flatButton"');

    try {
        $currentExportedFile = new EfrontFile($currentUser -> user['directory'].'/temp/'.EfrontFile :: encode($currentLesson -> lesson['name']).'.zip');
        $smarty -> assign("T_EXPORTED_FILE", $currentExportedFile);
    } catch (Exception $e) {}

    if ($form -> isSubmitted() && $form -> validate()) {
        try {
            $file = $currentLesson -> export('all', true, $form -> exportValue('export_files'));
            $smarty -> assign("T_NEW_EXPORTED_FILE", $file);

            $message = _LESSONEXPORTEDSUCCESFULLY;
            $message_type = 'success';
        } catch (Exception $e) {
         handleNormalFlowExceptions($e);
        }
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form -> accept($renderer);
    $smarty -> assign('T_EXPORT_LESSON_FORM', $renderer -> toArray());

} elseif ($_GET['op'] == 'lesson_users') {
    if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    if ($_admin_) {
        $smarty -> assign("T_BASE_URL", 'ctg=lessons&lesson_settings='.$currentLesson -> lesson['id']);
    } else {
        $smarty -> assign("T_BASE_URL", 'ctg=settings');
    }
    try {
        $lessonUsers = $currentLesson -> getUsers(); //Get all users that have this lesson
        unset($lessonUsers[$currentUser -> login]); //Remove the current user from the list, he can't set parameters for his self!
        //COMMENTED OUT BECAUSE WE DON'T WANT TO SET USERS FROM THIS LIST
        //$nonLessonUsers = $currentLesson -> getNonUsers();                 //Get all the users that can, but don't, have this lesson
        //$users          = array_merge($lessonUsers, $nonLessonUsers);      //Merge users to a single array, which will be useful for displaying them
  $users = $lessonUsers;

        foreach ($users as $key => $user) {
            in_array($key, array_keys($nonLessonUsers)) ? $users[$key]['in_lesson'] = false : $users[$key]['in_lesson'] = true;
        }

  $roles = EfrontLessonUser :: getLessonsRoles(true);

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
                $users = array_slice($users, $offset, $limit, true);
            }

            $smarty -> assign("T_ROLES", $roles);
            $smarty -> assign("T_ALL_USERS", $users);
            $smarty -> assign("T_LESSON_USERS", array_keys($lessonUsers)); //We assign separately the lesson's users, to know when to display the checkboxes as "checked"
            $smarty -> assign("T_CURRENT_USER", $currentUser);
            $smarty -> display('includes/lesson_settings.tpl');
            exit;
        }
        if (isset($_GET['ajax']) && isset($_GET['reset_user'])) {
         $user = EfrontUserFactory :: factory($_GET['reset_user']);
         $user -> resetProgressInLesson($currentLesson);
         exit;
        }

    } catch (Exception $e) {
     handleNormalFlowExceptions($e);
    }

    if (isset($_GET['postAjaxRequest'])) {
        try {
            if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
                isset($_GET['user_type']) && in_array($_GET['user_type'], array_keys($roles)) ? $userType = $_GET['user_type'] : $userType = 'student';
                if (in_array($_GET['login'], array_keys($nonLessonUsers))) {
                    $currentLesson -> addUsers($_GET['login'], $userType);
                }
                if (in_array($_GET['login'], array_keys($lessonUsers))) {
                    $userType != $lessonUsers[$_GET['login']]['role'] ? $currentLesson -> setRoles($_GET['login'], $userType) : $currentLesson -> archiveLessonUsers($_GET['login']);
                }
            } else if (isset($_GET['addAll'])) {
                isset($_GET['filter']) ? $nonLessonUsers = eF_filterData($nonLessonUsers, $_GET['filter']) : null;
                $currentLesson -> addUsers(array_keys($nonLessonUsers));
            } else if (isset($_GET['removeAll'])) {
                isset($_GET['filter']) ? $lessonUsers = eF_filterData($lessonUsers, $_GET['filter']) : null;
                $currentLesson -> archiveLessonUsers(array_keys($lessonUsers));
            }
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        exit;
    }
} elseif ($_GET['op'] == 'lesson_layout') {
    $defaultPositions = unserialize($currentLesson -> options['default_positions']);
//pr($defaultPositions);
    $result = eF_getTableData("modules", "*");
    foreach ($result as $value) {
        $moduleInfo[$value['className']] = $value;
    }
    $curretType = $currentUser -> user['user_type'];
    $currentUser -> user['user_type'] = 'student';
    $modules = eF_loadAllModules();

    foreach($modules as $key => $module) {
        if (method_exists($module, 'getLessonModule') && $currentLesson -> options[$key]) {
            $lessonModules[$key] = $moduleInfo[$key];
        }
    }
    $currentUser -> user['user_type'] = $currentType;
    //pr($lessonModules);
    $smarty -> assign("T_LESSON_MODULES", $lessonModules);
    $smarty -> assign("T_LESSON_ID", $currentLesson -> lesson['id']);
//pr($currentLesson -> options);
    $invalidOptions = array();
    !$currentLesson -> options['content_tree'] ? $invalidOptions['moduleContentTree'] = 1 : null;
    !$currentLesson -> options['projects'] ? $invalidOptions['moduleProjectsList'] = 1 : null;
    !$currentLesson -> options['forum'] ? $invalidOptions['moduleForumList'] = 1 : null;
    !$currentLesson -> options['comments'] ? $invalidOptions['moduleComments'] = 1 : null;
    !$currentLesson -> options['calendar'] ? $invalidOptions['moduleCalendar'] = 1 : null;
    !$currentLesson -> options['digital_library'] ? $invalidOptions['moduleDigitalLibrary'] = 1 : null;
    !$currentLesson -> options['news'] ? $invalidOptions['moduleNewsList'] = 1 : null;

    $smarty -> assign("T_INVALID_OPTIONS", $invalidOptions);

//pr($defaultPositions);
    $smarty -> assign("T_DEFAULT_POSITIONS", $defaultPositions);
} else {
    $lessonSettings['theory'] = array('text' => _THEORY, 'image' => "32x32/theory.png", 'onClick' => 'activate(this, \'theory\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['theory']) && $currentLesson -> options['theory'] ? null : 'inactiveImage');
    $lessonSettings['examples'] = array('text' => _EXAMPLES, 'image' => "32x32/examples.png", 'onClick' => 'activate(this, \'examples\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['examples']) && $currentLesson -> options['examples'] ? null : 'inactiveImage');
    if ($GLOBALS['configuration']['disable_projects'] != 1) {
        $lessonSettings['projects'] = array('text' => _PROJECTS, 'image' => "32x32/projects.png", 'onClick' => 'activate(this, \'projects\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['projects']) && $currentLesson -> options['projects'] ? null : 'inactiveImage');
    }
    if ($GLOBALS['configuration']['disable_tests'] != 1) {
        $lessonSettings['tests'] = array('text' => _TESTS, 'image' => "32x32/tests.png", 'onClick' => 'activate(this, \'tests\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['tests']) && $currentLesson -> options['tests'] ? null : 'inactiveImage');
    }





    if ($GLOBALS['configuration']['disable_feedback'] != 1) {
        $lessonSettings['feedback'] = array('text' => _FEEDBACK, 'image' => "32x32/feedback.png", 'onClick' => 'activate(this, \'feedback\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['feedback']) && $currentLesson -> options['feedback'] ? null : 'inactiveImage');
    }

 $lessonSettings['rules'] = array('text' => _ACCESSRULES, 'image' => "32x32/rules.png", 'onClick' => 'activate(this, \'rules\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['rules']) && $currentLesson -> options['rules'] ? null : 'inactiveImage');
    if ($GLOBALS['configuration']['disable_forum'] != 1) {
        $lessonSettings['forum'] = array('text' => _FORUM, 'image' => "32x32/forum.png", 'onClick' => 'activate(this, \'forum\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['forum']) && $currentLesson -> options['forum'] ? null : 'inactiveImage');
    }
    if ($GLOBALS['configuration']['disable_comments'] != 1) {
        $lessonSettings['comments'] = array('text' => _COMMENTS, 'image' => "32x32/note.png", 'onClick' => 'activate(this, \'comments\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['comments']) && $currentLesson -> options['comments'] ? null : 'inactiveImage');
    }
    if ($GLOBALS['configuration']['disable_news'] != 1) {
        $lessonSettings['news'] = array('text' => _ANNOUNCEMENTS, 'image' => "32x32/announcements.png", 'onClick' => 'activate(this, \'news\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['news']) && $currentLesson -> options['news'] ? null : 'inactiveImage');
    }






    $lessonSettings['scorm'] = array('text' => _SCORM, 'image' => "32x32/scorm.png", 'onClick' => 'activate(this, \'scorm\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['scorm']) && $currentLesson -> options['scorm'] ? null : 'inactiveImage');
 $lessonSettings['ims'] = array('text' => _IMS, 'image' => "32x32/autocomplete.png", 'onClick' => 'activate(this, \'ims\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['ims']) && $currentLesson -> options['ims'] ? null : 'inactiveImage');
    $lessonSettings['smart_content'] = array('text' => _SMARTCONTENT, 'image' => "32x32/import.png", 'onClick' => 'activate(this, \'smart_content\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => !isset($currentLesson -> options['smart_content']) || $currentLesson -> options['smart_content'] ? null : 'inactiveImage');

    if (sizeof(eF_getTableData("files", "id", "shared=".$currentLesson -> lesson['id'])) > 0) {
        $lessonSettings['digital_library'] = array('text' => _SHAREDFILES, 'image' => "32x32/file_explorer.png", 'onClick' => 'activate(this, \'digital_library\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['digital_library']) && $currentLesson -> options['digital_library'] ? null : 'inactiveImage');
    }
    if ($GLOBALS['configuration']['disable_calendar'] != 1) {
        $lessonSettings['calendar'] = array('text' => _CALENDAR, 'image' => "32x32/calendar.png", 'onClick' => 'activate(this, \'calendar\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['calendar']) && $currentLesson -> options['calendar'] ? null : 'inactiveImage');
    }
    if ($GLOBALS['configuration']['disable_glossary'] != 1) {
        $lessonSettings['glossary'] = array('text' => _GLOSSARY, 'image' => "32x32/glossary.png", 'onClick' => 'activate(this, \'glossary\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['glossary']) && $currentLesson -> options['glossary'] ? null : 'inactiveImage');
    }
    $lessonSettings['auto_complete'] = array('text' => _AUTOCOMPLETE, 'image' => "32x32/autocomplete.png", 'onClick' => 'activate(this, \'auto_complete\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['auto_complete']) && $currentLesson -> options['auto_complete'] ? null : 'inactiveImage');
    $lessonSettings['content_tree'] = array('text' => _CONTENTTREEFIRSTPAGE, 'image' => "32x32/content_tree.png", 'onClick' => 'activate(this, \'content_tree\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['content_tree']) && $currentLesson -> options['content_tree'] ? null : 'inactiveImage');
    $lessonSettings['lesson_info'] = array('text' => _LESSONINFORMATION, 'image' => "32x32/information.png", 'onClick' => 'activate(this, \'lesson_info\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => isset($currentLesson -> options['lesson_info']) && $currentLesson -> options['lesson_info'] ? null : 'inactiveImage');
    if ($GLOBALS['configuration']['disable_bookmarks'] != 1) {
        $lessonSettings['bookmarking'] = array('text' => _BOOKMARKS, 'image' => "32x32/bookmark.png", 'onClick' => 'activate(this, \'bookmarking\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['bookmarking']) && $currentLesson -> options['bookmarking'] ? null : 'inactiveImage');
    }

 $lessonSettings['reports'] = array('text' => _STATISTICS, 'image' => "32x32/reports.png", 'onClick' => 'activate(this, \'reports\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['reports']) && $currentLesson -> options['reports'] ? null : 'inactiveImage');
 $lessonSettings['content_report'] = array('text' => _CONTENTREPORT, 'image' => "32x32/warning.png", 'onClick' => 'activate(this, \'content_report\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['content_report']) && $currentLesson -> options['content_report'] ? null : 'inactiveImage');
 $lessonSettings['print_content'] = array('text' => _PRINTCONTENT, 'image' => "32x32/printer.png", 'onClick' => 'activate(this, \'print_content\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['print_content']) && $currentLesson -> options['print_content'] ? null : 'inactiveImage');
 $lessonSettings['start_resume'] = array('text' => _STARTRESUME, 'image' => "32x32/continue.png", 'onClick' => 'activate(this, \'start_resume\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['start_resume']) && $currentLesson -> options['start_resume'] ? null : 'inactiveImage');
    $lessonSettings['show_percentage'] = array('text' => _COMPLETIONPERCENTAGEBLOCK, 'image' => "32x32/percent.png", 'onClick' => 'activate(this, \'show_percentage\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['show_percentage']) && $currentLesson -> options['show_percentage'] ? null : 'inactiveImage');
    $lessonSettings['show_content_tools'] = array('text' => _UNITOPTIONSBLOCK, 'image' => "32x32/options.png", 'onClick' => 'activate(this, \'show_content_tools\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['show_content_tools']) && $currentLesson -> options['show_content_tools'] ? null : 'inactiveImage');
    $lessonSettings['show_right_bar'] = array('text' => _RIGHTBAR, 'image' => "32x32/hide_right.png", 'onClick' => 'activate(this, \'show_right_bar\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['show_right_bar']) && $currentLesson -> options['show_right_bar'] ? null : 'inactiveImage');
    $lessonSettings['show_left_bar'] = array('text' => _LEFTBAR, 'image' => "32x32/hide_left.png", 'onClick' => 'activate(this, \'show_left_bar\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['show_left_bar']) && $currentLesson -> options['show_left_bar'] ? null : 'inactiveImage');
    $lessonSettings['show_student_cpanel'] = array('text' => _STUDENTCPANEL, 'image' => "32x32/options.png", 'onClick' => 'activate(this, \'show_student_cpanel\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['show_student_cpanel']) && $currentLesson -> options['show_student_cpanel'] ? null : 'inactiveImage');
    $lessonSettings['show_dashboard'] = array('text' => _DASHBOARD, 'image' => "32x32/generic.png", 'onClick' => 'activate(this, \'show_dashboard\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => !isset($currentLesson -> options['show_dashboard']) || $currentLesson -> options['show_dashboard'] ? null : 'inactiveImage');

    if ($GLOBALS['currentTheme'] -> options['sidebar_interface'] == 1 || $GLOBALS['currentTheme'] -> options['sidebar_interface'] == 2) {
  $lessonSettings['show_horizontal_bar'] = array('text' => _SHOWHORIZONTALBAR, 'image' => "32x32/generic.png", 'onClick' => 'activate(this, \'show_horizontal_bar\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => !isset($currentLesson -> options['show_horizontal_bar']) || $currentLesson -> options['show_horizontal_bar'] ? null : 'inactiveImage');
 }





 //$lessonSettings['complete_next_lesson']= array('text' => _MOVETONEXTLESSONONCOMPLETE,'image' => "32x32/options.png", 'onClick' => 'activate(this, \'complete_next_lesson\')',      'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => isset($currentLesson -> options['complete_next_lesson']) && $currentLesson -> options['complete_next_lesson'] ? null : 'inactiveImage');


    ///MODULES6
    if ($currentUser -> getType() == "administrator") {
        $loadedModules = eF_loadAllModules(true);
    }

    foreach ($loadedModules as $module) {
        if ($module -> isLessonModule()) {
            // The $setLanguage variable is defined in globals.php
            if (!in_array("administrator", $module -> getPermittedRoles())) {
                $mod_lang_file = $module -> getLanguageFile($setLanguage);
                if (is_file ($mod_lang_file)) {
                    require_once $mod_lang_file;
                }
            }
            // The $setLanguage variable is defined in globals.php
            if (!in_array("administrator", $module -> getPermittedRoles())) {
                $mod_lang_file = $module -> getLanguageFile($setLanguage);
                if (is_file ($mod_lang_file)) {
                    require_once $mod_lang_file;
                }
            }
            $lessonSettings[$module -> className] = array('text' => $module -> getName(), 'image' => $module->getModuleIcon() ? $module->getModuleIcon() : "32x32/addons.png", 'onClick' => 'activate(this, \''.$module -> className.'\')', 'title' => _CLICKTOTOGGLE, 'group' => 3, 'class' => ($currentLesson -> options[$module -> className] == 1) ? null : 'inactiveImage');
        }
    }

    foreach ($currentLesson -> options as $key => $value) { //Remove activated elements from above list
        if ($value && isset($lessonSettings[$key])) {
            $lessonSettings[$key]['onClick'] = 'activate(this, \''.$key.'\')';
            $lessonSettings[$key]['style'] = 'color:inherit';
        }
    }

    //If the professor's type restricts access to settings, unset all 'onclick' actions
    if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'] != 'change') {
        foreach ($lessonSettings as $key => $value) {
            $lessonSettings[$key]['onClick'] = '';
        }
    }

    $smarty -> assign("T_LESSON_SETTINGS", $lessonSettings);
    $smarty -> assign("T_LESSON_SETTINGS_GROUPS", array(1 => _LESSONOPTIONS, 2 => _LESSONMODULES, 3 => _MODULES));

    if (!isset($currentUser -> coreAccess['settings']) || $currentUser -> coreAccess['settings'] == 'change') {
        if (isset($_GET['ajax']) && isset($_GET['activate']) && in_array($_GET['activate'], array_keys($lessonSettings))) {
            try {
                $currentLesson -> options[$_GET['activate']] = 1;
                $currentLesson -> persist();
                //echo "Option activated";
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } elseif (isset($_GET['ajax']) && isset($_GET['deactivate']) && in_array($_GET['deactivate'], array_keys($lessonSettings))) {
            try {
                $currentLesson -> options[$_GET['deactivate']] = 0;
                $currentLesson -> persist();
                //echo "Option deactivated";
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    }
}
