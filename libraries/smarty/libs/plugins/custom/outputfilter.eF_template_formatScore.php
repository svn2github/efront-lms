<?php
/**
* Replaces occurences of the form #filter:timestamp-1132843907# with the current date
*/
function smarty_outputfilter_eF_template_formatScore($compiled, &$smarty) {
    $new = preg_replace("/#filter:score-#/e", "", $compiled);
    $new = preg_replace("/#filter:score-([0-9.]*)#/e", "formatScore(\$1)", $new);
    
    return $new;
}

?>