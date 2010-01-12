<?php
/**
 * 
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


if ($_SESSION['s_type'] == "administrator"){
	$result = eF_getTableData("lessons", "id,name,directions_ID","active=1 AND name like '%$preffix%'", "name");  	
} else {
	$result = eF_getTableData("users_to_lessons ul, lessons l", "l.id, l.name,l.directions_ID", "ul.users_LOGIN='".$_SESSION['s_login']."' and ul.user_type = 'professor' and ul.lessons_ID=l.id AND l.name like '%$preffix%'", "l.name"); 	
}	

$lessons        = array();              
$directionsTree = new EfrontDirectionsTree();
$directionPaths = $directionsTree -> toPathString();

for ($i = 0 ; $i < sizeof($result) ; $i ++) {
	$hiname = local_highlightSearch($result[$i]['name'], $preffix);
    
	$pathString  = $directionPaths[$result[$i]['directions_ID']].'&nbsp;&rarr;&nbsp;'.$hiname;
    $lessons[$i] = array('id'          => $result[$i]['id'], 
    					 'name'        => $result[$i]['name'], 
    					 'path_string' => $pathString); 
}


$lessons = array_values(eF_multisort($lessons, 'path_string', 'asc'));    //Sort results based on path string

$str = '<ul>';
for ($k = 0; $k < sizeof($lessons); $k++){
	$str = $str.'<li id='.$lessons[$k]['id'].'>'.$lessons[$k]['path_string'].'</li>';
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