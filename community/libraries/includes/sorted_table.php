<?php

if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if (isset($_GET['ajax']) && $_GET['ajax'] == $tableName) {
 if (!$alreadySorted) {
  list($tableSize, $dataSource) = filterSortPage($dataSource);
  $smarty -> assign("T_TABLE_SIZE", $tableSize);
 }
 if (!empty($dataSource)) {
  $smarty -> assign("T_DATA_SOURCE", $dataSource);
 }

 $smarty -> assign("T_SORTED_TABLE", $tableName);
 if ($benchmark) {
  $benchmark -> set('script');
 }
 $smarty -> display($_SESSION['s_type'].'.tpl');
 if ($benchmark) {
  $benchmark -> set('smarty');
  $benchmark -> stop();
  $output = $benchmark -> display();
  if (G_DEBUG) {
   echo $output;
  }
 }
 exit;
}
