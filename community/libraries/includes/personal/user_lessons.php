<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if (isset($currentUser->coreAccess['users']) && $currentUser->coreAccess['users'] != 'change') {
 $_change_lessons_ = false;
} else if ($currentUser -> user['user_type'] == 'administrator') {
 $_change_lessons_ = true;
} else if ($currentUser -> user['login'] == $editedUser -> user['login']) {
 $_change_lessons_ = false;






} else {
 $_change_lessons_ = false;
}
$smarty -> assign("_change_lessons_", $_change_lessons_);


try {
 if (isset($_GET['postAjaxRequest']) && $_change_lessons_) {
  if (isset($_GET['add_lesson'])) {
   if ($_GET['insert'] == "true") {
    $editedUser -> addLessons($_GET['add_lesson'], $_GET['user_type'], 1);
   } else if ($_GET['insert'] == "false") {
    $editedUser -> archiveUserLessons($_GET['add_lesson']);
   } else if (isset($_GET['addAll'])) {
    $userNonLessons = $editedUser -> getNonLessons(true);

    $lessons = array();
    foreach ($userNonLessons as $key => $lesson) {
     if (!$lesson -> lesson['course_only']) {
      $lessons[$lesson -> lesson['id']] = $lesson -> lesson;
     }
    }

    isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;
    $editedUser -> addLessons(array_keys($lessons), $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type'], 1);
   } else if (isset($_GET['removeAll'])) {
    $userLessons = $editedUser -> getLessons(true);
    $lessons = array();
    foreach ($userLessons as $key => $lesson) {
     if (!$lesson -> lesson['course_only']) {
      $lessons[$lesson -> lesson['id']] = $lesson -> lesson;
     }
    }
    isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;
    $editedUser -> archiveUserLessons(array_keys($lessons));
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
    $alredy_attending = implode("','", array_keys($editedUser -> getLessons()));

    // Thus we can find the missing courses to fill the skill gap
    $lessons_proposed = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID JOIN lessons ON lessons.id = module_hcd_lesson_offers_skill.lesson_ID","module_hcd_lesson_offers_skill.lesson_ID, lessons.*, count(module_hcd_lesson_offers_skill.skill_ID) as skills_offered", "module_hcd_lesson_offers_skill.skill_ID IN ('".$skills_missing."') AND module_hcd_lesson_offers_skill.lesson_ID NOT IN ('".$alredy_attending."')","","module_hcd_lesson_offers_skill.lesson_ID ORDER BY skills_offered DESC");

    // And assign them
    foreach ($lessons_proposed as $lesson) {
     $editedUser -> addLessons($lesson['lesson_ID']);
    }
   }
   exit;
  }

 }

 if (isset($_GET['ajax']) && $_GET['ajax'] == 'toggle_user' && $_GET['type'] == 'lesson' && $_change_lessons_) {
  $response = array('status' => 1);
  $editLesson = new EfrontLesson($_GET['id']);

  if ($editLesson -> isUserActiveInLesson($editedUser)) {
   $editLesson -> unConfirm($editedUser);
   $response['access'] = 0;
  } else {
   $editLesson -> confirm($editedUser);
   $response['access'] = 1;
  }
  echo json_encode($response);
  exit;
 } else if (isset($_GET['ajax']) && in_array($_GET['ajax'], array('lessonsTable'))) {

  $roles = EfrontLessonUser :: getLessonsRoles(true);
  $smarty -> assign("T_ROLES_ARRAY", $roles);

  $directionsTree = new EfrontDirectionsTree();
  $smarty -> assign("T_DIRECTION_PATHS", $directionsTree->toPathString());

  $rolesBasic = EfrontLessonUser :: getLessonsRoles();
  $smarty -> assign("T_BASIC_ROLES_ARRAY", $rolesBasic);

  $smarty -> assign("T_EDITED_USER_TYPE", $editedUser -> user['user_types_ID'] ? $editedUser -> user['user_types_ID'] : $editedUser -> user['user_type']);

  if (isset($_GET['ajax']) && $_GET['ajax'] == 'lessonsTable') {
   $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'directions_ID', 'completed','active_in_lesson', 'user_type', 'score', 'has_lesson'));
   if ($_change_lessons_) {
    $nonLessons = array();
    foreach ($editedUser -> getNonLessons(true) as $key => $lesson) {
     if (!$lesson -> lesson['course_only']) {
      $nonLessons[$key] = $lesson;
     }
    }

    $lessons = $editedUser -> getUserStatusInIndependentLessons(true) + $nonLessons;
   } else {
    $lessons = $editedUser -> getUserStatusInIndependentLessons(true);
   }

   $lessons = EfrontLesson :: convertLessonObjectsToArrays($lessons);
   foreach ($lessons as $key => $value) {
    if (!isset($value['completed'])) { //Populate missing fields in order for sorting to work correctly
     $lessons[$key]['completed'] = '';
     $lessons[$key]['score'] = '';
     $lessons[$key]['active_in_lesson'] = '';
     $lessons[$key]['has_lesson'] = 0;
     $lessons[$key]['user_type'] = '';
    }
    if (!$value['active']) {
     if (!$_change_lessons_) {
      unset($lessons[$key]);
     }





    }
   }
   $dataSource = $lessons;
  }

  $tableName = $_GET['ajax'];
  include("sorted_table.php");
 }

} catch (Exception $e) {
 handleAjaxExceptions($e);
}
