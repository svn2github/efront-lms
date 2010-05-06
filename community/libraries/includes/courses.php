<?php
//This file cannot be called directly, only included.
define("_ONLYXCANBEAPPLIEDATATIME", "Only %x entities where processed, which is the limit for this operation");

if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

$loadScripts[] = 'includes/courses';

if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] == 'hidden') {
 eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
} else if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
 $_change_ = false;
} else {
 $_change_ = true;
}
$smarty -> assign("_change_", $_change_);

if (isset($_GET['delete_course']) && eF_checkParameter($_GET['delete_course'], 'id')) {
 try {
  if (!$_change_) {
   throw new Exception(_UNAUTHORIZEDACCESS);
  }
  $course = new EfrontCourse($_GET['delete_course']);
  $course -> delete();
 } catch (Exception $e) {
     handleAjaxExceptions($e);
 }
 exit;
} elseif (isset($_GET['archive_course']) && eF_checkParameter($_GET['archive_course'], 'login')) { //The administrator asked to delete a course
 try {
  if (!$_change_) {
   throw new Exception(_UNAUTHORIZEDACCESS);
  }
  $course = new Efrontcourse($_GET['archive_course']);
  $course -> archive();
 } catch (Exception $e) {
     handleAjaxExceptions($e);
 }
 exit;
} elseif (isset($_GET['deactivate_course']) && eF_checkParameter($_GET['deactivate_course'], 'id')) {
 try {
  if (!$_change_) {
   throw new Exception(_UNAUTHORIZEDACCESS);
  }
  $course = new EfrontCourse($_GET['deactivate_course']);
  $course -> course['active'] = 0;
  $course -> persist();
  echo "0";
 } catch (Exception $e) {
     handleAjaxExceptions($e);
 }
 exit;
} elseif (isset($_GET['activate_course']) && eF_checkParameter($_GET['activate_course'], 'id')) {
 try {
  if (!$_change_) {
   throw new Exception(_UNAUTHORIZEDACCESS);
  }
  $course = new EfrontCourse($_GET['activate_course']);
  $course -> course['active'] = 1;
  $course -> persist();
  echo "1";
 } catch (Exception $e) {
     handleAjaxExceptions($e);
 }
 exit;
}
//Handle sorted tables actions
else if (isset($_GET['ajax']) && isset($_GET['edit_course']) && $_change_) {
 try {
  $editCourse = new EfrontCourse($_GET['edit_course']);
  $smarty -> assign('T_EDIT_COURSE', $editCourse);

  //Perform ajax operations
  if ($_GET['ajax'] == 'skillsTable') {
      $skills = $editCourse -> getSkills();
   $dataSource = $skills;
   $tableName = 'skillsTable';
   include("sorted_table.php");
  } else if ($_GET['ajax'] == 'lessonsTable') {

      $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true);
      $lessons = $editCourse -> getCourseLessonsIncludingUnassigned($constraints);
      $totalEntries = $editCourse -> countCourseLessonsIncludingUnassigned($constraints);
   $dataSource = EfrontLesson :: convertLessonObjectsToArrays($lessons);

      $directionsTree = new EfrontDirectionsTree();
      $directionsPaths = $directionsTree -> toPathString();
   foreach ($dataSource as $key => $value) {
       $dataSource[$key]['directionsPath'] = $directionsPaths[$value['directions_ID']];
       if ($value['instance_source']) {
        if ($value['originating_course'] == $editCourse -> course['id']) {
            $dataSource[$key]['mode'] = 'unique';
        } else {
            $dataSource[$key]['mode'] = 'shared';
        }
       }
   }

   $tableName = $_GET['ajax'];
   $alreadySorted = 1;
   $smarty -> assign("T_TABLE_SIZE", $totalEntries);
   include("sorted_table.php");

  } else if ($_GET['ajax'] == 'usersTable') {
      $roles = EfrontLessonUser :: getLessonsRoles(true);
      $smarty -> assign("T_ROLES", $roles);

         $rolesBasic = EfrontLessonUser :: getLessonsRoles();
         $smarty -> assign("T_BASIC_ROLES_ARRAY", $rolesBasic);

         $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'active' => true);
   $users = $editCourse -> getCourseUsersIncludingUnassigned($constraints);
   $totalEntries = $editCourse -> countCourseUsersIncludingUnassigned($constraints);
   $dataSource = EfrontUser :: convertUserObjectsToArrays($users);
   $tableName = $_GET['ajax'];
   $alreadySorted = 1;
   $smarty -> assign("T_TABLE_SIZE", $totalEntries);
   include("sorted_table.php");
  } else if ($_GET['ajax'] == 'instancesTable') {

      if ($editCourse -> course['instance_source']) {
          $instanceSource = new EfrontCourse($editCourse -> course['instance_source']);
          $courseInstances = $instanceSource -> getInstances();
      } else {
          $courseInstances = $editCourse -> getInstances();
      }

      foreach ($courseInstances as $key => $instance) {
          $courseInstances[$key] -> course['num_students'] = sizeof($courseInstances[$key] -> getStudentUsers());
          $courseInstances[$key] -> course['num_lessons'] = $courseInstances[$key] -> countCourseLessons();





      }
      $courseInstances = EfrontCourse :: convertCourseObjectsToArrays($courseInstances);

   $dataSource = $courseInstances;
   $tableName = 'instancesTable';
   /**Handle sorted table's sorting and filtering*/
   include("sorted_table.php");
  } elseif (isset($_GET['mode'])) {
   $editCourse -> setLessonMode($_GET['lesson'], $_GET['mode']);
  } elseif (isset($_GET['add_instance'])) {
   if ($editCourse -> course['instance_source']) { //If we are inside an instance, then consider its parent
    EfrontCourse :: createInstance($editCourse -> course['instance_source']);
   } else {
    EfrontCourse :: createInstance($editCourse -> course['id']);
   }
  } else if (isset($_GET['postAjaxRequest']) && $_GET['postAjaxRequest'] == 'lessons') {
      $editCourse -> handlePostAjaxRequestionForLessons();
  } else if (isset($_GET['postAjaxRequest']) && $_GET['postAjaxRequest'] == 'skills') {
            $editCourse -> handlePostAjaxRequestForSkills();
  } else if (isset($_GET['postAjaxRequest']) && $_GET['postAjaxRequest'] == 'users') {
      $editCourse -> handlePostAjaxRequestForUsers();
  } elseif ($_GET['ajax'] == 'confirm_user') {
   $editCourse -> confirm($_GET['user']);
  } elseif ($_GET['ajax'] == 'unconfirm_user') {
   $editCourse -> unConfirm($_GET['user']);
  }
 } catch (Exception $e) {
     handleAjaxExceptions($e);
 }
 exit;

} elseif (isset($_GET['add_course']) || isset($_GET['edit_course'])) {
 if (!$_change_) {
  eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=courses&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
 }

 if (isset($_GET['add_course'])) {
  $post_target = 'add_course=1';
 } else {
  $post_target = 'edit_course='.$_GET['edit_course'];
  $smarty -> assign("T_COURSE_OPTIONS", array(array('text' => _COURSESETTINGS, 'image' => "16x16/generic.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=courses&course=".$_GET['edit_course']."&op=course_info")));
 }

 $form = new HTML_QuickForm("add_courses_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=courses&".$post_target, "", null, true);
 $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
 $form -> addElement('text', 'name', _COURSENAME, 'class = "inputText"');
 $form -> addRule('name', _THEFIELD.' "'._COURSENAME.'" '._ISMANDATORY, 'required', null, 'client');
 //$form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');
 try {
  $directionsTree = new EfrontDirectionsTree();
  if (sizeof($directionsTree -> tree) == 0) {
   eF_redirect(basename($_SERVER['PHP_SELF']).'?ctg=directions&add_direction=1&message='.urlencode(_TOCREATECOURSEYOUMUSTFIRSTCREATECATEGORY).'&message_type=failure');
  }
  $directions = $directionsTree -> toPathString();
 } catch (Exception $e) {
     handleNormalFlowExceptions($e);
 }
 $form -> addElement('select', 'directions_ID', _DIRECTION, $directions); //Append a directions select box to the form

 if ($GLOBALS['configuration']['onelanguage'] != true) {
  $languages = EfrontSystem :: getLanguages(true, true);
  $form -> addElement('select', 'languages_NAME', _LANGUAGE, $languages);
 }

 $form -> addElement('advcheckbox', 'active', _ACTIVEFEM, null, null, array(0, 1));
 $form -> addElement('advcheckbox', 'show_catalog', _SHOWCOURSEINCATALOG, null, null, array(0, 1));
 $form -> addElement('text', 'price', _PRICE, 'class = "inputText" style = "width:50px"');
 //$form -> addElement('text', 'course_code', _COURSECODE, 'class = "inputText" style = "width:50px"');
 $form -> addElement('text', 'training_hours', _TRAININGHOURS, 'class = "inputText" style = "width:50px"');

 $recurringOptions = array(0 => _NO, 'D' => _DAILY, 'W' => _WEEKLY, 'M' => _MONTHLY, 'Y' => _YEARLY);
 $recurringDurations = array('D' => array_combine(range(1, 90), range(1, 90)),
        'W' => array_combine(range(1, 52), range(1, 52)),
        'M' => array_combine(range(1, 24), range(1, 24)),
        'Y' => array_combine(range(1, 5), range(1, 5))); //Imposed by paypal interface
 $form -> addElement('select', 'recurring', _SUBSCRIPTION, $recurringOptions, 'onchange = "$(\'duration_row\').show();$$(\'span\').each(function (s) {if (s.id.match(\'_duration\')) {s.hide();}});if (this.selectedIndex) {$(this.options[this.selectedIndex].value+\'_duration\').show();} else {$(\'duration_row\').hide();}"');
 $form -> addElement('select', 'D_duration', _DAYSCONDITIONAL, $recurringDurations['D']);
 $form -> addElement('select', 'W_duration', _WEEKSCONDITIONAL, $recurringDurations['W']);
 $form -> addElement('select', 'M_duration', _MONTHSCONDITIONAL, $recurringDurations['M']);
 $form -> addElement('select', 'Y_duration', _YEARSCONDITIONAL, $recurringDurations['Y']);
 $form -> addElement('text', 'max_users', _MAXIMUMUSERS, 'class = "inputText" style = "width:50px"');
 $form -> addElement('text', 'duration', _AVAILABLEFOR, 'style = "width:50px;"');
 $form -> addRule('duration', _THEFIELD.' "'._AVAILABLEFOR.'" '._MUSTBENUMERIC, 'numeric', null, 'client');
 if (isset($_GET['edit_course'])) {
  $editCourse = new EfrontCourse($_GET['edit_course']);
  $smarty -> assign('T_EDIT_COURSE', $editCourse);
   $form -> setDefaults($editCourse -> options);
   $form -> setDefaults($editCourse -> course);
  $form -> setDefaults(array($editCourse -> options['recurring'].'_duration' => $editCourse -> options['recurring_duration']));
 } else {
  $form -> setDefaults(array('active' => 1,
           'show_catalog' => 1,
           'price' => 0,
           'languages_NAME' => $GLOBALS['configuration']['default_language']));
 }
 if (!$_change_) {
  $form -> freeze();
 } else {
  $form -> addElement('submit', 'submit_course', _SUBMIT, 'class = "flatButton"');
  if ($form -> isSubmitted() && $form -> validate()) {
   $fields = array('languages_NAME' => $GLOBALS['configuration']['onelanguage'] ? $GLOBALS['configuration']['default_language'] : $form -> exportValue('languages_NAME'),
       'show_catalog' => $form -> exportValue('show_catalog'),
       'directions_ID' => $form -> exportValue('directions_ID'),
       'name' => $form -> exportValue('name'),
       'active' => $form -> exportValue('active'),
       //'duration'	   	 => $form -> exportValue('duration') ? $form -> exportValue('duration') : null,
       'max_users' => $form -> exportValue('max_users') ? $form -> exportValue('max_users') : null,
       'price' => $form -> exportValue('price'));
   try {
    if (isset($_GET['edit_course'])) {
     $editCourse -> course = array_merge($editCourse -> course, $fields);
     if ($courseSk = $editCourse -> getCourseSkill()) {
      eF_updateTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFCOURSE . " " .$form -> exportValue('name')), "skill_ID = " .$courseSk['skill_ID']) ;
     }
     $message = _COURSEUPDATED;
     //$redirect = basename($_SERVER['PHP_SELF']).'?ctg=courses&message='.urlencode(_COURSEUPDATED).'&message_type=success';
    } else {
     $editCourse = EfrontCourse :: createCourse($fields);
     $message = _SUCCESFULLYCREATEDCOURSE;
     $redirect = basename($_SERVER['PHP_SELF'])."?ctg=courses&edit_course=".$editCourse -> course['id']."&tab=lessons&message=".urlencode(_SUCCESFULLYCREATEDCOURSE)."&message_type=success";
    }
    $message_type = 'success';
    if ($form -> exportValue('price') && $form -> exportValue('recurring') && in_array($form -> exportValue('recurring'), array_keys($recurringOptions))) {
     $editCourse -> options['recurring'] = $form -> exportValue('recurring');
     if ($editCourse -> options['recurring']) {
      $editCourse -> options['recurring_duration'] = $form -> exportValue($editCourse -> options['recurring'].'_duration');
     }
    } else {
     unset($editCourse -> options['recurring']);
    }
    //$editCourse -> course['instance_source'] OR $editCourse -> options['course_code'] = $form -> exportValue('course_code');	//Instances don't have a code of their own
    $editCourse -> options['training_hours'] = $form -> exportValue('training_hours');
    $editCourse -> options['duration'] = $form -> exportValue('duration') ? $form -> exportValue('duration') : null;
    //$editCourse -> options['course_code'] 	 = $form -> exportValue('course_code') ? $form -> exportValue('course_code') : null;
    //$editCourse -> options['duration'] = $form -> exportValue('duration');
    //$start_date = mktime(0, 0, 0, $_POST['date_Month'], $_POST['date_Day'], $_POST['date_Year']);
    $editCourse -> persist();
    if ($form -> exportValue('branches_ID') && eF_checkParameter($form -> exportValue('branches_ID'), 'id')) {
     $result = eF_getTableData("module_hcd_course_to_branch", "branches_ID", "courses_ID=".$editCourse -> course['id']);
     if (sizeof($result) == 0) {
      eF_insertTableData("module_hcd_course_to_branch", array("branches_ID" => $form -> exportValue('branches_ID'), "courses_ID" => $editCourse -> course['id']));
     } else {
      eF_updateTableData("module_hcd_course_to_branch", array("branches_ID" => $form -> exportValue('branches_ID')), "courses_ID=".$editCourse -> course['id']);
     }
    }
    !isset($redirect) OR eF_redirect($redirect);
   } catch (Exception $e) {
       handleNormalFlowExceptions($e);
   }
  }
 }
 $renderer = prepareFormRenderer($form);
 $form -> accept($renderer);
 $smarty -> assign('T_COURSE_FORM', $renderer -> toArray());
//The courses advanced settings
} elseif (isset($_GET['course'])) {
 $currentCourse = new EfrontCourse($_GET['course']);
 $smarty -> assign("T_CURRENT_COURSE", $currentCourse);
 $baseUrl = 'ctg=courses&course='.$currentCourse -> course['id'];
 $smarty -> assign("T_BASE_URL", $baseUrl);
 require_once 'course_settings.php';
//The main courses list
} else {
 //Directly import course
 $form = new HTML_QuickForm("import_course_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=courses", "", null, true); //Build the form
 $form -> addElement('file', 'import_content', _UPLOADFILE, 'class = "inputText"');
 $form -> addElement('submit', 'submit_course', _SUBMIT, 'class = "flatButton"');
 try {
  if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
   $directionsTree = new EfrontDirectionsTree();
   if (sizeof($directionsTree -> tree) == 0) {
    eF_redirect(basename($_SERVER['PHP_SELF']).'?ctg=directions&add_direction=1&message='.urlencode(_TOCREATECOURSEYOUMUSTFIRSTCREATECATEGORY).'&message_type=failure');
    exit;
   }
   $userTempDir = $GLOBALS['currentUser'] -> user['directory'].'/temp';
   if (!is_dir($userTempDir)) { //If the user's temp directory does not exist, create it
    $userTempDir = EfrontDirectory :: createDirectory($userTempDir, false);
   } else {
    $userTempDir = new EfrontDirectory($userTempDir);
   }
   $newCourse = EfrontCourse :: createCourse();
   $filesystem = new FileSystemTree($userTempDir, true);
   $file = $filesystem -> uploadFile('import_content', $userTempDir);
   $exportedFile = $file;
   $newCourse -> import($exportedFile, false, true);
  }
 } catch (EfrontFileException $e) {
     handleNormalFlowExceptions($e);
 }
 $renderer = prepareFormRenderer($form);
 $form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created
 $smarty -> assign('T_IMPORT_COURSE_FORM', $renderer -> toArray()); //Assign the form to the template
 /** Calculate and display course ajax lists*/
 $sortedColumns = array('name', 'location', 'num_students', 'num_lessons', 'num_skills', 'start_date', 'end_date', 'price', 'created', 'active', 'operations');
 $smarty -> assign("T_DATASOURCE_SORT_BY", array_search('active', $sortedColumns));
 $smarty -> assign("T_DATASOURCE_SORT_ORDER", 'desc');
 $smarty -> assign("T_DATASOURCE_OPERATIONS", array('statistics', 'settings', 'delete'));
 $smarty -> assign("T_DATASOURCE_COLUMNS", $sortedColumns);
 if ($_GET['ajax'] == 'coursesTable' || $_GET['ajax'] == 'instancesTable') {
  try {
   if ($_GET['ajax'] == 'coursesTable') {
    $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => false);
   }
   if ($_GET['ajax'] == 'instancesTable' && eF_checkParameter($_GET['instancesTable_source'], 'id')) {
    $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => $_GET['instancesTable_source']);
   }
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
 }
}
?>
