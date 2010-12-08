<?php
/**

* Logs out a user.

*

* This page provides a list of logged in users, where the administrator may pick one to log out.

*/
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
$admins = array(); $logged_in_admins = array();
$professors = array(); $logged_in_professors = array();
$students = array(); $logged_in_students = array();
//$result = eF_getTableData("users_online, users", "users_online.users_LOGIN, users.user_type", "users.login = users_online.users_LOGIN");
// pr($result);
$onlineUsers = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);
foreach ($onlineUsers as $value) {
    if ($value['user_type'] == 'administrator' && $value['login'] != $currentUser -> user['login']) {
        array_push($admins, $value['login']);
    } else if ($value['user_type'] == 'professor') {
        array_push($professors, $value['login']);
    } else if ($value['user_type'] == 'student') {
        array_push($students, $value['login']);
    }
}

foreach ($admins as $value) { //Create the online users list
        $logged_in_admins[$value] = $value;
}
foreach ($professors as $value) {
        $logged_in_professors[$value] = $value;
}
foreach ($students as $value) {
        $logged_in_students[$value] = $value;
}
$form = new HTML_QuickForm("logout_user_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=logout_user&popup=1", "", null, true);

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
    $message = _THEUSER.": ".$form -> exportValue('user_type')." "._HASBEENLOGGEDOUT;
    $message_type = 'success';
}

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form -> accept($renderer);
$smarty -> assign('T_LOGOUT_USER_FORM', $renderer -> toArray());
