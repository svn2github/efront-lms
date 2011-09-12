<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

$userMainForm = new HTML_QuickForm("user_main_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=user&tab=main", "", null, true);
$userMainForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$userMainForm -> addElement("advcheckbox", "signup", _EXTERNALLYSIGNUP, null, 'class = "inputCheckBox"', array(0, 1));
$userMainForm -> addElement('select', 'default_type', _DEFAULTUSERTYPE, EfrontUser :: getRoles(true), 'class = "inputCheckBox"');
$userMainForm -> addElement("advcheckbox", "remember_login", _REMEMBERLOGIN, null, null, 'class = "inputCheckBox"', array(0, 1));





 $userMainForm -> addElement("advcheckbox", "activation", _AUTOMATICUSERACTIVATION, null, 'id = "activation" onclick = "if (this.checked) {$(\'mail_activation\').checked=false}" class = "inputCheckBox"', array(0, 1));
 $userMainForm -> addElement("advcheckbox", "mail_activation", _MAILUSERACTIVATION, null, 'id = "mail_activation" onclick = "if (this.checked) {$(\'activation\').checked=false}" class = "inputCheckBox"', array(0, 1));

$userMainForm -> addElement("advcheckbox", "show_license_note", _ENABLELICENSENOTE, null, 'class = "inputCheckBox" onclick = "this.checked ? $(\'license_note\').show() : $(\'license_note\').hide();"', array(0, 1));
$userMainForm -> addElement("advcheckbox", "reset_license_note", _RESETLICENSENOTE, null, 'class = "inputCheckBox"', array(0, 1));
$userMainForm -> addElement("static", "", _USETHISINCASEYOUWANTALLUSERSTORECOMPLYTOLICENSENOTE);
$userMainForm -> addElement("textarea", "license_note", _LICENSENOTE, 'class = "inputText simpleEditor" style = "height:100px;width:500px;"');
$userMainForm -> addElement("advcheckbox", "lesson_enroll", _ALLOWINDEPENDENTLESSONS, null, 'class = "inputCheckBox"', array(0, 1));
$userMainForm -> addElement("select", "insert_group_key", _VIEWINSERTGROUPKEY, array(_NO, _YES), 'class = "inputSelect"');
$userMainForm -> addElement("select", "mapped_accounts", _MAPPEDACCOUNTS, array(_ENABLED, _DISABLEDFORSTUDENTS, _DISABLEDFORPROFESSORSANDSTUDENTS, _DISABLEDFORALL), 'class = "inputSelect"');
$userMainForm -> addElement("text", "username_format", _USERNAMEFORMAT, 'class = "inputText"');
$userMainForm -> addElement("static", "", _USERNAMEFORMATINFO);
$userMainForm -> addElement("text", "pm_space", _MAXIMUMPMUSAGESPACE, 'size = "5"');
$userMainForm -> addElement("static", "",_MAXIMUMPMUSAGESPACEINFO);
$userMainForm -> addRule('pm_space', _INVALIDFIELDDATA, 'checkParameter', 'id');

$userMainForm -> setDefaults($GLOBALS['configuration']);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $userMainForm -> freeze();
} else {
 $userMainForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($userMainForm -> isSubmitted() && $userMainForm -> validate()) {
  $values = $userMainForm -> exportValues();
  if ($values['reset_license_note']) {
   eF_updateTableData("users", array("viewed_license" => 0), "viewed_license = 1");
  }
  if ($values['username_format']) {
   if (function_exists('apc_delete')) {
    apc_delete(G_DBNAME.':_usernames');
   }
  }
  unset($values['reset_license_note']); //Unset it, since we don't need to store this value to the database
  unset($values['submit']);
  foreach ($values as $key => $value) {
   EfrontConfiguration :: setValue($key, $value);
  }
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=user&tab=main&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
}
$smarty -> assign("T_USER_MAIN_FORM", $userMainForm -> toArray());

$userMultipleLoginsForm = new HTML_QuickForm("user_multiple_logins_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=user&tab=multiple_logins", "", null, true);
$userMultipleLoginsForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$groups = array();
foreach (EfrontGroup::getGroups() as $value) {
 $groups[$value['id']] = $value['name'];
}
$userMultipleLoginsForm -> addElement("select", "global", _ALLOWMULTIPLELOGINSGLOBALLY, array(0 => _NO, 1 => _YES));
$userMultipleLoginsForm -> addElement("select", "user_types", _EXCEPTFORTHEROLES, EfrontUser :: getRoles(true), "multiple");
if (sizeof($groups) > 0) {
 $userMultipleLoginsForm -> addElement("select", "groups", _EXCEPTFORTHEGROUPS, $groups, "multiple");
}
$userMultipleLoginsForm -> addElement("static", "", _HOLDDOWNCTRLFORMULTIPLESELECT);
$userMultipleLoginsForm -> setDefaults(unserialize($GLOBALS['configuration']['multiple_logins']));
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $userMultipleLoginsForm -> freeze();
} else {
 $userMultipleLoginsForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($userMultipleLoginsForm -> isSubmitted() && $userMultipleLoginsForm -> validate()) {
  $values = $userMultipleLoginsForm -> exportValues();
  $multipleLogins = array('global' => $values['global'] ? 1 : 0,
        'user_types' => $values['user_types'],
        'groups' => $values['groups']);
  EfrontConfiguration :: setValue('multiple_logins', serialize($multipleLogins));
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=user&tab=multiple_logins&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
}
$smarty -> assign("T_USER_MULTIPLE_LOGINS_FORM", $userMultipleLoginsForm -> toArray());


$userWebserverAuthenticationForm = new HTML_QuickForm("user_webserver_authentication_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=user&tab=webserver_authentication", "", null, true);
$userWebserverAuthenticationForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$userWebserverAuthenticationForm -> addElement("advcheckbox", "webserver_auth", _WEBSERVERAUTHENTICATION, null, 'class = "inputCheckBox"', array(0, 1));
$userWebserverAuthenticationForm -> addElement("advcheckbox", "webserver_registration", _WEBSERVERREGISTRATION, null, 'class = "inputCheckBox"', array(0, 1));
$userWebserverAuthenticationForm -> addElement("text", "error_page", _ERRORPAGEFORINVALIDLOGIN, 'class = "inputText"');
$userWebserverAuthenticationForm -> addElement("text", "unauthorized_page", _ERRORPAGEFORUNAUTHORIZED, 'class = "inputText"');
$userWebserverAuthenticationForm -> addElement("text", "username_variable", _VARIABLEFORUSERNAME, 'class = "inputText"');
$userWebserverAuthenticationForm -> addElement("text", "registration_file", _INCLUDEFILETHATHANDLESUSERCREATION, 'class = "inputText"');
eval('$usernameVar='.$GLOBALS['configuration']['username_variable'].';');
$userWebserverAuthenticationForm -> addRule('webserver_auth', str_replace(array("%x", "%y"), array($GLOBALS['configuration']['username_variable'], $_SESSION['s_login']), _VARIABLEMUSTCONTAINLOGIN) , 'callback', create_function('$checkbox', "if (\$GLOBALS['usernameVar'] == \$_SESSION['s_login']) {return true;}"));
$userWebserverAuthenticationForm -> setDefaults($GLOBALS['configuration']);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $userWebserverAuthenticationForm -> freeze();
} else {
 $userWebserverAuthenticationForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($userWebserverAuthenticationForm -> isSubmitted() && $userWebserverAuthenticationForm -> validate()) {
  $values = $userWebserverAuthenticationForm -> exportValues();
  unset($values['submit']);
  foreach ($values as $key => $value) {
   EfrontConfiguration :: setValue($key, $value);
  }
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=user&tab=webserver_authentication&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
}

$smarty -> assign("T_USER_WEBSERVER_AUTHENTICATION_FORM", $userWebserverAuthenticationForm -> toArray());
