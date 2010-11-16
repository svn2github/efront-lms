<?php

/**

 * Defines behaviour and state for all classes used by the module.

 * 

 * @name FUZE_AbstractClass

 * @version 1.0

 * @author A. Fotoglidis <a.fotoglidis@actonbit.gr>

 * @copyright EPIGNOSIS LTD <http://www.efrontlearning.net>

 */
abstract class FUZE_AbstractClass {
 protected $_id;
 protected $_dao;
 protected $_to;
 /**

	 * The constructor should always be implemented by sub-classes of 

	 * FUZE_Controller_Abstract.

	 */
 public function __construct($id) {
  $this->_id = $id;
 }
 abstract protected function _init();
 ///////////////////////////////////////////////////////////////////////////
 // GETTER METHODS BELOW
 ///////////////////////////////////////////////////////////////////////////
 public function getId() {
  return $this->_id;
 }
 abstract public function getControllerName();
 public function getDao() {
  return $this->_dao;
 }
 public function getTo() {
  return $this->_to;
 }

 ///////////////////////////////////////////////////////////////////////////
 // END OF GETTER METHODS
 ///////////////////////////////////////////////////////////////////////////

 ///////////////////////////////////////////////////////////////////////////
 // SETTER METHODS BELOW
 ///////////////////////////////////////////////////////////////////////////

 public function setId($id) {
  $this->_id = $id;
 }

 ///////////////////////////////////////////////////////////////////////////
 // END OF SETTER METHODS
 ///////////////////////////////////////////////////////////////////////////

}
