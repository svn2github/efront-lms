<?php
/**

* Search courses

*

* Find users that fulfill a number of

* courses related criteria like course

* attendance, dates of attendance etc

*

* @package eFront

* @version 1.0

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
// Create ajax enabled table for employees
$loadScripts[] = 'includes/search_courses';
$loadScripts[] = 'scriptaculous/prototype';
if (isset($_GET['ajax'])) {
    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
        $sort = $_GET['sort'];
        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
    } else {
        $sort = 'login';
    }
    $search_string = "";
    $dif_tables = "users ";
    $found =0;
//pr($_GET);
    foreach ($_GET as $criterium => $value) {
        // If a course has been defined
        if (strncmp($criterium, "courses",7)==0 && $value != '') {
            $found = 1;
            $crit_broken = explode("_",$criterium);
            $id = $crit_broken[1];
            // If the condition == 3 then the user must not have this course assigned
            if ($_GET['condition_'.$id] == '3' ) {
                if ($search_string != "") {
                    $search_string .= " AND ";
                }
                $search_string .= "NOT EXISTS (SELECT users_login FROM users_to_courses AS users_to_courses".$id." WHERE users_to_courses".$id.".courses_ID = '".$value."' AND users_to_courses".$id.".users_login = login) ";
            } else {
                $dif_tables .= " LEFT OUTER JOIN users_to_courses as users_to_courses".$id." ON users.login = users_to_courses".$id.".users_login";
                if ($search_string != "") {
                    $search_string .= " AND ";
                }
                $search_string .= "users_to_courses".$id.".courses_ID = '".$value."' ";

                if (!isset($_GET['condition_'.$id]) || $_GET['condition_'.$id] == '' || $_GET['condition_'.$id] == '1') {
                    $search_string .= " AND users_to_courses".$id.".completed = '1' ";
                    if (isset($_GET['from_date_day_'.$id]) && $_GET['from_date_day_'.$id] != '0' && $_GET['from_date_day_'.$id] != '') {
                        $from_timestamp = mktime(0, 0, 0, $_GET['from_date_month_'.$id], $_GET['from_date_day_'.$id], $_GET['from_date_year_'.$id]);

                        // On the defined date
                        if ($_GET['from_date_cond_'.$id] == "2") {
                            $search_string .= " AND users_to_courses".$id.".from_timestamp >= " . $from_timestamp . " AND users_to_courses".$id.".from_timestamp < " . ($from_timestamp + 86400); //"
                        // Until the defined starting date
                        } else if ($_GET['from_date_cond_'.$id] == "3") {
                            $search_string .= " AND users_to_courses".$id.".from_timestamp <= " . $from_timestamp . " ";
                        // From the defined starting date
                        } else {
                            $search_string .= " AND users_to_courses".$id.".from_timestamp >= " . $from_timestamp . " ";
                        }

                    }
                    if (isset($_GET['to_date_day_'.$id]) && $_GET['to_date_day_'.$id] != '0' && $_GET['to_date_day_'.$id] != '') {
                        $to_timestamp = mktime(0, 0, 0, $_GET['to_date_month_'.$id], $_GET['to_date_day_'.$id], $_GET['to_date_year_'.$id]);
                        // On the defined date
                        if ($_GET['to_date_cond_'.$id] == "2") {
                            $search_string .= " AND users_to_courses".$id.".to_timestamp >= " . $to_timestamp . " AND users_to_courses".$id.".to_timestamp < " . ($to_timestamp + 86400);
                        // Until the defined starting date
                        } else if ($_GET['to_date_cond_'.$id] == "1") {
                            $search_string .= " AND users_to_courses".$id.".to_timestamp >= " . $to_timestamp . " ";
                        // to the defined starting date
                        } else {
                            $search_string .= " AND users_to_courses".$id.".to_timestamp <= " . $to_timestamp . " ";
                        }
                    }
                } else if ( $_GET['condition_'.$id] == '2' ) {
                    $search_string .= " AND users_to_courses".$id.".completed = '0' ";
                    if (isset($_GET['from_date_day_'.$id]) && $_GET['from_date_day_'.$id] != '0' && $_GET['from_date_day_'.$id] != '') {
                        $from_timestamp = mktime(0, 0, 0, $_GET['from_date_month_'.$id], $_GET['from_date_day_'.$id], $_GET['from_date_year_'.$id]);

                        if ($_GET['from_date_cond_'.$id] == "2") {
                            $search_string .= " AND users_to_courses".$id.".from_timestamp >= " . $from_timestamp . " AND users_to_courses".$id.".from_timestamp < " . ($from_timestamp + 86400);
                        // Until the defined starting date
                        } else if ($_GET['from_date_cond_'.$id] == "3") {
                            $search_string .= " AND users_to_courses".$id.".from_timestamp <= " . $from_timestamp . " ";
                        // From the defined starting date
                        } else {
                            $search_string .= " AND users_to_courses".$id.".from_timestamp >= " . $from_timestamp . " ";
                        }


                    }
                }
            }

        }
    }



    if ($found) {

        $employees = eF_getTableData($dif_tables, "users.*",$search_string,"","login limit ".G_DEFAULT_TABLE_SIZE);
//pr($employees);

        // @todo: problem with professors in one and students in another course
        foreach ($employees as $userId => $employee) {
            if ($employee['user_type'] != 'student') {
                unset($employees[$userId]);
            }

        }
        $employees = eF_multiSort($employees, $_GET['sort'], $order);
        if (isset($_GET['filter'])) {
            $employees = eF_filterData($employees , $_GET['filter']);
        }

        $smarty -> assign("T_EMPLOYEES_SIZE", sizeof($employees));

        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
            $employees = array_slice($employees, $offset, $limit);
        }

    } else {
        $employees = array();
    }

    $recipients = basename($_SERVER['PHP_SELF'])."?ctg=messages&add=1&recipient=";
    $first = 1;
    foreach ($employees as $employee) {
        if ($first) {
            $recipients .= $employee['login'];
            $first = 0;
        } else {
            $recipients .= ";".$employee['login'];
        }

    }

    $smarty -> assign("T_SENDALLMAIL_URL", $recipients);
    $smarty -> assign("T_EMPLOYEES", $employees);
    $smarty -> display($_SESSION['s_type'].'.tpl');
    exit;
} else {
    $sendmail_link = array(
         array('id' => 'sendToAllId', 'text' => _SENDMESSAGETOALLFOUNDEMPLOYEES, 'image' => "16x16/mail.png", 'href' => "javascript:void(0);", "onClick" => "this.href=document.getElementById('sendAllRecipients').value;eF_js_showDivPopup('"._SENDMESSAGE."', 2)", 'target' => 'POPUP_FRAME')
    );
    $smarty -> assign("T_SENDALLMAIL_LINK", $sendmail_link);

}

/* Create the selection criteria form */
$form = new HTML_QuickForm("search_courses_form", "post", $_SESSION['s_type'].".php?ctg=search_courses", "", null, true);

// Courses list
$constraints = array('active' => 1, 'archive' => 0, 'return_objects' => false, 'sort' => 'name');
$courses = EFrontCourse :: getAllCourses($constraints);

$course_list = array();
$course_list['0'] = _COURSES;
foreach ($courses as $course) {
    $id = $course['id'];
    $course_list[$id] = str_replace("'", "\'", $course['name']);
}

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
$thisyear = date("Y");
for ($i = ($thisyear-45); $i < ($thisyear+2); $i++) {
    $years[$i] = $i;
}

$date_conditions = array("1" => _FROM, "2" => _ON, "3"=> _TO);
$form -> addElement('select', 'courses' , null, $course_list ,'id="courses_row" onchange="javascript:ajaxPostSearch(\\\'row\\\',this);"');
$form -> addElement('select', 'condition' , null, array("1" => _COMPLETED, "2" => _NOTCOMPLETED, "3" => _NOTASSIGNED),'id="condition_row" onchange="javascript: show_hide_dates(\\\'row\\\', this);ajaxPostSearch(\\\'row\\\',this);"');
$form -> addElement('select', 'from_date_cond' , null, $date_conditions ,'id="from_date_cond_row" onchange="ajaxPostSearch(\\\'row\\\',this);"');
$form -> addElement('select', 'from_date_day' , null, $days ,'id="from_date_day_row" onchange="ajaxPostSearch(\\\'row\\\',this);"');
$form -> addElement('select', 'from_date_month' , null, $months,'id="from_date_month_row" onchange="ajaxPostSearch(\\\'row\\\',this);"');
$form -> addElement('select', 'from_date_year' , null, $years,'id="from_date_year_row" onchange="ajaxPostSearch(\\\'row\\\',this);"');
$form -> addElement('select', 'to_date_cond' , null, $date_conditions ,'id="to_date_cond_row" onchange="ajaxPostSearch(\\\'row\\\',this);"');
$form -> addElement('select', 'to_date_day' , null, $days ,'id="to_date_day_row" onchange="ajaxPostSearch(\\\'row\\\',this);"');
$form -> addElement('select', 'to_date_month' , null, $months,'id="to_date_month_row" onchange="ajaxPostSearch(\\\'row\\\',this);"');
$form -> addElement('select', 'to_date_year' , null, $years,'id="to_date_year_row" onchange="ajaxPostSearch(\\\'row\\\',this);"');
// Hidden where the current query is stored
$form -> addElement('hidden', 'query' , null, 'id="query"');
// Set today as the default to-date
$today = getdate(time());
$form -> setDefaults(array( 'from_date_cond' => '1',
                            'to_date_cond' => '3',
                            'to_date_day' => $today['mday'],
                            'to_date_month' => $today['mon'],
                            'to_date_year' => $today['year']));
// Render and create the form
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
$form -> setRequiredNote(_REQUIREDNOTE);
$form -> accept($renderer);
$smarty -> assign("T_EMPLOYEES_SIZE", 0);
$smarty -> assign('T_SEARCH_COURSE_USERS_FORM', $renderer -> toArray());
/*

            // Supervisors are allowed to see only the data of the employees that work in the braches they supervise

            if ($currentEmployee -> getType() == _SUPERVISOR) {

                $employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN", "users.*, count(job_description_ID) as jobs_num", "(EXISTS (SELECT users_login from module_hcd_employee_works_at_branch where branch_ID in (" . $_SESSION['supervises_branches'] ." ) and module_hcd_employee_works_at_branch.assigned='1' and users.login = module_hcd_employee_works_at_branch.users_login) OR EXISTS (select module_hcd_employees.users_login from module_hcd_employees LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.users_login = module_hcd_employees.users_login where users.login=module_hcd_employees.users_login AND module_hcd_employee_works_at_branch.branch_ID IS NULL)) AND users.user_type <> 'administrator' GROUP BY login", "login limit ".G_DEFAULT_TABLE_SIZE);

            } else if ($_SESSION['s_type'] == 'administrator') {

                $employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN", "users.*, count(job_description_ID) as jobs_num","","","login limit ".G_DEFAULT_TABLE_SIZE);

            }



            $smarty -> assign("T_EMPLOYEES_SIZE", sizeof($employees));

            // Always one employee - administrator

            $smarty -> assign("T_EMPLOYEES", $employees);







$load_editor = true;



$today = getdate(time());                                                                           //Get current time in an array

$today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);                            //Create a timestamp that is today, 00:00. this will be used in calendar for displaying today

(eF_checkParameter($_GET['view_calendar'], 'timestamp')) ? $view_calendar = $_GET['view_calendar']: $view_calendar = $today;    //If a specific calendar date is not defined in the GET, set as the current day to be today

isset($_GET['show_interval']) ? $show_interval = $_GET['show_interval'] : $show_interval = 'day';



#ifdef ENTERPRISE



    if (isset($_GET['type'])) {

        $type = $_GET['type'];

    } else {

        $type = 0;

    }



    $events     = eF_getCalendar(0, $type);                                                         //Get all events for this type





#else

    $events     = eF_getCalendar();                                                                 //Get all events



#endif



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





#ifdef ENTERPRISE



if ($currentUser -> getType() != 'administrator') {

    // No form defined (or needed) in the presentation mode in order to user $form->addElement...

    $calendar_type  = "<select name='calendar_type' id='calendar_type' onChange='javascript:location.href = \"". $_SESSION['s_type'] .".php?ctg=calendar&view_calendar=".$view_calendar."&show_interval=".$show_interval."&type=\" + document.getElementById(\"calendar_type\").value' >";

    $calendar_type .= "<option value='0'"; if ($type == "0") $calendar_type .= "selected='selected'"; $calendar_type .= ">" . _ORGANIZATIONPROFILE . "</option>";



    $calendar_type .= "<option value='1'"; if ($type == "1") $calendar_type .= "selected='selected'"; $calendar_type .= ">" . _EDUCATIONAL. "</option>";

    if ($_SESSION['s_lessons_ID']) {

        $calendar_type .= "<option value='2'"; if ($type == "2") $calendar_type .= "selected='selected'"; $calendar_type .= ">" . $currentLesson -> lesson['name']. "</option>";

    }

    $calendar_type .= "</select>";





    // Used to modify the links for interval selection below as well

    $smarty -> assign("T_CALENDAR_TYPE_LINK", "&type=".$type);

}

#endif

$smarty -> assign("T_TYPE", $type);



$smarty -> assign("T_CALENDAR_TYPE_SELECT", $calendar_type);



if (isset($_GET['delete_calendar']) && eF_checkParameter($_GET['delete_calendar'], 'id')) {

    if (eF_deleteTableData("calendar", "id=".$_GET['delete_calendar'])) {

        $message      = _SUCCESFULLYDELETEDEVENT;

        $message_type = 'success';



#ifdef ENTERPRISE



            $type_in_header = "&type=".$type;

#endif

        eF_redirect(''.basename($_SERVER['PHP_SELF']).'?ctg=calendar&view_calendar='.$view_calendar.'&show_interval='.$show_interval . $type_in_header . '&message='.$message.'&message_type='.$message_type);

    } else {

        $message      = _SOMEPROBLEMEMERGED;

        $message_type = 'failure';

    }

} elseif (isset($_GET['add_calendar']) || (isset($_GET['edit_calendar']) && eF_checkParameter($_GET['edit_calendar'], 'id'))) {

    $smarty -> assign('T_CALENDAR_TITLE' , _CALENDAR);

    $load_editor = true;

    $smarty -> assign("T_POPUP_MODE", true);



    isset($_GET['add_calendar']) ? $post_target = 'add_calendar=1' : $post_target = 'edit_calendar='.$_GET['edit_calendar'];

	



#ifdef ENTERPRISE



        $type_in_header = "&type=".$type;

    //    echo $type_in_header."<br>";

#endif



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



    $tmp = ef_getTableData("users_to_lessons u, lessons l", "u.lessons_ID, l.name", "u.lessons_ID = l.ID AND u.users_LOGIN='".$_SESSION['s_login']."'");





#ifdef ENTERPRISE
    // In HCD module calendar, we also have calendar events that are not associated with any lesson, the Company events. We set their ID = 0 (which will

    // never conflict with a lesson ID, since lesson_ID NOT NULL by the database rule).



        $lessons = array("0" => _ORGANIZATION);

#else

        $lessons = array();

		

#endif

    if ($currentUser -> getType() == 'professor') {

        for ($i = 0; $i < sizeof($tmp); $i++){

            $lessons[$tmp[$i]['lessons_ID']] = $tmp[$i]['name'];

        }

    }




#ifdef ENTERPRISE



        $form -> addElement('select', 'lesson', _CATEGORY, $lessons);

        if ($type == 0) {

            $form -> setDefaults(array('lesson' => '0'));

        } else if ($type == 1) {

            $form -> setDefaults(array('lesson' => $tmp[0]['lessons_ID']));

        } else {

            $form -> setDefaults(array('lesson' => $_SESSION['s_lessons_ID']));

        }



#else

        $form -> addElement('select', 'lesson', _LESSON, $lessons);



#endif





    //$dates = range($month_start, $month_end, 86400);//#filter:timestamp-

    //$dates = array_combine($dates, $dates);

    //array_walk($dates, create_function('&$v,$k', '$v = "#filter:timestamp-".$v."#";'));

    //$form -> addElement('select', 'timestamp', null, $dates, 'class = "inputSelect"');

    $options = array(

        'format'         => 'd m Y',

        'minYear'        => date("Y"),

        'maxYear'        => date('Y') + 1,

    );

    $form -> addElement('date', 'event_date', _DATE, $options);

    $form -> setDefaults(array('event_date' => $view_calendar));



    $form -> addElement('submit', 'submit_event', _SUBMIT, 'class = "flatButton" onClick="if (!document.getElementById(\'mce_editor_0\').contentWindow.frames.document.body.firstChild.nodeValue) { alert(\''._EVENTFIELDISMANDATORY.'\');return false;}"'); //if (document.getElementById(\'event\').value == \'\') { alert(\'hi\');return false; } else { alert(\'ok\'); return true;}



    $form -> addElement('submit', 'submit_event_add_another', _SUBMITANDADDANOTHER, 'class = "flatButton" onclick="if (!document.getElementById(\'mce_editor_0\').contentWindow.frames.document.body.firstChild.nodeValue) { alert(\''._EVENTFIELDISMANDATORY.'\');return false;}"'); //else {document.forms[0].target = \'_self\';}"');

//var aa = 2;document.getElementById('popup_close').onclick = bourdelo()

//

//    $form -> addElement('button', 'submit_another', _SUBMITANDADDANOTHER, 'class = "flatButton" onclick="alert(Hello\")" ');



    if (isset($_GET['edit_calendar'])) {

        $event = eF_getTableData("calendar", "id, data, lessons_ID", "id=".$_GET['edit_calendar']);

        $form -> setDefaults(array("event" => $event[0]['data']));

        $form -> setDefaults(array("lesson" => $event[0]['lessons_ID']));

    }

//$db->debug = true;

    if ($form -> isSubmitted()) {

        if ($form -> validate()) {

            $values = $form -> exportValues();

            $timestamp = mktime(0, 0, 0, $values['event_date']['m'], $values['event_date']['d'], $values['event_date']['Y']);

            if (isset($_GET['add_calendar'])) {

                $fields = array('lessons_ID'  => $values['lesson'],

                                'data'        => $values['event'],

                                'timestamp'   => $timestamp,

                                'active'      => 1,

                                'users_LOGIN' => $_SESSION['s_login']);

				

#ifdef ENTERPRISE

				

                    $type_in_header = "&type=".$type;

				

#endif



                if (eF_insertTableData("calendar", $fields)) {

                    $message      = _SUCCESFULLYADDEDEVENT;

                    $message_type = 'success';



                    if (isset($popup)) {            //In this case, the window is in 'popup mode'. So, when finished, reload parent window.

                        echo "<script>!/\?/.test(parent.location) ? parent.location = parent.location+'?message=".$message."&message_type=".$message_type."' : parent.location = parent.location+'&message=".$message."&message_type=".$message_type."';</script>";

                    } else {



                        if (isset($_POST['submit_event_add_another'])) {

                        // VERY IMPORTANT TO PUT add_another LAST

eF_redirect(''.$form->exportValue('previous_url'). '&pmessage='.$message.'&pmessage_type='.$message_type.'&add_another=1');

//                            eF_redirect(''.basename($_SERVER['PHP_SELF']).'?ctg=calendar&view_calendar='.$view_calendar.'&add_calendar=1&message='.$message.'&message_type='.$message_type.'&add_another=1');

                        } else {

//                            eF_redirect(''.basename($_SERVER['PHP_SELF']).'?ctg=calendar&view_calendar='.$view_calendar.'&show_interval='.$show_interval . $type_in_header . '&message='.$message.'&message_type='.$message_type);

eF_redirect(''.$form->exportValue('previous_url'). '&message='.$message.'&message_type='.$message_type);



                        }

                    }

                } else {

                    $message      = _SOMEPROBLEMEMERGED;

                    $message_type = 'failure';

                }

            } else {

                if (eF_updateTableData("calendar", array('data' => $values['event'], 'timestamp' => $timestamp), "id=".$event[0]['id'])) {

                    $message      = _SUCCESFULLYUPDATEDEDEVENT;

                    $message_type = 'success';

                    eF_redirect(''.basename($_SERVER['PHP_SELF']).'?ctg=calendar&message='.$message.'&show_interval='.$show_interval . $type_in_header .'&message_type='.$message_type);

                } else {

                    $message      = _SOMEPROBLEMEMERGED;

                    $message_type = 'failure';

                }

            }

        }

    }



    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);





    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);

    $form -> setRequiredNote(_REQUIREDNOTE);

    $form -> accept($renderer);



    $smarty -> assign('T_ADD_EVENT_FORM', $renderer -> toArray());

}



if (isset($_GET['add_another'])) {

    $smarty -> assign('T_ADD_ANOTHER', "1");

}



*/
?>
