<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

$customizationDisableForm = new HTML_QuickForm("customization_disable_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=customization&tab=disable", "", null, true);
$customizationDisableForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$customizationDisableForm -> addElement("advcheckbox", "disable_projects", _PROJECTS, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_bookmarks", _BOOKMARKS, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_comments", _COMMENTS, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_online_users", _ONLINEUSERS, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_glossary", _GLOSSARY, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_calendar", _CALENDAR, null, 'class = "inputCheckBox"', array(1, 0));



$customizationDisableForm -> addElement("advcheckbox", "disable_news", _ANNOUNCEMENTS, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_messages", _MESSAGES, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_messages_student", _MESSAGESSTUDENTS, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_forum", _FORUMS, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_tests", _TESTS, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "chat_enabled", _CHAT, null, 'class = "inputCheckBox"', array(0, 1));
$customizationDisableForm -> addElement("advcheckbox", "disable_tooltip", _TOOLTIP, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_help", _HELP, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_feedback", _FEEDBACK, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_payments", _PAYMENTS, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("static", "", _WARNINGDISABLINGPAYMENTSWILLSETALLPRICESTOZERO);
$customizationDisableForm -> addElement("advcheckbox", "disable_move_blocks", _MOVEBLOCK, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_change_info", _USERSCANCHANGEINFO, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> addElement("advcheckbox", "disable_change_pass", _USERSCANCHANGEPASS, null, 'class = "inputCheckBox"', array(1, 0));
$customizationDisableForm -> setDefaults($GLOBALS['configuration']);

if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $customizationDisableForm -> freeze();
} else {
 $customizationDisableForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');

 if ($customizationDisableForm -> isSubmitted() && $customizationDisableForm -> validate()) { //If the form is submitted and validated
  $values = $customizationDisableForm -> exportValues();
  unset($values['submit']);
  foreach ($values as $key => $value) {
   EfrontConfiguration :: setValue($key, $value);
  }
  if ($values['disable_payments'] == 1) {
   eF_updateTableData("lessons", array('price' => 0), "id=id");
   eF_updateTableData("courses", array('price' => 0), "id=id");
  }
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=customization&tab=disable&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
 $smarty -> assign("T_CUSTOMIZATION_DISABLE_FORM", $customizationDisableForm -> toArray());
}
?>
