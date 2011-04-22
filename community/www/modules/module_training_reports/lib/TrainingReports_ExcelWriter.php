<?php

global$_TIME_REPORTS_EXCEL_FORMATS;

require_once('Spreadsheet/Excel/Writer.php');

class TrainingReports_ExcelWriter extends Spreadsheet_Excel_Writer {

    private $workSheet;
    private $formats;
    private $report;
    private $fields;
    private $fieldsOptions;
    private $fieldsWidths;
    private $courses;
    private $coursesOptions;
    private $coursesWidth;

    public function __construct() {

        parent::Spreadsheet_Excel_Writer();

        $this->setTempDir(G_UPLOADPATH);
        $this->setVersion(8);

        /* Add custom colors */
        $this->setCustomColor(12, 242, 242, 242); // light gray
        $this->setCustomColor(13, 191, 191, 191); // gray for borders
        $this->setCustomColor(14, 194, 214, 154); // green
        $this->setCustomColor(15, 184, 204, 228); // blue
        $this->setCustomColor(17, 147, 205, 221); // cyan
        $this->setCustomColor(18, 250, 192, 144); // orange
        $this->setCustomColor(19, 247, 150, 70); // incomplete lesson
        $this->setCustomColor(20, 199, 80, 77); // not started lesson
        $this->setCustomColor(21, 155, 187, 89); // completed lesson

        global $_TIME_REPORTS_EXCEL_FORMATS;

        $formats = array();
        foreach ($_TIME_REPORTS_EXCEL_FORMATS as $key => $rules) {
            $this->formats[$key] = $this->addFormat($rules);
        }

        $this->workSheet = &$this->addWorksheet('Report');
        $this->workSheet->setInputEncoding('utf-8');

        $this->fieldsWidths = array();
        $this->coursesWidth = 0;

        $this->fieldsOptions = TrainingReports_Report::getFieldsOptions();
        $this->coursesOptions = TrainingReports_Report::getCoursesOptions();
    }

    /**
     *
     * @param TrainingReports_Report $report
     */
    public function write($report) {

        /* Get report data */
        $this->report = $report;
        $this->fields = $report->getFields();
        $this->courses = $report->getCourses();
        $this->periods = $report->getPeriods();
        $this->users = $report->getUserData();

        $fieldsCount = sizeof($this->fields);
        $coursesCount = sizeof($this->courses);
        $periodsCount = sizeof($this->periods);
        $usersCount = sizeof($this->users);

        $lastColumn = $fieldsCount + ($periodsCount * ($coursesCount + 1));
        $lastRow = $usersCount + 8;

        /* Set width of header and footer rows */
        $this->workSheet->setRow(0, 40);
        $this->workSheet->setRow(1, 10);
        $this->workSheet->setRow(2, 30);
        $this->workSheet->setRow(3, 16);
        $this->workSheet->setRow(4 + $usersCount, 23);
        $this->workSheet->setRow(5 + $usersCount, 23);
        $this->workSheet->setRow(6 + $usersCount, 23);
        $this->workSheet->setRow(7 + $usersCount, 23);
        $this->workSheet->setRow(8 + $usersCount, 10);

        /* Write header & set its layout */
        $title = $this->report->getName() . ' ('
                . formatTimestamp($this->report->getFromTimestamp()) . ' - '
                . formatTimestamp($this->report->getToTimestamp()) . ')';

        $this->workSheet->write(0, 0, $title, $this->formats['header']);
        $this->workSheet->mergeCells(0, 0, 0, $lastColumn);
        $this->workSheet->setColumn($fieldsCount, $fieldsCount, 1);

        /* Write users header */
        foreach ($this->fields as $index => $field) {
            $string = $this->fieldsOptions[$field];
            $this->workSheet->write(2, $index, $string, $this->formats['user_header']);
            $this->setFieldWidth($index, mb_strlen($string), 'header');
        }

        /* Write periods header */
        $column = $fieldsCount + 1;
        foreach ($this->periods as $periodIndex => $period) {
            $string = $period['title'];
            $formatName = 'period_header' . ($periodIndex % 3);

            $this->workSheet->write(2, $column, $string, $this->formats[$formatName]);
            $this->workSheet->mergeCells(2, $column, 2, $column + $coursesCount - 1);
            $this->workSheet->setColumn($column + $coursesCount, $column + $coursesCount, 1);

            $this->setCourseWidth(mb_strlen($string), 'header');

            foreach ($this->courses as $index => $course) {
                $string = $this->coursesOptions[$course];

                $this->workSheet->write(3, $column + $index, $string, $this->formats['course_header']);
                $this->setCourseWidth(mb_strlen($string));
            }
            $column = $column + $coursesCount + 1;
        }


        /* Write users */
        foreach ($this->users as $userIndex => $user) {

            /* Set row height */
            $this->workSheet->setRow($userIndex + 4, 23);

            /* Write the fields for the user */
            foreach ($this->fields as $fieldIndex => $field) {

                switch ($field) {
                    case 'timestamp':
                    case 'last_login':
                        if (empty($user[$field])) {
                            $string = _NEVER;
                        } else {
                            $string = formatTimestamp($user[$field], 'time');
                        }
                        break;
                    case 'completed':
                        $string = $user[$field] ? _YES : _NO;
                        break;
                    default:
                        $string = $user[$field];
                        break;
                }

                $this->workSheet->write($userIndex + 4, $fieldIndex, $string, $this->formats['user']);
                $this->setFieldWidth($fieldIndex, mb_strlen($string));
            }

            /* Write periods for the user */
            $column = $fieldsCount + 1;
            foreach ($this->periods as $periodIndex => $period) {

                $periodStart = $period['start'];
                $periodEnd = $period['end'];
                $this->workSheet->setColumn($column, $column + $coursesCount, $this->coursesWidth);

                /* Write data for the course for the given period */
                foreach ($this->courses as $courseIndex => $course) {

                    $string = _TRAININGREPORTS_NOTSTARTED;
                    $formatName = 'outside';

                    if (isset($user['courses'][$course]) == false) {
                        $this->workSheet->write($userIndex + 4, $column + $courseIndex, $string, $this->formats[$formatName]);
                        continue;
                    }

                    $courseData = $user['courses'][$course];
                    $from = $courseData['first_access'];
                    $to = $courseData['to_timestamp'];


                    /* If user has completed */
                    if (($to > $periodStart && $to < $periodEnd) || ($to > 0 && $to < $periodStart)) {
                        $string = _TRAININGREPORTS_STARTED . " - " . _TRAININGREPORTS_ENDED . "\n" .
                                formatTimestamp($from) . ' - ' . formatTimestamp($to);
                        $formatName = 'completed';
                    } else if ($from != null && $from > $periodStart && $from < $periodEnd) {
                        $string = _TRAININGREPORTS_STARTED . "\n" . formatTimestamp($from);
                        $formatName = 'incomplete';
                    } else if ($from != null && $from < $periodStart) {
                        $string = _TRAININGREPORTS_STARTED . "\n" . formatTimestamp($from);
                        $formatName = 'incomplete';
                    } else {
                        $string = _TRAININGREPORTS_NOTSTARTED;
                        $formatName = 'outside';
                    }

                    $this->workSheet->write($userIndex + 4, $column + $courseIndex, $string, $this->formats[$formatName]);
                }
                $column = $column + $coursesCount + 1;
            }
        }

        /* Draw borders */
        /* Borders: top, bottom, right, left */
        $this->applyFormat(1, 0, 1, $lastColumn, $this->formats['top_border']);
        $this->applyFormat($lastRow, 0, $lastRow, $lastColumn, $this->formats['bottom_border']);
        $this->applyFormat(1, $lastColumn, $lastRow, $lastColumn, $this->formats['right_border']);
        $this->applyFormat(1, $fieldsCount, $lastRow, $fieldsCount, $this->formats['left_border']);

        /* Draw corners: top-left, top-right, bottom-left, bottom-right */
        $this->workSheet->writeBlank(1, $fieldsCount, $this->formats['topleft_border']);
        $this->workSheet->writeBlank(1, $lastColumn, $this->formats['topright_border']);
        $this->workSheet->writeBlank($lastRow, $fieldsCount, $this->formats['bottomleft_border']);
        $this->workSheet->writeBlank($lastRow, $lastColumn, $this->formats['bottomright_border']);

        /* Write legends */
        $this->workSheet->writeBlank($usersCount + 5, $fieldsCount + 1, $this->formats['completed']);
        $this->workSheet->write($usersCount + 5, $fieldsCount + 2, _TRAININGREPORTS_LEGENDCOMPLETED, $this->formats['legend']);
        $this->workSheet->mergeCells($usersCount + 5, $fieldsCount + 2, $usersCount + 5, $lastColumn);
        $this->workSheet->writeBlank($usersCount + 6, $fieldsCount + 1, $this->formats['incomplete']);
        $this->workSheet->write($usersCount + 6, $fieldsCount + 2, _TRAININGREPORTS_LEGENDINCOMPLETE, $this->formats['legend']);
        $this->workSheet->mergeCells($usersCount + 6, $fieldsCount + 2, $usersCount + 6, $lastColumn);
        $this->workSheet->writeBlank($usersCount + 7, $fieldsCount + 1, $this->formats['outside']);
        $this->workSheet->write($usersCount + 7, $fieldsCount + 2, _TRAININGREPORTS_LEGENDOUTSIDE, $this->formats['legend']);
        $this->workSheet->mergeCells($usersCount + 7, $fieldsCount + 2, $usersCount + 7, $lastColumn);

        /* fix column widths for user fields */
        foreach ($this->fieldsWidths as $index => $width) {
            $this->workSheet->setColumn($index, $index, $width);
        }

        /* fix column widths for user periods */
        foreach ($this->fieldsWidths as $index => $width) {
            $this->workSheet->setColumn($index, $index, $width);
        }

        $this->workSheet->hideGridLines();
        $this->workSheet->hideScreenGridlines();
    }

    private function setFieldWidth($index, $width, $type='user') {

        if ($type == 'header') {
            $width = $width * 1.2;
        }

        if (isset($this->fieldsWidths[$index]) == false || $this->fieldsWidths[$index] < $width) {
            $this->fieldsWidths[$index] = $width;
        }
    }

    private function setCourseWidth($width, $type = 'course') {

        if ($type == 'header') {
            $width = ($width * 1.55) / (sizeof($this->courses));
        } else {
            $width = $width * 0.85;
        }

        if ($this->coursesWidth < $width) {
            $this->coursesWidth = $width;
        }
    }

    private function applyFormat($fromRow, $fromCol, $toRow, $toCol, $format) {
        for ($row = $fromRow; $row <= $toRow; $row++) {
            for ($col = $fromCol; $col <= $toCol; $col++) {
                $this->workSheet->writeBlank($row, $col, $format);
            }
        }
    }

}

$_TIME_REPORTS_EXCEL_FORMATS = array();

$_TIME_REPORTS_EXCEL_FORMATS['header'] = array(
    'border' => 0,
    'bold' => '1',
    'size' => '20',
    'color' => 'black',
    'fgcolor' => 12);

$_TIME_REPORTS_EXCEL_FORMATS['user_header'] = array(
    'bold' => '1',
    'vAlign' => 'vcenter',
    'size' => '10',
    'color' => 'black',
    'fgcolor' => 'white');

$_TIME_REPORTS_EXCEL_FORMATS['user'] = array(
    'vAlign' => 'vcenter',
    'size' => '9',
    'color' => 'black',
    'fgcolor' => 'white');

$_TIME_REPORTS_EXCEL_FORMATS['period_header0'] = array(
    'border' => 0,
    'bold' => '1',
    'hAlign' => 'center',
    'vAlign' => 'vcenter',
    'size' => '14',
    'color' => 'black',
    'fgcolor' => 14);

$_TIME_REPORTS_EXCEL_FORMATS['period_header1'] = array(
    'border' => 0,
    'bold' => '1',
    'hAlign' => 'center',
    'vAlign' => 'vcenter',
    'size' => '14',
    'color' => 'black',
    'fgcolor' => 17);

$_TIME_REPORTS_EXCEL_FORMATS['period_header2'] = array(
    'border' => 0,
    'bold' => '1',
    'hAlign' => 'center',
    'vAlign' => 'vcenter',
    'size' => '14',
    'color' => 'black',
    'fgcolor' => 18);

$_TIME_REPORTS_EXCEL_FORMATS['course_header'] = array(
    'size' => '8',
    'color' => 'black');

$_TIME_REPORTS_EXCEL_FORMATS['legend'] = array(
    'vAlign' => 'vcenter',
    'size' => '8',
    'color' => 'black');

$_TIME_REPORTS_EXCEL_FORMATS['period_header_alt'] = array(
    'border' => 0,
    'bold' => '1',
    'hAlign' => 'center',
    'vAlign' => 'vcenter',
    'size' => '14',
    'color' => 'black',
    'fgcolor' => 'white');

$_TIME_REPORTS_EXCEL_FORMATS['empty'] = array(
    'border' => 1,
    'bordercolor' => 13,
    'bold' => '0',
    'hAlign' => 'center',
    'vAlign' => 'vcenter',
    'size' => '8',
    'color' => 'black',
    'fgcolor' => 'white');

$_TIME_REPORTS_EXCEL_FORMATS['completed'] = array(
    'border' => 1,
    'bordercolor' => 13,
    'bold' => '0',
    'hAlign' => 'center',
    'vAlign' => 'vcenter',
    'size' => '8',
    'color' => 'black',
    'textWrap' => 1,
    'fgcolor' => 21);

$_TIME_REPORTS_EXCEL_FORMATS['incomplete'] = array(
    'border' => 1,
    'bordercolor' => 13,
    'bold' => '0',
    'hAlign' => 'center',
    'vAlign' => 'vcenter',
    'size' => '8',
    'color' => 'black',
    'textWrap' => 1,
    'fgcolor' => 19);

$_TIME_REPORTS_EXCEL_FORMATS['outside'] = array(
    'border' => 1,
    'bordercolor' => 13,
    'bold' => '0',
    'hAlign' => 'center',
    'vAlign' => 'vcenter',
    'size' => '8',
    'color' => 'black',
    'textWrap' => 1,
    'fgcolor' => 20);

$_TIME_REPORTS_EXCEL_FORMATS['top_border'] = array(
    'top' => 1,
    'bordercolor' => 13);

$_TIME_REPORTS_EXCEL_FORMATS['bottom_border'] = array(
    'bottom' => 1,
    'bordercolor' => 13);

$_TIME_REPORTS_EXCEL_FORMATS['left_border'] = array(
    'left' => 1,
    'bordercolor' => 13);

$_TIME_REPORTS_EXCEL_FORMATS['right_border'] = array(
    'right' => 1,
    'bordercolor' => 13);

$_TIME_REPORTS_EXCEL_FORMATS['topleft_border'] = array(
    'top' => 1,
    'left' => 1,
    'bordercolor' => 13);

$_TIME_REPORTS_EXCEL_FORMATS['topright_border'] = array(
    'top' => 1,
    'right' => 1,
    'bordercolor' => 13);

$_TIME_REPORTS_EXCEL_FORMATS['bottomright_border'] = array(
    'bottom' => 1,
    'right' => 1,
    'bordercolor' => 13);

$_TIME_REPORTS_EXCEL_FORMATS['bottomleft_border'] = array(
    'bottom' => 1,
    'left' => 1,
    'bordercolor' => 13);
?>
