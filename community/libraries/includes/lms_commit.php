<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

try {
 //debug();
 //pr($_POST);pr($_GET);
 unset($_POST['_']);

 //id and credit are not stored in any table
 $credit = true;
 if ($_POST['credit'] == 'no-credit') {
  $credit = false;
 }
 unset ($_POST['credit']);
 //unset ($_POST['session_time']);
 unset ($_POST['id']);
 unset ($_POST['popup']);

 //used only in scorm_data
 $fields['timestamp'] = time();
 foreach ($_POST as $key => $value) { //Store POST parameters in a variable, so that they may be inserted in a database tabl
  $fields[$key] = $value;
 }
 $fields['users_LOGIN'] = $_SESSION['s_login']; //The current user
 if (!isset($fields['content_ID'])) {
  exit;
 }
 if ($_GET['scorm_version'] != '2004') {
  $trackActivityInfo[$fields['content_ID']]['completion_status'] = strtolower($fields['lesson_status']);
  $trackActivityInfo[$fields['content_ID']]['success_status'] = strtolower($fields['lesson_status']);
  unset($fields['objectives']);
  unset($fields['navigation']);
  unset($fields['completion_status']);
  unset($fields['success_status']);
  unset($fields['shared_data']);
  unset($fields['comments_from_lms']);
  unset($fields['comments_from_learner']);
  unset($fields['interactions']);
  unset($fields['learner_preferences']);
  unset($fields['score_scaled']);
  unset($fields['progress_measure']);
  unset($fields['finish']);
  $result = eF_getTableData("scorm_data", "total_time,id", "content_ID=".$fields['content_ID']." AND users_LOGIN='".$fields['users_LOGIN']."'");
 }
 if (strtolower($fields['completion_status']) == 'passed' ||
  strtolower($fields['completion_status']) == 'completed' ||
  strtolower($fields['lesson_status']) == 'passed' ||
  strtolower($fields['lesson_status']) == 'completed') {
   $seenUnit = true;
 } else {
  $seenUnit = false;
 }
 $scoUser = EfrontUserFactory :: factory($_SESSION['s_login'], false, 'student');
 $scoLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
 $scoUnit = new EfrontUnit($fields['content_ID']);
 if (sizeof($result) > 0) { //This means that the students re-enters the unit
  if (isset($fields['total_time'])&&isset($fields['session_time']) && $fields['total_time'] && $fields['session_time']) { //Make sure that time is properly converted, for example 35+35 minutes become 1 hour 10 minutes, instead if 70 minutes
   $time_parts1 = explode(":", $fields['total_time']);
   $time_parts2 = explode(":", $fields['session_time']);
   $time_parts[0] = $time_parts1[0] + $time_parts2[0];
   $time_parts[1] = $time_parts1[1] + $time_parts2[1];
   $time_parts[2] = $time_parts1[2] + $time_parts2[2];
   //print_r($time_parts1);print_r($time_parts2);print_r($time_parts);
   $time_parts[1] = $time_parts[1] + floor($time_parts[2]/60);
   $time_parts[2] = fmod($time_parts[2], 60);
   $time_parts[0] = $time_parts[0] + floor($time_parts[1]/60);
   $time_parts[1] = fmod($time_parts[1], 60);
   $fields['total_time'] = sprintf("%04d",$time_parts[0]).":".sprintf("%02d",$time_parts[1]).":".sprintf("%05.2f",$time_parts[2]);
  }
  $doneContent = eF_getTableData("users_to_lessons", "done_content", "users_LOGIN='".$scoUser->user['login']."' and lessons_ID=".$scoLesson->lesson['id']);
  $doneContent = unserialize($doneContent[0]['done_content']);
  //pr($doneContent[$scoUnit['id']]);pr($scoUnit['options']['reentry_action']);exit;
  //If the user has passed this unit and we have selected that the reentry action will leave status unchanged, then switch to no-credit mode
  if (isset($doneContent[$scoUnit['id']]) && $scoUnit['options']['reentry_action'] && !$seenUnit) {
   $credit=false;
  }
  unset($fields['session_time']);
  if ($_GET['scorm_version'] == '2004') {
   eF_updateTableData("scorm_data_2004", $fields, "id=".$result[0]['id']); //Update old values with new ones
  } elseif($credit) {
   eF_updateTableData("scorm_data", $fields, "id=".$result[0]['id']); //Update old values with new ones
  }
 } else {
  if (isset($fields['total_time']) && $fields['total_time']) {
   $time_parts = explode(":", $fields['total_time']);
   $fields['total_time'] = sprintf("%04d",$time_parts[0]).":".sprintf("%02d",$time_parts[1]).":".sprintf("%05.2f",$time_parts[2]);
  }
  $fields['total_time'] = $fields['session_time'];
  unset($fields['session_time']);
  if ($_GET['scorm_version'] == '2004') {
   $result = eF_insertTableData("scorm_data_2004", $fields); //Insert a new entry that relates the current user with this SCO
  } elseif ($credit) {
   $result = eF_insertTableData("scorm_data", $fields); //Insert a new entry that relates the current user with this SCO
  }
 }
 if ($credit && $seenUnit) {
  $scoUser -> setSeenUnit($scoUnit, $scoLesson, true);
 }
 $newUserProgress = EfrontStats :: getUsersLessonStatus($scoLesson, $scoUser -> user['login']);
 $newPercentage = $newUserProgress[$scoLesson -> lesson['id']][$scoUser -> user['login']]['overall_progress'];
 $newConditionsPassed = $newUserProgress[$scoLesson -> lesson['id']][$scoUser -> user['login']]['conditions_passed'];
 $newLessonPassed = $newUserProgress[$scoLesson -> lesson['id']][$scoUser -> user['login']]['lesson_passed'];
 //pr($trackActivityInfo);
 echo json_encode(array($newPercentage, $newConditionsPassed, $newLessonPassed, $scormState, $redirectTo, $trackActivityInfo));
} catch (Exception $e) {
 //pr($e);
}
exit;
