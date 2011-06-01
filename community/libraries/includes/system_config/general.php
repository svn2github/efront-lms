<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

$generalSecurityForm = new HTML_QuickForm("general_security_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=general&tab=security", "", null, true);
$generalSecurityForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$generalSecurityForm -> addElement("text", "file_white_list", _ALLOWEDEXTENSIONS, 'class = "inputText"');
$generalSecurityForm -> addElement("static", "", _COMMASEPARATEDLISTASTERISKEXTENSIONEXAMPLE);
$generalSecurityForm -> addElement("text", "file_black_list", _DISALLOWEDEXTENSIONS, 'class = "inputText"');
$generalSecurityForm -> addElement("static", "", _COMMASEPARATEDLISTASTERISKEXTENSIONEXAMPLE.' '._DENIALTAKESPRECEDENCE);
$generalSecurityForm -> addElement("text", "password_length", _MINIMUMPASSWORDLENGTH,'size = "5"');
$generalSecurityForm -> addElement("advcheckbox", "force_change_password", _FORCECHANGEPASSWORD, null, 'class = "inputCheckBox"', array(0, 1));
$generalSecurityForm -> addElement("text", "autologout_time", _LOGOUTUSERAFTERMINUTES, 'size = "5"');
$generalSecurityForm -> addElement("text", "updater_period", _UPDATERPERIODSECONDS, 'size = "8"');
$generalSecurityForm -> addElement("static", "", _RECOMMENDEDVALUEMORETHAN2000LESSTHANAUTOLOGOUTTIME);
$generalSecurityForm -> addElement("advcheckbox", "eliminate_post_xss", _ELIMINATEPOSTXSS, null, 'class = "inputCheckBox"', array(0, 1));
$generalSecurityForm -> addElement("advcheckbox", "password_reminder", _PASSWORDREMINDER, null, 'class = "inputCheckBox"', array(0, 1));
$generalSecurityForm -> addElement("advcheckbox", "constrain_access", _CONTRAINACCESSTOCONTENT, null, 'class = "inputCheckBox"', array(0, 1));
//$generalSecurityForm -> addElement("text", "logout_redirect", _LOGOUTREDIRECT, 'class = "inputText"'); // Moved to appearance tab
$generalSecurityForm -> setDefaults($GLOBALS['configuration']);
//$generalSecurityForm -> addRule('autologout_time', _INVALIDFIELDDATA, 'checkParameter', 'uint');
$generalSecurityForm -> addRule('autologout_time', _THEFIELD.' '._LOGOUTUSERAFTER.' '._ISMANDATORY, 'required', null, 'client');
$generalSecurityForm -> addRule('password_length', _INVALIDFIELDDATA, 'checkParameter', 'uint');
$generalSecurityForm -> addRule('password_length', _THEFIELD.' '._MINIMUMPASSWORDLENGTH.' '._ISMANDATORY, 'required', null, 'client');
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $generalSecurityForm -> freeze();
} else {
 $generalSecurityForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($generalSecurityForm -> isSubmitted() && $generalSecurityForm -> validate()) {
  $values = $generalSecurityForm -> exportValues();
  unset($values['submit']);
  if ($values['constrain_access']) {
   $str = "Options -Indexes
<IfModule rewrite_module>
 RewriteEngine on
 RewriteBase /
 RewriteCond %{REQUEST_URI} ^(.*)\/content\/lessons\/.*$
 RewriteRule !^((.*.php)|(.*\/))$ %1/view_file.php?server=1
</IfModule>";
   if (!is_file(G_CONTENTPATH.".htaccess")) {
    file_put_contents(G_CONTENTPATH.".htaccess", $str);
   }
  } else {
   unlink(G_CONTENTPATH.".htaccess");
  }
  foreach ($values as $key => $value) {
   $result = EfrontConfiguration :: setValue($key, $value);
  }
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=general&tab=security&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
}
$smarty -> assign("T_GENERAL_SECURITY_FORM", $generalSecurityForm -> toArray());
$generalLocaleForm = new HTML_QuickForm("general_locale", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=general&tab=locale", "", null, true);
$generalLocaleForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$defaultEncodings = array_combine(mb_list_encodings(), mb_list_encodings());
$encodings['UTF7-IMAP'] = 'UTF7-IMAP';
/*
if (in_array(_CHARSET, $defaultEncodings)) {
	$encodings[_CHARSET] = _CHARSET;
}
*/
$encodings['UTF8'] = 'UTF8';
$encodings = array_merge($encodings, $defaultEncodings);
// Hard-coded cities per time zone - hopefully all are DST aware
$generalLocaleForm -> addElement("select", "default_language", _DEFAULTLANGUAGE, EfrontSystem :: getLanguages(true, true), 'class = "inputSelect"');
$generalLocaleForm -> addElement("advcheckbox", "onelanguage", _ONLYONELANGUAGE, null, 'class = "inputCheckBox"', array(0, 1));
$generalLocaleForm -> addElement("select", "date_format", _DATEFORMAT, array("DD/MM/YYYY" => "DD/MM/YYYY", "MM/DD/YYYY" => "MM/DD/YYYY", "YYYY/MM/DD" => "YYYY/MM/DD"));
$generalLocaleForm -> addElement("select", "time_zone", _TIMEZONE, eF_getTimezones(), 'class = "inputText" style="width:40em"');
$generalLocaleForm -> addElement("select", "currency", _CURRENCY, $CURRENCYNAMES);
$generalLocaleForm -> addElement("select", "currency_order", _SHOWCURRENCYSYMBOL, array(1 => _BEFOREPRICE, 0 => _AFTERPRICE));
$generalLocaleForm -> addElement("text", "decimal_point", _DECIMALPOINT, 'class = "inputText" style = "width:50px"');
$generalLocaleForm -> addElement("text", "thousands_sep", _THOUSANDSSEPARATOR, 'class = "inputText" style = "width:50px"');
$generalLocaleForm -> addElement("select", "file_encoding", _TRANSLATEFILESYSTEM, $encodings, 'class = "inputSelect"');
$generalLocaleForm -> setDefaults($GLOBALS['configuration']);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $generalLocaleForm -> freeze();
} else {
 $generalLocaleForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($generalLocaleForm -> isSubmitted() && $generalLocaleForm -> validate()) { //If the form is submitted and validated
  $values = $generalLocaleForm -> exportValues();
  unset($values['submit']);
  foreach ($values as $key => $value) {
   $result = EfrontConfiguration :: setValue($key, $value);
  }
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=general&tab=locale&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
}
$smarty -> assign("T_GENERAL_LOCALE_FORM", $generalLocaleForm -> toArray());
$generalSMTPForm = new HTML_QuickForm("general_smtp", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=general&tab=smtp", "", null, true);
$generalSMTPForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$generalSMTPForm -> addElement("text", "system_email", _SYSTEMEMAIL, 'class = "inputText"');
$generalSMTPForm -> addElement("text", "smtp_host", _SMTPSERVER, 'class = "inputText"');
$generalSMTPForm -> addElement("static", "", _IFUSESSLTHENPHPOPENSSL);
$generalSMTPForm -> addElement("text", "smtp_user", _SMTPUSER, 'class = "inputText"');
$generalSMTPForm -> addElement("password", "smtp_pass", _SMTPPASSWORD, 'class = "inputText"');
$generalSMTPForm -> addElement("text", "smtp_port", _SMTPPORT, 'class = "inputText"');
$generalSMTPForm -> addElement("text", "smtp_timeout", _SMTPTIMEOUT, 'class = "inputText"');
$generalSMTPForm -> addElement("advcheckbox", "smtp_auth", _SMTPAUTH, null, 'class = "inputCheckBox"', array(0, 1));
$generalSMTPForm -> setDefaults($GLOBALS['configuration']);
$generalSMTPForm -> addRule('system_email', _INVALIDFIELDDATA, 'checkParameter', 'email');
$generalSMTPForm -> addRule('system_email', _THEFIELD.' '._SYSTEMEMAIL.' '._ISMANDATORY, 'required', null, 'client');
$generalSMTPForm -> addRule('smtp_port', _INVALIDFIELDDATA, 'checkParameter', 'uint');
$generalSMTPForm -> addRule('smtp_timeout', _INVALIDFIELDDATA, 'checkParameter', 'uint');
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $generalSMTPForm -> freeze();
} else {
 $generalSMTPForm -> addElement("submit", "check_smtp", _CHECKSETTINGS, 'class = "flatButton"');
 $generalSMTPForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($generalSMTPForm -> isSubmitted() && $generalSMTPForm -> validate()) {
  $values = $generalSMTPForm -> exportValues();
  unset($values['submit']);
  if (!isset($values['check_smtp'])) {
   foreach ($values as $key => $value) {
    $result = EfrontConfiguration :: setValue($key, $value);
   }
   eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=general&tab=smtp&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
  } else {
   $user_mail = eF_getTableData("users", "email", "login='".$_SESSION['s_login']."'");
   $header = array ('From' => $values['system_email'],
         'To' => $user_mail[0]['email'],
         'Subject' => 'Test email',
         'Content-type' => 'text/plain;charset="UTF-8"', // if content-type is text/html, the message cannot be received by mail clients for Registration content
         'Content-Transfer-Encoding' => '7bit');
   $smtp = Mail::factory('smtp', array('auth' => $values['smtp_auth'] ? true : false,
             'host' => $values['smtp_host'],
             'password' => $values['smtp_pass'],
             'port' => $values['smtp_port'],
             'username' => $values['smtp_user'],
             'timeout' => $values['smtp_timeout']));
   $result = $smtp -> send($user_mail[0]['email'], $header, 'This is a test email sent from '.G_SERVERNAME.' to verify SMTP settings');
   if ($result === true) {
    $message = _EMAILSENDTOYOURADDRESS;
    $message_type = 'success';
   } else {
    $message = $result -> getMessage();
    $message_type = 'failure';
   }
  }
 }
}
$smarty -> assign("T_GENERAL_SMTP_FORM", $generalSMTPForm -> toArray());
$generalPHPForm = new Html_QuickForm("general_php_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=general&tab=php", "", null, true);
$generalPHPForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$generalPHPForm -> addElement("static", "sidenote", 'M');
$generalPHPForm -> addElement("text", "memory_limit", _MEMORYLIMIT, 'class = "inputText" style = "width:35px"');
$generalPHPForm -> addElement("static", "sidenote", _SECONDS);
$generalPHPForm -> addElement("text", "max_execution_time", _MAXEXECUTIONTIME, 'class = "inputText" style = "width:35px"');
$generalPHPForm -> addElement("static", "", _LEAVEBLANKTOUSEPHPINI);
$generalPHPForm -> addElement("advcheckbox", "gz_handler", _GZHANDLER, null, 'class = "inputCheckBox"', array(0, 1));
$generalPHPForm -> addElement("advcheckbox", "compress_tests", _COMPRESSTESTS, null, 'class = "inputCheckBox"', array(0, 1));
$generalPHPForm -> addElement("text", "max_file_size", _MAXFILESIZE, 'class = "inputText"');
$generalPHPForm -> addElement("static", "", _MAXFILEISAFFECTEDANDIS.' <b>'.FileSystemTree::getUploadMaxSize().'</b> '._KB);
$generalPHPForm -> addElement("text", "debug_mode", _DEBUGMODE, null, 'class = "inputText"');
$generalPHPForm -> addElement("static", "", _COMMASEPARATEDLISTOFUSERSOR1FORALL);
$generalPHPForm -> addRule('memory_limit', _INVALIDFIELDDATA, 'checkParameter', 'id');
$generalPHPForm -> addRule('max_execution_time', _INVALIDFIELDDATA, 'checkParameter', 'id');
$generalPHPForm -> addRule('max_file_size', _INVALIDFIELDDATA, 'checkParameter', 'id');
$generalPHPForm -> addRule('max_file_size', _INVALIDFIELDDATAFORFIELD.': "'._MAXFILESIZE.'"', 'numeric', null, 'client');
$generalPHPForm -> setDefaults($GLOBALS['configuration']);
isset($configuration['memory_limit']) ? $generalPHPForm -> setDefaults(array('memory_limit' => $configuration['memory_limit'])) : $generalPHPForm -> setDefaults(array('memory_limit' => (int)ini_get('memory_limit')));
isset($configuration['max_execution_time']) ? $generalPHPForm -> setDefaults(array('max_execution_time' => $configuration['max_execution_time'])) : $generalPHPForm -> setDefaults(array('max_execution_time' => ini_get('max_execution_time')));
isset($configuration['gz_handler']) ? $generalPHPForm -> setDefaults(array('gz_handler' => $configuration['gz_handler'])) : $generalPHPForm -> setDefaults(array('gz_handler' => ''));
//		isset($configuration['display_errors'])   ? $generalPHPForm -> setDefaults(array('display_errors'	 => $configuration['display_errors']))	 : $generalPHPForm -> setDefaults(array('display_errors'	 => ini_get('display_errors')));
if ($GLOBALS['configuration']['version_hosted']) {
 $generalPHPForm -> freeze(array('memory_limit', 'max_execution_time'));
}
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
 $generalPHPForm -> freeze();
} else {
 $generalPHPForm -> addElement("submit", "submit", _SAVE, 'class = "flatButton"');
 if ($generalPHPForm -> isSubmitted() && $generalPHPForm -> validate()) { //If the form is submitted and validated
  $values = $generalPHPForm -> exportValues();
  unset($values['submit']);
  if ($GLOBALS['configuration']['version_hosted']) {
   unset($values['memory_limit']);
   unset($values['max_execution_time']);
  }
  foreach ($values as $key => $value) {
   if ($value == '') {
    if ($key == 'memory_limit' || $key == 'max_execution_time') {
     ini_restore($key);
     EfrontConfiguration :: setValue($key, str_ireplace("M", "", ini_get($key)));
    } elseif ($key == 'max_file_size') {
     EfrontConfiguration :: setValue($key, FileSystemTree :: getUploadMaxSize());
    } else {
     EfrontConfiguration :: deleteValue($key);
    }
   } else {
    if ($key == 'memory_limit' || $key == 'max_execution_time') { //You can't set these values below the php.ini setting
     ini_restore($key);
     EfrontConfiguration :: setValue($key, $value);
    } else {
     EfrontConfiguration :: setValue($key, $value);
    }
   }
  }
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=system_config&op=general&tab=php&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
 }
}
$smarty -> assign("T_GENERAL_PHP_FORM", $generalPHPForm -> toArray());
