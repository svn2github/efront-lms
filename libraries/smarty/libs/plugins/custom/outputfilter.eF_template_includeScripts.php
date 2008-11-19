<?php
/**
* Loads specific js based on whether they are used (Loading Order does matter!)
*/

function smarty_outputfilter_eF_template_includeScripts($compiled, &$smarty) {
    if (preg_match('/tabber/', $compiled)) {
        $compiled .= '<script language = "JavaScript" type = "text/javascript" src = "js/tabber.php"></script>';
    } 
    if (preg_match('/sortedTable/', $compiled)) {
        $compiled .= '<script language="JavaScript" type="text/javascript" src="js/sort_page_table_ajax2.php"></script>';
    } 
    if (preg_match('/tooltipSpan/', $compiled) || preg_match('/class = "calendar"/', $compiled)) {
        $compiled .= '<script language="JavaScript" type="text/javascript" src="js/wz_tooltip.php"></script>';
    } 
    return $compiled;
}

?>