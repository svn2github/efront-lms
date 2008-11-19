<?php
/**
* Page for registration of LMS SCORM data
* 
* This page is used to store commited data from a SCORM page to the LMS
* @package eFront
* @version 1.0
*/

//General initialization and parameters
session_cache_limiter('none');
session_start();

$path = "../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

error_reporting(E_ALL);
echo "<pre>";print_r($_POST);print_r($_GET);
$db->debug=true;
if (!eF_checkUser($_SESSION['s_login'], $_SESSION['s_password'])) {                        //Only avalid user may access this page
    header("location:index.php");
    exit;
}

//print '<META HTTP-EQUIV = "refresh" CONTENT = "2;URL='.basename($_SERVER['PHP_SELF']).'?close=true"/>';

if (isset($_POST['close'])) {                                                              //Close the window
    print '<SCRIPT LANGUAGE="JavaScript">
                //self.opener.location.reload(); 
                window.close();
           </script>';
}

if (isset($_POST['credit']) && $_POST['credit'] == 'no-credit') {                          //The lesson may not be credited, for example the professor deals with it
    echo "system message: <i>LESSON NOT CREDITED</i><br/>";
    exit;
}

//These are values that are present to the POST data, but not needed; so unset them.
unset ($_POST['credit']);
unset ($_POST['session_time']);
unset ($_POST['id']);

$fields['timestamp']  = time();
foreach ($_POST as $key => $value) {                                                       //Store POST parameters in a variable, so that they may be inserted in a database table
    $fields[$key] = $value;
}

$fields['users_LOGIN'] = $_SESSION['s_login'];                                             //The current user
if (!isset($fields['content_ID'])) {
    exit;
}

$result = eF_getTableData("scorm_data", "total_time,id", "content_ID=".$fields['content_ID']." AND users_LOGIN='".$fields['users_LOGIN']."'");      //Βρες αν είναι η πρώτη φορά που κάνει το SCO κι αν όχι, κάνε τις απαραίτητες ενέργειες (π.χ. άθροιση συνολικής ώρας --> αν και δεν ξέρω αν είναι πραγματικά απαραίτητο, το πρωτόκολλο το αφήνει αδιευκρίνηστο)
if (sizeof($result) > 0) {                                                      //This means that the students re-enters the unit
    if (isset($fields['total_time']) && isset($result[0]['total_time'])) {      //Make sure that time is properly converted, for example 35+35 minutes become 1 hour 10 minutes, instead if 70 minutes
        $time_parts1 = explode(":", $fields['total_time']);
        $time_parts2 = explode(":", $result[0]['total_time']);
        $time_parts[0] = $time_parts1[0] + $time_parts2[0];
        $time_parts[1] = $time_parts1[1] + $time_parts2[1];
        $time_parts[2] = $time_parts1[2] + $time_parts2[2];
//print_r($time_parts1);print_r($time_parts2);print_r($time_parts);
        $time_parts[1] = $time_parts[1] + floor($time_parts[2]/60);
        $time_parts[2] = fmod($time_parts[2], 60);
        $time_parts[0] = $time_parts[0] + floor($time_parts[1]/60);
        $time_parts[1] = fmod($time_parts[1], 60);

        $fields['total_time'] = sprintf("%04d",$time_parts[0]).":".sprintf("%02d",$time_parts[1]).":".sprintf("%05.2f",$time_parts[2]);            //Το φέρνουμε στην επιθυμητή μορφή, HHHH:MM:SS.SS
    }
    
    eF_updateTableData("scorm_data", $fields, "id=".$result[0]['id']);        //Update old values with new ones
} else {    
    $result = eF_insertTableData("scorm_data", $fields);                      //Insert a new entry that relates the current user with this SCO
}

if (strtolower($fields['lesson_status']) == 'passed' || strtolower($fields['lesson_status']) == 'completed') {
    $result = eF_getTableData("users_to_lessons", "done_content", "users_LOGIN='".$_SESSION['s_login']."' and lessons_ID=".$_SESSION['s_lessons_ID']);
    sizeof($result) > 0 ? $done_content = unserialize($result[0]['done_content']) : $done_content = array();

    $done_content[$fields['content_ID']] = $fields['content_ID'];
    $result[0]['done_content']           = serialize($done_content);
    $result[0]['current_unit']           = $fields['content_ID'];

    eF_updateTableData("users_to_lessons", $result[0], "users_LOGIN='".$_SESSION['s_login']."' and lessons_ID=".$_SESSION['s_lessons_ID']);
}

//pr($fields);

?>
