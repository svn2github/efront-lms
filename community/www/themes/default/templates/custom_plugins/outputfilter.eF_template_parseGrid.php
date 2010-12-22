<?php
/**
*
*/

function smarty_outputfilter_eF_template_parseGrid($compiled, &$smarty) {
 if (isset($_GET['ajax'])) {
  if (($stringStart = strpos($compiled, "<!--ajax:".$_GET['ajax']."-->")) !== false && ($stringEnd = strpos($compiled, "<!--/ajax:".$_GET['ajax']."-->")) !== false) {
   $new = mb_substr($compiled, $stringStart+strlen("<!--ajax:".$_GET['ajax']."-->"), $stringEnd-$stringStart-strlen("<!--ajax:".$_GET['ajax']."-->"));
  }

  return $new;
 } else {
  return $compiled;
 }
}
?>
