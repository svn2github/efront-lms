<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     eF_formatTitlePath<br>
 * Purpose:  Format the top tilebar path using an algorithm to
 *			 cut each inner link if necessary.
 *
 * @param string
 * @param integer
 * @param integer
 * @param string
 * @return string
 */
function smarty_modifier_eF_formatTitlePath($string, $length = 80, $pathLimit = 6, $etc = '...')
{
 $piecesStart = explode("&raquo;&nbsp;", $string); // with tags
 $stripped = strip_tags($string); //remove tags to count characters
 $piecesStripped = explode("&raquo;&nbsp;", $stripped);

 array_walk($piecesStripped, create_function('&$v, $k', '$v = str_replace("&nbsp;", "", $v);'));

 if (mb_strlen($stripped) <= $length) {
  $finalString = implode("<span>&raquo;&nbsp;</span>", $piecesStart); // with tags
  $finalString = str_replace(" </a>", "</a>", $finalString);
  return $finalString;
 }

 $piecesLength = $piecesStripped;
 array_walk($piecesLength, 'trim');
 array_walk($piecesLength, create_function('&$v, $k', '$v = mb_strlen($v);'));
 $piecesLengthStart = $piecesLength;
 $piecesNum = sizeof($piecesLength);
 $step = 0;
 while (array_sum($piecesLength) > $length && $step < 5) {
 $step++;
  for ($k = 1; $k < $piecesNum; $k++) {
   if ($piecesLength[$k] > $pathLimit) {
    $piecesLength[$k] = $piecesLength[$k] - round($piecesLength[$k]*($piecesNum -$k)/10);
    if(array_sum($piecesLength) <= $length) {
     break;
    }
   }
  }
 }
 $piecesFinal = array();
 foreach ($piecesStart as $key => $value) {
  //$piecesFinal[$key] = str_replace($piecesStripped[$key], mb_substr($piecesStripped[$key], 0, $piecesLength[$key]), $value);

  if ($piecesLengthStart[$key] - $piecesLength[$key] > 3) {
   $replacement = mb_substr($piecesStripped[$key], 0, $piecesLength[$key]).$etc;
  } else {
   $replacement = $piecesStripped[$key];
  }

  $temp = $value;
  // added because preg_replace returns null when value contains /
  $piecesFinal[$key] = preg_replace('/'.$piecesStripped[$key].'<\/a>/', $replacement.'</a>', $value);
  if (is_null($piecesFinal[$key])) {
   $piecesFinal[$key] = $temp;

  }

 }

 $finalString = implode("&raquo;&nbsp;", $piecesFinal); // with tags
 $finalString = str_replace(" </a>", "</a>", $finalString);
 return $finalString;
}
?>
