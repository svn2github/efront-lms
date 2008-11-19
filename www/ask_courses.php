<?php
/**
*/
session_cache_limiter('none');
session_start();

$path = "../libraries/";

include_once $path."configuration.php";

if (!eF_checkUser($_SESSION['s_login'], $_SESSION['s_password'])) {
    eF_printMessage("You must login to access this page");
    exit;
}

eF_checkParameter($_POST['preffix'], 'text') ? $preffix = $_POST['preffix'] : $preffix = '%';

if($_SESSION['s_type'] == "administrator"){
	$result = eF_getTableData("courses", "id, name, directions_ID","active=1 AND name like '%$preffix%'");  	
} else {
	$result = eF_getTableData("courses c, users_to_courses uc", "c.id, c.name, c.directions_ID", "uc.user_type = 'professor' AND c.active=1 AND c.id = uc.courses_ID AND uc.users_LOGIN='".$_SESSION['s_login']."' AND c.name like '%$preffix%'"); 	
}	

$courses        = array();              
$directionsTree = new EfrontDirectionsTree();
$directionPaths = $directionsTree -> toPathString();

for ($i = 0 ; $i < sizeof($result) ; $i ++) {
	$hiname = local_highlightSearch($result[$i]['name'], $preffix);
    
	$pathString  = $directionPaths[$result[$i]['directions_ID']].'&nbsp;&rarr;&nbsp;'.$hiname;
    $courses[$i] = array('id'          => $result[$i]['id'], 
    					 'name'        => $result[$i]['name'], 
    					 'path_string' => $pathString); 
}

$courses = array_values(eF_multisort($courses, 'path_string', 'asc'));    //Sort results based on path string

$str = '<ul>';
for ($k = 0; $k < sizeof($courses); $k++){
	$str = $str.'<li id='.$courses[$k]['id'].'>'.$courses[$k]['path_string'].'</li>';
}
$str .= '</ul>';

echo $str;


function local_highlightSearch($searchResults, $searchCriteria, $bgcolor = 'Yellow'){
    $startTag = '<span style="background-color: '.$bgcolor.'">';
    $endTag   = '</span>';
    $searchResults = eregi_replace($searchCriteria, $startTag.$searchCriteria.$endTag, $searchResults);
      
    return $searchResults;
}
?>