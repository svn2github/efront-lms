<?php
/**

* Replaces occurences of the form ##EEFRONTEDITOROFFSET## with the right editor offset path

*/
function smarty_outputfilter_eF_template_setEditorOffset($compiled, &$smarty) {
 $offset = mb_substr( G_SERVERNAME , mb_strpos(G_SERVERNAME, $_SERVER["HTTP_HOST"]) + mb_strlen($_SERVER["HTTP_HOST"]) + 1);
    $new = preg_replace("/##EFRONTEDITOROFFSET##/", '/'.$offset, $compiled);
    return $new;
}

?>
