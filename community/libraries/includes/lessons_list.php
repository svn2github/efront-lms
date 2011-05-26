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

 } elseif (isset($_GET['export']) && $_GET['export'] == 'xml') {
  require_once("xml_export.php");

 } elseif (isset($_GET['course'])) {
  $currentCourse = new EfrontCourse($_GET['course']);
  $result = eF_getTableData("users_to_courses", "user_type", "users_LOGIN='".$currentUser -> user['login']."' and courses_ID=".$currentCourse -> course['id']);
  if (empty($result) || $roles[$result[0]['user_type']] != 'professor') {
   throw new Exception(_UNAUTHORIZEDACCESS);
  }

  $baseUrl = 'ctg=lessons&course='.$currentCourse -> course['id'];
  $smarty -> assign("T_BASE_URL", $baseUrl);
  $smarty -> assign("T_CURRENT_COURSE", $currentCourse);

  require_once 'course_settings.php';
 } elseif (isset($_GET['op']) && $_GET['op'] == 'search') {
  require_once "module_search.php";

 } elseif (isset($_GET['catalog'])) {
  require_once "catalog_page.php";

 } else {
  $myCoursesOptions = array();

  $directionsTree = new EfrontDirectionsTree();

  $options = array('noprojects' => 1, 'notests' => 1);
  $userLessons = $currentUser -> getUserStatusInLessons(false, true);
  foreach ($userLessons as $key => $lesson) {
   if (!$lesson -> lesson['active']) {
    unset($userLessons[$key]);
   }
  }

  /*
		 $userLessonProgress = EfrontStats :: getUsersLessonStatus($userLessons, $currentUser -> user['login'], $options);
		 $userLessons        = array_intersect_key($userLessons, $userLessonProgress); //Needed because EfrontStats :: getUsersLessonStatus might remove automatically lessons, based on time constraints
		 */
  if ($currentUser -> coreAccess['dashboard'] != 'hidden') {
   $myCoursesOptions[] = array('text' => _MYACCOUNT, 'image' => "32x32/user.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=personal");
  }
  $constraints = array('archive' => false, 'active' => true, 'sort' => 'name');
  $userCourses = $currentUser -> getUserCourses($constraints);
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
   if ((!$currentUser -> user['user_types_ID'] && $course -> course['user_type'] != $currentUser -> user['user_type']) || ($currentUser -> user['user_types_ID'] && $course -> course['user_type'] != $currentUser -> user['user_types_ID'])) {
    $course -> course['different_role'] = 1;
   }
   $userCourses[$key] = $course;
  }
  $options = array('lessons_link' => '#user_type#.php?lessons_ID=',
                              'courses_link' => $roles[$course -> course['user_type']] == 'professor' ? true : false,
                  'catalog' => false,
         'only_progress_link' => true,
         'collapse' => $GLOBALS['configuration']['collapse_catalog']);
  if (sizeof ($userLessons) > 0 || sizeof($userCourses) > 0) {
   $smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, $userLessons, $userCourses, $userProgress, $options));
  }
  $innertable_modules = array();
  foreach ($loadedModules as $module) {
   unset($InnertableHTML);
     $centerLinkInfo = $module -> getCenterLinkInfo();
    $InnertableHTML = $module -> getCatalogModule();
    $InnertableHTML ? $module_smarty_file = $module -> getCatalogSmartyTpl() : $module_smarty_file = false;
   // If the module has a lesson innertable
   if ($InnertableHTML) {
    // Get module html - two ways: pure HTML or PHP+smarty
    // If no smarty file is defined then false will be returned
    if ($module_smarty_file) {
     // Execute the php code -> The code has already been executed by above (**HERE**)
     // Let smarty know to include the module smarty file
     $innertable_modules[$module->className] = array('smarty_file' => $module_smarty_file);
    } else {
     // Present the pure HTML cod
     $innertable_modules[$module->className] = array('html_code' => $InnertableHTML);
    }
   }
  }
  if (!empty($innertable_modules)) {
   $smarty -> assign("T_INNERTABLE_MODULES", $innertable_modules);
  }
  if ($GLOBALS['configuration']['insert_group_key']) {
   $myCoursesOptions[] = array('text' => _ENTERGROUPKEY, 'image' => "32x32/key.png", 'href' => "javascript:void(0)", 'onclick' => "eF_js_showDivPopup('"._ENTERGROUPKEY."', 0, 'group_key_enter')");
  }
  if ($GLOBALS['configuration']['lessons_directory']) {
   $myCoursesOptions[] = array('text' => _COURSECATALOG, 'image' => "32x32/catalog.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=lessons&catalog=1");
  }
  if ((!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') && $GLOBALS['configuration']['disable_messages'] != 1) {
   $myCoursesOptions[] = array('text' => _MESSAGES, 'image' => "32x32/mail.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=messages");
  }
  if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
   $myCoursesOptions[] = array('text' => _STATISTICS, 'image' => "32x32/reports.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=statistics");
  }
  if ((!isset($currentUser -> coreAccess['forum']) || $currentUser -> coreAccess['forum'] != 'hidden') && $GLOBALS['configuration']['disable_forum'] != 1) {
   $myCoursesOptions[] = array('text' => _FORUM, 'image' => "32x32/forum.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=forum");
  }
  if ($GLOBALS['configuration']['disable_calendar'] != 1 && (!isset($currentUser -> coreAccess['calendar']) || $currentUser -> coreAccess['calendar'] != 'hidden')) {
         $myCoursesOptions[] = array('text' => _CALENDAR, 'image' => "32x32/calendar.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=calendar");
  }
  $smarty -> assign("T_LAYOUT_CLASS", $currentTheme -> options['toolbar_position'] == "left" ? "hideRight" : "hideLeft"); //Whether to show the sidemenu on the left or on the right
  $smarty -> assign("T_COURSES_LIST_OPTIONS", $myCoursesOptions);
 }
} catch (Exception $e) {
 handleNormalFlowExceptions($e);
}
