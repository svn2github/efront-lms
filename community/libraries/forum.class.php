<?php
/**

 * 

 * @author user

 *

 */
class EfrontForumException
{
}
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * 

 * @author user

 *

 */
class f_forums extends EfrontEntity
{
    /**

     * 

     * @var unknown_type

     */
    public $tree;
    /**

     * 

     * @return unknown_type

     */
    public function __construct($param) {
        //$this -> entity = 'f_forums';
        parent :: __construct($param);
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#delete()

     */
    public function delete() {
        $forumTree = self :: getForumTree();
        $children = $forumTree[$this -> {$this -> entity}['id']]; //Get all the forum's direct siblings
        for ($i = 0; isset($children[$i]); $i++) { //Find all the forum siblings' siblings
            $children = array_merge ($children, $forumTree[$children[$i]]);
        }
        $children[] = $this -> {$this -> entity}['id']; //Append the deleted forum to the childrens list
        $topics = eF_getTableDataFlat("f_topics", "id", "f_forums_ID in (".implode(",", $children).")"); //Get forums' topics
        if (sizeof($topics) > 0) { //Delete forums' messages and topics
            $fmid = eF_getTableDataFlat("f_messages", "id", "f_topics_ID in (".implode(",", $topics['id']).")");
            if (sizeof($fmid) > 0) {
                EfrontSearch :: removeText('f_messages', implode(",", $fmid['id']), '', true);
            }
            eF_deleteTableData("f_messages", "f_topics_id in (".implode(",", $topics['id']).")");
            eF_deleteTableData("f_topics", "id in (".implode(",", $topics['id']).")");
        }
        $fpid = eF_getTableDataFlat("f_poll", "id", "f_forums_ID in (".implode(",", $children).")");
        if (sizeof($fpid) > 0) {
            EfrontSearch :: removeText('f_poll', implode(",", $fpid['id']), '', true);
        }
        eF_deleteTableData("f_poll", "f_forums_ID in (".implode(",", $children).")"); //Delete polls
        EfrontSearch :: removeText('f_forums', implode(",", $children), '', true);
        eF_deleteTableData("f_forums", "id in (".implode(",", $children).")"); //Finally, delete forums themselves
    }
    /**

     * 

     * @param $forums

     * @return unknown_type

     */
    public static function getForumTree($forums = false) {
        if ($forums === false) {
            $forums = f_forums :: getAll("f_forums");
        }
        $forumTree = array();
        $tempForums = $forums;
        //Convert array to tree. At the end of the loop, the $forums array will hold the forum tree, where each node is an array of its child nodes
        while (sizeof($tempForums) > 0 && $count++ < 1000) { //$count is put here to prevent infinite loops
            $node = current($tempForums); //Get the key/node pairs of the first array element
            $key = key($tempForums);
            $parent_id = $node['parent_id'];
            $forumTree[$parent_id][] = $node['id']; //Append to the tree array, at the forum id index, the id of its child
            $forumTree[$node['id']] = array();
            $forums[$node['id']] = $node; //Copy node to forums, which will be used later as forums source
            unset($tempForums[$key]); //We visited the node, so delete it from the (array) graph
        }
        return $forumTree;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
        $form -> addElement('text', 'title', _TITLE, 'class = "inputText"');
        $form -> addRule('title', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
        $form -> addElement('select', 'status', _STATUS, array(1 => _PUBLIC, 2 => _LOCKED, 3 => _INVISIBLE));
        $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputTextarea simpleEditor"');
        $form -> addElement('submit', 'submit_add_forum', _SUBMIT, 'class = "flatButton"');
        $form -> setDefaults(array('title' => $this -> {$this -> entity}['title'],
                                   'lessons_ID' => $this -> {$this -> entity}['lessons_ID'],
                 'status' => $this -> {$this -> entity}['status'],
                 'comments' => $this -> {$this -> entity}['comments']));
        return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form) {
        $values = $form -> exportValues();
        $fields = array("title" => $values['title'],
                        "lessons_ID" => isset($values['lessons_ID']) ? $values['lessons_ID'] : $defaultLesson,
                        "status" => isset($values['status']) ? $values['status']: 1,
                        "comments" => $values['comments']);
        if (isset($_GET['edit'])) {
            $this -> {$this -> entity} = array_merge($this -> {$this -> entity}, $fields);
            $this -> persist();
        } else {
            $fields['users_LOGIN'] = $_SESSION['s_login'];
            $fields['parent_id'] = isset($_GET['parent_forum_id']) ? $_GET['parent_forum_id'] : 0;
            self :: create($fields);
        }
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#persist()

     */
    public function persist() {
        parent :: persist();
        //Propagate the forum status to all of its subforums and topics
        $result = eF_getTableData("f_forums", "*", "id=".$this -> {$this -> entity}['id']);
        $fields = $result[0];
        $childrenTempList = array($result[0]['id']);
        //pr($childrenTempList);
        $childrenList = $childrenTempList;
        while (!empty($childrenTempList)) {
            $list = implode(",", $childrenTempList);
            $result = eF_getTableDataFlat("f_forums", "id", "parent_id IN ($list)");
            $childrenTempList = $result['id'];
            if (!empty($childrenTempList)) {
                $childrenList = array_merge($childrenList, $childrenTempList);
            }
        }
        $list = implode(",",$childrenList);
        eF_updateTableData("f_forums", array("status" => $fields['status']), "id IN ($list)");
        eF_updateTableData("f_topics", array("status" => $fields['status']), "f_forums_ID IN ($list)");
        EfrontSearch :: removeText('f_forums', $this -> {$this -> entity}['id'], '');
        EfrontSearch :: insertText($fields['title'], $this -> {$this -> entity}['id'], "f_forums", "title");
        if (mb_strlen($fields['comments']) > 3) {
            EfrontSearch :: insertText(strip_tags($fields['comments']), $this -> {$this -> entity}['id'], "f_forums", "data");
        }
    }
    /**

     * 

     * @param $fields

     * @return unknown_type

     */
    public static function create($fields = array()) {
        $new_id = eF_insertTableData("f_forums", $fields);
        EfrontSearch :: insertText($fields['title'], $new_id, "f_forums", "title");
        if (mb_strlen($fields['comments']) > 3) {
            EfrontSearch :: insertText(strip_tags($fields['comments']), $new_id, "f_forums", "data");
        }
        // Timelines add event
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_FORUM,
                   "users_LOGIN" => $_SESSION['s_login'],
                   "lessons_ID" => isset($GLOBALS['currentLesson']) ? $GLOBALS['currentLesson'] -> lesson['id'] : null,
                   "lessons_name" => isset($GLOBALS['currentLesson']) ? $GLOBALS['currentLesson'] -> lesson['name'] : null,
                   "entity_ID" => $new_id,
                   "entity_name" => $fields['name']));
    }
    /**

     * 

     * @param $tree

     * @param $node

     * @param $topics

     * @param $polls

     * @param $messages

     * @param $last_post

     * @return unknown_type

     */
    public static function calculateForumStats($tree, $node, $topics, $polls, $messages, $last_post) {
        $total = array();
        $total['topics'] += $topics[$node];
        $total['polls'] += $polls[$node];
        $total['messages'] += $messages[$node];
        $last_post[$node] >= $total['last_post'] ? $total['last_post'] = $last_post[$node] : '';
        foreach ($tree[$node] as $id) {
            if (in_array($id, array_keys($tree))) {
                $temp = self :: calculateForumStats($tree, $id, $topics, $polls, $messages, $last_post);
                $total['topics'] += $temp['topics'];
                $total['polls'] += $temp['polls'];
                $total['messages'] += $temp['messages'];
                $last_post[$id] > $total['last_post'] ? $total['last_post'] = $last_post[$id] : '';
            } else {
                $total['topics'] += $topics[$id];
                $total['polls'] += $polls[$id];
                $total['messages'] += $messages[$id];
                $last_post[$id] > $total['last_post'] ? $total['last_post'] = $last_post[$id] : '';
            }
        }
        return $total;
    }
}
/**

 * 

 * @author user

 *

 */
class f_topics extends EfrontEntity
{
    /**

     * 

     * @return unknown_type

     */
    public function __construct($param) {
        $this -> entity = 'f_topics';
        parent :: __construct($param);
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#delete()

     */
    public function delete() {
        $fmid = eF_getTableDataFlat("f_messages", "id", "f_topics_ID=".$this -> {$this -> entity}['id']);
        eF_deleteTableData("f_messages", "f_topics_ID=".$this -> {$this -> entity}['id']);
        parent :: delete();
        EfrontSearch :: removeText('f_messages', implode(",", $fmid['id']), '', true);
        EfrontSearch :: removeText('f_topics', $this -> {$this -> entity}['id'], '');
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
     $form -> addElement('text', 'title', _TITLE, 'class = "inputText"');
     $form -> addRule('title', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
     $form -> addElement('select', 'status', _STATUS, array(1 => _PUBLIC, 2 => _LOCKED, 3 => _INVISIBLE));
     $form -> addElement('textarea', 'message', _MESSAGE, 'class = "inputTextarea simpleEditor"');
     $form -> addElement('submit', 'submit_add_topic', _SUBMIT, 'class = "flatButton"');
        $form -> setDefaults(array('title' => $this -> {$this -> entity}['title'],
                                   'status' => $this -> {$this -> entity}['status']));
        return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form) {
        $values = $form -> exportValues();
        if (isset($_GET['edit'])) {
         $fields = array("title" => $values['title'],
                         "status" => $values['status']);
            $this -> {$this -> entity} = array_merge($this -> {$this -> entity}, $fields);
            $this -> persist();
        } else {
         $fields = array("title" => $values['title'],
                         "message" => $values['message'],
                         "status" => $values['status'] ? $values['status'] : 1,
                         "f_forums_ID" => $_GET['forum_id'],
                         "users_LOGIN" => $_SESSION['s_login'],
                         "timestamp" => time(),
                         "sticky" => 0);
            self :: create($fields);
        }
    }
    public function persist() {
        parent :: persist();
        EfrontSearch :: removeText('f_topics', $this -> {$this -> entity}['id'], '');
        EfrontSearch :: insertText($fields['title'], $this -> {$this -> entity}['id'], "f_topics", "title");
    }
    /**

     * 

     * @param $fields

     * @return unknown_type

     */
    public static function create($fields = array()) {
        //The message field is only used for creating the topic's initial message
        $message = $fields['message'];
        unset($fields['message']);
        $topic_id = eF_insertTableData("f_topics", $fields);
        $message_fields = array("title" => $fields['title'],
                                "body" => $message,
                                "f_topics_ID" => $topic_id,
                                "users_LOGIN" => $_SESSION['s_login'],
                                "timestamp" => time(),
                                "replyto" => 0);
        $new_id = eF_insertTableData("f_messages", $message_fields);
        EfrontSearch :: insertText($message_fields['title'], $new_id, "f_messages", "title");
        EfrontSearch :: insertText($fields['title'], $topic_id, "f_topics", "title");
        if (mb_strlen($message_fields['body']) > 3) {
            EfrontSearch :: insertText(strip_tags($message_fields['body']), $new_id, "f_messages", "data");
        }
        // Timelines add event
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_TOPIC,
                   "users_LOGIN" => $_SESSION['s_login'],
                   "lessons_ID" => isset($GLOBALS['currentLesson']) ? $GLOBALS['currentLesson'] -> lesson['id'] : null,
                   "lessons_name" => isset($GLOBALS['currentLesson']) ? $GLOBALS['currentLesson'] -> lesson['name'] : null,
                   "entity_ID" => $new_id,
                   "entity_name" => $fields['title']));
    }
}
class f_messages extends EfrontEntity {
    /**

     * 

     * @return unknown_type

     */
    public function __construct($param) {
        $this -> entity = 'f_messages';
        parent :: __construct($param);
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#delete()

     */
    public function delete() {
        parent :: delete();
        EfrontSearch :: removeText('f_messages', $this -> {$this -> entity}['id'], '');
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
     $form -> addElement('text', 'title', _TITLE, 'class = "inputText"');
     $form -> addElement('textarea', 'body', _BODY, 'id = "editor_message_data" class = "inputTextarea simpleEditor"');
     $form -> addElement('hidden', 'replyto', null);
     $form -> addElement('submit', 'submit_add_message', _SUBMIT, 'class = "flatButton"');
     if (isset($_GET['replyto']) && in_array($_GET['replyto'], $GLOBALS['legalValues'])) {
         $replyto = eF_getTableData("f_messages", "*", "id=".$_GET['replyto']);
         $form -> setDefaults(array('title' => 'Re: '.$replyto[0]['title']));
         if (isset($_GET['quote'])) {
             $form -> setDefaults(array('body' => '[quote]'.str_replace(array('<p>', '</p>'), '', $replyto[0]['body']).'[/quote]'));
         }
     }
     $form -> setDefaults(array('title' => $this -> {$this -> entity}['title'],
                                   'body' => $this -> {$this -> entity}['body']));
        return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form) {
        $values = $form -> exportValues();
        if (isset($_GET['edit'])) {
         $fields = array("title" => $values['title'],
                         "body" => $values['body']);
            $this -> {$this -> entity} = array_merge($this -> {$this -> entity}, $fields);
            $this -> persist();
        } else {
            $fields = array("title" => $values['title'],
                            "body" => $values['body'],
                            "f_topics_ID" => $_GET['topic_id'],
                            "users_LOGIN" => $_SESSION['s_login'],
                            "timestamp" => time(),
                            "replyto" => $values['replyto'] ? $values['replyto'] : 0);
            self :: create($fields);
        }
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#persist()

     */
    public function persist() {
        parent :: persist();
        EfrontSearch :: removeText('f_messages', $this -> {$this -> entity}['id'], '');
        EfrontSearch :: insertText($fields['title'], $this -> {$this -> entity}['id'], "f_messages", "title");
        if (mb_strlen($fields['body']) > 3) {
            EfrontSearch :: insertText(strip_tags($fields['body']), $this -> {$this -> entity}['id'], "f_messages", "data");
        }
    }
    /**

     * 

     * @param $fields

     * @return unknown_type

     */
    public static function create($fields = array()) {
        $new_id = eF_insertTableData("f_messages", $fields);
        EfrontSearch :: insertText($fields['title'], $new_id, "f_messages", "title");
        if (mb_strlen($fields['body']) > 3) {
            EfrontSearch :: insertText(strip_tags($fields['body']), $new_id, "f_messages", "data");
        }
        // Timelines add event
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_FORUM_MESSAGE_POST,
                   "users_LOGIN" => $_SESSION['s_login'],
                   "lessons_ID" => isset($GLOBALS['currentLesson']) ? $GLOBALS['currentLesson'] -> lesson['id'] : null,
                   "lessons_name" => isset($GLOBALS['currentLesson']) ? $GLOBALS['currentLesson'] -> lesson['name'] : null,
                   "entity_ID" => $new_id,
                   "entity_name" => $fields['title']));
    }
}
class f_poll extends EfrontEntity {
    /**

     * 

     * @return unknown_type

     */
    public function __construct($param) {
        $this -> entity = 'f_poll';
        parent :: __construct($param);
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#delete()

     */
    public function delete() {
       $result = eF_getTableData("f_poll", "users_LOGIN", "id=".$this -> {$this -> entity}['id']); //Get poll information, to make sure that the user has the priviledge to delete it
       eF_deleteTableData("f_users_to_polls", "f_poll_ID=".$this -> {$this -> entity}['id']);
       parent :: delete();
       EfrontSearch :: removeText('f_poll', $this -> {$this -> entity}['id'], '');
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#getForm($form)

     */
    public function getForm($form) {
     $form -> addElement('text', 'poll_subject', _SUBJECT, 'class = "inputText"');
     $form -> addRule('poll_subject', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
     $form -> addElement('textarea', 'poll_text', _QUESTIONTEXT, 'class = "inputTextarea simpleEditor"');
     $formatDate = eF_dateFormat();
     $options = array(
         'format' => $formatDate,
         'minYear' => date("Y"),
         'maxYear' => date('Y') + 2,
     );
     $form -> addElement('date', 'from', null, $options);
     $form -> addElement('date', 'to', null, $options);
     $form -> setDefaults(array('from' => time(), 'to' => time() + 30 * 86400)); //1 month forward
        $form -> addElement('text', 'options[0]', _INSERTMULTIPLEQUESTIONS, 'class = "inputText inputText_QuestionChoice"');
        $form -> addRule('options[0]', _THEFIELD.' "'._INSERTMULTIPLEQUESTIONS.'" '._ISMANDATORY, 'required', null, 'client');
        $form -> addElement('text', 'options[1]', '', 'class = "inputText inputText_QuestionChoice"');
        $form -> addRule('options[1]', _THEFIELD.' "'._INSERTMULTIPLEQUESTIONS.'" '._ISMANDATORY, 'required', null, 'client');
     $form -> addElement('submit', 'submit_add_poll', _SUBMIT, 'class = "flatButton"');
        $form -> setDefaults(array('poll_subject' => $this -> {$this -> entity}['title'],
                                   'poll_text' => $this -> {$this -> entity}['question']));
     $values['options'] = array_values(unserialize($this -> {$this -> entity}['options'])); //We put array_values to make sure that the array starts from zero
        foreach ($values['options'] as $key => $value) {
            if ($key > 2) {
             $form -> addElement('text', 'options['.$key.']', null, 'class = "inputText inputText_QuestionChoice"');
             $form -> addRule('options['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
            }
            $form -> setDefaults(array('options['.$key.']' => htmlspecialchars_decode($value, ENT_QUOTES)));
        }
        return $form;
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#handleForm($form)

     */
    public function handleForm($form) {
        $values = $form -> getSubmitValues();
        foreach ($values['options'] as $key => $value) {
            $values['options'][$key] = htmlspecialchars($value, ENT_QUOTES,'UTF-8');
        }
        $options = serialize(array_values($values['options'])); //Array values are put here to reindex array, if the keys are not in order
        $form_values = $form -> exportValues();
        $start = mktime(0, 0, 0, $form_values['from']['m'], $form_values['from']['d'], $form_values['from']['Y']);
        $end = mktime(0, 0, 0, $form_values['to']['m'], $form_values['to']['d'], $form_values['to']['Y']);
        if ($start > $end) {
            throw new Exception(_ENDDATEMUSTBEBEFORESTARTDATE);
        } else if ($form -> validate()) {
            $fields = array('options' => $options,
                            'title' => $form_values['poll_subject'],
                            'question' => $form_values['poll_text'],
                            'timestamp_start' => $start,
                            'timestamp_end' => $end);
            if (isset($_GET['edit'])) { //If we are changing an existing question
             $this -> {$this -> entity} = array_merge($this -> {$this -> entity}, $fields);
             $this -> persist();
            } else {
                $fields['timestamp_created'] = time();
                $fields['f_forums_ID'] = $_GET['forum_id'];
                $fields['users_LOGIN'] = $_SESSION['s_login'];
                self :: create($fields);
            }
        }
    }
    /**

     * (non-PHPdoc)

     * @see libraries/EfrontEntity#persist()

     */
    public function persist() {
        parent :: persist();
        EfrontSearch :: removeText('f_poll', $this -> {$this -> entity}['id'], 'title');
        EfrontSearch :: insertText($fields['title'], $this -> {$this -> entity}['id'], "f_poll", "title");
    }
    /**

     * 

     * @param $fields

     * @return unknown_type

     */
    public static function create($fields = array()) {
        $new_id = eF_insertTableData("f_poll", $fields);
        EfrontSearch :: insertText($fields['title'], $new_id, "f_poll", "title");
        if (mb_strlen($fields['question']) > 3) {
            EfrontSearch :: insertText(strip_tags($fields['question']), $new_id, "f_poll","data");
        }
        // Timelines add event
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_POLL,
                   "users_LOGIN" => $_SESSION['s_login'],
                   "lessons_ID" => isset($GLOBALS['currentLesson']) ? $GLOBALS['currentLesson'] -> lesson['id'] : null,
                   "lessons_name" => isset($GLOBALS['currentLesson']) ? $GLOBALS['currentLesson'] -> lesson['name'] : null,
                   "entity_ID" => $new_id,
                   "entity_name" => $fields['title']));
    }
}
?>
