<?php

/**
* @package eFront
*/


function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
    if ($length == 0)
        return '';

    if (mb_strlen($string) > $length) {
        $length -= mb_strlen($etc);
        if (!$break_words && !$middle) {
            $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1));
        }
        if(!$middle) {
            return mb_substr($string, 0, $length).$etc;
        } else {
            return mb_substr($string, 0, $length/2) . $etc . mb_substr($string, -$length/2);
        }
    } else {
        return $string;
    }
}

function debug($mode = true) {
    ini_set("display_errors", true);
    if ($mode) {
        error_reporting(E_ALL);
        $GLOBALS['db']->debug=true;
    } else {
        error_reporting(E_ERROR);
        $GLOBALS['db']->debug=false;
    }
}

/**
 * Add slashes to parameter
 *
 * This function is used to conditionally perform an addslashes() to the specfified parameter,
 * based on the get_magic_quotes_gpc directive status. If the parameter is an array, then the
 * function is applied recursively to all its elements
 * If $checkGpc is false, eF_addSlashes calls addslashes without checking get_magic_quotes_gpc
 * $checkGpc should be false if Quickform exportValues is used (because exportValues performs a stripslashes operation)
 * <br>Example:
 * <code>
 * $values = eF_addSlashes($form -> exportValues(), false);     //slash POST variables from HTML_Quickform
 * </code>
 *
 * @param mixed $param The value to add slashes to, can be either a string or an array
 * @param bool $checkGpc If false function does not check get_magic_quotes_gpc directive status
 * @return mixed the slashed parameter
 * @since 3.5.1
 * @access pubic
 */
function eF_addSlashes($param, $checkGpc = true) {
    if (get_magic_quotes_gpc() && $checkGpc) {
        return $param;
    } else {
        if (is_array($param)) {
            array_walk_recursive($param, create_function('&$v, $k', '$v = addslashes($v);'));
            return $param;
        } else {
            return addslashes($param);
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
        default: break;
    }
    $dateString = iconv(_CHARSET, 'UTF-8', strftime($format, $timestamp));

    return $dateString;
}
/**
* Sort multi-dimensional arrays
*/
function eF_multiSort($array, $sort_by, $sort_order = 'asc') {
    if (!in_array($sort_by, array_keys(current($array)))) {
        return $array;
    }

    $keys        = array_keys($array);
    $sort_values = array();
    foreach ($array as $value) {
        $sort_values[] = $value[$sort_by];
    }
    if (is_numeric($sort_values[0])) {    //If the column consists of numeric data, we want the default sorting to be descending, so we reverse the parameter
            $sort_order == 'asc' ? $sort_order = SORT_DESC : $sort_order = SORT_ASC;
    } else {
            $sort_order == 'asc' ? $sort_order = SORT_ASC : $sort_order = SORT_DESC;
    }

    array_multisort($sort_values, $sort_order, $keys);
    foreach ($keys as $key) {
        //$temp[] = $array[$key];          //Use this in order to have keys reindexed
        $temp[$key] = $array[$key];        //Use this in order to have keys preserved
    }

    return $temp;
}

/**
* Get calendar events
*
* This function is used to retrieve calendar events for the current lesson.
* If a timestamp is specified, then only events for the day that this timestamp
* belongs to are returned. If timestamp is omitted, all events for the current
* lesson are returned
*
* @param int $type 0,1 or 2 corresponding Organization profile, Current lesson, All lessons calendar events
* @param int $timestamp The timestamp
* @return array the list of events on that day
* @version 1.0
*/
function eF_getCalendar($timestamp = false, $type = 1) {

    //$lessons_ID = $_SESSION['s_lessons_ID'];

    // The type is going to define the lessons array - for backwards compatibility
    // if no type is defined then events regarding all lessons are to be returned
    if ($type == 0) {
        $lessons = array("0" => "0");
    } else if ($type == 2) {
        $lessons = array("0" => $_SESSION['s_lessons_ID']);
    } else {
        $login = $_SESSION['s_login'];
        $lessons = array();

        global $currentUser;

        if ($currentUser -> getType() != 'administrator') {
            $tmp = eF_getTableData("users_to_lessons", "lessons_ID", "users_LOGIN='".$login."'");
        } else {
            $tmp = eF_getTableData("users_to_lessons", "lessons_ID", "");
        }
        for ($i = 0; $i < sizeof($tmp); $i++)
        {
            $lessons[] = $tmp[$i]['lessons_ID'];
        }
    }
    $l = implode(",", $lessons);

    if (!$timestamp || !eF_checkParameter($timestamp, 'timestamp')) {

        if (MODULE_HCD_INTERFACE) {
            $result = eF_getTableData("calendar c LEFT OUTER JOIN lessons l ON c.lessons_ID = l.ID", "c.id, c.timestamp, c.data, l.name", "c.lessons_ID in (".$l.") AND c.active=1", "timestamp");
        } else {
            $result = eF_getTableData("calendar c LEFT OUTER JOIN lessons l ON c.lessons_ID = l.ID", "c.id, c.timestamp, c.data, l.name", "c.lessons_ID in (".$l.") AND c.active=1", "timestamp");

//            $result = eF_getTableData("calendar c, lessons l", "c.id, c.timestamp, c.data, l.name", "c.lessons_ID in (".$l.") AND c.active=1 AND c.lessons_ID = l.ID", "timestamp");
        }
    } else {

        $timestamp_info = getdate($timestamp);
        $timestamp_from = mktime(0, 0, 0, $timestamp_info['mon'], $timestamp_info['mday'], $timestamp_info['year']);     //today first sec
        $timestamp_to   = mktime(23, 23, 59, $timestamp_info['mon'], $timestamp_info['mday'], $timestamp_info['year']);  //today last sec
        if (MODULE_HCD_INTERFACE) {
            $result = eF_getTableData("calendar c LEFT OUTER JOIN lessons l ON c.lessons_ID = l.ID", "c.id, c.timestamp, c.data, l.name", "c.lessons_ID in (".$l.") AND c.active=1 AND timestamp >= ".($timestamp_from)." AND timestamp <= ".($timestamp_to), "timestamp");
        } else {
            $result = eF_getTableData("calendar c LEFT OUTER JOIN lessons l ON c.lessons_ID = l.ID", "c.id, c.timestamp, c.data, l.name", "c.lessons_ID in (".$l.") AND c.active=1 AND timestamp >= ".($timestamp_from)." AND timestamp <= ".($timestamp_to), "timestamp");

//            $result = eF_getTableData("calendar c, lessons l", "c.id, c.timestamp, c.data, l.name", "c.lessons_ID in (".$l.") AND c.active=1 AND c.lessons_ID = l.ID AND timestamp >= ".($timestamp_from)." AND timestamp <= ".($timestamp_to), "timestamp");
        }
    }

    foreach ($result as $event) {
        $events[$event['timestamp']]['data'][] = $event['data'];
        $events[$event['timestamp']]['id'][]   = $event['id'];
        $events[$event['timestamp']]['lesson'][]   = $event['name'];
    }

    return $events;
}



/**
* Checks if the given user and password pair is valid
*
* The function is used to check if a certain username/password pair is valid, and to return this user's
* type. If no arguments are given, equivalent Session variables are considered.
* <br/>Example:
* <code>
* //The following code may be put at a page's header, indicating that only a valid user of type 'professor' may access it
* if (eF_checkUser($_SESSION['s_login'], $_SESSION['s_password']) != "professor") {
*     header("location:index.php");
*     exit;
* }
* </code>
*
* @param string $login The user name
* @param string $password The user password
* @return mixed The user type, one of 'professor', 'student' and 'administrator' or false if the combination is not valid.
* @version 1.0
* @todo Remove the global variable...
* - Added $lessons_ID parameter
* -version 2.6 From now on user_type depends also from lesson
*/

function eF_checkUser($login = false, $password = false, $lessons_ID = false)
{
    global $message;
    global $configuration;

    if (!$login){                                                                                  //?? ??? ???????? ????????, ???? ?? ??? ?? SESSION
        $login = $_SESSION['s_login'];
        if (!$password) {
            $password = $_SESSION['s_password'];
        }
    }
    if(!$lessons_ID){
        if (isset($_SESSION['s_lessons_ID']))
            $lessons_ID = $_SESSION['s_lessons_ID'];
    }
    $res = eF_getTableData("users", "password, user_type, active", "login='$login'");
    if (!$res) {
        $message = _YOUCANNOTLOGINAS." ".$login;
        return false;
    } elseif ($res[0]['password'] == 'ldap') {
//        if (eF_checkUserLdap($login, str_rot13($password))) {                                       //rot13 applies a very simple encryption/decryption to the password, since it is stored as clear text (not hashed) to the session
            $user_type = eF_getUserBasicType($login, $lessons_ID);
            return $user_type;
//        } else {
//            $message = _WRONGPASSWORDTRYAGAIN;
//            return false;
//        }
    } elseif($res[0]['password'] != $password) {
        $message = _WRONGPASSWORDTRYAGAIN;
        return false;
    } elseif($res[0]['active'] != 1) {
        if ($configuration['activation'] == 0 && $configuration['mail_activation'] == 1){
            $message = _YOUWILLRECEIVEMAILFORACCOUNTACTIVATION;
        }else{
            $message = _NOTACTIVATEDACCOUNTTRYAGAINLATER;
        }
        return false;
    } else {
        $user_type = eF_getUserBasicType($login, $lessons_ID);
        return $user_type;
    }
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
    $ip_sep = explode('.',  $dotquad_ip);
    return sprintf('%02x%02x%02x%02x',  $ip_sep[0],  $ip_sep[1],  $ip_sep[2],  $ip_sep[3]);
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

    $allowedIPs = $GLOBALS['configuration']['ip_white_list'];                                              //Read the allowed IPs
    if (!$allowedIPs || !$client_ip) {                                                    //If the database doesn't
        $ok1 = true;
    } else {
        $client_ip_parts = explode('.', $client_ip);
        $allowed_ips     = explode(",", preg_replace("/\s+/", "", $allowedIPs));                                    //explode ips into an array, after stripping off any whitespace
        $ok1 = false;
        foreach($allowed_ips as $ip) {
            $ip_parts = explode('.', $ip);
            $count = 0;
            $temp  = true;
            while ($temp && $count++ < 4) {
                if ($client_ip_parts[$count] != $ip_parts[$count] && $ip_parts[$count] != '*') {
                    $temp = false;
                }
            }
            $ok1 = $ok1 | $temp;
        }
    }

    $disAllowedIPs = $GLOBALS['configuration']['ip_black_list'];                                              //Read the allowed IPs
    if (!$disAllowedIPs || !$client_ip) {                                                    //If the database doesn't
        $ok2 = false;
    } else {
        $client_ip_parts = explode('.', $client_ip);
        $allowed_ips     = explode(",", preg_replace("/\s+/", "", $disAllowedIPs));                                    //explode ips into an array, after stripping off any whitespace
        $ok2 = false;
        foreach($allowed_ips as $ip) {
            $ip_parts = explode('.', $ip);
            $count = 0;
            $temp  = true;
            while ($temp && $count++ < 4) {
                if ($client_ip_parts[$count] != $ip_parts[$count] && $ip_parts[$count] != '*') {
                    $temp = false;
                }
            }
            $ok2 = $ok2 | $temp;
        }
    }


    return $ok1 & !$ok2;                                                                                                    //For the user to be able to login, he must either be in the first group or the second group
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

    $img_str     = '<image src = "images/smilies/icon_';
    $text_array    = array(':)', ':-)',
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
    $smilies_array = array($img_str.'smile.gif" />',     $img_str.'smile.gif" />',
                           $img_str.'sad.gif" />',       $img_str.'sad.gif" />',
                           $img_str.'wink.gif" />',      $img_str.'wink.gif" />',
                           $img_str.'redface.gif" />',
                           $img_str.'biggrin.gif" />',   $img_str.'biggrin.gif" />',
                           $img_str.'surprised.gif" />', $img_str.'surprised.gif" />',
                           $img_str.'eek.gif" />',       $img_str.'eek.gif" />',
                           $img_str.'confused.gif" />',  $img_str.'confused.gif" />',
                           $img_str.'cool.gif" />',      $img_str.'cool.gif" />',
                           $img_str.'lol.gif" />',
                           $img_str.'mad.gif" />',       $img_str.'mad.gif" />',
                           $img_str.'razz.gif" />',      $img_str.'razz.gif" />',
                           $img_str.'cry.gif" />',
                           $img_str.'evil.gif" />',
                           $img_str.'twisted.gif" />',
                           $img_str.'rolleyes.gif" />');

    $smilied_text = str_replace($text_array, $smilies_array, $str);

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
    $basedn   = $GLOBALS['configuration']['ldap_basedn'];
    $ldap_uid = $GLOBALS['configuration']['ldap_uid'];

    $ds = eF_ldapConnect();
    $sr = ldap_search($ds, $basedn, $ldap_uid.'='.$login);

    if (ldap_count_entries($ds, $sr) != 1) {
        return false;                                       //User either does not exist or more than 1 users found
    }
    $dn = ldap_get_dn($ds, ldap_first_entry($ds, $sr));

    $b = ldap_bind($ds, $dn, $password);
    if (!$b) {
        return 0;                                       //login / password values don't match
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
    $basedn   = $GLOBALS['configuration']['ldap_basedn'];          //The base DN is needed to perform searches

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
    $server   = eF_getTableData("configuration", "value", "name='ldap_server'");
    $port     = eF_getTableData("configuration", "value", "name='ldap_port'");
    $binddn   = eF_getTableData("configuration", "value", "name='ldap_binddn'");
    $bind_pwd = eF_getTableData("configuration", "value", "name='ldap_password'");
    $protocol = eF_getTableData("configuration", "value", "name='ldap_protocol'");

    $ds = ldap_connect($GLOBALS['configuration']['ldap_server'], $GLOBALS['configuration']['ldap_port']);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $GLOBALS['configuration']['ldap_protocol']);
    ldap_set_option($ds, LDAP_OPT_TIMELIMIT, 10);

    $b  = ldap_bind($ds, $GLOBALS['configuration']['ldap_binddn'], $GLOBALS['configuration']['ldap_password']);

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
* @param string $type The parameter type
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
            if (!preg_match("/^[0-9]{1,100}$/", $parameter)) {                              //Caution: If 0 is met, then it will return 0 and not false! so, it must checked against false to make sure
                return false;
            }
            break;

        case 'login':
            if (!preg_match("/^[^0-9]_*\w+(\w*[._@-]*\w*)*$/", $parameter)) {                      //This means: begins with 0 or more '_', never a number, followed by at least 1 word character, followed by any combination of .,_,-,@ and word characters.
                return false;
            }
            break;

        case 'email':
            if (!preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $parameter)) {  //This means: begins with 0 or more '_' or '-', followed by at least 1 word character, followed by any combination of '_', '-', '.' and word characters, then '@', then the same as before, then the '.' and then 1 ore more characters.
                return false;
            }
            break;

        case 'filename':
            if (preg_match("/^.*((\.\.)|(\/)|(\\\)).*$/", $parameter)) {                      //File name must not contain .. or slashes of any kind
                return false;
            }
            break;

        case 'directory':
            if (preg_match("/^.*((\.\.)|(\\\)).*$/", $parameter)) {                      //Directory is the same as filename, except that it may contain forward slashes
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
            if (!preg_match("/^[A-Za-z0-9]{1,100}$/", $parameter)) {
                return false;
            }
        break;

        case 'ldap_attribute':
            if (!preg_match("/^[A-Za-z0-9:;\-_]{1,100}$/", $parameter)) {                     //An ldap attribute may be of the form: cn:lang-el;
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

       case 'path':
           if (preg_match("/^.*[$\'\"]+.*$/", $parameter)) {
                return false;
            }
       break;

       default:
            break;
    }
    return $parameter;

}



/**
* Get announcements
*
* This function gets the lesson announcements (news). It returns an array holding the announcement title, id
* and timestamp.
* From v3 you can add news which display from a specific time in the future...makriria(2007/07/17)
* <br/>Example:
* <code>
* $news = eF_getNews();
* print_r($news);
* //Returns:
*Array
*(
*    [0] => Array
*        (
*            [title] => announcement 1
*            [id] => 3
*            [timestamp] => 1125751731
*            [users_LOGIN] => admin
*        )
*
*    [1] => Array
*        (
*            [title] => Important announcem...
*            [id] => 5
*            [timestamp] => 1125751012
*            [users_LOGIN] => peris
*        )
*)
* </code>
*
* @param int $lessons_ID The lesson id
* @return array The news array
* @version 1.0
*/
function eF_getNews($lessons_ID = false) {

    if (!$lessons_ID) {
        isset($_SESSION['s_lessons_ID']) ? $lessons_ID = $_SESSION['s_lessons_ID'] : $lessons_ID = 0;
    }

    if ($_SESSION['s_type'] == "student"){                                  // students see only previous news
        $news_array = eF_getTableData("news n, users u", "n.*, u.surname, u.name", "n.users_LOGIN = u.login and (n.lessons_ID=$lessons_ID OR n.lessons_ID=0) AND n.timestamp<=".time(), "n.timestamp desc"); //"
    } elseif ($_SESSION['s_type'] == "administrator") {                     // administrators see only news with lessons_Id=0
        $news_array = eF_getTableData("news n, users u", "n.*", "n.users_LOGIN = u.login and n.lessons_ID=0", "n.timestamp desc");
    } else {
        $news_array = eF_getTableData("news n, users u", "n.*", "n.users_LOGIN = u.login and (n.lessons_ID=$lessons_ID OR n.lessons_ID=0)", "n.timestamp desc");
    }

    return $news_array;
}


/**
* Get comments
*
* This function gets the lesson comments. It returns an array holding the name of the lesson where the comment was put,
* the comment id, the comment itself (which is put as a title on the lesson name link), and finally the timestamp and the
* user that posted it. IF a lesson id is not specified, then comments for the current lesson are returned.If a login is
* specified, then only comments that the specified user has posted are returned. If a content id is specified, then only
* comments of this unit are displayed.
* <br/>Example:
* <code>
* $comments = eF_getComments();
* print_r($comments);
* //Returns:
*Array
*(
*    [0] => Array
*        (
*            [id] => 3
*            [data] => This is a comment
*            [users_LOGIN] => admin
*            [timestamp] => 1125751731
*            [content_name] => unit 1.2
*            [content_id] => 145
*            [content_type] => theory
*        )
*)
* </code>
*
* @param int $lessons_ID The lesson id
* @param string $login The user login
* @param int $content_ID The unit id to return its comments
* @param int $limit The results limit
* @return array The comments array
* @version 1.1
* @Changes from 1.0 to 1.1: 15/11/2005
* - Added $limit parameter
*/
function eF_getComments($lessons_ID = false, $login = false, $content_ID = false, $limit = false) {

    if (!$lessons_ID || !eF_checkParameter($lessons_ID, 'id')) {
        $lessons_ID = $_SESSION['s_lessons_ID'];
    }

    if ($login && eF_checkParameter($login, 'login')) {
        $login_str = " AND comments.users_LOGIN='$login'";
    } else {
        $login_str = '';
    }

    if ($content_ID && eF_checkParameter($content_ID, 'id')) {
        $content_ID_str = ' AND content.id='.$content_ID;
    } else {
        $content_ID_str = '';
    }

    if ($limit && eF_checkParameter($limit, 'uint')) {
        $limit_str = ' limit '.$limit;
    } else {
        $limit_str = '';
    }

    $comments_array = eF_getTableData("comments, content", "comments.id AS id, comments.data AS data, comments.users_LOGIN AS users_LOGIN, comments.timestamp AS timestamp, content.name AS content_name, content.id AS content_ID, content.ctg_type AS content_type", "content.lessons_ID=$lessons_ID AND comments.content_ID=content.id AND content.active=1 AND comments.active=1".$login_str.$content_ID_str, "comments.timestamp DESC".$limit_str);

    return $comments_array;


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
            /** MODULE HCD: Present HCD link **/
            if (MODULE_HCD_INTERFACE) {
               $menu['general']['module_hcd']          = array('title' => _ORGANIZATION,          'link' => 'administrator.php?ctg=module_hcd',   'image' => 'factory');
            }
            $menu['general']['users']         = array('title' => _USERS,         'link' => 'administrator.php?ctg=users',         'image' => 'user1');
            $menu['general']['lessons']       = array('title' => _LESSONS,       'link' => 'administrator.php?ctg=lessons',       'image' => 'lessons');
            $menu['general']['directions']    = array('title' => _CATEGORIES,    'link' => 'administrator.php?ctg=directions',    'image' => 'kdf');
            $menu['general']['courses']       = array('title' => _COURSES,       'link' => 'administrator.php?ctg=courses',       'image' => 'books');

            //$menu['general']['user_types']    = array('title' => _ROLES,         'link' => 'administrator.php?ctg=user_types',    'image' => 'users_family');
            $menu['general']['user_groups']   = array('title' => _GROUPS,        'link' => 'administrator.php?ctg=user_groups',    'image' => 'users3');
            $menu['general']['statistics'] = array('title' => _STATISTICS,'link' => 'administrator.php?ctg=statistics',       'image' => 'chart');
            $menu['general']['cms']           = array('title' => _CMS,           'link' => 'administrator.php?ctg=cms',           'image' => 'document_text');
            if (!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden') {
                $menu['general']['forum']         = array('title' => _FORUM,         'link' => 'forum/forum_index.php',               'image' => 'messages');
            }
//            if (!isset($GLOBALS['currentUser'] -> coreAccess['personal_messages']) || $GLOBALS['currentUser'] -> coreAccess['personal_messages'] != 'hidden') {
                $menu['general']['messages']      = array('title' => _MESSAGES,      'link' => 'forum/messages_index.php',            'image' => 'mail2');
//            }
            $menu['general']['emails']        = array('title' => _EMAILS,        'link' => 'administrator.php?ctg=emails',        'image' => 'mail');
            $menu['general']['chat']          = array('title' => _CHAT,          'link' => 'chat/chat_index.php',                 'image' => 'user1_message');

            foreach ($user_module['administrator'] as $value) {
                if ($value['position'] == 'left') {
                    $menu['general'][$value['name']] = array('title' => $value['title'], 'link' => 'administrator.php?ctg='.$value['name'], 'image' => 'component_green');
                }
            }
            $menu['general']['logout']        = array('title' => _LOGOUT,        'link' => 'index.php?logout=true',               'image' => 'exit');

        break;

        case 'professor' :
            if ($_SESSION['s_lessons_ID'] != false) {
                $menu['lesson']['control_panel'] = array('title' => _MAINPAGE, 'link' => 'professor.php?ctg=control_panel', 'image' => 'home', 'id' => 'lesson_main_a');

                if ($GLOBALS['currentUser'] -> coreAccess['content'] != 'hidden') {
                    $menu['lesson']['content']       = array('title' => _CONTENTMANAGEMENT, 'link' => 'professor.php?ctg=content',       'image' => 'tests', 'id' => 'content_a');
                    //$menu['lesson']['scheduling']    = array('title' => _SCHEDULING,        'link' => 'professor.php?ctg=scheduling',    'image' => 'date-time', 'id' => 'scheduling_a');
                    if ($GLOBALS['currentLesson'] -> options['projects']) {
                        $menu['lesson']['projects'] = array('title' => _PROJECTS, 'link' => 'professor.php?ctg=projects', 'image' => 'exercises', 'id' => 'exercises_a');
                    }
                    if ($GLOBALS['currentLesson'] -> options['tests']) {
                        $menu['lesson']['tests'] = array('title' => _TESTS, 'link' => 'professor.php?ctg=tests', 'image' => 'document_edit', 'id' => 'tests_a');
                    }
                    if ($GLOBALS['currentLesson'] -> options['rules']) {
                        $menu['lesson']['rules'] = array('title' => _ACCESSRULES, 'link' => 'professor.php?ctg=rules', 'image' => 'recycle', 'id' => 'rules_a');
                    }
                }
                if ($GLOBALS['currentLesson'] -> options['glossary'] && $GLOBALS['currentUser'] -> coreAccess['glossary'] != 'hidden') {
                    $menu['lesson']['glossary'] = array('title' => _GLOSSARY, 'link' => 'professor.php?ctg=glossary', 'image' => 'book_open2', 'id' => 'glossary_a');
                }
                if ($GLOBALS['currentLesson'] -> options['forum'] && (!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden')) {
                    $forums_id = eF_getTableData("f_forums", "id", "lessons_ID=".$_SESSION['s_lessons_ID']);
                    if (sizeof($forums_id) > 0) {
                        $menu['lesson']['forum'] = array('title' => _FORUM, 'link' => 'forum/forum_index.php?forum='.$forums_id[0]['id'],'image' => 'messages', 'id' => 'forum_a');
                    } else {
                        $menu['lesson']['forum'] = array('title' => _FORUM, 'link' => 'forum/forum_index.php','image' => 'messages', 'id' => 'forum_a');
                    }
                }

                if ($GLOBALS['currentLesson'] -> options['survey'] && (!isset($GLOBALS['currentUser'] -> coreAccess['surveys']) || $GLOBALS['currentUser'] -> coreAccess['surveys'] != 'hidden')) {
                    $menu['lesson']['survey']        = array('title' => _SURVEY,         'link' => 'professor.php?ctg=survey',       'image' => 'form_green', 'id' => 'survey_a');
                }

                /*
                if (($GLOBALS['currentLesson'] -> options['calendar']) && $GLOBALS['currentLesson'] -> options['calendar']==1) {
                    $menu['lesson']['calendar']      = array('title' => _CALENDAR,       'link' => 'professor.php?ctg=calendar',     'image' => 'calendar', 'id' => 'calendar_a');
                }
                */

                if ($GLOBALS['currentUser'] -> coreAccess['files'] != 'hidden') {
                    $menu['lesson']['file_manager']  = array('title' => _FILES, 'link' => 'professor.php?ctg=content&op=file_manager',     'image' => 'folder_view', 'id' => 'file_manager_a');
                }
                if ($GLOBALS['currentUser'] -> coreAccess['settings'] != 'hidden') {
                    $menu['lesson']['settings'] = array('title' => _LESSONSETTINGS, 'link' => 'professor.php?ctg=settings',     'image' => 'gear', 'id' => 'settings_a');
                }

                foreach ($user_module['professor'] as $value) {
                    if ($value['position'] == 'left' && ($module['mandatory'] != 'false' || ($GLOBALS['currentLesson'] -> options[$module['name']]))) {
                        $menu['lesson'][$value['name']] = array('title' => $value['title'], 'link' => 'professor.php?ctg='.$value['name'], 'image' => 'component_green', 'id' => 'module');
                    }
                }
            }

            $menu['general']['lessons']  = array('title' => _LESSONS,  'link' => 'professor.php?ctg=lessons',  'image' => 'lessons');
            if (MODULE_HCD_INTERFACE) {
               $menu['general']['module_hcd'] = array('title' => _ORGANIZATION,   'link' => 'professorpage.php?view=organization',   'image' => 'factory', 'target' => '_top');
            }

            $menu['general']['statistics'] = array('title' => _STATISTICS, 'link' => 'professor.php?ctg=statistics', 'image' => 'chart');              
            $menu['general']['calendar'] = array('title' => _CALENDAR,       'link' => 'professor.php?ctg=calendar',     'image' => 'calendar');
            $menu['general']['messages'] = array('title' => _MESSAGES, 'link' => 'forum/messages_index.php',   'image' => 'mail2');
            $menu['general']['personal'] = array('title' => _SETTINGS, 'link' => 'professor.php?ctg=personal', 'image' => 'gears');
            $menu['general']['logout']   = array('title' => _LOGOUT,   'link' => 'index.php?logout=true',      'image' => 'exit');
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
                            $menu['lesson']['theory'] = array('title' => _THEORY, 'link' => 'student.php?ctg=content&type=theory', 'num' => sizeof($theoryContentIds), 'image' => 'book_blue', 'target' => "mainframe", 'id' => 'theory_a');
                        }
                    }
                    if ($GLOBALS['currentLesson'] -> options['examples']) {
                        if (sizeof($exampleContentIds) > 0) {
                            $menu['lesson']['examples'] = array('title' => _EXAMPLES, 'link' => 'student.php?ctg=content&type=examples', 'num' => sizeof($exampleContentIds), 'image' => 'lightbulb_on', 'target' => "mainframe", 'id' => 'examples_a');
                        }
                    }
                    if ($GLOBALS['currentLesson'] -> options['projects']) {
                        $projects = $GLOBALS['currentLesson'] -> getProjects();
                        if (sizeof($projects) > 0) {
                            $menu['lesson']['projects'] = array('title' => _PROJECTS, 'link' => 'student.php?ctg=projects', 'num' => sizeof($projects), 'image' => 'exercises', 'target' => "mainframe", 'id' => 'exercises_a');
                        }
                    }
                    if ($GLOBALS['currentLesson'] -> options['tests']) {
                        if (sizeof($testsContentIds) > 0) {
                            $menu['lesson']['tests'] = array('title' => _TESTS, 'link' => 'student.php?ctg=content&type=tests', 'num' => sizeof($testsContentIds), 'image' => 'tests', 'target' => "mainframe", 'id' => 'tests_a');
                        }
                    }
                }
                if ($GLOBALS['currentLesson'] -> options['forum'] && (!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden')) {
                    $forums_id = eF_getTableData("f_forums", "id", "lessons_ID=".$_SESSION['s_lessons_ID']);
                    if (sizeof($forums_id) > 0) {
                        $menu['lesson']['forum'] = array('title' => _FORUM, 'link' => 'forum/forum_index.php?forum='.$forums_id[0]['id'], 'image' => 'messages', 'target' => "mainframe", 'id' => 'forum_a');
                    } else {
                        $menu['lesson']['forum'] = array('title' => _FORUM, 'link' => 'forum/forum_index.php', 'image' => 'messages', 'target' => "mainframe", 'id' => 'forum_a');
                    }
                }

                if ($GLOBALS['currentLesson'] -> options['glossary'] && (!isset($GLOBALS['currentUser'] -> coreAccess['content']) || $GLOBALS['currentUser'] -> coreAccess['content'] != 'hidden')) {
                    $menu['lesson']['glossary'] = array('title' => _GLOSSARY, 'link' => 'student.php?ctg=glossary', 'image' => 'book_open2', 'target' => "mainframe", 'id' => 'glossary_a');
                }
                foreach ($user_module['student'] as $value) {
                    if ($value['position'] == 'left' && ($module['mandatory'] != 'false' || ($GLOBALS['currentLesson'] -> options[$module['name']]))) {
                        $menu['lesson'][$value['name']] = array('title' => $value['title'], 'link' => 'student.php?ctg='.$value['name'], 'image' => 'component_green', 'target' => "mainframe", 'id' => 'modules_i');
                    }
                }
            }

            $menu['general']['lessons']  = array('title' => _LESSONS,  'link' => 'student.php?ctg=lessons',  'image' => 'lessons', 'target' => "mainframe");
            if (MODULE_HCD_INTERFACE) {
               $menu['general']['module_hcd'] = array('title' => _ORGANIZATION,   'link' => 'studentpage.php?view=organization',   'image' => 'factory', 'target' => '_top');
            }

            if ($_SESSION['s_lessons_ID'] != false) {
                $menu['general']['statistics'] = array('title' => _STATISTICS, 'link' => 'student.php?ctg=statistics', 'image' => 'chart', 'target' => "mainframe");
            }
            $menu['general']['calendar'] = array('title' => _CALENDAR, 'link' => 'student.php?ctg=calendar', 'image' => 'calendar', 'target' => "mainframe");
            $menu['general']['messages'] = array('title' => _MESSAGES,   'link' => 'forum/messages_index.php', 'image' => 'mail2', 'target' => "mainframe");
            $menu['general']['personal'] = array('title' => _SETTINGS,   'link' => 'student.php?ctg=personal', 'image' => 'gears', 'target' => "mainframe");
            $menu['general']['logout']   = array('title' => _LOGOUT,     'link' => '/index.php?logout=true',    'image' => 'exit',  'target' => "mainframe");

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
    $hours   = ($interval - $seconds - ($minutes * 60)) / 3600;

    if ($ago) {
        if ($hours > 24) {
            return floor($hours/24).' '.mb_strtolower(floor($hours/24) == 1 ? _DAY : _DAYS); 
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


/**
* Get glossary words
*
* This function is used to return an array of words, divided per initial letter, given an array of
* glossary entries.
*
* @return array letters: An array of words, divided in subarrays per letter
* @version 0.9
* @todo
*/
function eF_getAllGlossaryWords($words) {

    $initials = array();
    foreach($words as $key => $value) {
        $letter = mb_substr($value['name'],0,1);
        if($letter=="?" || $letter=="?")
            $letter="?";
        if($letter=="?" || $letter=="?")
            $letter="?";
        if($letter=="?" || $letter=="?")
            $letter="?";
        if($letter=="?" || $letter=="?" || $letter=="?" || $letter=="?" || $letter=="?")
            $letter="?";
        if($letter=="?" || $letter=="?")
            $letter="?";
        if($letter=="?" || $letter=="?" || $letter=="?" || $letter=="?" || $letter=="?")
            $letter="?";
        $letter = mb_strtoupper($letter);
        //echo "LETTER: ".$letter." ASCII: ".ord($letter)."<br/>";
        if (preg_match("/[0-9]/", $letter)) {
            $initials["0-9"][$letter][] = $words[$key];
        } else if (!preg_match("/\w/", $letter)) {
            $initials["Symbols"][$letter][] = $words[$key];
        } else {
            $initials[$letter][] = $words[$key];
        }//$initials[$letter]=1;
    }
    $setNum = isset($initials["0-9"]);
    $setSym = isset($initials["Symbols"]);
    if( $setNum || $setSym )
    {
        $tempNum = $initials["0-9"];
        $tempSym = $initials["Symbols"];
        unset($initials["0-9"]);
        unset($initials["Symbols"]);
        ksort($initials);
        if($setNum)
            $initials["0-9"] = $tempNum;
        if($setSym)
            $initials["Symbols"] = $tempSym;
    }
    else
        ksort($initials);

    //print_r($initials);

    return $initials;
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
            $result = eF_getTableData("users", "login", "login='$needle'");
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
    $filter = mb_strtolower($filter);
    if ($filter) {
        foreach ($data as $key => $value) {
            $imploded_string = implode(",", $value);                 //Instead of checking each row value one-by-one, check it all at once
            if (strpos(mb_strtolower($imploded_string), $filter) === false) {
                unset($data[$key]);
            }
        }
    }

    return $data;
}

function eF_applyGlossary($str) {
    $glossary_words = eF_getTableData("glossary_words", "name,info", "lessons_ID=".$_SESSION['s_lessons_ID']);  //Get all the glossary words of this lesson
    $pos = 0;
    $searchdata     = array();
    $searchdatanext = array();
    $replacedata    = array();

    foreach ($glossary_words as $key => $value) {
        $first_letter = mb_substr($value['name'], 0, 1);
        if ($first_letter != '<') {
            $value['name']        = str_replace("/", "\/", $value['name']);            
            $searchdata[$pos]     = "/(\p{Z})(".$value['name'].")(\p{Z})/usi";                     //This used to be "/\b(".$value['name'].")\b/si" but the word boundary \b at the end does not work with utf8 strings (and other characters as well, like \w, \d etc, see the PHP manual on UTF8 handling). So, we put the (much slower) \p{Z} However, this RE still doesn't capture the following cases: 1. When the word is at the very beginning or end of the content. 2. When it is followed by itseld. For example, if we are looking for 'foobar', then in this content only the first occurence wull be captured: blah blah foobar foobar blah blah 
            $searchdatanext[$pos] = "/(yty656hgh".$value['name'].")/si";
            $replacedata[$pos]    = $value['info'];

            $pos = $pos + 1;
        }
    }
    $str = eF_highlightWords($str, $searchdata, $replacedata);
    $str = preg_replace("/encode\*\(\)\!768atyj/", "", $str);        
    $str = preg_replace($searchdatanext, $replacedata, $str);

    return $str;
}

/**
*
*/
function eF_highlightWords ($text, $searchdata, $replacedata) {
    $word = $searchdata;
    $text_pieces = preg_split("'(<a.*>.*</a>)|(<.+?>)'", $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

    $count = 0;
    $found = false;
    $i = 0;
    $info = $replacedata;

    foreach ($text_pieces as $piece) {
        if ( (mb_strpos($piece, '<') === FALSE) && ($found==false) ) {
        //echo $piece."<br>";
            if ($newPiece = preg_replace_callback($searchdata, 'eF_encodeWords', $piece)) {
                $piece = $newPiece;                
            }
            
        //echo $piece."<br>";
        }
        $new_text_pieces[$i] = $piece;
        $i++;
    }

    $text = implode('', $new_text_pieces);
    return $text;
}

/**
*
*/
function eF_encodeWords($matches)
{
    $matching_text = $matches[2];    

    $words = explode(" ", $matching_text);
    foreach($words as $key => $word) {
        $words[$key] = 'encode*()!768atyj'.$word;
    }
    $new_text = implode(' ',$words);
    return $matches[1]."<a class = 'glossary' onClick = 'togglePopup(this);' href = 'javascript:void(0)'>".$new_text."<img class = 'tooltip' border = '0' src='images/others/tooltip_arrow.gif'/><span><img align = 'right' class='close' border='0' src='images/16x16/error.png'/>yty656hgh".eF_encodeWordsInner($matching_text)."</span></a>".$matches[3];
    
}

/**
*
*/
function eF_encodeWordsInner($text)
{

    $words = explode(" ", $text);
    foreach($words as $key => $word) {
        $words[$key] = 'encode*()!768atyj'.$word;
    }
    $new_text = implode(' ',$words);
    return $new_text;
    //return "<a class = 'glossary info' href = 'javascript:void(0)'>".$new_text."<img class = 'tooltip' border = '0' src='/images/others/tooltip_arrow.gif'/><span>yty656hgh".$matching_text."</span></a>";
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
        $image_path = "../" . substr($imageFile, $position);
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
            $sidebarLinks = $sidebarLinks[$menu_category];

            foreach ($sidebarLinks as $mod_link) {

                // The "moduleLink" in the following array denotes special treatment
                $links[] = array("id" => $module -> className . (($mod_link['id'])? "_".$mod_link['id']:""),
                                 "image" => eF_getRelativeModuleImagePath($mod_link['image']),
                                 "link" => $mod_link['link'],
                                 "title" => $mod_link['title'],
                                 "moduleLink" => "1",
                                 "eFrontExtensions" => $mod_link['eFrontExtensions'],
                                 "class"  => "menuOption");
            }
        }
    }
    return $links;

}

/**
* Function to return an array with objects regarding
* all module classes installed in the system
* Used for checking for events to be executed
*/
function eF_loadAllModules() {
    $modulesDB = eF_getTableData("modules","*","");
    $modules = array();

    global $currentUser;
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
                    $message      = '"'.$className .'" '. _MODULECLASSNOTEXISTSIN . ' ' .G_MODULESPATH.$folder.'/'.$className.'.class.php';
                    $message_type = 'failure';
                }
            } else {
                $message = _ERRORLOADINGMODULE;
                $message_type = "failure";
            }
        }
    }
    return $modules;
}


/**
* Function eF_checkVersionKey
*
* This function is used to check the key inserted for validation of version
*
* @param string $key The version key
* @return boolean
* @version 1.0
*/

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


function eF_checkVersionKey ($licence_key) {
    global $VERSIONTYPES, $path;
    $result = null;
    for ($i = 1; $i < 30001; $i++) {
        if (md5($i.G_PRIVATEKEY) == substr($licence_key, 0, 32)) {
              $users = $i;
        }
        if (md5($i.G_PRIVATEKEY) == substr($licence_key, 64, 92)) {
              $serial = $i;
        }
    }

    foreach ($VERSIONTYPES as $key => $value) {
        if (substr($licence_key, 32, -32) == md5($key.G_PRIVATEKEY)) {
            $type = $key;
        }
    }

    if ($users > 0 && $serial > 0 && array_key_exists($type, $VERSIONTYPES)) {
        if ($type == 'educational' && is_file('ipn.php')) {
            $paypal = 1;
            $hcd    = 0;
            return array('users' => $users, 'type' => $type, 'serial' => $serial, 'paypal' => $paypal, 'hcd' => $hcd);
        } elseif ($type == 'enterprise' && is_file($path.'hcd.class.php') && is_file($path.'hcd_user.class.php')) {
            $paypal = 0;
            $hcd    = 1;
            return array('users' => $users, 'type' => $type, 'serial' => $serial, 'paypal' => $paypal, 'hcd' => $hcd);
        } elseif ($type == 'standard') {
            $paypal = 0;
            $hcd    = 0;
            return array('users' => $users, 'type' => $type, 'serial' => $serial, 'paypal' => $paypal, 'hcd' => $hcd);
        } else {
            return null;
        }
    } else {
        if ((is_file('ipn.php')) || (is_file($path.'hcd.class.php') && is_file($path.'hcd_user.class.php'))) {
            return array('users' => 10, 'type' => 'unregistered', 'serial' => '', 'paypal' => 0, 'hcd' => 0);
        } else {
            return array('users' => 100000, 'type' => 'open_source', 'serial' => '', 'paypal' => 0, 'hcd' => 0);
        }
    }
}



function createToken($length){

    $salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";  // salt to select chars from
    srand((double)microtime()*1000000); // start the random generator
    $token=""; // set the inital variable
    for ($i=0;$i<$length;$i++)  // loop and create password
        $token = $token . substr ($salt, rand() % strlen($salt), 1);

    return $token;
}

function checkToken($token){
    $tmp = ef_getTableData("tokens","status","token='$token'");
    $token = $tmp[0]['status'];
    if ($token == 'logged'){
        return true;
    }
    else
        return false;
}

function printInPdfRows($str, $pdf, $row_chars = false) {
    // Set the characters per pdf row
    if ($row_chars) {
        $max_chars_per_line = $row_chars;
    } else {
        $max_chars_per_line = 150;
    }

    $str_len = strlen($str);
    if ($str_len > $max_chars_per_line) {
        $spec_array = explode(" ",$str);
        $i = 0;
        do {
            $char_sum = 0;
            $row = "";
            while ($char_sum < $max_chars_per_line && $str_len > 0 && isset($spec_array[$i])) {
                $len = strlen($spec_array[$i]);
                $char_sum += $len;
                $row .= ($spec_array[$i++] . " ");
                $str_len -= ($len+1);
            }
            $pdf->Cell(170, 5, $row, 0, 1, L, 0);
        } while($str_len > 0);
    } else {
        $pdf->Cell(170, 5, $str, 0, 1, L, 0);
    }

    return true;
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

    $thumb = imagecreatetruecolor($newwidth, $newheight);
    if ($extension == "png") {
        $source =imagecreatefrompng($filename);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    } else if ($extension == "gif") {
        $source =imagecreatefromgif($filename);
        imagecolortransparent($thumb, imagecolorallocate($thumb, 0, 0, 0));
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    } else {
        $source = imagecreatefromjpeg($filename);
    }

    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    if ($extension == "png") {
        return imagepng($thumb, $filename, 0, PNG_ALL_FILTERS);
    } else if ($extension == "gif") {
        return imagegif($thumb, $filename, 100);
    } else {
        return imagejpeg($thumb, $filename,100);
    }

}



/**
* Get user online times
*
* This function calculates the time a user spent online. If the optional interval parameter is set, then
* statistics are calculated only for this time period.
* <br/>Example:
* <code>
* $interval = array('from' => time()-86400, 'to' => time());        //Calculate statistics for the last 24 hours
* $times = eF_getUserTimes('john', $interval);
* print_r($times);
* //Returns:
*Array
*(
*    [duration] => Array
*        (
*            [0] => 19
*            [1] => 120
*            [2] => 63
*        )
*
*    [times] => Array
*        (
*           [0] => 1118770769
*           [1] => 1118824615
*           [2] => 1118824760
*        )
*)
* </code>
*
* @param string $login The user login name
* @param array $interval The time interval to calculate statistics for
* @return array The login times and durations (in seconds)
* @version 1.0 27/10/2005
*/
function eF_getUserTimes($login, $interval = false) {
    $times = array('duration' => array(), 'time' => array(), 'session_ip' => array());

    if (isset($interval['from']) && eF_checkParameter($interval['from'], 'timestamp') && isset($interval['to']) && eF_checkParameter($interval['to'], 'timestamp')) {
        $result = eF_getTableDataFlat("logs", "timestamp, action, session_ip", "timestamp > ".$interval['from']." and timestamp < ".$interval['to']." and users_LOGIN='".$login."' and (action='login' or action = 'logout')", "timestamp");
    } else {
        $result = eF_getTableDataFlat("logs", "timestamp, action, session_ip", "users_LOGIN='".$login."' and (action='login' or action = 'logout')", "timestamp");
    }

    if (sizeof($result) > 0) {
        for ($i = 0; $i < sizeof($result['action']) - 1; $i++) {                                            //The algorithm goes like this: We search for the 'login' actions in the log. When one is found, then we search either for the next 'login' or 'logout' action, if there are no other actions, or the last non-login or logout action. This way, we calculate the true time spent inside the system. If we calculated only the logout-login times, then when a user had closed a window without logging out first, the online time would be reported falsely
            if ($result['action'][$i] == 'login') {
                $count      = $i + 1;
                $end_action = $result['timestamp'][$count];
                while ($result['action'][$count] != 'logout' && $result['action'][$count] != 'login' && $count < sizeof($result['action'])) {
                    $end_action = $result['timestamp'][$count];
                    $count++;
                }
                if ($end_action - $result['timestamp'][$i] <= 3600){    //only take into account intervals less than one hour
                    $times['duration'][]   = $end_action - $result['timestamp'][$i];
                    $times['time'][]       = $result['timestamp'][$i];
                    $times['session_ip'][] = eF_decodeIP($result['session_ip'][$i]);
                }
            }
        }
    }

    return $times;
}


/**
* Calculates folder statistics.
*
* This function is used to calculate how many files and folders are contained inside a lesson folder. It
* also calculates the total size of these files. The parameter given corresponds to the lesson id, which
* coincides with the lesson folder name inside the content/ directory. The function returns a 3-values array,
* whith the first element being the total files number, the second the total folders and the third the total size.
* <br/>Example<br/>:
* <code>
* $files_array = EfrontLessonFiles(3);                                         //3 is the lesson id
* print_r($files_array);
* //Outputs:
*Array
*(
*    [0] => 10
*    [1] => 2
*    [2] => 1791160
*)
* </code>
* @param string $lesson_id The lesson id
* @return array An array with 3 elements: number of files, number of directories and total file size, in bytes. if the lesson does not exist it returns false.
* @version 1.0
*/
function EfrontLessonFiles($lesson_id)
{
    $dir = G_LESSONSPATH.$lesson_id;

    if ($handle = @opendir($dir)) {                                         //Check if lesson directory exists
        $stats = eF_diveIntoDir($dir);                                        //Calculate statistics
    } else {
        $stats = false;
    }

    return $stats;
}


/**
* Calculates statistics for the designated folder.
*
* This function is used to recursively calculate statistics for the designated folder.
* Statistics include number of files and folders as well as total file size.
* It returns a 3-valued array containing these values.
*
* @param string $dir The directory name
* @param int $files Total files to continue counting from
* @param int $directories Total directories to continue counting from
* @param int $file_size Total file size to continue counting from
* @return array An array with 3 elements, total files, directories and file size
* @version 1.2.1
* @see EfrontLessonFiles
* changes from 1.2 to 1.2.1:
* - Fixed bug in file size reporting
* changes from version 1.0 (renamed to eF_diveIntoDirOld) to 1.2:
* - Rewritten because it was buggy (it displayed only the root folder statistics)
*/
function eF_diveIntoDir($dir, $files = 0, $directories = 0, $file_size = 0)
{
    if ($handle = @opendir($dir)) {
        $in_directories = 0;
        while (false !== ($file = readdir($handle)))  {
            if (is_dir($dir.'/'.$file)) {
                if ($file != "." && $file != "..") {
                    $new_dir = $dir.'/'.$file;
                    $directories++;
                    list($files, $directories, $file_size) = eF_diveIntoDir($new_dir, $files, $directories, $file_size);
                }
            } else {
                $files++;
                $file_stat = stat($dir.'/'.$file);
                $file_size += $file_stat[7];
            }
        }
    }

    return array($files, $directories, $file_size);
}


function utf8ToUnicode(&$str)
{
  $mState = 0;     // cached expected number of octets after the current octet
                   // until the beginning of the next UTF8 character sequence
  $mUcs4  = 0;     // cached Unicode character
  $mBytes = 1;     // cached expected number of octets in the current sequence

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
          $mUcs4  = 0;
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
?>