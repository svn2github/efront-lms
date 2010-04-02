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

$importForm -> addElement('select', 'import_type', _DATATYPE, $import_export_types, 'id ="import_type" class = "inputCheckbox" onchange="changeCategory(this.value)"', array(0, 1));
$importForm -> addElement('advcheckbox', 'import_keep', _KEEPEXISTINGUSERS, null, 'class = "inputCheckbox"', array(0, 1));
$importForm -> addElement("select", "date_format", _DATEFORMAT, array("DD/MM/YYYY" => "DD/MM/YYYY", "MM/DD/YYYY" => "MM/DD/YYYY", "YYYY/MM/DD" => "YYYY/MM/DD"));
$importForm -> setDefaults(array('import_keep' => 0));
$importForm -> addElement('submit', 'submit_import', _IMPORTDATA, 'class = "flatButton"');

$help_info = array();
foreach ($import_export_types as $type => $name) {
 if ($type != "anything") {
  $help_info[$type] = array("mandatory" => implode(", ", EfrontImport::getMandatoryFields($type)),
             "optional" => implode(", ", EfrontImport::getOptionalFields($type)));
 }
}

$smarty -> assign("T_HELP_IMPORT_INFO", $help_info);

if ($importForm -> isSubmitted() && $importForm -> validate()) {
    try {
        if (!is_dir($currentUser -> user['directory']."/temp")) {
            mkdir($currentUser -> user['directory']."/temp", 0755);
        }
        $importForm -> exportValue('import_keep') ? $replaceUsers = false : $replaceUsers = true;

        $filesystem = new FileSystemTree($currentUser -> user['directory']."/temp");
        $uploadedFile = $filesystem -> uploadFile('import_file');

        $options = array("replace_existing" => $replaceUsers,
             "date_format" => $importForm -> exportValue('date_format'));
        $importer = EfrontImportFactory::factory("csv", $uploadedFile, $options);
        //pr($importForm -> exportValues());
        //echo $importForm -> exportValue('import_type')."<BR>";
        $importType = $importForm -> exportValue('import_type');

        if ($importType == "anything") {
         $import_types = $import_export_types;
         unset($import_types['anything']);
         $import_types['employees'] = 1;
         $log = array("success" => array(), "failure" => array());
         foreach ($import_types as $import_type => $import_name) {
          $templog = $importer -> import($import_type);
          $log["success"] = array_merge($log["success"], $templog["success"]);
          $log["failure"] = array_merge($log["failure"], $templog["failure"]);

         }

        } else {
         $log = $importer -> import($importType);
        //	pr($log);
        }
        $message_type = 'success';
        $successes = sizeof($log['success']);
  $failures = sizeof($log['failure']);
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
   if ($failures != 1) {
    $message .= "<BR>" . $failures . " " . _FAILEDIMPORTS;
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
/*

//include "module_importExportUsers.php";

$importForm = new HTML_QuickForm("import_users_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=import_export&oper=import_users", "", null, true);

$importForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter



//$fileUpload = & HTML_QuickForm :: createElement('file', 'users_file', _DATAFILE, 'class = "inputText"');

//$importForm -> addElement($fileUpload);

$importForm -> addElement('file', 'users_file', _DATAFILE, 'class = "inputText"');

$importForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);            //getUploadMaxSize returns size in KB

$importForm -> addRule('users_file', _YOUMUSTUPLOADFILE, 'uploadedfile', null, 'client');



$importForm -> addElement('radio', 'replace_users', _KEEPEXISTINGUSERS, null, 'keep');



#ifdef ENTERPRISE

    include "../libraries/module_hcd_tools.php";

    $company_branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");

    $importForm -> addElement('select', 'branch' , _INSERTINTOBRANCH, eF_createBranchesTreeSelect($company_branches,5),'class = "inputText"');

#endif



#ifndef COMMUNITY

    $result = eF_getTableData("groups", "id, name","");



    $groups = array("0" => _NOSPECIFICGROUP);

    foreach ($result as $group) {

        $groups[$group['id']] = $group['name'];

    }

    $importForm -> addElement('select', 'group' , _INSERTINTOGROUP, $groups,'class = "inputText"');

#endif



$importForm -> addElement('radio', 'replace_users', _KEEPEXISTINGUSERS, null, 'keep');

$importForm -> addElement('radio', 'replace_users', _REPLACEEXISTINGUSERS, null, 'replace');

$importForm -> addElement('checkbox', 'send_email', _SENDINFOVIAEMAIL);

$importForm -> addElement('submit', 'submit_import_users', _IMPORTUSERSDATA, 'class="flatButton"');



$importForm -> setDefaults(array('replace_users' => 0));



//$form_sendEmail = & HTML_QuickForm :: createElement('checkbox', 'send_email', _SENDINFOVIAEMAIL, null, null);

//$importForm -> addElement($form_sendEmail);

$admin = '"'.$_SESSION['s_login'].'"';

$usersTable  = eF_getTableData("users", "*", "");

$tableFields = array_keys($usersTable[0]);

//exclude additional accounts that destroy csv format because of serialized data

$tableFields = array_values(array_diff($tableFields, array("additional_accounts")));



$smarty -> assign("T_FIELDS", $tableFields);

if (isset($_GET['csv_sample']) && $_GET['csv_sample']==1) {

    header("content-type:text/plain");

    header('content-disposition: attachment; filename= "csv_sample.txt"');

    echo implode(";", $tableFields);

    foreach ($tableFields as $field) {

        $userFields[$field] = $currentUser -> user[$field];

    }

    $userFields['password'] = '<password>';

    echo "\n".implode(";", $userFields);

    exit;

}



if ($importForm -> isSubmitted() && $importForm -> validate()) {

    try {

        if (!is_dir($currentUser -> user['directory']."/temp")) {

            mkdir($currentUser -> user['directory']."/temp", 0755);

        }

        $importForm -> exportValue('replace_users') ? $replaceUsers = true : $replaceUsers = false;



        $filesystem   = new FileSystemTree($currentUser -> user['directory']."/temp");

        $uploadedFile = $filesystem -> uploadFile('users_file');



        list($newUsers, $messages) = EfrontSystem :: importUsers($uploadedFile, $replaceUsers);



        //pr($newUsers);

#ifdef ENTERPRISE

            if ($importForm -> exportValue('branch')) {

                $branchID = $importForm -> exportValue('branch');

                $branch_to_import_to = new EfrontBranch($branchID);

                // Users are assigned to a branch through a specific (here the default) job description

                $jobId = $branch_to_import_to -> getDefaultJobDescription();

            }

#endif



#ifndef COMMUNITY

            if ($importForm -> exportValue('group')) {

                $group = new EfrontGroup($importForm -> exportValue('group'));

                $group -> addUsers($newUsers);

            }

#endif



        foreach ($newUsers as $key => $value) {

            //pr($value);

            if ($importForm -> exportValue('send_email')) {

                $subject    = _ACCOUNTACTIVATIONMAILSUBJECT;

                $from       = $configuration['system_email'];

                $to         = $value -> user['email'];

                $body       = _THISEMAIL.'<br><br>'._CONTAINSINFORMATIONABOUTYOURACCOUNTINTHEPLATFORM.' '._EFRONTNAME.'<br><br>'._LOGIN.':'.$value -> user['login'].'<br><br>'._PASSWORD.':'.$value -> user['password'].'<br><br>'._THANKYOU;

                eF_mail($from, $to, $subject, $body);

            }



            // If we are in HCD, then the $newUsers is an array of eFront Employee Objects

            if ($jobId) {

                $employee = $value -> aspects['hcd'];

                $employee -> addJob($value, $jobId, $branchID, 0, _NOSPECIFICJOB);

            }

        }

        $message      = _TOTALINSERTED.' '.sizeof($newUsers).' '._USERS;

        $message_type = 'success';

        if (sizeof($messages) > 0) {

            $message     .= '. '._THEFOLLOWINGUSERSCOULDNOTBEIMPORTED.":<br>".implode("<br>", $messages);

            $message_type = 'failure';

        }

    } catch (Exception $e) {

        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());

        $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';

        $message_type = 'failure';

    }

}



$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);



$importForm -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);

$importForm -> setRequiredNote(_REQUIREDNOTE);

$importForm -> accept($renderer);

$smarty -> assign('T_IMPORT_USERS_FORM', $renderer -> toArray());



$exportForm = new HTML_QuickForm("export_users_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=import_export&oper=export_users&tab=export", "", null, true);

$exportForm -> addElement('radio', 'export_users', _KEEPEXISTINGUSERS, null, 'csvA');

$exportForm -> addElement('radio', 'export_users', _KEEPEXISTINGUSERS, null, 'csvB');

$exportForm -> setDefaults(array('export_users' => 0));

$exportForm -> addElement('submit', 'submit_export_users', _EXPORTUSERSDATA, 'class = "flatButton"');



if ($exportForm -> isSubmitted() && $exportForm -> validate()) {

    $exportForm -> exportValue('export_users') == 'csvA' ? $separator = ',' : $separator = ';';

    try {

        $file = EfrontSystem :: exportUsers($separator);

        header("content-type:".$file['mime_type']);

        header('content-disposition: attachment; filename= "'.($file['name']).'"');

        readfile($file['path']);

        exit;

    } catch (Exception $e) {

        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());

        $message      = _ERRORRESTORINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';

        $message_type = 'failure';

    }

    //$smarty -> assign("T_EXPORTED_FILE", $file);

}

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

$exportForm -> accept($renderer);



$smarty -> assign('T_EXPORT_USERS_FORM', $renderer -> toArray());





#ifdef ENTERPRISE

    // The HCD related data import/exports

     

    if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {

        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");

    }



    $hcdImportForm = new HTML_QuickForm("import_hcd_data_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=import_export&oper=import&tab=import_data", "", null, true);

    $hcdImportForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter



    $hcdImportForm -> addElement('file', 'hcd_file', _DATAFILE, 'class = "inputText"');

    $hcdImportForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);            //getUploadMaxSize returns size in KB

    $hcdImportForm -> addRule('file', _YOUMUSTUPLOADFILE, 'uploadedfile', null, 'client');



    $hcdImportForm -> addElement('select', 'category' , null, array("branch" => _BRANCHES, "jobs" => _JOBDESCRIPTIONS, "skills" => _SKILLS),'class = "inputText" onChange="changeCategory(this)"');



    $hcdImportForm -> addElement('radio', 'hcd_replace_users', _KEEPEXISTINGDATA, null, 'keep');

    $hcdImportForm -> addElement('radio', 'hcd_replace_users', _REPLACEEXISTINGDATA, null, 'replace');



    $hcdImportForm -> addElement('submit', 'submit_hcd_import', _IMPORTDATA, 'class="flatButton"');



    $hcdImportForm -> setDefaults(array('hcd_replace_users' => 0));



    $admin = '"'.$_SESSION['s_login'].'"';

    $branchTable = array("name", "address", "city", "country", "telephone", "email","father_name");

    $smarty -> assign("T_BRANCH_FIELDS", $branchTable);





    $jobsTable = array("job_name","job_role_description", "employees_needed", "branch_name");

    $smarty -> assign("T_JOBS_FIELDS", $jobsTable);



    $skillTable = array("description","skill_category_description");

    $smarty -> assign("T_SKILL_FIELDS", $skillTable);



    if (isset($_GET['csv_sample'])) {

        header("content-type:text/plain");

        header('content-disposition: attachment; filename= "csv_sample.txt"');



        if ($_GET['csv_sample'] == "2") {

            echo implode(";", $branchTable) . "\r\n";

            echo "Central Branch;Park Avenue 123;New York;USA;+1 555 124543;central_branch@efront.us;\r\n";

            echo "Child Branch;Park Avenue 124;New York;USA;+1 555 124544;child_branch@efront.us;Central Branch\r\n";

        } else if ($_GET['csv_sample'] == "3") {

            echo implode(";", $jobsTable) . "\r\n";

            echo "Lead software Developer;Programs in C++ and Java;2;Central Branch\r\n";

            echo "Manager;Is responsible for the branch;1;\r\n";

        } else {

            echo implode(";", $skillTable) . "\r\n";

            echo "PHP programming;Programming Languages\r\n";

            echo "Java;Programming Languages\r\n";

            echo "English;Languages\r\n";

            echo "French;Languages\r\n";

        }

        exit;

    }



    if ($hcdImportForm -> isSubmitted() && $hcdImportForm -> validate()) {

        try {

            if (!is_dir($currentUser -> user['directory']."/temp")) {

                mkdir($currentUser -> user['directory']."/temp", 0755);

            }

            ($hcdImportForm -> exportValue('hcd_replace_users') == "replace") ? $replaceData = true : $replaceData = false;



            $filesystem   = new FileSystemTree($currentUser -> user['directory']."/temp");

            $uploadedFile = $filesystem -> uploadFile('hcd_file');



            if ($hcdImportForm -> exportValue('category') == "branch") {

                list($newUsers, $messages) = EfrontBranch :: import($uploadedFile, $replaceData);

                $message = _TOTALINSERTED.' '.sizeof($newUsers).' '._BRANCHES;

                $message_type = 'success';

                if (sizeof($messages) > 0) {

                    $message     .= '. '._THEFOLLOWINGBRANCHESCOULDNOTBEIMPORTED.":<br>".implode("<br>", $messages);

                    $message_type = 'failure';

                }

            } else if ($hcdImportForm -> exportValue('category') == "jobs") {

                list($newUsers, $messages) = EfrontJob :: import($uploadedFile, $replaceData);

                $message = _TOTALINSERTED.' '.sizeof($newUsers).' '._JOBDESCRIPTIONS;

                $message_type = 'success';

                if (sizeof($messages) > 0) {

                    $message     .= '. '._THEFOLLOWINGJOBSCOULDNOTBEIMPORTED.":<br>".implode("<br>", $messages);

                    $message_type = 'failure';

                }

            } else {

                list($newUsers, $messages) = EfrontSkill :: import($uploadedFile, $replaceData);

                $message = _TOTALINSERTED.' '.sizeof($newUsers).' '._SKILLS;

                $message_type = 'success';

                if (sizeof($messages) > 0) {

                    $message     .= '. '._THEFOLLOWINGSKILLSCOULDNOTBEIMPORTED.":<br>".implode("<br>", $messages);

                    $message_type = 'failure';

                }

            }







        } catch (Exception $e) {

            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());

            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';

            $message_type = 'failure';

        }

    }



    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);



    $hcdImportForm -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);

    $hcdImportForm -> setRequiredNote(_REQUIREDNOTE);

    $hcdImportForm -> accept($renderer);

    $smarty -> assign('T_HCD_IMPORT_FORM', $renderer -> toArray());



    $hcdExportForm = new HTML_QuickForm("export_hcd_data_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=import_export&oper=export_users&tab=export_data", "", null, true);

    $hcdExportForm -> addElement('select', 'export_category' , null, array("branch" => _BRANCHES, "jobs" => _JOBDESCRIPTIONS, "skills" => _SKILLS),'class = "inputText" ');



    $hcdExportForm -> addElement('radio', 'export_hcd_users', _KEEPEXISTINGUSERS, null, 'csvA');

    $hcdExportForm -> addElement('radio', 'export_hcd_users', _KEEPEXISTINGUSERS, null, 'csvB');

    $hcdExportForm -> setDefaults(array('export_hcd_users' => 0));





    $hcdExportForm -> addElement('submit', 'submit_export_hcd_users', _EXPORTDATA, 'class = "flatButton"');



    if ($hcdExportForm -> isSubmitted() && $hcdExportForm -> validate()) {

        $hcdExportForm -> exportValue('export_users') == 'csvA' ? $separator = ',' : $separator = ';';

        try {



            if ($hcdExportForm -> exportValue('export_category') == "branch") {

                $file = EfrontBranch :: export($separator);

            } else if ($hcdExportForm -> exportValue('export_category') == "jobs") {

                $file = EfrontJob :: export($separator);

            } else {

                $file = EfrontSkill :: export($separator);

            }

            header("content-type:".$file['mime_type']);

            header('content-disposition: attachment; filename= "'.($file['name']).'"');

            readfile($file['path']);

            exit;

        } catch (Exception $e) {

            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());

            $message      = _ERRORRESTORINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';

            $message_type = 'failure';

        }

        //$smarty -> assign("T_EXPORTED_FILE", $file);

    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $hcdExportForm -> accept($renderer);



    $smarty -> assign('T_HCD_EXPORT_FORM', $renderer -> toArray());







#endif

*/
?>
