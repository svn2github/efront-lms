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


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
function build($columns, $data){
 $csv = ""; // initialise csv variable

  foreach($columns as $heading) // csv column headings
  {
   $csv .= $heading."\t"; // concat heading onto row
  }
  $csv .= "\n"; // all the headings have been added so move to new line for csv content

  foreach($data as $row) // csv table content
  {
   foreach($columns as $column => $t)
   {
    if(strpos($row[$column],',')) // if cell content has a comma in it...
    {
     // ...double any existing quotes to escape them...
     $row[$column] = str_replace('"','""',$row[$column]);
     // ...and wrap the cell in quotes so the comma doesn't break everything.
     $row[$column] = '"'.$row[$column].'"';
    }
    $csv .= $row[$column]."\t"; // concat the value onto the row
    if($t==end($columns))
    {
     // if we're at the end of a row move to a new line for next row
     $csv .= "\n";
    }
   }
  }
  $csv = iconv('utf-8','greek',$csv);
  return $csv;
}
////////////////////////////////////////////////////////////////////////
function save($csv,$file_name=null){
 // if no file name is provided set the file name to todays date
 if(is_null($file_name)) $file_name = date('Y-m-d');
 // set content type and file name then output csv content
 header("Content-type: application/vnd.ms-excel; charset: utf-8");
header("Content-Disposition: attachment; filename=$file_name");
header ('Content-Transfer-Encoding: binary');
header ('Content-Length: '.filesize('product1.xls'));
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header ('Cache-Control: cache, must-revalidate');
header ('Pragma: public');
 echo $csv;
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

 $doc = new DOMDocument();
 $doc->load( 'config.xml' );

 $chat_system= $doc->getElementsByTagName( "chat_system" );
 foreach( $chat_system as $x )
 {
  $time = $x->getElementsByTagName( "chatHeartbeatTime" );
  $time = $time->item(0)->nodeValue;

  echo "$time";
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

 $doc = new DOMDocument();
 $doc->load( 'config.xml' );

 $chat_system= $doc->getElementsByTagName( "chat_system" );
 foreach( $chat_system as $x )
 {
  $time = $x->getElementsByTagName( "refresh_rate" );
  $time = $time->item(0)->nodeValue;

  echo "$time";
 }
}

////////////////////////////////////////////////////////////////////////////////
function setChatheartBeat(){

 $doc = new DOMDocument();
 $doc->load( 'config.xml' );

 $chat_system= $doc->getElementsByTagName( "chat_system" );
 foreach( $chat_system as $x )
 {
  $time = $x->getElementsByTagName( "refresh_rate" );
  $t = $time->item(0)->nodeValue;

  $status = $x->getElementsByTagName( "status" );
  $s = $status->item(0)->nodeValue;
  echo "$t $s";
 }

 $xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<chat_system>
<status>".$s."</status>
<chatHeartbeatTime>".$_GET['t']."</chatHeartbeatTime>
<refresh_rate>".$t."</refresh_rate>
</chat_system>";


 $file = fopen("config.xml", "w");
 fwrite($file, $xml);
 fclose($file);
}
/////////////////////////////////////////////////////////////////////////////
function setRefresh_rate(){

 $doc = new DOMDocument();
 $doc->load( 'config.xml' );

 $chat_system= $doc->getElementsByTagName( "chat_system" );
 foreach( $chat_system as $x )
 {
  $time = $x->getElementsByTagName( "chatHeartbeatTime" );
  $t = $time->item(0)->nodeValue;

  $status = $x->getElementsByTagName( "status" );
  $s = $status->item(0)->nodeValue;
  echo "$t $s";
 }

 $xml = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<chat_system>
<status>".$s."</status>
<chatHeartbeatTime>".$t."</chatHeartbeatTime>
<refresh_rate>".$_GET['t']."</refresh_rate>
</chat_system>";


 $file = fopen("config.xml", "w");
 fwrite($file, $xml);
 fclose($file);
}
