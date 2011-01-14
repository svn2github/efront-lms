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
        eF_executeQuery("CREATE TABLE module_demo_data(id int(11) not null auto_increment primary key,
                      data text not null)");
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
         eF_executeQuery("ALTER TABLE module_demo_data add (timestamp int(11) default 0)");
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

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#addScripts()

     */
    public function addScripts() {
     return array();
    }
    /**

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
  $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
  if ($form -> isSubmitted() && $form -> validate()) {
   try {
    $values = $form -> exportValues();
    if (eF_checkParameter($values['data'], 'text')) {
     eF_insertTableData("module_demo_data", array('data' => $values['data']));
    }
   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
   }
  }
  $smarty -> assign("T_DEMO_FORM", $form -> toArray());
        return true;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getSmartyTpl()

     */
    public function getSmartyTpl() {
     return $this -> moduleBaseDir."module_demo_page.tpl";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLessonModule()

     */
    public function getLessonModule() {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLessonSmartyTpl()

     */
    public function getLessonSmartyTpl() {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getContentSideInfo()

     */
    public function getContentSideInfo() {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getContentSmartyTpl()

     */
    public function getContentSmartyTpl() {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getContentSideTitle()

     */
    public function getContentSideTitle() {
     return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getControlPanelModule()

     */
    public function getControlPanelModule() {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getControlPanelSmartyTpl()

     */
    public function getControlPanelSmartyTpl() {
        $smarty = $this->getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getDashboardModule()

     */
    public function getDashboardModule() {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getDashboardSmartyTpl()

     */
    public function getDashboardSmartyTpl() {
        $smarty = $this->getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCatalogModule()

     */
    public function getCatalogModule() {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCatalogSmartyTpl()

     */
    public function getCatalogSmartyTpl() {
        $smarty = $this->getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLandingPageModule()

     */
    public function getLandingPageModule() {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLandingPageSmartyTpl()

     */
    public function getLandingPageSmartyTpl() {
        $smarty = $this->getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
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
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getModuleCSS()

     */
    public function getModuleCSS() {
        return false;
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
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "administrator") {
            return array('title' => $this -> getName(),
                         'image' => $this -> moduleBaseDir . 'images/logo.png',
                         'link' => $this -> moduleBaseUrl);
        }
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getLessonCenterLinkInfo()

     */
    public function getLessonCenterLinkInfo() {
        return false;
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
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onDeleteUser()

     */
    public function onDeleteUser($login) {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onNewLesson()

     */
    public function onNewLesson($lessonId) {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onDeleteLesson()

     */
    public function onDeleteLesson($lessonId) {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onDeleteCourse()

     */
    public function onDeleteCourse($courseId) {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onRevokeCourseCertificate()

     */
    public function onRevokeCourseCertificate($login, $courseId) {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onIssueCourseCertificate()

     */
    public function onIssueCourseCertificate($login, $courseId, $certificateArray) {
     return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onPrepareCourseCertificate()

     */
    public function onPrepareCourseCertificate($login, $courseId, $certificateData) {
     return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onExportCourse()

     */
    public function onExportCourse($courseId) {
     return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onImportCourse()

     */
    public function onImportCourse($courseId, $data) {
     return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onNewCourse()

     */
    public function onNewCourse($courseId) {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onCompleteCourse()

     */
    public function onCompleteCourse($courseId, $login) {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onResetProgressInCourse($courseId, $login)

     */
    public function onResetProgressInCourse($courseId, $login) {
     return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onResetProgressInCourse($login)

     */
    public function onResetProgressInAllCourses($login) {
     return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onExportLesson()

     */
    public function onExportLesson($lessonId) {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onImportLesson()

     */
    public function onImportLesson($lessonId, $data) {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onCompleteLesson()

     */
    public function onCompleteLesson($lessonId, $login) {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onNewPageLoad()

     */
    public function onNewPageLoad() {
        return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onSetTheme()

     */
    public function onSetTheme($theme) {
     return false;
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#onDeleteTheme()

     */
    public function onDeleteTheme($theme) {
     return false;
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
             'image' => $this -> moduleBaseLink.'images/generic.png',
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
    private function fooBar() {
     //Do nothing!
     return true;
    }
}
?>
