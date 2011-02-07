<?php

include("../../../libraries/configuration.php");

session_start();
global $dbh;
$dbh = mysql_connect(G_DBHOST,G_DBUSER,G_DBPASSWD) or die('Could not connect to mysql server.' );
mysql_selectdb(G_DBNAME,$dbh);

if ($_GET['force'] == "clearU2ULogs") { clearU2ULogs(); }
if ($_GET['force'] == "getlessons") { getLessonsCatalogue(); }

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
function getLessonsCatalogue(){
 $lsn = eF_getTableData("lessons", "name", "1");

  $str = '<ul>';
  foreach ($lsn as $lesson){
   $str = $str.'<li id='.$lesson'>'.$lesson.'</li>';
  }
  $str = '</ul>';
  echo $str;
}
