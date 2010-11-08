<?php

class PP {

 public function __construct() {
  $res = eF_getTableData("users", "*", "id=1");
  print_r($res);
  die('mesa');
 }

}
