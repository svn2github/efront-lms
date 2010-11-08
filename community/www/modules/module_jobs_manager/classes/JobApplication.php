<?php

class JobApplication extends AbstractClass {

 private $_job_id;
 private $_name;
 private $_email;
 private $_phone;
 private $_city;
 private $_country;
 private $_cover;
 private $_cv_filename;
 private $_read;
 private $_date_added;

 private $_upload_path_local;
 private $_upload_path_web;

 public function __construct($id) {
  parent::__construct($id);
  $this->_initPaths();
  $this->_init();
 }

 protected function _init() {
  try {
   $options = array();
   $this->_dao = DAOFactory::getDAO($this, $options);
   $this->_dao->_init();
   $this->_to = $this->_dao->getTO();

   $this->_job_id = $this->_to->get('job_id');
   $this->_name = $this->_to->get('name');
   $this->_email = $this->_to->get('email');
   $this->_phone = $this->_to->get('phone');
   $this->_city = $this->_to->get('city');
   $this->_country = $this->_to->get('country');
   $this->_cover = $this->_to->get('cover');
   $this->_cv_filename = $this->_to->get('cv_filename');
   $this->_read = $this->_to->get('read');
   $this->_date_added = $this->_to->get('date_added');
  }
  catch (Exception $afe) {
   $this->_name = '';
   $this->_job_id = '';
   $this->_email = '';
   $this->_phone = '';
   $this->_city = '';
   $this->_country = '';
   $this->_cover = '';
   $this->_cv_filename = '';
   $this->_read = '';
   $this->_date_added = '';
   throw new Exception("Job Application item not found.");
  }
 }

 protected function _initPaths() {
  // Processing upload paths
  // Assumes that the 'uploads' folder is located at the same level as the 'classes' folder
  // Uload local path
  $tmp_path = dirname(__FILE__);
  $this->_upload_path_local = str_ireplace('classes','uploads'. DIRECTORY_SEPARATOR, $tmp_path);

  // Upload web path
  $protocol = 'http';
  if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
   $protocol = 'https';
  }
  $host = $_SERVER['HTTP_HOST'];
  $baseUrl = $protocol . '://' . $host;
  if (substr($baseUrl, -1)=='/') {
   $baseUrl = substr($baseUrl, 0, strlen($baseUrl)-1);
  }
  $this->_upload_path_web = $baseUrl.$_SERVER['REQUEST_URI'];
  $this->_upload_path_web = substr($this->_upload_path_web, 0, stripos($this->_upload_path_web, 'administrator.php'));
  $this->_upload_path_web .= 'modules/module_jobs_manager/uploads/';
  //$this->_upload_path_web = str_ireplace($_SERVER['DOCUMENT_ROOT'], $baseUrl, $this->_upload_path_local);
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
 /**

	 * Retrieves the name of the applicant.

	 * 

	 * @return String The value of the name attribute.

	 * 

	 * @access public

	 */
 public function getName () { return $this->_name; }
 /**

	 * Retrieves the id of the parent job instance.

	 * 

	 * @return String THe id of the parent job instance.

	 * 

	 * @access public

	 */
 public function getJobId() { return $this->_job_id; }
 /**

	 * Retrieves the value of the email attribute of the application.

	 * 

	 * @return String The value of the email attribute of the application.

	 * 

	 * @access public

	 */
 public function getEmail() { return $this->_email; }
 /**

	 * Retrieves the value of the phone attribute of the application.

	 * 

	 * @return String The value of the phone attribute of the application.

	 * 

	 * @access public

	 */
 public function getPhone() { return $this->_phone; }
 /**

	 * Retrieves the value of the city attribute of the application.

	 * 

	 * @return String The value of the city attribute of the application.

	 * 

	 * @access public

	 */
 public function getCity() { return $this->_city; }
 /**

	 * Retrieves the value of the country attribute of the application.

	 * 

	 * @return String The value of the country attribute of the application.

	 * 

	 * @access public

	 */
 public function getCountry() { return $this->_country; }
 /**

	 * Retrieves the value of the cover attribute of the application.

	 * 

	 * @return String The value of the cover attribute of the application.

	 * 

	 * @access public

	 */
 public function getCover() { return $this->_cover; }
 /**

	 * Retrieves the value of the cv_filename attribute of the application.

	 * 

	 * @return String The value of the cv_filename attribute of the application.

	 * 

	 * @access public

	 */
 public function getCvFilename() { return $this->_cv_filename; }
 public function getUploadPathLocal() {
  return $this->_upload_path_local;
 }
 public function getUploadPathWeb() {
  return $this->_upload_path_web;
 }
 /**

	 * Retrieves the path to the CV in local filesystem context.

	 * 

	 * @return mixed String if a path is found, false otherwise.

	 * 

	 * @access public

	 */
 public function getCvFilenameLocal() {
  $path = false;
  if ($this->_cv_filename) $path = $this->_upload_path_local . $this->_cv_filename;
  return $path;
 }
 /**

	 * Retrieves the path to the CV in web context.

	 * 

	 * @return mixed String if a path is found, false otherwise.

	 * 

	 * @access public

	 */
 public function getCvFilenameWeb() {
  $path = false;
  if ($this->_cv_filename) $path = $this->_upload_path_web . $this->_cv_filename;
  return $path;
 }
 /**

	 * Retrieves the URL to the file uploaded by applicant.

	 * 

	 * @return String The URL to the cv file.

	 * 

	 * @access public

	 */
 public function getCvURL() {
  // TO-DO
  // fix logic here
  return $this->_cv_filename;
 }
 /**

	 * Determines whether the application has been read or not.

	 * 

	 * @return bool The result of the check.

	 * 

	 * @access public

	 */
 public function isRead() { return ($this->_read ? true : false); }
 /**

	 * Retrieves the Job listing date_added attribute.

	 * 

	 * @return string The job listing date_added.

	 * 

	 * @access public

	 */
 public function getDateAdded() { return $this->_date_added; }
 ///////////////////////////////////////////////////////////////////////////
 // END OF GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////
 ///////////////////////////////////////////////////////////////////////////
 // SETTERS HERE ///////////////////////////////////////////////////////////
 ///////////////////////////////////////////////////////////////////////////
 /**

	 * Sets the new value for the object's read attribute. It updates 

	 * live instance and also delegates DB update to DAO by altering the TO.

	 * 

	 * @param bool $data The new value for the read attribute of the object.

	 * 

	 * @access public

	 */
 public function setRead($data) {
  // Bool logic is carried out at action level
  if ($data <> $this->_read) {
   $this->_read = $data;
   $this->_to->set('read', $this->_read);
   $this->_to->setChanged();
  }
 }
 /**

	 * Removes the CV file from the filesystem, if it exists.

	 * 

	 * @access public

	 */
 public function removeCv() {
  if ($this->_cv_filename) {
   @unlink($this->getCvFilenameLocal());
  }
 }
 ///////////////////////////////////////////////////////////////////////////
 // END OF GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////
}
