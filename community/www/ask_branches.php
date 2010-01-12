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
	$result = eF_getTableData("module_hcd_branch", "branch_ID, name","name like '%$preffix%'");  	
}	

for ($i = 0 ; $i < sizeof($result) ; $i ++) {
	$hiname = local_highlightSearch($result[$i]['name'] , $preffix);
   
    $branches[$i] = array('branch_ID'  => $result[$i]['branch_ID'], 
    					 'name'        => $result[$i]['name'], 
    					 'path_string' => $hiname); 
}

$branches = array_values(eF_multisort($branches, 'path_string', 'asc'));    //Sort results based on path string

$str = '<ul>';
for ($k = 0; $k < sizeof($branches); $k++){
	$str = $str.'<li id='.$branches[$k]['branch_ID'].'>'.$branches[$k]['path_string'].'</li>';
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