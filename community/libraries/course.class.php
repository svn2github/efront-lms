<?php
/**
 * File for courses
 *
 * @package eFront
 */

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}


/**
 * Course exceptions
 *
 * This class extends Exception to provide the exceptions related to courses
 * @package eFront
 * @since 3.5.0
 *
 */
class EfrontCourseException extends Exception
{
 const COURSE_NOT_EXISTS = 251;
 const INVALID_ID = 252;
 const MAX_USERS_LIMIT = 253;
 const DATABASE_ERROR = 254;
 const INVALID_PARAMETER = 255;
 const PARTIAL_IMPORT = 256;
 const INVALID_USER_TYPE = 257;
 const COURSE_NOT_EMPTY = 258;
 const GENERAL_ERROR = 299;
 const INVALID_LOGIN = 300;
}

/**
 * This class represents a course in eFront
 *
 * @package eFront
 * @since 3.5.0
 */
class EfrontCourse
{
 /**
	 * The maximum length of a course name
	 *
	 * @var string
	 * @since 3.6.1
	 */
 const MAX_NAME_LENGTH = 150;

 /**
	 * The limit for a mass operation, such as adding users to a course
	 *
	 * @var int
	 * @since 3.6.3
	 */
 const MAX_MASS_OPERATION_SIZE = 50;

 /**
	 * The course variable
	 *
	 * @since 3.5.0
	 * @var array
	 * @access public
	 */
 public static $course = array();

 /**
	 * The course users
	 *
	 * @since 3.5.0
	 * @var array
	 * @access public
	 */
 public $users = false;

 /**
	 * The course rules
	 *
	 * @since 3.5.0
	 * @var array
	 * @access public
	 */
 public $rules = array();

 /**
	 * Default course options
	 *
	 * @since 3.5.2
	 * @var array
	 * @access public
	 */
 public $options = array(
    'recurring' => 0,
    'recurring_duration' => 0,
        'auto_complete' => 1,
        'auto_certificate' => 0,
        'certificate' => '',
        'certificate_tpl_id' => 0,
        'certificate_tpl_id_rtf' => 0,
        'certificate_export_method' => 'xml',
    //'course_code' => '',
        'duration' => 0,
        'training_hours' => '',
        'start_date' => '',
    'end_date' => ''
   );

 /**
	 * Initialize course
	 *
	 * This function creates the course instance based on the
	 * given course id.
	 * <br/>Example:
	 * <code>
	 * $course = new EfrontCourse(5);       //create object for course with id 5
	 * </code>
	 *
	 * @param mixed $course The course id or the course array
	 * @since 3.5.0
	 * @access public
	 */
 function __construct($source) {
  $this -> initializeDataFromSource($source);
  $this -> initializeRules();
  $this -> initializeOptions();
  $this -> buildPriceString();
 }

 /**
	 * Initialize course data based on the passed parameter. If the parameter is an id, then
	 * a db query takes place in order to retrive course values. Otherwise, if it's an array
	 * it is used for the course values.
	 *
	 * @param mixed $course A course id or an array with course values
	 * @since 3.6.1
	 * @access private
	 */
 private function initializeDataFromSource($source) {
  if (is_array($source)) {
   $this -> course = $source;
  } elseif (!$this -> validateId($source)) {
   throw new EfrontCourseException(_INVALIDID, EfrontCourseException :: INVALID_ID);
  } else {
   $source = eF_getTableData("courses", "*", "id = $source");
   if (empty($source)) {
    throw new EfrontCourseException(_COURSEDOESNOTEXIST, EfrontCourseException :: COURSE_NOT_EXISTS);
   }
   $this -> course = $source[0];
  }

  if (!$this -> course['directions_ID']) {
   $this -> setCategoryId();
  }
 }

 /**
	 * Set a category (direction) id, in case it's missing.
	 * If this course is an instance, set it to be the same as the originating course. Otherwise,
	 * set it to be the first active category
	 *
	 * @since 3.6.3
	 * @access private
	 */
 private function setCategoryId() {
  if ($this -> course['instance_source']) {
   $parentCourse = new EfrontCourse($this -> course['instance_source']);
   if ($parentCourse -> course['directions_ID']) {
    $this -> course['directions_ID'] = $parentCourse -> course['directions_ID'];
    $this -> persist();
   }
  } else {
   $result = eF_getTableData("directions", "id", "active=1", "", "", 1);
   if (!empty($result)) {
    $this -> course['directions_ID'] = $result[0]['id'];
    $this -> persist();
   }
  }
 }

 /**
	 * Initialize course rules, by unserializing the stored rules array
	 *
	 * @since 3.6.1
	 * @access private
	 */
 private function initializeRules() {
  $this -> validateSerializedArray($this -> course['rules']) OR $this -> course['rules'] = $this -> sanitizeSerialized($this -> course['rules']);
  $this -> rules = unserialize($this -> course['rules']);
 }

 /**
	 * Initialize course options, by unserializing the stored options array
	 *
	 * @since 3.6.1
	 * @access private
	 */
 private function initializeOptions() {
  $this -> validateSerializedArray($this -> course['options']) OR $this -> course['options'] = $this -> sanitizeSerialized($this -> course['options']);
  $options = unserialize($this -> course['options']);
  $newOptions = array_diff_key($this -> options, $options); //$newOptions are course options that were added to the EfrontCourse object AFTER the lesson options serialization took place
  $this -> options = $options + $newOptions; //Set course options
 }

 /**
	 * Build the price string. This function takes the course price and
	 * creates a human-readable version, based on whether it is a one-time
	 * or a recurring price
	 *
	 * @since 3.6.1
	 * @access private
	 */
 private function buildPriceString() {
  if ($this -> validateFloat($this -> course['price'])) { //Create the string representing the course price
   $this -> options['recurring'] ? $recurring = array($this -> options['recurring'], $this -> options['recurring_duration']) : $recurring = false;
   $this -> course['price_string'] = formatPrice($this -> course['price'], $recurring);
  } else {
   $this -> course['price_string'] = formatPrice(0);
  }
 }

 /**
	 * Check whether the course should display in the catalog
	 *
	 * This function returns false if this course should not appear in the catalog at all.
	 * Otherwise, it returns the id to which the catalog entry should point
	 *
	 * @return mixed Either false or the id of the course that the catalog entry points to
	 * @since 3.6.3
	 * @access public
	 */
 public function shouldDisplayInCatalog() {
  if ($this -> course['show_catalog']) {
   return $this -> course['id'];
  } else {
   $instances = $this -> getInstances();
   if (!empty($instances)) {
    foreach ($instances as $instance) {
     if ($instance -> course['show_catalog']) {
      return $instance -> course['id'];
     }
    }
   } else {
    return false;
   }
  }

 }

 /**
	 * Return an array of EfrontLesson objects that belong to this course, based
	 * on the specified constraints
	 *
	 * @param array $constraints Database constraints
	 * @return array The course lessons
	 * @since 3.6.3
	 * @access public
	 */
 public function getCourseLessons($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontCourse :: convertLessonConstraintsToSqlParameters($constraints);

  $from = "lessons_to_courses lc, lessons l";
  $where[] = "l.archive = 0 and l.course_only=1 and l.id=lc.lessons_ID and courses_ID=".$this -> course['id'];
  $result = eF_getTableData($from, "lc.start_date, lc.end_date, lc.previous_lessons_ID, l.*",
  implode(" and ", $where), $orderby, false, $limit);

  $result = $this -> sortLessons($result);
  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontCourse :: convertDatabaseResultToLessonObjects($result);
  } else {
   return EfrontCourse :: convertDatabaseResultToLessonArray($result);
  }

 }

 /**
	 * Count the number of lessons in the course, based on the specified constraints
	 *
	 * @param array $constraints Database constraints
	 * @return int The total course lessons
	 * @since 3.6.3
	 * @access public
	 */
 public function countCourseLessons($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);

  list($where, $limit, $orderby) = EfrontCourse :: convertLessonConstraintsToSqlParameters($constraints);
  $from = "lessons_to_courses lc, lessons l";
  $where[] = "l.archive = 0 and l.course_only=1 and l.id=lc.lessons_ID and courses_ID=".$this -> course['id'];
  $result = eF_countTableData($from, "l.id",
  implode(" and ", $where));

  return $result[0]['count'];
 }

 /**
	 * Experimental addition based on sorted table
	 */
 public function addCourseLessons($constraints = array()) {
  $lessons = $this -> getCourseLessonsIncludingUnassigned($constraints);
  $this -> addLessons($lessons);
 }

 /**
	 * Experimental removal based on sorted table
	 */
 public function removeCourseLessons($constraints = array()) {
  $lessons = $this -> getCourseLessons($constraints);
  $this -> removeLessons($lessons);
 }

 /**
	 * Return an array of EfrontLesson objects, based on the specified constraints. If any of the lessons
	 * is part of the course, then it has an extra field 'has_lesson' set to 1
	 *
	 * @param array $constraints Database constraints
	 * @return array All the lessons, with course lessons having has_lesson=1
	 * @since 3.6.3
	 * @access public
	 */
 public function getCourseLessonsIncludingUnassigned($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontCourse :: convertLessonConstraintsToSqlParameters($constraints);

  $from = "lessons l left outer join (select lessons_ID from lessons_to_courses where courses_ID='".$this -> course['id']."') r on l.id=r.lessons_ID ";
  $select = "l.*, r.lessons_ID is not null as has_lesson";
  $where[] = "l.course_only=1";
  $result = eF_getTableData($from, $select, implode(" and ", $where), $orderby, false, $limit);

  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontCourse :: convertDatabaseResultToLessonObjects($result);
  } else {
   return EfrontCourse :: convertDatabaseResultToLessonArray($result);
  }

 }

 /**
	 * Count the number of lessons in the course, based on the specified constraints, including unassigned
	 *
	 * @param array $constraints Database constraints
	 * @return int The total course lessons
	 * @since 3.6.3
	 * @access public
	 */
 public function countCourseLessonsIncludingUnassigned($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontCourse :: convertLessonConstraintsToSqlParameters($constraints);

  $from = "lessons l left outer join (select lessons_ID from lessons_to_courses where courses_ID='".$this -> course['id']."') r on l.id=r.lessons_ID ";
  $select = "l.id";
  $where[] = "l.course_only=1";
  $result = eF_countTableData($from, $select, implode(" and ", $where));

  return $result[0]['count'];
 }


 /**
	 * Get the schedule for this lesson, in this course
	 *
	 * @param mixed $lesson The lesson to get the schedule for
	 * @return array The lesson's schedule in the course
	 * @since 3.6.3
	 * @access public
	 */
 public function getLessonScheduleInCourse($lesson) {
  $lesson = EfrontLesson::convertArgumentToLessonObject($lesson);

  $result = eF_getTableData("lessons_to_courses", "start_date, end_date", "courses_ID=".$this -> course['id']." and lessons_ID=".$lesson -> lesson['id']);
  return $result[0];
 }

 /**
	 * Set the schedule for this lesson, in this course.
	 *
	 * @param mixed $lesson The lesson to set schedule for
	 * @param int $fromTimestamp A timestamp indicating when the lesson starts
	 * @param int $toTimestamp A timestamp indicating when the lesson ends
	 * @since 3.6.3
	 * @access public
	 */
 public function setLessonScheduleInCourse($lesson, $fromTimestamp, $toTimestamp) {
  $lesson = EfrontLesson::convertArgumentToLessonObject($lesson);

  $fields = array("start_date" => $fromTimestamp, "end_date" => $toTimestamp);
  $where = "courses_ID=".$this -> course['id']." and lessons_ID=".$lesson -> lesson['id'];
  self::persistCourseLessons($fields, $where);
 }

 /** Unset any schedule set for this lesson, in this course
	 *
	 * @param mixed $lesson The lesson to get the schedule for
	 * @return array The lesson's schedule in the course
	 * @since 3.6.3
	 * @access public
	 */
 public function unsetLessonScheduleInCourse($lesson) {
  $lesson = EfrontLesson::convertArgumentToLessonObject($lesson);

  $fields = array("start_date" => null, "end_date" => null);
  $where = "courses_ID=".$this -> course['id']." and lessons_ID=".$lesson -> lesson['id'];
  self::persistCourseLessons($fields, $where);
 }

 /**
	 * Sort course lessons, based on the succession built-in the database, if any
	 *
	 * @param array $result The course lessons array, as retrieved from the database
	 * @return array The course lessons array, sorted accordingly and with lesson ids as keys
	 * @since 3.6.1
	 * @access private
	 */
 private function sortLessons($result) {
  $previous = 0; //Previous is only used when no previous_lessons_ID is set
  $courseLessons = $previousValues = array();
  foreach ($result as $value) {
   $courseLessons[$value['id']] = $value;
   $previousValues[$value['id']] = $value['previous_lessons_ID'];
   $value['previous_lessons_ID'] !== false ? $previousLessons[$value['previous_lessons_ID']] = $value : $previousLessons[$previous] = $value;
   $previous = $value['id'];
  }

  if (array_sum($previousValues)) { //The special case where all previous values are 0, which is checked by array_sum, means that there is no specific ordering
   //Sorting algorithm, based on previous_lessons_ID. The algorithm is copied from EfrontContentTree :: reset() and is the same with the one applied for content. It is also used in questions order
   $node = $count = 0;
   $nodes = array(); //$count is used to prevent infinite loops
   while (sizeof($previousLessons) > 0 && isset($previousLessons[$node]) && $count++ < 1000) {
    $nodes[$previousLessons[$node]['id']] = $previousLessons[$node];
    $newNode = $previousLessons[$node]['id'];
    unset($previousLessons[$node]);
    $node = $newNode;
   }
   if (sizeof($nodes) != sizeof($courseLessons)) { //If the ordering is messed up for some reason.
    $nodes = $courseLessons;

    $fields = array("previous_lessons_ID" => 0);
    $where = "courses_ID=".$this -> course['id'];
    self::persistCourseLessons($fields, $where);
   }
  } else {
   $nodes = $courseLessons;
  }
  return $nodes;
 }

 /**
	 * Add lessons to course
	 *
	 * This function is used to add lessons to the current course
	 * <br/>Example:
	 * <code>
	 * $course -> addLessons(4);                        //Add lesson with id 4
	 * $course -> addLessons(array(4,5,6));             //Add lessons with ids 4,5,6
	 * </code>
	 *
	 * @param mixed $lessons Either a single lesson id, or an array of ids
	 * @return array The new list of course lessons
	 * @since 3.5.0
	 * @access public
	 */
 public function addLessons($lessons) {

  $lessonObjects = $this -> verifyLessonsList($lessons);
  $lastLessonId = $this -> getCourseLastLesson();
  $courseUsers = $this -> getUsers();

  $result = eF_getTableDataFlat("lessons_to_courses", "lessons_ID", "courses_ID=".$this -> course['id']); //We don't call getCourseLessons() because we need all and every lesson, so this is faster
  foreach ($lessonObjects as $key => $lesson) {
   if (!in_array($key, $result['lessons_ID'])) {
    eF_insertTableData("lessons_to_courses", array('courses_ID' => $this -> course['id'],
                                    'lessons_ID' => $key,
                  'previous_lessons_ID' => $lastLessonId));
    $this -> addCourseUsersToLesson($lesson, $courseUsers);





    $lastLessonId = $key;
   }
  }

  return EfrontCourse::convertLessonObjectsToArrays($this -> getCourseLessons());
 }

 /**
	 * Remove lessons from course
	 *
	 * This function is used to reove lessons from the current course
	 * <br/>Example:
	 * <code>
	 * $course -> removeLessons(4);                         //Remove lesson with id 4
	 * $course -> removeLessons(array(4,5,6));              //Remove lessons with ids 4,5,6
	 * </code>
	 *
	 * @param mixed $lessons Either a single lesson id, or an array of ids
	 * @return array The new list of course lessons
	 * @since 3.5.0
	 * @access public
	 */
 public function removeLessons($lessons) {

  $lessonObjects = $this -> verifyLessonsList($lessons);
  $previousLessons = $this -> getPreviousLessonsInCourse();
  $lessonsToCourses = $this -> countLessonsOccurencesInCourses();

  foreach ($lessonObjects as $key => $lesson) {

   $this -> removeLessonFromCourseRules($lesson);

   if ($lessonsToCourses[$key] == 1) { //Meaning that this lesson was only related to this course
    $lesson -> archiveLessonUsers(array_keys($this -> getUsers()));
   }





   eF_deleteTableData("lessons_to_courses", "courses_ID=".$this -> course['id']." and lessons_ID=".$key);

   $fields = array("previous_lessons_ID" => $previousLessons[$key]);
   $where = "courses_ID=".$this -> course['id']." and previous_lessons_ID=$key";
   self::persistCourseLessons($fields, $where);
  }

  return EfrontCourse::convertLessonObjectsToArrays($this -> getCourseLessons());
 }

 /**
	 * This function removes a lesson from the course's rules, which are rather complicated
	 *
	 * @param mixed $lesson The lesson to remove from course rules
	 * @since 3.6.3
	 * @access private
	 * @todo: Simplify course rules implementation
	 */
 private function removeLessonFromCourseRules($lesson) {
  $lesson = EfrontLesson::convertArgumentToLessonObject($lesson);

  unset($this -> rules[$lesson]); //Unset rules that have this lesson as source

  foreach ($this -> rules as $id => $rule) {
   foreach ($rule['lesson'] as $key => $value) {

    if ($value == $lesson) {
     unset($rule['lesson'][$key]);
     unset($rule['condition'][$key+1]);
    }

    if (sizeof($rule['lesson']) == 0) {
     unset($this -> rules[$id]);
    } else {
     if (sizeof($rule['condition']) == 0) {
      unset($rule['condition']);
     }
     $this -> rules[$id] = $rule;
    }
   }
   if ($this -> rules[$id]) {
    $this -> rules[$id]['lesson'] = array_values($this -> rules[$id]['lesson']);
    array_unshift($this -> rules[$id]['lesson'], 0);
    unset($this -> rules[$id]['lesson'][0]);
    if ($this -> rules[$id]['condition']) {
     $this -> rules[$id]['condition'] = array_values($this -> rules[$id]['condition']);
     array_unshift($this -> rules[$id]['condition'], 0);
     array_unshift($this -> rules[$id]['condition'], 0);
     unset($this -> rules[$id]['condition'][0]);
     unset($this -> rules[$id]['condition'][1]);
    }
   }
  }
  $this -> persist();
 }

 /**
	 * For each of the course's lesson, get its previous
	 *
	 * @return array The id of the previous lesson, for each lesson in the course
	 * @since 3.6.1
	 * @access private
	 */
 private function getPreviousLessonsInCourse() {
  $courseLessons = $this -> getCourseLessons();
  foreach ($courseLessons as $id => $lesson) {
   $previousLessons[$id] = $lesson->lesson['previous_lessons_ID'];
  }
  return $previousLessons;
 }

 /**
	 * Count how many occurences each lesson has in courses
	 *
	 * @return array The number of occurences in courses for each lesson
	 * @since 3.6.1
	 * @access private
	 */
 private function countLessonsOccurencesInCourses() {
  $result = eF_getTableDataFlat("lessons_to_courses lc", "lc.lessons_ID, count(lc.lessons_ID)", "", "", "lc.lessons_ID");
  $lessonsToCourses = array_combine($result['lessons_ID'], $result['count(lc.lessons_ID)']);
  return $lessonsToCourses;
 }



 /**
	 * Get the id of the last lesson in the course
	 *
	 * @return int The last lesson id
	 * @since 3.6.1
	 * @access private
	 */
 private function getCourseLastLesson() {
  $lastLesson = end($this -> getCourseLessons());
  if ($lastLesson) {
   $lastLessonId = $lastLesson->lesson['id'];
  } else {
   $lastLessonId = 0;
  }
  return $lastLessonId;
 }

 /**
	 * Verify the integrity of the lessons list and convert each one to
	 * an object, if it isn't already. The returned array has the lesson ids
	 * as keys.
	 *
	 * @param mixed $lessons An array of lesson objects or lesson ids, or a single lesson, or a single lesson object
	 * @return array The array of lesson objects, where keys are ids
	 * @since 3.6.1
	 * @access private
	 */
 private function verifyLessonsList($lessonsList) {
  is_array($lessonsList) OR $lessonsList = array($lessonsList);

  $newLessonsList = array();
  foreach ($lessonsList as $lesson) {
   ($lesson instanceof EfrontLesson) OR $lesson = new EfrontLesson($lesson);
   $newLessonsList[$lesson -> lesson['id']] = $lesson;
  }
  return $newLessonsList;
 }

 /**
	 * Verify the integrity of the course list and convert each one to
	 * an object, if it isn't already. The returned array has the courses ids
	 * as keys.
	 *
	 * @param mixed $courses An array of course objects, course ids, or arrays with course values (or single versions of all these)
	 * @return array The array of course objects, where keys are ids
	 * @since 3.6.1
	 * @access public
	 * @static
	 */
 public static function verifyCoursesList($coursesList) {
  is_array($coursesList) OR $coursesList = array($coursesList);

  $newCoursesList = array();
  foreach ($coursesList as $course) {
   ($course instanceof EfrontCourse) OR $course = new EfrontCourse($course);
   $newCoursesList[$course -> course['id']] = $course;
  }
  return $newCoursesList;
 }

 /**
	 * Add this course's users to the specified lesson
	 *
	 * @param EfrontLesson $lesson The lesson to add users to
	 * @since 3.6.1
	 * @access private
	 */
 private function addCourseUsersToLesson($lesson, $usersToAdd = false, $confirmed = true) {
  if (!$usersToAdd) {
   $usersToAdd = $this -> getUsers();
  }
  $users = $roles = array();

  foreach ($usersToAdd as $login => $user) {
   if ($user['user_type'] != 'administrator') {
    $users[] = $login;
    $roles[] = $user['role'];
   }
  }

  $lesson -> addUsers($users, $roles, $confirmed);
 }

 /**
	 * Insert the skill corresponding to this course: Every course is mapped to a skill like "Knowledge of that course"
	 * This insertion takes place when a course is changed from course_only to regular course
	 *
	 * <br/>Example:
	 * <code>
	 * $course -> insertCourseSkill();
	 * </code>
	 *
	 * @return the id of the newly created record in the module_hcd_course_offers_skill table or false if something went wrong
	 * @since 3.6.2
	 * @access public
	 */
 public function insertCourseSkill() {
  // If insertion of a self-contained course add the corresponding skill
  // Insert the corresponding course skill to the skill and course_offers_skill tables
  $courseSkillId = eF_insertTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFCOURSE . " ". $this -> course['name'], "categories_ID" => -1));

  // Insert question to course skill records for all course questions
  $questions = eF_getTableData("questions", "id", "lessons_ID in ('". implode("','", array_keys($this->getCourseLessons())) . "')");
  $insert_string = "";
  foreach ($questions as $question) {
   if ($insert_string != "") {
    $insert_string .= ",('" . $question['id']. "','" . $courseSkillId . "',2)";
   } else {
    $insert_string .= "('".$question['id']."','".$courseSkillId."',2)";
   }
  }


  if ($insert_string != "") {
   eF_executeNew("INSERT INTO questions_to_skills VALUES " . $insert_string);
  }

  return eF_insertTableData("module_hcd_course_offers_skill", array("courses_ID" => $this -> course['id'], "skill_ID" => $courseSkillId));
 }

 /**
	 * Get the skill corresponding to this course: Every course is mapped to a skill like "Knowledge of that course"
	 *
	 * <br/>Example:
	 * <code>
	 * $course_skill = $course -> getcourseSkill();
	 * </code>
	 *
	 * @return An array of the form [skill_ID] => [courses_ID, description, specification,skill_ID,categories_ID]
	 * @since 3.5.2
	 * @access public
	 */
 public function getCourseSkill() {
  return false;
 }
 /**
	 * Add the lesson's questions to the course's skill
	 *
	 * @param EfrontLesson $lesson The lesson to retrieve questions for
	 * @since 3.6.1
	 * @access private
	 */
 private function addLessonQuestionsToCourseSkill($lesson) {
  $lessonQuestions = eF_getTableDataFlat("questions", "id", "lessons_ID = ". $lesson ->lesson['id']);
  $courseSkill = $this -> getCourseSkill();
  // Get course specific skill
  foreach ($lessonQuestions['id'] as $questionId) {
   $fields[] = array("questions_id" => $questionId,
         "skills_ID" => $courseSkill['courses_ID'],
         "relevance" => 2);
  }
  eF_insertTableDataMultiple("questions_to_skills", $fields);
 }
 /**
	 * Remove the lesson's questions from the course's skill
	 *
	 * @param EfrontLesson $lesson The lesson to retrieve questions for
	 * @since 3.6.1
	 * @access private
	 */
 private function removeLessonQuestionsFromCourseSkill($lesson) {
  $lessonQuestions = eF_getTableDataFlat("questions", "id", "lessons_ID = ". $lesson ->lesson['id']);
  $courseSkill = $this -> getCourseSkill();
  if (!empty($lessonQuestions['id'])) {
   eF_deleteTableData("questions_to_skills", "questions_id IN ('".implode("','",$lessonQuestions['id'])."') AND skills_ID = ".$courseSkill['skill_ID']);
  }
 }
 /**
	 * Get course users
	 *
	 * This function is used to retrieve a list with the users
	 * that have this course, along with their declared type
	 * <br/>Example:
	 * <code>
	 * $course -> getUsers();
	 * </code>
	 *
	 * @param boolean $returnObjects Whether to return EfrontUser Objects
	 * @return array An array of users or EfrontUser objects
	 * @since 3.5.0
	 * @access public
	 * @todo: Replace with getCourseUsersXXX()
	 */
 public function getUsers($returnObjects = false, $constraints = array()) {
  if ($this -> users === false) {
   $this -> initializeUsers($constraints);
  }
  if ($returnObjects) {
   $users = array();
   if (is_array($this -> users)) {
    foreach ($this -> users as $key => $user) {
     $users[$key] = EfrontUserFactory :: factory($key);
     $users[$key] -> user = array_merge($users[$key]->user, $user); }
   }
   return $users;
  } else {
   return $this -> users;
  }
 }
 /**
	 * Get the course users that are students
	 *
	 * This function returns all the course users that their role is a student role
	 *
	 * @param boolean $returnObjects Whether to return objects
	 * @return mixed An array of users or EfrontUser objects
	 * @since 3.6.1
	 * @access public
	 * @todo: Replace with getCourseUsersXXX()
	 */
 public function getStudentUsers($returnObjects = false, $constraints = array()) {
  $courseUsers = $this -> getUsers($returnObjects, $constraints) OR $courseUsers = array();
  foreach ($courseUsers as $key => $value) {
   if ($value instanceOf EfrontUser) {
    $value = $value -> user;
   }
   if (!EfrontUser::isStudentRole($value['role'])) {
    unset($courseUsers[$key]);
   }
  }
  return $courseUsers;
 }
 /**
	 * Get the course users that are professors
	 *
	 * This function returns all the course users that their role is a professor role
	 *
	 * @param boolean $returnObjects Whether to return objects
	 * @return mixed An array of users or EfrontUser objects
	 * @since 3.6.1
	 * @access public
	 * @todo: Replace with getCourseUsersXXX()
	 */
 public function getProfessorUsers($returnObjects = false, $constraints = array()) {
  $courseUsers = $this -> getUsers($returnObjects, $constraints) OR $courseUsers = array();
  foreach ($courseUsers as $key => $value) {
   if ($value instanceOf EfrontUser) {
    $value = $value -> user;
   }
   if (!EfrontUser::isProfessorRole($value['role'])) {
    unset($courseUsers[$key]);
   }
  }
  return $courseUsers;
 }
 /**
	 * Check if the specified user has a 'student' role in the course
	 *
	 * @param mixed $user a login or an EfrontUser object
	 * @return boolean True if the user's role in the course is 'student'
	 * @since 3.6.1
	 * @access public
	 * @todo: Replace with getCourseUsersXXX()
	 */
 public function isStudentInCourse($user) {
  if ($user instanceOf EfrontUser) {
   $user = $user -> user['login'];
  }
  $roles = $this -> getPossibleCourseRoles();
  $courseUsers = $this -> getUsers();
  if (in_array($user, array_keys($courseUsers)) && $roles[$courseUsers[$user]['role']] == 'student') {
   return true;
  } else {
   return false;
  }
 }
 /**
	 * Check if the specified user has a 'professor' role in the course
	 *
	 * @param mixed $user a login or an EfrontUser object
	 * @return boolean True if the user's role in the course is 'professor'
	 * @since 3.6.1
	 * @access public
	 * @todo Implement using getCourseUsersXXX()
	 */
 public function isProfessorInCourse($user) {
  if ($user instanceOf EfrontUser) {
   $user = $user -> user['login'];
  }
  $roles = $this -> getPossibleCourseRoles();
  $courseUsers = $this -> getUsers();
  if (in_array($user, array_keys($courseUsers)) && $roles[$courseUsers[$user]['role']] == 'professor') {
   return true;
  } else {
   return false;
  }
 }
 /**
	 * Get course users based on the specified constraints, but display results for the mother course only, in case the course has instances
	 *
	 * @param array $constraints The constraints for the query
	 * @return array An array of EfrontUser objects
	 * @since 3.6.2
	 * @access public
	 */
 public function getCourseUsersAggregatingResults($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  $from = "(users u, (select uc.user_type as role,uc.score,uc.completed,uc.users_LOGIN,uc.to_timestamp, uc.from_timestamp as active_in_course, uc.from_timestamp as enrolled_on from courses c left outer join users_to_courses uc on uc.courses_ID=c.id where (c.id=".$this -> course['id']." or c.instance_source=".$this -> course['id'].") and uc.archive=0) r)";
  $from = EfrontCourse :: appendTableFiltersUserConstraints($from, $constraints);
  $where[] = "u.login=r.users_LOGIN";
  $select = "u.*, max(score) as score, max(completed) as completed, max(to_timestamp) as to_timestamp, max(role) as role, 1 as has_course, max(active_in_course) as active_in_course, max(enrolled_on) as enrolled_on";
  $groupby = "r.users_LOGIN";
/*
#ifdef ENTERPRISE
			$from   .= " left outer join module_hcd_employees e on e.users_LOGIN=u.login";
			//$where[] = "e.users_LOGIN=u.login";
			$select .= ",e.*, e.users_LOGIN as has_hcd";
#endif
*/
  $result = eF_getTableData($from, $select, implode(" and ", $where), $orderby, $groupby, $limit);
  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontUser :: convertDatabaseResultToUserObjects($result);
  } else {
   return EfrontUser :: convertDatabaseResultToUserArray($result);
  }
 }
 /**
	 * Count course users based on the specified constraints, but display results for the mother course only, in case the course has instances
	 *
	 * @param array $constraints The constraints for the query
	 * @return int Total entries
	 * @since 3.6.2
	 * @access public
	 */
 public function countCourseUsersAggregatingResults($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  $from = "(users u, (select uc.score,uc.completed,uc.users_LOGIN,uc.to_timestamp, uc.from_timestamp as active_in_course from courses c left outer join users_to_courses uc on uc.courses_ID=c.id where (c.id=".$this -> course['id']." or c.instance_source=".$this -> course['id'].") and uc.archive=0) r)";
  $from = EfrontCourse :: appendTableFiltersUserConstraints($from, $constraints);
  $where[] = "u.login=r.users_LOGIN";
  $select = "u.login";
  $groupby = "r.users_LOGIN";
  $result = eF_countTableData($from, $select, implode(" and ", $where), false, $groupby);
  return $result[0]['count'];
 }
 /**
	 * Get course users based on the specified constraints
	 *
	 * @param array $constraints The constraints for the query
	 * @return array An array of EfrontUser objects
	 * @since 3.6.2
	 * @access public
	 */
 public function getCourseUsers($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  $select = "u.*, uc.courses_ID,uc.completed,uc.score,uc.user_type as role,uc.from_timestamp as active_in_course, uc.to_timestamp, uc.comments, uc.issued_certificate, 1 as has_course";
  $where[] = "u.login=uc.users_LOGIN and uc.courses_ID='".$this -> course['id']."' and uc.archive=0";
  $result = eF_getTableData("users u, users_to_courses uc", $select, implode(" and ", $where), $orderby, false, $limit);
  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontUser :: convertDatabaseResultToUserObjects($result);
  } else {
   return EfrontUser :: convertDatabaseResultToUserArray($result);
  }
 }
 /**
	 * Count course users based on the specified constraints
	 * @param array $constraints The constraints for the query
	 * @return int Total entries
	 * @since 3.6.3
	 * @access public
	 */
 public function countCourseUsers($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  //$select  = "u.*, uc.courses_ID,uc.completed,uc.score,uc.user_type,uc.from_timestamp as active_in_course, 1 as has_course";
  $where[] = "u.login=uc.users_LOGIN and uc.courses_ID='".$this -> course['id']."' and uc.archive=0";
  $result = eF_countTableData("users u, users_to_courses uc", "u.login", implode(" and ", $where));
  return $result[0]['count'];
 }
 /**
	 * Get course users based on the specified constraints, but include unassigned users as well.
	 * Assigned users have the flag 'has_course' set to 1
	 *
	 * @param array $constraints The constraints for the query
	 * @return array An array of EfrontUser objects
	 * @since 3.6.2
	 * @access public
	 */
 public function getCourseUsersIncludingUnassigned($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  $where[] = "user_type != 'administrator'";
  $select = "u.*, r.courses_ID is not null as has_course, r.completed,r.score, r.from_timestamp as active_in_course, r.to_timestamp as timestamp_completed, r.role";
  $from = "users u left outer join (select completed,score,courses_ID,from_timestamp, to_timestamp,users_LOGIN,user_type as role from users_to_courses where courses_ID='".$this -> course['id']."' and archive=0) r on u.login=r.users_LOGIN";
  $result = eF_getTableData($from, $select, implode(" and ", $where), $orderby, false, $limit);
  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontUser :: convertDatabaseResultToUserObjects($result);
  } else {
   return EfrontUser :: convertDatabaseResultToUserArray($result);
  }
 }
 /**
	 * Count course users based on the specified constraints, including unassigned
	 * @param array $constraints The constraints for the query
	 * @return array An array of EfrontUser objects
	 * @since 3.6.3
	 * @access public
	 */
 public function countCourseUsersIncludingUnassigned($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  $where[] = "user_type != 'administrator'";
  $select = "u.login";
  $from = "users u left outer join (select distinct completed,score,courses_ID,from_timestamp,users_LOGIN from users_to_courses where courses_ID='".$this -> course['id']."' and archive=0) r on u.login=r.users_LOGIN";
  $result = eF_countTableData($from, $select, implode(" and ", $where));
  return $result[0]['count'];
 }
 /**
	 * Get course users based on the specified constraints, but include unassigned users as well. If the course
	 * has instances, then propagate user status in the mother course
	 *
	 * @param array $constraints The constraints for the query
	 * @return array An array of EfrontUser objects
	 * @since 3.6.2
	 * @access public
	 */
 public function getCourseUsersAggregatingResultsIncludingUnassigned($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  $from = "users u left outer join
     (select users_LOGIN,max(score) as score, max(completed) as completed, 1 as has_course from
      (select uc.user_type as role, uc.score,uc.completed,uc.users_LOGIN from courses c left outer join users_to_courses uc on uc.courses_ID=c.id where (c.id=".$this -> course['id']." or c.instance_source=".$this -> course['id'].") and uc.archive=0) foo
     group by users_LOGIN) r on u.login=r.users_login";
  $result = eF_getTableData($from, "u.*, r.*",
  implode(" and ", $where), $orderby, $groupby, $limit);
  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontUser :: convertDatabaseResultToUserObjects($result);
  } else {
   return EfrontUser :: convertDatabaseResultToUserArray($result);
  }
 }
 /**
	 * Get the possible roles for a user in a course. This function caches EfrontLessonUser :: getLessonsRoles() results
	 * for improved efficiency
	 *
	 * @return array the possible users' roles in the course
	 * @since 3.6.1
	 * @access private
	 */
 private function getPossibleCourseRoles() {
  if (!isset($this -> roles) || !$this -> roles) {
   $this -> roles = EfrontLessonUser :: getLessonsRoles();
  }
  return $this -> roles;
 }
 public static function convertCourseUserConstraintsToSqlParameters($constraints) {
  list($where, $limit, $orderby) = EfrontCourse :: convertLessonConstraintsToSqlParameters($constraints);
  $where = EfrontUser::addWhereConditionToUserConstraints($constraints);
  $limit = self::addLimitConditionToConstraints($constraints);
  $order = self::addSortOrderConditionToConstraints($constraints);
  return array($where, $limit, $order);
 }
 /**
	 * Initialize course users
	 *
	 * @since 3.6.1
	 * @access private
	 * @todo remove when not needed
	 */
 private function initializeUsers($constraints = array()) {
  $this -> course['total_students'] = $this -> course['total_professors'] = 0;
  $roles = EfrontLessonUser :: getLessonsRoles();
  !empty($constraints) OR $constraints = array('archive' => false);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseUserConstraintsToSqlParameters($constraints);
  $from = "users_to_courses uc, users u";
  $from = EfrontCourse :: appendTableFiltersUserConstraints($from, $constraints);
  $select = "u.*, u.user_type as basic_user_type, uc.user_type as role, uc.from_timestamp as active_in_course, uc.score, uc.completed, uc.issued_certificate";
  $where[] = "uc.archive = 0 and uc.users_LOGIN = u.login and uc.courses_ID=".$this -> course['id'];
  $result = eF_getTableData($from, $select, implode(" and ", $where), $orderby, false, $limit);
  foreach ($result as $value) {
   $this -> users[$value['login']] = $value;
   if ($roles[$value['role']] == 'student') {
    $this -> course['total_students']++;
   } elseif ($roles[$value['role']] == 'professor') {
    $this -> course['total_professors']++;
   }
  }
 }
 /**
	 * Get non course users
	 *
	 * This function is used to retrieve a list with the users
	 * that can, but don't have this course
	 * <br/>Example:
	 * <code>
	 * $course -> getNonUsers();
	 * </code>
	 *
	 * @param boolean $returnObjects Whether to return EfrontUser Objects
	 * @return array An array with user logins
	 * @since 3.5.0
	 * @access public
	 * @todo deprecated
	 */
 public function getNonUsers($returnObjects = false) {
  $subquery = "select u.*, u.user_type as basic_user_type,uc.courses_ID as has_course from users u left outer join users_to_courses uc on (uc.users_login=u.login and courses_id=".$this -> course['id']." and uc.archive != 0) where u.archive = 0 and u.active=1 and u.user_type != 'administrator'";
  $result = eF_getTableData("($subquery) s", "s.*, s.has_course is null");
  $users = array();
  foreach ($result as $user) {
   $user['user_types_ID'] ? $user['role'] = $user['user_types_ID'] : $user['role'] = $user['basic_user_type'];
   $returnObjects ? $users[$user['login']] = EfrontUserFactory :: factory($user['login']) : $users[$user['login']] = $user;
  }
  return $users;
 }
 /**
	 * Add the user in the course having the specified role
	 *
	 * @param string $user The user's login
	 * @param mixed $roleInCourse the user's role in the course
	 * @since 3.6.1
	 * @access private
	 */
 private function addUsersToCourse($usersData, $archivedCourseUsers = false) {
  if (!$archivedCourseUsers) {
   $archivedCourseUsers = $this -> getArchivedUsers();
  }
  $newUsers = array();
  foreach ($usersData as $value) {
   if (in_array($value['login'], $archivedCourseUsers)) {
    //Update only fields not related to progress
    $updateFields = array('active' => 1,
          'archive' => 0,
          'from_timestamp' => $value['confirmed'] ? time() : 0,
          'user_type' => $value['role'],
          'to_timestamp' => 0);
    $where = "users_LOGIN='".$value['login']."' and courses_ID=".$this -> course['id'];
    self::persistCourseUsers($updateFields, $where, $this -> course['id'], $value['login']);
    //eF_updateTableData("users_to_lessons", array("active" => 1, "archive" => 0), "users_LOGIN='".$value['login']."' and lessons_ID=".$this -> lesson['id']);
   } else {
    $newUsers[] = $value['login'];
    $fields[] = array('users_LOGIN' => $value['login'],
         'courses_ID' => $this -> course['id'],
         'active' => 1,
         'archive' => 0,
         'from_timestamp' => $value['confirmed'] ? time() : 0,
         'user_type' => $value['role'],
         'completed' => 0,
         'score' => 0,
         'issued_certificate' => '',
         'comments' => '',
         'to_timestamp' => 0);
   }
  }
  if (!empty($newUsers)) {
   eF_insertTableDataMultiple("users_to_courses", $fields);
  }
  if (!defined(_DISABLE_EVENTS) || _DISABLE_EVENTS !== true) {
   foreach ($usersData as $value) {
    $event = array("type" => EfrontUser::isStudentRole($value['role']) ? EfrontEvent::COURSE_ACQUISITION_AS_STUDENT : EfrontEvent::COURSE_ACQUISITION_AS_PROFESSOR,
          "users_LOGIN" => $value['login'],
          "lessons_ID" => $this -> course['id'],
          "lessons_name" => $this -> course['name']);
    EfrontEvent::triggerEvent($event);
    if (EfrontUser::isStudentRole($value['role'])) {
     $event = array("type" => (-1) * EfrontEvent::COURSE_COMPLETION,
          "users_LOGIN" => $value['login'],
          "lessons_ID" => $this -> course['id'],
          "lessons_name" => $this -> course['name'],
        "replace" => true,
        "create_negative" => false);
     EfrontEvent::triggerEvent($event);
    }
   }
  }
  $this -> users = false;
 }
 private function getArchivedUsers() {
  $result = eF_getTableDataFlat("users_to_courses", "users_LOGIN", "archive!=0 and courses_ID=".$this->course['id']);
  if (empty($result)) {
   return array();
  } else {
   return $result['users_LOGIN'];
  }
 }
 /**
	 * Set the user's role in the course
	 *
	 * @param $user The user to set the role for
	 * @param $roleInCourse The role in the course
	 * @since 3.6.1
	 * @access private
	 */
 private function setUserRolesInCourse($usersData) {
  $courseUsers = $this -> getUsers();
  foreach ($usersData as $value) {
   if ($courseUsers[$value['login']]['role'] != $value['role']) {
    $fields = array('archive' => 0,
        'user_type' => $value['role'],
        'from_timestamp' => $value['confirmed'] ? time() : 0);
    $where = "users_LOGIN='".$value['login']."' and courses_ID=".$this -> course['id'];
    self::persistCourseUsers($fields, $where, $this -> course['id'], $value['login']);
   }
  }
 }
 /**
	 * Add users to course
	 *
	 * This function is used to register one or more users to the current course. A single login
	 * or an array of logins may be specified
	 * <br/>Example:
	 * <code>
	 * $course -> addUsers('joe', 'professor');         //Add the user with login 'joe' as a professor to this course
	 * $users = array('joe', 'mary', 'mike');
	 * $types = array('student', 'student', 'professor');
	 * $course -> addUsers($users, $types);             //Add the users in the array $users with roles $types
	 * </code>
	 *
	 * @param mixed $login The user login name
	 * @param mixed $role The user role for this course, defaults to 'student'
	 * @param boolean $confirmed If false, then the registration is set to 'pending' mode and the administration must confirm it
	 * @since 3.5.0
	 * @access public
	 * @todo deprecated
	 */
 public function addUsers($users, $userRoles = 'student', $confirmed = true) {
  if ($this -> course['supervisor_LOGIN']) {
   $confirmed = false;
  }
  $roles = EfrontUser :: getRoles();
  $users = EfrontUser::verifyUsersList($users);
  $userRoles = EfrontUser::verifyRolesList($userRoles, sizeof($users));
  foreach ($userRoles as $key => $value) {
   if (!EfrontUser::isStudentRole($value) && !EfrontUser::isProfessorRole($value)) {
    unset($userRoles[$key]);unset($users[$key]);
   }
  }
  if (empty($users)) {
   return false;
  }
  $result = eF_getTableData("users_to_courses uc, users u", "uc.users_LOGIN, uc.archive, uc.user_type, uc.to_timestamp, u.archive as user_archive, uc.completed", "u.login=uc.users_LOGIN and uc.courses_ID=".$this->course['id']);
  $courseUsers = array();
  $courseRoles = $this -> getPossibleCourseRoles();
  $courseStudents = 0;
  foreach ($result as $value) {
   $courseUsers[$value['users_LOGIN']] = $value;
   if (!$value['user_archive'] && !$value['archive'] && EfrontUser::isStudentRole($value['user_type'])) {
    $courseStudents++;
   }
  }
  /*This query returns an array like:
+------------+------------+-------------+-----------+----------------+---------+
| courses_ID | lessons_ID | users_login | user_type | from_timestamp | archive |
+------------+------------+-------------+-----------+----------------+---------+
|          1 |          3 | professor   | professor |     1233140503 |       0 |
|          1 |          3 | elpapath    | professor |     1233140503 |       0 |
|          1 |         19 | periklis3   | student   |     1280488977 |       0 |
|          1 |         20 | NULL        | NULL      |           NULL |    NULL |
+------------+------------+-------------+-----------+----------------+---------+
		So that it contains all the course's lessons and NULL for any lesson that does not have a user assigned
		*/
  $result = eF_getTableData("lessons_to_courses lc left outer join users_to_lessons ul on lc.lessons_ID=ul.lessons_ID",
          "lc.lessons_ID, ul.users_LOGIN, ul.user_type, ul.from_timestamp, ul.archive, ul.to_timestamp, ul.completed",
          "courses_ID = ".$this -> course['id']);
  $courseLessonsToUsers = array();
  foreach ($result as $value) {
   if (!is_null($value['users_LOGIN'])) {
    $courseLessonsToUsers[$value['lessons_ID']][$value['users_LOGIN']] = $value;
   } else {
    $courseLessonsToUsers[$value['lessons_ID']] = array();
   }
  }
  $courseLessons = array_unique(array_keys($courseLessonsToUsers));
  $result = eF_getTableData("projects", "id, lessons_ID", "auto_assign=1 and deadline >= ".time()." and lessons_ID in (select lessons_ID from lessons_to_courses where courses_ID=".$this -> course['id'].")");
  $newProjectAssignments = $courseLessonsAutoAssignProjects = $assignedProjectsToUsers = array();
  foreach ($result as $value) {
   $courseLessonsAutoAssignProjects[$value['lessons_ID']][] = $value['id'];
  }
  $result = eF_getTableData("users_to_projects up, projects p", "up.users_LOGIN, up.projects_ID", "up.projects_ID=p.id and p.auto_assign=1 and p.deadline >= ".time()." and p.lessons_ID in (select lessons_ID from lessons_to_courses where courses_ID=".$this -> course['id'].")");
  foreach ($result as $value) {
   $assignedProjectsToUsers[$value['users_LOGIN']][$value['projects_ID']] = $value['projects_ID'];
  }
  $newUsers = array();
  $existingUsers = array();
  foreach ($users as $key => $user) {
   $roleInCourse = $userRoles[$key];
   $roles[$roleInCourse] == 'student' ? $isStudentRoleInCourse = true : $isStudentRoleInCourse = false;
   if ($this -> course['max_users'] && $isStudentRoleInCourse && $this -> course['max_users'] <= $courseStudents++) {
    throw new EfrontCourseException(_MAXIMUMUSERSREACHEDFORCOURSE, EfrontCourseException :: MAX_USERS_LIMIT);
   }
   if (!isset($courseUsers[$user])) {
    $newUsers[] = array('users_LOGIN' => $user,
         'courses_ID' => $this -> course['id'],
         'active' => 1,
         'archive' => 0,
         'from_timestamp' => $confirmed ? time() : 0,
         'user_type' => $roleInCourse,
         'completed' => 0,
         'score' => 0,
         'issued_certificate' => '',
         'comments' => '',
         'to_timestamp' => 0);
   } elseif ($roleInCourse != $courseUsers[$user]['user_type'] || $courseUsers[$user]['archive']) {
    //update from_timestamp value when user reassigned to a course (only if it is not completed)
    if ($courseUsers[$user]['completed']) {
     $fields = array('archive' => 0,
         'user_type' => $roleInCourse);
    } else {
     $fields = array('archive' => 0,
         'user_type' => $roleInCourse,
         'from_timestamp' => time());
    }
    //!$courseUsers[$user]['archive'] OR $fields['to_timestamp'] = 0;
    $confirmed OR $fields['from_timestamp'] = 0;
    $where = "users_LOGIN='".$user."' and courses_ID=".$this -> course['id'];
    self::persistCourseUsers($fields, $where, $this -> course['id'], $user);
    $existingUsers[] = $courseUsers[$user];
   }
   foreach ($courseLessons as $id) {
    if (!isset($courseLessonsToUsers[$id][$user])) {
     $usersToAddToCourseLesson[$id][$user] = array('login' => $user, 'role' => $roleInCourse, 'confirmed' => $confirmed);
     $newLessonUsers[] = array('users_LOGIN' => $user,
            'lessons_ID' => $id,
            'active' => 1,
            'archive' => 0,
            'from_timestamp' => $confirmed ? time() : 0,
            'user_type' => $roleInCourse,
            'positions' => '',
            'done_content' => '',
            'current_unit' => 0,
            'completed' => 0,
            'score' => 0,
            'comments' => '',
            'to_timestamp' => 0);
     if (EfrontUser::isStudentRole($roleInCourse)) {
      foreach ($courseLessonsAutoAssignProjects[$id] as $projectId) {
       if (!isset($assignedProjectsToUsers[$user][$projectId])) {
        $newProjectAssignments[] = array('users_LOGIN' => $user,
                 'projects_ID' => $projectId);
       }
      }
     }
    } elseif ($roleInCourse != $courseLessonsToUsers[$id][$user]['user_type'] || $courseLessonsToUsers[$id][$user]['archive']) {
     //update also lesson from_timestamp value when user reassigned to a course (only if it is not completed)
     if ($courseLessonsToUsers[$id][$user]['completed']) {
      $fields = array('archive' => 0,
          'user_type' => $roleInCourse);
     } else {
      $fields = array('archive' => 0,
          'user_type' => $roleInCourse,
          'from_timestamp' => time());
     }
     //!$courseLessonsToUsers[$id][$user]['archive'] OR $fields['to_timestamp'] = 0;
     $confirmed OR $fields['from_timestamp'] = 0;
     eF_updateTableData("users_to_lessons", $fields, "users_LOGIN='".$user."' and lessons_ID=".$id);
     if (EfrontUser::isStudentRole($roleInCourse)) {
      foreach ($courseLessonsAutoAssignProjects[$id] as $projectId) {
       if (!isset($assignedProjectsToUsers[$user][$projectId])) {
        $newProjectAssignments[] = array('users_LOGIN' => $user,
                 'projects_ID' => $projectId);
       }
      }
     }
    }
   }
  }
  if (!empty($newUsers)) {
   eF_insertTableDataMultiple("users_to_courses", $newUsers);
  }
  if (!empty($newLessonUsers)) {
   eF_insertTableDataMultiple("users_to_lessons", $newLessonUsers);
  }
  if (!empty($newProjectAssignments)) {
   eF_insertTableDataMultiple("users_to_projects", $newProjectAssignments);
  }
  !isset($newUsers) ? $newUsers = array() : null;
  !isset($existingUsers) ? $existingUsers = array() : null;
  $eventArray = array_merge($newUsers, $existingUsers);
  if (!defined(_DISABLE_EVENTS) || _DISABLE_EVENTS !== true) {
   foreach ($eventArray as $value) {
    $event = array("type" => EfrontUser::isStudentRole($value['user_type']) ? EfrontEvent::COURSE_ACQUISITION_AS_STUDENT : EfrontEvent::COURSE_ACQUISITION_AS_PROFESSOR,
          "users_LOGIN" => $value['users_LOGIN'],
          "lessons_ID" => $this -> course['id'],
          "lessons_name" => $this -> course['name']);
    EfrontEvent::triggerEvent($event);
    if (EfrontUser::isStudentRole($value['user_type'])) {
     $event = array("type" => (-1) * EfrontEvent::COURSE_COMPLETION,
          "users_LOGIN" => $value['users_LOGIN'],
          "lessons_ID" => $this -> course['id'],
          "lessons_name" => $this -> course['name'],
        "replace" => true,
        "create_negative" => false);
     EfrontEvent::triggerEvent($event);
    }
   }
  }
  $modules = eF_loadAllModules();
  foreach ($modules as $module) {
   $module -> onAddUsersToCourse($this -> course['id'], $eventArray, $newLessonUsers);
  }
  $this -> users = false; //Reset users cache
 }
 /**
	 * Remove user from course
	 *
	 * This function is used to remove a user from the current course
	 * <br/>Example:
	 * <code>
	 * $course -> removeUsers('jdoe');   //Remove user with login 'jdoe'
	 * </code>
	 *
	 * @param array $user the user login to remove
	 * @return array The new list of course users
	 * @since 3.5.0
	 * @access public
	 * @todo rename to removeUsersFromCourse
	 */
 public function removeUsers($users) {
  $users = EfrontUser::verifyUsersList($users);
  $this -> removeUsersFromCourseLessons($users);
  $this -> sendNotificationsRemoveCourseUsers($users);
  foreach ($users as $user) {
   eF_deleteTableData("users_to_courses", "users_LOGIN='$user' and courses_ID=".$this -> course['id']);
   //$cacheKey = "user_course_status:course:".$this -> course['id']."user:".$user;
   //Cache::resetCache($cacheKey);
  }
  $this -> users = false; //Reset users cache
  return $this -> getUsers();
 }
 /**
	 * This function removes users from the course lessons
	 *
	 * @param array $users the users to remove
	 * @since 3.6.3
	 * @access private
	 */
 private function removeUsersFromCourseLessons($users) {
  if (sizeof($users) == 1) {
   $key = key($users);
   $lessonsToCourses = EfrontLesson::countLessonsOccurencesInCoursesForUser($users[$key]);
  } else {
   $lessonsToCourses = EfrontLesson::countLessonsOccurencesInCoursesForAllUsers();
  }
  foreach ($this -> getCourseLessons() as $lesson) {
   $usersToRemove = array();
   foreach ($users as $user) {
    if ($lessonsToCourses[$user][$lesson -> lesson['id']] == 1) {
     $usersToRemove[] = $user;
    }
   }
   $lesson -> removeUsers($usersToRemove);
  }
 }
 /**
	 * Archive user in course
	 *
	 * This function is used to archive a user in the current course. It's similar to removing,
	 * only the user relation to the tracking data is not lost but retained
	 * <br/>Example:
	 * <code>
	 * $course -> archiveCourseUsers('jdoe');   //Archive user with login 'jdoe'
	 * </code>
	 *
	 * @param array $user the user login to archive
	 * @return array The new list of course users
	 * @since 3.5.0
	 * @access public
	 */
 public function archiveCourseUsers($users) {
  $users = EfrontUser::verifyUsersList($users);
  $this -> archiveUsersInCourseLessons($users);
  $this -> sendNotificationsRemoveCourseUsers($users);
  foreach ($users as $user) {
   $fields = array("archive" => time());
   $where = "users_LOGIN='$user' and courses_ID=".$this -> course['id'];
   self::persistCourseUsers($fields, $where, $this -> course['id'], $user);
  }
  $this -> users = false; //Reset users cache
  return $this -> getUsers();
 }
 /**
	 * This function archives the specified users in course's lessons
	 *
	 * @param array $users The users to archive
	 * @since 3.6.3
	 * @access private
	 */
 private function archiveUsersInCourseLessons($users) {
  if (sizeof($users) == 1) {
   $key = key($users);
   $lessonsToCourses = EfrontLesson::countLessonsOccurencesInCoursesForUser($users[$key]);
  } else {
   $lessonsToCourses = EfrontLesson::countLessonsOccurencesInCoursesForAllUsers();
  }
  foreach ($this -> getCourseLessons() as $lesson) {
   $usersToArchive = array();
   foreach ($users as $user) {
    if ($lessonsToCourses[$user][$lesson -> lesson['id']] == 1) {
     $usersToArchive[] = $user;
    }
   }
   $lesson -> archiveLessonUsers($usersToArchive);
  }
 }
 /**
	 * Send a notification to each user that has been removed from the course
	 *
	 * @param array $users The users to send notification to
	 * @since 3.6.3
	 * @access private
	 */
 private function sendNotificationsRemoveCourseUsers($users) {
  foreach ($users as $user) {
   EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_REMOVAL,
           "users_LOGIN" => $user,
           "lessons_ID" => $this -> course['id'],
           "lessons_name" => $this -> course['name']));
  }
 }
 /** Check if user is active in this course
	 *
	 * This function will return true if the user can normally access this course, or
	 * false if he/she needs registration confirmation by the admin or supervisor
	 *
	 * @param mixed $login Either the user login, or an EfrontLessonUser object
	 * @since 3.6.7
	 * @access public
	 */
 public function isUserActiveInCourse($login) {
  $login = EfrontUser::convertArgumentToUserLogin($login);
  $result = eF_getTableData("users_to_courses", "from_timestamp", "archive = 0 and users_LOGIN='$login' and courses_ID=".$this -> course['id']);
  if (empty($result)) {
   throw new EfrontUserException(_THEUSERDOESNOTHAVETHISCOURSE.': '.$this -> course['id'], EfrontUserException::USER_NOT_HAVE_COURSE);
  } else if ($result[0]['from_timestamp'] > 0) {
   return true;
  } else {
   return false;
  }
 }
 /**
	 * Confirm user registration
	 *
	 * This function is used to set the specified user's status for the current course
	 * to 'available', if it was previously set to 'pending'
	 * <br/>Example:
	 * <code>
	 * $course = new EfrontCourse(32);
	 * $course -> confirm('jdoe');
	 * </code>
	 *
	 * @param mixed $login Either the user login, or an EfrontLessonUser object
	 * @since 3.5.2
	 * @access public
	 */
 public function confirm($login) {
  $login = EfrontUser::convertArgumentToUserLogin($login);
  foreach ($this -> getCourseLessons() as $lesson) {
   $lesson -> confirm($login);
  }
  $fields = array("from_timestamp" => time());
  $where = "users_LOGIN='".$login."' and courses_ID=".$this -> course['id']." and from_timestamp=0";
  self::persistCourseUsers($fields, $where, $this -> course['id'], $login);
 }
 /** This function is used to set the specified user's status for the current course
	 * to 'pending'
	 * <br/>Example:
	 * <code>
	 * $course = new EfrontCourse(32);
	 * $course -> unconfirm('jdoe');
	 * </code>
	 *
	 * @param mixed $login Either the user login, or an EfrontLessonUser object
	 * @since 3.6.0
	 * @access public
	 */
 public function unConfirm($login) {
  $login = EfrontUser::convertArgumentToUserLogin($login);
  foreach ($this -> getCourseLessons() as $lesson) {
   $lesson -> unConfirm($login);
  }
  $fields = array("from_timestamp" => 0);
  $where = "users_LOGIN='".$login."' and courses_ID=".$this -> course['id'];
  self::persistCourseUsers($fields, $where, $this -> course['id'], $login);
 }
 /**
	 * Convert the group argument to a group id
	 *
	 * @param mixed $group The argument to convert
	 * @return int The group's id
	 * @since 3.6.3
	 */
 private static function convertArgumentToGroupId($group) {
  if ($group instanceof EfrontGroup) {
   $group = $group -> group['id'];
  } else if (!eF_checkParameter($group, 'id')) {
   throw new EfrontGroupException(_INVALIDID, EfrontGroupException::INVALID_ID);
  }
  return $group;
 }
 /**
	 * Set user roles in course
	 *
	 * This function sets the role for the specified user in the course
	 * <br/>Example:
	 * <code>
	 * $course -> addUsers('jdoe', 'student');              //Added the user 'jdoe' in the lesson, having the role 'student'
	 * $course -> setRoles('jdoe', 'professor');                //Updated jdoe's role to be 'professor'
	 * </code>
	 * Multiple values can be set if arguments are arrays
	 *
	 * @param mixed $users The user login name
	 * @param mixed $roles The user role for this course
	 * @since 3.5.0
	 * @access public
	 */
 public function setRoles($users, $roles) {
  $users = EfrontUser::verifyUsersList($users);
  $roles = EfrontUser::verifyRolesList($roles, sizeof($users));
  $courseLessons = $this -> getCourseLessons();
  foreach ($users as $key => $value) {
   $fields = array('user_type' => $roles[$key]);
   $where = "users_LOGIN='".$value."' and courses_ID=".$this -> course['id'];
   self::persistCourseUsers($fields, $where, $this -> course['id'], $value);
   foreach ($courseLessons as $lessonId => $lesson) {
    $lesson -> setRoles($value, $roles[$key]);
   }
  }
 }
 /**
	 * Persist stored value
	 *
	 * This function is used to store course values to the database
	 * <br/>Example:
	 * <code>
	 * $course -> perist();
	 * </code>
	 *
	 * @return boolean true if everything is ok
	 * @since 3.5.0
	 * @access public
	 */
 public function persist() {
  $this -> rules ? $this -> course['rules'] = serialize($this -> rules) : $this -> course['rules'] = null;
  $this -> options ? $this -> course['options'] = serialize($this -> options) : $this -> course['options'] = null;
  $localeSettings = localeconv();
  $this -> course['price'] = str_replace($localeSettings['decimal_point'], '.', $this -> course['price']); //This way, you handle the case where the price is in the form 1,000.00
  $fields = $this -> validateAndSanitizeCourseFields($this -> course);
  eF_updateTableData("courses", $fields, "id=".$this -> course['id']);
  if (!defined(_DISABLE_SEARCH) || _DISABLE_SEARCH !== true) {
   EfrontSearch :: removeText('courses', $this -> course['id'], '');
   EfrontSearch :: insertText($fields['name'], $this -> course['id'], "courses", "title");
  }
  return true;
 }
 /**
	 * Make sure that the course fields are valid, and sanitize properly if not
	 * @param array $courseFields The course fields
	 * @return array The sanitized fields
	 * @since 3.6.3
	 * @access private
	 */
 private static function validateAndSanitizeCourseFields($courseFields) {
  $courseFields = self :: setDefaultCourseValues($courseFields);
  $fields = array('name' => self :: validateAndSanitize($courseFields['name'], 'name'),
                        'active' => self :: validateAndSanitize($courseFields['active'], 'boolean'),
            'archive' => self :: validateAndSanitize($courseFields['archive'], 'timestamp'),
            'created' => self :: validateAndSanitize($courseFields['created'], 'timestamp'),
            'start_date' => self :: validateAndSanitize($courseFields['start_date'], 'timestamp'),
            'end_date' => self :: validateAndSanitize($courseFields['end_date'], 'timestamp'),
      'options' => self :: validateAndSanitize($courseFields['options'], 'serialized'),
            'metadata' => self :: validateAndSanitize($courseFields['metadata'], 'serialized'),
            'info' => self :: validateAndSanitize($courseFields['info'], 'serialized'),
            'description' => self :: validateAndSanitize($courseFields['description'], 'text'),
            'price' => self :: validateAndSanitize($courseFields['price'], 'float'),
            'show_catalog' => self :: validateAndSanitize($courseFields['show_catalog'], 'boolean'),
            'publish' => self :: validateAndSanitize($courseFields['publish'], 'boolean'),
            'directions_ID' => self :: validateAndSanitize($courseFields['directions_ID'], 'directions_foreign_key'),
                        'languages_NAME' => self :: validateAndSanitize($courseFields['languages_NAME'], 'languages_foreign_key'),
                        'reset' => self :: validateAndSanitize($courseFields['reset'], 'boolean'),
                        'certificate_expiration' => self :: validateAndSanitize($courseFields['certificate_expiration'], 'integer'),
       'reset_interval' => self :: validateAndSanitize($courseFields['reset_interval'], 'integer'),
                        'max_users' => self :: validateAndSanitize($courseFields['max_users'], 'integer'),
                        'ceu' => self :: validateAndSanitize($courseFields['ceu'], 'integer'),
      'rules' => self :: validateAndSanitize($courseFields['rules'], 'serialized'),
                        'supervisor_LOGIN' => self :: validateAndSanitize($courseFields['supervisor_LOGIN'], 'text'),
      'instance_source' => self :: validateAndSanitize($courseFields['instance_source'], 'courses_foreign_key'),
      'depends_on' => self :: validateAndSanitize($courseFields['depends_on'], 'courses_foreign_key'));
  return $fields;
 }
 /**
	 * Set default values for course fields
	 * @param array $courseFields The current course fields
	 * @return array The course fields, with default values where missing
	 */
 private static function setDefaultCourseValues($courseFields) {
  $defaultValues = array('name' => '',
                            'active' => 1,
                'archive' => 0,
                'created' => time(),
                'start_date' => '',
                'end_date' => '',
          'options' => '',
                'metadata' => '',
                'info' => '',
                'description' => '',
                'price' => 0,
                'show_catalog' => 1,
                'publish' => 1,
                'directions_ID' => 0,
                         'languages_NAME' => 'english',
                         'reset' => 0,
                         'certificate_expiration' => 0,
          'reset_interval' => 0,
                         'max_users' => 0,
                         'rules' => '',
          'supervisor_LOGIN' => '',
                'instance_source' => 0,
          'depends_on' => 0);
  return array_merge($defaultValues, $courseFields);
 }
 /**
	 * Validate and sanitize parameters
	 *
	 * This function is used to validate and sanitize the passed parameter.
	 * It accepts the $field argument and its type and validates the field argument against the
	 * desired type.
	 * <br/>Example:
	 * <code>
	 * $course -> validateAndSanitize(32, 'float');	//returns 32
	 * $course -> validateAndSanitize('32asd', 'integer');	//returns 32
	 * </code>
	 *
	 * @param mixed $field The field to validate and optionally sanitize
	 * @param string $type The desired parameter type
	 * @return mixed The original passed parameter, sanitized
	 * @since 3.6.1
	 * @access public
	 */
 public static function validateAndSanitize($field, $type) {
  try {
   self :: validate($field, $type);
  } catch (EfrontCourseException $e) {
   if ($e -> getCode() == EfrontCourseException::INVALID_PARAMETER) {
    $field = self :: sanitize($field, $type);
   } else {
    throw $e;
   }
  }
  return $field;
 }
 /**
	 * Validate input based on the specified type
	 *
	 * This function validates the parameter value against the specified
	 * type. If it does not match, an exception is thrown.
	 * <br/>Example:
	 * <code>
	 * $course -> validate(32, 'float');	//returns true
	 * $course -> validate('32asd', 'integer');	//throws exception.
	 * </code>
	 *
	 * @param mixed $field The field to validate
	 * @param string $type The desired parameter type
	 * @return boolean Whether the passed parameter is valid
	 * @since 3.6.1
	 * @access public
	 */
 public static function validate($field, $type) {
  $validParameter = true;
  switch ($type) {
   case 'id': self :: validateId($field) OR $validParameter = false; break;
   case 'name': self :: validateName($field) OR $validParameter = false; break;
   case 'boolean': self :: validateBoolean($field) OR $validParameter = false; break;
   case 'float': self :: validateFloat($field) OR $validParameter = false; break;
   case 'integer': self :: validateInteger($field) OR $validParameter = false; break;
   case 'directions_foreign_key': self :: validateDirectionsForeignKey($field) OR $validParameter = false; break;
   case 'languages_foreign_key': self :: validateLanguagesForeignKey($field) OR $validParameter = false; break;
   case 'courses_foreign_key': self :: validateCoursesForeignKey($field) OR $validParameter = false; break;
   case 'text': self :: validateText($field) OR $validParameter = false; break;
   case 'serialized': self :: validateSerialized($field) OR $validParameter = false; break;
   case 'timestamp': self :: validateTimestamp($field) OR $validParameter = false; break;
   case 'boolean_or_timestamp': (self :: validateBoolean($field) || self :: validateTimestamp($field)) OR $validParameter = false; break;
   default: break;
  }
  if ($validParameter) {
   return true;
  } else {
   throw new EfrontCourseException(_INVALIDPARAMETER.' ('.$type.'): "'.$field.'"', EfrontCourseException::INVALID_PARAMETER);
  }
 }
 private static function validateId($field) {
  !eF_checkParameter($field, 'id') ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateName($field) {
  mb_strlen($field) > self::MAX_NAME_LENGTH ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateText($field) {
  return true;
 }
 private static function validateBoolean($field) {
  $field !== false && $field !== true ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateTimestamp($field) {
  !eF_checkParameter($field, 'timestamp') ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateSerialized($field) {
  unserialize($field) === false && $field !== serialize(false) ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateSerializedArray($field) {
  $unserialized = unserialize($field);
  $unserialized === false || !is_array($unserialized) ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateNull($field) {
  !is_null($field) ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateFloat($field) {
  !is_numeric($field) ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateInteger($field) {
  !is_numeric($field) ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateDirectionsForeignKey($field) {
  !eF_checkParameter($field, 'id') || sizeof(eF_getTableData("directions", "id", "id=".$field)) == 0 ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateCoursesForeignKey($field) {
  !eF_checkParameter($field, 'id') || sizeof(eF_getTableData("courses", "id", "id=".$field)) == 0 ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateLessonsForeignKey($field) {
  !eF_checkParameter($field, 'id') || sizeof(eF_getTableData("lessons", "id", "id=".$field)) == 0 ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateLanguagesForeignKey($field) {
  !eF_checkParameter($field, 'login') || sizeof(eF_getTableData("languages", "name", "name='".$field."'")) == 0 ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 private static function validateUsersForeignKey($field) {
  !eF_checkParameter($field, 'login') || sizeof(eF_getTableData("users", "login", "login='$field'")) == 0 ? $returnValue = false : $returnValue = true;
  return $returnValue;
 }
 /**
	 * Sanitize parameter
	 *
	 * This function is used to sanitize the passed parameter, based on the type
	 * specified
	 * <br/>Example:
	 * <code>
	 * $course -> sanitize(32, 'float');	//returns 32
	 * $course -> sanitize('32asd', 'integer');	//returns 32
	 * </code>
	 *
	 * @param mixed $field The field to sanitize
	 * @param string $type The desired parameter type
	 * @return mixed The sanitized passed parameter
	 * @since 3.6.1
	 * @access public
	 */
 public static function sanitize($field, $type) {
  switch ($type) {
   case 'name': $field = self :: sanitizeName($field); break;
   case 'boolean': $field = self :: sanitizeBoolean($field); break;
   case 'boolean_or_timestamp': $field = self :: sanitizeBoolean($field); break;
   case 'timestamp': $field = self :: sanitizeTimestamp($field); break;
   case 'serialized': $field = self :: sanitizeSerialized($field); break;
   case 'float': $field = self :: sanitizeFloat($field); break;
   case 'integer':
   case 'id': $field = self :: sanitizeInteger($field); break;
   case 'directions_foreign_key':
   case 'languages_foreign_key':
   case 'courses_foreign_key': $field = self :: sanitizeForeignKey($field); break;
   case 'text': default: break;
  }
  return $field;
 }
 private static function sanitizeTimestamp($field) {
  $field = 0;
  return $field;
 }
 private static function sanitizeName($field) {
  $field = mb_substr($field, 0, self::MAX_NAME_LENGTH);
  return $field;
 }
 private static function sanitizeBoolean($field) {
  $field = ($field != 0);
  return $field;
 }
 private static function sanitizeSerialized($field) {
  $field = serialize(array());
  return $field;
 }
 private static function sanitizeFloat($field) {
  $field = (float)$field;
  return $field;
 }
 private static function sanitizeInteger($field) {
  $field = (int)$field;
  return $field;
 }
 private static function sanitizeForeignKey($field) {
  $field = 0;
  return $field;
 }
 /**
	 * Archive course
	 *
	 * This function is used to archive the course object, by setting its active status to 0 and its
	 * archive status to 1. It also deactivates its instances
	 * <br/>Example:
	 * <code>
	 * $course -> archive();	//Archives the course object
	 * $course -> unarchive();	//Archives the course object and activates it as well
	 * </code>
	 *
	 * @since 3.6.0
	 * @access public
	 */
 public function archive() {
  $this -> course['archive'] = time();
  $this -> course['active'] = 0;
  foreach ($this -> getInstances(array('archive' => false)) as $value) {
   $value -> course['active'] = 0;
   $value -> course['archive'] = time();
   $value -> persist();
   $value -> archiveUniqueLessons();
  }
  $this -> persist();
 }
 /**
	 * Delete course lessons that where created especially for it
	 *
	 * @since 3.6.1
	 * @access private
	 */
 private function archiveUniqueLessons() {
  $result = eF_getTableData("lessons", "*", "originating_course=".$this -> course['id']);
  foreach ($result as $value) {
   $value = new EfrontLesson($value);
   $value -> archive();
  }
 }
 /**
	 * Unarchive course
	 *
	 * This function is used to unarchive the course object, by setting its active status to 1 and its
	 * archive status to 0
	 * <br/>Example:
	 * <code>
	 * $course -> archive();	//Archives the course object
	 * $course -> unarchive();	//Archives the course object and activates it as well
	 * </code>
	 *
	 * @since 3.6.0
	 * @access public
	 */
 public function unarchive() {
  $this -> course['archive'] = 0;
  $this -> course['active'] = 1;
  //Check whether the original category exists
  $result = eF_getTableDataFlat("directions", "id");
  if (in_array($this -> course['directions_ID'], $result['id'])) { // If the original category exists, no problem
   $this -> persist();
  } elseif (empty($result)) { //If no categories exist in the system, throw exception
   throw new EfrontLessonException(_NOCATEGORIESDEFINED, EfrontLessonException::CATEGORY_NOT_EXISTS);
  } else { //If some other category exists, assign it there
   $this -> course['directions_ID'] = $result['id'][0];
   $this -> persist();
  }
  $this -> unarchiveUniqueLessons();
 }
 /**
	 * Delete course lessons that where created especially for it
	 *
	 * @since 3.6.1
	 * @access private
	 */
 private function unarchiveUniqueLessons() {
  $result = eF_getTableData("lessons", "*", "originating_course=".$this -> course['id']);
  foreach ($result as $value) {
   $value = new EfrontLesson($value);
   $value -> unarchive();
  }
 }
 /**
	 * Delete course
	 *
	 * This function is used to delete the current course. It also
	 * removes the course from the succession information of other
	 * courses
	 * <br/>Example:
	 * <code>
	 * $course -> delete();
	 * </code>
	 *
	 * @since 3.5.0
	 * @access public
	 * @todo remove from other courses succession
	 */
 public function delete() {
  $this -> removeLessons(array_keys($this -> getCourseLessons()));
  $courseUsers = eF_getTableDataFlat("users_to_courses", "users_LOGIN", "courses_ID=".$this -> course['id']);
  $this -> removeUsers($courseUsers["users_LOGIN"]);
  $this -> deleteCourseInstances();
  $this -> removeCourseSkills();
  $this -> deleteUniqueLessons();
  calendar::deleteCourseCalendarEvents($this);
  eF_deleteTableData("courses", "id=".$this -> course['id']);
  eF_updateTableData("courses", array("depends_on" => 0), "depends_on = ".$this->course['id']);
  $modules = eF_loadAllModules();
  foreach ($modules as $module) {
   $module -> onDeleteCourse($this -> course['id']);
  }
  EfrontSearch :: removeText('courses', $this -> course['id'], '');
 }
 /**
	 * Delete course instances
	 *
	 * @since 3.6.1
	 * @access private
	 */
 private function deleteCourseInstances() {
  $result = eF_getTableData("courses", "*", "instance_source=".$this -> course['id']);
  foreach ($result as $value) {
   $value = new EfrontCourse($value);
   $value -> delete();
  }
 }
 /**
	 * Delete course lessons that where created especially for it
	 *
	 * @since 3.6.1
	 * @access private
	 */
 private function deleteUniqueLessons() {
  $result = eF_getTableData("lessons", "*", "originating_course=".$this -> course['id']);
  foreach ($result as $value) {
   $value = new EfrontLesson($value);
   $value -> delete(false);
  }
 }
 /**
	 * Remove skills from course
	 *
	 * @since 3.6.1
	 * @access private
	 */
 public function removeCourseSkills() {
 }
 /**
	 * Create course instance
	 *
	 * This function is used to create a course instance, from the specified course source
	 * <br/>Example:
	 * <code>
	 * $instance = EfrontCourse :: createInstance(43);
	 * </code>
	 *
	 * @param mixed $instanceSource Either a course id or an EfrontCourse object.
	 * @return EfrontCourse The new course instance
	 * @since 3.6.1
	 * @access public
	 * @static
	 */
 public static function createInstance($instanceSource) {
  if (!($instanceSource instanceof EfrontCourse)) {
   $instanceSource = new EfrontCourse($instanceSource);
  }
  $instance = self :: createDbEntryForInstance($instanceSource);
  self :: assignSourceLessonsToInstance($instanceSource, $instance);
  self :: assignSourceSkillsToInstance($instanceSource, $instance);
  self :: assignSourceBranchToInstance($instanceSource, $instance);
  $instance = new EfrontCourse($instance -> course['id']); //refresh instance object
  return $instance;
 }
 private static function createDbEntryForInstance($instanceSource) {
  $currentSourceInstances = sizeof($instanceSource -> getInstances());
  $result = eF_getTableData("courses", "*", "id=".$instanceSource -> course['id']); //Get all the fields of the course directly from the database
  unset($result[0]['id']);
  $result[0]['name'] .= ' ('._INSTANCE.' #'.($currentSourceInstances+1).")";
  $result[0]['instance_source'] = $instanceSource -> course['id'];
  $result[0]['created'] = time();
  $instance = EfrontCourse :: createCourse($result[0]);
  $instance -> options['course_code'] = ''; //Instances don't have a course code of their own
  $instance -> rules = array();
  $instance -> persist();
  return $instance;
 }
 private static function assignSourceLessonsToInstance($instanceSource, $instance) {
  $result = eF_getTableDataFlat("lessons_to_courses lc, lessons l", "l.id, l.instance_source", "l.id=lc.lessons_ID and lc.courses_ID=".$instanceSource -> course['id']);
  $instanceSourceLessonsThatAreUnique = array_combine($result['id'], $result['instance_source']);
  $instanceSourceLessons = $instanceSource -> getCourseLessons();
  $newLessons = array();
  foreach ($instanceSourceLessons as $key => $foo) { //Do this to get the lessons in the correct order
   $value = $instanceSourceLessonsThatAreUnique[$key];
   if ($value) {
    $lessonInstance = EfrontLesson :: createInstance($value, $instance -> course['id']);
    $newLessons[] = $lessonInstance -> lesson['id'];
   } else {
    $newLessons[] = $key;
   }
  }
  $instance -> addLessons($newLessons);
 }
 private static function assignSourceSkillsToInstance($instanceSource, $instance) {
  $courseSkills = $instanceSource -> getSkills(true);
  foreach ($courseSkills as $key => $skill) {
   $instance -> assignSkill($skill['skill_ID'], $skill['specification']);
  }
 }
 private static function assignSourceBranchToInstance($instanceSource, $instance) {
 }
 /**
	 * Revoke sertificate
	 *
	 * This function is used to revoke the certificate for the
	 * specified user.
	 * <br/>Example:
	 * <code>
	 * $course -> revokeCertificate($login);
	 * </code>
	 *
	 * @param mixed $login The user to revoke certificate for, either a login string or an EfrontUser object
	 * @return boolean Whether the certificate was revoked successfully
	 * @since 3.5.0
	 * @access public
	 */
 public function revokeCertificate($login) {
  $login = EfrontUser::convertArgumentToUserLogin($login);
  $fields = array("issued_certificate" => "");
  $where = "users_LOGIN='$login' and courses_ID=".$this -> course['id'];
  self::persistCourseUsers($fields, $where, $this -> course['id'], $login);
  EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_CERTIFICATE_REVOKE,
          "users_LOGIN" => $login,
          "lessons_ID" => $this -> course['id'],
          "lessons_name" => $this -> course['name']));
  $modules = eF_loadAllModules();
  foreach ($modules as $module) {
   $module -> onRevokeCourseCertificate($login, $this -> course['id']);
  }
  return true;
 }
 /**
	 * Issue certificate for the specified user
	 *
	 * This function is used to issue a certificate for
	 * the specified user.
	 * <br/>Example:
	 * <code>
	 * $certificate = $course -> prepareCertificate('jdoe');            //Prepare the certificate for user 'jdoe'
	 * $course -> issueCertificate('jdoe', $certificate);               //Issue certificate for user 'jdoe'
	 * </code>
	 *
	 * @param string $login The login of the user to issue certificate for
	 * @return boolean true if the certificate was issued successfully
	 * @since 3.5.0
	 * @access public
	 */
 public function issueCertificate($login, $certificate) {
  $login = EfrontUser::convertArgumentToUserLogin($login);
  $fields = array("issued_certificate" => $certificate);
  $where = "users_LOGIN='$login' and courses_ID=".$this -> course['id'];
  self::persistCourseUsers($fields, $where, $this -> course['id'], $login);
  $certificateArray = unserialize($certificate);
  EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_CERTIFICATE_ISSUE,
          "users_LOGIN" => $login,
          "lessons_ID" => $this -> course['id'],
          "lessons_name" => $this -> course['name'],
          "entity_ID" => $certificateArray['serial_number'],
          "entity_name" => $certificateArray['grade']));
  $modules = eF_loadAllModules();
  foreach ($modules as $module) {
   $module -> onIssueCourseCertificate($login, $this -> course['id'], $certificateArray);
  }
  return true;
 }
 /**
	 * Prepare certificate for user
	 *
	 * This function is used to prepare the certificate that
	 * will be issued to the specified user. It returns an array with the certificate data
	 * <br/>Example:
	 * <code>
	 * $certificate = $course -> prepareCertificate('jdoe');
	 * </code>
	 *
	 * @param string $login The user to prepare a certificate for
	 * @return string The certificate data
	 * @since 3.5.0
	 * @access public
	 */
 public function prepareCertificate($login, $time = '') {
  $login = EfrontUser::convertArgumentToUserLogin($login);
  $courseUser = EfrontUserFactory :: factory($login);
  $userStats = EfrontStats :: getUsersCourseStatus($this, $login);
  $data = array('organization' => $GLOBALS['configuration']['site_name'],
      'course_name' => $this -> course['name'],
      'user_surname' => $courseUser -> user['surname'],
      'user_name' => $courseUser -> user['name'],
      'serial_number' => md5(uniqid(mt_rand(), true)),
      'grade' => $userStats[$this -> course['id']][$login]['score'],
      'date' => $time != '' ? $time : time());
  $data = serialize($data);
  $modules = eF_loadAllModules();
  foreach ($modules as $module) {
   $module -> onPrepareCourseCertificate($login, $this -> course['id'], $data);
  }
  return $data;
 }
 /**
	 * Get the course certificate
	 *
	 * This function is used to retrieve the certificate template
	 * used in the course
	 * <br/>Example:
	 * <code>
	 * $course -> getCertificate();                             //Retuns the lesson certificate template
	 * </code>
	 *
	 * @return string The course certificate
	 * @since 3.5.0
	 * @access public
	 */
 public function getCertificate() {
  if (!$this -> options['certificate'] && is_file(G_CURRENTTHEMEPATH."templates/certificate-".$this -> course['languages_NAME'].".tpl")) {
   $certificate = file_get_contents(G_CURRENTTHEMEPATH."templates/certificate-".$this -> course['languages_NAME'].".tpl");
  } elseif ($this -> options['certificate']) {
   $certificate = $this -> options['certificate'];
  } else {
   $certificate = _DEFAULTCERTIFICATE;
  }
  return $certificate;
 }
 /**
	 * Set the course certificate
	 *
	 * This function is used to set the course certificate
	 * template.
	 * <br/>Example:
	 * <code>
	 * $course -> setCertificate($certificate);
	 * </code>
	 *
	 * @param string $certificate The course certificate template
	 * @since 3.5.0
	 * @access public
	 * @todo check parameter
	 */
 public function setCertificate($certificate) {
  $this -> options['certificate'] = $certificate;
  $this -> persist();
 }
 /**
	 * Convert course to HTML list
	 *
	 * This function converts the course to an HTML list
	 * with the lessons it contains.
	 * <br/>Example:
	 * <code>
	 * $course -> toHTML();
	 * </code>
	 *
	 * @param array $userInfo User information to customize data for
	 * @param $options Specific display options
	 * @return string The HTML code of the course list
	 * @since 3.5.0
	 * @access public
	 * @todo convert to smarty template
	 */
 public function toHTML($lessons = false, $options = array(), $checkLessons = array(), $meets_depends_on_criteria = true) {
  !isset($options['courses_link']) ? $options['courses_link'] = false : null;
  !isset($options['lessons_link']) ? $options['lessons_link'] = false : null;
 /*	if (isset($options['collapse']) && $options['collapse'] == 2) {
			$display 			= '';
			$display_lessons 	= 'style = "display:none"';
			$imageString = 'down';
		} elseif (isset($options['collapse']) && $options['collapse'] == 1) {
			$display 			= 'style = "display:none"';
			$display_lessons 	= 'style = "display:none"';
			$classString = ' class = "visible" ';
			$imageString = 'up';
		} else {
			$display 			= '';
			$display_lessons 	= '';
			$classString = ' class = "visible" ';
			$imageString = 'up';
		}
	*/
  $display = '';
  $display_lessons = '';
  $classString = ' class = "visible" ';
  $imageString = 'up';
  $roles = EfrontLessonUser :: getLessonsRoles();
  $roleNames = EfrontLessonUser :: getLessonsRoles(true);
  if ($this -> course['user_type']) {
   $roleBasicType = $roles[$this -> course['user_type']]; //The basic type of the user's role in the course
  } else {
   $roleBasicType = null;
  }
  $courseLessons = $this -> getCourseLessons();
  if ($lessons) {
   foreach ($courseLessons as $key => $value) {
    $courseLessons[$key] -> lesson = array_merge($lessons[$key] -> lesson, $courseLessons[$key] -> lesson);
   }
  }
  if ($roleBasicType == 'student') { // fixed to apply checkRules to sub-student user types
   if (empty($checkLessons)) {
    $checkLessons = $courseLessons;
   } else {
    foreach ($courseLessons as $key => $value) {
     if (isset($checkLessons[$key])) {
      $checkLessons[$key]->lesson['start_date'] = $value->lesson['start_date'];
      $checkLessons[$key]->lesson['end_date'] = $value->lesson['end_date'];
      $temp[$key] = $checkLessons[$key]; //bring them in the correct order
     }
    }
    $checkLessons = $temp;
   }
   $eligible = $this -> checkRules($this -> course['users_LOGIN'], $checkLessons);
  } else {
   if (sizeof($courseLessons) > 0) {
    $eligible = array_combine(array_keys($courseLessons), array_fill(0, sizeof($courseLessons), 1)); //All lessons set to true
   } else {
    $eligible = array();
   }
  }
  //$eligible = array_combine(array_keys($this -> getLessons()), array_fill(0, sizeof($this -> getLessons()), 1));    //All lessons set to true
  foreach ($eligible as $lessonId => $value) {
   $eligible[$lessonId] = $courseLessons[$lessonId];
   $eligible[$lessonId] -> eligible = $value;
  }
  $courseString = '
                        <table class = "coursesTable" >
                            <tr class = "lessonsList" >
                             <td>
                                    <img id = "course_img'.$this -> course['id'].'" src = "images/32x32/courses.png">';
  if (!isset($this -> course['from_timestamp']) || $this -> course['from_timestamp']) {
   if ($options['courses_link'] && $options['courses_link'] === true) {
    $coursesLink = basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$this -> course['id'].'&op=course_info';
   } else if ($options['courses_link']) {
    $coursesLink = str_replace("#user_type#", $roleBasicType, $options['courses_link']).$this -> course['id'];
   } elseif ($options['tooltip']) {
    $coursesLink = 'javascript:void(0)';
   } else {
    $coursesLink = '';
   }
   if ($GLOBALS['configuration']['disable_tooltip'] != 1) {
    if ($options['tooltip']) {
     $courseString .= '
          <a href = "'.$coursesLink.'" class = "info" url = "ask_information.php?courses_ID='.$this -> course['id'].'" >
           <span class = "listName">'.$this -> course['name'].'</span>
          </a>';
    } else {
     $options['courses_link'] ? $courseString .= '<a href = "'.$coursesLink.'">'.$courseString .= $this -> course['name'].'</a>' : $courseString .= $this -> course['name'];
    }
   } else {
    $courseString .= $this -> course['name'];
   }
  } else {
   $courseString .= '<a href = "javascript:void(0)" class = "inactiveLink" title = "'._CONFIRMATIONPEDINGFROMADMIN.'">'.$this -> course['name'].'</a>';
  }
  if ($this -> course['different_role']) {
   $courseString .= '<span class = "courseRole">&nbsp;('.$roleNames[$this -> course['user_type']].')</span>';
  }
  if ($this -> course['start_date'] > time()) {
   $courseString .= '<span style = "vertical-align:middle">&nbsp;('._COURSESTARTSAT.' '.formatTimestamp($this -> course['start_date'], 'time_nosec').')</span>';
   if ($roleBasicType == 'student') {
    foreach ($eligible as $lessonId => $value) {
     $eligible[$lessonId] -> eligible = false;
    }
   }
  }
  elseif (!is_null($this -> course['remaining']) && $roleBasicType == 'student') {
   if ($this -> course['remaining'] > 0) {
    $courseString .= '<span style = "vertical-align:middle">&nbsp;('.eF_convertIntervalToTime($this -> course['remaining'], true).' '.mb_strtolower(_REMAINING).')</span>';
   } else {
    $courseString .= '<span style = "vertical-align:middle">&nbsp;('._ACCESSEXPIRED.')</span>';
    foreach ($eligible as $lessonId => $value) {
     $eligible[$lessonId] -> eligible = false;
    }
   }
  }
  if (!$meets_depends_on_criteria && $this->course['depends_on']) {
   foreach ($eligible as $lessonId => $value) {
    $eligible[$lessonId] -> eligible = false;
   }
  }
  $courseOptions = array();
  if ($roleBasicType == 'professor') {
   if (!isset($GLOBALS['currentUser'] -> coreAccess['course_settings']) || $GLOBALS['currentUser'] -> coreAccess['course_settings'] != 'hidden') {
    $autocompleteImage = '16x16/certificate.png';
     $autocompleteImage = '16x16/autocomplete.png';
    $courseOptions['information'] = '<img src = "images/16x16/information.png" title = "'._COURSEINFORMATION.'" alt = "'._COURSEINFORMATION.'" class = "ajaxHandle" onclick = "location = \''.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$this -> course['id'].'&op=course_info\'" />&nbsp;';
    $courseOptions['completion'] = '<img src = "images/'.$autocompleteImage.'" title = "'._COMPLETION.'" alt = "'._COMPLETION.'" class = "ajaxHandle" onclick = "location = \''.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$this -> course['id'].'&op=course_certificates\'" />&nbsp;';
    $courseOptions['rules'] = '<img src = "images/16x16/rules.png" title = "'._COURSERULES.'" alt = "'._COURSERULES.'" class = "ajaxHandle" onclick = "location=\''.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$this -> course['id'].'&op=course_rules\'" />&nbsp;';
    $courseOptions['order'] = '<img src = "images/16x16/order.png" title = "'._COURSEORDER.'" alt = "'._COURSEORDER.'" class = "ajaxHandle" onclick = "location=\''.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$this -> course['id'].'&op=course_order\'" />&nbsp;';
    $courseOptions['schedule'] = '<img src = "images/16x16/calendar.png" title = "'._COURSESCHEDULE.'" alt = "'._COURSESCHEDULE.'" class = "ajaxHandle" onclick = "location=\''.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$this -> course['id'].'&op=course_scheduling\'" />&nbsp;';
    if (!isset($GLOBALS['currentUser'] -> coreAccess['course_settings']) || $GLOBALS['currentUser'] -> coreAccess['course_settings'] == 'change') {
     $courseOptions['export'] = '<img src = "images/16x16/export.png" title = "'._EXPORTCOURSE.'" alt = "'._EXPORTCOURSE.'" class = "ajaxHandle" onclick = "location=\''.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$this -> course['id'].'&op=export_course\'" />&nbsp;';
     $courseOptions['import'] = '<img src = "images/16x16/import.png" title = "'._IMPORTCOURSE.'" alt = "'._IMPORTCOURSE.'" class = "ajaxHandle" onclick = "location=\''.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$this -> course['id'].'&op=import_course\'" />&nbsp;';
    }
    foreach ($GLOBALS['currentUser'] -> getModules() as $module) {
     if ($moduleTabPage = $module -> getTabPageSmartyTpl('course_settings')) {
      $courseOptions[$moduleTabPage['tab_page']] = '<img src = "'.$moduleTabPage['image'].'" title = "'.$moduleTabPage['title'].'" alt = "'.$moduleTabPage['title'].'" class = "ajaxHandle" onclick = "location=\''.basename($_SERVER['PHP_SELF']).'?ctg=lessons&course='.$this -> course['id'].'&op='.$moduleTabPage['tab_page'].'\'"/>&nbsp;';
     }
    }
    $courseString .= '<span style = "margin-left:30px">('._COURSEACTIONS.': '.implode('', $courseOptions).')</span>';
   }
  } else {
   if ($this -> course['completed']) {
    $courseOptions['completed'] = '<img src = "images/16x16/success.png" title = "'._COURSECOMPLETED.': '.formatTimestamp($this -> course['to_timestamp'], 'time').'" alt = "'._COURSECOMPLETED.': '.formatTimestamp($this -> course['to_timestamp'], 'time').'">&nbsp;';
   }
    if ($this -> course['issued_certificate']) {
     $dateTable = unserialize($this -> course['issued_certificate']);
     $certificateExportMethod = $this->options['certificate_export_method'];
    }
    $courseString .= '<span style = "margin-left:30px">'.implode('', $courseOptions).'</span>';
  }
  $courseString .= '
                                </td><td>';
  if (isset($options['buy_link']) && $options['buy_link'] && sizeof($this -> getInstances()) == 0 && !$this -> course['has_course'] && !$this -> course['reached_max_users'] && $_SESSION['s_type'] != 'administrator') {
   $this -> course['price'] ? $priceString = formatPrice($this -> course['price'], array($this -> options['recurring'], $this -> options['recurring_duration']), true) : $priceString = false;
   $courseString .= '
                         <span class = "buyLesson">
                                         <span onclick = "addToCart(this, \''.$this -> course['id'].'\', \'course\')">'.$priceString.'</span>
                          <img class = "ajaxHandle" src = "images/16x16/shopping_basket_add.png" alt = "'._BUY.'" title = "'._BUY.'" onclick = "addToCart(this, \''.$this -> course['id'].'\', \'course\')">
                                        </span>';
  }
  $courseString .= '
                                </td></tr>';
  if (sizeof($eligible) > 0) {
   //changed from subtree_course to subcoursetree because of #1332	
   $courseString .= '
                            <tr id = "subcoursetree'.$this -> course['id'].'" name = "default_visible" '.$display_lessons.'>
                                <td colspan = "2">
                                 <table>';
   foreach ($eligible as $lessonId => $lesson) {
    $roleBasicType = $roles[$lesson -> lesson['user_type']]; //The basic type of the user's role in the lesson
    $courseString .= '<tr class = "directionEntry">';
    if (isset($lesson -> lesson['active_in_lesson']) && !$lesson -> lesson['active_in_lesson']) {
     $courseString .= '<td style = "padding-bottom:2px"></td><td><a href = "javascript:void(0)" class = "inactiveLink" title = "'._CONFIRMATIONPEDINGFROMADMIN.'">'.$lesson -> lesson['name'].'</a></td>';
    } else if (!$lesson -> eligible) {
     if ($lesson -> lesson['completed']) {
      if ($lesson->options['show_percentage'] != 0) {
       $courseString .= '
       <td class = "lessonProgress">
                                <span class = "progressNumber completedLessonProgress" style = "width:50px;">&nbsp;</span>
                                <span class = "progressBar completedLessonProgress" style = "width:50px;text-align:center"><img src = "images/16x16/success.png" alt = "'._LESSONCOMPLETE.'" title = "'._LESSONCOMPLETE.'" /></span>
                                &nbsp;&nbsp;
                            </td>';
      } else {
       $courseString .= '
       <td class = "lessonProgress">
                            </td>';
      }
     } else {
      if ($lesson->options['show_percentage'] != 0) {
       $courseString .= '
       <td class = "lessonProgress">
                                <span class = "progressNumber incompletedLessonProgress" style = "width:50px;">'.$lesson -> lesson['overall_progress']['percentage'].'%</span>
                                <span class = "progressBar incompletedLessonProgress" style = "width:'.($lesson -> lesson['overall_progress']['percentage'] / 2).'px;">&nbsp;</span>
                                &nbsp;&nbsp;
                            </td>';
      } else {
       $courseString .= '
       <td class = "lessonProgress">
                            </td>';
      }
     }
     if ($GLOBALS['configuration']['disable_tooltip'] != 1) {
      $courseString .= '
       <td>
                             <a href = "javascript:void(0)" title = "" class = "inactiveLink info" url = "ask_information.php?lessons_ID='.$lesson -> lesson['id'].'&from_course='.$this -> course['id'].'">
                              '.$lesson -> lesson['name'].'
                             </a>
                            <td>';
     } else {
      $courseString .= '
       <td>
                             <a href = "javascript:void(0)" title = "" class = "inactiveLink">
                              '.$lesson -> lesson['name'].'
                             </a>
                            <td>';
     }
    } else {
     if ($lesson -> lesson['user_type'] && $roles[$lesson -> lesson['user_type']] == 'student' && $lesson -> lesson['completed']) { //Show the progress bar
      if ($lesson->options['show_percentage'] != 0) {
       $courseString .= '
       <td class = "lessonProgress">
                                <span class = "progressNumber completedLessonProgress" style = "width:50px;">&nbsp;</span>
                                <span class = "progressBar completedLessonProgress" style = "width:50px;text-align:center"><img src = "images/16x16/success.png" alt = "'._LESSONCOMPLETE.'" title = "'._LESSONCOMPLETE.'" style = "vertical-align:middle" /></span>
                                &nbsp;&nbsp;
                            </td>';
      } else {
       $courseString .= '
       <td class = "lessonProgress">
                            </td>';
      }
     } elseif ($lesson -> lesson['user_type'] && $roles[$lesson -> lesson['user_type']] == 'student') {
      if ($lesson->options['show_percentage'] != 0) {
       $courseString .= '
       <td class = "lessonProgress">
                                <span class = "progressNumber incompletedLessonProgress" style = "width:50px;">'.$lesson -> lesson['overall_progress']['percentage'].'%</span>
                                <span class = "progressBar incompletedLessonProgress" style = "width:'.($lesson -> lesson['overall_progress']['percentage'] / 2).'px;">&nbsp;</span>
                                &nbsp;&nbsp;
                            </td>';
      } else {
       $courseString .= '
       <td class = "lessonProgress">
                            </td>';
      }
     } else {
      $courseString .= '
       <td></td>';
     }
     if ($GLOBALS['configuration']['disable_tooltip'] != 1) {
      $courseString .= '
                      <td>
                       '.($options['lessons_link'] ? '<a href = "'.str_replace("#user_type#", $roleBasicType, $options['lessons_link']).$lesson -> lesson['id'].'&from_course='.$this -> course['id'].'" class = "info" url = "ask_information.php?lessons_ID='.$lesson -> lesson['id'].'&from_course='.$this -> course['id'].'">'.$lesson -> lesson['name'].'</a>' : $lesson -> lesson['name']).'
                                </td>';
     } else {
      $courseString .= '
                      <td>
                       '.($options['lessons_link'] ? '<a href = "'.str_replace("#user_type#", $roleBasicType, $options['lessons_link']).$lesson -> lesson['id'].'&from_course='.$this -> course['id'].'" >'.$lesson -> lesson['name'].'</a>' : $lesson -> lesson['name']).'
                                </td>';
     }
    }
    $courseString .= '';
   }
   $courseString .= '
        </tr>
                            </table>
                        </td></tr>';
  }
  $courseString .= '
                        </table>';
  return $courseString;
 }
 /**
	 * Get all branches: for the branches this course offers the course_ID value will be filled
	 *
	 * <br/>Example:
	 * <code>
	 * $branchesOfCourse = $course -> getBranches();
	 * </code>
	 *
	 * @param $only_own set true if only the branches of this course are to be returned and not all branches
	 * @return an array with branches where each record has the form [branch_ID] => [course_ID]
	 * @since 3.6.0
	 * @access public
	 * @todo refactor
	 */
 public function getBranches($only_own = false) {
  if (!isset($this -> branches) || !$this -> branches) {
   $this -> branches = false; //Initialize branches to something
   $branches = eF_getTableData("module_hcd_branch LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID LEFT OUTER JOIN module_hcd_course_to_branch ON (module_hcd_course_to_branch.branches_ID = module_hcd_branch.branch_ID AND module_hcd_course_to_branch.courses_ID='".$this -> course['id']."')", "module_hcd_branch.*, module_hcd_branch.branch_ID as branches_ID, module_hcd_course_to_branch.courses_ID, branch1.name as father","");
   foreach ($branches as $key => $branch) {
    if ($only_own && $branch['courses_ID'] != $this -> course['id']) {
     unset($branches[$key]);
    } else {
     $bID = $branch['branches_ID'];
     $this -> branches[$bID] = $branch;
    }
   }
  }
  return $this -> branches;
 }
 /* Return an array to be inputed as the contents of a select item
	 *
	 * This function is used to create a select with directions, lessons and courses
	 * categorized properly under a select item
	 *
	 * The values of the select are:
	 * course_<course_ID>		Course name
	 * lesson_<course_ID>_<lesson_ID>		Lesson name
	 *
	 * The categorization display is the following
	 * course C
	 * -- lesson in C
	 * -- lesson in C
	 *
	 * print an HTML representation of the HTML tree
	 * <br/>Example:
	 * <code>
	 * $course -> toSelect();
	 * </code>
	 *
	 * @return an array for inputing a categorized select for courses
	 * @since 3.5.2
	 * @access public
	 * @todo refactor
	 */
 public function toSelect() {
  $courseLessons = EfrontCourse::convertLessonObjectsToArrays($this->getCourseLessons());
  $eligible = $courseLessons;
  foreach ($courseLessons as $lessonId => $value) {
   $eligible[$lessonId] = new EfrontLesson($lessonId);
   $eligible[$lessonId] -> eligible = $value;
   if (!$eligible[$lessonId] -> lesson['active'] || !in_array($lessonId, array_keys($courseLessons))) {
    unset($eligible[$lessonId]); //Remove inactive lessons from list
   }
  }
  $courseArray = array();
  $courseArray['course_' . $this -> course['id']] = " " . $this->course['name'];
  if (sizeof($eligible) > 0) {
   foreach ($eligible as $lessonId => $lesson) {
    $courseArray['lesson_' . $this -> course['id'] . "_" . $lessonId] = "- " . $lesson -> lesson['name'];
   }
  }
  return $courseArray;
 }
 /**
	 * Get course information
	 *
	 * This function returns the course information in an array
	 * with attributes: 'general_description', 'assessment',
	 * 'objectives', 'lesson_topics', 'resources', 'other_info',
	 * as well as other information, including professors, lessons, etc.
	 *
	 * <br/>Example:
	 * <code>
	 * $info = $course -> getInformation();         //Get course information
	 * </code>
	 *
	 * @return array The lesson information
	 * @since 3.5.0
	 * @access public
	 * @todo refactor
	 */
 public function getInformation() {
  $information = array();
  if ($this -> course['info']) {
   $order = array("general_description", "objectives", "assessment", "lesson_topics", "resources", "other_info", "learning_method"); // for displaying fiels sorted
   $infoSorted = array();
   $unserialized = unserialize($this -> course['info']);
   foreach ($order as $value) {
    if ($unserialized[$value] != "") {
     $infoSorted[$value] = $unserialized[$value];
    }
   }
   $information = $infoSorted;
  }
  foreach ($this -> getUsers() as $key => $user) {
   if ($user['role'] == 'professor') {
    $information['professors'][$key] = $user;
   }
  }
  if (sizeof($instances = $this -> getInstances()) > 1) {
   $information['instances'] = sizeof($instances);
  } else {
   $information['lessons_number'] = $this -> countCourseLessons();
   $information['price_string'] = $this -> course['price_string'];
  }
  $information['language'] = $this -> course['languages_NAME'];
  $information['created'] = $this -> course['created'];
  return $information;
 }
 /**
	 * Export course
	 *
	 * This function is used to export the current course.
	 * The function recurively exports() the course's lessons, and
	 * then stores the course's data as well.
	 * <br/>Example:
	 * <code>
	 * $course = new EfrontCourse(13);						//Instantiate course with id 13
	 * $exportedFile = $course -> export();					//Export course
	 * </code>
	 *
	 * @return EfrontFile The exported file
	 * @since 3.5.2
	 * @access public
	 */
 public function export() {
  $courseTempDir = $this -> createCourseTempDirectory();
  $this -> exportCourseLessons($courseTempDir);
  $this -> exportDatabaseData($courseTempDir);
  $file = $this -> createCourseExportFile($courseTempDir);
  return $file;
 }
 private function createCourseTempDirectory() {
  $userTempDir = $this -> createUserTempDirectory();
  $courseTempDir = $userTempDir['path'].'/course_export_'.$this -> course['id']; //The compressed file will be moved to the user's temp directory
  if (is_dir($courseTempDir)) { //If the user's temp directory does not exist, create it
   $foo = new EfrontDirectory($courseTempDir);
   $foo -> delete();
  }
  $courseTempDir = EfrontDirectory :: createDirectory($courseTempDir, false);
  return $courseTempDir;
 }
 private function createUserTempDirectory() {
  $userTempDir = $GLOBALS['currentUser'] -> user['directory'].'/temp';
  if (!is_dir($userTempDir)) { //If the user's temp directory does not exist, create it
   $userTempDir = EfrontDirectory :: createDirectory($userTempDir, false);
  } else {
   $userTempDir = new EfrontDirectory($userTempDir);
  }
  return $userTempDir;
 }
 private function exportCourseLessons($courseTempDir) {
  $courseLessons = $this -> getCourseLessons();
  foreach ($courseLessons as $id => $lesson) {
   $exportedFile = $lesson -> export('all', false);
   $exportedFile -> copy($courseTempDir['path']);
  }
 }
 private function exportDatabaseData($courseTempDir) {
  $data = array();
  $data['courses'] = eF_getTableData("courses", "*", "id=".$this -> course['id']);
  unset($data['courses'][0]['instance_source']);
  foreach ($this -> getCourseLessons() as $value) {
   $data['lessons_to_courses'][] = array('courses_ID' => $this -> course['id'], 'lessons_ID' => $value->lesson['id']);
  }
  $modules = eF_loadAllModules();
  foreach ($modules as $module) {
   if ($moduleData = $module -> onExportCourse($this -> course['id'])) {
    $data[$module -> className] = $moduleData;
   }
  }
  file_put_contents($courseTempDir['path'].'/data.dat', serialize($data));
 }
 private function createCourseExportFile($courseTempDir) {
  $userTempDir = new EfrontDirectory($GLOBALS['currentUser'] -> user['directory'].'/temp');
  $file = $courseTempDir -> compress($this -> course['id'].'_exported.zip', false); //Compress the lesson files
  $newList = FileSystemTree :: importFiles($file['path']); //Import the file to the database, so we can download it
  $file = new EfrontFile(current($newList));
  $newFileName = EfrontFile :: encode($this -> course['name']).'.zip';
  if (!eF_checkParameter($newFileName, 'file')) {
   $newFileName = $file['name'];
  }
        $newFileName = str_replace(array('"', '>', '<', '*', '?', ':'), array('&quot;', '&gt;', '&lt;', '&#42;', '&#63;', '&#58;'), $newFileName);
  //$file -> rename($userTempDir['path'].'/'.$newFileName, true);
  //changed because of checkFile in rename
        rename($file['path'], $userTempDir['path'].'/'.$newFileName);
  FileSystemTree :: importFiles($userTempDir['path'].'/'.$newFileName);
  $returnFile = new EfrontFile($userTempDir['path'].'/'.$newFileName);
  //$file   -> rename($userTempDir['path'].'/'.EfrontFile :: encode($this -> course['name']).'.zip', true);
  $courseTempDir -> delete();
  return $returnFile;
 }
 /**
	 * Import course
	 *
	 * This function is used to import a previously exported course
	 * <br/>Example:
	 * <code>
	 * $course = new EfrontCourse(23);					//Instantiate course with id 23
	 * $file = $course -> export();						//Export course to a file
	 * $newCourse = new EfrontCourse(43);				//Instantiate course with id 43
	 * $newCourse -> import($file);						//Import course data from file, deleting existing lessons
	 * $newCourse -> import($file, false);				//Import course data from file, retaining existing lessons
	 * </code>
	 *
	 * @param mixed $file An EfrontFile object or a path to a file to uncompress
	 * @param boolean $removeLessons Whether to remove existing lessons from course prior to importing data
	 * @param boolean $courseProperties Whether to import course properties as well
	 * @return EfrontCourse The course itself
	 * @since 3.5.2
	 * @access public
	 */
 public function import($courseFile, $removeLessons = true, $courseProperties = false) {
  $data = $this -> getCourseDataFromExportedFile($courseFile);
  if ($courseProperties) {
   $this -> mergeCourseProperties($data);
  }
  if ($removeLessons) {
   $this -> removeLessons(array_keys($this -> getCourseLessons()));
  }
  $this -> importLessonsToCourse($data, $courseFile);
  // MODULES - Import module data
  // Get all modules (NOT only the ones that have to do with the user type)
  $modules = eF_loadAllModules();
  foreach ($modules as $module) {
   if (isset($data[$module->className])) {
    $module -> onImportCourse($this -> course['id'], $data[$module->className]);
    unset($data[$module->className]);
   }
  }
  $courseFile -> delete();
  return $course;
 }
 /**
	 * Uncompress exported file and get data
	 *
	 * This function uncompresses the exported course file and reads the serialized database
	 * data into an array
	 *
	 * @param EfrontFile $file The exported file
	 * @return array The serialized course data
	 * @since 3.6.1
	 * @access private
	 */
 private function getCourseDataFromExportedFile($file) {
  $fileList = $file -> uncompress();
  $fileList = array_unique(array_reverse($fileList, true));
  $dataFile = new EfrontFile($file['directory'].'/data.dat');
  $filedata = file_get_contents($dataFile['path']);
  $dataFile -> delete();
  $data = unserialize($filedata);
  unset($data['courses'][0]['id']);
  unset($data['courses'][0]['instance_source']);
  return $data;
 }
 /**
	 * Merge current course properties with exported course properties
	 *
	 * @param array $data The exported course data
	 * @since 3.6.1
	 * @access private
	 */
 private function mergeCourseProperties($data) {
  unset($data['courses'][0]['directions_ID']);
  unset($data['courses'][0]['created']);
  $this -> course = array_merge($this -> course, $data['courses'][0]);
  $this -> options = unserialize($data['courses'][0]['options']);
  $this -> rules = unserialize($data['courses'][0]['rules']);
  $this -> persist();
 }
 /**
	 * Import exported lessons to course
	 *
	 * @param array $data The exported course data
	 * @param EfrontFile $courseFile The file of the exported course
	 * @since 3.6.1
	 * @access private
	 */
 private function importLessonsToCourse($data, $courseFile) {
  $data['lessons_to_courses'] = $this -> setCorrectLessonOrder($data['lessons_to_courses']);
  foreach ($data['lessons_to_courses'] as $value) {
   $lesson = EfrontLesson :: createLesson(array('name' => 'imported_lesson', //This is changed right below, during import
                'course_only' => true,
                'directions_ID' => $this -> course['directions_ID']));
   $lessonFile = new EfrontFile($courseFile['directory'].'/'.$value['lessons_ID'].'_exported.zip');
   $lessonFile = $lessonFile -> copy($lesson -> getDirectory());
   $lesson -> import($lessonFile, false, false, true);
   $this -> addLessons($lesson);
   $this -> replaceLessonInCourseRules($value['lessons_ID'], $lesson);
  }
 }
 private function setCorrectLessonOrder($lessonsToCourses) {
  foreach ($lessonsToCourses as $value) {
   $lessons[$value['lessons_ID']] = $value;
   $previous[$value['lessons_ID']] = $value['previous_lessons_ID'];
  }
  $count = $current = 0;
  $orderedLessons = array();
  while (sizeof ($previous) > 0 && $count++ < 1000) {
   $current = array_search($current, $previous);
   $orderedLessons[] = $lessons[$current];
   unset($previous[$current]);
  }
  if (sizeof($orderedLessons) != sizeof($lessons)) {
   $orderedLessons = $lessonsToCourses;
  }
  return $orderedLessons;
 }
 /**
	 * Print a link with tooltip
	 *
	 * This function is used to print a course link with a popup tooltip
	 * containing information on this lesson. The link must be provided
	 * and optionally the information.
	 * <br/>Example:
	 * <code>
	 * echo $course -> toHTMLTooltipLink('javascript:void(0)');
	 * </code>
	 *
	 * @param string $link The link to print
	 * @param array $courseInformation The information to display (According to the EfrontCourse :: getInformation() format)
	 * @since 3.5.0
	 * @access public
	 * @todo refactor
	 */
 public function toHTMLTooltipLink($link, $courseInformation = false) {
  if ($GLOBALS['configuration']['disable_tooltip'] != 1) {
   if (!$courseInformation) {
    $courseInformation = $this -> getInformation();
   }
   if (!$link) {
    $link = 'javascript:void(0)';
   }
   if (isset($courseInformation['professors'])) {
    foreach ($courseInformation['professors'] as $value) {
     $professorsString[] = $value['name'].' '.$value['surname'];
    }
    $courseInformation['professors'] = implode(", ", $professorsString);
   }
   $tooltipInfo = array();
   foreach ($courseInformation as $key => $value) {
    if ($value) {
     switch ($key) {
      case 'professors' : $tooltipInfo[] = '<strong>'._PROFESSORS."</strong>: $value<br/>"; break;
      case 'lessons_number' : $tooltipInfo[] = '<strong>'._LESSONS."</strong>: $value<br/>"; break;
      case 'instances' : $tooltipInfo[] = '<strong>'._COURSEINSTANCES."</strong>: $value<br/>"; break;
      case 'general_description': $tooltipInfo[] = '<strong>'._DESCRIPTION."</strong>: $value<br/>"; break;
      case 'assessment' : $tooltipInfo[] = '<strong>'._ASSESSMENT."</strong>: $value<br/>"; break;
      case 'objectives' : $tooltipInfo[] = '<strong>'._OBJECTIVES."</strong>: $value<br/>"; break;
      case 'lesson_topics' : $tooltipInfo[] = '<strong>'._COURSETOPICS."</strong>: $value<br/>"; break;
      case 'resources' : $tooltipInfo[] = '<strong>'._RESOURCES."</strong>: $value<br/>"; break;
      case 'other_info' : $tooltipInfo[] = '<strong>'._OTHERINFO."</strong>: $value<br/>"; break;
      default: break;
     }
    }
   }
   $classes = array();
   if (sizeof($tooltipInfo) > 0) {
    $classes[] = 'info';
    $tooltipString = '
     <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;">
      '.$this -> course['name'].'
      <span class = "tooltipSpan">'.implode("", $tooltipInfo).'</span></a>';
   } else {
    $tooltipString = '
     <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;">
      '.$this -> course['name'].'</a>';
   }
  } else {
   $tooltipString = '
     <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;">
      '.$this -> course['name'].'</a>';
  }
  return $tooltipString;
 }
 /**
	 * Create new course
	 *
	 * Create a new course based on the specified $fields
	 * <br/>Example:
	 * <code>
	 * $fields = array('name' => 'new course', 'languages_NAME' => 'english');
	 * $course = EfrontCourse :: createCourse($fields);
	 * </code>
	 *
	 * @param array $fields The new fields
	 * @return EfrontCourse the new course
	 * @since 3.5.0
	 * @access public
	 */
 public static function createCourse($fields) {
  $fields['metadata'] = self::createCourseMetadata($fields);
  $fields = self::validateAndSanitizeCourseFields($fields);
  isset($fields['creator_LOGIN']) OR $fields['creator_LOGIN'] = $_SESSION['s_login'];
  $newId = eF_insertTableData("courses", $fields);
  // Insert the corresponding lesson skill to the skill and lesson_offers_skill tables. Automatic skill generation only for the educational version
  EfrontSearch :: insertText($fields['name'], $newId, "courses", "title");
  $course = new EfrontCourse($newId);
  self::notifyModuleListenersForCourseCreation($course);
  return $course;
 }
 private static function notifyModuleListenersForCourseCreation($course) {//PROTONC
  // Get all modules (NOT only the ones that have to do with the user type)
  $modules = eF_loadAllModules();
  // Trigger all necessary events. If the function has not been re-defined in the derived module class, nothing will happen
  foreach ($modules as $module) {
   $module -> onNewCourse($course -> course['id']);
  }
 }
 /**
	 * Create course metadata
	 *
	 * @param array $fields Course properties
	 * @return string Serialized representation of metadata array
	 * @since 3.6.1
	 * @access private
	 */
 private static function createCourseMetadata($fields) {
  $languages = EfrontSystem :: getLanguages(true);
  $courseMetadata = array('title' => $fields['name'],
                                'creator' => formatLogin($GLOBALS['currentUser'] -> user['login']),
                                'publisher' => formatLogin($GLOBALS['currentUser'] -> user['login']),
                                'contributor' => formatLogin($GLOBALS['currentUser'] -> user['login']),
                                'date' => date("Y/m/d", time()),
                                'language' => $languages[$fields['languages_NAME']],
                                'type' => 'course');
  $metadata = serialize($courseMetadata);
  return $metadata;
 }
 /**
	 * Delete course (statically)
	 *
	 * This function is used to delete an existing course. In order to do
	 * this, it caclulates all the course dependendant elements, deletes them
	 * and finally deletes the course itself. This function is the same as
	 * Efrontcourse :: delete(), except that it is called statically, so it
	 * instatiates first the course objects and then calls delete() on it.
	 * Alternatively, $course may be already a course object.
	 * <br/>Example:
	 * <code>
	 * try {
	 *   EfrontCourse :: delete(32);                     //32 is the course id
	 * } catch (Exception $e) {
	 *   echo $e -> getMessage();
	 * }
	 * </code>
	 *
	 * @param mixed $course The course id or a course object
	 * @return boolean True if everything is ok
	 * @since 3.5.0
	 * @access public
	 * @static
	 * @todo remove - deprecated
	 */
 public static function deleteCourse($course) {
  if (!($course instanceof EfrontCourse)) {
   $course = new EfrontCourse($course);
  }
  return $course -> delete();
 }
 /**
	 * Get system courses
	 *
	 * This function is used used to return a list with all the system
	 * lessons.
	 * <br/>Example:
	 * <code>
	 * $lessons = EFrontCourse :: getCourses();
	 * </code>
	 *
	 * @param boolean $returnObjects whether to return EfrontCourse objects
	 * @return array The lessons list
	 * @since 3.5.0
	 * @access public
	 * @static
	 * @todo deprecated
	 */
 public static function getCourses($returnObjects = false) {
  //$result = eF_getTableData("courses c, directions d", "c.*, d.name as direction_name, (select count( * ) from courses l where instance_source=c.id) as has_instances", "c.directions_ID=d.id and archive=0 and instance_source=0");
  $result = eF_getTableData("courses c", "c.*, (select count( * ) from courses l where instance_source=c.id) as has_instances", "archive=0 and instance_source=0");
  foreach ($result as $value) {
   $returnObjects ? $courses[$value['id']] = new EfrontCourse($value) : $courses[$value['id']] = $value;
  }
  return $courses;
 }
 public static function getCoursesWithPendingUsers($constraints = array()) {
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $where[] = "uc.courses_ID=c.id and uc.from_timestamp=0";
  $where[] = "uc.users_LOGIN=u.login and u.archive=0 and uc.archive=0";
  $result = eF_getTableData("users u, courses c, users_to_courses uc", "c.*, uc.users_LOGIN", implode(" and ", $where), $orderby, $groupby, $limit);
  return $result;
 }
 public static function getCoursesWithPendingUsersForSupervisor($constraints = array(), $supervisor) {
  $supervisor = EfrontUser::convertArgumentToUserLogin($supervisor);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $where[] = "uc.courses_ID=c.id and uc.from_timestamp=0";
  $where[] = "uc.users_LOGIN=u.login and u.archive=0";
  $where[] = "c.supervisor_LOGIN='$supervisor'";
  $result = eF_getTableData("users u, courses c, users_to_courses uc", "c.*, uc.users_LOGIN", implode(" and ", $where), $orderby, $groupby, $limit);
  return $result;
 }
 /**
	 * @todo: Return num_lessons
	 */
 public static function getCoursesWithSpecificUserParticipation($constraints = array(), $login) {
  $login = EfrontUser::convertArgumentToUserLogin($login);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $innerQuery = "select courses_ID,completed,score,user_type,from_timestamp as active_in_course from users_to_courses where archive=0 and users_login='".$login."'";
  $result = eF_getTableData("courses c left outer join ($innerQuery) r on c.id=r.courses_ID", "c.*,r.*,r.courses_ID is not null as has_course, (select count( * ) from courses l where instance_source=c.id) as has_instances", implode(" and ", $where), $orderby, $groupby, $limit);
  return self :: convertDatabaseResultToCourseObjects($result);
 }
 public static function getCoursesWithSpecificGroupParticipation($constraints = array(), $group) {
  $group = self :: convertArgumentToGroupId($group);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $innerQuery = "select courses_ID,user_type from courses_to_groups where groups_ID='".$group."'";
  $result = eF_getTableData("courses c left outer join ($innerQuery) r on c.id=r.courses_ID", "c.*,r.*,r.courses_ID is not null as has_course, (select count( * ) from courses l where instance_source=c.id) as has_instances", implode(" and ", $where), $orderby, $groupby, $limit);
  return self :: convertDatabaseResultToCourseObjects($result);
 }
 private static function setCourseUserSelection(&$constraints = array()) {
  //"(select count( * ) from users_to_courses uc, users u where uc.courses_ID=c.id and u.archive=0 and u.active=1 and uc.archive=0 and u.login=uc.users_LOGIN and u.user_type='student') as num_students";
  if (empty($constraints['table_filters'])) {
   return "(select count( * ) from users_to_courses uc, users u where uc.courses_ID=c.id and u.archive=0 and u.active=1 and uc.archive=0 and u.login=uc.users_LOGIN and u.user_type='student') as num_students";
  } else {
   list($where, $limit, $orderby) = EfrontCourse :: convertCourseUserConstraintsToSqlParameters($constraints);
   $from = "users_to_courses uc, users u";
   $from = EfrontCourse :: appendTableFiltersUserConstraints($from, $constraints);
   $where[] = "uc.courses_ID=c.id";
   $where[] = "u.archive=0";
   $where[] = "u.login=uc.users_LOGIN";
   $where[] = "u.user_type='student'";
   unset($constraints['table_filters']); // to avoid using the filters again in courses....
   return "(select count(*) FROM " . $from . " WHERE " . implode(" AND ", $where) . ") as num_students";
  }
 }
 public static function getAllCourses($constraints = array()) {
  $select['main'] = 'c.id';
  $select['has_instances'] = ""; //Must be here, even if empty
  $select['num_lessons'] = "(select count( * ) from lessons_to_courses cl, lessons l where cl.courses_ID=c.id and l.archive=0 and l.id=cl.lessons_ID) as num_lessons";
  $select['num_students'] = EfrontCourse :: setCourseUserSelection($constraints);
  $select = EfrontCourse :: convertCourseConstraintsToRequiredFields($constraints, $select);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  //$result = eF_getTableData("courses c", $select, implode(" and ", $where), $orderby, false, $limit);
  //WITH THIS NEW QUERY, WE GET THE SLOW 'has_instances' PROPERTY AFTER FILTERING
  $from = array("courses.*", "t.*");
  if (in_array('has_instances', array_keys($select))) {
   unset($select['has_instances']);
   $from[] = "(select count(id) from courses c1 where c1.instance_source=courses.id and c1.archive=0) as has_instances";
  }
  $sql = prepareGetTableData("courses c", implode(",", $select), implode(" and ", $where), $orderby, false, $limit);
  $result = eF_getTableData("courses, ($sql) t", implode(",", $from), "courses.id=t.id");
  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontCourse :: convertDatabaseResultToCourseObjects($result);
  } else {
   return EfrontCourse :: convertDatabaseResultToCourseArray($result);
  }
 }
 public static function countAllCourses($constraints = array()) {
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  //$where[] = "d.id=c.directions_ID";
  $result = eF_countTableData("courses c", "c.id", implode(" and ", $where));
  return $result[0]['count'];
 }
 public static function convertDatabaseResultToLessonObjects($result) {
  $lessonObjects = array();
  foreach ($result as $value) {
   $lessonObjects[$value['id']] = new EfrontLesson($value);
  }
  return $lessonObjects;
 }
 public static function convertDatabaseResultToLessonArray($result) {
  $lessonArray = array();
  foreach ($result as $value) {
   $lessonArray[$value['id']] = $value;
  }
  return $lessonArray;
 }
 public static function convertDatabaseResultToCourseObjects($result) {
  $courseObjects = array();
  foreach ($result as $value) {
   $courseObjects[$value['id']] = new EfrontCourse($value);
  }
  return $courseObjects;
 }
 public static function convertDatabaseResultToCourseArray($result) {
  $courseArray = array();
  foreach ($result as $value) {
   $courseArray[$value['id']] = $value;
  }
  return $courseArray;
 }
 public static function convertLessonObjectsToArrays($lessonObjects) {
  foreach ($lessonObjects as $key => $value) {
   if ($value instanceOf EfrontLesson) {
    $lessonObjects[$key] = $value -> lesson;
   }
  }
  return $lessonObjects;
 }
 public static function convertCourseObjectsToArrays($courseObjects) {
  foreach ($courseObjects as $key => $value) {
   if ($value instanceOf EfrontCourse) {
    $courseObjects[$key] = $value -> course;
   }
  }
  return $courseObjects;
 }
 public static function convertUserObjectsToArrays($userObjects) {
  foreach ($userObjects as $key => $value) {
   if ($value instanceOf EfrontUser) {
    $userObjects[$key] = $value -> user;
   }
  }
  return $userObjects;
 }
 public static function convertLessonConstraintsToSqlParameters($constraints) {
  $where = self::addWhereConditionToLessonConstraints($constraints);
  $limit = self::addLimitConditionToConstraints($constraints);
  $order = self::addSortOrderConditionToConstraints($constraints);
  return array($where, $limit, $order);
 }
 private static function addWhereConditionToLessonConstraints($constraints) {
  if (isset($constraints['archive'])) {
   $constraints['archive'] ? $where[] = 'l.archive!=0' : $where[] = 'l.archive=0';
  }
  if (isset($constraints['active'])) {
   $constraints['active'] ? $where[] = 'l.active=1' : $where[] = 'l.active=0';
  }
  if (isset($constraints['filter']) && eF_checkParameter($constraints['filter'], 'text')) {
   $result = eF_describeTable("lessons");
   $tableFields = array();
   foreach ($result as $value) {
    $tableFields[] = "l.".$value['Field'].' like "%'.$constraints['filter'].'%"';
   }
   $where[] = "(".implode(" OR ", $tableFields).")";
  }
  if (isset($constraints['condition'])) {
   $where[] = $constraints['condition'];
  }
  return $where;
 }
 /*
	 * Append the tables that are used from the statistics filters to the FROM table list
	 */
 public static function appendTableFiltersUserConstraints($from, $constraints) {
  if (isset($constraints['table_filters'])) {
   foreach ($constraints['table_filters'] as $constraint) {
    if (isset($constraint['table']) && isset($constraint['joinField'])) {
     $from .= " JOIN " . $constraint['table'] . " ON u.login = " . $constraint['joinField'];
    }
   }
  }
  return $from;
 }
 public function convertCourseConstraintsToRequiredFields($constraints, $select) {
  foreach ($select as $key => $value) {
   if ((!isset($constraints['required_fields']) || !in_array($key, $constraints['required_fields'])) && $key != 'main') {
    unset($select[$key]);
   }
  }
  return $select;
 }
 public static function convertCourseConstraintsToSqlParameters($constraints) {
  $where = self::addWhereConditionToCourseConstraints($constraints);
  $limit = self::addLimitConditionToConstraints($constraints);
  $order = self::addSortOrderConditionToConstraints($constraints);
  return array($where, $limit, $order);
 }
 private static function addWhereConditionToCourseConstraints($constraints) {
  $where = array();
  if (isset($constraints['archive'])) {
   $constraints['archive'] ? $where[] = 'c.archive!=0' : $where[] = 'c.archive=0';
  }
  if (isset($constraints['active'])) {
   $constraints['active'] ? $where[] = 'c.active=1' : $where[] = 'c.active=0';
  }
  if (isset($constraints['instance'])) {
   if ($constraints['instance'] === true) {
    $where[] = 'c.instance_source!=0';
   } else if ($constraints['instance'] == false) {
    $where[] = 'c.instance_source=0';
   } else if (eF_checkParameter($constraints['instance'], 'id')) {
    $where[] = '(c.instance_source='.$constraints['instance'].' or c.id='.$constraints['instance'].')';
   }
  }
  if (isset($constraints['filter']) && eF_checkParameter($constraints['filter'], 'text')) {
   $constraints['filter'] = trim(urldecode($constraints['filter']), "||||");
   $result = eF_describeTable("courses");
   $tableFields = array();
   foreach ($result as $value) {
    $tableFields[] = "c.".$value['Field'].' like "%'.$constraints['filter'].'%"';
   }
   $where[] = "(".implode(" OR ", $tableFields).")";
  }
  if (isset($constraints['condition'])) {
   $where[] = $constraints['condition'];
  }
  foreach ($constraints['table_filters'] as $constraint) {
   $where[] = $constraint['condition'];
  }
  return $where;
 }
 private static function addSortOrderConditionToConstraints($constraints) {
  $order = '';
  if (isset($constraints['sort']) && eF_checkParameter($constraints['sort'], 'alnum_with_spaces')) {
   $order = $constraints['sort'];
   if (isset($constraints['order']) && in_array($constraints['order'], array('asc', 'desc'))) {
    $order .= ' '.$constraints['order'];
   }
  }
  return $order;
 }
 private static function addLimitConditionToConstraints($constraints) {
  $limit = '';
  if (isset($constraints['limit']) && eF_checkParameter($constraints['limit'], 'int') && $constraints['limit'] > 0) {
   $limit = $constraints['limit'];
  }
  if ($limit && isset($constraints['offset']) && eF_checkParameter($constraints['offset'], 'int') && $constraints['offset'] >= 0) {
   $limit = $constraints['offset'].','.$limit;
  }
  return $limit;
 }
 /**
	 * Get all skills: for the skills this course offers the courses_ID value will be filled
	 *
	 * <br/>Example:
	 * <code>
	 * $skillsOffered = $course -> getSkills();
	 * </code>
	 *
	 * @param $only_own set true if only the skills of this course are to be returned and not all skills
	 * @return an array with skills where each record has the form [skill_ID] => [courses_ID, description, specification,skill_ID,categories_ID]
	 * @since 3.5.0
	 * @access public
	 */
 public function getSkills($only_own = false) {
  if (!isset($this -> skills) || !$this -> skills) {
   $this -> skills = false; //Initialize skills to something
   $skills = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_skill_categories ON module_hcd_skill_categories.id = module_hcd_skills.categories_ID LEFT OUTER JOIN module_hcd_course_offers_skill ON (module_hcd_course_offers_skill.skill_ID = module_hcd_skills.skill_ID AND module_hcd_course_offers_skill.courses_ID='".$this -> course['id']."')", "module_hcd_skills.description,specification, module_hcd_skills.skill_ID,courses_ID, categories_ID, module_hcd_skill_categories.description as category","");
   foreach ($skills as $key => $skill) {
    if ($only_own && $skill['courses_ID'] != $this -> course['id']) {
     unset($skills[$key]);
    } else {
     $skID = $skill['skill_ID'];
     $this -> skills[$skID] = $skill;
    }
   }
  }
  return $this -> skills;
 }
 /**
	 * Assign a skill to this course or update an existing skill description
	 *
	 * This function is used to correlate a skill to the course - if the
	 * course is completed then this skill is assigned to the user that completed it
	 *
	 * <br/>Example:
	 * <code>
	 * $course -> assignSkill(2, "Beginner PHP knowledge");   // The course will offer skill with id 2 and "Beginner PHP knowledge"
	 * </code>
	 *
	 * @param $skill_ID the id of the skill to be assigned
	 * @return boolean true/false
	 * @since 3.5.0
	 * @access public
	 */
 public function assignSkill($skill_ID, $specification) {
  $this -> getSkills();
  // Check if the skill is not assigned as offered by this course
  if ($this -> skills[$skill_ID]['courses_ID'] == "") {
   eF_insertTableData("module_hcd_course_offers_skill", array("skill_ID" => $skill_ID, "courses_ID" => $this -> course['id'], "specification" => $specification));
   $this -> skills[$skill_ID]['courses_ID'] = $this -> course['id'];
   $this -> skills[$skill_ID]['specification'] = $specification;
  } else {
   eF_updateTableData("module_hcd_course_offers_skill", array("specification" => $specification), "skill_ID = '".$skill_ID."' AND courses_ID = '". $this -> course['id'] ."'");
   $this -> skills[$skill_ID]['specification'] = $specification;
  }
  return true;
 }
 /**
	 * Remove a skill that is offered from this course
	 *
	 * This function is used to stop the correlation of a skill to the course - if the
	 * course is completed then this skill is assigned to the user that completed it
	 *
	 * <br/>Example:
	 * <code>
	 * $course -> removeSkill(2);   // The course will stop offering skill with id 2
	 * </code>
	 *
	 * @param $skill_ID the id of the skill to be removed from the skills to be offered list
	 * @return boolean true/false
	 * @since 3.5.0
	 * @access public
	 */
 public function removeSkill($skill_ID) {
  $this -> getSkills();
  // Check if the skill is not assigned as offered by this course
  if ($this -> skills[$skill_ID]['courses_ID'] == $this -> course['id']) {
   eF_deleteTableData("module_hcd_course_offers_skill", "skill_ID = '".$skill_ID."' AND courses_ID = '". $this -> course['id'] ."'");
   $this -> skills[$skill_ID]['specification'] = "";
   $this -> skills[$skill_ID]['courses_ID'] = "";
  }
  return true;
 }
 /**
	 * Assign a branch to this course
	 *
	 * This function is used to correlate a branch to the course
	 * All users of the branch should be assigned to this course
	 *
	 * <br/>Example:
	 * <code>
	 * $course -> assignBranch(2);   // The course will be assigned to branch with id 2
	 * </code>
	 *
	 * @param $branch_ID the id of the branch to be assigned
	 * @return boolean true/false
	 * @since 3.6.0
	 * @access public
	 */
 public function assignBranch($branch_ID) {
  $this -> getBranches();
  // Check if the branch is not assigned as offered by this course
  if ($this -> branches[$branch_ID]['courses_ID'] == "") {
   eF_insertTableData("module_hcd_course_to_branch", array("branches_ID" => $branch_ID, "courses_ID" => $this -> course['id']));
   $this -> branches[$branch_ID]['courses_ID'] = $this -> course['id'];
   $newBranch = new EfrontBranch($branch_ID);
   $employees = $newBranch ->getEmployees(false,true); //get data flat
   $this -> addUsers($employees['login'], $employees['user_type']);
  }
  return true;
 }
 /**
	 * Remove association of a branch with this course
	 *
	 * This function is used to stop the correlation of a branch to the course
	 *
	 * <br/>Example:
	 * <code>
	 * $course -> removeBranch(2);   // The course will stop offering branch with id 2
	 * </code>
	 *
	 * @param $branch_ID the id of the branch to be removed from the course
	 * @return boolean true/false
	 * @since 3.6.0
	 * @access public
	 */
 public function removeBranch($branch_ID) {
  $this -> getBranches();
  // Check if the branch is not assigned as offered by this course
  if ($this -> branches[$branch_ID]['courses_ID'] == $this -> course['id']) {
   eF_deleteTableData("module_hcd_course_to_branch", "branches_ID = '".$branch_ID."' AND courses_ID = '". $this -> course['id'] ."'");
   $this -> branches[$branch_ID]['courses_ID'] = "";
  }
  return true;
 }
 /**
	 * Get course instances
	 *
	 * This function is used to return the course instances, which are special courses that
	 * derive from a parent course
	 * <br>Example:
	 * <code>
	 * $course = new EfrontCourse(43);
	 * $instances = $course -> getInstances();	//Return an array of EfrontCourse objects, where keys are the ids
	 * </code>
	 *
	 * @return array An array of EfrontCourse objects
	 * @since 3.6.1
	 * @access public
	 */
 public function getInstances($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false);
  $constraints['instance'] = $this -> course['id'];
  $constraints['required_fields'] = array('num_students', 'num_lessons', 'num_skills', 'location');
  $courseInstances = self :: getAllCourses($constraints);
/*
#ifdef ENTERPRISE
			$result 	  = eF_getTableData("module_hcd_course_to_branch cb, module_hcd_branch b", "cb.branches_ID, cb.courses_ID, b.name", "b.branch_ID=cb.branches_ID");
			$branchResult = array();
			foreach ($result as $value) {
				$branchResult[$value['courses_ID']][$value['branches_ID']] = $value['name'];
			}
			foreach ($courseInstances as $key => $course) {
				$courseInstances[$key] -> branches = $branchResult[$course -> course['id']];
				$courseInstances[$key] -> course['branch_name'] = implode(",", $courseInstances[$key] -> branches);
			}
#endif
*/
  return $courseInstances;
 }
 /**
	 * Get course instances
	 *
	 * This function is used to return the course instances, which are special courses that
	 * derive from a parent course
	 * <br>Example:
	 * <code>
	 * $course = new EfrontCourse(43);
	 * $instances = $course -> getInstances();	//Return an array of EfrontCourse objects, where keys are the ids
	 * </code>
	 *
	 * @return array An array of EfrontCourse objects
	 * @since 3.6.1
	 * @access public
	 */
 public function countCourseInstances($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  $constraints['instance'] = $this -> course['id'];
  //$constraints['required_fields'] = array('num_students', 'num_lessons', 'num_skills', 'location');
  $courseInstancesNum = self :: countAllCourses($constraints);
  return $courseInstancesNum;
 }
 /**
	 * Set the lesson mode to "unique" or "shared" to the course
	 *
	 * Algorithm explanation:
	 * A lesson in a course may either be "shared" or "unique".
	 * "Shared" means that the very same lesson is shared across all courses that it's part of. If the lesson with id '35'
	 * is part of courses A, B and C, then all of them reference the id 35.
	 * "Unique" means that as soon as the lesson is set to this mode, the original lesson, with id 35, is cloned (instance) and a new lesson
	 * is created, for example with id 246. This lesson is then assigned to the course. The only common with the "parent" lesson, 35,
	 * is that it shares the same folder. Other than that, it's completely separated
	 * When a "unique" lesson is created, then two additional fields are populated: "instance_source" and "originating_course".
	 * The former indicates the lesson from which this instance was derived. The latter indicates which course it was created for.
	 * This way, if we continuously change the lesson mode from shared to unique, there will not be new instances created, but the existing one
	 * will be used instead
	 *
	 *
	 * @param mixed $lesson An EfrontLesson or a lesson id
	 * @param string $mode 'unique' or 'shared'
	 * @since 3.6.1
	 * @access public
	 */
 public function setLessonMode($lesson, $mode) {
  ($lesson instanceof EfrontLesson) OR $lesson = new EfrontLesson($lesson);
  if ($mode == 'unique') {
   $this -> setUniqueLessonMode($lesson);
  } else if ($mode == 'shared') {
   $this -> setSharedLessonMode($lesson);
  }
 }
 /**
	 * Set the lesson mode to 'unique' in the course
	 *
	 * @param EfrontLesson $lesson The lesson to set the mode for
	 * @since 3.6.1
	 * @access private
	 */
 private function setUniqueLessonMode($lesson) {
  $courseUsers = $this -> countCourseUsers(array('archive' => false));
  if ($courseUsers['count'] > 0) {
   throw new Exception(_YOUCANNOTCHANGEMODECOURSENOTEMPTY, EfrontCourseException::COURSE_NOT_EMPTY);
  }
  //First, search for any instances that where already defined for this lesson and course in the past
  $result = eF_getTableData("lessons", "*", "instance_source=".$lesson -> lesson['id']." and originating_course=".$this -> course['id']);
  if (sizeof($result) > 0) {
   $lessonInstance = new EfrontLesson($result[0]);
   $this -> addLessons($lessonInstance);
  } else {
   $lessonInstance = EfrontLesson :: createInstance($lesson, $this);
   $this -> addLessons($lessonInstance);
  }
  $this -> replaceLessonInCourseOrder($lesson, $lessonInstance);
  $this -> replaceLessonInCourseRules($lesson, $lessonInstance); //Must be put *before* removeLessons()
  //$this -> removeLessons($lesson);		//commented out because it was messing up with order
  eF_deleteTableData("lessons_to_courses", "courses_ID=".$this -> course['id']." and lessons_ID=".$lesson->lesson['id']);
 }
 /**
	 * Set the lesson mode to 'shared' in the course
	 *
	 * @param EfrontLesson $lesson The lesson to set the mode for
	 * @since 3.6.1
	 * @access private
	 */
 private function setSharedLessonMode($lesson) {
  $courseUsers = $this -> countCourseUsers(array('archive' => false));
  if ($courseUsers['count'] > 0) {
   throw new Exception(_YOUCANNOTCHANGEMODECOURSENOTEMPTY, EfrontCourseException::COURSE_NOT_EMPTY);
  }
  $this -> addLessons($lesson -> lesson['instance_source']);
  $this -> replaceLessonInCourseOrder($lesson, $lesson -> lesson['instance_source']);
  $this -> replaceLessonInCourseRules($lesson, $lesson -> lesson['instance_source']); //Must be put *before* removeLessons()
  //$this -> removeLessons($lesson);		//commented out because it was messing up with order
  eF_deleteTableData("lessons_to_courses", "courses_ID=".$this -> course['id']." and lessons_ID=".$lesson->lesson['id']);
 }
 /**
	 * Replace a lesson reference in rules for another lesson
	 *
	 * @param EfrontLesson $oldLesson The lesson to replace
	 * @param EfrontLesson $newLesson The new lesson to put
	 * @since 3.6.1
	 * @access private
	 */
 private function replaceLessonInCourseRules($oldLesson, $newLesson) {
  $oldLesson = EfrontLesson::convertArgumentToLessonId($oldLesson);
  $newLesson = EfrontLesson::convertArgumentToLessonId($newLesson);
  foreach ($this -> rules as $id => $rule) {
   if ($id == $oldLesson) {
    $this -> rules[$newLesson] = $rule;
    unset($this -> rules[$oldLesson]);
   }
  }
  foreach ($this -> rules as $id => $rule) {
   foreach ($rule['lesson'] as $key => $value) {
    if ($value == $oldLesson) {
     $this -> rules[$id]['lesson'][$key] = $newLesson;
    }
   }
  }
  $this -> persist();
 }
 /**
	 * Replace a lesson reference in rules for another lesson
	 *
	 * @param EfrontLesson $oldLesson The lesson to replace
	 * @param EfrontLesson $newLesson The new lesson to put
	 * @since 3.6.1
	 * @access private
	 */
 private function replaceLessonInCourseOrder($oldLesson, $newLesson) {
  $oldLesson = EfrontLesson::convertArgumentToLessonId($oldLesson);
  $newLesson = EfrontLesson::convertArgumentToLessonId($newLesson);
  $previousLessons = $this -> getPreviousLessonsInCourse();
  //Set the lessons that point to the old lesson, to point to the new lesson
  $fields = array("previous_lessons_ID" => $newLesson);
  $where = "previous_lessons_ID= ".$oldLesson." and courses_ID=".$this -> course['id'];
  self::persistCourseLessons($fields, $where);
  //Set the new lesson's previous lesson to be the same as the old one's
  $fields = array("previous_lessons_ID" => $previousLessons[$oldLesson]);
  $where = "lessons_ID = ".$newLesson. " and courses_ID=".$this -> course['id'];
  self::persistCourseLessons($fields, $where);
 }
 /**
	 * Convert a course argument to a course id
	 *
	 * @param mixed $course The course argument, can be an id or an EfrontCourse object
	 * @return int The course id
	 * @since 3.6.1
	 * @access public
	 * @static
	 */
 public static function convertArgumentToCourseId($course) {
  if ($course instanceOf EfrontCourse) {
   $course = $course -> course['id'];
  } else if (!eF_checkParameter($course, 'id')) {
   throw new EfrontCourseException(_INVALIDID, EfrontCourseException :: INVALID_ID);
  }
  return $course;
 }
 /**
	 * Check succession rules for user
	 *
	 * This function checks the user's eligibility for the course lessons,
	 * based on the course rules and the user's completed lessons, as well as
	 * the dates that each lesson is available on.
	 * <br/>Eaample:
	 * <code>
	 * $course = new EfrontCourse(23);
	 * $eligibility = $course -> checkRules('jdoe');
	 * </code>
	 * In the above example, let's suppose that the course 23 has 3 lessons, with ids 1,2 and 3. Let's suppose that in order to access
	 * lesson 2, the user must have completed lesson 1, and for accessing lesson 3, the user must have completed both lessons 1 and 2.
	 * Then, if the user has completed lesson 1, the above example will return:
	 * <code>array(2 => 1, 3 => 0);</code>
	 * where if he has completed both 2 and 3 it will return:
	 * <code>array(2 => 1, 3 => 1);</code>
	 *
	 * @param mixed $user A user login or an EfrontUser object
	 * @return array The eligibility array, holding lessons ids as keys and true/false (or 0/1) as values
	 * @since 3.5.0
	 * @access public
	 */
 public function checkRules($user, $courseLessons = false) {
  if ($courseLessons == false) {
   if (!($user instanceOf EfrontUser)) {
    $user = EfrontUserFactory::factory($user);
   }
   $courseLessons = $user -> getUserStatusInCourseLessons($this);
  }
  $user = EfrontUser::convertArgumentToUserLogin($user);
  $roles = EfrontLessonUser :: getLessonsRoles();
  $courseLessons = EfrontCourse::convertLessonObjectsToArrays($courseLessons);
  if (!empty($courseLessons)) {
   $allowed = array_combine(array_keys($courseLessons), array_fill(0, sizeof($courseLessons), 1)); //By default, all lessons are accessible
  } else {
   $allowed = array();
  }
  if ($this -> course['depends_on']) {
   try {
    $dependsOn = new EfrontCourse($this -> course['depends_on']);
    if ($dependsOn -> course['active'] && !$dependsOn -> course['archive']) {
     $result = eF_getTableData("users_to_courses", "completed, user_type", "users_LOGIN='".$user."' and courses_ID=".$dependsOn->course['id']);
     if (!$result[0]['completed'] && $roles[$result[0]['user_type']] == 'student') {
      foreach ($allowed as $key => $value) {
       $allowed[$key] = 0;
      }
      return $allowed;
     }
    }
   } catch (Exception $e) {}
  }
  $completedLessons = array();
  foreach ($courseLessons as $key => $value) {
   !isset($value['start_date']) OR $dates[$key]['from_timestamp'] = $value['start_date'];
   !isset($value['end_date']) OR $dates[$key]['to_timestamp'] = $value['end_date'];
   if ($roles[$value['user_type']] == 'student') {
    $completedLessons[$key] = $value['completed'];
   }
  }
  foreach ($this -> rules as $lessonId => $lessonRules) {
   if (eF_checkParameter($lessonId, 'id')) {
    $evalString = '';
    for ($i = 1; $i < sizeof($lessonRules['lesson']); $i++) {
     $evalString .= $completedLessons[$lessonRules['lesson'][$i]].' '.($lessonRules['condition'][$i+1] == 'and' ? '&' : '|');
    }
    $evalString = $evalString.' '.$completedLessons[$lessonRules['lesson'][$i]];
    if (!empty($completedLessons) && $completedLessons[$lessonRules['lesson'][$i]]) {
     eval("\$allowed[$lessonId] = $evalString;");
    }
   }
  }
  foreach ($allowed as $id => $allow) {
   if (isset($dates[$id]['from_timestamp']) && $dates[$id]['from_timestamp'] > time()) {
    $allowed[$id] = 0;
   }
   if (isset($dates[$id]['to_timestamp']) && $dates[$id]['to_timestamp'] < time()) {
    $allowed[$id] = 0;
   }
  }
  return $allowed;
 }
 /**
	 * Check whether a lesson is part of a course
	 *
	 * @param mixed lesson A lesson id or an EfrontLesson object
	 * @since 3.6.3
	 * @access public
	 */
 public function isCourseLesson($lesson) {
  $lesson = EfrontLesson::convertArgumentToLessonObject($lesson);
  $result = eF_getTableData("lessons_to_courses", "*", "lessons_ID=".$lesson -> lesson['id']." and courses_ID=".$this -> course['id']);
  return !empty($result);
 }
 /**
	 * Store relationship of courses to users. This function serves as a single entry point for the database,
	 * to simplify caching manipulation
	 *
	 * @param array $fields The fields to store to the database table
	 * @param string $where The WHERE clause of the query
	 * @since 3.6.3
	 * @access public
	 * @static
	 */
 public static function persistCourseUsers($fields, $where, $courseId, $userLogin) {
  eF_updateTableData("users_to_courses", $fields, $where);
  //$cacheKey = "user_course_status:course:".$courseId."user:".$userLogin;
  //Cache::resetCache($cacheKey);
 }
 /**
	 * Store relationship of courses to lessons. This function serves as a single entry point for the database,
	 * to simplify caching manipulation
	 *
	 * @param array $fields The fields to store to the database table
	 * @param string $where The WHERE clause of the query
	 * @since 3.6.3
	 * @access public
	 * @static
	 */
 public static function persistCourseLessons($fields, $where) {
  eF_updateTableData("lessons_to_courses", $fields, $where);
 }
 public function handlePostAjaxRequestionForLessons() {
  if (isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id')) {
   !$this -> isCourseLesson($_GET['id']) ? $this -> addLessons($_GET['id']) : $this -> removeLessons($_GET['id']) ;
  } else if (isset($_GET['addAll'])) {
   $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true);
   unset($constraints['limit']);//This way, we preserve filter, but the operation still applies to all entries
   $this -> addCourseLessons($constraints);
   /*
			 $courseLessons = $this -> getCourseLessons();
			 $result = eF_getTableData("lessons", "*", "archive=0 and active=1 and course_only=1");
			 $lessons = array();
			 foreach ($result as $lesson) {
			 $lessons[$lesson['id']] = $lesson;
			 }
			 isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;
			 $this -> addLessons(array_diff(array_keys($lessons), array_keys($courseLessons)));
			 */
  } else if (isset($_GET['removeAll'])) {
   //$constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true);
   //$this -> removeCourseLessons($constraints);
   $courseLessons = $this -> getCourseLessons();
   isset($_GET['filter']) ? $courseLessons = eF_filterData($courseLessons, $_GET['filter']) : null;
   $this -> removeLessons(array_keys($courseLessons));
  }
  $constraints = array('archive' => false, 'active' => true, 'return_objects' => false);
  echo json_encode(array('lessons' => array_keys($this -> getCourseLessons($constraints))));
 }
 public function handlePostAjaxRequestForSkills() {
  if ($_GET['insert'] == "true") {
   $this -> assignSkill($_GET['add_skill'], $_GET['specification']);
  } else if ($_GET['insert'] == "false") {
   $this -> removeSkill($_GET['add_skill']);
  } else if (isset($_GET['addAll'])) {
   $skills = $this -> getSkills();
   isset($_GET['filter']) ? $skills = eF_filterData($skills, $_GET['filter']) : null;
   foreach ($skills as $skill) {
    if (!$skill['courses_ID']) {
     $this -> assignSkill($skill['skill_ID'], "");
    }
   }
  } else if (isset($_GET['removeAll'])) {
   $skills = $this -> getSkills();
   isset($_GET['filter']) ? $skills = eF_filterData($skills, $_GET['filter']) : null;
   foreach ($skills as $skill) {
    if ($skill['courses_ID'] == $this -> course['id']) {
     $this -> removeSkill($skill['skill_ID']);
    }
   }
  }
 }
 public function handlePostAjaxRequestForUsers() {
  if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
   $this -> handlePostAjaxRequestForSingleUser();
  } else if (isset($_GET['addAll'])) {
   $this -> handlePostAjaxRequestForUsersAddAll();
  } else if (isset($_GET['removeAll'])) {
   $this -> handlePostAjaxRequestForUsersRemoveAll();
  }
 }
 private function handlePostAjaxRequestForSingleUser() {
  isset($_GET['user_type']) && in_array($_GET['user_type'], array_keys(EfrontLessonUser :: getLessonsRoles())) ? $userType = $_GET['user_type'] : $userType = 'student';
  $user = EfrontUserFactory :: factory($_GET['login']);
  if (!$user -> hasCourse($this) || $user -> getUserTypeInCourse($this) != $userType) {
   $this -> addUsers($user, $userType);
  } else {
   $this -> archiveCourseUsers($user);
  }
 }
 private function handlePostAjaxRequestForUsersAddAll() {
  $constraints = array('archive' => false, 'active' => true, 'condition' => 'r.courses_ID is null');
  $users = $this -> getCourseUsersIncludingUnassigned($constraints);
  $users = EfrontUser :: convertUserObjectsToArrays($users);
  isset($_GET['filter']) ? $users = eF_filterData($users, $_GET['filter']) : null;
  $userTypes = array();
  foreach ($users as $user) {
   $user['user_types_ID'] ? $userTypes[] = $user['user_types_ID'] : $userTypes[] = $user['user_type'];
  }
  if (sizeof($users) <= self::MAX_MASS_OPERATION_SIZE) {
   $this -> addUsers(array_keys($users), $userTypes);
  } else {
   $users = array_slice($users, 0, self::MAX_MASS_OPERATION_SIZE);
   $userTypes = array_slice($userTypes, 0, self::MAX_MASS_OPERATION_SIZE);
   $this -> addUsers(array_keys($users), $userTypes);
   throw new EfrontCourseException(str_replace("%x", self::MAX_MASS_OPERATION_SIZE, _ONLYXCANBEAPPLIEDATATIME), EfrontCourseException :: PARTIAL_IMPORT);
  }
 }
 private function handlePostAjaxRequestForUsersRemoveAll() {
  $constraints = array('archive' => false, 'active' => true, 'condition' => 'r.courses_ID is not null');
  $users = $this -> getCourseUsersIncludingUnassigned($constraints);
  $users = EfrontUser :: convertUserObjectsToArrays($users);
  isset($_GET['filter']) ? $users = eF_filterData($users, $_GET['filter']) : null;
  $this -> archiveCourseUsers(array_keys($users));
 }
 /**
	 * Check if a course must be reset because of certificate expiry or 'before expiry' reset
	 *
	 * @param mixed lesson A lesson id or an EfrontLesson object
	 * @since 3.6.3
	 * @access public
	 */
 public static function checkCertificateExpire() {
  $courses = eF_getTableData("courses", "id,reset_interval,reset", "certificate_expiration !=0" );
  $notifications = eF_getTableData("event_notifications", "id,event_type,after_time,send_conditions", "event_type=-59 and active=1");
  $notifications_on_event = eF_getTableData("event_notifications", "id,event_type,after_time,send_conditions", "event_type=59 and active=1");
  foreach ($courses as $value) {
   $course = new EfrontCourse($value['id']);
   $constraints = array('archive' => false, 'active' => true, 'condition' => 'issued_certificate != ""');
   $users = $course -> getStudentUsers(true, $constraints);
   foreach ($users as $user) {
    $dateTable = unserialize($user -> user['issued_certificate']);
    if (eF_checkParameter($dateTable['date'], 'timestamp')) { //new way that issued date saves
     $expirationArray = convertTimeToDays($course -> course['certificate_expiration']);
     $expirationTimestamp = getCertificateExpirationTimestamp($dateTable['date'], $expirationArray);
     if ($course -> course['reset_interval'] != 0) {
      $resetArray = convertTimeToDays($value['reset_interval']);
      $resetTimestamp = getCertificateResetTimestamp($expirationTimestamp, $resetArray);
      if ($resetTimestamp < time()) {
       $user -> resetProgressInCourse($course, true, true);
      }
     }
     if ($course -> course['reset']) { //If student completed again the course with reset_interval, he has a new expire date so he will not be reset,(so it is not elseif)
      if ($expirationTimestamp < time()) {
       $user -> resetProgressInCourse($course, true);
       foreach ($notifications_on_event as $notification) {
        $send_conditions = unserialize($notification['send_conditions']);
        $courses_ID = $send_conditions['courses_ID'];
        if ($courses_ID == $value['id'] || $courses_ID == 0) {
         if ($notification['after_time'] == 0) {
          EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_CERTIFICATE_EXPIRY,
           "users_LOGIN" => $user -> user['login'],
           "lessons_ID" => $course -> course['id'],
           "lessons_name" => $course -> course['name'],
           'create_negative' => false));
         }
        }
       }
      }
     }
     if (!$course -> course['reset'] && !$course -> course['reset_interval']) {
      if ($expirationTimestamp < time()) {
       eF_updateTableData("users_to_courses", array("issued_certificate" => ""), "users_LOGIN='".$user -> user['login']."' and courses_ID = ".$course -> course['id']);
       foreach ($notifications_on_event as $notification) {
        $send_conditions = unserialize($notification['send_conditions']);
        $courses_ID = $send_conditions['courses_ID'];
        if ($courses_ID == $value['id'] || $courses_ID == 0) {
         if ($notification['after_time'] == 0) {
          EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_CERTIFICATE_EXPIRY,
           "users_LOGIN" => $user -> user['login'],
           "lessons_ID" => $course -> course['id'],
           "lessons_name" => $course -> course['name'],
           "create_negative" => false));
         }
        }
       }
      }
     }
     foreach ($notifications as $notification) {
      $send_conditions = unserialize($notification['send_conditions']);
      $courses_ID = $send_conditions['courses_ID'];
      if ($courses_ID == $value['id'] || $courses_ID == 0) {
       if ($notification['after_time'] < 0) {
        $resetArray = convertTimeToDays(abs($notification['after_time']));
        $resetTimestamp = getCertificateResetTimestamp($expirationTimestamp, $resetArray);
        // in order notification to be sent one (not every day after $resetTimestamp)	
        if ($GLOBALS['configuration']['last_reset_certificate'] < $resetTimestamp && $resetTimestamp < time() && $expirationTimestamp > time()) {
         EfrontEvent::triggerEvent(array("type" => (-1) * EfrontEvent::COURSE_CERTIFICATE_EXPIRY,
          "users_LOGIN" => $user -> user['login'],
          "lessons_ID" => $course -> course['id'],
          "lessons_name" => $course -> course['name'],
          "create_negative" => false));
        }
       }
      }
     }
    }
   }
  }
 }
}
