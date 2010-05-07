<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

/* Reports are at this point developed as "search employee" module. They report which employee(s) fulfill some criteria */

/* Check permissions: only administrator and supervisors can see the reports - the supervisors for the employees that work in the branches they supervise */
if ($currentUser -> getType() != "administrator" && $currentEmployee -> getType() != _SUPERVISOR) {
 $message = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
 $message_type = 'failure';
 eF_redirect("".$_SESSION['s_type'].".php?ctg=module_hcd&op=reports&message=".urlencode($message)."&message_type=".$message_type);
 exit;
}

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
 if (((isset($_GET['branch_ID']) && $_GET['branch_ID']!="" && $_GET['branch_ID']!="0") || (isset($_GET['job_description_ID']) && $_GET['job_description_ID'] !="0" && $_GET['job_description_ID']!="") || ( (isset($_GET['skill_ID']) && $_GET['skill_ID']!= "" && $_GET['skill_ID']!=0) || (isset($_GET['other_skills']) && $_GET['other_skills'] != "")))) {
  // Check or not the include subbranches checkbox
  if ($_GET['include_sb'] == "true" || $_POST['include_subbranches']) {
   $include_sb = 1;
  } else {
   $include_sb = 0;
  }
  /* branch_ID equals zero when ANY is selected */
  if ($_GET['branch_ID'] != 0) {

   if ($_GET['branch_ID'] > 0) {
    if ($include_sb) {
     $branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");
     $subbranches = eF_subBranches($_GET['branch_ID'], $branches);

     $subbranches[] = $_GET['branch_ID'];
     $branches_list = implode("','",$subbranches);

     $where_part = "module_hcd_employee_works_at_branch.branch_ID IN ('" . $branches_list . "')";
    } else {
     $where_part = "module_hcd_employee_works_at_branch.branch_ID = '" . $_GET['branch_ID'] . "'";
    }
   } else {


    $leavesArray = eF_getAllBranchLeaves();
    if ($_GET['branch_ID'] == -1) {
     $branches_list = implode("','", $leavesArray);
    } else {

     $branches_list = implode("','", $leavesArray);
     $father_branchesArray = eF_getTableDataFlat("module_hcd_branch as branches JOIN module_hcd_branch as father_branches ON branches.father_branch_ID = father_branches.branch_ID", "father_branches.branch_ID", "branches.branch_ID IN ('".$branches_list."')");
     $father_branchesArray = $father_branchesArray['branch_ID'];
     if ($include_sb) {
      $branches_list = implode("','", array_merge($leavesArray, $father_branchesArray));
     } else {
      $branches_list = implode("','", $father_branchesArray);
     }
    }
    $where_part = "module_hcd_employee_works_at_branch.branch_ID IN ('" . $branches_list . "')";

   }

   $employees_data1 = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.users_login = users.login AND module_hcd_employee_works_at_branch.assigned = '1' LEFT OUTER JOIN module_hcd_job_description ON module_hcd_job_description.job_description_ID = module_hcd_employee_has_job_description.job_description_ID", "users.*", $where_part,"","login");
   foreach ($employees_data1 as $empl1) {
    $log = $empl1['login'];
    $employees1[$log] = $empl1;
   }
  }

  if ($_GET['job_description_ID'] != "0") {
   $where_part = "module_hcd_job_description.description = '" . $_GET['job_description_ID'] . "'";
   $employees_data2 = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN LEFT OUTER JOIN module_hcd_job_description ON module_hcd_job_description.job_description_ID = module_hcd_employee_has_job_description.job_description_ID", "users.*", $where_part,"","login");
   foreach ($employees_data2 as $empl2) {
    $log = $empl2['login'];
    $employees2[$log] = $empl2;
   }
  }


  if (($_GET['skill_ID'] != 0) || (isset($_GET['other_skills']) && $_GET['other_skills'] != "")) {

   $skills_to_be_found = array();
   if ($_GET['skill_ID'] != 0) {
    $skills_to_be_found[$_GET['skill_ID']] = $_GET['skill_ID'];
   }

   if (isset($_GET['other_skills']) && $_GET['other_skills'] != "") {
    $other_skills = explode("_", $_GET['other_skills']);
    foreach ($other_skills as $skill) {
     $skills_to_be_found[$skill] = $skill;
    }
   }

   $where_part = "";

   $basic_condition = " EXISTS (SELECT login FROM module_hcd_employee_has_skill WHERE users_login = login AND ";
   if ($_GET['all'] == "true") {
    $connector = "AND";
   } else {
    $connector = "OR";
   }
   foreach ($skills_to_be_found as $skill) {
    if ($where_part == "") {
     $where_part = $basic_condition . "skill_ID = " . $skill . ") ";
    } else {
     $where_part .= " " . $connector . $basic_condition . " skill_ID = " . $skill . ") ";
    }

   }

   //skill_ID= '" .  . "'";
   $employees_data3 = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_skill ON module_hcd_employee_has_skill.users_login = users.login", "users.*", $where_part,"","login");
   foreach ($employees_data3 as $empl3) {
    $log = $empl3['login'];
    $employees3[$log] = $empl3;
   }
  }


  if ($_GET['all'] == "false") {
   if ($employees1) {
    $employees = $employees1;
   }
   if ($employees2) {
    if (!$employees1) {
     $employees = $employees2;
    } else {
     $employees = array_merge($employees1,$employees2);
    }
   }
   if ($employees3) {
    if (!$employees1 && !$employees2) {
     $employees = $employees3;
    } else {
     $employees = array_merge($employees,$employees3);
    }
   }

  } else {
   if ($employees1) {
    $employees = $employees1;
   } else {
    // No employee was found while one should => return empty array
    if ($_GET['branch_ID'] != 0) {
     $employees2 = 0;
     $employees3 = 0;
    }
   }

   if ($employees2) {
    if ($_GET['branch_ID'] == 0) {
     $employees = $employees2;
    } else {
     $employees = array_intersect_assoc($employees1,$employees2);
    }
   } else {
    // No employee was found while one should => return empty array
    if ($_GET['job_description_ID'] != "0") {
     $employees = array();
     $employees3 = 0;
    }
   }
   if ($employees3) {
    if ($_GET['branch_ID'] == 0 && $_GET['job_description_ID'] == "0") {
     $employees = $employees3;
    } else {
     $employees = array_intersect_assoc($employees,$employees3);
    }
   } else {
    // No employee was found while one should => return empty array
    if ($_GET['skill_ID'] != "0") {
     $employees = array();
    }
   }

  }


 } else if (isset($_GET['login']) || isset($_POST['login'])) {
  $employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN", "users.*","","","login");
 }

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
  $datesFields = array("timestamp", "hired_on" , "left_on");

  /* Get all employees fulfilling the "advanced criteria" */
  // Need to create the criteria - we could check the field names of the current user object
  $criteria = array_merge(array_keys($currentUser -> user), array_keys($currentUser -> aspects['hcd'] -> employee));
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
       case 2: $sign = "<"; break;
       case 3: $sign = "="; break;
       default: $sign = ">";
      }

      if ($sql_query != $list) {
       $sql_query .= $preposition . " (($field IS NOT NULL) AND ($field $sign $value)) ";
      } else {
       $sql_query .= $preposition . " ((($field IS NOT NULL) AND ($field $sign $value)) ";
      }

     } else {
      if ($sql_query != $list) {
       $sql_query .= $preposition . " ($field LIKE '%$value%') ";
      } else {
       $sql_query .= $preposition . " (($field LIKE '%$value%') ";
      }
     }
     $found_field = 1;

    }
   }
  }

//		if ($found_field) {
//			$sql_query .= ")";
//			$found_field = 0;
//		}

  // Custom fields management
  if ($found_field) {
   $sql_query .= ")";
  }
  /*************** THE SEARCH QUERY ****************/
//		echo $sql_query."<Br>";		
  $result = eF_getTableDataFlat("users LEFT OUTER JOIN module_hcd_employees ON users.login = module_hcd_employees.users_login","login", $sql_query . " LIMIT 100");
  //pr($result);
  $k = 0;
  /* Get the intersection of the two arrays */
  foreach ($employees as $key => $employee) {
   if (!in_array($employee['login'], $result['login'])) {
    unset($employees[$key]);
   }
   $k++;
  }
 }
 /* Get employee jobs */
 $recipients_array = array();
 foreach ($employees as $key => $employee) {
  $recipients_array[] = $employee['login'];
  $temp_employee = EfrontEmployeeFactory :: factory($employee['login']);
  $employees[$key]['jobs'] = $temp_employee -> getJobs();
  $employees[$key]['jobs_num'] = sizeof($employees[$key]['jobs']);
  //pr($employees[$key]['jobs']);
  // Calculate the size of the div for this employee
  $maxlen = 0;
  foreach ($employees[$key]['jobs'] as $job) {
   if (($tempsump = strlen($job['description']) + strlen($job['name'])) > $maxlen) {
    $maxlen = $tempsum;
   }
  }
  $employees[$key]['div_size'] = ($maxlen + strlen(_ATBRANCH) + 2) * 15 ; // length of _ATBRANCH + 2 spaces - formula chars*size_per_char=20 / 2
  if ($employees[$key]['div_size'] > 400) {
   $employees[$key]['div_size'] = 400;
  }
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
 $options = array(array('image' => '16x16/scorm.png', 'title' => _SEARCHFOREMPLOYEE, 'link' => $_SESSION['s_type'].'.php?ctg=module_hcd&op=reports' , 'selected' => true),
 array('image' => '16x16/glossary.png', 'title' => _SEARCHCOURSEUSERS, 'link' => 'administrator.php?ctg=search_courses', 'selected' => false));
 $smarty -> assign("T_TABLE_OPTIONS", $options);
 /*

	 $search_course_user = array(

	 array('text' => _SEARCHCOURSEUSERS, 'image' => "16x16/", 'href' => "", 'title' => _SEARCHCOURSEUSERS));

	 $smarty -> assign ("T_SEARCH_COURSE_USER", $search_course_user);

	 */
}
/* Create the selection criteria form */
$form = new HTML_QuickForm("reports_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=reports&search=1&branch_ID=".$_GET['branch_ID']."&job_description_ID=".$_GET['job_description_ID']."&skill_ID=".$_GET['skill_ID'], "", null, true);
$form -> addElement('radio', 'criteria', null, null, 'all_criteria', 'checked = "checked" id="all_criteria" onclick="javascript:refreshResults()"');
$form -> addElement('radio', 'criteria', null, null, 'any_criteria', 'id="any_criteria" onclick="javascript:refreshResults()"');
/* Get data for creating the selects */
/* Braches (in hierarchical form) */
$branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");
$branches_list = eF_createBranchesTreeSelect($branches, 6);
$branches_list[0] = _DONTTAKEINTOACCOUNT;
$form -> addElement('select', 'search_branch', _WORKINGATBRANCH, $branches_list, 'id = "search_branch" class = "inputSelectMed" onchange="javascript:refreshResults()"');
// If a branch is selected then the form will reload on clicking the checkbox.
if (isset($_GET['branch_ID'])) {
 $onclick_event = ' onclick = "" ';
} else {
 $onclick_event = '';
}
$form -> addElement('advcheckbox', 'include_subbranches', _INCLUDESUBBRANCHES, null, 'class = "inputCheckbox" id="include_subbranchesId" onClick="javascript:includeSubbranches()"');
/* Job descriptions (all different job descriptions irrespective of the branch they belong to) */
if (isset($_GET['branch_ID']) && $_GET['branch_ID']!="" && $_GET['branch_ID']>0) {
 $activeBranch = new EfrontBranch($_GET['branch_ID']);
 $job_description_list = $activeBranch -> createJobDescriptionsSelect();
 $job_description_list[0] = _DONTTAKEINTOACCOUNT;
} else {
 $job_descriptions = eF_getTableData("module_hcd_job_description", "distinct description","");
 $job_description_list = array("0" => _DONTTAKEINTOACCOUNT);
 foreach ($job_descriptions as $job_description) {
  $log = $job_description['description'];
  $job_description_list["$log"] = $job_description['description'];
 }
}
$form -> addElement('select', 'search_job_description', _WITHJOBDESCRIPTION, $job_description_list, 'id = "search_job_description" class = "inputSelectMed" onchange="javascript:refreshResults()"');
/* Skills */
$skills = eF_getTableData("module_hcd_skills", "skill_ID, description","");
$skills_list = array("0" => _DONTTAKEINTOACCOUNT);
foreach ($skills as $skill) {
 $log = $skill['skill_ID'];
 $skills_list["$log"] = $skill['description'];
}
$form -> addElement('select', 'search_skill', _WITHSKILL, $skills_list, 'id = "search_skill" class = "inputSelectMed" onchange="javascript:refreshResults()"');
$form -> addElement('select', 'search_skill_template' , null, $skills_list ,'id="search_skill_row" class = "inputSelectMed"  onchange="javascript:refreshResults();"');
$form -> addElement('submit', 'submit_report', _SUBMIT, 'class = "flatButton"');
/* For advanced search form: All information that regard employees (taken from the main form) */
$form -> addElement('text', 'new_login', _LOGIN, 'class = "inputText" id="new_login" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'name', _FIRSTNAME, 'class = "inputText" id="name" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'surname', _SURNAME, 'class = "inputText" id="surname" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'email', _EMAILADDRESS, 'class = "inputText" id="email" onChange="javascript:setAdvancedCriterion(this);"');
$roles = eF_getTableDataFlat("user_types", "*");
$roles_array[''] = "";
$roles_array['student'] = _STUDENT;
$roles_array['professor'] = _PROFESSOR;
// Only the administrator may assign administrator rights
if ($currentUser -> getType() == "administrator") {
 $roles_array['administrator'] = _ADMINISTRATOR;
}
for ($k = 0; $k < sizeof($roles['id']); $k++) {
 if ($roles['active'][$k] == 1 || (isset($editedUser) && $editedUser -> user['user_types_ID'] == $roles['id'][$k])) { //Make sure that the user's current role will be listed, even if it's deactivated
  $roles_array[$roles['id'][$k]] = $roles['name'][$k];
 }
}
$form -> addElement('select', 'user_type', _USERTYPE, $roles_array, 'id="user_type" onChange="javascript:setAdvancedCriterion(this);"');
/*

 $roles = eF_getTableDataFlat("user_types", "user_type", "active=1");



 $roles_array['']              = "";

 $roles_array['student']       = _STUDENT;

 $roles_array['professor']     = _PROFESSOR;

 $roles_array['administrator'] = _ADMINISTRATOR;



 for ($k = 0; $k < sizeof($roles['user_type']); $k++){

 $roles_array[$roles['user_type'][$k]] = $roles['user_type'][$k];

 }

 $form -> addElement('select', 'user_type', _USERTYPE, $roles_array, 'id="user_type" onChange="javascript:setAdvancedCriterion(this);"');

 */
$form -> addElement('advcheckbox', 'active', _ACTIVE, null, ' id ="active" class = "inputCheckbox" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'registration', _REGISTRATIONDATE, 'class = "inputText" id="timestamp" onChange="javascript:setAdvancedCriterion(this);"');
// Permanent data of personal records of employees
$form -> addElement('text', 'father', _FATHERNAME, 'class = "inputText" id="father" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('select', 'sex' , _GENDER, array("" => "", "0" => _MALE, "1" => _FEMALE), 'class = "inputText" id="sex" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'birthday', _BIRTHDAY, 'class = "inputText" id="birthday" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'birthplace',_BIRTHPLACE , 'class = "inputText" id="birthplace" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'birthcountry', _BIRTHCOUNTRY, 'class = "inputText" id="birthcountry" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'mother_tongue', _MOTHERTONGUE, 'class = "inputText" id="mother_tongue" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'nationality', _NATIONALITY, 'class = "inputText" id="nationality" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'address', _ADDRESS, 'class = "inputText" id="address" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'city', _CITY, 'class = "inputText" id="city" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'country', _COUNTRY, 'class = "inputText" id="country" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'homephone', _HOMEPHONE, 'class = "inputText" id="homephone" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'mobilephone', _MOBILEPHONE, 'class = "inputText" id="mobilephone" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'office', _OFFICE, 'class = "inputText" id="office" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'company_internal_phone', _COMPANYINTERNALPHONE, 'class = "inputText" id="company_internal_phone" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'afm', _VATREGNUMBER, 'class = "inputText" id="afm" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'doy', _TAXOFFICE, 'class = "inputText" id="doy" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'police_id_number', _POLICEIDNUMBER, 'class = "inputText" id="police_id_number" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'passport_data', _PASSPORTDATA, 'class = "inputText" id="passport_data" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('advcheckbox', 'driving_licence', _DRIVINGLICENSE, null, 'class = "inputCheckbox" id="driving_licence" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'work_permission_data', _WORKPERMISSIONDATA, 'class = "inputText" id="work_permission_data" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('advcheckbox', 'national_service_completed', _NATIONALSERVICECOMPLETED, null, 'class = "inputCheckbox" id="national_service_completed" onChange="javascript:setAdvancedCriterion(this);"');
// Non permanent data
$form -> addElement('text', 'employement_type', _EMPLOYMENTTYPE, 'class = "inputText" id="employement_type" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'hired_on', _HIREDON, 'class = "inputText" id="hired_on" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'left_on', _LEFTON, 'class = "inputText" id="left_on" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'wage', _WAGE, 'class = "inputText" id="wage" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('select', 'marital_status', _MARITALSTATUS, array("" => "", "0" => _SINGLE, "1" => _MARRIED),'class = "inputText" id="marital_status" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('advcheckbox', 'transport', _TRANSPORTMEANS, null, ' id ="transport" class = "inputCheckbox" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'bank', _BANK, 'class = "inputText" id="bank" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('text', 'bank_account', _BANKACCOUNT, 'class = "inputText" id="bank_account" onChange="javascript:setAdvancedCriterion(this);"');
$form -> addElement('select', 'way_of_working', _WAYOFWORKING, array("" => "", "0" => _FULLTIME, "1" => _PARTTIME),'class = "inputText" id="way_of_working" onChange="javascript:setAdvancedCriterion(this);"');
//   $form -> addElement('submit', 'submit_personal_details', _REGISTERADVANCEDSEARCHFIELDS, 'class = "flatButton"');
/* The default values are either posted ($POST array) when the submit button 'submit_personal_details' is used, or gotten ($GET array) on page

 reload, which occurs every time each of the branches,jobs,skills selects changes its value    */
$form -> setDefaults(array( 'new_login' => $_POST['login']?$_POST['login']:$_GET['login'],
                                                  'name' => $_POST['name']?$_POST['name']:$_GET['name'],
                                                  'surname' => $_POST['surname']?$_POST['surname']:$_GET['surname'],
                                                  'email' => $_POST['email']?$_POST['email']:$_GET['email'],
                                                  'active' => $_POST['active']?$_POST['active']:$_GET['active'],
                                                  'registration' => $_POST['registration']?$_POST['registration']:$_GET['registration'],
                                                  'wage' => $_POST['wage']?$_POST['wage']:$_GET['wage'],
                                                  'hired_on' => $_POST['hired_on']?$_POST['hired_on']:$_GET['hired_on'],
                                                  'left_on' => $_POST['left_on']?$_POST['left_on']:$_GET['left_on'],
                                                  'address' => $_POST['address']?$_POST['address']:$_GET['address'],
                                                  'city' => $_POST['city']?$_POST['city']:$_GET['city'],
                                                  'country' => $_POST['country']?$_POST['country']:$_GET['country'],
                                                  'father' => $_POST['father']?$_POST['father']:$_GET['father'],
                                                  'homephone' => $_POST['homephone']?$_POST['homephone']:$_GET['homephone'],
                                                  'mobilephone' => $_POST['mobilephone']?$_POST['mobilephone']:$_GET['mobilephone'],
                                                  'sex' => $_POST['sex']?$_POST['sex']:$_GET['sex'],
                                                  'birthday' => $_POST['birthday']?$_POST['birthday']:$_GET['birthday'],
                                                  'birthplace' => $_POST['birthplace']?$_POST['birthplace']:$_GET['birthplace'],
                                                  'birthcountry' => $_POST['birthcountry']?$_POST['birthcountry']:$_GET['birthcountry'],
                                                  'mother_tongue' => $_POST['mother_tongue']?$_POST['mother_tongue']:$_GET['mother_tongue'],
                                                  'nationality' => $_POST['nationality']?$_POST['nationality']:$_GET['nationality'],
                                                  'company_internal_phone' => $_POST['company_internal_phone']?$_POST['company_internal_phone']:$_GET['company_internal_phone'],
                                                  'office' => $_POST['office']?$_POST['office']:$_GET['office'],
                                                  'doy' => $_POST['doy']?$_POST['doy']:$_GET['doy'],
                                                  'afm' => $_POST['afm']?$_POST['afm']:$_GET['afm'],
                                                  'police_id_number' => $_POST['police_id_number']?$_POST['police_id_number']:$_GET['police_id_number'],
                                                  'driving_licence' => $_POST['driving_licence']?$_POST['driving_licence']:$_GET['driving_licence'],
                                                  'work_permission_data' => $_POST['work_permission_data']?$_POST['work_permission_data']:$_GET['work_permission_data'],
                                                  'national_service_completed' => $_POST['national_service_completed']?$_POST['national_service_completed']:$_GET['national_service_completed'],
                                                  'employement_type' => $_POST['employement_type']?$_POST['employement_type']:$_GET['employement_type'],
                                                  'bank' => $_POST['bank']?$_POST['bank']:$_GET['bank'],
                                                  'bank_account' => $_POST['bank_account']?$_POST['bank_account']:$_GET['bank_account'],
                                                  'marital_status' => $_POST['marital_status']?$_POST['marital_status']:$_GET['marital_status'],
                                                  'transport' => $_POST['transport']?$_POST['transport']:$_GET['transport'],
                                                  'way_of_working' => $_POST['way_of_working']?$_POST['way_of_working']:$_GET['way_of_working'],
                                                  'user_type' => $_POST['user_type']?$_POST['user_type']:$_GET['user_type']));
// Dates management - search needs to know which fields are dates
$datesFields = array("timestamp", "hired_on" , "left_on");
// Custom fields management
$smarty -> assign("T_DATES_SEARCH_CRITERIA", implode(",", $datesFields));
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$renderer -> setRequiredTemplate(
            '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
// Management of the 'send email to all found' link icon on the top right of the table
// During page load create the item
$mass_operations = array(array('id' => 'groupUsersId', 'text' => _SETFOUNDEMPLOYEESINTOGROUP, 'image' => "16x16/users.png", 'href' => "javascript:void(0);", "onClick" => "eF_js_showDivPopup('"._SETFOUNDEMPLOYEESINTOGROUP."', 0, 'insert_into_group')", 'target' => 'POPUP_FRAME'),
        array('id' => 'sendToAllId', 'text' => _SENDMESSAGETOALLFOUNDEMPLOYEES, 'image' => "16x16/mail.png", 'href' => "javascript:void(0);", "onClick" => "this.href='".$currentUser->getType().".php?ctg=messages&add=1&recipient='+document.getElementById('usersFound').value;eF_js_showDivPopup('"._SENDMESSAGE."', 2)", 'target' => 'POPUP_FRAME'));
$smarty -> assign("T_SENDALLMAIL_LINK", $mass_operations);
$form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
$form -> setRequiredNote(_REQUIREDNOTE);
$form -> accept($renderer);
$smarty -> assign('T_REPORT_FORM', $renderer -> toArray());
// Popup to set to custom group form
$group_form = new HTML_QuickForm("insert_into_groups_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=reports&search=1&branch_ID=".$_GET['branch_ID']."&job_description_ID=".$_GET['job_description_ID'], "", null, true);
$groups = array("0" => _INSERTINTONEWGROUP);
$groupsResult = EfrontGroup::getGroups();
foreach ($groupsResult as $group) {
 $groups[$group['id']] = $group['name'];
}
$group_form -> addElement('select', 'existing_group', _INSERTINTOEXISTINGGROUP, $groups, 'id = "existing_group_id" class = "inputSelectMed" onchange="javascript:updateNewGroup(this, \'new_group_id\')"');
$group_form -> addElement('text', 'new_group', _NEWGROUPNAME, 'class = "inputText" id="new_group_id" onChange="javascript:$(\'existing_group_id\').value = 0;"');
$group_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
$group_form -> setRequiredNote(_REQUIREDNOTE);
$group_form -> accept($renderer);
$smarty -> assign('T_INSERT_INTO_GROUP_POPUP_FORM', $renderer -> toArray());
?>
