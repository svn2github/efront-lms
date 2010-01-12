<?php
/**
* Smarty plugin: smarty_function_eF_template_printSurveysList function. Prints surveys
*
*/
function smarty_function_eF_template_printSurvey($params ,  &$smarty){
    //$returnVal = '';
   
    if( strcmp($params['user_type'],"student") == 0){
        $header = '<form name="submitSurvey" method="POST" action="student.php?ctg=survey&op=survey_store&screen_survey=3">
            <table width="100%">';
    }
    if( strcmp($params['user_type'],"external") == 0){
        $header = '<form name="submitSurvey" method="POST" action="external_survey.php?username='.$params['username'].'&coupon='.$params['coupon'].'&surveys_ID='.$params['surveys_ID'].'&op=survey_store&screen=3">
            <table width="98%">';
    }
    if( strcmp($params['user_type'],"professor") == 0){
        $header = '<table width="100%" align="left">';
    }
    
    foreach($params['data'][0] as $key => $value){
        if( strcmp($key,"survey_name") == 0 ){
            $header .= '<tr><td align="left"><b>'.$value.'</b></td>
                        </tr>';
        }else if( strcmp($key,"survey_info") == 0 ){
            $header .= '<tr><td align="left"><b>'.$value.'</b></td>
                        </tr>
                        <tr><td class="horizontalSeparator">&nbsp;</td></tr>
                        ';
        }else{
            continue;
        }
    }
    $header .= '<tr><td colspan="2">'.$params['intro'].'</td></tr>
                <tr><td colspan="2">&nbsp;</td></tr>';

    $questions = '';
    for($i = 0 ; $i < sizeof($params['questions']) ; $i ++){
        $j = $i +1;
        $questions .= '<tr>
                <td><input type="hidden" name="surveys_ID" value="'.$params['questions'][$i]['surveys_ID'].'"></td>
                <td><input type="hidden" name="question_ID['.$i.']" value="'.$params['questions'][$i]['id'].'"></td>
                   </tr>';
        if( strcmp($params['questions'][$i]['type'],"yes_no") == 0){							
			$questions .= ' <tr><td class = "questionWeight" style = "vertical-align:middle"><img style = "vertical-align:middle" src="images/32x32/surveys.png"/>'._QUESTION.'&nbsp;'.$j.'</td></tr>
                    <tr>
                        <td>
                        &nbsp;'.$params['questions'][$i]['question'].'
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>
                            &nbsp;&nbsp;<select name="answer['.$i.']">';
                            $cnt = 0;
                            foreach(unserialize($params['questions'][$i]['answers']) as $key => $value){
                                foreach($value as $new_key => $new_value){
                                    $questions .= '<option value="'.$new_value.'">'.$new_value.'</option>';
                                    $cnt ++;
                                }
                            }
                            $questions.='
                            </select>
                        </td>
                    </tr>';
                    if($params['action'] == 'survey_preview'){
                        $user_answer = unserialize($params['answers'][$i]['user_answers']);
                        $questions .= '<tr><td class="surveyAnswer">'._STUDENTANSWER.':'.$user_answer.'</td></tr>';
                    }
        }
        if( strcmp($params['questions'][$i]['type'],"development") == 0 ){
            $questions .= ' <tr><td class="questionWeight" style = "vertical-align:middle"><img style = "vertical-align:middle" src="images/32x32/surveys.png" border="0px" />'._QUESTION.'&nbsp;'.$j.'</td></tr>
                    <tr>
                        <td>
                         &nbsp;'.$params['questions'][$i]['question'].'
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></td>
                    <tr>
                        <td>
                            <textarea class="simpleEditor" rows="5" cols="50" id="data" name="answer['.$i.']">
                            </textarea>
                        <td>
                    </tr>
                ';
                
                if($params['action'] == 'survey_preview'){
                    $user_answer = unserialize($params['answers'][$i]['user_answers']);
                    $questions .= '<tr><td class="surveyAnswer">'._STUDENTANSWER.'&nbsp;:&nbsp;'.$user_answer.'</td></tr>';
                }
        }
        if( strcmp($params['questions'][$i]['type'],"dropdown") == 0){
            $choices = unserialize($params['questions'][$i]['answers']);
            $questions .= '<tr><td class="questionWeight" style = "vertical-align:middle"><img style = "vertical-align:middle" src="images/32x32/surveys.png" border="0px" />'._QUESTION.'&nbsp;'.$j.'</td></tr>
                    <tr>
                        <td>
                            &nbsp;'.$params['questions'][$i]['question'].'
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>
                            &nbsp;&nbsp;<select name="answer['.$i.']">
                            ';
                            for($k = 0 ; $k < sizeof($choices['drop_down']) ; $k ++)
                                $questions .= '<option name="label['.$k.']">'.$choices['drop_down'][$k].'</option>';
            $questoins .='
                            </select>
                        </td>
                    </tr>';
            
            if($params['action'] == 'survey_preview'){
                $user_answer = unserialize($params['answers'][$i]['user_answers']);
                $questions .= '<tr><td class="surveyAnswer">'._STUDENTANSWER.'&nbsp;:&nbsp;'.$user_answer.'</td></tr>';
            }
        }
        if( strcmp($params['questions'][$i]['type'],"multiple_one") == 0){
            $choices = unserialize($params['questions'][$i]['answers']);
            $questions .= '<tr><td class="questionWeight" style = "vertical-align:middle"><img style = "vertical-align:middle" src="images/32x32/surveys.png" border="0px" />'._QUESTION.'&nbsp;'.$j.'</td></tr>
                    <tr>
                        <td>
                        &nbsp;'.$params['questions'][$i]['question'].'
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td>
                    <radiogroup>
                    ';
                    for($k = 0 ; $k < sizeof($choices['multiple_one']) ; $k ++)
                        $questions .= '<input style = "vertical-align:top" class="inputRadioSurvey" type="radio" name="answer['.$i.']" value="'.$choices['multiple_one'][$k].'">&nbsp;'.$choices['multiple_one'][$k].'<br>';
                    $questions .= '</radiogroup>
                    </td></tr>';
            if($params['action'] == 'survey_preview'){
                $user_answer = unserialize($params['answers'][$i]['user_answers']);
                $questions .= '<tr><td class="surveyAnswer">'._STUDENTANSWER.'&nbsp;:&nbsp;'.$user_answer.'</td></tr>';
            }
        }
        if( strcmp($params['questions'][$i]['type'],"multiple_many") == 0){
            $choices = unserialize($params['questions'][$i]['answers']);
            $questions .= '<tr><td class="questionWeight" style = "vertical-align:middle"><img style = "vertical-align:middle" src="images/32x32/surveys.png" border="0px" />'._QUESTION.'&nbsp;'.$j.'</td></tr>
                    <tr>
                        <td>&nbsp;'.$params['questions'][$i]['question'].'</td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                      ';
                      for($k = 0 ; $k < sizeof($choices['multiple_many']) ; $k++){
                         $questions .= '<tr><td><input class="inputCheckbox" type="checkbox" name="answer['.$i.']['.$k.']" value="'.$choices['multiple_many'][$k].'"/>&nbsp;'.$choices['multiple_many'][$k].'</td></tr>';
                      }
            if($params['action'] == 'survey_preview'){
                $user_answer = unserialize($params['answers'][$i]['user_answers']);
                $keys = array_keys($user_answer);
                $questions .= '<tr><td class="surveyAnswer">'._STUDENTANSWER.':&nbsp;&nbsp;';
                for($j = 0 ; $j < sizeof($user_answer) ; $j ++){
                    $questions .= $user_answer[$keys[$j]].' &nbsp;';
                }
                $questions .= '</td></tr>';
            }
        }
        $questions .= '<tr><td>&nbsp;</td></tr>';
    }
    if( strcmp($params['user_type'],"professor") == 0 ){
        $questions .= '<tr><td align="center">&nbsp;</td></tr></table>';
    }
    if( strcmp($params['user_type'],"student") == 0 || strcmp($params['user_type'],"external") == 0 ){
        $questions .= '<tr><td align="left"><input class="flatButton" type="submit" value="'._SURVEYSUBMIT.'"/></td></tr>
        </table></form>';
    }
    
    $returnVal = '<table style = "width:100%;text-align:left"><tr><td>'.$header.$questions.'</td></tr></table>';
    
    return $returnVal;
}
?>
