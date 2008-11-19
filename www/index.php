<?php
/**
 * Platform index page
 *
 * This is the index page, allowing for logging in, registering new users,
 * contacting and resetting password
 *
 * @package eFront
 * @version 3.1.1
 */
session_cache_limiter('none');
session_start();    //This causes the double-login problem, where the user needs to login twice when already logged in with the same browser

    $debug_TimeStart = microtime(true);
    
    $path = "../libraries/";
    
    if (!is_file($path."configuration.php")) {                        //If the configuration file does not exist, this is a fresh installation, so redirect to installation page
        is_file("install/install.php") ? header("location:install/install.php") : print('Failed locating configuration file <br/> Failed locating installation directory <br/> Please execute installation script manually <br/>');
        exit;
    } else {
        /** Configuration file */
        require_once $path."configuration.php";
    }
    
    if (isset($_GET['register_lessons'])) {
        setcookie('c_request', 'directory.php?fct=cartPreview', time() + 300);
    }

    if (isset($_GET['bypass_language']) && in_array($_GET['bypass_language'], array_keys(EfrontSystem :: getLanguages()))) {                        //Check if another language is specified in the address bar
        $_SESSION['s_language'] = $_GET['bypass_language'];
    } 
    if (isset($_SESSION['s_language'])) {
        $smarty -> assign("T_LANGUAGE", $_SESSION['s_language']);
    } else {
        $smarty -> assign("T_LANGUAGE", $GLOBALS['configuration']['default_language']);
    }
    
    
    
    foreach (EfrontSystem :: getLanguages() as $key => $value) {
        if ($value['active']) {
            $languages[$key] = $value['translation'];
        }
    }    
    $smarty -> assign("T_LANGUAGES", $languages);    
    
    if (is_dir("install") && isset($_GET['delete_install'])) {
        try {
            $dir = new EfrontDirectory('install');
            $dir -> delete();
        } catch (Exception $e) {
            echo "The installation directory could not be deleted. Please delete it manually or your system security is at risk.";
        }
    }
    
    $debug_InitTime = microtime(true) - $debug_TimeStart;
    
    $message = '';$message_type = '';                                                                        //Initialize error messages
    
    if ($configuration['cms_page'] != "" && sizeof($_GET) == 0){                                             //if there is cms page and no get parameter defined
        header("location:".G_RELATIVEADMINLINK.$configuration['cms_page'].".php");
    }
    
    if (isset($_GET['logout']) && !isset($_POST['submit_login'])) {                                                       //If user wants to log out
        session_start();
        if ($_SESSION['s_login']) {
            try {
                $user = EfrontUserFactory :: factory($_SESSION['s_login']);
                $user -> logout();
                if ($GLOBALS['configuration']['logout_redirect']) {
                    header("location:".$GLOBALS['configuration']['logout_redirect']);
                }
            } catch (EfrontUserException $e) {
                $message = $e -> getMessage();
            }
        }
    
        if (isset($_GET['reason']) && $_GET['reason']=='timeout') {
            $message      = _YOUHAVELOGGEDOUTBECAUSEYOURSESSIONHASTIMEDOUT;
            $message_type = 'failure';
        }
    }
    
    $blocks = array('login'           => _LOGINENTRANCE,
    				'online'          => _USERSONLINE, 
    				'lessons'         => _LESSONS, 
                	'selectedLessons' => _SELECTEDLESSONS,
    				'news'            => _SYSTEMNEWS);
    $customBlocks = unserialize($GLOBALS['configuration']['custom_blocks']);
    foreach ($customBlocks as $key => $block) {
        $blocks[$key] = $block['title'];
    }
    $smarty -> assign("T_CUSTOM_BLOCKS", $customBlocks);
    $smarty -> assign("T_BLOCKS", $blocks);
    $currentPositions = unserialize($GLOBALS['configuration']['index_positions']);
    $smarty -> assign("T_POSITIONS", $currentPositions);
    
    $directionsTree = new EfrontDirectionsTree();
    if (isset($_GET['filter'])) {
        $result  = eF_getTableData("lessons", "*");
        foreach ($result as $value) {
            $lessonNames[$value['id']] = array('name' => $value['name']);
            $lessonData[$value['id']] = $value;
        }    
        
        $lessons = eF_filterData($lessonNames, $_GET['filter']);   
    
        foreach ($lessons as $id => $value) {
            $lessons[$id] = new EfrontLesson($lessonData[$id]);
        }
    
        $result  = eF_getTableData("courses", "*");
        foreach ($result as $value) {
            $courseNames[$value['id']] = array('name' => $value['name']);
            $courseData[$value['id']] = $value;
        }    
        $courses = eF_filterData($courseNames, $_GET['filter']);    
        foreach ($courses as $id => $value) {
            $courses[$id] = new EfrontCourse($courseData[$id]);
        }
    
        echo $directionsTree -> toHTML(false, $lessons, $courses, false, array('lessons_link' => basename($_SERVER['PHP_SELF']).'?ctg=lesson_info&lessons_ID=',
                            												   'courses_link' => basename($_SERVER['PHP_SELF']).'?ctg=lesson_info&courses_ID=',
                            												   'search'       => true, 
                            												   'tree_tools'   => false));
        exit;
    }
    $options        = array('lessons_link' => basename($_SERVER['PHP_SELF']).'?ctg=lesson_info&lessons_ID=',
                            'courses_link' => basename($_SERVER['PHP_SELF']).'?ctg=lesson_info&courses_ID=',
                            'search'       => true,
                            'url'          => $_SERVER['PHP_SELF']);
    $smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, false, false, false, $options)); 


/* -------------------------------------------------------Login part-------------------------------------------------------------------*/
    if (isset($_COOKIE['cookie_login']) && isset($_COOKIE['cookie_password'])) {
	    try {
	        $user = EfrontUserFactory :: factory($_COOKIE['cookie_login']);
	        $user -> login($_COOKIE['cookie_password'], true);
	        header("location:".$user -> user['user_type']."page.php");
	        exit;
	    } catch (EfrontUserException $e) {}
	}

    $form = new HTML_QuickForm("login_form", "post", basename($_SERVER['PHP_SELF'])."?index_efront", "", null, true);
    $form -> removeAttribute('name');
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter

    $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
    $form -> addRule('login', _THEFIELD.' "'._LOGIN.'" '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('login', _YOUCANNOTLOGINAS, 'checkParameter', 'login');

    $form -> addElement('password', 'password', _PASSWORD, 'class = "inputText" tabindex = "0"');
    $form -> addRule('password', _THEFIELD.' "'._PASSWORD.'" '._ISMANDATORY, 'required', null, 'client');

    $form -> addElement('checkbox', 'remember', _REMEMBERME, null, 'class = "inputCheckbox"');
    $form -> addElement('submit', 'submit_login', _ENTER, 'class = "flatButton"');

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_LOGIN_FORM', $renderer -> toArray());

    if ($form -> isSubmitted() && $form -> validate()) {
        try {
        	$user = EfrontUserFactory :: factory($form -> exportValue('login'));
            $user -> login($form -> exportValue('password'));
	        if ($form -> exportValue('remember')) {                                                             //The user asked to remeber login (it is implemented with cookies)
	            $expire = time() + 30 * 86400;                                                                  //1 month
	            setcookie("cookie_login",    $_SESSION['s_login'],    $expire);
	            setcookie("cookie_password", $_SESSION['s_password'], $expire);
	        } else {
	            setcookie("cookie_login",    '', time() - 3600);
	            setcookie("cookie_password", '', time() - 3600);
	        }

	        header("location:".$user -> user['user_type']."page.php");
	        exit;
        } catch (EfrontUserException $e) {
            if ($GLOBALS['configuration']['activate_ldap']) {
                if (!extension_loaded('ldap')) {
                    $message      = $e -> getMessage().'<br/>'._LDAPEXTENSIONNOTLOADED;
                    $message_type = 'failure';
                } else {
                    $result = eF_checkUserLdap($form -> exportValue('login'), $form -> exportValue('password'));
                    if ($result) {                                                //The user exists in the LDAP server
                        header("location:index.php?ctg=signup&ldap=1&login=".$form -> exportValue('login'));
                    } else if ($result === 0) {                                    //false means that this user does not exist, and 0 means that the user exists but the password is invalid
                        $message = _INVALIDPASSWORD;
                        $message_type = 'failure';
                    } else {
                        $message      = $e -> getMessage();
                        $message_type = 'failure';
                    }
                }
            } else {
                $message      = $e -> getMessage();
                $message_type = 'failure';
            }
        }
    }
/* -----------------End of Login part-----------------------------*/

/* ---------------------------------------------------------Activation by email part--------------------------------------------------------- */
if (isset($_GET['account']) && isset($_GET['key']) && eF_checkParameter($_GET['account'], 'login') && eF_checkParameter($_GET['key'], 'timestamp')) {
    $result = eF_getTableData("users", "timestamp, active", "login='".$_GET['account']."'");
    if ($configuration['activation'] == 0 && $configuration['mail_activation'] == 1) {
        if ($result[0]['active'] == 0 && $result[0]['timestamp'] == $_GET['key']) {
            try {
	            $user = EfrontUserFactory :: factory($_GET['account']);//new EfrontUser($_GET['login']);
	            $user -> activate();
	            $message      = _ACCOUNTSUCCESSFULLYACTIVATED;
	            $message_type = 'success';
	            header('location:'.basename($_SERVER['PHP_SELF']).'?message='.urlencode($message).'&message_type=success');
            } catch (EfrontException $e) {
	            $message      = _PROBLEMACTIVATINGACCOUNT.': '.$e -> getMessage().' ('.$e -> getCode().')';
	            $message_type = 'failure';
            }
        }
    } else {
        $message      = _YOUCANNOTACCESSTHISPAGE;
        header('location:'.basename($_SERVER['PHP_SELF']).'message='.urlencode($message).'&message_type=failure');
    }

}


/* ---------------------------------------------------------Reset Password part--------------------------------------------------------- */
if (isset($_GET['ctg']) && $_GET['ctg'] == 'reset_pwd') {                                                         //The user asked to display the contact form
    $smarty -> assign('T_CTG', 'reset_pwd');

    $form = new HTML_QuickForm("reset_password_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=reset_pwd", "", null, true);
    $form -> removeAttribute('name');
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter

    $form -> addElement('text', 'login_or_pwd', _LOGINOREMAIL, 'class = "inputText"');
    $form -> addRule('login_or_pwd', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('login_or_pwd', _INVALIDFIELDDATA, 'checkParameter', 'text');
    $form -> addElement('submit', 'submit_reset_password', _SUBMIT, 'class="flatButton"');

    if ($form -> isSubmitted() && $form -> validate()) {
        $input = $form -> exportValue("login_or_pwd");
        try {
            if (eF_checkParameter($input, 'email')) {                                               //The user entered an email address
                $result = eF_getTableData("users", "login", "email='".$input."'");                  //Get the user stored login
                $user = EfrontUserFactory :: factory($result[0]['login']);
            } elseif (eF_checkParameter($input, 'login')) {                                         //The user entered his login name
                $user = EfrontUserFactory :: factory($input);
            }
            if ($user -> isLdapUser) {
                header('location:'.basename($_SERVER['PHP_SELF']).'?message='.urlencode(_LDAPUSERMUSTCONTACTADMIN.$GLOBALS['configuration']['system_email']).'&message_type=failure');
            } else {
                eF_emailPasswordConfirmation($user -> user['email'], $user -> user['login'], $user -> user['name']);
                header('location:'.basename($_SERVER['PHP_SELF']).'?message='.urlencode(_ANEMAILHASBEENSENT).'&message_type=success');
            }
        } catch (Exception $e) {
            $message      = _NONEXISTINGMAIL;
            $message_type = 'failure';
        }
    } elseif (isset($_GET['id']) && isset($_GET['login'])) {                             //Second stage, user received the email and clicked on the link
        $login = $_GET['login'];
        if (!eF_checkParameter($login, 'login')) {                                      //Possible hacking attempt: malformed user
            $message      = _INVALIDUSER;
            $message_type = 'failure';
        } else {
            $user = eF_getTableData("users", "email, name", "login='".$login."'");
            if (strcmp($_GET['id'], md5($login.G_MD5KEY)) == 0 && sizeof($user) > 0) {
                $password           = mb_substr(md5($login.time()), 0, 8);
                $password_encrypted = md5($password.G_MD5KEY);

                eF_updateTableData("users", array('password' => $password_encrypted), "login='$login'");

                if (eF_emailNewPassword($user[0]['email'], $user[0]['name'], $password)) {
                    $message      = _EMAILWITHPASSWORDSENT;
                    header('location:'.basename($_SERVER['PHP_SELF']).'?message='.urlencode($message).'&message_type=success');
                } else {
                    $message      = _THEMAILCOULDNOTBESENDTRYLATER;
                    $message_type = 'failure';
                }

            } else {
                $message      = _INVALIDUSER;
                $message_type = 'failure';
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

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
	$versionDetails = eF_checkVersionKey($configuration['version_key']);

	if (sizeof($users['login']) > $versionDetails['users']) {
        $message      = _USERSEXCEEDED;
        $message_type = 'failure';
        header("location:".basename($_SERVER['PHP_SELF'])."?message=".urlencode($message)."&message_type=$message_type");
	}

    $smarty -> assign("T_CTG", "signup");

    $form = new HTML_QuickForm("signup_register_personal_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=signup".(isset($_GET['ldap']) ? '&ldap=1' : ''), "", null, true);
    $form -> removeAttribute('name');
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter
    $form -> registerRule('checkNotExist', 'callback', 'eF_checkNotExist');             //This rule is using our function, eF_checkNotExist, to ensure that no duplicate values are inserted in unique fields, such as login and email

    foreach (EfrontSystem :: getLanguages() as $key => $value) {
        if ($value['active']) {
            $languages[$key] = $value['translation'];
        }
    }
    $form -> addElement('select', 'languages_NAME', _LANGUAGE, $languages, 'class = "inputSelect" onchange = "location = \'index.php?ctg=signup&bypass_language=\'+this.options[this.selectedIndex].value"');     //A select drop down for languages
    if ($_SESSION['s_language']) {
        $form -> setDefaults(array('languages_NAME' => $_SESSION['s_language']));                                       //The default language is also the selected one
    } else {
        $form -> setDefaults(array('languages_NAME' => $GLOBALS['configuration']['default_language']));                                       //The default language is also the selected one
    }
    if ($GLOBALS['configuration']['onelanguage']) {
        $form -> freeze(array('languages_NAME'));
    }

    $form -> addElement('text', 'login', _LOGIN, 'class = "inputText"');
    $form -> addRule('login', _THEFIELD.' '._LOGIN.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('login', _THEFIELD.' '._LOGIN.' '._HASINVALIDCHARACTERS.'. '._ONLYALLOWEDCHARACTERSLOGIN, 'checkParameter', 'login');
    $form -> addRule('login', _THELOGIN.' &quot;'.($form -> exportValue('login')).'&quot; '._ALREADYEXISTS, 'checkNotExist', 'login');

    $form -> addElement(isset($_GET['ldap']) ? 'text' : 'password', 'password', _PASSWORD, 'class = "inputText"');
    $form -> addElement(isset($_GET['ldap']) ? 'text' : 'password', 'passrepeat', _REPEATPASSWORD, 'class = "inputText"');
    $form -> addRule('password', _THEFIELD.' '._PASSWORD.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('passrepeat', _THEFIELD.' '._REPEATPASSWORD.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule(array('password', 'passrepeat'), _PASSWORDSDONOTMATCH, 'compare', null, 'client');

    $form -> addElement('text', 'firstName', _FIRSTNAME, 'class = "inputText"');
    $form -> addRule('firstName', _THEFIELD.' '._FIRSTNAME.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('firstName', _THEFIELD.' '._FIRSTNAME.' '._HASINVALIDCHARACTERS.'. '._ONLYALLOWEDCHARACTERSTEXT, 'checkParameter', 'text');

    $form -> addElement('text', 'lastName', _LASTNAME, 'class = "inputText"');
    $form -> addRule('lastName', _THEFIELD.' '._LASTNAME.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('lastName', _THEFIELD.' '._LASTNAME.' '._HASINVALIDCHARACTERS.'. '._ONLYALLOWEDCHARACTERSTEXT, 'checkParameter', 'text');

    $form -> addElement('text', 'email', _EMAILADDRESS, 'class = "inputText "');
    $form -> addRule('email', _THEFIELD.' '._EMAILADDRESS.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('email', _THEFIELD.' '._EMAILADDRESS.' '._HASINVALIDCHARACTERS.'. '._ONLYALLOWEDCHARACTERSTEXT, 'email', null, 'client');
    $form -> addRule('email', _THEEMAIL.' &quot;'.($form -> exportValue('email')).'&quot; '._ALREADYEXISTS, 'checkNotExist', 'email');

    $user_profile = eF_getTableData("user_profile", "*", "active=1");    //Get admin-defined form fields for user registration

    foreach ($user_profile as $field) {                             //Add custom fields, defined in user_profile database table
        $userProfileFields[] = $field['name'];
        if ($field['type'] == 'select') {
            $options = unserialize($field['options']);
            $form -> addElement('select', $field['name'], $field['description'], array_combine($options, $options), 'class = " inputSelect"');
        } else {
            $form -> addElement('text', $field['name'], $field['description'], 'class = " inputText"');
        }
        if ($field['mandatory']) {
            $form -> addRule($field['name'], _THEFIELD.' '.$field['description'].' '._ISMANDATORY, 'required', null, 'client');
        }
        if ($field['default_value']) {
            $form -> setDefaults(array($field['name'] => $field['default_value']));
        }
    }

    $smarty -> assign("T_USER_PROFILE_FIELDS", $userProfileFields);

    $element =& $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputText" id = "comments"');
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
        $form -> setDefaults(array("login"      => $_GET['login'],
                                   "password"   => _LDAPACCOUNTPASSWORD,
                                   "passrepeat" => _LDAPACCOUNTPASSWORD,
                                   "email"      => $result[0]['mail'][0],
                                   "firstName"  => $first_name,
                                   "lastName"   => $last_name));
        $form -> freeze(array('login', 'password', 'passrepeat'));
    } elseif ($configuration['only_ldap']) {
        $message      = _ONLYLDAPREGISTRATIONPERMITTED;
        $message_type = 'failure';
        header("location:".basename($_SERVER['PHP_SELF'])."?message=".urlencode($message)."&message_type=$message_type");
    }

    if ($form -> isSubmitted()) {
        if ($form -> validate()) {
            if (isset($_SESSION['s_login'])) {                                          //A logged-in user wants to signup: Log him out first
				$user = EfrontUserFactory :: factory($_SESSION['s_login']);
				$user -> logout();
            }

            $values = $form -> exportValues();                                          //Get the form values

            $user_data = array("login"          => $values['login'],
                               "password"       => isset($_GET['ldap']) ? 'ldap' : $values['password'],
                               "name"           => $values['firstName'],
                               "surname"        => $values['lastName'],
                               "email"          => $values['email'],
                               "comments"       => $values['comments'],
                               "pending"		=> ($configuration['activation']) ? 0 : 1,
                               "active"			=> $configuration['activation'],
                               "languages_NAME" => $values['languages_NAME']);
            foreach ($user_profile as $field) {                                         //Get the custom fields values
                $user_data[$field['name']] = $values[$field['name']];
            }
            
            try {	                
	        	$newUser = EfrontUser :: createUser($user_data);
	        	
	        	eF_mailRegister($values['email'],   $user_data, $lessons_selection, $configuration['activation']);
                eF_mailRegister($GLOBALS['configuration']['system_email'], $user_data, $lessons_selection, $configuration['activation']);

                if ($configuration['activation'] == 0) {
	                if ($configuration['mail_activation'] == 1){
	                    $tmp            = eF_getTableData("users","timestamp","login='".$user_data['login']."'");
	                    $timestamp      = $tmp[0]["timestamp"];
	                    $subject_nonutf = _ACCOUNTACTIVATIONMAILSUBJECT;
	                    $subject        = mb_convert_encoding($subject_nonutf,'UTF-8');
	                    $body           = _DEARUSER." ".$user_data['login'].",\r\n\r\n"._ACCOUNTACTIVATIONMAILBODY."\r\n<a href=\"".G_SERVERNAME."index.php?account=".$user_data['login']."&key=".$timestamp."\">".G_SERVERNAME."index.php?account=".$user_data['login']."&key=".$timestamp."</a>\r\n";
	                    eF_mail($configuration['system_email'],$user_data['email'],$subject,$body,false,true);

	                    $message = _YOUWILLRECEIVEMAILFORACCOUNTACTIVATION;
	                    header('location:'.basename($_SERVER['PHP_SELF']).'?message='.$message.'&message_type=success');
	                } else{
	                    $message = _ADMINISTRATORWILLACTIVATEYOURACCOUNT;
	                    header('location:'.basename($_SERVER['PHP_SELF']).'?message='.$message.'&message_type=success');
	                }
                } else {
                    $message = _SUCCESSREGISTER;
                    header('location:'.basename($_SERVER['PHP_SELF']).'?message='.$message.'&message_type=success');
                }
	        	
	        } catch (Exception $e) {
	        	$messages[] = '&quot;'.$csvUser['login'].'&quot;: '.$e -> getMessage().' ('.$e -> getCode().')';
	        }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

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
if (isset($_GET['ctg']) && $_GET['ctg'] == 'contact') {                                                         //The user asked to display the contact form
    $smarty -> assign('T_CTG', 'contact');

    $form = new HTML_QuickForm("contact_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=contact", "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter

    $form -> addElement('text', 'email', _YOUREMAIL, 'class = "inputText"');
    $form -> addRule('email', _THEFIELD.' "'._EMAIL.'" '._ISMANDATORY, 'required');
    $form -> addRule('email', _INVALIDFIELDDATA, 'checkParameter', 'email');

    $form -> addElement('text', 'message_subject', _MESSAGESUBJECT, 'class = "inputText"');
    $form -> addRule('message_subject', _INVALIDFIELDDATA, 'checkParameter', 'text');

    $form -> addElement('textarea', 'message_body', _TEXT, 'class = "inputText" id = "contact"');
    $form -> addElement('submit', 'submit_contact', _SUBMIT, 'class = "flatButton"');

    if ($form -> isSubmitted()) {
        if ($form -> validate()) {
            $to      = $form -> exportValue("email");
            $subject = $form -> exportValue("message_subject");
            $body    = $form -> exportValue("message_body");
            if (eF_mail($GLOBALS['configuration']['system_email'], $to, $subject." ["._FROM.": ".$sender."]", $body,false,true)) {
                $message      = _SENDSUCCESS;
                $message_type = 'success';
                header('location:'.basename($_SERVER['PHP_SELF']).'?message='.$message.'&message_type='.$message_type);
            } else {
                $message      = _SENDFAILURE;
                $message_type = 'failure';
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

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
if (isset($_GET['ctg']) && $_GET['ctg'] == 'lesson_info') {                                                    //The user asked to display information on a lesson
    session_start();

    $loadScripts[] = 'drag-drop-folder-tree';
    try {
        if (isset($_GET['lessons_ID'])) {
            $lesson     = new EfrontLesson($_GET['lessons_ID']);
            $smarty -> assign("T_LESSON", $lesson);

            $lessonInformation = $lesson -> getInformation();            
            $content    = new EfrontContentTree($lesson);    
            if (sizeof($content -> tree) > 0) {
                $smarty -> assign("T_CONTENT_TREE", $content -> toHTML(false, 'dhtml_content_tree', array('noclick' => 1)));
            }
            $lessonInfo = new LearningObjectInformation(unserialize($lesson -> lesson['info']));
            $smarty -> assign("T_LESSON_INFO", $lessonInfo);
            $additionalInfo = $lesson -> getInformation();
            $smarty -> assign("T_ADDITIONAL_LESSON_INFO", $additionalInfo);           
        } else if ($_GET['courses_ID']) {
            $course     = new EfrontCourse($_GET['courses_ID']);
            $smarty -> assign("T_COURSE", $course);

            $lessons = $course -> getLessons();
            $smarty -> assign("T_COURSE_LESSONS", $lessons);
            
            $courseInfo = new LearningObjectInformation(unserialize($course -> course['info']));
            $smarty -> assign("T_COURSE_INFO", $courseInfo);
        }
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message      = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = failure;
    }
}

/* -------------------------------------------------------End of Lesson information part--------------------------------------------------------- */
if ($_GET['message']) {
    $message     .= $_GET['message'];
    $message_type = $_GET['message_type'];
}
$smarty -> assign('T_MESSAGE', $message);
$smarty -> assign('T_MESSAGE_TYPE', $message_type);

try {
    $logoFile = new EfrontFile($configuration['logo']);
    $smarty -> assign("T_LOGO", 'logo/'.$logoFile['physical_name']);

	// Get current dimensions
	list($width, $height) = getimagesize($logoFile['path']);
	if ($width > 120 || $height > 80) {
		// Get normalized dimensions
		list($newwidth, $newheight) = eF_getNormalizedDims($logoFile['path'], 120, 80);

		// The template will check if they are defined and normalize the picture only if needed
		$smarty -> assign("T_NEWWIDTH", $newwidth);
		$smarty -> assign("T_NEWHEIGHT", $newheight);
	}
} catch (EfrontFileException $e) {
    $smarty -> assign("T_LOGO", "logo.png");
}

$smarty -> assign('T_LDAPSUPPORT', $configuration['activate_ldap']);
$smarty -> assign('T_ONLY_LDAP', $configuration['only_ldap']);
$smarty -> assign('T_EXTERNALLYSIGNUP', $configuration['signup']);

if (strlen($configuration['css']) > 0 && is_file(G_CUSTOMCSSPATH.$configuration['css'])) {                //Load custom css, if one exists
    $smarty -> assign("T_CUSTOM_CSS", $configuration['css']);
}

$debug_timeBeforeSmarty = microtime(true) - $debug_TimeStart;

$loadScripts[] = 'scriptaculous/prototype';
$loadScripts[] = 'scriptaculous/effects';
$loadScripts[] = 'EfrontScripts';


$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($loadScripts));                    //array_unique, so it doesn't send duplicate entries
$smarty -> assign("T_NEWS", eF_getTableData("news", "*", "lessons_ID=0", "timestamp desc"));
$smarty -> assign("T_ONLINE_USERS_LIST", EfrontUser :: getUsersOnline());

$smarty -> display('index.tpl');







$debug_timeAfterSmarty = microtime(true) - $debug_TimeStart;

$debug_TotalTime = microtime(true) - $debug_TimeStart;

if (G_DEBUG) {
    echo "
    <div onclick = 'this.style.display=\"none\"' style = 'position:absolute;top:0px;right:0px;background-color:lightblue;border:1px solid black' >
    <table>
        <tr><th colspan = '100%'>Benchmarking info (click to remove)</th></tr>
        <tr><td>Initialization time: </td><td>".round($debug_InitTime, 5)." sec</td></tr>
        <tr><td>Time up to smarty: </td><td>".round($debug_timeBeforeSmarty, 5)." sec</td></tr>
        <tr><td>Database time (".$databaseQueries." q): </td><td>".($databaseTime > 100 ? 0 : round($databaseTime, 5))." sec</td></tr>
        <tr><td>Smarty overhead: </td><td>".round($debug_timeAfterSmarty - $debug_timeBeforeSmarty, 5)." sec</td></tr>
        <tr><td colspan = \"2\" class = \"horizontalSeparator\"></td></tr>
        <tr><td>Total execution time: </td><td>".round($debug_TotalTime, 5)." sec</td></tr>
        <tr><td>Execution time for this script is: </td><td>".round($debug_TotalTime - $debug_InitTime - ($debug_timeAfterSmarty - $debug_timeBeforeSmarty), 5)." sec</td></tr>
    </table>
    </div>";
}

?>
