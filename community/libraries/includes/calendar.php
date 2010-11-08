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

$load_editor = true;
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
isset($_GET['show_interval']) ? $showInterval = $_GET['show_interval'] : $showInterval = 'day';

$events = calendar :: getCalendarEventsForUser($currentUser);
$smarty -> assign("T_CALENDAR_TYPES", calendar :: $calendarTypes);

if (isset($_GET['ajax']) && $_GET['ajax'] == "calendarTable") {
 $dataSource = calendar :: filterCalendarEvents($events, $showInterval, $viewCalendar);
 $tableName = $_GET['ajax'];
 include("sorted_table.php");
}


$entityName = 'calendar';
if ($user -> user['user_type'] == 'administrator') { //admins can edit all events
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
