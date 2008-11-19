<?php
/**
 * Administrator main page
 *
 * This page performs all administrative functions. There are 7 main categories in this page,
 * denoted by corresponding ctgs: Control panel, Users, Lessons, Directions, Course, User Types Statistics
 * and Emails.

 * @package eFront
 * @version 3.0
 */
$debug_TimeStart = microtime(true);     //Debugging timer - initialization

session_cache_limiter('none');          //Initialize session
session_start();

$path = "../libraries/";                //Define default path

/** The configuration file.*/
require_once $path."configuration.php";
$debug_InitTime = microtime(true) - $debug_TimeStart;       //Debugging timer - time spent on file inclusion

//Set headers in order to eliminate browser cache (especially IE's)
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
//If the page is shown as a popup, make sure it remains in such mode
if (isset($_GET['popup']) || isset($_POST['popup']) || (strpos(strtolower($_SERVER['HTTP_REFERER']), 'popup') !== false && !strpos(strtolower($_SERVER['HTTP_REFERER']), 'evaluation'))) {
    output_add_rewrite_var('popup', 1);
    $smarty -> assign("T_POPUP_MODE", true);
    $popup = 1;
}

$message = '';$message_type = '';                            //Initialize messages, because if register_globals is turned on, some messages will be displayed twice
$loadScripts = array('EfrontScripts', 'scriptaculous/prototype', 'scriptaculous/scriptaculous', 'scriptaculous/effects');

/*Check the user type. If the user is not valid or not an administrator, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
        $currentUser -> applyRoleOptions($currentUser -> user['user_types_ID']);                //Initialize user's role options for this lesson
        $smarty -> assign("T_CURRENT_USER", $currentUser);
        if ($currentUser -> user['user_type'] != 'administrator') {
            echo "<script>parent.location = 'index.php?message=".urlencode(_YOUCANNOTACCESSTHISPAGE)."&message_type=failure'</script>";        //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
            //header("location:index.php?message=".urlencode(_YOUCANNOTACCESSTHISPAGE)."&message_type=failure");
            exit;
        }
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        echo "<script>parent.location = 'index.php?message=".urlencode($message)."&message_type=failure'</script>";        //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
        //header("location:index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    setcookie('c_request', $_SERVER['REQUEST_URI'], time() + 300);
    echo "<script>parent.location = 'index.php?message=".urlencode(_RESOURCEREQUESTEDREQUIRESLOGIN)."&message_type=failure'</script>";        //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
    //header("location:index.php?message=".urlencode(_RESOURCEREQUESTEDREQUIRESLOGIN)."&message_type=failure");
    exit;
}
if ($_COOKIE['c_request']) {
    setcookie('c_request', '', time() - 86400);
    header("location:".$_COOKIE['c_request']);
}
// Share the hcd value with smarty
$module_hcd_interface = MODULE_HCD_INTERFACE;
$smarty -> assign("T_MODULE_HCD_INTERFACE", $module_hcd_interface);

///MODULE1
$loadedModules = $currentUser -> getModules();

$module_css_array = array();
$module_js_array = array();

// Include module languages
foreach ($loadedModules as $module) {
    // The $setLanguage variable is defined in globals.php
    $mod_lang_file = $module -> getLanguageFile($setLanguage);
    if (is_file ($mod_lang_file)) {
        require_once $mod_lang_file;
    }

    // Get module css
    if($mod_css_file = $module -> getModuleCSS()) {
        if (is_file ($mod_css_file)) {

            // Get the relative path
            if ($position = strpos($mod_css_file, "modules")) {
                $mod_css_file = substr($mod_css_file, $position);
            }
            $module_css_array[] = $mod_css_file;
        }
    }

    // Get module js
    if($mod_js_file = $module -> getModuleJS()) {
        if (is_file($mod_js_file)) {
            // Get the relative path
            if ($position = strpos($mod_js_file, "modules")) {
                $mod_js_file = substr($mod_js_file, $position);
            }

            $module_js_array[] = $mod_js_file;
        }
    }
}


/*Added Session variable for search results*/
$_SESSION['referer'] = $_SERVER['REQUEST_URI'];


/*These are the possible ctg we can have. */
$possible_ctgs = array('control_panel', 'users', 'lessons', 'directions', 'courses', 'calendar','module','tests',
                       'user_types', 'user_groups', 'statistics', 'cms', 'languages', 'style', 'search_courses', 'tests');
if (sizeof($module_ctgs) > 0) {
    $possible_ctgs = array_merge($possible_ctgs, array_keys($module_ctgs));
}

/** MODULE HCD: allow the ctg=module_hcd **/
if (!$module_hcd_interface) {
    !isset($_GET['ctg']) || !in_array($_GET['ctg'], $possible_ctgs)  ? $ctg = "control_panel" : $ctg = $_GET['ctg'];    //The default ctg is 'control_panel'
} else {
    !isset($_GET['ctg']) || (!in_array($_GET['ctg'], $possible_ctgs) && ($_GET['ctg'] != "module_hcd")) ? $ctg = "control_panel" : $ctg = $_GET['ctg'];    //The default ctg is 'control_panel'
}

$smarty -> assign("T_CTG", $ctg);       //As soon as we derive the current ctg, assign it to smarty.
$smarty -> assign("T_OP", isset($_GET['op']) ? $_GET['op'] : false);

/*
 Control panel is the first page that the administrator sees, and contains links to most of the available functions
 At the control panel main page, you will find 5 sections:
 - A Settings list, with icons representing available functions
 - System announcements
 - Recent personal messages
 - The list of new users
 - The list of new lesson registrations
 */
if ($ctg == 'control_panel') {
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/dragdrop';
    /*
     Include the module that is used to perform the searches
     */
    if (isset($_GET['op']) && $_GET['op'] == 'search') {
        /**Functions to perform searches*/
        require_once "module_search.php";
    }
    /*
     Module administration
     */
///MODULES4
    elseif (isset($_GET['op']) && $_GET['op'] == 'modules') {
        if (isset($currentUser -> coreAccess['modules']) && $currentUser -> coreAccess['modules'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }

        if (!isset($currentUser -> coreAccess['modules']) || $currentUser -> coreAccess['modules'] == 'change') {
            if (isset($_GET['delete']) && eF_checkParameter($_GET['delete'], 'filename')) {
                $smarty -> assign("T_REFRESH_SIDE", true);
                $lesson_options = eF_getTableData("lessons", "options, id");
                foreach ($lesson_options as $value) {
                    if ($value['options']) {
                        $options = unserialize($value['options']);
                        if (in_array($_GET['delete'], array_keys($options))) {//pr($options);
                            unset($options[$_GET['delete']]);
                            eF_updateTableData("lessons", array('options' => serialize($options)), "id=".$value['id']);
                        }
                    }
                }

                $className = $_GET['delete'];
                $module_folder_position = eF_getTableData("modules", "position", "className='". $className."'");

                $folder = $module_folder_position[0]['position'];
                require_once G_MODULESPATH.$folder."/".$className.".class.php";

                if (class_exists($className)) {
                    $module = new $className("administrator.php?ctg=module&op=".$className, $folder);
                    $module -> onUninstall();
                } else {
                    $message      = '"'.$className .'" '. _MODULECLASSNOTEXISTSIN . ' ' .G_MODULESPATH.$folder.'/'.$className.'.class.php';
                    $message_type = 'failure';
                }

                // PROBLEM: if the folder is open and cannot be deleted then the module cannot be reinstalled
                eF_deleteFolder(G_MODULESPATH.$folder.'/');
                eF_deleteTableData("modules", "className='".$className."'");

    /*
                $tables = $db -> GetCol('show tables');
                foreach ($tables as $table) {
                    if (preg_match('/('.$_GET['delete'].'.*)/', $table, $matches)) {
                        eF_executeNew('drop table '.$matches[1]);
                    }
                }
    */

                $message      = _SUCCESFULLYDELETEDMODULE;
                $message_type = 'success';

            } elseif(isset($_GET['activate']) && eF_checkParameter($_GET['activate'], 'filename')) {
                eF_updateTableData("modules", array("active" => 1), "className = '".$_GET['activate']."'");
                exit;
            } elseif(isset($_GET['deactivate']) && eF_checkParameter($_GET['deactivate'], 'filename')) {
                eF_updateTableData("modules", array("active" => 0), "className = '".$_GET['deactivate']."'");
                exit;
            }
        } else {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $modulesList = eF_getTableData("modules", "*");

        // Check for errors in modules
        foreach ($modulesList as $key => $module) {

            $folder = $module['position'];
            $className = $module['className'];
            $permissions = explode(",",$module['permissions']);

            // Check if module folder exists
            $modulesList[$key]['folder_exists'] = is_dir(G_MODULESPATH.$folder);
            if (!$modulesList[$key]['folder_exists']) {
                $modulesList[$key]['errors'] = _THISFOLDERDOESNOTEXIT . ": " . G_MODULESPATH . $folder;
            } else {
                // Check if module class exists
                $modulesList[$key]['class_exists'] = is_file(G_MODULESPATH. $folder.'/'.$className. ".class.php");
                if (!$modulesList[$key]['class_exists']) {
                    $modulesList[$key]['errors'] =  _NOMODULECLASSFOUND . ' "'. $className .'" : '.G_MODULESPATH. $folder .'/'.$className. ".class.php";
                } else {
                    // The module class can be instantiated if the module is not to be upgraded now
                    if ($_GET['upgrade'] != $className) {
                        if (!isset($loadedModules[$className])) {
                            // Include module definition file if it hasn't been included yet
                            require_once G_MODULESPATH.$folder."/".$className.".class.php";
                        }

                        if (class_exists($className)) {
                            $moduleInstance = new $className($user_type.".php?ctg=module&op=".$className, $folder);
                            if (!$moduleInstance -> diagnose($error)) {
                                $modulesList[$key]['errors'] = $error;
                            }
                        } else {
                            $message      = '"'.$className .'" '. _MODULECLASSNOTEXISTSIN . ' ' .G_MODULESPATH.$folder.'/'.$className.'.class.php';
                            $message_type = 'failure';
                        }
                    }
                }
            }
        }
//pr($modulesList);
        $smarty -> assign("T_MODULES", $modulesList);

        $upload_form = new HTML_QuickForm("upload_file_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=modules', "", null, true);
        $upload_form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
        $upload_form -> addElement('file', 'file_upload[0]', null, 'class = "inputText"');
        $upload_form -> addElement('submit', 'submit_upload_file', _UPLOAD, 'class = "flatButton"');
        if ($upload_form -> isSubmitted() && $upload_form -> validate()) {
            list($ok, $upload_messages, $upload_messages_type, $filename) = eF_handleUploads("file_upload", G_MODULESPATH);

            if(isset($_GET['upgrade'])) {
                $prev_module_version = eF_getTableData("modules", "position", "className = '".$_GET['upgrade']."'");
                $prev_module_folder = $prev_module_version[0]['position'];

                // The name of the temp folder to extract the new version of the module
                $module_folder = $prev_module_folder; //basename($filename[0], '.zip') . time();
                $module_position = $prev_module_folder;//basename($filename[0], '.zip');

            } else {
                $module_folder = basename($filename[0], '.zip');
                $module_position = $module_folder;
            }


            if (!$ok) {
                $message      = $upload_messages[0];
                $message_type = $upload_messages_type[0];
            } elseif (is_dir(G_MODULESPATH.$module_folder) && !isset($_GET['upgrade'])) {
                $message      = _FOLDERWITHMODULENAMEEXISTSIN . G_MODULESPATH;
                $message_type = 'failure';
            } else {
                $zip = new ZipArchive;
                if ($zip -> open($filename[0]) === TRUE) {
                    $zip -> extractTo(G_MODULESPATH.$module_folder);
                    $zip -> close();

                    if (is_file(G_MODULESPATH.$module_folder.'/module.xml')) {

                        $xml         = simplexml_load_file(G_MODULESPATH.$module_folder.'/module.xml');

                        $className = (string)$xml -> className;
                        $className = str_replace(" ", "", $className);
                        $database_file = (string)$xml -> database;

                        if (is_file(G_MODULESPATH.$module_folder.'/'.$className. ".class.php")) {
                            $module_exists = 0;

                            // Do not check for module existence if the module is to be upgraded
                            if (!isset($_GET['upgrade'])) {
                                foreach ($modulesList as $module) {
                                    if ($module['className'] == $className) {
                                        $module_exists = 1;
                                    }
                                }
                            }

                            if ($module_exists == 0) {

                                require_once G_MODULESPATH.$module_folder."/".$className.".class.php";

                                if (class_exists($className)) {
                                    $module = new $className("administrator.php?ctg=module&op=".$className, $className);

                                    // Check whether the roles defined are acceptable
                                    $roles = $module -> getPermittedRoles();
                                    $roles_failure = 0;
                                    if (sizeof($roles) == 0) {
                                        $message = _NOMODULEPERMITTEDROLESDEFINED;
                                        $message_type = 'failure';
                                        $roles_failure = 1;
                                    } else {
                                        foreach ($roles as $role) {
                                            if ($role != 'administrator' && $role != 'student' && $role != 'professor') {
                                                $message = _PERMITTEDROLESMODULEERROR;
                                                $message_type = 'failure';
                                                $roles_failure = 1;
                                            }
                                        }
                                    }

                                    if ($roles_failure) {
                                        eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                                    } else {

                                        $fields      = array('className'   => $className,
                                                             'db_file'     => $database_file,
                                                             'name'        => $className,
                                                             'active'      => 1,
                                                             'title'       => ((string)$xml -> title)?(string)$xml -> title:" ",
                                                             'author'      => (string)$xml -> author,
                                                             'version'     => (string)$xml -> version,
                                                             'description' => (string)$xml -> description,
                                                             'position'    => $module_position,
                                                             'permissions' => implode(",", $module -> getPermittedRoles()));


                                        if (!isset($_GET['upgrade'])) {
                                            // Install module database
                                            if ($module -> onInstall()) {
                                                if (eF_insertTableData("modules", $fields)) {
                                                    $message      = _MODULESUCCESFULLYINSTALLED;
                                                    $message_type = 'success';
                                                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=modules&message=".urlencode($message)."&message_type=".$message_type."&refresh_side=1");
                                                } else {
                                                    $module -> onUninstall();
                                                    $message      = _PROBLEMINSERTINGPARSEDXMLVALUESORMODULEEXISTS;
                                                    $message_type = 'failure';
                                                    eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                                                }
                                            } else {
                                                $message      = _MODULEDBERRORONINSTALL;
                                                $message_type = 'failure';
                                                eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                                            }
                                        } else {

                                            // If the module is to be installed to a different than the existing folder that
                                            // already exists (like the directory name of another module) then the upgrade should
                                            // be aborted

                                            // If everything went ok, then upgrade the module
                                            if ($module -> onUpgrade()) {

                                                // If the upgrade is successful, then update the modules table
                                                if (eF_updateTableData("modules", $fields, "className ='".$_GET['upgrade']."'")) {

                                                    // Delete the existing module folder
                                                    $message      = _MODULESUCCESFULLYUPGRADED;
                                                    $message_type = 'success';
                                                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=modules&message=".urlencode($message)."&message_type=".$message_type);
                                                } else {
                                                    $message      = _PROBLEMINSERTINGPARSEDXMLVALUESORMODULEEXISTS;
                                                    $message_type = 'failure';
                                                }

                                            } else {
                                                $message      = _MODULEDBERRORONUPGRADECHECKUPGRADEFUNCTION;
                                                $message_type = 'failure';
                                            }
                                        }
                                    }
                                } else {
                                    $message      = '"'.$className .'" '. _MODULECLASSNOTEXISTSIN . ' ' .G_MODULESPATH.$module_folder.'/'.$className.'.class.php';
                                    $message_type = 'failure';
                                    eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                                }
                            } else {
                                $message      = '"'.$className .'": '. _MODULEISALREADYINSTALLED;
                                $message_type = 'failure';
                                eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                            }
                        } else {
                            $message      = _NOMODULECLASSFOUND . ' "'. $className .'" : '.G_MODULESPATH.$module_folder;
                            $message_type = 'failure';
                            eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                        }
                    } else if (!is_dir(G_MODULESPATH.$module_folder)) {
                        $message      = _THISFOLDERDOESNOTEXIT.': '.G_MODULESPATH.$module_folder;
                        $message_type = 'failure';
                    } else {
                        $message      = _DESCRIPTIONFILECOULDNOTBEFOUND;
                        $message_type = 'failure';
                        eF_deleteFolder(G_MODULESPATH.$module_folder.'/');
                    }
                } else {
                    $message      = _COULDNOTOPENZIPFILE;
                    $message_type = 'failure';
                }
            }

        }
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $upload_form -> accept($renderer);

        $smarty -> assign('T_UPLOAD_FILE_FORM', $renderer -> toArray());
        //$db -> debug = true;
        //eF_insertTableData("modules", array("name" => "test", "active" => 1));
        //pr($modules);
    }
    /*
     Paypal administration
     */
    elseif (isset($_GET['op']) && $_GET['op'] == 'paypal') {
        if (isset($currentUser -> coreAccess['paypal']) && $currentUser -> coreAccess['paypal'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        if (!MODULE_PAYPAL) {
            header ('location:'.basename($_SERVER['PHP_SELF']));
        }
        $login = $currentUser->user['login'];

        $form = new HTML_QuickForm("config_form", "post", $_SERVER['REQUEST_URI'], "", null, true);
        $form -> addElement('text', 'paypalbusiness', null, 'class = "inputText"');
        $form -> addElement('submit', 'submit_config',_SAVECHANGES, 'class = "flatButton"');
        $form->addRule('paypalbusiness', _PAYPALBUSINESSMAILPLZ, 'required', null, 'client');

        $config_data = eF_getTableData("paypal_configuration", "*", "");
        if(sizeof($config_data)>0){
            $form -> setDefaults(array("paypalbusiness" => $config_data[0]['paypalbusiness'],
                                       "mailstudents"   => $config_data[0]['mailstudents'],
                                       "mailprofessors" => $config_data[0]['mailprofessors'],
                                       "mailadmins"     => $config_data[0]['mailadmins']));
        }

        if ($form -> isSubmitted() && $form -> validate()) {
            $fields = array('paypalbusiness'    => $form -> exportValue("paypalbusiness"),
                            'mailstudents'      => $form -> exportValue("mailstudents") ? 1 : 0,
                            'mailprofessors'    => $form -> exportValue("mailprofessors") ? 1 : 0,
                            'mailadmins'        => $form -> exportValue("mailadmins") ? 1 : 0);
            if(sizeof($config_data)>0){
                if(eF_updateTableData("paypal_configuration", $fields, "1=1")){
                    $message      = _UPDATESUCCESFULLYMADE;
                    $message_type = 'success';
                }else{
                    $message      = _SOMEPROBLEMOCCURED;
                    $message_type = 'failure';
                }
            }else{
                if(eF_insertTableData("paypal_configuration", $fields)){
                    $message      = _UPDATESUCCESFULLYMADE;
                    $message_type = 'success';
                }else{
                    $message      = _SOMEPROBLEMOCCURED;
                    $message_type = 'failure';
                }
            }
            header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=paypal&message='.$message.'&message_type='.$message_type);
        }
        $paypal_data_s = eF_getTableData("paypal_data", "*", "status='completed' ORDER BY timestamp_finish DESC");
        $paypal_data_ns = eF_getTableData("paypal_data", "*", "status!='completed' ORDER BY timestamp DESC");
        $smarty -> assign('T_PAYPALDATA_S', $paypal_data_s);
        $smarty -> assign('T_PAYPALDATA_NS', $paypal_data_ns);

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> accept($renderer);
        $smarty -> assign('T_CONFIG_FORM_DEFAULT', $renderer -> toArray());
    }
    /*
     Perform an environmental status check
     */
    elseif (isset($_GET['op']) && $_GET['op'] == 'versionkey') {
        if (isset($currentUser -> coreAccess['version_key']) && $currentUser -> coreAccess['version_key'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }

        $form = new HTML_QuickForm("config_version", "post", $_SERVER['REQUEST_URI'], "", null, true);
        $form -> addElement("text", "version_key",      _VERSIONKEY,    'class = "inputText" style="width: 55em;"');
        $form -> addRule('version_key',                 _THEFIELD.' "'._VERSIONKEY.'" '._ISMANDATORY, 'required', null, 'client');
        $config_data = eF_getTableData("configuration", "*", "name='version_key'");

        if (sizeof($config_data) > 0) {
            $form -> setDefaults(array("version_key" => $config_data[0]['value']));
            $data = eF_checkVersionKey($config_data[0]['value']);
            $data['type'] = $VERSIONTYPES[$data['type']];
            $smarty -> assign('T_VERSIONKEY_DEFAULT_MSG', $data);
        }

        if (isset($currentUser -> coreAccess['version_key']) && $currentUser -> coreAccess['version_key'] != 'change') {
            $form -> freeze();
        } else {
            $form -> addElement('submit', 'submit_config',  _SAVECHANGES,   'class = "flatButton"');
            if ($form -> isSubmitted() && $form -> validate()) {
                $key = $form -> exportValue("version_key");
                if (mb_strlen($key) == '96') {
                    $results        = eF_checkVersionKey($key);
                    if (sizeof($results) == '5') {
                        EfrontConfiguration :: setValue('version_key', $key);
                        EfrontConfiguration :: setValue('version_users', $results['users']);
                        EfrontConfiguration :: setValue('version_type', $results['type']);
                        EfrontConfiguration :: setValue('version_paypal', $results['paypal']);
                        EfrontConfiguration :: setValue('version_hcd', $results['hcd']);
                        $message        = _UPDATESUCCESFULLYMADE;
                        $message_type   = 'success';
                        //$smarty -> assign("T_REFRESH_SIDE", true);
                    } else {
                        $message        = _SOMEPROBLEMOCCURED;
                        $message_type   = 'failure';
                    }
                } else {
                    $message        = _SOMEPROBLEMOCCURED;
                    $message_type   = 'failure';
                }
                unset($_SESSION['s_version_type']);
                header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=versionkey&message='.$message.'&message_type='.$message_type."&refresh_side=1");
            }
        }
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form   -> accept($renderer);
        $smarty -> assign('T_VERSIONKEY_DEFAULT', $renderer -> toArray());
    }

    /*
     Perform an environmental status check
     */
    elseif (isset($_GET['op']) && $_GET['op'] == 'maintenance') {
        if (isset($currentUser -> coreAccess['maintenance']) && $currentUser -> coreAccess['maintenance'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        /**Functions to perform status check*/
        require_once "check_status.php";

        //Lock down operations
        if (!isset($currentUser -> coreAccess['maintenance']) || $currentUser -> coreAccess['maintenance'] == 'change') {
            $lockdown_form = new HTML_QuickForm("lockdown_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=lock_down", "", null, true);  //Build the form
            $lockdown_form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                                                   //Register our custom input check function

            $lockdown_form -> addElement('checkbox', 'display_message', null, null, 'class = "inputCheckBox"');
            $lockdown_form -> addElement('checkbox', 'logout_users', null, null, 'class = "inputCheckBox"');
            $lockdown_form -> addElement('checkbox', 'set_announcement', null, null, 'class = "inputCheckBox"');
            $options = array(
                'format'         => 'Y m d - H:i:s',
                'minYear'        => date("Y"),
                'maxYear'        => date('Y') + 2,
            );
            $lockdown_form -> addElement('date', 'from', null, $options);
            $lockdown_form -> addElement('date', 'to', null, $options);
            $lockdown_form -> setDefaults(array("from"             => time(),
                                                "to"               => time() + 7200,                        //2-hour locking by default
                                                "display_message"  => true,
                                                "logout_users"     => true,
                                                "set_announcement" => true));

            $lockdown_form -> addElement('submit', 'submit_lockdown', _LOCKDOWN, 'class = "flatButton"');
            //Check here, whether the system is already locked, and present unlock button
            if ($lockdown_form -> isSubmitted() && $lockdown_form -> validate()) {                                                              //If the form is submitted and validated
                $values = $lockdown_form -> exportValues();
                //pr($values);
                $from = mktime($values['from']['H'], $values['from']['i'], $values['from']['s'], $values['from']['m'], $values['from']['d'], $values['from']['Y']);
                $to   = mktime($values['to']['H'],   $values['to']['i'],   $values['to']['s'],   $values['to']['m'],   $values['to']['d'],   $values['to']['Y']);

                if ($from <= time()) {

                }
            }

            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer
            $lockdown_form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

            $smarty -> assign('T_LOCKDOWN_FORM', $renderer -> toArray());                     //Assign the form to the template

            //User check
            $users     = eF_getTableDataFlat("users", "login");
            $users_dir = eF_getDirContents(G_ROOTPATH.'upload/', '', false, false);
            if (($offset = array_search('.htaccess', $users_dir)) !== false) {                  //Remove .htaccess from files list
                unset($users_dir[$offset]);
            }
            $orphan_user_folders = array_diff($users_dir, $users['login']);
            $orphan_users        = array_diff($users['login'], $users_dir);

            $smarty -> assign("T_ORPHAN_USERS", implode(", ", $orphan_users));
            $smarty -> assign("T_ORPHAN_USER_FOLDERS", implode(", ", $orphan_user_folders));

            //Lessons check
            $lessons     = eF_getTableDataFlat("lessons", "id, name");
            $lessons     = array_combine($lessons['name'], $lessons['id']);
            $lessons_dir = eF_getDirContents(G_ROOTPATH.'www/content/lessons/', '', false, false);
            foreach ($lessons_dir as $key => $dir) {                                                    //Remove non-integer lessons from list (such as scorm_uploaded_files);
                if (!preg_match("/^\d+$/", $dir)) {
                    unset($lessons_dir[$key]);
                }
            }
            $orphan_lesson_folders = array_diff($lessons_dir, $lessons);
            $orphan_lessons        = array_diff($lessons, $lessons_dir);
            $smarty -> assign("T_ORPHAN_LESSONS", implode(", ", array_keys($orphan_lessons)));
            $smarty -> assign("T_ORPHAN_LESSON_FOLDERS", implode(", ", $orphan_lesson_folders));

            if (isset($_GET['cleanup']) && ($_GET['cleanup'] == 'orphan_user_folders' || $_GET['cleanup'] == 'all')) {
                foreach ($orphan_user_folders as $folder) {
                    if (!eF_deleteFolder(G_ROOTPATH.'upload/'.$folder.'/')) {
                        $errors[] = $folder;
                    }
                }
                if ($_GET['cleanup'] != 'all') {
                    if (!isset($errors)) {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._SUCCESFULLYCLEANEDUPFOLDERS.'&message_type=success');
                    } else {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._THEFOLLOWINGFOLDERSCOULDNOTBEDELETED.': '.implode(", ", $errors).'&message_type=failure');
                    }
                }
            }
            if (isset($_GET['cleanup']) && ($_GET['cleanup'] == 'users_without_folders' || $_GET['cleanup'] == 'all')) {
                foreach ($orphan_users as $login) {
                    if (!eF_deleteUser($login)) {
                        $errors[] = $login;
                    }
                }
                if ($_GET['cleanup'] != 'all') {
                    if (!isset($errors)) {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._SUCCESFULLYCLEANEDUPUSERS.'&message_type=success');
                    } else {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._THEFOLLOWINGUSERSCOULDNOTBEDELETED.': '.implode(", ", $errors).'&message_type=failure');
                    }
                }
            }
            if (isset($_GET['cleanup']) && ($_GET['cleanup'] == 'orphan_lesson_folders' || $_GET['cleanup'] == 'all')) {
                foreach ($orphan_lesson_folders as $folder) {
                    if (!eF_deleteFolder(G_ROOTPATH.'www/content/lessons/'.$folder.'/')) {
                        $errors[] = $folder;
                    }
                }
                if ($_GET['cleanup'] != 'all') {
                    if (!isset($errors)) {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._SUCCESFULLYCLEANEDUPFOLDERS.'&message_type=success');
                    } else {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._THEFOLLOWINGFOLDERSCOULDNOTBEDELETED.': '.implode(", ", $errors).'&message_type=failure');
                    }
                }
            }
            if (isset($_GET['cleanup']) && ($_GET['cleanup'] == 'lessons_without_folders' || $_GET['cleanup'] == 'all')) {
                foreach ($orphan_lessons as $lesson_id) {
                    if (!EfrontLesson::deleteLesson($lesson_id)){
                        $errors[] = $folder;
                    }
                }
                if ($_GET['cleanup'] != 'all') {
                    if (!isset($errors)) {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._SUCCESFULLYCLEANEDUPLESSONS.'&message_type=success');
                    } else {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._THEFOLLOWINGLESSONSCOULDNOTBEDELETED.': '.implode(", ", $errors).'&message_type=failure');
                    }
                }
            }
            if (isset($_GET['create'])  && $_GET['create']  == 'user_folders') {
                foreach ($orphan_users as $login) {
                    if (!mkdir(G_ROOTPATH.'upload/'.$login)                               ||
                    !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments')        ||
                    !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments/Drafts') ||
                    !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments/Sent')   ||
                    !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments/Incoming'))
                    {
                        $errors[] = $login;
                    }
                }
                if (!isset($errors)) {
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._SUCCESFULLYCREATEDUSERFOLDERS.'&message_type=success');
                } else {
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._THEFOLLOWINGUSERFOLDERSCOULDNOTBECREATED.': '.implode(", ", $errors).'&message_type=failure');
                }
            }
            if (isset($_GET['create'])  && $_GET['create']  == 'lesson_folders') {
                foreach ($orphan_lessons as $lesson_name => $lesson_id) {
                    if (!mkdir(G_ROOTPATH.'www/content/lessons/'.$lesson_id)) {
                        $errors[] = $lesson_name;
                    }
                }
                if (!isset($errors)) {
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._SUCCESFULLYCREATEDLESSONFOLDERS.'&message_type=success');
                } else {
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=maintenance&tab=cleanup&message="._THEFOLLOWINGLESSONFOLDERSCOULDNOTBECREATED.': '.implode(", ", $errors).'&message_type=failure');
                }
            }

            //Recreate search table
            if (isset($_GET['reindex']) && $_GET['ajax'] == 1) {
                try {
                    EfrontSearch :: reBuiltIndex();
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
                //header("location: administrator.php?ctg=control_panel&message=".urlencode(_SEARCHTABLERECREATED)."&message_type=success");
            }
        }
    }
    /*
     This part is used to set configuration options, concerning system, ldap and smtp operations
     */
    else if (isset($_GET['op']) && $_GET['op'] == 'system_config') {
        if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $system_form = new Html_QuickForm("system_variables", "post", basename(basename($_SERVER['PHP_SELF']))."?ctg=control_panel&op=system_config&tab=vars", "", null, true);
        $system_form -> addElement("advcheckbox", "signup",      _EXTERNALLYSIGNUP,        null, 'class = "inputCheckBox"', array(0, 1));
        $system_form -> addElement("advcheckbox", "activation",  _AUTOMATICUSERACTIVATION, null, 'id = "activation" onclick = "if (this.checked) {$(\'mail_activation\').checked=false}" class = "inputCheckBox"', array(0, 1));
        $system_form -> addElement("advcheckbox", "mail_activation",  _MAILUSERACTIVATION, null, 'id = "mail_activation" onclick = "if (this.checked) {$(\'activation\').checked=false}" class = "inputCheckBox"', array(0, 1));
        $system_form -> addElement("advcheckbox", "onelanguage", _ONLYONELANGUAGE,         null, 'class = "inputCheckBox"', array(0, 1));
        $system_form -> addElement("advcheckbox", "show_footer", _SHOWFOOTER,              null, 'class = "inputCheckBox"', array(0, 1));
        $system_form -> addElement("advcheckbox", "api",         _ENABLEDAPI,              null, 'class = "inputCheckBox"', array(0, 1));
		$system_form -> addElement("advcheckbox", "math_content",_ENABLEMATHCONTENT,       null, 'class = "inputCheckBox"', array(0, 1));

        $system_form -> addElement("text", "system_email",    _SYSTEMEMAIL,          'class = "inputText"');
        $system_form -> addElement("text", "ip_white_list",   _ALLOWEDIPS,           'class = "inputText"');
        $system_form -> addElement("text", "ip_black_list",   _DISALLOWEDIPS,        'class = "inputText"');
        $system_form -> addElement("text", "file_white_list", _ALLOWEDEXTENSIONS,    'class = "inputText"');
        $system_form -> addElement("text", "file_black_list", _DISALLOWEDEXTENSIONS, 'class = "inputText"');
        
        $system_form -> addElement("text", "sidebar_width", _SIDEBARWIDTH, 'class = "inputText"');
        $system_form -> addElement("text", "max_file_size",   _MAXFILESIZE,          'class = "inputText"');
        $system_form -> addElement("text", "site_name",       _SITENAME,             'class = "inputText"');
        $system_form -> addElement("text", "site_moto",       _SITEMOTO,             'class = "inputText"');
        $system_form -> addElement("text", "logout_redirect", _LOGOUTREDIRECT,       'class = "inputText"');

        $system_form -> addElement("select", "default_language",  _DEFAULTLANGUAGE, EfrontSystem :: getLanguages(true), 'class = "inputSelect"');
        $system_form -> addElement("select", "lessons_directory", _VIEWDIRECTORY, array(_NO, _YES), 'class = "inputSelect"');
        //$system_form -> addElement("select", "interface_view",    _INTERFACEVIEW, array("1" => _INTERFACEVIEWSIMPLE, "2" => _INTERFACEVIEWEXTENDED), 'class = "inputSelect"');

        $defaultEncodings = array_combine(mb_list_encodings(), mb_list_encodings());
        $encodings['UTF7-IMAP'] = 'UTF7-IMAP';
        if (in_array(_CHARSET, mb_list_encodings())) {
             $encodings[_CHARSET] = _CHARSET;
        }
        $encodings['UTF8'] = 'UTF8';
        $encodings = array_merge($encodings, $defaultEncodings);
        $system_form -> addElement("select", "file_encoding", _TRANSLATEFILESYSTEM, $encodings, 'class = "inputSelect"');

        if (MODULE_PAYPAL) {
            if (is_file('ipn.php')) {
                $system_form -> addElement("advcheckbox", "paypal", _PAYPALUSE, null, 'class = "inputCheckBox"', array(0, 1));
            }
        }

        $system_form -> addRule('system_email', _THEFIELD.' "'._SYSTEMEMAIL.'" '._ISMANDATORY, 'required', null, 'client');
        $system_form -> addRule('system_email', _INVALIDFIELDDATAFORFIELD.': "'._SYSTEMEMAIL.'"' , 'email', null, 'client');
        $system_form -> addRule('max_file_size', _THEFIELD.' "'._MAXFILESIZE.'" '._ISMANDATORY, 'required', null, 'client');
        $system_form -> addRule('max_file_size', _INVALIDFIELDDATAFORFIELD.': "'._MAXFILESIZE.'"', 'numeric', null, 'client');
        
        $system_form -> addRule('sidebar_width', _SIDEBARVALUESMUSTBEBETWEEN. ' 175  ' . _AND . ' 450', 'callback', create_function('&$a', 'return ($a >= 175 && $a <= 450);')); 
        $system_form -> addRule('sidebar_width', _THEFIELD.' "'._SIDEBARWIDTH.'" '._ISMANDATORY, 'required', null, 'client');
        $system_form -> addRule('sidebar_width', _INVALIDFIELDDATAFORFIELD.': "'._SIDEBARWIDTH.'"', 'numeric', null, 'client');        
        $system_form -> setDefaults($configuration);

        if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
            $system_form -> freeze();
        } else {
            $system_form -> addElement("submit", "submit_system_variables", _SAVE, 'class = "flatButton"');
            if ($system_form -> isSubmitted() && $system_form -> validate()) {                                                              //If the form is submitted and validated
                $values = $system_form -> exportValues();

                foreach ($values as $key => $value) {
                    if ($key == 'sidebar_width') {
                        $temp = EfrontConfiguration::getValues();
                       
                        if ($temp['sidebar_width'] != $value) {
                            echo " ";
                            // Reload everything if sidebar value changed
                            $smarty -> assign("T_RELOAD_ALL",1);
                        }
                    }
                    
                    $result = EfrontConfiguration :: setValue($key, $value);
                    if (!$result) {
                        $failed_updates[] = _COULDNOTUPDATE." $key "._WITHVALUE." ".$value;
                    }
                }
                if (!isset($failed_updates)) {
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=system_config&tab=vars&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
                } else {
                    $message      = implode(", ", $failed_updates);
                    $message_type = 'failure';
                }
            }
        }
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
        $system_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
        $system_form -> setRequiredNote(_REQUIREDNOTE);
        $system_form -> accept($renderer);
        $smarty -> assign('T_SYSTEM_VARIABLES_FORM', $renderer -> toArray());
        $smarty -> assign("T_MAX_FILE_SIZE", FileSystemTree :: getUploadMaxSize());

        $extensions   = get_loaded_extensions();
        if (in_array('ldap', $extensions)) {
            $ldap_form = new Html_QuickForm("ldap_variables", "post", basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=system_config&tab=ldap", "", null, true);
            $ldap_form -> addElement("advcheckbox", "activate_ldap", _ACTIVATELDAP,    null, 'class = "inputCheckBox"', array(0, 1));
            $ldap_form -> addElement("advcheckbox", "only_ldap",     _SUPPORTONLYLDAP, null, 'class = "inputCheckBox"', array(0, 1));

            //$ldap_form -> addElement("text", "ldap_ssl",        _USESSL,       'class = "inputText"');
            $ldap_form -> addElement("text", "ldap_server",     _LDAPSERVER,   'class = "inputText"');
            $ldap_form -> addElement("text", "ldap_port",       _LDAPPORT,     'class = "inputText"');
            $ldap_form -> addElement("text", "ldap_binddn",     _LDAPBINDDN,   'class = "inputText"');
            $ldap_form -> addElement("password", "ldap_password",   _LDAPPASSWORD, 'class = "inputText"');
            $ldap_form -> addElement("text", "ldap_basedn",     _LDAPBASEDN,   'class = "inputText"');
            $ldap_form -> addElement("select", "ldap_protocol", _LDAPPROTOCOLVERSION, array('2' => '2', '3' => '3'));

            $ldap_form -> addElement("text", "ldap_uid",               _LOGINATTRIBUTE,      'class = "inputText"');
            $ldap_form -> addElement("text", "ldap_cn",                _LDAPCOMMONNAME,      'class = "inputText"');
            $ldap_form -> addElement("text", "ldap_postaladdress",     _LDAPADDRESS,         'class = "inputText"');
            $ldap_form -> addElement("text", "ldap_l",                 _LDAPLOCALITY,        'class = "inputText"');
            $ldap_form -> addElement("text", "ldap_telephonenumber",   _LDAPTELEPHONENUMBER, 'class = "inputText"');
            $ldap_form -> addElement("text", "ldap_mail",              _LDAPMAIL,            'class = "inputText"');
            $ldap_form -> addElement("text", "ldap_preferredlanguage", _LDAPLANGUAGE,        'class = "inputText"');

            $ldap_form -> setDefaults($configuration);

            if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
                $ldap_form -> freeze();
            } else {
                $ldap_form -> addElement("submit", "check_ldap", _CHECKSETTINGS, 'class = "flatButton"');
                $ldap_form -> addElement("submit", "submit_ldap_variables", _SAVE, 'class = "flatButton"');

                if ($ldap_form -> isSubmitted() && $ldap_form -> validate()) {                                                              //If the form is submitted and validated
                    $values = $ldap_form -> exportValues();
                    //error_reporting(E_ALL);pr($values);
                    if (isset($values['check_ldap'])) {
                        if (!($ds = ldap_connect($values['ldap_server'], $values['ldap_port']))) {
                            $message      = _CANNOTCONNECTLDAPSERVER;
                            $message_type = 'failure';

                        } else {
                            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $values['ldap_protocol']);
                            if (!($bind = ldap_bind($ds, $values['ldap_binddn'], $values['ldap_password']))) {
                                $message      = _CANNOTBINDLDAPSERVER;
                                $message_type = 'failure';
                            } else {
                                $message      = _SUCESSFULLYCONNECTEDTOLDAPSERVER;
                                $message_type = 'success';
                            }
                        }
                    } else {
                        foreach ($values as $key => $value) {
                            $result = EfrontConfiguration :: setValue($key, $value);
                            if (!$result) {
                                $failed_updates[] = _COULDNOTUPDATE." $key "._WITHVALUE." ".$value;
                            }
                        }
                        if (!isset($failed_updates)) {
                            $message      = _SUCCESFULLYUPDATECONFIGURATION;
                            $message_type = 'success';
                        } else {
                            $message      = implode(", ", $failed_updates);
                            $message_type = 'failure';
                        }
                    }
                }
            }
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

            $renderer -> setRequiredTemplate (
               '{$html}{if $required}
                    &nbsp;<span class = "formRequired">*</span>
                {/if}');
            $ldap_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
            $ldap_form -> setRequiredNote(_REQUIREDNOTE);
            $ldap_form -> accept($renderer);
            $smarty -> assign('T_LDAP_VARIABLES_FORM', $renderer -> toArray());
        } else {
            $smarty -> assign("T_EXTENSION_MISSING", 'ldap');
        }

        $smtp_form = new Html_QuickForm("smtp_variables", "post", basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=system_config&tab=smtp", "", null, true);

        $smtp_form -> addElement("text",     "smtp_host",    _SMTPSERVER,   'class = "inputText"');
        $smtp_form -> addElement("text",     "smtp_user",    _SMTPUSER,     'class = "inputText"');
        $smtp_form -> addElement("password", "smtp_pass",    _SMTPPASSWORD, 'class = "inputText"');
        $smtp_form -> addElement("text",     "smtp_port",    _SMTPPORT,     'class = "inputText"');
        $smtp_form -> addElement("text",     "smtp_timeout", _SMTPTIMEOUT,  'class = "inputText"');

        //$smtp_form -> addElement("advcheckbox", "smtp_ssl",  _USESSL,   null, 'class = "inputCheckBox"', array(1, 0));
        $smtp_form -> addElement("advcheckbox", "smtp_auth", _SMTPAUTH, null, 'class = "inputCheckBox"', array(0, 1));

        $smtp_form -> setDefaults($configuration);

        if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
            $smtp_form -> freeze();
        } else {
            $smtp_form -> addElement("submit", "check_smtp", _CHECKSETTINGS, 'class = "flatButton"');
            $smtp_form -> addElement("submit", "submit_smtp_variables", _SAVE, 'class = "flatButton"');

            if ($smtp_form -> isSubmitted() && $smtp_form -> validate()) {                                                              //If the form is submitted and validated
                $values = $smtp_form -> exportValues();

                if (isset($values['check_smtp'])) {
                    $user_mail = eF_getTableData("users", "email", "login='".$_SESSION['s_login']."'");
                    $header = array ('From'                      => $GLOBALS['configuration']['system_email'],
                                     'To'                        => $user_mail[0]['email'],
                                     'Subject'                   => 'Test email',
                                     'Content-type'              => 'text/plain;charset="UTF-8"',                       // if content-type is text/html, the message cannot be received by mail clients for Registration content
                                     'Content-Transfer-Encoding' => '7bit');
                    $smtp =& Mail::factory('smtp', array('auth'      => $values['smtp_auth'] ? true : false,
                                                         'host'      => $values['smtp_host'],
                                                         'password'  => $values['smtp_pass'],
                                                         'port'      => $values['smtp_port'],
                                                         'username'  => $values['smtp_user'],
                                                         'timeout'   => $values['smtp_timeout']));

                    $result = $smtp -> send($user_mail[0]['email'], $header, 'This is a test email send to verify SMTP settings');

                    if ($result === true) {
                        $message      = _EMAILSENDTOYOURADDRESS;
                        $message_type = 'success';
                    } else {
                        $message      = _EMAILCOULDNOTBESENDBECAUSE.': '.mb_substr($result -> getMessage(), 0, mb_strpos($result -> getMessage(), ':'));
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
                        $message      = _SUCCESFULLYUPDATECONFIGURATION;
                        $message_type = 'success';
                    } else {
                        $message      = implode(", ", $failed_updates);
                        $message_type = 'failure';
                    }
                }
            }
        }
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
        $smtp_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
        $smtp_form -> setRequiredNote(_REQUIREDNOTE);
        $smtp_form -> accept($renderer);
        $smarty -> assign('T_SMTP_VARIABLES_FORM', $renderer -> toArray());


        $locale_form = new Html_QuickForm("locale_variables", "post", basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=system_config&tab=locale", "", null, true);

        //$locale_form -> addElement("text", "set_locale",    _SPECIFICLOCALE,     'class = "inputText"');
        $locale_form -> addElement("text", "location",      _LOCATION,           'class = "inputText"');
        $locale_form -> addElement("text", "time_zone",     _TIMEZONE,           'class = "inputText"');
        $locale_form -> addElement("text", "decimal_point", _DECIMALPOINT,       'class = "inputText" style = "width:50px"');
        $locale_form -> addElement("text", "thousands_sep", _THOUSANDSSEPARATOR, 'class = "inputText" style = "width:50px"');
        $locale_form -> addElement("select", "currency",    _CURRENCY,   array('EUR' => $CURRENCYNAMES['EUR'], 'USD' => $CURRENCYNAMES['USD'], 'GBP' => $CURRENCYNAMES['GBP'], 'JPY' => $CURRENCYNAMES['JPY'], 'CAD' => $CURRENCYNAMES['CAD'] , 'AUD' => $CURRENCYNAMES['AUD']));
        $locale_form -> addElement("select", "date_format", _DATEFORMAT, array("DD/MM/YYYY" => "DD/MM/YYYY", "MM/DD/YYYY" => "MM/DD/YYYY", "YYYY/MM/DD" => "YYYY/MM/DD"));

        $locale_form -> setDefaults($configuration);

        if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
            $locale_form -> freeze();
        } else {
            $locale_form -> addElement("submit", "submit_locale", _SUBMIT, 'class = "flatButton"');

            if ($locale_form -> isSubmitted() && $locale_form -> validate()) {                                                              //If the form is submitted and validated
                $values = $locale_form -> exportValues();
                unset($values["submit_locale"]);

                foreach ($values as $key => $value) {
                    $result = EfrontConfiguration :: setValue($key, $value);
                    if (!$result) {
                        $failed_updates[] = _COULDNOTUPDATE." $key "._WITHVALUE." ".$value;
                    }
                }
                if (!isset($failed_updates)) {
                    $message      = _SUCCESFULLYUPDATECONFIGURATION;
                    $message_type = 'success';
                } else {
                    $message      = implode(", ", $failed_updates);
                    $message_type = 'failure';
                }
            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
        $locale_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
        $locale_form -> setRequiredNote(_REQUIREDNOTE);
        $locale_form -> accept($renderer);
        $smarty -> assign('T_LOCALE_VARIABLES_FORM', $renderer -> toArray());



        $php_form = new Html_QuickForm("php_variables", "post", basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=system_config&tab=php", "", null, true);

        $php_form -> addElement("text", "memory_limit",       null, 'class = "inputText" style = "width:60px"');
        $php_form -> addElement("text", "max_execution_time", null, 'class = "inputText" style = "width:60px"');
        $php_form -> addElement("advcheckbox", "gz_handler",  null, null, 'class = "inputCheckBox"', array(0, 1));
        //        $php_form -> addElement("advcheckbox", "display_errors", null, null, 'class = "inputCheckBox"', array(0, 1));

        $php_form -> setDefaults($configuration);

        isset($configuration['memory_limit'])       ? $php_form -> setDefaults(array('memory_limit'       => $configuration['memory_limit']))       : $php_form -> setDefaults(array('memory_limit'       => (int)ini_get('memory_limit')));
        isset($configuration['max_execution_time']) ? $php_form -> setDefaults(array('max_execution_time' => $configuration['max_execution_time'])) : $php_form -> setDefaults(array('max_execution_time' => ini_get('max_execution_time')));
        isset($configuration['gz_handler'])         ? $php_form -> setDefaults(array('gz_handler'         => $configuration['gz_handler']))         : $php_form -> setDefaults(array('gz_handler'         => ''));
        //        isset($configuration['display_errors'])   ? $php_form -> setDefaults(array('display_errors'     => $configuration['display_errors']))     : $php_form -> setDefaults(array('display_errors'     => ini_get('display_errors')));

        if (isset($currentUser -> coreAccess['configuration']) && $currentUser -> coreAccess['configuration'] != 'change') {
            $php_form -> freeze();
        } else {
            $php_form -> addElement("submit", "submit_php", _SUBMIT, 'class = "flatButton"');

            if ($php_form -> isSubmitted() && $php_form -> validate()) {                                                              //If the form is submitted and validated
                $values = $php_form -> exportValues();
                unset($values["submit_php"]);

                foreach ($values as $key => $value) {
                    if ($value == '') {
                        eF_deleteTableData("configuration", "name = '$key'");
                        unset($configuration[$key]);
                    } else {
                        if ($key == 'memory_limit' || $key == 'max_execution_time') {                              //You can't set these values below the php.ini setting
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
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=system_config&tab=php&message=".urlencode(_SUCCESFULLYUPDATECONFIGURATION)."&message_type=success");
                } else {
                    $message      = implode(", ", $failed_updates);
                    $message_type = 'failure';
                }
            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
        $php_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
        $php_form -> setRequiredNote(_REQUIREDNOTE);
        $php_form -> accept($renderer);
        $smarty -> assign('T_PHP_VARIABLES_FORM', $renderer -> toArray());

        /*Layout part from here over*/
	    $customBlocks = unserialize($GLOBALS['configuration']['custom_blocks']);
	    
	    if (isset($_GET['add_block']) || (isset($_GET['edit_block']) && in_array($_GET['edit_block'], array_keys($customBlocks)))) {
	        if (isset($_GET['postAjaxRequest_insert'])) {
			$file_id 		= urldecode($_GET['file_id']);
            $file_insert 	= new EfrontFile($file_id);
			if (strpos($file_insert['mime_type'] , "image") !== false) {
				$img_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
				echo "<img src=\"".$img_return."\" border=0 />";
				exit;
			} elseif (strpos($file_insert['mime_type'] , "pdf") !== false) {
				$pdf_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
				echo '<iframe src="'.$pdf_return.'"  name="pdfFrame_'.urlencode($file_insert['id']).'" width="100%" height="600"></iframe>';
				exit;
			} elseif (strpos($file_insert['mime_type'] , "php") !== false) {
				$php_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/content/admin/"));
				echo '<a href="'.G_RELATIVEADMINLINK.$php_return.'">'.$php_return.'</a><br />';
				exit;
			}elseif (strpos($file_insert['mime_type'] , "flash") !== false) {
				$flash_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
				if ($_GET['editor_mode'] == "true") {
					echo '<img width="400" height="400" src="editor/tiny_mce/themes/advanced/images/spacer.gif"  title="'.$flash_return.'" alt="'.$flash_return.'" class="mceItemFlash" />';
					exit;
				} else {
					echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="400" height="400">
					<param name="src" value="'.$flash_return.'" />
					<param name="width" value="400" />
					<param name="height" value="400" />
					<embed type="application/x-shockwave-flash" src="'.$flash_return.'" width="400" height="400"></embed>
					</object>';
					exit;
				}
			} else {
				echo "<a href=view_file.php?action=download&file=".$file_id.">".$file_insert['physical_name']."</a>";
				exit;	
				
			}
        }

			$load_editor = true;
	        $layout_form = new HTML_QuickForm("add_block_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=system_config&tab=layout&".(isset($_GET['edit_block']) ? 'edit_block='.$_GET['edit_block'] : 'add_block=1'), "", null, true);

            $layout_form -> addElement('text', 'title', _BLOCKTITLE, 'class = "inputText"');
            $layout_form -> addElement('textarea', 'content', _BLOCKCONTENT, 'id="block_content_data" class = "mceEditor" style = "width:100%;height:300px;"');
            $layout_form -> addElement('submit', 'submit_block',_SAVE, 'class = "flatButton"');
            $layout_form -> addRule('title', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
	        
            if (isset($_GET['edit_block'])) {
                $layout_form -> setDefaults($customBlocks[$_GET['edit_block']]);
            }
            
            if ($layout_form -> isSubmitted() && $layout_form -> validate()) {
                $block = array('title'   => $layout_form -> exportValue('title'),
                               'content' => $layout_form -> exportValue('content'));
                                
                if (isset($_GET['edit_block'])) {
                    $customBlocks[$_GET['edit_block']] = $block;
                } else {
                    sizeof($customBlocks) > 0 ? $customBlocks[] = $block : $customBlocks = array($block);                
                }
                EfrontConfiguration::setValue('custom_blocks', serialize($customBlocks));
                
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=system_config&tab=layout");
            }
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $layout_form -> accept($renderer);
            $smarty -> assign('T_ADD_BLOCK_FORM', $renderer -> toArray());
            
			$loadScripts[] = 'drag-drop-folder-tree';
			$loadScripts[] = 'scriptaculous/effects';
			$basedir    = G_ADMINPATH;
			try {
				$filesystem = new FileSystemTree($basedir);
				$filesystem -> handleAjaxActions($currentUser);

				if (isset($_GET['edit_block'])) {
					$url = basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=system_config&edit_block='.$_GET['edit_block'];
				}else{
					$url = basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=system_config&add_block=1';
				}
				$options    = array('share' => false);

				if (isset($_GET['ajax'])) {
					isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

					if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
						$sort = $_GET['sort'];
						isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
					} else {
						$sort = 'login';
					}

					if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
						isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
					}
					isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
					isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
					$ajaxOptions 	= array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
					$extraFileTools = array(array('image' => 'images/16x16/arrow_right_green.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
					echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, $extraFileTools, "", "", "", false);
					exit;
				}
				$smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, $extraFileTools, "", "", "", false));
			} catch (Exception $e) {
				$smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
				$message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
				$message_type = 'failure';
			}
	    
			} else {
	        $loadScripts[] = 'scriptaculous/prototype';
	        $loadScripts[] = 'scriptaculous/scriptaculous';
	        $loadScripts[] = 'scriptaculous/effects';
	        $loadScripts[] = 'scriptaculous/dragdrop';

	        $blocks = array('login'           => _LOGINENTRANCE,
        					'online'          => _USERSONLINE, 
        					'lessons'         => _LESSONS, 
                        	'selectedLessons' => _SELECTEDLESSONS,
        					'news'            => _SYSTEMNEWS);
	        foreach ($customBlocks as $key => $block) {
	            $blocks[$key] = $block['title'];
	        }
	        
	        $smarty -> assign("T_BLOCKS", json_encode($blocks));
	        $currentPositions = unserialize($GLOBALS['configuration']['index_positions']);
            $smarty -> assign("T_POSITIONS", json_encode($currentPositions));
            
	        if (isset($_GET['ajax']) && $_GET['ajax'] == 'set_layout') {
	            parse_str($_POST['leftList']);
	            parse_str($_POST['centerList']);
	            parse_str($_POST['rightList']);

	            !isset($leftList)   ? $leftList   = array() : null;
	            !isset($centerList) ? $centerList = array() : null;
	            !isset($rightList)  ? $rightList  = array() : null;

	            array_pop($leftList);array_pop($rightList);array_pop($centerList);        //Remove emmpty values, that are the 'bogus' li element 

	            $positions = serialize(array('leftList' => $leftList, 'centerList' => $centerList, 'rightList' => $rightList, 'layout' => $_POST['layout']));
	            EfrontConfiguration :: setValue('index_positions', $positions);
	            exit;
	        } else if (isset($_GET['ajax']) && $_GET['ajax'] == 'reset_layout') {
	            EfrontConfiguration :: setValue('index_positions', '');
	            exit;
	        } else if (isset($_GET['ajax']) && isset($_GET['delete_block'])) {
	            unset($customBlocks[$_GET['delete_block']]);
	            EfrontConfiguration::setValue('custom_blocks', serialize($customBlocks));
	            exit;
	        }
	    }      
    }
    else if ( isset($_GET['op']) && $_GET['op'] == 'users') {
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        //include "module_importExportUsers.php";
        $importForm = new HTML_QuickForm("import_users_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=users&oper=import_users&tab=import", "", null, true);
        $importForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter

        //$fileUpload = & HTML_QuickForm :: createElement('file', 'users_file', _DATAFILE, 'class = "inputText"');
        //$importForm -> addElement($fileUpload);
        $importForm -> addElement('file', 'users_file', _DATAFILE, 'class = "inputText"');
        $importForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);            //getUploadMaxSize returns size in KB
        $importForm -> addRule('users_file', _YOUMUSTUPLOADFILE, 'uploadedfile', null, 'client');
        $importForm -> addElement('radio', 'replace_users', _KEEPEXISTINGUSERS, null, 'keep');
        $importForm -> addElement('radio', 'replace_users', _REPLACEEXISTINGUSERS, null, 'replace');
        $importForm -> addElement('checkbox', 'send_email', _SENDINFOVIAEMAIL);
        $importForm -> addElement('submit', 'submit_import_users', _IMPORTUSERSDATA, 'class=flatButton');

        $importForm -> setDefaults(array('replace_users' => 0));

        //$form_sendEmail = & HTML_QuickForm :: createElement('checkbox', 'send_email', _SENDINFOVIAEMAIL, null, null);
        //$importForm -> addElement($form_sendEmail);
        $admin = '"'.$_SESSION['s_login'].'"';
        $usersTable  = eF_getTableData("users", "*", "");
        $tableFields = array_keys($usersTable[0]);

        $smarty -> assign("T_FIELDS", $tableFields);
        if (isset($_GET['csv_sample'])) {
            header("content-type:text/plain");
            header('content-disposition: attachment; filename= "csv_sample.txt"');
            echo implode(";", $tableFields);
            foreach ($tableFields as $field) {
                $userFields[$field] = $currentUser -> user[$field];
            }
            $userFields['password'] = '<password>';
            echo "\n".implode(";", $userFields);
            exit;
        }

        if ($importForm -> isSubmitted() && $importForm -> validate()) {
            try {
                if (!is_dir($currentUser -> user['directory']."/temp")) {
                    mkdir($currentUser -> user['directory']."/temp", 0755);
                }
                $importForm -> exportValue('replace_users') ? $replaceUsers = true : $replaceUsers = false;

                $filesystem   = new FileSystemTree($currentUser -> user['directory']."/temp");
                $uploadedFile = $filesystem -> uploadFile('users_file');

                list($newUsers, $messages) = EfrontSystem :: importUsers($uploadedFile, $replaceUsers);
                //pr($newUsers);
				foreach ($newUsers as $key => $value) {
				//pr($value);
                    if ($importForm -> exportValue('send_email')) {
                        $subject    = _ACCOUNTACTIVATIONMAILSUBJECT;
                        $from       = $configuration['system_email'];
                        $to         = $value -> user['email'];
                        $body       = _THISEMAIL.'<br><br>'._CONTAINSINFORMATIONABOUTYOURACCOUNTINTHEPLATFORM.' '._EFRONT.'<br><br>'._LOGIN.':'.$value -> user['login'].'<br><br>'._PASSWORD.':'.$value -> user['password'].'<br><br>'._THANKYOU;
                        eF_mail($from, $to, $subject, $body);
                    }
                }
                $message      = _TOTALINSERTED.' '.sizeof($newUsers).' '._USERS;
                $message_type = 'success';
                if (sizeof($messages) > 0) {
                    $message     .= '. '._THEFOLLOWINGUSERSCOULDNOTBEIMPORTED.":<br>".implode("<br>", $messages);
                    $message_type = 'failure';
                }
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $importForm -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $importForm -> setRequiredNote(_REQUIREDNOTE);
        $importForm -> accept($renderer);
        $smarty -> assign('T_IMPORT_USERS_FORM', $renderer -> toArray());

        $exportForm = new HTML_QuickForm("export_users_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=users&oper=export_users&tab=export", "", null, true);
        $exportForm -> addElement('radio', 'export_users', _KEEPEXISTINGUSERS, null, 'csvA');
        $exportForm -> addElement('radio', 'export_users', _KEEPEXISTINGUSERS, null, 'csvB');
        $exportForm -> setDefaults(array('export_users' => 0));
        $exportForm -> addElement('submit', 'submit_export_users', _EXPORTUSERSDATA);

        if ($exportForm -> isSubmitted() && $exportForm -> validate()) {
            $exportForm -> exportValue('export_users') == 'csvA' ? $separator = ',' : $separator = ';';
            try {
                $file = EfrontSystem :: exportUsers($separator);
                header("content-type:".$file['mime_type']);
                header('content-disposition: attachment; filename= "'.($file['name']).'"');
                readfile($file['path']);
                exit;
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = _ERRORRESTORINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
            //$smarty -> assign("T_EXPORTED_FILE", $file);
        }
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $exportForm -> accept($renderer);

        $smarty -> assign('T_EXPORT_USERS_FORM', $renderer -> toArray());

    } else if (isset($_GET['op']) && $_GET['op'] == 'user_profile') {
        if (isset($currentUser -> coreAccess['user_profile']) && $currentUser -> coreAccess['user_profile'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $result        = eF_getTableData("user_profile", "*");
        $profileFields = array();
        $languages     = EfrontSystem :: getLanguages(true);
        foreach ($result as $field) {
            $field['languages_NAME']       = $languages[$field['languages_NAME']];
            $profileFields[$field['name']] = $field;
        }

        if (isset($_GET['delete_field']) && in_array($_GET['delete_field'], array_keys($profileFields))) {
            if (isset($currentUser -> coreAccess['user_profile']) && $currentUser -> coreAccess['user_profile'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            }
            if (eF_deleteTableData("user_profile", "name='".$_GET['delete_field']."'")) {
                eF_executeNew("alter table users drop ".$_GET['delete_field']);
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=user_profile&message=".urlencode(_SUCCESSFULLYDELETEDFIELD)."&message_type=success");
            } else {
                $message      = _ERROROCCURED;
                $message_type = "failure";
            }
        } else if (isset($_GET['activate_field']) && in_array($_GET['activate_field'], array_keys($profileFields))) {
            if (isset($currentUser -> coreAccess['user_profile']) && $currentUser -> coreAccess['user_profile'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            }
            eF_updateTableData("user_profile", array("active" => 1), "name='".$_GET['activate_field']."'");
            exit;
        } else if (isset($_GET['deactivate_field']) && in_array($_GET['deactivate_field'], array_keys($profileFields))) {
            if (isset($currentUser -> coreAccess['user_profile']) && $currentUser -> coreAccess['user_profile'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            }
            eF_updateTableData("user_profile", array("active" => 0), "name='".$_GET['deactivate_field']."'");
            exit;
        } else if (isset($_GET['add_field']) || (isset($_GET['edit_field']) && in_array($_GET['edit_field'], array_keys($profileFields)))) {
            if (isset($currentUser -> coreAccess['user_profile']) && $currentUser -> coreAccess['user_profile'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            }
            isset($_GET['add_field']) ? $postTarget = 'add_field=1' : $postTarget = 'edit_field='.$_GET['edit_field'];

            $form = new HTML_QuickForm("field_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=user_profile&'.$postTarget, "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter

            $form -> addElement('text', 'name',          null, 'class = "inputText"');
            $form -> addElement('text', 'description',   null, 'class = "inputText"');
            $form -> addElement('text', 'default_value', null, 'class = "inputText" id = "default_value"');
            $form -> addElement('text', 'values[0]',     null, 'class = "inputText"');
            $form -> addElement('select', 'db_type',        null, array ('text' => _TEXT,    'int'    => _INTEGER));
            $form -> addElement('select', 'type',           null, array ('text' => _TEXTBOX, 'select' => _SELECTBOX), 'onchange = "changeType()";');
            $form -> addElement('select', 'languages_NAME', null, EfrontSystem :: getLanguages(true));
            $form -> addElement("advcheckbox", "active",    null, null, 'class = "inputCheckBox"', array(0, 1));
            $form -> addElement("advcheckbox", "visible",   null, null, 'class = "inputCheckBox"', array(0, 1));
            $form -> addElement("advcheckbox", "mandatory", null, null, 'class = "inputCheckBox"', array(0, 1));
            $form -> addElement('submit', 'submit_field', _SUBMIT, 'class = "flatButton"');

            $form -> addRule('name', _THEFIELD.' "'._NAME.'" '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'alnum_with_spaces');

            $form -> setDefaults(array("active" => 1));
            if (isset($_GET['edit_field'])) {
                $result  = eF_getTableData("user_profile", "*", "name='".$_GET['edit_field']."'");
                if ($result[0]['type'] == 'select') {
                    $options = unserialize($result[0]['options']);
                    for ($i = 1; $i < sizeof($options); $i++) {
                        $form -> addElement('text', "values[$i]", null, 'class = "inputText"');
                        $form -> setDefaults(array("values[$i]" => $options[$i]));
                    }
                    $form -> setDefaults(array('values[0]' => $options[0]));
                    $smarty -> assign("T_SELECT_OPTIONS", sizeof($options) - 1);
                }
                $form -> setDefaults($result[0]);
                $form -> freeze(array('name', 'db_type', 'type'));
            }

            if ($form -> isSubmitted() && $form -> validate()) {

                $values = $form -> exportValues();

                $fields = array('name'           => trim($values['name']),
                                'description'    => $values['description'],
                                'default_value'  => $values['default_value'],
                                'db_type'        => $values['db_type'],
                                'type'           => $values['type'],
                                'options'        => ($values['type'] == 'select')? serialize(array_values($_POST['values'])):"",        //array_values is needed, since the values may not be sorted (for example th 3rd value missing)
                                'active'         => $values['active'],
                                'visible'        => $values['visible'],
                                'mandatory'      => $values['mandatory'],
                                'languages_NAME' => $values['languages_NAME'],
                                'size'           => 255);

                if (isset($_GET['add_field'])) {
                    if (in_array($fields['name'], array_keys($profileFields))) {
                        $message      = _FIELDALREADYEXISTS;
                        $message_type = 'failure';
                    } else if (eF_insertTableData("user_profile", $fields)) {
                        $sql = 'alter table users add '.$fields['name'];
                        $fields['db_type'] == 'text' ? $sql .= ' varchar (255)'                    : $sql .= ' int';
                        $fields['mandatory']         ? $sql .= ' not null'                         : $sql .= '';
                        $fields['default']           ? $sql .= ' default "'.$fields['default'].'"' : $sql .= '';
                        if (eF_executeNew($sql)) {
                            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=user_profile&message=".urlencode(_FIELDCREATED)."&message_type=success");
                        } else {
                            eF_deleteTableData("user_profile", "name='".$fields['name']."'");
                            $message      = _COULDNOTADDFIELD;
                            $message_type = 'failure';
                        }
                    } else {
                        $message      = _COULDNOTADDFIELD;
                        $message_type = 'failure';
                    }
                } else {
                    if (eF_updateTableData("user_profile", $fields, "name='".$values['name']."'")) {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=user_profile&message=".urlencode(_FIELDUPDATED)."&message_type=success");
                    } else {
                        $message      = _COULDNOTUPDATEFIELD;
                        $message_type = 'failure';
                    }
                }

            }
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer
            $renderer -> setRequiredTemplate(
               '{$html}{if $required}
                    &nbsp;<span class = "formRequired">*</span>
                {/if}');

            $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
            $form -> setRequiredNote(_REQUIREDNOTE);
            $form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

            $smarty -> assign('T_FIELD_FORM', $renderer -> toArray());                     //Assign the form to the template

        } else {
            $smarty -> assign("T_PROFILE_FIELDS", $profileFields);
        }
    }

    /*Show the announcements (news) full page*/
    elseif (isset($_GET['op']) && $_GET['op'] == 'news') {
        if (isset($currentUser -> coreAccess['news']) && $currentUser -> coreAccess['news'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");exit;
        }
        //$news = eF_getTableData("news", "*", "lessons_ID=".$currentLesson -> lesson['id']);
        $smarty -> assign("T_NEWS", eF_getNews());
    }
    
    else if (isset($_GET['op']) && $_GET['op'] == 'backup') {
        if (isset($currentUser -> coreAccess['backup']) && $currentUser -> coreAccess['backup'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $loadScripts[] = 'drag-drop-folder-tree';
        $loadScripts[] = 'scriptaculous/effects';
        $basedir    = G_BACKUPPATH;
        if (isset($_GET['restore'])) {
            ini_set("memory_limit", "-1");
            try {
                $restoreFile = new EfrontFile($_GET['restore']);
                if (!EfrontSystem :: restore($_GET['restore'])) {
                    $message      = _ERRORRESTORINGFILE;
                    $message_type = 'failure';
                } else {
                    $message      = _SUCCESFULLYRESTOREDSYSTEM;
                    $message_type = 'success';
                }
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = _ERRORRESTORINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        }

        try {
            $filesystem = new FileSystemTree($basedir);
            $filesystem -> handleAjaxActions($currentUser);

            $url            = basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=backup';
            $options        = array('zip' => false, 'create_folder' => false, 'folders' => false);
            if (!isset($currentUser -> coreAccess['backup']) || $currentUser -> coreAccess['backup'] == 'change') {
                $extraFileTools = array(array('image' => 'images/16x16/undo.png', 'title' => _RESTORE, 'action' => 'restore'));
            }
            $extraHeaderOptions = array(array('image' => 'images/16x16/redo.png', 'title' => _BACKUP, 'action' => 'eF_js_showDivPopup(\''._BACKUP.'\', 0, \'backup_table\')'));

            if (isset($_GET['ajax'])) {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'timestamp';
                }

                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                }
                isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, $extraFileTools, false, $extraHeaderOptions, '', false);
                exit;
            }
            $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, $extraFileTools, false, $extraHeaderOptions, '', false));
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }


        $backup_form = new HTML_QuickForm("backup_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=backup', "", null, true);
        $backup_form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter

        $backup_form -> addElement('text', 'backupname', null, 'class = "inputText"');
        $backup_form -> addRule('backupname', _THEFIELD.' '._FILENAME.' '._ISMANDATORY, 'required', null, 'client');
        $backup_form -> setDefaults(array("backupname" => "efront_backup_".date('Y_m_d_h.i.s', time())));

        $backup_form -> addElement('select', 'backuptype', null, array ("0" => _DATABASEONLY, "1" => _ALLDATABACKUP));
        $backup_form -> addElement('submit', 'submit_backup', _TAKEBACKUP, 'class = "flatButton" onclick = "$(\'backup_image\').show();"');

        if ($backup_form -> isSubmitted() && $backup_form -> validate()) {
            $values = $backup_form -> exportValues();

            try {
                $backupFile = EfrontSystem :: backup($values['backupname'].'.zip', $values['backuptype']);
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=backup&message=".urlencode(_SUCCESFULLYBACKEDUP)."&message_type=success");
            } catch (EfrontFileException $e) {
                $message      = _BACKUPFAILED.': '.$e -> getMessage();
                $message_type = 'failure';
            }
        }
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $backup_form -> accept($renderer);
        $smarty -> assign('T_BACKUP_FORM', $renderer -> toArray());
    }

    else if (isset($_GET['op']) && $_GET['op'] == 'languages') {
        if (isset($currentUser -> coreAccess['languages']) && $currentUser -> coreAccess['languages'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $languages = EfrontSystem :: getLanguages();
        if (isset($_GET['delete_language']) && eF_checkParameter($_GET['delete_language'], 'file') && in_array($_GET['delete_language'], array_keys($languages)) && $_GET['delete_language'] != 'english') {
            if (isset($currentUser -> coreAccess['languages']) && $currentUser -> coreAccess['languages'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            }
            try {
                $file = new EfrontFile(G_ROOTPATH.'/libraries/language/lang-'.$_GET['delete_language'].'.php.inc');
                $file -> delete();
                eF_deleteTableData("languages", "name='".$_GET['delete_language']."'");
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } elseif (isset($_GET['deactivate_language']) && eF_checkParameter($_GET['deactivate_language'], 'file') && in_array($_GET['deactivate_language'], array_keys($languages))) {
            if (isset($currentUser -> coreAccess['languages']) && $currentUser -> coreAccess['languages'] != 'change') {
                echo urlencode(_UNAUTHORIZEDACCESS);
                exit;
            }
            //Although db operations do not support exceptions (yet), we leave this here for future support
            try {
                eF_updateTableData("languages", array("active" => 0), "name='".$_GET['deactivate_language']."'");
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } elseif (isset($_GET['activate_language']) && eF_checkParameter($_GET['activate_language'], 'file') && in_array($_GET['activate_language'], array_keys($languages))) {
            if (isset($currentUser -> coreAccess['languages']) && $currentUser -> coreAccess['languages'] != 'change') {
                echo urlencode(_UNAUTHORIZEDACCESS);
                exit;
            }
            //Although db operations do not support exceptions (yet), we leave this here for future support
            try {
                eF_updateTableData("languages", array("active" => 1), "name='".$_GET['activate_language']."'");
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
        if (!isset($currentUser -> coreAccess['languages']) || $currentUser -> coreAccess['languages'] == 'change') {
            $createForm = new HTML_QuickForm("create_language_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=languages', "", null, true);
            $createForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
            $createForm -> addElement('text', 'english_name', _ENGLISHNAME, 'class = "inputText" id = "language_name"');
            $createForm -> addElement('text', 'translation', _TRANSLATION, 'class = "inputText" id = "language_translation"');
            $createForm -> addElement('file', 'language_upload', _FILENAME, 'class = "inputText"');
            $createForm -> addElement('hidden', 'selected_language', null, 'id = "selected_language"');
            $createForm -> addElement('submit', 'submit_upload_language', _SUBMIT, 'class = "flatButton"');
            $createForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);            //getUploadMaxSize returns size in KB
            $createForm -> addRule('english_name', _THEFIELD.' "'._ENGLISHNAME.'" '._ISMANDATORY, 'required', null, 'client');
            $createForm -> addRule('english_name', _INVALIDFIELDDATA.': '._ENGLISHNAME, 'checkParameter', 'file');
            $createForm -> addRule('translation', _THEFIELD.' "'._TRANSLATION.'" '._ISMANDATORY, 'required', null, 'client');
            //$createForm -> addRule('language_upload', _THEFIELD.' "'._FILENAME.'" '._ISMANDATORY, 'required', null, 'client');

            if ($createForm -> isSubmitted() && $createForm -> validate()) {
                $values = $createForm -> exportValues();
                try {
                    if ($values['selected_language']) {
                        if ($_FILES['language_upload']['error'] == 0) {
                            $filesystem   =  new FileSystemTree(G_ROOTPATH.'libraries/language');
                            $uploadedFile = $filesystem -> uploadFile('language_upload', G_ROOTPATH.'libraries/language');
                            $uploadedFile -> rename(dirname($uploadedFile['path']).'/lang-'.$values['english_name'].'.php.inc', true);
                        }
                        $fields = array("name"        => $values['english_name'],
                                        "translation" => $values['translation']);
                        eF_updateTableData("languages", $fields, "name='".$values['selected_language']."'");
                        //include "editor/tiny_mce/langs/language.php";
                        $RetValues = file(G_SERVERNAME."/editor/tiny_mce/langs/language.php?langname=".$values['english_name']);
						header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=languages&message=".urlencode(_SUCCESSFULLYUPDATEDLANGUAGE)."&message_type=success");
                    } else {
                        if ($_FILES['language_upload']['error'] == 0) {
                            $filesystem   =  new FileSystemTree(G_ROOTPATH.'libraries/language');
                            $uploadedFile = $filesystem -> uploadFile('language_upload', G_ROOTPATH.'libraries/language');
                            if($uploadedFile['extension'] == "zip"){
                                $lang_zip_file_temp = new EfrontFile($uploadedFile['path']);
                                $lang_zip_file =  $lang_zip_file_temp -> uncompress(false);
                                $lang_file_rename = new EfrontFile($lang_zip_file[0]);
                                $lang_file_rename -> rename(dirname($uploadedFile['path']).'/lang-'.$values['english_name'].'.php.inc', true);
                            }else{
                                $uploadedFile -> rename(dirname($uploadedFile['path']).'/lang-'.$values['english_name'].'.php.inc', true);
                            }
                        } else {
                            $file = new EfrontFile(G_ROOTPATH.'libraries/language/lang-english.php.inc');
                            $file -> copy(G_ROOTPATH.'libraries/language/lang-'.$values['english_name'].'.php.inc');
                        }
                        $fields = array("name"        => $values['english_name'],
                                        "translation" => $values['translation'],
                                        "active"      => 1);
                        eF_insertTableData("languages", $fields);
                        $RetValues = file(G_SERVERNAME."/editor/tiny_mce/langs/language.php?langname=".$values['english_name']);
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=languages&message=".urlencode(_SUCCESSFULLYADDEDLANGUAGE)."&message_type=success");
                    }
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }

            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $renderer -> setRequiredTemplate (
                   '{$html}{if $required}
                        &nbsp;<span class = "formRequired">*</span>
                    {/if}');

            $createForm -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
            $createForm -> setRequiredNote(_REQUIREDNOTE);
            $createForm -> accept($renderer);
            $smarty -> assign("T_CREATE_LANGUAGE_FORM", $renderer -> toArray());
            $smarty -> assign("T_MAX_FILE_SIZE", FileSystemTree :: getUploadMaxSize());
        }

        $smarty -> assign("T_LANGUAGES", $languages);
    }
    /*
     Set the current CSS file
     */
    else if (isset($_GET['op']) && $_GET['op'] == 'style') {
        if (isset($currentUser -> coreAccess['set_style']) && $currentUser -> coreAccess['set_style'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $loadScripts[] = 'drag-drop-folder-tree';
        $loadScripts[] = 'scriptaculous/effects';
        if (!is_dir(G_CUSTOMCSSPATH)) {
            mkdir(G_CUSTOMCSSPATH, 0755);
        }
        $basedir    = G_CUSTOMCSSPATH;

        $url                = basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=style';
        if (!isset($currentUser -> coreAccess['set_style']) || $currentUser -> coreAccess['set_style'] == 'change') {
            $extraFileTools     = array(array('image' => 'images/16x16/pin_red.png', 'title' => _APPLY,                'action' => 'useStyle'));
            $extraHeaderOptions = array(array('image' => 'images/16x16/import1.png', 'title' => _DOWNLOADDEFAULTSTYLE, 'href'   => 'view_file.php?file='.G_ROOTPATH.'www/css/css_global.css&action=download'),
                                        array('image' => 'images/16x16/undo.png',    'title' => _SETDEFAULTSTYLE,      'action' => 'useDefaultStyle(this)'));
        } else {
            $options = array('edit' => false, 'delete' => false, 'upload' => false, 'create_folder' => false);
        }
        try {
            $filesystem = new FileSystemTree($basedir);
            $filesystem -> handleAjaxActions($currentUser);

            if (isset($_GET['ajax'])) {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }

                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                }
                isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, $extraFileTools, false, $extraHeaderOptions, '', false);
                exit;
            }
            $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, $extraFileTools, false, $extraHeaderOptions, '', false));
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }

        foreach (new EfrontFileOnlyFilterIterator(new ArrayIterator($filesystem -> tree)) as $key => $value) {
            $styles[] = $value['id'];
        }

        if (isset($_GET['use_none'])) {
            try {
                EfrontConfiguration :: setValue("css", false);
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        } else if (isset($_GET['set_style'])) {
            if (!in_array($_GET['set_style'], $styles)) {
                header("HTTP/1.0 500");
                echo _INVALIDPAGE;
            } else {
                try {
                    $styleFile = new EfrontFile($_GET['set_style']);
                    EfrontConfiguration :: setValue('css', $styleFile['physical_name']);
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
            }
            exit;
        }
    }
    /*
     Module inclusion. If there are any modules that need to be displayed as ops in the control panel, they are included here
     */
    else if (isset($_GET['op']) && in_array($_GET['op'], array_keys($module_ctgs))) {
        include(G_MODULESPATH.$_GET['op'].'/module.php');
        $smarty -> assign("T_OP_MODULE", $module_ctgs[$_GET['op']]);
    }
    else {
        $innerTableIdentifier = 'admin_cpanel';        //This is a notifier for cookies handling the show/hide status of inner tables. It affects only control panel and is considered inside printInnerTable smarty plugin

        if (!isset($currentUser -> coreAccess['set_logo']) || $currentUser -> coreAccess['set_logo'] == 'change') {
            $logo_form = new HTML_QuickForm("upload_logo_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=control_panel', "", null, true);
            $logo_form -> addElement('file', 'logo', _EFRONTLOGO);
            $logo_form -> addElement("advcheckbox", "default_logo", _USEDEFAULTLOGO, null, 'class = "inputCheckBox"', array(0, 1));
            $logo_form -> addElement('submit', 'submit_upload_logo', _SUBMIT, 'class = "flatButton"');
            $smarty -> assign("T_MAX_UPLOAD_SIZE", FileSystemTree :: getUploadMaxSize());
            if ($logo_form -> isSubmitted() && $logo_form -> validate()) {
                try {
                    if ($logo_form -> exportValue('default_logo')) {
                        EfrontConfiguration :: setValue('logo', false);
                    } else {
                        try {
                            $logoFile = new EfrontFile($configuration['logo']);
                            $logoFile -> delete();
                        } catch (Exception $e) {}

                        $logoDirectory = new EfrontDirectory(G_LOGOPATH);
                        $filesystem    = new FileSystemTree(G_LOGOPATH);

                        $logoFile = $filesystem -> uploadFile('logo', $logoDirectory);
                        EfrontConfiguration :: setValue('logo', $logoFile['id']);

                        // Normalize avatar picture to 120 x DimY or DimX x 80
                        eF_normalizeImage(G_LOGOPATH . $logoFile['name'], $logoFile['extension'], 120, 80);
                    }
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_SUCCESFULLYUPDATEDLOGO)."&message_type=success");
                } catch (EfrontFileException $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $logo_form -> accept($renderer);
            $smarty -> assign('T_UPLOAD_LOGO_FORM', $renderer -> toArray());

            try {
                if (!isset($logoFile)) {
                    $logoFile = new EfrontFile($configuration['logo']);
                }

                $smarty -> assign("T_LOGO", 'logo/'.$logoFile['physical_name']);
                // Get current dimensions
                list($width, $height) = getimagesize($logoFile['path']);
                if ($width > 400 || $height > 200) {
                    // Get normalized dimensions
                    list($newwidth, $newheight) = eF_getNormalizedDims($logoFile['path'], 400, 200);

                    // The template will check if they are defined and normalize the picture only if needed
                    $smarty -> assign("T_NEWWIDTH", $newwidth);
                    $smarty -> assign("T_NEWHEIGHT", $newheight);
                }

            } catch (EfrontFileException $e) {
                $smarty -> assign("T_LOGO", "logo.png");
            }
        }
        /*Calculate element positions, so they can be rearreanged accordingly to the user selection*/
        $elementPositions = eF_getTableData("configuration", "value", "name='".$_SESSION['s_login']."_positions'");
        if (sizeof($elementPositions) > 0) {
            $elementPositions = unserialize($elementPositions[0]['value']);
            $smarty -> assign("T_POSITIONS_FIRST", $elementPositions['first']);
            $smarty -> assign("T_POSITIONS_SECOND", $elementPositions['second']);
            $smarty -> assign("T_POSITIONS", array_merge($elementPositions['first'], $elementPositions['second']));
        } else {
            $smarty -> assign("T_POSITIONS", array());
        }

        /*Functions list*/
        $i = 0;
        if (MODULE_HCD_INTERFACE) {
            $adminOptions[$i++]  = array('text' => _ORGANIZATION,      'image' => "32x32/factory.png",                'href' => "administrator.php?ctg=module_hcd");
            if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
                $adminOptions[$i++]  = array('text' => _EMPLOYEES,         'image' => "32x32/user1.png",                  'href' => "administrator.php?ctg=users");
            }
        } else {
            if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
                $adminOptions[$i++]  = array('text' => _USERS,             'image' => "32x32/user1.png",                  'href' => "administrator.php?ctg=users");
            }
        }
        if (!isset($currentUser -> coreAccess['lessons']) || $currentUser -> coreAccess['lessons'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _LESSONS,                'image' => "32x32/board.png",                  'href' => "administrator.php?ctg=lessons");
            $adminOptions[$i++] = array('text' => _COURSES,                'image' => "32x32/books.png",                  'href' => "administrator.php?ctg=courses");
            $adminOptions[$i++] = array('text' => _DIRECTIONS,             'image' => "32x32/kdf.png",                    'href' => "administrator.php?ctg=directions");
        }
        if (!isset($currentUser -> coreAccess['user_types']) || $currentUser -> coreAccess['user_types'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _ROLES,                  'image' => "32x32/users_family.png",           'href' => "administrator.php?ctg=user_types");
        }
        if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _GROUPS,                 'image' => "32x32/users3.png",                 'href' => "administrator.php?ctg=user_groups");
        }
        if (($_SESSION['s_version_type'] == 'Educational' || $_SESSION['s_version_type'] == 'Enterprise') && (!isset($currentUser -> coreAccess['skillgaptests']) || $currentUser -> coreAccess['skillgaptests'] != 'hidden')) {
            $adminOptions[$i++] = array('text' => _SKILLGAPTESTS, 'image' => "32x32/pda_write.png",                 'href' => "administrator.php?ctg=tests");
        }
        if (!isset($currentUser -> coreAccess['configuration']) || $currentUser -> coreAccess['configuration'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _CONFIGURATIONVARIABLES, 'image' => "32x32/pencil.png",                 'href' => "administrator.php?ctg=control_panel&op=system_config");
        }
        if (!isset($currentUser -> coreAccess['set_style']) || $currentUser -> coreAccess['set_style'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _CHANGESTYLE,            'image' => "32x32/colors.png",                 'href' => "administrator.php?ctg=control_panel&op=style");
        }
        if (!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _SENDMESSAGE,            'image' => "32x32/mail_out.png",               'href' => "forum/new_message.php");
        }
        if (!isset($currentUser -> coreAccess['logout_user']) || $currentUser -> coreAccess['logout_user'] == 'view') {
            $adminOptions[$i++] = array('text' => _LOGOUTUSER,             'image' => "32x32/exit.png",                   'href' => "logout_user.php", 'onClick' => "eF_js_showDivPopup('"._LOGOUTUSER."', new Array('600px', '200px'))", 'target' => 'POPUP_FRAME');
        }
        if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _USERSDATA,              'image' => "32x32/users1.png",                 'href' => "administrator.php?ctg=control_panel&op=users");
        }
        //$adminOptions[$i++] = array('text' => _RECREATESEARCHTABLE,    'image' => "32x32/exchange.png",               'href' => "administrator.php?ctg=control_panel&op=reindex");
        if (!isset($currentUser -> coreAccess['user_profile']) || $currentUser -> coreAccess['user_profile'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _CUSTOMIZEUSERSPROFILE,  'image' => "32x32/businessman_add.png",        'href' => "administrator.php?ctg=control_panel&op=user_profile");
        }
        if (!isset($currentUser -> coreAccess['set_logo']) || $currentUser -> coreAccess['set_logo'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _CHANGESITELOGO,         'image' => "32x32/photo_scenery.png",          'href' => "javascript:void(0)", 'onClick' => "eF_js_showDivPopup('"._CHANGESITELOGO."', new Array('400px', '200px'), 'set_logo_table')");
        }
        if (!isset($currentUser -> coreAccess['languages']) || $currentUser -> coreAccess['languages'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _LANGUAGES,              'image' => "32x32/languages.png",               'href' => "administrator.php?ctg=control_panel&op=languages");
        }
        if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _STATISTICS,             'image' => "32x32/chart.png",                  'href' => "administrator.php?ctg=statistics");
        }
        if (!isset($currentUser -> coreAccess['backup']) || $currentUser -> coreAccess['backup'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _BACKUP." - "._RESTORE,  'image' => "32x32/server_client_exchange.png", 'href' => "administrator.php?ctg=control_panel&op=backup");
        }

        //$adminOptions[14] = array('text' => _LANGUAGEADMINISTRATION, 'image' => "32x32/messages.png",               'href' => "javascript:void(0)", 'onClick' => "popUp('/set_language.php', 600, 400)");
        if (!isset($currentUser -> coreAccess['cms']) || $currentUser -> coreAccess['cms'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _CMS, 'image' => "32x32/document_text.png",          'href' => "administrator.php?ctg=cms");
        }
        /** MODULE HCD: Changing name of menu **/
        if (MODULE_HCD_INTERFACE) {
            $adminOptions[$i++]  = array('text' => _FILERECORD, 'image' => "32x32/folder_view.png",              'href' => "administrator.php?ctg=users&edit_user=".$_SESSION['s_login']."&tab=file_record");
        }
        if (!isset($currentUser -> coreAccess['maintenance']) || $currentUser -> coreAccess['maintenance'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _MAINTENANCE, 'image' => "32x32/nut_and_bolt.png",           'href' => "administrator.php?ctg=control_panel&op=maintenance");
        }
		if (!isset($currentUser -> coreAccess['forum']) || $currentUser -> coreAccess['forum'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _FORUM,              'image' => "32x32/messages.png",               'href' => "forum/forum_index.php");
        }
		if (!isset($currentUser -> coreAccess['chat']) || $currentUser -> coreAccess['chat'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _CHAT,              'image' => "32x32/user1_message.png",               'href' => "chat/chat_index.php");
        }
        if (!isset($currentUser -> coreAccess['modules']) || $currentUser -> coreAccess['modules'] != 'hidden') {
            $adminOptions[$i++] = array('text' => _MODULES, 'image' => "32x32/components.png",             'href' => "administrator.php?ctg=control_panel&op=modules");
        }
        if (MODULE_PAYPAL && (!isset($currentUser -> coreAccess['paypal']) || $currentUser -> coreAccess['paypal'] != 'hidden')) {
            if (is_file('ipn.php')) {
                $adminOptions[$i++] = array('text' => _PAYPALTITLE, 'image' => "32x32/paypal.png",             'href' => "administrator.php?ctg=control_panel&op=paypal");
            }
        }
        if (!isset($currentUser -> coreAccess['version_key']) || $currentUser -> coreAccess['version_key'] != 'hidden') {
            if ((is_file('ipn.php')) || (is_file($path.'hcd.class.php') && is_file($path.'hcd_user.class.php'))) {
                $adminOptions[$i++] = array('text' => _VERSIONKEYTITLE, 'image' => "32x32/keys.png",                 'href' => "administrator.php?ctg=control_panel&op=versionkey");
            }
        }

///MODULE2
        foreach ($loadedModules as $module) {
            if ($centerLinkInfo = $module -> getCenterLinkInfo()) {
                $adminOptions[] = array('text' => $centerLinkInfo['title'],  'image' => eF_getRelativeModuleImagePath($centerLinkInfo['image']),        'href' => $centerLinkInfo['link']);
            }

            $mainInnertableHTML = $module -> getControlPanelModule();   //**HERE**
            $innertable_modules = array();
            // If the module has a lesson innertable
            if ($mainInnertableHTML) {

                // Get module html - two ways: pure HTML or PHP+smarty
                // If no smarty file is defined then false will be returned
                if ($module_smarty_file = $module -> getControlPanelSmartyTpl()) {
                    // Execute the php code -> The code has already been executed by above (**HERE**)
                    // Let smarty know to include the module smarty file
                    $innertable_modules[$module->className] = array('smarty_file' => $module_smarty_file);
                } else {
                    // Present the pure HTML cod
                    $innertable_modules[$module->className] = array('html_code' => $mainInnertableHTML);
                }
            }
        }
        $smarty -> assign("T_INNERTABLE_MODULES", isset($innertable_modules) ? $innertable_modules : false);
        $smarty -> assign("T_ADMIN_OPTIONS", $adminOptions);                    //Use the above array to build the icons table

        /*New personal messages list*/
        if ($currentUser -> coreAccess['personal_messages'] != 'hidden') {
            $personal_messages = eF_getPersonalMessages(false, 10);                 //Get the administrator's 10 most recent messages
            $smarty -> assign("T_PERSONAL_MESSAGES", $personal_messages);           //Assign messages to smarty

            /*New forum messages list*/
            $personal_message_options = array(                                  //If the user can access the forum, assign true links
            array('text' => _GOTOINBOX,   'image' => "16x16/redo.png", 'href' => "forum/messages_index.php"),
            array('text' => _SENDMESSAGE, 'image' => "16x16/add2.png", 'href' => "forum/new_message.php", 'onClick' => "eF_js_showDivPopup('"._SENDMESSAGE."', new Array('650px', '450px'))", 'target' => "POPUP_FRAME")
            );
            $smarty -> assign("T_PERSONAL_MESSAGES_OPTIONS", $personal_message_options);    //Assign the above links to smarty, to be used as handles at the inner table header
        }

        /*New users list*/
        if ($currentUser -> coreAccess['users'] != 'hidden') {
            //$users  = eF_getTableData("users", "login, surname, name, timestamp", "active=0", "timestamp DESC");    //Find every user that is not active
            $users  = eF_getTableData("users", "login, surname, name, timestamp", "pending=1", "timestamp DESC"); //Find every user that is not active... new way
            $smarty -> assign("T_INACTIVE_USERS", $users);                                                          //Assign them to smarty, to be displayed at the first page
            $smarty -> assign("T_INACTIVE_USERS_LINK", basename($_SERVER['PHP_SELF'])."?ctg=users");
            /*New lesson registrations list*/
            $lessons = eF_getTableData("users_to_lessons ul, lessons l", "DISTINCT users_LOGIN,  count(lessons_ID) AS count", "ul.lessons_ID = l.id and l.course_only = 0 and ul.from_timestamp=0", "", "users_LOGIN");     //Get the new lesson registrations
            //$courses = eF_getTableData("users_to_courses", "DISTINCT users_LOGIN,  count(lessons_ID) AS count", "from_timestamp=0", "", "users_LOGIN");     //Get the new lesson registrations
            $smarty  -> assign("T_NEW_LESSONS", $lessons);                                                          //Assign the list to smarty, to be displayed at the first page

            
        }
        /*System announcements list*/
        $announcements = eF_getNews();
        $announcements_options[] = array('text' => _ANNOUNCEMENTGO,  'image' => "16x16/redo.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=news");
        if (!isset($currentUser -> coreAccess['news']) || $currentUser -> coreAccess['news'] == 'change') {
            $announcements_options[] = array('text' => _ANNOUNCEMENTADD, 'image' => "16x16/add2.png", 'href' => "news.php?op=insert", 'onClick' => "eF_js_showDivPopup('"._ANNOUNCEMENTADD."', 1)", 'target' => 'POPUP_FRAME');
        }
        $smarty -> assign("T_NEWS", $announcements);
        $smarty -> assign("T_NEWS_OPTIONS",$announcements_options);
        $smarty -> assign("T_NEWS_LINK", "administrator.php?ctg=control_panel&op=news");
        
        /* Calendar innertable */
        $today = getdate(time());                                                                     //Get current time in an array
        $today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);                      //Create a timestamp that is today, 00:00. this will be used in calendar for displaying today
        (eF_checkParameter($_GET['view_calendar'], 'timestamp')) ? $view_calendar = $_GET['view_calendar']: $view_calendar = $today;    //If a specific calendar date is not defined in the GET, set as the current day to be today

        $today = getdate(time());                                                                     //Get current time in an array
        $today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);                      //Create a timestamp that is today, 00:00. this will be used in calendar for displaying today
        (eF_checkParameter($_GET['view_calendar'], 'timestamp')) ? $view_calendar = $_GET['view_calendar']: $view_calendar = $today;    //If a specific calendar date is not defined in the GET, set as the current day to be today

        $smarty -> assign("T_CALENDAR_TYPE", "&type=2");
        $result = eF_getTableData("calendar","*","");
        foreach ($result as $event) {
            if (!isset($events[$event['timestamp']])) {
                $events[$event['timestamp']]['data'] = array();
                $events[$event['timestamp']]['id'] = array();
            }
            $events[$event['timestamp']]['data'][] = $event['data'];
            $events[$event['timestamp']]['id'][]   = $event['id'];
        }
        $smarty -> assign("T_CALENDAR_EVENTS", $events);                                                    //Assign events and specific day timestamp to smarty, to be used from calendar
        $smarty -> assign("T_VIEW_CALENDAR", $view_calendar);

        if (!isset($currentUser -> coreAccess['calendar']) || $currentUser -> coreAccess['content'] == 'change') {
            $calendar_options = array(                                                                          //Create calendar options and assign them to smarty, to be displayed at the calendar inner table
                    array('text' => _GOTOCALENDAR, 'image' => "16x16/redo.png", 'href' => "administrator.php?ctg=calendar"),
                    array('text' => _ADDCALENDAR,  'image' => "16x16/add2.png", 'href' => "administrator.php?ctg=calendar&add_calendar=1&view_calendar=".$view_calendar.$type_of_events, "onClick" => "eF_js_showDivPopup('"._ADDCALENDAR."', 2)", "target" => "POPUP_FRAME", "id" => "add_new_event_link"));
        } else {
            $calendar_options = array(                                                                          //Create calendar options and assign them to smarty, to be displayed at the calendar inner table
                    array('text' => _GOTOCALENDAR, 'image' => "16x16/redo.png", 'href' => "administrator.php?ctg=calendar"));
        }

        $smarty -> assign("T_CALENDAR_OPTIONS", $calendar_options);
        $smarty -> assign("T_CALENDAR_LINK", "administrator.php?ctg=calendar");
        if (isset($_GET['add_another'])) {
            $smarty -> assign('T_ADD_ANOTHER', "1");
        }

    }
}

/*
 Users is the page that concerns user administration. It uses module_personal.php to perform most of the update functions,
 since the same functions need to be performed from the professor and student as well (for themseleves)
 There are 5 sub options in this page, denoted by an extra link part:
 - &add_user=1                   When we are adding a new user
 - &delete_user=<login>          When we want to delete user <login>
 - &edit_user=<login>            When we want to edit user <login>
 - &deactivate_user=<login>      When we deactivate user <login>
 - &activate_user=<login>        When we activate user <login>
 */
elseif ($ctg == 'users') {
    if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }

    if (isset($_GET['delete_user']) && eF_checkParameter($_GET['delete_user'], 'login')) {    //The administrator asked to delete a user
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        try {
            $user = EfrontUserFactory :: factory($_GET['delete_user']);
            if ($module_hcd_interface) {
                $user -> aspects['hcd'] -> delete();
                $message      = _EMPLOYEEDELETED;
            }
            $user -> delete();
            $message      = _USERDELETED;

            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=users&message=".urlencode($message)."&message_type=success");
        } catch (Exception $e) {
            $message      = _THEUSERCOULDNOTBEDELETED.': '.$e -> getMessage().' ('.$e->getCode().')';
            $message_type = "failure";
        }
    } elseif (isset($_GET['deactivate_user']) && eF_checkParameter($_GET['deactivate_user'], 'login') && ($_GET['deactivate_user'] != $_SESSION['s_login'])) {      //The administrator asked to deactivate a user
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);exit;
        }
        try {
            $user = EfrontUserFactory :: factory($_GET['deactivate_user']);
            $user -> deactivate();
            $message = _USERACTIVATED;
        } catch (Exception $e) {
            $message      = _THEUSERCOULDNOTBEDEACTIVATED.': '.$e -> getMessage().' ('.$e->getCode().')';
            $message_type = "failure";
        }
        echo $message;exit;
    } elseif (isset($_GET['activate_user']) && eF_checkParameter($_GET['activate_user'], 'login')) {          //The administrator asked to activate a user
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);exit;
        }
        try {
            $user = EfrontUserFactory :: factory($_GET['activate_user']);
            $user -> activate();
            $message = _USERDEACTIVATED;
        } catch (Exception $e) {
            $message      = _THEUSERCOULDNOTBEACTIVATED.': '.$e -> getMessage().' ('.$e->getCode().')';
            $message_type = "failure";
        }
        echo $message;exit;
    } elseif (isset($_GET['add_user']) || (isset($_GET['edit_user']) && $login = eF_checkParameter($_GET['edit_user'], 'login'))) {   //The administrator asked to add a new user or to edit a user
        $smarty -> assign("T_PERSONAL", true);
        /**Include the personal settings file*/
        include "module_personal.php";                      //User addition and manipulation is done through module_personal.

    } else {                                                //The professor just asked to view the users
        if (!$module_hcd_interface) {
            if (isset($_GET['ajax'])) {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }

                $languages    = EfrontSystem :: getLanguages(true);
                $smarty -> assign("T_LANGUAGES", $languages);
                $users        = eF_getTableData("users", "*");
                $user_lessons = eF_getTableDataFlat("users_to_lessons", "users_LOGIN, count(lessons_ID) as lessons_num", "", "", "users_LOGIN");
				$user_courses = eF_getTableDataFlat("users_to_courses", "users_LOGIN, count(courses_ID) as courses_num", "", "", "users_LOGIN");
				$user_groups = eF_getTableDataFlat("users_to_groups", "users_LOGIN, count(groups_ID) as groups_num", "", "", "users_LOGIN");
                $user_lessons = array_combine($user_lessons['users_LOGIN'], $user_lessons['lessons_num']);
				$user_courses = array_combine($user_courses['users_LOGIN'], $user_courses['courses_num']);
				$user_groups = array_combine($user_groups['users_LOGIN'], $user_groups['groups_num']);

                array_walk($users, create_function('&$v, $k, $s', '$s[$v["login"]] ? $v["lessons_num"] = $s[$v["login"]] : $v["lessons_num"] = 0;'), $user_lessons);      //Assign lessons number to users array (this way we eliminate the need for an expensive explicit loop)
				array_walk($users, create_function('&$v, $k, $s', '$s[$v["login"]] ? $v["courses_num"] = $s[$v["login"]] : $v["courses_num"] = 0;'), $user_courses);    
				array_walk($users, create_function('&$v, $k, $s', '$s[$v["login"]] ? $v["groups_num"] = $s[$v["login"]] : $v["groups_num"] = 0;'), $user_groups);    

				$users = eF_multiSort($users, $sort, $order);
                if (isset($_GET['filter'])) {
                    $users = eF_filterData($users, $_GET['filter']);
                }
                $smarty -> assign("T_USERS_SIZE", sizeof($users));

                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $users = array_slice($users, $offset, $limit);
                }
				
                $smarty -> assign("T_USERS", $users);
                $smarty -> assign("T_ROLES", EfrontUser :: getRoles(true));
                $smarty -> display('administrator.tpl');
                exit;
            }
        } else {
            $_GET['op'] = "employees";
            include "module_hcd.php";
        }
    }

}
/*
 Lessons is the page that concerns lesson administration. Here the administrator can view, add, delete and modify lessons
 There are 5 sub options in this page, denoted by an extra link part:
 - &add_lesson=1                       When we are adding a new lesson
 - &delete_lesson=<lesson_ID>          When we want to delete lesson <lesson_ID>
 - &edit_lesson=<lesson_ID>            When we want to edit lesson <lesson_ID>
 - &deactivate_lesson=<lesson_ID>      When we deactivate lesson <lesson_ID>
 - &activate_lesson=<lesson_ID>        When we activate lesson <lesson_ID>
 */
elseif ($ctg == 'lessons') {
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }

    if (isset($_GET['delete_lesson']) && eF_checkParameter($_GET['delete_lesson'], 'id')) {       //The administrator asked to delete a lesson
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            exit;
        }
        try {
            $lesson = new EfrontLesson($_GET['delete_lesson']);
            $lesson -> delete();
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=lessons&message=".urlencode(_LESSONSDELETED)."&message_type=success");
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = "failure";
        }
    } elseif (isset($_GET['deactivate_lesson']) && eF_checkParameter($_GET['deactivate_lesson'], 'id')) {     //The administrator asked to deactivate a lesson
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'hidden') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $lesson = new EfrontLesson($_GET['deactivate_lesson']);
            $lesson -> deactivate();
            $message = urlencode(_LESSONDEACTIVATED);
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = "failure";
        }
        echo $message;exit;
    } elseif (isset($_GET['activate_lesson']) && eF_checkParameter($_GET['activate_lesson'], 'id')) {                //The administrator asked to activate a lesson
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $lesson = new EfrontLesson($_GET['activate_lesson']);
            $lesson -> activate();
            $message = urlencode(_LESSONACTIVATED);
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = "failure";
        }
        echo $message;exit;
    } elseif (isset($_GET['unset_course_only']) && eF_checkParameter($_GET['unset_course_only'], 'id')) {     //The administrator asked to deactivate a lesson
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $lesson = new EfrontLesson($_GET['unset_course_only']);
            $lesson -> lesson['course_only'] = 0;

            $lesson -> insertLessonSkill();

            $lesson -> persist();
            $message = urlencode(_LESSONOPTIONSET);
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = "failure";
        }
        echo $message;exit;
    } elseif (isset($_GET['set_course_only']) && eF_checkParameter($_GET['set_course_only'], 'id')) {                //The administrator asked to activate a lesson
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $lesson = new EfrontLesson($_GET['set_course_only']);
            $lesson -> lesson['course_only'] = 1;
            $lesson -> deleteLessonSkill();

            $lesson -> persist();
            $message = urlencode(_LESSONOPTIONSET);
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = "failure";
        }
        echo $message;exit;
    } elseif (isset($_GET['add_lesson']) || (isset($_GET['edit_lesson']) && eF_checkParameter($_GET['edit_lesson'], 'id'))) {        //The administrator asked to add or edit a lesson
        $loadScripts[] = 'scriptaculous/scriptaculous';
        $loadScripts[] = 'scriptaculous/effects';

        isset($_GET['add_lesson']) ? $post_target = 'add_lesson=1' : $post_target = 'edit_lesson='.$_GET['edit_lesson'];            //Set the form post target in correspondance to the current function we are performing

        $form = new HTML_QuickForm("add_lessons_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=lessons&".$post_target, "", null, true);  //Build the form
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                                                   //Register our custom input check function
        $form -> addElement('text', 'name', _LESSONNAME, 'class = "inputText"');                    //The lesson name, it is required and of type 'text'

        $form -> addRule('name', _THEFIELD.' "'._LESSONNAME.'" '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');
        if ($GLOBALS['configuration']['onelanguage'] != true){
            $form -> addElement('select', 'languages_NAME', _LANGUAGE, EfrontSystem :: getLanguages(true));  //Add a language select box to the form
        }

        try {                                                                //If there are no direction set, redirect to add direction page
            $directionsTree = new EfrontDirectionsTree();
            if (sizeof($directionsTree -> tree) == 0) {
                header("location:".basename($_SERVER['PHP_SELF']).'?ctg=directions&add_direction=1&message='.urlencode(_YOUMUSTFIRSTCREATEDIRECTION).'&message_type=failure');
            }
            $form -> addElement('select', 'directions_ID', _DIRECTION, $directionsTree -> toPathString());                    //Append a directions select box to the form
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = 'failure';
        }

        $form -> addElement('text', 'price', _PRICE, 'class = "inputText" style = "width:50px"');                        //Add the price, active and submit button to the form
        $form -> addElement('advcheckbox', 'active', _ACTIVENEUTRAL, null, null, array(0, 1));
        $form -> addElement('radio', 'course_only', _LESSONAVAILABLE, _COURSEONLY, 1);
        $form -> addElement('radio', 'course_only', _LESSONAVAILABLE, _DIRECTLY, 0);

        $recurringOptions = array(0 => _NO, 'day' => _DAY, 'week' => _WEEK, 'month' => _MONTH, 'year' => _YEAR);
        $form -> addElement('select', 'recurring', _RECURRINGPAYMENT, $recurringOptions);  //Add a language select box to the form
        
        if (isset($_GET['edit_lesson'])) {                                                          //If we are editing a lesson, we set the default form values to the ones stored in the database
            $editLesson = new EfrontLesson($_GET['edit_lesson']);
            $form -> setDefaults(array('name'           => $editLesson -> lesson['name'],
                                       'active'         => $editLesson -> lesson['active'],
                                       'course_only'    => $editLesson -> lesson['course_only'],
                                       'directions_ID'  => $editLesson -> lesson['directions_ID'],
                                       'languages_NAME' => $editLesson -> lesson['languages_NAME'],
                                       'price'          => $editLesson -> lesson['price'],
                                       'recurring'      => $editLesson -> options['recurring']));
            $smarty -> assign("T_EDIT_LESSON", $editLesson -> lesson);
        } else {
            $form -> addElement('file', 'import_content', _UPLOADLESSONFILE, 'class = "inputText"');
            $form -> setDefaults(array('active'         => 1,                                              //For a new lesson, by default active is set to 1 and price to 0
                                       'price'          => 0,
                                       'course_only'    => 0,
                                       'languages_NAME' => $GLOBALS['configuration']['default_language']));
        }

        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            $form -> freeze();
        } else {
            $form -> addElement('submit', 'submit_lesson', _SUBMIT, 'class = "flatButton"');

            if ($form -> isSubmitted() && $form -> validate()) {                        //If the form is submitted and validated
                $GLOBALS['configuration']['onelanguage'] == true ? $languages_NAME = $GLOBALS['configuration']['default_language']: $languages_NAME = $form -> exportValue('languages_NAME');
                if (isset($_GET['add_lesson'])) {                                             //The second case is when the administrator adds a new lesson
                    $fields_insert = array( 'name'           => $form -> exportValue('name'),
                                       		'languages_NAME' => $languages_NAME,
                                       		'directions_ID'  => $form -> exportValue('directions_ID'),
                                       		'active'         => $form -> exportValue('active'),
                                       		'course_only'    => $form -> exportValue('course_only') == '' ? 0 : $form -> exportValue('course_only'),
                                       		'price'          => $form -> exportValue('price'));

                    try {
                        $newLesson = EfrontLesson :: createLesson($fields_insert);
                        if ($form -> exportValue('price') && $form -> exportValue('recurring') && in_array($form -> exportValue('recurring'), array_keys($recurringOptions))) {
                            $newLesson -> options['recurring'] = $form -> exportValue('recurring');
                            $newLesson -> persist();
                        } 
                        try {
                            $filesystem   = new FileSystemTree($newLesson -> getDirectory());
                            $uploadedFile = $filesystem -> uploadFile('import_content', $newLesson -> getDirectory());
                            $newLesson   -> import($uploadedFile);
                        } catch (Exception $e) {}
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=lessons&edit_lesson=".($newLesson -> lesson['id'])."&tab=users&message=".urlencode(_SUCCESSFULLYCREATEDLESSON)."&message_type=success");
                    } catch (EfrontLessonException $e) {
                        $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
                        $message_type = 'failure';
                    }
                } elseif (isset($_GET['edit_lesson'])) {                                                  //The first case is when the administrator is editing a lesson
                    $fields_update = array( 'name'           => $form -> exportValue('name'),
                                       		'directions_ID'  => $form -> exportValue('directions_ID'),
                                       		'languages_NAME' => $languages_NAME,
                                       		'active'         => $form -> exportValue('active'),
                                       		'course_only'    => $form -> exportValue('course_only'),
                                       		'price'          => $form -> exportValue('price'));
                    $editLesson -> lesson = array_merge($editLesson -> lesson, $fields_update);
                    if ($form -> exportValue('price') && $form -> exportValue('recurring') && in_array($form -> exportValue('recurring'), array_keys($recurringOptions))) {
                        $editLesson -> options['recurring'] = $form -> exportValue('recurring');
                    } else {
                        unset($editLesson -> options['recurring']);
                    }
                    try {
                        $editLesson -> persist();
                        $lesson_forum = eF_getTableData("f_forums", "id", "lessons_ID=".$_GET['edit_lesson']);                  //update lesson's forum and chat names as well
                        if (sizeof($lesson_forum) > 0) {
                            eF_updateTableData("f_forums", array('title' => $form -> exportValue('name')), "id=".$lesson_forum[0]['id']);
                        }
                        $lesson_chat = eF_getTableData("chatrooms", "id", "lessons_ID=".$_GET['edit_lesson']);
                        if (sizeof($lesson_chat) > 0) {
                            eF_updateTableData("chatrooms", array('name' => $form -> exportValue('name')), "id=".$lesson_chat[0]['id']);
                        }
                        //header("location:".basename(basename($_SERVER['PHP_SELF'])).'?ctg=lessons&message='.urlencode(_LESSONUPDATED).'&message_type=success');
                    } catch (Exception $e) {
                        $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
                        $message_type = 'failure';
                    }
                }
            }

        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer
        $renderer -> setRequiredTemplate (
               '{$html}{if $required}
                    &nbsp;<span class = "formRequired">*</span>
                {/if}');

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

        $smarty -> assign('T_LESSON_FORM', $renderer -> toArray());                     //Assign the form to the template

        /** MODULE HCD: Submission of skills **/
        /*******************************************
         SUBMISSION OF SKILLS (LESSON TO SKILLS)
         *******************************************/
        if (MODULE_HCD_INTERFACE) {
            /* Ajax assignments/removals of the skill to employees */
            if ($_GET['postAjaxRequest'] && isset($_GET['add_skill'])) {

                /* Find all employees having this skill */
                if ($_GET['insert'] == "true") {
                    $editLesson -> assignSkill($_GET['add_skill'], $_GET['specification']);
                } else if ($_GET['insert'] == "false") {
                    $editLesson -> removeSkill($_GET['add_skill']);
                } else if (isset($_GET['addAll'])) {
                    $skills = $editLesson -> getSkills();
                    foreach ($skills as $skill) {
                        if ($skill['lesson_ID'] == "") {
                            $editLesson -> assignSkill($skill['skill_ID'], "");
                        }
                    }
                } else if (isset($_GET['removeAll'])) {
                    $skills = $editLesson -> getSkills();
                    foreach ($skills as $skill) {
                        if ($skill['lesson_ID'] == $editLesson -> lesson['id']) {
                            $editLesson -> removeSkill($skill['skill_ID']);
                        }
                    }
                }
                exit;
            }
        }

        if (isset($_GET['edit_lesson'])) {                                          //If we are editing a lesson, get the information needed to build the users to lesson list
            try {

                /** MODULE HCD: Get all skills this lesson has to offer **/
                if(MODULE_HCD_INTERFACE) {
                    $skills = $editLesson -> getSkills();
                    if (isset($_GET['ajax']) && $_GET['ajax'] == 'skillsTable') {
                        isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                        if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                            $sort = $_GET['sort'];
                            isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                        } else {
                            $sort = 'description';
                        }

                        $skills = eF_multiSort($skills, $sort, $order);
                        $smarty -> assign("T_SKILLS_SIZE", sizeof($skills));
                        if (isset($_GET['filter'])) {
                            $skills = eF_filterData($skills, $_GET['filter']);
                        }
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $skills = array_slice($skills, $offset, $limit);
                        }

                        if (!empty($skills)) {
                            $smarty -> assign("T_SKILLS", $skills);
                        }
                        $smarty -> display('administrator.tpl');
                        exit;
                    } else {

                        if (!empty($skills)) {
                            $smarty -> assign("T_SKILLS", $skills);
                            $smarty -> assign("T_SKILLS_SIZE", sizeof($skills));
                        }
                    }
                }

                $lessonUsers    = $editLesson -> getUsers();                        //Get all users that have this lesson
                $nonLessonUsers = $editLesson -> getNonUsers();                     //Get all the users that can, but don't, have this lesson

                $users = array_merge($lessonUsers, $nonLessonUsers);       //Merge users to a single array, which will be useful for displaying them

                $roles = EfrontLessonUser :: getLessonsRoles(true);
                //$roles = eF_getTableDataFlat("user_types", "*", "active=1 AND basic_user_type!='administrator'");    //Get available roles
                //sizeof($roles) > 0 ? $roles = array_combine($roles['id'], $roles['name']) : $roles = array();                                             //Match keys with values, it's more practical this way
                $roles = array('student' => _STUDENT, 'professor' => _PROFESSOR) + $roles;                     //Append basic user types to the beginning of the array

                if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
                    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                        $sort = $_GET['sort'];
                        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                    } else {
                        $sort = 'login';
                    }
                    $users = eF_multiSort($users, $sort, $order);
                    $smarty -> assign("T_USERS_SIZE", sizeof($users));
                    if (isset($_GET['filter'])) {
                        $users = eF_filterData($users, $_GET['filter']);
                    }
                    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                        $users = array_slice($users, $offset, $limit);
                    }

                    $smarty -> assign("T_ROLES", $roles);
                    $smarty -> assign("T_ALL_USERS", $users);
                    $smarty -> assign("T_LESSON_USERS", array_keys($lessonUsers));                                             //We assign separately the lesson's users, to know when to display the checkboxes as "checked"
                    $smarty -> display('administrator.tpl');
                    exit;
                }
            } catch (Exception $e) {
                $message      = $e -> getMessage().' ('.$e -> getCode().')';
                $message_type = 'failure';
            }


            if (isset($_GET['postAjaxRequest'])) {
                try {
                    if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
                        isset($_GET['user_type']) && in_array($_GET['user_type'], array_keys($roles)) ? $userType = $_GET['user_type'] : $userType = 'student';
                        if (in_array($_GET['login'], array_keys($nonLessonUsers))) {
                            $editLesson -> addUsers($_GET['login'], $userType);
                        }
                        if (in_array($_GET['login'], array_keys($lessonUsers))) {
                            $userType != $lessonUsers[$_GET['login']]['role'] ? $editLesson -> setRoles($_GET['login'], $userType) : $editLesson -> removeUsers($_GET['login']);
                        }
                    } else if (isset($_GET['addAll'])) {
                        $userTypes = array();
                        foreach ($nonLessonUsers as $user) {
                            $user['user_types_ID'] ? $userTypes[] = $user['user_types_ID'] : $userTypes[] = $user['basic_user_type'];
                        }
                        $editLesson -> addUsers(array_keys($nonLessonUsers), $userTypes);
                    } else if (isset($_GET['removeAll'])) {
                        $editLesson -> removeUsers(array_keys($lessonUsers));
                    }
                    exit;
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            }
        }
    } else if (isset($_GET['lesson_info']) && eF_checkParameter($_GET['lesson_info'], 'id')) {
        $loadScripts[] = 'scriptaculous/scriptaculous';                            //Load effects to be used on ajax users assignment
        $loadScripts[] = 'scriptaculous/effects';
        $form = new HTML_QuickForm("empty_form", "post", null, null, null, true);

        try {
            $currentLesson = new EfrontLesson($_GET['lesson_info']);
            $smarty -> assign("T_CURRENT_LESSON", $currentLesson);

            $lessonInformation = unserialize($currentLesson -> lesson['info']);
            $information       = new LearningObjectInformation($lessonInformation);

            $lessonMetadata = unserialize($currentLesson -> lesson['metadata']);
            $metadata       = new DublinCoreMetadata($lessonMetadata);
            if (!isset($currentUser -> coreAccess['lessons']) || $currentUser -> coreAccess['lessons'] == 'change') {
                $smarty -> assign("T_LESSON_INFO_HTML", $information -> toHTML($form, false));
                $smarty -> assign("T_LESSON_METADATA_HTML", $metadata -> toHTML($form));
            } else {
                $smarty -> assign("T_LESSON_INFO_HTML", $information -> toHTML($form, false, false));
                $smarty -> assign("T_LESSON_METADATA_HTML", $metadata -> toHTML($form, true, false));
            }
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = "failure";
        }

        if (isset($_GET['postAjaxRequest'])) {
            if (in_array($_GET['dc'], array_keys($information -> metadataAttributes))) {
                if ($_GET['value']) {
                    $lessonInformation[$_GET['dc']] = $_GET['value'];
                } else {
                    unset($lessonInformation[$_GET['dc']]);
                }
                $currentLesson -> lesson['info'] = serialize($lessonInformation);
            } elseif (in_array($_GET['dc'], array_keys($metadata -> metadataAttributes))) {
                if ($_GET['value']) {
                    $lessonMetadata[$_GET['dc']] = $_GET['value'];
                } else {
                    unset($lessonMetadata[$_GET['dc']]);
                }
                $currentLesson -> lesson['metadata'] = serialize($lessonMetadata);
            }

            $currentLesson -> persist();
            echo $_GET['value'];
            exit;
        }
    } else if (isset($_GET['lesson_settings']) && eF_checkParameter($_GET['lesson_settings'], 'id')) {
        $currentLesson = new EfrontLesson($_GET['lesson_settings']);
        $smarty -> assign("T_CURRENT_LESSON", $currentLesson);

        if (!isset($currentUser -> coreAccess['lessons']) || $currentUser -> coreAccess['lessons'] == 'change') {
            $options = array(
                        array('image' => '16x16/gear.png',    'title' => _LESSONOPTIONS, 'link' => basename(basename($_SERVER['PHP_SELF'])).'?ctg=lessons&lesson_settings='.$currentLesson -> lesson['id']                    , 'selected' => !isset($_GET['op'])                                  ? true : false),
                        array('image' => '16x16/refresh.png', 'title' => _RESTARTLESSON, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=lessons&lesson_settings='.$currentLesson -> lesson['id'].'&op=reset_lesson' , 'selected' => isset($_GET['op']) && $_GET['op'] == 'reset_lesson'  ? true : false),
                        array('image' => '16x16/import2.png', 'title' => _IMPORTLESSON,  'link' => basename($_SERVER['PHP_SELF']).'?ctg=lessons&lesson_settings='.$currentLesson -> lesson['id'].'&op=import_lesson', 'selected' => isset($_GET['op']) && $_GET['op'] == 'import_lesson' ? true : false),
                        array('image' => '16x16/export1.png', 'title' => _EXPORTLESSON,  'link' => basename($_SERVER['PHP_SELF']).'?ctg=lessons&lesson_settings='.$currentLesson -> lesson['id'].'&op=export_lesson', 'selected' => isset($_GET['op']) && $_GET['op'] == 'export_lesson' ? true : false));
        } else {
            $options = array(array('image' => '16x16/gear.png', 'title' => _LESSONOPTIONS, 'link' => basename(basename($_SERVER['PHP_SELF'])).'?ctg=lessons&lesson_settings='.$currentLesson -> lesson['id'], 'selected' => !isset($_GET['op']) ? true : false));
        }
        $smarty -> assign("T_TABLE_OPTIONS", $options);

        if ($_GET['op'] == 'reset_lesson') {
            if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                exit;
            }
            /*Reset lesson part*/
            $form = new HTML_QuickForm("reset_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lessons&lesson_settings='.$currentLesson -> lesson['id'].'&op=reset_lesson', "", null, true);
            $form -> addElement('checkbox', 'users',    null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson students
            $form -> addElement('checkbox', 'news',     null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson announcements
            $form -> addElement('checkbox', 'comments', null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson comments
            $form -> addElement('checkbox', 'rules',    null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson rules
            $form -> addElement('checkbox', 'calendar', null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson calendar
            $form -> addElement('checkbox', 'glossary', null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson glossary
            $form -> addElement('checkbox', 'tracking', null, null, 'class = "inputCheckBox"');         //Whether to delete the lesson tracking information
            $form -> addElement('submit', 'submit_reset_lesson', _SUBMIT, 'class = "flatButton"');

            if ($form -> isSubmitted() && $form -> validate()) {
                $currentLesson -> initialize(array_keys($form -> exportValues()));

                $message      = _RESTARTLESSONCOMPLETED;
                $message_type = 'success';
            }

            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);
            $smarty -> assign('T_RESET_LESSON_FORM', $renderer -> toArray());
        } elseif ($_GET['op'] == 'import_lesson') {
            if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                exit;
            }
            /* Import part */
            $form = new HTML_QuickForm("import_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lessons&lesson_settings='.$currentLesson -> lesson['id'].'&op=import_lesson', "", null, true);
/*
            $form -> addElement('checkbox', 'content',  null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson content
            $form -> addElement('checkbox', 'periods',  null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson periods
            $form -> addElement('checkbox', 'files',    null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson files
            $form -> addElement('checkbox', 'users',    null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson students
            $form -> addElement('checkbox', 'news',     null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson announcements
            $form -> addElement('checkbox', 'comments', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson comments
            $form -> addElement('checkbox', 'rules',    null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson rules
            $form -> addElement('checkbox', 'calendar', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson calendar
            $form -> addElement('checkbox', 'glossary', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson glossary
            $form -> addElement('checkbox', 'tracking', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson tracking information
            $form -> addElement('checkbox', 'surveys',  null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson surveys
*/
            $form -> addElement('file', 'file_upload', null, 'class = "inputText"');                    //Lesson file
            $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);            //getUploadMaxSize returns size in KB
            $form -> addElement('submit', 'submit_import_lesson', _SUBMIT, 'class = "flatButton"');

            $smarty -> assign("T_MAX_FILESIZE", FileSystemTree :: getUploadMaxSize());

            if ($form -> isSubmitted() && $form -> validate()) {
                try {
                    //$lesson       = new EfrontLesson($_SESSION['s_lessons_ID']);
                    //$directory      = new EfrontDirectory($currentLesson -> getDirectory());                    //the directory to upload the file to.
                    $currentLesson -> initialize('all');
                    $filesystem     = new FileSystemTree($currentLesson -> getDirectory());
                    $uploadedFile   = $filesystem -> uploadFile('file_upload', $currentLesson -> getDirectory());
                    $currentLesson -> import($uploadedFile);

                    $message      = _LESSONIMPORTEDSUCCESFULLY;
                    $message_type = 'success';
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _PROBLEMIMPORTINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }

            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);
            $smarty -> assign('T_IMPORT_LESSON_FORM', $renderer -> toArray());
        } elseif ($_GET['op'] == 'export_lesson') {
            if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                exit;
            }

            /* Export part */
            $form = new HTML_QuickForm("export_lesson_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lessons&lesson_settings='.$currentLesson -> lesson['id'].'&op=export_lesson', "", null, true);
/*
            $form -> addElement('checkbox', 'periods',  null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson periods
            $form -> addElement('checkbox', 'news',     null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson announcements
            $form -> addElement('checkbox', 'comments', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson comments
            $form -> addElement('checkbox', 'rules',    null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson rules
            $form -> addElement('checkbox', 'calendar', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson calendar
            $form -> addElement('checkbox', 'glossary', null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson glossary
            $form -> addElement('checkbox', 'surveys',  null, null, 'class = "inputCheckBox" checked');         //Whether to delete the lesson surveys
*/
            $form -> addElement('submit', 'submit_export_lesson', _EXPORT, 'class = "flatButton"');

            try {
                $currentExportedFile = new EfrontFile($currentUser -> user['directory'].'/temp/'.EfrontFile :: encode($currentLesson -> lesson['name']).'.zip');
                $smarty -> assign("T_EXPORTED_FILE", $currentExportedFile);
            } catch (Exception $e) {}

            if ($form -> isSubmitted() && $form -> validate()) {
                try {
                    //$lesson = new EfrontLesson($_SESSION['s_lessons_ID']);
                    $file = $currentLesson -> export(array_keys($form -> exportValues()));
                    $smarty -> assign("T_NEW_EXPORTED_FILE", $file);

                    $message      = _LESSONEXPORTEDSUCCESFULLY;
                    $message_type = 'success';
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                }
            }

            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);
            $smarty -> assign('T_EXPORT_LESSON_FORM', $renderer -> toArray());

        } else {
            $lessonSettings['theory']          = array('text' => _THEORY,            'image' => isset($currentLesson -> options['theory'])          && $currentLesson -> options['theory']          ? "32x32/book_blue.png"     : "32x32/book_blue_gray.png",     'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'theory\')',          'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['examples']        = array('text' => _EXAMPLES,          'image' => isset($currentLesson -> options['examples'])        && $currentLesson -> options['examples']        ? "32x32/lightbulb_on.png"  : "32x32/lightbulb_on_gray.png",  'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'examples\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['projects']        = array('text' => _PROJECTS,          'image' => isset($currentLesson -> options['projects'])        && $currentLesson -> options['projects']        ? "32x32/exercises.png"     : "32x32/exercises_gray.png",     'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'projects\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['tests']           = array('text' => _TESTS,             'image' => isset($currentLesson -> options['tests'])           && $currentLesson -> options['tests']           ? "32x32/document_edit.png" : "32x32/document_edit_gray.png", 'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'tests\')',           'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['survey']          = array('text' => _SURVEY,            'image' => isset($currentLesson -> options['survey'])          && $currentLesson -> options['survey']          ? "32x32/form_green.png"    : "32x32/form_green_gray.png",    'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'survey\')',          'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['rules']           = array('text' => _ACCESSRULES,       'image' => isset($currentLesson -> options['rules'])           && $currentLesson -> options['rules']           ? "32x32/recycle.png"       : "32x32/recycle_gray.png",       'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'rules\')',           'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['forum']           = array('text' => _FORUM,             'image' => isset($currentLesson -> options['forum'])           && $currentLesson -> options['forum']           ? "32x32/messages.png"      : "32x32/messages_gray.png",      'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'forum\')',           'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['comments']        = array('text' => _COMMENTS,          'image' => isset($currentLesson -> options['comments'])        && $currentLesson -> options['comments']        ? "32x32/note.png"          : "32x32/note_gray.png",          'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'comments\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['online']          = array('text' => _USERSONLINE,       'image' => isset($currentLesson -> options['online'])          && $currentLesson -> options['online']          ? "32x32/users4.png"        : "32x32/users4_gray.png",        'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'online\')',          'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
	            
	        $chat_enabled = eF_getTableData("configuration", "value", "name ='chat_enabled'");
	        if ($chat_enabled[0]['value'] == 1) {
                $lessonSettings['chat']            = array('text' => _CHAT,              'image' => isset($currentLesson -> options['chat'])            && $currentLesson -> options['chat']            ? "32x32/user1_message.png" : "32x32/user1_message_gray.png", 'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'chat\')',            'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
	        }    
            $lessonSettings['scorm']           = array('text' => _SCORM,             'image' => isset($currentLesson -> options['scorm'])           && $currentLesson -> options['scorm']           ? "32x32/book_red.png"      : "32x32/book_red_gray.png",      'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'scorm\')',           'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            //$lessonSettings['dynamic_periods'] = array('text' => _PERIODSPERSTUDENT, 'image' => isset($currentLesson -> options['dynamic_periods']) && $currentLesson -> options['dynamic_periods'] ? "32x32/user1_time.png"    : "32x32/user1_time_gray.png",    'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'dynamic_periods\')', 'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['digital_library'] = array('text' => _DIGITALLIBRARY,    'image' => isset($currentLesson -> options['digital_library']) && $currentLesson -> options['digital_library'] ? "32x32/disk_blue.png"     : "32x32/disk_blue_gray.png",     'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'digital_library\')', 'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['calendar']        = array('text' => _CALENDAR,          'image' => isset($currentLesson -> options['calendar'])        && $currentLesson -> options['calendar']        ? "32x32/calendar.png"      : "32x32/calendar_gray.png",      'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'calendar\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['new_content']     = array('text' => _NEWCONTENT,        'image' => isset($currentLesson -> options['new_content'])     && $currentLesson -> options['new_content']     ? "32x32/book_blue_new.png" : "32x32/book_blue_new_gray.png", 'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'new_content\')',     'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['glossary']        = array('text' => _GLOSSARY,          'image' => isset($currentLesson -> options['glossary'])        && $currentLesson -> options['glossary']        ? "32x32/book_open2.png"    : "32x32/book_open2_gray.png",    'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'glossary\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            $lessonSettings['auto_complete']   = array('text' => _AUTOCOMPLETE,      'image' => isset($currentLesson -> options['auto_complete'])   && $currentLesson -> options['auto_complete']   ? "32x32/book_green.png"    : "32x32/book_green_gray.png",    'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'auto_complete\')',   'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            //$lessonSettings['close_sidebar']   = array('text' => _AUTOCOMPLETE,      'image' => isset($currentLesson -> options['auto_complete'])   && $currentLesson -> options['auto_complete']   ? "32x32/book_green.png"    : "32x32/book_green_gray.png",    'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'auto_complete\')',   'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
            //$lessonSettings['tracking']        = array('text' => _TRACKING,          'image' => isset($currentLesson -> options['tracking'])        && $currentLesson -> options['tracking']        ? "32x32/dot-chart.png"     : "32x32/dot-chart_gray.png",     'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \'tracking\')',        'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);


            $candidateLessonModules = eF_loadAllModules();
            ///MODULES6
            foreach ($candidateLessonModules as $module) {
                if ($module -> isLessonModule()) {
                    $mod_lang_file = $module -> getLanguageFile($setLanguage);
                    if (is_file ($mod_lang_file)) {
                        require_once $mod_lang_file;
                    }
                    $lessonSettings[$module -> className] = array('text' => $module -> getName(), 'image' => ($currentLesson -> options[$module -> className] == 1) ? "32x32/component_green.png"  : "32x32/component_green_gray.png", 'href' => 'javascript:void(0)', 'onClick' => 'activate(this, \''.$module -> className.'\')', 'style' => 'color:gray', 'title' => _CLICKTOTOGGLE);
                }
            }

            foreach ($currentLesson -> options as $key => $value) {                                               //Remove activated elements from above list
                if ($value && isset($lessonSettings[$key])) {
                    $lessonSettings[$key]['onClick'] = 'activate(this, \''.$key.'\')';
                    $lessonSettings[$key]['style']   = 'color:inherit';
                }
            }

            //If the administrator's type restricts access to settings, unset all 'onclick' actions
            if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                foreach ($lessonSettings as $key => $value) {
                    $lessonSettings[$key]['onClick'] = '';
                }
            }

            $smarty -> assign("T_LESSON_SETTINGS", $lessonSettings);

            if (isset($_GET['ajax']) && isset($_GET['activate']) && in_array($_GET['activate'], array_keys($lessonSettings))) {
                try {
                    $currentLesson -> options[$_GET['activate']] = 1;
                    $currentLesson -> persist();
                    if ($currentLesson -> options['digital_library'] == 1) {                        //If the professor set a digital library, create the corresponding if folder, if it does not exist
                        if (!is_dir(G_LESSONSPATH.$currentLesson -> lesson['id']."/"."Digital Library"))
                        @mkdir(G_LESSONSPATH.$currentLesson -> lesson['id']."/"."Digital Library");
                    }
                    echo "Option activated";
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            } elseif (isset($_GET['ajax']) && isset($_GET['deactivate']) && in_array($_GET['deactivate'], array_keys($lessonSettings))) {
                try {
                    $currentLesson -> options[$_GET['deactivate']] = 0;
                    $currentLesson -> persist();
                    echo "Option deactivated";
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            }
        }
    } else {                                            //The default action is to just print a list with the lessons defined in the system
        $lessons = EFrontLesson :: getLessons(true);
        $directionsTree = new EfrontDirectionsTree();
        $directionPaths = $directionsTree -> toPathString();
        $languages      = EfrontSystem :: getLanguages(true);

        if ($module_hcd_interface) {
            $result  = eF_getTableDataFlat("lessons LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_lesson_offers_skill.lesson_ID = lessons.id","lessons.id, count(skill_ID) as skills_offered","","","id");
            foreach ($result['id'] as $key => $lesson_id) {
                $lessons[$lesson_id]['skills_offered'] = $result['skills_offered'][$key];
            }
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] == 'lessonsTable') {
            isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
            if (isset($_GET['sort'])) {
                isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                $lessons = eF_multiSort($lessons, $_GET['sort'], $order);
            }
            if (isset($_GET['filter'])) {
                $lessons = eF_filterData($lessons, $_GET['filter']);
            }
            $smarty -> assign("T_LESSONS_SIZE", sizeof($lessons));
            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $lessons = array_slice($lessons, $offset, $limit);
            }

            foreach ($lessons as $key => $lesson) {
                $obj = new EfrontLesson($lesson);
                $lessons[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=lessons&edit_lesson='.$lesson['id']);
                $lessons[$key]['direction_name'] = $directionPaths[$lesson['directions_ID']];
                $lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
            }
            $smarty -> assign("T_LESSONS_DATA", $lessons);

            $smarty -> display('administrator.tpl');
            exit;
        }
    }

}
elseif ($ctg == 'tests') {
    require_once "module_tests.php";
}
/*
 Directions is the page that concerns direction administration. Here the administrator can view, add, delete and modify directions
 There are 5 sub options in this page, denoted by an extra link part:
 - &add_direction=1                       When we are adding a new direction
 - &delete_direction=<direction_ID>          When we want to delete direction <direction_ID>
 - &edit_direction=<direction_ID>            When we want to edit direction <direction_ID>
 - &deactivate_direction=<direction_ID>      When we deactivate direction <direction_ID>
 - &activate_direction=<direction_ID>        When we activate direction <direction_ID>
 */
elseif ($ctg == 'directions') {
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }

    if (isset($_GET['delete_direction']) && eF_checkParameter($_GET['delete_direction'], 'id')) {
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        try {
            $direction = new EfrontDirection($_GET['delete_direction']);
            if (sizeof($direction -> getLessons(false, true)) > 0) {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=directions&message=".urlencode(_YOUMUSTDELETEALLLESSONSANDSUBDIRECTIONSINTHISDIRECTIONBEFOREDELETINGIT)."&message_type=failure");
            } else {
                $direction -> delete();
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=directions&message=".urlencode(_DIRECTIONDELETED)."&message_type=success");
            }
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = 'failure';
        }

    } elseif (isset($_GET['deactivate_direction']) && eF_checkParameter($_GET['deactivate_direction'], 'id')) {
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $direction = new EfrontDirection($_GET['deactivate_direction']);
            if (sizeof($direction -> getLessons(false, true)) > 0) {
                $message     = _YOUMUSTDELETEALLLESSONSANDSUBDIRECTIONSINTHISDIRECTIONBEFOREDEACTIVATINGIT;
                $message_type = 'failure';
            } else {
                $direction['active'] = 0;
                $direction -> persist();
                $message = urlencode(_DIRECTIONDEACTIVATED);
            }
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = 'failure';
        }
        echo $message; exit;
    } elseif (isset($_GET['activate_direction']) && eF_checkParameter($_GET['activate_direction'], 'id')) {
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $direction = new EfrontDirection($_GET['activate_direction']);
            $direction['active'] = 1;
            $direction -> persist();
            $message = urlencode(_DIRECTIONACTIVATED);
            //header("location:".basename($_SERVER['PHP_SELF'])."?ctg=directions&message=".urlencode(_DIRECTIONACTIVATED)."&message_type=success");
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = 'failure';
        }
        echo $message; exit;
    } elseif (isset($_GET['add_direction']) || (isset($_GET['edit_direction']) && eF_checkParameter($_GET['edit_direction'], 'id'))) {
        $directionsTree  = new EfrontDirectionsTree();
        $directionsPaths = $directionsTree -> toPathString();
        if (isset($_GET['add_direction'])) {
            $post_target    = 'add_direction=1';
            $defaults_array = array('active' => 1);
        } else {
            $post_target    = 'edit_direction='.$_GET['edit_direction'];
            $editDirection  = new EfrontDirection($_GET['edit_direction']);
            $defaults_array = array('name'                => $editDirection['name'],
                                    'active'              => $editDirection['active'],
                                    'parent_direction_ID' => $editDirection['parent_direction_ID']);
            //Remove direction's children from the list of selectable parents
            $directionChildren = array();
            foreach (new EfrontAttributeFilterIterator(new RecursiveIteratorIterator($directionsTree -> getNodeChildren($_GET['edit_direction'])), array('id')) as $key => $value) {
                if (isset($directionsPaths[$value])) {
                    unset($directionsPaths[$value]);
                }
            }
        }

        $form = new HTML_QuickForm("add_directions_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=directions&".$post_target, "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter

        $form -> addElement('text', 'name', _DIRECTIONNAME, 'class = "inputText"');
        $form -> addRule('name', _THEFIELD.' '._DIRECTIONNAME.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');

        $selectOptions    = $directionsPaths;
        $selectOptions[0] = _ROOTDIRECTION;
        ksort($selectOptions);
        $form -> addElement('select', 'parent_direction_ID', _PARENTDIRECTION, $selectOptions);
        $form -> addElement("advcheckbox", "active", _ACTIVEFEM, null, 'class = "inputCheckBox"', array(0, 1));

        $form -> setDefaults($defaults_array);

        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            $form -> freeze();
        } else {
            $form -> addElement('submit', 'submit_direction', _SUBMIT, 'class = "flatButton"');

            if ($form -> isSubmitted() && $form -> validate()) {
                $direction_content = array("name"                => $form -> exportValue('name'),
                                           "parent_direction_ID" => $form -> exportValue('parent_direction_ID'),
                                           "active"              => $form -> exportValue('active'));
                if (isset($_GET['edit_direction'])) {
                    $editDirection['name']                = $direction_content['name'];
                    $editDirection['parent_direction_ID'] = $direction_content['parent_direction_ID'];
                    $editDirection['active']              = $direction_content['active'];
                    try {
                        $editDirection -> persist();
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=directions&message=".urlencode(_SUCCESFULLYUPDATEDDIRECTION)."&message_type=success");
                    } catch (Exception $e) {
                        $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
                        $message_type = 'failure';
                    }
                } else {
                    try {
                        EfrontDirection :: createDirection($direction_content);
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=directions&message=".urlencode(_SUCCESFULLYADDEDDIRECTION)."&message_type=success");
                    } catch (Exception $e) {
                        $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
                        $message_type = 'failure';
                    }
                }
            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);

        $smarty -> assign('T_DIRECTIONS_FORM', $renderer -> toArray());

        if (isset($_GET['edit_direction'])) {
            $loadScripts[] = 'scriptaculous/scriptaculous';
            $loadScripts[] = 'scriptaculous/effects';

            $lessons   = EFrontLesson :: getLessons();
            $languages = EfrontSystem :: getLanguages(true);
            $smarty -> assign("T_DIRECTIONS_PATHS", $directionsTree -> toPathString());

            if ($module_hcd_interface) {
                $result  = eF_getTableDataFlat("lessons LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_lesson_offers_skill.lesson_ID = lessons.id","lessons.id, count(skill_ID) as skills_offered","","","id");
                foreach ($result['id'] as $key => $lesson_id) {
                    $lessons[$lesson_id]['skills_offered'] = $result['skills_offered'][$key];
                }
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 'lessonsTable') {
                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $lessons = eF_multiSort($lessons, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $lessons = eF_filterData($lessons, $_GET['filter']);
                }
                $smarty -> assign("T_LESSONS_SIZE", sizeof($lessons));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $lessons = array_slice($lessons, $offset, $limit);
                }
                foreach ($lessons as $key => $lesson) {
                    $lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
                }

                $smarty -> assign("T_LESSONS_DATA", $lessons);

                $smarty -> display('administrator.tpl');
                exit;
            }
            if (isset($_GET['postAjaxRequest'])) {
                try {
                    if (isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id') && isset($_GET['directions_ID']) && eF_checkParameter($_GET['directions_ID'], 'id')) {
                        $lesson = new EfrontLesson($_GET['id']);
                        $lesson -> lesson['directions_ID'] = $_GET['directions_ID'];
                        $lesson -> persist();
                    }
                    exit;
                } catch (Exception $e) {
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
            }

        }
    } else {
        $directionsTree    = new EfrontDirectionsTree();

        $directionsPaths   = $directionsTree -> toPathString(false);
        $flatTree          = $directionsTree -> getFlatTree();

        foreach ($flatTree as &$value) {
            $value['pathString'] = $directionsPaths[$value['id']];
            $direction           = new EfrontDirection($value);
            $value['lessons']    = sizeof($direction -> getLessons());
        }
        unset($value);
        $smarty -> assign("T_DIRECTIONS_DATA", $flatTree);
    }
}
/*
 */
elseif ($ctg == 'courses') {
    if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    $result        = eF_getTableDataFlat("courses", "id");
    $systemCourses = $result['id'];
    if (isset($_GET['delete_course']) && eF_checkParameter($_GET['delete_course'], 'id') && in_array($_GET['delete_course'], $systemCourses)) {
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            exit;
        }
        try {
            $course = new EfrontCourse($_GET['delete_course']);
            $course -> delete();
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=courses&message=".urlencode(_COURSEDELETED)."&message_type=success");
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = 'failure';
        }
    } elseif (isset($_GET['deactivate_course']) && eF_checkParameter($_GET['deactivate_course'], 'id') && in_array($_GET['deactivate_course'], $systemCourses)) {
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $course = new EfrontCourse($_GET['deactivate_course']);
            $course -> course['active'] = 0;
            $course -> persist();
            $message = urlencode(_COURSEDEACTIVATED);
            //header("location:".basename($_SERVER['PHP_SELF'])."?ctg=courses&message=".urlencode(_COURSEDEACTIVATED)."&message_type=success");
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = 'failure';
        }
        echo $message;exit;
    } elseif (isset($_GET['activate_course']) && eF_checkParameter($_GET['activate_course'], 'id') && in_array($_GET['activate_course'], $systemCourses)) {
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $course = new EfrontCourse($_GET['activate_course']);
            $course -> course['active'] = 1;
            $course -> persist();
            $message = urlencode(_COURSEACTIVATED);
            //header("location:".basename($_SERVER['PHP_SELF'])."?ctg=courses&message=".urlencode(_COURSEACTIVATED)."&message_type=success");
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = 'failure';
        }
        echo $message;exit;
    } elseif (isset($_GET['add_course']) || (isset($_GET['edit_course']) && eF_checkParameter($_GET['edit_course'], 'id')) && in_array($_GET['edit_course'], $systemCourses)) {

        isset($_GET['add_course']) ? $post_target = 'add_course=1' : $post_target = 'edit_course='.$_GET['edit_course'];

        $form = new HTML_QuickForm("add_courses_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=courses&".$post_target, "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
        //$form -> registerRule('checkNotExist', 'callback', 'eF_checkNotExist');
        $form -> addElement('text', 'name', _COURSENAME, 'class = "inputText"');
        $form -> addRule('name', _THEFIELD.' "'._COURSENAME.'" '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');
        //$form -> addRule('name',  _COURSENAME.' &quot;'.($form -> exportValue('name')).'&quot; '._ALREADYEXISTS, 'checkNotExist', 'course');

        try {
            $directionsTree = new EfrontDirectionsTree();
            if (sizeof($directionsTree -> tree) == 0) {
                header("location:".basename($_SERVER['PHP_SELF']).'?ctg=directions&add_direction=1&message='.urlencode(_YOUMUSTFIRSTCREATEDIRECTION).'&message_type=failure');
            }
            $directions     = $directionsTree -> toPathString();
        } catch (Exception $e) {
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            $message_type = 'failure';
        }

        $form -> addElement('select', 'directions_ID', _DIRECTION, $directions);                    //Append a directions select box to the form

        if ($GLOBALS['configuration']['onelanguage'] != true){
            $languages = EfrontSystem :: getLanguages(true);
            $form -> addElement('select', 'languages_NAME', _LANGUAGE, array_combine(array_keys($languages), $languages));
        }

        $form -> addElement('text', 'price', _PRICE, 'class = "inputText" style = "width:50px"');
        $form -> addElement('checkbox', 'active', _ACTIVEFEM);

        if (isset($_GET['edit_course'])) {
            $editCourse = new EfrontCourse($_GET['edit_course']);

            $smarty -> assign('T_COURSE_NAME', $editCourse -> course['name']);
            $form -> setDefaults(array('name'           => $editCourse -> course['name'],
                                       'active'         => $editCourse -> course['active'],
                                       'languages_NAME' => $editCourse -> course['languages_NAME'],
                                       'directions_ID'  => $editCourse -> course['directions_ID'],
                                       'price'          => $editCourse -> course['price']));
        } else {
            $form -> setDefaults(array('active' => 1,
                                       'price'  => 0,
                                       'languages_NAME' => $GLOBALS['configuration']['default_language']));
        }

        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            $form -> freeze();
        } else {
            $form -> addElement('submit', 'submit_course', _SUBMIT, 'class = "flatButton"');

            if ($form -> isSubmitted() && $form -> validate()) {
                if (isset($_GET['edit_course'])) {

                    $GLOBALS['configuration']['onelanguage'] == true ? $languages_NAME = $GLOBALS['configuration']['default_language'] : $languages_NAME = $form -> exportValue('languages_NAME');
                    $fields_update = array('name'           => $form -> exportValue('name'),
                                           'languages_NAME' => $languages_NAME,
                                           'active'         => $form -> exportValue('active'),
                                           'directions_ID'  => $form -> exportValue('directions_ID'),
                                           'price'          => $form -> exportValue('price'));

                    try {
                        $editCourse -> course = array_merge($editCourse -> course, $fields_update);
                        $editCourse -> persist();
                        header("location:".basename($_SERVER['PHP_SELF']).'?ctg=courses&message='.urlencode(_COURSEUPDATED).'&message_type=success');
                    } catch (Exception $e) {
                        $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
                        $message_type = 'failure';
                    }

                } elseif (isset($_GET['add_course'])) {
                    $GLOBALS['configuration']['onelanguage'] == true ? $languages_NAME = $GLOBALS['configuration']['default_language'] : $languages_NAME = $form -> exportValue('languages_NAME');
                    $fields_insert = array('name'           => $form -> exportValue('name'),
                                           'languages_NAME' => $languages_NAME,
                                           'active'         => $form -> exportValue('active'),
                                           'directions_ID'  => $form -> exportValue('directions_ID'),
                                           'price'          => $form -> exportValue('price'));

                    try {
                        $newCourse = EfrontCourse :: createCourse($fields_insert);
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=courses&edit_course=".$newCourse -> course['id']."&tab=lessons&message=".urlencode(_SUCCESFULLYCREATEDCOURSE)."&message_type=success");
                    } catch (Excpetion $e) {
                        $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
                        $message_type = 'failure';
                    }
                }
            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);

        $smarty -> assign('T_COURSE_FORM', $renderer -> toArray());


        if (isset($_GET['edit_course'])) {

            /** MODULE HCD: Submission of skills **/
            /*******************************************
             SUBMISSION OF SKILLS (COURSE TO SKILLS)
             *******************************************/
            if (MODULE_HCD_INTERFACE) {
                /* Ajax assignments/removals of the skill to employees */
                if ($_GET['postAjaxRequest'] && isset($_GET['add_skill'])) {

                    /* Find all employees having this skill */
                    if ($_GET['insert'] == "true") {
                        $editCourse -> assignSkill($_GET['add_skill'], $_GET['specification']);
                    } else if ($_GET['insert'] == "false") {
                        $editCourse -> removeSkill($_GET['add_skill']);
                    } else if (isset($_GET['addAll'])) {
                        $skills = $editCourse -> getSkills();
                        foreach ($skills as $skill) {
                            if ($skill['courses_ID'] == "") {
                                $editCourse -> assignSkill($skill['skill_ID'], "");
                            }
                        }
                    } else if (isset($_GET['removeAll'])) {
                        $skills = $editCourse -> getSkills();
                        foreach ($skills as $skill) {
                            if ($skill['courses_ID'] == $editCourse -> course['id']) {
                                $editCourse -> removeSkill($skill['skill_ID']);
                            }
                        }
                    }
                    exit;
                }
            }


            $loadScripts[] = 'scriptaculous/scriptaculous';
            $loadScripts[] = 'scriptaculous/effects';

            $lessons         = EfrontLesson :: getLessons();
            $courseLessons   = $editCourse -> getLessons();
            $directionsPaths = $directionsTree -> toPathString();
            $languages       = EfrontSystem :: getLanguages(true);

            foreach ($lessons as $key => $lesson) {
                $lessons[$key]['directionsPath']  = $directionsPaths[$lesson['directions_ID']];
                if (in_array($lesson['id'], array_keys($courseLessons))) {
                    $lessons[$key]['course_assigned'] = true;
                } else {
                    $lessons[$key]['course_assigned'] = false;
                    if ($lesson['active'] == 0 || $lesson['languages_NAME'] != $editCourse -> course['languages_NAME'] || !$lesson['course_only']) {
                        unset($lessons[$key]);
                    }
                }
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 'lessonsTable') {
                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $lessons = eF_multiSort($lessons, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $lessons = eF_filterData($lessons, $_GET['filter']);
                }
                $smarty -> assign("T_LESSONS_SIZE", sizeof($lessons));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $lessons = array_slice($lessons, $offset, $limit);
                }
                foreach ($lessons as $key => $lesson) {
                    $lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
                }
                $smarty -> assign("T_LESSONS_DATA", $lessons);

                $smarty -> display('administrator.tpl');
                exit;
            }
            if (isset($_GET['postAjaxRequest']) && $_GET['postAjaxRequest'] == 'lessons') {
                try {
                    if (isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id')) {
                        !in_array($_GET['id'], array_keys($courseLessons)) ? $editCourse -> addLessons($_GET['id']) : $editCourse -> removeLessons($_GET['id']) ;
                    } else if (isset($_GET['addAll'])) {
                        $editCourse -> addLessons(array_diff(array_keys($lessons), array_keys($courseLessons)));
                    } else if (isset($_GET['removeAll'])) {
                        $editCourse -> removeLessons(array_keys($courseLessons));
                    }
                    exit;
                } catch (Exception $e) {
                    header("HTTP/1.0 500");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
            }

            try {
                /** MODULE HCD: Get all skills this course has to offer **/
                if (MODULE_HCD_INTERFACE) {
                    $skills = $editCourse -> getSkills();
                    if (isset($_GET['ajax']) && $_GET['ajax'] == 'skillsTable') {
                        isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                        if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                            $sort = $_GET['sort'];
                            isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                        } else {
                            $sort = 'description';
                        }

                        $skills = eF_multiSort($skills, $sort, $order);
                        $smarty -> assign("T_SKILLS_SIZE", sizeof($skills));
                        if (isset($_GET['filter'])) {
                            $skills = eF_filterData($skills, $_GET['filter']);
                        }
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $skills = array_slice($skills, $offset, $limit);
                        }

                        if (!empty($skills)) {
                            $smarty -> assign("T_SKILLS", $skills);
                        }
                        $smarty -> display('administrator.tpl');
                        exit;
                    } else {

                        if (!empty($skills)) {
                            $smarty -> assign("T_SKILLS", $skills);
                            $smarty -> assign("T_SKILLS_SIZE", sizeof($skills));
                        }
                    }
                }

                $courseUsers    = $editCourse -> getUsers();                        //Get all users that have this course
                $nonCourseUsers = $editCourse -> getNonUsers();                     //Get all the users that can, but don't, have this course
                $users          = $courseUsers + $nonCourseUsers;                   //Merge users to a single array, which will be useful for displaying them (+ is used instead of array_merge, for the case that a user has numerical login)

                $roles = EfrontLessonUser :: getLessonsRoles(true);

                if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
                    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                        $sort = $_GET['sort'];
                        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                    } else {
                        $sort = 'login';
                    }

                    $users = eF_multiSort($users, $sort, $order);
                    $smarty -> assign("T_USERS_SIZE", sizeof($users));
                    if (isset($_GET['filter'])) {
                        $users = eF_filterData($users, $_GET['filter']);
                    }
                    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                        $users = array_slice($users, $offset, $limit, true);
                    }

                    $smarty -> assign("T_ROLES", $roles);
                    $smarty -> assign("T_ALL_USERS", $users);
                    $smarty -> assign("T_COURSE_USERS", array_keys($courseUsers));                                             //We assign separately the course's users, to know when to display the checkboxes as "checked"

                    $smarty -> display('administrator.tpl');
                    exit;
                }
            } catch (Exception $e) {
                header("HTTP/1.0 500");
                echo $e -> getMessage().' ('.$e -> getCode().')';
                exit;
            }


            if (isset($_GET['postAjaxRequest']) && $_GET['postAjaxRequest'] == 'users') {
                try {
                    if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
                        isset($_GET['user_type']) && in_array($_GET['user_type'], array_keys($roles)) ? $userType = $_GET['user_type'] : $userType = 'student';
                        if (in_array($_GET['login'], array_keys($nonCourseUsers))) {
                            $editCourse -> addUsers($_GET['login'], $userType);
                        }
                        if (in_array($_GET['login'], array_keys($courseUsers))) {
                            $userType != $courseUsers[$_GET['login']]['role'] ? $editCourse -> setRoles($_GET['login'], $userType) : $editCourse -> removeUsers($_GET['login']);
                        }
                    } else if (isset($_GET['addAll'])) {
                        $userTypes = array();
                        foreach ($nonCourseUsers as $user) {
                            $user['user_types_ID'] ? $userTypes[] = $user['user_types_ID'] : $userTypes[] = $user['basic_user_type'];
                        }
                        $editCourse -> addUsers(array_keys($nonCourseUsers), $userTypes);
                    } else if (isset($_GET['removeAll'])) {
                        foreach ($courseUsers as $user) {
                            $userRoles[] = $user['basic_user_type'];
                        }
                        $editCourse -> removeUsers(array_keys($courseUsers), $userRoles);
                    }
                } catch (Exception $e) {
                    header("HTTP/1.0 500");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            }
        }
    } elseif (isset($_GET['course']) && in_array($_GET['course'], $systemCourses)) {
        $loadScripts[] = 'scriptaculous/scriptaculous';                            //Load effects to be used on ajax users assignment
        $loadScripts[] = 'scriptaculous/effects';

        $options = array(array('image' => '16x16/about.png',       'title' => _COURSEINFORMATION,  'link' => $_GET['op'] != 'course_info'        ? basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$_GET['course'].'&op=course_info'         : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_info'         ? false : true),
                         array('image' => '16x16/certificate.png', 'title' => _COURSECERTIFICATES, 'link' => $_GET['op'] != 'course_certificate' ? basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$_GET['course'].'&op=course_certificates' : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_certificates' ? false : true),
                         array('image' => '16x16/recycle.png',     'title' => _COURSERULES,        'link' => $_GET['op'] != 'course_rules'       ? basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$_GET['course'].'&op=course_rules'        : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_rules'        ? false : true),
                         array('image' => '16x16/replace2.png',    'title' => _COURSEORDER,        'link' => $_GET['op'] != 'course_order'       ? basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$_GET['course'].'&op=course_order'        : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_order'        ? false : true),
                         array('image' => '16x16/calendar.png',    'title' => _SCHEDULING,         'link' => $_GET['op'] != 'course_scheduling'  ? basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$_GET['course'].'&op=course_scheduling'   : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_scheduling'   ? false : true));
        $smarty -> assign("T_TABLE_OPTIONS", $options);

        $currentCourse = new EfrontCourse($_GET['course']);
        $smarty -> assign("T_CURRENT_COURSE", $currentCourse);
        if ($_GET['op'] == 'course_info') {
            $form = new HTML_QuickForm("empty_form", "post", null, null, null, true);

            $courseInformation = unserialize($currentCourse -> course['info']);
            $information       = new LearningObjectInformation($courseInformation);

            $courseMetadata = unserialize($currentCourse -> course['metadata']);
            $metadata       = new DublinCoreMetadata($courseMetadata);
            if (!isset($currentUser -> coreAccess['lessons']) || $currentUser -> coreAccess['lessons'] == 'change') {
                $smarty -> assign("T_COURSE_INFO_HTML", $information -> toHTML($form, false));
                $smarty -> assign("T_COURSE_METADATA_HTML", $metadata -> toHTML($form));
            } else {
                $smarty -> assign("T_COURSE_INFO_HTML", $information -> toHTML($form, false, false));
                $smarty -> assign("T_COURSE_METADATA_HTML", $metadata -> toHTML($form, true, false));
            }


            if (isset($_GET['postAjaxRequest'])) {
                if (in_array($_GET['dc'], array_keys($information -> metadataAttributes))) {
                    if ($_GET['value']) {
                        $courseInformation[$_GET['dc']] = $_GET['value'];
                    } else {
                        unset($courseInformation[$_GET['dc']]);
                    }
                    $currentCourse -> course['info'] = serialize($courseInformation);
                } elseif (in_array($_GET['dc'], array_keys($metadata -> metadataAttributes))) {
                    if ($_GET['value']) {
                        $courseMetadata[$_GET['dc']] = $_GET['value'];
                    } else {
                        unset($courseMetadata[$_GET['dc']]);
                    }
                    $currentCourse -> course['metadata'] = serialize($courseMetadata);
                }
                try {
                    $currentCourse -> persist();
                    echo $_GET['value'];
                } catch (Exception $e) {
                    header("HTTP/1.0 500");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            }
        } else if ($_GET['op'] == 'course_certificates') {

            $users = EfrontStats::getUsersCourseStatus($currentCourse);
            $users = $users[$currentCourse -> course['id']];
            if (isset($_GET['edit_user']) && in_array($_GET['edit_user'], array_keys($users))) {
                $userStats = $users[$_GET['edit_user']];
                $form = new HTML_QuickForm("edit_user_complete_course_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$_GET['course'].'&op=course_certificates&edit_user='.$_GET['edit_user'].'&popup=1', "", null, true);
                $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter

                $form -> addElement('advcheckbox', 'completed', _COMPLETED, null, 'class = "inputCheckbox"', array(0,1));            //Whether the user has completed the course
                $form -> addElement('text', 'score', _SCORE, 'class = "inputText" style = "width:40px;"');                                                        //The user course score
                $form -> addRule('score', _THEFIELD.' "'._SCORE.'" '._MUSTBENUMERIC, 'numeric', null, 'client');                            //The score must be numeric
                $form -> addRule('score', _RATEMUSTBEBETWEEN0100, 'callback', create_function('&$a', 'return ($a >= 0 && $a <= 100);'));    //The score must be between 0 and 100

                $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputContentTextarea simpleEditor" style = "width:100%;height:5em;"');      //Comments on student's performance

                $totalScore = 0;
                foreach ($userStats['lesson_status'] as $stat) {
                    $totalScore += $stat['score'] / sizeof($userStats['lesson_status']);
                }

                $form -> setDefaults(array("completed" => $userStats['completed'],
                                           "score"     => $userStats['completed'] ? $userStats['score'] : round($totalScore),
                                           "comments"  => $userStats['comments']));

                if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                    $form -> freeze();
                } else {
                    $form -> addElement('submit', 'submit_course_complete', _SUBMIT, 'class = "flatButton"');       //The submit button
                    if ($form -> isSubmitted() && $form -> validate()) {
                        $fields = array("completed" => $form -> exportValue('completed'),
                                        "score"     => $form -> exportValue('completed') ? $form -> exportValue('score')    : 0,
                                        "comments"  => $form -> exportValue('completed') ? $form -> exportValue('comments') : '');
                        eF_updateTableData("users_to_courses", $fields, "users_LOGIN = '".$_GET['edit_user']."' and courses_ID=".$currentCourse -> course['id']);
                        echo '<script>parent.location="'.basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$_GET['course'].'&op=course_certificates&message='.urlencode(_STUDENTSTATUSCHANGED).'&message_type=success"</script>';
                    }
                }
                $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

                $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
                $form -> setRequiredNote(_REQUIREDNOTE);
                $form -> accept($renderer);

                $smarty -> assign('T_COMPLETE_COURSE_FORM', $renderer -> toArray());
                $smarty -> assign("T_USER_PROGRESS", $userStats);
            } else if (isset($_GET['issue_certificate']) && in_array($_GET['issue_certificate'], array_keys($users))) {
                if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                    exit;
                }
                try {
                    $certificate = $currentCourse -> prepareCertificate($_GET['issue_certificate']);
                    $currentCourse -> issueCertificate($_GET['issue_certificate'], $certificate);
                    echo header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$_GET['course'].'&op=course_certificates&message='.urlencode(_CERTIFICATEISSUEDSUCCESFULLY).'&message_type=success');
                } catch (Exception $e) {
                    $message      = _PROBLEMISSUINGCERTIFICATE.': '.$e -> getMessage().' ('.$e -> getCode().')';
                    $message_type = 'failure';
                }
            } else if (isset($_GET['revoke_certificate']) && in_array($_GET['revoke_certificate'], array_keys($users))) {
                if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                    exit;
                }
                try {
                    $currentCourse -> revokeCertificate($_GET['revoke_certificate']);
                    header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$currentCourse -> course['id'].'&op=course_certificates&message='.urlencode(_CERTIFICATEREVOKED).'&message_type=success');
                } catch (Exception $e) {
                    $message      = _PROBLEMREVOKINGCERTIFICATE;
                    $message_type = 'failure';
                }
            } else if (isset($_GET['auto_complete'])) {
                if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                    exit;
                }
                if ($currentCourse -> course['auto_complete']) {
                    $currentCourse -> course['auto_complete']    = 0;
                    $currentCourse -> course['auto_certificate'] = 0;
                } else {
                    $currentCourse -> course['auto_complete'] = 1;
                }
                $currentCourse -> persist();
            } else if (isset($_GET['auto_certificate'])) {
                if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                    exit;
                }
                if ($currentCourse -> course['auto_certificate']) {
                    $currentCourse -> course['auto_certificate'] = 0;
                } else {
                    $currentCourse -> course['auto_certificate'] = 1;
                }
                $currentCourse -> persist();
            }

            if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
                foreach ($users as $key => $user) {
                    if ($user['user_type'] != 'student') {
                        unset($users[$key]);
                    }
                }

                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }
                $users = eF_multiSort($users, $sort, $order);
                $smarty -> assign("T_USERS_SIZE", sizeof($users));
                if (isset($_GET['filter'])) {
                    $users = eF_filterData($users, $_GET['filter']);
                }
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $users = array_slice($users, $offset, $limit);
                }
                foreach ($users as $key => $value) {
                    $users[$key]['issued_certificate'] = $value['issued_certificate'];
                }
                $smarty -> assign("T_USERS_PROGRESS", $users);
                $smarty -> display('administrator.tpl');
                exit;
            }
            if (isset($_GET['export']) && $_GET['export'] == 'rtf') {
                if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                    exit;
                }
                $result = eF_getTableData("users_to_courses", "*", "users_LOGIN = '".$_GET['user']."' and courses_ID = '".$_GET['course']."' limit 1");
                if (sizeof($result) == 1 || isset($_GET['preview'])) {
                    $course = new EfrontCourse($_GET['course']);
                    if (!isset($_GET['preview'])){
                        $certificate_tpl_id = $course -> course['certificate_tpl_id'];
                        if ($certificate_tpl_id <= 0) {
                            $cfile = new EfrontFile(G_CERTIFICATETEMPLATEPATH."certificate1.rtf");
                        } else {
                            $cfile = new EfrontFile($certificate_tpl_id);
                        }
                        $template_data = file_get_contents($cfile['path']);
                        $issued_data = unserialize($result[0]['issued_certificate']);
                        $certificate = $template_data;
                        if (sizeof($issued_data) > 1){
                            $certificate   = $template_data;
                            $certificate   = str_replace("#organization#", utf8ToUnicode($issued_data['organization']), $certificate);
                            $certificate   = str_replace("#user_name#", utf8ToUnicode($issued_data['user_name']), $certificate);
                            $certificate   = str_replace("#user_surname#", utf8ToUnicode($issued_data['user_surname']), $certificate);
                            $certificate   = str_replace("#course_name#", utf8ToUnicode($issued_data['course_name']), $certificate);
                            $certificate   = str_replace("#grade#", utf8ToUnicode($issued_data['grade']), $certificate);
                            $certificate   = str_replace("#date#", utf8ToUnicode($issued_data['date']), $certificate);
                        }
                    }
                    else {
                        $certificateDirectory = G_CERTIFICATETEMPLATEPATH;
                        $selectedCertificate = $_GET['certificate_tpl'];
                        $certificate = file_get_contents($certificateDirectory.$selectedCertificate);
                    }
                    $filename = "certificate_".$_GET['user'].".rtf";
                    header("Content-type: application/rtf");
                    header("Content-disposition: inline; filename=$filename");
                    header("Content-length: " . strlen($certificate));
                    echo $certificate;
                    exit(0);
                }
            }
        } else if ($_GET['op'] == 'format_certificate') {
            if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
                exit;
            }

            if ($currentCourse -> course['certificate_tpl_id'] > 0){
                $certificateFile = new EfrontFile($currentCourse -> course['certificate_tpl_id']);
                $dname = $certificateFile -> offsetGet('name');
            }

            try {
                $certificateFileSystemTree = new FileSystemTree(G_CERTIFICATETEMPLATEPATH);
                foreach (new EfrontFileTypeFilterIterator(new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($certificateFileSystemTree -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('rtf')) as $key => $value) {
                    $existingCertificates[basename($key)] = basename($key);
                }
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            }


            $form = new HTML_QuickForm("edit_course_certificate_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$currentCourse -> course['id'].'&op=format_certificate', "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('file', 'file_upload', _CERTIFICATETEMPLATE, 'class = "inputText"');
            $form -> addElement('select', 'existing_certificate', _ORSELECTONEFROMLIST, $existingCertificates, "id = 'select_certificate'");
            $form -> addElement('button', 'preview', _PREVIEW,
            'onclick = "javascript:window.open(\''.basename($_SERVER['PHP_SELF']).'?ctg=courses&course='.$currentCourse -> course['id'].'&op=course_certificates&export=rtf&preview=1&certificate_tpl=\'+document.forms[0].existing_certificate.value)"
            title = "'._VIEWCERTIFICATE.'"');
            $form -> addElement('submit', 'submit_certificate', _SAVE, 'class = "flatButton"');
            $form -> setDefaults(array('existing_certificate' => $dname));
            $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);

            if ($form -> isSubmitted() && $form -> validate()) {
                $certificateDirectory = G_CERTIFICATETEMPLATEPATH;
                if (!is_dir($certificateDirectory)) {
                    mkdir($certificateDirectory);
                }
                $logoid = 0;
                try {
                    if ($_FILES['file_upload']['size'] > 0) {
                        $filesystem   = new FileSystemTree($certificateDirectory);
                        $uploadedFile = $filesystem -> uploadFile('file_upload', $certificateDirectory);
                        $certificateid = $uploadedFile['id'];
                    } else {
                        $selectedCertificate = $form -> exportValue('existing_certificate');
                        $certificateFile = new EfrontFile(G_CERTIFICATETEMPLATEPATH.$selectedCertificate);
                        if ($certificateFile['id'] < 0) { //if the file doesn't exist, then import it
                            $selectedCertificate = $certificateFileSystemTree -> seekNode(G_CERTIFICATETEMPLATEPATH.$selectedCertificate);
                            $newList             = FileSystemTree :: importFiles($selectedCertificate['path']);
                            $certificateid       = key($newList);
                        }
                        else {
                            $certificateid = $certificateFile['id'];
                        }
                    }
                    $currentCourse -> course['certificate_tpl_id'] = $certificateid;
                    $currentCourse -> persist();
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=lessons&course=".$currentCourse -> course['id']."&op=course_certificates&message=".urlencode(_SUCCESFULLYUPDATEDCERTIFICATE)."&message_type=success");
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }

            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

            $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
            $form -> setRequiredNote(_REQUIREDNOTE);
            $form -> accept($renderer);
            $smarty -> assign('T_CERTIFICATE_FORM', $renderer -> toArray());
        }  else if ($_GET['op'] == 'course_rules') {
            $courseLessons = $currentCourse -> getLessons();

            $rules_form = new HTML_QuickForm("course_rules_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=courses&course=".$currentCourse -> course['id']."&op=course_rules", "", null, true);
            if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                $rules_form -> freeze();
            } else {
                $rules_form -> addElement('submit', 'submit_rule', _SUBMIT, 'class = "flatButton"');
                if ($rules_form -> isSubmitted() && $rules_form -> validate()) {
                    foreach ($_POST['rules'] as $rule_lesson) {
                        if (sizeof(array_unique($rule_lesson['lesson'])) != sizeof($rule_lesson['lesson'])) {
                            $duplicate = true;
                        }
                    }
                    if (!isset($duplicate)) {
                        try {
                            $currentCourse -> rules = $_POST['rules'];
                            $currentCourse -> persist();
                            $message      = _SUCCESFULLYSETORDER;
                            $message_type = 'success';
                        } catch (Exception $e) {
                            $message      = _PROBLEMSETTINGORDER.': '.$e -> getMessage().' ('.$e -> getCode().')';
                            $message_type = 'failure';
                        }
                    } else {
                        $message      = _DUPLICATESARENOTALLOWED;
                        $message_type = 'failure';
                    }
                }
            }
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

            $rules_form -> accept($renderer);
            $smarty -> assign('T_COURSE_RULES_FORM', $renderer -> toArray());
            $smarty -> assign("T_COURSE_RULES", $currentCourse -> rules);
            $smarty -> assign('T_COURSE_LESSONS', $courseLessons);
        } else if ($_GET['op'] == 'course_order') {
            $loadScripts[] = 'drag-drop-folder-tree';
            $courseLessons = $currentCourse -> getLessons();

            $smarty -> assign('T_COURSE', $currentCourse -> course);
            $smarty -> assign('T_COURSE_LESSONS', $courseLessons);

            if (isset($_GET['ajax']) && isset($_GET['order'])) {
                if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                    echo urlencode(_UNAUTHORIZEDACCESS);
                    exit;
                }
                $order    = explode(",", $_GET['order']);
                $previous = 0;
                foreach ($order as $value) {
                    $result = explode("-", $value);
                    if (in_array($value, array_keys($courseLessons))) {
                        eF_updateTableData("lessons_to_courses", array("previous_lessons_ID" => $previous), "courses_ID=".$currentCourse -> course['id']." and lessons_ID=".$result[0]);
                    }
                    $previous = $result[0];
                }
                exit;
            }
        } else if ($_GET['op'] == 'course_scheduling') {
            $courseLessons = $currentCourse -> getLessons();
            if (isset($_GET['set_schedule']) && in_array($_GET['set_schedule'], array_keys($courseLessons))) {
                if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                    header("HTTP/1.0 500");
                    echo _UNAUTHORIZEDACCESS;
                    exit;
                }
                try {
                    $lesson        = new EfrontLesson($_GET['set_schedule']);
                    $fromTimestamp = mktime($_GET['from_Hour'], $_GET['from_Minute'], 0, $_GET['from_Month'], $_GET['from_Day'], $_GET['from_Year']);
                    $toTimestamp   = mktime($_GET['to_Hour'],   $_GET['to_Minute'],   0, $_GET['to_Month'],   $_GET['to_Day'],   $_GET['to_Year']);
                    if ($fromTimestamp < $toTimestamp) {
                        $lesson -> lesson['from_timestamp'] = $fromTimestamp;
                        $lesson -> lesson['to_timestamp']   = $toTimestamp;
//                        $lesson -> lesson['shift']          = $form -> exportValue('shift') ? 1 : 0;

                        $lesson -> persist();
                        echo _FROM.' '.formatTimestamp($fromTimestamp, 'time_nosec').' '._TO.' '.formatTimestamp($toTimestamp, 'time_nosec').'&nbsp;';
                    } else {
                        header("HTTP/1.0 500");
                        echo _ENDDATEMUSTBEBEFORESTARTDATE;
                    }
                } catch (Exception $e) {
                    header("HTTP/1.0 500");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }

                exit;
            } else if (isset($_GET['delete_schedule']) && in_array($_GET['delete_schedule'], array_keys($courseLessons))) {
                if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
                    header("HTTP/1.0 500");
                    echo _UNAUTHORIZEDACCESS;
                    exit;
                }
                try {
                    $lesson = new EfrontLesson($_GET['delete_schedule']);
                    $lesson -> lesson['from_timestamp'] = '';
                    $lesson -> lesson['to_timestamp']   = '';
                    $lesson -> lesson['shift']          = 0;

                    $lesson -> persist();
                } catch (Exception $e) {
                    header("HTTP/1.0 500 ");
                    echo $e -> getMessage().' ('.$e -> getCode().')';
                }
                exit;
            }

            $smarty -> assign("T_COURSE_LESSONS", $courseLessons);
        }
    } else {
        $courses        = EFrontCourse :: getCourses();
        $directionsTree = new EfrontDirectionsTree();
        $directions     = $directionsTree -> toPathString();
        $languages      = EfrontSystem :: getLanguages(true);
        $result         = eF_getTableData("lessons_to_courses", "*");
        foreach ($result as $value) {
            $courseLessons[$value['courses_ID']][] = $value['lessons_ID'];
        }

        foreach ($courses as $key => $course) {
            $obj = new EfrontCourse($course['id']);
            $course['directions_ID'] ? $courses[$key]['directionsPath'] = $directions[$course['directions_ID']] : $courses[$key]['directionsPath'] = '';
            $courses[$key]['languages_NAME'] = $languages[$course['languages_NAME']];
            $courses[$key]['lessons_num']    = sizeof($courseLessons[$course['id']]);
            $courses[$key]['link']           = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=courses&edit_course='.$course['id']);
        }

        if ($module_hcd_interface) {
            $result  = eF_getTableDataFlat("courses LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_course_offers_skill.courses_ID = courses.id","courses.id, count(skill_ID) as skills_offered","","","id");
            foreach ($result['id'] as $key => $courses_id) {
                $courses[$courses_id]['skills_offered'] = $result['skills_offered'][$key];
            }
        }
        $smarty -> assign("T_COURSES_DATA", $courses);
/*
        $courseInformationForm = new HTML_QuickForm("course_info_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=courses", "", null, true);
        $courseInformationForm -> addElement('submit', 'submit_info', _SUBMIT, 'class = "flatButton"');
        if ($courseInformationForm -> isSubmitted() && $courseInformationForm -> validate()) {
            $values = $courseInformationForm -> exportValues();
            pr($values);
        }
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $courseInformationForm -> accept($renderer);
        $smarty -> assign('T_COURSE_INFO_FORM', $renderer -> toArray());
  */
    }

}



/* Ranking tests (rrrrrrrrrrrrr)*/
else if ($ctg == 'tests') {

    if (isset($_GET['view_results'])) {

        // GET THE CORRECT TEST

        // Per-user analysis of the tests => skill gap analysis
        if (isset($_GET['user'])) {

            // PROPOSED LESSONS
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'proposedLessonsTable') {
                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                $directionsTree = new EfrontDirectionsTree();
                $directionsPaths = $directionsTree -> toPathString();
                $languages       = EfrontSystem :: getLanguages(true);

                $skills_missing = array();
                $all_skills = "";

                foreach ($_GET as $key => $value) {
                    // all skill-related posted values are just the skill_ID ~ a uint value
                    if (eF_checkParameter($key, 'unit')) {
                        if ($value == 1) {
                            $skills_missing[] = $key;
                            $all_skills .= "&".$skill_item['id'] . "=1";
                        } else {
                            $all_skills .= "&".$skill_item['id'] . "=0";
                        }
                    }
                }
                // This smarty variable will denote all missing and existing skills
                $smarty -> assign("T_MISSING_SKILLS_URL", $all_skills);

                // check what you GET and keep only the skills
                $skills_missing = implode("','",  $skills_missing);

                $user = EfrontUserFactory :: factory($_GET['user']);
                $alredy_attending = implode("','",  array_keys($user -> getLessons()));

                $lessons_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID JOIN lessons ON lessons.id = module_hcd_lesson_offers_skill.lesson_ID","module_hcd_lesson_offers_skill.lesson_ID, lessons.*, count(module_hcd_lesson_offers_skill.skill_ID) as skills_offered", "module_hcd_lesson_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_lesson_offers_skill.lesson_ID NOT IN ('".$alredy_attending."')","","module_hcd_lesson_offers_skill.lesson_ID ORDER BY skills_offered DESC");

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $lessons_proposed = eF_multiSort($lessons_proposed, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $lessons_proposed = eF_filterData($lessons_proposed, $_GET['filter']);
                }
                $smarty -> assign("T_PROPOSED_LESSONS_SIZE", sizeof($lessons_proposed));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $lessons_proposed = array_slice($lessons_proposed, $offset, $limit);
                }
                foreach ($lessons_proposed as $key => $proposed_lesson) {
                    $obj = new EfrontLesson($proposed_lesson['lesson_ID']);
                    $lessons_proposed[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=lessons&edit_lesson='.$proposed_lesson['id']);
                    $lessons_proposed[$key]['direction_name'] = $directionsPaths[$proposed_lesson['directions_ID']];
                    $lessons_proposed[$key]['languages_NAME'] = $languages[$proposed_lesson['languages_NAME']];
                }
//pr($lessons_proposed);
                $smarty -> assign("T_PROPOSED_LESSONS_DATA", $lessons_proposed);

                $smarty -> display('administrator.tpl');
                exit;
            }


            // PROPOSED COURSES
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'proposedCoursesTable') {
                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                $directionsTree = new EfrontDirectionsTree();
                $directionsPaths = $directionsTree -> toPathString();
                $languages       = EfrontSystem :: getLanguages(true);

                $skills_missing = array();
                $all_skills = "";

                foreach ($_GET as $key => $value) {
                    // all skill-related posted values are just the skill_ID ~ a uint value
                    if (eF_checkParameter($key, 'unit')) {
                        if ($value == 1) {
                            $skills_missing[] = $key;
                            $all_skills .= "&".$skill_item['id'] . "=1";
                        } else {
                            $all_skills .= "&".$skill_item['id'] . "=0";
                        }
                    }
                }
                // This smarty variable will denote all missing and existing skills
                $smarty -> assign("T_MISSING_SKILLS_URL", $all_skills);

                // check what you GET and keep only the skills
                $skills_missing = implode("','",  $skills_missing);

                $user = EfrontUserFactory :: factory($_GET['user']);

                $alredy_attending = implode("','",  array_keys($user -> getCourses()));
                $courses_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID JOIN courses ON courses.id = module_hcd_course_offers_skill.course_ID","module_hcd_course_offers_skill.course_ID, courses.*, count(module_hcd_course_offers_skill.skill_ID) as skills_offered", "module_hcd_course_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_course_offers_skill.course_ID NOT IN ('".$alredy_attending."')","","module_hcd_course_offers_skill.course_ID ORDER BY skills_offered DESC");

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $courses_proposed = eF_multiSort($courses_proposed, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $courses_proposed = eF_filterData($courses_proposed, $_GET['filter']);
                }
                $smarty -> assign("T_PROPOSED_COURSES_SIZE", sizeof($courses_proposed));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $courses_proposed = array_slice($courses_proposed, $offset, $limit);
                }
                foreach ($courses_proposed as $key => $proposed_course) {
                    $obj = new EfrontCourse($proposed_course['course_ID']);
                    $courses_proposed[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=courses&edit_course='.$proposed_course['id']);
                    $courses_proposed[$key]['direction_name'] = $directionsPaths[$proposed_course['directions_ID']];
                    $courses_proposed[$key]['languages_NAME'] = $languages[$proposed_course['languages_NAME']];
                }
//pr($courses_proposed);
                $smarty -> assign("T_PROPOSED_COURSES_DATA", $courses_proposed);

                $smarty -> display('administrator.tpl');
                exit;
            }

            // ASSIGNED LESSONS
            if (isset($_GET['ajax'])  && $_GET['ajax'] == 'assignedLessonsTable') {
                $directionsTree = new EfrontDirectionsTree();
                $directionPaths = $directionsTree -> toPathString();
                $lessons        = EfrontLesson :: getLessons();

                $editedUser = EfrontUserFactory :: factory($_GET['user']);
                $userLessons    = $editedUser -> getLessons(true);
                foreach ($lessons as $key => $lesson) {
                    $lessons[$key]['directions_name'] = $directionPaths[$lesson['directions_ID']];
                    $lessons[$key]['user_type']       = $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type'];
                    $lessons[$key]['partof']          = 0;
                    if (in_array($lesson['id'], array_keys($userLessons))) {
                        $lessons[$key]['from_timestamp']  = $userLessons[$key] -> userStatus['from_timestamp'];
                        $lessons[$key]['partof']          = 1;
                        $lessons[$key]['user_type']       = $userLessons[$key] -> userStatus['user_type'];
                        $lessons[$key]['completed']       = $userLessons[$key] -> userStatus['completed'];
                        $lessons[$key]['score']           = $userLessons[$key] -> userStatus['score'];
                    } else if ($currentUser -> user['user_type'] != 'administrator' || !$lesson['active']) {
                        unset($lessons[$key]);
                    } else if ($lesson['languages_NAME'] != $editedUser -> user['languages_NAME']) {
                        unset($lessons[$key]);
                    }
                    if ($lesson['course_only']) {
                        unset($lessons[$key]);
                    }
                }

                $roles = EfrontLessonUser :: getLessonsRoles(true);
                $smarty -> assign("T_ROLES_ARRAY", $roles);

                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $lessons = eF_multiSort($lessons, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $lessons = eF_filterData($lessons, $_GET['filter']);
                }
                $smarty -> assign("T_ASSIGNED_LESSONS_SIZE", sizeof($lessons));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $lessons = array_slice($lessons, $offset, $limit);
                }
                //foreach ($lessons as $key => $lesson) {
                    //$lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
                //}
                foreach ($lessons as $key => $lesson) {
                    if (!$lesson['partof']) {
                        unset($lessons[$key]);
                    } else {
                        $obj = new EfrontLesson($lesson['id']);
                        $lessons[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=lessons&edit_lesson='.$lesson['id']);
                    }
                }
                $smarty -> assign("T_ASSIGNED_LESSONS_DATA", $lessons);
                //pr($lessons);
//pr($lessons);
                $smarty -> display('administrator.tpl');
                exit;
            }

            if (isset($_GET['ajax'])  && $_GET['ajax'] == 'assignedCoursesTable') {
                $directionsTree = new EfrontDirectionsTree();
                $directionPaths = $directionsTree -> toPathString();
                $courses        = EfrontCourse :: getCourses();

                $editedUser = EfrontUserFactory :: factory($_GET['user']);
                $userCourses    = $editedUser -> getCourses(true);
                foreach ($courses as $key => $course) {
                    $courses[$key]['directions_name'] = $directionPaths[$course['directions_ID']];
                    $courses[$key]['user_type']       = $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type'];
                    $courses[$key]['partof']          = 0;
                    if (in_array($course['id'], array_keys($userCourses))) {
                        $courses[$key]['from_timestamp']  = $userCourses[$key] -> userStatus['from_timestamp'];
                        $courses[$key]['partof']          = 1;
                        $courses[$key]['user_type']       = $userCourses[$key] -> userStatus['user_type'];
                        $courses[$key]['completed']       = $userCourses[$key] -> userStatus['completed'];
                        $courses[$key]['score']           = $userCourses[$key] -> userStatus['score'];
                    } else if ($currentUser -> user['user_type'] != 'administrator' || !$course['active']) {
                        unset($courses[$key]);
                    } else if ($course['languages_NAME'] != $editedUser -> user['languages_NAME']) {
                        unset($courses[$key]);
                    }
                    if ($course['course_only']) {
                        unset($courses[$key]);
                    }
                }

                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $courses = eF_multiSort($courses, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $courses = eF_filterData($courses, $_GET['filter']);
                }
                $smarty -> assign("T_ASSIGNED_COURSES_SIZE", sizeof($courses));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $courses = array_slice($courses, $offset, $limit);
                }
                //foreach ($courses as $key => $course) {
                    //$courses[$key]['languages_NAME'] = $languages[$course['languages_NAME']];
                //}
                foreach ($courses as $key => $course) {
                    if (!$course['partof']) {
                        unset($courses[$key]);
                    } else {
                        $obj = new EfrontCourse($course['id']);
                        $courses[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=courses&edit_course='.$course['id']);
                    }
                }
                $smarty -> assign("T_ASSIGNED_COURSES_DATA", $courses);
                //pr($courses);
//pr($courses);
                $smarty -> display('administrator.tpl');
                exit;
            }


            if (isset($_GET['ajax'])  && $_GET['ajax'] == 'coursesTable') {
                $directionsTree = new EfrontDirectionsTree();
                $directionPaths = $directionsTree -> toPathString();
                $courses        = EfrontCourse :: getCourses();

                $editedUser = EfrontUserFactory :: factory($_GET['user']);
                $userCourses    = $editedUser -> getCourses(true);
                foreach ($courses as $key => $course) {
                    $courses[$key]['partof']          = 0;
                    $courses[$key]['directions_name'] = $directionPaths[$course['directions_ID']];
                    $courses[$key]['user_type']       = $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type'];
                    if (in_array($course['id'], array_keys($userCourses))) {
                        $courses[$key]['from_timestamp']  = $userCourses[$key] -> userStatus['from_timestamp'];
                        $courses[$key]['partof']          = 1;
                        $courses[$key]['user_type']       = $userCourses[$key] -> userStatus['user_type'];
                        $courses[$key]['completed']       = $userCourses[$key] -> userStatus['completed'];
                        $courses[$key]['score']           = $userCourses[$key] -> userStatus['score'];
                    } else if ($currentUser -> user['user_type'] != 'administrator' || !$course['active']) {
                        unset($courses[$key]);
                    } else if ($course['languages_NAME'] != $editedUser -> user['languages_NAME']) {
                        unset($courses[$key]);
                    }
                }
                $courses = array_values($courses); //Reindex so that sorting works

                $roles = EfrontLessonUser :: getLessonsRoles(true);
                $smarty -> assign("T_ROLES_ARRAY", $roles);


                isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort'])) {
                    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                    $courses = eF_multiSort($courses, $_GET['sort'], $order);
                }
                if (isset($_GET['filter'])) {
                    $courses = eF_filterData($courses, $_GET['filter']);
                }
                $smarty -> assign("T_COURSES_SIZE", sizeof($courses));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $courses = array_slice($courses, $offset, $limit);
                }
                //foreach ($courses as $key => $course) {
                    //$courses[$key]['languages_NAME'] = $languages[$course['languages_NAME']];
                //}

                $smarty -> assign("T_COURSES_DATA", $courses);

                $smarty -> display($_SESSION['s_type'].'.tpl');
                exit;
            }

            $myarray = array();

            $myarray[0] = array('id'=>1, 'skill'=>'Knowledge of Greedy Algorithms', 'score'=>45);
            $myarray[1] = array('id'=>2, 'skill'=>'Knowledge of Maya Civilization', 'score'=>65);
            $myarray[2] = array('id'=>3, 'skill'=>'Knowledge of Psychology', 'score'=>75);
            $myarray[3] = array('id'=>4, 'skill'=>'Knowledge of Advanced Nanorobotics', 'score'=>25);


            //eF_getTableData("
            $smarty -> assign("T_SKILLSGAP",$myarray);

            // Get the missing skills according to the analysis
            $skills_missing = array();
            $all_skills = "";
            foreach ($myarray as $skill_item) {
                if ($skill_item['score'] < 50) {
                    $skills_missing[] = $skill_item['id'];
                    $all_skills .= "&".$skill_item['id'] . "=1";
                } else {
                    $all_skills .= "&".$skill_item['id'] . "=0";
                }
            }

            // This smarty variable will denote all missing and existing skills
            $smarty -> assign("T_MISSING_SKILLS_URL", $all_skills);
//pr($skills_missing);
            $skills_missing = implode("','",  $skills_missing);
            $user = EfrontUserFactory :: factory($_GET['user']);

            $lessons_attending = implode("','",  array_keys($user -> getLessons()));
            $lessons_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID","module_hcd_lesson_offers_skill.lesson_ID, count(module_hcd_lesson_offers_skill.skill_ID) as skills_offered", "module_hcd_lesson_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_lesson_offers_skill.lesson_ID NOT IN ('".$lessons_attending."')","","module_hcd_lesson_offers_skill.lesson_ID ORDER BY skills_offered DESC");


            $courses_attending = implode("','",  array_keys($user -> getCourses()));
            $courses_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID","module_hcd_course_offers_skill.course_ID, count(module_hcd_course_offers_skill.skill_ID) as skills_offered", "module_hcd_course_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_course_offers_skill.course_ID NOT IN ('".$courses_attending."')","","module_hcd_course_offers_skill.course_ID ORDER BY skills_offered DESC");


        } else {
            // SHOW USERS LIST

        }



    } else {

        // SHOW TESTS LIST
    }

}




/*
 User types is the page that concerns direction administration. Here the administrator can view, add, delete and modify User types
 There are 5 sub options in this page, denoted by an extra link part:
 - &add_user_type=1                       When we are adding a new user_type
 - &delete_user_type=<user_type>          When we want to delete user type <user_type>
 - &edit_user_type=<user_type>            When we want to edit user type <user_type>
 - &deactivate_user_type=<user_type>      When we deactivate user type <user_type>
 - &activate_user_type=<user_type>        When we activate user type <user_type>
 */
elseif ($ctg == 'user_types') {
    if (isset($currentUser -> coreAccess['user_types']) && $currentUser -> coreAccess['user_types'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';

    if (isset($_GET['delete_user_type']) && eF_checkParameter($_GET['delete_user_type'], 'id')) {
        if (isset($currentUser -> coreAccess['user_types']) && $currentUser -> coreAccess['user_types'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        if (eF_deleteTableData("user_types", "id='".$_GET['delete_user_type']."'") && eF_updateTableData("users", array("user_types_ID" => 0), "user_types_ID=".$_GET['delete_user_type'])) {
            $message      = _USERTYPEDELETED;
            $message_type = 'success';
        } else {
            $message      = _USERTYPECOULDNOTBEDELETED;
            $message_type = 'failure';
        }
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=user_types&message=".$message."&message_type=".$message_type);
    } elseif (isset($_GET['deactivate_user_type']) && eF_checkParameter($_GET['deactivate_user_type'], 'id')) {
        if (isset($currentUser -> coreAccess['user_types']) && $currentUser -> coreAccess['user_types'] != 'change') {
            echo _UNAUTHORIZEDACCESS;
            exit;
        }
        if (!eF_updateTableData("user_types", array('active' => 0), "id='".$_GET['deactivate_user_type']."'")) {
            header("HTTP/1.0 500 ");
            echo _SOMEPROBLEMEMERGED;
        }
        exit;
    } elseif (isset($_GET['activate_user_type']) && eF_checkParameter($_GET['activate_user_type'], 'id')) {
        if (isset($currentUser -> coreAccess['user_types']) && $currentUser -> coreAccess['user_types'] != 'change') {
            echo _UNAUTHORIZEDACCESS;
            exit;
        }
        if (!eF_updateTableData("user_types", array('active' => 1), "id='".$_GET['activate_user_type']."'")) {
            header("HTTP/1.0 500 ");
            echo _SOMEPROBLEMEMERGED;
        }
        exit;
    } elseif (isset($_GET['add_user_type']) || (isset($_GET['edit_user_type']) && eF_checkParameter($_GET['edit_user_type'], 'text'))) {
        $studentOptions       = array("content"           => _CONTENT,
                                      "calendar"          => _CALENDAR,
                                      "statistics"        => _STATISTICS,
                                      "forum"             => _FORUM,
                                      "personal_messages" => _PERSONALMESSAGES,
                                      "surveys"           => _SURVEYS,
                                      "chat"              => _CHAT,
                                      "control_panel"     => _CONTROLPANEL,
                                      "news"              => _ANNOUNCEMENTS);

        $professorOptions     = array("settings"          => _LESSONOPTIONS,
                                      "users"             => _LESSONUSERS,
                                      "content"           => _CONTENT,
                                      "news"              => _ANNOUNCEMENTS,
                                      "files"             => _FILES,
                                      "progress"          => _USERSPROGRESS,
                                      "glossary"          => _GLOSSARY,
                                      "calendar"          => _CALENDAR,
                                      "statistics"        => _STATISTICS,
                                      "forum"             => _FORUM,
                                      "personal_messages" => _PERSONALMESSAGES,
                                      "surveys"           => _SURVEYS,
                                      "chat"              => _CHAT,
                                      "control_panel"     => _CONTROLPANEL);

        $administratorOptions = array("lessons"           => _LESSONS,
                                      "users"             => _USERS,
                                      "configuration"     => _CONFIGURATIONOPTIONS,
                                      "set_style"         => _SETSTYLE,
                                      "logout_user"       => _LOGOUTUSER,
                                      "user_profile"      => _USERPROFILE,
                                      "user_types"        => _USERTYPES,
                                      "set_logo"          => _CHANGESITELOGO,
                                      "cms"               => _CMS,
                                      "languages"         => _LANGUAGES,
                                      "version_key"       => _VERSIONKEY,
                                      "maintenance"       => _MAINTENANCE,
                                      "backup"            => _BACKUPRESTORE,
                                      "modules"           => _MODULES,
                                      "statistics"        => _STATISTICS,
                                      "calendar"          => _CALENDAR,
                                      "news"              => _ANNOUNCEMENTS,
                                      "forum"             => _FORUM,
                                      "personal_messages" => _PERSONALMESSAGES,
                                      "chat"              => _CHAT,
                                      //"paypal"            => _PAYPAL,
                                      "control_panel"     => _CONTROLPANEL);
        if (MODULE_PAYPAL) {
            $administratorOptions["paypal"] = _PAYPAL;
        }
        
        if ($_SESSION['s_version_type'] == 'Educational' || $_SESSION['s_version_type'] == 'Enterprise') {
            $administratorOptions["skillgaptests"] = _SKILLGAPTESTS;
        }

        $basicTypes = EfrontUser :: $basicUserTypesTranslations;

        if (isset($_GET['edit_user_type'])) {
            $result    = eF_getTableData("user_types", "*", "id='".$_GET['edit_user_type']."'");
            $basicType = $result[0]['basic_user_type'];
        } else if (isset($_GET['basic_type']) && in_array($_GET['basic_type'], array_keys($basicTypes))) {
            $basicType = $_GET['basic_type'];
        } else {
            $basicType = 'student';
        }

        switch($basicType){
            case "administrator":
                $options = $administratorOptions;
                break;
            case "professor":
                $options = $professorOptions;
                break;
            default:
                $options = $studentOptions;
                break;
        }

        isset($_GET['add_user_type']) ? $postTarget = 'add_user_type=1' : $postTarget = "edit_user_type=".$_GET['edit_user_type'];
        $form = new HTML_QuickForm("add_type_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=user_types&".$postTarget."&basic_type=".$basicType, "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
        $form -> registerRule('checkNotExist', 'callback', 'eF_checkNotExist');

        $form -> addElement('text', 'name', _TYPENAME, 'class = "inputText"');
        $form -> addRule('name', _THEFIELD.' '._TYPENAME.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');
        $form -> addRule('name', _USERTYPE.' &quot;'.($form -> exportValue('name')).'&quot; '._ALREADYEXISTS, 'checkNotExist', 'user_type');

        $form -> addElement('select', 'basic_user_type', _BASICUSERTYPE, $basicTypes, 'class = "inputSelect" onchange = "location = \'administrator.php?ctg=user_types&'.$postTarget.'&basic_type=\'+this.options[this.selectedIndex].value+\'&name=\'+document.getElementsByName(\'name\')[0].value"');

        foreach ($options as $key => $value){
            $form -> addElement("select", "core_access[$key]",  $value, array('change' => _CHANGE, 'view' => _VIEW, 'hidden' => _HIDE));
        }
        $form -> setDefaults(array('basic_user_type' => $basicType, 'name' => $_GET['name']));

        if (isset($_GET['edit_user_type'])) {
            $form -> freeze(array('basic_user_type'));
            $form -> setDefaults(array('name'            => $result[0]['name'],
                                       'basic_user_type' => $result[0]['basic_user_type'],
                                       'core_access'     => unserialize($result[0]['core_access'])));
            $smarty -> assign("T_USER_TYPE_NAME", $result[0]['name']);
        }

        if ((isset($currentUser -> coreAccess['user_types']) && $currentUser -> coreAccess['user_types'] != 'change') || ($currentUser -> user['user_types_ID'] == $_GET['edit_user_type'])) {
            $form -> freeze();
        } else {
            $form -> addElement('submit', 'submit_type', _SAVE, 'class = "flatButton"');

            if ($form -> isSubmitted() && $form -> validate()) {
                $values = $form -> exportValues();
                $fields = array("name"            => $values['name'],
                                "basic_user_type" => $values['basic_user_type'],
                                "core_access"     => serialize($values['core_access']));

                if (isset($_GET['edit_user_type'])) {
                    if (eF_updateTableData("user_types", $fields, "id=".$_GET['edit_user_type'])) {
                        $message      = _SUCCESFULLYUPDATEDUSERTYPE;
                        $message_type = 'success';
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=user_types&message=".urlencode($message)."&message_type=".$message_type);
                    } else {
                        $message      = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                } else {
                    if (eF_insertTableData("user_types", $fields)) {
                        $message      = _SUCCESFULLYADDEDUSERTYPE;
                        $message_type = 'success';
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=user_types&message=".urlencode($message)."&message_type=".$message_type);
                    } else {
                        $message      = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                }

            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);

        $smarty -> assign('T_USERTYPES_OPTIONS', $options);
        $smarty -> assign('T_USERTYPES_FORM', $renderer -> toArray());

    } else {
        $result = eF_getTableData("user_types", "*");
        $smarty -> assign("T_USERTYPES_DATA", $result);
        $smarty -> assign("T_BASIC_USER_TYPES", EfrontUser :: $basicUserTypesTranslations);
    }
}
elseif ($ctg == 'user_groups') {
    if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    if (isset($_GET['delete_user_group']) && eF_checkParameter($_GET['delete_user_group'], 'id')) {
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        try {
            $group = new EfrontGroup($_GET['delete_user_group']);
            $group -> delete();
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=user_groups&message=".urlencode(_GROUPDELETED)."&message_type=success");
        } catch (Exception $e) {
            $message      = $e -> getMessage();
            $message_type = 'failure';
        }
    } elseif (isset($_GET['deactivate_user_group']) && eF_checkParameter($_GET['deactivate_user_group'], 'id')) {
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $group = new EfrontGroup($_GET['deactivate_user_group']);
            $group -> group['active'] = 0;
            $group -> persist();
            $message = _GROUPDEACTIVATED;
        } catch (Exception $e) {
            $message = $e -> getMessage();
        }
        echo $message;exit;
    } elseif (isset($_GET['activate_user_group']) && eF_checkParameter($_GET['activate_user_group'], 'id')) {
        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            $group = new EfrontGroup($_GET['activate_user_group']);
            $group -> group['active'] = 1;
            $group -> persist();
            $message = _GROUPACTIVATED;
        } catch (Exception $e) {
            $message = $e -> getMessage();
        }
        echo $message;exit;
    } elseif (isset($_GET['add_user_group']) || ( isset($_GET['edit_user_group']) && eF_checkParameter($_GET['edit_user_group'], 'id')) ) {
        $loadScripts[] = 'scriptaculous/scriptaculous';
        $loadScripts[] = 'scriptaculous/effects';

        isset($_GET['add_user_group']) ? $postTarget = 'add_user_group=1' : $postTarget = "edit_user_group=".$_GET['edit_user_group'];
        $form = new HTML_QuickForm("add_group_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=user_groups&$postTarget", "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
        $form -> registerRule('checkNotExist', 'callback', 'eF_checkNotExist');

        $form -> addElement('text', 'name', _NAME, 'class = "inputText"');
        $form -> addElement('text', 'description', _DESCRIPTION, 'class = "inputText"');
        $form -> addRule('name', _THEFIELD.' '._TYPENAME.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');


        if (isset($_GET['edit_user_group'])) {
            try {
                $currentGroup = new EfrontGroup($_GET['edit_user_group']);
            } catch (Exception $e) {
                $message      = $e -> getMessage();
                $message_type = 'failure';
            }
            $form -> setDefaults(array('name' => $currentGroup -> group['name'], 'description' => $currentGroup -> group['description']));
        }

        if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
            $form -> freeze();
        } else {
            $form -> addElement('submit', 'submit_type', _SUBMIT, 'class = "flatButton"');

            if ($form -> isSubmitted() && $form -> validate()) {
                if (isset($_GET['edit_user_group'])) {
                    try {
                        $currentGroup -> group['name']        = $form -> exportValue('name');
                        $currentGroup -> group['description'] = $form -> exportValue('description');
                        $currentGroup -> persist();
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=user_groups&message=".urlencode(_SUCCESFULLYUPDATEDGROUP)."&message_type=success");
                    } catch (Exception $e){
                        $message      = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                } else {
                    $content['name']        = $form -> exportValue('name');
                    $content['description'] = $form -> exportValue('description');
                    try {
                        $group = EfrontGroup::create($content);
                    } catch (Exception $e){
                        $message      = $e -> getMessage();;
                        $message_type = 'failure';
                    }
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=user_groups&edit_user_group=".$group -> group['id']."&tab=users&message=".urlencode(_SUCCESFULLYADDEDGROUP)."&message_type=success");
                }
            }
        }
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);
        $smarty -> assign('T_USERGROUPS_FORM_R', $renderer -> toArray());

        if (isset($_GET['edit_user_group'])) {
            $groupUsers = $currentGroup -> getUsers();
            $result     = eF_getTableData("users", "*");
            $users      = array();
            foreach ($result as $user) {
                $user['in_group'] = false;
                if (in_array($user['login'], $groupUsers[$user['user_type']])) {
                    $user['in_group']      = true;
                    $users[$user['login']] = $user;
                } else if ($user['active']) {
                    $users[$user['login']] = $user;
                }

            }

            if (isset($_GET['ajax'])) {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }

                if (isset($_GET['filter'])) {
                    $users = eF_filterData($users, $_GET['filter']);
                }
                $users = eF_multiSort($users, $sort, $order);
                $smarty -> assign("T_USERS_SIZE", sizeof($users));

                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $users = array_slice($users, $offset, $limit);
                }

                $smarty -> assign("T_GROUP_USERS", $users);
                $smarty -> display('administrator.tpl');
                exit;
            }
            if (isset($_GET['postAjaxRequest'])) {
                if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
                    if ($users[$_GET['login']]['in_group']) {
                        eF_deleteTableData("users_to_groups", "users_LOGIN='".$_GET['login']."' and groups_ID=".$_GET['edit_user_group']);
                        echo "Deleted user ".$_GET['login']." from group";
                    } else {
                        $fields_insert = array("users_LOGIN" =>  $_GET['login'],
                                               "groups_ID"   =>  $_GET['edit_user_group']);
                        eF_insertTableData("users_to_groups", $fields_insert);
                        echo "Added user ".$_GET['login']." to group";
                    }
                } else if (isset($_GET['selectAll'])) {
                    if (isset($_GET['status']) && $_GET['status'] == 'true') {
                        foreach ($users as $user) {
                            if (!$user['in_group']) {
                                $fields_insert = array("users_LOGIN" =>  $user['login'],
                                                       "groups_ID"   =>  $_GET['edit_user_group']);
                                eF_insertTableData("users_to_groups", $fields_insert);
                                echo "Added user ".$user['login']." to group";
                            }
                        }
                    } else {
                        eF_deleteTableData("users_to_groups", "groups_ID=".$_GET['edit_user_group']);
                        echo "All users where deleted from group";
                    }
                } else {
                    echo "Error setting state for user ".$_GET['login'].". User name is not valid";
                }
                exit;
            }

        }

    } else {
        $result = eF_getTableData("groups g LEFT OUTER JOIN users_to_groups ug ON g.id=ug.groups_ID", "g.*, count(ug.groups_ID) as num_users", "", "", "id");
        $smarty -> assign("T_USERGROUPS", $result);
    }
}
/*
 */
elseif ($ctg == 'cms') {
    if (isset($currentUser -> coreAccess['cms']) && $currentUser -> coreAccess['cms'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    try {
        $default_page = $GLOBALS['configuration']['cms_page'];
        $filesystem   = new FileSystemTree(G_ADMINPATH);
        foreach (new EfrontFileTypeFilterIterator(new ArrayIterator($filesystem -> tree), array('php')) as $key => $value) {
            $pages[] = basename($key, '.php');
        }
        $smarty -> assign('T_CMS_PAGES', $pages);
        $smarty -> assign('T_DEFAULT_PAGE', $default_page);
    } catch (Exception $e) {
        $message      = $e -> getMessage();
        $message_type = 'failure';
    }

    if (isset($_GET['delete_page']) && in_array($_GET['delete_page'], $pages)) {
        if (isset($currentUser -> coreAccess['cms']) && $currentUser -> coreAccess['cms'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        if (unlink (G_ADMINPATH."".$_GET['delete_page'].".php")) {
            if ($GLOBALS['configuration']['page'] == $_GET['delete_page']) {
                EfrontConfiguration :: setValues("cms_page", false);
            }
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=cms&message=".urlencode(_PAGEDELETED)."&message_type=success");
        } else {
            $message      = _PAGECOULDNOTBEDELETED;
            $message_type = 'failure';
        }
    } elseif (isset($_GET['use_none'])) {
        if (isset($currentUser -> coreAccess['cms']) && $currentUser -> coreAccess['cms'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        try {
            EfrontConfiguration :: setValue("cms_page", false);
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    } /*elseif (isset($_GET['file_manager'])) {
        if (isset($currentUser -> coreAccess['cms']) && $currentUser -> coreAccess['cms'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $loadScripts[] = 'drag-drop-folder-tree';
        $loadScripts[] = 'scriptaculous/effects';
        $basedir    = G_ADMINPATH;
        try {
            $filesystem = new FileSystemTree($basedir);
            $filesystem -> handleAjaxActions($currentUser);

            $url        = basename($_SERVER['PHP_SELF']).'?ctg=cms&file_manager=1';
            $options    = array('share' => false);

            if (isset($_GET['ajax'])) {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }

                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                }
                isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options);
                exit;
            }
            $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options));
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }

    }*/ elseif (isset($_GET['set_page'])) {
        if (isset($currentUser -> coreAccess['cms']) && $currentUser -> coreAccess['cms'] != 'change') {
            echo urlencode(_UNAUTHORIZEDACCESS);
            exit;
        }
        if (!in_array($_GET['set_page'], $pages)) {
            header("HTTP/1.0 500 ");
            echo _INVALIDPAGE;
        }
        try {
            EfrontConfiguration :: setValue('cms_page', $_GET['set_page']);
            //header("location:".basename($_SERVER['PHP_SELF'])."?ctg=cms&message=".urlencode(_PAGESETSUCCESSFULLY)."&message_type=success");
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    } elseif (isset($_GET['add_page']) || (isset($_GET['edit_page']) && in_array($_GET['edit_page'], $pages) && eF_checkParameter($_GET['edit_page'], 'filename'))) {
        if (isset($currentUser -> coreAccess['cms']) && $currentUser -> coreAccess['cms'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        if (isset($_GET['postAjaxRequest_insert'])) {
			$file_id 		= urldecode($_GET['file_id']);
            $file_insert 	= new EfrontFile($file_id);
			if (strpos($file_insert['mime_type'] , "image") !== false) {
				$img_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
				echo "<img src=\"".$img_return."\" border=0 />";
				exit;
			} elseif (strpos($file_insert['mime_type'] , "pdf") !== false) {
				$pdf_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
				echo '<iframe src="'.$pdf_return.'"  name="pdfFrame_'.urlencode($file_insert['id']).'" width="100%" height="600"></iframe>';
				exit;
			} elseif (strpos($file_insert['mime_type'] , "php") !== false) {
				$php_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/content/admin/"));
				echo '<a href="'.G_RELATIVEADMINLINK.$php_return.'">'.$php_return.'</a><br />';
				exit;
			}elseif (strpos($file_insert['mime_type'] , "flash") !== false) {
				$flash_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
				if ($_GET['editor_mode'] == "true") {
					echo '<img width="400" height="400" src="editor/tiny_mce/themes/advanced/images/spacer.gif"  title="'.$flash_return.'" alt="'.$flash_return.'" class="mceItemFlash" />';
					exit;
				} else {
					echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="400" height="400">
					<param name="src" value="'.$flash_return.'" />
					<param name="width" value="400" />
					<param name="height" value="400" />
					<embed type="application/x-shockwave-flash" src="'.$flash_return.'" width="400" height="400"></embed>
					</object>';
					exit;
				}
			} else {
				echo "<a href=view_file.php?action=download&file=".$file_id.">".$file_insert['physical_name']."</a>";
				exit;	
				
			}
        }
        $load_editor = true;
        isset($_GET['edit_page']) ? $post_target = '&edit_page='.$_GET['edit_page'] : $post_target = '&add_page=1';

        $form = new HTML_QuickForm("add_page_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=cms".$post_target, "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
        $form -> addElement('text', 'name', _FILENAME, 'class = "inputText"');
        $form -> addRule('name', _THEFIELD.' '._FILENAME.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');
        $form -> addElement('textarea', 'page', _PAGECONTENT, 'id="editor_cms_data" class = "inputContentTextarea templateEditor" style = "width:100%;height:30em;"');

        if (isset($_GET['edit_page'])) {
            $pageContent      = file_get_contents(G_ADMINPATH."".$_GET['edit_page'].".php");
            $defaults['name'] = $_GET['edit_page'];
            $defaults['page'] = preg_replace("/.*<<<EOT(.*)EOT.*/s", "\$1", $pageContent);//, false, $matches);
            $form -> setDefaults($defaults);
        } else {
            $defaults['page'] = '<a href="'.G_SERVERNAME.'index.php?index_efront">'._EFRONTLOGIN.'</a>';
            $form -> setDefaults($defaults);
        }
        $form -> addElement('submit', 'submit_cms', _SUBMIT, 'class = "flatButton"');

        if ($form -> isSubmitted() && $form -> validate()) {
            $values   = $form -> exportValues();
            $filename = G_ADMINPATH.$values['name'].'.php';
            if (is_file(G_ADMINPATH.'cms_templates/default_template.php')) {
                $defaultContent = file_get_contents(G_ADMINPATH.'cms_templates/default_template.php');
                $newContent     = preg_replace("/put_content_here/", $values['page'], $defaultContent);
            } else {
                $newContent = $values['page'];
            }
            file_put_contents($filename, $newContent);
            chmod($filename, 0644);
            try {
                header("location:".basename($_SERVER['PHP_SELF'])."?ctg=cms&message=".urlencode(_SUCCESFULLYADDEDPAGE)."&message_type=success");
            } catch (Exception $e) {
                $message      = $e -> getMessage().'('.$e -> getCode().')';
                $message_type = 'failure';
            }
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);
        $smarty -> assign('T_CMS_FORM', $renderer -> toArray());
    
		$loadScripts[] = 'drag-drop-folder-tree';
        $loadScripts[] = 'scriptaculous/effects';
        $basedir    = G_ADMINPATH;
        try {
            $filesystem = new FileSystemTree($basedir);
            $filesystem -> handleAjaxActions($currentUser);

            if (isset($_GET['edit_page'])) {
            	$url = basename($_SERVER['PHP_SELF']).'?ctg=cms&edit_page='.$_GET['edit_page'];
            }else{
                $url = basename($_SERVER['PHP_SELF']).'?ctg=cms&add_page=1';
			}
            $options    = array('share' => false);

            if (isset($_GET['ajax'])) {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }

                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                }
                isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                $ajaxOptions 	= array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                $extraFileTools = array(array('image' => 'images/16x16/arrow_right_green.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
                echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, $extraFileTools, '', '', '', false);
                exit;
            }
            $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, $extraFileTools, '', '', '', false));
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
	} 
}
/*
 Calendar
 */
elseif ($ctg == 'calendar') {
    if ($currentUser -> coreAccess['calendar'] != 'hidden') {
        include_once "calendar.php";
    } else {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
}
/*
 Search courses is used to find the course users that fulfill an arbitrary number of criteria
 */
elseif ($ctg == 'search_courses') {
    include "search_courses.php";
}
/*
 Statistics is the page that calculates and displays the system statistics. It depends entirely on
 module_statistics.php
 */
elseif ($ctg == 'statistics') {
    if (isset($currentUser -> coreAccess['statistics']) && $currentUser -> coreAccess['statistics'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    include "module_statistics.php";
}
/*
 MODULE HCD: Include the module hcd.php file
 */
elseif ($ctg == 'module_hcd') {
    $loadScripts = array_merge($loadScripts, array('scriptaculous/prototype', 'scriptaculous/scriptaculous'));
    include "module_hcd.php";
}
/*
///MODULES3: Include the module code
 */
elseif ($ctg == 'module') {

//    $loadScripts = array_merge($loadScripts, array('scriptaculous/prototype', 'scriptaculous/scriptaculous'));
    $className = $_GET['op'];
    if (isset($loadedModules[$className])) {

        // Get top title navigational links
        $nav_links = $loadedModules[$className] -> getNavigationLinks();
        if ($nav_links) {
            $links_size = sizeof($nav_links);
            $count = 0;
            $module_nav_links = "";
            foreach ($nav_links as $link) {
                $module_nav_links .= '<a class="titleLink" href ="'.$link['link'].'">'.$link['title'].'</a>';
                $count++;
                if ($count < $links_size) {
                    $module_nav_links .= '&nbsp;&raquo;&nbsp;';
                }
            }
        } else {
            $module_nav_links = '<a class="titleLink" href ="'.$loadedModules[$className] -> moduleBaseUrl.'">'.$loadedModules[$className] -> getName().'</a>';
        }
        $smarty -> assign("T_MODULE_NAVIGATIONAL_LINKS", $module_nav_links);

        // Get link to highlight on the left sidebar
        $highlight = $loadedModules[$className] -> getLinkToHighlight();
        if ($highlight) {
            $highlight = $loadedModules[$className] -> className . "_" . $highlight;
        } else {
            $highlight = $loadedModules[$className] -> className;
        }
        $smarty -> assign("T_MODULE_HIGHLIGHT", $highlight);

        // Get module html - two ways: pure HTML or PHP+smarty
        // If no smarty file is defined then false will be returned
        if ($module_smarty_file = $loadedModules[$className] -> getSmartyTpl()) {
            // Execute the php code
            $loadedModules[$className] -> getModule();
            // Let smarty know to include the module smarty file
            $smarty -> assign("T_MODULE_SMARTY", $module_smarty_file);
        } else {
            // Present the pure HTML code
            $smarty -> assign("T_MODULE_PAGE", $loadedModules[$className] -> getModule());
        }
    } else {
        $message = _ERRORLOADINGMODULE;
        $message_type = "failure";
    }

}

/*
 At this point, we apply module functionality
 */
/*
elseif (sizeof($modules) > 0 && in_array($ctg, array_keys($module_ctgs))) {
    include(G_MODULESPATH.$ctg.'/module.php');
    $smarty -> assign("T_CTG_MODULE", $module_ctgs[$ctg]);
}
*/


$fields_log = array ('users_LOGIN' => $_SESSION['s_login'],                                 //This is the log entry array
                     'timestamp'   => time(),
                     'action'      => 'lastmove',
                     'comments'    => 0,
                     'session_ip'  => eF_encodeIP($_SERVER['REMOTE_ADDR']));

eF_deleteTableData("logs", "users_LOGIN='".$_SESSION['s_login']."' AND action='lastmove'"); //Only one lastmove action interests us, so delete any other
eF_insertTableData("logs", $fields_log);

$smarty -> assign("T_HEADER_EDITOR", $load_editor);                                         //Specify whether we need to load the editor

if (isset($_GET['refresh']) || isset($_GET['refresh_side'])) {
    $smarty -> assign("T_REFRESH_SIDE","true");
}


///MODULES5
$smarty -> assign("T_MODULE_CSS", $module_css_array);
$smarty -> assign("T_MODULE_JS", $module_js_array);
foreach ($loadedModules as $module) {
    $loadScripts = array_merge($loadScripts, $module -> addScripts());
}

if ($message) {
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';
}

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($loadScripts));                    //array_unique, so it doesn't send duplicate entries


$smarty -> assign("T_CURRENT_CTG", $ctg);
$smarty -> assign("T_MENUCTG", $ctg);
//$smarty -> assign("T_MENU", eF_getMenu());

$smarty -> assign("T_QUERIES", $numberOfQueries);

$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_SEARCH_MESSAGE", $search_message);

$smarty -> assign("T_TEST_MESSAGE", 'Test Message');
$debug_timeBeforeSmarty = microtime(true) - $debug_TimeStart;

$smarty -> display('administrator.tpl');
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
