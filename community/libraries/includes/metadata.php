<?php
/**
 * 
 */

if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$loadScripts[] = 'includes/metadata';

try {
    if ($_GET['unit']) {
        $currentUnit = new EfrontUnit($_GET['unit']);
        if (!$currentUnit['metadata']) {
            $defaultMetadata = array('title'       => $currentUnit['name'],
                                     'date'        => date("Y/m/d", $currentUnit['timestamp']));
            $currentUnit['metadata'] = serialize($defaultMetadata);
            $currentUnit -> persist();
        }
        $form = new HTML_QuickForm("empty_form", "post", null, null, null, true);
        try {
            $contentMetadata = unserialize($currentUnit['metadata']);
            $metadata        = new DublinCoreMetadata($contentMetadata);
            $smarty -> assign("T_CONTENT_METADATA_HTML", $metadata -> toHTML($form));
            $smarty -> assign("T_CURRENT_UNIT", $currentUnit);
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = "failure";
        }

        if (isset($_GET['postAjaxRequest'])) {
            if (in_array($_GET['dc'], array_keys($metadata -> metadataAttributes))) {
                if ($_GET['value']) {
                    $contentMetadata[$_GET['dc']] = ($_GET['value']);
                } else {
                    unset($contentMetadata[$_GET['dc']]);
                }
                $currentUnit['metadata'] = serialize($contentMetadata);
            }
            try {
                $currentUnit -> persist();
                echo $_GET['value'];
            } catch (Exception $e) {
                header("HTTP/1.0 500 ");
                echo $e -> getMessage().' ('.$e -> getCode().')';
            }
            exit;
        }
    } else {
        //eF_redirect("".$_SERVER['PHP_SELF'].".php");
    }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}


?>