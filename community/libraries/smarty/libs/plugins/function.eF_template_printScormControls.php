<?php
/**
* Smarty plugin: eF_template_printSide function
*/
function smarty_function_eF_template_printScormControls($params, &$smarty) {


	$params['start'] ? $startStr = '[<a href = "'.basename($_SERVER['PHP_SELF']).'?navigation=start' . '" />start</a>]' : $startStr     = '';	
	
	$params['previous'] ? $previousStr = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=previous' . '" />previous	 </a>]' : $previousStr     =
		'[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=previous" id="previous" onClick="return false"  />previous [Disabled]	 </a>]';	
	
	$params['continue'] ? $continueStr = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=continue' . '" />continue	 </a>]' : $continueStr     = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=continue" id="continue" onClick="return false" />continue [Disabled]</a>]';
	


	$params['exit'] ? $exitStr = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=exit' . '" />exit	 </a>]' : $exitStr     = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=exit" id="exit" onClick="return false" />exit[Disabled]	 </a>]';

	$params['exitAll'] ? $exitAllStr = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=exitAll' . '" />exitAll	 </a>]' : $exitAll     = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=exitAll" id="exitAll" onClick="return false" />exitAll[Disabled]	 </a>]';	

	$params['abandon'] ? $abandonStr = '[<a href = "" ">abandon</a>]' : $abandonStr = '[<a href = "" ">abandon</a>]';
	$params['abandonAll'] ? $abandonAllStr = '[<a href = "" ">abandonAll</a>]' : $abandonAllStr = '[<a href = "" ">abandonAll [Disabled]</a>]';



	$params['suspendAll'] ? $suspendAllStr = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=suspendAll' . '" />suspendAll	 </a>]' : $suspendAll     = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=suspendAll" id="suspendAll" onClick="return false" />suspendAll [Disabled]</a>]';


	$params['resumeAll'] ? $resumeAllStr = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=resumeAll' . '" />resumeAll	 </a>]' : $resumeAll     = '[<a href = "'.basename($_SERVER['PHP_SELF']). '?navigation=resumeAll" id="resumeAll" onClick="return false" />resumeAll	[Disabled]</a>]';	
	



	return $startStr.'&nbsp;'.$previousStr.'&nbsp;'.$continueStr.'&nbsp;'.$jumpStr.'&nbsp;'.$exitStr.'&nbsp;'.$exitAllStr.'&nbsp;'.$abandonStr.'&nbsp;'.$abandonAllStr.'&nbsp;'.$suspendAllStr.'&nbsp;'.$resumeAllStr;







}

?>
