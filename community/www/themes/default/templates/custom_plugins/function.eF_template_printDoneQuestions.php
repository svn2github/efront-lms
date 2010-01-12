<?php
/**
* Smarty plugin: smarty_function_eF_template_printDoneQuestions function. Prints inner table
*
*/
function smarty_function_eF_template_printDoneQuestions($params, &$smarty) {

    $max_title_size = 25;                                           //The maximum length of the title, after which it is cropped with ...
    $list_fold_size = 20;                                            //The folding occurs in this number of lines

    if (isset($params['limit'])) {
        $limit = min($params['limit'], sizeof($params['data']));
    } else {
        $limit = sizeof($params['data']);
    }

    $str = '        
        <table border = "0" width = "100%">';
    for ($i = 0; $i < $list_fold_size && $i < $limit; $i++) {
        strip_tags($params['data'][$i]['text']);
        $title_message = strip_tags($params['data'][$i]['text']);
        if (mb_strlen($params['data'][$i]['text']) > $max_title_size) {
            $params['data'][$i]['text'] = mb_substr($params['data'][$i]['text'], 0, $max_title_size).'...';                                 //If the message title is large, cut it and append ...            
        }
        $str .= '
            <tr><td>
                    <span class = "counter">'.($i + 1).'.</span> <span title="'.$title_message.'"> '.$params['data'][$i]['text'].'</span></td>
                <td align = "right">#filter:user_login-'.$params['data'][$i]['users_LOGIN'].'# ';
        $title2 = '#filter:timestamp-'.$params['data'][$i]['timestamp'].'#';
        $str .= '<img src="images/16x16/calendar.png" title="'.$title2.'" alt="'.$title2.'" style = "vertical-align:middle"/> <a class = "editLink" href = "'.basename($_SERVER['PHP_SELF']).'?ctg=tests&correct_question='.$params['data'][$i]['id'].'"><img src = "images/16x16/edit.png" alt = "'._CORRECTION.'" title = "'._CORRECTION.'" border = "0"/></a>';
        $str .= '
                </td></tr>';
    } 
    
    if ($i == 0) {
        $str .= '
            <tr><td class = "emptyCategory">'._NOQUESTIONSTOCORRECTFORTHISLESSON.'</td></tr>
        </table>';
    } elseif ($limit > $list_fold_size) {
        $str .= '
            <tr><td>
                    <img src = "images/others/plus.png" onclick = "show_hide(this, \'done_questions\');">
                </td></tr>
        </table>
    
            <table border = "0" width = "100%" id = "done_questions" style = "display:none">';
        for ($i = $list_fold_size; $i < $limit; $i++) {
            strip_tags($params['data'][$i]['text']);
            $title_message = $params['data'][$i]['text']; 
            if (mb_strlen($params['data'][$i]['text']) > $max_title_size) {
                $params['data'][$i]['text'] = mb_substr($params['data'][$i]['text'], 0, $max_title_size).'...';                                 //If the message title is large, cut it and append ...            
            }
            $str .= '
                <tr><td>
                        <span class = "counter">'.($i + 1).'.</span> <span title="'.$title_message.'"> '.$params['data'][$i]['text'].'</span></td>
                    <td align = "right">#filter:user_login-'.$params['data'][$i]['users_LOGIN'].'# ';
            $title2 = '#filter:timestamp-'.$params['data'][$i]['timestamp'].'#';
            $str .= '<img src="images/16x16/calendar.png" title="'.$title2.'" alt="'.$title2.'" style = "vertical-align:middle"/> (<a href = "'.basename($_SERVER['PHP_SELF']).'?ctg=tests&correct_question='.$params['data'][$i]['id'].'"><img src = "images/16x16/edit.png" alt = "'._CORRECTION.'" title = "'._CORRECTION.'" border = "0"/></a>)';
            $str .= '
                    </td></tr>';
        } 
        $str .= '
            </table>';
    } else {
        $str .= '
        </table>';        
    }        
    return $str; 
}

?>