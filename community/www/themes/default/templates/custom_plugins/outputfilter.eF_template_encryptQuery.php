<?php
/**

*/
function smarty_outputfilter_eF_template_encryptQuery($compiled, &$smarty) {
    $re = "/(href\s*=\s*['\"][^>]*\?)(.*)(['\"])/U";
    //preg_match_all($re, $compiled, $matches);		//This does nothing, but is left here commented-out in case we want to quickly check which urls are matched
    $compiled = preg_replace_callback($re, "local_encryptQueryReplace", $compiled);
    return $compiled;
}

function local_encryptQueryReplace($matches) {
    $parsedUrl = parse_url($matches[0]);
    //Convert only internal links
    if (stristr($parsedUrl['host'], 'http') === false || stristr('http://'.$parsedUrl['path'], G_SERVERNAME) !== false || stristr('https://'.$parsedUrl['path'], G_SERVERNAME) !== false) {
  $matches[2] = 'cru='.encryptString($matches[2]);
    }
    return $matches[1].$matches[2].$matches[3];
}
?>
