<?php
/**
* Calendar management
*
* This page is used to view the calendar and edit events to it
* In classical eFront, only the professors might edit events
* In HCD, anyone can edit events
* @package eFront
* @version 1.0
*/

$load_editor = true;

$today = getdate(time());                                                                           //Get current time in an array
$today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);                            //Create a timestamp that is today, 00:00. this will be used in calendar for displaying today
(eF_checkParameter($_GET['view_calendar'], 'timestamp')) ? $view_calendar = $_GET['view_calendar']: $view_calendar = $today;    //If a specific calendar date is not defined in the GET, set as the current day to be today
isset($_GET['show_interval']) ? $show_interval = $_GET['show_interval'] : $show_interval = 'day';


// The type variable denotes the profile of the calendar: Organization profile, Current lesson profile, All lessons profile (0,1 and 2 respectively)
if ($show_interval == 'all') {
    $type = "3";

    $calendar_type_link = "&type=".$_GET['type'];
    $result = eF_getTableData("calendar c LEFT OUTER JOIN lessons l ON c.lessons_ID = l.ID", "c.id, c.timestamp, c.data, l.name","","","timestamp");
    foreach ($result as $event) {
        $events[$event['timestamp']]['data'][] = $event['data'];
        $events[$event['timestamp']]['id'][]   = $event['id'];
        $events[$event['timestamp']]['lesson'][]  = $event['name'];
    }
} else {
    if (MODULE_HCD_INTERFACE) {
        if (isset($_GET['type'])) {
            $type = $_GET['type'];
        } else if ($_SESSION['s_lessons_ID']) {
            $type = "2";
        } else {
            $type = 0;
        }
        $events     = eF_getCalendar(0, $type);                                                         //Get all events for this type
    } else {
        $events     = eF_getCalendar();                                                                 //Get all events
    }
}
$smarty -> assign("T_CALENDAR_EVENTS", $events);

$timestamp_info = getdate($view_calendar);                                                          //Extract date information from timestamp
$timestamp_info['wday'] == 0 ? $timestamp_info['wday'] = 7 : '';                                    //getdate() returns week days from 0-6, with Sunday beeing 0. So, we convert Sunday to 7
$month_start = mktime(0,  0,  0,    $timestamp_info['mon'],     1,                                                     $timestamp_info['year']);
$month_end   = mktime(23, 59, 59,   $timestamp_info['mon'] + 1, 0,                                                     $timestamp_info['year']);
$week_start  = mktime(0,  0,  0,    $timestamp_info['mon'],     $timestamp_info['mday'] - $timestamp_info['wday'] + 1, $timestamp_info['year']);
$week_end    = mktime(23, 59, 59,   $timestamp_info['mon'],     $timestamp_info['mday'] - $timestamp_info['wday'] + 7, $timestamp_info['year']);
$day_start   = mktime(0,  0,  0,    $timestamp_info['mon'],     $timestamp_info['mday'],                               $timestamp_info['year']);
$day_end     = mktime(23, 59, 59,   $timestamp_info['mon'],     $timestamp_info['mday'],                               $timestamp_info['year']);

foreach ($events as $timestamp => $event) {                                                         //Assign events on each interval
    if ($timestamp >= $month_start && $timestamp <= $month_end) {
        $month_events[$timestamp] = $event;
    }
    if ($timestamp >= $week_start && $timestamp <= $week_end) {
        $week_events[$timestamp] = $event;
    }
    if ($timestamp >= $day_start && $timestamp <= $day_end) {
        $day_events[$timestamp] = $event;
    }
}

switch ($show_interval) {
    case 'all':
        $interval_events = $events;
        $smarty -> assign('T_CALENDAR_TITLE' , _CALENDAR);
        break;
    case 'month':
        $interval_events = $month_events;
        $smarty -> assign('T_CALENDAR_TITLE' , _CALENDAR . " " . _FFROM . " " . date("d/m/y",$month_start) . " " . _TO . " " . date("d/m/y",$month_end));
        break;
    case 'week':
        $interval_events = $week_events;
        $smarty -> assign('T_CALENDAR_TITLE' , _CALENDAR . " " . _FFROM . " " . date("d/m/y",$week_start) . " " . _TO . " " . date("d/m/y",$week_end));
        break;
    case 'day':
    default:
        $interval_events = $day_events;
        $smarty -> assign('T_CALENDAR_TITLE' , _CALENDAR . " " . date("d/m/y",$day_start));
        break;
}

$smarty -> assign("T_INTERVAL_CALENDAR_EVENTS", $interval_events);
$smarty -> assign("T_VIEW_CALENDAR", $view_calendar);

if (MODULE_HCD_INTERFACE && $type != "3") {
    // No form defined (or needed) in the presentation mode in order to user $form->addElement...
    $calendar_type  = "<select name = 'calendar_type' id = 'calendar_type' style = 'vertical-align:middle' onChange='javascript:location.href = \"". $_SESSION['s_type'] .".php?ctg=calendar&view_calendar=".$view_calendar."&show_interval=".$show_interval."&type=\" + document.getElementById(\"calendar_type\").value' >";
    $calendar_type .= "<option value='0'"; if ($type == "0") $calendar_type .= "selected='selected'"; $calendar_type .= ">" . _ORGANIZATIONPROFILE . "</option>";

    $calendar_type .= "<option value='1'"; if ($type == "1") $calendar_type .= "selected='selected'"; $calendar_type .= ">" . _EDUCATIONAL. "</option>";
    if ($_SESSION['s_lessons_ID']) {
        $calendar_type .= "<option value='2'"; if ($type == "2") $calendar_type .= "selected='selected'"; $calendar_type .= ">" . $currentLesson -> lesson['name']. "</option>";
    }
    $calendar_type .= "</select>";

    // Used to modify the links for interval selection below as well
    $calendar_type_link = "&type=".$type;

}
$smarty -> assign("T_TYPE", $type);
$smarty -> assign("T_CALENDAR_TYPE_SELECT", $calendar_type);

if (isset($_GET['delete_calendar']) && eF_checkParameter($_GET['delete_calendar'], 'id')) {
    if (isset($currentUser -> coreAccess['calendar']) && $currentUser -> coreAccess['calendar'] != 'change') {
        exit;
    }
    if (eF_deleteTableData("calendar", "id=".$_GET['delete_calendar'])) {
        $message      = _SUCCESFULLYDELETEDEVENT;
        $message_type = 'success';
        if (MODULE_HCD_INTERFACE) {
            $type_in_header = "&type=".$type;
        }
        header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=calendar&view_calendar='.$view_calendar.'&show_interval='.$show_interval . $type_in_header . '&message='.$message.'&message_type='.$message_type);
    } else {
        $message      = _SOMEPROBLEMEMERGED;
        $message_type = 'failure';
    }
} elseif (isset($_GET['add_calendar']) || (isset($_GET['edit_calendar']) && eF_checkParameter($_GET['edit_calendar'], 'id'))) {
    if (isset($currentUser -> coreAccess['calendar']) && $currentUser -> coreAccess['calendar'] != 'change') {
        exit;
    }
    $smarty -> assign('T_CALENDAR_TITLE' , _CALENDAR);
    $load_editor = true;
    $smarty -> assign("T_POPUP_MODE", true);

    isset($_GET['add_calendar']) ? $post_target = 'add_calendar=1' : $post_target = 'edit_calendar='.$_GET['edit_calendar'];
    if (MODULE_HCD_INTERFACE) {
        $type_in_header = "&type=".$type;
    //    echo $type_in_header."<br>";
    }
    $form = new HTML_QuickForm("add_calendar_event_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=calendar&view_calendar='.$view_calendar.'&show_interval='.$show_interval . $type_in_header.'&'.$post_target, "", "target='_parent' id='main_form'", true);
//    $form = new HTML_QuickForm("add_calendar_event_form", "post", $_SERVER['HTTP_REFERER'].'&'.$post_target, "", "target='_parent' id='main_form'", true);

    // Hidden for maintaining the previous_url value
    $form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');
//pr($_SERVER);
    $previous_url = $_SERVER['HTTP_REFERER'];

    if ($position = strpos($previous_url, "&pmessage")) {
            $previous_url = substr($previous_url, 0, $position);
   } else if ($position = strpos($previous_url, "&message")) {
            $previous_url = substr($previous_url, 0, $position);
    }

    $form -> setDefaults(array( 'previous_url'     =>  $previous_url));

    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter
    $form -> addElement('textarea', 'event', _EVENT, 'id="event" class = "simpleEditor" style = "width:100%;height:10em;"');
    //$form -> addRule('event', _THEFIELD.' '._ISMANDATORY, 'required');
    //$form -> addRule('event', _THEFIELD.' '._EVALUATIONCOMMENT .' '._ISMANDATORY, 'required');

    if ($currentUser -> getType() == 'professor') {
        $tmp = eF_getTableData("users_to_lessons u, lessons l", "u.lessons_ID, l.name", "u.lessons_ID = l.ID AND u.users_LOGIN='".$_SESSION['s_login']."'");
    } else {
        $tmp = eF_getTableData("users_to_lessons u, lessons l", "u.lessons_ID, l.name", "u.lessons_ID = l.ID");
    }
    // In HCD module calendar, we also have calendar events that are not associated with any lesson, the Company events. We set their ID = 0 (which will
    // never conflict with a lesson ID, since lesson_ID NOT NULL by the database rule).
    if (MODULE_HCD_INTERFACE) {
        $lessons = array("0" => _ORGANIZATION);
    } else {
        $lessons = array();
    }
    if ($currentUser -> getType() != 'student') {
        for ($i = 0; $i < sizeof($tmp); $i++){
            $lessons[$tmp[$i]['lessons_ID']] = $tmp[$i]['name'];
        }

    }
    if (MODULE_HCD_INTERFACE) {
        $form -> addElement('select', 'lesson', _RELATEDTO, $lessons);
        if ($type == 0) {
            $form -> setDefaults(array('lesson' => '0'));
        } else if ($type == 1) {
            $form -> setDefaults(array('lesson' => $tmp[0]['lessons_ID']));
        } else {
            $form -> setDefaults(array('lesson' => $_SESSION['s_lessons_ID']));
        }

    } else {
        $form -> addElement('select', 'lesson', _LESSON, $lessons);
        $form -> setDefaults(array('lesson' => $_SESSION['s_lessons_ID']));
    }


    //$dates = range($month_start, $month_end, 86400);//#filter:timestamp-
    //$dates = array_combine($dates, $dates);
    //array_walk($dates, create_function('&$v,$k', '$v = "#filter:timestamp-".$v."#";'));
    //$form -> addElement('select', 'timestamp', null, $dates, 'class = "inputSelect"');
    $options = array(
        'format'         => 'd m Y, h:i',
        'minYear'        => date("Y"),
        'maxYear'        => date('Y') + 1,
    );
    $form -> addElement('date', 'event_date', _DATE, $options);

    $form -> addElement('submit', 'submit_event', _SUBMIT, 'class = "flatButton" onClick="if (!document.getElementById(\'mce_editor_0\').contentWindow.frames.document.body.innerHTML) { alert(\''._EVENTFIELDISMANDATORY.'\');return false;}"'); //if (document.getElementById(\'event\').value == \'\') { alert(\'hi\');return false; } else { alert(\'ok\'); return true;}
    $form -> addElement('submit', 'submit_event_add_another', _SUBMITANDADDANOTHER, 'class = "flatButton" onclick="if (!document.getElementById(\'mce_editor_0\').contentWindow.frames.document.body.innerHTML ) { alert(\''._EVENTFIELDISMANDATORY.'\');return false;}"'); //else {document.forms[0].target = \'_self\';}"');

    if (isset($_GET['edit_calendar'])) {

        $event = eF_getTableData("calendar", "id, data, lessons_ID,timestamp", "id=".$_GET['edit_calendar']);
        //pr($event);
        $form -> setDefaults(array("event_date" => $event[0]['timestamp']));
        $form -> setDefaults(array("event" => $event[0]['data']));
        $form -> setDefaults(array("lesson" => $event[0]['lessons_ID']));
    } else {
//echo $view_calendar ." san new <BR>";
        $form -> setDefaults(array('event_date' => $view_calendar));
    }
//$db->debug = true;
    if ($form -> isSubmitted()) {
        if ($form -> validate()) {
            $values = $form -> exportValues();

            $timestamp = mktime($values['event_date']['h'], $values['event_date']['i'], $values['event_date']['s'], $values['event_date']['m'], $values['event_date']['d'], $values['event_date']['Y']);
            if (isset($_GET['add_calendar'])) {
                $fields = array('lessons_ID'  => $values['lesson'],
                                'data'        => $values['event'],
                                'timestamp'   => $timestamp,
                                'active'      => 1,
                                'users_LOGIN' => $_SESSION['s_login']);

                if (MODULE_HCD_INTERFACE) {
                    $type_in_header = "&type=".$type;
                }

                if (eF_insertTableData("calendar", $fields)) {
                    $message      = _SUCCESFULLYADDEDEVENT;
                    $message_type = 'success';

                    if (isset($popup)) {            //In this case, the window is in 'popup mode'. So, when finished, reload parent window.
                        echo "<script>!/\?/.test(parent.location) ? parent.location = parent.location+'?message=".$message."&message_type=".$message_type."' : parent.location = parent.location+'&message=".$message."&message_type=".$message_type."';</script>";
                    } else {
                        $next_url = $form->exportValue('previous_url');
                        if (!strpos($next_url, "?")) {
                                $next_url = $next_url . "?dummy=";
                        }

                        if (isset($_POST['submit_event_add_another'])) {
                        // VERY IMPORTANT TO PUT add_another LAST
                  // echo $form->exportValue('previous_url')     ."**<BR>";

header('location:'.$next_url . '&pmessage='.$message.'&pmessage_type='.$message_type.'&add_another=1');
//                            header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=calendar&view_calendar='.$view_calendar.'&add_calendar=1&message='.$message.'&message_type='.$message_type.'&add_another=1');
                        } else {
//                            header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=calendar&view_calendar='.$view_calendar.'&show_interval='.$show_interval . $type_in_header . '&message='.$message.'&message_type='.$message_type);
header('location:'. $next_url . '&message='.$message.'&message_type='.$message_type);

                        }
                    }
                } else {
                    $message      = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';
                }
            } else {
                if (eF_updateTableData("calendar", array('data' => $values['event'], 'timestamp' => $timestamp, 'lessons_ID' => $values['lesson'], 'users_LOGIN' => $_SESSION['s_login']), "id=".$event[0]['id'])) {
                    $message      = _SUCCESFULLYUPDATEDEDEVENT;
                    $message_type = 'success';
                    header('location:'.basename($_SERVER['PHP_SELF']).'?ctg=calendar&message='.$message.'&show_interval='.$show_interval . $type_in_header .'&message_type='.$message_type);
                } else {
                    $message      = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';
                }
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);


    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_ADD_EVENT_FORM', $renderer -> toArray());
} else {
    $options = array(array('image' => '16x16/table_selection_cell.png',        'title' => _SHOWDAYEVENTS,  'link' => ($show_interval == 'day')?'javascript:void(0)':basename($_SERVER['PHP_SELF'])."?ctg=calendar&view_calendar=$view_calendar&show_interval=day$calendar_type_link", 'selected' => ($show_interval == 'day')? true : false),
                     array('image' => '16x16/table_selection_row.png',        'title' => _SHOWWEEKEVENTS,  'link' => ($show_interval == 'week')?'javascript:void(0)':basename($_SERVER['PHP_SELF'])."?ctg=calendar&view_calendar=$view_calendar&show_interval=week$calendar_type_link", 'selected' => ($show_interval == 'week')? true : false),
                     array('image' => '16x16/table_selection_all.png',        'title' => _SHOWMONTHEVENTS,  'link' => ($show_interval == 'month')?'javascript:void(0)':basename($_SERVER['PHP_SELF'])."?ctg=calendar&view_calendar=$view_calendar&show_interval=month$calendar_type_link", 'selected' => ($show_interval == 'month')? true : false),
                     array('image' => '16x16/table_sql_create.png',        'title' => _SHOWALLEVENTS,  'link' => ($show_interval == 'all')?'javascript:void(0)':basename($_SERVER['PHP_SELF'])."?ctg=calendar&view_calendar=$view_calendar&show_interval=all$calendar_type_link",  'selected' => ($show_interval == 'all')? true : false));
    $smarty -> assign("T_CALENDAR_OPTIONS", $options);
}

if (isset($_GET['add_another'])) {
    $smarty -> assign('T_ADD_ANOTHER', "1");
}
?>