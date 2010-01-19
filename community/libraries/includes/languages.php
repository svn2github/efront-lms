<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

//pr($languages);
$loadScripts[] = 'includes/languages';
if (isset($currentUser -> coreAccess['languages']) && $currentUser -> coreAccess['languages'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}
$languages = EfrontSystem :: getLanguages();
if (isset($_GET['delete_language']) && eF_checkParameter($_GET['delete_language'], 'file') && in_array($_GET['delete_language'], array_keys($languages)) && $_GET['delete_language'] != 'english') {
    if (isset($currentUser -> coreAccess['languages']) && $currentUser -> coreAccess['languages'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    try {
        $file = new EfrontFile(G_ROOTPATH.'/libraries/language/lang-'.$_GET['delete_language'].'.php.inc');
        $file -> delete();
        eF_deleteTableData("languages", "name='".$_GET['delete_language']."'");
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo $e -> getMessage().' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['deactivate_language']) && eF_checkParameter($_GET['deactivate_language'], 'file') && in_array($_GET['deactivate_language'], array_keys($languages))) {
    if (isset($currentUser -> coreAccess['languages']) && $currentUser -> coreAccess['languages'] != 'change') {
        echo urlencode(_UNAUTHORIZEDACCESS);
        exit;
    }
    //Although db operations do not support exceptions (yet), we leave this here for future support
    try {
        eF_updateTableData("languages", array("active" => 0), "name='".$_GET['deactivate_language']."'");
        echo "0";
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo $e -> getMessage().' ('.$e -> getCode().')';
    }
    exit;
} elseif (isset($_GET['activate_language']) && eF_checkParameter($_GET['activate_language'], 'file') && in_array($_GET['activate_language'], array_keys($languages))) {
    if (isset($currentUser -> coreAccess['languages']) && $currentUser -> coreAccess['languages'] != 'change') {
        echo urlencode(_UNAUTHORIZEDACCESS);
        exit;
    }
    //Although db operations do not support exceptions (yet), we leave this here for future support
    try {
        eF_updateTableData("languages", array("active" => 1), "name='".$_GET['activate_language']."'");
        echo "1";
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo $e -> getMessage().' ('.$e -> getCode().')';
    }
    exit;
}
if (!isset($currentUser -> coreAccess['languages']) || $currentUser -> coreAccess['languages'] == 'change') {
    $createForm = new HTML_QuickForm("create_language_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=languages', "", null, true);
    $createForm -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
    $createForm -> addElement('text', 'english_name', _ENGLISHNAME, 'class = "inputText" id = "language_name"');
    $createForm -> addElement('text', 'translation', _TRANSLATION, 'class = "inputText" id = "language_translation"');
    $createForm -> addElement("advcheckbox", "rtl", _RTLLANGUAGE, null, 'class = "inputCheckBox" id = "language_rtl"', array(0, 1));
    $createForm -> addElement('file', 'language_upload', _FILENAME, 'class = "inputText"');

    $createForm -> addElement('hidden', 'selected_language', null, 'id = "selected_language"');
    $createForm -> addElement('submit', 'submit_upload_language', _SUBMIT, 'class = "flatButton"');
    $createForm -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);            //getUploadMaxSize returns size in KB
    $createForm -> addRule('english_name', _THEFIELD.' "'._ENGLISHNAME.'" '._ISMANDATORY, 'required', null, 'client');
    $createForm -> addRule('english_name', _INVALIDFIELDDATA.': '._ENGLISHNAME, 'checkParameter', 'file');
    $createForm -> addRule('translation', _THEFIELD.' "'._TRANSLATION.'" '._ISMANDATORY, 'required', null, 'client');
    //$createForm -> addRule('language_upload', _THEFIELD.' "'._FILENAME.'" '._ISMANDATORY, 'required', null, 'client');

    if ($createForm -> isSubmitted() && $createForm -> validate()) {
        $values = $createForm -> exportValues();
        try {
            if ($values['selected_language']) {
                if ($_FILES['language_upload']['error'] == 0) {
                    $filesystem   =  new FileSystemTree(G_ROOTPATH.'libraries/language');
                    $uploadedFile = $filesystem -> uploadFile('language_upload', G_ROOTPATH.'libraries/language');
                    $uploadedFile -> rename(dirname($uploadedFile['path']).'/lang-'.$values['english_name'].'.php.inc', true);
                }
                $fields = array("name"        => $values['english_name'],
                                        "translation" => $values['translation'],
                                        "rtl"         => $values['rtl']);
                eF_updateTableData("languages", $fields, "name='".$values['selected_language']."'");
                //include "editor/tiny_mce/langs/language.php";
                $RetValues = file(G_SERVERNAME."/editor/tiny_mce/langs/language.php?langname=".$values['english_name']);
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=languages&message=".urlencode(_SUCCESSFULLYUPDATEDLANGUAGE)."&message_type=success");
            } else {
                if ($_FILES['language_upload']['error'] == 0) {
                    $filesystem   =  new FileSystemTree(G_ROOTPATH.'libraries/language');
                    $uploadedFile = $filesystem -> uploadFile('language_upload', G_ROOTPATH.'libraries/language');
                    if ($uploadedFile['extension'] == "zip") {
                        $lang_zip_file_temp = new EfrontFile($uploadedFile['path']);
                        $lang_zip_file      = $lang_zip_file_temp -> uncompress(false);
                        $lang_file_rename   = new EfrontFile($lang_zip_file[0]);
                        $lang_file_rename -> rename(dirname($uploadedFile['path']).'/lang-'.$values['english_name'].'.php.inc', true);
                    } else {
                        $uploadedFile -> rename(dirname($uploadedFile['path']).'/lang-'.$values['english_name'].'.php.inc', true);
                    }
                } else {
                    $file = new EfrontFile(G_ROOTPATH.'libraries/language/lang-english.php.inc');
                    $file -> copy(G_ROOTPATH.'libraries/language/lang-'.$values['english_name'].'.php.inc');
                }
                $fields = array("name"        => $values['english_name'],
                                        "translation" => $values['translation'],
                                        "active"      => 1,
                                        "rtl"         => $values['rtl']);
                eF_insertTableData("languages", $fields);
                $RetValues = file(G_SERVERNAME."/editor/tiny_mce/langs/language.php?langname=".$values['english_name']);
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=languages&message=".urlencode(_SUCCESSFULLYADDEDLANGUAGE)."&message_type=success");
            }
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $renderer -> setRequiredTemplate (
                   '{$html}{if $required}
                        &nbsp;<span class = "formRequired">*</span>
                    {/if}');

    $createForm -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
    $createForm -> setRequiredNote(_REQUIREDNOTE);
    $createForm -> accept($renderer);
    $smarty -> assign("T_CREATE_LANGUAGE_FORM", $renderer -> toArray());
    $smarty -> assign("T_MAX_FILE_SIZE", FileSystemTree :: getUploadMaxSize());

    $dataSource = $languages;
    $tableName  = 'languagesTable';
    /**Handle sorted table's sorting and filtering*/
    include("sorted_table.php");

}

$smarty -> assign("T_LANGUAGES", $languages);
?>