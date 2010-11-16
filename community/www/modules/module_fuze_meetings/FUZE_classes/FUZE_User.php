<?php

/**

 * Represents the logic entity that handles the behaviour and state of 

 * a FUZE user.

 * 

 * @name FUZE_User

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_User extends FUZE_AbstractClass {
 private $_sys_id;
 private $_fuze_email, $_sys_email;
 private $_password;
 private $_login_url;
 private $_firstname;
 private $_lastname;
 private $_timezone;
 private $_date_added;
 private $_suspended;
 private $_meetings;
 private $_future_meetings;
 private $_meetings_per_lesson;
 private $_students_per_lesson;
 private $_lessons;
 public function __construct($id) {
  parent::__construct($id);
  $this->_meetings = array();
  $this->_future_meetings = array();
  $this->_meetings_per_lesson = array();
  $this->_lessons = array();
  $this->_init();
 }
 protected function _init() {
  try {
   $options = array();
   $this->_dao = FUZE_DAOFactory::getDAO($this, $options);
   $this->_dao->_init();
   $this->_to = $this->_dao->getTO();
   $this->_sys_id = $this->_to->get('sys_id');
   $this->_fuze_email = $this->_to->get('fuze_email');
   $this->_sys_email = $this->_to->get('sys_email');
   $this->_password = $this->_to->get('password');
   $this->_login_url = $this->_to->get('login_url');
   $this->_firstname = $this->_to->get('firstname');
   $this->_lastname = $this->_to->get('lastname');
   $this->_timezone = $this->_to->get('timezone');
   $this->_date_added = $this->_to->get('date_added');
   $this->_suspended = $this->_to->get('suspended');
   $this->_meetings = $this->_to->get('meetings');
   $this->_future_meetings = $this->_to->get('future_meetings');
   $this->_meetings_per_lesson = $this->_to->get('meetings_per_lesson');
   $this->_students_per_lesson = $this->_to->get('students_per_lesson');
   $this->_lessons = $this->_to->get('lessons');
  }
  catch (Exception $afe) {
   $this->_sys_id = '';
   $this->_sys_email = '';
   $this->_fuze_email = '';
   $this->_password = '';
   $this->_login_url = '';
   $this->_firstname = '';
   $this->_lastname = '';
   $this->_timezone = '';
   $this->_date_added = '';
   $this->_suspended = false;
   throw new Exception("FUZE user not found.");
  }
 }
 /**

	 * This is the implementation of the abstract method inherited from 

	 * the parent class AF_Controller_Abstract.

	 * 

	 * @return String The processed controller name.

	 */
 public function getControllerName() {
  return 'FUZE_User';
 }
 ///////////////////////////////////////////////////////////////////////////
 // GETTER METHODS BELOW
 ///////////////////////////////////////////////////////////////////////////
 public function getSysId() {
  return $this->_sys_id;
 }
 public function getFirstName() {
  return $this->_firstname;
 }
 public function getLastName() {
  return $this->_lastname;
 }
 public function getFuzeEmail() {
  return $this->_fuze_email;
 }

 public function getSysEmail() {
  return $this->_sys_email;
 }

 public function getPassword() {
  return $this->_password;
 }

 public function getTimezone() {
  return $this->_timezone;
 }

 public function getDateAddedRaw() {
  return $this->_date_added;
 }

 public function getLoginUrl() {
  return $this->_login_url;
 }

 public function getTranslatedDateAdded($to_tz = false, $format = false) {
  if (!$to_tz) $to_tz = 'UTC';
  if (!$format) $format = 'Y-m-d H:i:s';
  return FUZE_Tools::get_local_time($to_tz, $this->_date_added, $format);
 }

 public function isSuspended() {
  return ($this->_suspended ? true : false);
 }

 public function getMeetings() {
  return $this->_meetings;
 }

 public function getMeetingsCount() {
  return count($this->_meetings);
 }

 public function getMeeting($id) {
  $meeting_item = false;
  if (in_array($id, array_keys($this->_meetings))) {
   if ($this->_meetings[$id] === null) {
    try {
     $this->_meetings[$id] = new FUZE_Meeting($id);
     $meeting_item = $this->_meetings[$id];
    }
    catch (Exception $e) { /* DO NOTHING */ }
   }
  }

  return $meeting_item;
 }

 public function getFutureMeetings() {
  return $this->_future_meetings;
 }

 public function getFutureMeetingsCount() {
  return count($this->_future_meetings);
 }

 public function getFutureMeetingsSubset($args) {
  $meeting_array = array();
  if (isset($args['offset']) && isset($args['other']) &&
    isset($args['limit']) && !empty($args['limit']) &&
    isset($args['sort']) && !empty($args['sort']) &&
    isset($args['order']) && !empty($args['order'])
   ){
    $meeting_array = $this->_dao->getFutureMeetingsSubset($args);
  }

  return $meeting_array;
 }

 public function getMeetingsByLesson($lesson_id) {
  $meetings_array = array();
  if (isset($this->_meetings_per_lesson[$lesson_id]) && count($this->_meetings_per_lesson[$lesson_id])) {
   $meetings_array = $this->_meetings_per_lesson[$lesson_id];
  }

  return $meetings_array;
 }

 public function getMeetingsByLessonCount($lesson_id) {
  return count($this->getMeetingsByLesson($lesson_id));
 }

 public function getFutureMeetingsPage($current_page, $items_per_page) {
  $tmp_array = FUZE_Tools::get_elements($this->_future_meetings, $current_page, $items_per_page, true);
  $array = array();
  foreach ($tmp_array AS $meeting_id => $meeting_item) {
   $array[$meeting_id] = $this->getMeeting($meeting_id);
  }

  return $array;
 }

 public function getMeetingsByLessonPage($current_page, $items_per_page, $lesson_id) {
  $tmp_array = FUZE_Tools::get_elements($this->getMeetingsByLesson($lesson_id), $current_page, $items_per_page, true);
  $array = array();
  if (count($tmp_array)) {
   foreach ($tmp_array AS $meeting_id) {
    $array [$meeting_id] = $this->getMeeting($meeting_id);
   }
  }

  return $array;
 }

 public function getLessons() {
  return $this->_lessons;
 }

 public function hasLesson($lesson_id) {
  $success = false;
  if (isset($this->_lessons[$lesson_id])) $success = true;

  return $success;
 }

 public function getLessonName($lesson_id) {
  $name = false;
  if (isset($this->_lessons[$lesson_id])) $name = $this->_lessons[$lesson_id];

  return $name;
 }

 public function getStudentsByLesson($lesson_id) {
  $students_array = array();
  if (isset($this->_students_per_lesson[$lesson_id]) && count($this->_students_per_lesson[$lesson_id])) {
   $students_array = $this->_students_per_lesson[$lesson_id];
  }

  return $students_array;
 }

 public function getStudentsByLessonSubset($args) {
  $student_array = array();
  if (isset($args['lesson_id']) && !empty($args['lesson_id']) &&
    isset($args['offset']) && isset($args['other']) &&
    isset($args['limit']) && !empty($args['limit']) &&
    isset($args['sort']) && !empty($args['sort']) &&
    isset($args['order']) && !empty($args['order'])
   ){
    $student_array = $this->_dao->getStudentsByLessonSubset($args);
  }

  return $student_array;
 }

 public function getStudentsByLessonCount($lesson_id) {
  return count($this->getStudentsByLesson($lesson_id));
 }

 public function getStudentsByLessonPage($current_page, $items_per_page, $lesson_id) {
  $tmp_array = FUZE_Tools::get_elements($this->getStudentsByLesson($lesson_id), $current_page, $items_per_page, true);
  $array = array();
  if (count($tmp_array)) {
   foreach ($tmp_array AS $key => $student_id) {
    $array[$student_id] = null;
   }
  }

  return $array;
 }

 ///////////////////////////////////////////////////////////////////////////
 // END GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////

 public function setLessonsAsProfessor($lessons) {

 }

 public function suspend() {
  if (!$this->_suspended) {
   ## At this point we cancel all the meetings scheduled by
   ## this user.
   $this->_removeAllMeetings();
   $this->_suspended = 1;
   $this->_to->set('suspended', $this->_suspended);
   $this->_to->setChanged();
  }
 }

 public function unsuspend() {
  if ($this->_suspended) {
   $this->_suspended = 0;
   $this->_to->set('suspended', $this->_suspended);
   ## At the same time we make the user look like a new user
   $this->_date_added = time();
   $this->_to->set('date_added', $this->_date_added);
   $this->_to->setChanged();
  }
 }

 protected function _removeAllMeetings() {
  if (count($this->_meetings)) {
   foreach ($this->_meetings AS $meeting_id => $meeting) {
    $meeting = $this->getMeeting($meeting_id);
    if ($meeting) {
     ## All calendar events set for this meeting should also
     ## be removed from system. This is carried out inside
     ## the meeting object currently, but it should possibly
     ## be moved inside the FUZE_User object logic.
     $meeting->cancel();
    }
   }
  }
 }

 public function addMeeting($args) {
  $args ['fuze_email'] = $this->_fuze_email;
  $args ['fuze_passwd'] = $this->_password;
  $response = array('success' => false);
  try {
   $add_meeting_response = $this->_dao->addMeeting($args);
   if ($add_meeting_response ['success']) {
    $meeting_id = $add_meeting_response['meeting_id'];
    $this->_meetings [$meeting_id] = null;
    $meeting_item = $this->getMeeting($meeting_id);
    $response ['success'] = true;
    $response ['meeting_item'] = $meeting_item;
   }
   else {
    $response ['error_msg'] = $add_meeting_response ['error_msg'];
   }
  }
  catch (Exception $e) {
   return array('success' => false, 'error_msg' => _FUZE_PROF_SCHEDULE_ERROR);
  }

  return $response;
 }

 public function editMeeting($args) {
  $args ['fuze_email'] = $this->_fuze_email;
  $args ['fuze_passwd'] = $this->_password;
  $response = array('success' => false);
  try {
   $edit_meeting_response = $this->_dao->editMeeting($args);
   if ($edit_meeting_response ['success']) {
    $response ['success'] = true;
   }
   else {
    $response ['error_msg'] = $edit_meeting_response ['error_msg'];
   }
  }
  catch (Exception $e) {
   return array('success' => false, 'error_msg' => _FUZE_PROF_SCHEDULE_ERROR);
  }

  return $response;
 }

 public function startMeeting($args) {
  $args ['fuze_email'] = $this->_fuze_email;
  $args ['fuze_passwd'] = $this->_password;
  $args ['launch'] = true;
  $meeting_item = false;
  try {
   $meeting_id = $this->_dao->addMeeting($args);
   $this->_meetings [$meeting_id] = null;
   $meeting_item = $this->getMeeting($meeting_id);
  }
  catch (Exception $e) {
   return _FUZE_PROF_SCHEDULE_ERROR;
  }

  return $meeting_item;
 }

 public function removeMeeting($id) {
  $success = false;
  if (isset($this->_meetings[$id])) {
   unset($this->_meetings[$id]);
   $this->_to->set('meetings', $this->_meetings);
   $this->_to->setChanged();
  }

  return $success;
 }
}
