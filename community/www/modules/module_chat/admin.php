<?php

include("../../../libraries/configuration.php");

session_start();
global $dbh;
$dbh = mysql_connect(G_DBHOST,G_DBUSER,G_DBPASSWD) or die('Could not connect to mysql server.' );
mysql_selectdb(G_DBNAME,$dbh);

if ($_GET['force'] == "clearU2ULogs") { clearU2ULogs(); }
if ($_GET['force'] == "clearAllLogs") { clearAllLogs(); }
if ($_GET['force'] == "getLessonFromId") { getLessonFromId(); }


/////////////////////////////////////////////////////////////////////////////
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
function clearAllLogs(){

 $sql = "DELETE FROM module_chat WHERE 1";
 $result = mysql_query($sql);

 if (!$result) {
     die('Error executing Clear Log query: ' . mysql_error());
 }
 else{
  echo ("CHAT HISTORY SUCCESFULLY DELETED");
 }
}
//////////////////////////////////////////////////////////////////////////////
function getLessonFromId(){

 $id = $_GET["loglessonid"];
 $sql = "SELECT name FROM lessons WHERE id='".$id."'";
 $result = mysql_query($sql);

 while ($lesson = mysql_fetch_array($result)){
  echo $lesson["name"];
 }
}

///////////////////////////////////////////////////////////////////////////////
