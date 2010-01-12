<?php

function smarty_function_eF_template_printUserName($params, &$smarty) {
    if ($params['login'] && $params['name'] && $params['surname']) {
        $str = $params['surname'].' '.mb_substr($params['name'], 0, 1).'. ('.$params['login'].')';
    }
    
    return $str;
}

?>