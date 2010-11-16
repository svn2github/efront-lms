<?php

/**

 * Determines which of the available adapters (cURL || file_get_contents)

 * should be used for all communications to software running on the proxy.

 * 

 * All instances of the transport means should be acquired by use of the 

 * static method RequestFactory::getRequestHandle($options).

 * 

 * @name RequestFactory

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class RequestFactory {
 public static function getRequestHandle($options) {
  $handle = false;
  if (is_array($options) && count($options)) {
   if (function_exists('curl_init')) {
    /* DO NOTHING IN CASE OF AN EXCEPTION, LET THE EXCEPTION BUBBLE UP TO HIGHER LEVEL */
    $handle = new Request_Adapter_Curl($options);
   }
   else {
    /* DO NOTHING IN CASE OF AN EXCEPTION, LET THE EXCEPTION BUBBLE UP TO HIGHER LEVEL */
    $handle = new Request_Adapter_File($options);
   }
  }
  else {
   throw new Exception ("Wrong initialisation parameters for request handle.");
  }
  return $handle;
 }
}
