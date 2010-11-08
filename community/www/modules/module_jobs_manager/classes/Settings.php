<?php

class Settings extends AbstractClass {

 private $_email_content;
 private $_confirmation_email_content;
 private $_about_content;
 private $_logo_filename;
 private $_list_location;
 private $_list_type;
 private $_all_emails;
 private $_set_emails;

 private $_email_send_name;
 private $_email_send_email;
 private $_email_send_phone;
 private $_email_send_cv_url;

 private $_upload_path_local;
 private $_upload_path_web;
 private $_public_page_path;

 public function __construct() {
  parent::__construct(false);
  $this->_initPaths();
  $this->_init();
 }

 protected function _init() {
  try {
   $options = array();
   $this->_dao = DAOFactory::getDAO($this, $options);

   $this->_dao->_init();
   $this->_to = $this->_dao->getTO();
   $this->_email_content = $this->_to->get('email_content');
   $this->_confirmation_email_content = $this->_to->get('confirmation_email_content');
   $this->_about_content = $this->_to->get('about_content');
   $this->_list_location = $this->_to->get('list_location');
   $this->_list_type = $this->_to->get('list_type');
   $this->_all_emails = $this->_to->get('all_emails');
   $this->_set_emails = $this->_to->get('set_emails');
   $this->_logo_filename = $this->_to->get('logo_filename');
   $this->_email_send_cv_url = $this->_to->get('send_cv_url');
   $this->_email_send_email = $this->_to->get('send_email');
   $this->_email_send_name = $this->_to->get('send_name');
   $this->_email_send_phone = $this->_to->get('send_phone');
  }
  catch (Exception $afe) {
   $this->_email_content = '';
   $this->_confirmation_email_content = '';
   $this->_about_content = '';
   $this->_list_location = '';
   $this->_list_type = '';
   $this->_all_emails = '';
   $this->_set_emails = '';
   $this->_logo_filename = '';
   $this->_email_send_cv_url = 0;
   $this->_email_send_email = 0;
   $this->_email_send_name = 0;
   $this->_email_send_phone = 0;
   throw new Exception("Settings not found.");
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

  // public page path
  $this->_public_page_path = $baseUrl.$_SERVER['REQUEST_URI'];
  $this->_public_page_path = substr($this->_public_page_path, 0, stripos($this->_public_page_path, 'administrator.php'));
  $this->_public_page_path .= 'modules/module_jobs_manager/public/';
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
 // GETTERS START HERE
 ///////////////////////////////////////////////////////////////////////////
 public function getSendCvUrl() {
  return ($this->_email_send_cv_url ? true : false);
 }
 public function getSendEmail() {
  return ($this->_email_send_email ? true : false);
 }
 public function getSendName() {
  return ($this->_email_send_name ? true : false);
 }
 public function getSendPhone() {
  return ($this->_email_send_phone ? true : false);
 }

 public function getUserEmails() {
  return $this->_all_emails;
 }

 public function getSetEmails() {
  return $this->_set_emails;
 }

 public function getEmailContent() {
  return $this->_email_content;
 }

 public function getConfirmationEmailContent() {
  return $this->_confirmation_email_content;
 }

 public function getAboutContent() {
  return $this->_about_content;
 }

 public function getListLocation() {
  return $this->_list_location;
 }

 public function getListType() {
  return $this->_list_type;
 }

 public function getLogoFilename() {
  return $this->_logo_filename;
 }

 public function getLogoFilenameLocal() {
  $path = false;
  if ($this->_logo_filename) $path = $this->_upload_path_local . $this->_logo_filename;

  return $path;
 }

 public function getLogoFilenameWeb() {
  $path = false;
  if ($this->_logo_filename) $path = $this->_upload_path_web . $this->_logo_filename;

  return $path;
 }

 public function getUploadPathLocal() {
  return $this->_upload_path_local;
 }

 public function getUploadPathWeb() {
  return $this->_upload_path_web;
 }

 public function getPublicPageUrl() {
  return $this->_public_page_path;
 }

 ///////////////////////////////////////////////////////////////////////////
 // GETTERS END HERE
 ///////////////////////////////////////////////////////////////////////////

 ///////////////////////////////////////////////////////////////////////////
 // SETTERS START HERE
 ///////////////////////////////////////////////////////////////////////////

 public function setSetEmails($data) {
  if ($data <> $this->_set_emails && is_array($data)) {
   $this->_set_emails = $data;
   $this->_to->set('set_emails', $this->_set_emails);
   $this->_to->setChanged();
  }
 }

 public function setEmailContent($data) {
  if ($data <> $this->_email_content) {
   $this->_email_content = $data;
   $this->_to->set('email_content', $this->_email_content);
   $this->_to->setChanged();
  }
 }

 public function setConfirmationEmailContent($data) {
  if ($data <> $this->_confirmation_email_content) {
   $this->_confirmation_email_content = $data;
   $this->_to->set('confirmation_email_content', $this->_confirmation_email_content);
   $this->_to->setChanged();
  }
 }

 public function setAboutContent($data) {
  if ($data <> $this->_about_content) {
   $this->_about_content = $data;
   $this->_to->set('about_content', $this->_about_content);
   $this->_to->setChanged();
  }
 }

 public function setListLocation($data) {
  if ($data <> $this->_list_location) {
   $this->_list_location = $data;
   $this->_to->set('list_location', $this->_list_location);
   $this->_to->setChanged();
  }
 }

 public function setListType($data) {
  if ($data <> $this->_list_type) {
   $this->_list_type = $data;
   $this->_to->set('list_type', $this->_list_type);
   $this->_to->setChanged();
  }
 }

 public function setLogoFilename($data) {
  if ($data <> $this->_logo_filename) {
   $this->_logo_filename = $data;
   $this->_to->set('logo_filename', $this->_logo_filename);
   $this->_to->setChanged();
  }
 }

 public function setSendEmail($bool) {
  $bool = ($bool ? 1 : 0);
  if ($bool <> $this->_email_send_email) {
   $this->_email_send_email = $bool;
   $this->_to->set('send_email', $this->_email_send_email);
   $this->_to->setChanged();
  }
 }

 public function setSendName($bool) {
  $bool = ($bool ? 1 : 0);
  if ($bool <> $this->_email_send_name) {
   $this->_email_send_name = $bool;
   $this->_to->set('send_name', $this->_email_send_name);
   $this->_to->setChanged();
  }
 }

 public function setSendCvUrl($bool) {
  $bool = ($bool ? 1 : 0);
  if ($bool <> $this->_email_send_cv_url) {
   $this->_email_send_cv_url = $bool;
   $this->_to->set('send_cv_url', $this->_email_send_cv_url);
   $this->_to->setChanged();
  }
 }

 public function setSendPhone($bool) {
  $bool = ($bool ? 1 : 0);
  if ($bool <> $this->_email_send_phone) {
   $this->_email_send_phone = $bool;
   $this->_to->set('send_phone', $this->_email_send_phone);
   $this->_to->setChanged();
  }
 }

 ///////////////////////////////////////////////////////////////////////////
 // SETTERS END HERE
 ///////////////////////////////////////////////////////////////////////////

}
