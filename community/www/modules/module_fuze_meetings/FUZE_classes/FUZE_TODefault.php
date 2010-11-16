<?php

/**

 * The default Transfer Object.

 * 

 * @name FUZE_TODefault

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
class FUZE_TODefault {
 protected $_properties;
 protected $_is_changed;
 public function __construct() {
  $this->_properties = array();
  $this->_is_changed = false;
 }
 public function __desctruct() {}
 public function get($field) {
  if(in_array($field,array_keys($this->_properties))) {
   return $this->_properties[$field];
  }
  else {
   throw new Exception('Property not known: '.$field.' not found.');
  }
 }
 public function set($field, $value) {
  $success = false;
  $this->_properties[$field] = $value;
  $success = true;

  return $success;
 }

 public function dump() {
  return $this->_properties;
 }

 public function setChanged() {
  $this->_is_changed = true;
 }

 public function isChanged() {
  return $this->_is_changed;
 }

 public static function isZero($value) {
  return ($value === 0);
 }

 public static function isEmptyString($value) {
  return ($value === '');
 }

 public static function isNull($value) {
  return (is_null($value));
 }

 public static function isTrue($value) {
  return (is_bool($value) && $value);
 }

 public static function isFalse($value) {
  return (is_bool($value) && !$value);
 }
}
