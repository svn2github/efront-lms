<?php
/**
* EfrontMenu Class file
*
* @package eFront
* @version 1.0
*/

/**
 * EfrontMenuException class
 *
 * This class extends Exception class and is used to issue errors regarding lessons
 * @author Nick Baltas <mpaltas@efront.gr>
 * @version 1.0
 */
class EfrontMenuException extends Exception
{
    const NO_ERROR          = 0;
    const MENU_NOT_EXISTS = 201;
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
 * EfrontMenu class
 *
 * This class represents a menu
 * @author Baltas Nick <mpaltas@efront.gr>
 * @version 1.0
 */
class EfrontMenu
{

    public $menuCount;

    /**
     * The menu array. A two dimensional array of menus
     *
     * @since 3.5.0
     * @var array
     * @access public
     */
    public $menu = array();

    function __construct() {
        $this -> menuCount = 1;
    }


    /**
     * Function that creates a menu with the given attributes
     *
     * Example:
     * <code>
     * $menu = new EfrontMenu();
     * $newMenuId = $menu -> createMenu( array("title" => _LESSONS, "image" => "user1")); // Create a menu with title Lessons and that image
     * $menu -> insertMenuOption (array(...), false, $newMenuId);
     *
     * @param: menuAttributes is an array of fields
     * $return: the new menu id
     *
     */
    public function createMenu($menuAttributes) {
        // The menu id may be one of the attributes
        isset($menuAttributes['id']) ? $id = $menuAttributes['id']: $id = $this -> menuCount++;

        foreach ($menuAttributes as $key => $value) {
            $this -> menu[$id][$key] = $value;
        }

        return $id;
    }


    /**
     * Function to add another Option in the menu item
     * the $menuOptions is an array with the following fields
     * 'id': the id of the menu
     * 'image': the image of the menu item without the extension
     * 'link': the href of the link pointed to by
     * 'title': the title of the link
     *
     * Example:
     * <code>
     * $menu = new EfrontMenu();
     * $menu -> insertMenuOption( array ("id" => "users_a", "image" => "user1", "link" => "administrator.php?ctg=users", "target" => "mainframe", "title" => _USERS), false, _USERS, "user1");
     * $menu -> insertMenuOption( array ("id" => "users_a", "image" => "user1", "link" => "administrator.php?ctg=users", "target" => "mainframe", "title" => _USERS) , 'menu_with_user' );
     * $menu -> insertMenuOption( array (array ("id" => "users_a", "image" => "user1", "link" => "administrator.php?ctg=users", "target" => "mainframe", "title" => _USERS), array ("id" => "employees_a", "image" => "user2", "href" => "administrator.php?ctg=employees", "target" => "mainframe", false, _EMPLOYEES));
     * </code>
     *
     * @param: $menuOptions the array with all values. May also be an array of arrays
     * @param: $specific_menuID optional parameter to assign a specific name to the menu
     * @return: false if the menu had wrong fields otherwise the id of the new menu
     */
    public function insertMenuOption($menuOptions, $specific_menuID = false, $menuTitle = false, $menuImg = false) {

        if (isset($menuOptions['id'])) {
            (!$specific_menuID)? $menuId = $this -> menuCount++ : $menuId = $specific_menuID;

            if (!isset($menuOptions['target'])) {
                $menuOptions['target'] = "mainframe";
            }


            if (!isset($menuOptions['link'])) {
                $menuOptions['link'] = "#";
            }

            $optionId = $menuOptions['id'];
            $this -> menu[$menuId]['options'][$optionId]  = $menuOptions;
        } else if (is_array(end($menuOptions))) {

            (!$specific_menuID)? $menuId = $this -> menuCount++ : $menuId = $specific_menuID;

            foreach ($menuOptions as $menuOption) {
                if (!isset($menuOption['target'])) {
                    $menuOption['target'] = "mainframe";
                }


                if (!isset($menuOption['link'])) {
                    $menuOption['link'] = "#";
                }

                $optionId = $menuOption['id'];
                $this -> menu[$menuId]['options'][$optionId] = $menuOption;
            }


        } else {
            return false;
        }

        if ($menuTitle) {
            $this -> menu[$menuId]["title"] = $menuTitle;
        }
        if ($menuImg) {

            $this -> menu[$menuId]["image"] = $menuImg;
        }
        return $menuId -1 ;
    }


    /**
     * Function to insert a row in a specific menu that has a prefined HTML code
     *
     * Example:
     * <code>
     * $menu = new EfrontMenu();
     * $newMenuId = $menu -> createMenu( array("title" => _LESSONS, "image" => "user1")); // Create a menu with title Lessons and that image
     * $newMenu -> insertMenuOptionAsRawHtml("<table height='8px'></table>", $newMenuId); // add this table - blank line
     *
     * @param: $menuId the id of the menu
     * @return: boolean false if the menu had wrong fields otherwise true
     */
     public function insertMenuOptionAsRawHtml($htmlCode, $menuId) {
        if ($menuId) {
            $this -> menu[$menuId]['options'][]['html'] = $htmlCode;
            return true;
        } else {
            return false;
        }
     }

    /**
     * Function which adds an image to the header of a menu
     * Attention: NOT to the left of a menu option
     *
     * Example:
     * <code>
     * $menu = new EfrontMenu();
     * $id = $menu -> insertMenuOption( array ("id" => "users_a", "image" => "user1", "link" => "administrator.php?ctg=users", "target" => "mainframe", "title" => _USERS) );
     * $menu -> addMenuImage($id, "user1");
     * </code>
     *
     * @param: $menuId the ID of the menu to assign the image to
     * @param: $img the image to be assigned without its extension
     */
    public function addMenuImage($menuId, $img) {
        if ($this -> menu[$menuId]) {
            $this -> menu[$menuId]["image"] = $img;
        }

    }


    /**
     * Function that creates the system menu
     * Only administrators may have system menus
     */
    public function addSystemMenu() {

        $systemMenu = array();
        $systemMenu[0] = array("id" => "control_panel_a", "image" => "home", "link" => "administrator.php?ctg=control_panel", "title" => _CONTROLCENTER);
        $systemMenu[1] = array("id" => "forum_a",         "image" => "messages", "link" => "forum/forum_index.php", "title" => _FORUM);
        $systemMenu[2] = array("id" => "cms_a", "image" => "document_text", "link" => "administrator.php?ctg=cms", "title" => _CMS);
        $systemMenu[3] = array("id" => "chat_a", "image" => "user1_message", "link" => "chat/chat_index.php", "title" => _CHAT);

        if (!$GLOBALS['currentUser'] -> coreAccess['statistics'] || $GLOBALS['currentUser'] -> coreAccess['statistics'] != 'hidden') {
            $systemMenu[4] = array("id" => "statistics_system_a", "image" => "chart", "link" => "administrator.php?ctg=statistics&option=system", "title" => _SYSTEMSTATISTICS);
        }
        $this -> insertMenuOption($systemMenu, false, _SYSTEM);
    }


    /**
     * Function that creates the users menu
     * Only administrators may have users menus
     */
    public function addUsersMenu() {

        $usersMenu = array();
        $usersMenu[0] = array("id" => "users_a", "image" => "user1", "link" => "administrator.php?ctg=users", "title" => _USERS);
        //$usersMenu[1] = array("id" => "user_types_a",         "image" => "users_family", "link" => "administrator.php?ctg=user_types", "title" => _ROLES);
        $usersMenu[2] = array("id" => "user_groups_a", "image" => "users3", "link" => "administrator.php?ctg=user_groups", "title" => _GROUPS);
        $usersMenu[3] = array("id" => "statistics_user_a", "image" => "chart", "link" => "administrator.php?ctg=statistics&option=user", "title" => _USERSTATISTICS);

        if (MODULE_HCD_INTERFACE) {
            $usersMenu[4] = array("id" => "search_employee_a", "image" => "book_red", "link" => "administrator.php?ctg=module_hcd&op=reports", "title" => _SEARCHFOREMPLOYEE);
        }
        $this -> insertMenuOption($usersMenu, false, _USERS);
    }

}

?>