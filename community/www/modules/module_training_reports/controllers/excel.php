<?php
require_once($this->moduleBaseDir . '/lib/utilities.php');
require_once($this->moduleBaseDir . '/lib/TrainingReports_ExcelWriter.php');
require_once($this->moduleBaseDir . '/lib/TrainingReports_Report.php');

$id = isset($_GET['id']) ? $_GET['id'] : $id;

if (TrainingReports_Report::isValid($id) == false) {
    eF_redirect($this->moduleBaseUrl . '&cat=view');
}

$report = new TrainingReports_Report($id);

$workBook = new TrainingReports_ExcelWriter();
$workBook->write($report);
$workBook->send($report->getName().'.xls');
$workBook->close();
exit;
?>
