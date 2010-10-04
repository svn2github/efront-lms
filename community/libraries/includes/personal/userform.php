<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}







$directionsTree = new EfrontDirectionsTree();
$directionsArray = $directionsTree -> getFlatTree();
$smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toPathString());

$constraints = array('archive' => false);
$userCourses = $editedUser -> getUserCourses($constraints);
foreach ($userCourses as $key => $course) {
 $courseLessons[$key] = $course -> getCourseLessons();
 $userCourses[$key] = $course -> course; //strip object, we don't need it
 $coursesScores[] = $course -> course['score'];
}
$smarty -> assign("T_USER_COURSES", $userCourses);

//pr(($courseLessons));exit;
$userLessons = $editedUser -> getUserStatusInLessons();
$result = EfrontStats :: getStudentsDoneTests($userLessons, $editedUser -> user['login']);
foreach ($result[$editedUser -> user['login']] as $value) {
 $userDoneTests[$value['lessons_ID']][] = $value;
}
$smarty -> assign("T_USER_TESTS", $userDoneTests);
foreach ($userLessons as $key => $lesson) {
 if ($lesson -> lesson['course_only']) {
  foreach($courseLessons as $courseId => $foo) {
   if (isset($courseLessons[$courseId][$key])) {
    $courseLessons[$courseId][$key] = $lesson -> lesson;
   }
  }
  unset($userLessons[$key]); //Remove course lesson from lessons list
 } else {
  $lessonsScores[] = $lesson -> lesson['score'];
  $userLessons[$key] = $lesson -> lesson; //strip object, we don't need it
 }
}
$smarty -> assign("T_USER_LESSONS", $userLessons);
$smarty -> assign("T_COURSE_LESSONS", $courseLessons);

if (sizeof($userCourses) > 0) {
 $averages['courses'] = formatScore(round(array_sum($coursesScores) / sizeof($coursesScores), 2));
}
if (sizeof($userLessons) > 0) {
 $averages['lessons'] = formatScore(round(array_sum($lessonsScores) / sizeof($lessonsScores), 2));
}
$smarty -> assign("T_AVERAGES", $averages);

//pr($userDoneTests);


/*
$tests = eF_getTableData("done_tests JOIN tests ON done_tests.tests_ID = tests.id JOIN content ON tests.content_ID = content.id JOIN lessons ON lessons.id = content.lessons_ID","content.name as name, done_tests.timestamp, done_tests.score, done_tests.comments, lessons.id as lesson_id, tests.content_ID","users_login = '".$_GET['edit_user']."'", "content.name");
foreach($tests as $test) {
	$test_id = $test['content_ID'];
	$user_tests[$test_id] = $test;
}
*/


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
  if (!$lessonObject -> lesson['course_only']) {
   !isset($userDoneTests[$editedUser -> user['login']]) ? $userDoneTests[$editedUser -> user['login']] = array() : null;
   $id = $lessonObject -> lesson['id'];
   $lessons[$id] = array();

   // Get info for every lesson of the course
   $lessons[$id]['name'] = $lessonObject -> lesson['name'];
   $lessons[$id]['active'] = $lessonObject -> lesson['active'];
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

 }
/*
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
*/

$smarty -> assign("T_EMPLOYEE_FORM_CAPTION", _USERFORM.":&nbsp;" . formatLogin($editedUser -> user['login']));

if (isset($_GET['pdf'])) {
 $editedEmployee -> printToPdf($editedUser, $evaluations, $skill_categories, $courses, $lessons, $all_average, $logo_fn);
 exit(0);
}
