<?php

require_once($this->moduleBaseDir . '/lib/utilities.php');
require_once($this->moduleBaseDir . '/lib/TrainingReports_Report.php');

/* Create a new Report */
if ($this->command == 'create') {

    $form = mtr_getCreateReportForm($this->moduleBaseUrl . '&cat=view&cmd=create');

    if ($form->isSubmitted() && $form->validate()) {
        $report = array(
            'name' => $form->exportValue('name'),
            'from_date' => time(),
            'to_date' => time(),
            'separated_by' => 'week');

        $newId = eF_insertTableData('module_time_reports', $report);

        $this->smarty->assign('T_TRAININGREPORT_NEWID', $newId);
        $this->smarty->assign('T_TRAININGREPORT_CREATEMESSAGE', _TRAININGREPORTS_SUCCESSFULLYCREATED);
    }

    $this->smarty->assign('T_TRAININGREPORT_FORM', $form->toArray());

    /* Clone a report */
} else if ($this->command == 'clone' && TrainingReports_Report::isValid($this->id)) {

    $trainingReport = new TrainingReports_Report($this->id);
    $form = mtr_getCloneReportForm($this->moduleBaseUrl . '&cat=view&cmd=clone&id=' . $this->id, $trainingReport);

    if ($form->isSubmitted() && $form->validate()) {

        $report = $trainingReport->getReport();
        unset($report['id']);
        $report['name'] = $form->exportValue('name');

        $newId = eF_insertTableData('module_time_reports', $report);
        $fields = eF_getTableData('module_time_reports_fields', '*', 'reports_ID=' . $this->id);

        foreach ($fields as $index => $entry) {
            $fields[$index]['reports_ID'] = $newId;
        }
        eF_insertTableDataMultiple('module_time_reports_fields', $fields);

        $courses = eF_getTableData('module_time_reports_courses', '*', 'reports_ID=' . $this->id);
        foreach ($courses as $index => $entry) {
            $courses[$index]['reports_ID'] = $newId;
        }
        eF_insertTableDataMultiple('module_time_reports_courses', $courses);

        $this->smarty->assign('T_TRAININGREPORT_NEWID', $newId);
        $this->smarty->assign('T_TRAININGREPORT_CLONEMESSAGE', _TRAININGREPORTS_SUCCESSFULLYCLONED);
    }

    $this->smarty->assign('T_TRAININGREPORT_FORM', $form->toArray());

    /* Select and view report */
} else {

    if (TrainingReports_Report::isValid($this->id)) {

        if ($this->command == 'delete') {
            eF_deleteTableData('module_time_reports_fields', 'reports_ID=' . $this->id);
            eF_deleteTableData('module_time_reports_courses', 'reports_ID=' . $this->id);
            eF_deleteTableData('module_time_reports', 'id=' . $this->id);

            eF_redirect($this->moduleBaseUrl . '&cat=view&message_type=success&message=' . rawurlencode(_TRAININGREPORTS_SUCCESSFULLYDELETED));
        }

        $trainingReport = new TrainingReports_Report($this->id);
        $periodOptions = $trainingReport->getPeriodsOptions();

        $this->smarty->assign('T_TRAININGREPORT_FIELDS', $trainingReport->getFieldsOptions());
        $this->smarty->assign('T_TRAININGREPORT_SELECTEDFIELDS', $trainingReport->getFields());
        $this->smarty->assign('T_TRAININGREPORT_COURSES', $trainingReport->getCoursesOptions());
        $this->smarty->assign('T_TRAININGREPORT_SELECTEDCOURSES', $trainingReport->getCourses());
        $this->smarty->assign('T_TRAININGREPORT_SEPARATEDBY', $periodOptions[$trainingReport->getSeparatedBy()]);
        $this->smarty->assign('T_TRAININGREPORT_REPORT', $trainingReport->getReport());
    }

    $form = mtr_getReportsForm($this->moduleBaseUrl . '&cat=view', $this->id);
    $renderer = mtr_getFormRenderer($form, $this->smarty);
    $this->smarty->assign('T_TRAININGREPORT_FORM', $renderer->toArray());
}

/**
 *
 * @param <type> $url
 * @return HTML_QuickForm 
 */
function mtr_getReportsForm($url, $id) {

    $reports = eF_getTableDataFlat('module_time_reports', '*', '1', 'name ASC');
    $reports_options = array_combine($reports['id'], $reports['name']);
    $reports_options[0] = _TRAININGREPORTS_SELECTREPORT;
    ksort($reports_options);

    $form = new HTML_QuickForm('mtr_form', 'post', $url, '', null, true);
    $form->addElement('select', 'report', _REPORT, $reports_options, array('id' => 'module-reports-select'));
    $form->setDefaults(array('report' => $id));

    return $form;
}

/**
 * Returns a form for creating a new training report.
 *
 * @param <type> $url
 * @return HTML_QuickForm 
 */
function mtr_getCreateReportForm($url) {

    $form = new HTML_QuickForm("time_reports_form", "post", $url, "", null, true);

    $form->addElement('text', 'name', _NAME);
    $form->addRule('name', _THEFIELD . ' "' . _NAME . '" ' . _ISMANDATORY, 'required');
    $form->addElement('submit', 'submit', _CREATE, 'class = "flatButton"');

    return $form;
}

/**
 * Returns a form for cloning a report.
 *
 * @param string $url
 * @param TrainingReport_Report $trainingReport
 * @return HTML_QuickForm 
 */
function mtr_getCloneReportForm($url, $trainingReport) {

    $defaults = array('name' => $trainingReport->getName(). ' ' . _COPY);

    $form = new HTML_QuickForm("time_reports_form", "post", $url, "", null, true);

    $form->addElement('text', 'name', _NAME);
    $form->addRule('name', _THEFIELD . ' "' . _NAME . '" ' . _ISMANDATORY, 'required');
    $form->addElement('submit', 'submit', _CLONE, 'class = "flatButton"');
    $form->setDefaults($defaults);

    return $form;
}

?>
