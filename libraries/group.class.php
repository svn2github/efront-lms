<?php
/**
 *
 */

/**
 * EfrontGroupException class
 *
 * This class extends Exception class and is used to issue errors regarding groups
 * @author Antonellis Panagiotis <antonellis@efront.gr>
 * @version 1.0
 */
class EfrontGroupException extends Exception
{
    const NO_ERROR          = 0;
    const GROUP_NOT_EXISTS  = 301;
    const INVALID_ID        = 302;
    const INVALID_USER      = 303;
}

/**
 * EfrontGroup class
 *
 * This class represents a group
 * @author Antonellis Panagiotis <antonelli@efront.gr>
 * @version 1.0
 */
class EfrontGroup
{
    /**
     * The group array.
     *
     * @since 1.0
     * @var array
     * @access protected
     */
    public $group = array();

    /**
     * The group users. Calling getUsers() initializes it; otherwise, it evaluates to false.
     *
     * @since 1.0
     * @var array
     * @access protected
     */
    protected $users = false;

    
    /**
     * Class constructor
     *
     * This function is used to instantiate the class. The instatiation is done
     * based on a group id. If an entry with this id is not found in the database,
     * an EfrontGroupException is thrown.
     * <br/>Example:
     * <code>
     * $group = new EfrontGroup(32);                     //32 is a group id
     * </code>
     *
     * @param int $group_id The group id
     * @since 1.0
     * @access public
     */
    function __construct($group_id) {
        if (!eF_checkParameter($group_id, 'id')) {
            throw new EfrontGroupException(_INVALIDID, EfrontGroupException :: INVALID_ID);
        }
        $group = eF_getTableData("groups", "*", "id = $group_id");
        if (sizeof($group) == 0) {
            throw new EfrontGroupException(_GROUPDOESNOTEXIST, EfrontGroupException :: GROUP_NOT_EXISTS);
        } else {
            $this -> group    = $group[0];
        }
    }

    /**
     * Create group
     *
     * This function creates a new group. This involves creating
     * the database instance..
     * The function argument is an array with field values, corresponding to database
     * fields. All fields are optional, and if absent they are filled with default values,
     * but 'name' is strongly recommended to be defined
     * <br/>Example:
     * <code>
     * $fields = array('name' => 'Test group', 'description' => 'Description of the group');
     * try {
     *   $newGroup = EfrontGroup :: create($fields);                     //$newgroup is now a new group object
     * } catch (Exception $e) {
     *   echo $e -> getMessage();
     * }
     * </code><br/>
     * 
     * @param array $fields The new group characteristics
     * @return EfrontGroup the new group object
     * @since 1.0
     * @access public
     * @static
     */
    public static function create($fields) {
        //These are the mandatory fields. In case one of these is absent, fill it in with a default value
        !isset($fields['name'])           ? $fields['name']           = 'Default name'                                : null;
 
        $group_id = eF_insertTableData("groups", $fields);                                    //Insert the group to the database
        $newGroup = new EfrontGroup($group_id);
        return $newGroup;
    }

    /**
     * Delete group
     *
     * This function is used to delete an existing group. In order to do
     * this, it caclulates all the group dependendant elements, deletes them
     * and finally deletes the group itself.
     *
     * <br/>Example:
     * <code>
     * try {
     *   $group = new EfrontGroup(32);                     //32 is the group id
     *   $group -> delete();
     * } catch (Exception $e) {
     *   echo $e -> getMessage();
     * }
     * </code>
     *
     * @return boolean True if everything is ok
     * @since 1.0
     * @access public
     */
    public function delete() {
        ef_deleteTableData("users_to_groups", "groups_ID=".$this -> group['id']);
        eF_deleteTableData("groups", "id=".$this -> group['id']);
        return true;
    }

    /**
     * Get group users
     *
     * This function returns an array with the group users. The array
     * has 2 sub-arrays, 'student' and 'professor', each of which holding
     * a list of user logins. If the optional $type is specified, then only
     * a one-dimensional array is returned, holding the group users of the
     * specified type.
     * <br/>Example:
     * <code>
     * try {
     *   $group     = new EfrontGroup(32);                     //32 is the group id
     *   $all_users  = $group -> getUsers();
     *   $professors = $group -> getUsers('professor');
     * } catch (Exception $e) {
     *   echo $e -> getMessage();
     * }
     * </code><br/>
     * One thing to note is that this function caches results throughout the life
     * cycle of the object. That is, if some other function updates the group users,
     * the function results will not be altered, unless $refresh is set to true
     *
     * @param string $type The group users type
     * @param boolean $refresh Whether to explicitly refresh the object cached data set
     * @return array A 2-dimensional array with group users per type, or a 1-dimensional array with group users of the specified type
     * @since 1.0
     * @access public
     */
    public function getUsers($type = false, $refresh = false) {
        if ($this -> users === false || $refresh) {                //Make a database query only if the variable is not initialized, or it is explicitly asked
            $this -> users = array('professor' => array(), 'student' => array());
            $result = eF_getTableData("users_to_groups ug, users u", "ug.*, u.user_type", "u.login=ug.users_LOGIN and groups_ID=".$this -> group['id']);
            foreach ($result as $value) {
                $this -> users[$value['user_type']][] = $value['users_LOGIN'];
            }
         }

        if ($type) {
            return $this -> users[$type];
        } else {
            return $this -> users;
        }
    }

    
    
    public function addUser($login) {
        if (!eF_checkParameter(login, 'login')) {
            throw new EfrontGroupException(_INVALIDUSER, EfrontGroupException :: INVALID_USER);
        }
        $fields = array();
        $fields['groups_ID'] = $this -> group['id'];
        $fields['users_LOGIN'] = $login;
        $ok = eF_insertTableData("users_to_groups", $fields);
        return $ok;
    }
    
    
    public function deleteUser($login) {
        if (!eF_checkParameter(login, 'login')) {
            throw new EfrontGroupException(_INVALIDUSER, EfrontGroupException :: INVALID_USER);
        }
        $ok = eF_deleteTableData("users_to_groups", "users_LOGIN='".$login."' and groups_ID=".$this -> group['id']);
        return $ok;
    }
    
   
    public function setData($fields, $persist = false) {
        if (sizeof($fields['name']) > 0)
            $this -> group['name'] = $fields['name'];
        if (sizeof($fields['description']) > 0)
            $this -> group['description'] = $fields['description'];
        if (sizeof($fields['active']) > 0)
            $this -> group['active'] = $fields['active'];
        if ($persist) {
            $this -> persist();
        }
    }
    
    /**
     * Persist group values
     * 
     * This function is used to persist any changes made to the current 
     * group.
     * <br/>Example:
     * <code>
     * $group = new EfrontGroup(3);										//Instantiate group with id 3
     * $group -> group['name'] = 'new name';							//Change a group's value
     * $group -> persist();												//Store changes values to the database
     * </code>
     * 
     * @return boolean True if everything is ok
     * @since 3.5.0
     * @access public
     */
    public function persist() {
        $fields['name']        = $this -> group['name'];
        $fields['description'] = $this -> group['description'];
        $fields['active']      = $this -> group['active'];
        
        $ok = eF_updateTableData("groups", $fields, "id=".$this -> group['id']);
        return $ok;
    }


   /**
     * Returns the existing groups
     * 
     * This function returns the existing groups
     * group.
     * <br/>Example:
     * <code>
     * $groups -> EfrontGroup :: getGroups();							
     * </code>
     * 
     * @param boolean Flat to indicate whether 
     * @param boolean Flag to indicate whether to return disabled groups
     * @return array An array of groups. Each element is the group array
     * @since 3.5.0
     * @access public
     */    
    public static function getGroups($returnObjects = false, $returnDisabled = false){
        $groups = array();
        if ($returnDisabled){
            $data = ef_getTableData("groups", "id, name");
        }
        else{
            $data = ef_getTableData("groups", "id, name", "active = 1");
        }
        if ($returnObjects){
            foreach ($data as $group_info){
                $group = new EfrontGroup($group_info['id']);
                $groups[$group_info['id']] = $group;
            }
        }
        else{
            foreach ($data as $group_info){
                $groups[] = $group_info;
            }
        }
        return $groups;
    }
}




?>