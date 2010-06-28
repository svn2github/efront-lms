<?php
/**

* Smarty plugin: eF_template_printSide function

*/
function smarty_function_eF_template_printPreviousNext($params, &$smarty) {
 mb_strlen($params['previous']['name']) - 3 > EfrontUnit::MAXIMUM_NAME_LENGTH ? $params['previous']['name'] = mb_substr($params['previous']['name'], 0, EfrontUnit::MAXIMUM_NAME_LENGTH).'...' : null;
 mb_strlen($params['next']['name']) - 3 > EfrontUnit::MAXIMUM_NAME_LENGTH ? $params['next']['name'] = mb_substr($params['next']['name'], 0, EfrontUnit::MAXIMUM_NAME_LENGTH).'...' : null;
 $params['previous'] ? $previousStr = '<a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['previous']['id'].'" title = "'.$params['previous']['name'].'"><img class = "handle" src = "images/32x32/navigate_left.png"  title = "'.$params['previous']['name'].'" alt = "'.$params['previous']['name'].'" /></a><a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['previous']['id'].'" title = "'.$params['previous']['name'].'">'.$params['previous']['name'].'</a>' : $previousStr = '';
 $params['next'] ? $nextStr = '<a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['next']['id'].'" 	 title = "'.$params['next']['name'].'"    >'.$params['next']['name'].'</a><a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['next']['id'].'" 	 title = "'.$params['next']['name'].'"    ><img class = "handle" src = "images/32x32/navigate_right.png" title = "'.$params['next']['name'].'" 	   alt = "'.$params['next']['name'].'"     /></a>' : $nextStr = '';
/*

	if ($params['previous']) {

		$previousStr = '

			<table style = "border:1px solid red;">

				<tr><td><a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['previous']['id'].'" title = "'.$params['previous']['name'].'"><img class = "handle" src = "images/32x32/navigate_left.png"  title = "'.$params['previous']['name'].'" alt = "'.$params['previous']['name'].'" /></a></td>

					<td><a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['previous']['id'].'" title = "'.$params['previous']['name'].'">'.$params['previous']['name'].'</a></td></tr>

			</table>

			';

	}

	if ($params['next']) {

		$nextStr     = '

			<table style = "border:1px solid red;">

				<tr><td><a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['next']['id'].'" 	 title = "'.$params['next']['name'].'"    >'.$params['next']['name'].'</a></td>

					<td><a href = "'.basename($_SERVER['PHP_SELF']).'?view_unit='.$params['next']['id'].'" 	 title = "'.$params['next']['name'].'"    ><img class = "handle" src = "images/32x32/navigate_right.png" title = "'.$params['next']['name'].'" 	   alt = "'.$params['next']['name'].'"     /></a></td></tr>

			</table>

		';

	}

*/
    return $previousStr.'&nbsp;'.$nextStr;
}
?>
