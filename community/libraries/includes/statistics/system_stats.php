<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*If the user is not the administrator, then */
if ($currentUser -> user['user_type'] != 'administrator') {
    exit;
}
$smarty -> assign("T_OPTION", $_GET['option']);

try {


    if (isset($_GET['from_year'])) { //the admin has chosen a period
        $from = mktime($_GET['from_hour'], $_GET['from_min'], 0, $_GET['from_month'], $_GET['from_day'], $_GET['from_year']);
        $to = mktime($_GET['to_hour'], $_GET['to_min'], 0, $_GET['to_month'], $_GET['to_day'], $_GET['to_year']);
    } else {
        $from = mktime(date("H"), date("i"), date("s"), date("m"), date("d") - 7, date("Y"));
        $to = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
    }
    $smarty -> assign('T_FROM_TIMESTAMP', $from);
    $smarty -> assign('T_TO_TIMESTAMP', $to);

    $actions = array('login' => _LOGIN,
                             'logout' => _LOGOUT,
                             'lesson' => _ACCESSEDLESSON,
                             'content' => _ACCESSEDCONTENT,
                             'tests' => _ACCESSEDTEST,
                             'test_begin' => _BEGUNTEST,
                             'lastmove' => _NAVIGATEDSYSTEM);
    $smarty -> assign("T_ACTIONS", $actions);

    if (isset($_GET['showlog']) && $_GET['showlog'] == "true") {
        $lessonNames = eF_getTableDataFlat("lessons", "id, name");
        $lessonNames = array_combine($lessonNames['id'], $lessonNames['name']);
        $contentNames = eF_getTableDataFlat("content", "id, name");
        $contentNames = array_combine($contentNames['id'], $contentNames['name']);
        $testNames = eF_getTableDataFlat("tests t, content c", "t.id, c.name", "c.id=t.content_ID");
        $testNames = array_combine($testNames['id'], $testNames['name']);
        $result = eF_getTableData("logs", "*", "timestamp between $from and $to order by timestamp");

        foreach ($result as $key => $value) {
            $value['lessons_ID'] ? $result[$key]['lesson_name'] = $lessonNames[$value['lessons_ID']] : null;
            if ($value['action'] == 'content' || $value['action'] == 'tests' || $value['action'] == 'test_begin') {
                $result[$key]['content_name'] = $contentNames[$value['comments']];
            }
        }
  $analytic_log = $result;
        $smarty -> assign("T_SYSTEM_LOG", $analytic_log);
    }

    $users = array();
    $result = eF_getTableData("logs, users", "users.name, users.surname, users.active, users_LOGIN, count(logs.id) as cnt ", "users.login=users_LOGIN and action = 'login' and logs.timestamp between $from and $to group by users_LOGIN order by count(logs.id) desc");
//    $userTimes = EfrontUser :: getLoginTime(false, array('from' => $from, 'to' => $to));

    $timesReport = new EfrontTimes(array($from, $to));
    $userTimes = $timesReport -> getSystemSessionTimesForUsers();

    foreach($result as $value) {
        $users[$value['users_LOGIN']]['name'] = $value['name'];
        $users[$value['users_LOGIN']]['surname'] = $value['surname'];
        $users[$value['users_LOGIN']]['active'] = $value['active'];
        $users[$value['users_LOGIN']]['accesses'] = $value['cnt'];
        $users[$value['users_LOGIN']]['seconds'] = $userTimes[$value['users_LOGIN']];
    }

    $lessons = array();
    $result = eF_getTableData("logs", "*", "timestamp between $from and $to");
    foreach ($result as $value) {

        if ($value['lessons_ID']) {
            $lessons[$value['lessons_ID']] = array();
        }
    }

    $totalUserAccesses = $totalUserTime = 0;
    foreach ($users as $key => $user) {
        $users[$key]['time'] = eF_convertIntervalToTime($user['seconds']);
        $totalUserAccesses += $user['accesses'];
        $totalUserTime += $user['seconds'];
    }

    if (!isset($_GET['showusers'])) {
        $users = array_slice($users, 0, 20);
    }

    $smarty -> assign("T_ACTIVE_USERS", $users);
    $smarty -> assign("T_TOTAL_USER_ACCESSES", $totalUserAccesses);
    $smarty -> assign("T_TOTAL_USER_TIME", eF_convertIntervalToTime($totalUserTime));
    $smarty -> assign("T_USER_TIMES", array('logins' => implode(",", array_keys($userTimes)), 'times' => implode(",", $userTimes))); //Needed only for chart

/*

//Commented out until we convert old log-based stats to time-based



    $directionsTree = new EfrontDirectionsTree();

    $directionsTreePaths = $directionsTree -> toPathString();



    $result       = eF_getTableDataFlat("lessons", "id, name, active, directions_ID");

    $lessonNames  = array_combine($result['id'], $result['name']);

    $lessonActive = array_combine($result['id'], $result['active']);

    $lessonCategory = array_combine($result['id'], $result['directions_ID']);



    $lessonTimes = $timesReport -> getSystemSessionTimesForLessons();

    foreach ($lessons as $key => $value) {

        try {

			$lessons[$key]['seconds']= $lessonTimes[$key];

            $lessons[$key]['name']   = $directionsTreePaths[$lessonCategory[$key]].'&nbsp;&rarr;&nbsp;'.$lessonNames[$key];

            $lessons[$key]['active'] = $lessonActive[$key];

        } catch (Exception $e) {}                    //Don't halt on a single error

        if (!$lessonNames[$key]) {

            unset($lessons[$key]);

        }

    }



    foreach ($lessons as $key => $lesson) {

        $lessons[$key]['time'] = eF_convertIntervalToTime($lesson['seconds']);

    }

    if (!isset($_GET['showlessons'])) {

        $lessons = array_slice($lessons, 0, 20);

    }



    $smarty -> assign("T_ACTIVE_LESSONS", $lessons);

*/
    $userTypes = eF_getTableData("users", "user_type, count(user_type) as num", "", "", "user_type");
    $smarty -> assign("T_USER_TYPES", $userTypes);
    try {
     if (isset($_GET['ajax']) && $_GET['ajax'] == 'graph_system_access') {
      $result = eF_getTableData("logs", "*", "timestamp between $from and $to and action = 'login' order by timestamp");
      //Assign the number of accesses to each week day
      foreach ($result as $value) {
       $cnt = 0;
       for ($i = $from; $i <= $to; $i += 86400) {
        $labels[$cnt] = $i;
        isset($count[$cnt]) OR $count[$cnt] = 0;
        if ($i <= $value['timestamp'] && $value['timestamp'] < $i + 86400) {
         $count[$cnt]++;
        }
        $cnt++;
       }
      }
      $graph = new EfrontGraph();
      $graph -> type = 'line';
      //$step = sizeof($labels)/8;
      for ($i = 0; $i < sizeof($labels); $i++) {
       $graph -> data[] = array($i, $count[$i]);
       $graph -> xLabels[] = array($i, formatTimestamp($labels[$i]));
      }
      $graph -> xTitle = _DAY;
      $graph -> yTitle = _LOGINS;
      $graph -> title = _LOGINSPERDAY;
      echo json_encode($graph);
      exit;
     } elseif (isset($_GET['ajax']) && $_GET['ajax'] == 'graph_system_users_access') {
      $graph = new EfrontGraph();
      $graph -> type = 'bar';
      $count = 0;
      foreach ($userTimes as $key => $value) {
       $graph -> xLabels[] = array($count, formatLogin($key));
       $graph -> data[] = array($count++, round($value/60));
      }
      $graph -> xTitle = _USERS;
      $graph -> yTitle = _MINUTES;
      $graph -> title = _MINUTESPERUSER;
      echo json_encode($graph);
      exit;
     } elseif (isset($_GET['ajax']) && $_GET['ajax'] == 'graph_system_user_types') {
   $result = eF_getTableData("users", "user_type, count(user_type) as num", "", "", "user_type");
   $roles = EfrontUser::getRoles(true);
   $graph = new EfrontGraph();
      $graph -> type = 'bar';
      $count = 0;
      foreach ($result as $value) {
       $graph -> data[] = array($count, $value['num']);
       $graph -> xLabels[] = array($count++, $roles[$value['user_type']]);
      }
      $graph -> xTitle = _USERTYPES;
      $graph -> yTitle = _USERS;
      $graph -> title = _USERSEPERUSERTYPE;
      echo json_encode($graph);
   exit;
     }
    } catch (Exception $e) {
     handleAjaxExceptions($e);
    }
} catch (Exception $e) {
 handleNormalFlowExceptions($e);
}
if (isset($_GET['excel'])) {
    require_once 'Spreadsheet/Excel/Writer.php';
    $workBook = new Spreadsheet_Excel_Writer();
    $workBook -> setTempDir(G_UPLOADPATH);
    $workBook -> setVersion(8);
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
    $workSheet = & $workBook -> addWorksheet("System info");
    $workSheet -> setInputEncoding('utf-8');
    $workSheet -> setColumn(0, 0, 5);
    $workSheet -> write(1, 1, _BASICINFO." (".formatTimestamp($from)." - ".formatTimestamp($to).")", $headerFormat);
    $workSheet -> mergeCells(1, 1, 1, 2);
    $workSheet -> setColumn(1, 3, 30);
    $workSheet -> write(2, 1, _ACCESSNUMBER, $fieldLeftFormat);
    $workSheet -> write(2, 2, $totalUserAccesses, $fieldRightFormat);
    $workSheet -> write(3, 1, _TOTALACCESSTIME, $fieldLeftFormat);
    $time = EfrontTimes::formatTimeForReporting($totalUserTime);
    $workSheet -> write(3, 2, $time['time_string'], $fieldRightFormat);
    $workSheet -> write(5, 1, _USERSINFO." (".formatTimestamp($from)." - ".formatTimestamp($to).")", $headerFormat);
    $workSheet -> mergeCells(5, 1, 5, 3);
    $workSheet -> write(6, 1, _LOGIN, $titleLeftFormat);
    $workSheet -> write(6, 2, _ACCESSNUMBER, $titleLeftFormat);
    $workSheet -> write(6, 3, _TOTALTIME, $titleLeftFormat);
    $row=7;
    foreach ($users as $login => $value) {
     $workSheet -> write($row, 1, formatLogin($login), $fieldLeftFormat);
     $workSheet -> write($row, 2, $value['accesses'], $fieldCenterFormat);
     $time = EfrontTimes::formatTimeForReporting($value['seconds']);
     $workSheet -> write($row++, 3, $time['time_string'], $fieldCenterFormat);
    }
    $workSheet = & $workBook -> addWorkSheet("Analytic log");
    $workSheet -> setInputEncoding('utf-8');
    $workSheet -> setColumn(0, 0, 5);
    $workSheet -> write(1, 1, _ANALYTICLOG, $headerFormat);
    $workSheet -> mergeCells(1, 1, 1, 7);
    $workSheet -> setColumn(1, 6, 30);
    $workSheet -> write(2, 1, _LOGIN, $fieldLeftFormat);
    $workSheet -> write(2, 2, _LESSON, $fieldLeftFormat);
    $workSheet -> write(2, 3, _UNIT, $fieldLeftFormat);
    $workSheet -> write(2, 4, _ACTION, $fieldLeftFormat);
    $workSheet -> write(2, 5, _TIME, $fieldLeftFormat);
    $workSheet -> write(2, 6, _IPADDRESS, $fieldLeftFormat);
    $row=3;
        foreach ($analytic_log as $value) {
            $workSheet -> write($row, 1, formatLogin($value['users_LOGIN']), $fieldLeftFormat);
            $workSheet -> write($row, 2, $value['lesson_name'], $fieldCenterFormat);
            $workSheet -> write($row, 3, $value['content_name'], $fieldLeftFormat);
            $workSheet -> write($row, 4, $actions[$value['action']], $fieldLeftFormat);
            $workSheet -> write($row, 5, formatTimestamp($value['timestamp'], 'time'), $fieldLeftFormat);
         $workSheet -> write($row++, 6, eF_decodeIP($value['session_ip']), $fieldLeftFormat);
        }
    $workBook -> send('system_reports.xls');
    $workBook -> close();
    exit(0);
}
