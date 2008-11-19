<?php
/**
 * EfrontUser Class file
 *
 * @package eFront
 * @version 3.5.0
 */

class EfrontUserException extends Exception
{
    const NO_ERROR          = 0;
    const INVALID_LOGIN     = 401;
    const USER_NOT_EXISTS   = 402;
    const INVALID_PARAMETER = 403;
    const USER_EXISTS       = 404;
    const DATABASE_ERROR    = 405;
    const USER_FILESYSTEM_ERROR = 406;
    const INVALID_TYPE      = 407;
    const ALREADY_IN        = 408;
    const INVALID_PASSWORD  = 409;
    const USER_NOT_HAVE_LESSON = 410;
    const WRONG_INPUT_TYPE  = 411;
    const USER_PENDING      = 412;
    const TYPE_NOT_EXISTS   = 414;
    const MAXIMUM_REACHED   = 415;
    const RESTRICTED_USER_TYPE = 416;
    const GENERAL_ERROR     = 499;
}


/**
 * Abstract class for users
 *
 *
 */
abstract class EfrontUser
{
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
    public $groups = false;

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
     * Instantiate class
     *
     * This function instantiates a new EfrontUser sibling object based on the given
     * login. If $password is set, then it verifies the given password against
     * the stored one. Either the EfrontUserFactory may be used, or directly the
     * EfrontX class.
     * <br/>Example:
     * <code>
     * $user = EfrontUserFactory :: factory('jdoe');            //Use factory to instantiate user object with login 'jdoe'
     * $user = EfrontUserFactory :: factory('jdoe', 'mypass');  //Use factory to instantiate user object with login 'jdoe' and perform password verification
     * $user = new EfrontAdministrator('jdoe')                  //Instantiate administrator user object with login 'jdoe'
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
        } else if ($password !== false && $password != $user[0]['password']) {
            throw new EfrontUserException(_INVALIDPASSWORD.': '.$user, EfrontUserException :: INVALID_PASSWORD);
        }

        $this -> user  = $user;
        $this -> login = $user['login'];

        $this -> user['directory'] = G_UPLOADPATH.$this -> user['login'];
        if (!is_dir($this -> user['directory'])) {
            mkdir($this -> user['directory'], 0755);
        }
        $this -> user['password'] == 'ldap' ? $this -> isLdapUser = true : $this -> isLdapUser = false;
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
     * @return array with new user settings if the new user was successfully created
     * @since 3.5.0
     * @access public
     */
    public static function createUser($userProperties) {
        $users = eF_getTableDataFlat("users", "*");
        $versionDetails = eF_checkVersionKey($GLOBALS['configuration']['version_key']);

        if (isset($versionDetails) && sizeof($users['login']) > $versionDetails['users']) {
            throw new EfrontUserException(_MAXIMUMUSERSNUMBERREACHED.': '.$userProperties['login'], EfrontUserException :: MAXIMUM_REACHED);
        }
        if (!isset($userProperties['login']) || !eF_checkParameter($userProperties['login'], 'login')) {
            throw new EfrontUserException(_INVALIDLOGIN.': '.$userProperties['login'], EfrontUserException :: INVALID_LOGIN);
        }
        if (in_array($userProperties['login'], $users['login']) > 0) {
            throw new EfrontUserException(_USERALREADYEXISTS.': '.$userProperties['login'], EfrontUserException :: USER_EXISTS);
        }
        if ($userProperties['email'] && !eF_checkParameter($userProperties['email'], 'email')) {
            throw new EfrontUserException(_INVALIDEMAIL.': '.$userProperties['email'], EfrontUserException :: INVALID_PARAMETER);
        }
        if (!MODULE_HCD_INTERFACE) {
            if (in_array($userProperties['email'], $users['email']) > 0) {
                throw new EfrontUserException(_EMAILALREADYEXISTS.': '.$userProperties['email'], EfrontUserException :: USER_EXISTS);
            }
        }
        if (!isset($userProperties['name']) || !eF_checkParameter($userProperties['name'], 'text')) {
            throw new EfrontUserException(_INVALIDNAME.': '.$userProperties['name'], EfrontUserException :: INVALID_PARAMETER);
        }
        if (!isset($userProperties['surname']) || !eF_checkParameter($userProperties['surname'], 'text')) {
            throw new EfrontUserException(_INVALIDSURNAME.': '.$userProperties['login'], EfrontUserException :: INVALID_PARAMETER);
        }
        !isset($userProperties['user_type'])      ? $userProperties['user_type']      = 'student'                                     : null;                                          //If a user type is not specified, by default make the new user student
        !isset($userProperties['password'])		  ? $passwordNonTransformed			  = $userProperties['password'] : $passwordNonTransformed = $userProperties['login']; 
        if ($userProperties['password'] != 'ldap') {
			!isset($userProperties['password'])       ? $userProperties['password']       = md5($userProperties['login'].G_MD5KEY)        : $userProperties['password'] = md5($userProperties['password'].G_MD5KEY); 	
		} 
		//!isset($userProperties['password'])       ? $userProperties['password']       = md5($userProperties['login'].G_MD5KEY)        : $userProperties['password'] = md5($userProperties['password'].G_MD5KEY);        //If password is not specified, use login instead
        
        !isset($userProperties['languages_NAME']) ? $userProperties['languages_NAME'] = $GLOBALS['configuration']['default_language'] : null;                                          //If language is not specified, use default language
        !isset($userProperties['active'])         ? $userProperties['active']         = 0                                             : null;                                           // 0 means inactive, 1 means active
        !isset($userProperties['active'])         ? $userProperties['pending']        = 0                                             : null;                                           // 0 means not pending, 1 means pending
        !isset($userProperties['timestamp'])      ? $userProperties['timestamp']      = time()                                        : null;
        !isset($userProperties['user_types_ID'])  ? $userProperties['user_types_ID']  = 0                                             : null;
        
        if (eF_insertTableData("users", $userProperties)) {

            $user_dir = G_UPLOADPATH.$userProperties['login'].'/';
            if (is_dir($user_dir)) {                                                //If the directory already exists, delete it first
                try {
                    $directory = new EfrontDirectory($user_dir);
                    $directory -> delete();
                } catch (EfrontFileException $e) {}                                    //Don't stop on filesystem errors
            }
            if (mkdir($user_dir, 0755) || is_dir($user_dir)) {                            //Now, the directory either gets created, or already exists (in case errors happened above). In both cases, we continue
                //Create personal messages attachments folders
                mkdir($user_dir.'message_attachments/', 0755);
                mkdir($user_dir.'message_attachments/Incoming/', 0755);
                mkdir($user_dir.'message_attachments/Sent/', 0755);
                mkdir($user_dir.'message_attachments/Drafts/', 0755);
                mkdir($user_dir.'avatars/', 0755);

                //Create database representations for personal messages folders (it has nothing to do with filsystem database representation)
                eF_insertTableData("f_folders", array('name' => 'Incoming', 'users_LOGIN' => $userProperties['login']));
                eF_insertTableData("f_folders", array('name' => 'Sent',     'users_LOGIN' => $userProperties['login']));
                eF_insertTableData("f_folders", array('name' => 'Drafts',   'users_LOGIN' => $userProperties['login']));

                // Assign to the new user all skillgap tests that should be automatically assigned to every new student
                if ($userProperties['user_type'] == 'student') {
                    $tests = EfrontTest :: getAutoAssignedTests();
                    foreach ($tests as $test) {
                        eF_insertTableData("users_to_skillgap_tests", array("users_LOGIN" => $userProperties['login'], "tests_ID" => $test['id']));
                    }
                }
                $newUser = EfrontUserFactory :: factory($userProperties['login']);
                $newUser -> user['password'] = $passwordNonTransformed;
                
				global $currentUser;  // this is for running eF_loadAllModules ..needs to go somewhere else
                if (!$currentUser) {
            		$currentUser = $newUser;
				}
                ///MODULES1 - Module user add events
                // Get all modules (NOT only the ones that have to do with the user type)
                $modules = eF_loadAllModules();
                // Trigger all necessary events. If the function has not been re-defined in the derived module class, nothing will happen
                foreach ($modules as $module) {
                    $module -> onNewUser($userProperties['login']);
                }

                return $newUser;
            } else {
                eF_deleteTableData("users", "login='".$userProperties['login']."'");    //Delete the created user, so that nothing happened
                throw new EfrontUserException(_COULDNOTCREATEUSERDIRECTORY.': '.$userdir, EfrontUserException :: USER_FILESYSTEM_ERROR);    //The directory could not be created after all...
            }


        } else {
            throw new EfrontUserException(_COULDNOTINSERTUSER.': '.$userProperties['login'], EfrontUserException :: DATABASE_ERROR);
        }
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
                    $users[] = $value['LOGIN'];
                }
            } else{
                $users[] = $value['LOGIN'];
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
     *      $user = EfrontUserFactory :: factory('admin');
     *      echo $user -> getType();            //Returns 'administrator'
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
        $password_encrypted = md5($password.G_MD5KEY);
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
     * echo $user -> getPassword();             //echos something like '36f49e43c662986b838258ab099d0d5a'
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
     * $user -> setLoginType('ldap');               //Set login type to 'ldap'
     * $user -> setLoginType('normal', 'testpass'); //Set login type to 'normal' using password 'testpass'
     * $user -> setLoginType();                     //Set login type to 'normal' and use default password (the user's login)
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
            !$password ? $password = md5($this -> user['login'].G_MD5KEY) : null;                            //If a password is not specified, use the user's login name
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
     * $user -> getLoginType();                     //Returns either 'normal' or 'ldap'
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
        if (eF_updateTableData("users", array("active" => 1, "pending" => 0), "login = '".$this -> user['login']."'")) {
            $this -> user['active'] = 1;
            return true;
        } else {
            return false;
        }
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
        if (eF_updateTableData("users", array("active" => 0), "login = '".$this -> user['login']."'")) {
            $this -> user['active'] = 0;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set avatar image
     *
     * This function is used to set the user's avatar image.
     * <br/>Example:
     * <code>
     * $file = new EfrontFile(32);                                             //This is a file uploaded -for example- in the filesystem.
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
        if ($destroySession) {
            if ($this -> user['login'] == $_SESSION['s_login']) {                        //Is the current user beeing logged out? If so, destroy the session.
                $_SESSION = array();
                isset($_COOKIE[session_name()]) ? setcookie(session_name(), '', time()-42000, '/') : null;
                session_destroy();
                setcookie ("cookie_login", "", time() - 3600);
                setcookie ("cookie_password", "", time() - 3600);
                unset($_COOKIE['cookie_login']);                        //These 2 lines are necessary, so that index.php does not think they are set
                unset($_COOKIE['cookie_password']);
            } else {
                $session_path = ini_get('session.save_path');
                $session_name = eF_getTableData('logs', 'comments', 'users_LOGIN="'.$this -> user['login'].'" AND action = "login"', 'timestamp desc limit 1');
                unlink($session_path.'/sess_'.$session_name[0]['comments']);
            }
        }
        eF_deleteTableData("users_to_chatrooms", "users_LOGIN='".$this -> user['login']."'");                                           //Log out user from the chat
        eF_deleteTableData("chatrooms", "users_LOGIN='".$this -> user['login']."' and type='one_to_one'");                              //Delete any one-to-one conversations
        eF_deleteTableData("users_online", "users_LOGIN='".$this -> user['login']."'");

        $result = eF_getTableData("logs", "action", "users_LOGIN = '".$this -> user['login']."'", "timestamp desc limit 1");          //?? ??? ????? ???????? ???, ????? ??? logs ??? ????? logout, ???? ?? ????? logout ??? ??? ??? ?? ???????

        if ($result[0]['action'] != 'logout') {
            $fields_insert = array('users_LOGIN' => $this -> user['login'],
                                   'timestamp'   => time(),
                                   'action'      => 'logout',
                                   'comments'    => 0,
                                   'session_ip'  => eF_encodeIP($_SERVER['REMOTE_ADDR']));
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
        if ($this -> user['pending']) {
            throw new EfrontUserException(_USERPENDING, EfrontUserException :: USER_PENDING);
        }
        if ($this -> isLdapUser) {                                    //Authenticate LDAP user
            if (!eF_checkUserLdap($this -> user['login'], $password)) {
                throw new EfrontUserException(_INVALIDPASSWORD, EfrontUserException :: INVALID_PASSWORD);
            }
        } else {                                                      //Authenticate normal user
            if (!$encrypted) {
                $password = md5($password.G_MD5KEY);
            }
            if ($password != $this -> user['password']) {
                throw new EfrontUserException(_INVALIDPASSWORD, EfrontUserException :: INVALID_PASSWORD);
            }
        }

        if ($this -> isLoggedIn()) {                        //If the user is already logged in, log him out
            $this -> logout(false);
        } else if ($_SESSION['s_login']) {
            try {
                $user = EfrontUserFactory :: factory($_SESSION['s_login']);
                $user -> logout(false);
            } catch (Exception $e) {}
        }
        $_SESSION['s_lessons_ID'] = '';    //Here, we should reset all session values, except for cart contents
/*
        //Destroy previous session data
        if (session_id()) {
            $_SESSION = array();
            //isset($_COOKIE[session_name()]) ? setcookie(session_name(), '', time()-42000, '/') : null;
            session_destroy();
        }
*/
        //Create new session
        //session_cache_limiter('none');
        //session_start();

        //if user language is deactivated or deleted, login user with system default language
        $result = eF_getTableData("languages", "name", "name='".$this -> user['languages_NAME']."' and active=1");
        if ($result[0]['name'] == $this -> user['languages_NAME']){
            $login_language = $this -> user['languages_NAME'];
        }else {
            $login_language = $GLOBALS['configuration']['default_language'];
        }
        //Assign session variables
        $_SESSION['s_login']    = $this -> user['login'];
        $_SESSION['s_password'] = $this -> user['password'];
        $_SESSION['s_type']     = $this -> user['user_type'];
        $_SESSION['s_language'] = $login_language;

        //Insert log entry
        $fields_insert = array('users_LOGIN' => $this -> user['login'],
                               'timestamp'   => time(),
                               'action'      => 'login',
                               'comments'    => session_id(),
                               'session_ip'  => eF_encodeIP($_SERVER['REMOTE_ADDR']));
        eF_insertTableData("logs", $fields_insert);

        $fields = array('users_LOGIN'   => $this -> user['login'],
                        'timestamp'     => time(),
                        'timestamp_now' => time(),
                        'session_ip'    => $_SERVER['REMOTE_ADDR']);
        eF_insertTableData("users_online", $fields);


        return true;
    }

    /**
     * Check if the user is already logged in and update his timestamp
     *
     * This function examines the system database to decide whether the user is still logged in and updates current time
     * <br/>Example:
     * <code>
     * $user = EfrontUserFactory :: factory('jdoe');
     * $user -> refreshLoggin();                               //Returns true if the user is logged in
     * </code>
     *
     * @return boolean True if the user is logged in
     * @since 3.5.0
     * @access public
     */
    public function refreshLoggin() {
        $result = eF_getTableData('users_online', '*', "users_LOGIN='".$this -> user['login']."'");
        if (sizeof($result) > 0) {
            eF_updateTableData("users_online",     array("timestamp_now" => time()), "users_LOGIN='".$this -> user['login']."'");
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the list of users that are currently online
     *
     * This function is used to get a list of the users that are currently online
     * <br>Example:
     * <code>
     * $online = EfrontUser :: getUsersOnline();
     * </code>
     *
     * @param boolean $userType Return only users of the basic type $user_type
     * @param int $interval The idle interval above which a user is not considered logged in anymore
     * @return array The list of online users
     * @since 3.5.0
     * @access public
     */
    public static function getUsersOnline($userType = false, $interval = 600) {
        $usersOnline = array();
        if (!in_array($userType, self :: $basicUserTypes)) {
            $userType = false;
        }

        $result  = eF_getTableData("users_online, users", "*", "users.login = users_online.users_LOGIN ".($userType ? "and users.user_type='".$userType."'" : null), "", "users_online.users_LOGIN, users_online.timestamp_now desc");

        foreach ($result as $value) {
            if (time() - $value['timestamp_now'] < $interval) {
                $usersOnline[] = array('login' => $value['users_LOGIN'], 'type' => $value['user_type'], 'time' => time() - $value['timestamp'], 'name' => $value['name'], 'surname' => $value['surname']);
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
     * $user -> isLoggedIn();                               //Returns true if the user is logged in
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
            $message = _USERDIRECTORYCOULDNOTBEDELETED.': '.$e -> getMessage().' ('.$e -> getCode().')';    //This does nothing at the moment
        }

        foreach ($this -> aspects as $aspect) {
            $aspect -> delete();
        }

        eF_updateTableData("f_forums",     array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
        eF_updateTableData("f_messages",   array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
        eF_updateTableData("f_topics",     array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
        eF_updateTableData("f_poll",       array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
        eF_updateTableData("chatrooms",    array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
        eF_updateTableData("chatmessages", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
        eF_updateTableData("calendar",     array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
        eF_updateTableData("news",         array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");
        eF_updateTableData("files",        array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");


        eF_deleteTableData("users", "login='".$this -> user['login']."'");

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
     * $user -> surname = 'doe';                            //Change object's surname
     * $user -> persist();                                  //Persist changed value
     * </code>
     *
     * @since 3.5.0
     * @access public
     */
    public function persist() {
        $fields = array('password'       => $this -> user['password'],
                        'email'          => $this -> user['email'],
                        'languages_NAME' => $this -> user['languages_NAME'],
                        'name'           => $this -> user['name'],
                        'surname'        => $this -> user['surname'],
                        'active'         => $this -> user['active'],
                        'comments'       => $this -> user['comments'],
                        'user_type'      => $this -> user['user_type'],
                        'timestamp'      => $this -> user['timestamp'],
                        'avatar'         => $this -> user['avatar'],
                        'pending'        => $this -> user['pending'],
                        'user_types_ID'  => $this -> user['user_types_ID']);
        eF_updateTableData("users", $fields, "login='".$this -> user['login']."'");
    }



    /**
     * Get the user groups list
     *
     * <br/>Example:
     * <code>
     * $groupsList    = $user -> getGroups();                         //Returns an array with pairs [groups id] => [employee specification for this group]
     * </code>
     * If $returnObjects is specified, then each group in the groups array will
     * contain an additional field holding information on the user's group status
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
     * $user -> addGroups(23);                         //Add a single group with id 23
     * $user -> addGroups(array(23,24,25));            //Add multiple groups using an array
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
        if (MODULE_HCD_INTERFACE) {
            global $currentUser;
            $result = eF_getTableData("groups", "id, name", "");
            $group_names = array();
            foreach ($result as $res) {
                $id = $res['id'];
                $group_names[$id] = $res['name'];
            }
        }

        foreach ($groupIds as $key => $groupId) {
            if (eF_checkParameter($groupId, 'id')) {

                // Check if the group is already assigned, if so, complement the existing specification
                if (!isset($this -> groups[$groupId])) {

                    if ($ok = eF_insertTableData("users_to_groups", array("users_LOGIN" => $this -> login, "groups_ID" => $groupId))) {

                        // Register group assignment into the event log - event log only available in HCD
                        if (MODULE_HCD_INTERFACE) {
                            eF_insertTableData("module_hcd_events", array("event_code"    => 5,
                                                                          "users_login"   => $this -> login,
                                                                          "author"        => $currentUser -> login,
                                                                          "specification" => _EMPLOYEEWASASSIGNEDGROUP .' "' . $group_names[$groupId]. '"',
                                                                          "timestamp"     => time()));
                        }

                        $this -> groups[$groupId] = $groupId;
                    } else {
                        throw new EfrontUserException(_OPERATIONWASNOTCOMPLETEDSUCCESFULLY.': '.$ok, EfrontUserException :: DATABASE_ERROR);
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
     * $employee -> removeGroups(23);                          //Remove a signle group with id 23
     * $employee -> removeGroups(array(23,24,25));             //Remove multiple groups using an array
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
                unset($groupIds[$key]);                                        //Remove illegal vaues from groups array.
            }
        }

        eF_deleteTableData("users_to_groups", "users_login = '".$this -> login."' and groups_ID in (".implode(",", $groupIds).")");    //delete groups from list

        // Register group assignment into the event log - event log only available in HCD
        if (MODULE_HCD_INTERFACE) {
            global $currentUser;
            $result = eF_getTableData("groups", "id, name", "");
            $group_names = array();
            foreach ($result as $res) {
                $id = $res['id'];
                $group_names[$id] = $res['name'];
            }
            foreach ($this -> groups as $key => $groupId) {
                $id = $groupId['groups_ID'];
                if (in_array($id, $groupIds)) {
                    eF_insertTableData("module_hcd_events", array("event_code"    => 5,
                                                              "users_login"   => $this -> login,
                                                              "author"        => $currentUser -> login,
                                                              "specification" => _EMPLOYEEWASRELEASEDFROMGROUP . ' "' .$group_names[$id]. '"',
                                                              "timestamp"     => time()));

                    unset($this -> groups[$key]);                                        //Remove groups from cache array."
                }
            }
        }

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
        $modules   = array();
        $user_type = $this -> getType();

        // Get all modules enabled for this user type
        foreach ($modulesDB as $module) {
            $folder = $module['position'];
            $className = $module['className'];

            // If a module is to be updated then its class should not be loaded now
            if (!($this -> getType() == "administrator" && $_GET['ctg'] == "control_panel" && $_GET['op'] == "modules" && $_GET['upgrade'] == $className)) {

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
                        $message      = '"'.$className .'" '. _MODULECLASSNOTEXISTSIN . ' ' .G_MODULESPATH.$folder.'/'.$className.'.class.php';
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
     *      $interval['from'] = "00000000";
     *      $interval['to']   = time();
     *      $time  = EfrontUser :: getLoginTime('jdoe', $interval); //$time['jdoe'] now holds his times
     *      $times = EfrontUser :: getLoginTime($interval); //$times now holds an array of times for all users
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
            $to   = $interval['to'];
        } else {
            $from = "00000000";
            $to   = time();
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
            $start     = 0;
            $inlogin   = 0;
            foreach ($result as $value) {
                if ($inlogin) {
                    if ($value['action'] != 'logout' && $value['action'] != 'login'){
                        $totalTime += $value['timestamp'] - $start;
                        $start      = $value['timestamp'];
                    } else if ($value['action'] == 'logout') {
                        if ($value['timestamp'] < ($start + 3600)) {  //if it is logged in more than an hour, we don't consider it
                            $totalTime += $value['timestamp'] - $start;
                        }
                        $inlogin = 0;
                    } else if ($value['action'] == 'login') {
                        $inlogin = 1;
                        $start   = $value['timestamp'];
                    }
                } else {
                    if ($value['action'] == 'login') {
                        $inlogin = 1;
                        $start   = $value['timestamp'];
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
     * Apply role options to object
     *
     * This function is used to apply role options, using the specified role
     * <br/>Example:
     * <code>
     * $user = EfrontUserFactory :: factory('jdoe');
     * $user -> applyRoleOptions(4);                        //Apply the role options for user type with id 4 to the $user object
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
            $result = eF_getTableData("user_types", "*", "id=".$role);
            unserialize($result[0]['core_access'])    ? $this -> coreAccess    = unserialize($result[0]['core_access'])    : null;
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
        $roles      = eF_getTableDataFlat("user_types", "*", "active=1");    //Get available roles
        if (sizeof($roles) > 0) {
            $getNames ? $roles = self :: $basicUserTypesTranslations + array_combine($roles['id'], $roles['name']) : $roles = array_combine(self :: $basicUserTypes, self :: $basicUserTypes) + array_combine($roles['id'], $roles['basic_user_type']);
        } else {
            $getNames ? $roles = self :: $basicUserTypesTranslations : $roles = array_combine(self :: $basicUserTypes, self :: $basicUserTypes);
        }

        return $roles;
    }

}

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
     * $info = $user -> getInformation();         //Get lesson information
     * </code>
     *
     * @param string $user The user login to customize lesson information for
     * @return array The user information
     * @since 3.5.0
     * @access public
     */
    public function getInformation() {
        $languages   = EfrontSystem :: getLanguages(true);
        $info        = array();
        $info['login']             = $this -> user['login'];
        $info['name']              = $this -> user['name'];
        $info['surname']           = $this -> user['surname'];
        $info['fullname']          = $this -> user['name'] . " " . $this -> user['surname'];
        $info['user_type']         = $this -> user['user_type'];
        $info['user_types_ID']     = $this -> user['user_types_ID'];
        $info['student_lessons']   = array();
        $info['professor_lessons'] = array();
        $info['total_lessons']     = 0;
        $info['total_login_time']  = self :: getLoginTime($this -> user['login']);
        $info['language']          = $languages[$this -> user['languages_NAME']];
        $info['active']            = $this -> user['active'];
        $info['active_str']        = $this -> user['active'] ? _YES : _NO;
        $info['joined']            = $this -> user['timestamp'];
        $info['joined_str']        = formatTimestamp($this -> user['timestamp'], 'time');
        $info['avatar']            = $this -> user['avatar'];

        return $info;
    }

    public function getRole() {
        return "administrator";
    }
}


abstract class EfrontLessonUser extends EfrontUser
{
    /**
     * The user lessons array.
     *
     * @since 3.5.0
     * @var array
     * @access public
     */
    public $lessons = array();


    /**
     * Assign lessons to user.
     *
     * This function can be used to assign a lesson to the current user. If $userTypes
     * is specified, then the user is assigned to the lesson using the specified type.
     * By default, the user basic type is used.
     * <br/>Example:
     * <code>
     * $user = EfrontUserFactory :: factory('jdoe');
     * $user -> addLessons(23);                         //Add a signle lesson with id 23
     * $user -> addLessons(23, 'professor');            //Add a signle lesson with id 23 and set the user type to 'professor'
     * $user -> addLessons(array(23,24,25));            //Add multiple lessons using an array
     * $user -> addLessons(array(23,24,25), array('professor', 'student', 'professor'));            //Add multiple lessons using an array for lesson ids and another for corresponding user types
     * </code>
     *
     * @param mixed $lessonIds Either a single lesson id, or an array if ids
     * @param mixed $userTypes The corresponding user types for the specified lessons
     * @param boolean $activeate Lessons will be set as active or not
     * @return int The array of lesson ids or false if the lesson already exists.
     * @since 3.5.0
     * @access public
     */
    public function addLessons($lessonIds, $userTypes, $activate = 0) {
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
            $lesson -> addUsers($this -> user['login'], $userTypes[$key]);        
        }
        
        $this -> lessons = false;    //Reset lessons information
        
        return $this -> getLessons();
    }

    /**
     * Remove lessons from user.
     *
     * This function can be used to remove a lesson from the current user.
     * <br/>Example:
     * <code>
     * $user = EfrontUserFactory :: factory('jdoe');
     * $user -> removeLessons(23);                          //Remove a signle lesson with id 23
     * $user -> removeLessons(array(23,24,25));             //Remove multiple lessons using an array
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
                unset($lessonIds[$key]);                                        //Remove illegal vaues from lessons array.
            }
        }

        eF_deleteTableData("users_to_lessons", "users_LOGIN = '".$this -> user['login']."' and lessons_ID in (".implode(",", $lessonIds).")");    //delete lessons from list
        $userLessons = eF_getTableDataFlat("users_to_lessons", "lessons_ID, user_type", "users_LOGIN = '".$this -> user['login']."'");
        $this -> lessons = array_combine($userLessons['lessons_ID'], $userLessons['user_type']);

        return $this -> lessons;
    }

    /**
     * Get the users's lessons list
     *
     * This function is used to get a list of ids with the users's lessons.
     * If $returnObjects is set and true, then An array of lesson objects is returned
     * The list is returned using the object's cache (unless $returnObjects is true).
     * <br/>Example:
     * <code>
     * $lessonsList    = $user -> getLessons();                         //Returns an array with pairs [lessons id] => [user type]
     * $lessonsObjects = $user -> getLessons(true);                     //Returns an array of lesson objects
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
        if (sizeof($this -> lessons) > 0 && !$returnObjects) {
            $userLessons = $this -> lessons;
        } else {
            if ($returnObjects) {
                $userLessons = array();
                //Assign all lessons to an array, this way avoiding looping queries
                $result     = eF_getTableData("lessons l, users_to_lessons ul", "l.*", "l.id=ul.lessons_ID and ul.users_LOGIN = '".$this -> user['login']."'");
                foreach ($result as $value) {
                    $lessons[$value['id']] = $value;
                }
                $courseLessons    = array();
                $nonCourseLessons = array();
                $result      = eF_getTableData("users u,users_to_lessons ul", "ul.*, u.user_type as basic_user_type, u.user_types_ID", "ul.users_LOGIN = u.login and ul.users_LOGIN = '".$this -> user['login']."' and ul.lessons_ID != 0");

                foreach ($result as $value) {
                    try {
                        $lesson = new EfrontLesson($lessons[$value['lessons_ID']]);
                        $lesson -> userStatus = $value;
                        if ($lesson -> lesson['course_only']) {
                            $courseLessons[$value['lessons_ID']] = $lesson;
                        } else {
                            $nonCourseLessons[$value['lessons_ID']] = $lesson;
                        }
                    } catch (Exception $e) {}    //Do nothing in case of exception, simply do not take into account this lesson
                }
                $userLessons = $courseLessons + $nonCourseLessons;
            } else {
                $result = eF_getTableDataFlat("users_to_lessons", "lessons_ID, user_type", "users_LOGIN = '".$this -> user['login']."'");
                $this -> lessons = array_combine($result['lessons_ID'], $result['user_type']);
                foreach ($this -> lessons as $lessonId => &$userType) {
                    if (!$userType) {                                                    //For some reason, the user type is not set in the database. so set it now
                        $userType = $this -> user['user_type'];
                        eF_updateTableData("users_to_lessons", array("user_type" => $userType), "lessons_ID=$lessonId and users_LOGIN='".$this -> user['login']."'");
                    }
                }
                unset($userType);
                $userLessons = $this -> lessons;
            }
        }

        if ($basicType) {
            $roles = EfrontLessonUser :: getLessonsRoles();
            foreach ($userLessons as $id => $role) {
                if ($role instanceof EfrontLesson) {                                //$returnObjects is true
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

    /**
     * Get user potential lessons
     *
     * This function returns a list with the lessons that the user
     * may take, but doesn't have. The list may be either a list of ids
     * (faster) or a list of EfrontLesson objects.
     * <br/>Example:
     * <code>
     * $user -> getNonLessons();            //Returns a list with potential lessons ids
     * $user -> getNonLessons(true);        //Returns a list of EfrontLesson objects
     * </code>
     *
     * @param boolean $returnObjects Whether to return a list of objects
     * @return array The list of ids or objects
     * @since 3.5.0
     * @access public
     */
    public function getNonLessons($returnObjects = false) {
        $userLessons = eF_getTableDataFlat("users_to_lessons", "lessons_ID", "users_LOGIN = '".$this -> user['login']."'");
        sizeof($userLessons) > 0 ? $sql = "and id not in (".implode(",", $userLessons['lessons_ID']).")" : $sql = '';

        if ($returnObjects) {
            $nonUserLessons = array();
            $lessons        = eF_getTableData("lessons", "*", "languages_NAME='".$this -> user['languages_NAME']."'".$sql);
            foreach ($lessons as $value) {
                $nonUserLessons[$value['id']]  = new EfrontLesson($value['id']);
            }
            return $nonUserLessons;
        } else {
            $lessons = eF_getTableDataFlat("lessons", "*", "languages_NAME='".$this -> user['languages_NAME']."'".$sql);
            return $lessons['id'];
        }
    }

    /**
     * Get user courses
     *
     * This function gets the courses that the user has enrolled to,
     * along with his declared type. If $returnObjects is set, then
     * a list of objects is returned. Otherwise return value is an array
     * containing only id/user_type pairs
     * <br/>Example:
     * <code>
     * $user -> getCourses();                           //Get an array where keys are course ids and values are user type
     * $user -> getCourses(true);                       //Return an array of EfrontCourse objects
     * </code>
     * If $returnObjects is true, then each EfrontCourse object will contain
     * an additional field named "userStatus" holding user-specific information, such
     * as his type
     *
     * @param boolean $returnObjects If true, then return list of objects
     * @param string $basicType If set, then return only lessons that the user has the specific basic role in them
     * @return array An array with the user courses, where either keys are course ids and values are user types or it contains EfrontCourse objects
     * @since 3.5.0
     * @access public
     */
    public function getCourses($returnObjects = false, $basicType = false) {
        if ($returnObjects) {
            $userCourses = array();
            $result     = eF_getTableData("users_to_courses", "courses_ID, user_type", "users_LOGIN='".$this -> user['login']."'");
            $userStatus = EfrontStats :: getUsersCourseStatus(false, $this -> user['login']);
            foreach ($result as $value) {
                try {
                    $userCourses[$value['courses_ID']]  = new EfrontCourse($value['courses_ID']);
                    $userCourses[$value['courses_ID']] -> userStatus = $userStatus[$value['courses_ID']][$this -> user['login']];
                } catch (Exception $e) {}    //Do nothing in case of exception, simply do not take into account this course
            }
            return $userCourses;
        } else {
            $userCourses = eF_getTableDataFlat("users_to_courses", "courses_ID, user_type", "users_LOGIN='".$this -> user['login']."'");
            return array_combine($userCourses['courses_ID'], $userCourses['user_type']);
        }

        if ($basicType) {
            $roles = EfrontLessonUser :: getLessonsRoles();
            foreach ($userCourses as $id => $role) {
                if ($role instanceof EfrontCourse) {                                //$returnObjects is true
                    if ($roles[$role -> userStatus['user_type']] != $basicType) {
                        unset($userCourses[$id]);
                    }
                } else {
                    if ($roles[$role] != $basicType) {
                        unset($userCourses[$id]);
                    }
                }
            }
        }        
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
     * $user -> addCourses(23);                         //Add a signle course with id 23
     * $user -> addCourses(23, 'professor');            //Add a signle course with id 23 and set the user type to 'professor'
     * $user -> addCourses(array(23,24,25));            //Add multiple courses using an array
     * $user -> addCourses(array(23,24,25), array('professor', 'student', 'professor'));            //Add multiple courses using an array for course ids and another for corresponding user types
     * </code>
     *
     * @param mixed $courseIds Either a single course id, or an array if ids
     * @param mixed $userTypes The corresponding user types for the specified courses
     * @param boolean $activeate Courses will be set as active or not
     * @return int The array of course ids or false if the course already exists.
     * @since 3.5.0
     * @access public
     * @todo auto_projects
     */
    public function addCourses($courseIds, $userTypes, $activate = 0) {
        $courses = $this -> getCourses();

        if (!is_array($courseIds)) {
            $courseIds = array($courseIds);
        }
        if (!is_array($userTypes)) {
            $userTypes = array($userTypes);
        }

        $roles = EfrontLessonUser :: getLessonsRoles();

        foreach ($courseIds as $key => $courseID) {

            if (eF_checkParameter($courseID, 'id')) {
                // If the course id does not exist then insert it
                if (!in_array($courseID, array_keys($courses))) {
                    isset($userTypes[$key]) && in_array($userTypes[$key], array_keys($roles)) ? $userType = $userTypes[$key] : $userType = $this -> user['user_type'];

                    if (eF_insertTableData("users_to_courses", array("users_LOGIN" => $this -> user['login'], "courses_ID" => $courseID, "user_type" => $userType, "from_timestamp" => ($activate)? time() : 0, "active" => $activate ))) {

                        $course_lessons  = eF_getTableDataFlat("lessons_to_courses", "lessons_ID", "courses_ID = '".$courseID."'");
                        foreach ($course_lessons['lessons_ID'] as $key => $lesson) {
                            $this -> addLessons($lesson, $userType, $activate);
                        }
                        $courses[$courseID] = $userType;
                    }
                } else {
                    // If the course id exists see if the user_type needs update
                    isset($userTypes[$key]) && in_array($userTypes[$key], array_keys($userTypes)) ? $userType = $userTypes[$key] : $userType = $this -> user['user_type'];

                    if ($userType != $courses[$courseID]) {
                        eF_updateTableData("users_to_courses", array("user_type" => $userType), "users_LOGIN = '". $this -> user['login'] ."' AND courses_ID = '".  $courseID . "'");

                        $course_lessons  = eF_getTableDataFlat("lessons_to_courses", "lessons_ID", "courses_ID = '".$courseID."'");
                        foreach ($course_lessons['lessons_ID'] as $key => $lesson) {
                            $this -> addLessons($lesson, $userType, $activate);
                        }
                        $courses[$courseID] = $userType;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }
        return $courses;
    }

    /**
     * Remove courses from user.
     *
     * This function can be used to remove a course from the current user.
     * <br/>Example:
     * <code>
     * $user = EfrontUserFactory :: factory('jdoe');
     * $user -> removeCourses(23);                          //Remove a signle course with id 23
     * $user -> removeCourses(array(23,24,25));             //Remove multiple courses using an array
     * </code>
     *
     * @param int $courseIds Either a single course id, or an array if ids
     * @return true.
     * @since 3.5.0
     * @access public
     */
    public function removeCourses($courseIds) {
        if (!is_array($courseIds)) {
            $courseIds = array($courseIds);
        }

        foreach ($courseIds as $key => $courseID) {
            if (!eF_checkParameter($courseID, 'id')) {
                unset($courseIds[$key]);                                        //Remove illegal vaues from courses array.
            }
        }

        $result = eF_getTableData("lessons_to_courses lc, users_to_courses uc", "lc.*", "lc.courses_ID=uc.courses_ID and uc.users_LOGIN = '".$this -> user['login']."'");
        foreach ($result as $value) {
            $lessonsToCourses[$value['lessons_ID']][] = $value['courses_ID'];
            $coursesToLessons[$value['courses_ID']][] = $value['lessons_ID'];
        }
        $userLessonsThroughCourse = eF_getTableDataFlat("lessons_to_courses lc, users_to_courses uc", "lc.lessons_ID", "lc.courses_ID=uc.courses_ID and uc.courses_ID in (".implode(",", $courseIds).") and uc.users_LOGIN = '".$this -> user['login']."'");
        $userLessonsThroughCourse = $userLessonsThroughCourse['lessons_ID'];

        eF_deleteTableData("users_to_courses", "users_LOGIN = '".$this -> user['login']."' and courses_ID in (".implode(",", $courseIds).")");    //delete courses from list

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
     * Get user potential courses
     *
     * This function returns a list with the courses that the user
     * may take, but doesn't have. The list may be either a list of ids
     * (faster) or a list of EfrontCourse objects.
     * <br/>Example:
     * <code>
     * $user -> getNonCourses();            //Returns a list with potential courses ids
     * $user -> getNonCourses(true);        //Returns a list of EfrontCourse objects
     * </code>
     *
     * @param boolean $returnObjects Whether to return a list of objects
     * @return array The list of ids or objects
     * @since 3.5.0
     * @access public
     */
    public function getNonCourses($returnObjects = false) {
        $userCourses = eF_getTableDataFlat("users_to_courses", "courses_ID", "users_LOGIN = '".$this -> user['login']."'");
        sizeof($userCourses) > 0 ? $sql = "and id not in (".implode(",", $userCourses['courses_ID']).")" : $sql = '';

        if ($returnObjects) {
            $nonUserCourses = array();
            $courses        = eF_getTableData("courses", "*", "languages_NAME='".$this -> user['languages_NAME']."'".$sql);
            foreach ($courses as $value) {
                $nonUserCourses[$value['id']]  = new EfrontCourse($value['id']);
            }
            return $nonUserCourses;
        } else {
            $courses = eF_getTableDataFlat("courses", "*", "languages_NAME='".$this -> user['languages_NAME']."'".$sql);
            return $courses['id'];
        }
    }


    /**
     * Set user role
     *
     * This function is used to set the specific role of this user.
     * <br/>Example:
     * <code>
     * $user -> setRole(23, 'simpleUser');          //Set this user's role to 'simpleUser' for lesson with id 23
     * $user -> setRole(23);                        //Set this user's role to the same as its basic type (for example 'student') for lesson with id 23
     * $user -> setRole(false, 'simpleUser');       //Set this user's role to 'simpleUser' for all lessons
     * $user -> setRole();                          //Set this user's role to the same as its basic type (for example 'student') for all lessons
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
     * $this -> getRole(4);                                 //Get the role for lesson with id 4
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
        $roles      = eF_getTableDataFlat("user_types", "*", "active=1 AND basic_user_type!='administrator'");    //Get available roles
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
     *      $user = EfrontUserFactory :: factory('professor');
     *      $students = $user -> getProfessorStudents();
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
                if (!in_array($students, $student)){
                    $students[] = $student['login'];
                }
            }
        }
        return $students;
    }



    /**
     * Get user information
     *
     * This function returns the user information in an array
     *
     *
     * <br/>Example:
     * <code>
     * $info = $user -> getInformation();         //Get lesson information
     * </code>
     *
     * @param string $user The user login to customize lesson information for
     * @return array The user information
     * @since 3.5.0
     * @access public
     */
    public function getInformation() {
        $languages   = EfrontSystem :: getLanguages(true);
        $info        = array();
        $info['login']             = $this -> user['login'];
        $info['name']              = $this -> user['name'];
        $info['surname']           = $this -> user['surname'];
        $info['fullname']          = $this -> user['name'] . " " . $this -> user['surname'];
        $info['user_type']         = $this -> user['user_type'];
        $info['user_types_ID']     = $this -> user['user_types_ID'];
        $info['student_lessons']   = $this -> getLessons(true, 'student');
        $info['professor_lessons'] = $this -> getLessons(true, 'professor');
        $info['total_lessons']     = sizeof($info['student_lessons']) + sizeof($info['professor_lessons']);
        $info['total_login_time']  = self :: getLoginTime($this -> user['login']);
        $info['language']          = $languages[$this -> user['languages_NAME']];
        $info['active']            = $this -> user['active'];
        $info['active_str']        = $this -> user['active'] ? _YES : _NO;
        $info['joined']            = $this -> user['timestamp'];
        $info['joined_str']        = formatTimestamp($this -> user['timestamp'], 'time');
        $info['avatar']            = $this -> user['avatar'];

        return $info;
    }


}

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
        eF_updateTableData("rules", array("users_LOGIN" => ''), "users_LOGIN='".$this -> user['login']."'");

        eF_deleteTableData("users_to_lessons", "users_LOGIN='".$this -> user['login']."'");
        eF_deleteTableData("users_to_courses", "users_LOGIN='".$this -> user['login']."'");

    }
}

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
            eF_deleteTableData("done_questions",    "done_tests_ID IN (".implode(",", $userDoneTests['id']).")");
            eF_deleteTableData("done_tests",        "users_LOGIN='".$this -> user['login']."'");
        }

        eF_deleteTableData("users_to_lessons",      "users_LOGIN='".$this -> user['login']."'");
        eF_deleteTableData("users_to_courses",      "users_LOGIN='".$this -> user['login']."'");
        eF_deleteTableData("users_to_projects",     "users_LOGIN='".$this -> user['login']."'");
        eF_deleteTableData("users_to_done_tests",   "users_LOGIN='".$this -> user['login']."'");
        eF_deleteTableData("users_to_skillgap_tests",   "users_LOGIN='".$this -> user['login']."'");
        eF_deleteTableData("completed_tests",   "users_LOGIN='".$this -> user['login']."'");
    }

    /**
     * Complete lesson
     *
     * This function is used to set the designated lesson's status
     * to 'completed' for the current user.
     * <br/>Example:
     * <code>
     * $user -> completeLesson(5, 87, 'Very good progress');                                      //Complete lesson with id 5
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
            $fields = array('completed'    => 1,
                            'to_timestamp' => time(),
                            'score'        => $score,
                            'comments'     => $comments);
            eF_updateTableData("users_to_lessons", $fields, "users_LOGIN = '".$this -> user['login']."' and lessons_ID=".$lesson -> lesson['id']);

            if (MODULE_HCD_INTERFACE) {
                if (!$this -> aspects['hcd']) {
                    $this -> aspects['hcd'] = EfrontEmployeeFactory :: factory($this -> user['login']);
                }
                $employee = $this -> aspects['hcd'];

                $newSkills = eF_getTableDataFlat("module_hcd_lesson_offers_skill","skill_ID, specification","lesson_ID = '".$lesson -> lesson['id']."'");
                // The lesson associated skills will *complement* the existing ones - last argument = true
                $employee -> addSkills( $newSkills['skill_ID'],  $newSkills['specification'], true);
            }

            $courses    = $lesson -> getCourses();                                            //Get the courses that this lesson is part of. This way, we can auto complete a course, if it should be auto completed
            $userStatus = EfrontStats :: getUsersCourseStatus(array_keys($courses), $this -> user['login']);
            foreach ($courses as $course) {
                if ($course['auto_complete']) {
                    $completed  = array();
                    $score      = array();
                    foreach ($userStatus[$course['id']][$this -> user['login']]['lesson_status'] as $status) {
                        $status['completed'] ? $completed[] = 1 : $completed[] = 0;
                        $score[] = $status['score'];
                    }

                    if (array_sum($completed) == sizeof($completed)) {                                    //If all the course's lessons are completed, then auto complete the course, using the mean lessons score
                        $this -> completeCourse($course['id'], round(array_sum($score) / sizeof($score)), _AUTOCOMPLETEDCOURSE);
                    }
                }
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
     * $user -> completeCourse(5, 87, 'Very good progress');                                      //Complete course with id 5
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

        if (in_array($course -> course['id'], array_keys($this -> getCourses()))) {
            $fields = array('completed'    => 1,
                            'to_timestamp' => time(),
                            'score'        => $score,
                            'comments'     => $comments);
            $result = eF_updateTableData("users_to_courses", $fields, "users_LOGIN = '".$this -> user['login']."' and courses_ID=".$course -> course['id']);
            if ($result && $course -> course['auto_certificate']) {
                $certificate = $course -> prepareCertificate($this -> user['login']);
                $course -> issueCertificate($this -> user['login'], $certificate);
            }

            // Assign the related course skills to the employee
            if (MODULE_HCD_INTERFACE) {
                if (!$this -> aspects['hcd']) {
                    $this -> aspects['hcd'] = EfrontEmployeeFactory :: factory($this -> user['login']);
                }
                $employee = $this -> aspects['hcd'];
                $newSkills = eF_getTableDataFlat("module_hcd_course_offers_skill","skill_ID, specification","courses_ID = '".$course -> course['id']."'");

                // The course associated skills will *complement* the existing ones - last argument = true
                $employee -> addSkills( $newSkills['skill_ID'],  $newSkills['specification'], true);
            }
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
     * $user -> setSeenUnit(32, 2, true);                           //Set the unit with id 32 in lesson 2 as seen
     * $user -> setSeenUnit(32, 2, false);                          //Set the unit with id 32 in lesson 2 as not seen
     * </code>
     *
     * @param mixed $unit The unit to set status for, can be an id or an EfrontUnit object
     * @param mixed $lesson The lesson that the unit belongs to, can be an id or an EfrontLesson object
     * @param boolean $seen Whether to set the unit as seen or not
     * @return boolean true if everything is ok
     * @since 3.5.0
     * @access public
     */
    public function setSeenUnit($unit, $lesson, $seen) {
        if (isset($this -> coreAccess['content']) && $this -> coreAccess['content'] != 'change') {    //If user type is not plain 'student' and is not set to 'change' mode, do nothing
            return true;
        }
        if ($unit instanceof EfrontUnit) {                                            //Check validity of $unit
            $unit = $unit['id'];
        } elseif (!eF_checkParameter($unit, 'id')) {
            throw new EfrontContentException(_INVALIDID.": $unit", EfrontContentException :: INVALID_ID);
        }
        if ($lesson instanceof EfrontLesson) {                                        //Check validity of $lesson
            $lesson = $lesson -> lesson['id'];
        } elseif (!eF_checkParameter($lesson, 'id')) {
            throw new EfrontLessonException(_INVALIDID.": $lesson", EfrontLessonException :: INVALID_ID);
        }

        $lessons = $this -> getLessons();
        if (!in_array($lesson, array_keys($lessons))) {                                //Check if the user is actually registered in this lesson
            throw new EfrontUserException(_USERDOESNOTHAVETHISLESSON.": ".$lesson, EfrontUserException :: USER_NOT_HAVE_LESSON);
        }

        $result = eF_getTableData("users_to_lessons", "done_content, current_unit", "users_LOGIN='".$this -> user['login']."' and lessons_ID=".$lesson);
        sizeof($result) > 0 ? $doneContent = unserialize($result[0]['done_content']) : $doneContent = array();

        $current_unit = 0;
        if ($seen) {
            $doneContent[$unit] = $unit;
            $current_unit       = $unit;
        } else {
            unset($doneContent[$unit]);
            if ($unit == $result[0]['current_unit']) {
                sizeof($doneContent) ? $current_unit = end($doneContent) : $current_unit = 0;
            }
        }
        sizeof($doneContent) ? $doneContent = serialize($doneContent) : $doneContent = null;

        $result = eF_updateTableData("users_to_lessons", array('done_content' => $doneContent, 'current_unit' => $current_unit), "users_LOGIN='".$this -> user['login']."' and lessons_ID=".$lesson);

        return $result;
    }


    /**
     * Get skillgap tests to do
     *
     * This function returns an array with all skill gap tests assigned to the student
     * <br/>Example:
     * <code>
     * $user -> getSkillgapTests();                           //Set the unit with id 32 in lesson 2 as seen
     * </code>
     *
     * @param No parameters
     * @return Array of tests in the form [test_id] => [id, test_name]
     * @since 3.5.2
     * @access public
     */
    public function getSkillgapTests() {
    	$skillgap_tests = array();
        if ($_SESSION['s_version_type'] == 'Educational' || $_SESSION['s_version_type'] == 'Enterprise') {
	        $result = eF_getTableData("users_to_skillgap_tests JOIN tests ON tests_ID = id", "*", "users_LOGIN = '".$this -> user['login']."' AND publish = 1");
	        foreach ($result as $res) {
	            $skillgap_tests[$res['id']] = array('id' => $res['id'], 'name' => $res['name'], 'solved' => $res['solved']);
	        }
        }    
        return $skillgap_tests;
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
     * $user = EfrontUserFactory :: factory('jdoe');            //Use factory function to instantiate user object with login 'jdoe'
     * $userData = eF_getTableData("users", "*", "login='jdoe'");
     * $user = EfrontUserFactory :: factory($userData[0]);      //Use factory function to instantiate user object using prepared data
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
        if (eF_checkParameter($user, 'login')) {
            $result = eF_getTableData("users", "*", "login='".$user."'");
            if (sizeof($result) == 0) {
                throw new EfrontUserException(_USERDOESNOTEXIST.': '.$user, EfrontUserException :: USER_NOT_EXISTS);
            } else if ($password !== false && $password != $result[0]['password']) {
                throw new EfrontUserException(_INVALIDPASSWORDFORUSER.': '.$user[0]['login'], EfrontUserException :: INVALID_PASSWORD);
            }
            $user = $result[0];
        }

        $forceType ? $userType = $forceType : $userType = $user['user_type'];
        switch ($userType) {
            case 'administrator' : $factory = new EfrontAdministrator($user, $password); break;
            case 'professor'     : $factory = new EfrontProfessor($user, $password);     break;
            case 'student'       : $factory = new EfrontStudent($user, $password);       break;
            default: throw new EfrontUserException(_INVALIDUSERTYPE.': "'.$userType.'"', EfrontUserException :: INVALID_TYPE); break;
        }


        if (MODULE_HCD_INTERFACE) {
            $factory -> aspects['hcd'] = EfrontEmployeeFactory :: factory($user['login']);
        }

        return $factory;
    }


}



?>