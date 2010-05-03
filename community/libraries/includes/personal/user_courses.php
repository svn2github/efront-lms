<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}


try {
 if (isset($_GET['postAjaxRequest'])) {
  if (isset($_GET['add_lesson'])) {
   if ($_GET['insert'] == "true") {
    $courseUser -> addLessons($_GET['add_lesson'], $_GET['user_type'], 1);
   } else if ($_GET['insert'] == "false") {
    $courseUser -> archiveUserLessons($_GET['add_lesson']);
   } else if (isset($_GET['addAll'])) {
    $userNonLessons = $courseUser -> getNonLessons(true);
    $lessons = array();
    foreach ($userNonLessons as $key => $lesson) {
     if (!$lesson -> lesson['course_only']) {
      $lessons[$lesson -> lesson['id']] = $lesson -> lesson;
     }
    }

    isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;
    $courseUser -> addLessons(array_keys($lessons), 0, 1);
   } else if (isset($_GET['removeAll'])) {
    $userLessons = $courseUser -> getLessons(true);
    $lessons = array();
    foreach ($userLessons as $key => $lesson) {
     if (!$lesson -> lesson['course_only']) {
      $lessons[$lesson -> lesson['id']] = $lesson -> lesson;
     }
    }
    isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;
    $courseUser -> archiveUserLessons(array_keys($lessons));
   } else if (isset($_GET['addAllLessonsFromTest'])) {
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
    $alredy_attending = implode("','", array_keys($courseUser -> getLessons()));

    // Thus we can find the missing courses to fill the skill gap
    $lessons_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID JOIN lessons ON lessons.id = module_hcd_lesson_offers_skill.lesson_ID","module_hcd_lesson_offers_skill.lesson_ID, lessons.*, count(module_hcd_lesson_offers_skill.skill_ID) as skills_offered", "module_hcd_lesson_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_lesson_offers_skill.lesson_ID NOT IN ('".$alredy_attending."')","","module_hcd_lesson_offers_skill.lesson_ID ORDER BY skills_offered DESC");

    // And assign them
    foreach ($lessons_proposed as $lesson) {
     $courseUser -> addLessons($lesson['lesson_ID']);
    }
   }
   exit;
  } else if (isset($_GET['add_course'])) {
   if ($_GET['insert'] == "true") {
    $courseUser -> addCourses($_GET['add_course'], $_GET['user_type'], 1);
   } else if ($_GET['insert'] == "false") {
    $courseUser -> archiveUserCourses($_GET['add_course']);
   } else if (isset($_GET['addAll'])) {
    $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true, 'instance' => isset($_GET['instancesTable_source']) && $_GET['instancesTable_source'] ? $_GET['instancesTable_source'] : false);
    $userCourses = $courseUser -> getUserCoursesIncludingUnassigned($constraints);
    $courseUser -> addCourses($userCourses, 0, 1);
   } else if (isset($_GET['removeAll'])) {
    $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true, 'instance' => isset($_GET['instancesTable_source']) && $_GET['instancesTable_source'] ? $_GET['instancesTable_source'] : false);
    $userCourses = $courseUser -> getUserCourses($constraints);
    $courseUser -> archiveUserCourses($userCourses);
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
    $alredy_attending = implode("','", array_keys($courseUser -> getCourses()));

    // Thus we can find the missing courses to fill the skill gap
    $courses_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_course_offers_skill ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID JOIN courses ON courses.id = module_hcd_course_offers_skill.courses_ID","module_hcd_course_offers_skill.courses_ID, courses.*, count(module_hcd_course_offers_skill.skill_ID) as skills_offered", "module_hcd_course_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_course_offers_skill.courses_ID NOT IN ('".$alredy_attending."')","","module_hcd_course_offers_skill.courses_ID ORDER BY skills_offered DESC");

    // And assign them
    foreach ($courses_proposed as $course) {
     $courseUser -> addCourses($course['courses_ID']);
    }
   }
   exit;
  }

 }

 if (isset($_GET['ajax']) && in_array($_GET['ajax'], array('lessonsTable', 'courseLessonsTable', 'instancesTable', 'coursesTable'))) {

  $roles = EfrontLessonUser :: getLessonsRoles(true);
  $smarty -> assign("T_ROLES_ARRAY", $roles);

  $rolesBasic = EfrontLessonUser :: getLessonsRoles();
  $smarty -> assign("T_BASIC_ROLES_ARRAY", $rolesBasic);

  $smarty -> assign("T_EDITED_USER_TYPE", $courseUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $courseUser -> user['user_type']);

  if (isset($_GET['ajax']) && $_GET['ajax'] == 'lessonsTable') {
   $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'completed', 'user_type', 'score', 'has_lesson'));
   if ($showUnassigned) {
    $lessons = $courseUser -> getUserStatusInIndependentLessons() + $courseUser -> getNonLessons(true);
   } else {
    $lessons = $courseUser -> getUserStatusInIndependentLessons();
   }
   $lessons = EfrontLesson :: convertLessonObjectsToArrays($lessons);
   $dataSource = $lessons;
  }
  if (isset($_GET['ajax']) && $_GET['ajax'] == 'courseLessonsTable' && eF_checkParameter($_GET['courseLessonsTable_source'], 'id')) {
   $lessons = $courseUser -> getUserStatusInCourseLessons(new EfrontCourse($_GET['courseLessonsTable_source']));
   $lessons = EfrontLesson :: convertLessonObjectsToArrays($lessons);
   $dataSource = $lessons;
  }

  if ($_GET['ajax'] == 'coursesTable' || $_GET['ajax'] == 'instancesTable') {
   $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'location', 'user_type', 'num_lessons', 'status', 'completed', 'score', 'has_course'));
   if (isset($_GET['ajax']) && $_GET['ajax'] == 'coursesTable') {
    $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true, 'instance' => false);
    if ($showUnassigned) {
     $courses = $courseUser -> getUserCoursesAggregatingResultsIncludingUnassigned($constraints);
     $totalEntries = $courseUser -> countUserCoursesAggregatingResultsIncludingUnassigned($constraints);
    } else {
     $courses = $courseUser -> getUserCoursesAggregatingResults($constraints);
     $totalEntries = $courseUser -> countUserCoursesAggregatingResults($constraints);
    }
   }
   if (isset($_GET['ajax']) && $_GET['ajax'] == 'instancesTable' && eF_checkParameter($_GET['instancesTable_source'], 'id')) {
    $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true, 'instance' => $_GET['instancesTable_source']);
    if ($showUnassigned) {
     $courses = $courseUser -> getUserCoursesIncludingUnassigned($constraints);
     $totalEntries = $courseUser -> countUserCoursesIncludingUnassigned($constraints);
    } else {
     $courses = $courseUser -> getUserCourses($constraints);
     $totalEntries = $courseUser -> countUserCourses($constraints);
    }
   }
   $courses = EfrontCourse :: convertCourseObjectsToArrays($courses);
   $dataSource = $courses;
   $smarty -> assign("T_SHOW_COURSE_LESSONS", true);
   $smarty -> assign("T_TABLE_SIZE", $totalEntries);
  }

  $tableName = $_GET['ajax'];
  $alreadySorted = true;
  include("sorted_table.php");
 }

} catch (Exception $e) {
 handleAjaxExceptions($e);
}

?>
