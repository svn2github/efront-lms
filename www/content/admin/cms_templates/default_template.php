<?php
$path = "../../../libraries/";
require_once $path."configuration.php";

$loadScripts = array('EfrontScripts', 'scriptaculous/prototype', 'scriptaculous/scriptaculous', 'scriptaculous/effects');

$content = <<<EOT
put_content_here
EOT;

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($loadScripts));
$smarty -> assign('T_CONTENT', $content);
$smarty -> display('cms_templates/default_template.tpl');
?>