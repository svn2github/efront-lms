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
                $dif_tables .= " JOIN users_to_courses as users_to_courses".$id." ON users.login = users_to_courses".$id.".users_login";
                if ($search_string != "") {
                    $search_string .= " AND ";
                }
                $search_string .= "users_to_courses".$id.".courses_ID = '".$value."' and users.active=1";

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

        $employees = eF_getTableData($dif_tables, "users.*",$search_string,"");

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

// Create the main menu headers







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

/* Create the link to the search for course user page */
if ($currentUser -> getType() == "administrator") {




  $options = array(array('image' => '16x16/scorm.png', 'title' => _SEARCHFORUSER, 'link' => $_SESSION['s_type'].'.php?ctg=search_users' , 'selected' => false),
       array('image' => '16x16/glossary.png', 'title' => _SEARCHCOURSEUSERS, 'link' => 'administrator.php?ctg=search_courses', 'selected' => true));


 $smarty -> assign("T_TABLE_OPTIONS", $options);
}
?>
