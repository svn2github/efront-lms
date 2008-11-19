<?php
/**
* Manage personal messages folders
*
* This page provides personal messages folders functionality
*
* @package eFront
* @version 0.1
*/
session_cache_limiter('none');
session_start();

$path = "../../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

/*Check the user type. If the user is not valid, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
        $smarty -> assign("T_CURRENT_USER", $currentUser);

        if (MODULE_HCD_INTERFACE) {
            $currentUser -> aspects['hcd'] = EfrontEmployeeFactory :: factory($_SESSION['s_login']);
            $employee = $currentUser -> aspects['hcd'];
        }
        
        if ($_SESSION['s_lessons_ID']) {
            $userLessons = $currentUser -> getLessons();
            $currentUser -> applyRoleOptions($userLessons[$_SESSION['s_lessons_ID']]);                //Initialize user's role options for this lesson
            $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
        } else {
            $currentUser -> applyRoleOptions();                //Initialize user's role options for this lesson                   
        }

        if ($currentUser -> coreAccess['personal_messages'] == 'hidden') {
            header("location:".G_SERVERNAME.$_SESSION['s_type'].".php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        header("location:index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    header("location:index.php?message=".urlencode(_YOUCANNOTACCESSTHISPAGE)."&message_type=failure");
    exit;
}


$folder = '';
if (isset($_POST['submit_create'])) {
    if (!eF_checkParameter($_POST['folder_name'], 'filename')) {
        $message = _INVALIDFOLDERNAME;
        $message_type = 'failure';
    }

    $max_folders    = eF_getTableData("f_configuration", "value", "name='maximum_folders'");
    $num_of_folders = eF_getTableData("f_folders", "count(*)", "users_LOGIN='".$_SESSION['s_login']."'");
    $max_folders[0]['value'] = ($max_folders[0]['value'])?$max_folders[0]['value']:G_MAX_MESSAGES_FOLDERS;
    if ($max_folders[0]['value'] == $num_of_folders[0]['count(*)']) {
        $message      = _SORRYYOUCANNOTCREATEANYMOREFOLDERS;
        $message_type = 'failure';
    }

    $folder = eF_getTableData("f_folders", "name", "name='".$_POST['folder_name']."'");
    if (sizeof($folder) > 0) {
        $message      = _FOLDERWITHSAMENAMEALREADYEXISTS;
        $message_type = 'failure';
        //eF_printMessage(_FOLDERWITHSAMENAMEALREADYEXISTS);
        $folder = $_POST['folder_name'];
    } else {
        $fields_insert = array("name" => $_POST['folder_name'],
                               "users_LOGIN" => $_SESSION['s_login'],
                               "parent_id" => 0);
        eF_insertTableData("f_folders", $fields_insert);
        @mkdir(G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/'.$_POST['folder_name']);
        $message      = _FOLDERCREATED;
        $message_type = 'success';
    }
} elseif (isset($_POST['submit_modify'])) {
    if (!eF_checkParameter($_POST['folder_name'], 'filename')) {
        $message      = _INVALIDFOLDERNAME;
        $message_type = 'failure';
    }

    if (!isset($_POST['folder_id']) || !eF_checkParameter($_POST['folder_id'], 'id')) {
        $message      = _INVALIDID;
        $message_type = 'failure';
        //eF_printMessage(_INVALIDID);
        //eF_printCloseButton();
        //exit;
    }

    $folder = eF_getTableData("f_folders", "name", "name='".$_POST['folder_name']."'");
    if (sizeof($folder) > 0) {
        $message      = _FOLDERWITHSAMENAMEALREADYEXISTS;
        $message_type = 'failure';
        $folder = $_POST['folder_name'];
    } else {
        $folder_name = eF_getTableData("f_folders", "name", "id=".$_POST['folder_id']);
        eF_updateTableData("f_folders", array("name" => $_POST['folder_name']), "id=".$_POST['folder_id']);
        @rename(G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/'.$folder_name[0]['name'], G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/'.$_POST['folder_name']);
        $message      = _FOLDERUPDATED;
        $message_type = 'success';
    }
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'add':
            break;
        case 'edit':
            if (!isset($_GET['id']) || !eF_checkParameter($_GET['id'], 'id')) {
                $message      = _INVALIDID;
                $message_type = 'failure';
            }
            $folder = eF_getTableData("f_folders", "name", "users_LOGIN='".$_SESSION['s_login']."' and id=".$_GET['id']);
            if (sizeof($folder) == 0) {
                $message      = _THISFOLDERDOESNOTEXIT;
                $message_type = 'failure';
            }

            $smarty -> assign("T_FOLDER", $folder[0]['name']);
            break;
        case 'delete':
            if (!isset($_GET['id']) || !eF_checkParameter($_GET['id'], 'id')) {
                $message      = _INVALIDID;
                $message_type = 'failure';
            }
            $folder = eF_getTableData("f_folders", "name", "users_LOGIN='".$_SESSION['s_login']."' and id=".$_GET['id']);
            if (sizeof($folder) == 0) {
                $message      = _THISFOLDERDOESNOTEXIT;
                $message_type = 'failure';
            }
            eF_deleteTableData("f_folders", "id=".$_GET['id']);

            $p_messages = eF_getTableData("f_personal_messages", "id", "f_folders_ID=".$_GET['id']);
            foreach ($p_messages as $p_message) {
                eF_deletePersonalMessage($p_message['id']);
            }

            $success = eF_deleteFolder(G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/'.$folder[0]['name']);
            if ($success) {
                $message      = _FOLDERDELETED;
                $message_type = 'success';
            } else {
                $message      = _SOMEPROBLEMOCCURED;
                $message_type = 'failure';
            }

            break;
        case 'statistics':
            $folders = eF_getTableData("f_folders", "*", "users_LOGIN='".$_SESSION['s_login']."'");

            foreach ($folders as $key => $folder) {
                $messages_num  = eF_getTableData("f_personal_messages", "count(*)", "users_LOGIN='".$_SESSION['s_login']."' and f_folders_ID=".$folder['id']);
                $stats         = eF_diveIntoDir(G_UPLOADPATH.$_SESSION['s_login']."/message_attachments/".$folder['name']);
                $folders[$key]['msgs']         = $messages_num[0]['count(*)'];
                $folders[$key]['files_number'] = $stats[0];
                $folders[$key]['files_size']   = $stats[2] / 1000;
            }
            $smarty -> assign ("T_FOLDERS", $folders);

            break;
        default:
            break;
    }
} else {
    $message      = _YOUMUSTSPECIFYANACTION;
    $message_type = 'failure';
}

$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> display("forum/manage_folders.tpl");
?>