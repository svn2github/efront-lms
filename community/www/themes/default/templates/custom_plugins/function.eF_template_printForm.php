<?php
/**
* prints a form
*
*/
function smarty_function_eF_template_printForm($params, &$smarty) {
 $hiddenString = $elementString = $submitString = '';

 foreach ($params['form']['elements'] as $value) {
  if ($value['type'] == 'submit') {
   $submitButtons[] = $value['html'];
  } else if ($value['type'] == 'hidden') {
   $hiddenString .= $value['html'];
  } else if ($value['type'] == 'static') {
   $elementString .= '
   <tr><td class = "labelCell"></td>
    <td class = "infoCell">'.$value['label'].'</td></tr>';
  } else {
   $elementString .= '
   <tr><td class = "labelCell">'.$value['label'].':&nbsp;</td>
    <td class = "elementCell">'.$value['html'].($value['required'] ? '&nbsp;<span class = "formRequired">*</span>' : '').($value['error'] ? '<br><span class = "formError">'.$value['error'].'</span>' : '').'</td></tr>';
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
