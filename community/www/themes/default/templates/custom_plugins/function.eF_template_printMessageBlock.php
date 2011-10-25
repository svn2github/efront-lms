<?php
/**

* prints a block

*

*/
function smarty_function_eF_template_printMessageBlock($params, &$smarty) {
    !isset($params['type']) || !$params['type'] ? $params['type'] = 'failure' : null;
    in_array($params['type'], array('success', 'failure')) OR $params['type'] = 'failure';
    if ($params['type'] == 'success') {
        $messageImage = '<img src = "images/32x32/success.png" alt = "'._SUCCESS.'" title = "'._SUCCESS.'">';
    } else {
        $messageImage = '<img src = "images/32x32/warning.png" alt = "'._FAILURE.'" title = "'._FAILURE.'">';
    }
    if (mb_strlen($params['content']) > 1000) {
     $prefix = mb_substr($params['content'], 0, 1000);
     $suffix = mb_substr($params['content'], mb_strlen($params['content']) - 300, mb_strlen($params['content']));
     $infix = mb_substr($params['content'], 1001, mb_strlen($params['content']) - mb_strlen($prefix) - mb_strlen($suffix));
     $params['content'] = $prefix.'<a href = "javascript:void(0)" onclick = "this.style.display = \'none\';Element.extend(this).next().show()"><br>[...]<br></a><span style = "display:none">'.$infix.'</span>'.$suffix;
    }
    $str .= '
        <div class = "block" id = "messageBlock">
        <div class = "blockContents messageContents">
         <table class = "messageBlock">
             <tr><td>'.$messageImage.'</td>
              <td class = "'.strip_tags($params['type']).'Block">'.strip_tags($params['content']).'</td>
              <td><img src = "images/32x32/close.png" alt = "'._CLOSE.'" title = "'._CLOSE.'" onclick = "window.Effect ? new Effect.Fade($(\'messageBlock\')) : document.getElementById(\'messageBlock\').style.display = \'none\';"></td></tr>
            </table>
        </div>
        </div>';

    return $str;
}
?>
