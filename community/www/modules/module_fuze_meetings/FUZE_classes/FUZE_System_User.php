<?php

/**

 * Represents the logic entity that encapsulates part of the behaviour 

 * and state of a system user.

 * 

 * @name FUZE_System_User

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_System_User extends FUZE_AbstractClass {
 private $_sys_email;
 private $_user_type;
 private $_firstname;
 private $_lastname;
 private $_login;
 private $_timezone;
 public function __construct($id) {
  parent::__construct($id);
  $this->_init();
 }
 protected function _init() {
  try {
   $options = array();
   $this->_dao = FUZE_DAOFactory::getDAO($this, $options);
   $this->_dao->_init();
   $this->_to = $this->_dao->getTO();
   $this->_sys_email = $this->_to->get('sys_email');
   $this->_user_type = $this->_to->get('user_type');
   $this->_firstname = $this->_to->get('firstname');
   $this->_lastname = $this->_to->get('lastname');
   $this->_login = $this->_to->get('login');
   $this->_timezone = $this->_to->get('timezone');
  }
  catch (Exception $afe) {
   $this->_sys_email = '';
   $this->_user_type = '';
   $this->_firstname = '';
   $this->_lastname = '';
   $this->_login = '';
   $this->_timezone = '';
   throw new Exception("System user not found.");
  }
 }
 /**

	 * This is the implementation of the abstract method inherited from 

	 * the parent class AF_Controller_Abstract.

	 * 

	 * @return String The processed controller name.

	 */
 public function getControllerName() {
  return 'FUZE_System_User';
 }
 ///////////////////////////////////////////////////////////////////////////
 // GETTER METHODS BELOW
 ///////////////////////////////////////////////////////////////////////////
 public function getFirstName() {
  return $this->_firstname;
 }
 public function getLastName() {
  return $this->_lastname;
 }
 public function getSysEmail() {
  return $this->_sys_email;
 }
 public function getTimezone() {
  return $this->_timezone;
 }
 public function getLogin() {
  return $this->_login;
 }
 ///////////////////////////////////////////////////////////////////////////
 // END GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////

 ## NO SETTER METHODS HERE. ALL SYSTEM USERS ARE MANAGED BY EFRONT
}
