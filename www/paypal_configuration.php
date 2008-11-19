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

$path = "../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

if (eF_checkUser($_SESSION['s_login'], $_SESSION['s_password']) != 'administrator') {                   //Only a valid user may access this page
    header("location: index.php");
    exit;
}

$form = new HTML_QuickForm("config_form", "post", 'paypal_configuration.php', "", null, true);
$form -> addElement('text', 'paypalbusiness', null, 'class = "inputText"');
$form -> addElement('checkbox', 'mailstudents',  null, null, 'class = "inputCheckBox"');
$form -> addElement('checkbox', 'mailprofessors', null, null, 'class = "inputCheckBox"');
$form -> addElement('checkbox', 'mailadmins', null, null, 'class = "inputCheckBox"');
$form -> addElement('submit', 'submit_config',_SAVECHANGES, 'class = "flatButton"');       
$form->addRule('paypalbusiness', _PAYPALBUSINESSMAILPLZ, 'required', null, 'client');

$config_data = eF_getTableData("paypal_configuration", "*", "");
if (sizeof($config_data)>0) {
	$form -> setDefaults(array("paypalbusiness" => $config_data[0]['paypalbusiness'],
							   "mailstudents"	=> $config_data[0]['mailstudents'],
							   "mailprofessors"	=> $config_data[0]['mailprofessors'],
							   "mailadmins"		=> $config_data[0]['mailadmins']));
}

if ($form -> isSubmitted() && $form -> validate()) {
	$fields = array('paypalbusiness'	=> $form -> exportValue("paypalbusiness"),
					'mailstudents'		=> $form -> exportValue("mailstudents") ? 1 : 0,
					'mailprofessors'	=> $form -> exportValue("mailprofessors") ? 1 : 0,
					'mailadmins'		=> $form -> exportValue("mailadmins") ? 1 : 0);
	if (sizeof($config_data) > 0) {
		if (eF_updateTableData("paypal_configuration", $fields, "1=1")) {
			$message      = _UPDATESUCCESFULLYMADE;
			$message_type = 'success';
		} else {
			$message      = _SOMEPROBLEMOCCURED;
			$message_type = 'failure';
		}
	} else {
		if (eF_insertTableData("paypal_configuration", $fields)) {
			$message      = _UPDATESUCCESFULLYMADE;
			$message_type = 'success';
		} else {
			$message      = _SOMEPROBLEMOCCURED;
			$message_type = 'failure';
		}		
	}
}

$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form -> accept($renderer);
$smarty -> assign('T_CONFIG_FORM_DEFAULT', $renderer -> toArray()); 

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array());
$smarty -> assign("T_HEADER_EDITOR", $load_editor);
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> display("paypal_configuration.tpl");

?>

