<?php

$path = "../../../libraries/";                //Define default path


require_once $path."configuration.php";
$users = EfrontUser :: getUsersOnline();

$smarty -> assign("T_NUMBER", sizeof($users));

$smarty -> display('editor/online_users.tpl');
?>