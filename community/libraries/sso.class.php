<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

class SSOException extends Exception
{
	public function printErrorMessage() {
		header("Content-Type: text/plain");
		echo $this -> getCode().', '.$this -> getMessage();
	}
	
	const INVALID_KEY   = 1001;
	const EXPIRED_KEY   = 1002;
	const NOT_IMPLEMENTED = 1003;
	const CONNECTION_ERROR = 1004;
}
class SSO
{
	/**
	 * 
	 * @var unknown_type
	 */
	public $key = 0;
	/**
	 * 
	 * @var unknown_type
	 */
	public $allowedIps = array();
	/**
	 * 
	 * @var unknown_type
	 */
	public $timeout = 300;
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function __construct() {}
	
	/**
	 * 
	 * @param $descriptor
	 * @return unknown_type
	 */
	public function checkDescriptor($descriptor) {
		if (!is_numeric($descriptor)) {
			throw new SSOException("The key must be numeric", SSOException::INVALID_KEY);
		} 
		return true;
	}
	
	/**
	 * 
	 * @param $descriptor
	 * @return unknown_type
	 */
	public function createKey($descriptor) {
		$this -> key = time() + $descriptor;
		return $this -> key;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function printKey() {
		echo '0,'.$this -> key;
	}
	
	/**
	 * 
	 * @param $descriptor
	 * @param $param
	 * @return unknown_type
	 */
	public function checkKey($descriptor, $param) {
		if (time() + $param - $descriptor < $this -> timeout) {
			throw new SSOException('Key has expired', SSOException::EXPIRED_KEY);
		}
		return true;
	}
}


?>