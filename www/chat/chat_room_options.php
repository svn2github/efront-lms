<?php
/**
* Chat options page
*
* This page implements various chat options. 
*
* @package eFront
* @version 0.1
* @todo Professor statistics
*/

session_cache_limiter('none');
session_start();

$path = "../../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";


/*Check the user type. If the user is not valid, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);

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
        $smarty -> assign("T_CURRENT_USER", $currentUser);
        
        if ($currentUser -> coreAccess['chat'] == 'hidden') {
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


if (isset($_POST['chat_room_submit']) && (!$currentUser -> coreAccess['chat'] || $currentUser -> coreAccess['chat'] == 'change')) {                                                                        //Create the new room
    if (!eF_checkParameter($_POST['chat_room_name'], 'text')) {
        $message      = _INVALIDNAME;
        $message_type = failure;
    } else {
        $result = eF_getTableData("chatrooms", "id", "name='".$_POST['chat_room_name']."'");
        if (sizeof($result) == 0 ) {
            isset($_POST['chat_room_active']) ? $active = 1 : $active = 0;
            $_POST['chat_room_type'] == 'private' ? $type = 'private' : $type = 'public';                           //Check for security reasons

            $fields_insert = array ('name'             => $_POST['chat_room_name'],                                 //***Check here
                                    'active'           => 1, //mpaltas-change-all are active by defautl$active,
                                    'type'             => $type,
                                    'users_LOGIN'      => $_SESSION['s_login'],
                                    'create_timestamp' => time());

            eF_insertTableData("chatrooms", $fields_insert);

            $message      = _SUCCESFULLYADDEDROOM;
            $message_type = 'success';
        } else {
            $message      = _ROOMALREADYEXISTS;
            $message_type = 'failure';
        }
    }
}

if (isset($_GET['new_public_room']) || isset($_GET['new_private_room'])) {                                      //Display the Create new room from
    isset($_GET['new_public_room']) ? $room_type = 'public' : $room_type = 'private';
    $smarty -> assign("T_ROOM_TYPE", $room_type);    
}

if (isset($_GET['past_messages'])) {                                                  //Room options
    $options_str = '';
    $chatrooms   = eF_getTableData("chatrooms", "name, id", "type = 'private' AND users_LOGIN = '".$_SESSION['s_login']."'");            //Get only private rooms that belong to this user
    
    if ($_SESSION['s_type'] == "administrator"){
        $chatrooms   = array_merge(eF_getTableData("chatrooms", "id, name, active", "type = 'public'"), $chatrooms); 
    } else {
        $chatrooms   = array_merge(eF_getTableData("chatrooms,users_to_lessons as ul", "distinct id, name, chatrooms.active", "type = 'public' AND (chatrooms.lessons_ID=ul.lessons_ID OR chatrooms.lessons_ID IS NULL) AND ul.users_LOGIN='".$_SESSION['s_login']."'"), $chatrooms); 
    }   
//Also get all public rooms where user has access....26/2/2007
  //pr($chatrooms);
    $chatrooms = array_merge(array(array("name" => _EFRONTMAIN, "id" => 0 , "active" =>1)), $chatrooms);
    $smarty -> assign("T_CHATROOMS", $chatrooms);
    $smarty -> assign("T_DAY_BEFORE", time() - 86400);
    
    $users = eF_getTableData("users", "login, name, user_type");
    $smarty -> assign("T_USERS", $users);
    
}

if (isset($_POST['chat_submit_show_messages'])) {
    $from_timestamp = mktime($_POST['from_time_Hour'], $_POST['from_time_Minute'], 0, $_POST['from_date_Month'], $_POST['from_date_Day'], $_POST['from_date_Year']);
    $to_timestamp   = mktime($_POST['to_time_Hour'],   $_POST['to_time_Minute'],   0, $_POST['to_date_Month'],   $_POST['to_date_Day'],   $_POST['to_date_Year']);
    
    if ($_POST['select_chat_room'] != 0 && !eF_checkParameter($_POST['select_chat_room'], 'id')) {
        eF_printMessage(_INVALIDID);
        exit;
    } else {
        $chatroom = $_POST['select_chat_room'];
    }
    
    if ($_POST['select_user'] != 0 && !eF_checkParameter($_POST['select_user'], 'login')) {
        eF_printMessage(_INVALIDLOGIN);
        exit;
    } else {
        $_POST['select_user'] ? $user_sql = " AND users_LOGIN = '".$_POST['select_user']."'" : $user_sql = '';
    }
    
    $messages = eF_getTableData("chatmessages", "timestamp, content, users_LOGIN, users_USER_TYPE", "timestamp >= $from_timestamp AND timestamp <= $to_timestamp AND chatrooms_ID=$chatroom".$user_sql);

    foreach ($messages as $value) {
        if ($value['content'][0] != '#' || !preg_match("/^#for_user-(\S*):(\d+)#/", $value['content'], $matches) || ($matches[1] == $_SESSION['s_login'] && $value['content'] = mb_substr($value['content'], mb_strlen($matches[0])))) {     //Explanation for this line: The first part, $value['content'][0] != '#' , is a fast check for the special character #. if it does not exist, proceed and display message. Otherwise, check if the character is followed by a specific sequence, of the form: #for_user-<login>#. If so, then display only the message to the current user (where $_SESSION['s_login'] == <login>) and finally delete the special sequence from the beginning of the message (the mb_substr part). otherwise (if it starts with # but is not a special message), display the message.
            $messages_filtered[] = $value;
        }
    }
    
    $smarty -> assign("T_MESSAGES", $messages_filtered);
    
}

if (isset($_POST['chat_submit_delete_messages'])) {
    $from_timestamp = mktime($_POST['from_time_Hour'], $_POST['from_time_Minute'], 0, $_POST['from_date_Month'], $_POST['from_date_Day'], $_POST['from_date_Year']);
    $to_timestamp   = mktime($_POST['to_time_Hour'],   $_POST['to_time_Minute'],   0, $_POST['to_date_Month'],   $_POST['to_date_Day'],   $_POST['to_date_Year']);
    
    if ($_POST['select_chat_room'] != 0 && !eF_checkParameter($_POST['select_chat_room'], 'id')) {
        eF_printMessage(_INVALIDID);
        exit;
    } else {
        $chatroom = $_POST['select_chat_room'];
    }
    
    if ($_POST['select_user'] != 0 && !eF_checkParameter($_POST['select_user'], 'login')) {
        eF_printMessage(_INVALIDLOGIN);
        exit;
    } else {
        $_POST['select_user'] ? $user_sql = " AND users_LOGIN = '".$_POST['select_user']."'" : $user_sql = '';
    }
    
    if (eF_deleteTableData("chatmessages", "timestamp >= $from_timestamp AND timestamp <= $to_timestamp AND chatrooms_ID=$chatroom".$user_sql)) {
	    $message = _CHATMESSAGESDELETEDSUCCESSFULLY;
	    $message_type = "success";
    } else {
        $message = _CHATMESSAGESCOULDNOTBEDELETED;
	    $message_type = "failure";
    }
}

if (isset($_POST['chat_submit_export_messages'])) {
    $from_timestamp = mktime($_POST['from_time_Hour'], $_POST['from_time_Minute'], 0, $_POST['from_date_Month'], $_POST['from_date_Day'], $_POST['from_date_Year']);
    $to_timestamp   = mktime($_POST['to_time_Hour'],   $_POST['to_time_Minute'],   0, $_POST['to_date_Month'],   $_POST['to_date_Day'],   $_POST['to_date_Year']);
    
    if ($_POST['select_chat_room'] != 0 && !eF_checkParameter($_POST['select_chat_room'], 'id')) {
        eF_printMessage(_INVALIDID);
        exit;
    } else {
        $chatroom = $_POST['select_chat_room'];
    }
    
    if ($_POST['select_user'] != 0 && !eF_checkParameter($_POST['select_user'], 'login')) {
        eF_printMessage(_INVALIDLOGIN);
        exit;
    } else {
        $_POST['select_user'] ? $user_sql = " AND users_LOGIN = '".$_POST['select_user']."'" : $user_sql = '';
    }
    
    $messages = eF_getTableData("chatmessages", "timestamp, content, users_LOGIN, users_USER_TYPE", "timestamp >= $from_timestamp AND timestamp <= $to_timestamp AND chatrooms_ID=$chatroom".$user_sql);
    $file = EfrontSystem::exportChat($messages);
    header("content-type:".$file['mime_type']);
    header('content-disposition: attachment; filename= "'.($file['name']).'"');
    readfile($file['path']);
    exit;
}

if (isset($_GET['show_users']) && eF_checkParameter($_GET['show_users'], 'id')) {
    $room_name  = eF_getTableData("chatrooms", "name", "id=".$_GET['show_users']);
    $users_list = eF_getTableDataFlat("users_to_chatrooms", "users_LOGIN", "chatrooms_ID=".$_GET['show_users']);
    
    $smarty -> assign("T_CHATROOM_NAME", $room_name[0]['name']);
    $smarty -> assign("T_USERS_LIST", $users_list['users_LOGIN']);    
}


$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);

$smarty -> load_filter('output', 'eF_template_formatTimestamp');

$smarty -> display("chat/chat_room_options.tpl");
?>

