<?php
/**
* Smarty plugin: eF_template_printBackButton function
*/
function smarty_function_eF_template_printBackButton($params, &$smarty) {
    switch ($params['type']) {
        case 'link':
            $code = '
                <a href = "" onclick = "history.back();return false">'._BACK.'</a>';
            break;
        case 'button':
        default:                                                                                        //defaults to button
            $code = '
                <input class = "flatButton" type = "button" value = "'._BACK.'" onclick = "history.back()" />';
            break;
    }        
    
    return $code;
}

?>