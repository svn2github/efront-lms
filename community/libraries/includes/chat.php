<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

if (isset($_GET['chat_room_options'])) {

    if (isset($_POST['chat_room_submit']) && (!$currentUser -> coreAccess['chat'] || $currentUser -> coreAccess['chat'] == 'change')) { //Create the new room
        if (!eF_checkParameter($_POST['chat_room_name'], 'text')) {
            $message = _INVALIDNAME;
            $message_type = failure;
        } else {
            $result = eF_getTableData("chatrooms", "id", "name='".$_POST['chat_room_name']."'");
            if (sizeof($result) == 0 ) {
                isset($_POST['chat_room_active']) ? $active = 1 : $active = 0;
                $_POST['chat_room_type'] == 'private' ? $type = 'private' : $type = 'public'; //Check for security reasons

                $fields_insert = array ('name' => $_POST['chat_room_name'], //***Check here
                                    'active' => 1, //mpaltas-change-all are active by defautl$active,
                                    'type' => $type,
                                    'users_LOGIN' => $_SESSION['s_login'],
                                    'create_timestamp' => time());

                eF_insertTableData("chatrooms", $fields_insert);

                $message = _SUCCESFULLYADDEDROOM;
                $message_type = 'success';
            } else {
                $message = _ROOMALREADYEXISTS;
                $message_type = 'failure';
            }
        }
    }

    if (isset($_GET['new_public_room']) || isset($_GET['new_private_room'])) { //Display the Create new room from
        isset($_GET['new_public_room']) ? $room_type = 'public' : $room_type = 'private';
        $smarty -> assign("T_ROOM_TYPE", $room_type);
    }

    if (isset($_GET['past_messages'])) { //Room options
        $options_str = '';
        $chatrooms = eF_getTableData("chatrooms", "name, id", "type = 'private' AND users_LOGIN = '".$_SESSION['s_login']."'"); //Get only private rooms that belong to this user

        if ($_SESSION['s_type'] == "administrator"){
            $chatrooms = array_merge(eF_getTableData("chatrooms", "id, name, active", "type = 'public'"), $chatrooms);
            $chatrooms = array_merge(array(array("name" => _EFRONTMAIN, "id" => 0 , "active" =>1)), $chatrooms);
            $smarty -> assign("T_CHATROOMS", $chatrooms);
            //  pr($chatrooms);

        } else {
            if (isset($_GET['chat_room'])) {
                if ($_GET['chat_room'] != 0) {
                    $room_name = eF_getTableData("chatrooms", "name, id, active", "id = ". $_GET['chat_room']);
                    $smarty -> assign("T_CHATROOMS", array($room_name[0]));
                    $smarty -> assign("T_SINGLE_ROOM_NAME",$room_name[0]['name']);
                } else {
                    $smarty -> assign("T_CHATROOMS", array(array("name" => _EFRONTMAIN, "active" => 1, "id" => 0)));
                    $smarty -> assign("T_SINGLE_ROOM_NAME",_EFRONTMAIN);
                }
                // only one room will be permitted for viewing

            }
        }
        //Also get all public rooms where user has access....26/2/2007
        $smarty -> assign("T_DAY_BEFORE", time() - 86400);

        $users = eF_getTableData("users", "login, name, user_type");
        $smarty -> assign("T_USERS", $users);

    }

    if (isset($_POST['chat_submit_show_messages'])) {
        $from_timestamp = mktime($_POST['from_time_Hour'], $_POST['from_time_Minute'], 0, $_POST['from_date_Month'], $_POST['from_date_Day'], $_POST['from_date_Year']);
        $to_timestamp = mktime($_POST['to_time_Hour'], $_POST['to_time_Minute'], 0, $_POST['to_date_Month'], $_POST['to_date_Day'], $_POST['to_date_Year']);

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
            if ($value['content'][0] != '#' || !preg_match("/^#for_user-(\S*):(\d+)#/", $value['content'], $matches) || ($matches[1] == $_SESSION['s_login'] && $value['content'] = mb_substr($value['content'], mb_strlen($matches[0])))) { //Explanation for this line: The first part, $value['content'][0] != '#' , is a fast check for the special character #. if it does not exist, proceed and display message. Otherwise, check if the character is followed by a specific sequence, of the form: #for_user-<login>#. If so, then display only the message to the current user (where $_SESSION['s_login'] == <login>) and finally delete the special sequence from the beginning of the message (the mb_substr part). otherwise (if it starts with # but is not a special message), display the message.
                $messages_filtered[] = $value;
            }
        }

        $smarty -> assign("T_MESSAGES", $messages_filtered);

    }

    if (isset($_POST['chat_submit_delete_messages'])) {
        $from_timestamp = mktime($_POST['from_time_Hour'], $_POST['from_time_Minute'], 0, $_POST['from_date_Month'], $_POST['from_date_Day'], $_POST['from_date_Year']);
        $to_timestamp = mktime($_POST['to_time_Hour'], $_POST['to_time_Minute'], 0, $_POST['to_date_Month'], $_POST['to_date_Day'], $_POST['to_date_Year']);

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
        $to_timestamp = mktime($_POST['to_time_Hour'], $_POST['to_time_Minute'], 0, $_POST['to_date_Month'], $_POST['to_date_Day'], $_POST['to_date_Year']);

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
        $room_name = eF_getTableData("chatrooms", "name", "id=".$_GET['show_users']);
        $users_list = eF_getTableDataFlat("users_to_chatrooms", "users_LOGIN", "chatrooms_ID=".$_GET['show_users']);

        $smarty -> assign("T_CHATROOM_NAME", $room_name[0]['name']);
        $smarty -> assign("T_USERS_LIST", $users_list['users_LOGIN']);
    }

} else {


    $user_type = eF_getUserBasicType($_SESSION['s_login']);
    $smarty ->assign("T_USER",$user_type);
    if (isset($_GET['delete']) && eF_checkParameter($_GET['id'], 'id')) { //Delete the room
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
        $_GET['activate_system_chat'] ? EfrontConfiguration::setValue("chat_enabled", 1) : EfrontConfiguration::setValue("chat_enabled", 0);
        $smarty -> assign("T_RELOAD_SIDEFRAME", 1);
  $smarty ->assign("T_REFRESH_SIDE", true);
    }
    $smarty -> assign("T_CHAT_ENABLED", $GLOBALS['configuration']['chat_enabled']);

    if (isset($_GET['activate']) && eF_checkParameter($_GET['id'], 'id')) { //Activate / deactivate the room
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
            $fields_update = array('active' => $active);
            eF_updateTableData("chatrooms", $fields_update, "id=".$_GET['id']);
        }
    }

    $ctg = "chat";


    if (isset($_GET['logout'])) {
        if (isset($_GET['chatrooms_ID']) && eF_checkParameter($_GET['chatrooms_ID'], 'id')) {
            $chatrooms_ID = $_GET['chatrooms_ID'];
            eF_deleteTableData("users_to_chatrooms", "users_LOGIN='".$_SESSION['s_login']."' AND chatrooms_ID=".$chatrooms_ID);

            $system_message = '<span class = "chatSystemMessage">'._THEUSER.' '.$_SESSION['s_login'].' '._LEFTTHEROOM.' <span class = "boldFont">('._SYSTEMMESSAGE.')</span></span><br/>'; //Build the entrance notification message
            $fields_insert = array('users_LOGIN' => $_SESSION['s_login'],
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'content' => $system_message,
                               'timestamp' => time(),
                               'chatrooms_ID' => $chatrooms_ID);
            eF_insertTableData("chatmessages", $fields_insert); //Insert the system message into the database

            unset ($_GET['chatrooms_ID']);
        } else {
            $user_in_rooms_flat = eF_getTableDataFlat("users_to_chatrooms", "chatrooms_ID", "users_LOGIN = '".$_SESSION['s_login']."'");
            foreach ($user_in_rooms_flat['chatrooms_ID'] as $chatrooms_ID) {
                eF_deleteTableData("users_to_chatrooms", "users_LOGIN='".$_SESSION['s_login']."' AND chatrooms_ID=".$chatrooms_ID);

                $system_message = '<span class = "chatSystemMessage">'._THEUSER.' '.$_SESSION['s_login'].' '._LEFTTHEROOM.' <span class = "boldfont">('._SYSTEMMESSAGE.')</span></span><br/>'; //Build the entrance notification message
                $fields_insert = array('users_LOGIN' => $_SESSION['s_login'],
                                   'users_USER_TYPE' => $_SESSION['s_type'],
                                   'content' => $system_message,
                                   'timestamp' => time(),
                                   'chatrooms_ID' => $chatrooms_ID);
                eF_insertTableData("chatmessages", $fields_insert); //Insert the system message into the database
            }
        }
    }

    if (isset($_GET['chatrooms_ID']) && eF_checkParameter($_GET['chatrooms_ID'], 'id') && isset($_GET['invite']) && eF_checkParameter($_GET['invite'], 'login')) { //The user asked to have a private conversation
        $fields_insert = array('name' => _PRIVATECONVERSATIONWITH.' '.$_GET['invite'],
                            'create_timestamp' => time(),
                            'users_LOGIN' => $_SESSION['s_login'],
                            'type' => 'one_to_one',
                            'active' => 1);

        $private_id = eF_insertTableData("chatrooms", $fields_insert); //Create the room

        $fields_insert = array('users_LOGIN' => $_SESSION['s_login'],
                           'users_USER_TYPE' => $_SESSION['s_type'],
                           'content' => '<span class = "chatSystemMessage">'._WAITINGFORUSER.' '.$_GET['invite'].' '._TORESPOND.'<span class = "boldfont">('._SYSTEMMESSAGE.')</span></span>',
                           'timestamp' => time() + 10, //If it's right on time, it won;t display in the new window
                           'chatrooms_ID' => $private_id);

        eF_insertTableData("chatmessages", $fields_insert);

        $user_type = eF_getTableData("users", "user_type", "login='".$_GET['invite']."'");
        $fields_insert = array('users_LOGIN' => $_GET['invite'],
                           'users_USER_TYPE' => $user_type[0]['user_type'],
                           'content' => '',
                           'timestamp' => time(),
                           'chatrooms_ID' => $_GET['chatrooms_ID']);

        $msg_id_to = eF_insertTableData("chatmessages", $fields_insert);

        $fields_insert = array('users_LOGIN' => $_SESSION['s_login'],
                           'users_USER_TYPE' => $_SESSION['s_type'],
                           'content' => '',
                           'timestamp' => time(),
                           'chatrooms_ID' => $_GET['chatrooms_ID']);

        $msg_id_from = eF_insertTableData("chatmessages", $fields_insert);

        eF_updateTableData("chatmessages", array('content' => '#for_user-'.$_SESSION['s_login'].':'.$msg_id_from.'#<span class = "chatSystemMessage">'._YOUHAVEINVITEDUSER.' '.$_GET['invite'].' '._TOAPRIVATECONVERSATION.'.<span class = "boldFont">('._SYSTEMMESSAGE.')</span></span>'), "id=$msg_id_from");
        eF_updateTableData("chatmessages", array('content' => '#for_user-'.$_GET['invite'].':'.$msg_id_to.'#<span class = "chatSystemMessage">'._THEUSER.' '.$_SESSION['s_login'].' '._INVITESYOUTOPRIVATECONVERSATION.'. '._DOYOU.' <a href = "javascript:void(0)" onclick = "popUp(&quot;chat/chat_index.php?chatrooms_ID='.$private_id.'&msg_id_from='.$msg_id_from.'&msg_id_to='.$msg_id_to.'&from_user='.$_SESSION['s_login'].'&reply=accept&standalone=1&quot;, 600, 400)">'._YOUACCEPT.'</a> '._OR.' <a href = "javascript:void(0)" onclick = "popUp(&quot;chat/chat_index.php?chatrooms_ID='.$private_id.'&msg_id_from='.$msg_id_from.'&msg_id_to='.$msg_id_to.'&from_user='.$_SESSION['s_login'].'&reply=deny&standalone=1&quot;, 200, 100)">'._YOUDENY.'</a>'._QUESTIONMARK.'<span>('._SYSTEMMESSAGE.')</span></span>'), "id=$msg_id_to");

        eF_redirect(basename($_SERVER['PHP_SELF']).'?chatrooms_ID='.$private_id.'&standalone=1');


    } elseif (isset($_GET['chatrooms_ID'])) { //The user selected a room to enter
        $chatrooms_ID = eF_checkParameter($_GET['chatrooms_ID'], 'uint');
        if ($chatrooms_ID === false) { //Check if the passed parameter is valid
            eF_printMessage(_WRONGROOMID);
            exit;
        }
        $res_active = eF_getTableData("chatrooms","active","id=$chatrooms_ID");
        if ($res_active[0]['active'] != 1) { //Check if the passed parameter is valid
            eF_printMessage(_CHATROOMDEACTIVATEDBYADMIN);
            exit;
        }

        $user_already_online = eF_getTableData("users_to_chatrooms","users_LOGIN","users_LOGIN = '".$_SESSION['s_login']."' AND chatrooms_ID = $chatrooms_ID");
        $fields_insert = array('users_LOGIN' => $_SESSION['s_login'],
                               'chatrooms_ID' => $chatrooms_ID,
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'timestamp' => time());
        if(count($user_already_online)==0) {
            eF_insertTableData("users_to_chatrooms", $fields_insert); //Insert new user to database
        }
        if (isset($_GET['msg_id_from']) && eF_checkParameter($_GET['msg_id_from'], 'id') && isset($_GET['msg_id_to']) && eF_checkParameter($_GET['msg_id_to'], 'id') && isset($_GET['from_user']) && eF_checkParameter($_GET['from_user'], 'login')) { //We have just accepted an invitation for a one-to-one conversation. Update messages to display the acceptance and reload chat page.
            if ($_GET['reply'] == 'accept') {
                $reply_str = '<span class = "chatSystemMessage">'._CONVERSATIONINVITATIONACCEPTED.'<span>('._SYSTEMMESSAGE.')</span></span>';
                $reload_str = 'location:chat/chat_index.php?chatrooms_ID='.$chatrooms_ID.'&standalone=1';
            } else {
                $reply_str = '<span class = "chatSystemMessage">'._CONVERSATIONINVITATIONDENIED.'<span>('._SYSTEMMESSAGE.')</span></span>';
                $reload_str = 'location:chat/chat_index.php?close=true';
            }

            eF_updateTableData("chatmessages", array("content" => "#for_user-".$_GET['from_user'].":0#$reply_str"), "id=".$_GET['msg_id_from']);
            eF_updateTableData("chatmessages", array("content" => "#for_user-".$_SESSION['s_login'].":0#$reply_str"), "id=".$_GET['msg_id_to']);
            $fields_insert = array('users_LOGIN' => $_SESSION['s_login'],
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'content' => $reply_str,
                               'timestamp' => time(),
                               'chatrooms_ID' => $chatrooms_ID);
            //print_r($fields_insert);
            eF_insertTableData("chatmessages", $fields_insert);

            header($reload_str);
        }

        isset($_GET['standalone']) ? $standalone_str = '&standalone=1' : $standalone_str = ''; //Is it the stand-alone version of the window?

        $room_name = eF_getTableData("chatrooms", "name", "id = $chatrooms_ID"); //Get the room name

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
}





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
