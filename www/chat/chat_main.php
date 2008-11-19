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
* @todo να ρυθμίζει ο ίδιος ο χρήστης το refresh
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

$ctg = 'chat';

//echo "<pre>";print_r($_POST);echo "</pre>";
//error_reporting(E_ALL);

$messages_limit = 400;                                                                 //The messages list limit
$data = '';


if (isset($_GET['chatrooms_ID'])) {                                                     //Valid chat session
    $chatrooms_ID = eF_checkParameter($_GET['chatrooms_ID'], 'uint');
    if ($chatrooms_ID === false) {                                                      //Check if the passed parameter is valid
        eF_printMessage(_WRONGROOMID);
        exit;
    }

    isset($_GET['standalone']) ? $standalone_str = '&standalone=1' : $standalone_str = '';      //Is it the stand-alone version of the window?

    $rooms_str = '<table cellpadding = "1" >';                                          //Display public and private rooms list
print_r($public_rooms);
    $public_rooms  = eF_getTableData("chatrooms, users_to_chatrooms", "distinct chatrooms.id, chatrooms.name, count(*)", "chatrooms.active=1 AND chatrooms.type = 'public' and users_to_chatrooms.chatrooms_ID = chatrooms.id group by id");
    $private_rooms = eF_getTableData("chatrooms, users_to_chatrooms", "distinct chatrooms.id, chatrooms.name, chatrooms.users_LOGIN, count(*)", "chatrooms.active=1 AND chatrooms.type = 'private' and users_to_chatrooms.chatrooms_ID = chatrooms.id group by id");

    for ($i = 0; $i < sizeof($public_rooms); $i++) {                                    //The public rooms string part
        if ($public_rooms[$i]['id'] != $chatrooms_ID) {
            $rooms_str .= '<tr><td><b>['.($i+1).']</b> <a href = "chat/chat_index.php?chatrooms_ID='.$public_rooms[$i]['id'].$standalone_str.'" target = "_parent">'.$public_rooms[$i]['name'].'</a> (<a href = "javascript:void(0)" onclick = "popUp(&quot;chat/chat_room_options.php?show_users='.$public_rooms[$i]['id'].'&quot;, 200, 200)">'.$public_rooms[$i]['count(*)'].'</a>)</td></tr>';
        } else {
            $notice = '<tr><td>'._NOTEPUBLICROOMSARESTOREDANDACCESSED.'.</td></tr>';
            $room_name = $public_rooms[$i]['name'];
            $rooms_str .= '<tr><td><b>['.($i+1).']</b> '.$public_rooms[$i]['name'].' (<a href = "javascript:void(0)" onclick = "popUp(&quot;chat/chat_room_options.php?show_users='.$public_rooms[$i]['id'].'&quot;, 200, 200)">'.$public_rooms[$i]['count(*)'].'</a>)</td></tr>';
        }
    }
    for ($j = 0; $j < sizeof($private_rooms); $j++) {                                   //The private rooms string part
        if ($private_rooms[$j]['id'] != $chatrooms_ID) {
            $rooms_str .= '<tr><td><b>['.($j+1+$i).']</b> <a href = "chat/chat_index.php?chatrooms_ID='.$private_rooms[$j]['id'].$standalone_str.'" target = "_parent">'.$private_rooms[$j]['name'].'</a> (<a href = "javascript:void(0)" onclick = "popUp(&quot;chat/chat_room_options.php?show_users='.$private_rooms[$j]['id'].'&quot;, 200, 200)">'.$private_rooms[$j]['count(*)'].'</a>) *</td></tr>';
        } else {
            $room_name = $private_rooms[$j]['name'];
            $rooms_str .= '<tr><td><b>['.($i+1).']</b> '.$private_rooms[$j]['name'].' (<a href = "javascript:void(0)" onclick = "popUp(&quot;chat/chat_room_options.php?show_users='.$private_rooms[$j]['id'].'&quot;, 200, 200)">'.$private_rooms[$j]['count(*)'].'</a>) * </td></tr>';
        }
    }    

    
    if (sizeof($private_rooms) > 0) {
        $rooms_str .= '<table><tr><td>*: '._PRIVATEROOM.'</td></tr></table>';                                                                              //Display footnote
    } 
    
    $rooms_str .= '</table>';

    $users_str = array();                                                               //Build the users list. Professors are displayed in blue, and administrators in red
    $users     = eF_getTableDataFlat("users_to_chatrooms", "users_LOGIN, users_USER_TYPE, timestamp", "chatrooms_ID=".$chatrooms_ID);
    for ($i = 0; isset($users['users_LOGIN']) && $i < sizeof($users['users_LOGIN']); $i++) {                            //Check each user type, to display in different color
        switch ($users['users_USER_TYPE'][$i]) {
            case 'professor':                                                           //Professor: blue bold
                $style_str = 'font-weight:bold;color:blue';
                break;
            case 'administrator':
                $style_str = 'font-weight:bold;color:red';                              //Administrator: red bold
                break;
            default:
                $style_str = '';                                                        //Everybody else: default
                break;
        }

        if ($users['users_LOGIN'][$i] != $_SESSION['s_login']) {
            $users_str[] = '<a href = "javascript:void(0)" onclick = "popUp(&quot;chat/chat_index.php?chatrooms_ID='.$chatrooms_ID.'&invite='.$users['users_LOGIN'][$i].'&standalone=1&quot;, 600, 400)">'.$users['users_LOGIN'][$i].'</a>';                                   //Make the strings an array, so we may then implode() it with commas
        } else {                                                                        //Check if we are logged in the chat
            $offset = $i;
            $enter_time = $users['timestamp'][$i];                                      //Keep the entrance time so we display only messages after this time
        }
    }

    if (sizeof($users_str) > 0) {
        $users_str = '<table cellpadding = "1"><tr><td>'.implode(", ", $users_str).'</td></tr></table>';                                                              //The users list string
    } else {
        $users_str = '<table><tr><td class = "empty_category">'._THEREARENOOTHERUSERSRIGHTNOWINTHISROOM.'</td></tr></table>';                                                              //The users list string
    }
     
        
    $system_message = '';                                                               //New user entrance message: string initialization
    if (!isset($offset)) {                                                              //Means it is a new user, so we add him to the users list
        if (!isset($_SESSION['last_message'])) {
            $_SESSION['last_message'] = array();
        }
        $enter_time    = time();
        $fields_insert = array('users_LOGIN'     => $_SESSION['s_login'],
                               'chatrooms_ID'    => $chatrooms_ID,
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'timestamp'       => $enter_time);                               
        eF_insertTableData("users_to_chatrooms", $fields_insert);                       //Insert new user to database                               
        $system_message = '<span style = "color: purple;"><i>'._THEUSER.' '.$_SESSION['s_login'].' '._ENTEREDROOM.' </i><b>('._SYSTEMMESSAGE.')</b></span><br/>'; //Build the entrance notification message
        $fields_insert = array('users_LOGIN'     => $_SESSION['s_login'],
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'content'         => $system_message,
                               'timestamp'       => $enter_time,
                               'chatrooms_ID'    => $chatrooms_ID);        
        eF_insertTableData("chatmessages", $fields_insert);                             //Insert the system message into the database
    } 
    
    if (isset($_POST['submit']) || isset($_POST['chat_message'])) {                                         //The user posted a message. IF the user just pressed enter, in IE POST['submit'] is not set!!!!!, so we inclde the || clause...
        $fields_insert = array('users_LOGIN'     => $_SESSION['s_login'],
                               'users_USER_TYPE' => $_SESSION['s_type'],
                               'content'         => htmlspecialchars($_POST['chat_message'], ENT_QUOTES),
                               'timestamp'       => time(),
                               'chatrooms_ID'    => $_POST['hidden_chat_room_id']);        
        //echo $fields_insert['content']."---";
        eF_insertTableData("chatmessages", $fields_insert);                             //Insert the message into the database
        print '
                <script>
                <!--
                    parent.document.chat_form.chat_message.value = "";                  //Clear the contents of the field
                //-->
               </script>';
    }
            
        
   $messages = eF_getTableData("chatmessages", "users_LOGIN, users_USER_TYPE, timestamp, content", "timestamp > $enter_time AND chatrooms_ID = $chatrooms_ID ", "timestamp DESC LIMIT $messages_limit");     //Retrieve the recent messages

    $new_msg  = false;
    if ($_SESSION['last_message'][$chatrooms_ID] != $messages[0]['timestamp']) {
        $_SESSION['last_message'][$chatrooms_ID] = $messages[0]['timestamp'];
        $new_msg = array('user' => $messages[0]['users_LOGIN'], 'message' => $messages[0]['content']);
        if (mb_strlen($new_msg['message']) > 15) {
            $new_msg['message'] = mb_substr($new_msg['message'], 0, 25).'...';
        }
    }


    //isset($_GET['standalone']) ? $font_size = 'font-size:10px;' : $font_size = 'font-size:14px;';
    $font_size = 'font-size:11px;';
    
    foreach ($messages as $value) {                                                     //Loop through messages, so that they are displayed in different format, depending on the message poster
        if ($value['users_LOGIN'] == $_SESSION['s_login']) {                        
            $span_style = 'color:darkorange;';                                           //Own messages are displayed in darkorange
        } elseif ($value['users_USER_TYPE'] == 'professor') {
            $span_style = 'color:blue;';                                                 //Professor messages are displayed in blue
        } elseif ($value['users_USER_TYPE'] == 'administrator') {
            $span_style = 'color:red;';                                                  //Administrator messages are displayed in red
        } else {
            $span_style = '';                                                            //Other messages are displayed in default format
        }
        

        date("ymd", time()) == date("ymd", $value['timestamp']) ? $time_str = date("H:i:s", $value['timestamp']) : $time_str = formatTimestamp($value['timestamp'], 'time');       //for today's messages don't display date, only time.
        if ($value['content'][0] != '#' || !preg_match("/^#for_user-(\S*):(\d+)#/", $value['content'], $matches) || ($matches[1] == $_SESSION['s_login'] && $value['content'] = mb_substr($value['content'], mb_strlen($matches[0])))) {     //Explanation for this line: The first part, $value['content'][0] != '#' , is a fast check for the special character #. if it does not exist, proceed and display message. Otherwise, check if the character is followed by a specific sequence, of the form: #for_user-<login>#. If so, then display only the message to the current user (where $_SESSION['s_login'] == <login>) and finally delete the special sequence from the beginning of the message (the mb_substr part). otherwise (if it starts with # but is not a special message), display the message.
            $data .= '<span style="'.$span_style.$font_size.'">'.$time_str.' '.$value['users_LOGIN'].': '.$value['content'].'</span><br/>';     //Display the message, along with any notification message
        }
    }
} else {
    header('location:chat/chat_index.php');                                                        //If no room was selected, redirect to the chat main page.
}

$data = eF_convertTextToSmilies($data);

eF_printHeader();
print '<META HTTP-EQUIV="expires" CONTENT="Wed, 26 Feb 1997 08:21:57 GMT">';
print "
    <script>

    <!--
        var obj = parent.test.document.getElementById('chat_content');
        obj.innerHTML = '';

        obj = parent.document.getElementById('rooms_list');
        obj.innerHTML = '".$rooms_str."';
        obj = parent.document.getElementById('users_list');
        obj.innerHTML = '".$users_str."';
        obj = parent.document.getElementById('notice');
        obj.innerHTML = '".$notice."';
    //-->
    </script>
";

if (isset($_GET['standalone'])) {
    if ($new_msg) {
        print "
        <script>
        <!--
            obj = parent.opener.document.getElementById('new_chat_message');
            if (obj) {
                obj.innerHTML = '<img src = \"images/24x24/message.png\" /><br />"._USER." ".$new_msg['user']." says: <br /><i>".$new_msg['message']."</i>';
            }
        //-->
        </script>";
    } else {
        print "
        <script>
        <!--
            obj = parent.opener.document.getElementById('new_chat_message');
            if (obj) {
                obj.innerHTML = '';
            }
        //-->
        </script>";
    }
}
?>