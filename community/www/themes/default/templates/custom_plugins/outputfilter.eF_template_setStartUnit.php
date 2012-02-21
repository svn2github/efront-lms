<?php
/**

* Replaces occurences of the form ##STARTUNIT## with the right editor offset path

*/
function smarty_outputfilter_eF_template_setStartUnit($compiled, &$smarty) {
    $new = str_replace("##CLICKTOSTARTUNIT##", _CLICKTOSTARTUNIT, $compiled);
    $new = str_replace("##STARTUNIT##", _STARTUNIT, $new);
    return $new;
}
?>
