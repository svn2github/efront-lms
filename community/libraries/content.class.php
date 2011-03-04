<?php


/**

 * File for content classes

 *

 * @package eFront

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * Efront content exceptions

 *

 * This class extends Exception to provide the exceptions related to content

 * @package eFront

 *

 * @since 3.5.0

 */
class EfrontContentException extends Exception
{
    /**

     * The id provided is not valid, for example it is not a number or it is 0

     * @since 3.5.0

     */
    const INVALID_ID = 501;
    /**

     * The unit requested does not exist

     * @since 3.5.0

     */
    const UNIT_NOT_EXISTS = 502;
    /**

     * The unit can not be inserted for some reason

     * @since 3.5.0

     */
    const CANNOT_INSERT_UNIT = 503;
    /**

     * The project requested does not exist

     * @since 3.5.0

     */
    const PROJECT_NOT_EXISTS = 504;
    /**

     * The user login provided is not valid or does not exist

     * @since 3.5.0

     */
    const INVALID_LOGIN = 505;
    /**

     * The score is not valid, for example it is not numeric

     * @since 3.5.0

     */
    const INVALID_SCORE = 506;
    /**

     * The data provided is not valid, for example quotes or other illegal characters

     * @since 3.5.0

     */
    const INVALID_DATA = 507;
    /**

     * An error originating in database actions

     * @since 3.5.0

     */
    const DATABASE_ERROR = 508;
    /**

     * Unsupported content type, for example SCORM 2004 in community edition

     * @since 3.6.0

     */
    const UNSUPPORTED_CONTENT = 509;
    /**

     * An unspecific error

     * @since 3.5.0

     */
    const GENERAL_ERROR = 599;
}
/**

 * This class represents a content unit in eFront

 *

 * @package eFront

 * @since 3.5.0

 */
class EfrontUnit extends ArrayObject
{
    /**

     * The maximum length for unit names. After that, the names appear truncated

     */
    const MAXIMUM_NAME_LENGTH = 40;
    /**

     * Class constructor

     *

     * This function is used to instantiate the unit object

     * Since the class inherits from ArrayObject, normally

     * an array should be provided for instantiation. However,

     * the choice of using a unit id has been added for greater

     * flexibility, but still you are advised to avoid doing so,

     * since it might lead to big and unnecessary database overhead

     * <br/>Example:

     * <code>

     * $content = eF_getTableData("content", "*");

     * $unit = new EfrontUnit($content[4]);             //The best way: instantiate unit using existing information

     * $unit = new EfrontUnit(7);                       //The bad way: Let class to retrieve information. Should be avoided unless we are dealing with a single unit

     * </code>

     *

     * @param mixed $array Either a unit information array, or a unit id

     * @since 3.5.0

     * @access public

     */
    function __construct($array) {
        if (!is_array($array)) {
            if (eF_checkParameter($array, 'id')) {
                $result = eF_getTableData("content", "*", "id=$array");
                if (sizeof($result) == 0) {
                    throw new EfrontContentException(_UNITDOESNOTEXIST.": $array", EfrontContentException :: UNIT_NOT_EXISTS);
                } else {
                    $array = $result[0];
                }
            } else {
                throw new EfrontContentException(_INVALIDID.": $array", EfrontContentException :: INVALID_ID);
            }
  }
  //pr($array['options']);exit;
        if (unserialize($array['options'])) {
            $array['options'] = unserialize($array['options']);
        } else {
            $array['options'] = false;
  }
        parent :: __construct($array);
    }
    /**

     * Covert unit to SCORM unit

     *

     * This function augments a unit so that it includes

     * all the scorm-related fields

     *

     * @param array $array The original unit

     * @return array The unit augmented with scorm fields

     * @since 3.6.0

     * @access public

     */
    public function convertToScorm($array) {
        $result = eF_getTableData("scorm_sequencing_content_to_organization as c, scorm_sequencing_organizations as o", "*", "c.content_ID=".$array['id']." AND c.organization_content_ID = o.content_ID");
        $array['package_ID'] = $result[0]['content_ID'];
        $array['objectives_global_to_system'] = $result[0]['objectives_global_to_system'];
        $array['shared_data_global_to_system'] = $result[0]['shared_data_global_to_system'];
        $result = eF_getTableData("scorm_sequencing_control_mode", "*", "content_ID=".$array['id']);
        if (!empty($result)) {
            $array = array_merge($array, $result[0]);
        }
        $result = eF_getTableData("scorm_sequencing_constrained_choice", "*", "content_ID=".$array['id']);
        if (!empty($result)) {
            $array = array_merge($array, $result[0]);
        }
        $result = eF_getTableData("scorm_sequencing_completion_threshold", "*", "content_ID=".$array['id']);
        if (!empty($result)) {
            $array = array_merge($array, $result[0]);
        }
        $result = eF_getTableData("scorm_sequencing_delivery_controls", "*", "content_ID=".$array['id']);
        if (!empty($result)) {
            $array = array_merge($array, $result[0]);
        }
        $result = eF_getTableData("scorm_sequencing_hide_lms_ui", "*", "content_ID=".$array['id']);
        if (!empty($result)) {
            $array['hide_lms_ui'] = unserialize($result[0]['options']);
        } else {
            $array['hide_lms_ui'] = false;
        }
        $limit_condition = eF_getTableData("scorm_sequencing_limit_conditions", "*", "content_ID = '".$array['id']."'");
        if (empty($limit_condition)) {
            $array['limit_condition_attempt_control'] = 'false';
        } else {
            $array['limit_condition_attempt_control'] = 'true';
            $array = array_merge($array, $limit_condition[0]);
        }
        $result = eF_getTableData("scorm_sequencing_rollup_considerations", "*", "content_ID = '".$array['id']."'");
        if (!empty($result)) {
            $array = array_merge($array, $result[0]);
        }
        $result = eF_getTableData("scorm_sequencing_rollup_controls", "*", "content_ID = '".$array['id']."'");
        if (!empty($result)) {
            $array = array_merge($array, $result[0]);
        }
        //SCORM 2004 Rollup rules
        $result = eF_getTableData("scorm_sequencing_rollup_rules", "*", "content_ID = ".$array['id']);
        $array['rollup_rules'] = $result;
        foreach ($result as $key => $value) {
            $result = eF_getTableData("scorm_sequencing_rollup_rule", "*", "scorm_sequencing_rollup_rules_ID = ".$value['id']);
            $array['rollup_rules'][$key]['rollup_rule'] = $result;
        }
        //SCORM 2004 Rules
        $result = eF_getTableData("scorm_sequencing_rules", "*", "content_ID = ".$array['id']);
        $array['rules'] = $result;
        foreach ($result as $key => $value) {
            $result = eF_getTableData("scorm_sequencing_rule", "*", "scorm_sequencing_rules_ID = ".$value['id']);
            $array['rules'][$key]['rule'] = $result;
        }
        return $array;
    }
    /**

     * Store changed values to the database

     *

     * This unit is used to stored any changed values to the database

     * <br/>Example:

     * <code>

     * $unit['name'] = 'new name';

     * $unit -> persist();

     * </code>

     *

     * @return boolean true if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function persist() {
        $fields = array('name' => $this['name'],
                        'data' => $this['data'],
                        'parent_content_ID' => $this['parent_content_ID'],
                        'lessons_ID' => $this['lessons_ID'],
                        'timestamp' => $this['timestamp'],
                        'ctg_type' => $this['ctg_type'],
                        'active' => $this['active'],
                        'previous_content_ID' => $this['previous_content_ID'],
                        'options' => !is_array($this['options']) && $this['options'] ? $this['options'] : serialize($this['options']),
                        'metadata' => $this['metadata']);
        //The special string efront#special#text is used in order to remove the (heavy) content from the nodes when traversing it. However,
        //there is a chance that the tree traversal persists values as well. So, using this special string, we know that we must not
        //alter the content
        if ($this['data'] == 'efront#special#text') {
            unset($fields['data']);
        }
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::CONTENT_MODIFICATION, "lessons_ID" => $this['lessons_ID'], "entity_ID" => $this['id'], "entity_name" => $this['name']));
        return eF_updateTableData("content", $fields, "id=".$this['id']);
    }
    /**

     * Set search keywords

     *

     * This function updates the search keywords related to this unit's name and content.

     * It should be executed when and only when there is a change in any of the above fields,

     * since it performs excessive database queries.

     * <br>Example:

     * <code>

     * $unit = new EfrontUnit(34);          //Instantiate unit with id 34

     * $unit['name'] = 'New unit name';     //Change unit name

     * $unit -> persist();                  //Store new data

     * $unit -> setSearchKeywords();        //Update keywords

     * </code>

     *

     * @return boolean true if everything is ok

     * @since 3.5.2

     * @access public

     */
    public function setSearchKeywords() {
        EfrontSearch :: removeText('content', $this['id'], 'data'); //Refresh the search keywords
        EfrontSearch :: insertText($this['data'], $this['id'], "content", "data");
        EfrontSearch :: removeText('content', $this['id'], 'title'); //Refresh the search keywords
        EfrontSearch :: insertText($this['name'], $this['id'], "content", "title");
    }
    /**

     * Delete unit

     *

     * This function is used to delete the current unit.

     * <br/>Example:

     * <code>

     * $unit -> delete();

     * </code>

     *

     * @return boolean true if everything is ok

     * @since 3.5.0

     * @access public

     */
    public function delete() {
     if ($this['ctg_type'] == 'tests' || $this['ctg_type'] == 'feedback') {
            $result = eF_getTableData("tests", "id, content_ID", "content_ID=".$this['id']);
            if (sizeof($result) > 0) {
                $test = new EfrontTest($result[0]);
                $test -> delete();
            }
        }
  $result = eF_getTableData("lesson_conditions", "*", "lessons_ID=".$this['lessons_ID']);
        foreach ($result as $value) {
         $conditionOptions = unserialize($value['options']);
         if (($value['type'] == 'specific_unit' || $condition['type'] == 'specific_test') && $conditionOptions[0] == $this['id']) {
          eF_deleteTableData("lesson_conditions", "id=".$value['id']);
         }
        }
        eF_deleteTableData("content", "id=".$this['id']); //Delete Unit from database
        eF_deleteTableData("scorm_data", "content_ID=".$this['id']); //Delete Unit from scorm_data
  eF_deleteTableData("comments", "content_ID=".$this['id']); //Delete comments of this unit
  eF_deleteTableData("rules", "content_ID=".$this['id']." OR rule_content_ID=".$this['id']); //Delete rules associated with this unit
  eF_updateTableData("questions", array("content_ID" => 0), "content_ID=".$this['id']); //Remove association of questions with this unit but not delete them
  EfrontSearch :: removeText('content', $this['id'], ''); //Delete keywords
  //Delete scorm data related to the unit
    }
    /**

     * Activate unit

     *

     * This function is used to activate the current unit.

     * If the unit is a test unit, the correspoding test is also activated

     * <br/>Example:

     * <code>

     * $unit = new EfrontUnit(43);              //Instantiate object for unit with id 43

     * $unit -> activate();                     //Activate unit

     * </code>

     *

     * @since 3.5.0

     * @access public

     */
    public function activate() {
        $this['active'] = 1;
        $this -> persist();
        if ($this['ctg_type'] == 'tests') {
            $result = eF_getTableData("tests", "id", "content_ID=".$this['id']);
            if (sizeof($result) > 0) {
                $test = new EfrontTest($result[0]['id']);
                if (!$test -> test['active']) {
                    $test -> activate();
                }
            }
        }
    }
    /**

     * Deactivate unit

     *

     * This function is used to deactivate the current unit.

     * If the unit is a test unit, the correspoding test is also deactivated

     * <br/>Example:

     * <code>

     * $unit = new EfrontUnit(43);              //Instantiate object for unit with id 43

     * $unit -> deactivate();                   //Deactivate unit

     * </code>

     *

     * @since 3.5.0

     * @access public

     */
    public function deactivate() {
        if ($this['ctg_type'] == 'tests') {
            $result = eF_getTableData("tests", "id", "content_ID=".$this['id']);
            if (sizeof($result) > 0) {
                $test = new EfrontTest($result[0]['id']);
                if ($test -> test['active']) {
                    $test -> deactivate();
                }
            }
        }
        $this['active'] = 0;
        $this -> persist();
    }
    /**

     * Get unit questions

     *

     * This function returns a list with all the questions

     * that belong to this unit. If $returnObjects is true, then

     * Question objects are returned.

     * <br/>Example:

     * <code>

     * $questions = $this -> getQuestions();            //Get a simple list of questions

     * $questions = $this -> getQuestions(true);        //Get a list of Question objects

     * </code>

     *

     * @param boolean $returnObjects Whether to return Question objects

     * @return array An array of questions

     * @since 3.5.0

     * @access public

     */
    public function getQuestions($returnObjects = false) {
        $questions = array();
        $result = eF_getTableData("questions", "*", "content_ID=".$this['id']);
        if (sizeof($result) > 0) {
            foreach ($result as $value) {
                $returnObjects ? $questions[$value['id']] = QuestionFactory :: factory($value) : $questions[$value['id']] = $value;
            }
        }
        return $questions;
    }
    /**

     * Query if the unit is a test

     *

     * This function returns true if the unit corresponds to a test,

     * otherwise it returns false

     * <br/>Example:

     * <code>

     * $unit = new EfrontUnit(7);

     * $flg = $unit->isTest();

     * </code>

     *

     *

     * @return boolean A flag to indicate if the unit is test

     * @since 3.5.0

     * @access public

     */
    public function isTest(){
        if (parent::offsetGet('ctg_type') == "tests"){
            return true;
        }
        else{
            return false;
        }
    }
    public function toXML(){
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' ."\n";
        $xml .= "\t" . '<unit>';
        $xml .= "\t\t<name>".parent::offsetGet('name')."</name>\n";
        $xml .= "\t\t<ctg_type>".parent::offsetGet('ctg_type')."</ctg_type>\n";
        $xml .= "\t</unit>";
        return $xml;
    }
    public function fromXML($xmlstr){
        $xml = new SimpleXMLElement($xmlstr);
        parent::offsetSet('name', (string)$xml->unit->name);
        parent::offsetSet('ctg_type', (string)$xml->unit->ctg_type);
    }
    /**

     * Get the id of prerequisite unit for this unit

     *

     * This function returns false if there is no prerequisite unit,

     * otherwise returns the id of the prerequisite unit

     * <br/>Example:

     * <code>

     * $unit = new EfrontUnit(7);

     * $pid = $unit->getPrerequisite();

     * </code>

     *

     *

     * @return mixed An integer id if there is a prerequisite, or false otherwise

     * @since 3.5.0

     * @access public

     */
    public function getPrerequisite(){
        if (parent::offsetGet('previous_content_ID') != "0"){
            return parent::offsetGet('previous_content_ID');
        }
        else
            return false;
    }
   /**

     * Get the lesson files

     *

     * This function returns an array of the file ids or paths which are used by this unit

     * <br/>Example:

     * <code>

     * $unit = new EfrontUnit(7);

     * $files = $unit -> getFiles();

     * </code>

     *

     * @return array An array with the file ids

     * @since 3.5.0

     * @access public

     */
    public function getFiles($returnObjects = false) {
        $data = parent :: offsetGet('data');
        preg_match_all("/view_file\.php\?file=(\d+)/", $data, $matchesId);
        $filesId = $matchesId[1];
        preg_match_all("#(".G_SERVERNAME.")*content/lessons/(.*)\"#U", $data, $matchesPath);
        $filesPath = $matchesPath[2];
        foreach ($filesId as $file) {
            $returnObjects ? $files[] = new EfrontFile($file) : $files[] = $file;
        }
        foreach ($filesPath as $file) {
            $returnObjects ? $files[] = new EfrontFile(G_LESSONSPATH.html_entity_decode($file)) : $files[] = G_LESSONSPATH.html_entity_decode($file);
        }
        return $files;
    }
    /**

     * Create a new unit

     *

     * This function is used to create a new unit.

     * <br/>Example:

     * <code>

     * $fields = array('name' => 'new unit', 'ctg_type' => 'theory');

     * $unit = EfrontUnit :: createUnit($fields);

     * </code>

     *

     * @param array $fields The new unit fields

     * @return EfrontUnit The newly created unit

     * @since 3.5.0

     * @access public

     */
    public static function createUnit($fields = array()) {
        if (!isset($fields['lessons_ID'])) {
            return false;
        }
        !isset($fields['name']) ? $fields['name'] = 'Default unit' : null;
        !isset($fields['timestamp']) ? $fields['timestamp'] = time() : null;
        !isset($fields['ctg_type']) ? $fields['ctg_type'] = 'theory' : null;
        if (!isset($fields['metadata'])) {
            $defaultMetadata = array('title' => $fields['name'],
                                     'creator' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                     'publisher' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                     'contributor' => $GLOBALS['currentUser'] -> user['name'].' '.$GLOBALS['currentUser'] -> user['surname'],
                                     'date' => date("Y/m/d", $fields['timestamp']),
                                     'type' => 'content');
            $fields['metadata'] = serialize($defaultMetadata);
        }
        $newId = eF_insertTableData("content", $fields);
        $result = eF_getTableData("content", "*", "id=".$newId); //We perform an extra step/query for retrieving data, sinve this way we make sure that the array fields will be in correct order (forst id, then name, etc)
        $unit = new EfrontUnit($result[0]);
        EfrontSearch :: insertText(htmlspecialchars($fields['name'], ENT_QUOTES), $unit['id'], "content", "title");
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::CONTENT_CREATION, "lessons_ID" => $fields['lessons_ID'], "entity_ID" => $unit['id'], "entity_name" => $fields['name']));
        return $unit;
    }
}
/**

 * This class represents the content tree and extends EfrontTree class

 * @package eFront

 * @since 3.5.0

 */
class EfrontContentTree extends EfrontTree
{
    /**

     * The lesson id

     *

     * @var int

     * @since 3.5.0

     * @access public

     */
    public $lessonId = 0;
    /**

     * Content rules. The array is initialized only after the call to getRules()

     *

     * @since 3.5.0

     * @var array

     * @access public

     * @see getRules()

     */
    protected $rules = false;
    /**

     * These values signify the SCORM 2004 version

     *

     * @since 3.6.0

     * @var array

     * @access public

     * @static

     */
    public static $scorm2004Versions = array('CAM 1.3' , '2004 3rd Edition', '2004 4th Edition');
    /**

     * Instantiate tree object

     *

     * The constructor instantiates the tree based on the lesson id

     * <br/>Example:

     * <code>

     * $tree = new EfrontContentTree(23);                   //23 is the lesson id

     * $lesson = new EfrontLesson(23);                      //23 is the lesson id

     * $tree = new EfrontContentTree($lesson);              //Content may be alternatively instantiated using the lesson object

     * </code>

     *

     * @param mixed $lesson Either The lesson id or an EfrontLesson object

     * @param array $data If true, then the tree nodes hold data as well

     * @since 3.5.0

     * @access public

     */
    function __construct($lesson, $data = false) {
        if ($lesson instanceof EfrontLesson) {
            $lessonId = $lesson -> lesson['id'];
        } elseif (!eF_checkParameter($lesson, 'id')) {
            throw new EfrontContentException(_INVALIDLESSONID.': '.$lesson, EfrontContentException :: INVALID_ID);
        } else {
            $lessonId = $lesson;
        }
        $this -> lessonId = $lessonId; //Set the lesson id
        $this -> data = $data; //Is used in reset()
        $this -> reset(); //Initialize content tree
        $firstUnit = $this -> getFirstNode();
        $this -> currentUnitId = $firstUnit['id'];
    }
    /**

     * Construct content tree structure

     *

     * Creates a tree-like representation of the content, using arrays as EfrontUnit,

     * a class that extends ArrayObject.

     * Each unit is represented as an array with the appropriate fields

     * (id, name, timestamp etc). If the unit has children units, then

     * these are subarrays of the current unit array. All keys correspond

     * to unit ids.

     * If, for some reason, there are units with invalid succession data,

     * (parent or previous content ids), these are appended at the end of

     * the content tree.

     * <br/>Example:

     * <code>

     * $content = new EfrontContentTree(4);                                 //Initialize content tree for lesson with id 4

     * //Do some nasty stuff with content tree

     * $content -> reset();                                                 //Reset content tree to its original state

     * </code>

     *

     * @since 3.5.0

     * @access public

     */
    public function reset() {
        if ($this -> data) {
            $result = eF_getTableData("content", "*, data != '' as has_data", "lessons_ID = '".$this -> lessonId."'");
        } else {
            $fields = eF_getTableFields("content");
            unset($fields[array_search('data', $fields)]);
            $result = eF_getTableData("content", implode(",", $fields).", data != '' as has_data", "lessons_ID = '".$this -> lessonId."'");
        }
        if (sizeof($result) == 0) {
            $this -> tree = new RecursiveArrayIterator(array());
            return;
        }
        $scorm2004Units = array();
        $units = array();
        foreach ($result as $unit) {
            $units[$unit['id']] = $unit;
        }
        if (!empty($scorm2004Units)) {
            $units = $this -> convertUnitsTo2004($units, $scorm2004Units);
        }
        //$units   = eF_getTableData("content", "id,name,parent_content_ID,lessons_ID,timestamp,ctg_type,active,previous_content_ID", "lessons_ID = '".$this -> lessonId."'");
        $rejected = array();
        foreach ($units as $node) { //Assign previous content ids as keys to the previousNodes array, which will be used for sorting afterwards
            if (!$this -> data) {
                $node['has_data'] ? $node['data'] = 'efront#special#text' : $node['data'] = ''; //Eliminate with 'efront#special#text' data for units that don't have any content, and set an empty space ' ' for units that have content. This is done so that the toHTML can handle differently the ones from the others. The efront#special#text is checked by persist() in order to leave data unchanged in case we are updating
            }
            $node = new EfrontUnit($node); //We convert arrays to array objects, which is best for manipulating data through iterators
            if (!isset($previousNodes[$node['previous_content_ID']])) {
                $previousNodes[$node['previous_content_ID']] = $node;
            } else {
                $rejected[$node['id']] = $node; //$rejected holds cut off units, which do not have a valid previous_content_ID
            }
        }
        $node = 0;
        $count = 0;
        $nodes = array(); //$count is used to prevent infinite loops
        while (sizeof($previousNodes) > 0 && isset($previousNodes[$node]) && $count++ < 1000) { //Order the nodes array according to previous_content_ID information. if $previousNodes[$node] is not set, it means that there are illegal previous content id entries in the array (for example, a unit reports as previous a non-existent unit). In this case, all the remaining units in the $previousNodes array are rejected
            $nodes[$previousNodes[$node]['id']] = $previousNodes[$node]; //Assign the previous node to be the array key
            $newNode = $previousNodes[$node]['id'];
            unset($previousNodes[$node]);
            $node = $newNode;
        }
        if (sizeof($previousNodes) > 0) { //If $previousNodes is not empty, it means there are invalid (orphan) units in the array, so append them to the $rejected list
            foreach ($previousNodes as $value) {
                $rejected[$value['id']] = $value;
            }
        }
        $tree = $nodes;
        $count = 0; //$count is used to prevent infinite loops
        while (sizeof($tree) > 1 && $count++ < 50000) { //We will merge all branches under the main tree branch, the 0 node, so its size will become 1
            foreach ($nodes as $key => $value) {
                if ($value['parent_content_ID'] == 0 || in_array($value['parent_content_ID'], array_keys($nodes))) { //If the unit parent is in the $nodes array keys - which are the unit ids- or it is 0, then it is  valid
                    $parentNodes[$value['parent_content_ID']][] = $value; //Find which nodes have children and assign them to $parentNodes
                    $tree[$value['parent_content_ID']][$value['id']] = array(); //We create the "slots" where the node's children will be inserted. This way, the ordering will not be lost
                } else {
                    $rejected = $rejected + array($value['id'] => $value); //Append units with invalid parents to $rejected list
                    unset($nodes[$key]); //Remove the invalid unit from the units array, as well as from the parentUnits, in case a n entry for it was created earlier
                    unset($parentNodes[$value['parent_content_ID']]);
                }
            }
            if (isset($parentNodes)) { //If the unit was rejected, there won't be a $parentNodes array
                $leafNodes = array_diff(array_keys($nodes), array_keys($parentNodes)); //Now, it's easy to see which nodes are leaf nodes, just by subtracting $parentNodes from the whole set
                foreach ($leafNodes as $leaf) {
                    $parent_id = $nodes[$leaf]['parent_content_ID']; //Get the leaf's parent
                    $tree[$parent_id][$leaf] = $tree[$leaf]; //Append the leaf to its parent's tree branch
                    unset($tree[$leaf]); //Remove the leaf from the main tree branch
                    unset($nodes[$leaf]); //Remove the leaf from the nodes set
                }
                unset($parentNodes); //Reset $parentNodes; new ones will be calculated at the next loop
            }
        }
        if (sizeof($tree) > 0 && !isset($tree[0])) { //This is a special case, where only one node exists in the tree
            $tree = array($tree);
        }
        isset($tree[0]) ? $tree = $tree[0] : $tree = array();
        if (sizeof($rejected) > 0) { //Append rejected nodes to the end of the tree array, updating their parent/previous information
            foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($tree), RecursiveIteratorIterator :: SELF_FIRST)) as $lastUnit); //Advance to the last tree node
            isset($lastUnit) ? $previousId = $lastUnit['id'] : $previousId = 0; //There is a chance that no normal units exist in the tree. In this case, there will be no $lastUnit
            foreach ($rejected as $id => $node) { //Update broken nodes
                $node['parent_content_ID'] = 0;
                $node['previous_content_ID'] = $previousId;
                $node -> persist(); //Persist changes to the database
                $tree[$id] = $node;
                $previousId = $id;
            }
        }
        $this -> tree = new RecursiveArrayIterator($tree);
        //Create arrays for assigning the immediate children and the parents of each unit. These will come especially handy for SCORM 2004 calculations
        $this -> immediateDescendants = array();
        $this -> nodeParents = array();
        foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($tree), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
            foreach(array_keys((array)$value) as $member) {
                !is_numeric($member) OR $this -> immediateDescendants[$key][] = $member;
            }
            if (isset($this -> nodeParents[$value['parent_content_ID']]) && $this -> nodeParents[$value['parent_content_ID']]) {
                $this -> nodeParents[$key] = array_merge(array($value['parent_content_ID']), $this -> nodeParents[$value['parent_content_ID']]);
            } else {
                $this -> nodeParents[$key] = array($value['parent_content_ID']);
            }
        }
        //pr($this -> nodeParents);
        //pr($this -> immediateDescendants);
    }
    public function convertUnitsTo2004($units, $scorm2004Units) {
        $scormContentIds = implode(",", $scorm2004Units);
  $result = eF_getTableData("scorm_sequencing_content_to_organization as c, scorm_sequencing_organizations as o", "c.content_ID, c.organization_content_ID, o.objectives_global_to_system, o.shared_data_global_to_system", "c.content_ID in ($scormContentIds) AND c.organization_content_ID = o.content_ID");
  foreach ($result as $value) {
   $units[$value['content_ID']]['package_ID'] = $value['organization_content_ID'];
            $array['objectives_global_to_system'] = $value['objectives_global_to_system'];
            $array['shared_data_global_to_system'] = $value['shared_data_global_to_system'];
        }
        $result = eF_getTableData("scorm_sequencing_control_mode", "*", "content_ID in ($scormContentIds)");
        foreach ($result as $value) {
            $units[$value['content_ID']] = array_merge($units[$value['content_ID']], $value);
        }
        $result = eF_getTableData("scorm_sequencing_constrained_choice", "*", "content_ID in ($scormContentIds)");
        foreach ($result as $value) {
            $units[$value['content_ID']] = array_merge($units[$value['content_ID']], $value);
        }
        $result = eF_getTableData("scorm_sequencing_completion_threshold", "*", "content_ID in ($scormContentIds)");
        foreach ($result as $value) {
            $units[$value['content_ID']] = array_merge($units[$value['content_ID']], $value);
        }
        $result = eF_getTableData("scorm_sequencing_delivery_controls", "*", "content_ID in ($scormContentIds)");
        foreach ($result as $value) {
            $units[$value['content_ID']] = array_merge($units[$value['content_ID']], $value);
        }
        $result = eF_getTableData("scorm_sequencing_hide_lms_ui", "*", "content_ID in ($scormContentIds)");
        foreach ($result as $value) {
            $units[$value['content_ID']]['hide_lms_ui'] = unserialize($value['options']);
        }
        $result = eF_getTableData("scorm_sequencing_limit_conditions", "*", "content_ID in ($scormContentIds)");
        foreach ($scorm2004Units as $value) { //First, assign to every SCORM unit the 'false' for the limit_condition_attempt_control...
            $units[$value]['limit_condition_attempt_control'] = 'false';
        }
        foreach ($result as $value) { //...and then set it to true where applicable
            $units[$value['content_ID']]['limit_condition_attempt_control'] = 'true';
            $units[$value['content_ID']] = array_merge($units[$value['content_ID']], $value);
        }
        $result = eF_getTableData("scorm_sequencing_rollup_considerations", "*", "content_ID in ($scormContentIds)");
        foreach ($result as $value) {
            $units[$value['content_ID']] = array_merge($units[$value['content_ID']], $value);
        }
        $result = eF_getTableData("scorm_sequencing_rollup_controls", "*", "content_ID in ($scormContentIds)");
        foreach ($result as $value) {
            $units[$value['content_ID']] = array_merge($units[$value['content_ID']], $value);
        }
        //SCORM 2004 Rollup rules
        //First get all rollup rules to an array and assign them to each rollup rull group, based on scorm_sequencing_rollup_rules_ID
        $allRollupRules = array();
        $result = eF_getTableData("scorm_sequencing_rollup_rule", "*");
  foreach ($result as $value) {
            $allRollupRules[$value['scorm_sequencing_rollup_rules_ID']][] = $value;
        }
        //Now, asssign the rollup rule and the group to the unit
        $result = eF_getTableData("scorm_sequencing_rollup_rules", "*", "content_ID in ($scormContentIds)");
  foreach ($result as $value) {
   $value['rollup_rule'] = $allRollupRules[$value['id']];
            $units[$value['content_ID']]['rollup_rules'][] = $value;
   //          $units[$value['content_ID']]['rollup_rules']['rollup_rule'] = $allRollupRules[$value['id']];
//			$units[$value['content_ID']] = array_merge($units[$value['content_ID']], $allRollupRules[$value['id']]);
        }
        //First get all sequencing rules to an array and assign them to each sequencing rull group, based on scorm_sequencing_rules_ID
        $allRules = array();
        $result = eF_getTableData("scorm_sequencing_rule", "*");
  foreach ($result as $value) {
   $allRules[$value['scorm_sequencing_rules_ID']][] = $value;
            //$allRollupRules[$value['scorm_sequencing_rollup_rules_ID']][] = array('rollup_rule' => $value);
        }
        //Now, asssign the sequencing rule and the group to the unit
        $result = eF_getTableData("scorm_sequencing_rules", "*", "content_ID in ($scormContentIds)");
  foreach ($result as $key => $value) {
   $value['rule'] = $allRules[$value['id']];
            $units[$value['content_ID']]['rules'][] = $value;
  //        $units[$value['content_ID']]['rules']['rule'] = $allRules[$value['id']];
         //   $value['rule'] = $allRules[$value['id']]['rule'];
         //   $units[$value['content_ID']]['rules'][$key] = $value;
//pr($value);
//pr($allRules[$value['id']]);
            //$units[$value['content_ID']] = array_merge($units[$value['content_ID']], $allRules[$value['id']]);
//pr($units[$value['content_ID']]);
  }
     //  pr($value);
/*

        $result = eF_getTableData("scorm_sequencing_rollup_rules", "*", "content_ID in ($scormContentIds)");

        $array['rollup_rules'] = $result;

        foreach ($result as $key => $value) {

            $result = eF_getTableData("scorm_sequencing_rollup_rule", "*", "scorm_sequencing_rollup_rules_ID = ".$value['id']);

            $array['rollup_rules'][$key]['rollup_rule'] = $result;

        }



        //SCORM 2004 Rules

        $result = eF_getTableData("scorm_sequencing_rules", "*", "content_ID in ($scormContentIds)");

        $array['rules'] = $result;

        foreach ($result as $key => $value) {

            $result = eF_getTableData("scorm_sequencing_rule", "*", "scorm_sequencing_rules_ID = ".$value['id']);

            $array['rules'][$key]['rule'] = $result;

        }

*/
        return $units;
    }
    /**

     * Remove unit

     *

     * This function is used to remove a unit from the content tree

     * <br/>Example:

     * <code>

     * $content = new EfrontContentTree(4);                                 //Initialize content tree for lesson with id 4

     * $content -> removeNode(57);                                          //Remove the unit 57 and all of its subunits

     * </code>

     *

     * @param int $removeId The unit id that will be removed

     * @since 3.5.0

     * @access public

     */
    public function removeNode($removeId) {
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator($this -> tree, RecursiveIteratorIterator :: SELF_FIRST)); //Get an iterator for the current tree. This iterator returns only whole unit arrays and not unit members separately (such as id, timestamp etc)
        $iterator -> rewind(); //Initialize iterator
        while ($iterator -> valid() && $iterator -> key() != $removeId) { //Forward iterator index until you reach the designated element, which has an index equal to the unit id that will be removed
            $iterator -> next();
        }
        if ($iterator -> valid()) {
            $iterator -> current() -> delete(); //Delete the current unit from the database
            $previousUnit = $this -> getPreviousNode($iterator -> key()); //Get the deleted unit's previous unit
            $iterator -> offsetUnset($removeId); //Delete the unit from the content tree
            if ($previousUnit) { //If we are deleting the first unit, there is no previous unit
                $nextUnit = $this -> getNextNode($previousUnit['id']); //Get the previous unit's next unit, which still points to the old unit
                if ($nextUnit) { //If we are deleting the last unit, there is not next unit
                    $nextUnit['previous_content_ID'] = $previousUnit['id']; //Update the next unit to point at the deleted unit' previous unit
                    $nextUnit -> persist(); //Persist these changes to the database
                }
            } else {
                $firstUnit = $this -> getFirstNode(); //If we deleted the first unit, then we need to set the new first unit to have 0 as previous unit
                if ($firstUnit) { //...Unless the deleted unit was the last content unit
                    $firstUnit['previous_content_ID'] = 0;
                    $firstUnit -> persist(); //Persist these changes to the database
                }
            }
        } else {
            throw new EfrontContentException(_UNITDOESNOTEXIST.': '.$removeId, EfrontContentException :: UNIT_NOT_EXISTS);
        }
    }
    /**

     * Insert unit to tree

     *

     * This function is used to insert a new unit to the content tree

     * <br/>Example:

     * <code>

     * $unit = array("id"                  => 99,                           //Create the array of the new unit

     *               "name"                => "Test Insert Unit",

     *               "lessons_ID"          => 1,

     *               "timestamp"           => time(),

     *               "ctg_type"            => "theory",

     *               "active"              => 1,

     *               "parent_content_ID"   => 5,

     *               "previous_content_ID" => 5);

     *

     * $content = new EfrontContentTree(4);                                 //Initialize content tree for lesson with id 4

     * $content -> insertNode($unit);                                       //Insert the new unit

     * </code>

     *

     * @param array $unit The unit array

     * @param int $parentUnit The parent of the specified node, if it is not set inside the node (not used for the moment)

     * @param int $previousUnit The previous of the specified node, if it is not set inside the node (not used for the moment)

     * @return EfrontUnit The new unit

     * @since 3.5.0

     * @access public

     * @todo implement $parentUnit/$preciousUnit functionality, when not present inside $unit

     */
    public function insertNode($unit, $parentUnit = false, $previousUnit = false) {
        if (!isset($unit['id'])) {
            if (!isset($unit['previous_content_ID'])) {
                $unit['parent_content_ID'] ? $children = $this -> getNodeChildren($unit['parent_content_ID']) : $children = $this -> tree; //Get the new unit's parent children. If the parent unit is 0, then we will append it to the end of the tree
                foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator($children, RecursiveIteratorIterator :: SELF_FIRST), 'id') as $lastUnitId) {} //Iterate through the parent's children until you reach the last one
                $lastUnitId ? $unit['previous_content_ID'] = $lastUnitId : $unit['previous_content_ID'] = 0; //The new unit's parent last unit
            }
            $unit = EfrontUnit :: createUnit($unit);
        }
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator($this -> tree, RecursiveIteratorIterator :: SELF_FIRST)); //Get an iterator for the current tree. This iterator returns only whole unit arrays and not unit members separately (such as id, timestamp etc)
        $iterator -> rewind(); //Initialize iterator
        while ($iterator -> valid() && $iterator -> key() != $unit['previous_content_ID']) { //Forward iterator index until you reach the unit's previous unit
            $iterator -> next();
        }
        $iterator -> next(); //Advance iterator once more to get the next unit
        if ($iterator -> valid()) { //If there is a next unit
            $lastUnit = $unit; //Set the current last unit to be the inserted unit
            foreach (new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($unit), RecursiveIteratorIterator :: SELF_FIRST)) as $lastUnit); //If the inserted unit is actually a tree branch with children, advance to the last one
            $iterator -> current() -> offsetSet('previous_content_ID', $lastUnit['id']); //The current unit will have the $lastUnit as previous
            $iterator -> current() -> persist(); //Store value to the database
        }
        $this -> reset(); //Rebuild content tree, so that the unit may appear to the right place
        return $this -> seekNode($unit['id']);
    }
    /**

     * Append unit to the end of the content tree

     *

     * This function is the same as insertNode(), only that it

     * resets parent and previous unit information so that the

     * unit is appended to the end of the content tree.

     *

     * @param array $unit The unit array

     * @since 3.5.0

     * @access public

     */
    public function appendUnit($unit) {
        $lastUnit = $this -> getLastNode();
        $unit['parent_content_ID'] = 0;
        $unit['previous_content_ID'] = $lastUnit['id'];
        $newUnit = $this -> insertNode($unit);
        return $newUnit;
    }
    /**

     * Get the current unit of the tree

     *

     * This function returns the current unit, in a flat array

     * <br/>Example:

     * <code>

     * $content = new EfrontContentTree(4);             //Create the content tree for lesson with id 4

     * $unit = $content -> getCurrentNode();                //$unit now holds the first unit of the tree

     * </code>

     *

     * @param int $queryUnit A unit id, to get its array

     * @return array The current unit

     * @since 3.5.0

     * @access public

     * @todo Correct it!

     */
    public function getCurrentNode($queryUnit = false){
        $queryUnit === false ? $unitId = $this -> currentUnitId : $unitId = $queryUnit;
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST)); //Create iterators for the tree
        $iterator -> rewind(); //Initialize iterator
        while ($iterator -> valid() && $iterator -> key() != $unitId) { //Advance iterator until we reach the designated unit
            $iterator -> next();
        }
        if ($iterator -> valid()) {
            $flatTree[] = $this -> filterOutChildren($iterator -> current());
        }
        return $flatTree;
    }
    /**

     * Get the next units in the tree

     *

     * This function returns the next units, in a flat array.

     * <br/>Example:

     * <code>

     * $content = new EfrontContentTree(4);             //Create the content tree for lesson with id 4

     * $units = $content -> getNextNodes();             //$units now holds all the next units of the current unit

     * $units = $content -> getNextNodes(32);           //$units now holds all the next units of unit 32

     * </code>

     *

     * @param int $queryUnit A unit id, to get its next units

     * @return array The next units array

     * @since 3.5.0

     * @access public

     * @todo Correct it!

     */
    public function getNextNodes($queryUnit = false) {
        $queryUnit === false ? $unitId = $this -> currentUnitId : $unitId = $queryUnit; //If queryUnit is not specified, use the current unit
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST)); //Create iterators for the tree
        $iterator -> rewind(); //Initialize iterator
        while ($iterator -> valid() && $iterator -> key() != $unitId) { //Advance iterator until we reach the designated unit
            $iterator -> next();
        }
        if ($iterator -> valid()) { //If we found the designated unit, avance the iterator once more to get its next unit
            $iterator -> next();
        } else {
            throw new EfrontContentException(_UNITDOESNOTEXIST.': '.$unitId, EfrontContentException :: UNIT_NOT_EXISTS);
        }
        if ($iterator -> valid()) { //If there is a next unit, get it
            while ($iterator -> valid()) { //Advance iterator until we reach the designated unit
                $flatTree[] = $this -> filterOutChildren($iterator -> current());
                $iterator -> next();
            }
            return $flatTree;
        } else { //The unit was apparently the last unit; return an empty array
            return array();
        }
    }
    /**

     * Get the previous units

     *

     * This function returns the previous units, in a flat array.

     * <br/>Example:

     * <code>

     * $units = $content -> getPreviousNodes();             //$units now holds all the previous units of the current unit

     * $units = $content -> getPreviousNodes(32);           //$units now holds all the previous units of unit 32

     * </code>

     *

     * @param int $queryUnit A unit id, to get its previous units

     * @return array The previous units array

     * @since 3.5.0

     * @access public

     * @todo Correct it!

     */
    public function getPreviousNodes($queryUnit = false) {
        $queryUnit === false ? $unitId = $this -> currentUnitId : $unitId = $queryUnit; //If queryUnit is not specified, use the current unit
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST)); //Create iterators for the tree
        $iterator -> rewind(); //Initialize iterator
        while ($iterator -> valid() && $iterator -> key() != $unitId) { //Advance iterator until we reach the designated unit
            $flatTree[] = $this -> filterOutChildren($iterator -> current()); //Assign units in each loop to the flat array (without children)
            $iterator -> next();
        }
        if ($iterator -> valid()) { //We reached the designated unit, so return
            return $flatTree;
        } else {
            if (!isset($flatTree)) { //If iterator value is not valid, and $flatTree is not set, this means that the designated unit was the first, so return empty array
                return array();
            } else { //If $flatTree is set and iterator is invalid, the unit speify did not exist
                throw new EfrontContentException(_UNITDOESNOTEXIST.': '.$unitId, EfrontContentException :: UNIT_NOT_EXISTS);
            }
        }
    }
    /**

     * Get content rules

     *

     * This function retrieves the content rules

     * <br/>Example:

     * <code>

     * $content -> getRules();              //Returns an array with lesson rules

     * $content -> getRules(43);            //Returns an array with lesson rules that refer to unit with id 43

     * </code>

     *

     * @param int $queryUnit If set, return rules for this unit only

     * @return array The lesson rules

     * @since 3.5.0

     * @access public

     */
    public function getRules($queryUnit = false) {
        if ($this -> rules === false) {
            $contentIds = array();
            foreach (new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree)), 'id') as $key => $id) {
                $contentIds[] = $id;
            }
            $rules = array();
            if (sizeof($contentIds) > 0) {
                $rules = eF_getTableData("rules", "*", "content_ID in (".implode(",", $contentIds).") or lessons_ID=".$this -> lessonId);
            }
            if (sizeof($rules) > 0) {
                foreach ($rules as $value) {
                    $this -> rules[$value['id']] = $value;
                }
            } else {
                $this -> rules = array();
            }
        }
        if ($queryUnit) {
            $unitRules = array();
            foreach ($this -> rules as $key => $rule) {
                if ($rule['content_ID'] == $queryUnit || $rule['content_ID'] == 0) {
                    $unitRules[$key] = $rule;
                }
            }
            return $unitRules;
        } else {
            return $this -> rules;
        }
    }
    /**

     * Delete rules

     *

     * This function can be used to delete one or more rules

     * <br/>Example:

     * <code>

     * $content -> deleteRules(54);                                 //Delete rule with id 54

     * $content -> deleteRules(array(54,34,76));                    //Delete rules with specified ids

     * </code>

     *

     * @param mixed $rules one or more rule ids to delete

     * @return The content rules left

     * @since 3.5.0

     * @access public

     */
    public function deleteRules($rules) {
        if ($this -> rules === false) { //Initialize rules, if you haven't done so
            $this -> getRules();
        }
        if (!is_array($rules)) { //Convert single rule to array
            $rules = array($rules);
        }
        foreach ($rules as $ruleId) {
            if (eF_checkParameter($ruleId, 'id') && in_array($ruleId, array_keys($this -> rules))) {
                eF_deleteTableData("rules", "id=$ruleId");
                unset($this -> rules[$ruleId]);
            }
        }
        return $this -> rules;
    }
    /**

     * Check if the user can access the specified unit

     *

     * @param int $queryUnit The unit to check for

     * @param unknown_type $seenUnits

     * @return unknown

     * @since 3.5.0

     * @access public

     */
    public function checkRules($queryUnit, $seenContent) {
        $rules = $this -> getRules($queryUnit);
        $allow = true;
        foreach ($rules as $id => $rule) {
            switch ($rule['rule_type']) {
                case 'always':
                 if ($rule['users_LOGIN'] == $_SESSION['s_login']) {
                     return _YOUHAVEBEENEXCLUDEDBYPROFESSOR;
                 }
                 break;
                case 'hasnot_seen':
                    if (!in_array($rule['rule_content_ID'], array_keys($seenContent))) {
                        try {
                            $ruleUnit = $this -> seekNode($rule['rule_content_ID']);
                            $scorm2004 = in_array($current['scorm_version'], EfrontContentTree::$scorm2004Versions);
                            if ($ruleUnit['active'] && ($ruleUnit['data'] || $current['ctg_type'] == 'tests' || $scorm2004)) {
                                return _MUSTFIRSTREADUNIT.' <a href = "student.php?ctg=content&view_unit='.$ruleUnit['id'].'">'.$ruleUnit['name'].'</a><br/>';
                            }
                        } catch (EfrontContentException $e) {}
                    }
                    break;
                case 'hasnot_passed':
                    if (!in_array($rule['rule_content_ID'], array_keys($seenContent)) || $seenContent[$rule['rule_content_ID']] / 100 < $rule['rule_option']) {
                        try {
                            $ruleUnit = $this -> seekNode($rule['rule_content_ID']);
                            if ($ruleUnit['active']) {
                                return _MUSTFIRSTTAKEATLEAST.' '.($rule['rule_option'] * 100).' % '._ATTEST.' <a href="student.php?ctg=tests&view_unit='.$ruleUnit['id'].'">'.$ruleUnit['name'].'</a><br/>';
                            }
                        } catch (EfrontContentException $e) {}
                    }
                    break;
                case 'serial':
                    //Get the visitable units
                    foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST))) as $key => $value) {
                        $visitableContentIds[$key] = $key; //Get the not-test unit ids for this content
                    }
                    //Locate the first active previous unit
                    $previousUnit = $this -> getPreviousNode($queryUnit);
                    while ($previousUnit && !in_array($previousUnit['id'], $visitableContentIds)) {
                        $previousUnit = $this -> getPreviousNode($previousUnit['id']);
                    }
                    //A previous active unit exists. Is it complete?
                    if ($previousUnit && !in_array($previousUnit['id'], array_keys($seenContent))) {
                        return _MUSTSEECONTENTSERIALSOMUSTFIRSTREADUNIT.' <a href = "student.php?ctg=content&view_unit='.$previousUnit['id'].'">'.$previousUnit['name'].'</a><br/>';
                    }
                    break;
                case 'tree':
                    break;
                default: break;
            }
        }
        return true;
    }
    /**

     * Get content comments

     *

     * This function is used to retrive all the comments that have been posted

     * to the current content.

     * <br/>Example:

     * <code>

     * $content = new EfrontContentTree(34);        //Initialize content for lesson with id 34

     * $comments = $content -> getComments();       //Return an array of arrays, each of which holds the comment data

     * </code>

     *

     * @param int $queryUnit If this parameter is specified, then only comments regarding the specified unit are returned

     * @return array The content comments

     * @since 3.5.2

     * @access public

     */
    public function getComments($queryUnit = false) {
        if ($queryUnit && eF_checkParameter($queryUnit, 'id')) {
            $result = eF_getTableData("comments", "*", "content_ID = ".$queryUnit);
        } else {
            //Get all ids
            $contentIds = array();
            foreach (new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree)), 'id') as $key => $id) {
                $contentIds[] = $id;
            }
            if (sizeof($contentIds) > 0) {
                $result = eF_getTableData("comments", "*", "content_ID in (".implode(",", $contentIds).")");
            } else {
                $result = array();
            }
        }
        $comments = array();
        foreach ($result as $value) {
            $comments[$value['id']] = $value;
        }
        return $comments;
    }
    /**

     * Delete comments from content

     *

     * This function is used to delete the specified comments from the

     * content.

     * <br/>Example:

     * <code>

     * $content = new EfrontContentTree(34);        //Initialize content for lesson with id 34

     * $content -> deleteComments(4);   //Delete comment with ids 4

     * $content -> deleteComments(array(4,6,2));    //Delete comments with ids 4,6,2

     * $content -> deleteComments(array_keys($content -> getComments()));   //Delete all comments

     * </code>

     *

     * @param mixed $comments A comment id or an array of comment ids

     * @since 3.5.2

     * @access public

     */
    public function deleteComments($comments) {
        if (!is_array($comments)) {
            $comments = array($comments);
        }
        $contentComments = array_keys($this -> getComments());
        $ids = array();
        foreach ($comments as $key => $value) {
            if (in_array($value, $contentComments)) {
                $ids[] = $value;
            }
        }
        if (sizeof($ids) > 0) {
            eF_deleteTableData("comments", "id in (".implode(",", $ids).")");
        }
    }
    /**

     * Repair tree

     *

     * This function is the "last resort": It rearranges all the

     * lesson's units so that they are visible to the system. It

     * revokes any succession information and creates a flat

     * tree, where units are arbitrarily sorted.

     * <br/>Example:

     * <code>

     * $content = new EfrontContentTree(4);     //Create the content tree for lesson with id 4

     * $content -> repairTree();                //Repair tree. Now, the content tree will be flat, containing all the lesson units

     * </code>

     *

     * @since 3.5.0

     * @access public

     * @static

     */
    public function repairTree() {
        $units = eF_getTableData("content", "*", "lessons_ID=".$this -> lessonId); //Get all lesson units
        $previous = 0;
        foreach ($units as $key => $value) {
            eF_updateTableData("content", array("previous_content_ID" => $previous, "parent_content_ID" => 0), "id=".$value['id']); //Update succession information and erase parent information
            $previous = $value['id'];
        }
    }
    /**

     * Mark seen nodes

     *

     * This function gets the units that the user has seen

     * and sets the 'seen' property either to 1 or to 0.

     * <br/>Example:

     * <code>

     * $currentContent = new EfrontContentTree(5);           //Initialize content for lesson with id 5

     * $currentContent -> markSeenNodes($currentUser);       //Mark the seen content for user in object $currentUser

     * </cod>

     *

     * @param mixed $user Either a user login or an EfrontUser object

     * @since 3.5.0

     * @access public

     */
    public function markSeenNodes($user) {
        $user instanceof EfrontUser ? $login = $user -> user['login'] : $login = $user;
        $seenContent = EfrontStats :: getStudentsSeenContent($this -> lessonId, $login);
        $seenNodes = array_keys($seenContent[$this -> lessonId][$login]);
        $resultScorm = eF_getTabledataFlat("scorm_data", "content_ID, lesson_status", "users_LOGIN='$login'");
        $resultScorm = array_combine($resultScorm['content_ID'], $resultScorm['lesson_status']);
        $result = eF_getTableData("content c, completed_tests ct, tests t", "t.content_ID, ct.status, ct.timestamp", "ct.status != 'deleted' and ct.archive = 0 and c.id = t.content_ID and c.lessons_ID = ".$this -> lessonId." and ct.tests_ID = t.id and ct.users_LOGIN='$login'");
        foreach ($result as $value) {
            $resultTests[$value['content_ID']] = $value['status'];
            $resultTestsTimes[$value['content_ID']] = $value['timestamp'];
        }
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));
        foreach ($iterator as $key => $value) {
            in_array($key, $seenNodes) || $resultScorm[$key] == 'completed' || $resultScorm[$key] == 'passed' ? $value['seen'] = 1 : $value['seen'] = 0;
            if ($resultScorm[$key]) {
             $resultScorm[$key] == 'incomplete' ? $value['incomplete'] = 1 : $value['incomplete'] = 0;
             $resultScorm[$key] == 'failed' ? $value['failed'] = 1 : $value['failed'] = 0;
            } else if ($resultTests[$key]) {
             $resultTests[$key] == 'incomplete' ? $value['incomplete'] = $resultTestsTimes[$key] : $value['incomplete'] = 0;
             $resultTests[$key] == 'failed' ? $value['failed'] = 1 : $value['failed'] = 0;
            }
        }
    }
    /**

     * Copy test

     *

     * This function copies a test into the current content tree

     * <br/>Example:

     * <code>

     * $currentContent = new EfrontContentTree(5);           //Initialize content for lesson with id 5

     * $currentContent -> copyTest(3, false);   //Copy the corresponding test into the content tree (at its end)

     * </code>

     *

     * @param int $testId The id of the test to be copied

     * @param mixed $targetUnit The id of the parent unit (or the parent EfrontUnit)in which the new unit will be copied, or false (the unit will be appended at the end)

     * @param boolean $copyQuestions Whether to copy questions as well. Copied questions will be attached to the test itself as parent unit

     * @return EfrontUnit The newly created test unit object

     * @since 3.5.0

     * @access public

     */
    public function copyTest($testId, $targetUnit = false, $copyQuestions = true) {
        $oldTest = new EfrontTest($testId);
        $oldUnit = $oldTest -> getUnit();
        $oldUnit['data'] = $oldTest -> test['description']; //Hack in order to successfully copy files. It will be removed when we implement the new copy/export framework
        $newUnit = $this -> copySimpleUnit($oldUnit, $targetUnit);
        $oldTest -> test['description'] = $newUnit['data']; //As above
        $newTest = EfrontTest::createTest($newUnit, $oldTest -> test);
        $newUnit['data'] = ''; //As above
        $newUnit -> persist(); //As above
        if ($copyQuestions) {
         $testQuestions = $oldTest -> getQuestions(true);
         $newQuestions = array();
         if (sizeof($testQuestions) > 0) {
          $result = eF_getTableData("questions", "*", "id in (".implode(",", array_keys($testQuestions)).")");
          foreach ($result as $value) {
           $questionData[$value['id']] = $value;
           unset($questionData[$value['id']]['id']);
          }
         }
         foreach ($testQuestions as $key => $oldQuestion){
          $questionData[$key]['content_ID'] = $newUnit -> offsetGet('id');
          $questionData[$key]['lessons_ID'] = $newUnit -> offsetGet('lessons_ID');
          $questionData[$key]['text'] = $this -> copyQuestionFiles($questionData[$key]['text'], $oldUnit['lessons_ID']);
          $questionData[$key]['explanation'] = $this -> copyQuestionFiles($questionData[$key]['explanation'], $oldUnit['lessons_ID']);
          $newQuestion = Question :: createQuestion($questionData[$key]);
          $qid = $newQuestion -> question['id'];
          $newQuestions[$qid] = $oldTest -> getAbsoluteQuestionWeight($oldQuestion -> question['id']);
         }
         $newTest -> addQuestions($newQuestions);
        }
        return $newUnit;
    }
 public function copyQuestionFiles($data, $sourceId) {
  //$data = $question['text'];
  preg_match_all("/view_file\.php\?file=(\d+)/", $data, $matchesId);
        $filesId = $matchesId[1];
        preg_match_all("#(".G_SERVERNAME.")*content/lessons/(.*)\"#U", $data, $matchesPath);
        $filesPath = $matchesPath[2];
        foreach ($filesId as $file) {
            $files[] = $file;
        }
        foreach ($filesPath as $file) {
            $files[] = G_LESSONSPATH.html_entity_decode($file);
        }
        $lesson = new EfrontLesson($this -> lessonId);
        //$data   = $unit -> offsetGet('data');
        foreach ($files as $file){
         try {
          $sourceFile = new EfrontFile($file);
          $sourceFileOffset = preg_replace("#".G_LESSONSPATH."#", "", $sourceFile['directory']);
          $position = strpos($sourceFileOffset, "/"); //check case that the file is in a subfolder of the lesson
          if ($position !== false) {
           $sourceLink = mb_substr($sourceFileOffset, $position+1);
           mkdir($lesson -> getDirectory().$sourceLink.'/', 0755, true);
           $copiedFile = $sourceFile -> copy($lesson -> getDirectory().$sourceLink.'/'.basename($sourceFile['path']), false);
          } else {
           $copiedFile = $sourceFile -> copy($lesson -> getDirectory().basename($sourceFile['path']), false);
          }
          str_replace("view_file.php?file=".$file, "view_file.php?file=".$copiedFile -> offsetGet('id'), $data);
          $data = preg_replace("#(".G_SERVERNAME.")*content/lessons/".$sourceId."/(.*)#", "content/lessons/".$this -> lessonId.'/${2}', $data);
         } catch (EfrontFileException $e) {
          if ($e -> getCode() == EfrontFileException :: FILE_ALREADY_EXISTS) {
           $copiedFile = new EfrontFile($lesson -> getDirectory().'/'.basename($sourceFile['path']));
           str_replace("view_file.php?file=".$file, "view_file.php?file=".$copiedFile -> offsetGet('id'), $data);
           $data = preg_replace("#(".G_SERVERNAME.")*content/lessons/".$sourceId."/(.*)#", "content/lessons/".$this -> lessonId.'/${2}', $data, -1, $count);
          }
         } //this means that the file already exists
        }
        //$question['text'] = $data;
  return $data;
 }
    /**

     * Copy unit

     *

     * This function copies a unit (along with its children)into the current content tree

     * If the unit corresponds to a test, then it copies the corresponding test

     * <br/>Example:

     * <code>

     * $currentContent = new EfrontContentTree(5);           //Initialize content for lesson with id 5

     * $sourceUnit = new EfrontUnit(20);                     //Get the unit with id = 20

     * $currentContent -> copyUnit($sourceUnit, false);   //Copy the source unit into the content tree (at its end)

     * </code>

     *

     * @param EfrontUnit $sourceUnit The unit object to be copied

     * @param mixed $targetUnit The id of the parent unit (or the parent EfrontUnit)in which the new unit will be copied, or false (the unit will be appended at the end)

     * @return EfrontUnit The newly created unit object

     * @since 3.5.0

     * @access public

     */
    public function copyUnit($sourceUnit, $targetUnit = false, $previousContentId = false) {
        if (!($sourceUnit instanceof EfrontUnit)) {
            $sourceUnit = new EfrontUnit($sourceUnit);
        }
        if ($sourceUnit -> offsetGet('ctg_type') == 'tests') {
            $tid = eF_getTableData("tests, content", "tests.id as id", "tests.content_ID = content.id and content.id =".$sourceUnit -> offsetGet('id'));
            $testUnit = $this -> copyTest($tid[0]['id'], $targetUnit);
            return $testUnit;
        } else if ($sourceUnit -> offsetGet('ctg_type') == "scorm") {
            $sid = eF_getTableData("scorm_data, content", "scorm_data.id", "scorm_data.content_ID = content.id and content.id =".$sourceUnit -> offsetGet('id')." and scorm_data.users_LOGIN is null");
            $scormUnit = $this -> copyScorm($sid[0]['id'], $sourceUnit, $targetUnit);
            return $scormUnit;
        } else {
            if ($targetUnit) {
                if (!($targetUnit instanceof EfrontUnit)) {
                    $targetUnit = new EfrontUnit($targetUnit);
                }
                $newParentUnit = $this -> copySimpleUnit($sourceUnit, $targetUnit, $previousContentId);
            } else {
                $newParentUnit = $this -> copySimpleUnit($sourceUnit, false);
            }
            $sourceTree = new EfrontContentTree($sourceUnit -> offsetGet('lessons_ID'), true);
            $children = $sourceTree -> getNodeChildren($sourceUnit); //$children is a RecursiveArrayIterator
            while ($children -> valid()) {
                $childUnit = $children -> current();
                if ($childUnit instanceof EfrontUnit){
                    $this -> copyUnit($childUnit, $newParentUnit);
                }
                $children -> next();
            }
            return $newParentUnit;
        }
    }
    /**

     * Copy simple unit

     *

     * This function copies a unit (NOT its children) into the current content tree

     * <br/>Example:

     * <code>

     * $currentContent = new EfrontContentTree(5);           //Initialize content for lesson with id 5

     * $sourceUnit = new EfrontUnit(20);                     //Get the unit with id = 20

     * $currentContent -> copySimpleUnit($sourceUnit, false);   //Copy the source unit into the content tree (at its end)

     * </code>

     *

     * @param EfrontUnit $sourceUnit The unit object to be copied

     * @param mixed $targetUnit The id of the parent unit (or the parent EfrontUnit)in which the new unit will be copied, or false (the unit will be appended at the end)

     * @param mixed $previousUnit The id of the previous unit (or the unit itself) of the new unit, or false (the unit will be put to the end of the units)

     * @param boolean $copyFiles whether to copy files as well.

     * @param boolean $copyQuestions Whether to copy questions as well

     * @return EfrontUnit The newly created unit object

     * @since 3.5.0

     * @access public

     */
    public function copySimpleUnit($sourceUnit, $targetUnit = false, $previousUnit = false, $copyFiles = true, $copyQuestions = true) {
     if (!($sourceUnit instanceof EfrontUnit)) {
      $sourceUnit = new EfrontUnit($sourceUnit);
     }
     $newUnit['name'] = $sourceUnit -> offsetGet('name');
        $newUnit['ctg_type'] = $sourceUnit -> offsetGet('ctg_type');
        $newUnit['data'] = $sourceUnit -> offsetGet('data');
        $newUnit['lessons_ID'] = $this -> lessonId;
        if ($targetUnit) {
            if ($targetUnit instanceOf EfrontUnit) {
                $newUnit['parent_content_ID'] = $targetUnit -> offsetGet('id');
            } else if (eF_checkParameter($targetUnit, 'id')) {
                $newUnit['parent_content_ID'] = $targetUnit;
            }
            if ($previousUnit instanceOf EfrontUnit) {
                $newUnit['previous_content_ID'] = $previousUnit -> offsetGet('id');
            } else if (eF_checkParameter($previousUnit, 'id')) {
                $newUnit['previous_content_ID'] = $previousUnit;
            }
            $unit = $this -> insertNode($newUnit);
        } else {
            $unit = $this -> appendUnit($newUnit);
        }
        if ($copyFiles) {
            $files = $unit -> getFiles();
            $lesson = new EfrontLesson($this -> lessonId);
            $data = $unit -> offsetGet('data');
            foreach ($files as $file){
                try {
                    $sourceFile = new EfrontFile($file);
                    $sourceFileOffset = preg_replace("#".G_LESSONSPATH."#", "", $sourceFile['directory']);
     $position = strpos($sourceFileOffset, "/"); //check case that the file is in a subfolder of the lesson
     if ($position !== false) {
      $sourceLink = mb_substr($sourceFileOffset, $position+1);
      mkdir($lesson -> getDirectory().$sourceLink.'/', 0755, true);
      $copiedFile = $sourceFile -> copy($lesson -> getDirectory().$sourceLink.'/'.basename($sourceFile['path']), false);
     } else {
      $copiedFile = $sourceFile -> copy($lesson -> getDirectory().basename($sourceFile['path']), false);
     }
                    str_replace("view_file.php?file=".$file, "view_file.php?file=".$copiedFile -> offsetGet('id'), $data);
                    $data = preg_replace("#(".G_SERVERNAME.")*content/lessons/".$sourceUnit['lessons_ID']."/(.*)#", "content/lessons/".$this -> lessonId.'/${2}', $data);
                } catch (EfrontFileException $e) {
                    if ($e -> getCode() == EfrontFileException :: FILE_ALREADY_EXISTS) {
                        $copiedFile = new EfrontFile($lesson -> getDirectory().'/'.basename($sourceFile['path']));
                        str_replace("view_file.php?file=".$file, "view_file.php?file=".$copiedFile -> offsetGet('id'), $data);
                        $data = preg_replace("#(".G_SERVERNAME.")*content/lessons/".$sourceUnit['lessons_ID']."/(.*)#", "content/lessons/".$this -> lessonId.'/${2}', $data, -1, $count);
                    }
                } //this means that the file already exists
            }
            $unit -> offsetSet('data', $data);
         if ($file && $unit['ctg_type'] == 'scorm' || $unit['ctg_type'] == 'scorm_test') {
          $d = new EfrontDirectory(dirname($file));
          $d -> copy($lesson -> getDirectory().basename(dirname($file)), true);
         }
        }
        $unit -> persist();
  // copying questions that belong to this unit
  if ($copyQuestions) {
   $questions = eF_getTableData("questions","*","content_ID=".$sourceUnit -> offsetGet('id'));
   for ($k = 0; $k < sizeof($questions); $k++) {
    $questions[$k]['content_ID'] = $unit-> offsetGet('id');
    $questions[$k]['lessons_ID'] = $unit-> offsetGet('lessons_ID');
    unset($questions[$k]['id']);
    eF_insertTableData("questions",$questions[$k]);
   }
  }
        return $unit;
    }
    /**

     * Copy a simple scorm unit or test

     *

     * This function copies a scorm unit/test (NOT its children) into the current content tree

     * <br/>Example:

     * <code>

     * $currentContent = new EfrontContentTree(5);           //Initialize content for lesson with id 5

     * $currentContent -> copySimpleUnit(3, false);   //Copy the scorm unit with id = 3 into the content tree (at its end)

     * </code>

     *

     * @param int The id of the scorm unit/test (in the scorm_data table)

     * @param EfrontUnit $sourceUnit The unit object to be copied

     * @param mixed $targetUnit The id of the parent unit (or the parent EfrontUnit)in which the new unit will be copied, or false (the unit will be appended at the end)

     * @return EfrontUnit The newly created unit object

     * @since 3.5.0

     * @access public

     */
    public function copyScorm($sid, $sourceUnit, $targetUnit){
        $newUnit = $this -> copySimpleUnit($sourceUnit, $targetUnit, false);
        $data = eF_getTableData("scorm_data", "*", "id = ".$sid);
        $scorm_array = $data[0];
        unset($scorm_array['id']);
        $scorm_array['content_ID'] = $newUnit -> offsetGet('id');
        eF_insertTableData("scorm_data", $scorm_array);
        return $newUnit;
    }
    public function fromXMLNode($node){
        for ($i = 0; $i < sizeof($node->content->unit); $i++){
            importUnitFromXML($xml->unit[$i], 0);
        }
        $this -> reset();
    }
    public function fromXML($xmlfile){
        $xml = simplexml_load_file($xmlfile);
        for ($i = 0; $i < sizeof($xml->content->unit); $i++){
            importUnitFromXML($xml->unit[$i], 0);
        }
        $this -> reset();
    }
    private function importUnitFromXML($unitelement, $parentid){
        $fields = array();
        $fields['name'] = (string) $unitelement->name;
        $fields['data'] = (string) $unitelement->data;
        $fields['ctg_type'] = (string) $unitelement->ctg_type;
        $fields['parent_content_ID'] = $parentid;
        $uid = ef_insertTableData("content", $fields);
        EfrontSearch :: insertText($fields['name'], $uid, "content", "title");
        EfrontSearch :: insertText($fields['data'], $uid, "content", "data");
        if ($fields['ctg_type'] == 'tests'){
            $testfields = array();
            $testfields['content_id'] = (string) $unitelement->id;
            $testfields['duration'] = (string) $unitelement->test[0]->duration;
            $testfields['redoable'] = (string) $unitelement->test[0]->redoable;
            $testfields['onebyone'] = (string) $unitelement->test[0]->onebyone;
            $testfields['answers'] = (string) $unitelement->test[0]->answers;
            $testfields['description'] = (string) $unitelement->test[0]->description;
            $testfields['shuffle_questions'] = (string) $unitelement->test[0]->shuffle_questions;
            $testfields['shuffle_answers'] = (string) $unitelement->test[0]->shuffle_answers;
            $testfields['given_answers'] = (string) $unitelement->test[0]->given_answers;
            $tid = ef_insertTableData("tests", $testfields);
        }
        //import the subunits
        for ($i = 0; $i < sizeof($unitelement->unit); $i++){
            importUnitFromXML($unitelement->unit[$i]);
        }
    }
    /**

     * Create HTML representation of the content tree

     *

     * This function is used to create an HTML representation of the content tree

     * The representation is based on javascript library drag_drop_folder_tree.js

     * If an iterator is not specified, then the tree displayed corrresponds to the default

     * tree (excluding inactive units). Otherwise, the specified iterator is used.

     * $treeId should be specified in case there will be more than one trees on the same page.

     * $options specifies the appearance and behaviour of the tree. Possible options are:

     * <br/>- edit   (true/false)

     * <br/>- delete (true/false)

     * <br/>- activate (true/false)

     * <br/>- drag (true/false)

     * <br/>- noclick (true/false)

     * <br/>- selectedNode (node id)

     * <br/>- truncateNames (maximum length)

     * <br/>- expand (true/false)

     * <br/>- tree_root (true/false) (whether to display a root node)

     * <br/>- show_hide (true/false) (whether to display the show/hide header)

     * <br/>- onclick (function)

     * <br/>- custom (any custom code to be put next to each unit name)

     * <br/>Example:

     * <code>

     * echo $content -> toHTML();                               //Display tree with all defaults

     * $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($content -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('ctg_type' => 'theory'));

     * echo $content -> toHTML($iterator);                      //Display tree with only theory nodes

     * echo $content -> toHTML($iterator, 'theory_tree')        //Display tree with only theory nodes, having id 'theory_tree'

     * echo $content -> toHTML($iterator, 'theory_tree', array('edit' => 1, 'delete' => 1))     //Display tree with only theory nodes, having id 'theory_tree', and which is editable and deleteable

     * </code>

     *

     * @param RecursiveIteratorIterator $iterator The content tree iterator

     * @param string $treeId The HTML id that will be assigned to the tree

     * @param array $options Behaviour options for the tree

     * @param array $scormState An array that lists special parameters for depicting SCO state (applicable to scorm 2004 content only)

     * @return string The HTML code

     * @since 3.5.0

     * @access public

     */
    public function toHTML($iterator = false, $treeId = false, $options = array(), $scormState = array()) {
  !isset($options['hideFeedback']) ? $options['hideFeedback'] = false : null;
        !isset($options['onclick']) ? $options['onclick'] = false : null;
        !isset($options['custom']) ? $options['custom'] = false : null;
        !isset($options['tree_root']) ? $options['tree_root'] = false : null;
        isset($options['selectedNode']) OR $options['selectedNode'] = '';
  //Decide whether the tree is draggable
  isset($options['drag']) && $options['drag'] ? $nodrag = 'false' : $nodrag = 'true';
  //Should the tree expand
  isset($options['expand']) && $options['expand'] ? $expand = $options['expand'] : null;
  //Show the expand all/collapse all link
  !isset($options['show_hide']) || $options['show_hide'] ? $showHide = true : $showHide = false;
        if (!$iterator) {
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)); //Default iterator excludes non-active units
        }
        if (!$treeId) {
            $treeId = 'dhtml_tree';
        }
        $iterator -> rewind();
        $current = $iterator -> current();
        $depth = $iterator -> getDepth();
        $initDepth = $depth;
        $treeString = '';
        $count = 0; //Counts the total number of nodes, used to signify whether the tree has content
        if (!$current && !$options['tree_root']) { //Completely empty tree
         $treeString .= '<span class = "emptyCategory">'._NOCONTENTFOUND."</span>";
        }
        while ($iterator -> valid()) {
            $iterator -> next();
            //$current['ctg_type'] == 'tests' ? $contentType = 'tests' : $contentType = 'content';
   if ($current['ctg_type'] == "tests" || $current['ctg_type'] == "feedback") {
    $contentType = $current['ctg_type'];
   } else {
    $contentType = 'content';
   }
            $scorm2004 = in_array($current['scorm_version'], EfrontContentTree::$scorm2004Versions);
            $linkClass = array();
            $liClass = array();
            $tooltip = array();
            $fullName = htmlspecialchars($current['name']); //Full name will be needed for the link title, which is never truncated
            //Decide whether the unit name will be truncated
            $unitName = htmlspecialchars($current['name']);
            if (isset($options['truncateNames']) && mb_strlen($current['name']) > $options['truncateNames']) {
                $unitName = mb_substr(htmlspecialchars($current['name']), 0, $options['truncateNames']).'...';
            }
            //Create the activate/deactivate link
            //isset($options['activate'])      && $options['activate']                                                                             ? $activateLink    = '<a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.$contentType.'&op=unit_order&activate_unit='.$current['id'].'"><img style = "vertical-align:middle" src = "images/16x16/trafficlight_green.png" title = "'._ACTIVATE.'" alt = "'._ACTIVATE.'" border = "0" /></a>'  : $activateLink = '';
            $activateLink = '';
            if (isset($options['activate']) && $options['activate']) {
                if ($current['active']) {
                    $image = 'trafficlight_green.png';
                    $title = _DEACTIVATE;
                } else {
                    $image = 'trafficlight_red.png';
                    $title = _ACTIVATE;
                }
                $activateLink = '<a href = "javascript:void(0)" onclick = "JSTreeObj.activateUnit(null, $(\'node'.$current['id'].'\').down().next().next())"><img class = "handle" src = "images/16x16/'.$image.'" title = "'.$title.'" alt = "'.$title.'" /></a>';
            }
            //Create the edit link
            $editLink = '';
            if (isset($options['edit']) && $options['edit']) {
                $editLink = '<a href = "'.basename($_SERVER['PHP_SELF']).'?ctg='.$contentType.'&edit='.$current['id'].'"><img class = "handle" src = "images/16x16/edit.png" title = "'._EDIT.'" alt = "'._EDIT.'" /></a>';
   }
   //Create the delete link
            $deleteLink = '';
            if (isset($options['delete']) && $options['delete']) {
                $deleteLink = '<a href = "javascript:void(0)" onclick = "JSTreeObj.deleteUnit(null, $(\'node'.$current['id'].'\').down().next().next())"><img src = "images/16x16/error_delete.png" title = "'._DELETE.'" alt = "'._DELETE.'" class = "handle"/></a>';
            }
            //Create the target link
            $targetLink = basename($_SERVER['PHP_SELF']).'?view_unit='.$current['id'];
            //Set the selected node
            if (isset($options['selectedNode']) && $options['selectedNode'] == $current['id']) {
                $linkClass[] = 'drag_tree_current';
            }
            //Set the display style according to whether the unit has data
            if ($current['data'] == '' && $current['ctg_type'] == 'scorm' && $scorm2004) {
                $linkClass[] = 'treeHeader';
            } else if ($current['data'] == '' && $current['ctg_type'] != 'tests' && $current['ctg_type'] != 'feedback') {
                $linkClass[] = 'treeNoContent';
                $fullName.= ' ('._EMPTYUNIT.')';
                $targetLink = 'javascript:void(0)';
   }
   //Set the display style according to whether the unit is inactive
   if ($current['active'] == 0) {
       $linkClass[] = 'inactiveLink';
   }
   if ($current['ctg_type'] == "feedback" && $options['hideFeedback'] == true) {
    $linkClass[] = 'inactiveLink';
    $targetLink = 'javascript:void(0)';
   }
   $showTools = true;
   if (isset($options['noclick']) && $options['noclick']) {
                $targetLink = 'javascript:void(0)';
   }
   if (!$options['onclick'] && $targetLink == 'javascript:void(0)') {
             $linkClass[] = 'treeUnclickable';
            }
            //Set the class name, based on the unit status
            if ($current['ctg_type'] == 'scorm') {
                $ctgType = 'theory';
            } else if ($current['ctg_type'] == 'scorm_test') {
                $ctgType = 'tests';
            } else {
                $ctgType = $current['ctg_type'];
   }
            if (isset($current['incomplete']) && $current['incomplete']) {
                $liClass[] = $ctgType.'_incomplete';
                $tooltip[] = '('._TESTSTARTEDAT.': '.formatTimestamp($current['incomplete'], 'time').')';
            } else if (isset($current['failed']) && $current['failed']) {
                $liClass[] = $ctgType.'_failed';
            } else if ((isset($current['seen']) && $current['seen']) || isset($current['completed']) && $current['completed']) {
                $liClass[] = $ctgType.'_passed';
            } else {
                $liClass[] = $ctgType;
   }
   /*



            if ((isset($current['seen']) && $current['seen']) ||(isset($current['completed']) && $current['completed']) || (isset($current['passed']) && $current['passed'])) {

				$liClass[] = $ctgType.'_passed';



            } else if (isset($current['incomplete']) && $current['incomplete'] && !$current['failed']) {

				$liClass[] = $ctgType.'_incomplete';

				$tooltip[] = '('._TESTSTARTEDAT.': '.formatTimestamp($current['incomplete'], 'time').')';



            } else if (isset($current['failed']) && $current['failed']) {

				$liClass[] = $ctgType.'_failed';



            } else {

                $liClass[] = $ctgType;

			}

 */
            if ($options['onclick']) {
                $onclick = $options['onclick'];
            } else if (in_array('treeUnclickable', $linkClass)) {
                $onclick = 'return false';
            } else {
                $onclick = '';
   }
            //$toolsString = '<span class = "toolsDiv" style = "position:absolute">'.$activateLink.$editLink.$deleteLink.'</span>';
   $toolsString = '<span>'.$activateLink.$startLink.$editLink.$deleteLink.$options['custom'][$current['id']].'</span>';
   if (!$showTools) {
    unset($toolsString);
            }
            $treeString .= '
                <li style = "white-space:nowrap;'.$display.'" class = "'.implode(" ", $liClass).'" id = "node'.$current['id'].'" noDrag = "'.$nodrag.'" noRename = "true" noDelete = "true">
                    <a onclick = "'.$onclick.'" class = "'.(!$current['active'] ? 'treeinactive' : '').' treeLink '.implode(" ", $linkClass).'" href = "'.$targetLink.'" title = "'.$fullName.' '.implode(" ", $tooltip).'">'.$unitName." </a>&nbsp;".$toolsString;
            $iterator -> getDepth() > $depth ? $treeString .= '<ul>' : $treeString .= '</li>';
            for ($i = $depth; $i > $iterator -> getDepth() && $i > $initDepth; $i--) {
                $treeString .= '</ul></li>';
            }
            $current = $iterator -> current();
            $depth = $iterator -> getDepth();
            $count++;
        }
        $str = '';
        if ($showHide) {
            $str .= '
                <div id = "expand_collapse_div'.$treeId.'" '.(isset($expand) ? 'expand = "'.$expand.'"' : null).'>
                    <b><a href = "javascript:void(0)" onclick = "treeObj.setTreeId(\''.$treeId.'\');treeObj.collapseAll();">'._HIDEALL.'</a></b> /
                    <b><a href = "javascript:void(0)" onclick = "treeObj.setTreeId(\''.$treeId.'\');treeObj.expandAll();" >'._SHOWALL.'</a></b><br/>
                </div>';
        }
        $str .= '
            <table>
                <tr><td>
                    <ul id = "'.$treeId.'" class = "dhtmlgoodies_tree" selectedNode = "'.$options['selectedNode'].'">';
        if ($options['tree_root']) {
            $str .= '
                    <li class = "theory" id = "0" noDrag = "false">
                        <a class = "treeactive treeLink" style = "white-space:nowrap;" href = "javascript:void(0)" title = "'._TREEROOT.'">'._TREEROOT."</a>&nbsp;";
        }
        $str .= $treeString.'
                    </ul>
                </td></tr>
            </table>';
        return $str;
    }
    /**

     * Create array to be used for HTML options

     *

     * This function is used to create a structure that can be used

     * in select lists. The array is of the form [content id] => [content name string]

     * where content name is prepended with spaces and special characters "&raquo;" that

     * denote its depth.

     * <br/>Example:

     * <code>

     * $optionsArray = $content -> toHTMLSelectOptions();

     * </code>

     * An iterator may be optionally specified, in order to display specific units (by default

     * all active units are used).

     * Note that unit names more than 50 characters long are truncated.

     *

     * @param RecursiveIteratorIterator $iterator The tree iterator to be used

     * @return array The options array

     * @since 3.5.0

     * @access public

     */
    public function toHTMLSelectOptions($iterator = false) {
        if (!$iterator) {
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)); //Default iterator excludes non-active units
        }
        $optionsArray = array();
        foreach ($iterator as $value) {
            mb_strlen($value['name']) > 50 ? $value['name'] = mb_substr($value['name'], 0, 50).'...' : null;
            if ($iterator -> getDepth()) {
                $optionsArray[$value['id']] = implode("", array_fill(0, $iterator -> getDepth(), "&nbsp;&nbsp;")).'&raquo;&nbsp;'.$value['name']; //This line prints spaces and a >> in front of every unit. The spaces number depend on the depth of the unit
            } else {
                $optionsArray[$value['id']] = $value['name'];
            }
        }
        return $optionsArray;
    }
    /**

     * Return path strings for each unit

     *

     * This function returns path strings for each unit, depicting its position in the content tree and

     * its parents.

     * <br>Example:

     * <code>

     * $content = new EfrontContentTree(4);                                 //Initialize content tree for lesson with id 4

     * $patString = $content -> toPathStrings();

     * //Returns an array with contents like:

     * //[364] => Unit 1: Overview

     * //[360] => Unit 2: Introduction to mathematics

     * //[361] => Unit 2: Introduction to mathematics » Unit 2.1: Geometry

     * </code>

     *

     * @param RecursiveIteratorIterator $iterator The tree iterator to be used

     * @return array The path strings array

     * @since 3.5.2

     * @access public

     */
    public function toPathStrings($iterator = false) {
        if (!$iterator) {
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)); //Default iterator excludes non-active units
        }
        $pathStrings = array();
        foreach ($iterator as $value) {
            mb_strlen($value['name']) > 50 ? $value['name'] = mb_substr($value['name'], 0, 50).'...' : null;
            foreach ($this -> getNodeAncestors($value['id']) as $key => $node) {
                 $pathStrings[$value['id']][$node['id']] = $node['name'];
            }
            $pathStrings[$value['id']] = implode("&nbsp;&raquo;&nbsp;", array_reverse($pathStrings[$value['id']]));
        }
        return $pathStrings;
    }
    /**

     * Get node ancestors - cached

     *

     * This function overloads EfrontTree : getNodeAncestors to support for caching

     *

     * @param mixed $node Either the node id or an EfrontNode object

     * @param boolean $refresh Whether to refresh the cached copy

     * @see libraries/EfrontTree#getNodeAncestors()

     * @since 3.5.3

     * @access public

     */
 public function getNodeAncestors($node, $refresh = false) {
  $node instanceof ArrayObject ? $nodeId = $node['id'] : $nodeId = $node;
  if (isset($this -> cacheNodeAncestors[$nodeId]) && $this -> cacheNodeAncestors[$nodeId] && !$refresh) {
   return $this -> cacheNodeAncestors[$nodeId];
  } else {
   $parents = parent :: getNodeAncestors($nodeId);
   $this -> cacheNodeAncestors[$nodeId] = $parents;
   return $parents;
  }
 }
 /**

	 * Create empty units from an array

	 *

	 * This functions takes a nested array of names and converts them to an hierarchy

	 * of empty units.

	 *

	 * @param array $structure The names that will be converted to a units structure

	 * @return array The structure

	 * @since 3.6.0

	 * @access public

	 */
    public function createEmptyUnits($structure, $lessons_ID) {
        foreach ($structure as $key => $value) {
            $sizes[] = sizeof($value);
        }
        $maxSize = max($sizes);
        $unit = $this -> getLastNode();
        $created = array();
        $treeStructure = array();
        for ($i = 0; $i < $maxSize; $i++) {
            //First get the elements that have index 0, meaning they are on the top, then the 1 etc
         foreach ($structure as $key => $value) {
             if (isset($value[$i]) && !in_array($value[$i], $created)) {
              $fields = array('name' => $value[$i],
                              'lessons_ID' => $lessons_ID,
                              'data' => '',
                              'parent_content_ID' => array_search($value[$i-1], $created) ? array_search($value[$i-1], $created) : 0);
              $unit = $this -> insertNode($fields);
              $created[$unit['id']] = $value[$i];
             }
             if ($value[$i]) {
                 $treeStructure[$key][$unit['id']] = $value[$i];
             }
         }
        }
        return $treeStructure;
    }
}
/**

 * Iterator Filter for traversing only visitable units, incuding empty ones

 *

 * @package eFront

 * @version 3.5.0

 */
class EfrontVisitableAndEmptyFilterIterator extends FilterIterator
{
    /**

     * Accepts only units that may be visited, i.e. are active

     *

     * @return boolean

     * @since 3.5.0

     * @access public

     */
    function accept() {
        $current = $this -> current();
        return $current instanceof ArrayObject && ($current['active'] == 1 && $current['publish'] == 1);
    }
}
/**

 * Iterator Filter for traversing only visitable units

 *

 * @package eFront

 * @version 3.5.0

 */
class EfrontVisitableFilterIterator extends FilterIterator
{
    /**

     * Accepts only units that may be visited, i.e. are active and either have content or are tests

     *

     * @return boolean

     * @since 3.5.0

     * @access public

     */
    function accept() {
        $current = $this -> current();
        $scorm2004 = in_array($current['scorm_version'], EfrontContentTree::$scorm2004Versions);
        return $current instanceof ArrayObject && ($current['active'] == 1 && $current['publish'] == 1 && ($current['data'] != '' || $current['ctg_type'] == 'tests' || $scorm2004 || $current['ctg_type'] == 'feedback'));
    }
}
/**

 * Iterator Filter for traversing only SCORM units

 *

 * @package eFront

 * @version 3.5.0

 */
class EfrontSCORMFilterIterator extends FilterIterator
{
    /**

     * Accepts only SCORM units

     *

     * @return boolean

     * @since 3.5.0

     * @access public

     */
    function accept() {
        if ($this -> current() -> offsetGet('ctg_type') == 'scorm' || $this -> current() -> offsetGet('ctg_type') == 'scorm_test') {
            return true;
        }
    }
}
/**

 * Iterator Filter for traversing only non-SCORM units

 *

 * @package eFront

 * @version 3.5.0

 */
class EfrontNoSCORMFilterIterator extends FilterIterator
{
    /**

     * Accepts only non-SCORM units

     *

     * @return boolean

     * @since 3.5.0

     * @access public

     */
    function accept() {
        if ($this -> current() -> offsetGet('ctg_type') != 'scorm' && $this -> current() -> offsetGet('ctg_type') != 'scorm_test') {
            return true;
        }
    }
}
/**

 * Iterator Filter for traversing only Test units

 *

 * @package eFront

 * @version 3.5.0

 */
class EfrontTestsFilterIterator extends FilterIterator
{
    /**

     * Accepts only test units (normal or SCORM)

     *

     * @return boolean

     * @since 3.5.0

     * @access public

     */
    function accept() {
        if ($this -> current() -> offsetGet('ctg_type') == 'tests' || $this -> current() -> offsetGet('ctg_type') == 'scorm_test') {
            return true;
        }
    }
}
/**

 * Iterator Filter for traversing only non-Test units

 *

 * @package eFront

 * @version 3.5.0

 */
class EfrontNoTestsFilterIterator extends FilterIterator
{
    /**

     * Accepts only test units (normal or SCORM)

     *

     * @return boolean

     * @since 3.5.0

     * @access public

     */
    function accept() {
        if ($this -> current() -> offsetGet('ctg_type') != 'tests') {
            return true;
        }
    }
}
/**

 * Iterator Filter for traversing only Feedback units

 *

 * @package eFront

 * @version 3.6.3

 */
class EfrontFeedbackFilterIterator extends FilterIterator
{
    /**

     * Accepts only feedback units

     *

     * @return boolean

     * @since 3.6.3

     * @access public

     */
    function accept() {
        if ($this -> current() -> offsetGet('ctg_type') == 'feedback') {
            return true;
        }
    }
}
/**

 * Iterator Filter for traversing only non-Feedback units

 *

 * @package eFront

 * @version 3.6.3

 */
class EfrontNoFeedbackFilterIterator extends FilterIterator
{
    /**

     * Accepts only non-feedback units

     *

     * @return boolean

     * @since 3.6.3

     * @access public

     */
    function accept() {
        if ($this -> current() -> offsetGet('ctg_type') != 'feedback') {
            return true;
        }
    }
}
/**

 * Iterator Filter for traversing only Theory units

 *

 * @package eFront

 * @version 3.5.0

 */
class EfrontTheoryFilterIterator extends FilterIterator
{
    /**

     * Accepts only theory and SCORM units

     *

     * @return boolean

     * @since 3.5.0

     * @access public

     */
    function accept() {
        if ($this -> current() -> offsetGet('ctg_type') == 'theory' || $this -> current() -> offsetGet('ctg_type') == 'scorm') {
            return true;
        }
    }
}
/**

 * Iterator Filter for traversing only Example units

 *

 * @package eFront

 * @version 3.5.0

 */
class EfrontExampleFilterIterator extends FilterIterator
{
    /**

     * Accepts only examplesunits

     *

     * @return boolean

     * @since 3.5.0

     * @access public

     */
    function accept() {
        if ($this -> current() -> offsetGet('ctg_type') == 'examples') {
            return true;
        }
    }
}
/**

 * Iterator Filter for traversing only Content (theory, example and SCORM) units

 *

 * @package eFront

 * @version 3.5.0

 */
class EfrontContentFilterIterator extends FilterIterator
{
    /**

     * Accepts content that is not tests

     *

     * @return boolean

     * @since 3.5.0

     * @access public

     */
    function accept() {
        if ($this -> current() -> offsetGet('ctg_type') != 'tests' && $this -> current() -> offsetGet('ctg_type') != 'scorm_test') {
            return true;
        }
    }
}
/**

 * Iterator for removing data from nodes

 *

 * @package eFront

 * @version 3.5.3

 */
class EfrontRemoveDataFilterIterator extends FilterIterator
{
 function accept() {
  $this -> current() -> offsetSet('data', '');
  return true;
 }
}
/**

 * Keeps node only if its key is listed inside the $filter array

 *

 * @package eFront

 * @version 3.5.3

 */
class EfrontInArrayFilterIterator extends FilterIterator
{
 /**

	 * Filter mode

	 *

	 * @var mixed filter mode

	 */
    protected $filter;
    /**

     * Constructor

     *

     * @param Iterator $it

     * @param array $filter

     */
    function __construct($it, $filter) {
        parent::__construct($it);
        !is_array($filter) ? $this -> filter = array() : $this -> filter = $filter;;
    }
 function accept() {
  return in_array($this -> key(), $this -> filter);
 }
}
