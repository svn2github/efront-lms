<?php
/**
* Smarty plugin: eF_template_printMessage function
*/
function smarty_function_eF_template_printMessage($params, &$smarty) {

    if (isset($params['message'])) {
        if (isset($params['type']) && $params['type'] == 'success') {
            $str = '
                <table class = "messageTable" id = "messageTable">
                    <tr><td class = "message_success">
                            <img src = "images/32x32/check2.png" title = "'._SUCCESS.'" alt = "'._SUCCESS.'" style = "vertical-align:middle"/>
                        </td><td class = "message_success" style = "width:100%;vertical-align:middle">
                            '.$params['message'].'
                    	</td><td class = "message_success">
                    		<img src = "images/16x16/error.png" alt = "'._CLOSE.'" title = "'._CLOSE.'" onclick = "if (window.Effect) new Effect.Fade(Element.extend(this).up().up().up().up().up().up()); else document.getElementById(\'messageTable\').parentNode.parentNode.style.display=\'none\';">
                    	</td></tr>
                </table>';            
        } else if (isset($params['type']) && $params['type'] == 'system_announcement'){
            $str = '
                <table class = "messageTable" id = "messageTable">
                    <tr><td class = "message">
                            <img src = "images/32x32/wrench.png"  title = "'._SYSTEM.'" alt = "'._SYSTEM.'" style = "vertical-align:middle"/>
                        </td><td class = "message_announcement" style = "width:100%;vertical-align:middle;padding-left:10px;">
                            '.$params['message'].'
                    	</td><td class = "message_announcement">
                    		<img src = "images/16x16/error.png" alt = "'._CLOSE.'" title = "'._CLOSE.'" onclick = "if (window.Effect) new Effect.Fade(Element.extend(this).up().up().up().up().up().up()); else document.getElementById(\'messageTable\').parentNode.parentNode.style.display=\'none\';">
                    	</td></tr>
                </table>';
        } else {
            $str = '
                <table class = "messageTable" id = "messageTable">
                    <tr><td class = "message">
                            <img src = "images/32x32/warning.png"  title = "'._WARNING.'" alt = "'._WARNING.'" style = "vertical-align:middle"/>
                        </td><td class = "message" style = "width:100%;vertical-align:middle">
                            '.$params['message'].'
                    	</td><td class = "message">
                    		<img src = "images/16x16/error.png" alt = "'._CLOSE.'" title = "'._CLOSE.'" onclick = "if (window.Effect) new Effect.Fade(Element.extend(this).up().up().up().up().up().up()); else document.getElementById(\'messageTable\').parentNode.parentNode.style.display=\'none\';">
                    	</td></tr>
                </table>';
        }      
     } else {
        $str = '';
     }
    
    return $str;
}

?>