<?php
/**

* Smarty plugin: eF_template_printMessage function

*/
function smarty_function_eF_template_printMessage($params, &$smarty) {
    if (isset($params['message'])) {
        if (mb_strlen($params['message']) > 1000) {
            $prefix = mb_substr($params['message'], 0, 1000);
            $suffix = mb_substr($params['message'], mb_strlen($params['message']) - 300, mb_strlen($params['message']));
            $infix = mb_substr($params['message'], 1001, mb_strlen($params['message']) - mb_strlen($prefix) - mb_strlen($suffix));
            $params['message'] = $prefix.'<a href = "javascript:void(0)" onclick = "this.style.display = \'none\';Element.extend(this).next().show()"><br>[...]<br></a><span style = "display:none">'.$infix.'</span>'.$suffix;
        }
        if (isset($params['type']) && $params['type'] == 'success') {
            $image = '<img src = "images/32x32/success.png" title = "'._SUCCESS.'" alt = "'._SUCCESS.'"/>';
            $class = "message_success";
        } else if (isset($params['type']) && $params['type'] == 'system_announcement'){
            $image = '<img src = "images/32x32/tools.png"  title = "'._SYSTEM.'" alt = "'._SYSTEM.'" />';
            $class = "message_announcement";
        } else {
            $image = '<img src = "images/32x32/warning.png"  title = "'._WARNING.'" alt = "'._WARNING.'" />';
            $class = "message_failure";
        }
        $str = '
                <table class = "messageTable" id = "messageTable">
                    <tr><td class = "'.$class.'">
                            '.$image.'
                        </td><td class = "'.$class.' message_text">
                            '.$params['message'].'
                     </td><td class = "'.$class.'">
                      <img src = "images/16x16/close.png" alt = "'._CLOSE.'" title = "'._CLOSE.'" onclick = "if (window.Effect) new Effect.Fade(Element.extend(this).up().up().up().up().up().up()); else document.getElementById(\'messageTable\').parentNode.parentNode.style.display=\'none\';">
                     </td></tr>
                </table>';

    } else {
        $str = '';
     }

    return $str;
}

?>
