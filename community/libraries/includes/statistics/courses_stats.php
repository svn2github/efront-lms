<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$smarty -> assign("T_OPTION", $_GET['option']);

require_once $path."includes/statistics/stats_filters.php";

if (isset($_GET['sel_course'])) {
 $directionsTree = new EfrontDirectionsTree();
 $directionsPaths = $directionsTree -> toPathString();

 $course_id = $_GET['sel_course'];
    $infoCourse = new EfrontCourse($_GET['sel_course']);
    $infoCourse -> course['num_lessons'] = $infoCourse -> countCourseLessons();
    $constraints = array('table_filters' => $stats_filters);
    //$infoCourse -> course['num_students']   = sizeof($infoCourse -> getStudentUsers(false, $constraints));
    //$infoCourse -> course['num_professors'] = sizeof($infoCourse -> getProfessorUsers(false, $constraints));
    $infoCourse -> course['category_path'] = $directionsPaths[$infoCourse -> course['directions_ID']];

    $smarty -> assign("T_CURRENT_COURSE", $infoCourse);
    $smarty -> assign("T_STATS_ENTITY_ID", $_GET['sel_course']);

    try {
     $roles = EfrontLessonUser :: getLessonsRoles(true);
     $smarty -> assign("T_ROLES_ARRAY", $roles);

     $rolesBasic = EfrontLessonUser :: getLessonsRoles();
     $smarty -> assign("T_BASIC_ROLES_ARRAY", $rolesBasic);

     foreach ($rolesBasic as $key => $role) {
      $constraints = array('archive' => false, 'table_filters' => $stats_filters, 'condition' => 'uc.user_type = "'.$key.'"');
      $numUsers = $infoCourse -> countCourseUsers($constraints);
      if ($numUsers) {
       $usersPerRole[$key] = $numUsers;
      }
      //$role == 'student' ? $studentRoles[] = $key : $professorRoles[] = $key;
     }
     $infoCourse -> course['users_per_role'] = $usersPerRole;
     $infoCourse -> course['num_users'] = array_sum($usersPerRole);


     $courseInstances = $infoCourse -> getInstances();
     $smarty -> assign("T_COURSE_INSTANCES", $courseInstances);
     $smarty -> assign("T_COURSE_HAS_INSTANCES", sizeof($courseInstances) > 1);

     $smarty -> assign("T_DATASOURCE_SORT_BY", 0);
     if (isset($_GET['ajax']) && $_GET['ajax'] == 'courseUsersTable') {
      $smarty -> assign("T_DATASOURCE_COLUMNS", array('login', 'location', 'user_type', 'completed', 'score', 'operations', 'to_timestamp', 'enrolled_on'));
      $smarty -> assign("T_DATASOURCE_OPERATIONS", array('statistics'));
      $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'return_objects' => false, 'table_filters' => $stats_filters);
      $users = $infoCourse -> getCourseUsersAggregatingResults($constraints);
      $totalEntries = $infoCourse -> countCourseUsersAggregatingResults($constraints);
      $dataSource = $users;
      $smarty -> assign("T_TABLE_SIZE", $totalEntries);
     }
     if (isset($_GET['ajax']) && $_GET['ajax'] == 'instanceUsersTable' && eF_checkParameter($_GET['instanceUsersTable_source'], 'login')) {
      $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'user_type', 'location', 'active_in_course', 'completed', 'score', 'operations', 'to_timestamp', 'active_in_course'));
      $smarty -> assign("T_DATASOURCE_OPERATIONS", array('statistics'));
      $smarty -> assign("T_SHOW_COURSE_LESSONS", true);
      $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => $infoCourse -> course['id']);
      $constraints['required_fields'] = array('num_lessons', 'location');
      $constraints['return_objects'] = false;
      //$constraints['table_filters']   = $stats_filters;		//This is not needed here, since this list is for a specific user
      $infoUser = EfrontUserFactory :: factory($_GET['instanceUsersTable_source']);
      $courses = $infoUser -> getUserCourses($constraints);
      $totalEntries= $infoUser -> countUserCourses($constraints);

      $dataSource = $courses;
      $smarty -> assign("T_TABLE_SIZE", $totalEntries);
     }
     if (isset($_GET['ajax']) && $_GET['ajax'] == 'courseLessonsUsersTable' && eF_checkParameter($_GET['courseLessonsUsersTable_source'], 'id')) {
      $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'time_in_lesson', 'overall_progress', 'test_status', 'project_status', 'completed', 'score', 'user_type'));
      $infoUser = EfrontUserFactory :: factory($_GET['courseLessonsUsersTable_login']);
      $lessons = $infoUser -> getUserStatusInCourseLessons(new EfrontCourse($_GET['courseLessonsUsersTable_source']));
      $lessons = EfrontLesson :: convertLessonObjectsToArrays($lessons);
      $dataSource = $lessons;
     }
     if (isset($_GET['ajax']) && $_GET['ajax'] == 'coursesTable') {
      $smarty -> assign("T_DATASOURCE_COLUMNS", array('name', 'location', 'directions_name', 'num_students', 'num_lessons', 'num_skills', 'price', 'created', 'operations', 'sort_by_column' => 8));
      $smarty -> assign("T_DATASOURCE_OPERATIONS", array('statistics', 'settings'));
      $smarty -> assign("T_SHOW_COURSE_LESSONS", true);
      // the 'active' is now part of the table filters 
      $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'instance' => $infoCourse -> course['id']);
      $constraints['required_fields'] = array('has_instances', 'location', 'num_students', 'num_lessons', 'num_skills');
      $constraints['table_filters'] = $stats_filters;
      $courses = EfrontCourse :: getAllCourses($constraints);
      $courses = EfrontCourse :: convertCourseObjectsToArrays($courses);
      array_walk($courses, create_function('&$v,$k', '$v["has_instances"] = 0;')); //Eliminate the information on whether this course has instances, since this table only lists a course's instances anyway (and we want the + to expand its lessons always)
      $dataSource = $courses;
     }
     if (isset($_GET['ajax']) && $_GET['ajax'] == 'courseLessonsTable' && eF_checkParameter($_GET['courseLessonsTable_source'], 'id')) {
      $smarty -> assign("T_DATASOURCE_COLUMNS", array('name'));
      $lessons = $infoCourse -> getCourseLessons();
      $lessons = EfrontLesson :: convertLessonObjectsToArrays($lessons);
      $dataSource = $lessons;
     }

     $tableName = $_GET['ajax'];
     $alreadySorted = true;
     include("sorted_table.php");
    } catch (Exception $e) {
     handleAjaxExceptions($e);
    }

    $groups = EfrontGroup :: getGroups();
    $smarty -> assign("T_GROUPS", $groups);

}

if (isset($_GET['excel'])) {
    require_once 'Spreadsheet/Excel/Writer.php';

    $workBook = new Spreadsheet_Excel_Writer();
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
    $headerFormat = & $workBook -> addFormat(array('border' => 0, 'bold' => '1', 'size' => '11', 'color' => 'black', 'fgcolor' => 22, 'align' => 'center'));
    $formatContent = & $workBook -> addFormat(array('HAlign' => 'left', 'Valign' => 'top', 'TextWrap' => 1));
    $headerBigFormat = & $workBook -> addFormat(array('HAlign' => 'center', 'FgColor' => 22, 'Size' => 16, 'Bold' => 1));
    $titleCenterFormat = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 11, 'Bold' => 1));
    $titleLeftFormat = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 11, 'Bold' => 1));
    $fieldLeftFormat = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10));
    $fieldRightFormat = & $workBook -> addFormat(array('HAlign' => 'right', 'Size' => 10));
    $fieldCenterFormat = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 10));

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
    $workSheet -> write(7, 2, $infoCourse -> course['price'].' '.$GLOBALS['CURRENCYNAMES'][$GLOBALS['configuration']['currency']], $fieldRightFormat);
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

    $constraints = array('table_filters' => $stats_filters);
    $constraints['return_objects'] = false;
    $users = $infoCourse -> getCourseUsersAggregatingResults($constraints);

    foreach ($users as $info) {
        $workSheet -> write($row, 4, $info['login'], $fieldLeftFormat);
        $workSheet -> write($row, 5, $info['name'], $fieldLeftFormat);
        $workSheet -> write($row, 6, $info['surname'], $fieldLeftFormat);
        $workSheet -> write($row, 7, $roles[$info['user_type']], $fieldLeftFormat);
        //$workSheet -> write($row, 7, $info['time']['hours']."h ".$info['time']['minutes']."' ".$$info['time']['seconds']."''", $fieldCenterFormat);
        $workSheet -> write($row, 8, formatScore($info['score'])."%", $fieldCenterFormat);
        $workSheet -> write($row, 9, $info['completed'] ? _YES : _NO, $fieldCenterFormat);
        $row++;
    }
    $row += 2;
/*

    //COMMENTED OUT BECAUSE WE CHANGED THE REPORTING METHOD NOT TO INCLUDE LESSONS

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

*/
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
    $pdf -> Cell(70, 5, _COURSE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $infoCourse -> course['name'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    $pdf -> Cell(70, 5, _CATEGORY, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['direction'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    $pdf -> Cell(70, 5, _LESSONS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['lessons'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    if ($groupname || $branchName) {
        $pdf -> Cell(70, 5, _STUDENTS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($studentLogins).' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
        $pdf -> Cell(70, 5, _PROFESSORS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, sizeof($professorLogins).' ', 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    } else {
        $pdf -> Cell(70, 5, _STUDENTS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['students'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
        $pdf -> Cell(70, 5, _PROFESSORS, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['professors'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    }
    $pdf -> Cell(70, 5, _PRICE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $infoCourse -> course['price'].' '.$GLOBALS['CURRENCYNAMES'][$GLOBALS['configuration']['currency']], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
    $pdf -> Cell(70, 5, _LANGUAGE, 0, 0, L, 0);$pdf -> SetTextColor(0, 0, 255);$pdf -> Cell(70, 5, $basicInfo['language'], 0, 1, L, 0);$pdf -> SetTextColor(0, 0, 0);
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
    $constraints = array('table_filters' => $stats_filters);
    $constraints['return_objects'] = false;
    $users = $infoCourse -> getCourseUsersAggregatingResults($constraints);
    foreach ($users as $login => $info) {
        $pdf -> Cell(45, 7, $info['login'], 0, 0, L, 0);
        $pdf -> Cell(45, 7, $info['name'], 0, 0, L, 0);
        $pdf -> Cell(45, 7, $info['surname'], 0, 0, L, 0);
        $pdf -> Cell(45, 7, $roles[$info['user_type']], 0, 0, L, 0);
        $pdf -> Cell(45, 7, formatScore($info['score'])."%", 0, 0, C, 0);
        $pdf -> Cell(45, 7, $info['completed'] ? _YES : _NO, 0, 1, C, 0);
    }
//COMMENTED OUT BECAUSE WE CHANGED THE REPORTING METHOD NOT TO INCLUDE LESSONS    
/*

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

*/
    $pdf -> Output();
    exit(0);
}
?>
