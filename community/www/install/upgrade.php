<?php
/**
 * $LastChangedRevision: 5390 $
 * Installation file
 *
 * This file is used to perform the upgrade
 * @package eFront
 */
session_cache_limiter('none'); //Initialize session
session_start();

//error_reporting(E_ALL);

/*The inclusion directory*/
$path = "../../libraries/";

//Read current version from sample_config file
preg_match("/define\('G_VERSION_NUM', '(.*)'\);/", file_get_contents("sample_config.php"), $matches);
define("G_VERSION_NUM", $matches[1]);
//Read current build from globals.php file
//preg_match('/\$LastChangedRevision: (\d+) \$/', file_get_contents($path."globals.php"), $matches);
preg_match('/\$build = (\d+);/', file_get_contents($path."globals.php"), $matches);




define("G_BUILD", $matches[1]);

$versionTypes = array('educational' => 'Educational',
                      'enterprise' => 'Enterprise',
                      'standard' => 'Community++',
                      'community' => 'Community');
define("G_VERSIONTYPE", 'community');
/*Disable output buffering during installation for better error handling*/
define("NO_OUTPUT_BUFFERING", true);
//Set some ini properties we need
ini_set("display_errors", true);
ini_set('include_path', $path.'../PEAR/');
//ini_set("memory_limit", "-1");
ini_get("max_execution_time") < 120 ? ini_set("max_execution_time", "120") : null;
//It is imperative that the smarty directory is writable in order to continue
if (!is_writable($path.'smarty/themes_cache')) {
 echo Installation :: printErrorMessage("Directory <b>".realpath($path.'smarty/themes_cache')."</b> must be writable by the server in order to continue");
 exit;
}
//Check whether we are on http or https
isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? $protocol = 'https' : $protocol = 'http';
//Set the servername
define("G_SERVERNAME", dirname(dirname($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'])).'/');
//unset session, if any
//    unset($_SESSION);
//    session_destroy();
/** HTML_QuickForm Class*/
require_once 'HTML/QuickForm.php';
/** HTML_QuickForm Smarty renderer class*/
require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
/**ADODB database abstraction class*/
require_once($path.'adodb/adodb.inc.php');
/**ADODB exceptions class*/
require_once($path.'adodb/adodb-exceptions.inc.php');
/**Various tools*/
require_once($path.'tools.php');
//Initialize ADODB
$ADODB_CACHE_DIR = $path."adodb/cache";
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
if (function_exists('apc_clear_cache')) {
 apc_clear_cache('user');
}
$values = Installation :: checkCurrentVersion();
$db = ADONewConnection('mysql');
$values['old_db_name'] = $values['db_name'];
//first, try to connect to the host
$db -> NConnect($values['db_host'], $values['db_user'], $values['db_password'], $values['db_name']);
$db -> Execute("SET NAMES 'UTF8'");
try {
 $db -> Execute("alter table users add last_login int(10) unsigned default NULL");
 $db->Execute("update users u set last_login=(select max(timestamp) from logs where users_LOGIN=u.login and action='login')");
} catch (Exception $e) {
 $failed_queries[] = $e->getMessage();
}
try {
 $db->Execute("alter table lessons add access_limit int(10) default 0");
} catch (Exception $e) {
 $failed_queries[] = $e->getMessage();
}
try {
 $db->Execute("alter table users_to_lessons add access_counter int(10) default 0");
} catch (Exception $e) {
 $failed_queries[] = $e->getMessage();
}
//Finally, change the version number and clear apc cache
try {
 $db->Execute("update configuration set value = '".G_VERSION_NUM."' where name = 'database_version'");
 if (function_exists('apc_clear_cache')) {
  apc_clear_cache('user');
 }
} catch (Exception $e) {
 $failed_queries[] = $e->getMessage();
}
$file_contents = file_get_contents($GLOBALS['path'].'configuration.php'); //Load sample configuration file
$new_file_contents = preg_replace("/(define\(['\"]G_VERSION_NUM['\"], ['\"]).*(['\"]\);)/", '${1}'.G_VERSION_NUM.'${2}', $file_contents);
if (!file_put_contents($GLOBALS['path'].'configuration.php', $new_file_contents)) {
 throw new Exception("The configuration file could not be created");
}
var_dump($failed_queries);
/*
EfrontConfiguration :: setValue('database_version', G_VERSION_NUM);

	

if (!defined("PREPROCESSED") && $GLOBALS['configuration']['version_type'] != G_VERSIONTYPE) {

	EfrontConfiguration :: setValue('version_type', G_VERSIONTYPE);

	EfrontConfiguration :: setValue('version_users', '');

	EfrontConfiguration :: setValue('version_activated', '');

	EfrontConfiguration :: setValue('version_upgrades', '');

	EfrontConfiguration :: setValue('version_key', '');

}

//EfrontStats::createViews();



if ($_GET['ajax']) {
	echo json_encode(array('status' => 1, 'message' => 'Successfully completed Unattended upgrade'));
	EfrontSystem :: unlockSystem();
} else {
	header("location:".G_SERVERNAME."index.php?delete_install=1");
}
*/
/**
 *
 * @author user
 *
 */
class Installation
{
 /**
	 *
	 * @return unknown_type
	 */
 public static function checkCurrentVersion() {
  if (is_file($GLOBALS['path'].'configuration.php')) {
   $file_contents = file_get_contents($GLOBALS['path'].'configuration.php'); //Load existing configuration file
   preg_match('/define\(["\']G_DBTYPE["\'], ["\'](.*)["\']\);/', $file_contents, $type);
   preg_match('/define\(["\']G_DBHOST["\'], ["\'](.*)["\']\);/', $file_contents, $host);
   preg_match('/define\(["\']G_DBUSER["\'], ["\'](.*)["\']\);/', $file_contents, $user);
   preg_match('/define\(["\']G_DBPASSWD["\'], ["\'](.*)["\']\);/', $file_contents, $password);
   preg_match('/define\(["\']G_DBNAME["\'], ["\'](.*)["\']\);/', $file_contents, $name);
   //preg_match('/define\(["\']G_VERSION_NUM["\'], ["\'](.*)["\']\);/', $file_contents, $name);
   $currentVersion = array('db_type' => $type[1],
                  'db_host' => $host[1],
                                    'db_user' => $user[1],
                                    'db_password' => $password[1],
                                    'db_name' => $name[1]);
  } else {
  }
  return $currentVersion;
 }
 /**
	 * Print a default error message
	 *
	 * This function prints an error message.
	 *
	 * @param string $message The error message
	 * @return string The HTML code of the formatted message
	 * @since 3.6.0
	 * @access public
	 * @static
	 */
 public static function printErrorMessage($message) {
  $str = '
     <style>
     .singleMessage{width:100%;font-family:trebuchet ms;font-size:14px;border:1px solid red;background-color:#ffcccc;margin-top:10px}
     .singleMessage td{padding:10px;}
     .singleMessage td:first-child{width:1%}
     </style>
     <table class = "singleMessage">
      <tr><td><img src = "../themes/default/images/32x32/warning.png" alt = "Failure" title = "Failure"></td>
       <td><div style = "font-size:16px;font-weight:bold">An error occured:</div><div>'.$message.'</div></tr>
     </table>
     ';
  return $str;
 }
}
?>
