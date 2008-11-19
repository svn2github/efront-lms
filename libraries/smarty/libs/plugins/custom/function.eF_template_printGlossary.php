<?php
/**
* Smarty plugin: smarty_function_eF_template_printGlossary function. Prints inner table
*
*/
function smarty_function_eF_template_printGlossary($params, &$smarty) {

    $max_title_size = 20;                       //The maximum length of the title, after which it is cropped with ...

    $str = '';

    if ($params['user_type'] == 'professor' && (!isset($GLOBALS['currentUser'] -> coreAccess['glossary']) || $GLOBALS['currentUser'] -> coreAccess['glossary'] == 'change')) {
        $str .= '
        	<table>
                <tr><td><a href="add_definition.php?add=1" onclick = "eF_js_showDivPopup(\''._ADDDEFINITION.'\', 1)" target = "POPUP_FRAME"><img src="images/16x16/add2.png" title="'._ADDDEFINITION.'" alt="'._ADDDEFINITION.'" border="0" ></a></td>
                    <td><a href="add_definition.php?add=1" onclick = "eF_js_showDivPopup(\''._ADDDEFINITION.'\', 1)" target = "POPUP_FRAME">'._ADDDEFINITION.'</a></td></tr>
            </table>';
    }

    if (isset($_GET['tabberajax'])) {
        $tabberToShow = $_GET['tabberajax'];
    } else {
        $tabberToShow = 0;
    }

  if (sizeof($params['data']) > 0) {         //If there are entries, show the tabber
  
    $str .= '
        <div class="tabber">';

    $counter = 0;
    foreach ($params['data'] as $key => $value) {
        $str .= '
        <div class="tabbertab '.(isset($_GET['tab']) && mb_strtolower($_GET['tab']) == mb_strtolower($key) ? 'tabbertabdefault' : '').' useAjax" id="tabbertab'.$counter.'" title="'.$key.'">
<!--tabberajax:tabbertab'.$counter.'-->';
        if ($counter == $tabberToShow) {
            $str .= '<table style = "width:100%" id="tabbertab_table'.$counter.'">';

            if ($key == '0-9' || $key == 'Symbols') {

                foreach ($value as $inner_key => $inner_value) {
                    $str .= '
                        <tr class = "defaultRowHeight"><td colspan = "100%" class = "boldFont">'.htmlentities($inner_key).' :</td></tr>
                        <tr class = "defaultRowHeight"><td class = "topTitle">'._TERM.'</td><td class = "topTitle">'._EXPLANATION.'</td>';
                    if ($params['user_type'] == 'professor' && (!isset($GLOBALS['currentUser'] -> coreAccess['glossary']) || $GLOBALS['currentUser'] -> coreAccess['glossary'] == 'change')) {
                        $str .= '<td align="center" class = "topTitle" width="10%">'._FUNCTIONS.'</td>';
                    }
                    $str .= '</tr>';

                    $count = 0;
                    foreach($inner_value as $inner_inner_key => $inner_inner_value) {
                        fmod($count++, 2) ? $class_name = 'evenRowColor' : $class_name = 'oddRowColor';
                        $str .= '
                        <tr class = "'.$class_name.' defaultRowHeight">
                            <td class = "boldFont">'.$inner_inner_value['name'].'</td>
                            <td>'.$inner_inner_value['info'].'</td>';
                        if ($params['user_type'] == 'professor' && (!isset($GLOBALS['currentUser'] -> coreAccess['glossary']) || $GLOBALS['currentUser'] -> coreAccess['glossary'] == 'change')) {
                            $str .= '
                            <td align="center" class = "nowrap">
                              	<a class = "editLink"   href = "add_definition.php?update='.$inner_inner_value['id'].'" onclick = "eF_js_showDivPopup(\''._EDITDEFINITION.'\', 1)" target = "POPUP_FRAME"><img src = "images/16x16/edit.png" alt = "'._EDITDEFINITION.'" title = "'._EDITDEFINITION.'" border = "0"/></a>
                              	<a class = "deleteLink" href = "add_definition.php?delete='.$inner_inner_value['id'].'&tab='.$key.'" target = "POPUP_FRAME" onclick = "return confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')"><img src = "images/16x16/delete.png" alt = "'._DELETE.'" title = "'._DELETE.'" border = "0"/></a>
                            </td>';
                        }
                        $str .= '</tr>';
                    }
                    $str .= '<tr class = "horizontalSeparator defaultRowHeight"><td>&nbsp;</td></tr>';
                }
            } else {
                $str .= '
                    <tr class = "defaultRowHeight">
                        <td class = "topTitle">'._TERM.'</td><td class = "topTitle">'._EXPLANATION.'</td>';
                    if ($params['user_type'] == 'professor' && (!isset($GLOBALS['currentUser'] -> coreAccess['glossary']) || $GLOBALS['currentUser'] -> coreAccess['glossary'] == 'change')) {
                        $str .= '
                        <td align="center" class = "topTitle" width="10%">'._FUNCTIONS.'</td>';
                    }
                    $str .= '</tr>';

                $count = 0;
                foreach ($value as $inner_key => $inner_value) {
                    fmod($count++, 2) ? $class_name = 'evenRowColor' : $class_name = 'oddRowColor';

                    $str .= '
                    <tr class = "'.$class_name.' defaultRowHeight">
                        <td class = "boldFont">'.$inner_value['name'].'</td>
                        <td>'.$inner_value['info'].'</td>';
                    if ($params['user_type'] == 'professor' && (!isset($GLOBALS['currentUser'] -> coreAccess['glossary']) || $GLOBALS['currentUser'] -> coreAccess['glossary'] == 'change')) {
                        $str .= '
                        <td class = "nowrap" align="center">
                        	<a class = "editLink"   href = "add_definition.php?update='.$inner_value['id'].'" onclick = "eF_js_showDivPopup(\''._EDITDEFINITION.'\', 1)" target = "POPUP_FRAME"><img src = "images/16x16/edit.png" alt = "'._EDITDEFINITION.'" title = "'._EDITDEFINITION.'" border = "0"/></a>
                        	<a class = "deleteLink" href = "add_definition.php?delete='.$inner_value['id'].'&tab='.$key.'" target = "POPUP_FRAME" onclick = "return confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\')"><img src = "images/16x16/delete.png" alt = "'._DELETE.'" title = "'._DELETE.'" border = "0"/></a>
                        </td>';
                    }
                    $str .= '
                        </tr>';
                }
            }

            $str .= '</table>';
        }
        $str .= '
<!--/tabberajax:tabbertab'.$counter++.'-->
        </div>';
    }
    $str .= '</div>';

  } else {                          //if there are no entries, display message
    $str .= '<br/><span class="empty_category">'._NODEFINITIONSFOUNDFORTHISLESSON.'</span>';
  }

    return $str;
}

?>