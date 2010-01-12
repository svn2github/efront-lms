<?php
/**

* Emails managements

*

* This page is used to compose an email, and select its recipients

* @package eFront

* @version 1.0

*/
$load_editor = true;
$lessons = eF_getTableDataFlat("lessons", "id,name", "", "name");
sizeof($lessons) > 0 ? $lessons = array_combine($lessons['id'], $lessons['name']) : $lessons = array();
$smarty -> assign("T_LESSONS", $lessons);
$users = EfrontUser::getUsers(true);
$user_types = eF_getTableDataFlat("user_types", "user_type");
sizeof($user_types) > 0 ? $custom_user_types = array_combine($user_types['user_type'], $user_types['user_type']) : $custom_user_types = array();
$default_user_types = array("administrator" => _ADMINISTRATOR,
                            "professor" => _PROFESSOR,
                            "student" => _STUDENT);
$user_types = $default_user_types + $custom_user_types;
$form = new HTML_QuickForm("email_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=emails", "", null, true);
$form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$form -> addElement('radio', 'recipients', null, null, 'all_users', 'selected = "selected" onclick = "eF_js_selectRecipients(\'all_users\')"');//document.getElementById(\'lesson_recipients\').disabled = \'disabled\';document.getElementById(\'user_type_recipients\').disabled = \'disabled\';document.getElementById(\'user_recipients\').disabled = \'disabled\'"');
$form -> addElement('radio', 'recipients', null, null, 'active_users', 'onclick = "eF_js_selectRecipients(\'active_users\')"');//'onclick = "document.getElementById(\'lesson_recipients\').disabled = \'disabled\';document.getElementById(\'user_type_recipients\').disabled = \'disabled\';document.getElementById(\'user_recipients\').disabled = \'disabled\'"');
$form -> addElement('radio', 'recipients', null, null, 'specific_lesson', 'onclick = "eF_js_selectRecipients(\'specific_lesson\')"');//'onclick = "document.getElementById(\'lesson_recipients\').disabled = \'\';document.getElementById(\'user_type_recipients\').disabled = \'disabled\';document.getElementById(\'user_recipients\').disabled = \'disabled\'"');
$form -> addElement('select', 'lesson', null, $lessons, 'id = "lesson_recipients" class = "inputSelect" disabled = "disabled"');
$form -> addRule('lesson', _INVALIDFIELDDATA, 'checkParameter', 'id');

$form -> addElement('radio', 'recipients', null, null, 'specific_type', 'onclick = "eF_js_selectRecipients(\'specific_type\')"');//'onclick = "document.getElementById(\'lesson_recipients\').disabled = \'disabled\';document.getElementById(\'user_type_recipients\').disabled = \'\';document.getElementById(\'user_recipients\').disabled = \'disabled\'"');
$form -> addElement('select', 'user_type', null, $user_types, 'id = "user_type_recipients" class = "inputSelect" disabled = "disabled"');
$form -> addRule('user_type', _INVALIDFIELDDATA, 'checkParameter', 'text');

$form -> addElement('radio', 'recipients', null, null, 'specific_user', 'onclick = "eF_js_selectRecipients(\'specific_user\')"');//'onclick = "document.getElementById(\'lesson_recipients\').disabled = \'disabled\';document.getElementById(\'user_type_recipients\').disabled = \'disabled\';document.getElementById(\'user_recipients\').disabled = \'\'"');
$form -> addElement('select', 'user', null, $users, 'id = "user_recipients" class = "inputSelect" disabled = "disabled"');
$form -> addRule('user', _INVALIDFIELDDATA, 'checkParameter', 'login');

$form -> setDefaults(array('recipients' => 'active_users'));


// GGET DATA FOR CREATING THE SELECTS
/**************************************************/
$form -> addElement('text', 'subject', _SUBJECT, 'class = "emailSubjectText"');
$form -> addRule('subject', _THEFIELD.' '._SUBJECT.' '._ISMANDATORY, 'required', null, 'client');
$form -> addRule('subject ', _INVALIDFIELDDATA, 'checkParameter', 'text');
$form -> addElement('textarea', 'body', _EMAILBODY, 'class = "emailBodyTextarea mceEditor"');
$form -> addElement('submit', 'send_email', _SENDEMAIL, 'class = "flatButton"');
if ($form -> isSubmitted()) {
    if ($form -> validate()) {
        switch ($form -> exportValue('recipients')) {
            case 'all_users':
                $result = eF_getTableDataFlat("users", "login, email");
                break;
            case 'active_users':
                $result = eF_getTableDataFlat("users", "login, email", "active=1");
                break;
            case 'specific_lesson':
                $result = eF_getTableDataFlat("users, users_to_lessons", "login, email", "users.active=1 AND users_to_lessons.active=1 AND users.login=users_to_lessons.users_LOGIN AND users_to_lessons.lessons_ID=".($form -> exportValue('lesson')));
                break;
            case 'specific_type':
                $result = eF_getTableDataFlat("users", "login, email", "users.active=1 AND users.user_type='".($form -> exportValue('user_type'))."'");
                break;
            case 'specific_user':
                $result = eF_getTableDataFlat("users", "login, email", "login = '".($form -> exportValue('user'))."'");
                break;
             /** MODULE HCD: Create recipients list from the HCD selects -- NO if $module... needed here !!!**/
            case 'specific_branch_job_description':
                $branches_list = $form -> exportValue('branch_recipients');
                if ($_POST['include_subbranches']) {
                    // Find all subbranches - the $branches array has been defined during the creation of the list
                    $subbranches = eF_subBranches($form -> exportValue('branch_recipients'),$branches);
                    $branches_list .= "," . implode(",",$subbranches);
                }
                if ($form -> exportValue('job_description_recipients') != "0") {
                    $result = eF_getTableDataFlat("users JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_login JOIN module_hcd_job_description ON module_hcd_job_description.job_description_ID = module_hcd_employee_has_job_description.job_description_ID","distinct login, email", "users.active = 1 AND module_hcd_job_description.description = '".$form -> exportValue('job_description_recipients') ."' AND module_hcd_job_description.branch_ID IN (".$branches_list.") ");
                } else {
                    $result = eF_getTableDataFlat("users JOIN module_hcd_employee_works_at_branch ON users.login = module_hcd_employee_works_at_branch.users_login","distinct login, email", "users.active = 1 AND module_hcd_employee_works_at_branch.branch_ID IN (".$branches_list.") AND module_hcd_employee_works_at_branch.assigned = '1'");
                }
                break;
            case 'specific_skill':
                $result = eF_getTableDataFlat("users JOIN module_hcd_employee_has_skill ON users.login = module_hcd_employee_has_skill.users_login","distinct login, email", "users.active = 1 AND module_hcd_employee_has_skill.skill_ID = '".$form -> exportValue('skill_recipients') ."'");
                break;
            case 'specific_group':
                $result = eF_getTableDataFlat("users JOIN users_to_groups ON users.login = users_to_groups.users_LOGIN","distinct login, email", "users_to_groups.groups_ID = '".$form -> exportValue('group_recipients') ."'");
                break;
            default:
                break;
        }
        foreach ($result['email'] as $key => $value) {
            if (!eF_checkParameter($value, 'email')) {
                $message .= 'Notice: '._USER.' '.$result['login'][$key].' '._HASINVALIDEMAILADDRESS.': '.$value.'<br />';
                unset($result['email'][$key]);
            }
        }
        if (sizeof($result['email']) > 0) {
            $recipient = implode(", ", $result['email']);
            // Debug:
            //pr($result);
            $newresult = eF_mail($GLOBALS['configuration']['system_email'], $recipient, $form -> exportValue('subject'), $form -> exportValue('body'));
            if ($newresult == true) {
                $message .= _EMAILSENDAT.sizeof($result['email']).' '._USERS;
                $message_type = 'success';
            } else {
            //    $message     .= _EMAILCOULDNOTBESENDBECAUSE.': '.mb_substr($result -> getMessage(), 0, mb_strpos($result -> getMessage(), ':'));
                $message_type = 'failure';
            }
        }
    }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
$form -> setRequiredNote(_REQUIREDNOTE);
$form -> accept($renderer);
$smarty -> assign('T_EMAIL_FORM', $renderer -> toArray());
?>
