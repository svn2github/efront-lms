<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

if (!$currentUser -> coreAccess['forum'] || $currentUser -> coreAccess['forum'] == 'change') {
    $_change_ = 1;
}

try {
    if ($GLOBALS['configuration']['disable_forum'] == 1) {
        eF_redirect("".basename($_SERVER['PHP_SELF']));
    }

    $loadScripts[] = 'includes/forum';

    $forums = f_forums :: getAll("f_forums");
    //@todo: if forum of lesson deactivated by professor not display it in list
    if (!$_admin_) {
        $userLessons = $currentUser -> getEligibleLessons();
        foreach ($forums as $key => $value) {
            //This takes the forum that belongs to this lesson, as well as general forums
            if ($value['lessons_ID'] && !in_array($value['lessons_ID'], array_keys($userLessons))) {
                unset($forums[$key]);
            }
        }
    }

    $legalForumValues = array_keys($forums);
    if (sizeof($legalForumValues) > 0) {
        $legalTopicValues = eF_getTableDataFlat("f_topics", "id", "f_forums_ID in (".implode(",", $legalForumValues).")");
        $legalTopicValues = $legalTopicValues['id'];
        $legalPollValues = eF_getTableDataFlat("f_poll", "id", "f_forums_ID in (".implode(",", $legalForumValues).")");
        $legalPollValues = $legalPollValues['id'];

        $legalMessageValues = array();
        if (sizeof($legalTopicValues) > 0) {
         $legalMessageValues = eF_getTableDataFlat("f_messages", "id", "f_topics_ID in (".implode(",", $legalTopicValues).")");
         $legalMessageValues = $legalMessageValues['id'];
        }
    }
    $forumTree = f_forums :: getForumTree($forums);

    if (isset($_GET['forum']) && !in_array($_GET['forum'], $legalForumValues)) {
        unset($_GET['forum']);
    }

    //Get forum configuration values
    $forum_config = eF_getTableDataFlat("f_configuration", "*");
    sizeof($forum_config) > 0 ? $forum_config = array_combine($forum_config['name'], $forum_config['value']) : $forum_config = array();

    $smarty -> assign("T_FORUM_CONFIG", $forum_config);

    $user_type = eF_getUserBasicType($_SESSION['s_login']);
    $smarty -> assign("T_USER",$user_type);

    if ($_GET['type'] == 'forum' && isset($_GET['delete']) && in_array($_GET['delete'], $legalForumValues)) {
        try {
            $forum = new f_forums($_GET['delete']);
            $forum -> delete();
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo rawurlencode($e -> getMessage()).' ('.$e -> getCode().')';
        }
        exit;
    } else if ($_GET['type'] == 'topic' && isset($_GET['delete']) && in_array($_GET['delete'], $legalTopicValues)) {
        try {
            $topic = new f_topics($_GET['delete']);
            $topic -> delete();
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo rawurlencode($e -> getMessage()).' ('.$e -> getCode().')';
        }
        exit;
    } else if ($_GET['type'] == 'poll' && isset($_GET['delete']) && in_array($_GET['delete'], $legalPollValues)) {
        try {
            $poll = new f_poll($_GET['delete']);
            $poll -> delete();
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo rawurlencode($e -> getMessage()).' ('.$e -> getCode().')';
        }
        exit;
    } else if ($_GET['type'] == 'message' && isset($_GET['delete']) && in_array($_GET['delete'], $legalMessageValues)) {
        try {
            $forum = new f_messages($_GET['delete']);
            $forum -> delete();
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo rawurlencode($e -> getMessage()).' ('.$e -> getCode().')';
        }
        exit;
    } else if ($_GET['type'] == 'forum' && (!$_student_ || $forum_config['students_add_forums']) && (isset($_GET['add']) || (isset($_GET['edit']) && in_array($_GET['edit'], $legalForumValues)))) {
  $load_editor = 1;
        if ($_admin_) {
            $lessons = eF_getTableDataFlat("lessons", "id, name", "active=1");
            if (sizeof($lessons) > 0) {
                //Get every lesson's name
                $lessons = array_combine($lessons['id'], $lessons['name']);
            }
        } else {
            $lessons = $currentUser -> getLessons(true);
            foreach ($lessons as $key => $value) {
                //Keep only names
                $lessons[$key] = $value -> lesson['name'];
            }
        }
        //Truncate long lesson names
        array_walk($lessons, create_function('&$v', 'mb_strlen($v) > 50 ? $v = mb_substr($v, 0, 50)."..." : null;'));
        $lessons[0] = _ALLLESSONS;
        ksort($lessons);

        $entityForm = new HTML_QuickForm("forum_add_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=forum".(isset($_GET['edit']) ? '&edit='.$_GET['edit'] : '&add=1')."&type=forum&parent_forum_id=".$_GET['parent_forum_id'], "", null, true); //Build the form
        $entityForm -> addElement('select', 'lessons_ID', _ACCESSIBLEBYUSERSOFLESSON, $lessons);

        if (isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID']) {
            $entityForm -> setDefaults(array('lessons_ID' => $_SESSION['s_lessons_ID']));
        } elseif ($_GET['parent_forum_id'] && in_array($_GET['parent_forum_id'], $legalForumValues)) {
            $entityForm -> setDefaults(array('lessons_ID' => $result[0]['lessons_ID']));
        }

        $entityName = 'f_forums';
        $legalValues = $legalForumValues;
        include("entity.php");
    } else if ($_GET['type'] == 'topic' && (isset($_GET['add']) || (isset($_GET['edit']) && in_array($_GET['edit'], $legalTopicValues)))) {
        $load_editor = 1;
        $entityForm = new HTML_QuickForm("topic_add_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=forum".(isset($_GET['edit']) ? '&edit='.$_GET['edit'] : '&add=1')."&type=topic&forum_id=".$_GET['forum_id'], "", null, true); //Build the form

        $entityName = 'f_topics';
        $legalValues = $legalTopicValues;
        include("entity.php");

    } else if ($_GET['type'] == 'poll' && (isset($_GET['add']) || (isset($_GET['edit']) && in_array($_GET['edit'], $legalPollValues)))) {
        $load_editor = 1;
        $entityForm = new HTML_QuickForm("poll_add_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=forum".(isset($_GET['edit']) ? '&edit='.$_GET['edit'] : '&add=1')."&type=poll&forum_id=".$_GET['forum_id'], "", null, true); //Build the form

        $entityName = 'f_poll';
        $legalValues = $legalPollValues;
        include("entity.php");
    } else if ($_GET['type'] == 'message' && (isset($_GET['add']) || (isset($_GET['edit']) && in_array($_GET['edit'], $legalMessageValues)))) {
        $load_editor = 1;
        $entityForm = new HTML_QuickForm("message_add_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=forum".(isset($_GET['edit']) ? '&edit='.$_GET['edit'] : '&add=1')."&type=message&topic_id=".$_GET['topic_id'], "", null, true); //Build the form

        $entityName = 'f_messages';
        $legalValues = $legalMessageValues;
        include("entity.php");
    } else if (isset($_GET['config'])) {
        $form = new HTML_QuickForm("forum_admin_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=forum&config=1", "", null, true); //Build the form
        $form -> addElement('select', 'allow_html', _ALLOWHTMLFPM, array(1 => _YES, 0 => _NO));
        $form -> addElement('select', 'polls', _ACTIVATEPOLLS, array(1 => _YES, 0 => _NO));
        $form -> addElement('select', 'forum_attachments', _ALLOWATTACHMENTSINF, array(1 => _YES, 0 => _NO));
        $form -> addElement('select', 'students_add_forums', _USERSMAYADDFORUMS, array(0 => _NO, 1 => _YES, ));
        $form -> addElement('text', 'pm_quota', _PMQUOTA, 'class = "inputText" style = "width:40px"');
        $form -> addElement('text', 'pm_attach_quota', _PMATTACHMENTSQUOTA, 'class = "inputText" style = "width:40px"');
        $form -> addRule('pm_quota', _THEFIELD.' "'._PMQUOTA.'" '._MUSTBENUMERIC, 'numeric', null, 'client');
        $form -> addRule('pm_attach_quota', _THEFIELD.' "'._PMATTACHMENTSQUOTA.'" '._MUSTBENUMERIC, 'numeric', null, 'client');

        $form -> addElement('submit', 'submit_settings', _SUBMIT, 'class = "flatButton"');

        $current_values = eF_getTableDataFlat("f_configuration", "*");
        $current_values = array_combine($current_values['name'], $current_values['value']);
        $form -> setDefaults($current_values);

        if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
            $values = $form -> exportValues();
            eF_deleteTableData("f_configuration");

            $fields[] = array('name' => 'allow_html', "value" => $values['allow_html'] ? 1 : 0);
            $fields[] = array('name' => 'polls', "value" => $values['polls'] ? 1 : 0);
            $fields[] = array('name' => 'forum_attachments', "value" => $values['forum_attachments'] ? 1 : 0);
            $fields[] = array('name' => 'students_add_forums', "value" => $values['students_add_forums'] ? 1 : 0);
            $fields[] = array('name' => 'pm_quota', "value" => $values['pm_quota']);
            $fields[] = array('name' => 'pm_attach_quota', "value" => $values['pm_attach_quota']);

            foreach ($fields as $field) {
                eF_insertTableData("f_configuration", array("name" => $field['name'], "value" => $field['value']));
            }

            $message = _SUCCESSFULLYINSERTEDVALUES;
            $message_type = 'success';
        }

        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty); //Create a smarty renderer

        $renderer -> setRequiredTemplate (
   '{$html}{if $required}
        &nbsp;<span class = "formRequired">*</span>
    {/if}');
        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created

        $smarty -> assign('T_CONFIGURATION_FORM', $renderer -> toArray()); //Assign the form to the template

    } else {
        if (isset($_GET['topic']) && eF_checkParameter($_GET['topic'], 'id')) {
            $topic = eF_getTableData("f_topics", "*", "id=".$_GET['topic']);
            $user_posts = eF_getTableDataFlat("f_messages, users", "distinct login, count(f_messages.id) as num", "users.login = f_messages.users_LOGIN group by login");
//pr($user_posts);
            $user_posts = array_combine($user_posts['login'], $user_posts['num']);
            $posts = eF_getTableData("f_messages, users", "users.avatar, users.user_type, f_messages.*", "users.login = f_messages.users_LOGIN and f_topics_ID=".$_GET['topic'], "timestamp");

            foreach ($posts as &$post) {
                $post['body'] = preg_replace("/\[quote\](.*)\[\/quote\]/", "<div class = 'quote'><b>"._QUOTE.":</b><div class = 'quoteBody'>\$1</div></div>", $post['body']);
            }
            //    $forum      = eF_getTableData("f_forums", "*", "id=".$topic[0]['f_forums_ID']);

            $smarty -> assign("T_USER_POSTS", $user_posts);

            $smarty -> assign("T_POSTS", $posts);
            $smarty -> assign("T_TOPIC", $topic[0]);
            //    $smarty -> assign("T_FORUM", $forum[0]);

            $current_topic[0]['viewed_by'] ? $viewed_by = unserialize($topic[0]['viewed_by']) : $viewed_by = array();

            if (!in_array($_SESSION['s_login'], $viewed_by)) {
                $viewed_by[] = $_SESSION['s_login'];
                $fields_update = array("views" => ++$topic[0]['views'],
                               "viewed_by" => serialize($viewed_by));
                eF_updateTableData("f_topics", $fields_update, "id=".$_GET['topic']);
            }

            $parent_forum = $topic[0]['f_forums_ID'];
        } else if (isset($_GET['poll']) && in_array($_GET['poll'], $legalPollValues)) {
            $result = eF_getTableData("f_users_to_polls", "*", "f_poll_ID=".$_GET['poll']." and users_LOGIN='".$_SESSION['s_login']."'");
            if (sizeof($result) > 0 || (isset($_GET['action']) && $_GET['action'] == 'view') || ($currentUser -> coreAccess['forum'] && $currentUser -> coreAccess['forum'] != 'change')) {
                $smarty -> assign("T_ACTION", "view");
            }

            $poll_data = eF_getTableData("f_poll", "*", "id=".$_GET['poll']);
            $parent_forum = $poll_data[0]['f_forums_ID'];
            $poll_data[0]['options'] = array_values(unserialize($poll_data[0]['options'])); //Array values are put here to reindex array, if the keys are not in order
            $poll_votes = eF_getTableData("f_users_to_polls", "*", "f_poll_ID=".$_GET['poll']);

            $poll_data[0]['timestamp_end'] > time() ? $poll_data[0]['isopen'] = true : $poll_data[0]['isopen'] = false;

            $votes_distrib = array();
            for ($i = 0; $i < sizeof($poll_data[0]['options']); $i++){
                $votes_distrib[$i]['vote'] = 0;
            }

            for ($i = 0; $i < sizeof($poll_votes); $i++){
                $votes_distrib[$poll_votes[$i]['vote']]['vote']++;
            }

            for ($i = 0; $i < sizeof($votes_distrib); $i++){
                $votes_distrib[$i]['perc'] = round($votes_distrib[$i]['vote'] / sizeof($poll_votes), 2);
                $votes_distrib[$i]['text'] = $poll_data[0]['options'][$i];
                $votes_distrib[$i]['width'] = $votes_distrib[$i]['perc'] * 200;
            }

            $smarty -> assign("T_POLL_VOTES", $votes_distrib);
            $smarty -> assign("T_POLL_TOTALVOTES", sizeof($poll_votes));

            $form = new HTML_QuickForm("poll_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=forum&poll=".$_GET['poll'], "", null, true); //Build the form
            foreach ($poll_data[0]['options'] as $key => $option) {
                $group[] = HTML_Quickform :: createElement('radio', 'vote', null, $option, $key);
            }
            $form -> addGroup($group, 'options', '', '<br/>');
            $form -> addRule('options', _PLEASEPICKANOPTION, 'required', null, 'client');
            $form -> addElement('submit', 'submit_poll', _VOTE, 'class = "flatButton"');

            if ($form -> isSubmitted() && $form -> validate()) {
                $values = $form -> exportValues();
                //pr($values);
                //debug();
                $res = eF_getTableData("f_users_to_polls", "*", "f_poll_ID=".$values['options']['vote']." and users_LOGIN='".$currentUser -> user['login']."'");
                //debug(false);
                if (sizeof($res) > 0){
                    $message = _YOUHAVEALREADYVOTED;
                    $message_type = 'failure';
                } else {
                    $fields = array('f_poll_ID' => $_GET['poll'],
                            'users_LOGIN' => $_SESSION['s_login'],
                            'vote' => $values['options']['vote'],
                            'timestamp' => time());

                    if (eF_insertTableData("f_users_to_polls", $fields)){
                        $message = _SUCCESFULLYVOTED;
                        $message_type = 'success';
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=forum&poll=".$_GET['poll']);
                    } else {
                        $message = _SOMEPROBLEMEMERGED;
                        $message_type = 'failure';
                    }
                }
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty); //Create a smarty renderer
            $form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created
            $smarty -> assign('T_POLL_FORM', $renderer -> toArray()); //Assign the form to the template

            $smarty -> assign("T_POLL", $poll_data[0]);


        } else {
            $messages = eF_getTableDataFlat("f_messages", "f_topics_ID"); //Get all the forum messages
            $messages = array_count_values($messages['f_topics_ID']); //Count the messages contained in each topic
            $forum_topics = eF_getTableDataFlat("f_topics", "f_forums_ID"); //Get all the forum topics
            $count = 0;
            foreach ($messages as $key => $value) { //This way we may calculate the number of messages contained in each topic, without further queries
                $forum_messages[$forum_topics['f_forums_ID'][$count++]] += $value;
            }
            $forum_topics = array_count_values($forum_topics['f_forums_ID']); //Count the number of topics contained in each forum
            $forum_polls = eF_getTableDataFlat("f_poll", "f_forums_ID"); //Get all the forum polls
            $forum_polls = array_count_values($forum_polls['f_forums_ID']); //Count the number of polls contained in each forum

            foreach ($forumTree as $key => $value) { //Find the last post for each forum
                if ($key) {
                    $result = eF_getTableData("f_topics, f_messages", "f_messages.*", "f_topics.id=f_messages.f_topics_ID and f_topics.f_forums_ID=$key", "timestamp desc limit 1");
                    sizeof($result) > 0 ? $last_post[$key] = $result[0] : '';
                }
            }

            foreach ($forumTree as $key => $value) { //Calculate recursively the number of topics and messages in each forum, as well as the last post in each forum
                $stats = f_forums :: calculateForumStats($forumTree, $key, $forum_topics, $forum_polls, $forum_messages, $last_post);
                $forums[$key]['topics'] = $stats['topics'];
                $forums[$key]['polls'] = $stats['polls'];
                $forums[$key]['messages'] = $stats['messages'];
                $forums[$key]['last_post'] = $stats['last_post'];
            }

            unset($forums[0]); //Unset node with id 0, since this refers to the root node (which does not exist)
            $forums = eF_multiSort($forums, 'title'); //Show forums in alphabetical order

            $smarty -> assign("T_FORUMS", $forums);

            isset($_GET['forum']) && eF_checkParameter($_GET['forum'], 'id') ? $parent_forum = $_GET['forum'] : $parent_forum = 0;
            $smarty -> assign("T_PARENT_FORUM", $parent_forum);
            $smarty -> assign("T_HAS_SUBFORUMS", sizeof($forumTree[$_GET['forum']]));

            $polls = eF_getTableData("f_poll", "*", "f_forums_ID=".$parent_forum);
            $topics = eF_getTableData("f_topics", "*", "f_forums_ID=".$parent_forum);
            foreach ($topics as &$topic) {
                $result = eF_getTableDataFlat("f_messages", "users_LOGIN, id, timestamp, body", "f_topics_ID=".$topic['id']);
                $topic['messages'] = sizeof($result['timestamp']);
                if (sizeof($result) > 0) { //find the topic's last post
                    arsort($result['timestamp']);
                    $key = key($result['timestamp']);
                    $topic['last_post'] = array('id' => $result['id'][$key], 'users_LOGIN' => $result['users_LOGIN'][$key], 'timestamp' => $result['timestamp'][$key]);
                    $topic['first_message'] = strip_tags($result['body'][0]);
                }
                $last_posts[] = $topic['last_post']['timestamp']; //This array will be used for sorting according to last post
            }
            array_multisort($last_posts, SORT_DESC , $topics); //Sort topics so that those with most recent messages are displayed first

            foreach ($polls as &$poll) {
                $result = eF_getTableDataFlat("f_users_to_polls", "count(*)", "vote != 0 and f_poll_ID=".$poll['id']);
                $poll['votes'] = $result['count(*)'][0];
            }
            $smarty -> assign("T_FORUM_TOPICS", $topics);
            $smarty -> assign("T_FORUM_POLLS", $polls);

            if ((!$currentUser -> coreAccess['forum'] || $currentUser -> coreAccess['forum'] == 'change') && ($currentUser -> user['user_type'] != 'student' || (isset($forum_config) && $forum_config['students_add_forums']))) {
                $forum_options = array(1 => array('text' => _NEWFORUM, 'image' => "16x16/add.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=forum&add=1&type=forum&parent_forum_id=$parent_forum&popup=1", 'onclick' => "eF_js_showDivPopup('"._NEWFORUM."', 2)", 'target' => "POPUP_FRAME"));
                $smarty -> assign("T_FORUM_OPTIONS", $forum_options);
            }

        }

        //Calculate the forum parents, so the title may be created and displayed
        while ($parent_forum != 0 && $count++ < 100) { //Count is put to prevent an unexpected infinite loop
            $result = eF_getTableData("f_forums", "id,title,parent_id,lessons_ID", "id=$parent_forum");
            $parent_forum = $result[0]['parent_id'];
            $parents[$result[0]['id']] = $result[0]['title'];
            $firstNode = $result[0]['lessons_ID'];

        }
        //echo $firstNode;
        $smarty -> assign("T_FIRSTNODE", $firstNode);
        //pr($parents);
        $smarty -> assign("T_FORUM_PARENTS", array_reverse($parents, true));

    }

} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
?>
