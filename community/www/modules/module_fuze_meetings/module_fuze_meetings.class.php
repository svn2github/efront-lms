<?php

require_once 'FUZE_class_includer.php';

/**

 * The main class that encapsulates the basic module behaviour and state

 * and also handles requests to the module backend.

 * 

 * @name module_fuze_meetings

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class module_fuze_meetings extends EfrontModule {
 private $_message;
 private $_form_error;
 private $_settings;
 private $_f_account;
 private $_current_user;
 private $_current_user_id;
 private $_current_user_login;
 private $_current_user_role;
 private $_current_user_timezone;
 private $_current_user_fullname;
 private $_current_user_email;
 private $_current_user_lessons_as_student;
 private $_current_user_lessons_as_professor;
 private $_current_lesson;
 private $_current_lesson_id;
 private $_current_lesson_students;
 private $_current_lesson_professors;
 private $_f_user_manager;
 // Used only in the case that the curent user is having a student role
 private $_current_student_meetings;
 private $_current_student_five_next_meetings;
 public function __construct() {
  global $currentUser;
  if (G_VERSIONTYPE == 'community' || !$currentUser) return false;
  $defined_moduleBaseUrl = '';
  if ($currentUser->getType() == 'administrator') $defined_moduleBaseUrl = 'administrator.php?ctg=modules&op=module_fuze_meetings';
  elseif ($currentUser->getType() == 'professor') $defined_moduleBaseUrl = 'professor.php?ctg=modules&op=module_fuze_meetings';
  else $defined_moduleBaseUrl = 'student.php?ctg=modules&op=module_fuze_meetings';
  parent::__construct($defined_moduleBaseUrl,"module_fuze_meetings");
  $this->_init();
 }
 protected function _init() {
  try {
   $this->_getFUZEAccountInstance();
   $this->_current_user = $this->getCurrentUser();
   $this->_current_user_id = $this->_current_user->user['id'];
   $this->_current_user_timezone = $this->_current_user->user['timezone'];
   if (!trim($this->_current_user_timezone)) $this->_current_user_timezone = 'UTC';
   $this->_current_user_login = $this->_current_user->user['login'];
   $this->_current_user_fullname = $this->_current_user->user ['name'] . ' ' . $this->_current_user->user ['surname'];
   $this->_current_user_email = $this->_current_user->user ['email'];
   $this->_current_lesson = $this->getCurrentLesson();
   if ($this->_current_lesson) {
    $this->_current_lesson_id = $this->_current_lesson->lesson['id'];
    $this->_current_lesson_students = array_keys($this->_current_lesson->getUsers('student'));
    $this->_current_lesson_professors = array_keys($this->_current_lesson->getUsers('professor'));
    $this->_current_user_role = $this->_getCurrentUserRole();
    $this->_current_user_lessons_as_professor = array_keys($this->_current_user->getLessons(false,'professor'));
    $this->_current_user_lessons_as_student = array_keys($this->_current_user->getLessons(false,'student'));
    if ($this->_current_user_role == 'student') {
     // We need to initialise the meetings that are scheduled for this student and lesson
     $this->_current_student_meetings = array();
     $this->_current_student_five_next_meetings = array();
     $timestamp = time() - 300; // 5 minutes ago
     $_sql_tables = "`_mod_fm_meeting` AS `_mfm`, `_mod_fm_meeting_attendee` AS `_mfma`";
     $_sql_fields = "DISTINCT(`_mfm`.`id`) AS `_mid`";
     $_sql_where = "`_mfm`.`lesson_id` = {$this->_current_lesson_id} AND `_mfm`.`starttime` > {$timestamp} AND `_mfma`.`meeting_id` = `_mfm`.`id` AND `_mfma`.`sys_id` = {$this->_current_user_id}";
     $_sql_order = "`_mfm`.`starttime`";
     try {
      $res = eF_getTableData($_sql_tables, $_sql_fields, $_sql_where, $_sql_order);
      foreach ($res AS $entry) {
       $this->_current_student_meetings [$entry ['_mid']] = null;
       if (count($this->_current_student_five_next_meetings) < 5) {
        // We only get the 5 next meetings to present on cpanel.
        $this->_current_student_five_next_meetings [$entry ['_mid']] = null;
       }
      }
     }
     catch (Exception $e) { /* DO NOTHING */ }
    }
   }
   if ($this->_f_account->isRegistered()) {
    $this->_f_user_manager = new FUZE_User_Manager();
   }
  }
  catch (Exception $e) {
   $sql = "show tables like '_mod_fm_account%'";
   $res = $GLOBALS['db']->GetAll($sql);
   if (!count($res)) {
    $this->onInstall();
   }
   else {
    throw $e;
   }
  }
 }
 public function getName() {
  return _FUZE_MEETINGS;
 }
 public function getPermittedRoles() {
  return array('administrator','professor','student');
 }
 public function isLessonModule() {
        return true;
    }

 public function getControlPanelModule() {
  return true;
 }

 public function getSidebarLinkInfo() {
  $links = array();
  if ($this->_current_user->getType() == 'administrator') {
   $links['id'] = 'mod_fuze_meetings_id1';
   $links['title'] = _FUZE_MEETINGS;
   $links['image'] = $this -> moduleBaseDir . 'images/16x16/logo16';
   $links['eFrontExtensions'] = '1';
   $links['link'] = $this->moduleBaseUrl;
   $links = array('other' => array('menuTitle' => "Modules", 'links' => array($links)));
  }
  elseif ($this->_current_user->getType() == 'professor') {
   $links['id'] = 'mod_fuze_meetings_id1';
   $links['title'] = _FUZE_MEETINGS;
   $links['image'] = $this -> moduleBaseDir . 'images/16x16/logo16';
   $links['eFrontExtensions'] = '1';
   $links['link'] = $this->moduleBaseUrl;
   $links = array('other' => array('menuTitle' => "Modules", 'links' => array($links)));
  }
  elseif ($this->_current_user->getType() == 'student') {
   $links['id'] = 'mod_fuze_meetings_id1';
   $links['title'] = _FUZE_MEETINGS;
   $links['image'] = $this -> moduleBaseDir . 'images/16x16/logo16';
   $links['eFrontExtensions'] = '1';
   $links['link'] = $this->moduleBaseUrl;
   $links = array('other' => array('menuTitle' => "Modules", 'links' => array($links)));
  }

  return $links;
 }

 public function getLinkToHighlight() {
  return 'mod_fuze_meetings_id1';
 }

 /**

	 * Takes care of the navigation links on the top of the page. Different 

	 * for different users, depending on their role in regard to the given 

	 * current course or lesson.

	 * 

	 * @see libraries/EfrontModule::getNavigationLinks()

	 * @access public

	 */
 public function getNavigationLinks() {
  $home_link = '';
  if ($this->_current_user->getType() == 'administrator') {
   $home_link = 'administrator.php?ctg=control_panel';
  }
  elseif ($this->_current_user->getType() == 'professor') {
   $home_link = 'professor.php?ctg=control_panel';
  }
  else {
   $home_link = 'student.php?ctg=control_panel';
  }
  $navigation_links = array();
  $navigation_links [] = array('title' => 'Home', 'link' => $home_link);
  $navigation_links [] = array('title' => _FUZE_MEETINGS, 'link' => $this->moduleBaseUrl);
  $action = (isset($_GET['action']) ? strtolower($_GET['action']) : false);
  if ($this->_current_user_role == 'professor') {
   $navigation_links = array();
   $navigation_links [] = array ('title' => _MYCOURSES, 'link' => 'professor.php?ctg=lessons');
   $navigation_links [] = array ('title' => $this->_current_lesson -> lesson['name'], 'link' => 'professor.php?ctg=control_panel');
   $navigation_links [] = array ('title' => _FUZE_MEETINGS, 'link' => $this->moduleBaseUrl);
   if ($action == 'meeting_schedule_prep') {
    $title = _FUZE_MEETINGS_NAV_TITLE_SCHEDULE;
    $link = $this->moduleBaseUrl . '&action=meeting_schedule_prep';
    $navigation_links [] = array('title' => $title, 'link' => $link);
   }
   elseif ($action == 'meeting_edit_prep') {
    $title = _FUZE_MEETINGS_NAV_TITLE_EDIT;
    $link = $this->moduleBaseUrl . '&action=meeting_edit_prep';
    $navigation_links [] = array('title' => $title, 'link' => $link);
   }
   elseif ($action == 'meeting_host_prep') {
    $title = _FUZE_MEETINGS_NAV_TITLE_HOST;
    $link = $this->moduleBaseUrl . '&action=meeting_host_prep';
    $navigation_links [] = array('title' => $title, 'link' => $link);
   }
  }
  return $navigation_links;
 }
 public function getCenterLinkInfo(){
  $center_link_info = false;
  if ($this->_current_user->getType() == 'administrator') {}
  elseif ($this->_current_user_role == 'professor') {
   $center_link_info = array(
         'title' => _FUZE_MEETINGS,
         'image' => $this->moduleBaseDir.'images/logo_32.png',
         'link' => $this->moduleBaseUrl
        );
  }
  return $center_link_info;
 }
 public function getLessonCenterLinkInfo() {
  $center_link_info = false;
  if ($this->_current_user_role == 'professor') {
   if ($this->_f_account && $this->_f_account->isRegistered()) {
    $fuze_user = false;
    try {
     $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
    }
    catch (Exception $e) { /* DO NOTHING */ }
    if ($fuze_user) {
     $center_link_info = array(
         'title' => _FUZE_MEETINGS,
         'image' => $this->moduleBaseDir.'images/logo_32.png',
         'link' => $this->moduleBaseUrl
        );
    }
   }
  }
  return $center_link_info;
 }

 public function getModule() { return true; }

 public function getLessonModule() {
  if ($this->_f_account && $this->_f_account->isRegistered()) {
   return true;
  }
  else {
   return false;
  }
 }

 public function getLessonSmartyTpl() {
  if ($this->_f_account && $this->_f_account->isRegistered()) {
   return $this->getControlPanelSmartyTpl();
  }
  else {
   return false;
  }
 }

 public function getControlPanelSmartyTpl() {
  $tpl = false;
  $smarty = $this -> getSmartyVar();
  $smarty->assign("MOD_FM_BASEURL", $this->moduleBaseUrl);
  $smarty->assign("MOD_FM_BASELINK", $this->moduleBaseLink);
  $smarty->assign("MOD_FM_BASEDIR", $this->moduleBaseDir);
  $smarty->assign("MOD_FM_MODULES_BASEURL", G_MODULESURL);
  $smarty->assign("MOD_FM_MODULES_BASEPATH", G_MODULESPATH);
  if ($this->_f_account->isRegistered()) {
   if ($this->_current_user->getType() == 'administrator') {
    ///////////////////////////////////////////////////////////
    // Fetching all professor id's and names, the local accounts
    $local_accounts = array();
    $res = eF_getTableData("`users`","`id`,CONCAT(`surname`,', ',`name`) AS `nick`","`user_type` = 'professor' AND `active` = 1 AND (`archive` = 0 OR `archive` IS NULL)", "`nick`");
    if ($res && is_array($res) && count($res)) {
     foreach ($res AS $entry) {
      $local_accounts [] = array('id' => $entry ['id'], 'name' => $entry ['nick']);
     }
    }
    $smarty->assign("MOD_FM_ADMIN_CPANEL_ACCOUNTS",$local_accounts);
    // DONE with collecting professor local data
    ///////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////
    // Fetching FUZE account count
    //
    // TO-DO
    // At this point we could cross-reference the FUZE accounts
    // against the local accounts and do a little tidying up of
    // the FUZE accounts in case some local accounts have been 
    // removed from the system.
    $fuze_active_accounts = $this->_getActiveUserCount();
    $smarty->assign("MOD_FM_ADMIN_CPANEL_ACCOUNTS_AMOUNT",$fuze_active_accounts);
    // DONE with FUZE account data retrieval
    ///////////////////////////////////////////////////////////
          $tpl = $this->moduleBaseDir . 'views/smarty.admin.mod_fm_cpanel.tpl';
   }
   elseif ($this->_current_user_role == 'professor') {
    $fuze_user = false;
    try {
     $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
    }
    catch (Exception $e) { /* DO NOTHING */ }
    if ($fuze_user && !$fuze_user->isSuspended()) {
     $tpl = $this->_prof_default_cpanel($smarty, $fuze_user);
    }
    elseif (!$fuze_user) {
     $smarty->assign("_FUZE_PROF_VAR_USER_ID",$this->_current_user_id);
     $tpl = $this->moduleBaseDir . 'views/smarty.professor.mod_fm_cpanel_register.tpl';
    }
    else {
     $tpl = $this->moduleBaseDir . 'views/smarty.professor.mod_fm_cpanel_suspended.tpl';
    }
   }
   elseif ($this->_current_user_role == 'student') {
    if (count($this->_current_student_five_next_meetings)) {
     $tpl = $this->_student_default_cpanel($smarty);
    }
    else {
     $tpl = $this->moduleBaseDir . 'views/smarty.student.mod_fm_cpanel_no_meetings.tpl';
    }
   }
  }
  else {
   // If the FUZE module is not registered we allow only the system admin to interact with it.
   if ($this->_current_user->getType() == 'administrator') {
          $tpl = $this->moduleBaseDir . 'views/smarty.admin.mod_fm_cpanel_register.tpl';
         }
  }

        return $tpl;
 }

 public function getSmartyTpl() {
  // This is to load the textareas using tiny_MCE
  global $load_editor;
  $load_editor = true;
  // END tiny_MCE
  $template = false;
  $smarty = $this -> getSmartyVar();
  $smarty->assign("MOD_FM_BASEURL", $this->moduleBaseUrl);
  $smarty->assign("MOD_FM_BASELINK", $this->moduleBaseLink);
  $smarty->assign("MOD_FM_BASEDIR", $this->moduleBaseDir);
  $smarty->assign("MOD_FM_MODULES_BASEURL", G_MODULESURL);
  $smarty->assign("MOD_FM_MODULES_BASEPATH", G_MODULESPATH);
  $controller_action = false;
  if (isset($_GET['action'])) { $controller_action = strtolower($_GET['action']); }
  ///////////////////////////////////////////////////////////
  // Functionality available to administrators starts here //
  if ($this->_current_user->getType() == 'administrator') {
   if (!$this->_f_account->isRegistered()) {
    // Only a register action can be accepted here
    if ($controller_action == 'register') {
     $response = $this->_admin_register();
     die(json_encode($response));
    }
   }
   else {
    $id = $_GET['local_id'];
    // Only a user_create, user_suspend and user_show actions can be accepted here 
    if ($controller_action == 'user_create') {
     $response = $this->_admin_user_create($id);
     die(json_encode($response));
    }
    elseif ($controller_action == 'user_suspend') {
     $response = $this->_admin_user_suspend($id);
     die(json_encode($response));
    }
    elseif ($controller_action == 'user_show') {
     $response = $this->_admin_user_show($id);
     die(json_encode($response));
    }
    elseif ($controller_action == 'user_login') {
     $response = $this->_admin_user_login($id);
     die(json_encode($response));
    }
   }
  }
  // Functionality available to administrators ends here ////
  ///////////////////////////////////////////////////////////

  ///////////////////////////////////////////////////////////
  // Functionality available to professors starts here //////

  // - Professors cannot use any of the module functionality unless an
  //   administrator has first registered this eFront copy with service.

  // - Professors can use the module only if they have an account. If not
  //   they can only use the function for creating a new account.

  if ($this->_current_user_role == 'professor' && $this->_f_account->isRegistered()) {
   if ($controller_action == 'user_create') {
    $id = $_GET['local_id'];
    $response = $this->_prof_user_create($id);
    die(json_encode($response));
   }
   elseif ($controller_action == 'user_login') {
    $response = $this->_prof_user_login();
    die(json_encode($response));
   }
   elseif ($controller_action == 'meeting_schedule_prep') {
    // This is to rpesent the user with the form to schedule the meeting
    $this->_prof_schedule_unset_lesson();
    $template = $this->_prof_internal_schedule_prep($smarty);
   }
   elseif ($controller_action == 'meeting_schedule') {
    // This is to save the newly scheduled meeting
    $response = $this->_prof_internal_schedule();
    die(json_encode($response));
   }
   elseif ($controller_action == 'meeting_start') {
    // This is when the user clicks on the 'Go to meeting' link on their cpanel
    $response = $this->_prof_internal_start();
    die(json_encode($response));
   }
   elseif ($controller_action == 'meeting_host_prep') {
    // This is to present the user with the form they need to prepare 
    // the meeting that they want to start hosting immediately after 
    // they're done setting subject etc.
    $template = $this->_prof_internal_host_prep($smarty);
   }
   elseif ($controller_action == 'meeting_host') {
    // This is to actually start hosting the site that was just 
    // configured and is to start immediately after configuring.
    $response = $this->_prof_internal_host();
    die(json_encode($response));
   }
   elseif ($controller_action == 'launcher') {
    $template = $this->_prof_internal_launcher($smarty);
   }
   elseif ($controller_action == 'fetch_users') {
    $template = $this->_prof_fetch_users();
   }
   elseif ($controller_action == 'set_schedule_lesson') {
    $lesson_id = $_GET['lesson_id'];
    $this->_prof_schedule_set_lesson($lesson_id);
    die(json_encode(array('success'=>true)));
   }
   elseif ($controller_action == 'validate_date') {
    $date = $_GET['date'];
    $time = $_GET['time'];
    $response = $this->_prof_validate_date_time($date, $time);
    die(json_encode($response));
   }
   elseif ($controller_action == 'fetch_meetings') {
    $template = $this->_prof_fetch_meetings();
   }
   elseif ($controller_action == 'meeting_edit_prep') {
    $template = $this->_prof_meeting_edit_prep($smarty);
   }
   elseif ($controller_action == 'meeting_edit') {
    $response = $this->_prof_meeting_edit();
    die(json_encode($response));
   }
   elseif ($controller_action == 'meeting_cancel') {
    $response = $this->_prof_meeting_cancel();
    die(json_encode($response));
   }
   else {
    // We present all meetings in case no action is defined
    $template = $this->_prof_internal_all_meetings($smarty);
   }
  }

  // Functionality available to professors ends here ////////
  ///////////////////////////////////////////////////////////

  ///////////////////////////////////////////////////////////
  // Functionality available to students starts here ////////

  // - Students cannot use any of the module functionality unless an
  //   administrator has first registered this eFront copy with service.

  // - Students can use the module only if they are invited to one or
  //   more of the planned meetings. Otherwise they only get an empty 
  //   frame on their control panel.

  if ($this->_current_user_role == 'student' && $this->_f_account->isRegistered()) {
   /* NOTHING DEFINED AS DEFAULT VIEW FOR STUDENTS */
  }

  // Functionality available to students ends here //////////
  ///////////////////////////////////////////////////////////

  return $template;
 }

 ///////////////////////////////////////////////////////////////////////////
 // ADMIN PROCEDURES BELOW
 ///////////////////////////////////////////////////////////////////////////

 protected function _admin_user_create($id) {
  $fuze_user = false;
  if ($id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  $response = array('success' => false);
  $system_user = false;
  if (!$fuze_user) {
   try {
    $system_user = new FUZE_System_User($id);
   }
   catch (Exception $e) {
    $system_user = false;
   }
   if ($system_user) {
    // Preparing the arguments that we'll use for the creation of the fuze user.
    $args = array();
    $args ['email'] = $system_user->getSysEmail();
    $args ['firstname'] = $system_user->getFirstName();
    $args ['lastname'] = $system_user->getLastName();
    $args ['password'] = md5(uniqid('user',time()));
    $args ['sys_id'] = $id;
    $args ['creator'] = 'administrator';

    if (eF_checkParameter($args['email'], 'email')) {
     $function_response = $this->_f_user_manager->addUser($args);
     if ($function_response ['success']) {
      $fuze_user = $function_response ['user_item'];
      if ($fuze_user instanceof FUZE_User) {
       $response ['success'] = true;
       $response ['date_added'] = $fuze_user->getTranslatedDateAdded($this->_current_user_timezone);
       $response ['fuze_email'] = $fuze_user->getFuzeEmail();
       $response ['fuze_password'] = $fuze_user->getPassword();
       $response ['login_url'] = $fuze_user->getLoginUrl();
       ///////////////////////////////////////////////////////////
       // Fetching FUZE account count
       $fuze_active_accounts = $this->_getActiveUserCount();
       $response ['active_user_count'] = $fuze_active_accounts;
       // DONE with FUZE account data retrieval
       ///////////////////////////////////////////////////////////
      }
      else {
       $response ['error_msg'] = _FUZE_ADMIN_CREATE_USER_ERROR;
      }
     }
     else {
      $response ['error_msg'] = $function_response ['error_msg'];
     }
    }
    else {
     $response ['error_msg'] = _FUZE_ADMIN_ERROR_NO_VALID_EMAIL;
    }
   }
   else {
    $response ['error_msg'] = _FUZE_ADMIN_CREATE_USER_ERROR;
   }
  }
  else {
   // We already have an account for this user but the 
   // local account has been suspended. We just have to 
   // reinstate it.
   $fuze_user->unsuspend();
   $response ['success'] = true;
   $response ['date_added'] = $fuze_user->getTranslatedDateAdded($this->_current_user_timezone);
   $response ['fuze_email'] = $fuze_user->getFuzeEmail();
   $response ['fuze_password'] = $fuze_user->getPassword();
   $response ['login_url'] = $fuze_user->getLoginUrl();
   ///////////////////////////////////////////////////////////
   // Fetching FUZE account count
   // The change has not yet propagated to DB at this point so we subtract manually.
   $fuze_active_accounts = $this->_getActiveUserCount() + 1;
   $response ['active_user_count'] = $fuze_active_accounts;
   // DONE with FUZE account data retrieval
   ///////////////////////////////////////////////////////////
  }

  return $response;
 }

 protected function _admin_register() {
  // We need to collect the data necessary for the registration
  $params = array();
  $params ['contact_name'] = $this->_current_user->user['surname'] . ', ' . $this->_current_user->user['name'];
  $params ['contact_email'] = $this->_current_user->user['email'];
  $params ['g_version'] = G_VERSION_NUM;
  $params ['g_edition'] = G_VERSIONTYPE_CODEBASE;
  $response = $this->_f_account->register($params);
  if ($response === true ) {
   $response = array('success' => true);
   $response ['professor_ids'] = array();
   $response ['professor_names'] = array();
   ///////////////////////////////////////////////////////////
   // Fetching all professor id's and names
   $res = eF_getTableData("`users`","`id`,CONCAT(`surname`,', ',`name`) AS `nick`","`user_type` = 'professor' AND `active` = 1 AND (`archive` = 0 OR `archive` IS NULL)", "`nick`");
   if ($res && is_array($res) && count($res)) {
    foreach ($res AS $entry) {
     $response ['professor_ids'][] = $entry ['id'];
     $response ['professor_names'][] = $entry ['nick'];
    }
   }
   // DONE with collecting professor data
   ///////////////////////////////////////////////////////////
  }
  else {
   $response = array('success' => false, 'error_msg' => $response);
  }

  return $response;
 }

 protected function _admin_user_show($id) {
  $fuze_user = false;
  if ($id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  $response = array('success' => false);
  if ($fuze_user && !$fuze_user->isSuspended()) {
   $response ['success'] = true;
   $response ['date_added'] = $fuze_user->getTranslatedDateAdded($this->_current_user_timezone, 'D, d M Y [H:i:s]');
   $response ['fuze_email'] = $fuze_user->getFuzeEmail();
   $response ['fuze_password'] = $fuze_user->getPassword();
   $response ['login_url'] = $fuze_user->getLoginUrl();
   ///////////////////////////////////////////////////////////
   // Fetching FUZE account count
   $fuze_active_accounts = $this->_getActiveUserCount();
   $response ['active_user_count'] = $fuze_active_accounts;
   // DONE with FUZE account data retrieval
   ///////////////////////////////////////////////////////////
  }

  return $response;
 }

 protected function _admin_user_suspend($id) {
  $fuze_user = false;
  if ($id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  $response = array('success' => false);
  if ($fuze_user) {
   if ($this->_f_user_manager->suspendUserBySysId($id)) {
    $response ['success'] = true;
    ///////////////////////////////////////////////////////////
    // Fetching FUZE account count
    // The change has not yet propagated to DB at this point so we subtract manually.
    $fuze_active_accounts = $this->_getActiveUserCount() - 1;
    $response ['active_user_count'] = $fuze_active_accounts;
    // DONE with FUZE account data retrieval
    ///////////////////////////////////////////////////////////
   }
  }

  return $response;
 }

 protected function _admin_user_login($id) {
  $fuze_user = false;
  if ($id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  $response = array('success' => false);
  if ($fuze_user) {
   $function_response = $fuze_user->getUpgradeUrl();
   if ($function_response ['success']) {
    $response ['success'] = true;
    $response ['url'] = $function_response ['url'];
   }
   else {
    if ($function_response ['error_msg']) {
     $response ['error_msg'] = $function_response ['error_msg'];
    }
   }
  }

  return $response;
 }

 ///////////////////////////////////////////////////////////////////////////
 // PROFESSOR PROCEDURES BELOW
 ///////////////////////////////////////////////////////////////////////////

 protected function _prof_default_cpanel($smarty, $fuze_user) {
  // Taking the next 5 meetings for this user and lesson
  $meetings = $fuze_user->getMeetingsByLessonPage(0, 5, $this->_current_lesson_id);
  $time_description = false;
  $latest_meetings = array();
  if (count($meetings)) {
   foreach ($meetings AS $meeting_id => $meeting) {
    $latest_meetings [$meeting_id] = array();
    $latest_meetings [$meeting_id]['subject'] = $meeting->getSubject();
    if ($meeting->isHappeningNow()) {
     $latest_meetings [$meeting_id]['starttime'] = _FUZE_TIME_CPANEL_NOW;
     $latest_meetings [$meeting_id]['link'] = _FUZE_PROF_CPANEL_GO_TO_MEETING;
     $latest_meetings [$meeting_id]['meetingid'] = $meeting->getId();
    }
    else {
     $latest_meetings [$meeting_id]['link'] = 0;
     $latest_meetings [$meeting_id]['starttime'] = $meeting->getStartTimeTranslated($this->_current_user_timezone);
    }
    if (!$time_description) {
     $time_parts = FUZE_Tools::get_rough_time_description($meeting->getStartTime());
     $time_description = $this->_get_rough_time_description($time_parts);
    }
   }
  }
  // In case no meetings were found
  else {
   $time_description = _FUZE_PROF_MEETING_AMOUNT_MEETING_NONE;
  }
  $smarty->assign('_FUZE_PROF_CPANEL_MEETINGS', $latest_meetings);
  $smarty->assign('_FUZE_PROF_CPANEL_SCHEDULE_TIME_DESC', $time_description);

  return $this->moduleBaseDir . 'views/smarty.professor.mod_fm_cpanel.tpl';
 }

 protected function _prof_user_create($id) {
  $fuze_user = false;
  if ($id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  $response = array('success' => false);
  $system_user = false;
  if (!$fuze_user) {
   try {
    $system_user = new FUZE_System_User($id);
   }
   catch (Exception $e) {
    $system_user = false;
   }
   if ($system_user) {
    // Preparing the arguments that we'll use for the creation of the fuze user.
    $args = array();
    $args ['email'] = $system_user->getSysEmail();
    $args ['firstname'] = $system_user->getFirstName();
    $args ['lastname'] = $system_user->getLastName();
    $args ['password'] = md5(uniqid('user',time()));
    $args ['sys_id'] = $id;
    $args ['creator'] = 'professor';

    if (eF_checkParameter($args['email'], 'email')) {
     $function_response = $this->_f_user_manager->addUser($args);
     if ($function_response ['success']) {
      $fuze_user = $function_response ['user_item'];
      if ($fuze_user instanceof FUZE_User) {
       $response ['success'] = true;
      }
      else {
       $response ['error_msg'] = _FUZE_PROF_CREATE_USER_ERROR;
      }
     }
     else {
      $response ['error_msg'] = $function_response ['error_msg'];
     }
    }
    else {
     $response ['error_msg'] = _FUZE_PROF_ERROR_NO_VALID_EMAIL;
    }
   }
   else {
    $response ['error_msg'] = _FUZE_PROF_CREATE_USER_ERROR;
   }
  }
  ///////////////////////////////////////////////////////////
  // No facility in place for reinstating suspended accounts
  // for users of type 'professor'
  ///////////////////////////////////////////////////////////

  return $response;
 }

 protected function _prof_user_login() {
  $fuze_user = false;
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  $response = array('success' => false);
  if ($fuze_user && !$fuze_user->isSuspended()) {
   $function_response = $fuze_user->getUpgradeUrl();
   if ($function_response ['success']) {
    $response ['success'] = true;
    $response ['url'] = $function_response ['url'];
   }
   else {
    if ($function_response ['error_msg']) {
     $response ['error_msg'] = $function_response ['error_msg'];
    }
   }
  }

  return $response;
 }

 protected function _prof_internal_schedule_prep($smarty) {
  $template = false;
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   // Na vro ta stoixeia olon ton mathimaton ta opoia didaskei autos o 
   // kathigitis giati ta xreiazome sto select gia to scheduling.
   $smarty->assign('_FUZE_PROF_SCHEDULE_LESSON_ID',$this->_current_lesson_id);
   $smarty->assign('_FUZE_PROF_SCHEDULE_LESSON_NAME',$this->_current_lesson->lesson['name']);
   $all_lessons = $fuze_user->getLessons();
   $smarty->assign('_FUZE_PROF_SCHEDULE_LESSON_LIST', $all_lessons);

   $template = $this->moduleBaseDir . 'views/smarty.professor.mod_fm_schedule.tpl';
  }

  return $template;
 }

 protected function _prof_internal_schedule() {
  $response = array('success' => false);
  $args = array();
  $students = $_GET['students'];
  $students = explode(',',$students);
  $args ['starttime'] = FUZE_Tools::get_UTC_timestamp($this->_current_user_timezone, $_GET['starttime']);
  $args ['subject'] = $_GET['subject'];
  $args ['lesson_id'] = $_GET['lesson_id'];
  $args ['students'] = $students;
  $args ['send_invites'] = $_GET['send_invites'];
  $args ['add_events'] = $_GET['add_events'];
  $args ['launch'] = false;
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   $function_response = $fuze_user->addMeeting($args);
   if ($function_response ['success']) {
    $meeting_item = $function_response ['meeting_item'];
    if ($meeting_item instanceof FUZE_Meeting) {
     $response ['success'] = true;
    }
    else {
     $response ['error_msg'] = _FUZE_PROF_SCHEDULE_ERROR;
    }
   }
   elseif (isset($function_response ['url']) && !empty($function_response ['url'])) {
    $response ['url'] = $function_response ['url'];
   }
   else {
    $response ['error_msg'] = $function_response ['error_msg'];
   }
  }
  else {
   $response ['error_msg'] = _FUZE_PROF_SCHEDULE_ERROR;
  }

  return $response;
 }

 protected function _prof_schedule_set_lesson($lesson_id) {
  // This is a hack to allow us to use a different lesson than the one 
  // the professor is using right now.
  // We should unset this upon saving the data for the meeting or when 
  // calling the schedule_prep method. 
  $_SESSION['_mod_fm_schedule_lesson_id'] = $lesson_id;
 }

 protected function _prof_validate_date_time($date, $time) {
  $response = array('success' => false);
  $original_date = $date;
  $year = substr($date,0,4);
  $date = substr($date,5);
  $month = substr($date,0,stripos($date,'-'));
  $date = substr($date,stripos($date,'-')+1);
  $day = $date;

  if (checkdate($month, $day, $year)) {
   // We convert given date_time to UTC timestamp and compare against current
   $given_date_time = $original_date . ' ' . str_ireplace('-',':',$time);
   $meeting_timestamp = FUZE_Tools::get_UTC_timestamp($this->_current_user_timezone, $given_date_time);
   $current_timestamp = time();
   // We allow for the meeting to be set for up to five minutes 
   // before the actual current time as the user's clock might be a little slow
   if ($current_timestamp <= ($meeting_timestamp+300)) {
    $response ['success'] = true;
   }
  }

  return $response;
 }

 protected function _prof_schedule_unset_lesson() {
  $_SESSION['_mod_fm_schedule_lesson_id'] = null;
  unset($_SESSION['_mod_fm_schedule_lesson_id']);
 }

 protected function _prof_fetch_users() {
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   $args = array();
   if (!isset($_SESSION['_mod_fm_schedule_lesson_id'])) {
    $args ['lesson_id'] = $this->_current_lesson_id;
   }
   else {
    $args ['lesson_id'] = $_SESSION['_mod_fm_schedule_lesson_id'];
   }
   $args ['order'] = $_GET['order'];
   $args ['sort'] = $_GET['sort'];
   $args ['offset'] = $_GET['offset'];
   $args ['limit'] = $_GET['limit'];
   $args ['other'] = $_GET['other'];
   $tmp_students_array = $fuze_user->getStudentsByLessonSubset($args);
   $students_array = array();
   if (count($tmp_students_array)) {
    foreach ($tmp_students_array AS $student_id) {
     try {
      $student = new FUZE_System_User($student_id);
     }
     catch (Exception $e) { /* DO NOTHING */ }
     if ($student) {
      $students_array [$student_id] = array();
      $students_array [$student_id]['login'] = $student->getLogin();
      $students_array [$student_id]['name'] = $student->getFirstName();
      $students_array [$student_id]['surname'] = $student->getLastName();
     }
    }
   }
   $smarty = $this->getSmartyVar();
   $smarty->assign('T_TABLE_SIZE',$fuze_user->getStudentsByLessonCount($args['lesson_id']));
   $dataSource = $students_array;
   $tableName = $_GET['ajax'];
   if (isset($_GET['ajax']) && $_GET['ajax'] == $tableName) {
    if (!$alreadySorted) {
     list($tableSize, $dataSource) = filterSortPage($dataSource);
     $smarty -> assign("T_TABLE_SIZE", $tableSize);
    }
    if (!empty($dataSource)) {
     $smarty -> assign("T_DATA_SOURCE", $dataSource);
    }
    $smarty -> assign("T_SORTED_TABLE", $tableName);
   }
  }

  return $this->moduleBaseDir . 'views/smarty.professor.mod_fm_schedule.tpl';
 }

 protected function _prof_internal_host_prep($smarty) {
  $template = false;
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   // Na vro ta stoixeia olon ton mathimaton ta opoia didaskei autos o 
   // kathigitis giati ta xreiazome sto select gia tin epomeni othoni.
   $smarty->assign('_FUZE_PROF_SCHEDULE_LESSON_ID',$this->_current_lesson_id);
   $smarty->assign('_FUZE_PROF_SCHEDULE_LESSON_NAME',$this->_current_lesson->lesson['name']);
   $all_lessons = $fuze_user->getLessons();
   $smarty->assign('_FUZE_PROF_SCHEDULE_LESSON_LIST', $all_lessons);

   $template = $this->moduleBaseDir . 'views/smarty.professor.mod_fm_host.tpl';
  }

  return $template;
 }

 protected function _prof_internal_host() {
  $response = array('success' => false);
  $args = array();
  $students = $_GET['students'];
  $students = explode(',',$students);
  $args ['starttime'] = FUZE_Tools::get_UTC_timestamp($this->_current_user_timezone, date('r',time()));
  $args ['subject'] = $_GET['subject'];
  $args ['lesson_id'] = $_GET['lesson_id'];
  $args ['students'] = $students;
  $args ['launch'] = true;
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   $function_response = $fuze_user->addMeeting($args);
   if ($function_response ['success']) {
    $meeting_item = $function_response ['meeting_item'];
    if ($meeting_item instanceof FUZE_Meeting) {
     try {
      $url = $meeting_item->getLaunchNowUrl();
      if (is_string($url)) {
       $response ['success'] = true;
       $response ['url'] = $url;
      }
     }
     catch (Exception $e) {
      $response ['error_msg'] = $e->getMessage();
     }
    }
   }
   elseif (isset($function_response ['url']) && !empty($function_response ['url'])) {
    $response ['url'] = $function_response ['url'];
   }
   else {
    $response ['error_msg'] = $function_response ['error_msg'];
   }
  }
  else {
   $response ['error_msg'] = _FUZE_PROF_HOST_ERROR;
  }

  return $response;
 }

 protected function _prof_internal_start() {
  $response = array('success' => false);
  $meeting_id = $_GET['meetingid'];
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   try {
    $meeting_item = $fuze_user->getMeeting($meeting_id);
    if ($meeting_item) {
     $args = array();
     $args ['fuze_email'] = $fuze_user->getFuzeEmail();
     $args ['fuze_password'] = $fuze_user->getPassword();
     $function_response = $meeting_item->launchMeeting($args);
     if ($function_response ['success']) {
      if (is_string($function_response ['url'])) {
       $response ['success'] = true;
       $response ['url'] = $function_response ['url'];
      }
      else {
       $response ['error_msg'] = _FUZE_PROF_LAUNCH_ERROR;
      }
     }
     elseif (isset($function_response ['url']) && !empty($function_response ['url'])) {
      $response ['url'] = $function_response ['url'];
     }
     else {
      $response ['error_msg'] = $function_response ['error_msg'];
     }
    }
    else {
     $response ['error_msg'] = _FUZE_PROF_LAUNCH_ERROR;
    }
   }
   catch (Exception $e) {
    $response ['error_msg'] = $e->getMessage();
   }
  }

  return $response;
 }

 protected function _prof_internal_launcher($smarty) {
  $template = false;
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   // Get the FUZE url to embed in launcher window
   $url = $_GET['url'];
   $smarty->assign('_FUZE_PROF_LAUNCHER_URL',$url);

   $template = $this->moduleBaseDir . 'views/smarty.professor.launcher.tpl';
  }

  return $template;
 }

 /**

	 * Shows all meetings that are to be held in a future time.

	 * 

	 * @param unknown_type $smarty

	 * @access protected

	 */
 protected function _prof_internal_all_meetings($smarty) {
  $template = false;
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   $template = $this->moduleBaseDir . 'views/smarty.professor.mod_fm_all_meetings.tpl';
  }
  return $template;
 }
 /**

	 * Retrieves a subset of the current user/lesson meetings, according to 

	 * certain given sorting criteria.

	 * 

	 * @access protected

	 */
 protected function _prof_fetch_meetings() {
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   $args = array();
   $args ['order'] = $_GET['order'];
   $args ['sort'] = $_GET['sort'];
   $args ['offset'] = $_GET['offset'];
   $args ['limit'] = $_GET['limit'];
   $args ['other'] = $_GET['other'];
   $tmp_meeting_array = $fuze_user->getFutureMeetingsSubset($args);
   $meeting_array = array();
   if (count($tmp_meeting_array)) {
    foreach ($tmp_meeting_array AS $meeting_id) {
     try {
      $meeting = new FUZE_Meeting($meeting_id);
     }
     catch (Exception $e) { /* DO NOTHING */ }
     if ($meeting) {
      $meeting_array [$meeting_id] = array();
      $meeting_array [$meeting_id]['subject'] = $meeting->getSubject();
      $meeting_array [$meeting_id]['lesson_name'] = $meeting->getLessonName();
      if ($meeting->isHappeningNow()) {
       $meeting_array [$meeting_id]['starttime'] = _FUZE_TIME_CPANEL_NOW;
       $meeting_array [$meeting_id]['link'] = _FUZE_PROF_CPANEL_GO_TO_MEETING;
       $meeting_array [$meeting_id]['meetingid'] = $meeting->getId();
      }
      else {
       $meeting_array [$meeting_id]['starttime'] = $meeting->getStartTimeTranslated($this->_current_user_timezone);
       $meeting_array [$meeting_id]['link'] = 0;
      }
     }
    }
   }
   $smarty = $this->getSmartyVar();
   $smarty->assign('T_TABLE_SIZE',$fuze_user->getFutureMeetingsCount());
   $dataSource = $meeting_array;
   $tableName = $_GET['ajax'];
   if (isset($_GET['ajax']) && $_GET['ajax'] == $tableName) {
    if (!$alreadySorted) {
     list($tableSize, $dataSource) = filterSortPage($dataSource);
     $smarty -> assign("T_TABLE_SIZE", $tableSize);
    }
    if (!empty($dataSource)) {
     $smarty -> assign("T_DATA_SOURCE", $dataSource);
    }
    $smarty -> assign("T_SORTED_TABLE", $tableName);
   }
  }
  return $this->moduleBaseDir . 'views/smarty.professor.mod_fm_all_meetings.tpl';
 }
 /**

	 * Carries out the logic for canceling a scheduled meeting. No interaction 

	 * with the FUZE API is initiated by this operation, it has local effect 

	 * only.

	 * 

	 * @access protected

	 */
 protected function _prof_meeting_cancel() {
  $response = array('success' => false);
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   $meeting_id = $_GET ['meeting_id'];
   $meeting_item = false;
   try {
    $meeting_item = new FUZE_Meeting($meeting_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
   if ($meeting_item) {
    // Check that the meeting owner is the current user
    $user_id = $meeting_item->getUserId();
    if ($user_id == $fuze_user->getId()) {
     // Check that the meeting is not happening right this minute
     if ($meeting_item->isHappeningNow()) {
      $response ['error_msg'] = _FUZE_PROF_ERROR_REMOVE_HAPPENING_NOW;
     }
     else {
      $function_response = $meeting_item->cancel();
      if ($function_response ['success']) {
       $response ['success'] = true;
      }
      else {
       $response ['error_msg'] = $function_response ['error_msg'];
      }
     }
    }
    else {
     $response ['error_msg'] = _FUZE_PROF_ERROR_REMOVE_AUTHORISATION;
    }
   }
   else {
    $response ['error_msg'] = _FUZE_PROF_VIEW_ALL_REMOVE_FAILURE;
   }
  }
  else {
   $response ['error_msg'] = _FUZE_PROF_VIEW_ALL_REMOVE_FAILURE;
  }
  return $response;
 }
 /**

	 * Prepares a meeting to be edited.

	 * 

	 * @param unknown_type $smarty The smarty instance where we are to pass 

	 * all variables useful for rendering this page.

	 * 

	 * @return String The template to be rendered by smarty.

	 * 

	 * @access protected

	 */
 protected function _prof_meeting_edit_prep($smarty) {
  $template = false;
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   $meeting_id = $_GET['meeting_id'];
   $smarty->assign("_FUZE_PROF_MEETING_EDIT_MEETING", false);
   if ($meeting_id) {
    $meeting_item = false;
    try {
     $meeting_item = new FUZE_Meeting($meeting_id);
    }
    catch (Exception $e) {
     $smarty->assign("_FUZE_PROF_MEETING_EDIT_ERROR", _FUZE_PROF_MEETING_EDIT_ERROR);
    }
   }
   if ($meeting_item) {
    if ($meeting_item->getUserId() == $fuze_user->getID()) {
     if (!$meeting_item->isHappeningNow()) {
      $data_array = array();
      $data_array ['meeting_id'] = $meeting_item->getId();
      $data_array ['attendees'] = array();
      $tmp_attendees = $meeting_item->getAttendeesAll();
      if (count($tmp_attendees) && is_array($tmp_attendees)) {
       foreach ($tmp_attendees AS $attendee_id => $attendee) {
        try {
         $attendee = new FUZE_Meeting_Attendee($attendee_id);
         $data_array ['attendees'][$attendee->getSysId()] = $attendee;
        }
        catch (Exception $e) { echo $e->getMessage();/* DO NOTHING */ }
       }
      }
      $data_array ['subject'] = $meeting_item->getSubject();
      $data_array ['lesson_id'] = $meeting_item->getLessonId();
      $data_array ['meeting_date'] = $meeting_item->getStartTimeTranslated($this->_current_user_timezone, 'Y-m-d');
      $data_array ['meeting_time'] = $meeting_item->getStartTimeTranslated($this->_current_user_timezone, 'H:i');
      // Setting the session lesson so we can retrieve the 
      // right subset of students when on prep page, regardless
      // of what lesson is active currently.
      $_SESSION['_mod_fm_schedule_lesson_id'] = $data_array ['lesson_id'];
      $smarty->assign("_FUZE_PROF_MEETING_EDIT_MEETING", $data_array);
     }
     else {
      $smarty->assign("_FUZE_PROF_MEETING_EDIT_ERROR", _FUZE_PROF_MEETING_EDIT_ERROR);
     }
    }
    else {
     $smarty->assign("_FUZE_PROF_MEETING_EDIT_ERROR", _FUZE_PROF_MEETING_EDIT_ERROR);
    }
   }
   // Na vro ta stoixeia olon ton mathimaton ta opoia didaskei autos o 
   // kathigitis giati ta xreiazome sto select gia to scheduling.
   $all_lessons = $fuze_user->getLessons();
   $smarty->assign('_FUZE_PROF_SCHEDULE_LESSON_LIST', $all_lessons);
   $template = $this->moduleBaseDir . 'views/smarty.professor.mod_fm_edit.tpl';
  }
  return $template;
 }
 /**

	 * Carries out the logc for editing a scheduled meeting.

	 * 

	 * @access protected

	 */
 protected function _prof_meeting_edit() {
  $response = array('success' => false);
  $args = array();
  $students = $_GET['students'];
  $students = explode(',',$students);
  $args ['starttime'] = FUZE_Tools::get_UTC_timestamp($this->_current_user_timezone, $_GET['starttime']);
  $args ['subject'] = $_GET['subject'];
  $args ['lesson_id'] = $_GET['lesson_id'];
  $args ['students'] = $students;
  $args ['send_invites'] = $_GET['send_invites'];
  $args ['add_events'] = $_GET['add_events'];
  $meeting_id = $_GET['meeting_id'];
  // Instantiating the FUZE account user here.
  if ($this->_current_user_id) {
   try {
    $fuze_user = $this->_f_user_manager->getUserBySysId($this->_current_user_id);
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  if ($fuze_user && !$fuze_user->isSuspended()) {
   $meeting_item = false;
   try {
    $meeting_item = new FUZE_Meeting($meeting_id);
   }
   catch (Exception $e) {
    $response ['error_msg'] = _FUZE_PROF_MEETING_EDIT_ERROR;
   }
   if ($meeting_item) {
    if ($meeting_item->getUserId() == $fuze_user->getID()) {
     if (!$meeting_item->isHappeningNow()) {
      // Getting old subject and starttime
      $args ['old_subject'] = $meeting_item->getSubject();
      $args ['old_starttime'] = $meeting_item->getStartTime();
      $args ['fuze_meeting_id'] = $meeting_item->getFuzeMeetingId();
      $args ['local_meeting_id'] = $meeting_item->getId();
      // Getting old attendees
      $args ['remaining_students'] = array();
      $args ['removed_students'] = array();
      $args ['new_students'] = array();
      $old_students = array();
      $tmp_old_students = array_keys($meeting_item->getAttendeesAll());
      if (count($tmp_old_students) && is_array($tmp_old_students)) {
       foreach ($tmp_old_students AS $attendee_id) {
        try {
         $attendee = new FUZE_Meeting_Attendee($attendee_id);
         $old_students[] = $attendee->getSysId();
        }
        catch (Exception $e) { echo $e->getMessage();/* DO NOTHING */ }
       }
      }
      foreach ($args ['students'] AS $student_id) {
       if (in_array($student_id, $old_students)) {
        $args ['remaining_students'][] = $student_id;
       }
       else {
        $args ['new_students'][] = $student_id;
       }
      }
      // Then we get the students that are to be removed
      foreach ($old_students AS $student_id) {
       if (!in_array($student_id, $args['students'])) {
        $args ['removed_students'][] = $student_id;
       }
      }
      $function_response = $fuze_user->editMeeting($args);
      if ($function_response ['success']) {
       $response ['success'] = true;
      }
      else {
       $response ['error_msg'] = $function_response ['error_msg'];
      }
     }
     else {
      $response ['error_msg'] = _FUZE_PROF_MEETING_EDIT_ERROR;
     }
    }
    else {
     $response ['error_msg'] = _FUZE_PROF_MEETING_EDIT_ERROR;
    }
   }
   else {
    $response ['error_msg'] = _FUZE_PROF_MEETING_EDIT_ERROR;
   }
  }
  else {
   $response ['error_msg'] = _FUZE_PROF_MEETING_EDIT_ERROR;
  }
  return $response;
 }
 /**

	 * Covers the case when some professor clicks on the center link icon and

	 * navigates to the default internal page which is the one showing all 

	 * meetings for the current lesson.

	 * 

	 * @access protected

	 */
 protected function _prof_internal_default($smarty, $fuze_user) {
  /* NOTHING HERE. THIS IS IN EFFECT CARRID OUT BY 'all_meetings' ACTION */
 }
 ///////////////////////////////////////////////////////////////////////////
 // STUDENT PROCEDURES BELOW
 ///////////////////////////////////////////////////////////////////////////
 protected function _student_default_cpanel($smarty) {
  // Instantiating the next 5 meetings for this user and lesson
  $tmp_meetings = $this->_current_student_five_next_meetings;
  $this->_current_student_five_next_meetings = array();
  foreach ($tmp_meetings AS $meeting_id => $meeting) {
   if (!($meeting instanceof FUZE_Meeting)) {
    // The meeting needs to be instantiated here
    try {
     $meeting_item = new FUZE_Meeting($meeting_id);
     $this->_current_student_five_next_meetings [$meeting_id] = $meeting_item;
    }
    catch (Exception $e) { /* DO NOTHING */ }
   }
  }
  $time_description = false;
  $latest_meetings = array();
  if (count($this->_current_student_five_next_meetings)) {
   foreach ($this->_current_student_five_next_meetings AS $meeting_id => $meeting) {
    $latest_meetings [$meeting_id] = array();
    $latest_meetings [$meeting_id]['subject'] = $meeting->getSubject();
    if ($meeting->isHappeningNow()) {
     $latest_meetings [$meeting_id]['starttime'] = _FUZE_TIME_CPANEL_NOW;
     $latest_meetings [$meeting_id]['link'] = _FUZE_STUDENT_CPANEL_GO_TO_MEETING;
     $attendee_email = urlencode($this->_current_user_email);
     $latest_meetings [$meeting_id]['url'] = $meeting->getAttendUrl() . "?email={$attendee_email}&name={$this->_current_user_fullname}";
    }
    else {
     $latest_meetings [$meeting_id]['link'] = 0;
     $latest_meetings [$meeting_id]['starttime'] = $meeting->getStartTimeTranslated($this->_current_user_timezone);
    }
    if (!$time_description) {
     $time_parts = FUZE_Tools::get_rough_time_description($meeting->getStartTime());
     $time_description = $this->_get_rough_time_description($time_parts);
    }
   }
  }
  // In case no meetings were found
  else {
   $time_description = _FUZE_PROF_MEETING_AMOUNT_MEETING_NONE;
  }
  $smarty->assign('_FUZE_STUDENT_CPANEL_MEETINGS', $latest_meetings);
  $smarty->assign('_FUZE_STUDENT_CPANEL_SCHEDULE_TIME_DESC', $time_description);
  $smarty->assign("MOD_FM_MODULES_BASEURL", G_MODULESURL);
  $smarty->assign("MOD_FM_MODULES_BASEPATH", G_MODULESPATH);
  return $this->moduleBaseDir . 'views/smarty.student.mod_fm_cpanel.tpl';
 }
 ///////////////////////////////////////////////////////////////////////////
 // ASSIST PROCEDURES BELOW
 ///////////////////////////////////////////////////////////////////////////
 private function _getActiveUserCount() {
  $fuze_accounts = 0;
  $res = eF_getTableData("`_mod_fm_user`", "COUNT(*) AS `amount`", "`suspended` != 1");
  if ($res && is_array($res) && count($res)) {
   $fuze_accounts = $res [0]['amount'];
  }
  return $fuze_accounts;
 }
 /**

	 * Eliminates magic quotes for the systems where magic quotes are active.

	 * @param unknown_type $data

	 * @return Ambigous <string, unknown>

	 */
 private function _eliminateMagicQuotes($data) {
  $tmp_data = $data;
  if (get_magic_quotes_gpc()) {
   if (is_array($data)) {
    foreach ($data AS $key => $value) {
     $tmp_data [stripslashes($key)] = stripslashes($value);
    }
   }
   else {
    $tmp_data = stripslashes($data);
   }
  }
  return $tmp_data;
 }
 /**

	 * Determines the current role of the current user, depending on 

	 * the course/lesson that is currently selected.

	 * 

	 * @return string The role of the current user.

	 * @access private

	 */
 private function _getCurrentUserRole() {
  $role = 'student';
  if ($this->_current_user->getType() == 'professor' || $this->_current_user->getType() == 'student') {
   if (in_array($this->_current_user_login, $this->_current_lesson_professors)) {
    $role = 'professor';
   }
   elseif (in_array($this->_current_user_login, $this->_current_lesson_students)) {
    $role = 'student';
   }
  }
  elseif ($this->_current_user->getType() == 'administrator') {
   $role = 'administrator';
  }
  return $role;
 }
 /**

	 * The FUZE account instance should be treated as a singleton, we pass 

	 * just the one instance around the entire app.

	 * 

	 * @return FUZE_Account The FUZE_Account instance that is used for this 

	 * installation of eFront.

	 * 

	 * @access private

	 */
 private function _getFUZEAccountInstance() {
  if (!$this->_f_account) {
   $this->_f_account = new FUZE_Account();
  }
  return $this->_f_account;
 }
 private function _get_rough_time_description ($time_parts) {
  if ($time_parts['now']) {
   return _FUZE_TIME_NEXT_MEETING . ' ' . _FUZE_TIME_IN_IS . ' ' . _FUZE_TIME_NOW;
  }
  if ($time_parts['future'] && !$time_parts['year'] && !$time_parts['month'] && !$time_parts['week'] && !$time_parts['day'] && !$time_parts['hour'] && $time_parts['minute'] < 3) {
   return _FUZE_TIME_NEXT_MEETING . ' ' . _FUZE_TIME_IN_IS . ' ' . _FUZE_TIME_NOW;
  }
  if (!$time_parts['future'] && (!$time_parts['year'] && !$time_parts['month'] && !$time_parts['week'] && !$time_parts['day'] && !$time_parts['hour'] && ($time_parts['minute'] < 10))) {
   return _FUZE_TIME_NEXT_MEETING . ' ' . _FUZE_TIME_IN_IS . ' ' . _FUZE_TIME_NOW;
  }
  $time_description = '';
  if ($time_parts['future']) {
   $time_description = _FUZE_TIME_NEXT_MEETING . ' ' . _FUZE_TIME_IN_IS . ' ';
  }
  if ($time_parts['year']) {
   $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_IN_OVER . ' ' . $time_parts['year'] . ' ' . ($time_parts['year']>1 ? _FUZE_TIME_YEARS : _FUZE_TIME_YEAR);
  }
  else {
   if ($time_parts['month']) {
    $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_IN_OVER . ' ' . $time_parts['month'] . ' ' . ($time_parts['month']>1 ? _FUZE_TIME_MONTHS : _FUZE_TIME_MONTH);
   }
   else {
    if ($time_parts['week']) {
     if ($time_parts['week'] > 1) {
      $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_IN_OVER . ' ' . $time_parts['week'] . ' ' . _FUZE_TIME_WEEKS;
     }
     elseif ($time_parts['week'] == 1) {
      if ($time_parts['day']) {
       $time_description .= _FUZE_TIME_IN_FUTURE . ' 1 ' . _FUZE_TIME_WEEK . ' ' . _FUZE_TIME_AND . ' ' . $time_parts['day'] . ' ' . ($time_parts['day'] > 1 ? _FUZE_TIME_DAYS : _FUZE_TIME_DAY);
      }
      else {
       $time_description .= _FUZE_TIME_IN_FUTURE . ' 1 ' . _FUZE_TIME_WEEK;
      }
     }
    }
    else {
     if ($time_parts['day'] > 1) {
      $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . $time_parts['day'] . ' ' . _FUZE_TIME_DAYS;
     }
     elseif ($time_parts['day'] == 1) {
      if ($time_parts['hour'] > 8) {
       $tie_description .= _FUZE_TIME_IN_FUTURE . ' 1 ' . _FUZE_TIME_DAY . ' ' . _FUZE_TIME_AND . ' ' . $time_parts['hour'] . ' ' . _FUZE_TIME_HOURS;
      }
      else {
       $time_description .= _FUZE_TIME_TOMORROW;
      }
     }
     else {
      if ($time_parts['hour'] > 4) {
       $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_ABOUT . ' ' . $time_parts['hour'] . ' ' . _FUZE_TIME_HOURS;
      }
      elseif ($time_parts['hour'] && $time_parts['hour'] <= 4) {
       $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_ABOUT . ' ' . $time_parts['hour'] . ' ' . ($time_parts['hour'] > 1 ? _FUZE_TIME_HOURS : _FUZE_TIME_HOUR) . ' ';
       $time_description .= _FUZE_TIME_AND . ' ';
       if ($time_parts['minute'] && $time_parts['minute'] <= 10) {
        $time_description .= _FUZE_TIME_A_FEW . ' ' . _FUZE_TIME_MINUTES;
       }
       elseif ($time_parts['minute'] > 10 && $time_parts['minute'] <= 25) {
        $time_description .= '15 ' . _FUZE_TIME_MINUTES;
       }
       elseif ($time_parts['minute'] > 25 && $time_parts['minute'] <= 40) {
        $time_description .= '30 ' . _FUZE_TIME_MINUTES;
       }
       elseif ($time_parts['minute'] > 40 && $time_parts['minute'] <= 59) {
        $time_description .= '45 ' . _FUZE_TIME_MINUTES;
       }
      }
      else {
       // A matter of minutes
       if ($time_parts['minute'] && $time_parts['minute'] <= 8) {
        $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_A_FEW . ' ' . _FUZE_TIME_MINUTES;
       }
       elseif ($time_parts['minute'] > 8 && $time_parts['minute'] <= 12) {
        $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_ABOUT . ' 10 ' . _FUZE_TIME_MINUTES;
       }
       elseif ($time_parts['minute'] > 13 && $time_parts['minute'] <= 18) {
        $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_ABOUT . ' 15 ' . _FUZE_TIME_MINUTES;
       }
       elseif ($time_parts['minute'] > 18 && $time_parts['minute'] <= 25) {
        $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_ABOUT . ' 20 ' . _FUZE_TIME_MINUTES;
       }
       elseif ($time_parts['minute'] > 25 && $time_parts['minute'] <= 35) {
        $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_ABOUT . ' 30 ' . _FUZE_TIME_MINUTES;
       }
       elseif ($time_parts['minute'] > 35 && $time_parts['minute'] <= 59) {
        $time_description .= _FUZE_TIME_IN_FUTURE . ' ' . _FUZE_TIME_ABOUT . ' 45 ' . _FUZE_TIME_MINUTES;
       }
      }
     }
    }
   }
  }
  return $time_description;
 }
 ///////////////////////////////////////////////////////////////////////////
 // INSTALL & UNINSTALL PROCEDURES BELOW
 ///////////////////////////////////////////////////////////////////////////
 /**

	 * Build the database and initialise default values during installation.

	 * 

	 * @access public

	 */
 public function onInstall() {
  $sql = 'SET FOREIGN_KEY_CHECKS=0;';
  eF_executeNew($sql);
  eF_executeNew('DROP TABLE IF EXISTS `_mod_fm_account`');
  eF_executeNew('DROP TABLE IF EXISTS `_mod_fm_user`');
  eF_executeNew('DROP TABLE IF EXISTS `_mod_fm_meeting_attendee`');
  eF_executeNew('DROP TABLE IF EXISTS `_mod_fm_meeting`');
  $sql = 'CREATE TABLE `_mod_fm_account` (';
  $sql .= '`consumer_key` CHAR(88),';
  $sql .= '`consumer_secret` CHAR(40)';
  $sql .= ')Engine=InnoDB charset=utf8;';
  eF_executeNew($sql);
  $sql = 'INSERT INTO `_mod_fm_account` (`consumer_key`,`consumer_secret`) VALUES ("","")';
  eF_executeNew($sql);
  $sql = 'CREATE TABLE `_mod_fm_user` (';
  $sql .= '`id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,';
  $sql .= '`sys_id` MEDIUMINT(8) UNSIGNED NOT NULL,';
  $sql .= '`password` CHAR(32) NOT NULL,';
  $sql .= '`login_url` VARCHAR(255) NOT NULL,';
  $sql .= '`fuze_email` VARCHAR(255) NOT NULL,';
  $sql .= '`date_added` INT(11) UNSIGNED NOT NULL,';
  $sql .= '`creator` ENUM (\'administrator\',\'professor\') NOT NULL,';
  $sql .= '`suspended` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,';
  $sql .= 'PRIMARY KEY (`id`)';
  $sql .= ')Engine=InnoDB charset=utf8;';
  eF_executeNew($sql);
  $sql = 'CREATE TABLE `_mod_fm_meeting` (';
  $sql .= '`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,';
  $sql .= '`user_id` MEDIUMINT(8) UNSIGNED NOT NULL,';
  $sql .= '`subject` VARCHAR(255) NOT NULL,';
  $sql .= '`starttime` INT(11) NOT NULL,';
  $sql .= '`launch_url` VARCHAR(255) NOT NULL,';
  $sql .= '`attend_url` VARCHAR(255) NOT NULL,';
  $sql .= '`lesson_id` MEDIUMINT(8) UNSIGNED NOT NULL,';
  $sql .= '`calendar_id` MEDIUMINT(8) UNSIGNED,';
  $sql .= '`fuze_meeting_id` VARCHAR(255) NOT NULL,';
  $sql .= '`launch_now_url` VARCHAR(255),';
  $sql .= 'PRIMARY KEY (`id`),';
  $sql .= 'FOREIGN KEY (`user_id`) REFERENCES `_mod_fm_user`(`id`)';
  $sql .= ')Engine=InnoDB charset=utf8;';
  eF_executeNew($sql);
  $sql = 'CREATE TABLE `_mod_fm_meeting_attendee` (';
  $sql .= '`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,';
  $sql .= '`sys_id` MEDIUMINT(8) UNSIGNED NOT NULL,';
  $sql .= '`meeting_id` INT(11) UNSIGNED NOT NULL,';
  $sql .= 'PRIMARY KEY (`id`),';
  $sql .= 'UNIQUE(`sys_id`,`meeting_id`),';
  $sql .= 'FOREIGN KEY (`meeting_id`) REFERENCES `_mod_fm_meeting`(`id`) ON DELETE CASCADE ON UPDATE CASCADE';
  $sql .= ')Engine=InnoDB charset=utf8;';
  eF_executeNew($sql);
  $sql = 'SET FOREIGN_KEY_CHECKS=1;';
  eF_executeNew($sql);
  return true;
 }
 /**

	 * Drop DB tables related to this modle during uninstall.

	 */
 public function onUninstall() {
  eF_executeNew('DROP TABLE IF EXISTS `_mod_fm_account`');
  eF_executeNew('DROP TABLE IF EXISTS `_mod_fm_user`');
  eF_executeNew('DROP TABLE IF EXISTS `_mod_fm_meeting_attendee`');
  eF_executeNew('DROP TABLE IF EXISTS `_mod_fm_meeting`');
  return true;
 }
}
?>
