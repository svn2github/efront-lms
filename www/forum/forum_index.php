<?php
/**
* forum root page
*
* This page provides links to all forum topics 
*
* @package eFront
* @version 0.1
*/

$debug_TimeStart = microtime(true);     //Debugging timer - initialization

session_cache_limiter('none');
session_start();

$path = "../../libraries/";

/** Configuration file.*/
require_once $path."configuration.php";
$debug_InitTime = microtime(true) - $debug_TimeStart;       //Debugging timer - time spent on file inclusion

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

/*Check the user type. If the user is not valid, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
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
        $smarty -> assign("T_CURRENT_USER", $currentUser);
        
        if ($currentUser -> coreAccess['forum'] == 'hidden') {
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

/**Search module is used to display the search field and perform the searches*/
//include ("../module_search.php");
$load_editor  = false;
$loadScripts = array('EfrontScripts', 'scriptaculous/prototype');

//Get forum configuration values
$forum_config = eF_getTableDataFlat("f_configuration", "*");
sizeof($forum_config) > 0 ? $forum_config = array_combine($forum_config['name'], $forum_config['value']) : $forum_config = array();

$smarty -> assign("T_FORUM_CONFIG", $forum_config);
/**Added Session variable for search results*/
$_SESSION['referer'] = $_SERVER['REQUEST_URI'];
$user_type = eF_getUserBasicType($_SESSION['s_login']);
$smarty ->assign("T_USER",$user_type);
    
if (isset($_GET['topic']) && eF_checkParameter($_GET['topic'], 'id')) {
    $topic      = eF_getTableData("f_topics", "*", "id=".$_GET['topic']);
    $user_posts = eF_getTableDataFlat("f_messages, users", "distinct login, count(id)", "users.login = f_messages.users_LOGIN group by login");
    $user_posts = array_combine($user_posts['login'], $user_posts['count(id)']);
    $posts      = eF_getTableData("f_messages, users", "users.avatar, users.user_type, f_messages.*", "users.login = f_messages.users_LOGIN and f_topics_ID=".$_GET['topic'], "timestamp");

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
        $fields_update = array("views"     => ++$topic[0]['views'],
                               "viewed_by" => serialize($viewed_by));
        eF_updateTableData("f_topics", $fields_update, "id=".$_GET['topic']);
    }

    $parent_forum = $topic[0]['f_forums_ID'];
} else if (isset($_GET['poll']) && eF_checkParameter($_GET['poll'], 'id')) {
    $result       = eF_getTableData("f_users_to_polls", "*", "f_poll_ID=".$_GET['poll']." and users_LOGIN='".$_SESSION['s_login']."'");       
    if (sizeof($result) > 0 || (isset($_GET['action']) && $_GET['action'] == 'view') || ($currentUser -> coreAccess['forum'] && $currentUser -> coreAccess['forum'] != 'change')) {            
        $smarty -> assign("T_ACTION", "view");
    }

    $poll_data               = eF_getTableData("f_poll", "*", "id=".$_GET['poll']);
    $parent_forum            = $poll_data[0]['f_forums_ID'];
    $poll_data[0]['options'] = array_values(unserialize($poll_data[0]['options']));                     //Array values are put here to reindex array, if the keys are not in order
    $poll_votes              = eF_getTableData("f_users_to_polls", "*", "f_poll_ID=".$_GET['poll']);
    
    $poll_data[0]['timestamp_end'] > time() ? $poll_data[0]['isopen'] = true : $poll_data[0]['isopen'] = false;
    
    $votes_distrib = array();
    for ($i = 0; $i < sizeof($poll_data[0]['options']); $i++){
         $votes_distrib[$i]['vote'] = 0;    
    }

    for ($i = 0; $i < sizeof($poll_votes); $i++){
        $votes_distrib[$poll_votes[$i]['vote']]['vote']++;
    }

    for ($i = 0; $i < sizeof($votes_distrib); $i++){
        $votes_distrib[$i]['perc']  = round($votes_distrib[$i]['vote'] / sizeof($poll_votes), 2);
        $votes_distrib[$i]['text']  = $poll_data[0]['options'][$i];
        $votes_distrib[$i]['width'] = $votes_distrib[$i]['perc'] * 200;
    }

    $smarty -> assign("T_POLL_VOTES", $votes_distrib);
    $smarty -> assign("T_POLL_TOTALVOTES", sizeof($poll_votes));

    $form = new HTML_QuickForm("poll_form", "post", "forum/forum_index.php?poll=".$_GET['poll'], "", null, true);  //Build the form
    foreach ($poll_data[0]['options'] as $key => $option) {
        $group[] = HTML_Quickform :: createElement('radio', 'vote', null, $option, $key);
    }
    $form -> addGroup($group, 'options', '', '<br/>');
    $form -> addElement('submit', 'submit_poll', _VOTE, 'class = "flatButton"');    

    if ($form -> isSubmitted() && $form -> validate()) {
        $values = $form -> exportValues();
        $res    = eF_getTableData("f_users_to_polls", "*", "f_poll_ID=".$values['vote']." and users_LOGIN='".$_SESSION['s_login']."'");       
        if (sizeof($res) > 0){
            $message      = _YOUHAVEALREADYVOTED;
            $message_type = 'failure';
        } else {
            $fields = array('f_poll_ID'   => $_GET['poll'],
                            'users_LOGIN' => $_SESSION['s_login'],
                            'vote'        => $values['options']['vote'],
                            'timestamp'   => time());

            if (eF_insertTableData("f_users_to_polls", $fields)){
                $message      = _SUCCESFULLYVOTED;
                $message_type = 'success';
                header("location:forum_index.php?poll=".$_GET['poll']);
            } else {
                $message      = _SOMEPROBLEMEMERGED;
                $message_type = 'failure';
            }           
        }
    }
    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);                  //Create a smarty renderer
    $form -> accept($renderer);                                                     //Assign this form to the renderer, so that corresponding template code is created
    $smarty -> assign('T_POLL_FORM', $renderer -> toArray());                     //Assign the form to the template
    
    $smarty -> assign("T_POLL", $poll_data[0]);
    

} else {
    /*
    We now need to calculate the forum tree, as well as the total topics and messages for each of the forums
    */
    $smarty -> assign('T_CTG', 'testctg');
    $forums        = array();
	if ($_SESSION['s_type'] == 'administrator') {
		$result_forums = eF_getTableData("f_forums", "*");
	} elseif ($_SESSION['s_type'] == 'professor') {
		$resultLessons = eF_getTableDataFlat("users_to_lessons","lessons_ID","users_LOGIN='".$_SESSION['s_login']."'");
		$lessonsList = implode(",",$resultLessons['lessons_ID']);
		$result_forums = eF_getTableData("f_forums", "*", "f_forums.lessons_ID=0 OR f_forums.lessons_ID IN ($lessonsList)");
	} else {
		$resultLessons = eF_getTableDataFlat("users_to_lessons","lessons_ID","users_LOGIN='".$_SESSION['s_login']."'");
		$lessonsList = implode(",",$resultLessons['lessons_ID']);
		$result_forumsTemp = eF_getTableData("f_forums", "*", "f_forums.lessons_ID=0 OR f_forums.lessons_ID IN ($lessonsList)");
		$result_forums  = array();
		foreach ($result_forumsTemp as $key => $value) {
			if ($value['lessons_ID'] != 0) {
				$lessonTemp = new EfrontLesson($value['lessons_ID']);  //if forum of lesson deactivated by professor not display it in list
				if ($lessonTemp -> options['forum'] != 0) {
					$result_forums[] = $result_forumsTemp[$key];
				}
			} else {
				$result_forums[] = $result_forumsTemp[$key];
			}	
		}
	}

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

    $messages     = eF_getTableDataFlat("f_messages", "f_topics_ID");   //Get all the forum messages
    $messages     = array_count_values($messages['f_topics_ID']);       //Count the messages contained in each topic
    $forum_topics = eF_getTableDataFlat("f_topics", "f_forums_ID");     //Get all the forum topics
    $count        = 0;
    foreach ($messages as $key => $value) {                             //This way we may calculate the number of messages contained in each topic, without further queries
        $forum_messages[$forum_topics['f_forums_ID'][$count++]] += $value;
    }
    $forum_topics = array_count_values($forum_topics['f_forums_ID']);   //Count the number of topics contained in each forum
    $forum_polls  = eF_getTableDataFlat("f_poll", "f_forums_ID");       //Get all the forum polls
    $forum_polls  = array_count_values($forum_polls['f_forums_ID']);    //Count the number of polls contained in each forum

    foreach ($forum_tree as $key => $value) {                           //Find the last post for each forum
        $result = eF_getTableData("f_topics, f_messages", "f_messages.*", "f_topics.id=f_messages.f_topics_ID and f_topics.f_forums_ID=$key", "timestamp desc limit 1");
        sizeof($result) > 0 ? $last_post[$key] = $result[0] : '';
    }

    foreach ($forum_tree as $key => $value) {                           //Calculate recursively the number of topics and messages in each forum, as well as the last post in each forum
        $stats = eF_local_calculateForumStats($forum_tree, $key, $forum_topics, $forum_polls, $forum_messages, $last_post);
        $forums[$key]['topics']    = $stats['topics'];
        $forums[$key]['polls']     = $stats['polls'];
        $forums[$key]['messages']  = $stats['messages'];
        $forums[$key]['last_post'] = $stats['last_post'];
    }

    unset($forums[0]);                                                    //Unset node with id 0, since this refers to the root node (which does not exist)  
	$forums = eF_multiSort($forums, 'title');  //Show forums in alphabetical order
	
	$smarty -> assign("T_FORUMS", $forums);

    isset($_GET['forum']) && eF_checkParameter($_GET['forum'], 'id') ? $parent_forum = $_GET['forum'] : $parent_forum = 0;
	$smarty -> assign("T_PARENT_FORUM", $parent_forum);
    $smarty -> assign("T_HAS_SUBFORUMS", sizeof($forum_tree[$_GET['forum']]));
    
    $polls  = eF_getTableData("f_poll",   "*", "f_forums_ID=".$parent_forum);
    $topics = eF_getTableData("f_topics", "*", "f_forums_ID=".$parent_forum);
    foreach ($topics as &$topic) {
        $result = eF_getTableDataFlat("f_messages", "users_LOGIN, id, timestamp, body", "f_topics_ID=".$topic['id']);
        $topic['messages']  = sizeof($result['timestamp']);
        if (sizeof($result) > 0) {                                          //find the topic's last post
            arsort($result['timestamp']);
            $key                = key($result['timestamp']);
            $topic['last_post']     = array('id' => $result['id'][$key], 'users_LOGIN' => $result['users_LOGIN'][$key], 'timestamp' => $result['timestamp'][$key]);
            $topic['first_message'] = strip_tags($result['body'][0]);
        }
        $last_posts[] = $topic['last_post']['timestamp'];                    //This array will be used for sorting according to last post
    }
    array_multisort($last_posts, SORT_DESC , $topics);                       //Sort topics so that those with most recent messages are displayed first

    foreach ($polls as &$poll) {
        $result        = eF_getTableDataFlat("f_users_to_polls", "count(*)", "vote != 0 and f_poll_ID=".$poll['id']);
        $poll['votes'] = $result['count(*)'][0];
    }
    $smarty -> assign("T_FORUM_TOPICS", $topics);
    $smarty -> assign("T_FORUM_POLLS", $polls);

    if ((!$currentUser -> coreAccess['forum'] || $currentUser -> coreAccess['forum'] == 'change') && ($currentUser -> user['user_type'] != 'student' || (isset($forum_config) && $forum_config['students_add_forums']))) {
        $forum_options = array( 1=> array('text' => _NEWFORUM, 'image' => "16x16/add2.png", 'href' => "forum/forum_add.php?add_forum=1&forum_id=$parent_forum", 'onClick' => "eF_js_showDivPopup('"._NEWFORUM."', 2)", 'target' => "POPUP_FRAME",),
								0=> array ('text' => _FORUMS, 'image' => "16x16/undo.png", 'href' => "forum/forum_index.php"));
        $smarty -> assign("T_FORUM_OPTIONS", $forum_options);
    }

}

//Calculate the forum parents, so the title may be created and displayed
while ($parent_forum != 0 && $count++ < 100) {                                                              //Count is put to prevent an unexpected infinite loop
    $result                    = eF_getTableData("f_forums", "id,title,parent_id,lessons_ID", "id=$parent_forum");
    $parent_forum              = $result[0]['parent_id'];
    $parents[$result[0]['id']] = $result[0]['title'];
	$firstNode = $result[0]['lessons_ID'];
	
}
//echo $firstNode;
$smarty -> assign("T_FIRSTNODE", $firstNode);
//pr($parents);
$smarty -> assign("T_FORUM_PARENTS", array_reverse($parents, true));

/**This part is used at the page header*/
$css = eF_getTableData("configuration", "value", "name='css'");
if ($css && eF_checkParameter($css[0]['value'], 'filename') && is_file(G_ROOTPATH.'www/css/custom_css/'.$css[0]['value'])) {
    $smarty -> assign("T_HEADER_CSS", $css[0]['value']);
} else {
    $smarty -> assign("T_HEADER_CSS", "normal.css");
}
$smarty -> assign("T_CURRENT_CTG", $ctg);
$smarty -> assign("T_HEADER_EDITOR", $load_editor);


if (isset($_POST['search_text'])) {
    $title = $title.' &raquo '._SEARCHRESULTS;
}

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    if (isset($_GET['message_type'])) {
        $message_type = $_GET['message_type'];
    }
}
$smarty -> assign("T_CURRENT_USER", $currentUser);
if ($message) {
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';
}

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", $loadScripts);

try{
    $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
    $lesson_name = $currentLesson -> lesson['name'];
}
catch (Exception $e){
    $lesson_name = "";
}


$smarty -> assign('T_LESSONNAME', $lesson_name);
$smarty -> assign('T_CURRENT', $current);

$smarty -> assign('T_MENUCTG', 'forum');

$smarty -> assign("T_SHOWFOOTER", $GLOBALS['configuration']['show_footer']);
$smarty -> assign("T_ADMINEMAIL", $GLOBALS['configuration']['system_email']);

$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_SEARCH_MESSAGE", $search_message);

//$smarty -> assign("T_UNREAD_MESSAGES", eF_getUnreadMessagesNumber());

$smarty -> load_filter('output', 'eF_template_formatTimestamp');
$smarty -> load_filter('output', 'eF_template_formatLogins');

$debug_timeBeforeSmarty = microtime(true) - $debug_TimeStart;
$smarty -> display('forum/forum_index.tpl');
$debug_timeAfterSmarty = microtime(true) - $debug_TimeStart;

$debug_TotalTime = microtime(true) - $debug_TimeStart;

if (G_DEBUG) {
    echo "
    <div onclick = 'this.style.display=\"none\"' style = 'position:absolute;top:0px;right:0px;background-color:lightblue;border:1px solid black' >
    <table>
        <tr><th colspan = '100%'>Benchmarking info (click to remove)</th></tr>
        <tr><td>Initialization time: </td><td>".round($debug_InitTime, 5)." sec</td></tr>
        <tr><td>Time up to smarty: </td><td>".round($debug_timeBeforeSmarty, 5)." sec</td></tr>
        <tr><td>Database time (".$databaseQueries." q): </td><td>".($databaseTime > 100 ? 0 : round($databaseTime, 5))." sec</td></tr>
        <tr><td>Smarty overhead: </td><td>".round($debug_timeAfterSmarty - $debug_timeBeforeSmarty, 5)." sec</td></tr>
        <tr><td colspan = \"2\" class = \"horizontalSeparator\"></td></tr>
        <tr><td>Total execution time: </td><td>".round($debug_TotalTime, 5)." sec</td></tr>
        <tr><td>Execution time for this script is: </td><td>".round($debug_TotalTime - $debug_InitTime - ($debug_timeAfterSmarty - $debug_timeBeforeSmarty), 5)." sec</td></tr>
    </table>
    </div>";
}

/**
*
*/
function eF_local_calculateForumStats($tree, $node, $topics, $polls, $messages, $last_post) {
    $total = array();
    $total['topics']   += $topics[$node];
    $total['polls']    += $polls[$node];
    $total['messages'] += $messages[$node];
    $last_post[$node] >= $total['last_post'] ? $total['last_post'] = $last_post[$node] : '';
    foreach ($tree[$node] as $id) {
        if (in_array($id, array_keys($tree))) {
            $temp = eF_local_calculateForumStats($tree, $id, $topics, $polls, $messages, $last_post);
            $total['topics']   += $temp['topics'];
            $total['polls']    += $temp['polls'];
            $total['messages'] += $temp['messages'];
            $last_post[$id] > $total['last_post'] ? $total['last_post'] = $last_post[$id] : '';
        } else {
            $total['topics']   += $topics[$id];
            $total['polls']    += $polls[$id];
            $total['messages'] += $messages[$id];
            $last_post[$id] > $total['last_post'] ? $total['last_post'] = $last_post[$id] : '';
        }
    }

    return $total;
}

?>