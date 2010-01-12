<?php
/**
* Smarty plugin: smarty_function_eF_template_printComments function. Prints inner table
*
*/
function smarty_function_eF_template_printComments($params, &$smarty) {

    $max_title_size = 50;                                           //The maximum length of the title, after which it is cropped with ...
    $list_fold_size = 3;                                            //The folding occurs in this number of lines

    if (isset($params['limit'])) {
        $limit = min($params['limit'], sizeof($params['data']));
    } else {
        $limit = sizeof($params['data']);
    }

    $str .= '
        <table border = "0" width = "100%">';
    for ($i = 0; $i < $list_fold_size && $i < $limit; $i++) {
        if (mb_strlen($params['data'][$i]['content_name']) > $max_title_size) {
            $params['data'][$i]['content_name'] = mb_substr($params['data'][$i]['content_name'], 0, $max_title_size).'...';                                 //If the message title is large, cut it and append ...            
        }
        $title2 = '#filter:timestamp_time-'.$params['data'][$i]['timestamp'].'#';
        
        $str .= '
            <tr><td>
                    <span class = "counter">'.($i + 1).'.</span>';

        if ($_SESSION['s_type'] != "administrator") {
			// Students and professors are redirected to the same page - one type just views the other may also edit content
        	$str .= '<a title = "'.$params['data'][$i]['data'].'" href = "'.$_SESSION['s_type'].'.php?ctg=content&view_unit='.$params['data'][$i]['content_ID'];
    
        	// Students and professors may have to change lesson session - using the new_lessons_ID parameter for this purpose
			if (isset($params['data'][$i]['show_lessons_id']) && $params['data'][$i]['show_lessons_id'] != 0 && isset($params['data'][$i]['show_lessons_name'])) {				
				$str .= '&new_lessons_ID='.$params['data'][$i]['show_lessons_id'].'&sbctg=content"><b>'. $params['data'][$i]['show_lessons_name'] . '</b>: ' . $params['data'][$i]['content_name'].'</a></td>';			
			} else {
	        	$str .= '">'.$params['data'][$i]['content_name'].'</a></td>';
	    	}
        } else {
        	// Administrators have no links to projects
        	if (isset($params['data'][$i]['show_lessons_id']) && $params['data'][$i]['show_lessons_id'] != 0 && isset($params['data'][$i]['show_lessons_name'])) {
				$str .= '<a title="'.$title_message.'" href = "administrator.php?ctg=lessons&edit_lesson='.$params['data'][$i]['show_lessons_id'] . '"><b>'. $params['data'][$i]['show_lessons_name']. "</b></a>: " . $params['data'][$i]['content_name'].'</td>';
        	} else {
				$str .= $params['data'][$i]['content_name'].'</td>';        		        	
        	}
        }        
                        
		$str .= '<td align = "right">
                    #filter:user_login-'.$params['data'][$i]['users_LOGIN'].'#, 
                    <span title = "'.$title2.'">'.eF_convertIntervalToTime(time() - $params['data'][$i]['timestamp'], true).' '._AGO.'</span>                    
                </td></tr>';
    } 

    if ($i == 0) {
        $str .= '
            <tr><td class = "emptyCategory">'._NONEWCOMMENTS.'</td></tr>
        </table>';
    }/* elseif ($limit > $list_fold_size) {  //decision for no folding 2007/07/24
        $str .= '
            <tr><td>
                    <img src = "images/others/plus.png" onclick = "show_hide(this, \'recent_comments\');">
                </td></tr>
        </table>
    
            <table border = "0" width = "100%" id = "recent_comments" style = "display:none">';
        for ($i = $list_fold_size; $i < $limit; $i++) {
            if (mb_strlen($params['data'][$i]['content_name']) > $max_title_size) {
                $params['data'][$i]['content_name'] = mb_substr($params['data'][$i]['content_name'], 0, $max_title_size).'...';                                 //If the message title is large, cut it and append ...            
            }
            $str .= '
                <tr><td>
                        <span class = "counter">'.($i + 1).'.</span> <a href = "'.$_SESSION['s_type'].'.php?ctg=content&view_unit='.$params['data'][$i]['content_ID'].'" title = "'.$params['data'][$i]['data'].'">'.$params['data'][$i]['content_name'].'</a></td>
                    <td align = "right">#filter:user_login-'.$params['data'][$i]['users_LOGIN'].'# ';
            $title2 = '#filter:timestamp-'.$params['data'][$i]['timestamp'].'#';
            $str .='<img src="images/16x16/calendar.png" title="'.$title2.'" alt="'.$title2.'" style = "vertical-align:middle"/>';
            $str .= '
                    </td></tr>';
        } 
        $str .= '
            </table>';
    }*/ else {
        $str .= '
            </table>';
    }    

    return $str; 
}

?>