<?php
/**
* Smarty plugin: eF_template_printSide function
*/
function smarty_function_eF_template_printScormControls($params, &$smarty) {

	$params['exit'] ? $exitStr = '[<a href = "" ">exit</a>]' : $exitStr = '';
	$params['exitAll'] ? $exitAllStr = '[<a href = "" ">exitAll</a>]' : $exitAllStr = '';
	$params['abandon'] ? $abandonStr = '[<a href = "" ">abandon</a>]' : $abandonStr = '';
	$params['abandonAll'] ? $abandonAllStr = '[<a href = "" ">abandonAll</a>]' : $abandonAllStr = '';
	$params['suspendAll'] ? $suspendAllStr = '[<a href = "" ">suspendAll</a>]' : $suspendAllStr = '';
	
    return $exitStr.'&nbsp;'.$exitAllStr.'&nbsp;'.$abandonStr.'&nbsp;'.$abandonAllStr.'&nbsp;'.$suspendAllStr;
}

?>
