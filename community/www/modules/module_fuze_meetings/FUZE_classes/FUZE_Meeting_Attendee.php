<?php

/**

 * Represents the logic entity that handles the behaviour and state of an 

 * attendee of a FUZE meeting.

 * 

 * @name FUZE_Meeting_Attendee

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_Meeting_Attendee extends FUZE_AbstractClass {
 private $_firstname;
 private $_lastname;
 private $_meeting_id;
 private $_sys_id;
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
   $this->_firstname = $this->_to->get('firstname');
   $this->_lastname = $this->_to->get('lastname');
   $this->_sys_id = $this->_to->get('sys_id');
   $this->_meeting_id = $this->_to->get('meeting_id');
  }
  catch (Exception $afe) {
   $this->_firstname = '';
   $this->_lastname = '';
   $this->_meeting_id = '';
   $this->_sys_id = '';
   throw new Exception("FUZE meeting attendee not found.");
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
 public function getFirstname() {
  return $this->_firstname;
 }
 public function getLastname() {
  return $this->_lastname;
 }
 public function getSysId() {
  return $this->_sys_id;
 }
 public function getMeeting() {
  return $this->_meeting_id;
 }
}
