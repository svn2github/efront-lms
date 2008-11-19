<?php
/**
 * File for courses
 *
 * @package eFront
*/

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
    const INVALID_ID        = 252;
    /**
     * An unspecific error
     * @since 3.5.0
     */
    const GENERAL_ERROR     = 299;

    const INVALID_LOGIN     = 300;
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
    public $rules = false;

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
        if ($this -> lessons == false) {
            $result = eF_getTableData("lessons_to_courses lc, lessons l", "lc.previous_lessons_ID, l.*", "l.id=lc.lessons_ID and courses_ID=".$this -> course['id']);
            if (sizeof($result) > 0) {
                $previous = 0;                                            //Previous is only used when no previos_lessons_ID is set
                foreach ($result as $value) {
                    $courseLessons[$value['id']] = $value;
                    $value['previous_lessons_ID'] !== false ? $previousLessons[$value['previous_lessons_ID']] = $value : $previousLessons[$previous] = $value;
                    $previous = $value['id'];
                }

                //Sorting algorithm, based on previous_lessons_ID. The algorithm is copied from EfrontContentTree :: reset() and is the same with the one applied for content. It is also used in questions order
                $node  = 0;
                $count = 0;
                $nodes = array();                                                                          //$count is used to prevent infinite loops
                while (sizeof($previousLessons) > 0 && isset($previousLessons[$node]) && $count++ < 1000) {
                    $nodes[$previousLessons[$node]['id']] = $previousLessons[$node];
                    $newNode = $previousLessons[$node]['id'];
                    unset($previousLessons[$node]);
                    $node    = $newNode;
                }
                $this -> lessons = $nodes;

                if (sizeof($nodes) != sizeof($result)) {                    //If the ordering is messed up for some reason
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
        $courseUsers  = array();

        foreach ($this -> getUsers() as $login => $user) {
            $courseUsers[$login] = $user['user_type'];
        }

        if (!is_array($lessons)) {
            $lessons = array($lessons);
        }
        $lastLesson = end($this -> getLessons());
        if ($lastLesson['previous_lessons_ID'] || $lastLesson['previous_lessons_ID'] === 0) {            //0 is a valid entry
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
                    $lesson      = new EfrontLesson($value);
                    $lessonUsers = $lesson -> getUsers();
                    $usersToBeAddedToLesson = $courseUsers;
                    foreach ($usersToBeAddedToLesson as $login => $userType) {
                        if (in_array($login, array_keys($lessonUsers))) {            //If a user already has this lesson, update his role
                            $lesson -> setRoles($login, $userType);
                        } else {
                            $lesson -> addUsers($login, $userType);
                        }
                    }
                }

                // Add question to course skill for each question of the newly added course

                if (!$lesson) {
                    $lesson      = new EfrontLesson($value);
                }
                $questions = eF_getTableData("questions", "id", "lessons_ID = ". $lesson ->lesson['id']);
                // Get course specific skill
                $course_skill = $this -> getCourseSkill();
                $insert_string = "";
                foreach ($questions as $question) {
                    if ($insert_string != "") {
                        $insert_string .= ",('".$question['id']."','".$course_skill['skill_ID']."',2)";
                    } else {
                        $insert_string .= "('".$question['id']."','".$course_skill['skill_ID']."',2)";
                    }
                }

                if ($insert_string != "") {
                    eF_executeNew("INSERT INTO questions_to_skills VALUES " . $insert_string);
                }
            }
        }

        $this -> lessons = false;                //Reset object's lesson information
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

        $result           = eF_getTableDataFlat("lessons l, users_to_courses uc, users_to_lessons ul, lessons_to_courses lc", "lc.lessons_ID, count(lc.lessons_ID)", "l.course_only = 1 and l.id=lc.lessons_ID and ul.users_LOGIN=uc.users_LOGIN and lc.lessons_ID=ul.lessons_ID and lc.courses_ID=uc.courses_ID", "", "lc.lessons_ID");
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
                $course_skill = $this -> getCourseSkill();
                $questions = eF_getTableDataFlat("questions", "id", "lessons_ID = ". $value);
                eF_deleteTableData("questions_to_skills", "questions_id IN ('".implode("','",$questions['id'])."') AND skills_ID = " . $course_skill['skill_ID']);

            }
        }

        $this -> lessons = false;                //Reset object's lesson information
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
        $skills = $this -> getSkills();

        foreach ($skills as $skid=>$skill) {
            if ($skill['courses_ID'] == $this -> course['id'] && $skill['categories_ID'] == -1) {
                return $skill;
            }
        }
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

            $result = eF_getTableData("users_to_lessons", "users_LOGIN, lessons_ID");   //We will check if all the course's lessons are assigned to the user. So first, get all users to lessons assignments (so we don't have to perform loops with queries)
            foreach ($result as $value) {
                $usersToLessons[$value['users_LOGIN']][] = $value['lessons_ID'];        //Create a practical array representation
            }
            $courseLessons = array_keys($this -> getLessons());                         //Get the course's lessons

            $result        = eF_getTableData("users_to_courses uc, users u", "u.login, u.name, u.surname, u.user_type as basic_user_type, u.active, u.user_types_ID, uc.user_type as role", "uc.users_LOGIN = u.login and uc.courses_ID=".$this -> course['id']);
            foreach ($result as $value) {
                $this -> users[$value['login']] = $value;
                foreach ($courseLessons as $lesson) {                                   //For each lesson, check if the user has it. If he doesn't, add him to the lesson.
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
        $result      = eF_getTableData("users u", "u.login, u.name, u.surname, u.user_type as basic_user_type, u.user_types_ID, u.active", "active=1 and user_type != 'administrator' and languages_NAME='".$this -> course['languages_NAME']."' and login NOT IN ('".implode("','", array_keys($lessonUsers))."')");

        if (sizeof($result) > 0) {
            foreach ($result as $user) {
                $user['user_types_ID'] ? $user['role'] = $user['user_types_ID'] : $user['role'] = $user['user_type'];
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
            $role  = array($role);
        }
        if (sizeof($role) < sizeof($login)) {
            $role = array_pad($role, sizeof($login), $role);
        }

        foreach ($login as $key => $value) {
            if (eF_checkParameter($value, 'login')) {
                $fields = array('users_LOGIN'    => $value,
                                'courses_ID'     => $this -> course['id'],
                                'active'         => 1,
                                'from_timestamp' => $confirmed ? time() : 0,
                                'user_type'      => current($role));
                if (!eF_insertTableData("users_to_courses", $fields)) {
                    $errors[] = _CANNOTADDUSERTOCOURSE.' ('.EfrontLessonException :: DATABASE_ERROR.')';
                } else {
                    foreach ($this -> getLessons(true) as $lessonId => $lesson) {
                        $lesson -> addUsers($value, current($role), $confirmed);
                    }
                }
                next($role);
            } else {
                $errors[] = _INVALIDLOGIN.': '.$value.' ('.EfrontLessonException :: INVALID_LOGIN.')';
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
     * $course -> removeUser('jdoe');   //Remove user with login 'jdoe'
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
            if (eF_checkParameter($value, 'login')) {
                $result           = eF_getTableDataFlat("lessons l, users_to_courses uc, users_to_lessons ul, lessons_to_courses lc", "lc.lessons_ID, count(lc.lessons_ID)", "l.course_only = 1 and l.id=lc.lessons_ID and ul.users_LOGIN=uc.users_LOGIN and lc.lessons_ID=ul.lessons_ID and lc.courses_ID=uc.courses_ID and uc.users_LOGIN='".$value."'", "", "lc.lessons_ID, uc.users_LOGIN");
                $lessonsToCourses = array_combine($result['lessons_ID'], $result['count(lc.lessons_ID)']);
                if (eF_deleteTableData("users_to_courses", "users_LOGIN='$value' and courses_ID=".$this -> course['id'])) {
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
        if (!in_array($user, array_keys($this -> getUsers()))) {                                        //If this is an invalid user or does not have this course, issue an exception
            throw new EfrontCourseException(_UNKNOWNUSER.': '.$user, EfrontCourseException :: INVALID_LOGIN);
        }

        $allowed = array_combine(array_keys($this -> getLessons()), array_fill(0, sizeof($this -> getLessons()), 1));       //By default, all lessons are accessible
        $result  = eF_getTableDataFlat("users_to_lessons ul, lessons l", "ul.lessons_ID, ul.completed, ul.user_type, l.from_timestamp, l.to_timestamp", "ul.lessons_ID = l.id and ul.users_LOGIN='$user'");
        foreach ($result['lessons_ID'] as $key => $value) {
            $result['from_timestamp'][$key] ? $dates[$value]['from_timestamp'] = $result['from_timestamp'][$key] : null;
            $result['to_timestamp'][$key]   ? $dates[$value]['to_timestamp']   = $result['to_timestamp'][$key]   : null;
        }

        if (sizeof($result) > 0 && $result['user_type'][0] == 'student') {
            $completedLessons = array_combine($result['lessons_ID'], $result['completed']);
        } else {
            return $allowed;
        }

        foreach ($this -> rules as $lessonId => $lessonRules) {
            $evalString = '';
            for ($i = 1; $i < sizeof($lessonRules['lesson']); $i++) {
                $evalString .=  $completedLessons[$lessonRules['lesson'][$i]].' '.($lessonRules['condition'][$i+1] == 'and' ? '&' : '|');
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
            $role  = array($role);
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
        $fields = array('name'                  => $this -> course['name'],
                        'directions_ID'         => $this -> course['directions_ID'],
                        'info'                  => $this -> course['info'],
                        'price'                 => $this -> course['price'],
                        'active'                => $this -> course['active'],
                        'languages_NAME'        => $this -> course['languages_NAME'],
                        'metadata'              => $this -> course['metadata'],
                        'certificate'           => $this -> course['certificate'],
                        'auto_certificate'      => $this -> course['auto_certificate'],
                        'auto_complete'         => $this -> course['auto_complete'],
                        'rules'                 => $this -> course['rules'],
                        'certificate_tpl_id'    => $this -> course['certificate_tpl_id']);

        eF_updateTableData("courses", $fields, "id=".$this -> course['id']);
        EfrontSearch :: removeText('courses', $this -> course['id'], '');
        EfrontSearch :: insertText($fields['name'], $this -> course['id'], "courses", "title");
        return true;
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
     * @param string $login The user to revoke certificate for
     * @return boolean Whether the certificate was revoked successfully
     * @since 3.5.0
     * @accee
     */
    public function revokeCertificate($login) {
        if (eF_checkParameter($login, 'login')) {
            eF_updateTableData("users_to_courses", array("issued_certificate" => ""), "users_LOGIN='$login' and courses_ID=".$this -> course['id']);
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
            $courseUser  = EfrontUserFactory :: factory($login);
            $userStats   = EfrontStats::getUsersCourseStatus($this, $login);
            $data['organization'] = $GLOBALS['configuration']['site_name'];
            $data['course_name']  = $this -> course['name'];
            $data['user_surname'] = $courseUser -> user['surname'];
            $data['user_name']    = $courseUser -> user['name'];
            $data['grade']        = $userStats[$this -> course['id']][$login]['score'];
            $data['date']         = formatTimestamp(time());
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
        if (!$this -> course['certificate'] && is_file(G_SMARTYPATH."certificate-".$this -> course['languages_NAME'].".tpl")) {
            $certificate = file_get_contents(G_SMARTYPATH."certificate-".$this -> course['languages_NAME'].".tpl");
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
     * @return string The HTML code of the course list
     * @since 3.5.0
     * @access public
     */
/*    
    public function toHTML() {
        $roles     = EfrontLessonUser :: getLessonsRoles();
        $roleNames = EfrontLessonUser :: getLessonsRoles(true);

        $courseString .= '
                        <table class = "lessonsTable">
                            <tr>
                                <td class = "lessonsList" width = "1%">
                                    <img name = "default_visible_image" id = "course_img'.$this -> course['id'].'" src = "images/others/minus.png" style = "vertical-align:middle" align = "center" onclick = "show_hide($(\'course_img'.$this -> course['id'].'\'), \'subtree_course'.$this -> course['id'].'\');">
                                </td>
                                <td class = "lessonsList">
                                    <table>
                                        <tr><td><img src = "images/24x24/books.png" alt = "Categories" title = "Categories" style = "vertical-align:middle"/></td>
                                            <td class = "lessonsList_title">
                                                '.$this -> toHTMLTooltipLink();
                                                //.($this -> userStatus['user_type'] != $this -> userStatus['basic_user_type'] ? '&nbsp;<span style = "color:green; font-size: 9px; display:inline">('.EfrontUser :: $basicUserTypesTranslations[$this -> userStatus['user_type']].')</span>' : null);
        if ((!$this -> userStatus['user_types_ID'] && $this -> userStatus['user_type'] != $this -> userStatus['basic_user_type']) || ($this -> userStatus['user_types_ID'] && $this -> userStatus['user_type'] != $this -> userStatus['user_types_ID'])) {
            $courseString .= '
                                &nbsp;<span style = "color:green; font-size: 9px; display:inline">('.$roleNames[$this -> userStatus['user_type']].')</span>';
        }

        if ($this -> userStatus['user_type'] == 'professor') {
            $courseString .= '</td><td style = "padding-left:10px">
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_info" >
                                                    <img src = "images/16x16/about.png" title = "'._COURSEINFORMATION.'" alt = "'._COURSEINFORMATION.'" border = "0" style = "vertical-align:middle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_certificates">
                                                    <img src = "images/16x16/certificate_add.png" title = "'._COURSECERTIFICATES.'" alt = "'._COURSECERTIFICATES.'" border = "0" style = "vertical-align:middle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_rules">
                                                    <img src = "images/16x16/recycle.png" title = "'._COURSERULES.'" alt = "'._COURSERULES.'" border = "0" style = "vertical-align:middle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_order">
                                                    <img src = "images/16x16/replace2.png" title = "'._COURSEORDER.'" alt = "'._COURSEORDER.'" border = "0" style = "vertical-align:middle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_scheduling">
                                                    <img src = "images/16x16/calendar.png" title = "'._COURSESCHEDULE.'" alt = "'._COURSESCHEDULE.'" border = "0" style = "vertical-align:middle"></a>';
        } elseif ($this -> userStatus['user_type'] == 'student') {
            if ($this -> userStatus['completed']) {
                $courseString .= '</td><td style = "padding-left:10px">
                                                <img src = "images/16x16/check.png" title = "'._COURSECOMPLETED.'" alt = "'._COURSECOMPLETED.'" border = "0" style = "vertical-align:middle">';

                if ($this -> userStatus['issued_certificate']) {
                    $courseString .= '
                                                <a href = "student.php?ctg=lessons&course='.$this -> course['id'].'&export=rtf&user='.$this -> userStatus['login'].'" target="_blank">
                                                    <img src = "images/16x16/certificate.png" title = "'._COURSECERTIFICATE.'" alt = "'._COURSECERTIFICATE.'" border = "0" style = "vertical-align:middle"></a>';
                }
            }

        }
        if (!isset($this -> userStatus)) {
            $courseString .= '
                                                &nbsp;<span style = "color:green; font-size: 9px; display:inline">(
                                                '.($this -> course['price'] ? $this -> course['price'] : _FREECOURSE).'
                                                )</span>&nbsp;<a href = ""><img style = "vertical-align:middle" src = "images/16x16/money.png" title = "'._BUYCOURSE.'" alt = "'._BUYCOURSE.'" border = "0"></a>';
        } else {
            if ($this -> userStatus['user_type'] == 'student') {
                $eligible = $this -> checkRules($this -> userStatus['login']);
            } else {
                $eligible = array_combine(array_keys($this -> getLessons()), array_fill(0, sizeof($this -> getLessons()), 1));    //All lessons set to true
            }

            foreach ($eligible as $lessonId => $value) {
                $eligible[$lessonId] = new EfrontLesson($lessonId);
                $eligible[$lessonId] -> eligible = $value;
                if (!$eligible[$lessonId] -> lesson['active'] || !in_array($lessonId, array_keys($this -> getLessons()))) {
                    unset($eligible[$lessonId]);                        //Remove inactive lessons from list
                }
            }
        }

        $courseString .= '
                                            </td></tr>
                                    </table>
                                </td></tr>';

        if (sizeof($eligible) > 0) {
            $this -> userStatus['login'] ? $courseUser = $this -> userStatus['login'] : $courseUser = false;
            $courseString .= '
                            <tr id = "subtree_course'.$this -> course['id'].'" name = "default_visible">
                                <td class = "lessonsList_nocolor">&nbsp;</td>
                                <td>';

            $courseString .= '
                                    <table width = "100%">';

            //The call to EfrontLesson :: getInformation (which is done by EfrontLesson :: toHTMLTooltipLink) can get rather expensive for many lessons, so we build the information here
            $resultTests      = eF_getTableDataFlat("content", "count(*), lessons_ID", "active=1 and (ctg_type = 'tests' or ctg_type = 'scorm_test')", "", "lessons_ID");
            $resultTests      = array_combine($resultTests['lessons_ID'], $resultTests['count(*)']);
            $resultTheory     = eF_getTableDataFlat("content", "count(*), lessons_ID", "active=1 and ctg_type = 'theory'", "", "lessons_ID");
            $resultTheory     = array_combine($resultTheory['lessons_ID'], $resultTheory['count(*)']);
            $resultExamples   = eF_getTableDataFlat("content", "count(*), lessons_ID", "active=1 and ctg_type = 'examples'", "", "lessons_ID");
            $resultExamples   = array_combine($resultExamples['lessons_ID'], $resultExamples['count(*)']);
            $resultProjects   = eF_getTableDataFlat("projects", "count(*), lessons_ID", "", "", "lessons_ID");
            $resultProjects   = array_combine($resultProjects['lessons_ID'], $resultProjects['count(*)']);
            $result           = eF_getTableData("users u, users_to_lessons ul", "u.login, u.name, u.surname, ul.lessons_ID", "u.login=ul.users_LOGIN and ul.user_type='professor'", "ul.lessons_ID");
            foreach ($result as $value) {
                $resultProfessors[$value['lessons_ID']][$value['login']] = $value;
            }

            foreach ($eligible as $lessonId => $lesson) {
                //Assign the information to each lesson
                $information[$lessonId]['content']    = $resultTheory[$lessonId] + $resultExamples[$lessonId];
                $information[$lessonId]['tests']      = $resultTests[$lessonId];
                $information[$lessonId]['professors'] = $resultProfessors[$lessonId];
                $information[$lessonId]['projects']   = $resultProjects[$lessonId];
                $information[$lessonId]['projects']   = $resultProjects[$lessonId];
                if ($lesson -> lesson['info']) {
                    unserialize($lesson -> lesson['info']) !== false ? $information[$lessonId] = array_merge($information[$lessonId], unserialize($lesson -> lesson['info'])) : $information[$lessonId] = array_merge($information[$lessonId], $lesson -> lesson['info']);
                }

                $courseString .= '
                                            <tr>';
                if ($courseUser) {
                    $lesson -> userStatus = $this -> userStatus['lesson_status'][$lessonId];
                }
                if ($courseUser && $roles[$lesson -> userStatus['user_type']] == 'student') {
                    if ($lesson -> userStatus['completed']){
                        $courseString .= '
                                                <td style = "width:5px;text-align:center">
                                                    <img src = "images/16x16/check.png" alt = "'._LESSONCOMPLETE.'" title = "'._LESSONCOMPLETE.'" style = "margin-left:10px;vertical-align:middle" />
                                                </td>';
                    } else {
                        $courseString .= '
                                                <td style = "width:50px;">
                                                    <span style = "position:absolute;text-align:center;width:50px;border:1px solid #d3d3d3;vertical-align:middle;z-index:2">'.$lesson -> userStatus['overall_progress'].'%</span>
                                                    <span style = "background-color:#A0BDEF;width:'.($lesson -> userStatus['overall_progress']/2).'px;border:1px dotted #d3d3d3;position:absolute">&nbsp;</span>
                                                    &nbsp;&nbsp;
                                                </td>';
                    }
                } else {
                    $courseString .= '
                                                <td></td>';
                }
                $courseString .= '
                                                <td class = "lessonsList_lessons">&nbsp;';

                $lessonInformation = array_merge(array('course_dependency' => ''), $information[$lessonId]);    //This is to ensure that dependency will move to the beginning of the list

                foreach ($this -> rules[$lessonId]['lesson'] as $key => $id) {
                    $lessonInformation['course_dependency'] .= $this -> lessons[$id]['name'];
                    if ($this -> rules[$lessonId]['condition'][$key+1]) {
                        $this -> rules[$lessonId]['condition'][$key+1] == 'and' ? $lessonInformation['course_dependency'] .= '&nbsp;<b>'._AND.'</b>&nbsp;' : $lessonInformation['course_dependency'] .= '&nbsp;<b>'._OR.'</b>&nbsp;';
                    }
                }

                if ($courseUser && ($lesson -> eligible)) {
                    $courseString .= $lesson -> toHTMLTooltipLink($roles[$lesson -> userStatus['user_type']].'.php?ctg=control_panel&lessons_ID='.$lessonId, $lessonInformation);
                } elseif (!$courseUser) {
                    $courseString .= $lesson -> lesson['name'];
                } else {
                    $courseString .= $lesson -> toHTMLTooltipLink(false, $lessonInformation);
                }
                if ($lesson -> userStatus['user_type'] != $this -> userStatus['user_type']) {
                    $courseString .= '&nbsp;<span style = "color:green; font-size: 9px; display:inline">('.$roleNames[$lesson -> userStatus['user_type']].')</span>';
                }
                $courseString .= '
                                            </td></tr>';

            }
            $courseString .= '
                                    </table>
                                </td></tr>';
        }
        $courseString .= '
                        </table>';

        return $courseString;
    }
*/
    
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
        
        $roles     = EfrontLessonUser :: getLessonsRoles();
        $roleNames = EfrontLessonUser :: getLessonsRoles(true);
        $roleBasicType = $roles[$userInfo['courses'][$this -> course['id']]['user_type']];        //The basic type of the user's role in the course

        if ($userInfo['courses'][$this -> course['id']]['user_type'] == 'student') {
            $eligible = $this -> checkRules($userInfo['courses'][$this -> course['id']]['login']);
        } else {
            $eligible = array_combine(array_keys($this -> getLessons()), array_fill(0, sizeof($this -> getLessons()), 1));    //All lessons set to true
        }
        
            //$eligible = array_combine(array_keys($this -> getLessons()), array_fill(0, sizeof($this -> getLessons()), 1));    //All lessons set to true
        foreach ($eligible as $lessonId => $value) {
            $eligible[$lessonId] = new EfrontLesson($lessonId);
            $eligible[$lessonId] -> eligible = $value;
            if (!$eligible[$lessonId] -> lesson['active'] || !in_array($lessonId, array_keys($this -> getLessons()))) {
                unset($eligible[$lessonId]);                        //Remove inactive lessons from list
            }
        }
        
        $courseString .= '
                        <table class = "lessonsTable">
                            <tr>
                                <td class = "lessonsList" width = "1%">
                                    <img name = "default_visible_image" id = "course_img'.$this -> course['id'].'" src = "images/others/minus.png" style = "vertical-align:middle" align = "center" onclick = "show_hide($(\'course_img'.$this -> course['id'].'\'), \'subtree_course'.$this -> course['id'].'\');">
                                </td>
                                <td class = "lessonsList">
                                    <table>
                                        <tr><td><img src = "images/24x24/books.png" alt = "Categories" title = "Categories" style = "vertical-align:middle"/></td>
                                            <td class = "lessonsList_title">';
        if (!isset($userInfo['courses'][$this -> course['id']]['from_timestamp']) || $userInfo['courses'][$this -> course['id']]['from_timestamp']) {
            if ($options['tooltip']) {
                $courseString .= '
            									<a href = "'.($options['courses_link'] ? str_replace("#user_type#", $roleBasicType, $options['courses_link']).$this -> course['id'] : 'javascript:void(0)').'" class = "info" onmouseover = "updateInformation(this, '.$this -> course['id'].', \'course\')" onclick = "this.update(\''.$this -> course['name'].'\');">
            										'.$this -> course['name'].'
            										<img class = "tooltip" border = "0" src = "images/others/tooltip_arrow.gif"/>
            										<span class = "tooltipSpan"></span>
            									</a>';             
            } else {
                $options['courses_link'] ? $courseString .= '<a href = "'.str_replace("#user_type#", $roleBasicType, $options['courses_link']).$this -> course['id'].'">'.$courseString .= $this -> course['name'].'</a>' : $courseString .= $this -> course['name'];
            }
        } else {
            $courseString .= '<a href = "javascript:void(0)" class = "inactiveLink" title = "'._CONFIRMATIONPEDINGFROMADMIN.'">'.$this -> course['name'].'</a>';
        }
        if ($userInfo['courses'][$this -> course['id']]['different_role']) {
            $courseString .= '&nbsp;<span style = "color:green; font-size: 9px; display:inline">('.$roleNames[$userInfo['courses'][$this -> course['id']]['user_type']].')</span>';
        }
        if ($userInfo['courses'][$this -> course['id']]['user_type'] == 'professor') {
            $courseString .= '</td><td style = "padding-left:10px">
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_info" >
                                                    <img src = "images/16x16/about.png" title = "'._COURSEINFORMATION.'" alt = "'._COURSEINFORMATION.'" border = "0" style = "vertical-align:middle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_certificates">
                                                    <img src = "images/16x16/certificate_add.png" title = "'._COURSECERTIFICATES.'" alt = "'._COURSECERTIFICATES.'" border = "0" style = "vertical-align:middle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_rules">
                                                    <img src = "images/16x16/recycle.png" title = "'._COURSERULES.'" alt = "'._COURSERULES.'" border = "0" style = "vertical-align:middle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_order">
                                                    <img src = "images/16x16/replace2.png" title = "'._COURSEORDER.'" alt = "'._COURSEORDER.'" border = "0" style = "vertical-align:middle"></a>
                                                <a href = "professor.php?ctg=lessons&course='.$this -> course['id'].'&op=course_scheduling">
                                                    <img src = "images/16x16/calendar.png" title = "'._COURSESCHEDULE.'" alt = "'._COURSESCHEDULE.'" border = "0" style = "vertical-align:middle"></a>';            
        } else {
            if ($userInfo['courses'][$this -> course['id']]['completed']) {
                $courseString .= '</td><td style = "padding-left:10px">
                                                <img src = "images/16x16/check.png" title = "'._COURSECOMPLETED.'" alt = "'._COURSECOMPLETED.'" border = "0" style = "vertical-align:middle">';

                if ($userInfo['courses'][$this -> course['id']]['issued_certificate']) {
                    $courseString .= '
                                                <a href = "student.php?ctg=lessons&course='.$this -> course['id'].'&export=rtf&user='.$this -> userStatus['login'].'" target="_blank">
                                                    <img src = "images/16x16/certificate.png" title = "'._COURSECERTIFICATE.'" alt = "'._COURSECERTIFICATE.'" border = "0" style = "vertical-align:middle"></a>';
                }
            }            
        }
        $courseString .= '
                                            </td></tr>
                                    </table>
                                </td></tr>';

        if (sizeof($eligible) > 0) {
            $courseString .= '
                            <tr id = "subtree_course'.$this -> course['id'].'" name = "default_visible">
                                <td class = "lessonsList_nocolor">&nbsp;</td>
                                <td>
                                	<table width = "100%">';
            
            foreach ($eligible as $lessonId => $lesson) {    
//pr($lesson);                   
                //$roleBasicType = $roles[$userInfo['lessons'][$lessonId]['user_type']];        //The basic type of the user's role in the lesson 
                $courseString .= '<tr class = "directionEntry">';
                if (isset($userInfo['lessons'][$lessonId]['from_timestamp']) && !$userInfo['lessons'][$lessonId]['from_timestamp']) {
                    $courseString .= '<td style = "padding-bottom:2px"></td><td><a href = "javascript:void(0)" class = "inactiveLink" title = "'._CONFIRMATIONPEDINGFROMADMIN.'">'.$lesson -> lesson['name'].'</a></td>';
                } else if (!$lesson -> eligible) {
					$courseString  .= '
                            <td style = "width:50px;padding-bottom:2px">
                                <span class = "progressNumber" style = "width:50px;">'.$userInfo['lessons'][$lessonId]['overall_progress'].'%</span>
                                <span class = "progressBar" style = "width:'.($userInfo['lessons'][$lessonId]['overall_progress'] / 2).'px;">&nbsp;</span>
                                &nbsp;&nbsp;
                            </td>
                            <td class = "lessonsList_lessons">&nbsp;
                            	<a href = "javascript:void(0)" title = "" class = "inactiveLink info" onmouseover = "updateInformation(this, '.$lesson -> lesson['id'].', \'lesson\')">
                            		'.$lesson -> lesson['name'].'
                            		<img class = "tooltip" border = "0" src = "images/others/tooltip_arrow.gif"/>
                            		<span class = "tooltipSpan"></span>
                            	</a>
                            <td>';
					
                } else {
                    if ($roles[$userInfo['lessons'][$lessonId]['user_type']] == 'student') {                                                                //Show the progress bar
						$courseString  .= '
                            <td style = "width:50px;padding-bottom:2px">
                                <span class = "progressNumber" style = "width:50px;">'.$userInfo['lessons'][$lessonId]['overall_progress'].'%</span>
                                <span class = "progressBar" style = "width:'.($userInfo['lessons'][$lessonId]['overall_progress'] / 2).'px;">&nbsp;</span>
                                &nbsp;&nbsp;
                            </td>';                            
					} else {
						$courseString .= '
							<td style = "width:1px;padding-bottom:2px"></td>';
					}
										
                    $courseString .= '
                    		<td class = "lessonsList_lessons">&nbsp;
                    			'.($options['lessons_link'] ? '<a href = "'.str_replace("#user_type#", $roleBasicType, $options['lessons_link']).$lesson -> lesson['id'].'" class = "info" onmouseover = "updateInformation(this, '.$lesson -> lesson['id'].', \'lesson\')" onclick = "this.update(\''.$lesson -> lesson['name'].'\');">'.$lesson -> lesson['name'].'<img class = "tooltip" border = "0" src = "images/others/tooltip_arrow.gif"/><span class = "tooltipSpan"></span></a>' : $lesson -> lesson['name']).'
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
            if ($user['user_type'] == 'professor') {
                $information['professors'][$key] = $user;
            }
        }
        $information['lessons_number'] = sizeof($this -> getLessons());

        return $information;
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

        if ($courseInformation['professors']) {
            foreach ($courseInformation['professors'] as $value) {
                $professorsString[] = $value['name'].' '.$value['surname'];
            }
            $courseInformation['professors'] = implode(", ", $professorsString);
        }

        foreach ($courseInformation as $key => $value) {
            if ($value) {
                switch ($key) {
                    case 'professors'         : $tooltipInfo[] = '<strong>'._PROFESSORS."</strong>: $value<br/>";         break;
                    case 'lessons_number'     : $tooltipInfo[] = '<strong>'._LESSONS."</strong>: $value<br/>";            break;
                    case 'general_description': $tooltipInfo[] = '<strong>'._GENERALDESCRIPTION."</strong>: $value<br/>"; break;
                    case 'assessment'         : $tooltipInfo[] = '<strong>'._ASSESSMENT."</strong>: $value<br/>";         break;
                    case 'objectives'         : $tooltipInfo[] = '<strong>'._OBJECTIVES."</strong>: $value<br/>";         break;
                    case 'lesson_topics'      : $tooltipInfo[] = '<strong>'._LESSONTOPICS."</strong>: $value<br/>";       break;
                    case 'resources'          : $tooltipInfo[] = '<strong>'._RESOURCES."</strong>: $value<br/>";          break;
                    case 'other_info'         : $tooltipInfo[] = '<strong>'._OTHERINFO."</strong>: $value<br/>";          break;
                    default: break;
                }
            }
        }
        if (sizeof($tooltipInfo) > 0) {
            $classes[]     = 'info';
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
        !isset($fields['name'])           ? $fields['name']           = 'Default name'                                : null;
        !isset($fields['languages_NAME']) ? $fields['languages_NAME'] = $GLOBALS['configuration']['default_language'] : null;

        $languages      = EfrontSystem :: getLanguages(true);
        $courseMetadata = array('title'       => $fields['name'],
                                'creator'     => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                'publisher'   => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                'contributor' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                'date'        => date("Y/m/d", time()),
                                'language'    => $languages[$fields['languages_NAME']]);
        $fields['metadata'] = serialize($lessonMetadata);

        $newId = eF_insertTableData("courses", $fields);
        EfrontSearch :: insertText($fields['name'], $newId, "courses", "title");
        $course = new EfrontCourse($newId);

        // Insert the corresponding lesson skill to the skill and lesson_offers_skill tables
        $courseSkillId = eF_insertTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFCOURSE . " ". $fields['name'], "categories_ID" => -1));
        eF_insertTableData("module_hcd_course_offers_skill", array("courses_ID" => $newId, "skill_ID" => $courseSkillId));


        return $course;
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
     * @return array The lessons list
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function getCourses() {
        $result = eF_getTableData("courses c", "c.*");
        foreach ($result as $value) {
            $courses[$value['id']] = $value;
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
        if (! $this -> skills) {
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