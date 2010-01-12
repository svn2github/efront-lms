<?php

/** Tis_Service_LiveDocx_Exception **/
require_once 'Tis/Service/LiveDocx/Exception.php';

/** Zend_Soap_Client **/
require_once 'Zend/Soap/Client.php';


/**
 * phpLiveDocx
 *
 * The template based document creation platform
 *
 * Technical documentation and sample applications:
 * http://www.phpLiveDocx.org
 *
 * Contact the author:
 * http://www.phpLiveDocx.org/contact/
 *
 * Zend Framework (required by this class):
 * http://www.ZendFramework.com
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file phplivedocx/docs/LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://www.phpLiveDocx.org/articles/phplivedocx-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to the author so we can send you a copy immediately.
 *
 * @package    Tis_Service_LiveDocx
 * @subpackage core
 * @author     Jonathan Maron
 * @copyright  (c) 2008 - 2009 Jonathan Maron
 * @license    http://www.phpLiveDocx.org/articles/phplivedocx-license/ New BSD License
 *
 */
class Tis_Service_LiveDocx
{
    /**
     * LiveDocx service version
     */
    const VERSION = '1.1';

    /**
     * LiveDocx service object
     *
     * @var object
     */
    protected $liveDocx;

    // -------------------------------------------------------------------------

    /**
     * Implemented in children only
     *
     * @param string $username
     * @param string $password
     *
     * @return throws Tis_Service_LiveDocx_Exception
     * @return boolean
     */
    public function __construct($username, $password) { }

    /**
     * Clean up and log out of LiveDocx service
     *
     * @return boolean
     */
    public function __destruct ()
    {
        return $this->logOut();
    }

    // -------------------------------------------------------------------------

    /**
     * Init Soap client - connect to SOAP service
     *
     * @param string $endpoint
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    protected function initSoapClient ($endpoint)
    {
        try {
            $this->liveDocx = new Zend_Soap_Client($endpoint);
        } catch (Zend_Soap_Client_Exception $e) {
            self::throwException($e, 'Cannot connect to LiveDocx service at ' . $endpoint);
        }

        return null;
    }

    // -------------------------------------------------------------------------

    /**
     * Log in to LiveDocx service
     *
     * @param string $username
     * @param string $password
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    protected function logIn ($username, $password)
    {
        try {
            $this->liveDocx->LogIn(
                array(
                    'username' => $username,
                    'password' => $password
                )
            );
        } catch (Exception $e) {
            self::throwException($e, 'Cannot login into LiveDocx service - username and/or password are invalid');
        }

        return null;
    }

    /**
     * Log out of the LiveDocx service
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    protected function logOut ()
    {
        try {
            $this->liveDocx->LogOut();
        } catch (Exception $e) {
            self::throwException($e, 'Cannot log out of LiveDocx service');
        }

        return null;
    }

    // -------------------------------------------------------------------------

    /**
     * Return the document format (extension) of a filename
     *
     * @param string $filename
     *
     * @return string
     */
    public static function getFormat ($filename)
    {
        return strtolower(substr(strrchr($filename, '.'), 1));
    }

    /**
     * Return the current API version
     *
     * @return string
     */
    public static function getVersion ()
    {
        return self::VERSION;
    }

    /**
     * Compare the current API version with another version
     *
     * @param string $version (STRING NOT FLOAT)
     * @return unknown
     */
    public static function compareVersion ($version)
    {
        return version_compare($version, self::getVersion());
    }

    // -------------------------------------------------------------------------

    /**
     * Generate exception
     *
     * @param object $backendException
     * @param string $errorMessage
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    protected static function throwException ($backendException, $errorMessage)
    {
        //$error = sprintf('%s // %s', $errorMessage, $backendException->getMessage());
        //throw new Tis_Service_LiveDocx_Exception($error);

        throw new Tis_Service_LiveDocx_Exception($errorMessage);
    }

    // -------------------------------------------------------------------------

} // end of class























//    /**
//     * Create a new user on LiveDocx service (restricted access)
//     *
//     * @param string $username
//     * @param string $password
//     * @param string $email
//     *
//     * @throws Tis_Service_LiveDocx_Exception
//     * @return void
//     */
//    public function createUser ($username, $password, $email)
//    {
//        try {
//            $this->liveDocx->CreateUser(
//                array(
//                    'username' => $username,
//                    'password' => $password,
//                    'email'    => $email
//                )
//            );
//        } catch (Exception $e) {
//            self::throwException($e, 'Cannot create user');
//        }
//
//        return null;
//    }
//
//    /**
//     * Delete a user from LiveDocx service (restricted access)
//     *
//     * @param string $username
//     *
//     * @throws Tis_Service_LiveDocx_Exception
//     * @return void
//     */
//    public function deleteUser ($username)
//    {
//        try {
//            $this->liveDocx->DeleteUser(
//                array(
//                    'username' => $username
//                )
//            );
//        } catch (Exception $e) {
//            self::throwException($e, 'Cannot delete user');
//        }
//
//        return null;
//    }
//
//    /**
//     * Check whether a user exists on LiveDocx service (restricted access)
//     *
//     * @param string $username
//     *
//     * @return boolean
//     */
//    public function userExists ($username)
//    {
//        $result = $this->liveDocx->UserExists(
//            array(
//                'username' => $username
//            )
//        );
//
//        return (boolean) $result->UserExistsResult;
//    }
