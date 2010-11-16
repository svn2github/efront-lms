<?php

/**

 * The communication adapter for use with cURL.

 * 

 * @name Request_Adapter_Curl

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class Request_Adapter_Curl extends Request_Adapter_Abstract {
 public function runRequest() {
  $response = false;
  if ($this->_request_type <> Request_Adapter_Abstract::REQUEST_ADAPTER_REQUEST_TYPE_REGISTER) {
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
   if ($response = $this->_registerAccount()) {
    $response = $response;
   }
  }
  return $response;
 }

 /**

	 * Executes the request, always as a 'POST' request.

	 * 

	 * @param Array $params Holds the parameters that are necessary for 

	 * the request.

	 * 

	 * @return Array The array that holds the result of the resuest. This 

	 * array always has two elements, the first indicating the result of the 

	 * request and the second one containing the returned data or eddor message

	 * depending on whether the request has been successfull or not.

	 * 

	 * @access private

	 */
 protected function _fetchReply ($options, $debug = false) {
  $response = false;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $options ['url'] );
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $options ['params'] );
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_USERAGENT, $options ['user_agent'] );
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  if ($debug) { echo 'Options: '; print_r($options); echo '<p>Response: '; var_dump($response); echo '</p>'; }
  if (!curl_errno($ch)) {
   $info = curl_getinfo($ch);
   $http_response = $info ['http_code'];
   if ($debug) { print_r($info); }
   if ($http_response > 400) {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_HTTP_OVER_400');
   }
   else {
    $response = json_decode($response);
    if (!$response) {
     $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_RESPONSE_MALFORMAT');
    }
   }
  }
  else {
   ## WE CAN CARRY OUT THE INTERNATIONALISATION FOR THE ERROR CODES WHILE
   ## TRYING TO ACCESS THE PROXY HERE
   $err_code = curl_errno($ch);
   if ($err_code == CURLE_UNSUPPORTED_PROTOCOL) {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_UNSUPPORTED_PROTOCOL');
   }
   elseif ($err_code == CURLE_FAILED_INIT) {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_FAILED_INIT');
   }
   elseif ($err_code == CURLE_URL_MALFORMAT) {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_URL_MALFORMAT');
   }
   elseif ($err_code == CURLE_URL_MALFORMAT_USER) {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_URL_MALFORMAT_USER');
   }
   elseif ($err_code == CURLE_COULDNT_RESOLVE_PROXY) {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_COULDNT_RESOLVE_PROXY');
   }
   elseif ($err_code == CURLE_COULDNT_RESOLVE_HOST) {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_COULDNT_RESOLVE_HOST');
   }
   elseif ($err_code == CURLE_COULDNT_CONNECT) {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_COULDNT_CONNECT');
   }
   elseif ($err_code == CURLE_FTP_WEIRD_SERVER_REPLY) {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_FTP_WEIRD_SERVER_REPLY');
   }
   elseif ($err_code == CURLE_REMOTE_ACCESS_DENIED) {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_REMOTE_ACCESS_DENIED');
   }
   else {
    $response = array('success' => false, 'error_msg' => '_MOD_FUZE_REQUEST_ERROR_REMOTE_UNKNOWN');
   }
  }
  curl_close($ch); # Closing the cURL handle
  return json_encode($response);
 }
}
