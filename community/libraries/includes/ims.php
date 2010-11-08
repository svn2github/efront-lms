<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$loadScripts[] = "includes/ims";
if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
    $options = array(array('image' => '16x16/import.png', 'title' => _IMSIMPORT, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=ims&ims_import=1', 'selected' => $_GET['ims_export'] ? false : true));
    //array('image' => '16x16/export.png', 'title' => _IMSEXPORT, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=ims&ims_export=1', 'selected' => !$_GET['ims_export'] ? false : true));
} else {
    $options = array(array('image' => '16x16/import.png', 'title' => _IMSIMPORT, 'link' => basename($_SERVER['PHP_SELF']).'?ctg=ims&ims_import=1', 'selected' => $_GET['ims_export'] ? false : true));
}

$smarty -> assign("T_TABLE_OPTIONS", $options);
 if (!$_GET['ims_export']) {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }

    try {
        $smarty -> assign("T_MAX_FILE_SIZE", FileSystemTree :: getUploadMaxSize());
        $maxUploads = 100;

        $form = new HTML_QuickForm("upload_ims_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=ims&ims_import=1', "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter

  $form -> addElement('file', 'ims_file[0]', _UPLOADTHEIMSFILEINZIPFORMAT);
  for ($i = 1; $i < $maxUploads; $i++) {
      $form -> addElement('file', "ims_file[$i]", null);
  }

        $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);

        $form -> addElement('text', 'url_upload', _UPLOADFILEFROMURL, 'class = "inputText"');
        $form -> addElement('select', 'embed_type', _EMBEDTYPE, array('iframe' => _INLINEIFRAME, 'popup'=> _NEWWINDOWPOPUP), 'class = "inputSelect"');
        $form -> addElement('text', 'popup_parameters', _POPUPPARAMETERS, 'class = "inputText" style = "width:600px"');
        $form -> addElement('submit', 'submit_upload_ims', _SUBMIT, 'class = "flatButton"');

        $form -> setDefaults(array('popup_parameters' => 'width=800,height=600,scrollbars=no,resizable=yes,status=yes,toolbar=no,location=no,menubar=no'));

        //@todo: url upload, if not exists, report a human-readable error!
        $timestamp = time();

        if ($form -> isSubmitted() && $form -> validate()) {
            $values = $form -> exportValues();
            try {
                $urlUpload = $form -> exportValue('url_upload');

                $imsFiles = array();
                if ($urlUpload != "" ) {
                    FileSystemTree :: checkFile($urlUpload);
                    $urlArray = explode("/", $urlUpload);
                    $urlFile = urldecode($urlArray[sizeof($urlArray) - 1]);

                    if (!copy($urlUpload, $currentLesson -> getDirectory().$urlFile)) {
                        throw new Exception(_PROBLEMUPLOADINGFILE);
                    } else {
                        $imsFiles[] = new EfrontFile($currentLesson -> getDirectory().$urlFile);
                    }
                } else {
                    $filesystem = new FileSystemTree($currentLesson -> getDirectory(), true);

                    foreach ($_FILES['ims_file']['name'] as $key => $value) {
                        if (!in_array($value, $imsFiles)) { //This way we bypass duplicates
                            try {
                                $imsFiles[$value] = $filesystem -> uploadFile("ims_file", $currentLesson -> getDirectory(), $key);
                            } catch (EfrontFileException $e) {
                                if ($e -> getCode() != UPLOAD_ERR_NO_FILE) {
                                    throw $e;
                                }
                            }
                        }
                    }

                }
                //pr($imsFiles);exit;
                foreach ($imsFiles as $imsFile) {
                    /* Imports ims package to database */
                    $imsFolderName = EfrontFile :: encode(basename($imsFile['name'], '.zip'));
                    $imsPath = $currentLesson -> getDirectory().$imsFolderName.'/';
                    is_dir($imsPath) OR mkdir($imsPath, 0755);
                    //pr($imsPath.$imsFile['name']);
                    //try {
                    $imsFile -> rename($imsPath.$imsFile['name'], true);
                    //} catch (Exception $e) {pr($e);throw $e;}
                    $fileList = $imsFile -> uncompress(false);
                    $imsFile -> delete();

                    $total_fields = array();
                    $resources = array();

                    $manifestFile = new EfrontFile($imsPath.'imsmanifest.xml');
                    EfrontIMS :: import($currentLesson, $manifestFile, $imsFolderName, array('embed_type' => $values['embed_type'], 'popup_parameters' => $values['popup_parameters']));
                }
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=ims&message=".urlencode(_SUCCESSFULLYIMPORTEDIMSFILE)."&message_type=success");
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = failure;
            }

        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $form -> accept($renderer);
        $smarty -> assign('T_UPLOAD_IMS_FORM', $renderer -> toArray());
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = failure;
    }
} else if ($_GET['ims_export']) {
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    $form = new HTML_QuickForm("export_ims_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=ims&ims_export=1', "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
    $form -> addElement('submit', 'submit_export_ims', _EXPORT, 'class = "flatButton"');
    if ($form -> isSubmitted() && $form -> validate()) {
        define ('IMS_FOLDER', G_ROOTPATH."www/content/ims_data");
        if (!is_dir(IMS_FOLDER)) {
            mkdir(IMS_FOLDER, 0755);
        }
        $ims_filename = "ims_lesson".$lessons_id.".zip";

        if (is_file(IMS_FOLDER."/".$ims_filename)) {
            unlink(IMS_FOLDER."/".$ims_filename);
        }

        $lessons_id = $currentLesson -> lesson['id'];

        try {
            $filesystem = new FileSystemTree($currentLesson -> getDirectory());
            foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator($filesystem -> tree, RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
                ($value instanceOf EfrontDirectory) ? $filelist[] = preg_replace("#".$currentLesson -> getDirectory()."#", "", $key).'/' : $filelist[] = preg_replace("#".$currentLesson -> getDirectory()."#", "", $key);
            }

            $lesson_entries = eF_getTableData("content", "id,name,data", "lessons_ID=" . $lessons_id . " and ctg_type!='tests' and active=1");

            require_once("ims_tools.php");
            create_manifest($lessons_id, $lesson_entries, $filelist, IMS_FOLDER);

            $imsDirectory = new EfrontDirectory(IMS_FOLDER ."/lesson". $lessons_id."/");

            $compressedFile = $imsDirectory -> compress(false, false, true);
            $imsDirectory -> delete();

            $smarty -> assign("T_IMS_EXPORT_FILE", $compressedFile);
            $smarty -> assign("T_MESSAGE", _SUCCESSFULLYEXPORTEDIMSFILE);
            $smarty -> assign("T_MESSAGE_TYPE", "success");
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = "failure";
        }
    }
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form -> accept($renderer);

    $smarty -> assign('T_EXPORT_IMS_FORM', $renderer -> toArray());

}
