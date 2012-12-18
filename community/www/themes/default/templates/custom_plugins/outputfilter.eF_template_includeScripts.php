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
        $basicScripts[] = 'scriptaculous/controls';
     $basicScripts[] = 'ajax_sorted_table/ajax_sorted_table';
        $compiled .= '<script language="JavaScript" type="text/javascript">
            var sorted_translations = new Array();
            sorted_translations["loadingdata"] = "'._LOADINGDATA.'";
            sorted_translations["operationaffectmany"] = "'._OPERATIONWILLAFFECTMANYAREYOUSURE.'";
            sorted_translations["filter"] = "'._FILTER.'";
            sorted_translations["rowsperpage"] = "'._ROWS.'";
            sorted_translations["displayingresults"] = "'.mb_convert_case(_RESULTS, MB_CASE_TITLE).'";
            sorted_translations["outof"] = "'._OUTOF.'";
            sorted_translations["_SHOWINGONLYACTIVEENTITIES"] = "'._SHOWINGONLYACTIVEENTITIES.'";
            sorted_translations["_SHOWINGONLYINACTIVEENTITIES"] = "'._SHOWINGONLYINACTIVEENTITIES.'";
            sorted_translations["_SHOWINGALLENTITIES"] = "'._SHOWINGALLENTITIES.'";
            sorted_translations["_NEXT"] = "'._NEXT.'";
            sorted_translations["_PREVIOUS"] = "'._PREVIOUS.'";
            sorted_translations["_FIRST"] = "'._FIRST.'";
            sorted_translations["_LAST"] = "'._LAST.'";
            sorted_translations["_ALLBRANCHES"] = "'._ALLBRANCHES.'";
            sorted_rtl = "'.$GLOBALS['rtl'].'";
           </script>';
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
