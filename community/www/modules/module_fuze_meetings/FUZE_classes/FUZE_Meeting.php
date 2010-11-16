<?php

/**

 * Represents the logic entity that handles the behaviour and state of a 

 * FUZE meeting.

 * 

 * @name FUZE_Meeting

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_Meeting extends FUZE_AbstractClass {
 private $_subject;
 private $_user_id;
 private $_launch_url;
 private $_launch_now_url;
 private $_attend_url;
 private $_starttime;
 private $_calendar_id;
 private $_fuze_meeting_id;
 private $_lesson_id;
 private $_lesson_name;
 private $_attendees;
 public function __construct($id) {
  parent::__construct($id);
  $this->_attendees = array();
  $this->_init();
 }
 protected function _init() {
  try {
   $options = array();
   $this->_dao = FUZE_DAOFactory::getDAO($this, $options);
   $this->_dao->_init();
   $this->_to = $this->_dao->getTO();
   $this->_subject = $this->_to->get('subject');
   $this->_user_id = $this->_to->get('user_id');
   $this->_launch_url = $this->_to->get('launch_url');
   $this->_attend_url = $this->_to->get('attend_url');
   $this->_starttime = $this->_to->get('starttime');
   $this->_calendar_id = $this->_to->get('calendar_id');
   $this->_fuze_meeting_id = $this->_to->get('fuze_meeting_id');
   $this->_launch_now_url = $this->_to->get('launch_now_url');
   $this->_lesson_id = $this->_to->get('lesson_id');
   $this->_lesson_name = $this->_to->get('lesson_name');
   $this->_attendees = $this->_to->get('attendees');
  }
  catch (Exception $afe) {
   $this->_subject = '';
   $this->_user_id = '';
   $this->_launch_url = '';
   $this->_attend_url = '';
   $this->_starttime = '';
   $this->_calendar_id = '';
   $this->_fuze_meeting_id = '';
   $this->_launch_now_url = '';
   $this->_lesson_id = '';
   $this->_lesson_name = '';
   $this->_attendees = array();
   throw new Exception("FUZE meeting not found.");
  }
 }
 /**

	 * This is the implementation of the abstract method inherited from 

	 * the parent class AF_Controller_Abstract.

	 * 

	 * @return String The processed controller name.

	 */
 public function getControllerName() {
  return get_class($this);
 }
 ///////////////////////////////////////////////////////////////////////////
 // GETTER METHODS BELOW
 ///////////////////////////////////////////////////////////////////////////
 public function getSubject() {
  return $this->_subject;
 }
 public function getUserId() {
  return $this->_user_id;
 }
 public function getLaunchUrl() {
  return $this->_launch_url;
 }
 public function getLaunchNowUrl() {
  return $this->_launch_now_url;
 }

 public function getAttendUrl() {
  return $this->_attend_url;
 }

 public function getStartTime() {
  return $this->_starttime;
 }

 public function getStartTimeTranslated($tz_to, $format = 'Y-m-d H:i') {
  return FUZE_Tools::get_local_time($tz_to, $this->_starttime, $format);
 }

 public function getElapsedTime() {
  return ($this->_starttime - time());
 }

 public function getFuzeMeetingId() {
  return $this->_fuze_meeting_id;
 }

 public function getLessonId() {
  return $this->_lesson_id;
 }

 public function getLessonName() {
  return $this->_lesson_name;
 }

 public function getAttendeesAll() {
  return $this->_attendees;
 }

 public function getAttendeesCount() {
  return count($this->_attendees);
 }

 public function getAttendee($id) {
  $attendee_item = false;
  if (in_array($id, array_keys($this->_attendees))) {
   if ($this->_attendees [$id] === null) {
    try {
      $this->_attendees [$id] = new FUZE_Meeting_Attendee($id);
      $attendee_item = $this->_attendees [$id];
    }
    catch (Exception $e) { /* DO NOTHING */ }
   }
  }

  return $attendee_item;
 }

 public function isHappeningNow() {
  $result = false;
  if ($this->_starttime < time()+120) {
   $result = true;
  }

  return $result;
 }

 ///////////////////////////////////////////////////////////////////////////
 // END GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////

 ///////////////////////////////////////////////////////////////////////////
 // SETTER METHODS BELOW
 ///////////////////////////////////////////////////////////////////////////

 ///////////////////////////////////////////////////////////////////////////
 // END SETTER METHODS
 ///////////////////////////////////////////////////////////////////////////

 public function launchMeeting($args) {
  $response = array('success' => false);
  $args ['meetingid'] = $this->_fuze_meeting_id;
  $function_response = $this->_dao->launchMeeting($args);
  if ($function_response ['success']) {
   if (is_string($function_response ['url'])) {
    $response ['success'] = true;
    $this->_launch_now_url = $function_response ['url'];
    $response ['url'] = $this->_launch_now_url;
   }
   else {
    $response ['error_msg'] = _FUZE_PROF_LAUNCH_ERROR;
   }
  }
  else {
   $response ['error_msg'] = $function_response ['error_msg'];
  }

  return $response;
 }

 /**

	 * Sets the attendees of the meeting.

	 * 

	 * @param Array $data The array that holds the system id's of the users 

	 * that are set to attend this meeting.

	 * 

	 * @access public

	 */
 public function setAttendees($data) {
  if (is_array($data)) {
  }
 }
 /**

	 * Cancels the meeting, can be called either because a meeting is 

	 * explicitly cancelled or because the professor that has scheduled 

	 * the meeting is suspended.

	 * 

	 * @return Boolean Indicates success or failure.

	 * 

	 * @access public

	 */
 public function cancel() {
  $function_response = array('success' => false);
  if ($this->_dao->cancel()) {
   $function_response ['success'] = true;
  }
  return $function_response;
 }
}
