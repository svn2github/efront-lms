<?php

/**

 * The DAO of the FUZE_User class.

 * 

 * @name FUZE_UserDAO

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_UserDAO extends FUZE_AbstractDAO {
 private $_meetings;
 private $_meetings_as_professor;
 private $_meetings_as_student;
 private $_meetings_per_lesson;
 private $_future_meetings;
 private $_students_per_lesson;
 private $_lessons;
 private $_login;
 public function __construct($controller) {
  parent::__construct($controller);
  $this->_meetings = array();
  $this->_lessons = array();
  $this->_meetings_per_lesson = array();
  $this->_students_per_lesson = array();
  $this->_future_meetings = array();
  $this->_db = $GLOBALS['db'];
  $this->_init();
 }
 public function __destruct() {
  /* THE ONLY CHANGE PROPAGATED TO DB IS SUSPENDED MODE AND MEETINGS */
  if ($this->_to->isChanged()) {
   // Extract all value from TO
   $args = array();
   $args ['`suspended`'] = $this->extractFromTO('suspended');
   $args ['`date_added`'] = $this->extractFromTO('date_added');
   // Flush new values to DB
   $GLOBALS['db'] = $this->_db;
   eF_updateTableData('`_mod_fm_user`', $args, '`id`=' . $this->_controller_id);
   // Managing meetings below
   $tmp_meetings = $this->_to->get('meetings');
   if (count($tmp_meetings) < count($this->_meetings)) {
    $removed_ids = array_keys(array_diff_key($this->_meetings, $tmp_meetings));
    if (count($removed_ids)) {
     $removed_string = '';
     foreach ($removed_ids AS $id) {
      $removed_string .= $id . ',';
     }
     $removed_string = substr($removed_string, 0, -1); // Get rid of trailling comma
     // First we need to remove the calendar entries for these meetings.
     eF_deleteTableData("`calendar`", "`id` IN (SELECT DISTINCT(`calendar_id`) FROM `_mod_fm_meeting` WHERE `id` IN ($removed_string))");
     // Then we remove the meeting entries
     eF_deleteTableData("`_mod_fm_meeting`", "`id` IN ($removed_string)");
    }
   }
  }
 }
 /**

	 * Retrieving contents of the `mod_jam_job` table so as to initialise 

	 * the state of the job manager.

	 * 

	 * @access public

	 */
 public function _init() {
  $res = eF_getTableData("`_mod_fm_user`","*", "`id`=".$this->_controller->getId());
  if (is_array($res) && count($res)) {
   $this->_to->set('sys_id', $res[0]['sys_id']);
   $this->_to->set('password', $res[0]['password']);
   $this->_to->set('login_url', $res[0]['login_url']);
   $this->_to->set('fuze_email', $res[0]['fuze_email']);
   $this->_to->set('date_added', $res[0]['date_added']);
   $this->_to->set('suspended', $res[0]['suspended']);
   // Sti sunexeia vriskoume ta upoloipa stoixeia ta opoia einai ta stoixeia tou user sto systima.
   $res = eF_getTableData("`users`", "*", '`id` = ' . $res [0]['sys_id']);
   if (is_array($res) && count($res)) {
    $this->_to->set('sys_email', $res[0]['email']);
    $this->_to->set('timezone', $res[0]['timezone']);
    $this->_to->set('firstname', $res[0]['name']);
    $this->_to->set('lastname', $res[0]['surname']);
    $this->_to->set('login', $res[0]['login']);
    $this->_login = $res[0]['login'];
    // We need to retrieve information about the meetings of this user as professor
    $res = eF_getTableData("`_mod_fm_meeting`", "`id`", '`user_id` = ' . $this->_controller->getId());
    if (is_array($res) && count($res)) {
     foreach ($res AS $entry) {
      $this->_meetings [$entry['id']] = null;
     }
    }
    $this->_to->set('meetings', $this->_meetings);
   }
   // Get user lessons
   $res = eF_getTableData("`users_to_lessons` AS `utl`, `lessons` AS `l`","`utl`.`lessons_ID` AS `lid`, `l`.`name` AS `lname`","`utl`.`users_LOGIN` = '" . $this->_login . "' AND `utl`.`archive` = 0 AND `utl`.`user_type` = 'professor' AND `l`.`id` = `utl`.`lessons_ID`");
   if (is_array($res) && count($res)) {
    foreach ($res AS $lesson) {
     $this->_lessons [$lesson['lid']] = $lesson['lname'];
    }
   }
   $this->_to->set('lessons', $this->_lessons);
   // Get the students per lesson
   $lesson_ids = array_keys($this->_lessons);
   if (count($lesson_ids)) {
    foreach ($lesson_ids AS $lesson_id) {
     $this->_students_per_lesson [''.$lesson_id] = array();
     $res = eF_getTableData("`users_to_lessons` AS `utl`, `users` AS `u`", "`u`.`id` AS `uid`", "`utl`.`lessons_ID` = " . $lesson_id ." AND `utl`.`archive` = 0 AND `utl`.`user_type` = 'student' AND `u`.`login` = `utl`.`users_LOGIN` AND `u`.`archive` = 0","`surname`");
     if (is_array($res) && count($res)) {
      foreach ($res AS $student) {
       $this->_students_per_lesson[$lesson_id][] = $student['uid'];
      }
     }
    }
   }
   $this->_to->set('students_per_lesson', $this->_students_per_lesson);
   // Get meetings by lesson id
   if (count($lesson_ids)) {
    foreach ($lesson_ids AS $lesson_id) {
     $this->_meetings_per_lesson [''.$lesson_id] = array();
     // we're only interested in meetings that are being held 
     // right now or are scheduled for the future.
     $timestamp = time() - 300; // 5 minutes ago
     $res = eF_getTableData("`_mod_fm_meeting`","`id`","`starttime` > " . $timestamp . " AND `lesson_id` = " . $lesson_id . " AND `user_id` = " . $this->_controller_id, "`starttime`");
     if (is_array($res) && count($res)) {
      foreach ($res AS $meeting) {
       $this->_meetings_per_lesson [$lesson_id][] = $meeting['id'];
      }
     }
    }
   }
   $this->_to->set('meetings_per_lesson',$this->_meetings_per_lesson);
   // Get future meetings only (all future meetings)
   // We're only interested in the meetings that are scheduled
   // starting 5 minutes ago and any time in the future.
   $timestamp = time() - 300; // 5 minutes ago
   $res = eF_getTableData("`_mod_fm_meeting`","`id`","`starttime` > " . $timestamp . " AND `user_id` = " . $this->_controller_id);
   if (is_array($res) && count($res)) {
    foreach ($res AS $meeting) {
     $this->_future_meetings [] = $meeting['id'];
    }
   }
   $this->_to->set('future_meetings',$this->_future_meetings);
  }
 }
 public function addMeeting($args) {
  $function_response = array('success' => false);
  if (is_array($args) && count($args)) {
   if (isset($args['subject']) && !empty($args['subject']) &&
       isset($args['starttime']) && !empty($args['starttime']) &&
       isset($args['lesson_id']) && !empty($args['lesson_id']) &&
       isset($args['fuze_email']) && !empty($args['fuze_email']) &&
       isset($args['fuze_passwd']) && !empty($args['fuze_passwd']) &&
       isset($args['students']) && !empty($args['students']) &&
       isset($args['launch'])
    ) {
     // Here we have to interact with the proxy to schedule the meeting
     // If the value for 'launch' is true then we start the meeting immediately
     // otherwise we schedule it for later according to starttime.
     if ( !$args['launch'] ) {
      $starttime = FUZE_Tools::get_local_time('UTC', $args['starttime'],'r');
      $endtime = FUZE_Tools::get_local_time('UTC', $args['starttime']+3600,'r');
      $options = array();
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_SCHEDULE;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS] = array();
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $args ['fuze_email'];
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $args ['fuze_passwd'];
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME] = $starttime;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME] = $endtime;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL_DEFAULT;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE_DEFAULT;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE_DEFAULT;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT] = $args['subject'];

      $handle = RequestFactory::getRequestHandle($options);
      try {
       $response = $handle->runRequest();
      }
      catch (Exception $e) {
       $response = $e->getMessage();
      }

      if (is_array($response) && isset($response['launchmeetingurl']) && !empty($response['launchmeetingurl']) &&
       isset($response['meetingurl']) && !empty($response['meetingurl']) &&
       isset($response['fuze_meeting_id']) && !empty($response['fuze_meeting_id'])
       ) {
        $calendar_id = false;
        // We add a new event in the calendar if this is requested.
        if ($args ['add_events']) {
         $bind = array();
         $bind ['data'] = _FUZE_CALENDAR_MEETING_NOTIFICATION;
         $bind ['timestamp'] = $args ['starttime'];
         $bind ['type'] = 'lesson';
         $bind ['foreign_ID'] = $args ['lesson_id'];
         $bind ['users_LOGIN'] = $this->_login;
         $table_name = '`calendar`';
         $calendar_id = eF_insertTableData($table_name, $bind);
        }

        // We create the new entry in the meeting table
        $bind = array();
        $bind ['user_id'] = $this->_controller_id;
        $bind ['subject'] = $args ['subject'];
        $bind ['starttime'] = $args ['starttime'];
        $bind ['launch_url'] = $response ['launchmeetingurl'];
        $bind ['attend_url'] = $response ['meetingurl'];
        $bind ['lesson_id'] = $args ['lesson_id'];
        $bind ['fuze_meeting_id'] = $response ['fuze_meeting_id'];
        if ($calendar_id) $bind ['calendar_id'] = $calendar_id;
        $table_name = '`_mod_fm_meeting`';
        $meeting_id = false;
        try {
         $meeting_id = eF_insertTableData($table_name, $bind);
         $function_response ['success'] = true;
         $function_response ['meeting_id'] = $meeting_id;
        }
        catch (Exception $e) {
         $function_response ['error_msg'] = $e->getMessage();
        }

        if ($meeting_id) {
         // We create the entries in the attendee table
         $bind = array();
         foreach ($args['students'] AS $student_id) {
          $array = array();
          $array ['meeting_id'] = $meeting_id;
          $array ['sys_id'] = $student_id;
          $bind [] = $array;
         }
         $table_name = '`_mod_fm_meeting_attendee`';
         eF_insertTableDataMultiple($table_name, $bind);

         // We notify attendees by email if this is requested.
         if ($args ['send_invites']) {
          $attendees = array();
          $_sql_tables = "`_mod_fm_meeting_attendee` AS `_mfma`, `users` AS `_up`, `users` AS `_us`, `_mod_fm_user` AS `_mfu`";
          $_sql_fields = "DISTINCT(CONCAT(`_up`.`name`, ' ', `_up`.`surname`)) AS `_pname`, CONCAT(`_us`.`name`, ' ', `_us`.`surname`) AS `_sname`, `_us`.`email` AS `_semail`, `_up`.`email` AS `_pemail`";
          $_sql_conditions = "`_us`.`id` = `_mfma`.`sys_id` AND `_up`.`id` = `_mfu`.`sys_id` AND `_mfu`.`id` = {$this->_controller_id} AND `_mfma`.`meeting_id` = {$meeting_id}";
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

          if (count($attendees)) {
           // Retrieve the lesson_name
           $meeting_lesson_name = '';
           $res = eF_getTableData("`lessons`", "`name`", "`id` = {$args['lesson_id']}");
           if (is_array($res) && count($res)) {
            $meeting_lesson_name = $res [0]['name'];
           }
           // We notify attendees by email
           $meeting_name = $args ['subject'];
           $meeting_starttime = date('r', $args ['starttime']);
           $email_subject = str_ireplace('###MEETING_NAME###', $meeting_name, _FUZE_EMAIL_MEETING_NOTIFICATION_NEW_SUBJECT);
           $email_content = _FUZE_EMAIL_MEETING_NOTIFICATION_NEW_CONTENT;
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
        }
        else {
         if ($calendar_id) {
          // We have to remove the erroneously added calendar entry
          $sql = 'DELETE FROM `calendar` WHERE `id` = ' . $calendar_id;
          eF_executeNew($sql);
         }
        }
      }
      else {
       $msg = constant($response);
       if ($msg) $function_response ['error_msg'] = $msg;
       else $function_response ['error_msg'] = $response;
      }
     }
     elseif ($args ['launch']) {
      // This is the case when we immediately start the meeting
      $starttime = FUZE_Tools::get_local_time('UTC', $args['starttime'],'r');
      $endtime = FUZE_Tools::get_local_time('UTC', $args['starttime']+3600,'r');
      $options = array();
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_START;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS] = array();

      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $args ['fuze_email'];
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $args ['fuze_passwd'];
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME] = $starttime;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME] = $endtime;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL_DEFAULT;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE_DEFAULT;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE_DEFAULT;
      $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT] = $args['subject'];

      $handle = RequestFactory::getRequestHandle($options);
      try {
       $response = $handle->runRequest();
      }
      catch (Exception $e) {
       $response = $e->getMessage();
      }

      if (is_array($response) && isset($response['launchmeetingurl']) && !empty($response['launchmeetingurl']) &&
       isset($response['meetingurl']) && !empty($response['meetingurl']) &&
       isset($response['fuze_meeting_id']) && !empty($response['fuze_meeting_id']) &&
       isset($response['launch_now_url']) && !empty($response['launch_now_url'])
       ) {
        // We create the new entry in the meeting table
        $bind = array();
        $bind ['user_id'] = $this->_controller_id;
        $bind ['subject'] = $args ['subject'];
        $bind ['starttime'] = $args ['starttime'];
        $bind ['launch_url'] = $response ['launchmeetingurl'];
        $bind ['attend_url'] = $response ['meetingurl'];
        $bind ['lesson_id'] = $args ['lesson_id'];
        $bind ['fuze_meeting_id'] = $response ['fuze_meeting_id'];
        $bind ['launch_now_url'] = $response ['launch_now_url'];
        $table_name = '`_mod_fm_meeting`';
        try {
         $meeting_id = eF_insertTableData($table_name, $bind); // Returns the last_insert_id or false on failure.
         $function_response ['success'] = true;
         $function_response ['meeting_id'] = $meeting_id;
        }
        catch (Exception $e) {
         $function_response ['error_msg'] = $e->getMessage();
        }
      }
      else {
       $msg = constant($response);
       if ($msg) $function_response ['error_msg'] = $msg;
       else $function_response ['error_msg'] = $response;
      }
     }
   }
  }

  return $function_response;
 }

 public function editMeeting($args) {
  $function_response = array('success' => false);
  if (is_array($args) && count($args)) {
   //print_r($args); die();
   if (isset($args['subject']) && !empty($args['subject']) &&
       isset($args['starttime']) && !empty($args['starttime']) &&
       isset($args['lesson_id']) && !empty($args['lesson_id']) &&
       isset($args['fuze_email']) && !empty($args['fuze_email']) &&
       isset($args['fuze_passwd']) && !empty($args['fuze_passwd']) &&
       isset($args['old_subject']) && !empty($args['old_subject']) &&
       isset($args['old_starttime']) && !empty($args['old_starttime']) &&
       isset($args['fuze_meeting_id']) && !empty($args['fuze_meeting_id']) &&
       isset($args['local_meeting_id']) && !empty($args['local_meeting_id']) &&
       isset($args['removed_students']) &&
       isset($args['remaining_students']) &&
       isset($args['new_students']) &&
       isset($args['students']) && !empty($args['students'])
    ) {
     $starttime = FUZE_Tools::get_local_time('UTC', $args['starttime'],'r');
     $endtime = FUZE_Tools::get_local_time('UTC', $args['starttime']+3600,'r');
     $options = array();
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_UPDATE;
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS] = array();

     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID] = $args ['fuze_meeting_id'];
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $args ['fuze_email'];
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $args ['fuze_passwd'];
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME] = $starttime;
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME] = $endtime;
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL_DEFAULT;
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE_DEFAULT;
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE_DEFAULT;
     $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT] = $args['subject'];

     $handle = RequestFactory::getRequestHandle($options);
     try {
      $response = $handle->runRequest();
     }
     catch (Exception $e) {
      $response = $e->getMessage();
     }

     if (is_array($response)) {
      $function_response ['success'] = true;
      // We have to modify local copy of meeting and send invites etc now
      // Delete old calendar entries
      $sql = "DELETE FROM `calendar` WHERE `id` IN (SELECT `calendar_id` FROM `_mod_fm_meeting` WHERE `id` = {$args ['local_meeting_id']})";
      eF_executeNew($sql);

      $calendar_id = false;
      // We add a new event in the calendar if this is requested.
      if ($args ['add_events']) {
       $bind = array();
       $bind ['data'] = _FUZE_CALENDAR_MEETING_NOTIFICATION;
       $bind ['timestamp'] = $args ['starttime'];
       $bind ['type'] = 'lesson';
       $bind ['foreign_ID'] = $args ['lesson_id'];
       $bind ['users_LOGIN'] = $this->_login;
       $table_name = '`calendar`';
       $calendar_id = eF_insertTableData($table_name, $bind);
      }

      $bind = array();
      $bind ['starttime'] = $args ['starttime'];
      $bind ['subject'] = $args ['subject'];
      $bind ['lesson_id'] = $args ['lesson_id'];
      if ($calendar_id) {
       $bind ['calendar_id'] = $calendar_id;
      }
      else {
       $bind ['calendar_id'] = NULL;
      }
      eF_updateTableData("`_mod_fm_meeting`", $bind, "`id` = {$args ['local_meeting_id']}");

      // We update the attendee table in the DB
      // Removing old attendees
      $sql = "DELETE FROM `_mod_fm_meeting_attendee` WHERE `meeting_id` = {$args ['local_meeting_id']}";
      eF_executeNew($sql);
      // Adding new attendees
      $bind = array();
      foreach ($args['students'] AS $student_id) {
       $array = array();
       $array ['meeting_id'] = $args ['local_meeting_id'];
       $array ['sys_id'] = $student_id;
       $bind [] = $array;
      }
      $table_name = '`_mod_fm_meeting_attendee`';
      eF_insertTableDataMultiple($table_name, $bind);

      // We notify attendees by email if this is requested.
      if ($args ['send_invites']) {
       // first we get all necessary info on new students that will be sent a clean email notification
       if (count($args ['new_students'])) {
        $students_transliteration = implode(',',$args ['new_students']);
        $attendees = array();
        $_sql_tables = "`_mod_fm_meeting_attendee` AS `_mfma`, `users` AS `_up`, `users` AS `_us`, `_mod_fm_user` AS `_mfu`";
        $_sql_fields = "DISTINCT(CONCAT(`_up`.`name`, ' ', `_up`.`surname`)) AS `_pname`, CONCAT(`_us`.`name`, ' ', `_us`.`surname`) AS `_sname`, `_us`.`email` AS `_semail`, `_up`.`email` AS `_pemail`";
        $_sql_conditions = "`_us`.`id` = `_mfma`.`sys_id` AND `_up`.`id` = `_mfu`.`sys_id` AND `_mfu`.`id` = {$this->_controller_id} AND `_mfma`.`sys_id` IN ({$students_transliteration})";
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

        if (count($attendees)) {
         // Retrieve the lesson_name
         $meeting_lesson_name = '';
         $res = eF_getTableData("`lessons`", "`name`", "`id` = {$args['lesson_id']}");
         if (is_array($res) && count($res)) {
          $meeting_lesson_name = $res [0]['name'];
         }
         // We notify attendees by email
         $meeting_name = $args ['subject'];
         $meeting_starttime = date('r', $args ['starttime']);
         $email_subject = str_ireplace('###MEETING_NAME###', $meeting_name, _FUZE_EMAIL_MEETING_NOTIFICATION_NEW_SUBJECT);
         $email_content = _FUZE_EMAIL_MEETING_NOTIFICATION_NEW_CONTENT;
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

       // Then we send out to the ones that are removed and no longer invited to the meeting
       if (count($args ['removed_students'])) {
        $students_transliteration = implode(',',$args ['removed_students']);
        $attendees = array();
        $_sql_tables = "`_mod_fm_meeting_attendee` AS `_mfma`, `users` AS `_up`, `users` AS `_us`, `_mod_fm_user` AS `_mfu`";
        $_sql_fields = "DISTINCT(CONCAT(`_up`.`name`, ' ', `_up`.`surname`)) AS `_pname`, CONCAT(`_us`.`name`, ' ', `_us`.`surname`) AS `_sname`, `_us`.`email` AS `_semail`, `_up`.`email` AS `_pemail`";
        $_sql_conditions = "`_us`.`id` = `_mfma`.`sys_id` AND `_up`.`id` = `_mfu`.`sys_id` AND `_mfu`.`id` = {$this->_controller_id} AND `_mfma`.`sys_id` IN ({$students_transliteration})";
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

        if (count($attendees)) {
         // Retrieve the lesson_name
         $meeting_lesson_name = '';
         $res = eF_getTableData("`lessons`", "`name`", "`id` = {$args['lesson_id']}");
         if (is_array($res) && count($res)) {
          $meeting_lesson_name = $res [0]['name'];
         }
         // We notify attendees by email
         $meeting_name = $args ['old_subject'];
         $meeting_starttime = date('r', $args ['old_starttime']);
         $email_subject = str_ireplace('###MEETING_NAME###', $meeting_name, _FUZE_EMAIL_MEETING_NOTIFICATION_MODIFIED_NOT_INVITED_SUBJECT);
         $email_content = _FUZE_EMAIL_MEETING_NOTIFICATION_MODIFIED_NOT_INVITED_CONTENT;
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

       // finally we send to those that are still invited but we want to nitify o the changes.
       if (count($args ['remaining_students'])) {
        $students_transliteration = implode(',',$args ['remaining_students']);
        $attendees = array();
        $_sql_tables = "`_mod_fm_meeting_attendee` AS `_mfma`, `users` AS `_up`, `users` AS `_us`, `_mod_fm_user` AS `_mfu`";
        $_sql_fields = "DISTINCT(CONCAT(`_up`.`name`, ' ', `_up`.`surname`)) AS `_pname`, CONCAT(`_us`.`name`, ' ', `_us`.`surname`) AS `_sname`, `_us`.`email` AS `_semail`, `_up`.`email` AS `_pemail`";
        $_sql_conditions = "`_us`.`id` = `_mfma`.`sys_id` AND `_up`.`id` = `_mfu`.`sys_id` AND `_mfu`.`id` = {$this->_controller_id} AND `_mfma`.`sys_id` IN ({$students_transliteration})";
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

        if (count($attendees)) {
         // Retrieve the lesson_name
         $meeting_lesson_name = '';
         $res = eF_getTableData("`lessons`", "`name`", "`id` = {$args['lesson_id']}");
         if (is_array($res) && count($res)) {
          $meeting_lesson_name = $res [0]['name'];
         }
         // We notify attendees by email
         $meeting_name = $args ['subject'];
         $meeting_starttime = date('r', $args ['starttime']);
         $email_subject = str_ireplace('###MEETING_NAME###', $meeting_name, _FUZE_EMAIL_MEETING_NOTIFICATION_MODIFIED_SUBJECT);
         $email_content = _FUZE_EMAIL_MEETING_NOTIFICATION_MODIFIED_CONTENT;
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
      }
     }
     else {
      $function_response ['success'] = $response;
     }
   }
  }

  return $function_response;
 }


 public function getStudentsByLessonSubset($args) {
  $student_array = array();
  $lesson_id = $args['lesson_id'];
  $order_by = $args['sort'];
  $order = $args['order'];
  $offset = $args ['offset'];
  $limit = $args ['limit'];
  $other = $args ['other'];

  $_sql_table = "`users_to_lessons` AS `utl`, `users` AS `u`";
  $_sql_fields = "`u`.`id` AS `uid`";
  $_sql_conditions = "`utl`.`lessons_ID` = $lesson_id AND `utl`.`archive` = 0 AND `utl`.`user_type` = 'student' AND `u`.`login` = `utl`.`users_LOGIN` AND `u`.`archive` = 0";
  $_sql_order = "$order_by $order";
  $_sql_group = false;
  $_sql_limit = "$offset,$limit";
  //echo $_sql_table . ' ' . $_sql_fields . ' ' . $_sql_conditions . ' ' . $_sql_order . ' ' . $_sql_group . ' ' . $_sql_limit;
  try {
   $res = eF_getTableData($_sql_table, $_sql_fields, $_sql_conditions,$_sql_order, $_sql_group, $_sql_limit);
   foreach ($res AS $entry) {
    $student_array [] = $entry['uid'];
   }
  }
  catch (Exception $e) { /* DO NOTHING */ }

  return $student_array;
 }

 public function getFutureMeetingsSubset($args) {
  $meeting_array = array();
  $order_by = $args ['sort'];
  $order = $args['order'];
  $offset = $args ['offset'];
  $limit = $args ['limit'];

  $timestamp = time() - 300; // 5 minutes ago
  $_sql_table = "`_mod_fm_meeting` AS `_mfm`";
  $_sql_fields = "`_mfm`.`id` AS `_mid`";
  $_sql_conditions = "`_mfm`.`starttime` > {$timestamp} AND `_mfm`.`user_id` = {$this->_controller_id}";
  $_sql_order = "$order_by $order";
  $_sql_group = false;
  $_sql_limit = "$offset,$limit";
  try {
   $res = eF_getTableData($_sql_table, $_sql_fields, $_sql_conditions, $_sql_order, $_sql_group, $_sql_limit);
   foreach ($res AS $entry) {
    $meeting_array [] = $entry['_mid'];
   }
  }
  catch (Exception $e) { /* DO NOTHING */ }

  return $meeting_array;
 }
}
