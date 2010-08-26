<?php
/**
* prints a form
*
*/
function smarty_function_eF_template_printForm($params, &$smarty) {
 $hiddenString = $elementString = $submitString = '';

 foreach ($params['form']['elements'] as $key => $value) {

  if ($value['type'] == 'submit') {
   $submitButtons[] = $value['html'];
  } else if ($value['type'] == 'hidden') {
   $hiddenString .= $value['html'];
  } else if ($value['type'] == 'static' && $value['name'] == 'sidenote') {
   $sideNoteString = '&nbsp;'.$value['label'];
  } else if ($value['type'] == 'static') {
   $elementString .= '
   <tr><td class = "labelCell"></td>
    <td class = "infoCell">'.formatStaticText($value).'</td></tr>';
  } else {
   $value['required'] ? $requiredString = '&nbsp;<span class = "formRequired">*</span>' : $requiredString = '';
   $value['error'] ? $errorString = '<br><span class = "formError">'.$value['error'].'</span>' : $errorString = '';
   $elementString .= '
   <tr><td class = "labelCell">'.($value['label'] ? $value['label'].':&nbsp;' : '').'</td>
    <td class = "elementCell">'.$value['html'].$requiredString.$sideNoteString.$errorString.'</td></tr>';
   $sideNoteString = '';
  }
 }
 if ($submitButtons) {
  $submitString = '
   <tr><td class = "labelCell"></td>
    <td class = "submitCell">'.implode(" ", $submitButtons).'</td></tr>';
 }

 $formString =
<<<FORM
 {$params['form']['javascript']}
 <form {$params['form']['attributes']}>
  $hiddenString
  <table class = "formElements">
  $elementString
  $submitString
  </table>
 </form>
FORM;

 return $formString;
}


?>
