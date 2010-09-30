<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}
$tests = eF_getTableData("done_tests JOIN tests ON done_tests.tests_ID = tests.id JOIN content ON tests.content_ID = content.id JOIN lessons ON lessons.id = content.lessons_ID","content.name as name, done_tests.timestamp, done_tests.score, done_tests.comments, lessons.id as lesson_id, tests.content_ID","users_login = '".$_GET['edit_user']."'", "content.name");
foreach($tests as $test) {
 $test_id = $test['content_ID'];
 $user_tests[$test_id] = $test;
}
if ($ctg != "personal" && $editedUser -> getType() == "student") {
 $directionsTree = new EfrontDirectionsTree();
 $directionsArray = $directionsTree -> getFlatTree();
 $user_lessons = $editedUser -> getLessons(true);
 $constraints = array('archive' => false);
 $user_courses = $editedUser -> getUserCourses($constraints);
 $userDoneTests = EfrontStats :: getStudentsDoneTests($user_lessons, $editedUser -> user['login']);
 $all_average = array("courses" => array("title" => _COURSESAVERAGE, "sum" => 0, "count" => 0),
                                     "lessons" => array("title" => _LESSONSAVERAGE, "sum" => 0, "count" => 0),
                                     "tests" => array("title" => _TESTSAVERAGE, "sum" => 0, "count" => 0));
 $courses = array();
 // COURSES
 foreach ($user_courses as $courseObject) {
  !isset($userDoneTests[$editedUser -> user['login']]) ? $userDoneTests[$editedUser -> user['login']] = array() : null;
  $id = $courseObject-> course['id'];
  $courses[$id]['name'] = $courseObject-> course['name'];
  $courses[$id]['category']= $directionsArray[$courseObject-> course['directions_ID']]['name'];
  $courses[$id]['active'] = $courseObject-> course['active'];
  $courses[$id]['lessons'] = array();
  $courses[$id]['completed'] = $courseObject-> course['completed'];
  $courses[$id]['active_in_course'] = $courseObject-> course['active_in_course'];
  if ($courses[$id]['completed']) {
   $courses[$id]['score'] = $courseObject-> course['score'];
   $courses[$id]['to_timestamp'] = $courseObject-> course['to_timestamp'];
   $all_average['courses']['sum'] += $courses[$id]['score'];
   $all_average['courses']['count']++;
   //                    $courses[$id]['comments'] = $courseObject-> userStatus['comments'];
  }
  // Get info for every lesson of the course
  foreach($courseObject -> getCourseLessons() as $lesson_id => $lesson_info) {
   $courses[$id]['lessons'][$lesson_id] = array();
   //                        $user_lessons[$lesson_id] -> userStatus =  EfrontStats :: getUsersLessonStatus($lesson_id, $editedUser -> user['login']);
   $courses[$id]['lessons'][$lesson_id]['name'] = $user_lessons[$lesson_id] -> lesson['name'];
   $courses[$id]['lessons'][$lesson_id]['from_timestamp'] = $user_lessons[$lesson_id] -> userStatus['from_timestamp'];
   if ($user_lessons[$lesson_id] -> userStatus['completed']) {
    $courses[$id]['lessons'][$lesson_id]['completed'] = $user_lessons[$lesson_id] -> userStatus['completed'];
    $courses[$id]['lessons'][$lesson_id]['to_timestamp'] = $user_lessons[$lesson_id] -> userStatus['to_timestamp'];
    $courses[$id]['lessons'][$lesson_id]['score'] = ceil($user_lessons[$lesson_id] -> userStatus['score']);
    $all_average['lessons']['sum'] += $courses[$id]['lessons'][$lesson_id]['score'];
    $all_average['lessons']['count']++;
   }
   // Course
   $lesson_done_tests = sizeof($userDoneTests[$editedUser -> user['login']]);
   if ($lesson_done_tests) {
    $lesson_done_tests = 0;
    $courses[$id]['lessons'][$lesson_id]['tests'] = array();
    $test_sum = 0;
    foreach ($userDoneTests[$editedUser -> user['login']] as $test_id => $test_info) {
     if ($test_info[lessons_ID] == $lesson_id) {
      $courses[$id]['lessons'][$lesson_id]['tests'][$test_id]['score'] = formatScore($test_info['score']);
      $courses[$id]['lessons'][$lesson_id]['tests'][$test_id]['name'] = $test_info['name'];
      $courses[$id]['lessons'][$lesson_id]['tests'][$test_id]['timestamp'] = $test_info['timestamp'];
      $courses[$id]['lessons'][$lesson_id]['tests'][$test_id]['comments'] = $test_info['comments'];
      $test_sum += $test_info['score'];
      $lesson_done_tests++;
     }
    }
    $all_average['tests']['sum'] += ($test_sum);
    $all_average['tests']['count'] += $lesson_done_tests;
    $courses[$id]['lessons'][$lesson_id]['tests_average'] = ceil($test_sum / $lesson_done_tests);
    $courses[$id]['lessons'][$lesson_id]['tests_count'] = $lesson_done_tests;
   } else {
    $lesson_done_tests = 0;
   }
   // Remove the lesson from the lessons list, so that it does not appear again
   //unset($user_lessons[$lesson_id]);
  }
 }
 $lessons = array();
 //pr($userDoneTests);
 foreach ($user_lessons as $lessonObject) {
  !isset($userDoneTests[$editedUser -> user['login']]) ? $userDoneTests[$editedUser -> user['login']] = array() : null;
  $id = $lessonObject -> lesson['id'];
  $lessons[$id] = array();
  // Get info for every lesson of the course
  $lessons[$id]['name'] = $lessonObject -> lesson['name'];
  $lessons[$id]['category']= $directionsArray[$lessonObject-> lesson['directions_ID']]['name'];
  //                    $lessonObject -> userStatus = EfrontStats :: getUsersLessonStatus($id, $editedUser -> user['login']);
  //pr($lessonObject);
  //echo "*".$lessonObject -> userStatus['completed']."*";
  $lessons[$id]['from_timestamp'] = $lessonObject -> userStatus['from_timestamp'];
  if ($lessonObject -> userStatus['completed']) {
   $lessons[$id]['completed'] = $lessonObject -> userStatus['completed'];
   $lessons[$id]['to_timestamp'] = $lessonObject -> userStatus['to_timestamp'];
   $lessons[$id]['score'] = ceil($lessonObject -> userStatus['score']);
   $all_average['lessons']['sum'] += $lessons[$id]['score'];
   $all_average['lessons']['count']++;
  }
  $lesson_done_tests = sizeof($userDoneTests[$editedUser -> user['login']]);
  if ($lesson_done_tests) {
   $lesson_done_tests = 0;
   $lessons[$id]['tests'] = array();
   $test_sum = 0;
   foreach ($userDoneTests[$editedUser -> user['login']] as $test_id => $test_info) {
    if ($test_info[lessons_ID] == $id) {
     $lessons[$id]['tests'][$test_id]['score'] = formatScore($test_info['score']);
     $lessons[$id]['tests'][$test_id]['name'] = $test_info['name'];
     $lessons[$id]['tests'][$test_id]['timestamp'] = $test_info['timestamp'];
     $lessons[$id]['tests'][$test_id]['comments'] = $test_info['comments'];
     $test_sum += $test_info['score'];
     $lesson_done_tests++;
    }
   }
   $all_average['tests']['sum'] += formatScore($test_sum);
   $all_average['tests']['count'] += $lesson_done_tests;
   $lessons[$id]['tests_average'] = formatScore($test_sum / $lesson_done_tests);
   $lessons[$id]['tests_count'] = $lesson_done_tests;
  }
 }
 if (isset($_COOKIE['form_cookie'])) {
  switch($_COOKIE['form_cookie']) {
   case 'name':
    $courses = eF_multisort($courses, 'name');
    $lessons = eF_multisort($lessons, 'name');
    break;
   case 'completion_date':
    $courses = eF_multisort($courses, 'to_timestamp', 'desc');
    $lessons = eF_multisort($lessons, 'to_timestamp', 'desc');
    break;
   case 'category':
    $courses = eF_multisort($courses, 'category');
    $lessons = eF_multisort($lessons, 'category');
    break;
   case 'score':
    $courses = eF_multisort($courses, 'score');
    $lessons = eF_multisort($lessons, 'score');
    break;
   default: break;
  }
 }
 $smarty -> assign("T_COURSES",$courses);
 $smarty -> assign("T_LESSONS",$lessons);
 foreach ($all_average as $kind => $avg) {
  if ($all_average[$kind]['count']) {
   $all_average[$kind]['avg'] = formatScore(ceil($all_average[$kind]['sum'] / $all_average[$kind]['count']));
  } else {
   unset($all_average[$kind]);
  }
 }
 $smarty -> assign("T_AVERAGES",$all_average);
} else {
 $smarty -> assign("T_NOTRAINING", 1);
}
$smarty -> assign("T_EMPLOYEE_FORM_CAPTION", _USERFORM.":&nbsp;" . $editedUser -> user['name'] . "&nbsp;" . $editedUser -> user['surname']);
if (isset($_GET['print_preview'])) {
 $employee_form_options = array( //Create calendar options and assign them to smarty, to be displayed at the calendar inner table
 array('text' => _PRINTEMPLOYEEFORM, 'image' => "16x16/printer.png", 'href' => $_SESSION['s_type'].".php?ctg=users&edit_user=".$editedUser->login."&op=status&print=1&popup=1", "onClick" => "eF_js_showDivPopup('"._PRINTEMPLOYEEFORM."', 2)", "target" => "POPUP_FRAME"));
} else {
 $employee_form_options = array( //Create calendar options and assign them to smarty, to be displayed at the calendar inner table
 array('text' => _PRINTPREVIEW, 'image' => "16x16/search.png", 'href' => $_SESSION['s_type'].".php?ctg=users&edit_user=".$editedUser->login."&op=status&print_preview=1&popup=1", "onClick" => "eF_js_showDivPopup('"._EMPLOYEEFORMPRINTPREVIEW."', 2)", "target" => "POPUP_FRAME"),
 array('text' => _PRINTEMPLOYEEFORM, 'image' => "16x16/printer.png", 'href' => $_SESSION['s_type'].".php?ctg=users&edit_user=".$editedUser->login."&op=status&print=1&popup=1", "onClick" => "eF_js_showDivPopup('"._PRINTEMPLOYEEFORM."', 2)", "target" => "POPUP_FRAME"));
}
//$smarty -> assign("T_EMPLOYEE_FORM_OPTIONS", $employee_form_options);
