<?php
/**

* prints a block

*

*/
function smarty_insert_customBlock($params, &$smarty) {
 $str = '';
 if (is_file(G_EXTERNALPATH.$params['file'])) {
  $str = file_get_contents(G_EXTERNALPATH.$params['file']);
 }
 return $str;
}
?>
