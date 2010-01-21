<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$loadScripts[] = 'includes/courses';

if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}
$result = eF_getTableDataFlat("courses", "id", "archive = 0");
$systemCourses = $result['id'];
if (isset($_GET['delete_course']) && eF_checkParameter($_GET['delete_course'], 'id') && in_array($_GET['delete_course'], $systemCourses)) {
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        exit;
    }
    try {
        $course = new EfrontCourse($_GET['delete_course']);
        $course -> delete();
    } catch (Exception $e) {
        $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['archive_course']) && eF_checkParameter($_GET['archive_course'], 'login')) { //The administrator asked to delete a course
    try {
     if (isset($currentUser -> coreAccess['courses']) && $currentUser -> coreAccess['courses'] != 'change') {
         throw new Exception(_UNAUTHORIZEDACCESS);
     }
        $course = new Efrontcourse($_GET['archive_course']);
        $course -> archive();
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['deactivate_course']) && eF_checkParameter($_GET['deactivate_course'], 'id') && in_array($_GET['deactivate_course'], $systemCourses)) {
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
        echo urlencode(_UNAUTHORIZEDACCESS);
        exit;
    }
    try {
        $course = new EfrontCourse($_GET['deactivate_course']);
        $course -> course['active'] = 0;
        $course -> persist();
        echo "0";
    } catch (Exception $e) {
        $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['activate_course']) && eF_checkParameter($_GET['activate_course'], 'id') && in_array($_GET['activate_course'], $systemCourses)) {
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
        echo urlencode(_UNAUTHORIZEDACCESS);
        exit;
    }
    try {
        $course = new EfrontCourse($_GET['activate_course']);
        $course -> course['active'] = 1;
        $course -> persist();
        echo "1";
    } catch (Exception $e) {
        $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['add_course']) || (isset($_GET['edit_course']) && eF_checkParameter($_GET['edit_course'], 'id')) && in_array($_GET['edit_course'], $systemCourses)) {

    if (isset($_GET['add_course'])) {
        $post_target = 'add_course=1';
    } else {
        $post_target = 'edit_course='.$_GET['edit_course'];
        $smarty -> assign("T_COURSE_OPTIONS", array(array('text' => _COURSESETTINGS, 'image' => "16x16/generic.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=courses&course=".$_GET['edit_course']."&op=course_info")));
    }


    $form = new HTML_QuickForm("add_courses_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=courses&".$post_target, "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
    //$form -> registerRule('checkNotExist', 'callback', 'eF_checkNotExist');
    $form -> addElement('text', 'name', _COURSENAME, 'class = "inputText"');
    $form -> addRule('name', _THEFIELD.' "'._COURSENAME.'" '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');
    //$form -> addRule('name',  _COURSENAME.' &quot;'.($form -> exportValue('name')).'&quot; '._ALREADYEXISTS, 'checkNotExist', 'course');

    try {
        $directionsTree = new EfrontDirectionsTree();
        if (sizeof($directionsTree -> tree) == 0) {
            eF_redirect("".basename($_SERVER['PHP_SELF']).'?ctg=directions&add_direction=1&message='.urlencode(_TOCREATECOURSEYOUMUSTFIRSTCREATECATEGORY).'&message_type=failure');
        }
        $directions = $directionsTree -> toPathString();
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }

    $form -> addElement('select', 'directions_ID', _DIRECTION, $directions); //Append a directions select box to the form

    if ($GLOBALS['configuration']['onelanguage'] != true){
        $languages = EfrontSystem :: getLanguages(true, true);
        $form -> addElement('select', 'languages_NAME', _LANGUAGE, array_combine(array_keys($languages), $languages));
    }

    $form -> addElement('text', 'price', _PRICE, 'class = "inputText" style = "width:50px"');
    $form -> addElement('advcheckbox', 'active', _ACTIVEFEM, null, null, array(0, 1));
    $form -> addElement('advcheckbox', 'show_catalog', _SHOWCOURSEINCATALOG, null, null, array(0, 1));

    $recurringOptions = array(0 => _NO, 'D' => _DAILY, 'W' => _WEEKLY, 'M' => _MONTHLY, 'Y' => _YEARLY);
    $recurringDurations = array('D' => array_combine(range(1, 90), range(1, 90)),
                                    'W' => array_combine(range(1, 52), range(1, 52)),
                                    'M' => array_combine(range(1, 24), range(1, 24)),
                                    'Y' => array_combine(range(1, 5), range(1, 5))); //Imposed by paypal interface
    $form -> addElement('select', 'recurring', _SUBSCRIPTION, $recurringOptions, 'onchange = "$(\'duration_row\').show();$$(\'span\').each(function (s) {if (s.id.match(\'_duration\')) {s.hide();}});if (this.selectedIndex) {$(this.options[this.selectedIndex].value+\'_duration\').show();} else {$(\'duration_row\').hide();}"');
    $form -> addElement('select', 'D_duration', _DAYSCONDITIONAL, $recurringDurations['D']);
    $form -> addElement('select', 'W_duration', _WEEKSCONDITIONAL, $recurringDurations['W']);
    $form -> addElement('select', 'M_duration', _MONTHSCONDITIONAL, $recurringDurations['M']);
    $form -> addElement('select', 'Y_duration', _YEARSCONDITIONAL, $recurringDurations['Y']);

    $form -> addElement('text', 'max_users', _MAXIMUMUSERS, 'class = "inputText" style = "width:50px"');
    $form -> addElement('text', 'duration', _AVAILABLEFOR, 'style = "width:50px;"');
    $form -> addRule('duration', _THEFIELD.' "'._AVAILABLEFOR.'" '._MUSTBENUMERIC, 'numeric', null, 'client');

    if (isset($_GET['edit_course'])) {
        $editCourse = new EfrontCourse($_GET['edit_course']);

        $smarty -> assign('T_EDIT_COURSE', $editCourse);
        $form -> setDefaults(array('name' => $editCourse -> course['name'],
                                   'active' => $editCourse -> course['active'],
                                   'show_catalog' => $editCourse -> course['show_catalog'],
                                   'languages_NAME' => $editCourse -> course['languages_NAME'],
                                   'duration' => $editCourse -> course['duration'] ? $editCourse -> course['duration'] : '',
                                   'max_users' => $editCourse -> course['max_users']? $editCourse -> course['max_users']: null,
                 'directions_ID' => $editCourse -> course['directions_ID'],
                                   'price' => $editCourse -> course['price'],
                                   'recurring' => $editCourse -> options['recurring'],
        $editCourse -> options['recurring'].'_duration' => $editCourse -> options['recurring_duration']));
    } else {
        $form -> setDefaults(array('active' => 1,
                                   'show_catalog' => 1,
                 'price' => 0,
                                   'languages_NAME' => $GLOBALS['configuration']['default_language']));
    }

    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
        $form -> freeze();
    } else {
        $form -> addElement('submit', 'submit_course', _SUBMIT, 'class = "flatButton"');

        if ($form -> isSubmitted() && $form -> validate()) {
            if (isset($_GET['edit_course'])) {

                $GLOBALS['configuration']['onelanguage'] == true ? $languages_NAME = $GLOBALS['configuration']['default_language'] : $languages_NAME = $form -> exportValue('languages_NAME');
                $fields_update = array('name' => $form -> exportValue('name'),
                                       'languages_NAME' => $languages_NAME,
                                       'active' => $form -> exportValue('active'),
            'show_catalog' => $form -> exportValue('show_catalog'),
                                       'duration' => $form -> exportValue('duration'),
                                       'max_users' => $form -> exportValue('max_users') ? $form -> exportValue('max_users') : null,
                        'directions_ID' => $form -> exportValue('directions_ID'),
                                       'price' => $form -> exportValue('price'));
                try {
                    $editCourse -> course = array_merge($editCourse -> course, $fields_update);
                    if ($form -> exportValue('price') && $form -> exportValue('recurring') && in_array($form -> exportValue('recurring'), array_keys($recurringOptions))) {
                        $editCourse -> options['recurring'] = $form -> exportValue('recurring');
                        if ($editCourse -> options['recurring']) {
                            $editCourse -> options['recurring_duration'] = $form -> exportValue($editCourse -> options['recurring'].'_duration');
                        }
                    } else {
                        unset($editCourse -> options['recurring']);
                    }
                    $editCourse -> persist();

                    if ($courseSk = $editCourse -> getCourseSkill()) {
                        eF_updateTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFCOURSE . " " .$form -> exportValue('name')), "skill_ID = " .$courseSk['skill_ID']) ;
                    }
                    eF_redirect("".basename($_SERVER['PHP_SELF']).'?ctg=courses&message='.urlencode(_COURSEUPDATED).'&message_type=success');
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }

            } elseif (isset($_GET['add_course'])) {
                $GLOBALS['configuration']['onelanguage'] == true ? $languages_NAME = $GLOBALS['configuration']['default_language'] : $languages_NAME = $form -> exportValue('languages_NAME');
                $fields_insert = array('name' => $form -> exportValue('name'),
                                       'languages_NAME' => $languages_NAME,
                        'show_catalog' => $form -> exportValue('show_catalog'),
                                       'duration' => $form -> exportValue('duration') ? $form -> exportValue('duration') : 0,
                                       'max_users' => $form -> exportValue('max_users') ? $form -> exportValue('max_users') : null,
                                       'active' => $form -> exportValue('active'),
                                       'directions_ID' => $form -> exportValue('directions_ID'),
                                       'price' => $form -> exportValue('price'));

                try {
                    $newCourse = EfrontCourse :: createCourse($fields_insert);
                    if ($form -> exportValue('price') && $form -> exportValue('recurring') && in_array($form -> exportValue('recurring'), array_keys($recurringOptions))) {
                        $newCourse -> options['recurring'] = $form -> exportValue('recurring');
                        if ($newCourse -> options['recurring']) {
                            $newCourse -> options['recurring_duration'] = $form -> exportValue($newCourse -> options['recurring'].'_duration');
                        }
                        $newCourse -> persist();
                    }
                    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=courses&edit_course=".$newCourse -> course['id']."&tab=lessons&message=".urlencode(_SUCCESFULLYCREATEDCOURSE)."&message_type=success");
                } catch (Excpetion $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }
        }
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_COURSE_FORM', $renderer -> toArray());


    if (isset($_GET['edit_course'])) {
        $loadScripts[] = 'scriptaculous/scriptaculous';
        $loadScripts[] = 'scriptaculous/effects';
        $lessons = EfrontLesson :: getLessons();
        $courseLessons = $editCourse -> getLessons();
        $directionsPaths = $directionsTree -> toPathString();
        $languages = EfrontSystem :: getLanguages(true);
        foreach ($lessons as $key => $lesson) {
            $lessons[$key]['directionsPath'] = $directionsPaths[$lesson['directions_ID']];
            if (in_array($lesson['id'], array_keys($courseLessons))) {
                $lessons[$key]['course_assigned'] = true;
            } else {
                $lessons[$key]['course_assigned'] = false;
                if ($lesson['active'] == 0 || !$lesson['course_only']) {
                    unset($lessons[$key]);
                }
            }
        }
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
                $lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
            }
            $smarty -> assign("T_LESSONS_DATA", $lessons);
            $smarty -> display('administrator.tpl');
            exit;
        }
        if (isset($_GET['postAjaxRequest']) && $_GET['postAjaxRequest'] == 'lessons') {
            try {
                if (isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id')) {
                    !in_array($_GET['id'], array_keys($courseLessons)) ? $editCourse -> addLessons($_GET['id']) : $editCourse -> removeLessons($_GET['id']) ;
                } else if (isset($_GET['addAll'])) {
                    isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;
                    $editCourse -> addLessons(array_diff(array_keys($lessons), array_keys($courseLessons)));
                } else if (isset($_GET['removeAll'])) {
                    isset($_GET['filter']) ? $courseLessons = eF_filterData($courseLessons, $_GET['filter']) : null;
                    $editCourse -> removeLessons(array_keys($courseLessons));
                }
                exit;
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
        }
        try {
            $courseUsers = $editCourse -> getUsers(); //Get all users that have this course
            foreach ($courseUsers as $key => $value) {
                $courseUsers[$key]['in_course'] = 1;
            }
            $nonCourseUsers = $editCourse -> getNonUsers(); //Get all the users that can, but don't, have this course
            $users = $courseUsers + $nonCourseUsers; //Merge users to a single array, which will be useful for displaying them (+ is used instead of array_merge, for the case that a user has numerical login)
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
                $smarty -> assign("T_COURSE_USERS", array_keys($courseUsers)); //We assign separately the course's users, to know when to display the checkboxes as "checked"
                $smarty -> display('administrator.tpl');
                exit;
            }
        } catch (Exception $e) {
            header("HTTP/1.0 500");
            echo $e -> getMessage().' ('.$e -> getCode().')';
            exit;
        }
        if (isset($_GET['postAjaxRequest']) && $_GET['postAjaxRequest'] == 'users') {
            try {
                if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
                    isset($_GET['user_type']) && in_array($_GET['user_type'], array_keys($roles)) ? $userType = $_GET['user_type'] : $userType = 'student';
                    if (in_array($_GET['login'], array_keys($nonCourseUsers))) {
                        $editCourse -> addUsers($_GET['login'], $userType);
                    }
                    if (in_array($_GET['login'], array_keys($courseUsers))) {
                        $userType != $courseUsers[$_GET['login']]['role'] ? $editCourse -> setRoles($_GET['login'], $userType) : $editCourse -> removeUsers($_GET['login']);
                    }
                } else if (isset($_GET['addAll'])) {
                    $userTypes = array();
                    isset($_GET['filter']) ? $nonCourseUsers = eF_filterData($nonCourseUsers, $_GET['filter']) : null;
                    foreach ($nonCourseUsers as $user) {
                        $user['user_types_ID'] ? $userTypes[] = $user['user_types_ID'] : $userTypes[] = $user['basic_user_type'];
                    }
                    $editCourse -> addUsers(array_keys($nonCourseUsers), $userTypes);
                } else if (isset($_GET['removeAll'])) {
                    isset($_GET['filter']) ? $courseUsers = eF_filterData($courseUsers, $_GET['filter']) : null;
                    foreach ($courseUsers as $user) {
                        $userRoles[] = $user['basic_user_type'];
                    }
                    $editCourse -> removeUsers(array_keys($courseUsers), $userRoles);
                }
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    }
} elseif (isset($_GET['course']) && in_array($_GET['course'], $systemCourses)) {
    $currentCourse = new EfrontCourse($_GET['course']);
    $smarty -> assign("T_CURRENT_COURSE", $currentCourse);
    $baseUrl = 'ctg=courses&course='.$currentCourse -> course['id'];
    $smarty -> assign("T_BASE_URL", $baseUrl);
    require_once 'course_settings.php';
} else {
    $form = new HTML_QuickForm("import_course_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=courses", "", null, true); //Build the form
    $form -> addElement('file', 'import_content', _UPLOADLESSONFILE, 'class = "inputText"');
    $form -> addElement('submit', 'submit_course', _SUBMIT, 'class = "flatButton"');
    try {
        if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
            $directionsTree = new EfrontDirectionsTree();
            if (sizeof($directionsTree -> tree) == 0) {
                eF_redirect(basename($_SERVER['PHP_SELF']).'?ctg=directions&add_direction=1&message='.urlencode(_TOCREATECOURSEYOUMUSTFIRSTCREATECATEGORY).'&message_type=failure');
                exit;
            }
            $userTempDir = $GLOBALS['currentUser'] -> user['directory'].'/temp';
            if (!is_dir($userTempDir)) { //If the user's temp directory does not exist, create it
                $userTempDir = EfrontDirectory :: createDirectory($userTempDir, false);
            } else {
                $userTempDir = new EfrontDirectory($userTempDir);
            }
            $newCourse = EfrontCourse :: createCourse();
            $filesystem = new FileSystemTree($userTempDir, true);
            $file = $filesystem -> uploadFile('import_content', $userTempDir);
            $exportedFile = $file;
            $newCourse -> import($exportedFile, false, true);
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
    $smarty -> assign('T_IMPORT_COURSE_FORM', $renderer -> toArray()); //Assign the form to the template
    $allCourses = EFrontCourse :: getCourses(true);
    $directionsTree = new EfrontDirectionsTree();
    $directions = $directionsTree -> toPathString(true, true);
    $languages = EfrontSystem :: getLanguages(true);
    $result = eF_getTableData("lessons_to_courses", "*");
    foreach ($result as $value) {
        $courseLessons[$value['courses_ID']][] = $value['lessons_ID'];
    }
    foreach ($allCourses as $key => $course) {
        //$obj = new EfrontCourse($course['id']);
        $course -> course['directions_ID'] ? $course -> course['directionsPath'] = $directions[$course -> course['directions_ID']] : $course -> course['directionsPath'] = '';
        $course -> course['languages_NAME'] = $languages[$course -> course['languages_NAME']];
        $course -> course['lessons_num'] = isset($courseLessons[$course -> course['id']]) ? sizeof($courseLessons[$course -> course['id']]) : 0;
        $course -> course['link'] = $course -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=courses&edit_course='.$course -> course['id']);
        $course -> course['students'] = sizeof($course -> getUsers('student'));
  //$courses[$key]['price_string']   = $obj -> course['price_string'];
        $courses[$course -> course['id']] = $course -> course;
    }
    if (G_VERSIONTYPE == 'enterprise') {
        $result = eF_getTableDataFlat("courses LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_course_offers_skill.courses_ID = courses.id","courses.id, count(skill_ID) as skills_offered, courses.archive","courses.archive=0","","id");
        foreach ($result['id'] as $key => $courses_id) {
            $courses[$courses_id]['skills_offered'] = $result['skills_offered'][$key];
        }
    }
    $smarty -> assign("T_COURSES_DATA", $courses);
}
?>
