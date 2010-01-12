<?php
/**
* Smarty plugin: eF_template_printTimestampToDate function
*/
function smarty_function_eF_template_printTimestampToDate($params, &$smarty) {
    if (!isset($params['timestamp']) || $params['timestamp'] <= 0) {
        $params['timestamp'] = time();
    } 
    
    //$str = date("d M Y", $params['timestamp']);
	$str = eF_timestampToDate($params['timestamp']);
    return $str;
}

?>