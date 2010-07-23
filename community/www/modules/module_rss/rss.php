<?php

$path = "../../../libraries/";
require_once($path."configuration.php");
//debug();

$result = eF_getTableData("modules", "*", "name='module_rss'");
$module = $result[0];
if ($module['active']) {
 $folder = $module['position'];
 $className = $module['className'];

 require_once G_MODULESPATH.$folder."/".$className.".class.php";
 $moduleInstance = new $className("student.php?ctg=module&op=".$className, $folder);
 $moduleInstance -> createRssFeed($_GET['type'], $_GET['mode'], $_GET['lesson']);
 exit;
} else {
 header("Content-Type: application/xml; charset="._CHARSET);
 echo "<message>Rss feeds not activated</message>";
 exit;
}


/*
$date = getdate();
$lastHour = mktime($date['hours'], 0, 0, $date['mon'], $date['mday'], $date['year']);

lastModificationTime($lastHour);
cacheHeaders(lastModificationTime());
lastModificationTime($lastHour);


function cacheHeaders($lastModifiedDate) {
    if ($lastModifiedDate) {
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModifiedDate) {
            if (php_sapi_name()=='CGI') {
                Header("Status: 304 Not Modified");
            } else {
                Header("HTTP/1.0 304 Not Modified");
            }
            exit;
        } else {
            $gmtDate = gmdate("D, d M Y H:i:s \G\M\T",$lastModifiedDate);
            header('Last-Modified: '.$gmtDate);
        }
    }
}

// This function uses a static variable to track the most recent
// last modification time
function lastModificationTime($time=0) {
    static $last_mod ;
    if (!isset($last_mod) || $time > $last_mod) {
        $last_mod = $time ;
    }
    return $last_mod ;
}

*/
?>
