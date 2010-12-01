<?php

$path = '../../../libraries/';
session_start();
require_once($path.'configuration.php');

$url = urldecode($_GET['url']);
$smarty->assign('_FUZE_PROF_LAUNCHER_URL',$url);
$smarty->assign('_FUZE_PROF_LAUNCHER_LOGO', G_MODULESURL . 'module_fuze_meetings' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'efront_logo.png');
$template = G_MODULESPATH . 'module_fuze_meetings' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'smarty.professor.launcher.tpl';
$smarty->display($template);
