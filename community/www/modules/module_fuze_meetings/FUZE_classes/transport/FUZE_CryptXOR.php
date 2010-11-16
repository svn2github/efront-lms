<?php

/**

 * Carries out a light XOR encryption/decryption.

 * 

 * @name FUZE_CryptXOR

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_CryptXOR {
 public function encrypt($plain_text, $key) {
  return base64_encode($this->_xor_process($plain_text, $key));
 }
 public function decrypt($cipher_text, $key) {
  return $this->_xor_process(base64_decode($cipher_text), $key);
 }
 protected function _xor_process($string, $key) {
  $key_length = strlen($key);
  $string_length = strlen($string);
  for ($i=0; $i<$string_length; $i++) {
   $r_pos = $i % $key_length;
   $r = ord($string[$i]) ^ ord($key[$r_pos]);
   $string[$i] = chr($r);
  }
  return $string;
 }
}
