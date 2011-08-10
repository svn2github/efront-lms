<?php
/**

 * File for notifications

 *

 * @package eFront

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * Notification exceptions

 *

 * This class extends Exception to provide the exceptions related to notifications

 * @package eFront

 * @since 3.6.0

 *

 */
class EfrontNotificationException extends Exception
{
    /**

     * The notification requested does not exist

     * @since 3.6.0

     */
    const EVENT_NOT_EXISTS = 251;
    /**

     * The id provided is not valid, for example it is not a number or it is 0

     * @since 3.6.0

     */
    const INVALID_ID = 252;
    /**

     * An unspecific error

     * @since 3.6.0

     */
    const GENERAL_ERROR = 299;
    const INVALID_LOGIN = 300;
    const NOEVENTCODE_DEFINED = 301;
    const NORECIPIENTS_DEFINED = 302;
    const NORECIPIENTLOGIN_DEFINE = 303;
}
/**

 * This class represents a notification in eFront

 *

 * @package eFront

 * @since 3.6.0

 */
class EfrontNotification
{
 // constants representing the recipient category
 const TRIGGERINGUSER = 1; // only the user triggering the event shall receive the notification
 const ALLSYSTEMUSERS = 2; // all system users
 const ALLLESSONUSERS = 3; // all lesson users will receive the notification
 const EXPLICITLYSEL = 4; // explicitly selected
 const LESSONPROFESSORS = 5; // the professors of a lesson
 const SYSTEMADMINISTRATOR = 6; // system administrator
 const LESSONUSERSNOTCOMPLETED = 7; // all users that haven't completed the lesson
 const COURSEPROFESSORS = 8; // all course professors
 const USERSUPERVISORS = 9; // all users that supervise the branches of the user
 const ALLCOURSEUSERS = 10; // all course professors
    /**

     * The notification variable

     *

     * @since 3.6.0

     * @var array

     * @access public

     */
    public $notification = array();
    public $recipients = array();
    /**

     * Create notification instance

     *

     * This function creates the notification instance based on the

     * given notification id.

     * <br/>Example:

     * <code>

     * $notification = new EfrontNotification(5);       //create object for notification with id 5

     * </code>

     *

     * @param mixed $notification The notification id or the notification array

     * @since 3.6.0

     * @access public

     */
    function __construct($notification) {
        if (is_array($notification)) {
            $this -> notification = $notification;
        } else {
            if (!eF_checkParameter($notification, 'id')) {
                throw new EfrontNotificationException(_INVALIDID, EfrontNotificationException :: INVALID_ID);
            }
            $notification = eF_getTableData("notifications", "*", "id = $notification");
            if (sizeof($notification) == 0) {
                throw new EfrontNotificationException(_EVENTDOESNOTEXIST, EfrontNotificationException :: EVENT_NOT_EXISTS);
            }
            $this -> notification = $notification[0];
        }
    }
    /**

     * Get all system notification types

     *

     * This function returns an array with the notification types and their descriptive strings.

     * <br/>Example:

     * <code>

     * EfrontNotification::getNotificationTypes(false);

     * </code>

     *

     * @param boolean $get_module_notifications, will also return notifications registered by modules

     * @return array The current notification types

     * @since 3.6.0

     * @access public

     */
    public static function getNotificationTypes($get_module_notifications = false) {
     $system_notifications = array(EfrontNotification::SYSTEM_JOIN => array("text" => _SYSTEMJOIN, "category" => "system"),
             EfrontNotification::SYSTEM_REMOVAL => array("text" => _SYSTEM_REMOVAL, "category" => "system"),
             EfrontNotification::SYSTEM_VISITED => array("text" => _SYSTEM_REMOVAL, "category" => "system"),
             EfrontNotification::SYSTEM_REMOVAL => array("text" => _SYSTEM_REMOVAL, "category" => "system"),
             EfrontNotification::LESSON_ACQUISITION_AS_STUDENT => array("text" => _LESSON_ACQUISITION_AS_STUDENT, "category" => "lessons"),
             EfrontNotification::LESSON_ACQUISITION_AS_PROFESSOR => array("text" => _LESSON_ACQUISITION_AS_PROFESSOR, "category" => "lessons"),
             EfrontNotification::LESSON_VISITED => array("text" => _LESSON_VISITED, "category" => "lessons", "canBeNegated" => _LESSON_NOT_VISITED),
             EfrontNotification::LESSON_REMOVAL => array("text" => _LESSON_REMOVAL, "category" => "lessons"),
             EfrontNotification::LESSON_COMPLETION => array("text" => _LESSON_COMPLETION, "category" => "lessons", "canBeNegated" => _LESSON_NOT_COMPLETED),
             EfrontNotification::NEW_POST_FOR_LESSON_TIMELINE_TOPIC => array("text" => _NEW_POST_FOR_LESSON_TIMELINE_TOPIC, "category" => "lesson"),
             EfrontNotification::DELETE_POST_FROM_LESSON_TIMELINE => array("text" => _DELETE_POST_FROM_LESSON_TIMELINE, "category" => "lesson"),
             EfrontNotification::TEST_CREATION => array("text" => _TEST_CREATION, "category" => "tests"),
             EfrontNotification::TEST_START => array("text" => _TEST_START, "category" => "tests"),
             EfrontNotification::TEST_COMPLETION => array("text" => _TEST_COMPLETION, "category" => "tests", "canBeNegated" => _TEST_NOT_COMPLETED),
             EfrontNotification::CONTENT_CREATION => array("text" => _CONTENT_CREATION, "category" => "content"),
             EfrontNotification::CONTENT_MODIFICATION => array("text" => _CONTENT_MODIFICATION, "category" => "content"),
             EfrontNotification::CONTENT_START => array("text" => _CONTENT_START, "category" => "content"),
             EfrontNotification::CONTENT_COMPLETION => array("text" => _CONTENT_COMPLETION, "category" => "content", "canBeNegated" => _CONTENT_NOT_COMPLETED),
             EfrontNotification::NEW_COMMENT_WRITING => array("text" => _NEW_COMMENT_WRITING, "category" => "content"),
             EfrontNotification::NEW_FORUM => array("text" => _NEW_FORUM, "category" => "forum"),
             EfrontNotification::NEW_TOPIC => array("text" => _NEW_TOPIC, "category" => "forum"),
             EfrontNotification::NEW_POLL => array("text" => _NEW_POLL, "category" => "forum"),
             EfrontNotification::NEW_FORUM_MESSAGE_POST => array("text" => _NEW_FORUM_MESSAGE_POST, "category" => "forum"),
             EfrontNotification::STATUS_CHANGE => array("text" => _STATUS_CHANGE, "category" => "personal"),
             EfrontNotification::AVATAR_CHANGE => array("text" => _AVATAR_CHANGE, "category" => "personal"),
             EfrontNotification::PROFILE_CHANGE => array("text" => _PROFILE_CHANGE , "category" => "personal"),
             EfrontNotification::NEW_PROFILE_COMMENT_FOR_OTHER => array("text" => _NEW_PROFILE_COMMENT_FOR_OTHER, "category" => "personal"),
             EfrontNotification::NEW_PROFILE_COMMENT_FOR_SELF => array("text" => _NEW_PROFILE_COMMENT_FOR_SELF, "category" => "personal"),
             EfrontNotification::DELETE_PROFILE_COMMENT_FOR_SELF => array("text" => _DELETE_PROFILE_COMMENT_FOR_SELF, "category" => "personal"));
     return $system_notifications;
    }
 public static function addDefaultNotifications() {
     // Since 3.6.0 - Add predefined events - maybe set isDefault as metadata
     $predefined_events = array(EfrontEvent::SYSTEM_FORGOTTEN_PASSWORD,
              EfrontEvent::SYSTEM_NEW_PASSWORD_REQUEST,
              EfrontEvent::SYSTEM_ON_EMAIL_ACTIVATION,
              EfrontEvent::SYSTEM_REGISTER,
              EfrontEvent::NEW_SURVEY,
              EfrontEvent::NEW_SYSTEM_ANNOUNCEMENT,
              EfrontEvent::NEW_LESSON_ANNOUNCEMENT,
              (-1) * EfrontEvent::SYSTEM_VISITED,
              EfrontEvent::SYSTEM_JOIN,
              EfrontEvent::TEST_COMPLETION,
              EfrontEvent::PROJECT_SUBMISSION,
              (-1) * EfrontEvent::PROJECT_EXPIRY,
              (-1) * EfrontEvent::LESSON_PROGRAMMED_EXPIRY,
              EfrontEvent::NEW_TOPIC,
              EfrontEvent::NEW_FORUM_MESSAGE_POST,
              EfrontEvent::CONTENT_MODIFICATION);
     $registered_events = eF_getTableDataFlat("event_notifications", "event_type", "event_type IN ('". implode("','", $predefined_events) ."')");
  $registered_events = $registered_events['event_type'];
  if (!in_array(EfrontEvent::SYSTEM_FORGOTTEN_PASSWORD, $registered_events)) {
   $default_notification = array("event_type" => EfrontEvent::SYSTEM_FORGOTTEN_PASSWORD,
               "send_conditions" => serialize(array()),
               "send_immediately"=> 1,
            "subject" => _PASSWORDRECOVERY,
            "message" => _DEARUSER." ###users_name###,<br><br>".
                           _THISISANAUTOMATEDEMAILSENTFROM." ###host_name### "._BECAUSEYOUASKEDTORECOVERPASSWORD." "._PLEASECLICKTHECONFIRMATIONLINKBELOW.".<br><br>"
                           .'###host_name###/index.php?ctg=reset_pwd&login=###users_login###&id=###md5(###users_login###)###<br><br>'
                           ._ALTERNATIVELYCOPYANDPASTEBROWSER.".<br>"._CLIKCINGONTHELINKWILLCONFIRM." <br>"._FORFURTHERCONTACTADMINAT.' ###host_name###/index.php?ctg=contact <br><br>'._KINDREGARDSEFRONT."<br>---<br>"._ADMINISTRATIONGROUP."<br>###site_name###<br>###site_motto###<br>"
                           ._AUTOMATEDEMAILSENTFROM." ###host_name### "._ON." ###date###<br><br>");
   eF_insertTableData("event_notifications", $default_notification);
  }
  if (!in_array(EfrontEvent::SYSTEM_NEW_PASSWORD_REQUEST, $registered_events)) {
   $default_notification = array("event_type" => EfrontEvent::SYSTEM_NEW_PASSWORD_REQUEST,
               "send_conditions" => serialize(array()),
               "send_immediately"=> 1,
            "subject" => _PASSWORDRECOVERY,
            "message" => _DEARUSER." ###users_name###,<br><br>"._THISISANAUTOMATEDEMAILSENTFROM." ###host_name### "._WITHTHENEWPASSWORD." <br>"._THENEWPASSWORDIS."<br><br>###new_password###<br>
                              <br>"._FORFURTHERCONTACTADMINAT." ###host_name###/index.php?ctg=contact <br><br>"._KINDREGARDSEFRONT."<br>---<br>"._ADMINISTRATIONGROUP."<br>###site_name###<br>###site_motto###<br>"
                              ._AUTOMATEDEMAILSENTFROM." ###host_name### "._ON." ###date###");
   eF_insertTableData("event_notifications", $default_notification);
  }
  if (!in_array(EfrontEvent::SYSTEM_ON_EMAIL_ACTIVATION, $registered_events)) {
   $default_notification = array("event_type" => EfrontEvent::SYSTEM_ON_EMAIL_ACTIVATION,
               "send_conditions" => serialize(array()),
               "send_immediately"=> 1,
            "subject" => _ACCOUNTACTIVATIONMAILSUBJECT,
            "message" => _DEARUSER." ###users_name###,<br><br>"._WELCOMETOOUR.' '._ELEARNINGPLATFORM.".! <br>"._ACCOUNTACTIVATIONMAILBODY."<br>###host_name###/index.php?account=###users_login###&key=###timestamp###<br><br><br>".
                           _AUTOMATEDEMAILSENTFROM.' ###host_name### '._ON.' ###date###<br>'.
                           _FORFURTHERCONTACTADMINAT." ###host_name###/index.php?ctg=contact <br><br>"._KINDREGARDSEFRONT."<br>---<br>"._ADMINISTRATIONGROUP."<br>###site_name###<br>###site_motto###<br>");
   eF_insertTableData("event_notifications", $default_notification);
  }
  if (!in_array(EfrontEvent::SYSTEM_REGISTER, $registered_events)) {
      $message = _DEARUSER.' ###users_name###,<br><br>'
                  ._WELCOMETOOUR.' '._ELEARNINGPLATFORM.". <br>"._ACCOUNTACTIVATEDWITHPERSONALINFORMATION."<br><br>".
                 _LOGIN .': ###users_login###<br>'.
                 _FIRSTNAME .': ###users_name###<br>'.
                 _LASTNAME .': ###users_surname###<br>'.
                 _EMAILADDRESS.': ###users_email###<br>'.
                 _LANGUAGE.': ###users_language###<br>'.
                 _COMMENTS.': ###users_comments###<br><br>';
   // we cannot really check about the other two cases...
         if ($GLOBALS['configuration']['mail_activation']) {
             $message .= _YOUMAYLOGINMAILACTIVATION . "<br><br>";
            }
            $message .= _FORFURTHERCONTACTADMINAT.' ###host_name###/index.php?ctg=contact <br><br>'._KINDREGARDSEFRONT."<br>---<br>"._ADMINISTRATIONGROUP."<br>###site_name###<br>###site_motto###<br>";
   $default_notification = array("event_type" => EfrontEvent::SYSTEM_REGISTER,
               "send_conditions" => serialize(array()),
               "send_immediately"=> 1,
            "subject" => _REGISTRATIONEMAIL,
            "message" => $message);
   eF_insertTableData("event_notifications", $default_notification);
  }
/*

h) Enhmerwsh ana X meres gia shmantika gegonota sto eFront (auto prepei na to syzhthsoume)

*/
 }
    /**

     * Get notification lessons

     *

     * This function gets a list with the notification lessons. If a specific order

     * is set, the lessons are ordered based on it

     * <br/>Example:

     * <code>

     * $notification -> getLessons();

     * </code>

     *

     * @param boolean $returnObjects Whether to return EfrontLesson objects

     * @return array The notification lessons

     * @since 3.6.0

     * @access public

     */
    public function getLessons($returnObjects = false) {
        if ($this -> lessons == false) {
            $result = eF_getTableData("lessons_to_notifications lc, lessons l", "lc.previous_lessons_ID, l.*", "l.id=lc.lessons_ID and notifications_ID=".$this -> notification['id']);
            if (sizeof($result) > 0) {
                $previous = 0; //Previous is only used when no previos_lessons_ID is set
                foreach ($result as $value) {
                    $notificationLessons[$value['id']] = $value;
                    $value['previous_lessons_ID'] !== false ? $previousLessons[$value['previous_lessons_ID']] = $value : $previousLessons[$previous] = $value;
                    $previous = $value['id'];
                }
                //Sorting algorithm, based on previous_lessons_ID. The algorithm is copied from EfrontContentTree :: reset() and is the same with the one applied for content. It is also used in questions order
                $node = 0;
                $count = 0;
                $nodes = array(); //$count is used to prnotification infinite loops
                while (sizeof($previousLessons) > 0 && isset($previousLessons[$node]) && $count++ < 1000) {
                    $nodes[$previousLessons[$node]['id']] = $previousLessons[$node];
                    $newNode = $previousLessons[$node]['id'];
                    unset($previousLessons[$node]);
                    $node = $newNode;
                }
                $this -> lessons = $nodes;
                if (sizeof($nodes) != sizeof($result)) { //If the ordering is messed up for some reason
                    $this -> lessons = $notificationLessons;
                    eF_updateTableData("lessons_to_notifications", array("previous_lessons_ID" => NULL), "notifications_ID=".$this -> notification['id']);
                }
            } else {
                $this -> lessons = array();
            }
        }
        if ($returnObjects) {
            foreach ($this -> lessons as $key => $lesson) {
                $lessons[$key] = new EfrontLesson($lesson['id']);
            }
            return $lessons;
        } else {
            return $this -> lessons;
        }
    }
    /**

     * Create new notification

     *

     * Create a new notification based on the specified fields

     * <br/>Example:

     * <code>

     * EfrontNotification :: addNotification("1241253445", "Dear user,<br><br>Hi", array("lessons_ID" => 2), 259200);

     * </code>

     *

     * @param timestamp: the time when this message will be sent

     * @param message: the templated message for this message. the values for the template are extracted from the $condition

     * @param condition: the conditions that the recipients must fulfill to be sent the message. if null, the message will be sent to everyone

     * 					 the conditions are in an array form "field" => "value"

     * @param send_interval (optional): if defined then the message will be sent periodically every $send_interval seconds

     * @return boolean true if everything went alright else false

     * @since 3.6.0

     * @access public

     */
    public static function addNotification($timestamp, $subject, $message, $condition, $html_message, $send_interval = false) {
     $notification = array ("timestamp" => $timestamp,
             "send_conditions" => serialize($condition),
             "message" => $message,
             "subject" => $subject,
             "html_message" => $html_message);
  if ($send_interval) {
      $notification['send_interval'] = $send_interval;
     }
     return eF_insertTableData("notifications", $notification);
    }
    /**

     * Edit existing notification

     *

     * Edit existing notification based on the specified fields

     * <br/>Example:

     * <code>

     * EfrontNotification :: editNotification(2, "1241253445", "Dear user,<br><br>Hi", array("lessons_ID" => 2), 259200);

     * </code>

     *

     * @param timestamp: the time when this message will be sent

     * @param message: the templated message for this message. the values for the template are extracted from the $condition

     * @param condition: the conditions that the recipients must fulfill to be sent the message. if null, the message will be sent to everyone

     * 					 the conditions are in an array form "field" => "value"

     * @param send_interval (optional): if defined then the message will be sent periodically every $send_interval seconds

     * @return boolean true if everything went alright else false

     * @since 3.6.0

     * @access public

     */
    public static function editNotification($id, $timestamp, $subject, $message, $condition, $html_message, $send_interval = false) {
     $notification = array ("timestamp" => $timestamp,
             "send_conditions" => serialize($condition),
             "message" => $message,
             "subject" => $subject,
             "html_message" => $html_message);
  if ($send_interval) {
      $notification['send_interval'] = $send_interval;
     }
     return eF_updateTableData("notifications", $notification, "id = " . $id);
    }
    /**

     * Initialize the notifications for the ones sent prior or after some time to an event, i.e.

     * Find all users that are related to this notification, see when they should have triggered this

     * notification (when this notification was not declared) and see to it that they get their message

     * when they should

     *

     * We should create the users that should be sent the newly created/edited notification according to

     * - the current time (time())

     * - the "after time" of the notification (1 - 60 days)

     * - the conditions set for this event

     * Note that all $users_to_notify results should have a SPECIFIC FORM which is:

     * users_LOGIN, users_name, users_surname, timestamp, [lessons_ID, lessons_name, entity_ID, entity_name]

     * which together with the 'type' field of the event will be passed as arguments to the appendNewNotification

     * which works with these arguments

     *

     * <br/>Example:

     * <code>

     * EfrontNotification :: initializeEventNotification($fields);

     * </code>

     *

     * @param $fields: the descripting fields of the event notification

     * @since 3.6.0

     * @access public

     */
    public static function initializeEventNotification($event_notification) {
        $event_types = EfrontEvent::getEventTypes();
        // The same regardless whether $event_notification['after_time'] is positive (After Event)
        // or negative (Before Event): we will compare timestamps with past or future timestamps
        // respectively and send now only the notifications that make (have not expired)
        $timediff = time() - $event_notification['after_time'];
        if (EfrontEvent::SYSTEM_JOIN == $event_notification['event_type']) {
            $users_to_notify = eF_getTableData("users", "login as users_LOGIN, name as users_name, surname as users_surname, timestamp", "timestamp > " . $timediff);
        } else if (EfrontEvent::SYSTEM_VISITED == abs($event_notification['event_type'])) {
            $users_result = eF_getTableData("logs JOIN users ON logs.users_LOGIN = users.login", "distinct users.login as users_LOGIN, users.name as users_name, users.surname as users_surname, logs.timestamp", "action = 'login' AND logs.timestamp > " . $timediff, "users.login ASC, logs.timestamp DESC");
            // Removing duplicates to keep only last record of each user - since the list is sorted this will work
            $previous_user = "";
            $users_to_notify = array();
            $users_having_entered = array();
            foreach ($users_result as $key => $user) {
                if ($user['users_LOGIN'] != $previous_user) {
                    $users_to_notify[] = $user;
                    $previous_user = $user['users_LOGIN'];
                    $users_having_entered[] = $user['users_LOGIN'];
                }
            }
            $users_never_entered = eF_getTableData("users", "users.login as users_LOGIN, users.name as users_name, users.surname as users_surname, users.timestamp", "login NOT IN ('".implode("','", $users_having_entered) ."') AND timestamp > " . $timediff);
            foreach ($users_never_entered as $key => $user) {
                $users_to_notify[] = $user;
            }
        } else if (EfrontEvent::LESSON_ACQUISITION_AS_STUDENT == $event_notification['event_type'] ||
                   EfrontEvent::LESSON_ACQUISITION_AS_PROFESSOR == $event_notification['event_type'] ||
                   EfrontEvent::LESSON_COMPLETION == abs($event_notification['event_type']) || // for the corresponding AFTER NOT event
                   EfrontEvent::LESSON_PROGRAMMED_START == abs($event_notification['event_type']) || // for the corresponding BEFORE event
                   EfrontEvent::LESSON_PROGRAMMED_EXPIRY == abs($event_notification['event_type']) ) { // for the corresponding BEFORE event
            $conditions = unserialize($event_notification['send_conditions']);
            $extra_condition = "";
            if (EfrontEvent::LESSON_ACQUISITION_AS_STUDENT == $event_notification['event_type']) {
                $extra_condition = "users_to_lessons.user_type = 'student' AND ";
    $timestamp_column = "users_to_lessons.from_timestamp";
            } else if (EfrontEvent::LESSON_ACQUISITION_AS_PROFESSOR == $event_notification['event_type']) {
                $extra_condition = "users_to_lessons.user_type = 'professor' AND ";
                $timestamp_column = "users_to_lessons.from_timestamp";
            } else if (EfrontEvent::LESSON_COMPLETION == $event_notification['event_type']) {
                $extra_condition = "users_to_lessons.completed = '1' AND ";
                $timestamp_column = "users_to_lessons.to_timestamp";
            } else if (EfrontEvent::LESSON_COMPLETION == (-1) * $event_notification['event_type']) {
                $extra_condition = "users_to_lessons.completed = '0' AND ";
                $timestamp_column = "users_to_lessons.to_timestamp";
            } else if (EfrontEvent::LESSON_PROGRAMMED_START == abs($event_notification['event_type'])) {
                $timestamp_column = "lessons.from_timestamp";
            } else if (EfrontEvent::LESSON_PROGRAMMED_EXPIRY == abs($event_notification['event_type'])) {
                $timestamp_column = "lessons.to_timestamp";
            }
            if ($conditions['lessons_ID'] != 0) {
                $extra_condition .= " lessons.id = " . $conditions['lessons_ID'] . " AND ";
            }
            if (EfrontEvent::LESSON_PROGRAMMED_START != abs($event_notification['event_type']) && EfrontEvent::LESSON_PROGRAMMED_EXPIRY != abs($event_notification['event_type'])) {
    $users_to_notify = eF_getTableData("users_to_lessons JOIN users ON users_to_lessons.users_LOGIN = users.login JOIN lessons ON users_to_lessons.lessons_ID = lessons.id", "users.login as users_LOGIN, users.name as users_name, users.surname as users_surname, users_to_lessons.lessons_ID, lessons.name as lessons_name, " . $timestamp_column . " as timestamp", $extra_condition . $timestamp_column . "> " . $timediff." and users.archive=0 and users_to_lessons.archive=0");
            } else {
                $users_to_notify = eF_getTableData("lessons", "lessons.id as lessons_ID, lessons.name as lessons_name, " . $timestamp_column . " as timestamp", $extra_condition . $timestamp_column . "> " . $timediff);
            }
        } else if (EfrontEvent::LESSON_VISITED == abs($event_notification['event_type'])) {
            $conditions = unserialize($event_notification['send_conditions']);
            if ($conditions['lessons_ID'] != 0) {
                $extra_condition .= " logs.lessons_ID = " . $conditions['lessons_ID'] . " AND ";
            }
            $users_result = eF_getTableData("logs JOIN users ON logs.users_LOGIN = users.login JOIN lessons ON lessons.id = logs.lessons_ID", "distinct users.login as users_LOGIN, users.name as users_name, users.surname as users_surname, logs.timestamp, lessons.id as lessons_ID, lessons.name as lessons_name", $extra_condition . " action = 'lesson' AND logs.timestamp > " . $timediff, "users.login ASC, logs.timestamp DESC");
            // Removing duplicates to keep only last record of each user - since the list is sorted this will work
            $previous_user = "";
            $users_to_notify = array();
            foreach ($users_result as $key => $user) {
                if ($user['users_LOGIN'] != $previous_user) {
                    $users_to_notify[] = $user;
                    $previous_user = $user['users_LOGIN'];
                }
            }
        } else if (EfrontEvent::PROJECT_SUBMISSION == $event_notification['event_type']) {
            $conditions = unserialize($event_notification['send_conditions']);
            if ($conditions['lessons_ID'] != 0) {
                $extra_condition .= " projects.lessons_ID = " . $conditions['lessons_ID'] . " AND ";
            }
            $timestamp_column = "users_to_projects.upload_timestamp";
            $users_to_notify = eF_getTableData("users_to_projects JOIN users ON users_to_projects.users_LOGIN = users.login JOIN projects ON users_to_projects.projects_ID = projects.id JOIN lessons ON lessons.id = projects.lessons_ID", "users.login as users_LOGIN, users.name as users_name, users.surname as users_surname, projects.lessons_ID, lessons.name as lessons_name, ". $timestamp_column ." as timestamp, projects.id as entity_ID, projects.title as entity_name", $extra_condition . $timestamp_column . "> " . $timediff);
        } else if (EfrontEvent::PROJECT_EXPIRY == abs($event_notification['event_type'])) {
            $timestamp_column = "projects.deadline";
            if ($conditions['lessons_ID'] != 0) {
                $extra_condition .= " projects.lessons_ID = " . $conditions['lessons_ID'] . " AND ";
            }
            $users_to_notify = eF_getTableData("projects JOIN lessons ON lessons.id = projects.lessons_ID", "projects.lessons_ID, lessons.name as lessons_name, ". $timestamp_column ." as timestamp, projects.id as entity_ID, projects.title as entity_name", $extra_condition . $timestamp_column . "> " . $timediff);
        } else if (EfrontEvent::COURSE_ACQUISITION_AS_STUDENT == $event_notification['event_type'] ||
                   EfrontEvent::COURSE_ACQUISITION_AS_PROFESSOR == $event_notification['event_type'] ||
                   EfrontEvent::COURSE_COMPLETION == abs($event_notification['event_type']) ||
                   EfrontEvent::COURSE_PROGRAMMED_START == abs($event_notification['event_type']) || // for the corresponding BEFORE event
                   EfrontEvent::COURSE_PROGRAMMED_EXPIRY == abs($event_notification['event_type'])) { // for the corresponding BEFORE event
            $conditions = unserialize($event_notification['send_conditions']);
            $extra_condition = "";
            if (EfrontEvent::COURSE_ACQUISITION_AS_STUDENT == $event_notification['event_type']) {
                $extra_condition = "users_to_courses.user_type = 'student' AND ";
    $timestamp_column = "users_to_courses.from_timestamp";
            } else if (EfrontEvent::COURSE_ACQUISITION_AS_PROFESSOR == $event_notification['event_type']) {
                $extra_condition = "users_to_courses.user_type = 'professor' AND ";
                $timestamp_column = "users_to_courses.from_timestamp";
            } else if (EfrontEvent::COURSE_COMPLETION == $event_notification['event_type'] || EfrontEvent::COURSE_CERTIFICATE_ISSUE == $event_notification['event_type']) {
                $extra_condition = "users_to_courses.completed = '1' AND ";
                $timestamp_column = "users_to_courses.to_timestamp";
            } else if (EfrontEvent::COURSE_COMPLETION == (-1) * $event_notification['event_type']) {
                $extra_condition = "users_to_courses.completed = '0' AND ";
                $timestamp_column = "users_to_courses.from_timestamp";
            } else if (EfrontEvent::COURSE_PROGRAMMED_START == abs($event_notification['event_type'])) {
                $timestamp_column = "courses.start_date";
            } else if (EfrontEvent::COURSE_PROGRAMMED_EXPIRY == abs($event_notification['event_type'])) {
                $timestamp_column = "courses.end_date";
            }
            if ($conditions['courses_ID'] != 0) {
                $extra_condition .= " courses.id = " . $conditions['courses_ID'] . " AND ";
            }
            if (EfrontEvent::COURSE_PROGRAMMED_START == abs($event_notification['event_type']) || EfrontEvent::COURSE_PROGRAMMED_EXPIRY == abs($event_notification['event_type'])) {
             $users_to_notify = eF_getTableData("courses", "courses.id as lessons_ID, courses.name as lessons_name, " . $timestamp_column . " as timestamp", $extra_condition . $timestamp_column . "> " . $timediff);
            } else {
                $users_to_notify = eF_getTableData("users_to_courses JOIN users ON users_to_courses.users_LOGIN = users.login JOIN courses ON users_to_courses.courses_ID = courses.id", "users.login as users_LOGIN, users.name as users_name, users.surname as users_surname, users_to_courses.courses_ID, courses.name as courses_name, " . $timestamp_column . " as timestamp", $extra_condition . $timestamp_column . "> " . $timediff." and users.archive=0 and users_to_courses.archive=0");
            }
  } else if (EfrontEvent::COURSE_CERTIFICATE_ISSUE == $event_notification['event_type']) {
   $users_result = eF_getTableData("users_to_courses JOIN users ON users_to_courses.users_LOGIN = users.login JOIN courses ON users_to_courses.courses_ID = courses.id", "users.login as users_LOGIN, users.name as users_name, users.surname as users_surname, users_to_courses.courses_ID, courses.name as courses_name, users_to_courses.issued_certificate", "users_to_courses.completed = '1' AND users_to_courses.issued_certificate <> '' and users.archive=0 and users_to_courses.archive=0");
      $users_to_notify = array();
            foreach ($users_result as $key => $user) {
             $certificate = unserialize($user['issued_certificate']);
             if ($certificate['date'] > $timediff) {
              $user['timestamp'] = $certificate['date'];
              $users_to_notify[] = $user;
             }
            }
  } else if (EfrontEvent::COURSE_CERTIFICATE_EXPIRY == $event_notification['event_type']) {
   $users_result = eF_getTableData("users_to_courses JOIN users ON users_to_courses.users_LOGIN = users.login JOIN courses ON users_to_courses.courses_ID = courses.id", "users.login as users_LOGIN, users.name as users_name, users.surname as users_surname, users_to_courses.courses_ID, courses.name as courses_name, users_to_courses.issued_certificate", "users_to_courses.completed = '1' AND users_to_courses.issued_certificate <> '' and users.archive=0 and users_to_courses.archive=0");
      $users_to_notify = array();
            foreach ($users_result as $key => $user) {
             $certificate = unserialize($user['issued_certificate']);
             if ($certificate['date'] > $timediff) {
              $user['timestamp'] = $certificate['date'];
              $users_to_notify[] = $user;
             }
            }
  } else if (EfrontEvent::COURSE_VISITED == abs($event_notification['event_type'])) {
            $conditions = unserialize($event_notification['send_conditions']);
            if ($conditions['courses_ID'] != 0) {
                $extra_condition .= " logs.courses_ID = " . $conditions['courses_ID'] . " AND ";
            }
            $users_result = eF_getTableData("logs JOIN users ON logs.users_LOGIN = users.login JOIN courses ON courses.id = logs.courses_ID", "distinct users.login as users_LOGIN, users.name as users_name, users.surname as users_surname, logs.timestamp, courses.id as courses_ID, courses.name as courses_name", $extra_condition . " action = 'course' AND logs.timestamp > " . $timediff, "users.login ASC, logs.timestamp DESC");
            // Removing duplicates to keep only last record of each user - since the list is sorted this will work
            $previous_user = "";
            $users_to_notify = array();
            foreach ($users_result as $key => $user) {
                if ($user['users_LOGIN'] != $previous_user) {
                    $users_to_notify[] = $user;
                    $previous_user = $user['users_LOGIN'];
                }
            }
        } else if (EfrontEvent::NEW_SURVEY == $event_notification['event_type']) {
            $conditions = unserialize($event_notification['send_conditions']);
            if ($conditions['lessons_ID'] != 0) {
                $extra_condition .= " projects.lessons_ID = " . $conditions['lessons_ID'] . " AND ";
            }
            $timestamp_column = "surveys.start_date";
            $users_to_notify = eF_getTableData("surveys JOIN users ON surveys.author = users.login JOIN lessons ON lessons.id = surveys.lessons_ID", "users.login as users_LOGIN, users.name as users_name, users.surname as users_surname, surveys.lessons_ID, lessons.name as lessons_name, surveys.id as entity_ID, surveys.name as entity_name, ". $timestamp_column ." as timestamp", $extra_condition . $timestamp_column . "> " . $timediff);
        }
        global $currentUser;
        if (sizeof($users_to_notify) > 0) {
            foreach ($users_to_notify as $user_event_fields) {
                if (!isset($user_event_fields['users_LOGIN'])) {
                    $user_event_fields['users_LOGIN'] = $currentUser -> user['login'];
                    $user_event_fields['users_name'] = $currentUser -> user['name'];
                    $user_event_fields['users_surname'] = $currentUser -> user['surname'];
                }
                $user_event_fields['type'] = $event_notification['event_type'];
                $user_event_fields['send_interval'] = $event_notification['after_time'];
                $event = new EfrontEvent($user_event_fields); // this should create an event instance for our class
          $event -> appendNewNotification($event_types, true, false); // append this notification to the email queue
            }
        } else {
            eF_deleteTableData("notifications", "id_type_entity LIKE '".$event_notification['id']."_%'");
        }
    }
    /**

     * Create new event notification

     *

     * Create a new notification based on the specified fields to be triggered on a particular system event

     * <br/>Example:

     * <code>

     * EfrontNotification :: addEventNotification(EfrontEvent::LESSON_ACQUISITION_AS_STUDENT, "Dear user,<br><br>Welcome to our lesson Greedy algorithms", array("lessons_ID" => 1));

     * </code>

     *

     * @param type: the event_type of this event. should be one of the EfrontEvent or custom module defined events

     * @param message: the templated message for this message. the values for the template are extracted from the condition

     * @param condition: the conditions that the recipients must fulfill to be sent the message. if null, the message will be sent to everyone

     * 					 the conditions are in an array form "field" => "value"

     * @param recip_cat: the recipients category (defined as constants in this class)

     * @param after_time (optional): time to send after the event has been triggered

     * @return boolean true if everything went alright else false

     * @since 3.6.0

     * @access public

     */
    public static function addEventNotification($events_type, $subject, $message, $condition, $recipients_category, $html_message, $after_time = false, $send_immediately = false) {
    }
    /**

     * Edit an existing event notification

     *

     * <br/>Example:

     * <code>

     * EfrontNotification :: editEventNotification(2, EfrontEvent::LESSON_ACQUISITION_AS_STUDENT, "Dear user,<br><br>Welcome to our lesson Greedy algorithms", array("lessons_ID" => 1));

     * </code>

     *

     * @param id: the id of the edited event notification

     * @param type: the event_type of this event. should be one of the EfrontEvent or custom module defined events

     * @param subject: the templated subject of the message

     * @param message: the templated message for this message. the values for the template are extracted from the condition

     * @param condition: the conditions that the recipients must fulfill to be sent the message. if null, the message will be sent to everyone

     * 					 the conditions are in an array form "field" => "value"

     * @param after_time (optional): time to send after the event has been triggered

     * @return boolean true if everything went alright else false

     * @since 3.6.0

     * @access public

     */
    public static function editEventNotification($event_id, $event_type, $subject, $message, $condition, $recipients_category, $html_message, $after_time = false, $send_immediately = false) {
      $notification = array ("message" => $message,
              "subject" => $subject,
              "html_message" => ($html_message) ? 1: 0);
  $result = eF_updateTableData("event_notifications", $notification, "id = '" . $event_id . "'");
     if ($result && $after_time) {
   // add notifications for the existing users - the event notification entry must first be inserted before initializing existing users
   EfrontNotification::initializeEventNotification($notification);
  }
    }
    /*

     * Function used to the most recent sent notification messages

     *

     * @param: the maximum number of messages to be returned

     * @return: the X at most recent messages from the sent_notifications table ordered by time of sending

     * @since: 3.6.0

     */
    public static function getRecentlySent($limit = false) {
     if ($limit) {
      return eF_getTableData("sent_notifications", "*" , "1=1 limit $limit", "timestamp DESC");
     } else {
   return eF_getTableData("sent_notifications", "*", "", "timestamp DESC");
     }
    }
    /*

     * Function used to return the notification's recipients

     *

     * <br/>Example:

     * <code>

     * $n = new EfrontNotification(12);

     * $recipients = $n -> getRecipients();

     * foreach ($recipients as $recipient) {

     * 		// send personal msg

     * }

     * </code>

     *

     * @returns array of eFront logins, names, surnames, emails, user_types regarding the notification's recipients

     */
    public function getRecipients() {
     $recipients_list = array();
     if (isset($this -> notification['send_conditions'])) {
    //echo $this -> notification['send_conditions'];
      if ($this -> notification['send_conditions'] == "N;") {
       $recipients = eF_getTableData("users", "*", "active=1 and archive=0");
    //sending_queue_msgs[$key]['recipients'] = _ALLUSERS;
       foreach ($recipients as $recipient) {
        $recipients_list[$recipient['login']] = $recipient;
       }
   } else {
    // the send_conditions field contains the information which identify the recipients
    // it is defined in ....
    //digests.php during the definition of the event notification
    $this -> notification['send_conditions'] = unserialize($this -> notification['send_conditions']);
       if (is_array($this -> notification['send_conditions'])) {
              $this -> recipients = $this -> notification['send_conditions'];
        // The recipients array definitely exists, due to constructor checks
        if (isset($this -> recipients["lessons_ID"]) && $this -> recipients["lessons_ID"]) {
         $lesson = new EfrontLesson($this -> recipients["lessons_ID"]);
         if (isset($this -> recipients["user_type"])) {
          // return lesson users of specific type
          $recipients = array();
          foreach ($lesson -> getUsers($this -> recipients["user_type"]) as $value) {
           if ($value['active']) {
            $recipients[] = $value;
           }
          }
         } else if (isset($this -> recipients["completed"])) {
             // return lesson students according to whether they have completed the lesson or not
          $recipients = array();
          foreach ($lesson -> getUsersCompleted($this -> recipients["completed"]) as $value) {
           if ($value['active']) {
            $recipients[] = $value;
           }
          }
         } else {
          // return all users
          $recipients = array();
          foreach ($lesson -> getUsers() as $value) {
           if ($value['active']) {
            $recipients[] = $value;
           }
          }
         }
        } else if (isset($this -> recipients["courses_ID"])) {
         if ($this -> recipients['user_type'] == "professor") {
       $completed_condition = " AND uc.user_type = 'professor'";
         } else if ($this -> recipients['completed'] == "1") {
          $completed_condition = " AND completed = '1'";
         } else {
          $completed_condition = "";
         }
         $recipients = eF_getTableData("users_to_courses uc, users u", "u.login, u.name, u.surname, u.email, u.user_type as basic_user_type, u.active, u.user_types_ID, uc.user_type as role", "u.active=1 and u.archive=0 and uc.archive=0 and uc.users_LOGIN = u.login and uc.courses_ID=". $this -> recipients["courses_ID"] . $completed_condition);
        } else if (isset($this -> recipients['user_type'])) {
         $recipients = eF_getTableData("users", "*", "active=1 and archive=0 and user_type = '". $this -> recipients['user_type']."'");
        } else if (isset($this -> recipients['entity_ID']) && isset($this -> recipients['entity_category'])) {
         if ($this -> recipients['entity_category'] == "survey") {
          $recipients = eF_getTableData("users_to_surveys JOIN users ON users_LOGIN = users.login", "users.*", "users.active=1 and users.archive=0 and surveys_ID = '".$this -> recipients["entity_ID"]."'");
       $resDone = eF_getTableDataFlat("users_to_done_surveys", "users_LOGIN", "surveys_ID=".$this -> recipients["entity_ID"]);
          $usersToSent = array();
          if (!empty($resDone['users_LOGIN'])){
        foreach ($recipients as $key => $value) {
         if (!in_array($value['login'], $resDone['users_LOGIN'])){
          $usersToSent[] = $value;
         }
        }
        $recipients = $usersToSent;
          }
         } else if ($this -> recipients['entity_category'] == "projects") {
       $recipients = eF_getTableData("users_to_projects JOIN users ON users_LOGIN = users.login", "users.*", "users.active=1 and users.archive=0 and projects_ID = '".$this -> recipients["entity_ID"]."'");
         }
        } else if (isset($this -> recipients["groups_ID"])) {
         $recipients = eF_getTableData("users_to_groups JOIN users ON users_login = users.login", "users.*", "users.active=1 and users.archive=0 and groups_ID = '".$this -> recipients["groups_ID"]."'");
        } else if (isset($this -> recipients['users_login'])) {
          $recipients = $this -> recipients['users_login'];
        }
        foreach ($recipients as $recipient) {
         $recipients_list[$recipient['login']] = $recipient;
        }
       } else {
        if ($this -> notification['recipient'] != "") {
         preg_match("/\d+_(\d+)/", $this -> notification['id_type_entity'], $matches);
         if ($matches[1] == EfrontEvent::SYSTEM_ON_EMAIL_ACTIVATION) { //In this case, we want an inactive user to receive the email
          $user = eF_getTableData("users", "*", "archive=0 and login = '".$this -> notification['recipient']."'");
         } else {
          $user = eF_getTableData("users", "*", "active=1 and archive=0 and login = '".$this -> notification['recipient']."'");
         }
         if (!empty($user)) {
          $recipients_list[$this -> notification['recipient']] = $user[0];
         }
        }
       }
   }
        } else {
      if ($this -> notification['recipient'] != "") {
       $user = eF_getTableData("users", "*", "active=1 and archive=0 and login = '".$this -> notification['recipient']."'");
       if (!empty($user)) {
        $recipients_list[$this -> notification['recipient']] = $user[0];
       }
      }
    }
     return $recipients_list;
    }
    /*

     * Function that sends this notification as an email to a user

     *

     * The notification's message is supposed to be already formulated - i.e templates

     * replaced by this particular user's details

     * <br/>Example:

     * <code>

     * $n = new EfrontNotification(12);

     * $n -> sendTo(array('login' => 'joe','email' => 'test@test.gr', 'name' => 'Joe', 'surname' => 'Doe', 'user_type' => 'student'));

     *  $n -> sendTo('jack');

     * </code>

     *

     * @param: an array with the recipient's data containing 'login', 'email', 'name', 'surname' and 'user_type' or just a login

     * @return: true if the email was successfully sent

     */
    public function sendTo($recipient) {
     if (is_array($recipient)) {
      if (isset($recipient['login'])) {
       if (!(isset($recipient['email']) && isset($recipient['name']) && isset($recipient['surname']) && isset($recipient['user_type']) )) {
     $recipient = $recipient['login'];
       } else {
        $defined = 1;
       }
      } else {
       throw new EfrontNotificationException(_UNKNOWNRECIPIENT, EfrontNotificationException::NORECIPIENTLOGIN_DEFINED);
      }
     }
     if (!$defined) {
      $recipient = eF_getTableData("users", "*", "login = '".$recipient."'");
      if (!empty($recipient)) {
       $recipient = $recipient[0];
      } else {
       throw new EfrontNotificationException(_UNKNOWNRECIPIENT, EfrontNotificationException::NORECIPIENTLOGIN_DEFINED);
      }
     }
     // create the array of substitutions for this particular user and replace them in the subject/message texts
     $hostname = G_SERVERNAME;
     if ($hostname[strlen($hostname)-1] == "/") {
      $hostname = substr($hostname,0,strlen($hostname)-1);
     }
     $language = eF_getTableData("languages", "translation", "name = '" . $recipient['languages_NAME']. "'");
     if (!empty($language)) {
         $language = $language[0]['translation'];
     }
        $template_formulations = array("users_name" => $recipient['name'],
                  "users_surname" => $recipient['surname'],
                  "users_login" => $recipient['login'],
                  "users_email" => $recipient['email'],
                  "users_comments" => $recipient['comments'],
                  "users_language" => $language,
                  "date" => formatTimestamp(time()),
                  "date_time" => formatTimestamp(time(), 'time'),
                  "timestamp" => time(),
                  "user_type" => $recipient['user_type'],
                  "host_name" => $hostname,
                  "site_name" => $GLOBALS['configuration']['site_name'],
               "site_motto" => $GLOBALS['configuration']['site_motto']);
     $header = array ('From' => $GLOBALS['configuration']['system_email'],
                         'To' => $recipient['email'],
                         'Subject' => eF_formulateTemplateMessage($this -> notification['subject'], $template_formulations),
                         'Content-Transfer-Encoding' => '7bit',
          'Date' => date("r"));
     if ($this -> notification['html_message'] == 1) {
      $header['Content-type'] = 'text/html;charset="UTF-8"'; // if content-type is text/html, the message cannot be received by mail clients for Registration content
     } else {
      $header['Content-type'] = 'text/plain;charset="UTF-8"';
     }
        $smtp = Mail::factory('smtp', array('auth' => $GLOBALS['configuration']['smtp_auth'] ? true : false,
                                             'host' => $GLOBALS['configuration']['smtp_host'],
                                             'password' => $GLOBALS['configuration']['smtp_pass'],
                                             'port' => $GLOBALS['configuration']['smtp_port'],
                                             'username' => $GLOBALS['configuration']['smtp_user'],
                                             'timeout' => $GLOBALS['configuration']['smtp_timeout']));
        // force url change for html messages
        $message = eF_getCorrectLanguageMessage($this -> notification['message'], $recipient['languages_NAME']);
        // Local paths names should become urls
        if ($this -> notification['html_message'] == 1) {
         $message = str_replace('="content', '="###host_name###/content', $message);
   if ($configuration['math_images']) {
    $message = "<html><body><script type = \"text/javascript\" src = \"###host_name###/js/ASCIIMath2Tex.js\"> </script>".$message."</body></html>";
   } else {
    $message = "<html><body><script type = \"text/javascript\" src = \"###host_name###/js/ASCIIMathML.js\"> </script>".$message."</body></html>";
   }
  } else {
      $message = str_replace("<br />", "\r\n", $message);
   $message = str_replace("<br>", "\r\n", $message);
   $message = str_replace("<p>", "\r\n", $message);
   $message = str_replace("</p>", "\r\n", $message);
   $message = str_replace("&amp;", "&", $message);
   $message = strip_tags($message);
  }
        $message = eF_formulateTemplateMessage($message, $template_formulations);
     $message = eF_replaceMD5($message);
     //ssssssssssssssssssssss
     if ($smtp -> send($recipient['email'], $header, $message)) {
     //if (true) {  echo $recipient['email'] . " (" .$recipient['name'] . " " . $recipient['surname'] . ") " . $message ."<BR>";  // for debugging
     // put into sent_notifications table
         eF_insertTableData("sent_notifications", array("timestamp" => time(),
                     "recipient" => $recipient['email'] . " (" .$recipient['name'] . " " . $recipient['surname'] . ")",
                     "subject" => $header['Subject'],
                     "body" => $message));
         return true;
     } else {
         return false;
        }
    }
 /*

	 * Function that schedules the next time a notification needs to be send for "after" event types

	 * or deletes the notification for "before" event types

	 */
    public function scheduleNext() {
     if ($this -> notification['send_interval'] > 0) {
      eF_updateTableData("notifications", array("timestamp" => $this -> notification['timestamp'] + $this -> notification['send_interval']), "id = '". $this -> notification['id']. "'");
     } else {
      eF_deleteTableData("notifications", "id = '". $this -> notification['id']. "'");
     }
    }
    /*

     * Function used statically to send the next notifications

     *

     * <br/>Example:

     * <code>

     * EfrontNotification :: sendNextNotifications(10);

     * </code>

     *     * This function reads from the notifications table the $limit top values

     * and sends at most $limit emails according to each notifications' rules.

     *

     * @param: $limit the maximum number of emails to be sent

     */
    public static function sendNextNotifications($limit = false) {
     if (!$limit) {
      $limit = 5;
     }
     $init_limit = $limit;
     $result = eF_getTableData("notifications", "*", "active = 1 AND timestamp <" . time(), "timestamp ASC LIMIT $limit");
     $notifications_to_send = array();
     foreach ($result as $next_notification) {
      $notification = new EfrontNotification($next_notification);
      // Try to send all messages of this notification
      // Get message recipients: one or more
      $recipients = $notification -> getRecipients();
      try {
       foreach ($recipients as $login => $recipient) {
        // Send message
        if ($notification -> sendTo($recipient)) {
         $limit--;
        }
        unset($recipients[$login]);
        if (!$limit) {
         break;
        }
       }
      } catch (Exception $e) {
       $sendingErrors[] = $e -> getMessage();
      }
      // Check if the notification is periodical - if so  arrange (insert) the next notification
      // Note here: generated single recipient notifications should never have a send interval
      if ($notification -> notification['send_interval'] != "") {
       $notification -> scheduleNext();
      } else {
       // Pop this notification - delete it
       eF_deleteTableData("notifications", "id = '". $notification -> notification['id']."'");
      }
      if ($sendingErrors) {
       throw new Exception(implode(",", $sendingErrors));
      }
      // If all $limit messages have been sent, check whether some recipients still remain
      if (!$limit) {
       // Push all remaining recipients back to the notifications list, as single user notifications
       if (sizeof($recipients) > 0) {
        $notifications_to_send = array();
        foreach ($recipients as $login => $recipient) {
         $notifications_to_send[] = time() . "', '" . $login . "', '".$notification -> notification['message'] . "', '".$notification -> notification['subject'] . "', '" . $notification -> notification['id_type_entity'];
        }
        if (sizeof($notifications_to_send)) {
         eF_execute("INSERT INTO notifications (timestamp, recipient, message, subject, id_type_entity) VALUES ('". implode("'),('", $notifications_to_send) . "')");
        }
       }
       return $init_limit; // all messages have been sent
      }
     }
     return $init_limit - $limit;
    }
    /*

     * Function used statically to remove the stored sent notifications

     * beyond the limit of a certain number

     *

     * <br/>Example:

     * <code>

     * EfrontNotification :: clearSentMessages();

     * </code>

     *

     * @return: the number of messages deleted

     */
    public static function clearSentMessages() {
  $all_stored_sent = EfrontNotification::getRecentlySent();
  $total_num = sizeof($all_stored_sent);
  if ($total_num > $GLOBALS['configuration']['notifications_max_sent_messages']) {
   $sent_messages_to_delete = $total_num - $GLOBALS['configuration']['notifications_max_sent_messages'];
   // he list is sorted so delete the messages after the Nth entry
   $ids_to_delete = array();
   for ($i = $GLOBALS['configuration']['notifications_max_sent_messages']; isset($all_stored_sent[$i]); $i++) {
    $ids_to_delete[] = $all_stored_sent[$i]['id'];
   }
   if (!empty($ids_to_delete)) {
    eF_deleteTableData("sent_notifications", "id in ('" . implode("','", $ids_to_delete) . "')");
   }
  }
 }
    /**

     * Activate - deactivation functions

     */
    public static function activateEventNotification($event_notification_id) {
     eF_updateTableData("event_notifications", array("active" => 1), "id = '" . $event_notification_id. "'");
     $event_notification = eF_getTableData("event_notifications", "*", "id = " . $event_notification_id);
     EfrontNotification::initializeEventNotification($event_notification[0]);
    }
    public static function deactivateEventNotification($event_notification_id) {
  eF_updateTableData("event_notifications", array("active" => 0), "id = '" . $event_notification_id. "'");
  // disable unsent event notifications currently in the queue
  ///eF_updateTableData("notifications", array("active" => 0), "id_type_entity LIKE '" . $event_notification_id. "_%' ");
  // delete all related events - they will be readded if the event is activated again through initializeEventNotification
  eF_deleteTableData("notifications", "id_type_entity LIKE '" . $event_notification_id. "_%' ");
//		$event_notification = eF_getTableData("event_notifications", "*", "id = " . $event_notification_id);
//		EfrontNotification::initializeEventNotification($event_notification[0]);
    }
    public static function deleteEventNotification($event_notification_id) {
     eF_deleteTableData("event_notifications", "id = '" . $event_notification_id. "'");
     // delete unsent event notifications currently in the queue
     eF_deleteTableData("notifications", "id_type_entity LIKE '" . $event_notification_id. "_%' ");
    }
    public function activate() {
  return eF_updateTableData("notifications", array("active" => 1), "id = '" . $this -> notification['id'] . "'");
    }
    public function deactivate() {
  return eF_updateTableData("notifications", array("active" => 0), "id = '" . $this -> notification['id'] . "'");
    }
    public function delete() {
     return eF_deleteTableData("notifications", "id = '" . $this -> notification['id'] . "'");
    }
    /**

     * Get all (scheduled and on event) notifications

     *

     * <br/>Example:

     * <code>

     * EfrontNotification :: getAllNotifications();

     * </code>

     *

     * @return array of notifications in form []=>array([id],[send_interval],[send_conditions],[message], ([timestamp] OR [event]))

     * @since 3.6.0

     * @access public

     */
    public static function getAllNotifications() {
     $allNotifications = eF_getTableData("notifications", "*" , "id_type_entity IS NULL");
     $event_notifications = eF_getTableData("event_notifications", "*" , "", "active desc");
     $event_types = EfrontEvent::getEventTypes();
     foreach ($event_notifications as $notification) {
      if ($notification['event_type'] > 0) {
       $event_types_text = $event_types[$notification['event_type']]['text'];
      } else {
       $notification['event_type'] = (-1) * $notification['event_type'];
       if ($notification['send_interval'] > 0 || $notification['after_time'] > 0) {
           $event_types_text = $event_types[$notification['event_type']]['canBeNegated']; // get the negative event text
       } else {
        $event_types_text = $event_types[$notification['event_type']]['text']; //using the updated positive now value
       }
      }
         if ($notification['send_recipients'] == EfrontNotification::TRIGGERINGUSER) {
       $event_notification_recipients = _USERTRIGGERINGTHEEVENT;
      } else if ($notification['send_recipients'] == EfrontNotification::ALLSYSTEMUSERS) {
       $event_notification_recipients = _ALLSYSTEMUSERS;
      } else if ($notification['send_recipients'] == EfrontNotification::SYSTEMADMINISTRATOR) {
       $event_notification_recipients = _SYSTEMADMINISTRATOR;
      } else if ($notification['send_recipients'] == EfrontNotification::ALLLESSONUSERS) {
    $event_notification_recipients = _ALLLESSONUSERS;
   } else if ($notification['send_recipients'] == EfrontNotification::EXPLICITLYSEL) {
    $event_notification_recipients = _EXPLICITLYSELECTED;
   } else if ($notification['send_recipients'] == EfrontNotification::LESSONPROFESSORS) {
    $event_notification_recipients = _LESSONPROFESSORS;
   } else if ($notification['send_recipients'] == EfrontNotification::LESSONUSERSNOTCOMPLETED) {
       $event_notification_recipients = _LESSONUSERSNOTCOMPLETED;
   } else if ($notification['send_recipients'] == EfrontNotification::COURSEPROFESSORS) {
    $event_notification_recipients = _COURSEPROFESSORS;
      } else if ($notification['send_recipients'] == EfrontNotification::ALLCOURSEUSERS) {
    $event_notification_recipients = _ALLCOURSEUSERS;
   } else if ($notification['send_recipients'] == EfrontNotification::USERSUPERVISORS) {
    $event_notification_recipients = _USERSUPERVISORS;
      } else {
    $event_notification_recipients = "";
      }
      $allNotifications[] = array("id" => $notification['id'], "active" => $notification['active'], "event_notification_recipients" => $event_notification_recipients, "event_type"=> $notification['event_type'], "event" => $event_types_text, "send_interval" => $notification['after_time'], "send_conditions" => $notification['send_conditions'], "subject" => $notification['subject']);
     }
  return $allNotifications;
    }
   /**

     * Get notification message

     *

     * This function creates the message string for this notification

     * according to its type and its information

     *

     * <br/>Example:

     * <code>

     * $notification = new EfrontNotification(5);       //create object for notification with id 5

     * $notification -> createMessage();

     * echo $notification -> notification['message'];

     * </code>

     *

     * @param array with all modules to optimize module message printing

     * @returns the message value also set to the $this -> notification['message'] field

     * @since 3.6.0

     * @access public

     */
    public function createMessage($modulesArray = false) {
     if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_EVENTS == 0) {
      return array();
     }
     global $currentUser;
     if ($this -> notification['type'] >= EfrontNotification::MODULE_BASE_TYPE_CODE) {
      $className = $this -> notification['entity_ID'];
      if (isset($modulesArray[$className])) {
       $data = array();
       if ($this -> notification['entity_name'] != '') {
        $data = unserialize($this -> notification['entity_name']);
       }
       foreach ($this -> notification as $field => $value) {
        $data[$field] = $value;
       }
          $this -> notification['message'] = $modulesArray[$className] -> getNotificationMessage((integer)$this -> notification['type'] - EfrontNotification::MODULE_BASE_TYPE_CODE, $data);
      }
         if (! $this -> notification['message']) {
          $this -> notification['message'] = _UNREGISTEREDEVENT. " " . _FORTHEMODULE . " '" .$className. "'";
         }
     } else {
         $this -> notification['message'] = _NAMEARTICLE . " <b><a  href = \"".$currentUser -> getType().".php?ctg=social&op=show_profile&user=".$this->notification['users_LOGIN']. "&popup=1\" onclick = \"eF_js_showDivPopup('" . _USERPROFILE . "', 1)\"  target = \"POPUP_FRAME\"> ". $this -> notification['users_name']. " " . $this -> notification['users_surname']. "</a></b> ";
         if ($this -> notification['type'] == EfrontNotification::LESSON_ACQUISITION_AS_STUDENT) {
             $this -> notification['message'] .= _WASASSIGNEDTHELESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::LESSON_ACQUISITION_AS_PROFESSOR) {
             $this -> notification['message'] .= _WILLBETEACHINGLESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::LESSON_COMPLETION) {
             $this -> notification['message'] .= _HASCOMPLETEDLESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::LESSON_REMOVAL) {
             $this -> notification['message'] .= _NOLONGERATTENDSLESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::NEW_COMMENT_WRITING) {
             $this -> notification['message'] .= _WROTEACOMMENTFORUNIT . " <b>" . $this -> notification['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::TEST_COMPLETION) {
             $this -> notification['message'] .= _COMPLETEDTEST . " <b>" . $this -> notification['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::TEST_CREATION) {
             $this -> notification['message'] .= _CREATEDTHETEST . " <b>" . $this -> notification['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::NEW_FORUM) {
             $this -> notification['message'] .= _CREATEDTHENEWFORUM . " <b>" . $this -> notification['entity_name'] ."</b> " . _FORTHELESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::NEW_TOPIC) {
             $this -> notification['message'] .= _CREATEDTHENEWTOPIC . " <b>" . $this -> notification['entity_name'] ."</b> " . _FORTHELESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::NEW_POLL) {
             $this -> notification['message'] .= _CREATEDTHENEWPOLL . " <b>" . $this -> notification['entity_name'] ."</b> " . _FORTHELESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::NEW_FORUM_MESSAGE_POST) {
             $this -> notification['message'] .= _POSTEDTHENEWMESSAGE . " <b>" . $this -> notification['entity_name'] ."</b> " . _INTHEFORUMOFTHELESSON . " <b>" . $this -> notification['lessons_name'] ."</b>";
         } else if ($this -> notification['type'] == EfrontNotification::STATUS_CHANGE) {
             $this -> notification['message'] .= _CHANGEDHISHERPROFILE;
         } else if ($this -> notification['type'] == EfrontNotification::AVATAR_CHANGE) {
             $this -> notification['message'] .= _CHANGEDHISHERAVATARPICTURE;
         } else if ($this -> notification['type'] == EfrontNotification::PROFILE_CHANGE) {
             $this -> notification['message'] .= _CHANGEDHISHERPROFILE;
         } else if ($this -> notification['type'] == EfrontNotification::NEW_PROFILE_COMMENT_FOR_OTHER) {
             $this -> notification['message'] .= _COMMENTEDONTHEPROFILEOF;
             // Here check whether this is your own profile or not
             if ($this -> notification['entity_ID'] != $currentUser -> user['login']) {
              $this -> notification['message'] .= " <b><a  href = \"".$currentUser -> getType().".php?ctg=social&op=show_profile&user=".$this->notification['entity_ID']. "&popup=1\" onclick = \"eF_js_showDivPopup('" . _USERPROFILE . "', 1)\"  target = \"POPUP_FRAME\"> ". $this -> notification['entity_name']. "</a></b> ";
             } else {
              $this -> notification['message'] .= " <b>". $this->notification['entity_name'] . "</b>";
             }
         } else if ($this -> notification['type'] == EfrontNotification::NEW_PROFILE_COMMENT_FOR_SELF) {
          $this -> notification['message'] .= _COMMENTEDONHISHEROWNPROFILE;
         } else if ($this -> notification['type'] == EfrontNotification::DELETE_PROFILE_COMMENT_FOR_SELF) {
          $this -> notification['message'] .= _DELETEDACOMMENTFROMHISHEROWNPROFILE;
         } else if ($this -> notification['type'] == EfrontNotification::NEW_POST_FOR_LESSON_TIMELINE_TOPIC) {
          $topic_post = unserialize($this -> notification['entity_name']);
          $this -> notification['message'] .= _POSTEDFORLESSONTOPIC . " <b>" . $topic_post['topic_title'] . "</b> " . _THEPOST . ": " . $topic_post['data'];
          if ($this -> notification['users_LOGIN'] == $GLOBALS['currentUser'] -> user['login']) {
           $this -> notification['editlink'] = "<a href='".$_SESSION['s_type'] . ".php?ctg=social&op=timeline&lessons_ID=" . $this -> notification['lessons_ID'] . "&post_topic=" . $this -> notification['entity_ID'] . "&action=change&popup=1&id=" . $topic_post['post_id'] ."' onclick = 'eF_js_showDivPopup(\""._EDITMESSAGEFORLESSONTIMELINETOPIC. "\", 1)'  target = 'POPUP_FRAME'><img src='images/16x16/edit.png' border='0' alt = '"._EDITMESSAGEFORLESSONTIMELINETOPIC."' title='"._EDITMESSAGEFORLESSONTIMELINETOPIC."' /></a>";
           $this -> notification['deletelink'] = "<a href='".$_SESSION['s_type'] . ".php?ctg=social&op=timeline&lessons_ID=" . $this -> notification['lessons_ID'] . "&post_topic=" . $this -> notification['entity_ID'] . "&action=delete&id=" . $topic_post['post_id']."'><img src='images/16x16/error_delete.png' border='0' alt = '"._DELETEMESSAGEFORLESSONTIMELINETOPIC."' title='"._DELETEMESSAGEFORLESSONTIMELINETOPIC."' /></a>";
          }
         } else if ($this -> notification['type'] == EfrontNotification::DELETE_POST_FROM_LESSON_TIMELINE) {
          $this -> notification['message'] .= _DELETEDAPOSTFORLESSONTOPIC . " " . $this -> notification['entity_name'];
         } else {
             $this -> notification['message'] = _UNREGISTEREDEVENT;
         }
     }
        $this -> notification['time'] = eF_convertIntervalToTime(time() - $this->notification['timestamp'], true). ' '._AGO;
        return $this -> notification['message'];
    }
   /**

     * Check whether the next notifications in the queue should be sent

     *

     * This function checks a number of conditions to decide whether to send the next notifications according to:

     * - whether cron mode is used or not

     * - the average number of page loads between successive sends

     * - the maximum time between successive sends

     * - whether the send_immediately flag was set for an event notification

     *

     * <br/>Example:

     * <code>

     * if (EfrontNotification::shouldSendNextNotifications()) {

     * 		// send next notifications

     * }

     * </code>

     *

     * @returns true if next notifications should be sent or false otherwise

     * @since 3.6.0

     * @access public

     */
    public static function shouldSendNextNotifications() {
        if ($GLOBALS['configuration']['notifications_use_cron'] == 1) {
            return false;
        }
        if ((isset($_SESSION['send_next_notifications_now']) && $_SESSION['send_next_notifications_now']) ||
            ($GLOBALS['configuration']['notifications_pageloads'] > 0 && rand(1, $GLOBALS['configuration']['notifications_pageloads']) == 1) ||
            (($GLOBALS['configuration']['notifications_maximum_inter_time'] > 0 && $GLOBALS['configuration']['notifications_last_send_timestamp'] > 0) && ($GLOBALS['configuration']['notifications_last_send_timestamp'] + 60 *$GLOBALS['configuration']['notifications_maximum_inter_time'] < time()))) {
                /*//Debugging

                echo "condition 1: ***" . (isset($_SESSION['send_next_notifications_now']) && $_SESSION['send_next_notifications_now']) . "***<BR>";

                echo "condition 2: ***" . ($GLOBALS['configuration']['notifications_pageloads'] > 0 && rand(1, $GLOBALS['configuration']['notifications_pageloads']) == 1) . "***<BR>";

                echo "condition 3: ***" . (($GLOBALS['configuration']['notifications_maximum_inter_time'] > 0 && $GLOBALS['configuration']['notifications_last_send_timestamp'] > 0) && ($GLOBALS['configuration']['notifications_last_send_timestamp'] + 60 *$GLOBALS['configuration']['notifications_maximum_inter_time'] < time())) . "***<BR>";

                echo $GLOBALS['configuration']['notifications_last_send_timestamp'] . "  " . (60 *$GLOBALS['configuration']['notifications_maximum_inter_time']) . " " . time() ." <BR>";

                */
                return true;
        }
    }
}
