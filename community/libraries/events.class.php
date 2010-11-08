<?php
/**

 * File for evebts

 *

 * @package eFront

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * Event exceptions

 *

 * This class extends Exception to provide the exceptions related to events

 * @package eFront

 * @since 3.6.0

 *

 */
class EfrontEventException extends Exception
{
    /**

     * The event requested does not exist

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
 const LESSON_COMPLETED = 302;
}
/**

 * This class represents a event in eFront

 *

 * @package eFront

 * @since 3.6.0

 */
class EfrontEvent
{
    /* The constant type for each event - DO NOT CHANGE AS THEY ARE LINKED WITH THE digests.php FILE AND module events

     *

     * Description:

     * Events belonging to different "entities", like lessons, units, tests, forums etc, belong to different code groups

     * of 25 events.

     * Note: The entity_ID and entity_name fields have different meanings according to each event type.

     * Therefore, next to each event type we display the array fields needed and what the entities' fields mean in each case

     */
    // System event codes: [1-24]
 const SYSTEM_JOIN = 1;
 const SYSTEM_VISITED = 2;
 const SYSTEM_REMOVAL = 3;
    const SYSTEM_FORGOTTEN_PASSWORD = 4;
    const SYSTEM_REGISTER = 5;
    const SYSTEM_ON_EMAIL_ACTIVATION = 6; // entity_name (=timestamp of activation email sending)
    const SYSTEM_NEW_PASSWORD_REQUEST = 7; // entity_name (=new password) ?extra-security-needed?
 const SYSTEM_USER_DEACTIVATE = 8;
 // Lesson codes: [25 - 49]
    const LESSON_ACQUISITION_AS_STUDENT = 25; // users_LOGIN, lessons_ID, lessons_name
    const LESSON_ACQUISITION_AS_PROFESSOR = 26; // users_LOGIN, lessons_ID, lessons_name
    const LESSON_VISITED = 27; // users_LOGIN, lessons_ID, lessons_name
    const LESSON_REMOVAL = 28; // users_LOGIN, lessons_ID, lessons_name
    const LESSON_COMPLETION = 29; // users_LOGIN, lessons_ID, lessons_name
    const PROJECT_SUBMISSION = 30; // users_LOGIN, lessons_ID, lessons_name, entity_ID = project_ID, entity_name = project_name
    const PROJECT_CREATION = 31; // users_LOGIN, lessons_ID, lessons_name, entity_ID = project_ID, entity_name = project_name
 const LESSON_PROGRAMMED_START = 39; // users_LOGIN, lessons_ID, lessons_name
 const LESSON_PROGRAMMED_EXPIRY = 40;
    const PROJECT_EXPIRY = 41; // users_LOGIN, lessons_ID, lessons_name, entity_ID = project_ID, entity_name = project_name
    // Course codes: [50-74] - IMPORTANT KEEP COURSE-EVENTS STRICTLY WITHIN THESE LIMITS
               // 	For courses we have lessons_name -> courses_name
    const COURSE_ACQUISITION_AS_STUDENT = 50; // users_LOGIN, lessons_ID, lessons_name
    const COURSE_ACQUISITION_AS_PROFESSOR = 51; // users_LOGIN, lessons_ID, lessons_name
    const COURSE_VISITED = 52; // users_LOGIN, lessons_ID, lessons_name
    const COURSE_REMOVAL = 53; // users_LOGIN, lessons_ID, lessons_name
    const COURSE_COMPLETION = 54; // users_LOGIN, lessons_ID, lessons_name
    const COURSE_CERTIFICATE_ISSUE = 55; // users_LOGIN, lessons_ID, lessons_name, entity_name (grade)
    const COURSE_CERTIFICATE_REVOKE = 56; // users_LOGIN, lessons_ID, lessons_name	const COURSE_PROGRAMMED_START = 57;		 	// users_LOGIN, lessons_ID, lessons_name
    const COURSE_PROGRAMMED_EXPIRY = 58;
    // Test codes: [75-99]
    const TEST_CREATION = 75;
    const TEST_START = 76;
    const TEST_COMPLETION = 77; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=test_ID), entity_name (=test name)
    // Content [100-124]
    const CONTENT_CREATION = 100;
    const CONTENT_MODIFICATION = 101;
    const CONTENT_START = 102;
 const CONTENT_COMPLETION = 103;
    const NEW_COMMENT_WRITING = 104; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=content_ID), entity_name (=unit name)
    // Forum codes: [125-149]
    const NEW_FORUM = 125; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=forum_ID), entity_name (=forum name)
    const NEW_TOPIC = 32; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=forum_ID), entity_name (=forum name)
    const NEW_POLL = 33; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=forum_ID), entity_name (=forum name)
  const NEW_POST_FOR_LESSON_TIMELINE_TOPIC = 34; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=topics_ID), entity_name (=array("id" => post id, "data" => post text)
    const DELETE_POST_FROM_LESSON_TIMELINE = 35; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=topics_ID), entity_name (=topic_title)
  const NEW_FORUM_MESSAGE_POST = 38; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=forum_ID), entity_name (=forum name)
    // Personal information codes: [150-174]
    const STATUS_CHANGE = 150; // users_LOGIN, entity_name (=new status)
    const AVATAR_CHANGE = 151; // users_LOGIN, entity_id (=new img file id)
    const PROFILE_CHANGE = 152; // users_LOGIN
    const NEW_PROFILE_COMMENT_FOR_OTHER = 153; // users_LOGIN (=login of author), users_name/surname (=author name/surname),
                // entity_id (=login of target user profile), entity_name (=target user Name Surname)
    const NEW_PROFILE_COMMENT_FOR_SELF = 154; // users_LOGIN (=login of author), users_name/surname (=author name/surname)
    const DELETE_PROFILE_COMMENT_FOR_SELF = 155; // users_LOGIN (=login of author), users_name/surname (=author name/surname)
    // Announcements: [175-199]
    const NEW_SYSTEM_ANNOUNCEMENT = 175; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=news_ID), entity_name (=news title)
    const NEW_LESSON_ANNOUNCEMENT = 176; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=news_ID), entity_name (=news title)
    // Survey: [200-224]
    const NEW_SURVEY = 200; // users_LOGIN, lessons_ID, lessons_name, entity_ID (=survey_ID), entity_name (=survey message)
    // Hcd: [300-324]
    const HCD_NEW_BRANCH = 300; // users_LOGIN, lessons_ID (=branches_ID), lessons_name (=branch_name)
    const HCD_REMOVE_BRANCH = 301; // users_LOGIN, lessons_ID (=branches_ID), lessons_name (=branch_name)
    const HCD_NEW_JOB_ASSIGNMENT = 302; // users_LOGIN, lessons_ID (=branches_ID), lessons_name (=branch_name), entity_ID (=job id), entity_name (=job name)
    const HCD_REMOVE_JOB_ASSIGNMENT = 303; // users_LOGIN, lessons_ID (=branches_ID), lessons_name (=branch_name), entity_ID (=job id), entity_name (=job name)
    const HCD_FIRED = 304; // users_LOGIN
    const HCD_NEW_SKILL = 305; // users_LOGIN, entity_ID (=skill_ID), entity_name (=skill_description \(specification\))
    const HCD_REMOVE_SKILL = 306; // users_LOGIN, entity_ID (=skill_ID), entity_name (=skill_description \(specification\))
    const HCD_SKILL_EDIT = 307; // users_LOGIN, entity_ID (=skill_ID), entity_name (=skill_description \(specification\))
    const HCD_HIRED = 308; // users_LOGIN
    const HCD_LEFT = 309; // users_LOGIN
    const HCD_WAGE_CHANGE = 310; // users_LOGIN, entity_name (=new wage)
    // Groups: [325-
    const NEW_ASSIGNMENT_TO_GROUP = 325; // users_LOGIN, entity_ID (=group_ID), entity_name (=group_name)
    const REMOVAL_FROM_GROUP = 326; // users_LOGIN, entity_ID (=group_ID), entity_name (=group_name)
    // Payments: 350-352
    const NEW_BALANCE_PAYMENT = 350;
    const NEW_PAYPAL_PAYMENT = 351;
    const COUPON_USAGE = 352; // users_LOGIN, users_name/surname, entity_ID (=coupons_ID), entity_name (=coupon_code)
 const MODULE_BASE_TYPE_CODE = 1000; // all events with type > 1000 are considered module-related events
           // the type of each event inside each module class is [type_value] - 1000
           // the class of the module involved is defined in the entity_ID field
           // For these events: (entity_ID => className, entity_name => serialized(data from module - view addEvent module method)
 const SAME_USER_INTERVAL = 600; // time after which a new event of the same type from the same user will be reported
    /**

     * The event variable

     *

     * @since 3.6.0

     * @var array

     * @access public

     */
    public $event = array();
    /**

     * Create event instance

     *

     * This function creates the event instance based on the

     * given event id.

     * <br/>Example:

     * <code>

     * $event = new EfrontEvent(5);       //create object for event with id 5

     * </code>

     *

     * @param mixed $event The event id or the event array

     * @since 3.6.0

     * @access public

     */
    function __construct($event) {
        if (is_array($event)) {
            $this -> event = $event;
        } else {
            if (!eF_checkParameter($event, 'id')) {
                throw new EfrontEventException(_INVALIDID, EfrontEventException :: INVALID_ID);
            }
            $event = eF_getTableData("events", "*", "id = $event");
            if (sizeof($event) == 0) {
                throw new EfrontEventException(_EVENTDOESNOTEXIST, EfrontEventException :: EVENT_NOT_EXISTS);
            }
            $this -> event = $event[0];
        }
    }
    /**

     * Get all system event types

     *

     * This function returns an array with the event types and their descriptive strings.

     * <br/>Example:

     * <code>

     * EfrontEvent::getEventTypes(false);

     * </code>

     *

     * @param boolean $get_module_events, will also return events registered by modules

     * @return array The current event types

     * @since 3.6.0

     * @access public

     */
    private static $system_events = false;
    public static function getEventTypes($get_module_events = false) {
     if (!isset($system_events) || !$system_events) {
      $system_events = array(EfrontEvent::SYSTEM_JOIN => array("text" => _SYSTEMJOIN, "category" => "system", "priority" => 1, "afterEvent" => 1),
             EfrontEvent::SYSTEM_REMOVAL => array("text" => _SYSTEM_REMOVAL, "category" => "system"),
             EfrontEvent::SYSTEM_VISITED => array("text" => _SYSTEM_VISITED, "category" => "system", "canBeNegated" => _SYSTEM_NOT_VISITED, "priority" => 1, "afterEvent" => 1),
             EfrontEvent::SYSTEM_FORGOTTEN_PASSWORD => array("text" => _SYSTEM_ONPASSWORD_FORGOTTEN, "category" => "system"),
             EfrontEvent::SYSTEM_NEW_PASSWORD_REQUEST => array("text" => _SYSTEM_ON_NEW_PASSWORD_REQUEST, "category" => "system"),
             EfrontEvent::SYSTEM_REGISTER => array("text" => _SYSTEM_REGISTERED, "category" => "system"),
             EfrontEvent::SYSTEM_ON_EMAIL_ACTIVATION => array("text" => _SYSTEM_EMAIL_ACTIVATION, "category" => "system"),
          EfrontEvent::SYSTEM_USER_DEACTIVATE => array("text" => _SYSTEM_USER_DEACTIVATED, "category" => "system"),
             EfrontEvent::LESSON_ACQUISITION_AS_STUDENT => array("text" => _LESSON_ACQUISITION_AS_STUDENT, "category" => "lessons", "priority" => 1, "afterEvent" => 1),
             EfrontEvent::LESSON_ACQUISITION_AS_PROFESSOR => array("text" => _LESSON_ACQUISITION_AS_PROFESSOR, "category" => "lessons", "priority" => 1, "afterEvent" => 1),
             EfrontEvent::LESSON_VISITED => array("text" => _LESSON_VISITED, "category" => "lessons", "canBeNegated" => _LESSON_NOT_VISITED, "afterEvent" => 1),
             EfrontEvent::LESSON_REMOVAL => array("text" => _LESSON_REMOVAL, "category" => "lessons"),
             EfrontEvent::LESSON_COMPLETION => array("text" => _LESSON_COMPLETION, "category" => "lessons", "canBeNegated" => _LESSON_NOT_COMPLETED, "priority" => 1, "afterEvent" => 1),
             EfrontEvent::PROJECT_CREATION => array("text" => _NEWLESSONPROJECT, "category" => "lessons", "priority" => 1),
             EfrontEvent::PROJECT_SUBMISSION => array("text" => _PROJECT_SUBMISSION, "category" => "lessons", "priority" => 1, "afterEvent" => 1),
             EfrontEvent::LESSON_PROGRAMMED_START => array("text" => _PROGRAMMEDLESSONSTART, "category" => "lessons", "canBePreceded" => 1, "priority" => 1, "afterEvent" => 1),
             EfrontEvent::LESSON_PROGRAMMED_EXPIRY => array("text" => _PROGRAMMEDLESSONEXPIRY, "category" => "lessons", "canBePreceded" => 1, "priority" => 1, "afterEvent" => 1),
             EfrontEvent::PROJECT_EXPIRY => array("text" => _PROJECTEXPIRY, "category" => "projects", "canBePreceded" => 1, "priority" => 1, "afterEvent" => 1),
             EfrontEvent::COURSE_ACQUISITION_AS_STUDENT => array("text" => _COURSE_ACQUISITION_AS_STUDENT, "category" => "courses", "afterEvent" => 1),
             EfrontEvent::COURSE_ACQUISITION_AS_PROFESSOR => array("text" => _COURSE_ACQUISITION_AS_PROFESSOR, "category" => "courses", "afterEvent" => 1),
             //EfrontEvent::COURSE_VISITED => array("text" => _COURSE_VISITED, "category" => "courses", "canBeNegated" => _COURSE_NOT_VISITED),
             EfrontEvent::COURSE_REMOVAL => array("text" => _COURSE_REMOVAL, "category" => "courses"),
             EfrontEvent::COURSE_COMPLETION => array("text" => _COURSE_COMPLETION, "category" => "courses", "canBeNegated" => _COURSE_NOT_COMPLETED, "afterEvent" => 1),
          EfrontEvent::COURSE_CERTIFICATE_ISSUE => array("text" => _CERTIFICATEISSUE, "category" => "courses", "afterEvent" => 1),
          EfrontEvent::COURSE_CERTIFICATE_REVOKE => array("text" => _CERTIFICATEREVOKE, "category" => "courses"),
          EfrontEvent::COURSE_PROGRAMMED_START => array("text" => _PROGRAMMEDCOURSESTART, "category" => "courses", "canBePreceded" => 1, "priority" => 1, "afterEvent" => 1),
          EfrontEvent::COURSE_PROGRAMMED_EXPIRY => array("text" => _PROGRAMMEDCOURSEEXPIRY, "category" => "courses", "canBePreceded" => 1, "priority" => 1, "afterEvent" => 1),
             EfrontEvent::NEW_POST_FOR_LESSON_TIMELINE_TOPIC => array("text" => _NEW_POST_FOR_LESSON_TIMELINE_TOPIC, "category" => "social"),
             EfrontEvent::DELETE_POST_FROM_LESSON_TIMELINE => array("text" => _DELETE_POST_FROM_LESSON_TIMELINE, "category" => "social"),
             EfrontEvent::TEST_CREATION => array("text" => _TEST_CREATION, "category" => "tests"),
             //EfrontEvent::TEST_START => array("text" => _TEST_START, "category" => "tests"),
             EfrontEvent::TEST_COMPLETION => array("text" => _TEST_COMPLETION, "category" => "tests", "canBeNegated" => _TEST_NOT_COMPLETED),
             EfrontEvent::CONTENT_CREATION => array("text" => _CONTENT_CREATION, "category" => "content"),
             EfrontEvent::CONTENT_MODIFICATION => array("text" => _CONTENT_MODIFICATION, "category" => "content"),
             //EfrontEvent::CONTENT_START => array("text" => _CONTENT_START, "category" => "content"),
             EfrontEvent::CONTENT_COMPLETION => array("text" => _CONTENT_COMPLETION, "category" => "content", "canBeNegated" => _CONTENT_NOT_COMPLETED),
             EfrontEvent::NEW_COMMENT_WRITING => array("text" => _NEW_COMMENT_WRITING, "category" => "content"),
             EfrontEvent::NEW_FORUM => array("text" => _NEW_FORUM, "category" => "forum"),
             EfrontEvent::NEW_TOPIC => array("text" => _NEW_TOPIC, "category" => "forum"),
             EfrontEvent::NEW_POLL => array("text" => _NEW_POLL, "category" => "forum"),
             EfrontEvent::NEW_FORUM_MESSAGE_POST => array("text" => _NEW_FORUM_MESSAGE_POST, "category" => "forum"),
             EfrontEvent::STATUS_CHANGE => array("text" => _STATUS_CHANGE, "category" => "personal"),
             EfrontEvent::AVATAR_CHANGE => array("text" => _AVATAR_CHANGE, "category" => "personal"),
             EfrontEvent::PROFILE_CHANGE => array("text" => _PROFILE_CHANGE , "category" => "personal"),
             EfrontEvent::NEW_PROFILE_COMMENT_FOR_OTHER => array("text" => _NEW_PROFILE_COMMENT_FOR_OTHER, "category" => "personal"),
             EfrontEvent::NEW_PROFILE_COMMENT_FOR_SELF => array("text" => _NEW_PROFILE_COMMENT_FOR_SELF, "category" => "personal"),
             EfrontEvent::DELETE_PROFILE_COMMENT_FOR_SELF => array("text" => _DELETE_PROFILE_COMMENT_FOR_SELF, "category" => "personal"),
             EfrontEvent::NEW_SYSTEM_ANNOUNCEMENT => array("text" => _NEWSYSTEMANNOUNCEMENT, "category" => "news", "priority" => 1),
             EfrontEvent::NEW_LESSON_ANNOUNCEMENT => array("text" => _NEWLESSONANNOUNCEMENT, "category" => "news", "priority" => 1),
             EfrontEvent::NEW_ASSIGNMENT_TO_GROUP => array("text" => _GROUPASSIGNMENT, "category" => "groups"),
             EfrontEvent::REMOVAL_FROM_GROUP => array("text" => _REMOVALFROMGROUP, "category" => "groups"),
             EfrontEvent::NEW_SURVEY => array("text" => _NEWSURVEY, "category" => "survey", "afterEvent" => 1),
             EfrontEvent::NEW_BALANCE_PAYMENT => array("text" => _NEWBALANCEPAYMENT, "category" => "payments"),
             EfrontEvent::NEW_PAYPAL_PAYMENT => array("text" => _NEWPAYPALPAYMENT, "category" => "payments"),
             EfrontEvent::COUPON_USAGE => array("text" => _COUPONUSAGE, "category" => "payments")
          );
//2222222222222222222222222
     }
     return $system_events;
    }
    /**

     * Get event lessons

     *

     * This function gets a list with the event lessons. If a specific order

     * is set, the lessons are ordered based on it

     * <br/>Example:

     * <code>

     * $event -> getLessons();

     * </code>

     *

     * @param boolean $returnObjects Whether to return EfrontLesson objects

     * @return array The event lessons

     * @since 3.6.0

     * @access public

     */
    public function getLessons($returnObjects = false) {
        if ($this -> lessons == false) {
            $result = eF_getTableData("lessons_to_events lc, lessons l", "lc.previous_lessons_ID, l.*", "l.id=lc.lessons_ID and events_ID=".$this -> event['id']);
            if (sizeof($result) > 0) {
                $previous = 0; //Previous is only used when no previos_lessons_ID is set
                foreach ($result as $value) {
                    $eventLessons[$value['id']] = $value;
                    $value['previous_lessons_ID'] !== false ? $previousLessons[$value['previous_lessons_ID']] = $value : $previousLessons[$previous] = $value;
                    $previous = $value['id'];
                }
                //Sorting algorithm, based on previous_lessons_ID. The algorithm is copied from EfrontContentTree :: reset() and is the same with the one applied for content. It is also used in questions order
                $node = 0;
                $count = 0;
                $nodes = array(); //$count is used to prevent infinite loops
                while (sizeof($previousLessons) > 0 && isset($previousLessons[$node]) && $count++ < 1000) {
                    $nodes[$previousLessons[$node]['id']] = $previousLessons[$node];
                    $newNode = $previousLessons[$node]['id'];
                    unset($previousLessons[$node]);
                    $node = $newNode;
                }
                $this -> lessons = $nodes;
                if (sizeof($nodes) != sizeof($result)) { //If the ordering is messed up for some reason
                    $this -> lessons = $eventLessons;
                    eF_updateTableData("lessons_to_events", array("previous_lessons_ID" => NULL), "events_ID=".$this -> event['id']);
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

     * Get events that are relevant for a particular user

     * from the most recent to the least recent ones

     *

     * Relevancy is dictated by common classes (or common branches in HCD)

     * <br/>Example:

     * <code>

     * $events = EfrontEvent :: getEvents('joe');

     * or

     * $events = EfrontEvent :: getEvents(array('joe', 'averel', 'jack'));

     * </code>

     *

     * @param $login the login of the associated user or an array of all related users logins

     * @param $returnObjects whether to return EfrontEvent objects or arrays of data from the databaes

     * @param $max how many events to return from the database

     * @return array of DB records or array of EfrontEvent objects

     * @since 3.6.0

     * @access public

     */
    public static function getEvents($login, $returnObjects = false, $max = false) {
     if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_EVENTS == 0) {
      return array();
     }
        if (!is_array($login)) {
            $login = array("0" => $login);
        }
        //@todo change de xreiazetai
        if ($max) {
            $events = eF_getTableData("events", "*", "users_LOGIN in ('".implode("','", $login)."')","timestamp DESC LIMIT $max");
        } else {
            $events = eF_getTableData("events", "*", "users_LOGIN in ('".implode("','", $login)."')","timestamp DESC");
        }
        if ($returnObjects) {
            $eventObjects = array();
            foreach ($events as $event) {
                $eventObjects[] = new EfrontEvent($event);
            }
            return $eventObjects;
        } else {
            return $events;
        }
    }
   /**

     * Get events for all users

     * from the most recent to the least recent ones

     *

     * Relevancy is dictated by common classes (or common branches in HCD)

     *

     * @param $returnObjects whether to return EfrontEvent objects or arrays of data from the databaes

     * @param $max how many events to return from the database

     * @return array of DB records or array of EfrontEvent objects

     * @since 3.6.7

     * @access public

     * @static

     */
    public static function getEventsForAllUsers($returnObjects = false, $max = false) {
     if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_EVENTS == 0) {
      return array();
     }
        //@todo change de xreiazetai
        if ($max) {
            $events = eF_getTableData("events", "*", "", "timestamp DESC LIMIT $max");
        } else {
            $events = eF_getTableData("events", "*", "", "timestamp DESC");
        }
        if ($returnObjects) {
            $eventObjects = array();
            foreach ($events as $event) {
                $eventObjects[] = new EfrontEvent($event);
            }
            return $eventObjects;
        } else {
            return $events;
        }
    }
    /**

     * Get all events related to forums visible to a user

     *

     * <br/>Example:

     * <code>

     * $events = EfrontEvent :: getForumEvents('joe');

     * or

     * $events = EfrontEvent :: getForumEvents(array('joe', 'averel', 'jack'), true, 10);	//10 EfrontEvent objects will be returned

     * </code>

     *

     * @param $login the login of the associated user or an array of all related users logins

     * @param $returnObjects whether to return EfrontEvent objects or arrays of data from the databaes

     * @param $max how many events to return from the database

     * @return array of DB records or array of EfrontEvent objects

     * @since 3.6.0

     * @access public

     */
 public static function getForumEvents($login, $returnObjects = false, $max = false) {
     if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_EVENTS == 0) {
      return array();
     }
        if (!is_array($login)) {
            $login = array("0" => $login);
        }
        //@todo change de xreiazetai
        $forum_events = array(EfrontEvent::NEW_FORUM,EfrontEvent::NEW_TOPIC, EfrontEvent::NEW_POLL);
        if ($max) {
            $events = eF_getTableData("events", "*", "users_LOGIN in ('".implode("','", $login)."') AND type IN ('".implode("','", $forum_events)."')","timestamp DESC LIMIT $max");
        } else {
            $events = eF_getTableData("events", "*", "users_LOGIN in ('".implode("','", $login)."') AND type IN ('".implode("','", $forum_events)."')","timestamp DESC");
        }
        if ($returnObjects) {
            $eventObjects = array();
            foreach ($events as $event) {
                $eventObjects[] = new EfrontEvent($event);
            }
            return $eventObjects;
        } else {
            return $events;
        }
    }
    /**

     * Trigger an event

     *

     * Denotes the triggering of an event in the eFront system

     * All functionalities that should take place during an event triggering

     * like logging, notification sending etc, should be defined here

     * <br/>Example:

     * <code>

     * $fields = array('name' => 'new event', 'languages_NAME' => 'english');

     * $event = EfrontEvent :: triggerEvent($fields);

     * </code>

     *

     * @param array $fields The new fields

     * @param boolean $send_notification Send notification or not

     * @return EfrontEvent the new event

     * @since 3.6.0

     * @access public

     */
    public static function triggerEvent($fields, $send_notification = true) {
  // Check and create all necessary fields
        if (!isset($fields['type'])) {
            throw new EfrontEventException(_NOEVENTCODEDEFINED, EfrontEventException::NOEVENTCODE_DEFINED);
        }
        //These are the mandatory fields. In case one of these is absent, fill it in with a default value
        // If no user is defined the currentuser will be used as user triggering the event
        if (!isset($fields['users_LOGIN'])) {
            $fields['users_LOGIN'] = $GLOBALS['currentUser'] -> user['login'];
            $fields['users_name'] = $GLOBALS['currentUser'] -> user['name'];
            $fields['users_surname'] = $GLOBALS['currentUser'] -> user['surname'];
        }
        // If a users login is defined, but without any name/surname fields, then get them from the DB
        if (!isset($fields['users_name']) || !isset($fields['users_surname'])) {
            $users_id = eF_getTableData("users", "name, surname", "login = '".$fields['users_LOGIN']."'");
            if ($users_id) {
             $fields['users_name'] = $users_id[0]['name'];
             $fields['users_surname'] = $users_id[0]['surname'];
            } else {
             $fields['users_name'] = '';
             $fields['users_surname'] = '';
            }
        }
        // Events that canBePreceded might be triggered from now for the future - these events have their timestamp field already set
        if (!isset($fields['timestamp'])) {
         $fields['timestamp'] = time();
        }
        // Get the events array and get the information for this event type
        $event_types = EfrontEvent::getEventTypes();
  $type = $event_types[$fields['type']];
        // the $fields['lessons_ID'] may refer to either courses or lessons according to the type of the event
        if ($type['category'] == "courses") {
         // Allow multiple course ids for each event
          if (is_array($fields['lessons_ID'])) {
             $event_courses = eF_getTableData("courses", "id, name" , "id in (".implode(",", $fields['lessons_ID']).")");
             $result = true;
             //$fields['lessons'] = array();
             $fields['lessons_name'] = "";
             foreach ($event_courses as $course) {
              //$fields['lessons'][] = array("lessons_ID" => $course['id'], 'lessons_name' => $course['name']);
              if ($fields['lessons_name'] != "") {
               $fields['lessons_name'] .= ", ";
              }
              $fields['lessons_name'] .= $course['name'];
             }
         } else {
          // if not pre-defined
          if (!isset($fields['lessons_name']) || $fields['lessons_name'] == "") {
           $event_courses = eF_getTableData("courses", "id, name" , "id = '". $fields['lessons_ID']."'");
           $fields['lessons_name'] = $event_courses[0]['name'];
          }
         }
        } else {
         // Allow multiple lesson ids for each event
         if (isset($fields['lessons_ID']) && is_array($fields['lessons_ID'])) {
             $event_lessons = eF_getTableData("lessons", "id, name" , "id in (".implode(",", $fields['lessons_ID']).")");
             $result = true;
             //$fields['lessons'] = array();
             $fields['lessons_name'] = "";
             foreach ($event_lessons as $lesson) {
              //$fields['lessons'][] = array("lessons_ID" => $lesson['id'], 'lessons_name' => $lesson['name']);
              if ($fields['lessons_name'] != "") {
               $fields['lessons_name'] .= ", ";
              }
              $fields['lessons_name'] .= $lesson['name'];
             }
         } else {
          // if not pre-defined
          if (isset($fields['lessons_ID']) && (!isset($fields['lessons_name']) || $fields['lessons_name'] == "")) {
           $event_lessons = eF_getTableData("lessons", "id, name" , "id = '". $fields['lessons_ID'] ."'");
           $fields['lessons_name'] = $event_lessons[0]['name'];
          }
         }
        }
  if ($fields['type'] == EfrontEvent::CONTENT_COMPLETION) {
   if (isset($fields['entity_ID']) && !isset($fields['entity_name'])) {
    $unit_info = new EfrontUnit($fields['entity_ID']);
    $fields['entity_name'] = $unit_info['name'];
   }
  }
     // If social eFront module is enabled then log this event
     //if ((isset($GLOBALS['configuration']['social_modules_activated']) && $GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_EVENTS) != 0) {
      // Negative events like not visited, not completed etc are not to be logged
      if ($fields['type'] > 0 && (!isset($event_types[$fields['type']]['notToBeLogged']) || $event_types[$fields['type']]['notToBeLogged'] == 0)) {
       if (isset($fields['explicitly_selected'])) {
        $explicitly_selected = $fields['explicitly_selected'];
        unset($fields['explicitly_selected']);
       }
       EfrontEvent::logEvent($fields);
       if (isset($explicitly_selected)) {
        $fields['explicitly_selected'] = $explicitly_selected;
       }
      }
     //}
     // By default all notifications will be sent
     if ($send_notification) {
      $event = new EfrontEvent($fields); // this should create an event instance for our class
      $event -> appendNewNotification($event_types); // append this notification to the email queue
     }
    }
    // Substitute templates in message body/subject of emails
    private function createSubstitutionsArray($event_types, $send_recipients) {
     if ($this -> event['timestamp'] != "") {
         $timestamp = $this -> event['timestamp'];
     } else {
         $timestamp = time();
     }
     // The user's templates should only be replaced now if the notification is to be sent to him only
     if ($send_recipients == EfrontNotification::TRIGGERINGUSER) {
      $subst_array = array("users_name" => $this -> event['users_name'],
             "users_surname" => $this -> event['users_surname'],
              "users_LOGIN" => $this -> event['users_LOGIN'],
            "date" => formatTimestamp($timestamp),
               "date_time" => formatTimestamp(time(), 'time'),
            "timestamp" => $timestamp);
     } else {
      $subst_array = array("date" => formatTimestamp($timestamp),
            "date_time" => formatTimestamp(time(), 'time'),
            "timestamp" => $timestamp);
     }
     $triggeringUser = EfrontUserFactory::factory($this -> event['users_LOGIN']);
     $subst_array["triggering_users_name"] = $triggeringUser -> user['name'];
     $subst_array["triggering_users_surname"] = $triggeringUser -> user['surname'];
     $subst_array["triggering_users_login"] = $triggeringUser -> user['login'];
     $subst_array["triggering_user_type"] = $triggeringUser -> user['user_type'];
     $subst_array["triggering_users_email"] = $triggeringUser -> user['email'];
     // Special case - new password
     if ($this -> event['type'] == EfrontEvent::SYSTEM_NEW_PASSWORD_REQUEST) {
      $subst_array['new_password'] = $this -> event['entity_name'];
     }
     if ($this -> event['type'] == EfrontEvent::SYSTEM_JOIN) {
      $subst_array['new_password'] = $this -> event['entity_name'];
     }
     if (isset($event_types[abs($this -> event['type'])])) {
      $type = $event_types[abs($this -> event['type'])];
      //echo $type . "***";
      // the $this -> event['lessons_name'] might refer to courses or lessons according to the category
      if ($type['category'] == "courses") {
       $subst_array['courses_name'] = $this -> event['lessons_name'];
      } else if ($type['category'] == 'payments') {
       $subst_array['lessons_name'] = $this -> event['lessons_name'];
      } else {
       if ($this -> event['lessons_ID'] == 0) {
        $subst_array['lessons_name'] = "###site_name### " . _SYSTEM;
       } else {
        if ($this -> event['lessons_name']) {
         $subst_array['lessons_name'] = $this -> event['lessons_name'];
        } else {
         try {
          $lesson = new EfrontLesson($this -> event['lessons_ID']);
          $subst_array['lessons_name'] = $lesson -> lesson['id'];
         } catch (EfrontLessonException $e) {
          $subst_array['lessons_name'] = _LESSONNOTFOUND;
         }
        }
       }
      }
         if ($type['category'] == "tests") {
       $subst_array['tests_name'] = $this -> event['entity_name'];
      }
      if ($type['category'] == "news") {
       $subst_array['announcement_title'] = $this -> event['entity_name'];
       $news = eF_getTableData("news", "data", "id = '". $this -> event['entity_ID'] ."'");
       if ($news[0]['data']) {
        $subst_array['announcement_body'] = $news[0]['data'];
       }
      }
      if ($type['category'] == "survey") {
       $subst_array['survey_message'] = $this -> event['entity_name'];
       $subst_array['survey_id'] = $this -> event['entity_ID'];
       $survey = eF_getTableData("surveys", "survey_name", "id = '". $this -> event['entity_ID'] ."'");
       if ($survey[0]['survey_name']) {
        $subst_array['survey_name'] = $survey[0]['survey_name'];
       }
      }
         if ($type['category'] == "content") {
       $content = eF_getTableData("content", "name, data", "id = '". $this -> event['entity_ID'] ."'");
       if ($content[0]['name']) {
        $subst_array['unit_title'] = $content[0]['name'];
        $subst_array['unit_content'] = $content[0]['data'];
       }
      }
      if ($type['category'] == "branch" || $type['category'] == "job") {
       $subst_array['branch_name'] = $this -> event['lessons_name'];
      }
      if ($type['category'] == "job") {
       $subst_array['job_description_name'] = $this -> event['entity_name'];
      }
     } else {
      throw new EfrontEventException(_EVENTDOESNOTEXIST, EfrontEventException :: EVENT_NOT_EXISTS);
     }
     return $subst_array;
    }
    /*

     * Appends a notification resulting from an event triggering into the notification's queue (DB)

     * This notification will be sent by the notifying daemon

     *

     * @param: event_types the array with all registered event types

     * @replace_notification: bool denoting whether previous notifications of that type for that user will be first deleted

     * @create_negative: check and create notifications for the negative of an event, for example create the NOT VISITED event

     * 					when an event VISITED is used (*** and vice versa ***)

     */
    public function appendNewNotification($event_types, $replace_notification = false, $create_negative = true) {
     if ($create_negative) {
         // Get all (positive and negative) notifications stored for this event (more than one are possible for each event)
         $event_notifications = eF_getTableData("event_notifications", "*", "active = 1 AND (event_type = '".$this -> event['type'] ."' OR event_type = '".(-1) * $this -> event['type'] ."')");
     } else {
         // Get all notifications stored for exactly this event (only positive or negative though more than one are possible for each event)
        $event_notifications = eF_getTableData("event_notifications", "*", "active = 1 AND (event_type = '".$this -> event['type'] ."')");
     }
  if (sizeof($event_notifications)) {
   // Form each one and append it to the notifications queue
      $notifications_to_send = array();
      foreach ($event_notifications as $event_notification) {
       // Check whether the triggered event satisfies the conditions to be sent as an announcement
       $conditions = unserialize($event_notification['send_conditions']);
       $conditions_passed = true;
       foreach ($conditions as $field => $value) {
        // A value of 0 means any* (any lesson, test, content etc)
        if ($value != 0) {
         if ($this -> event['lessons_ID'] != $value && $this -> event['entity_ID'] != $value) {
          $conditions_passed = false;
          break;
         }
        }
        $conditions_passed = true;
       }
       // If all conditions are satisfied (or no conditions exist)
       if ($conditions_passed) {
        // Set type - entity field: denoting the type of the event ."_". the ID of the involved entity (lesson, test, forum etc)
           if ($this -> event['entity_ID']) {
         $event_notification['id_type_entity'] = $event_notification['id'] . "_" . $event_notification['event_type'] . "_" . $this -> event['entity_ID'];
        } else if ($this -> event['lessons_ID']) {
         $event_notification['id_type_entity'] = $event_notification['id'] . "_" . $event_notification['event_type'] . "_" . $this -> event['lessons_ID'];
        } else {
         $event_notification['id_type_entity'] = $event_notification['id'] . "_" . $event_notification['event_type'] . "_";
        }
        // Check whether this is of a NOT-event
        if ($event_notification['event_type'] < 0 || $replace_notification) {
         $event_notification['event_type'] = (-1) * $event_notification['event_type'];
         // in that case delete the corresponding record in the table (if such exists)
         eF_deleteTableData("notifications", "id_type_entity= '".$event_notification['id_type_entity'] . "' AND recipient = '". $this -> event['users_LOGIN'] ."'");
        }
        // Set event notification recipients
        if ($event_notification['send_recipients'] == EfrontNotification::TRIGGERINGUSER) {
         $event_notification['send_conditions'] = "";
         $event_notification['recipient'] = $this -> event['users_LOGIN'];
        } else if ($event_notification['send_recipients'] == EfrontNotification::ALLSYSTEMUSERS) {
         $event_notification['send_conditions'] = "N;";
         $event_notification['recipient'] = "";
        } else if ($event_notification['send_recipients'] == EfrontNotification::SYSTEMADMINISTRATOR) {
         $event_notification['send_conditions'] = serialize(array("user_type" => "administrator"));
         $event_notification['recipient'] = "";
        } else if ($event_notification['send_recipients'] == EfrontNotification::ALLLESSONUSERS) {
         $event_notification['send_conditions'] = serialize(array("lessons_ID" => $this -> event['lessons_ID']));
         $event_notification['recipient'] = "";
        } else if ($event_notification['send_recipients'] == EfrontNotification::LESSONPROFESSORS) {
         $event_notification['send_conditions'] = serialize(array("lessons_ID" => $this -> event['lessons_ID'],
                        "user_type" => "professor"));
         $event_notification['recipient'] = "";
        } else if ($event_notification['send_recipients'] == EfrontNotification::COURSEPROFESSORS) {
         $event_notification['send_conditions'] = serialize(array("courses_ID" => $this -> event['lessons_ID'],
                        "user_type" => "professor"));
         $event_notification['recipient'] = "";
        } else if ($event_notification['send_recipients'] == EfrontNotification::ALLCOURSEUSERS) {
         $event_notification['send_conditions'] = serialize(array("courses_ID" => $this -> event['lessons_ID']));
         $event_notification['recipient'] = "";
        } else if ($event_notification['send_recipients'] == EfrontNotification::LESSONUSERSNOTCOMPLETED) {
         $event_notification['send_conditions'] = serialize(array("lessons_ID" => $this -> event['lessons_ID'],
                        "completed" => "0"));
         $event_notification['recipient'] = "";
        } else if ($event_notification['send_recipients'] == EfrontNotification::EXPLICITLYSEL) {
         if (isset($this -> event['explicitly_selected'])) {
          // General case - set field "explicitly_selected" in the triggerEvent fields
          if (!is_array($this -> event['explicitly_selected'])) {
           $this -> event['explicitly_selected'] = array($this -> event['explicitly_selected']);
          }
          $event_notification['send_conditions'] = serialize(array ("users_login" => $this -> event['explicitly_selected']));
          $event_notification['recipient'] = "";
         } else {
          // This special treatment is used for surveys - so that all members of the survey will get the notification when the time of dispatch comes
          $event_notification['send_conditions'] = serialize(array("entity_ID" => $this -> event['entity_ID'],
                         "entity_category" => $event_types[$event_notification['event_type']]['category']));
          $event_notification['recipient'] = "";
         }
        }
        /*

	    			// Special treatment due to explicity recipient selection

	    			if ($this -> event['type'] == EfrontEvent::NEW_SURVEY) {

	    				$event_notification['send_conditions'] = serialize(array("surveys_ID" => $this -> event['entity_ID']));

	    				$event_notification['recipient'] = "";

	    			}

					*/
        //@TODO unite with upper
        // Format the message on the first layer: replacing event specific information now
        // Note: Recipient's specific information will be first replaced in layer 2 (before sending)
        $template_formulations = $this -> createSubstitutionsArray($event_types, $event_notification['send_recipients']);
        $subject = eF_formulateTemplateMessage($event_notification['subject'], $template_formulations);
        $message = eF_formulateTemplateMessage($event_notification['message'], $template_formulations);
        $html_message = $event_notification['html_message'];
        // Create a single array to implode it and insert it at once in the notifications queue table
        //
        if ($event_notification['send_immediately']) {
         $timestamp = 0;
         $_SESSION['send_next_notifications_now'] = 1;
        } else {
         $timestamp = $this -> event['timestamp'] + ($event_notification['after_time']?$event_notification['after_time']:0);
        }
        $notifications_to_send[] = array('timestamp' => $timestamp,
                                         'id_type_entity' => $event_notification['id_type_entity'],
                                         'send_interval' => 0, //changed from $event_notification['after_time']
                                         'send_conditions' => $event_notification['send_conditions'],
                                         'recipient' => $this -> event['users_LOGIN'],
                                         'subject' => $subject,
                                         'message' => $message,
                                         'html_message' => $html_message);
        //$notifications_to_send[] = $timestamp. "','". $event_notification['id_type_entity'] ."','" .$event_notification['after_time']. "', '" .$event_notification['send_conditions']."','". $event_notification['recipient']. "', '".$subject. "', '".$message. "', '".$html_message;
       }
      }
      if (sizeof($notifications_to_send)) {
       //eF_execute("INSERT INTO notifications (timestamp, id_type_entity, send_interval, send_conditions, recipient, subject, message, html_message) VALUES ('". implode("'),('", $notifications_to_send) . "')");
       eF_insertTableDataMultiple("notifications", $notifications_to_send);
      }
     }
    }
    /**

     * Create new event

     *

     * Create a new event based on the specified $fields

     * <br/>Example:

     * <code>

     * $fields = array('name' => 'new event', 'languages_NAME' => 'english');

     * $event = EfrontEvent :: logEvent($fields);

     * </code>

     *

     * @param array $fields The new fields

     * @return EfrontEvent the new event

     * @since 3.6.0

     * @access public

     */
    public static function logEvent($fields) {
     if ($fields['type'] == EfrontEvent::PROJECT_EXPIRY) {
      eF_deleteTableData("events", "lessons_ID = ". $fields['lessons_ID'] . " AND type = ".EfrontEvent::PROJECT_EXPIRY . " AND entity_ID = " . $fields['entity_ID']);
     }
        if (!isset($fields['type'])) {
            throw new EfrontEventException(_NOEVENTCODEDEFINED, EfrontEventException::NOEVENTCODE_DEFINED);
        }
        //These are the mandatory fields. In case one of these is absent, fill it in with a default value
        if (!isset($fields['users_LOGIN'])) {
            $fields['users_LOGIN'] = $GLOBALS['currentUser'] -> user['login'];
            $fields['users_name'] = $GLOBALS['currentUser'] -> user['name'];
            $fields['users_surname'] = $GLOBALS['currentUser'] -> user['surname'];
        }
        if (!isset($fields['users_name']) || !isset($fields['users_surname'])) {
            $users_id = eF_getTableData("users", "name, surname", "login = '".$fields['users_LOGIN']."'");
            $fields['users_name'] = $users_id[0]['name'];
            $fields['users_surname'] = $users_id[0]['surname'];
        }
        if (!isset($fields['timestamp'])) {
         $fields['timestamp'] = time();
        }
        // Allow multiple lesson ids for each event
        if (isset($fields['lessons_ID']) && is_array($fields['lessons_ID'])) {
            $event_lessons = eF_getTableData("lessons", "id, name" , "id in (".implode(",", $fields['lessons_ID']).")");
            $result = true;
            foreach ($event_lessons as $lesson) {
          $fields['lessons_ID'] = $lesson['id'];
          $fields['lessons_name'] = $lesson['name'];
          $result = $result & eF_insertTableData("events", $fields);
            }
            return $result;
        } else {
            // Else just a single event
         //!isset($fields['lessons_ID'])     ? $fields['lessons_ID']      = $GLOBALS['currentLesson'] -> lesson['id'] : null;
         //!isset($fields['lessons_name'])   ? $fields['lessons_name']    = $GLOBALS['currentLesson'] -> lesson['name'] : null;
         unset($fields['explicitly_selected']);
         return eF_insertTableData("events", $fields);
        }
        //EfrontSearch :: insertText($fields['name'], $newId, "events", "title");
        // Insert the corresponding lesson skill to the skill and lesson_offers_skill tables
        // Automatic skill generation only for the educational version
        /*

#ifdef EDUCATIONAL

	        $eventSkillId = eF_insertTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFEVENT . " ". $fields['name'], "categories_ID" => -1));

	        eF_insertTableData("module_hcd_event_offers_skill", array("events_ID" => $newId, "skill_ID" => $eventSkillId));

#endif

		*/
    }
   /**

     * Get event message

     *

     * This function creates the message string for this event

     * according to its type and its information

     *

     * <br/>Example:

     * <code>

     * $event = new EfrontEvent(5);       //create object for event with id 5

     * $event -> createMessage();

     * echo $event -> event['message'];

     * </code>

     *

     * @param array with all modules to optimize module message printing

     * @returns the message value also set to the $this -> event['message'] field

     * @since 3.6.0

     * @access public

     */
    public function createMessage($modulesArray = false) {
     if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_EVENTS == 0) {
      return array();
     }
     global $currentUser;
     // Module related code
     if ($this -> event['type'] >= EfrontEvent::MODULE_BASE_TYPE_CODE) {
      $className = $this -> event['entity_ID'];
      if (isset($modulesArray[$className])) {
       $data = array();
       if ($this -> event['entity_name'] != '') {
        $data = unserialize($this -> event['entity_name']);
       }
       foreach ($this -> event as $field => $value) {
        $data[$field] = $value;
       }
          $this -> event['message'] = $modulesArray[$className] -> getEventMessage((integer)$this -> event['type'] - EfrontEvent::MODULE_BASE_TYPE_CODE, $data);
      }
         if (! $this -> event['message']) {
          $this -> event['message'] = _UNREGISTEREDEVENT. " " . _FORTHEMODULE . " '" .$className. "'";
         }
     } else {
      // Basic system event codes
      // All excluded events are not of the form: The user did sth. For example: Project X expired
      if ($this -> event['type'] != EfrontEvent::PROJECT_EXPIRY && $this -> event['type'] != EfrontEvent::LESSON_PROGRAMMED_EXPIRY && $this -> event['type'] != EfrontEvent::LESSON_PROGRAMMED_START ) {
          //changed to $_SESSION['s_type'] to work for different roles between lessons
       $this -> event['message'] = _NAMEARTICLE . " <b><a  href = \"".$_SESSION['s_type'].".php?ctg=social&op=show_profile&user=".$this->event['users_LOGIN']. "&popup=1\" onclick = \"eF_js_showDivPopup('" . _USERPROFILE . "', 1)\"  target = \"POPUP_FRAME\"> ".formatLogin($this -> event['users_LOGIN'])."</a></b> ";
      }
         if ($this -> event['type'] == EfrontEvent::SYSTEM_JOIN) {
          $this -> event['message'] .= _HASJOINEDTHESYSTEM;
         } else if ($this -> event['type'] == EfrontEvent::SYSTEM_VISITED) {
          $this -> event['message'] .= _VISITEDTHESYSTEM;
         } else if ($this -> event['type'] == EfrontEvent::SYSTEM_FORGOTTEN_PASSWORD) {
             $this -> event['message'] .= _HASFORGOTTENHISPASSWORD;
   } else if ($this -> event['type'] == EfrontEvent::SYSTEM_REMOVAL) {
    $this -> event['message'] .= _HASBEENREMOVEDFROMTHESYSTEM;
         } else if ($this -> event['type'] == EfrontEvent::LESSON_ACQUISITION_AS_STUDENT) {
             $this -> event['message'] .= _WASASSIGNEDTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::LESSON_ACQUISITION_AS_PROFESSOR) {
             $this -> event['message'] .= _WILLBETEACHINGLESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::LESSON_VISITED) {
             $this -> event['message'] .= _VISITEDLESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::LESSON_REMOVAL) {
             $this -> event['message'] .= _NOLONGERATTENDSLESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::LESSON_COMPLETION) {
             $this -> event['message'] .= _HASCOMPLETEDLESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::PROJECT_SUBMISSION) {
             $this -> event['message'] .= _SUBMITTEDPROJECT . " <b>" . $this -> event['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::PROJECT_CREATION) {
             $this -> event['message'] .= _HASCREATEDPROJECT . " <b>" . $this -> event['entity_name'] ."</b> " . _FORTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::LESSON_PROGRAMMED_START) {
             $this -> event['message'] .= _SCHEDULEDSTARTOFLESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::LESSON_PROGRAMMED_EXPIRY) {
             $this -> event['message'] .= _SCHEDULEDEXPIRYOFLESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::PROJECT_EXPIRY) {
             $this -> event['message'] .= _THEPROJECT . " <b>" . $this -> event['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b> " . _HASEXPIRED;
         } else if ($this -> event['type'] == EfrontEvent::NEW_LESSON_ANNOUNCEMENT) {
             $this -> event['message'] .= _HASPUBLISHEDTHEANNOUNCEMENT. " <b>" . $this -> event['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b> ";
         } else if ($this -> event['type'] == EfrontEvent::SYSTEM_NEW_PASSWORD_REQUEST) {
    $this -> event['message'] .= _HASASKEDFORANEWPASSWORD;
         } else if ($this -> event['type'] == EfrontEvent::SYSTEM_REGISTER) {
    $this -> event['message'] .= _WASREGISTEREDINTOTHESYSTEM;
   } else if ($this -> event['type'] == EfrontEvent::SYSTEM_ON_EMAIL_ACTIVATION) {
    $this -> event['message'] .= _ACTIVATEDHISACCOUNTWITHEACTIVATIONMAIL;
   } else if ($this -> event['type'] == EfrontEvent::SYSTEM_USER_DEACTIVATE) {
    $this -> event['message'] .= _WASDEACTIVATEDFROMTHESYSTEM;
         // For courses we have lessons_name -> courses_name
         } else if ($this -> event['type'] == EfrontEvent::COURSE_ACQUISITION_AS_STUDENT) {
             $this -> event['message'] .= _WASASSIGNEDTHECOURSE . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::COURSE_ACQUISITION_AS_PROFESSOR) {
             $this -> event['message'] .= _WILLBETEACHINGCOURSE . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::COURSE_COMPLETION) {
             $this -> event['message'] .= _HASCOMPLETEDCOURSE . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::COURSE_REMOVAL) {
             $this -> event['message'] .= _NOLONGERATTENDSCOURSE . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::COURSE_PROGRAMMED_START) {
             $this -> event['message'] .= _SCHEDULEDSTARTOFCOURSE . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::COURSE_PROGRAMMED_EXPIRY) {
             $this -> event['message'] .= _SCHEDULEDEXPIRYOFCOURSE . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::TEST_CREATION) {
          $this -> event['message'] .= _CREATEDTHETEST . " <b>" . $this -> event['entity_name'] ."</b> " . _FORTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::CONTENT_MODIFICATION) {
             $this -> event['message'] .= _HASMODIFIEDUNIT . " <b>" . $this -> event['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::CONTENT_CREATION) {
             $this -> event['message'] .= _HASCREATEDUNIT . " <b>" . $this -> event['entity_name'] ."</b> " . _FORTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
            } else if ($this -> event['type'] == EfrontEvent::CONTENT_COMPLETION) {
             $this -> event['message'] .= _HASCOMPLETEDUNIT . " <b>" . $this -> event['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::NEW_SYSTEM_ANNOUNCEMENT) {
          $this -> event['message'] .= _HASPUBLISHEDTHEANNOUNCEMENT. " <b>" . $this -> event['entity_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::NEW_SURVEY) {
             $this -> event['message'] .= _HASPUBLISHEDSURVEY . " <b>" . str_replace("</p>", "", str_replace("<p>", "", $this -> event['entity_name'])) ."</b> " . _FORTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::NEW_COMMENT_WRITING) {
             $this -> event['message'] .= _WROTEACOMMENTFORUNIT . " <b>" . $this -> event['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::TEST_START) {
             $this -> event['message'] .= _STARTEDTEST . " <b>" . $this -> event['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::TEST_COMPLETION) {
             $this -> event['message'] .= _COMPLETEDTEST . " <b>" . $this -> event['entity_name'] ."</b> " . _OFTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::NEW_FORUM) {
             $this -> event['message'] .= _CREATEDTHENEWFORUM . " <b>" . $this -> event['entity_name'] ."</b> " . _FORTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::NEW_TOPIC) {
             $this -> event['message'] .= _CREATEDTHENEWTOPIC . " <b>" . $this -> event['entity_name'] ."</b> " . _FORTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::NEW_POLL) {
             $this -> event['message'] .= _CREATEDTHENEWPOLL . " <b>" . $this -> event['entity_name'] ."</b> " . _FORTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::NEW_FORUM_MESSAGE_POST) {
             $this -> event['message'] .= _POSTEDTHENEWMESSAGE . " <b>" . $this -> event['entity_name'] ."</b> " . _INTHEFORUMOFTHELESSON . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::STATUS_CHANGE) {
             $this -> event['message'] .= _CHANGEDHISHERPROFILE;
         } else if ($this -> event['type'] == EfrontEvent::AVATAR_CHANGE) {
             $this -> event['message'] .= _CHANGEDHISHERAVATARPICTURE;
         } else if ($this -> event['type'] == EfrontEvent::PROFILE_CHANGE) {
             $this -> event['message'] .= _CHANGEDHISHERPROFILE;
         } else if ($this -> event['type'] == EfrontEvent::NEW_PROFILE_COMMENT_FOR_OTHER) {
             $this -> event['message'] .= _COMMENTEDONTHEPROFILEOF;
             // Here check whether this is your own profile or not
             if ($this -> event['entity_ID'] != $currentUser -> user['login']) {
              $this -> event['message'] .= " <b><a  href = \"".$currentUser -> getType().".php?ctg=social&op=show_profile&user=".$this->event['entity_ID']. "&popup=1\" onclick = \"eF_js_showDivPopup('" . _USERPROFILE . "', 1)\"  target = \"POPUP_FRAME\"> ". $this -> event['entity_name']. "</a></b> ";
             } else {
              $this -> event['message'] .= " <b>". $this->event['entity_name'] . "</b>";
             }
         } else if ($this -> event['type'] == EfrontEvent::NEW_PROFILE_COMMENT_FOR_SELF) {
          $this -> event['message'] .= _COMMENTEDONHISHEROWNPROFILE;
         } else if ($this -> event['type'] == EfrontEvent::DELETE_PROFILE_COMMENT_FOR_SELF) {
          $this -> event['message'] .= _DELETEDACOMMENTFROMHISHEROWNPROFILE;
         } else if ($this -> event['type'] == EfrontEvent::NEW_POST_FOR_LESSON_TIMELINE_TOPIC) {
          $topic_post = unserialize($this -> event['entity_name']);
          $this -> event['message'] .= _POSTEDFORLESSONTOPIC . " <b>" . $topic_post['topic_title'] . "</b> " . _THEPOST . ": " . $topic_post['data'];
          if ($this -> event['users_LOGIN'] == $GLOBALS['currentUser'] -> user['login']) {
           $this -> event['editlink'] = "<a href='".$_SESSION['s_type'] . ".php?ctg=social&op=timeline&lessons_ID=" . $this -> event['lessons_ID'] . "&post_topic=" . $this -> event['entity_ID'] . "&action=change&popup=1&id=" . $topic_post['post_id'] ."' onclick = 'eF_js_showDivPopup(\""._EDITMESSAGEFORLESSONTIMELINETOPIC. "\", 1)'  target = 'POPUP_FRAME'><img src='images/16x16/edit.png' border='0' alt = '"._EDITMESSAGEFORLESSONTIMELINETOPIC."' title='"._EDITMESSAGEFORLESSONTIMELINETOPIC."' /></a>";
           $this -> event['deletelink'] = "<a href='".$_SESSION['s_type'] . ".php?ctg=social&op=timeline&lessons_ID=" . $this -> event['lessons_ID'] . "&post_topic=" . $this -> event['entity_ID'] . "&action=delete&id=" . $topic_post['post_id']."'><img src='images/16x16/error_delete.png' border='0' alt = '"._DELETEMESSAGEFORLESSONTIMELINETOPIC."' title='"._DELETEMESSAGEFORLESSONTIMELINETOPIC."' /></a>";
          }
         } else if ($this -> event['type'] == EfrontEvent::DELETE_POST_FROM_LESSON_TIMELINE) {
          $this -> event['message'] .= _DELETEDAPOSTFORLESSONTOPIC . " " . $this -> event['entity_name'];
         } else if ($this -> event['type'] == EfrontEvent::HCD_NEW_BRANCH) {
          $this -> event['message'] .= _CREATEDTHEBRANCH . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::HCD_REMOVE_BRANCH) {
          $this -> event['message'] .= _DELETEDTHEBRANCH. " <b>" . $this -> event['lessons_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::HCD_NEW_JOB_ASSIGNMENT) {
    $this -> event['message'] .= _WASASSIGNEDTHEJOB . " <b>" . $this -> event['entity_name'] ."</b>" . _ATBRANCH . " <b>" . $this -> event['lessons_name'] ."</b>";
   } else if ($this -> event['type'] == EfrontEvent::HCD_REMOVE_JOB_ASSIGNMENT) {
    $this -> event['message'] .= _WASREMOVEDFROMJOB . " <b>" . $this -> event['entity_name'] ."</b>" . _ATBRANCH . " <b>" . $this -> event['lessons_name'] ."</b>";
   } else if ($this -> event['type'] == EfrontEvent::HCD_FIRED) {
    $this -> event['message'] .= _WASFIRED;
   } else if ($this -> event['type'] == EfrontEvent::NEW_ASSIGNMENT_TO_GROUP) {
    $this -> event['message'] .= _WASASSIGNEDTOGROUP . " <b>" . $this -> event['entity_name'] ."</b>";
   } else if ($this -> event['type'] == EfrontEvent::REMOVAL_FROM_GROUP) {
    $this -> event['message'] .= _WASREMOVEDFROMGROUP . " <b>" . $this -> event['entity_name'] ."</b>";
   } else if ($this -> event['type'] == EfrontEvent::NEW_PAYPAL_PAYMENT) {
       $this -> event['message'] .= _PAYEDWITHPAYPAL . " <b>" . $this -> event['entity_name'] ."</b>";
   } else if ($this -> event['type'] == EfrontEvent::NEW_BALANCE_PAYMENT) {
                $this -> event['message'] .= _PAYEDWITHBALANCE . " <b>" . $this -> event['entity_name'] ."</b>";
   } else if ($this -> event['type'] == EfrontEvent::COUPON_USAGE) {
       $this -> event['message'] .= _USEDCOUPON . " <b>" . $this -> event['entity_name'] ."</b>";
   } else if ($this -> event['type'] == EfrontEvent::HCD_NEW_SKILL) {
    $this -> event['message'] .= _WASASSIGNEDSKILL . " <b>" . $this -> event['entity_name'] ."</b>";
   } else if ($this -> event['type'] == EfrontEvent::HCD_REMOVE_SKILL) {
    $this -> event['message'] .= _DOESNOTHAVEANYMORESKILL . " <b>" . $this -> event['entity_name'] ."</b>";
   } else if ($this -> event['type'] == EfrontEvent::HCD_SKILL_EDIT) {
    $this -> event['message'] .= _HADHISSKILLEDITEDTO . " <b>" . $this -> event['entity_name'] ."</b>";
   } else if ($this -> event['type'] == EfrontEvent::HCD_HIRED) {
    $this -> event['message'] .= _WASHIRED;
   } else if ($this -> event['type'] == EfrontEvent::HCD_LEFT) {
    $this -> event['message'] .= _HASLEFTHECOMPANY;
   } else if ($this -> event['type'] == EfrontEvent::HCD_WAGE_CHANGE) {
    $this -> event['message'] .= _HASHADHISWAGECHANGETO ." <b>" . $this -> event['entity_name'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::COURSE_CERTIFICATE_ISSUE) {
             $this -> event['message'] .= _HASCERTIFICATED . " <b>" . $this -> event['lessons_name'] ."</b> " . _WITHGRADE ." <b>". $this -> event['entity_name'] ."</b> ". _WITHKEY . " <b> ". $this -> event['entity_ID'] ."</b>";
         } else if ($this -> event['type'] == EfrontEvent::COURSE_CERTIFICATE_REVOKE) {
             $this -> event['message'] .= _HASLOSTCERTIFICATE . " <b>" . $this -> event['lessons_name'] ."</b>";
         } else {
           return false;
         }
     }
        $this -> event['time'] = eF_convertIntervalToTime(time() - $this->event['timestamp'], true). ' '._AGO;
        return $this -> event['message'];
    }
}
