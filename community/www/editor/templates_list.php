<?php
session_cache_limiter('none');
session_start();
$path = "../../libraries/";

/** The configuration file.*/
include_once $path."configuration.php";

$fileSystemTree = new FileSystemTree('../content/editor_templates/'.$_SESSION['s_login']);

$str = 'var tinyMCETemplateList = [';
foreach ($fileSystemTree ->tree as $key => $value) {
$relative_path = str_replace(G_ROOTPATH.'www/', '',$value['path']);
 $str .= '["'.$value['name'].'", "'.$relative_path.'"],';
}
if (substr($str , -1) == ",") {
 $str = substr($str, 0, -1);
}
$str .= '];';

print $str;
?>
