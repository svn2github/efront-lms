<?php

/**

 * Represents the logic entity that handles the behaviour and state of a 

 * manager for module users.

 * 

 * @name FUZE_User_Manager

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_User_Manager extends FUZE_AbstractClass {
 private $_users;
 private $_active_user_count;
 public function __construct($id = false) {
  parent::__construct($id);
  $this->_users = array();
  $this->_init();
 }
 protected function _init() {
  try {
   $options = array();
   $this->_dao = FUZE_DAOFactory::getDAO($this, $options);
   $this->_dao->_init();
   $this->_to = $this->_dao->getTO();
   $this->_users = $this->_to->get('users');
   $this->_active_user_count = $this->_to->get('active_user_count');
  }
  catch (Exception $afe) {
   $this->_users = array();
   $this->_active_user_count = 0;
   throw new Exception("FUZE user manager not found.");
  }
 }
 /**

	 * This is the implementation of the abstract method inherited from 

	 * the parent class AF_Controller_Abstract.

	 * 

	 * @return String The processed controller name.

	 */
 public function getControllerName() {
  return 'FUZE_User_Manager';
 }
 ///////////////////////////////////////////////////////////////////////////
 // GETTER METHODS BELOW
 ///////////////////////////////////////////////////////////////////////////
 public function getUsers() {
  return $this->_users;
 }
 public function getUsersCount() {
  return count($this->_users);
 }
 public function getActiveUserCount() {
  return $this->_active_user_count;
 }
 public function getUser($id) {
  $user_item = false;
  if (in_array($id, array_keys($this->_users))) {
   if ($this->_users[$id] === null) {
    try {
     $this->_users[$id] = new FUZE_User($id);
    }
    catch (Exception $e) { var_dump($e);/* DO NOTHING */ }
   }
   $user_item = $this->_users[$id];
  }
  return $user_item;
 }
 /**

	 * Retrieves an instance of type 'FUZE_User' 

	 * @param unknown_type $sys_id

	 */
 public function getUserBySysId($sys_id) {
  $user_item = false;
  if (is_numeric($sys_id) && $sys_id > 0 && count($this->_users)) {
   foreach ($this->_users AS $user_id => $user) {
    $tmp_user_item = $this->getUser($user_id);
    if ($tmp_user_item->getSysId() == $sys_id) {
     $user_item = $this->_users [$user_id];
     break;
    }
   }
  }
  return $user_item;
 }
 ///////////////////////////////////////////////////////////////////////////
 // END GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////
 /**

	 * Adds a new user to the system.

	 * 

	 * @param Array $args The array that holds the parameters necessary for the

	 * creation of the new user.

	 * 

	 * @return FUZE_User The newly created FUZE_User object.

	 */
 public function addUser($args) {
  $response = array('success' => false);
  if (is_array($args) && count($args)) {
   $function_response = $this->_dao->addUser($args);
   if ($function_response ['success']) {
    $user_id = $function_response['user_id'];
    $this->_users [$user_id] = null;
    $user_item = $this->getUser($user_id);
    $response ['success'] = true;
    $response ['user_item'] = $user_item;
   }
   else {
    $response ['error_msg'] = $function_response ['error_msg'];
   }
  }
  return $response;
 }
 public function suspendUser($user_id) {
  $success =false;
  if ($user_id) {
   $user_item = $this->getUser($user_id);
   if ($user_item) {
    $user_item->suspend();
    $success = true;
   }
  }
  return $success;
 }
 public function suspendUserBySysId($sys_id) {
  $success = false;
  if ($sys_id) {
   $user_item = $this->getUserBySysId($sys_id);
   if ($user_item) {
    $user_item->suspend();
    $success = true;
   }
  }
  return $success;
 }
}
