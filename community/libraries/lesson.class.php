<?php
/**

* EfrontLesson Class file

*

* @package eFront

* @version 3.5.0

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * EfrontLessonException class

 *

 * This class extends Exception class and is used to issue errors regarding lessons

 * @author Venakis Periklis <pvenakis@efront.gr>

 * @package eFront

 * @version 1.0

 */
class EfrontLessonException extends Exception
{
    const NO_ERROR = 0;
    const LESSON_NOT_EXISTS = 201;
    const INVALID_ID = 202;
    const CANNOT_CREATE_DIR = 203;
    const INVALID_LOGIN = 204;
    const DATABASE_ERROR = 205;
    const DIR_NOT_EXISTS = 206;
    const FILESYSTEM_ERROR = 207;
    const DIRECTION_NOT_EXISTS = 208;
    const MAX_USERS_LIMIT = 209;
    const CATEGORY_NOT_EXISTS = 210;
    const GENERAL_ERROR = 299;
}
/**

 * EfrontLesson class

 *

 * This class represents a lesson

 * @author Venakis Periklis <pvenakis@efront.gr>

 * @package eFront

 * @version 1.0

 */
class EfrontLesson
{
    /**

     * The lesson array.

     *

     * @since 3.5.0

     * @var array

     * @access public

     */
    public $lesson = array();
    /**

     * The lesson users. Calling getUsers() initializes it; otherwise, it evaluates to false.

     *

     * @since 3.5.0

     * @var array

     * @access protected

     */
    protected $users = false;
    /**

     * The lesson conditions. Calling getConditions() initializes it; otherwise, it evaluates to false.

     *

     * @since 3.5.0

     * @var array

     * @access protected

     * @see getConditions()

     */
    protected $conditions = false;
    /**

     * The lesson directory, equals G_LESSONSPATH.$lessonId

     *

     * @since 3.5.0

     * @var string

     * @access protected

     */
    protected $directory = '';
    /**

     * Default lesson options

     *

     * @since 3.5.0

     * @var array

     * @access public

     */
    public $options = array('theory' => 1,
                            'examples' => 1,
                            'projects' => 1,
                            'tests' => 1,
                            'survey' => 1,
                            'rules' => 1,
                            'forum' => 1,
                            'comments' => 1,
                            'news' => 1,
                            'online' => 1,
                            'chat' => 1,
                            'scorm' => 1,
                            'dynamic_periods' => 0,
                            'digital_library' => 1,
                            'calendar' => 1,
                            'new_content' => 1,
                            'glossary' => 1,
       'reports' => 1,
                            'tracking' => 1,
                            'auto_complete' => 0,
                            'content_tree' => 1,
                            'lesson_info' => 1,
       'bookmarking' => 1,
       'content_report' => 0,
          'print_content' => 1,
          'start_resume' => 1,
                            'show_percentage' => 1,
                            'show_right_bar' => 1,
                            'show_left_bar' => 0,
                            'show_student_cpanel' => 1,
                            'recurring' => 0,
                            'recurring_duration' => 0,
                            'show_content_tools' => 1,
                            'show_dashboard' => 1,
          'show_horizontal_bar' => 1,
                            //'complete_next_lesson'=> 0,
          'default_positions' => '');
    /**

     * Class constructor

     *

     * This function is used to instantiate the class. The instatiation is done

     * based on a lesson id or a lesson array.

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(32);                     //32 is a lesson id

     * $result = eF_getTableData("lessons", "*", "id=32"); //Get lesson data from databasse

     * $lesson = new EfrontLesson($result[0]);             //Instantiate using prepared data

     * </code>

     *

     * @param int $lesson Either the lesson id or the lesson data

     * @since 3.5.0

     * @access public

     */
    function __construct($lesson) {
        if (!is_array($lesson)) {
            if (!eF_checkParameter($lesson, 'id')) {
                throw new EfrontLessonException(_INVALIDID.": $lesson", EfrontLessonException :: INVALID_ID);
            }
            $result = eF_getTableData("lessons", "*", "id = $lesson");
            if (sizeof($result) == 0) {
                throw new EfrontLessonException(_LESSONDOESNOTEXIST.": $lesson", EfrontLessonException :: LESSON_NOT_EXISTS);
            }
            $this -> lesson = $result[0];
        } else {
            $this -> lesson = $lesson;
        }
        if (!is_dir(G_LESSONSPATH.$this -> lesson['id'])) {
            mkdir(G_LESSONSPATH.$this -> lesson['id'], 0755);
        }
        if ($this -> lesson['options'] && $options = unserialize($this -> lesson['options'])) {
            $newOptions = array_diff_key($this -> options, $options); //$newOptions are lesson options that were added to the EfrontLesson object AFTER the lesson options serialization took place
            $this -> options = $options + $newOptions; //Set lesson options
        }
  if ($this -> lesson['share_folder'] && is_dir(G_LESSONSPATH.$this -> lesson['share_folder'])) {
      $this -> directory = G_LESSONSPATH.$this -> lesson['share_folder'].'/';
  } else {
      $this -> directory = G_LESSONSPATH.$this -> lesson['id'].'/';
  }
        if ($this -> lesson['price']) { //Create the string representing the lesson price
   isset($this -> options['recurring']) && $this -> options['recurring'] ? $recurring = array($this -> options['recurring'], $this -> options['recurring_duration']) : $recurring = false;
            $this -> lesson['price_string'] = formatPrice($this -> lesson['price'], $recurring);
  } else {
      $this -> lesson['price_string'] = formatPrice(0);
  }
    }
    /**

     * Create lesson

     *

     * This function created a new lesson. This involves creating

     * the database instance, creating the corresponding filesystem

     * folder (read below) and finally creating corresponding forum and chat entries

     * as well as any other information needed.

     * The function argument is an array with field values, corresponding to database

     * fields. All fields are optional, and if absent they are filled with default values,

     * but 'name', 'languages_NAME' and 'directions_ID' are strongly recommended to be defined

     * <br/>Example:

     * <code>

     * $fields = array('name' => 'Test lesson', 'directions_ID' => 2, 'languages_NAME' => 'english');

     * try {

     *   $newLesson = EfrontLesson :: createLesson($fields);                     //$newLesson is now a new lesson object

     * } catch (Exception $e) {

     *   echo $e -> getMessage();

     * }

     * </code><br/>

     * When a lesson is created, a folder named after the lesson id is created

     * inside the G_LESSONSPATH folder. For example, if G_LESSONSPATH is

     * /var/www/efront/www/content/lessons/ and the new lesson gets the id 35,

     * a corresponding folder will be created: /var/www/efront/www/content/lessons/35/

     *

     * @param array $fields The new lesson characteristics

     * @return EfrontLesson the new lesson object

     * @since 3.5.0

     * @access public

     * @static

     */
    public static function createLesson($fields) {
        is_dir(G_LESSONSPATH) || mkdir(G_LESSONSPATH, 0755);
        //These are the mandatory fields. In case one of these is absent, fill it in with a default value
        !isset($fields['name']) ? $fields['name'] = 'Default name' : null;
        !isset($fields['languages_NAME']) ? $fields['languages_NAME'] = $GLOBALS['configuration']['default_language'] : null;
        if (!isset($fields['directions_ID'])) {
            $directions = eF_getTableData("directions", "id");
            sizeof($directions) > 0 ? $fields['directions_ID'] = $directions[0]['id'] : $fields['directions_ID'] = 1;
        }
        $fields['created'] = time();
        $languages = EfrontSystem :: getLanguages(true);
        $lessonMetadata = array('title' => $fields['name'],
                                'creator' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                'publisher' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                'contributor' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                'date' => date("Y/m/d", time()),
                                'language' => $languages[$fields['languages_NAME']],
                                'type' => 'lesson');
        $fields['metadata'] = serialize($lessonMetadata);
        $lessonId = eF_insertTableData("lessons", $fields); //Insert the lesson to the database
        //If a folder with the name $lessonId already exists, delete the lessons entry and retry, so that a new id will be used
        while (is_dir(G_LESSONSPATH.$lessonId) && $q++ < 10000 && !isset($fields['id'])) { //$q is put here to prevent infinite loops
            $newLessonId = eF_insertTableData("lessons", $fields); //"Issue" the new id by inserting a new database entry
            eF_deleteTableData("lessons", "id=$lessonId"); //delete the previous entry
            $lessonId = $newLessonId;
        }
        if ($q == 10000) {
            return false;
        }
        EfrontSearch :: insertText($fields['name'], $lessonId, "lessons", "title");
        if (!mkdir(G_LESSONSPATH.$lessonId, 0755)) {
            eF_deleteTableData("lessons", "id=$lessonId"); //If the folder could not be created, delete the stored lesson database entry and then propagate the exception
            throw new EfrontLessonException(_LESSONDIRECTORYCANNOTBCREATED, EfrontLessonException :: CANNOT_CREATE_DIR);
        }
        //Create corresponding forum and chat entries for the new lesson
        $forumFields = array('title' => $fields['name'],
                             'lessons_ID' => $lessonId,
                             'parent_id' => 0,
                             'status' => 1,
                             'users_LOGIN' => $_SESSION['s_login'] ? $_SESSION['s_login'] : '',
                             'comments' => '');
        $forum_id = eF_insertTableData("f_forums", $forumFields);
        EfrontSearch :: insertText($fields['name'], $forum_id, "f_forums", "title");
        $chatFields = array('name' => $fields['name'],
                            'create_timestamp' => time(),
                            'type' => 'public',
                            'users_LOGIN' => $_SESSION['s_login'] ? $_SESSION['s_login'] : '',
                            'lessons_ID' => $lessonId,
                            'active' => 1);
        eF_insertTableData("chatrooms", $chatFields);
        $newLesson = new EfrontLesson($lessonId);
        ///MODULES1 - Module lesson add events
        // Get all modules (NOT only the ones that have to do with the user type)
        $modules = eF_loadAllModules();
        // Trigger all necessary events. If the function has not been re-defined in the derived module class, nothing will happen
        foreach ($modules as $module) {
            $module -> onNewLesson($lessonId);
        }
        return $newLesson;
    }
    /**

     * Archive lesson

     * 

     * This function is used to archive the lesson object, by setting its active status to 0 and its

     * archive status to 1

     * <br/>Example:

     * <code>

     * $lesson -> archive();	//Archives the lesson object

     * $lesson -> unarchive();	//Archives the lesson object and activates it as well 

     * </code>

     * 

     * @since 3.6.0

     * @access public

     */
    public function archive() {
        $this -> lesson['archive'] = time();
        $this -> lesson['active'] = 0;
        $this -> persist();
    }
    /**

     * Unarchive lesson

     * 

     * This function is used to unarchive the lesson object, by setting its active status to 1 and its

     * archive status to 0

     * <br/>Example:

     * <code>

     * $lesson -> archive();	//Archives the lesson object

     * $lesson -> unarchive();	//Archives the lesson object and activates it as well 

     * </code>

     * 

     * @since 3.6.0

     * @access public

     */
    public function unarchive() {
        $this -> lesson['archive'] = 0;
        $this -> lesson['active'] = 1;
        //Check whether the original category exists
        $result = eF_getTableDataFlat("directions", "id");
        if (in_array($this -> lesson['directions_ID'], $result['id'])) {
         // If the original category exists, no problem
         $this -> persist();
        } elseif (empty($result)) {
         //If no categories exist in the system, throw exception
         throw new EfrontLessonException(_NOCATEGORIESDEFINED, EfrontLessonException::CATEGORY_NOT_EXISTS);
        } else {
         //If some other category exists, assign it there
         $this -> lesson['directions_ID'] = $result['id'][0];
         $this -> persist();
        }
    }
    /**

     * Delete lesson

     *

     * This function is used to delete an existing lesson. In order to do

     * this, it caclulates all the lesson dependendant elements, deletes them

     * and finally deletes the lesson itself.

     *

     * <br/>Example:

     * <code>

     * try {

     *   $lesson = new EfrontLesson(32);                     //32 is the lesson id

     *   $lesson -> delete();

     * } catch (Exception $e) {

     *   echo $e -> getMessage();

     * }

     * </code>

     *

     * @return boolean True if everything is ok

     * @since 3.5.0

     * @access public

     * @todo: add getForums() to OO delete

     * @todo: add getChatrooms to OO delete

     */
    public function delete() {
        $id = $this -> lesson['id'];
        $this -> initialize('all');
        foreach ($this -> getCourses(true) as $course) {
         $course -> removeLessons($this -> lesson['id']);
        }
        eF_deleteTableData("events", "lessons_ID=".$this -> lesson['id']);
        eF_deleteTableData("lessons_to_groups", "lessons_ID=".$this -> lesson['id']);
        eF_deleteTableData("lessons_timeline_topics", "lessons_ID=".$this -> lesson['id']);
        $chatroom = eF_getTableData("chatrooms", "id", "lessons_ID=".$this -> lesson['id']); //Get the lesson chat room
        if (sizeof($chatroom) > 0) {
            eF_deleteTableData("chatmessages", "chatrooms_ID=".$chatroom[0]['id']); //Delete the chat room messages
            eF_deleteTableData("chatrooms", "id=".$chatroom[0]['id']); //Delete the lesson chatroom
        }
        //delete the forums of this lesson
        $lessons_forums = eF_getTableData("f_forums", "id","lessons_ID=$id");
        foreach($lessons_forums as $value) {
            $result_forums = eF_getTableData("f_forums", "*");
            $forum_tree = array();
            //Convert array to tree. At the end of the loop, the $forums array will hold the forum tree, where each node is an array of its child nodes
            while (sizeof($result_forums) > 0 && $count++ < 10000) { //$count is put here to prevent infinite loops
                $node = current($result_forums); //Get the key/node pairs of the first array element
                $key = key($result_forums);
                $parent_id = $node['parent_id'];
                $forum_tree[$parent_id][] = $node['id']; //Append to the tree array, at the forum id index, the id of its child
                $forum_tree[$node['id']] = array();
                $forums[$node['id']] = $node; //Copy node to forums, which will be used later as forums source
                unset($result_forums[$key]); //We visited the node, so delete it from the (array) graph
            }
            $children = $forum_tree[$value[id]]; //Get all the forum's direct siblings
            for ($i = 0; isset($children[$i]); $i++) { //Find all the forum siblings' siblings
                $children = array_merge ($children, $forum_tree[$children[$i]]);
            }
            $children[] = $value[id]; //Append the deleted forum to the childrens list
            $topics = eF_getTableDataFlat("f_topics", "id", "f_forums_ID in (".implode(",", $children).")"); //Get forums' topics
            if (sizeof($topics) > 0) { //Delete forums' messages and topics
                $fmid = eF_getTableDataFlat("f_messages", "id", "f_topics_ID in (".implode(",", $topics['id']).")");
                EfrontSearch :: removeText('f_messages', implode(",", $fmid['id']), '', true);
                eF_deleteTableData("f_messages", "topics_id in (".implode(",", $topics['id']).")");
                eF_deleteTableData("f_topics", "id in (".implode(",", $topics['id']).")");
            }
            $fpid = eF_getTableDataFlat("f_poll", "id", "f_forums_ID in (".implode(",", $children).")");
            if (sizeof($fmid) > 0) {
                EfrontSearch :: removeText('f_poll', implode(",", $fmid['id']), '', true);
            }
            if (sizeof($children) > 0) {
                eF_deleteTableData("f_poll", "f_forums_ID in (".implode(",", $children).")"); //Delete polls
                EfrontSearch :: removeText('f_forums', implode(",", $children), '', true);
                eF_deleteTableData("f_forums", "id in (".implode(",", $children).")"); //Finally, delete forums themselves
            }
        }
        eF_deleteTableData("lessons", "id=$id");
        EfrontSearch :: removeText('lessons', $id, '');
        return true;
    }
    /**

     * Returns the direction of the lesson

     *

     * This function is used to return the direction of the lesson

     * <br/>Example:

     * <code>

     * $direction_info = $lesson -> getDirection();

     * </code>

     *

     * @return array The returned array has two fields: id and name of the lesson's direction

     * @since 3.5.0

     * @access public

     */
    public function getDirection(){
        $result = array();
        $res = eF_getTableData("directions", "name", "id=".$this -> lesson['directions_ID']);
        $direction_name = $res[0]['name'];
        $result['id'] = $this -> lesson['directions_ID'];
        $result['name'] = $direction_name;
        return $result;
    }
    /**

     * Check if a lesson with a specified lesson_id exists

     *

     * This function is used to check if a lesson with a specified id exists

     * <br/>Example:

     * <code>

     * $flg -> EfrontLesson :: exists(2);

     * </code>

     *

     * @param  int  The lesson id to check

     * @return boolean True if the lesson exists

     * @since 3.5.0

     * @access public

     */
    public static function exists($lessonId){
        $res = ef_getTableData("lessons","id","id=".$lessonId);
        if (sizeof($res) > 0){
            return true;
        }
        else{
            return false;
        }
    }
    /**

     * Activate lesson

     *

     * This function is used to activate a lesson

     * <br/>Example:

     * <code>

     * $lesson -> activate();

     * </code>

     *

     * @return boolean True if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function activate() {
        $this -> lesson['active'] = 1;
        $this -> persist();
        return true;
    }
    /**

     * Deactivate lesson

     *

     * This function is used to deactivate a lesson

     * <br/>Example:

     * <code>

     * $lesson -> deactivate();

     * </code>

     *

     * @return boolean True if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function deactivate() {
        $this -> lesson['active'] = 0;
        $this -> persist();
        return true;
    }
    /**

     * Delete lesson (statically)

     *

     * This function is used to delete an existing lesson. In order to do

     * this, it caclulates all the lesson dependendant elements, deletes them

     * and finally deletes the lesson itself. This function is the same as

     * EfrontLesson :: delete(), except that it is called statically, so it

     * instatiates first the lesson objects and then calls delete() on it.

     * Alternatively, $lesson may be already a lesson object.

     * <br/>Example:

     * <code>

     * try {

     *   EfrontLesson :: delete(32);                     //32 is the lesson id

     * } catch (Exception $e) {

     *   echo $e -> getMessage();

     * }

     * </code>

     *

     * @param mixed $lesson The lesson id or a lesson object

     * @return boolean True if everything is ok

     * @since 3.5.0

     * @access public

     * @static

     */
    public static function deleteLesson($lesson) {
        if (!($lesson instanceof EfrontLesson)) {
            $lesson = new EfrontLesson($lesson);
        }
        return $lesson -> delete();
    }
    /**

     * Get lesson directory

     *

     * This function returns the lesson directory, normally G_LESSONSPATH.$lesson['id']

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(43);          //Instantiate object for lesson with id 43

     * $lesson -> getDirectory();               //Returns something like /var/sites/efront/www/content/lessons/43

     * $lesson -> getDirectory(true);           //Returns EfrontDirectory object for lesson directory

     * </code>

     *

     * @param boolean $returnObject If true, an EfrontDirectory object is returned

     * @return mixed Either a string with the lesson directory, or the equivalent EfrontDirectory object

     * @since 3.5.0

     * @access public

     */
    public function getDirectory($returnObject = false) {
        if ($returnObject) {
            return new EfrontDirectory($this -> directory);
        } else {
            return $this -> directory;
        }
    }
    /**

     * Get url to lesson's directory

     * 

     * This function is used to return the public url that this lesson uses

     * Since we may have shadow lessons, this url depends on the lesson directory

     * 

     * @return string The lesson url

     * @since 3.6.0

     * @access public

     */
    public function getDirectoryUrl() {
        $url = G_RELATIVELESSONSLINK.str_replace(G_LESSONSPATH, '', $this -> getDirectory());
        return $url;
    }
    /**

     * Get lesson users

     *

     * This function returns an array with the lesson users.

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(32);                      //32 is a lesson id

     * $lessonUsers    = $lesson -> getUsers();             //Get the lesson users

     * $nonLessonUsers = $lesson -> getNonUsers();          //Get the users that don't have the lesson, but are eligible to

     * </code>

     * The returned array keys match the users logins

     *

     * @param string $basicType The user's basic type in the lesson

     * @param boolean $refresh Whether to explicitly refresh the object cached data set

     * @return array A 2-dimensional array with lesson users per type, or a 1-dimensional array with lesson users of the specified type

     * @since 3.5.0

     * @access public

     */
    public function getUsers($basicType = false, $refresh = false) {
        if ($this -> users === false || $refresh) { //Make a database query only if the variable is not initialized, or it is explicitly asked
            $this -> users = array();
            $result = eF_getTableData("users u, users_to_lessons ul", "u.*, ul.user_type as role, ul.from_timestamp", "u.user_type != 'administrator' and users_LOGIN = login and lessons_ID=".$this -> lesson['id']);
            foreach ($result as $value) {
                $this -> users[$value['login']] = array('login' => $value['login'],
                                                        'email' => $value['email'],
                                                        'name' => $value['name'],
                                                        'surname' => $value['surname'],
                                                        'basic_user_type' => $value['user_type'],
                                                        'user_type' => $value['user_type'],
                                                        'user_types_ID' => $value['user_types_ID'],
                                                        'role' => $value['role'],
              'from_timestamp' => $value['from_timestamp'],
                                                        'active' => $value['active'],
                          'avatar' => $value['avatar'],
                                                        'partof' => 1);
            }
        }
        if ($basicType) {
            $users = array();
            $roles = EfrontLessonUser :: getLessonsRoles();
            foreach ($this -> users as $login => $value) {
                if ($roles[$value['role']] == $basicType) {
                    $users[$login] = $value;
                }
            }
            return $users;
        } else {
            return $this -> users;
        }
    }
    /**

     * Get lesson students according to whether they have completed the lesson or not

     *

     * This function returns an array with the lesson users that have completed the

     * lesson or not

     * 

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(32);                      //32 is a lesson id

     * $lessonUsersCompleted    = $lesson -> getUsersCompleted(true);          

     * $lessonUsersNotCompleted = $lesson -> getUsersCompleted(false);         

     * </code>

     * The returned array keys match the users logins

     *

     * @param boolean $completed Whether the returned users should have completed the lesson or not

     * @param boolean $refresh Whether to explicitly refresh the object cached data set

     * @return array A 2-dimensional array with lesson users per type, or a 1-dimensional array with lesson users of the specified type

     * @since 3.6.0

     * @access public

     */
    public function getUsersCompleted($completed, $refresh = false) {
        if ($this -> users === false || $refresh) { //Make a database query only if the variable is not initialized, or it is explicitly asked
            $this -> users = array();
            if ($completed) {
                $completedInt = 1;
            } else {
                $completedInt = 0;
            }
            $result = eF_getTableData("users u, users_to_lessons ul", "u.*, ul.user_type as role, ul.from_timestamp", "u.user_type != 'administrator' and users_LOGIN = login and ul.user_type = 'student' and lessons_ID=".$this -> lesson['id'] . " AND completed = " . $completedInt);
            foreach ($result as $value) {
                $this -> users[$value['login']] = array('login' => $value['login'],
                                                        'email' => $value['email'],
                                                        'name' => $value['name'],
                                                        'surname' => $value['surname'],
                                                        'basic_user_type' => $value['user_type'],
                                                        'user_types_ID' => $value['user_types_ID'],
                                                        'role' => $value['role'],
              'from_timestamp' => $value['from_timestamp'],
                                                        'active' => $value['active'],
                          'avatar' => $value['avatar'],
                                                        'partof' => 1);
            }
        }
        return $this -> users;
    }
    /**

     * Get users that don't have the lesson

     *

     * This function is complementary to getUsers(); it returns only those users

     * that don't have this lesson.

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(32);                      //32 is a lesson id

     * $lessonUsers    = $lesson -> getUsers();             //Get the lesson users

     * $nonLessonUsers = $lesson -> getNonUsers();          //Get the users that don't have the lesson, but are eligible to

     * </code>

     * The returned array keys match the users logins

     *

     * @param boolean $type The users type

     * @return array A list with eleigible users for this lesson

     * @since 3.5.0

     * @access public

     */
    public function getNonUsers($type = false) {
        foreach ($this -> getUsers() as $key => $value) {
            $lessonUsers[$value['login']] = $value;
        }
        //$sql = "users.active = 1 and users.languages_NAME = '".$this -> lesson['languages_NAME']."' and login NOT IN ('".implode("','", array_keys($lessonUsers))."') ";
        $sql = "users.active = 1 and login NOT IN ('".implode("','", array_keys($lessonUsers))."') ";
        $type && in_array($type, EfrontUser :: basicUserTypes) ? $sql.= "and user_type == '".$type."'" : $sql.= "and user_type != 'administrator'";
        $result = eF_getTableData("users", "*", $sql);
        $nonLessonUsers = array();
        foreach ($result as $value) {
            $nonLessonUsers[$value['login']] = array('login' => $value['login'],
                                                     'email' => $value['email'],
                                                     'name' => $value['name'],
                                                     'surname' => $value['surname'],
                                                     'basic_user_type' => $value['user_type'],
                                                     'user_types_ID' => $value['user_types_ID'],
                                                     'role' => $value['user_types_ID'] ? $value['user_types_ID'] : $value['user_type'],
                                                     'active' => $value['active'],
                                                     'partof' => 0);
        }
        return $nonLessonUsers;
    }
    /**

     * Get lesson course

     *

     * This function returns the courses that this lesson is part

     * of.

     * <br/>Example:

     * <code>

     * $courses = $this -> getCourses();                    //Return an array of course ids and properties

     * $courses = $this -> getCourses(true);                //Return an array of EfrontCourse objects

     * </code>

     *

     * @param boolean $returnObjects Whether to return EfrontCourse objects or a simple arrays

     * @return array The courses list, where keys are ids and values are either arrays or EfrontCourse objects

     * @since 3.5.0

     * @access public

     */
    public function getCourses($returnObjects = false) {
        $result = eF_getTableData("courses JOIN lessons_to_courses ON courses.id = courses_ID", "courses.*", "lessons_ID = ".$this -> lesson['id']);
        $courses = array();
        foreach ($result as $value) {
            $returnObjects ? $courses[$value['id']] = new EfrontCourse($value['id']) : $courses[$value['id']] = $value;
        }
        return $courses;
    }
    /**

     * Add users to lesson

     *

     * This function is used to register one or more users to the current lesson. A single login

     * or an array of logins may be specified

     * <br/>Example:

     * <code>

     * $lesson -> addUsers('joe', 'professor');         //Add the user with login 'joe' as a professor to this lesson

     * $users = array('joe', 'mary', 'mike');

     * $types = array('student', 'student', 'professor');

     * $lesson -> addUsers($users, $types);             //Add the users in the array $users with roles $types

     * </code>

     *

     * @param mixed $login The user login name

     * @param mixed $role The user role for this lesson, defaults to 'student'

     * @param boolean $confirmed If false, then the registration is set to 'pending' mode and the administration must confirm it

     * @return boolean true if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function addUsers($login, $role = 'student', $confirmed = true) {
        if (!is_array($login)) {
            $login = array($login);
            $role = array($role);
        }
  $temp = array();
  if (!is_array($role) && is_array($login)) { //in case second param is not defined by call and first is an array
   foreach ($login as $value){
    $temp[] = $role;
   }
   $role = $temp;
  }
        $userTypes = EfrontLessonUser :: getLessonsRoles();
        $lessonUsers = $this -> getUsers();
        $addedStudents = array();
        $count = sizeof($this -> getUsers('student'));
        foreach ($login as $key => $value) {
            if (!in_array($value, array_keys($lessonUsers))) {
                if (eF_checkParameter($value, 'login')) {
                    $fields = array('users_LOGIN' => $value,
                                    'lessons_ID' => $this -> lesson['id'],
                                    'active' => 1,
                                    'from_timestamp' => $confirmed ? time() : 0,
                                    'user_type' => current($role));
                    if ($this -> lesson['max_users'] && $this -> lesson['max_users'] <= $count++ && ($fields['user_type'] == 'student' || $userTypes[$fields['user_type']]['basic_user_type'] == 'student')) {
                        throw new EfrontLessonException(_MAXIMUMUSERSREACHEDFORLESSON, EfrontLessonException :: MAX_USERS_LIMIT);
                    }
                    next($role);
                    try {
                        eF_insertTableData("users_to_lessons", $fields);
                        if ($fields['user_type'] == 'student' || $userTypes[$fields['user_type']]['basic_user_type'] == 'student') {
                            $addedStudents[] = $value;
                            // Timelines: new event
                            EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_ACQUISITION_AS_STUDENT, "users_LOGIN" => $value, "lessons_ID" => $this -> lesson['id'], "lessons_name" => $this -> lesson['name']));
                        } else {
                            EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_ACQUISITION_AS_PROFESSOR, "users_LOGIN" => $value, "lessons_ID" => $this -> lesson['id'], "lessons_name" => $this -> lesson['name']));
                        }
                    } catch (Exception $e) {
                        $errors[] = _CANNOTADDUSERTOLESSON.' ('.EfrontLessonException :: DATABASE_ERROR.': '.$e->getMessage().')';
                    }
                } else {
                    $errors[] = _INVALIDLOGIN.': '.$value.' ('.EfrontLessonException :: INVALID_LOGIN.')';
                }
            } else {
                if ($lessonUsers[$value]['role'] != $role[$key]) {
                    $this -> setRoles($value, $role[$key]);
                }
            }
        }
        //Assign new users to auto projects
        $lessonProjects = $this -> getProjects();
        $autoAssignProjects = array();
        foreach ($lessonProjects as $id => $project) {
            if ($project['auto_assign']) {
                $autoAssignProjects[$id] = new EfrontProject($id);
            }
        }
        if (sizeof($addedStudents) > 0) {
            foreach ($autoAssignProjects as $project) {
                $project -> addUsers($addedStudents);
            }
        }
        if (!isset($errors)) {
            return true;
        } else {
            throw new EfrontLessonException(_PROBLEMADDINGUSERSTOLESSON.': '.implode("<br>", $errors), EfrontLessonException :: GENERAL_ERROR);
        }
    }
    /**

     * Remove user from lesson

     *

     * This function is used to remove a user from the current lesson. A single login

     * or an array of logins may be specified

     * <br/>Example:

     * <code>

     * $lesson -> removeUsers('jdoe');          //Remove the user with login 'jdoe'

     * </code>

     *

     * @param string $login The user login name

     * @return boolean true if everything is ok

     * @since 3.5.0

     * @access public

     * @todo remove him from projects list

     */
    public function removeUsers($login) {
        if (!is_array($login)) {
            $login = array($login);
        }
        foreach ($login as $value) {
            if (eF_checkParameter($value, 'login')) {
                eF_deleteTableData("users_to_lessons", "users_LOGIN='$value' and lessons_ID=".$this -> lesson['id']);
    $lessonTests = $this -> getTests(false);
    if (sizeof($lessonTests) > 0) {
     eF_updateTableData("completed_tests", array('archive' => 1), "users_LOGIN='$value' and tests_ID in (".implode(",", $lessonTests).")");
    }
                //Timelines event
                EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_REMOVAL, "users_LOGIN" => $value, "lessons_ID" => $this -> lesson['id'], "lessons_name" => $this -> lesson['name']));
            }
        }
        if (!isset($errors)) {
            return true;
        } else {
            throw new EfrontLessonException(_PROBLEMADDINGUSERSTOLESSON.': '.implode("<br>", $errors), EfrontLessonException :: GENERAL_ERROR);
        }
    }
    /**

     * Confirm user registration

     * 

     * This function is used to set the specified user's status for the current lesson

     * to 'available', if it was previously set to 'pending'

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(15);

     * $lesson -> confirm('jdoe');

     * </code>

     *

     * @param mixed $login Either the user login, or an EfrontLessonUser object

     * @since 3.5.2

     * @access public

     */
    public function confirm($login) {
        if ($login instanceof EfrontLessonUser) {
            $login = $login -> user['login'];
        } else if (!eF_checkParameter($login, 'login')) {
            throw new EfrontUserException(_INVALIDLOGIN, EfrontUserException::INVALID_LOGIN);
        }
        eF_updateTableData("users_to_lessons", array("from_timestamp" => time()), "users_LOGIN='".$login."' and lessons_ID=".$this -> lesson['id']." and from_timestamp=0");
    }
    /**

     * Set user roles in lesson

     *

     * This function sets the role for the specified user

     * <br/>Example:

     * <code>

     * $lesson -> addUsers('jdoe', 'student');              //Added the user 'jdoe' in the lesson, having the role 'student'

     * $lesson -> setRoles('jdoe', 'professor');                //Updated jdoe's role to be 'professor'

     * </code>

     * Multiple values can be set if arguments are arrays

     *

     * @param mixed $login The user login name

     * @param mixed $role The user role for this lesson

     * @return boolean true if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function setRoles($login, $role) {
        if (!is_array($login)) {
            $login = array($login);
            $role = array($role);
        }
        foreach ($login as $key => $value) {
            if (eF_checkParameter($value, 'login')) {
                if (!eF_updateTableData("users_to_lessons", array('user_type' => $role[$key]), "users_LOGIN='".$value."' and lessons_ID=".$this -> lesson['id'])) {
                    $errors[] = _CANNOTUPDATEUSERLESSONINFORMATION.' ('.EfrontLessonException :: DATABASE_ERROR.')';
                }
            } else {
                $errors[] = _INVALIDLOGIN.': '.$value.' ('.EfrontLessonException :: INVALID_LOGIN.')';
            }
        }
        if (!isset($errors)) {
            return true;
        } else {
            throw new EfrontLessonException(_PROBLEMUPDATINGUSERSTOLESSON.': '.implode("<br>", $errors), EfrontLessonException :: GENERAL_ERROR);
        }
    }
    /**

     * Get the user role in this lesson

     *

     * This function gets the role of the specified user in the specified lesson

     * <br>Example:

     * <code>

     * $lesson -> getRole('jdoe');      //Return the user's role, e.g. 'professor', or, if it's a custom role, it's id, e.g. '5'

     * </code>

     *

     * @param string $login The user's login

     * @return mixed The user's role in lesson

     * @since 3.5.0

     * @access public

     */
    public function getRole($login) {
        $lessonUsers = $this -> getUsers();
        if (in_array($login, array_keys($lessonUsers))) {
            return $lessonUsers[$login]['role'];
        } else {
            throw new EfrontUserException(_USERDOESNOTHAVETHISLESSON.": ".$lesson, EfrontUserException :: USER_NOT_HAVE_LESSON);
        }
    }
    /**

     * Get the tests of the lesson

     *

     * This returns the tests of the lesson

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(12);

     * $tests = $lesson -> getTests(true);

     * </code>

     * @param boolean returnObjects. Flag to indicate whether to return a list of objects or a list of ids

     * @param boolean $onlyActive Whether to return only active tests or all

     * @return array the lesson's tests (either an array of ids or an array of EfrontTest objects)

     * @since 3.5.0

     * @access public

     */
    public function getTests($returnObjects = false, $onlyActive = false) {
        $tests = array();
        if (!$onlyActive) {
            $test_data = eF_getTableData("tests t, content c", "t.*", "t.lessons_ID = ".$this -> lesson['id']." and t.content_id = c.id and c.lessons_ID=".$this -> lesson['id']);
        } else {
            $test_data = eF_getTableData("tests t, content c", "t.*", "t.active = 1 and c.active = 1 and t.lessons_ID = ".$this -> lesson['id']." and t.content_id = c.id and c.lessons_ID=".$this -> lesson['id']);
        }
        foreach ($test_data as $t){
            if (!$returnObjects){
                $tests[] = $t['id'];
            } else{
                $test = new EfrontTest($t['id']);
                $tests[$t['id']] = $test;
            }
        }
        return $tests;
    }
    /**

     * Get the scorm tests of the lesson

     *

     * This returns the tests of the lesson

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(12);

     * $tests = $lesson -> getScormTests();

     * </code>

     *

     * @return array the lesson's scorm tests (an array of ids)

     * @since 3.5.0

     * @access public

     * @static

     */
    public function getScormTests(){
        $tests = array();
        $scorm_data = eF_getTableData("content", "id", "lessons_ID=".$this -> lesson['id']." and ctg_type='scorm_test'");
        foreach ($scorm_data as $data){
            $tests[] = $data['id'];
        }
        return $tests;
    }
    /**

    * Get the questions of the lesson

    *

    * This returns the questions of the lesson

    * <br/>Example:

    * <code>

    * $lesson = new EfrontLesson(12);

    * $questions = $lesson -> getQuestions(true);

    * </code>

    * 

    * @param bool returnObjects. Flag to indicate whether to return a list of objects or a list of ids

    * @return array the lesson's questions (either an array of ids or an array of Question objects)

    * @since 3.5.0

    * @access public

    * @static

    */
    public function getQuestions($returnObjects = false){
        /* //old implementation

        $questions = array();

        $question_data = eF_getTableData("questions q, tests_to_questions tq, content c, tests t", "distinct (q.id)", "tq.questions_ID = q.id and tq.tests_ID = t.id and t.content_id = c.id and c.lessons_ID=".$this -> lesson['id']);

        foreach ($question_data as $qid){

            if (!$returnObjects){

                $questions[] = $qid['id'];

            } else {

                $question = QuestionFactory :: factory($qid['id']);

                $questions[$qid['id']] = $question;

            }

        }

        return $questions;

	*/
        $questions = array();
        $result = eF_getTableData("questions", "*", "lessons_ID=".$this -> lesson['id']);
        if (sizeof($result) > 0) {
            foreach ($result as $value) {
                $returnObjects ? $questions[$value['id']] = QuestionFactory :: factory($value) : $questions[$value['id']] = $value;
            }
        }
        return $questions;
    }
    /**

     * Get lesson information

     *

     * This function returns the lesson information in an array

     * with attributes: 'general_description', 'assessment',

     * 'objectives', 'lesson_topics', 'resources', 'other_info',

     * as well as other information, including professors, projects

     * etc.

     * If a user is specified, the information is customized on this

     * user.

     *

     * <br/>Example:

     * <code>

     * $info = $lesson -> getInformation();         //Get lesson information

     * $info = $lesson -> getInformation('jdoe');   //Get lesson information, customizable for user 'jdoe'

     * </code>

     *

     * @param string $user The user login to customize lesson information for

     * @return array The lesson information

     * @since 3.5.0

     * @access public

     */
    public function getInformation($user = false, $information = false) {
        $lessonContent = new EFrontContentTree($this -> lesson['id'], array('id', 'previous_content_ID', 'parent_content_ID', 'active', 'publish', 'ctg_type'));
        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1, 'publish' => 1)) as $key => $value) {
            switch($value['ctg_type']) {
                case 'tests': case 'scorm_test': $testIds[$key] = $key; break;
                case 'theory': case 'scorm': $theoryIds[$key] = $key; break;
                case 'examples': $exampleIds[$key] = $key; break;
                default: break;
            }
        }
/*        

        $testIds = array();

        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1, 'publish' => 1, 'ctg_type' => 'tests')) as $key => $value) {

            $testIds[$key] = $key;            //Count tests

        }

        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1, 'ctg_type' => 'scorm_test')) as $key => $value) {

            $testIds[$key] = $key;            //Count tests

        }



        $theoryIds = array();

        foreach (new EfrontTheoryFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)))) as $key => $value) {

            $theoryIds[$key] = $key;            //Count theory

        }

        //        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'scorm', 'active' => 1)) as $key => $value) {

//            $theoryIds[$key] = $key;            //Count theory

//        }



        $exampleIds = array();

        foreach (new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'examples'))) as $key => $value) {

            $exampleIds[$key] = $key;            //Count examples

        }

*/
        $user ? $projects = $this -> getProjects(false, $user, false) : $projects = $this -> getProjects();
        $direction = $this -> getDirection();
        $info['students'] = $this -> getUsers('student');
        $info['professors'] = $this -> getUsers('professor');
        $info['tests'] = sizeof($testIds);
        $info['theory'] = sizeof($theoryIds);
        $info['examples'] = sizeof($exampleIds);
        $info['content'] = $info['examples'] + $info['theory'];
        $info['projects'] = sizeof($projects);
        $info['direction'] = $direction['name'];
        $info['active'] = $this -> lesson['active'];
        $info['active_string'] = $this -> lesson['active'] == 1 ? _YES : _NO;
        $info['price'] = $this -> lesson['price'];
        $info['price_string'] = $this -> lesson['price_string'];
        $info['language'] = $this -> lesson['languages_NAME'];
        $info['from_timestamp']= $this -> lesson['from_timestamp'];
        $info['to_timestamp'] = $this -> lesson['to_timestamp'];
        $info['created'] = $this -> lesson['created'];
        //$info['shift']         = $this -> lesson['shift'];
        if ($info['professors']) {
            foreach ($info['professors'] as $value) {
                $professorsString[] = $value['name'].' '.$value['surname'];
            }
            $info['professors_string'] = implode(", ", $professorsString);
        }
        if ($this -> lesson['info']) {
   $order = array("general_description", "objectives", "assessment", "lesson_topics", "resources", "other_info", "learning_method"); // for displaying fiels sorted
   $infoSorted = array();
   $unserialized = unserialize($this -> lesson['info']);
   foreach ($order as $value) {
    if ($unserialized[$value] != "") {
     $infoSorted[$value] = $unserialized[$value];
    }
   }
            !is_array($this -> lesson['info']) && unserialize($this -> lesson['info']) !== false ? $info = array_merge($infoSorted, $info) : $info = array_merge($infoSorted, $info);
        }
        return $info;
    }
   /**

     * Get lesson statisticsinformation

     *

     * This function returns the lesson information in an array

     * with attributes: 'general_description', 'assessment',

     * 'objectives', 'lesson_topics', 'resources', 'other_info',

     * as well as other information, including professors, projects

     * etc.

     * If a user is specified, the information is customized on this

     * user.

     *

     * <br/>Example:

     * <code>

     * $info = $lesson -> getInformation();         //Get lesson information

     * $info = $lesson -> getInformation('jdoe');   //Get lesson information, customizable for user 'jdoe'

     * </code>

     *

     * @param string $user The user login to customize lesson information for

     * @return array The lesson information

     * @since 3.5.0

     * @access public

     */
    public function getStatisticInformation($user = false) {
        $lessonContent = new EFrontContentTree($this -> lesson['id']);
        $testIds = array();
        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1, 'publish' => 1, 'ctg_type' => 'tests')) as $key => $value) {
            $testIds[$key] = $key; //Count tests
        }
        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1, 'ctg_type' => 'scorm_test')) as $key => $value) {
            $testIds[$key] = $key; //Count scorm tests
        }
        $theoryIds = array();
        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'theory', 'active' => 1)) as $key => $value) {
            $theoryIds[$key] = $key; //Count theory
        }
        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'scorm', 'active' => 1)) as $key => $value) {
            $theoryIds[$key] = $key; //Count scorm content
        }
        $exampleIds = array();
        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'examples', 'active' => 1)) as $key => $value) {
            $exampleIds[$key] = $key; //Count examples
        }
        if (!$user) {
            $projects = $this -> getProjects();
            $lessonComments = eF_getTableData("comments, content", "comments.*",
                                              "comments.content_ID = content.id and content.lessons_ID=".$this->lesson['id']);
            $lessonMessages = eF_getTableData("f_messages, f_topics, f_forums", "f_messages.*",
                                              "f_messages.f_topics_ID = f_topics.id and f_topics.f_forums_ID = f_forums.id and f_forums.lessons_ID=".$this->lesson['id']);
            $lessonChat = eF_getTableData("chatmessages, chatrooms", "chatmessages.*",
                                              "chatmessages.chatrooms_ID = chatrooms.id and chatrooms.lessons_ID = ".$this->lesson['id']);
        } else {
            $projects = $this -> getProjects(false, $user, false);
            $lessonComments = eF_getTableData("comments, content", "comments.*",
                                              "comments.users_LOGIN='".$user."' and comments.content_ID = content.id and content.lessons_ID=".$this->lesson['id']);
            $lessonMessages = eF_getTableData("f_messages, f_topics, f_forums", "f_messages.*",
                                              "f_messages.users_LOGIN='".$user."' and f_messages.f_topics_ID = f_topics.id and f_topics.f_forums_ID = f_forums.id and f_forums.lessons_ID=".$this->lesson['id']);
            $lessonChat = eF_getTableData("chatmessages, chatrooms", "chatmessages.*",
                                              "chatmessages.users_LOGIN='".$user."' and chatmessages.chatrooms_ID = chatrooms.id and chatrooms.lessons_ID = ".$this->lesson['id']);
        }
        $direction = $this -> getDirection();
        $languages = EfrontSystem :: getLanguages(true);
        $info['students'] = $this -> getUsers('student');
        $info['professors'] = $this -> getUsers('professor');
        $info['tests'] = sizeof($testIds);
        $info['theory'] = sizeof($theoryIds);
        $info['examples'] = sizeof($exampleIds);
        $info['content'] = $info['examples'] + $info['theory'];
        $info['projects'] = sizeof($projects);
        $info['comments'] = sizeof($lessonComments);
        $info['messages'] = sizeof($lessonMessages);
        $info['chatmessages'] = sizeof($lessonChat);
        $info['direction'] = $direction['name'];
        $info['active'] = $this -> lesson['active'];
        $info['active_string'] = $this -> lesson['active'] == 1 ? _YES : _NO;
        $info['price'] = $this -> lesson['price'];
        $info['price_string'] = $this -> lesson['price_string'];
        $info['language'] = $languages[$this -> lesson['languages_NAME']];
        $info['created'] = $languages[$this -> lesson['created']];
        if ($this -> lesson['info']) {
            if (unserialize($this -> lesson['info']) !== false) {
             $storedInfo = unserialize($this -> lesson['info']);
             unset($storedInfo['professors']); //Due to an old bug, serialized information may contain professors as well. So, we must remove them
                $info = array_merge($info, $storedInfo);
            } else if (is_array($this -> lesson['info'])) {
                $info = array_merge($info, $this -> lesson['info']);
            }
        }
        return $info;
    }
    /**

     * Set lesson information

     *

     * This function sets the lesson information to the designated

     * value. If $info is not set, the lesson information is erased

     * <br/>Example:

     * <code>

     * $info = array('general_description' => 'Hello world');               //Set the new lesson information, erasing any old information

     * $lesson -> setInformation($info);

     * $info = $lesson -> getInformation();                                 //Set new information, keeping old information at the same time

     * $info['assessment'] = 'Goodbye cruel world';

     * $lesson -> setInformation($info);

     * $lesson -> setInformation();                                         //Erase information

     * </code>

     *

     * @param array $info The lesson information

     * @return boolean True if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function setInformation($info = false) {
        if ($info) {
            $info = serialize($info);
            $this -> lesson['info'] = $info;
            eF_updateTableData("lessons", array("info" => $info), "id=".$this -> lesson['id']);
        } else {
            $this -> lesson['info'] = '';
            eF_updateTableData("lessons", array("info" => ""), "id=".$this -> lesson['id']);
        }
        return true;
    }
    /**

     * Get lesson news

     * 

     * This function is used to retrieve any news related to this lesson

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(45);		//Initialize lesson with id 45

     * $news = $lesson -> getNews();		//Get the lesson news

     * $news = $lesson -> getNews(true);	//Get the only lesson news that are set to past dates

     * </code>

     *

     * @param boolean $showPrevious Whether to only return past news

     * @return array An array of news

     * @since 3.5.2

     * @access public

     */
    public function getNews($showPrevious = true) {
        if ($showPrevious){ // students see only previous news
            $result = eF_getTableData("news n, users u", "n.*, u.surname, u.name", "n.users_LOGIN = u.login and n.lessons_ID=".$this -> lesson['id']." AND n.timestamp<=".time(), "n.timestamp desc, n.id desc"); //"            
        } else {
            $result = eF_getTableData("news n, users u", "n.*", "n.users_LOGIN = u.login and n.lessons_ID=".$this -> lesson['id'], "n.timestamp desc, n.id desc");
        }
        $news = array();
        foreach ($result as $value) {
            $news[$value['id']] = $value;
        }
        return $news;
    }
    /**

     * Delete lesson news

     * 

     * This function is used to delete the specified news from the 

     * lesson.

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(45);		//Initialize lesson with id 45

     * $lesson -> deleteNews(12);									//Delete lesson announcement with id 12 

     * $lesson -> deleteNews(array(12, 43, 765));					//Delete lesson announcements with ids 12, 43, 765 

     * $lesson -> deleteNews(array_keys($lesson -> getNews()));		//Delete all lesson announcements 

     * </code>

     *

     * @param unknown_type $news

     * @return unknown

     */
    public function deleteNews($news) {
        if (!is_array($news)) {
            $news = array($news);
        }
        $lessonNews = array_keys($this -> getNews());
        $ids = array();
        foreach ($news as $key => $value) {
            if (in_array($value, $lessonNews)) {
                $ids[] = $value;
            }
        }
        if (sizeof($ids) > 0) {
            $result = eF_deleteTableData("news", "id in (".implode(",", $ids).")");
        }
        return $result;
    }
    /**

     * Get lesson units

     * 

     * This function returns a list of the units that 

     * belong to this lesson.

     * <br>Example:

     * <code>

     * $lesson = new EfrontLesson(13);			//Instantiate lesson object

     * $units = $lesson -> getUnits();			//Return a list of ids that represent the lesson units

     * </code>

     *

     * @return array The ids of the lesson content units

     * @since 3.5.2

     * @access public

     */
    public function getUnits() {
        $contentTree = new EfrontContentTree($this->lesson['id']);
        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($contentTree -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
            $units[$key] = $key;
        }
        return $units;
    }
    /**

     * Initialize lesson

     *

     * This function is used to initialize specific aspects of the current lesson

     * These aspects can be the lesson content, announcements, users, glossary etc

     * specified in the $deleteEntities array.

     * <br/>Example:

     * <code>

     * try {

     *   $lesson = new EfrontLesson(32);                     //32 is the lesson id

     *  $lesson -> initialize(array('content', 'glossary', 'rules'));           //Erase all content, glossary terms and content rules

     * } catch (Exception $e) {

     *   echo $e -> getMessage();

     * }

     * </code><br/>

     * If $deleteEntities is 'all', then all lesson aspects are reset

     *

     * @param mixed $deleteEntities Eiterh an array with lesson aspects to initialize, or 'all' which equals to 'reset everything'

     * @return boolean True if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function initialize($deleteEntities) {
        $possibleEntities = array('content',
                                  'tests',
                                  'questions',
                                  'rules',
                'conditions',
                                  'comments',
                                  'users',
                                  'news',
                 'files',
                                  'calendar',
                                  'glossary',
                 'tracking',
                                  'scheduling',
                                  'surveys',
                'events',
                'modules',
                'projects');
        if ($deleteEntities == 'all') {
            $deleteEntities = $possibleEntities;
        }
        $content = eF_getTableDataFlat("content", "*", "lessons_ID=".$this -> lesson['id']); //Get the lesson units
        sizeof($content['id']) > 0 ? $content_list = implode(",", $content['id']) : $content_list = array(); //Create list of content ids, will come in handy later
        foreach ($deleteEntities as $value) {
            switch ($value) {
                case 'tests':
                    $lessonTests = $this -> getTests(true);
                    foreach ($lessonTests as $id => $test) {
                        $test -> delete();
                    }
                    break;
                case 'questions':
                    $lessonQuestions = $this -> getQuestions(true);
                    foreach ($lessonQuestions as $id => $question) {
                        $question -> delete();
                    }
                    break;
                case 'rules':
                    $content = new EfrontContentTree($this -> lesson['id']);
                    $contentRules = $content -> getRules();
                    $content -> deleteRules(array_keys($contentRules));
                    break;
                case 'conditions':
                    $lessonConditions = $this -> getConditions();
                    $this -> deleteConditions(array_keys($lessonConditions));
                    break;
                case 'comments':
                    $content = new EfrontContentTree($this -> lesson['id']);
                    $content -> deleteComments(array_keys($content -> getComments())); //Delete all comments
                    break;
                case 'content':
                    $content = new EfrontContentTree($this -> lesson['id']);
                    foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($content -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $unit) {
                        $unit -> delete();
                    }
                    break;
                case 'users':
                    $lessonUsers = $this -> getUsers('student');
                    $this -> removeUsers(array_keys($lessonUsers));
                    break;
                case 'news':
                    $lessonNews = $this -> getNews();
                    $this -> deleteNews(array_keys($lessonNews));
                    break;
                case 'files':
                 //Only delete files if this lesson is not sharing its folder
                 if (!$this -> lesson['share_folder']) {
                  $directory = new EfrontDirectory($this -> directory);
                  $directory -> delete();
                 }
                 break;
                case 'calendar':
                    in_array('calendar', $deleteEntities) ? eF_deleteTableData("calendar", "lessons_ID=".$this -> lesson['id']) : null;
                    break;
                case 'glossary':
                    in_array('glossary', $deleteEntities) ? eF_deleteTableData("glossary", "lessons_ID=".$this -> lesson['id']) : null;
                    break;
                case 'projects':
                    $lessonProjects = $this -> getProjects(true);
                    foreach ($lessonProjects as $value) {
                        $value -> delete();
                    }
                    break;
                case 'tracking':
                    $tracking_info = array("done_content" => "",
                                           "issued_certificate" => "",
                                           "comments" => "",
                                           "completed" => 0,
                                           "current_unit" => 0,
                                           "score" => 0);
                    eF_updateTableData("users_to_lessons", $tracking_info, "lessons_ID = ".$this -> lesson['id']);
                    if (!isset($lessonTests)) {
                        $lessonTests = $this -> getTests(true);
                    }
                    foreach ($lessonTests as $id => $test) {
                        eF_deleteTableData("completed_tests", "tests_ID=$id");
                    }
                    $units = $this -> getUnits();
                    if (sizeof($units) > 0) {
                        eF_deleteTableData("scorm_data", "content_ID in (".implode(",", $units).")");
                    }
                    break;
                case 'scheduling':
                    $this -> lesson['from_timestamp'] = null;
                    $this -> lesson['to_timestamp'] = null;
                    $this -> persist();
                    break;
                case 'surveys':
                    $surveys = eF_getTableDataFlat("surveys", "id", "lessons_ID=".$this -> lesson['id']);
                    $surveys_list = implode(",", $surveys['id']);
                    if (!empty($surveys_list)) {
                     eF_deleteTableData("questions_to_surveys", "surveys_ID IN ($surveys_list)");
                     eF_deleteTableData("survey_questions_done", "surveys_ID IN ($surveys_list)");
                     eF_deleteTableData("users_to_surveys", "surveys_ID IN ($surveys_list)");
                     eF_deleteTableData("users_to_done_surveys", "surveys_ID IN ($surveys_list)");
                     eF_deleteTableData("surveys", "lessons_ID=".$this -> lesson['id']);
                    }
                    break;
                case 'events':
                 $events = $this -> getEvents();
                 if (!empty($events)) {
      eF_deleteTableData("events", "id in (".implode(",", array_keys($events)).")");
                 }
                 break;
                case 'modules':
                    $modules = eF_loadAllModules();
                    foreach ($modules as $module) {
                        $module -> onDeleteLesson($this -> lesson['id']);
                    }
                    break;
            }
        }
/*        

        if (in_array('questions', $deleteEntities)) {                                                             //Delete lesson questions

            $result = eF_getTableData("questions", "id", "lessons_ID=".$this -> lesson['id']);

            foreach ($result as $value) {

                $questionIds[] = $value['id']; 

            }

            if (sizeof($questionIds) > 0) {

                eF_deleteTableData("tests_to_questions", "questions_ID in (".implode(",", $questionIds).")");

            }

            eF_deleteTableData("questions", "lessons_ID=".$this -> lesson['id']);            

        }

        if (in_array('content', $deleteEntities)) {                                                             //Delete lesson units

            foreach ($content['id'] as $value) {

                $value = new EfrontUnit($value);

                $value -> delete();

            }

            $result = eF_getTableDataFlat("content", "id", "lessons_ID=".$this -> lesson['id']);

            $list   = implode(",", $result['id']);

            EfrontSearch :: removeText('content', $list, '', true);

            eF_deleteTableData("content", "lessons_ID=".$this -> lesson['id']);                             //Just make sure everything is deleted

        }

        if (in_array('surveys', $deleteEntities)) {

            $surveys      = eF_getTableDataFlat("surveys", "id", "lessons_ID=".$this -> lesson['id']);

            $surveys_list = implode(",", $surveys['id']);

            eF_deleteTableData("questions_to_surveys",  "surveys_ID IN ($surveys_list)");

            eF_deleteTableData("survey_questions_done", "surveys_ID IN ($surveys_list)");

            eF_deleteTableData("users_to_surveys",      "surveys_ID IN ($surveys_list)");

            eF_deleteTableData("users_to_done_surveys", "surveys_ID IN ($surveys_list)");

            eF_deleteTableData("surveys", "lessons_ID=".$this -> lesson['id']);

        }

        if (in_array('tracking', $deleteEntities)) {

            $tracking_info = array("done_content"       => "",

                                   "issued_certificate" => "",

                                   "comments"           => "",

                                   "completed"          => 0,

                                   "current_unit"       => 0,

                                   "score"              => 0);

            eF_updateTableData("users_to_lessons", $tracking_info, "lessons_ID = ".$this -> lesson['id']);

        }

        if (in_array('files', $deleteEntities)) {

            $filesystem = new FileSystemTree($this -> getDirectory());

            foreach (new ArrayIterator($filesystem -> tree) as $key => $value) {

                $value -> delete();

            }

        }

        in_array('conditions', $deleteEntities) ? eF_deleteTableData("lesson_conditions", "lessons_ID=".$this -> lesson['id']) : null;

        in_array('calendar',   $deleteEntities) ? eF_deleteTableData("calendar",          "lessons_ID=".$this -> lesson['id']) : null;

        in_array('periods',    $deleteEntities) ? eF_deleteTableData("periods",           "lessons_ID=".$this -> lesson['id']) : null;

        in_array('news',       $deleteEntities) ? eF_deleteTableData("news",              "lessons_ID=".$this -> lesson['id']) : null;

        in_array('glossary',   $deleteEntities) ? eF_deleteTableData("glossary",    "lessons_ID=".$this -> lesson['id']) : null;

        in_array('users',      $deleteEntities) ? eF_deleteTableData("users_to_lessons",  "lessons_ID=".$this -> lesson['id']." and user_type = 'student'") : null;



        in_array('comments', $deleteEntities) ? eF_deleteTableData("comments", "content_ID IN (".$content_list.")") : null;

        in_array('rules',    $deleteEntities) ? eF_deleteTableData("rules",    "content_ID IN (".$content_list.") or rule_content_ID IN (".$content_list.")") : null;



        $done_tests = eF_getTableDataFlat("done_tests", "id", "tests_ID IN ($content_list)");

        sizeof($done_tests) > 0 ? eF_deleteTableData("done_questions", "done_tests_ID IN (".implode(",", $done_tests['id']).")") : null;

        eF_deleteTableData("done_tests",          "tests_ID IN ($content_list)");

        eF_deleteTableData("users_to_done_tests", "tests_ID IN ($content_list)");

        eF_deleteTableData("logs",                "action='test_begin' AND comments IN ($content_list)");



*/
    }
   /**

     * Get this lesson's chat room

     *

     * This function is used to get the chat room belonging to the specific lesson

     * We assume that chatrooms are correctly inserted to the database during lesson creation

     * <br/>Example:

     * <code>

     * try {

     *   $lesson = new EfrontLesson(32);     //32 is the lesson id

     *   $id = $lesson -> getChatroom();           //Get the id of the chat room of this lesson

     * </code><br/>

     *

     * @return the id of the lesson's chatroom, stored in the ($this -> chatroom['id'])

     * @since 3.5.2

     * @access public

     */
    public function getChatroom() {
        if (!isset($this -> chatroom['id'])) {
            $chatroom_info = eF_getTableData("chatrooms", "id", "lessons_ID = '".$this -> lesson['id']."'");
            $this -> chatroom = array();
            $this -> chatroom['id'] = $chatroom_info[0]['id'];
        }
        return $this -> chatroom['id'];
    }
    /*

     * Disable chatroom

     */
    public function disableChatroom() {
        eF_updateTableData("chatrooms", array("active" => 0), "lessons_ID = '".$this -> lesson['id']."'");
        eF_deleteTableData("users_to_chatrooms", "chatrooms_ID = " . $this->getChatroom());
        $this -> setOptions(array("chat" => 0));
    }
    /*

     * Enable chatroom

     */
    public function enableChatroom() {
        eF_updateTableData("chatrooms", array("active" => 1), "lessons_ID = '".$this -> lesson['id']."'");
        $this -> setOptions(array("chat" => 1));
    }
   /*

     * Get this lesson's chat room currently online users

     * 

     * This function is used to get all users currently existing in the chat room 

     * These are either the ones having this lesson as $currentLesson or entering the lesson's chat room

     * <br/>Example:

     * <code>

     * try {

     *   $lesson = new EfrontLesson(32);     //32 is the lesson id

     *   $userList = $lesson -> getChatroomUsers();           //Get the id of the chat room of this lesson

     * </code><br/>

     *

     * The result is stored under the $this -> chatroom['users'] array 

     * @return the array of users where each record has the form [users_login] => [users_login, user_type, timestamp (of entrance)]  

     * @since 3.5.2

     * @access public

     */
    public function getChatroomUsers() {
        $result = eF_getTableData("users_to_chatrooms", "*", "chatrooms_ID = '".$this-> getChatroom()."'");
        $this -> chatroom['users'] = array();
        foreach ($result as $user) {
            $this -> chatroom['users'][$user['users_LOGIN']] = array($user['users_LOGIN'], $user['users_USER_TYPE'], $user['timestamp']);
        }
        return $this -> chatroom['users'];
    }
    /**

     * Add a user to this lesson's chat room

     *

     * This function is used to add a user to this lesson's chat room

     * <br/>Example:

     * <code>

     * try {

     *   $lesson = new EfrontLesson(32);     //32 is the lesson id

     *   $editedUser = EfrontUserFactory :: factory('joe');

     *   $lesson -> addChatroomUser($editedUser);           //Get the id of the chat room of this lesson

     * </code><br/>

     *

     * @param any eFront user object

     * @return the result of the database insertion operation

     * @since 3.5.2

     * @access public

     */
    public function addChatroomUser($user) {
        eF_deleteTableData("users_to_chatrooms", "users_LOGIN = '".$user -> user['login']."'");
        $userRecord = array("users_LOGIN" => $user -> user['login'],
                            "chatrooms_ID" => $this -> getChatroom(),
                            "users_USER_TYPE" => $user -> user['user_type'],
                            "timestamp" => time());
        return eF_insertTableData("users_to_chatrooms", $userRecord);
    }
    /**

     * Removes a user from this lesson's chat room

     *

     * This function is used to remove a user from the chat room belonging to this lesson

     * according to his or her login

     * <br/>Example:

     * <code>

     * try {

     *   $lesson = new EfrontLesson(32);     //32 is the lesson id

     *   $lesson -> removeChatroomUser('joe');           //Get the id of the chat room of this lesson

     * </code><br/>

     *

     * @param the login of the user to be removed

     * @return the result of the database insertion operation

     * @since 3.5.2

     * @access public

     */
    public function removeChatroomUser($login) {
        return eF_deleteTableData("users_to_chatrooms", "users_LOGIN = '".$login."' AND chatrooms_ID = '".$this->getChatroom()."'");
    }
    public function toXML($flgContent){
        $str = "";
        //write out general lesson information
        foreach ($lesson as $key => $value){
            $str .= "<$key>$value<$key>";
        }
        //write out the content tree
        $contentTree = new EfrontContentTree($this->lesson['id']);
        $str .= $contentTree->toXML(false);
        //write out the glossary
        $glossary = ef_getTableData("glossary","*","lessons_ID=".$this->lessonId);
        if (sizeof($glossary) > 0){
            $str .= '<glossary>';
            for ($i = 0; $i < sizeof($glossary); $i++){
                $str .= '<word>';
                $str .= '<name>'.$glossary[$i]['name'].'</name>';
                $str .= '<info>'.$glossary[$i]['info'].'</info>';
                $str .= '<type>'.$glossary[$i]['type'].'</type>';
                $str .= '<active>'.$glossary[$i]['active'].'</active>';
                $str .= '</word>';
            }
            $str .= '</glossary>';
        }
        //write out the lesson conditions
        $conditions = ef_getTableData("lesson_conditions", "*", "lessons_ID=".$this->lessonId);
        if (sizeof($conditions) > 0){
            $str .= '<conditions>';
            for ($i = 0; $i < sizeof($conditions); $i++){
                $str .= '<condition>';
                $str .= '<type>'.$conditions[$i]['type'].'</type>';
                $str .= '<options>'.$conditions[$i]['options'].'</options>';
                $str .= '<relation>'.$conditions[$i]['relation'].'</relation>';
                $str .= '</condition>';
            }
            $str .= '</conditions>';
        }
        //write out the rules
        $rules = ef_getTableData("rules r, content c", "r.*", "c.id = r.content_ID and c.lessons_ID=".$this->lessonId);
        if (sizeof($rules) > 0){
            $str .= '<rules>';
            for ($i = 0; $i < sizeof($rules); $i++){
                $str .= "<rule>";
                $str .= "<content_id>".$rules[$i]['content_ID']."</content_id>";
                $str .= "<rule_content_id>".$rules[$i]['rule_content_ID']."</rule_content_id>";
                $str .= "<rule_type>".$rules[$i]['rule_type']."</rule_type>";
                $str .= "<rule_option>".$rules[$i]['rule_option']."</rule_option>";
                $str .= '</rules>';
            }
            $str .= '</rules>';
        }
        if ($flgContent){
            //to do export the content as well
        }
        $xmlstr .= '<?xml version="1.0" encoding="UTF-8"?><lesson>'.$str.'</lesson>';
        return $xmlstr;
    }
    public function fromXML($xmlfile){
        $xml = simplexml_load_file($xmlfile);
        $contentTree = new EfrontContentTree($this->lesson['id']);
        $contentTree -> fromXMLNode($xml->lessonId[0]);
        $fields = array();
        $fields['name'] = (string)$xml->lesson->name;
        $fields['info'] = (string)$xml->lesson->info;
        $fields['options'] = (string)$xml->lesson->options;
        $fields['course_only'] = (string)$xml->lesson->course_only;
        //$fields['auto_certificate'] = (string)$xml->lesson->auto_certificate;
        //$fields['auto_complete'] = (string)$xml->lesson->auto_complete;
        //$fields['publish'] = (string)$xml->lesson->publish;
        $lid = ef_inserTableData("lessons", $fields);
        for ($i = 0; $i < sizeof($xml->lesson->conditions->condition); $i++){
            $condition = array();
            $condition['type'] = $xml->lessonId->conditions->condition[$i]->type;
            $condition['options'] = $xml->lessonId->conditions->condition[$i]->options;
            $condition['relation'] = $xml->lessonId->conditions->condition[$i]->relation;
            $cid = ef_insertTableData("lesson_conditions", $condition);
        }
        for ($i = 0; $i < sizeof($xml->lesson->rules->rule); $i++){
            $rule = array();
            $rule['content_id'] = $xml->lessonId->rules->rule[$i]->content_id;
            $rule['rule_content_id'] = $xml->lessonId->rules->rule[$i]->rule_content_id;
            $rule['rule_type'] = $xml->lessonId->rules->rule[$i]->rule_type;
            $rule['rule_option'] = $xml->lessonId->rules->rule[$i]->rule_option;
            $rid = ef_insertTableData("rules", $rule);
        }
    }
    public function toIMS($path){
        $lesson_entries = eF_getTableData("content", "id, name, data", "lessons_ID=" . $this->lesson['id'] . " and ctg_type = 'theory' and active=1");
        $cur_dir = getcwd();
        chdir(G_LESSONSPATH.$this->lesson['id'].'/');
        $filelist = eF_getDirContents();
        chdir($cur_dir);
        create_manifest($this->lesson['id'], $lesson_entries, $filelist, $path);
        //create the EfrontLesson.xml
        $str = '<?xml version="1.0" encoding="UTF-8"?><efrontlesson>';
        //write out the glossary
        $glossary = ef_getTableData("glossary","*","lessons_ID=".$this->lesson['id']);
        if (sizeof($glossary) > 0){
            $str .= '<glossary>';
            for ($i = 0; $i < sizeof($glossary); $i++){
                $str .= '<word>';
                $str .= '<name>'.$glossary[$i]['name'].'</name>';
                $str .= '<info>'.$glossary[$i]['info'].'</info>';
                $str .= '<type>'.$glossary[$i]['type'].'</type>';
                $str .= '<active>'.$glossary[$i]['active'].'</active>';
                $str .= '</word>';
            }
            $str .= '</glossary>';
        }
        //write out the lesson conditions
        $conditions = ef_getTableData("lesson_conditions", "*", "lessons_ID=".$this->lesson['id']);
        if (sizeof($conditions) > 0){
            $str .= '<conditions>';
            for ($i = 0; $i < sizeof($conditions); $i++){
                $str .= '<condition>';
                $str .= '<type>'.$conditions[$i]['type'].'</type>';
                $str .= '<options>'.$conditions[$i]['options'].'</options>';
                $str .= '<relation>'.$conditions[$i]['relation'].'</relation>';
                $str .= '</condition>';
            }
            $str .= '</conditions>';
        }
        //write out the rules
        $rules = ef_getTableData("rules r, content c", "r.*", "c.id = r.content_ID and c.lessons_ID=".$this->lesson['id']);
        if (sizeof($rules) > 0){
            $str .= '<rules>';
            for ($i = 0; $i < sizeof($rules); $i++){
                $str .= "<rule>";
                $str .= "<content_id>".$rules[$i]['content_ID']."</content_id>";
                $str .= "<rule_content_id>".$rules[$i]['rule_content_ID']."</rule_content_id>";
                $str .= "<rule_type>".$rules[$i]['rule_type']."</rule_type>";
                $str .= "<rule_option>".$rules[$i]['rule_option']."</rule_option>";
                $str .= '</rule>';
            }
            $str .= '</rules>';
        }
        //write out the questions
        $questions = ef_getTableData("questions q, content c", "q.*", "q.content_id = c.id and c.lessons_id=".$this->lesson['id']."");
        if (sizeof($questions) > 0){
            $str .= "<questions>";
            for ($i = 0; $i < sizeof($questions); $i++){
                $str .= "<question>";
                $str .= "<refid>q".$questions[$i]['id']."</refid>";
                $str .= "<type>".$questions[$i]['type']."</type>";
                $str .= "<difficulty>".$questions[$i]['difficulty']."</difficulty>";
                $str .= "<options>".$questions[$i]['options']."</options>";
                $str .= "<answer>".$questions[$i]['answer']."</answer>";
                $str .= "<explanation>".$questions[$i]['explanation']."</explanation>";
                $str .= "</question>";
            }
            $str .= "</questions>";
        }
        //write out the tests
        $tests = ef_getTableData("tests t, content c", "t.*", "t.content_id = c.id and c.lessons_id=".$this->lesson['id']."");
        if (sizeof($tests) > 0){
            $str .= "<tests>";
            for ($i = 0; $i < sizeof($tests); $i++){
                $str .= "<test>";
                $str .= "<refid>t".$tests[$i]['id']."</refid>";
                $str .= "<duration>".$tests[$i]['duration']."</duration>";
                $str .= "<redoable>".$tests[$i]['redoable']."</redoable>";
                $str .= "<onebyone>".$tests[$i]['onebyone']."</onebyone>";
                $str .= "<answers>".$tests[$i]['answers']."</answers>";
                $str .= "<shuffle_questions>".$tests[$i]['shuffle_questions']."</shuffle_questions>";
                $str .= "<shuffle_answers>".$tests[$i]['shuffle_answers']."</shuffle_answers>";
                $str .= "<given_answers>".$tests[$i]['given_answers']."</given_answers>";
                $questions = ef_getTableData("tests_to_questions","*","tests_ID = ".$tests[$i]['id']);
                $str .= "<questions>";
                for ($j = 0; $j < sizeof($questions); $j++){
                    $str .= "<question>";
                    $str .= "<refid>q".$questions[$j]['questions_ID']."</refid>";
                    $str .= "<weight>".$questions[$j]['weight']."</weight>";
                    $str .= "<previous>q".$questions[$j]['previous_question_ID']."</previous>";
                    $str .= "</question>";
                }
                $str .= "</questions>";
                $str .= "</test>";
            }
            $str .= "</tests>";
        }
        $str .= '</efrontlesson>';
        if ($fp = fopen($path . "/lesson".$this->lesson['id']."/eFrontLesson.xml", "wb")) {
          fwrite($fp, $str);
        }
        fclose($fp);
        chdir($path."/lesson". $this->lesson['id']);
        $d = dir(".");
        $entries = array();
        while (false !== ($entry = $d->read())) {
            if ($entry != "." & $entry != ".."){
                array_push($entries, $entry);
            }
        }
        $d->close();
        $zip = new Archive_zip($path."/lesson".$this->lesson['id'].".tar.gz");
        $zip -> create( $entries );
        chdir($cur_dir);
        deldir($path."/lesson". $this->lesson['id']);
    }
    public function fromIMS($ims_file, $deleteEntities = false){
        if ($deleteEntities) {
            $this -> initialize($deleteEntities); //Initialize the lesson aspects that the user specified
        }
        $timestamp = time();
        $zip = new Archive_zip($ims_file);
        $extracted_files = $zip -> extract(array('add_path' => G_LESSONSPATH.$this->lesson['id']."/IMS_".$timestamp));
        $total_fields = array();
        $resources = array();
        $tagArray = @eF_local_parseManifest(G_LESSONSPATH.$this->lesson['id']."/IMS_".$timestamp);
        $questions = array();
        $tests = array();
        $content = array();
        $inContent = false;
        $inTest = false;
        $inQuestion = false;
        $references = array();
        $questionsKeys = array();
        $testsKeys = array();
        foreach($tagArray as $key => $value) {
            $fields = array();
            switch ($value['tag']) {
                case 'TITLE':
                    $cur = $value['parent_index'];
                    if ($inContent){
                        $content[$cur]['name'] = $value['value'];
                    }
                    if ($inTest){
                        $tests[$cur]['name'] = $value['value'];
                    }
                    break;
                case 'ITEM':
                    $cur = $key;
                    if ($value['attributes']['TYPE'] != 'question' && $value['attributes']['TYPE'] != 'test')
                    {
                        $inContent = true;
                        $inTest = false;
                        $inQuestion = false;
                        $content[$key]['lessons_ID'] = $this->lesson['id'];
                        $content[$key]['timestamp'] = time();
                        $content[$key]['ctg_type'] = 'theory';
                        $content[$key]['active'] = 1;
                        $references[$key] = $value['attributes']['IDENTIFIERREF'];
                    }
                    else if ($value['attributes']['TYPE'] == 'test')
                    {
                        $inTest = true;
                        $inContent = false;
                        $inQuestion = false;
                        $tests[$key]['active'] = '1';
                        $testsKeys[$value['attributes']['IDENTIFIERREF']] = $key;
                        $references[$key] = $value['attributes']['IDENTIFIERREF'];
                    }
                    else if ($value['attributes']['TYPE'] == 'question')
                    {
                        $inQuestion = true;
                        $inContent = false;
                        $inTest = false;
                        $questions[$key]['difficulty'] = 'medium'; //initial value
                        $questions[$key]['type'] = 'true_false'; //initial value
                        $questions[$key]['timestamp'] = time();
                        $questionsKeys[$value['attributes']['IDENTIFIERREF']] = $key;
                        $references[$key] = $value['attributes']['IDENTIFIERREF'];
                    }
                    break;
                case 'RESOURCE':
                    $resources[$key] = $value['attributes']['IDENTIFIER'];
                    break;
                case 'FILE':
                    $files[$key] = $value['attributes']['HREF'];
                    break;
                case 'ADLCP:MAXTIMEALLOWED':
                    $maxtimeallowed[$key] = $value['value'];
                    break;
                case 'ADLCP:TIMELIMITACTION':
                    $timelimitaction[$key] = $value['value'];
                    break;
                case 'ADLCP:MASTERYSCORE':
                    $masteryscore[$key] = $value['value'];
                    break;
                case 'ADLCP:DATAFROMLMS':
                    $datafromlms[$key] = $value['value'];
                    break;
                case 'ADLCP:PREREQUISITES':
                    $prerequisites[$key] = $value['value'];
                    break;
                default:
                    break;
            }
        }
        foreach ($references as $key => $value) {
            $ref = array_search($value, $resources);
            if ($ref !== false && !is_null($ref)) {
                $data = file_get_contents(G_LESSONSPATH.$this->lesson['id']."/IMS_".$timestamp."/".$tagArray[$ref]['attributes']['HREF']);
                $primitive_hrefs[$ref] = $tagArray[$ref]['attributes']['HREF'];
                $path_part[$ref] = dirname($primitive_hrefs[$ref]);
                foreach($tagArray[$ref]['children'] as $value2) {
                    if ($tagArray[$value2]['tag'] == 'DEPENDENCY') {
                        $idx = array_search($tagArray[$value2]['attributes']['IDENTIFIERREF'], $resources);
                        foreach ($tagArray[$idx]['children'] as $value3) {
                            if ($tagArray[$value3]['tag'] == 'FILE') {
                                $data = preg_replace("#(\.\.\/(\w+\/)*)?".$tagArray[$value3]['attributes']['HREF']."#", G_LESSONSPATH.$this->lesson['id']."/".$path_part[$ref]."/$1".$tagArray[$value3]['attributes']['HREF'], $data);
                            }
                        }
                    }
                }
                if ($content[$key]['active'] == 1){
                    $i1 = stripos($data, "<body");
                    $i2 = stripos($data, ">", $i1);
                    $i3 = strripos($data, "<script");
                    $data = substr($data, $i2 + 1, $i3 - $i2 - 1);
                    $content[$key]['data'] = $data;
                }
                else if ($tests[$key]){
                    $data = $data;
                    $tests[$key]['description'] = $data;
                }
                else if ($questions[$key]){
                    $data = $data;
                    $questions[$key]['text'] = $data;
                }
            }
        }
        $inStart = true;
        foreach ($content as $key => $value){
            $cid = ef_insertTableData("content", $value);
            /* TODO INDEX */
            if ($inStart){
                $inStart = false;
                foreach ($questions as $keyq => $valueq){
                    $questions[$keyq]['content_ID'] = $cid;
                }
            }
        }
        foreach ($questions as $key => $value){
            $qid = ef_insertTableData("questions", $value);
            $questions[$key]['id'] = $qid;
        }
        foreach ($tests as $key => $value){
            $test_content['lessons_ID'] = $this->lesson['id'];
            $test_content['timestamp'] = time();
            $test_content['ctg_type'] = 'tests';
            $test_content['active'] = 1;
            $test_content['name'] = $value['name'];
            unset($value['name']);
            $cid = ef_insertTableData("content", $test_content);
            $value['content_id'] = $cid;
            $tid = ef_insertTableData("tests", $value);
            $tests[$key]['id'] = $tid;
        }
        eF_local_buildDirectories(G_LESSONSPATH.$this->lesson['id']."/", G_LESSONSPATH.$this->lesson['id']."/IMS_".$timestamp);
        foreach ($files as $key => $value) {
    //      if (!in_array($value, $primitive_hrefs))
    //      {
                $newhref = $tagArray[$tagArray[$key]['parent_index']]['attributes']['XML:BASE'];
                //copy(G_LESSONSPATH.$this->lesson['id']."/IMS_".$timestamp."/".trim($newhref,"/")."/".trim($value,"/"), G_LESSONSPATH.$this->lesson['id']."/".trim($newhref,"/")."/".trim($value,"/"));
    //      }
        }
        $cur_dir = getcwd();
        chdir(G_LESSONSPATH.$this->lesson['id']."/IMS_".$timestamp);
        $filelist = eF_getDirContents();
        foreach ($filelist as $value) {
            copy($value, G_LESSONSPATH.$this->lesson['id']."/".$value);
        }
        chdir($cur_dir);
        foreach ($prerequisites as $key => $value) {
            foreach ($tagArray as $key2 => $value2) {
                if (isset($value2['attributes']['IDENTIFIER']) && $value2['attributes']['IDENTIFIER'] == $value) {
                    unset($fields_insert);
                    $fields_insert['users_LOGIN'] = "*";
                    $fields_insert['content_ID'] = $tagArray[$tagArray[$key]['parent_index']]['this_id'];
                    $fields_insert['rule_type'] = "hasnot_seen";
                    $fields_insert['rule_content_ID'] = $value2['this_id'];
                    $fields_insert['rule_option'] = 0;
                    //eF_insertTableData("rules", $fields_insert);
                }
            }
        }
        //read the special EfrontLesson.xml
        $xmlfile = G_LESSONSPATH.$this->lesson['id']."/IMS_".$timestamp."/EfrontLesson.xml";
        $xml = simplexml_load_file($xmlfile);
        for ($i = 0; $i < sizeof($xml->conditions->condition); $i++){
            $condition = array();
            $condition['type'] = (string)$xml->conditions->condition[$i]->type;
            $condition['options'] = (string)$xml->conditions->condition[$i]->options;
            $condition['relation'] = (string)$xml->conditions->condition[$i]->relation;
            $condition['lessons_ID'] = $this->lesson['id'];
            $cid = ef_insertTableData("lesson_conditions", $condition);
        }
        for ($i = 0; $i < sizeof($xml->glossary->word); $i++){
            $glossary = array();
            $glossary['name'] = (string)$xml->glossary->word[$i]->name;
            $glossary['type'] = (string)$xml->glossary->word[$i]->type;
            $glossary['info'] = (string)$xml->glossary->word[$i]->info;
            $glossary['active'] = (string)$xml->glossary->word[$i]->active;
            $glossary['lessons_ID'] = $this->lesson['id'];
            $cid = ef_insertTableData("glossary", $glossary);
        }
        for ($i = 0; $i < sizeof($xml->questions->question); $i++){
            $update = array();
            $refid = (string)$xml->questions->question[$i]->refid;
            $qk = $questionsKeys[$refid];
            $qid = $questions[$qk]['id'];
            $update['type'] = (string)$xml->questions->question[$i]->type;
            $update['difficulty'] = (string)$xml->questions->question[$i]->difficulty;
            $update['options'] = (string)$xml->questions->question[$i]->options;
            $update['answer'] = (string)$xml->questions->question[$i]->answer;
            $update['explanation'] = (string)$xml->questions->question[$i]->explanation;
            ef_updateTableData("questions", $update, "id = $qid");
        }
        for ($i = 0; $i < sizeof($xml->tests->test); $i++){
            $update = array();
            $refid = (string)$xml->tests->test[$i]->refid;
            $tk = $testsKeys[$refid];
            $tid = $tests[$tk]['id'];
            $update['duration'] = (string)$xml->tests->test[$i]->duration;
            $update['redoable'] = (string)$xml->tests->test[$i]->redoable;
            $update['onebyone'] = (string)$xml->tests->test[$i]->onebyone;
            $update['answers'] = (string)$xml->tests->test[$i]->answers;
            $update['shuffle_questions'] = (string)$xml->tests->test[$i]->shuffle_questions;
            $update['shuffle_answers'] = (string)$xml->tests->test[$i]->shuffle_answers;
            $update['given_answers'] = (string)$xml->tests->test[$i]->given_answers;
            ef_updateTableData("tests", $update, "id=".$tid);
            for ($j = 0; $j < sizeof($xml->tests->test[$i]->questions->question); $j++){
                $testQuestions = array();
                $refid = (string)$xml->tests->test[$i]->questions->question[$j]->refid;
                $qk = $questionsKeys[$refid];
                $qid = $questions[$qk]['id'];
                $previd = (string)$xml->tests->test[$i]->questions->question[$j]->previous;
                if ($previd != "q0"){
                    $pk = $questionsKeys[$previd];
                    $pid = $questions[$pk]['id'];
                }
                else
                    $pid = 0;
                $testQuestions['tests_ID'] = $tid;
                $testQuestions['questions_ID'] = $qid;
                $testQuestions['previous_question_ID'] = $pid;
                $testQuestions['weight'] = (string)$xml->tests->test[$i]->questions->question[$j]->weight;
                ef_insertTableData("tests_to_questions", $testQuestions);
            }
        }
    }
    /**

     * Import lesson

     *

     * This function is used to import a lesson exported to a file

     * The first step is to optionally initialize the lesson, using initialize().

     * It then uncompresses the given file and proceeds to importing

     * <br/>Example:

     * <code>

     * try {

     *     $lesson = new EfrontLesson(32);                                             //32 is the lesson id

     *     $file = new EfrontFile($lesson -> getDirectory().'data.tar.gz');            //The file resides inside the lesson directory and is called 'data.tar.gz'

     *     $lesson -> import(array('content'), $file);

     * } catch (Exception $e) {

     *     echo $e -> getMessage();

     * }

     * </code><br/>

     *

     * @param EfrontFile $file The compressed lesson file object

     * @param array $deleteEntities The lesson aspects to initialize

     * @param boolean $lessonProperties Whether to import lesson properties as well

     * @param boolean $keepName Whether to keep the current (false) or the original name (true)

     * @return boolean True if the importing was successful

     * @since 3.5.0

     * @access public

     * @see EfrontLesson :: initialize()"

     */
    public function import($file, $deleteEntities = false, $lessonProperties = false, $keepName = false) {
        if ($deleteEntities) {
            $this -> initialize($deleteEntities); //Initialize the lesson aspects that the user specified
        }
        if (!($file instanceof EfrontFile)) {
            $file = new EfrontFile($file);
        }
        $fileList = $file -> uncompress();
        $file -> delete();
        $fileList = array_unique(array_reverse($fileList, true));
        $dataFile = new EfrontFile($this -> directory.'data.dat');
        $filedata = file_get_contents($dataFile['path']);
        $dataFile -> delete();
        $data = unserialize($filedata);
        $data['content'] = self :: eF_import_fixTree($data['content'], $last_current_node);
        for ($i = 0; $i < sizeof($data['files']); $i++) {
            if (isset($data['files'][$i]['file'])) {
                $newName = str_replace(G_ROOTPATH, '', dirname($data['files'][$i]['file']).'/'.EfrontFile :: encode(basename($data['files'][$i]['file'])));
                $newName = preg_replace("#(.*)www/content/lessons/#", "www/content/lessons/", $newName);
                $newName = preg_replace("#www/content/lessons/\d+/(.*)#", "www/content/lessons/".$this -> lesson['id']."/\$1", $newName);
                if ($data['files'][$i]['original_name'] != basename($data['files'][$i]['file'])) {
                    if (is_file(G_ROOTPATH.$newName)) {
                        $replaceString['/\/?(view_file.php\?file=)'.$data['files'][$i]['id'].'([^0-9])/'] = '${1}'.array_search(G_ROOTPATH.$newName, $fileList).'${2}'; //Replace old ids with new ids
                        //$mp[$data['files'][$i]['id']] = array_search(G_ROOTPATH.$newName, $fileList);
                        $file = new EfrontFile(G_ROOTPATH.$newName);
                        $file -> rename(G_ROOTPATH.dirname($newName).'/'.EfrontFile :: encode(rtrim($data['files'][$i]['original_name'], "/")));
                    }
                }
            } else {
                $newName = preg_replace("#www/content/lessons/\d+/(.*)#", "www/content/lessons/".$this -> lesson['id']."/\$1", $data['files'][$i]['path']);
                if (is_file(G_ROOTPATH.$newName)) {
                    $replaceString['/\/?(view_file.php\?file=)'.$data['files'][$i]['id'].'([^0-9])/'] = '${1}'.array_search(G_ROOTPATH.$newName, $fileList).'${2}'; //Replace old ids with new ids
                }
            }
        }
        for ($i = 0; $i < sizeof($data['files']); $i++) {
            if (isset($data['files'][$i]['file'])) {
                $newName = str_replace(G_ROOTPATH, '', dirname($data['files'][$i]['file']).'/'.EfrontFile :: encode(basename($data['files'][$i]['file'])));
                $newName = preg_replace("#(.*)www/content/lessons/#", "www/content/lessons/", $newName);
                $newName = preg_replace("#www/content/lessons/\d+/(.*)#", "www/content/lessons/".$this -> lesson['id']."/\$1", $newName);
                if ($data['files'][$i]['original_name'] != basename($data['files'][$i]['file'])) {
                    if (is_dir(G_ROOTPATH.$newName)) {
                        $file = new EfrontDirectory(G_ROOTPATH.$newName);
                        $file -> rename(G_ROOTPATH.dirname($newName).'/'.EfrontFile :: encode(rtrim($data['files'][$i]['original_name'], "/")));
                    }
                }
            }
        }
        unset($data['files']);
        $last_current_node = 0;
        $existing_tree = eF_getContentTree($nouse, $this -> lesson['id'], 0, false, false);
        if (sizeof($existing_tree) > 0) {
            $last_current_node = $existing_tree[sizeof($existing_tree) - 1]['id'];
            $first_node = self :: eF_import_getTreeFirstChild($data['content']);
            $data['content'][$first_node]['previous_content_ID'] = $last_current_node;
        }
        // MODULES - Import module data
        // Get all modules (NOT only the ones that have to do with the user type)
        $modules = eF_loadAllModules();
        foreach ($modules as $module) {
            if (isset($data[$module->className])) {
                $module -> onImportLesson($this -> lesson['id'], $data[$module->className]);
                unset($data[$module->className]);
            }
        }
        $dbtables = eF_showTables();
        //Skip tables that don't exist in current installation, such as modules' tables
        foreach (array_diff(array_keys($data), $dbtables) as $value) {
            unset($data[$value]);
        }
        //tests_to_questions table requires special handling	
  //$testsToQuestions = $data['tests_to_questions'];
  //unset($data['tests_to_questions']);
        if (!$data['questions'] && $data['tests_to_questions']) {
            unset($data['tests_to_questions']);
        }
        foreach ($data as $table => $tabledata) {
            if ($table == "glossary_words") {
                $table = "glossary";
            }
            if ($table == "lessons") { //from v3 lessons parameters also imported
                if ($lessonProperties) {
                    unset($data['lessons']['id']);
                    unset($data['lessons']['directions_ID']);
                    unset($data['lessons']['created']);
                    $this -> lesson = array_merge($this -> lesson, $data['lessons']);
                    $this -> persist();
                } else {
                 eF_updateTableData("lessons", array('info' => $data['lessons']['info'],
                                                     'metadata' => $data['lessons']['metadata'],
                                                     'options' => $data['lessons']['options']), "id=".$this -> lesson['id']);
                }
                if ($keepName) {
                    eF_updateTableData("lessons", array("name" => $data['lessons']['name']), "id=".$this -> lesson['id']);
                }
            } else {
                if ($table == "questions") {
                    foreach ($tabledata as $key => $value) {
                        unset($tabledata[$key]['timestamp']);
                        $tabledata[$key]['lessons_ID'] = $this -> lesson['id'];
      if ($tabledata[$key]['estimate'] == "") {
       unset($tabledata[$key]['estimate']);
      }
      if (isset($tabledata[$key]['code'])) { //code field removed in version 3.6
       unset($tabledata[$key]['code']);
      }
                    }
                }
                if ($table == "tests") {
                    for ($i = 0; $i < sizeof($tabledata); $i++) {
                        if (!isset($tabledata[$i]['options'])) {
                            $tabledata[$i]['options'] = serialize(array('duration' => $tabledata[$i]['duration'],
                                                                        'redoable' => $tabledata[$i]['redoable'],
                                                                        'onebyone' => $tabledata[$i]['onebyone'],
                                                                        'answers' => $tabledata[$i]['answers'],
                                                                        'given_answers' => $tabledata[$i]['given_answers'],
                                                                        'shuffle_questions' => $tabledata[$i]['shuffle_questions'],
                                                                        'shuffle_answers' => $tabledata[$i]['shuffle_answers']));
                            unset($tabledata[$i]['duration']);
                            unset($tabledata[$i]['redoable']);
                            unset($tabledata[$i]['onebyone']);
                            unset($tabledata[$i]['answers']);
                            unset($tabledata[$i]['given_answers']);
                            unset($tabledata[$i]['shuffle_questions']);
                            unset($tabledata[$i]['shuffle_answers']);
                        }
                    }
                }
                for ($i = 0; $i < sizeof($tabledata); $i++) {
                    if ($table == "tests") {
                        if (!isset($tabledata[$i]['lessons_ID'])) {
                            $tabledata[$i]['lessons_ID'] = $this -> lesson['id'];
                        }
                    }
                    if ($tabledata[$i]) {
                     $sql = "INSERT INTO ".G_DBPREFIX.$table." SET ";
                     $connector = "";
                     $fields = array();
                        foreach ($tabledata[$i] as $key => $value) {
                            if ($key == "id") {
                                $old_id = $value;
                            } else {
                                if (($table == "content" AND $key == "data") || ($table == "questions" AND $key == "text") || ($table == "tests" AND $key == "description")) {
                                 $value = str_replace("##SERVERNAME##", "", $value);
                                    //$value = str_replace("/##LESSONSLINK##", "content/lessons/".$this -> lesson['id'], $value);
                                    $value = str_replace("##LESSONSLINK##", "content/lessons/".$this -> lesson['id'], $value);
                                    $content_data = $value;
                                } elseif ($key == "lessons_ID") {
                                    $value = $this -> lesson['id'];
                                } elseif ($table == "lesson_conditions" AND $key == "options") {
                                    if (mb_strpos($data['lesson_conditions'][$i]['type'], "specific") === false){
                                    }else{
                                        $options = unserialize($data['lesson_conditions'][$i]['options']);
                                        $options[0] = $map['content'][$options[0]];
                                        $value = serialize($options);
                                    }
                                }
                                elseif ($table != "content" AND mb_substr($key, -3) == "_ID") {
                                    $from_table = mb_substr($key, 0, -3);
                                    if (isset($map[$from_table][$value])) {
                                        $value = $map[$from_table][$value];
                                    }
                                }
                                if ($table == 'scorm_sequencing_content_to_organization' && $key == 'organization_content_ID') {
                                    $value = $map['content'][$value];
                                }
                                if ($table == 'scorm_sequencing_maps_info' && $key == 'organization_content_ID') {
                                    $value = $map['content'][$value];
                                }
                                if ($table == "content" AND $key == 'previous_content_ID' AND !$value) {
                                    $value = 0;
                                }
                                if (!($table == "content" AND $key == "format")) {
                                    //$sql .= $connector.$key."='".str_replace("'","''",$value)."'";
                                    //$connector = ", ";
                                    $fields[$key] = $value;
                                }
                                if ($table == "content" AND $key == "name") {
                                    $content_name = $value;
                                }
                            }
                        }
                        $new_id = eF_insertTableData($table, $fields);
                     //eF_executeNew($sql);
                     //$new_id = mysql_insert_id();
                     //$GLOBALS['db']->debug=true;
                     if ($table == "content") {
                         EfrontSearch :: insertText($content_name, $new_id, "content", "title");
                         EfrontSearch :: insertText(strip_tags($content_data), $new_id, "content", "data");
                     }
                     $map[$table][$old_id] = $new_id;
                    }
                }
            }
        }
        if ($data['content']) {
   $map['content'] = array_reverse($map['content'], true);
            foreach($map['content'] as $old_id => $new_id) {
                eF_updateTableData("content", array('parent_content_ID' => $new_id), "parent_content_ID=$old_id AND lessons_ID=".$this -> lesson['id']);
                eF_updateTableData("content", array('previous_content_ID' => $new_id), "previous_content_ID=$old_id AND lessons_ID=".$this -> lesson['id']);
                //eF_updateTableData("questions", array('content_ID' => $new_id), "content_ID=$old_id");
            }
        }
        if ($data['rules']) {
            foreach($map['content'] as $old_id => $new_id) {
                eF_updateTableData("rules", array('rule_content_ID' => $new_id), "rule_content_ID=$old_id");
            }
        }
        // Update lesson skill
        $lessonSkillId = $this -> getLessonSkill();
        // The lesson offers skill record remains the same
        if ($lessonSkillId) {
            eF_updateTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFLESSON . " ". $this -> lesson['name'] , "categories_ID" => -1), "skill_ID = ". $lessonSkillId['skill_ID']);
        }
        if ($data['questions']) {
            foreach($map['questions'] as $old_id => $new_id) {
    eF_updateTableData("tests_to_questions", array('previous_question_ID' => $new_id), "previous_question_ID=$old_id and tests_ID in (select id from tests where lessons_ID=".$this -> lesson['id'].")");
                // Update all questions of not course_only lessons to offer the lessons skill
                if ($lessonSkillId) {
                    eF_insertTableData("questions_to_skills", array("questions_id" => $new_id, "skills_ID" => $lessonSkillId['skill_ID'], "relevance" => 2));
                }
                //eF_insertTableData("questions_to_skills", array("q
                //$questions = eF_getTableDataFlat("questions", "id", "lessons_ID = ". $this ->lesson['id']);
                //eF_deleteTableData("questions_to_skills", "questions_id IN ('".implode("','",$questions['id'])."')");
            }
        }
        foreach ($map['content'] as $old_id => $new_id) { //needs debugging
            $content_new_IDs[] = $new_id;
        }
        $content_new_IDs_list = implode(",",$content_new_IDs);
        if ($content_new_IDs_list) {
            $content_data = eF_getTableData("content", "data,id", "id IN ($content_new_IDs_list) AND lessons_ID=".$this -> lesson['id']);
        }
        if (isset($replaceString)) {
            for ($i = 0; $i < sizeof($content_data); $i++) {
                $replaced = preg_replace(array_keys($replaceString), array_values($replaceString), $content_data[$i]['data']);
                eF_updateTableData("content", array('data' => $replaced), "id=".$content_data[$i]['id']);
                EfrontSearch :: removeText('content', $content_data[$i]['id'], 'data'); //Refresh the search keywords
                EfrontSearch :: insertText($replaced, $content_data[$i]['id'], "content", "data");
            }
        }
        if ($content_new_IDs_list) {
            $content_data = eF_getTableData("content", "data,id", "id IN ($content_new_IDs_list) AND lessons_ID=".$this -> lesson['id']." AND data like '%##EFRONTINNERLINK##%'");
        }
        for($i =0; $i < sizeof($content_data); $i++) {
         preg_match_all("/##EFRONTINNERLINK##.php\?ctg=content&amp;view_unit=(\d+)/", $content_data[$i]['data'], $regs);
         foreach ($regs[1] as $value) {
             $replaced = str_replace("##EFRONTINNERLINK##.php?ctg=content&amp;view_unit=".$value,"##EFRONTINNERLINK##.php?ctg=content&amp;view_unit=".$map["content"][$value], $content_data[$i]['data']);
             eF_updateTableData("content", array('data' => $replaced), "id=".$content_data[$i]['id']);
             EfrontSearch :: removeText('content', $content_data[$i]['id'], 'data'); //Refresh the search keywords
             EfrontSearch :: insertText($replaced, $content_data[$i]['id'], "content", "data");
         }
        }
        $tests = eF_getTableData("tests t, content c", "t.id, t.name, c.name as c_name", "t.content_ID=c.id");
        foreach ($tests as $test) {
            if (!$test['name']) {
                eF_updateTableData("tests", array("name" => $test['c_name']), "id=".$test['id']);
            }
        }
//exit;        
        return true;
    }
    /**

     * Fix efront v1.x trees

     *

     * This function is used for fixing lessons from efront version 1 that

     * are beeing imported to the system

     *

     * @param array $tree The old content tree

     * @param int $last_current_node The last current node

     * @return array The fixed tree

     * @since 3.5.0

     * @access private

     */
    private function eF_import_fixTree($tree, $last_current_node = 0)
    {
        for ($i = 0; $i < sizeof($tree); $i++) {
            if ($tree[$i]['parent_content_ID'] == 0) {
                $roots[$i] = $tree[$i];
                $roots[$i]['idx'] = $i;
            }
        }
        foreach ($roots as $key => $node) {
            $roots[$key]['last_child'] = self :: eF_import_getLastChild($tree, $key);
            if ($node['previous_content_ID'] == 0 && $node['parent_content_ID'] == 0) {
                $eligible[] = $roots[$key];
            }
        }
        foreach ($eligible as $key => $node) {
            $timestamps[] = $node['timestamp'];
            $found = true;
            $temp = $roots;
            $eligible[$key]['final_child'] = $node['last_child'];
            while (sizeof($temp) > 0 && $found) {
                $found = false;
                foreach ($temp as $temp_key => $temp_node) {
                    if ($eligible[$key]['final_child'] == $temp_node['previous_content_ID']) {
                        $eligible[$key]['final_child'] = $temp_node['last_child'];
                        unset($temp[$temp_key]);
                        $found = true;
                    }
                }
            }
        }
        array_multisort($timestamps, SORT_ASC, $eligible);
        for ($i = 0; $i < sizeof($eligible); $i++) {
            if ($i < sizeof($eligible) - 1) {
                $eligible[$i + 1]['previous_content_ID'] = $eligible[$i]['final_child'];
            }
            unset($eligible[$i]['last_child']);
            unset($eligible[$i]['final_child']);
            $idx = $eligible[$i]['idx'];
            unset($eligible[$i]['idx']);
            $tree[$idx] = $eligible[$i];
        }
        return $tree;
    }
    /**

     * Get the tree last child

     *

     * This function is used for importing lessons and returns the tree's last child

     *

     * @param array $tree The content tree

     * @param int $idx The current index

     * @return int The last children index

     * @since 3.5.0

     * @access private

     */
    private function eF_import_getLastChild($tree, $idx) {
        $original_tree = $tree;
        $count = 0;
        $children[$idx] = $tree[$idx]['id'];
        $found = true;
        while (sizeof($tree) > 0 && $count++ < 1000 && $found) {
            $found = false;
            foreach ($tree as $key => $node) {
                if (in_array($node['parent_content_ID'], $children)) {
                    $children[$key] = $node['id'];
                    unset($tree[$key]);
                    $found = true;
                }
            }
        }
        foreach ($children as $key => $child) {
            $previous[] = $original_tree[$key]['previous_content_ID'];
        }
        $last = (array_diff($children, $previous));
        $last = array_values($last);
        return $last[0];
    }
    /**

     * Get the first child of the tree

     *

     * This function is used for importing lessons and returns the tree's first child

     *

     * @param array $tree

     * @return int the first child index

     * @since 3.5.0

     * @access private

     */
    private function eF_import_getTreeFirstChild($tree)
    {
        $count = 0;
        while ($tree[$count]['parent_content_ID'] != 0 || $tree[$count]['previous_content_ID'] != 0)
        {
            $count++;
        }
        $first_node = $count;
        return $first_node;
    }
    /**

     * Export lesson

     *

     * This function is used to export the current lesson's data to

     * a file, which can then be imported to other systems. Apart from

     * the lesson content, the user may optinally specify additional

     * information to export, using the $exportEntities array. If

     * $exportEntities is 'all', everything that can be exported, is

     * exported

     *

     * <br/>Example:

     * <code>

     * $exportedFile = $lesson -> export('all');

     * </code>

     *

     * @param array $exportEntities The additional data to export

     * @param boolean $rename Whether to rename the exported file with the same name as the lesson

     * @return EfrontFile The object of the exported data file

     * @since 3.5.0

     * @access public

     */
    public function export($exportEntities, $rename = true) {
        if (!$exportEntities) {
            $exportEntities = array('export_surveys' => 1, 'export_announcements' => 1, 'export_glossary' => 1,
                                    'export_calendar' => 1, 'export_comments' => 1, 'export_rules' => 1);
        }
        if (is_file($this -> directory.'data.tar.gz')) {
            try {
                $file = new EfrontFile($this -> directory.'data.tar.gz');
                $file -> delete();
            } catch (EfrontFileException $e) {
                unlink($this -> directory.'data.tar.gz');
            }
        }
        $data['lessons'] = $this -> lesson;
        unset($data['lessons']['shared_folder']);
        $content = eF_getTableData("content", "*", "lessons_ID=".$this -> lesson['id']);
        if (sizeof($content) > 0) {
            for ($i = 0; $i < sizeof($content); $i++) {
                $content[$i]['data'] = str_replace(G_SERVERNAME, "##SERVERNAME##", $content[$i]['data']);
                $content[$i]['data'] = str_replace("content/lessons/".$this -> lesson['id'], "##LESSONSLINK##", $content[$i]['data']);
            }
            $content_list = implode(",", array_keys($content));
            $data['content'] = $content;
            $questions = eF_getTableData("questions", "*", "lessons_ID=".$this -> lesson['id']);
            if (sizeof($questions) > 0) {
                for ($i = 0; $i < sizeof($questions); $i++) {
                    $questions[$i]['text'] = str_replace(G_SERVERNAME, "##SERVERNAME##", $questions[$i]['text']);
                    $questions[$i]['text'] = str_replace("content/lessons/".$this -> lesson['id'], "##LESSONSLINK##", $questions[$i]['text']);
                }
                $data['questions'] = $questions;
            }
            $tests = eF_getTableData("tests", "*", "lessons_ID=".$this -> lesson['id']);
            if (sizeof($tests)) {
    $testsIds = array();
    foreach ($tests as $key => $value) {
     $testsIds[] = $value['id'];
    }
                $tests_list = implode(",", array_values($testsIds));
                $tests_to_questions = eF_getTableData("tests_to_questions", "*", "tests_ID IN ($tests_list)");
                for ($i = 0; $i < sizeof($tests); $i++) {
                    $tests[$i]['description'] = str_replace(G_SERVERNAME, "##SERVERNAME##", $tests[$i]['description']);
                    $tests[$i]['description'] = str_replace("content/lessons/".$this -> lesson['id'], "##LESSONSLINK##", $tests[$i]['description']);
                }
                $data['tests'] = $tests;
                $data['tests_to_questions'] = $tests_to_questions;
            }
            if (isset($exportEntities['export_rules'])) {
                $rules = eF_getTableData("rules", "*", "lessons_ID=".$this -> lesson['id']);
                if (sizeof($rules) > 0) {
                    $data['rules'] = $rules;
                }
            }
            if (isset($exportEntities['export_comments'])) {
                $comments = eF_getTableData("comments", "*", "content_ID IN ($content_list)");
                if (sizeof($comments) > 0) {
                    $data['comments'] = $comments;
                }
            }
        }
        if (isset($exportEntities['export_calendar'])) {
            $calendar = eF_getTableData("calendar", "*", "lessons_ID=".$this -> lesson['id']);
            if (sizeof($calendar) > 0) {
                $data['calendar'] = $calendar;
            }
        }
        if (isset($exportEntities['export_glossary'])) {
            $glossary = eF_getTableData("glossary", "*", "lessons_ID = ".$this -> lesson['id']);
            if (sizeof($glossary) > 0) {
                $data['glossary'] = $glossary;
            }
        }
        if (isset($exportEntities['export_announcements'])) {
            $news = eF_getTableData("news", "*", "lessons_ID=".$this -> lesson['id']);
            if (sizeof($news) > 0) {
                $data['news'] = $news;
            }
        }
        if (isset($exportEntities['export_surveys'])) {
            $surveys = eF_getTableData("surveys", "*", "lessons_ID=".$this -> lesson['id']); //prepei na ginei to   lesson_ID -> lessons_ID sti basi (ayto isos to parampsoyme eykola)
            if (sizeof($surveys) > 0) {
                $data['surveys'] = $surveys;
                $surveys_list = implode(",", array_keys($surveys));
                $questions_to_surveys = eF_getTableData("questions_to_surveys", "*", "surveys_ID IN ($surveys_list)"); // oposipote omos to survey_ID -> surveys_ID sti basi
                if (sizeof($questions_to_surveys) > 0) {
                    $data['questions_to_surveys'] = $questions_to_surveys;
                }
            }
        }
        $lesson_conditions = eF_getTableData("lesson_conditions", "*", "lessons_ID=".$this -> lesson['id']);
        if (sizeof($lesson_conditions) > 0) {
            $data['lesson_conditions'] = $lesson_conditions;
        }
        $projects = eF_getTableData("projects", "*", "lessons_ID=".$this -> lesson['id']);
        if (sizeof($projects) > 0) {
            $data['projects'] = $projects;
        }
        $lesson_files = eF_getTableData("files", "*", "path like '".str_replace(G_ROOTPATH, '', EfrontDirectory :: normalize($this -> directory))."%'");
        if (sizeof($lesson_files) > 0) {
            $data['files'] = $lesson_files;
        }
        //'scorm_sequencing_rollup_rule', 'scorm_sequencing_rule',
        // MODULES - Export module data
        // Get all modules (NOT only the ones that have to do with the user type)
        $modules = eF_loadAllModules();
        foreach ($modules as $module) {
            if ($moduleData = $module -> onExportLesson($this -> lesson['id'])) {
                $data[$module -> className] = $moduleData;
            }
        }
        file_put_contents($this -> directory.'/'."data.dat", serialize($data)); //Create database dump file
        $lessonDirectory = new EfrontDirectory($this -> directory);
        $file = $lessonDirectory -> compress($this -> lesson['id'].'_exported.zip', false); //Compress the lesson files
        $newList = FileSystemTree :: importFiles($file['path']); //Import the file to the database, so we can download it
        $file = new EfrontFile(current($newList));
        $userTempDir = $GLOBALS['currentUser'] -> user['directory'].'/temp'; //The compressed file will be moved to the user's temp directory
        if (!is_dir($userTempDir)) { //If the user's temp directory does not exist, create it
            $userTempDir = EfrontDirectory :: createDirectory($userTempDir, false);
            $userTempDir = $userTempDir['path'];
        }
        try {
            $existingFile = new EfrontFile($userTempDir.'/'.$this -> lesson['name'].'.zip'); //Delete any previous exported files
            $existingFile -> delete();
        } catch (Exception $e) {}
        if ($rename) {
            $file -> rename($userTempDir.'/'.EfrontFile :: encode($this -> lesson['name']).'.zip', true);
        }
        unlink($this -> directory.'/'."data.dat"); //Delete database dump file
        return $file;
    }
    public function export2() {
        try {
         $dom = new DomDocument();
      $id = $dom -> createAttribute('id');// 
      $id -> appendChild($dom -> createTextNode($this -> lesson['id']));
      $lessonNode = $dom -> createElement("lesson");
      $lessonNode -> appendChild($id);
      $lessonNode = $dom -> appendChild($lessonNode);
      $parentNodes[0] = $lessonNode;
         $lessonContent = new EfrontContentTree($this -> lesson['id']);
         foreach ($iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($lessonContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $properties) {
             $result = eF_getTableData("content", "*", "id=$key");
          $contentNode = $dom -> appendChild($dom -> createElement("content")); //<content></content>
          $parentNodes[$iterator -> getDepth() + 1] = $contentNode;
          $attribute = $contentNode -> appendChild($dom -> createAttribute('id')); //<content id = ""></content>
          $attribute -> appendChild($dom -> createTextNode($key)); //<content id = "CONTENTID:32"></content>
          foreach ($result[0] as $element => $value) {
if ($element == 'data') $value = htmlentities($value);
              if ($element != 'id' && $element != 'previous_content_ID' && $element != 'parent_content_ID' && $element != 'lessons_ID') {
               $element = $contentNode -> appendChild($dom -> createElement($element));
               $element -> appendChild($dom -> createTextNode($value));
              }
          }
/*

		        if ($properties['ctg_type'] == 'tests') {

		            $result = eF_getTableData("tests", "*", "content_ID=$key");

		            foreach ($result[0] as $element => $value) {

		                

		            }

		        }

*/
             $parentNodes[$iterator -> getDepth()] -> appendChild($contentNode); //<lesson><content></content></lesson>
         }
         header("content-type:text/xml");
         echo (($dom -> saveXML()));
         //$content = eF_getTableData("content", "*", "lessons_ID=".$this -> lesson['id']);
        } catch (Exception $e) {
            pr($e);
        }
    }
    /**

     * Get system lessons

     *

     * This function is used used to return a list with all the system

     * lessons.

     * <br/>Example:

     * <code>

     * $lessons = EFrontLesson :: getLessons();

     * </code>

     *

     * @return array The lessons list

     * @since 3.5.0

     * @access public

     * @static

     */
    public static function getLessons($returnObjects = false) {
        $result = eF_getTableData("lessons l, directions d", "l.*, d.name as direction_name", "l.directions_ID=d.id and l.archive=0", "l.name");
        foreach ($result as $value) {
            if ($returnObjects){
                $lessons[$value['id']] = new EfrontLesson($value);
            } else {
             $value['info'] = unserialize($value['info']);
             $lessons[$value['id']] = $value;
            }
        }
        return $lessons;
    }
    /**

     * Get system lessons that do not currently belong to a course

     *

     * This function is used used to return a list with all stand-alone 

     * system lessons.

     * <br/>Example:

     * <code>

     * $independent_lessons = EFrontLesson :: getStandAloneLessons();

     * </code>

     *

     * @param boolean $returnObjects whether to return objects

     * @return array The lessons list

     * @since 3.5.0

     * @access public

     * @static

     */
    public static function getStandAloneLessons($returnObjects = false) {
        $result = eF_getTableData("lessons l, directions d", "l.*, d.name as direction_name", "l.directions_ID=d.id AND l.course_only=0");
        foreach ($result as $value) {
            $value['info'] = unserialize($value['info']);
            if ($returnObjects) {
             $lessons[$value['id']] = new EfrontLesson($value);
            } else {
             $lessons[$value['id']] = $value;
            }
        }
        return $lessons;
    }
    /**

     * Get options

     *

     * This function is used to get the lesson specified options

     * <br/>Example:

     * <code>

     * $options = array('theory', 'tests');

     * $lesson -> getOptions($options);             //Get the values of 'theory' and 'tests' options

     * $lesson -> getOptions();                     //Get all options

     * </code>

     *

     * @param array $options An array of lesson options

     * @return array The values of the requested options

     * @since 3.5.0

     * @access public

     */
    public function getOptions($options) {
        if ($options && !is_array($options)) {
            $options = array($options);
        }
        if (sizeof($options) > 0) {
            $requestedOptions = array();
            foreach ($options as $value) {
                if (isset($this -> options[$value])) {
                    $requestedOptions[$value] = $this -> options[$value];
                }
            }
            return $requestedOptions;
        } else {
            return $this -> options;
        }
    }
    /**

     * Set options

     *

     * This function sets the lesson options, based on the array

     * specified

     * <br/>Example:

     * <code>

     * $options = array('theory' => 1, 'tests' => 0);

     * $lesson -> setOptions($options);

     * </code>

     *

     * @param array $options An array of lesson options

     * @since 3.5.0

     * @access public

     */
    public function setOptions($options) {
        foreach ($options as $key => $value) {
            if (isset($this -> options[$key])) {
                $this -> options[$key] = $value;
            }
        }
        $this -> lesson['options'] = serialize($this -> options);
        eF_updateTableData("lessons", array("options" => $this -> lesson['options']), "id=".$this -> lesson['id']);
    }
    /**

     * Store database values

     *

     * This function is used to store changed lesson properties

     * to the database.

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(4);           //Instantiate lesson with id 4

     * $lesson -> lesson['name'] = 'new name';  //Change a lesson's property, for example its name

     * $lesson -> persist();                    //Store any changed values to the database

     * </code>

     *

     * @since 3.5.0

     * @access public

     */
    public function persist() {
        $fields = array('name' => $this -> lesson['name'],
                        'directions_ID' => $this -> lesson['directions_ID'],
                        'info' => $this -> lesson['info'],
                        'price' => str_replace(array($GLOBALS['configuration']['decimal_point'], $GLOBALS['configuration']['thousands_sep']), array('.', ''), $this -> lesson['price']),
                        'active' => $this -> lesson['active'],
                        'duration' => $this -> lesson['duration'] ? $this -> lesson['duration'] : 0,
//                        'share_folder'    => $this -> lesson['share_folder'] ? $this -> lesson['share_folder'] : 0,
      'show_catalog' => $this -> lesson['course_only'] ? 1 : $this -> lesson['show_catalog'], //if lesson is available only via course, it can not be hidden from catalog
                        'options' => serialize($this -> options),
                        'languages_NAME' => $this -> lesson['languages_NAME'],
                        'metadata' => $this -> lesson['metadata'],
                        'course_only' => $this -> lesson['course_only'],
                        'certificate' => $this -> lesson['certificate'],
//                        'auto_certificate'=> $this -> lesson['auto_certificate'],
//                        'auto_complete'   => $this -> lesson['auto_complete'],
//                        'publish'         => $this -> lesson['publish'],
                        'max_users' => $this -> lesson['max_users'] ? $this -> lesson['max_users'] : null,
                        'from_timestamp' => $this -> lesson['from_timestamp'] ? $this -> lesson['from_timestamp'] : 0,
                        'to_timestamp' => $this -> lesson['to_timestamp'] ? $this -> lesson['to_timestamp'] : 0,
                        'shift' => $this -> lesson['shift'],
                        'archive' => $this -> lesson['archive'],
            'created' => $this -> lesson['created']);
        if (!eF_updateTableData("lessons", $fields, "id=".$this -> lesson['id'])) {
            throw new EfrontUserException(_DATABASEERROR, EfrontUserException :: DATABASE_ERROR);
        }
        EfrontSearch :: removeText('lessons', $this -> lesson['id'], 'title'); //Refresh the search keywords
        EfrontSearch :: insertText($fields['name'], $this -> lesson['id'], "lessons", "title");
    }
    /**

     * Get lesson conditions

     *

     * This function can be used to retrieve the conditions set

     * for the current lesson

     * <br/>Example:

     * <code>

     * $lesson -> getConditions();                  //Returns an array with the lesson conditions

     * </code>

     *

     * @param array $conditions The conditions array, as a result query

     * @return array The lesson conditions

     * @since 3.5.0

     * @access public

     */
    public function getConditions($conditions = false) {
        if ($this -> conditions === false) {
         if (!$conditions) {
          $conditions = eF_getTableData("lesson_conditions", "*", "lessons_ID=".$this -> lesson['id']);
         }
            $this -> conditions = array();
            foreach ($conditions as $value) {
                $value['options'] = unserialize($value['options']);
                $this -> conditions[$value['id']] = $value;
            }
        }
        return $this -> conditions;
    }
    /**

     * Delete lesson conditions

     *

     * This function is used to delete one or more lesson conditions

     * <br/>Example:

     * <code>

     * $lesson -> deleteConditions(3);                                  //Delete condition with id 3

     * $lesson -> deleteConditions(array(3, 6, 34));                    //Delete conditions with ids 3,6 and 34

     * </code>

     *

     * @param mixed $conditions An id or an array of ids

     * @return array The remaining conditions

     * @since 3.5.0

     * @access public

     */
    public function deleteConditions($conditions) {
        if ($this -> conditions === false) { //Initialize conditions, if you haven't done so
            $this -> getConditions();
        }
        if (!is_array($conditions)) { //Convert single condition to array
            $conditions = array($conditions);
        }
        foreach ($conditions as $conditionId) {
            if (eF_checkParameter($conditionId, 'id') && in_array($conditionId, array_keys($this -> conditions))) {
                eF_deleteTableData("lesson_conditions", "id=$conditionId");
                unset($this -> rules[$conditionId]);
            }
        }
        return $this -> conditions;
    }
    /**

     * Get lesson projects

     *

     * This function is usd to retrieve the projects of this lesson

     * <br/>Example:

     * <code>

     * $lesson = new EfrontLesson(65);                          //Create new lesson object

     * $projectList = $lesson -> getProjects();                 //Get all projects for this lesson

     * $projectList = $lesson -> getProjects(true);             //Get all projects for this lesson as an EfrontProject objects list

     * $projectList = $lesson -> getProjects(true, 'jdoe');     //Get projects assigned to 'jdoe' for this lesson, as an EfrontProject objects list

     * </code>

     *

     * @param boolean $returnObjects Whether to return EfrontProject objects or just an array with projects properties

     * @param string $login If specified, return projects only assigned to this user

     * @return array an array of lesson projects

     * @since 3.5.0

     * @access public

     */
    public function getProjects($returnObjects = false, $login = false, $nonExpired = false) {
        if ($login instanceof EfrontUser) {
            $login = $login -> user['login'];
        }
        if ($login && eF_checkParameter($login, 'login')) {
            !$nonExpired ? $result = eF_getTableData("projects p, users_to_projects up", "p.*, up.grade, up.comments, up.filename", "up.users_LOGIN = '$login' and up.projects_ID = p.id and p.lessons_ID=".$this -> lesson['id']) : $result = eF_getTableData("projects p, users_to_projects up", "p.*, up.grade, up.comments, up.filename", "p.deadline > ".time()." and up.users_LOGIN = '$login' and up.projects_ID = p.id and p.lessons_ID=".$this -> lesson['id']);
        } else {
            !$nonExpired ? $result = eF_getTableData("projects", "*", "lessons_ID=".$this -> lesson['id']) : $result = eF_getTableData("projects", "*", "deadline > ".time()." and lessons_ID=".$this -> lesson['id']);
        }
        $projects = array();
        foreach ($result as $value) {
            $returnObjects ? $projects[$value['id']] = new EfrontProject($value) : $projects[$value['id']] = $value;
        }
        return $projects;
    }
    /**

     * Print a link with tooltip

     *

     * This function is used to print a lesson link with a popup tooltip

     * containing information on this lesson. The link must be provided

     * and optionally the information.

     * <br/>Example:

     * <code>

     * echo $lesson -> toHTMLTooltipLink('student.php?ctg=control_panel&lessons_ID=2');

     * </code>

     *

     * @param string $link The link to print

     * @param array $lessonInformation The information to display (According to the EfrontLesson :: getInformation() format)

     * @since 3.5.0

     * @access public

     */
    public function toHTMLTooltipLink($link, $lessonInformation = false) {
  if ($GLOBALS['configuration']['disable_tooltip'] != 1) {
   if (!$lessonInformation) {
    $lessonInformation = $this -> getInformation();
   }
   sizeof($lessonInformation['content']) > 0 || sizeof($lessonInformation['tests']) > 0 ? $classes[] = 'nonEmptyLesson' : $classes[] = 'emptyLesson'; //Display the link differently depending on whether it has content or not
   if (!$link) {
    $link = 'javascript:void(0)';
    $classes[] = 'inactiveLink';
   }
   if ($lessonInformation['professors']) {
    foreach ($lessonInformation['professors'] as $value) {
     $professorsString[] = $value['name'].' '.$value['surname'];
    }
    $lessonInformation['professors'] = implode(", ", $professorsString);
   }
   foreach ($lessonInformation as $key => $value) {
    if ($value) {
     switch ($key) {
      case 'professors' : $tooltipInfo[] = '<strong>'._PROFESSORS."</strong>: $value<br/>"; break;
      case 'content' : $tooltipInfo[] = '<strong>'._CONTENTUNITS."</strong>: $value<br/>"; break;
      case 'tests' : $tooltipInfo[] = '<strong>'._TESTS."</strong>: $value<br/>"; break;
      case 'projects' : $GLOBALS['configuration']['disable_projects'] != 1 ? $tooltipInfo[] = '<strong>'._PROJECTS."</strong>: $value<br/>" : null; break;
      case 'course_dependency' : $tooltipInfo[] = '<strong>'._DEPENDSON."</strong>: $value<br/>"; break;
      case 'from_timestamp' : $tooltipInfo[] = '<strong>'._AVAILABLEFROM."</strong>: ".formatTimestamp($value, 'time_nosec')."<br/>";break;
      case 'to_timestamp' : $tooltipInfo[] = '<strong>'._AVAILABLEUNTIL."</strong>: ".formatTimestamp($value, 'time_nosec')."<br/>"; break;
      case 'general_description': $tooltipInfo[] = '<strong>'._GENERALDESCRIPTION."</strong>: $value<br/>"; break;
      case 'assessment' : $tooltipInfo[] = '<strong>'._ASSESSMENT."</strong>: $value<br/>"; break;
      case 'objectives' : $tooltipInfo[] = '<strong>'._OBJECTIVES."</strong>: $value<br/>"; break;
      case 'lesson_topics' : $tooltipInfo[] = '<strong>'._LESSONTOPICS."</strong>: $value<br/>"; break;
      case 'resources' : $tooltipInfo[] = '<strong>'._RESOURCES."</strong>: $value<br/>"; break;
      case 'other_info' : $tooltipInfo[] = '<strong>'._OTHERINFO."</strong>: $value<br/>"; break;
      default: break;
     }
    }
   }
   if (sizeof($tooltipInfo) > 0) {
    $classes[] = 'info';
    $tooltipString = '
     <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;">
      '.$this -> lesson['name'].'
      <img class = "tooltip" border = "0" src="images/others/tooltip_arrow.gif"/><span class = "tooltipSpan">
      '.implode("", $tooltipInfo).'</span></a>';
   } else {
    $tooltipString = '
     <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;">
      '.$this -> lesson['name'].'</a>';
   }
  } else {
   $tooltipString = '
     <a href = "'.$link.'" class = "'.implode(" ", $classes).'" style = "vertical-align:middle;">
      '.$this -> lesson['name'].'</a>';
  }
        return $tooltipString;
    }
   /**

     * Get all skills: for the skills this lesson offers the lesson_ID value will be filled

     * 

     * <br/>Example:

     * <code>

     * $skillsOffered = $lesson -> getSkills();

     * </code>

     *

     * @param $only_own set true if only the skills of this lesson are to be returned and not all skills

     * @return an array with skills where each record has the form [skill_ID] => [lesson_ID, description, specification,skill_ID, categories_ID]

     * @since 3.5.0

     * @access public

     */
    public function getSkills($only_own = false) {
        if (!isset($this -> skills) || !$this -> skills) {
            $this -> skills = false; //Initialize skills to something
            $skills = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_lesson_offers_skill ON (module_hcd_lesson_offers_skill.skill_ID = module_hcd_skills.skill_ID AND module_hcd_lesson_offers_skill.lesson_ID='".$this -> lesson['id']."')", "description,specification, module_hcd_skills.skill_ID,lesson_ID,categories_ID","");
            foreach ($skills as $key => $skill) {
                if ($only_own && $skill['lesson_ID'] != $this -> lesson['id']) {
                    unset($skills[$key]);
                } else {
                 $skID = $skill['skill_ID'];
                    $this -> skills[$skID] = $skill;
                }
            }
        }
        return $this -> skills;
    }
   /**

     * Get all branches: for the branches this lesson offers the lesson_ID value will be filled

     * 

     * <br/>Example:

     * <code>

     * $branchesOfLesson = $lesson -> getBranches();

     * </code>

     *

     * @param $only_own set true if only the branches of this lesson are to be returned and not all branches

     * @return an array with branches where each record has the form [branch_ID] => [lesson_ID]

     * @since 3.6.0

     * @access public

     */
    public function getBranches($only_own = false) {
        if (!isset($this -> branches) || !$this -> branches) {
            $this -> branches = false; //Initialize branches to something
            $branches = eF_getTableData("module_hcd_branch LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID LEFT OUTER JOIN module_hcd_lesson_to_branch ON (module_hcd_lesson_to_branch.branches_ID = module_hcd_branch.branch_ID AND module_hcd_lesson_to_branch.lessons_ID='".$this -> lesson['id']."')", "module_hcd_branch.*, module_hcd_branch.branch_ID as branches_ID, module_hcd_lesson_to_branch.lessons_ID, branch1.name as father","");
            foreach ($branches as $key => $branch) {
                if ($only_own && $branch['lessons_ID'] != $this -> lesson['id']) {
                    unset($branches[$key]);
                } else {
                 $bID = $branch['branches_ID'];
                    $this -> branches[$bID] = $branch;
                }
            }
        }
        return $this -> branches;
    }
   /**

     * Insert the skill corresponding to this lesson: Every lesson is mapped to a skill like "Knowledge of that lesson"

     * This insertion takes place when a lesson is changed from course_only to regular lesson

     *

     * <br/>Example:

     * <code>

     * $lesson -> insertLessonSkill();

     * </code>

     *

     * @return the id of the newly created record in the module_hcd_lesson_offers_skill table or false if something went wrong

     * @since 3.5.2

     * @access public

     */
    public function insertLessonSkill() {
        // If insertion of a self-contained lesson add the corresponding skill
        // Insert the corresponding lesson skill to the skill and lesson_offers_skill tables
        $lessonSkillId = eF_insertTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFLESSON . " ". $this -> lesson['name'], "categories_ID" => -1));
        // Insert question to lesson skill records for all lesson questions
        $questions = eF_getTableData("questions", "id", "lessons_ID = ". $this ->lesson['id']);
        $insert_string = "";
        foreach ($questions as $question) {
            if ($insert_string != "") {
                $insert_string .= ",('" . $question['id']. "','" . $lessonSkillId . "',2)";
            } else {
                $insert_string .= "('".$question['id']."','".$lessonSkillId."',2)";
            }
        }
        if ($insert_string != "") {
            eF_executeNew("INSERT INTO questions_to_skills VALUES " . $insert_string);
        }
        return eF_insertTableData("module_hcd_lesson_offers_skill", array("lesson_ID" => $this -> lesson['id'], "skill_ID" => $lessonSkillId));
    }
    /**

     * Function to remove all course inherited skills by all courses where this lesson belongs

     */
    public function removeCoursesInheritedSkills() {
        $courses = $this -> getCourses(true);
        foreach ($courses as $course) {
            $courseSkill = $course -> getCourseSkill();
            if ($courseSkill) {
                eF_deleteTableData("questions_to_skills", "skills_ID = " . $courseSkill['skill_ID']);
            }
        }
    }
   /**

     * Delete the skill corresponding to this lesson: Every lesson is mapped to a skill like "Knowledge of that lesson"

     * This deletion takes place when a lesson is changed from regular lesson to course_only

     *

     * <br/>Example:

     * <code>

     * $lesson -> deleteLessonSkill();

     * </code>

     *

     * @return the result of the table deletion

     * @since 3.5.2

     * @access public

     */
    public function deleteLessonSkill() {
        // Delete the corresponding lesson skill to the skill and lesson_offers_skill tables
        $lesson_skill = eF_getTableData("module_hcd_skills JOIN module_hcd_lesson_offers_skill ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID","*", "lesson_ID = ". $this -> lesson['id'] . " AND module_hcd_skills.categories_ID = -1");
        eF_deleteTableData("module_hcd_skills", "skill_ID = ". $lesson_skill[0]['skill_ID']);
        // Delete all question-to-lesson specific skill assignments
        $questions = eF_getTableDataFlat("questions", "id", "lessons_ID = ". $this ->lesson['id']);
        eF_deleteTableData("questions_to_skills", "questions_id IN ('".implode("','",$questions['id'])."') AND skills_ID = " . $lesson_skill[0]['skill_ID']);
        return eF_deleteTableData("module_hcd_lesson_offers_skill", "lesson_ID = " . $this -> lesson['id'] . " AND skill_ID = " . $lesson_skill[0]['skill_ID']);
    }
   /**

     * Get the skill corresponding to this lesson: Every lesson that is not course_only is

     * mapped to a skill like "Knowledge of that lesson"

     * <br/>Example:

     * <code>

     * $lesson_skill = $lesson -> getLessonSkill();

     * </code>

     *

     * @return An array of the form [skill_ID] => [lesson_ID, description, specification,skill_ID, categories_ID]

     * @since 3.5.2

     * @access public

     */
    public function getLessonSkill() {
        return false;
    }
   /**

     * Assign a skill to this lesson or update an existing skill description

     *

     * This function is used to correlate a skill to the lesson - if the

     * lesson is completed then this skill is assigned to the user that completed it

     *

     * <br/>Example:

     * <code>

     * $lesson -> assignSkill(2, "Beginner PHP knowledge");   // The lesson will offer skill with id 2 and "Beginner PHP knowledge"

     * </code>

     *

     * @param $skill_ID the id of the skill to be assigned

     * @return boolean true/false

     * @since 3.5.0

     * @access public

     */
    public function assignSkill($skill_ID, $specification) {
        $this -> getSkills();
        // Check if the skill is not assigned as offered by this lesson
        if ($this -> skills[$skill_ID]['lesson_ID'] == "") {
            if ($ok = eF_insertTableData("module_hcd_lesson_offers_skill", array("skill_ID" => $skill_ID, "lesson_ID" => $this -> lesson['id'], "specification" => $specification))) {
                $this -> skills[$skill_ID]['lesson_ID'] = $this -> lesson['id'];
                $this -> skills[$skill_ID]['specification'] = $specification;
            } else {
                throw new EfrontLessonException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontLessonException :: DATABASE_ERROR);
            }
        } else {
            if ($ok = eF_updateTableData("module_hcd_lesson_offers_skill", array("specification" => $specification), "skill_ID = '".$skill_ID."' AND lesson_ID = '". $this -> lesson['id'] ."'") ) {
                $this -> skills[$skill_ID]['specification'] = $specification;
            } else {
                throw new EfrontLessonException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontLessonException :: DATABASE_ERROR);
            }
        }
        return true;
    }
   /**

     * Remove a skill that is offered from this lesson

     *

     * This function is used to stop the correlation of a skill to the lesson - if the

     * lesson is completed then this skill is assigned to the user that completed it

     *

     * <br/>Example:

     * <code>

     * $lesson -> removeSkill(2);   // The lesson will stop offering skill with id 2

     * </code>

     *

     * @param $skill_ID the id of the skill to be removed from the skills to be offered list

     * @return boolean true/false

     * @since 3.5.0

     * @access public

     */
    public function removeSkill($skill_ID) {
        $this -> getSkills();
        // Check if the skill is not assigned as offered by this lesson
        if ($this -> skills[$skill_ID]['lesson_ID'] == $this -> lesson['id']) {
            if ($ok = eF_deleteTableData("module_hcd_lesson_offers_skill", "skill_ID = '".$skill_ID."' AND lesson_ID = '". $this -> lesson['id'] ."'") ) {
                $this -> skills[$skill_ID]['specification'] = "";
                $this -> skills[$skill_ID]['lesson_ID'] = "";
            } else {
                throw new EfrontLessonException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontLessonException :: DATABASE_ERROR);
            }
        }
        return true;
    }
   /**

     * Assign a branch to this lesson 

     *

     * This function is used to correlate a branch to the lesson

     * All users of the branch should be assigned to this lesson

     *

     * <br/>Example:

     * <code>

     * $lesson -> assignBranch(2);   // The lesson will be assigned to branch with id 2 

     * </code>

     *

     * @param $branch_ID the id of the branch to be assigned

     * @return boolean true/false

     * @since 3.6.0

     * @access public

     */
    public function assignBranch($branch_ID) {
        $this -> getBranches();
        // Check if the branch is not assigned as offered by this lesson
        if ($this -> branches[$branch_ID]['lessons_ID'] == "") {
            if ($ok = eF_insertTableData("module_hcd_lesson_to_branch", array("branches_ID" => $branch_ID, "lessons_ID" => $this -> lesson['id']))) {
                $this -> branches[$branch_ID]['lessons_ID'] = $this -> lesson['id'];
                $newBranch = new EfrontBranch($branch_ID);
                $employees = $newBranch ->getEmployees(false,true); //get data flat
                $this -> addUsers($employees['login'], $employees['user_type']);
            } else {
                throw new EfrontLessonException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontLessonException :: DATABASE_ERROR);
            }
        }
        return true;
    }
   /**

     * Remove association of a branch with this lesson

     *

     * This function is used to stop the correlation of a branch to the lesson

     *

     * <br/>Example:

     * <code>

     * $lesson -> removeBranch(2);   // The lesson will stop offering branch with id 2

     * </code>

     *

     * @param $branch_ID the id of the branch to be removed from the lesson

     * @return boolean true/false

     * @since 3.6.0

     * @access public

     */
    public function removeBranch($branch_ID) {
        $this -> getBranches();
        // Check if the branch is not assigned as offered by this lesson
        if ($this -> branches[$branch_ID]['lessons_ID'] == $this -> lesson['id']) {
            if ($ok = eF_deleteTableData("module_hcd_lesson_to_branch", "branches_ID = '".$branch_ID."' AND lessons_ID = '". $this -> lesson['id'] ."'") ) {
                $this -> branches[$branch_ID]['lessons_ID'] = "";
            } else {
                throw new EfrontLessonException(_EMPLOYEESRECORDCOULDNOTBEUPDATED, EfrontLessonException :: DATABASE_ERROR);
            }
        }
        return true;
    }
   /**

     * Get all events related with this lesson

     *

     * This function is used to acquire all events related for this lesson,

     * according to a topical timeline

     *

     * <br/>Example:

     * <code>

     * $lesson -> getEvents();   // Get all events of this lessons from the most recent to the oldest

     * </code>

     *

     * @param $topic_ID the id of the topic to which the return events for the timeline should belong

     * @param $returnObjects whether to return event objects or not

     * @param $avatarSize the normalization size for the avatar images

     * @param $limit maximum number of events to return 

     * @return boolean true/false

     * @since 3.6.0

     * @access public

     */
    public function getEvents($topic_ID = false, $returnObjects = false, $avatarSize, $limit = false) {
  if (!($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_LESSON_TIMELINES)) {
      return array();
  }
     if ($topic_ID) {
      // only current lesson users
      $users = $this -> getUsers();
      $users_logins = array_keys($users); // don't mix with course events - with courses_ID = $this->lesson['id']		
      $related_events = eF_getTableData("events", "*", "type = '".EfrontEvent::NEW_POST_FOR_LESSON_TIMELINE_TOPIC. "' AND entity_ID = '".$topic_ID."' AND lessons_ID = '". $this->lesson['id']."' AND users_LOGIN IN ('".implode("','", $users_logins)."') AND (type < 50 OR type >74)", "timestamp desc");
        } else {
      // only current lesson users
      $users = $this -> getUsers();
      $users_logins = array_keys($users);
//    		if ($limit) {
//    			$related_events = eF_getTableData("events", "*", "lessons_ID = '". $this->lesson['id']."' AND users_LOGIN IN ('".implode("','", $users_logins)."')", "timestamp desc LIMIT " . $limit);
//    			
//    		} else {
      $related_events = eF_getTableData("events", "*", "lessons_ID = '". $this->lesson['id']."' AND users_LOGIN IN ('".implode("','", $users_logins)."')  AND (type < 50 OR type >74)	", "timestamp desc");
//    		}
        }
     if (!isset($avatarSize) || $avatarSize <= 0) {
      $avatarSize = 25;
     }
     $prev_event = false;
     $count = 0;
     $filtered_related_events = array();
     foreach($related_events as $key => $event) {
   $user = $users[$event['users_LOGIN']];
   // Logical combination of events
   if ($prev_event) {
    // since we have decreasing chronological order we now that $event['timestamp'] < $prev_event['timestamp']
    if ($event['users_LOGIN'] == $prev_event['event']['users_LOGIN'] && $event['type'] == $prev_event['event']['type'] && $prev_event['event']['timestamp'] - $event['timestamp'] < EfrontEvent::SAME_USER_INTERVAL) {
     unset($filtered_related_events[$prev_event['key']]);
     $count--;
    }
   }
   $filtered_related_events[$key] = $event;
         try {
             $file = new EfrontFile($user['avatar']);
             $filtered_related_events[$key]['avatar'] = $user['avatar'];
             list($filtered_related_events[$key]['avatar_width'], $filtered_related_events[$key]['avatar_height']) = eF_getNormalizedDims($file['path'],$avatarSize, $avatarSize);
         } catch (EfrontfileException $e) {
             $filtered_related_events[$key]['avatar'] = G_SYSTEMAVATARSPATH."unknown_small.png";
             $filtered_related_events[$key]['avatar_width'] = $avatarSize;
             $filtered_related_events[$key]['avatar_height'] = $avatarSize;
         }
         $prev_event = array("key"=>$key, "event"=>$event);
   if ($limit && ++$count == $limit) {
    break;
   }
     }
     if ($returnObjects) {
            $eventObjects = array();
            foreach ($filtered_related_events as $event) {
                $eventObjects[] = new EfrontEvent($event);
            }
            return $eventObjects;
        } else {
            return $filtered_related_events;
        }
    }
}
?>
