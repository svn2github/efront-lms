<?php
/**

 * 

 */
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
if ($GLOBALS['configuration']['disable_comments'] == 1 || (isset($currentUser -> coreAccess['comments']) && $currentUser -> coreAccess['comments'] == 'hidden') || (isset($currentLesson -> options['comments']) && !$currentLesson -> options['comments'])) {
    eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}
//Create shorthands for user access rights, to avoid long variable names
!isset($currentUser -> coreAccess['comments']) || $currentUser -> coreAccess['comments'] == 'change' ? $_change_ = 1 : $_change_ = 0;

$load_editor = true;
if ($_professor_) {
    $comments = comments::getComments($currentLesson -> lesson['id'], false, $currentUnit['id'], false, false);
} else {
    $comments = comments::getComments($currentLesson -> lesson['id'], $GLOBALS['currentUser'], $currentUnit['id'], false, false);
}

//An array of legal ids for editing entries
$legalValues = array();
foreach ($comments as $value) {
    //if ($value['users_LOGIN'] == $GLOBALS['currentUser'] -> user['login'] || $_professor_) {
        $legalValues[] = $value['id'];
    //}
}



//Theses values will be used for the new comment
$values = array('content_ID' => $currentUnit['id'], 'users_LOGIN' => $currentUser -> user['login']);
$entityForm = new HTML_QuickForm("create_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=comments&view_unit=".$currentUnit['id'].(isset($_GET['add']) ? '&add=1' : '&edit='.$_GET['edit']), "", null, true);

$entityName = 'comments';

include("entity.php");
?>
