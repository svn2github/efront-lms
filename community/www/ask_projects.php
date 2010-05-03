<?php
/**

*/
$path = "../libraries/";
error_reporting(E_ALL);
/** Configuration file.*/
include_once $path."configuration.php";
session_start();

$preffix = $_POST['preffix'];
$currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);

if($_SESSION['s_type'] == "administrator"){
 $projects_info = eF_getTableDataFlat("projects p, lessons l", "p.id, p.title as project_title, l.name as lesson_name ","p.lessons_ID = l.id AND p.title like '%$preffix%'", "p.title");
} else {
 $projects_info = eF_getTableDataFlat("projects p, users_to_lessons ul, lessons l", "p.id, p.title as project_title, l.name as lesson_name ", "(ul.user_type = 'professor' OR ul.user_type =".$currentUser->user['user_types_ID'].") AND p.lessons_ID = l.id AND ul.users_LOGIN='".$_SESSION['s_login']."' and ul.lessons_ID=l.id AND p.title like '%$preffix%'", "p.title");
}

   $info_array = array();
            for($i = 0 ; $i < sizeof($projects_info['project_title']) ; $i ++){
                $hiname = highlight_search($projects_info['project_title'][$i], $preffix);

    $path_string = $projects_info['lesson_name'][$i]."->".$hiname;
                $info_array[$i] = array('id' => $projects_info['id'][$i],'name' => $projects_info['project_title'][$i],'path_string' =>$path_string);
            }



$str = '<ul>';
for ($k = 0; $k < sizeof($info_array); $k++){
 $str = $str.'<li id='.$info_array[$k]['id'].'>'.$info_array[$k]['path_string'].'</li>';
}
$str = $str.'</ul>';

echo $str;

function highlight_search($search_results, $search_criteria, $bgcolor='Yellow'){
    $start_tag = '<span style="background-color: '.$bgcolor.'">';
    $end_tag = '</span>';
    $search_results = eregi_replace($search_criteria, $start_tag . $search_criteria . $end_tag, $search_results);
    return $search_results;
}
?>
