<?php

class Job extends AbstractClass {

 private $_code;
 private $_title;
 private $_description;
 private $_skills;
 private $_company_desc;
 private $_remuneration;
 private $_type;
 private $_experience;
 private $_functions;
 private $_active;
 private $_date_added;

 private $_applications;

 public function __construct($id) {
  parent::__construct($id);
  $this->_applications = array();
  $this->_init();
 }

 protected function _init() {
  try {
   $options = array();
   $this->_dao = DAOFactory::getDAO($this, $options);
   $this->_dao->_init();
   $this->_to = $this->_dao->getTO();

   $this->_code = $this->_to->get('code');
   $this->_title = $this->_to->get('title');
   $this->_description = $this->_to->get('description');
   $this->_skills = $this->_to->get('skills');
   $this->_company_desc = $this->_to->get('company_desc');
   $this->_type = $this->_to->get('type');
   $this->_remuneration = $this->_to->get('remuneration');
   $this->_experience = $this->_to->get('experience');
   $this->_functions = $this->_to->get('functions');
   $this->_active = $this->_to->get('active');
   $this->_date_added = $this->_to->get('date_added');

   $this->_applications = $this->_to->get('apps');
  }
  catch (Exception $afe) {
   $this->_code = '';
   $this->_title = '';
   $this->_description = '';
   $this->_skills = '';
   $this->_company_desc = '';
   $this->_type = '';
   $this->_remuneration = '';
   $this->_experience = '';
   $this->_functions = '';
   $this->_active = '';
   $this->_date_added = '';
   throw new Exception("Job item not found.");
  }
 }

 /**

	 * This is the implementation of the abstract method inherited from 

	 * the parent class AF_Controller_Abstract.

	 * 

	 * @return String The processed controller name.

	 */
 public function getControllerName() {
  return get_class($this);
 }
 ///////////////////////////////////////////////////////////////////////////
 // GETTER METHODS BELOW
 ///////////////////////////////////////////////////////////////////////////
 public function getCode() { return $this->_code; }
 public function getRemuneration() { return $this->_remuneration; }
 /**

	 * Retrieves the job listing title.

	 * 

	 * @return String The title of the job opening.

	 * 

	 * @access public

	 */
 public function getTitle() {
  return $this->_title;
 }
 /**

	 * Retrieves the Description of the job listing.

	 * 

	 * @return String The description of the job listing.

	 * 

	 * @access public

	 */
 public function getDescription() {
  return $this->_description;
 }
 /**

	 * Retrieves the skills of the job listing.

	 * 

	 * @return String The skills of the job.

	 * 

	 * @access public

	 */
 public function getSkills() {
  return $this->_skills;
 }
 /**

	 * Retrieves the skills of the job listing.

	 * 

	 * @return String The skills of the job.

	 * 

	 * @access public

	 */
 public function getCompanyDesc() {
  return $this->_company_desc;
 }
 /**

	 * Retrieves the type of the job listing.

	 * 

	 * @return String The type of the job.

	 * 

	 * @access public

	 */
 public function getType() {
  return $this->_type;
 }
 /**

	 * Retrieves the experience of the job listing.

	 * 

	 * @return String The experience of the job.

	 * 

	 * @access public

	 */
 public function getExperience() {
  return $this->_experience;
 }
 /**

	 * Retrieves the functions of the job listing.

	 * 

	 * @return String The functions of the job.

	 * 

	 * @access public

	 */
 public function getFunctions() {
  return $this->_functions;
 }
 public function getFunctionsArray() {
  $array = array();
  $tmp_array = explode('::', $this->_functions);
  foreach ($tmp_array AS $key => $val) {
   if (trim($val) <> '') $array [] = $val;
  }
  return $array;
 }
 /**

	 * Retrieves the Job listing date_added attribute.

	 * 

	 * @return string The job listing date_added.

	 * 

	 * @access public

	 */
 public function getDateAdded() {
  return $this->_date_added;
 }
 /**

	 * Determines whether this is an active job opening or not.

	 * 

	 * @return bool Indicates check result.

	 * 

	 * @access public

	 */
 public function isActive() {
  return ($this->_active ? true : false);
 }
 /**

	 * Retrieves the amount of applications found for this job.

	 * 

	 * @return mixed The amount of applications found for this job.

	 * 

	 * @access public

	 */
 public function getApplicationCount() {
  return count($this->_applications);
 }
 /**

	 * Retrieves a sub-set, defined by tuple index and tuple size, of the 

	 * job applications for this job opening.

	 * 

	 * @param string $current_page The tuple index.

	 * @param string $items_per_page The tuple size.

	 * 

	 * @access public

	 */
 public function getApplicationPage($current_page, $items_per_page) {
  $app_array = array_chunk($this->_applications, $items_per_page, true);
  $app_array = $array[$current_page];
  if (count($app_array)) {
   foreach ($app_array AS $app_id => &$app) {
    $app = $this->getApplication($app_id);
   }
  }
  return $app_array;
 }
 /**

	 * Retrieves the job applications for this job.

	 * 

	 * @return Array The array that holds the job applications for this job. 

	 * The array keys are the job application id's and the values can be 

	 * either a solid JobApplication instance for each key or FALSE.

	 * 

	 * @access public

	 */
 public function getAllApplications() {
  return $this->_applications;
 }
 /**

	 * Retrieves the Job application instance that ocrresponds to the 

	 * given application id. If the application id does not correspond to some 

	 * valid job application entry then FALSE is returned instead.

	 * 

	 * @param string $app_id The id of the application we're after.

	 * @return mixed JobApplication instance on success and false on failure.

	 * 

	 * @access public

	 */
 public function getApplication($app_id) {
  $app_item = false;
  if (array_key_exists($app_id, $this->_applications)) {
   if ($this->_applications[$app_id] === null) {
    $this->_applications[$app_id] = new JobApplication($app_id);
   }
   $app_item = $this->_applications[$app_id];
  }
  return $app_item;
 }
 ///////////////////////////////////////////////////////////////////////////
 // END OF GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////
 ///////////////////////////////////////////////////////////////////////////
 // SETTERS HERE ///////////////////////////////////////////////////////////
 ///////////////////////////////////////////////////////////////////////////
 public function setCode($data) {
  if ($data <> $this->_code) {
   $this->_code = $data;
   $this->_to->set('code', $this->_code);
   $this->_to->setChanged();
  }
 }
 public function setRemuneration($data) {
  if ($data <> $this->_remuneration) {
   $this->_remuneration = $data;
   $this->_to->set('remuneration', $this->_remuneration);
   $this->_to->setChanged();
  }
 }
 /**

	 * Sets the new value for the object's title. It updates live instance and

	 * also delegates DB update to DAO by altering the TO.

	 * 

	 * @param String $data The new value for the title of the object.

	 * 

	 * @access public

	 */
 public function setTitle($data) {
  if ($data <> $this->_title) { // Not running anything unless we really have to
   $this->_title = $data;
   $this->_to->set('title', $this->_title);
   $this->_to->setChanged();
  }
 }
 /**

	 * Sets the new value for the object's functions. It updates live instance 

	 * and also delegates DB update to DAO by altering the TO.

	 * 

	 * @param String $data The new value for the functions of the object.

	 * 

	 * @access public

	 */
 public function setFunctions($data) {
  if ($data <> $this->_functions) {
   $this->_functions = $data;
   $this->_to->set('functions', $this->_functions);
   $this->_to->setChanged();
  }
 }
 /**

	 * Sets the new value for the object's type. It updates live instance 

	 * and also delegates DB update to DAO by altering the TO.

	 * 

	 * @param String $data The new value for the type of the object.

	 * 

	 * @access public

	 */
 public function setType($data) {
  if ($data <> $this->_type) {
   $this->_type = $data;
   $this->_to->set('type', $this->_type);
   $this->_to->setChanged();
  }
 }
 /**

	 * Sets the new value for the object's company desc. It updates live instance 

	 * and also delegates DB update to DAO by altering the TO.

	 * 

	 * @param String $data The new value for the company desc of the object.

	 * 

	 * @access public

	 */
 public function setCompanyDesc($data) {
  if ($data <> $this->_company_desc) {
   $this->_company_desc = $data;
   $this->_to->set('company_desc', $this->_company_desc);
   $this->_to->setChanged();
  }
 }
 /**

	 * Sets the new value for the object's skills. It updates live instance 

	 * and also delegates DB update to DAO by altering the TO.

	 * 

	 * @param String $data The new value for the skills of the object.

	 * 

	 * @access public

	 */
 public function setSkills($data) {
  if ($data <> $this->_skills) {
   $this->_skills = $data;
   $this->_to->set('skills', $this->_skills);
   $this->_to->setChanged();
  }
 }
 /**

	 * Sets the new value for the object's experience attribute. It updates 

	 * live instance and also delegates DB update to DAO by altering the TO.

	 * 

	 * @param bool $data The new value for the experience attribute of the 

	 * object.

	 * 

	 * @access public

	 */
 public function setExperience($data) {
  // Bool logic is carried out at action level
  if ($data <> $this->_experience) {
   $this->_experience = $data;
   $this->_to->set('experience', $this->_experience);
   $this->_to->setChanged();
  }
 }
 /**

	 * Sets the new value for the object's description. It updates live 

	 * instance and also delegates DB update to DAO by altering the TO.

	 * 

	 * @param String $data The new value for the description of the object.

	 * 

	 * @access public

	 */
 public function setDescription($data) {
  if ($data <> $this->_description) {
   $this->_description = $data;
   $this->_to->set('description', $this->_description);
   $this->_to->setChanged();
  }
 }
 /**

	 * Sets the new value for the object's active attribute. It updates 

	 * live instance and also delegates DB update to DAO by altering the TO.

	 * 

	 * @param bool $data The new value for the active attribute of the object.

	 * 

	 * @access public

	 */
 public function setActive($data) {
  // Bool logic is carried out at action level
  if ($data <> $this->_active) {
   $this->_active = $data;
   $this->_to->set('active', $this->_active);
   $this->_to->setChanged();
  }
 }
 ///////////////////////////////////////////////////////////////////////////
 // END OF GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////
 public function addApplication($args) {
  $object = false;
  // Validation logic carried out on app logic level
  if ($app_id = $this->_dao->addApp($args)) {
   $this->_applications[$app_id] = null;
   $object = $this->getApplication($app_id);
  }
  return $object;
 }
 public function removeApplication($app_id) {
  $success = false;
  if (array_key_exists($app_id, $this->_applications)) {
   try {
    // We need to remove the app files
    $app = $this->getApplication($app_id);
    $app->removeCv();
   }
   catch (Exception $e) { /* DO NOTHING */ }
   unset($this->_applications[$app_id]);
   $this->_to->set('apps', $this->_applications);
   $this->_to->setChanged();
   $success = true;
  }
  return $success;
 }
}
