<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

/*

 Categories is the page that concerns direction administration. Here the administrator can view, add, delete and modify directions

 There are 5 sub options in this page, denoted by an extra link part:

 - &add_direction=1                       When we are adding a new direction

 - &delete_direction=<direction_ID>          When we want to delete direction <direction_ID>

 - &edit_direction=<direction_ID>            When we want to edit direction <direction_ID>

 - &deactivate_direction=<direction_ID>      When we deactivate direction <direction_ID>

 - &activate_direction=<direction_ID>        When we activate direction <direction_ID>

 */
$loadScripts[] = 'includes/categories';
if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] == 'hidden') {
 eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}
//Create shorthands for user access rights, to avoid long variable names
!isset($currentUser -> coreAccess['lessons']) || $currentUser -> coreAccess['lessons'] == 'change' ? $_change_ = 1 : $_change_ = 0;
$smarty -> assign("_change_", $_change_);
if (isset($_GET['delete_direction']) && eF_checkParameter($_GET['delete_direction'], 'id')) {
 if (!$_change_) {
  throw new Exception(_UNAUTHORIZEDACCESS);
 }
 try {
  $direction = new EfrontDirection($_GET['delete_direction']);
  if (sizeof($direction -> getLessons(false, true)) > 0 || sizeof($direction -> getCourses(false, true)) > 0) {
   throw new EfrontDirectionException(_YOUMUSTDELETEALLLESSONSANDSUBDIRECTIONSINTHISDIRECTIONBEFOREDELETINGIT.': '.$direction, EfrontDirectionException :: NOT_EMPTY_CATEGORY);
  } else {
   $direction -> delete();
  }
 } catch (Exception $e) {
  $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
  header("HTTP/1.0 500 ");
  echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
 }
 exit;
} elseif (isset($_GET['deactivate_direction']) && eF_checkParameter($_GET['deactivate_direction'], 'id')) {
 if (!$_change_) {
  throw new Exception(_UNAUTHORIZEDACCESS);
 }
 try {
  $direction = new EfrontDirection($_GET['deactivate_direction']);
  if (sizeof($direction -> getLessons(false, true)) > 0 || sizeof($direction -> getCourses(false, true)) > 0) {
   throw new EfrontDirectionException(_YOUMUSTDELETEALLLESSONSANDSUBDIRECTIONSINTHISDIRECTIONBEFOREDELETINGIT.': '.$direction, EfrontDirectionException :: NOT_EMPTY_CATEGORY);
  } else {
   $direction['active'] = 0;
   $direction -> persist();
   echo "0";
  }
 } catch (Exception $e) {
  $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
  header("HTTP/1.0 500 ");
  echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
 }
 exit;
} elseif (isset($_GET['activate_direction']) && eF_checkParameter($_GET['activate_direction'], 'id')) {
 if (!$_change_) {
  throw new Exception(_UNAUTHORIZEDACCESS);
 }
 try {
  $direction = new EfrontDirection($_GET['activate_direction']);
  $direction['active'] = 1;
  $direction -> persist();
  echo "1";
 } catch (Exception $e) {
  $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
  header("HTTP/1.0 500 ");
  echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
 }
 exit;
} elseif (isset($_GET['add_direction']) || (isset($_GET['edit_direction']) && eF_checkParameter($_GET['edit_direction'], 'id'))) {
 $directionsTree = new EfrontDirectionsTree();
 $directionsPaths = $directionsTree -> toPathString(true, true);
 if (isset($_GET['add_direction'])) {
  $post_target = 'add_direction=1';
  $defaults_array = array('active' => 1);
 } else {
  $post_target = 'edit_direction='.$_GET['edit_direction'];
  $editDirection = new EfrontDirection($_GET['edit_direction']);
  $defaults_array = array('name' => $editDirection['name'],
                                    'active' => $editDirection['active'],
                                    'parent_direction_ID' => $editDirection['parent_direction_ID']);
  //Remove direction's children from the list of selectable parents
  $directionChildren = array();
  foreach (new EfrontAttributeFilterIterator(new RecursiveIteratorIterator($directionsTree -> getNodeChildren($_GET['edit_direction'])), array('id')) as $key => $value) {
   if (isset($directionsPaths[$value])) {
    unset($directionsPaths[$value]);
   }
  }
 }
 $form = new HTML_QuickForm("add_directions_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=directions&".$post_target, "", null, true);
 $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
 $form -> addElement('text', 'name', _DIRECTIONNAME, 'class = "inputText"');
 $form -> addRule('name', _THEFIELD.' '._DIRECTIONNAME.' '._ISMANDATORY, 'required', null, 'client');
 $form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');
 $selectOptions = $directionsPaths;
 $selectOptions[0] = _ROOTDIRECTION;
 ksort($selectOptions);
 $form -> addElement('select', 'parent_direction_ID', _PARENTDIRECTION, $selectOptions);
 //$form -> addElement("advcheckbox", "active", _ACTIVEFEM, null, 'class = "inputCheckBox"', array(0, 1));

 $form -> setDefaults($defaults_array);

 if (!$_change_) {
  $form -> freeze();
 } else {
  $form -> addElement('submit', 'submit_direction', _SUBMIT, 'class = "flatButton"');

  if ($form -> isSubmitted() && $form -> validate()) {
   $direction_content = array("name" => $form -> exportValue('name'),
                                           "parent_direction_ID" => $form -> exportValue('parent_direction_ID'),
                                           "active" => 1);
   if (isset($_GET['edit_direction'])) {
    $editDirection['name'] = $direction_content['name'];
    $editDirection['parent_direction_ID'] = $direction_content['parent_direction_ID'];
    $editDirection['active'] = $direction_content['active'];
    try {
     $editDirection -> persist();
     eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=directions&message=".urlencode(_SUCCESFULLYUPDATEDDIRECTION)."&message_type=success");
    } catch (Exception $e) {
     $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
     $message_type = 'failure';
    }
   } else {
    try {
     EfrontDirection :: createDirection($direction_content);
     eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=directions&message=".urlencode(_SUCCESFULLYADDEDDIRECTION)."&message_type=success");
    } catch (Exception $e) {
     $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().')';
     $message_type = 'failure';
    }
   }
  }
 }

 $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

 $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
 $form -> setRequiredNote(_REQUIREDNOTE);
 $form -> accept($renderer);

 $smarty -> assign('T_DIRECTIONS_FORM', $renderer -> toArray());

 if (isset($_GET['edit_direction'])) {

  $smarty -> assign("T_DIRECTIONS_PATHS", $directionsTree -> toPathString(true, true));
  $lessons = $editDirection -> getLessons(true);//EFrontLesson :: getLessons();
  $courses = $editDirection -> getCourses(true);//EFrontCourse :: getCourses();
  $languages = EfrontSystem :: getLanguages(true);

  foreach ($lessons as $key => $lesson) {
   $lessons[$key] = $lesson -> lesson;
  }
  foreach ($courses as $key => $course) {
   $courses[$key] = $course -> course;
  }

  if (isset($_GET['ajax']) && $_GET['ajax'] == 'lessonsTable') {
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
   foreach ($lessons as $key => $lesson) {
    $lessons[$key]['languages_NAME'] = $languages[$lesson['languages_NAME']];
   }

   $smarty -> assign("T_LESSONS_DATA", $lessons);

   $smarty -> display('administrator.tpl');
   exit;
  }
  if (isset($_GET['ajax']) && $_GET['ajax'] == 'coursesTable') {
   isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
   if (isset($_GET['sort'])) {
    isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
    $courses = eF_multiSort($courses, $_GET['sort'], $order);
   }
   if (isset($_GET['filter'])) {
    $courses = eF_filterData($courses, $_GET['filter']);
   }
   $smarty -> assign("T_COURSES_SIZE", sizeof($courses));
   if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
    $courses = array_slice($courses, $offset, $limit);
   }
   foreach ($courses as $key => $course) {
    $courses[$key]['languages_NAME'] = $languages[$course['languages_NAME']];
   }

   $smarty -> assign("T_COURSES_DATA", $courses);

   $smarty -> display('administrator.tpl');
   exit;
  }
  if (isset($_GET['lessonsPostAjaxRequest'])) {
   try {
    if (isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id') && isset($_GET['directions_ID']) && eF_checkParameter($_GET['directions_ID'], 'id')) {
     $lesson = new EfrontLesson($_GET['id']);
     if ($_GET['directions_ID'] != $lesson -> lesson['directions_ID']) {
      $updateLessonInstancesCategory = true; //This means we need to update instances to match the course's new category
     }
     $lesson -> lesson['directions_ID'] = $_GET['directions_ID'];
     $lesson -> persist();
     if (isset($updateLessonInstancesCategory) && $updateLessonInstancesCategory) {
      eF_updateTableData("lessons", array("directions_ID" => $lesson -> lesson['directions_ID']), "instance_source=".$lesson -> lesson['id']);
     }
    }
    exit;
   } catch (Exception $e) {
    echo $e -> getMessage().' ('.$e -> getCode().')';
   }
  }
  if (isset($_GET['coursesPostAjaxRequest'])) {
   try {
    if (isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id') && isset($_GET['directions_ID']) && eF_checkParameter($_GET['directions_ID'], 'id')) {
     $course = new EfrontCourse($_GET['id']);
     if ($_GET['directions_ID'] != $course -> course['directions_ID']) {
      $updateCourseInstancesCategory = true; //This means we need to update instances to match the course's new category
     }
     $course -> course['directions_ID'] = $_GET['directions_ID'];
     $course -> persist();
     if (isset($updateCourseInstancesCategory) && $updateCourseInstancesCategory) {
      eF_updateTableData("courses", array("directions_ID" => $course -> course['directions_ID']), "instance_source=".$course -> course['id']);
     }

    }
    exit;
   } catch (Exception $e) {
    echo $e -> getMessage().' ('.$e -> getCode().')';
   }
  }
 }
} else {
 $directionsTree = new EfrontDirectionsTree();

 $directionsPaths = $directionsTree -> toPathString(false);
 $flatTree = $directionsTree -> getFlatTree();

 foreach ($flatTree as $key => $value) {
  $flatTree[$key]['pathString'] = $directionsPaths[$value['id']];
  $direction = new EfrontDirection($flatTree[$key]);
  $flatTree[$key]['lessons'] = sizeof($direction -> getLessons());
  $flatTree[$key]['courses'] = sizeof($direction -> getCourses());
 }
 unset($value);

 $smarty -> assign("T_DIRECTIONS_DATA", $flatTree);
}
