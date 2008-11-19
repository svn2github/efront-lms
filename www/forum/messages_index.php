<?php
/**
* Personal messages root page
*
* This page provides messages functionality
*
* @package eFront
* @version 1.0
*/
$debug_TimeStart = microtime(true);     //Debugging timer - initialization

session_cache_limiter('none');
session_start();

$path = "../../libraries/";

/** Configuration file.*/
require_once $path."configuration.php";
$debug_InitTime = microtime(true) - $debug_TimeStart;       //Debugging timer - time spent on file inclusion

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
/**Added Session variable for search results*/
$_SESSION['referer'] = $_SERVER['REQUEST_URI'];

$ctg = "messages";

/*** Ajax Methods - Add/remove skills/jobs***/
if (isset($_GET['postAjaxRequest'])) {
    /** Post skill - Ajax skill **/
    if (isset($_GET['move_message_folder'])) {
        $user_messages = eF_getTableDataFlat("f_personal_messages", "id", "users_LOGIN='".$_SESSION['s_login']."'");
        if (in_array($_GET['message'], $user_messages['id'])) {
            eF_movePersonalMessage($_GET['message'], $_GET['move_message_folder']);
        }
    } elseif (isset($_GET['message_size_quota'])) {
        $total_files    = eF_diveIntoDir(G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/');
        echo ceil($total_files[2] / 1000);
        exit;
    } elseif (isset($_GET['delete_message'])) {
        eF_deletePersonalMessage($_GET['delete_message']);
        exit;
    }
}

if (sizeof($_POST) > 0) {
    if (isset($_POST['delete'])) {
        if (sizeof($_POST['check']) == 0) {
            $message = _YOUMUSTSELECTMESSAGESTODELETE;
        } else {
            $user_messages = eF_getTableDataFlat("f_personal_messages", "id", "users_LOGIN='".$_SESSION['s_login']."'");

            foreach ($_POST['check'] as $value) {
                if (in_array($value, $user_messages['id'])) {
                    $outcome = eF_deletePersonalMessage($value);
                    if ($outcome === true) {
                        $message      = _SUCCESSFULLYDELETEDMESSAGES;
                        $message_type = "success";
                    } else {
                        $message = $outcome;
                    }
                }
            }
        }
    } elseif (isset($_POST['mark_read']) || isset($_POST['mark_unread'])) {
        if (!isset($_POST['check']) || sizeof($_POST['check']) == 0) {
            $message = _YOUMUSTSELECTMESSAGESTOMARK;
        } else {
            $user_messages = eF_getTableDataFlat("f_personal_messages", "id", "users_LOGIN='".$_SESSION['s_login']."'");
            foreach ($_POST['check'] as $value) {
                if (in_array($value, $user_messages['id'])) {
                    isset($_POST['mark_read']) ? $viewed = 1 : $viewed = 0;
                    eF_updateTableData("f_personal_messages", array("viewed" => $viewed), "id=".$value);
                }
            }
        }
    } elseif (isset($_POST['mark_flag']) || isset($_POST['mark_unflag'])) {
        if (!isset($_POST['check']) || sizeof($_POST['check']) == 0) {
            $message = _YOUMUSTSELECTMESSAGESTOFLAG;
        } else {//$db -> debug = true;
            $user_messages = eF_getTableDataFlat("f_personal_messages", "id", "users_LOGIN='".$_SESSION['s_login']."'");
            foreach ($_POST['check'] as $value) {
                if (in_array($value, $user_messages['id'])) {
                    isset($_POST['mark_flag']) ? $priority = 1 : $priority = 0;
                    eF_updateTableData("f_personal_messages", array("priority" => $priority), "id=".$value);
                }
            }
        }
    } elseif (isset($_POST['move_messages_submit'])) {
        if (!isset($_POST['check']) || sizeof($_POST['check']) == 0) {
            $message =_YOUMUSTSELECTMESSAGESTOMOVE;
        } elseif (!eF_checkParameter($_POST['move_message'], 'id')) {
            $message = _INVALIDID;
        } else {
            $user_messages = eF_getTableDataFlat("f_personal_messages", "id", "users_LOGIN='".$_SESSION['s_login']."'");
            foreach ($_POST['check'] as $value) {
                if (in_array($value, $user_messages['id'])) {
                    //eF_updateTableData("f_personal_messages", array("f_folders_ID" => $_POST['move_message']), "id=".$value);
                    eF_movePersonalMessage($value, $_POST['move_message']);
                }
            }
        }
    }
}

//---------------------------------------Start of Folders-------------------------------------------

if (!is_dir(G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/')) {                                                 //Check if the messages folder for this user exists on the disk
    mkdir(G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/');
}




$in_folder = eF_getTableData("f_folders", "id", "users_LOGIN='".$_SESSION['s_login']."' and name='Incoming'");
if (sizeof($in_folder) == 0) {                                                                      //If the Incoming folder does not exist, for some reason, in the database, create it
    $in_folder[0]['id'] = eF_insertTableData("f_folders", array('name' => 'Incoming', 'users_LOGIN' => $_SESSION['s_login'], 'parent_id' => 0));
}

$sent_folder = eF_getTableData("f_folders", "id", "users_LOGIN='".$_SESSION['s_login']."' and name='Sent'");
if (sizeof($sent_folder) == 0) {                                                                    //If the Sent folder does not exist, for some reason, in the database, create it
    eF_insertTableData("f_folders", array('name' => 'Sent', 'users_LOGIN' => $_SESSION['s_login'], 'parent_id' => 0));
}

$draft_folder = eF_getTableData("f_folders", "id", "users_LOGIN='".$_SESSION['s_login']."' and name='Drafts'");
if (sizeof($draft_folder) == 0) {                                                                   //If the Drafts folder does not exist, for some reason, in the database, create it
    eF_insertTableData("f_folders", array('name' => 'Drafts', 'users_LOGIN' => $_SESSION['s_login'], 'parent_id' => 0));
}

isset($_GET['folder']) && eF_checkParameter($_GET['folder'], 'filename') ? $folder = $_GET['folder'] : $folder = $in_folder[0]['id'];

// The sorting according to id guarantess that the folders appear in the order "Incoming", "Sent", "Drafts" and then anything else
$p_folders  = eF_getTableData("f_folders", "*", "users_LOGIN='".$_SESSION['s_login']."'","id");


$folder_options = array(
    array('text' => _NEWFOLDER, 'image' => "16x16/folder_add.png", 'href' => "forum/manage_folders.php?action=add", 'onClick' => "eF_js_showDivPopup('"._CREATEFOLDER."', new Array('400px', '100px'))", 'target' => 'POPUP_FRAME')
);

if (!isset($_GET['ajax'])) {
    $smarty -> assign("T_FOLDERS_OPTIONS", $folder_options);
}

for ($i = 0; $i < sizeof($p_folders); $i++) {
    if (!is_dir(G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/'.$p_folders[$i]['name'])) {                      //Check whether the folders exist physically on the disk
        mkdir(G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/'.$p_folders[$i]['name']);
    }

}
//---------------------------------------End of Folders-------------------------------------------

//---------------------------------------Start of Volume-------------------------------------------

$res1 = eF_getTableData("f_configuration", "value", "name='quota_num_of_messages'");
$res2 = eF_getTableData("f_configuration", "value", "name='quota_kilobytes'");

$res1[0]['value'] = ($res1[0]['value'])? $res1[0]['value'] : G_QUOTA_NUM_OF_MESSAGES;
$res2[0]['value'] = ($res2[0]['value'])? $res2[0]['value'] : G_QUOTA_KB;

$smarty -> assign("T_QUOTA_NUM_OF_MESSAGES", $res1[0]['value']);
$smarty -> assign("T_QUOTA_KILOBYTES", $res2[0]['value']);

$total_messages = eF_getTableData("f_personal_messages", "count(*)", "users_LOGIN='".$_SESSION['s_login']."'");
$total_files    = eF_diveIntoDir(G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/');

$smarty -> assign("T_TOTAL_MESSAGES", $total_messages[0]['count(*)']);
$smarty -> assign("T_TOTAL_SIZE", ceil($total_files[2] / 1000));

$total_messages_percentage = round(100 * $total_messages[0]['count(*)'] / $res1[0]['value'], 2);
$total_files_percentage    = round(100 * ceil($total_files[2]/1000) / $res2[0]['value'], 2);

$smarty -> assign("T_TOTAL_MESSAGES_PERCENTAGE", $total_messages_percentage);
$smarty -> assign("T_TOTAL_FILES_PERCENTAGE", $total_files_percentage);

$volume_options = array(
array('text' => _VIEWFOLDERSTATISTICS, 'image' => "16x16/chart.png", 'href' => "forum/manage_folders.php?action=statistics", 'onClick' => "eF_js_showDivPopup('"._FOLDERSTATISTICS."', new Array('400px', '200px'))", 'target' => 'POPUP_FRAME'));
$smarty -> assign("T_VOLUME_OPTIONS", $volume_options);

//---------------------------------------End of Volume-------------------------------------------

//---------------------------------------Start of Messages-------------------------------------------
$viewing_message = false;

if (isset($_GET['flag']) && eF_checkParameter($_GET['flag'], 'id')) {
    eF_updateTableData("f_personal_messages", array('priority' => 1), "id=".$_GET['flag']);
} elseif (isset($_GET['unflag']) && eF_checkParameter($_GET['unflag'], 'id')) {
    eF_updateTableData("f_personal_messages", array('priority' => 0), "id=".$_GET['unflag']);
} elseif (isset($_GET['read']) && eF_checkParameter($_GET['read'], 'id')) {
    eF_updateTableData("f_personal_messages", array('viewed' => 1), "id=".$_GET['read']);
} elseif (isset($_GET['unread']) && eF_checkParameter($_GET['unread'], 'id')) {
    eF_updateTableData("f_personal_messages", array('viewed' => 0), "id=".$_GET['unread']);
}

isset($_GET['page']) && eF_checkParameter($_GET['page'], 'uint') ? $page = $_GET['page'] : $page = 1;

$p_messages_per_page = eF_getTableData("f_configuration", "value", "name='personal_messages_per_page'");
$p_messages_per_page[0]['value'] ? $p_messages_per_page = $p_messages_per_page[0]['value'] : $p_messages_per_page = 20;

// Create ajax enabled table for employees
$loadScripts = array_merge($loadScripts, array('scriptaculous/prototype'));
if (isset($_GET['ajax'])) {
    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = $p_messages_per_page;

    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
        $sort = $_GET['sort'];
        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
    } else {
        $sort = 'priority';
    }

    $p_messages   = eF_getTableData("f_personal_messages", "*", "users_LOGIN='".$_SESSION['s_login']."' and f_folders_ID=".$folder, "priority desc, viewed,timestamp desc");

    $p_messages = eF_multiSort($p_messages, $_GET['sort'], $order);
//pr($p_messages);
    if (isset($_GET['filter'])) {
        $p_messages = eF_filterData($p_messages , $_GET['filter']);
    }

    $smarty -> assign("T_MESSAGES_SIZE", sizeof($p_messages));

    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
        $p_messages = array_slice($p_messages, $offset, $limit);
    }

    // Keep only the first characters of the subject

    // Keep only the first characters of the recipient's list
    $subject_chars = 50;
    $recipient_chars = 30;
    foreach ($p_messages as $key => $p_message) {
        if (strlen($p_message['title']) > ($subject_chars - (($p_message['attachments'])? 4:0))) {
            $p_messages[$key]['title'] = substr($p_message['title'],0,$subject_chars - (($p_message['attachments'])? 4:0) - 3) . "...";
        }
        if (strlen($p_message['recipient']) > $recipient_chars) {
            $p_messages[$key]['recipient'] = substr($p_message['recipient'],0,$recipient_chars - 3) . "...";
        }
    }

    $smarty -> assign("T_MESSAGES", $p_messages);

    /**This part is used at the page header*/
    $css = eF_getTableData("configuration", "value", "name='css'");
    if ($css && eF_checkParameter($css[0]['value'], 'filename') && is_file(G_ROOTPATH.'www/css/custom_css/'.$css[0]['value'])) {
        $smarty -> assign("T_HEADER_CSS", $css[0]['value']);
    } else {
        $smarty -> assign("T_HEADER_CSS", "normal.css");
    }
    $smarty -> assign("T_CURRENT_CTG", $ctg);
    $smarty -> assign("T_HEADER_EDITOR", $load_editor);

    include ("../module_search.php");

    $smarty -> assign("T_HEADER_LOAD_SCRIPTS", $loadScripts);

    $smarty -> assign('T_VIEWINGMESSAGE', 0);
    $smarty -> assign('T_FOLDER', $folder);

    $smarty -> assign('T_MENUCTG', 'messages');
    $smarty -> assign('T_MESSAGE', $message);
    $smarty -> assign('T_MESSAGE_TYPE', $message_type);
    $smarty -> assign("T_SEARCH_MESSAGE", $search_message);

    $smarty -> assign("T_SHOWFOOTER", $GLOBALS['configuration']['show_footer']);      //Needed by footer
    $smarty -> assign("T_ADMINEMAIL", $GLOBALS['configuration']['system_email']);      //Needed by footer
    $smarty -> assign("T_QUERIES", $numberOfQueries);           //Needed by footer

    try{
        $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
        $lesson_name = $currentLesson -> lesson['name'];
    }
    catch (Exception $e){
        $lesson_name = "";
    }

    $smarty -> assign('T_LESSONNAME', $lesson_name);

    $smarty -> load_filter('output', 'eF_template_formatTimestamp');
    $smarty -> load_filter('output', 'eF_template_formatLogins');
    $smarty -> display('forum/messages_index.tpl');
    exit;
} else {
    $p_messages          = eF_getTableData("f_personal_messages", "*", "users_LOGIN='".$_SESSION['s_login']."' and f_folders_ID=".$folder, "priority desc, viewed,timestamp desc");

    $num_of_pages        = ceil(sizeof($p_messages) / $p_messages_per_page);

    $page != 1 && $num_of_pages > 0 ? $pages_str = '<a href = "forum/message_index.php?folder='.$folder.'&page='.($page - 1).'">&laquo</a>' : $pages_str = '';
    for ($i = 1; $i <= $num_of_pages; $i++) {
        if ($i != $page) {
            $pages_str .= ' <a href = "forum/message_index.php?folder='.$folder.'&page='.$i.'">'.$i.'</a>';
        } else {
            $pages_str .= ' <b>'.$i.'</b>';
        }
    }
    $page != $num_of_pages && $num_of_pages > 0 ? $pages_str .= ' <a href = "forum/message_index.php?folder='.$folder.'&page='.($page + 1).'">&raquo;</a>' : $pages_str .= '';

    $offset = ($page - 1) * $p_messages_per_page;                              //This is used to display messages per page

    $smarty -> assign("T_MESSAGES", $p_messages);
    $smarty -> assign("T_MESSAGES_SIZE", sizeof($p_messages));

//    $smarty -> assign("T_PAGES", $pages_str);

}





//---------------------------------------End of Messages-------------------------------------------

//--------------------------------------View message---------------------------------------------
if (isset($_GET['p_message']) && $_GET['p_message'] != '') {
    if (!eF_checkParameter($_GET['p_message'], 'id')) {
        eF_printMessage(_INVALIDID);
        $alert_message = _TRIEDACCESSMESSAGEMALFORMEDID;
        //eF_alertAdmin($_SESSION['s_login'], time(), $alert_message);                                            //Alert admin, since this might be a hacking attempt
        exit;
    }

    $personal_message = eF_getTableData("f_personal_messages", "*", "users_LOGIN = '".$_SESSION['s_login']."' and id=".$_GET['p_message']);
    $personal_message[0]['body']= str_replace("&nbsp;"," ",$personal_message[0]['body']);
    $personal_message[0]['body']  = html_entity_decode($personal_message[0]['body'], ENT_QUOTES);
    $smarty -> assign("T_PERSONALMESSAGE", $personal_message[0]);

    if (sizeof($personal_message) == 0) {
        $message = _THISMESSAGEDOESNOTEXIST;
    } else {
        if ($personal_message[0]['attachments']) {
/*
            $attachments = array();
            $attachments[] = unserialize($personal_message[0]['attachments']);
            foreach ($attachments as $attach) {
                $attach_filenames[] = preg_replace('/[0-9]{10}_prefix_(.*)/', "$1", basename($attach));
                $attach_names[]     = basename($attach);
            }
            $smarty -> assign("T_ATTACHMENTS_FILENAMES", $attach_filenames);
            $smarty -> assign("T_ATTACHMENTS_NAMES", $attach_names);
*/

            try {
                $attachment = new EfrontFile($personal_message[0]['attachments']);
                $smarty -> assign("T_ATTACHMENT", $attachment);
            } catch (Exception $e) {
                $message      = _ERROROPENINGATTACHMENT;
                $message_type = 'failure';
            }
        }
        $viewing_message = true;
        eF_updateTableData("f_personal_messages", array("viewed" => 1), "id=".$personal_message[0]['id']);
    }
}

//--------------------------------------End of view message---------------------------------------------

$ctg_title = "Personal Messages";

$in_messages_count  = eF_getTableData("f_personal_messages", "*", "users_LOGIN='".$_SESSION['s_login']."' and f_folders_ID=".$in_folder[0]['id']);

$out_messages_count  = eF_getTableData("f_personal_messages", "*", "users_LOGIN='".$_SESSION['s_login']."' and f_folders_ID=".$sent_folder[0]['id']);

$draft_messages_count  = eF_getTableData("f_personal_messages", "*", "users_LOGIN='".$_SESSION['s_login']."' and f_folders_ID=".$draft_folder[0]['id']);

$p_folders[0]['count'] = sizeof($in_messages_count);
$p_folders[1]['count'] = sizeof($out_messages_count);
$p_folders[2]['count'] = sizeof($draft_messages_count);
$folders_size = sizeof($p_folders);
for ($i = 3; $i < $folders_size; $i++) {
    $temp_count  = eF_getTableData("f_personal_messages", "*", "users_LOGIN='".$_SESSION['s_login']."' and f_folders_ID=".$p_folders[$i]['id']);
    $p_folders[$i]['count'] = sizeof($temp_count);

}
$smarty -> assign("T_FOLDERS", $p_folders);


/**This part is used at the page header*/
$css = eF_getTableData("configuration", "value", "name='css'");
if ($css && eF_checkParameter($css[0]['value'], 'filename') && is_file(G_ROOTPATH.'www/css/custom_css/'.$css[0]['value'])) {
    $smarty -> assign("T_HEADER_CSS", $css[0]['value']);
} else {
    $smarty -> assign("T_HEADER_CSS", "normal.css");
}
$smarty -> assign("T_CURRENT_CTG", $ctg);
$smarty -> assign("T_HEADER_EDITOR", $load_editor);

include ("../module_search.php");
$loadScripts[] = 'EfrontScripts';
$loadScripts[] = 'scriptaculous/prototype';
$loadScripts[] = 'scriptaculous/scriptaculous';
$loadScripts[] = 'scriptaculous/effects';
$loadScripts[] = 'scriptaculous/dragdrop';

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", $loadScripts);

$smarty -> assign('T_VIEWINGMESSAGE', $viewing_message);
$smarty -> assign('T_FOLDER', $folder);

$smarty -> assign('T_MENUCTG', 'messages');
$smarty -> assign('T_MESSAGE', $message);
$smarty -> assign('T_MESSAGE_TYPE', $message_type);
$smarty -> assign("T_SEARCH_MESSAGE", $search_message);

$smarty -> assign("T_SHOWFOOTER", $GLOBALS['configuration']['show_footer']);      //Needed by footer
$smarty -> assign("T_ADMINEMAIL", $GLOBALS['configuration']['system_email']);      //Needed by footer
$smarty -> assign("T_QUERIES", $numberOfQueries);           //Needed by footer

try{
    $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
    $lesson_name = $currentLesson -> lesson['name'];
}
catch (Exception $e){
    $lesson_name = "";
}

$smarty -> assign('T_LESSONNAME', $lesson_name);

$smarty -> load_filter('output', 'eF_template_formatTimestamp');
$smarty -> load_filter('output', 'eF_template_formatLogins');

$smarty -> display('forum/messages_index.tpl');



?>
