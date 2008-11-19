<?php
/**
* Smarty plugin: smarty_function_eF_template_printUsersList function. 
*
*/
function smarty_function_eF_template_printLessonsList($params, &$smarty) {

    $lessons_str     = '<option value = "-1">---- '._LESSONS.' ----</option>';
    
foreach ($params['data'] as $key => $value) {
  
	for ($i = 0; $i < sizeof($params['data'][$key]); $i++) {
        
        $params['selected'] == $params['data'][$key][$i]['id'] ? $selected = 'selected' : $selected = '';
        

        $lessons_str .= '
                    <option value = "'.$params['data'][$key][$i]['id'].'" '.$selected.'>'.$params['data'][$key][$i]['name'].' ('.$key.')</option>';


        
    }        
}  
    return $lessons_str; 
}

?>