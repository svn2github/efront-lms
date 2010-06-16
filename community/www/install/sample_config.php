<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/** The database Host */
define('G_DBTYPE', 'mysql');
/** The database Host */
define('G_DBHOST', 'localhost');
/** The database user*/
define('G_DBUSER', 'efront');
/** The database user password*/
define('G_DBPASSWD', 'efront');
/** The database name*/
define('G_DBNAME', 'efront');
/** The database tables prefix*/
define('G_DBPREFIX', '');

/** The server name*/
define('G_SERVERNAME', 'http://'.$_SERVER['HTTP_HOST'].'/');

/**Software root path*/
define('G_ROOTPATH', str_replace("\\", "/", dirname(dirname(__FILE__)))."/");

/**Current version*/
define('G_VERSION_NUM', '3.6.4');

/**Include function files*/
require_once('globals.php');
?>
