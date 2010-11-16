<?php

/**

 * Represents the logic entity that handles the central FUZE Meetings account

 * in a module-wide context.

 * 

 * @name FUZE_Account

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_Account extends FUZE_AbstractClass {
 private $_consumer_key;
 private $_consumer_secret;
 public function __construct($id = false) {
  parent::__construct($id);
  $this->_init();
 }
 protected function _init() {
  try {
   $options = array();
   $this->_dao = FUZE_DAOFactory::getDAO($this, $options);
   $this->_dao->_init();
   $this->_to = $this->_dao->getTO();
   $this->_consumer_key = $this->_to->get('consumer_key');
   $this->_consumer_secret = $this->_to->get('consumer_secret');
  }
  catch (Exception $afe) {
   $this->_consumer_key = '';
   $this->_consumer_secret = '';
   throw new Exception("FUZE trunk account data not found.");
  }
 }
 /**

	 * This is the implementation of the abstract method inherited from 

	 * the parent class AF_Controller_Abstract.

	 * 

	 * @return String The processed controller name.

	 */
 public function getControllerName() {
  return 'FUZE_Account';
 }
 ///////////////////////////////////////////////////////////////////////////
 // GETTER METHODS BELOW
 ///////////////////////////////////////////////////////////////////////////
 public function getConsumerKey() {
  return $this->_consumer_key;
 }
 public function getConsumerSecret() {
  return $this->_consumer_secret;
 }
 public function isRegistered() {
  return ($this->_consumer_key <> '' && $this->_consumer_secret <> '');
 }
 ///////////////////////////////////////////////////////////////////////////
 // END GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////
 ///////////////////////////////////////////////////////////////////////////
 // SETTER METHODS BELOW
 ///////////////////////////////////////////////////////////////////////////
 public function setConsumerKey($data) {
  // The setter can only be ran if the existing value is ''
  if ($data <> $this->_consumer_key && $this->_consumer_key == '') {
   $this->_consumer_key = $data;
   $this->_to->set('consumer_key', $this->_consumer_key);
   $this->_to->setChanged();
  }
 }

 public function setConsumerSecret($data) {
  // The setter can only be ran if the existing value is ''
  if ($data <> $this->_consumer_secret && $this->_consumer_secret == '') {
   $this->_consumer_secret = $data;
   $this->_to->set('consumer_secret', $this->_consumer_secret);
   $this->_to->setChanged();
  }
 }

 ///////////////////////////////////////////////////////////////////////////
 // END SETTER METHODS
 ///////////////////////////////////////////////////////////////////////////

 public function register($args) {
  $success = false;
  if (!$this->isRegistered() &&
   isset($args ['contact_name']) && !empty($args ['contact_name']) &&
   isset($args ['contact_email']) && !empty($args ['contact_email']) &&
   isset($args ['g_version']) && !empty($args ['g_version']) &&
   isset($args ['g_edition']) && !empty($args ['g_edition'])
   ) {
    $account_details = $this->_dao->register($args);
    if (is_array($account_details) &&
     isset($account_details ['k']) && !empty($account_details ['k']) &&
     isset($account_details ['s']) && !empty($account_details ['s'])
     ) {
      $this->_consumer_key = $account_details ['k'];
      $this->_to->set('consumer_key', $this->_consumer_key);
      $this->_consumer_secret = $account_details ['s'];
      $this->_to->set('consumer_secret', $this->_consumer_secret);
      $this->_to->setChanged();
      $success = true;
    }
    elseif (is_string($account_details)) {


     $success = $account_details;
    }
  }

  return $success;
 }
}
