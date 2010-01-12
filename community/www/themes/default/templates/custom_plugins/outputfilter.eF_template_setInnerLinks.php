<?php
/**
* Replaces occurences of the form ##EFRONTINNERLINK## with the right user type file
*/

function smarty_outputfilter_eF_template_setInnerLinks($compiled, &$smarty) {
    $new = preg_replace("/##EFRONTINNERLINK##/", $_SESSION['s_lesson_user_type'], $compiled);

    return $new;
}

?>