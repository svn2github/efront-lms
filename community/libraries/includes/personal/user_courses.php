<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if (isset($currentUser->coreAccess['users']) && $currentUser->coreAccess['users'] != 'change') {
 $_change_courses_ = false;
} else if ($currentUser -> user['user_type'] == 'administrator') {
 $_change_courses_ = true;
} else if ($currentUser -> user['login'] == $editedUser -> user['login']) {
 $_change_courses_ = false;
} else if (!$currentEmployee -> isSupervisor()) {
 $_change_courses_ = false;
} else if ($currentEmployee -> supervisesEmployee($editedUser->user['login'])) {
 $_change_courses_ = true;
} else {
 $_change_courses_ = false;
}
$smarty -> assign("_change_courses_", $_change_courses_);


try {
 if (isset($_GET['postAjaxRequest'])) {
  if (isset($_GET['add_course'])) {
   if ($_GET['insert'] == "true") {
    $editedUser -> addCourses($_GET['add_course'], $_GET['user_type'], 1);
   } else if ($_GET['insert'] == "false") {
    $editedUser -> archiveUserCourses($_GET['add_course']);
   } else if (isset($_GET['addAll'])) {
    $constraints = array('archive' => false, 'active' => true, 'instance' => isset($_GET['instancesTable_source']) && $_GET['instancesTable_source'] ? $_GET['instancesTable_source'] : false) + createConstraintsFromSortedTable();
    $constraints['condition'] = 'r.courses_ID is null or r.archive != 0';
    $userCourses = $editedUser -> getUserCoursesIncludingUnassigned($constraints);
    $editedUser -> addCourses($userCourses, $editedUser -> user['user_type'], 1);
   } else if (isset($_GET['removeAll'])) {
    $constraints = array('archive' => false, 'active' => true, 'instance' => isset($_GET['instancesTable_source']) && $_GET['instancesTable_source'] ? $_GET['instancesTable_source'] : false) + createConstraintsFromSortedTable();
    $userCourses = $editedUser -> getUserCourses($constraints);
    $editedUser -> archiveUserCourses($userCourses);
   } else if (isset($_GET['addAllCoursesFromTest'])) {
    // The missing and required skill set is sent over with the ajax request
    $skills_missing = array();
    $all_skills = "";
    foreach ($_GET as $key => $value) {
     // all skill-related posted values are just the skill_ID ~ a uint value
     if (eF_checkParameter($key, 'unit')) {
      if ($value == 1) {
       $skills_missing[] = $key;
      }
     }
    }

    // We found all the skills missing
    $skills_missing = implode("','", $skills_missing);

    // We have all the already attended courses
    $alredy_attending = implode("','", array_keys($editedUser -> getUserCourses()));

    // Thus we can find the missing courses to fill the skill gap
    $courses_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID JOIN courses ON courses.id = module_hcd_course_offers_skill.courses_ID","module_hcd_course_offers_skill.courses_ID, courses.*, count(module_hcd_course_offers_skill.skill_ID) as skills_offered", "module_hcd_course_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_course_offers_skill.courses_ID NOT IN ('".$alredy_attending."')","","module_hcd_course_offers_skill.courses_ID ORDER BY skills_offered DESC");

    // And assign them
    foreach ($courses_proposed as $course) {
     $editedUser -> addCourses($course['courses_ID']);
    }
   }
   exit;
  }

 }

 if (isset($_GET['ajax']) && $_GET['ajax'] == 'toggle_user' && $_GET['type'] == 'course') {
  $response = array('status' => 1);
  $editCourse = new EfrontCourse($_GET['id']);
  if ($editCourse -> isUserActiveInCourse($editedUser)) {
   $editCourse -> unConfirm($editedUser);
   $response['access'] = 0;
  } else {
   $editCourse -> confirm($editedUser);
   $response['access'] = 1;
  }
  echo json_encode($response);
  exit;
 } else if (isset($_GET['ajax']) && in_array($_GET['ajax'], array('courseLessonsTable', 'instancesTable', 'coursesTable'))) {

  $roles = EfrontLessonUser :: getLessonsRoles(true);
  $smarty -> assign("T_ROLES_ARRAY", $roles);

  $directionsTree = new EfrontDirectionsTree();
  $smarty -> assign("T_DIRECTION_PATHS", $directionsTree->toPathString());

  $rolesBasic = EfrontLessonUser :: getLessonsRoles();
  $smarty -> assign("T_BASIC_ROLES_ARRAY", $rolesBasic);

  $smarty -> assign("T_EDITED_USER_TYPE", $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type']);

  if (isset($_GET['ajax']) && $_GET['ajax'] == 'courseLessonsTable' && eF_checkParameter($_GET['courseLessonsTable_source'], 'id')) {
   $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'completed', 'score'));
   $course = new EfrontCourse($_GET['courseLessonsTable_source']);
   $courseLessons = $course -> getCourseLessons();
   $userLessons = $editedUser -> getUserStatusInCourseLessons($course);
   foreach ($userLessons as $key => $value) {
    $courseLessons[$key] = $value;
   }
   $lessons = EfrontLesson :: convertLessonObjectsToArrays($courseLessons);
   $dataSource = $lessons;
  }


  if ($_GET['ajax'] == 'coursesTable' || $_GET['ajax'] == 'instancesTable') {
   if (isset($_GET['ajax']) && $_GET['ajax'] == 'coursesTable') {
    $_GET['sort'] != 'null' OR $_GET['sort'] = 'has_course';
    $constraints = array('archive' => false, 'instance' => false) + createConstraintsFromSortedTable();
    $constraints['required_fields'] = array('has_instances', 'location', 'active_in_course', 'user_type', 'completed', 'score', 'has_course', 'num_lessons');
    $constraints['return_objects'] = false;
    if ($_change_courses_) {
     $courses = $editedUser -> getUserCoursesAggregatingResultsIncludingUnassigned($constraints);
     $totalEntries = $editedUser -> countUserCoursesAggregatingResultsIncludingUnassigned($constraints);
    } else {
     $courses = $editedUser -> getUserCoursesAggregatingResults($constraints);
     $totalEntries = $editedUser -> countUserCoursesAggregatingResults($constraints);
    }
   }
   if (isset($_GET['ajax']) && $_GET['ajax'] == 'instancesTable' && eF_checkParameter($_GET['instancesTable_source'], 'id')) {
    $constraints = array('archive' => false, 'active' => true, 'instance' => $_GET['instancesTable_source']) + createConstraintsFromSortedTable();
    $constraints['required_fields'] = array('has_instances', 'location', 'active_in_course', 'user_type', 'completed', 'score', 'has_course', 'num_lessons');
    $constraints['return_objects'] = false;
    if ($_change_courses_) {
     $courses = $editedUser -> getUserCoursesIncludingUnassigned($constraints);
     $totalEntries = $editedUser -> countUserCoursesIncludingUnassigned($constraints);
    } else {
     $courses = $editedUser -> getUserCourses($constraints);
     $totalEntries = $editedUser -> countUserCourses($constraints);
    }
   }

   $alreadySorted = true;
   $dataSource = $courses;
   $smarty -> assign("T_SHOW_COURSE_LESSONS", true);
   $smarty -> assign("T_TABLE_SIZE", $totalEntries);
  }

  $tableName = $_GET['ajax'];
  include("sorted_table.php");
 }

} catch (Exception $e) {
 handleAjaxExceptions($e);
}
