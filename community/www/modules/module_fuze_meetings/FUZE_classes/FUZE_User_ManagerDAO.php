<?php

/**

 * The DAO of the FUZE_User_Manager class.

 * 

 * @name FUZE_User_ManagerDAO

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_User_ManagerDAO extends FUZE_AbstractDAO {
 private $_users;
 public function __construct($controller) {
  parent::__construct($controller);
  $this->_users = array();
  $this->_db = $GLOBALS['db'];
  $this->_init();
 }
 public function __destruct() {
  /* NO STATE CHANGES ARE PROPAGATED TO DB */
 }
 public function _init() {
  $res = eF_getTableData("`_mod_fm_user`", "`id`");
  if (is_array($res) && count($res)) {
   foreach ($res AS $entry) {
    $this->_users [$entry['id']] = null;
   }
  }
  $this->_to->set('users', $this->_users);
  // Retrieving active user count.
  $res = eF_getTableData("`_mod_fm_user`", "COUNT(*) AS `amount`", "`suspended` != 1");
  if (is_array($res) && count($res)) {
   $active_user_count = $res[0]['amount'];
  }
  $this->_to->set('active_user_count', $active_user_count);
 }
 public function addUser($args) {
  $function_response = array('success' => false);
  if (isset($args ['firstname']) && !empty($args ['firstname']) &&
   isset($args ['lastname']) && !empty($args ['lastname']) &&
   isset($args ['email']) && !empty($args ['email']) &&
   isset($args ['password']) && !empty($args ['password']) &&
   isset($args ['sys_id']) && !empty($args ['sys_id'])
   ) {
    // At this point we initiate the communication with app at proxy
    $options = array();
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_USER_CREATE;
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS] = array();
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS]['firstname'] = $args ['firstname'];
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS]['lastname'] = $args ['lastname'];
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS]['email'] = $args ['email'];
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS]['password'] = $args ['password'];

    $handle = RequestFactory::getRequestHandle($options);
    try {
     $response = $handle->runRequest();
    }
    catch (Exception $e) {
     $response = $e->getMessage();
    }

    if (is_array($response)) {
     // An ola kala kai to response einai tis morfis pou prepei
     $bind = array();
     $bind ['login_url'] = $response ['login_url'];
     $bind ['password'] = $args ['password'];
     $bind ['fuze_email'] = $args ['email'];
     $bind ['sys_id'] = $args ['sys_id'];
     $bind ['date_added'] = time();
     $table_name = '`_mod_fm_user`';
     $user_id = false;
     try {
      $user_id = eF_insertTableData($table_name, $bind); // Returns the last_insert_id or false on failure.
      $function_response ['success'] = true;
      $function_response ['user_id'] = $user_id;
     }
     catch (Exception $e) {
      $function_response ['error_msg'] = $e->getMessage();
     }
    }
    else {
     $msg = constant($response);
     if ($msg) $function_response ['error_msg'] = $msg;
     else $function_response ['error_msg'] = $response;
    }
  }

  return $function_response;
 }

}

?>
