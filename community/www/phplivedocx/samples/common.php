<?php

// -----------------------------------------------------------------------------

// Turn up error reporting
error_reporting (E_ALL|E_STRICT);

// Turn off WSDL caching
ini_set ('soap.wsdl_cache_enabled', 0);

// -----------------------------------------------------------------------------

// Define path to Zend Framework (ZF) and LiveDocx (LD)
//define ('PATH_ZF', 'C:/xampp/htdocs/efront/Zend/library');
define ('PATH_LD', dirname(__FILE__) . '/../library');

// Define credentials for LD
//define ('USERNAME', '');
//define ('PASSWORD', '');

// Define locale (for ZF)
define ('LOCALE', 'en_US');

// -----------------------------------------------------------------------------

// Set path to libraries
set_include_path(PATH_ZF . PATH_SEPARATOR . PATH_LD . PATH_SEPARATOR);

// -----------------------------------------------------------------------------

// Set autoloader to autoload libraries
// (saves including libraries)
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Tis_');

// -----------------------------------------------------------------------------

// If you are using a version of the Zend Framework, which is older than v1.8
// you must use the following autoloader. For details, see: http://is.gd/v5zW

//    require_once 'Zend/Loader.php';
//    Zend_Loader::registerAutoload();

// -----------------------------------------------------------------------------

/**
 * Decorator to format return value of list methods
 *
 * @param array $result
 * @return string
 */
function listDecorator($result)
{
    $ret = '';

    if (count($result) > 0) {
         foreach ($result as $record) {
             $ret .= '         Filename  : ' . $record['filename']   . "\n";
             $ret .= '         File Size : ' . $record['fileSize']   . "\n";
             $ret .= '     Creation Time : ' . $record['createTime'] . "\n";
             $ret .= 'Last Modified Time : ' . $record['modifyTime'] . "\n\n";
         }
    }

    return $ret;
}

/**
 * Decorator to format array
 *
 * @param array $result
 * @return string
 */
function arrayDecorator($result)
{
    $ret = '';
    if (count($result) > 0) {
        $ret .= implode(', ', $result);
    } else {
        $ret .= 'none';
    }

    return $ret;
}

