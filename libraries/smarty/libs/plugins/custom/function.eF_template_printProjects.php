<?php
/**
* Smarty plugin: smarty_function_eF_template_printPersonalMessages function. Prints inner table
*
*/
function smarty_function_eF_template_printProjects($params, &$smarty) {

    $max_title_size = 50;                                           //The maximum length of the title, after which it is cropped with ...
    $list_fold_size = 5;                                            //The folding occurs in this number of lines

    if (isset($params['limit'])) {
        $limit = min($params['limit'], sizeof($params['data']));
    } else {
        $limit = sizeof($params['data']);
    }
    
    $params['data'] = array_values($params['data']);
    
    $str = '        
        <table border = "0" width = "100%">';
    for ($i = 0; $i < $list_fold_size && $i < $limit; $i++) {
        $title_message = $params['data'][$i]['title'];
        if (mb_strlen($params['data'][$i]['title']) > $max_title_size) {
            $params['data'][$i]['title'] = mb_substr($params['data'][$i]['title'], 0, $max_title_size).'...';                                 //If the project title is large, cut it and append ...            
        }
        $str .= '
            <tr><td>
                    <span class = "counter">'.($i + 1).'.</span> <a title="'.$title_message.'" href = "student.php?ctg=projects&view_project='.$params['data'][$i]['id'].'">'.$params['data'][$i]['title'].'</a></td>
                <td align = "right">#filter:user_login-'.$params['data'][$i]['creator_LOGIN'].'#, ';
        $title2 = _DEADLINE.': #filter:timestamp_time-'.$params['data'][$i]['deadline'].'#';
        $str .= '<span title = "'.$title2.'">'._EXPIRESIN.' '.eF_convertIntervalToTime($params['data'][$i]['deadline'] - time(), true)."</span>";
        //$str .= '<img src="images/16x16/calendar.png" title="'.$title2.'" alt="'.$title2.'" style = "vertical-align:middle"/>
        $str .= '</td></tr>';
    } 
    
    if ($i == 0) {
        $str .= '
            <tr><td class = "emptyCategory">'._NOPROJECTS.'</td></tr>
        </table>';
    } else {
        $str .= '
            </table>';
    }
    
   
    return $str; 
}

?>