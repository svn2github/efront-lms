<?php
/**

 * Browses directory contents

 *

 * This file is used by the editor, to display a lesson's folder contents

 * @package eFront

 * @version 1.0

 */
//General initializations and parameters
session_cache_limiter('none');
session_start();
$path = "../../libraries/";
/** The configuration file.*/
include_once $path."configuration.php";
//Access is not allowed to users that are not logged in
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        eF_redirect("index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    eF_redirect("index.php?message=".urlencode(_YOUCANNOTACCESSTHISPAGE)."&message_type=failure");
    exit;
}

$loadScripts = array('scriptaculous/prototype', 'EfrontScripts', 'drag-drop-folder-tree');

$contentTree = new EfrontContentTree($_SESSION['s_lessons_ID']);
$visitableIterator = new EfrontVisitableAndEmptyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($contentTree -> tree), RecursiveIteratorIterator :: SELF_FIRST)));
$html = $contentTree -> toHTML($visitableIterator, false, array('noclick' => true, 'onclick' => 'setLink(this)'));

$mainScripts = getMainScripts();
$smarty -> assign("T_HEADER_MAIN_SCRIPTS", implode(",", $mainScripts));


$smarty -> assign("T_CONTENT_TREE", $html);


$smarty -> display("browsecontent.tpl");

?>
