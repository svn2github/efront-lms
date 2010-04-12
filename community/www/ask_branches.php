<?php
/**

*/
session_cache_limiter('none');
session_start();
$path = "../libraries/";

include_once $path."configuration.php";
include_once $path."module_hcd_tools.php";

if (!eF_checkUser($_SESSION['s_login'], $_SESSION['s_password'])) {
    eF_printMessage("You must login to access this page");
    exit;
}

eF_checkParameter($_POST['preffix'], 'text') ? $preffix = $_POST['preffix'] : $preffix = '%';

if($_SESSION['s_type'] == "administrator"){
 $result = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","name like '%$preffix%'");

 $ordered_branches = eF_createBranchesTreeSelect($result, 1);
 foreach ($result as $key => $branch) {
  $ordered_branches[$branch['branch_ID']] = array("branch_ID" => $branch['branch_ID'],
              "name" => $ordered_branches[$branch['branch_ID']]);
 }
 $result = $ordered_branches;
}

foreach ($result as $key => $branch) {
 $hiname = local_highlightSearch($branch['name'] , $preffix);

    $branches[$key] = array('branch_ID' => $branch['branch_ID'],
          'name' => $branch['name'],
          'path_string' => $hiname);
}

$str = '<ul>';
foreach ($branches as $branch) {
 $str = $str.'<li id='.$branch['branch_ID'].'>'.$branch['path_string'].'</li>';
}
$str .= '</ul>';

echo $str;


function local_highlightSearch($searchResults, $searchCriteria, $bgcolor = 'Yellow'){
    $startTag = '<span style="background-color: '.$bgcolor.'">';
    $endTag = '</span>';
    $searchResults = eregi_replace($searchCriteria, $startTag.$searchCriteria.$endTag, $searchResults);

    return $searchResults;
}
?>
