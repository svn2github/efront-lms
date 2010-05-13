<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}
$loadScripts[] = 'includes/lessons_list';
$loadScripts[] = 'includes/catalog';
try {
 if (isset($_GET['op']) && $_GET['op'] == 'tests') {
  require_once("tests/show_skill_gap_tests.php");

 } elseif (isset($_GET['export']) && $_GET['export'] == 'rtf') {
  require_once("rtf_export.php");

 } elseif (isset($_GET['course'])) {
  //@todo: You don't have to retrieve all courses just to check one
  $userCourses = $currentUser -> getCourses();
  if ($roles[$userCourses[$_GET['course']]] != 'professor' || !in_array($_GET['course'], array_keys($userCourses))) {
   throw new Exception(_UNAUTHORIZEDACCESS);
  }

  $currentCourse = new EfrontCourse($_GET['course']);
  $baseUrl = 'ctg=lessons&course='.$currentCourse -> course['id'];
  $smarty -> assign("T_BASE_URL", $baseUrl);
  $smarty -> assign("T_CURRENT_COURSE", $currentCourse);

  require_once 'course_settings.php';
 } elseif (isset($_GET['op']) && $_GET['op'] == 'search') {
  require_once "module_search.php";

 } elseif (isset($_GET['catalog'])) {
  require_once "catalog_page.php";

 } else {

  $directionsTree = new EfrontDirectionsTree();

  $options = array('noprojects' => 1, 'notests' => 1);
  $userLessons = $currentUser -> getUserStatusInLessons(false, true);

  /*

		 $userLessonProgress = EfrontStats :: getUsersLessonStatus($userLessons, $currentUser -> user['login'], $options);

		 $userLessons        = array_intersect_key($userLessons, $userLessonProgress); //Needed because EfrontStats :: getUsersLessonStatus might remove automatically lessons, based on time constraints

		 */


  $userCourses = $currentUser -> getUserCourses();
  foreach ($userCourses as $key => $course) {
   //this must be here (before $userCourses assignment) in order to revoke a certificate if it is expired and/or re-assign a course to a student if needed
   if ($course -> course['start_date'] && $course -> course['start_date'] > time()) {
    $value['remaining'] = null;
   } elseif ($course -> course['end_date'] && $course -> course['end_date'] < time()) {
    $value['remaining'] = 0;
   } else if ($course -> options['duration'] && $course -> course['active_in_course']) {
    if ($course -> course['active_in_course'] < $course -> course['start_date']) {
     $course -> course['active_in_course'] = $course -> course['start_date'];
    }
    $course -> course['remaining'] = $course -> course['active_in_course'] + $course -> options['duration']*3600*24 - time();
    if ($course -> course['end_date'] && $course -> course['end_date'] < $course -> course['active_in_course'] + $course -> options['duration']*3600*24) {
     $course -> course['remaining'] = $course -> course['end_date'] - time();
    }
   } else {
    $course -> course['remaining'] = null;
   }
   //Check whether the course registration is expired. If so, set $value['active_in_course'] to false, so that the effect is to appear disabled
   if ($course -> course['duration'] && $course -> course['active_in_course'] && $course -> course['duration'] * 3600 * 24 + $course -> course['active_in_course'] < time()) {
    $course -> archiveCourseUsers($course -> course['users_LOGIN']);
   }
   if ($course -> course['user_type'] != $currentUser -> user['user_type']) {
    $course -> course['different_role'] = 1;
   }
   $userCourses[$key] = $course;
  }
  //$userCourses        = $currentUser -> getCourses(true, false, $options);
  //$userCourseProgress = EfrontStats :: getUsersCourseStatus($userCourses, $currentUser -> user['login'], $options);
  //$userCourses        = array_intersect_key($userCourses, $userCourseProgress); //Needed because EfrontStats :: getUsersCourseStatus might remove automatically courses, based on time constraints
  //debug(false);exit;
  /*

		 $temp = array();

		 foreach ($userLessonProgress as $lessonId => $user) {

		 $temp[$lessonId] = $user[$currentUser -> user['login']];

		 }

		 $userProgress['lessons'] = $temp;



		 $temp = array();

		 foreach ($userCourseProgress as $courseId => $user) {

		 $temp[$courseId] = $user[$currentUser -> user['login']];

		 }

		 $userProgress['courses'] = $temp;

		 */
  $options = array('lessons_link' => '#user_type#.php?lessons_ID=',
                              'courses_link' => false,
                  'catalog' => false);
  if (sizeof ($userLessons) > 0 || sizeof($userCourses) > 0) {
   $smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, $userLessons, $userCourses, $userProgress, $options));
  }
 }
} catch (Exception $e) {
 handleNormalFlowExceptions($e);
}
?>
