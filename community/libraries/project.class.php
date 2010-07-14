<?php
/**

* EfrontProject Class file

*

* @package eFront

* @version 3.6

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * Class for projects

 * 

 * @package eFront

 */
class EfrontProject {
    /**

     * The number of users that has done this project

     *

     * @var int

     * @since 3.5.0

     * @access public

     */
    public $doneUsers = 0;
    /**

     * The number of users that have not done this project

     *

     * @var int

     * @since 3.5.0

     * @access public

     */
    public $pendingUsers = 0;
    /**

     * Project deadline in human-readable format

     *

     * @var string

     * @since 3.5.0

     * @access public

     */
    public $timeRemaining = '';
    /**

     * The project users

     * 

     * @since 3.5.0

     * @var array

     * @access protected

     */
    protected $users = false;
    /**

     * The project properties

     * 

     * @since 3.5.0

     * @var array

     * @access public

     */
    public $project = array();
    /**

     * Initialize project

     * 

     * This function is used to initialize the designated project

     * 

     * @param $project The project initialization data

     * @since 3.5.0

     * @access public

     */
    function __construct($project) {
        if (!is_array($project)) {
         if (!eF_checkParameter($project, 'id')) {
             throw new EfrontContentException(_INVALIDPROJECTID.': '.$project, EfrontContentException :: INVALID_ID);
         }
         $project = eF_getTableData("projects", "*", "id=$project");
         if (sizeof($project) == 0) {
             throw new EfrontContentException(_PROJECTNOTFOUND.': '.$project, EfrontContentException :: PROJECT_NOT_EXISTS);
         }
         $this -> project = $project[0];
        } else {
            $this -> project = $project;
        }
        $this -> timeRemaining = eF_convertIntervalToTime($this -> project['deadline'] - time(), true);
    }
    /**

     * Get users for project

     * 

     * This function returns a list of users that this project

     * is assigned to.

     * <br/>Example:

     * <code>

     * $project      = new EfrontProject(32);               //Initialize project with id 32

     * $projectUsers = $project -> getUsers();              //Get a list of users that have this project assigned

     * </code>

     * This object caches data; Set $refresh to true in order to receive a clean copy

     * 

     * @param $refresh Whether to refresh cache information

     * @return array The list of users

     * @since 3.5.0

     * @access public

     */
    public function getUsers() {
        if ($this -> users === false) {
            $result = eF_getTableData("users_to_projects up, users", "up.*, users.name, users.surname, users.active", "users.login=up.users_LOGIN and projects_ID=".$this -> project['id']);
            foreach ($result as $value) {
                $projectUsers[$value['users_LOGIN']] = $value;
                $value['filename'] ? $this -> doneUsers++ : $this -> pendingUsers++;
            }
            $this -> users = $projectUsers;
        }
        return $this -> users;
    }
    /**

     * Assign project to users

     * 

     * This function is used to assign one or more users to the

     * current project.

     * <br/>Example:

     * <code>

     * $project = new EfrontProject(32);                    //Initialize project with id 32

     * $project -> addUsers('jdoe');                        //Assign project to user 'jdoe'

     * $project -> addUsers(array('john', 'george'));       //Assign project to users 'john' and 'george'

     * </code>

     * 

     * @param mixed The users to add to the project, may be a single login or an array of logins

     * @return array The new projet users list;

     * @since 3.5.0

     * @access public

     */
    public function addUsers($login) {
        if (!is_array($login)) {
            $login = array($login);
        }
        $projectLesson = new EfrontLesson($this -> project['lessons_ID']);
        $lessonUsers = $projectLesson -> getUsers('student');
        foreach ($login as $value) {
            if (in_array($value, array_keys($lessonUsers)) && !in_array($value, array_keys($this -> getUsers())) && eF_checkParameter($value, 'login')) {
                eF_insertTableData("users_to_projects", array("users_LOGIN" => $value, "projects_ID" => $this -> project['id']));
            }
        }
        return $this -> getUsers(true);
    }
    /**

     * Remove project from users

     * 

     * This function is used to remove assignment of one or more users from the

     * current project.

     * <br/>Example:

     * <code>

     * $project = new EfrontProject(32);                    //Initialize project with id 32

     * $project -> removeUsers('jdoe');                     //Remove project from user 'jdoe'

     * $project -> removeUsers(array('john', 'george'));    //Remove project from users 'john' and 'george'

     * </code>

     * 

     * @param mixed The users to remove from the project, may be a single login or an array of logins

     * @return array The new projet users list;

     * @since 3.5.0

     * @access public

     */
    public function removeUsers($login) {
        if (!is_array($login)) {
            $login = array($login);
        }
        $projectLesson = new EfrontLesson($this -> project['lessons_ID']);
        $lessonUsers = $projectLesson -> getUsers('student');
        foreach ($login as $value) {
            if (in_array($value, array_keys($lessonUsers)) && in_array($value, array_keys($this -> users)) && eF_checkParameter($value, 'login')) {
                eF_deleteTableData("users_to_projects", "users_LOGIN = '$value' and projects_ID = ".$this -> project['id']);
            }
        }
        return $this -> getUsers(true);
    }
    /**

     * Grade user

     * 

     * This function is used to grade the project that a user uploaded

     * <br>Example:

     * <code>

     * $project = new EfrontProject(43);                //Instantiate project with id 43

     * $project -> grade('jdoe', 54, 'Mediocre try');   //Put a grade for user jdoe

     * </code>

     *

     * @param string $login The user to grad

     * @param int $grade The user grade, an integer 0-100

     * @param string $comments Comments for the grade

     * @return boolean true if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function grade($login, $grade, $comments) {
        if (!in_array($login, array_keys($this -> getUsers()))) {
            throw new EfrontContentException(_USERDOESNOTHAVETHISPROJECT, EfrontContentException :: INVALID_LOGIN);
        }
        if (eF_checkParameter($grade, 'uint') === false || $grade > 100) {
            throw new EfrontContentException(_INVALIDSCORE.': "'.$grade.'" '._SCOREMUSTBEINTEGERBETWEEN0100, EfrontContentException :: INVALID_SCORE);
        }
        if ($comments && !eF_checkParameter($comments, 'text')) {
            throw new EfrontContentException(_INVALIDDATA.': '.$comments, EfrontContentException :: INVALID_DATA);
        }
        if (eF_updateTableData("users_to_projects", array('grade' => $grade, 'comments' => $comments), "users_LOGIN='$login' and projects_ID=".$this -> project['id'])) {
            return true;
        } else {
            throw new EfrontContentException(_THEPROJECTGRADECOULDNOTBEUPDATED, EfrontContentException :: DATABASE_ERROR);
        }
    }
    /**

     * Get project files

     *

     * This function returns a list of file ids that have been posted

     * for this project. this list includes the user name and surname, 

     * the id, full path and name of the file and finally the time it 

     * was uploaded.

     * <br/>Example:

     * <code>

     * $project -> getFiles();

     * </code>

     * 

     * @return array The list of files posted for this project

     * @since 3.5.0

     * @access public

     */
    public function getFiles() {
        $files = eF_getTableData("files f, users u, users_to_projects up", "f.*, u.name, u.surname, u.login, up.upload_timestamp", "up.filename = f.id and up.users_LOGIN = u.login and up.projects_ID=".$this -> project['id']);
        return $files;
    }
    /**

     * Create a new project

     * 

     * This function is used to create a new project,

     * based on the specified values.

     * <br/>Example:

     * <code>

     * </code>

     * 

     * @param array $fields The new project properties

     * @return EfrontProject The new project

     * @since 3.5.0

     * @access public

     */
    public static function createProject($fields = array()) {
        $projectMetadata = array('title' => $fields['title'],
                                 'creator' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                 'publisher' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                 'contributor' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                 'date' => date("Y/m/d", time()),
                                 'type' => 'project');
        $fields['metadata'] = serialize($projectMetadata);
        $newId = eF_insertTableData("projects", $fields);
        $result = eF_getTableData("projects", "*", "id=".$newId); //We perform an extra step/query for retrieving data, sinve this way we make sure that the array fields will be in correct order (forst id, then name, etc)
        $project = new EfrontProject($result[0]['id']);
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::PROJECT_CREATION, "users_LOGIN" => $GLOBALS['currentUser'] -> user['login'], "users_name" => $GLOBALS['currentUser'] -> user['name'], "users_surname" => $GLOBALS['currentUser'] -> user['surname'], "lessons_ID" => $GLOBALS['currentLesson'] -> lesson['id'], "lessons_name" => $GLOBALS['currentLesson'] -> lesson['name'], "entity_name" => $fields['title'], "entity_ID" => $newId));
        return $project;
    }
    /**

     * Persist project properties

     * 

     * This function can be used to persist with the database 

     * any changes made to the current project object.

     * <br/>Example:

     * <code>

     * $project -> project['title'] = 'new Title';              //Change the project title

     * $project -> persist();                                   //Make the change permanent

     * </code>

     * 

     * @return boolean True if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function persist() {
        eF_updateTableData("projects", $this -> project, "id=".$this -> project['id']);
    }
    /**

     * Delete the project

     * 

     * This function is used to delete the current project.

     * All related information is lost, as well as files associated

     * with the project.

     * <br/>Example:

     * <code>

     * $project = new EfrontProject(12);                //Instantiate project with id 12

     * $project -> delete();                            //Delete project and all associated information

     * </code>

     * 

     * @since 3.5.0

     * @access public

     * @todo delete project files

     */
    public function delete() {
        foreach ($this -> getUsers() as $value) {
            if ($value['filename']) {
                $file = new EfrontFile($value['filename']);
                $file -> delete();
            }
        }
        eF_deleteTableData("users_to_projects", "projects_ID=".$this -> project['id']);
        eF_deleteTableData("projects", "id=".$this -> project['id']);
    }
    /**

     * Reset the project completion

     * 

     * This function is used to reset the current project completion.

     * All related information is lost, as well as files associated

     * with the project.

     * <br/>Example:

     * <code>

     * $project = new EfrontProject(12);                //Instantiate project with id 12

     * $project -> reset($login);                            //Reset project completion for $login and all associated information

     * </code>

     * 

     * @since 3.5.0

     * @access public

     * @todo delete project files

     */
    public function reset($login = false) {
  $users = $this -> getUsers();
  if (!in_array($login, array_keys($users))) {
   throw new EfrontContentException(_USERDOESNOTHAVETHISPROJECT, EfrontContentException :: INVALID_LOGIN);
  }
  if (eF_updateTableData("users_to_projects", array('grade' => NULL, 'comments' => NULL, 'upload_timestamp' => NULL, 'filename' => NULL), "users_LOGIN='$login' and projects_ID=".$this -> project['id'])) {
            $file = new EfrontFile($users[$login]['filename']);
   $file -> delete();
   return true;
        } else {
            throw new EfrontContentException(_THEPROJECTGRADECOULDNOTBEUPDATED, EfrontContentException :: DATABASE_ERROR);
        }
    }
}
?>
