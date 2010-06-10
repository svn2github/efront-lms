<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if ($currentUser -> user['user_type'] == 'administrator') {
 $validUsers = EfrontUser :: getUsers(true);
} else if ($_SESSION['s_lessons_ID']) {
 $statisticsLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
 $lessonUsers = $statisticsLesson -> getUsers();
 if ($lessonRoles[$lessonUsers[$currentUser -> user['login']]['role']] == 'professor') {
  $validUsers = $lessonUsers;
 } else if ($lessonRoles[$lessonUsers[$currentUser -> user['login']]['role']] == 'student') {
  $validUsers[$currentUser -> user['login']] = $currentUser;

  if (!$isSupervisor) {
   $smarty -> assign("T_SINGLE_USER", true); //assign this variable, so that select user panel is not available
   $_GET['sel_user'] = $currentUser -> user['login'];
  }
 } else {
  throw new EfrontUserException(_USERDOESNOTHAVETHISLESSON.": ".$statisticsLesson -> lesson['name'], EfrontUserException :: USER_NOT_HAVE_LESSON);
 }
} else { //if the system user is a simple student
 if ($_student_ && !$isSupervisor) {
  $smarty -> assign("T_SINGLE_USER", true);
  $_GET['sel_user'] = $currentUser -> user['login'];
  $validUsers = array($currentUser -> user['login'] => $currentUser -> user['login']);
 } else {
  $userLessons = $currentUser -> getLessons(true);
  $users = array();
  foreach ($userLessons as $lesson) {
   $users = $users + $lesson -> getUsers();
  }
  $validUsers = $users;
 }
}

if ($currentUser -> user['user_type'] != 'administrator' && $isSupervisor) {

 require_once $path."module_hcd_tools.php";
 $supervisor_at_branches = eF_getRights();

 $supervised_employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN LEFT OUTER JOIN module_hcd_employee_works_at_branch ON users.login = module_hcd_employee_works_at_branch.users_LOGIN","users.*","(users.user_type <> 'administrator' AND ((module_hcd_employee_works_at_branch.branch_ID IN (" . $_SESSION['supervises_branches'] ." ) AND module_hcd_employee_works_at_branch.assigned='1') OR EXISTS (select module_hcd_employees.users_login from module_hcd_employees LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.users_login = module_hcd_employees.users_login where users.login=module_hcd_employees.users_login AND module_hcd_employee_works_at_branch.branch_ID IS NULL))) AND active = 1 GROUP BY login", "login");
 foreach ($supervised_employees as $employee) {
  if (!isset($validUsers[$employee['login']])) {
   $validUsers[$employee['login']] = $employee;
  }
 }
}

if (isset($_GET['sel_user'])) {
 if (in_array($_GET['sel_user'], array_keys($validUsers))) {
  $infoUser = EfrontUserFactory :: factory($_GET['sel_user']);
 } else {
  throw new EfrontUserException(_USERISNOTVALIDORYOUCANNOTSEEUSER.": ".$_GET['sel_user'], EfrontUserException :: INVALID_LOGIN);
 }

 $directionsTree = new EfrontDirectionsTree();
 $directionsTreePaths = $directionsTree -> toPathString();


 $smarty -> assign("T_USER_LOGIN", $infoUser -> user['login']);
 $smarty -> assign("T_REPORTS_USER", $infoUser);
 if ($_GET['specific_lesson_info'] && $_GET['lesson']) {
  $lessons = $infoUser -> getUserStatusInLessons($_GET['lesson']);
  $smarty -> assign ("T_USER_STATUS_IN_LESSON", $lessons[$_GET['lesson']]);

  $status = EfrontStats :: getUsersLessonStatus($_GET['lesson'], $infoUser -> user['login']);
  $doneTests = EfrontStats :: getStudentsDoneTests($_GET['lesson'], $infoUser -> user['login']);
  foreach ($doneTests[$infoUser -> user['login']] as $test) {
   unset($pendingTests[$test['tests_ID']]); //remove done tests
  }
  $smarty -> assign("T_USER_PENDING_TESTS", $pendingTests);
  $smarty -> assign("T_USER_DONE_TESTS", $doneTests[$infoUser -> user['login']]);
  $smarty -> assign("T_USER_STATUS", $status[$_GET['lesson']][$infoUser -> user['login']]);
 } elseif ($_GET['specific_course_info'] && $_GET['course']) {
  $lessons = $infoUser -> getUserStatusInCourseLessons(new EfrontCourse($_GET['course']));
  $smarty -> assign ("T_USER_STATUS_IN_COURSE_LESSONS", $lessons);
 } else {


  try {
   $roles = EfrontLessonUser :: getLessonsRoles(true);
   $smarty -> assign("T_ROLES_ARRAY", $roles);

   $rolesBasic = EfrontLessonUser :: getLessonsRoles();
   $smarty -> assign("T_BASIC_ROLES_ARRAY", $rolesBasic);

   if (isset($_GET['ajax']) && $_GET['ajax'] == 'lessonsTable') {
    $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'location', 'user_type', 'num_lessons', 'status', 'completed', 'score', 'operations', 'sort_by_column' => 4));
    $smarty -> assign("T_DATASOURCE_OPERATIONS", array('progress'));
    $lessons = $infoUser -> getUserStatusInIndependentLessons();
    $lessons = EfrontLesson :: convertLessonObjectsToArrays($lessons);
    $dataSource = $lessons;
   }
   if (isset($_GET['ajax']) && $_GET['ajax'] == 'courseLessonsTable' && eF_checkParameter($_GET['courseLessonsTable_source'], 'id')) {
    $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'location', 'user_type', 'num_lessons', 'status', 'completed', 'score', 'operations', 'sort_by_column' => 4));
    $smarty -> assign("T_DATASOURCE_OPERATIONS", array('progress'));
    $lessons = $infoUser -> getUserStatusInCourseLessons(new EfrontCourse($_GET['courseLessonsTable_source']));
    $lessons = EfrontLesson :: convertLessonObjectsToArrays($lessons);
    $dataSource = $lessons;
   }
   $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'location', 'user_type', 'num_lessons', 'status', 'completed', 'score', 'operations', 'sort_by_column' => 4));
   $smarty -> assign("T_DATASOURCE_OPERATIONS", array('progress'));
   if ($_GET['ajax'] == 'coursesTable' || $_GET['ajax'] == 'instancesTable') {
    if (isset($_GET['ajax']) && $_GET['ajax'] == 'coursesTable') {
     $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true, 'instance' => false);
     $constraints['required_fields'] = array('has_instances', 'location', 'user_type', 'completed', 'score', 'has_course', 'num_lessons');
     $constraints['return_objects'] = false;
     $courses = $infoUser -> getUserCoursesAggregatingResults($constraints);
    }

    if (isset($_GET['ajax']) && $_GET['ajax'] == 'instancesTable' && eF_checkParameter($_GET['instancesTable_source'], 'id')) {
     $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true, 'instance' => $_GET['instancesTable_source']);
     $constraints['required_fields'] = array('num_lessons', 'location');
     $constraints['return_objects'] = false;
     $courses = $infoUser -> getUserCourses($constraints);
    }

    $dataSource = $courses;
    $smarty -> assign("T_SHOW_COURSE_LESSONS", true);
   }

   $tableName = $_GET['ajax'];
   include("sorted_table.php");

  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }

  try {
   $userInfo = array();
   $userInfo['general'] = $infoUser -> getInformation();
   $userInfo['communication'] = EfrontStats :: getUserCommunicationInfo($infoUser);

   if ($GLOBALS['configuration']['chat_enabled']) {
    if (sizeof($userInfo['communication']['chat_messages'])) {
     $last = current($userInfo['communication']['chat_messages']);
     $userInfo['communication']['chat_last_message'] = formatTimestamp($last['timestamp'], 'time');
    } else {
     $userInfo['communication']['chat_last_message'] = "";
    }
   }
   if (sizeof($userInfo['communication']['forum_messages'])) {
    $last = current($userInfo['communication']['forum_messages']);
    $userInfo['communication']['forum_last_message'] = formatTimestamp($last['timestamp'], 'time');
   } else {
    $userInfo['communication']['forum_last_message'] = "";
   }

   $userInfo['usage'] = EfrontStats :: getUserUsageInfo($infoUser);

   try {
    $avatar = new EfrontFile($userInfo['general']['avatar']);
    $avatar['id'] != -1 ? $smarty -> assign ("T_AVATAR", $avatar['id']) : $smarty -> assign ("T_AVATAR", $avatar['path']);
   } catch (Exception $e) {
    $smarty -> assign ("T_AVATAR", G_SYSTEMAVATARSPATH."unknown_small.png");
   }
   $smarty -> assign("T_USER_INFO", $userInfo);
   if ($infoUser -> user['user_type'] != 'administrator') {
    $userLessons = $infoUser -> getUserLessons();
   } else {
    $userLessons = array();
   }
   //$allUserTimes = EfrontStats :: getUsersTimeAll(false, false, array_keys($userLessons));
   $actions = array('login' => _LOGIN,
        'logout' => _LOGOUT,
        'lesson' => _ACCESSEDLESSON,
        'content' => _ACCESSEDCONTENT,
        'tests' => _ACCESSEDTEST,
        'test_begin' => _BEGUNTEST,
        'lastmove' => _NAVIGATEDSYSTEM);
   $smarty -> assign("T_ACTIONS", $actions);
   if (isset($_GET['from_year'])) { //the admin has chosen a period
    $from = mktime($_GET['from_hour'], $_GET['from_min'], 0, $_GET['from_month'], $_GET['from_day'], $_GET['from_year']);
    $to = mktime($_GET['to_hour'], $_GET['to_min'], 0, $_GET['to_month'], $_GET['to_day'], $_GET['to_year']);
   } else {
    $from = mktime(date("H"), date("i"), 0, date("m"), date("d") - 7, date("Y"));
    $to = mktime(date("H"), date("i"), 0, date("m"), date("d"), date("Y"));
   }
   // Predefined periods
   $periods = array();
   $today = time();
   $week_back = getdate($today - 7*24*3600);
   $week_back = $week_back["mon"] . "," . $week_back["mday"] . "," . $week_back["year"];
   $month_back = mktime(date("H"), date("i"), 0, date("m")-1, date("d"), date("Y"));
   $month_back = getdate($month_back);
   $month_back = $month_back["mon"] . "," . $month_back["mday"] . "," . $month_back["year"];
   $day_back = getdate($today - 24*3600);
   $day_back = $day_back["mon"] . "," . $day_back["mday"] . "," . $day_back["year"];
   $two_days_back = getdate($today - 48*3600);
   $two_days_back = $two_days_back["mon"] . "," . $two_days_back["mday"] . "," . $two_days_back["year"];
   $today = getdate(time());
   $today = $today["month"] . "," . $today["mday"] . "," . $today["year"];
   $periods[] = array("name" => _PREVIOUSWEEK, "value" => $week_back . "|" . $today);
   $periods[] = array("name" => _TODAY, "value" => $day_back . "|" . $today);
   $periods[] = array("name" => _YESTERDAY, "value" => $two_days_back . "|" . $day_back);
   $periods[] = array("name" => _PREVIOUSMONTH, "value" => $month_back . "|" . $today);
   $smarty -> assign('T_PREDEFINED_PERIODS', $periods);
   if (isset($_GET['showlog']) && $_GET['showlog'] == "true") {
    $lessonNames = eF_getTableDataFlat("lessons", "id, name");
    $lessonNames = array_combine($lessonNames['id'], $lessonNames['name']);
    $contentNames = eF_getTableDataFlat("content", "id, name");
    $contentNames = array_combine($contentNames['id'], $contentNames['name']);
    $testNames = eF_getTableDataFlat("tests t, content c", "t.id, c.name", "c.id=t.content_ID");
    $testNames = array_combine($testNames['id'], $testNames['name']);
    $result = eF_getTableData("logs", "*", "timestamp between $from and $to and users_LOGIN='".$infoUser -> user['login']."' order by timestamp desc");
    foreach ($result as $key => $value) {
     $value['lessons_ID'] ? $result[$key]['lesson_name'] = $lessonNames[$value['lessons_ID']] : null;
     if ($value['action'] == 'content') {
      $result[$key]['content_name'] = $contentNames[$value['comments']];
     } else if ($value['action'] == 'tests' || $value['action'] == 'test_begin') {
      $result[$key]['content_name'] = $testNames[$value['comments']];
     }
    }
    $smarty -> assign("T_USER_LOG", $result);
   }
   //pr($infoUser -> getUserStatusInLessons());
   foreach ($userLessons as $id => $lesson) {
    $userTraffic = EfrontStats :: getUsersTime($lesson, $infoUser -> user['login'], $from, $to);
    if ($userTraffic[$infoUser -> user['login']]['accesses']) {
     $traffic['lessons'][$id] = $userTraffic[$infoUser -> user['login']];
     $traffic['lessons'][$id]['name'] = $lesson -> lesson['name'];
     $traffic['lessons'][$id]['active'] = $lesson -> lesson['active'];
     $traffic['total_access'] += $traffic['lessons'][$id]['accesses'];
    }
   }
   $result = eF_getTableData("logs", "count(*)", "action = 'login' and timestamp between $from and $to and users_LOGIN='".$infoUser -> user['login']."' order by timestamp");
   $traffic['total_logins'] = $result[0]['count(*)'];
   $smarty -> assign("T_USER_TRAFFIC", $traffic);
   $smarty -> assign('T_FROM_TIMESTAMP', $from);
   $smarty -> assign('T_TO_TIMESTAMP', $to);
  } catch (Exception $e) {
   handleNormalFlowExceptions($e);
  }
 }
}
if (isset($_GET['excel']) && $_GET['excel'] == 'user') {
 require_once 'Spreadsheet/Excel/Writer.php';
 $workBook = new Spreadsheet_Excel_Writer();
 $workBook -> setTempDir(G_UPLOADPATH);
 $workBook -> setVersion(8);
 $workBook -> send('export_'.$infoUser -> user['login'].'.xls');
 $formatExcelHeaders = & $workBook -> addFormat(array('Size' => 14, 'Bold' => 1, 'HAlign' => 'left'));
 $headerFormat = & $workBook -> addFormat(array('border' => 0, 'bold' => '1', 'size' => '11', 'color' => 'black', 'fgcolor' => 22, 'align' => 'center'));
 $formatContent = & $workBook -> addFormat(array('HAlign' => 'left', 'Valign' => 'top', 'TextWrap' => 1));
 $headerBigFormat = & $workBook -> addFormat(array('HAlign' => 'center', 'FgColor' => 22, 'Size' => 16, 'Bold' => 1));
 $titleCenterFormat = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 11, 'Bold' => 1));
 $titleLeftFormat = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 11, 'Bold' => 1));
 $fieldLeftFormat = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10));
 $fieldRightFormat = & $workBook -> addFormat(array('HAlign' => 'right', 'Size' => 10));
 $fieldCenterFormat = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 10));
 //first tab
 $workSheet = & $workBook -> addWorksheet("(".$infoUser -> user['login'].") General Statistics");
 $workSheet -> setInputEncoding('utf-8');
 $workSheet -> setColumn(0, 0, 5);
 //basic info
 $workSheet -> write(1, 1, _BASICINFO, $headerFormat);
 $workSheet -> mergeCells(1, 1, 1, 2);
 $workSheet -> setColumn(1, 2, 35);
 $roles = EfrontUser :: getRoles(true);
 $row = 2;
 $workSheet -> write($row, 1, _LOGIN, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $userInfo['general']['login'], $fieldRightFormat);
 $workSheet -> write($row, 1, _USERNAME, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $userInfo['general']['fullname'], $fieldRightFormat);
 $workSheet -> write($row, 1, _USERTYPE, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $roles[$userInfo['general']['user_type']], $fieldRightFormat);
 $workSheet -> write($row, 1, _USERROLE, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $roles[$userInfo['general']['user_types_ID']], $fieldRightFormat);
 if ($GLOBALS['configuration']['lesson_enroll']) {
  $workSheet -> write($row, 1, _LESSONS, $fieldLeftFormat);
  $workSheet -> write($row++, 2, $userInfo['general']['total_lessons'], $fieldRightFormat);
 }
 $workSheet -> write($row, 1, _COURSES, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $userInfo['general']['total_courses'], $fieldRightFormat);
 $workSheet -> write($row, 1, _TOTALLOGINTIME, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $userInfo['general']['total_login_time']['hours']."h ". $userInfo['general']['total_login_time']['minutes']."' ".$userInfo['general']['total_login_time']['seconds']."'' ", $fieldRightFormat);
 $workSheet -> write($row, 1, _LANGUAGE, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $userInfo['general']['language'], $fieldRightFormat);
 $workSheet -> write($row, 1, _ACTIVE, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $userInfo['general']['active_str'], $fieldRightFormat);
 $workSheet -> write($row, 1, _JOINED, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $userInfo['general']['joined_str'], $fieldRightFormat);
 //communication info
 $workSheet -> write($row, 1, _USERCOMMUNICATIONINFO, $headerFormat);
 $workSheet -> mergeCells($row, 1, $row++, 2);
 //$workSheet -> setColumn(10, 10, 35);
 if ($GLOBALS['configuration']['disable_forum'] != 1) {
  $workSheet -> write($row, 1, _FORUMPOSTS, $fieldLeftFormat);
  $workSheet -> write($row++, 2, sizeof($userInfo['communication']['forum_messages']), $fieldRightFormat);
  $workSheet -> write($row, 1, _FORUMLASTMESSAGE, $fieldLeftFormat);
  $workSheet -> write($row++, 2, $userInfo['communication']['forum_last_message'], $fieldRightFormat);
 }
 if ($GLOBALS['configuration']['disable_messages'] != 1) {
  $workSheet -> write($row, 1, _PERSONALMESSAGES, $fieldLeftFormat);
  $workSheet -> write($row++, 2, sizeof($userInfo['communication']['personal_messages']), $fieldRightFormat);
  $workSheet -> write($row, 1, _MESSAGESFOLDERS, $fieldLeftFormat);
  $workSheet -> write($row++, 2, sizeof($userInfo['communication']['personal_folders']), $fieldRightFormat);
 }
 $workSheet -> write($row, 1, _FILES, $fieldLeftFormat);
 $workSheet -> write($row++, 2, sizeof($userInfo['communication']['files']), $fieldRightFormat);
 $workSheet -> write($row, 1, _FOLDERS, $fieldLeftFormat);
 $workSheet -> write($row++, 2, sizeof($userInfo['communication']['personal_folders']), $fieldRightFormat);
 $workSheet -> write($row, 1, _TOTALSIZE, $fieldLeftFormat);
 $workSheet -> write($row++, 2, sizeof($userInfo['communication']['total_size'])._KB, $fieldRightFormat);
 if ($GLOBALS['configuration']['chat_enabled']) {
  $workSheet -> write($row, 1, _CHATMESSAGES, $fieldLeftFormat);
  $workSheet -> write($row++, 2, sizeof($userInfo['communication']['chat_messages']), $fieldRightFormat);
  $workSheet -> write($row, 1, _CHATLASTMESSAGE, $fieldLeftFormat);
  $workSheet -> write($row++, 2, $userInfo['communication']['chat_last_message'], $fieldRightFormat);
 }
 if ($GLOBALS['configuration']['disable_comments'] != 1) {
  $workSheet -> write($row, 1, _COMMENTS, $fieldLeftFormat);
  $workSheet -> write($row++, 2, sizeof($userInfo['communication']['comments']), $fieldRightFormat);
 }
 //usage info
 $workSheet -> write($row, 1, _USERUSAGEINFO, $headerFormat);
 $workSheet -> mergeCells($row, 1, $row++, 2);
 //$workSheet -> setColumn(21, 21, 35);
 $workSheet -> write($row, 1, _LASTLOGIN, $fieldLeftFormat);
 $workSheet -> write($row++, 2, formatTimestamp($userInfo['usage']['last_login']['timestamp'], 'time'), $fieldRightFormat);
 $workSheet -> write($row, 1, _TOTALLOGINS, $fieldLeftFormat);
 $workSheet -> write($row++, 2, sizeof($userInfo['usage']['logins']), $fieldRightFormat);
 $workSheet -> write($row, 1, _MONTHLOGINS, $fieldLeftFormat);
 $workSheet -> write($row++, 2, sizeof($userInfo['usage']['month_logins']), $fieldRightFormat);
 $workSheet -> write($row, 1, _WEEKLOGINS, $fieldLeftFormat);
 $workSheet -> write($row++, 2, sizeof($userInfo['usage']['week_logins']), $fieldRightFormat);
 $workSheet -> write($row, 1, _MEANDURATION, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $userInfo['usage']['mean_duration']."'", $fieldRightFormat);
 $workSheet -> write($row, 1, _MONTHMEANDURATION, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $userInfo['usage']['month_mean_duration']."'", $fieldRightFormat);
 $workSheet -> write($row, 1, _WEEKMEANDURATION, $fieldLeftFormat);
 $workSheet -> write($row++, 2, $userInfo['usage']['week_mean_duration']."'", $fieldRightFormat);
 $row = 1;
 //course users info
 $constraints = array('instance' => false, 'archive' => false, 'active' => true);
 $constraints['required_fields'] = array('has_instances', 'location', 'user_type', 'completed', 'score', 'has_course', 'num_lessons', 'to_timestamp');
 $constraints['return_objects'] = false;
 $userCourses = $infoUser -> getUserCoursesAggregatingResults($constraints);
 if (sizeof($userCourses) > 0) {
  $workSheet -> write($row, 4, _COURSESINFO, $headerFormat);
  $workSheet -> mergeCells($row, 4, $row, 11);
  $workSheet -> setColumn($row, 10, 15);
  $row++;
  $workSheet -> write($row, 4, _COURSE, $titleLeftFormat);
  //$workSheet -> write($row, 5, _LESSONS, $titleCenterFormat);
  $workSheet -> write($row, 6, _SCORE, $titleCenterFormat);
  $workSheet -> write($row, 7, _COMPLETED, $titleCenterFormat);
  $workSheet -> write($row, 8, _COMPLETEDON, $titleCenterFormat);
  foreach ($userCourses as $id => $course) {
   //$course = $course -> course;
   $row++;
   $workSheet -> write($row, 4, $course['name'], $fieldLeftFormat);
   //$workSheet -> write($row, 5, $course['lessons'], $fieldCenterFormat);
   $workSheet -> write($row, 6, formatScore($course['score'])."%", $fieldCenterFormat);
   $workSheet -> write($row, 7, $course['completed'] ? _YES : _NO, $fieldCenterFormat);
   $workSheet -> write($row, 8, formatTimestamp($course['to_timestamp']), $fieldCenterFormat);
  }
 }
 $row++;
 //lesson info
 $userLessons = $infoUser -> getUserStatusInLessons();
 if (sizeof($userLessons) > 0 && $GLOBALS['configuration']['lesson_enroll']) {
  $workSheet -> write($row, 4, _LESSONSINFO, $headerFormat);
  $workSheet -> mergeCells($row, 4, $row, 11);
  $workSheet -> setColumn(4, 10, 15);
  $row++;
  $workSheet -> write($row, 4, _LESSON, $titleLeftFormat);
  $workSheet -> write($row, 5, _TIME, $titleCenterFormat);
  $workSheet -> write($row, 6, _OVERALL, $titleCenterFormat);
  if ($GLOBALS['configuration']['disable_tests'] != 1) {
   $workSheet -> write($row, 7, _TESTS, $titleCenterFormat);
  }
  if($GLOBALS['configuration']['disable_projects'] != 1) {
   $workSheet -> write($row, 8, _PROJECTS, $titleCenterFormat);
  }
  $workSheet -> write($row, 9, _COMPLETED, $titleCenterFormat);
  $workSheet -> write($row, 10, _COMPLETEDON, $titleCenterFormat);
  $workSheet -> write($row++, 11, _GRADE, $titleCenterFormat);
  foreach ($userLessons as $id => $lesson) {
   $lesson = $lesson -> lesson;
   if ($lesson['active'] && !$lesson['course_only']) {
    $workSheet -> write($row, 4, str_replace("&nbsp;&rarr;&nbsp;", " -> ", $lesson['name']), $fieldLeftFormat);
    $workSheet -> write($row, 5, $lesson['time_in_lesson']['time_string'], $fieldCenterFormat);
    $workSheet -> write($row, 6, formatScore($lesson['overall_progress']['percentage'])."%", $fieldCenterFormat);
    if($GLOBALS['configuration']['disable_tests'] != 1) {
     $workSheet -> write($row, 7, formatScore($lesson['test_status']['mean_score'])."%", $fieldCenterFormat);
    }
    if($GLOBALS['configuration']['disable_projects'] != 1) {
     $workSheet -> write($row, 8, formatScore($lesson['project_status']['mean_score'])."%", $fieldCenterFormat);
    }
    $workSheet -> write($row, 9, $lesson['completed'] ? _YES : _NO, $fieldCenterFormat);
    $workSheet -> write($row, 10, formatTimestamp($lesson['timestamp_completed']), $fieldCenterFormat);
    $workSheet -> write($row, 11, $lesson['completed'] ? formatScore($lesson['score'])."%" : '', $fieldCenterFormat);
    $row++;
   }
  }
  $row++;
 }
 $result = eF_getTableDataFlat("lessons", "id, name, active");
 $lessonNames = array_combine($result['id'], $result['name']);
 //Done tests sheet
 $doneTests = EfrontStats :: getStudentsDoneTests(false, $infoUser -> user['login']);
 if (sizeof($doneTests[$infoUser -> user['login']]) > 0) {
  $workSheet = & $workBook -> addWorksheet('Tests Info');
  $workSheet -> setInputEncoding('utf-8');
  $workSheet -> setColumn(0, 0, 5);
  $row = 1;
  $workSheet -> write($row, 1, _TESTSINFORMATION, $headerFormat);
  $workSheet -> mergeCells($row, 1, $row, 4);
  $workSheet -> setColumn(1, 4, 25);
  $row++;
  $workSheet -> write($row, 1, _LESSON, $titleLeftFormat);
  $workSheet -> write($row, 2, _TESTNAME, $titleCenterFormat);
  $workSheet -> write($row, 3, _SCORE, $titleCenterFormat);
  $workSheet -> write($row++, 4, _DATE, $titleCenterFormat);
  $avgScore = 0;
  foreach ($doneTests[$infoUser -> user['login']] as $contentId => $test) {
   $workSheet -> write($row, 1, $lessonNames[$test['lessons_ID']], $fieldLeftFormat);
   $workSheet -> write($row, 2, $test['name'], $fieldCenterFormat);
   $workSheet -> write($row, 3, formatScore($test['score'])."%", $fieldCenterFormat);
   $workSheet -> write($row++, 4, formatTimestamp($test['timestamp'], 'time_nosec'), $fieldCenterFormat);
   $avgScore += $test['score'];
  }
  $row +=2;
  $workSheet -> write($row, 2, _AVERAGESCORE, $titleLeftFormat);
  $workSheet -> write($row++, 3, formatScore($avgScore / sizeof($doneTests[$infoUser -> user['login']]))."%", $fieldCenterFormat);
 }
 //Assigend projects sheet
 $assignedProjects = EfrontStats :: getStudentsAssignedProjects(false, $infoUser -> user['login']);
 if (sizeof($assignedProjects[$infoUser -> user['login']]) > 0 && $GLOBALS['configuration']['disable_projects'] != 1) {
  $workSheet = & $workBook -> addWorksheet('Projects Info');
  $workSheet -> setInputEncoding('utf-8');
  $workSheet -> setColumn(0, 0, 5);
  $row = 1;
  $workSheet -> write($row, 1, _PROJECTSINFORMATION, $headerFormat);
  $workSheet -> mergeCells($row, 1, $row, 4);
  $workSheet -> setColumn(1, 4, 25);
  $row++;
  $workSheet -> write($row, 1, _LESSON, $titleLeftFormat);
  $workSheet -> write($row, 2, _PROJECTNAME, $titleLeftFormat);
  $workSheet -> write($row, 3, _SCORE, $titleCenterFormat);
  $workSheet -> write($row++, 4, _COMMENTS, $titleLeftFormat);
  $avgScore = 0;
  foreach ($assignedProjects[$infoUser -> user['login']] as $project) {
   $workSheet -> write($row, 1, $lessonNames[$project['lessons_ID']], $fieldLeftFormat);
   $workSheet -> write($row, 2, $project['title'], $fieldLeftFormat);
   $workSheet -> write($row, 3, formatScore($project['grade'])."%", $fieldCenterFormat);
   $workSheet -> write($row++, 4, $project['comments'], $fieldLeftFormat);
   $avgScore += $project['grade'];
  }
  $row +=2;
  $workSheet -> write($row, 2, _AVERAGESCORE, $titleLeftFormat);
  $workSheet -> write($row++, 3, formatScore($avgScore / sizeof($assignedProjects[$infoUser -> user['login']]))."%", $titleCenterFormat);
 }
 //transpose tests array, from (login => array(test id => test)) to array(lesson id => array(login => array(test id => test)))
 $temp = array();
 foreach ($doneTests as $login => $userTests) {
  foreach ($userTests as $contentId => $test) {
   $temp[$test['lessons_ID']][$login][$contentId] = $test;
  }
 }
 $doneTests = $temp;
 //transpose projects array, from (login => array(project id => project)) to array(lesson id => array(login => array(project id => project)))
 $temp = array();
 foreach ($assignedProjects as $login => $userProjects) {
  foreach ($userProjects as $projectId => $project) {
   $temp[$project['lessons_ID']][$login][$projectId] = $project;
  }
 }
 $assignedProjects = $temp;
 //add a separate sheet for each distinct course of that user
 $count = 1;
 foreach ($userCourses as $id => $course) {
  $constraints = array('instance' => $id, 'archive' => false, 'active' => true);
  $constraints['required_fields'] = array('has_instances', 'location', 'user_type', 'completed', 'score', 'has_course', 'num_lessons', 'to_timestamp');
  //$constraints['return_objects']  = false;
  $instances = $infoUser -> getUserCourses($constraints);
  //unset($instances[$course -> course['id']]); //Remove self from instances
  //$course = $course -> course;
  $workSheet = & $workBook -> addWorksheet("Course ".$count++);
  $workSheet -> setInputEncoding('utf-8');
  $workSheet -> write(0, 0, $course['name'], $headerBigFormat);
  $workSheet -> mergeCells(0, 0, 0, 9);
  $workSheet -> write(1, 0, $infoUser -> user['name']." ".$infoUser -> user['surname'].' ('.$infoUser -> user['login'].')', $fieldCenterFormat);
  $workSheet -> mergeCells(1, 0, 1, 9);
  $workSheet -> setColumn(0, 0, 20);
  $workSheet -> setColumn(1, 1, 20);
  $row = 3;
  $workSheet -> write($row, 0, _STATUS, $headerFormat);
  $workSheet -> mergeCells($row, 0, $row++, 1);
  $workSheet -> write($row, 0, _COMPLETED, $fieldCenterFormat);
  $workSheet -> write($row++, 1, $course['completed'] ? _YES : _NO, $fieldCenterFormat);
  $workSheet -> write($row, 0, _COMPLETEDON, $fieldCenterFormat);
  $workSheet -> write($row++, 1, formatTimestamp($course['to_timestamp']), $fieldCenterFormat);
  $workSheet -> write($row, 0, _GRADE, $fieldCenterFormat);
  $workSheet -> write($row++, 1, formatScore($course['score'])."%", $fieldCenterFormat);
  if (sizeof($instances) > 1) {
   $row++;
   $workSheet -> write($row, 0, _COURSEINSTANCES, $headerFormat);
   $workSheet -> mergeCells($row, 0, $row, 2);
   $workSheet -> setColumn(0, 2, 25);
   $row++;
   $workSheet -> write($row, 0, _INSTANCE, $titleLeftFormat);
   $workSheet -> write($row, 1, _COMPLETED, $titleCenterFormat);
   $workSheet -> write($row, 3, _SCORE, $titleCenterFormat);
   foreach ($instances as $instance) {
    $row++;
    $workSheet -> write($row, 0, $instance -> course['name'], $fieldLeftFormat);
    $workSheet -> write($row, 1, $instance -> course['completed'] ? _YES.', '._ON.' '.formatTimestamp($instance -> course['to_timestamp']) : _NO, $fieldCenterFormat);
    $workSheet -> write($row, 3, formatScore($instance -> course['score'])."%", $fieldCenterFormat);
   }
  }
  $row+=2;
  foreach ($instances as $instance) {
   $row+=2;
   $workSheet -> write($row, 0, '"'.$instance -> course['name'].'" '._LESSONS, $headerFormat);
   $workSheet -> mergeCells($row, 0, $row, 2);
   $workSheet -> setColumn(0, 2, 25);
   $row++;
   $workSheet -> write($row, 0, _NAME, $titleLeftFormat);
   $workSheet -> write($row, 1, _COMPLETED, $titleCenterFormat);
   $workSheet -> write($row, 2, _SCORE, $titleCenterFormat);
   $lessons = $infoUser -> getUserStatusInCourseLessons($instance);
   foreach ($lessons as $lesson) {
    $row++;
    $workSheet -> write($row, 0, $lesson -> lesson['name'], $fieldLeftFormat);
    $workSheet -> write($row, 1, $lesson -> lesson['completed'] ? _YES.', '._ON.' '.formatTimestamp($lesson -> lesson['timestamp_completed']) : _NO, $fieldCenterFormat);
    $workSheet -> write($row, 2, formatScore($lesson -> lesson['score'])."%", $fieldCenterFormat);
   }
  }
 }
 //add a separate sheet for each distinct lesson of that user
 $count = 1;
 foreach ($userLessons as $id => $lesson) {
  $lesson = $lesson -> lesson;
  $workSheet = & $workBook -> addWorksheet("Lesson ".$count++);
  $workSheet -> setInputEncoding('utf-8');
  $workSheet -> write(0, 0, $lesson['name'], $headerBigFormat);
  $workSheet -> mergeCells(0, 0, 0, 9);
  $workSheet -> write(1, 0, $infoUser -> user['name']." ".$infoUser -> user['surname'].' ('.$infoUser -> user['login'].')', $fieldCenterFormat);
  $workSheet -> mergeCells(1, 0, 1, 9);
  $workSheet -> setColumn(0, 0, 20);
  $workSheet -> setColumn(1, 1, 20);
  $row = 3;
  $workSheet -> write($row, 0, _TIMEINLESSON, $headerFormat);
  $workSheet -> mergeCells($row, 0, $row++, 1);
  $workSheet -> write($row, 0, $lesson['time_in_lesson']['time_string'], $fieldCenterFormat);
  $workSheet -> mergeCells($row, 0, $row++, 1);
  $workSheet -> write($row, 0, _STATUS, $headerFormat);
  $workSheet -> mergeCells($row, 0, $row++, 1);
  $workSheet -> write($row, 0, _COMPLETED, $fieldCenterFormat);
  $workSheet -> write($row++, 1, $lesson['completed'] ? _YES : _NO, $fieldCenterFormat);
  $workSheet -> write($row, 0, _COMPLETEDON, $fieldCenterFormat);
  $workSheet -> write($row++, 1, formatTimestamp($lesson['timestamp_completed']), $fieldCenterFormat);
  $workSheet -> write($row, 0, _GRADE, $fieldCenterFormat);
  $workSheet -> write($row++, 1, formatScore($lesson['score'])."%", $fieldCenterFormat);
  $workSheet -> write($row, 0, _OVERALL, $headerFormat);
  $workSheet -> mergeCells($row, 0, $row++, 1);
  $workSheet -> write($row, 0, formatScore($lesson['overall_progress']['percentage'])."%", $fieldCenterFormat);
  $workSheet -> mergeCells($row, 0, $row++, 1);
  if (sizeof($doneTests[$id][$infoUser -> user['login']]) > 0 && $GLOBALS['configuration']['disable_tests'] != 1) {
   $workSheet -> write($row, 0, _TESTS, $headerFormat);
   $workSheet -> mergeCells($row, 0, $row++, 1);
   $avgScore = 0;
   foreach ($doneTests[$id][$infoUser -> user['login']] as $test) {
    $workSheet -> write($row, 0, $test['name'], $fieldCenterFormat);
    $workSheet -> write($row++, 1, formatScore($test['score'])."%", $fieldCenterFormat);
    $avgScore += $test['score'];
   }
   $workSheet -> write($row, 0, _AVERAGESCORE, $titleCenterFormat);
   $workSheet -> write($row++, 1, formatScore($avgScore / sizeof($doneTests[$id][$infoUser -> user['login']]))."%", $titleCenterFormat);
  }
  if (sizeof($assignedProjects[$id][$infoUser -> user['login']]) > 0 && $GLOBALS['configuration']['disable_projects'] != 1) {
   $workSheet -> write($row, 0, _PROJECTS, $headerFormat);
   $workSheet -> mergeCells($row, 0, $row++, 1);
   $avgScore = 0;
   foreach ($assignedProjects[$id][$infoUser -> user['login']] as $project) {
    $workSheet -> write($row, 0, $project['title'], $fieldCenterFormat);
    $workSheet -> write($row++, 1, formatScore($project['grade'])."%", $fieldCenterFormat);
    $avgScore += $project['grade'];
   }
   $workSheet -> write($row, 0, _AVERAGESCORE, $titleCenterFormat);
   $workSheet -> write($row++, 1, formatScore($avgScore / sizeof($assignedProjects[$id][$infoUser -> user['login']]))."%", $titleCenterFormat);
  }
 }
 $workBook -> close();
 exit();
} else if (isset($_GET['pdf']) && $_GET['pdf'] == 'user') {
 $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
 $pdf -> SetCreator(PDF_CREATOR);
 $pdf -> SetAuthor(PDF_AUTHOR);
 //set margins
 $pdf -> SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
 //set auto page breaks
 $pdf -> SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
 $pdf -> SetHeaderMargin(PDF_MARGIN_HEADER);
 $pdf -> SetFooterMargin(PDF_MARGIN_FOOTER);
 $pdf -> setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor
 $pdf -> setHeaderFont(Array('FreeSerif', 'I', 11));
 $pdf -> setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
 $pdf -> setHeaderData('','','', _STATISTICSFORUSER.": ".$infoUser -> user['name'].' '.$infoUser -> user['surname'].' ('.$infoUser -> user['login'].')');
 //initialize document
 $pdf -> AliasNbPages();
 $pdf -> AddPage();
 $pdf -> SetFont("FreeSerif", "B", 12);
 $pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(100, 10, _GENERALUSERINFO, 0, 1, L, 0);
 $roles = EfrontUser :: getRoles(true);
 $pdf -> SetFont("FreeSerif", "", 10);
 $pdf -> Cell(70, 5, _HUMANNAME, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['general']['name']." ".$userInfo['general']['surname'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(70, 5, _USERTYPE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $roles[$userInfo['general']['user_type']], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(70, 5, _USERROLE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $roles[$userInfo['general']['user_types_ID']], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(70, 5, _LANGUAGE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['general']['language'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(70, 5, _ACTIVE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['general']['active'] ? _YES : _NO, 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(70, 5, _JOINED, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['general']['joined_str'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> SetFont("FreeSerif", "B", 12);
 $pdf -> SetTextColor(0,0,0);
 $pdf -> Cell(100, 10, _USERCOMMUNICATIONINFO, 0, 1, L, 0);
 $pdf -> SetFont("FreeSerif", "", 10);
 if ($GLOBALS['configuration']['disable_forum'] != 1) {
  $pdf -> Cell(70, 5, _FORUMPOSTS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['forum_messages']).' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
  $pdf -> Cell(70, 5, _FORUMLASTMESSAGE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['communication']['forum_last_message'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 }
 if ($GLOBALS['configuration']['disable_messages'] != 1) {
  $pdf -> Cell(70, 5, _PERSONALMESSAGES, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['personal_messages']).' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
  $pdf -> Cell(70, 5, _MESSAGESFOLDERS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['personal_folders']).' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 }
 $pdf -> Cell(70, 5, _FILES, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['files']).' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(70, 5, _FOLDERS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['personal_folders']).' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(70, 5, _TOTALSIZE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['communication']['total_size'].' '._KB, 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 if ($GLOBALS['configuration']['chat_enabled']) {
  $pdf -> Cell(70, 5, _CHATMESSAGES, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['chat_messages']).' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
  $pdf -> Cell(70, 5, _CHATLASTMESSAGE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['communication']['chat_last_message'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 }
 if ($GLOBALS['configuration']['disable_comments'] != 1) {
  $pdf -> Cell(70, 5, _COMMENTS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['comments']).' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 }
 $pdf -> SetFont("FreeSerif", "B", 12);
 $pdf -> SetTextColor(0,0,0);
 $pdf -> Cell(100, 10, _USERUSAGEINFO, 0, 1, L, 0);
 $pdf -> SetFont("FreeSerif", "", 10);
 $pdf -> Cell(90, 5, _LASTLOGIN, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, formatTimestamp($userInfo['usage']['last_login']['timestamp'], 'time'), 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(90, 5, _TOTALLOGINS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, sizeof($userInfo['usage']['logins']), 0, 1, L, 0).' ';$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(90, 5, _MONTHLOGINS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, sizeof($userInfo['usage']['month_logins']), 0, 1, L, 0).' ';$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(90, 5, _WEEKLOGINS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, sizeof($userInfo['usage']['week_logins']), 0, 1, L, 0).' ';$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(90, 5, _MEANDURATION, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, $userInfo['usage']['mean_duration']."'", 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(90, 5, _MONTHMEANDURATION, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, $userInfo['usage']['month_mean_duration']."'", 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $pdf -> Cell(90, 5, _WEEKMEANDURATION, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, $userInfo['usage']['week_mean_duration']."'", 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
 $constraints = array('instance' => false, 'archive' => false, 'active' => true);
 $constraints['required_fields'] = array('has_instances', 'location', 'user_type', 'completed', 'score', 'has_course', 'num_lessons', 'to_timestamp');
 $constraints['return_objects'] = false;
 $userCourses = $infoUser -> getUserCoursesAggregatingResults($constraints);
 if (sizeof($userCourses) > 0) {
  $pdf -> SetTextColor(0, 0, 0);
  $pdf -> AddPage('L');
  $pdf -> SetFont("FreeSerif", "B", 12);
  $pdf -> Cell(60, 12, _COURSES, 0, 1, L, 0);
  $pdf -> SetFont("FreeSerif", "B", 10);
  $pdf -> Cell(100, 7, _COURSE, 0, 0, L, 0);
  $pdf -> Cell(50, 7, _COMPLETED, 0, 0, L, 0);
  $pdf -> Cell(50, 7, _SCORE, 0, 0, L, 0);
  $pdf -> Cell(50, 7, _COMPLETEDON, 0, 1, L, 0);
  $pdf -> SetFont("FreeSerif", "", 10);
  $pdf -> SetTextColor(0, 0, 255);
  foreach ($userCourses as $id => $course) {
   $pdf -> Cell(100, 5, str_replace("&nbsp;&rarr;&nbsp;", " -> ", $course['name']), 0, 0, L, 0);
   $pdf -> Cell(50, 5, $course['completed'] ? _YES : _NO, 0, 0, L, 0);
   $pdf -> Cell(50, 5, formatScore($course['score'])."%", 0, 0, L, 0);
   $pdf -> Cell(50, 5, formatTimestamp($course['to_timestamp']), 0, 1, L, 0);
  }
 }
 //lessons page
 $userLessons = $infoUser -> getUserStatusInLessons();
 if (sizeof($userLessons) > 0 && $GLOBALS['configuration']['lesson_enroll']) {
  $pdf -> SetTextColor(0, 0, 0);
  $pdf -> AddPage('L');
  $pdf -> SetFont("FreeSerif", "B", 12);
  $pdf -> Cell(60, 12, _LESSONS, 0, 1, L, 0);
  $pdf -> SetFont("FreeSerif", "B", 10);
  $pdf -> Cell(70, 7, _LESSON, 0, 0, L, 0);
  $pdf -> Cell(30, 7, _TIMEINLESSON, 0, 0, L, 0);
  $pdf -> Cell(40, 7, _COMPLETED, 0, 0, L, 0);
  if ($GLOBALS['configuration']['disable_tests'] != 1 && $GLOBALS['configuration']['disable_projects'] != 1) {
   $pdf -> Cell(40, 7, _OVERALL, 0, 0, C, 0);
   $pdf -> Cell(40, 7, _TESTS, 0, 0, C, 0);
   $pdf -> Cell(40, 7, _PROJECTS, 0, 1, C, 0);
  } elseif($GLOBALS['configuration']['disable_projects'] != 1) {
   $pdf -> Cell(40, 7, _OVERALL, 0, 0, C, 0);
   $pdf -> Cell(40, 7, _PROJECTS, 0, 1, C, 0);
  } elseif($GLOBALS['configuration']['disable_tests'] != 1) {
   $pdf -> Cell(40, 7, _OVERALL, 0, 0, C, 0);
   $pdf -> Cell(40, 7, _TESTS, 0, 1, C, 0);
  } else {
   $pdf -> Cell(40, 7, _OVERALL, 0, 1, C, 0);
  }
  $pdf -> SetFont("FreeSerif", "", 10);
  $pdf -> SetTextColor(0, 0, 255);
  foreach ($userLessons as $id => $lesson) {
   $lesson = $lesson -> lesson;
   if ($lesson['active']) {
    $pdf -> Cell(70, 5, str_replace("&nbsp;&rarr;&nbsp;", " -> ", $lesson['name']), 0, 0, L, 0);
    $pdf -> Cell(30, 5, $lesson['time_in_lesson']['time_string'], 0, 0, L, 0);
    $pdf -> Cell(40, 5, $lesson['completed'] ? _YES.', '._ON.' '.formatTimestamp($lesson['timestamp_completed']) : _NO, 0, 0, L, 0);
    if ($GLOBALS['configuration']['disable_tests'] != 1 && $GLOBALS['configuration']['disable_projects'] != 1) {
     $pdf -> Cell(40, 5, formatScore($lesson['overall_progress']['percentage'])."%", 0, 0, C, 0);
     $pdf -> Cell(40, 5, formatScore($lesson['test_status']['mean_score'])."%", 0, 0, C, 0);
     $pdf -> Cell(40, 5, formatScore($lesson['project_status']['mean_score'])."%", 0, 1, C, 0);
    } elseif($GLOBALS['configuration']['disable_projects'] != 1) {
     $pdf -> Cell(40, 5, formatScore($lesson['overall_progress']['percentage'])."%", 0, 0, C, 0);
     $pdf -> Cell(40, 5, formatScore($lesson['project_status']['mean_score'])."%", 0, 1, C, 0);
    } elseif($GLOBALS['configuration']['disable_tests'] != 1) {
     $pdf -> Cell(40, 5, formatScore($lesson['overall_progress']['percentage'])."%", 0, 0, C, 0);
     $pdf -> Cell(40, 5, formatScore($lesson['test_status']['mean_score'])."%", 0, 1, C, 0);
    } else {
     $pdf -> Cell(40, 5, formatScore($lesson['overall_progress']['percentage'])."%", 1, 0, C, 0);
    }
   }
  }
 }
 $result = eF_getTableDataFlat("lessons", "id, name, active");
 $lessonNames = array_combine($result['id'], $result['name']);
 //tests page
 if ($GLOBALS['configuration']['disable_tests'] != 1) {
  $doneTests = EfrontStats :: getStudentsDoneTests(false, $infoUser -> user['login']);
  if (sizeof($doneTests[$infoUser -> user['login']]) > 0) {
   $pdf -> SetTextColor(0, 0, 0);
   $pdf -> AddPage('L');
   $pdf -> SetFont("FreeSerif", "B", 12);
   $pdf -> Cell(60, 12, _TESTS, 0, 1, L, 0);
   $pdf -> SetFont("FreeSerif", "B", 10);
   $pdf -> Cell(100, 7, _LESSON, 0, 0, L, 0);
   $pdf -> Cell(100, 7, _TESTNAME, 0, 0, L, 0);
   $pdf -> Cell(40, 7, _SCORE, 0, 0, C, 0);
   $pdf -> Cell(40, 7, _DATE, 0, 1, C, 0);
   $pdf -> SetFont("FreeSerif", "", 10);
   $pdf -> SetTextColor(0, 0, 255);
   $avgScore = 0;
   foreach ($doneTests[$infoUser -> user['login']] as $test) {
    $pdf -> Cell(100, 5, $lessonNames[$test['lessons_ID']], 0, 0, L, 0);
    $pdf -> Cell(100, 5, $test['name'], 0, 0, L, 0);
    $pdf -> Cell(40, 5, formatScore($test['score'])."%", 0, 0, C, 0);
    $pdf -> Cell(40, 5, formatTimestamp($test['timestamp'], 'time_nosec'), 0, 1, C, 0);
    $avgScore += $test['score'];
   }
   $pdf -> Cell(100, 5, '', 0, 1, L, 0);
   $pdf -> SetFont("FreeSerif", "B", 10);$pdf -> SetTextColor(0, 0, 0);
   $pdf -> Cell(100, 5, '', 0, 0, L, 0);
   $pdf -> Cell(100, 5, _AVERAGESCORE, 0, 0, L, 0);
   $pdf -> Cell(40, 5, formatScore($avgScore / sizeof($doneTests[$infoUser -> user['login']]))."%", 0, 1, C, 0);
  }
 }
 //projects page
 if($GLOBALS['configuration']['disable_projects'] != 1) {
  $assignedProjects = EfrontStats :: getStudentsAssignedProjects(false, $infoUser -> user['login']);
  if (sizeof($assignedProjects[$infoUser -> user['login']]) > 0) {
   $pdf -> SetTextColor(0, 0, 0);
   $pdf -> AddPage('L');
   $pdf -> SetFont("FreeSerif", "B", 12);
   $pdf -> Cell(60, 12, _PROJECTS, 0, 1, L, 0);
   $pdf -> SetFont("FreeSerif", "B", 10);
   $pdf -> Cell(100, 7, _LESSON, 0, 0, L, 0);
   $pdf -> Cell(100, 7, _TITLE, 0, 0, L, 0);
   $pdf -> Cell(40, 7, _GRADE, 0, 1, C, 0);
   $pdf -> SetFont("FreeSerif", "", 10);
   $pdf -> SetTextColor(0, 0, 255);
   $avgScore = 0;
   foreach ($assignedProjects[$infoUser -> user['login']] as $project) {
    $pdf -> Cell(100, 5, $lessonNames[$project['lessons_ID']], 0, 0, L, 0);
    $pdf -> Cell(100, 5, $project['title'], 0, 0, L, 0);
    $pdf -> Cell(40, 5, formatScore($project['grade'])."%", 0, 1, C, 0);
    $avgScore += $project['grade'];
   }
   $pdf -> Cell(100, 5, '', 0, 1, L, 0);
   $pdf -> SetFont("FreeSerif", "B", 10);$pdf -> SetTextColor(0, 0, 0);
   $pdf -> Cell(100, 5, '', 0, 0, L, 0);
   $pdf -> Cell(100, 5, _AVERAGESCORE, 0, 0, L, 0);
   $pdf -> Cell(40, 5, formatScore($avgScore / sizeof($assignedProjects[$infoUser -> user['login']]))."%", 0, 1, C, 0);
  }
 }
 //transpose tests array, from (login => array(test id => test)) to array(lesson id => array(login => array(test id => test)))
 $temp = array();
 foreach ($doneTests as $login => $userTests) {
  foreach ($userTests as $contentId => $test) {
   $temp[$test['lessons_ID']][$login][$contentId] = $test;
  }
 }
 $doneTests = $temp;
 //transpose projects array, from (login => array(project id => project)) to array(lesson id => array(login => array(project id => project)))
 $temp = array();
 foreach ($assignedProjects as $login => $userProjects) {
  foreach ($userProjects as $projectId => $project) {
   $temp[$project['lessons_ID']][$login][$projectId] = $project;
  }
 }
 $assignedProjects = $temp;
 //add a separate sheet for each distinct course of that user
 foreach ($userCourses as $id => $course) {
  $constraints = array('instance' => $id, 'archive' => false, 'active' => true);
  $constraints['required_fields'] = array('has_instances', 'location', 'user_type', 'completed', 'score', 'has_course', 'num_lessons', 'to_timestamp');
  //$constraints['return_objects']  = false;
  $instances = $infoUser -> getUserCourses($constraints);
  $pdf -> SetTextColor(0, 0, 0);
  $pdf -> AddPage('L');
  $pdf -> SetFont("FreeSerif", "B", 12);
  $pdf -> Cell(60, 12, $course['name'], 0, 1, L, 0);
  $pdf -> SetFont("FreeSerif", "B", 10);
  $pdf -> Cell(40, 7, _COMPLETED, 0, 0, L, 0);
  $pdf -> Cell(40, 7, _GRADE, 0, 1, C, 0);
  $pdf -> SetFont("FreeSerif", "", 10);
  $pdf -> SetTextColor(0, 0, 255);
  $pdf -> Cell(40, 7, $course['completed'] ? _YES.', '._ON.' '.formatTimestamp($course['to_timestamp']) : _NO, 0, 0, L, 0);
  $pdf -> Cell(40, 7, formatScore($course['score'])."%", 0, 1, C, 0);
  if (sizeof($instances) > 1) {
   $pdf -> SetTextColor(0, 0, 0);
   $pdf -> SetFont("FreeSerif", "B", 10);
   $pdf -> Cell(60, 12, '', 0, 1, L, 0);
   $pdf -> Cell(60, 7, _COURSEINSTANCES, 0, 1, L, 0);
   $pdf -> SetFont("FreeSerif", "B", 10);
   $pdf -> Cell(80, 7, _INSTANCE, 0, 0, L, 0);
   $pdf -> Cell(40, 7, _COMPLETED, 0, 0, L, 0);
   $pdf -> Cell(40, 7, _GRADE, 0, 1, C, 0);
   $pdf -> SetTextColor(0, 0, 255);
   foreach ($instances as $instance) {
    $pdf -> Cell(80, 7, $instance -> course['name'], 0, 0, L, 0);
    $pdf -> Cell(40, 7, $instance -> course['completed'] ? _YES.', '._ON.' '.formatTimestamp($instance -> course['to_timestamp']) : _NO, 0, 0, L, 0);
    $pdf -> Cell(40, 7, formatScore($instance -> course['score'])."%", 0, 1, C, 0);
   }
  }
  foreach ($instances as $instance) {
   $pdf -> SetTextColor(0, 0, 0);
   $pdf -> SetFont("FreeSerif", "B", 10);
   $pdf -> Cell(60, 12, '', 0, 1, L, 0);
   $pdf -> Cell(60, 7, _LESSONS, 0, 1, L, 0);
   $pdf -> SetFont("FreeSerif", "B", 10);
   $pdf -> Cell(80, 7, _NAME, 0, 0, L, 0);
   $pdf -> Cell(40, 7, _COMPLETED, 0, 0, L, 0);
   $pdf -> Cell(40, 7, _GRADE, 0, 1, C, 0);
   $pdf -> SetTextColor(0, 0, 255);
   $lessons = $infoUser -> getUserStatusInCourseLessons($instance);
   foreach ($lessons as $lesson) {
    $pdf -> Cell(80, 7, $lesson -> lesson['name'], 0, 0, L, 0);
    $pdf -> Cell(40, 7, $lesson -> lesson['completed'] ? _YES.', '._ON.' '.formatTimestamp($lesson -> lesson['timestamp_completed']) : _NO, 0, 0, L, 0);
    $pdf -> Cell(40, 7, formatScore($lesson -> lesson['score'])."%", 0, 1, C, 0);
   }
  }
 }
 //add a separate sheet for each distinct lesson of that user
 foreach ($userLessons as $id => $lesson) {
  $lesson = $lesson -> lesson;
  $pdf -> SetTextColor(0, 0, 0);
  $pdf -> AddPage('L');
  $pdf -> SetFont("FreeSerif", "B", 12);
  $pdf -> Cell(60, 12, $lesson['name'], 0, 1, L, 0);
  $pdf -> SetFont("FreeSerif", "B", 10);
  $pdf -> Cell(40, 7, _TIMEINLESSON, 0, 0, L, 0);
  $pdf -> Cell(40, 7, _COMPLETED, 0, 0, L, 0);
  $pdf -> Cell(40, 7, _GRADE, 0, 0, C, 0);
  $pdf -> Cell(40, 7, _CONTENT, 0, 1, C, 0);
  $pdf -> SetFont("FreeSerif", "", 10);
  $pdf -> SetTextColor(0, 0, 255);
  $pdf -> Cell(40, 7, $lesson['time_in_lesson']['time_string'], 0, 0, L, 0);
  $pdf -> Cell(40, 7, $lesson['completed'] ? _YES.', '._ON.' '.formatTimestamp($lessons['timestamp_completed']) : _NO, 0, 0, L, 0);
  $pdf -> Cell(40, 7, formatScore($lesson['score'])."%", 0, 0, C, 0);
  $pdf -> Cell(40, 7, formatScore($lesson['overall_progress']['percentage'])."%", 0, 1, C, 0);
  if (sizeof($doneTests[$id][$infoUser -> user['login']]) > 0 && $GLOBALS['configuration']['disable_tests'] != 1) {
   $pdf -> SetTextColor(0, 0, 0);
   $pdf -> SetFont("FreeSerif", "B", 10);
   $pdf -> Cell(60, 12, '', 0, 1, L, 0);
   $pdf -> Cell(60, 7, _TESTS, 0, 1, L, 0);
   $pdf -> SetTextColor(0, 0, 255);
   $avgScore = 0;
   foreach ($doneTests[$id][$infoUser -> user['login']] as $test) {
    $pdf -> Cell(60, 7, $test['name'], 0, 0, L, 0);
    $pdf -> Cell(60, 7, formatScore($test['score'])."%", 0, 0, C, 0);
    $pdf -> Cell(60, 7, formatTimestamp($test['timestamp']), 0, 1, C, 0);
    $avgScore += $test['score'];
   }
   $pdf -> SetTextColor(0, 0, 0);
   $pdf -> Cell(60, 7, _AVERAGESCORE, 0, 0, L, 0);
   $pdf -> Cell(60, 7, formatScore($avgScore / sizeof($doneTests[$id][$infoUser -> user['login']]))."%", 0, 1, C, 0);
  }
  if (sizeof($assignedProjects[$id][$infoUser -> user['login']]) > 0 && $GLOBALS['configuration']['disable_projects'] != 1) {
   $pdf -> SetTextColor(0, 0, 0);
   $pdf -> SetFont("FreeSerif", "B", 10);
   $pdf -> Cell(60, 12, '', 0, 1, L, 0);
   $pdf -> Cell(60, 7, _PROJECTS, 0, 1, L, 0);
   $pdf -> SetTextColor(0, 0, 255);
   $avgScore = 0;
   foreach ($assignedProjects[$id][$infoUser -> user['login']] as $project) {
    $pdf -> Cell(60, 7, $project['title'], 0, 0, L, 0);
    $pdf -> Cell(60, 7, formatScore($project['grade'])."%", 0, 1, C, 0);
    $avgScore += $project['grade'];
   }
   $pdf -> SetTextColor(0, 0, 0);
   $pdf -> Cell(60, 7, _AVERAGESCORE, 0, 0, L, 0);
   $pdf -> Cell(60, 7, formatScore($avgScore / sizeof($assignedProjects[$id][$infoUser -> user['login']]))."%", 0, 1, C, 0);
  }
 }
 $pdf -> Output();
 exit(0);
}
?>
