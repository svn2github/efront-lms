<?php
/**
* Add glossary terms.
*
* This file is used to add and edit glossary terms
* @package eFront
* @version 1.0
*/

session_cache_limiter('none');
session_start();

$path = "../libraries/";

include_once $path."configuration.php";

//error_reporting(E_ALL);
//pr($_GET);pr($_POST);

if (eF_checkUser($_SESSION['s_login'], $_SESSION['s_password'], $_SESSION['s_lessons_ID']) != "professor") {
    header("location:index.php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    exit;
}
try {
    $currentUser = EfrontUserFactory :: factory($_SESSION['s_login'], false, 'professor');
    
    if (isset($currentUser -> coreAccess['glossary']) && $currentUser -> coreAccess['glossary'] != 'change') {
        throw new Exception();
    }
} catch (Exception $e) {
    header("location:index.php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    exit;
}


if (isset($_GET['postAjaxRequest']) && $_GET['add'] == 1 && isset($_GET['term']) && isset($_GET['definition'])) {
    $fields = array("name"       => addslashes($_GET['term']),
                    "info"       => $_GET['definition'],
                    "lessons_ID" => $_SESSION['s_lessons_ID']);

    if (preg_match('/^\d.*$/', $fields['name'])) {
        $tab = '0-9';
    } elseif (preg_match('/^\w.*$/', $fields['name'], $matches)) {
        $tab = mb_substr($fields['name'], 0, 1);
    } else {
        $tab = 'Symbols';
    }

    eF_insertTableData("glossary_words", $fields);
    exit;
}

if (isset($_GET['delete']) && eF_checkParameter($_GET['delete'], 'id')) {
    eF_deleteTableData("glossary_words", "id=".$_GET['delete']);
    $message      = _SUCCESFULLYDELETEDDEFINITION;
    $message_type = 'success';
} elseif (isset($_GET['add']) || (isset($_GET['update']) && eF_checkParameter($_GET['update'], 'id'))) {
    $load_editor = true;

    if (isset($_GET['update'])) {
        $form = new HTML_QuickForm("change_glossary_form", "post", basename($_SERVER['PHP_SELF'])."?update=".$_GET['update'], "", null, true);
    } else {
        $form = new HTML_QuickForm("add_glossary_form", "post", basename($_SERVER['PHP_SELF'])."?add=1", "", null, true);
    }
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter

    $form -> addElement('text', 'term', _TERM, 'id="termField" class = "inputText"');
    $form -> addRule('term', _THEFIELD.' '._TERM.' '._ISMANDATORY, 'required');

    $form -> addElement('textarea', 'definition', _DEFINITION, 'class = "simpleEditor inputTextarea"');
    $form -> addElement('submit', 'submit_term', _SUBMITTERM, 'class = "flatButton"');

    if (isset($_GET['add'])) {
        $form -> addElement('button', 'submit_term_add_another', _SUBMITANDADDANOTHER, 'class = "flatButton" onclick="ajaxPostTerm(this);"'); //else {document.forms[0].target = \'_self\';}"');
    }
    if (isset($_GET['update'])) {
        $result = eF_getTableData("glossary_words", "name,info", "id=".$_GET['update']);
        $form -> setDefaults(array("term" => $result[0]['name'], "definition" => $result[0]['info']));
    }

    if ($form -> isSubmitted() && $form -> validate()) {
        $fields = array("name"       => addslashes($form -> exportValue('term')),
                        "info"       => $form -> exportValue('definition'),
                        "lessons_ID" => $_SESSION['s_lessons_ID']);

        if (preg_match('/^\d.*$/', $fields['name'])) {
            $tab = '0-9';
        } elseif (preg_match('/^\w.*$/', $fields['name'], $matches)) {
            $tab = mb_substr($fields['name'], 0, 1);
        } else {
            $tab = 'Symbols';
        }

        if (isset($_GET['add'])) {
            if (eF_insertTableData("glossary_words", $fields)) {
                $message      = _SUCCESFULLYADDEDDEFINITION;
                $message_type = 'success';
            } else {
                $message      = _DEFINITIONCOULDNOTBEINSERTED;
                $message_type = 'failure';
            }
        } else {
            if (eF_updateTableData("glossary_words", $fields, "id=".$_GET['update'])) {
                $message      = _SUCCESFULLYUPDATEDDEFINITION;
                $message_type = 'success';
            } else {
                $message      = _DEFINITIONCOULDNOTBEUPDATED;
                $message_type = 'failure';
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $renderer->setRequiredTemplate(
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}'
        );
    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_GLOSSARY_FORM', $renderer -> toArray());

}
$loadScripts = array('EfrontScripts', 'scriptaculous/prototype');
$loadScripts[] = 'scriptaculous/scriptaculous';
$loadScripts[] = 'scriptaculous/effects';
$loadScripts[] = 'ajax';

isset($tab) ? $smarty -> assign("T_TAB", $tab) : $smarty -> assign("T_TAB", $_GET['tab']);
$smarty -> assign("T_RELOAD_FATHER_FRAME", 1);

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", $loadScripts);
$smarty -> assign("T_HEADER_EDITOR", $load_editor);
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> display('add_definition.tpl');
?>