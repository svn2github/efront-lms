<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if ($currentUser -> getType() != "administrator" && (($currentEmployee -> getType() != _SUPERVISOR) ||(isset($currentBranch) && !$currentEmployee -> supervisesBranch($currentBranch->branch['branch_ID']) ))) {
 $message = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
 $message_type = 'failure';
 eF_redirect("".$_SESSION['s_type'].".php?ctg=module_hcd&op=chart&message=".urlencode($message)."&message_type=".$message_type);
 exit;
}

try {
 /* Check permissions: only admins and supervisors can see branches - the supervisors only their own */
 if (isset($_GET['delete_branch'])) {
  $currentBranch = new EfrontBranch($_GET['delete_branch']);
 } else if (isset($_GET['edit_branch'])) {
  $currentBranch = new EfrontBranch($_GET['edit_branch']);
 }
} catch (EfrontBranchException $e) {
 $message = $e -> getMessage().' ('.$e -> getCode().')';
}


/*****************************************************

 ON AJAX REQUESTING the branch's job descriptions select

 **************************************************** */
if (isset($_GET['postAjaxRequest'])) {
 if (isset($_GET['getJobSelect'])) {
  $ar = $currentBranch -> createJobDescriptionsSelect($attributes);
  foreach ($ar as $val=>$element) {
   echo $val."<option>".$element."<option>";
  }
  exit;
 } else if (isset($_GET['getSupervisorsSelect'])) {
  $ar = $currentBranch -> createSupervisorsSelect();
  foreach ($ar as $val=>$element) {
   echo $val."<option>".$element."<option>";
  }
  exit;
 }
}
if (isset($_GET['delete_branch'])) { //The administrator asked to delete a branch
 try {
  $currentBranch = new EfrontBranch($_GET['delete_branch']);
  $currentBranch -> delete();
  exit;
 } catch (EfrontBranchException $e) {
  handleAjaxExceptions($e);
 }
} else if (isset($_GET['add_branch']) || isset($_GET['edit_branch'])) {
 try {
  if (isset($_GET['add_branch'])) {
   $form = new HTML_QuickForm("branch_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=branches&add_branch=1" . (isset($_GET['returntab'])?"&returntab=" . $_GET['returntab']:""), "", null, true);
  } else {
   $form = new HTML_QuickForm("branch_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=branches&edit_branch=" . $_GET['edit_branch'] , "", null, true);

   // First job is to assign the jobs Assign jobs
   if (isset($_GET['postAjaxRequest'])) {
    if (isset($_GET['add_lesson'])) {
     /* Find all employees having this skill */
     if ($_GET['insert'] == "true") {
      echo $currentBranch -> assignLesson($_GET['add_lesson']);
     } else if ($_GET['insert'] == "false") {
      echo $currentBranch -> removeLesson($_GET['add_lesson']);
     } else if (isset($_GET['addAll'])) {
      $lessons = $currentBranch -> getAllLessons();
      isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;

      foreach ($lessons as $lesson) {
       if ($lesson['branches_ID'] == "") {
        $currentBranch -> assignLesson($lesson['id']);
       }
      }
      echo "-1";
     } else if (isset($_GET['removeAll'])) {
      $lessons = $currentBranch -> getAllLessons();
      isset($_GET['filter']) ? $lessons = eF_filterData($lessons, $_GET['filter']) : null;
      foreach ($lessons as $lesson) {
       if ($lesson['branches_ID'] == $currentBranch -> branch['branch_ID']) {
        $currentBranch -> removeLesson($lesson['id']);
       }
      }
      echo "-1"; // reload
     }
     exit;

    } else if (isset($_GET['add_course'])) {
     /* Find all employees having this skill */
     if ($_GET['insert'] == "true") {
      echo $currentBranch -> assignCourse($_GET['add_course']);
     } else if ($_GET['insert'] == "false") {
      echo $currentBranch -> removeCourse($_GET['add_course']);
     } else if (isset($_GET['addAll'])) {
      $courses = $currentBranch -> getAllCourses();
      isset($_GET['filter']) ? $courses = eF_filterData($courses, $_GET['filter']) : null;

      foreach ($courses as $course) {
       if ($course['branches_ID'] == "") {
        $currentBranch -> assignCourse($course['id']);
       }
      }
      echo "-1";
     } else if (isset($_GET['removeAll'])) {
      $courses = $currentBranch -> getAllCourses();
      isset($_GET['filter']) ? $courses = eF_filterData($courses, $_GET['filter']) : null;
      foreach ($courses as $course) {
       if ($course['branches_ID'] == $currentBranch -> branch['branch_ID']) {
        $currentBranch -> removeCourse($course['id']);
       }
      }
      echo "-1"; // reload
     }
     exit;

    } else {

     // Find all employees having this skill
     if ($_GET['insert'] == "true") {
      $editedUser = EfrontUserFactory :: factory($_GET['add_employee']);
      if ($editedUser -> getType() != "administrator") {
       $editedEmployee = $editedUser -> aspects['hcd'];

       if ($_GET['default_job'] != '') {

        if ($_GET['default_job'] != $_GET['add_job'] || $_GET['default_position'] != $_GET['add_position']) {
         $old_job_description_ID = eF_getJobDescriptionId($_GET['default_job'], $_GET['edit_branch']);
         $editedEmployee = $editedEmployee -> removeJob ($old_job_description_ID);
        }
       }

       $new_job_description_ID = eF_getJobDescriptionId($_GET['add_job'], $_GET['edit_branch']);
       $editedEmployee = $editedEmployee -> addJob ($editedUser, $new_job_description_ID, $_GET['edit_branch'], $_GET['add_position']);
      } else {
       header("HTTP/1.0 500 ");
       echo (_ADMINISTRATORSNOJOBDESCRIPTIONS);
       exit;
      }
     } else if ($_GET['insert'] == "false") {

      $editedUser = EfrontUserFactory :: factory($_GET['add_employee']);
      $editedEmployee = $editedUser -> aspects['hcd'];
      $old_job_description_ID = eF_getJobDescriptionId($_GET['add_job'], $_GET['edit_branch']);
      $editedEmployee = $editedEmployee -> removeJob ($old_job_description_ID);

     } else if (isset($_GET['addAll'] )) {

      $employees = $currentBranch -> getEmployeesWithJobs();
      // Filter all employee according to the filter
      isset($_GET['filter']) ? $employees = eF_filterData($employees,$_GET['filter']) : null;
      $jobs = $currentBranch -> getJobDescriptions();

      foreach ($employees as $employee) {
       if ($employee['job_description_ID'] == "") {
        $editedUser = EfrontUserFactory :: factory($employee['login']);
        $editedEmployee = $editedUser -> aspects['hcd'];
        if ($editedUser -> getType() != "administrator") {
         $editedEmployee = $editedEmployee -> addJob ($editedUser, $jobs[0]['job_description_ID'], $_GET['edit_branch'], 0);
        }

       }
      }
     } else if (isset($_GET['removeAll'] )) {
      $employees = $currentBranch -> getEmployeesWithJobs();
      isset($_GET['filter']) ? $employees = eF_filterData($employees,$_GET['filter']) : null;

      foreach ($employees as $employee) {
       if ($employee['job_description_ID'] != "") {
        $editedUser = EfrontUserFactory :: factory($employee['login']);
        $editedEmployee = $editedUser -> aspects['hcd'];

        $employee_jobs = $editedEmployee -> getJobs();
        $jobs = $currentBranch -> getJobDescriptions();
        foreach ($jobs as $job) {
         if (in_array($job['job_description_ID'], array_keys($employee_jobs))) {
          $editedEmployee = $editedEmployee -> removeJob ($job['job_description_ID'], $_GET['edit_branch'], $employee_jobs['role']);
         }
        }


       }
      }
     }
     $_GET['ajax'] = 1;
    }
   }

   $smarty -> assign("T_DATASOURCE_SORT_BY", 5);
   $smarty -> assign("T_DATASOURCE_SORT_ORDER", 'desc');
   $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'location', 'directions_name', 'num_lessons', 'num_skills', 'has_course'));
   if (isset($_GET['ajax']) && ($_GET['ajax'] == 'coursesTable' || $_GET['ajax'] == 'instancesTable')) {
    try {
     if ($_GET['ajax'] == 'coursesTable') {
      $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => false);
     }
     if ($_GET['ajax'] == 'instancesTable' && eF_checkParameter($_GET['instancesTable_source'], 'id')) {
      $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => $_GET['instancesTable_source']);
     }
     $courses = $currentBranch -> getBranchCoursesIncludingUnassigned($constraints);
     $dataSource = EfrontCourse :: convertCourseObjectsToArrays($courses);
     $tableName = $_GET['ajax'];
     $alreadySorted = 1;
     include("sorted_table.php");
    } catch (Exception $e) {
     handleAjaxExceptions($e);
    }
   }
   if (isset($_GET['ajax']) && $_GET['ajax'] == 'lessonsTable') {
    $lessons = $currentBranch -> getAllLessons();
    isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

    if (isset($_GET['sort'])) {
     isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
     $lessons = eF_multiSort($lessons, $_GET['sort'], $order);
    }

    if (isset($_GET['filter'])) {
     $lessons = eF_filterData($lessons, $_GET['filter']);
    }
    $smarty -> assign("T_LESSONS_SIZE", sizeof($lessons));
    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
     isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
     $lessons = array_slice($lessons, $offset, $limit);
    }


    $directionsTree = new EfrontDirectionsTree();
    $directionPaths = $directionsTree -> toPathString();
    $languages = EfrontSystem :: getLanguages(true);

    foreach ($lessons as $key => $lesson) {
     $obj = new EfrontLesson($lesson);
     //$lessons[$key]['link'] = $obj -> toHTMLTooltipLink(basename($_SERVER['PHP_SELF']).'?ctg=lessons&edit_lesson='.$lesson['id']);
     $lessons[$key]['direction_name'] = $directionPaths[$lesson['directions_ID']];
     $lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
     $lessons[$key]['price_string'] = $obj -> lesson['price_string'];
     //$lessons[$key]['students']	   = sizeof($obj -> getUsers('student'));
    }
    $smarty -> assign("T_LESSONS_DATA", $lessons);
    $smarty -> display($_SESSION['s_type'].'.tpl');
    exit;
   }
   if (isset($_GET['ajax']) && $_GET['ajax'] == 'branchUsersTable') {
    if ($currentEmployee -> getType() == _SUPERVISOR) {
     $sbranchesList = implode("','", $currentEmployee -> supervisesBranches);
     $exclude_admin_condition = " AND users.user_type != 'administrator'";
     $employees = eF_getTableData("users JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active, module_hcd_job_description.description, module_hcd_job_description.job_description_ID, module_hcd_branch.name as bname, module_hcd_job_description.branch_ID, module_hcd_employee_works_at_branch.supervisor", "users.archive = 0 AND module_hcd_job_description.branch_ID IN ('". $sbranchesList ."') AND module_hcd_job_description.branch_ID = '". $currentBranch -> branch['branch_ID'] . "' AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login " . $exclude_admin_condition ,"","");

    } else {
     $employees = eF_getTableData("users JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description  ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active, module_hcd_job_description.description, module_hcd_job_description.job_description_ID, module_hcd_branch.name as bname, module_hcd_job_description.branch_ID, module_hcd_employee_works_at_branch.supervisor", "users.archive = 0 AND module_hcd_job_description.branch_ID = '". $currentBranch -> branch['branch_ID'] . "' AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login" ,"","");
    }

    // Both roles are allowed to view all subbranches, if this is asked
    if ($_GET['showAllEmployees'] == 1) {
     $sbranchesList = implode("','", $currentBranch -> getAllSubbranches());
     $subbranches_employees = eF_getTableData("users JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description  ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active, module_hcd_job_description.description, module_hcd_job_description.job_description_ID, module_hcd_branch.name as bname, module_hcd_job_description.branch_ID, module_hcd_employee_works_at_branch.supervisor", "users.archive = 0 AND module_hcd_job_description.branch_ID IN ('". $sbranchesList ."') AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login " . $exclude_admin_condition ,"","");
     //pr($subbranches_employees);
     foreach ($subbranches_employees as $sub_employee) {
      $employees[] = $sub_employee;
     }
    }

          $dataSource = $employees;
    $tableName = $_GET['ajax'];
    include("sorted_table.php");
   }
   // Create ajax enabled table for employees
   if (isset($_GET['ajax']) && $_GET['ajax'] == 'branchJobsTable') {

    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
     $sort = $_GET['sort'];
     isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
    } else {
     $sort = 'login';
    }

    $employees = $currentBranch -> getEmployeesWithJobs();//employees;
    $employees = eF_multiSort($employees, $_GET['sort'], $order);

    if (isset($_GET['filter'])) {
//					// temp array used to remove the two columns that are common in all rows
//					// but appear only to the half of them... this leads to unintuitional filtering
//					$temp_array_for_filtering = array();
//					foreach ($employees as $key => $employee) {
//						unset($employee['job_select']);
//						unset($employee['position_select']);
//						$temp_array[$key] = $employee;
//					}
//
//					$temp_array = eF_filterData($temp_array, $_GET['filter']);
//					foreach ($employees as $key => $employee) {
//						if (!isset($temp_array[$key])) {
//							unset($employees[$key]);
//						}
//					}
     $employees = eF_filterData($temp_array, $_GET['filter']);
    }
    $smarty -> assign("T_EMPLOYEES_SIZE", sizeof($employees));

    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
     isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
     $employees = array_slice($employees, $offset, $limit);
    }

    $employees = $currentBranch -> createEmployeeJobsHtml($employees);
    pr($employees);
    if ($employees) { // if false, then there are no job descriptions
     $smarty -> assign("T_EMPLOYEES", $employees);
    } else {
     $smarty -> assign("T_EMPLOYEES_SIZE", 0);
     $smarty -> assign("T_NOBRANCHJOBSERROR", 1);
    }
    $smarty -> display($_SESSION['s_type'].'.tpl');
    exit;
   }
  }

  // Hidden for maintaining the previous_url value
  $form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');
  $previous_url = basename(getenv('HTTP_REFERER'));

  if ($position = strpos($previous_url, "&message")) {
   $previous_url = substr($previous_url, 0, $position);
  }
  $form -> setDefaults(array('previous_url' => $previous_url));

  $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
  $form -> addElement('text', 'branch_name', _BRANCHNAME, 'class = "inputText"');

  // Dates
  $days = array();
  $days['0'] = _DAY;
  for ($i = 1; $i < 32; $i++) {
   $days[$i] = $i;
  }

  $months = array();
  $months['0'] = _MONTH;
  for ($i = 1; $i <= 12; $i++) {
   $months[$i] = $i;
  }

  $years = array();
  $years['0'] = _YEAR;
  for ($i = 2008; $i < 2015; $i++) {
   $years[$i] = $i;
  }

  $form -> addRule('branch_name', _THEFIELD.' '._BRANCHNAME.' '._ISMANDATORY, 'required', null, 'client');
  $form -> addElement('text', 'address', _ADDRESS, 'class = "inputText"');
  $form -> addElement('text', 'city', _CITY, 'class = "inputText"');
  $form -> addElement('text', 'country', _COUNTRY, 'class = "inputText"');
  $form -> addElement('text', 'telephone', _TELEPHONE, 'class = "inputText"');
  $form -> addElement('text', 'email', _EMAIL, 'class = "inputText"');

  /* Get data */
  if (isset($_GET['edit_branch'])) {
   /* Set the link to the details of the father branch */
   $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $currentBranch -> branch['father_branch_ID'] . "\"";
   $smarty -> assign("T_BRANCH_NAME", $currentBranch -> branch['name']);
  }

  // Variable used to forbid the appearance of the link appearing for the lense;
  $forbidden_link = "";
  /* Select or possible father branches (the ones this supervisor manages) or all (if user is administrator)*/
  if ($currentEmployee -> getType() == _SUPERVISOR) {
   $father_branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","branch_ID IN (" . implode(",",$currentEmployee -> supervisesBranches). ")");

   // Show only existing branches
   $only_existing = 1;
   if ($currentBranch && !$currentEmployee -> supervisesBranch($currentBranch -> branch['father_branch_ID'])) {
    if ($currentBranch -> branch['father_branch_ID'] == 0) {
     $only_existing = 0;
    }
    $father_branches[] = array('branch_ID' => $currentBranch -> branch['father_branch_ID'], 'name' => $currentBranch -> branch['father_name'], 'father_branch_ID' => '');
    $forbidden_link = $currentBranch -> branch['father_branch_ID'];
    $smarty -> assign ("T_FORBID_LINK", 1);
   }
  } else {
   $father_branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");
   // Show all branches
   $only_existing = 0;
  }

  if (!empty($father_branches)) {
   if (isset($_GET['edit_branch'])) {
    $smarty -> assign("T_FATHER_BRANCH_ID", $currentBranch -> branch['father_branch_ID']);
    $smarty -> assign("T_FATHER_BRANCH_INFO", $details_link);
   }

   $smarty -> assign("T_SHOWFATHER", 1);

   $form -> addElement('select', 'fatherBranch' , _FATHERBRANCH, eF_createBranchesTreeSelect($father_branches,$only_existing),'class = "inputText"  id="fatherBranch" onchange="javascript:change_branch(\'fatherBranch\',\'details_link\',\''.$forbidden_link.'\')"');

   // If add_branch request coming from another branch subbranches menu, pre-enter the fatherBranch form
   if (isset($_GET['add_branch'])) {
    if (isset($_GET['add_branch_to'])) {
     $form -> setDefaults(array( 'fatherBranch' => $_GET['add_branch_to']));
     $smarty -> assign("T_FATHER_BRANCH_ID", $_GET['add_branch_to']);
     $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $_GET['add_branch_to'] . "\"";
     $smarty -> assign("T_FATHER_BRANCH_INFO", $details_link);
    }
    else if ($currentEmployee -> getType() == _SUPERVISOR) {
     $_GET['add_branch_to'] = $father_branches[0]['branch_ID']; // keep the $_GET variable for checking at the smarty side
     $form -> setDefaults(array( 'fatherBranch' => $_GET['add_branch_to']));
     $smarty -> assign("T_FATHER_BRANCH_ID", $_GET['add_branch_to']);
     $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $_GET['add_branch_to'] . "\"";
     $smarty -> assign("T_FATHER_BRANCH_INFO", $details_link);
    }
   }
  } else {
   $first_branch = 1;
  }

  if (isset($_GET['edit_branch'])) {
   $subbranches = $currentBranch -> getSubbranches();
   if (!empty($subbranches)) {
    $smarty -> assign("T_SUBBRANCHES", $subbranches);
   }

   // Get job descriptions with skills
   $job_descriptions = $currentBranch -> getJobDescriptions(true);
   if(!empty($job_descriptions)) {
    $smarty -> assign("T_JOB_DESCRIPTIONS", $job_descriptions);
   }

   $delete_link = array(
   array('text' => _DELETE, 'image' => "16x16/error_delete.png", 'href' => $_SESSION['s_type'].".php?ctg=module_hcd&op=branches&delete_branch=".$currentBranch -> branch['branch_ID']."&father_ID=".$currentBranch -> branch['father_branch_ID'], 'onClick' => "return confirm('"._AREYOUSUREYOUWANTTODISMISSTHEBRANCH."')", 'target' => '_self')
   );

   $smarty -> assign ("T_DELETE_LINK", $delete_link);

   $show_subbranches_activate = array(
   array('text' => _SHOWEMPLOYEESFROMSUBBRANCHES, 'image' => "16x16/question_type_one_correct.png", 'href' => 'javascript:void(0)', 'onClick' => "ajaxShowAllSubbranches();", 'target' => '_self')
   );

   $smarty -> assign ("T_SUBBRANCHES_LINK", $show_subbranches_activate);
  }

  $form -> addElement('submit', 'submit_branch_details', _SUBMIT, 'class = "flatButton"');

  if (isset($_GET['edit_branch'])) {
   $form -> setDefaults(array( 'branch_name' => $currentBranch -> branch['name'],
           'address' => $currentBranch -> branch['address'],
           'city' => $currentBranch -> branch['city'],
           'country' => $currentBranch -> branch['country'],
           'telephone' => $currentBranch -> branch['telephone'],
           'email' => $currentBranch -> branch['email']));
  }

  $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
  $renderer -> setRequiredTemplate(
   '{$html}{if $required}
    &nbsp;<span class = "formRequired">*</span>
   {/if}');
 } catch (EfrontBranchException $e) {
  $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
  $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
  $message_type = 'failure';
 }

 if ($form -> isSubmitted() && $form -> validate()) {
  $branch_content = array('name' => $form -> exportValue('branch_name'),
        'address' => $form -> exportValue('address'),
        'city' => $form -> exportValue('city'),
        'country' => $form -> exportValue('country'),
        'telephone'=> $form -> exportValue('telephone'),
        'email' => $form -> exportValue('email'));
  try {
   if (isset($_GET['add_branch'])) {
    if ($first_branch != 1) {
     $branch_content['father_branch_ID'] = $form -> exportValue('fatherBranch');
    }
    $newBranch = EfrontBranch :: createBranch($branch_content);
    $message = _SUCCSSFULLYCREATEDBRANCH;
    $message_type = 'success';
   } elseif (isset($_GET['edit_branch'])) {
    $branch_content['father_branch_ID'] = $form->exportValue('fatherBranch');
    $currentBranch -> updateBranchData($branch_content);
    $message = _BRANCHDATAUPDATED;
    $message_type = 'success';
   }
  } catch (EfrontBranchException $e) {
   $message = $e -> getMessage().' ('.$e -> getCode().')';
  }
  // Instead of going back to the branches go the previous link
  if (isset($_GET['add_branch'])) {
   if (isset($_GET['returntab']) && $_GET['returntab'] == "basic") {
    eF_redirect(basename($form->exportValue('previous_url'))."&message=". urlencode($message) . "&message_type=" . $message_type . "&tab=basic&newId=" . $newBranch ->branch['branch_ID']);
   } else {
    eF_redirect(basename($form->exportValue('previous_url'))."&message=". urlencode($message) . "&message_type=" . $message_type . "&tab=" . (isset($_GET['returntab'])?$_GET['returntab']:"subbranches"));
   }
  } else {
   eF_redirect($_SESSION['s_type'].".php?ctg=module_hcd&op=branches&edit_branch=".$_GET['edit_branch']."&message=". urlencode($message) . "&message_type=" . $message_type);
  }
  exit;
 }

 $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
 $form -> setRequiredNote(_REQUIREDNOTE);
 $form -> accept($renderer);
 $smarty -> assign('T_BRANCH_FORM', $renderer -> toArray());

} else {

 /*Select branches-father branches and employees number sorted by father_branch_ID (so that the ones with no father ID will be on the top)*/
 try {
  if ($_SESSION['s_type'] == "administrator" || $currentEmployee -> getType() == _SUPERVISOR) {
   $permission_to_change = 1;
   $smarty -> assign("T_CHANGE_RIGHTS", $permission_to_change);
  }

  if (isset($_GET['ajax']) && $_GET['ajax'] == 'branchesTable') {
   if ($_SESSION['s_type'] == "administrator") {
    $branches = eF_getTableData("(module_hcd_branch LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_branch.branch_ID = module_hcd_employee_works_at_branch.branch_ID AND module_hcd_employee_works_at_branch.assigned = '1') LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID GROUP BY module_hcd_branch.branch_ID ORDER BY branch1.branch_ID", "module_hcd_branch.branch_ID, module_hcd_branch.name, module_hcd_branch.city, module_hcd_branch.address,  count(users_login) as employees,  branch1.branch_ID as father_ID, branch1.name as father, supervisor","");
   } else {
    $branches = eF_getTableData("(module_hcd_branch LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_branch.branch_ID = module_hcd_employee_works_at_branch.branch_ID AND module_hcd_employee_works_at_branch.assigned = '1') LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID WHERE module_hcd_branch.branch_ID IN (".$_SESSION['supervises_branches'].") GROUP BY module_hcd_branch.branch_ID ORDER BY branch1.branch_ID", "module_hcd_branch.name, module_hcd_branch.city, module_hcd_branch.address,  count(users_login) as employees,  module_hcd_branch.branch_ID, branch1.branch_ID as father_ID, branch1.name as father","");
   }
   $dataSource = $branches;
   $tableName = $_GET['ajax'];
   include("sorted_table.php");
  }
 } catch (EfrontBranchException $e) {
  $message = $e -> getMessage().' ('.$e -> getCode().')';
 }

}

?>
