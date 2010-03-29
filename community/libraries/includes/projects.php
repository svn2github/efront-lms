<?php
/**

 *

 */
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
$loadScripts[] = 'includes/projects';

//Create shorthands for user type, to avoid long variable names
$_student_ = $_professor_ = $_admin_ = 0;
if ($_SESSION['s_lesson_user_type'] == 'student') {
    $_student_ = 1;
} else if ($_SESSION['s_lesson_user_type'] == 'professor') {
    $_professor_ = 1;
}

//Create shorthands for user access rights, to avoid long variable names
$_change_ = $_hidden_ = 0;
if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
    $_change_ = 1;
} elseif (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden') {
    $_hidden_ = 1;
}


if ($GLOBALS['configuration']['disable_projects'] == 1) {
    eF_redirect("".basename($_SERVER['PHP_SELF']));
}

if (!$currentLesson) {
    eF_redirect("".basename($_SERVER['PHP_SELF']));
}
if ($_hidden_) {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}

try {
    $_professor_ ? $projects = $currentLesson -> getProjects(true) : $projects = $currentLesson -> getProjects(true, $currentUser -> user['login']);
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}

if (isset($_GET['delete_project']) && in_array($_GET['delete_project'], array_keys($projects)) && $_professor_ && $_change_) {
    try {
        $currentProject = $projects[$_GET['delete_project']];
        $currentProject -> delete();
    } catch (Exception $e) {
        $message = _PROJECTCOULDNOTBEDELETED.': '.$e -> getMessage().' ('.$e -> getCode().')';
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} else if (isset($_GET['compress_data']) && in_array($_GET['compress_data'], array_keys($projects)) && $_professor_) { //download project data
    try {
        $currentProject = $projects[$_GET['compress_data']];
        $projectFiles = $currentProject -> getFiles();

        if (!is_dir($currentUser -> user['directory'].'/projects/')) {
            mkdir($currentUser -> user['directory'].'/projects/', 0755);
        }
        $projectDir = $currentUser -> user['directory'].'/projects/'.$currentProject -> project['id'];
        if (!is_dir($projectDir)) {
            mkdir($projectDir, 0755);
        }
        $projectDirectory = new EfrontDirectory($projectDir);
        foreach ($projectFiles as $file) {
            try {
                $projectFile = new EfrontFile($file['id']);
                $newFileName = EfrontFile :: encode($file['users_LOGIN'].'_'.date("d.m.Y", $file['upload_timestamp']).'_'.$projectFile['name']);
                $projectFile -> copy($projectDir.'/'.$newFileName);
            } catch (EfrontFileException $e) { //Don't halt for a single file
                $message .= $e -> getMessage().' ('.$e -> getCode().')';
            }
        }
        $zipFileName = $currentUser -> user['directory'].'/projects/'.EfrontFile :: encode($currentProject -> project['title']).'.zip';
        $zipFile = $projectDirectory -> compress($zipFileName, false, true);
        $projectDirectory -> delete();
        eF_redirect("view_file.php?file=".urlencode($zipFile['path'])."&action=download");
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = _FILESCOULDNOTBEDOWNLOADED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
} else if ((isset($_GET['add_project']) || (isset($_GET['edit_project']) && in_array($_GET['edit_project'], array_keys($projects)))) && $_professor_) {
    //ajax request for inserting file in editor

    //This page has a file manager, so bring it on with the correct options
    $basedir = $currentLesson -> getDirectory();
    //Default options for the file manager
    if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
        $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
    } else {
        $options = array('delete' => false,
                'edit' => false,
                'share' => false,
                'upload' => false,
                'create_folder' => false,
                'zip' => false,
                'lessons_ID' => $currentLesson -> lesson['id'],
                'metadata' => 0);
    }
    //Default url for the file manager
    $url = basename($_SERVER['PHP_SELF']).'?ctg=content&'.(isset($_GET['edit']) ? 'edit='.$_GET['edit'] : 'add=1');
    $extraFileTools = array(array('image' => 'images/16x16/arrow_right.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
    /**The file manager*/
    include "file_manager.php";

    //This page also needs an editor and ASCIIMathML
    $load_editor = true;
    if ($configuration['math_content'] && $configuration['math_images']) {
        $loadScripts[] = 'ASCIIMath2Tex';
    } elseif ($configuration['math_content']) {
        $loadScripts[] = 'ASCIIMathML';
    }

    $form = new HTML_QuickForm("create_project_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=projects".(isset($_GET['add_project']) ? '&add_project=1' : '&edit_project='.$_GET['edit_project']), "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');

    $form -> addElement('text', 'title', _PROJECTTITLE, 'class = "inputText"');
    $form -> addRule('title', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('title', _INVALIDFIELDDATA, 'checkParameter', 'text');

    $form -> addElement('checkbox', 'auto_assign', _AUTOASSIGNTONEWUSERS, null, 'class = "inputCheckBox"');
    $form -> addElement('textarea', 'data', _PROJECTDATA, 'id="editor_project_data" class = "inputProjectTextarea mceEditor" style = "width:100%;height:30em;"');

    if (isset($_GET['edit_project'])) {
        $currentProject = $projects[$_GET['edit_project']];
        $smarty -> assign("T_CURRENT_PROJECT", $currentProject);
        $form -> setDefaults(array('title' => $currentProject -> project['title'],
                                   'auto_assign' => $currentProject -> project['auto_assign'],
                                   'data' => $currentProject -> project['data']));
        $smarty -> assign('T_DEADLINE_TIMESTAMP', $currentProject -> project['deadline']);
    } else {
        $smarty -> assign('T_DEADLINE_TIMESTAMP', mktime(0, 0, 0, date("m") + 1 ,date("d"), date("Y")));
    }

    if (!$_change_) {
        $form -> freeze();
    } else {
        $form -> addElement('submit', 'submit_add_project', _SUBMIT, 'class=flatButton');

        if ($form -> isSubmitted() && $form -> validate()) {
            $deadline = mktime($_POST['deadline_Hour'], $_POST['deadline_Minute'], 0, $_POST['deadline_Month'], $_POST['deadline_Day'], $_POST['deadline_Year']);
            if ($deadline > time()) {
                $values = $form -> exportValues();
                try {
                    if (isset($_GET['add_project'])) {
                        $fields = array('title' => $values['title'],
          'data' => $values['data'],
          'deadline' => $deadline,
          'creator_LOGIN' => $currentUser -> user['login'],
          'lessons_ID' => $currentLesson -> lesson['id'],
          'auto_assign' => $values['auto_assign'] ? 1 : 0);

                        $newProject = EfrontProject :: createProject($fields);

                        EfrontEvent::triggerEvent(array("type" => EfrontEvent::PROJECT_EXPIRY, "timestamp" => $deadline, "lessons_ID" => $currentLesson -> lesson['id'], "lessons_name" => $currentLesson -> lesson['name'], "entity_ID" => $newProject -> project['id'], "entity_name" => $newProject -> project['title']));

                        $message = _PROJECTCREATEDSUCCESSFULLY;
                        $message_type = 'success';
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=projects&edit_project=".$newProject -> project['id']."&tab=project_users&message=$message&message_type=$message_type");
                    } else {
                        $currentProject -> project['title'] = $values['title'];
                        $currentProject -> project['data'] = $values['data'];
                        $currentProject -> project['deadline'] = $deadline;
                        $currentProject -> project['auto_assign'] = $values['auto_assign'] ? 1 : 0;
                        $currentProject -> persist();

                        EfrontEvent::triggerEvent(array("type" => EfrontEvent::PROJECT_EXPIRY, "timestamp" => $deadline, "lessons_ID" => $currentLesson -> lesson['id'], "lessons_name" => $currentLesson -> lesson['name'], "entity_ID" => $_GET['edit_project'], "entity_name" => $values['title']));
                        $message = _PROJECTUPDATEDSUCCESSFULLY;
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=projects&message=".urlencode($message)."&message_type=success");
                    }
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            } else {
                $message = _DEADLINEDATEMUSTBEINFUTURE;
                $message_type = 'failure';
            }
        }
    }
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_ADD_PROJECT_FORM', $renderer -> toArray());

    //Build the project users list
    if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
        $users = $currentLesson -> getUsers('student');
        $projectUsers = $currentProject -> getUsers();
        foreach ($users as $key => $user) {
            $users[$key]['checked'] = 0;
            if (in_array($key, array_keys($projectUsers))) { //Set the checked status, depending on whether the user has this project
                $users[$key]['checked'] = 1;
            } else if (!$user['active']) {
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

        $smarty -> assign("T_CURRENT_USER", $currentUser);
        $smarty -> assign("T_ALL_USERS", $users);
        $smarty -> display('professor.tpl');
        exit;
    }
    //ajax request to register users with project
    if (isset($_GET['postAjaxRequest'])) {
        try {
            $users = $currentLesson -> getUsers('student');
            $projectUsers = $currentProject -> getUsers();
            if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
                if (in_array($_GET['login'], array_keys($projectUsers))) { //The user has the project, so remove him
                    $currentProject -> removeUsers($_GET['login']);
                } elseif (in_array($_GET['login'], array_keys($users))) { //The user doesn't have the project, so add him
                    $currentProject -> addUsers($_GET['login']);
                }
            } else if (isset($_GET['addAll'])) {
                isset($_GET['filter']) ? $users = eF_filterData($users, $_GET['filter']) : null;
                $currentProject -> addUsers(array_keys($users));
            } else if (isset($_GET['removeAll'])) {
                isset($_GET['filter']) ? $projectUsers = eF_filterData($projectUsers, $_GET['filter']) : null;
                $currentProject -> removeUsers(array_keys($projectUsers));
            }
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    }
    //configure the file manager
    $loadScripts[] = 'scriptaculous/effects';
    try {

        //This page has a file manager, so bring it on with the correct options
        $basedir = $currentLesson -> getDirectory();
        //Default options for the file manager
        if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
            $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
        } else {
            $options = array('delete' => false,
                 'edit' => false,
                 'share' => false,
                 'upload' => false,
                 'create_folder' => false,
                 'zip' => false,
                 'lessons_ID' => $currentLesson -> lesson['id'],
                 'metadata' => 0);
        }
        //Default url for the file manager
        $url = basename($_SERVER['PHP_SELF']).'?ctg=projects&'.(isset($_GET['edit_project']) ? 'edit_project='.$_GET['edit_project'] : 'add_project=1');
        $extraFileTools = array(array('image' => 'images/16x16/arrow_right.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
        /**The file manager*/
        include "file_manager.php";
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
} else if (isset($_GET['project_results']) && in_array($_GET['project_results'], array_keys($projects)) && $_professor_) {
    $currentProject = $projects[$_GET['project_results']];

    $smarty -> assign("T_CURRENT_PROJECT", $currentProject);
    if (isset($_GET['ajax']) && $_GET['ajax'] == 'resultsTable') {
        $users = $currentProject -> getUsers();
        //$files          = eF_getTableDataFlat("files", "id,original_name");
        sizeof($files) > 0 ? $files = array_combine($files['id'], $files['original_name']) : $files = array();
        foreach ($users as $key => $user) {
            if ($user['filename']) {
                try {
                    $projectFile = new EfrontFile($user['filename']);
                    $users[$key]['file'] = $projectFile['name'];
                    !$user['upload_timestamp'] ? $users[$key]['upload_timestamp'] = 'empty' : null; //Setting 'empty' here, makes possible to sort correctly onload (otherwise, empty timestamps where always put above more recent timestamps)
                } catch (Exception $e) {
                    $users[$key]['filename'] = '';
                    $users[$key]['upload_timestamp'] = '';
                }
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

        $smarty -> assign("T_CURRENT_USER", $currentUser);
        $smarty -> assign("T_ALL_USERS", $users);
        $smarty -> display('professor.tpl');
        exit;
    }
    //ajax request to register project grades and comments
    if (isset($_GET['postAjaxRequest'])) {
        try {
            $projectUsers = $currentProject -> getUsers();
            if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login') && in_array($_GET['login'], array_keys($projectUsers))) {
                $currentProject -> grade($_GET['login'], $_GET['grade'], $_GET['comments']);
            }
        } catch (Exception $e) {
            $message = _PROJECTCOULDNOTBEDELETED.': '.$e -> getMessage().' ('.$e -> getCode().')';
            header("HTTP/1.0 500 ");
            echo rawurlencode($e -> getMessage()).' ('.$e -> getCode().')';
        }
        exit;
    }
} else if (isset($_GET['view_project']) && in_array($_GET['view_project'], array_keys($projects))) {
    try {
        $currentProject = $projects[$_GET['view_project']];

        $projectUser = $currentProject -> getUsers();
        $projectUser = $projectUser[$currentUser -> user['login']];
        $currentProject -> project['deadline'] < time() ? $currentProject -> expired = true : $currentProject -> expired = false;

        if ($configuration['math_content'] && $configuration['math_images']) {
            $loadScripts[] = 'ASCIIMath2Tex';
        } elseif ($configuration['math_content']) {
            $loadScripts[] = 'ASCIIMathML';
        }

        if ($projectUser['filename']) {
            try {
                $projectFile = new EfrontFile($projectUser['filename']);
                $smarty -> assign("T_PROJECT_FILE", $projectFile);
                if (isset($_GET['delete_file']) && !$currentProject -> expired) {
                    $projectFile -> delete();
                    eF_updateTableData("users_to_projects", array('filename' => '', 'upload_timestamp' => ''), "users_LOGIN='".$currentUser -> user['login']."' AND projects_ID=".$_GET['view_project']);
                    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=projects&view_project=".$_GET['view_project']."&message=".urlencode(_FILEDELETEDSUCCESSFULLY)."&message_type=success");
                }
            } catch (EfrontFileException $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        }

        $form = new HTML_QuickForm("upload_project_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=projects&view_project='.$_GET['view_project'], "", null, true);
        if (!$projectFile) {
            $file = $form -> addElement('file', 'filename', _FILE);

            $maxFileSize = FileSystemTree :: getUploadMaxSize();
            $form -> addRule('filename', _THEFIELD.' "'._FILE.'" '._ISMANDATORY, 'required', null, 'client');
            $form -> setMaxFileSize($maxFileSize * 1024);
            $form -> addElement('submit', 'submit_upload_project', _SENDPROJECT, 'class = "flatButton"');
        }

        $smarty -> assign("T_MAX_FILE_SIZE", $maxFileSize);
        if ($form -> isSubmitted() && $form -> validate() && !$currentProject -> expired) {
            try {
                $projectDirectory = G_UPLOADPATH.$currentUser -> user['login'].'/projects';
                if (!is_dir($projectDirectory)) {
                    EfrontDirectory :: createDirectory($projectDirectory);
                }
                $filesystem = new FileSystemTree($projectDirectory);
                $uploadedFile = $filesystem -> uploadFile('filename', $projectDirectory);
                $uploadedFile -> rename($uploadedFile['directory'].'/project_'.$currentProject -> project['id'].'.'.$uploadedFile['extension']);
                $fields_update = array("filename" => $uploadedFile['id'],
                                           "upload_timestamp" => time());
                eF_updateTableData("users_to_projects", $fields_update, "users_LOGIN='".$currentUser -> user['login']."' AND projects_ID=".$_GET['view_project']);


                EfrontEvent::triggerEvent(array("type" => EfrontEvent::PROJECT_SUBMISSION,
                   "users_LOGIN" => $currentUser -> user['login'],
                   "lessons_ID" => $currentLesson -> lesson['id'],
                   "lessons_name" => $currentLesson -> lesson['name'],
                   "entity_ID" => $currentProject -> project['id'],
                   "entity_name" => $currentProject -> project['title']));

                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=projects&view_project=".$_GET['view_project']."&message=".urlencode(_FILEUPLOADED)."&message_type=success");
            } catch (EfrontFileException $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        } elseif ($currentProject -> expired) {
            $message = _PROJECTEXPIRED;
            $message_type = 'failure';
        }

        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);

        $smarty -> assign('T_UPLOAD_PROJECT_FORM', $renderer -> toArray());
        $smarty -> assign("T_CURRENT_PROJECT", $currentProject);
        $smarty -> assign("T_PROJECT_USER_INFO", $projectUser);

    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
} else {
    $currentProjects = array();
    $passedProjects = array();

    foreach ($projects as $project) {
        //getUsers() initializes user information for the specified projects
        $projectUsers = $project -> getUsers();
  time() < $project -> project['deadline'] ? $currentProjects[] = $project : $passedProjects[] = $project;
    }
    unset($project);

    $smarty -> assign("T_CURRENT_PROJECTS", $currentProjects);
    $smarty -> assign("T_ACTIVE_COUNT", sizeof($currentProjects));

    $smarty -> assign("T_EXPIRED_PROJECTS", $passedProjects);
    $smarty -> assign("T_INACTIVE_COUNT", sizeof($passedProjects));
}

?>
