<?php
/**
* Smarty plugin: smarty_function_eF_template_printNews function. Prints inner table
*
*/
function smarty_function_eF_template_printNews($params, &$smarty) {
    
    $max_title_size = 50;                                           //The maximum length of the title, after which it is cropped with ...
    $list_fold_size = 3;                                            //The folding occurs in this number of lines
    if (isset($params['fold'])) {                                    //Check if there is a custom folding threshold set
        $params['fold'] ? $list_fold_size = $params['fold'] : $list_fold_size = 1000000;
    }

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
            <tr style = "'.($params['data'][$i]['lessons_ID'] == 0 && $_SESSION['s_type'] != 'administrator' ? 'font-weight:bold' : '').'">
            	<td><span class = "counter">'.($i + 1).'.</span> <a title="'.$title_message.'" href = "news.php?id='.$params['data'][$i]['id'].'" target = "POPUP_FRAME" style = "'.$new_str.'" onClick = "eF_js_showDivPopup(\''._ANNOUNCEMENT.'\', 1);">'.$params['data'][$i]['title'].'</a></td>
        		<td align = "right">
        			#filter:user_login-'.$params['data'][$i]['users_LOGIN'].'#, 
        			<span title = " #filter:timestamp_time-'.$params['data'][$i]['timestamp'].'#">'.eF_convertIntervalToTime(time() - $params['data'][$i]['timestamp'], true).' '._AGO."</span>";       

        if ($_SESSION['s_login'] == $params['data'][$i]['users_LOGIN']) {            
                $str .= '
                    <a class = "editLink"   href = "news.php?id='.$params['data'][$i]['id'].'&op=change" target = "POPUP_FRAME" onClick = "eF_js_showDivPopup(\''._EDITANNOUNCEMENT.'\', 1);"><img src = "images/16x16/edit.png" alt = "'._EDITANNOUNCEMENT.'" title = "'._EDITANNOUNCEMENT.'" border = "0" style = "vertical-align:middle"/></a>
                    <a class = "deleteLink" href = "news.php?id='.$params['data'][$i]['id'].'&op=delete" target = "POPUP_FRAME" onclick = "return confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')"><img src = "images/16x16/delete.png" alt = "'._DELETE.'" title = "'._DELETE.'" border = "0" style = "vertical-align:middle"/></a>';
        }
        $str .= '
                </td></tr>';
    } 
    
    if ($i == 0) {
    	if($_SESSION['s_type'] != 'administrator') {
			$str .= '
            <tr><td class = "empty_category">'._NOANNOUNCEMENTSPOSTED.'</td></tr>
        	</table>';
		}else{
			$str .= '
            	<tr><td class = "empty_category">'._NOANNOUNCEMENTSPOSTEDADMIN.'</td></tr>
			</table>';	
		}
        
    
	} else {
        $str .= '
        </table>';        
    }

    return $str; 
}

?>