<?php

/**

 * The communication adapter for use with PHP file_get_contents() method.

 * 

 * @name Request_Adapter_Curl

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class Request_Adapter_File extends Request_Adapter_Abstract {
 public function runRequest() {
  $response = false;
  if ($this->_request_type <> Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_REGISTER) {
   // Initially a request token has to be acquired by app server
   $this->_request_token = $this->_getRequestToken();
   $options = array();
   if ($this->_request_token) {
    $options ['params'] = $this->_prepareParams($this->_params);
    $options ['user_agent'] = $this->_user_agent;
    if ($this->_request_type == Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_ACCOUNT_CANCEL) {
     $options ['url'] = Request_Adapter_Abstract::REQUEST_ADAPTER_URL_ACCOUNT_CANCEL;
    }
    elseif ($this->_request_type == Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_USER_CREATE) {
     $options ['url'] = Request_Adapter_Abstract::REQUEST_ADAPTER_URL_USER_CREATE;
    }
    elseif ($this->_request_type == Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_USER_CANCEL) {
     $options ['url'] = Request_Adapter_Abstract::REQUEST_ADAPTER_URL_USER_CANCEL;
    }
    elseif ($this->_request_type == Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_LAUNCH) {
     $options ['url'] = Request_Adapter_Abstract::REQUEST_ADAPTER_URL_MEETING_LAUNCH;
    }
    elseif ($this->_request_type == Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_SCHEDULE) {
     $options ['url'] = Request_Adapter_Abstract::REQUEST_ADAPTER_URL_MEETING_SCHEDULE;
    }
    elseif ($this->_request_type == Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_START) {
     $options ['url'] = Request_Adapter_Abstract::REQUEST_ADAPTER_URL_MEETING_START;
    }
    elseif ($this->_request_type == Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_STATUS) {
     $options ['url'] = Request_Adapter_Abstract::REQUEST_ADAPTER_URL_MEETING_STATUS;
    }
    elseif ($this->_request_type == Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_MEETING_UPDATE) {
     $options ['url'] = Request_Adapter_Abstract::REQUEST_ADAPTER_URL_MEETING_UPDATE;
    }
   }
   // Using the request token and the other parameters we run the final request.
   if (isset($options ['url']) && isset($this->_request_token)) {
    if ($response = $this->_fetchReply($options)) {
     if ($response = json_decode($response)) {
      if ($response->success) {
       $response = $this->_decryptData($response->x_data, true);
      }
      else {
       throw new Exception ($response->error_msg);
      }
     }
    }
   }
  }
  else {
   // This is ran only during registration when we have not yet acquired consumer key and secret.
   // Therefore the OAuth step is skipped.
   if ($response = $this->_registerAccount()) {
    $response = $response;
   }
  }
  return $response;
 }
 protected function _fetchReply($options, $debug = false) {
  $response = false;
  $resource = $options['url'] . '?' . $options ['params'];
  $response = file_get_contents($resource);
  if ($debug) { echo 'Options: '; print_r($options); echo '<p>Response: '; var_dump($response); echo '</p>'; }
  $response = json_decode($response);
  if (!$response) {
   $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_RESPONSE_MALFORMAT');
  }

  return json_encode($response);
 }
}
