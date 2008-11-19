<?php
/**
* Module for personal information
*
* This file is used for user management - either personal or by an administrator
*
* Roadmap: (search for the following titles)
* - Check the user type and define the currentUser instance
* - [HCD] Access Control
* - [HCD] Evaluation Management -> exit
* - Add User or Edit User
* --- Create $editedUser, [HCD] $editedEmployee in case of submit
*
* --- Submit posted forms: lessons, courses, groups, avatar, [HCD] job descriptions, [HCD] skills
* --- Create the add/edit user form
* --- Submit posted form: personal information
*
* --- [HCD] Retrieve all Employee information to appear on the form: job descriptions, skills, evaluations
* --- [HCD] Include file manager
* --- Retrieve all User information to appear on the form: personal information, lessons, courses, groups
* -
*
* @package eFront
* @version 1.0
*/

//error_reporting(E_ALL);
//echo "<pre>";print_r($_POST);print_r($_GET);
//print_r($_FILES);


if (MODULE_HCD_INTERFACE) {
    require_once "../libraries/module_hcd_tools.php";
}

/***************************************************************/
/*** Check the user type and define the currentUser instance ***/
/***************************************************************/
if (isset($currentUser -> login) && $_SESSION['s_password']) {
    try {
        // The factory takes care for the definition of the HCD user type in $currentUser -> aspects['hcd']
        if (!($currentUser instanceOf EfrontUser)) {
            $currentUser = EfrontUserFactory :: factory($currentUser -> login);
        }
        $currentEmployee = $currentUser -> aspects['hcd'];

    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        header("location:index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    header("location:index.php?message=".urlencode(_YOUCANNOTACCESSTHISPAGE)."&message_type=failure");
    exit;
}

/*****************************************************************************************************************/
/************************************** [HCD] Access Control *****************************************************/
/*****************************************************************************************************************/
if (MODULE_HCD_INTERFACE) {
    // Check if you are changing your own data - every HCD type is allowed to do that
    if ($_GET['ctg'] != 'personal') {
        if ($currentUser -> login == $_GET['edit_user']) {
            $ctg = 'personal';
            $smarty -> assign('T_CTG',$ctg);
            $_GET['ctg'] = 'personal';

        } else if ($currentUser -> getType() != "administrator") {      // Administrators are allowed to do anything - no need to check further
            // If you are a Supervisor...
            if ($currentEmployee -> isSupervisor() ) {

                // Check if you can manage/see this employee`s data - if not, prevent access
                if (isset($_GET['edit_user']) && !$currentEmployee -> supervisesEmployee($_GET['edit_user']) ) {
                    $message = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
                    $message_type = "failure";
                    header("location:".$_SERVER['HTTP_REFERER']."&message=".$message."&message_type=".$message_type);
                    exit;
                }

            } else {

                // Only Employees with no supervisor rights reach this point
                // Simple employees who are professors are allowed to manage evaluations - if this is not the case, then prevent access
                if ( !($currentUser -> getType() == "professor" && (isset($_GET['add_evaluation']) || isset($_GET['edit_evaluation']) || isset($_GET['delete_evaluation'])))) {
                    $message      = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
                    $message_type = "failure";
                    header("location:".$_SERVER['HTTP_REFERER']."&message=".$message."&message_type=".$message_type);
                    exit;
                }

            }
        }
    }
}

/*******************************************************************************************************************/
/************************************** [HCD] Evaluation Management -> exit ****************************************/
/*******************************************************************************************************************/
if (MODULE_HCD_INTERFACE && isset($_GET['delete_evaluation']) ) {

    $flag = 0;
    /*** Evaluations are deleted either by administrators or by the users who wrote them ***/
    if($currentUser -> getType() != 'administrator') {
        $evaluations = eF_getTableData("module_hcd_events","*","event_ID = '".$_GET['delete_evaluation']."'");  // query (1)

        if ($evaluations[0]['author'] != $currentUser -> login && $evaluations[0]['event_code'] >= 10) {
            $message      = _YOUCANNOTDELETESOMEELSESEVALUATION;
            $message_type = 'failure';
            $flag = 1;
        }
    }

    // The flag is used to avoid the query above (1), in case the user has administrator rights
    if ($flag == 0) {
        if (eF_deleteTableData("module_hcd_events", "event_ID = '".$_GET['delete_evaluation']."'")) {
            if ($evaluations[0]['event_code'] >= 10) {
                $message      = _EVALUATIONDELETED;
            } else {
                $message      = _HISTORYRECORDDELETED;
            }
            $message_type = 'success';
        } else {
            $message      = _THEEVALUATIONCOULDNOTBEDELETED;
            $message_type = 'failure';
        }
    }

    $previous_url = getenv('HTTP_REFERER');
    if ($position = strpos($previous_url, "&message")) {
        $previous_url = substr($previous_url, 0, $position);
    }
    header("location:".$previous_url."&tab=evaluations&message=". $message . "&message_type=" . $message_type);
}


if (MODULE_HCD_INTERFACE && (isset($_GET['add_evaluation']) || isset($_GET['edit_evaluation']))) {

    if (isset($_GET['add_evaluation'])) {
        $form = new HTML_QuickForm("evaluations_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=users&edit_user=".$_GET['edit_user']."&add_evaluation=1", "", "target='_parent'", true);
    } else {
        $form = new HTML_QuickForm("evaluations_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=users&edit_user=".$_GET['edit_user']."&edit_evaluation=".$_GET['edit_evaluation'], "", "target='_parent'", true);
    }

    // Hidden for maintaining the previous_url value
    $form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');
    $previous_url = getenv('HTTP_REFERER');
    if ($position = strpos($previous_url, "&message")) {
        $previous_url = substr($previous_url, 0, $position);
    }
    $form -> setDefaults(array( 'previous_url'     =>  $previous_url));

    $form -> addElement('text', 'specification', _EVALUATIONCOMMENT, 'class = "inputText"');
    if(isset($_GET['edit_evaluation'])) {
        $evaluations = eF_getTableData("module_hcd_events","*","event_ID = '".$_GET['edit_evaluation']."'");
        if ($currentUser -> getType() != 'administrator' && ($evaluations[0]['author'] != $currentUser -> login)) {
            $message      = _YOUCANNOTEDITSOMEELSESEVALUATION;
            $message_type = 'failure';
            header("location:".basename($form->exportValue('previous_url'))."&message=". $message . "&message_type=" . $message_type . "&tab=evaluations");
            //header("location:".$_SERVER['HTTP_REFERER']."&tab=evaluations&message=". $message . "&message_type=" . $message_type);
            exit;
        }
        $form -> setDefaults( array('specification'  =>  $evaluations[0]['specification']));
    }
    $form -> addRule('specification', _THEFIELD.' '._EVALUATIONCOMMENT .' '._ISMANDATORY, 'required', null, 'client');
    $form -> addElement('submit', 'submit_evaluation_details', _SUBMIT, 'class = "flatButton" tabindex="2"');

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $renderer -> setRequiredTemplate(
        '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}');

    /*****************************************************
     EVALUATION DATA SUBMISSION
     **************************************************** */
    if ($form -> isSubmitted()) {
        if ($form -> validate()) {
            $evaluation_content = array('specification'  => $form->exportValue('specification'),
                                        'event_code'     => 10,
                                        'users_login'    => $_GET['edit_user'],
                                        'author'         => $currentUser -> login,
                                        'timestamp'      => time());

            if (isset($_GET['add_evaluation'])) {
                if ($ok = eF_insertTableData("module_hcd_events", $evaluation_content)) {
                    $message      = _SUCCESSFULLYCREATEDEVALUATION;
                    $message_type = 'success';
                }
                else {
                    $message      = _EVALUATIONCOULDNOTBECREATED.": ".$ok;
                    $message_type = 'failure';
                }

            } elseif (isset($_GET['edit_evaluation'])) {
                eF_updateTableData("module_hcd_events", $evaluation_content, "event_ID = '" . $_GET['edit_evaluation']. "'");
                $message      = _EVALUATIONDATAUPDATED;
                $message_type = 'success';
            }

            // A little risky, but i think that all urls have sth like ?ctg= , so np
            header("location:".basename($form->exportValue('previous_url'))."&message=". $message . "&message_type=" . $message_type . "&tab=evaluations");
            exit;
        }
    }

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);
    $smarty -> assign('T_EVALUATIONS_FORM', $renderer -> toArray());

} else {
/****************************************************************************************************************************************************/
/************************************************* ADD USER OR EDIT USER ****************************************************************************/
/****************************************************************************************************************************************************/

    /************************************************* Create $editedUser, [HCD] $editedEmployee in case of submit *************************************************/
    //If the user is not specified through the get parameter, it means that a user with no priviledges is changing his own personal settings.
    if (!isset($_GET['edit_user']) && !isset($_GET['add_user'])) {
        $_GET['edit_user'] = $currentUser -> login;
        $editedUser        = $currentUser;
        $editedEmployee    = $currentUser -> aspects['hcd'];
    } else if (isset($_GET['edit_user'])) {
        // The $editedUser object will be set here if a user is changing his own data. Otherwise, it will be created here for the user under edition
        if (!isset($editedUser)) {
            try {
                $editedUser     = EfrontUserFactory :: factory($_GET['edit_user']); //new EfrontUser();
                $editedEmployee = $editedUser -> aspects['hcd'];
            } catch (Exception $e) {
                $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().'('.$e -> getCode().')';
                $message_type = 'failure';
            }
        }
    }

    $smarty -> assign("T_LOGIN", $_GET['edit_user']);

//Set the avatar
    try {
        $avatarsFileSystemTree = new FileSystemTree(G_AVATARSPATH.'system_avatars');
        foreach (new EfrontFileTypeFilterIterator(new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($avatarsFileSystemTree -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('png')) as $key => $value) {
            $systemAvatars[basename($key)] = basename($key);
        }
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    }


    if ($editedUser -> user['login'] == $currentUser -> user['login'] && $currentUser -> getType() != 'administrator') {    //The user is editing himself
        $form = new HTML_QuickForm("set_avatar_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=personal", "", null, true);
    } else {                                                                                                            //The user is being edited by the admin
        $form = new HTML_QuickForm("set_avatar_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=users&edit_user=".$editedUser -> user['login'], "", null, true);
    }
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
    $form -> addElement('file', 'file_upload', _IMAGEFILE, 'class = "inputText"');
    $form -> addElement('advcheckbox', 'delete_avatar', _DELETECURRENTAVATAR, null, 'class = "inputCheckbox"', array(0, 1));
    $form -> addElement('select', 'system_avatar' , _ORSELECTONEFROMLIST, $systemAvatars, "id = 'select_avatar'");
    $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);            //getUploadMaxSize returns size in KB

    if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
        $form -> freeze();
    } else {
        $form -> addElement('submit', 'submit_upload_file', _APPLYAVATARCHANGES, 'class = "flatButton"');

        if ($form -> isSubmitted() && $form -> validate()) {
            $avatarDirectory = G_UPLOADPATH.$editedUser -> login.'/avatars';
            if (!is_dir($avatarDirectory)) {
                mkdir($avatarDirectory);
            }
            try {
                if ($_FILES['file_upload']['size'] > 0) {
                    $filesystem   = new FileSystemTree($avatarDirectory);
                    $uploadedFile = $filesystem -> uploadFile('file_upload', $avatarDirectory);

                    // Normalize avatar picture to 150xDimY or DimX x 100
                    eF_normalizeImage($avatarDirectory . "/" . $uploadedFile['name'], $uploadedFile['extension'], 150, 100);

                    $editedUser -> user['avatar'] = $uploadedFile['id'];
                    $editedUser -> persist();
                    $message      = _SUCCESFULLYSETAVATAR;
                    $message_type = 'success';
                } else {
                    if ($form -> exportValue('delete_avatar')) {
                        $selectedAvatar = 'unknown_small.png';
                    } else {
                        $selectedAvatar = $form -> exportValue('system_avatar');
                    }

                    $selectedAvatar = $avatarsFileSystemTree -> seekNode(G_AVATARSPATH.'system_avatars/'.$selectedAvatar);
                    $newList        = FileSystemTree :: importFiles($selectedAvatar['path']);                                //Import the file to the database, so we can access it with view_file
                    $editedUser -> user['avatar'] = key($newList);
                    $editedUser -> persist();

                    $message      = _SUCCESFULLYSETAVATAR;
                    $message_type = 'success';
                }

                if ($editedUser -> login == $currentUser -> login) {
                    $smarty -> assign("T_REFRESH_SIDE", 1);
                    $smarty -> assign("T_PERSONAL_CTG", 1);
                }
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form -> accept($renderer);
    $smarty -> assign('T_AVATAR_FORM', $renderer -> toArray());
//End of set the avatar





    /** MODULE_HCD: Submission **/
    /** Post job descriptions **/
    if (isset($_POST['employee_to_job'])) {

        // The assignment here is made on job descriptions and not job_description Ids. This means that a new job record could be inserted.
        $new_job_description_ID = eF_getJobDescriptionId($_POST['job_descriptions'], $_POST['branches']);

        // Check whether the user has been assigned to a branch with a job description
        try {
            $editedEmployee = $editedEmployee -> addJob($editedUser, $new_job_description_ID , $_POST['branches'], $_POST['branch_position']);
            $message      = _OPERATIONCOMPLETEDSUCCESFULLY;
            $message_type = 'success';
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().'('.$e -> getCode().')';
            $message_type = 'failure';
        }

    }

    /* Delete job assignment from employee */
    if (isset($_GET['edit_user']) && isset($_GET['delete_job']) ) {
        try {
            $editedEmployee = $editedEmployee -> removeJob($_GET['delete_job']);
            $message      = _OPERATIONCOMPLETEDSUCCESFULLY;
            $message_type = 'success';
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().'('.$e -> getCode().')';
            $message_type = 'failure';
        }
    }

    /* Remove skill from employee */
    if (isset($_GET['edit_user']) && isset($_GET['delete_skill']) ) {
        try {
            $editedEmployee -> removeSkills($_GET['delete_skill']);
            $message      = _OPERATIONCOMPLETEDSUCCESFULLY;
            $message_type = 'success';
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().'('.$e -> getCode().')';
            $message_type = 'failure';
        }
    }

    /*** Ajax Methods - Add/remove skills/jobs***/
    if (isset($_GET['postAjaxRequest'])) {
        try {
            echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
            /** Post skill - Ajax skill **/
            if (isset($_GET['add_skill'])) {
                if ($_GET['insert'] == "true") {
                    $editedEmployee -> addSkills($_GET['add_skill'], $_GET['specification']);
                } else if ($_GET['insert'] == "false") {
                    $editedEmployee -> removeSkills($_GET['add_skill']);
                } else if (isset($_GET['addAll'])) {

                    $skills = $editedEmployee -> getSkills();
                    $skills = array_keys($skills);
                    $allSkills = EfrontSkill::getAllSkills();

                    foreach ($allSkills as $skill) {
                        if (!in_array($skill['skill_ID'], $skills)) {
                            $editedEmployee -> addSkills($skill['skill_ID'], "");
                        }
                    }
                } else if (isset($_GET['removeAll'])) {
                    $skills = $editedEmployee -> getSkills();
                    $skills = array_keys($skills);
                    $allSkills = EfrontSkill::getAllSkills();
                    foreach ($allSkills as $skill) {
                        if (in_array($skill['skill_ID'], $skills)) {
                            $editedEmployee -> removeSkills($skill['skill_ID']);
                        }
                    }
                }
            } else if (isset($_GET['add_job'])) {
            //pr($_GET);
                $_GET['add_job'] = urldecode($_GET['add_job']);
                // Find all employees having this skill
                if ($_GET['insert'] == "1") {
                    $new_job_description_ID = eF_getJobDescriptionId($_GET['add_job'], $_GET['add_branch']);
                    if ($_GET['default_job'] != '') {
                        $old_job_description_ID = eF_getJobDescriptionId($_GET['default_job'], $_GET['default_branch']);
                        if ($_GET['add_branch'] != $_GET['default_branch'] || $_GET['default_position'] != $_GET['add_position'] || $new_job_description_ID != $old_job_description_ID) {
                            $old_job_description_ID = eF_getJobDescriptionId($_GET['default_job'], $_GET['default_branch']);
                            $editedEmployee = $editedEmployee -> removeJob ($old_job_description_ID);
                        }

                    }
                    //echo $new_job_description_ID . "**<BR>";

                    $editedEmployee = $editedEmployee -> addJob ($editedUser, $new_job_description_ID, $_GET['add_branch'], $_GET['add_position'], $_GET['add_job']);

                } else if ($_GET['insert'] == "0") {
                    $old_job_description_ID = eF_getJobDescriptionId($_GET['add_job'], $_GET['add_branch']);
                    $editedEmployee = $editedEmployee -> removeJob ($old_job_description_ID);
                }
            } else if (isset($_GET['add_lesson'])) {
                if ($_GET['insert'] == "true") {
                    $editedUser -> addLessons($_GET['add_lesson'], $_GET['user_type'], 1);
                } else if ($_GET['insert'] == "false") {
                    $editedUser -> removeLessons($_GET['add_lesson']);
                } else if (isset($_GET['addAll'])) {
                    $userNonLessons = $editedUser -> getNonLessons(true);
                    $lessons = array();
                    foreach ($userNonLessons as $key => $lesson) {
                        if (!$lesson -> lesson['course_only']) {
                            $lessons[] = $lesson -> lesson['id'];
                        }
                    }
                    $editedUser -> addLessons($lessons, 0, 1);
                } else if (isset($_GET['removeAll'])) {
                    $userLessons = $editedUser -> getLessons(true);
                    $lessons     = array();
                    foreach ($userLessons as $key => $lesson) {
                        if (!$lesson -> lesson['course_only']) {
                            $lessons[] = $lesson -> lesson['id'];
                        }
                    }
                    $editedUser -> removeLessons($lessons);
                } else if (isset($_GET['addAllLessonsFromTest'])) {
                    // The missing and required skill set is sent over with the ajax request
                    $skills_missing = array();
                    $all_skills = "";
                    foreach ($_GET as $key => $value) {
                        // all skill-related posted values are just the skill_ID ~ a uint value
                        if (eF_checkParameter($key, 'unit')) {
                            if ($value == 1) {
                                $skills_missing[] = $key;
                            }
                        }
                    }

                    // We found all the skills missing
                    $skills_missing = implode("','",  $skills_missing);

                    // We have all the already attended courses
                    $alredy_attending = implode("','",  array_keys($editedUser -> getLessons()));

                    // Thus we can find the missing courses to fill the skill gap
                    $lessons_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID JOIN lessons ON lessons.id = module_hcd_lesson_offers_skill.lesson_ID","module_hcd_lesson_offers_skill.lesson_ID, lessons.*, count(module_hcd_lesson_offers_skill.skill_ID) as skills_offered", "module_hcd_lesson_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_lesson_offers_skill.lesson_ID NOT IN ('".$alredy_attending."')","","module_hcd_lesson_offers_skill.lesson_ID ORDER BY skills_offered DESC");

                    // And assign them
                    foreach ($lessons_proposed as $lesson) {
                        $editedUser -> addLessons($lesson['lesson_ID']);
                    }
                }
            } else if (isset($_GET['add_course'])) {
                if ($_GET['insert'] == "true") {
                    $editedUser -> addCourses($_GET['add_course'], $_GET['user_type'], 1);
                } else if ($_GET['insert'] == "false") {
                    $editedUser -> removeCourses($_GET['add_course']);
                } else if (isset($_GET['addAll'])) {
                    $courses = $editedUser -> getNonCourses();
                    $editedUser -> addCourses($courses, 0, 1);
                } else if (isset($_GET['removeAll'])) {
                    $courses = $editedUser -> getCourses();
                    $courses = array_keys($courses);
                    $editedUser -> removeCourses($courses);
                } else if (isset($_GET['addAllCoursesFromTest'])) {
                    // The missing and required skill set is sent over with the ajax request
                    $skills_missing = array();
                    $all_skills = "";
                    foreach ($_GET as $key => $value) {
                        // all skill-related posted values are just the skill_ID ~ a uint value
                        if (eF_checkParameter($key, 'unit')) {
                            if ($value == 1) {
                                $skills_missing[] = $key;
                            }
                        }
                    }

                    // We found all the skills missing
                    $skills_missing = implode("','",  $skills_missing);

                    // We have all the already attended courses
                    $alredy_attending = implode("','",  array_keys($editedUser -> getCourses()));

                    // Thus we can find the missing courses to fill the skill gap
                    $courses_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID JOIN courses ON courses.id = module_hcd_course_offers_skill.courses_ID","module_hcd_course_offers_skill.courses_ID, courses.*, count(module_hcd_course_offers_skill.skill_ID) as skills_offered", "module_hcd_course_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_course_offers_skill.courses_ID NOT IN ('".$alredy_attending."')","","module_hcd_course_offers_skill.courses_ID ORDER BY skills_offered DESC");

                    // And assign them
                    foreach ($courses_proposed as $course) {
                        $editedUser -> addCourses($course['courses_ID']);
                    }
                }
            } else if (isset($_GET['add_group'])) {

                if ($_GET['insert'] == "true") {
                    $editedUser -> addGroups($_GET['add_group']);
                } else if ($_GET['insert'] == "false") {
                    $editedUser -> removeGroups($_GET['add_group']);
                } else if (isset($_GET['addAll'])) {
                    $groups = eF_getTableDataFlat("groups","id","");
                    $editedUser -> addGroups($groups['id']);
                } else if (isset($_GET['removeAll'])) {
                    $groups = eF_getTableDataFlat("groups","id","");
                    $editedUser -> removeGroups($groups['id']);
                }
            }

            exit;
        } catch (Exception $e) {
            header("HTTP/1.0 500");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
    }

    /** Get the skill list by ajax **/
    $edit_user= $_GET['edit_user'];
    // Create ajax enabled table for employees
    $load_scripts = array_merge($load_scripts, array('scriptaculous/prototype'));
    if (isset($_GET['ajax']) && $_GET['ajax'] == 'skillsTable') {
        isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

        if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
            $sort = $_GET['sort'];
            isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
        } else {
            $sort = 'login';
        }

        // ** Get skills **
        // We do not use the getSkills() method, because it will only return the skills of the employee and we need to present them ALL
        $skill_categories = eF_getTableData("module_hcd_skill_categories", "*", "", "description","");
        $skills = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_employee_has_skill ON (module_hcd_employee_has_skill.skill_ID = module_hcd_skills.skill_ID AND module_hcd_employee_has_skill.users_login='$edit_user') LEFT JOIN users ON module_hcd_employee_has_skill.author_login = users.login", "users_login, description,specification, module_hcd_skills.skill_ID, categories_ID, users.surname, users.name","");

        $skills = eF_multiSort($skills, $sort, $order);
        $smarty -> assign("T_SKILLS_SIZE", sizeof($skills));
        if (isset($_GET['filter'])) {
            $skills = eF_filterData($skills, $_GET['filter']);
        }
        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
            $skills = array_slice($skills, $offset, $limit);
        }

        if (!empty($skills)) {
            $smarty -> assign("T_SKILLS", $skills);
        }
        $smarty -> display($_SESSION['s_type'].'.tpl');
        exit;
    }


   if (isset($_GET['ajax']) && $_GET['ajax'] == 'JobsFormTable') {
        isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

        if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
            $sort = $_GET['sort'];
            isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
        } else {
            $sort = 'name';
        }

        // ** Get skills **
        // We do not use the getSkills() method, because it will only return the skills of the employee and we need to present them ALL
        $employees_placements = $editedEmployee -> getJobs();

        if (!empty($employees_placements)) {
            $smarty -> assign("T_FORM_PLACEMENTS", $employees_placements);
        }
        $smarty -> display($_SESSION['s_type'].'.tpl');
        exit;
    }





    /** Get the employees history by ajax **/
    if (isset($_GET['ajax']) && $_GET['ajax'] == 'historyFormTable') {
        isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

        if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
            $sort = $_GET['sort'];
            isset($_GET['order']) && $_GET['order'] == 'asc' ? $order = 'asc' : $order = 'desc';
        } else {
            $sort = 'timestamp';
        }

        /** Get history **/
        $history = eF_getTableData("module_hcd_events", "*", "users_login = '".$_GET['edit_user']."' AND event_code <10"); //"
        $history = eF_multiSort($history, $sort, $order);
        if (isset($_GET['filter'])) {
            $history = eF_filterData($history , $_GET['filter']);
        }

        $smarty -> assign('T_HISTORY_SIZE', sizeof($history));

        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
            $history = array_slice($history, $offset, $limit);
        }


        if(!empty($history)) {
            $smarty -> assign("T_HISTORY", $history);
        }

        $smarty -> display($_SESSION['s_type'].'.tpl');
        exit;
    }    
    if (isset($_GET['ajax'])  && $_GET['ajax'] == 'lessonsTable' && $editedUser -> user['user_type'] != "administrator") {
        $directionsTree = new EfrontDirectionsTree();
        $directionPaths = $directionsTree -> toPathString();
        $lessons        = EfrontLesson :: getLessons();
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
        $smarty -> assign("T_LESSONS_SIZE", sizeof($lessons));
        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
            $lessons = array_slice($lessons, $offset, $limit);
        }
        //foreach ($lessons as $key => $lesson) {
            //$lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
        //}
        $smarty -> assign("T_LESSONS_DATA", $lessons);
        $smarty -> assign("T_EDITED_USER", $editedUser);

        $smarty -> display($_SESSION['s_type'].'.tpl');
        exit;
    }
    
    if (isset($_GET['ajax'])  && $_GET['ajax'] == 'coursesTable' && $editedUser -> user['user_type'] != "administrator") {
        $directionsTree = new EfrontDirectionsTree();
        $directionPaths = $directionsTree -> toPathString();
        $courses        = EfrontCourse :: getCourses();
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
    if (isset($_GET['ajax']) && $_GET['ajax'] == 'confirm_user') {
        try {
            if ($_GET['type'] == 'course') {
                $course = new EfrontCourse($_GET['id']);
                $course -> confirm($editedUser);                
            } else {
                $lesson = new EfrontLesson($_GET['id']);
                $lesson -> confirm($editedUser);
                //eF_updateTableData("users_to_lessons", array("from_timestamp" => time()), "users_LOGIN='".$editedUser -> user['login']."' and lessons_ID=".$_GET['id']." and from_timestamp=0");                
            }
        } catch (Exception $e) {
            header("HTTP/1.0 500");
            echo $e -> getMessage().' ('.$e -> getCode().')';            
        }
        exit;
    }


    /****************************************************************************************************************************************************/
    /*********************************************************** Create the add/edit user form ******************************************************************/
    /****************************************************************************************************************************************************/
    $user_profile = eF_getTableData("user_profile", "*", "active=1");    //Get admin-defined form fields for user registration
    if (isset($_GET['add_user'])) {                 //We add a new user, so we need to display login field. Only an administrator has the ability to add a user.
        $form = new HTML_QuickForm("add_users_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=users&add_user=1", "", null, true);


        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter

        $form -> addElement('text', 'new_login', _LOGIN, 'class = "inputText"');
        $form -> addRule('new_login', _THEFIELD.' '._LOGIN.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('new_login', _INVALIDFIELDDATA, 'checkParameter', 'login');

        $form -> registerRule('checkNotExist', 'callback', 'eF_checkNotExist');
        $form -> registerRule('passwdCheck', 'callback', 'eF_passwdCheck');
        $form -> addRule('new_login',  _THELOGIN.' &quot;'.($form -> exportValue('new_login')).'&quot; '._ALREADYEXISTS, 'checkNotExist', 'login');

        $form -> addElement('password', 'password_', _PASSWORD, 'class = "inputText"');
        $form -> addRule('password_', _THEFIELD.' '._PASSWORD.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('password_', _PASSWORDMUSTBE6CHARACTERS, 'passwdCheck');

        $form -> addElement('password', 'passrepeat', _REPEATPASSWORD, 'class = "inputText "');
        $form -> addRule('passrepeat', _THEFIELD.' '._REPEATPASSWORD.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule(array('password_', 'passrepeat'), _PASSWORDSDONOTMATCH, 'compare', null, 'client');

    } elseif (isset($_GET['edit_user']) && eF_checkParameter($_GET['edit_user'], 'login')) {

        // In HCD module both administrator and supervisors may change other employees data
        if (MODULE_HCD_INTERFACE) {
            ($currentEmployee -> getType() != _EMPLOYEE)   ? $post_target = "?ctg=users&edit_user=".$_GET['edit_user'] : $post_target = "?ctg=personal";
        } else {
            // In classic eFront, only the administrator may change someone else's data
            ($currentUser -> getType() == "administrator") ? $post_target = "?ctg=users&edit_user=".$_GET['edit_user'] : $post_target = "?ctg=personal";
        }

        $form = new HTML_QuickForm("change_users_form", "post", basename($_SERVER['PHP_SELF']).$post_target, "", null, true);



        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter

        if (!$editedUser -> isLdapUser) {              //needs to check ldap
            $form -> addElement('password', 'password_', _PASSWORDLEAVEBLANK, 'class = "inputText"');
            $form -> addElement('password', 'passrepeat', _REPEATPASSWORD, 'class = "inputText "');
            $form -> addRule(array('password_', 'passrepeat'), _PASSWORDSDONOTMATCH, 'compare', null, 'client');
        } else {
            $smarty -> assign("T_LDAP_USER", true);
        }

        $smarty -> assign("T_USER_TYPE", $editedUser -> user['user_type']);
        $smarty -> assign("T_REGISTRATION_DATE", $editedUser -> user['timestamp']);
        try {
            $avatar = new EfrontFile($editedUser -> user['avatar']);
            $smarty -> assign ("T_AVATAR", $editedUser -> user['avatar']);
//echo $editedUser -> user['avatar']."<BR>";
//pr($avatar);
            // Get current dimensions
            list($width, $height) = getimagesize($avatar['path']);
            if ($width > 200 || $height > 100) {
                // Get normalized dimensions
                list($newwidth, $newheight) = eF_getNormalizedDims($avatar['path'], 200, 100);

                // The template will check if they are defined and normalize the picture only if needed
                $smarty -> assign("T_NEWWIDTH", $newwidth);
                $smarty -> assign("T_NEWHEIGHT", $newheight);
            }

        } catch (Exception $e) {
            $smarty -> assign ("T_AVATAR", G_AVATARSPATH."system_avatars/unknown_small.png");
        }

        //Student's Paypal Transactions
        $trans = eF_getTableData("paypal_data", "*", "user='".$editedUser -> user['login']."' and payment_status= 'Completed'");
        foreach ($trans as $key => $value) {
            $trans[$key]['items']       = explode(", ", $value['item_name']);
            $trans[$key]['items_ids']   = explode(",", $value['item_number']);
        }

        $smarty -> assign("T_USER_TRANSACTIONS", $trans);
        $smarty -> assign("T_USER_TRANSACTIONS_NUM", sizeof($trans));
    }

    $form -> addElement('text', 'name', _NAME, 'class = "inputText"');
    $form -> addRule('name', _THEFIELD.' '._NAME.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');

    $form -> addElement('text', 'surname', _SURNAME, 'class = "inputText"');
    $form -> addRule('surname', _THEFIELD.' '._SURNAME.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('surname', _INVALIDFIELDDATA, 'checkParameter', 'text');

    $form -> addElement('text', 'email', _EMAILADDRESS, 'class = "inputText"');

    // Find all groups available to create the select-group drop down
    $groups_table = eF_getTableData("groups", "id, name", "");
    if (!empty($groups_table)) {
        $groups = array ("" => "");
        foreach ($groups_table as $group) {
            $gID = $group['id'];
            $groups["$gID"] = $group['name'];
        }
        $form -> addElement('select', 'group' , _GROUP, $groups ,'class = "inputText" id="group" name="group"');
    } else {
        $form -> addElement('select', 'group' , _GROUP, array ("" => _NOGROUPSDEFINED) ,'class = "inputText" id="group" name="group" disabled="disabled"');
    }

    // Email address is not mandatory for HCD mode
    if (!MODULE_HCD_INTERFACE){
       $form -> addRule('email', _THEFIELD.' '._EMAILADDRESS.' '._ISMANDATORY, 'required', null, 'client');
       $form -> addRule('email', _INVALIDFIELDDATA, 'checkParameter', 'email');
    }

    /** MODULE_HCD: Extra information needed for hcd - following HRMASTER rules **/
    if (MODULE_HCD_INTERFACE) {

        // Several form fields are to be disabled if the user is changing his own data - the wage for example :)
        if ($ctg == 'personal' && $editedUser -> getType() != 'administrator') {
            $disabled_tag = "disabled = \"disabled\"";
        }

        // Permanent data of personal records of employees
        $form -> addElement('text', 'father', _FATHERNAME, 'class = "inputText"');
        //$form -> addRule('father', _THEFIELD.' '._SURNAME.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('father', _INVALIDFIELDDATA, 'checkParameter', 'text');
        $form -> addElement('select', 'sex' , _GENDER, array("0" => _MALE, "1" => _FEMALE), 'class = "inputText" ');
        $form -> addElement('text', 'birthday', _BIRTHDAY, 'class = "inputText" ');
        $form -> addElement('text', 'birthplace',_BIRTHPLACE , 'class = "inputText" ');
        $form -> addElement('text', 'birthcountry', _BIRTHCOUNTRY, 'class = "inputText" ');
        $form -> addElement('text', 'mother_tongue', _MOTHERTONGUE, 'class = "inputText" ');
        $form -> addElement('text', 'nationality', _NATIONALITY, 'class = "inputText" ');
        $form -> addElement('text', 'address', _ADDRESS, 'class = "inputText" ');
        $form -> addElement('text', 'city', _CITY, 'class = "inputText" ');
        $form -> addElement('text', 'country', _COUNTRY, 'class = "inputText" ');
        $form -> addElement('text', 'homephone', _HOMEPHONE, 'class = "inputText" ');
        $form -> addElement('text', 'mobilephone', _MOBILEPHONE, 'class = "inputText" ');
        $form -> addElement('text', 'office', _OFFICE, 'class = "inputText" ');
        $form -> addElement('text', 'company_internal_phone', _COMPANYINTERNALPHONE, 'class = "inputText" ');
        $form -> addElement('text', 'afm', _VATREGNUMBER, 'class = "inputText" ');
        $form -> addElement('text', 'doy', _TAXOFFICE, 'class = "inputText" ');
        $form -> addElement('text', 'police_id_number', _POLICEIDNUMBER, 'class = "inputText" ');
        $form -> addElement('advcheckbox', 'driving_licence', _DRIVINGLICENSE, null, 'class = "inputCheckbox"');
        $form -> addElement('text', 'work_permission_data', _WORKPERMISSIONDATA, 'class = "inputText" ' . $disabled_tag);
        $form -> addElement('advcheckbox', 'national_service_completed', _NATIONALSERVICECOMPLETED, null, 'class = "inputCheckbox" ' . $disabled_tag);

        // Non permanent data
        $form -> addElement('text', 'employement_type', _EMPLOYMENTTYPE, 'class = "inputText" '. $disabled_tag);
        $form -> addElement('text', 'hired_on', _HIREDON, 'class = "inputText" '. $disabled_tag);
        $form -> addElement('text', 'left_on', _LEFTON, 'class = "inputText"' . $disabled_tag);
        $form -> addElement('text', 'wage', _WAGE, 'class = "inputText" '. $disabled_tag);
        $form -> addElement('select', 'marital_status', _MARITALSTATUS, array("0" => _SINGLE, "1" => _MARRIED),'class = "inputText" ');
        $form -> addElement('text', 'bank', _BANK, 'class = "inputText" ');
        $form -> addElement('text', 'bank_account', _BANKACCOUNT, 'class = "inputText" ');
        $form -> addElement('select', 'way_of_working', _WAYOFWORKING,  array("" => "", "0" => _FULLTIME, "1" => _PARTTIME),'class = "inputText" id="way_of_working" ' . $disabled_tag);
        $form -> addElement('advcheckbox', 'transport', _TRANSPORTMEANS, null, 'class = "inputCheckbox"');

        /** Create select list from all jobs that can be assigned from this Supervisor or the Administrator **/
        if ($currentUser -> getType() == 'administrator') {
           $branches1 = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");
           $all_branches = eF_createBranchesTreeSelect($branches1, 0);
        } else if ($currentEmployee -> getType() == _SUPERVISOR) {
           $branches1 = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","branch_ID IN (" . implode(",", $currentEmployee -> supervisesBranches) . ")");

           // A supervisor may only assign to the branches he supervises hence the second parameter equals to 1. The admin may assign an employee to no branch as well
           $all_branches = eF_createBranchesTreeSelect($branches1, 0);
        }


        /** Get employee's data will be needed later **/
        if (isset($_GET['edit_user'])) {
            // Get employee content - todo change to *oo
            $employees_content = eF_getTableData("module_hcd_employees LEFT OUTER JOIN module_hcd_employee_has_job_description ON module_hcd_employees.users_login = module_hcd_employee_has_job_description.users_login LEFT OUTER JOIN module_hcd_job_description ON module_hcd_job_description.job_description_ID = module_hcd_employee_has_job_description.job_description_ID LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.users_login = module_hcd_employees.users_login AND module_hcd_employee_works_at_branch.branch_ID = module_hcd_job_description.branch_ID", "*", "module_hcd_employees.users_login='".$_GET['edit_user']."'");

            // Define the employee's jobs array, if it hasn't already been created
            $editedEmployee -> getJobs();

            // This is HARD-CODED: the last job description will always be selected : if you change this - change also the code in the tpl which updates this field through javascript
            $init_job = end($editedEmployee->jobs); // for the time being we return to the main form, the last job returned from the query to the database; more advanced selection criteria could be used

        }

        /** Create the branches selects according to this employee's data **/
        if (!empty($branches1)) {
            $form -> addElement('select', 'branches_main' , _BRANCH, $all_branches ,'class = "inputText" id="branches_main" name="branches_main"  onchange="javascript:change_branch(\'branches_main\',\'details_link\',\'jobs_main\')"');
            $form -> addElement('select', 'placement', _PLACEMENT,  array("0" => _EMPLOYEE, "1" => _SUPERVISOR),'class = "inputText" id="placement"');

            $branch_jobs_disabled = '';


            if ($init_job['branch_ID']) {
                $my_branch_id = $init_job['branch_ID'];

                // If the employee already works at a branch create the appropriate job list for this branch
                $workingBranch = new EfrontBranch($init_job['branch_ID']);
                $all_jobs = $workingBranch -> createJobDescriptionsSelect();

            } else {

                // Create the generic job description list
                $all_job_descriptions = eF_getTableData("module_hcd_job_description","distinct description","");
                // Will only be enabled on branch selection
               $branch_jobs_disabled = 'disabled ="disabled"';

                if (!empty($all_job_descriptions)) {
                    $all_jobs = array(_NOSPECIFICJOB => _NOSPECIFICJOB);
                    foreach ($all_job_descriptions as $all_job) {
                        $jID = $all_job['description'];
                        if ($all_job['description'] != _NOSPECIFICJOB) {
                            $all_jobs["$jID"] = $all_job['description'];
                        }
                    }
                } else {
                    // No job descriptions registered
                    $all_jobs = array(_NOSPECIFICJOB => _NOSPECIFICJOB);
                }
            }

            //IE does not support options.disabled.... Special treatment needed
            if (MSIE_BROWSER) {
                $form -> addElement('select', 'all_jobs' , _JOBDESCRIPTION, $all_jobs, 'class="inputText" onFocus = "this.selIndex = this.selectedIndex;" onChange="restoreSelection(this);" id= "jobs_main"  ' . $branch_jobs_disabled);
            } else {
                $form -> addElement('select', 'all_jobs' , _JOBDESCRIPTION, $all_jobs, 'class="inputText" id= "jobs_main"  ' . $branch_jobs_disabled);
            }
        } else {
            $form -> addElement('select', 'branches_main' , _BRANCH, array("" => _NOBRANCHESHAVEBEENREGISTERED) ,'class = "inputText" id="branches_main" name="branches_main" disabled="disabled"');
            $form -> addElement('select', 'placement', _PLACEMENT,  array("0" => _EMPLOYEE, "1" => _SUPERVISOR),'class = "inputText" id="placement" disabled="disabled"');
        }


        // Set form defaults for edited employee
        if (isset($_GET['edit_user'])) {

            if (!empty($branches1)) {
                $form -> addElement('select', 'branches' , _BRANCHES, $all_branches ,'id="branches_row" onchange="javascript:change_branch(\\\'branches_row\\\',\\\'branches_details_link_row\\\',\\\'job_descriptions_row\\\', document.getElementById(\\\'job_descriptions_row\\\').value); ajaxPostJob(\\\'row\\\',this);"');
                //IE does not support options.disabled.... Special treatment needed
                if (MSIE_BROWSER) {
                    $form -> addElement('select', 'job_descriptions' , _JOBS, $all_jobs,'id="job_descriptions_row" onchange="javascript:if(restoreSelection(this))ajaxPostJob(\\\'row\\\',this);');
                } else {
                    $form -> addElement('select', 'job_descriptions' , _JOBS, $all_jobs,'id="job_descriptions_row" onchange="javascript:ajaxPostJob(\\\'row\\\',this);"');
                }
                $form -> addElement('select', 'branch_position' , null, array("0" => _EMPLOYEE, "1" => _SUPERVISOR),'id="branch_position_row" onchange="ajaxPostJob(\\\'row\\\',this);"');
            }

            $form -> setDefaults($editedEmployee -> employee);
            $form -> setDefaults(array('all_jobs'                   => $init_job['description'],
                                           'branches_main'              => $init_job['branch_ID'],
                                           'placement'                  => $init_job['supervisor']));


        }//if edit
    }

    if (isset($_GET['edit_user'])) {
        $editedUser -> getGroups();
        $init_group = end($editedUser -> groups);
        $form -> setDefaults(array('group'   => $init_group['groups_ID']));
    }


    /************************** END OF HCD MODULE ********************************/

    if (isset($_GET['edit_user'])) {
         $form -> setDefaults($editedUser -> user);
         //If the user's type is other than the basic types, set the corresponding select box to point to this one
         if ($editedUser -> user['user_types_ID']) {
             $form -> setDefaults(array('user_type' => $editedUser -> user['user_types_ID']));
         }
    }
	$resultRole = eF_getTableData("users", "user_types_ID", "login='".$currentUser -> login."'");
	$smarty -> assign("T_CURRENTUSERROLEID", $resultRole[0]['user_types_ID']);
    // In HCD mode supervisors - and not only administrators - may create employees
    if ($currentUser -> getType() == "administrator" || (MODULE_HCD_INTERFACE && $ctg != "personal")) {
		if ($resultRole[0]['user_types_ID'] == 0) {
			$roles = eF_getTableDataFlat("user_types", "*");

			$roles_array['student']       = _STUDENT;
			$roles_array['professor']     = _PROFESSOR;

			// Only the administrator may assign administrator rights
			if ($currentUser -> getType() == "administrator") {
				$roles_array['administrator'] = _ADMINISTRATOR;
			}

			for ($k = 0; $k < sizeof($roles['id']); $k++) {
				if ($roles['active'][$k] == 1 || (isset($editedUser) && $editedUser -> user['user_types_ID'] == $roles['id'][$k])) {    //Make sure that the user's current role will be listed, even if it's deactivated
					$roles_array[$roles['id'][$k]] = $roles['name'][$k];
				}
			}
			$form -> addElement('select', 'user_type', _USERTYPE, $roles_array);
        }
		
		$form -> addElement('advcheckbox', 'active', _ACTIVEUSER, null, 'class = "inputCheckbox"');
        // Set default values for new users
        if (isset($_GET['add_user'])) {
            $form -> setDefaults(array('active' => '1'));
        }

        if ($GLOBALS['configuration']['onelanguage']) {
            $form -> addElement('hidden', 'languages_NAME', $GLOBALS['configuration']['default_language']);
        } else {
            $form -> addElement('select', 'languages_NAME', _LANGUAGE, EfrontSystem :: getLanguages(true));

            // Set default values for new users
            if (isset($_GET['add_user'])) {
                $form -> setDefaults(array('languages_NAME' => $GLOBALS['configuration']['default_language']));
            }
        }

    }

    //Add custom fields, defined in user_profile database table
    foreach ($user_profile as $field) {
        $user_profile_fields[] = $field['name'];
        unset($options_assoc);
        if ($field['type'] == "select"){
            $options = unserialize($field['options']);
            foreach($options as $temp_value){
                $options_assoc[$temp_value] = $temp_value;
            }

            $form -> addElement($field['type'], $field['name'], $field['description'], $options_assoc, 'class = " input'.$field['type'].'"');
            if ($field['mandatory']) {

                $form -> addRule($field['name'], _THEFIELD.' '.$field['description'].' '._ISMANDATORY, 'required', null, 'client');
            }
        } else {
            $form -> addElement($field['type'], $field['name'], $field['description'], 'class = "input'.$field['type'].'"');
            if ($field['mandatory']) {
                $form -> addRule($field['name'], _THEFIELD.' '.$field['description'].' '._ISMANDATORY, 'required', null, 'client');
            }
        }

        $form -> addRule('firstName', _THEFIELD.' '.$field['description'].' '._HASINVALIDCHARACTERS.'. '._ONLYALLOWEDCHARACTERSTEXT, 'checkParameter', 'text');
    }



    foreach ($user_profile as $field) {
        if (isset($_GET['edit_user'])) {
            $form -> setDefaults(array($field['name'] => $editedUser -> user[$field['name']]));
        } else {
            $form -> setDefaults(array($field['name'] => $field['default_value']));
        }
    }

    if (isset($user_profile_fields)) {

        $smarty -> assign("T_USER_PROFILE_FIELDS", $user_profile_fields);
    }

    /****************************************************************************************************************************************************/
    /*********************************************************** Submit posted form: personal information ******************************************************************/
    /****************************************************************************************************************************************************/
//pr($form->exportValues());
//pr($form->exportValues);
//echo $form->exportValue('all_jobs');
    if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
        $form -> freeze();
    } else {
        $form -> addElement('submit', 'submit_personal_details', _SUBMIT, 'class = "flatButton"');

        if ($form -> isSubmitted() && $form -> validate()) {

            $values = $form -> exportValues();

            $user_profile = eF_getTableData("user_profile", "*", "active=1");    //Get admin-defined form fields for user registration

            /** MODULE_HCD: Create array from the form employee data **/
            if (MODULE_HCD_INTERFACE) {
                $new_employees_content = array();
                foreach($HCDEMPLOYEECATEGORIES as $key => $hcdField) {
                    if ($hcdField != "users_login" && $hcdField != "candidate") {
                        if ($form -> exportValue($hcdField) == "" && ($hcdField == "wage" || $hcdField == "driving_licence" || $hcdField == "national_service_completed" || $hcdField == "transport" || $hcdField == "way_of_working")) {
                            $new_employees_content[$hcdField] = 0;
                            if ($editedEmployee) {
                                $editedEmployee -> employee[$hcdField] = 0;
                            }
                        } else {
                            $new_employees_content[$hcdField] = $form -> exportValue($hcdField);
                            if ($editedEmployee) {
                                $editedEmployee -> employee[$hcdField] = $form -> exportValue($hcdField);
                            }
                        }
                    }
                }

                // Check whether a job has been defined for the new employee
                if ($values['branches_main'] && $values['branches_main'] != "0") {
                    $job_assigned = array( "branch_ID"       => $values['branches_main'],
                                           "job_description" => $values['all_jobs'],
                                           "role"            => $values['placement']);
                    $users_content = array($users_content, $job_assigned);
                }

            }

            //Check the user_type. If it's an id, it means that it's not one of the basic user types; so derive the basic user type and populate the user_types_ID field
            if (is_numeric($values['user_type'])) {
                $result = eF_getTableData("user_types", "id, basic_user_type", "id=".$values['user_type']);
                if (sizeof($result) > 0) {
                    $values['user_type']     = $result[0]['basic_user_type'];
                    $values['user_types_ID'] = $result[0]['id'];
                } else {
                    $values['user_type'] = 'student';
                }
            } else {
                $values['user_types_ID'] = 0;
            }

            /****************************/
            /*** ON ADDING A NEW USER ***/
            /****************************/
            if (isset($_GET['add_user'])) {

                // Create array from normal user data
                $users_content = array('login'          => $values['new_login'],
                                       'name'           => $values['name'],
                                       'surname'        => $values['surname'],
                                       'active'         => $values['active'],
                                       'email'          => $values['email'],
                                       'password'       => $values['password_'],
                                       'user_type'      => $values['user_type'],
                                       'languages_NAME' => $values['languages_NAME'],
                                       'user_types_ID'  => $values['user_types_ID']);

                foreach ($user_profile as $field) {                                         //Get the custom fields values
                    $users_content[$field['name']] = $values[$field['name']];
                }

                // Insert the user into the database
                try {
                    EfrontUser :: createUser($users_content);

                    // Assignment of user group
                    if ($values['group']) {

                        $group = new eFrontGroup($values['group']);
                        $group -> addUser($values['new_login']);
                    }

                    if (MODULE_HCD_INTERFACE) {
                        $new_employees_content['users_login'] = $values['new_login'];

                        EfrontHcdUser :: createUser($new_employees_content);
                        // Assignment of jobs - Check whether the user has been assigned to a branch with a job description
                        if ($job_assigned) {

                            // If the selected pair of branch-job description doesn't exist then insert it
                            $new_job_description_ID = eF_getJobDescriptionId($job_assigned['job_description'], $job_assigned['branch_ID']);
                            if ($job_assigned['role'] == '1') {
                                $employee = new EfrontSupervisor($new_employees_content, array());
                            } else {
                                $employee = new EfrontEmployee($new_employees_content, array());
                            }
                            $newUser = EfrontUserFactory :: factory ($values['new_login']);

                            $employee -> addJob ($newUser, $new_job_description_ID, $job_assigned['branch_ID'], $job_assigned['role']);
                        }

                        // The messages are included in the headers
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=users&edit_user=".$values['new_login']."&message="._EMPLOYEECREATED."&message_type=success");
                    } else {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=users&edit_user=".$values['new_login']."&tab=lessons&message="._USERCREATED."&message_type=success");
                    }
                    exit;
                } catch (Exception $e) {
                    $message      = _USERCOULDNOTBECREATED.': '.$e -> getMessage().'('.$e -> getCode().')';
                    $message_type = 'failure';
                }

            /***********************************/
            /*** ON EDITING AN EXISTING USER ***/
            /***********************************/
            } elseif (isset($_GET['edit_user'])) {
                $users_content = array('name'           => $values['name'],
                                       'surname'        => $values['surname'],
                                       'email'          => $values['email'],
                                       'user_types_ID'  => $values['user_types_ID']);

                if ($currentUser -> getType() == "administrator") {
                    $users_content['active']         = $values['active'];
                    $users_content['languages_NAME'] = $values['languages_NAME'];
                    $users_content['user_type']      = $values['user_type'];
                    $users_content['pending']        = 0;  //The user cannot be pending, since the admin sent this information
                }

                foreach ($user_profile as $field) {     //Get the custom fields values
                    $users_content[$field['name']] = $form -> exportValue($field['name']);
                }

                if (isset($values['password_']) && $values['password_']) {
                    $users_content['password'] = md5($values['password_'].G_MD5KEY);
                }

                // If name/surname changed then the sideframe must be reloaded
                if ($editedUser -> login == $currentUser -> login && ($editedUser -> user['name'] != $values['name'] || $editedUser -> user['surname'] != $values['surname'])) {
                    $smarty -> assign("T_REFRESH_SIDE", 1);
                    $smarty -> assign("T_PERSONAL_CTG", 1);
                }

                eF_updateTableData("users", $users_content, "login='".$_GET['edit_user']."'");

                // mpaltas temporary solution: manual OO to keep $editedUser object cache consistent
                if ($editedUser -> user['user_type'] != $values['user_type']) {
                    // the new instance will be of the updated type
                    $editedUser = EfrontUserFactory :: factory($_GET['edit_user']);
                }
                foreach ($users_content as $field => $content) {
                    $editedUser -> user[$field] = $content;
                }
                // end of mpaltas temp solution

                $currentUser -> getType() == "administrator" ? $message = _PERSONALDATACHANGESUCCESSADMIN : $message = _PERSONALDATACHANGESUCCESS;
                $message_type = 'success';

                if (isset($values['password_']) && $values['password_'] && $currentUser -> login == $_GET['edit_user']) {    //In case the user changed his password, change it in the session as well
                    $_SESSION['s_password'] = $users_content['password'];
                }

                // Assignment of user group
                if ($values['group'] != $init_group['groups_ID']) {
                    if ($init_group['groups_ID']) {
                        $editedUser -> removeGroups($init_group['groups_ID']);
                    }

                    if ($values['group']) {
                        $editedUser -> addGroups($values['group']);
                    } else {
                        $groups = eF_getTableDataFlat("groups","id","");
                        $editedUser -> removeGroups($groups['id']);
                    }
                }

                /** MODULE_HCD: Submission of employee data - check if user exists as employee otherwise -> insert data**/
                if(MODULE_HCD_INTERFACE) {
                    try {
                        // Check if there is a record with that user in the employee's table
                        // If not then the user was created in a non-HCD mode and a new Employee should be created
                        if (!isset($editedUser -> aspects['hcd'])) {

                            $new_employees_content['users_login'] = $_GET['edit_user'];
                            $editedUser -> aspects['hcd'] = new EfrontEmployee($new_employees_content, $job_assigned, $values['group']);
                            $editedEmployee = $editedUser -> aspects['hcd'];

                        // If the employee had already existed the change, then update him
                        } else {
                            $editedEmployee = $editedEmployee -> updateEmployeeData($new_employees_content);
                            $message = _EMPLOYEESDATASUCCESSFULLYUPDATED;
                            $message_type = 'success';

                        }

                        // Check whether the user has been assigned to a new job description
                        if ($ctg != "personal" && ($init_job['branch_ID'] != $values['branches_main'] || $init_job['description'] != $values['all_jobs'] || $init_job['supervisor'] != $values['placement'])) {
                            // Release the employee from his old job if one existed
                            if ($init_job['description'] && $init_job['branch_ID']) {
                                $old_job_description_id = eF_getTableData("module_hcd_job_description", "job_description_ID", "description = '".$init_job['description']."' AND branch_ID = '".$init_job['branch_ID']."'");
                                $editedEmployee = $editedEmployee -> removeJob($old_job_description_id[0]['job_description_ID']);   //($_GET['edit_user'], $old_job_description_id[0]['job_description_ID'], $init_job['branch_ID'], $init_job['supervisor'],&$message, &$message_type);
                            }

                            // Reassignment of jobs - new job description to the employee
                            if ($values['branches_main'] != 0) {
                                // If the selected pair of branch-job description doesn't exist then insert it
                                $new_job_description_ID = eF_getJobDescriptionId($values['all_jobs'], $values['branches_main']);

                                // Add this new job to the employee
                                $editedEmployee = $editedEmployee -> addJob($editedUser, $new_job_description_ID, $values['branches_main'], $values['placement'],$values['all_jobs']);

                                // If the branch has changed then change the select as well
                                //if ($init_job['branch_ID'] != $values['branches_main']) {
                                    //$my_branch_id = $values['branches_main'];
                                    //$workingBranch = new EfrontBranch($values['branches_main']);
                                    //$attributes = array('id' => 'jobs_main', 'class'=>'inputText','defaultVal'=> $values['all_jobs']);
                                    //$branch_jd_select = $workingBranch -> createJobDescriptionsSelect($attributes);
                                //}

                            }
                        }
                    } catch (Exception $e) {
                        $message      = _EMPLOYEESRECORDCOULDNOTBEUPDATED.': '.$e -> getMessage().'('.$e -> getCode().')';
                        $message_type = 'failure';
                    }

                }
            }

        }
    }
    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $renderer -> setRequiredTemplate(
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}');

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);
    $smarty -> assign('T_PERSONAL_DATA_FORM', $renderer -> toArray());

    // Put in the end to include possible updated values
    if ($init_job['branch_ID']) {
        $smarty -> assign("T_BRANCH_INFO", "href=\"" . $currentUser -> getType(). ".php?ctg=module_hcd&op=branches&edit_branch=" . $my_branch_id . "\"");
        $smarty -> assign('my_jobs_label', _JOBDESCRIPTION);
        $smarty -> assign('my_jobs_html', 1 );
    }

    /****************************************************************************************************************************************************/
    /***************************** [HCD] Retrieve all Employee information to appear on the form: job descriptions, skills, evaluations *****************/
    /****************************************************************************************************************************************************/
    /** GET DATA FOR EMPLOYEE'S PLACEMENTS AND SKILLS **/
    if (isset($_GET['edit_user'])) {

        $edit_user= $_GET['edit_user'];

        $smarty -> assign('T_USERNAME',"" . $editedUser -> user['name'] . " " . $editedUser -> user['surname'] . "");
        $smarty -> assign('T_SIMPLEUSERNAME',$editedUser -> user['name'] . " " . $editedUser -> user['surname']);
        $smarty -> assign('T_USER', $editedUser -> user);

        if (MODULE_HCD_INTERFACE) {
            /*** Get employee data ***/
            $smarty -> assign('T_EMPLOYEE',$editedEmployee -> employee);

            /*** Get employee jobs ***/
            $employees_placements = $editedEmployee -> getJobs();

            if(!empty($employees_placements)) {
                $smarty -> assign("T_PLACEMENTS_SIZE", sizeof($employees_placements));
                $smarty -> assign("T_PLACEMENTS", $employees_placements);
            } else {
                $smarty -> assign("T_PLACEMENTS_SIZE", 0);
            }


            /** Get skills **/
            // We do not use the getSkills() method, because it will only return the skills of the employee and we need to present them ALL
            $skill_categories = eF_getTableData("module_hcd_skill_categories","*","","description","");
            $skills = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_employee_has_skill ON (module_hcd_employee_has_skill.skill_ID = module_hcd_skills.skill_ID AND module_hcd_employee_has_skill.users_login='$edit_user') LEFT JOIN users ON module_hcd_employee_has_skill.author_login = users.login", "users_login, description,specification, module_hcd_skills.skill_ID, categories_ID, users.surname, users.name","");

            if (!empty($skills)) {
                $smarty -> assign("T_SKILLS", $skills);

                // Select for each skill category
                foreach ($skill_categories as $cat_id => $skill_category) {

                    $skill_categories[$cat_id]['skills'] = array();
                    foreach ($skills as $id => $skill) {
                        if ($skill['users_login'] != $editedUser -> login) {
                            unset($skills[$id]);
                        } else {
                            if ($skill['categories_ID'] == $skill_category['id']) {
                                $skill_ID = $skill['skill_ID'];
                                $skill_categories[$cat_id]['skills'][$skill_ID] = $skill;
                            }

                        }
                    }

                    $skill_categories[$cat_id]['size'] = sizeof($skill_categories[$cat_id]['skills']);

                 }

                 $smarty -> assign("T_SKILL_CATEGORIES", $skill_categories);
            }

            /** Get evaluations **/
            $evaluations = eF_getTableData("users JOIN module_hcd_events ON login = author","login, name, surname,module_hcd_events.*","module_hcd_events.users_login = '".$_GET['edit_user']."' && event_code = 10","","","login");
            $smarty -> assign('T_EVALUATIONS', $evaluations);

            $tests = eF_getTableData("done_tests JOIN tests ON done_tests.tests_ID = tests.id JOIN content ON tests.content_ID = content.id JOIN lessons ON lessons.id = content.lessons_ID","content.name as name, done_tests.timestamp, done_tests.score, done_tests.comments, lessons.id as lesson_id, tests.content_ID","users_login = '".$_GET['edit_user']."'", "content.name");
            foreach($tests as $test) {
                $test_id = $test['content_ID'];
                $user_tests[$test_id] = $test;
            }

            if ($ctg != "personal" && $editedUser -> getType() == "student") {
                $user_lessons = $editedUser -> getLessons(true);
                $user_courses = $editedUser -> getCourses(true);
                $userDoneTests = EfrontStats :: getStudentsDoneTests($user_lessons, $editedUser -> user['login']);
                $all_average = array("courses" => array("title" => _COURSESAVERAGE, "sum" => 0, "count" => 0),
                                     "lessons" => array("title" => _LESSONSAVERAGE, "sum" => 0, "count" => 0),
                                     "tests"   => array("title" => _TESTSAVERAGE, "sum" => 0, "count" => 0));

                $courses = array();
//pr($user_courses);
//pr($user_lessons);

                // COURSES
                foreach ($user_courses as $courseObject) {

                    $id = $courseObject-> course['id'];
                    $courses[$id]['name'] = $courseObject-> course['name'];
                    $courses[$id]['lessons'] = array();

                    $courses[$id]['completed'] = $courseObject-> userStatus['completed'];
                    if ($courses[$id]['completed']) {
                        $courses[$id]['score'] = $courseObject-> userStatus['score'];
                        $all_average['courses']['sum'] += $courses[$id]['score'];
                        $all_average['courses']['count']++;
//                    $courses[$id]['comments'] = $courseObject-> userStatus['comments'];
                    }


                    $courseObject -> getLessons(true);

                    // Get info for every lesson of the course
                    foreach($courseObject -> lessons as $lesson_id => $lesson_info) {
                        $courses[$id]['lessons'][$lesson_id] = array();
//                        $user_lessons[$lesson_id] -> userStatus =  EfrontStats :: getUsersLessonStatus($lesson_id, $editedUser -> user['login']);
                        $courses[$id]['lessons'][$lesson_id]['name'] = $user_lessons[$lesson_id] -> lesson['name'];
                        if ($user_lessons[$lesson_id] -> userStatus['completed']) {
                            $courses[$id]['lessons'][$lesson_id]['completed'] = $user_lessons[$lesson_id] -> userStatus['completed'];
                            $courses[$id]['lessons'][$lesson_id]['to_timestamp'] = $user_lessons[$lesson_id] -> userStatus['to_timestamp'];
                            $courses[$id]['lessons'][$lesson_id]['score'] = ceil($user_lessons[$lesson_id] -> userStatus['score']);

                            $all_average['lessons']['sum'] += $courses[$id]['lessons'][$lesson_id]['score'];
                            $all_average['lessons']['count']++;
                        }


                        // Course
                        $lesson_done_tests = sizeof($userDoneTests[$editedUser -> user['login']]);
                        if ($lesson_done_tests) {
                            $lesson_done_tests = 0;
                            $courses[$id]['lessons'][$lesson_id]['tests'] = array();
                            $test_sum = 0;
                            foreach ($userDoneTests[$editedUser -> user['login']] as $test_id => $test_info) {
                                if ($test_info[lessons_ID] == $lesson_id) {

                                    $courses[$id]['lessons'][$lesson_id]['tests'][$test_id]['score']     = formatScore($test_info['score']);
                                    $courses[$id]['lessons'][$lesson_id]['tests'][$test_id]['name']      = $test_info['name'];
                                    $courses[$id]['lessons'][$lesson_id]['tests'][$test_id]['timestamp'] = $test_info['timestamp'];
                                    $courses[$id]['lessons'][$lesson_id]['tests'][$test_id]['comments']  = $test_info['comments'];
                                    $test_sum += $test_info['score'];
                                    $lesson_done_tests++;
                                }

                            }

                            $all_average['tests']['sum'] += ($test_sum);
                            $all_average['tests']['count'] += $lesson_done_tests;
                            $courses[$id]['lessons'][$lesson_id]['tests_average'] = ceil($test_sum / $lesson_done_tests);
                            $courses[$id]['lessons'][$lesson_id]['tests_count'] = $lesson_done_tests;
                        } else {
                            $lesson_done_tests = 0;
                        }

                        // Remove the lesson from the lessons list, so that it does not appear again
                        unset($user_lessons[$lesson_id]);
                    }
                }


                $lessons = array();
//pr($userDoneTests);
                foreach ($user_lessons as $lessonObject) {
                    $id = $lessonObject -> lesson['id'];
                    $lessons[$id] = array();

                    // Get info for every lesson of the course
                    $lessons[$id]['name'] = $lessonObject -> lesson['name'];
//                    $lessonObject -> userStatus = EfrontStats :: getUsersLessonStatus($id, $editedUser -> user['login']);
//pr($lessonObject);
//echo "*".$lessonObject -> userStatus['completed']."*";
                    if ($lessonObject -> userStatus['completed']) {
                        $lessons[$id]['completed'] = $lessonObject -> userStatus['completed'];
                        $lessons[$id]['to_timestamp'] = $lessonObject -> userStatus['to_timestamp'];
                        $lessons[$id]['score'] = ceil($lessonObject -> userStatus['score']);
                        $all_average['lessons']['sum'] += $lessons[$id]['score'];
                        $all_average['lessons']['count']++;
                    }

                    $lesson_done_tests = sizeof($userDoneTests[$editedUser -> user['login']]);
                    if ($lesson_done_tests) {
                        $lesson_done_tests = 0;
                        $lessons[$id]['tests'] = array();
                        $test_sum = 0;

                        foreach ($userDoneTests[$editedUser -> user['login']] as $test_id => $test_info) {
                            if ($test_info[lessons_ID] == $id) {
                                $lessons[$id]['tests'][$test_id]['score']     = formatScore($test_info['score']);
                                $lessons[$id]['tests'][$test_id]['name']      = $test_info['name'];
                                $lessons[$id]['tests'][$test_id]['timestamp'] = $test_info['timestamp'];
                                $lessons[$id]['tests'][$test_id]['comments']  = $test_info['comments'];
                                $test_sum += $test_info['score'];
                                $lesson_done_tests++;
                            }
                        }

                        $all_average['tests']['sum'] += formatScore($test_sum);
                        $all_average['tests']['count'] += $lesson_done_tests;
                        $lessons[$id]['tests_average'] = formatScore($test_sum / $lesson_done_tests);
                        $lessons[$id]['tests_count'] = $lesson_done_tests;
                    }

                }
//pr($lessons);
//pr($courses);
                $smarty -> assign("T_COURSES",$courses);
                $smarty -> assign("T_LESSONS",$lessons);

                foreach ($all_average as $kind => $avg) {
                    if ($all_average[$kind]['count']) {
                        $all_average[$kind]['avg'] = formatScore(ceil($all_average[$kind]['sum'] / $all_average[$kind]['count']));
                    } else {
                        unset($all_average[$kind]);
                    }
                }
                $smarty -> assign("T_AVERAGES",$all_average);
            } else {
                $smarty -> assign("T_NOTRAINING", 1);
            }

            $smarty -> assign("T_EMPLOYEE_FORM_CAPTION", _EMPLOYEEFORM.":&nbsp;" . $editedUser -> user['name'] . "&nbsp;" . $editedUser -> user['surname']);
            if ($_GET['print_preview']) {
                $employee_form_options = array(                                                                          //Create calendar options and assign them to smarty, to be displayed at the calendar inner table
                                            array('text' => _PRINTEMPLOYEEFORM, 'image' => "16x16/printer.png", 'href' => $_SESSION['s_type'].".php?ctg=users&edit_user=".$editedUser->login."&print=1", "onClick" => "eF_js_showDivPopup('"._PRINTEMPLOYEEFORM."', new Array('800px','500px'))", "target" => "POPUP_FRAME"),
                                            array('text' => _PDFFORMAT, 'image' => "file_types/pdf.png", 'href' => $_SESSION['s_type'].".php?ctg=users&edit_user=".$editedUser->login."&pdf=1")
                                );
            } else {

                $employee_form_options = array(                                                                          //Create calendar options and assign them to smarty, to be displayed at the calendar inner table
                                            array('text' => _PRINTPREVIEW, 'image' => "16x16/view.png", 'href' => $_SESSION['s_type'].".php?ctg=users&edit_user=".$editedUser->login."&print_preview=1", "onClick" => "eF_js_showDivPopup('"._EMPLOYEEFORMPRINTPREVIEW."', new Array('800px','500px'))", "target" => "POPUP_FRAME"),
                                            array('text' => _PRINTEMPLOYEEFORM, 'image' => "16x16/printer.png", 'href' => $_SESSION['s_type'].".php?ctg=users&edit_user=".$editedUser->login."&print=1", "onClick" => "eF_js_showDivPopup('"._PRINTEMPLOYEEFORM."', new Array('800px','500px'))", "target" => "POPUP_FRAME"),
                                            array('text' => _PDFFORMAT, 'image' => "file_types/pdf.png", 'href' => $_SESSION['s_type'].".php?ctg=users&edit_user=".$editedUser->login."&pdf=1")
                                );
            }
            $smarty -> assign("T_EMPLOYEE_FORM_OPTIONS", $employee_form_options);


            /** Get history **/
            $history = eF_getTableData("module_hcd_events", "*", "users_login = '".$_GET['edit_user']."' AND event_code <10","timestamp","asc"); //"

            if(!empty($history)) {
                $smarty -> assign("T_HISTORY", $history);
            }

            try {
                $logoFile = new EfrontFile($configuration['logo']);
                $smarty -> assign("T_LOGO", 'logo/'.$logoFile['physical_name']);
                $logo_fn = G_IMAGESPATH."logo/".$logoFile['physical_name'];
            } catch (EfrontFileException $e) {
                $logo_fn = G_IMAGESPATH."logo.png";
                $smarty -> assign("T_LOGO", "logo.png");
            }

            if ($_GET['pdf']) {
                $editedEmployee -> printToPdf($editedUser, $evaluations, $skill_categories, $courses, $lessons, $all_average, $logo_fn);
                exit(0);
            }

            /** Get evaluations **/
            $evaluations = eF_getTableData("module_hcd_events", "*", "users_login = '".$_GET['edit_user']."' AND event_code >=10","timestamp");
            if(!empty($evaluations)) {
                $smarty -> assign("T_EVALUATION", $evaluations);
            }

            /****************************************************************************************************************************************/
            /****************************************************** [HCD] Include file manager ******************************************************/
            /****************************************************************************************************************************************/
            /** Create file manager **/
            // Folder where the anyone looks for user public files

                                /** TODO: THE FOLLOWING SHOULD GO AWAY ONCE WE MAKE SURE THAT ALL USERS HAVE BY DEFAULT A DIRECTORY **/
                                if (!is_dir(G_UPLOADPATH.$_GET['edit_user'].'/')) {
                                    mkdir(G_UPLOADPATH.$_GET['edit_user'].'/',0755);
                                }
                                if (!is_dir(G_UPLOADPATH.$_GET['edit_user'].'/module_hcd/')) {
                                    mkdir(G_UPLOADPATH.$_GET['edit_user'].'/module_hcd/',0755);
                                }
                                if (!is_dir(G_UPLOADPATH.$_GET['edit_user'].'/module_hcd/public/')) {
                                   mkdir(G_UPLOADPATH.$_GET['edit_user'].'/module_hcd/public/',0755);
                                }
                                $root_dir = G_UPLOADPATH . $_GET['edit_user'].'/module_hcd/public/';
                                /*UP TO HERE*/

            /* MODULE HCD SHOULD REDIRECT TO THE RIGHT TAB OF THE "EMPLOYEES" FORM */
            if (MODULE_HCD_INTERFACE) {
                if(($pos = strpos($_SERVER['REQUEST_URI'],"&tab")) != false) {
                  $trimmed_referer = substr($_SERVER['REQUEST_URI'],0,$pos);
                } else {
                    $trimmed_referer = $_SERVER['REQUEST_URI'];
                }
                $smarty -> assign("T_REFERER", $trimmed_referer);
            }
            $smarty -> assign("T_REFERER", $trimmed_referer);

            // The following line of code includes into the script the code needed for the file manager
            $loadScripts[] = 'drag-drop-folder-tree';
            $loadScripts[] = 'scriptaculous/effects';

            try {
                $filesystem = new FileSystemTree($root_dir);
                $filesystem -> handleAjaxActions($editedUser);
                if ($ctg != 'personal' || $currentUser -> user['user_type'] == 'administrator') {
                    $url = basename($_SERVER['PHP_SELF']).'?ctg=users&edit_user='.$_GET['edit_user'].'&tab=file_record';
                } else {
                    $url = basename($_SERVER['PHP_SELF']).'?ctg=personal&tab=file_record';
                }

                $options    = array('db_files_only' => false, 'share' => false);
                if (isset($_GET['ajax']) && $_GET['ajax'] == 'filesTable') {

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
                    echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options);
                    exit;
                }
                $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options));
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }

        }
        // end of MODULE_HCD_INTERFACE

    /****************************************************************************************************************************************************/
    /***************************** Retrieve all User information to appear on the form: personal information, lessons, courses, groups ******************/
    /****************************************************************************************************************************************************/

        /** Get groups **/
        $groups = eF_getTableData("groups", "*");
        $user_groups = $editedUser -> getGroups();
        $groups_size = sizeof($groups);
        for ($k = 0; $k < $groups_size; $k++) {
            $groups[$k]['partof'] = 0;
            if (in_array($groups[$k]['id'], array_keys($user_groups))) {
                $groups[$k]['partof'] = 1;
            } else if (!$groups[$k]['active'] || $currentUser -> getType() != "administrator") {
                unset($groups[$k]);
            }
        }

        if (!empty($groups)) {
           $smarty -> assign("T_USER_TO_GROUP_FORM", $groups);
        }
    }



} // apo to evaluation

?>





