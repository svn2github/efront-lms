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

session_cache_limiter('none');
session_start();

$path = "../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

if (!eF_checkUser($_SESSION['s_login'],  $_SESSION['s_password'])) {                   //Only a valid user may access this page
    exit;
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-type: text/xml");


$ie = isset($_GET['ie']) ? true : false ;

$messages_num = eF_getUnreadMessagesNumber();

$user = EfrontUserFactory :: factory($_SESSION['s_login']);

$user -> refreshLoggin(); 
$users_online = EfrontUser :: getUsersOnline();

$times = eF_getTableData("users_online", "users_LOGIN, timestamp", "", "" ,"users_LOGIN");


foreach($times as $key => $value) {
    $time_online = eF_convertIntervalToTime(time()-$times[$key]['timestamp']);
    foreach($users_online as $inner_key => $inner_value) {
        if($inner_value['login'] == $value['users_LOGIN']) {
            $users_online[$inner_key]['hours'] = $time_online['hours'];
            $users_online[$inner_key]['minutes'] = $time_online['minutes'];
        }
    }
}

    isset($params['align']) ? $align = $params['align'] : $align = 'left';

    $str = '
        <table border = "0" width = "100%">
            <tr><td align = '.$align.'>';
    for ($i = 0; $i < sizeof($users_online); $i++) {
        //$params['data'][$i]['type'] == 'professor' ? $style = "font-weight:bold;" : $style = '';
        $i > 0 && $i < sizeof($users_online) ? $comma = ', ' : $comma = '';

    $timestr = '';

    if($users_online[$i]['hours']!=0 && $users_online[$i]['minutes']!=0) {
        $timestr = _USERISONLINE.": ".$users_online[$i]['hours']." "._HOURS." "._AND." ".$users_online[$i]['minutes']." "._MINUTES;
    } else if($users_online[$i]['hours']!=0 && $users_online[$i]['minutes']==0) {
        $timestr = _USERISONLINE.": ".$users_online[$i]['hours']." "._HOURS;
    } else if($users_online[$i]['hours']==0 && $users_online[$i]['minutes']!=0) {
        $timestr = _USERISONLINE.": ".$users_online[$i]['minutes']." "._MINUTES;
    } else {
        $timestr = _USERJUSTLOGGEDIN;
    }


        //$str .= $comma.'#filter:user_login-'.$users_online[$i]['login'].'#';
    //$str .= $comma. "<a href = 'javascript:void(0)' onclick = \"show_user_box('"._USER." ".$users_online[$i]['login']."','".$users_online[$i]['login']."','"._SENDMESSAGE."','"._WEB."','".$users_online[$i]['type']."','".$timestr."')\">";
    $str .= $comma. "<a href = 'javascript:void(0)' alt=\"".$users_online[$i]['name'] ." ". $users_online[$i]['surname'] ."\" title=\"".$users_online[$i]['name'] ." ".$users_online[$i]['surname'] ."\" onclick = \"eF_js_showDivPopup('', new Array('300px', '100px'), 'user_table');show_user_box('"._USER." ".$users_online[$i]['login']."','".$users_online[$i]['login']."','"._SENDMESSAGE."','"._WEB."','".$users_online[$i]['type']."','".$timestr."');\">";

  switch($users_online[$i]['type']){
    case "administrator":
        $str .= "<span style=\"color:red\">".$users_online[$i]['login']. "</a>";
        break;
    case "professor":
        $str .= "<span style=\"color:green\">".$users_online[$i]['login']. "</a>";
        break;
    case "student":
        $str .= "<span style=\"color:black\">".$users_online[$i]['login']. "</a>";
        break;

}
    }

    if (sizeof($users_online) == 0) {
        $str .= '<tr><td class = "empty_category">'._NOOTHERUSERSONLINE.'</td></tr>';
    }

        $str .= '
            </td></tr>
        </table>';

$find_array = array('<','>');
$replace_array = array('&lt;','&gt;');
$strf = str_replace($find_array,$replace_array,$str);


echo "<?xml version=\"1.0\" ?>";
echo "<root>";
echo "<users_num>";
echo sizeof($users_online);
echo "</users_num>";
echo "<users>";
//if($ie)
//    echo iconv("ISO-8859-7","UTF-8",$strf);
//else
    echo $strf;
echo "</users>";
echo "<number>";
echo $messages_num;
echo "</number>";
echo "<text>";
if($messages_num==0)
{
    $str = "&lt;span style='visibility:hidden'&gt;&lt;/span&gt;";
    //if($ie)
    //    echo iconv("ISO-8859-7","UTF-8",$str);
    //else
        echo $str;
}
else if($messages_num==1)
{
    $str = "&lt;a href='forum/messages_index.php' target = 'mainframe'&gt;"._YOUHAVE." ".$messages_num." ".mb_strtolower(_NEWMESSAGE)."&lt;/a&gt;";
    //if($ie)
    //    echo iconv("ISO-8859-7","UTF-8",$str);
    //else
        echo $str;
}
else
{
    $str = "&lt;a href='forum/messages_index.php' target = 'mainframe'&gt;"._YOUHAVE." ".$messages_num." ".mb_strtolower(_NEWMESSAGES)."&lt;/a&gt;";
    //if($ie)
    //    echo iconv("ISO-8859-7","UTF-8",$str);
    //else
        echo $str;
}
echo "</text>";
echo "</root>";
exit;

//echo $messages_num."-|*special_splitter*|-";
//if($messages_num==0)
//  echo "";
//else if($messages_num==1)
//{
//  $str = "<a href='forum/messages_index.php'>"._YOUHAVE." ".$messages_num." "._NEWMESSAGE."</a>";
//  //echo iconv("ISO_8859-7","UTF-8",$str);
//  echo $str;
//}
//else
//{
//  $str = "<a href='forum/messages_index.php'>"._YOUHAVE." ".$messages_num." "._NEWMESSAGES."</a>";
//  //echo iconv("ISO_8859-7","UTF-8",$str);
//  echo $str;
//}
?>
