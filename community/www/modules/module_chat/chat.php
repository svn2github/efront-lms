<?php

include("../../../libraries/configuration.php");

session_start();

if (!isset($_SESSION['chatter'])){
 exit(1);
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



if (!isset($_SESSION['chatHistory'])) {
 $_SESSION['chatHistory'] = array();
}

if (!isset($_SESSION['openChatBoxes'])) {
 $_SESSION['openChatBoxes'] = array();
}

function logoutFromChat(){
  eF_executeNew("DELETE FROM module_chat_users WHERE username='".$_SESSION['chatter']."'");
}

function loginToChat(){
  eF_executeNew("INSERT IGNORE INTO module_chat_users (username ,timestamp_) VALUES ('".$_SESSION['chatter']."', CURRENT_TIMESTAMP);");
}

function chatHeartbeat() {


 if (!$_SESSION['last_msg']){
  $my_t=getdate();
  $_SESSION['last_msg'] = $my_t[year].'-'.$my_t[mon].'-'.$my_t[mday].' '.$my_t[hours].':'.$my_t[minutes].':'.$my_t[seconds];
 }

 if (!$_SESSION['s_lessons_ID']){
  $sql = "select * from module_chat where (module_chat.to_user = '".mysql_real_escape_string($_SESSION['chatter'])."' AND sent>'".$_SESSION['last_msg']."') order by id ASC";
 }
 else{
  $sql = "select * from module_chat where (module_chat.to_user = '".mysql_real_escape_string($_SESSION['chatter'])."' AND sent>'".$_SESSION['last_msg']."') OR (module_chat.to_user = '".str_replace(' ','_',$_SESSION["lessonname"])."' AND module_chat.from_user != '".$_SESSION['chatter']."' AND sent>'".$_SESSION['last_msg']."') order by id ASC";
 }
 $query = mysql_query($sql);
 $items = '';

 $chatBoxes = array();

 while ($chat = mysql_fetch_array($query)) {

  $_SESSION['last_msg'] = $chat['sent'];
  if (!isset($_SESSION['openChatBoxes'][$chat['from_user']]) && isset($_SESSION['chatHistory'][$chat['from_user']])) {
   $items = $_SESSION['chatHistory'][$chat['from_user']];
  }

  $chat['message'] = sanitize($chat['message']);

  if ($chat['to_user'] == $_SESSION["lessonname"])
   $title = $_SESSION["lessonname"];
  else
   $title = $chat['from_user'];
  $items .= <<<EOD
        {
   "s": "0",
   "t": "{$title}",
   "f": "{$chat['from_user']}",
   "m": "{$chat['message']}"
    },
EOD;

 if (!isset($_SESSION['chatHistory'][$chat['from_user']])) {
  $_SESSION['chatHistory'][$chat['from_user']] = '';
 }

 $_SESSION['chatHistory'][$chat['from_user']] .= <<<EOD
         {
   "s": "0",
   "t": "{$title}",
   "f": "{$chat['from_user']}",
   "m": "{$chat['message']}"
    },
EOD;

  //unset($_SESSION['tsChatBoxes'][$chat['from_user']]);
  $_SESSION['openChatBoxes'][$chat['from_user']] = $chat['sent'];
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
 if (!empty($_SESSION['openChatBoxes'])) {
  foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
   $items .= chatBoxSession($chatbox);
  }
 }
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
 $_SESSION['openChatBoxes'][$_POST['to']] = date('Y-m-d H:i:s', time());
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
  $sql = "insert into module_chat (module_chat.from_user,module_chat.to_user,message,sent,module_chat.isLesson) values ('".mysql_real_escape_string($from)."', '".mysql_real_escape_string($to)."','".mysql_real_escape_string($message)."',NOW(), '0')";
 }
 else{
  $sql = "insert into module_chat (module_chat.from_user,module_chat.to_user,message,sent,module_chat.isLesson) values ('".mysql_real_escape_string($from)."', '".mysql_real_escape_string($to)."','".mysql_real_escape_string($message)."',NOW(), '1')";
 }
 $query = mysql_query($sql);
 echo "1";
 exit(0);
}
function closeChat() {
 unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);
 echo "1";
 exit(0);
}
function sanitize($text) {
 $text = htmlspecialchars($text, ENT_QUOTES);
 $text = str_replace("\n\r","\n",$text);
 $text = str_replace("\r\n","\n",$text);
 $text = str_replace("\n","<br>",$text);
 return $text;
}
?>
