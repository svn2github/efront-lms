<?php
/**
* Smarty plugin: smarty_function_eF_template_printPersonalMessages function. Prints inner table
*
*/
function smarty_function_eF_template_printPersonalMessages($params, &$smarty) {

    $max_title_size = 50;                                           //The maximum length of the title, after which it is cropped with ...
    $list_fold_size = 3;                                            //The folding occurs in this number of lines

    if (isset($params['limit'])) {
        $limit = min($params['limit'], sizeof($params['data']));
    } else {
        $limit = sizeof($params['data']);
    }

    $str = '        
        <table border = "0" width = "100%">';
    for ($i = 0; $i < $list_fold_size && $i < $limit; $i++) {
        $title_message = $params['data'][$i]['title'];
        if (mb_strlen($params['data'][$i]['title']) > $max_title_size) {
            $params['data'][$i]['title'] = mb_substr($params['data'][$i]['title'], 0, $max_title_size).'...';                                 //If the message title is large, cut it and append ...            
        }

        $str .= '
            <tr><td>
                    <span class = "counter">'.($i + 1).'.</span> <a title="'.$title_message.'" href = "forum/messages_index.php?p_message='.$params['data'][$i]['id'].'">'.$params['data'][$i]['title'].'</a></td>
                <td align = "right">#filter:user_login-'.$params['data'][$i]['sender'].'#, ';
        $title2 = ' #filter:timestamp_time-'.$params['data'][$i]['timestamp'].'#';
        $str .= '<span title = "'.$title2.'">'.eF_convertIntervalToTime(time() - $params['data'][$i]['timestamp'], true).' '._AGO."</span>";
        //$str .= '<img src="images/16x16/calendar.png" title="'.$title2.'" alt="'.$title2.'" style = "vertical-align:middle"/>
        $str .= '</td></tr>';
    } 
    
    if ($i == 0) {
        $str .= '
            <tr><td class = "emptyCategory">'._NONEWPERSONALMESSAGES.'</td></tr>
        </table>';
    } else {
        $str .= '
            </table>';
    }
    
    
    return $str; 
}

?>