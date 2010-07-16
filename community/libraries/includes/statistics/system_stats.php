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
        $from = mktime(date("H"), date("i"), 0, date("m"), date("d") - 7, date("Y"));
        $to = mktime(date("H"), date("i"), 0, date("m"), date("d"), date("Y"));
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

        $smarty -> assign("T_SYSTEM_LOG", $result);
    }

    $users = array();
    $result = eF_getTableData("logs, users", "users.name, users.surname, users.active, users_LOGIN, count(logs.id) as cnt ", "users.login=users_LOGIN and action = 'login' and logs.timestamp between $from and $to group by users_LOGIN order by count(logs.id) desc");
    $userTimes = EfrontUser :: getLoginTime(false, array('from' => $from, 'to' => $to));

    foreach($result as $value) {
        $users[$value['users_LOGIN']]['name'] = $value['name'];
        $users[$value['users_LOGIN']]['surname'] = $value['surname'];
        $users[$value['users_LOGIN']]['active'] = $value['active'];
        $users[$value['users_LOGIN']]['accesses'] = $value['cnt'];
        $users[$value['users_LOGIN']]['seconds'] = $userTimes[$value['users_LOGIN']]['total_seconds'];
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
    foreach ($userTimes as $key => $value) {
     $userTimes[$key] = $value['total_seconds'];
    }

    if (!isset($_GET['showusers'])) {
        $users = array_slice($users, 0, 20);
    }

    $smarty -> assign("T_ACTIVE_USERS", $users);
    $smarty -> assign("T_TOTAL_USER_ACCESSES", $totalUserAccesses);
    $smarty -> assign("T_TOTAL_USER_TIME", eF_convertIntervalToTime($totalUserTime));
    $smarty -> assign("T_USER_TIMES", array('logins' => implode(",", array_keys($userTimes)), 'times' => implode(",", $userTimes))); //Needed only for chart

    $directionsTree = new EfrontDirectionsTree();
    $directionsTreePaths = $directionsTree -> toPathString();

    $result = eF_getTableDataFlat("lessons", "id, name, active, directions_ID");
    $lessonNames = array_combine($result['id'], $result['name']);
    $lessonActive = array_combine($result['id'], $result['active']);
    $lessonCategory = array_combine($result['id'], $result['directions_ID']);

    $allStats = EfrontStats :: getUsersTimeAll($from, $to);
    foreach ($lessons as $key => $value) {
        try {
            $stats = $allStats[$key];
            foreach ($stats as $user => $info) {
                $lessons[$key]['accesses'] += $info['accesses'];
                $lessons[$key]['seconds'] += $info['total_seconds'];
            }
            $lessons[$key]['name'] = $directionsTreePaths[$lessonCategory[$key]].'&nbsp;&rarr;&nbsp;'.$lessonNames[$key];
            $lessons[$key]['active'] = $lessonActive[$key];
        } catch (Exception $e) {} //Don't halt on a single error
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
      for ($i = 0; $i < sizeof($labels); $i++) {
       $graph -> data[] = array($i, $count[$i]);
       $graph -> xLabels[] = array($i, '<span style = "white-space:nowrap">'.formatTimestamp($labels[$i]).'</span>');
      }

      $graph -> xTitle = _DAY;
      $graph -> yTitle = _LOGINS;
      $graph -> title = _LOGINSPERDAY;

      echo json_encode($graph);
      exit;
     } elseif (isset($_GET['ajax']) && $_GET['ajax'] == 'graph_system_users_access') {
      $graph = new EfrontGraph();
      $graph -> type = 'horizontal_bar';
      $count = 0;
      foreach ($userTimes as $key => $value) {
       $graph -> data[] = array(round($value/60), $count);
       $graph -> xLabels[] = array($count++, '<span style = "white-space:nowrap">'.formatLogin($key).'</span>');
      }
      $graph -> xTitle = _MINUTES;
      $graph -> yTitle = _USERS;
      $graph -> title = _MINUTESPERUSER;

      echo json_encode($graph);
      exit;
     } elseif (isset($_GET['ajax']) && $_GET['ajax'] == 'graph_system_user_types') {
   $result = eF_getTableData("users", "user_type, count(user_type) as num", "", "", "user_type");

   $graph = new EfrontGraph();
      $graph -> type = 'bar';
      $count = 0;
      foreach ($result as $value) {
       $graph -> data[] = array($count, $value['num']);
       $graph -> xLabels[] = array($count++, $value['user_type']);
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
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
?>
