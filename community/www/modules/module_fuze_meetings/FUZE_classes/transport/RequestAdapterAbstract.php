<?php

/**

 * The prototype for the classes handling the communication of requests to

 * the proxy.

 *  

 * @name Request_Adapter_Abstract

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
abstract class Request_Adapter_Abstract {
 protected $_user_agent;
 protected $_timestamp;
 protected $_nonce;
 protected $_request_type; # Holds the value for the current request type
 protected $_params;
 protected $_request_token;
 protected $_crypt;
 protected $_oauth_consumer_key;
 protected $_oauth_consumer_secret;
 ## This is the single use key for encrypting data sent and received during registration
 const REQUEST_ADAPTER_REGISTRATION_KEY = 'f9o68a@134Cf9b014DF50d7a1AQ*29+e849_3#cb565q14$nDlIO0olI.LiAsD';
 const REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL = 'email';
 const REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD = 'password';
 const REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID = 'meetingid';
 const REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT = 'subject';
 const REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE = 'timezone';
 const REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE_DEFAULT = 'UTC';
 const REQUEST_ADAPTER_REQUEST_PARAMS_FIRSTNAME = 'firstname';
 const REQUEST_ADAPTER_REQUEST_PARAMS_LASTNAME = 'lastname';
 const REQUEST_ADAPTER_REQUEST_PARAMS_CONTACTNAME = 'contact_name';
 const REQUEST_ADAPTER_REQUEST_PARAMS_CONTACTEMAIL = 'contact_email';
 const REQUEST_ADAPTER_REQUEST_PARAMS_GVERSION = 'g_version';
 const REQUEST_ADAPTER_REQUEST_PARAMS_GEDITION = 'g_edition';
 const REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME = 'starttime';
 const REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME = 'endtime';
 const REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE = 'includetollfree';
 const REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE_DEFAULT = true;
 const REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL = 'includeinternationaldial';
 const REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL_DEFAULT = true;

 ## Edo orizontai ta URL's pou xrisimopoiountai gia ta requests analoga me
 ## ton typo tou request.
 const REQUEST_ADAPTER_URL_OAUTH_AUTH = 'http://fuze.efront.actonbit.gr/api/get_request_token/';
 const REQUEST_ADAPTER_URL_REGISTER = 'http://fuze.efront.actonbit.gr/api/account_register/';
 const REQUEST_ADAPTER_URL_MEETING_LAUNCH = 'http://fuze.efront.actonbit.gr/api/meeting_launch/';
 const REQUEST_ADAPTER_URL_MEETING_SCHEDULE = 'http://fuze.efront.actonbit.gr/api/meeting_schedule/';
 const REQUEST_ADAPTER_URL_MEETING_START = 'http://fuze.efront.actonbit.gr/api/meeting_start/';
 const REQUEST_ADAPTER_URL_MEETING_STATUS = 'http://fuze.efront.actonbit.gr/api/meeting_status/';
 const REQUEST_ADAPTER_URL_MEETING_UPDATE = 'http://fuze.efront.actonbit.gr/api/meeting_update/';
 const REQUEST_ADAPTER_URL_ACCOUNT_CANCEL = 'http://fuze.efront.actonbit.gr/api/account_cancel/';
 const REQUEST_ADAPTER_URL_USER_CREATE = 'http://fuze.efront.actonbit.gr/api/user_create/';
 const REQUEST_ADAPTER_URL_USER_CANCEL = 'http://fuze.efront.actonbit.gr/api/user_cancel/';
 ## TO-DO


 ## Edo orizontai oi tupoi ton diathesimon methods. Ta URL's gia to 
 ## kathena apo ta methods orizetai parapano.
 const REQUEST_ADAPTER_REQUEST_TYPE = '_request_adapter_request_type';
 const REQUEST_ADAPTER_REQUEST_TYPE_REGISTER = '_request_adapter_request_type_register';
 const REQUEST_ADAPTER_REQUEST_TYPE_MEETING_LAUNCH = '_request_adapter_request_type_meeting_launch';
 const REQUEST_ADAPTER_REQUEST_TYPE_MEETING_SCHEDULE = '_request_adapter_request_type_meeting_schedule';
 const REQUEST_ADAPTER_REQUEST_TYPE_MEETING_START = '_request_adapter_request_type_meeting_start';
 const REQUEST_ADAPTER_REQUEST_TYPE_MEETING_STATUS = '_request_adapter_request_type_meeting_status';
 const REQUEST_ADAPTER_REQUEST_TYPE_MEETING_UPDATE = '_request_adapter_request_type_meeting_update';
 const REQUEST_ADAPTER_REQUEST_TYPE_ACCOUNT_CANCEL = '_request_adapter_request_type_account_cancel';
 const REQUEST_ADAPTER_REQUEST_TYPE_USER_CREATE = '_request_adapter_request_type_user_create';
 const REQUEST_ADAPTER_REQUEST_TYPE_USER_CANCEL = '_request_adapter_request_type_user_cancel';
 ## TO-DO




 ## Edo orizontai oi statheres pou xrisimopoiountai sta options pou pername ston constructor
 ## gia na perasoume tis times gia credentials kai parameters analoga me to request type.
 const REQUEST_ADAPTER_REQUEST_UNAME = '_request_adapter_request_uname';
 const REQUEST_ADAPTER_REQUEST_PASS = '_request_adapter_request_pass';
 const REQUEST_ADAPTER_REQUEST_PARAMS = '_request_adapter_request_params';

 public function __construct($options) {
  $this->_init();
  $this->_prepareRequest($options);
 }

 /**

	 * Kanonika edo tha ginetai to initialisation ton parametron opos consumer_key, shared_secret etc

	 */
 protected function _init() {
  $this->_crypt = new FUZE_CryptXOR();
  $this->_user_agent = 'eFront FUZEBOX.moduleClient/1.0a [el] (' . $_SERVER["SERVER_ADDR"] . '; ' . $_SERVER["SERVER_NAME"] . '; ' . $_SERVER["SERVER_SOFTWARE"] . '; +http://fuze.efrontlearning.net/)';
  $this->_timestamp = time();
  $f_account = new FUZE_Account();
  if ($f_account->isRegistered()) {
   $this->_oauth_consumer_key = $f_account->getConsumerKey();
   $this->_oauth_consumer_secret = $f_account->getConsumerSecret();
   /*

			$this->_oauth_consumer_key = 'ZWNjYzRhNjU0YzRjZmM5MzNjMjM4ZGY0MzczNDBkNTYxZThmYzkzMzkxOGJiODNlM2ZmMGViZGNlNDNlNjhkMg--';

			$this->_oauth_consumer_secret = 'a3e37e21c1fd304638ce86d075ebcc1af0705631';

			*/
  }
  else {
   $this->_oauth_consumer_key = false;
   $this->_oauth_consumer_secret = false;
  }
  /*

		const OAUTH_CONSUMER_KEY = 'ZWNjYzRhNjU0YzRjZmM5MzNjMjM4ZGY0MzczNDBkNTYxZThmYzkzMzkxOGJiODNlM2ZmMGViZGNlNDNlNjhkMg--';

		const OAUTH_CONSUMER_SECRET = 'a3e37e21c1fd304638ce86d075ebcc1af0705631';

		const OAUTH_AUTH_URL = 'http://fuze.efront.actonbit.gr/api/get_request_token/';

		*/
 }
 /**

	 * Retrieves the request token that is necessary to continue with the 

	 * rest of the logic in the functionality.

	 * 

	 * @return String The value that is to be used as the request token.

	 * 

	 * @access protected

	 */
 protected function _getRequestToken() {
  $token = false;
  $this->_nonce = substr(md5(uniqid(rand(0,$this->_timestamp))),0,rand(5,10)) . '.' . md5($this->_timestamp . 'salt#1' . $this->_user_agent . 'salt#2');
  $options = array();
  $options ['url'] = Request_Adapter_Abstract::REQUEST_ADAPTER_URL_OAUTH_AUTH;
  $options ['params'] = $this->_signRequest();
  $options ['user_agent'] = $this->_user_agent;
  $response = $this->_fetchReply($options);
  //var_dump($response); die('in Request_Adapter_Abstract');
  if ($response = json_decode($response)) {
   if ($response->success) {
    $token = $response->token;
   }
   else {
    throw new Exception ($response->error_msg);
   }
  }
  return $token;
 }
 /**

	 * This method is responsible for registering a new account with the 

	 * service. This operation occurs only once upon installation of the 

	 * module and is carried out outside the OAuth & request token logic

	 * as the client has no valid credentials yet. This method is called

	 * from inside the runRequest() method.

	 */
 protected function _registerAccount() {
  $response = false;
  if ($this->_request_type == Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_REGISTER) {
   $options = array();
   $options ['url'] = Request_Adapter_Abstract::REQUEST_ADAPTER_URL_REGISTER;
   $options ['params'] = 'x_data=' . $this->_encryptData($this->_params);
   $options ['user_agent'] = $this->_user_agent;
   $response = $this->_fetchReply($options);
   if ($response = json_decode($response)) {
    if ($response->success) {
     $response = $this->_decryptData($response->x_data);
    }
    else {
     throw new Exception ($response->error_msg);
    }
   }
  }
  return $response;
 }
 protected function _encryptData($data, $key = false) {
  $data = serialize($data);
  if (!$key) {
   $data = $this->_crypt->encrypt($data, Request_Adapter_Abstract::REQUEST_ADAPTER_REGISTRATION_KEY);
  }
  else {
   $data = $this->_crypt->encrypt($data, $key);
  }
  $data = $this->_oauth_urlencode($data);
  return $data;
 }
 protected function _decryptData($data, $key = false) {
  if (!$key) {
   $data = $this->_crypt->decrypt($data, Request_Adapter_Abstract::REQUEST_ADAPTER_REGISTRATION_KEY);
  }
  else {
   $key = hash_hmac('sha1', $this->_oauth_consumer_key, hash_hmac('sha1', $this->_oauth_consumer_secret, $this->_timestamp) );
   $data = $this->_crypt->decrypt($data, $key);
  }
  $data = unserialize($data);
  return $data;
 }
 /**

	 * Runs a sanity check on the request parameters passed by app. Certain 

	 * parameters are required for the request to be successfull.

	 * 

	 * @param Array $options The array that holds the 

	 */
 protected function _prepareRequest($options) {
  if (is_array($options) && isset($options[self::REQUEST_ADAPTER_REQUEST_TYPE]) && isset($options[self::REQUEST_ADAPTER_REQUEST_PARAMS]) && is_array($options[self::REQUEST_ADAPTER_REQUEST_PARAMS]) ) {
   if ($options[self::REQUEST_ADAPTER_REQUEST_TYPE] == self::REQUEST_ADAPTER_REQUEST_TYPE_REGISTER) {
    $params = $options[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS];
    if (isset($params['contact_name']) && !empty($params['contact_name']) &&
     isset($params['contact_email']) && !empty($params['contact_email']) &&
     isset($params['g_version']) && !empty($params['g_version']) &&
     isset($params['g_edition']) && !empty($params['g_edition'])) {
      $this->_request_type = self::REQUEST_ADAPTER_REQUEST_TYPE_REGISTER;
      $this->_params = array();
      $this->_params ['contact_name'] = $params ['contact_name'];
      $this->_params ['contact_email'] = $params ['contact_email'];
      $this->_params ['g_version'] = $params ['g_version'];
      $this->_params ['g_edition'] = $params ['g_edition'];
    }
    else {
     throw new Exception("Wrong parameters found during request adapter initialisation.");
    }
   }
   elseif ($options[self::REQUEST_ADAPTER_REQUEST_TYPE] == self::REQUEST_ADAPTER_REQUEST_TYPE_USER_CREATE) {
    $this->_request_type = self::REQUEST_ADAPTER_REQUEST_TYPE_USER_CREATE;
    $params = $options[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS];
    if (isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_FIRSTNAME]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_FIRSTNAME]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_LASTNAME]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_LASTNAME]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD])
     ) {
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_FIRSTNAME] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_FIRSTNAME];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_LASTNAME] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_LASTNAME];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD];
    }
    else {
     throw new Exception("Wrong parameters found during request adapter initialisation.");
    }
   }
   elseif ($options[self::REQUEST_ADAPTER_REQUEST_TYPE] == self::REQUEST_ADAPTER_REQUEST_TYPE_USER_CANCEL) {
    $this->_request_type = self::REQUEST_ADAPTER_REQUEST_TYPE_USER_CANCEL;
    $params = $options[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS];
    if (isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) && !empty($params[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD])
     ) {
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD];
    }
    else {
     throw new Exception("Wrong parameters found during request adapter initialisation.");
    }
   }
   elseif ($options[self::REQUEST_ADAPTER_REQUEST_TYPE] == self::REQUEST_ADAPTER_REQUEST_TYPE_ACCOUNT_CANCEL) {
    $this->_request_type = self::REQUEST_ADAPTER_REQUEST_TYPE_ACCOUNT_CANCEL;
    $params = $options[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS];
    if (true) {
    }
    else {
     throw new Exception("Wrong parameters found during request adapter initialisation.");
    }
   }
   elseif ($options[self::REQUEST_ADAPTER_REQUEST_TYPE] == self::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_LAUNCH) {
    $this->_request_type = self::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_LAUNCH;
    $params = $options[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS];
    if (isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) && !empty($params[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID])
     ) {
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID];
    }
    else {
     throw new Exception("Wrong parameters found during request adapter initialisation.");
    }
   }
   elseif ($options[self::REQUEST_ADAPTER_REQUEST_TYPE] == self::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_SCHEDULE) {
    $this->_request_type = self::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_SCHEDULE;
    $params = $options[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS];
    if (isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) && !empty($params[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT])
     ) {
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE_DEFAULT;
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE_DEFAULT;
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL_DEFAULT;
    }
    else {
     throw new Exception("Wrong parameters found during request adapter initialisation.");
    }
   }
   elseif ($options[self::REQUEST_ADAPTER_REQUEST_TYPE] == self::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_START) {
    $this->_request_type = self::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_START;
    $params = $options[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS];
    if (isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) && !empty($params[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT])
     ) {
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE] = ($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE] ? $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE] : Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE_DEFAULT);
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE_DEFAULT;
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL_DEFAULT;
    }
    else {
     throw new Exception("Wrong parameters found during request adapter initialisation.");
    }
   }
   elseif ($options[self::REQUEST_ADAPTER_REQUEST_TYPE] == self::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_STATUS) {
    $this->_request_type = self::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_STATUS;
    $params = $options[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS];
    if (isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) && !empty($params[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID])
     ) {
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID];
    }
    else {
     throw new Exception("Wrong parameters found during request adapter initialisation.");
    }
   }
   elseif ($options[self::REQUEST_ADAPTER_REQUEST_TYPE] == self::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_UPDATE) {
    $this->_request_type = self::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_UPDATE;
    $params = $options[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS];
    if (isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) && !empty($params[Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT]) &&
     isset($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID]) && !empty($params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID])
     ) {
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_EMAIL];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_PASSWORD];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_MEETING_ID];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_STARTTIME];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_ENDTIME];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT] = $params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_SUBJECT];
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TIMEZONE_DEFAULT;
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_TOLLFREE_DEFAULT;
      $this->_params [Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL] = Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_PARAMS_INTERNATIONALDIAL_DEFAULT;
    }
    else {
     throw new Exception("Wrong parameters found during request adapter initialisation.");
    }
   }
  }
  else {
   ## In case the request type or the necessary parameters are not defined.
   throw new Exception("Wrong parameters found during request adapter initialisation.");
  }
 }
 /**

	 * Prepares the parameters to be sent over to the proxy for any given 

	 * request except for new registration where a different preparation 

	 * method is used. A light XOR encryption is used, to encrypt the data

	 * sent to the proxy, using a combination of timestamp and request_token 

	 * as the key. This method is called inside the runRequest() method as 

	 * the method's logic requires that a request token is present.

	 * 

	 * @param Array $params The array that holdsthe parameters that are to 

	 * be sent over to the proxy.

	 * 

	 * @return String The encrypted and prepared paramaeters to be sent to 

	 * the proxy for any given request.

	 * 

	 * @access protected

	 */
 protected function _prepareParams($params) {
  $this->_timestamp = time();
  $key = hash_hmac('sha1', $this->_oauth_consumer_key, hash_hmac('sha1', $this->_oauth_consumer_secret, $this->_timestamp) );
  $params = $this->_encryptData($params,$key);
  $concat = 'auth_stage=1';
  $concat .= '&consumer_key=' . $this->_oauth_urlencode($this->_oauth_consumer_key);
  $concat .= '&request_token=' . $this->_oauth_urlencode($this->_request_token);
  $concat .= '&timestamp=' . $this->_timestamp;
  $concat .= '&x_data=' . $params;
  return $concat;
 }
 /**

	 * Encodes the initial request according to the OAuth protocol and by 

	 * using the consumer key and consumer secret that was provided for this 

	 * client installation.

	 * 

	 * @return String The encoded request that is made in order to get a 

	 * request token and appended to the string is the signature as a product 

	 * of the OAuth signing protocol.

	 * 

	 * @access protected

	 */
 protected function _signRequest() {
  // Data to encode
  $data = array(
     'oauth_consumer_key' => $this->_oauth_consumer_key,
   'oauth_timestamp' => $this->_timestamp,
   'oauth_signature_method' => "HMAC-SHA1",
   'oauth_nonce' => $this->_nonce,
     'oauth_version' => "1.0",
   'auth_stage' => "0"
  );
  ksort($data);
  $encoded_data = array();
  foreach ($data AS $key => $value) {
   $encoded_data[$this->_oauth_urlencode($key)] = $this->_oauth_urlencode($value);
  }
  ksort($encoded_data);
  $concat = '';
  foreach ($encoded_data AS $key => $value) {
   $concat .= $key . '=' . $value . '&';
  }
  $concat = substr($concat,0,-1);
  $base_string = 'POST&' . $this->_oauth_urlencode(Request_Adapter_Abstract::REQUEST_ADAPTER_URL_OAUTH_AUTH) . '&' . $this->_oauth_urlencode($concat);
  $signature = hash_hmac('sha1', $base_string, $this->_oauth_urlencode($this->_oauth_consumer_secret).'&',true); # raw encoded
  $signature = $this->_oauth_urlencode(base64_encode($signature));
  $signed = $concat . '&oauth_signature=' . $signature;
  return $signed;
 }
 protected function _oauth_urlencode($string) {
  return $this->_rfc3986_encode($string);
 }
 protected function _rfc3986_encode($string) {
  $string = rawurlencode($string);
  return str_replace('%7E', '~', $string);
 }
 abstract public function runRequest();
 abstract protected function _fetchReply($options);
}
