<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}


$loadScripts[] = 'includes/maintenance';
if (isset($currentUser -> coreAccess['maintenance']) && $currentUser -> coreAccess['maintenance'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}
/**Functions to perform status check*/
require_once "check_status.php";

//Create and capture phpinfo code
ob_start();
phpinfo();
$info = ob_get_contents();
ob_end_clean();

$info = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
$smarty -> assign("T_PHPINFO", $info);

 
//Lock down operations
if (!isset($currentUser -> coreAccess['maintenance']) || $currentUser -> coreAccess['maintenance'] == 'change') {
    $load_editor = true;
    $lockdown_form = new HTML_QuickForm("lockdown_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=lock_down", "", null, true);  //Build the form
    $lockdown_form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                                                   //Register our custom input check function

    $lockdown_form -> addElement('textarea', 'lock_message', _LOCKDOWNMESSAGE, 'class = "inputContentTextarea mceEditor" style = "width:100%;height:20em;"');
    $lockdown_form -> addElement('checkbox', 'logout_users', null, null, 'class = "inputCheckBox"');
    $lockdown_form -> setDefaults(array("lock_message"  => $GLOBALS['configuration']['lock_message'] ? $GLOBALS['configuration']['lock_message'] : _SYSTEMDOWNFORMAINTENANCE,
                                                "logout_users"  => true));

    $lockdown_form -> addElement('submit', 'submit_lockdown', _LOCKDOWN, 'class = "flatButton"');
    $lockdown_form -> addElement('submit', 'submit_unlock', _UNLOCK, 'class = "flatButton"');

    //Check here, whether the system is already locked, and present unlock button
    if ($lockdown_form -> isSubmitted() && $lockdown_form -> validate()) {                                                              //If the form is submitted and validated
        $values = $lockdown_form -> exportValues();
        if ($GLOBALS['configuration']['lock_down'] && isset($values['submit_unlock'])) {
            EfrontSystem :: unlockSystem();
        } else {
            EfrontSystem :: lockSystem($values['lock_message'], $values['logout_users']);
        }
        eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=lock_down");
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer
    $lockdown_form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

    $smarty -> assign('T_LOCKDOWN_FORM', $renderer -> toArray());                     //Assign the form to the template

    //User check
    $users     = eF_getTableDataFlat("users", "login");
    //$users_dir = eF_getDirContents(G_ROOTPATH.'upload/', '', false, false);
    $users_dir = scandir(G_ROOTPATH.'upload/');
    foreach ($users_dir as $key => $value) {
        if (!is_dir(G_ROOTPATH.'upload/'.$value) || in_array($value, array('.', '..', '.svn'))) {
            unset($users_dir[$key]);
        }
    }
    $orphan_user_folders = array_diff($users_dir, $users['login']);
    $orphan_users        = array_diff($users['login'], $users_dir);

    $smarty -> assign("T_ORPHAN_USERS", implode(", ", $orphan_users));
    $smarty -> assign("T_ORPHAN_USER_FOLDERS", implode(", ", $orphan_user_folders));

    //Lessons check
    $lessons     = eF_getTableDataFlat("lessons", "id, name");
    $lessons     = array_combine($lessons['id'], $lessons['name']);
    //$lessons_dir = eF_getDirContents(G_ROOTPATH.'www/content/lessons/', '', false, false);
    $lessons_dir = scandir(G_LESSONSPATH);
    foreach ($lessons_dir as $key => $dir) {                                                    //Remove non-integer lessons from list (such as scorm_uploaded_files);
        if (!preg_match("/^\d+$/", $dir)) {
            unset($lessons_dir[$key]);
        }
    }
    $orphan_lesson_folders = array_diff($lessons_dir, array_keys($lessons));
    $orphan_lessons        = array_diff(array_keys($lessons), $lessons_dir);
    $smarty -> assign("T_ORPHAN_LESSONS", implode(", ", array_keys($orphan_lessons)));
    $smarty -> assign("T_ORPHAN_LESSON_FOLDERS", implode(", ", $orphan_lesson_folders));

    if (isset($_GET['cleanup']) && ($_GET['cleanup'] == 'orphan_user_folders' || $_GET['cleanup'] == 'all')) {
        foreach ($orphan_user_folders as $folder) {
            try {
                $dir = new EfrontDirectory(G_ROOTPATH.'upload/'.$folder.'/');
                $dir -> delete();
            } catch (Exception $e) {
                $errors[] = $e -> getMessage();
            }
        }
        if ($_GET['cleanup'] != 'all') {
            if (!isset($errors)) {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_SUCCESFULLYCLEANEDUPFOLDERS).'&message_type=success');
            } else {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_THEFOLLOWINGFOLDERSCOULDNOTBEDELETED).': '.implode(", ", $errors).'&message_type=failure');
            }
        }
    }
    if (isset($_GET['cleanup']) && ($_GET['cleanup'] == 'users_without_folders' || $_GET['cleanup'] == 'all')) {
        foreach ($orphan_users as $login) {
            try {
            $user = EfrontUserFactory::factory($login);
            $user -> delete();
            } catch (Exception $e) {
                $errors[] = $login;
            }
        }
        if ($_GET['cleanup'] != 'all') {
            if (!isset($errors)) {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_SUCCESFULLYCLEANEDUPUSERS).'&message_type=success');
            } else {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_THEFOLLOWINGUSERSCOULDNOTBEDELETED).': '.implode(", ", $errors).'&message_type=failure');
            }
        }
    }
    if (isset($_GET['cleanup']) && ($_GET['cleanup'] == 'orphan_lesson_folders' || $_GET['cleanup'] == 'all')) {
        foreach ($orphan_lesson_folders as $folder) {
            try {
                $dir = new EfrontDirectory(G_ROOTPATH.'www/content/lessons/'.$folder.'/');
                $dir -> delete();
            } catch (Exception $e) {
                $errors[] = $e -> getMessage();
            }
        }
        if ($_GET['cleanup'] != 'all') {
            if (!isset($errors)) {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_SUCCESFULLYCLEANEDUPFOLDERS).'&message_type=success');
            } else {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_THEFOLLOWINGFOLDERSCOULDNOTBEDELETED).': '.implode(", ", $errors).'&message_type=failure');
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
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_SUCCESFULLYCLEANEDUPLESSONS).'&message_type=success');
            } else {
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_THEFOLLOWINGLESSONSCOULDNOTBEDELETED).': '.implode(", ", $errors).'&message_type=failure');
            }
        }
    }
    if (isset($_GET['create'])  && $_GET['create']  == 'user_folders') {
        foreach ($orphan_users as $login) {
            if (!mkdir(G_ROOTPATH.'upload/'.$login)                           ||
            !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments')        ||
            !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments/Drafts') ||
            !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments/Sent')   ||
            !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments/Incoming')) {
                $errors[] = $login;
            }
        }
        if (!isset($errors)) {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_SUCCESFULLYCREATEDUSERFOLDERS).'&message_type=success');
        } else {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_THEFOLLOWINGUSERFOLDERSCOULDNOTBECREATED).': '.implode(", ", $errors).'&message_type=failure');
        }
    }
    if (isset($_GET['create'])  && $_GET['create']  == 'lesson_folders') {
        foreach ($orphan_lessons as $lesson_name => $lesson_id) {
            if (!mkdir(G_ROOTPATH.'www/content/lessons/'.$lesson_id)) {
                $errors[] = $lesson_name;
            }
        }
        if (!isset($errors)) {
            eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_SUCCESFULLYCREATEDLESSONFOLDERS).'&message_type=success');
        } else {
            eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_THEFOLLOWINGLESSONFOLDERSCOULDNOTBECREATED).': '.implode(", ", $errors).'&message_type=failure');
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
    }
    if (isset($_GET['cache']) && $_GET['ajax'] == 1) {
        try {
            if ($_GET['cache'] == 'templates') {
                $cacheTree = new FileSystemTree(G_THEMECACHE, true);
                foreach (new EfrontDirectoryOnlyFilterIterator($cacheTree -> tree) as $value) {
                    $value -> delete();
                }
            } else if ($_GET['cache'] == 'tests') {
                eF_deleteTableData("cache");
            }
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    }
}


?>