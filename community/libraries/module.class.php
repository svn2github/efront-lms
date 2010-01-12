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
    const NO_ERROR          = 0;
    const BRANCH_NOT_EXISTS = 201;
    const INVALID_ID        = 202;
    const FATHER_NOT_VALID  = 203;
    const INVALID_LOGIN     = 204;
    const DATABASE_ERROR    = 205;
    const DIR_NOT_EXISTS    = 206;
    const FILESYSTEM_ERROR  = 207;
    const DIRECTION_NOT_EXISTS = 208;
    const GENERAL_ERROR     = 299;
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
 * - onNewPageLoad() {
 *
 */

abstract class EfrontModule
{
    var $className;
    var $moduleBaseUrl;
    var $moduleBaseDir;
    var $moduleBaseLink;

    // Constructor
    function __construct($defined_moduleBaseUrl ,  $defined_moduleFolder ) {
        // Information set by running environment
        $this -> className = get_class($this);
        $this -> moduleBaseDir = G_MODULESPATH. $defined_moduleFolder ."/";
        $this -> moduleBaseUrl = $defined_moduleBaseUrl;
        $this -> moduleBaseLink = G_SERVERNAME . "modules/". $defined_moduleFolder . "/";

    }

    // Function that checks whether the module's defined components are correct
    function diagnose(&$error) {

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


    // Fundamental methods

    // Module name - Mandatory
    abstract public function getName();

    // Access control - Mandatory
    abstract public function getPermittedRoles();

	// Function denoting whether the module is related to lessons (and hence can be activated-deactivated) or not
	public function isLessonModule() {
		return false;
	}

    // Function to include the language file. Can be overriden to include any file
    public function getLanguageFile($language) {
		if (is_file($this -> moduleBaseDir . "lang-".$language.".php")) {
	        return $this -> moduleBaseDir . "lang-".$language.".php";
		}
		return $this -> moduleBaseDir . "lang-english.php";
    }

    // Any further actions that need to take place during installation
    public function onInstall() {
        return true;
    }

    // Any further actions that need to take place during uninstalling
    public function onUninstall() {
        return true;
    }

    // Any further actions that need to take place during module upgrade
    // This might relate mainly to changes taking place in the database tables that have
    // been defined for this module. If the upgraded version of the module is to
    // use different tables at the eFront database (like different or additional fields or field
    // names), then this function should take care to maintain existing data from
    // the previous module version to the new table. This could happen like that:
    /*
     * 1) Create a temporary table of the form that the upgraded version of the module requires
     * 2) Parse data from the existing table
     * 3) Transform them in such a way that the newly defined table will accept
     * 4) Insert the transformed data to the newly created temp table
     * 5) Delete the initial table (from which the data have been read) for the module
     * 6) Rename the temporary table to the name that the module table needs to have
     *
     */
    // This algorithm guarantees that if something goes wrong no data will be lost, since
    // existing data are deleted only once they have been successfully copied to the new table
    // It is noted here that if onUpgrade() is not defined, then the eFront system will leave
    // existing module database tables and their data intact.
    public function onUpgrade() {
        return true;
    }

    /************ eFront information provided to the module ************/
    // Runtime variables
    public function getCurrentUser() {
        global $currentUser;
        return $currentUser;
    }

    public function getCurrentLesson() {
        global $currentLesson;
        return $currentLesson;
    }
    public function getCurrentUnit() {
    	global $currentUnit;
    	return $currentUnit;	
    }
    
    public function getSmartyVar() {
        global $smarty;
        return $smarty;
    }

    public function setMessageVar($message, $message_type) {
        $GLOBALS['message'] = $message;
        $GLOBALS['message_type'] = $message_type;
        return true;
    }

    /*
     * Add event to eFront's  events log
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
     *  
     * </code>
     *
     * @param $type: integer: the unique code of the event inside the particular module scope
     * @param $data: array: information required by the getEventMessage function to display the related message
     * for this event. 
     * 
     * Note: 
     * Field timestamp is automatically completed
     * If fields "users_LOGIN", "users_name" and "users_surname" are not defined, then the currentUser's info will be used
     * If fields "lessons_ID" and "lessons_name" are defined, then this event will also be related with that lesson
     * The array might contain any other fields. However, the exact same ones need to be used by getEventMessage
     * @return the result of the event insertion to the database or false if arguments are not correct
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

    /*
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
     *
     * @param $type: integer: the unique code of the event inside the particular module scope
     * @param $data: array: information as provided by the addEvent method, needed to display this message
     * for this event. 
     * 
     * Notes: 
     * Fields "timestamp", "users_LOGIN", "users_name" and "users_surname" are ALWAYS provided
     * The remaining fields are the same ones provided by the addEvent function
     * The time of the event is implicitly printed by the eFront system and should not be provided by your defined event messages
     * @return the message associated with this event and the provided data or false if no such message is to be provided
     * @since 3.6.0
     * @access public
     */
    public function getEventMessage($type, $data) {
		return false;
    }
    
    // Method used to load current eFront scripts from the www/js folder as
    // return array("XX","folderY/ZZ"); will load www/js/XX.js and www/js/folderY/ZZ.js
    public function addScripts() {
        return array();
    }

    /***********************************************/
    /************ DEFINING MODULE PAGES ************/
    /***********************************************/

    /***** Main - Independent module pages *******/

    /**
     * This is the function for the php code of the MAIN module pages (the ones
     * called from url:    $this->moduleBaseUrl . "&...."
     * The global smarty variable may also be used here and in conjunction
     * with the getSmartyTpl() function, use php+smarty to display the page
     */
    public function getModule() {
        return false;
    }

    /**
     * This is the function that returns the name of the module smarty template file
     * for the appearance of the main page of the module (if one such is used)
     * Example implementation:

        public function getSmartyTpl() {
            return $this -> moduleBaseDir . "
        }

     */
    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASELINK" , $this -> moduleBaseLink);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
    }


    /***** Lesson module pages *******/

    /**
     * This is the function for the php code of the module page that may
     * appear as a sub-window on the main lesson page of the current Lesson (for students/professors)
     * Note: Current lesson information may be retrieved with the getCurrentLesson() function
     */
    public function getLessonModule() {
        return false;
    }

    public function getLessonSmartyTpl() {
        return false;
    }

    /***** Lesson content module pages *******/
    public function getContentSideInfo() {
        return false;
    }

    public function getContentSmartyTpl() {
        return false;
    }    
    
    // Returns the title string to appear on top of the content side - if such is defined
    public function getContentSideTitle() {
    	return false;
    }
    
    /***** Administrator control panel *******/

    /**
     * This is the function for the php code of the module page that may
     * appear as a sub-window on the main administrator control panel page
     */
    public function getControlPanelModule() {
        return false;
    }

    public function getControlPanelSmartyTpl() {
        $smarty = $this->getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        return false;
    }


    // Get module javascript code
    public function getModuleJS() {
        return false;
    }

    // Get module css
    public function getModuleCSS() {
        return false;
    }


    /****
     * Get Navigational links for the top of the independent module page(s)
     * Get information in an array of sub-arrays with fields:
     * 'title': the title to appear on the link
     * 'image': the image to appear (if image inside module folder then use ($this -> moduleBaseDir) . 'imageFileName' -TODO
     * 'link': the url of the page to be from this link
     * Each sub-array represents a different link. Between them the "&raquo;" character is automatically inserted by the system
     * Example implementation:

        public function getNavigationLinks() {
            if (isset($_GET['subpage1'])) {
                return array (array ('title' => "Main Page" , 'link'  => $this -> moduleBaseUrl),
                              array ('title' => "Sub Page 1", 'link'  => $this -> moduleBaseUrl . "&operation=subpage1"));
            else {
                return false;   // Only the default page with the module Name as title will be returned
            }
        }
     */
     public function getNavigationLinks() {
        return false;
     }

    /****
     * Get links to be highlighted
     * Each time a module independent page is displayed a different link of the left sidebar can be highlighted
     * To do this return the id of the corresponding link as defined by your getSidebarLinkInfo() returned array
     * Example implementation:

        public function getLinkToHighlight() {
            if (isset($_GET['management'])) {
                return 'other_link_id1';
            } else {
                return 'other_link_id2';
            }
        }
     */
     public function getLinkToHighlight() {
        return false;
     }

    /****
     * Control Panel Module Link
     * Get information in an array with fields:
     * 'title': the title to appear on the link
     * 'image': the image to appear (if image inside module folder then use ($this -> moduleBaseDir) . 'imageFileName'
     * 'target': POPUP or innerTable (default Innertable) - TODO
     *
     *
     *  Example implementation:

        public function getCenterLinkInfo() {
            return array ('title' => 'My Module',
                          'image' => $this -> moduleBaseDir . 'images/my_module.jpg');
        }
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
     *
     *  Example implementation:

        public function getCenterLinkInfo() {
            return array ('title' => 'My Module',
                          'image' => $this -> moduleBaseDir . 'images/my_module.jpg');
        }
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

        public function getSidebarLinkInfo() {
            $link_of_menu_system = array   (array ('id'    => 'system_link_id',
                                                   'title' => 'My System Related Module Part 1',
                                                   'image' => '16x16/pens',                                 // no extension in the filename,
                                                   'eFrontExtensions' => '1',                               // question_type_free_text.png and pens.gif must exist in 16x16
                                                   'link'  => $this -> moduleBaseUrl . "&module_op=system_operation"),
                                            array ('id'    => 'system_link_id2',
                                                   'title' => 'My System Related Module Part 2',
                                                   'image' => '16x16/pencil2.png',
                                                   'link'  => $this -> moduleBaseUrl . "&module_op=system_operation"));

            $link_of_module_menus  = array ( array ('id'    => 'other_link_id1',
                                                    'title' => 'Main Module',
                                                    'image' => $this -> moduleBaseDir . 'images/my_module_pic', // no extension in the filename
                                                    'eFrontExtensions' => '1',                                  // my_module_pic.gif and my_module_pic.png must exist in $this->moduleBaseDir . 'images/'
                                                    'link'  => $this -> moduleBaseUrl),
                                             array ('id'    => 'other_link_id2',
                                                    'title' => 'Second Module Page',
                                                    'image' => '16x16/attachment.png',
                                                    'link'  => $this -> moduleBaseUrl . '&module_operat=2'));

            return array ( "system" => $link_of_menu_system,
                           "other"  => array('menuTitle' => 'My Module Menu', 'links' => $link_of_module_menus));
        }
    */
    public function getSidebarLinkInfo() {
        return false;
    }

    //the following two can also become a module-aspect in User
    // Code to execute when a user with login = $login has been registered
    public function onNewUser($login) {
        return false;
    }

    // Code to execute when a user with login = $login is deleted
    public function onDeleteUser($login) {
        return false;
    }

    //the following two can also become a module-aspect in Lesson
    // Code to execute when a lesson with id = $lessonId has been registered
    public function onNewLesson($lessonId) {
        return false;
    }

    // Code to execute when a lesson with id = $lessonId is deleted
    public function onDeleteLesson($lessonId) {
        return false;
    }

    // Code to execute when a lesson with id = $lessonId is exported. This
    // function should return an array with all information (like DB values)
    // that need to be stored into the exported lesson file
    /*  Example implementation:
            public function onExportLesson($lessonId) {
                $data = eF_getTableData("myModule", "*", "lessons_ID = $lessonId");
                $data['myModuleVersion'] = "3.5beta";
                return $data;
            }
     */
    public function onExportLesson($lessonId) {
        return false;
    }


    // Code to execute when a lesson with id = $lessonId is imported. This
    // function gets $data as argument which is in the exact same format
    // as it was exported by the onExportLesson function.
    /*  Example implementation (in accordance with the above given export example):
            public function onExportLesson($lessonId, $data) {
                echo "My module's version is " . $data['myModuleVersion'];
                unset($data['myModuleVersion']);

                foreach ($data as $record) {
                    eF_insertTableData("myModule", $record);
                }
                return true;
            }
     */
    public function onImportLesson($lessonId, $data) {
        return false;
    }

	public function onCompleteLesson($lessonId, $login) {
        return false;
    } 

    // For system events - every time a new page is loaded
    public function onNewPageLoad() {
        return false;
    }  
}

?>