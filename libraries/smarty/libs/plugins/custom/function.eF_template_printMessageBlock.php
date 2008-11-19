<?php
/**
* prints a block
*
*/
function smarty_function_eF_template_printMessageBlock($params, &$smarty) {
    !isset($params['type']) ? $params['type'] = 'failure' : null;
    if ($params['type'] == 'success') {
        $messageImage = '<img src = "images/32x32/check2.png" alt = "'._SUCCESS.'" title = "'._SUCCESS.'">';
    } else {
        $messageImage = '<img src = "images/32x32/warning.png" alt = "'._FAILURE.'" title = "'._FAILURE.'">';        
    }
         
    $str .= '
        <div class = "block" id = "messageBlock">
        <div class = "top-left"></div>
        <div class = "top-right"></div>
        <div class = "blockContents">       
        	<table class = "messageBlock">
            	<tr><td>'.$messageImage.'</td>
            		<td class = "'.$params['type'].'Block">'.$params['content'].'</td>
            		<td><img src = "images/16x16/error.png" alt = "'._CLOSE.'" title = "'._CLOSE.'" onclick = "window.Effect ? new Effect.Fade($(\'messageBlock\')) : document.getElementById(\'messageBlock\').style.display = \'none\';"></td></tr>
            </table>	
        </div>
        <div class = "bottom-left"></div>
        <div class = "bottom-right"></div>
        </div>';
    
    return $str;
}
?>