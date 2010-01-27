<?php

$path = "../libraries/";
/** Configuration file.*/
include_once $path."configuration.php";
session_start();
//$_POST['preffix']  = "%";
//$_POST['preffix']  = "a";

if (isset($_POST['preffix'])) {
 if (mb_strpos($_POST['preffix'], ";") === false) {
     $user = $_POST['preffix'];
 } else {
     $user = mb_substr(strrchr($_POST['preffix'], ";"), 1);
 }
}
//pr($_SESSION);
$users = array();
if (isset($user) && $user) {
    $preffix = $user;
    if ($_GET['type'] == 1) {
        if ($_SESSION['s_type'] == "administrator") {
            $users = eF_getTableData("users", "login,name,surname", "active = 1 and (login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%' OR user_type like '$preffix%')", "login");
  } else {
            $user = EfrontUserFactory :: factory($_SESSION['s_login']);
            if (!$_SESSION['s_lessons_ID']) {
                $students = $user -> getProfessorStudents();
            } else {
                $lesson = new EfrontLesson($_SESSION['s_lessons_ID']);
                $students = array_keys($lesson -> getUsers('student'));
                //pr($students);
            }
            $logins = array();
            $size = sizeof($students);
            for ($i = 0; $i < $size; $i++) {
                if (!in_array($students[$i], $logins)){
                    $logins[] = $students[$i];
                }
            }
            $logins[] = $_SESSION['s_login'];
//pr($logins);
            $students_list = "'".implode("','", $logins)."'";
            $users = eF_getTableData("users", "login,name,surname", "login IN ($students_list) AND (login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%' OR user_type like '$preffix%')", "login");
        }
    } else {
        if($_SESSION['s_type'] == "administrator"){
            $users = eF_getTableData("users", "login,name,surname", "login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%'", "login");
   $users[] = array('login' => "[*]",'name' => _ALLUSERS, 'surname' => _ALLUSERS);
        } else {
            $currentUser = EfrontUserFactory::factory($_SESSION['s_login']);
            $grant_full_access = false;
            if (!$grant_full_access) {
             $logins = array();
    $myGroupsIds = array_keys($currentUser -> getGroups());
    //echo "Groups<BR><BR><BR>";pr($myGroupsIds);
             if (!empty($myGroupsIds)) {
     $result = eF_getTableDataFlat("users JOIN users_to_groups", "distinct users_LOGIN", "users.login = users_to_groups.users_LOGIN AND groups_ID IN ('" . implode("','", $myGroupsIds) ."')");
     $logins = $result['users_LOGIN'];
             }
             $myLessonsIds = array_keys($currentUser -> getLessons());
             //pr($result);echo "Lessons<BR><BR><BR>";pr($myLessonsIds);
             if (!empty($myLessonsIds)) {
                 $result = eF_getTableDataFlat("users JOIN users_to_lessons", "distinct users_LOGIN", "users.login = users_to_lessons.users_LOGIN AND lessons_ID IN ('" . implode("','", $myLessonsIds) ."')");
                 //pr($result);
                 foreach($result['users_LOGIN'] as $login) {
                  if (!in_array($login, $logins)){
                   $logins[] = $login;
                  }
                 }
             }
             $myCoursesIds = eF_getTableDataFlat("users_to_courses", "courses_ID", "users_LOGIN = '". $currentUser -> user['login']."'");
             $myCoursesIds = $myCoursesIds['courses_ID'];
             if (!empty($myCoursesIds)) {
                 $result = eF_getTableDataFlat("users JOIN users_to_courses", "distinct users_LOGIN", "users.login = users_to_courses.users_LOGIN AND courses_ID IN ('" . implode("','", $myCoursesIds) ."')");
                 //pr($result);
                 foreach($result['users_LOGIN'] as $login) {
                  if (!in_array($login, $logins)){
                   $logins[] = $login;
                  }
                 }
             }
             //echo "HCD<BR><BR><BR>";
                 $result = eF_getTableDataFlat("users JOIN module_hcd_employee_works_at_branch", "users_LOGIN", "users.login = module_hcd_employee_works_at_branch.users_LOGIN AND branch_ID IN ('". implode("','", $branches)."')");
                 //pr($result);
                 foreach($result['users_LOGIN'] as $login) {
                  if (!in_array($login, $logins)){
                   $logins[] = $login;
                  }
                 }
                }
                //echo "TELIKA<BR><BR><BR>";pr($logins);
   $related_users_list = "'".implode("','", $logins)."'";
            $users = eF_getTableData("users", "distinct login,name,surname", "(login IN (". $related_users_list . ") OR user_type <> 'student') AND (login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%')", "login");
        }
        if($_SESSION['s_type'] == "professor"){
            $users[] = array('login' => "[*]",'name' => _MYSTUDENTS, 'surname' => _MYSTUDENTS);
        }
            //pr($users);
    }
}
$str = '<ul>';
for ($k = 0; $k < sizeof($users); $k++){
    /*$hilogin = highlight_search($users[$k]['login'], $preffix);

     $hiname = highlight_search($users[$k]['name'], $preffix);

     $hisurname = highlight_search($users[$k]['surname'], $preffix);  */
    $hilogin = $users[$k]['login'];
    $hiname = $users[$k]['name'];
    $hisurname = $users[$k]['surname'];
    if ($users[$k]['login'] == '[*]') {
        $formattedLogins[$users[$k]['login']] = $hiname;
    } else {
        $formattedLogins[$users[$k]['login']] = formatLogin(false, array('login' => $hilogin, 'name' => $hiname, 'surname' => $hisurname));
    }
    //$str = $str.'<li id='.$users[$k]['login'].'>'.$formattedLogin.'</li>';
}
if ($GLOBALS['configuration']['username_format_resolve']) {
    $common = array_diff_assoc($formattedLogins, array_unique($formattedLogins));
    foreach ($common as $key => $value) {
        $originalKey = array_search($value, $formattedLogins);
        $formattedLogins[$originalKey] = $value.' ('.$originalKey.')';
        $formattedLogins[$key] = $value.' ('.$key.')';
    }
}
for ($k = 0; $k < sizeof($users); $k++){
    $str = $str.'<li id='.$users[$k]['login'].'>'.$formattedLogins[$users[$k]['login']].'</li>';
}
$str = $str.'</ul>';
echo $str;
function highlight_search($search_results, $search_criteria, $bgcolor='Yellow'){
    $start_tag = '<span style="background-color: '.$bgcolor.'">';
    $end_tag = '</span>';
    //pr($search_results);pr($search_criteria);
    $search_results = str_replace($search_criteria, $start_tag . $search_criteria . $end_tag, $search_results);
    return $search_results;
}
?>
