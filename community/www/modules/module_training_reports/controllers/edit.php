<?php

require_once($this->moduleBaseDir . '/lib/utilities.php');
require_once($this->moduleBaseDir . '/lib/TrainingReports_Report.php');

if (TrainingReports_Report::isValid($this->id) == false) {
    eF_redirect($this->moduleBaseUrl . '&cat=view');
}

/* Get report */
$trainingReport = new TrainingReports_Report($this->id);
$report = $trainingReport->getReport();
$form = mtr_getReportsForm($this->moduleBaseUrl . '&cat=edit&id=' . $this->id, $trainingReport);

if ($form->isSubmitted()) {

    /* Store selected fields to db */
    $submittedFields = isset($_POST['fields']) ? $_POST['fields'] : array();

    $fields = array();
    foreach ($submittedFields as $index => $field) {
        $fields[] = array(
            'reports_ID' => $this->id,
            'name' => $field,
            'position' => $index);
    }

    eF_deleteTableData('module_time_reports_fields', 'reports_ID=' . $this->id);
    eF_insertTableDataMultiple('module_time_reports_fields', $fields);

    /* Store selected courses to db */
    $submittedCourses = isset($_POST['courses']) ? $_POST['courses'] : array();

    $courses = array();
    foreach ($submittedCourses as $index => $course) {
        $courses[] = array(
            'reports_ID' => $this->id,
            'courses_ID' => $course,
            'position' => $index);
    }

    eF_deleteTableData('module_time_reports_courses', 'reports_ID=' . $this->id);
    eF_insertTableDataMultiple('module_time_reports_courses', $courses);
    $trainingReport = new TrainingReports_Report($this->id);
    if ($form->validate()) {
        $report['name'] = $form->exportValue('name');
        $report['from_date'] = mtr_toTimestamp($form->exportValue('start_date'), 0, 0);
        $report['to_date'] = mtr_toTimestamp($form->exportValue('end_date'), 23, 59);
        $report['separated_by'] = $form->exportValue('separate_by');
        eF_updateTableData('module_time_reports', $report, 'id=' . $this->id);
        eF_redirect($this->moduleBaseUrl . '&cat=view&id=' . $this->id . '&message_type=success&message=' . rawurlencode(_TRAININGREPORTS_SUCCESSFULLYSAVED));
    }
}
$renderer = mtr_getFormRenderer($form, $this->smarty);
$this->smarty->assign('T_TRAININGREPORTS_REPORT', $trainingReport->getReport());
$this->smarty->assign('T_TRAININGREPORTS_FIELDS', $trainingReport->getFieldsOptions());
$this->smarty->assign('T_TRAININGREPORTS_SELECTEDFIELDS', $trainingReport->getFields());
$this->smarty->assign('T_TRAININGREPORTS_COURSES', $trainingReport->getCoursesOptions());
$this->smarty->assign('T_TRAININGREPORTS_SELECTEDCOURSES', $trainingReport->getCourses());
$this->smarty->assign('T_TRAININGREPORTS_FORM', $renderer->toArray());
/**
 *
 * @param string $url
 * @param TrainingReports_Report $trainingReport
 * @return HTML_QuickForm
 */
function mtr_getReportsForm($url, $trainingReport) {
    $period_options = $trainingReport->getPeriodsOptions();
    $defaults = array(
        'name' => $trainingReport->getName(),
        'start_date' => $trainingReport->getFromTimestamp(),
        'end_date' => $trainingReport->getToTimestamp(),
        'separate_by' => $trainingReport->getSeparatedBy());
    $form = new HTML_QuickForm('mtr_form', 'post', $url, '', null, true);
    $form->registerRule('isValidDate', 'callback', 'mtr_isValidDate');
    $form->addFormRule('mtr_isStartDateBeforeEndDate');
    $form->addFormRule('mtr_exceedsMaxColumns');
    $form->addElement('text', 'name', _NAME);
    $form->addRule('name', _THEFIELD . ' "' . _NAME . '" ' . _ISMANDATORY, 'required');
    $form->addElement(EfrontEntity::createDateElement($form, 'start_date', _STARTDATE, array('format' => getDateFormat())));
    $form->addRule('start_date', _TRAININGREPORTS_INVALIDDATE, 'isValidDate');
    $form->addElement(EfrontEntity::createDateElement($form, 'end_date', _ENDDATE, array('format' => getDateFormat())));
    $form->addRule('end_date', _TRAININGREPORTS_INVALIDDATE, 'isValidDate');
    $form->addElement('select', 'separate_by', _TRAININGREPORTS_SEPARATEDBY, $period_options);
    $form->addElement('submit', 'submit', _SAVE, 'class="flatButton"');
    $form->setDefaults($defaults);
    return $form;
}
?>
