<?php
/**
* Smarty plugin: smarty_function_eF_template_printSurveysList function. Prints surveys
*
*/

function smarty_function_eF_template_printSurveysList($params ,  &$smarty){
    
    $str = '';
    
    $str .= '<table style = "width:100%;text-align:left">';
        if( strcmp($params['user_type'],"professor") == 0 ){
            $str .='<tr>
                    <td>
                        <table width="100%" border="0px">
                        <tr>
                            <td class="headerImage" width="2%" align="right">
                                <a href="professor.php?ctg=survey&action=create_survey&survey_action=create&screen=1&lessons_ID='.$_SESSION['s_lessons_ID'].'"><img src="images/16x16/add.png" title='._CREATESURVEY.' border="0px" /></a>
                            </td>
                            <td class="headerTitle"><a href="professor.php?ctg=survey&action=create_survey&survey_action=create&screen=1&lessons_ID='.$_SESSION['s_lessons_ID'].'">'._CREATESURVEY.'</a></td>
                        </tr>
                        </table>
                    </td>
                </tr>';
        }
        $str .='
            <tr><td colspan="100%" align="left" width="100%">
            <form name="select0" action="javascript:void(0);" onsubmit="return false"> 
            <table width="100%" align="left" border="0px" class="sortedTable">';
            
            $str .='<tr class="defaultRowHeight">
                <td class="topTitle" align="left">'._SURVEYCODE.'</td>
                <td class="topTitle" align="left">'._SURVEYNAME.'</td>
                <td class="topTitle" align="center"> '._SURVEYNUMBEROFQUESTIONS.'</td>
                <td class="topTitle" align="left">'._SURVEYAVALIABLEFROM.'</td>
                <td class="topTitle" align="left" colspan="1">'._SURVEYUNTIL.'</td>
                <td class="topTitle" align="center">'._SURVEYSTATUS.'</td>';
                if( strcmp($params['user_type'],"professor") == 0 ){
                    $str .= '<td class="topTitle" align="center">'._PARTICIPATION.'</td>
                         <td class="topTitle" align="center">'._OPERATIONS.'</td>
                         <td class="topTitle" align="left">'._PUBLISH.'</td>';
                }
            $str .= '</tr>';
            for($i = 0 ; $i < sizeof($params['data']) ; $i ++){
                    if($params['data'] == '0'){
                        $str.='<tr><td class="emptyCategory" colspan="100%">'._NODATAFOUND.'</td></tr>';
                        break;
                    }else{
                        if(fmod($i,2)){
                            $now = time();
                            if( $now >= $params['data'][$i]['end_date'] ){
                                $str .= '<tr class="emptyCategory">';
                            }else{
                                $str .= '<tr class="oddRowColor">';
                            }
                        }else{
                            $now = time();
                            if( $now >= $params['data'][$i]['end_date'] ){
                                $str .= '<tr class="emptyCategory">';
                            }else{
                                $str .= '<tr class="evenRowColor">';
                            } 
                        }
                        $users = array('total_users' => eF_getTableData("users_to_surveys","count(*)","surveys_ID=".intVal($params['data'][$i]['id'])),
                                   'done_users' => eF_getTableData("users_to_done_surveys","count(*)"," done=1 AND surveys_ID=".intVal($params['data'][$i]['id'])));
                        foreach($params['data'][$i] as $key => $value){
                            if($key == 'survey_code'){
                                $str .= '<td align="left">'.$value.'</td>';
                            }
                            if($key == 'survey_name'){
                                $str.='<td align="left"><a href="'.$params['user_type'].'.php?ctg=survey&surveys_ID='.$params['data'][$i]['id'].'&screen_survey=2">'.$value.'</a></span></td><td align="center">'.$params['questions'][$i][0]['count(*)'].'</td>
                                ';
                            }else if($key == 'share'){
                                if($value == 'yes'){ $str .= '<td align="left">'._YES.'</td>'; }
                                else { $str .= '<td align="left">'._NO.'</td>'; }
                            }else if($key == 'status'){
                                $now = time();
								if (time() < $params['data'][$i]['end_date']) {
									if($value == 1){
										$str .= '<td align="center"><a href="professor.php?ctg=survey&action=change_status&survey_action=deactivate_survey&surveys_ID='.$params['data'][$i]['id'].'"><img src="images/16x16/trafficlight_green.png" border="0px" title="'._DEACTIVATE.'" /></a></td>';
									} else{
										$str .= '<td align="center"><a href="professor.php?ctg=survey&action=change_status&survey_action=activate_survey&surveys_ID='.$params['data'][$i]['id'].'"><img src="images/16x16/trafficlight_red.png" border="0px" title="'._ACTIVATE.'" /></a></td>';
									}
								} else {
									if($value == 1){
										$str .= '<td align="center"><a href="javascript:void(0);"><img src="images/16x16/trafficlight_green.png" border="0px" title="'._DEACTIVATE.'" /></a></td>';
									}else{
										$str .= '<td align="center"><a href="javascript:void(0);"><img src="images/16x16/trafficlight_red.png" border="0px" title="'._ACTIVATE.'" /></a></td>';
									}
								}
                            }else if($key == 'start_date' || $key == 'end_date'){
                                $str .= '<td align="left">#filter:timestamp-'.$value.'#</td>';
                            }else if($key != 'id' && $key != 'survey_code' && $key != 'lessons_ID' && $key != 'share' && $key != 'status' && $key != 'end_date' && $key != 'start_date'){
                                $str.='<td align="center">'.$value.'</td>';
                            }else{
                                continue;
                            }
                        }
                        if( strcmp($params['user_type'],"professor") == 0 ){
                            $str .='<td align="center"><a href="professor.php?ctg=survey&action=view_users&surveys_ID='.$params['data'][$i]['id'].'">'.$users['done_users'][0]['count(*)'].'/'.$users['total_users'][0]['count(*)'].'</a></td>
                                <td align="center">
                                    <a href="professor.php?ctg=survey&action=create_survey&survey_action=update&surveys_ID='.$params['data'][$i]['id'].'&screen=1"><img src="images/16x16/edit.png" border="0px" title="'._EDITSURVEY.'"/></a>
                                    <a href="professor.php?ctg=survey&action=preview&surveys_ID='.$params['data'][$i]['id'].'"><img src="images/16x16/search.png" border="0px" title="'._PREVIEW.'"/></a>
                                    <a href="professor.php?ctg=survey&action=statistics&surveys_ID='.$params['data'][$i]['id'].'"><img src="images/16x16/reports.png" border="0px" title="'._SURVEYSTATISTICS.'"/></a>
                                    <a href="'.$params['user_type'].'.php?ctg=survey&surveys_ID='.$params['data'][$i]['id'].'&screen_survey=2"><img src="images/16x16/add.png" border="0px" title="'._EDITQUESTION.'" /></a>
                                    <a href="professor.php?ctg=survey&action=delete&surveys_ID='.$params['data'][$i]['id'].'" onclick="return confirm(\''._IRREVERSIBLEACTIONAREYOUSURE.'\');"><img src="images/16x16/error_delete.png" border="0px" title="'._DELETE.'"/></a>
                                </td>';
                        }
                        if( strcmp($params['user_type'] , "professor") == 0){
                            if(time() > $params['data'][$i]['end_date']){
                                $str .='
                                    <td><input class="flatButton" type="button" value="'._PUBLISH.'" disabled></td>
                                    </tr>
                                    ';
                            }else{
                                $str .='
                                    <td><input class="flatButton" type="button" value="'._PUBLISH.'" onclick="Javascript:self.location=\'professor.php?ctg=survey&action=publish&lessons_ID='.$params['lessons_ID'].'&surveys_ID='.$params['data'][$i]['id'].'\'" ></td>
                                    </tr>
                                    ';
                            }
                        }
                    }
            }
            $str.='</table>
            </form></td></tr></table>
            ';
    return $str;
} 

?>
