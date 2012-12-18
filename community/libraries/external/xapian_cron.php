<?php
if (php_sapi_name() != "cli") {
 print "This script is written to run under the command line ('cli') version of\n";
 print "the PHP interpreter, but you're using the '".php_sapi_name()."' version\n";
 exit(1);
}

error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set("html_errors", true);
//$file = $argv[1];
require_once "xapian.class.php";
//EfrontXapian::getInstance()->addFileToIndex($file);
EfrontXapian::cron();


?>
