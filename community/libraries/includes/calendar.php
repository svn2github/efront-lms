<?php
/**
* Calendar management
*
* This page is used to view the calendar and edit events to it
* In classical eFront, only the professors might edit events
* In HCD, anyone can edit events
* @package eFront
* @version 3.6.0
*/

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if ($GLOBALS['configuration']['disable_calendar'] == 1 || (isset($currentUser -> coreAccess['calendar']) && $currentUser -> coreAccess['calendar'] == 'hidden')) {
 eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}


$loadScripts[] = 'scriptaculous/controls';
$loadScripts[] = 'includes/calendar';

//Create shorthands for user access rights, to avoid long variable names
!isset($currentUser -> coreAccess['calendar']) || $currentUser -> coreAccess['calendar'] == 'change' ? $_change_ = 1 : $_change_ = 0;
$smarty -> assign("_change_", $_change_);

if (eF_checkParameter($_GET['view_calendar'], 'timestamp')) { //If a specific calendar date is not defined in the GET, set as the current day to be today
 $viewCalendar = $_GET['view_calendar'];
} else {
 $today = getdate(time()); //Get current time in an array
 $viewCalendar = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']); //Create a timestamp that is today, 00:00. this will be used in calendar for displaying today
}
isset($_GET['show_interval']) && eF_checkParameter($_GET['show_interval'], 'string') ? $showInterval = $_GET['show_interval'] : $showInterval = 'day';

$smarty->assign("T_VIEW_CALENDAR", $viewCalendar);
$smarty->assign("T_SHOW_INTERVAL", $showInterval);

$events = calendar :: getCalendarEventsForUser($currentUser);
$smarty -> assign("T_CALENDAR_TYPES", calendar :: $calendarTypes);

if ($_SESSION['s_type'] != 'administrator' && $_SESSION['s_current_branch']) { //this applies to branch urls
 $currentBranch = new EfrontBranch($_SESSION['s_current_branch']);
 $branchTreeUsers = array_keys($currentBranch->getBranchTreeUsers());
 foreach ($events as $key => $value) {
  if ($value['type'] != 'global' && !in_array($value['users_LOGIN'], $branchTreeUsers)) {
   unset($events[$key]);
  }
 }
}
if (isset($_GET['ajax'])) {
 try {
  if ($_GET['ajax'] == "calendarTable") {
   $dataSource = calendar :: filterCalendarEvents($events, $showInterval, $viewCalendar);
   $tableName = $_GET['ajax'];
   include("sorted_table.php");
   exit;
  } else if ($_GET['set_default_course']) {
   if ($_SESSION['s_courses_ID']) {
    $course = new EfrontCourse($_SESSION['s_courses_ID']);
    echo json_encode(array('status' => true, 'foreign_ID' => $course -> course['id'], 'name' => $course -> course['name']));
   }
   exit;
  } else if ($_GET['set_default_lesson']) {
   if ($_SESSION['s_lessons_ID']) {
    $lesson = new EfrontLesson($_SESSION['s_lessons_ID']);
    echo json_encode(array('status' => true, 'foreign_ID' => $lesson -> lesson['id'], 'name' => $lesson -> lesson['name']));
   }
   exit;
  } else if ($_GET['set_default_group']) {
   $groups = $currentUser -> getGroups();
   if (sizeof($groups) > 0) {
    $group = new EfrontGroup(current($groups));
    echo json_encode(array('status' => true, 'foreign_ID' => $group -> group['id'], 'name' => $group -> group['name']));
   }
   exit;
  } else if ($_GET['set_default_branch']) {
   $branches = explode(",", $_SESSION['supervises_branches']);
   if ($_SESSION['supervises_branches'] && sizeof($branches) > 0) {
    $branch = new EfrontBranch($branches[0]);
    echo json_encode(array('status' => true, 'foreign_ID' => $branch -> branch['branch_ID'], 'name' => $branch -> branch['name']));
   }
   exit;
  }
 } catch (Exception $e) {
  handleAjaxExceptions($e);
 }
}
$entityName = 'calendar';
if ($currentUser -> user['user_type'] == 'administrator') { //admins can edit all events
 $legalValues = array_keys($events);
} else {
 $legalValues = array_keys(calendar :: getUserCalendarEvents($currentUser));
}
include("entity.php");
$events = calendar :: sortCalendarEventsByTimestamp($events);
$smarty -> assign("T_SORTED_CALENDAR_EVENTS", $events);
$smarty -> assign("T_VIEW_CALENDAR", $viewCalendar);
$options = array(array('image' => '16x16/calendar_selection_day.png', 'title' => _SHOWDAYEVENTS, 'link' => basename($_SERVER['PHP_SELF'])."?ctg=calendar&view_calendar=$viewCalendar&show_interval=day", 'selected' => ($showInterval == 'day' ? true : false)),
     array('image' => '16x16/calendar_selection_week.png', 'title' => _SHOWWEEKEVENTS, 'link' => basename($_SERVER['PHP_SELF'])."?ctg=calendar&view_calendar=$viewCalendar&show_interval=week", 'selected' => ($showInterval == 'week' ? true : false)),
     array('image' => '16x16/calendar_selection_month.png', 'title' => _SHOWMONTHEVENTS, 'link' => basename($_SERVER['PHP_SELF'])."?ctg=calendar&view_calendar=$viewCalendar&show_interval=month", 'selected' => ($showInterval == 'month' ? true : false)),
     array('image' => '16x16/calendar_selection_all.png', 'title' => _SHOWALLEVENTS, 'link' => basename($_SERVER['PHP_SELF'])."?ctg=calendar&view_calendar=$viewCalendar&show_interval=all", 'selected' => ($showInterval == 'all' ? true : false)));
$smarty -> assign("T_CALENDAR_OPTIONS", $options);
