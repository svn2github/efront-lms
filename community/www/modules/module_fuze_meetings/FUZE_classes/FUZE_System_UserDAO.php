<?php

/**

 * The DAO of the FUZE_System_User class.

 * 

 * @name FUZE_System_UserDAO

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_System_UserDAO extends FUZE_AbstractDAO {
 public function __construct($controller) {
  parent::__construct($controller);
  $this->_init();
 }
 public function __destruct() {
  /* NO STATE CHANGES ARE PROPAGATED TO DB FOR SYSTEM USERS */
 }
 /**

	 * Retrieving contents of the `mod_jam_job` table so as to initialise 

	 * the state of the job manager.

	 * 

	 * @access public

	 */
 public function _init() {
  $res = eF_getTableData("`users`","*","`id` = " . $this->_controller->getId());
  if (is_array($res) && count($res)) {
   $this->_to->set('sys_email',$res[0]['email']);
   $this->_to->set('user_type', $res[0]['user_type']);
   $this->_to->set('timezone', $res[0]['timezone']);
   $this->_to->set('firstname', $res[0]['name']);
   $this->_to->set('lastname', $res[0]['surname']);
   $this->_to->set('login', $res[0]['login']);
  }
 }
}
?>
