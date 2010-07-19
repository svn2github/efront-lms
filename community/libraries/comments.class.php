<?php
/**

* comments Class file

*

* @package eFront

* @version 3.6

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * 

 * @author user

 *

 */
class comments extends EfrontEntity
{
    /**

     * The comments properties

     * 

     * @since 3.6.0

     * @var array

     * @access public

     */
    public $comments = array();
    /**

     * Create comments

     * 

     * This function is used to create comments

     * <br>Example:

     * <code>

	 * $comments = comments :: create($fields));			//$fields is an array of data for the comment

     * </code>

     * 

     * @param $fields An array of data

     * @return comments The new object

     * @since 3.6.0

     * @access public

     * @static

     */
    public static function create($fields = array()) {
        $fields = array("data" => $fields['data'],
                        "users_LOGIN" => $fields['users_LOGIN'],
                        "content_ID" => $fields['content_ID'],
                        "private" => $fields['private'],
                        "timestamp" => isset($fields['timestamp']) && $fields['timestamp'] ? $fields['timestamp'] : time(),
                        "active" => !isset($fields['active']) || $fields['active'] ? 1 : 0);
        $newId = eF_insertTableData("comments", $fields);
        $comments = new comments($newId);
  $sourceUnit = new EfrontUnit($fields['content_ID']);
  EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_COMMENT_WRITING, "lessons_ID" => $sourceUnit['lessons_ID'], "entity_ID" => $fields['content_ID'], "entity_name" => $sourceUnit['name']));
        return $comments;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
     $form -> addElement('textarea', 'data', _COMMENT, 'class = "simpleEditor inputTextarea"');
     $form -> addElement('advcheckbox', 'private', _PRIVATE, null, 'class = "inputCheckbox"', array(0, 1));
     $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
     $form -> setDefaults(array('data' => $this -> comments['data'],
                                'private' => $this -> comments['private']));
        return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form, $values = false) {
        if (!$values) {
            $values = $form -> exportValues();
        }
        if (isset($_GET['edit'])) {
            $this -> comments["data"] = $values['data'];
            $this -> comments["private"] = $values['private'];
            $this -> persist();
        } else {
            $comments = self :: create($values);
            $this -> comments = $comments;
        }
    }
    /**

	 * Get comments

	 *

	 * This function gets the lesson comments. It returns an array holding the name of the lesson where the comment was put,

	 * the comment id, the comment itself (which is put as a title on the lesson name link), and finally the timestamp and the

	 * user that posted it. IF a lesson id is not specified, then comments for the current lesson are returned.If a login is

	 * specified, then only comments that the specified user has posted are returned. If a content id is specified, then only

	 * comments of this unit are displayed.

	 * <br/>Example:

	 * <code>

	 * $comments = comments :: getComments();

	 * print_r($comments);

	 * //Returns:

	 *Array

	 *(

	 *    [0] => Array

	 *        (

	 *            [id] => 3

	 *            [data] => This is a comment

	 *            [users_LOGIN] => admin

	 *            [timestamp] => 1125751731

	 *            [content_name] => unit 1.2

	 *            [content_id] => 145

	 *            [content_type] => theory

	 *        )

	 *)

	 * </code>

	 * @param mixed $lessons_ID The lesson id or false

	 * @param string $login The user login or false

	 * @param mixed $content_ID The unit id to return its comments or false

	 * @param mixed $limit The results limit or false

	 * @param string $private false for returning only public comments, true for everything 

	 * @return array The comments array

	 * @since 3.6.0

	 * @access public

	 * @static

     */
    public static function getComments($lesson = false, $user = false, $content_ID = false, $limit = false, $private = true) {
        if ($lesson instanceOf EfrontLesson) {
            $lesson = $lesson -> lesson['id'];
        } else if (!eF_checkParameter($lesson, 'id')) {
            $lesson = $_SESSION['s_lessons_ID'];
        }
        if ($user instanceOf EfrontUser) {
            $user = $user -> user['login'];
        } elseif (!eF_checkParameter($user, 'login')) {
            $user = '';
        }
        if ($user) {
            $login_str = " AND cm.users_LOGIN='$user'";
        } else {
            $login_str = '';
        }
        if ($content_ID && eF_checkParameter($content_ID, 'id')) {
            $content_ID_str = ' AND cn.id='.$content_ID;
        } else {
            $content_ID_str = '';
        }
        if ($limit && eF_checkParameter($limit, 'uint')) {
            $limit_str = ' limit '.$limit;
        } else {
            $limit_str = '';
        }
        if ($private) {
            $private = ' and cm.private = 0';
        } else {
            $private = '';
        }
        $comments = eF_getTableData("comments cm, content cn", "cm.id AS id, cm.data AS data, cm.users_LOGIN AS users_LOGIN, cm.timestamp AS timestamp, cn.name AS content_name, cn.id AS content_ID, cn.ctg_type AS content_type", "cn.lessons_ID=$lesson AND cm.content_ID=cn.id AND cn.active=1 AND cm.active=1".$private.$login_str.$content_ID_str, "cm.timestamp DESC".$limit_str);
        return $comments;
    }
    /**

     * Clear duplicate comments

     * 

     * There are times that the system may end up with duplicate comments, like when

     * copying content. This function is used to effectively eliminate duplicates.

     * <br/>Example:

     * <code>

     * comments :: clearDuplicates($currentLesson);

     * </code>

     * 

     * @param mixed $lesson a lesson id or an EfrontLesson object

     * @access public

     * @static

     * @since 3.6.0

     */
    public static function clearDuplicates($lesson) {
     if ($lesson instanceOf EfrontLesson) {
      $lessonId = $lesson -> lesson['id'];
     } elseif (eF_checkParameter($lesson, 'id')) {
      $lessonId = $lesson;
     } else {
      throw new EfrontLessonException(_INVALIDID.": $lesson", EfrontLessonException :: INVALID_ID);
     }
     $result = eF_getTableData("comments", "*", "lessons_ID=".$lessonId, "id");
     foreach ($result as $value) {
      $commentsTerms[$value['id']] = $value;
         $id = $value['id'];
   unset($value['id']);
      $checksums[$id] = md5(serialize($value));
     }
     $uniques = array_unique($checksums);
     $duplicates = array_diff_key($checksums, $uniques);
     foreach ($duplicates as $key => $value) {
         $comments = new comments($commentsTerms[$key]);
         $comments -> delete();
     }
    }
 public function persist() {
  parent :: persist();
  $sourceUnit = new EfrontUnit($this -> comments['content_ID']);
  EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_COMMENT_WRITING, "lessons_ID" => $sourceUnit['lessons_ID'], "entity_ID" => $this -> comments['content_ID'], "entity_name" => $sourceUnit['name']));
 }
}
?>
