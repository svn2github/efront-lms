<?php
/**
* Smarty plugin: eF_template_printSide function
*/
function smarty_function_eF_template_printPreviousNext($params, &$smarty) {

	$params['previous'] ? $previousStr = '<a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['previous']['id'].'" title = "'.$params['previous']['name'].'"><img class = "handle" src = "images/32x32/navigate_left.png"  title = "'.$params['previous']['name'].'" alt = "'.$params['previous']['name'].'" /></a><a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['previous']['id'].'" title = "'.$params['previous']['name'].'">'.$params['previous']['name'].'</a>' : $previousStr = '';
	$params['next']     ? $nextStr     = '<a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['next']['id'].'" 	 title = "'.$params['next']['name'].'"    >'.$params['next']['name'].'</a><a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['next']['id'].'" 	 title = "'.$params['next']['name'].'"    ><img class = "handle" src = "images/32x32/navigate_right.png" title = "'.$params['next']['name'].'" 	   alt = "'.$params['next']['name'].'"     /></a>' : $nextStr     = '';
    
    return $previousStr.'&nbsp;'.$nextStr;
}

?>