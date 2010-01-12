<?php
/**
* Smarty plugin: smarty_function_eF_template_printNewContent function. Prints inner table
*
*/
function smarty_function_eF_template_printNewContent($params, &$smarty) {

    $max_title_size = 50;                                           //The maximum length of the title, after which it is cropped with ...
    $list_fold_size = 5;                                            //The folding occurs in this number of lines

    $limit = sizeof($params['data']);

    $str .= '
        <table border = "0" width = "100%">';
    for ($i = 0; $i < $list_fold_size && $i < $limit; $i++) {
        if (mb_strlen($params['data'][$i]['content_name']) > $max_title_size) {
            $params['data'][$i]['name'] = mb_substr($params['data'][$i]['name'], 0, $max_title_size).'...';                                 //If the message title is large, cut it and append ...            
        }
        
        
        if($params['data'][$i]['ctg_type'] == "tests"){
        	if(mb_strlen($params['data'][$i]['name']) > $max_title_size){
				$str .= '<tr><td>
                        '.($i + 1).'. <a href = "'.$_SESSION['s_type'].'.php?ctg=tests&view_unit='.$params['data'][$i]['id'].'" title = "'.$params['data'][$i]['name'].'">'.mb_substr($params['data'][$i]['name'], 0, $max_title_size).'...</a></td>';	
			}else{
				$str .= '
                	<tr><td>
                        '.($i + 1).'. <a href = "'.$_SESSION['s_type'].'.php?ctg=tests&view_unit='.$params['data'][$i]['id'].'" title = "'.$params['data'][$i]['name'].'">'.$params['data'][$i]['name'].'</a></td>';	
			}
            
                        
            $str .= '<td align = "right">'; 
        }else{
        	if(mb_strlen($params['data'][$i]['name']) > $max_title_size){
				$str .= '<tr><td>
                        '.($i + 1).'. <a href = "'.$_SESSION['s_type'].'.php?ctg=content&view_unit='.$params['data'][$i]['id'].'" title = "'.$params['data'][$i]['name'].'">'.mb_substr($params['data'][$i]['name'], 0, $max_title_size).'...</a></td>';	
			}else{
				$str .= '<tr><td>
                        '.($i + 1).'. <a href = "'.$_SESSION['s_type'].'.php?ctg=content&view_unit='.$params['data'][$i]['id'].'" title = "'.$params['data'][$i]['name'].'">'.$params['data'][$i]['name'].'</a></td>';	
			}	
			$str .= '<td align = "right">';  
        }
                
                
        $title2 =  '#filter:timestamp-'.$params['data'][$i]['timestamp'].'#';               
        $str .= '<img src="images/16x16/calendar.png" title="'.$title2.'" alt="'.$title2.'" style = "vertical-align:middle"/></td></tr>';
    } 

    if ($i == 0) {
        $str .= '
            <tr><td class = "emptyCategory">'._NONEWCONTENT.'</td></tr>
        </table>';
    } elseif ($limit > $list_fold_size) {
        $str .= '
            <tr><td>
                    <img src = "images/others/plus.png" onclick = "show_hide(this, \'new_content\');">
                </td></tr>
        </table>
    
            <table border = "0" width = "100%" id = "new_content" style = "display:none">';
        for ($i = $list_fold_size; $i < $limit; $i++) {
        if (mb_strlen($params['data'][$i]['content_name']) > $max_title_size) {
            $params['data'][$i]['name'] = mb_substr($params['data'][$i]['name'], 0, $max_title_size).'...';                                 //If the message title is large, cut it and append ...            
        }
        
        if($params['data'][$i]['ctg_type'] == "tests"){
            $str .= '
                <tr><td>
                        '.($i + 1).'. <a href = "'.$_SESSION['s_type'].'.php?ctg=tests&view_unit='.$params['data'][$i]['id'].'" title = "'.$params['data'][$i]['name'].'">'.$params['data'][$i]['name'].'</a></td>
                    <td align = "right">';
        }else{
            $str .= '
                <tr><td>
                        '.($i + 1).'. <a href = "'.$_SESSION['s_type'].'.php?ctg=content&view_unit='.$params['data'][$i]['id'].'" title = "'.$params['data'][$i]['name'].'">'.$params['data'][$i]['name'].'</a></td>
                    <td align = "right">';  
        }
        
        
        $title2 =  '#filter:timestamp-'.$params['data'][$i]['timestamp'].'#';
        $str .= '<img src="images/16x16/calendar.png" title="'.$title2.'" alt="'.$title2.'" style = "vertical-align:middle"/></td></tr>';
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