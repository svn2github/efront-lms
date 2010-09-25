<?php
 /* Permission checking according to type and global settings */
 if ($currentEmployee -> getType() == _EMPLOYEE && isset($GLOBALS['configuration']['show_organization_chart']) && $GLOBALS['configuration']['show_organization_chart'] == 0) {
  eF_redirect($_SERVER['PHP_SELF']."?message=".urlencode(_SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION)."&message_type=failure");
  exit;
 }

 // Get the logo to print it on the page
 if (isset($_GET['print'])) {
  /* The chart will be created by the eF_createBranchesTree with arguments the data gathered */
  $branchesTreePrintable = EfrontBranch :: createBranchesTree();
  $branchesTreePrintable = preg_replace("/<img[^>]+\>/i", " ", $branchesTreePrintable);

  $smarty -> assign('T_CHART_TREE', $branchesTreePrintable);
  $smarty -> assign("T_POPUP_MODE", 1);
 } else {
/*
		$tree = EfrontBranch :: createBranchesTree();
pr(preg_replace(array("/<li.*>/U", "/<ul.*>/U"), array("&nbsp;", "&nbsp;"), $tree));
*/
  /* The chart will be created by the eF_createBranchesTree with arguments the data gathered */
  $smarty -> assign('T_CHART_TREE', EfrontBranch :: createBranchesTree());
  $chart_options = array( //Create calendar options and assign them to smarty, to be displayed at the calendar inner table
  array('text' => _PRINT, 'image' => "16x16/printer.png", 'href' => $_SESSION['s_type'].".php?ctg=module_hcd&op=chart&print=1", "onClick" => "eF_js_showDivPopup('"._PRINTCHART."', 2)", "target" => "POPUP_FRAME"));
  $smarty -> assign("T_CHART_OPTIONS",$chart_options);
 }

?>
