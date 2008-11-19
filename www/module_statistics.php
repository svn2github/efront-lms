<?php
$loadScripts = array_merge($loadScripts, array('scriptaculous/prototype','scriptaculous/scriptaculous','scriptaculous/effects','scriptaculous/controls'));

$smarty -> assign("T_CATEGORY", 'statistics');
$smarty -> assign("T_BASIC_TYPE", $currentUser -> user['user_type']);

$isProfessor = 0;
$isStudent   = 0;

//check to see if the user has any lessons as a student and any lessons as professor
$lessonRoles = EfrontLessonUser::getLessonsRoles();
if ($currentUser -> user['user_type'] != 'administrator') {
    $lessons = $currentUser -> getLessons(false);
    foreach ($lessons as $key => $type) {
        if ($lessonRoles[$type] == 'professor') {
            $isProfessor = 1;
            $professorLessons[] = $key;
        } else if ($type == 'student') {
            $isStudent = 1;
            $studentLessons[] = $key;
        }
    }
}

$smarty -> assign("T_ISPROFESSOR", $isProfessor);
$smarty -> assign("T_ISSTUDENT", $isStudent);


if ($currentUser -> user['user_type'] != 'administrator') {
    if ($isProfessor) {
        if (isset($currentLesson) && !in_array($currentLesson -> lesson['id'], $professorLessons)) {
            $_GET['option'] = 'user';
        } else if (!isset($currentLesson) && $currentUser -> user['user_type'] != 'professor') {
            $_GET['option'] = 'user';
        }
    } else {
        $_GET['option'] = 'user';
        if (!$_SESSION['s_lessons_ID']) {
			$_GET['sel_user'] = $_SESSION['s_login'];
		
		}
    } 
}
$smarty -> assign("T_OPTION", $_GET['option']);

try {
    /*no option is set, so just show the available options*/
    if (!isset($_GET['option'])) {
        if ($currentUser -> user['user_type'] == 'administrator') {
            $options[]  = array('text' => _USERSTATISTICS,    'image' => "32x32/user1.png",   'href' => "administrator.php?ctg=statistics&option=user");
            $options[]  = array('text' => _LESSONSTATISTICS,  'image' => "32x32/board.png",   'href' => "administrator.php?ctg=statistics&option=lesson");
            $options[]  = array('text' => _COURSESTATISTICS,  'image' => "32x32/books.png",   'href' => "administrator.php?ctg=statistics&option=course");
            $options[]  = array('text' => _TESTSTATISTICS,    'image' => "32x32/edit.png",    'href' => "administrator.php?ctg=statistics&option=test");
            $options[]  = array('text' => _SYSTEMSTATISTICS,  'image' => "32x32/chart.png",   'href' => "administrator.php?ctg=statistics&option=system");
            $smarty -> assign("T_STATISTICS_OPTIONS", $options);
        } else if ($isProfessor) {
            $options[]  = array('text' => _USERSTATISTICS,    'image' => "32x32/user1.png",   'href' => $_SERVER['PHP_SELF']."?ctg=statistics&option=user");
            $options[]  = array('text' => _LESSONSTATISTICS,  'image' => "32x32/board.png",   'href' => $_SERVER['PHP_SELF']."?ctg=statistics&option=lesson");
            $options[]  = array('text' => _COURSESTATISTICS,  'image' => "32x32/books.png",   'href' => $_SERVER['PHP_SELF']."?ctg=statistics&option=course");
            $options[]  = array('text' => _TESTSTATISTICS,    'image' => "32x32/edit.png",    'href' => $_SERVER['PHP_SELF']."?ctg=statistics&option=test");
            $smarty -> assign("T_STATISTICS_OPTIONS", $options);
        }
    } else if ($_GET['option'] == 'user') {    
    
        if ($currentUser -> user['user_type'] == 'administrator') {
            $validUsers = EfrontUser :: getUsers(false);
        } else if ($_SESSION['s_lessons_ID']) {
            $statisticsLesson = new EfrontLesson($_SESSION['s_lessons_ID']);        
            $lessonUsers      = $statisticsLesson -> getUsers();
            if ($lessonRoles[$lessonUsers[$currentUser -> user['login']]['role']] == 'professor') {
                $validUsers = $lessonUsers;
            } else if ($lessonRoles[$lessonUsers[$currentUser -> user['login']]['role']] == 'student') {
                $validUsers[$currentUser -> user['login']] = $currentUser;
                $smarty -> assign("T_SINGLE_USER", true);                            //assign this variable, so that select user panel is not available
                $_GET['sel_user'] = $currentUser -> user['login'];          
            } else {
                throw new EfrontUserException(_USERDOESNOTHAVETHISLESSON.": ".$statisticsLesson -> lesson['name'], EfrontUserException :: USER_NOT_HAVE_LESSON);
            }
        } else {                                               //if the system user is a simple student
            
			if ($_SESSION['s_type'] == 'student') {
				$smarty -> assign("T_SINGLE_USER", true); 
			}
			$userLessons = $currentUser -> getLessons(true);
            $users       = array();
            foreach ($userLessons as $lesson) {
                $users = $users + $lesson -> getUsers();
            }
            $validUsers = $users;
        }

        if (isset($_GET['sel_user'])) {
            if (in_array($_GET['sel_user'], array_keys($validUsers))) {
                $infoUser = EfrontUserFactory :: factory($_GET['sel_user']);        
            } else {
                throw new EfrontUserException(_USERISNOTVALIDORYOUCANNOTSEEUSER.": ".$_GET['sel_user'], EfrontUserException :: INVALID_LOGIN);
            }
        }
    
        if ($infoUser) {
            $smarty -> assign("T_USER_LOGIN", $infoUser -> user['login']);
            $userInfo = array();
            $userInfo['general']       = $infoUser -> getInformation();
            $userInfo['communication'] = EfrontStats :: getUserCommunicationInfo($infoUser);
            if (sizeof($userInfo['communication']['chat_messages'])) {
                $last = current($userInfo['communication']['chat_messages']);
                $userInfo['communication']['chat_last_message'] = formatTimestamp($last['timestamp'], 'time');
            } else {
                $userInfo['communication']['chat_last_message'] = "";
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
                $smarty -> assign ("T_AVATAR", G_AVATARSPATH."system_avatars/unknown_small.png");
            }
    
            $smarty -> assign("T_USER_INFO", $userInfo);
            
            /*courses*/
            $roles = EfrontLessonUser :: getLessonsRoles();
            $studentCourses   = array();
            $professorCourses = array();
            if ($infoUser -> user['user_type'] != 'administrator') {
                $courses = $infoUser -> getCourses(false);
                foreach ($courses as $id => $type) {
                    if ($roles[$type] == 'student') {
                        $studentCourses[$id] = new EfrontCourse($id);
                    } else if ($roles[$type] == 'professor') {
                        $professorCourses[$id] = new EfrontCourse($id);
                    }
                }
            }
    
            $userCourseInfo = array();
            $status = EfrontStats :: getUsersCourseStatus(array_keys($studentCourses), $infoLesson -> user['login']);

            foreach ($studentCourses as $id  => $course) {
                $userCourseInfo['student'][$id] = array('name'      => $course -> course['name'],
                                                        'role'      => $status[$id][$infoUser -> user['login']]['user_type'],
                                                        'active'    => $course -> course['active'],
                                                        'completed' => $status[$id][$infoUser -> user['login']]['completed'],
                                                        'score'	    => $status[$id][$infoUser -> user['login']]['score'],
                										'lessons'   => sizeof($course -> getLessons(false)));
            }
            $status = EfrontStats :: getUsersCourseStatus(array_keys($professorCourses), $infoLesson -> user['login']);       
            foreach ($professorCourses as $id => $course) {
                $userCourseInfo['professor'][$id] = array('name'       => $course -> course['name'],
                                                          'role'       => $status[$id][$infoUser -> user['login']]['user_type'],
                										  'active'     => $course -> course['active'],
                                                          'lessons'    => sizeof($course -> getLessons(false)),
                                                          'professors' => 0,
                                                          'students'   => 0);
                foreach ($course -> getUsers(false) as $login => $cuser) {
                    if ($roles[$cuser['user_type']] == 'student') {
                        $userCourseInfo['professor'][$id]['students']++;
                    } else if ($roles[$cuser['user_type']] == 'professor') {
                        $userCourseInfo['professor'][$id]['professors']++;
                    }
                }
            }
            $smarty -> assign("T_USER_COURSE_INFO", $userCourseInfo);
                        
            //get information for the lessons the user is a student and professor
            $userStudentLessons   = $userInfo['general']['student_lessons'];
            $userProfessorLessons = $userInfo['general']['professor_lessons'];

            $userLessonInfo = array();
            $status = EfrontStats :: getUsersLessonStatus($userStudentLessons, $infoUser -> user['login']);

            foreach ($userStudentLessons as $lesson) {
                $time   = EfrontStats :: getUsersTime($lesson, $infoUser -> user['login']);
                $userLessonInfo['student'][$lesson -> lesson['id']] = array('name'           => $lesson -> lesson['name'],
                                                                            'role'           => $status[$lesson -> lesson['id']][$infoUser -> user['login']]['user_type'],
                															'content'        => $status[$lesson -> lesson['id']][$infoUser -> user['login']]['content_progress'],
                                                                            'tests'          => $status[$lesson -> lesson['id']][$infoUser -> user['login']]['tests_avg_score'],
                                                                            'tests_progress' => $status[$lesson -> lesson['id']][$infoUser -> user['login']]['tests_progress'],
                                                                            'total_tests'    => sizeof($lesson -> getTests()),
                                                                            'projects'       => $status[$lesson -> lesson['id']][$infoUser -> user['login']]['projects_avg_score'],
                                                                            'projects_progress' => $status[$lesson -> lesson['id']][$infoUser -> user['login']]['projects_progress'],
                															'total_projects' => sizeof($lesson -> getProjects()),
                                                                            'time'           => $time[$infoUser -> user['login']],
                                                                            'seconds'        => $userTimes[$lesson -> lesson['id']]['total_seconds'],
                                                                            'active'         => $lesson -> lesson['active'],
                                                                            'completed'      => $status[$lesson -> lesson['id']][$infoUser -> user['login']]['completed'],
                                                                            'score'          => $status[$lesson -> lesson['id']][$infoUser -> user['login']]['score']);
            }

            foreach ($userProfessorLessons as $lesson) {
                $time       = EfrontStats :: getUsersTime($lesson, $infoUser -> user['login']);
                $lessonInfo = $lesson -> getStatisticInformation();
                $userLessonInfo['professor'][$lesson -> lesson['id']] = array('name'      => $lesson -> lesson['name'],
                                                                              'role'      => $lessonInfo['professors'][$infoUser -> user['login']]['role'],
                															  'content'   => $lessonInfo['theory'],
                                                                              'tests'     => sizeof($lesson -> getTests()),
                                                                              'projects'  => sizeof($lesson -> getProjects()),
                                                                              'time'      => $time[$infoUser -> user['login']],
                                                                              'seconds'   => $professorTimes[$lesson -> lesson['id']]['total_seconds'],
                                                                              'active'    => $lesson -> lesson['active']);
            }        
            $smarty -> assign("T_USER_LESSON_INFO", $userLessonInfo);
            $smarty -> assign("T_ROLES", EfrontLessonUser :: getLessonsRoles(true));
    
            try {
                $actions = array('login'      => _LOGIN,
                                 'logout'     => _LOGOUT,
                                 'lesson'     => _ACCESSEDLESSON,
                                 'content'    => _ACCESSEDCONTENT,
                                 'tests'      => _ACCESSEDTEST,
                                 'test_begin' => _BEGUNTEST,
                                 'lastmove'   => _NAVIGATEDSYSTEM);
                $smarty -> assign("T_ACTIONS", $actions);
                
                if (isset($_GET['from_year'])) { //the admin has chosen a period
                    $from = mktime($_GET['from_hour'], $_GET['from_min'], 0, $_GET['from_month'], $_GET['from_day'], $_GET['from_year']);
                    $to   = mktime($_GET['to_hour'],   $_GET['to_min'],   0, $_GET['to_month'],   $_GET['to_day'],   $_GET['to_year']);
                } else {
                    $from    = mktime(date("H"), date("i"), 0, date("m"), date("d") - 7, date("Y"));
                    $to      = mktime(date("H"), date("i"), 0, date("m"), date("d"),     date("Y"));
                }
    
                if (isset($_GET['showlog']) && $_GET['showlog'] == "true") {
                    $lessonNames  = eF_getTableDataFlat("lessons", "id, name");
                    $lessonNames  = array_combine($lessonNames['id'], $lessonNames['name']);
                    $contentNames = eF_getTableDataFlat("content", "id, name");
                    $contentNames = array_combine($contentNames['id'], $contentNames['name']);
                    $testNames    = eF_getTableDataFlat("tests t, content c", "t.id, c.name", "c.id=t.content_ID");
                    $testNames    = array_combine($testNames['id'], $testNames['name']);
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
    
                foreach ($userStudentLessons + $userProfessorLessons as $id => $lesson) {
                    $userTraffic = EfrontStats :: getUsersTime($lesson, $infoUser -> user['login'], $from, $to);
                    if ($userTraffic[$infoUser -> user['login']]['accesses']) {
                        $traffic['lessons'][$id] = $userTraffic[$infoUser -> user['login']];
                        $traffic['lessons'][$id]['name']   = $lesson -> lesson['name'];
                        $traffic['lessons'][$id]['active'] = $lesson -> lesson['active'];
                        $traffic['total_access'] += $traffic['lessons'][$id]['accesses'];
                    }
                }
                $result = eF_getTableData("logs", "count(*)", "action = 'login' and timestamp between $from and $to and users_LOGIN='".$infoUser -> user['login']."' order by timestamp");
                $traffic['total_logins'] = $result[0]['count(*)'];

                $smarty -> assign("T_USER_TRAFFIC", $traffic);
                $smarty -> assign('T_FROM_TIMESTAMP', $from);
                $smarty -> assign('T_TO_TIMESTAMP',   $to);
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        }
    
    
        if (isset($_GET['excel']) && $_GET['excel'] == 'user') {
            require_once 'Spreadsheet/Excel/Writer.php';
    
            $workBook = new Spreadsheet_Excel_Writer();
            $workBook -> setVersion(8);
            $workBook -> send('export_'.$infoUser -> user['login'].'.xls');
    
            $formatExcelHeaders = & $workBook -> addFormat(array('Size' => 14, 'Bold' => 1, 'HAlign' => 'left'));
            $headerFormat       = & $workBook -> addFormat(array('border' => 0, 'bold' => '1', 'size'    => '11', 'color' => 'black', 'fgcolor' => 22, 'align' => 'center'));
            $formatContent      = & $workBook -> addFormat(array('HAlign' => 'left', 'Valign' => 'top', 'TextWrap' => 1));
            $headerBigFormat    = & $workBook -> addFormat(array('HAlign' => 'center', 'FgColor' => 22, 'Size' => 16, 'Bold' => 1));
            $titleCenterFormat  = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 11, 'Bold' => 1));
            $titleLeftFormat    = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 11, 'Bold' => 1));
            $fieldLeftFormat    = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10));
            $fieldRightFormat   = & $workBook -> addFormat(array('HAlign' => 'right', 'Size' => 10));
            $fieldCenterFormat  = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 10));
    
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
            $workSheet -> write($row, 1, _LESSONS, $fieldLeftFormat);
            $workSheet -> write($row++, 2, $userInfo['general']['total_lessons'], $fieldRightFormat);
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
            $workSheet -> write($row, 1, _FORUMPOSTS, $fieldLeftFormat);
            $workSheet -> write($row++, 2, sizeof($userInfo['communication']['forum_messages']), $fieldRightFormat);
            $workSheet -> write($row, 1, _FORUMLASTMESSAGE, $fieldLeftFormat);
            $workSheet -> write($row++, 2, $userInfo['communication']['forum_last_message'], $fieldRightFormat);
            $workSheet -> write($row, 1, _PERSONALMESSAGES, $fieldLeftFormat);
            $workSheet -> write($row++, 2, sizeof($userInfo['communication']['personal_messages']), $fieldRightFormat);
            $workSheet -> write($row, 1, _MESSAGESFOLDERS, $fieldLeftFormat);
            $workSheet -> write($row++, 2, sizeof($userInfo['communication']['personal_folders']), $fieldRightFormat);
            $workSheet -> write($row, 1, _FILES, $fieldLeftFormat);
            $workSheet -> write($row++, 2, sizeof($userInfo['communication']['files']), $fieldRightFormat);
            $workSheet -> write($row, 1, _FOLDERS, $fieldLeftFormat);
            $workSheet -> write($row++, 2, sizeof($userInfo['communication']['personal_folders']), $fieldRightFormat);
            $workSheet -> write($row, 1, _TOTALSIZE, $fieldLeftFormat);
            $workSheet -> write($row++, 2, sizeof($userInfo['communication']['total_size'])._KB, $fieldRightFormat);
            $workSheet -> write($row, 1, _CHATMESSAGES, $fieldLeftFormat);
            $workSheet -> write($row++, 2, sizeof($userInfo['communication']['chat_messages']), $fieldRightFormat);
            $workSheet -> write($row, 1, _CHATLASTMESSAGE, $fieldLeftFormat);
            $workSheet -> write($row++, 2, $userInfo['communication']['chat_last_message'], $fieldRightFormat);
            $workSheet -> write($row, 1, _COMMENTS, $fieldLeftFormat);
            $workSheet -> write($row++, 2, sizeof($userInfo['communication']['comments']), $fieldRightFormat);
    
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
    
            //lesson info
            $row = 1;
            if (sizeof($userLessonInfo['student']) > 0) {
                $workSheet -> write($row, 4, _LESSONSINFO, $headerFormat);
                $workSheet -> mergeCells($row, 4, $row, 10);
                $workSheet -> setColumn(4, 10, 15);
    
                $row++;
                $workSheet -> write($row, 4, _LESSON, $titleLeftFormat);
                $workSheet -> write($row, 5, _TIME, $titleCenterFormat);
                $workSheet -> write($row, 6, _CONTENT, $titleCenterFormat);
                $workSheet -> write($row, 7, _TESTS, $titleCenterFormat);
                $workSheet -> write($row, 8, _PROJECTS, $titleCenterFormat);
                $workSheet -> write($row, 9, _COMPLETED, $titleCenterFormat);
                $workSheet -> write($row++, 10, _GRADE, $titleCenterFormat);
    
                foreach ($userLessonInfo['student'] as $id => $lesson) {
                    if ($lesson['active']) {
                        $workSheet -> write($row, 4, $lesson['name'], $fieldLeftFormat);
                        $workSheet -> write($row, 5, $lesson['time']['hours']."h ".$lesson['time']['minutes']."' ".$lesson['time']['seconds']."''", $fieldCenterFormat);
                        $workSheet -> write($row, 6, formatScore($lesson['content'])."%", $fieldCenterFormat);
                        $workSheet -> write($row, 7, formatScore($lesson['tests'])."%", $fieldCenterFormat);
                        $workSheet -> write($row, 8, formatScore($lesson['projects'])."%", $fieldCenterFormat);
                        $workSheet -> write($row, 9, $lesson['completed'] ? _YES : _NO, $fieldCenterFormat);
                        $workSheet -> write($row, 10, formatScore($lesson['score'])."%", $fieldCenterFormat);
                        $row++;
                    }
                }
                $row++;
            }
    
            //course users info
            if (sizeof($userCourseInfo['student']) > 0) {
                $workSheet -> write($row, 4, _COURSESINFO, $headerFormat);
                $workSheet -> mergeCells($row, 4, $row, 10);
                $workSheet -> setColumn($row, 10, 15);
    
                $row++;
                $workSheet -> write($row, 4, _COURSE, $titleLeftFormat);
                $workSheet -> write($row, 5, _LESSONS, $titleCenterFormat);
                $workSheet -> write($row, 6, _SCORE, $titleCenterFormat);
                $workSheet -> write($row, 7, _COMPLETED, $titleCenterFormat);
                foreach ($userCourseInfo['student'] as $id => $course) {
                    $row++;
                    $workSheet -> write($row, 4, $course['name'], $fieldLeftFormat);
                    $workSheet -> write($row, 5, $course['lessons'], $fieldCenterFormat);
                    $workSheet -> write($row, 6, formatScore($course['score'])."%", $fieldCenterFormat);
                    $workSheet -> write($row, 7, $course['completed'] ? _YES : _NO, $fieldCenterFormat);
                }
            }
    
            $result       = eF_getTableDataFlat("lessons", "id, name, active");
            $lessonNames  = array_combine($result['id'], $result['name']);
    
            //Done tests sheet
            $doneTests = EfrontStats :: getStudentsDoneTests(false, $infoUser -> user['login']);
            if (sizeof($doneTests[$infoUser -> user['login']]) > 0) {
                $workSheet = & $workBook -> addWorksheet('Tests Info');
                $workSheet -> setInputEncoding('utf-8');
    
                $workSheet -> setColumn(0, 0, 5);
    
                $row = 1;
                $workSheet -> write($row, 1, _TESTSINFORMATION, $headerFormat);
                $workSheet -> mergeCells($row, 1, $row, 3);
                $workSheet -> setColumn(1, 3, 25);
    
                $row++;
                $workSheet -> write($row, 1, _LESSON, $titleLeftFormat);
                $workSheet -> write($row, 2, _TESTNAME, $titleCenterFormat);
                $workSheet -> write($row++, 3, _SCORE, $titleCenterFormat);
    
                $avgScore     = 0;
                foreach ($doneTests[$infoUser -> user['login']] as $contentId => $test) {
                    $workSheet -> write($row, 1, $lessonNames[$test['lessons_ID']], $fieldLeftFormat);
                    $workSheet -> write($row, 2, $test['name'], $fieldCenterFormat);
                    $workSheet -> write($row++, 3, formatScore($test['score'])."%", $fieldCenterFormat);
                    $avgScore += $test['score'];
                }
                $row +=2;
                $workSheet -> write($row, 2, _AVERAGESCORE, $titleLeftFormat);
                $workSheet -> write($row++, 3, formatScore($avgScore / sizeof($doneTests[$infoUser -> user['login']]))."%", $fieldCenterFormat);
            }
    
            //Assigend projects sheet
            $assignedProjects = EfrontStats :: getStudentsAssignedProjects(false, $infoUser -> user['login']);
            if (sizeof($assignedProjects[$infoUser -> user['login']]) > 0) {
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
    
                $avgScore     = 0;
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
            //add a separate sheet for each distinct lesson of that user
            $count = 1;
            foreach ($userLessonInfo['student'] as $id => $lesson) {
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
                $workSheet -> write($row, 0, $lesson['time']['hours']."h ".$lesson['time']['minutes']."' ".$lesson['time']['seconds']."''", $fieldCenterFormat);
                $workSheet -> mergeCells($row, 0, $row++, 1);
    
                $workSheet -> write($row, 0, _STATUS, $headerFormat);
                $workSheet -> mergeCells($row, 0, $row++, 1);
                $workSheet -> write($row, 0, _COMPLETED, $fieldCenterFormat);
                $workSheet -> write($row++, 1, $lesson['completed'] ? _YES : _NO, $fieldCenterFormat);
                $workSheet -> write($row, 0, _GRADE, $fieldCenterFormat);
                $workSheet -> write($row++, 1, formatScore($lesson['score'])."%", $fieldCenterFormat);
    
                $workSheet -> write($row, 0, _CONTENT, $headerFormat);
                $workSheet -> mergeCells($row, 0, $row++, 1);
                $workSheet -> write($row, 0, formatScore($lesson['content'])."%", $fieldCenterFormat);
                $workSheet -> mergeCells($row, 0, $row++, 1);
    
                if (sizeof($doneTests[$id][$infoUser -> user['login']]) > 0) {
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
    
                if (sizeof($assignedProjects[$id][$infoUser -> user['login']]) > 0) {
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
        } 
        else if (isset($_GET['pdf']) && $_GET['pdf'] == 'user') {
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
            $pdf -> Cell(70, 5, _NAME,     0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['general']['name']." ".$userInfo['general']['surname'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _USERTYPE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $roles[$userInfo['general']['user_type']],                        0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _USERROLE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $roles[$userInfo['general']['user_types_ID']],                             0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _LANGUAGE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['general']['language'],                                 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _ACTIVE,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['general']['active'] ? _YES : _NO,                      0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _JOINED,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['general']['joined_str'],                               0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> SetTextColor(0,0,0);
            $pdf -> Cell(100, 10, _USERCOMMUNICATIONINFO, 0, 1, L, 0);
    
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> Cell(70, 5, _FORUMPOSTS,       0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['forum_messages']).' ',    0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _FORUMLASTMESSAGE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['communication']['forum_last_message'],            0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _PERSONALMESSAGES, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['personal_messages']).' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _MESSAGESFOLDERS,  0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['personal_folders']).' ',  0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _FILES,            0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['files']).' ',             0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _FOLDERS,          0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['personal_folders']).' ',  0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _TOTALSIZE,        0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['communication']['total_size'].' '._KB,            0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _CHATMESSAGES,     0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['chat_messages']).' ',     0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _CHATLASTMESSAGE,  0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $userInfo['communication']['chat_last_message'],             0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _COMMENTS,         0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($userInfo['communication']['comments']).' ',          0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> SetTextColor(0,0,0);
            $pdf -> Cell(100, 10, _USERUSAGEINFO, 0, 1, L, 0);
    
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> Cell(90, 5, _LASTLOGIN,         0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, formatTimestamp($userInfo['usage']['last_login']['timestamp'], 'time'),              0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(90, 5, _TOTALLOGINS,       0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, sizeof($userInfo['usage']['logins']),          0, 1, L, 0).' ';$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(90, 5, _MONTHLOGINS,       0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, sizeof($userInfo['usage']['month_logins']),    0, 1, L, 0).' ';$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(90, 5, _WEEKLOGINS,        0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, sizeof($userInfo['usage']['week_logins']),     0, 1, L, 0).' ';$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(90, 5, _MEANDURATION,      0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, $userInfo['usage']['mean_duration']."'",       0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(90, 5, _MONTHMEANDURATION, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, $userInfo['usage']['month_mean_duration']."'", 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(90, 5, _WEEKMEANDURATION,  0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, $userInfo['usage']['week_mean_duration']."'",  0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    
            //lessons page
            if (sizeof($userLessonInfo['student']) > 0) {
                $pdf -> SetTextColor(0, 0, 0);
                $pdf -> AddPage('L');
                $pdf -> SetFont("FreeSerif", "B", 12);
                $pdf -> Cell(60, 12, _LESSONS, 0, 1, L, 0);
    
                $pdf -> SetFont("FreeSerif", "B", 10);
                $pdf -> Cell(100, 7, _LESSON,       0, 0, L, 0);
                $pdf -> Cell(50,  7, _TIMEINLESSON, 0, 0, L, 0);
                $pdf -> Cell(40,  7, _CONTENT,      0, 0, C, 0);
                $pdf -> Cell(40,  7, _TESTS,        0, 0, C, 0);
                $pdf -> Cell(40,  7, _PROJECTS,     0, 1, C, 0);
    
                $pdf -> SetFont("FreeSerif", "", 10);
                $pdf -> SetTextColor(0, 0, 255);
                foreach ($userLessonInfo['student'] as $id => $lesson) {
                    if ($lesson['active']) {
                        $pdf -> Cell(100, 5, $lesson['name'], 0, 0, L, 0);
                        $pdf -> Cell(50, 5, $lesson['time']['hours']."h ".$lesson['time']['minutes']."' ".$lesson['time']['seconds']."''", 0, 0, L, 0);
                        $pdf -> Cell(40, 5, formatScore($lesson['content'])."%",  0, 0, C, 0);
                        $pdf -> Cell(40, 5, formatScore($lesson['tests'])."%",    0, 0, C, 0);
                        $pdf -> Cell(40, 5, formatScore($lesson['projects'])."%", 0, 1, C, 0);
                    }
                }
            }
    
            $result       = eF_getTableDataFlat("lessons", "id, name, active");
            $lessonNames  = array_combine($result['id'], $result['name']);
             
            //tests page
            $doneTests = EfrontStats :: getStudentsDoneTests(false, $infoUser -> user['login']);
            if (sizeof($doneTests[$infoUser -> user['login']]) > 0) {
                $pdf -> SetTextColor(0, 0, 0);
                $pdf -> AddPage('L');
                $pdf -> SetFont("FreeSerif", "B", 12);
                $pdf -> Cell(60, 12, _TESTS, 0, 1, L, 0);
    
                $pdf -> SetFont("FreeSerif", "B", 10);
                $pdf -> Cell(100, 7, _LESSON,   0, 0, L, 0);
                $pdf -> Cell(100, 7, _TESTNAME, 0, 0, L, 0);
                $pdf -> Cell(40,  7, _SCORE,    0, 1, C, 0);
    
                $pdf -> SetFont("FreeSerif", "", 10);
                $pdf -> SetTextColor(0, 0, 255);
                $avgScore = 0;
                foreach ($doneTests[$infoUser -> user['login']] as $test) {
                    $pdf -> Cell(100, 5, $lessonNames[$test['lessons_ID']],     0, 0, L, 0);
                    $pdf -> Cell(100, 5, $test['name'],                         0, 0, L, 0);
                    $pdf -> Cell(40,  5, formatScore($test['score'])."%", 0, 1, C, 0);
                    $avgScore += $test['score'];
                }
                $pdf -> Cell(100, 5, '',            0, 1, L, 0);
                $pdf -> SetFont("FreeSerif", "B", 10);$pdf -> SetTextColor(0, 0, 0);
                $pdf -> Cell(100, 5, '',            0, 0, L, 0);
                $pdf -> Cell(100, 5, _AVERAGESCORE, 0, 0, L, 0);
                $pdf -> Cell(40,  5, formatScore($avgScore / sizeof($doneTests[$infoUser -> user['login']]))."%", 0, 1, C, 0);
            }
    
            //projects page
            $assignedProjects = EfrontStats :: getStudentsAssignedProjects(false, $infoUser -> user['login']);
            if (sizeof($assignedProjects[$infoUser -> user['login']]) > 0) {
                $pdf -> SetTextColor(0, 0, 0);
                $pdf -> AddPage('L');
                $pdf -> SetFont("FreeSerif", "B", 12);
                $pdf -> Cell(60, 12, _PROJECTS, 0, 1, L, 0);
    
                $pdf -> SetFont("FreeSerif", "B", 10);
                $pdf -> Cell(100, 7, _LESSON, 0, 0, L, 0);
                $pdf -> Cell(100, 7, _TITLE,  0, 0, L, 0);
                $pdf -> Cell(40,  7, _GRADE,  0, 1, C, 0);
    
                $pdf -> SetFont("FreeSerif", "", 10);
                $pdf -> SetTextColor(0, 0, 255);
                $avgScore = 0;
                foreach ($assignedProjects[$infoUser -> user['login']] as $project) {
                    $pdf -> Cell(100, 5, $lessonNames[$project['lessons_ID']], 0, 0, L, 0);
                    $pdf -> Cell(100, 5, $project['title'],                    0, 0, L, 0);
                    $pdf -> Cell(40,  5, formatScore($project['grade'])."%",   0, 1, C, 0);
                    $avgScore += $project['grade'];
                }
                $pdf -> Cell(100, 5, '',            0, 1, L, 0);
                $pdf -> SetFont("FreeSerif", "B", 10);$pdf -> SetTextColor(0, 0, 0);
                $pdf -> Cell(100, 5, '',            0, 0, L, 0);
                $pdf -> Cell(100, 5, _AVERAGESCORE, 0, 0, L, 0);
                $pdf -> Cell(40,  5, formatScore($avgScore / sizeof($assignedProjects[$infoUser -> user['login']]))."%", 0, 1, C, 0);
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
            //add a separate sheet for each distinct lesson of that user
            foreach ($userLessonInfo['student'] as $id => $lesson) {
                $pdf -> SetTextColor(0, 0, 0);
                $pdf -> AddPage('L');
                $pdf -> SetFont("FreeSerif", "B", 12);
                $pdf -> Cell(60, 12, $lesson['name'], 0, 1, L, 0);
    
                $pdf -> SetFont("FreeSerif", "B", 10);
                $pdf -> Cell(40, 7, _TIMEINLESSON, 0, 0, L, 0);
                $pdf -> Cell(40, 7, _COMPLETED,  0, 0, L, 0);
                $pdf -> Cell(40, 7, _GRADE,  0, 0, C, 0);
                $pdf -> Cell(40, 7, _CONTENT,  0, 1, C, 0);
    
                $pdf -> SetFont("FreeSerif", "", 10);
                $pdf -> SetTextColor(0, 0, 255);
                $pdf -> Cell(40, 7, $lesson['time']['hours']."h ".$lesson['time']['minutes']."' ".$lesson['time']['seconds']."''", 0, 0, L, 0);
                $pdf -> Cell(40, 7, $lesson['passed'] ? _YES : _NO,  0, 0, L, 0);
                $pdf -> Cell(40, 7, formatScore($lesson['score'])."%",  0, 0, C, 0);
                $pdf -> Cell(40, 7, formatScore($lesson['content'])."%",  0, 1, C, 0);
    
    
                if (sizeof($doneTests[$id][$infoUser -> user['login']]) > 0) {
                    $pdf -> SetTextColor(0, 0, 0);
                    $pdf -> SetFont("FreeSerif", "B", 10);
                    $pdf -> Cell(60, 12, '', 0, 1, L, 0);
                    $pdf -> Cell(60, 7, _TESTS, 0, 1, L, 0);
                    $pdf -> SetTextColor(0, 0, 255);               
                    $avgScore = 0;
                    foreach ($doneTests[$id][$infoUser -> user['login']] as $test) {
                        $pdf -> Cell(60, 7, $test['name'], 0, 0, L, 0);
                        $pdf -> Cell(60, 7, formatScore($test['score'])."%", 0, 1, C, 0);
                        $avgScore += $test['score'];
                    }
                    $pdf -> SetTextColor(0, 0, 0);
                    $pdf -> Cell(60, 7, _AVERAGESCORE, 0, 0, L, 0);
                    $pdf -> Cell(60, 7, formatScore($avgScore / sizeof($doneTests[$id][$infoUser -> user['login']]))."%", 0, 1, C, 0);
                }
    
                if (sizeof($assignedProjects[$id][$infoUser -> user['login']]) > 0) {
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
    } else if ($_GET['option'] == 'lesson') {        
        $smarty -> assign("T_OPTION", $_GET['option']);
        try {
            if ($currentUser -> user['user_type'] == 'administrator') {
                $lessons = EfrontLesson :: getLessons();
            } else if ($isProfessor) {
                $lessons = $currentUser -> getLessons(true, 'professor');
            }
    
            if (sizeof($lessons) == 1) {
                $infoLesson = array_pop($lessons);                        //get the current (first) lesson
                if (!($infoLesson instanceof EfrontLesson)) {
                    $infoLesson = new EfrontLesson($infoLesson['id']);
                }                
            } else if (isset($_GET['sel_lesson']) && in_array($_GET['sel_lesson'], array_keys($lessons))) {
                $infoLesson = new EfrontLesson($_GET['sel_lesson']);
            } else if (isset($_SESSION['s_lessons_ID']) && in_array($_SESSION['s_lessons_ID'], array_keys($lessons))) {
                $infoLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
            }
            $smarty -> assign("T_INFO_LESSON", $infoLesson -> lesson);
    
            //get the lesson information
            if (isset($infoLesson)) {
                try {
                    $lessonInfo = $infoLesson -> getStatisticInformation();
                    $groups     = EfrontGroup :: getGroups();
    
                    $smarty -> assign("T_LESSON_NAME", $infoLesson -> lesson['name']);
                    $smarty -> assign("T_LESSON_ID", $infoLesson -> lesson['id']);
    
                    $smarty -> assign("T_GROUPS", $groups);
                    if (isset($_GET['group_filter'])) {
                        $smarty -> assign("T_GROUP_ID", $_GET['group_filter']);
                    }
                    $smarty -> assign("T_LESSON_INFO", $lessonInfo);
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
    
                if (isset($_GET['group_filter']) && $_GET['group_filter'] != -1) {
                    try {
                        $selectedGroup = new EfrontGroup($_GET['group_filter']);
                        $groupUsers    = $selectedGroup -> getUsers();
                    } catch (Exception $e) {
                        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                        $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                        $message_type = 'failure';
                    }
                }
                     
                try {
                    $smarty -> assign("T_ROLES", EfrontLessonUser :: getLessonsRoles(true));
                    
                    $students = $infoLesson -> getUsers('student');
                    $logins   = array();
                    foreach ($students as $key => $user) {
                        if (isset($groupUsers) && !in_array($user['login'], $groupUsers['student'])) {
                            unset($students[$key]);
                        } else {
                            $logins[] = $user['login'];
                        }
                    }
    
                    $status           = EfrontStats :: getUsersLessonStatus($infoLesson, $logins);
                    $userTimes        = EfrontStats :: getUsersTime($infoLesson -> lesson['id'], $logins);
                    $doneTests        = EfrontStats :: getStudentsDoneTests($infoLesson -> lesson['id'], $logins);
                    $assignedProjects = EfrontStats :: getStudentsAssignedProjects($infoLesson -> lesson['id'], $logins);
                    $studentsPosts    = EfrontStats :: getUsersForumPosts($infoLesson -> lesson['id'], $logins);
                    
                    $studentsInfo = array();
                    foreach ($logins as $login) {
                        $studentsInfo[$login] = array('name'      => $status[$infoLesson -> lesson['id']][$login]['name'],
                                                      'surname'   => $status[$infoLesson -> lesson['id']][$login]['surname'],
                                                      'role'      => $status[$infoLesson -> lesson['id']][$login]['user_type'],
                        							  'active'	  => $status[$infoLesson -> lesson['id']][$login]['active'],
                                                      'time'      => $userTimes[$login],
                                                      'seconds'   => $userTimes[$login]['total_seconds'],
                                                      'content'   => $status[$infoLesson -> lesson['id']][$login]['content_progress'],
                                                      'tests'     => $status[$infoLesson -> lesson['id']][$login]['tests_avg_score'],
                                                      'tests_progress' => $status[$infoLesson -> lesson['id']][$login]['tests_progress'],
                                                      'total_tests'    => sizeof($infoLesson -> getTests()),
                                                      'projects_progress' => $status[$infoLesson -> lesson['id']][$login]['projects_progress'],
                									  'total_projects'    => sizeof($infoLesson -> getProjects()),
                        							  'projects'  => $status[$infoLesson -> lesson['id']][$login]['projects_avg_score'],
                                                      'completed' => $status[$infoLesson -> lesson['id']][$login]['completed'],
                                                      'score'     => $status[$infoLesson -> lesson['id']][$login]['score'],
                                                      'posts'     => $studentsPosts[$login]);
                    }
                    $smarty -> assign("T_STUDENTS_INFO", $studentsInfo);

                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
    
                try {
                    $professors = $infoLesson -> getUsers('professor');
                    $logins = array();
                    foreach ($professors as $key => $professor) {
                        if (isset($groupUsers) && !in_array($professor['login'], $groupUsers['professor'])) {
                            unset($professors[$key]);
                        } else {
                            $logins[] = $professor['login'];
                        }                    
                    }
    
                    $professorTimes    = EfrontStats :: getUsersTime($infoLesson -> lesson['id'], $logins);
                    $professorPosts    = EfrontStats :: getUsersForumPosts($infoLesson -> lesson['id'], $logins);
                    $professorComments = EfrontStats :: getUsersComments($infoLesson -> lesson['id'], $logins);
    
                    $professorsInfo = array();
                    foreach ($logins as $login) {
                        $professorsInfo[$login] = array('name'     => $professors[$login]['name'],
                                                     	'surname'  => $professors[$login]['surname'],
                                                        'role'     => $professors[$login]['role'],
                                                     	'active'   => $professors[$login]['active'],
                        							 	'time'     => $professorTimes[$login],
                                                     	'seconds'  => $professorTimes[$login]['total_seconds'],
                                                     	'posts'    => $professorPosts[$login],
                                                     	'comments' => $professorComments[$login]);
                    }
                    $smarty -> assign("T_PROFESSORS_INFO", $professorsInfo);
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
    
                /*
                 *  Lesson's tests
                 */
                try {
                    $lessonTests = $infoLesson -> getTests(true);
                    $scormTests  = $infoLesson -> getScormTests();
                    if (sizeof($lessonTests) > 0 || sizeof($scormTests) > 0) {
                        $testsInfo = EfrontStats :: getTestInfo(array_keys($lessonTests));
                        if (sizeof($scormTestsInfo = EfrontStats :: getScormTestInfo($scormTests)) > 0) {
                            $testsInfo = $testsInfo + $scormTestsInfo;
                        }
    
                        if (isset($groupUsers)) {
                            foreach ($testsInfo as $id => $test) {
                                foreach ($test['done'] as $key => $value) {
                                    if (!in_array($value['users_LOGIN'], $groupUsers['student'])) {
                                        unset($testsInfo[$id]['done'][$key]);
                                    }
                                }
                            }
                        }
    
                        $smarty -> assign("T_TESTS_INFO", $testsInfo);
                        
                    }
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
    
                /*
                 *      Lesson's questions
                 */
                try {
                    $lessonQuestions = array_keys($infoLesson -> getQuestions());
                    if (sizeof($lessonQuestions) > 0) {
                        $info = EfrontStats :: getQuestionInfo($lessonQuestions);
    
                        $questionsInfo = array();
                        foreach ($info as $id => $questionInfo) {
                            $questionsInfo[$id] = array('text'       => $questionInfo['general']['reduced_text'],
                                                        'type'       => $questionInfo['general']['type'],
                                                        'difficulty' => $questionInfo['general']['difficulty'],
                                                        'times_done' => $questionInfo['done']['times_done'],
                                                        'avg_score'  => round($questionInfo['done']['avg_score'], 2));
                        }
                        $smarty -> assign("T_QUESTIONS_INFORMATION", $questionsInfo);
                    }
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
    
                /*
                 *      Lesson's projects
                 */
                try {
                    $lessonProjects = $infoLesson -> getProjects(true, false, false);
    
                    if (sizeof($lessonProjects) > 0) {
                        $projectsInfo = EfrontStats :: getProjectInfo(array_keys($lessonProjects));
    
                        $smarty -> assign("T_PROJECTS_INFORMATION", $projectsInfo);
                    }
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
    
                /*
                 *  lesson traffic
                 */
                try {
                    if (isset($_GET['from_year'])) { //the admin has chosen a period
                        $from = mktime($_GET['from_hour'], $_GET['from_min'], 0, $_GET['from_month'], $_GET['from_day'], $_GET['from_year']);
                        $to   = mktime($_GET['to_hour'],   $_GET['to_min'],   0, $_GET['to_month'],   $_GET['to_day'],   $_GET['to_year']);
                    } else {
                        $from    = mktime(date("H"), date("i"), 0, date("m"), date("d") - 7, date("Y"));
                        $to      = mktime(date("H"), date("i"), 0, date("m"), date("d"),     date("Y"));
                    }
    
                    $actions = array('login'      => _LOGIN,
                                     'logout'     => _LOGOUT,
                                     'lesson'     => _ACCESSEDLESSON,
                                     'content'    => _ACCESSEDCONTENT,
                                     'tests'      => _ACCESSEDTEST,
                                     'test_begin' => _BEGUNTEST,
                                     'lastmove'   => _NAVIGATEDSYSTEM);
                    $smarty -> assign("T_ACTIONS", $actions);
                    
                    if (isset($_GET['showlog']) && $_GET['showlog'] == "true") {
                        $contentNames = eF_getTableDataFlat("content", "id, name");
                        $contentNames = array_combine($contentNames['id'], $contentNames['name']);
                        $testNames    = eF_getTableDataFlat("tests t, content c", "t.id, c.name", "c.id=t.content_ID");
                        $testNames    = array_combine($testNames['id'], $testNames['name']);
                        $result       = eF_getTableData("logs", "*", "timestamp between $from and $to and lessons_ID='".$infoLesson -> lesson['id']."' order by timestamp desc");
    
                        foreach ($result as $key => $value) {
                            if ($value['action'] == 'content') {
                                $result[$key]['content_name'] = $contentNames[$value['comments']];
                            } else if ($value['action'] == 'tests' || $value['action'] == 'test_begin') {
                                $result[$key]['content_name'] = $testNames[$value['comments']];
                            }
                        }
                        $smarty -> assign("T_LESSON_LOG", $result);
                    }
    
                    $users = eF_getTableDataFlat("users", "login, active");
                    $users = array_combine($users['login'], $users['active']);
                    $traffic['users'] = EfrontStats :: getUsersTime($infoLesson -> lesson['id'], false, $from, $to);
                    foreach ($traffic['users'] as $key => $user) {
                        if (isset($groupUsers) && !in_array($key, $groupUsers['professor']) && !in_array($key, $groupUsers['student'])) {
                            unset($traffic['users'][$key]);
                        } else {
                            $traffic['users'][$key]['active'] = $users[$key];
                        }                                        
                    }
    
                    foreach ($traffic['users'] as $value) {
                        $traffic['total_seconds'] += $value['total_seconds'];
                        $traffic['total_access']  += $value['accesses'];
                    }
                    $traffic['total_time'] = eF_convertIntervalToTime($traffic['total_seconds']);
    
                    $smarty -> assign("T_LESSON_TRAFFIC", $traffic);
                    $smarty -> assign('T_FROM_TIMESTAMP', $from);
                    $smarty -> assign('T_TO_TIMESTAMP',   $to);
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    
        if (isset($_GET['excel']) && $_GET['excel'] == 'lesson') {
            require_once 'Spreadsheet/Excel/Writer.php';
    
            $workBook = new Spreadsheet_Excel_Writer();
            $workBook -> setVersion(8);
            $workBook -> send('export_'.$infoLesson -> lesson['name'].'.xls');
    
            $formatExcelHeaders    = & $workBook -> addFormat(array('Size'   => 14, 'Bold' => 1, 'HAlign' => 'left'));
            $headerFormat          = & $workBook -> addFormat(array('border' => 0, 'bold' => '1', 'size' => '11', 'color' => 'black', 'fgcolor' => 22, 'align' => 'center'));
            $formatContent         = & $workBook -> addFormat(array('HAlign' => 'left', 'Valign' => 'top', 'TextWrap' => 1));
            $headerBigFormat       = & $workBook -> addFormat(array('HAlign' => 'center', 'FgColor' => 22, 'Size' => 16, 'Bold' => 1));
            $titleCenterFormat     = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 11, 'Bold' => 1));
            $titleLeftFormat       = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 11, 'Bold' => 1));
            $fieldLeftFormat       = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10));
            $fieldRightFormat      = & $workBook -> addFormat(array('HAlign' => 'right', 'Size' => 10));
            $fieldCenterFormat     = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 10));
            $fieldLeftBoldFormat   = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10, 'Bold' => 1));
            $fieldLeftItalicFormat = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10, 'Italic' => 1));
    
            //first tab
            $workSheet = & $workBook -> addWorksheet("General Lesson Info");
            $workSheet -> setInputEncoding('utf-8');
    
            $workSheet -> setColumn(0, 0, 5);
    
            //basic info
            $workSheet -> write(1, 1, _BASICINFO, $headerFormat);
            $workSheet -> mergeCells(1, 1, 1, 2);
            $workSheet -> setColumn(1, 2, 30);
    
            $directionName = eF_getTableData("directions", "name", "id=".$infoLesson -> lesson['directions_ID']);
            $languages     = EfrontSystem :: getLanguages(true);
    
            $workSheet -> write(2, 1, _LESSON, $fieldLeftFormat);
            $workSheet -> write(2, 2, $infoLesson -> lesson['name'], $fieldRightFormat);
            $workSheet -> write(3, 1, _CATEGORY, $fieldLeftFormat);
            $workSheet -> write(3, 2, $directionName[0]['name'], $fieldRightFormat);
            $workSheet -> write(4, 1, _STUDENTS, $fieldLeftFormat);
            $workSheet -> writeNumber(4, 2, sizeof($infoLesson -> getUsers('student')), $fieldRightFormat);
            $workSheet -> write(5, 1, _PROFESSORS, $fieldLeftFormat);
            $workSheet -> writeNumber(5, 2, sizeof($infoLesson -> getUsers('professor')), $fieldRightFormat);
            $workSheet -> write(6, 1, _PRICE, $fieldLeftFormat);
            $workSheet -> write(6, 2, $infoLesson -> lesson['price'].' '.$GLOBALS['CURRENCYNAMES'][$GLOBALS['configuration']['currency']], $fieldRightFormat);
            $workSheet -> write(7, 1, _LANGUAGE, $fieldLeftFormat);
            $workSheet -> write(7, 2, $languages[$infoLesson -> lesson['languages_NAME']], $fieldRightFormat);
            $workSheet -> write(8, 1, _ACTIVE, $fieldLeftFormat);
            $workSheet -> write(8, 2, $infoLesson -> lesson['active'] ? _YES : _NO, $fieldRightFormat);
    
            //participation info
            $workSheet -> write(9, 1, _LESSONPARTICIPATIONINFO, $headerFormat);
            $workSheet -> mergeCells(9, 1, 9, 2);
            //$workSheet -> setColumn(9, 9, 30);
    
            $workSheet -> write(10, 1, _COMMENTS, $fieldLeftFormat);
            $workSheet -> write(10, 2, $lessonInfo['comments'], $fieldRightFormat);
            $workSheet -> write(11, 1, _MESSAGES, $fieldLeftFormat);
            $workSheet -> write(11, 2, $lessonInfo['messages'], $fieldRightFormat);
            $workSheet -> write(12, 1, _CHATMESSAGES, $fieldLeftFormat);
            $workSheet -> write(12, 2, $lessonInfo['chatmessages'], $fieldRightFormat);
    
            //lesson content info
            $workSheet -> write(14, 1, _LESSONCONTENTINFO, $headerFormat);
            $workSheet -> mergeCells(14, 1, 14, 2);
            //$workSheet -> setColumn(14, 14, 30);
    
            $workSheet -> write(15, 1, _THEORY, $fieldLeftFormat);
            $workSheet -> write(15, 2, $lessonInfo['theory'], $fieldRightFormat);
            $workSheet -> write(16, 1, _PROJECTS, $fieldLeftFormat);
            $workSheet -> write(16, 2, $lessonInfo['projects'], $fieldRightFormat);
            $workSheet -> write(17, 1, _EXAMPLES, $fieldLeftFormat);
            $workSheet -> write(17, 2, $lessonInfo['examples'], $fieldRightFormat);
            $workSheet -> write(18, 1, _TESTS, $fieldLeftFormat);
            $workSheet -> write(18, 2, $lessonInfo['tests'], $fieldRightFormat);
    
            $workSheet -> setColumn(3, 3, 5);
    
            //lesson users info
            $workSheet -> write(1, 4, _USERSINFO, $headerFormat);
            $workSheet -> mergeCells(1, 4, 1, 11);
            $workSheet -> setColumn(4, 10, 15);
    
            $workSheet -> write(2, 4, _LOGIN, $titleLeftFormat);
            $workSheet -> write(2, 5, _LESSONROLE, $titleLeftFormat);
            $workSheet -> write(2, 6, _TIME, $titleCenterFormat);
            $workSheet -> write(2, 7, _CONTENT, $titleCenterFormat);
            $workSheet -> write(2, 8, _TESTS, $titleCenterFormat);
            $workSheet -> write(2, 9, _PROJECTS, $titleCenterFormat);
            $workSheet -> write(2, 10, _COMPLETED, $titleCenterFormat);
            $workSheet -> write(2, 11, _GRADE, $titleCenterFormat);
    
            $roles = EfrontLessonUser :: getLessonsRoles(true);
            
            $row = 3;
            foreach ($students as $login => $user) {
                $workSheet -> write($row, 4, $login, $fieldLeftFormat);
                $workSheet -> write($row, 5, $roles[$studentsInfo[$login]['role']], $fieldLeftFormat);
                $workSheet -> write($row, 6, $studentsInfo[$login]['time']['hours']."h ".$studentsInfo[$login]['time']['minutes']."' ".$studentsInfo[$login]['time']['seconds']."''", $fieldCenterFormat);
                $workSheet -> write($row, 7, formatScore($studentsInfo[$login]['content'])."%", $fieldCenterFormat);
                $workSheet -> write($row, 8, formatScore($studentsInfo[$login]['tests'])."%", $fieldCenterFormat);
                $workSheet -> write($row, 9, formatScore($studentsInfo[$login]['projects'])."%", $fieldCenterFormat);
                $workSheet -> write($row, 10, $studentsInfo[$login]['completed'] ? _YES : _NO, $fieldCenterFormat);
                $workSheet -> write($row, 11, formatScore($studentsInfo[$login]['score'])."%", $fieldCenterFormat);
                $row++;
            }
            $row += 2;
    
            //lesson professors info
            $workSheet -> write($row, 4, _PROFESSORSINFO, $headerFormat);
            $workSheet -> mergeCells($row, 4, $row++, 11);
            $workSheet -> write($row, 4, _LOGIN, $titleLeftFormat);
            $workSheet -> write($row, 5, _LESSONROLE, $titleLeftFormat);
            $workSheet -> write($row++, 6, _TIME, $titleCenterFormat);
            foreach ($professors as $login => $user) {
                $workSheet -> write($row, 4, $login, $fieldLeftFormat);
                $workSheet -> write($row, 5, $roles[$professorsInfo[$login]['role']], $fieldLeftFormat);
                $workSheet -> write($row, 6, $professorsInfo[$login]['time']['hours']."h ".$professorsInfo[$login]['time']['minutes']."' ".$professorsInfo[$login]['time']['seconds']."''", $fieldCenterFormat);
                $row++;
            }
    
            //Sheet with lesson's tests and questions
            if (isset($testsInfo)) {
                $workSheet = & $workBook -> addWorksheet('Tests Info');
                $workSheet -> setInputEncoding('utf-8');
    
                $workSheet -> setColumn(0, 0, 5);
    
                $workSheet -> write(1, 1, _TESTSINFORMATION, $headerFormat);
                $workSheet -> mergeCells(1, 1, 1, 2);
                $workSheet -> setColumn(1, 1, 30);
                $row = 3;
                foreach ($testsInfo as $id => $info) {
                    $avgScore = array();
                    $workSheet -> write($row, 1, $info['general']['name'], $titleLeftFormat);
                    $workSheet -> mergeCells($row, 1, $row, 2);
                    $row++;
                    foreach ($info['done'] as $results) {
                        $workSheet -> write($row, 1, $results['users_LOGIN'], $fieldLeftFormat);
                        $workSheet -> write($row++, 2, formatScore(round($results['score'], 2))."%", $fieldCenterFormat);
                        $avgScore[] = $results['score'];
                    }
                    if (sizeof($avgScore) > 0) {
                        $workSheet -> write($row, 1, _AVERAGESCORE, $fieldLeftBoldFormat);
                        $workSheet -> write($row++, 2, formatScore(round(array_sum($avgScore) / sizeof($avgScore), 2))."%", $fieldCenterFormat);
                    } else {
                        $workSheet -> write($row++, 1, _NODATAFOUND, $fieldLeftItalicFormat);
                    }
                    $row++;
                }            
            }
    
            if (sizeof($lessonQuestions) > 0) {
                $workSheet -> setColumn(3, 3, 3);
                $workSheet -> write(1, 4, _QUESTIONSINFORMATION, $headerFormat);
                $workSheet -> mergeCells(1, 4, 1, 8);
                $workSheet -> setColumn(4, 4, 30);
                $workSheet -> setColumn(5, 8, 20);
                $row = 3;
    
                $workSheet -> write($row, 4, _QUESTION, $titleLeftFormat);
                $workSheet -> write($row, 5, _QUESTIONTYPE, $titleCenterFormat);
                $workSheet -> write($row, 6, _DIFFICULTY, $titleCenterFormat);
                $workSheet -> write($row, 7, _TIMESDONE, $titleCenterFormat);
                $workSheet -> write($row++, 8, _AVERAGESCORE, $titleCenterFormat);
    
                $questionShortHands = array('multiple_one'  => 'MC',
                                            'multiple_many' => 'MCMA',
                                            'match'         => 'MA',
                                            'empty_spaces'  => 'FB',
                                            'raw_text'      => 'OA',
                                            'true_false'    => 'YN');
    
                foreach ($questionsInfo as $id => $questionInfo) {
                    $workSheet -> write($row, 4, $questionInfo['text'], $fieldLeftFormat);
                    $workSheet -> write($row, 5, $questionShortHands[$questionInfo['type']], $fieldCenterFormat);
                    $workSheet -> write($row, 6, Question :: $questionDifficulties[$questionInfo['difficulty']], $fieldCenterFormat);
                    $workSheet -> write($row, 7, $questionInfo['times_done'], $fieldCenterFormat);
                    $workSheet -> write($row, 8, formatScore($questionInfo['avg_score'])."%", $fieldCenterFormat);
                    $row++;
                }
    
                $row++;
                $workSheet -> write($row++, 4, _MCEXPLANATION, $fieldLeftFormat);
                $workSheet -> write($row++, 4, _MCMAEXPLANATION, $fieldLeftFormat);
                $workSheet -> write($row++, 4, _MAEXPLANATION, $fieldLeftFormat);
                $workSheet -> write($row++, 4, _FBEXPLANATION, $fieldLeftFormat);
                $workSheet -> write($row++, 4, _OAEXPLANATION, $fieldLeftFormat);
                $workSheet -> write($row, 4, _YNEXPLANATION, $fieldLeftFormat);
    
            }
    
            //Sheet with tests matrix
            if (isset($testsInfo)) {
                $workSheet = & $workBook -> addWorksheet('Tests Matrix');
                $workSheet -> setInputEncoding('utf-8');
    
                $workSheet -> setColumn(0, 0, 40);
                $workSheet -> write(0, 0, _TESTSMATRIX, $headerFormat);
    
                $rows   = array();
                $row    = 2;
                $column = 0;
                foreach ($students as $login => $user) {
                    $rows[$login] = $row;
                    $workSheet -> write($row++, $column, $login." (".$user['name']." ".$user['surname'].")", $fieldLeftFormat);
                }
                $row    = 1;
                $column = 1;
                foreach ($testsInfo as $id => $info) {
                    $row = 1;
                    $workSheet -> setColumn($column, $column, 20);
                    $workSheet -> write($row++, $column, $info['general']['name'], $fieldCenterFormat);
                    foreach ($info['done'] as $results) {
                        $workSheet -> write($rows[$results['users_LOGIN']], $column, formatScore(round($results['score'], 2))."%", $fieldCenterFormat);
                    }
                    $column++;
                }                        
            }
            
            //Sheet with lesson's projects
            if (sizeof($lessonProjects) > 0) {
                $workSheet = & $workBook -> addWorksheet('Projects Info');
                $workSheet -> setInputEncoding('utf-8');
    
                $workSheet -> setColumn(0, 0, 5);
    
                $workSheet -> write(1, 1, _PROJECTSINFORMATION, $headerFormat);
                $workSheet -> mergeCells(1, 1, 1, 3);
                $workSheet -> setColumn(1, 1, 30);
                $row = 3;
                foreach ($lessonProjects as $id => $project) {
                    $avgScore = array();
                    $workSheet -> write($row, 1, $project -> project['title'], $titleLeftFormat);
                    $workSheet -> mergeCells($row, 1, $row, 3);
                    $row++;
                    foreach ($projectsInfo[$id]['done'] as $results) {
                        $workSheet -> write($row, 1, $results['users_LOGIN'], $fieldLeftFormat);
                        $workSheet -> write($row, 2, formatScore(round($results['grade'], 2))."%", $fieldCenterFormat);
                        $workSheet -> write($row++, 3, $results['comments'], $fieldCenterFormat);
                        $avgScore[] = formatScore($results['grade'])."%";
                    }
                    if (sizeof($avgScore) > 0) {
                        $workSheet -> write($row, 1, _AVERAGESCORE, $fieldLeftBoldFormat);
                        $workSheet -> write($row++, 2, formatScore(round(array_sum($avgScore) / sizeof($avgScore), 2))."%", $fieldCenterFormat);
                    } else {
                        $workSheet -> write($row++, 1, _NODATAFOUND, $fieldLeftItalicFormat);
                    }
                    $row++;
                }
                
                
                //Sheet with projects matrix
                $workSheet = & $workBook -> addWorksheet('Projects Matrix');
                $workSheet -> setInputEncoding('utf-8');
    
                $workSheet -> setColumn(0, 0, 40);
                $workSheet -> write(0, 0, _PROJECTSMATRIX, $headerFormat);
    
                $rows   = array();
                $row    = 2;
                $column = 0;
                foreach ($students as $login => $user) {
                    $rows[$login] = $row;
                    $workSheet -> write($row++, $column, $login." (".$user['name']." ".$user['surname'].")", $fieldLeftFormat);
                }
                $row    = 1;
                $column = 1;
                foreach ($lessonProjects as $id => $project) {
                    $row = 1;
                    $workSheet -> setColumn($column, $column, 20);
                    $workSheet -> write($row++, $column, $project -> project['title'], $fieldCenterFormat);
                    foreach ($projectsInfo[$id]['done'] as $results) {
                        if (in_array($results['users_LOGIN'], array_keys($rows))) {
                            $workSheet -> write($rows[$results['users_LOGIN']], $column, formatScore(round($results['grade'], 2))."%", $fieldCenterFormat);
                        }
                    }
                    $column++;
                }                        
            }        
            //add a separate sheet for each distinct student of that lesson
            //$doneTests        = EfrontStats :: getStudentsDoneTests($infoLesson -> lesson['id']);
            $assignedProjects = EfrontStats :: getStudentsAssignedProjects($infoLesson -> lesson['id']);
            foreach ($students as $login => $user) {
                $workSheet = & $workBook -> addWorksheet($login);
                $workSheet -> setInputEncoding('utf-8');
    
                $row = 0;
                $workSheet -> write($row, 0, $infoLesson -> lesson['name'], $headerBigFormat);
                $workSheet -> mergeCells($row, 0, $row++, 9);
                $workSheet -> write($row, 0, $studentsInfo[$login]['name']." ".$studentsInfo[$login]['surname'].' ('.$login.')', $fieldCenterFormat);
                $workSheet -> mergeCells($row, 0, $row++, 9);
    
                $workSheet -> setColumn(0, 0, 40);
    
                $workSheet -> write(++$row, 0, _LESSONROLE, $headerFormat);
                $workSheet -> mergeCells($row, 0, $row, 1);
                $workSheet -> write(++$row, 0, $roles[$studentsInfo[$login]['role']], $fieldCenterFormat);
                $workSheet -> mergeCells($row, 0, $row, 1);
                $workSheet -> write(++$row, 0, _TIMEINLESSON, $headerFormat);
                $workSheet -> mergeCells($row, 0, $row, 1);
                $workSheet -> write(++$row, 0, $studentsInfo[$login]['time']['hours']."h ".$studentsInfo[$login]['time']['minutes']."' ".$studentsInfo[$login]['time']['seconds']."''", $fieldCenterFormat);
                $workSheet -> mergeCells($row, 0, $row, 1);
                $workSheet -> write(++$row, 0, _STATUS, $headerFormat);
                $workSheet -> mergeCells($row, 0, $row, 1);
                $workSheet -> write(++$row, 0, _COMPLETED, $fieldCenterFormat);
                $workSheet -> write($row, 1, $studentsInfo[$login]['completed'] ? _YES : _NO, $fieldCenterFormat);
                $workSheet -> write(++$row, 0, _GRADE, $fieldCenterFormat);
                $workSheet -> write($row, 1, formatScore($studentsInfo[$login]['score'])."%", $fieldCenterFormat);
    
                $workSheet -> write(++$row, 0, _CONTENT, $headerFormat);
                $workSheet -> mergeCells($row, 0, $row, 1);
                $workSheet -> write(++$row, 0, formatScore($studentsInfo[$login]['content'])."%", $fieldCenterFormat);
                $workSheet -> mergeCells($row, 0, $row, 1);
    
                $row++;
                if (sizeof($doneTests[$login]) > 0) {
                    $avgScore = array();
                    $workSheet -> write($row, 0, _TESTS, $headerFormat);
                    $workSheet -> mergeCells($row, 0, $row++, 1);
                    foreach ($doneTests[$login] as $test) {
                        $workSheet -> write($row, 0, $test['name'], $fieldLeftFormat);
                        $workSheet -> write($row, 1, formatScore($test['score'])."%", $fieldCenterFormat);
                        $avgScore[] = $test['score'];
                        $row++;
                    }
                    $row +=2;
                    $workSheet -> write($row, 0, _AVERAGESCORE, $titleLeftFormat);
                    $workSheet -> write($row++, 1, formatScore(array_sum($avgScore) / sizeof($avgScore))."%", $fieldCenterFormat);
                }
    
                if (sizeof($assignedProjects[$login]) > 0) {
                    $workSheet -> write($row, 0, _PROJECTS, $headerFormat);
                    $workSheet -> mergeCells($row, 0, $row, 1);
                    $row++;
                    foreach ($assignedProjects[$login] as $project) {
                        $workSheet -> write($row, 0, $project['title'], $fieldCenterFormat);
                        $workSheet -> write($row, 1, formatScore($project['grade'])."%", $fieldCenterFormat);
                        $workSheet -> write($row, 2, $project['comments'], $fieldCenterFormat);
                        $row++;
                    }
                }
            }
    
            $workBook -> close();
            exit(0);
        } else if (isset($_GET['pdf']) && $_GET['pdf'] == 'lesson') {
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
            $pdf -> setHeaderData('','','', _STATISTICSFORLESSON.": ".$infoLesson -> lesson['name']);
    
            //initialize document
            $pdf -> AliasNbPages();
            $pdf -> AddPage();
    
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(100, 10, _BASICINFO, 0, 1, L, 0);
    
            $directionName = eF_getTableData("directions", "name", "id=".$infoLesson -> lesson['directions_ID']);
            $languages     = EfrontSystem :: getLanguages(true);
    
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> Cell(70, 5, _LESSON,        0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $infoLesson -> lesson['name'],                           0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _CATEGORY,      0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $directionName[0]['name'],                               0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _STUDENTS,      0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($infoLesson -> getUsers('student')).' ',          0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _PROFESSORS,    0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($infoLesson -> getUsers('professor')).' ',        0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _PRICE,         0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $infoLesson -> lesson['price'].' '.$GLOBALS['CURRENCYNAMES'][$GLOBALS['configuration']['currency']], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _LANGUAGE,      0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $languages[$infoLesson -> lesson['languages_NAME']],     0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _ACTIVENEUTRAL, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $infoLesson -> lesson['active'] ? _YES : _NO,            0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> SetTextColor(0,0,0);
            $pdf -> Cell(100, 10, _LESSONPARTICIPATIONINFO, 0, 1, L, 0);
    
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> Cell(70, 5, _COMMENTS,     0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $lessonInfo['comments'].' ',     0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _MESSAGES,     0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $lessonInfo['messages'].' ',     0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _CHATMESSAGES, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $lessonInfo['chatmessages'].' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> SetTextColor(0,0,0);
            $pdf -> Cell(100, 10, _LESSONCONTENTINFO, 0, 1, L, 0);
    
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> Cell(90, 5, _THEORY,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, $lessonInfo['theory'].' ',   0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(90, 5, _PROJECTS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, $lessonInfo['projects'].' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(90, 5, _EXAMPLES, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, $lessonInfo['examples'].' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(90, 5, _TESTS,    0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(40, 5, $lessonInfo['tests'].' ',    0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    
            //lessons page
            $pdf -> SetTextColor(0, 0, 0);
            $pdf -> AddPage('L');
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> Cell(60, 12, _USERSINFO, 0, 1, L, 0);
    
            $pdf -> SetFont("FreeSerif", "B", 10);
            $pdf -> Cell(70, 7, _NAME,     0, 0, L, 0);
            //$pdf -> Cell(50, 7, _LESSONROLE,0, 0, L, 0);
            $pdf -> Cell(30, 7, _TIME,      0, 0, L, 0);
            $pdf -> Cell(30, 7, _CONTENT,   0, 0, C, 0);
            $pdf -> Cell(30, 7, _TESTS,     0, 0, C, 0);
            $pdf -> Cell(30, 7, _PROJECTS,  0, 0, C, 0);
            $pdf -> Cell(30, 7, _COMPLETED, 0, 0, C, 0);
            $pdf -> Cell(30, 7, _GRADE,     0, 1, C, 0);
    
            $roles = EfrontLessonUser :: getLessonsRoles(true);
            
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> SetTextColor(0, 0, 255);
            foreach ($students as $login => $user) {
                $pdf -> Cell(70, 7, $studentsInfo[$login]['name'].' '.$studentsInfo[$login]['surname'].' ('.$login.')', 0, 0, L, 0);
                //$pdf -> Cell(50, 7, $roles[$studentsInfo[$login]['role']],   0, 0, L, 0);
                $pdf -> Cell(30, 7, $studentsInfo[$login]['time']['hours']."h ".$studentsInfo[$login]['time']['minutes']."' ".$studentsInfo[$login]['time']['seconds']."''", 0, 0, L, 0);
                $pdf -> Cell(30, 7, formatScore($studentsInfo[$login]['content'])."%",   0, 0, C, 0);
                $pdf -> Cell(30, 7, formatScore($studentsInfo[$login]['tests'])."%",     0, 0, C, 0);
                $pdf -> Cell(30, 7, formatScore($studentsInfo[$login]['projects'])."%",  0, 0, C, 0);
                $pdf -> Cell(30, 7, formatScore($studentsInfo[$login]['completed'])."%", 0, 0, C, 0);
                $pdf -> Cell(30, 7, formatScore($studentsInfo[$login]['score'])."%",     0, 1, C, 0);
            }
    
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(60, 12, _PROFESSORSINFO, 0, 1, L, 0);
    
            $pdf -> SetFont("FreeSerif", "B", 10);
            $pdf -> Cell(100, 7, _NAME, 0, 0, L, 0);
            $pdf -> Cell(60,  7, _LESSONROLE,0, 0, L, 0);
            $pdf -> Cell(60,  7, _TIME, 0, 1, L, 0);
    
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> SetTextColor(0, 0, 255);
            foreach ($professors as $login => $user) {
                $pdf -> Cell(100, 7, $professorsInfo[$login]['name'].' '.$professorsInfo[$login]['surname'].' ('.$login.')', 0, 0, L, 0);
                $pdf -> Cell(60, 7, $roles[$professorsInfo[$login]['role']],   0, 0, L, 0);
                $pdf -> Cell(60, 5, $professorsInfo[$login]['time']['hours']."h ".$professorsInfo[$login]['time']['minutes']."' ".$professorsInfo[$login]['time']['seconds']."''", 0, 1, L, 0);
            }
    
            //Page with lesson's tests and questions
            if (isset($testsInfo)) {
                $pdf -> SetTextColor(0, 0, 0);
                $pdf -> AddPage('L');
                $pdf -> SetFont("FreeSerif", "B", 12);
                $pdf -> Cell(60, 12, _TESTSINFORMATION, 0, 1, L, 0);
    
                foreach ($testsInfo as $id => $info) {
                    $pdf -> SetFont("FreeSerif", "B", 10);
                    $pdf -> Cell(60, 12, $info['general']['name'], 0, 1, L, 0);
    
                    $avgScore = array();
                    $pdf -> SetTextColor(0, 0, 255);
                    foreach ($info['done'] as $results) {
                        $pdf -> Cell(30, 7, $results['users_LOGIN'], 0, 0, L, 0);
                        $pdf -> Cell(30, 7, formatScore(round($results['score'], 2))."%", 0, 1, L, 0);
                        $avgScore[] = $results['score'];
                    }
                    $pdf -> SetTextColor(0, 0, 0);
                    if (sizeof($avgScore) > 0) {
                        $pdf -> Cell(30, 7, _AVERAGESCORE, 0, 0, L, 0);
                        $pdf -> Cell(30, 7, formatScore(round(array_sum($avgScore) / sizeof($avgScore), 2))."%", 0, 1, L, 0);
                    } else {
                        $pdf -> Cell(30, 7, _NODATAFOUND, 0, 1, L, 0);
                    }
                    $row++;
                }
            }
    
            if (sizeof($lessonQuestions) > 0) {
                $questionShortHands = array('multiple_one'  => 'MC',
                                                'multiple_many' => 'MCMA',
                                                'match'         => 'MA',
                                                'empty_spaces'  => 'FB',
                                                'raw_text'      => 'OA',
                                                'true_false'    => 'YN');
    
                $pdf -> SetTextColor(0, 0, 0);
                $pdf -> AddPage('L');
                $pdf -> SetFont("FreeSerif", "B", 12);
                $pdf -> Cell(60, 12, _QUESTIONSINFORMATION, 0, 1, L, 0);
    
                $pdf -> SetFont("FreeSerif", "B", 10);
                $pdf -> Cell(100, 12, _QUESTION, 0, 0, L, 0);
                $pdf -> Cell(30, 12, _QUESTIONTYPE, 0, 0, C, 0);
                $pdf -> Cell(30, 12, _DIFFICULTY, 0, 0, L, 0);
                $pdf -> Cell(30, 12, _TIMESDONE, 0, 0, C, 0);
                $pdf -> Cell(30, 12, _AVERAGESCORE, 0, 1, C, 0);
    
                $pdf -> SetTextColor(0, 0, 255);
                foreach ($questionsInfo as $id => $questionInfo) {
                    $pdf -> Cell(100, 7, $questionInfo['text'], 0, 0, L, 0);
                    $pdf -> Cell(30, 7, $questionShortHands[$questionInfo['type']], 0, 0, C, 0);
                    $pdf -> Cell(30, 7, Question :: $questionDifficulties[$questionInfo['difficulty']], 0, 0, L, 0);
                    $pdf -> Cell(30, 7, $questionInfo['times_done'], 0, 0, C, 0);
                    $pdf -> Cell(30, 7, formatScore($questionInfo['avg_score'])."%", 0, 1, C, 0);
                }
    
                $pdf -> SetTextColor(0, 0, 0);
                $pdf -> Cell(140, 7, _MCEXPLANATION, 0, 1, L, 0);
                $pdf -> Cell(140, 7, _MCMAEXPLANATION, 0, 1, L, 0);
                $pdf -> Cell(140, 7, _MAEXPLANATION, 0, 1, L, 0);
                $pdf -> Cell(140, 7, _FBEXPLANATION, 0, 1, L, 0);
                $pdf -> Cell(140, 7, _OAEXPLANATION, 0, 1, L, 0);
                $pdf -> Cell(140, 7, _YNEXPLANATION, 0, 1, L, 0);
            }
    
            //Page with lesson's projects
            if (sizeof($lessonProjects) > 0) {
                $pdf -> SetTextColor(0, 0, 0);
                $pdf -> AddPage('L');
                $pdf -> SetFont("FreeSerif", "B", 12);
                $pdf -> Cell(60, 12, _PROJECTSINFORMATION, 0, 1, L, 0);
    
                foreach ($lessonProjects as $id => $project) {
                    $pdf -> SetFont("FreeSerif", "B", 10);
                    $pdf -> Cell(60, 12, $project -> project['title'], 0, 1, L, 0);
    
                    $avgScore = array();
                    $pdf -> SetTextColor(0, 0, 255);
                    foreach ($projectsInfo[$id]['done'] as $results) {
                        $pdf -> Cell(30, 7, $results['users_LOGIN'], 0, 0, L, 0);
                        $pdf -> Cell(30, 7, formatScore(round($results['grade'], 2))."%", 0, 0, L, 0);
                        $pdf -> Cell(30, 7, $results['comments'], 0, 1, L, 0);
                        $avgScore[] = $results['grade'];
                    }
                    $pdf -> SetTextColor(0, 0, 0);
                    if (sizeof($avgScore) > 0) {
                        $pdf -> Cell(30, 7, _AVERAGESCORE, 0, 0, L, 0);
                        $pdf -> Cell(30, 7, formatScore(round(array_sum($avgScore) / sizeof($avgScore), 2))."%", 0, 1, L, 0);
                    } else {
                        $workSheet -> write($row++, 1, _NODATAFOUND, $fieldLeftItalicFormat);
                    }
                    $row++;
                }
            }
    
            //add a separate page for each distinct student of that lesson
            $doneTests        = EfrontStats :: getStudentsDoneTests($infoLesson -> lesson['id']);
            $assignedProjects = EfrontStats :: getStudentsAssignedProjects($infoLesson -> lesson['id']);
            foreach ($students as $login => $user) {
                $pdf -> SetTextColor(0, 0, 0);
                $pdf -> AddPage();
                $pdf -> SetFont("FreeSerif", "B", 12);
                $pdf -> Cell(60, 12, $infoLesson -> lesson['name'], 0, 1, L, 0);
                $pdf -> SetFont("FreeSerif", "B", 10);
                $pdf -> Cell(60, 12, $studentsInfo[$login]['name']." ".$studentsInfo[$login]['surname'].' ('.$login.')', 0, 1, L, 0);
    
                $pdf -> SetFont("FreeSerif", "", 10);
                $pdf -> Cell(60, 5, _LESSONROLE,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(60, 5, $roles[$studentsInfo[$login]['role']], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
                $pdf -> Cell(60, 5, _TIMEINLESSON, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(60, 5, $studentsInfo[$login]['time']['hours']."h ".$studentsInfo[$login]['time']['minutes']."' ".$studentsInfo[$login]['time']['seconds']."''", 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
                $pdf -> Cell(60, 5, _COMPLETED,    0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(60, 5, $studentsInfo[$login]['completed'] ? _YES : _NO, 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
                $pdf -> Cell(60, 5, _GRADE,        0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(60, 5, formatScore($studentsInfo[$login]['score'])."%", 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    
                $pdf -> Cell(60, 15, '', 0, 1, L, 0);
    
                $pdf -> SetFont("FreeSerif", "B", 10);
                $pdf -> Cell(40, 7, _CONTENT, 0, 0, L, 0);
                $pdf -> Cell(40, 7, _TESTS, 0, 0, L, 0);
                $pdf -> Cell(40, 7, _PROJECTS, 0, 1, L, 0);
    
                $pdf -> SetFont("FreeSerif", "", 10);
                $pdf -> SetTextColor(0, 0, 255);
                $pdf -> Cell(40, 5, formatScore($studentsInfo[$login]['content'])."%", 0, 0, L, 0);
                $pdf -> Cell(40, 5, formatScore($studentsInfo[$login]['tests'])."%", 0, 0, L, 0);
                $pdf -> Cell(40, 5, formatScore($studentsInfo[$login]['projects'])."%", 0, 1, L, 0);
    
                if (sizeof($doneTests[$login]) > 0) {
                    $pdf -> Cell(60, 15, '', 0, 1, L, 0);    //Empty line
                    $pdf -> SetFont("FreeSerif", "B", 10);
                    $pdf -> SetTextColor(0, 0, 0);
                    $pdf -> Cell(60, 7, _TESTS, 0, 1, L, 0);
                    $pdf -> SetFont("FreeSerif", "", 10);
                    $pdf -> SetTextColor(0, 0, 255);
                    foreach ($doneTests[$login] as $test) {
                        $pdf -> Cell(60, 5, $test['name'], 0, 0, L, 0);
                        $pdf -> Cell(10, 5, formatScore(round($test['score'], 2))."%", 0, 1, L, 0);
                    }
                }
    
                if (sizeof($assignedProjects[$login]) > 0) {
                    $pdf -> Cell(60, 15, '', 0, 1, L, 0);    //Empty line
                    $pdf -> SetFont("FreeSerif", "B", 10);
                    $pdf -> SetTextColor(0, 0, 0);
                    $pdf -> Cell(60, 7, _PROJECTS, 0, 1, L, 0);
                    $pdf -> SetFont("FreeSerif", "", 10);
                    $pdf -> SetTextColor(0, 0, 255);
                    foreach ($assignedProjects[$login] as $project) {
                        $pdf -> Cell(60, 5, $project['title'], 0, 0, L, 0);
                        $pdf -> Cell(20, 5, formatScore($project['grade'])."%", 0, 0, L, 0);
                        $pdf -> Cell(150, 5, $project['comments'], 0, 1, L, 0);
                    }
                }
            }
    
            $pdf -> Output();
            exit(0);
        }
    } else if ($_GET['option'] == 'course') {
        $smarty -> assign("T_OPTION", $_GET['option']);
    
        if (isset($_GET['sel_course'])) {
            $course_id     = $_GET['sel_course'];
            $infoCourse = new EfrontCourse($course_id);
            $smarty -> assign("T_COURSE_NAME", $infoCourse -> course['name']);
            $smarty -> assign("T_COURSE_ID", $course_id);
    
            $roles = EfrontLessonUser :: getLessonsRoles();
            $smarty -> assign("T_ROLES", EfrontLessonUser :: getLessonsRoles(true));
            
            $basicInfo         = array();
            $basicInfo['id']   = $course_id;
            $basicInfo['name'] = $infoCourse -> course['name'];
            $result             = eF_getTableData("directions", "name", "id=".$infoCourse -> course['directions_ID']);
            if (sizeof($result) > 0) {
                $basicInfo['direction'] = $result[0]['name'];
            }
            $basicInfo['lessons']    = sizeof($infoCourse -> getLessons(false));
            $basicInfo['professors'] = 0;
            $basicInfo['students']   = 0;
            $courseUsers = $infoCourse -> getUsers(false);
            $studentLogins   = array();
            $professorLogins = array();
            foreach ($courseUsers as $login => $user) {
                if ($roles[$user['role']] == 'student') {
                    $basicInfo['students']++;
                    $studentLogins[] = $login;
                } else if ($roles[$user['role']] == 'professor') {
                    $basicInfo['professors']++;
                    $professorLogins[] = $login;
                }
            }
            
            $languages = EfrontSystem :: getLanguages(true);
            $basicInfo['language'] = $languages[$infoCourse -> course['languages_NAME']];
            $basicInfo['price']    = $infoCourse -> course['price'].' '.$GLOBALS['CURRENCYSYMBOLS'][$GLOBALS['configuration']['currency']];
    
            $smarty -> assign("T_COURSE_INFO", $basicInfo);
    
            $status    = EfrontStats :: getUsersCourseStatus($infoCourse, $studentLogins);
            $userStats = array();
            foreach ($studentLogins as $login) {
                $userStats[$login] = array('name'      => $status[$infoCourse -> course['id']][$login]['name'],
                                           'surname'   => $status[$infoCourse -> course['id']][$login]['surname'],
                                           'active'    => $status[$infoCourse -> course['id']][$login]['active'],
                                           'role'	   => $status[$infoCourse -> course['id']][$login]['user_type'],
                						   'score'     => $status[$infoCourse -> course['id']][$login]['score'],
                                           'completed' => $status[$infoCourse -> course['id']][$login]['completed'],
                                           //'time'      => $status[$infoCourse -> course['id']][$login]['total_time'],
                                           //'seconds'   => $status[$infoCourse -> course['id']][$login]['total_time']['total_seconds']
                                           );
            }
            $smarty -> assign("T_COURSE_USERS_STATS", $userStats);
    
            $status         = EfrontStats :: getUsersCourseStatus($infoCourse, $professorLogins);
            $professorStats = array();
            foreach ($professorLogins as $login) {
                $professorStats[$login] = array('name'      => $status[$infoCourse -> course['id']][$login]['name'],
                                                'surname'   => $status[$infoCourse -> course['id']][$login]['surname'],
                                                'role'	    => $status[$infoCourse -> course['id']][$login]['user_type'],
                                                'active'    => $status[$infoCourse -> course['id']][$login]['active'],
                                                //'time'      => $status[$infoCourse -> course['id']][$login]['total_time'],
                                                //'seconds'   => $status[$infoCourse -> course['id']][$login]['total_time']['total_seconds']
                                                );
            }
            $smarty -> assign("T_COURSE_PROFESSORS_STATS", $professorStats);
    
            $courseLessons = $infoCourse -> getLessons(true);
            $lessonsInfo   = array();
            foreach ($courseLessons as $id => $lesson) {
                $stats                        = $lesson -> getStatisticInformation();
                $lessonsInfo[$id]['name']     = $lesson -> lesson['name'];
                $lessonsInfo[$id]['active']   = $lesson -> lesson['active'];
                $lessonsInfo[$id]['content']  = $stats['content'];
                $lessonsInfo[$id]['tests']    = $stats['tests'];
                $lessonsInfo[$id]['projects'] = $stats['projects'];
            }
            $smarty -> assign("T_COURSE_LESSON_STATS", $lessonsInfo);
    
        }
    
        if (isset($_GET['excel'])) {
            require_once 'Spreadsheet/Excel/Writer.php';
    
            $workBook  = new Spreadsheet_Excel_Writer();
            $workBook -> setVersion(8);
    
            $workBook -> send('export_'.$course -> course['name'].'.xls');
    
            $formatExcelHeaders = & $workBook -> addFormat(array('Size' => 14, 'Bold' => 1, 'HAlign' => 'left'));
            $headerFormat       = & $workBook -> addFormat(array('border' => 0, 'bold' => '1', 'size' => '11', 'color' => 'black', 'fgcolor' => 22, 'align' => 'center'));
            $formatContent      = & $workBook -> addFormat(array('HAlign' => 'left', 'Valign' => 'top', 'TextWrap' => 1));
            $headerBigFormat    = & $workBook -> addFormat(array('HAlign' => 'center', 'FgColor' => 22, 'Size' => 16, 'Bold' => 1));
            $titleCenterFormat  = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 11, 'Bold' => 1));
            $titleLeftFormat    = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 11, 'Bold' => 1));
            $fieldLeftFormat    = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10));
            $fieldRightFormat   = & $workBook -> addFormat(array('HAlign' => 'right', 'Size' => 10));
            $fieldCenterFormat  = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 10));
    
            //first tab
            $workSheet = & $workBook -> addWorksheet("General Course Info");
            $workSheet -> setInputEncoding('utf-8');
    
            $workSheet -> setColumn(0, 0, 5);
    
            //basic info
            $workSheet -> write(1, 1, _BASICINFO, $headerFormat);
            $workSheet -> mergeCells(1, 1, 1, 2);
            $workSheet -> setColumn(1, 2, 30);
    
            $workSheet -> write(2, 1, _COURSE, $fieldLeftFormat);
            $workSheet -> write(2, 2, $infoCourse -> course['name'], $fieldRightFormat);
            $workSheet -> write(3, 1, _DIRECTION, $fieldLeftFormat);
            $workSheet -> write(3, 2, $basicInfo['direction'], $fieldRightFormat);
            $workSheet -> write(4, 1, _LESSONS, $fieldLeftFormat);
            $workSheet -> writeNumber(4, 2, $basicInfo['lessons'], $fieldRightFormat);
            $workSheet -> write(5, 1, _STUDENTS, $fieldLeftFormat);
            $workSheet -> writeNumber(5, 2, $basicInfo['students'], $fieldRightFormat);
            $workSheet -> write(6, 1, _PROFESSORS, $fieldLeftFormat);
            $workSheet -> write(6, 2, $basicInfo['professors'], $fieldRightFormat);
            $workSheet -> write(7, 1, _PRICE, $fieldLeftFormat);
            $workSheet -> write(7, 2,  $infoCourse -> course['price'].' '.$GLOBALS['CURRENCYNAMES'][$GLOBALS['configuration']['currency']], $fieldRightFormat);
            $workSheet -> write(8, 1, _LANGUAGE, $fieldLeftFormat);
            $workSheet -> write(8, 2, $basicInfo['language'], $fieldRightFormat);
    
    
            //course users info
            $workSheet -> write(1, 4, _USERSINFO, $headerFormat);
            $workSheet -> mergeCells(1, 4, 1, 9);
            $workSheet -> setColumn(4, 9, 15);
    
            $workSheet -> write(2, 4, _LOGIN, $titleLeftFormat);
            $workSheet -> write(2, 5, _NAME, $titleLeftFormat);
            $workSheet -> write(2, 6, _SURNAME, $titleLeftFormat);
            $workSheet -> write(2, 7, _COURSEROLE, $titleLeftFormat);
            //$workSheet -> write(2, 7, _TOTALTIME, $titleCenterFormat);
            $workSheet -> write(2, 8, _SCORE, $titleCenterFormat);
            $workSheet -> write(2, 9, _COMPLETED, $titleCenterFormat);
    
            $roles = EfrontLessonUser :: getLessonsRoles(true);
            $row = 3;
            foreach ($userStats as $login => $info) {
                $workSheet -> write($row, 4, $login, $fieldLeftFormat);
                $workSheet -> write($row, 5, $info['name'], $fieldLeftFormat);
                $workSheet -> write($row, 6, $info['surname'], $fieldLeftFormat);
                $workSheet -> write($row, 7, $roles[$info['role']], $fieldLeftFormat);
                //$workSheet -> write($row, 7, $info['time']['hours']."h ".$info['time']['minutes']."' ".$$info['time']['seconds']."''", $fieldCenterFormat);
                $workSheet -> write($row, 8, formatScore($info['score'])."%", $fieldCenterFormat);
                $workSheet -> write($row, 9, $info['completed'] ? _YES : _NO, $fieldCenterFormat);
                $row++;
            }
            $row += 2;
    
            //lessons
            $workSheet -> write($row, 4, _LESSONS, $headerFormat);
            $workSheet -> mergeCells($row, 4, $row, 8);
    
            $row++;
            $workSheet -> write($row, 4, _LESSON, $titleLeftFormat);
            $workSheet -> write($row, 5, _CONTENT, $titleCenterFormat);
            $workSheet -> write($row, 6, _TESTS, $titleCenterFormat);
            $workSheet -> write($row, 7, _PROJECTS, $titleCenterFormat);
            $row++;
            foreach ($lessonsInfo as $id => $info) {
                $workSheet -> write($row, 4, $info['name'], $fieldLeftFormat);
                $workSheet -> write($row, 5, $info['content'], $fieldCenterFormat);
                $workSheet -> write($row, 6, $info['tests'], $fieldCenterFormat);
                $workSheet -> write($row, 7, $info['projects'], $fieldCenterFormat);
                $row++;
            }
    
            $workBook -> close();
            exit(0);
    
        } else if (isset($_GET['pdf'])) {
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
            $pdf -> setHeaderData('','','', _STATISTICSFORCOURSE.": ".$infoCourse -> course['name']);
    
            //initialize document
            $pdf -> AliasNbPages();
            $pdf -> AddPage();
    
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(100, 10, _BASICINFO, 0, 1, L, 0);
           
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> Cell(70, 5, _COURSE,     0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $infoCourse -> course['name'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _CATEGORY,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['direction'],       0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _LESSONS,    0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['lessons'],         0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _STUDENTS,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['students'],        0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _PROFESSORS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['professors'],      0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _PRICE,      0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $infoCourse -> course['price'].' '.$GLOBALS['CURRENCYNAMES'][$GLOBALS['configuration']['currency']], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _LANGUAGE,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['language'],        0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    
            //users
            $pdf -> AddPage('L');        
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> Cell(100, 10, _USERSINFO, 0, 1, L, 0);
            
            $pdf -> SetFont("FreeSerif", "B", 10);
            $pdf -> Cell(45, 7, _LOGIN, 0, 0, L, 0);
            $pdf -> Cell(45, 7, _NAME, 0, 0, L, 0);
            $pdf -> Cell(45, 7, _SURNAME, 0, 0, L, 0);
            $pdf -> Cell(45, 7, _COURSEROLE, 0, 0, L, 0);
            $pdf -> Cell(45, 7, _SCORE, 0, 0, C, 0);
            $pdf -> Cell(45, 7, _COMPLETED, 0, 1, C, 0);
    
            $roles = EfrontLessonUser :: getLessonsRoles(true);
            
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> SetTextColor(0, 0, 255);
            foreach ($userStats as $login => $info) {            
                $pdf -> Cell(45, 7, $login, 0, 0, L, 0);
                $pdf -> Cell(45, 7, $info['name'], 0, 0, L, 0);
                $pdf -> Cell(45, 7, $info['surname'], 0, 0, L, 0);
                $pdf -> Cell(45, 7, $roles[$info['role']], 0, 0, L, 0);
                $pdf -> Cell(45, 7, formatScore($info['score'])."%", 0, 0, C, 0);
                $pdf -> Cell(45, 7, $info['completed'] ? _YES : _NO, 0, 1, C, 0);
            }
    
            //lessons
            $pdf -> AddPage('L');        
            $pdf -> SetTextColor(0, 0, 0);
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> Cell(100, 10, _LESSONS, 0, 1, L, 0);
            
            $pdf -> SetFont("FreeSerif", "B", 10);
            $pdf -> Cell(60, 7, _LESSON, 0, 0, L, 0);
            $pdf -> Cell(60, 7, _CONTENT, 0, 0, C, 0);
            $pdf -> Cell(60, 7, _TESTS, 0, 0, C, 0);
            $pdf -> Cell(60, 7, _PROJECTS, 0, 1, C, 0);
    
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> SetTextColor(0, 0, 255);
            foreach ($lessonsInfo as $id => $info) {
                $pdf -> Cell(60, 7, $info['name'], 0, 0, L, 0);
                $pdf -> Cell(60, 7, $info['content'].' ', 0, 0, C, 0);
                $pdf -> Cell(60, 7, $info['tests'].' ', 0, 0, C, 0);
                $pdf -> Cell(60, 7, $info['projects'].' ', 0, 1, C, 0);
            }
            
            $pdf -> Output();
            exit(0);        
        }
    } else if ($_GET['option'] == 'test') {
    
        $smarty -> assign("T_OPTION", $_GET['option']);
        if (isset($_GET['sel_test'])) {
            try {
                //Calculate user names
                $result = eF_getTableData("users", "login, name, surname");
                foreach ($result as $value) {
                    $userNames[$value['login']] = $value;
                }
                $smarty -> assign("T_USER_NAMES", $userNames);
                
                $testId        = $_GET['sel_test'];
                $test          = new EfrontTest($testId);
                $testInfo      = EfrontStats :: getTestInfo($testId);
                $testQuestions = $test -> getQuestions(true);
                $testStats     = EfrontStats :: getDoneTestsPerTest(false, $test);
                unset($testStats[$testId]['average_score']);
                
                
                $smarty -> assign("T_TEST_INFO", $testInfo[$testId]);
                $smarty -> assign("T_TEST_NAME", $test -> test['name']);
                $smarty -> assign("T_TEST_STATS", $testStats[$testId]);
                $smarty -> assign("T_TEST_QUESTIONS", $testQuestions);
                $smarty -> assign("T_TEST_QUESTIONS_STATS", EfrontStats :: getQuestionsStatistics($test));
                $smarty -> assign("T_TEST_QUESTIONS_TRANSLATIONS", Question :: $questionTypes);
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        }
    
        if (isset($_GET['excel'])) {
            require_once 'Spreadsheet/Excel/Writer.php';
    
            $workBook  = new Spreadsheet_Excel_Writer();
            $workBook -> setVersion(8);
    
            $workBook -> send('test_'.$_GET['sel_test'].'_export.xls');
    
            $formatExcelHeaders = & $workBook -> addFormat(array('Size' => 14, 'Bold' => 1, 'HAlign' => 'center'));
            $headerFormat       = & $workBook -> addFormat(array('border' => 0, 'bold' => '1', 'size' => '11', 'color' => 'black', 'fgcolor' => 22, 'align' => 'center'));
            $formatContent      = & $workBook -> addFormat(array('HAlign' => 'left', 'Valign' => 'top', 'TextWrap' => 1));
            $headerBigFormat    = & $workBook -> addFormat(array('HAlign' => 'center', 'FgColor' => 22, 'Size' => 16, 'Bold' => 1));
            $titleCenterFormat  = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 11, 'Bold' => 1));
            $titleLeftFormat    = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 11, 'Bold' => 1));
            $fieldLeftFormat    = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10));
            $fieldRightFormat   = & $workBook -> addFormat(array('HAlign' => 'right', 'Size' => 10));
            $fieldCenterFormat  = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 10));
    
            //first tab
            $workSheet = & $workBook -> addWorksheet("General Test Info");
            $workSheet -> setInputEncoding('utf-8');
    
            $workSheet -> setColumn(0, 0, 5);
    
            //basic info
            $workSheet -> write(0, 0, $testInfo[$testId]['general']['name'], $formatExcelHeaders);
            $workSheet -> mergeCells(0, 0, 0, 7);
    
            $row = 1;
            $workSheet -> write($row, 1, _BASICINFO, $headerFormat);
            $workSheet -> mergeCells($row, 1, $row, 2);
            $workSheet -> setColumn($row++, 2, 30);
    
            $workSheet -> write($row, 1, _NAME, $fieldLeftFormat);
            $workSheet -> write($row++, 2, $testInfo[$testId]['general']['name'], $fieldRightFormat);
            $workSheet -> write($row, 1, _LESSON, $fieldLeftFormat);
            $workSheet -> write($row++, 2, $testInfo[$testId]['general']['lesson_name'], $fieldRightFormat);
            $workSheet -> write($row, 1, _TESTDURATION, $fieldLeftFormat);
            $workSheet -> write($row++, 2, $testInfo[$testId]['general']['duration'] ? $testInfo[$testId]['general']['duration_str']['hours'].'h '.$testInfo[$testId]['general']['duration_str']['minutes']."' ".$testInfo[$testId]['general']['duration_str']['seconds']."''" : _UNLIMITED, $fieldRightFormat);
            $workSheet -> write($row, 1, _QUESTIONS, $fieldLeftFormat);
            $workSheet -> write($row++, 2, sizeof($testInfo[$testId]['questions']), $fieldRightFormat);
    
            $row    = 1;
            $column = 4;
            $workSheet -> write($row, $column, _QUESTIONS, $headerFormat);
            $workSheet -> mergeCells($row, $column, $row, $column + 3);
            $row++;
                    
            $workSheet -> write($row, $column, _QUESTIONTEXT, $titleLeftFormat);
            $workSheet -> write($row, $column + 1, _QUESTIONTYPE, $titleCenterFormat);
            $workSheet -> write($row, $column + 2, _DIFFICULTY, $titleCenterFormat);
            $workSheet -> write($row++, $column + 3, _WEIGHT, $titleCenterFormat);
            $workSheet -> setColumn($column, $column, 30);
            $workSheet -> setColumn($column + 1, $column + 3, 20);
            //$workSheet -> setColumn(7, 9, 20);
    
            $questionShortHands = array('multiple_one'  => 'MC',
                                        'multiple_many' => 'MCMA',
                                        'match'         => 'MA',
                                        'empty_spaces'  => 'FB',
                                        'raw_text'      => 'OA',
                                        'true_false'    => 'YN');
            
            foreach ($testQuestions as $id => $question) {
                $workSheet -> write($row, $column, trim(strip_tags($question -> question['text'])), $fieldLeftFormat);
                $workSheet -> write($row, $column + 1, $questionShortHands[$question -> question['type']], $fieldCenterFormat);
                $workSheet -> write($row, $column + 2, Question :: $questionDifficulties[$question -> question['difficulty']], $fieldCenterFormat);
                $workSheet -> write($row++, $column + 3, $question -> question['weight'], $fieldCenterFormat);
            }
    
            $row += 4;
            $workSheet -> write($row++, $column, _MCEXPLANATION, $fieldLeftFormat);
            $workSheet -> write($row++, $column, _MCMAEXPLANATION, $fieldLeftFormat);
            $workSheet -> write($row++, $column, _MAEXPLANATION, $fieldLeftFormat);
            $workSheet -> write($row++, $column, _FBEXPLANATION, $fieldLeftFormat);
            $workSheet -> write($row++, $column, _OAEXPLANATION, $fieldLeftFormat);
            $workSheet -> write($row, $column, _YNEXPLANATION, $fieldLeftFormat);
            
            $workSheet -> close();
    
            $workSheet = & $workBook -> addWorkSheet("Test results information");
            $workSheet -> setInputEncoding('utf-8');
    
            $workSheet -> write(0, 0, $testInfo[$testId]['general']['name'], $formatExcelHeaders);
            $workSheet -> mergeCells(0, 0, 0, 2);
            $row    = 1;
            $workSheet -> write($row, 0, _USER, $headerFormat);
            $workSheet -> write($row, 1, _SCORE, $headerFormat);
            $workSheet -> write($row++, 2, _DATE, $headerFormat);
            $workSheet -> setColumn(0, 2, 30);
            foreach ($testInfo[$testId]['done'] as $doneInfo) {
                $workSheet -> write($row, 0, $doneInfo['name'].' '.$doneInfo['surname'].' ('.$doneInfo['users_LOGIN'].')', $fieldLeftFormat);
                $workSheet -> write($row, 1, formatScore($doneInfo['score'])."%", $fieldCenterFormat);
                $workSheet -> write($row++, 2, formatTimestamp($doneInfo['timestamp'], 'time'), $fieldLeftFormat);
            }
            $workSheet -> close();
    
            $workBook -> close();
            exit(0);
        } elseif (isset($_GET['pdf'])) {
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
            $pdf -> setHeaderData('','','', _STATISTICSFORCOURSE.": ".$infoCourse -> course['name']);
    
            //initialize document
            $pdf -> AliasNbPages();
            $pdf -> AddPage();
    
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(100, 10, _BASICINFO, 0, 1, L, 0);
            
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> Cell(70, 5, _NAME,     0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $testInfo[$testId]['general']['name'],        0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _LESSON,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $testInfo[$testId]['general']['lesson_name'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _TESTDURATION, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $testInfo[$testId]['general']['duration'] ? $testInfo[$testId]['general']['duration_str']['hours'].'h '.$testInfo[$testId]['general']['duration_str']['minutes']."' ".$testInfo[$testId]['general']['duration_str']['seconds']."''" : _UNLIMITED, 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(70, 5, _QUESTIONS,    0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($testInfo[$testId]['questions']),   0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    
            //questions
            $questionShortHands = array('multiple_one'  => 'MC',
                                        'multiple_many' => 'MCMA',
                                        'match'         => 'MA',
                                        'empty_spaces'  => 'FB',
                                        'raw_text'      => 'OA',
                                        'true_false'    => 'YN');
            
            $pdf -> AddPage('L');        
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> Cell(100, 10, _QUESTIONS, 0, 1, L, 0);
            
            $pdf -> SetFont("FreeSerif", "B", 10);
            $pdf -> Cell(100, 7, _QUESTIONTEXT, 0, 0, L, 0);
            $pdf -> Cell(30, 7, _QUESTIONTYPE, 0, 0, C, 0);
            $pdf -> Cell(30, 7, _DIFFICULTY, 0, 0, C, 0);
            $pdf -> Cell(30, 7, _WEIGHT, 0, 1, C, 0);
    
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> SetTextColor(0, 0, 255);
            foreach ($testQuestions as $id => $question) {            
                $pdf -> Cell(100, 7, $question -> question['plain_text'], 0, 0, L, 0);
                $pdf -> Cell(30, 7, $questionShortHands[$question -> question['type']], 0, 0, C, 0);
                $pdf -> Cell(30, 7, Question :: $questionDifficulties[$question -> question['difficulty']], 0, 0, C, 0);
                $pdf -> Cell(30, 7, $question -> question['weight'], 0, 1, C, 0);
            }
            
            $pdf -> Cell(100, 10, '', 0, 1, L, 0);
            
            $pdf -> SetTextColor(0, 0, 0);
            $pdf -> Cell(140, 7, _MCEXPLANATION, 0, 1, L, 0);
            $pdf -> Cell(140, 7, _MCMAEXPLANATION, 0, 1, L, 0);
            $pdf -> Cell(140, 7, _MAEXPLANATION, 0, 1, L, 0);
            $pdf -> Cell(140, 7, _FBEXPLANATION, 0, 1, L, 0);
            $pdf -> Cell(140, 7, _OAEXPLANATION, 0, 1, L, 0);
            $pdf -> Cell(140, 7, _YNEXPLANATION, 0, 1, L, 0);
            
            //donw test info
            $pdf -> AddPage('L');        
            $pdf -> SetFont("FreeSerif", "B", 12);
            $pdf -> Cell(100, 10, $testInfo[$testId]['general']['name'], 0, 1, L, 0);
            
            $pdf -> SetFont("FreeSerif", "B", 10);
            $pdf -> Cell(60, 7, _USER, 0, 0, L, 0);
            $pdf -> Cell(60, 7, _SCORE, 0, 0, C, 0);
            $pdf -> Cell(60, 7, _DATE, 0, 1, L, 0);
    
            $pdf -> SetFont("FreeSerif", "", 10);
            $pdf -> SetTextColor(0, 0, 255);
            foreach ($testInfo[$testId]['done'] as $doneInfo) {            
                $pdf -> Cell(60, 7, $doneInfo['name'].' '.$doneInfo['surname'].' ('.$doneInfo['users_LOGIN'].')', 0, 0, L, 0);
                $pdf -> Cell(60, 7, formatScore($doneInfo['score'])."%", 0, 0, C, 0);
                $pdf -> Cell(60, 7, formatTimestamp($doneInfo['timestamp'], 'time'), 0, 1, L, 0);
            }
            
            $pdf -> Output();
            exit(0);        
        
        }
    } else if ($_GET['option'] == 'system') {
        /*If the user is not the administrator, then */
        if ($currentUser -> user['user_type'] != 'administrator') {
            exit;
        }
        $smarty -> assign("T_OPTION", $_GET['option']);
    
        try {
            if (isset($_GET['from_year'])) { //the admin has chosen a period
                $from = mktime($_GET['from_hour'], $_GET['from_min'], 0, $_GET['from_month'], $_GET['from_day'], $_GET['from_year']);
                $to   = mktime($_GET['to_hour'],   $_GET['to_min'],   0, $_GET['to_month'],   $_GET['to_day'],   $_GET['to_year']);
            } else {
                $from    = mktime(date("H"), date("i"), 0, date("m"), date("d") - 7, date("Y"));
                $to      = mktime(date("H"), date("i"), 0, date("m"), date("d"),     date("Y"));
            }
            $smarty -> assign('T_FROM_TIMESTAMP', $from);
            $smarty -> assign('T_TO_TIMESTAMP',   $to);
    
            $actions = array('login'      => _LOGIN,
                             'logout'     => _LOGOUT,
                             'lesson'     => _ACCESSEDLESSON,
                             'content'    => _ACCESSEDCONTENT,
                             'tests'      => _ACCESSEDTEST,
                             'test_begin' => _BEGUNTEST,
                             'lastmove'   => _NAVIGATEDSYSTEM);
            $smarty -> assign("T_ACTIONS", $actions);
    
    
            if (isset($_GET['showlog']) && $_GET['showlog'] == "true") {
                $lessonNames  = eF_getTableDataFlat("lessons", "id, name");
                $lessonNames  = array_combine($lessonNames['id'], $lessonNames['name']);
                $contentNames = eF_getTableDataFlat("content", "id, name");
                $contentNames = array_combine($contentNames['id'], $contentNames['name']);
                $testNames    = eF_getTableDataFlat("tests t, content c", "t.id, c.name", "c.id=t.content_ID");
                $testNames    = array_combine($testNames['id'], $testNames['name']);
                $result       = eF_getTableData("logs", "*", "timestamp between $from and $to order by timestamp");
                
                foreach ($result as $key => $value) {
                    $value['lessons_ID'] ? $result[$key]['lesson_name'] = $lessonNames[$value['lessons_ID']] : null;
                    if ($value['action'] == 'content') {
                        $result[$key]['content_name'] = $contentNames[$value['comments']];
                    } else if ($value['action'] == 'tests' || $value['action'] == 'test_begin') {
                        $result[$key]['content_name'] = $testNames[$value['comments']];
                    }
                }
    
                $smarty -> assign("T_SYSTEM_LOG", $result);
            }
    
            $users   = array();        
            $result  = eF_getTableData("logs, users", "users.name, users.surname, users.active, users_LOGIN, count(id) as cnt ", "users.login=users_LOGIN and action = 'login' and logs.timestamp between $from and $to group by users_LOGIN order by count(id) desc");
            $userTimes = EfrontUser :: getLoginTime(false, array('from' => $from, 'to' => $to));
    
            foreach($result as $value) {
                $users[$value['users_LOGIN']]['name']     = $value['name'];
                $users[$value['users_LOGIN']]['surname']  = $value['surname'];
                $users[$value['users_LOGIN']]['active']   = $value['active'];
                $users[$value['users_LOGIN']]['accesses'] = $value['cnt'];
                $users[$value['users_LOGIN']]['seconds']  = $userTimes[$value['users_LOGIN']]['total_seconds'];
            }
            
            $lessons = array();        
            $result  = eF_getTableData("logs", "*", "timestamp between $from and $to");
            foreach ($result as $value) {
    
                if ($value['lessons_ID']) {
                    $lessons[$value['lessons_ID']] = array();
                }
            }
            
            $totalUserAccesses = $totalUserTime = 0;
            foreach ($users as $key => $user) {
                $users[$key]['time'] = eF_convertIntervalToTime($user['seconds']);
                $totalUserAccesses += $user['accesses'];
                $totalUserTime     += $user['seconds'];
                $userTimes[$key]    = $user['seconds'];                         //Needed only for chart
            }
            if (!isset($_GET['showusers'])) {
                $users = array_slice($users, 0, 20);
            }
    
            $smarty -> assign("T_ACTIVE_USERS", $users);
            $smarty -> assign("T_TOTAL_USER_ACCESSES", $totalUserAccesses);
            $smarty -> assign("T_TOTAL_USER_TIME", eF_convertIntervalToTime($totalUserTime));
            $smarty -> assign("T_USER_TIMES", array('logins' => implode(",", array_keys($userTimes)), 'times' => implode(",", $userTimes)));                    //Needed only for chart
    
            $result       = eF_getTableDataFlat("lessons", "id, name, active");
            $lessonNames  = array_combine($result['id'], $result['name']);
            $lessonActive = array_combine($result['id'], $result['active']);
            foreach ($lessons as $key => $value) {
                try {
                    $stats = EfrontStats :: getUsersTime($key, false, $from, $to);
                    foreach ($stats as $user => $info) {
                        $lessons[$key]['accesses'] += $info['accesses'];
                        $lessons[$key]['seconds']  += $info['total_seconds'];
                    }
                    $lessons[$key]['name']   = $lessonNames[$key];
                    $lessons[$key]['active'] = $lessonActive[$key];
                } catch (Exception $e) {}                    //Don't halt on a single error
            }
            foreach ($lessons as $key => $lesson) {
                $lessons[$key]['time'] = eF_convertIntervalToTime($lesson['seconds']);
            }
            if (!isset($_GET['showlessons'])) {
                $lessons = array_slice($lessons, 0, 20);
            }
            
            $smarty -> assign("T_ACTIVE_LESSONS", $lessons);
    
            $userTypes = eF_getTableData("users", "user_type, count(user_type) as num", "", "", "user_type");
            $smarty -> assign("T_USER_TYPES", $userTypes);
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
    
?>
