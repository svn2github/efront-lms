<?php
/**
* Smarty plugin: smarty_function_eF_template_printIconTable function. Prints a table with icons and descriptions
*
* $params is an array with fields: title, columns, links, image
* $params['links'] is an array with fields: text, image
*/
function smarty_function_eF_template_printIconTable($params, &$smarty) {

    $innerTableIdentifier = $GLOBALS['innerTableIdentifier'];
    $cookieString = md5($_SESSION['s_login'].$_SESSION['s_lessons_ID'].$GLOBALS['innerTableIdentifier'].urlencode($params['title']));
    $cookieValue  = $_COOKIE['innerTables'][$cookieString];
    
    $str = '
        <table class = "iconTable" style = "margin:0px 0px 5px 0px;border:1px solid #CCCCCC">
            <tr class = "handle" style = "background:#FAFAFA url(\'images/others/grey1.png\') repeat-x top;"> 
                <th class = "iconTableHeader" style = "border-bottom:1px solid #999999;">
            		<img class = "iconTableImage" src = "images/'.$params['image'].'" title = "'.$params['title'].'" alt = "'.$params['title'].'"/>&nbsp;'.$params['title'].'
            	</th>
            	<td class = "innerTableTd" style = "text-align:right;border-bottom:1px solid #999999;">
            		<img src = "images/others/blank.gif" id = "'.urlencode($params['title']).'_imageId"  onclick = "visibilityStatus = toggleVisibility($(\''.urlencode($params['title']).'_tableId\'), this);createCookie(\'innerTables['.$cookieString.']\', visibilityStatus)"/>
            	</td></tr>';
    
    $optionsStr = '';
    if (isset($params['options'])) {
        foreach ($params['options'] as $key => $value) {
            $key > 0 ? $style = 'border-left:1px solid #999999;' : $style = '';
            $value['selected'] ? $style .= 'background-color:#D3D3D3;font-weight:bold;' : null;
            $optionsStr.= '
                    	<td style = "padding:5px;white-space:nowrap;'.$style.'">&nbsp;
                        	<a href = "'.$value['link'].'"><img src = "images/'.($value['image'] ? $value['image'] : '16x16/arrow_right_blue.png').'" style = "vertical-align:middle" border = "0"/></a>
                        	<a href = "'.$value['link'].'" style = "vertical-align:middle" class = "'.$value['class'].'">'.$value['title'].'</a>&nbsp;
                        </td>';
        }
	    $str .= '
	    		<tr><td colspan = "2" style = "border-bottom:1px solid #999;">
	    				<table>
	    					<tr>'.$optionsStr.'</tr>
	    				</table>
	    			</td></tr>';
    }
    $str .= '
        	<tr><td  style = "padding:0px" colspan = "2" id = "'.urlencode($params['title']).'_tableId" '.($cookieValue == 'hidden' ? 'style = "display:none"' : '').'>        		
        		<table style = "width:100%"><tr>';//id = "'.urlencode($params['title']).'_tableId" '.($cookieValue == 'hidden' ? 'style = "display:none"' : '').'
    $counter = 1;
    foreach($params['links'] as $key => $value) {
        $str .= '
	        	<td class = "'.(isset($value['class']) && $value['class']=='inactiveLink' ? 'emptyIconTableTD' : 'iconTableTD').'" style = "width:'.(100 / $params['columns']).'%" >
			        <a href = "'.$value['href'].'" class = "'.$value['class'].'" title = "'.$value['title'].'" onclick = "'.$value['onClick'].'" '.(isset($value['target']) ? 'target = "'.$value['target'].'"' : '').' style = "'.$value['style'].'">
				        <img border = "0" src = "images/'.$value['image'].'" alt = "'.$value['title'].'" title = "'.$value['title'].'"/><br clear = "all"/>'.$value['text'].'
				    </a></td>';
        if ($counter++ % $params['columns'] == 0) {
            $str .= '
	        </tr><tr>';
        }
    }
    $str .= '</tr></table></td></tr>';
    
    for ($i = $counter; $counter < $i && $i % $params['columns'] != 1; $i++) {
        $str .= '
    	    	<td class = "emptyIconTableTD">&nbsp;</td>';
    }
    $str .= '
        	</tr>
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