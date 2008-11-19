<?php
/**
* Smarty plugin: eF_template_printCloseButton function
*/
function smarty_function_eF_template_printCloseButton($params, &$smarty) {
    if (!isset($params['reload']) || !$params['reload']) {        
        $onclick_str = 'javascript:window.close()';
    } else {
        if (isset($params['page']) && $params['page']) {
            $onclick_str = 'javascript:self.opener.location = \''.urldecode($params['page']).'\'; window.close()';
        } else {
            $onclick_str = 'javascript:self.opener.location.reload(); window.close()';
        }
    }
    
    $str = '
            <input class = "flatButton" type = "button" onClick = "'.$onclick_str.'" value = "'._CLOSEWINDOW.'" />';
    
    return $str;
}

?>