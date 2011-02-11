<?php
/**

* @package eFront

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
function formatStaticText($value) {
     $toggleEditorCode = '
  <div>
            <img onclick = "toggleEditor(\'data\',\'simpleEditor\');" class = "handle" src = "images/16x16/order.png" title = "'._TOGGLEHTMLEDITORMODE.'" alt = "'._TOGGLEHTMLEDITORMODE.'" />&nbsp;
   <a href = "javascript:toggleEditor(\'data\',\'simpleEditor\');" id = "###editor_id###">'._TOGGLEHTMLEDITORMODE.'</a>
  </div>';

     switch ($value['name']) {
      case 'toggle_editor_code': $value['label'] = str_replace('###editor_id###', $value['label'], $toggleEditorCode); break;
      default: break;
     }

     return $value['label'];
}

function filterSortPage($dataSource) {
 isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

 if (isset($_GET['sort']) && $_GET['sort'] && eF_checkParameter($_GET['sort'], 'text')) {
  $sort = $_GET['sort'];
  isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
 } else {
  $sort = key(current($dataSource)); //The first field of the data array is the default sorting field
  $order = 'desc';
 }

 $dataSource = eF_multiSort($dataSource, $sort, $order);
 if (isset($_GET['filter'])) {
  $dataSource = eF_filterData($dataSource, $_GET['filter']);
 }
 $tableSize = sizeof($dataSource);

 if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
  isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
  $dataSource = array_slice($dataSource, $offset, $limit, true);
 }

 return array($tableSize, $dataSource);
}

function prepareFormRenderer($form) {
 $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
 $form -> setRequiredNote(_REQUIREDNOTE);

 $renderer = new HTML_QuickForm_Renderer_ArraySmarty($GLOBALS['smarty']);
 $renderer->setRequiredTemplate(
         '{$html}{if $required}
              &nbsp;<span class = "formRequired">*</span>
          {/if}'
          );

 $renderer->setErrorTemplate(
         '{$html}{if $error}
              <span class = "formError">{$error}</span>
          {/if}'
          );
 $form -> accept($renderer);

    return $renderer;
}

function createConstraintsFromSortedTable() {
 $constraints = array();

 isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $constraints['offset'] = $_GET['offset'] : null;
 isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int') ? $constraints['limit'] = $_GET['limit'] : $constraints['limit'] = G_DEFAULT_TABLE_SIZE;
 isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'alnum_with_spaces') ? $constraints['sort'] = $_GET['sort'] : null;
 isset($_GET['filter']) ? $constraints['filter'] = $_GET['filter'] : null;
 isset($_GET['order']) && in_array($_GET['order'], array('asc', 'desc')) ? $constraints['order'] = $_GET['order'] : $constraints['order'] = 'asc';

 //These 2 lines remove the ||| limits of the branch/job filter
 $filter = explode("||||", $constraints['filter']);

 $constraints['filter'] = $filter[0];
 !isset($filter[1]) OR $constraints['branch'] = $filter[1];
 !isset($filter[2]) OR $constraints['jobs'] = $filter[2];

 if (isset($_COOKIE['toggle_active']) && $_COOKIE['toggle_active'] == 1) {
  $constraints['active'] = 1;
 } else if (isset($_COOKIE['toggle_active']) && $_COOKIE['toggle_active'] == -1) {
  $constraints['active'] = 0;
 }

 return $constraints;
}

function handleAjaxExceptions($e) {
 header("HTTP/1.0 500");
 $message = str_replace("<br>", ",", $e -> getMessage());
 if ($e -> getCode()) {
  $message .= ' ('.$e -> getCode().')';
 }
 echo $message;
 exit;
}

function handleNormalFlowExceptions($e) {
 $GLOBALS['smarty'] -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
 $GLOBALS['message'] = $e -> getMessage();
 if ($e -> getCode()) {
  $GLOBALS['message'] .= ' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
 }
 $GLOBALS['message_type'] = 'failure';
}

/**

 * If json_decode() does not exist (5.2.0), use external implementation

 */
if (!function_exists('json_decode')) {
    require_once 'external/facebook-platform/php/jsonwrapper/JSON/JSON.php';
    function json_decode($arg) {
        global $services_json;
        if (!isset($services_json)) {
            $services_json = new Services_JSON();
        }
        return $services_json->decode($arg);
    }
}
/**

 * If str_getcsv() does not exist (5.3.0), use external implementation

 */
if (!function_exists('str_getcsv')) {
 function str_getcsv($input, $delimiter=',', $enclosure='"', $escape=null, $eol=null) {
  $temp=fopen("php://memory", "rw");
  fwrite($temp, $input);
  fseek($temp, 0);
  $r=fgetcsv($temp, 4096, $delimiter, $enclosure);
  fclose($temp);
  return $r;
 }
}
/*

 * Function that transforms a templated message according to the specific

 * substitutions provided in the template_substitutions array

 *

 * All fields to be substituted are denoted within ###...###.

 * @param: message the templated message to be formulated

 * @param: template_substituions: the array in the form array("fieldA" => "valueA",...) to substitute ###fieldA### with valueA

 */
function eF_formulateTemplateMessage($message, $template_substitutions) {
    foreach ($template_substitutions as $field => $value) {
        $message = str_replace("###" . $field . "###", $value, $message);
    }
    return $message;
}
/**

 * Profiling function

 *

 * @param unknown_type $start

 * @param unknown_type $path

 * @return unknown

 */
function profile($start = true, &$path) {
    $outputDirectory = 'www/profiles';
    if (!is_dir(G_ROOTPATH.$outputDirectory)) {
        mkdir(G_ROOTPATH.$outputDirectory, 0755);
    }
    if ($start) {
        if (function_exists('apd_set_pprof_trace') && is_file(G_ROOTPATH."www/pprofp.php")) {
            $path = apd_set_pprof_trace(G_ROOTPATH.$outputDirectory);
        }
    } else {
        $str = '<a href = "pprofp.php?data_file='.basename($path).'&opt=u">'.basename($path).'</a>';
        return $str;
    }
}
/**

 * This function is used to set the debugging mode to on. This is equivalent to setting error_reporting(E_ALL) and

 * $db -> debug = true (which outputs all sql queries and result messages)

 * <br/>Example:

 * <code>

 * debug();

 * eF_getTableData("users", "*", "");

 * debug(false);

 * </code>

 *

 * @param boolean $mode Set the debugging to on/off

 * @param int $level the reporting level. Setting it to E_ALL & ~E_NOTICE shows all errors but notices

 * @since 3.5.0

 */
function debug($mode = true, $level = E_ALL) {
    ini_set("display_errors", true);
    if ($mode) {
  echo "Starting debug output";
     $_SESSION['debug_start'] = microtime(true);
        error_reporting($level);
        $GLOBALS['db']->debug=true;
    } else {
        error_reporting(E_ERROR);
        $GLOBALS['db']->debug=false;
        pr("time for this part: ".(microtime(true) - $_SESSION['debug_start']));
        unset($_SESSION['debug_start']);
    }
}
function eF_truncatePath($string, $length = 40, $pathLimit = 6, $etc = '...', $delimiter = "&nbsp;&rarr;&nbsp;")
{
 $stripped = strip_tags($string); //remove tags to count characters
 $piecesStripped = explode($delimiter, $stripped);
 if (mb_strlen($stripped) <= $length) {
  return $string;
 }
 //Remove the last element and keep it separately, as we don't want it to be truncated
 $lastElement = end($piecesStripped);
 $piecesStripped = array_slice($piecesStripped, 0, -1);
 $piecesLength = $piecesStripped;
 array_walk($piecesLength, 'trim');
 array_walk($piecesLength, create_function('&$v, $k', '$v = mb_strlen($v);'));
 $piecesLengthStart = $piecesLength;
 $piecesNum = sizeof($piecesLength);
 $step = 0;
 while (array_sum($piecesLength) > $length && $step < 5) {
 $step++;
  for ($k = 1; $k < $piecesNum; $k++) {
   if ($piecesLength[$k] > $pathLimit) {
    $piecesLength[$k] = $piecesLength[$k] - round($piecesLength[$k]*($piecesNum -$k)/10);
    if(array_sum($piecesLength) <= $length) {
     break;
    }
   }
  }
 }
 $piecesFinal = array();
 foreach ($piecesStripped as $key => $value) {
  if ($piecesLengthStart[$key] - $piecesLength[$key] > 3) {
   $replacement = mb_substr($piecesStripped[$key], 0, $piecesLength[$key]).$etc;
  } else {
   $replacement = $piecesStripped[$key];
  }
  $temp = $value;
  // added because preg_replace returns null when value contains /
  $piecesFinal[$key] = preg_replace('/'.preg_quote($piecesStripped[$key], '/').'/', $replacement, $value);
  if (is_null($piecesFinal[$key])) {
   $piecesFinal[$key] = $temp;
  }
 }
 $piecesFinal[] = $lastElement;
 $finalString = implode($delimiter, $piecesFinal); // with tags
 return $finalString;
}
/**

 * Format a user login

 *

 * This function formats a user's login based on

 * configuration parameters. There are 4 possible format parameters:

 * #surname#, #name#, #n# (The name initial) and #login#

 * The function replaces the passed login according to the format

 * specified in the global configuration. For example, if the format

 * is #surname# #n#. (#login#), the login 'jdoe' might become:

 * Doe J. (jdoe)

 *

 * @param $login The user login to replace with the formatted string

 * @param $fields If set, the function will not query database but will use instead this array. $login is not used in this case

 * @param boolean $duplicate Whether to resolve duplicates by adding the login in the end of the string

 * @return string The formatted string

 * @since 3.6.0

 */
function formatLogin($login, $fields = array(), $duplicate = true) {
    //The function is usually called by a filter, which passes a preg matches array, where index 1 holds the login
    !is_array($login) OR $login = $login[1];
 $roles = EfrontUser :: getRoles(true);
    $tags = array('#surname#', '#name#', '#login#', '#n#', '#type#');
    if (!empty($fields)) {
        $replacements = array($fields['surname'], $fields['name'], $fields['login'], mb_substr($fields['name'], 0, 1), $roles[$fields['user_type']]);
        $format = str_replace($tags, $replacements, $GLOBALS['configuration']['username_format']);
        return $format;
    } else {
//pr(apc_sma_info(false));exit;
     if (!isset($GLOBALS['_usernames'])) {
      $GLOBALS['_usernames'] = array();
      if (function_exists('apc_fetch') && $usernames = apc_fetch(G_DBNAME.':_usernames')) {
       $GLOBALS['_usernames'] = $usernames;
      } else {
       $result = eF_getTableDataFlat("users", "login, name, surname, user_type");
       foreach ($result['login'] as $key => $value) {
        $replacements = array($result['surname'][$key], $result['name'][$key], $value, mb_substr($result['name'][$key], 0, 1), $roles[$result['user_type'][$key]]);
        $format = trim(str_replace($tags, $replacements, $GLOBALS['configuration']['username_format']));
        $GLOBALS['_usernames'][$value] = $format;
       }
       if ($GLOBALS['configuration']['username_format_resolve'] && $duplicate) {
        $common = array_diff_assoc($GLOBALS['_usernames'], array_unique($GLOBALS['_usernames']));
        foreach ($common as $key => $value) {
         $originalKey = array_search($value, $GLOBALS['_usernames']);
         $GLOBALS['_usernames'][$originalKey] = $value.' ('.$originalKey.')';
         $GLOBALS['_usernames'][$key] = $value.' ('.$key.')';
        }
       }
       if (function_exists('apc_store')) {
        apc_store(G_DBNAME.':_usernames', $GLOBALS['_usernames']);
       }
      }
     }
     if ($GLOBALS['_usernames'][$login]) {
      return $GLOBALS['_usernames'][$login];
     } else {
      return $login;
     }
    }
}
/**

 * Format score

 *

 * This function is used to format the score according to the current settings

 * <br/>Example:

 * <code>

 * echo formatScore(23.5);      //Prints 23,50 if locale settings for decimal points is ','

 * </code>

 *

 * @param int $score The score to format

 * @return string The formatted score

 * @since 3.5.0

 * @access public

 */
function formatScore($score) {
    $scoreString = number_format($score, 2, $GLOBALS['configuration']['decimal_point'], '');
    return $scoreString;
}
/**

 * Return a price based on current settings

 *

 * Ths function formats a number as a price, taking into account current settings

 * <br>Example:

 * <code>

 * $price = formatPrice(3);		//Returns something like $3.00

 * $price = formatPrice(3, array('W', 3));		//Returns something like $3.00 / 3 Weeks

 * </code>

 *

 * @param $price The price to format

 * @param $recurring an array where the first element is one of 'D', 'W', 'M', 'Y' and the second element is a number (how many)

 * @param $showDiscount Whether to apply global discount (if any)

 * @return string The formatted price

 * @since 3.6.0

 */
function formatPrice($price, $recurring = false, $showDiscount = false) {
    $recurringOptions = array('D' => _DAYSCONDITIONAL,
            'W' => _WEEKSCONDITIONAL,
            'M' => _MONTHSCONDITIONAL,
            'Y' => _YEARSCONDITIONAL);
    if ($recurring && $recurring[0] && $recurring[1]) {
        $recurringString = '<span class = "recurringPrice"> / '.$recurring[1].' '.$recurringOptions[$recurring[0]].'</span>';
    } else {
        $recurringString = '';
    }
    $discountPrice = '';
    if ($showDiscount && $GLOBALS['configuration']['total_discount'] && $GLOBALS['configuration']['discount_start'] < time() && $GLOBALS['configuration']['discount_start'] + $GLOBALS['configuration']['discount_period']*3600*24 > time()) {
        $discountPrice = formatPrice($price - $price * $GLOBALS['configuration']['total_discount']/100, false, false);
        //$discountPrice = number_format($discountPrice, 2, $GLOBALS['configuration']['decimal_point'], $GLOBALS['configuration']['thousands_sep']);
    }
    $price = number_format($price, 2, $GLOBALS['configuration']['decimal_point'], $GLOBALS['configuration']['thousands_sep']);
    $currency = $GLOBALS['CURRENCYSYMBOLS'][$GLOBALS['configuration']['currency']];
    $GLOBALS['configuration']['currency_order'] ? $price = $currency.$price : $price = $price.$currency;
    //pr($GLOBALS['configuration']);
    if ($discountPrice) {
        $price = '<span class = "discountPrice">'.$discountPrice.' </span><span class = "oldPrice">'.$price.'</span>';
    } else {
        $price = '<span class = "normalPrice">'.$price.' </span>';
    }
    $price = $price.$recurringString;
    return $price;
}
/**

 * Format timestamp

 *

 * This function is used to convert the given timestamp into

 * human-readable format. The order is derived from the 'date_format'

 * configuration option.

 * <br/>Example:

 * <code>

 * $timestamp = time();

 * $dateString = formatTimestamp($timestamp);                   //Returns something like 20 May 2008

 * $dateString = formatTimestamp($timestamp, 'time');           //Returns something like 11:50:23, 20 May 2008

 * </code>

 * The function is the equivalent of the "formatTimestamp" smarty filter

 *

 * @param int $timestamp The timestamp to convert

 * @param string $mode The mode to use. Can be one of 'time' (full date/time), 'time_nosec' (date/time without seconds) or 'date' (default - date without time)

 * @return string The date in human-readable format

 */
function formatTimestamp($timestamp, $mode = false) {
    if (!$timestamp) {
        return '';
    }
    switch ($GLOBALS['configuration']['date_format']) {
        case "YYYY/MM/DD": $format = '%Y %b %d'; break;
        case "MM/DD/YYYY": $format = '%b %d %Y'; break;
        case "DD/MM/YYYY": default: $format = '%d %b %Y'; break;
    }
    switch ($mode) {
        case 'time': $format .= ', %H:%M:%S'; break;
        case 'time_nosec': $format .= ', %H:%M'; break;
        case 'time_only_nosec': $format = '%H:%M'; break;
        default: break;
    }
    $dateString = iconv(_CHARSET, 'UTF-8', strftime($format, $timestamp));
    return $dateString;
}
/**

 * Return date format

 *

 * This function returns a string suitable for use with date() and date()-like

 * functions, based on the current system settings

 * <br>Example:

 * <code>

 * echo getDateFormat(); //returns 'Ymd' or 'mdY' or 'dmY'

 * </code>

 *

 * @return string The date format based on system settings

 */
function getDateFormat() {
    switch ($GLOBALS['configuration']['date_format']) {
        case "YYYY/MM/DD": $format = 'YMd'; break;
        case "MM/DD/YYYY": $format = 'MdY'; break;
        case "DD/MM/YYYY": default: $format = 'dMY'; break;
    }
    return $format;
}
/**

 * Format an HTML table to simple text

 *

 * This function is used to convert an html table to plain text

 * <br/>Example:

 * <code>

 * $table = "<table><tr><td>a1</td><td>a2</td></tr><tr><td>b1</td><td>b2</td></tr></table>";

 * $plain_text= formatTimestamp($table);           //Returns something \na1\ta2\nb1\t\b2\n

 * </code>

 * The function is the equivalent of the "formatTimestamp" smarty filter

 *

 * @param int $timestamp The timestamp to convert

 * @param string $mode The mode to use. Can be one of 'time' (full date/time), 'time_nosec' (date/time without seconds) or 'date' (default - date without time)

 * @return string The date in human-readable format

 */
function formatHTMLTableToText($table) {
 $result = str_replace("<tr>", "<tr>\n", $table);
 $result = str_replace("<br>", "\n", $result);
 $result = str_replace("<td>", "<td>\t", $result);
 $result = str_replace("&nbsp;", " ", $result);
 $result = str_replace("&rarr;", "-->", $result);
 $result = strip_tags($result);
 return $result;
}
/**

* Sort multi-dimensional arrays

*/
function eF_multiSort($array, $sort_by, $sort_order = 'asc') {
    if (!in_array($sort_by, array_keys(current($array)))) {
        return $array;
    }
    $keys = array_keys($array);
    $sort_values = array();
    foreach ($array as $value) {
        $sort_values[] = mb_strtolower($value[$sort_by]); //mb_strtolower is used because array_multisort() takes into account the case, so that strings are sorted as ABC...XYZabc...xyz instead of AaBbCc...
    }
    if (is_numeric($sort_values[0])) { //If the column consists of numeric data, we want the default sorting to be descending, so we reverse the parameter
            $sort_order == 'asc' ? $sort_order = SORT_DESC : $sort_order = SORT_ASC;
    } else {
            $sort_order == 'asc' ? $sort_order = SORT_ASC : $sort_order = SORT_DESC;
    }
    array_multisort($sort_values, $sort_order, $keys);
    //pr($sort_values);pr($sort_order);pr($keys);
    foreach ($keys as $key) {
        //$temp[] = $array[$key];          //Use this in order to have keys reindexed
        $temp[$key] = $array[$key]; //Use this in order to have keys preserved
    }
    return $temp;
}
function setWritePermissions($dir) {
 $failedDirectories = $failedFiles = array();
 $d = new RecursiveDirectoryIterator($dir);
 if (!chmod($dir, 0755)) {
  $failedDirectories[] = $dir;
 }
 foreach (new RecursiveIteratorIterator($d, RecursiveIteratorIterator::SELF_FIRST) as $path) {
  if ($path->isDir() && $path -> getBasename() != '..') {
   if (!chmod($path -> getPathName(), 0755)) {
    $failedDirectories[] = $path -> getPathName();
   }
  } else if ($path -> isFile()) {
   if (!chmod($path -> getPathName(), 0644)) {
    $failedFiles[] = $path -> getPathName();
   }
  }
 }
 return array($failedDirectories, $failedFiles);
}
function setReadPermissions($dir) {
 $failedDirectories = $failedFiles = array();
 $d = new RecursiveDirectoryIterator($dir);
 if (!chmod($dir, 0555)) {
  $failedDirectories[] = $dir;
 }
 foreach (new RecursiveIteratorIterator($d, RecursiveIteratorIterator::SELF_FIRST) as $path) {
  if ($path->isDir() && $path -> getBasename() != '..') {
   if (!chmod($path -> getPathName(), 0555)) {
    $failedDirectories[] = $path -> getPathName();
   }
  } else if ($path -> isFile()) {
   if (!chmod($path -> getPathName(), 0444)) {
    $failedFiles[] = $path -> getPathName();
   }
  }
 }
 return array($failedDirectories, $failedFiles);
}
function checkPermissions($dir) {
 $failedDirectories = $failedFiles = array();
 $efrontDirectories = array("www", "libraries", "Zend", "PEAR", "backups", "upload");
 $d = new RecursiveDirectoryIterator($dir);
 foreach (new RecursiveIteratorIterator($d, RecursiveIteratorIterator::SELF_FIRST) as $key => $path) {
  if (!$path -> isWritable()) {
   if ($path->isDir()) {
    foreach ($efrontDirectories as $key2 => $value2)
     if (strpos($path -> getPathName(), $value2) !== false) {
      $failedDirectories[] = $path -> getPathName();
     }
   } else {
    $failedFiles[] = $path -> getPathName();
   }
  }
 }
 if (!is_writable(G_ROOTPATH)){
  $failedDirectories[] = G_ROOTPATH;
 }
 return array($failedDirectories, $failedFiles);
}
/**

* Encodes an IP to its hexadecimal equivalent

*

* This function takes an IP representation and converts it to its hexadecimal equivalent

* <br/> Example:

* <code>

* eF_encodeIP('127.0.0.1');         //Outputs: 7f000001

* </code>

*

* @param string $dotquad_ip The string representing the IP

* @return string The hexadecimal representation of the IP

* @version 1.0

*/
function eF_encodeIP($dotquad_ip) {
    $ip_sep = explode('.', $dotquad_ip);
    return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
}
/**

* Decodes an IP from hexadecimal to its equivalent human-readable format

*

* This function takes a hexadecimal IP representation and converts it to its

* equivalent human readable format.

* <br/> Example:

* <code>

* eF_decodeIP('7f000001');         //Outputs: 127.0.0.1

* </code>

*

* @param string $hex_ip The hexadecimal representation of the IP

* @return string The human readable representation of the IP

* @version 1.0

*/
function eF_decodeIP($hex_ip) {
    if (!$hex_ip) {
        return '';
    }
    $dotquad_ip = hexdec(mb_substr($hex_ip,0,2)).'.'.
                  hexdec(mb_substr($hex_ip,2,2)).'.'.
                  hexdec(mb_substr($hex_ip,4,2)).'.'.
                  hexdec(mb_substr($hex_ip,6,2));
    return $dotquad_ip;
}
/**

* Checks if a client may access the system;

*

* This function checks the client IP against the stored values. If there exist values and the client IP is

* not included, the system denies access to it.

*

* @return bool true if the client may access the system

* @version 1.0

*/
function eF_checkIP()
{
    $client_ip = $_SERVER['REMOTE_ADDR'];
    $allowedIPs = $GLOBALS['configuration']['ip_white_list']; //Read the allowed IPs
    if (!$allowedIPs || !$client_ip) { //If the database doesn't
        $ok1 = true;
    } else {
     $client_ip_parts = explode('.', $client_ip);
        $allowed_ips = explode(",", preg_replace("/\s+/", "", $allowedIPs)); //explode ips into an array, after stripping off any whitespace
        $ok1 = false;
        foreach($allowed_ips as $ip) {
            $ip_parts = explode('.', $ip);
            $count = 0;
            $temp = true;
            while ($temp && $count < 4) {
                if ($client_ip_parts[$count] != $ip_parts[$count] && $ip_parts[$count] != '*' && $ip_parts[$count]) {
                    $temp = false;
                }
                $count++;
            }
            $ok1 = $ok1 | $temp;
        }
    }
    $disAllowedIPs = $GLOBALS['configuration']['ip_black_list']; //Read the allowed IPs
    if (!$disAllowedIPs || !$client_ip) { //If the database doesn't
        $ok2 = false;
    } else {
        $client_ip_parts = explode('.', $client_ip);
        $allowed_ips = explode(",", preg_replace("/\s+/", "", $disAllowedIPs)); //explode ips into an array, after stripping off any whitespace
        $ok2 = false;
        foreach($allowed_ips as $ip) {
            $ip_parts = explode('.', $ip);
            $count = 0;
            $temp = true;
            while ($temp && $count < 4) {
                if ($client_ip_parts[$count] != $ip_parts[$count] && $ip_parts[$count] != '*' ) {
                    $temp = false;
                }
                $count++;
            }
            $ok2 = $ok2 | $temp;
        }
    }
    return $ok1 & !$ok2; //For the user to be able to login, he must either be in the first group or the second group
}
/**

* Converts string to smilies

*

* This function parses a string and replaces text smilies occurences with the

* equivalent icons

*

* @param string $str The string to parse

* @return string The string returned

* @version 0.8

*/
function eF_convertTextToSmilies($str) {
    $img_str = ' <image src = "'.G_CURRENTTHEMEURL.'images/smilies/icon_';
    $text_array = array(':)', ':-)',
                           ':(', ':-(',
                           ';)', ';-)',
                           ':oops:',
                           ':D', ':-D',
                           ':o', ':-o',
                           '8O', '8-O',
                           ':?', ':-?',
                           '8)', '8-)',
                           ':lol:',
                           ':x', ':-x',
                           ':P', ':-P',
                           ':cry:',
                           ':evil:',
                           ':twisted:',
                           ':roll:');
    $smilies_array = array($img_str.'smile.gif" /> ', $img_str.'smile.gif" /> ',
                           $img_str.'sad.gif" /> ', $img_str.'sad.gif" /> ',
                           $img_str.'wink.gif" /> ', $img_str.'wink.gif" /> ',
                           $img_str.'redface.gif" /> ',
                           $img_str.'biggrin.gif" /> ', $img_str.'biggrin.gif" /> ',
                           $img_str.'surprised.gif" /> ', $img_str.'surprised.gif" /> ',
                           $img_str.'eek.gif" /> ', $img_str.'eek.gif" /> ',
                           $img_str.'confused.gif" /> ', $img_str.'confused.gif" /> ',
                           $img_str.'cool.gif" /> ', $img_str.'cool.gif" /> ',
                           $img_str.'lol.gif" /> ',
                           $img_str.'mad.gif" /> ', $img_str.'mad.gif" /> ',
                           $img_str.'razz.gif" /> ', $img_str.'razz.gif" /> ',
                           $img_str.'cry.gif" /> ',
                           $img_str.'evil.gif" /> ',
                           $img_str.'twisted.gif" /> ',
                           $img_str.'rolleyes.gif" /> ');
    $smilied_text = str_replace($text_array, $smilies_array, $str);
    //$smilied_text .= " "; //spaces required for chat
    return $smilied_text;
}
/**

* Checks if the designated user is ldap registered

*

* The input are a login / password pair, based on which the system tries to authenticate the user to

* the designated in the configuration LDAP server.

*

* @param string $login The user name

* @param string $password The user password

* @return mixed The user type, one of 'professor', 'student' and 'administrator' or false if the combination is not valid.

* @version 0.1

* @todo implementation

* @todo Remove the global variable...

*/
function eF_checkUserLdap($login, $password)
{
    $basedn = $GLOBALS['configuration']['ldap_basedn'];
    $ldap_uid = $GLOBALS['configuration']['ldap_uid'];
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    $ds = eF_ldapConnect();
    $sr = ldap_search($ds, $basedn, "$ldap_uid=$login");
    if (ldap_count_entries($ds, $sr) == 0) {
        return false; //User either does not exist or more than 1 users found
    }
    $dn = ldap_get_dn($ds, ldap_first_entry($ds, $sr));
    $b = ldap_bind($ds, $dn, $password);
    if (!$b) {
        return 0; //login / password values don't match
    }
    return true;
}
/**

* Get values for specified ldap attributes

*

* This function accepts a search filter and an array of attributes,

* and returns the equivalent values from the ldap server

*

* @param string $filter The search filter

* @param array $attributes The LDAP attributes

* @return array The array of attribute values

* @version 1.0

*/
function eF_getLdapValues($filter, $attributes)
{
    $basedn = $GLOBALS['configuration']['ldap_basedn']; //The base DN is needed to perform searches
    $ds = eF_ldapConnect();
    $sr = ldap_search($ds, $basedn, $filter, $attributes);
    $result = ldap_get_entries($ds, $sr);
    return $result;
}
/**

* Connect to LDAP server

*

* This function uses configuration values toattempt a connect to the LDAP server

*

* @return resource The LDAP link identifier

* @version 1.0

*/
function eF_ldapConnect() {
    $ds = ldap_connect($GLOBALS['configuration']['ldap_server'], $GLOBALS['configuration']['ldap_port']);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $GLOBALS['configuration']['ldap_protocol']);
    ldap_set_option($ds, LDAP_OPT_TIMELIMIT, 10);
    ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
    $b = ldap_bind($ds, $GLOBALS['configuration']['ldap_binddn'], $GLOBALS['configuration']['ldap_password']);
    return $ds;
}
/**

* Check a parameter against a type

*

* This function accepts a parameter and a type. It then checks the parameter against a regular expression corresponding

* to the type specified. If the regular expression is met, then the parameter is returned. Otherwise, false is returned

* Supported types are:<br>

* - string: Only characters, [A-Za-a]

* - uint: Only positive numbers or zero, [0-9]

* - id: Alias for uint

* - login: Valid login names are made of alphanumeric characters and @, no spaces

* - email: Valid email address

* - filename: Valid filenames must not include special characters, such as /,\,..

* - hex: Hexadecimal number

* - alnum: Alphanumeric characters, [A-Za-z0-9]

* - alnum_with_spaces: Alphanumeric characters, but spaces are valid as well, [A-Za-z0-9\s]

* - ldap_attribute: Valid ldap attribute names

* - text: A string with plain characters, digits, and symbols, but not quotes or other special characters (like $, / etc)

*

* <br>Example:

* <code>

* $param = 'Hello world!';

* if (eF_checkParameter($param, 'string')) {

*     echo "Parameter is String";

* }

*

* $param = '123';

* if (eF_checkParameter($param, 'unit')) {

*     echo "Parameter is Unsigned integer";

* }

*

* </code>

* But be careful:

* <code>

* $param = '0';

* if (eF_checkParameter($param, 'unit')) {                      //Wrong way! This will not evalute to true, since eF_checkParameter will return $param, which is 0.

*     echo "Parameter is Unsigned integer";

* }

*

* if (eF_checkParameter($param, 'unit') !== false) {             //Correct way, since we make sure that the value returned is actually false.

*     echo "Parameter is Unsigned integer";

* }

* </code>

*

* @param mixed $param The parameter to check

* @param string $type The parameter type (One of: string | uint | id | login | email | file | filename | directory | hex | timestamp | date | alnum | ldap_attribute | alnum_with_spaces | alnum_general | text | path)

* @return mixed The parameter, if it is of the specified type, or false otherwise

* @version 1.0.1

* Changes from 1.0 to 1.1:

* - Modified email declaration, so it can detect emails that have a dot (.) in the first part (before the '@').

*/
function eF_checkParameter($parameter, $type, $correct = false)
{
    switch ($type) {
        case 'string':
            if (!preg_match("/^[A-Za-z]{1,100}$/", $parameter)) {
                return false;
            }
            break;
        case 'uint':
        case 'id':
            if (!preg_match("/^[0-9]{1,100}$/", $parameter)) { //Caution: If 0 is met, then it will return 0 and not false! so, it must checked against false to make sure
                return false;
            }
            break;
        case 'login':
            //if (!preg_match("/^[^0-9]_*\w+(\w*[._@-]*\w*)*$/", $parameter)) {              //This means: begins with 0 or more '_', never a number, followed by at least 1 word character, followed by any combination of .,_,-,@ and word characters.
            if (!preg_match("/^_*\w+(\w*[._@-]*\w*)*$/", $parameter) || mb_strlen($parameter) > 100) { //This means: begins with 0 or more '_',                 followed by at least 1 word character, followed by any combination of .,_,-,@ and word characters.
                return false;
            }
            break;
        case 'email':
            if (!preg_match("/^([a-zA-Z0-9_\.\-'])+\@(([a-zA-Z0-9_\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $parameter)) { //This means: begins with 0 or more '_' or '-', followed by at least 1 word character, followed by any combination of '_', '-', '.' and word characters, then '@', then the same as before, then the '.' and then 1 ore more characters.
                return false;
            }
            break;
        case 'filename':
        case 'file':
            if (preg_match("/^.*((\.\.)|(\/)|(\\\)).*$/", $parameter)) { //File name must not contain .. or slashes of any kind
                return false;
            }
            break;
        case 'directory':
            if (preg_match("/^.*((\.\.)|(\\\)).*$/", $parameter)) { //Directory is the same as filename, except that it may contain forward slashes
                return false;
            }
            break;
        case 'hex':
            if (!preg_match("/^[0-9a-fA-F]{1,100}$/", $parameter)) {
                return false;
            }
            break;
        case 'timestamp':
            if (!preg_match("/^[0-9]{10}$/", $parameter)) {
                return false;
            }
            break;
        case 'date':
            if (!preg_match("/^[0-3]?[0-9]\-[0-1]?[0-9]\-[0-9]{4}$/", $parameter)) {
                return false;
            }
            break;
        case 'alnum':
            if (!preg_match("/^[A-Za-z0-9_]{1,100}$/", $parameter)) {
                return false;
            }
        break;
        case 'ldap_attribute':
            if (!preg_match("/^[A-Za-z0-9:;\-_]{1,100}$/", $parameter)) { //An ldap attribute may be of the form: cn:lang-el;
                return false;
            }
        break;
       case 'alnum_with_spaces':
            if (!preg_match("/^[A-Za-z0-9_\s]{1,100}$/", $parameter)) {
                return false;
            }
       break;
       case 'alnum_general':
            if (!preg_match("/^[\.,_\-A-Za-z0-9\s]{1,100}$/", $parameter)) {
                return false;
            }
       break;
       case 'text':
            if (preg_match("/^.*[$\/\'\"]+.*$/", $parameter)) {
                return false;
            }
       break;
       case 'noscript':
            if (preg_match("/^.*<script>.*<\/script>.*$/i", $parameter)) {
                return false;
            }
       break;
       case 'path':
           if (preg_match("/^.*[$\"]+.*$/", $parameter)) {
                return false;
            }
       break;
       default:
            break;
    }
    return $parameter;
}
function strip_script_tags($str) {
    $str = preg_replace("/<script>(.*)<\/script>/i", "$1", $str);
    return $str;
}
/**

* Get navigation menu

*

* This function returns an array with all the elements and links of the user navigation menu. The array

* is in the form $menu[category][type] => array('title' => 'link'). There are 2 types of categories: 'lesson',

* which refers to lesson specific options, and 'general', which refers to general options.

* For example, some of the entries of the student menu are shown below:

* <code>

*Array

*(

*    [lesson][lessons] => Array

*        (

*            [title] => Lessons

*            [link] => student.php?ctg=lessons&op=lessons_list

*        )

*

*    [lesson][theory] => Array

*        (

*            [title] => Theory

*            [link] => student.php?ctg=theory

*        )

*

*    [lesson][examples] => Array

*        (

*            [title] => Examples

*            [link] => student.php?ctg=examples

*        )

*    [general][personal] => Array

*        (

*            [title] => Options

*            [link] => student.php?ctg=personal

*        )

* </code>

*

* @return array The navigation menu

* @version 0.5

*/
function eF_getMenu()
{
    $menu = array();
    switch($_SESSION['s_type']) {
        case 'administrator':
            $menu['general']['control_panel'] = array('title' => _CONTROLCENTER, 'link' => 'administrator.php?ctg=control_panel', 'image' => 'home');
            $menu['general']['users'] = array('title' => _USERS, 'link' => 'administrator.php?ctg=users', 'image' => 'users');
            $menu['general']['lessons'] = array('title' => _LESSONS, 'link' => 'administrator.php?ctg=lessons', 'image' => 'lessons');
            $menu['general']['directions'] = array('title' => _CATEGORIES, 'link' => 'administrator.php?ctg=directions', 'image' => 'categories');
            $menu['general']['courses'] = array('title' => _COURSES, 'link' => 'administrator.php?ctg=courses', 'image' => 'courses');
            //$menu['general']['user_types']    = array('title' => _ROLES,         'link' => 'administrator.php?ctg=user_types',    'image' => 'user_types');
            $menu['general']['user_groups'] = array('title' => _GROUPS, 'link' => 'administrator.php?ctg=user_groups', 'image' => 'users');
            $menu['general']['statistics'] = array('title' => _STATISTICS,'link' => 'administrator.php?ctg=statistics', 'image' => 'reports');
            $menu['general']['cms'] = array('title' => _CMS, 'link' => 'administrator.php?ctg=cms', 'image' => 'unit');
            if ((!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden') && $GLOBALS['configuration']['disable_forum'] != 1) {
                $menu['general']['forum'] = array('title' => _FORUM, 'link' => $_SESSION['s_type'].'.php?ctg=forum', 'image' => 'message');
            }
            if ((!isset($GLOBALS['currentUser'] -> coreAccess['personal_messages']) || $GLOBALS['currentUser'] -> coreAccess['personal_messages'] != 'hidden') && $GLOBALS['configuration']['disable_messages'] != 1) {
               $menu['general']['messages'] = array('title' => _MESSAGES, 'link' => $_SESSION['s_type'].".php?ctg=messages", 'image' => 'mail');
            }
            $menu['general']['emails'] = array('title' => _EMAILS, 'link' => 'administrator.php?ctg=emails', 'image' => 'mail');
            $menu['general']['chat'] = array('title' => _CHAT, 'link' => $_SESSION['s_type'].".php?ctg=chat", 'image' => 'chat');
            foreach ($user_module['administrator'] as $value) {
                if ($value['position'] == 'left') {
                    $menu['general'][$value['name']] = array('title' => $value['title'], 'link' => 'administrator.php?ctg='.$value['name'], 'image' => 'addons');
                }
            }
            $menu['general']['logout'] = array('title' => _LOGOUT, 'link' => 'index.php?logout=true', 'image' => 'logout');
        break;
        case 'professor' :
            if ($_SESSION['s_lessons_ID'] != false) {
                $menu['lesson']['control_panel'] = array('title' => _MAINPAGE, 'link' => 'professor.php?ctg=control_panel', 'image' => 'home', 'id' => 'lesson_main_a');
                if ($GLOBALS['currentUser'] -> coreAccess['content'] != 'hidden') {
                    $menu['lesson']['content'] = array('title' => _CONTENTMANAGEMENT, 'link' => 'professor.php?ctg=content', 'image' => 'content', 'id' => 'content_a');
                    //$menu['lesson']['scheduling']    = array('title' => _SCHEDULING,        'link' => 'professor.php?ctg=scheduling',    'image' => 'date-time', 'id' => 'scheduling_a');
                    if ($GLOBALS['currentLesson'] -> options['projects'] && $GLOBALS['configuration']['disable_projects'] != 1) {
                        $menu['lesson']['projects'] = array('title' => _PROJECTS, 'link' => 'professor.php?ctg=projects', 'image' => 'projects', 'id' => 'exercises_a');
                    }
                    if ($GLOBALS['currentLesson'] -> options['tests'] && $GLOBALS['configuration']['disable_tests'] != 1) {
                        $menu['lesson']['tests'] = array('title' => _TESTS, 'link' => 'professor.php?ctg=tests', 'image' => 'tests', 'id' => 'tests_a');
                    }
     if ($GLOBALS['currentLesson'] -> options['feedback'] && $GLOBALS['configuration']['disable_feedback'] != 1) {
                        $menu['lesson']['feedback'] = array('title' => _FEEDBACK, 'link' => 'professor.php?ctg=feedback', 'image' => 'feedback', 'id' => 'feedback_a');
                    }
                    if ($GLOBALS['currentLesson'] -> options['rules']) {
                        $menu['lesson']['rules'] = array('title' => _ACCESSRULES, 'link' => 'professor.php?ctg=rules', 'image' => 'rules', 'id' => 'rules_a');
                    }
                }
                if ($GLOBALS['currentLesson'] -> options['glossary'] && $GLOBALS['currentUser'] -> coreAccess['glossary'] != 'hidden' && $GLOBALS['configuration']['disable_glossary'] != 1) {
     $menu['lesson']['glossary'] = array('title' => _GLOSSARY, 'link' => 'professor.php?ctg=glossary', 'image' => 'glossary', 'id' => 'glossary_a');
                }
                if ($GLOBALS['currentLesson'] -> options['forum'] && (!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden') && $GLOBALS['configuration']['disable_forum'] != 1) {
                    $forums_id = eF_getTableData("f_forums", "id", "lessons_ID=".$_SESSION['s_lessons_ID']);
                    if (sizeof($forums_id) > 0) {
                        $menu['lesson']['forum'] = array('title' => _FORUM, 'link' => $_SESSION['s_type'].'.php?ctg=forum&forum='.$forums_id[0]['id'],'image' => 'message', 'id' => 'forum_a');
                    } else {
                        $menu['lesson']['forum'] = array('title' => _FORUM, 'link' => $_SESSION['s_type'].".php?ctg=forum",'image' => 'message', 'id' => 'forum_a');
                    }
                }
                /*

                if (($GLOBALS['currentLesson'] -> options['calendar']) && $GLOBALS['currentLesson'] -> options['calendar']==1) {

                    $menu['lesson']['calendar']      = array('title' => _CALENDAR,       'link' => 'professor.php?ctg=calendar',     'image' => 'calendar', 'id' => 'calendar_a');

                }

                */
                if ($GLOBALS['currentUser'] -> coreAccess['files'] != 'hidden') {
                    $menu['lesson']['file_manager'] = array('title' => _FILES, 'link' => 'professor.php?ctg=file_manager', 'image' => 'file_explorer', 'id' => 'file_manager_a');
                }
                if ($GLOBALS['currentUser'] -> coreAccess['settings'] != 'hidden') {
                    $menu['lesson']['settings'] = array('title' => _LESSONSETTINGS, 'link' => 'professor.php?ctg=settings', 'image' => 'tools', 'id' => 'settings_a');
                }
                foreach ($user_module['professor'] as $value) {
                    if ($value['position'] == 'left' && ($module['mandatory'] != 'false' || ($GLOBALS['currentLesson'] -> options[$module['name']]))) {
                        $menu['lesson'][$value['name']] = array('title' => $value['title'], 'link' => 'professor.php?ctg='.$value['name'], 'image' => 'addons', 'id' => 'module');
                    }
                }
            }
            $menu['general']['lessons'] = array('title' => _LESSONS, 'link' => 'professor.php?ctg=lessons', 'image' => 'lessons');
            $menu['general']['statistics'] = array('title' => _STATISTICS, 'link' => 'professor.php?ctg=statistics', 'image' => 'reports');
            if ($GLOBALS['configuration']['disable_calendar'] != 1) {
       $menu['general']['calendar'] = array('title' => _CALENDAR, 'link' => 'professor.php?ctg=calendar', 'image' => 'calendar');
            }
   if ((!isset($GLOBALS['currentUser'] -> coreAccess['personal_messages']) || $GLOBALS['currentUser'] -> coreAccess['personal_messages'] != 'hidden') && $GLOBALS['configuration']['disable_messages'] != 1) {
                $menu['general']['messages'] = array('title' => _MESSAGES, 'link' => $_SESSION['s_type'].".php?ctg=messages", 'image' => 'mail');
   }
   if (!isset($GLOBALS['currentUser'] -> coreAccess['dashboard']) || $GLOBALS['currentUser'] -> coreAccess['dashboard'] != 'hidden') {
    $menu['general']['personal'] = array('title' => _SETTINGS, 'link' => 'professor.php?ctg=personal', 'image' => 'user');
   }
            $menu['general']['logout'] = array('title' => _LOGOUT, 'link' => 'index.php?logout=true', 'image' => 'logout');
        break;
        case 'student':
            if ($_SESSION['s_lessons_ID'] != false) {
                $menu['lesson']['control_panel'] = array('title' => _MAINPAGE, 'link' => 'student.php?ctg=control_panel', 'image' => 'home', 'target' => "mainframe", 'id' => 'lesson_main_a');
                if ($GLOBALS['currentUser'] -> coreAccess['content'] != 'hidden') {
                    $currentContent = new EfrontContentTree($_SESSION['s_lessons_ID']);
                    foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontTheoryFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)))) as $key => $value) {
                        $theoryContentIds[$key] = $key;
                    }
                    foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontExampleFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)))) as $key => $value) {
                        $exampleContentIds[$key] = $key;
                    }
                    foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontTestsFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)))) as $key => $value) {
                        $testsContentIds[$key] = $key;
                    }
                    if ($GLOBALS['currentLesson'] -> options['theory']) {
                        if (sizeof($theoryContentIds) > 0) {
                            $menu['lesson']['theory'] = array('title' => _THEORY, 'link' => 'student.php?ctg=content&type=theory', 'num' => sizeof($theoryContentIds), 'image' => 'theory', 'target' => "mainframe", 'id' => 'theory_a');
                        }
                    }
                    if ($GLOBALS['currentLesson'] -> options['examples']) {
                        if (sizeof($exampleContentIds) > 0) {
                            $menu['lesson']['examples'] = array('title' => _EXAMPLES, 'link' => 'student.php?ctg=content&type=examples', 'num' => sizeof($exampleContentIds), 'image' => 'examples', 'target' => "mainframe", 'id' => 'examples_a');
                        }
                    }
                    if ($GLOBALS['currentLesson'] -> options['projects'] && $GLOBALS['configuration']['disable_projects'] != 1) {
                        $projects = $GLOBALS['currentLesson'] -> getProjects();
                        if (sizeof($projects) > 0) {
                            $menu['lesson']['projects'] = array('title' => _PROJECTS, 'link' => 'student.php?ctg=projects', 'num' => sizeof($projects), 'image' => 'projects', 'target' => "mainframe", 'id' => 'exercises_a');
                        }
                    }
                    if ($GLOBALS['currentLesson'] -> options['tests'] && $GLOBALS['configuration']['disable_tests'] != 1) {
                        if (sizeof($testsContentIds) > 0) {
                            $menu['lesson']['tests'] = array('title' => _TESTS, 'link' => 'student.php?ctg=content&type=tests', 'num' => sizeof($testsContentIds), 'image' => 'tests', 'target' => "mainframe", 'id' => 'tests_a');
                        }
                    }
                }
                if ($GLOBALS['currentLesson'] -> options['forum'] && (!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden') && $GLOBALS['configuration']['disable_forum'] != 1) {
                    $forums_id = eF_getTableData("f_forums", "id", "lessons_ID=".$_SESSION['s_lessons_ID']);
                    if (sizeof($forums_id) > 0) {
                        $menu['lesson']['forum'] = array('title' => _FORUM, 'link' => $_SESSION['s_type'].'.php?ctg=forum&forum='.$forums_id[0]['id'], 'image' => 'message', 'target' => "mainframe", 'id' => 'forum_a');
                    } else {
                        $menu['lesson']['forum'] = array('title' => _FORUM, 'link' => $_SESSION['s_type'].".php?ctg=forum", 'image' => 'message', 'target' => "mainframe", 'id' => 'forum_a');
                    }
                }
                if ($GLOBALS['currentLesson'] -> options['glossary'] && (!isset($GLOBALS['currentUser'] -> coreAccess['content']) || $GLOBALS['currentUser'] -> coreAccess['content'] != 'hidden') && $GLOBALS['configuration']['disable_glossary'] != 1) {
                    $menu['lesson']['glossary'] = array('title' => _GLOSSARY, 'link' => 'student.php?ctg=glossary', 'image' => 'glossary', 'target' => "mainframe", 'id' => 'glossary_a');
                }
                foreach ($user_module['student'] as $value) {
                    if ($value['position'] == 'left' && ($module['mandatory'] != 'false' || ($GLOBALS['currentLesson'] -> options[$module['name']]))) {
                        $menu['lesson'][$value['name']] = array('title' => $value['title'], 'link' => 'student.php?ctg='.$value['name'], 'image' => 'addons', 'target' => "mainframe", 'id' => 'modules_i');
                    }
                }
            }
            $menu['general']['lessons'] = array('title' => _LESSONS, 'link' => 'student.php?ctg=lessons', 'image' => 'lessons', 'target' => "mainframe");
            if ($_SESSION['s_lessons_ID'] != false) {
                $menu['general']['statistics'] = array('title' => _STATISTICS, 'link' => 'student.php?ctg=statistics', 'image' => 'reports', 'target' => "mainframe");
            }
            if ($GLOBALS['configuration']['disable_calendar'] != 1) {
                $menu['general']['calendar'] = array('title' => _CALENDAR, 'link' => 'student.php?ctg=calendar', 'image' => 'calendar', 'target' => "mainframe");
            }
            if ((!isset($GLOBALS['currentUser'] -> coreAccess['personal_messages']) || $GLOBALS['currentUser'] -> coreAccess['personal_messages'] != 'hidden') && $GLOBALS['configuration']['disable_messages'] != 1) {
                $menu['general']['messages'] = array('title' => _MESSAGES, 'link' => $_SESSION['s_type'].".php?ctg=messages", 'image' => 'mail', 'target' => "mainframe");
            }
   if (!isset($GLOBALS['currentUser'] -> coreAccess['dashboard']) || $GLOBALS['currentUser'] -> coreAccess['dashboard'] != 'hidden') {
    $menu['general']['personal'] = array('title' => _SETTINGS, 'link' => 'student.php?ctg=personal', 'image' => 'user', 'target' => "mainframe");
   }
            $menu['general']['logout'] = array('title' => _LOGOUT, 'link' => '/index.php?logout=true', 'image' => 'logout', 'target' => "mainframe");
        break;
    }
    return $menu;
}
/**

* Converts a timestamp interval to time interval

*

* This function is used to convert the interval specified into a human - readable format.

* <br/> Example:

* <code>

* $timestamp_from = mktime(10, 34, 27, 10, 7, 2005);

* $timestamp_to = mktime(11, 47, 4, 10, 7, 2005);

* $interval = $timestamp_to - $timestamp_from;

* print_r(eF_convertIntervalToTime($interval));

* </code>

* Returns:

* <code>

*Array

*(

*    [hours] => 1

*    [minutes] => 12

*    [seconds] => 37

*)

* </code>

*/
function eF_convertIntervalToTime($interval, $ago = false)
{
    $seconds = $interval % 60;
    $minutes = (($interval - $seconds) / 60) % 60;
    $hours = ($interval - $seconds - ($minutes * 60)) / 3600;
    if ($ago) {
        if ($hours > 24) {
            $str = round($hours/24).' '.mb_strtolower(round($hours/24) == 1 ? _DAY : _DAYS);
            if (round($hours / 24) == 1 && $hours % 24 >= 1) {
                $str.= ' '.($hours % 24).' '.mb_strtolower($hours == 1 ? _HOUR : _HOURS);
            }
            return $str;
        } elseif ($hours > 0) {
            return $hours.' '.mb_strtolower($hours == 1 ? _HOUR : _HOURS);
        } elseif ($minutes > 0) {
            return $minutes.' '.mb_strtolower($minutes == 1 ? _MINUTE : _MINUTES);
        } else {
            return $seconds.' '.mb_strtolower($seconds == 1 ? _SECOND : _SECONDS);
        }
    } else {
        return array('hours' => $hours, 'minutes' => $minutes, 'seconds' => $seconds);
    }
}
function eF_getSurveyInfo($lesson_id){
    $survey_about = eF_getTableData("surveys","id,survey_code,survey_name,start_date,end_date,status","lessons_ID=".$lesson_id);
    $survey_questions = array();
    for($i = 0 ; $i < sizeof($survey_about) ; $i ++)
        $survey_questions[$i] = eF_getTableData("questions_to_surveys","count(*)","surveys_ID=".$survey_about[$i]['id']);
    $data = array('survey_info' => $survey_about,
                  'survey_questions' => $survey_questions);
    return $data;
}
function eF_getSurveyQuestions($survey_id){
    $data = eF_getTableData("questions_to_surveys","id,surveys_ID,father_ID,type,question,answers,created,info","surveys_ID=".$survey_id,"father_ID ASC");
    if(sizeof($data) == 0)
        return 0;
    else
    return $data;
}
function eF_getSurveyStatistics($survey_id){
    $survey_questions = eF_getTableData("questions_to_surveys","type,question,answers","surveys_ID=".$survey_id,"father_ID ASC");
    $done_users = eF_getTableData("users_to_done_surveys","users_LOGIN","surveys_ID=".$survey_id);
    $votes = array();
    for($i = 0 ; $i < sizeof($done_users) ; $i +=1){
        $user = '"'.$done_users[$i]['users_LOGIN'].'"';
        $user_answers = eF_getTableData("survey_questions_done sqd ,questions_to_surveys qts","sqd.user_answers,qts.type","sqd.question_ID = qts.id AND sqd.surveys_ID=".$survey_id." AND qts.surveys_ID=".$survey_id." AND qts.surveys_ID=sqd.surveys_ID AND sqd.users_LOGIN=".$user,"qts.father_ID ASC");
        $vote = array();
        for($j = 0 ; $j < sizeof($survey_questions) ; $j+=1){
            if($user_answers[$j]['type'] == 'multiple_many'){
                $choices = unserialize($survey_questions[$j]['answers']);
                $type = array_keys($choices);
                $keys = array_keys($choices[$type[0]]);
                $needles = unserialize($user_answers[$j]['user_answers']);
                for($k = 0 ; $k < sizeof($choices[$type[0]]) ; $k +=1){
                    $place = array_search($needles[$k],$choices[$type[0]]);
                    if((string)$place != ''){
                        $vote[$j][$keys[$k]] =$place;
                    }else{
                        $vote[$j][$keys[$k]] == -1;
                    }
                }
            }else{
                if($user_answers[$j]['type'] != 'development'){
                    $choices = unserialize($survey_questions[$j]['answers']);
                    $needle = unserialize($user_answers[$j]['user_answers']);
                    $type = array_keys($choices);
                    $keys = array_keys($choices[$type[0]]);
                    $place = array_search($needle,$choices[$type[0]]);
                    $vote[$j]=$place;
                }else{
                    $vote[$j] =1;
                }
            }
        }
        $votes[$i] = $vote;
    }
    return array('questions' => $survey_questions , 'votes' => $votes);
}
/**

* Check if the specified needle exists in the database

*

* This function is used to check against the database if the specified needle already exists.

* Type may be one of login, mail

* Example:<br>

* <code>

* eF_checkNotExist('john', 'login');                  //returns true if it exists

* eF_checkNotExist('jdoe@somewhere.net', 'mail');     //returns true if it exists

* </code>

*

* @param string $needle The string to check for

* @param string type The data type

* @return bool True if the string exists

* @version 1.0

*/
function eF_checkNotExist($needle, $type) {
    switch ($type) {
        case 'login':
            $result = eF_getTableData("users", "login", "login='$needle' and archive=0");
            break;
        case 'email':
            $result = eF_getTableData("users", "email", "email='$needle'");
            break;
        case 'user_type':
            $result = eF_getTableData("user_types", "user_type", "user_type='$needle'");
            break;
        case 'course':
            $result = eF_getTableData("courses", "name", "name='$needle'");
            break;
        default:
            $result = array();
            break;
    }
    if (sizeof($result) > 0) {
        return false;
    } else {
        return true;
    }
}
function pr($ar) {
    echo "<pre>";print_r($ar);echo "</pre>";
}
function pre($ar) {
    echo "<pre>";print_r($ar);echo "</pre>";exit;
}
function vd($ar) {
    echo "<pre>";var_dump($ar);echo "</pre>";
}
/**

* Filters data array

*

* This function is used to filter the specified array according to the given filter.

* Each array element is checked against the filter, and if the filter is not contained

* in any data "row", the row is removed from the array. The function does not reindex

* array keys

*

* @param array $data The 2-dimensional data array

* @param string $filter The search filter

* @return array The new array

* @version 1.0

*/
function eF_filterData($data, $filter) {
 $filter = trim(mb_strtolower($filter), '||');
 if ($filter) {
     foreach ($data as $key => $value) {
         $imploded_string = implode(",", $value); //Instead of checking each row value one-by-one, check it all at once
         if (strpos(mb_strtolower($imploded_string), $filter) === false) {
             unset($data[$key]);
         }
     }
 }
    return $data;
}
/**

* Function to return the relative to the www/ path of an image file

* If the $imageFile contains the "modules" string, then it is assumed to exist

* inside the modules folder and therefore its path must be changed to be addressed

* correctly from the eFront functions

* @param $imageFile the file name of the Image

* @return the string of the relative path of the Image

* @version 1.0

*

**/
function eF_getRelativeModuleImagePath($imageFile) {
    // If an image inside hte
    if ($position = strpos($imageFile, "modules")) {
        $image_path = G_SERVERNAME.substr($imageFile, $position);
    } else {
        $image_path = "../".$imageFile;
    }
    return $image_path;
}
/**

* Function to return an array with all links

* that have been defined for all modules ($modules) for the current user

* type for the menu defined by parameter ($menu_category)

*

*<br>Example:

* $modules = $user -> getModules();

* $sysMenus = eF_getModuleMenu($modules, "system");

*

* @param $modules the module list,

*        $menu_category: one of "system" | "lessons" | "users" | "organization" | "tools" | "current_lesson" | "links" (for "other" menus)

* @return the array of the links found for this menu

* @version 1.0

*

**/
function eF_getModuleMenu($modules, $menu_category) {
    $links = array();
    foreach ($modules as $module) {
        if ($menu_category != "current_lesson" || ($menu_category == "current_lesson" && $GLOBALS['currentLesson'] -> options[$module -> className])) {
            $sidebarLinks = $module -> getSidebarLinkInfo();
            isset($sidebarLinks[$menu_category]) ? $sidebarLinks = $sidebarLinks[$menu_category] : $sidebarLinks = array();
            foreach ($sidebarLinks as $mod_link) {
                // The "moduleLink" in the following array denotes special treatment
                $links[] = array("id" => $module -> className . (($mod_link['id'])? "_".$mod_link['id']:""),
                                 "image" => eF_getRelativeModuleImagePath($mod_link['image']),
                                 "link" => $mod_link['link'],
                                 "title" => $mod_link['title'],
                                 "moduleLink" => "1",
                                 "eFrontExtensions" => $mod_link['eFrontExtensions'],
                                 "class" => "menuOption");
            }
        }
    }
    return $links;
}
/**

* Function to return an array with objects regarding

* ALL module classes installed in the system

* and not only the ones for this user type

* Used for checking for events to be executed

*/
function eF_loadAllModules($onlyActive = false, $disregardUser = false) {
    if ($onlyActive) {
     $modulesDB = eF_getTableData("modules","*","active=1");
    } else {
     $modulesDB = eF_getTableData("modules","*","");
    }
    $modules = array();
    if (!$disregardUser) {
     global $currentUser;
     if ($currentUser) {
      // Get all modules enabled
      foreach ($modulesDB as $module) {
       $folder = $module['position'];
       $className = $module['className'];
       // If a module is to be updated then its class should not be loaded now
       if (!($currentUser -> getType() == "administrator" && $_GET['ctg'] == "control_panel" && $_GET['op'] == "modules" && $_GET['upgrade'] == $className)) {
        if(is_file(G_MODULESPATH.$folder."/".$className.".class.php")) {
         require_once G_MODULESPATH.$folder."/".$className.".class.php";
         if (class_exists($className)) {
          $modules[$className] = new $className("", $folder);
         } else {
          $message = '"'.$className .'" '. _MODULECLASSNOTEXISTSIN . ' ' .G_MODULESPATH.$folder.'/'.$className.'.class.php';
          $message_type = 'failure';
         }
        } else {
         $message = _ERRORLOADINGMODULE;
         $message_type = "failure";
        }
       }
      }
     }
    } else {
     foreach ($modulesDB as $module) {
      $folder = $module['position'];
      $className = $module['className'];
      if (is_file(G_MODULESPATH.$folder."/".$className.".class.php")) {
       require_once G_MODULESPATH.$folder."/".$className.".class.php";
       if (class_exists($className)) {
        $modules[$className] = new $className("", $folder);
       }
      }
     }
    }
    return $modules;
}
/**

 * For php 5.1.x that lacks memory_get_peak_usage(), fall back to memory_get_usage()

 */
if (!function_exists('memory_get_peak_usage')) {
 function memory_get_peak_usage() {
  return memory_get_usage();
 }
}
/**

 * Supplementary json_encode in case php version is < 5.2 (taken from http://gr.php.net/json_encode)

 */
if (!function_exists('json_encode'))
{
    function json_encode($a=false)
    {
        if (is_null($a)) return 'null';
        if ($a === false) return 'false';
        if ($a === true) return 'true';
        if (is_scalar($a))
        {
            if (is_float($a))
            {
                // Always use "." for floats.
                return floatval(str_replace(",", ".", strval($a)));
            }
            if (is_string($a))
            {
                static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
            }
            else
            return $a;
        }
        $isList = true;
        for ($i = 0, reset($a); $i < count($a); $i++, next($a))
        {
            if (key($a) !== $i)
            {
                $isList = false;
                break;
            }
        }
        $result = array();
        if ($isList)
        {
            foreach ($a as $v) $result[] = json_encode($v);
            return '[' . join(',', $result) . ']';
        }
        else
        {
            foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
            return '{' . join(',', $result) . '}';
        }
    }
}
// Normalize picture to $maxNewWidth x $maxNewHeightof dimensions
function eF_getNormalizedDims($filename, $maxNewWidth, $maxNewHeight) {
    list($width, $height) = getimagesize($filename);
    $newwidth = $width;
    $newheight = $height;
    while($newwidth > $maxNewWidth || $newheight > $maxNewHeight) {
        if ($newwidth > $maxNewWidth) {
            $newheight = ceil($maxNewWidth * $newheight/$newwidth);
            $newwidth = $maxNewWidth;
        }
        if ($newheight > $maxNewHeight) {
            $newwidth = ceil($maxNewHeight * $newwidth/$newheight);
            $newheight = $maxNewHeight;
        }
    }
    return array($newwidth, $newheight);
}
// Normalize picture of type $extension (png, gif, jpg or jpeg) with $filename
// to dimensions to $maxNewWidth x DimY or DimX x $maxNewHeight
// and overwriting existing picture with the normalized one
function eF_normalizeImage($filename, $extension, $maxNewWidth, $maxNewHeight) {
    if (!extension_loaded('gd') && !extension_loaded('gd2')) {
        return false;
    }
    // Get current dimensions
    list($width, $height) = getimagesize($filename);
    // Get normalized dimensions
    list($newwidth, $newheight) = eF_getNormalizedDims($filename, $maxNewWidth, $maxNewHeight);
    return eF_createImage($filename, $extension, $width, $height, $newwidth, $newheight);
}
// Recreate an image (width x height) with new dimensions (newwidth x newheight)
function eF_createImage($filename, $extension, $width, $height, $newwidth, $newheight) {
    if (!extension_loaded('gd') && !extension_loaded('gd2')) {
        return false;
    }
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    if ($extension == "png") {
        $source =imagecreatefrompng($filename);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    } else if ($extension == "gif") {
        $source =imagecreatefromgif($filename);
        imagecolortransparent($thumb, imagecolorallocate($thumb, 0, 0, 0));
        imagealphablending($thumb, true);
        imagesavealpha($thumb, true);
    } else {
        $source = imagecreatefromjpeg($filename);
    }
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    if ($extension == "png") {
        return imagepng($thumb, $filename, 0, PNG_ALL_FILTERS);
    } else if ($extension == "gif") {
        return imagegif($thumb, $filename, 500);
    } else {
        return imagejpeg($thumb, $filename,100);
    }
}
/**

 *

 * @param $str

 * @return unknown_type

 */
function utf8ToUnicode(&$str)
{
  $mState = 0; // cached expected number of octets after the current octet
                   // until the beginning of the next UTF8 character sequence
  $mUcs4 = 0; // cached Unicode character
  $mBytes = 1; // cached expected number of octets in the current sequence
  $out = array();
  $len = strlen($str);
  for($i = 0; $i < $len; $i++) {
    $in = ord($str{$i});
    if (0 == $mState) {
      // When mState is zero we expect either a US-ASCII character or a
      // multi-octet sequence.
      if (0 == (0x80 & ($in))) {
        // US-ASCII, pass straight through.
        $out[] = $in;
        $mBytes = 1;
      } else if (0xC0 == (0xE0 & ($in))) {
        // First octet of 2 octet sequence
        $mUcs4 = ($in);
        $mUcs4 = ($mUcs4 & 0x1F) << 6;
        $mState = 1;
        $mBytes = 2;
      } else if (0xE0 == (0xF0 & ($in))) {
        // First octet of 3 octet sequence
        $mUcs4 = ($in);
        $mUcs4 = ($mUcs4 & 0x0F) << 12;
        $mState = 2;
        $mBytes = 3;
      } else if (0xF0 == (0xF8 & ($in))) {
        // First octet of 4 octet sequence
        $mUcs4 = ($in);
        $mUcs4 = ($mUcs4 & 0x07) << 18;
        $mState = 3;
        $mBytes = 4;
      } else if (0xF8 == (0xFC & ($in))) {
        /* First octet of 5 octet sequence.

         *

         * This is illegal because the encoded codepoint must be either

         * (a) not the shortest form or

         * (b) outside the Unicode range of 0-0x10FFFF.

         * Rather than trying to resynchronize, we will carry on until the end

         * of the sequence and let the later error handling code catch it.

         */
        $mUcs4 = ($in);
        $mUcs4 = ($mUcs4 & 0x03) << 24;
        $mState = 4;
        $mBytes = 5;
      } else if (0xFC == (0xFE & ($in))) {
        // First octet of 6 octet sequence, see comments for 5 octet sequence.
        $mUcs4 = ($in);
        $mUcs4 = ($mUcs4 & 1) << 30;
        $mState = 5;
        $mBytes = 6;
      } else {
        /* Current octet is neither in the US-ASCII range nor a legal first

         * octet of a multi-octet sequence.

         */
        return false;
      }
    } else {
      // When mState is non-zero, we expect a continuation of the multi-octet
      // sequence
      if (0x80 == (0xC0 & ($in))) {
        // Legal continuation.
        $shift = ($mState - 1) * 6;
        $tmp = $in;
        $tmp = ($tmp & 0x0000003F) << $shift;
        $mUcs4 |= $tmp;
        if (0 == --$mState) {
          /* End of the multi-octet sequence. mUcs4 now contains the final

           * Unicode codepoint to be output

           *

           * Check for illegal sequences and codepoints.

           */
          // From Unicode 3.1, non-shortest form is illegal
          if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
              ((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
              ((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
              (4 < $mBytes) ||
              // From Unicode 3.2, surrogate characters are illegal
              (($mUcs4 & 0xFFFFF800) == 0xD800) ||
              // Codepoints outside the Unicode range are illegal
              ($mUcs4 > 0x10FFFF)) {
            return false;
          }
          if (0xFEFF != $mUcs4) {
            // BOM is legal but we don't want to output it
            $out[] = $mUcs4;
          }
          //initialize UTF8 cache
          $mState = 0;
          $mUcs4 = 0;
          $mBytes = 1;
        }
      } else {
        /* ((0xC0 & (*in) != 0x80) && (mState != 0))

         *

         * Incomplete multi-octet sequence.

         */
        return false;
      }
    }
  }
  $outstr = "";
  for ($i = 0; $i < sizeof($out); $i++){
    $outstr.= "\u".$out[$i];
    $outstr.= "\u".$out[$i];
  }
  return $outstr;
}
/**

* Returns appropriate date format string for functions

* Quickformat (AddElement with 'date' parameter and eF_template_html_select_date

* for field_order parameter



* @param bool $returnSpaces If it is true it returns string valid for AddElement, else valid for eF_template_html_select_date

* @param string $format Input date format string (in the format of database). By default $GLOBALS['configuration']['date_format']

* @return string The appropriate date format string



*/
function eF_dateFormat($returnSpaces = true, $format = false)
{
    if ($format == false) {
        $format = $GLOBALS['configuration']['date_format'];
    }
    if ($returnSpaces) {
        switch ($format) {
        case "YYYY/MM/DD":
            $output = "Y m d";
            break;
        case "DD/MM/YYYY":
            $output = "d m Y";
            break;
        case "MM/DD/YYYY":
            $output = "m d Y";
            break;
        default :
            $output = "d m Y";
            break;
        }
    } else {
        switch ($format) {
            case "YYYY/MM/DD":
                $output = "YMD";
                break;
            case "DD/MM/YYYY":
                $output = "DMY";
                break;
            case "MM/DD/YYYY":
                $output = "MDY";
                break;
            default :
                $output = "DMY";
                break;
            }
    }
    return $output;
}
function eF_assignSupervisorMissingSubBranchesRecursive() {
 $count = 0;
 $fixed = true;
 while ($fixed && $count++ < 10) {
  $fixed = eF_assignSupervisorMissingSubBranches();
  eF_getRights();
 }
 //exit;
}
function eF_assignSupervisorMissingSubBranches() {
//pr($_SESSION['supervises_branches']);
 $currentUser = $GLOBALS['currentUser'];
 $supervisor_at_branches = eF_getRights();
 if (($currentUser -> aspects['hcd'] instanceOf EfrontSupervisor) || ($currentUser -> aspects['hcd'] instanceOf EfrontHcdAdministrator)) {
  $derivedSupervisorAtBranches = array_keys($currentUser -> aspects['hcd'] -> getSupervisedBranchesRecursive()); //This dynamically calculates the branches that the user is supervisor. It is used to automatically fix discrepancies (for example, when a user is supervisor in branch A and not in branch A->B->C)
 } else {
  $derivedSupervisorAtBranches = array();
 }
 $fixed = false;
 foreach ($derivedSupervisorAtBranches as $branchId) {
  if (!in_array($branchId, $supervisor_at_branches['branch_ID'])) {
   $fields = array('users_login' => $currentUser -> user['login'],
       'supervisor' => 1,
       'assigned' => 0,
       'branch_ID' => $branchId);
   eF_insertTableData("module_hcd_employee_works_at_branch", $fields);
   $fixed = true;
  }
 }
 return $fixed;
}
/**

 * Function that inserts automatic lesson skills and course skills

 * for the educational version, if they do not already exist

 */
function eF_insertAutoLessonCourseSkills() {
                  // Skillgap tests related code
                    // Two conditions must be fulfilled - for educational version:
                    // - every lesson offers a lesson specific skill [I](Knowledge of lesson: xxx) (and every course the same [II])
                    // - every question is automatically linked to the skill of the lesson is belongs to [III]
                    // [I] Check and addition of all existing lesson related skills
                    $lessons = eF_getTableData("lessons","*","");
                    $lesson_skills = eF_getTableDataFlat("module_hcd_skills NATURAL JOIN module_hcd_lesson_offers_skill", "*", "categories_ID = -1");
                    foreach($lessons as $lesson) {
                        // If the lesson is not provided only through a course - where the course skill applies
                        if ($lesson['course_only'] == 0) {
                            // If the lesson's skill is not currently logged to the table of lesson-skills
                            if (!in_array($lesson['id'], $lesson_skills['lesson_ID'])) {
                                $new_skill_id = eF_insertTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFLESSON . " ". $lesson['name'], "categories_ID" => -1));
                                if (!$to_add_to_lesson_offers) {
                                    $to_add_to_lesson_offers = "('".$lesson['id'] . "','". $new_skill_id . "')";
                                } else {
                                    $to_add_to_lesson_offers .= ",('".$lesson['id'] . "','". $new_skill_id . "')";
                                }
                            }
                        }
                    }
                    if (isset($to_add_to_lesson_offers)) {
                        eF_executeNew("INSERT INTO module_hcd_lesson_offers_skill (lesson_ID,skill_ID) VALUES " . $to_add_to_lesson_offers);
                    }
                    // [II] Check and addition of all existing course related skills
                    $courses = eF_getTableData("courses","*","");
                    $course_skills = eF_getTableDataFlat("module_hcd_skills NATURAL JOIN module_hcd_course_offers_skill", "*", "categories_ID = -1");
                    foreach($courses as $course) {
                        // If the course is not provided only through a course - where the course skill applies
                        if ($course['course_only'] == 0) {
                            // If the course's skill is not currently logged to the table of course-skills
                            if (!in_array($course['id'], $course_skills['courses_ID'])) {
                                $new_skill_id = eF_insertTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFCOURSE. " ". $course['name'], "categories_ID" => -1));
                                if (!$to_add_to_course_offers) {
                                    $to_add_to_course_offers = "('".$course['id'] . "','". $new_skill_id . "')";
                                } else {
                                    $to_add_to_course_offers .= ",('".$course['id'] . "','". $new_skill_id . "')";
                                }
                            }
                        }
                    }
                    if (isset($to_add_to_course_offers)) {
                        eF_executeNew("INSERT INTO module_hcd_course_offers_skill (courses_ID,skill_ID) VALUES " . $to_add_to_course_offers);
                    }
                    /// [III] Each question should offer the skill of the lesson it belongs or of the course its lesson belongs
                    // ATTENTION: The following works correctly because it succeeds the code where all lessons have a corresponding skill - otherwise problem
                    $questions = eF_getTableData("questions LEFT OUTER JOIN questions_to_skills ON questions.id = questions_to_skills.questions_ID JOIN lessons ON lessons.id = questions.lessons_ID","questions.id, lessons.course_only, questions.lessons_ID, questions_to_skills.skills_ID", "questions.lessons_ID <> 0");
                    // This returns a 1-1 table: 1 lesson to its 1 corresponding skill
                    $result = eF_getTableData("module_hcd_lesson_offers_skill JOIN module_hcd_skills ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID", "module_hcd_lesson_offers_skill.*", "module_hcd_skills.categories_ID = -1");
                    //$skills = eF_getTableData("questions LEFT OUTER JOIN (questions_to_skills JOIN module_hcd_lesson_offers_skill ON questions_to_skills.skills_ID = module_hcd_lesson_offers_skill.skill_ID) ON questions.id = questions_to_skills.questions_ID JOIN lessons ON lessons.id = questions.lessons_ID WHERE questions.lessons_ID <> 0", "questions.id, questions.lessons_ID, module_hcd_lesson_offers_skill.lesson_ID,lessons.course_only", "");
                    $lesson_to_skill = array();
                    foreach ($result as $rid => $skill) {
                        $lesson_to_skill[$skill['lesson_ID']] = $skill['skill_ID'];
                    }
                    $lessons_only_from_courses = array();
                    // DB Insertion inside a loop - well only once...
                    foreach ($questions as $qid => $question) {
                        //  The question belongs to a lesson outside a course with a skill_ID that is among the lesson related skill IDs or NULL and not equal to the skill of the specific lesson skill, then insert it
                        if ($question['course_only'] == 0) {
                            if ($question['skills_ID'] != $lesson_to_skill[$question['lessons_ID']] && (!$question['skills_ID'] || in_array($question['skills_ID'], $lesson_to_skill))) {
                                eF_insertTableData("questions_to_skills", array("questions_ID" => $question['id'], "skills_ID" => $lesson_to_skill[$question['lessons_ID']], "relevance" => 2));
                            }
                            unset($questions[$qid]);
                        } else {
                            $lessons_only_from_courses[] = $question['lessons_ID'];
                        }
                    }
                    // Now correlate questions to the skills of courses that have course_only lessons with those questions
                    // This returns a 1-1 table: 1 course to its 1 corresponding skill
                    $result = eF_getTableData("module_hcd_course_offers_skill JOIN module_hcd_skills ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID", "module_hcd_course_offers_skill.*", "module_hcd_skills.categories_ID = -1 AND module_hcd_course_offers_skill.courses_ID IN ('". implode("','", $lessons_only_from_courses) ."')");
                    $course_to_skill = array();
                    foreach ($result as $rid => $skill) {
                        $course_to_skill[$skill['courses_ID']] = $skill['skill_ID'];
                    }
}
/**

 * Function that checks that the value for an eFront social module is valid

 */
function eF_checkSocialModuleExistance($value) {
    // Value zero is used to denote all social modules
    if ($value == 0) {
        return true;
    }
    $l = log($value, 2);
    // The log should be an integer from 0 for 1 to 10 for 1024
    if ($l >= 0 && $l < SOCIAL_MODULES_ALL && ($l - intval($l) == 0)) {
        return true;
    }
    return false;
}
/*

 * Returning an array with the world's timezones

 */
function eF_getTimezones() {
  $timezones = array();
  $timezones['Pacific/Kwajalein'] = "(GMT -12:00) Eniwetok, Kwajalein";
        $timezones['Pacific/Samoa'] = "(GMT -11:00) Midway Is, Samoa";
        $timezones['Pacific/Honolulu'] = "(GMT -10:00) Hawaii";
        $timezones['US/Alaska'] = "(GMT -09:00) Alaska";
        $timezones['America/Los_Angeles'] = "(GMT -08:00) Pacific Time (US & Canada), Tijuana";
        $timezones['America/Mazatlan'] = "(GMT -07:00) Chihuahua, La Paz, Mazatlan";
        $timezones['America/Phoenix'] = "(GMT -07:00) Mountain Time (US & Canada)";
        $timezones['America/Chicago'] = "(GMT -07:00) Arizona";
        $timezones['America/Costa_Rica']= "(GMT -6:00) San Jose";
        $timezones['America/Chicago'] = "(GMT -06:00) Central Time (US & Canada)";
        $timezones['America/Mexico_City'] = "(GMT -06:00) Mexico City, Tegucigalpa";
        $timezones['Canada/Saskatchewan'] = "(GMT -06:00) Saskatchewan";
        $timezones['America/New_York'] = "(GMT -05:00) Eastern Time (US & Canada)";
        $timezones['America/Indiana/Indianapolis'] = "(GMT -05:00) Indiana (East)";
        $timezones['America/Bogota'] = "(GMT -05:00) Bogota, Lima, Quito";
  $timezones['America/Caracas'] = "(GMT -04:30) Caracas";
        $timezones['America/Santiago'] = "(GMT -04:00) Atlantic Time (Canada), Santiago";
  $timezones['America/La_Paz'] = "(GMT -04:00) La Paz";
        $timezones['Canada/Newfoundland'] = "(GMT -03:30) Newfoundland";
        $timezones['America/Buenos_Aires'] = "(GMT -03:00) Buenos Aires, Georgetown, Brasilia, Greenland";
        $timezones['Etc/GMT+2'] = "(GMT -02:00) Mid-Atlantic";
        $timezones['Atlantic/Azores'] = "(GMT -01:00) Azores";
        $timezones['Atlantic/Cape_Verde'] = "(GMT -01:00) Cape Verde Island";
        $timezones['Africa/Casablanca'] = "(GMT 00:00) Casablanca, Monrovia";
        $timezones['Europe/London'] = "(GMT 00:00) Greenwich Mean Time: Dublin, Edinburgh, Lisbon, London";
        $timezones['Europe/Paris'] = "(GMT +01:00) Amsterdam, CopenHagen, Madrid, Paris, Vilnius, West Central Africa";
        $timezones['Europe/Zagreb'] = "(GMT +01:00) Belgrade, Sarajevo, Skopje, Sofija, Zagreb";
        $timezones['Europe/Bratislava'] = "(GMT +01:00) Bratislava, Budapest, Ljubljana, Prague, Warsaw";
        $timezones['Europe/Vienna'] = "(GMT +01:00) Brussels, Berlin, Bern, Rome, Stockholm, Vienna";
        $timezones['Africa/Cairo'] = "(GMT +02:00) Cairo";
        $timezones['Africa/Harare'] = "(GMT +02:00) Harare, Pretoria";
        $timezones['Asia/Jerusalem'] = "(GMT +02:00) Israel";
        $timezones['Europe/Bucharest'] = "(GMT +02:00) Bucharest";
        $timezones['Europe/Helsinki'] = "(GMT +02:00) Helsinki, Riga, Tallinn";
        $timezones['Europe/Athens'] = "(GMT +02:00) Athens, Istanbul, Minsk";
        $timezones['Asia/Kuwait'] = "(GMT +03:00) Kuwait, Riyadh";
        $timezones['Africa/Nairobi'] = "(GMT +03:00) Nairobi";
        $timezones['Asia/Baghdad'] = "(GMT +03:00) Baghdad";
        $timezones['Europe/Moscow'] = "(GMT +03:00) Moscow, St. Petersburg, Volgograd";
        $timezones['Asia/Tehran'] = "(GMT +03:30) Tehran +3:30";
        $timezones['Asia/Muscat'] = "(GMT +04:00) Abu Dhabi, Muscat";
        $timezones['Asia/Baku'] = "(GMT +04:00) Baku, Tbilisi";
        $timezones['Asia/Kabul'] = "(GMT +04:00) Kabul";
        $timezones['Asia/Karachi'] = "(GMT +05:00) Islamabad, Karachi, Tashkent";
        $timezones['Asia/Yekaterinburg'] = "(GMT +05:00) Ekaterinburg";
        $timezones['Asia/Calcutta'] = "(GMT +05:30) Bombay, Calcutta, Madras, New Delhi";
        $timezones['Asia/Kathmandu'] = "(GMT +05:45) Kathmandu";
        $timezones['Asia/Almaty'] = "(GMT +06:00) Almaty, Dhaka";
        $timezones['Asia/Colombo'] = "(GMT +06:00) Columbo";
        $timezones['Asia/Novosibirsk'] = "(GMT +06:00) Almaty, Novosibirsk";
        $timezones['Asia/Rangoon'] = "(GMT +06:30) Rangoon";
        $timezones['Asia/Bangkok'] = "(GMT +07:00) Bangkok, Hanoi, Jakarta";
        $timezones['Asia/Krasnoyarsk'] = "(GMT +07:00) Krasnoyarsk";
        $timezones['Asia/Hong_Kong'] = "(GMT +08:00) Beijing, Chongqing, Hong Kong, Urumqi";
        $timezones['Australia/Perth'] = "(GMT +08:00) Perth";
        $timezones['Asia/Singapore'] = "(GMT +08:00) Singapore";
        $timezones['Asia/Taipei'] = "(GMT +08:00) Taipei";
        $timezones['Asia/Irkutsk'] = "(GMT +08:00) Irkutsk, Ulaan Bataar";
        $timezones['Asia/Tokyo'] = "(GMT +09:00) Osaka, Sapporo, Tokyo";
        $timezones['Asia/Seoul'] = "(GMT +09:00) Seoul";
        $timezones['Asia/Yakutsk'] = "(GMT +09:00) Yakutsk";
        $timezones['Australia/Adelaide'] = "(GMT +09:30) Adelaide";
        $timezones['Australia/Darwin'] = "(GMT +09:30) Darwin";
        $timezones['Australia/Canberra'] = "(GMT +10:00) Canberra, Melbourne, Sydney";
        $timezones['Australia/Brisbane'] = "(GMT +10:00) Brisbane";
        $timezones['Pacific/Guam'] = "(GMT +10:00) Guam, Port Moresby";
        $timezones['Australia/Hobart'] = "(GMT +10:00) Hobart";
        $timezones['Asia/Vladivostok'] = "(GMT +10:00) Vladivostok";
        $timezones['Asia/Magadan'] = "(GMT +11:00) Magadan, Solomon Is, New Caledonia";
        $timezones['Pacific/Fiji'] = "(GMT +12:00) Fiji, Kamchatka, Marshall Is";
        $timezones['Pacific/Auckland'] = "(GMT +12:00) Auckland, Wellington";
        $timezones['Pacific/Tongatapu'] = "(GMT +13:00) Nuku'alofa";
        return $timezones;
}
/**

 * Detect agent browser

 *

 * @since 3.6.0

 * @return string The client's browser

 */
function detectBrowser() {
    $mobileAgents = array('iphone', 'ipad', 'ipod', 'blackberry', 'htc', 'palm', 'windows ce', 'opera mini', 'android', 'midp', 'symbian');
    $agent = $_SERVER['HTTP_USER_AGENT'];
    switch (true) {
        case preg_match("/(".implode("|", $mobileAgents).")/i", $agent) != 0: $browser = 'mobile'; break;
        case stripos($agent, 'firefox') !== false: $browser = 'firefox'; break;
        case stripos($agent, 'msie 6.0') !== false: $browser = 'ie6'; break;
        case stripos($agent, 'msie') !== false: $browser = 'ie'; break;
        case stripos($agent, 'chrome') !== false: $browser = 'chrome'; break;
        case stripos($agent, 'safari') !== false: $browser = 'safari'; break;
        default: $browser = 'ie'; break;
    }
    return $browser;
}
/**

 * Redirect to another page

 *

 * This function implements either server-side (php) or client side (javascript) redirection

 * <br/>Example:

 * <code>

 * </code>

 *

 * @param string $url The url to redirect to. If 'self' is used, it is equivalent to a reload (only it isn't)

 * @param boolean $js Whether to use js-based redirection

 * @param string $target which frame to reload (only applicable when $js is true). Can be 'top', 'window' or any frame name

 * @param boolean $retainUrl Whether to retain the url as it is

 * @since 3.6.0

 */
function eF_redirect($url, $js = false, $target = 'top', $retainUrl = false) {
 if (!$retainUrl) {
     $parts = parse_url($url);
     if (isset($parts['query']) && $parts['query']) {
         if ($GLOBALS['configuration']['encrypt_url']) {
             $parts['query'] = 'cru='.encryptString($parts['query']);
         }
         $parts['query'] = '?'.$parts['query'];
     } else {
         $parts['query'] = '';
     }
     $url = G_SERVERNAME.basename($parts['path']).$parts['query'];
 }
 session_write_close();
    if ($js) {
        echo "<script language='JavaScript'>$target.location='$url'</script>";
    } else {
        header("location:$url");
    }
    exit;
}
/**

 * Encrypt a string based on the specified parameter

 *

 * @param string $string The string to encode

 * @param string $method The method to use

 * @return string The encoded string

 * @since 3.6.0

 */
function encryptString($string, $method = 'base64') {
 $hashResidue = strrchr($string, '#');
 $string = str_replace($hashResidue, '', $string);
    switch ($method) {
        case 'rot13' : $encodedString = urlencode(str_rot13($string));break;
        case 'base64': $encodedString = urlencode(base64_encode($string));break;
        default : $encodedString = $string;break;
    }
    $encodedString .= $hashResidue;
    return $encodedString;
}
/**

 * Decode a string based on the specified parameter

 * If the string ends with #somechars, then this part will not be encrypted

 *

 * @param string $string The string to encode

 * @param string $method The method to use

 * @return string The decoded string

 * @since 3.6.0

 */
function decryptString($string, $method = 'base64') {
 $hashResidue = strrchr($string, '#');
 $string = str_replace($hashResidue, '', $string);
 switch ($method) {
        case 'rot13' : $decodedString = str_rot13(urldecode($string));break;
        case 'base64': $decodedString = base64_decode(urldecode($string));break;
        default : $decodedString = $string;break;
    }
    $decodedString .= $hashResidue;
    return $decodedString;
}
/**

 * This function decrypts only the part of a url that may be encrypted

 *

 * @param $url The url to decrypt

 * @param string $method The method to use

 * @return string The decoded string

 * @since 3.6.3

 */
function decryptUrl($url, $method = 'base64') {
 $parts = parse_url($url);
 parse_str($parts['query'], $query);
 mb_internal_encoding('utf-8'); //This must be put here due to PHP bug #48697
 if (decryptString($query['cru'])) {
  $urlString = array(decryptString($query['cru']));
 }
 unset($query['cru']);
 foreach ($query as $key => $value) {
  $urlString[] = "$key=$value";
 }
 $urlString = $parts['path'].'?'.implode('&', $urlString);
 return $urlString;
}
/**

* Prints a warning or error message

*

* This function prints a message in a yellow box with an exclamation mark. It is used when

* an important message must be displayed, such as a confirmation or a warning. If the $print

* variable is set, then the message is printed, otherwise it is returned in a string

*

* @param string $str The message to be printed

* @param bool $print If the message will be directly displayed, or returned in a string

* @return string The string with the message

* @version 1.0

*/
function eF_printMessage($str, $print = true, $message_type = '')
{
    if ($str) {
        if ($message_type == 'success') {
            $message = '
                <table border = "1" width = "100%" align = "center" bgcolor = "gray" rules = "none" style = "border-color:black">
                    <tr><td class = "message_success">
                            <img src = "images/32x32/success.png" title="'.$str.'" alt="'.$str.'">
                        </td><td width = "99%" class = "message_success" align = "center">
                            '.$str.'
                    </td></tr>
                </table><br/>';
        } else {
            $message = '
                <table border = "1" width = "100%" align = "center" bgcolor = "gray" rules = "none" style = "border-color:black">
                    <tr><td class="message">
                            <img src = "images/32x32/warning.png" title="'.$str.'" alt="'.$str.'">
                        </td><td width = "99%" class="message" align="center">
                            '.$str.'
                    </td></tr>
                </table><br/>';
        }
        if ($print) {
            print $message;
        } else {
            return $message;
        }
    }
}
/**

* Send an email

*

* This function is a custom wrapper function for PEAR::Mail class.

* <br>Example:

* <code>

* eF_mail('admin@efront.gr', 'Test email', 'Hello world!');

* </code>

* @param string $sender The email sender

* @param string $recipient The email recipient. In case of multiple recipients, these are specified with a comma separated list

* @param string $subject The email subject.

* @param string $content The email content.

* @return mixed It propagates the PEAR Mail result, which is true on success or PEAR_ERROR instance on failure

* @version 4.0

* Changes from version 3.0 to version 4.0:

* - Rewritten in order to use $GLOBALS['configuration'],

* - Fixed buggy behaviour

* - Fixed return results

*/
function eF_mail($sender, $recipient, $subject, $body, $attachments = false, $onlyText = false, $bcc = false) {
    if ($bcc) {
        $toField = 'Bcc';
    } else {
        $toField = 'To';
    }
    $hdrs = array('From' => $sender,
                  'Subject' => $subject,
                  $toField => $recipient,
                  'Date' => date("r"));
    if ($bcc) {
     $hdrs['To'] = 'noreply@'.$_SERVER["HTTP_HOST"];
    }
    $params = array("text_charset" => "UTF-8",
                    "html_charset" => "UTF-8",
                    "head_charset" => "UTF-8");
    $mime = new Mail_mime("\n");
    if (!$onlyText) {
        $mime -> setHTMLBody($body);
    } else {
        $mime -> setTXTBody($body);
    }
    if ($attachments) {
        $file = new EfrontFile($attachments[0]);
        $mime -> addAttachment($file['path'], $file['mime_type'], $file['physical_name']);
    }
    $body = $mime -> get($params);
    $hdrs = $mime -> headers($hdrs);
    $smtp = Mail::factory('smtp', array('auth' => $GLOBALS['configuration']['smtp_auth'] ? true : false,
                                         'host' => $GLOBALS['configuration']['smtp_host'],
                                         'password' => $GLOBALS['configuration']['smtp_pass'],
                                         'port' => $GLOBALS['configuration']['smtp_port'],
                                         'username' => $GLOBALS['configuration']['smtp_user'],
                                         'timeout' => $GLOBALS['configuration']['smtp_timeout']));
    $result = $smtp -> send($recipient, $hdrs, $body);
    return $result;
}
/*

 * Function regarding notification message bodies

 * The language denoted by the argument is picked and returned if that language tag <----...----> exists

 * Otherwise the default language is returned

 */
function eF_getCorrectLanguageMessage($message, $language) {
    $language_tag = "<------------------------".$language."------------------------>";
    $pos = strpos($message, $language_tag);
    if ($pos) {
        $message = substr($message, ($pos + strlen($language_tag)));
        // get message text until next language
        if (($i = strpos($message, "<------------------------"))) {
            return substr($message, 0, $i);
        } else {
            // this is the last language tag, return entire remaining message
            return $message;
        }
    } else {
        // this particular language tag is not defined, return default language
        if (($i = strpos($message, "<------------------------"))) {
            return substr($message, 0, $i);
        } else {
            // no language tag defined
            return $message;
        }
    }
}
/*

 * Function creating md5 digests in strings containing the

 * tag ###md5(...)###. The resulting string will remove the tag

 * and replace the inner bracket text with its md5 equivalent

 */
function eF_replaceMD5($message) {
    $pos = strpos($message, "###md5(");
    //echo "*****".$pos."****<BR>";
    if ($pos) {
        $remaining_msg = substr($message, $pos+7);
        //echo $remaining_msg."<BR>";
        $pos2 = strpos($remaining_msg, ")###");
        //echo "*****".$pos2."****<BR>";
        if ($pos2) {
            $message = substr($message, 0, $pos) . md5(substr($message, $pos+7, $pos2).G_MD5KEY) . eF_replaceMD5(substr($message, $pos+7+$pos2+4));
        }
    }
    return $message;
}
/**

* Get basic type of user  ...which is enum(student,professor,administrator)

*

* This function returns the basic user type  for the given lesson

*

* @param login $login, defaults to corresponding session variable

* @param int $lesson_id the current lesson id, defaults to corresponding session variable

* @return basic user_type of login

* @version 2.6

* @from now on, basic user_type is lesson specific.if value in users_to_lesson is NULL (because of an import), we take default basic user type from table users

* @deprecated

*/
function eF_getUserBasicType($login = false, $lessons_ID = false){
    if($login == false){
        $login = $_SESSION['s_login'];
    }
    if($lessons_ID == false){
        if (isset($_SESSION['s_lessons_ID']))
            $lessons_ID = $_SESSION['s_lessons_ID'];
    }
    $user = EfrontUserFactory :: factory($login);
    try{
        $lesson = new EfrontLesson($lessons_ID);
        $role = $user -> getRole($lesson -> lesson['id']);
    }
    catch (Exception $e){
        $role = $user -> user['user_type'];
    }
    if ($role != "student" && $role != "professor" && $role != "administrator" ){
        $res2 = eF_getTableData("user_types","basic_user_type","user_type='".$role."'");
        $user_type = $res2[0]['basic_user_type'];
    }else{
        $user_type = $role;
    }
    return $user_type;
}
function convertTimeToSeconds($time) {
 $time_parts = explode(":", $time);
 $seconds = round($time_parts[2] + $time_parts[1]*60 + $time_parts[0]*60*60);
 return $seconds;
}
function convertSecondsToTime($time) {
 $newTime = array();
 $newTime['hours'] = floor($time / 3600);
 $newTime['minutes'] = floor(($time % 3600) / 60);
 $newTime['seconds'] = floor(($time % 3600) % 60);
 return ($newTime);
}
/**

* Add time

*

* This function is used to add times represented as arrays

* in the form seconds,minutes,hours

* @param the two arrays conforming to these standards -

* the result will be stored on top of the first one

* @return the sum of the two arrays

* @version 1.0

*/
function addTime(&$a, $b) {
 $time1 = 3600 * $a['hours'] + 60 * $a['minutes'] + $a['seconds'];
 $time2 = 3600 * $b['hours'] + 60 * $b['minutes'] + $b['seconds'];
 $time1 += $time2;
 $a['hours'] = floor($time1 / 3600);
 $a['minutes'] = floor(($time1 % 3600) / 60);
 $a['seconds'] = floor(($time1 % 3600) % 60);
 /*

	$a['seconds'] += $b['seconds'];

	$a['minutes'] += $b['minutes'];





	// Confoming stage

	$extraminutes = floor($a['seconds'] / 60);

	$a['seconds'] = $a['seconds'] % 60;



	$a['minutes'] += $extraminutes;

	$extrahours = floor($a['minutes'] / 60);

	$a['minutes'] = $a['minutes'] % 60;



	$a['hours'] =



	*/
}
/**

 * Determine which entity we should count time for. This may be 'system', 'lesson' or 'unit', in

 * an array with id => entity key/value pair. For example,

 * array(43 => 'lesson')

 * array(653 => 'unit')

 * array(0 => 'system')		//'system' is always 0

 * @param string $url The url to parse

 */
function getUserTimeTarget($url) {
 if (isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID']) {
  $entity = array($_SESSION['s_lessons_ID'] => 'lesson');
 } else {
  $entity = array(0 => 'system');
 }
 $urlParts = parse_url($url);
 $queryParts = explode('&', $urlParts['query']);
 foreach($queryParts as $part) {
  $result = explode("=", $part);
  switch ($result[0]) {
   case 'view_unit':
   case 'package_ID': $entity = array($result[1] => 'unit'); break;
   default: break;
  }
 }
 return $entity;
}
/**

 * Get the time that the user has spent on this entity, during the active session

 * @param $entity The entity to calculate time fo

 */
function getUserLastTimeInTarget($entity) {
 $result = eF_getTableData("user_times", "time", "session_expired=0 and session_id = '".session_id()."' and users_LOGIN='".$_SESSION['s_login']."' and entity='".current($entity)."' and entity_id='".key($entity)."'");
 if (sizeof($result) > 0) {
  return $result[0]['time'];
 } else {
  return false;
 }
}
/**

 * Either refresh the 'time' field of the current user/session/entity, or create a new entry

 * if the user just entered an entity during this session

 */
function refreshLogin() {
 if ($_SESSION['s_login']) {
  $entity = getUserTimeTarget($_SERVER['REQUEST_URI']); //Something like 'system', 'lesson' or 'unit'
  $totalTimeSoFar = getUserLastTimeInTarget($entity); //The time the user has spent during this session, on this entity
  if ($totalTimeSoFar === false) {
   //Insert a new entry for this entity, to start counting time for
   $fields = array("session_timestamp" => time(),
       "session_id" => session_id(),
       "session_expired" => 0,
       "users_LOGIN" => $_SESSION['s_login'],
       "timestamp_now" => time(),
       "time" => 0,
       "lessons_ID" => $_SESSION['s_lessons_ID'] ? $_SESSION['s_lessons_ID'] : null,
       "courses_ID" => $_SESSION['s_courses_ID'] ? $_SESSION['s_courses_ID'] : null,
       "entity" => current($entity),
       "entity_id" => key($entity));
   eF_insertTableData("user_times", $fields);
  } else {
   //Update times for this entity
   $result = eF_executeNew("update user_times set time=time+(".time()."-timestamp_now),timestamp_now=".time()."
         where session_expired = 0 and session_id = '".session_id()."' and users_LOGIN = '".$_SESSION['s_login']."'
          and entity = '".current($entity)."' and entity_id = '".key($entity)."'");
  }
  eF_updateTableData("user_times", array("session_expired" => 1), "session_expired = 0 and session_id = '".session_id()."' and users_LOGIN = '".$_SESSION['s_login']."'
          and (entity != '".current($entity)."' or entity_id != '".key($entity)."')");
 }
}
function getMainScripts() {
 $mainScripts = array('EfrontScripts',
       'scriptaculous/prototype',
       'scriptaculous/scriptaculous',
       'scriptaculous/effects',
       'prototip/prototip',
       'efront_ajax',
                      'includes/events');
 return $mainScripts;
}
/**

* Clear templates cache

* This function is used to explicity clear templates cache from code where needed

* @version 1.0

*/
function clearTemplatesCache() {
 try {
  $cacheTree = new FileSystemTree(G_THEMECACHE, true);
  foreach (new EfrontDirectoryOnlyFilterIterator($cacheTree -> tree) as $value) {
   $value -> delete();
  }
 } catch (Exception $e) {}
}
class AjaxResultObject
{
 public $message = '';
 public $response = false;
 public function __construct($response, $message) {
  $this -> response = $response;
  $this -> message = $message;
 }
 public function display($return = false) {
  $output = json_encode(array('response' => $this -> response, 'message' => $this -> message));
  if ($return) {
   return $output;
  } else {
   echo $output;
  }
 }
}
