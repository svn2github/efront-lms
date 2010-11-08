<?php

class JobManagerDAO extends AbstractDAO {

 private $_jobs;
 private $_db;

 public function __construct($controller) {
  parent::__construct($controller);
  $this->_db = $GLOBALS['db'];
  $this->_jobs = array();
  $this->_init();
 }

 /**

	 * The destructor takes care of finalising the DB entries for the job 

	 * manager. It automatically handles contact items that have been removed 

	 * in the business logic layer.

	 * 

	 * @access public

	 */
 public function __destruct() {
  if ($this->_to->isChanged()) {
   $tmp_jobs = $this->_to->get('jobs');
   if (count($tmp_jobs) < count($this->_jobs)) {
    $removed_ids = array_keys(array_diff_key($this->_jobs, $tmp_jobs));
    if (count($removed_ids)) {
     $removed_string = '';
     foreach ($removed_ids AS $id) {
      $removed_string .= $id . ',';
     }
     $removed_string = substr($removed_string, 0, -1); // Get rid of trailling comma
     $GLOBALS['db'] = $this->_db;
     eF_deleteTableData("`mod_jam_job`", "`id` IN ($removed_string)");
    }
   }
  }
 }
 /**

	 * Retrieving contents of the `mod_jam_job` table so as to initialise 

	 * the state of the job manager.

	 * 

	 * @access public

	 */
 public function _init() {
  $jobs = array();
  $res = eF_getTableData("`mod_jam_job`","`id`", false, "`date_added` DESC");
  if (is_array($res) && count($res)) {
   foreach ($res as $entry) {
    $this->_jobs [$entry['id']] = null;
   }
  }
  $this->_to->set('jobs', $this->_jobs);
 }
 /**

	 * Adding a new entry in the database.

	 * 

	 * @param Array $args The array that holds all data necessary for the 

	 * 

	 * @access public

	 */
 public function addJob($args) {
  $functions = $args['functions'];
  $params = array();
  $params ['`functions`'] = '';
  foreach ($functions AS $key => $function) {
   $params ['`functions`'] .= $function . '::';
  }
  $params ['`functions`'] = substr($params['`functions`'], 0, -2);
  $params ['`code`'] = $args['code'];
  $params ['`title`'] = $args['title'];
  $params ['`description`'] = $args['desc'];
  $params ['`skills`'] = $args['skills'];
  $params ['`company_desc`'] = $args['company_desc'];
  $params ['`remuneration`'] = $args['remuneration'];
  $params ['`type`'] = $args['type'];
  $params ['`experience`'] = $args['experience'];
  return eF_insertTableData('`mod_jam_job`', $params); // Returns the last_insert_id or false on failure.
 }
}
