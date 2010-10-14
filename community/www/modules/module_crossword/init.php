<?
set_time_limit(0);

require 'config.php';
require 'lib/utility.php';
require 'lib/mysql.class.php';
require 'lib/php_crossword.class.php';

$script_start = getmicrotime();

$cols = (int)$_REQUEST['cols'] ? (int)$_REQUEST['cols'] : $_SESSION['CROSSWORD_MAXWORD'];
$rows = (int)$_REQUEST['rows'] ? (int)$_REQUEST['rows'] : $_SESSION['CROSSWORD_MAXWORD'];
$max_words = (int)$_REQUEST['max_words'] ? (int)$_REQUEST['max_words'] : $_SESSION['CROSSWORD_MAXWORD'];
$max_tries = (int)$_REQUEST['max_tries'] ? (int)$_REQUEST['max_tries'] : $_SESSION['CROSSWORD_MAXWORD'];
$groupid = !empty($_REQUEST['groupid']) ? $_REQUEST['groupid'] : 'demo';

$pc =& new PHP_Crossword($rows, $cols);

$pc->setGroupID($groupid);
$pc->setMaxWords($max_words);

// just support for Lithuanian charset
$charset = $pc->groupid == 'lt' ? 'ISO-8859-13' : 'ISO-8859-1';
?>
