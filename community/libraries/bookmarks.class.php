<?php
/**
* bookmarks Class file
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
class bookmarks extends EfrontEntity
{
    /**
     * The bookmarks properties
     * 
     * @since 3.6.0
     * @var array
     * @access public
     */
    public $bookmarks = array();
       
    /**
     * Create bookmarks
     * 
     * This function is used to create bookmarks
     * <br>Example:
     * <code>
	 * $bookmarks = bookmarks :: create($fields));		//$fields is an array of data for the bookmark 
     * </code>
     * 
     * @param $fields An array of data
     * @return bookmarks The new object
     * @since 3.6.0
     * @access public
     * @static
     */
    public static function create($fields = array()) {        
        
        $newId    = eF_insertTableData("bookmarks", $fields);
        $bookmark = new bookmarks($newId);        

        return $bookmarks;            
    }
    
    /**
     * (non-PHPdoc)
     * @see libraries/EfrontEntity#getForm($form)
     */
    public function getForm($form) {}
    
    /**
     * (non-PHPdoc)
     * @see libraries/EfrontEntity#handleForm($form)
     */
    public function handleForm($form, $values = false) {}
    
    /**
	 * Get bookmarks
	 *
	 * This function gets the lesson bookmarks. It returns an array holding the name of the lesson where the comment was put,
	 * the comment id, the comment itself (which is put as a title on the lesson name link), and finally the timestamp and the
	 * user that posted it. IF a lesson id is not specified, then bookmarks for the current lesson are returned.If a login is
	 * specified, then only bookmarks that the specified user has posted are returned. If a content id is specified, then only
	 * bookmarks of this unit are displayed.
	 * <br/>Example:
	 * <code>
	 * $bookmarks = bookmarkds :: getBookmarks();
	 * print_r($bookmarks);
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
     * @param $lesson The lesson, either an id or an EfrontLesson object
     * @param $user The user, either a user login or an EfrontUser object
	 * @return array The bookmarks array
	 * @since 3.6.0
	 * @access public
	 * @static
     */
    public static function getBookmarks($user, $lesson) {
        if ($user instanceof EfrontUser) {
            $user = $user -> user['login'];
        } else if (!eF_checkParameter($user, 'login')) {
            throw new EfrontUserException(_INVALIDLOGIN.': '.$user['login'], EfrontUserException :: INVALID_LOGIN);
        }
        if ($lesson instanceof EfrontLesson) {
            $lesson = $lesson -> lesson['id'];
        } else if (!eF_checkParameter($lesson, 'id')) {
            throw new EfrontLessonException(_INVALIDID.": $lesson", EfrontLessonException :: INVALID_ID);
        }        
        
        $bookmarks = array();
        $result    = eF_getTableData("bookmarks b, lessons l", "b.*, l.name as lesson_name", "b.lessons_ID=l.id and users_LOGIN='".$user."' and lessons_ID=".$lesson);
        foreach ($result as $value) {
            $bookmarks[$value['id']] = $value;
        }
        
        return $bookmarks;

    }
    
    
}