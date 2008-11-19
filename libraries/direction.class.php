<?php
/**
 * File for directions
 *
 * @package eFront
*/

/**
 * Direction exceptions
 *
 * This class extends Exception to provide the exceptions related to directions
 * @package eFront
 * @since 3.5.0
 * 
 */
class EfrontDirectionException extends Exception
{
    /**
     * The direction requested does not exist
     * @since 3.5.0
     */    
    const DIRECTION_NOT_EXISTS = 1051;
    /**
     * The id provided is not valid, for example it is not a number or it is 0
     * @since 3.5.0
     */
    const INVALID_ID        = 1052;
    /**
     * An unspecific error
     * @since 3.5.0
     */
    const GENERAL_ERROR     = 1099;
}

/**
 * This class represents a direction in eFront
 * 
 * @package eFront
 * @since 3.5.0
 */
class EfrontDirection extends ArrayObject
{
    /**
     * The maximum length for direction names. After that, the names appear truncated
     */
    const MAXIMUM_NAME_LENGTH = 50;

    /**
     * Instantiate direction
     *
     * This function is the class constructor, which instantiates the
     * EfrontDirection object, based on the direction values
     * <br/>Example:
     * <code>
     * $direction_array = eF_getTableData("directions", "*", "id=4");
     * $direction = new EfrontDirection($direction_array[0]);
     * </code>
     *
     * @param array $array The direction values
     * @since 3.5.0
     * @access public
     */
    function __construct($direction) {
        if (!is_array($direction)) {
            if (!eF_checkParameter($direction, 'id')) {
                throw new EfrontLessonException(_INVALIDID.': '.$direction, EfrontDirectionException :: INVALID_ID);
            }
            $result = eF_getTableData("directions", "*", "id=".$direction);  
            if (sizeof($result) == 0) {
                throw new EfrontLessonException(_CATEGORYDOESNOTEXIST.': '.$direction, EfrontDirectionException :: DIRECTION_NOT_EXISTS);
            }
            $direction = $result[0];
        }
        parent :: __construct($direction);
    }

    /**
     * Persist changed values
     *
     * This function is used to persist the direction values
     * <br/>Example:
     * <code>
     * $direction_array = eF_getTableData("directions", "*", "id=4");
     * $direction = new EfrontDirection($direction_array[0]);
     * $direction['name'] = 'new name';
     * $direction -> persist();
     * </code>
     *
     * @since 3.5.0
     * @access public
     */
    function persist() {
        foreach (new EfrontAttributesOnlyFilterIterator($this -> getIterator()) as $key => $value) {
            $fields[$key] = $value;
        }
        eF_updateTableData("directions", $fields, "id=".$fields['id']);
    }

    /**
     * Delete a direction
     * This function is used to delete the current direction
     * <br/>Example:
     * <code>
     * $direction_array = eF_getTableData("directions", "*", "id=4");
     * $direction = new EfrontDirection($direction_array[0]);
     * $direction -> delete();
     * </code>
     *
     * @since 3.5.0
     * @access public
     */
    function delete() {
        foreach (new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this)), 'id') as $key => $value) {
            eF_deleteTableData("directions", "id=".$value);                                               //Delete Units from database
        }
    }

    /**
     * Get direction's lessons
     *
     * This function is used to get the lessons that belong
     * to this direction.
     * <br/>Example:
     * <code>
     * $lessons = $direction -> getLessons();
     * </code>
     *
     * @param boolean $returnObjects Whether to return EfrontLesson objects or a simple array
     * @param boolean $subDirections Whether to return subDirections lessons as well
     * @return array An array of lesson ids/names pairs or EfrontLesson objects
     * @since 3.5.0
     * @access public
     */
    function getLessons($returnObjects = false, $subDirections = false) {
        if (!$subDirections) {
            $result = eF_getTableData("lessons", "id, name", "directions_ID=".$this['id']);
        } else {
            $directions = new EfrontDirectionsTree();
            $children       = $directions -> getNodeChildren($this['id']);
            foreach (new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($children)), array('id')) as $value) {
                $siblings[] = $value;
            }
            $result = eF_getTableData("lessons", "id, name", "directions_ID in (".implode(",", $siblings).")");
        }
        $lessons = array();

        foreach ($result as $value) {
            $returnObjects ? $lessons[$value['id']] = new EfrontLesson($value['id']) : $lessons[$value['id']] = $value['name'];
        }

        return $lessons;
    }

    /**
     * Get direction's courses
     *
     * This function is used to get the courses that belong
     * to this direction.
     * <br/>Example:
     * <code>
     * $courses = $direction -> getCourses();
     * </code>
     *
     * @param boolean $returnObjects Whether to return EfrontCourse objects or a simple array
     * @param boolean $subDirections Whether to return subDirections lessons as well
     * @return array An array of course ids/names pairs or EfrontCourse objects
     * @since 3.5.0
     * @access public
     */
    function getCourses($returnObjects = false, $subDirections = false) {
        if (!$subDirections) {
            $result = eF_getTableData("courses", "id, name", "directions_ID=".$this['id']);
        } else {
            $directionsTree = new EfrontDirectionsTree();
            $children       = $directionsTree -> getNodeChildren($this['id']);
            foreach (new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($children)), array('id')) as $value) {
                $siblings[] = $value;
            }
            $result = eF_getTableData("courses", "id, name", "directions_ID in (".implode(",", $siblings).")");
        }
        $courses = array();

        foreach ($result as $value) {
            $returnObjects ? $courses[$value['id']] = new EfrontCourse($value['id']) : $courses[$value['id']] = $value['name'];
        }

        return $courses;
    }

    /**
     * Create direction
     *
     * This function is used to create a new direction
     * <br/>Example:
     * <code>
     * $fields = array('name' => 'new direction');
     * EfrontDirection :: createDirection($fields);
     * </code>
     *
     * @param array $fields The new direction's fields
     * @return EfrontDirection The new direction
     * @since 3.5.0
     * @access public
     * @static
     */
    public static function createDirection($fields = array()) {
        !isset($fields['name']) ? $fields['name'] = 'Default direction' : null;

        $newId     = eF_insertTableData("directions", $fields);
        $result    = eF_getTableData("directions", "*", "id=".$newId);                                            //We perform an extra step/query for retrieving data, sinve this way we make sure that the array fields will be in correct order (forst id, then name, etc)
        $direction = new EfrontDirection($result[0]);

        return $direction;
    }
}


/**
 * This class represents the directions tree and extends EfrontTree class 
 * @package eFront
 * @since 3.5.0 
 */
class EfrontDirectionsTree extends EfrontTree
{
    /**
     * Initialize tree
     *
     * This function is used to initialize the directions tree
     * <br/>Example:
     * <code>
     * $directionsTree = new EfrontDirectionsTree();
     * </code>
     *
     * @since 3.5.0
     * @access public
     */
    function __construct() {
        $this -> reset();
    }

    /**
     * Reset/initialize directions tree
     *
     * This function is used to initialize or reset the directions tree
     * <br/>Example:
     * <code>
     * $directionsTree = new EfrontDirectionsTree();
     * $directionsTree -> reset();
     * </code>
     *
     * @since 3.5.0
     * @access public
     */
    public function reset() {
        $directions = eF_getTableData("directions", "*", "", "name");

        if (sizeof($directions) == 0) {
            $this -> tree = new RecursiveArrayIterator(array());
            return;
        }

        foreach ($directions as $node) {                //Assign previous direction ids as keys to the previousNodes array, which will be used for sorting afterwards
            $nodes[$node['id']] = new EfrontDirection($node);        //We convert arrays to array objects, which is best for manipulating data through iterators
        }

        $rejected = array();
        $tree     = $nodes;
        $count    = 0;                                                                          //$count is used to prevent infinite loops
        while (sizeof($tree) > 1 && $count++ < 1000) {                                       //We will merge all branches under the main tree branch, the 0 node, so its size will become 1
            foreach ($nodes as $key => $value) {
                if ($value['parent_direction_ID'] == 0 || in_array($value['parent_direction_ID'], array_keys($nodes))) {        //If the unit parent is in the $nodes array keys - which are the unit ids- or it is 0, then it is  valid
                    $parentNodes[$value['parent_direction_ID']][]      = $value;               //Find which nodes have children and assign them to $parentNodes
                    $tree[$value['parent_direction_ID']][$value['id']] = array();              //We create the "slots" where the node's children will be inserted. This way, the ordering will not be lost
                } else {
                    $rejected = $rejected + array($value['id'] => $value);                   //Append units with invalid parents to $rejected list
                    unset($nodes[$key]);                                                     //Remove the invalid unit from the units array, as well as from the parentUnits, in case a n entry for it was created earlier
                    unset($parentNodes[$value['parent_direction_ID']]);
                }
            }
            if (isset($parentNodes)) {                                                       //If the unit was rejected, there won't be a $parentNodes array
                $leafNodes = array_diff(array_keys($nodes), array_keys($parentNodes));       //Now, it's easy to see which nodes are leaf nodes, just by subtracting $parentNodes from the whole set
                foreach ($leafNodes as $leaf) {
                    $parent_id = $nodes[$leaf]['parent_direction_ID'];                         //Get the leaf's parent
                    $tree[$parent_id][$leaf] = $tree[$leaf];                                 //Append the leaf to its parent's tree branch
                    unset($tree[$leaf]);                                                     //Remove the leaf from the main tree branch
                    unset($nodes[$leaf]);                                                    //Remove the leaf from the nodes set
                }
                unset($parentNodes);                                                         //Reset $parentNodes; new ones will be calculated at the next loop
            }
        }
        if (sizeof($tree) > 0 && !isset($tree[0])) {                                         //This is a special case, where only one node exists in the tree
            $tree = array($tree);
        }
        foreach ($tree as $key => $value) {
            if ($key != 0) {
                $rejected[$key] = $value;
            }
        }
        
        if (sizeof($rejected) > 0) {                                            //Append rejected nodes to the end of the tree array, updating their parent/previous information
            foreach ($rejected as $key => $value) {
                eF_updateTableData("directions", array("parent_direction_ID" => 0), "id=".$key);
                $value['parent_direction_ID'] = 0;
                $tree[0][] = $value;
            }
        }

        $this -> tree = new RecursiveArrayIterator($tree[0]);
    }

    /**
     * Experimental function for merging lessons and courses to the main tree
     *
     */
    public function reset2() {
        $directions = eF_getTableData("directions", "*", "", "name");
        
        $result  = eF_getTableData("lessons", "*");
        $lessons = array();
        foreach ($result as $value) {
            $lessons[$value['directions_ID']][] = new EfrontLesson($value);
        }
        $result  = eF_getTableData("courses", "*");
        $courses = array();
        foreach ($result as $value) {
            $courses[$value['directions_ID']][] = new EfrontCourse($value);
        }
        
        if (sizeof($directions) == 0) {
            $this -> tree = new RecursiveArrayIterator(array());
            return;
        }

        foreach ($directions as $node) {                //Assign previous direction ids as keys to the previousNodes array, which will be used for sorting afterwards
            $nodes[$node['id']] = new EfrontDirection($node);        //We convert arrays to array objects, which is best for manipulating data through iterators
            $nodes[$node['id']]['lessons'] = $lessons[$node['id']];
            $nodes[$node['id']]['courses'] = $lessons[$node['id']];
        }

        $rejected = array();
        $tree     = $nodes;
        $count    = 0;                                                                          //$count is used to prevent infinite loops
        while (sizeof($tree) > 1 && $count++ < 1000) {                                       //We will merge all branches under the main tree branch, the 0 node, so its size will become 1
            foreach ($nodes as $key => $value) {
                if ($value['parent_direction_ID'] == 0 || in_array($value['parent_direction_ID'], array_keys($nodes))) {        //If the unit parent is in the $nodes array keys - which are the unit ids- or it is 0, then it is  valid
                    $parentNodes[$value['parent_direction_ID']][]      = $value;               //Find which nodes have children and assign them to $parentNodes
                    $tree[$value['parent_direction_ID']][$value['id']] = array();              //We create the "slots" where the node's children will be inserted. This way, the ordering will not be lost
                } else {
                    $rejected = $rejected + array($value['id'] => $value);                   //Append units with invalid parents to $rejected list
                    unset($nodes[$key]);                                                     //Remove the invalid unit from the units array, as well as from the parentUnits, in case a n entry for it was created earlier
                    unset($parentNodes[$value['parent_direction_ID']]);
                }
            }
            if (isset($parentNodes)) {                                                       //If the unit was rejected, there won't be a $parentNodes array
                $leafNodes = array_diff(array_keys($nodes), array_keys($parentNodes));       //Now, it's easy to see which nodes are leaf nodes, just by subtracting $parentNodes from the whole set
                foreach ($leafNodes as $leaf) {
                    $parent_id = $nodes[$leaf]['parent_direction_ID'];                         //Get the leaf's parent
                    $tree[$parent_id][$leaf] = $tree[$leaf];                                 //Append the leaf to its parent's tree branch
                    unset($tree[$leaf]);                                                     //Remove the leaf from the main tree branch
                    unset($nodes[$leaf]);                                                    //Remove the leaf from the nodes set
                }
                unset($parentNodes);                                                         //Reset $parentNodes; new ones will be calculated at the next loop
            }
        }
        if (sizeof($tree) > 0 && !isset($tree[0])) {                                         //This is a special case, where only one node exists in the tree
            $tree = array($tree);
        }
        foreach ($tree as $key => $value) {
            if ($key != 0) {
                $rejected[$key] = $value;
            }
        }
        
        if (sizeof($rejected) > 0) {                                            //Append rejected nodes to the end of the tree array, updating their parent/previous information
            foreach ($rejected as $key => $value) {
                eF_updateTableData("directions", array("parent_direction_ID" => 0), "id=".$key);
                $value['parent_direction_ID'] = 0;
                $tree[0][] = $value;
            }
        }

        $this -> tree = new RecursiveArrayIterator($tree[0]);
    }    
    
    /**
     * Insert node to the tree
     *
     * @param mixed $node
     * @param mixed $parentNode
     * @param mixed $previousNode
     * @since 3.5.0
     * @access public
     */
    public function insertNode($node, $parentNode = false, $previousNode = false) {}

    /**
     * Remove node from tree
     *
     * @param mixed $node
     * @since 3.5.0
     * @access public
     */
    public function removeNode($node) {



    }
    
    /**
     * Print an HTML representation of the directions tree
     *
     * This function is used to print an HTML representation of the HTML tree
     * <br/>Example:
     * <code>
     * $directionsTree -> toHTML();                         //Print directions tree
     * </code>
     * Possible options are:
     * - lessons_link			//a value of '#user_type#' inside the url will be replaced with the user type
     * - courses_link			//a value of '#user_type#' inside the url will be replaced with the user typed
     * - tooltip		 
     * - search					//display the search box (true/false)
     * - tree_tools				//Whether to display the top div with tree tools, show/hide and search (true/false)
     * - url					//A url to search ajax functions for. defaults to current url
     * 
     * @param RecursiveIteratorIterator $iterator An optional custom iterator
     * @param array $lessons An array of EfrontLesson Objects
     * @param array $courses An array of EfrontCourse Objects
     * @param array $userInfo Optional information for the user accessing the tree
     * @param array $options display options for the tree
     * @return string The HTML version of the tree
     * @since 3.5.0
     * @access public
     */
    public function toHTML($iterator = false, $lessons = false, $courses = false, $userInfo = array(), $options = array()) {

        //!isset($options['show_cart'])   ? $options['show_cart']   = false : null;
        //!isset($options['information']) ? $options['information'] = false : null;
        !isset($options['lessons_link']) ? $options['lessons_link'] = false : null;
        !isset($options['courses_link']) ? $options['courses_link'] = false : null;
        !isset($options['tooltip'])      ? $options['tooltip']      = true  : null;
        !isset($options['search'])       ? $options['search']       = false : null;
        !isset($options['tree_tools'])   ? $options['tree_tools']   = true  : null;
        !isset($options['url'])          ? $options['url']          = $_SERVER['REQUEST_URI'] : null;    //Pay attention since REQUEST_URI is empty if accessing index.php with the url http://localhost/
        
        if (!$iterator) {
            $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));
        }

        if ($lessons === false) {                                                    //If a lessons list is not specified, get all active lessons
            $result = eF_getTableData("lessons", "*", "active=1");                   //Get all lessons at once, thus avoiding looping queries
            foreach ($result as $value) {
                $lessons[$value['id']] = new EfrontLesson($value);                   //Create an array of EfrontLesson objects
            }
        }

        $directionsLessons = array();
        foreach ($lessons as $id => $lesson) {
            if (!$lesson -> lesson['active']) {                                      //Remove inactive lessons
                unset($lessons[$id]);
            } elseif (!$lesson -> lesson['course_only']) {                           //Lessons in courses will be handled by the course's display method, so remove them from the list
                $directionsLessons[$lesson -> lesson['directions_ID']][] = $id;      //Create an intermediate array that maps lessons to directions
            }
        }
        
        if ($courses === false) {                                                   //If a courses list is not specified, get all active courses
            $result = eF_getTableData("courses", "*", "active=1");                  //Get all courses at once, thus avoiding looping queries
            foreach ($result as $value) {
                $courses[$value['id']] = new EfrontCourse($value);                  //Create an array of EfrontCourse objects
            }
        }
        $directionsCourses = array();
        foreach ($courses as $id => $course) {
            if (!$course -> course['active']) {                                     //Remove inactive courses
                unset($courses[$id]);
            } else {
                $directionsCourses[$course -> course['directions_ID']][] = $id;     //Create an intermediate array that maps courses to directions
            }
        }
        $roles     = EfrontLessonUser :: getLessonsRoles();
        $roleNames = EfrontLessonUser :: getLessonsRoles(true);

        //We need to calculate which directions will be displayed. We will keep only directions that have lessons or courses and their parents. In order to do so, we traverse the directions tree and set the 'hasNodes' attribute to the nodes that will be kept
        foreach ($iterator as $key => $value) {
            if (isset($directionsLessons[$value['id']]) || isset($directionsCourses[$value['id']])) {
                $count = $iterator -> getDepth();
                $value['hasNodes'] = true;
                isset($directionsLessons[$value['id']]) ? $value['lessons'] = $directionsLessons[$value['id']] : null;        //Assign lessons ids to the direction
                isset($directionsCourses[$value['id']]) ? $value['courses'] = $directionsCourses[$value['id']] : null;        //Assign courses ids to the direction
                while ($count) {
                    $node = $iterator -> getSubIterator($count--);
                    $node['hasNodes'] = true;                        //Mark "keep" all the parents of the node
                }
            }
        }

        $iterator = new EfrontNodeFilterIterator($iterator, array('hasNodes' => true));    //Filter in only tree nodes that have the 'hasNodes' attribute

        $iterator   -> rewind();
        $current    = $iterator -> current();
        $treeString = '';            			
        if ($options['tree_tools']) {
            $treeString = '            			
        				<div style = "padding-top:8px;padding-bottom:8px">
        					'.($options['search'] ? '<span style = "float:right;"><span style = "vertical-align:middle">'._SEARCH.': <input type = "text" style = "vertical-align:middle" onKeyPress = "if (event.keyCode == 13) {filterTree(this)}"></span></span>' : '').'
        					<a href = "javascript:void(0)" onclick = "showAll()" >'._SHOWALL.'</a> / <a href = "javascript:void(0)" onclick = "hideAll()">'._HIDEALL.'</a>
        				</div>';
        }
        $treeString .= '            			
        				<div id = "directions_tree">';

        while ($iterator -> valid()) {
            $children = array();                    //The $children array is used so that when collapsing a direction, all its children disappear as well
            foreach (new EfrontNodeFilterIterator(new ArrayIterator($this -> getNodeChildren($current), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
                $children[] = $key;
            }

            $treeString .= '
                        <table class = "lessonsTable" id = "direction_'.$current['id'].'">
                            <tr>
                            	<td style = "padding-left:'.(20 * $iterator -> getDepth()).'px;"></td>
                            	<td class = "lessonsList" width = "1%" >
                                    <img name = "default_visible_image" id = "subtree_img'.$current['id'].'" src = "images/others/minus.png" style = "vertical-align:middle" align = "center" onclick = "showHideDirections(this, \''.implode(",", $children).'\', \''.$current['id'].'\', (this.getAttribute(\'src\')).match(/plus/) ? \'show\' : \'hide\')">
                                    <span style = "display:none" id = "subtree_children_'.$current['id'].'">'.implode(",", $children).'</span>
                                </td>
                                <td class = "lessonsList" style = "width:100%">
                                    <table>
                                        <tr><td><img src = "images/24x24/directions.png" alt= "Categories" title="Categories" /></td>
                                            <td class = "lessonsList_title">'.$current['name'].'</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>';

            if (sizeof($current['lessons']) > 0) {
                $treeString .= '
                            <tr id = "subtree'.$current['id'].'" name = "default_visible">
                            	<td></td>
                                <td class = "lessonsList_nocolor">&nbsp;</td>
                                <td>
                                    <table width = "100%">';
                foreach ($current -> offsetGet('lessons') as $lessonId) {    
                    $roleBasicType = $roles[$userInfo['lessons'][$lessonId]['user_type']];        //The basic type of the user's role in the lesson             
                    $toolsString   = '';
/*
                    if ($options['show_cart']) {
                        if ($lessons[$lessonId] -> lesson['price']) {
                            $toolsString .= '<a href = "javascript:void(0)"><img style = "vertical-align:middle" src = "images/16x16/cart_add.png" alt = "'._ADDTOCART.'" title = "'._ADDTOCART.'" border = "0" onclick = "ajaxPost(\''.$lessonId.'\', this);" /></a>&nbsp;';
                        } else {
                            $toolsString .= '<a href = "javascript:void(0)"><img style = "vertical-align:middle" src = "images/16x16/redo.png" alt = "'._ENROLL.'" title = "'._ENROLL.'" border = "0" onclick = ""  /></a>&nbsp;';
                        }
                    }
                    if ($options['information']) {
                        $toolsString .= '<a href = "index.php?ctg=lesson_info&lesson='.$lessonId.'"><img style = "vertical-align:middle" src = "images/16x16/about.png" alt = "'._INFORMATION.'" title = "'._INFORMATION.'" border = "0" onclick = ""  /></a>&nbsp;';
                    }
*/
                    $treeString .= '
                                    	<tr class = "directionEntry">';
                    if ($userInfo['lessons'][$lessonId]) {                       
                        if ($roles[$userInfo['lessons'][$lessonId]['user_type']] == 'student' && $userInfo['lessons'][$lessonId]['completed']) {                    //Show the "completed" mark
                            $treeString .= '
                        					<td style = "width:5px;text-align:center">
                            					<img src = "images/16x16/check.png" alt = "'._LESSONCOMPLETE.'" title = "'._LESSONCOMPLETE.'" style = "margin-left:10px;vertical-align:middle" />
                            				</td>';                            
                        } elseif ($roles[$userInfo['lessons'][$lessonId]['user_type']] == 'student') {                                                                //Show the progress bar
                            $treeString .= '
                                            <td style = "width:50px;">
                                                <span class = "progressNumber" style = "width:50px;">'.$userInfo['lessons'][$lessonId]['overall_progress'].'%</span>
                                                <span class = "progressBar" style = "width:'.($userInfo['lessons'][$lessonId]['overall_progress'] / 2).'px;">&nbsp;</span>
                                                &nbsp;&nbsp;
                                            </td>';                            
                        } else {
                            $treeString .= '<td style = "width:1px;padding-bottom:2px"></td>';
                        }
                    }
                    $treeString .= '
                                        <td class = "lessonsList_lessons">&nbsp;';
                    if (!isset($userInfo['lessons'][$lessonId]['from_timestamp']) || $userInfo['lessons'][$lessonId]['from_timestamp']) {
                        if ($options['tooltip']) {
                            $treeString .= '
                            					<a href = "'.($options['lessons_link'] ? str_replace("#user_type#", $roleBasicType, $options['lessons_link']).$lessons[$lessonId] -> lesson['id'] : 'javascript:void(0)').'" class = "info" onmouseover = "updateInformation(this, '.$lessonId.', \'lesson\')" onclick = "this.update(\''.$lessons[$lessonId] -> lesson['name'].'\');">
                            						'.$lessons[$lessonId] -> lesson['name'].'
                            						<img class = "tooltip" border = "0" src = "images/others/tooltip_arrow.gif"/>
                            						<span class = "tooltipSpan"></span>
                            					</a>';
                        } else {
                            $options['lessons_link'] ? $treeString .= '<a href = "'.str_replace("#user_type#", $roleBasicType, $options['lessons_link']).$lessons[$lessonId] -> lesson['id'].'">'.$lessons[$lessonId] -> lesson['name'].'</a>' : $treeString .= $lessons[$lessonId] -> lesson['name'];
                        }
                    } else {
                        $treeString .= '<a href = "javascript:void(0)" class = "inactiveLink" title = "'._CONFIRMATIONPEDINGFROMADMIN.'">'.$lessons[$lessonId] -> lesson['name'].'</a>';
                    }
                    $treeString .= '
                                        '.$toolsString.'
	                                        '.($userInfo['lessons'][$lessonId]['different_role'] ? '&nbsp;<span style = "color:green; font-size: 9px; display:inline">('.$roleNames[$userInfo['lessons'][$lessonId]['user_type']].')</span>' : '').'
                                        </td>
                                	</tr><tr><td colspan="100%"></td></tr>';
                }
                    $treeString .= '
                                    </table>
                                </td></tr>';
            }
            if (sizeof($current['courses']) > 0) {
                $treeString .= '
                            <tr id = "subtree'.$current['id'].'" name = "default_visible">
                            	<td></td>
                                <td class = "lessonsList_nocolor">&nbsp;</td>
                                <td>';
                foreach ($current -> offsetGet('courses') as $courseId) {
                    $treeString .= $courses[$courseId] -> toHTML($userInfo, $options);
                }
                $treeString .= '
                                </td>
                            </tr>';
            }
            $treeString .= '
                        </table>';

            $iterator -> next();
            $current = $iterator -> current();
        }
        
        $treeString .= "
        			</div>
                        <script>
                        	function showAll() {
                        		$$('tr').each(function (tr) 	  {tr.id.match(/subtree/) ? tr.show() : null;});
                           		$$('table').each(function (table) {table.id.match(/direction_/) ? table.show() : null;});
                           		$$('img').each(function (img) {img.src.match(/plus/) ? img.setAttribute('src', 'images/others/minus.png') : null;});
                        	}
                        	function hideAll() {
                        		$$('tr').each(function (tr) 	  {tr.id.match(/subtree/) ? tr.hide() : null;});
                           		//$$('table').each(function (table) {table.id.match(/direction_/) ? table.hide() : null;});
                           		$$('img').each(function (img) {img.src.match(/minus/) ? img.setAttribute('src', 'images/others/plus.png') : null;});
                        	}
                        	
                            function showHideDirections(el, ids, id, mode) {                            	
                            	Element.extend(el);		//IE intialization
                                if (mode == 'show') {
                            		el.up().up().nextSiblings().each(function(s) {s.show()});
                                    if (ids) {
                                        ids.split(',').each(function (s) { showHideDirections($('subtree_img'+id), $('subtree_children_'+s) ? $('subtree_children_'+s).innerHTML : '', s, 'show') });
                                        ids.split(',').each(function (s) { obj = $('direction_'+s); obj ? obj.show() : '';});
    								}
    								$('subtree_img'+id) ? $('subtree_img'+id).setAttribute('src', 'images/others/minus.png') : '';
    							} else {
                            		el.up().up().nextSiblings().each(function(s) {s.hide()});
                                    if (ids) {
                                        ids.split(',').each(function (s) { showHideDirections($('subtree_img'+id), $('subtree_children_'+s) ? $('subtree_children_'+s).innerHTML : '', s, 'hide') });
                                        ids.split(',').each(function (s) { obj = $('direction_'+s); obj ? obj.hide() : ''});
    								}
                            		$('subtree_img'+id) ? $('subtree_img'+id).setAttribute('src', 'images/others/plus.png') : '';
    							}
    						}
    						function updateInformation(el, id, type) {
    							Element.extend(el);
    							type == 'lesson' ? url = 'ask_information.php?lessons_ID='+id : url = 'ask_information.php?courses_ID='+id;
    							el.select('span').each(function (s) {    								
    								if (s.hasClassName('tooltipSpan') && s.empty()) {
    									s.insert(new Element('img').writeAttribute({src:'images/others/progress1.gif'}).addClassName('progress')).setStyle({height:'50px'});
    									new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            onSuccess: function (transport) {
                                            	//alert(transport.responseText);
                                            	s.setStyle({height:'auto'}).update(transport.responseText);
    										}
    									});
    								}
   								});
    						}
    						function filterTree(el) {
    							Element.extend(el);
    							//$$('tr.directionEntry').each(function (s) {if(s.innerHTML.stripTags().toLowerCase().match(el.value.toLowerCase())) {s.show()} else {s.hide()}});
    							var url = '".$options['url']."';
    							url.match(/\?/) ? url = url+'&' : url = url + '?';
    							el.addClassName('loadingImg');
    							new Ajax.Request(url+'filter='+el.value, {
                                    method:'get',
                                    asynchronous:true,
                                    onSuccess: function (transport) {
                                    	$('directions_tree').innerHTML = transport.responseText;
                                    	el.removeClassName('loadingImg');
									}
								});
    						}
                            </script>";

        return $treeString;
    }
    
    
    /**
     * Print paths string
     *
     * This function is used to print the paths to the each direction
     * based on its ancestors.
     * <br/>Example:
     * <code>
     * $paths = $directionsTree -> toPathString();  //$paths is an array with direction ids as keys, and paths as values, for example 'Direction 1 -> Directions 1.1 -> Direction 1.1.1'
     * </code>
     *
     * @param boolean $$includeLeaf Whether leaf direction will be included to the path string
     * @return array The direction paths
     * @since 3.5.0
     * @access public
     */
    public function toPathString($includeLeaf = true) {
        $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));
        foreach ($iterator as $id => $value) {
            $values = array();
            foreach ($this -> getNodeAncestors($id) as $direction) {
                $values[] = $direction['name'];
            }
            if (!$includeLeaf) {
                unset($values[0]);
            }
            $parentsString[$id] = implode('&nbsp;&rarr;&nbsp;', array_reverse($values));
        }

        return $parentsString;
    }

}


?>