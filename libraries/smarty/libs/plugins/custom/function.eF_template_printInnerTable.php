<?php
/**
* Smarty plugin: smarty_function_eF_template_printInnerTable function. Prints inner table
*
* $params is an array with fields: title, data, image, navigation (optional), is_last (optional), absoluteImagePath(optional)
* $params['data'] is plain html
*/
function smarty_function_eF_template_printInnerTable($params, &$smarty) {
    if (!$params['data'] && isset($params['alt'])) {
        $params['data'] = $params['alt'];
    }
    
    $innerTableIdentifier = $GLOBALS['innerTableIdentifier'];
    $cookieString = md5($_SESSION['s_login'].$_SESSION['s_lessons_ID'].$GLOBALS['innerTableIdentifier'].urlencode($params['title']));
    $cookieValue  = $_COOKIE['innerTables'][$cookieString];
    
    $str = '
        <table class = "innerTable" style = "margin:0px 0px 5px 0px;border:1px solid #CCCCCC">
            <tr class = "handle" style = "background:#FAFAFA url(\'images/others/grey1.png\') repeat-x top;border-vottom:1px solid #999999">
                <th class = "innerTableHeader" style = "border-bottom:1px solid #999999;">
                    <img class = "iconTableImage" src = "'.(!isset($params['absoluteImagePath']) ? $str .= 'images/' : '').$params['image'].'" title="'.$params['title'].'" alt="'.$params['title'].'"/>&nbsp;';
            if (isset($params['link']) && sizeof($params['link']) > 0){
                if ($params['titleStyle']) {
                    $str.= '<a href="'.$params['link'].'"><span style="'.$params['titleStyle'].'" >'.$params['title'].'</span></a></th>';
                } else {
                    $str.= '<a href="'.$params['link'].'">'.$params['title'].'</a></th>';
                }
            } else {
                if ($params['titleStyle']) {
                    $str .= '<span style="'.$params['titleStyle'].'" >'.$params['title'].'</span></a></th>';
                } else {
                    $str.= $params['title'].'</a></th>';
                }
            }
            $str .= '<td class = "innerTableTd" style = "text-align:right;border-bottom:1px solid #999999;">';
            if (isset($params['options'])) {
                foreach ($params['options'] as $key => $value) {
                    if (isset($value['class']) && $value['class'] != '') {
                        $classstr = '"optionsIcons '.$value['class'].'"';
                    } else {
                        $classstr = "optionsIcons";
                    }
                    isset($value['target']) ? $target = 'target = "'.$value['target'].'"' : $target = '';
                    $str .= '<a href="'.$value['href'].'" ';
                    if ($value['id']) {
                        $str .= ' id = "'.$value['id'].'" ';
                    }
                    $str .= 'onclick="'.$value['onClick'].'" '.$target.'><img border = "0" class="'.$classstr.'" src="images/'.$value['image'].'" title="'.$value['text'].'" alt="'.$value['text'].'"></a>';
                }
            }
            

            $str .= '
            	<img src = "images/others/blank.gif" id = "'.urlencode($params['title']).'_imageId"  onclick = "visibilityStatus = toggleVisibility($(\''.urlencode($params['title']).'_tableId\'), this);createCookie(\'innerTables['.$cookieString.']\', visibilityStatus)"/>
            </td></tr>';

            $optionsStr = '';
            if (isset($params['main_options'])) {
                foreach ($params['main_options'] as $key => $value) {
                    $key > 0 ? $style = 'border-left:1px solid #999999;' : $style = '';
                    $value['selected'] ? $style .= 'background-color:#D3D3D3;font-weight:bold;' : null;
                    $optionsStr.= '
                        <td style = "padding:5px;white-space:nowrap;'.$style.'">&nbsp;
                            <a href = "'.$value['link'].'"><img src = "images/'.($value['image'] ? $value['image'] : '16x16/arrow_right_blue.png').'" style = "vertical-align:middle" border = "0"/></a>
                            <a href = "'.$value['link'].'" style = "vertical-align:middle" class = "'.$value['class'].'">'.$value['title'].'</a>&nbsp;
                        </td>';
                }
            }

            if($params['data'] != ""){
                $str .= '
            <tr><td colspan = "3" style = "padding-left:5px;" id = "'.urlencode($params['title']).'_tableId" '.($cookieValue == 'hidden' ? 'style = "display:none"' : '').'>
            	
            	'.(isset($params['main_options']) ? '<div style = "border-bottom:1px solid #999;"><table><tr>'.$optionsStr.'</tr></table></div>' : '').$params['data'].'</td></tr>';
            } else {
                $str .= '
            <tr><td colspan = "3" class = "emptyCategory">'._NOCONTENTFOUND.'</td></tr>';
            }
     $str .= (isset($params['navigation']) ? '
            <tr><td colspan = "3"">'.$params['navigation'].'</td></tr>' : '').''.(!isset($params['is_last']) ? '
            '                    : '').'
        </table>
        <script>
        ';
     if (!($cookieValue)) {
        $str .= 'Element.extend($(\''.urlencode($params['title']).'_imageId\')).addClassName(\'minus\'); Element.extend($(\''.urlencode($params['title']).'_imageId\')).ancestors().each(function (s) {if (s.readAttribute(\'collapsed\')) {visibilityStatus = toggleVisibility($(\''.urlencode($params['title']).'_tableId\'), $(\''.urlencode($params['title']).'_imageId\'));}})';
     } else {
         $cookieValue == 'hidden' ? $str .= 'Element.extend($(\''.urlencode($params['title']).'_imageId\')).addClassName(\'plus\')' : $str .= 'Element.extend($(\''.urlencode($params['title']).'_imageId\')).addClassName(\'minus\')';
     }
     $str .= '</script>';

    return $str;
}

?>