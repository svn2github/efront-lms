<?php
/**
* Smarty plugin: eF_template_printSide function
*/
function smarty_function_eF_template_printSide($params, &$smarty) {
    $str = '
                        <table class = "sideTable">
                            <tr><td>
                                <table width = "100%">
                                    <tr height = "20" bgcolor = "#3399ff">
                                        <td class = "topTitle">
                                            '.$params['title'].' '.(isset($params['array']) ? '('.sizeof($params['array']).')' : '').'
                                        </td>
                                        <td class = "topTitle rightAlign">
                                                <img id = "'.$params['id'].'_img" src="images/others/blank.gif" class="minus"
                                style="position:relative;top:3px;"
                                                onClick = "toggleVisibility(document.getElementById(\''.$params['id'].'_id\'),this)"/>
                                        </td>
                                    </tr>
                                    <tr id="'.$params['id'].'_id" bgcolor = "white"><td colspan="2">
                                        <table width = "100%">
                                            <tr><td>
                                                '.$params['data'].'
                                            </td></tr>
                                            '.(isset($params['navigation']) ? '<tr><td><br/>'.$params['navigation'].'</td></tr>' : '').'
                                       </table>
                                    </td>
                                    <!--<td>&nbsp;</td>-->
                                    </tr>
                                </table>
                            </td></tr>
                        </table>
                        <br/>';
    
    return $str;
}

?>