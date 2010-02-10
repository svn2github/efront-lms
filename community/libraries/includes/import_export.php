<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}
//include "module_importExportUsers.php";
$importForm = new HTML_QuickForm("import_users_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=import_export&oper=import_users", "", null, true);
$importForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter

//$fileUpload = & HTML_QuickForm :: createElement('file', 'users_file', _DATAFILE, 'class = "inputText"');
//$importForm -> addElement($fileUpload);
$importForm -> addElement('file', 'users_file', _DATAFILE, 'class = "inputText"');
$importForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024); //getUploadMaxSize returns size in KB
$importForm -> addRule('users_file', _YOUMUSTUPLOADFILE, 'uploadedfile', null, 'client');

$importForm -> addElement('radio', 'replace_users', _KEEPEXISTINGUSERS, null, 'keep');
$importForm -> addElement('radio', 'replace_users', _KEEPEXISTINGUSERS, null, 'keep');
$importForm -> addElement('radio', 'replace_users', _REPLACEEXISTINGUSERS, null, 'replace');
$importForm -> addElement('checkbox', 'send_email', _SENDINFOVIAEMAIL);
$importForm -> addElement('submit', 'submit_import_users', _IMPORTUSERSDATA, 'class="flatButton"');
$importForm -> setDefaults(array('replace_users' => 0));
//$form_sendEmail = & HTML_QuickForm :: createElement('checkbox', 'send_email', _SENDINFOVIAEMAIL, null, null);
//$importForm -> addElement($form_sendEmail);
$admin = '"'.$_SESSION['s_login'].'"';
$usersTable = eF_getTableData("users", "*", "");
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
        $filesystem = new FileSystemTree($currentUser -> user['directory']."/temp");
        $uploadedFile = $filesystem -> uploadFile('users_file');
        list($newUsers, $messages) = EfrontSystem :: importUsers($uploadedFile, $replaceUsers);
        //pr($newUsers);
        foreach ($newUsers as $key => $value) {
            //pr($value);
            if ($importForm -> exportValue('send_email')) {
                $subject = _ACCOUNTACTIVATIONMAILSUBJECT;
                $from = $configuration['system_email'];
                $to = $value -> user['email'];
                $body = _THISEMAIL.'<br><br>'._CONTAINSINFORMATIONABOUTYOURACCOUNTINTHEPLATFORM.' '._EFRONTNAME.'<br><br>'._LOGIN.':'.$value -> user['login'].'<br><br>'._PASSWORD.':'.$value -> user['password'].'<br><br>'._THANKYOU;
                eF_mail($from, $to, $subject, $body);
            }
            // If we are in HCD, then the $newUsers is an array of eFront Employee Objects
            if ($jobId) {
                $employee = $value -> aspects['hcd'];
                $employee -> addJob($value, $jobId, $branchID, 0, _NOSPECIFICJOB);
            }
        }
        $message = _TOTALINSERTED.' '.sizeof($newUsers).' '._USERS;
        $message_type = 'success';
        if (sizeof($messages) > 0) {
            $message .= '. '._THEFOLLOWINGUSERSCOULDNOTBEIMPORTED.":<br>".implode("<br>", $messages);
            $message_type = 'failure';
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
        $message = _ERRORRESTORINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
    //$smarty -> assign("T_EXPORTED_FILE", $file);
}
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$exportForm -> accept($renderer);
$smarty -> assign('T_EXPORT_USERS_FORM', $renderer -> toArray());
?>
