<?php
/**

 * File for directions

 *

 * @package eFront

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}
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
 const INVALID_ID = 1052;
 /**

	 * The category is not empty

	 * @since 3.5.4

	 */
 const NOT_EMPTY_CATEGORY = 1052;
 /**

	 * An unspecific error

	 * @since 3.5.0

	 */
 const GENERAL_ERROR = 1099;
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
   eF_deleteTableData("directions", "id=".$value); //Delete Units from database
   eF_updateTableData("lessons", array("directions_ID" => 0), "directions_ID=".$value);
   eF_updateTableData("courses", array("directions_ID" => 0), "directions_ID=".$value);
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
   $result = eF_getTableData("lessons", "id, name", "archive = 0 && directions_ID=".$this['id']);
  } else {
   $directions = new EfrontDirectionsTree();
   $children = $directions -> getNodeChildren($this['id']);
   foreach (new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($children)), array('id')) as $value) {
    $siblings[] = $value;
   }
   $result = eF_getTableData("lessons", "id, name", "archive = 0 && directions_ID in (".implode(",", $siblings).")");
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

	 * @param boolean $subDirections Whether to return subDirections courses as well

	 * @return array An array of course ids/names pairs or EfrontCourse objects

	 * @since 3.5.0

	 * @access public

	 */
 function getCourses($returnObjects = false, $subDirections = false) {
  if (!$subDirections) {
   $result = eF_getTableData("courses", "id, name", "archive = 0 && instance_source = 0 && directions_ID=".$this['id']);
  } else {
   $directionsTree = new EfrontDirectionsTree();
   $children = $directionsTree -> getNodeChildren($this['id']);
   foreach (new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($children)), array('id')) as $value) {
    $siblings[] = $value;
   }
   $result = eF_getTableData("courses", "id, name", "archive = 0 && instance_source = 0 && directions_ID in (".implode(",", $siblings).")");
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
  $newId = eF_insertTableData("directions", $fields);
  $result = eF_getTableData("directions", "*", "id=".$newId); //We perform an extra step/query for retrieving data, sinve this way we make sure that the array fields will be in correct order (forst id, then name, etc)
  $direction = new EfrontDirection($result[0]);
  return $direction;
 }
 /**

	 * Delete category (statically)

	 *

	 * This function is used to delete an existing category.

	 * This function is the same as EfrontDirection :: delete(),

	 * except that it is called statically

	 * <br/>Example:

	 * <code>

	 * try {

	 *   EfrontDirection :: delete(32);					 //32 is the category id

	 * } catch (Exception $e) {

	 *   echo $e -> getMessage();

	 * }

	 * </code>

	 *

	 * @param mixed $category The category id or a category object

	 * @return boolean True if everything is ok

	 * @since 3.5.0

	 * @access public

	 * @static

	 */
 public static function deleteDirection($category) {
  if (!($category instanceof EfrontDirection)) {
   $category = new EfrontDirection($category);
  }
  return $category -> delete();
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
  foreach ($directions as $node) { //Assign previous direction ids as keys to the previousNodes array, which will be used for sorting afterwards
   $nodes[$node['id']] = new EfrontDirection($node); //We convert arrays to array objects, which is best for manipulating data through iterators
  }
  $rejected = array();
  $tree = $nodes;
  $count = 0; //$count is used to prevent infinite loops
  while (sizeof($tree) > 1 && $count++ < 1000) { //We will merge all branches under the main tree branch, the 0 node, so its size will become 1
   foreach ($nodes as $key => $value) {
    if ($value['parent_direction_ID'] == 0 || in_array($value['parent_direction_ID'], array_keys($nodes))) { //If the unit parent is in the $nodes array keys - which are the unit ids- or it is 0, then it is  valid
     $parentNodes[$value['parent_direction_ID']][] = $value; //Find which nodes have children and assign them to $parentNodes
     $tree[$value['parent_direction_ID']][$value['id']] = array(); //We create the "slots" where the node's children will be inserted. This way, the ordering will not be lost
    } else {
     $rejected = $rejected + array($value['id'] => $value); //Append units with invalid parents to $rejected list
     unset($nodes[$key]); //Remove the invalid unit from the units array, as well as from the parentUnits, in case a n entry for it was created earlier
     unset($parentNodes[$value['parent_direction_ID']]);
    }
   }
   if (isset($parentNodes)) { //If the unit was rejected, there won't be a $parentNodes array
    $leafNodes = array_diff(array_keys($nodes), array_keys($parentNodes)); //Now, it's easy to see which nodes are leaf nodes, just by subtracting $parentNodes from the whole set
    foreach ($leafNodes as $leaf) {
     $parent_id = $nodes[$leaf]['parent_direction_ID']; //Get the leaf's parent
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
  foreach ($tree as $key => $value) {
   if ($key != 0) {
    $rejected[$key] = $value;
   }
  }
  if (sizeof($rejected) > 0) { //Append rejected nodes to the end of the tree array, updating their parent/previous information
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
  $result = eF_getTableData("lessons", "*");
  $lessons = array();
  foreach ($result as $value) {
   $lessons[$value['directions_ID']][] = new EfrontLesson($value);
  }
  $result = eF_getTableData("courses", "*");
  $courses = array();
  foreach ($result as $value) {
   $courses[$value['directions_ID']][] = new EfrontCourse($value);
  }
  if (sizeof($directions) == 0) {
   $this -> tree = new RecursiveArrayIterator(array());
   return;
  }
  foreach ($directions as $node) { //Assign previous direction ids as keys to the previousNodes array, which will be used for sorting afterwards
   $nodes[$node['id']] = new EfrontDirection($node); //We convert arrays to array objects, which is best for manipulating data through iterators
   $nodes[$node['id']]['lessons'] = $lessons[$node['id']];
   $nodes[$node['id']]['courses'] = $lessons[$node['id']];
  }
  $rejected = array();
  $tree = $nodes;
  $count = 0; //$count is used to prevent infinite loops
  while (sizeof($tree) > 1 && $count++ < 1000) { //We will merge all branches under the main tree branch, the 0 node, so its size will become 1
   foreach ($nodes as $key => $value) {
    if ($value['parent_direction_ID'] == 0 || in_array($value['parent_direction_ID'], array_keys($nodes))) { //If the unit parent is in the $nodes array keys - which are the unit ids- or it is 0, then it is  valid
     $parentNodes[$value['parent_direction_ID']][] = $value; //Find which nodes have children and assign them to $parentNodes
     $tree[$value['parent_direction_ID']][$value['id']] = array(); //We create the "slots" where the node's children will be inserted. This way, the ordering will not be lost
    } else {
     $rejected = $rejected + array($value['id'] => $value); //Append units with invalid parents to $rejected list
     unset($nodes[$key]); //Remove the invalid unit from the units array, as well as from the parentUnits, in case a n entry for it was created earlier
     unset($parentNodes[$value['parent_direction_ID']]);
    }
   }
   if (isset($parentNodes)) { //If the unit was rejected, there won't be a $parentNodes array
    $leafNodes = array_diff(array_keys($nodes), array_keys($parentNodes)); //Now, it's easy to see which nodes are leaf nodes, just by subtracting $parentNodes from the whole set
    foreach ($leafNodes as $leaf) {
     $parent_id = $nodes[$leaf]['parent_direction_ID']; //Get the leaf's parent
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
  foreach ($tree as $key => $value) {
   if ($key != 0) {
    $rejected[$key] = $value;
   }
  }
  if (sizeof($rejected) > 0) { //Append rejected nodes to the end of the tree array, updating their parent/previous information
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
 public function removeNode($node) {}
 /**

	 * Return an array of lesson ids, corresponding to the lessons of this categories tree

	 *

	 * @param array $lessons The lessons list

	 * @return array The lesson ids list

	 * @since 3.6.3

	 * @access public

	 */
 public function getLessonsList($lessons = array()) {
  $lessonsList = array();
  $iterator = $this -> initializeIterator(false, $lessons, $courses);
  foreach ($iterator as $key => $value) {
   foreach($value -> offsetGet('lessons') as $id) {
    $lessonsList[] = $id;
   }
  }
  return $lessonsList;
 }
 /**

	 * Print an HTML representation of the directions tree

	 *

	 * This function is used to print an HTML representation of the HTML tree

	 * <br/>Example:

	 * <code>

	 * $directionsTree -> toHTML();						 //Print directions tree

	 * </code>

	 * Possible options are:

	 * - lessons_link			//a value of '#user_type#' inside the url will be replaced with the user type

	 * - courses_link			//a value of '#user_type#' inside the url will be replaced with the user typed

	 * - tooltip

	 * - search					//display the search box (true/false)

	 * - tree_tools				//Whether to display the top div with tree tools, show/hide and search (true/false)

	 * - url					//A url to search ajax functions for. defaults to current url

	 * - collapse				//Whether to start with categories collapsed

	 * - buy_link				//Whether to display "buy" (add to cart) links

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
  $options = $this -> parseTreeOptions($options);
  $lessons = $this -> parseTreeLessons($lessons);
  $courses = $this -> parseTreeCourses($courses);
  $iterator = $this -> initializeIterator($iterator, $lessons, $courses, $options);
  $current = $iterator -> current();
  $treeString = '
      <div id = "directions_tree">';
  list($display, $display_lessons, $imageString, $classString) = $this -> getTreeDisplaySettings($options);
  $lessonsString = $coursesString = '';
  while ($iterator -> valid()) {
   $lessonsString = $this -> printCategoryLessons($iterator, $display_lessons, $options, $lessons);
   $coursesString = $this -> printCategoryCourses($iterator, $display, $userInfo, $options, $courses, $lessons);
   if ($lessonsString || $coursesString) {
    $treeString .= $this -> printCategoryTitle($iterator, $display, $imageString, $classString);
    $treeString .= $lessonsString.$coursesString.'
       </table>';
   }
   $iterator -> next();
  }
  if ($options['tree_tools']) {
   $treeString = $this -> printTreeTools($options).$treeString; //This is put at the end, so that $this -> hasLessonsAsStudent is populated
  }
  return $treeString;
 }
 private function parseTreeOptions($options) {
  //!isset($options['show_cart'])   ? $options['show_cart']   = false : null;
  //!isset($options['information']) ? $options['information'] = false : null;
  !isset($options['lessons_link']) ? $options['lessons_link'] = false : null;
  !isset($options['courses_link']) ? $options['courses_link'] = false : null;
  !isset($options['tooltip']) ? $options['tooltip'] = true : null;
  !isset($options['search']) ? $options['search'] = false : null;
  !isset($options['catalog']) ? $options['catalog'] = false : null;
  !isset($options['tree_tools']) ? $options['tree_tools'] = true : null;
  !isset($options['url']) ? $options['url'] = $_SERVER['REQUEST_URI'] : null; //Pay attention since REQUEST_URI is empty if accessing index.php with the url http://localhost/
  !isset($options['course_lessons']) ? $options['course_lessons'] = true : null;
  return $options;
 }
 private function parseTreeLessons($lessons) {
  if ($lessons === false) { //If a lessons list is not specified, get all active lessons
   $result = eF_getTableData("lessons", "*", "archive = 0 && active=1", "name"); //Get all lessons at once, thus avoiding looping queries
   foreach ($result as $value) {
    $lessons[$value['id']] = new EfrontLesson($value); //Create an array of EfrontLesson objects
   }
  }
  return $lessons;
 }
 private function parseTreeCourses($courses) {
  if ($courses === false) { //If a courses list is not specified, get all active courses
   $result = eF_getTableData("courses", "*", "archive = 0 && active=1", "name"); //Get all courses at once, thus avoiding looping queries
   foreach ($result as $value) {
    $courses[$value['id']] = new EfrontCourse($value); //Create an array of EfrontCourse objects
   }
  }
  return $courses;
 }
 private function initializeIterator($iterator, $lessons, $courses, $options) {
  if (!$iterator) {
   $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));
  }
  $iterator = $this -> filterOutEmptyCategories($iterator, $lessons, $courses, $options);
  $iterator = new EfrontNodeFilterIterator($iterator, array('hasNodes' => true)); //Filter in only tree nodes that have the 'hasNodes' attribute
  $iterator -> rewind();
  return $iterator;
 }
 private function filterOutEmptyCategories($iterator, $lessons, $courses, $options) {
  $directionsLessons = array();
  foreach ($lessons as $id => $lesson) {
   if ($options['catalog'] && !$lesson -> lesson['show_catalog']) { //Remove inactive lessons
    unset($lessons[$id]);
   } elseif (!$lesson -> lesson['course_only']) { //Lessons in courses will be handled by the course's display method, so remove them from the list
    $directionsLessons[$lesson -> lesson['directions_ID']][] = $id; //Create an intermediate array that maps lessons to directions
   }
  }
  $directionsCourses = array();
  foreach ($courses as $id => $course) {
   $displayCatalogEntry[$course -> course['id']] = $course -> shouldDisplayInCatalog();
   if ($options['catalog'] && !$displayCatalogEntry[$course -> course['id']]) { //Remove inactive courses
    unset($courses[$id]);
   } else {
    if ($courses[$id] -> course['instance_source']) {
     $instanceSource = new EfrontCourse($courses[$id] -> course['instance_source']);
     $directionsCourses[$instanceSource -> course['directions_ID']][] = $id; //Course instances don't have a directions on their own
    } else {
     $directionsCourses[$course -> course['directions_ID']][] = $id; //Create an intermediate array that maps courses to directions
    }
   }
  }
  //We need to calculate which categories will be displayed. We will keep only categories that have lessons or courses and their parents. In order to do so, we traverse the categories tree and set the 'hasNodes' attribute to the nodes that will be kept
  foreach ($iterator as $key => $value) {
   if (isset($directionsLessons[$value['id']]) || isset($directionsCourses[$value['id']])) {
    $count = $iterator -> getDepth();
    $value['hasNodes'] = true;
    isset($directionsLessons[$value['id']]) ? $value['lessons'] = $directionsLessons[$value['id']] : null; //Assign lessons ids to the direction
    isset($directionsCourses[$value['id']]) ? $value['courses'] = $directionsCourses[$value['id']] : null; //Assign courses ids to the direction
    while ($count) {
     $node = $iterator -> getSubIterator($count--);
     $node['hasNodes'] = true; //Mark "keep" all the parents of the node
    }
   }
  }
  return $iterator;
 }
 private function printTreeTools($options) {
  if (!isset($_COOKIE['display_all_courses'])) {
   setcookie('display_all_courses', 0);
  }
  $treeString = '
    <div style = "padding-top:8px;padding-bottom:8px">
     '.($options['search'] ? '<span style = "float:right;"><span style = "vertical-align:middle">'._SEARCH.': <input type = "text" style = "vertical-align:middle" onKeyPress = "if (event.keyCode == 13) {filterTree(this, \''.$options['url'].'\')}"></span></span>' : '').'
     <a href = "javascript:void(0)" onclick = "showAll()" >'._EXPANDALL.'</a> / <a href = "javascript:void(0)" onclick = "hideAll()">'._COLLAPSEALL.'</a>';
  if ($options['only_progress_link'] && $this -> hasLessonsAsStudent) {
   $treeString .= '
     |
     <span>'._CURRENTLYSHOWING.':</span>
     <select onchange = "setCookie(\'display_all_courses\', this.options[this.options.selectedIndex].value);location=location">
      <option value = "0">'._MATERIALINPROGRESS.'</option>
      <option value = "1" '.($_COOKIE['display_all_courses'] == '1' ? 'selected' : '').'>'._ALLMATERIAL.'</option>
     </select>
    </div>';
  }
  return $treeString;
 }
 private function printProgressBar($treeLesson, $roleBasicType) {
  $treeString = '';
  if ($roleBasicType == 'student' && $treeLesson -> lesson['completed']) { //Show the "completed" mark
   $treeLesson -> lesson['completed'] ? $icon = 'success' : $icon = 'semi_success';
    $treeString .= '
     <td class = "lessonProgress">
      <span class = "progressNumber" style = "width:50px;">&nbsp;</span>
      <span class = "progressBar" style = "width:50px;text-align:center"><img src = "images/16x16/'.$icon.'.png" alt = "'._LESSONCOMPLETE.'" title = "'._LESSONCOMPLETE.'" /></span>
      &nbsp;&nbsp;
     </td>';
  } elseif ($roleBasicType == 'student') { //Show the progress bar
   if ($treeLesson->options['show_percentage'] != 0) {
    $treeString .= '
    <td class = "lessonProgress">
     <span class = "progressNumber" style = "width:50px;">'.$treeLesson -> lesson['overall_progress']['percentage'].'%</span>
     <span class = "progressBar" style = "width:'.($treeLesson -> lesson['overall_progress']['percentage'] / 2).'px;">&nbsp;</span>
     &nbsp;&nbsp;
    </td>';
   } else {
    $treeString .= '
     <td class = "lessonProgress">&nbsp;
     </td>';
   }
  } else {
   $treeString .= '<td style = "width:1px;padding-bottom:2px;"></td>';
  }
  if ($roleBasicType == 'student') {
   $this -> hasLessonsAsStudent = true;
  }
  return $treeString;
 }
 private function printLessonBuyLink($treeLesson, $options) {
  $treeString = '';
  if (isset($options['buy_link']) && $options['buy_link'] && (!isset($treeLesson -> lesson['has_lesson']) || !$treeLesson -> lesson['has_lesson']) && (!isset($treeLesson -> lesson['reached_max_users']) || !$treeLesson -> lesson['reached_max_users']) && (!isset($_SESSION['s_type']) || $_SESSION['s_type'] != 'administrator')) {
   $treeString .= '
    <span class = "buyLesson">
     <span onclick = "addToCart(this, '.$treeLesson -> lesson['id'].', \'lesson\')">'.$this -> showLessonPrice($treeLesson).'</span>
     <img class = "ajaxHandle" src = "images/16x16/shopping_basket_add.png" alt = "'._ADDTOCART.'" title = "'._ADDTOCART.'" onclick = "addToCart(this, '.$treeLesson -> lesson['id'].', \'lesson\')">
    </span>';
  }
  return $treeString;
 }
 private function showLessonPrice($lesson) {
  if ($lesson -> lesson['price']) {
   $lesson -> lesson['price'] ? $priceString = formatPrice($lesson -> lesson['price'], array($lesson -> options['recurring'], $lesson -> options['recurring_duration']), true) : $priceString = false;
   return $priceString;
  } else {
   $priceString = _FREELESSON;
   return $priceString;
  }
 }
 private function showCoursePrice($course) {
  if ($course -> course['price']) {
   $course -> course['price'] ? $priceString = formatPrice($course -> course['price'], array($course -> options['recurring'], $course -> options['recurring_duration']), true) : $priceString = false;
   return $priceString;
  } else {
   $priceString = _FREECOURSE;
   return $priceString;
  }
 }
 private function printCourseLinks($treeCourse, $options, $roleBasicType) {
  $treeString = '';
  $courseLink = $options['courses_link'];
  $href = str_replace("#user_type#", $roleBasicType, $courseLink).$treeCourse -> shouldDisplayInCatalog();
  if (isset($options['buy_link'])) {
   if ($options['buy_link'] && (!isset($treeCourse -> course['has_instances']) || !$treeCourse -> course['has_instances']) && (!isset($treeCourse -> course['has_course']) || !$treeCourse -> course['has_course']) && (!isset($treeCourse -> course['reached_max_users']) || !$treeCourse -> course['reached_max_users']) && (!isset($_SESSION['s_type']) || $_SESSION['s_type'] != 'administrator')) {
    $treeString .= '
        <span class = "buyLesson">
         <span onclick = "addToCart(this, '.$treeCourse -> course['id'].', \'course\')">'.$this -> showCoursePrice($treeCourse).'</span>
         <img class = "ajaxHandle" src = "images/16x16/shopping_basket_add.png" alt = "'._ADDTOCART.'" title = "'._ADDTOCART.'" onclick = "addToCart(this, '.$treeCourse -> course['id'].', \'course\')">
        </span>';
    $hasInstancesClass = 'boldFont';
   } else {
    $instanceString .= '
        <img class = "ajaxHandle" src = "images/16x16/arrow_right.png" alt = "'._INFORMATION.'" title = "'._INFORMATION.'" onclick = "location=\''.$href.'\'">';
/*

				$treeString .= '

							<span class = "buyLesson">

								&nbsp;<a href = '.$href.'><img class = "handle" src = "images/16x16/arrow_right.png" alt = "'._INFORMATION.'" title = "'._INFORMATION.'"></a>

							</span>';

*/
   }
  }
  if (!isset($treeCourse -> course['from_timestamp']) || $treeCourse -> course['from_timestamp']) { //from_timestamp in user status means that the user's status in the course is not 'pending'
   $classNames = array();
   if ($options['tooltip'] && $GLOBALS['configuration']['disable_tooltip'] != 1) {
    $treeString .= '<a href = "'.($courseLink ? $href : 'javascript:void(0)').'" class = "'.$hasInstancesClass.' info '.implode(" ", $classNames).'" onmouseover = "updateInformation(this, '.$treeCourse -> course['id'].', \'course\')">'.$treeCourse -> course['name'].'
          <img class = "tooltip" border = "0" src = "images/others/tooltip_arrow.gif" height = "15" width = "15"/>
          <span class = "tooltipSpan"></span>
         </a>';
   } else {
    $courseLink ? $treeString .= '<a href = "'.str_replace("#user_type#", $roleBasicType, $courseLink).$treeCourse -> course['id'].'" class = "'.$hasInstancesClass.'">'.$treeCourse -> course['name'].'</a>' : $treeString .= $treeCourse -> course['name'];
   }
  } else {
   $treeString .= '<a href = "javascript:void(0)" class = "'.$hasInstancesClass.' inactiveLink" title = "'._CONFIRMATIONPEDINGFROMADMIN.'">'.$treeCourse -> course['name'].'</a>';
  }
  $treeString .= $instanceString;
  return $treeString;
 }
 private function printLessonLink($treeLesson, $options, $roleBasicType) {
  $treeString = '';
  if (!$roleBasicType || $treeLesson -> lesson['active_in_lesson']) { //active_in_lesson (equals from_timestamp in users_to_lessons) in user status means that the user's status in the lesson is not 'pending'
   $classNames = array();
   $lessonLink = $options['lessons_link'];
   if ($roleBasicType == 'student' && (($treeLesson -> lesson['from_timestamp'] && $treeLesson -> lesson['from_timestamp'] > time()) || ($treeLesson -> lesson['to_timestamp'] && $treeLesson -> lesson['to_timestamp'] < time()))) { //here, from_timestamp and to_timestamp refer to the lesson periods
    $lessonLink = false;
    $classNames[] = 'inactiveLink';
   }
   if ($options['tooltip'] && $GLOBALS['configuration']['disable_tooltip'] != 1) {
    $treeString .= '<a href = "'.($lessonLink ? str_replace("#user_type#", $roleBasicType, $lessonLink).$treeLesson -> lesson['id'] : 'javascript:void(0)').'" class = "info '.implode(" ", $classNames).'" onmouseover = "updateInformation(this, '.$treeLesson -> lesson['id'].', \'lesson\')">'.$treeLesson -> lesson['name'].'
             <img class = "tooltip" border = "0" src = "images/others/tooltip_arrow.gif" height = "15" width = "15"/>
             <span class = "tooltipSpan"></span>
            </a>';
   } else {
    $lessonLink ? $treeString .= '<a href = "'.str_replace("#user_type#", $roleBasicType, $lessonLink).$treeLesson -> lesson['id'].'">'.$treeLesson -> lesson['name'].'</a>' : $treeString .= $treeLesson -> lesson['name'];
   }
  } else {
   $treeString .= '<a href = "javascript:void(0)" class = "inactiveLink" title = "'._CONFIRMATIONPEDINGFROMADMIN.'">'.$treeLesson -> lesson['name'].'</a>';
  }
  return $treeString;
 }
 private function printCategoryTitle($iterator, $display, $imageString, $classString) {
  $treeString = '';
  $current = $iterator -> current();
  $children = array(); //The $children array is used so that when collapsing a direction, all its children disappear as well
  foreach (new EfrontNodeFilterIterator(new ArrayIterator($this -> getNodeChildren($current), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
   $children[] = $key;
  }
  $treeString .= '
     <table class = "directionsTable" id = "direction_'.$current['id'].'" '.($iterator -> getDepth() >= 1 ? $display : '').'>
      <tr class = "lessonsList">
       <td class = "listPadding"><div style = "width:'.(20 * $iterator -> getDepth()).'px;">&nbsp;</div></td>
       <td class = "listToggle">';
  if ($iterator -> getDepth() >= 1) {
   $treeString .= '<img id = "subtree_img'.$current['id'].'" class = "visible" src = "images/16x16/navigate_up.png" alt = "'._CLICKTOTOGGLE.'" title = "'._CLICKTOTOGGLE.'" onclick = "Element.extend(this);showHideDirections(this, \''.implode(",", $children).'\', \''.$current['id'].'\', (this.hasClassName(\'visible\')) ? \'hide\' : \'show\');">';
  } else {
   $treeString .= '<img id = "subtree_img'.$current['id'].'" '.$classString.' src = "images/16x16/navigate_'.$imageString.'.png" alt = "'._CLICKTOTOGGLE.'" title = "'._CLICKTOTOGGLE.'" onclick = "Element.extend(this);showHideDirections(this, \''.implode(",", $children).'\', \''.$current['id'].'\', (this.hasClassName(\'visible\')) ? \'hide\' : \'show\');">';
  }
  $treeString .= '</td>
       <td class = "listIcon">
        <img src = "images/32x32/categories.png" >
        <span style = "display:none" id = "subtree_children_'.$current['id'].'">'.implode(",", $children).'</span>
       </td>
       <td class = "listTitle"><span class = "listName">'.$current['name'].'</span></td>
      </tr>';
  return $treeString;
 }
 private function getTreeDisplaySettings($options) {
  if (isset($options['collapse']) && $options['collapse'] == 2) {
   $display = '';
   $display_lessons = 'style = "display:none"';
   $imageString = 'down';
   $classString = '';
  } elseif (isset($options['collapse']) && $options['collapse'] == 1) {
   $display = 'style = "display:none"';
   $display_lessons = 'style = "display:none"';
   $imageString = 'down';
   $classString = '';
  } else {
   $display = '';
   $display_lessons = '';
   $imageString = 'up';
   $classString = ' class = "visible" ';
  }
  return array($display, $display_lessons, $imageString, $classString);
 }
 private function printCategoryLessons($iterator, $display_lessons, $options, $lessons) {
  $roles = EfrontLessonUser :: getLessonsRoles();
  $roleNames = EfrontLessonUser :: getLessonsRoles(true);
  $treeString = $lessonsString = '';
  $current = $iterator -> current();
  foreach ($current -> offsetGet('lessons') as $lessonId) {
   $treeLesson = $lessons[$lessonId];
   if (isset($treeLesson -> lesson['user_type']) && $treeLesson -> lesson['user_type']) {
    $roleInLesson = $treeLesson -> lesson['user_type'];
    $roleBasicType = $roles[$roleInLesson]; //Indicates that this is a catalog with user data
   } else {
    $roleBasicType = null;
   }
   if ($_COOKIE['display_all_courses'] == '1' || $roleBasicType != 'student' || (!$treeLesson -> lesson['completed'] && (is_null($treeLesson -> lesson['remaining']) || $treeLesson -> lesson['remaining'] > 0))) {
    $lessonsString .= '<tr class = "directionEntry">';
    if ($roleBasicType) {
     $lessonsString .= $this -> printProgressBar($treeLesson, $roleBasicType);
    }
    $lessonsString .= '<td>';
    $lessonsString .= $this -> printLessonBuyLink($treeLesson, $options);
    $lessonsString .= $this -> printLessonLink($treeLesson, $options, $roleBasicType);
    $lessonsString .= (isset($treeLesson -> lesson['different_role']) && $treeLesson -> lesson['different_role'] ? '&nbsp;<span class = "courseRole">('.$roleNames[$treeLesson -> lesson['user_type']].')</span>' : '').'
           '.(isset($treeLesson -> lesson['remaining']) && !is_null($treeLesson -> lesson['remaining']) && $roles[$treeLesson -> lesson['user_type']] == 'student' ? '<span class = "">('.eF_convertIntervalToTime($treeLesson -> lesson['remaining'], true).' '.mb_strtolower(_REMAINING).')</span>' : '').'
          </td>
         </tr>';
   }
  }
  if (isset($current['lessons']) && sizeof($current['lessons']) > 0 && $lessonsString) {
   if (isset($options['collapse']) && $options['collapse'] == 2) {
    $treeString .= '
       <tr id = "subtree'.$current['id'].'" name = "default_visible" '. $display_lessons.'>';
   } else {
    $treeString .= '
       <tr id = "subtree'.$current['id'].'" name = "default_visible" '.($iterator -> getDepth() >= 1 ? '' : $display_lessons).'>';
   }
   $treeString .= ' <td></td>
       <td class = "lessonsList_nocolor">&nbsp;</td>
       <td colspan = "2">
        <table width = "100%">'.$lessonsString.'
        </table>
        </td></tr>';
  }
  return $treeString;
 }
 private function printCategoryCourses($iterator, $display, $userInfo, $options, $courses, $lessons) {
  $roles = EfrontLessonUser :: getLessonsRoles();
  $roleNames = EfrontLessonUser :: getLessonsRoles(true);
  $treeString = '';
  $current = $iterator -> current();
  if (isset($current['courses']) && sizeof($current['courses']) > 0) {
   $coursesTreeString = '';
   foreach ($current -> offsetGet('courses') as $courseId) {
    $treeCourse = $courses[$courseId];
    if (isset($treeCourse -> course['user_type']) && $treeCourse -> course['user_type']) {
     $roleInCourse = $treeCourse -> course['user_type'];
     $roleBasicType = $roles[$roleInCourse]; //Indicates that this is a catalog with user data
     if ($roleBasicType == 'student') {
      $this -> hasLessonsAsStudent = true;
     }
    } else {
     $roleBasicType = null;
    }
    if ($_COOKIE['display_all_courses'] == '1' || $roleBasicType != 'student' || (!$treeCourse -> course['completed'] && (is_null($treeCourse -> course['remaining']) || $treeCourse -> course['remaining'] > 0))) {
     if ($options['course_lessons']) {
      $coursesTreeString .= $treeCourse -> toHTML($lessons, $options);
     } else {
      $coursesTreeString .= '
      <table width = "100%">
       <tr class = "directionEntry">
        <td>';
      $coursesTreeString .= $this -> printCourseLinks($treeCourse, $options, $roleBasicType);
      $coursesTreeString .= '
        </td>
       </tr>
      </table>';
     }
    }
   }
   if ($coursesTreeString) {
    $treeString .= '
       <tr id = "subtree'.$current['id'].'" name = "default_visible" '.$display.'>
        <td></td>
        <td class = "lessonsList_nocolor">&nbsp;</td>
        <td colspan = "2">
        '.$coursesTreeString.'
        </td>
       </tr>';
   }
  }
  return $treeString;
 }
 /* Return an array to be inputed as the contents of a select item or

	 * an HTML select object with directions->courses->lessons

	 *

	 * This function is used to create a select with directions, lessons and courses

	 * categorized properly under a select item

	 *

	 * The values of the returned array of HTML select are different but always start

	 * with the type of educational entity, i.e. "direction_", "course_" and "lesson_"

	 * and finish with the id of that entity "_<id>". The inbetween parts differ

	 *

	 * The categorization display is the following

	 * direction D

	 * - subdirection SuBD

	 * -- course C1 in SubD

	 * ---- lesson in C1

	 * ---- lesson in C1

	 * - course C2 in D

	 * -- lesson in C2

	 * -- lesson in C2

	 * - lesson in D

	 *

	 * <br/>Example:

	 * <code>

	 * $directionsTree -> toSelect();						 //Print directions tree

	 * </code>

	 *

	 * @param boolean $returnClassedHTML return the HTML select object rather than the array - different colors denote different educational entities

	 * @param boolean $includeSkillGaps the skill gap test questions will be included

	 * @param boolean $showQuestions defines whether to show the number of questions of each lesson

	 * @param RecursiveIteratorIterator $iterator An optional custom iterator

	 * @param array $lessons An array of EfrontLesson Objects

	 * @param array $courses An array of EfrontCourse Objects

	 * @return array to be used or string for The HTML version of the tree

	 * @since 3.5.2

	 * @access public

	 */
 public function toSelect($returnClassedHTML = false, $includeSkillGaps = false, $showQuestions = false, $iterator = false, $lessons = false, $courses = false) {
  if (!$iterator) {
   $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));
  }
  if ($lessons === false) { //If a lessons list is not specified, get all active lessons
   if ($showQuestions) {
    $result = eF_getTableData("lessons l JOIN questions q ON q.lessons_ID = l.id", "l.id, l.name, count(q.id) as questions", "q.type <> 'raw_text' AND l.archive = 0 AND l.active=1", "" , "l.name");
   } else {
    $result = eF_getTableData("lessons", "*", "archive = 0 && active=1"); //Get all lessons at once, thus avoiding looping queries
   }
   foreach ($result as $value) {
    $value['name'] = str_replace("'","&#039;",$value['name']);
    $lessons[$value['id']] = new EfrontLesson($value); //Create an array of EfrontLesson objects
   }
  }
  $directionsQuestions = array();
  $directionsLessons = array();
  foreach ($lessons as $id => $lesson) {
   if (!$lesson -> lesson['active']) { //Remove inactive lessons
    unset($lessons[$id]);
   } elseif (!$lesson -> lesson['course_only']) { //Lessons in courses will be handled by the course's display method, so remove them from the list
    $directions_ID = $lesson -> lesson['directions_ID'];
    $directionsLessons[$directions_ID][] = $id; //Create an intermediate array that maps lessons to directions
    if ($showQuestions) {
     if (isset($directionsQuestions[$directions_ID])) {
      $directionsQuestions[$directions_ID] += $lesson -> lesson['questions'];
     } else {
      $directionsQuestions[$directions_ID] = $lesson -> lesson['questions'];
     }
    }
   }
  }
  if ($courses === false) { //If a courses list is not specified, get all active courses
   if ($showQuestions) {
    $resultQuestions = eF_getTableData("courses c JOIN lessons_to_courses lc ON lc.courses_ID = c.id JOIN questions q ON q.lessons_ID = lc.lessons_ID", "c.id, count(q.id) as questions", "q.type <> 'raw_text' AND c.archive = 0 AND c.active=1", "" , "c.name");
    $coursesQuestions = array();
    foreach ($resultQuestions as $resultQs) {
     $coursesQuestions[$resultQs['id']] = $resultQs['questions'];
    }
   }
   $result = eF_getTableData("courses", "*", "archive = 0 AND active=1"); //Get all courses at once, thus avoiding looping queries
   foreach ($result as $value) {
    $value['name'] = str_replace("'","&#039;",$value['name']);
    $value['questions'] = ($coursesQuestions[$value['id']]!="")?$coursesQuestions[$value['id']]:0; // 0 + to cast empty values to 0
    $courses[$value['id']] = new EfrontCourse($value); //Create an array of EfrontCourse objects
   }
  }
  $directionsCourses = array();
  foreach ($courses as $id => $course) {
   if (!$course -> course['active']) { //Remove inactive courses
    unset($courses[$id]);
   } else {
    $directions_ID = $course -> course['directions_ID'];
    $directionsCourses[$directions_ID][] = $id; //Create an intermediate array that maps courses to directions
    if ($showQuestions) {
     if (isset($directionsQuestions[$directions_ID])) {
      $directionsQuestions[$directions_ID] += $course -> course['questions'];
     } else {
      $directionsQuestions[$directions_ID] = $course -> course['questions'];
     }
    }
   }
  }
  //We need to calculate which directions will be displayed. We will keep only directions that have lessons or courses and their parents. In order to do so, we traverse the directions tree and set the 'hasNodes' attribute to the nodes that will be kept
  foreach ($iterator as $key => $value) {
   if (isset($directionsLessons[$value['id']]) || isset($directionsCourses[$value['id']])) {
    $count = $iterator -> getDepth();
    $value['hasNodes'] = true;
    isset($directionsLessons[$value['id']]) ? $value['lessons'] = $directionsLessons[$value['id']] : null; //Assign lessons ids to the direction
    isset($directionsCourses[$value['id']]) ? $value['courses'] = $directionsCourses[$value['id']] : null; //Assign courses ids to the direction
    while ($count) {
     $node = $iterator -> getSubIterator($count--);
     $node['hasNodes'] = true; //Mark "keep" all the parents of the node
    }
   }
  }
  $iterator = new EfrontNodeFilterIterator($iterator, array('hasNodes' => true)); //Filter in only tree nodes that have the 'hasNodes' attribute
  $iterator -> rewind();
  $current = $iterator -> current();
    // pr($current);
  $current_level_father = 0;
  $treeArray = array();
  if ($includeSkillGaps) {
   $treeArray['lesson_0'] = _SKILLGAPTESTS;
  }
  $offset = "";
  while ($iterator -> valid()) {
   $children = array(); //The $children array is used so that when collapsing a direction, all its children disappear as well
   foreach (new EfrontNodeFilterIterator(new ArrayIterator($this -> getNodeChildren($current), RecursiveIteratorIterator :: SELF_FIRST)) as $key => $value) {
    $children[] = $key;
   }
   if ($offset != "") {
    $treeArray['direction_' . $current['id']] = str_replace("'", "&#039;", $offset . " " . $current['name']);
   } else {
    $treeArray['direction_' . $current['id']] = str_replace("'", "&#039;", $current['name']);
   }
   if (sizeof($current['lessons']) > 0) {
    foreach ($current -> offsetGet('lessons') as $lessonId) {
     $treeArray['lesson_' . $current['id']. '_' . $lessonId] = str_replace("'", "&#039;", $offset . "- ". $lessons[$lessonId] -> lesson['name']);
    }
   }
   if (sizeof($current['courses']) > 0) {
    foreach ($current -> offsetGet('courses') as $courseId) {
     $coursesArray = $courses[$courseId] -> toSelect();
     $first = 1;
     foreach ($coursesArray as $courseId => $courseName) {
      // The first result is the name of the course - the rest lesson names
      // We need this distinction to have different keys (starting with course_ or lesson_ correctly
      if ($first) {
       $treeArray['course_' . $current['id']. '_' . $courseId . "_" . $courseId] = str_replace("'", "&#039;", $offset . "-". $courseName);
       $first = 0;
      } else {
       $treeArray['lesson_' . $current['id']. '_' . $courseId . "_" . $courseId] = str_replace("'", "&#039;", $offset . "-". $courseName);
      }
     }
    }
   }
   $iterator -> next();
   $current = $iterator -> current();
     // $current
   if ($current['parent_direction_ID'] != $current_level_father) {
    $offset .= "-";
    $current_level_father = $current['parent_direction_ID'];
   }
  }
  if ($returnClassedHTML) {
   $htmlString = '<select id= "educational_criteria_row" name ="educational_criteria_row" onchange="createQuestionsSelect(this)" mySelectedIndex = "0">';
   if ($showQuestions) {
    $result = eF_getTableData("questions", "lessons_ID, count(lessons_ID) as quests", "type <> 'raw_text'", "", "lessons_ID");
    $lessonQuestions = array();
    foreach ($result as $lesson) {
     if ($lesson['quests'] > 0) {
      $lessonQuestions[$lesson['lessons_ID']] = $lesson['quests'];
     }
    }
   }
   foreach($treeArray as $key => $value) {
    $extras = " ";
    $htmlString .= '<option';
    if (strpos($key, "direction_") === 0) {
     $directions_ID = strrchr($key,"_");
     $htmlString .= ' value = "direction'. $directions_ID . '" style="background-color:maroon; color:white"';
     $course_ID = substr($directions_ID,1);
     if ($showQuestions) {
      $questions = $directionsQuestions[$directions_ID];
      if ($questions) {
       $extras = ' (' . $questions .')';
      } else {
       $extras = ' (0)';
      }
     }
    } else if (strpos($key, "course_") === 0) {
     $course_ID = strrchr($key,"_");
     $htmlString .= ' value = "course'. $course_ID . '" style="background-color:green; color:white"';
     $course_ID = substr($course_ID,1);
     if ($showQuestions) {
      $questions = $courses[$course_ID] -> course['questions'];
      if ($questions) {
       $extras = ' (' . $questions .')';
      } else {
       $extras = ' (0)';
      }
     }
    } else {
     $htmlString .= ' value = "lesson'. strrchr($key,"_") . '" ';
     if ($showQuestions) {
      $lessonId = substr(strrchr($key,"_"),1);
      if ($showQuestions) {
       if ($lessonQuestions[$lessonId]) {
        $extras .= "(" . $lessonQuestions[$lessonId]. ")";
       } else {
        $extras .= "(0)";
       }
      }
     }
    }
    $htmlString .= ">" . $value . $extras . "</option>";
   }
   $htmlString .= "</select>";
   // If no lessons or anything is found, then an empty select or array should be returned
   return $htmlString;
  }
  return $treeArray;
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
 public function toPathString($includeLeaf = true, $onlyActive = false) {
  if ($onlyActive) {
   $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1));
  } else {
   $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($this -> tree), RecursiveIteratorIterator :: SELF_FIRST));
  }
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
