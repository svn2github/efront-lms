<?php

class JobDAO extends AbstractDAO {

 private $_apps;

 public function __construct($controller) {
  parent::__construct($controller);
  $this->_apps = array();
  $this->_db = $GLOBALS['db'];
  $this->_init();
 }

 public function __destruct() {
  if ($this->_to->isChanged()) {
   // Extract all value from TO
   $args = array();
   $args ['`code`'] = $this->extractFromTO('code');
   $args ['`title`'] = $this->extractFromTO('title');
   $args ['`description`'] = $this->extractFromTO('description');
   $args ['`skills`'] = $this->extractFromTO('skills');
   $args ['`remuneration`'] = $this->extractFromTO('remuneration');
   $args ['`company_desc`'] = $this->extractFromTO('company_desc');
   $args ['`functions`'] = $this->extractFromTO('functions');
   $args ['`type`'] = $this->extractFromTO('type');
   $args ['`experience`'] = $this->extractFromTO('experience');
   $args ['`active`'] = $this->extractFromTO('active');
   // Flush new values to DB
   $GLOBALS['db'] = $this->_db;
   eF_updateTableData('`mod_jam_job`', $args, '`id`='.$this->_controller_id);

   $tmp_apps = $this->_to->get('apps');
   if (count($tmp_apps) < count($this->_apps)) {
    $removed_ids = array_keys(array_diff_key($this->_apps, $tmp_apps));
    if (count($removed_ids)) {
     $removed_string = '';
     foreach ($removed_ids AS $id) {
      $removed_string .= $id . ',';
     }
     $removed_string = substr($removed_string, 0, -1); // Get rid of trailling comma
     eF_deleteTableData("`mod_jam_job_app`", "`id` IN ($removed_string)");
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
  $apps = array();
  $res = eF_getTableData("`mod_jam_job`","*", "`id`=".$this->_controller->getId());
  if (is_array($res) && count($res)) {
   $this->_to->set('code', $res[0]['code']);
   $this->_to->set('title', $res[0]['title']);
   $this->_to->set('description', $res[0]['description']);
   $this->_to->set('skills', $res[0]['skills']);
   $this->_to->set('remuneration', $res[0]['remuneration']);
   $this->_to->set('company_desc', $res[0]['company_desc']);
   $this->_to->set('functions', $res[0]['functions']);
   $this->_to->set('active', $res[0]['active']);
   $this->_to->set('date_added', $res[0]['date_added']);
   $this->_to->set('type', $res[0]['type']);
   $this->_to->set('experience', $res[0]['experience']);
   $res = eF_getTableData("`mod_jam_job_app`","`id`", "`job_id`=".$this->_controller->getId());
   if (is_array($res) && count($res)) {
    foreach ($res as $entry) {
     $this->_apps [$entry['id']] = null;
    }
   }
   $this->_to->set('apps', $this->_apps);
  }
 }
/**

	 * Adding a new entry in the database.

	 * 

	 * @param Array $args The array that holds all data necessary for the 

	 * 

	 * @access public

	 */
 public function addApp($args) {
  $params = array();
  $params ['`job_id`'] = $args['job_id'];
  $params ['`name`'] = $args['name'];
  $params ['`email`'] = $args['email'];
  $params ['`phone`'] = $args['phone'];
  $params ['`city`'] = $args['city'];
  $params ['`country`'] = $args['country'];
  $params ['`cover`'] = $args['cover'];
  $params ['`cv_filename`'] = $args['cv_filename'];
  try {
   return eF_insertTableData('`mod_jam_job_app`', $params); // Returns the last_insert_id or false on failure.
  }
  catch (Exception $e) {
   echo 'Exception sto JobDAO.php';
   var_dump($e);
  }
 }
}
