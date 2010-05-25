<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$loadScripts[] = 'administrator/system_config';
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] == 'hidden') {
    eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}

$load_editor = true;

$systemForm = new Html_QuickForm("system_variables", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=vars", "", null, true);
$systemForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');

$systemForm -> addElement("advcheckbox", "signup", _EXTERNALLYSIGNUP, null, 'class = "inputCheckBox"', array(0, 1));
$roles = EfrontUser :: getRoles(true);
$systemForm -> addElement('select', 'default_type', null, $roles, 'class = "inputCheckBox"');






 $systemForm -> addElement("advcheckbox", "activation", _AUTOMATICUSERACTIVATION, null, 'id = "activation" onclick = "if (this.checked) {$(\'mail_activation\').checked=false}" class = "inputCheckBox"', array(0, 1));
 $systemForm -> addElement("advcheckbox", "mail_activation", _MAILUSERACTIVATION, null, 'id = "mail_activation" onclick = "if (this.checked) {$(\'activation\').checked=false}" class = "inputCheckBox"', array(0, 1));

$systemForm -> addElement("advcheckbox", "onelanguage", _ONLYONELANGUAGE, null, 'class = "inputCheckBox"', array(0, 1));
$systemForm -> addElement("advcheckbox", "api", _ENABLEDAPI, null, 'class = "inputCheckBox"', array(0, 1));
$systemForm -> addElement("advcheckbox", "math_content", _ENABLEMATHCONTENT, null, 'class = "inputCheckBox"', array(0, 1));
$systemForm -> addElement("advcheckbox", "show_license_note", _ENABLELICENSENOTE, null, 'class = "inputCheckBox" onclick = "this.checked ? $(\'license_note\').show() : $(\'license_note\').hide();"', array(0, 1));
$systemForm -> addElement("advcheckbox", "reset_license_note", _RESETLICENSENOTE, null, 'class = "inputCheckBox"', array(0, 1));
$systemForm -> addElement("advcheckbox", "lesson_enroll", _ALLOWINDEPENDENTLESSONS, null, 'class = "inputCheckBox"', array(0, 1));
$systemForm -> addElement("advcheckbox", "eliminate_post_xss", _ELIMINATEPOSTXSS, null, 'class = "inputCheckBox"', array(0, 1));
$systemForm -> addElement("advcheckbox", "debug_mode", _DEBUGMODE, null, 'class = "inputCheckBox"', array(0, 1));
$systemForm -> addElement("advcheckbox", "password_reminder", _PASSWORDREMINDER, null, 'class = "inputCheckBox"', array(0, 1));
$systemForm -> addElement("advcheckbox", "math_images", _LOADMATHTYPESASIMAGES, null, 'class = "inputCheckBox"', array(0, 1));
//$systemForm -> addElement("advcheckbox", "smarty_caching", 	_SMARTYCACHING,       	 null, 'class = "inputCheckBox"', array(0, 1));
//$systemForm -> addElement("advcheckbox", "smarty_caching_timeout", _SMARTYCACHETIMEOUT, null, 'class = "inputCheckBox"', array(0, 1));
$systemForm -> addElement("text", "math_server", _MATHSERVER, 'class = "inputText"');
//$systemForm -> addElement("text", "license_server",	     _LICENSESERVER,		'class = "inputText"');
$systemForm -> addElement("text", "system_email", _SYSTEMEMAIL, 'class = "inputText"');
$systemForm -> addElement("text", "file_white_list", _ALLOWEDEXTENSIONS, 'class = "inputText"');
$systemForm -> addElement("text", "file_black_list", _DISALLOWEDEXTENSIONS, 'class = "inputText"');
$systemForm -> addElement("text", "logout_redirect", _LOGOUTREDIRECT, 'class = "inputText"');
$systemForm -> addElement("text", "password_length", _MINIMUMPASSWORDLENGTH,'size = "5"');
$systemForm -> addElement("text", "autologout_time", _LOGOUTUSERAFTER, 'size = "5"');
$systemForm -> addElement("text", "phplivedocx_server", _PHPLIVEDOCXSERVER, 'class = "inputText"');
$systemForm -> addElement("text", "phplivedocx_username",_USERNAME);
$systemForm -> addElement("text", "phplivedocx_password",_PASSWORD);
$systemForm -> addElement("select", "default_language", _DEFAULTLANGUAGE, EfrontSystem :: getLanguages(true, true), 'class = "inputSelect"');
$systemForm -> addElement("select", "insert_group_key", _VIEWINSERTGROUPKEY, array(_NO, _YES), 'class = "inputSelect"');
//If we are on a windows system, and the zip_method is already PHP, then don't display option to change it
if (stripos(php_uname(), 'windows') === false || $GLOBALS['configuration']['zip_method'] != "php") {
    $systemForm -> addElement("select", "zip_method", _ZIPHANDLING, array('php' => "PHP", 'system' => _SYSTEM), 'class = "inputSelect"');
} else {
    $systemForm -> addElement("select", "zip_method", _ZIPHANDLING, array('php' => "PHP"), 'class = "inputSelect"');
}
$systemForm -> addElement("textarea", "license_note", _LICENSENOTE, 'class = "inputText simpleEditor" style = "height:100px;width:500px;"');
$systemForm -> addRule('autologout_time', _INVALIDFIELDDATA, 'checkParameter', 'id');
$systemForm -> addRule('password_length', _INVALIDFIELDDATA, 'checkParameter', 'id');
$systemForm -> addRule('system_email', _THEFIELD.' "'._SYSTEMEMAIL.'" '._ISMANDATORY, 'required', null, 'client');
$systemForm -> addRule('system_email', _INVALIDFIELDDATAFORFIELD.': "'._SYSTEMEMAIL.'"' , 'email', null, 'client');
if (is_dir($path."versions/sso/")) {
    $filesystem = new FileSystemTree($path."versions/sso/");
    foreach (new EfrontFileTypeFilterIterator(new ArrayIterator($filesystem -> tree), array('php')) as $key => $value) {
        preg_match("/(\w+)\.class\.php/", $value['physical_name'], $matches);
        $ssos[$matches[1]] = $matches[1];
    }
    if (sizeof($ssos) > 0) {
        $systemForm -> addElement("select", "use_sso", _USESSO, array_merge(array(0 => _NONE), $ssos), 'class = "inputSelect"');
    }
}
$defaultEncodings = array_combine(mb_list_encodings(), mb_list_encodings());
$encodings['UTF7-IMAP'] = 'UTF7-IMAP';
if (in_array(_CHARSET, mb_list_encodings())) {
    $encodings[_CHARSET] = _CHARSET;
}
$encodings['UTF8'] = 'UTF8';
$encodings = array_merge($encodings, $defaultEncodings);
$systemForm -> addElement("select", "file_encoding", _TRANSLATEFILESYSTEM, $encodings, 'class = "inputSelect"');
$systemForm -> addElement("select", "mapped_accounts", _MAPPEDACCOUNTS, array(_ENABLED, _DISABLEDFORSTUDENTS, _DISABLEDFORPROFESSORSANDSTUDENTS, _DISABLEDFORALL), 'class = "inputSelect"');
unset($configuration['submit_system_variables']);
$systemForm -> setDefaults($configuration);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
    $systemForm -> freeze();
} else {
    $systemForm -> addElement("submit", "submit_system_variables", _SAVE, 'class = "flatButton"');
    if ($systemForm -> isSubmitted() && $systemForm -> validate()) { //If the form is submitted and validated
        $values = $systemForm -> exportValues();
        //Reset viewed license status
        if ($values['reset_license_note']) {
            eF_updateTableData("users", array("viewed_license" => 0), "viewed_license = 1");
        }
        $phplivedocxConfig = '<?php
define("PATH_ZF","'.G_ROOTPATH.'Zend/library/'.'");
define("USERNAME","'.$values['phplivedocx_username'].'");
define("PASSWORD","'.$values['phplivedocx_password'].'");
define("PHPLIVEDOCXAPI","'.$values['phplivedocx_server'].'");
?>';
  if (!file_exists($path."phplivedocx_config.php") || is_writable($path."phplivedocx_config.php")) {
   file_put_contents($path."phplivedocx_config.php", $phplivedocxConfig);
  } else {
   $failed_updates[] = _PHPLIVEDOCXCONFIGURATIONFILEISNOTWRITABLE;
  }
/*        

        $file = file_get_contents($path."phplivedocx_config.php");

        $file = preg_replace("/(define\(\"USERNAME\",\").*(\"\);)/", "\${1}".$values['phplivedocx_username'].'$2', $file);

        $file = preg_replace("/(define\(\"PASSWORD\",\").*(\"\);)/", "\${1}".$values['phplivedocx_password'].'$2', $file);

        $file = preg_replace("/(define\(\"PHPLIVEDOCXAPI\",\").*(\"\);)/", "\${1}".$values['phplivedocx_server'].'$2', $file);

        file_put_contents($path."phplivedocx_config.php", $file);

*/
        unset($values['reset_license_note']); //Unset it, since we don't need to store this value to the database
        unset($values['submit_system_variables']);
        foreach ($values as $key => $value) {
            $result = EfrontConfiguration :: setValue($key, $value);
            if (!$result) {
                $failed_updates[] = _COULDNOTUPDATE." $key "._WITHVALUE." ".$value;
            }
        }
        if (!isset($failed_updates)) {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=vars&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
        } else {
            $message = implode(", ", $failed_updates);
            $message_type = 'failure';
        }
    }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
$renderer->setErrorTemplate(
        '{$html}{if $error}
             <div class = "formError">{$error}</div>
         {/if}'
         );
$systemForm -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
$systemForm -> setRequiredNote(_REQUIREDNOTE);
$systemForm -> accept($renderer);
$smarty -> assign('T_SYSTEM_VARIABLES_FORM', $renderer -> toArray());
$smarty -> assign("T_MAX_FILE_SIZE", FileSystemTree :: getUploadMaxSize());
/*Appearance part*/
$logoForm = new HTML_QuickForm("upload_logo_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=system_config&tab=appearance', "", null, true);
$logoForm -> addElement('file', 'logo', null);
//Don't show normalization if GD isn't set.
if (extension_loaded('gd') || extension_loaded('gd2')) {
    $smarty -> assign("T_GD_LOADED", true);
    $logoForm -> addElement("text", "logo_max_width", _LOGOWIDTH, 'size = "5"');
    $logoForm -> addElement("text", "logo_max_height", _LOGOHEIGHT, 'size = "5"');
    $logoForm -> addRule('logo_max_width', _LOGODIMENSIONSMUSTBEPOSITIVE , 'callback', create_function('$a', 'return ($a > 0);'));
    $logoForm -> addRule('logo_max_height', _LOGODIMENSIONSMUSTBEPOSITIVE , 'callback', create_function('$a', 'return ($a > 0);'));
    $logoForm -> addElement("advcheckbox", "normalize_dimensions", _NORMALIZEDIMENSIONS, null, 'class = "inputCheckBox"', array(0, 1));
    $logoForm -> setDefaults(array('normalize_dimensions' => 1));
}
$logoForm -> addElement("advcheckbox", "default_logo", _USEDEFAULTLOGO, null, 'class = "inputCheckBox" id = "set_default_logo" onclick = "$(\'logo_settings\').select(\'input\').each(function(s) {if (s.type != \'submit\' && s.id != \'set_default_logo\') s.disabled ? s.disabled = \'\' : s.disabled = \'disabled\' })"', array(0, 1));
$logoForm -> addElement('submit', 'submit_upload_logo', _SUBMIT, 'class = "flatButton"');
$smarty -> assign("T_MAX_UPLOAD_SIZE", FileSystemTree :: getUploadMaxSize());
try {
    // Get current dimensions
    list($width, $height) = getimagesize($GLOBALS['logoFile']['path']);
    $logoForm -> setDefaults(array('logo_max_width' => $width,
                                        'logo_max_height' => $height));
} catch (EfrontFileException $e) {
    $logoForm -> setDefaults(array('logo_max_width' => 200,
                                        'logo_max_height' => 150));
}
if ($logoForm -> isSubmitted() && $logoForm -> validate()) {
    try {
        if ($logoForm -> exportValue('default_logo')) {
            EfrontConfiguration :: setValue('logo', '');
        } else {
            $logoDirectory = new EfrontDirectory(G_LOGOPATH);
            $filesystem = new FileSystemTree(G_LOGOPATH);
            try {
                $logoFile = $filesystem -> uploadFile('logo', $logoDirectory);
                if (strpos($logoFile['mime_type'], 'image') === false) {
                    throw new EfrontFileException(_NOTANIMAGEFILE, EfrontFileException::NOT_APPROPRIATE_TYPE);
                }
                EfrontConfiguration :: setValue('logo', $logoFile['id']);
            } catch (EfrontFileException $e) {
                //Don't halt if no file was uploaded (errcode = 4). Otherwise, throw the exception
                if ($e -> getCode() != 4) {
                    throw $e;
                }
            }
            // Normalize avatar picture to the dimensions set in the System Configuration menu
            // NOTE: the picture will be modified to match existing settings. Future higher settings will be disregarded, while
            // lower ones might affect the quality of the displayed image
            if ($logoForm -> exportValue("normalize_dimensions") == 1) {
                eF_normalizeImage(G_LOGOPATH . $logoFile['name'], $logoFile['extension'], $logoForm->exportValue("logo_max_width"), $logoForm->exportValue("logo_max_height"));
            } else {
                list($width, $height) = getimagesize(G_LOGOPATH . $logoFile['name']);
                eF_createImage(G_LOGOPATH . $logoFile['name'], $logoFile['extension'], $width, $height, $logoForm->exportValue("logo_max_width"), $logoForm->exportValue("logo_max_height"));
            }
        }
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=appearance&message=".rawurlencode(_OPERATIONCOMPLETEDSUCCESSFULLY)."&message_type=success");
    } catch (EfrontFileException $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$logoForm -> accept($renderer);
$smarty -> assign('T_UPLOAD_LOGO_FORM', $renderer -> toArray());
$faviconForm = new HTML_QuickForm("upload_favicon_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=system_config&tab=appearance', "", null, true);
$faviconForm -> addElement('file', 'favicon', null);
$faviconForm -> addElement("advcheckbox", "default_favicon", _USEDEFAULTLOGO, null, 'class = "inputCheckBox"  id = "set_default_favicon" onclick = "$(\'favicon_settings\').select(\'input\').each(function(s) {if (s.type != \'submit\' && s.id != \'set_default_favicon\') s.disabled ? s.disabled = \'\' : s.disabled = \'disabled\' })"', array(0, 1));
$faviconForm -> addElement('submit', 'submit_upload_favicon', _SUBMIT, 'class = "flatButton"');
if ($faviconForm -> isSubmitted() && $faviconForm -> validate()) {
    try {
        if ($faviconForm -> exportValue('default_favicon')) {
            EfrontConfiguration :: setValue('favicon', '');
        } else {
            $faviconDirectory = new EfrontDirectory(G_LOGOPATH);
            $filesystem = new FileSystemTree(G_LOGOPATH);
            try {
                $faviconFile = $filesystem -> uploadFile('favicon', $logoDirectory);
                if (strpos($faviconFile['mime_type'], 'image') === false) {
                    throw new EfrontFileException(_NOTANIMAGEFILE, EfrontFileException::NOT_APPROPRIATE_TYPE);
                }
                EfrontConfiguration :: setValue('favicon', $faviconFile['id']);
            } catch (Exception $e) {
                if ($e -> getCode() != 4) {
                    throw $e;
                }
            }
        }
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=appearance&message=".rawurlencode(_OPERATIONCOMPLETEDSUCCESSFULLY)."&message_type=success");
    } catch (EfrontFileException $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$faviconForm -> accept($renderer);
$smarty -> assign('T_UPLOAD_FAVICON_FORM', $renderer -> toArray());
$customizationForm = new HTML_QuickForm("customization_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=appearance", "", null, true);
$customizationForm -> addElement("advcheckbox", "show_footer", _SHOWFOOTER, null, 'class = "inputCheckBox"', array(0, 1));
$customizationForm -> addElement("textarea", "additional_footer", _EDITFOOTER, 'style = "height:100px;width:500px;"');
$customizationForm -> addElement("text", "site_name", _SITENAME, 'class = "inputText"');
$customizationForm -> addElement("text", "site_motto", _SITEMOTO, 'class = "inputText"');
$customizationForm -> addElement("advcheckbox", "motto_on_header", _SHOWMOTTOONHEADER, null, 'class = "inputCheckBox"', array(0,1));
$customizationForm -> addElement("text", "username_format", _USERNAMEFORMAT, 'class = "inputText"');
$customizationForm -> addElement("select", "collapse_catalog", _COLLAPSECATALOG, array(_NO, _YES, _ONLYFORLESSONS), 'class = "inputCheckBox"');
$customizationForm -> addElement("advcheckbox", "display_empty_blocks", _SHOWEMPTYBLOCKS, null, 'class = "inputCheckBox"', array(0,1));
$customizationForm -> addElement("select", "lessons_directory", _VIEWDIRECTORY, array(_NO, _YES, _YESAFTERLOGIN), 'class = "inputSelect"');
$customizationForm -> addElement("select", "login_redirect_page", _LOGINREDIRECTPAGE, array('lesson_catalog' => _LESSONSCATALOG, 'user_dashboard' => _USERDASHBOARD), 'class = "inputCheckBox"');
$customizationForm -> addElement("select", "editor_type", _EDITORTYPE, array('tinymce' => _TINYMCE, 'tinymce_new' => _NEWTINYMCE), 'class = "inputCheckBox"');
$customizationForm -> addElement("submit", "submit_system_variables", _SAVE, 'class = "flatButton"');
$customizationForm -> setDefaults($GLOBALS['configuration']);
if ($customizationForm -> isSubmitted() && $customizationForm -> validate()) {
    //If the form is submitted and validated
    $values = $customizationForm -> exportValues();
    //Reset viewed license status
    foreach ($values as $key => $value) {
        $result = EfrontConfiguration :: setValue($key, $value);
  //delete cache when changing editor type
  $cacheTree = new FileSystemTree(G_THEMECACHE, true);
        foreach (new EfrontDirectoryOnlyFilterIterator($cacheTree -> tree) as $value) {
            $value -> delete();
        }
        if (!$result) {
            $failed_updates[] = _COULDNOTUPDATE." $key "._WITHVALUE." ".$value;
        }
    }
    if (!isset($failed_updates)) {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=appearance&message=".rawurlencode(_OPERATIONCOMPLETEDSUCCESSFULLY)."&message_type=success");
    } else {
        $message = implode(", ", $failed_updates);
        $message_type = 'failure';
    }
}
$customizationForm -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
$customizationForm -> setRequiredNote(_REQUIREDNOTE);
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$renderer -> setRequiredTemplate (
       '{$html}{if $required}
       &nbsp;<span class = "formRequired">*</span>
       {/if}');
$renderer->setErrorTemplate(
       '{$html}{if $error}
       <div class = "formError">{$error}</div>
       {/if}');
$customizationForm -> accept($renderer);
$smarty -> assign('T_CUSTOMIZATION_FORM', $renderer -> toArray());
/*LDAP part*/
$smtp_form = new Html_QuickForm("smtp_variables", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=smtp", "", null, true);
$smtp_form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$smtp_form -> addElement("text", "smtp_host", _SMTPSERVER, 'class = "inputText"');
$smtp_form -> addElement("text", "smtp_user", _SMTPUSER, 'class = "inputText"');
$smtp_form -> addElement("password", "smtp_pass", _SMTPPASSWORD, 'class = "inputText"');
$smtp_form -> addElement("text", "smtp_port", _SMTPPORT, 'class = "inputText"');
$smtp_form -> addRule('smtp_port', _INVALIDFIELDDATA, 'checkParameter', 'id');
$smtp_form -> addElement("text", "smtp_timeout", _SMTPTIMEOUT, 'class = "inputText"');
$smtp_form -> addRule('smtp_timeout', _INVALIDFIELDDATA, 'checkParameter', 'id');
//$smtp_form -> addElement("advcheckbox", "smtp_ssl",  _USESSL,   null, 'class = "inputCheckBox"', array(1, 0));
$smtp_form -> addElement("advcheckbox", "smtp_auth", _SMTPAUTH, null, 'class = "inputCheckBox"', array(0, 1));
$smtp_form -> setDefaults($configuration);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
    $smtp_form -> freeze();
} else {
    $smtp_form -> addElement("submit", "check_smtp", _CHECKSETTINGS, 'class = "flatButton"');
    $smtp_form -> addElement("submit", "submit_smtp_variables", _SAVE, 'class = "flatButton"');
    if ($smtp_form -> isSubmitted() && $smtp_form -> validate()) { //If the form is submitted and validated
        $values = $smtp_form -> exportValues();
        if (isset($values['check_smtp'])) {
            $user_mail = eF_getTableData("users", "email", "login='".$_SESSION['s_login']."'");
            $header = array ('From' => $GLOBALS['configuration']['system_email'],
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
            $result = $smtp -> send($user_mail[0]['email'], $header, 'This is a test email send to verify SMTP settings');
            if ($result === true) {
                $message = _EMAILSENDTOYOURADDRESS;
                $message_type = 'success';
            } else {
                $message = _EMAILCOULDNOTBESENDBECAUSE.': '.mb_substr($result -> getMessage(), 0, mb_strpos($result -> getMessage(), ':'));
                $message_type = 'failure';
            }
        } else {
            foreach ($values as $key => $value) {
                $result = EfrontConfiguration :: setValue($key, $value);
                if (!$result) {
                    $failed_updates[] = _COULDNOTUPDATE." $key "._WITHVALUE." ".$value;
                }
            }
            if (!isset($failed_updates)) {
                $message = _SUCCESFULLYUPDATECONFIGURATION;
                $message_type = 'success';
            } else {
                $message = implode(", ", $failed_updates);
                $message_type = 'failure';
            }
        }
    }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
$smtp_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
$smtp_form -> setRequiredNote(_REQUIREDNOTE);
$smtp_form -> accept($renderer);
$smarty -> assign('T_SMTP_VARIABLES_FORM', $renderer -> toArray());
$locale_form = new Html_QuickForm("locale_variables", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=locale", "", null, true);
// Hard-coded cities per time zone - hopefully all are DST aware
$timezones = eF_getTimezones();
$locale_form -> addElement("select", "time_zone", _TIMEZONE, $timezones, 'class = "inputText" style="width:40em"');
$locale_form -> addElement("select", "currency", _CURRENCY, $CURRENCYNAMES);
$locale_form -> addElement("select", "currency_order", _SHOWCURRENCYSYMBOL, array(1 => _BEFOREPRICE, 0 => _AFTERPRICE));
$locale_form -> addElement("text", "decimal_point", _DECIMALPOINT, 'class = "inputText" style = "width:50px"');
$locale_form -> addElement("text", "thousands_sep", _THOUSANDSSEPARATOR, 'class = "inputText" style = "width:50px"');
$locale_form -> addElement("select", "date_format", _DATEFORMAT, array("DD/MM/YYYY" => "DD/MM/YYYY", "MM/DD/YYYY" => "MM/DD/YYYY", "YYYY/MM/DD" => "YYYY/MM/DD"));
$locale_form -> setDefaults($configuration);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
    $locale_form -> freeze();
} else {
    $locale_form -> addElement("submit", "submit_locale", _SUBMIT, 'class = "flatButton"');
    if ($locale_form -> isSubmitted() && $locale_form -> validate()) { //If the form is submitted and validated
        $values = $locale_form -> exportValues();
        unset($values["submit_locale"]);
        foreach ($values as $key => $value) {
            $result = EfrontConfiguration :: setValue($key, $value);
            if (!$result) {
                $failed_updates[] = _COULDNOTUPDATE." $key "._WITHVALUE." ".$value;
            }
        }
        if (!isset($failed_updates)) {
            $message = _SUCCESFULLYUPDATECONFIGURATION;
            $message_type = 'success';
        } else {
            $message = implode(", ", $failed_updates);
            $message_type = 'failure';
        }
    }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
$locale_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
$locale_form -> setRequiredNote(_REQUIREDNOTE);
$locale_form -> accept($renderer);
$smarty -> assign('T_LOCALE_VARIABLES_FORM', $renderer -> toArray());
$disable_form = new Html_QuickForm("disable_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=disable", "", null, true);
$disable_form -> addElement("advcheckbox", "disable_projects", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_bookmarks", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_comments", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_online_users", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_glossary", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_calendar", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_surveys", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_news", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_messages", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_forum", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_tests", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "chat_enabled", null, null, 'class = "inputCheckBox"', array(1, 0));
$disable_form -> addElement("advcheckbox", "disable_tooltip", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_help", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> addElement("advcheckbox", "disable_feedback", null, null, 'class = "inputCheckBox"', array(0, 1));
$disable_form -> setDefaults($configuration);
isset($configuration['disable_projects']) ? $disable_form -> setDefaults(array('disable_projects' => $configuration['disable_projects'])) : $disable_form -> setDefaults(array('disable_projects' => 0));
isset($configuration['disable_bookmarks']) ? $disable_form -> setDefaults(array('disable_bookmarks' => $configuration['disable_bookmarks'])) : $disable_form -> setDefaults(array('disable_bookmarks' => 0));
isset($configuration['disable_comments']) ? $disable_form -> setDefaults(array('disable_comments' => $configuration['disable_comments'])) : $disable_form -> setDefaults(array('disable_comments' => 0));
isset($configuration['disable_online_users']) ? $disable_form -> setDefaults(array('disable_online_users' => $configuration['disable_online_users'])) : $disable_form -> setDefaults(array('disable_online_users' => 0));
isset($configuration['disable_glossary']) ? $disable_form -> setDefaults(array('disable_glossary' => $configuration['disable_glossary'])) : $disable_form -> setDefaults(array('disable_glossary' => 0));
isset($configuration['disable_calendar']) ? $disable_form -> setDefaults(array('disable_calendar' => $configuration['disable_calendar'])) : $disable_form -> setDefaults(array('disable_calendar' => 0));
isset($configuration['disable_surveys']) ? $disable_form -> setDefaults(array('disable_surveys' => $configuration['disable_surveys'])) : $disable_form -> setDefaults(array('disable_surveys' => 0));
isset($configuration['disable_news']) ? $disable_form -> setDefaults(array('disable_news' => $configuration['disable_news'])) : $disable_form -> setDefaults(array('disable_news' => 0));
isset($configuration['disable_messages']) ? $disable_form -> setDefaults(array('disable_messages' => $configuration['disable_messages'])) : $disable_form -> setDefaults(array('disable_messages' => 0));
isset($configuration['disable_forum']) ? $disable_form -> setDefaults(array('disable_forum' => $configuration['disable_forum'])) : $disable_form -> setDefaults(array('disable_forum' => 0));
isset($configuration['disable_tests']) ? $disable_form -> setDefaults(array('disable_tests' => $configuration['disable_tests'])) : $disable_form -> setDefaults(array('disable_tests' => 0));
isset($configuration['disable_chat']) ? $disable_form -> setDefaults(array('disable_chat' => $configuration['disable_chat'])) : $disable_form -> setDefaults(array('disable_chat' => 0));
isset($configuration['disable_tooltip']) ? $disable_form -> setDefaults(array('disable_tooltip' => $configuration['disable_tooltip'])) : $disable_form -> setDefaults(array('disable_tooltip' => 0));
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
    $disable_form -> freeze();
} else {
    $disable_form -> addElement("submit", "submit_disable", _SUBMIT, 'class = "flatButton"');
    if ($disable_form -> isSubmitted() && $disable_form -> validate()) { //If the form is submitted and validated
        $values = $disable_form -> exportValues();
        unset($values["submit_disable"]);
        foreach ($values as $key => $value) {
            if (!($result = EfrontConfiguration :: setValue($key, $value))) {
                $failed_updates[] = _COULDNOTUPDATE." $key "._WITHVALUE." ".$value;
            }
        }
        if (!isset($failed_updates)) {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=disable&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success&refresh_side=1");
        } else {
            $message = implode(", ", $failed_updates);
            $message_type = 'failure';
        }
    }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
$disable_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
$disable_form -> setRequiredNote(_REQUIREDNOTE);
$disable_form -> accept($renderer);
$smarty -> assign('T_DISABLE_VARIABLES_FORM', $renderer -> toArray());
/********* Social module ************/
// The social modules are using a bitmap variable - each bit corresponds to a social module functionality
/********* Social module ************/
// The enterprise modules are using a bitmap variable - each bit corresponds to a enterprise module functionality
$php_form = new Html_QuickForm("php_variables", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=php", "", null, true);
$php_form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
$php_form -> addElement("text", "memory_limit", null, 'class = "inputText" style = "width:60px"');
$php_form -> addRule('memory_limit', _INVALIDFIELDDATA, 'checkParameter', 'id');
$php_form -> addElement("text", "max_execution_time", null, 'class = "inputText" style = "width:60px"');
$php_form -> addRule('max_execution_time', _INVALIDFIELDDATA, 'checkParameter', 'id');
$php_form -> addElement("advcheckbox", "gz_handler", null, null, 'class = "inputCheckBox"', array(0, 1));
$php_form -> addElement("text", "max_file_size", _MAXFILESIZE, 'class = "inputText"');
$php_form -> addRule('max_file_size', _INVALIDFIELDDATA, 'checkParameter', 'id');
//$php_form -> addRule('max_file_size', _THEFIELD.' "'._MAXFILESIZE.'" '._ISMANDATORY, 'required', null, 'client');
$php_form -> addRule('max_file_size', _INVALIDFIELDDATAFORFIELD.': "'._MAXFILESIZE.'"', 'numeric', null, 'client');
//        $php_form -> addElement("advcheckbox", "display_errors", null, null, 'class = "inputCheckBox"', array(0, 1));
$php_form -> setDefaults($configuration);
isset($configuration['memory_limit']) ? $php_form -> setDefaults(array('memory_limit' => $configuration['memory_limit'])) : $php_form -> setDefaults(array('memory_limit' => (int)ini_get('memory_limit')));
isset($configuration['max_execution_time']) ? $php_form -> setDefaults(array('max_execution_time' => $configuration['max_execution_time'])) : $php_form -> setDefaults(array('max_execution_time' => ini_get('max_execution_time')));
isset($configuration['gz_handler']) ? $php_form -> setDefaults(array('gz_handler' => $configuration['gz_handler'])) : $php_form -> setDefaults(array('gz_handler' => ''));
//        isset($configuration['display_errors'])   ? $php_form -> setDefaults(array('display_errors'     => $configuration['display_errors']))     : $php_form -> setDefaults(array('display_errors'     => ini_get('display_errors')));
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
    $php_form -> freeze();
} else {
    $php_form -> addElement("submit", "submit_php", _SUBMIT, 'class = "flatButton"');
    if ($php_form -> isSubmitted() && $php_form -> validate()) { //If the form is submitted and validated
        $values = $php_form -> exportValues();
        unset($values["submit_php"]);
        foreach ($values as $key => $value) {
            if ($value == '') {
                if ($key == 'memory_limit' || $key == 'max_execution_time') {
                    ini_restore($key);
                    $result = EfrontConfiguration :: setValue($key, str_ireplace("M", "", ini_get($key)));
                } elseif ($key == 'max_file_size') {
                    $result = EfrontConfiguration :: setValue($key, FileSystemTree :: getUploadMaxSize());
                } else {
                    eF_deleteTableData("configuration", "name = '$key'");
                    unset($configuration[$key]);
                }
            } else {
                if ($key == 'memory_limit' || $key == 'max_execution_time') { //You can't set these values below the php.ini setting
                    ini_restore($key);
                    if ((int)ini_get($key) <= $value || $value == -1) {
                        $result = EfrontConfiguration :: setValue($key, $value);
                    } else {
                        $failed_updates[] = _COULDNOTUPDATE." $key "._WITHVALUE." ".$value.": "._VALUEISSMALLERTHATPHPINI;
                    }
                } else {
                    if (!($result = EfrontConfiguration :: setValue($key, $value))) {
                        $failed_updates[] = _COULDNOTUPDATE." $key "._WITHVALUE." ".$value;
                    }
                }
            }
        }
        if (!isset($failed_updates)) {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=php&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
        } else {
            $message = implode(", ", $failed_updates);
            $message_type = 'failure';
        }
    }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
$php_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
$php_form -> setRequiredNote(_REQUIREDNOTE);
$php_form -> accept($renderer);
$smarty -> assign('T_PHP_VARIABLES_FORM', $renderer -> toArray());
/*Multiple users part*/
$multiple_logins_form = new HTML_QuickForm("multiple_logins_variables", "post", basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=multiple_logins", "", null, true);
$groups = array();
foreach (EfrontGroup::getGroups() as $value) {
    $groups[$value['id']] = $value['name'];
}
$users = EfrontUser :: getUsers();
$userTypes = array();
$multiple_logins_form -> addElement("select", "global", null, array(0 => _NO, 1 => _YES));
$multiple_logins_form -> addElement("select", "user_types", null, EfrontUser :: getRoles(true), "multiple");
//$multiple_logins_form -> addElement("select", "users", null, $users, "multiple");
if (sizeof($groups) > 0) {
    $multiple_logins_form -> addElement("select", "groups", null, $groups, "multiple");
}
$multiple_logins_form -> setDefaults(unserialize($configuration['multiple_logins']));
//$php_form -> setDefaults($configuration);
if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
    $multiple_logins_form -> freeze();
} else {
    $multiple_logins_form -> addElement("submit", "submit_multiple_logins", _SUBMIT, 'class = "flatButton"');
    if ($multiple_logins_form -> isSubmitted() && $multiple_logins_form -> validate()) {
        $values = $multiple_logins_form -> exportValues();
        $multipleLogins = array('global' => $values['global'] ? 1 : 0,
                                        'user_types' => $values['user_types'],
        //'users'      => $values['users'],
                                        'groups' => $values['groups']);
        if (EfrontConfiguration :: setValue('multiple_logins', serialize($multipleLogins))) {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=system_config&tab=multiple_logins&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
        } else {
            $message = _COULDNOTSETCONFIGURATIONVALUE.': '.serialize($multipleLogins);
            $message_type = 'failure';
        }
    }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
$multiple_logins_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
$multiple_logins_form -> setRequiredNote(_REQUIREDNOTE);
$multiple_logins_form -> accept($renderer);
$smarty -> assign('T_MULTIPLE_LOGINS_FORM', $renderer -> toArray());
?>
