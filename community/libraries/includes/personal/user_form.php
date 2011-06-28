<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}
session_write_close();







if ($editedUser -> user['user_type'] != 'administrator') {
 $directionsTree = new EfrontDirectionsTree();
 $directionsArray = $directionsTree -> getFlatTree();
 $smarty -> assign("T_DIRECTIONS_TREE", $directionsPathStrings = $directionsTree -> toPathString());

 $studentRoles = EfrontLessonUser :: getRoles();
 foreach ($studentRoles as $key => $value) {
  if ($value != 'student') {
   unset($studentRoles[$key]);
  }
 }

 $constraints = array('archive' => false);
 if ($_COOKIE['setUserFormSelectedSort']) {
  preg_match("/\d_(\w+)--(\w+)/", $_COOKIE['setUserFormSelectedSort'], $matches);
  in_array($matches[1], array('name', 'directions_ID', 'active_in_course', 'completed', 'score')) ? $constraints['sort'] = $matches[1] : $constraints['sort'] = 'name';
  $matches[2] == 'desc' ? $constraints['order'] = 'asc' : $constraints['order'] = 'desc';
 }
 $userCourses = $editedUser -> getUserCourses($constraints);
 foreach ($userCourses as $key => $value) {
  if (!in_array($value -> course['user_type'], $studentRoles)) {
   unset($userCourses[$key]);
  }
 }

 $constraints = array('archive' => false, 'active' => true, 'return_objects' => false);
 foreach ($userCourses as $key => $course) {
  $courseLessons[$key] = $course -> getCourseLessons($constraints);
  $userCourses[$key] = $course -> course; //strip object, we don't need it
  if ($course -> course['completed']) {
   $coursesScores[] = $course -> course['score'];
  }
 }

 $smarty -> assign("T_USER_COURSES", $userCourses);

 $userLessons = $editedUser -> getUserStatusInLessons();
 foreach ($userLessons as $key => $value) {
  if (!in_array($value -> lesson['user_type'], $studentRoles)) {
   unset($userLessons[$key]);
  }
 }

 $result = EfrontStats :: getStudentsDoneTests($userLessons, $editedUser -> user['login']);
    $testNames = eF_getTableDataFlat("tests t, content c", "t.id, c.name", "c.id=t.content_ID and c.ctg_type='tests'");
    $testNames = array_combine($testNames['id'], $testNames['name']);
 foreach ($result[$editedUser -> user['login']] as $key => $value) {
  if (in_array($key, array_keys($testNames))) {
   $userDoneTests[$value['lessons_ID']][] = $value;
  }
 }

 $smarty -> assign("T_USER_TESTS", $userDoneTests);

 foreach ($userLessons as $key => $lesson) {
  if ($lesson -> lesson['course_only']) {
   foreach($courseLessons as $courseId => $foo) {
    if (isset($courseLessons[$courseId][$key])) {
     $courseLessons[$courseId][$key] = $lesson -> lesson;
    } elseif ($foo -> lesson) {
     $courseLessons[$courseId][$key] = $foo -> lesson;
    }
   }
   unset($userLessons[$key]); //Remove course lesson from lessons list
  } else {
   if ($lesson -> lesson['completed']) {
    $lessonsScores[] = $lesson -> lesson['score'];
   }
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
}
$smarty -> assign("T_EMPLOYEE_FORM_CAPTION", _USERFORM.": " . formatLogin($editedUser -> user['login']));




if (isset($_GET['pdf'])) {

 $pdf = new EfrontPdf(_EMPLOYEEFORM . ": " . formatLogin($editedUser -> user['login']));
 try {
  $avatarFile = new EfrontFile($infoUser -> user['avatar']);
 } catch(Exception $e) {
  $avatarFile = new EfrontFile(G_SYSTEMAVATARSPATH."unknown_small.png");
 }

 $info = array(array(_NAME, formatLogin($editedUser -> user['login'])),
      array(_BIRTHDAY, formatTimestamp($editedEmployee -> employee['birthday'])),
      array(_ADDRESS, $editedEmployee -> employee['address']),
      array(_CITY, $editedEmployee -> employee['city']),
      array(_HIREDON, formatTimestamp($editedEmployee -> employee['hired_on'])),
      array(_LEFTON, formatTimestamp($editedEmployee -> employee['left_on'])));
 $pdf -> printInformationSection(_GENERALUSERINFO, $info, $avatarFile);

 $info = array();
 foreach ($jobs as $value) {
  $info[] = array($value['name'], strip_tags($value['description']).(!$value['supervisor'] OR _SUPERVISOR));
 }
 $pdf -> printInformationSection(_PLACEMENTS, $info);

 $info = array();
 foreach ($evaluations as $value) {
  $info[] = array(formatLogin($value['author']).' '.formatTimestamp($value['timestamp']), strip_tags($value['specification']));
 }
 $pdf -> printInformationSection(_EVALUATIONS, $info);

 $info = array();
 foreach ($skills as $value) {
  $info[] = array($value['description'].' ', $value['specification'].' '.($value['score'] ? "({$value['score']}%)" : '')); //Append space, in order to always appear
 }
 $pdf -> printInformationSection(_SKILLS, $info);


 if ($editedUser -> user['user_type'] != 'administrator' && (!empty($userCourses) || !empty($userLessons))) {
  $formatting = array(_NAME => array('width' => '40%', 'fill' => false),
       _CATEGORY => array('width' => '25%','fill' => false),
       _REGISTRATIONDATE => array('width' => '13%','fill' => false),
       _COMPLETED => array('width' => '13%','fill' => false, 'align' => 'C'),
       _SCORE => array('width' => '9%','fill' => false, 'align' => 'R'));

  $data = array();
  foreach ($userCourses as $courseId => $value) {

   $data[$courseId] = array(_NAME => $value['name'],
         _CATEGORY => str_replace("&nbsp;&rarr;&nbsp;", " -> ", $directionsPathStrings[$value['directions_ID']]),
         _REGISTRATIONDATE => formatTimestamp($value['active_in_course']),
         _COMPLETED => $value['completed'] ? _YES.($value['to_timestamp'] ? ', '._ON.' '.formatTimestamp($value['to_timestamp']) : '') : '-',
         _SCORE => formatScore($value['score']).'%',
         'active' => $value['active']);


   if (isset($courseLessons[$value['id']]) && !empty($courseLessons[$value['id']])) {
    $subsectionFormatting = array(_NAME => array('width' => '78%', 'fill' => true),
             _COMPLETED => array('width' => '13%', 'fill' => true, 'align' => 'C'),
             _SCORE => array('width' => '9%', 'fill' => true, 'align' => 'R'));
    $subSectionData = array();
    foreach ($courseLessons[$value['id']] as $lessonId => $courseLesson) {
     $subSectionData[$lessonId] = array(_NAME => $courseLesson['name'],
                _COMPLETED => $courseLesson['completed'] ? _YES.($courseLesson['timestamp_completed'] ? ', '._ON.' '.formatTimestamp($courseLesson['timestamp_completed']): '') : '-',
                _SCORE => formatScore($courseLesson['score']).'%');
/*
					if (isset($userDoneTests[$value['id']])) {
						$testSubsectionFormatting = array(_TESTNAME	=> array('width' => '78%', 'fill' => true),
														  _STATUS	=> array('width' => '13%', 'fill' => true, 'align' => 'C'),
														  _SCORE	=> array('width' => '9%',  'fill' => true, 'align' => 'R'));
						$testsSubSectionData = array();
						foreach ($userDoneTests[$value['id']] as $test) {
							$testsSubSectionData[] = array(_TESTNAME => $test['name'],
														   _STATUS   => $test['status'],
														   _SCORE 	 => formatScore($test['score']).'%');
						}
						$testSubSections[$lessonId] = array('data' => $testsSubSectionData, 'formatting' => $testSubsectionFormatting, 'title' => _TESTSFORLESSON.': '.$courseLesson['name']);
					}
*/
    }
    $subSections[$courseId] = array('data' => $subSectionData, 'formatting' => $subsectionFormatting, 'title' => _LESSONSFORCOURSE.': '.$value['name'], 'subSections' => $testSubSections);
   }
  }
  $pdf->printDataSection(_TRAINING.': '._COURSES, $data, $formatting, $subSections);

  $data = $subSections = array();
  foreach ($userLessons as $lessonId => $value) {
   $data[$lessonId] = array(_NAME => $value['name'],
        _CATEGORY => str_replace("&nbsp;&rarr;&nbsp;", " -> ", $directionsPathStrings[$value['directions_ID']]),
        _REGISTRATIONDATE => formatTimestamp($value['active_in_lesson']),
        _COMPLETED => $value['completed'] ? _YES.($value['timestamp_completed'] ? ', '._ON.' '.formatTimestamp($value['timestamp_completed']): '') : '-',
        _SCORE => formatScore($value['score']).'%');
/*
			if (isset($userDoneTests[$value['id']])) {
				$subsectionFormatting = array(_TESTNAME	=> array('width' => '78%', 'fill' => true),
											  _STATUS	=> array('width' => '13%', 'fill' => true, 'align' => 'C'),
											  _SCORE	=> array('width' => '9%',  'fill' => true, 'align' => 'R'));
				$subSectionData = array();
				foreach ($userDoneTests[$value['id']] as $test) {
					$subSectionData[] = array(_TESTNAME	=> $test['name'],
											  _STATUS   => $test['status'],
											  _SCORE 	=> formatScore($test['score']).'%');
				}
				$subSections[$lessonId] = array('data' => $subSectionData, 'formatting' => $subsectionFormatting, 'title' => _TESTSFORLESSON.': '.$value['name']);
			}
*/
  }
  $pdf->printDataSection(_TRAINING.': '._LESSONS, $data, $formatting, $subSections);


  $info = array();
  if (isset($averages['courses'])) {
   $info[] = array(_COURSESAVERAGE, $averages['courses'].'%');
  }
  if (isset($averages['lessons'])) {
   $info[] = array(_LESSONSAVERAGE, $averages['lessons'].'%');
  }
  $pdf -> printInformationSection(_OVERALL, $info);
 }

 $pdf -> OutputPdf('user_form_'.$editedUser -> user['login'].'.pdf');
 exit;

}
