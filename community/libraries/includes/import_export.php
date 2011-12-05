<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}

$loadScripts[] = "includes/import";
$import_export_types = EfrontImport::getImportTypes();

// ******************************************* Import form *******************************************
$importForm = new HTML_QuickForm("import_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=import_export&op=import", "", null, true);

$importForm -> addElement('file', 'import_file', _DATAFILE, 'class = "inputText"');
$importForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024); //getUploadMaxSize returns size in KB
$importForm -> addRule('import_file', _YOUMUSTUPLOADFILE, 'uploadedfile', null, 'client');

$importForm -> addElement('select', 'import_type', _DATATYPE, $import_export_types, 'id ="import_type" class = "inputCheckbox" onchange="changeCategory(this.value)"');
$importForm -> addElement('advcheckbox', 'import_keep', _KEEPEXISTINGUSERS, null, 'class = "inputCheckbox"', array(0, 1));
$importForm -> addElement('advcheckbox', 'replace_assignments', _REPLACEASSIGNMENTS, null, 'class = "inputCheckbox"', array(0, 1));
$importForm -> addElement("select", "date_format", _DATEFORMAT, array("DD/MM/YYYY" => "DD/MM/YYYY", "MM/DD/YYYY" => "MM/DD/YYYY", "YYYY/MM/DD" => "YYYY/MM/DD"));
$importForm -> setDefaults(array('import_keep' => 0));
$importForm -> addElement('submit', 'submit_import', _IMPORTDATA, 'class = "flatButton"');

$help_info = array();
foreach ($import_export_types as $type => $name) {
 if ($type != "anything") {
  $help_info[$type] = array("mandatory" => implode(", ", EfrontImport::getMandatoryFields($type)),
             "optional" => implode(", ", EfrontImport::getOptionalFields($type)),
          "sample_type" => $type);
 }
}

$smarty -> assign("T_HELP_IMPORT_INFO", $help_info);

if (isset($_GET['csv_sample']) && $_GET['csv_sample']==1 && isset($_GET['sample_type'])) {
 $sample_type = $_GET['sample_type'];
    header("content-type:text/plain");
    header('content-disposition: attachment; filename= "csv_'.$sample_type.'_sample.csv"');
    echo implode(",", EfrontImport::getMandatoryFields($sample_type)) . "," . implode(",", EfrontImport::getOptionalFields($sample_type)) . "\n";
    exit;
}


if ($importForm -> isSubmitted()) {
    try {
        if (!is_dir($currentUser -> user['directory']."/temp")) {
            mkdir($currentUser -> user['directory']."/temp", 0755);
        }
        $importForm -> exportValue('import_keep') ? $replaceUsers = false : $replaceUsers = true;
        $filesystem = new FileSystemTree($currentUser -> user['directory']."/temp");
        $uploadedFile = $filesystem -> uploadFile('import_file');

        $options = array("replace_existing" => $replaceUsers,
             "date_format" => $importForm -> exportValue('date_format'),
             "replace_assignments" => $importForm->exportValue('replace_assignments'));

        $importer = EfrontImportFactory::factory("csv", $uploadedFile, $options);
        $importType = $importForm -> exportValue('import_type');

        if ($importType == "anything") {
         $import_types = $import_export_types;
         unset($import_types['anything']);





         $log = array("success" => array(), "failure" => array());
         foreach ($import_types as $import_type => $import_name) {
          $templog = $importer -> import($import_type);

          $headerType = EfrontImport::getImportTypeName($import_type);
          if ($headerType) {
           $import_header = "<u>" . _IMPORTRESULTSFOR . " " . $headerType . "</u>";
           if (!empty($templog["success"])) {
            array_unshift($templog["success"], $import_header);
           } else {
            $templog["success"] = array($import_header);
           }
           $log["success"] = array_merge($log["success"], $templog["success"]);
           if (!empty($templog["failure"])) {
            array_unshift($templog["failure"], $import_header);
           } else {
            $templog["failure"] = array($import_header);
           }
           $log["failure"] = array_merge($log["failure"], $templog["failure"]);
          }
         }


         // Variable to remove the header elements from success/failure counting



          $excess_elements = sizeof($import_export_types); // minus the 'anything'

        } else {

         $log = $importer -> import($importType);
         file_put_contents('import_log_'.time().'.txt', implode("\n", $log['success']).implode("\n", $log['failure']));
         $excess_elements = 0;
        //	pr($log);
        }
        $message_type = 'success';
        $successes = sizeof($log['success']) - $excess_elements;
  $failures = sizeof($log['failure']) - $excess_elements;
        if ($successes < $failures) {
         $message_type = 'failure';
        }
        if ($successes) {
         if ($successes != 1) {
          $message .= $successes . " " . _SUCCESSFULLIMPORTS;
    $message .= ": <BR>";
         }
   $message .= "&nbsp;&nbsp;&nbsp;" . implode("<BR>&nbsp;&nbsp;&nbsp;", $log['success']);
  }
  if ($failures) {
   if ($failures != 1 || $successes > 0) {
    if ($successes > 0) {
     $message .= "<BR><hr><BR>";
    }
    $message .= $failures . " " . _FAILEDIMPORTS;
    $message .= ": <BR>";
   }
   $message .= "&nbsp;&nbsp;&nbsp;" . implode("<BR>&nbsp;&nbsp;&nbsp;", $log['failure']);
  }
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$importForm -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
$importForm -> setRequiredNote(_REQUIREDNOTE);
$importForm -> accept($renderer);
$smarty -> assign('T_IMPORT_FORM', $renderer -> toArray());
// ******************************************* Export form *******************************************
$exportForm = new HTML_QuickForm("export_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=import_export&op=export&tab=export", "", null, true);
unset($import_export_types['anything']);
$exportForm -> addElement('select', 'export_type', _DATATYPE, $import_export_types, 'class = "inputCheckbox"', array(0, 1));
$exportForm -> addElement("select", "date_format", _DATEFORMAT, array("DD/MM/YYYY" => "DD/MM/YYYY", "MM/DD/YYYY" => "MM/DD/YYYY", "YYYY/MM/DD" => "YYYY/MM/DD"));
$exportForm -> addElement('radio', 'export_separator', _KEEPEXISTINGUSERS, null, 'csvA');
$exportForm -> addElement('radio', 'export_separator', _KEEPEXISTINGUSERS, null, 'csvB');
$exportForm -> setDefaults(array('export_separator' => "csvA"));
$exportForm -> addElement('submit', 'submit_export', _EXPORTDATA, 'class = "flatButton"');
if ($exportForm -> isSubmitted() && $exportForm -> validate()) {
    $exportForm -> exportValue('export_separator') == 'csvA' ? $separator = ',' : $separator = ';';
    try {
     $options = array("separator" => $separator,
          "date_format" => $exportForm -> exportValue('date_format'));
     $exporter = EfrontExportFactory::factory("csv", $options);
        $file = $exporter -> export($exportForm -> exportValue('export_type'));
        header("content-type:".$file['mime_type']);
        header('content-disposition: attachment; filename= "'.($file['name']).'"');
        readfile($file['path']);
        exit;
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = _ERRORRESTORINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
    //$smarty -> assign("T_EXPORTED_FILE", $file);
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$exportForm -> accept($renderer);
$smarty -> assign('T_EXPORT_FORM', $renderer -> toArray());
