<?php


$path = "../libraries/";
/** Configuration file.*/
include_once $path."configuration.php";
session_start();



if (mb_strpos($_POST['preffix'], ";") == false){
    $user = $_POST['preffix'];
} else {
    $user = mb_substr(strrchr($_POST['preffix'], ";"), 1);
}

if ($user != "") {
    $preffix   = $user;
    if ($_GET['type'] == 1){
        if ($_SESSION['s_type'] == "administrator") {
            $users = eF_getTableData("users", "login,name,surname", "active = 1 and (login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%' OR user_type like '$preffix%')", "login");
        } else {
            $user     = EfrontUserFactory :: factory($_SESSION['s_login']);
            $students = $user -> getProfessorStudents();
            $logins = array();
            for ($i = 0; $i < sizeof($students); $i++) {
                if (!in_array($logins)){
                    $logins[] = $students[$i];
                }
            }
            $logins[] = $_SESSION['s_login'];
            $students_list = "'".implode("','", $logins)."'";
            $users         = eF_getTableData("users", "login,name,surname", "login IN ($students_list) AND (login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%' OR user_type like '$preffix%')", "login");
        }
    } else {
        $users = eF_getTableData("users", "login,name,surname", "login like '$preffix%' OR name like '$preffix%' OR surname like '$preffix%'", "login");
        if($_SESSION['s_type'] == "administrator"){
            $users[] = array('login' => "[*]",'name' => _ALLUSERS);
        }elseif($_SESSION['s_type'] == "professor"){
            $users[] = array('login' => "[*]",'name' => _MYSTUDENTS);
        }
    }

    $str =  '<ul>';
    for ($k = 0; $k < sizeof($users); $k++){
        $hilogin = highlight_search($users[$k]['login'], $preffix);
        $hiname = highlight_search($users[$k]['name'], $preffix);
        $hisurname = highlight_search($users[$k]['surname'], $preffix);
        $str = $str.'<li id='.$users[$k]['login'].'>'.$hilogin.'<span class="informal">&nbsp;('.$hiname.'&nbsp;'.$hisurname.')</span></li>';
    }
    $str = $str.'</ul>';

    echo $str;
}

function highlight_search($search_results, $search_criteria, $bgcolor='Yellow'){

    $start_tag = '<span style="background-color: '.$bgcolor.'">';
    $end_tag = '</span>';
    $search_results = eregi_replace($search_criteria, $start_tag . $search_criteria . $end_tag, $search_results);
    return $search_results;

}
?>



