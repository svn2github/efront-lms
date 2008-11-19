<?php
/**
* Smarty plugin: smarty_function_eF_template_printOnlineUsers function. Prints inner table
*
*/
function smarty_function_eF_template_printOnlineUsers($params, &$smarty) {

    isset($params['align']) ? $align = $params['align'] : $align = 'left';

    $str = '
        <table border = "0" width = "100%">
            <tr><td align = '.$align.'>';
    for ($i = 0; $i < sizeof($params['data']); $i++) {
        //$params['data'][$i]['type'] == 'professor' ? $style = "font-weight:bold;" : $style = '';
        $i > 0 && $i < sizeof($params['data']) ? $comma = ', ' : $comma = '';

        $str .= $comma.'#filter:user_login-'.$params['data'][$i]['login'].'#';
    }

    if ($i == 0) {
        $str .= '<tr><td class = "empty_category">'._NOOTHERUSERSONLINE.'</td></tr>';
    }

    $str .= '
            </td></tr>
        </table>';

    return $str;
}

?>