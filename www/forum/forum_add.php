<?php
/**
* forum add page
*
* This page is used to add a new topic or message
*
* @package eFront
* @version 0.1
*/

session_cache_limiter('none');
session_start();

$path = "../../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$loadScripts = array('EfrontScripts');

/*Check the user type. If the user is not valid, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
        $smarty -> assign("T_CURRENT_USER", $currentUser);

        if (MODULE_HCD_INTERFACE) {
            $currentUser -> aspects['hcd'] = EfrontEmployeeFactory :: factory($_SESSION['s_login']);
            $employee = $currentUser -> aspects['hcd'];
        }
        
        if ($_SESSION['s_lessons_ID']) {
            $userLessons = $currentUser -> getLessons();
            $currentUser -> applyRoleOptions($userLessons[$_SESSION['s_lessons_ID']]);                //Initialize user's role options for this lesson
            $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
        } else {
            $currentUser -> applyRoleOptions();                //Initialize user's role options for this lesson                   
        }

        if ($currentUser -> coreAccess['forum'] && $currentUser -> coreAccess['forum'] != 'change') {
            header("location:".G_SERVERNAME.$_SESSION['s_type'].".php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        header("location:index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    header("location:index.php?message=".urlencode(_YOUCANNOTACCESSTHISPAGE)."&message_type=failure");
    exit;
}

//Get forum configuration values
$forum_config = eF_getTableDataFlat("f_configuration", "*");
sizeof($forum_config) > 0 ? $forum_config = array_combine($forum_config['name'], $forum_config['value']) : $forum_config = array();

if (isset($_GET['delete_forum']) && eF_checkParameter($_GET['delete_forum'], 'id')) {
    $smarty -> assign("T_CATEGORY", "1");                           //Bogus info to exclude will-be-deprecated code from displaying

    $result = eF_getTableData("f_forums", "users_LOGIN", "id=".$_GET['delete_forum']);                  //Get forum information, to make sure that the user has the priviledge to delete it
    if ($result[0]['users_LOGIN'] == $_SESSION['s_login'] || $_SESSION['s_type'] == 'administrator') {
        $result_forums = eF_getTableData("f_forums", "*");
        $forum_tree    = array();
        //Convert array to tree. At the end of the loop, the $forums array will hold the forum tree, where each node is an array of its child nodes
        while (sizeof($result_forums) > 0 && $count++ < 10000) {            //$count is put here to prevent infinite loops
            $node                     = current($result_forums);            //Get the key/node pairs of the first array element
            $key                      = key($result_forums);
            $parent_id                = $node['parent_id'];               
            $forum_tree[$parent_id][] = $node['id'];                        //Append to the tree array, at the forum id index, the id of its child
            $forum_tree[$node['id']]  = array();
            $forums[$node['id']]      = $node;                              //Copy node to forums, which will be used later as forums source
            unset($result_forums[$key]);                                    //We visited the node, so delete it from the (array) graph
        }

        $children = $forum_tree[$_GET['delete_forum']];                     //Get all the forum's direct siblings
        for ($i = 0; isset($children[$i]); $i++) {                          //Find all the forum siblings' siblings
            $children = array_merge ($children, $forum_tree[$children[$i]]);
        }
        $children[] = $_GET['delete_forum'];                                 //Append the deleted forum to the childrens list
        
        $topics     = eF_getTableDataFlat("f_topics", "id", "f_forums_ID in (".implode(",", $children).")");    //Get forums' topics
        if (sizeof($topics) > 0) {                                                                              //Delete forums' messages and topics
			$fmid = eF_getTableDataFlat("f_messages", "id", "f_topics_ID in (".implode(",", $topics['id']).")");
			EfrontSearch :: removeText('f_messages', implode(",", $fmid['id']), '', true);
            eF_deleteTableData("f_messages", "topics_id in (".implode(",", $topics['id']).")");
            eF_deleteTableData("f_topics", "id in (".implode(",", $topics['id']).")");
        }
		$fpid = eF_getTableDataFlat("f_poll", "id", "f_forums_ID in (".implode(",", $children).")");
		EfrontSearch :: removeText('f_poll', implode(",", $fmid['id']), '', true);
        eF_deleteTableData("f_poll", "f_forums_ID in (".implode(",", $children).")");                           //Delete polls
		EfrontSearch :: removeText('f_forums', implode(",", $children), '', true);
        eF_deleteTableData("f_forums", "id in (".implode(",", $children).")");                                  //Finally, delete forums themselves



        $message      = _FORUMDELETEDSUCCESSFULLY;
        $message_type = 'success';
    } else {
        $message      = _UNPRIVILEGEDATTEMPT;
        $message_type = 'failure';
    }
} else if (isset($_GET['delete_topic']) && eF_checkParameter($_GET['delete_topic'], 'id')) {
    $result = eF_getTableData("f_topics", "users_LOGIN", "id=".$_GET['delete_topic']);                  //Get topic information, to make sure that the user has the priviledge to delete it
    if ($result[0]['users_LOGIN'] == $_SESSION['s_login'] || $_SESSION['s_type'] == 'administrator') {
		$fmid = eF_getTableDataFlat("f_messages", "id", "f_topics_ID=".$_GET['delete_topic']);
		EfrontSearch :: removeText('f_messages', implode(",", $fmid['id']), '', true);
		EfrontSearch :: removeText('f_topics', $_GET['delete_topic'], '');

        eF_deleteTableData("f_messages", "f_topics_ID=".$_GET['delete_topic']);
        eF_deleteTableData("f_topics", "id=".$_GET['delete_topic']);
        $message      = _TOPICDELETEDSUCCESSFULLY;
        $message_type = 'success';
    } else {
        $message      = _UNPRIVILEGEDATTEMPT;
        $message_type = 'failure';
    }
} else if (isset($_GET['delete_poll']) && eF_checkParameter($_GET['delete_poll'], 'id')) {
    $result = eF_getTableData("f_poll", "users_LOGIN", "id=".$_GET['delete_poll']);                     //Get poll information, to make sure that the user has the priviledge to delete it
    if ($result[0]['users_LOGIN'] == $_SESSION['s_login'] || $_SESSION['s_type'] == 'administrator') {
        eF_deleteTableData("f_users_to_polls", "f_poll_ID=".$_GET['delete_poll']);
        eF_deleteTableData("f_poll", "id=".$_GET['delete_poll']);
		EfrontSearch :: removeText('f_poll', $_GET['delete_poll'], '');

        $message      = _POLLDELETEDSUCCESSFULLY;
        $message_type = 'success';
    } else {
        $message      = _UNPRIVILEGEDATTEMPT;
        $message_type = 'failure';
    }
} else if (isset($_GET['delete_message']) && eF_checkParameter($_GET['delete_message'], 'id')) {
    $result = eF_getTableData("f_messages", "users_LOGIN", "id=".$_GET['delete_message']);                     //Get message information, to make sure that the user has the priviledge to delete it
    if ($result[0]['users_LOGIN'] == $_SESSION['s_login'] || $_SESSION['s_type'] == 'administrator') {
        eF_deleteTableData("f_messages", "id=".$_GET['delete_message']);
		EfrontSearch :: removeText('f_messages', $_GET['delete_message'], '');

        $message      = _MESSAGEDELETEDSUCCESSFULLY;
        $message_type = 'success';
    } else {
        $message      = _UNPRIVILEGEDATTEMPT;
        $message_type = 'failure';
    }
} else if (isset($_GET['add_forum']) || (isset($_GET['edit_forum']) && eF_checkParameter($_GET['edit_forum'], 'id'))) {
    $smarty -> assign("T_CATEGORY", "1");                           //Bogus info to exclude will-be-deprecated code from displaying
    if (isset($_GET['edit_forum'])) {
        $forum_data  = eF_getTableData("f_forums", "*", "id=".$_GET['edit_forum']);
        $post_target = 'forum/forum_add.php?edit_forum='.$_GET['edit_forum'];
    } else {
        $post_target = 'forum/forum_add.php?add_forum=1';
    }
    
    $_SESSION['s_type'] == 'administrator' ? $lessons = eF_getTableDataFlat("lessons", "id,name") : $lessons = eF_getTableDataFlat("lessons, users_to_lessons", "lessons.id,lessons.name", "users_to_lessons.lessons_ID=lessons.id and users_to_lessons.users_LOGIN='".$_SESSION['s_login']."'");
    sizeof($lessons) > 0 ? $lessons = array_combine($lessons['id'], $lessons['name']) : '';
    array_walk($lessons, create_function('&$v', 'mb_strlen($v) > 50 ? $v = mb_substr($v, 0, 50)."..." : null;'));           //Truncate long lesson names
    $lessons[0] = _ALLLESSONS;
    ksort($lessons);
    
    $form = new HTML_QuickForm("forum_add_form", "post", $post_target."&forum_id=".$_GET['forum_id'], "", null, true);  //Build the form
    $form -> addElement('text', 'title', _TITLE, 'class = "inputText"');
    $form -> addRule('title', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
    $form -> addElement('select', 'lessons_ID', _ACCESSIBLEBYUSERSOFLESSON, $lessons);
    $form -> addElement('select', 'status', _STATUS, array('public' => _PUBLIC, 'locked' => _LOCKED, 'invisible' => _INVISIBLE));
    $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputTextarea" style = "width:99%;height:30px"');
    if (isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID']) {
        $form -> setDefaults(array('lessons_ID' => $_SESSION['s_lessons_ID']));
    } elseif ($_GET['forum_id'] && eF_checkParameter($_GET['forum_id'], 'id')) {
        $result        = eF_getTableData("f_forums", "lessons_ID", "id=".$_GET['forum_id']);
        $defaultLesson = $result[0]['lessons_ID'];
    }
    $form -> addElement('submit', 'submit_add_forum', _SUBMIT, 'class = "flatButton"');    

    if (isset($_GET['edit_forum'])) {
        $form -> setDefaults(array('title'      => $forum_data[0]['title'],
                                   'lessons_ID' => $forum_data[0]['lessons_ID'],
                                   'status'     => $forum_data[0]['status'],
                                   'comments'   => $forum_data[0]['comments']));
    }

    if ($form -> isSubmitted() && $form -> validate()) {                                                              //If the form is submitted and validated
        $values = $form -> exportValues();
        $fields = array("title"       => $values['title'], 
                        "lessons_ID"  => $values['lessons_ID'] ? $values['lessons_ID'] : $defaultLesson,
                        "status"      => $values['status'],
                        "comments"    => $values['comments']);

        if (isset($_GET['edit_forum'])) {
            eF_updateTableData("f_forums", $fields, "id=".$_GET['edit_forum']);
			
			$childrenList = array();

			$result = eF_getTableDataFlat("f_forums", "id", "id=".$_GET['edit_forum']);
		
			$childrenTempList = $result['id'];
			//pr($childrenTempList);exit;
			$childrenList = $childrenTempList;
			while (!empty($childrenTempList)) {
				$list = implode(",",$childrenTempList);
				$result = eF_getTableDataFlat("f_forums", "id", "parent_id IN ($list)");
				$childrenTempList = $result['id'];

				if(!empty($childrenTempList)) {
					$childrenList = array_merge($childrenList, $childrenTempList);
				}
			}		
			$list = implode(",",$childrenList);
			eF_updateTableData("f_forums", array("status" => $fields['status']), "id IN ($list)");	
			eF_updateTableData("f_topics", array("status" => $fields['status']), "f_forums_ID IN ($list)");
			
			EfrontSearch :: removeText('f_forums', $_GET['edit_forum'], '');
            EfrontSearch :: insertText($fields['title'], $_GET['edit_forum'], "f_forums", "title");
			if(strlen($fields['comments'])>3){
				EfrontSearch :: insertText(strip_tags($fields['comments']), $_GET['edit_forum'], "f_forums","data");
			}
            $message      = _FORUMUPDATEDSUCCESSFULLY;
            $message_type = 'success';
        } else {
            $fields['users_LOGIN'] = $_SESSION['s_login'];
            $fields['parent_id']   = isset($_GET['forum_id']) ? $_GET['forum_id'] : 0;
            $new_id = eF_insertTableData("f_forums", $fields);
            EfrontSearch :: insertText($fields['title'], $new_id, "f_forums", "title");
			if(strlen($fields['comments'])>3){
				EfrontSearch :: insertText(strip_tags($fields['comments']), $new_id, "f_forums","data");
			}
            $message      = _FORUMADDEDSUCCESSFULLY;
            $message_type = 'success';
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer

    $renderer -> setRequiredTemplate (
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}');
    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

    $smarty -> assign('T_FORUM_ADD_FORM', $renderer -> toArray());                     //Assign the form to the template
    
} else if ((isset($_GET['add_topic']) && eF_checkParameter($_GET['forum_id'], 'id')) || (isset($_GET['edit_topic']) && eF_checkParameter($_GET['edit_topic'], 'id'))) {
    $smarty -> assign("T_CATEGORY", "1");                           //Bogus info to exclude will-be-deprecated code from displaying
    if (isset($_GET['edit_topic'])) {
        $topic_data  = eF_getTableData("f_topics", "*", "id=".$_GET['edit_topic']);
        $post_target = 'forum/forum_add.php?edit_topic='.$_GET['edit_topic'];
    } else {
        $post_target = 'forum/forum_add.php?add_topic=1';
    }
    
    $form = new HTML_QuickForm("topic_add_form", "post", $post_target."&forum_id=".$_GET['forum_id'], "", null, true);  //Build the form
    $form -> addElement('text', 'title', _TITLE, 'class = "inputText"');
    $form -> addRule('title', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
    $form -> addElement('select', 'status', _STATUS, array('public' => _PUBLIC, 'locked' => _LOCKED, 'invisible' => _INVISIBLE));
    $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputTextarea" style = "height:30px;width:99%"');
    $form -> addElement('textarea', 'message', _MESSAGE, 'class = "simpleEditor" style = "width:100%;height:100px;"');
    //$form -> addElement('select', 'forums', _POSTTOFORUMS, array(1,2,3));
    
    $form -> addElement('submit', 'submit_add_topic', _SUBMIT, 'class = "flatButton"');    

    if (isset($_GET['edit_topic'])) {
        $form -> setDefaults(array('title'      => $topic_data[0]['title'],
                                   'status'     => $topic_data[0]['status'],
                                   'comments'   => $topic_data[0]['comments']));
    }

    if ($form -> isSubmitted() && $form -> validate()) {                                                              //If the form is submitted and validated
        $values = $form -> exportValues();
        if (isset($_GET['edit_topic'])) {
            $fields = array("title"       => $values['title'], 
                            "status"      => $values['status'],
                            "comments"    => $values['comments']);
            eF_updateTableData("f_topics", $fields, "id=".$_GET['edit_topic']);
            $message      = _SUCCESFULLYUPDATEDTOPIC;
            $message_type = 'success';
			EfrontSearch :: removeText('f_topics', $_GET['edit_topic'], '');
            EfrontSearch :: insertText($fields['title'], $_GET['edit_topic'], "f_topics", "title");
        } else {
            $fields = array("title"       => $values['title'], 
                            "status"      => $values['status'],
                            "comments"    => $values['comments'],
                            "f_forums_ID" => $_GET['forum_id'],
                            "users_LOGIN" => $_SESSION['s_login'],
                            "timestamp"   => time(),
                            "sticky"      => 0);
            $topic_id = eF_insertTableData("f_topics", $fields);
            $message_fields = array("title"       => $values['title'], 
                                    "body"        => $values['message'],
                                    "f_topics_ID" => $topic_id,
                                    "users_LOGIN" => $_SESSION['s_login'],
                                    "timestamp"   => time(),
                                    "replyto"     => 0);
            $new_id = eF_insertTableData("f_messages", $message_fields);
            EfrontSearch :: insertText($message_fields['title'], $new_id, "f_messages", "title");
			EfrontSearch :: insertText($fields['title'], $topic_id, "f_topics", "title");
			if(strlen($message_fields['body'])>3){
				EfrontSearch :: insertText(strip_tags($message_fields['body']), $new_id, "f_messages","data");
			}			
            $message      = _TOPICSUCCESFULLYCREATED;
            $message_type = 'success';
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer

    $renderer -> setRequiredTemplate (
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}');
    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

    $smarty -> assign('T_TOPIC_ADD_FORM', $renderer -> toArray());                     //Assign the form to the template
} else if ((isset($_GET['add_poll']) && eF_checkParameter($_GET['forum_id'], 'id')) || (isset($_GET['edit_poll']) && eF_checkParameter($_GET['edit_poll'], 'id'))) {
    $smarty -> assign("T_CATEGORY", "1");                           //Bogus info to exclude will-be-deprecated code from displaying
    if (isset($_GET['edit_poll'])) {
        $poll_data  = eF_getTableData("f_poll", "*", "id=".$_GET['edit_poll']);
        $post_target = 'forum/forum_add.php?edit_poll='.$_GET['edit_poll'];
    } else {
        $post_target = 'forum/forum_add.php?add_poll=1';
    }
    
    $form = new HTML_QuickForm("poll_add_form", "post", $post_target."&forum_id=".$_GET['forum_id'], "", null, true);  //Build the form
    $form -> addElement('text', 'poll_subject', _SUBJECT, 'class = "inputText"');
    $form -> addRule('poll_subject', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
    $form -> addElement('textarea', 'poll_text', _QUESTIONTEXT, 'class = "simpleEditor" style = "width:100%;height:80px"');
    $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputTextarea" style = "width:99%;height:30px"');

    $options = array(
        'format'  => 'd m Y',
        'minYear' => date("Y"),
        'maxYear' => date('Y') + 2,
    );
    $form -> addElement('date', 'from', null, $options);
    $form -> addElement('date', 'to', null, $options);
    $form -> setDefaults(array('from' => time(), 'to' => time() + 30 * 86400));                                 //1 month forward
    
    $form -> addElement('submit', 'submit_add_poll', _SUBMIT, 'class = "flatButton"');    

    if (isset($_GET['edit_poll'])) {
        $form -> setDefaults(array('poll_subject'  => $poll_data[0]['title'],
                                   'poll_text'     => $topic_data[0]['question']));
    }

    if ($form -> isSubmitted() || isset($poll_data)) {
        if ($poll_data && !$form -> isSubmitted()) {
            $values['options'] = array_values(unserialize($poll_data[0]['options']));      //We put array_values to make sure that the array starts from zero 
        } else {
            $values = $form -> getSubmitValues();
        }

        //Create each multiple choice from the beginning, this way including any choices the user added himself
        foreach ($values['options'] as $key => $value) {
            $form -> addElement('text', 'options['.$key.']', null, 'class = "inputText inputText_QuestionChoice"');    
            $form -> addRule('options['.$key.']', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
            $form -> addRule('options['.$key.']', _INVALIDFIELDDATA, 'checkParameter', 'text');
            $form -> setDefaults(array('options['.$key.']' => $value));
        }

        if ($form -> validate()) {                    
            $options = serialize(array_values($values['options']));                     //Array values are put here to reindex array, if the keys are not in order
        }
    } else {
        //By default, only 2 options are displayed
        $form -> addElement('text', 'options[0]', _INSERTMULTIPLEQUESTIONS, 'class = "inputText inputText_QuestionChoice"');    
        $form -> addRule('options[0]', _THEFIELD.' "'._INSERTMULTIPLEQUESTIONS.'" '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('options[0]', _INVALIDFIELDDATA, 'checkParameter', 'text');

        $form -> addElement('text', 'options[1]', '', 'class = "inputText inputText_QuestionChoice"');    
        $form -> addRule('options[1]', _THEFIELD.' "'._INSERTMULTIPLEQUESTIONS.'" '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('options[1]', _INVALIDFIELDDATA, 'checkParameter', 'text');
    }

    if ($form -> isSubmitted()) {
        $form_values = $form -> exportValues();
        
        $start = mktime(0, 0, 0, $form_values['from']['m'], $form_values['from']['d'], $form_values['from']['Y']);
        $end   = mktime(0, 0, 0, $form_values['to']['m'], $form_values['to']['d'], $form_values['to']['Y']);
        if ($start > $end) {
            $message      = _ENDDATEMUSTBEBEFORESTARTDATE;
            $message_type = 'failure';
        } else if ($form -> validate()) {            
            if (isset($poll_data)) {                                                                //If we are changing an existing question
                $fields = array('options'         => $options,
                                'title'           => $form_values['poll_subject'],
                                'question'        => $form_values['poll_text'],
                                'timestamp_start' => $start,
                                'timestamp_end'   => $end);

				EfrontSearch :: removeText('f_poll', $poll_data[0]['id'], 'title');
				EfrontSearch :: insertText($fields['title'], $poll_data[0]['id'], "f_poll", "title");

                if (eF_updateTableData("f_poll", $fields, "id=".$poll_data[0]['id'])) {          //Update the question
                    $message      = _SUCCESFULLYUPDATEDPOLL;
                    $message_type = 'success';
                } else {
                    $message      = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';                            
                }
            } else {
                $fields = array('options'           => $options,
                                'title'             => $form_values['poll_subject'],
                                'question'          => $form_values['poll_text'],
                                'timestamp_created' => time(),
                                'f_forums_ID'       => $_GET['forum_id'],
                                'users_LOGIN'       => $_SESSION['s_login'],
                                'comments'          => $form_values['comments'],
                                'timestamp_start'   => $start,
                                'timestamp_end'     => $end);
				$new_id = eF_insertTableData("f_poll", $fields);
				EfrontSearch :: insertText($fields['title'], $new_id, "f_poll", "title");
				if (strlen($fields['question']) > 3) {
					EfrontSearch :: insertText(strip_tags($fields['question']), $new_id, "f_poll","data");
				}
                if ($new_id>0) {
                    $message      = _SUCCESSFULLYADDEDPOLL;
                    $message_type = 'success';
                } else {
                    $message      = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';                            
                }
            }
        }
    }


    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer

    $renderer -> setRequiredTemplate (
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}');
    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

    $smarty -> assign('T_POLL_ADD_FORM', $renderer -> toArray());                     //Assign the form to the template
} else if ((isset($_GET['add_message']) && eF_checkParameter($_GET['topic_id'], 'id')) || (isset($_GET['edit_message']) && eF_checkParameter($_GET['edit_message'], 'id'))) {

    $smarty -> assign("T_CATEGORY", "1");                           //Bogus info to exclude will-be-deprecated code from displaying
    if (isset($_GET['edit_message'])) {
        $message_data  = eF_getTableData("f_messages", "*", "id=".$_GET['edit_message']);
        $post_target = 'forum/forum_add.php?edit_message='.$_GET['edit_message'];
    } else {
        $post_target = 'forum/forum_add.php?add_message=1';
    }
    
    $form = new HTML_QuickForm("message_add_form", "post", $post_target."&topic_id=".$_GET['topic_id'], "", null, true);  //Build the form
    $form -> addElement('text', 'title', _TITLE, 'class = "inputText"');
    $form -> addElement('textarea', 'body', _BODY, 'class = "simpleEditor" style = "width:100%;height:200px"');
    $form -> addElement('hidden', 'replyto', null);

    $form -> addElement('submit', 'submit_add_message', _SUBMIT, 'class = "flatButton"');    
    
    if (isset($_GET['replyto']) && eF_checkParameter($_GET['replyto'], 'id')) {
        $replyto = eF_getTableData("f_messages", "*", "id=".$_GET['replyto']);
        $form -> setDefaults(array('title' => 'Re: '.$replyto[0]['title']));  
        if (isset($_GET['quote'])) {
            $form -> setDefaults(array('body' => '[quote]'.$replyto[0]['body'].'[/quote]'));  
        }
    } else if (isset($_GET['edit_message'])) {
        $form -> setDefaults(array('title' => $message_data[0]['title'],
                                   'body'  => $message_data[0]['body']));
    }

    if ($form -> isSubmitted() && $form -> validate()) {                                                              //If the form is submitted and validated
        $values = $form -> exportValues();
        if (isset($_GET['edit_message'])) {
            $fields = array("title" => $values['title'], 
                            "body"  => $values['body']);
            eF_updateTableData("f_messages", $fields, "id=".$_GET['edit_message']);
			EfrontSearch :: removeText('f_messages', $_GET['edit_message'], '');
            EfrontSearch :: insertText($fields['title'], $_GET['edit_message'], "f_messages", "title");
			if(strlen($fields['body'])>3){
				EfrontSearch :: insertText(strip_tags($fields['body']), $_GET['edit_message'], "f_messages","data");
			}	
            $message      = _SUCCESFULLYUPDATEDMESSAGE;
            $message_type = 'success';
        } else {
            $fields = array("title"       => $values['title'], 
                            "body"        => $values['body'],
                            "f_topics_ID" => $_GET['topic_id'],
                            "users_LOGIN" => $_SESSION['s_login'],
                            "timestamp"   => time(),
                            "replyto"     => $values['replyto'] ? $values['replyto'] : 0);
            $new_id = eF_insertTableData("f_messages", $fields);
            EfrontSearch :: insertText($fields['title'], $new_id, "f_messages", "title");
			if(strlen($fields['body'])>3){
				EfrontSearch :: insertText(strip_tags($fields['body']), $new_id, "f_messages","data");
			}		
            $message      = _SUCCESFULLYPOSTEDMESSAGE;
            $message_type = 'success';
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer

    $renderer -> setRequiredTemplate (
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}');
    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);          //Set javascript error messages
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created

    $smarty -> assign('T_MESSAGE_ADD_FORM', $renderer -> toArray());                     //Assign the form to the template
}

!isset($forum_config['allow_html']) || $forum_config['allow_html'] ? $load_editor = true : $load_editor = false;
$smarty -> assign("T_HEADER_EDITOR",  $load_editor);
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", $loadScripts);
$smarty -> display("forum/forum_add.tpl");

?>

