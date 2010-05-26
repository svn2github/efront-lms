<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

/* Reports are at this point developed as "search employee" module. They report which employee(s) fulfill some criteria */
/* Check permissions: only administrator and supervisors can see the reports - the supervisors for the employees that work in the branches they supervise */
/* Create a new courses/instances list for mass assignments */
if ($_GET['ajax'] == 'coursesTable' || $_GET['ajax'] == 'instancesTable') {
 try {
  if ($_GET['ajax'] == 'coursesTable') {
   $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => false);
  }
  if ($_GET['ajax'] == 'instancesTable' && eF_checkParameter($_GET['instancesTable_source'], 'id')) {
   $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => $_GET['instancesTable_source']);
  }
  $constraints['required_fields'] = array('has_instances');
  $courses = EfrontCourse :: getAllCourses($constraints);
  $totalEntries = EfrontCourse :: countAllCourses($constraints);
  $dataSource = EfrontCourse :: convertCourseObjectsToArrays($courses);
  $smarty -> assign("T_TABLE_SIZE", $totalEntries);
  $tableName = $_GET['ajax'];
  $alreadySorted = 1;
  include("sorted_table.php");
 } catch (Exception $e) {
  handleAjaxExceptions($e);
 }
 exit;
}
/* Return the employees that match the search criteria */
if (isset($_GET['search'])) {
 /*****************************************************

	 GET EMPLOYEES FILLING THE CRITERIA

	 **************************************************** */
 //echo "employees<Br>";
 //pr($employees);
 /* Filter those data according to whether all or some of the criteria need to be fulfilled */
 if ($_GET['all'] == "false") {
  $preposition = " OR ";
 } else {
  $preposition = " AND ";
 }
 /* If advanced criteria are enabled */
 if (isset($_GET['login']) || isset($_POST['login'])) {
  $size = sizeof($employees);
  if ($size > 0) {
   $list = "users.login IN (";
   $k = 0;
   foreach ($employees as $employee) {
    $list = $list . "'" . $employee['login'] . "'" ;
    if ($k++ != $size - 1) {
     $list = $list . ",";
    }
   }
   $list = $list . ") ";
  }
  $sql_query = $list;
  $found_field = 0;
  // Dates management - search needs to know which fields are dates
   $datesFields = array("timestamp");
   /* Get all employees fulfilling the "advanced criteria" */
   // Need to create the criteria - we could check the field names of the current user object
   $criteria = array_keys($currentUser -> user);
  foreach ($criteria as $field) {
   if (isset($_GET[$field]) && $_GET[$field] != "") {
    $value = $_GET[$field];
    if (($value || $field == "sex" || $field =="marital_status" || $field == "way_of_working") && $field != "search_branch" && $field != "search_job_description" && $field != "search_skill" && $field != "criteria" && $field != "submit_personal_details" && $field != "include_subbranches") {
     if ($field == "new_login") {
      $field = "login";
     }
     if ($field == "user_type" && $value != '' && $value != "administrator" && $value != "professor" && $value != "student") {
      // then we have identified a custom user type
      $field = "user_types_ID";
     }
     if (in_array($field, $datesFields)) {
      $value = mktime(0, 0, 0, $_GET[$field . "Month"], $_GET[$field ."Day"], $_GET[$field . "Year"]);
      switch ($_GET[$field]) {
       case "2": $sign = "<"; break;
       case "3": $sign = "="; break;
       default: $sign = ">";
      }
      if ($sql_query != $list) {
       $sql_query .= $preposition . " (($field IS NOT NULL) AND ($field $sign $value)) ";
      } else {
       if ($sql_query) {
        $sql_query .= $preposition . " ((($field IS NOT NULL) AND ($field $sign $value)) ";
       } else {
        $sql_query .= " ((($field IS NOT NULL) AND ($field $sign $value)) ";
       }
      }
     } else {
      if ($sql_query != $list) {
       $sql_query .= $preposition . " ($field LIKE '%$value%') ";
      } else {
       if ($sql_query) {
        $sql_query .= $preposition . " (($field LIKE '%$value%') ";
       } else {
        $sql_query .= " ((($field IS NOT NULL) AND ($field LIKE '%$value%')) ";
       }
      }
     }
     $found_field = 1;
    }
   }
  }
//		echo $sql_query."<BR>";
//		if ($found_field) {
//			$sql_query .= ")";
//			$found_field = 0;
//		}
  // Custom fields management
  if ($found_field) {
   $sql_query .= ")";
  }
//		echo $sql_query."<BR>";
  /*************** THE SEARCH QUERY ****************/
   $result = eF_getTableData("users","*", $sql_query . " LIMIT 100");
   $employees = $result;
  //pr($result);
 }
  $recipients_array = array();
  foreach ($employees as $key => $employee) {
   $recipients_array[] = $employee['login'];
  }
 // Management of the 'send email to all found' link icon on the top right of the table
 // During ajax refresh
 if (isset($_GET['ajax']) && $_GET['ajax'] == 'foundEmployees') {
  $smarty -> assign("T_SENDALLMAIL_URL", implode($recipients_array, ";"));
  $dataSource = $employees;
  $tableName = $_GET['ajax'];
  /**Handle sorted table's sorting and filtering*/
  include("sorted_table.php");
 } else if (isset($_GET['stats']) && $_GET['stats'] == 1) {
  $user_logins = $recipients_array;
  $lessonNames = eF_getTableDataFlat("lessons", "id, name");
  $lessonNames = array_combine($lessonNames['id'], $lessonNames['name']);
  $contentNames = eF_getTableDataFlat("content", "id, name");
  $contentNames = array_combine($contentNames['id'], $contentNames['name']);
  $testNames = eF_getTableDataFlat("tests t, content c", "t.id, c.name", "c.id=t.content_ID");
  $testNames = array_combine($testNames['id'], $testNames['name']);
  //$result = eF_getTableData("logs", "*", "timestamp between $from and $to and users_LOGIN in ('".implode("','", $user_logins)."') order by timestamp desc");
  $result = eF_getTableData("logs", "*", "users_LOGIN in ('".implode("','", $user_logins)."') order by timestamp desc");
  foreach ($result as $key => $value) {
   $value['lessons_ID'] ? $result[$key]['lesson_name'] = $lessonNames[$value['lessons_ID']] : null;
   if ($value['action'] == 'content') {
    $result[$key]['content_name'] = $contentNames[$value['comments']];
   } else if ($value['action'] == 'tests' || $value['action'] == 'test_begin') {
    $result[$key]['content_name'] = $testNames[$value['comments']];
   }
  }
  $smarty -> assign("T_USER_LOG", $result);
  $traffic = array();
  $traffic['lessons'] = array();
  $allStats = EfrontStats :: getUsersTimeAll();
  //$allStats = EfrontStats :: getUsersTimeAll($from, $to);
  $result = EfrontLesson::getLessons();
  $probed_lessons = array();
  foreach ($result as $value) {
   $probed_lessons[$value['id']] = array("lessons_ID" => $value['id'], "lessons_name" => $value['name'], "active" => $value['active']);
  }
  foreach ($probed_lessons as $id => $lesson) {
   $userTraffic = $allStats[$id];
   //$userTraffic = EfrontStats :: getUsersTime($id, $user_logins, $from, $to);
   foreach ($user_logins as $user => $login) {
    if ($userTraffic[$login]['accesses']) {
     if (!isset($traffic['lessons'][$id])) {
      $traffic['lessons'][$id] = $userTraffic[$login];
      $traffic['lessons'][$id]['name'] = $lesson['lessons_name'];
      $traffic['lessons'][$id]['active'] = $lesson['active'];
     } else {
      $traffic['lessons'][$id]['accesses'] += $userTraffic[$login]['accesses'];
      addTime($traffic['lessons'][$id], $userTraffic[$login]);
      //$traffic['lessons'][$id]['total_seconds'] +=???????
     }
     $traffic['total_access'] += $userTraffic[$login]['accesses'];
    }
   }
  }
  //and timestamp between $from and $to
  $result = eF_getTableData("logs", "count(*)", "action = 'login' and users_LOGIN in ('".implode("','", $user_logins)."') order by timestamp");
  $traffic['total_logins'] = $result[0]['count(*)'];
  $smarty -> assign("T_USER_TRAFFIC", $traffic);
  $actions = array('login' => _LOGIN,
                               'logout' => _LOGOUT,
                               'lesson' => _ACCESSEDLESSON,
                               'content' => _ACCESSEDCONTENT,
                               'tests' => _ACCESSEDTEST,
                               'test_begin' => _BEGUNTEST,
                               'lastmove' => _NAVIGATEDSYSTEM);
  $smarty -> assign("T_ACTIONS", $actions);
  $smarty -> display($_SESSION['s_type'].'.tpl');
 } else if (isset($_GET['add_to_existing_group'])) {
  try {
   $group = new EfrontGroup($_GET['add_to_existing_group']);
   $group -> addUsers($recipients_array);
  } catch (Exception $e) {
   echo $e->getMessage();
  }
 } else if(isset($_GET['add_to_new_group'])) {
  try {
   $group = EfrontGroup::create(array("name" => $_GET['add_to_new_group']));
   $group -> addUsers($recipients_array);
   echo $group -> group['id'];
  } catch (Exception $e) {
   echo $e->getMessage();
  }
 } else if(isset($_GET['add_course'])) {
  try {
   $course = new EfrontCourse($_GET['add_course']);
   $course -> addUsers($recipients_array);
  } catch (Exception $e) {
   header("HTTP/1.0 500");
   echo $e -> getMessage().' ('.$e -> getCode().')';
  }
 }
 exit;
}
/********************** REPORTS PAGE PRESENTATION - FORM CREATION *********************/
/* Create the link to the search for course user page */
if ($currentUser -> getType() == "administrator") {
  $options = array(array('image' => '16x16/scorm.png', 'title' => _SEARCHFORUSER, 'link' => $_SESSION['s_type'].'.php?ctg=search_users' , 'selected' => true),
       array('image' => '16x16/glossary.png', 'title' => _SEARCHCOURSEUSERS, 'link' => 'administrator.php?ctg=search_courses', 'selected' => false));
 $smarty -> assign("T_TABLE_OPTIONS", $options);
}
/* Create the selection criteria form */
//$form = new HTML_QuickForm("reports_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=reports&search=1&branch_ID=".$_GET['branch_ID']."&job_description_ID=".$_GET['job_description_ID']."&skill_ID=".$_GET['skill_ID'], "", null, true);
$form = new HTML_QuickForm("reports_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=reports&search=1", "", null, true);
$form -> addElement('radio', 'criteria', null, null, 'all_criteria', 'checked = "checked" id="all_criteria" onclick="javascript:refreshResults()"');
$form -> addElement('radio', 'criteria', null, null, 'any_criteria', 'id="any_criteria" onclick="javascript:refreshResults()"');
/* Get data for creating the selects */
