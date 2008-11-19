<?php
/**
*
*/
require_once "libs/Smarty.class.php";

$smarty = new Smarty;

$smarty -> template_dir  = $path.'smarty/templates/';
$smarty -> compile_dir   = $path.'smarty/templates_c/';
$smarty -> config_dir    = $path.'smarty/configs/';
$smarty -> cache_dir     = $path.'smarty/cache/';
$smarty -> plugins_dir[] = $path.'smarty/libs/plugins/custom/';

is_dir($smarty -> cache_dir)   or mkdir($smarty -> cache_dir, 0755);                      //Create cache and template cache directories, if they don't exist
is_dir($smarty -> compile_dir) or mkdir($smarty -> compile_dir, 0755);

?>