<?php

class JobManager extends AbstractClass {

 private $_jobs;

 /**

	 * Overriding the parent constructor.

	 */
 public function __construct() {
  $this->_jobs = array();
  $this->_init();
 }
 protected function _init() {
  try {
   $options = array();
   $this->_dao = DAOFactory::getDAO($this, $options);
   $this->_dao->_init();
   $this->_to = $this->_dao->getTO();
   $this->_jobs = $this->_to->get('jobs');
  }
  catch (AF_Exception $afe) {
   $this->_jobs = array();
   throw new AF_Exception("Job Manager could not be initialised.");
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
 public function getJobCount() {
  return count($this->_jobs);
 }
 public function getJobs() {
  return $this->_jobs;
 }
 public function getJob($job_id) {
  $job_item = false;
  if (array_key_exists($job_id, $this->_jobs)) {
   if ($this->_jobs[$job_id] === null) {
    $this->_jobs[$job_id] = new Job($job_id);
   }
   $job_item = $this->_jobs[$job_id];
  }
  return $job_item;
 }
 public function getJobPage($current_page, $items_per_page) {
  $job_array = array_chunk($this->_jobs, $items_per_page, true);
  $job_array = $job_array[$current_page];
  if (count($job_array)) {
   $tmp_array = $job_array;
   $job_array = array();
   foreach ($tmp_array AS $job_id => $job) {
    $job_array[$job_id] = $this->getJob($job_id);
   }
  }

  return $job_array;
 }

 public function removeJob($job_id) {
  $success = false;
  if (array_key_exists($job_id, $this->_jobs)) {
   try {
    // We need to remove all application files from the filesystem.
    $job = new Job($job_id);
    $job_apps = $job->getAllApplications();
    foreach ($job_apps AS $app_id => $app) {
     try {
      $app = $job->getApplication($app_id);
      $app->removeCv();
     }
     catch (Exception $e) { /* NO ACTION REQUIRED */ }
    }
   }
   catch (Exception $e) { /* DO NOTHING */ }
   unset($this->_jobs[$job_id]);
   $this->_to->set('jobs', $this->_jobs);
   $this->_to->setChanged();
   $success = true;
  }
  return $success;
 }


 public function addJob($args) {
  $object = false;
  // Validation logic carried out on app logic level
  if ($job_id = $this->_dao->addJob($args)) {
   $this->_jobs[$job_id] = null;
   $object = $this->getJob($job_id);
  }

  return $object;
 }

 ///////////////////////////////////////////////////////////////////////////
 // END GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////

}
