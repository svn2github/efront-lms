<?php

/**

 * The DAO of the FUZE_Meeting class.

 * 

 * @name FUZE_MeetingDAO

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_MeetingDAO extends FUZE_AbstractDAO {
 private $_attendees;
 public function __construct($controller) {
  parent::__construct($controller);
  $this->_attendees = array();
  $this->_db = $GLOBALS['db'];
  $this->_init();
 }
 public function __destruct() {
  /*

		if ($this->_to->isChanged()) {

			// Extract all value from TO

		}

		*/
 }
 public function _init() {
  $res = eF_getTableData("`_mod_fm_meeting`","*", "`id` = " . $this->_controller_id);
  if (is_array($res) && count($res)) {
   $this->_to->set('subject', $res[0]['subject']);
   $this->_to->set('user_id', $res[0]['user_id']);
   $this->_to->set('launch_url', $res[0]['launch_url']);
   $this->_to->set('attend_url', $res[0]['attend_url']);
   $this->_to->set('starttime', $res[0]['starttime']);
   $this->_to->set('calendar_id', $res[0]['calendar_id']);
   $this->_to->set('lesson_id', $res[0]['lesson_id']);
   $this->_to->set('fuze_meeting_id', $res[0]['fuze_meeting_id']);
   $this->_to->set('launch_now_url', $res[0]['launch_now_url']);
   $res = eF_getTableData("`_mod_fm_meeting_attendee`","`id`", "`meeting_id`=" . $this->_controller_id);
   if (is_array($res) && count($res)) {
    foreach ($res as $entry) {
     $this->_attendees [$entry['id']] = null;
    }
   }
   $this->_to->set('attendees', $this->_attendees);
   // We also need to get the lesson name
   $res = eF_getTableData("`lessons`","`name`","`id` = {$this->extractFromTO('lesson_id')}");
   if (is_array($res) && count($res)) {
    $this->_to->set('lesson_name', $res[0]['name']);
   }
  }
 }
 /**

	 * Removes a scheduled meeting from the system. The calendar entry related 

	 * to this meeting needs to be removed and then the entries in table 

	 * `_mod_fm_meeting_attendee` related to this meeting are taken care of

	 * by the RDBMS as per foreign key integrity feature.

	 * 

	 * @return Boolean Indicates success or failure.

	 * 

	 * @access public

	 */
 public function cancel() {
  $success = false;
  // We need to modify all attendees that the meeting is now cancelled
  // but we're doing that only after the meeting has been successfully 
  // removed. We need, however, to retrieve the attendees' email
  // addresses here.
  $_sql_prof_id = $this->extractFromTO('user_id');
  $attendees = array();
  $_sql_tables = "`_mod_fm_meeting_attendee` AS `_mfma`, `users` AS `_up`, `users` AS `_us`, `_mod_fm_user` AS `_mfu`";
  $_sql_fields = "DISTINCT(CONCAT(`_up`.`name`, ' ', `_up`.`surname`)) AS `_pname`, CONCAT(`_us`.`name`, ' ', `_us`.`surname`) AS `_sname`, `_us`.`email` AS `_semail`, `_up`.`email` AS `_pemail`";
  $_sql_conditions = "`_us`.`id` = `_mfma`.`sys_id` AND `_up`.`id` = `_mfu`.`sys_id` AND `_mfu`.`id` = {$_sql_prof_id} AND `_mfma`.`meeting_id` = {$this->_controller_id}";
  $res = eF_getTableData($_sql_tables, $_sql_fields, $_sql_conditions);
  if (is_array($res) && count($res)) {
   foreach ($res AS $key => $entry) {
    $attendees [$key] = array();
    $attendees [$key]['professor_name'] = $entry ['_pname'];
    $attendees [$key]['student_name'] = $entry ['_sname'];
    $attendees [$key]['professor_email'] = $entry ['_pemail'];
    $attendees [$key]['student_email'] = $entry ['_semail'];
   }
  }
  // We need to remove the calendar entry
  $calendar_id = $this->extractFromTO('calendar_id');
  if ($calendar_id) {
   eF_deleteTableData("`calendar`","`id` = " . $calendar_id);
  }
  // Then we remove the meeting itself
  if ($res = eF_deleteTableData("`_mod_fm_meeting`","`id` = " . $this->_controller_id)) {
   $success = true;
   if (count($attendees)) {
    // We notify attendees by email
    $meeting_name = $this->extractFromTO('subject');
    $meeting_starttime = date('r', $this->extractFromTO('starttime'));
    $meeting_lesson_name = $this->extractFromTO('lesson_name');
    $email_subject = str_ireplace('###MEETING_NAME###', $meeting_name, _FUZE_EMAIL_MEETING_NOTIFICATION_CANCELLED_SUBJECT);
    $email_content = _FUZE_EMAIL_MEETING_NOTIFICATION_CANCELLED_CONTENT;
    $email_content = str_ireplace('###LESSON_NAME###', $meeting_lesson_name, $email_content);
    $email_content = str_ireplace('###MEETING_NAME###', $meeting_name, $email_content);
    $email_content = str_ireplace('###MEETING_STARTTIME###', $meeting_starttime, $email_content);
    // Rather ineffective as we need to send each email separately
    // because of different user_name embedded in text in each one
    // of the emails.
    foreach ($attendees AS $email_attrs) {
     $this_email_content = $email_content;
     $this_email_content = str_ireplace('###USER_NAME###', $email_attrs ['student_name'], $this_email_content);
     $this_email_content = str_ireplace('###PROFESSOR_NAME###', $email_attrs ['professor_name'], $this_email_content);
     FUZE_Tools::send_email($email_attrs ['professor_email'], $email_attrs ['student_email'], $this_email_content, $this_email_content, $email_subject);
    }
   }
  }
  return $success;
 }
 public function launchMeeting($args) {
  $function_response = array('success' => false);
  if (is_array($args) && count($args)) {
   if (isset($args['meetingid']) && !empty($args['meetingid']) &&
    isset($args['fuze_email']) && !empty($args['fuze_email']) &&
       isset($args['fuze_password']) && !empty($args['fuze_password'])
    ) {
     $options = array();
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_LAUNCH;
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS] = array();
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $args ['fuze_email'];
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $args ['fuze_password'];
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID] = $args ['meetingid'];
     $handle = RequestFactory::getRequestHandle($options);
     try {
      $response = $handle->runRequest();
     }
     catch (Exception $e) {
      $response = $e->getMessage();
     }
     if (is_array($response)) {
      if (isset($response ['launch_now_url'])) {
       $function_response ['success'] = true;
       $function_response ['url'] = $response ['launch_now_url'];
      }
      elseif (isset($response ['upgrade_url'])) {
       $function_response ['success'] = false;
       $function_response ['url'] = $response ['upgrade_url'];
      }
     }
     else {
      $msg = constant($response);
      if ($msg) $function_response ['error_msg'] = $msg;
      else $function_response ['error_msg'] = $response;
     }
   }
  }
  return $function_response;
 }
}
