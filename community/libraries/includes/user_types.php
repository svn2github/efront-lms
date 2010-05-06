<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*

 User types is the page that concerns direction administration. Here the administrator can view, add, delete and modify User types

 There are 5 sub options in this page, denoted by an extra link part:

 - &add_user_type=1                       When we are adding a new user_type

 - &delete_user_type=<user_type>          When we want to delete user type <user_type>

 - &edit_user_type=<user_type>            When we want to edit user type <user_type>

 - &deactivate_user_type=<user_type>      When we deactivate user type <user_type>

 - &activate_user_type=<user_type>        When we activate user type <user_type>

 */
    $loadScripts[] = 'includes/user_types';
try {
    if (isset($currentUser -> coreAccess['user_types']) && $currentUser -> coreAccess['user_types'] == 'hidden') {
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    if (isset($_GET['delete_user_type']) && eF_checkParameter($_GET['delete_user_type'], 'id')) {
        if (isset($currentUser -> coreAccess['user_types']) && $currentUser -> coreAccess['user_types'] != 'change') {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        try {
            eF_deleteTableData("user_types", "id='".$_GET['delete_user_type']."'") && eF_updateTableData("users", array("user_types_ID" => 0), "user_types_ID=".$_GET['delete_user_type']);
            $message = _USERTYPEDELETED;
            $message_type = 'success';
        } catch (Exception $e) {
            $message = _USERTYPECOULDNOTBEDELETED;
            header("HTTP/1.0 500 ");
            echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
        }
        exit;
    } elseif (isset($_GET['deactivate_user_type']) && eF_checkParameter($_GET['deactivate_user_type'], 'id')) {
        if (isset($currentUser -> coreAccess['user_types']) && $currentUser -> coreAccess['user_types'] != 'change') {
            echo _UNAUTHORIZEDACCESS;
            exit;
        }
        try {
            eF_updateTableData("user_types", array('active' => 0), "id='".$_GET['deactivate_user_type']."'");
            echo "0";
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo _SOMEPROBLEMEMERGED;
        }
        exit;
    } elseif (isset($_GET['activate_user_type']) && eF_checkParameter($_GET['activate_user_type'], 'id')) {
        if (isset($currentUser -> coreAccess['user_types']) && $currentUser -> coreAccess['user_types'] != 'change') {
            echo _UNAUTHORIZEDACCESS;
            exit;
        }
        try {
            eF_updateTableData("user_types", array('active' => 1), "id='".$_GET['activate_user_type']."'");
            echo "1";
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo _SOMEPROBLEMEMERGED;
        }
        exit;
    } elseif (isset($_GET['add_user_type']) || (isset($_GET['edit_user_type']) && eF_checkParameter($_GET['edit_user_type'], 'text'))) {
        $studentOptions = array("content" => _CONTENT,
                                      "calendar" => _CALENDAR,
                                      "statistics" => _STATISTICS,
                                      "forum" => _FORUM,
                                      "personal_messages" => _PERSONALMESSAGES,
                                      //"surveys"           => _SURVEYS,
                                      "chat" => _CHAT,
                                      "control_panel" => _CONTROLPANEL,
           "dashboard" => _DASHBOARD,
           "social" => _SOCIAL);
  $GLOBALS['configuration']['disable_calendar'] != 1 ? $professorOptions["calendar"] = _CALENDAR : null;
  $GLOBALS['configuration']['disable_surveys'] != 1 ? $professorOptions["surveys"] = _SURVEYS : null;
  $GLOBALS['configuration']['disable_news'] != 1 ? $professorOptions["news"] = _ANNOUNCEMENTS : null;
  $GLOBALS['configuration']['disable_forum'] != 1 ? $professorOptions["forum"] = _FORUM : null;
        $professorOptions = array("settings" => _LESSONOPTIONS,
                                      "users" => _LESSONUSERS,
                                      "content" => _CONTENT,
                                      //"news"              => _ANNOUNCEMENTS,
                                      "files" => _FILES,
                                      "progress" => _USERSPROGRESS,
                                     // "glossary"          => _GLOSSARY,
                                      "calendar" => _CALENDAR,
                                      "statistics" => _STATISTICS,
                                      "forum" => _FORUM,
                                      "personal_messages" => _PERSONALMESSAGES,
                                      //"surveys"           => _SURVEYS,
                                      "chat" => _CHAT,
                                      "control_panel" => _CONTROLPANEL,
           "dashboard" => _DASHBOARD,
           "social" => _SOCIAL);
  $GLOBALS['configuration']['disable_glossary'] != 1 ? $professorOptions["glossary"] = _GLOSSARY : null;
  $GLOBALS['configuration']['disable_calendar'] != 1 ? $professorOptions["calendar"] = _CALENDAR : null;
  $GLOBALS['configuration']['disable_surveys'] != 1 ? $professorOptions["surveys"] = _SURVEYS : null;
  $GLOBALS['configuration']['disable_news'] != 1 ? $professorOptions["news"] = _ANNOUNCEMENTS : null;
  $GLOBALS['configuration']['disable_forum'] != 1 ? $professorOptions["forum"] = _FORUM : null;
        $administratorOptions = array("lessons" => _LESSONS,
                                      "users" => _USERS,
                                      "configuration" => _CONFIGURATIONOPTIONS,
                                      "themes" => _THEMES,
                                      "logout_user" => _LOGOUTUSER,
                                      "user_profile" => _USERPROFILE,
                                      "user_types" => _USERTYPES,
                                      "languages" => _LANGUAGES,
                                      "version_key" => _VERSIONKEY,
                                      "maintenance" => _MAINTENANCE,
                                      "backup" => _BACKUPRESTORE,
                                      "modules" => _MODULES,
                                      "statistics" => _STATISTICS,
                                      "archive" => _ARCHIVE,
                                      "calendar" => _CALENDAR,
                                      //"news"              => _ANNOUNCEMENTS,
                                      "forum" => _FORUM,
                                      "personal_messages" => _PERSONALMESSAGES,
                                      "chat" => _CHAT,
                                      "notifications" => _EMAILDIGESTS,
                                      "control_panel" => _CONTROLPANEL,
           "dashboard" => _DASHBOARD,
           "social" => _SOCIAL);
  $GLOBALS['configuration']['disable_calendar'] != 1 ? $administratorOptions["calendar"] = _CALENDAR : null;
  $GLOBALS['configuration']['disable_news'] != 1 ? $administratorOptions["news"] = _ANNOUNCEMENTS : null;
  $GLOBALS['configuration']['disable_forum'] != 1 ? $administratorOptions["forum"] = _FORUM : null;
        $basicTypes = EfrontUser :: $basicUserTypesTranslations;
        if (isset($_GET['edit_user_type'])) {
            $result = eF_getTableData("user_types", "*", "id='".$_GET['edit_user_type']."'");
            $basicType = $result[0]['basic_user_type'];
        } else if (isset($_GET['basic_type']) && in_array($_GET['basic_type'], array_keys($basicTypes))) {
            $basicType = $_GET['basic_type'];
        } else {
            $basicType = 'student';
        }
        switch($basicType){
            case "administrator":
                $options = $administratorOptions;
                break;
            case "professor":
                $options = $professorOptions;
                break;
            default:
                $options = $studentOptions;
                break;
        }
        isset($_GET['add_user_type']) ? $postTarget = 'add_user_type=1' : $postTarget = "edit_user_type=".$_GET['edit_user_type'];
        $form = new HTML_QuickForm("add_type_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=user_types&".$postTarget."&basic_type=".$basicType, "", null, true);
        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
        //$form -> registerRule('checkNotExist', 'callback', 'eF_checkNotExist');
        $form -> addElement('text', 'name', _TYPENAME, 'class = "inputText"');
        $form -> addRule('name', _THEFIELD.' '._TYPENAME.' '._ISMANDATORY, 'required', null, 'client');
        //$form -> addRule('name', _INVALIDFIELDDATA, 'checkParameter', 'text');
        //$form -> addRule('name', _USERTYPE.' &quot;'.($form -> exportValue('name')).'&quot; '._ALREADYEXISTS, 'checkNotExist', 'user_type');
        $form -> addElement('select', 'basic_user_type', _BASICUSERTYPE, $basicTypes, 'id="basic_user_type" class = "inputSelect" onchange = "location = \'administrator.php?ctg=user_types&'.$postTarget.'&basic_type=\'+this.options[this.selectedIndex].value+\'&name=\'+document.getElementsByName(\'name\')[0].value"');
        foreach ($options as $key => $value){
            $form -> addElement("select", "core_access[$key]", $value, array('change' => _CHANGE, 'view' => _VIEW, 'hidden' => _HIDE));
        }
        $form -> setDefaults(array('basic_user_type' => $basicType, 'name' => $_GET['name']));
        if (isset($_GET['edit_user_type'])) {
            $form -> freeze(array('basic_user_type'));
            $form -> setDefaults(array('name' => $result[0]['name'],
                                       'basic_user_type' => $result[0]['basic_user_type'],
                                       'core_access' => unserialize($result[0]['core_access'])));
            $smarty -> assign("T_USER_TYPE_NAME", $result[0]['name']);
        }
        if ((isset($currentUser -> coreAccess['user_types']) && $currentUser -> coreAccess['user_types'] != 'change') || ($currentUser -> user['user_types_ID'] == $_GET['edit_user_type'])) {
            $form -> freeze();
        } else {
            $form -> addElement('submit', 'submit_type', _SAVE, 'class = "flatButton"');
            if ($form -> isSubmitted() && $form -> validate()) {
                $values = $form -> exportValues();
                $fields = array("name" => $values['name'],
                                "basic_user_type" => $values['basic_user_type'],
                                "core_access" => serialize($values['core_access']));
                if (isset($_GET['edit_user_type'])) {
                    if (eF_updateTableData("user_types", $fields, "id=".$_GET['edit_user_type'])) {
                        $message = _SUCCESFULLYUPDATEDUSERTYPE;
                        $message_type = 'success';
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=user_types&message=".urlencode($message)."&message_type=".$message_type);
                    } else {
                        $message = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                } else {
                    if (eF_insertTableData("user_types", $fields)) {
                        $message = _SUCCESFULLYADDEDUSERTYPE;
                        $message_type = 'success';
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=user_types&message=".urlencode($message)."&message_type=".$message_type);
                    } else {
                        $message = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                }
            }
        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);
        $smarty -> assign('T_USERTYPES_OPTIONS', $options);
        $smarty -> assign('T_USERTYPES_FORM', $renderer -> toArray());
    } else {
        $result = eF_getTableData("user_types", "*");
        $smarty -> assign("T_USERTYPES_DATA", $result);
        $smarty -> assign("T_BASIC_USER_TYPES", EfrontUser :: $basicUserTypesTranslations);
    }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
?>
