<?php
/**

* 

* @package eFront

* @version 3.6.0

*/
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
if ($GLOBALS['configuration']['disable_glossary'] == 1 || (isset($currentUser -> coreAccess['glossary']) && $currentUser -> coreAccess['glossary'] == 'hidden')) {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}
//Create shorthands for user access rights, to avoid long variable names
!isset($currentUser -> coreAccess['glossary']) || $currentUser -> coreAccess['glossary'] == 'change' ? $_change_ = 1 : $_change_ = 0;
$load_editor = true;
$loadScripts[] = 'includes/filemanager';

$entityName = 'glossary';
$glossary = eF_getTableData("glossary", "id,name,info", "lessons_ID=".$currentLesson -> lesson['id']);
foreach ($glossary as $value) {
    $legalValues[] = $value['id'];
}

$words = glossary :: getGlossaryWords($glossary);
$smarty -> assign("T_GLOSSARY", $words);

     //This page has a file manager, so bring it on with the correct options
     $basedir = $currentLesson -> getDirectory();
     //Default options for the file manager
        if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
            $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 0);
        } else {
      $options = array('delete' => false,
                 'edit' => false,
                 'share' => false,
                 'upload' => false,
                 'create_folder' => false,
                 'zip' => false,
                 'lessons_ID' => $currentLesson -> lesson['id'],
                 'metadata' => 0);
        }
        //Default url for the file manager
        $url = basename($_SERVER['PHP_SELF']).'?ctg=content&'.(isset($_GET['edit']) ? 'edit='.$_GET['edit'] : 'add=1');
        $extraFileTools = array(array('image' => 'images/16x16/arrow_right.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
        /**The file manager*/
     include "file_manager.php";

include("entity.php");
