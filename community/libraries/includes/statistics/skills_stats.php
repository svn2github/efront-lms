<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$smarty -> assign("T_OPTION", $_GET['option']);
try {

    if (isset($_GET['sel_skill'])) {
        $infoSkill = new EfrontSkill($_GET['sel_skill']);
    }

    if (isset($infoSkill)) {
        try {
         $skillCategory = eF_getTableData("module_hcd_skill_categories", "description", "id=".$infoSkill -> skill['categories_ID']);
         $infoSkill -> skill['category'] = $skillCategory[0]['description'];
      $smarty -> assign("T_CURRENT_SKILL_INFO", $infoSkill);
            $smarty -> assign("T_STATS_ENTITY_ID", $infoSkill -> skill['skill_ID']);
        } catch (Exception $e) {
         handleNormalFlowExceptions($e);
        }

        require_once $path."includes/statistics/stats_filters.php";

        try {
         if (isset($_GET['ajax']) && $_GET['ajax'] == 'skillUsersTable') {
          $constraints = createConstraintsFromSortedTable() + array('archive' => false, 'return_objects' => false, 'table_filters' => $stats_filters);
          $users = $infoSkill -> getSkillUsers($constraints);
          $totalEntries = $infoSkill -> countSkillUsers($constraints);
          $smarty -> assign("T_TABLE_SIZE", $totalEntries);

          $dataSource = $users;
          $tableName = $_GET['ajax'];
         }
         $alreadySorted = true;
         include("sorted_table.php");
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }

        try {
         if (isset($_GET['ajax']) && $_GET['ajax'] == 'graph_skill') {
          $constraints = array('archive' => false, 'return_objects' => false, 'table_filters' => $stats_filters);
          $users = $infoSkill -> getSkillUsers($constraints);

          $graph = new EfrontGraph();
          $graph -> type = 'bar';
          $data = array_merge(array(0=>0),array_values($users),array(0=>0));
          for ($i = 0; $i < sizeof($data); $i++) {
           $graph -> data[] = array($i, $data[$i]['score']);
           $graph -> xLabels[] = array($i, '<span style = "white-space:nowrap">'.formatLogin($data[$i]['login']).'</span>');
          }

          $graph -> xTitle = _USERS;
          $graph -> yTitle = _SCORE;
          $graph -> title = $infoSkill -> skill['description'];

          echo json_encode($graph);
          exit;

         }
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
    }
} catch (Exception $e) {
 handleNormalFlowExceptions($e);
}

if (isset($_GET['excel']) && $_GET['excel'] == 'skill') {
    // Get the associated group name
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
    require_once 'Spreadsheet/Excel/Writer.php';

    $workBook = new Spreadsheet_Excel_Writer();
    $workBook -> setTempDir(G_UPLOADPATH);
    $workBook -> setVersion(8);

    $filename = 'export_'.$infoSkill -> skill['description'];
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
    $fieldLeftBoldFormat = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10, 'Bold' => 1));
    $fieldLeftItalicFormat = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10, 'Italic' => 1));

    //first tab
    $workSheet = & $workBook -> addWorksheet("General Skill Info");
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
    $workSheet -> setColumn(1, 2, 20);

    $workSheet -> write(2, 1, _SKILL, $fieldLeftFormat);
    $workSheet -> write(2, 2, $infoSkill -> skill['description'], $fieldRightFormat);
    $workSheet -> write(3, 1, _CATEGORY, $fieldLeftFormat);
    $workSheet -> write(3, 2, $infoSkill -> skill['category'], $fieldRightFormat);

    $constraints = array('archive' => false, 'return_objects' => false, 'table_filters' => $stats_filters);
    $filteredUsers = $infoSkill -> getSkillUsers($constraints);

    $workSheet -> write(4, 1, _USERS, $fieldLeftFormat);
    $workSheet -> writeNumber(4, 2, sizeof($filteredUsers), $fieldRightFormat);

    $workSheet -> write(1, 4, _USERSINFO, $headerFormat);
    $workSheet -> mergeCells(1, 4, 1, 6);
    $workSheet -> setColumn(4, 4, 30);
    $workSheet -> setColumn(5, 5, 100);
    $workSheet -> setColumn(6, 6, 30);

    $workSheet -> write(2, 4, _LOGIN, $titleLeftFormat);
    $workSheet -> write(2, 5, _SPECIFICATION, $titleLeftFormat);
    $workSheet -> write(2, 6, _SCORE, $titleCenterFormat);

    $row = 3;
    foreach ($filteredUsers as $user) {
        $workSheet -> write($row, 4, formatLogin($user['login']), $fieldLeftFormat);
        $workSheet -> write($row, 5, $user['specification'], $fieldLeftFormat);
        $workSheet -> write($row, 6, $user['score']."%", $fieldCenterFormat);
        $row++;
    }

    $workBook -> close();
    exit(0);
} else if (isset($_GET['pdf']) && $_GET['pdf'] == 'skill') {

 $groupname = $branchName = false;
 try {
  $group = new EfrontGroup($_GET['group_filter']);
  $groupname = $group -> group['name'];
 } catch (Exception $e) {/*Do nothing if group filters are not specified*/}







 $reportTitle = _REPORT.": ".$infoSkill -> skill['description'];
 if ($groupname) {
  $reportTitle .= " "._FORGROUP.": ".$groupname;
  !$branchName OR $reportTitle .= _ANDBRANCH.": ".$branchName;
 } elseif ($branchName) {
  $reportTitle .= " "._FORBRANCH.": ".$branchName;
 }

 $pdf = new EfrontPdf($reportTitle);

    $constraints = array('archive' => false, 'return_objects' => false, 'table_filters' => $stats_filters);
    $filteredUsers = $infoSkill -> getSkillUsers($constraints);

 $info = array(array(_USER, $infoSkill -> skill['description']),
      array(_CATEGORY, $infoSkill -> skill['category']),
      array(_USERS, sizeof($filteredUsers)));
 $pdf -> printInformationSection(_BASICINFO, $info);

 $formatting = array(_USER => array('width' => '25%', 'fill' => false),
      _SPECIFICATION => array('width' => '65%', 'fill' => false),
      _SCORE => array('width' => '10%', 'fill' => false, 'align' => 'R'));
 $data = array();
 foreach ($filteredUsers as $user) {
  $data[] = array(_USER => formatLogin($user['login']),
      _SPECIFICATION => $user['specification'],
      _SCORE => $user['score']."%");
    }
 $pdf->printDataSection(_USERSINFO, $data, $formatting);

 $pdf -> OutputPdf('skill_form_'.$infoSkill -> skill['description'].'.pdf');
 exit;
}
