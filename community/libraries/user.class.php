<?php
/**

 * EfrontUser Class file

 *

 * @package eFront

 */
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}
/**

 * User exceptions class

 *

 * @package eFront

 */
class EfrontUserException extends Exception
{
 const NO_ERROR = 0;
 const INVALID_LOGIN = 401;
 const USER_NOT_EXISTS = 402;
 const INVALID_PARAMETER = 403;
 const USER_EXISTS = 404;
 const DATABASE_ERROR = 405;
 const USER_FILESYSTEM_ERROR = 406;
 const INVALID_TYPE = 407;
 const ALREADY_IN = 408;
 const INVALID_PASSWORD = 409;
 const USER_NOT_HAVE_LESSON = 410;
 const WRONG_INPUT_TYPE = 411;
 const USER_PENDING = 412;
 const TYPE_NOT_EXISTS = 414;
 const MAXIMUM_REACHED = 415;
 const RESTRICTED_USER_TYPE = 416;
 const USER_INACTIVE = 417;
 const USER_NOT_LOGGED_IN = 418;
 const GENERAL_ERROR = 499;
}
/**

 * Abstract class for users

 *

 * @package eFront

 * @abstract

 */
abstract class EfrontUser
{
 /**

	 * A caching variable for user types

	 *

	 * @since 3.5.3

	 * @var array

	 * @access private

	 * @static

	 */
 private static $userRoles;
 /**

	 * The basic user types.

	 *

	 * @since 3.5.0

	 * @var array

	 * @access public

	 * @static

	 */
 public static $basicUserTypes = array('student', 'professor', 'administrator');
 /**

	 * The basic user types.

	 *

	 * @since 3.5.0

	 * @var array

	 * @access public

	 * @static

	 */
 public static $basicUserTypesTranslations = array('student' => _STUDENT, 'professor' => _PROFESSOR, 'administrator' => _ADMINISTRATOR);
 /**

	 * The user array.

	 *

	 * @since 3.5.0

	 * @var array

	 * @access public

	 */
 public $user = array();
 /**

	 * The user login.

	 *

	 * @since 3.5.0

	 * @var string

	 * @access public

	 */
 public $login = '';
 /**

	 * The user groups.

	 *

	 * @since 3.5.0

	 * @var string

	 * @access public

	 */
 public $groups = array();
 /**

	 * The user login.

	 *

	 * @since 3.5.0

	 * @var string

	 * @access public

	 */
 public $aspects = array();
 /**

	 * Whether this user authenitactes through LDAP.

	 *

	 * @since 3.5.0

	 * @var boolean

	 * @access public

	 */
 public $isLdapUser = false;
 /**

	 * The core_access sets where each user has access to

	 * @var array

	 * @since 3.5.0

	 * @access public

	 */
 public $core_access = array();
 /**

	 * Cache for modules

	 * @var array

	 * @since 3.6.1

	 * @access public

	 */
 private static $cached_modules = false;
 /**

	 * Instantiate class

	 *

	 * This function instantiates a new EfrontUser sibling object based on the given

	 * login. If $password is set, then it verifies the given password against

	 * the stored one. Either the EfrontUserFactory may be used, or directly the

	 * EfrontX class.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');			//Use factory to instantiate user object with login 'jdoe'

	 * $user = EfrontUserFactory :: factory('jdoe', 'mypass');  //Use factory to instantiate user object with login 'jdoe' and perform password verification

	 * $user = new EfrontAdministrator('jdoe')				  //Instantiate administrator user object with login 'jdoe'

	 * </code>

	 *

	 * @param string $login The user login

	 * @param string $password An enrypted password to check for the user

	 * @since 3.5.0

	 * @access public

	 */
 function __construct($user, $password = false) {
  if (!eF_checkParameter($user['login'], 'login')) {
   throw new EfrontUserException(_INVALIDLOGIN.': '.$user['login'], EfrontUserException :: INVALID_LOGIN);
  } else if ($password !== false && $password != $user['password']) {
   throw new EfrontUserException(_INVALIDPASSWORD.': '.$user, EfrontUserException :: INVALID_PASSWORD);
  }
  $this -> user = $user;
  $this -> login = $user['login'];
  $this -> user['directory'] = G_UPLOADPATH.$this -> user['login'];
  if (!is_dir($this -> user['directory'])) {
   mkdir($this -> user['directory'], 0755);
  }
  $this -> user['password'] == 'ldap' ? $this -> isLdapUser = true : $this -> isLdapUser = false;
  //Initialize core access
  $this -> coreAccess = array();
 }
 /**

	 * Get the user's upload directory

	 *

	 * This function returns the path to the user's upload directory. The path always has a trailing

	 * slash at the end.

	 * <br/>Example:

	 * <code>

	 * $path = $user -> getDirectory(); //returns something like /var/www/efront/upload/admin/

	 * </code>

	 *

	 * @return string The path to the user directory

	 * @since 3.6.0

	 * @access public

	 */
 public function getDirectory() {
  return $this -> user['directory'].'/';
 }
 /**

	 * Create new user

	 *

	 * This function is used to create a new user in the system

	 * The user is created based on a a properties array, in which

	 * the user login, name, surname and email must be present, otherwise

	 * an EfrontUserException is thrown. Apart from these, all the other

	 * user elements are optional, and defaults will be used if they are left

	 * blank.

	 * Once the database representation is created, the constructor tries to create the

	 * user directories, G_UPLOADPATH.'login/' and message attachments subfolders. Finally

	 * it assigns a default avatar to the user. The function instantiates the user based on

	 * its type.

	 * <br/>Example:

	 * <code>

	 * $properties = array('login' => 'jdoe', 'name' => 'john', 'surname' => 'doe', 'email' => 'jdoe@example.com');

	 * $user = EfrontUser :: createUser($properties);

	 * </code>

	 *

	 * @param array $userProperties The new user properties

	 * @param array $users The list of existing users, with logins and active properties, in the form array($login => $active). It is handy to specify when creating massively users

	 * @return array with new user settings if the new user was successfully created

	 * @since 3.5.0

	 * @access public

	 */
 public static function createUser($userProperties, $users = array()) {
  if (empty($users)) {
   $users = eF_getTableDataFlat("users", "login, active");
   $users = array_combine($users['login'], $users['active']);
  }
  $activatedUsers = array_sum($users); //not taking into account deactivated users in license users count
  //$versionDetails = eF_checkVersionKey($GLOBALS['configuration']['version_key']);
  if (!isset($userProperties['login']) || !eF_checkParameter($userProperties['login'], 'login')) {
   throw new EfrontUserException(_INVALIDLOGIN.': '.$userProperties['login'], EfrontUserException :: INVALID_LOGIN);
  }
  if (in_array($userProperties['login'], array_keys($users)) > 0) {
   throw new EfrontUserException(_USERALREADYEXISTS.': '.$userProperties['login'], EfrontUserException :: USER_EXISTS);
  }
  if ($userProperties['email'] && !eF_checkParameter($userProperties['email'], 'email')) {
   throw new EfrontUserException(_INVALIDEMAIL.': '.$userProperties['email'], EfrontUserException :: INVALID_PARAMETER);
  }
  if (!isset($userProperties['name'])) {
   throw new EfrontUserException(_INVALIDNAME.': '.$userProperties['name'], EfrontUserException :: INVALID_PARAMETER);
  }
  if (!isset($userProperties['surname'])) {
   throw new EfrontUserException(_INVALIDSURNAME.': '.$userProperties['login'], EfrontUserException :: INVALID_PARAMETER);
  }
  !isset($userProperties['user_type']) ? $userProperties['user_type'] = 'student' : null; //If a user type is not specified, by default make the new user student
  isset($userProperties['password']) ? $passwordNonTransformed = $userProperties['password'] : $passwordNonTransformed = $userProperties['login'];
  if ($userProperties['password'] != 'ldap') {
   !isset($userProperties['password']) ? $userProperties['password'] = EfrontUser::createPassword($userProperties['login']) : $userProperties['password'] = self :: createPassword($userProperties['password']);
  }
  //!isset($userProperties['password'])	   ? $userProperties['password']	   = md5($userProperties['login'].G_MD5KEY)		: $userProperties['password'] = md5($userProperties['password'].G_MD5KEY);		//If password is not specified, use login instead
  !isset($userProperties['email']) ? $userProperties['email'] = '' : null; // 0 means not pending, 1 means pending
  !isset($userProperties['languages_NAME']) ? $userProperties['languages_NAME'] = $GLOBALS['configuration']['default_language'] : null; //If language is not specified, use default language
  !isset($userProperties['active']) || $userProperties['active'] == "" ? $userProperties['active'] = 0 : null; // 0 means inactive, 1 means active
  !isset($userProperties['pending']) ? $userProperties['pending'] = 0 : null; // 0 means not pending, 1 means pending
  !isset($userProperties['timestamp']) || $userProperties['timestamp'] == "" ? $userProperties['timestamp'] = time() : null;
  !isset($userProperties['user_types_ID']) ? $userProperties['user_types_ID'] = 0 : null;
  if (eF_insertTableData("users", $userProperties)) {
   $user_dir = G_UPLOADPATH.$userProperties['login'].'/';
   if (is_dir($user_dir)) { //If the directory already exists, delete it first
    try {
     $directory = new EfrontDirectory($user_dir);
     $directory -> delete();
    } catch (EfrontFileException $e) {} //Don't stop on filesystem errors
   }
   if (mkdir($user_dir, 0755) || is_dir($user_dir)) { //Now, the directory either gets created, or already exists (in case errors happened above). In both cases, we continue
    //Create personal messages attachments folders
    mkdir($user_dir.'message_attachments/', 0755);
    mkdir($user_dir.'message_attachments/Incoming/', 0755);
    mkdir($user_dir.'message_attachments/Sent/', 0755);
    mkdir($user_dir.'message_attachments/Drafts/', 0755);
    mkdir($user_dir.'avatars/', 0755);
    //Create database representations for personal messages folders (it has nothing to do with filsystem database representation)
    eF_insertTableDataMultiple("f_folders", array(array('name' => 'Incoming', 'users_LOGIN' => $userProperties['login']),
    array('name' => 'Sent', 'users_LOGIN' => $userProperties['login']),
    array('name' => 'Drafts', 'users_LOGIN' => $userProperties['login'])));
    // Assign to the new user all skillgap tests that should be automatically assigned to every new student
    $newUser = EfrontUserFactory :: factory($userProperties['login']);
    $newUser -> user['password'] = $passwordNonTransformed;
    global $currentUser; // this is for running eF_loadAllModules ..needs to go somewhere else
    if (!$currentUser) {
     $currentUser = $newUser;
    }
    EfrontEvent::triggerEvent(array("type" => EfrontEvent::SYSTEM_JOIN, "users_LOGIN" => $newUser -> user['login'], "users_name" => $newUser -> user['name'], "users_surname" => $newUser -> user['surname']));
    ///MODULES1 - Module user add events
    // Get all modules (NOT only the ones that have to do with the user type)
    if (!$cached_modules) {
     $cached_modules = eF_loadAllModules();
    }
    // Trigger all necessary events. If the function has not been re-defined in the derived module class, nothing will happen
    foreach ($cached_modules as $module) {
     $module -> onNewUser($userProperties['login']);
    }
    return $newUser;
   } else {
    eF_deleteTableData("users", "login='".$userProperties['login']."'"); //Delete the created user, so that nothing happened
    throw new EfrontUserException(_COULDNOTCREATEUSERDIRECTORY.': '.$userdir, EfrontUserException :: USER_FILESYSTEM_ERROR); //The directory could not be created after all...
   }
  } else {
   throw new EfrontUserException(_COULDNOTINSERTUSER.': '.$userProperties['login'], EfrontUserException :: DATABASE_ERROR);
  }
 }
 public static function checkUserAccess($type = false) {
  if ($GLOBALS['configuration']['webserver_auth']) {
   $user = EfrontUser :: checkWebserverAuthentication();
  } else if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
   $user = EfrontUserFactory :: factory($_SESSION['s_login']);
  } else {
   throw new EfrontUserException(_RESOURCEREQUESTEDREQUIRESLOGIN, EfrontUserException::USER_NOT_LOGGED_IN);
  }
  if ($user -> user['timezone']) {
   date_default_timezone_set($user -> user['timezone']);
  }
  $user -> applyRoleOptions($user -> user['user_types_ID']); //Initialize user's role options for this lesson
  if ($type && $user -> user['user_type'] != $type) {
   throw new Exception(_YOUCANNOTACCESSTHISPAGE, EfrontUserException::INVALID_TYPE);
  }
  return $user;
 }
 public static function checkWebserverAuthentication() {
  try {
   eval('$usernameVar='.$GLOBALS['configuration']['username_variable'].';');
   if (!$usernameVar) {
    header("location:".G_SERVERNAME.$GLOBALS['configuration']['error_page']);
    exit;
   } else {
    try {
     $user = EfrontUserFactory :: factory($usernameVar);
    } catch (EfrontUserException $e) {
     if ($e -> getCode() == EfrontUserException::USER_NOT_EXISTS && $GLOBALS['configuration']['webserver_registration']) {
      try {
       include($GLOBALS['configuration']['registration_file']);
      } catch (Exception $e) {
       header("location:".G_SERVERNAME.$GLOBALS['configuration']['unauthorized_page']);
       exit;
      }
     } else {
      header("location:".G_SERVERNAME.$GLOBALS['configuration']['unauthorized_page']);
      exit;
     }
    }
   }
  } catch (Exception $e) {
   header("location:".G_SERVERNAME.$GLOBALS['configuration']['unauthorized_page']);
  }
  return $user;
 }
 /**

	 * Get system users

	 *

	 * This function is used to return a list with all the users of the system

	 * <br/>Example:

	 * <code>

	 * $users = EFrontUser :: getUsers(false);

	 * </code>

	 *

	 * @param boolean returnAdmins A flag to indicate whether to return system administrators

	 * @return array The user list

	 * @since 3.5.0

	 * @access public

	 * @static

	 */
 public static function getUsers($returnAdmins = true) {
  $users = array();
  $result = eF_getTableData("users", "LOGIN, user_type");
  foreach ($result as $value) {
   if ($value['user_type'] == 'administrator'){
    if ($returnAdmins){
     $users[$value['LOGIN']] = $value['LOGIN'];
    }
   } else{
    $users[$value['LOGIN']] = $value['LOGIN'];
   }
  }
  return $users;
 }
 /**

	 * Add user profile field

	 */
 public static function addUserField() {}
 /**

	 * Remove user profile field

	 */
 public static function removeUserField() {}
 /**

	 * Get user type

	 *

	 * This function returns the user basic type, one of 'administrator', 'professor',

	 * 'student'

	 * <br/>Example:

	 * <code>

	 *	  $user = EfrontUserFactory :: factory('admin');

	 *	  echo $user -> getType();			//Returns 'administrator'

	 * </code>

	 *

	 * @return string The user type

	 * @since 3.5.0

	 * @access public

	 */
 public function getType() {
  return $this -> user['user_type'];
 }
 /**

	 * Set user password

	 *

	 * This function is used to change the user password to something

	 * new.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> setPassword('somepass');

	 * </code>

	 *

	 * @param string $password The new password

	 * @return boolean true if everything is ok

	 * @since 3.5.0

	 * @access public

	 */
 public function setPassword($password) {
  $password_encrypted = EfrontUser::createPassword($password);
  if (eF_updateTableData("users", array("password" => $password_encrypted), "login='".$this -> user['login']."'")) {
   $this -> user['password'] = $password;
   return true;
  } else {
   return false;
  }
 }
 /**

	 * Get user password

	 *

	 * This function returns the user password (MD5 encrypted)

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * echo $user -> getPassword();			 //echos something like '36f49e43c662986b838258ab099d0d5a'

	 * </code>

	 *

	 * @return string The user password (encrypted)

	 * @since 3.5.0

	 * @access public

	 */
 public function getPassword() {
  return $this -> user['password'];
 }
 /**

	 * Set login type

	 *

	 * This function is used to set the login type for the user. Currently this

	 * can be either 'normal' (default) or 'ldap'. Setting the login type to 'ldap'

	 * erases the user password and forces authentication through ldap server

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> setLoginType('ldap');			   //Set login type to 'ldap'

	 * $user -> setLoginType('normal', 'testpass'); //Set login type to 'normal' using password 'testpass'

	 * $user -> setLoginType();					 //Set login type to 'normal' and use default password (the user's login)

	 * </code>

	 * If the user was an ldap user and is reverted back to normal, the password is either specified

	 * or created by default to match the user's login

	 *

	 * @param string $loginType The new login type, one of 'ldap' or 'normal'

	 * @param string $password The new password, only used when converting ldap to normal accounts

	 * @return boolean True if everything is ok.

	 * @since 3.5.0

	 * @access public

	 */
 public function setLoginType($loginType = 'normal', $password = '') {
  //The user login type is specified by the password. If the password is 'ldap', the the login type is also ldap. There is no chance to mistaken normal users for ldap users, since all normal users have passwords stored in md5 format, which can never be 'ldap' (or anything like it)
  if ($loginType == 'ldap' && $this -> user['password'] != 'ldap') {
   eF_updateTableData("users", array("password" => 'ldap'), "login='".$this -> user['login']."'");
   $this -> user['password'] = 'ldap';
  } elseif ($loginType == 'normal' && $this -> user['password'] == 'ldap') {
   !$password ? $password = EfrontUser::createPassword($this -> user['login']) : null; //If a password is not specified, use the user's login name
   eF_updateTableData("users", array("password" => $password), "login='".$this -> user['login']."'");
   $this -> user['password'] = $password;
  }
  return true;
 }
 /**

	 * Get the login type

	 *

	 * This function is used to check whether the user's login type

	 * is 'normal' or 'ldap'

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> getLoginType();					 //Returns either 'normal' or 'ldap'

	 * </code>

	 *

	 * @return string Either 'normal' or 'ldap'

	 * @since 3.5.0

	 * @access public

	 */
 public function getLoginType() {
  if ($this -> user['password'] == 'ldap') {
   return 'ldap';
  } else {
   return 'normal';
  }
 }
 /**

	 * Activate user

	 *

	 * This function is used to activate the user

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> activate();

	 * </code>

	 *

	 * @return boolean True if everything is ok

	 * @since 3.5.0

	 * @access public

	 */
 public function activate() {
  $this -> user['active'] = 1;
  $this -> user['pending'] = 0;
  $this -> persist();
  return true;
 }
 /**

	 * Deactivate user

	 *

	 * This function is used to deactivate the user

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> deactivate();

	 * </code>

	 *

	 * @return boolean True if everything is ok

	 * @since 3.5.0

	 * @access public

	 */
 public function deactivate() {
  $this -> user['active'] = 0;
  $this -> persist();
  return true;
 }
 /**

	 * Set avatar image

	 *

	 * This function is used to set the user's avatar image.

	 * <br/>Example:

	 * <code>

	 * $file = new EfrontFile(32);											 //This is a file uploaded -for example- in the filesystem.

	 * $user -> setAvatar($file);

	 * </code>

	 *

	 * @param EfrontFile $file The file that will be used as avatar

	 * @return boolean True if everything is ok

	 * @since 3.5.0

	 * @access public

	 */
 public function setAvatar($file) {
  if (eF_updateTableData("users", array("avatar" => $file['id']), "login = '".$this -> user['login']."'")) {
   $this -> user['avatar'] = $file['id'];
   return true;
  } else {
   return false;
  }
 }
 /**

	 * Get avatar image

	 *

	 * This function returns the file object corresponding to the user avatar

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> getAvatar();	//Returns an EfrontFile object

	 * </code>

	 *

	 * @return EfrontFile The avatar's file object

	 * @since 3.6.0

	 * @access public

	 */
 public function getAvatar() {
  if ($this -> user['avatar']) {
   $avatar = new EfrontFile($this -> user['avatar']);
  } else {
   $avatar = new EfrontFile(G_SYSTEMAVATARSURL.'unknown_small.png');
  }
  return $avatar;
 }
 /**

	 * Set user status

	 *

	 * This function is used to set the user's status.

	 * <br/>Example:

	 * <code>

	 * $user -> setStatus("Carpe Diem!");

	 * </code>

	 *

	 * @param string to be set as the new status - could be ""

	 * @return boolean True if everything is ok

	 * @since 3.6.0

	 * @access public

	 */
 public function setStatus($status) {
  if (eF_updateTableData("users", array("status" => $status), "login = '".$this -> user['login']."'")) {
   $this -> user['status'] = $status;
   EfrontEvent::triggerEvent(array("type" => EfrontEvent::STATUS_CHANGE, "users_LOGIN" => $this -> user['login'], "users_name" => $this->user['name'], "users_surname" => $this->user['surname'], "entity_name" => $status));
   //echo $status;
   if ($_SESSION['facebook_user'] && $_SESSION['facebook_details']['status']['message'] != $status) {
    $path = "../libraries/";
    require_once $path . "external/facebook-platform/php/facebook.php";
    $facebook = new Facebook($GLOBALS['configuration']['facebook_api_key'], $GLOBALS['configuration']['facebook_secret']);
    // check permissions
    $has_permission = $facebook->api_client->call_method("facebook.users.hasAppPermission", array("ext_perm"=>"status_update"));
    if($has_permission){
     $facebook->api_client->call_method("facebook.users.setStatus", array("status" => $status, "status_includes_verb" => true));
     $temp = $facebook->api_client->fql_query("SELECT status FROM user WHERE uid = " . $_SESSION['facebook_user']);
     $_SESSION['facebook_details']['status'] = $temp[0]['status'];
    }
   }
   return true;
  } else {
   return false;
  }
 }
 /**

	 * Logs out user

	 *

	 * To log out a user, the function deletes the session information and updates the database

	 * tables.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> logout();

	 * </code>

	 *

	 * @param $destroySession Whether to destroy session data as well

	 * @return boolean True if the user was logged out succesfully

	 * @since 3.5.0

	 * @access public

	 */
 public function logout($destroySession = true) {
  // Delete FB-connect related cookies - without this code the "Session key invalid problem" appears
  if (isset($GLOBALS['configuration']['facebook_api_key']) && $GLOBALS['configuration']['facebook_api_key'] && $_COOKIE[$GLOBALS['configuration']['facebook_api_key'] . "_user"]) {
   foreach ($_COOKIE as $cookie_key => $cookie) {
    if (strpos($GLOBALS['configuration']['facebook_api_key'], $cookie) !== false) {
     unset($_COOKIE[$key]);
    }
   }
   //$path = "../libraries/";
   //require_once $path . "external/facebook-platform/php/facebook.php";
   //$facebook = new Facebook($GLOBALS['configuration']['facebook_api_key'], $GLOBALS['configuration']['facebook_secret']);
   //$facebook->clear_cookie_state();
  }
  if ($this -> user['login'] == $_SESSION['s_login']) { //Is the current user beeing logged out? If so, destroy the session.
   if ($destroySession) {
    $_SESSION = array();
    isset($_COOKIE[session_name()]) ? setcookie(session_name(), '', time()-42000, '/') : null;
    session_destroy();
    setcookie ("cookie_login", "", time() - 3600);
    setcookie ("cookie_password", "", time() - 3600);
    if (isset($_COOKIE['c_request'])) {
     setcookie('c_request', '', time() - 86400);
     unset($_COOKIE['c_request']);
    }
    unset($_COOKIE['cookie_login']); //These 2 lines are necessary, so that index.php does not think they are set
    unset($_COOKIE['cookie_password']);
   }
  } else {
   $session_path = ini_get('session.save_path');
   $session_name = eF_getTableData('logs', 'comments', 'users_LOGIN="'.$this -> user['login'].'" AND action = "login"', 'timestamp desc limit 1');
   unlink($session_path.'/sess_'.$session_name[0]['comments']);
  }
  eF_deleteTableData("users_to_chatrooms", "users_LOGIN='".$this -> user['login']."'"); //Log out user from the chat
  eF_deleteTableData("chatrooms", "users_LOGIN='".$this -> user['login']."' and type='one_to_one'"); //Delete any one-to-one conversations
  eF_deleteTableData("users_online", "users_LOGIN='".$this -> user['login']."'");
  $result = eF_getTableData("logs", "action", "users_LOGIN = '".$this -> user['login']."'", "timestamp desc limit 1"); //?? ??? ????? ???????? ???, ????? ??? logs ??? ????? logout, ???? ?? ????? logout ??? ??? ??? ?? ???????
  if ($result[0]['action'] != 'logout') {
   $fields_insert = array('users_LOGIN' => $this -> user['login'],
           'timestamp' => time(),
           'action' => 'logout',
           'comments' => 0,
           'session_ip' => eF_encodeIP($_SERVER['REMOTE_ADDR']));
   eF_insertTableData("logs", $fields_insert);
  }
  eF_deleteTableData('users_online', "users_LOGIN='".$this -> user['login']."'");
 }
 /**

	 * Login user

	 *

	 * This function logs the user in the system, using the specified password

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> login('mypass');

	 * </code>

	 *

	 * @param string $password The password to login with

	 * @param boolean $encrypted Whether the password is already encrypted

	 * @return boolean True if the user logged in successfully

	 * @since 3.5.0

	 * @access public

	 */
 public function login($password, $encrypted = false) {
  unset($_SESSION['previousMainUrl']);
  unset($_SESSION['previousSideUrl']);
  unset($_SESSION['s_lesson_user_type']);
  if ($this -> user['pending']) {
   throw new EfrontUserException(_USERPENDING, EfrontUserException :: USER_PENDING);
  }
  if ($this -> user['active'] == 0) {
   throw new EfrontUserException(_USERINACTIVE, EfrontUserException :: USER_INACTIVE);
  }
  if ($this -> isLdapUser) { //Authenticate LDAP user
   if (!eF_checkUserLdap($this -> user['login'], $password)) {
    throw new EfrontUserException(_INVALIDPASSWORD, EfrontUserException :: INVALID_PASSWORD);
   }
  } else { //Authenticate normal user
   if (!$encrypted) {
    $password = EfrontUser::createPassword($password);
   }
   if ($password != $this -> user['password']) {
    throw new EfrontUserException(_INVALIDPASSWORD, EfrontUserException :: INVALID_PASSWORD);
   }
  }
  if ($this -> isLoggedIn()) { //If the user is already logged in, log him out
   if (!$this -> allowMultipleLogin()) {
    $this -> logout(false);
   }
  } else if (isset($_SESSION['s_login']) && $_SESSION['s_login']) {
   try {
    $user = EfrontUserFactory :: factory($_SESSION['s_login']);
    $user -> logout(false);
   } catch (Exception $e) {}
  }
  $_SESSION['s_lessons_ID'] = ''; //@todo: Here, we should reset all session values, except for cart contents
  //if user language is deactivated or deleted, login user with system default language
  $result = eF_getTableData("languages", "name", "name='".$this -> user['languages_NAME']."' and active=1");
  if ($result[0]['name'] == $this -> user['languages_NAME']) {
   $login_language = $this -> user['languages_NAME'];
  } else {
   $login_language = $GLOBALS['configuration']['default_language'];
  }
  //Assign session variables
  $_SESSION['s_login'] = $this -> user['login'];
  $_SESSION['s_password'] = $this -> user['password'];
  $_SESSION['s_type'] = $this -> user['user_type'];
  $_SESSION['s_language'] = $login_language;
  //Insert log entry
  $fields_insert = array('users_LOGIN' => $this -> user['login'],
           'timestamp' => time(),
           'action' => 'login',
           'comments' => session_id(),
           'session_ip' => eF_encodeIP($_SERVER['REMOTE_ADDR']));
  eF_insertTableData("logs", $fields_insert);
  $fields = array('users_LOGIN' => $this -> user['login'],
       'timestamp' => time(),
       'timestamp_now' => time(),
       'session_ip' => $_SERVER['REMOTE_ADDR']);
  if (!$this -> isLoggedIn()) {
   eF_insertTableData("users_online", $fields);
  } else {
   eF_updateTableData("users_online", $fields, "users_LOGIN='".$this -> user['login']."'");
  }
  return true;
 }
 /**

	 * Check if this user is allowed to multiple logins

	 *

	 * This function checks the current system settings and returns true

	 * if the current user is allowed to be logged in to the system more than once

	 *

	 * @return boolean true if the user is allowed to loggin more than once

	 * @since 3.5.2

	 * @access private

	 */
 private function allowMultipleLogin() {
  $multipleLogins = unserialize($GLOBALS['configuration']['multiple_logins']);
  if ($multipleLogins) {
   if (in_array($this -> user['login'], $multipleLogins['users']) ||
   in_array($this -> user['user_type'], $multipleLogins['user_types']) ||
   in_array($this -> user['user_types_ID'], $multipleLogins['user_types']) ||
   array_intersect(array_keys($this -> getGroups()), $multipleLogins['groups'])) {
    if ($multipleLogins['global']) { //If global allowance is set to "true", it means that the above clause, which matches the exceptions, translates to "multiple logins are prohibited for this user"
     return false;
    } else {
     return true;
    }
   } else {
    if ($multipleLogins['global']) {
     return true;
    } else {
     return false;
    }
   }
  } else {
   return false;
  }
 }
 /**

	 * Check if the user is already logged in and update his timestamp

	 *

	 * This function examines the system database to decide whether the user is still logged in and updates current time

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> refreshLogin();							   //Returns true if the user is logged in

	 * </code>

	 *

	 * @return boolean True if the user is logged in

	 * @since 3.5.2

	 * @access public

	 */
 public function refreshLogin() {
  $result = eF_getTableData('users_online', '*', "users_LOGIN='".$this -> user['login']."'");
  if (sizeof($result) > 0) {
   eF_updateTableData("users_online", array("timestamp_now" => time()), "users_LOGIN='".$this -> user['login']."'");
   return true;
  } else {
   return false;
  }
 }
 /**

	 * Get the list of users that are currently online

	 *

	 * This function is used to get a list of the users that are currently online

	 * In addition, it logs out any inactive users, based on global setting

	 * <br>Example:

	 * <code>

	 * $online = EfrontUser :: getUsersOnline();

	 * </code>

	 *

	 * @param boolean $userType Return only users of the basic type $user_type

	 * @param int $interval The idle interval above which a user is logged out. If it's not specified, no logging out takes place

	 * @return array The list of online users

	 * @since 3.5.0

	 * @access public

	 */
 public static function getUsersOnline($interval = false) {
  $usersOnline = array();
  $result = eF_getTableData("users_online uo, users u", "uo.*, uo.timestamp as login_time, u.*", "u.login = uo.users_LOGIN", "", "uo.users_LOGIN, uo.timestamp_now desc");
  foreach ($result as $value) {
   if (time() - $value['timestamp_now'] < $interval || !$interval) {
    $usersOnline[] = array('login' => $value['login'],
            'name' => $value['name'],
            'surname' => $value['surname'],
            'formattedLogin'=> formatLogin(false, $value),
            'user_type' => $value['user_type'],
            'timestamp_now' => $value['timestamp_now'],
            'time' => eF_convertIntervalToTime(time() - $value['login_time']));
   } else {
    EfrontUserFactory :: factory($value) -> logout();
   }
  }
  return $usersOnline;
 }
 /**

	 * Check if the user is already logged in

	 *

	 * This function examines the system logs to decide whether the user is still logged in

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> isLoggedIn();							   //Returns true if the user is logged in

	 * </code>

	 *

	 * @return boolean True if the user is logged in

	 * @since 3.5.0

	 * @access public

	 */
 public function isLoggedIn() {
  //$result = eF_getTableData('logs', 'action', "users_LOGIN='".$this -> user['login']."'", "timestamp desc limit 1");
  $result = eF_getTableData('users_online', '*', "users_LOGIN='".$this -> user['login']."'");
  if (sizeof($result) > 0) {
   return true;
  } else {
   return false;
  }
 }
 /**

	 * Delete user

	 *

	 * This function is used to delete a user from the system.

	 * The user cannot be deleted if he is the last system administrator.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> delete();

	 * </code>

	 *

	 * @return boolean True if the user was deleted successfully

	 * @since 3.5.0

	 * @access public

	 */
 public function delete() {
  $this -> logout();
  ///MODULES2 - Module user delete events - Before anything else
  // Get all modules (NOT only the ones that have to do with the user type)
  $modules = eF_loadAllModules();
  // Trigger all necessary events. If the function has not been re-defined in the derived module class, nothing will happen
  foreach ($modules as $module) {
   $module -> onDeleteUser($this -> user['login']);
  }
  try {
   $directory = new EfrontDirectory($this -> user['directory']);
   $directory -> delete();
  } catch (EfrontFileException $e) {
   $message = _USERDIRECTORYCOULDNOTBEDELETED.': '.$e -> getMessage().' ('.$e -> getCode().')'; //This does nothing at the moment
  }
  foreach ($this -> aspects as $aspect) {
   $aspect -> delete();
  }
  eF_updateTableData("f_forums", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
  eF_updateTableData("f_messages", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
  eF_updateTableData("f_topics", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
  eF_updateTableData("f_poll", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
  eF_updateTableData("chatrooms", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
  eF_updateTableData("chatmessages", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
  eF_updateTableData("calendar", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
  eF_updateTableData("news", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
  eF_updateTableData("files", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("f_folders", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("f_personal_messages", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("bookmarks", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("comments", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("f_users_to_polls", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("logs", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("rules", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("users_online", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("users_to_surveys", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("users_to_done_surveys", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("survey_questions_done", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("lessons_timeline_topics_data", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("events", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("profile_comments", "users_LOGIN='".$this -> user['login']."'");
  //This line was in EfrontProfessor and EfrontStudent without an obvious reason. Admins may also be members of groups
  eF_deleteTableData("users_to_groups", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("users", "login='".$this -> user['login']."'");
  eF_deleteTableData("notifications", "recipient='".$this -> user['login']."'");
  EfrontEvent::triggerEvent(array("type" => EfrontEvent::SYSTEM_REMOVAL, "users_LOGIN" => $this -> user['login'], "users_name" => $this -> user['name'], "users_surname" => $this -> user['surname']));
  return true;
 }
 /**

	 * Set user type

	 *

	 * This function is used to change the basic user type

	 * @param string The new user type

	 * @since 3.5.0

	 * @access public

	 */
 public function changeType($userType) {
  if (!in_array($userType, EfrontUser :: $basicUserTypes)) {
   throw new EfrontUserException(_INVALIDUSERTYPE.': '.$userType, EfrontUser :: INVALID_TYPE);
  }
  switch ($userType) {
   case 'student':
    eF_updateTableData("users", array("user_type" => "student"), "login='".$this -> user['login']."'");
    break;
   case 'professor':
    eF_updateTableData("users", array("user_type" => "professor"), "login='".$this -> user['login']."'");
    break;
   case 'administrator':
    eF_updateTableData("users", array("user_type" => "administrator"), "login='".$this -> user['login']."'");
    break;
   default: break;
  }
 }
 /**

	 * Persist user values

	 *

	 * This function is used to store user's changed values to the database.

	 * <br/>Example:

	 * <code>

	 * $user -> surname = 'doe';							//Change object's surname

	 * $user -> persist();								  //Persist changed value

	 * </code>

	 *

	 * @return boolean True if everything is ok

	 * @since 3.5.0

	 * @access public

	 */
 public function persist() {
  $fields = array('password' => $this -> user['password'],
      'email' => $this -> user['email'],
      'languages_NAME' => $this -> user['languages_NAME'],
      'name' => $this -> user['name'],
      'surname' => $this -> user['surname'],
      'active' => $this -> user['active'],
      'comments' => $this -> user['comments'],
      'user_type' => $this -> user['user_type'],
      'timestamp' => $this -> user['timestamp'],
      'avatar' => $this -> user['avatar'],
      'pending' => $this -> user['pending'],
      'user_types_ID' => $this -> user['user_types_ID'],
      'viewed_license' => $this -> user['viewed_license'],
      'status' => $this -> user['status'],
      'balance' => $this -> user['balance'],
      'archive' => $this -> user['archive'],
      'additional_accounts' => $this -> user['additional_accounts'],
      'short_description' => $this -> user['short_description'],
      'autologin' => $this -> user['autologin']);
  eF_updateTableData("users", $fields, "login='".$this -> user['login']."'");
  return true;
 }
 /**

	 * Get the user groups list

	 *

	 * <br/>Example:

	 * <code>

	 * $groupsList	= $user -> getGroups();						 //Returns an array with pairs [groups id] => [employee specification for this group]

	 * </code>

	 *

	 * @return array An array of [group id] => [group ID] pairs, or an array of group objects

	 * @since 3.5.0

	 * @access public

	 */
 public function getGroups() {
  if (! $this -> groups ) {
   $result = eF_getTableData("users_to_groups", "groups_ID", "users_LOGIN = '".$this -> login."'");
   foreach ($result as $group) {
    $id = $group['groups_ID'];
    $this -> groups[$id] = $group;
   }
  }
  return $this -> groups;
 }
 /**

	 * Assign a group to user.

	 *

	 * This function can be used to assign a group to a user

	 * <br/>Example:

	 * <code>

	 * $user = EfrontHcdUserFactory :: factory('jdoe');

	 * $user -> addGroups(23);						 //Add a single group with id 23

	 * $user -> addGroups(array(23,24,25));			//Add multiple groups using an array

	 * </code>

	 *

	 * @return int The array of lesson ids.

	 * @since 3.5.0

	 * @access public

	 * @todo auto_projects

	 */
 public function addGroups($groupIds) {
  if (!$this -> groups) {
   $this -> getGroups();
  }
  if (!is_array($groupIds)) {
   $groupIds = array($groupIds);
  }
  // Info needed for log keeping
  foreach ($groupIds as $key => $groupId) {
   if (eF_checkParameter($groupId, 'id')) {
    // Check if the group is already assigned, if so, complement the existing specification
    if (!isset($this -> groups[$groupId])) {
     // Register group assignment into the event log - event log only available in HCD
     try {
      $group = new EfrontGroup($groupId);
      $group->addUsers($this -> user['login']);
      $this -> groups[$groupId] = $groupId;
     } catch (Exception $e) {
      throw new EfrontUserException(_OPERATIONWASNOTCOMPLETEDSUCCESFULLY.': '.$ok . $e->getTraceAsString(), EfrontUserException :: DATABASE_ERROR);
     }
    }
   }
  }
  return $this -> groups;
 }
 /**

	 * Remove groups from employee.

	 *

	 * This function can be used to remove a group from the current employee.

	 * <br/>Example:

	 * <code>

	 * $employee = EfrontHcdUserFactory :: factory('jdoe');

	 * $employee -> removeGroups(23);						  //Remove a signle group with id 23

	 * $employee -> removeGroups(array(23,24,25));			 //Remove multiple groups using an array

	 * </code>

	 *

	 * @param int $groupIds Either a single group id, or an array if ids

	 * @return int The array of group ids.

	 * @since 3.5.0

	 * @access public

	 */
 public function removeGroups($groupIds) {
  if (!$this -> groups) {
   $this -> getGroups();
  }
  if (!is_array($groupIds)) {
   $groupIds = array($groupIds);
  }
  foreach ($groupIds as $key => $groupId) {
   if (!eF_checkParameter($groupId, 'id')) {
    unset($groupIds[$key]); //Remove illegal vaues from groups array.
   }
  }
  eF_deleteTableData("users_to_groups", "users_login = '".$this -> login."' and groups_ID in (".implode(",", $groupIds).")"); //delete groups from list
  // Register group assignment into the event log - event log only available in HCD
  return $this -> groups;
 }
 ///MODULE3
 /**

	 * Get modules for this user (according to the user type).

	 *

	 * This function can is used to get the modules for the user

	 * <br/>Example:

	 * <code>

	 * $currentUser = EfrontUserFactory :: factory('jdoe');

	 * $modules = $currentUser -> getModules();

	 * </code>

	 *

	 * @param no parameter

	 * @return int The array of modules for the user type of this user.

	 * @since 3.5.0

	 * @access public

	 */
 public function getModules() {
  $modulesDB = eF_getTableData("modules","*","active = 1");
  $modules = array();
  $user_type = $this -> getType();
  // Get all modules enabled for this user type
  foreach ($modulesDB as $module) {
   $folder = $module['position'];
   $className = $module['className'];
   // If a module is to be updated then its class should not be loaded now
   if (!($this -> getType() == "administrator" && isset($_GET['ctg']) && $_GET['ctg'] == "control_panel" && isset($_GET['op']) && $_GET['op'] == "modules" && $_GET['upgrade'] == $className)) {
    if(is_dir(G_MODULESPATH.$folder) && is_file(G_MODULESPATH.$folder."/".$className.".class.php")) {
     require_once G_MODULESPATH.$folder."/".$className.".class.php";
     if (class_exists($className)) {
      $modules[$className] = new $className($user_type.".php?ctg=module&op=".$className, $folder);
      // Got to check if this is a lesson module so as to change the moduleBasePath
      if ($modules[$className] -> isLessonModule() && isset($GLOBALS['currentLesson'])) {
       $modules[$className] -> moduleBaseUrl = $this -> getRole($GLOBALS['currentLesson']) .".php?ctg=module&op=".$className;
      }
      if (!in_array($user_type, $modules[$className] -> getPermittedRoles())) {
       unset($modules[$className]);
      }
     } else {
      $message = '"'.$className .'" '. _MODULECLASSNOTEXISTSIN . ' ' .G_MODULESPATH.$folder.'/'.$className.'.class.php';
      $message_type = 'failure';
     }
    } else {
     eF_deleteTableData("modules","className = '".$className."'");
     $message = _ERRORLOADINGMODULE . " " . $className . " " . _MODULEDELETED;
     $message_type = "failure";
    }
   }
  }
  return $modules;
 }
 /**

	 * Get the login time for on e or all users in the specified time interval

	 *

	 * This function returns the login time for the specified user in the specified interval

	 * <br/>Example:

	 * <code>

	 *	  $interval['from'] = "00000000";

	 *	  $interval['to']   = time();

	 *	  $time  = EfrontUser :: getLoginTime('jdoe', $interval); //$time['jdoe'] now holds his times

	 *	  $times = EfrontUser :: getLoginTime($interval); //$times now holds an array of times for all users

	 * </code>

	 *

	 * @param mixed $login The user to calulate times for, or false for all users

	 * @param mixed An array of the form (from =>'', to=>'') or false (return the total login time)

	 * @return the total login time as an array of hours, minutes, seconds

	 * @since 3.5.0

	 * @access public

	 */
 public static function getLoginTime($login = false, $interval = false){
  if ($interval && eF_checkParameter($interval['from'], 'timestamp') && eF_checkParameter($interval['to'], 'timestamp')) {
   $from = $interval['from'];
   $to = $interval['to'];
  } else {
   $from = "00000000";
   $to = time();
  }
  if ($login && eF_checkParameter($login, 'login')) {
   $result = eF_getTableData("logs", "users_LOGIN, id, timestamp, action", "users_LOGIN = '$login' and timestamp between $from and $to", "id");
  } else {
   $result = eF_getTableData("logs", "users_LOGIN, id, timestamp, action", "timestamp between $from and $to", "id");
  }
  $userTimes = array();
  foreach ($result as $value) {
   $logs[$value['users_LOGIN']][] = $value;
  }
  foreach ($logs as $user => $result) {
   $totalTime = 0;
   $start = 0;
   $inlogin = 0;
   foreach ($result as $value) {
    if ($inlogin) {
     if ($value['action'] != 'logout' && $value['action'] != 'login'){
      if ($value['timestamp'] < ($start + 1800)) { //if it is inactive more than half an hour, we don't consider it
       $totalTime += $value['timestamp'] - $start;
       $start = $value['timestamp'];
      } else {
       //$totalTime += 900;   // we could consider half of this period or enitre in the future
       $start = $value['timestamp']; // It is needed to refresh start time even if time period was more half an hour. It was missing
      }
     } else if ($value['action'] == 'logout') {
      if ($value['timestamp'] < ($start + 1800)) { //if it is inactive more than half an hour, we don't consider it
       $totalTime += $value['timestamp'] - $start;
      } else {
       //$totalTime += 900; // we could consider half of this period or enitre in the future
      }
      $inlogin = 0;
     } else if ($value['action'] == 'login') {
      $inlogin = 1;
      $start = $value['timestamp'];
     }
    } else {
     if ($value['action'] == 'login') {
      $inlogin = 1;
      $start = $value['timestamp'];
     }
    }
   }
   $userTimes[$user] = eF_convertIntervalToTime($totalTime);
   $userTimes[$user]['total_seconds'] = $totalTime;
  }
  if ($login) {
   return $userTimes[$login];
  } else {
   return $userTimes;
  }
 }
 /**

	 * Archive user

	 *

	 * This function is used to archive the user object, by setting its active status to 0 and its

	 * archive status to 1

	 * <br/>Example:

	 * <code>

	 * $user -> archive();	//Archives the user object

	 * $user -> unarchive();	//Archives the user object and activates it as well

	 * </code>

	 *

	 * @since 3.6.0

	 * @access public

	 */
 public function archive() {
  $this -> user['archive'] = time();
  $this -> persist();
  $this -> deactivate();
 }
 /**

	 * Unarchive user

	 *

	 * This function is used to unarchive the user object, by setting its active status to 1 and its

	 * archive status to 0

	 * <br/>Example:

	 * <code>

	 * $user -> archive();	//Archives the user object

	 * $user -> unarchive();	//Archives the user object and activates it as well

	 * </code>

	 *

	 * @since 3.6.0

	 * @access public

	 */
 public function unarchive() {
  $this -> activate();
  $this -> user['archive'] = 0;
  $this -> persist();
 }
 /**

	 * Apply role options to object

	 *

	 * This function is used to apply role options, using the specified role

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> applyRoleOptions(4);						//Apply the role options for user type with id 4 to the $user object

	 * </code>

	 *

	 * @param int $role The role id to apply options for

	 * @since 3.5.0

	 * @access public

	 */
 public function applyRoleOptions($role = false) {
  if (!$role) {
   $role = $this -> user['user_types_ID'];
  }
  if ($role) {
   $result = eF_getTableData("user_types", "*", "id='".$role."'");
   unserialize($result[0]['core_access']) ? $this -> coreAccess = unserialize($result[0]['core_access']) : null;
   unserialize($result[0]['modules_access']) ? $this -> modulesAccess = unserialize($result[0]['modules_access']) : null;
  }
 }
 /**

	 * Get system roles

	 *

	 * This function is used to get all the roles in the system

	 * It returns an array where keys are the role ids and values are:

	 * - Either the role basic user types, if $getNames is false (the default)

	 * - or the role Names if $getNames is true

	 * The array is prepended with the 3 main roles, 'administrator', 'professor' and 'student'

	 * <br/>Example:

	 * <code>

	 * $roles = EfrontUser :: getRoles();

	 * </code>

	 *

	 * @param boolean $getNames Whether to return id/basic user type pairs or id/name pairs

	 * @return array The system roles

	 * @since 3.5.0

	 * @access public

	 * @static

	 */
 public static function getRoles($getNames = false) {
  //Cache results in self :: $userRoles
  if (is_null(self :: $userRoles)) {
   $roles = eF_getTableDataFlat("user_types", "*", "active=1"); //Get available roles
   self :: $userRoles = $roles;
  } else {
   $roles = self :: $userRoles;
  }
  if (sizeof($roles) > 0) {
   $getNames ? $roles = self :: $basicUserTypesTranslations + array_combine($roles['id'], $roles['name']) : $roles = array_combine(self :: $basicUserTypes, self :: $basicUserTypes) + array_combine($roles['id'], $roles['basic_user_type']);
  } else {
   $getNames ? $roles = self :: $basicUserTypesTranslations : $roles = array_combine(self :: $basicUserTypes, self :: $basicUserTypes);
  }
  return $roles;
 }
 /**

	 * Get the user profile's comments list

	 *

	 * <br/>Example:

	 * <code>

	 * $commentsList	= $user -> getProfileComments();						 //Returns an array with pairs [groups id] => [employee specification for this group]

	 * </code>

	 *

	 * @return array A sorted according to timestamp array of [comment id] => [timestamp, authors_LOGIN, authors_name, authors_surname, data] pairs, or an array of comments

	 * @since 3.6.0

	 * @access public

	 */
 public function getProfileComments() {
  if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_COMMENTS) {
   $result = eF_getTableData("profile_comments JOIN users ON authors_LOGIN = users.login", "profile_comments.id, profile_comments.timestamp, authors_LOGIN, users.name, users.surname, users.avatar, data", "users_LOGIN = '".$this -> user['login']."'", "timestamp DESC");
   $comments = array();
   foreach ($result as $comment) {
    $comments[$comment['id']] = $comment;
   }
   return $comments;
  } else {
   return array();
  }
 }
 /**

	 *

	 * @param $pwd

	 * @return unknown_type

	 */
 public static function createPassword($pwd, $mode = 'efront') {
  if ($mode == 'efront') {
   $encrypted = md5($pwd.G_MD5KEY);
  } else {
   $encrypted = $pwd;
  }
  return $encrypted;
 }
 /**

	 * Convert the user argument to a user login

	 *

	 * @param mixed $login The argument to convert

	 * @return string The user's login

	 * @since 3.6.3

	 * @access public

	 * @static

	 */
 public static function convertArgumentToUserLogin($login) {
  if ($login instanceof EfrontLessonUser) {
   $login = $login -> user['login'];
  } else if (!eF_checkParameter($login, 'login')) {
   throw new EfrontUserException(_INVALIDLOGIN, EfrontUserException::INVALID_LOGIN);
  }
  return $login;
 }
 public static function convertUserObjectsToArrays($userObjects) {
  foreach ($userObjects as $key => $value) {
   if ($value instanceOf EfrontUser) {
    $userObjects[$key] = $value -> user;
   }
  }
  return $userObjects;
 }
 public static function convertUserConstraintsToSqlParameters($constraints) {
  $where = EfrontUser::addWhereConditionToUserConstraints($constraints);
  $limit = EfrontUser::addLimitConditionToConstraints($constraints);
  $order = EfrontUser::addSortOrderConditionToConstraints($constraints);
  return array($where, $limit, $order);
 }
 public static function addWhereConditionToUserConstraints($constraints) {
  $where = array();
  if (isset($constraints['archive'])) {
   $constraints['archive'] ? $where[] = 'u.archive!=0' : $where[] = 'u.archive=0';
  }
  if (isset($constraints['active'])) {
   $constraints['active'] ? $where[] = 'u.active=1' : $where[] = 'u.active=0';
  }
  if (isset($constraints['filter']) && $constraints['filter']) {
   $result = eF_describeTable("users");
   $tableFields = array();
   foreach ($result as $value) {
    $tableFields[] = "u.".$value['Field'].' like "%'.$constraints['filter'].'%"';
   }
   $where[] = "(".implode(" OR ", $tableFields).")";
  }
  if (isset($constraints['condition'])) {
   $where[] = $constraints['condition'];
  }
  if (isset($constraints['table_filters'])) {
   foreach ($constraints['table_filters'] as $constraint) {
    $where[] = $constraint['condition'];
   }
  }
  return $where;
 }
 private static function addLimitConditionToConstraints($constraints) {
  $limit = '';
  if (isset($constraints['limit']) && eF_checkParameter($constraints['limit'], 'int') && $constraints['limit'] > 0) {
   $limit = $constraints['limit'];
  }
  if ($limit && isset($constraints['offset']) && eF_checkParameter($constraints['offset'], 'int') && $constraints['offset'] >= 0) {
   $limit = $constraints['offset'].','.$limit;
  }
  return $limit;
 }
 private static function addSortOrderConditionToConstraints($constraints) {
  $order = '';
  if (isset($constraints['sort']) && eF_checkParameter($constraints['sort'], 'alnum_with_spaces')) {
   $order = $constraints['sort'];
   if (isset($constraints['order']) && in_array($constraints['order'], array('asc', 'desc'))) {
    $order .= ' '.$constraints['order'];
   }
  }
  return $order;
 }
 public static function convertDatabaseResultToUserObjects($result) {
  $userObjects = array();
  foreach ($result as $value) {
   $userObjects[$value['login']] = EfrontUserFactory::factory($value);
  }
  return $userObjects;
 }
 public static function convertDatabaseResultToUserArray($result) {
  $userArray = array();
  foreach ($result as $value) {
   $userArray[$value['login']] = $value;
  }
  return $userArray;
 }
}
/**

 * Class for administrator users

 *

 * @package eFront

 */
class EfrontAdministrator extends EfrontUser
{
 /**

	 * Get user information

	 *

	 * This function returns the user information in an array

	 *

	 *

	 * <br/>Example:

	 * <code>

	 * $info = $user -> getInformation();		 //Get lesson information

	 * </code>

	 *

	 * @param string $user The user login to customize lesson information for

	 * @return array The user information

	 * @since 3.5.0

	 * @access public

	 */
 public function getInformation() {
  $languages = EfrontSystem :: getLanguages(true);
  $info = array();
  $info['login'] = $this -> user['login'];
  $info['name'] = $this -> user['name'];
  $info['surname'] = $this -> user['surname'];
  $info['fullname'] = $this -> user['name'] . " " . $this -> user['surname'];
  $info['user_type'] = $this -> user['user_type'];
  $info['user_types_ID'] = $this -> user['user_types_ID'];
  $info['student_lessons'] = array();
  $info['professor_lessons'] = array();
  $info['total_lessons'] = 0;
  $info['total_login_time'] = self :: getLoginTime($this -> user['login']);
  $info['language'] = $languages[$this -> user['languages_NAME']];
  $info['active'] = $this -> user['active'];
  $info['active_str'] = $this -> user['active'] ? _YES : _NO;
  $info['joined'] = $this -> user['timestamp'];
  $info['joined_str'] = formatTimestamp($this -> user['timestamp'], 'time');
  $info['avatar'] = $this -> user['avatar'];
  return $info;
 }
 public function getRole() {
  return "administrator";
 }
 /*

	 * Social eFront function

	 *

	 * For administrators it should return all users

	 */
 public function getRelatedUsers() {
  $all_users = EfrontUser::getUsers(true);
  foreach($all_users as $key=>$login) {
   if ($login == $this -> user['login']) {
    unset($all_users[$key]);
    break;
   }
  }
  return $all_users;
 }
 /**

	 *

	 * @return unknown_type

	 */
 public function getLessons() {
  return array();
 }
 public function getIssuedCertificates() {
  return array();
 }
}
/**

 * Class for users that may have lessons

 *

 * @package eFront

 * @abstract

 */
abstract class EfrontLessonUser extends EfrontUser
{
 /**

	 * A caching variable for user types

	 *

	 * @since 3.5.3

	 * @var array

	 * @access private

	 * @static

	 */
 private static $lessonRoles;
 /**

	 * The user lessons array.

	 *

	 * @since 3.5.0

	 * @var array

	 * @access public

	 */
 public $lessons = false;
 /**

	 * Assign lessons to user.

	 *

	 * This function can be used to assign a lesson to the current user. If $userTypes

	 * is specified, then the user is assigned to the lesson using the specified type.

	 * By default, the user basic type is used.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> addLessons(23);						 //Add a signle lesson with id 23

	 * $user -> addLessons(23, 'professor');			//Add a signle lesson with id 23 and set the user type to 'professor'

	 * $user -> addLessons(array(23,24,25));			//Add multiple lessons using an array

	 * $user -> addLessons(array(23,24,25), array('professor', 'student', 'professor'));			//Add multiple lessons using an array for lesson ids and another for corresponding user types

	 * </code>

	 *

	 * @param mixed $lessonIds Either a single lesson id, or an array if ids

	 * @param mixed $userTypes The corresponding user types for the specified lessons

	 * @param boolean $activate Lessons will be set as active or not

	 * @return mixed The array of lesson ids or false if the lesson already exists.

	 * @since 3.5.0

	 * @access public

	 */
 public function addLessons($lessonIds, $userTypes, $activate = 1) {
  if (sizeof($this -> lessons) == 0) {
   $this -> getLessons();
  }
  if (!is_array($lessonIds)) {
   $lessonIds = array($lessonIds);
  }
  if (!is_array($userTypes)) {
   $userTypes = array($userTypes);
  }
  $lessons = eF_getTableData("lessons", "*", "id in (".implode(",", $lessonIds).")");
  foreach ($lessons as $key => $lesson) {
   $lesson = new EfrontLesson($lesson);
   $lesson -> addUsers($this -> user['login'], $userTypes[$key], $activate);
  }
  $this -> lessons = false; //Reset lessons information
  return $this -> getLessons();
 }
 /**

	 * Confirm user's lessons

	 *

	 * This function can be used to set the "active" flag of a user's lesson to "true", so that

	 * he can access the corresponding lessons.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> confirmLessons(23);						 //Confirms the lesson with id 23

	 * $user -> addLessons(array(23,24,25));			//Confirms multiple lessons using an array

	 * </code>

	 *

	 * @param mixed $lessonIds Either a single lesson id, or an array if ids

	 * @return array The array of lesson ids

	 * @since 3.6.0

	 * @access public

	 */
 public function confirmLessons($lessonIds) {
  if (sizeof($this -> lessons) == 0) {
   $this -> getLessons();
  }
  if (!is_array($lessonIds)) {
   $lessonIds = array($lessonIds);
  }
  $lessons = eF_getTableData("lessons", "*", "id in (".implode(",", $lessonIds).")");
  foreach ($lessons as $key => $lesson) {
   $lesson = new EfrontLesson($lesson);
   $lesson -> confirm($this -> user['login']);
  }
  $this -> lessons = false; //Reset lessons information
  return $this -> getLessons();
 }
 /**

	 * Remove lessons from user.

	 *

	 * This function can be used to remove a lesson from the current user.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> removeLessons(23);						  //Remove a signle lesson with id 23

	 * $user -> removeLessons(array(23,24,25));			 //Remove multiple lessons using an array

	 * </code>

	 *

	 * @param int $lessonIds Either a single lesson id, or an array if ids

	 * @return int The array of lesson ids.

	 * @since 3.5.0

	 * @access public

	 */
 public function removeLessons($lessonIds) {
  if (!is_array($lessonIds)) {
   $lessonIds = array($lessonIds);
  }
  foreach ($lessonIds as $key => $lessonID) {
   if (!eF_checkParameter($lessonID, 'id')) {
    unset($lessonIds[$key]); //Remove illegal vaues from lessons array.
   }
  }
  eF_deleteTableData("users_to_lessons", "users_LOGIN = '".$this -> user['login']."' and lessons_ID in (".implode(",", $lessonIds).")"); //delete lessons from list
  foreach ($lessonIds as $lessonId) {
   $cacheKey = "user_lesson_status:lesson:".$lessonId."user:".$this -> user['login'];
   Cache::resetCache($cacheKey);
  }
  //Timelines event
  EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_REMOVAL, "users_LOGIN" => $this -> user['login'], "lessons_ID" => $lessonIds));
  $userLessons = eF_getTableDataFlat("users_to_lessons", "lessons_ID, user_type", "users_LOGIN = '".$this -> user['login']."'");
  $this -> lessons = array_combine($userLessons['lessons_ID'], $userLessons['user_type']);
  return $this -> lessons;
 }
 /**

	 * Reset the user's progress in the specified lesson

	 * 

	 * @param mixed $lesson The lesson to reset

	 * @since 3.6.3

	 * @access public

	 */
 public function resetProgressInLesson($lesson) {
  if (!($lesson instanceOf EfrontLesson)) {
   $lesson = new EfrontLesson($lesson);
  }
  $tracking_info = array("done_content" => "",
          "issued_certificate" => "",
          "from_timestamp" => time(),
          "to_timestamp" => null,
          "comments" => "",
          "completed" => 0,
          "current_unit" => 0,
          "score" => 0);
  eF_updateTableData("users_to_lessons", $tracking_info, "lessons_ID = ".$lesson -> lesson['id']);
  eF_deleteTableData("completed_tests", "users_LOGIN = '".$this -> user['login']."' and tests_ID in (select id from tests where lessons_ID='".$lesson -> lesson['id']."')");
  eF_deleteTableData("scorm_data", "users_LOGIN = '".$this -> user['login']."' and content_ID in (select id from content where lessons_ID='".$lesson -> lesson['id']."')");
 }
 /**

	 * Reset the user's progress in the specified course

	 * 

	 * @param mixed $course The course to reset

	 * @param boolean $resetLessons whether to reset lesson progress as well

	 * @since 3.6.3

	 * @access public

	 */
 public function resetProgressInCourse($course, $resetLessons = false) {
  if (!($course instanceOf EfrontCourse)) {
   $course = new EfrontLesson($course);
  }
  $tracking_info = array("issued_certificate" => "",
          "comments" => "",
          "from_timestamp" => time(),
          "to_timestamp" => 0,
          "completed" => 0,
          "score" => 0);
  eF_updateTableData("users_to_courses", $tracking_info, "courses_ID = ".$course -> course['id']);
  if ($resetLessons) {
   foreach ($course -> getCourseLessons() as $lesson) {
    $this -> resetProgressInLesson($lesson);
   }
  }
 }
 /**

	 * Get the users's lessons list

	 *

	 * This function is used to get a list of ids with the users's lessons.

	 * If $returnObjects is set and true, then An array of lesson objects is returned

	 * The list is returned using the object's cache (unless $returnObjects is true).

	 * <br/>Example:

	 * <code>

	 * $lessonsList	= $user -> getLessons();						 //Returns an array with pairs [lessons id] => [user type]

	 * $lessonsObjects = $user -> getLessons(true);					 //Returns an array of lesson objects

	 * </code>

	 * If $returnObjects is specified, then each lesson in the lessons array will

	 * contain an additional field holding information on the user's lesson status

	 *

	 * @param boolean $returnObjects Whether to return lesson objects

	 * @param string $basicType If set, then return only lessons that the user has the specific basic role in them

	 * @return array An array of [lesson id] => [user type] pairs, or an array of lesson objects

	 * @since 3.5.0

	 * @access public

	 */
 public function getLessons($returnObjects = false, $basicType = false) {
  if ($this -> lessons && !$returnObjects) {
   $userLessons = $this -> lessons;
  } else {
   if ($returnObjects) {
    $userLessons = array();
    //Assign all lessons to an array, this way avoiding looping queries
    $result = eF_getTableData("lessons l, users_to_lessons ul", "l.*", "l.archive=0 and l.id=ul.lessons_ID and ul.archive = 0 and ul.users_LOGIN = '".$this -> user['login']."'", "l.name");
    foreach ($result as $value) {
     $lessons[$value['id']] = $value;
    }
    $courseLessons = array();
    $nonCourseLessons = array();
    $result = eF_getTableData("users u,users_to_lessons ul, lessons l", "ul.*, u.user_type as basic_user_type, u.user_types_ID", "l.archive=0 and l.id = ul.lessons_ID and ul.archive=0 and ul.users_LOGIN = u.login and ul.users_LOGIN = '".$this -> user['login']."' and ul.lessons_ID != 0", "l.name");
    foreach ($result as $value) {
     try {
      $lesson = new EfrontLesson($lessons[$value['lessons_ID']]);
      $lesson -> userStatus = $value;
      if ($lesson -> lesson['course_only']) {
       $courseLessons[$value['lessons_ID']] = $lesson;
      } else {
       $nonCourseLessons[$value['lessons_ID']] = $lesson;
      }
     } catch (Exception $e) {} //Do nothing in case of exception, simply do not take into account this lesson
    }
    $userLessons = $courseLessons + $nonCourseLessons;
   } else {
    $result = eF_getTableDataFlat("users_to_lessons ul, lessons l", "ul.lessons_ID, ul.user_type", "l.archive=0 and ul.archive=0 and ul.lessons_ID=l.id and ul.users_LOGIN = '".$this -> user['login']."'", "l.name");
    if (sizeof($result) > 0) {
     $this -> lessons = array_combine($result['lessons_ID'], $result['user_type']);
    } else {
     $this -> lessons = array();
    }
    foreach ($this -> lessons as $lessonId => $userType) {
     if (!$userType) { //For some reason, the user type is not set in the database. so set it now
      $userType = $this -> user['user_type'];
      $this -> lessons[$lessonId] = $userType;
      eF_updateTableData("users_to_lessons", array("user_type" => $userType), "lessons_ID=$lessonId and users_LOGIN='".$this -> user['login']."'");
      $cacheKey = "user_lesson_status:lesson:".$lessonId."user:".$this -> user['login'];
      Cache::resetCache($cacheKey);
     }
    }
    unset($userType);
    $userLessons = $this -> lessons;
   }
  }
  if ($basicType) {
   $roles = EfrontLessonUser :: getLessonsRoles();
   foreach ($userLessons as $id => $role) {
    if ($role instanceof EfrontLesson) { //$returnObjects is true
     if ($roles[$role -> userStatus['user_type']] != $basicType) {
      unset($userLessons[$id]);
     }
    } else {
     if ($roles[$role] != $basicType) {
      unset($userLessons[$id]);
     }
    }
   }
  }
  return $userLessons;
 }
 //@TODO: REPLACE getLessons
 public function getUserLessons() {
  //if ($this -> lessons === false) {			//COMMENT-IN WHEN IT REPLACES getLessons()
  $this -> initializeLessons();
  //}
  $lessons = array();
  foreach ($this -> lessons as $key => $lesson) {
   $lessons[$key] = new EfrontLesson($lesson);
  }
  return $lessons;
 }
 /**

	 * Initialize user lessons

	 *

	 * @since 3.6.1

	 * @access protected

	 */
 private function initializeLessons() {
  $result = eF_getTableData("users_to_lessons ul, lessons l",
          "ul.*, ul.to_timestamp as timestamp_completed, ul.from_timestamp as active_in_lesson, l.id, l.name, l.directions_ID, l.course_only, l.instance_source, l.duration,l.options,l.to_timestamp,l.from_timestamp, 1 as has_lesson",
          "l.archive = 0 and ul.archive = 0 and l.id=ul.lessons_ID and ul.users_LOGIN='".$this -> user['login']."'");
  if (empty($result)) {
   $this -> lessons = array();
  } else {
   foreach ($result as $value) {
    $this -> lessons[$value['id']] = $value;
   }
  }
 }
 public function getUserAutonomousLessons() {
  $lessons = $this -> getUserLessons();
  foreach ($lessons as $key => $lesson) {
   if ($lesson -> lesson['instance_source']) {
    unset($lessons[$key]);
   }
  }
  return $lessons;
 }
 /**

	 * Get user's eligible lessons

	 *

	 * This function is used to filter the user's lessons, excluding all the lessons

	 * that he is enrolled to, but cannot access for some reason (rules, schedule, active, etc)

	 *

	 * <br/>Example:

	 * <code>

	 * $eligibleLessons = $user -> getEligibleLessons();						 //Returns an array of EfrontLesson objects

	 * </code>

	 *

	 * @return array An array of lesson objects

	 * @since 3.6.0

	 * @access public

	 * @see libraries/EfrontLessonUser#getLessons($returnObjects, $basicType)

	 */
 public function getEligibleLessons() {
  $userCourses = $this -> getUserCourses();
  $userLessons = $this -> getUserStatusInLessons(false, true);
//pr($userLessons);
  $roles = self :: getLessonsRoles();
  $roleNames = self :: getLessonsRoles(true);
  foreach ($userCourses as $course) {
   $eligible = $course -> checkRules($this -> user['login'], $userLessons);
   foreach ($eligible as $lessonId => $value) {
    if (!$value) {
     unset($userLessons[$lessonId]);
    }
   }
  }
  $eligibleLessons = array();
  foreach ($userLessons as $lesson) {
   if ($lesson -> lesson['active_in_lesson'] && (!isset($lesson -> lesson['eligible']) || (isset($lesson -> lesson['eligible']) && $lesson -> lesson['eligible']))) {
    $eligibleLessons[$lesson -> lesson['id']] = $lesson;
   }
  }
  return $eligibleLessons;
 }
 /**

	 * Get user potential lessons

	 *

	 * This function returns a list with the lessons that the user

	 * may take, but doesn't have. The list may be either a list of ids

	 * (faster) or a list of EfrontLesson objects.

	 * <br/>Example:

	 * <code>

	 * $user -> getNonLessons();			//Returns a list with potential lessons ids

	 * $user -> getNonLessons(true);		//Returns a list of EfrontLesson objects

	 * </code>

	 *

	 * @param boolean $returnObjects Whether to return a list of objects

	 * @return array The list of ids or objects

	 * @since 3.5.0

	 * @access public

	 */
 public function getNonLessons($returnObjects = false) {
  $userLessons = eF_getTableDataFlat("users_to_lessons", "lessons_ID", "archive=0 and users_LOGIN = '".$this -> user['login']."'");
  //sizeof($userLessons) > 0 ? $sql = "and id not in (".implode(",", $userLessons['lessons_ID']).")" : $sql = '';
  sizeof($userLessons) > 0 ? $sql = "active = 1 and id not in (".implode(",", $userLessons['lessons_ID']).")" : $sql = 'active = 1';
  if ($returnObjects) {
   $nonUserLessons = array();
   //$lessons		= eF_getTableData("lessons", "*", "languages_NAME='".$this -> user['languages_NAME']."'".$sql);
   $lessons = eF_getTableData("lessons", "*", $sql);
   foreach ($lessons as $value) {
    $nonUserLessons[$value['id']] = new EfrontLesson($value['id']);
   }
   return $nonUserLessons;
  } else {
   //$lessons = eF_getTableDataFlat("lessons", "*", "languages_NAME='".$this -> user['languages_NAME']."'".$sql);
   $lessons = eF_getTableDataFlat("lessons", "*", $sql);
   return $lessons['id'];
  }
 }
 /**

	 * Return only non lessons that can be selected by the student

	 *

	 * This function is similar to getNonLessons, the only difference being that it excludes lessons

	 * that can't be directly assigned, for example inactive, unpublished etc

	 *

	 * @return array The eligible lessons

	 * @since 3.6.0

	 * @access public

	 * @see EfrontLessonUser :: getNonLessons()

	 */
 public function getEligibleNonLessons() {
  $lessons = $this -> getNonLessons(true);
  foreach ($lessons as $key => $lesson) {
   if (!$lesson -> lesson['active'] || !$lesson -> lesson['publish'] || !$lesson -> lesson['show_catalog']) {
    unset($lessons[$key]);
   }
  }
  return $lessons;
 }
 public function getUserCourses($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  $select['main'] = 'c.id, uc.users_LOGIN,uc.courses_ID,uc.completed,uc.score,uc.user_type,uc.issued_certificate,uc.from_timestamp as active_in_course, uc.to_timestamp, 1 as has_course';
  $select['has_instances'] = "(select count( * ) from courses c1, users_to_courses uc1 where c1.instance_source=c.id and uc1.courses_ID=c1.id and uc.users_LOGIN='".$this -> user['login']."') as has_instances";
  $select['num_lessons'] = "(select count( * ) from lessons_to_courses cl, lessons l where cl.courses_ID=c.id and l.archive=0 and l.id=cl.lessons_ID) as num_lessons";
  $select['num_students'] = "(select count( * ) from users_to_courses uc, users u where uc.courses_ID=c.id and u.archive=0 and u.login=uc.users_LOGIN and u.user_type='student') as num_students";
  $select = EfrontCourse :: convertCourseConstraintsToRequiredFields($constraints, $select);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $where[] = "c.id=uc.courses_ID and uc.users_LOGIN='".$this -> user['login']."' and uc.archive=0";
  //$result  = eF_getTableData("courses c, users_to_courses uc", $select, implode(" and ", $where), $orderby, false, $limit);
  $sql = prepareGetTableData("courses c, users_to_courses uc", implode(",", $select), implode(" and ", $where), $orderby, false, $limit);
  $result = eF_getTableData("courses, ($sql) t", "courses.*, t.*", "courses.id=t.id");
  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontCourse :: convertDatabaseResultToCourseObjects($result);
  } else {
   return EfrontCourse :: convertDatabaseResultToCourseArray($result);
  }
 }
 public function countUserCourses($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $where[] = "c.id=uc.courses_ID and uc.users_LOGIN='".$this -> user['login']."' and uc.archive=0";
  $result = eF_countTableData("courses c, users_to_courses uc", "c.id", implode(" and ", $where));
  return $result[0]['count'];
 }
 public function getUserCoursesIncludingUnassigned($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  $select['main'] = "c.id, r.courses_ID is not null as has_course, r.completed,r.score, r.from_timestamp as active_in_course";
  $select['user_type'] = "(select user_type from users_to_courses uc1 where users_login='".$this -> user['login']."' and uc1.courses_ID=c.id) as user_type";
  $select['has_instances'] = "(select count( * ) from courses l where instance_source=c.id) as has_instances";
  $select['num_lessons'] = "(select count( * ) from lessons_to_courses cl, lessons l where cl.courses_ID=c.id and l.archive=0 and l.id=cl.lessons_ID) as num_lessons";
  $select['num_students'] = "(select count( * ) from users_to_courses uc, users u where uc.courses_ID=c.id and u.archive=0 and u.login=uc.users_LOGIN and u.user_type='student') as num_students";
  $select = EfrontCourse :: convertCourseConstraintsToRequiredFields($constraints, $select);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  //$result  = eF_getTableData("courses c left outer join (select completed,score,courses_ID, from_timestamp,archive from users_to_courses where users_login='".$this -> user['login']."' and archive=0) r on c.id=r.courses_ID ", $select, implode(" and ", $where), $orderby, "", $limit);
  $sql = prepareGetTableData("courses c left outer join (select completed,score,courses_ID, from_timestamp,archive from users_to_courses where users_login='".$this -> user['login']."' and archive=0) r on c.id=r.courses_ID ", implode(",", $select), implode(" and ", $where), $orderby, "", $limit);
  $result = eF_getTableData("courses, ($sql) t", "courses.*, t.*", "courses.id=t.id");
  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontCourse :: convertDatabaseResultToCourseObjects($result);
  } else {
   return EfrontCourse :: convertDatabaseResultToCourseArray($result);
  }
 }
 public function countUserCoursesIncludingUnassigned($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  //$where[] = "d.id=c.directions_ID";
  $result = eF_countTableData("courses c left outer join (select completed,score,courses_ID, from_timestamp from users_to_courses where users_login='".$this -> user['login']."' and archive=0) r on c.id=r.courses_ID ", "c.id",
  implode(" and ", $where));
  return $result[0]['count'];
 }
 /**

	 * Get all courses, signifying those that the user already has, and aggregate instance results

	 *

	 * @param array $constraints The constraints for the query

	 * @return array An array of EfrontCourse objects

	 * @since 3.6.2

	 * @access public

	 */
 public function getUserCoursesAggregatingResultsIncludingUnassigned($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  $select['main'] = 'c.id';
  $select['user_type'] = "(select user_type from users_to_courses uc1 where users_login='".$this -> user['login']."' and uc1.courses_ID=c.id) as user_type";
  $select['score'] = "(select max(score) 	 from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) as score";
  $select['completed'] = "(select max(completed) from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) as completed";
  $select['to_timestamp'] = "(select max(to_timestamp) from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) as to_timestamp";
  $select['active_in_course'] = "(select max(from_timestamp) from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) as active_in_course";
  $select['has_course'] = "(select count(*) > 0   from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) as has_course";
  $select['num_lessons'] = "(select count( * ) from lessons_to_courses cl, lessons l where cl.courses_ID=c.id and l.archive=0 and l.id=cl.lessons_ID) as num_lessons";
  $select['num_students'] = "(select count( * ) from users_to_courses uc, users u where uc.courses_ID=c.id and u.archive=0 and u.login=uc.users_LOGIN and u.user_type='student') as num_students";
  $select = EfrontCourse :: convertCourseConstraintsToRequiredFields($constraints, $select);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  //WITH THIS NEW QUERY, WE GET THE SLOW 'has_instances' PROPERTY AFTER FILTERING
  $sql = prepareGetTableData("courses c left outer join (select id from courses) r on c.id=r.id", implode(",", $select), implode(" and ", $where), $orderby, false, $limit);
  $result = eF_getTableData(
     "courses, ($sql) t",
     "courses.*, (select count(id) from courses c1 where c1.instance_source=courses.id ) as has_instances, t.*",
     "courses.id=t.id");
  //THIS WAS THE OLD QUERY, MUCH SLOWER
  //$result  = eF_getTableData("courses c left outer join (select id from courses) r on c.id=r.id", $select, implode(" and ", $where), $orderby, false, $limit);
  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontCourse :: convertDatabaseResultToCourseObjects($result);
  } else {
   return EfrontCourse :: convertDatabaseResultToCourseArray($result);
  }
 }
 public function countUserCoursesAggregatingResultsIncludingUnassigned($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  //$where[] = "d.id=c.directions_ID";
  $result = eF_countTableData("courses c left outer join (select id from courses) r on c.id=r.id", "c.id",
  implode(" and ", $where));
  return $result[0]['count'];
 }
 /**

	 * The same as self::getUserCoursesAggregatingResultsIncludingUnassigned, only it has an addition "where" condition

	 * @param array $constraints

	 * @return array

	 * @since 3.6.2

	 */
 public function getUserCoursesAggregatingResults($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  $select['main'] = 'c.id';
  $select['user_type'] = "(select user_type from users_to_courses uc1 where users_login='".$this -> user['login']."' and uc1.courses_ID=c.id) as user_type";
  $select['score'] = "(select max(score) 	 from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) as score";
  $select['completed'] = "(select max(completed) from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) as completed";
  $select['to_timestamp'] = "(select max(to_timestamp) from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) as to_timestamp";
  $select['active_in_course'] = "(select max(from_timestamp) from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) as active_in_course";
  $select['has_course'] = "(select count(*) > 0   from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) as has_course";
  $select['num_lessons'] = "(select count( * ) from lessons_to_courses cl, lessons l where cl.courses_ID=c.id and l.archive=0 and l.id=cl.lessons_ID) as num_lessons";
  $select['num_students'] = "(select count( * ) from users_to_courses uc, users u where uc.courses_ID=c.id and u.archive=0 and u.login=uc.users_LOGIN and u.user_type='student') as num_students";
  $select = EfrontCourse :: convertCourseConstraintsToRequiredFields($constraints, $select);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $where[] = "(select count(*) > 0 from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID)=1";
  //WITH THIS NEW QUERY, WE GET THE SLOW 'has_instances' PROPERTY AFTER FILTERING
  $sql = prepareGetTableData("courses c left outer join (select id from courses) r on c.id=r.id", implode(",", $select), implode(" and ", $where), $orderby, false, $limit);
  $result = eF_getTableData(
     "courses, ($sql) t",
     "courses.*, (select count(id) from courses c1 where c1.instance_source=courses.id ) as has_instances, t.*",
     "courses.id=t.id");
  //THIS WAS THE OLD QUERY, MUCH SLOWER
  //$result  = eF_getTableData("courses c left outer join (select id from courses) r on c.id=r.id", $select, implode(" and ", $where), $orderby, false, $limit);
  if (!isset($constraints['return_objects']) || $constraints['return_objects'] == true) {
   return EfrontCourse :: convertDatabaseResultToCourseObjects($result);
  } else {
   return EfrontCourse :: convertDatabaseResultToCourseArray($result);
  }
/*		

		list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);

		$select  = "c.*,

					  (select user_type from users_to_courses uc1 where users_login='".$this -> user['login']."' and uc1.courses_ID=c.id) 

					  		as user_type, 

					  (select max(score) 	 from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) 

					  		as score,

					  (select max(completed) from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) 

					  		as completed,

					  (select max(to_timestamp) from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) 

					  		as to_timestamp,

					  (select count(*) > 0   from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID) 

					  		as has_course,

					  (select count( * ) 	 from courses c1 where c1.instance_source=c.id ) 

					  		as has_instances, 

					  (select count( * ) from users_to_courses uc, users u where uc.courses_ID=c.id and u.archive=0 and u.login=uc.users_LOGIN and u.user_type='student') 

					  		as num_students,

					  (select count( * ) from lessons_to_courses cl, lessons l where cl.courses_ID=c.id and l.archive=0 and l.id=cl.lessons_ID) 

					  		as num_lessons";

#ifdef ENTERPRISE

			$select .= ",(select count( * ) from module_hcd_course_offers_skill s where courses_ID=c.id)

					  		as num_skills,

					  	(select b.name from module_hcd_branch b, module_hcd_course_to_branch cb where cb.branches_ID=b.branch_ID and cb.courses_ID=c.id limit 1) 

					  		as location";			

#endif



		$where[] = "(select count(*) > 0 from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID)=1";

		$result  = eF_getTableData("courses c left outer join (select id from courses) r on c.id=r.id", $select,

				implode(" and ", $where), $orderby, false, $limit);

		return EfrontCourse :: convertDatabaseResultToCourseObjects($result);

*/
 }
 public function countUserCoursesAggregatingResults($constraints = array()) {
  !empty($constraints) OR $constraints = array('archive' => false, 'active' => true);
  list($where, $limit, $orderby) = EfrontCourse :: convertCourseConstraintsToSqlParameters($constraints);
  $where[] = "d.id=c.directions_ID and (select count(*) > 0 from users_to_courses uc1, courses c1 where uc1.users_login='".$this -> user['login']."' and uc1.archive=0 and (c1.instance_source=c.id or c1.id=c.id) and c1.id=uc1.courses_ID)=1";
  $result = eF_countTableData("directions d,courses c left outer join (select id from courses) r on c.id=r.id", "c.id",
  implode(" and ", $where));
  return $result[0]['count'];
 }
 public function filterCoursesBasedOnInstance($courses, $instanceSource) {
  foreach ($courses as $key => $course) {
   if ($course -> course['instance_source'] != $instanceSource && $course -> course['id'] != $instanceSource) {
    unset($courses[$key]);
   } else {
    $courses[$key] -> course['num_lessons'] = $course -> countCourseLessons();
   }
  }
  return $courses;
 }
 /**

	 * Return only regular courses, not instances.

	 * Assign the completion and highest instance score to the parent course, from its instances.

	 *

	 */
 public function filterCoursesWithInstanceStatus($courses) {
  foreach ($courses as $key => $course) {
   if ($course -> course['instance_source']) {
    $instanceSource = $course -> course['instance_source'];
    if ($course -> course['completed']) {
     $courses[$instanceSource] -> course['completed'] = 1;
     if ($course -> course['score'] > $courses[$instanceSource] -> course['score']) {
      $courses[$instanceSource] -> course['score'] = $course -> course['score'];
     }
    }
    unset($courses[$key]);
   } else {
    $courses[$key] -> course['num_lessons'] = $course -> countCourseLessons();
   }
  }
  return $courses;
 }
 public function getUserStatusInIndependentLessons() {
  $userLessons = $this -> getUserStatusInLessons();
  foreach ($userLessons as $key => $lesson) {
   if ($lesson -> lesson['course_only']) {
    unset($userLessons[$key]);
   }
  }
  return $userLessons;
 }
 public function getUserStatusInCourseLessons($course) {
  $userLessons = $this -> getUserStatusInLessons();
  $courseLessons = $course -> getCourseLessons();
  foreach ($userLessons as $key => $lesson) {
   if (!in_array($key, array_keys($courseLessons))) {
    unset($userLessons[$key]);
   }
  }
  return $userLessons;
 }
 public function getUserStatusInLessons($lessons = false, $onlyContent = false) {
  $userLessons = $this -> getUserLessons();
  if ($lessons !== false) {
   $lessonIds = $this -> verifyLessonsList($lessons);
   foreach ($lessonIds as $id) {
    if (in_array($id, array_keys($userLessons))) {
     $temp[$id] = $userLessons[$id];
    }
   }
   $userLessons = $temp;
  }
  foreach ($userLessons as $key => $lesson) {
   $lesson = $this -> checkUserAccessToLessonBasedOnDuration($lesson);
   if ($lesson -> lesson['user_type'] != $this -> user['user_type']) {
    $lesson -> lesson['different_role'] = 1;
   }
   $userLessons[$key] -> lesson['overall_progress'] = $this -> getUserOverallProgressInLesson($lesson);
   if (!$onlyContent) {
    $userLessons[$key] -> lesson['project_status'] = $this -> getUserProjectsStatusInLesson($lesson);
    $userLessons[$key] -> lesson['test_status'] = $this -> getUserTestsStatusInLesson($lesson);
    $userLessons[$key] -> lesson['time_in_lesson'] = $this -> getUserTimeInLesson($lesson);
   }
  }
  return $userLessons;
 }
 private function checkUserAccessToLessonBasedOnDuration($lesson) {
  if ($lesson -> lesson['duration'] && $lesson -> lesson['from_timestamp']) {
   $lesson -> lesson['remaining'] = $lesson -> lesson['from_timestamp'] + $lesson -> lesson['duration']*3600*24 - time();
  } else {
   $lesson -> lesson['remaining'] = null;
  }
  //Check whether the lesson registration is expired. If so, set $value['from_timestamp'] to false, so that the effect is to appear disabled
  if ($lesson -> lesson['duration'] && $lesson -> lesson['from_timestamp'] && $lesson -> lesson['duration'] * 3600 * 24 + $lesson -> lesson['from_timestamp'] < time()) {
   $lesson -> archiveLessonUsers($lesson -> lesson['users_LOGIN']);
  }
  return $lesson;
 }
 public function archiveUserCourses($courses) {
  $courses = $this -> verifyCoursesList($courses);
  foreach ($courses as $course) {
   $course = new EfrontCourse($course);
   $course -> archiveCourseUsers($this);
  }
  $this -> courses = false; //Reset users cache
  return $this -> getUserCourses();
 }
 private function verifyCoursesList($courses) {
  if (!is_array($courses)) {
   $courses = array($courses);
  }
  foreach ($courses as $key => $value) {
   if ($value instanceOf EfrontCourse) {
    $courses[$key] = $value -> course['id'];
   } elseif (!eF_checkParameter($value, 'id')) {
    unset($courses[$key]);
   }
  }
  return array_values(array_unique($courses)); //array_values() to reindex array
 }
 private function verifyRolesList($roles, $length) {
  if (!is_array($roles)) {
   $roles = array($roles);
  }
  if (sizeof($roles) < $length) {
   $roles = array_pad($roles, $length, $roles[0]);
  }
  return array_values($roles); //array_values() to reindex array
 }
 private function sendNotificationsRemoveUserCourses($courses) {
  foreach ($courses as $key => $course) {
   $courseIds[] = $key;
  }
  EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_REMOVAL,
          "users_LOGIN" => $this -> user['login'],
          "lessons_ID" => $courseIds));
 }
 public function archiveUserLessons($lessons) {
  $lessons = $this -> verifyLessonsList($lessons);
  $this -> sendNotificationsRemoveUserLessons($lessons);
  foreach ($lessons as $lesson) {
   eF_updateTableData("users_to_lessons", array("archive" => time()), "users_LOGIN='".$this -> user['login']."' and lessons_ID=$lesson");
   $cacheKey = "user_lesson_status:lesson:".$lesson."user:".$this -> user['login'];
   Cache::resetCache($cacheKey);
  }
  $this -> lessons = false; //Reset users cache
  return $this -> getLessons();
 }
 private function verifyLessonsList($lessons) {
  if (!is_array($lessons)) {
   $lessons = array($lessons);
  }
  foreach ($lessons as $key => $value) {
   if ($value instanceOf EfrontLesson) {
    $lessons[$key] = $value -> lesson['id'];
   } elseif (!eF_checkParameter($value, 'id')) {
    unset($lessons[$key]);
   }
  }
  return array_values(array_unique($lessons)); //array_values() to reindex array
 }
 private function verifyLessonObjectsList($lessons) {
  if (!is_array($lessons)) {
   $lessons = array($lessons);
  }
  $lessonsList = array();
  foreach ($lessons as $value) {
   if (!($value instanceOf EfrontLesson)) {
    $value = new EfrontLesson($value);
    $lessonsList[$value -> lesson['id']] = $value;
   }
  }
  return $lessonsList;
 }
 private function sendNotificationsRemoveUserLessons($lessons) {
  foreach ($lessons as $key => $lesson) {
   $lessonIds[] = $key;
  }
  EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_REMOVAL,
          "users_LOGIN" => $this -> user['login'],
          "lessons_ID" => $lessonIds));
 }
 private function getUserTimeInLesson($lesson) {
  $userTimes = EfrontStats :: getUsersTimeAll(false, false, array($lesson -> lesson['id'] => $lesson -> lesson['id']), array($this -> user['login'] => $this -> user['login']));
  $userTimes = $userTimes[$lesson -> lesson['id']][$this -> user['login']];
  $userTimes['time_string'] = '';
  if ($userTimes['total_seconds']) {
   !$userTimes['hours'] OR $userTimes['time_string'] .= $userTimes['hours']._HOURSSHORTHAND.' ';
   !$userTimes['minutes'] OR $userTimes['time_string'] .= $userTimes['minutes']._MINUTESSHORTHAND.' ';
   !$userTimes['seconds'] OR $userTimes['time_string'] .= $userTimes['seconds']._SECONDSSHORTHAND;
  }
  return $userTimes;
 }
 private function getUserOverallProgressInLesson($lesson) {
  $totalUnits = $completedUnits = 0;
  $contentTree = new EfrontContentTree($lesson);
  $validUnits = array();
  foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($contentTree -> tree), RecursiveIteratorIterator :: SELF_FIRST))) as $key => $value) {
   $totalUnits++;
   $validUnits[$key] = $key;
  }
  if ($doneContent = unserialize($lesson -> lesson['done_content'])) {
   $doneContent = array_intersect($doneContent,$validUnits); // to avoid counting deleted content makriria fix
   $completedUnits = sizeof($doneContent);
  }
  if ($totalUnits) {
   $completedUnitsPercentage = round(100 * $completedUnits/$totalUnits, 2);
   return array('total' => $totalUnits,
       'completed' => $completedUnits,
       'percentage' => $completedUnitsPercentage);
  } else {
   return array('total' => 0,
       'completed' => 0,
       'percentage' => 0);
  }
 }
 private function getUserTestsStatusInLesson($lesson) {
  $completedTests = $meanTestScore = 0;
  $tests = $lesson -> getTests(true, true);
  $totalTests = sizeof($tests);
  $result = eF_getTableData("completed_tests ct, tests t", "ct.tests_ID, ct.score", "t.id=ct.tests_ID and ct.users_LOGIN='".$this -> user['login']."' and ct.archive=0 and t.lessons_ID=".$lesson -> lesson['id']);
  foreach ($result as $value) {
   if (in_array($value['tests_ID'], array_keys($tests))) {
    $meanTestScore += $value['score'];
    $completedTests++;
   }
  }
  $scormTests = $this -> getUserScormTestsStatusInLesson($lesson);
  $totalTests += sizeof($scormTests);
  foreach ($scormTests as $value) {
   $meanTestScore += $value;
   $completedTests++;
  }
  if ($totalTests) {
   $completedTestsPercentage = round(100 * $completedTests/$totalTests, 2);
   $meanTestScore = round($meanTestScore/$completedTests, 2);
   return array('total' => $totalTests,
       'completed' => $completedTests,
       'percentage' => $completedTestsPercentage,
       'mean_score' => $meanTestScore);
  } else {
   return array();
  }
 }
 private function getUserScormTestsStatusInLesson($lesson) {
  $usersDoneScormTests = eF_getTableData("scorm_data sd left outer join content c on c.id=sd.content_ID",
              "c.id, c.ctg_type, sd.masteryscore, sd.lesson_status, sd.score, sd.minscore, sd.maxscore",
              "c.ctg_type = 'scorm_test' and (sd.users_LOGIN = '".$this -> user['login']."' or sd.users_LOGIN is null) and c.lessons_ID = ".$lesson -> lesson['id']);
  $tests = array();
  foreach ($usersDoneScormTests as $doneScormTest) {
   if (is_numeric($doneScormTest['minscore']) || is_numeric($doneScormTest['maxscore'])) {
    $doneScormTest['score'] = 100 * $doneScormTest['score'] / ($doneScormTest['minscore'] + $doneScormTest['maxscore']);
   } else {
    $doneScormTest['score'] = $doneScormTest['score'];
   }
   $tests[$doneScormTest['id']] = $doneScormTest['score'];
  }
  return $tests;
 }
 private function getUserProjectsStatusInLesson($lesson) {
  $completedProjects = $meanProjectScore = 0;
  $projects = $lesson -> getProjects(true, $this);
  $totalProjects = sizeof($projects);
  foreach ($projects as $project) {
   if ($project -> project['grade'] || $project -> project['grade'] === 0) {
    $completedProjects++;
    $meanProjectScore += $project -> project['grade'];
   }
  }
  if ($totalProjects) {
   $completedProjectsPercentage = round(100 * $completedProjects/$totalProjects, 2);
   $meanProjectScore = round($meanProjectScore/$completedProjects, 2);
   return array('total' => $totalProjects,
       'completed' => $completedProjects,
       'percentage' => $completedProjectsPercentage,
       'mean_score' => $meanProjectScore);
  } else {
   return array();
  }
 }
 /**

	 * Get user certificates

	 *

	 * This function gets all certificates that have been issued for the user

	 * <br/>Example:

	 * <code>

	 * $user -> getIssuedCertificates();	   //Get an array with the information on the certificates

	 * </code>

	 *

	 * @return an array of the format [] => [course name, certificate key, date issued, date expire, issuing authority]

	 * @since 3.6.1

	 * @access public

	 */
 public function getIssuedCertificates() {
  $constraints = array('archive' => false, 'active' => true, 'condition' => 'issued_certificate != 0 or issued_certificate is not null');
  $constraints['return_objects'] = false;
  $courses = $this -> getUserCourses($constraints);
  $certificates = array();
  foreach ($courses as $course) {
   if ($certificateInfo = unserialize($course['issued_certificate'])) {
    $certificateInfo = unserialize($course['issued_certificate']);
    $certificates[] = array("courses_ID" => $course['id'],
          "course_name" => $course['name'],
          "serial_number" => $certificateInfo['serial_number'],
          "grade" => $certificateInfo['grade'],
          "issue_date" => $certificateInfo['date'],
          "active" => $course['active'],
          "expiration_date"=> ($course['certificate_expiration']) ? ($certificateInfo['date'] + $course['certificate_expiration']) : _NEVER);
   }
  }
  return $certificates;
 }
 /**

	 * Assign courses to user.

	 *

	 * This function can be used to assign a course to the current user. If $userTypes

	 * is specified, then the user is assigned to the course using the specified type.

	 * By default, the user asic type is used.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> addCourses(23);						 //Add a signle course with id 23

	 * $user -> addCourses(23, 'professor');			//Add a signle course with id 23 and set the user type to 'professor'

	 * $user -> addCourses(array(23,24,25));			//Add multiple courses using an array

	 * $user -> addCourses(array(23,24,25), array('professor', 'student', 'professor'));			//Add multiple courses using an array for course ids and another for corresponding user types

	 * </code>

	 *

	 * @param mixed $courseIds Either a single course id, or an array if ids

	 * @param mixed $userTypes The corresponding user types for the specified courses

	 * @param boolean $activeate Courses will be set as active or not

	 * @return mixed The array of course ids or false if the course already exists.

	 * @since 3.5.0

	 * @access public

	 * @todo auto_projects

	 */
 public function addCourses($courses, $roles = 'student', $confirmed = true) {
  $courses = $this -> verifyCoursesList($courses);
  $roles = $this -> verifyRolesList($roles, sizeof($courses));
  $courses = eF_getTableData("courses", "*", "id in (".implode(",", $courses).")");
  foreach ($courses as $key => $course) {
   $course = new EfrontCourse($course);
   $course -> addUsers($this -> user['login'], $roles[$key], $confirmed);
  }
  $this -> courses = false; //Reset courses information
  return $this -> getUserCourses();
 }
 /**

	 * Confirm user's lessons

	 *

	 * This function can be used to set the "active" flag of a user's lesson to "true", so that

	 * he can access the corresponding lessons.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> addCourses(23);						 //Confirm a signle course with id 23

	 * $user -> addCourses(array(23,24,25));			//Confirm multiple courses using an array

	 * </code>

	 *

	 * @param mixed $courseIds Either a single course id, or an array if ids

	 * @return array The array of course ids

	 * @since 3.6.0

	 * @access public

	 */
 public function confirmCourses($courses) {
  $courses = $this -> verifyCoursesList($courses);
  foreach ($courses as $key => $course) {
   $course = new EfrontCourse($course);
   $course -> confirm($this);
  }
  $this -> courses = false; //Reset courses information
  return $this -> getserUCourses();
 }
 /**

	 * Remove courses from user.

	 *

	 * This function can be used to remove a course from the current user.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> removeCourses(23);						  //Remove a signle course with id 23

	 * $user -> removeCourses(array(23,24,25));			 //Remove multiple courses using an array

	 * </code>

	 *

	 * @param int $courseIds Either a single course id, or an array if ids

	 * @return true.

	 * @since 3.5.0

	 * @access public

	 */
 public function removeCourses($courses) {
  $courseIds = $this -> verifyCoursesList($courses);
  $result = eF_getTableData("lessons_to_courses lc, users_to_courses uc", "lc.*", "lc.courses_ID=uc.courses_ID and uc.users_LOGIN = '".$this -> user['login']."'");
  foreach ($result as $value) {
   $lessonsToCourses[$value['lessons_ID']][] = $value['courses_ID'];
   $coursesToLessons[$value['courses_ID']][] = $value['lessons_ID'];
  }
  if (!empty($courseIds)) {
   $userLessonsThroughCourse = eF_getTableDataFlat("lessons_to_courses lc, users_to_courses uc", "lc.lessons_ID", "lc.courses_ID=uc.courses_ID and uc.courses_ID in (".implode(",", $courseIds).") and uc.users_LOGIN = '".$this -> user['login']."'");
   $userLessonsThroughCourse = $userLessonsThroughCourse['lessons_ID'];
  }
  eF_deleteTableData("users_to_courses", "users_LOGIN = '".$this -> user['login']."' and courses_ID in (".implode(",", $courseIds).")"); //delete courses from list
  foreach ($courseIds as $id) {
   $cacheKey = "user_course_status:course:".$id."user:".$this -> user['login'];
   Cache::resetCache($cacheKey);
  }
  EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_REMOVAL, "users_LOGIN" => $this -> user['login'], "lessons_ID" => $courseIds));
  foreach ($userLessonsThroughCourse as $lesson) {
   if (sizeof($lessonsToCourses[$lesson]) == 1) {
    $this -> removeLessons($lesson);
   } else if (sizeof(array_diff($lessonsToCourses[$lesson], $courseIds)) == 0) {
    $this -> removeLessons($lesson);
   }
  }
  return $true;
 }
 /**

	 * Set user role

	 *

	 * This function is used to set the specific role of this user.

	 * <br/>Example:

	 * <code>

	 * $user -> setRole(23, 'simpleUser');		  //Set this user's role to 'simpleUser' for lesson with id 23

	 * $user -> setRole(23);						//Set this user's role to the same as its basic type (for example 'student') for lesson with id 23

	 * $user -> setRole(false, 'simpleUser');	   //Set this user's role to 'simpleUser' for all lessons

	 * $user -> setRole();						  //Set this user's role to the same as its basic type (for example 'student') for all lessons

	 * </code>

	 *

	 * @param int $lessonId The lesson id

	 * @param string $userRole The new user role

	 * @return boolean true if everything is ok

	 * @since 3.5.0

	 * @access public

	 */
 public function setRole($lessonId = false, $userRole = false) {
  if ($userRole) {
   $fields = array("user_type" => $userRole);
  } else {
   $fields = array("user_type" => $this -> user['user_type']);
  }
  if ($lessonId && eF_checkParameter($lessonId, 'id')) {
   eF_updateTableData("users_to_lessons", $fields, "users_LOGIN='".$this -> user['login']."' and lessons_ID=$lessonId");
   $cacheKey = "user_lesson_status:lesson:".$lessonId."user:".$this -> user['login'];
   Cache::resetCache($cacheKey);
  } else {
   eF_updateTableData("users_to_lessons", $fields, "users_LOGIN='".$this -> user['login']."'");
  }
 }
 /**

	 * Get the user's role

	 *

	 * This function returns the user role for the specified lesson

	 * <br/>Example:

	 * <code>

	 * $this -> getRole(4);								 //Get the role for lesson with id 4

	 * </code>

	 *

	 * @param int $lessonId The lesson id to get the role for

	 * @return string The user role for the lesson

	 * @since 3.5.0

	 * @access public

	 */
 public function getRole($lessonId) {
  $roles = EfrontLessonUser :: getLessonsRoles();
  if ($lessonId instanceof EfrontLesson) {
   $lessonId = $lessonId -> lesson['id'];
  }
  if (in_array($lessonId, array_keys($this -> getLessons()))) {
   $result = eF_getTableData("users_to_lessons", "user_type", "users_LOGIN='".$this -> user['login']."' and lessons_ID=".$lessonId);
   return $roles[$result[0]['user_type']];
  } else {
   return false;
  }
 }
 /**

	 * Get roles applicable to lessons

	 *

	 * This function is used to get the roles in the system, that derive from professor and student

	 * It returns an array where keys are the role ids and values are:

	 * - Either the role basic user types, if $getNames is false (the default)

	 * - or the role Names if $getNames is true

	 * The array is prepended with the 2 main roles, 'professor' and 'student'

	 * <br/>Example:

	 * <code>

	 * $roles = EfrontLessonUser :: getLessonsRoles();

	 * </code>

	 *

	 * @param boolean $getNames Whether to return id/basic user type pairs or id/name pairs

	 * @return array The lesson-oriented roles

	 * @since 3.5.0

	 * @access public

	 * @static

	 */
 public static function getLessonsRoles($getNames = false) {
  //Cache results in self :: $lessonRoles
  if (is_null(self :: $lessonRoles)) {
   $roles = eF_getTableDataFlat("user_types", "*", "active=1 AND basic_user_type!='administrator'"); //Get available roles
   self :: $lessonRoles = $roles;
  } else {
   $roles = self :: $lessonRoles;
  }
  if (sizeof($roles) > 0) {
   $getNames ? $roles = array('student' => _STUDENT, 'professor' => _PROFESSOR) + array_combine($roles['id'], $roles['name']) : $roles = array('student' => 'student', 'professor' => 'professor') + array_combine($roles['id'], $roles['basic_user_type']);
  } else {
   $getNames ? $roles = array('student' => _STUDENT, 'professor' => _PROFESSOR) : $roles = array('student' => 'student', 'professor' => 'professor');
  }
  return $roles;
 }
 /**

	 * Get lesson users

	 *

	 * This function returns a list with the students of all the lessons in which the current user has a professor role

	 * <br/>Example:

	 * <code>

	 *	  $user = EfrontUserFactory :: factory('professor');

	 *	  $students = $user -> getProfessorStudents();

	 * </code>

	 *

	 * @return array A list of user logins

	 * @since 3.5.0

	 * @access public

	 */
 public function getProfessorStudents(){
  $lessons = $this -> getLessons(true, 'professor');
  $students = array();
  foreach ($lessons as $lesson){
   $lesson_students = array();
   $lesson_students = $lesson -> getUsers('student');
   foreach ($lesson_students as $student){
    $students[] = $student['login'];
   }
  }
  return array_unique($students);
 }
 /**

	 * Get user information

	 *

	 * This function returns the user information in an array

	 *

	 *

	 * <br/>Example:

	 * <code>

	 * $info = $user -> getInformation();		 //Get lesson information

	 * </code>

	 *

	 * @param string $user The user login to customize lesson information for

	 * @return array The user information

	 * @since 3.5.0

	 * @access public

	 */
 public function getInformation() {
  $languages = EfrontSystem :: getLanguages(true);
  $info = array();
  $info['login'] = $this -> user['login'];
  $info['name'] = $this -> user['name'];
  $info['surname'] = $this -> user['surname'];
  $info['fullname'] = $this -> user['name'] . " " . $this -> user['surname'];
  $info['user_type'] = $this -> user['user_type'];
  $info['user_types_ID'] = $this -> user['user_types_ID'];
  $info['student_lessons'] = $this -> getLessons(true, 'student');
  $info['professor_lessons'] = $this -> getLessons(true, 'professor');
  $info['total_lessons'] = sizeof($this -> getUserLessons());
  $info['total_courses'] = sizeof($this -> getUserCourses(array('active' => true, 'return_objects' => false)));
  $info['total_login_time'] = self :: getLoginTime($this -> user['login']);
  $info['language'] = $languages[$this -> user['languages_NAME']];
  $info['active'] = $this -> user['active'];
  $info['active_str'] = $this -> user['active'] ? _YES : _NO;
  $info['joined'] = $this -> user['timestamp'];
  $info['joined_str'] = formatTimestamp($this -> user['timestamp'], 'time');
  $info['avatar'] = $this -> user['avatar'];
  return $info;
 }
 /**

	 * Get user related users

	 *

	 * This function returns all users that related to this user

	 * The relation depends on common lessons

	 *

	 * <br/>Example:

	 * <code>

	 * $myRelatedUsers = $user -> getRelatedUsers();		 //Get related users

	 * </code>

	 *

	 * @return array Of related users logins

	 * @since 3.6.0

	 * @access public

	 */
 public function getRelatedUsers() {
  $myLessons = $this ->getLessons();
  $other_users = eF_getTableDataFlat("users_to_lessons", "distinct users_LOGIN" , "archive=0 and lessons_ID IN ('" . implode("','", array_keys($myLessons)) . "') AND users_LOGIN <> '" . $this -> user['login'] . "'");
  $users = $other_users['users_LOGIN'];
  return $users;
 }
 /**

	 * Get the common lessons with a particular user

	 *

	 * <br/>Example:

	 * <code>

	 * $common_lessons	= $user -> getCommonLessons('joe'); // find the common lessons between this user and 'joe'

	 * </code>

	 *

	 * @return array with pairs [lessons_id] => [lessons_id, lessons_name] referring to the common lessons of this object's user and user with login=$login

	 * @since 3.6.0

	 * @access public

	 */
 public function getCommonLessons($login) {
  $result = eF_getTableData("users_to_lessons as ul1 JOIN users_to_lessons as ul2 ON ul1.lessons_ID = ul2.lessons_ID JOIN lessons ON ul1.lessons_ID = lessons.id", "lessons.id, lessons.name", "ul1.archive=0 and ul2.archive=0 and ul1.users_LOGIN = '".$this -> user['login']."' AND ul2.users_LOGIN = '".$login."'");
  $common_lessons = array();
  foreach ($result as $common_lesson) {
   $common_lessons[$common_lesson['id']] = $common_lesson;
  }
  return $common_lessons;
 }
 /**

	 * Get skillgap tests to do

	 *

	 * This function returns an array with all skill gap tests assigned to the student

	 * <br/>Example:

	 * <code>

	 * $user -> getSkillgapTests();						   //Set the unit with id 32 in lesson 2 as seen

	 * </code>

	 *

	 * @param No parameters

	 * @return Array of tests in the form [test_id] => [id, test_name]

	 * @since 3.5.2

	 * @access public

	 */
 public function getSkillgapTests() {
  $skillgap_tests = array();
  return $skillgap_tests;
 }
 public function getUserStatusInCourses() {
 }
 public function hasCourse($course) {
  if ($course instanceOf EfrontCourse) {
   $course = $course -> course['id'];
  } elseif (!eF_checkParameter($course, 'id')) {
   throw new EfrontCourseException(_INVALIDID.": $course", EfrontCourseException :: INVALID_ID);
  }
  $result = eF_getTableData("users_to_courses", "courses_ID", "courses_ID=$course and users_LOGIN='".$this -> user['login']."' and archive=0");
  return sizeof($result) > 0;
 }
 public function getUserTypeInCourse($course) {
  if ($course instanceOf EfrontCourse) {
   $course = $course -> course['id'];
  } elseif (!eF_checkParameter($course, 'id')) {
   throw new EfrontCourseException(_INVALIDID.": $course", EfrontCourseException :: INVALID_ID);
  }
  $result = eF_getTableData("users_to_courses", "user_type", "courses_ID=$course and users_LOGIN='".$this -> user['login']."' and archive=0");
  if (!empty($result)) {
   return $result[0]['user_type'];
  } else {
   return false;
  }
 }
}
/**

 * Class for professor users

 *

 * @package eFront

 */
class EfrontProfessor extends EfrontLessonUser
{
 /**

	 * Delete user

	 *

	 * This function is used to delete a user from the system.

	 * The user cannot be deleted if he is the last system administrator.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> delete();

	 * </code>

	 *

	 * @return boolean True if the user was deleted successfully

	 * @since 3.5.0

	 * @access public

	 */
 public function delete() {
  parent :: delete();
  eF_deleteTableData("users_to_lessons", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("users_to_courses", "users_LOGIN='".$this -> user['login']."'");
/*

		foreach ($this -> getCourses() as $id => $value) {

			$cacheKey = "user_course_status:course:".$id."user:".$this -> user['login'];

			Cache::resetCache($cacheKey);

		}

*/
 }
}
/**

 * Class for student users

 *

 * @package eFront

 */
class EfrontStudent extends EfrontLessonUser
{
 /**

	 * Delete user

	 *

	 * This function is used to delete a user from the system.

	 * The user cannot be deleted if he is the last system administrator.

	 * <br/>Example:

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');

	 * $user -> delete();

	 * </code>

	 *

	 * @return boolean True if the user was deleted successfully

	 * @since 3.5.0

	 * @access public

	 */
 public function delete() {
  parent :: delete();
  $userDoneTests = eF_getTableData("done_tests", "id", "users_LOGIN='".$this -> user['login']."'");
  if (sizeof($userDoneTests) > 0) {
   eF_deleteTableData("done_questions", "done_tests_ID IN (".implode(",", $userDoneTests['id']).")");
   eF_deleteTableData("done_tests", "users_LOGIN='".$this -> user['login']."'");
  }
  eF_deleteTableData("users_to_lessons", "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("users_to_courses", "users_LOGIN='".$this -> user['login']."'");
/*

		foreach ($this -> getCourses() as $id => $value) {

			$cacheKey = "user_course_status:course:".$id."user:".$this -> user['login'];

			Cache::resetCache($cacheKey);

		}

*/
  eF_deleteTableData("users_to_projects", "users_LOGIN='".$this -> user['login']."'");
  //eF_deleteTableData("users_to_done_tests",   "users_LOGIN='".$this -> user['login']."'");
  eF_deleteTableData("completed_tests", "users_LOGIN='".$this -> user['login']."'");
 }
 /**

	 * Complete lesson

	 *

	 * This function is used to set the designated lesson's status

	 * to 'completed' for the current user.

	 * <br/>Example:

	 * <code>

	 * $user -> completeLesson(5, 87, 'Very good progress');									  //Complete lesson with id 5

	 * </code>

	 *

	 * @param mixed $lesson Either the lesson id, or an EfrontLesson object

	 * @param array $fields Extra fields containing the user score and any comments

	 * @return boolean true if everything is ok

	 * @since 3.5.0

	 * @access public

	 */
 public function completeLesson($lesson, $score = 100, $comments = '') {
  if (!($lesson instanceof EfrontLesson)) {
   $lesson = new EfrontLesson($lesson);
  }
  if (in_array($lesson -> lesson['id'], array_keys($this -> getLessons()))) {
   $fields = array('completed' => 1,
       'to_timestamp' => time(),
       'score' => $score,
       'comments' => $comments);
   eF_updateTableData("users_to_lessons", $fields, "users_LOGIN = '".$this -> user['login']."' and lessons_ID=".$lesson -> lesson['id']);
   //$cacheKey = "user_lesson_status:lesson:".$lesson -> lesson['id']."user:".$this -> user['login'];
   //Cache::resetCache($cacheKey);
   // Timelines event
   EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_COMPLETION, "users_LOGIN" => $this -> user['login'], "lessons_ID" => $lesson -> lesson['id'], "lessons_name" => $lesson -> lesson['name']));
   //Get results in lessons
   $userLessons = array();
   $result = eF_getTableData("users_to_lessons", "lessons_ID,completed,score", "users_LOGIN='".$this -> user['login']."'");
   foreach ($result as $value) {
    if ($userLessons[$value['lessons_ID']] = $value);
   }
   $lessonCourses = $lesson -> getCourses(true); //Get the courses that this lesson is part of. This way, we can auto complete a course, if it should be auto completed
   //Filter out courses that the student doesn't have
   $result = eF_getTableDataFlat("users_to_courses", "courses_ID", "users_LOGIN='".$this -> user['login']."'");
   $userCourses = $result['courses_ID'];
   foreach ($lessonCourses as $id => $course) {
    if (!in_array($id, $userCourses)) {
     unset($lessonCourses[$id]);
    }
   }
   //$userStatus = EfrontStats :: getUsersCourseStatus(array_keys($courses), $this -> user['login']);
   foreach ($lessonCourses as $course) {
    if ($course -> options['auto_complete']) {
     $constraints = array('archive' => false, 'active' => true, 'return_objects' => false);
     $courseLessons = $course -> getCourseLessons($constraints);
     $completed = $score = array();
     foreach ($courseLessons as $lessonId => $value) {
      $userLessons[$lessonId]['completed'] ? $completed[] = 1 : $completed[] = 0;
      $score[] = $userLessons[$lessonId]['score'];
     }
     if (array_sum($completed) == sizeof($completed)) { //If all the course's lessons are completed, then auto complete the course, using the mean lessons score
      $this -> completeCourse($course -> course['id'], round(array_sum($score) / sizeof($score)), _AUTOCOMPLETEDCOURSE);
     }
    }
   }
   $modules = eF_loadAllModules();
   foreach ($modules as $module) {
    $module -> onCompleteLesson($lesson -> lesson['id'],$this -> user['login']);
   }
   return true;
  } else {
   return false;
  }
 }
 /**

	 * Complete course

	 *

	 * This function is used to set the course status to completed for

	 * the current user. If the course is set to automatically issue a

	 * certificate, the certificate is issued.

	 * <br/>Example:

	 * <code>

	 * $user -> completeCourse(5, 87, 'Very good progress');									  //Complete course with id 5

	 * </code>

	 *

	 * @param Efrontmixed $course Either an EfrontCourse object or a course id

	 * @param int $score The course score

	 * @param string $comments Comments for the course completion

	 * @return boolean True if everything is ok

	 */
 public function completeCourse($course, $score, $comments) {
  if (!($course instanceof EfrontCourse)) {
   $course = new EfrontCourse($course);
  }
  if (in_array($course -> course['id'], array_keys($this -> getUserCourses()))) {
   $fields = array('completed' => 1,
       'to_timestamp' => time(),
       'score' => $score,
       'comments' => $comments);
   $where = "users_LOGIN = '".$this -> user['login']."' and courses_ID=".$course -> course['id'];
   EfrontCourse::persistCourseUsers($fields, $where, $course -> course['id'], $this -> user['login']);
   if ($course -> options['auto_certificate']) {
    $certificate = $course -> prepareCertificate($this -> user['login']);
    $course -> issueCertificate($this -> user['login'], $certificate);
   }
   EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_COMPLETION, "users_LOGIN" => $this -> user['login'], "lessons_ID" => $course -> course['id'], "lessons_name" => $course -> course['name']));
   // Assign the related course skills to the employee
   return $result;
  } else {
   return false;
  }
 }
 /**

	 * Set seen unit

	 *

	 * This function is used to set the designated unit as seen or not seen,

	 * according to $seen parameter. It also sets current unit to be the seen

	 * unit, if we are setting a unit as seen. Otherwise, the current unit is

	 * either leaved unchanged, or, if it matches the unset unit, it points

	 * to another seen unit.

	 * <br/>Example:

	 * <code>

	 * $user -> setSeenUnit(32, 2, true);						   //Set the unit with id 32 in lesson 2 as seen

	 * $user -> setSeenUnit(32, 2, false);						  //Set the unit with id 32 in lesson 2 as not seen

	 * </code>

	 * From version 3.5.2 and above, this function also sets the lesson as completed, if the conditions are met

	 *

	 * @param mixed $unit The unit to set status for, can be an id or an EfrontUnit object

	 * @param mixed $lesson The lesson that the unit belongs to, can be an id or an EfrontLesson object

	 * @param boolean $seen Whether to set the unit as seen or not

	 * @return boolean true if everything is ok

	 * @since 3.5.0

	 * @access public

	 */
 public function setSeenUnit($unit, $lesson, $seen) {
  if (isset($this -> coreAccess['content']) && $this -> coreAccess['content'] != 'change') { //If user type is not plain 'student' and is not set to 'change' mode, do nothing
   return true;
  }
  if ($unit instanceof EfrontUnit) { //Check validity of $unit
   $unit = $unit['id'];
  } elseif (!eF_checkParameter($unit, 'id')) {
   throw new EfrontContentException(_INVALIDID.": $unit", EfrontContentException :: INVALID_ID);
  }
  if ($lesson instanceof EfrontLesson) { //Check validity of $lesson
   $lesson = $lesson -> lesson['id'];
  } elseif (!eF_checkParameter($lesson, 'id')) {
   throw new EfrontLessonException(_INVALIDID.": $lesson", EfrontLessonException :: INVALID_ID);
  }
  $lessons = $this -> getLessons();
  if (!in_array($lesson, array_keys($lessons))) { //Check if the user is actually registered in this lesson
   throw new EfrontUserException(_USERDOESNOTHAVETHISLESSON.": ".$lesson, EfrontUserException :: USER_NOT_HAVE_LESSON);
  }
  $result = eF_getTableData("users_to_lessons", "done_content, current_unit", "users_LOGIN='".$this -> user['login']."' and lessons_ID=".$lesson);
  sizeof($result) > 0 ? $doneContent = unserialize($result[0]['done_content']) : $doneContent = array();
  $current_unit = 0;
  if ($seen) {
   $doneContent[$unit] = $unit;
   $current_unit = $unit;
  } else {
   unset($doneContent[$unit]);
   if ($unit == $result[0]['current_unit']) {
    sizeof($doneContent) ? $current_unit = end($doneContent) : $current_unit = 0;
   }
  }
  sizeof($doneContent) ? $doneContent = serialize($doneContent) : $doneContent = null;
  $result = eF_updateTableData("users_to_lessons", array('done_content' => $doneContent, 'current_unit' => $current_unit), "users_LOGIN='".$this -> user['login']."' and lessons_ID=".$lesson);
//		$cacheKey = "user_lesson_status:lesson:".$lesson."user:".$this -> user['login'];
//		Cache::resetCache($cacheKey);
  if ($current_unit) {
   EfrontEvent::triggerEvent(array("type" => EfrontEvent::CONTENT_COMPLETION, "users_LOGIN" => $this -> user['login'], "lessons_ID" => $lesson, "entity_ID" => $current_unit));
  }
  //Set the lesson as complete, if it can be.
  if ($seen) {
   $userProgress = EfrontStats :: getUsersLessonStatus($lesson, $this -> user['login']);
   $userProgress = $userProgress[$lesson][$this -> user['login']];
   if ($userProgress['lesson_passed'] && !$userProgress['completed']) {
    $lesson = new EfrontLesson($lesson);
    if ($lesson -> options['auto_complete']) {
     $userProgress['tests_avg_score'] ? $avgScore = $userProgress['tests_avg_score'] : $avgScore = 100;
     $timestamp = _AUTOCOMPLETEDAT.': '.date("Y/m/d, H:i:s");
     $this -> completeLesson($lesson, $avgScore, $timestamp);
    }
   }
  }
  return $result;
 }
 /**

	 * Get the next lesson in row, or in the course, if specified

	 * 

	 * @param EfrontLesson $lesson The lesson to account 

	 * @param mixed $course The course to regard, or false

	 * @return int The id of the next lesson in row

	 * @since 3.6.3

	 * @access public

	 */
 public function getNextLesson($lesson, $course = false) {
  $nextLesson = false;
  if ($course) {
   ($course instanceOf EfrontCourse) OR $course = new EfrontCourse($course);
   $eligibility = new ArrayIterator($course -> checkRules($_SESSION['s_login']));
   while ($eligibility -> valid() && ($key = $eligibility -> key()) != $lesson -> lesson['id']) {
    $eligibility -> next();
   }
   $eligibility -> next();
   if ($eligibility -> valid() && $eligibility -> key() && $eligibility -> current()) {
    $nextLesson = $eligibility -> key();
   }
  } else {
   $directionsTree = new EfrontDirectionsTree();
   $userLessons = new ArrayIterator($directionsTree -> getLessonsList($this -> getUserLessons()));
   while ($userLessons -> valid() && ($key = $userLessons -> current()) != $lesson -> lesson['id']) {
    $userLessons -> next();
   }
   $userLessons -> next();
   if ($userLessons -> valid() && $userLessons -> current()) {
    $nextLesson = $userLessons -> current();
   }
  }
  return $nextLesson;
 }
}
/**

 * User Factory class

 *

 * This clas is used as a factory for user objects

 * <br/>Example:

 * <code>

 * $user = EfrontUserFactory :: factory('jdoe');

 * </code>

 *

 * @package eFront

 * @version 3.5.0

 */
class EfrontUserFactory
{
 /**

	 * Construct user object

	 *

	 * This function is used to construct a user object, based on the user type.

	 * Specifically, it creates an EfrontStudent, EfrontProfessor, EfrontAdministrator etc

	 * An optional password verification may take place, if $password is specified

	 * If $user is a login name, the function queries database. Alternatively, it may

	 * use a prepared user array, which is mostly convenient when having to perform

	 * multiple initializations

	 * <br/>Example :

	 * <code>

	 * $user = EfrontUserFactory :: factory('jdoe');			//Use factory function to instantiate user object with login 'jdoe'

	 * $userData = eF_getTableData("users", "*", "login='jdoe'");

	 * $user = EfrontUserFactory :: factory($userData[0]);	  //Use factory function to instantiate user object using prepared data

	 * </code>

	 *

	 * @param mixed $user A user login or an array holding user data

	 * @param string $password An optional password to check against

	 * @param string $forceType Force the type to initialize the user, for example for when a professor accesses student.php as student

	 * @return EfrontUser an object of a class extending EfrontUser

	 * @since 3.5.0

	 * @access public

	 * @static

	 */
 public static function factory($user, $password = false, $forceType = false) {
  if (is_string($user) && eF_checkParameter($user, 'login')) {
   $result = eF_getTableData("users", "*", "login='".$user."'");
   if (sizeof($result) == 0) {
    throw new EfrontUserException(_USERDOESNOTEXIST.': '.$user, EfrontUserException :: USER_NOT_EXISTS);
   } else if ($password !== false && $password != $result[0]['password']) {
    throw new EfrontUserException(_INVALIDPASSWORDFORUSER.': '.$user, EfrontUserException :: INVALID_PASSWORD);
   }
   $user = $result[0];
  } elseif (!is_array($user)) {
   throw new EfrontUserException(_INVALIDLOGIN.': '.$user, EfrontUserException :: INVALID_PARAMETER);
  }
  $forceType ? $userType = $forceType : $userType = $user['user_type'];
  switch ($userType) {
   case 'administrator' : $factory = new EfrontAdministrator($user, $password); break;
   case 'professor' : $factory = new EfrontProfessor($user, $password); break;
   case 'student' : $factory = new EfrontStudent($user, $password); break;
   default: throw new EfrontUserException(_INVALIDUSERTYPE.': "'.$userType.'"', EfrontUserException :: INVALID_TYPE); break;
  }
  return $factory;
 }
}
?>
