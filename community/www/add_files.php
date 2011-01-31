<?php
/**

* Add files

*

* This file presents the page used to insert and handle files and folders

* @package eFront

* @version 1.1

* Changes from 1.0 to 1.1 (16/11/2005):

* Changes from 2.5 to 3 (06/07/2007): administrator also upload files in his folder (/content/admin)  makriria

* - Fixed very serious bug, concerning the ability to access files anywhere on the server

* - Fixed minor problems

*/
//General initializations and parameter
session_cache_limiter('none');
session_start();
$path = "../libraries/";
/** The configuration file.*/
include_once $path."configuration.php";
//error_reporting(E_ALL);
//echo "<pre>";print_r($_POST);print_r($_GET);
eF_printHeader();
try {
 $currentUser = EfrontUser :: checkUserAccess();
} catch (Exception $e) {
 echo "<script>parent.location = 'index.php?logout=true&message=".urlencode($e -> getMessage().' ('.$e -> getCode().')')."&message_type=failure'</script>"; //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
 exit;
}
if (!isset($_SESSION['s_lessons_ID']) && $_SESSION['s_type'] == "professor") { //Check if a lesson is selected if user is a professor
    eF_printMessage(_LESSONNOTSET);
    exit;
}
$allowed_extensions = eF_getTableData("configuration", "value", "name='allowed_extensions'"); //Get allowed and disallowed extensions, for the files that can be uploaded
$disallowed_extensions = eF_getTableData("configuration", "value", "name='disallowed_extensions'");
if (sizeof($allowed_extensions) == 0 || $allowed_extensions[0]['value'] == '') {
    unset ($allowed_extensions);
}
if (sizeof($disallowed_extensions) == 0 || $disallowed_extensions[0]['value'] == '') {
    unset ($disallowed_extensions);
}

if (isset($_GET['filename']) && !eF_checkParameter($_GET['filename'], 'directory')) { //filename is a file or folder that was asked to be deleted. Check if it is properly formatted and if not, alert the administrator
    eF_printMessage(_YOUCANNOTDELETETHISFILE);

    $alert_message = _TRIEDTODELETE.': '.$_GET['filename'];
    //eF_alertAdmin($_SESSION['s_login'], time(), $alert_message);

    exit;
}

if (!isset($_GET['dir']) && $_SESSION['s_type'] == "professor") {
    $dir = $_SESSION['s_lessons_ID']."/";
}elseif(!isset($_GET['dir']) && $_SESSION['s_type'] == "administrator"){
    $dir = '/';
} else {
    $dir = urldecode($_GET['dir']);
    if (!eF_checkParameter($_GET['dir'], 'directory')) {
        eF_printMessage(_YOUCANNOTACCESSTHISFOLDER);

        $alert_message = _TRIEDTOACCESS.': '.$_GET['dir'];
        //eF_alertAdmin($_SESSION['s_login'], time(), $alert_message, 4);
        exit;
    }
}


if (isset($_GET['op']) && $_GET['op'] == "delete") { //Delete file
    if (unlink(G_LESSONSPATH.$_GET['filename'])) {
        $smarty -> assign("T_DELETE_MESSAGE", _SUCCESFULLYDELETEDFILEWINDOWCLOSE5SECONDS);
        $smarty -> assign("T_DELETE_MESSAGE_TYPE", 'success');
    } else {
        $smarty -> assign("T_DELETE_MESSAGE", _THEREWASAPROBLEMDELETETINGFILEWINDOWCLOSE5SECONDS);
        $smarty -> assign("T_DELETE_MESSAGE_TYPE", 'failure');
    }
} elseif (isset($_GET['op']) && $_GET['op'] == "deletefolder") {
    if (eF_deleteFolder(G_LESSONSPATH.$_GET['filename'].'/')) {
        $smarty -> assign("T_DELETEFOLDER_MESSAGE", _SUCCESFULLYDELETEDFOLDERWINDOWCLOSE5SECONDS);
        $smarty -> assign("T_DELETEFOLDER_MESSAGE_TYPE", 'success');
    } else {
        $smarty -> assign("T_DELETEFOLDER_MESSAGE", _NOTSUCCESFULLYDELETEDFOLDERWINDOWCLOSE5SECONDS);
        $smarty -> assign("T_DELETEFOLDER_MESSAGE_TYPE", 'failure');
    }
} elseif (isset($_GET['op']) && $_GET['op'] == "createfolder") {
    if (isset($_POST['submit'])) {
        if (!eF_checkParameter($_POST['foldername'], 'filename')) {
            eF_printMessage(_INVALIDNAME);
            exit;
        }
        $foldername = $_POST['foldername'];
        $dir_to_create = urldecode($_POST['dir']).'/'.$foldername; //The folder that is going to be created

        $pos = mb_strpos(mb_strtolower(str_replace('\\', '/', realpath(G_LESSONSPATH.dirname($dir_to_create)))), mb_strtolower(G_LESSONSPATH.$_SESSION['s_lessons_ID']));
        if ($pos === false) {
            eF_printMessage(_YOUCANNOTCREATETHISFOLDER);

            $alert_message = _TRIEDTOCREATEFOLDER.': '.$dir_to_create;
            //eF_alertAdmin($_SESSION['s_login'], time(), $alert_message, 4);

            exit;
        }

        if (@mkdir(G_LESSONSPATH.$dir_to_create, 0755)) {
            $smarty -> assign("T_CREATEFOLDER_MESSAGE", _SUCCESFULLYCREATEDFOLDERWINDOWCLOSE5SECONDS);
            $smarty -> assign("T_CREATEFOLDER_MESSAGE_TYPE", 'success');
        } else {
            $smarty -> assign("T_CREATEFOLDER_MESSAGE", _COULDNOTCREATEFOLDERWINDOWCLOSE5SECONDS);
            $smarty -> assign("T_CREATEFOLDER_MESSAGE_TYPE", 'failure');
        }
    }

    $smarty -> assign("T_DIRECTORY", $dir);
} else {

    if (isset($_GET['files'])) {

        $image_files = explode(";", str_replace('\\\\', '\\', urldecode($_GET['files'])));
        for ($i = 0; $i < sizeof($image_files); $i++) {
            $copy_string[$i] = '<input type ="text" value="'.$image_files[$i].'" size="40"/> &raquo; ';
        }
        $message = _COPYNAMESFROMLEFTTORIGHTTOSAVEIMAGES;
    }

    isset($image_files) ? $size = sizeof($image_files) : $size = 10;

    if (isset($_POST['submit'])) {
        if (!eF_checkParameter($_POST['to_dir'], 'directory')) {
            eF_printMessage(_INVALIDNAME);
            exit;
        }
        if ($_SESSION['s_type'] == "professor"){
            $target_dir = G_LESSONSPATH.$_POST['to_dir'];
        }elseif ($_SESSION['s_type'] == "administrator"){
            $target_dir = G_ADMINPATH.$_POST['to_dir'];
        }
        list($ok, $upload_messages, $upload_messages_type, $filename) = eF_handleUploads('fileupload', $target_dir);


        $smarty -> assign("T_UPLOAD_MESSAGES", $upload_messages);
        $smarty -> assign("T_UPLOAD_MESSAGES_TYPE", $upload_messages_type);
    }

    $smarty -> assign("T_MESSAGE", $message);
    $smarty -> assign("T_DIRECTORY", $dir);
    $smarty -> assign("T_FILES", $files);
    $smarty -> assign("T_LESSONS_ID", $_SESSION['s_lessons_ID']);

    $smarty -> assign("T_COPY_STRING", $copy_string);
    $smarty -> assign("T_SIZE", $size);

    if (isset($allowed_extensions)) {
        $smarty -> assign("T_ALLOWED_EXTENSIONS", $allowed_extensions[0]['value']);
    }
    if (isset($disallowed_extensions)) {
        $smarty -> assign("T_DISALLOWED_EXTENSIONS", $disallowed_extensions[0]['value']);
    }

    if (isset($content_ID) && $content_ID != "") {
        $smarty -> assign("T_CONTENT_ID", $content_ID);
    }
}

$smarty -> display("add_files.tpl");
?>
