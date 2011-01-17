<?php
/**

* Chat functionality page

*

* This page implements the chat functionality. It is the iframe's target.

*

* @package eFront

* @version 0.1

* @todo Limited users per room

* @todo Limited rooms

*/
//session_cache_limiter('none');
session_start();
$path = "../libraries/";
/** Configuration file.*/
include_once $path."configuration.php";
try {
 $currentUser = EfrontUser :: checkUserAccess();
} catch (Exception $e) {
 echo "<script>parent.location = 'index.php?logout=true&message=".urlencode($e -> getMessage().' ('.$e -> getCode().')')."&message_type=failure'</script>"; //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
 exit;
}
$special_splitter = "||||";
if (isset($_GET['chatrooms_ID'])) {
    if (eF_checkParameter($_GET['chatrooms_ID'], 'id')) {
     $chatrooms_ID = $_GET['chatrooms_ID'];
    } else {
     $chatrooms_ID = 0;
    }
} else if (isset($_GET['bring_chatrooms'])){
    // The chatrooms are all the ones with more than zero users and the ones you have created
    $rooms = eF_getTableData("chatrooms LEFT OUTER JOIN users_to_chatrooms ON users_to_chatrooms.chatrooms_ID = chatrooms.id", "chatrooms.id, chatrooms.name, count(users_to_chatrooms.users_LOGIN) as users, chatrooms.users_LOGIN", "chatrooms.active=1 group by id");
//pr($rooms);
    $data = "";
    foreach ($rooms as $room) {
        if ($room['users'] > 0 || $room['users_LOGIN'] == $_SESSION['s_login']) {
            if ($room['users_LOGIN'] == $_SESSION['s_login']) {
                // The "_" after the room id means that the room is owned by the user that asked for it
                $data .= $room['id']. "_" . $special_splitter . $room['name'] .$special_splitter.$room['users']. $special_splitter;
            } else {
                $data .= $room['id']. $special_splitter . $room['name'] .$special_splitter.$room['users']. $special_splitter;
            }
        }
    }
    $data .= "-1";
   // pr($rooms);
    echo $data;
    exit;
}

// Delete a user from a chat room - this happens on room deactivations
if (isset($_GET['delete_user']) && eF_checkParameter($_GET['delete_user'], 'login')) {
    eF_deleteTableData("users_to_chatrooms", "chatrooms_ID = '".$chatrooms_ID."' AND users_LOGIN = '".$_GET['delete_user']."'");
    exit;
}

// Delete a chat room
if (isset($_GET['delete_room'])) {

    // The id check is inserted for security reasons - only if this session user is the owner will the channel be deleted
    if (eF_deleteTableData("chatrooms", "users_LOGIN = '".$_SESSION['s_login']."' AND id = '".$chatrooms_ID."'")) {
        eF_deleteTableData("users_to_chatrooms", "chatrooms_ID = '".$chatrooms_ID."'");
        $_SESSION['last_chat_msg_id'] = 0;
    }

    exit;
}

// Insert into a new chatroom
if (isset($_GET['add_user']) && isset($_GET['add_user_type']) && eF_checkParameter($_GET['add_user'], 'login') && eF_checkParameter($_GET['add_user_type'], 'alnum')) {
    eF_deleteTableData("users_to_chatrooms", "users_LOGIN = '".$_GET['add_user']."'");

    if ($chatrooms_ID != 0) {
     $userRecord = array("users_LOGIN" => $_GET['add_user'],
                         "chatrooms_ID" => $chatrooms_ID ,
                         "users_USER_TYPE" => $_GET['add_user_type'],
                         "timestamp" => time());

        eF_insertTableData("users_to_chatrooms", $userRecord);
    }

    // Set last_id to zero to get correct last messages
    $_SESSION['last_chat_msg_id'] = 0;
    exit;
}

// Get online users of current room
if(isset($_GET['get_users'])) {
    // The room users of the eFront general room are all users-the ones currently logged in to another channel
    $data = "";

    if ($chatrooms_ID == 0) {
        // @performance: 2DB
        //$all_users = eF_getTableDataFlat("users_online", "users_LOGIN");
        $all_users = eF_getTableDataFlat("user_times", "users_LOGIN", "session_expired=0");
        $other_room_users = eF_getTableDataFlat("users_to_chatrooms", "users_LOGIN", "");
        if (empty($other_room_users)) {
            $efront_general_users = $all_users['users_LOGIN'];
        } else {
            $efront_general_users = array_diff($all_users['users_LOGIN'], $other_room_users['users_LOGIN']);
        }
        foreach ($efront_general_users as $user) {
            $data .= $user . "<br>";
        }
    } else {
        // @performance: 1DB
        $users = eF_getTableData("users_to_chatrooms", "users_LOGIN", "chatrooms_ID = '".$chatrooms_ID. "'");
        foreach ($users as $user) {
            $data .= $user['users_LOGIN'] . "<br>";
        }
    }

    echo $data;
    exit;
}

$messages_limit = 25;//The messages list limit
$data = '';

if (isset($_POST['submit']) || isset($_POST['chat_message']) ) { //The user posted a message. IF the user just pressed enter, in IE POST['submit'] is not set!!!!!, so we include the || clause...
    if ($_POST['chat_message']!='') {

        // Check existence of room
        if ($chatrooms_ID != 0) { // the eFront general room always exists
         $roomExists = eF_getTableData("chatrooms", "active", "id = ". $chatrooms_ID);
         if (empty($roomExists)) {
             echo _CHATROOMDOESNOTEXIST_ERROR . $special_splitter; // notify user that room was deleted
             $_SESSION['last_chat_msg_id'] = 0;
             exit;
         } else if ($roomExists[0]['active'] == 0) {
             echo _CHATROOMISNOTENABLED_ERROR . $special_splitter; // notify user that room is not active
             $_SESSION['last_chat_msg_id'] = 0;
             exit;
         }
        }

     $fields_insert = array('users_LOGIN' => $_SESSION['s_login'],
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'content' => htmlspecialchars($_POST['chat_message'], ENT_QUOTES),
                               'timestamp' => time(),
                               'chatrooms_ID' => $chatrooms_ID);
     try {
      //pr($fields_insert);
      eF_insertTableData("chatmessages", $fields_insert); //Insert the message into the database
     } catch (Exception $e) {
      handleAjaxExceptions($e);
     }

    }
}

// Special treatment for the genaral eFront room

$last_id = isset($_SESSION['last_chat_msg_id'])?$_SESSION['last_chat_msg_id']:0;

/*

if ($last_id == 0 && $_GET['restart_session'] != 1) {

    // Some asking message came in before the first one - return

    exit;

}

*/
if ($last_id == 0 || $_GET['restart_session'] == 1) {
    $get_last_thirty_minutes = " AND timestamp > " . (time() - 1800); // 30*60 sec, last 30' minutes
    $last_id = 0;
}
if (!isset($_POST['chat_message'])) {
    $messages = eF_getTableData("chatmessages", "users_LOGIN, users_USER_TYPE, timestamp, content, id", "chatrooms_ID = $chatrooms_ID AND id > $last_id $get_last_thirty_minutes", "timestamp DESC,id DESC LIMIT $messages_limit"); //Retrieve the recent messages
    if (sizeof($messages)>0) {
        $new_id = $messages[0]['id'];
    } else {
        $new_id = $last_id;
    }
} else {
    $messages = eF_getTableData("chatmessages", "users_LOGIN, users_USER_TYPE, timestamp, content, id", "chatrooms_ID = $chatrooms_ID AND id > $last_id $get_last_thirty_minutes", "timestamp DESC, id DESC"); //Retrieve the most recent message
    if (sizeof($messages) > 0) {
        $new_id = $messages[0]['id'];
    } else {
        $new_id = $sent;
    }
}
// If this was just a check for activity in the chat room return now
if ($_GET['any_activity'] == 1) {
    if (sizeof($messages) > 0) {
        echo "ack";
        exit;
    } else {
        echo "noack";
        exit;
    }
}

$_SESSION['last_chat_msg_id'] = $new_id;
//echo "ethesa to session iso me $new_id<br>";
//    pr($messages);

$new_msg = false;
if ($_SESSION['last_message'][$chatrooms_ID] != $messages[0]['timestamp']) {
    $_SESSION['last_message'][$chatrooms_ID] = $messages[0]['timestamp'];
    $new_msg = array('user' => $messages[0]['users_LOGIN'], 'message' => $messages[0]['content']);
    if (mb_strlen($new_msg['message']) > 15) {
        $new_msg['message'] = mb_substr($new_msg['message'], 0, 25).'...';
    }
}

// The first value returned is the id of the chatroom
$data = $chatrooms_ID.$special_splitter;

foreach ($messages as $value) { //Loop through messages, so that they are displayed in different format, depending on the message poster
    if ($value['users_LOGIN'] == $_SESSION['s_login']) {
        $span_style = 'color:darkorange;'; //Own messages are displayed in darkorange
    } elseif ($value['users_USER_TYPE'] == 'professor') {
        $span_style = 'color:blue;'; //Professor messages are displayed in blue
    } elseif ($value['users_USER_TYPE'] == 'administrator') {
        $span_style = 'color:red;'; //Administrator messages are displayed in red
    } else {
        $span_style = 'color:green;'; //Other messages are displayed in default format
    }

    date("ymd", time()) == date("ymd", $value['timestamp']) ? $time_str = date("H:i:s", $value['timestamp']) : $time_str = formatTimestamp($value['timestamp'], 'time'); //for today's messages don't display date, only time.
    if ($value['content'][0] != '#' || !preg_match("/^#for_user-(\S*):(\d+)#/", $value['content'], $matches) || ($matches[1] == $_SESSION['s_login'] && $value['content'] = mb_substr($value['content'], mb_strlen($matches[0])))) { //Explanation for this line: The first part, $value['content'][0] != '#' , is a fast check for the special character #. if it does not exist, proceed and display message. Otherwise, check if the character is followed by a specific sequence, of the form: #for_user-<login>#. If so, then display only the message to the current user (where $_SESSION['s_login'] == <login>) and finally delete the special sequence from the beginning of the message (the mb_substr part). otherwise (if it starts with # but is not a special message), display the message.
        // Create links

        // Do it here for each icon
        $value['content'] = eF_convertTextToSmilies($value['content']);
        $value['content'] = eregi_replace("www[.]([^[:space:]]*)([[:alnum:]#?/&=])","http://www.\\1\\2", $value['content']);
        $value['content'] = eregi_replace("([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])","<a href=\"\\1://\\2\\3\" target=\"_blank\" > \\1://\\2\\3 </a> ", $value['content']);
        $data .= $value['users_LOGIN'].$special_splitter.$time_str.$special_splitter.$span_style.$special_splitter.$value['content'].$special_splitter; //Display the message, along with any notification message
    }
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header("Content-type: text/html;charset="._CHARSET);

//$data = eF_convertTextToSmilies($data);
//$data = iconv("UTF-8",_CHARSET,$data);
//echo $data.$special_splitter."new_limit: ".$new_limit."<br>all messages: ".$all_messages."<br>sent: ".$sent."<br>new_limit_flag: ".$new_limit_flag."-|*special_splitter*|-".$rooms_str."-|*special_splitter*|-".$new_limit."-|*special_splitter*|-".$sent;
echo $data;

?>
