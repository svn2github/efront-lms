<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*

 * Class defining the new module

 * The name must match the one provided in the module.xml file

 */
class module_course_reports extends EfrontModule {
 /**

	 * Get the module name, for example "Demo module"

	 *

	 * @see libraries/EfrontModule#getName()

	 */
    public function getName() {
     //This is a language tag, defined in the file lang-<your language>.php
        return _MODULE_COURSE_REPORTS_COURSEREPORTS;
    }
 /**

	 * Return the array of roles that will have access to this module

	 * You can return any combination of 'administrator', 'student' or 'professor'

	 *

	 * @see libraries/EfrontModule#getPermittedRoles()

	 */
    public function getPermittedRoles() {
        return array("administrator"); //This module will be available to administrators
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCenterLinkInfo()

     */
    public function getCenterLinkInfo() {
     return array('title' => $this -> getName(),
                     'image' => $this -> moduleBaseLink . 'img/reports.png',
                     'link' => $this -> moduleBaseUrl);
    }
    /**

     * The main functionality

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getModule()

     */
    public function getModule() {
     $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASELINK" , $this -> moduleBaseLink);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
  try {
   if (isset($_GET['ajax']) && $_GET['ajax'] == 'courseReportsTable') {
    $courses = eF_getTableData("courses c, users_to_courses uc, users u", "c.name,uc.users_LOGIN, uc.score, uc.completed, uc.issued_certificate, uc.to_timestamp", "c.archive=0 and uc.archive=0 and u.archive=0 and u.login=uc.users_LOGIN and uc.courses_ID=c.id and uc.user_type in ('".implode("','", EfrontLessonUser::getStudentRoles())."')");
    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
     $sort = $_GET['sort'];
     isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
    } else {
     $sort = 'login';
    }
    $courses = eF_multiSort($courses, $sort, $order);
    $smarty -> assign("T_TABLE_SIZE", sizeof($courses));
    if (isset($_GET['filter'])) {
     $courses = eF_filterData($courses, $_GET['filter']);
    }
    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
     isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
     $courses = array_slice($courses, $offset, $limit);
    }
    $smarty -> assign("T_DATA_SOURCE", $courses);
    $smarty -> display($this -> moduleBaseDir . "module.tpl");
    exit;
   } elseif (isset($_GET['ajax']) && $_GET['ajax'] == 'courselessonReportsTable') {
    $lessons = eF_getTableData("lessons l, users_to_lessons ul, users u, lessons_to_courses lc, courses c", "c.name as course_name, l.name, ul.users_LOGIN, ul.score, ul.completed, ul.to_timestamp", "l.course_only=1 and l.archive=0 and ul.archive=0 and u.archive=0 and u.login=ul.users_LOGIN and ul.lessons_ID=l.id and lc.lessons_ID=l.id and lc.courses_ID=c.id and ul.user_type in ('".implode("','", EfrontLessonUser::getStudentRoles())."')", "course_name");
    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
     $sort = $_GET['sort'];
     isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
    } else {
     $sort = 'login';
    }
    $lessons = eF_multiSort($lessons, $sort, $order);
    $smarty -> assign("T_TABLE_SIZE", sizeof($lessons));
    if (isset($_GET['filter'])) {
     $lessons = eF_filterData($lessons, $_GET['filter']);
    }
    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
     isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
     $lessons = array_slice($lessons, $offset, $limit);
    }
    $smarty -> assign("T_DATA_SOURCE", $lessons);
    $smarty -> display($this -> moduleBaseDir . "module.tpl");
    exit;
   } elseif (isset($_GET['ajax']) && $_GET['ajax'] == 'lessonReportsTable') {
    $lessons = eF_getTableData("lessons l, users_to_lessons ul, users u", "l.name, ul.users_LOGIN, ul.score, ul.completed, ul.to_timestamp", "l.course_only=0 and l.archive=0 and ul.archive=0 and u.archive=0 and u.login=ul.users_LOGIN and ul.lessons_ID=l.id and ul.user_type in ('".implode("','", EfrontLessonUser::getStudentRoles())."')");
    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
     $sort = $_GET['sort'];
     isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
    } else {
     $sort = 'login';
    }
    $lessons = eF_multiSort($lessons, $sort, $order);
    $smarty -> assign("T_TABLE_SIZE", sizeof($lessons));
    if (isset($_GET['filter'])) {
     $lessons = eF_filterData($lessons, $_GET['filter']);
    }
    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
     isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
     $lessons = array_slice($lessons, $offset, $limit);
    }
    $smarty -> assign("T_DATA_SOURCE", $lessons);
    $smarty -> display($this -> moduleBaseDir . "module.tpl");
    exit;
   } else if ($_GET['export'] && $_GET['export'] == 'course') {
    $courses = eF_getTableData("courses c, users_to_courses uc, users u", "c.name,uc.users_LOGIN, uc.score, uc.completed, uc.issued_certificate, uc.to_timestamp", "c.archive=0 and uc.archive=0 and u.archive=0 and u.login=uc.users_LOGIN and uc.courses_ID=c.id");
    foreach ($courses as $key=>$value) {
     $courses[$key]['to_timestamp'] ? $courses[$key]['to_timestamp'] = formatTimestamp($value['to_timestamp']) : $courses[$key]['to_timestamp'] = '-';
     $courses[$key]['score'] ? $courses[$key]['score'] = formatScore($value['score'])."%" : $courses[$key]['score'] = '-';
     $courses[$key]['completed'] ? $courses[$key]['completed'] = _YES : $courses[$key]['completed'] = _NO;
     $courses[$key]['issued_certificate'] ? $courses[$key]['issued_certificate'] = _YES : $courses[$key]['issued_certificate'] = _NO;
     $courses[$key]['users_LOGIN'] = formatLogin($value['users_LOGIN']);
    }
    array_unshift($courses, array(_COURSE, _USER, _SCORE, _COMPLETED, _CERTIFICATE, _COMPLETIONDATE));
    self :: exportToCsv($courses);
    exit;
   } else if ($_GET['export'] && $_GET['export'] == 'course_lesson') {
    $lessons = eF_getTableData("lessons l, users_to_lessons ul, users u, lessons_to_courses lc, courses c", "c.name as course_name, l.name, ul.users_LOGIN, ul.score, ul.completed, ul.to_timestamp", "l.course_only=1 and l.archive=0 and ul.archive=0 and u.archive=0 and u.login=ul.users_LOGIN and ul.lessons_ID=l.id and lc.lessons_ID=l.id and lc.courses_ID=c.id and ul.user_type in ('".implode("','", EfrontLessonUser::getStudentRoles())."')", "course_name");
    foreach ($lessons as $key=>$value) {
     $lessons[$key]['to_timestamp'] ? $lessons[$key]['to_timestamp'] = formatTimestamp($value['to_timestamp']) : $lessons[$key]['to_timestamp'] = '-';
     $lessons[$key]['score'] ? $lessons[$key]['score'] = formatScore($value['score'])."%" : $lessons[$key]['score'] = '-';
     $lessons[$key]['users_LOGIN'] = formatLogin($value['users_LOGIN']);
     $lessons[$key]['completed'] ? $lessons[$key]['completed'] = _YES : $lessons[$key]['completed'] = _NO;
    }
    array_unshift($lessons, array(_COURSE, _LESSON, _USER, _SCORE, _COMPLETED, _COMPLETIONDATE));
    self :: exportToCsv($lessons);
    exit;
   } else if ($_GET['export'] && $_GET['export'] == 'lesson') {
    $lessons = eF_getTableData("lessons l, users_to_lessons ul, users u", "l.name, ul.users_LOGIN, ul.score, ul.completed, ul.to_timestamp", "l.archive=0 and ul.archive=0 and u.archive=0 and u.login=ul.users_LOGIN and ul.lessons_ID=l.id");
    foreach ($lessons as $key=>$value) {
     $lessons[$key]['to_timestamp'] ? $lessons[$key]['to_timestamp'] = formatTimestamp($value['to_timestamp']) : $lessons[$key]['to_timestamp'] = '-';
     $lessons[$key]['score'] ? $lessons[$key]['score'] = formatScore($value['score'])."%" : $lessons[$key]['score'] = '-';
     $lessons[$key]['users_LOGIN'] = formatLogin($value['users_LOGIN']);
     $lessons[$key]['completed'] ? $lessons[$key]['completed'] = _YES : $lessons[$key]['completed'] = _NO;
    }
    array_unshift($lessons, array(_LESSON, _USER, _SCORE, _COMPLETED, _COMPLETIONDATE));
    self :: exportToCsv($lessons);
    exit;
   }
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
        return true;
    }
 public static function exportToCsv($data, $download = false, $name = "data.csv") {
  $currentUser = EfrontUserFactory::factory($_SESSION['s_login']);
  $fp = fopen($currentUser->getDirectory().$name, 'w');
  foreach ($data as $fields) {
   fputcsv($fp, $fields);
  }
  fclose($fp);
  $file = new EfrontFile($currentUser->getDirectory().$name);
  if (!$download) {
   $file -> sendFile(true);
  }
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
