<?php

/**

 * Defines the behaviour and state of the DAO instances used by classes 

 * in the module.

 * 

 * @name FUZE_AbstractDAO

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
abstract class FUZE_AbstractDAO {
 protected $_controller = null;
 protected $_controller_id = null;
 protected $_controller_name = null;
 protected $_to = null;
 public function __construct($controller) {
  if (is_object($controller) && is_subclass_of($controller, 'FUZE_AbstractClass')) {
   $this->_controller = $controller;
  }
  else {
   throw new Exception("Passed object is not an instance of type FUZE_AbstractClass.");
  }
  $this->_controller_id = $this->_getControllerId();
  $this->_controller_name = $this->_getControllerName();
  $this->_to = FUZE_TOFactory::getTO($this->_controller);
 }
 abstract public function _init();
 public function getTO() {
  return $this->_to;
 }
 public function getController() {
  return $this->_controller;
 }

 public function getControllerId() {
  return $this->_controller_id;
 }

 public function getControllerName() {
  return $this->_controller_name;
 }

 protected function _getControllerId() {
  $controller_id = false;
  if (in_array('getId',get_class_methods(get_class($this->_controller)))) {
   $controller_id = $this->_controller->getId();
  }

  return $controller_id;
 }

 protected function _getControllerName () {
  $controller_name = false;
  if (in_array('getControllerName',get_class_methods(get_class($this->_controller)))) {
   $controller_name = $this->_controller->getControllerName();
  }

  return $controller_name;
 }

 public function extractFromTO($field_name) {
  try {
   $value = $this->_to->get($field_name);
  }
  catch (Exception $afe) {
   throw new Exception("Error while trying to extract data from Transfer Object for key '$field_name'.");
  }

  $value = FUZE_TODefault::isEmptyString($value) ? (string)'' :
     (FUZE_TODefault::isZero($value) ? (int)'0' :
      (FUZE_TODefault::isNull($value) ? null :
       (FUZE_TODefault::isFalse($value) ? (int)'0' :
        (FUZE_TODefault::isTrue($value) ? (int)'1' :
         $value
        )
       )
      )
     );

  return $value;
 }
}
