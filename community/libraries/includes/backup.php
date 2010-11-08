<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

        if (isset($currentUser -> coreAccess['backup']) && $currentUser -> coreAccess['backup'] == 'hidden') {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        $basedir = G_BACKUPPATH;
        if (isset($_GET['restore'])) {
            ini_set("memory_limit", "-1");
            try {
                $restoreFile = new EfrontFile($_GET['restore']);
                if (!EfrontSystem :: restore($_GET['restore'], $_GET['force'] ? $_GET['force'] : false)) {
                    $message = _ERRORRESTORINGFILE;
                    $message_type = 'failure';
                } else {
                    $message = _SUCCESFULLYRESTOREDSYSTEM;
                    $message_type = 'success';
                }
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = _ERRORRESTORINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
                if ($e -> getCode() == EfrontSystemException::INCOMPATIBLE_VERSIONS) {
                    $message .= ' - <a href = "javascript:void(0)" onclick = "location=location+\'&force=1\'">Force restore</a>';
                }
            }
        }

        try {
            $url = basename($_SERVER['PHP_SELF']).'?ctg=backup';
            $basedir = G_BACKUPPATH;
            $options = array('zip' => false,
                    'create_folder' => false,
                 'folders' => false);
            if (!isset($currentUser -> coreAccess['backup']) || $currentUser -> coreAccess['backup'] == 'change') {
                $extraFileTools = array(array('image' => 'images/16x16/undo.png',
                         'title' => _RESTORE, 'action' => 'restore'));
            }
            $extraHeaderOptions = array(array('image' => 'images/16x16/go_into.png',
                      'title' => _BACKUP,
                      'action' => 'eF_js_showDivPopup(\''._BACKUP.'\', 0, \'backup_table\')'));
         /**The file manager*/
      include "file_manager.php";
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }

        $backup_form = new HTML_QuickForm("backup_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=backup', "", null, true);
        $backup_form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter

        $backup_form -> addElement('text', 'backupname', null, 'class = "inputText"');
        $backup_form -> addRule('backupname', _THEFIELD.' '._FILENAME.' '._ISMANDATORY, 'required', null, 'client');
        $backup_form -> setDefaults(array("backupname" => "backup_".date('Y_m_d_h.i.s', time())));

        $backup_form -> addElement('select', 'backuptype', null, array ("0" => _DATABASEONLY, "1" => _ALLDATABACKUP));
        $backup_form -> addElement('submit', 'submit_backup', _TAKEBACKUP, 'class = "flatButton" onclick = "$(\'backup_image\').show();"');

        if ($backup_form -> isSubmitted() && $backup_form -> validate()) {
            $values = $backup_form -> exportValues();

            try {
                $backupFile = EfrontSystem :: backup($values['backupname'].'.zip', $values['backuptype']);
                eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=backup&message=".urlencode(_SUCCESFULLYBACKEDUP)."&message_type=success");
            } catch (EfrontFileException $e) {
             $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
             $message = $e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
             $message_type = failure;
            }
        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $backup_form -> accept($renderer);
        $smarty -> assign('T_BACKUP_FORM', $renderer -> toArray());
