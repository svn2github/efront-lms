<?php
/**

 * Platform index page

 *

 * This is the index page, allowing for logging in, registering new users,

 * contacting and resetting password

 *

 * @package eFront

 * @version 3.6.0

 */
session_cache_limiter('none');
session_start(); //This causes the double-login problem, where the user needs to login twice when already logged in with the same browser
$path = "../libraries/";
//Automatically redirect to installation page if configuration file is missing
if (!is_file($path."configuration.php")) { //If the configuration file does not exist, this is a fresh installation, so redirect to installation page
 is_file("install/index.php") ? header("location:install/index.php") : print('Failed locating configuration file <br/> Failed locating installation directory <br/> Please execute installation script manually <br/>');
 exit;
} else {
 /** Configuration file */
 require_once $path."configuration.php";
}
//@todo:temporary here, should leave 
$cacheId = null;
$message = $message_type = '';
$benchmark = new EfrontBenchmark($debug_TimeStart);
$benchmark -> set('init');
//Set headers in order to eliminate browser cache (especially IE's)
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
//Delete installation directory after install/upgrade
if (is_dir("install") && isset($_GET['delete_install'])) {
 try {
  $dir = new EfrontDirectory('install');
  $dir -> delete();
 } catch (Exception $e) {
  echo "The installation directory could not be deleted. Please delete it manually or your system security is at risk.";
 }
}

//$smarty -> caching 	      = $GLOBALS['configuration']['smarty_cache'];					
//$smarty -> cache_lifetime = $GLOBALS['configuration']['smarty_cache_timeout'];	
//$cacheId = md5(serialize($_GET));
if (!$smarty -> is_cached('index.tpl', $cacheId) || !$GLOBALS['configuration']['smarty_caching']) {
 //Get available languages
 $languages = array();
    foreach (EfrontSystem :: getLanguages() as $key => $value) {
        if ($value['active']) {
            $languages[$key] = $value['translation'];
        }
    }
 ksort($languages);
    $smarty -> assign("T_LANGUAGES", $languages);
    $debug_InitTime = microtime(true) - $debug_TimeStart;
    if ($configuration['cms_page'] != "" && sizeof($_GET) == 0 && file_exists(G_CURRENTTHEMEPATH."external/".$GLOBALS['configuration']['cms_page'].".php")){ //if there is cms page and no get parameter defined
        eF_redirect("".G_SERVERNAME.G_CURRENTTHEMEURL."external/".$configuration['cms_page'].".php");
    }
    if (isset($_GET['logout']) && !isset($_POST['submit_login'])) { //If user wants to log out        
        if (isset($_SESSION['s_login']) && $_SESSION['s_login']) {
            try {
                $user = EfrontUserFactory :: factory($_SESSION['s_login']);
                $user -> logout();
                if ($GLOBALS['configuration']['logout_redirect']) {
                    strpos($GLOBALS['configuration']['logout_redirect'], 'http://') === 0 ? eF_redirect("".$GLOBALS['configuration']['logout_redirect']) : header("location:http://".$GLOBALS['configuration']['logout_redirect']);
                }
            } catch (EfrontUserException $e) {
                unset($_SESSION);
                session_destroy();
                $message = $e -> getMessage();
            }
        }
        if (isset($_GET['reason']) && $_GET['reason']=='timeout') {
            $message = _YOUHAVELOGGEDOUTBECAUSEYOURSESSIONHASTIMEDOUT;
            $message_type = 'failure';
        }
    }
 //Show information in the selected language
 if (isset($_GET['bypass_language']) && in_array($_GET['bypass_language'], array_keys($languages))) {
  $_SESSION['s_language'] = $_GET['bypass_language'];
 }
 //Keep persisted language across page calls
 if (isset($_SESSION['s_language'])) {
  $smarty -> assign("T_LANGUAGE", $_SESSION['s_language']);
 } else {
  $smarty -> assign("T_LANGUAGE", $GLOBALS['configuration']['default_language']);
 }
}
/*

 * Check if you should input the JS code to

 * trigger sending the next notificatoin emails

 * Since 3.6.0

 */
if (EfrontNotification::shouldSendNextNotifications()) {
 $smarty -> assign("T_TRIGGER_NEXT_NOTIFICATIONS_SEND", 1);
 $_SESSION['send_next_notifications_now'] = 0; // the msg that triggered the immediate send should be sent now
}
//if there is cms page and no get parameter defined, redirect to the cms page
if ($configuration['cms_page'] != "" && sizeof($_GET) == 0 && file_exists(G_CURRENTTHEMEPATH."external/".$GLOBALS['configuration']['cms_page'].".php")) { //check also if file exists to prevent from broken link
 //eF_redirect("".G_RELATIVEADMINLINK.$GLOBALS['configuration']['cms_page'].".php");
 eF_redirect("".G_SERVERNAME.G_CURRENTTHEMEURL."external/".$configuration['cms_page'].".php");
}
//The user logged out
if (isset($_GET['logout']) && !isset($_POST['submit_login'])) {
 //session_start();			//Isn't needed here if the head session_start() is in place
 if (isset($_SESSION['s_login']) && $_SESSION['s_login']) {
  try {
   $user = EfrontUserFactory :: factory($_SESSION['s_login']);
   $user -> logout();
   //Redirect user to another page, if such a configuration setting exists
   if ($GLOBALS['configuration']['logout_redirect']) {
    if ($GLOBALS['configuration']['logout_redirect'] == 'close') {
     echo "<script>window.close();</script>";
    } else {
     strpos($GLOBALS['configuration']['logout_redirect'], 'http://') === 0 ? eF_redirect("".$GLOBALS['configuration']['logout_redirect']) : header("location:http://".$GLOBALS['configuration']['logout_redirect']);
    }
   }
  } catch (EfrontUserException $e) {
   $message = $e -> getMessage();
   $message_type = 'failure';
  }
 }
}
if (!$smarty -> is_cached('index.tpl', $cacheId) || !$GLOBALS['configuration']['smarty_caching']) {
 $blocks = array('login' => array('title' => _LOGINENTRANCE, 'image' => '32x32/keys.png'),
        'online' => array('title' => _USERSONLINE, 'image' => '32x32/users.png'),
        'lessons' => array('title' => _LESSONS, 'image' => '32x32/theory.png'),
                 'selectedLessons' => array('title' => _SELECTEDLESSONS, 'image' => '32x32/shopping_basket.png'),
        'news' => array('title' => _SYSTEMNEWS, 'image' => '32x32/announcements.png'),
                 'links' => array('title' => _LINKS, 'image' => '32x32/generic.png'));
 //$customBlocks = unserialize($GLOBALS['configuration']['custom_blocks']);
 if (isset($currentTheme -> layout['custom_blocks']) && is_array($currentTheme -> layout['custom_blocks'])) {
     $customBlocks = $currentTheme -> layout['custom_blocks'];
 } else {
     $customBlocks = array();
 }
 foreach ($customBlocks as $key => $block) {
  $blocks[$key] = array('title' => $block['title'], 'image' => '32x32/generic.png');
 }
 if ($GLOBALS['configuration']['disable_online_users'] == 1) {
  unset($blocks['online']);
 }
 $smarty -> assign("T_CUSTOM_BLOCKS", $customBlocks);
 $smarty -> assign("T_BLOCKS", $blocks);
 $smarty -> assign("T_POSITIONS", $GLOBALS['currentTheme'] -> layout['positions']);
//pr($blocks);
    $directionsTree = new EfrontDirectionsTree();
 $options = array('lessons_link' => basename($_SERVER['PHP_SELF']).'?ctg=lesson_info&lessons_ID=',
                            'courses_link' => basename($_SERVER['PHP_SELF']).'?ctg=lesson_info&courses_ID=',
                            'search' => true,
                         'catalog' => true,
                            'url' => $_SERVER['PHP_SELF'],
       'collapse' => $GLOBALS['configuration']['collapse_catalog'],
       'buy_link' => true);
 include("directions_tree.php");
}
/* -------------------------------------------------------Login part-------------------------------------------------------------------*/
if (isset($_COOKIE['cookie_login']) && isset($_COOKIE['cookie_password'])) {
 try {
  $user = EfrontUserFactory :: factory($_COOKIE['cookie_login']);
  $user -> login($_COOKIE['cookie_password'], true);
  if ($GLOBALS['configuration']['show_license_note'] && $user -> user['viewed_license'] == 0) {
   eF_redirect("index.php?ctg=agreement");
  } else {
   // Check if the mobile version of eFront is required - if so set a session variable accordingly
   //eF_setMobile();
            EfrontEvent::triggerEvent(array("type" => EfrontEvent::SYSTEM_VISITED, "users_LOGIN" => $user -> user['login'], "users_name" => $user -> user['name'], "users_surname" => $user -> user['surname']));
   eF_redirect("".$user -> user['user_type']."page.php");
  }
  exit;
 } catch (EfrontUserException $e) {}
}
/*

 * Make sure that if a user has registered lessons without being logged in, 

 * after he logs in he will be redirected to the "complete registration" page

 * In addition, set "login_mode" to 1, meaning that the user pressed the "continue" 

 * button in his cart, so the next step should be loging in 

 */
if (isset($_GET['register_lessons'])) {
//	setcookie('c_request', 'index.php?register_lessons=1&checkout=1', time() + 300);
 $_SESSION['login_mode'] = '1';
} elseif (!isset($_GET['ctg']) || $_GET['ctg'] == 'lessons') {
//	setcookie('c_request', '', time() - 86400);
 $_SESSION['login_mode'] = '0';
}
isset($_GET['ctg']) && $_GET['ctg'] == 'login' ? $postTarget = basename($_SERVER['PHP_SELF'])."?ctg=login" : $postTarget = basename($_SERVER['PHP_SELF'])."?index_efront";
$form = new HTML_QuickForm("login_form", "post", $postTarget, "", "class = 'indexForm'", true);
$form -> removeAttribute('name');
$form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
$form -> addElement('text', 'login', _LOGIN, 'class = "inputText" id = "login_box"');
$form -> addRule('login', _THEFIELD.' "'._LOGIN.'" '._ISMANDATORY, 'required', null, 'client');
$form -> addRule('login', _INVALIDLOGIN, 'checkParameter', 'login');
$form -> addElement('password', 'password', _PASSWORD, 'class = "inputText" tabindex = "0"');
$form -> addRule('password', _THEFIELD.' "'._PASSWORD.'" '._ISMANDATORY, 'required', null, 'client');
$form -> addElement('checkbox', 'remember', _REMEMBERME, null, 'class = "inputCheckbox"');
$form -> addElement('submit', 'submit_login', _ENTER, 'class = "flatButton"');
if ($form -> isSubmitted() && $form -> validate()) {
 try {
  $user = EfrontUserFactory :: factory(trim($form -> exportValue('login')));
  if ($GLOBALS['configuration']['lock_down'] && $user -> user['user_type'] != 'administrator') {
   eF_redirect("index.php?message=".urlencode(_LOCKDOWNONLYADMINISTRATORSCANLOGIN)."&message_type=failure");
   exit;
  }
  $user -> login($form -> exportValue('password'));
  if ($form -> exportValue('remember')) { //The user asked to remeber login (it is implemented with cookies)
   $expire = time() + 30 * 86400; //1 month
   setcookie("cookie_login", $_SESSION['s_login'], $expire);
   setcookie("cookie_password", $_SESSION['s_password'], $expire);
  } else {
   setcookie("cookie_login", '', time() - 3600);
   setcookie("cookie_password", '', time() - 3600);
  }
  // Check if the mobile version of eFront is required - if so set a session variable accordingly
  //eF_setMobile();
  if ($GLOBALS['configuration']['show_license_note'] && $user -> user['viewed_license'] == 0) {
   eF_redirect("index.php?ctg=agreement");
  } elseif ($_SESSION['login_mode']) {
      eF_redirect("index.php?ctg=checkout&checkout=1");
  } else {
   EfrontEvent::triggerEvent(array("type" => EfrontEvent::SYSTEM_VISITED, "users_LOGIN" => $user -> user['login'], "users_name" => $user -> user['name'], "users_surname" => $user -> user['surname']));
   eF_redirect("".$user -> user['user_type']."page.php");
  }
  exit;
 } catch (EfrontUserException $e) {
  if ($GLOBALS['configuration']['activate_ldap']) {
   if (!extension_loaded('ldap')) {
    $message = $e -> getMessage().'<br/>'._LDAPEXTENSIONNOTLOADED;
    $message_type = 'failure';
   } else {
    $result = eF_checkUserLdap($form -> exportValue('login'), $form -> exportValue('password'));
    if ($result) { //The user exists in the LDAP server
     eF_redirect("index.php?ctg=signup&ldap=1&login=".$form -> exportValue('login'));
    } else {
     $message = _LOGINERRORPLEASEMAKESURECAPSLOCKISOFF;
     $message_type = 'failure';
    }
   }
  } elseif ($e -> getCode() == EfrontUserException :: USER_PENDING) {
   $message = $e -> getMessage();
   $message_type = 'failure';
  } elseif ($e -> getCode() == EfrontUserException :: USER_INACTIVE) {
   $message = $e -> getMessage();
   $message_type = 'failure';
  }
  else {
   $message = _LOGINERRORPLEASEMAKESURECAPSLOCKISOFF;
   $message_type = 'failure';
  }
  $form -> setConstants(array("login" => $values['login'], "password" => ""));
 } catch (Exception $e) {
     $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
     $message = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
     $message_type = failure;
 }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
$form -> setRequiredNote(_REQUIREDNOTE);
$form -> accept($renderer);
$smarty -> assign('T_LOGIN_FORM', $renderer -> toArray());
/* -----------------End of Login part-----------------------------*/
if (isset($_GET['ctg']) && $_GET['ctg'] == 'agreement' && $_SESSION['s_login']) { //Display license agreement
 try {
  $user = EfrontUserFactory :: factory($_SESSION['s_login']);
  $agreementForm = new HTML_QuickForm("agreement_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=agreement", "", "class = 'indexForm'", true);
  $agreementForm -> addElement('submit', 'submit_decline', _NOTACCEPTANDEXIT, 'class = "flatButton"');
  $agreementForm -> addElement('submit', 'submit_accept', _ACCEPTANDCONTINUE, 'class = "flatButton"');
  if ($agreementForm -> isSubmitted() && $agreementForm -> validate()) {
   $values = $agreementForm -> exportValues();
   if ($values['submit_accept']) {
    $user -> user['viewed_license'] = 1;
    $user -> persist();
    // Check if the mobile version of eFront is required - if so set a session variable accordingly
    //eF_setMobile();
    EfrontEvent::triggerEvent(array("type" => EfrontEvent::SYSTEM_VISITED, "users_LOGIN" => $user -> user['login'], "users_name" => $user -> user['name'], "users_surname" => $user -> user['surname']));
    eF_redirect("".$user -> user['user_type']."page.php");
   } else {
    $user -> logout();
    eF_redirect("index.php");
   }
  }
  $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
  $agreementForm -> accept($renderer);
  $smarty -> assign('T_AGREEMENT_FORM', $renderer -> toArray());
 } catch (Exception $e) {
  eF_redirect("index.php?message=".urlencode($e -> getMessage()." (".$e -> getCode().")")."&message_type=failure");
 }
}
/* ---------------------------------------------------------Activation by email part--------------------------------------------------------- */
if (isset($_GET['account']) && isset($_GET['key']) && eF_checkParameter($_GET['account'], 'login') && eF_checkParameter($_GET['key'], 'timestamp')) {
 $result = eF_getTableData("users", "timestamp, active", "login='".$_GET['account']."'");
 if ($configuration['activation'] == 0 && $configuration['mail_activation'] == 1) {
  if ($result[0]['active'] == 0 && $result[0]['timestamp'] == $_GET['key']) {
   try {
    $user = EfrontUserFactory :: factory($_GET['account']);//new EfrontUser($_GET['login']);
    $user -> activate();
    $message = _ACCOUNTSUCCESSFULLYACTIVATED;
    $message_type = 'success';
    eF_redirect(''.basename($_SERVER['PHP_SELF']).'?message='.urlencode($message).'&message_type=success');
   } catch (EfrontException $e) {
    $message = _PROBLEMACTIVATINGACCOUNT.': '.$e -> getMessage().' ('.$e -> getCode().')';
    $message_type = 'failure';
   }
  }
 } else {
  $message = _YOUCANNOTACCESSTHISPAGE;
  eF_redirect(''.basename($_SERVER['PHP_SELF']).'message='.urlencode($message).'&message_type=failure');
 }
}
/* ---------------------------------------------------------Reset Password part--------------------------------------------------------- */
if (isset($_GET['ctg']) && $_GET['ctg'] == 'reset_pwd') { //The user asked to display the contact form
 $smarty -> assign('T_CTG', 'reset_pwd');
 $form = new HTML_QuickForm("reset_password_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=reset_pwd", "", "class = 'indexForm'", true);
 $form -> removeAttribute('name');
 $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
 $form -> addElement('text', 'login_or_pwd', _LOGINOREMAIL, 'class = "inputText"');
 $form -> addRule('login_or_pwd', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
 $form -> addRule('login_or_pwd', _INVALIDFIELDDATA, 'checkParameter', 'text');
 $form -> addElement('submit', 'submit_reset_password', _SUBMIT, 'class="flatButton"');
 if ($form -> isSubmitted() && $form -> validate()) {
  $input = $form -> exportValue("login_or_pwd");
  try {
   if (eF_checkParameter($input, 'email')) { //The user entered an email address
    $result = eF_getTableData("users", "login", "email='".$input."'"); //Get the user stored login
    if (sizeof($result) > 1) {
     $message = _MORETHANONEUSERWITHSAMEMAILENTERLOGIN;
     $message_type = 'failure';
     eF_redirect(''.basename($_SERVER['PHP_SELF']).'?ctg=reset_pwd&message='.urlencode($message).'&message_type='.$message_type);
     exit;
    } else {
     $user = EfrontUserFactory :: factory($result[0]['login']);
    }
   } elseif (eF_checkParameter($input, 'login')) { //The user entered his login name
    $user = EfrontUserFactory :: factory($input);
   }
   if ($user -> isLdapUser) {
    eF_redirect(''.basename($_SERVER['PHP_SELF']).'?message='.urlencode(_LDAPUSERMUSTCONTACTADMIN.$GLOBALS['configuration']['system_email']).'&message_type=failure');
   } else {
             EfrontEvent::triggerEvent(array("type" => EfrontEvent::SYSTEM_FORGOTTEN_PASSWORD, "users_LOGIN" => $user->user['login'], "users_name" => $user->user['name'], "users_surname" => $user->user['surname']));
    $message = _ANEMAILHASBEENSENT;
    $message_type = 'success';
    if ($_SESSION['login_mode'] != 1) {
     eF_redirect(''.basename($_SERVER['PHP_SELF']).'?message='.urlencode($message).'&message_type='.$message_type);
    }
   }
  } catch (Exception $e) {
   $message = _NONEXISTINGMAIL;
   $message_type = 'failure';
   eF_redirect(''.basename($_SERVER['PHP_SELF']).'?ctg=reset_pwd&message='.urlencode($message).'&message_type='.$message_type);
  }
 } elseif (isset($_GET['id']) && isset($_GET['login'])) { //Second stage, user received the email and clicked on the link
  $login = $_GET['login'];
  if (!eF_checkParameter($login, 'login')) { //Possible hacking attempt: malformed user
   $message = _INVALIDUSER;
   $message_type = 'failure';
  } else {
   $user = eF_getTableData("users", "email, name", "login='".$login."'");
   if (strcmp($_GET['id'], EfrontUser::createPassword($login)) == 0 && sizeof($user) > 0) {
    $password = mb_substr(md5($login.time()), 0, 8);
    $password_encrypted = EfrontUser::createPassword($password);
    eF_updateTableData("users", array('password' => $password_encrypted), "login='$login'");
                EfrontEvent::triggerEvent(array("type" => EfrontEvent::SYSTEM_NEW_PASSWORD_REQUEST, "users_LOGIN" => $login, "entity_name" => $password));
                $message = _EMAILWITHPASSWORDSENT;
                eF_redirect(''.basename($_SERVER['PHP_SELF']).'?message='.urlencode($message).'&message_type=success');
   } else {
    $message = _INVALIDUSER;
    $message_type = 'failure';
   }
  }
 }
 $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
 $renderer -> setRequiredTemplate(
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}');
 $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
 $form -> setRequiredNote(_REQUIREDNOTE);
 $form -> accept($renderer);
 $smarty -> assign('T_RESET_PASSWORD_FORM', $renderer -> toArray());
}
/* -------------------------------------------------------End of Reset Password part--------------------------------------------------------- */
/* -----------------------------------------------------Sign up part--------------------------------------------------------- */
if (isset($_GET['ctg']) && ($_GET['ctg'] == "signup") && $configuration['signup']) {
 $users = eF_getTableDataFlat("users", "*");
 //$versionDetails = eF_checkVersionKey($configuration['version_key']);
 $smarty -> assign("T_CTG", "signup");
 $form = new HTML_QuickForm("signup_register_personal_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=signup".(isset($_GET['ldap']) ? '&ldap=1' : ''), "", "class = 'indexForm'", true);
 $form -> removeAttribute('name');
 $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
 $form -> registerRule('checkNotExist', 'callback', 'eF_checkNotExist'); //This rule is using our function, eF_checkNotExist, to ensure that no duplicate values are inserted in unique fields, such as login and email
    $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
    $form -> addRule('login', _THEFIELD.' '._LOGIN.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('login', _THEFIELD.' "'._LOGIN.'" '._MUSTBESMALLERTHAN.' 50 '.mb_strtolower(_CHARACTERS), 'maxlength', 50, 'client');
    $form -> addRule('login', _THEFIELD.' '._LOGIN.' '._HASINVALIDCHARACTERS.'. '._ONLYALLOWEDCHARACTERSLOGIN, 'checkParameter', 'login');
    $form -> addRule('login', _THELOGIN.' &quot;'.($form -> exportValue('login')).'&quot; '._ALREADYEXISTS, 'checkNotExist', 'login');
 $form -> addElement(isset($_GET['ldap']) ? 'text' : 'password', 'password', _PASSWORD, 'class = "inputText"');
 $form -> addElement(isset($_GET['ldap']) ? 'text' : 'password', 'passrepeat', _REPEATPASSWORD, 'class = "inputText"');
 $form -> addRule('password', _THEFIELD.' '._PASSWORD.' '._ISMANDATORY, 'required', null, 'client');
 $form -> addRule('passrepeat', _THEFIELD.' '._REPEATPASSWORD.' '._ISMANDATORY, 'required', null, 'client');
 $form -> addRule(array('password', 'passrepeat'), _PASSWORDSDONOTMATCH, 'compare', null, 'client');
 $form -> addRule('passrepeat', str_replace("%x", $GLOBALS['configuration']['password_length'], _PASSWORDMUSTBE6CHARACTERS), 'minlength', $GLOBALS['configuration']['password_length'], 'client');
    $form -> addElement('text', 'firstName', _FIRSTNAME, 'class = "inputText"');
    $form -> addRule('firstName', _THEFIELD.' '._FIRSTNAME.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('firstName', _THEFIELD.' "'._FIRSTNAME.'" '._MUSTBESMALLERTHAN.' 50 '.mb_strtolower(_CHARACTERS), 'maxlength', 50, 'client');
    $form -> addRule('firstName', _THEFIELD.' '._FIRSTNAME.' '._HASINVALIDCHARACTERS.'. '._ONLYALLOWEDCHARACTERSTEXT, 'checkParameter', 'text');
 $form -> addElement('text', 'lastName', _LASTNAME, 'class = "inputText"');
    $form -> addRule('lastName', _THEFIELD.' '._LASTNAME.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('lastName', _THEFIELD.' "'._LASTNAME.'" '._MUSTBESMALLERTHAN.' 50 '.mb_strtolower(_CHARACTERS), 'maxlength', 50, 'client');
    $form -> addRule('lastName', _THEFIELD.' '._LASTNAME.' '._HASINVALIDCHARACTERS.'. '._ONLYALLOWEDCHARACTERSTEXT, 'checkParameter', 'text');
 $form -> addElement('text', 'email', _EMAILADDRESS, 'class = "inputText "');
    $form -> addRule('email', _THEFIELD.' '._EMAILADDRESS.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('email', _THEFIELD.' '._EMAILADDRESS.' '._HASINVALIDCHARACTERS.'. '._ONLYALLOWEDCHARACTERSTEXT, 'email', null, 'client');
    //$form -> addRule('email', _THEEMAIL.' &quot;'.($form -> exportValue('email')).'&quot; '._ALREADYEXISTS, 'checkNotExist', 'email');
 $languages = array();
 foreach (EfrontSystem :: getLanguages() as $key => $value) {
  if ($value['active']) {
   $languages[$key] = $value['translation'];
  }
 }
 $form -> addElement('select', 'languages_NAME', _LANGUAGE, $languages, 'class = "inputSelect" onchange = "location = \'index.php?ctg=signup&bypass_language=\'+this.options[this.selectedIndex].value"'); //A select drop down for languages
 if ($_SESSION['s_language']) {
  $form -> setDefaults(array('languages_NAME' => $_SESSION['s_language'])); //The default language is also the selected one
 } else {
  $form -> setDefaults(array('languages_NAME' => $GLOBALS['configuration']['default_language'])); //The default language is also the selected one
 }
 if ($GLOBALS['configuration']['onelanguage']) {
  $form -> freeze(array('languages_NAME'));
 }
 $element = $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputText" id = "comments"');
 $element -> setCols(40);
 $element -> setRows(2);
 $form -> addElement('submit', 'submit_register', _REGISTER, 'class = "flatButton"');
 if (isset($_GET['ldap'])) {
  $result = eF_getLdapValues($GLOBALS['configuration']['ldap_uid'].'='.$_GET['login'], array($GLOBALS['configuration']['ldap_preferredlanguage'],
  $GLOBALS['configuration']['ldap_mail'],
  $GLOBALS['configuration']['ldap_cn'],
  $GLOBALS['configuration']['ldap_uid']));
  $name_parts = explode(" ", $result[0]['cn'][0]);
  $first_name = array_shift($name_parts);
  sizeof($name_parts) == 0 ? $last_name = $first_name : $last_name = implode(" ", $name_parts);
  $form -> setDefaults(array("login" => $_GET['login'],
                                   "password" => _LDAPACCOUNTPASSWORD,
                                   "passrepeat" => _LDAPACCOUNTPASSWORD,
                                   "email" => $result[0]['mail'][0],
                                   "firstName" => $first_name,
                                   "lastName" => $last_name));
  $form -> freeze(array('login', 'password', 'passrepeat'));
 } elseif ($configuration['only_ldap']) {
  $message = _ONLYLDAPREGISTRATIONPERMITTED;
  $message_type = 'failure';
  eF_redirect("".basename($_SERVER['PHP_SELF'])."?message=".urlencode($message)."&message_type=$message_type");
 }
 if ($form -> isSubmitted()) {
  if ($form -> validate()) {
   if (isset($_SESSION['s_login'])) { //A logged-in user wants to signup: Log him out first
    $user = EfrontUserFactory :: factory($_SESSION['s_login']);
    $user -> logout();
   }
   $values = $form -> exportValues(); //Get the form values
   $user_data = array("login" => $values['login'],
                               "password" => isset($_GET['ldap']) ? 'ldap' : $values['password'],
                               "name" => $values['firstName'],
                               "surname" => $values['lastName'],
                               "email" => $values['email'],
                               "comments" => $values['comments'],
                               "pending" => ($configuration['activation']) ? 0 : 1,
                               "active" => $configuration['activation'],
                               "languages_NAME" => $values['languages_NAME']);
            foreach ($user_profile as $field) { //Get the custom fields values
                $user_data[$field['name']] = $values[$field['name']];
            }
            try {
          $newUser = EfrontUser :: createUser($user_data);
          EfrontEvent::triggerEvent(array("type" => EfrontEvent::SYSTEM_REGISTER, "users_LOGIN" => $user_data['login'], "users_name" => $user_data['name'], "users_surname" => $user_data['surname']));
          // send not-visited notifications for the newly registered user
          EfrontEvent::triggerEvent(array("type" => (-1) * EfrontEvent::SYSTEM_VISITED, "users_LOGIN" => $user_data['login'], "users_name" => $user_data['name'], "users_surname" => $user_data['surname']));
    if ($configuration['activation'] == 0) {
     if ($configuration['mail_activation'] == 1){
      $tmp = eF_getTableData("users","timestamp","login='".$user_data['login']."'");
      $timestamp = $tmp[0]["timestamp"];
      EfrontEvent::triggerEvent(array("type" => EfrontEvent::SYSTEM_ON_EMAIL_ACTIVATION, "users_LOGIN" => $tmp[0]['login'], "users_name" => $tmp[0]['name'], "users_surname" => $tmp[0]['surname'], "timestamp" => $timestamp, "entity_name" => $timestamp));
      $message = _YOUWILLRECEIVEMAILFORACCOUNTACTIVATION;
      eF_redirect(''.basename($_SERVER['PHP_SELF']).'?message='.urlencode($message).'&message_type=success');
     } else{
      $message = _ADMINISTRATORWILLACTIVATEYOURACCOUNT;
      eF_redirect(''.basename($_SERVER['PHP_SELF']).'?message='.urlencode($message).'&message_type=success');
     }
    } else {
     $message = _SUCCESSREGISTER;
     $message_type = 'success';
     //Automatic registration trigers login as well, unless login_mode is enabled
     $newUser -> login($user_data['password'], true);
     if ($GLOBALS['configuration']['show_license_note'] && $newUser -> user['viewed_license'] == 0) {
      eF_redirect("index.php?ctg=agreement&message=".urlencode($message)."&message_type=".$message_type);
     } else if ($_SESSION['login_mode']) {
         eF_redirect("index.php?ctg=checkout&checkout=1&message=".urlencode($message)."&message_type=".$message_type);
     } else {
      eF_redirect("".$newUser -> user['user_type']."page.php?message=".urlencode($message)."&message_type=".$message_type);
     }
    }
   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = failure;
   }
  }
 }
 $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
 $renderer -> setRequiredTemplate(
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}'
        );
        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);
        $smarty -> assign('T_PERSONAL_INFO_FORM', $renderer -> toArray());
}
/* --------------------------------------------------- End of Sign up part--------------------------------------------------- */
/* -------------------------------------------------------Contact part--------------------------------------------------------- */
if (isset($_GET['ctg']) && $_GET['ctg'] == 'contact') { //The user asked to display the contact form
 $smarty -> assign('T_CTG', 'contact');
 $form = new HTML_QuickForm("contact_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=contact", "", "class = 'indexForm'", true);
 $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
 $form -> addElement('text', 'email', _YOUREMAIL, 'class = "inputText"');
 $form -> addRule('email', _THEFIELD.' "'._EMAIL.'" '._ISMANDATORY, 'required');
 $form -> addRule('email', _INVALIDFIELDDATA, 'checkParameter', 'email');
 $form -> addElement('text', 'message_subject', _MESSAGESUBJECT, 'class = "inputText"');
 $form -> addRule('message_subject', _INVALIDFIELDDATA, 'checkParameter', 'text');
 $form -> addElement('textarea', 'message_body', _TEXT, 'class = "inputText" id = "contact"');
 $form -> addElement('submit', 'submit_contact', _SUBMIT, 'class = "flatButton"');
 if ($form -> isSubmitted()) {
  if ($form -> validate()) {
   $to = $form -> exportValue("email");
   $subject = $form -> exportValue("message_subject");
   $body = $form -> exportValue("message_body");
   if (eF_mail($to, $GLOBALS['configuration']['system_email'], $subject." ["._FROM.": ".$sender."]", $body, false, true)) {
    $message = _SENDSUCCESS;
    $message_type = 'success';
    eF_redirect(''.basename($_SERVER['PHP_SELF']).'?message='.$message.'&message_type='.$message_type);
   } else {
    $message = _SENDFAILURE;
    $message_type = 'failure';
   }
  }
 }
 $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
 $renderer -> setRequiredTemplate(
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}');
 $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
 $form -> setRequiredNote(_REQUIREDNOTE);
 $form -> accept($renderer);
 $smarty -> assign('T_CONTACT_FORM', $renderer -> toArray());
}
/* -------------------------------------------------------End of Contact part--------------------------------------------------------- */
/* -------------------------------------------------------Lesson information part--------------------------------------------------------- */
if (isset($_GET['ctg']) && $_GET['ctg'] == 'lesson_info') { //The user asked to display information on a lesson
 //session_start();			//Isn't needed here if the head session_start() is in place
 if (!$smarty -> is_cached('index.tpl', $cacheId) || !$GLOBALS['configuration']['smarty_caching']) {
  try {
   if (isset($_GET['lessons_ID'])) {
    $lesson = new EfrontLesson($_GET['lessons_ID']);
    $lesson -> lesson['price_string'] = formatPrice($lesson -> lesson['price'], array($lesson -> options['recurring'], $lesson -> options['recurring_duration']), true);
    $smarty -> assign("T_LESSON", $lesson);
    $lessonInformation = $lesson -> getInformation();
    $content = new EfrontContentTree($lesson);
    if (sizeof($content -> tree) > 0) {
     $smarty -> assign("T_CONTENT_TREE", $content -> toHTML(false, 'dhtml_content_tree', array('noclick' => 1)));
    }
    $lessonInfo = new LearningObjectInformation(unserialize($lesson -> lesson['info']));
    $smarty -> assign("T_LESSON_INFO", $lessonInfo);
    $additionalInfo = $lesson -> getInformation();
    $smarty -> assign("T_ADDITIONAL_LESSON_INFO", $additionalInfo);
    if ($lesson -> lesson['course_only']) {
     $smarty -> assign("T_LESSON_COURSES", $lesson -> getCourses());
     if (isset($_GET['from_course']) && $_GET['from_course']) {
      $course = new EfrontCourse($_GET['from_course']);
      $smarty -> assign ("T_COURSE", $course);
      $smarty -> assign("T_HAS_COURSE", in_array($course -> course['id'], array_keys($userCourses)));
     }
    }
   } else if ($_GET['courses_ID']) {
    $course = new EfrontCourse($_GET['courses_ID']);
                $course -> course['price_string'] = formatPrice($course -> course['price'], array($course -> options['recurring'], $course -> options['recurring_duration']), true);
    $smarty -> assign("T_COURSE", $course);
    $lessons = $course -> getLessons();
    $smarty -> assign("T_COURSE_LESSONS", $lessons);
    $courseInfo = new LearningObjectInformation(unserialize($course -> course['info']));
    $smarty -> assign("T_COURSE_INFO", $courseInfo);
    $additionalInfo = $course -> getInformation();
    $smarty -> assign("T_ADDITIONAL_COURSE_INFO", $additionalInfo);
   }
  } catch (Exception $e) {
   $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
   $message = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
   $message_type = failure;
  }
 }
}
/* -------------------------------------------------------End of Lesson information part--------------------------------------------------------- */
if (isset($_GET['message'])) {
 $message ? $message .= '<br>'.$_GET['message'] : $message = $_GET['message'];
 $message_type = $_GET['message_type'];
}
$smarty -> assign('T_MESSAGE', $message);
$smarty -> assign('T_MESSAGE_TYPE', $message_type);
$smarty -> assign('T_LDAPSUPPORT', $configuration['activate_ldap']);
$smarty -> assign('T_ONLY_LDAP', $configuration['only_ldap']);
$smarty -> assign('T_EXTERNALLYSIGNUP', $configuration['signup']);
if (strlen($configuration['css']) > 0 && is_file(G_CUSTOMCSSPATH.$configuration['css'])) { //Load custom css, if one exists
 $smarty -> assign("T_CUSTOM_CSS", $configuration['css']);
}
$debug_timeBeforeSmarty = microtime(true) - $debug_TimeStart;
$benchmark -> set('script');
$loadScripts = array('includes/catalog');
if (isset($_GET['ajax']) && $_GET['ajax'] == 'cart') {
    try {
        include "catalog.php";
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo rawurlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
}
if (isset($_GET['ctg']) && $_GET['ctg'] == 'checkout' && $_GET['checkout'] && $_SESSION['s_login']) {
    try {
        /**Handles cart and catalog*/
        include "catalog.php";
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = failure;
    }
} else {
    $smarty -> assign("T_CART", cart :: prepareCart());
}
if ($GLOBALS['currentTheme'] -> options['sidebar_interface'] == 2 && $GLOBALS['currentTheme'] -> options['show_header'] == 2) {
 if (isset ($_SESSION['s_login'])) {
  try {
   $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
   if (isset($currentUser)) {
    if (unserialize($currentUser -> user['additional_accounts'])) {
     $accounts = unserialize($currentUser -> user['additional_accounts']);
     $queryString = "'".implode("','", array_values($accounts))."'";
     $result = eF_getTableData("users", "login, user_type", "login in (".$queryString.")");
        $smarty -> assign("T_BAR_ADDITIONAL_ACCOUNTS", $result);
    }
   }
  } catch (Exception $e) {
  }
 }
 if (((isset($GLOBALS['currentLesson']) && $GLOBALS['currentLesson'] -> options['online']) && $GLOBALS['currentLesson'] -> options['online'] == 1) || $_SESSION['s_type'] == 'administrator' ){
  $loadScripts[] = 'sidebar';
     //$currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
     $onlineUsers = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);
     $size = sizeof($onlineUsers);
     if ($size) {
         $smarty -> assign("T_ONLINE_USERS_COUNT", $size);
     }
     $smarty -> assign("T_ONLINE_USERS_LIST", $onlineUsers);
 }
}
if ($_SESSION['s_login']) { //This way, logged in users that stay on index.php are not logged out
    $loadScripts[] = 'sidebar';
}
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_SEARCH_MESSAGE", $search_message);
if (!$smarty -> is_cached('index.tpl', $cacheId) || !$GLOBALS['configuration']['smarty_caching']) {
 //Main scripts, such as prototype
 $mainScripts = array('scriptaculous/prototype',
       'scriptaculous/scriptaculous',
       'scriptaculous/effects',
                      'EfrontScripts',
       'efront_ajax',
                      'includes/events');
 $smarty -> assign("T_HEADER_MAIN_SCRIPTS", implode(",", $mainScripts));
 //Operation/file specific scripts
 $loadScripts = array_diff($loadScripts, $mainScripts); //Clear out duplicates
 $smarty -> assign("T_HEADER_LOAD_SCRIPTS", implode(",", array_unique($loadScripts))); //array_unique, so it doesn't send duplicate entries
 $smarty -> assign("T_NEWS", news :: getNews(0, true));
 $smarty -> assign("T_ONLINE_USERS_LIST", EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60));
 $smarty -> display('index.tpl');
} else {
 $smarty -> display('index.tpl');
}
$benchmark -> set('smarty');
$benchmark -> stop();
if (G_DEBUG) {
 echo $benchmark -> display();
}
?>