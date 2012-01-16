<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/* Permission checking according to type and global settings */
if ($currentEmployee -> getType() == _EMPLOYEE && isset($GLOBALS['configuration']['show_organization_chart']) && $GLOBALS['configuration']['show_organization_chart'] == 0) {
 eF_redirect($_SERVER['PHP_SELF']."?message=".urlencode(_SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION)."&message_type=failure");
 exit;
}
$loadScripts[] = 'hcd';
if (!isset($_COOKIE['orgChartMode']) && stripos($_SERVER['HTTP_USER_AGENT'], 'msie') !== false) {
 setcookie("orgChartMode", 1);
 $_COOKIE['orgChartMode'] = 1;
}

if ($_GET['ajax']) {
 $branchesTree = new EfrontBranchesTree();
 /* The chart will be created by the eF_createBranchesTree with arguments the data gathered */
 if ($_COOKIE['orgChartMode']) {
  $branchesTreePrintable = $branchesTree -> toHtml(false, true);
  //	$branchesTreePrintable = EfrontBranch :: createBranchesTree(true);
 } else {
  $branchesTreePrintable = $branchesTree -> toHtml(false, true);
  //	$branchesTreePrintable = EfrontBranch :: createBranchesTree();
 }

 if (isset($_GET['print'])) {
  $branchesTreePrintable = preg_replace("/<img[^>]+\>/i", " ", $branchesTreePrintable);
  $smarty -> assign("T_POPUP_MODE", 1);
 } else {
  $smarty -> assign("T_CHART_OPTIONS", array(array('text' => _PRINT, 'image' => "16x16/printer.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=module_hcd&op=chart&print=1", "onClick" => "eF_js_showDivPopup('"._PRINTCHART."', 2)", "target" => "POPUP_FRAME")));
 }
 $smarty -> assign('T_CHART_TREE', $branchesTreePrintable);

 $smarty->display($_SESSION['s_type'].'.tpl');
 exit;
}
