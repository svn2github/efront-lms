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
	$result = eF_getTableData("groups LEFT JOIN users_to_groups ON groups.id = users_to_groups.groups_ID", "id, name, description, count(users_LOGIN) as users_count","active=1 AND (name like '%$preffix%' OR description like '%$preffix%')", "", "groups_ID");  	
}	

for ($i = 0 ; $i < sizeof($result) ; $i ++) {
    if ($result[$i]['description']) {
         $result[$i]['name'] .= "&nbsp;-" .$result[$i]['description']; 
    }
    $result[$i]['name'] .= "&nbsp;(" . $result[$i]['users_count'] . ")";
	$hiname = local_highlightSearch($result[$i]['name'] , $preffix);
   
    $groups[$i] = array('id'          => $result[$i]['id'], 
    					 'name'        => $result[$i]['name'], 
    					 'path_string' => $hiname); 
}

$groups = array_values(eF_multisort($groups, 'path_string', 'asc'));    //Sort results based on path string

$str = '<ul>';
for ($k = 0; $k < sizeof($groups); $k++){
	$str = $str.'<li id='.$groups[$k]['id'].'>'.$groups[$k]['path_string'].'</li>';
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