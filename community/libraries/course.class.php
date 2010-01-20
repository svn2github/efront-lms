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
    /**

     * The course requested does not exist

     * @since 3.5.0

     */
    const COURSE_NOT_EXISTS = 251;
    /**

     * The id provided is not valid, for example it is not a number or it is 0

     * @since 3.5.0

     */
    const INVALID_ID = 252;
    /**

     * An unspecific error

     * @since 3.5.0

     */
    const MAX_USERS_LIMIT = 253;
    const DATABASE_ERROR = 254;
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

     * The course variable

     *

     * @since 3.5.0

     * @var array

     * @access public

     */
    public $course = array();
    /**

     * The course lessons

     *

     * @since 3.5.0

     * @var array

     * @access public

     */
    public $lessons = false;
    /**

     * The course users

     *

     * @since 3.5.0

     * @var array

     * @access public

     */
    public $users = array();
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
    public $options = array('recurring' => 0,
                            'recurring_duration' => 0);
    /**

     * Create course instance

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
    function __construct($course) {
        if (is_array($course)) {
            $this -> course = $course;
        } else {
            if (!eF_checkParameter($course, 'id')) {
                throw new EfrontCourseException(_INVALIDID, EfrontCourseException :: INVALID_ID);
            }
            $course = eF_getTableData("courses", "*", "id = $course");
            if (sizeof($course) == 0) {
                throw new EfrontCourseException(_COURSEDOESNOTEXIST, EfrontCourseException :: COURSE_NOT_EXISTS);
            }
            $this -> course = $course[0];
        }
        unserialize($this -> course['rules']) ? $this -> rules = unserialize($this -> course['rules']) : null;
        if ($this -> course['options'] && $options = unserialize($this -> course['options'])) {
            $newOptions = array_diff_key($this -> options, $options); //$newOptions are course options that were added to the EfrontCourse object AFTER the lesson options serialization took place
            $this -> options = $options + $newOptions; //Set course options
        }
        if ($this -> course['price']) { //Create the string representing the course price
            isset($this -> options['recurring']) && $this -> options['recurring'] ? $recurring = array($this -> options['recurring'], $this -> options['recurring_duration']) : $recurring = false;
            $this -> course['price_string'] = formatPrice($this -> course['price'], $recurring);
  } else {
      $this -> course['price_string'] = formatPrice(0);
  }
    }
    /**

     * Get course lessons

     *

     * This function gets a list with the course lessons. If a specific order

     * is set, the lessons are ordered based on it

     * <br/>Example:

     * <code>

     * $course -> getLessons();

     * </code>

     *

     * @param boolean $returnObjects Whether to return EfrontLesson objects

     * @return array The course lessons

     * @since 3.5.0

     * @access public

     */
    public function getLessons($returnObjects = false) {
        if ($this -> lessons === false) {
            $result = eF_getTableData("lessons_to_courses lc, lessons l", "lc.previous_lessons_ID, l.*", "l.id=lc.lessons_ID and courses_ID=".$this -> course['id']);
            if (sizeof($result) > 0) {
                $previous = 0; //Previous is only used when no previos_lessons_ID is set
                foreach ($result as $value) {
                    $courseLessons[$value['id']] = $value;
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
                    $this -> lessons = $courseLessons;
                    eF_updateTableData("lessons_to_courses", array("previous_lessons_ID" => NULL), "courses_ID=".$this -> course['id']);
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
        $courseUsers = array();
        foreach ($this -> getUsers() as $login => $user) {
            $courseUsers[$login] = $user['user_type'];
        }
        if (!is_array($lessons)) {
            $lessons = array($lessons);
        }
        $lastLesson = end($this -> getLessons());
        if ($lastLesson['previous_lessons_ID'] || $lastLesson['previous_lessons_ID'] === 0) { //0 is a valid entry
            $previous = $lastLesson['id'];
        }
        foreach ($lessons as $key => $value) {
            if (!eF_checkParameter($value, 'id')) {
                unset($lessons[$key]);
            } else {
                $fields = array('courses_ID' => $this -> course['id'],
                                'lessons_ID' => $value);
                if (isset($previous)) {
                    $fields['previous_lessons_ID'] = $previous;
                    $previous = $value;
                }
                eF_insertTableData("lessons_to_courses", $fields);
                //Add course users to this lesson
                if (sizeof($courseUsers) > 0) {
                    $lesson = new EfrontLesson($value);
                    $lessonUsers = $lesson -> getUsers();
                    $usersToBeAddedToLesson = $courseUsers;
                    foreach ($usersToBeAddedToLesson as $login => $userType) {
                        if (in_array($login, array_keys($lessonUsers))) { //If a user already has this lesson, update his role
                            $lesson -> setRoles($login, $userType);
                        } else {
                            $lesson -> addUsers($login, $userType);
                        }
                    }
                }
                // Add question to course skill for each question of the newly added course
                if (!$lesson) {
                    $lesson = new EfrontLesson($value);
                }
            }
        }
        $this -> lessons = false; //Reset object's lesson information
        return $this -> getLessons();
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
        if (!is_array($lessons)) {
            $lessons = array($lessons);
        }
        $courseLessons = $this -> getLessons();
        foreach ($courseLessons as $id => $lesson) {
            $previousLessons[$id] = $lesson['previous_lessons_ID'];
        }
        $result = eF_getTableDataFlat("lessons l, users_to_courses uc, users_to_lessons ul, lessons_to_courses lc", "lc.lessons_ID, count(lc.lessons_ID)", "l.course_only = 1 and l.id=lc.lessons_ID and ul.users_LOGIN=uc.users_LOGIN and lc.lessons_ID=ul.lessons_ID and lc.courses_ID=uc.courses_ID", "", "lc.lessons_ID");
        $lessonsToCourses = array_combine($result['lessons_ID'], $result['count(lc.lessons_ID)']);
        foreach ($lessons as $key => $value) {
            if (!eF_checkParameter($value, 'id') || !in_array($value, array_keys($courseLessons))) {
                unset($lessons[$key]);
            } else {
                eF_updateTableData("lessons_to_courses", array("previous_lessons_ID" => $previousLessons[$value]), "previous_lessons_ID=$value");
                eF_deleteTableData("lessons_to_courses", "courses_ID=".$this -> course['id']." and lessons_ID=".$value);
                if ($lessonsToCourses[$value] == 1) {
                    $lesson = new EfrontLesson($value);
                    $lesson -> removeUsers(array_keys($this -> getUsers()));
                }
                // Delete question to course skill records for all lesson questions
            }
        }
        $this -> lessons = false; //Reset object's lesson information
        return $this -> getLessons();
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

     * @return array An array where keys are the user logins and values are the user types

     * @since 3.5.0

     * @access public

     */
    public function getUsers($returnObjects = false) {
        if (sizeof($this -> users) == 0) {
            $result = eF_getTableData("users_to_lessons", "users_LOGIN, lessons_ID"); //We will check if all the course's lessons are assigned to the user. So first, get all users to lessons assignments (so we don't have to perform loops with queries)
            foreach ($result as $value) {
                $usersToLessons[$value['users_LOGIN']][] = $value['lessons_ID']; //Create a practical array representation
            }
            $courseLessons = array_keys($this -> getLessons()); //Get the course's lessons
            $result = eF_getTableData("users_to_courses uc, users u", "u.login, u.name, u.surname, u.user_type as basic_user_type, u.active, u.user_types_ID, uc.user_type as role, uc.from_timestamp", "uc.users_LOGIN = u.login and uc.courses_ID=".$this -> course['id']);
            foreach ($result as $value) {
                $this -> users[$value['login']] = $value;
                foreach ($courseLessons as $lesson) { //For each lesson, check if the user has it. If he doesn't, add him to the lesson.
                    if (!in_array($lesson, $usersToLessons[$value['login']])) {
                        $lesson = new EfrontLesson($lesson);
                        $lesson -> addUsers($value['login']);
                    }
                }
            }
        }
        if ($returnObjects) {
            foreach ($this -> users as $key => $user) {
                $users[$key] = EfrontUserFactory :: factory($key);
            }
            return $users;
        }
        return $this -> users;
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

     * @return array An array with user logins

     * @since 3.5.0

     * @access public

     */
    public function getNonUsers($returnObjects = false) {
        $lessonUsers = $this -> getUsers();
        //$result      = eF_getTableData("users u", "u.login, u.name, u.surname, u.user_type as basic_user_type, u.user_types_ID, u.active", "active=1 and user_type != 'administrator' and languages_NAME='".$this -> course['languages_NAME']."' and login NOT IN ('".implode("','", array_keys($lessonUsers))."')");
        $result = eF_getTableData("users u", "u.login, u.name, u.surname, u.user_type as basic_user_type, u.user_types_ID, u.active", "active=1 and user_type != 'administrator' and login NOT IN ('".implode("','", array_keys($lessonUsers))."')");
        if (sizeof($result) > 0) {
            foreach ($result as $user) {
                $user['user_types_ID'] ? $user['role'] = $user['user_types_ID'] : $user['role'] = $user['basic_user_type'];
                $returnObjects ? $users[$user['login']] = EfrontUserFactory :: factory($user['login']) : $users[$user['login']] = $user;
            }
            return $users;
        } else {
            return array();
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

     * @return boolean true if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function addUsers($login, $role = 'student', $confirmed = true) {
        if (!is_array($login)) {
            $login = array($login);
            $role = array($role);
        }
        if (sizeof($role) < sizeof($login)) {
            $role = array_pad($role, sizeof($login), $role);
        }
        $userTypes = EfrontLessonUser :: getLessonsRoles();
  $courseUsers = $this -> getUsers();
        $count = sizeof($this -> getUsers('student'));
        foreach ($login as $key => $value) {
         if ($value instanceof EfrontUser) {
             $value = $value -> user['login'];
         }
            if (!in_array($value, array_keys($courseUsers))) { //added this to avoid adding existing user when admin changes his role
    if (eF_checkParameter($value, 'login')) {
     $fields = array('users_LOGIN' => $value,
         'courses_ID' => $this -> course['id'],
         'active' => 1,
         'from_timestamp' => $confirmed ? time() : 0,
         'user_type' => current($role));
     if ($this -> course['max_users'] && $this -> course['max_users'] < $count++ && ($fields['user_type'] == 'student' || $userTypes[$fields['user_type']]['basic_user_type'] == 'student')) {
      throw new EfrontCourseException(_MAXIMUMUSERSREACHEDFORCOURSE, EfrontCourseException :: MAX_USERS_LIMIT);
     }
     try {
      eF_insertTableData("users_to_courses", $fields);
      foreach ($this -> getLessons(true) as $lessonId => $lesson) {
       try {
        $lesson -> addUsers($value, current($role), $confirmed);
       } catch (EfrontLessonException $e) {
        $errors[] = _CANNOTADDUSERTOLESSON.' ('.EfrontLessonException :: DATABASE_ERROR.': '.$e->getMessage().')';
       }
      }
      if (current($role) == "student") {
       EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_ACQUISITION_AS_STUDENT, "users_LOGIN" => $value, "lessons_ID" => $this -> course['id'], "lessons_name" => $this -> course['name']));
      } else {
       EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_ACQUISITION_AS_PROFESSOR, "users_LOGIN" => $value, "lessons_ID" => $this -> course['id'], "lessons_name" => $this -> course['name']));
      }
     } catch (Exception $e) {
      $errors[] = _CANNOTADDUSERTOCOURSE.' ('.EfrontCourseException :: DATABASE_ERROR.': '.$e->getMessage().')';
     }
     next($role);
    } else {
     $errors[] = _INVALIDLOGIN.': '.$value.' ('.EfrontLessonException :: INVALID_LOGIN.')';
    }
   } else {
                if ($courseUsers[$value]['role'] != $role[$key]) {
                    $this -> setRoles($value, $role[$key]);
                }
            }
  }
        if (!isset($errors)) {
            return true;
        } else {
            throw new EfrontLessonException(_CANNOTADDUSERTOCOURSE.': '.implode("<br>", $errors), EfrontLessonException :: GENERAL_ERROR);
        }
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

     */
    public function removeUsers($login) {
        if (!is_array($login)) {
            $login = array($login);
        }
        foreach ($login as $value) {
         if ($value instanceof EfrontUser) {
             $value = $value -> user['login'];
         }
            if (eF_checkParameter($value, 'login')) {
                $result = eF_getTableDataFlat("lessons l, users_to_courses uc, users_to_lessons ul, lessons_to_courses lc", "lc.lessons_ID, count(lc.lessons_ID)", "l.course_only = 1 and l.id=lc.lessons_ID and ul.users_LOGIN=uc.users_LOGIN and lc.lessons_ID=ul.lessons_ID and lc.courses_ID=uc.courses_ID and uc.users_LOGIN='".$value."'", "", "lc.lessons_ID, uc.users_LOGIN");
                $lessonsToCourses = array_combine($result['lessons_ID'], $result['count(lc.lessons_ID)']);
                if (eF_deleteTableData("users_to_courses", "users_LOGIN='$value' and courses_ID=".$this -> course['id'])) {
                 EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_REMOVAL, "users_LOGIN" => $value, "lessons_ID" => $this -> course['id'], "lessons_name" => $this -> course['name']));
                 foreach ($lessonsToCourses as $key => $value) {
                        if ($value == 1) {
                            $lesson = new EfrontLesson($key);
                            $lesson -> removeUsers($login);
                        }
                    }
                }
            }
        }
        if (!isset($errors)) {
            return true;
        } else {
            throw new EfrontLessonException(_CANNOTREMOVEUSERFROMCOURSE.': '.implode("<br>", $errors), EfrontLessonException :: GENERAL_ERROR);
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
        if ($login instanceof EfrontLessonUser) {
            $login = $login -> user['login'];
        } else if (!eF_checkParameter($login, 'login')) {
            throw new EfrontUserException(_INVALIDLOGIN, EfrontUserException::INVALID_LOGIN);
        }
        foreach ($this -> getLessons(true) as $lesson) {
            $lesson -> confirm($login);
        }
        eF_updateTableData("users_to_courses", array("from_timestamp" => time()), "users_LOGIN='".$login."' and courses_ID=".$this -> course['id']." and from_timestamp=0");
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
    public function checkRules($user) {
        if ($user instanceof EfrontUser) {
            $user = $user -> user['login'];
        }
        if (!in_array($user, array_keys($this -> getUsers()))) { //If this is an invalid user or does not have this course, issue an exception
            throw new EfrontCourseException(_UNKNOWNUSER.': '.$user, EfrontCourseException :: INVALID_LOGIN);
        }
        $allowed = array_combine(array_keys($this -> getLessons()), array_fill(0, sizeof($this -> getLessons()), 1)); //By default, all lessons are accessible
        $result = eF_getTableDataFlat("users_to_lessons ul, lessons l", "ul.lessons_ID, ul.completed, ul.user_type, l.from_timestamp, l.to_timestamp", "ul.lessons_ID = l.id and ul.users_LOGIN='$user'");
        foreach ($result['lessons_ID'] as $key => $value) {
            $result['from_timestamp'][$key] ? $dates[$value]['from_timestamp'] = $result['from_timestamp'][$key] : null;
            $result['to_timestamp'][$key] ? $dates[$value]['to_timestamp'] = $result['to_timestamp'][$key] : null;
        }
        if (sizeof($result) > 0 && $result['user_type'][0] == 'student') {
            $completedLessons = array_combine($result['lessons_ID'], $result['completed']);
        } else {
            return $allowed;
        }
        foreach ($this -> rules as $lessonId => $lessonRules) {
            $evalString = '';
            for ($i = 1; $i < sizeof($lessonRules['lesson']); $i++) {
                $evalString .= $completedLessons[$lessonRules['lesson'][$i]].' '.($lessonRules['condition'][$i+1] == 'and' ? '&' : '|');
            }
            $evalString = $evalString.' '.$completedLessons[$lessonRules['lesson'][$i]];
            eval("\$allowed[$lessonId] = $evalString;");
        }
        foreach ($allowed as $id => &$allow) {
            if (isset($dates[$id]['from_timestamp']) && $dates[$id]['from_timestamp'] > time()) {
                $allow = 0;
            }
            if (isset($dates[$id]['to_timestamp']) && $dates[$id]['to_timestamp'] < time()) {
                $allow = 0;
            }
        }
        unset($allow);
        //pr($allowed);
        return $allowed;
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

     * @param mixed $login The user login name

     * @param mixed $role The user role for this course

     * @return boolean true if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function setRoles($login, $role) {
        if (!is_array($login)) {
            $login = array($login);
            $role = array($role);
        }
        foreach ($login as $key => $value) {
            if (eF_checkParameter($value, 'login')) {
                if (!eF_updateTableData("users_to_courses", array('user_type' => $role[$key]), "users_LOGIN='".$value."' and courses_ID=".$this -> course['id'])) {
                    $errors[] = _CANNOTUPDATEUSERCOURSEINFORMATION.' ('.EfrontLessonException :: DATABASE_ERROR.')';
                } else {
                    foreach ($this -> getLessons(true) as $lessonId => $lesson) {
                        $lesson -> setRoles($value, $role[$key]);
                    }
                }
            } else {
                $errors[] = _INVALIDLOGIN.': '.$value.' ('.EfrontLessonException :: INVALID_LOGIN.')';
            }
        }
        if (!isset($errors)) {
            return true;
        } else {
            throw new EfrontCourseException(_PROBLEMUPDATINGUSERSTOLESSON.': '.implode("<br>", $errors), EfrontCourseException :: GENERAL_ERROR);
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
        $this -> rules ? $this -> course['rules'] = serialize($this -> rules) : $this -> course['rules'] = '';
        $fields = array('name' => $this -> course['name'],
                        'directions_ID' => $this -> course['directions_ID'],
                        'info' => $this -> course['info'],
                        'price' => str_replace(array($GLOBALS['configuration']['decimal_point'], $GLOBALS['configuration']['thousands_sep']), array('.', ''), $this -> course['price']),
                        'active' => $this -> course['active'],
                        'languages_NAME' => $this -> course['languages_NAME'],
                        'metadata' => $this -> course['metadata'],
                        'duration' => $this -> course['duration'] ? $this -> course['duration'] : 0,
                        'max_users' => $this -> course['max_users'] ? $this -> course['max_users'] : null,
                        'certificate' => $this -> course['certificate'],
                        'auto_certificate' => $this -> course['auto_certificate'],
                        'auto_complete' => $this -> course['auto_complete'],
                        'rules' => $this -> course['rules'],
                        'certificate_tpl_id' => $this -> course['certificate_tpl_id'],
      'certificate_expiration' => $this -> course['certificate_expiration'],
      'reset' => $this -> course['reset'],
                        'archive' => $this -> course['archive'],
                        'show_catalog' => $this -> course['show_catalog'] ? $this -> course['show_catalog'] : 0,
      'created' => $this -> course['created'],
                        'options' => serialize($this -> options));
        eF_updateTableData("courses", $fields, "id=".$this -> course['id']);
        EfrontSearch :: removeText('courses', $this -> course['id'], '');
        EfrontSearch :: insertText($fields['name'], $this -> course['id'], "courses", "title");
        return true;
    }
   /**

     * Archive lesson

     * 

     * This function is used to archive the course object, by setting its active status to 0 and its

     * archive status to 1

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
        $this -> persist();
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
        $this -> persist();
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
        eF_deleteTableData("users_to_courses", "courses_ID=".$this -> course['id']);
        eF_deleteTableData("courses", "id=".$this -> course['id']);
        // Delete the skill correlated with this lesson
        $skills = $this -> getSkills();
        foreach ($skills as $skid => $skill) {
            if ($skill['courses_ID'] != $this -> course['id']) {
                unset($skills[$skid]);
            }
        }
        $courseSkill = $this ->getCourseSkill();
        if ($courseSkill) {
            eF_deleteTableData("questions_to_skills", "skills_ID = '". $courseSkill['skill_ID'] . "'");
        }
        eF_deleteTableData("module_hcd_course_offers_skill", "courses_ID = '". $this -> course['id'] . "'");
        if (!empty($skills)) {
            eF_deleteTableData("module_hcd_skills", "skill_ID IN ('". implode("','", array_keys($skills)) . "')");
        }
        EfrontSearch :: removeText('courses', $this -> course['id'], '');
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
        if ($login instanceof EfrontUser) {
            $login = $login -> user['login'];
        }
        if (eF_checkParameter($login, 'login')) {
            eF_updateTableData("users_to_courses", array("issued_certificate" => ""), "users_LOGIN='$login' and courses_ID=".$this -> course['id']);
            EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_CERTIFICATE_REVOKE, "users_LOGIN" => $login, "lessons_ID" => $this -> course['id'], "lessons_name" => $this -> course['name']));
   return true;
        } else {
            throw new EfrontUserException(_INVALIDLOGIN.': '.$login, EfrontUserException :: INVALID_LOGIN);
        }
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
        if (eF_checkParameter($login, 'login')) {
            eF_updateTableData("users_to_courses", array("issued_certificate" => $certificate), "users_LOGIN='$login' and courses_ID=".$this -> course['id']);
            $certificateArray = unserialize($certificate);
   EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_CERTIFICATE_ISSUE, "users_LOGIN" => $login, "lessons_ID" => $this -> course['id'], "lessons_name" => $this -> course['name'],"entity_ID" =>$certificateArray['serial_number'], "entity_name" => $certificateArray['grade']));
   return true;
        } else {
            throw new EfrontUserException(_INVALIDLOGIN.': '.$login, EfrontUserException :: INVALID_LOGIN);
        }
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
    public function prepareCertificate($login) {
        if (eF_checkParameter($login, 'login')) {
            $data = array();
            $courseUser = EfrontUserFactory :: factory($login);
            $userStats = EfrontStats::getUsersCourseStatus($this, $login);
            $data['organization'] = $GLOBALS['configuration']['site_name'];
            $data['course_name'] = $this -> course['name'];
            $data['user_surname'] = $courseUser -> user['surname'];
            $data['user_name'] = $courseUser -> user['name'];
   $data['serial_number']= md5(uniqid(mt_rand(), true));
            $data['grade'] = $userStats[$this -> course['id']][$login]['score'];
            //$data['date']      = formatTimestamp(time());
   $data['date'] = time();
            $data = serialize($data);
            return $data;
        } else {
            throw new EfrontUserException(_INVALIDLOGIN.': '.$login, EfrontUserException :: INVALID_LOGIN);
        }
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
        if (!$this -> course['certificate'] && is_file(G_CURRENTTHEMEPATH."templates/certificate-".$this -> course['languages_NAME'].".tpl")) {
            $certificate = file_get_contents(G_CURRENTTHEMEPATH."templates/certificate-".$this -> course['languages_NAME'].".tpl");
        } elseif ($this -> course['certificate']) {
            $certificate = $this -> course['certificate'];
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
        eF_updateTableData("courses", array("certificate" => $certificate), "id=".$this -> course['id']);
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

     */
    public function toHTML($userInfo = array(), $options = array()) {
        !isset($options['courses_link']) ? $options['courses_link'] = false : null;
        !isset($options['lessons_link']) ? $options['lessons_link'] = false : null;
  if (isset($options['collapse']) && $options['collapse'] == 2) {
   $display = '';
   $display_lessons = 'style = "display:none"';
  } elseif (isset($options['collapse']) && $options['collapse'] == 1) {
   $display = 'style = "display:none"';
   $display_lessons = 'style = "display:none"';
  } else {
   $display = '';
   $display_lessons = '';
  }
        $roles = EfrontLessonUser :: getLessonsRoles();
        $roleNames = EfrontLessonUser :: getLessonsRoles(true);
        if ($userInfo['courses'][$this -> course['id']]['user_type']) {
            $roleBasicType = $roles[$userInfo['courses'][$this -> course['id']]['user_type']]; //The basic type of the user's role in the course
        } else {
            $roleBasicType = null;
        }
        if ($userInfo['courses'][$this -> course['id']]['user_type'] == 'student') {
            $eligible = $this -> checkRules($userInfo['courses'][$this -> course['id']]['login']);
        } else {
            if (sizeof($this -> getLessons()) > 0) {
                $eligible = array_combine(array_keys($this -> getLessons()), array_fill(0, sizeof($this -> getLessons()), 1)); //All lessons set to true
            } else {
                $eligible = array();
            }
        }
  if (sizeof($eligible) > 0) {
   $result = eF_getTableData("lessons", "*", "id in (".implode(",", array_keys($eligible)).")");
   foreach ($result as $value) {
    $lessons[$value['id']] = $value;
   }
  }
            //$eligible = array_combine(array_keys($this -> getLessons()), array_fill(0, sizeof($this -> getLessons()), 1));    //All lessons set to true
  foreach ($eligible as $lessonId => $value) {
            $eligible[$lessonId] = new EfrontLesson($lessons[$lessonId]);
            $eligible[$lessonId] -> eligible = $value;
            if (!$eligible[$lessonId] -> lesson['active'] || !in_array($lessonId, array_keys($this -> getLessons()))) {
                unset($eligible[$lessonId]); //Remove inactive lessons from list
            }
   if (!$eligible[$lessonId] -> lesson['show_catalog']) {
    unset($eligible[$lessonId]);
   }
        }
        $courseString = '
                        <table class = "coursesTable" >
                            <tr class = "lessonsList" >
                             <td class = "listToggle">
                              <img src = "images/16x16/navigate_down.png" class = "visible" alt = "'._CLICKTOTOGGLE.'" title = "'._CLICKTOTOGGLE.'" onclick = "showHideCourses(this, $(\'subtree_course'.$this -> course['id'].'\'))">
                             </td>
                             <td class = "listIcon">
                                    <img id = "course_img'.$this -> course['id'].'" src = "images/32x32/courses.png">
                                </td>
                                <td>';        
        if (!isset($userInfo['courses'][$this -> course['id']]['from_timestamp']) || $userInfo['courses'][$this -> course['id']]['from_timestamp']) {
            if ($options['tooltip']) {
                $courseString .= '
                  <a href = "'.($options['courses_link'] ? str_replace("#user_type#", $roleBasicType, $options['courses_link']).$this -> course['id'] : 'javascript:void(0)').'" class = "info" onmouseover = "updateInformation(this, '.$this -> course['id'].', \'course\')" >
                   <span class = "listName">'.$this -> course['name'].'</span>
                   <img class = "tooltip" src = "images/others/tooltip_arrow.gif"/>
                   <span class = "tooltipSpan"></span>
                  </a>';             
            } else {
                $options['courses_link'] ? $courseString .= '<a href = "'.str_replace("#user_type#", $roleBasicType, $options['courses_link']).$this -> course['id'].'">'.$courseString .= $this -> course['name'].'</a>' : $courseString .= $this -> course['name'];
            }
        } else {
            $courseString .= '<a href = "javascript:void(0)" class = "inactiveLink" title = "'._CONFIRMATIONPEDINGFROMADMIN.'">'.$this -> course['name'].'</a>';
        }
        if ($userInfo['courses'][$this -> course['id']]['different_role']) {
            $courseString .= '<span class = "courseRole">&nbsp;('.$roleNames[$userInfo['courses'][$this -> course['id']]['user_type']].')</span>';
        }
        if (!is_null($userInfo['courses'][$this -> course['id']]['remaining']) && $userInfo['courses'][$this -> course['id']]['user_type'] == 'student') {
            $courseString .= '<span class = "">&nbsp;('.eF_convertIntervalToTime($userInfo['courses'][$this -> course['id']]['remaining'], true).' '.mb_strtolower(_REMAINING).')</span>';
        }
        if ($roleBasicType == 'professor') {
            $courseString .= '<span class = "courseActions">&nbsp;('._COURSEACTIONS.':</span>   
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_info" >
                                                    <img src = "images/16x16/information.png" title = "'._COURSEINFORMATION.'" alt = "'._COURSEINFORMATION.'" class = "handle"></a>';
   $courseString .= '<a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_certificates">
                                                    <img src = "images/16x16/autocomplete.png" title = "'._COMPLETION.'" alt = "'._COMPLETION.'" class = "handle"></a>';
   $courseString .= '<a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_rules">
                                                    <img src = "images/16x16/rules.png" title = "'._COURSERULES.'" alt = "'._COURSERULES.'" class = "handle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_order">
                                                    <img src = "images/16x16/order.png" title = "'._COURSEORDER.'" alt = "'._COURSEORDER.'" class = "handle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_scheduling">
                                                    <img src = "images/16x16/calendar.png" title = "'._COURSESCHEDULE.'" alt = "'._COURSESCHEDULE.'" class = "handle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=export_course">
                                                    <img src = "images/16x16/export.png" title = "'._EXPORTCOURSE.'" alt = "'._EXPORTCOURSE.'" class = "handle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=import_course">
                                                    <img src = "images/16x16/import.png" title = "'._IMPORTCOURSE.'" alt = "'._IMPORTCOURSE.'" class = "handle"></a>
                                                <span>)</span>';            
        } else {
            if ($userInfo['courses'][$this -> course['id']]['completed']) {
                $courseString .= '<span class = "courseActions">&nbsp;</span>
                                                <img class = "handle" src = "images/16x16/success.png" title = "'._COURSECOMPLETED.'" alt = "'._COURSECOMPLETED.'">';
                if ($userInfo['courses'][$this -> course['id']]['issued_certificate']) {
     $dateTable = unserialize($userInfo['courses'][$this -> course['id']]['issued_certificate']);
    }
            }
        }
        $courseString .= '
                                </td><td>';
  if (isset($options['buy_link']) && $options['buy_link'] && !$this -> course['has_course'] && !$this -> course['reached_max_users'] && $_SESSION['s_type'] != 'administrator') {
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
            $courseString .= '
                            <tr id = "subtree_course'.$this -> course['id'].'" name = "default_visible" '.$display_lessons.'>
                                <td class = "lessonsList_nocolor">&nbsp;</td>
                                <td colspan = "2">
                                 <table>';
            foreach ($eligible as $lessonId => $lesson) {
                $roleBasicType = $roles[$userInfo['lessons'][$lessonId]['user_type']]; //The basic type of the user's role in the lesson 
                $courseString .= '<tr class = "directionEntry">';
                if (isset($userInfo['lessons'][$lessonId]['from_timestamp']) && !$userInfo['lessons'][$lessonId]['from_timestamp']) {
                    $courseString .= '<td style = "padding-bottom:2px"></td><td><a href = "javascript:void(0)" class = "inactiveLink" title = "'._CONFIRMATIONPEDINGFROMADMIN.'">'.$lesson -> lesson['name'].'</a></td>';
                } else if (!$lesson -> eligible) {
     if ($userInfo['lessons'][$lessonId]['completed']) {
      $courseString .= '
       <td class = "lessonProgress">
                                <span class = "progressNumber" style = "width:50px;">&nbsp;</span>
                                <span class = "progressBar" style = "width:50px;text-align:center"><img src = "images/16x16/success.png" alt = "'._LESSONCOMPLETE.'" title = "'._LESSONCOMPLETE.'" style = "vertical-align:middle" /></span>
                                &nbsp;&nbsp;
                            </td>'; 
     } else {
      $courseString .= '
       <td class = "lessonProgress">
                                <span class = "progressNumber" style = "width:50px;">'.$userInfo['lessons'][$lessonId]['overall_progress'].'%</span>
                                <span class = "progressBar" style = "width:'.($userInfo['lessons'][$lessonId]['overall_progress'] / 2).'px;">&nbsp;</span>
                                &nbsp;&nbsp;
                            </td>';
     }
     $courseString .= '
       <td>&nbsp;
                             <a href = "javascript:void(0)" title = "" class = "inactiveLink info" onmouseover = "updateInformation(this, '.$lesson -> lesson['id'].', \'lesson\')">
                              '.$lesson -> lesson['name'].'
                              <img class = "tooltip" src = "images/others/tooltip_arrow.gif"/>
                              <span class = "tooltipSpan"></span>
                             </a>
                            <td>';
                } else {
                    if ($userInfo['lessons'][$lessonId]['user_type'] && $roles[$userInfo['lessons'][$lessonId]['user_type']] == 'student' && $userInfo['lessons'][$lessonId]['completed']) { //Show the progress bar
      $courseString .= '
       <td class = "lessonProgress">
                                <span class = "progressNumber" style = "width:50px;">&nbsp;</span>
                                <span class = "progressBar" style = "width:50px;text-align:center"><img src = "images/16x16/success.png" alt = "'._LESSONCOMPLETE.'" title = "'._LESSONCOMPLETE.'" style = "vertical-align:middle" /></span>
                                &nbsp;&nbsp;
                            </td>';                            
     } elseif ($userInfo['lessons'][$lessonId]['user_type'] && $roles[$userInfo['lessons'][$lessonId]['user_type']] == 'student') {
      $courseString .= '
       <td class = "lessonProgress">
                                <span class = "progressNumber" style = "width:50px;">'.$userInfo['lessons'][$lessonId]['overall_progress'].'%</span>
                                <span class = "progressBar" style = "width:'.($userInfo['lessons'][$lessonId]['overall_progress'] / 2).'px;">&nbsp;</span>
                                &nbsp;&nbsp;
                            </td>';  
     } else {
      $courseString .= '
       <td></td>';
     }
                    $courseString .= '
                      <td>&nbsp;
                       '.($options['lessons_link'] ? '<a href = "'.str_replace("#user_type#", $roleBasicType, $options['lessons_link']).$lesson -> lesson['id'].'&from_course='.$this -> course['id'].'" class = "info" onmouseover = "updateInformation(this, '.$lesson -> lesson['id'].', \'lesson\')" onclick = "this.update(\''.$lesson -> lesson['name'].'\');">'.$lesson -> lesson['name'].'<img class = "tooltip" border = "0" src = "images/others/tooltip_arrow.gif"/><span class = "tooltipSpan"></span></a>' : $lesson -> lesson['name']).'
                                </td>';
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

     * $directionsTree -> toSelect();                         //Print directions tree

     * </code>

     * 

     * @return an array for inputing a categorized select for courses

     * @since 3.5.2

     * @access public

     */
    public function toSelect() {
        $eligible = $this->getLessons();
        foreach ($eligible as $lessonId => $value) {
            $eligible[$lessonId] = new EfrontLesson($lessonId);
            $eligible[$lessonId] -> eligible = $value;
            if (!$eligible[$lessonId] -> lesson['active'] || !in_array($lessonId, array_keys($this -> getLessons()))) {
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

     */
    public function getInformation() {
        $information = array();
        if ($this -> course['info']) {
            $information = unserialize($this -> course['info']);
        }
        foreach ($this -> getUsers() as $key => $user) {
            if ($user['role'] == 'professor') {
                $information['professors'][$key] = $user;
            }
        }
        $information['lessons_number'] = sizeof($this -> getLessons());
        $information['price_string'] = $this -> course['price_string'];
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
        $userTempDir = $GLOBALS['currentUser'] -> user['directory'].'/temp';
        $courseTempDir = $userTempDir.'/course_export'; //The compressed file will be moved to the user's temp directory
        if (!is_dir($userTempDir)) { //If the user's temp directory does not exist, create it
            $userTempDir = EfrontDirectory :: createDirectory($userTempDir, false);
        } else {
         $userTempDir = new EfrontDirectory($userTempDir);
        }
        if (is_dir($courseTempDir)) { //If the user's temp directory does not exist, create it
            $foo = new EfrontDirectory($courseTempDir);
            $foo -> delete();
        }
        $courseTempDir = EfrontDirectory :: createDirectory($courseTempDir, false);
        $courseLessons = $this -> getLessons(true);
        foreach ($courseLessons as $id => $lesson) {
            $exportedFile = $lesson -> export('all', false);
            $exportedFile -> copy($courseTempDir['path']);
        }
        $data = array();
        $data['courses'] = eF_getTableData("courses", "*", "id=".$this -> course['id']);
        //$data['lessons_to_courses'] = eF_getTableData("lessons_to_courses lc, lessons l", "lc.*, l.name", "l.id = lc.lessons_ID and courses_ID=".$this -> course['id']);
        foreach ($this -> getLessons() as $value) {
            $data['lessons_to_courses'][] = array('courses_ID' => $this -> course['id'], 'lessons_ID' => $value['id']);
        }
        file_put_contents($courseTempDir['path'].'/data.dat', serialize($data));
        $file = $courseTempDir -> compress($this -> course['id'].'_exported.zip', false); //Compress the lesson files
        $newList = FileSystemTree :: importFiles($file['path']); //Import the file to the database, so we can download it
        $file = new EfrontFile(current($newList));
        $file -> rename($userTempDir['path'].'/'.EfrontFile :: encode($this -> course['name']).'.zip', true);
        $courseTempDir -> delete();
        return $file;
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
    public function import($file, $removeLessons = true, $courseProperties = false) {
        $fileList = $file -> uncompress();
        $fileDir = $file['directory'];
        $file -> delete();
        $fileList = array_unique(array_reverse($fileList, true));
        $dataFile = new EfrontFile($fileDir.'/data.dat');
        $filedata = file_get_contents($dataFile['path']);
        $dataFile -> delete();
        $data = unserialize($filedata);
        //pr($data);
        if ($courseProperties) {
            unset($data['courses'][0]['id']);
            unset($data['courses'][0]['directions_ID']);
            unset($data['courses'][0]['created']);
            unset($data['courses'][0]['rules']);
            $this -> course = array_merge($this -> course, $data['courses'][0]);
            $this -> persist();
        }
        foreach ($data['lessons_to_courses'] as $value) {
            $lesson = EfrontLesson :: createLesson(array('name' => $value['name'], 'course_only' => true, 'directions_ID' => $this -> course['directions_ID']));
            $file = new EfrontFile($fileDir.'/'.$value['lessons_ID'].'_exported.zip');
            $newFile = $file -> copy($lesson -> getDirectory());
            $lesson -> import($newFile);
            $map[$value['lessons_ID']] = $lesson -> lesson['id'];
            $previous[$lesson -> lesson['id']] = $value['previous_lessons_ID'];
        }
        if ($data['courses'][0]['rules'] = unserialize($data['courses'][0]['rules'])) {
            foreach ($data['courses'][0]['rules'] as $lessonId => $value) {
                foreach ($value['lesson'] as $key => $ruleId) {
                    $temp[$map[$lessonId]]['lesson'][$key] = $map[$ruleId];
                }
            }
        }
        if ($removeLessons) {
            $this -> removeLessons(array_keys($this -> getLessons()));
        }
        $this -> addLessons(array_values($map));
        $this -> rules = $temp;
        foreach ($previous as $newLessonId => $oldPrevious) {
            $map[$oldPrevious] ? $newPrevious = $map[$oldPrevious] : $newPrevious = 0;
            eF_updateTableData("lessons_to_courses", array("previous_lessons_ID" => $newPrevious), "courses_ID=".$this -> course['id']." and lessons_ID=".$newLessonId);
        }
        unset($data['courses'][0]['id']);
        unset($data['courses'][0]['directions_ID']);
        unset($data['courses'][0]['active']);
        foreach ($data['courses'][0] as $key => $value) {
            $this -> course[$key] = $value;
        }
        $this -> persist();
        $this -> getLessons();
        return $course;
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

     */
    public function toHTMLTooltipLink($link, $courseInformation = false) {
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
                    case 'general_description': $tooltipInfo[] = '<strong>'._GENERALDESCRIPTION."</strong>: $value<br/>"; break;
                    case 'assessment' : $tooltipInfo[] = '<strong>'._ASSESSMENT."</strong>: $value<br/>"; break;
                    case 'objectives' : $tooltipInfo[] = '<strong>'._OBJECTIVES."</strong>: $value<br/>"; break;
                    case 'lesson_topics' : $tooltipInfo[] = '<strong>'._LESSONTOPICS."</strong>: $value<br/>"; break;
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
                    <img class = "tooltip" border = "0" src="images/others/tooltip_arrow.gif"/>
                    <span class = "tooltipSpan">'.implode("", $tooltipInfo).'</span></a>';
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
        //These are the mandatory fields. In case one of these is absent, fill it in with a default value
        !isset($fields['name']) ? $fields['name'] = 'Default name' : null;
        !isset($fields['languages_NAME']) ? $fields['languages_NAME'] = $GLOBALS['configuration']['default_language'] : null;
        if (!isset($fields['directions_ID'])) {
            $directions = eF_getTableData("directions", "id");
            sizeof($directions) > 0 ? $fields['directions_ID'] = $directions[0]['id'] : $fields['directions_ID'] = 1;
        }
        $fields['created'] = time();
        $languages = EfrontSystem :: getLanguages(true);
        $courseMetadata = array('title' => $fields['name'],
                                'creator' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                'publisher' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                'contributor' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                'date' => date("Y/m/d", time()),
                                'language' => $languages[$fields['languages_NAME']],
                                'type' => 'course');
        $fields['metadata'] = serialize($lessonMetadata);
        $newId = eF_insertTableData("courses", $fields);
        EfrontSearch :: insertText($fields['name'], $newId, "courses", "title");
        $course = new EfrontCourse($newId);
        return $course;
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

     * $lessons = EFrontLesson :: getLessons();

     * </code>

     *

     * @param boolean $returnObjects whether to return EfrontCourse objects

     * @return array The lessons list

     * @since 3.5.0

     * @access public

     * @static

     */
    public static function getCourses($returnObjects = false) {
        $result = eF_getTableData("courses c, directions d", "c.*, d.name as direction_name", "c.directions_ID=d.id and archive=0");
        foreach ($result as $value) {
         $returnObjects ? $courses[$value['id']] = new EfrontCourse($value) : $courses[$value['id']] = $value;
        }
        return $courses;
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
            $skills = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON (module_hcd_course_offers_skill.skill_ID = module_hcd_skills.skill_ID AND module_hcd_course_offers_skill.courses_ID='".$this -> course['id']."')", "description,specification, module_hcd_skills.skill_ID,courses_ID, categories_ID","");
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
            if ($ok = eF_insertTableData("module_hcd_course_offers_skill", array("skill_ID" => $skill_ID, "courses_ID" => $this -> course['id'], "specification" => $specification))) {
                $this -> skills[$skill_ID]['courses_ID'] = $this -> course['id'];
                $this -> skills[$skill_ID]['specification'] = $specification;
            } else {
                throw new EfrontcourseException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontcourseException :: DATABASE_ERROR);
            }
        } else {
            if ($ok = eF_updateTableData("module_hcd_course_offers_skill", array("specification" => $specification), "skill_ID = '".$skill_ID."' AND courses_ID = '". $this -> course['id'] ."'") ) {
                $this -> skills[$skill_ID]['specification'] = $specification;
            } else {
                throw new EfrontcourseException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontcourseException :: DATABASE_ERROR);
            }
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
            if ($ok = eF_deleteTableData("module_hcd_course_offers_skill", "skill_ID = '".$skill_ID."' AND courses_ID = '". $this -> course['id'] ."'") ) {
                $this -> skills[$skill_ID]['specification'] = "";
                $this -> skills[$skill_ID]['courses_ID'] = "";
            } else {
                throw new EfrontcourseException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontcourseException :: DATABASE_ERROR);
            }
        }
        return true;
    }
}
?>
