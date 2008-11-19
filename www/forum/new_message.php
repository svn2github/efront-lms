<?php
/**
* Send a personal message
*
* This page provides sending personal messages functionality
*
* @package eFront
* @version 1.0
*/
session_cache_limiter('none');
session_start();

$path = "../../libraries/";


/** Configuration file.*/
include_once $path."configuration.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

/*Check the user type. If the user is not valid, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
        $smarty -> assign("T_CURRENT_USER", $currentUser);

        if (MODULE_HCD_INTERFACE) {
            $currentUser -> aspects['hcd'] = EfrontEmployeeFactory :: factory($_SESSION['s_login']);
            $employee = $currentUser -> aspects['hcd'];
        }
        
        if ($_SESSION['s_lessons_ID']) {
            $userLessons = $currentUser -> getLessons();
            $currentUser -> applyRoleOptions($userLessons[$_SESSION['s_lessons_ID']]);                //Initialize user's role options for this lesson
            $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
        } else {
            $currentUser -> applyRoleOptions();                //Initialize user's role options for this lesson                   
        }

        if ($currentUser -> coreAccess['personal_messages'] == 'hidden') {
            header("location:".G_SERVERNAME.$_SESSION['s_type'].".php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        header("location:index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    header("location:index.php?message=".urlencode(_YOUCANNOTACCESSTHISPAGE)."&message_type=failure");
    exit;
}

$load_scripts = array('eF_scripts','scriptaculous/prototype','scriptaculous/scriptaculous','scriptaculous/effects','scriptaculous/controls');

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $message_type = $_GET['message_type'];
    
}
// Use this variable for turning on/off the module hcd interface
$module_hcd_interface = MODULE_HCD_INTERFACE;
$smarty -> assign("T_MODULE_HCD_INTERFACE", $module_hcd_interface);

$lessons    = eF_getTableDataFlat("lessons", "id,name", "", "name");
sizeof($lessons) > 0 ? $lessons = array_combine($lessons['id'], $lessons['name']) : $lessons = array();
$smarty -> assign("T_LESSONS", $lessons);

$courses    = eF_getTableDataFlat("courses", "id,name", "", "name");
sizeof($courses) > 0 ? $courses = array_combine($courses['id'], $courses['name']) : $courses = array();
$smarty -> assign("T_COURSES", $courses);


$users = EfrontUser::getUsers(true);
$roles = EfrontUser :: getRoles(true);

$form = new HTML_QuickForm("new_message_form", "post", "forum/new_message.php", "", "id = 'new_message_form'", true);  //Build the form

$form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');

$form -> addElement('radio', 'recipients', null, null, 'only_specific_users', 'id = "only_specific_users" onclick = "eF_js_selectRecipients(\'only_specific_users\')"');
$form -> addElement('radio', 'recipients', null, null, 'active_users', 'onclick = "eF_js_selectRecipients(\'active_users\')"');

$form -> addElement('radio', 'recipients', null, null, 'specific_course', 'onclick = "eF_js_selectRecipients(\'specific_course\')"');
$form -> addElement('select', 'specific_course',    null, $courses, 'id = "course_recipients" class = "inputSelectLong" disabled = "disabled"');
$form -> addElement('advcheckbox', 'specific_course_completed', _COMPLETED, null, 'class = "inputCheckbox" id="specific_course_completed_check" style="visibility:hidden" checked=""');

$form -> addElement('radio', 'recipients', null, null, 'specific_lesson', 'onclick = "eF_js_selectRecipients(\'specific_lesson\')"');
$form -> addElement('select', 'lesson',    null, $lessons, 'id = "lesson_recipients" class = "inputSelectLong" disabled = "disabled"');
$form -> addRule('lesson', _INVALIDFIELDDATA, 'checkParameter', 'id');

$form -> addElement('radio', 'recipients', null, null, 'specific_lesson_professor', 'onclick = "eF_js_selectRecipients(\'specific_lesson_professor\')"');
$form -> addElement('select', 'professor',    null, $lessons, 'id = "lesson_professor_recipients" class = "inputSelectLong" disabled = "disabled"');
$form -> addRule('lesson', _INVALIDFIELDDATA, 'checkParameter', 'id');

$form -> addElement('radio', 'recipients', null, null, 'specific_type', 'onclick = "eF_js_selectRecipients(\'specific_type\')"');
$form -> addElement('select', 'user_type', null, $roles, 'id = "user_type_recipients" class = "inputSelectLong" disabled = "disabled"');
$form -> addRule('user_type', _INVALIDFIELDDATA, 'checkParameter', 'text');

//$form -> addElement('radio', 'recipients', null, null, 'specific_user', 'onclick = "eF_js_selectRecipients(\'specific_user\')"');
//$form -> addElement('select', 'user',      null, array_combine($users['login'], $users['login']), 'id = "user_recipients" class = "inputSelectLong" disabled = "disabled"');
//$form -> addRule('user', _INVALIDFIELDDATA, 'checkParameter', 'login');

$form -> setDefaults(array('recipients' => 'only_specific_users'));



// Hidden for maintaining the previous_url value
$form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');

$previous_url = $_SERVER['HTTP_REFERER'];
if ($position = strpos($previous_url, "&message")) {
        $previous_url = substr($previous_url, 0, $position);
}
if ($position2 = strpos($previous_url, "sidebar")) {
        
} else {
    $form -> setDefaults(array( 'previous_url'     =>  $previous_url));
}    

/* **************************************************** */
/** MODULE HCD: Insert new radio buttons for more recipient options **/
/* **************************************************** */
// GGET DATA FOR CREATING THE SELECTS
if ($module_hcd_interface) {
    $branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");

    if (!empty($branches)) {
        $branches_list = array();
        include ("../../libraries/module_hcd_tools.php");
        $branches_list = eF_createBranchesTreeSelect($branches,1);
    } else {
        $branches_list = array("0" => _NOBRANCHESHAVEBEENREGISTERED);
//        $branches_list[0] = _NOBRANCHESHAVEBEENREGISTERED;
        $disable_branches = "disabled=\"disabled\"";
    }
//TODO:PROBLEM
//pr($branches_list);
    $job_descriptions = eF_getTableData("module_hcd_job_description", "distinct description","");

    if (!empty($job_descriptions)) {
        $job_description_list = array("0" => _ANYJOBDESCRIPTION);
        foreach ($job_descriptions as $job_description) {
            $log = $job_description['description'];
            $job_description_list["$log"] = $job_description['description'];
        }
    } else {
        $job_description_list["0"] = _NOJOBDESCRIPTIONSSHAVEBEENREGISTERED;
        $disable_job_descriptions = "disabled=\"disabled\"";
    }

    $skills = eF_getTableData("module_hcd_skills", "skill_ID, description","");
    $skills_list = array();
    if (!empty($skills)) {
        foreach ($skills as $skill) {
            $log = $skill['skill_ID'];
            $skills_list["$log"] = $skill['description'];
        }
    } else {
        $skills_list["0"] = _NOSKILLSHAVEBEENREGISTERED;
        $disable_skills = "disabled=\"disabled\"";
    }


    $form -> addElement('radio', 'recipients', null, null, 'to_supervisors', 'onclick = "eF_js_selectRecipients(\'to_supervisors\')"');
    $form -> addElement('radio', 'recipients', null, null, 'to_branch_supervisors', 'onclick = "eF_js_selectRecipients(\'to_branch_supervisors\')"');

    $form -> addElement('radio', 'recipients', null, null, 'specific_branch_job_description', $disable_branches . ' onclick = "eF_js_selectRecipients(\'specific_branch_job_description\')"');
    $form -> addElement('select', 'branch_recipients', null, $branches_list, 'id = "branch_recipients" class = "inputSelectLong" disabled = "disabled"');
    $form -> addElement('advcheckbox', 'include_subbranches', _INCLUDESUBBRANCHES, null, 'class = "inputCheckbox" id="include_subbranches" style="visibility:hidden" checked=""');

    $form -> addElement('radio', 'recipients', null, null, 'specific_job_description', $disable_job_descriptions . ' onclick = "eF_js_selectRecipients(\'specific_job_description\')"');
    $form -> addElement('select', 'job_description_recipients',null, $job_description_list, 'id = "job_description_recipients" class = "inputSelectLong" disabled = "disabled"');

    $form -> addElement('radio', 'recipients', null, null, 'specific_skill', $disable_skills . ' onclick = "eF_js_selectRecipients(\'specific_skill\')"');
    $form -> addElement('select', 'skill_recipients', null, $skills_list, 'id = "skill_recipients" class = "inputSelectLong" disabled = "disabled"');
}
/**************************************************/

// User groups in any case
$groups = eF_getTableData("groups", "id, name", "active=1");
$groups_list = array();
if (!empty($groups)) {
    foreach ($groups as $group) {
        $log = $group['id'];
        $groups_list["$log"] = $group['name'];
    }
} else {
    $groups_list["0"] = _NOGROUPSDEFINED;
    $disable_groups = "disabled=\"disabled\"";
}

$form -> addElement('radio', 'recipients', null, null, 'specific_group', $disable_groups . ' onclick = "eF_js_selectRecipients(\'specific_group\')"');
$form -> addElement('select', 'group_recipients', null, $groups_list, 'id = "group_recipients" class = "inputSelectLong" disabled = "disabled"');

$form -> addElement('text', 'recipient', _RECIPIENT, 'id = "autocomplete" class = "inputText" style = "width:400px" onKeyDown="if (!additional_recipients_hidden) { show_hide_additional_recipients();}" ');

$form -> addElement('text', 'subject',   _SUBJECT,   'id = "msg_subject" class = "inputText" style = "width:400px"');
//$form -> addRule('subject',   _THEFIELD.' "'._SUBJECT.'" '._ISMANDATORY,   'required', null, 'client');

$form -> addElement('file', 'attachment[0]', _ATTACHMENT, null, 'class = "inputText"');
$form -> addElement('checkbox', 'email', _SENDASEMAILALSO, null, 'class = "inputCheckBox"');
$form -> addElement('textarea', 'body', _BODY, 'class = "simpleEditor" style = "width:100%;height:200px"');

$form -> addElement('submit', 'submit_send_message',    _SENDMESSAGE,    'class = "flatButton"');
$form -> addElement('submit', 'submit_preview_message', _PREVIEWMESSAGE, 'class = "flatButton"');

if (isset($_GET['chat_invite']) && (eF_checkParameter($_GET['chat_invite'], id) || $_GET['chat_invite'] == 0) ) {
    $subject_str = _CHATINVITATION;
    if ($_GET['chat_invite'] == 0) {
        $room_name = _EFRONTMAIN;
    } else {
        $result = eF_getTableData("chatrooms", "name", "id = '".$_GET['chat_invite']."'");
        $room_name = $result[0]['name'];    
    }
    
    $body_str = _THEUSER." <i>".$_SESSION["s_login"]."</i> "._INVITESYOUTOJOINTHE." <b>" . $room_name . "</b>";
    $form -> setDefaults(array('subject' => $subject_str, 'body' => $body_str));
}
if (isset($_GET['recipient'])) {
    $form -> setDefaults(array('recipient' => $_GET['recipient']));
}
if (isset($_GET['reply'])) {
    $recipient = ef_getTableData("f_personal_messages", "sender", "id=".$_GET['reply']);
    $form -> setDefaults(array('recipient' => $recipient[0]['sender']));
}


if ($form -> isSubmitted() && $form -> validate()) {
    $values = $form -> exportValues();

    // The field with the recipients is no longer mandatory: we should check if it is empty
    if ($values['recipient']) {
        $values['recipient'] = str_replace(" ","",$values['recipient']);
        $values['recipient'] = trim($values['recipient']);

        if (mb_substr($values['recipient'], -1) == ';') {                            //remove trailing ; character
            $values['recipient'] = mb_substr($values['recipient'], 0, -1);
        }

        $recipients = explode(";", $values['recipient']);

        if (in_array("[*]",$recipients)){
            if ($_SESSION['s_type'] == "administrator"){
                $rec_users = eF_getTableDataFlat("users","login","active=1");       // entry [*] means message for all system users
                $recipients = array_merge($recipients, $users);
            } elseif($_SESSION['s_type'] == "professor"){
                $temp   = ef_getProfessorStudents($_SESSION['s_login']);        // entry [*] means message for all professor's students
                for($k = 0; $k < sizeof($temp); $k++){
                    $rec_users[] = $temp[$k]['login'];
                }
                $recipients = array_merge($recipients,$rec_users);
            }
            unset($recipients[array_search('[*]', $recipients)]);
        }

        $recipients = array_combine(array_values($recipients),array_values($recipients));
     }

//pr($recipients);


    switch ($form -> exportValue('recipients')) {
       // case 'all_users':
       //     $result = eF_getTableDataFlat("users", "login");
       //     break;
        case 'active_users':
            $result = eF_getTableDataFlat("users", "login", "active=1");
            break;
        case 'specific_lesson':
            $result = eF_getTableDataFlat("users, users_to_lessons", "login", "users.active=1 AND users_to_lessons.active=1 AND users.login=users_to_lessons.users_LOGIN AND users_to_lessons.lessons_ID=".($form -> exportValue('lesson')));
            break;
        case 'specific_course':
            if ($_POST['specific_course_completed']) {
                $and_completed_criterium = " AND users_to_courses.completed = 1 ";
            } else {
                $and_completed_criterium = " AND users_to_courses.completed = 0 ";
            }
            $result = eF_getTableDataFlat("users, users_to_courses", "login", "users.active=1 AND users_to_courses.active=1 AND users.login=users_to_courses.users_LOGIN " . $and_completed_criterium . " AND users_to_courses.courses_ID=".($form -> exportValue('specific_course')));
            break;
        case 'specific_lesson_professor':
            $result = eF_getTableDataFlat("users, users_to_lessons", "login", "users.active=1 AND users_to_lessons.active=1 AND users_to_lessons.user_type = 'professor' AND users.login=users_to_lessons.users_LOGIN AND users_to_lessons.lessons_ID=".($form -> exportValue('professor')));
            break;
        case 'specific_type':
            if (!is_numeric($form -> exportValue('user_type'))) { 
                $result = eF_getTableDataFlat("users", "login", "users.active=1 AND users.user_type='".($form -> exportValue('user_type'))."'");
            } else {
                $result = eF_getTableDataFlat("users", "login", "users.active=1 AND users.user_types_ID='".($form -> exportValue('user_type'))."'");
            }
            break;
        case 'specific_user':
            $result = eF_getTableDataFlat("users", "login", "login = '".($form -> exportValue('user'))."'");
            break;
        case 'specific_group':
            $result = eF_getTableDataFlat("users JOIN users_to_groups ON users.login = users_to_groups.users_LOGIN","distinct login", "users_to_groups.groups_ID = '".$form -> exportValue('group_recipients') ."'");
            break;
         /** MODULE HCD: Create recipients list from the HCD selects -- NO if $module... needed here !!!**/
        case 'to_supervisors':
            // Find all branches where this employee works
            $branches_working = eF_getTableData("module_hcd_employee_works_at_branch JOIN module_hcd_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID","module_hcd_branch.*", "users_login = '".$_SESSION['s_login']."' AND assigned = '1'");

            $supervising_branches = array();
            foreach ($branches_working as $branch) {
                // The $branches variable is defined above
                $this_branch_sbs = eF_getBranchAncestors($branch, $branches);

                if ($this_branch_sbs) {
                    $supervising_branches = array_merge($supervising_branches, $this_branch_sbs);
                }
                $supervising_branches = array_merge($supervising_branches, array($branch['branch_ID'] => $branch['branch_ID']));

            }

            $result = eF_getTableDataFlat("module_hcd_employee_works_at_branch", "distinct users_login as login", "supervisor = '1' AND branch_ID IN ('". implode( $supervising_branches, "','") ."')");
            break;
        case 'to_branch_supervisors':
            // Find all branches where this employee works
            $branches_working = eF_getTableDataFlat("module_hcd_employee_works_at_branch JOIN module_hcd_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID","module_hcd_branch.branch_ID", "users_login = '".$_SESSION['s_login']."' AND assigned = '1'");
            $result = eF_getTableDataFlat("module_hcd_employee_works_at_branch", "distinct users_login as login", "supervisor = '1' AND assigned='1' AND branch_ID IN ('". implode( $branches_working['branch_ID'], "','") ."')");
            break;

        case 'specific_branch_job_description':

            $branches_list = $form -> exportValue('branch_recipients');
            if ($_POST['include_subbranches']) {
                // Find all subbranches - the $branches array has been defined during the creation of the list
                $subbranches = eF_subBranches($form -> exportValue('branch_recipients'),$branches);
                $subbranches[] = $form -> exportValue('branch_recipients');
                $branches_list .= "','" . implode(",",$subbranches);
            }

            if ($form -> exportValue('job_description_recipients') != "0") {
                $result = eF_getTableDataFlat("users JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_login JOIN module_hcd_job_description ON module_hcd_job_description.job_description_ID = module_hcd_employee_has_job_description.job_description_ID","distinct login", "users.active = 1 AND module_hcd_job_description.description = '".$form -> exportValue('job_description_recipients') ."' AND module_hcd_job_description.branch_ID IN ('".$branches_list."') ");
            } else {
                $result = eF_getTableDataFlat("users JOIN module_hcd_employee_works_at_branch ON users.login = module_hcd_employee_works_at_branch.users_login","distinct login", "users.active = 1 AND module_hcd_employee_works_at_branch.branch_ID IN ('".$branches_list."') AND module_hcd_employee_works_at_branch.assigned = '1'");
            }

            break;
        case 'specific_job_description':
            if ($form -> exportValue('job_description_recipients') != "0") {
                $result = eF_getTableDataFlat("users JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_login JOIN module_hcd_job_description ON module_hcd_job_description.job_description_ID = module_hcd_employee_has_job_description.job_description_ID","distinct login", "users.active = 1 AND module_hcd_job_description.description = '".$form -> exportValue('job_description_recipients') ."'");
            } else {
                $result = eF_getTableDataFlat("users JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_login","distinct login", "users.active = 1");
            }

            break;
        case 'specific_skill':
            $result = eF_getTableDataFlat("users JOIN module_hcd_employee_has_skill ON users.login = module_hcd_employee_has_skill.users_login","distinct login", "users.active = 1 AND module_hcd_employee_has_skill.skill_ID = '".$form -> exportValue('skill_recipients') ."'");
            break;


        default:
            break;
    }

    // Using this approach to enable the merging of the two arrays
    if (!empty($result)) {
        $result = array_combine(array_values($result['login']),array_values($result['login']));
    }
    //pr($result);

    // Using the array_values function to form 0=>login1,1=>login2... instead of login1=>login1, login2=>login2
    if (isset($recipients) && !empty($result)) {
        $recipients = array_values(array_merge($recipients, $result));
    } else if (!empty($result)) {
        $recipients = array_values($result);
    }
    // else the $recipients = $recipients

    // If only a massive sent selection was used and no employee was found
    if (isset($recipients)) {
        $pm = new eF_PersonalMessage($_SESSION['s_login'], $recipients, $values['subject'], $values['body']);

        if ($_FILES['attachment']['name'][0] != "") {
            if ($_FILES['attachment']['size'][0] ==0 || $_FILES['attachment']['size'][0] > G_MAXFILESIZE ) {                                                           //If the directory could not be created, display an erro message
                $message      = _EACHFILESIZEMUSTBESMALLERTHAN." ".G_MAXFILESIZE." Bytes";
                $message_type = 'failure';
            }
            //Upload user avatar file
            $pm -> sender_attachment_timestamp = time();

            $user_dir = G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/Sent/'.$pm -> sender_attachment_timestamp.'/';
            mkdir($user_dir,0755);
            $filesystem = new FileSystemTree($user_dir);
            $uploadedFile = $filesystem -> uploadFile('attachment', $user_dir, 0);

            $pm -> sender_attachment_fileId =  $uploadedFile['id'];
            $pm -> setAttachment($uploadedFile['path']);
        }
        
        if ($pm -> send($values['email'], $values)) {
            $message      = _MESSAGEWASSENT;
            $message_type = 'success';
        } else {
            $message      = $pm -> errorMessage;
            $message_type = 'failure';
        }
    } else {
        $message      = _NORECIPIENTSHAVEBEENFOUND;
        $message_type = 'failure';
    }

//    pr($pm);pr($message);exit;
//echo $form->exportValue('previous_url'). '&message='.$message.'&message_type='.$message_type;

    if (strpos($form->exportValue('previous_url'), "new_message.php")) {
        header("location: ".G_SERVERNAME."forum/messages_index.php?message=".urlencode($message)."&message_type=".$message_type);
    } else {

        if (strpos($form->exportValue('previous_url'), '?')) {
            header('location:'.$form->exportValue('previous_url'). '&message='.urlencode($message).'&message_type='.$message_type);
        } else {
            header('location:'.$form->exportValue('previous_url'). '?message='.urlencode($message).'&message_type='.$message_type);
        }
    }
//
//    $smarty -> assign("T_RELOAD_PARENT", true);

}

$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer

$renderer -> setRequiredTemplate (
   '{$html}{if $required}
        &nbsp;<span class = "formRequired">*</span>
    {/if}');

$form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
$form -> setRequiredNote(_REQUIREDNOTE);
$form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

$smarty -> assign('T_ADD_MESSAGE_FORM', $renderer -> toArray());                     //Assign the form to the template

$smarty -> assign('T_MENUCTG', 'messages');
$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($load_scripts));
$smarty -> assign("T_HEADER_EDITOR", true);
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);


$smarty -> assign("T_SHOWFOOTER", $GLOBALS['configuration']['show_footer']);      //Needed by footer
$smarty -> assign("T_ADMINEMAIL", $GLOBALS['configuration']['system_email']);      //Needed by footer
$smarty -> assign("T_QUERIES", $numberOfQueries);           //Needed by footer


$smarty -> display("forum/new_message.tpl");





?>