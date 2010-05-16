<?php
/**

* curriculums Class file

*

* @package eFront

* @version 3.6

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
class EfrontCurriculumException extends Exception
{
 const EMPTY_COURSE = 1201;
}
class curriculums extends EfrontEntity
{
    /**

     * The curriculums properties

     * 

     * @since 3.6.0

     * @var array

     * @access public

     */
    public $curriculums = array();
    public function addCourses($courses) {
     $courses = EfrontCourse :: verifyCoursesList($courses);
     foreach ($courses as $course) {
      eF_insertTableData("curriculums_to_courses", array("curriculums_ID" => $this -> curriculums['id'], "courses_ID" => $course -> course['id']));
     }
    }
    public function removeCourses($courses) {
     $courses = EfrontCourse :: verifyCoursesList($courses);
     foreach ($courses as $course) {
      eF_deleteTableData("curriculums_to_courses", "curriculums_ID=".$this -> curriculums['id']." and courses_ID=".$course -> course['id']);
     }
    }
    public function assignToUser($user) {
     $courses = $this -> getCurriculumCourses();
     if (!empty($courses)) {
      $user -> addCourses($courses);
     } else {
      throw new EfrontCurriculumException(_YOUCANNOTASSIGNUSERSBECAUSECURRICULUMISEMPTY, EfrontCurriculumException :: EMPTY_COURSE);
     }
     eF_insertTableData("curriculums_to_users", array("users_LOGIN" => $user -> user['login'], "curriculums_ID" => $this -> curriculums['id']));
    }
    public function removeFromUser($user) {
     $courses = $this -> getCurriculumCourses();
     if (!empty($courses)) {
      $user -> removeCourses($courses);
     } else {
      throw new EfrontCurriculumException(_YOUCANNOTASSIGNUSERSBECAUSECURRICULUMISEMPTY, EfrontCurriculumException :: EMPTY_COURSE);
     }
     eF_deleteTableData("curriculums_to_users", "curriculums_ID=".$this -> curriculums['id']);
    }
    public function assignToGroup($group) {
     $courses = $this -> getCurriculumCourses();
     $group -> addCourses($courses);
    }
    public function getCurriculumCourses($constraints = array()) {
     !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $select = "c.*, cc.courses_ID, 1 as has_course";
  $where[] = "c.id=cc.courses_ID and cc.curriculums_ID='".$this -> curriculums['id']."'";
     $result = eF_getTableData("courses c, curriculums_to_courses cc", $select,
         implode(" and ", $where), $orderby, $groupby, $limit);
  return EfrontCourse :: convertDatabaseResultToCourseObjects($result);
    }
    public function countCurriculumCourses($constraints = array()) {
     !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
     list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $where[] = "c.id=cc.courses_ID and cc.curriculums_ID='".$this -> curriculums['id']."'";
     $result = eF_countTableData("courses c, curriculums_to_courses cc", "c.id",
         implode(" and ", $where));
  return $result[0]['count'];
    }
    public function getCurriculumCoursesIncludingUnassigned($constraints = array()) {
     !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $select = "c.*, r.courses_ID is not null as has_course";
  $result = eF_getTableData("courses c left outer join (select courses_ID from curriculums_to_courses where curriculums_ID='".$this -> curriculums['id']."') r on c.id=r.courses_ID ", $select,
         implode(" and ", $where), $orderby, "", $limit);
  return EfrontCourse :: convertDatabaseResultToCourseObjects($result);
    }

    public function countCurriculumCoursesIncludingUnassigned($constraints = array()) {
     !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
     list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
     $result = eF_countTableData("courses c left outer join (select courses_ID from curriculums_to_courses where curriculums_ID='".$this -> curriculums['id']."') r on c.id=r.courses_ID ", "c.id",
         implode(" and ", $where));

  return $result[0]['count'];
    }

    public function getCurriculumUsers($constraints = array()) {
     !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);

  list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  $select = "u.*, cu.users_LOGIN, 1 as has_user";
  $where[] = "u.login=cu.users_LOGIN and cu.curriculums_ID='".$this -> curriculums['id']."'";
     $result = eF_getTableData("users u, curriculums_to_users cu", $select,
         implode(" and ", $where), $orderby, $groupby, $limit);

  return EfrontUser :: convertDatabaseResultToUserObjects($result);
    }

    public function countCurriculumUsers($constraints = array()) {
     !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
     list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  $where[] = "u.login=cu.users_LOGIN and cu.curriculums_ID='".$this -> curriculums['id']."'";
     $result = eF_countTableData("users u, curriculums_to_users cu", "u.login",
         implode(" and ", $where));
  return $result[0]['count'];
    }

    public function getCurriculumUsersIncludingUnassigned($constraints = array()) {
     !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);

  list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  $select = "u.*, r.users_LOGIN is not null as has_user";
  $where[] = "u.user_type != 'administrator'";
  $result = eF_getTableData("users u left outer join (select users_LOGIN from curriculums_to_users where curriculums_ID='".$this -> curriculums['id']."') r on u.login=r.users_LOGIN ", $select,
         implode(" and ", $where), $orderby, "", $limit);

  return EfrontUser :: convertDatabaseResultToUserObjects($result);
    }

    public function countCurriculumUsersIncludingUnassigned($constraints = array()) {
     !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
     list($where, $limit, $orderby) = EfrontUser :: convertUserConstraintsToSqlParameters($constraints);
  $where[] = "u.user_type != 'administrator'";
     $result = eF_countTableData("users u left outer join (select users_LOGIN from curriculums_to_users where curriculums_ID='".$this -> curriculums['id']."') r on u.login=r.users_LOGIN ", "u.login",
         implode(" and ", $where));

  return $result[0]['count'];
    }

    /**

     * Create a curriculum

     * 

     * This function is used to create a curriculum

     * 

     * @param $fields An array of data

     * @return curriculum The new object

     * @since 3.6.0

     * @access public

     * @static

     */
    public static function create($fields = array(), $sendEmail = false) {
        $fields = array('name' => $fields['name'],
                        'active' => $fields['active'] ? $fields['active'] : 1,
            'description' => $fields['description']);
        $newId = eF_insertTableData("curriculums", $fields);
        $result = eF_getTableData("curriculums", "*", "id=".$newId); //We perform an extra step/query for retrieving data, sinve this way we make sure that the array fields will be in correct order (forst id, then name, etc)
        $curriculums = new curriculums($result[0]['id']);
        return $curriculums;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
     $form -> addElement('text', 'name', _NAME, 'class = "inputText"');
     $form -> addElement('textarea', 'description', _DESCRIPTION, 'class = "inputTextArea" style = "width:100%;height:5em');
     $form -> addElement('advcheckbox', 'active', _ACTIVE, null, null, array(0, 1));
     $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
     $form -> addRule('name', _THEFIELD.' "'._NAME.'" '._ISMANDATORY, 'required', null, 'client');
     if ($_GET['edit']) {
      $form -> setDefaults($this -> {$this -> entity});
     } else {
      $form -> setDefaults(array('active' => 1));
     }
        return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form) {
     $values = $form -> exportValues();
        if (isset($_GET['edit'])) {
            $this -> {$this -> entity}["name"] = $values['name'];
            $this -> {$this -> entity}["description"] = $values['description'];
            $this -> {$this -> entity}["active"] = $values['active'];
            $this -> persist();
        } else {
         $curriculums = self :: create($values);
            $this -> {$this -> entity} = $curriculums -> curriculums;
        }
    }
}
?>
