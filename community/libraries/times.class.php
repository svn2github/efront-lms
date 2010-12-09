<?php
/**
* EfrontTimes Class file
*
* @package eFront
* @version 3.5.0
*/

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/**
 * Statistics exceptions
 *
 * @package eFront
 */
class EfrontTimesException extends Exception
{
 const INVALID_ID = 1001;
 const INVALID_PARAMETER = 1002;

}

//Replacement for getUsersTimeAll, getUsersTime, getUserTimes, getParticipationStatistics, EfrontUser :: getLoginTime

/**
 * This class is used to handle time reporting
 *
 * @package eFront
 */
class EfrontTimes
{
 /**
	 * Instantiate Times object for the specified interval
	 *
	 * @param array $interval An array with 2 values, with keys either 0,1 or 'from', 'to'
	 * @since 3.6.7
	 * @access public
	 */
 public function __construct($interval = array()) {

  !isset($interval[0]) OR $interval['from'] = $interval[0];
  !isset($interval[1]) OR $interval['to'] = $interval[1];

  isset($interval['from']) ? $this -> fromTimestamp = $interval['from'] : $this -> fromTimestamp = 0;
  isset($interval['to']) ? $this -> toTimestamp = $interval['to'] : $this -> toTimestamp = time();
 }

 public function getUserTotalSessionTime($user) {
  $result = eF_getTableData("user_times", "sum(time)", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."'");
  return $result[0]['sum(time)'];
 }

 public function getUserSessionTimeInCourse($user, $course) {
  $result = eF_getTableData("user_times", "sum(time)", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."' and courses_ID = ".$course);
  return $result[0]['sum(time)'];
 }

 public function getUserSessionTimeInLesson($user, $lesson) {
  $result = eF_getTableData("user_times", "sum(time)", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."' and lessons_ID = ".$lesson);
  return $result[0]['sum(time)'];
 }

 public function getUserSessionTimeInUnit($user, $unit) {
  $result = eF_getTableData("user_times", "sum(time)", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."' and entity = 'unit' and entity_ID = ".$unit);
  return $result[0]['sum(time)'];
 }

 public function getUserSessionTimeInCourses($user) {
  $result = eF_getTableData("user_times", "courses_ID, sum(time) as time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."' and courses_ID is not null", "", "courses_ID");
  return $result;
 }

 public function getUserSessionTimeInLessons($user) {
  $result = eF_getTableData("user_times", "lessons_ID, sum(time) as time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."' and lessons_ID is not null", "", "lessons_ID");
  return $result;
 }

 public function getUserSessionTimeInUnits($user) {
  $result = eF_getTableData("user_times", "entity_ID, sum(time) as time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."' and entity = 'unit'", "", "entity_ID");
  return $result;
 }

 public function getUserSessionTimeInUnitsForLesson($user, $lesson) {
  $result = eF_getTableData("user_times", "entity_ID, sum(time) as time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."' and entity = 'unit' and lessons_ID=".$lesson, "", "entity_ID");
  return $result;
 }

 public function getSystemSessionTimesForUsers() {
  $result = eF_getTableDataFlat("user_times", "users_LOGIN, sum(time) as time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp, "", "users_LOGIN");
  if (sizeof($result['users_LOGIN']) > 0) {
   $result = array_combine($result['users_LOGIN'], $result['time']);
  } else {
   $result = array();
  }
  return $result;
 }

 public function getSystemSessionTimesForLessons() {
  $result = eF_getTableDataFlat("user_times", "lessons_ID, sum(time) as time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and lessons_ID is not null", "", "lessons_ID");
  if (sizeof($result['lessons_ID']) > 0) {
   $result = array_combine($result['lessons_ID'], $result['time']);
  } else {
   $result = array();
  }
  return $result;
 }

 public function getCourseSessionTimesForUsers($course) {
  $result = eF_getTableData("user_times", "users_LOGIN, sum(time) as time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and courses_ID = ".$course, "", "users_LOGIN");
  return $result;
 }

 public function getLessonSessionTimesForUsers($lesson) {
  $result = eF_getTableData("user_times", "users_LOGIN, sum(time) as time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and lessons_ID = ".$lesson, "", "users_LOGIN");
  return $result;
 }

 public function getUnitSessionTimesForUsers($unit) {
  $result = eF_getTableData("user_times", "users_LOGIN, sum(time) as time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and entity = 'unit' and entity_ID = ".$unit, "", "users_LOGIN");
  return $result;
 }

 public function getUserSessionTimes($user) {
  $result = eF_getTableData("user_times", "session_id,sum(time) as time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."'", "", "session_id");
  return $result;
 }

 public function getUserMeanSessionTime($user) {
  $meanTime = 0;
  $sessionTimes = $this -> getUserSessionTimes($user);
  foreach ($sessionTimes as $value) {
   $meanTime += $value['time'];
  }
  if ($meanTime) {
   $meanTime = round($meanTime/sizeof($sessionTimes));
  }
  return $meanTime;
 }

 public function getUserSessionTimeInLessonsPerDay($user) {
  list($startDay, $endDay) = $this -> convertBoundariesToDays();

  $result = eF_getTableData("user_times", "session_timestamp, lessons_ID, time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."' and lessons_ID is not null");
  foreach ($result as $value) {
   for ($i = $startDay; $i <= $endDay; $i += 86400) {
    isset($timesPerDay[$value['lessons_ID']][$i]) OR $timesPerDay[$value['lessons_ID']][$i] = 0;
    if ($i <= $value['session_timestamp'] && $value['session_timestamp'] < $i + 86400) {
     $timesPerDay[$value['lessons_ID']][$i] += $value['time'];
    }
   }
  }

  return $timesPerDay;
 }

 public function getLessonSessionTimesForUsersPerDay($lesson) {
  list($startDay, $endDay) = $this -> convertBoundariesToDays();

  $result = eF_getTableData("user_times", "session_timestamp, lessons_ID, time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and lessons_ID = ".$lesson);
  foreach ($result as $value) {
   for ($i = $startDay; $i <= $endDay; $i += 86400) {
    isset($timesPerDay[$value['users_LOGIN']][$i]) OR $timesPerDay[$value['users_LOGIN']][$i] = 0;
    if ($i <= $value['session_timestamp'] && $value['session_timestamp'] < $i + 86400) {
     $timesPerDay[$value['users_LOGIN']][$i] += $value['time'];
    }
   }
  }

  return $timesPerDay;
 }

 public function getUserSessionTimeInSingleLessonPerDay($user, $lesson) {
  list($startDay, $endDay) = $this -> convertBoundariesToDays();

  $result = eF_getTableData("user_times", "session_timestamp, lessons_ID, time", "session_timestamp < ".$this -> toTimestamp." and session_timestamp > ".$this -> fromTimestamp." and users_LOGIN = '".$user."' and lessons_ID=".$lesson);
  foreach ($result as $value) {
   for ($i = $startDay; $i <= $endDay; $i += 86400) {
    isset($timesPerDay[$i]) OR $timesPerDay[$i] = 0;
    if ($i <= $value['session_timestamp'] && $value['session_timestamp'] < $i + 86400) {
     $timesPerDay[$i] += $value['time'];
    }
   }
  }

  return $timesPerDay;
 }



 public static function formatTimeForReporting($seconds) {
  $totalTime = array('seconds' => 0, 'minutes' => 0, 'hours' => 0, 'total_seconds' => 0);
  if ($seconds >= 60) {
   $totalTime['seconds'] = $seconds % 60;
   $totalTime['minutes'] += floor($seconds / 60);
  } else {
   $totalTime['seconds'] = $seconds;
  }
  if ($totalTime['minutes'] >= 60) {
   $totalTime['hours'] = floor($totalTime['minutes']/60);;
   $totalTime['minutes'] = $totalTime['minutes'] % 60;;
  }
  $totalTime['total_seconds'] = $totalTime['hours'] * 3600 + $totalTime['minutes'] * 60 + $totalTime['seconds'];

  $totalTime['time_string'] = '';
  if ($totalTime['total_seconds']) {
   !$totalTime['hours'] OR $totalTime['time_string'] .= $totalTime['hours']._HOURSSHORTHAND.' ';
   !$totalTime['minutes'] OR $totalTime['time_string'] .= $totalTime['minutes']._MINUTESSHORTHAND.' ';
   !$totalTime['seconds'] OR $totalTime['time_string'] .= $totalTime['seconds']._SECONDSSHORTHAND;
  }

  return $totalTime;
 }

 private function convertBoundariesToDays() {
  $dateParts = getdate($this -> fromTimestamp);
  $startDay = mktime(0, 0, 0, $dateParts['mon'], $dateParts['mday'], $dateParts['year']);
  $dateParts = getdate($this -> toTimestamp);
  $endDay = mktime(23, 23, 59, $dateParts['mon'], $dateParts['mday'], $dateParts['year']);

  return array($startDay, $endDay);
 }


 public static function upgradeFromUsersOnline() {

  //Check if the users_online table actually exists. If not, then there is no need for upgrade
  try {
   $result = $GLOBALS['db'] -> GetAll("describe users_online");
  } catch (Exception $e) {
   return false;
  }

  //Get the first log entry
  $result = eF_getTableData("logs", "timestamp", "", "timestamp", "", "1");
  $dateParts = getdate($result[0]['timestamp']);
  $firstDay = mktime(0, 0, 0, $dateParts['mon'], $dateParts['mday'], $dateParts['year']);

  //Delete old upgrade attempts
  eF_deleteTableData("user_times");

  //Get system times for users
  $timeNow = time();
     for ($t = $firstDay; $t <= $timeNow - 86400; $t+=86400) {
   $userTimes[$t] = EfrontTimes::getDeprecatedUserTimesPerDay(array('from' => $t, 'to' => $t+86400));
     }

  foreach ($userTimes as $timestamp => $users) {
   foreach ($users as $login => $times) {
    $fields = array('session_timestamp' => $timestamp,
        'session_id' => 'from 3.6.6 upgrade',
        'session_expired' => 1,
        'users_LOGIN' => $login,
        'timestamp_now' => $timestamp,
        'time' => $times['total_seconds'],
        'lessons_ID' => NULL,
        'courses_ID' => NULL,
        'entity' => 'system',
        'entity_ID' => 0);
    eF_insertTableData("user_times", $fields);
   }
  }

  //Get times spent in SCORM
  $scormTimes = eF_getTableData("scorm_data sd, content c", "sd.total_time, sd.users_LOGIN, c.lessons_ID", "c.id=sd.content_ID");
  $scormSeconds = array();
  foreach ($scormTimes as $value) {
   if (!isset($scormSeconds[$value['lessons_ID']][$value['users_LOGIN']])) {
    $scormSeconds[$value['lessons_ID']][$value['users_LOGIN']] = 0;
   }

   $scormSeconds[$value['lessons_ID']][$value['users_LOGIN']] += convertTimeToSeconds($value['total_time']);
  }

  //Get times spent in lessons, as reported by system function
  $userTimes = EfrontStats::getUsersTimeAll();

  foreach ($userTimes as $lessonId => $users) {
   foreach ($users as $login => $user) {
    if ($user['total_seconds'] || $scormSeconds[$lessonId][$login]) {
     //If SCO times are bigger than lesson times, then use SCO times
     if ($user['total_seconds'] < $scormSeconds[$lessonId][$login]) {
      $user['total_seconds'] = $scormSeconds[$lessonId][$login];
     }
     $fields = array('session_timestamp' => time(),
        'session_id' => 'from 3.6.6 upgrade',
        'session_expired' => 1,
        'users_LOGIN' => $login,
        'timestamp_now' => time(),
        'time' => $user['total_seconds'],
        'lessons_ID' => $lessonId,
        'courses_ID' => NULL,
        'entity' => 'lesson',
        'entity_ID' => $lessonId);
     eF_insertTableData("user_times", $fields);
    }
   }
  }

  $GLOBALS['db'] -> Execute("drop table users_online");
 }

 /**
	 * previous EfrontUser :: getLoginTime
	 *
	 * @param array $interval
	 */
 private static function getDeprecatedUserTimesPerDay($interval) {
/*
		$scormTimes = eF_getTableData("scorm_data sd, content c", "sd.total_time", "c.id=sd.content_ID and users_LOGIN = '".$user['login']."' and c.lessons_ID=".$this -> lesson['id']);
		$scormSeconds = 0;
		foreach ($scormTimes as $value) {
			$scormSeconds += convertTimeToSeconds($value['total_time']);
		}
		$userTimes = EfrontStats :: getUsersTimeAll(false, false, array($this -> lesson['id'] => $this -> lesson['id']), array($user['login'] => $user['login']));
		$userTimes = $userTimes[$this -> lesson['id']][$user['login']];

		if ($userTimes['total_seconds'] < $scormSeconds) {
			$newTimes = convertSecondsToTime($scormSeconds);
			$newTimes['total_seconds'] = $scormSeconds;
			$newTimes['accesses']	   = $userTimes['accesses'];
			$userTimes = $newTimes;
		}

		$userTimes['time_string'] = '';
		if ($userTimes['total_seconds']) {
			!$userTimes['hours']   OR $userTimes['time_string'] .= $userTimes['hours']._HOURSSHORTHAND.' ';
			!$userTimes['minutes'] OR $userTimes['time_string'] .= $userTimes['minutes']._MINUTESSHORTHAND.' ';
			!$userTimes['seconds'] OR $userTimes['time_string'] .= $userTimes['seconds']._SECONDSSHORTHAND;
		}

*/

  if ($interval && eF_checkParameter($interval['from'], 'timestamp') && eF_checkParameter($interval['to'], 'timestamp')) {
   $from = $interval['from'];
   $to = $interval['to'];
  } else {
   $from = "00000000";
   $to = time();
  }

  if ($login && eF_checkParameter($login, 'login')) {
   $result = eF_getTableData("logs", "users_LOGIN, id, timestamp, action", "users_LOGIN = '$login' and timestamp between $from and $to", "id");
  } else {
   $result = eF_getTableData("logs", "users_LOGIN, id, timestamp, action", "timestamp between $from and $to", "id");
  }
  $userTimes = array();
  foreach ($result as $value) {
   $logs[$value['users_LOGIN']][] = $value;
  }

  foreach ($logs as $user => $result) {
   $totalTime = 0;
   $start = 0;
   $inlogin = 0;
   foreach ($result as $value) {
    if ($inlogin) {
     if ($value['action'] != 'logout' && $value['action'] != 'login'){
      if ($value['timestamp'] < ($start + 1800)) { //if it is inactive more than half an hour, we don't consider it
       $totalTime += $value['timestamp'] - $start;
       $start = $value['timestamp'];
      } else {
       //$totalTime += 900;   // we could consider half of this period or enitre in the future
       $start = $value['timestamp']; // It is needed to refresh start time even if time period was more half an hour. It was missing
      }
     } else if ($value['action'] == 'logout') {
      if ($value['timestamp'] < ($start + 1800)) { //if it is inactive more than half an hour, we don't consider it
       $totalTime += $value['timestamp'] - $start;
      } else {
       //$totalTime += 900; // we could consider half of this period or enitre in the future
      }
      $inlogin = 0;
     } else if ($value['action'] == 'login') {
      $inlogin = 1;
      $start = $value['timestamp'];
     }
    } else {
     if ($value['action'] == 'login') {
      $inlogin = 1;
      $start = $value['timestamp'];
     }
    }
   }

   $userTimes[$user] = eF_convertIntervalToTime($totalTime);
   $userTimes[$user]['total_seconds'] = $totalTime;
  }

  if ($login) {
   return $userTimes[$login];
  } else {
   return $userTimes;
  }
 }

 /**
	 * Previous EfrontStats::getUserTimes
	 *
	 * @param unknown_type $firstDay
	 */
 private static function getDeprecatedUserTimesPerDay2($firstDay) {
     for ($t = $firstDay; $t <= time(); $t+=86400) {
      $logs = eF_getTableData("logs", "timestamp, action, users_LOGIN", "timestamp >= ".$t." and timestamp < ".($t+86400), "timestamp");
   $timesPerUser = $resultPerUser = array();
      foreach ($logs as $key => $value) {
       $resultPerUser[$value['users_LOGIN']]['timestamp'][] = $value['timestamp'];
       $resultPerUser[$value['users_LOGIN']]['action'][] = $value['action'];
      }

      foreach ($resultPerUser as $login => $result) {
       $times = array();
       if (sizeof($result) > 0) {
        for ($i = 0; $i < sizeof($result['action']) - 1; $i++) { //The algorithm goes like this: We search for the 'login' actions in the log. When one is found, then we search either for the next 'login' or 'logout' action, if there are no other actions, or the last non-login or logout action. This way, we calculate the true time spent inside the system. If we calculated only the logout-login times, then when a user had closed a window without logging out first, the online time would be reported falsely
         if ($result['action'][$i] == 'login') {
          $count = $i + 1;
          $end_action = $result['timestamp'][$count];
          while ($result['action'][$count] != 'logout' && $result['action'][$count] != 'login' && $count < sizeof($result['action'])) {
           $end_action = $result['timestamp'][$count];
           $count++;
          }
          if ($end_action - $result['timestamp'][$i] <= 1800){ //only take into account intervals less than one hour
           $times['duration'][] = $end_action - $result['timestamp'][$i];
          }
         }
        }
       }
       if (!empty($times)) {
        $timesPerUser[$login] = array_sum($times['duration']);
       }
      }

   $dayLogs[$t] = $timesPerUser;
     }

     return $dayLogs;
 }
}
