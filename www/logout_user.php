<?php
/**
* Logs out a user.
* 
* This page provides a list of logged in users, where the administrator may pick one to log out.
* @package eFront
* @version 1.0
*/

//General initialization and parameters
session_cache_limiter('none');
session_start();

$path = "../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

//error_reporting(E_ALL);
//echo "<pre>";print_r($_POST);print_r($_GET);

$message = '';$message_type = '';

if (eF_checkUser($_SESSION['s_login'], $_SESSION['s_password']) != "administrator") {                           //Only an administrator may access this page
    header("location:index.php");
    exit;
}
try {
    $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
    
    if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
        throw new Exception();
    }
} catch (Exception $e) {
    header("location:index.php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    exit;
}
$admins     = array(); $logged_in_admins     = array(); 
$professors = array(); $logged_in_professors = array();
$students   = array(); $logged_in_students   = array();
$result = eF_getTableData("users_online, users", "users_online.users_LOGIN, users.user_type", "users.login = users_online.users_LOGIN"); 
foreach ($result as $value) {
    if ($value['user_type'] == 'administrator') {
        array_push($admins, $value['users_LOGIN']);
    } else if ($value['user_type'] == 'professor') {
        array_push($professors, $value['users_LOGIN']);
    } else if ($value['user_type'] == 'student') {
        array_push($students, $value['users_LOGIN']);
    }
}

foreach ($admins as $value) {                                                                                   //Create the online users list
        $logged_in_admins[$value] = $value;
}
foreach ($professors as $value) {
        $logged_in_professors[$value] = $value;
}
foreach ($students as $value) {
        $logged_in_students[$value] = $value;
}
$form = new HTML_QuickForm("logout_user_form", "post", basename($_SERVER['PHP_SELF']), "", null, true);

$select = & HTML_QuickForm :: createElement('select', 'user_type', _USERTYPE, null, 'class = "inputSelect"');        
$select -> addOption('--- ' ._ADMINISTRATORS. ' ---', '');
$select -> loadArray($logged_in_admins);
$select -> addOption('--- ' ._PROFESSORS. ' ---', '');
$select -> loadArray($logged_in_professors);
$select -> addOption('--- ' ._STUDENTS. ' ---', '');
$select -> loadArray($logged_in_students);

$form -> addElement($select);
$form -> addRule('user_type', _THEFIELD.' '._USERTYPE.' '._ISMANDATORY, 'required');           

$form -> addElement('submit', 'submit_logout_user', _LOGOUTUSER, 'class = "flatButton"');

if ($form -> isSubmitted() && $form -> validate()) {    
	$user = EfrontUserFactory :: factory($form -> exportValue('user_type'));
	$user -> logout();
    $message      = _THEUSER.": ".$form -> exportValue('user_type')." "._HASBEENLOGGEDOUT;
    $message_type = 'success';
}

$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form -> accept($renderer);
$smarty -> assign('T_LOGOUT_USER_FORM', $renderer -> toArray());    

$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> display("logout_user.tpl");

?>