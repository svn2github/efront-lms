<?php

/**

 * A collection of tools used by the module.

 * 

 * @name FUZE_Tools

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 */
class FUZE_Tools {
 /**

	 * Retrieves the UTC timestamp of a given point in time provided that the

	 * timezone from which we're translating is known.

	 * 

	 * @param String $tz_from The timezone we're translating from to UTC eg. 'Europe/Athens'.

	 * @param String $time_str A string representation of the time in point eg. '2010-10-25 15:45:31'

	 * 

	 * @return String The timestamp in UTC for the given datetime string.

	 * 

	 * @access public

	 */
 static public function get_UTC_timestamp($tz_from, $time_str) {
  $timestamp = false;
  if (phpversion() >= '5.3') {
   $dt = new DateTime($time_str, new DateTimeZone($tz_from));
   $timestamp = $dt->getTimestamp();
  }
  else {
   $timestamp = strtotime($time_str);
  }
  return $timestamp;
 }
 /**

	 * Retrieves a string representation of a datetime instance in a certain timezone

	 * provided that we have access to the UTC timestamp for this datetime.

	 * 

	 * @param String $tz_to A string representation of a timezone eg. 'Europe/Athens'.

	 * @param String $timestamp The UTC timestamp we want to translate.

	 * @param String $format The format of the returned datetime.

	 * 

	 * @return String The translated string representation of the given timestamp.

	 * 

	 * @access public

	 */
 static public function get_local_time($tz_to, $timestamp, $format = 'Y-m-d H:i:s') {
  if (phpversion() < '5.3') {
   date_default_timezone_set($tz_to);
  }
  return self::time_translate('UTC',$tz_to,date('r',$timestamp), $format);
 }
 static public function time_translate($tz_from, $tz_to, $time_str = 'now', $format = 'r') {
  $time_translated = false;
  if (phpversion() >= '5.3') {
   $dt = new DateTime($time_str, new DateTimezone($tz_from));
   $timestamp = $dt->getTimestamp();
   $time_translated = $dt->setTimezone(new DateTimezone($tz_to))->setTimestamp($timestamp)->format($format);
  }
  else {
   $date = new DateTime($time_str, new DateTimeZone($tz_to));
   $time_translated = $date->format($format);
  }
  return $time_translated;
 }
 static public function get_rough_time_description($timestamp) {
  if (!$timestamp) { $timestamp = time(); }
  $current_timestamp = time();
  $difference = abs($current_timestamp - $timestamp);
  ## amount of seconds in different time periods
  $minute = 60;
  $hour = 60*$minute;
  $day = 24*$hour;
  $week = 7*$day;
  $month = 30*$day;
  $year = 365*$day;
  ## The array that holds the time parts
  $time_parts = array('now' => false, 'future' => false, 'year' => false, 'month' => false, 'week' => false, 'day' => false, 'hour' => false, 'minute' => false);
  if ($current_timestamp > $timestamp) {
   ## Some time in the past ...
   $time_parts ['future'] = false;
  }
  elseif ($current_timestamp < $timestamp) {
   ## Some time in the future ...
   $time_parts ['future'] = true;
  }
  else {
   ## Right this moment ...
   $time_parts ['now'] = true;
  }
  $time_parts ['year'] = floor($difference/$year);
  $difference -= $time_parts ['year']*$year;
  $time_parts ['month'] = floor($difference/$month);
  $difference -= $time_parts ['month']*$month;
  $time_parts ['week'] = floor($difference/$week);
  $difference -= $time_parts ['week']*$week;
  $time_parts ['day'] = floor($difference/$day);
  $difference -= $time_parts ['day']*$day;
  $time_parts ['hour'] = floor($difference/$hour);
  $difference -= $time_parts ['hour']*$hour;
  $time_parts ['minute'] = floor($difference/$minute);
  return $time_parts;
 }
 public static function get_elements ($array, $tuple, $numberOfElements, $preserve_keys = false) {
  if ($tuple <= 0) $tuple = 1;
  $tuple -= 1;
  // If input array is empty then return an empty array
  if (!count($array)) {
   return array();
  }
  // If required offset is outside array boundaries
  // then return empty array
  if (count($array)<=($tuple*$numberOfElements)) {
   return array();
  }
  // If size of array is smaller than elements required
  // then return the entire array
  if (count($array)<=$numberOfElements) {
   return $array;
  }
  // Return an array starting at the offset tuple according
  // to the required number of elements per tuple
  $temp_array = array_chunk($array,$numberOfElements,$preserve_keys);
  return $temp_array[$tuple];
 }
 public static function send_email ($sender, $recipients, $html, $text, $subject) {
  require_once ('../../libraries/configuration.php');
  set_include_path('../../libraries/../PEAR/' . PATH_SEPARATOR .
       '../../libraries/includes/' . PATH_SEPARATOR .
       '../../libraries/' . PATH_SEPARATOR .
        '.' . PATH_SEPARATOR .
        '/usr/lib/php' . PATH_SEPARATOR .
        '/usr/local/lib/php' . PATH_SEPARATOR .
        get_include_path());
  $crlf = "\n";
  //$hdrs = array('From' => $GLOBALS['configuration']['system_email'], 'Subject' => $subject);
  $hdrs = array('From' => $sender, 'Subject' => $subject);
  $mime = new Mail_mime($crlf);
  $mime->setTXTBody($text);
  $mime->setHTMLBody($html);
  $params = array('html_charset'=>'utf-8', 'text_charset'=>'utf-8', 'head_charset'=>'utf-8', 'html_encoding'=>'base64', 'text_encoding'=>'base64');
  $body = $mime->get($params);
  $hdrs = $mime->headers($hdrs);
  $options = array();
  $options ['auth'] = ($GLOBALS['configuration']['smtp_auth'] ? true : false);
  $options ['host'] = $GLOBALS['configuration']['smtp_host'];
  $options ['password'] = $GLOBALS['configuration']['smtp_pass'];
  $options ['port'] = $GLOBALS['configuration']['smtp_port'];
  $options ['username'] = $GLOBALS['configuration']['smtp_user'];
  $options ['timeout'] = $GLOBALS['configuration']['smtp_timeout'];
  $smtp = Mail::factory('smtp', $options);
  $result = $smtp -> send($recipients, $hdrs, $body);
  unset($mime);
 }
}
