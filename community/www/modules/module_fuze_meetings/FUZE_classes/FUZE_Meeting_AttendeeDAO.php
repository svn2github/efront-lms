<?php

/**

 * The DAO of the FUZE_Meeting_Attendee class.

 * 

 * @name FUZE_Meeting_AttendeeDAO

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_Meeting_AttendeeDAO extends FUZE_AbstractDAO {
 private $_meetings;
 public function __construct($controller) {
  parent::__construct($controller);
  $this->_meetings = array();
  $this->_db = $GLOBALS['db'];
  $this->_init();
 }
 public function __destruct() {
  /*

		if ($this->_to->isChanged()) {

			//

		}

		*/
 }
 public function _init() {
  $res = eF_getTableData("`_mod_fm_meeting_attendee`","*", "`id` = " . $this->_controller_id);
  if (is_array($res) && count($res)) {
   $this->_to->set('sys_id', $res[0]['sys_id']);
   $this->_to->set('meeting_id', $res[0]['meeting_id']);
   ## Getting firstname, surname of the attendee
   $res = eF_getTableData("`users`","`name`,`surname`","`id`={$res[0]['sys_id']} AND `archive` = 0");
   if (is_array($res) && count($res)) {
    $this->_to->set('firstname', $res[0]['name']);
    $this->_to->set('lastname', $res[0]['surname']);
   }
  }
 }
}
