<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}

$loadScripts[] = 'includes/scheduling';

if (isset($_GET['delete_schedule']) && $_GET['delete_schedule']) {
    try {
        if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'] != 'change') {
            throw new Exception(rawurlencode(_UNAUTHORIZEDACCESS));
        }
        $currentLesson -> lesson['from_timestamp'] = null;
        $currentLesson -> lesson['to_timestamp']   = null;
        $currentLesson -> lesson['shift']          = 0;


        // @TODO maybe proper class internal invalidation
        eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_START . "_" . $lesson -> lesson['id']. "'");
        eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_EXPIRY . "_" . $lesson -> lesson['id']. "'");

        $currentLesson -> persist();
    } catch (Exception $e) {
        header("HTTP/1.0 500");
        echo $e -> getMessage().' ('.$e -> getCode().')';
    }

    exit;
}
$form = new HTML_QuickForm("add_period_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=scheduling&", "", null, true);
$form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');

$form -> addElement('text', 'from', _FROM, 'class = "inputText"');
$form -> addElement('text', 'to',   _TO,   'class = "inputText"');
//$form -> addElement('advcheckbox', 'shift', _SHIFTSCHEDULEFORNEWUSERS, null, 'class = "inputCheckbox"', array(0, 1));

if ($currentLesson -> lesson['from_timestamp']) {
    $smarty -> assign("T_FROM_TIMESTAMP", $currentLesson -> lesson['from_timestamp']);
    $smarty -> assign("T_TO_TIMESTAMP",   $currentLesson -> lesson['to_timestamp']);
} else {
    $smarty -> assign("T_FROM_TIMESTAMP", time());
    $smarty -> assign("T_TO_TIMESTAMP",   mktime(date("H"), date("i"), date("s"), date("m")+1, date("d"), date("Y")));    //One month after
}

if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'	] != 'change') {
    $form -> freeze();
} else {
    $form -> addElement('submit', 'submit_add_period', _SAVECHANGES, 'class = "flatButton"');           //The submit period button

    if ($form -> isSubmitted() && $form -> validate()) {
        $fromTimestamp = mktime($_POST['from_Hour'], $_POST['from_Minute'], 0, $_POST['from_Month'], $_POST['from_Day'], $_POST['from_Year']);
        $toTimestamp   = mktime($_POST['to_Hour'], $_POST['to_Minute'], 0, $_POST['to_Month'],   $_POST['to_Day'],   $_POST['to_Year']);

        if ($fromTimestamp < $toTimestamp) {
            $currentLesson -> lesson['from_timestamp'] = $fromTimestamp;
            $currentLesson -> lesson['to_timestamp']   = $toTimestamp;
            //$currentLesson -> lesson['shift']          = $form -> exportValue('shift') ? 1 : 0;
            $smarty -> assign("T_FROM_TIMESTAMP", $currentLesson -> lesson['from_timestamp']);
            $smarty -> assign("T_TO_TIMESTAMP",   $currentLesson -> lesson['to_timestamp']);

            // Note: the semantics of the following event triggers: these triggerings are used to create future "before event" notifications now
            // For this reason the timestamp is set to the values of the lesson
            eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_START . "_" . $lesson -> lesson['id']. "'");
            eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_EXPIRY . "_" . $lesson -> lesson['id']. "'");

            eF_deleteTableData("events", "lessons_ID = ". $currentLesson -> lesson['id'] . " AND (type = '".EfrontEvent::LESSON_PROGRAMMED_START. "' OR type = '". EfrontEvent::LESSON_PROGRAMMED_EXPIRY. "')");

            $currentLesson -> persist();
            EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_PROGRAMMED_START,  "timestamp" => $fromTimestamp, "lessons_ID" => $currentLesson -> lesson['id'], "lessons_name" => $currentLesson -> lesson['name']));
            EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_PROGRAMMED_EXPIRY, "timestamp" => $toTimestamp, "lessons_ID" => $currentLesson -> lesson['id'], "lessons_name" => $currentLesson -> lesson['name']));
            
            $message      = _OPERATIONCOMPLETEDSUCCESSFULLY;
            $message_type = 'success';
        } else {
            $message      = _ENDDATEMUSTBEBEFORESTARTDATE;
            $message_type = 'failure';
        }
    }
}

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

$form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
$form -> setRequiredNote(_REQUIREDNOTE);
$form -> accept($renderer);

$smarty -> assign('T_ADD_PERIOD_FORM', $renderer -> toArray());

?>