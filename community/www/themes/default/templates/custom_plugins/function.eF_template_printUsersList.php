<?php
/**
* Smarty plugin: smarty_function_eF_template_printUsersList function.
*
*/
function smarty_function_eF_template_printUsersList($params, &$smarty) {

    $admin_str     = '<option value = "-1" disabled>---- '._ADMINISTRATORS.' ----</option>';
    $professor_str = '<option value = "-1" disabled>---- '._PROFESSORS.' ----</option>';
    $student_str   = '<option value = "-1" disabled>---- '._STUDENTS.' ----</option>';

    for ($i = 0; $i < sizeof($params['data']); $i++) {
        $params['selected'] == $params['data'][$i]['login'] ? $selected = 'selected' : $selected = '';
        switch ($params['data'][$i]['user_type']) {
            case 'administrator':
                $admin_str .= '
                    <option value = "'.$params['data'][$i]['login'].'" '.$selected.'>'.$params['data'][$i]['login'].' ('.$params['data'][$i]['name'].'&nbsp;'.$params['data'][$i]['surname'].')</option>';
                break;
            case 'professor':
                $professor_str .= '
                    <option value = "'.$params['data'][$i]['login'].'" '.$selected.'>'.$params['data'][$i]['login'].' ('.$params['data'][$i]['name'].'&nbsp;'.$params['data'][$i]['surname'].')</option>';
                break;
            case 'student':
                $student_str .= '
                    <option value = "'.$params['data'][$i]['login'].'" '.$selected.'>'.$params['data'][$i]['login'].' ('.$params['data'][$i]['name'].'&nbsp;'.$params['data'][$i]['surname'].')</option>';
                break;
            default:
                break;
        }
    }

    return $professor_str.$student_str.$admin_str;
}

?>