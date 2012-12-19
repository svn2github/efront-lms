<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

/*

 * Class defining the new module

* The name must match the one provided in the module.xml file

*/
class module_branch_reports extends EfrontModule {
 public function addScripts() {
  if (strpos(decryptUrl($_SERVER['REQUEST_URI']), $this -> moduleBaseUrl) !== false) {
   return array('scriptaculous/controls', 'includes/statistics');
  } else {
   return array();
  }
 }
 /**

	 * Get the module name, for example "Demo module"

	 *

	 * @see libraries/EfrontModule#getName()

	 */
 public function getName() {
  //This is a language tag, defined in the file lang-<your language>.php
  return _MODULE_BRANCH_REPORTS_MODULEBRANCHREPORTS;
 }
 /**

	 * Return the array of roles that will have access to this module

	 * You can return any combination of 'administrator', 'student' or 'professor'

	 *

	 * @see libraries/EfrontModule#getPermittedRoles()

	 */
 public function getPermittedRoles() {
  return array('administrator', 'professor', 'student'); //This module will be available to administrators
 }
 /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCenterLinkInfo()

	 */
 public function getCenterLinkInfo() {
  return array('title' => $this -> getName(),
    'image' => $this -> moduleBaseLink . 'img/logo.png',
    'link' => $this -> moduleBaseUrl);
 }
 public function getReportsLinkInfo() {
  return array('title' => $this -> getName(),
    'image' => $this -> moduleBaseLink . 'img/logo.png',
    'link' => $this -> moduleBaseUrl);
 }
 /**

	 * The main functionality

	 *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getModule()

	 */
 public function getModule() {
  global $currentUser;
  $smarty = $this -> getSmartyVar();
  $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
  $smarty -> assign("T_MODULE_BASELINK" , $this -> moduleBaseLink);
  $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
  if (isset($_GET['sel_branch'])) {
   if (($_GET['from_year'])) { //the admin has chosen a period
    $from = mktime(0, 0, 0, $_GET['from_month'], $_GET['from_day'], $_GET['from_year']);
    $to = mktime(23,59,59, $_GET['to_month'], $_GET['to_day'], $_GET['to_year']);
   } else {
    $from = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
    $to = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
   }
   $smarty -> assign('T_FROM_TIMESTAMP', $from);
   $smarty -> assign('T_TO_TIMESTAMP', $to);
   $userTypes = EfrontUser::getRoles(true);
   if ($currentUser -> user['user_type'] == 'administrator') {
    $validBranches = EfrontBranch ::getBranches();
   } else {
    $validBranches = explode(",", $_SESSION['supervises_branches']);
   }
   if (isset($_GET['sel_branch'])) {
    if (in_array($_GET['sel_branch'], $validBranches)) {
     $infoBranch = new EfrontBranch($_GET['sel_branch']);
    } else {
     throw new EfrontUserException(_BRANCHISNOTVALIDORYOUCANNOTSEEBRANCH.": ".$_GET['sel_branch'], EfrontUserException :: INVALID_LOGIN);
    }
   }
   $tree = new EfrontBranchesTree();
   $branchPaths = $tree -> toPathString();
   $smarty -> assign("T_BRANCH_PATH", $branchPaths[$infoBranch -> branch['branch_ID']]);
   $infoBranchesTree = new EfrontBranchesTree();
   $infoBranchesPaths = $infoBranchesTree->toPathString();
   if ($_GET['subbranches'] == 1) {
    $users = $infoBranch->getBranchTreeUsers();
   } else {
    $users = $infoBranch->getBranchUsers();
   }
   $infoBranch -> branch['users_count'] = sizeof($users);
   $infoBranch -> branch['jobs_count'] = sizeof($infoBranch ->getJobDescriptions());
   $infoBranch -> branch['subbranches_count'] = sizeof($infoBranch ->getSubbranches());
   $smarty -> assign("T_BRANCH_INFO", $infoBranch -> branch);
   $smarty -> assign("T_BRANCH_NAME", $infoBranch -> branch['name']);
   $result = eF_getTableDataFlat("logs", "max(timestamp) as last_login, users_LOGIN", "action='login'", "", "users_LOGIN");
   $lastLogins = array_combine($result['users_LOGIN'], $result['last_login']);
   foreach ($users as $key => $user) {
    $user = EfrontUserFactory::factory($user['login']);
    //$users[$key]['courses'] = $user->getUserCourses(array('return_objects' => false));
    $users[$key]['user_type'] = $user->user['user_types_ID'] ? $userTypes[$user->user['user_types_ID']] : $userTypes[$user->user['user_type']];
    $time = EfrontUser::getLoginTime($user->user['login'], array('from' => $from, 'to' => $to));
    $users[$key]['time'] = eF_convertIntervalToTime($time['seconds']);
    $users[$key]['last_login'] = $lastLogins[$key];
    $users[$key]['branch_path'] = $infoBranchesPaths[$users[$key]['branch_ID']];
    if (!isset($_GET['excel']) && !isset($_GET['pdf'])) {
     $data[$users[$key]['login']] = $user->getUserCourses(array('return_objects' => false));
     foreach ($data[$key] as $key2 => $value) {
      if (($value['active_in_course'] < $from || $value['active_in_course'] > $to) && ($value['start_date'] < $from || $value['start_date'] > $to) && ($value['end_date'] < $from || $value['end_date'] > $to) && ($value['to_timestamp'] < $from || $value['to_timestamp'] > $to)) {
       unset($data[$key][$key2]);
      }
     }
    }
   }
   $smarty->assign("T_USERS", $users);
   $smarty->assign("T_DATA_SOURCE", $data);
   try {
    if (isset($_GET['ajax']) && strpos($_GET['ajax'], 'userTable') !== false) {
     $user = EfrontUserFactory::factory($_GET['user']);
     $data = $user->getUserCourses(array('return_objects' => false));
     foreach ($data as $key=>$value) {
      if (($value['active_in_course'] < $from || $value['active_in_course'] > $to) && ($value['start_date'] < $from || $value['start_date'] > $to) && ($value['end_date'] < $from || $value['end_date'] > $to) && ($value['to_timestamp'] < $from || $value['to_timestamp'] > $to)) {
       unset($data[$key]);
      }
     }
     isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
     if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
      $sort = $_GET['sort'];
      isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
     } else {
      $sort = 'login';
     }
     $data = eF_multiSort($data, $sort, $order);
     $smarty -> assign("T_TABLE_SIZE", sizeof($data));
     if (isset($_GET['filter'])) {
      $data = eF_filterData($data, $_GET['filter']);
     }
     if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
      isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
      $data = array_slice($data, $offset, $limit);
     }
     $smarty -> assign("T_DATA_SOURCE", $data);
     $smarty -> display($this -> moduleBaseDir . "module.tpl");
     exit;
    }
   } catch (Exception $e) {
    handleAjaxExceptions($e);
   }




   if (isset($_GET['excel'])) {

    require_once 'Spreadsheet/Excel/Writer.php';

    $workBook = new Spreadsheet_Excel_Writer();
    $workBook -> setTempDir(G_UPLOADPATH);
    $workBook -> setVersion(8);
    $workBook -> send('export_branch_'.$_GET['sel_branch'].'.xls');

    $formatExcelHeaders = & $workBook -> addFormat(array('Size' => 14, 'Bold' => 1, 'HAlign' => 'left'));
    $headerFormat = & $workBook -> addFormat(array('border' => 0, 'bold' => '1', 'size' => '11', 'color' => 'black', 'fgcolor' => 22, 'align' => 'center'));
    $formatContent = & $workBook -> addFormat(array('HAlign' => 'left', 'Valign' => 'top', 'TextWrap' => 1));
    $headerBigFormat = & $workBook -> addFormat(array('HAlign' => 'center', 'FgColor' => 22, 'Size' => 16, 'Bold' => 1));
    $titleCenterFormat = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 11, 'Bold' => 1));
    $titleLeftFormat = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 11, 'Bold' => 1));
    $fieldLeftFormat = & $workBook -> addFormat(array('HAlign' => 'left', 'Size' => 10));
    $fieldRightFormat = & $workBook -> addFormat(array('HAlign' => 'right', 'Size' => 10));
    $fieldCenterFormat = & $workBook -> addFormat(array('HAlign' => 'center', 'Size' => 10));

    $workSheet = & $workBook -> addWorksheet("(".$infoBranch ->branch['name'].") General Statistics");
    $workSheet -> setInputEncoding('utf-8');

    $workSheet -> setColumn(0, 0, 5);

    $workSheet -> write(1, 1, _BASICINFO, $headerFormat);
    $workSheet -> mergeCells(1, 1, 1, 2);
    $workSheet -> setColumn(1, 2, 35);

    for ($column = 3; $column < 10; $column++) {
     $workSheet -> setColumn(0, $column, 20);
    }
    $workSheet -> setColumn(0, 3, 26);
    $workSheet -> setColumn(0, 5, 26);
    $roles = EfrontLessonUser :: getLessonsRoles(true);
    $languages = EfrontSystem :: getLanguages(true);

    $row = 2;
    $workSheet -> write($row, 1, _BRANCHNAME, $fieldLeftFormat);
    $workSheet -> write($row++, 2, $infoBranch -> branch['name'], $fieldRightFormat);
    $workSheet -> write($row, 1, _BRANCHUSERS, $fieldLeftFormat);
    $workSheet -> write($row++, 2, $infoBranch -> branch['users_count'], $fieldRightFormat);
    $workSheet -> write($row, 1, _JOBDESCRIPTIONS, $fieldLeftFormat);
    $workSheet -> write($row++, 2, $infoBranch -> branch['jobs_count'], $fieldRightFormat);
    $workSheet -> write($row, 1, _SUBBRANCHES, $fieldLeftFormat);
    $workSheet -> write($row++, 2, $infoBranch -> branch['subbranches_count'], $fieldRightFormat);

    $row++;
    $workSheet -> write($row, 1, _USERS, $headerFormat);
    $workSheet -> mergeCells($row, 1, $row, 5);
    $row++;

    foreach ($users as $user) {
     $row++;
     $workSheet -> write($row, 1, formatLogin($user['login']), $headerFormat);
     $workSheet -> mergeCells($row, 1, $row, 5);
     $workSheet -> setColumn($row, 2, 35);
     $row++;

     $time = EfrontTimes::formatTimeForReporting($user['time']['seconds']);
     $workSheet -> write($row, 1, _USERNAME, $fieldLeftFormat);
     $workSheet -> write($row++, 2, formatLogin($user['login']), $fieldRightFormat);
     $workSheet -> write($row, 1, _USERTYPE, $fieldLeftFormat);
     $workSheet -> write($row++, 2, $user['user_type'], $fieldRightFormat);
     $workSheet -> write($row, 1, _PLACEMENT, $fieldLeftFormat);
     $workSheet -> write($row++, 2, $user['description'].' in '.$user['branch_path'], $fieldRightFormat);
     $workSheet -> write($row, 1, _LASTLOGIN, $fieldLeftFormat);
     $workSheet -> write($row++, 2, formatTimestamp($user['last_login'], 'time_nosec'), $fieldRightFormat);
     $workSheet -> write($row, 1, _TOTALTIME, $fieldLeftFormat);
     $workSheet -> write($row++, 2, $time['time_string'] ? $time['time_string'] : '-', $fieldRightFormat);

     $row++;
     $column = 1;
     $workSheet -> write($row, $column++, _COURSENAME , $titleLeftFormat);
     $workSheet -> write($row, $column++, _ENROLLEDON, $titleLeftFormat);
     $workSheet -> write($row, $column++, _STARTDATE, $titleLeftFormat);
     $workSheet -> write($row, $column++, _ENDDATE, $titleLeftFormat);
     $workSheet -> write($row++, $column++, _COMPLETED, $titleLeftFormat);

     $courses = EfrontUserFactory::factory($user['login'])->getUserCourses(array('return_objects' => false));
     foreach ($courses as $key=>$value) {
      if (($value['active_in_course'] < $from || $value['active_in_course'] > $to) && ($value['start_date'] < $from || $value['start_date'] > $to) && ($value['end_date'] < $from || $value['end_date'] > $to) && ($value['to_timestamp'] < $from || $value['to_timestamp'] > $to)) {
       unset($courses[$key]);
      } else {
       $column = 1;
       $workSheet -> write($row, $column++, $value['name'], $fieldLeftFormat);
       $workSheet -> write($row, $column++, formatTimestamp($value['active_in_course'], 'time_nosec'), $fieldLeftFormat);
       $workSheet -> write($row, $column++, formatTimestamp($value['start_date'], 'time_nosec'), $fieldLeftFormat);
       $workSheet -> write($row, $column++, formatTimestamp($value['end_date'], 'time_nosec'), $fieldLeftFormat);
       $workSheet -> write($row++, $column++, $value['completed'] ? formatTimestamp($value['to_timestamp'], 'time_nosec') : _NO, $fieldLeftFormat);
      }
     }

     $row++;
    }
    $workBook -> close();
    exit;
   } else if (isset($_GET['pdf'])) {
    $pdf = new EfrontPdf(_REPORT.": ".$infoBranch ->branch['name']);

    $info = array(array(_BRANCHNAME, $infoBranch->branch['name']),
      array(_BRANCHUSERS, $infoBranch->branch['users_count']),
      array(_JOBDESCRIPTIONS, $infoBranch->branch['jobs_count']),
      array(_SUBBRANCHES, $infoBranch->branch['subbranches_count']));
    $pdf -> printInformationSection(_BASICINFO, $info);

    $formatting_data = array(_COURSENAME => array('width' => '40%', 'fill' => false),
      _ENROLLEDON => array('width' => '15%', 'fill' => false, 'align' => 'C'),
      _STARTDATE => array('width' => '15%', 'fill' => false, 'align' => 'C'),
      _ENDDATE => array('width' => '15%', 'fill' => false, 'align' => 'C'),
      _COMPLETED => array('width' => '15%', 'fill' => false, 'align' => 'C'));
    $data = array();
    foreach ($users as $user) {
     $time = EfrontTimes::formatTimeForReporting($user['time']['seconds']);
     $info = array(array(_USERNAME, formatLogin($user['login'])),
       array(_USERTYPE, $user['user_type']),
       array(_PLACEMENT, $user['description'].' in '.$user['branch_path']),
       array(_LASTLOGIN, formatTimestamp($user['last_login'], 'time_nosec')),
       array(_TOTALTIME, $time['time_string'] ? $time['time_string'] : '-'));
     $pdf -> printInformationSection(formatLogin($user['login']), $info);

     $courses = EfrontUserFactory::factory($user['login'])->getUserCourses(array('return_objects' => false));
     $data = array();
     foreach ($courses as $key=>$value) {
      if (($value['active_in_course'] < $from || $value['active_in_course'] > $to) && ($value['start_date'] < $from || $value['start_date'] > $to) && ($value['end_date'] < $from || $value['end_date'] > $to) && ($value['to_timestamp'] < $from || $value['to_timestamp'] > $to)) {
       unset($courses[$key]);
      } else {
       $data[] = array(_COURSENAME => $value['name'],
         _ENROLLEDON => formatTimestamp($value['active_in_course'], 'time_nosec'),
         _STARTDATE => formatTimestamp($value['start_date'], 'time_nosec'),
         _ENDDATE => formatTimestamp($value['end_date'], 'time_nosec'),
         _COMPLETED => $value['completed'] ? formatTimestamp($value['to_timestamp'], 'time_nosec') : _NO);

      }
     }
     $pdf->printDataSection(_COURSES, $data, $formatting_data);
    }


    $pdf -> OutputPdf('branch_form_'.$infoBranch -> branch['name'].'.pdf');
    exit;
   }


  }


  return true;
 }

 /**

	 * Specify which file to include for template

	 *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getSmartyTpl()

	 */
 public function getSmartyTpl() {
  return $this -> moduleBaseDir."module.tpl";
 }
 /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getNavigationLinks()

	 */
 public function getNavigationLinks() {
  return array (array ('title' => _HOME, 'link' => $_SERVER['PHP_SELF']),
    array ('title' => $this -> getName(), 'link' => $this -> moduleBaseUrl));
 }
}
