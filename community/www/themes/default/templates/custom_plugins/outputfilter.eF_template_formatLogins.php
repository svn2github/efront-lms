<?php
/**

*/
function smarty_outputfilter_eF_template_formatLogins($compiled, &$smarty) {
    $compiled = preg_replace_callback("/#filter:login-(.*)#/U", create_function('$matches', 'return formatLogin($matches[1]);'), $compiled);

    return $compiled;
}


?>
