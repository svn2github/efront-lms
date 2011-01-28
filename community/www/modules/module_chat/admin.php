<?php

include("../../../libraries/configuration.php");

session_start();
global $dbh;
$dbh = mysql_connect(G_DBHOST,G_DBUSER,G_DBPASSWD) or die('Could not connect to mysql server.' );
mysql_selectdb(G_DBNAME,$dbh);

if ($_GET['force'] == "createLogs") { createLogs(); }
if ($_GET['force'] == "createLessonHistory") { createLessonHistory(); }
if ($_GET['force'] == "done") { done(); }
if ($_GET['force'] == "clearU2ULogs") { clearU2ULogs(); }
if ($_GET['force'] == "getChatHeartbeat") { getChatHeartbeat(); }
if ($_GET['force'] == "getRefresh_rate") { getRefresh_rate(); }
if ($_GET['force'] == "setChatheartBeat") { setChatheartBeat(); }
if ($_GET['force'] == "setRefresh_rate") { setRefresh_rate(); }



function createLogs(){

 $lsn = eF_getTableData("lessons", "name", "1");

 $lessons = array();

 foreach ($lsn as $lesson){
  //echo ("<tr><td><input type=\"radio\" name=\"lesson\" value=\"".str_replace(' ','_',$lesson['name'])."\">".$lesson['name']."</tr></td>");
  $lessons[] = $lesson;
 }
 return $lessons;
}


function createLessonHistory(){
 $sql = "select * from module_chat where (module_chat.to_user = '".$_POST['lesson']."') order by id ASC";
 $query = mysql_query($sql);

 $data = array();
 $i = 0;
 while ($chat = mysql_fetch_array($query)) {
  array_push($data, array('col1' => $chat["from_user"], 'col2' => $chat["message"], 'col3' =>$chat["sent"]));
 }

 $columns = array(
  'col1' => 'SENDER',
  'col2' => 'MESSAGE',
  'col3' => 'SENT TIME'
 );

 $csv = build($columns,$data);
 save($csv,$_POST['lesson']."-".date('Y-m-d'));
}
//////////////////////////////////////////////////////////////////////////////
function clearU2ULogs(){

 $sql = "DELETE FROM module_chat WHERE isLesson='0'";
 $result = mysql_query($sql);

 if (!$result) {
     die('Error executing Clear Log query: ' . mysql_error());
 }
 else{
  echo ("CHAT HISTORY SUCCESFULLY DELETED");
 }
}
///////////////////////////////////////////////////////////////////////////////

function getChatHeartbeat(){

 $rate = eF_getTableData("module_chat_config", "chatHeartbeatTime", "1");
 foreach( $rate as $r ){
  echo($r['chatHeartbeatTime']);
 }
}
///////////////////////////////////////////////////////////////////////////////

function getStatus(){

 $doc = new DOMDocument();
 $doc->load( 'config.xml' );

 $chat_system= $doc->getElementsByTagName( "chat_system" );
 foreach( $chat_system as $x )
 {
  $time = $x->getElementsByTagName( "status" );
  $time = $time->item(0)->nodeValue;

  echo "$time";
 }
}
///////////////////////////////////////////////////////////////////////////////

function getRefresh_rate(){

 /*$doc = new DOMDocument();

	$doc->load( 'config.xml' );

	

	$chat_system= $doc->getElementsByTagName( "chat_system" );

	foreach( $chat_system as $x )

	{

		$time = $x->getElementsByTagName( "refresh_rate" );

		$time = $time->item(0)->nodeValue;



		echo "$time";

	}*/
 $rate = eF_getTableData("module_chat_config", "refresh_rate", "1");
 foreach( $rate as $r ){
  echo $r['refresh_rate'];
 }
}
////////////////////////////////////////////////////////////////////////////////
