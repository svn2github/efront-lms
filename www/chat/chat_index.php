<?php
/**
* Chat root page
*
* This page provides links to all chat rooms and various chat functions
*
* @package eFront
* @version 0.1
* @todo delete conversations after a period of time
*/

session_cache_limiter('none');
session_start();

$path = "../../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

if (isset($_GET['message'])) {
    
    $message = $_GET['message'];
    $message_type = $_GET['message_type'];
} else {
    $message = '';$message_type = '';                            //Initialize messages, because if register_globals is turned on, some messages will be displayed twice
}
//echo "<pre>";print_r($_POST);print_r($_GET);echo "</pre>";
//error_reporting(E_ALL);
//echo "<pre>";print_r($_SESSION);

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

$load_editor  = false;
$load_scripts = array('print-script', 'eF_scripts', 'PieNG', 'scriptaculous/prototype', 'scriptaculous/scriptaculous', 'scriptaculous/sidebar_extra');


//if ($_SESSION['s_language'] == "greek") {
//    setlocale(LC_TIME,'gr_GR@euro', 'gr_GR', 'gr','gre','ell','greek');
//}

/**Search module is used to display the search field and perform the searches*/
include "../module_search.php";

/**Added Session variable for search results*/
$_SESSION['referer'] = $_SERVER['REQUEST_URI'];
$user_type = eF_getUserBasicType($_SESSION['s_login']);
$smarty ->assign("T_USER",$user_type);
if (isset($_GET['delete']) && eF_checkParameter($_GET['id'], 'id')) {                                                           //Delete the room
    if ($currentUser -> getType() == 'administrator') {
        $result = eF_getTableData("chatrooms", "id", "id = ".$_GET['id']);           
	    if (sizeof($result) > 0) {
	        eF_deleteTableData("chatrooms", "id=".$_GET['id']);
	        eF_deleteTableData("chatmessages", "chatrooms_ID=".$_GET['id']);
	        eF_deleteTableData("users_to_chatrooms", "chatrooms_ID=".$_GET['id']);
	        $message = _ROOMSUCCESSFULLYDELETED;
	        $message_type = "success";
	    }        
    }

}

// Deactivate the entire system chat
if (isset($_GET['activate_system_chat'])) {
    if ($_GET['activate_system_chat']) {
        eF_updateTableData("configuration", array("value" => 1), "name = 'chat_enabled'");
        //eF_updateTableData("chatrooms", array("active" => 1), "id");
        $chat_enabled = 1;
    } else {
        eF_updateTableData("configuration", array("value" => 0), "name = 'chat_enabled'");
        //eF_updateTableData("chatrooms", array("active" => 0), "id");
        //eF_deleteTableData("users_to_chatrooms");
        $chat_enabled = 0;
    }
    $smarty -> assign("T_RELOAD_SIDEFRAME", 1);
}
// Check if the chat system is enabled
if (!isset($chat_enabled)) {
    $enabled = eF_getTableData("configuration", "value", "name ='chat_enabled'");
    $chat_enabled = (int)$enabled[0]['value'];
}
$smarty -> assign("T_CHAT_ENABLED", $chat_enabled);


if (isset($_GET['activate']) && eF_checkParameter($_GET['id'], 'id')) {                                                         //Activate / deactivate the room
    $result = eF_getTableData("chatrooms", "active", "id=".$_GET['id']);
    $result[0]['active'] == 1 ? $active = 0 : $active = 1;

    $room = eF_getTableData("chatrooms", "*", "id=".$_GET['id']);
    
    // Check if room corresponds to a lesson , so that the lesson admin will know that
    // the chat module has been deactivated/activated
    if ($room[0]['lessons_ID'] != 0) {
        $lesson = new EfrontLesson($room[0]['lessons_ID']);
        if ($active) {
            $lesson ->enableChatroom();
        } else {
            $lesson ->disableChatroom();
        }
        
    } else {
	    $fields_update =     array('active' => $active);
	    eF_updateTableData("chatrooms", $fields_update, "id=".$_GET['id']);
    }
}


if (isset($_GET['activate']) && eF_checkParameter($_GET['id'], 'id')) {                                                         //Activate / deactivate the room
    $result = eF_getTableData("chatrooms", "active", "id=".$_GET['id']);
    $result[0]['active'] == 1 ? $active = 0 : $active = 1;

    $room = eF_getTableData("chatrooms", "*", "id=".$_GET['id']);
    
    // Check if room corresponds to a lesson , so that the lesson admin will know that
    // the chat module has been deactivated/activated
    if ($room[0]['lessons_ID'] != 0) {
        $lesson = new EfrontLesson($room[0]['lessons_ID']);
        if ($active) {
            $lesson ->enableChatroom();
        } else {
            $lesson ->disableChatroom();
        }
        
    } else {
	    $fields_update =     array('active' => $active);
	    eF_updateTableData("chatrooms", $fields_update, "id=".$_GET['id']);
    }
}

$ctg       = "chat";


if (isset($_GET['logout'])) {
    if (isset($_GET['chatrooms_ID']) && eF_checkParameter($_GET['chatrooms_ID'], 'id')) {
        $chatrooms_ID = $_GET['chatrooms_ID'];
        eF_deleteTableData("users_to_chatrooms", "users_LOGIN='".$_SESSION['s_login']."' AND chatrooms_ID=".$chatrooms_ID);

        $system_message = '<span class = "chatSystemMessage">'._THEUSER.' '.$_SESSION['s_login'].' '._LEFTTHEROOM.' <span class = "boldFont">('._SYSTEMMESSAGE.')</span></span><br/>'; //Build the entrance notification message
        $fields_insert = array('users_LOGIN'     => $_SESSION['s_login'],
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'content'         => $system_message,
                               'timestamp'       => time(),
                               'chatrooms_ID'    => $chatrooms_ID);        
        eF_insertTableData("chatmessages", $fields_insert);                             //Insert the system message into the database

        unset ($_GET['chatrooms_ID']);
    } else {
        $user_in_rooms_flat = eF_getTableDataFlat("users_to_chatrooms", "chatrooms_ID", "users_LOGIN = '".$_SESSION['s_login']."'");
        foreach ($user_in_rooms_flat['chatrooms_ID'] as $chatrooms_ID) {
            eF_deleteTableData("users_to_chatrooms", "users_LOGIN='".$_SESSION['s_login']."' AND chatrooms_ID=".$chatrooms_ID);

            $system_message = '<span class = "chatSystemMessage">'._THEUSER.' '.$_SESSION['s_login'].' '._LEFTTHEROOM.' <span class = "boldfont">('._SYSTEMMESSAGE.')</span></span><br/>'; //Build the entrance notification message
            $fields_insert = array('users_LOGIN'     => $_SESSION['s_login'],
                                   'users_USER_TYPE' => $_SESSION['s_type'],
                                   'content'         => $system_message,
                                   'timestamp'       => time(),
                                   'chatrooms_ID'    => $chatrooms_ID);        
            eF_insertTableData("chatmessages", $fields_insert);                             //Insert the system message into the database
        }
    }
}

if (isset($_GET['chatrooms_ID']) && eF_checkParameter($_GET['chatrooms_ID'], 'id') && isset($_GET['invite']) && eF_checkParameter($_GET['invite'], 'login')) {      //The user asked to have a private conversation
    $fields_insert = array('name'             => _PRIVATECONVERSATIONWITH.' '.$_GET['invite'],                     
                           'create_timestamp' => time(),
                           'users_LOGIN'      => $_SESSION['s_login'],
                           'type'             => 'one_to_one',
                           'active'           => 1);

    $private_id = eF_insertTableData("chatrooms", $fields_insert);                      //Create the room

    $fields_insert = array('users_LOGIN'     => $_SESSION['s_login'],
                           'users_USER_TYPE' => $_SESSION['s_type'],
                           'content'         => '<span class = "chatSystemMessage">'._WAITINGFORUSER.' '.$_GET['invite'].' '._TORESPOND.'<span class = "boldfont">('._SYSTEMMESSAGE.')</span></span>',
                           'timestamp'       => time() + 10,                            //If it's right on time, it won;t display in the new window
                           'chatrooms_ID'    => $private_id);
    
    eF_insertTableData("chatmessages", $fields_insert);
    
    $user_type = eF_getTableData("users", "user_type", "login='".$_GET['invite']."'");
    $fields_insert = array('users_LOGIN'     => $_GET['invite'], 
                           'users_USER_TYPE' => $user_type[0]['user_type'],
                           'content'         => '',
                           'timestamp'       => time(),
                           'chatrooms_ID'    => $_GET['chatrooms_ID']);

    $msg_id_to = eF_insertTableData("chatmessages", $fields_insert);

    $fields_insert = array('users_LOGIN'     => $_SESSION['s_login'], 
                           'users_USER_TYPE' => $_SESSION['s_type'],
                           'content'         => '',
                           'timestamp'       => time(),
                           'chatrooms_ID'    => $_GET['chatrooms_ID']);

    $msg_id_from = eF_insertTableData("chatmessages", $fields_insert);

    eF_updateTableData("chatmessages", array('content' => '#for_user-'.$_SESSION['s_login'].':'.$msg_id_from.'#<span class = "chatSystemMessage">'._YOUHAVEINVITEDUSER.' '.$_GET['invite'].' '._TOAPRIVATECONVERSATION.'.<span class = "boldFont">('._SYSTEMMESSAGE.')</span></span>'), "id=$msg_id_from");
    eF_updateTableData("chatmessages", array('content' => '#for_user-'.$_GET['invite'].':'.$msg_id_to.'#<span class = "chatSystemMessage">'._THEUSER.' '.$_SESSION['s_login'].' '._INVITESYOUTOPRIVATECONVERSATION.'. '._DOYOU.' <a href = "javascript:void(0)" onclick = "popUp(&quot;chat/chat_index.php?chatrooms_ID='.$private_id.'&msg_id_from='.$msg_id_from.'&msg_id_to='.$msg_id_to.'&from_user='.$_SESSION['s_login'].'&reply=accept&standalone=1&quot;, 600, 400)">'._YOUACCEPT.'</a> '._OR.' <a href = "javascript:void(0)" onclick = "popUp(&quot;chat/chat_index.php?chatrooms_ID='.$private_id.'&msg_id_from='.$msg_id_from.'&msg_id_to='.$msg_id_to.'&from_user='.$_SESSION['s_login'].'&reply=deny&standalone=1&quot;, 200, 100)">'._YOUDENY.'</a>'._QUESTIONMARK.'<span>('._SYSTEMMESSAGE.')</span></span>'), "id=$msg_id_to");

    header('location:chat/chat_index.php?chatrooms_ID='.$private_id.'&standalone=1');
   
} elseif (isset($_GET['chatrooms_ID'])) {                                                     //The user selected a room to enter
    $chatrooms_ID = eF_checkParameter($_GET['chatrooms_ID'], 'uint');
    if ($chatrooms_ID === false) {                                                      //Check if the passed parameter is valid
        eF_printMessage(_WRONGROOMID);
        exit;
    }
    $res_active = eF_getTableData("chatrooms","active","id=$chatrooms_ID");
    if ($res_active[0]['active'] != 1) {                                                      //Check if the passed parameter is valid
        eF_printMessage(_CHATROOMDEACTIVATEDBYADMIN);
        exit;
    }
    
    $user_already_online = eF_getTableData("users_to_chatrooms","users_LOGIN","users_LOGIN = '".$_SESSION['s_login']."' AND chatrooms_ID = $chatrooms_ID"); 
        $fields_insert = array('users_LOGIN'     => $_SESSION['s_login'],
                               'chatrooms_ID'    => $chatrooms_ID,
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'timestamp'       => time());
      if(count($user_already_online)==0) {                            
            eF_insertTableData("users_to_chatrooms", $fields_insert);                       //Insert new user to database                               
      }
    if (isset($_GET['msg_id_from']) && eF_checkParameter($_GET['msg_id_from'], 'id') && isset($_GET['msg_id_to']) && eF_checkParameter($_GET['msg_id_to'], 'id') && isset($_GET['from_user']) && eF_checkParameter($_GET['from_user'], 'login')) {      //We have just accepted an invitation for a one-to-one conversation. Update messages to display the acceptance and reload chat page.
        if ($_GET['reply'] == 'accept') {
            $reply_str  = '<span class = "chatSystemMessage">'._CONVERSATIONINVITATIONACCEPTED.'<span>('._SYSTEMMESSAGE.')</span></span>';
            $reload_str = 'location:chat/chat_index.php?chatrooms_ID='.$chatrooms_ID.'&standalone=1';
        } else {
            $reply_str  = '<span class = "chatSystemMessage">'._CONVERSATIONINVITATIONDENIED.'<span>('._SYSTEMMESSAGE.')</span></span>';
            $reload_str = 'location:chat/chat_index.php?close=true';
        }

        eF_updateTableData("chatmessages", array("content" => "#for_user-".$_GET['from_user'].":0#$reply_str"), "id=".$_GET['msg_id_from']);
        eF_updateTableData("chatmessages", array("content" => "#for_user-".$_SESSION['s_login'].":0#$reply_str"), "id=".$_GET['msg_id_to']);
        $fields_insert = array('users_LOGIN'     => $_SESSION['s_login'],
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'content'         => $reply_str,
                               'timestamp'       => time(),
                               'chatrooms_ID'    => $chatrooms_ID);
        //print_r($fields_insert);
        eF_insertTableData("chatmessages", $fields_insert);

        header($reload_str);
    }

    isset($_GET['standalone']) ? $standalone_str = '&standalone=1' : $standalone_str = '';      //Is it the stand-alone version of the window?
    
    $room_name = eF_getTableData("chatrooms", "name", "id = $chatrooms_ID");            //Get the room name
    
    $smarty -> assign("T_ROOM_TITLE", $room_name[0]['name']);
    $smarty -> assign("T_CHATROOMS_ID", $chatrooms_ID);
    $smarty -> assign("T_STANDALONE_STR", $standalone_str);    
    
    $smarty -> assign("T_SHOW_ROOM", true);
    
} else {
//pr(eF_local_roomInfo('public'));
    $smarty -> assign("T_PUBLIC_ROOMS", eF_local_roomInfo('public'));    
    $smarty -> assign("T_PRIVATE_ROOMS", eF_local_roomInfo('private'));
    
    $smarty -> assign("T_SHOW_ROOMS_LIST", true);
}


/**This part is used at the page header*/
$css = eF_getTableData("configuration", "value", "name='css'");
if ($css && eF_checkParameter($css[0]['value'], 'filename') && is_file(G_ROOTPATH.'www/css/custom_css/'.$css[0]['value'])) {
    $smarty -> assign("T_HEADER_CSS", $css[0]['value']);
} else {
    $smarty -> assign("T_HEADER_CSS", "normal.css");
}


if (isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID']) {
    $lesson_name = eF_getTableData("lessons", "name", "id=".$_SESSION['s_lessons_ID']);
    $smarty -> assign("T_LESSON_NAME", $lesson_name[0]['name']);
}

$smarty -> assign("T_HEADER_EDITOR", $load_editor);
$smarty -> assign("T_HEADER_LOAD_SCRIPTS", $load_scripts);
$smarty -> assign('T_MENUCTG', $ctg);
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_SHOWFOOTER", $GLOBALS['configuration']['show_footer']);

$smarty -> load_filter('output', 'eF_template_formatTimestamp');
$smarty -> load_filter('output', 'eF_template_formatLogins');

$smarty -> display("chat/chat_index.tpl");

//$smarty -> assign("T_CURRENT_CTG", $ctg);
//$smarty -> assign("T_TITLE", $title);
//$smarty -> assign("T_NUMOF_ONLINE_USERS", eF_getOnlineNumber());
//$smarty -> assign("T_NAVIGATION", $navigation);
//$smarty -> assign("T_MENU2", eF_printMenu($ctg, false));
//$smarty -> assign("T_MENU", eF_getMenu());

//$smarty -> assign("T_BOTTOM", eF_printBottom(false));
//$smarty -> assign("T_SEARCH_MESSAGE", $search_message);
//$smarty -> assign("T_ONLINE_USERS_LIST", eF_getUsersOnline(false));
//$smarty -> assign("T_ADMINEMAIL", $GLOBALS['configuration']['system_email']);

//$smarty -> assign("T_UNREAD_MESSAGES", eF_getUnreadMessagesNumber());


//---------------------------------------------------Local functions----------------------------------------------------

/**
* Get room information
*
* This function is used to get all the information necessary to build the chat rooms list
* The $room_type parameter can be one of 'public or 'private'.
* <br/>Example:
* <code>
* print_r(eF_local_roomInfo('public'));
* //Outputs:
*Array
*(
*    [0] => Array
*        (
*            [id] => 1
*            [name] => Public room 1
*            [create_timestamp] => 1119538555
*            [users_LOGIN] => admin
*            [active] => 1
*            [num_of_users_in_room] => 0
*            [exit] => true
*        )
*
*)
* </code>
* The [exit] array element is only existing when the current user is logged in the room.
*
* @param string $room_type The rooms type
* @return array The rooms list
* @version 1.0
* date: 1/10/05
* date: 21/2/07 ---> public rooms take care of users_to_lessons (except for admin)...makriria
*/
function eF_local_roomInfo($room_type) {

    if ($room_type == 'public') {
        if ($_SESSION['s_type'] == "administrator"){
            $rooms_array = eF_getTableData("chatrooms", "id, name, create_timestamp, users_LOGIN, active, lessons_ID", "");
        }else {
        $rooms_array = eF_getTableData("chatrooms,users_to_lessons as ul", "distinct id, name, create_timestamp, chatrooms.users_LOGIN, chatrooms.active", "type = 'public' AND (chatrooms.lessons_ID=ul.lessons_ID OR chatrooms.lessons_ID IS NULL) AND ul.users_LOGIN='".$_SESSION['s_login']."'");
        }
    } elseif ($room_type == 'private') {
        $rooms_array = eF_getTableData("chatrooms", "id, name, create_timestamp, users_LOGIN, active", "users_LOGIN = '".$_SESSION['s_login']."' AND type = 'private'");
    } else {
        return array();
    }

    $user_in_rooms_flat = eF_getTableDataFlat("users_to_chatrooms", "chatrooms_ID", "users_LOGIN = '".$_SESSION['s_login']."'");

    for ($i = 0; $i < sizeof($rooms_array); $i++) {
        if (sizeof($user_in_rooms_flat) > 0 && in_array($rooms_array[$i]['id'], $user_in_rooms_flat['chatrooms_ID'])) {
            $rooms_array[$i]['exit'] = true;
        }

        $num_of_users_in_room = eF_getTableData("users_to_chatrooms", "count(*)", "chatrooms_ID=".$rooms_array[$i]['id']);
        $num_of_users_in_room[0]['count(*)'] > 0 ? $rooms_array[$i]['num_of_users_in_room'] = $num_of_users_in_room[0]['count(*)'] : $rooms_array[$i]['num_of_users_in_room'] = 0;
    }

    return $rooms_array;
}


?>
