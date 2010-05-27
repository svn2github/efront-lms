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
    if (G_VERSION_NUM != $GLOBALS['configuration']['database_version']) {
        $smarty -> assign("T_DIFFERENT_VERSIONS", true);
    }

    $lockdown_form = new HTML_QuickForm("lockdown_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=lock_down", "", null, true); //Build the form
    $lockdown_form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register our custom input check function

    $lockdown_form -> addElement('textarea', 'lock_message', _LOCKDOWNMESSAGE, 'class = "inputContentTextarea mceEditor" style = "width:100%;height:20em;"');
    $lockdown_form -> addElement('checkbox', 'logout_users', null, null, 'class = "inputCheckBox"');
    $lockdown_form -> setDefaults(array("lock_message" => $GLOBALS['configuration']['lock_message'] ? $GLOBALS['configuration']['lock_message'] : _SYSTEMDOWNFORMAINTENANCE,
                                                "logout_users" => true));

    $lockdown_form -> addElement('submit', 'submit_lockdown', _LOCKDOWN, 'class = "flatButton"');
    $lockdown_form -> addElement('submit', 'submit_unlock', _UNLOCK, 'class = "flatButton"');

    //Check here, whether the system is already locked, and present unlock button
    if ($lockdown_form -> isSubmitted() && $lockdown_form -> validate()) { //If the form is submitted and validated
        $values = $lockdown_form -> exportValues();
        if ($GLOBALS['configuration']['lock_down'] && isset($values['submit_unlock'])) {
            EfrontSystem :: unlockSystem();
        } else {
            EfrontSystem :: lockSystem($values['lock_message'], $values['logout_users']);
        }
        eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=lock_down");
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty); //Create a smarty renderer
    $lockdown_form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created

    $smarty -> assign('T_LOCKDOWN_FORM', $renderer -> toArray()); //Assign the form to the template

    //User check
    $users = eF_getTableDataFlat("users", "login");
    //$users_dir = eF_getDirContents(G_ROOTPATH.'upload/', '', false, false);
    $users_dir = scandir(G_ROOTPATH.'upload/');
    foreach ($users_dir as $key => $value) {
        if (!is_dir(G_ROOTPATH.'upload/'.$value) || in_array($value, array('.', '..', '.svn'))) {
            unset($users_dir[$key]);
        }
    }
    $orphan_user_folders = array_diff($users_dir, $users['login']);
    $orphan_users = array_diff($users['login'], $users_dir);

    $orphanUserStr = implode(", ", $orphan_user);
    $smarty -> assign("T_ORPHAN_USERS", mb_strlen($orphanUserStr) > 200 ? mb_substr($orphanUserStr, 0, 200).'...' : $orphanUserStr);
    $orphanUserFoldersStr = implode(", ", $orphan_user_folders);
    $smarty -> assign("T_ORPHAN_USER_FOLDERS", mb_strlen($orphanUserFoldersStr) > 200 ? mb_substr($orphanUserFoldersStr, 0, 200).'...' : $orphanUserFoldersStr);

    //Lessons check
    $lessons = eF_getTableDataFlat("lessons", "id, name");
    $lessons = array_combine($lessons['id'], $lessons['name']);
    //$lessons_dir = eF_getDirContents(G_ROOTPATH.'www/content/lessons/', '', false, false);
    $lessons_dir = scandir(G_LESSONSPATH);
    foreach ($lessons_dir as $key => $dir) { //Remove non-integer lessons from list (such as scorm_uploaded_files);
        if (!preg_match("/^\d+$/", $dir)) {
            unset($lessons_dir[$key]);
        }
    }
    $orphan_lesson_folders = array_diff($lessons_dir, array_keys($lessons));
    $orphan_lessons = array_diff(array_keys($lessons), $lessons_dir);

    $orphanLessonStr = implode(", ", $orphan_lessons);
    $smarty -> assign("T_ORPHAN_LESSONS", mb_strlen($orphanLessonStr) > 200 ? mb_substr($orphanLessonStr, 0, 200).'...' : $orphanLessonStr);
    $orphanLessonFoldersStr = implode(", ", $orphan_lesson_folders);
    $smarty -> assign("T_ORPHAN_LESSON_FOLDERS", mb_strlen($orphanLessonFoldersStr) > 200 ? mb_substr($orphanLessonFoldersStr, 0, 200).'...' : $orphanLessonFoldersStr);

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
            try {
             $lesson = new EfrontLesson($lesson_id);
             $lesson -> delete();
            } catch (Exception $e) {
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
    if (isset($_GET['create']) && $_GET['create'] == 'user_folders') {
        foreach ($orphan_users as $login) {
            if (!mkdir(G_ROOTPATH.'upload/'.$login, 0755) ||
            !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments', 0755) ||
            !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments/Drafts', 0755) ||
            !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments/Sent', 0755) ||
            !mkdir(G_ROOTPATH.'upload/'.$login.'/message_attachments/Incoming', 0755)) {
                $errors[] = $login;
            }
        }
        if (!isset($errors)) {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_SUCCESFULLYCREATEDUSERFOLDERS).'&message_type=success');
        } else {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup&message=".urlencode(_THEFOLLOWINGUSERFOLDERSCOULDNOTBECREATED).': '.implode(", ", $errors).'&message_type=failure');
        }
    }
    if (isset($_GET['create']) && $_GET['create'] == 'lesson_folders') {
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

    $logSize = eF_countTableData("logs");
    $smarty -> assign("T_LOG_SIZE", $logSize[0]['count']);
    $lastLogEntry = eF_getTableData("logs", "timestamp", "", "timestamp", false, 1);
    $smarty -> assign("T_LAST_LOG_ENTRY", $lastLogEntry[0]['timestamp']);
    $cleanupForm = new HTML_QuickForm("cleanup_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup", "", null, true);
 $cleanupForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
 $cleanupForm -> addElement("text", "logs_size", null, 'class = "inputText" style = "width:60px"');
    $cleanupForm -> addElement("submit", "submit", _SUBMIT, 'class = "flatButton"');
    if ($cleanupForm -> isSubmitted() && $cleanupForm -> validate()) {
     $timestamp = mktime(0, 0, 0, $_POST['purge_Month'], $_POST['purge_Day'], $_POST['purge_Year']);
     if (eF_checkParameter($timestamp, 'int')) {
      eF_deleteTableData("logs", "timestamp < $timestamp");
     }
     eF_redirect(basename($_SERVER['PHP_SELF']."?ctg=maintenance&tab=cleanup&message=".urlencode(_SUCCESSFULLYPURGEDLOGS)."&message_type=success"));
    }
    $renderer = prepareFormRenderer($cleanupForm);
    $smarty -> assign("T_CLEANUP_FORM", $renderer -> toArray());

    $notificationsSize = eF_countTableData("notifications");
    $smarty -> assign("T_NOTIFICATIONS_SIZE", $notificationsSize[0]['count']);
    $lastNotificationEntry = eF_getTableData("notifications", "timestamp", "", "timestamp", false, 1);
    $smarty -> assign("T_LAST_NOTIFICATIONS_ENTRY", $lastNotificationEntry[0]['timestamp']);
    $form = new HTML_QuickForm("cleanup_notifications_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=maintenance&tab=cleanup", "", null, true);
 $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
 $form -> addElement("text", "notifications_size", null, 'class = "inputText" style = "width:60px"');
    $form -> addElement("submit", "submit", _SUBMIT, 'class = "flatButton"');
    if ($form -> isSubmitted() && $form -> validate()) {
     $timestamp = mktime(0, 0, 0, $_POST['purge_Month'], $_POST['purge_Day'], $_POST['purge_Year']);
     if (eF_checkParameter($timestamp, 'int')) {
      eF_deleteTableData("notifications", "timestamp < $timestamp");
     }
     eF_redirect(basename($_SERVER['PHP_SELF']."?ctg=maintenance&tab=cleanup&message=".urlencode(_OPERATIONCOMPLETEDSUCCESSFULLY)."&message_type=success"));
    }
    $renderer = prepareFormRenderer($form);
    $smarty -> assign("T_CLEANUP_NOTIFICATIONS_FORM", $renderer -> toArray());


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
            } else if ($_GET['cache'] == 'query') {
             eF_executeNew("reset query cache");
            }
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    }

 if (isset($_GET['autologin'])) {
  $users = eF_getTableData("users", "login,name,surname,active,autologin,timestamp");
  foreach ($users as $key => $value) {
   $usersArray[$value['login']] = $value;
  }
//pr($usersArray);		
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

                $smarty -> assign("T_ALL_USERS", $users);
                $smarty -> display('administrator.tpl');
                exit;
        }
  if (isset($_GET['postAjaxRequest'])) {
            try {
                if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
     $user = EfrontUserFactory :: factory($_GET['login']);
     if ($user -> user['autologin'] == "" ) {
      $convert = $_GET['login']."_".$usersArray[$_GET['login']]['timestamp'];
      $converted = md5($convert.G_MD5KEY);
      $user -> user['autologin'] = $converted;
     } else {
      $user -> user['autologin'] = "";
     }
     $user -> persist();
     echo $converted;
                } else if (isset($_GET['addAll'])) {
     isset($_GET['filter']) ? $usersArray = eF_filterData($usersArray, $_GET['filter']) : null;
     foreach ($usersArray as $key => $value) {
      if ($value['autologin'] == "") {
       $autologin = md5($key."_".$value['timestamp'].G_MD5KEY);
       eF_updateTableData("users", array('autologin' => $autologin), "login='".$key."'");
      }
     }
                } else if (isset($_GET['removeAll'])) {
     if (isset($_GET['filter'])) {
      $usersArray = eF_filterData($usersArray, $_GET['filter']);
      $queryString = "'".implode("','", array_keys($usersArray))."'";
      eF_updateTableData("users", array('autologin' => ""),"login IN (".$queryString.")");
     } else {
      eF_updateTableData("users", array('autologin' => ""),"login !=''");
     }
                }
                exit;
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }

 }

}


?>
