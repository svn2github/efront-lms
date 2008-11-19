<?php
/**
* Add comment
* 
* This file is the page which enables insertion and handling of comments
* @package eFront
* @version 1.0
*/

session_cache_limiter('none');
session_start();

$path = "../libraries/";
 
include_once $path."configuration.php";


if (isset($_GET['op']) && (eF_checkUser($_SESSION['s_login'], $_SESSION['s_password']) == "administrator")) {         //Only a professor may perform operations (insert, change, delete)
    eF_printMessage(_UNPRIVILEGEDATTEMPT);
    exit;
} elseif (!eF_checkUser($_SESSION['s_login'], $_SESSION['s_password'])) {                                       //Any logged-in user may view an announcement
    eF_printMessage("You must login to access this page");
    exit;
}

if (isset($_GET['id'])) {
    $id = eF_checkParameter($_GET['id'], 'uint');
    if ($id === false || !isset($_GET['id'])) {
        eF_printMessage(_INVALIDID);
        exit;
    }
}

if (isset($_GET['content_ID'])) {
    $content_ID = eF_checkParameter($_GET['content_ID'], 'uint');
    if ($content_ID === false || !isset($_GET['content_ID'])) {
        eF_printMessage(_INVALIDID);
        exit;
    }
}

if (isset($_GET['op']) && $_GET['op'] == "delete") {
	if ($_SESSION['s_type'] == 'professor' || sizeof(eF_getTableData("comments", "*", "id=".$_GET['id']." and users_LOGIN='".$_SESSION['s_login']."'")) > 0) { 
	    eF_deleteTableData("comments", "id=".$id);
	    eF_deleteTableData("search_keywords", "foreign_ID=".$id." AND table_name='comments'");                   
	
	    $message      = _COMMENTDELETED;
	    $message_type = 'success';
	}
} elseif(isset($_GET['op']) && ($_GET['op'] == 'insert' || $_GET['op'] == 'change')) {
    $load_editor = true;
    
    if (isset($_GET['op']) && $_GET['op'] == 'change' && isset($id)) {
        $form = new HTML_QuickForm("change_comments_form", "post", basename($_SERVER['PHP_SELF'])."?op=change&id=$id", "", null, true);
    } else {
        $form = new HTML_QuickForm("add_comments_form", "post", basename($_SERVER['PHP_SELF'])."?op=insert&content_ID=$content_ID", "", null, true);
    }
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter

    $form -> addElement('textarea', 'data', _ADDYOURCOMMENT, 'class = "simpleEditor inputTextArea" style="width:40em;height:10em;"');
    $form -> addElement('submit', 'submit_comments', _COMMENTADD, 'class = "flatButton"');

    if (isset($_GET['op']) && $_GET['op'] == 'change' && isset($id)) {
        $comments_content = eF_getTableData("comments", "*", "id=".$id);
        $form -> setDefaults(array('data' => $comments_content[0]['data']));
    }

    if ($form -> isSubmitted()) {
        if ($form -> validate()) {
            if (isset($_GET['op']) && $_GET['op'] == 'change' && isset($id)) {
                $comments_content = array("data"  => $form -> exportValue('data'));

                if (eF_updateTableData("comments", $comments_content, "id=".$id)) {
                    $message      = _SUCCESFULLYUPDATEDCOMMENT;
                    $message_type = 'success';
                } else {
                    $message      = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';
                }
            } elseif (isset($_GET['op']) && $_GET['op'] == 'insert' && isset($content_ID)) {
                $comments_content = array("data"        => $form -> exportValue('data'),
                                          "timestamp"   => time(),
                                          "content_ID"  => $content_ID,
                                          "users_LOGIN" => $_SESSION['s_login'],
                                          "active"      => 1);

                if (eF_insertTableData("comments", $comments_content)) {
                    $message      = _SUCCESFULLYADDEDCOMMENT;
                    $message_type = 'success';
                } else {
                    $message      = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';
                }
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_COMMENTS_FORM', $renderer -> toArray());    
} 

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array());
$smarty -> assign("T_HEADER_EDITOR", $load_editor);
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);

$smarty -> display("add_comment.tpl");


?>
