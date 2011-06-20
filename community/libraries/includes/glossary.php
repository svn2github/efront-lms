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
$entityName = 'glossary';
if ($GLOBALS['configuration']['disable_shared_glossary'] != 1) {
 $glossary = eF_getTableData("glossary", "id,name,info", "lessons_ID=".$currentLesson -> lesson['id']." OR lessons_ID=0");
} else {
 $glossary = eF_getTableData("glossary", "id,name,info", "lessons_ID=".$currentLesson -> lesson['id']);
}
foreach ($glossary as $value) {
    $legalValues[] = $value['id'];
}

$words = glossary :: getGlossaryWords($glossary);
$smarty -> assign("T_GLOSSARY", $words);


include("entity.php");
