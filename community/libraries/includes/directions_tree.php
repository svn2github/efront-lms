<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}


try {

    if (!isset($lessons) || (!$lessons && !is_array($lessons))) {
     $lessons = EfrontLesson :: getLessons(true);
     foreach ($lessons as $key => $lesson) {
      if ($lesson -> lesson['archive'] || !$lesson -> lesson['active']) {
       unset($lessons[$key]);
      }
     }
    }
    if (!isset($courses) || (!$courses && !is_array($courses))) {
     //$courses = EfrontCourse :: getCourses(true);
     $constraints = array('active' => true, 'archive' => false, 'instance' => false, 'sort' => 'name');
     $constraints['required_fields'] = array('has_instances');
     $courses = EfrontCourse :: getAllCourses($constraints);

     if ($_SESSION['s_current_branch']) { //filter out courses that don't belong to the current branch url
      $stats_filters = array();
      $branches = array($_SESSION['s_current_branch']);
      $branchesTree = new EfrontBranchesTree();
      $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($branchesTree -> getNodeChildren($_SESSION['s_current_branch'])), RecursiveIteratorIterator :: SELF_FIRST));
      foreach($iterator as $key => $value) {
       $branches[] = $key;
      }
      $result = eF_getTableDataFlat("module_hcd_course_to_branch", "courses_ID", "branches_ID in (".implode(",", $branches).")");
      foreach ($courses as $key => $value) {
       if (!in_array($key, $result['courses_ID'])) {
        unset($courses[$key]);
       }
      }

     }

    }

 //Mark the lessons and courses that the user already has, so that they can't be selected
 try {
  if (isset($_SESSION['s_login']) && $_SESSION['s_login']) {
   $currentUser = EfrontUserFactory::factory($_SESSION['s_login']);
   if ($currentUser -> user['user_type'] == 'administrator') {
    throw new Exception();
   }
   foreach ($currentUser -> getLessons() as $key => $value) {
    if (in_array($key, array_keys($lessons))) {
     $lessons[$key] -> lesson['has_lesson'] = 1;
    }
   }
   foreach ($currentUser -> getUserCourses() as $key => $value) {
    if (in_array($key, array_keys($courses))) {
     $courses[$key] -> course['has_course'] = 1;
    }
   }

   foreach ($lessons as $key => $lesson) {
    if ($lesson -> lesson['max_users'] && sizeof($lesson -> getUsers('student')) >= $lesson -> lesson['max_users']) {
     $lessons[$key] -> lesson['reached_max_users'] = 1;
    }
   }

   foreach ($courses as $key => $course) {
    if ($course -> course['max_users'] && sizeof($course -> getUsers('student')) >= $course -> course['max_users']) {
     $courses[$key] -> course['reached_max_users'] = 1;
    }
   }
  }
 } catch (Exception $e) {/*do nothing, it doesn't matter*/}

 if (isset($_GET['filter'])) {
  foreach ($lessons as $value) {
   $lessonNames[$value -> lesson['id']] = array('name' => $value -> lesson['name']);
  }
  $filtered = array_keys(eF_filterData($lessonNames, $_GET['filter']));
  foreach ($lessons as $key => $value) {
      if (!in_array($key, $filtered)) {
          unset($lessons[$key]);
      }
  }

  foreach ($courses as $value) {
   $courseNames[$value -> course['id']] = array('name' => $value -> course['name']);
  }
  $filtered = array_keys(eF_filterData($courseNames, $_GET['filter']));
  foreach ($courses as $key => $value) {
      if (!in_array($key, $filtered)) {
          unset($courses[$key]);
      }
  }
  if ($_GET['ajax']) {
   $options['collapse'] = false;
   $options['search'] = false;
   $options['tree_tools'] = false;

   $treeString = $directionsTree -> toHTML(false, $lessons, $courses, false, $options);
   $smarty -> assign("T_DISPLAYCODE", $treeString);
   $smarty -> display('display_code.tpl');
   exit;
  }
 }

 $smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, $lessons, $courses, false, $options));
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
