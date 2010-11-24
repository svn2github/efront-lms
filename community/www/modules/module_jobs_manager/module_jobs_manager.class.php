<?php

//error_reporting(E_ALL);
require_once 'class_includer.php';

class module_jobs_manager extends EfrontModule {

 private $_message;
 private $_form_error;
 private $_settings;

 public function getName() {
  return _JOBS_MANAGER; // 'The Jobs Manager';
 }

 public function getPermittedRoles() {
  return array('administrator');
 }

 public function isLessonModule() {
        return false;
    }

 public function getControlPanelModule() {
  $html = false;
  return $html;
 }

 public function getSidebarLinkInfo() {
  $current_user = $this->getCurrentUser();
  $links = array();
  if ($current_user->getType() == 'administrator') {
   $links['id'] = 'jobs_manager_link_id1';
   $links['title'] = _JOBS_MANAGER; //'The Jobs Manager';
   $links['image'] = $this -> moduleBaseDir . 'images/logo16'; //. 'images/16x16/logo16';
   $links['eFrontExtensions'] = '1';
   $links['link'] = $this->moduleBaseUrl;
   $links = array('other' => array('menuTitle' => "Modules", 'links' => array($links)));
  }

  return $links;
 }

 public function getLinkToHighlight() {
  return 'jobs_manager_link_id1';
 }

 public function getNavigationLinks() {
  $navigation_links = array();
  $navigation_links [] = array('title' => 'Home', 'link' => "administrator.php?ctg=control_panel");
  $navigation_links [] = array('title' => 'Jobs manager', 'link' => $this->moduleBaseUrl);

  $action = (isset($_GET['action']) ? strtolower($_GET['action']) : false);
  if ($action == 'show_app') {
   $app_id = (isset($_GET['app_id']) ? $_GET['app_id'] : false);
   $job_code = (isset($_GET['job_code']) ? $_GET['job_code'] : false);
   $back_link = (isset($_GET['back']) ? $_GET['back'] : false);
   $link = $this->moduleBaseUrl . '&action=show_app' . ($app_id ? '&app_id='.$app_id : '') . ($job_code ? '&job_code='.$job_code : '') . ($back_link ? '&back='.$back_link : '');
         $navigation_links [] = array ('title' => _MOD_JAM_TOP_NAV_SHOW_APP, 'link' => $link);
        }
        else if ($action == 'show_job') {
         $job_id = (isset($_GET['job_id']) ? $_GET['job_id'] : false);
         $back_link = (isset($_GET['back']) ? $_GET['back'] : false);
         $link = $this->moduleBaseUrl . '&action=show_job' . ($job_id ? '&job_id='.$job_id : '') . ($back_link ? '&back='.$back_link : '');
   $navigation_links [] = array ('title' => _MOD_JAM_TOP_NAV_SHOW_JOB, 'link' => $link);
  }
  else if ($action == 'edit_job') {
         $job_id = (isset($_GET['job_id']) ? $_GET['job_id'] : false);
         $back_link = (isset($_GET['back']) ? $_GET['back'] : false);
         $link = $this->moduleBaseUrl . '&action=edit_job' . ($job_id ? '&job_id='.$job_id : '') . ($back_link ? '&back='.$back_link : '');
   $navigation_links [] = array ('title' => _MOD_JAM_TOP_NAV_EDIT_JOB, 'link' => $link);
  }
  else if ($action == 'new_job') {
         $link = $this->moduleBaseUrl . '&action=new_job';
   $navigation_links [] = array ('title' => _MOD_JAM_TOP_NAV_NEW_JOB, 'link' => $link);
  }

  return $navigation_links;
 }

 public function getCenterLinkInfo(){
  return array(
   'title' => _JOBS_MANAGER,
   'image' => $this->moduleBaseDir.'images/logo_32.png',
   'link' => $this->moduleBaseUrl
  );
 }


 public function getModule() {
  return true;
 }

 public function getSmartyTpl() {
  //error_reporting(E_ALL);

  global $load_editor;
  $load_editor = true;

  $template = false;
  $smarty = $this->getSmartyVar();
  $smarty->assign("MOD_JOBS_MANAGER_BASEURL", $this->moduleBaseUrl);
  $smarty->assign("MOD_JOBS_MANAGER_BASELINK", $this->moduleBaseLink);
  $smarty->assign("MOD_JOBS_MANAGER_BASEDIR", $this->moduleBaseDir);
  if (isset($_GET['action'])) {
   $action = strtolower($_GET['action']);
   if ($action == 'new_job') {
    $template = $this->_newJob($smarty);
   }
   elseif ($action == 'remove_job') {
    $template = $this->_removeJob($smarty);
   }
   elseif ($action == 'save_job') {
    $template = $this->_saveJob($smarty);
   }
   elseif ($action == 'edit_job') {
    $template = $this->_editJob($smarty);
   }
   elseif ($action == 'show_job') {
    $template = $this->_showJob($smarty);
   }
   elseif ($action == 'toggle_job') {
    $this->_toggleJob($smarty);
   }
   elseif ($action == 'remove_app') {
    $template = $this->_removeApp($smarty);
   }
   elseif ($action == 'show_app') {
    $template = $this->_showApp($smarty);
   }
   elseif ($action == 'save_settings_emails') {
    $template = $this->_saveSettingsEmails($smarty);
   }
   elseif ($action == 'save_settings_page') {
    $template = $this->_saveSettingsPage($smarty);
   }
   elseif ($action == 'save_settings_logo') {
    $template = $this->_saveSettingsLogo($smarty);
   }
  }

  if (!$template) {
   // None of the above conditions were met
   $template = $this->_defaultPage($smarty);
  }

  return $template;
 }

 private function _getJobFunctions($indices = false) {
  $functions = array('Accounting/Auditing','Administrative','Advertising','Analyst','Art/Creative','Business Development','Consulting','Customer Service','Design','Distribution','Education','Engineering','Finance','General Business','Health Care Provider','Human Resources','Information Technology','Legal','Management','Manufacturing','Marketing','Other','Production','Product Management','Project Management','Public Relations','Purchasing','Quality Assurance','Research','Sales','Science','Strategy/Planning','Supply Chain','Training','Writing/Editing');
  if (is_array($indices) && count($indices)) {
   $tmp_array = $functions;
   $functions = array();
   foreach ($indices AS $key => $val) {
    if (isset($tmp_array[$val])) {
     $functions [] = $tmp_array[$val];
    }
   }
  }

  return $functions;
 }

 private function _getJobType($index = false) {
  $job_types = array(_JOBS_MANAGER_FORM_CHOOSE,'Full-time','Part-time','Contract','Temporary','Other');
  if ($index !== false) {
   if (isset($job_types[$index])) {
    $job_types = $job_types[$index];
   }
  }

  return $job_types;
 }

 private function _getExpLvl($index = false) {
  $experience_lvls = array(_JOBS_MANAGER_FORM_CHOOSE,'Executive','Director','Mid-senior level','Associate','Entry level','Internship','Non-applicable');
  if ($index !== false) {
   if (isset($experience_lvls[$index])) {
    $experience_lvls = $experience_lvls[$index];
   }
  }

  return $experience_lvls;
 }

 private function _appendMessage($data, $error = false) {
  $data = trim($data);
  if ($error) $this->_form_error = true;
  if (!$this->_message) $this->_message = '';
  $this->_message .= $data;
 }

 private function _validateCode($data) {
  $success = false;
  if (strlen(trim($data))) {
   $success = true;
  }
  else {
   $this->_appendMessage(_MOD_JAM_VALIDATION_ERROR_CODE.'<br/>', true);
  }

  return $success;
 }

 private function _validateTitle($data) {
  $success = false;
  if (strlen(trim($data))) {
   $success = true;
  }
  else {
   $this->_appendMessage(_MOD_JAM_VALIDATION_ERROR_TITLE.'<br/>', true);
  }

  return $success;
 }

 private function _validateType($data) {
  $success = false;
  $type = $this->_getJobType($data);
  if ($type && $data) {
   $success = true;
  }
  else {
   $this->_appendMessage(_MOD_JAM_VALIDATION_ERROR_TYPE.'<br/>', true);
  }

  return $success;
 }

 private function _validateExperience($data) {
  $ssuccess = false;
  $exp_lvl = $this->_getExpLvl($data);
  if ($exp_lvl && $data) {
   $success = true;
  }
  else {
   $this->_appendMessage(_MOD_JAM_VALIDATION_ERROR_EXPERIENCE.'<br/>', true);
  }

  return $success;
 }

 private function _validateFunctions($data) {
  $success = false;
  $functions = $this->_getJobFunctions($data);
  if (is_array($functions) && count($functions) && $data) {
   $success = true;
  }
  else {
   $this->_appendMessage(_MOD_JAM_VALIDATION_ERROR_FUNCTIONS.'<br/>', true);
  }

  return $success;
 }

 private function _validateDescription($data) {
  $success = false;
  if (strlen(trim($data))) {
   $success = true;
  }
  else {
   $this->_appendMessage(_MOD_JAM_VALIDATION_ERROR_DESCRIPTION.'<br/>', true);
  }

  return $success;
 }

 private function _validateCompanyDescription($data) {
  $success = false;
  if (strlen(trim($data))) {
   $success = true;
  }
  else {
   $this->_appendMessage(_MOD_JAM_VALIDATION_ERROR_COMPANY_DESC.'<br/>', true);
  }

  return $success;
 }

 private function _validateRemuneration($data) {
  $success = true;
  /*

		if (strlen(trim($data))) {

			if (is_numeric($data) && $data >= 0) {

				$success = true;

			}

			else {

				$this->_appendMessage(_MOD_JAM_VALIDATION_ERROR_RENUMERATION.'<br/>', true);

			}

		}

		else {

			// Remuneration is not a required field.

			$success = true;

		}

		*/
  return $success;
 }
 private function _validateForm() {
  $this->_validateCode($_POST['mod_jam_new_job_code']);
  $this->_validateTitle($_POST['mod_jam_new_job_title']);
  $this->_validateType($_POST['mod_jam_new_job_type']);
  $this->_validateExperience($_POST['mod_jam_new_job_exp']);
  $this->_validateFunctions($_POST['mod_jam_new_job_functions']);
  $this->_validateDescription($_POST['mod_jam_new_job_desc']);
  $this->_validateCompanyDescription($_POST['mod_jam_new_job_company_desc']);
  $this->_validateRemuneration($_POST['mod_jam_new_job_remuneration']);
  return (!$this->_form_error ? true : false);
 }
 private function _prepareFormOptions($array_1, $array_2) {
  $processed_array = array();
  if (is_array($array_2) && count($array_2)) {
   foreach ($array_1 AS $key => $val) {
    $data_array = array();
    $data_array['id'] = $val['id'];
    $data_array['name'] = $val['name'];
    $data_array['selected'] = 0;
    if (in_array( $val['name'] , $array_2 )) {
     $data_array['selected'] = 1;
    }
    $processed_array[$key] = $data_array;
   }
  }
  elseif (is_string($array_2)) {
   foreach ($array_1 AS $key => $val) {
    $data_array = array();
    $data_array['id'] = $val['id'];
    $data_array['name'] = $val['name'];
    $data_array['selected'] = 0;
    if ($val['name'] == $array_2 ) {
     $data_array['selected'] = 1;
    }
    $processed_array[$key] = $data_array;
   }
  }
  return $processed_array;
 }
 private function _defaultPage($smarty) {
  $smarty = $smarty;
  $template = $this->moduleBaseDir . 'smarty.tabbed_generic.tpl';
  $job_array = $this->_getJobTab($smarty);
  $this->_getAppTab($smarty, $job_array);
  $this->_getSettingsTab($smarty);
  return $template;
 }
 private function _getJobTab($smarty) {
  // Retrieving job list
  $job_manager = new JobManager();
  $job_count = $job_manager->getJobCount();
  $smarty->assign('_JOB_MANAGER_JOB_COUNT', $job_count);
  $tmp_array = $job_manager->getJobs();
  $job_array = array();
  foreach ($tmp_array AS $job_id => $job) {
   try {
    $job = new Job($job_id);
    $job_array [$job_id] = $job;
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  $job_data = array();
  if (count($job_array)) {
   foreach ($job_array AS $job_id => $job) {
    $job_data[$job_id] = array();
    $job_data[$job_id]['id'] = $job_id;
    $job_data[$job_id]['code'] = $job->getCode();
    $job_data[$job_id]['title'] = $job->getTitle();
    $job_data[$job_id]['date_added'] = $job->getDateAdded();
    $job_data[$job_id]['app_count'] = $job->getApplicationCount();
    if ($job->isActive()) {
     $job_data[$job_id]['active'] = true;
     $job_data[$job_id]['status'] = _JOBS_MANAGER_JOB_IS_ACTIVE;
    }
    else {
     $job_data[$job_id]['active'] = false;
     $job_data[$job_id]['status'] = _JOBS_MANAGER_JOB_IS_PAUSED;
    }
   }
  }
  $smarty->assign('_JOB_MANAGER_JOBS', $job_data);
  return $job_array;
 }
 private function _getAppTab($smarty, $job_array) {
  // Retrieving app list
  $app_data = array();
  $job_app_data = array();
  if (count($job_array)) {
   foreach ($job_array AS $job_id => $job) {
    if ($job) {
     $tmp_array = $job->getAllApplications();
     $app_array = array();
     foreach ($tmp_array AS $app_id => $app) {
      try {
       $app = new JobApplication($app_id);
       $app_array [$app_id] = $app;
      }
      catch (Exception $e) { /* DO NOTHING */ }
     }
     if (count($app_array)) {
      foreach ($app_array AS $app_id => $app) {
       $app_data[$app_id] = array();
       $app_data[$app_id]['id'] = $app_id;
       $app_data[$app_id]['name'] = $app->getName();
       $app_data[$app_id]['job_id'] = $job_id;
       $app_data[$app_id]['job_code'] = $job->getCode();
       $job_code = $app_data[$app_id]['job_code'];
       $app_data[$app_id]['email'] = $app->getEMail();
       $app_data[$app_id]['phone'] = $app->getPhone();
       $app_data[$app_id]['city'] = $app->getCity();
       $app_data[$app_id]['country'] = $app->getCountry();
       $app_data[$app_id]['cover'] = $app->getCover();
       $app_data[$app_id]['read'] = $app->isRead();
       $app_data[$app_id]['cv_filename'] = $app->getCvFilenameWeb();
       $app_data[$app_id]['date_added'] = $app->getDateAdded();
      }
     }
    }
   }
   $smarty->assign('_JOB_MANAGER_JOB_APPS', $app_data);
  }
 }
 private function _getSettingsTab($smarty) {
  // Preparing settings tab
  $settings = new Settings();
  //$all_emails = $settings->getUserEmails();
  $set_emails = $settings->getSetEmails();
  //$emails = array();
  /*

		foreach ($all_emails AS $key => $data) {

			$email = array();

			$email ['full_name'] = $data ['full_name'];

			$email ['email'] = $data ['email'];

			if (in_array($data['email'],$set_emails)) {

				$email ['selected'] = true;

			}

			else $email ['selected'] = false;

			$emails [] = $email;

		}

		*/
  $emails = '';
  foreach ($set_emails AS $email) {
   $emails .= $email . ';';
  }
  $emails = substr($emails,0,-1);
  $smarty->assign('_MOD_JAM_SETTINGS_EMAILS',$emails);
  $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_ALERT_CONTENT', $settings->getEmailContent());
  $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_REPLY_CONTENT', $settings->getConfirmationEmailContent());
  $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_SEND_EMAIL', $settings->getSendEmail());
  $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_SEND_NAME', $settings->getSendName());
  $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_SEND_CV_URL', $settings->getSendCvUrl());
  $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_SEND_PHONE', $settings->getSendPhone());
  // Company logo
  $smarty->assign('_MOD_JAM_SETTINGS_LOGO', $settings->getLogoFilenameWeb());
  // About content
  $smarty->assign('_MOD_JAM_SETTINGS_ABOUT', $settings->getAboutContent());
  // List location
  $smarty->assign('_MOD_JAM_SETTINGS_LIST_LOCATION', $settings->getListLocation());
  $smarty->assign('_MOD_JAM_SETTINGS_LIST_TYPE', $settings->getListType());
  $smarty->assign('_MOD_JAM_PUBLIC_URL', '<a href="'.$settings->getPublicPageUrl().'" target="_blank">'._MOD_JAM_SETTINGS_PAGE_URL.'</a>');
  $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_NAME",$settings->getSendName());
  $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_EMAIL",$settings->getSendEmail());
  $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_PHONE",$settings->getSendPhone());
  $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_CV_URL",$settings->getSendCvUrl());
 }
 ###########################################################################
 ## JOB RELATED METHODS
 ###########################################################################
 /**

	 * This is the method that carries the logic for the detailed presentation 

	 * of a job on the screen.

	 * 

	 * @param object $smarty

	 * 

	 * @return string The path to the smarty template for this page.

	 * 

	 * @access private

	 */
 private function _showJob($smarty) {
  $smarty = $smarty;
  $template = $this->moduleBaseDir . 'smarty.job_show.tpl';
  $back_link = 1;
  if (isset($_GET['job_id']) && is_numeric($_GET['job_id']) && !($_GET['job_id']%1) && $_GET['job_id'] > 0) {
   $job_id = $_GET['job_id'];
   $job = false;
   try {
    $job = new Job($job_id);
   }
   catch (Exception $e) {
    $job = false;
    $this->_appendMessage(_MOD_JAM_ERROR_NO_JOB.'<br/>', true);
    $message_type = 'failure';
    $this->setMessageVar($this->_message, $message_type);
   }
   if (isset($_GET['back']) && $_GET['back'] == 2) $back_link = 2;
   if ($back_link == 1) {
    // This is when user comes from job list
    $smarty->assign("MOD_JOBS_MANAGER_BACK",$this->moduleBaseUrl);
   }
   else {
    // This is when user comes from app list
    $smarty->assign("MOD_JOBS_MANAGER_BACK",$this->moduleBaseUrl.'&tab=apps');
   }
   if ($job) {
    // Prep work for the job state
    $job_data = array();
    $job_data['id'] = $job_id;
    $job_data['code'] = $job->getCode();
    $job_data['title'] = $job->getTitle();
    $job_data['description'] = $job->getDescription();
    $job_data['date_added'] = $job->getDateAdded();
    $job_data['app_count'] = $job->getApplicationCount();
    $job_data['company_desc'] = $job->getCompanyDesc();
    $job_data['remuneration'] = $job->getRemuneration();
    $job_data['type'] = $job->getType();
    $job_data['experience'] = $job->getExperience();
    $job_data['skills'] = $job->getSkills();
    $job_data['active'] = $job->isActive();
    if ($job->isActive()) {
     $job_data['status'] = _JOBS_MANAGER_JOB_IS_ACTIVE;
    }
    else {
     $job_data['status'] = _JOBS_MANAGER_JOB_IS_INACTIVE;
    }
    // Prep work on the applications for this job
    $tmp_array = $job->getAllApplications();
    $app_array = array();
    foreach ($tmp_array AS $app_id => $app) {
     try {
      $app = new JobApplication($app_id);
      $app_array [$app_id] = $app;
     }
     catch (Exception $e) { /* DO NOTHING */ }
    }
    $app_data = array();
    if (count($app_array)) {
     foreach ($app_array AS $app_id => $app) {
      $app_data [$app_id] = array();
      $app_data [$app_id]['id'] = $app_id;
      $app_data [$app_id]['job_id'] = $job_id;
      $app_data [$app_id]['job_code'] = $job->getCode();
      $job_code = $app_data[$app_id]['job_code'];
      $app_data [$app_id]['name'] = $app->getName();
      $app_data [$app_id]['email'] = $app->getEmail();
      $app_data [$app_id]['phone'] = $app->getPhone();
      $app_data [$app_id]['city'] = $app->getCity();
      $app_data [$app_id]['country'] = $app->getCountry();
      $app_data [$app_id]['cover'] = $app->getCover();
      $app_data [$app_id]['cv_filename'] = $app->getCvFilename();
      $app_data [$app_id]['date_added'] = $app->getDateAdded();
      $app_data [$app_id]['link'] = $this->moduleBaseUrl . "&app_id=$app_id&action=show_app&job_code=$job_code";
     }
    }
    $job_data['app_data'] = $app_data;
    $smarty->assign('_JOB_MANAGER_JOB', $job_data);
   }
  }
  return $template;
 }
 /**

	 * Toggles the status of a given Job instance.

	 * 

	 * @param unknown_type $smarty

	 * @return JSON The result of the activity

	 * 

	 * @access private

	 */
 private function _toggleJob($smarty) {
  $response = array();
  $response['success'] = false;
  if (isset($_GET['job_id']) && is_numeric($_GET['job_id']) && !($_GET['job_id']%1) && $_GET['job_id'] > 0) {
   $job_id = $_GET['job_id'];
   $job = false;
   try {
    $job = new Job($job_id);
    if ($job->isActive()) {
     $job->setActive(false);
     $response['success'] = true;
     $response['new_status'] = false;
    }
    else {
     $job->setActive(true);
     $response['success'] = true;
     $response['new_status'] = true;
    }
   }
   catch (Exception $e) { /* DO NOTHING */ }
  }
  die(json_encode($response));
 }
 private function _saveJob($smarty) {
  $smarty = $smarty;
  $template = $this->moduleBaseDir . 'smarty.job_new.tpl';
  if (isset($_GET['job_id'])) {
   $mode = 'edit';
  }
  else {
   $mode = 'new';
  }
  // Start of validating the form
  if ($this->_validateForm()) {
   $args = array();
   $args['code'] = trim($_POST['mod_jam_new_job_code']);
   $args['title'] = trim($_POST['mod_jam_new_job_title']);
   $args['type'] = $_POST['mod_jam_new_job_type'];
   $args['experience'] = $_POST['mod_jam_new_job_exp'];
   $args['remuneration'] = trim($_POST['mod_jam_new_job_remuneration']);
   $args['functions'] = $this->_getJobFunctions($_POST['mod_jam_new_job_functions']);
   $args['desc'] = trim($_POST['mod_jam_new_job_desc']);
   $args['skills'] = trim($_POST['mod_jam_new_job_skills']);
   $args['company_desc'] = trim($_POST['mod_jam_new_job_company_desc']);
   if (isset($_GET['job_id'])) {
    // That means we're saving an edited job
    try {
     $job = new Job($_GET['job_id']);
     $functions = '';
     foreach ($args['functions'] AS $key => $function) {
      $functions .= $function . '::';
     }
     $job->setFunctions(substr($functions, 0, -2));
     $job->setCode($args['code']);
     $job->setTitle($args['title']);
     $job->setType($args['type']);
     $job->setExperience($args['experience']);
     $job->setRemuneration($args['remuneration']);
     $job->setDescription($args['desc']);
     $job->setSkills($args['skills']);
     $job->setCompanyDesc($args['company_desc']);
     eF_redirect($this->moduleBaseUrl);
    }
    catch (Exception $e) {
     $this->_appendMessage('This is not a valid Job id!', true);
     $message_type = 'failure';
     $this->setMessageVar($this->_message, $message_type);
     $template = $this->_editJob($smarty);
    }
   }
   else {
    // That means we're saving a newly created job
    $job_manager = new JobManager();
    $job_manager->addJob($args);
   }
   $template = $this->_defaultPage($smarty);
  }
  else {
   $message_type = 'failure';
   $this->setMessageVar($this->_message, $message_type);
   if ($mode == 'edit') {
    $template = $this->_editJob($smarty);
   }
   else {
    $template = $this->_newJob($smarty);
   }
  }
  return $template;
 }
 private function _newJob($smarty) {
  $smarty = $smarty;
  $template = $this->moduleBaseDir . 'smarty.job_new.tpl';
  //This is when user comes from job list
  $smarty->assign("MOD_JOBS_MANAGER_BACK",$this->moduleBaseUrl);
  $post_target = '&action=save_job';
  $smarty->assign('MOD_JAM_FORM_POSTTARGET',$post_target);
  $functions = $this->_getJobFunctions();
  $tmp_functions = $functions;
  $functions = array();
  foreach ($tmp_functions AS $key => $value) {
   $functions [] = array('id'=>$key, 'name'=>$value, 'selected' => false);
  }
  if (isset($_POST['mod_jam_new_job_functions']) && is_array($_POST['mod_jam_new_job_functions']) && count($_POST['mod_jam_new_job_functions'])) {
   $selected_functions = $this->_getJobFunctions($_POST['mod_jam_new_job_functions']);
   $functions = $this->_prepareFormOptions($functions, $selected_functions);
  }
  $smarty->assign('_JOB_MANAGER_FORM_NEW_FUNCTION',$functions);
  $job_types = $this->_getJobType();
  $tmp_job_types = $job_types;
  $job_types = array();
  foreach ($tmp_job_types AS $key => $value) {
   $job_types [] = array('id'=>$key, 'name'=>$value, 'selected' => false);
  }
  if (isset($_POST['mod_jam_new_job_type']) && $_POST['mod_jam_new_job_type']) {
   $selected_job_types = $this->_getJobType($_POST['mod_jam_new_job_type']);
   $job_types = $this->_prepareFormOptions($job_types, $selected_job_types);
  }
  $smarty->assign('_JOB_MANAGER_FORM_NEW_TYPES',$job_types);
  $experience_lvls = $this->_getExpLvl();
  $tmp_job_xp_lvls = $experience_lvls;
  $experience_lvls = array();
  foreach ($tmp_job_xp_lvls AS $key => $value) {
   $experience_lvls [] = array('id'=>$key, 'name'=>$value, 'selected' => false);
  }
  if (isset($_POST['mod_jam_new_job_exp']) && $_POST['mod_jam_new_job_exp']) {
   $selected_xp_lvls = $this->_getExpLvl($_POST['mod_jam_new_job_exp']);
   $experience_lvls = $this->_prepareFormOptions($experience_lvls, $selected_xp_lvls);
  }
  $smarty->assign('_JOB_MANAGER_FORM_NEW_EXP',$experience_lvls);
  if (isset($_POST['mod_jam_new_job_code'])) {
   $smarty->assign('_MOD_JOBS_FORM_POPULATE_CODE', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_code'])));
  }
  if (isset($_POST['mod_jam_new_job_title'])) {
   $smarty->assign('_MOD_JOBS_FORM_POPULATE_TITLE', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_title'])));
  }
  if (isset($_POST['mod_jam_new_job_desc'])) {
   $smarty->assign('_MOD_JOBS_FORM_POPULATE_DESC', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_desc'])));
  }
  if (isset($_POST['mod_jam_new_job_remuneration'])) {
   $smarty->assign('_MOD_JOBS_FORM_POPULATE_REMUNERATION', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_remuneration'])));
  }
  if (isset($_POST['mod_jam_new_job_company_desc'])) {
   $smarty->assign('_MOD_JOBS_FORM_POPULATE_COMPANY_DESC', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_company_desc'])));
  }
  if (isset($_POST['mod_jam_new_job_skills'])) {
   $smarty->assign('_MOD_JOBS_FORM_POPULATE_SKILLS', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_skills'])));
  }
  return $template;
 }
 private function _editJob($smarty) {
  $smarty = $smarty;
  $template = $this->moduleBaseDir . 'smarty.job_new.tpl';
  $back_link = 1;
  if (isset($_GET['job_id']) && is_numeric($_GET['job_id']) && !($_GET['job_id']%1) && $_GET['job_id'] > 0) {
   $job_id = $_GET['job_id'];
   $job = false;
   try {
    $job = new Job($job_id);
   }
   catch (Exception $e) {
    $job = false;
   }
   if (isset($_GET['back']) && $_GET['back'] == 2) $back_link = 2;
   if ($back_link == 1) {
    // This is when user comes from job list
    $smarty->assign("MOD_JOBS_MANAGER_BACK",$this->moduleBaseUrl);
   }
   else {
    // This is when user comes from job show page
    $smarty->assign("MOD_JOBS_MANAGER_BACK",$this->moduleBaseUrl.'&action=show_job&job_id='.$job_id);
   }
   if ($job) {
    $post_target = '&action=save_job&job_id=' . $job_id;
    $smarty->assign('MOD_JAM_FORM_POSTTARGET',$post_target);
    $functions = $this->_getJobFunctions();
    $tmp_functions = $functions;
    $functions = array();
    foreach ($tmp_functions AS $key => $value) {
     $functions [] = array('id'=>$key, 'name'=>$value, 'selected' => false);
    }
    if (isset($_POST['mod_jam_new_job_functions']) && is_array($_POST['mod_jam_new_job_functions']) && count($_POST['mod_jam_new_job_functions'])) {
     $selected_functions = $this->_getJobFunctions($_POST['mod_jam_new_job_functions']);
     $functions = $this->_prepareFormOptions($functions, $selected_functions);
    }
    elseif ($selected_functions = $job->getFunctionsArray()) {
     // We prioritise the value found in the $_POST array, if any are found
     $functions = $this->_prepareFormOptions($functions, $selected_functions);
    }
    $smarty->assign('_JOB_MANAGER_FORM_NEW_FUNCTION',$functions);
    $job_types = $this->_getJobType();
    $tmp_job_types = $job_types;
    $job_types = array();
    foreach ($tmp_job_types AS $key => $value) {
     $job_types [] = array('id'=>$key, 'name'=>$value, 'selected' => false);
    }
    if (isset($_POST['mod_jam_new_job_type']) && $_POST['mod_jam_new_job_type']) {
     $selected_job_types = $this->_getJobType($_POST['mod_jam_new_job_type']);
     $job_types = $this->_prepareFormOptions($job_types, $selected_job_types);
    }
    elseif ($selected_job_types = $job->getType()) {
     // We prioritise the value found in the $_POST array, if any are found
     $job_types = $this->_prepareFormOptions($job_types, $selected_job_types);
    }
    $smarty->assign('_JOB_MANAGER_FORM_NEW_TYPES',$job_types);
    $experience_lvls = $this->_getExpLvl();
    $tmp_job_xp_lvls = $experience_lvls;
    $experience_lvls = array();
    foreach ($tmp_job_xp_lvls AS $key => $value) {
     $experience_lvls [] = array('id'=>$key, 'name'=>$value, 'selected' => false);
    }
    if (isset($_POST['mod_jam_new_job_exp']) && $_POST['mod_jam_new_job_exp']) {
     $selected_xp_lvls = $this->_getExpLvl($_POST['mod_jam_new_job_exp']);
     $experience_lvls = $this->_prepareFormOptions($experience_lvls, $selected_xp_lvls);
    }
    elseif ($selected_xp_lvls = $job->getExperience()) {
     // We prioritise the value found in the $_POST array, if any are found
     $experience_lvls = $this->_prepareFormOptions($experience_lvls, $selected_xp_lvls);
    }
    $smarty->assign('_JOB_MANAGER_FORM_NEW_EXP',$experience_lvls);
    $smarty->assign('_MOD_JOBS_FORM_POPULATE_CODE', $job->getCode());
    if (isset($_POST['mod_jam_new_job_code'])) {
     $smarty->assign('_MOD_JOBS_FORM_POPULATE_CODE', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_code'])));
    }
    $smarty->assign('_MOD_JOBS_FORM_POPULATE_TITLE', $job->getTitle());
    if (isset($_POST['mod_jam_new_job_title'])) {
     $smarty->assign('_MOD_JOBS_FORM_POPULATE_TITLE', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_title'])));
    }
    $smarty->assign('_MOD_JOBS_FORM_POPULATE_DESC', $job->getDescription());
    if (isset($_POST['mod_jam_new_job_desc'])) {
     $smarty->assign('_MOD_JOBS_FORM_POPULATE_DESC', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_desc'])));
    }
    $smarty->assign('_MOD_JOBS_FORM_POPULATE_REMUNERATION', $job->getRemuneration());
    if (isset($_POST['mod_jam_new_job_remuneration'])) {
     $smarty->assign('_MOD_JOBS_FORM_POPULATE_REMUNERATION', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_remuneration'])));
    }
    $smarty->assign('_MOD_JOBS_FORM_POPULATE_COMPANY_DESC', $job->getCompanyDesc());
    if (isset($_POST['mod_jam_new_job_company_desc'])) {
     $smarty->assign('_MOD_JOBS_FORM_POPULATE_COMPANY_DESC', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_company_desc'])));
    }
    $smarty->assign('_MOD_JOBS_FORM_POPULATE_SKILLS', $job->getSkills());
    if (isset($_POST['mod_jam_new_job_skills'])) {
     $smarty->assign('_MOD_JOBS_FORM_POPULATE_SKILLS', trim($this->_eliminateMagicQuotes($_POST['mod_jam_new_job_skills'])));
    }
    return $template;
   }
  }
  return $this->_defaultPage($smarty);
 }
 private function _removeJob($smarty) {
  $smarty = $smarty;
  if (isset($_GET['job_id']) && is_numeric($_GET['job_id']) && !($_GET['job_id']%1) && $_GET['job_id'] > 0) {
   $job_id = $_GET['job_id'];
   $job_manager = new JobManager();
   if ($job_manager->removeJob($job_id)) {
    unset($job_manager);
    eF_redirect($this->moduleBaseUrl);
   }
  }
  return $this->_defaultPage($smarty);
 }
 ###########################################################################
 ## APP RELATED METHODS
 ###########################################################################
 private function _showApp($smarty) {
  $smarty = $smarty;
  $template = $this->moduleBaseDir . 'smarty.app_show.tpl';
  $back_link = 0;
  if (isset($_GET['app_id']) && is_numeric($_GET['app_id']) && !($_GET['app_id']%1) && $_GET['app_id'] > 0) {
   $app_id = $_GET['app_id'];
   $app = false;
   try {
    $app = new JobApplication($app_id);
   }
   catch (Exception $e) {
    $app = false;
    $this->_appendMessage(_MOD_JAM_ERROR_NO_APP.'<br/>', true);
    $message_type = 'failure';
    $this->setMessageVar($this->_message, $message_type);
   }
   if (isset($_GET['back']) && $_GET['back']) $back_link = $_GET['back'];
   if (!$back_link) {
    // This is when user comes from app list
    $smarty->assign("MOD_JOBS_MANAGER_BACK",$this->moduleBaseUrl . '&tab=apps');
   }
   else {
    // This is when user comes from job show page
    $smarty->assign("MOD_JOBS_MANAGER_BACK",$this->moduleBaseUrl.'&action=show_job&job_id='.$back_link);
   }
   if ($app) {
    // We change the app's read value
    $app->setRead(true);
    // Prep work for the app state
    $app_data = array();
    $app_data ['id'] = $app_id;
    $app_data ['job_id'] = $app->getJobId();
    $app_data ['name'] = $app->getName();
    $app_data ['email'] = $app->getEmail();
    $app_data ['phone'] = $app->getPhone();
    $app_data ['city'] = $app->getCity();
    $app_data ['country'] = $app->getCountry();
    $app_data ['cover'] = $app->getCover();
    $app_data ['cv_filename'] = $app->getCvFilenameWeb();
    $app_data ['date_added'] = $app->getDateAdded();
    $smarty->assign('_JOB_MANAGER_APP', $app_data);
   }
  }
  return $template;
 }
 private function _removeApp($smarty) {
  $smarty = $smarty;
  if (isset($_GET['job_id']) && is_numeric($_GET['job_id']) && !($_GET['job_id']%1) && $_GET['job_id'] > 0 &&
   isset($_GET['app_id']) && is_numeric($_GET['app_id']) && !($_GET['app_id']%1) && $_GET['app_id'] > 0) {
   $job_id = $_GET['job_id'];
   $app_id = $_GET['app_id'];
   try {
    $job = new Job($job_id);
    if ($job->removeApplication($app_id)) {
     unset($job);
     eF_redirect($this->moduleBaseUrl . '&tab=apps');
    }
   }
   catch (Exception $e) {
    /* The job object was not found, we cannot proceed. */
   }
  }
  return $this->_defaultPage($smarty);
 }
 ###########################################################################
 ## SETTINGS RELATED METHODS
 ###########################################################################
 private function _saveSettingsEmails($smarty) {
  $smarty = $smarty;
  $template = $this->moduleBaseDir . 'smarty.tabbed_generic.tpl';
  // Start of validating the form
  $email_content = trim($this->_eliminateMagicQuotes($_POST['mod_jam_email_content_admin']));
  $confirmation_email_content = trim($this->_eliminateMagicQuotes($_POST['mod_jam_email_content_reply']));
  $emails = array();
  $validation_error = false;
  $settings = new Settings();
  if (isset($_POST['settings_emails']) && is_string($_POST['settings_emails']) && $_POST['settings_emails']) {
   $emails = $_POST['settings_emails'];
  }
  else {
   $validation_error = true;
   $this->_appendMessage(_MOD_JAM_SETTINGS_ERROR_NO_EMAILS . '<br/>', true);
   $message_type = 'failure';
   $this->setMessageVar($this->_message, $message_type);
  }
  // Making sure there's a value set for email contents
  if (!$email_content) {
   $validation_error = true;
   $this->_appendMessage(_MOD_JAM_SETTINGS_ERROR_NO_EMAIL_CONTENT . '<br/>', true);
   $message_type = 'failure';
   $this->setMessageVar($this->_message, $message_type);
  }
  if (!$confirmation_email_content) {
   $validation_error = true;
   $this->_appendMessage(_MOD_JAM_SETTINGS_ERROR_NO_EMAIL_REPLY_CONTENT . '<br/>', true);
   $message_type = 'failure';
   $this->setMessageVar($this->_message, $message_type);
  }
  if ($validation_error) {
   $set_emails = $settings->getSetEmails();
   $emails = '';
   foreach ($set_emails AS $email) {
    $emails .= $email . ';';
   }
   $emails = substr($emails,0,-1);
   $smarty->assign('_MOD_JAM_SETTINGS_EMAILS',$emails);
   if (isset($_POST['mod_jam_email_content_admin'])) {
    $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_ALERT_CONTENT', $email_content);
   }
   else {
    $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_ALERT_CONTENT', $settings->getEmailContent());
   }
   if (isset($_POST['mod_jam_email_content_reply'])) {
    $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_REPLY_CONTENT', $confirmation_email_content);
   }
   else {
    $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_REPLY_CONTENT', $settings->getConfirmationEmailContent());
   }
   if (in_array('send_name',$_POST['mod_jam_form_extras'])) {
    $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_NAME",1);
   }
   else {
    $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_NAME",0);
   }
   if (in_array('send_email',$_POST['mod_jam_form_extras'])) {
    $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_EMAIL",1);
   }
   else {
    $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_EMAIL",0);
   }
   if (in_array('send_phone',$_POST['mod_jam_form_extras'])) {
    $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_PHONE",1);
   }
   else {
    $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_PHONE",0);
   }
   if (in_array('send_cv_url',$_POST['mod_jam_form_extras'])) {
    $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_CV_URL",1);
   }
   else {
    $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_CV_URL",0);
   }
  }
  else {
   // All went well, we save changes.
   $send_name = (in_array('send_name',$_POST['mod_jam_form_extras']) ? 1 : 0);
   $send_email = (in_array('send_email',$_POST['mod_jam_form_extras']) ? 1 : 0);
   $send_phone = (in_array('send_phone',$_POST['mod_jam_form_extras']) ? 1 : 0);
   $send_cv_url = (in_array('send_cv_url',$_POST['mod_jam_form_extras']) ? 1 : 0);
   $settings->setSendCvUrl($send_cv_url);
   $settings->setSendEmail($send_email);
   $settings->setSendName($send_name);
   $settings->setSendPhone($send_phone);
   $settings->setEmailContent($email_content);
   $settings->setConfirmationEmailContent($confirmation_email_content);
   // We process the emails here
   $emails = str_ireplace(',',';',$emails);
   $tmp_emails = explode(';',$emails);
   $emails = array();
   if (count($tmp_emails)) {
    foreach ($tmp_emails AS $email) {
     if (trim($email)) {
      $emails [] = $email;
     }
    }
   }
   $settings->setSetEmails($emails);
   eF_redirect($this->moduleBaseUrl . '&tab=settings');
  }
  $job_array = $this->_getJobTab($smarty);
  $this->_getAppTab($smarty, $job_array);
  // Company logo
  $smarty->assign('_MOD_JAM_SETTINGS_LOGO', $settings->getLogoFilenameWeb());
  // About content
  $smarty->assign('_MOD_JAM_SETTINGS_ABOUT', $settings->getAboutContent());
  // List location
  $smarty->assign('_MOD_JAM_SETTINGS_LIST_LOCATION', $settings->getListLocation());
  // List type
  $smarty->assign('_MOD_JAM_SETTINGS_LIST_TYPE', $settings->getListType());
  return $template;
 }
 private function _saveSettingsPage($smarty) {
  $smarty = $smarty;
  $template = $this->moduleBaseDir . 'smarty.tabbed_generic.tpl';
  // Start of validating the form
  $about_content = trim($this->_eliminateMagicQuotes($_POST['mod_jam_settings_page_about']));
  $list_location = $_POST['mod_jam_settings_page_list_location'];
  $list_type = $_POST['mod_jam_settings_page_list_type'];
  $validation_error = false;
  $settings = new Settings();
  // Making sure there's a value set for about content
  if (!$about_content) {
   $validation_error = true;
   $this->_appendMessage(_MOD_JAM_SETTINGS_ERROR_NO_ABOUT, true);
   $message_type = 'failure';
   $this->setMessageVar($this->_message, $message_type);
  }
  if (!$validation_error) {
   // All went well, we save changes.
   $settings->setAboutContent($about_content);
   $settings->setListLocation($list_location);
   $settings->setListType($list_type);
   eF_redirect($this->moduleBaseUrl . '&tab=settings');
  }
  else {
   $job_array = $this->_getJobTab($smarty);
   $this->_getAppTab($smarty, $job_array);
   if (isset($_POST['mod_jam_settings_page_about'])) {
    $smarty->assign('_MOD_JAM_SETTINGS_ABOUT', $about_content);
   }
   else {
    $smarty->assign('_MOD_JAM_SETTINGS_ABOUT', $settings->getAboutContent());
   }
   if (isset($_POST['mod_jam_settings_page_list_location'])) {
    $smarty->assign('_MOD_JAM_SETTINGS_LIST_LOCATION', $list_location);
   }
   else {
    $smarty->assign('_MOD_JAM_SETTINGS_LIST_LOCATION', $settings->getListLocation());
   }
   if (isset($_POST['mod_jam_settings_page_list_type'])) {
    $smarty->assign('_MOD_JAM_SETTINGS_LIST_TYPE', $list_type);
   }
   else {
    $smarty->assign('_MOD_JAM_SETTINGS_LIST_TYPE', $settings->getListType());
   }
   // Initialising top section
   $set_emails = $settings->getSetEmails();
   $emails = '';
   foreach ($set_emails AS $email) {
    $emails .= $email . ';';
   }
   $emails = substr($emails,0,-1);
   $smarty->assign('_MOD_JAM_SETTINGS_EMAILS',$emails);
   $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_ALERT_CONTENT', $settings->getEmailContent());
   $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_REPLY_CONTENT', $settings->getConfirmationEmailContent());
   // Company logo
   $smarty->assign('_MOD_JAM_SETTINGS_LOGO', $settings->getLogoFilenameWeb());
   $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_NAME",$settings->getSendName());
   $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_EMAIL",$settings->getSendEmail());
   $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_PHONE",$settings->getSendPhone());
   $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_CV_URL",$settings->getSendCvUrl());
  }

  return $template;
 }

 private function _saveSettingsLogo($smarty) {
  $smarty = $smarty;
  $template = $this->moduleBaseDir . 'smarty.tabbed_generic.tpl';
  $settings = new Settings();
  $form_error = $_FILES['mod_jam_settings_logo_file']['error'];
  $validation_error = false;
  if ($form_error == 0) {
   // We add a timestamp to the filename
   $_FILES['mod_jam_settings_logo_file']['name'] = strtotime(date('Y-m-d H:i:s')) . '_' . $_FILES['mod_jam_settings_logo_file']['name'];
   $_FILES['mod_jam_settings_logo_file']['name'] = preg_replace('/\s+/', '_', $_FILES['mod_jam_settings_logo_file']['name']);
   $_FILES['mod_jam_settings_logo_file']['name'] = preg_replace('/\'+/', '_', $_FILES['mod_jam_settings_logo_file']['name']);
   $_FILES['mod_jam_settings_logo_file']['name'] = preg_replace('/"+/', '_', $_FILES['mod_jam_settings_logo_file']['name']);
   $fs = new FileSystemTree($settings->getUploadPathLocal());
   if ($file = $fs->uploadFile('mod_jam_settings_logo_file')) {
    // Old file has to be deleted
    $old_filename = ($settings->getLogoFilename() ? $settings->getLogoFilenameLocal() : false);
    if ($old_filename) {
     @unlink($old_filename);
    }
    // Settings need to be updated 
    $new_filename = $file['physical_name'];
    $settings->setLogoFilename($new_filename);
   }
   else {
    $validation_error = true;
    $this->_appendMessage(_MOD_JAM_SETTINGS_FILE_ERROR_GENERIC, true);
    $message_type = 'failure';
    $this->setMessageVar($this->_message, $message_type);
   }
  }
  else {
   if (defined('UPLOAD_ERR_FORM_SIZE') && $form_error == UPLOAD_ERR_FORM_SIZE) {
    $validation_error = true;
    $this->_appendMessage(_MOD_JAM_SETTINGS_FILE_ERROR_SIZE, true);
    $message_type = 'failure';
    $this->setMessageVar($this->_message, $message_type);
   }
   elseif (defined('UPLOAD_ERR_INI_SIZE') && $form_error == UPLOAD_ERR_INI_SIZE) {
    $validation_error = true;
    $this->_appendMessage(_MOD_JAM_SETTINGS_FILE_ERROR_SIZE, true);
    $message_type = 'failure';
    $this->setMessageVar($this->_message, $message_type);
   }
   elseif (defined('UPLOAD_ERR_NO_FILE') && $form_error == UPLOAD_ERR_NO_FILE) {
    $validation_error = true;
    $this->_appendMessage(_MOD_JAM_SETTINGS_FILE_ERROR_NO_FILE, true);
    $message_type = 'failure';
    $this->setMessageVar($this->_message, $message_type);
   }
   elseif (defined('UPLOAD_ERR_NO_TMP_DIR') && $form_error == UPLOAD_ERR_NO_TMP_DIR) {
    $validation_error = true;
    $this->_appendMessage(_MOD_JAM_SETTINGS_FILE_ERROR_FILESYSTEM, true);
    $message_type = 'failure';
    $this->setMessageVar($this->_message, $message_type);
   }
   elseif (defined('UPLOAD_ERR_CANT_WRITE') && $form_error == UPLOAD_ERR_CANT_WRITE) {
    $validation_error = true;
    $this->_appendMessage(_MOD_JAM_SETTINGS_FILE_ERROR_FILESYSTEM, true);
    $message_type = 'failure';
    $this->setMessageVar($this->_message, $message_type);
   }
   else {
    $validation_error = true;
    $this->_appendMessage(_MOD_JAM_SETTINGS_FILE_ERROR_GENERIC, true);
    $message_type = 'failure';
    $this->setMessageVar($this->_message, $message_type);
   }
  }

  if (!$validation_error) {
   eF_redirect($this->moduleBaseUrl . '&tab=settings');
  }

  $job_array = $this->_getJobTab($smarty);
  $this->_getAppTab($smarty, $job_array);
  // Initialising top section
  $set_emails = $settings->getSetEmails();
  $emails = '';
  foreach ($set_emails AS $email) {
   $emails .= $email . ';';
  }
  $emails = substr($emails,0,-1);
  // Company logo
  $smarty->assign('_MOD_JAM_SETTINGS_LOGO', $settings->getLogoFilenameWeb());
  $smarty->assign('_MOD_JAM_SETTINGS_EMAILS',$emails);
  $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_ALERT_CONTENT', $settings->getEmailContent());
  $smarty->assign('_MOD_JAM_SETTINGS_EMAIL_REPLY_CONTENT', $settings->getConfirmationEmailContent());
  $smarty->assign('_MOD_JAM_SETTINGS_ABOUT', $settings->getAboutContent());
  $smarty->assign('_MOD_JAM_SETTINGS_LIST_LOCATION', $settings->getListLocation());
  $smarty->assign('_MOD_JAM_SETTINGS_LIST_TYPE', $settings->getListType());
  $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_NAME",$settings->getSendName());
  $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_EMAIL",$settings->getSendEmail());
  $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_PHONE",$settings->getSendPhone());
  $smarty->assign("_MOD_JAM_SETTINGS_EMAIL_EXTRAS_CV_URL",$settings->getSendCvUrl());


  return $template;
 }

 ///////////////////////////////////////////////////////////////////////////
 // INSTALL & UNINSTALL PROCEDURES BELOW
 ///////////////////////////////////////////////////////////////////////////

 public function onInstall() {
  eF_executeNew('DROP TABLE IF EXISTS `mod_jam_job_app`');
  eF_executeNew('DROP TABLE IF EXISTS `mod_jam_job`');
  eF_executeNew('DROP TABLE IF EXISTS `mod_jam_settings`');
  eF_executeNew('DROP TABLE IF EXISTS `mod_jam_recipients`');
  $sql = 'CREATE TABLE `mod_jam_job` (';
  $sql .= '`id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,';
  $sql .= '`title` VARCHAR(255) NOT NULL,';
  $sql .= '`code` VARCHAR(20) NOT NULL, ';
  $sql .= '`description` TEXT,';
  $sql .= '`remuneration` VARCHAR(50) NOT NULL, ';
  $sql .= '`skills` TEXT,';
  $sql .= '`company_desc` TEXT,';
  $sql .= '`type` ENUM(\'Full-time\',\'Part-time\',\'Contract\',\'Temporary\',\'Other\') NOT NULL,';
  $sql .= '`experience` ENUM (\'Executive\',\'Director\',\'Mid-senior level\',\'Associate\',\'Entry level\',\'Internship\',\'Non-applicable\') NOT NULL,';
  $sql .= '`functions` TEXT,';
  $sql .= '`active` TINYINT(1) UNSIGNED DEFAULT 1,';
  $sql .= '`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,';
  $sql .= 'PRIMARY KEY (`id`)';
  $sql .= ')Engine=InnoDB charset=utf8;';
  eF_executeNew($sql);
  // Adding demo data
  $sql = 'INSERT INTO `mod_jam_job` (`id`,`title`,`code`,`description`,`remuneration`,`skills`,`company_desc`,`type`,`experience`,`functions`) ';
  $sql .= 'VALUES (1,"Job title #1","CODE-DEV-1","Some description for this job","10","The required skills for this job","The company description for this job",2,2,"Advertising::Analyst"),';
  $sql .= '(2,"Job title #2","CODE-DEV-2","Some description for this job here","20","The required skills for this job here","The company description for this job here",3,3,"Administrative::Advertising::Analyst"),';
  $sql .= '(3,"Job title #3","CODE-DEV-3","Some description for the job","30","The required skills for the job","The company description for the job",1,1,"Administrative::Advertising::Analyst"),';
  $sql .= '(4,"Job title #4","CODE-DEV-4","Some description for the job","40","The required skills","The company description",2,1,"Administrative::Advertising");';
  //eF_executeNew($sql);
  $sql = 'CREATE TABLE `mod_jam_job_app` (';
  $sql .= '`id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,';
  $sql .= '`job_id` SMALLINT(5) UNSIGNED NOT NULL,';
  $sql .= '`name` VARCHAR(255) NOT NULL,';
  $sql .= '`email` VARCHAR(255) NOT NULL,';
  $sql .= '`phone` VARCHAR(30) NOT NULL,';
  $sql .= '`city` VARCHAR(255) NOT NULL,';
  $sql .= '`country` VARCHAR(255) NOT NULL,';
  $sql .= '`cover` TEXT,';
  $sql .= '`read` TINYINT(1) NOT NULL DEFAULT 0,';
  $sql .= '`cv_filename` VARCHAR(255) NOT NULL,';
  $sql .= '`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,';
  $sql .= 'PRIMARY KEY (`id`),';
  $sql .= 'FOREIGN KEY (`job_id`) REFERENCES `mod_jam_job`(`id`) ON UPDATE CASCADE ON DELETE CASCADE';
  $sql .= ')Engine=InnoDB charset=utf8;';
  eF_executeNew($sql);
  // Adding demo data here
  $sql = 'INSERT INTO `mod_jam_job_app` (`job_id`,`name`,`email`,`phone`,`city`,`country`,`cover`,`cv_filename`) ';
  $sql .= 'VALUES (1,"Athanasios Fotoglidis","a.fotoglidis@gmail.com","6974150363","Thessaloniki","Hellas","This is a short explanation why I think I am right for this job.","331233901390123_filename.pdf"),';
  $sql .= '(1,"Giorgos Vasileiou","g.vasileiou@gmail.com","6974150364","Kalamata","Hellas","This is a short explanation why I think I am right for this job.","871213541390183_filename.pdf"),';
  $sql .= '(4,"Jonas Carlfors","j.carlfors@gmail.com","6974150369","Umea","Sweden","This is a short cover letter.","848275546598141_filename.pdf"),';
  $sql .= '(2,"John Smith","j.smith@gmail.com","6974150365","Edinburgh","Scotland","This is a short explanation why I think I am right for this job.","871213541390183_filename.pdf");';
  //eF_executeNew($sql);
  $sql = 'CREATE TABLE `mod_jam_settings` (';
  $sql .= '`logo_filename` VARCHAR(255) NOT NULL,';
  $sql .= '`about_content` TEXT NOT NULL,';
  $sql .= '`list_location` ENUM("RIGHT","LEFT") DEFAULT "LEFT", ';
  $sql .= '`list_type` ENUM("LIST","SELECT") DEFAULT "SELECT", ';
  $sql .= '`email_content` TEXT NOT NULL, ';
  $sql .= '`confirmation_email_content` TEXT NOT NULL, ';
  $sql .= '`send_name` TINYINT(1) DEFAULT 0, ';
  $sql .= '`send_email` TINYINT(1) DEFAULT 0, ';
  $sql .= '`send_phone` TINYINT(1) DEFAULT 0, ';
  $sql .= '`send_cv_url` TINYINT(1) DEFAULT 0';
  $sql .= ')Engine=InnoDB charset=utf8;';
  eF_executeNew($sql);
  $sql = 'INSERT INTO `mod_jam_settings` (`logo_filename`, `about_content`, `list_location`, `list_type`, `email_content`, `confirmation_email_content`) ';
  $sql .= 'VALUES ("logo_filename.jpg",';
  $sql .= '"<p><strong>About us</strong></p><p>This is a short description of the organisation.</p><p>Please provide a detailed description of your organisation here.</p>",';
  $sql .= '"LEFT", ';
  $sql .= '"SELECT", ';
  $sql .= '"<p>A new job application has been recorded.</p><p>Here is a summary of what has been submitted:</p><p>--------</p><p>Job position: ###JOB_TITLE###</p><p>Applicant: ###NAME###</p><p>E-mail: ###EMAIL###</p><p>CV: ###CV###</p><p>Cover letter: ###COVER###</p>",';
  $sql .= '"<p>Dear ###NAME###,</p><p>Thank you for your interest in the job titled: ###JOB_TITLE###</p><p>Your application has been recorded in our system.</p><p>We will be in touch with you regarding your job application soon.</p><p>Here is a summary of what you have submitted:</p><p>--------</p><p>Job position: ###JOB_TITLE###</p><p>Applicant: ###NAME###</p><p>E-mail: ###EMAIL###</p><p>Cover letter: ###COVER###</p>");';
  eF_executeNew($sql);
  $sql = 'CREATE TABLE `mod_jam_recipients` (';
  $sql .= '`id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,';
  $sql .= '`email` VARCHAR(255) NOT NULL,';
  $sql .= 'PRIMARY KEY (`id`)';
  $sql .= ')Engine=InnoDB charset=utf8;';
  eF_executeNew($sql);

  return true;
 }

 public function onUninstall() {
  eF_executeNew('DROP TABLE IF EXISTS `mod_jam_job_app`');
  eF_executeNew('DROP TABLE IF EXISTS `mod_jam_job`');
  eF_executeNew('DROP TABLE IF EXISTS `mod_jam_settings`');
  eF_executeNew('DROP TABLE IF EXISTS `mod_jam_recipients`');
  return true;
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
}
?>
