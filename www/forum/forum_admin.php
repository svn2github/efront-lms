<?php
/**
* forum admin page
*
* This page is used to configure forum
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

        if ($currentUser -> coreAccess['forum'] == 'hidden') {
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

$form = new HTML_QuickForm("forum_admin_form", "post", "", "", null, true);  //Build the form
$form -> addElement('select', 'allow_html',        _ALLOWHTMLFPM,  array(1 => _YES, 0 => _NO));
$form -> addElement('select', 'polls',             _ACTIVATEPOLLS, array(1 => _YES, 0 => _NO));
$form -> addElement('select', 'forum_attachments', _ALLOWATTACHMENTSINF, array(1 => _YES, 0 => _NO));
$form -> addElement('select', 'students_add_forums', _USERSMAYADDFORUMS, array(0 => _NO, 1 => _YES, ));
$form -> addElement('text', 'pm_quota',          _PMQUOTA,             'class = "inputText" style = "width:40px"');
$form -> addElement('text', 'pm_attach_quota',   _PMATTACHMENTSQUOTA,  'class = "inputText" style = "width:40px"');
$form -> addRule('pm_quota',        _THEFIELD.' "'._PMQUOTA.'" '._MUSTBENUMERIC,            'numeric', null, 'client');
$form -> addRule('pm_attach_quota', _THEFIELD.' "'._PMATTACHMENTSQUOTA.'" '._MUSTBENUMERIC, 'numeric', null, 'client');

$form -> addElement('submit', 'submit_settings', _SUBMIT, 'class = "flatButton"');

$current_values = eF_getTableDataFlat("f_configuration", "*");
$current_values = array_combine($current_values['name'], $current_values['value']);
$form -> setDefaults($current_values);

if ($form -> isSubmitted() && $form -> validate()) {                                                              //If the form is submitted and validated
    $values = $form -> exportValues();
    eF_deleteTableData("f_configuration");

    $fields[] = array('name' => 'allow_html',          "value" => $values['allow_html']          ? 1 : 0);
    $fields[] = array('name' => 'polls',               "value" => $values['polls']               ? 1 : 0);
    $fields[] = array('name' => 'forum_attachments',   "value" => $values['forum_attachments']   ? 1 : 0);
    $fields[] = array('name' => 'students_add_forums', "value" => $values['students_add_forums'] ? 1 : 0);
    $fields[] = array('name' => 'pm_quota',            "value" => $values['pm_quota']);
    $fields[] = array('name' => 'pm_attach_quota',     "value" => $values['pm_attach_quota']);

    foreach ($fields as $field) {
        eF_insertTableData("f_configuration", array("name" => $field['name'], "value" => $field['value']));
    }
    
    $message      = _SUCCESSFULLYINSERTEDVALUES;
    $message_type = 'success';
}

$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer

$renderer -> setRequiredTemplate (
   '{$html}{if $required}
        &nbsp;<span class = "formRequired">*</span>
    {/if}');
$form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
$form -> setRequiredNote(_REQUIREDNOTE);
$form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

$smarty -> assign('T_CONFIGURATION_FORM', $renderer -> toArray());                     //Assign the form to the template



$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> display("forum/forum_admin.tpl");

?>

