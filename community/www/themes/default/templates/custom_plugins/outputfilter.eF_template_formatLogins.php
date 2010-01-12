<?php
/**
* Replaces occurences of the form #filter:user_login-asdfas# with a personal message link
*/

function smarty_outputfilter_eF_template_formatLogins($compiled, &$smarty) {    
    $compiled = preg_replace_callback("/#filter:login-(.*)#/U", 'formatLogin', $compiled);
    
    return $compiled;
}


?>