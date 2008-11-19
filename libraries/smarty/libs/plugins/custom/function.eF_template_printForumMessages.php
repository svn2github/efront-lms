<?php
/**
* Smarty plugin: smarty_function_eF_template_printForumMessages function.
*
* This function prints a list with forum messages titles and the corresponding list
* It is used to both student and professor pages, at the front page, and wherever we need
* a list of forum messages.
* 
*/
function smarty_function_eF_template_printForumMessages($params, &$smarty) {

    $max_title_size = 50;                                           //The maximum length of the title, after which it is cropped with ...
    
    if (isset($params['limit'])) {                                  //If limit is specified, then only up to limit messages are displayed
        $limit = min($params['limit'], sizeof($params['data']));
    } else {
        $limit = sizeof($params['data']);
    }

    $str = '        
        <table border = "0" width = "100%">';
    for ($i = 0; $i < $limit; $i++) {
        $params['data'][$i]['title'] ? $title_message = $params['data'][$i]['title'] : $title_message = '<span class = "emptyCategory">'._NOTITLE.'</span>';
        if (mb_strlen($params['data'][$i]['title']) > $max_title_size) {
            $params['data'][$i]['title'] = mb_substr($params['data'][$i]['title'], 0, $max_title_size).'...';                                 //If the message title is large, cut it and append ... 
        }
        $str .= '
            <tr><td>
                    <span class = "counter">'.($i + 1).'.</span> 
                    <a title="'.$params['data'][$i]['title'].'" href = "forum/forum_index.php?topic='.$params['data'][$i]['topic_id'].'&view_message='.$params['data'][$i]['id'].'">
                        '.$title_message.'
                    </a>
                </td><td align = "right">#filter:user_login-'.$params['data'][$i]['users_LOGIN'].'#, ';
        $title2 = '#filter:timestamp_time-'.$params['data'][$i]['timestamp'].'#';
        //$str .= '<img src="images/16x16/calendar.png" title="'.$title2.'" alt="'.$title2.'" style = "vertical-align:middle"/>';
        $str .= '<span title = "'.$title2.'">'.eF_convertIntervalToTime(time() - $params['data'][$i]['timestamp'], true).' '._AGO."</span>";
        $str .= '
                </td></tr>';
    } 

    if ($i == 0) {
        $str .= '
            <tr><td class = "empty_category">'._NONEWFORUMMESSAGES.'</td></tr>';
    }
    
    $str .= '</table>';
    
    return $str; 
}

?>