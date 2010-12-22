<?php
/**

* EfrontModule Abstract Class file

*

* @package eFront

* @version 1.0

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * EfrontModuleException class

 *

 * This class extends Exception class and is used to issue errors regarding Modules

 * @author Nick Baltas <mpaltas@efront.gr>

 * @package eFront

 * @version 1.0

 */
class EfrontModuleException extends Exception
{
    const NO_ERROR = 0;
    const BRANCH_NOT_EXISTS = 201;
    const INVALID_ID = 202;
    const FATHER_NOT_VALID = 203;
    const INVALID_LOGIN = 204;
    const DATABASE_ERROR = 205;
    const DIR_NOT_EXISTS = 206;
    const FILESYSTEM_ERROR = 207;
    const DIRECTION_NOT_EXISTS = 208;
    const GENERAL_ERROR = 299;
}
/**

 * EfrontModule class

 *

 * This class represents a branch

 * @author Nick Baltas <mpaltas@efront.gr>

 * @package eFront

 * @version 1.0

 *

 * Index:

 *

 ***** General functions ********

 * - __construct($defined_moduleBaseUrl ,  $defined_moduleBaseDir)  // Constructor defining the relative paths for the module

 * - diagnose()     			// Function that checks whether the module's defined components are correct

 * - getName()					// Mandatory function returning the name of the module

 * - getPermittedRoles()		// Mandatory function returning an array of permitted roles from "administrator", "professor", "student"

 * - getLanguageFile()    		// Function to include the language file. Can be overriden to include any file

 * - getCenterLinkInfo()        // Return the info regarding the link on the main control panel

 * - getSidebarLinkInfo()       // Return infos regarding link(s) on the menu(s) of the left side control panel

 * - getNavigationLinks()       // Return navigational links for the top of the independent module page(s)

 * - getName()                  // The name-title of the module

 * - getLinkToHighlight()       // Get the id of the link to be highlighted by each independent module page

 *

 * - onInstall()				// Function to be executed when the module is installed to an eFront system

 * - onUninstall()				// Function to be executed when a module is deleted from a eFront system

 * - onUpgrade()				// Function to be executed when the module is upgraded from the link of the modules' list

 * - getCurrentUser() 			// Get the current session user

 * - getCurrentLesson() 		// Get the current session lesson

 * - getCurrentUnit()			// Get current content

 * - getSmartyVar() 			// Get the global smarty object

 * - setMessageVar($message, $message_type) // Set the global messages variables

 * - addScripts()				// Add javascript libraries to be included in module page load

 *

 ***** Module processing and appearance ********

 * - getModule()

 * - getSmartyTpl()

 * - getLessonModule()

 * - getLessonSmartyTpl()

 * - getContentSideInfo()

 * - getContentSmartyTpl()

 * - getContentSideTitle()

 * - getControlPanelModule()

 * - getControlPanelSmartyTpl()

 * - getDashboardModule()

 * - getDashboardSmartyTpl()

 * - getCatalogModule()

 * - getCatalogSmartyTpl()

 * - getLandingPageModule()

 * - getLandingPageSmartyTpl()

 * - getTabSmartyTpl($tabberId)

 * - getModuleJS()

 * - getModuleCSS()

 *

 ***** Module links ********

 * - getNavigationLinks()

 * - getLinkToHighlight()

 * - getSidebarLinkInfo()

 * - getLessonCenterLinkInfo()

 * - getCenterLinkInfo()

 *

 ***** Module event triggered functions ********

 * - onNewUser($login)

 * - onDeleteUser($login)

 * - onNewLesson($lessonId)

 * - onDeleteLesson($lessonId)

 * - onExportLesson($lessonId)

 * - onImportLesson($lessonId, $data)

 * - onCompleteLesson($lessonId, $login)

 * - onNewPageLoad()

 * - onSetTheme($theme)

 */
abstract class EfrontModule
{
    var $className;
    var $moduleBaseUrl;
    var $moduleBaseDir;
    var $moduleBaseLink;
    /**

     * Class constructor

     *

     * @param string $defined_moduleBaseUrl The basic url from which we access the module.

     * @param string $defined_moduleFolder The module's folder name inside the "modules/" folder

     * @since 3.5.0

     * @access public

     */
    public function __construct($defined_moduleBaseUrl, $defined_moduleFolder ) {
        // Information set by running environment
        $this -> className = get_class($this);
        $this -> moduleBaseDir = G_MODULESPATH. $defined_moduleFolder ."/";
        $this -> moduleBaseUrl = $defined_moduleBaseUrl;
        $this -> moduleBaseLink = G_SERVERNAME . "modules/". $defined_moduleFolder . "/";
    }
    /**

     * Function that checks whether the module's defined components are correct

     *

     * @param $error The error string to populate

     * @since 3.5.0

     * @access public

     */
    public final function diagnose(&$error) {
        // Check whether the roles defined are acceptable
        $roles = $this -> getPermittedRoles();
        foreach ($roles as $role) {
            if ($role != 'administrator' && $role != 'student' && $role != 'professor') {
                $error = _PERMITTEDROLESMODULEERROR;
                return false;
            }
        }
        // Check existence of user defined files
        $tpl = $this -> getSmartyTpl();
        if ($tpl && !is_file($tpl)) {
            $error = _SMARTYTEMPLATEDOESNOTEXIST . ": ".$tpl;
            return false;
        }
        $tpl = $this -> getLessonSmartyTpl();
        if ($tpl && !is_file($tpl)) {
            $error = _SMARTYTEMPLATEDOESNOTEXIST . ": ".$tpl;
            return false;
        }
        $tpl = $this -> getControlPanelSmartyTpl();
        if ($tpl && !is_file($tpl)) {
            $error = _SMARTYTEMPLATEDOESNOTEXIST . ": ".$tpl;
            return false;
        }
        $file = $this -> getModuleJS();
        if ($file && !is_file($file)) {
            $error = _FILEDOESNOTEXIST . ": ".$file;
            return false;
        }
        $file = $this -> getModuleCSS();
        if ($file && !is_file($file)) {
            $error = _FILEDOESNOTEXIST . ": ".$file;
            return false;
        }
        // All checks passed successfully
        return true;
    }
    /**

     * Return the name of the module

     *

     * Use this function whenever you want the name of the module to appear

     *

     * @return string The module name

     * @since 3.5.0

     * @access public

     */
    abstract public function getName();
    /**

     * Return an array of the roles this module applies to. For example, it can be array('administrator'), or array('professor', 'student')

     * Only valid array values are 'administrator', 'professor', 'student'

     *

     * @return array The permitted roles

     * @since 3.5.0

     * @access public

     */
    abstract public function getPermittedRoles();
    /**

     * Function denoting whether the module is related to lessons (and hence can be activated-deactivated) or not

     *

     * @return boolean Wether this is a lesson-related module

     * @since 3.5.0

     * @access public

     */
 public function isLessonModule() {
  return false;
 }
    /**

     * Function to include the language file, by default named 'lang-<language>.php', inside the

     * module's folder (for example, lang-english.php). Can be overriden to include any file

     *

     * @param $language The name of the language to include file for, eg 'english'

     * @since 3.5.0

     * @access public

     */
    public function getLanguageFile($language) {
  if (is_file($this -> moduleBaseDir . "lang-".$language.".php")) {
         return $this -> moduleBaseDir . "lang-".$language.".php";
  }
  return $this -> moduleBaseDir . "lang-english.php";
    }
    // Any further actions that need to take place during installation
    /**

     * Function to be executed when the module is installed to an eFront system

     * Example implementation:

     *

     * public function onInstall() {

     *   return eF_executeNew("CREATE TABLE module_mymodule (

     *                    id int(11) NOT NULL auto_increment,

     *                    name text not null,

     *                    PRIMARY KEY  (id)

     *                   ) DEFAULT CHARSET=utf8;");

     * }

     *

     * @return Boolean the result (true/false) of any module installation operations

     * @since 3.5.0

     * @access public

     */
    public function onInstall() {
        return true;
    }
    /**

     * Function to be executed when the module is removed from an eFront system

     * Example implementation:

     *

     * public function onUninstall() {

     *   return eF_executeNew("DROP TABLE module_mymodule;");

     * }

     *

     * @return Boolean the result (true/false) of any module uninstallation operations

     * @since 3.5.0

     * @access public

     */
    public function onUninstall() {
        return true;
    }
    /**

     * Any further actions that need to take place during module upgrade

     * This might relate mainly to changes taking place in the database tables that have

     * been defined for this module. If the upgraded version of the module is to

     * use different tables at the eFront database (like different or additional fields or field

     * names), then this function should take care to maintain existing data from

     * the previous module version to the new table. This could happen like that:

     * 1) Create a temporary table of the form that the upgraded version of the module requires

     * 2) Parse data from the existing table

     * 3) Transform them in such a way that the newly defined table will accept

     * 4) Insert the transformed data to the newly created temp table

     * 5) Delete the initial table (from which the data have been read) for the module

     * 6) Rename the temporary table to the name that the module table needs to have

     *

     * This algorithm guarantees that if something goes wrong no data will be lost, since

     * existing data are deleted only once they have been successfully copied to the new table

     * It is noted here that if onUpgrade() is not defined, then the eFront system will leave

     * existing module database tables and their data intact.

     *

     * @return Boolean the result (true/false) of any module upgrade operations

     * @since 3.5.0

     * @access public

     */
    public function onUpgrade() {
        return true;
    }
    /**

     * Return the current user object

     *

     * @return mixed Either an EfrontUser object of the current logged in user, or false if noone is logged in

     * @since 3.5.0

     * @access public

     */
    public function getCurrentUser() {
        if ($GLOBALS['currentUser']) {
         $currentUser = $GLOBALS['currentUser'];
        } elseif ($_SESSION['s_login']) {
         $currentUser = EfrontUserFactory::factory($_SESSION['s_login']);
        } else {
         $currentUser = false;
        }
        return $currentUser;
    }
    /**

     * Return the current course object

     *

     * @return mixed Either an EfrontCourse object of the current course, or false if we are not inside a course

     * @since 3.6.8

     * @access public

     */
    public function getCurrentCourse() {
  if (isset($_SESSION['s_courses_ID'])) {
         $currentCourse = new EfrontCourse($_SESSION['s_courses_ID']);
         return $currentCourse;
  } else {
   return false;
  }
    }
    /**

     * Return the current lesson object

     *

     * @return mixed Either an EfrontLesson object of the current lesson, or false if we are not inside a lesson

     * @since 3.5.0

     * @access public

     */
    public function getCurrentLesson() {
        if ($GLOBALS['currentLesson']) {
         $currentLesson = $GLOBALS['currentLesson'];
        } elseif ($_SESSION['s_lessons_ID']) {
         $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
        } else {
         $currentLesson = false;
        }
        return $currentLesson;
    }
    /**

     * Return the current unit object

     *

     * @return mixed Either an EfrontUnit object of the current unit, or false if we are not inside a unit

     * @since 3.5.0

     * @access public

     */
    public function getCurrentUnit() {
        if ($GLOBALS['currentUnit']) {
         $currentUnit = $GLOBALS['currentUnit'];
        } elseif ($_GET['view_unit']) {
         $currentUnit = new EfrontUnit($_GET['view_unit']);
        } else {
         $currentUnit = false;
        }
        return $currentUnit;
    }
    /**

     * Get the $smarty variable, to manipulate templates

     *

     * @return Object The $smarty variable

     * @since 3.5.0

     * @access public

     */
    public final function getSmartyVar() {
        global $smarty;
        return $smarty;
    }
    /**

     * Use this function to set a message that will appear on the top of the page.

     * Specify 'success' or 'failure' for the 2nd argument, to make it appear

     * as a success or failure message

     *

     * @param string $message The message to show

     * @param string $message_type Either 'success' or 'failure'

     * @since 3.5.0

     * @access public

     */
    public function setMessageVar($message, $message_type) {
        $GLOBALS['message'] = $message;
        $GLOBALS['message_type'] = $message_type;
    }
    /**

     * Add event to eFront's events log

     *

     * This function enables module to provide events to the log

     * Events should be UNIQUELY defined INSIDE the module

     *

     * All data required for the appearance of the log message (provided by the getEventMessage function)

     * should be defined in the second argument array.

     *

     * <br/>Example:

     * <code>

     * $define("NEW_MODULE_ENTITY_INSERTION", 1);

     * $module -> addEvent(NEW_MODULE_ENTITY_INSERTION, array("id" => $id, "title" => $title));

     * </code>

     * The $data parameter is the information required by the getEventMessage function to display the related message

     * for this event.

     * Note:

     * Field 'timestamp' is automatically completed

     * If fields "users_LOGIN", "users_name" and "users_surname" are not defined, then the currentUser's info will be used

     * If fields "lessons_ID" and "lessons_name" are defined, then this event will also be related with that lesson

     * The array might contain any other fields. However, the exact same ones need to be used by getEventMessage

     *

     * @param integer $type The unique code of the event inside the particular module scope

     * @param array $data The information required by the getEventMessage function to display the related message

     * for this event.

     * @return EfrontEvent the result of the event insertion to the database or false if arguments are not correct

     * @since 3.6.0

     * @access public

     */
    public function addEvent($type, $data) {
     $fields = array();
     // All module related events have the same offset + the particular event's type
     $fields['type'] = EfrontEvent::MODULE_BASE_TYPE_CODE + (integer)$type;
     // This should not exist normally, just in case
  unset($data['type']);
     // The discimination between events from different modules with the same type is made
     // by the entity_ID field, which is the className of the implicated module
     $fields['entity_ID'] = $this -> className;
     // Mandatory users_LOGIN, users_surname, users_name fields
     if (isset($data['users_LOGIN'])) {
      $fields['users_LOGIN'] = $data['users_LOGIN'];
      if(isset($data['surname']) && isset($data['name'])) {
       $fields['users_surname'] = $data['surname'];
       $fields['users_name'] = $data['name'];
      } else {
       $eventsUser = EfrontUserFactory::factory($data['users_LOGIN']);
       $fields['users_surname'] = $eventsUser -> user['surname'];
       $fields['users_name'] = $eventsUser -> user['name'];
      }
      // We remove data fields, to serialize all remaining ones into the entity_name field
      unset($data['users_LOGIN']);
      unset($data['users_surname']);
      unset($data['users_name']);
     } else {
   $currentUser = $this ->getCurrentUser();
      $fields['users_LOGIN'] = $currentUser -> user['login'];
      $fields['users_surname'] = $currentUser -> user['surname'];
      $fields['users_name'] = $currentUser -> user['name'];
     }
     // The lessons_ID field associates an event with a specific lesson
     if (isset($data['lessons_ID'])) {
      $fields['lessons_ID'] = $data['lessons_ID'];
   if (isset($data['lessons_name'])) {
    $fields['lessons_name'] = $data['lessons_name'];
    unset($data['lessons_name']);
   } else {
    $lesson = new EfrontLesson($fields['lessons_ID']);
    $fields['lessons_name'] = $lesson -> lesson['name'];
   }
      // We remove data fields, to serialize all remaining ones into the entity_name field
      unset($data['lessons_ID']);
     }
     // Serialize all remaining user provided data for this event, with the same labels as the ones given
     if (!empty($data)) {
      $fields['entity_name'] = serialize($data);
     }
     // Finally get current time
     $fields['timestamp'] = time();
        return EfrontEvent::triggerEvent($fields);
    }
    /**

     * Get the message associated to a particular event

     *

     * This function returns the message that should appear in a log/timeline/email digest etc

     * for this event. Data provided during event insertion are now used to create the message that should be

     * returned.

     *

     *

     * <br/>Example:

     * <code>

     * $define("NEW_MODULE_ENTITY_INSERTION", 1);

     * $data_array = array("id" => $id, "title" => $title);

     * $module -> addEvent(NEW_MODULE_ENTITY_INSERTION, $data_array);

     *

     * // Sample implementation

     *  public function getEventMessage($type, $data) {

     * 		if ($type == 1) {

     * 			return "User ".$data['users_surname']." ".$data['users_name']." inserted <a href='student.php?entity_ID=".$data['id']."'>entity</a> with title: " . $data['title'];

     * 		}

     * 		return false;

     * 	}

     * </code>

     * The $data argument is the information as provided by the addEvent method, needed to display this message

     * for this event.

     * Notes:

     * Fields "timestamp", "users_LOGIN", "users_name" and "users_surname" are ALWAYS provided

     * The remaining fields are the same ones provided by the addEvent function

     * The time of the event is implicitly printed by the eFront system and should not be provided by your defined event messages

     *

     * @param int $type the unique code of the event inside the particular module scope

     * @param array $data information as provided by the addEvent method, needed to display this message

     * for this event.

     * @return mixed the message associated with this event and the provided data or false if no such message is to be provided

     * @since 3.6.0

     * @access public

     */
    public function getEventMessage($type, $data) {
  return false;
    }
    // Method used to load current eFront scripts from the www/js folder as
    // return array("XX","folderY/ZZ"); will load www/js/XX.js and www/js/folderY/ZZ.js
    /**

     * Method used to load current eFront scripts from the www/js folder as

     * return array("XX","folderY/ZZ"); will load www/js/XX.js and www/js/folderY/ZZ.js

     *

     * @return array An array of efront scripts to load

     * @since 3.5.0

     * @access public

     */
    public function addScripts() {
        return array();
    }
    /**

     * This is the function for the php code of the MAIN module pages (the ones

     * called from url:    $this->moduleBaseUrl . "&...."

     * The global smarty variable may also be used here and in conjunction

     * with the getSmartyTpl() function, use php+smarty to display the page

     *

     * @since 3.5.0

     * @access public

     */
    public function getModule() {
        return false;
    }
    /**

     * This is the function that returns the name of the module smarty template file

     * for the appearance of the main page of the module (if one such is used)

     * Example implementation:

	 *

     *   public function getSmartyTpl() {

     *       return $this -> moduleBaseDir . "

     *   }

	 *

	 * @since 3.5.0

	 * @access public

     */
    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASELINK" , $this -> moduleBaseLink);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
    }
    /**

     * This is the function for the php code of the module page that may

     * appear as a sub-window on the main lesson page of the current Lesson (for students/professors)

     * Note: Current lesson information may be retrieved with the getCurrentLesson() function

     *

     * @since 3.5.0

     * @access public

     */
    public function getLessonModule() {
        return false;
    }
    /**

     * This is the template code returned inside a block in a lesson

     *

     * @since 3.5.0

     * @access public

     */
    public function getLessonSmartyTpl() {
        return false;
    }
    /**

     * This is the code executed inside the content (unit) page of a lesson

     *

     * @since 3.6.0

     * @access public

     */
    public function getContentSideInfo() {
        return false;
    }
    /**

     * This is the template code returned inside the content (unit) page of a lesson

     *

     * @since 3.6.0

     * @access public

     */
    public function getContentSmartyTpl() {
        return false;
    }
    /**

     * Returns the title string to appear on top of the content side - if such is definedon

     *

     * @since 3.6.0

     * @access public

     */
    public function getContentSideTitle() {
     return false;
    }
    /**

     * This is the function for the php code of the module page that may

     * appear as a sub-window on the main administrator control panel page

     *

     * @since 3.5.0

     * @access public

     */
    public function getControlPanelModule() {
        return false;
    }
    /**

     * This is the template code returned for admin's control panel blocks

     *

     * @since 3.5.0

     * @access public

     */
    public function getControlPanelSmartyTpl() {
        $smarty = $this->getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
    }
    /**

     * This is the function for the php code of the module page that may

     * appear as a sub-window on the main administrator control panel page

     *

     * @since 3.6.0

     * @access public

     */
    public function getDashboardModule() {
        return false;
    }
    /**

     * This is the template code returned for admin's control panel blocks

     *

     * @since 3.6.0

     * @access public

     */
    public function getDashboardSmartyTpl() {
        $smarty = $this->getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
    }
    /**

     * This is the function for the php code of the module page that may

     * appear inside the catalog

     *

     * @since 3.6.0

     * @access public

     */
    public function getCatalogModule() {
        return false;
    }
    /**

     * This is the template code returned for the catalog

     *

     * @since 3.6.0

     * @access public

     */
    public function getCatalogSmartyTpl() {
        $smarty = $this->getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
    }
    /**

     * This is the code executed when a module is set as "landing page" and the

     * user logs in

     *

     * @since 3.6.4

     * @access public

     */
    public function getLandingPageModule() {
        return false;
    }
    /**

     * This is the template code returned for admin's control panel blocks

     *

     * @since 3.6.4

     * @access public

     */
    public function getLandingPageSmartyTpl() {
        $smarty = $this->getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
    }
    /**

     * This should return an array with tab information. For example:

     * <code>

     *  public function getTabSmartyTpl($tabberIdentifier) {

     *		$smarty = $this -> getSmartyVar();

     *		if ($tabberIdentifier == 'branches') {

     *			$smarty -> assign("T_USERS", eF_getTableData("users", "login"));

     *		}

     *		return array('tab' => 'rss_branch', 'title' => 'RSS Branch', 'file' => $this -> moduleBaseDir."module_rss_branch_tab.tpl");

     *  }

     *</code>

     *

     * @param $tabberIdentifier A string with the unique identifier of the tab set

     * @since 3.6.8

     */
    public function getTabSmartyTpl($tabberIdentifier) {
        return false;
    }
 /**

	 * Get a javascript file to load for this module

	 *

	 * @since 3.5.0

	 * @access public

	 */
    public function getModuleJS() {
        return false;
    }
 /**

	 * Get a css file to load for this module

	 *

	 * @since 3.5.0

	 * @access public

	 */
    public function getModuleCSS() {
        return false;
    }
    /**

     * Get Navigational links for the top of the independent module page(s)

     * Get information in an array of sub-arrays with fields:

     * 'title': the title to appear on the link

     * 'image': the image to appear (if image inside module folder then use ($this -> moduleBaseDir) . 'imageFileName' -TODO

     * 'link': the url of the page to be from this link

     * Each sub-array represents a different link. Between them the "&raquo;" character is automatically inserted by the system

     * Example implementation:

	 *

     *   public function getNavigationLinks() {

     *       if (isset($_GET['subpage1'])) {

     *           return array (array ('title' => "Main Page" , 'link'  => $this -> moduleBaseUrl),

     *                         array ('title' => "Sub Page 1", 'link'  => $this -> moduleBaseUrl . "&operation=subpage1"));

     *       else {

     *           return false;   // Only the default page with the module Name as title will be returned

     *       }

     *   }

     *

     * @return array The array of navigational links

     * @since 3.5.0

     * @access public

     */
     public function getNavigationLinks() {
        return false;
     }
    /**

     * Get links to be highlighted

     * Each time a module independent page is displayed a different link of the left sidebar can be highlighted

     * To do this return the id of the corresponding link as defined by your getSidebarLinkInfo() returned array

     * Example implementation:

	 *

     *   public function getLinkToHighlight() {

     *       if (isset($_GET['management'])) {

     *           return 'other_link_id1';

     *       } else {

     *           return 'other_link_id2';

     *       }

     *   }

     *

     * @return array The link to highlight

     * @since 3.5.0

     * @access public

     */
     public function getLinkToHighlight() {
        return false;
     }
    /**

     * Control Panel Module Link

     * Get information in an array with fields:

     * 'title': the title to appear on the link

     * 'image': the image to appear (if image inside module folder then use ($this -> moduleBaseDir) . 'imageFileName'

     * 'target': POPUP or innerTable (default Innertable) - TODO

     *

     *

     *  Example implementation:

	 *

     *   public function getCenterLinkInfo() {

     *       return array ('title' => 'My Module',

     *                     'image' => $this -> moduleBaseDir . 'images/my_module.jpg');

     *   }

     *

     * @return array The array of the control panel info

     * @since 3.5.0

     * @access public

     */
    public function getCenterLinkInfo() {
        return false;
    }
    /****

     * Lesson Control Panel Module Link

     * Get information in an array with fields:

     * 'title': the title to appear on the link

     * 'image': the image to appear (if image inside module folder then use ($this -> moduleBaseDir) . 'imageFileName'

     * 'target': POPUP or innerTable (default Innertable) - TODO

     *

     *  Example implementation:

	 *

     *   public function getCenterLinkInfo() {

     *       return array ('title' => 'My Module',

     *                     'image' => $this -> moduleBaseDir . 'images/my_module.jpg');

     *   }

     *

     * @return array The array of the lesson control panel info

     * @since 3.5.0

     * @access public

     */
    public function getLessonCenterLinkInfo() {
        return false;
    }
    /****

     * Left Sidebar Module Links

     * Get information in an array of arrays with fields:

     * 'menu': defines the menu(s) where links will appear "system" | "lessons" | "users" | "organization" | "tools" | "current_lesson" | "other"

     *         if "other" is selected then an additional "menuTitle" field can be defined for the Title of the menu

     *         -- multiple other menus may be defined - TODO

     * 'id': a unique id of the link within the module (and NOT within the entire eFront) framework. This id is used for link highlighting purposes

     *       with highlightLink()

     * 'title': the title to appear on the link

     * 'image': the image to appear next to the link (if image inside module folder then use ($this -> moduleBaseDir) . 'imageFileName'

     * 'eFrontExtensions': you may optionally define two images for each link: one .png and .gif, which will appear under FF and IE respectively.

     *                     The filename (without the extension) and the path of the two pictures must be the same.

     *                     If 'eFrontExtensions' => 1, then do not use an extension to the image filename

     * 'link': the url of the page to be displayed in the main window

     * 'target': POPUP or mainTable (default Innertable) - TODO

     *

     *  Example implementation:

	 *

     *   public function getSidebarLinkInfo() {

     *       $link_of_menu_system = array   (array ('id'    => 'system_link_id',

     *                                              'title' => 'My System Related Module Part 1',

     *                                              'image' => '16x16/pens',                                 // no extension in the filename,

     *                                              'eFrontExtensions' => '1',                               // question_type_free_text.png and pens.gif must exist in 16x16

     *                                              'link'  => $this -> moduleBaseUrl . "&module_op=system_operation"),

     *                                       array ('id'    => 'system_link_id2',

     *                                              'title' => 'My System Related Module Part 2',

     *                                              'image' => '16x16/pencil2.png',

     *                                              'link'  => $this -> moduleBaseUrl . "&module_op=system_operation"));

	 *

     *       $link_of_module_menus  = array ( array ('id'    => 'other_link_id1',

     *                                               'title' => 'Main Module',

     *                                               'image' => $this -> moduleBaseDir . 'images/my_module_pic', // no extension in the filename

     *                                               'eFrontExtensions' => '1',                                  // my_module_pic.gif and my_module_pic.png must exist in $this->moduleBaseDir . 'images/'

     *                                               'link'  => $this -> moduleBaseUrl),

     *                                        array ('id'    => 'other_link_id2',

     *                                               'title' => 'Second Module Page',

     *                                               'image' => '16x16/attachment.png',

     *                                               'link'  => $this -> moduleBaseUrl . '&module_operat=2'));

	 *

     *       return array ( "system" => $link_of_menu_system,

     *                      "other"  => array('menuTitle' => 'My Module Menu', 'links' => $link_of_module_menus));

     *   }

     *

     * @return array The array of the sidebar menu items

     * @since 3.5.0

     * @access public

    */
    public function getSidebarLinkInfo() {
        return false;
    }
    /**

     * Code to execute when a new user is created

     *

     * @param string $login The login of the user

     * @since 3.5.0

     * @access public

     */
    public function onNewUser($login) {
        return false;
    }
    /**

     * Code to execute when a user is deleted

     *

     * @param string $login The login of the user

     * @since 3.5.0

     * @access public

     */
    public function onDeleteUser($login) {
        return false;
    }
    /**

     * Code to execute when a new lesson is created

     *

     * @param int $lessonId The id of the lesson

     * @since 3.5.0

     * @access public

     */
    public function onNewLesson($lessonId) {
        return false;
    }
    /**

     * Code to execute when a lesson is deleted

     *

     * @param int $lessonId The id of the lesson

     * @since 3.5.0

     * @access public

     */
    public function onDeleteLesson($lessonId) {
        return false;
    }
    /**

     * Code to execute when a course is deleted

     *

     * @param int $courseId The id of the course

     * @since 3.6.8

     * @access public

     */
    public function onDeleteCourse($courseId) {
        return false;
    }
    /**

     * Code to execute when a course certificate is revoked

     *

     * @param string $login The user that the certificate was issued for

     * @param int $courseId The id of the course

     * @since 3.6.8

     * @access public

     */
    public function onRevokeCourseCertificate($login, $courseId) {
        return false;
    }
    /**

     * Code to execute when a course certificate is issued

     *

     * @param string $login The user that the certificate was issued for

     * @param int $courseId The id of the course

     * @param array $certificateArray The certificate data array

     * @since 3.6.8

     * @access public

     */
    public function onIssueCourseCertificate($login, $courseId, $certificateArray) {
     return false;
    }
    /**

     * Code to execute when a course certificate is issued

     *

     * @param string $login The user that the certificate was issued for

     * @param int $courseId The id of the course

     * @param array $certificateData The certificate data

     * @since 3.6.8

     * @access public

     */
    public function onPrepareCourseCertificate($login, $courseId, $certificateData) {
     return false;
    }
    /**

     * Code to execute when a course is exported

     *

     * @param int $courseId The id of the course

     * @since 3.6.8

     * @access public

     */
    public function onExportCourse($courseId) {
     return false;
    }
    /**

     * Code to execute when a course is imported

     *

     * @param int $courseId The id of the course

     * @since 3.6.8

     * @access public

     */
    public function onImportCourse($courseId, $data) {
     return false;
    }
    /**

     * Code to execute when a course is created

     *

     * @param int $courseId The id of the course

     * @since 3.6.8

     * @access public

     */
    public function onNewCourse($courseId) {
        return false;
    }
    /**

     * Code to execute when a course is set as complete for a user

     *

     * @param int $courseId The id of the course

     * @param string $login The user login

     * @since 3.6.8

     * @access public

     */
    public function onCompleteCourse($courseId, $login) {
        return false;
    }
    /**

     * Code to execute when a course is reset for a user

     *

     * @param int $courseId The id of the course

     * @param string $login The user login

     * @since 3.6.8

     * @access public

     */
    public function onResetProgressInCourse($courseId, $login) {
     return false;
    }
    /**

     * Code to execute when a progress is reset for a user in all courses

     *

     * @param string $login The user login

     * @since 3.6.8

     * @access public

     */
    public function onResetProgressInAllCourses($login) {
     return false;
    }
    /**

     * Code to execute when a lesson with id = $lessonId is exported. This

     * function should return an array with all information (like DB values)

     * that need to be stored into the exported lesson file

     * Example implementation:

     * <code>

     *       public function onExportLesson($lessonId) {

     *           $data = eF_getTableData("myModule", "*", "lessons_ID = $lessonId");

     *           $data['myModuleVersion'] = "3.5beta";

     *           return $data;

     *       }

     * </code>

     *

     * @param int $lessonId The lesson id

     * @since 3.5.0

     * @access public

     */
    public function onExportLesson($lessonId) {
        return false;
    }
    /**

     * Code to execute when a lesson with id = $lessonId is imported. This

     * function gets $data as argument which is in the exact same format

     * as it was exported by the onExportLesson function.

     * Example implementation (in accordance with the above given export example):

     * <code>

     *       public function onExportLesson($lessonId, $data) {

     *           echo "My module's version is " . $data['myModuleVersion'];

     *           unset($data['myModuleVersion']);

     *

     *           foreach ($data as $record) {

     *               eF_insertTableData("myModule", $record);

     *           }

     *           return true;

     *       }

     * </code>

     *

     * @param int $lessonId The lesson id

     * @param array $data The data to import

     * @since 3.5.0

     * @access public

     */
    public function onImportLesson($lessonId, $data) {
        return false;
    }
    /**

     * Code to execute when a lesson is set as complete for a user

     *

     * @param int $lessonId The id of the lesson

     * @param string $login The user login

     * @since 3.6.8

     * @access public

     */
 public function onCompleteLesson($lessonId, $login) {
        return false;
    }
    /**

     * Code that executes every time a page is loaded

     *

     * @since 3.6.0

     * @access public

     */
    public function onNewPageLoad() {
        return false;
    }
    /**

     * This function is called when the system theme is set. If you are trying to change

     * the current theme, remember to unset the $_SESSION['s_theme'] variable

     *

     * @param int $theme The current system theme that was just set

     * @since 3.6.8

     * @access public

     */
    public function onSetTheme($theme) {
     return false;
    }
    /**

     * Code executed when a theme is deleted

     *

     * @param int $theme The theme that is being deleted

     * @since 3.6.8

     * @access public

     */
    public function onDeleteTheme($theme) {
     return false;
    }
    /**

     * This should return an array with "tab page" information. For example:

     * <code>

	 *   public function getTabPageSmartyTpl($tabPageIdentifier) {

	 *   	 switch ($tabPageIdentifier) {

	 *   		case 'course_settings':

	 *   			$tabPageData = array('tab_page' => 'course_settings_demo_tab',			//Use an existing name, to overwrite an existing functionality

	 *	    							 'title' 	=> _MODULE_DEMO_COURSESETTINGSTABPAGE,

	 *   								 'image'	=> '16x16/generic.php',

	 *	    							 'file'  	=> $this -> moduleBaseDir.'module_demo_course_settings_tab_page.tpl');

	 *   			break;

	 *   		default:break;

	 *   	 }

	 *       return $tabPageData;

	 *   }

     *</code>

     *

     * @param $tabPageIdentifier A string with the unique identifier of the tab page set

     * @since 3.6.8

     */
    public function getTabPageSmartyTpl($tabPageIdentifier) {
     return false;
    }
    /**

     * This should return an array with "fieldset" information. For example:

     * <code>

	 *   public function getFieldsetSmartyTpl($fieldsetIdentifier) {

	 *   	switch ($fieldsetIdentifier) {

	 *   		case 'lesson_progress':

	 *   			$fieldsetData = array('fieldset' => 'lesson_progress_demo_fieldset',			//Use an existing name, to overwrite an existing functionality

	 *	    							  'title' 	 => _MODULE_DEMO_COURSESETTINGSTABPAGE,

	 *	    							  'file'  	 => $this -> moduleBaseDir.'module_demo_lesson_progress_fieldset.tpl');

	 *   			break;

	 *   		default:break;

	 *   	}

	 *       return $fieldsetData;

	 *   }

     *</code>

     *

     * @param $tabPageIdentifier A string with the unique identifier of the tab page set

     * @since 3.6.8

     */
    public function getFieldsetSmartyTpl($fieldsetIdentifier) {
    }
}
