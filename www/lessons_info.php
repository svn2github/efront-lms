<?php
session_cache_limiter('none');          //Initialize session
session_start();
//error_reporting(E_ALL);
$path = "../libraries/";                //Define default path

/** The configuration file.*/
require_once $path."configuration.php";

$directionsTree = new EfrontDirectionsTree();
$smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML());

$smarty -> display('lessons_info.tpl');

?>