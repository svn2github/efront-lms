<?php

/**

 * The DAO of the FUZE_Account class.

 * 

 * @name FUZE_AccountDAO

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_AccountDAO extends FUZE_AbstractDAO {
 private $_consumer_key;
 private $_consumer_secret;
 public function __construct($controller) {
  parent::__construct($controller);
  $this->_db = $GLOBALS['db'];
  $this->_init();
 }
 public function __destruct() {
  // This will be ran only once when consumer_key and secret are still set on default values of '' && '' respectively.
  if ($this->_to->isChanged() && $this->_consumer_secret == '' && $this->_consumer_key == '') {
   // Exctract values from TO
   $args = array();
   $args ['consumer_key'] = $this->extractFromTO('consumer_key');
   $args ['consumer_secret'] = $this->extractFromTO('consumer_secret');
   // Flush new values to DB
   $GLOBALS['db'] = $this->_db;
   eF_updateTableData('`_mod_fm_account`', $args, '1 = 1');
  }
 }
 public function _init() {
  $res = eF_getTableData("`_mod_fm_account`","*","");
  if (is_array($res) && count($res)) {
   $this->_consumer_key = $res [0]['consumer_key'];
   $this->_consumer_secret = $res [0]['consumer_secret'];
   $this->_to->set('consumer_key', $this->_consumer_key);
   $this->_to->set('consumer_secret', $this->_consumer_secret);
  }
 }
 public function register($args) {
  $response = false;
  if (isset($args ['contact_name']) && !empty($args ['contact_name']) &&
   isset($args ['contact_email']) && !empty($args ['contact_email']) &&
   isset($args ['g_version']) && !empty($args ['g_version']) &&
   isset($args ['g_edition']) && !empty($args ['g_edition'])
   ) {
    $options = array();
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_REGISTER;
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS] = array();
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_CONTACTNAME] = $args ['contact_name'];
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_CONTACTEMAIL] = $args ['contact_email'];
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_GVERSION] = $args ['g_version'];
    $options [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS][Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_GEDITION] = $args ['g_edition'];
    $handle = RequestFactory::getRequestHandle($options);
    try {
     $response = $handle->runRequest();
    }
    catch (Exception $e) {
     $response = $e->getMessage();
    }
  }

  return $response;
 }
}
