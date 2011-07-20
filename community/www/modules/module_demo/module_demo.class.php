<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*

 * Class defining the new module

 * The name must match the one provided in the module.xml file

 */
class module_demo extends EfrontModule {
 /**

	 * Class constructor.

	 *

	 * Normally you don't have to call this function yourself, nor do you need to

	 * implement it in your modules

	 *

	 * @see libraries/EfrontModule#__construct()

	 */
 public function __construct($defined_moduleBaseUrl , $defined_moduleFolder) {
  parent::__construct($defined_moduleBaseUrl , $defined_moduleFolder);
 }
 /**

	 * Get the module name, for example "Demo module"

	 *

	 * @see libraries/EfrontModule#getName()

	 */
    public function getName() {
     //This is a language tag, defined in the file lang-<your language>.php
        return _MODULE_DEMO_MODULEDEMO;
    }
 /**

	 * Return the array of roles that will have access to this module

	 * You can return any combination of 'administrator', 'student' or 'professor'

	 *

	 * @see libraries/EfrontModule#getPermittedRoles()

	 */
    public function getPermittedRoles() {
        return array("administrator", "professor", "student"); //This module will be available to all roles
    }
 /**

	 * Whether this module will be related to a lesson

	 *

	 * @see libraries/EfrontModule#isLessonModule()

	 */
    public function isLessonModule() {
  return true;
 }
 /**

	 * Put any installation commands here, usually creating database tables

	 *

	 * @see libraries/EfrontModule#onInstall()

	 */
    public function onInstall() {
        eF_executeQuery("drop table if exists module_demo_data");
        eF_executeQuery("CREATE TABLE module_demo_data(id int(11) not null auto_increment primary key,timestamp int(11) default 0,data text not null)");
     return true;
    }
 /**

	 * Put any uninstallation operations here, usually deleting database tables

	 *

	 * @see libraries/EfrontModule#onUninstall()

	 */
    public function onUninstall() {
        eF_executeQuery("drop table if exists module_demo_data");
     return true;
    }
 /**

	 * Put any upgrade commands here, usually database table related

	 *

	 * @see libraries/EfrontModule#onUpgrade()

	 */
    public function onUpgrade() {
     try {
         eF_executeQuery("ALTER TABLE module_demo_data change timestamp timestamp int(11) default 0");
     } catch (Exception $e) {/*the table was already upgraded*/}
     return true;
    }
 /**

	 * Get the current user accessing the module, which by default is the same

	 * as the user currently logged in.

	 *

	 * @see libraries/EfrontModule#getCurrentUser()

	 */
    public function getCurrentUser() {
     return parent::getCurrentUser();
    }
 /**

	 * Get the current course, if one is set

	 *

	 * @see libraries/EfrontModule#getCurrentCourse()

	 */
    public function getCurrentCourse() {
     return parent::getCurrentCourse();
    }
 /**

	 * Get the current lesson, if one is set

	 *

	 * @see libraries/EfrontModule#getCurrentLesson()

	 */
    public function getCurrentLesson() {
     return parent::getCurrentLesson();
    }
 /**

	 * Get the current unit, if one is set

	 *

	 * @see libraries/EfrontModule#getCurrentUnit()

	 */
    public function getCurrentUnit() {
     return parent::getCurrentUnit();
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#setMessageVar()

     */
    public function setMessageVar($message, $message_type) {
     parent::setMessageVar($message, $message_type);
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#addEvent()

     */
    public function addEvent($type, $data) {
     return parent::addEvent($type, $data);
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getEventMessage()

     */
    public function getEventMessage($type, $data) {
     return parent::getEventMessage($type, $data);
    }
    /**

     * Pick a few of the efront scripts to be included

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#addScripts()

     */
    public function addScripts() {
     return array('scriptaculous/slider');
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
     $form = new HTML_QuickForm("demo_form", "post", $this -> moduleBaseUrl, "", null, true);
  $form -> addElement('text', 'data', _MODULE_DEMO_TEXTFIELD, 'class = "inputText"');
     $options = array_merge(array('format' => getDateFormat().' H:i',
             'minYear' => date("Y") - 4,
             'maxYear' => date("Y") + 3));
  $form -> addElement('date', 'timestamp', _DATE, $options);
  $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
  $form -> setDefaults(array('timestamp' => time()));
  if ($form -> isSubmitted() && $form -> validate()) {
   try {
    $values = $form -> exportValues();
    $timestamp = mktime($values['timestamp']['H'] ? $values['timestamp']['H'] : 0,
         $values['timestamp']['i'] ? $values['timestamp']['i'] : 0,
         0,
         $values['timestamp']['M'],
         $values['timestamp']['d'],
         $values['timestamp']['Y']);
    eF_insertTableData("module_demo_data", array('data' => $values['data'], 'timestamp' => $timestamp));
    eF_redirect($this -> moduleBaseUrl.'&message='.urlencode(_MODULE_DEMO_SUCCESSFULLYUPDATEDDATA).'&message_type=success');
    exit;
   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $this -> setMessageVar($message, 'failure');
   }
  }
  $smarty -> assign("T_DEMO_FORM", $form -> toArray());
  try {
   if (isset($_GET['ajax']) && $_GET['ajax'] == 'demoTable') {
    $this -> getAjaxResults();
    $smarty -> display($this -> moduleBaseDir . "module_demo_page.tpl");
    exit;
   } elseif (isset($_GET['ajax']) && isset($_GET['delete_data']) && eF_checkParameter($_GET['delete_data'], 'id')) {
    eF_deleteTableData("module_demo_data", "id=".$_GET['delete_data']);
    echo json_encode(array('status' => 1));
    exit;
   }
  } catch (Exception $e) {
   handleAjaxExceptions($e);
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
     return $this -> moduleBaseDir."module_demo_page.tpl";
    }
    /**

     * Code to execute on the lesson page

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLessonModule()

     */
    public function getLessonModule() {
     $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEURL" , $_SERVER['PHP_SELF'].'?ctg=control_panel');
        try {
         if (isset($_GET['ajax']) && $_GET['ajax'] == 'demoTable') {
          $this -> getAjaxResults();
          $smarty -> display($this -> moduleBaseDir . "module_demo_lessonpage.tpl");
          exit;
         }
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        return true;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLessonSmartyTpl()

     */
    public function getLessonSmartyTpl() {
        return $this -> moduleBaseDir."module_demo_lessonpage.tpl";
    }
    /**

     * Code executed when inside a content unit

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getContentSideInfo()

     */
    public function getContentSideInfo() {
        return true;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getContentSmartyTpl()

     */
    public function getContentSmartyTpl() {
        return $this -> moduleBaseDir."module_demo_content_side.tpl";
    }
    /**

     * If false, then the module title will appear

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getContentSideTitle()

     */
    public function getContentSideTitle() {
     return _MODULE_DEMO_CONTENTTOOLS;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getControlPanelModule()

     */
    public function getControlPanelModule() {
     $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEURL" , $_SERVER['PHP_SELF'].'?ctg=control_panel');
        try {
         if (isset($_GET['ajax']) && $_GET['ajax'] == 'demoTable') {
          $this -> getAjaxResults();
          $smarty -> display($this -> moduleBaseDir . "module_demo_lessonpage.tpl");
          exit;
         }
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        return true;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getControlPanelSmartyTpl()

     */
    public function getControlPanelSmartyTpl() {
     return $this -> moduleBaseDir."module_demo_cpanelpage.tpl";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getDashboardModule()

     */
    public function getDashboardModule() {
     $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEURL" , $_SERVER['PHP_SELF'].'?ctg=personal');
        try {
         if (isset($_GET['ajax']) && $_GET['ajax'] == 'demoTable') {
          $this -> getAjaxResults();
          $smarty -> display($this -> moduleBaseDir . "module_demo_dashboard.tpl");
          exit;
         }
        } catch (Exception $e) {
         handleAjaxExceptions($e);
        }
        return true;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getDashboardSmartyTpl()

     */
    public function getDashboardSmartyTpl() {
     return $this -> moduleBaseDir."module_demo_dashboard.tpl";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCatalogModule()

     */
    public function getCatalogModule() {
        return true;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCatalogSmartyTpl()

     */
    public function getCatalogSmartyTpl() {
     return $this -> moduleBaseDir."module_demo_catalog.tpl";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLandingPageModule()

     */
    public function getLandingPageModule() {
        return true;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLandingPageSmartyTpl()

     */
    public function getLandingPageSmartyTpl() {
     return $this -> moduleBaseDir."module_demo_landing_page.tpl";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getTabSmartyTpl()

     */
    public function getTabSmartyTpl($tabberIdentifier) {
     switch ($tabberIdentifier) {
      case 'branches':
       $tabData = array('tab' => 'branch_demo_tab',
            'title' => _MODULE_DEMO_BRANCHDEMOTAB,
            'file' => $this -> moduleBaseDir.'module_demo_branch_tab.tpl');
       break;
      case 'rules':
       $tabData = array('tab' => 'rules_demo_tab',
            'title' => _MODULE_DEMO_RULESDEMOTAB,
            'file' => $this -> moduleBaseDir.'module_demo_rules_tab.tpl');
       break;
      default:break;
     }
        return $tabData;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getModuleJS()

     */
    public function getModuleJS() {
        return $this->moduleBaseDir."module_demo.js";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getModuleCSS()

     */
    public function getModuleCSS() {
        return $this->moduleBaseDir."module_demo_css.css";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getNavigationLinks()

     */
    public function getNavigationLinks() {
        return array (array ('title' => _HOME, 'link' => $_SERVER['PHP_SELF']),
                      array ('title' => $this -> getName(), 'link' => $this -> moduleBaseUrl));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLinkToHighlight()

     */
    public function getLinkToHighlight() {
        return false;
     }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCenterLinkInfo()

     */
    public function getCenterLinkInfo() {
     return array('title' => $this -> getName().' (getCenterLinkInfo())',
                     'image' => $this -> moduleBaseLink . 'img/logo.png',
                     'link' => $this -> moduleBaseUrl);
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLessonCenterLinkInfo()

     */
    public function getLessonCenterLinkInfo() {
     return array('title' => $this -> getName().' (getLessonCenterLinkInfo())',
                     'image' => $this -> moduleBaseLink . 'img/logo.png',
                     'link' => $this -> moduleBaseUrl);
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontModule#getToolsLinkInfo()

     */
    public function getToolsLinkInfo() {
     return array('title' => $this -> getName().' (getToolsLinkInfo())',
                     'image' => $this -> moduleBaseLink . 'img/logo.png',
                     'link' => $this -> moduleBaseUrl);
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getSidebarLinkInfo()

     */
    public function getSidebarLinkInfo() {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onNewUser()

     */
    public function onNewUser($login) {
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%login%', formatLogin($login), _MODULE_DEMO_CREATEDUSER)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onDeleteUser()

     */
    public function onDeleteUser($login) {
  eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%login%', formatLogin($login), _MODULE_DEMO_DELETEDUSER)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onNewLesson()

     */
    public function onNewLesson($lessonId) {
     $lessonName = eF_getTableData("lessons", "name", "id=$lessonId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%lesson%', $lessonName, _MODULE_DEMO_CREATEDLESSON)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onDeleteLesson()

     */
    public function onDeleteLesson($lessonId) {
     $lessonName = eF_getTableData("lessons", "name", "id=$lessonId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%lesson%', $lessonName, _MODULE_DEMO_DELETEDLESSON)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onDeleteCourse()

     */
    public function onDeleteCourse($courseId) {
     $courseName = eF_getTableData("courses", "name", "id=$courseId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%course%', $courseName, _MODULE_DEMO_DELETEDCOURSE)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onRevokeCourseCertificate()

     */
    public function onRevokeCourseCertificate($login, $courseId) {
     $courseName = eF_getTableData("courses", "name", "id=$courseId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace(array('%course%', '%login%'), array($courseName, formatTimestamp($login)), _MODULE_DEMO_REVOKEDCERTIFICATE)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onIssueCourseCertificate()

     */
    public function onIssueCourseCertificate($login, $courseId, $certificateArray) {
     $courseName = eF_getTableData("courses", "name", "id=$courseId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace(array('%course%', '%login%'), array($courseName, formatTimestamp($login)), _MODULE_DEMO_ISSUEDCERTIFICATE)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onPrepareCourseCertificate()

     */
    public function onPrepareCourseCertificate($login, $courseId, $certificateData) {
     $courseName = eF_getTableData("courses", "name", "id=$courseId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace(array('%course%', '%login%'), array($courseName, formatTimestamp($login)), _MODULE_DEMO_PREPAREDCERTIFICATE)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onExportCourse()

     */
    public function onExportCourse($courseId) {
     $courseName = eF_getTableData("courses", "name", "id=$courseId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%course%', $courseName, _MODULE_DEMO_EXPORTEDCOURSE)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onImportCourse()

     */
    public function onImportCourse($courseId, $data) {
     $courseName = eF_getTableData("courses", "name", "id=$courseId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%course%', $courseName, _MODULE_DEMO_IMPORTEDCOURSE)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onNewCourse()

     */
    public function onNewCourse($courseId) {
     $courseName = eF_getTableData("courses", "name", "id=$courseId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%course%', $courseName, _MODULE_DEMO_CREATEDCOURSE)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onCompleteCourse()

     */
    public function onCompleteCourse($courseId, $login) {
     $courseName = eF_getTableData("courses", "name", "id=$courseId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace(array('%course%', '%login%'), array($courseName, formatTimestamp($login)), _MODULE_DEMO_COMPLETEDCOURSE)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onResetProgressInCourse($courseId, $login)

     */
    public function onResetProgressInCourse($courseId, $login) {
     $courseName = eF_getTableData("courses", "name", "id=$courseId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace(array('%course%', '%login%'), array($courseName, formatTimestamp($login)), _MODULE_DEMO_RESETCOURSE)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onResetProgressInCourse($login)

     */
    public function onResetProgressInAllCourses($login) {
     $courseName = eF_getTableData("courses", "name", "id=$courseId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%login%', formatLogin($login), _MODULE_DEMO_RESETALLCOURSE)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onExportLesson()

     */
    public function onExportLesson($lessonId) {
     $lessonName = eF_getTableData("lessons", "name", "id=$lessonId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%lesson%', $lessonName, _MODULE_DEMO_EXPORTEDLESSON)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onImportLesson()

     */
    public function onImportLesson($lessonId, $data) {
     $lessonName = eF_getTableData("lessons", "name", "id=$lessonId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%lesson%', $lessonName, _MODULE_DEMO_IMPORTEDLESSON)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onCompleteLesson()

     */
    public function onCompleteLesson($lessonId, $login) {
     $lessonName = eF_getTableData("lessons", "name", "id=$lessonId");
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace(array('%lesson%', '%login%'), array($lessonName, formatTimestamp($login)), _MODULE_DEMO_COMPLETEDLESSON)));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onNewPageLoad()

     */
    public function onNewPageLoad() {
     $this -> fooBar(); //Executed at the beginning of each page load
     return true;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onSetTheme()

     */
    public function onSetTheme($theme) {
        eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => "Activated theme {$theme->themes['name']}"));
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onDeleteTheme()

     */
    public function onDeleteTheme($theme) {
     eF_insertTableData("module_demo_data", array("timestamp" => time(), "data" => str_replace('%theme%', $theme, _MODULE_DEMO_DELETETHEME)));
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontModule#getTabPageSmartyTpl($tabPageIdentifier)

     */
    public function getTabPageSmartyTpl($tabPageIdentifier) {
     switch ($tabPageIdentifier) {
      case 'course_settings':
       $tabPageData = array('tab_page' => 'course_settings_demo_tab', //Use an existing name, to overwrite an existing functionality
             'title' => _MODULE_DEMO_COURSESETTINGSTABPAGE,
             'image' => $this -> moduleBaseLink.'img/generic.png',
             'file' => $this -> moduleBaseDir.'module_demo_course_settings_tab_page.tpl');
       break;
      default:break;
     }
        return $tabPageData;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontModule#getFieldsetSmartyTpl($fieldsetIdentifier)

     */
    public function getFieldsetSmartyTpl($fieldsetIdentifier) {
     switch ($fieldsetIdentifier) {
      case 'lesson_progress':
       $fieldsetData = array('fieldset' => 'lesson_progress_demo_fieldset', //Use an existing name, to overwrite an existing functionality
              'title' => _MODULE_DEMO_LESSONPROGRESSFIELDSET,
              'file' => $this -> moduleBaseDir.'module_demo_lesson_progress_fieldset.tpl');
       break;
      default:break;
     }
        return $fieldsetData;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontModule#onPageFinishLoadingSmartyTpl()

     */
    public function onPageFinishLoadingSmartyTpl() {
     $this -> fooBar();
     return $this -> moduleBaseDir.'module_demo_page_finish.tpl';
     //Return false if you don't want any code to display
     //return false;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontModule#onIndexPageLoad()

     */
    public static function onIndexPageLoad() {
     //Return false if you don't want any code to display
     return false;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontModule#onBeforeShowContent()

     */
    public function onBeforeShowContent(&$unit) {
     $unit['data'] = 'This unit data has changed from the Demo module<br>'.$unit['data'];
     return true;
    }
    private function fooBar() {
     //Do nothing!
     return true;
    }
    private function getAjaxResults() {
     $smarty = $this -> getSmartyVar();
     $demoData = eF_getTableData("module_demo_data", "*");
     isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
     if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
      $sort = $_GET['sort'];
      isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
     } else {
      $sort = 'login';
     }
     $demoData = eF_multiSort($demoData, $sort, $order);
     $smarty -> assign("T_TABLE_SIZE", sizeof($demoData));
     if (isset($_GET['filter'])) {
      $demoData = eF_filterData($demoData, $_GET['filter']);
     }
     if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
      isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
      $demoData = array_slice($demoData, $offset, $limit);
     }
     $smarty -> assign("T_DATA_SOURCE", $demoData);
    }
}
