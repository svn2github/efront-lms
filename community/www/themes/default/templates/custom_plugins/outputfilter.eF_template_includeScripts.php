<?php
/**
* Loads specific js based on whether they are used (Loading Order does matter!)
*/

function smarty_outputfilter_eF_template_includeScripts($compiled, &$smarty) {
    $basicScripts = array();
    if (preg_match('/tabber/', $compiled)) {
        $basicScripts[] = 'tabber';
    } 
    if (preg_match('/sortedTable/', $compiled)) {
        $basicScripts[] = 'ajax_sorted_table/ajax_sorted_table';
        $compiled .= '<script language="JavaScript" type="text/javascript">
        				var sorted_translations = new Array();
        				sorted_translations["loadingdata"] = "'._LOADINGDATA.'";
        				sorted_translations["operationaffectmany"] = "'._OPERATIONWILLAFFECTMANYAREYOUSURE.'";
        				sorted_translations["filter"] = "'._FILTER.'";
        				sorted_translations["rowsperpage"] = "'._ROWS.'";
        				sorted_translations["displayingresults"] = "'.mb_convert_case(_RESULTS, MB_CASE_TITLE).'";
        				sorted_translations["outof"] = "'._OUTOF.'";
        			 </script>';
    } 
    if (preg_match('/tooltipSpan/', $compiled) || preg_match('/class = "calendar"/', $compiled)) {
        $basicScripts[] = 'wz_tooltip';
    }
    if (preg_match('/dhtmlgoodies_tree/', $compiled) || preg_match('/class = "calendar"/', $compiled)) {
        $basicScripts[] = 'drag-drop-folder-tree';
    }
 
    if (preg_match('/__shouldTriggerNextNotifications/', $compiled)) {
    	$compiled .= '<script>if (__shouldTriggerNextNotifications) { new Ajax.Request("send_notifications.php?ajax=1", {method:\'get\', asynchronous:true}); } </script>';
    }   
     
    if (sizeof($basicScripts) > 0) {
        $compiled .= '<script type = "text/javascript" src = "js/scripts.php?build='.G_BUILD.'&load='.implode(",", $basicScripts).'"> </script>';
    }
    
    return $compiled;
}

?>