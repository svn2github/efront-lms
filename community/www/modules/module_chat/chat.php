<?php
session_cache_limiter('none'); //Initialize session
session_start();

$path = "../../../libraries/"; //Define default path

/** The configuration file.*/
require_once $path."configuration.php";

//Set headers in order to eliminate browser cache (especially IE's)
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past



if (!isset($_SESSION['chatter'])){
 exit(1);
}

if (!isset($_SESSION['chatboxesnum']))
 $_SESSION['chatboxesnum'] = 0;

if (!isset($_SESSION['chatHistory'])) {
 $_SESSION['chatHistory'] = array();
}

if (!isset($_SESSION['openChatBoxes'])) {
 $_SESSION['openChatBoxes'] = array();
}

global $dbh;
$dbh = mysql_connect(G_DBHOST,G_DBUSER,G_DBPASSWD) or die('Could not connect to mysql server.' );
mysql_selectdb(G_DBNAME,$dbh);

if ($_GET['action'] == "chatheartbeat") { chatHeartbeat(); }
if ($_GET['action'] == "sendchat") { sendChat(); }
if ($_GET['action'] == "closechat") { closeChat(); }
if ($_GET['action'] == "startchatsession") { startChatSession(); }
if ($_GET['action'] == "logoutfromchat") { logoutFromChat(); }
if ($_GET['action'] == "logintochat") { loginToChat(); }
if ($_GET['action'] == "getchatheartbeat") { getChatHeartbeat(); }
if ($_GET['action'] == "getrefreshrate") { getRefresh_rate(); }


function getChatHeartbeat(){

 $rate = eF_getTableData("module_chat_config", "chatHeartbeatTime", "1");
 foreach( $rate as $r ){
  echo($r['chatHeartbeatTime']);
 }
}

function getRefresh_rate(){

 $rate = eF_getTableData("module_chat_config", "refresh_rate", "1");
 foreach( $rate as $r ){
  echo $r['refresh_rate'];
 }
}


function logoutFromChat(){
  eF_executeNew("DELETE FROM module_chat_users WHERE username='".$_SESSION['chatter']."'");
}

function loginToChat(){
  eF_executeNew("INSERT IGNORE INTO module_chat_users (username ,timestamp_) VALUES ('".$_SESSION['chatter']."', CURRENT_TIMESTAMP);");
}

function chatHeartbeat() {


 if (!$_SESSION['last_msg']){
  //$my_t=getdate();
  //$_SESSION['last_msg'] = $my_t[year].'-'.$my_t[mon].'-'.$my_t[mday].' '.$my_t[hours].':'.$my_t[minutes].':'.$my_t[seconds];
  $_SESSION['last_msg'] = date("Y-m-d H:i:s", time()-date("Z")); //fix for timezone differences
 }

 if (!$_SESSION['last_lesson_msg']){
  //$my_t=getdate();
  //$_SESSION['last_lesson_msg'] = $my_t[year].'-'.$my_t[mon].'-'.$my_t[mday].' '.$my_t[hours].':'.$my_t[minutes].':'.$my_t[seconds];
  $_SESSION['last_lesson_msg'] = date("Y-m-d H:i:s", time()-date("Z")); //fix for timezone differences
 }

 $lesson_rooms = join("','",$_SESSION['lesson_rooms']);

 if (!$_SESSION['s_lessons_ID']){
  $sql = "select * from module_chat where (module_chat.to_user = '".mysql_real_escape_string($_SESSION['chatter'])."' AND sent>'".$_SESSION['last_msg']."') order by id ASC";
 }
 else{
  $sql = "select * from module_chat where (module_chat.to_user = '".mysql_real_escape_string($_SESSION['chatter'])."' AND sent>'".$_SESSION['last_msg']."') OR (module_chat.to_user IN ('$lesson_rooms') AND module_chat.from_user != '".$_SESSION['chatter']."' AND sent>'".$_SESSION['last_lesson_msg']."') order by id ASC";
 }
 $query = mysql_query($sql);
 $items = '';

 $chatBoxes = array();

 while ($chat = mysql_fetch_array($query)) {

  if (in_array($chat['to_user'],$_SESSION['lesson_rooms']))
   $title = $chat['to_user'];
  else
   $title = $chat['from_user'];

  $_SESSION['last_msg'] = $chat['sent'];
  $_SESSION['last_lesson_msg'] = $chat['sent'];
  if (!isset($_SESSION['openChatBoxes'][$title]) && isset($_SESSION['chatHistory'][$title])) {
   $items = $_SESSION['chatHistory'][$title];
  }

  $chat['message'] = sanitize($chat['message']);



  $items .= <<<EOD
        {
   "s": "0",
   "t": "{$title}",
   "f": "{$chat['from_user']}",
   "m": "{$chat['message']}"
    },
EOD;

 if (!isset($_SESSION['chatHistory'][$title])) {
  $_SESSION['chatHistory'][$title] = '';
 }


 //if ($title == $chat['from_user']){ // Maybe add else with "t": {$title} -> "t": {$chat[from_user]}

   $_SESSION['chatHistory'][$title] .= <<<EOD
         {
   "s": "0",
   "t": "{$title}",
   "f": "{$chat['from_user']}",
   "m": "{$chat['message']}"
    },
EOD;
 //}

  //unset($_SESSION['tsChatBoxes'][$chat['from_user']]);
  if (!isset( $_SESSION['openChatBoxes'][$title] )){
   $_SESSION['openChatBoxes'][$title] = $_SESSION['chatboxesnum'];
   $_SESSION['chatboxesnum'] = $_SESSION['chatboxesnum'] + 10;
  }
 }

 /*if (!empty($_SESSION['openChatBoxes'])) {

	foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {

		if (!isset($_SESSION['tsChatBoxes'][$chatbox])) {

			$now = time()-strtotime($time);

			$time = date('g:iA M dS', strtotime($time));



			$message = "Sent at $time";

			if ($now > 5) {

				$items .= <<<EOD

{

"s": "2",

"t": "{$title}",

"f": "$chatbox",

"m": "{$message}"

},

EOD;



	if (!isset($_SESSION['chatHistory'][$chatbox])) {

		$_SESSION['chatHistory'][$chatbox] = '';

	}



	$_SESSION['chatHistory'][$chatbox] .= <<<EOD

		{

"s": "2",

"t": "{$title}",

"f": "$chatbox",

"m": "{$message}"

},

EOD;

			$_SESSION['tsChatBoxes'][$chatbox] = 1;

		}

		}

	}

}

*/
 //$sql = "update module_chat set recd = 1 where module_chat.to_user = '".mysql_real_escape_string($_SESSION['chatter'])."' and recd = 0";
 //$query = mysql_query($sql);
 if ($items != '') {
  $items = substr($items, 0, -1);
 }
header('Content-type: application/json');
?>
{
  "items": [
   <?php echo $items;?>
        ]
}
<?php
   exit(0);
}
function chatBoxSession($chatbox) {
 $items = '';
 if (isset($_SESSION['chatHistory'][$chatbox])) {
  $items = $_SESSION['chatHistory'][$chatbox];
 }
 return $items;
}
function startChatSession() {
 $items = '';
 asort($_SESSION['openChatBoxes']);
 if (!empty($_SESSION['openChatBoxes'])) {
  foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
   $items .= chatBoxSession($chatbox);
  }
 }
 //asort($_SESSION['openChatBoxes']);
 if ($items != '') {
  $items = substr($items, 0, -1);
 }
header('Content-type: application/json');
?>
{
  "username": "<?php echo $_SESSION['chatter'];?>",
  "items": [
   <?php echo $items;?>
        ]
}
<?php
 exit(0);
}
function sendChat() {
 $from = $_SESSION['chatter'];
 $to = $_POST['to'];
 $message = $_POST['message'];
 if ( !isset($_SESSION['openChatBoxes'][$_POST['to']])){
  $_SESSION['openChatBoxes'][$_POST['to']] = $_SESSION['chatboxesnum'];
  $_SESSION['chatboxesnum'] = $_SESSION['chatboxesnum'] + 10;
 }
 $messagesan = sanitize($message);
 if (!isset($_SESSION['chatHistory'][$_POST['to']])) {
  $_SESSION['chatHistory'][$_POST['to']] = '';
 }
 $_SESSION['chatHistory'][$_POST['to']] .= <<<EOD
        {
   "s": "1",
   "t": "{$to}",
   "f": "{$to}",
   "m": "{$messagesan}"
    },
EOD;
 //unset($_SESSION['tsChatBoxes'][$_POST['to']]);
 if ($to != $_SESSION['lessonname']){
  $sql = "insert into module_chat (module_chat.from_user,module_chat.to_user,message,sent,module_chat.isLesson) values ('".mysql_real_escape_string($from)."', '".mysql_real_escape_string($to)."','".mysql_real_escape_string($message)."','".date("Y-m-d H:i:s", time()-date("Z"))."', '0')";
 }
 else{
  $sql = "insert into module_chat (module_chat.from_user,module_chat.to_user,message,sent,module_chat.isLesson) values ('".mysql_real_escape_string($from)."', '".mysql_real_escape_string($to)."','".mysql_real_escape_string($message)."','".date("Y-m-d H:i:s", time()-date("Z"))."', '1')";
 }
 $query = mysql_query($sql);
 echo $_SESSION['chatboxesnum'];
 exit(0);
}
function closeChat() {
 unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);
 if (str_replace(' ','_',$_POST['chatbox']) != $_SESSION["lessonname"] && in_array(str_replace(' ','_',$_POST['chatbox']),$_SESSION['lesson_rooms']))
  $_SESSION['lesson_rooms'] = remove_item_by_value($_SESSION['lesson_rooms'], str_replace(' ','_',$_POST['chatbox']));
 echo $_POST['chatbox'];
 exit(0);
}
function remove_item_by_value($array, $val = '') {
 if (empty($array) || !is_array($array)) return false;
 if (!in_array($val, $array)) return $array;
 foreach($array as $key => $value) {
  if ($value == $val) unset($array[$key]);
 }
 return array_values($array);
}

function sanitize($text) {
 $text = htmlspecialchars($text, ENT_QUOTES);
 $text = str_replace("\n\r","\n",$text);
 $text = str_replace("\r\n","\n",$text);
 $text = str_replace("\n","<br>",$text);
 return $text;
}
?>
