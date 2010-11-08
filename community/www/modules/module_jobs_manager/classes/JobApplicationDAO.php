<?php

class JobApplicationDAO extends AbstractDAO {

 public function __construct($controller) {
  parent::__construct($controller);
  $this->_db = $GLOBALS['db'];
  $this->_init();
 }

 public function __destruct() {
  if ($this->_to->isChanged()) {
   $read = $this->extractFromTO('read');
   $GLOBALS['db'] = $this->_db;
   eF_updateTableData("`mod_jam_job_app`",array("`read`"=>$read),"`id`=".$this->_controller->getId());
  }
 }

 /**

	 * Retrieving contents of the `mod_jam_job_app` table so as to initialise 

	 * the state of the application.

	 * 

	 * @access public

	 */
 public function _init() {
  $apps = array();
  $res = eF_getTableData("`mod_jam_job_app`","*", "`id`=".$this->_controller->getId());
  if (is_array($res) && count($res)) {
   $this->_to->set('job_id', $res[0]['job_id']);
   $this->_to->set('name', $res[0]['name']);
   $this->_to->set('email', $res[0]['email']);
   $this->_to->set('phone', $res[0]['phone']);
   $this->_to->set('city', $res[0]['city']);
   $this->_to->set('country', $res[0]['country']);
   $this->_to->set('cover', $res[0]['cover']);
   $this->_to->set('read', $res[0]['read']);
   $this->_to->set('cv_filename', $res[0]['cv_filename']);
   $this->_to->set('date_added', $res[0]['date_added']);
  }
 }
}
