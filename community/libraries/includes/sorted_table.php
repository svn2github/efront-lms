<?php

if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if (isset($_GET['ajax']) && $_GET['ajax'] == $tableName) {
 if (!$alreadySorted) {
  isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

  if (isset($_GET['sort']) && $_GET['sort'] && eF_checkParameter($_GET['sort'], 'text')) {
   $sort = $_GET['sort'];
   isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
  } else {
   $sort = key(current($dataSource)); //The first field of the data array is the default sorting field
   $order = 'desc';
  }

  $dataSource = eF_multiSort($dataSource, $sort, $order);
  if (isset($_GET['filter'])) {
   $dataSource = eF_filterData($dataSource, $_GET['filter']);
  }
  $smarty -> assign("T_TABLE_SIZE", sizeof($dataSource));

  if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
   isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
   $dataSource = array_slice($dataSource, $offset, $limit);
  }
 }
 if (!empty($dataSource)) {
  $smarty -> assign("T_DATA_SOURCE", $dataSource);
 }

 $smarty -> assign("T_SORTED_TABLE", $tableName);
 $benchmark -> set('script');
 $smarty -> display($_SESSION['s_type'].'.tpl');
 $benchmark -> set('smarty');
 $benchmark -> stop();
 if (G_DEBUG) {
  echo $benchmark -> display();
 }
 exit;
}


?>
