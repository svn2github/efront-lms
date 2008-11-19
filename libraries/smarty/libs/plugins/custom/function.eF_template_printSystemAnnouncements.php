<?php
/**
* Smarty plugin: smarty_function_eF_template_printSystemAnnouncements function. Prints inner table
*
*/
function smarty_function_eF_template_printSystemAnnouncements($params, &$smarty) {
    
    $max_title_size = 50;                                           //The maximum length of the title, after which it is cropped with ...
    $list_fold_size = 5;                                            //The folding occurs in this number of lines
    
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
        
        $new_str = '';
        if (isset($params['new_news']) && in_array($params['data'][$i]['id'], array_keys($params['new_news']))) {
            $new_str = 'font-weight:bold;color:darkgreen;';
        }
    
        $str .= '
            <tr><td>
                    <span class = "counter">'.($i + 1).'.</span> <a title="'.$title_message.'" href = "system_announcements.php?id='.$params['data'][$i]['id'].'" style = "'.$new_str.'" onClick = "eF_js_showDivPopup(\''._MESSAGE.'\', new Array(\'500px\', \'300px\'))" target = "POPUP_FRAME">'.$params['data'][$i]['title'].'</a></td>
                <td align = "right">#filter:user_login-'.$params['data'][$i]['users_LOGIN'].'# ';
        $title2 =' #filter:timestamp-'.$params['data'][$i]['timestamp'].'#';
        $str .= '<img src="images/16x16/calendar.png" title="'.$title2.'" alt="'.$title2.'" style = "vertical-align:middle"/>';
        
        if ($_SESSION['s_type'] == 'administrator') {
            $str .= '
                    <a class = "editLink"   href = "system_announcements.php?id='.$params['data'][$i]['id'].'&op=change" target = "POPUP_FRAME" onClick = "eF_js_showDivPopup(\''._CORRECTION.'\', new Array(\'500px\', \'300px\'))"><img src = "images/16x16/edit.png" alt = "'._CORRECTION.'" title = "'._CORRECTION.'" border = "0"/></a>
                    <a class = "deleteLink" href = "system_announcements.php?id='.$params['data'][$i]['id'].'&op=delete" target = "POPUP_FRAME"><img src = "images/16x16/delete.png" alt = "'._DELETE.'" title = "'._DELETE.'" border = "0"/></a>';
        }
        $str .= '
                </td></tr>';
    } 
    
    if ($i == 0) {
        $str .= '
            <tr><td class = "empty_category">'._NOSYSTEMANNOUNCEMENTSPOSTED.'</td></tr>
        </table>';
    } elseif ($limit > $list_fold_size) {
        $str .= '
            <tr><td>
                    <img src = "images/others/plus.png" onclick = "show_hide(this, \'news\');">
                </td></tr>
        </table>
    
            <table border = "0" width = "100%" id = "news" style = "display:none">';
        for ($i = $list_fold_size; $i < $limit; $i++) {
            $title_message = $params['data'][$i]['title'];
            if (mb_strlen($params['data'][$i]['title']) > $max_title_size) {
                $params['data'][$i]['title'] = mb_substr($params['data'][$i]['title'], 0, $max_title_size).'...';                                 //If the message title is large, cut it and append ...            
            }

            $str .= '
            <tr><td>
                    <span class = "counter">'.($i + 1).'.</span> <a title="'.$title_message.'" href = "system_announcements.php?id='.$params['data'][$i]['id'].'" style = "'.$new_str.'" onClick = "eF_js_showDivPopup(\''._MESSAGE.'\', new Array(\'500px\', \'300px\'))" target = "POPUP_FRAME">'.$params['data'][$i]['title'].'</a></td>
                <td align = "right">#filter:user_login-'.$params['data'][$i]['users_LOGIN'].'#';
            $title2 = ' #filter:timestamp-'.$params['data'][$i]['timestamp'].'#';
             $str .= '<img src="images/16x16/calendar.png" title="'.$title2.'" alt="'.$title2.'" style = "vertical-align:middle"/>';
            if ($_SESSION['s_type'] == 'administrator') {
                $str .= '
                    <a class = "editLink"   href = "system_announcements.php?id='.$params['data'][$i]['id'].'&op=change" target = "POPUP_FRAME" onClick = "eF_js_showDivPopup(\''._CORRECTION.'\', new Array(\'500px\', \'300px\'))"><img src = "images/16x16/edit.png" alt = "'._CORRECTION.'" title = "'._CORRECTION.'" border = "0"/></a>
                    <a class = "deleteLink" href = "system_announcements.php?id='.$params['data'][$i]['id'].'&op=delete" target = "POPUP_FRAME"><img src = "images/16x16/delete.png" alt = "'._DELETE.'" title = "'._DELETE.'" border = "0"/></a>';
            }
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