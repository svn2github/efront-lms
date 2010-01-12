<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$smarty -> assign("T_OPTION", $_GET['option']);

if (isset($_GET['sel_course'])) {
    $course_id     = $_GET['sel_course'];
    $infoCourse = new EfrontCourse($course_id);

    $groups     = EfrontGroup :: getGroups();
    $smarty -> assign("T_GROUPS", $groups);

    $smarty -> assign("T_COURSE_NAME", $infoCourse -> course['name']);
    $smarty -> assign("T_COURSE_ID", $course_id);

    $roles = EfrontLessonUser :: getLessonsRoles();
    $smarty -> assign("T_ROLES", EfrontLessonUser :: getLessonsRoles(true));

    $basicInfo         = array();
    $basicInfo['id']   = $course_id;
    $basicInfo['name'] = $infoCourse -> course['name'];
    $result             = eF_getTableData("directions", "name", "id=".$infoCourse -> course['directions_ID']);
    if (sizeof($result) > 0) {
        $basicInfo['direction'] = $result[0]['name'];
    }
    $basicInfo['lessons']    = sizeof($infoCourse -> getLessons(false));
    $basicInfo['professors'] = 0;
    $basicInfo['students']   = 0;
    $courseUsers = $infoCourse -> getUsers(false);
    $studentLogins   = array();
    $professorLogins = array();
    foreach ($courseUsers as $login => $user) {
        if ($roles[$user['role']] == 'student') {
            $basicInfo['students']++;
            $studentLogins[] = $login;
        } else if ($roles[$user['role']] == 'professor') {
            $basicInfo['professors']++;
            $professorLogins[] = $login;
        }
    }

    $languages = EfrontSystem :: getLanguages(true);
    $basicInfo['language'] = $languages[$infoCourse -> course['languages_NAME']];
    $basicInfo['price']    = $infoCourse -> course['price_string'];

    $smarty -> assign("T_COURSE_INFO", $basicInfo);


    if (G_VERSIONTYPE == 'enterprise') {
        // Create the branches select
        require_once $path."module_hcd_tools.php";
        $company_branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");
        $smarty -> assign("T_BRANCHES", eF_createBranchesTreeSelect($company_branches,4));
    }
     
    if (isset($_GET['group_filter']) && $_GET['group_filter'] != -1) {
        try {
            $selectedGroup = new EfrontGroup($_GET['group_filter']);
            $groupUsers    = $selectedGroup -> getUsers();
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    }

    if (G_VERSIONTYPE == 'enterprise' && isset($_GET['branch_filter']) && $_GET['branch_filter'] != 0) {
        // See whether a second - branch related filter is enforced
        // If so modify the groupUsers variable if it exists, otherwise create it
        $selectedBranch = new EfrontBranch($_GET['branch_filter']);
        $allBranchesUsers = $selectedBranch ->getEmployeesWithJobs();

        $branchEmployees = array();
        $branchEmployees['professor'] = array();
        $branchEmployees['student'] = array();
        foreach ($allBranchesUsers as $key => $employee) {
            // Only  the employees of the selected branch will have this field in the results
            if ($employee['branch_ID']) {
                $branchEmployees[$employee['user_type']][] = $employee['login'];
            }
        }
        // Merge results with the users from the possible group users filter
        if (isset($groupUsers)) {
            $groupUsers['student'] = array_intersect($groupUsers['student'], $branchEmployees['student']);
            $groupUsers['professor'] = array_intersect($groupUsers['professor'], $branchEmployees['professor']);
        } else {
            $groupUsers = $branchEmployees;
        }
    }


    $status    = EfrontStats :: getUsersCourseStatus($infoCourse, $studentLogins);
    foreach ($studentLogins as $key=>$login) {
        if (isset($groupUsers) && !in_array($login, $groupUsers['student'])) {
            unset($studentLogins[$key]);
        } else if (((!$_GET['user_filter'] || $_GET['user_filter'] == 1) && !$status[$infoCourse -> course['id']][$login]['active']) || ($_GET['user_filter'] == 2 && $status[$infoCourse -> course['id']][$login]['active'])) {
            unset($studentLogins[$key]);
        }
    }

    $userStats = array();
    foreach ($studentLogins as $login) {
        $userStats[$login] = array('name'      => $status[$infoCourse -> course['id']][$login]['name'],
                                           'surname'   => $status[$infoCourse -> course['id']][$login]['surname'],
                                           'active'    => $status[$infoCourse -> course['id']][$login]['active'],
                                           'role'	   => $status[$infoCourse -> course['id']][$login]['user_type'],
                						   'score'     => $status[$infoCourse -> course['id']][$login]['score'],
                                           'completed' => $status[$infoCourse -> course['id']][$login]['completed'],
        //'time'      => $status[$infoCourse -> course['id']][$login]['total_time'],
        //'seconds'   => $status[$infoCourse -> course['id']][$login]['total_time']['total_seconds']
        );
    }
    $smarty -> assign("T_COURSE_USERS_STATS", $userStats);

    $status         = EfrontStats :: getUsersCourseStatus($infoCourse, $professorLogins);
    foreach ($professorLogins as $key=>$login) {
        if (isset($groupUsers) && !in_array($login, $groupUsers['professor'])) {
            unset($professorLogins[$key]);
        } else if (((!$_GET['user_filter'] || $_GET['user_filter'] == 1) && !$status[$infoCourse -> course['id']][$login]['active']) || ($_GET['user_filter'] == 2 && $status[$infoCourse -> course['id']][$login]['active'])) {
            unset($professorLogins[$key]);
        }

    }

    $professorStats = array();
    foreach ($professorLogins as $login) {
        $professorStats[$login] = array('name'      => $status[$infoCourse -> course['id']][$login]['name'],
                                                'surname'   => $status[$infoCourse -> course['id']][$login]['surname'],
                                                'role'	    => $status[$infoCourse -> course['id']][$login]['user_type'],
                                                'active'    => $status[$infoCourse -> course['id']][$login]['active'],
        //'time'      => $status[$infoCourse -> course['id']][$login]['total_time'],
        //'seconds'   => $status[$infoCourse -> course['id']][$login]['total_time']['total_seconds']
        );
    }
    $smarty -> assign("T_COURSE_PROFESSORS_STATS", $professorStats);

    $courseLessons = $infoCourse -> getLessons(true);
    $lessonsInfo   = array();
    foreach ($courseLessons as $id => $lesson) {
        $stats                        = $lesson -> getStatisticInformation();
        $lessonsInfo[$id]['name']     = $lesson -> lesson['name'];
        $lessonsInfo[$id]['active']   = $lesson -> lesson['active'];
        $lessonsInfo[$id]['content']  = $stats['content'];
        $lessonsInfo[$id]['tests']    = $stats['tests'];
        $lessonsInfo[$id]['projects'] = $stats['projects'];
    }
    $smarty -> assign("T_COURSE_LESSON_STATS", $lessonsInfo);

}

if (isset($_GET['excel'])) {
    require_once 'Spreadsheet/Excel/Writer.php';

    $workBook  = new Spreadsheet_Excel_Writer();
    $workBook -> setTempDir(G_UPLOADPATH);
    $workBook -> setVersion(8);

    if (isset($_GET['group_filter']) && $_GET['group_filter']) {
        try {
            $group = new EfrontGroup($_GET['group_filter']);
            $groupname = str_replace(" ", "_" , $group -> group['name']);
        } catch (Exception $e) {
            $groupname = false;

        }
    }
    if (G_VERSIONTYPE == 'enterprise' && isset($_GET['branch_filter']) && $_GET['branch_filter']) {
        try {
            $branch = new EfrontBranch($_GET['branch_filter']);
            $branchName = $branch -> branch['name'];
        } catch (Exception $e) {
            $branchName = false;
        }
    }

    $filename = 'export_'.$course -> course['name'];
    if ($groupname) {
        $filename .= '_group_'.str_replace(" ", "_" , $groupname);
    }
    if ($branchName) {
        $filename .= '_branch_'.str_replace(" ", "_" , $branchName);
    }
    $workBook -> send($filename.'.xls');

    $formatExcelHeaders = & $workBook -> addFormat(array('Size' => 14, 'Bold' => 1, 'HAlign' => 'left'));
    $headerFormat       = & $workBook -> addFormat(array('border' => 0, 'bold' => '1', 'size' => '11', 'color' => 'black', 'fgcolor' => 22, 'align' => 'center'));
    $formatContent      = & $workBook -> addFormat(array('HAlign' => 'left', 'Valign' => 'top', 'TextWrap' => 1));
    $headerBigFormat    = & $workBook -> addFormat(array('HAlign' => 'center', 'FgColor' => 22, 'Size' => 16, 'Bold' => 1));
    $titleCenterFormat  = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 11, 'Bold' => 1));
    $titleLeftFormat    = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 11, 'Bold' => 1));
    $fieldLeftFormat    = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10));
    $fieldRightFormat   = & $workBook -> addFormat(array('HAlign' => 'right', 'Size' => 10));
    $fieldCenterFormat  = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 10));

    //first tab
    $workSheet = & $workBook -> addWorksheet("General Course Info");
    $workSheet -> setInputEncoding('utf-8');

    $workSheet -> setColumn(0, 0, 5);
     
    //basic info
    if ($groupname || $branchName) {
        $celltitle = "";
        if ($groupname) {
            $celltitle .= _BASICINFO . " " . _FORGROUP . ": ". $groupname . " ";
        }
        if ($branchName) {
            if ($celltitle != "") {
                $celltitle .= _ANDBRANCH. ": ". $branchName . " ";
            } else {
                $celltitle .= _BASICINFO . " " ._FORBRANCH . ": ". $branchName . " ";
            }
        }
        $workSheet -> write(1, 1, $celltitle, $headerFormat);
    } else {
        $workSheet -> write(1, 1, _BASICINFO, $headerFormat);
    }
     
    $workSheet -> mergeCells(1, 1, 1, 2);
    $workSheet -> setColumn(1, 2, 30);

    $workSheet -> write(2, 1, _COURSE, $fieldLeftFormat);
    $workSheet -> write(2, 2, $infoCourse -> course['name'], $fieldRightFormat);
    $workSheet -> write(3, 1, _DIRECTION, $fieldLeftFormat);
    $workSheet -> write(3, 2, $basicInfo['direction'], $fieldRightFormat);
    $workSheet -> write(4, 1, _LESSONS, $fieldLeftFormat);
    $workSheet -> writeNumber(4, 2, $basicInfo['lessons'], $fieldRightFormat);

    if ($groupname || $branchName) {
        $workSheet -> write(5, 1, _STUDENTS, $fieldLeftFormat);
        $workSheet -> writeNumber(5, 2, sizeof($studentLogins), $fieldRightFormat);
        $workSheet -> write(6, 1, _PROFESSORS, $fieldLeftFormat);
        $workSheet -> writeNumber(6, 2, sizeof($professorLogins), $fieldRightFormat);
    } else {
        $workSheet -> write(5, 1, _STUDENTS, $fieldLeftFormat);
        $workSheet -> writeNumber(5, 2, $basicInfo['students'], $fieldRightFormat);
        $workSheet -> write(6, 1, _PROFESSORS, $fieldLeftFormat);
        $workSheet -> write(6, 2, $basicInfo['professors'], $fieldRightFormat);
    }

    $workSheet -> write(7, 1, _PRICE, $fieldLeftFormat);
    $workSheet -> write(7, 2,  $infoCourse -> course['price'].' '.$GLOBALS['CURRENCYNAMES'][$GLOBALS['configuration']['currency']], $fieldRightFormat);
    $workSheet -> write(8, 1, _LANGUAGE, $fieldLeftFormat);
    $workSheet -> write(8, 2, $basicInfo['language'], $fieldRightFormat);


    //course users info
    $workSheet -> write(1, 4, _USERSINFO, $headerFormat);
    $workSheet -> mergeCells(1, 4, 1, 9);
    $workSheet -> setColumn(4, 9, 15);

    $workSheet -> write(2, 4, _LOGIN, $titleLeftFormat);
    $workSheet -> write(2, 5, _FIRSTNAME, $titleLeftFormat);
    $workSheet -> write(2, 6, _SURNAME, $titleLeftFormat);
    $workSheet -> write(2, 7, _COURSEROLE, $titleLeftFormat);
    //$workSheet -> write(2, 7, _TOTALTIME, $titleCenterFormat);
    $workSheet -> write(2, 8, _SCORE, $titleCenterFormat);
    $workSheet -> write(2, 9, _COMPLETED, $titleCenterFormat);

    $roles = EfrontLessonUser :: getLessonsRoles(true);
    $row = 3;
    foreach ($userStats as $login => $info) {
        $workSheet -> write($row, 4, $login, $fieldLeftFormat);
        $workSheet -> write($row, 5, $info['name'], $fieldLeftFormat);
        $workSheet -> write($row, 6, $info['surname'], $fieldLeftFormat);
        $workSheet -> write($row, 7, $roles[$info['role']], $fieldLeftFormat);
        //$workSheet -> write($row, 7, $info['time']['hours']."h ".$info['time']['minutes']."' ".$$info['time']['seconds']."''", $fieldCenterFormat);
        $workSheet -> write($row, 8, formatScore($info['score'])."%", $fieldCenterFormat);
        $workSheet -> write($row, 9, $info['completed'] ? _YES : _NO, $fieldCenterFormat);
        $row++;
    }
    $row += 2;

    //lessons
    $workSheet -> write($row, 4, _LESSONS, $headerFormat);
    $workSheet -> mergeCells($row, 4, $row, 8);

    $row++;
    $workSheet -> write($row, 4, _LESSON, $titleLeftFormat);
    $workSheet -> write($row, 5, _CONTENT, $titleCenterFormat);
    $workSheet -> write($row, 6, _TESTS, $titleCenterFormat);
    $workSheet -> write($row, 7, _PROJECTS, $titleCenterFormat);
    $row++;
    foreach ($lessonsInfo as $id => $info) {
        $workSheet -> write($row, 4, $info['name'], $fieldLeftFormat);
        $workSheet -> write($row, 5, $info['content'], $fieldCenterFormat);
        $workSheet -> write($row, 6, $info['tests'], $fieldCenterFormat);
        $workSheet -> write($row, 7, $info['projects'], $fieldCenterFormat);
        $row++;
    }

    $workBook -> close();
    exit(0);

} else if (isset($_GET['pdf'])) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
    $pdf -> SetCreator(PDF_CREATOR);
    $pdf -> SetAuthor(PDF_AUTHOR);

    //set margins
    $pdf -> SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //set auto page breaks
    $pdf -> SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf -> SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf -> SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf -> setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

    $pdf -> setHeaderFont(Array('FreeSerif', 'I', 11));
    $pdf -> setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf -> setHeaderData('','','', _STATISTICSFORCOURSE.": ".$infoCourse -> course['name']);

    //initialize document
    $pdf -> AliasNbPages();
    $pdf -> AddPage();

    $pdf -> SetFont("FreeSerif", "B", 12);
    $pdf -> SetTextColor(0, 0, 0);

    if (isset($_GET['group_filter']) && $_GET['group_filter']) {
        try {
            $group = new EfrontGroup($_GET['group_filter']);
            $groupname = str_replace(" ", "_" , $group -> group['name']);
        } catch (Exception $e) {
            $groupname = false;

        }
    }
    if (G_VERSIONTYPE == 'enterprise' && isset($_GET['branch_filter']) && $_GET['branch_filter']) {
        try {
            $branch = new EfrontBranch($_GET['branch_filter']);
            $branchName = $branch -> branch['name'];
        } catch (Exception $e) {
            $branchName = false;
        }
    }

    if ($groupname || $branchName) {
        $celltitle = "";
        if ($groupname) {
            $celltitle .= _BASICINFO . " " . _FORGROUP . ": ". $groupname . " ";
        }
        if ($branchName) {
            if ($celltitle != "") {
                $celltitle .= _ANDBRANCH. ": ". $branchName . " ";
            } else {
                $celltitle .= _BASICINFO . " " . _FORBRANCH . ": ". $branchName . " ";
            }
        }
        $pdf -> Cell(100, 10, $celltitle, 0, 1, L, 0);
    } else {
        $pdf -> Cell(100, 10, _BASICINFO, 0, 1, L, 0);
    }

     
    $pdf -> SetFont("FreeSerif", "", 10);
    $pdf -> Cell(70, 5, _COURSE,     0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $infoCourse -> course['name'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    $pdf -> Cell(70, 5, _CATEGORY,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['direction'],       0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    $pdf -> Cell(70, 5, _LESSONS,    0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['lessons'],         0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);

    if ($groupname || $branchName) {
        $pdf -> Cell(70, 5, _STUDENTS,      0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($studentLogins).' ',          0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
        $pdf -> Cell(70, 5, _PROFESSORS,    0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($professorLogins).' ',        0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    } else {
        $pdf -> Cell(70, 5, _STUDENTS,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['students'],        0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
        $pdf -> Cell(70, 5, _PROFESSORS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['professors'],      0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    }

    $pdf -> Cell(70, 5, _PRICE,      0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $infoCourse -> course['price'].' '.$GLOBALS['CURRENCYNAMES'][$GLOBALS['configuration']['currency']], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    $pdf -> Cell(70, 5, _LANGUAGE,   0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['language'],        0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);

    //users
    $pdf -> AddPage('L');
    $pdf -> SetFont("FreeSerif", "B", 12);
    $pdf -> Cell(100, 10, _USERSINFO, 0, 1, L, 0);

    $pdf -> SetFont("FreeSerif", "B", 10);
    $pdf -> Cell(45, 7, _LOGIN, 0, 0, L, 0);
    $pdf -> Cell(45, 7, _FIRSTNAME, 0, 0, L, 0);
    $pdf -> Cell(45, 7, _SURNAME, 0, 0, L, 0);
    $pdf -> Cell(45, 7, _COURSEROLE, 0, 0, L, 0);
    $pdf -> Cell(45, 7, _SCORE, 0, 0, C, 0);
    $pdf -> Cell(45, 7, _COMPLETED, 0, 1, C, 0);

    $roles = EfrontLessonUser :: getLessonsRoles(true);

    $pdf -> SetFont("FreeSerif", "", 10);
    $pdf -> SetTextColor(0, 0, 255);
    foreach ($userStats as $login => $info) {
        $pdf -> Cell(45, 7, $login, 0, 0, L, 0);
        $pdf -> Cell(45, 7, $info['name'], 0, 0, L, 0);
        $pdf -> Cell(45, 7, $info['surname'], 0, 0, L, 0);
        $pdf -> Cell(45, 7, $roles[$info['role']], 0, 0, L, 0);
        $pdf -> Cell(45, 7, formatScore($info['score'])."%", 0, 0, C, 0);
        $pdf -> Cell(45, 7, $info['completed'] ? _YES : _NO, 0, 1, C, 0);
    }

    //lessons
    $pdf -> AddPage('L');
    $pdf -> SetTextColor(0, 0, 0);
    $pdf -> SetFont("FreeSerif", "B", 12);
    $pdf -> Cell(100, 10, _LESSONS, 0, 1, L, 0);

    $pdf -> SetFont("FreeSerif", "B", 10);
    $pdf -> Cell(60, 7, _LESSON, 0, 0, L, 0);
    if ($GLOBALS['configuration']['disable_tests'] != 1 && $GLOBALS['configuration']['disable_projects'] != 1) {
        $pdf -> Cell(60, 7, _CONTENT, 0, 0, C, 0);
        $pdf -> Cell(60, 7, _TESTS, 0, 0, C, 0);
        $pdf -> Cell(60, 7, _PROJECTS, 0, 1, C, 0);
    } elseif ($GLOBALS['configuration']['disable_tests'] != 1) {
        $pdf -> Cell(60, 7, _CONTENT, 0, 0, C, 0);
        $pdf -> Cell(60, 7, _TESTS, 0, 1, C, 0);
    } elseif ($GLOBALS['configuration']['disable_projects']) {
        $pdf -> Cell(60, 7, _CONTENT, 0, 0, C, 0);
        $pdf -> Cell(60, 7, _PROJECTS, 0, 1, C, 0);
    } else {
        $pdf -> Cell(60, 7, _CONTENT, 0, 1, C, 0);
    }

    $pdf -> SetFont("FreeSerif", "", 10);
    $pdf -> SetTextColor(0, 0, 255);
    foreach ($lessonsInfo as $id => $info) {
        $pdf -> Cell(60, 7, $info['name'], 0, 0, L, 0);
        if ($GLOBALS['configuration']['disable_tests'] != 1 && $GLOBALS['configuration']['disable_projects'] != 1) {
            $pdf -> Cell(60, 7, $info['content'].' ', 0, 0, C, 0);
            $pdf -> Cell(60, 7, $info['tests'].' ', 0, 0, C, 0);
            $pdf -> Cell(60, 7, $info['projects'].' ', 0, 1, C, 0);
        } elseif ($GLOBALS['configuration']['disable_tests'] != 1) {
            $pdf -> Cell(60, 7, $info['content'].' ', 0, 0, C, 0);
            $pdf -> Cell(60, 7, $info['tests'].' ', 0, 1, C, 0);
        } elseif ($GLOBALS['configuration']['disable_projects']) {
            $pdf -> Cell(60, 7, $info['content'].' ', 0, 0, C, 0);
            $pdf -> Cell(60, 7, $info['projects'].' ', 0, 1, C, 0);
        } else {
            $pdf -> Cell(60, 7, $info['content'].' ', 0, 1, C, 0);
        }
    }

    $pdf -> Output();
    exit(0);
}
?>