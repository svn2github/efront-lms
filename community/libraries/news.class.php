<?php
/**

* news Class file

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
class news extends EfrontEntity
{
    /**

     * The news properties

     * 

     * @since 3.6.0

     * @var array

     * @access public

     */
    public $news = array();
    /**

     * Create news

     * 

     * This function is used to create news

     * <br>Example:

     * <code>

	 * $fields = array("title"       => $form -> exportValue('title'),

	 *       "data"        => $form -> exportValue('data'),

	 *       "timestamp"   => $from_timestamp,

	 *		 "expire"      => $to_timestamp,

	 *       "lessons_ID"  => isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID'] ? $_SESSION['s_lessons_ID'] : 0,

	 *       "users_LOGIN" => $_SESSION['s_login']);

	 *

	 * $news = news :: create($fields, 0));

	 * 

     * </code>

     * 

     * @param $fields An array of data

     * @param $sendEmail Whether to send the announcement as an email as well 

     * @return news The new object

     * @since 3.6.0

     * @access public

     * @static

     */
    public static function create($fields = array(), $sendEmail = false) {
        $fields = array('title' => $fields['title'],
                        'data' => $fields['data'],
                        'timestamp' => $fields['timestamp'] ? $fields['timestamp'] : time(),
                        'expire' => $fields['expire'] ? $fields['expire'] : time() + 3600*24*30,
                        'lessons_ID' => $fields['lessons_ID'],
                        'users_LOGIN' => $fields['users_LOGIN']);
        $newId = eF_insertTableData("news", $fields);
        $result = eF_getTableData("news", "*", "id=".$newId); //We perform an extra step/query for retrieving data, sinve this way we make sure that the array fields will be in correct order (forst id, then name, etc)
        $news = new news($result[0]['id']);
        if ($news -> news['lessons_ID']) {
            //EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_LESSON_ANNOUNCEMENT, "users_LOGIN" => $fields['users_LOGIN'], "users_name" => $currentUser -> user['name'], "users_surname" => $currentUser -> user['surname'], "lessons_ID" => $fields['lessons_ID'], "entity_ID" => $id, "entity_name" => $news_content['title']), isset($_POST['email']));
            EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_LESSON_ANNOUNCEMENT, "users_LOGIN" => $GLOBALS['currentUser'] -> user['login'], "users_name" => $GLOBALS['currentUser'] -> user['name'], "users_surname" => $GLOBALS['currentUser'] -> user['surname'], "lessons_ID" => $GLOBALS['currentLesson'] -> lesson['id'], "lessons_name" => $GLOBALS['currentLesson'] -> lesson['name'], "entity_name" => $fields['title'], "entity_ID" => $newId), $sendEmail);
        } else {
            //EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_SYSTEM_ANNOUNCEMENT, "users_LOGIN" => $fields['users_LOGIN'], "users_name" => $currentUser -> user['name'], "users_surname" => $currentUser -> user['surname'], "entity_ID" => $id, "entity_name" => $news_content['title']), isset($_POST['email']));
            EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_SYSTEM_ANNOUNCEMENT, "users_LOGIN" => $GLOBALS['currentUser'] -> user['login'], "users_name" => $GLOBALS['currentUser'] -> user['name'], "users_surname" => $GLOBALS['currentUser'] -> user['surname'], "lessons_name" => $GLOBALS['currentLesson'] -> lesson['name'], "entity_name" => $fields['title'], "entity_ID" => $newId), $sendEmail);
        }
        EfrontSearch :: insertText($news -> news['title'], $news -> news['id'], "news", "title");
        EfrontSearch :: insertText($news -> news['data'], $news -> news['id'], "news", "data");
        return $news;
    }
    /**

     * Persist news properties

     * 

     * This function can be used to persist with the database 

     * any changes made to the current news object.

     * <br/>Example:

     * <code>

     * $news -> news['title'] = 'new Title';              //Change the news title

     * $news -> persist();                                   //Make the change permanent

     * </code>

     * 

     * @since 3.6.0

     * @access public

     */
    public function persist() {
        parent :: persist();
        EfrontSearch :: removeText('news', $this -> news['id'], 'data');
        EfrontSearch :: insertText($this -> news['data'], $this -> news['id'], "news", "data");
        EfrontSearch :: removeText('news', $this -> news['id'], 'title');
        EfrontSearch :: insertText($this -> news['title'], $this -> news['id'], "news", "title");
    }
    /**

     * Delete the news

     * 

     * This function is used to delete the current news.

     * All related information is lost, as well as files associated

     * with the news.

     * <br/>Example:

     * <code>

     * $news = new news(12);                //Instantiate news with id 12

     * $news -> delete();                            //Delete news and all associated information

     * </code>

     * 

     * @since 3.6.0

     * @access public

     */
    public function delete() {
        parent :: delete();
     EfrontSearch :: removeText('news', $this -> news['id'], 'title');
     EfrontSearch :: removeText('news', $this -> news['id'], 'data');
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
     $form -> addElement('text', 'title', _ANNOUNCEMENTTITLE, 'class = "inputText"');
     $form -> addRule('title', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
     $form -> addElement('textarea', 'data', _ANNOUNCEMENTBODY, 'class = "simpleEditor inputTextarea" style = "width:98%;height:10em;"');
     $form -> addElement('checkbox', 'email', _SENDASEMAILALSO, null, 'class = "inputCheckBox"');
     $form -> addElement('submit', 'submit', _ANNOUNCEMENTADD, 'class = "flatButton"');
     $form -> setDefaults(array('title' => $this -> news['title'], 'data' => $this -> news['data']));
        return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form) {
        $from_timestamp = mktime($_POST['from_Hour'], $_POST['from_Minute'], 0, $_POST['from_Month'], $_POST['from_Day'], $_POST['from_Year']);
        $to_timestamp = mktime($_POST['to_Hour'], $_POST['to_Minute'], 0, $_POST['to_Month'], $_POST['to_Day'], $_POST['to_Year']);
        if (isset($_GET['edit'])) {
            $this -> news["title"] = $form -> exportValue('title');
            $this -> news["data"] = $form -> exportValue('data');
            $this -> news["timestamp"] = $from_timestamp;
            $this -> news["expire"] = $to_timestamp;
            $this -> persist();
        } else {
            $fields = array("title" => $form -> exportValue('title'),
                            "data" => $form -> exportValue('data'),
                            "timestamp" => $from_timestamp,
       "expire" => $to_timestamp,
                            "lessons_ID" => isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID'] ? $_SESSION['s_lessons_ID'] : 0,
                            "users_LOGIN" => $_SESSION['s_login']);
            $news = self :: create($fields, isset($_POST['email']));
            $this -> news = $news;
        }
    }
 /**

	* Get announcements

	*

	* This function gets the lesson announcements (news). It returns an array holding the announcement title, id

	* and timestamp.

	* <br/>Example:

	* <code>

	* $news = news ::: getNews();

	* print_r($news);

	* //Returns:

	*Array

	*(

	*    [0] => Array

	*        (

	*            [title] => announcement 1

	*            [id] => 3

	*            [timestamp] => 1125751731

	*            [users_LOGIN] => admin

	*        )

	*

	*    [1] => Array

	*        (

	*            [title] => Important announcem...

	*            [id] => 5

	*            [timestamp] => 1125751012

	*            [users_LOGIN] => peris

	*        )

	*)

	* </code>

	*

	* @param mixed $lessonId The lesson id or an array of ids

	* @param boolean $check_expire Whether to return only announcements that are valid for the current date

	* @return array The news array

	* @since 3.6.0

	* @static

	* @access public

	*/
    public static function getNews($lessonId, $checkExpire = false) {
  if ($checkExpire) {
   $expireString = " and (n.expire=0 OR n.expire >=".time().") AND n.timestamp<=".time();
   //$expireString = " AND n.timestamp<=".time();   // check why it was here hot talking into account expire. makriria 15/3/2010
  }
  if (is_array($lessonId) && !empty($lessonId)) {
   foreach ($lessonId as $key => $value) {
       if (!eF_checkParameter($value, 'id')) {
        unset($lessonId[$key]);
    }
   }
   if (!empty($lessonId)) {
    $result = eF_getTableData("news n, users u", "n.*, u.surname, u.name", "n.users_LOGIN = u.login".$expireString." and n.lessons_ID in (".implode(",", $lessonId).")", "n.timestamp desc, n.id desc");
    $news = array();
    foreach ($result as $value) {
        $interval = time() - $value['timestamp'];
        $value['time_since'] = eF_convertIntervalToTime(abs($interval), true).' '.($interval > 0 ? _AGO : _REMAININGPLURAL);
        $news[$value['id']] = $value;
    }
   }
   return $news;
  }
  //We don't have an "else" statement here, because in case the check in the above if removed all elements of lessonId (they were not ids), this part of code will be executed and the function won't fail 
  if (!eF_checkParameter($lessonId, 'id')) {
      $lessonId = 0;
  }
  $result = eF_getTableData("news n, users u", "n.*, u.surname, u.name", "n.users_LOGIN = u.login".$expireString." and n.lessons_ID=$lessonId", "n.timestamp desc, n.id desc");
  $news = array();
  foreach ($result as $value) {
      $interval = time() - $value['timestamp'];
      $value['time_since'] = eF_convertIntervalToTime(abs($interval), true).' '.($interval > 0 ? _AGO : _REMAININGPLURAL);
      $news[$value['id']] = $value;
  }
  return $news;
    }
}
