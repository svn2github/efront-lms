<?php

/**
 *
 * @param HTML_QuickForm $form
 * @param Smarty $smarty
 * @return HTML_QuickForm_Renderer_ArraySmarty 
 */
function mtr_getFormRenderer($form, $smarty) {

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $renderer->setRequiredTemplate('{$html}{if $required}&nbsp;<span class = "formRequired">*</span>{/if}');
    $renderer->setErrorTemplate('{$html}{if $error}<div class = "formError">{$error}</div>{/if}');
    $form->accept($renderer);

    return $renderer;
}

/**
 * Checks whether a given date is valid.
 *
 * @param array $value
 * @return boolean
 */
function mtr_isValidDate($value) {
    return checkdate($value['M'], $value['d'], $value['Y']);
}

/**
 * Checks whether a given date is in the past.
 *
 * @param array $value
 * @return boolean
 */
function mtr_exceedsMaxColumns($fields) {

    $submittedFields = isset($_POST['fields']) ? $_POST['fields'] : array();
    $fieldsCount = sizeof($submittedFields);

    $submittedCourses = isset($_POST['courses']) ? $_POST['courses'] : array();
    $coursesCount = sizeof($submittedCourses);

    $trainingReport = new TrainingReports_Report();
    $trainingReport->setFromTimestamp(mtr_toTimestamp($fields['start_date'], 0, 0));
    $trainingReport->setToTimestamp(mtr_toTimestamp($fields['end_date'], 23, 59));
    $trainingReport->setSeparatedBy($fields['separate_by']);

    $periodsCount = sizeof($trainingReport->getPeriods());
    $columns = $fieldsCount + ($periodsCount * ($coursesCount + 1)) + 1;

    if ($columns <= 256) {
        $result = true;
    } else {
        $result = array('separate_by' => _TRAININGREPORTS_MAXCOLUMNSEXCEEDED);
    }

    return $result;
}

/**
 * Checks whether a given date is in the past.
 *
 * @param array $value
 * @return boolean
 */
function mtr_isStartDateBeforeEndDate($fields) {

    $fromTimestamp = mtr_toTimestamp($fields['start_date'], 0, 0);
    $toTimestamp = mtr_toTimestamp($fields['end_date'], 23, 59);

    if ($fromTimestamp < $toTimestamp) {
        $result = true;
    } else {
        $result = array('start_date' => _TRAININGREPORTS_STARTDATEAFTERENDDATE);
    }

    return $result;
}

/**
 *
 * @param array $a
 * @return int
 */
function mtr_toTimestamp($a, $hours, $minutes) {
    return mktime($hours, $minutes, 0, $a['M'], $a['d'], $a['Y']);
}

?>
