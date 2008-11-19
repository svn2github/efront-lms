<?php
/**
* Student main page
*
* This page performs all student functions
* @package eFront
* @version 1.0
*/
$debug_TimeStart = microtime(true);     //Debugging timer - initialization
session_cache_limiter('none');          //Initialize session
session_start();

$path = "../libraries/";                //Define default path

/** The configuration file.*/
require_once $path."configuration.php";
$debug_InitTime = microtime(true) - $debug_TimeStart;       //Debugging timer - time spent on file inclusion

//Set headers in order to eliminate browser cache (especially IE's)
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

//If the page is shown as a popup, make sure it remains in such mode
if (isset($_GET['popup']) || isset($_POST['popup']) || strpos(strtolower($_SERVER['HTTP_REFERER']), 'popup') !== false) {
    output_add_rewrite_var('popup', 1);
    $smarty -> assign("T_POPUP_MODE", true);
    $popup = 1;
}

$message = '';$message_type = '';                            //Initialize messages, because if register_globals is turned on, some messages will be displayed twice
$loadScripts = array('scriptaculous/prototype', 'EfrontScripts');

/*Check the user type. If the user is not valid or not an administrator, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login'], false, 'student');
        $smarty -> assign("T_CURRENT_USER", $currentUser);
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        echo "<script>parent.location = 'index.php?message=".urlencode($message)."&message_type=failure'</script>";        //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
        //header("location:index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    setcookie('c_request', $_SERVER['REQUEST_URI'], time() + 300);
    echo "<script>parent.location = 'index.php?message=".urlencode(_RESOURCEREQUESTEDREQUIRESLOGIN)."&message_type=failure'</script>";        //This way the frameset will revert back to single frame, and the annoying effect of 2 index.php, one in each frame, will not happen
    //header("location:index.php?message=".urlencode(_RESOURCEREQUESTEDREQUIRESLOGIN)."&message_type=failure");
    exit;
}

if ($_COOKIE['c_request']) {
    setcookie('c_request', '', time() - 86400);
    header("location:".$_COOKIE['c_request']);
}
$roles       = EfrontLessonUser :: getLessonsRoles();
$userLessons = $currentUser -> getLessons();

/*This is the first time the professor enters this lesson, so register the lesson id to the session*/
if (isset($_GET['lessons_ID']) && eF_checkParameter($_GET['lessons_ID'], 'id') && (!isset($_SESSION['s_lessons_ID']) || $_GET['lessons_ID'] != $_SESSION['s_lessons_ID'])) {    
    if (in_array($_GET['lessons_ID'], array_keys($userLessons))) {
        $_SESSION['s_lessons_ID'] = $_GET['lessons_ID'];
        $_SESSION['s_type']       = $roles[$userLessons[$_GET['lessons_ID']]];
        $smarty -> assign("T_CHANGE_LESSON", "true");
        $smarty -> assign("T_REFRESH_SIDE", "true");
    } else {
        unset($_GET['lessons_ID']);
        $message      = _YOUCANNOTACCESSTHISLESSONORITDOESNOTEXIST;
        $message_type = 'failure';
        $ctg          = 'personal';
    }
}

if ($_SESSION['s_lessons_ID'] && $roles[$userLessons[$_SESSION['s_lessons_ID']]].'.php' != basename($_SERVER['PHP_SELF'])) {
    header('location:'.$roles[$userLessons[$_SESSION['s_lessons_ID']]].'.php');
    exit;
}

if (isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID']) {    //Check validity of current lesson
    $userLessons = $currentUser -> getLessons();
    if (!isset($userLessons[$_SESSION['s_lessons_ID']]) || $roles[$userLessons[$_SESSION['s_lessons_ID']]] != 'student') {
        header("location:student.php?ctg=lessons");    //redirect to student's lessons page
        exit;
    }
    try {
        $currentUser    -> applyRoleOptions($userLessons[$_SESSION['s_lessons_ID']]);                //Initialize user's role options for this lesson
        $currentLesson  = new EfrontLesson($_SESSION['s_lessons_ID']);                //Initialize lesson
        $smarty -> assign("T_TITLE_BAR", $currentLesson -> lesson['name']);
    } catch (Exception $e) {
        unset($_SESSION['s_lessons_ID']);
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        header("location:".basename($_SERVER['PHP_SELF'])."?message=".urlencode($message)."&message_type=failure");    //redirect to user lessons page
    }
    try {
        $currentContent = new EfrontContentTree($_SESSION['s_lessons_ID']);           //Initialize content
        $currentContent -> markSeenNodes($currentUser);
        $currentUser -> coreAccess['content'] != 'change' ? $currentLesson -> mode = 'browse' : $currentLesson -> mode = 'normal';    //If the user type's setting is other than 'change' from content, then set lesson mode to 'browse', which means that no unit completion or ' or whatever progress is recorded
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = _ERRORLOADINGCONTENT.": ".$_SESSION['s_lessons_ID'].": ".$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    }
}

/*Check current unit*/
if (isset($_GET['view_unit']) && eF_checkParameter($_GET['view_unit'], 'id')) {
    if ($currentUser -> coreAccess['content'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    try {
        $currentUnit  = $currentContent -> seekNode($_GET['view_unit']);              //Initialize current unit
        $log_comments = $_GET['view_unit'];                                           //Value to insert to logs
        $currentUnit['ctg_type'] == 'tests' ? $_GET['ctg'] = 'tests' : $_GET['ctg'] = 'content';
    } catch (Exception $e) {
        unset($_GET['view_unit']);
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = _ERRORLOADINGCONTENT.": ".$_SESSION['s_lessons_ID'].": ".$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    }
}

// Share the hcd value with smarty
$module_hcd_interface = MODULE_HCD_INTERFACE;
$smarty -> assign("T_MODULE_HCD_INTERFACE", $module_hcd_interface);

///MODULE1: Import
$loadedModules = $currentUser -> getModules();

$module_css_array = array();
$module_js_array = array();

// Include module languages
foreach ($loadedModules as $module) {
    // The $setLanguage variable is defined in globals.php
    $mod_lang_file = $module -> getLanguageFile($setLanguage);
    if (is_file ($mod_lang_file)) {
        require_once $mod_lang_file;
    }

    // Get module css
    if($mod_css_file = $module -> getModuleCSS()) {
        if (is_file ($mod_css_file)) {

            // Get the relative path
            if ($position = strpos($mod_css_file, "modules")) {
                $mod_css_file = substr($mod_css_file, $position);
            }
            $module_css_array[] = $mod_css_file;
        }
    }

    // Get module js
    if($mod_js_file = $module -> getModuleJS()) {
        if (is_file($mod_js_file)) {
            // Get the relative path
            if ($position = strpos($mod_js_file, "modules")) {
                $mod_js_file = substr($mod_js_file, $position);
            }

            $module_js_array[] = $mod_js_file;
        }
    }
}

if (isset($_GET['ajax']) && isset($_GET['bookmarks'])) {
    if ($_GET['bookmarks'] == 'remove' && isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id')) {
        eF_deleteTableData("bookmarks", "id=".$_GET['id']." and users_LOGIN='".$_SESSION['s_login']."'");
    } elseif ($_GET['bookmarks'] == 'add' && isset($_GET['url']) && eF_checkParameter($_GET['url'], 'text')) {
        $fields = array('users_LOGIN'     => $currentUser -> user['login'],
                        'users_USER_TYPE' => $currentUser -> user['user_type'],
                        'lessons_ID'      => isset($currentLesson) ? $currentLesson -> lesson['id'] : 0,
                        'name'            => $_GET['name'],
                        'url'             => $_GET['url']);
        if (isset($currentLesson)) {
            $fields['url'] .= '&lessons_ID='.$currentLesson -> lesson['id'];
            $fields['name'] = truncate($currentLesson -> lesson['name'], 30).'&nbsp;&raquo;&nbsp;'.$fields['name'];
        }
        if (eF_insertTableData("bookmarks", $fields)) {
            echo _SUCCESSFULLYINSERTEDBOOKMARK;
        } else {
            echo _ERRORINSERTINGBOOKMARK;
        }
        exit;
    }
    $result = eF_getTableData("bookmarks", "*", "users_LOGIN='".$_SESSION['s_login']."' AND name!=''", "id ASC");
    if (sizeof($result) == 0) {
        $bookmarksCode .= '<tr><td class = "emptyCategory centerAlign">'._NOBOOKMARKSFOUND.'</td></tr>';
    }
    foreach ($result as $bookmark) {
        $bookmarksCode .= '
                    <tr><td style = "text-align:left"><a href = "'.$bookmark['url'].'">'.$bookmark['name'].'</a></td>
                        <td style = "text-align:right"><a href = "javascript:void(0)" onclick = ""><img src = "images/16x16/delete.png" alt = "'._DELETEBOOKMARK.'" title = "'._DELETEBOOKMARK.'" border = "0" onclick = "removeBookmark('.$bookmark['id'].')"/></td></tr>';
    }
    echo '<table style = "width:100%">'.$bookmarksCode.'</table>';
    exit;
}
/*Added Session variable for search results*/
$_SESSION['referer'] = $_SERVER['REQUEST_URI'];

/*These are the possible ctg we can have. - The three last added by HCD */
$possible_ctgs = array('control_panel', 'content', 'scheduling', 'tests', 'rules', 'calendar', 'module',
                       'statistics', 'survey', 'glossary', 'settings', 'lessons', 'personal',
                       'projects', 'module_hcd', 'users','emails','evaluations', 'newtests');
if (sizeof($module_ctgs) > 0) {
    $possible_ctgs = array_merge($possible_ctgs, array_keys($module_ctgs));
}
!isset($_GET['ctg']) || !in_array($_GET['ctg'], $possible_ctgs)  ? $ctg = "control_panel" : $ctg = $_GET['ctg'];    //The default ctg is 'control_panel'

if (!$_SESSION['s_lessons_ID'] && $ctg != 'personal' && $ctg != 'statistics' && ($ctg == 'control_panel' && $_GET['op'] != "search")) {       //If there is not a lesson in the session, then the user just logged into the system. Redirect him to lessons page, except for the case he is viewing his personal information 2007/07/27 added search control. It was a problem when user had not choose a lesson.
    $ctg = 'lessons';
}

$smarty -> assign("T_CTG", $ctg);       //As soon as we derive the current ctg, assign it to smarty.
$smarty -> assign("T_OP", $_GET['op']);
$smarty -> assign("T_FCT", $_GET['fct']);

/*
Control panel is the first page that the student sees, and contains links to most of the available functions
At the control panel main page, you will find 8 sections:
- The content tree
- Lesson announcements
- Recent forum messages
- Recent personal messages
- Recent comments
- The calendar
- The digital library
- Any new content that was added recently
*/
if ($ctg == 'control_panel') {
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';
    $loadScripts[] = 'scriptaculous/dragdrop';
    $loadScripts[] = 'drag-drop-folder-tree';

    /*Insert a record into the logs table, if a lesson has been selected*/
    if (isset($_SESSION['s_lessons_ID'])) {
        $fields_log = array ('users_LOGIN' => $_SESSION['s_login'],                                 //This is the log entry array
                             'timestamp'   => time(),
                             'action'      => 'lesson',
                             'comments'    => 0,
                             'session_ip'  => eF_encodeIP($_SERVER['REMOTE_ADDR']),
                             'lessons_ID'  => $_SESSION['s_lessons_ID']);
        eF_deleteTableData("logs", "users_LOGIN='".$_SESSION['s_login']."' AND action='lastmove'"); //Only one lastmove action interests us, so delete any other
        eF_insertTableData("logs", $fields_log);
    }

    /*Include the module that is used to perform the searches*/
    if (isset($_GET['op']) && $_GET['op'] == 'search') {
        /**Functions to perform searches*/
        require_once "module_search.php";
    }

    /*Show the announcements (news) full page*/
    elseif (isset($_GET['op']) && $_GET['op'] == 'news') {
        if ($currentUser -> coreAccess['news'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }        
        $smarty -> assign("T_NEWS", eF_getNews());
    }
    /*
    This part is used to display the information that the professor
    has created for this lesson. It is also used to display the lesson
    objectives, that need to be met so that it can be considered done
    */
    elseif (isset($_GET['op']) && $_GET['op'] == 'lesson_information') {
        $lesson_info_categories = array('general_description' => _GENERALDESCRIPTION,
                                        'objectives'          => _OBJECTIVES,
                                        'assessment'          => _ASSESSMENT,
                                        'lesson_topics'       => _LESSONTOPICS,
                                        'resources'           => _RESOURCES,
                                        'other_info'          => _OTHERINFO);

        $lessonInformation = $currentLesson -> getInformation($currentUser -> user['login']);

        $smarty -> assign("T_LESSON_INFO", $lessonInformation);
        $smarty -> assign("T_LESSON_INFO_CATEGORIES", $lesson_info_categories);

        $seenContent = EfrontStats :: getStudentsSeenContent($currentLesson -> lesson['id'], $currentUser -> user['login']);

        $conditions       = $currentLesson -> getConditions();
        foreach ($iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST))) as $key => $value) {
            $visitableContentIds[$key] = $key;                                                    //Get the not-test unit ids for this content
        }
        foreach ($iterator = new EfrontTestsFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)))) as $key => $value) {
            $testsIds[$key] = $key;                                                    //Get the not-test unit ids for this content
        }

        $conditionsStatus = EfrontStats :: checkConditions($seenContent[$currentLesson -> lesson['id']][$currentUser -> user['login']], $conditions, $visitableContentIds, $testsIds);
        $smarty -> assign("T_CONDITIONS", $conditions);
        //pr($conditions);
        $smarty -> assign("T_CONDITIONS_STATUS", $conditionsStatus);

        foreach ($iterator = new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree)), array('id', 'name')) as $key => $value) {
            $key == 'id' ? $ids[] = $value : $names[] = $value;
        }
        $smarty -> assign("T_TREE_NAMES", array_combine($ids, $names));
    }
    /*
    Module inclusion. If there are any modules that need to be displayed as ops in the control panel, they are included here
    */
    else if (isset($_GET['op']) && in_array($_GET['op'], array_keys($module_ctgs))) {
        $module_mandatory = eF_getTableData("modules", "mandatory", "name = '".$_GET['op']."'");
        if ($module_mandatory[0]['mandatory'] != 'false' || ($GLOBALS['currentLesson'] -> options[$_GET['op']])) {
            include(G_MODULESPATH.$_GET['op'].'/module.php');
            $smarty -> assign("T_OP_MODULE", $module_ctgs[$_GET['op']]);
        }
    }
    elseif (isset($_GET['op']) && $_GET['op'] == 'digital_library' && $currentLesson -> options['digital_library']) {
        if ($currentUser -> coreAccess['content'] == 'hidden') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }

        $loadScripts[] = 'drag-drop-folder-tree';
        $loadScripts[] = 'scriptaculous/effects';
        $basedir    = $currentLesson -> getDirectory();
        try {
            $filesystem = new FileSystemTree($basedir);
            $iterator   = new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new EfrontDBOnlyFilterIterator(new EfrontFileOnlyFilterIterator(new RecursiveIteratorIterator($filesystem -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('shared' => $currentLesson -> lesson['id'])));
            $filesystem -> handleAjaxActions($currentUser);

            $url        = basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=digital_library';
            $options    = array('share' => false, 'zip' => false, 'folders' => false, 'delete' => false, 'edit' => false, 'create_folder' => false, 'upload' => false);

            if (isset($_GET['ajax'])) {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }

                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                }
                isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, false, false, false, $iterator, false);
                exit;
            }
            $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, false, false, false, $iterator, false));
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }

    }
    else {
        $innerTableIdentifier = 'student_cpanel';        //This is a notifier for cookies handling the show/hide status of inner tables. It affects only control panel and is considered inside printInnerTable smarty plugin

        if ($currentLesson -> options['content_tree']) {
            $iterator = new EfrontVisitableAndEmptyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)));
            $smarty  -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator, false, array('truncateNames' => 60)));
        }
        if (!$currentLesson -> options['tracking'] || $currentUser -> coreAccess['content'] == 'hidden') {
            $currentLesson -> options['lesson_info'] ? $lessonOptions[] = array('text' => _LESSONINFORMATION, 'image' => '32x32/about.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=lesson_information', 'onClick' => "eF_js_showDivPopup('"._LESSONINFORMATION."', 2)", 'target' => 'POPUP_FRAME') : null;
        } else {
            try {
                $userProgress = EfrontStats :: getUsersLessonStatus($currentLesson, $currentUser -> user['login']);
                $userProgress = $userProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']];               
                $seenContent  = EfrontStats::getStudentsSeenContent($currentLesson, $currentUser);
                $seenContent  = $seenContent[$currentLesson -> lesson['id']][$currentUser -> user['login']];
                $result       = eF_getTableData("users_to_lessons", "current_unit", "users_LOGIN = '".$currentUser -> user['login']."' and lessons_ID = ".$currentLesson -> lesson['id']);
                sizeof($result) > 0 ? $userProgress['current_unit']  = $result[0]['current_unit'] : $userProgress['current_unit'] = false;

                if ($userProgress['current_unit']) {                                    //If there exists a value within the 'current_unit' attribute, it means that the student was in the lesson before. Seek the first unit that he hasn't seen yet
                    $firstUnseenUnit = $currentContent -> getFirstNode();

                    //Get to the first unseen unit
                    while ($firstUnseenUnit && (!$firstUnseenUnit['active'] || in_array($firstUnseenUnit['id'], array_keys($seenContent)) || ($firstUnseenUnit['data'] == '' && $firstUnseenUnit['ctg_type'] != 'tests'))) {
                        $firstUnseenUnit = $currentContent -> getNextNode($firstUnseenUnit);
                    }
                    if (!$firstUnseenUnit) {
                        $headerOptions[] = array('text' => _YOUHAVESEENALLCONTENT, 'image' => '32x32/checks.png',     'href' => 'user_lesson.php?user='.$currentUser -> user['login']."&lesson=".$currentLesson -> lesson['id'], 'onClick' => "eF_js_showDivPopup('"._USERPROGRESS."', new Array('700px', '400px'))", 'target' => "POPUP_FRAME");
                    } elseif ($currentLesson -> options['start_resume']) {
                        $headerOptions[] = array('text' => _RESUMELESSON,          'image' => '32x32/media_play.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg='.($firstUnseenUnit['ctg_type'] == 'tests' ? 'tests' : 'content').'&view_unit='.$firstUnseenUnit['id']);
                    }
                    $smarty -> assign("T_CURRENT_UNIT", $firstUnseenUnit);
                } else {
                    $iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)));
                    $iterator -> next();
                    $firstUnit = $iterator -> current();

                    if ($firstUnit && $currentLesson -> options['start_resume']) {
                        $headerOptions[] = array('text' => _STARTLESSON, 'image' => '32x32/media_play_green.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$firstUnit['id']);
                    }
                }

                if ($userProgress['lesson_passed']) {
                    if (!$userProgress['completed']) {
                        $currentLesson -> options['lesson_info'] ? $headerOptions[] = array('text' => _LESSONCONDITIONSCOMPLETED, 'image' => '32x32/check2.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=lesson_information&popup=1', 'onClick' => "eF_js_showDivPopup('"._LESSONINFORMATION."', 2)", 'target' => 'POPUP_FRAME'): null;
                    }
                    if (!$userProgress['completed'] && $currentLesson -> options['auto_complete']) {
                        $userProgress['tests_avg_score'] ? $avgScore = $userProgress['tests_avg_score'] : $avgScore = 100;
                        $timestamp = _AUTOCOMPLETEDAT.': '.date("Y/m/d, H:i:s");
                        $currentUser -> completeLesson($currentLesson, $avgScore, $timestamp);

                        $userProgress['completed'] = 1;
                        $userProgress['score']     = $avgScore;
                        $userProgress['comments']  = $timestamp;
                    }
                } else {
                    $currentLesson -> options['lesson_info'] ? $headerOptions[] = array('text' => _LESSONINFORMATION, 'image' => '32x32/about.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=lesson_information&popup=1', 'onClick' => "eF_js_showDivPopup('"._LESSONINFORMATION."', 2)", 'target' => 'POPUP_FRAME') : null;
                }

                if ($userProgress['completed']) {
                    $smarty -> assign("T_LESSON_COMPLETED", $userProgress['completed']);
                    $currentLesson -> options['lesson_info'] ? $headerOptions[] = array('text' => _LESSONCOMPLETE, 'image' => '32x32/graduation_hat2.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg=control_panel&op=lesson_information&popup=1', 'onClick' => "eF_js_showDivPopup('"._LESSONINFORMATION."', 2)", 'target' => 'POPUP_FRAME') :null ;
                }

            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = _ERRORLOADINGCONTENT.": ".$_SESSION['s_lessons_ID'].": ".$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            }
        }

///MODULE2: Create lesson control panel sidelinks and innertable
        $innertable_modules = array();
        foreach ($loadedModules as $module) {
            if (isset($currentLesson -> options[$module -> className])  && $currentLesson -> options[$module -> className] == 1) {

                if ($centerLinkInfo = $module -> getLessonCenterLinkInfo()) {
                    $lessonOptions[] = array('text' => $centerLinkInfo['title'],  'image' => eF_getRelativeModuleImagePath($centerLinkInfo['image']), 'href' => $centerLinkInfo['link']);
                }

                unset($lessonInnertableHTML);
                $lessonInnertableHTML = $module -> getLessonModule();   //**HERE**
                // If the module has a lesson innertable
                if ($lessonInnertableHTML) {
                    // Get module html - two ways: pure HTML or PHP+smarty
                    // If no smarty file is defined then false will be returned
                    if ($module_smarty_file = $module -> getLessonSmartyTpl()) {
                        // Execute the php code -> The code has already been executed by above (**HERE**)
                        // Let smarty know to include the module smarty file
                        $innertable_modules[$module->className] = array('smarty_file' => $module_smarty_file);
                    } else {
                        // Present the pure HTML cod
                        $innertable_modules[$module->className] = array('html_code' => $lessonInnertableHTML);
                    }
                }
            }
        }

/*
        foreach ($loadedModules as $module) {
            if ($module['mandatory'] != 'false' || ($GLOBALS['currentLesson'] -> options[$module['name']])) {
                if ($module['position'] != 'left') {
                    $lessonOptions[] = array('text' => $module['title'], 'image' => "32x32/component_green.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=".$module['name']);
                }
                if ($module['menu'] == 'inner_table') {
                    //Include the inner table code
                    include(G_MODULESPATH.$module['name'].'/module_innerTable.php');
                    $innertable_modules[] = $module['name'];
                }
            }
        }
*/
        $smarty -> assign("T_INNERTABLE_MODULES", $innertable_modules);

        $smarty -> assign("T_LESSON_OPTIONS", $lessonOptions);
        $smarty -> assign("T_HEADER_OPTIONS", $headerOptions);
        $smarty -> assign("T_LESSON_OPTIONS_SIZE", sizeof($lessonOptions));
        $smarty -> assign("T_LESSON_LINK","student.php?ctg=lessons");

        $elementPositions = eF_getTableData("users_to_lessons", "positions", "lessons_ID=".$currentLesson -> lesson['id']." AND users_LOGIN='".$currentUser -> user['login']."'");
        if (sizeof($elementPositions) == 0 && $currentLesson -> options['default_positions']) {
            $elementPositions[0]['positions'] = $currentLesson -> options['default_positions'];
        }

        if (sizeof($elementPositions) > 0) {
            $elementPositions = unserialize($elementPositions[0]['positions']);     //Get the inner tables positions, stored by the user.
            
            $smarty -> assign("T_POSITIONS_FIRST", $elementPositions['first']);     //Assign element positions to smarty
            $smarty -> assign("T_POSITIONS_SECOND", $elementPositions['second']);
            $smarty -> assign("T_POSITIONS_VISIBILITY", $elementPositions['visibility']);
            $smarty -> assign("T_POSITIONS", array_merge($elementPositions['first'], $elementPositions['second']));
            if ($elementPositions['update']) {
                foreach ($_COOKIE['innerTables'] as $key => $value) {
                    setcookie("innerTables[$key]", "", time()-86400, "/");
                }
                unset($elementPositions['update']);
                eF_updateTableData("users_to_lessons", array("positions" => serialize($elementPositions)), "lessons_ID=".$currentLesson -> lesson['id']." AND users_LOGIN='".$currentUser -> user['login']."'");
            }
        } else {
            $smarty -> assign("T_POSITIONS", array());
        }

        /*Projects list*/
        //$allProjects = eF_getTableData("projects,users_to_projects","title,id,deadline,creator_LOGIN","lessons_ID=".$_SESSION['s_lessons_ID']." AND id=projects_ID AND users_LOGIN='".$_SESSION['s_login']."'");
        if ($currentLesson -> options['projects']) {
            $allProjects = $currentLesson -> getProjects(false, $currentUser -> user['login']);
            $smarty -> assign("T_ALL_PROJECTS", $allProjects);
            $projects_options = array(array('text' => _GOTOPROJECTS, 'image' => "16x16/redo.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=projects"));
            $smarty -> assign("T_PROJECTS_OPTIONS",$projects_options);
            $smarty -> assign("T_PROJECTS_LINK","student.php?ctg=projects");
        }
        /*Forum messages list*/
        $forum_messages   = eF_getForumMessages($_SESSION['s_lessons_ID'], 3);                              //Get any forum messages related to this lesson
        $forum_lessons_ID = eF_getTableData("f_forums", "id", "lessons_ID=".$_SESSION['s_lessons_ID']); //Get the forum category related to this lesson
        $smarty -> assign("T_FORUM_MESSAGES", $forum_messages);                                             //Assign forum messages and categoru information to smarty
        $smarty -> assign("T_FORUM_LESSONS_ID", $forum_lessons_ID[0]['id']);

        if ($forum_lessons_ID[0]['id']) {                                                                   //If there is a forum category associated to this lesson (and the user is eligible to use it), display corresponding links
            $forum_options = array(
                    array('text' => _GOTOFORUM, 'image' => "16x16/redo.png", 'href' => "forum/forum_index.php"),
                    array('text' => _SENDMESSAGEATFORUM, 'image' => "16x16/add2.png", 'href' => "forum/forum_add.php?type=topic&category=".$forum_lessons_ID[0]['id'], 'onClick' => "eF_js_showDivPopup('"._NEWMESSAGE."', new Array('650px', '450px'));", 'target' => 'POPUP_FRAME')
                    );
        } else {                                                                                            //If there isn't a forum caegory associated to this lesson, only display a link to forum
            $forum_options = array(
                    array('text' => _GOTOFORUM, 'image' => "16x16/redo.png", 'href' => "forum/forum_index.php")
                    );
        }
        $smarty -> assign("T_FORUM_OPTIONS", $forum_options);                                               //Assign forum options to smarty
        $smarty -> assign("T_FORUM_LINK", "forum/forum_index.php");

        /*Personal messages list*/
        if ($currentUser -> coreAccess['personal_messages'] != 'hidden') {
            $personal_messages = eF_getPersonalMessages(false, $p_messages_limit);                              //Get user personal messages
            $smarty -> assign("T_PERSONAL_MESSAGES", $personal_messages);                                       //Assign personal messages to smarty
                $personal_message_options = array(
                            array('text' => _GOTOINBOX, 'image' => "16x16/redo.png", 'href' => "forum/messages_index.php"),
                            array('text' => _SENDMESSAGE, 'image' => "16x16/add2.png", 'href' => "forum/new_message.php", 'onClick' => "eF_js_showDivPopup('"._NEWMESSAGE."', new Array('650px', '450px'));", 'target' => 'POPUP_FRAME')
                );
            $smarty -> assign("T_PERSONAL_MESSAGES_OPTIONS",$personal_message_options);                         //Assign personal messages options to smarty. to display with the inner table
            $smarty -> assign("T_PERSONAL_MESSAGES_LINK", "forum/messages_index.php");
        }
        /*Lesson announcements list*/
        if ($currentUser -> coreAccess['news'] != 'hidden') {
            $announcements         = eF_getNews();                                                                  //Get lesson announcements           
            $announcements_options = array(
                    array('text' => _ANNOUNCEMENTGO,  'image' => "16x16/redo.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=control_panel&op=news")
                    );

            $smarty -> assign("T_NEWS", $announcements);                                                        //Assign announcements to smarty
            $smarty -> assign("T_NEWS_OPTIONS",$announcements_options);
            $smarty -> assign("T_NEWS_LINK", "student.php?ctg=control_panel&op=news");
        }
        /*Comments list*/
        if ($GLOBALS['currentLesson'] -> options['comments'] == 1) {                                               //If the comments in lesson are enabled
            $comments = eF_getComments(false, false, false, 3);                                             //Retrieve 3 first comments
            $smarty -> assign("T_COMMENTS", $comments);                                                     //Assign to smarty
        }

        if ($currentLesson -> options['digital_library'] && $currentUser -> coreAccess['content'] != 'hidden') {                                        //If the lesson digital library is enabled

            $basedir    = $currentLesson -> getDirectory();
            try {
                $filesystem = new FileSystemTree($basedir);
                $iterator   = new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new EfrontDBOnlyFilterIterator(new EfrontFileOnlyFilterIterator(new RecursiveIteratorIterator($filesystem -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('shared' => $currentLesson -> lesson['id'])));
                $filesystem -> handleAjaxActions($currentUser);

                $url        = basename($_SERVER['PHP_SELF']).'?ctg=control_panel';
                $options    = array('share' => false, 'zip' => false, 'folders' => false, 'delete' => false, 'edit' => false, 'create_folder' => false, 'upload' => false, 'show_size' => false, 'show_date' => false);

                if (isset($_GET['ajax'])) {
                    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                        $sort = $_GET['sort'];
                        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                    } else {
                        $sort = 'login';
                    }

                    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    }
                    isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
                    isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';
                    $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
                    echo $filesystem -> toHTML($url, $other, $ajaxOptions, $options, false, false, false, $iterator, false);
                    exit;
                }
                $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, false, false, false, $iterator, false));
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                $message_type = 'failure';
            }
        }

        if ($currentUser -> coreAccess['calendar'] != 'hidden') {
            $calendar_options = array(                                                                          //Create calendar options and assign them to smarty, to be displayed at the calendar inner table
                array('text' => _GOTOCALENDAR, 'image' => "16x16/redo.png", 'href' => "student.php?ctg=calendar")
            );
            $smarty -> assign("T_CALENDAR_OPTIONS", $calendar_options);
            $smarty -> assign("T_CALENDAR_LINK", "student.php?ctg=calendar");

            $today = getdate(time());                                                                           //Get current time in an array
            $today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']);                            //Create a timestamp that is today, 00:00. this will be used in calendar for displaying today
            (eF_checkParameter($_GET['view_calendar'], 'timestamp')) ? $view_calendar = $_GET['view_calendar']: $view_calendar = $today;    //If a specific calendar date is not defined in the GET, set as the current day to be today

            $result = eF_getTableData("calendar","*","");
            foreach ($result as $event) {
                $events[$event['timestamp']]['data'][] = $event['data'];
                $events[$event['timestamp']]['id'][]   = $event['id'];
                $events[$event['timestamp']]['lesson'][]   = $event['name'];
            }
            $smarty -> assign("T_CALENDAR_EVENTS", $events);                                                    //Assign events and specific day timestamp to smarty, to be used from calendar
            $smarty -> assign("T_VIEW_CALENDAR", $view_calendar);
        }
    }
}
/*
Projects is the page where the student views the projects that have been assigned to him/her.
*/
elseif ($ctg == 'projects') {
    if ($currentUser -> coreAccess['content'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }

    try {
        $projects = $currentLesson -> getProjects(false, $currentUser -> user['login']);
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }

    if (isset($_GET['view_project']) && eF_checkParameter($_GET['view_project'], 'id') && in_array($_GET['view_project'], array_keys($projects))) {
        try {
            $currentProject = new EfrontProject($_GET['view_project']);
            $projectUser    = $currentProject -> getUsers();
            $projectUser    = $projectUser[$currentUser -> user['login']];
            $currentProject -> project['deadline'] < time() ? $currentProject -> expired = true : $currentProject -> expired = false;

            if ($projectUser['filename']) {
                try {
                    $projectFile = new EfrontFile($projectUser['filename']);
                    $smarty -> assign("T_PROJECT_FILE", $projectFile);
                    if (isset($_GET['delete_file']) && !$currentProject -> expired) {
                        $projectFile -> delete();
                        eF_updateTableData("users_to_projects", array('filename' => '', 'upload_timestamp' => ''), "users_LOGIN='".$currentUser -> user['login']."' AND projects_ID=".$_GET['view_project']);
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=projects&view_project=".$_GET['view_project']."&message=".urlencode(_FILEDELETEDSUCCESSFULLY)."&message_type=success");
                    }
                } catch (EfrontFileException $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            }

            $form        =  new HTML_QuickForm("upload_project_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=projects&view_project='.$_GET['view_project'], "", null, true);
            $file        =& $form -> addElement('file', 'filename', _FILE);
            $maxFileSize =  FileSystemTree :: getUploadMaxSize();
            //$form        -> addRule('filename', _NOTGIVEFILENAME, 'uploadedfile');
            $form        -> addRule('filename', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
            $form        -> setMaxFileSize($maxFileSize * 1024);
            $form        -> addElement('submit', 'submit_upload_project', _SENDPROJECT);

            $smarty -> assign("T_MAX_FILE_SIZE", $maxFileSize);
            if ($form -> isSubmitted() && $form -> validate() && !$currentProject -> expired) {
                try {
                    $projectDirectory = G_UPLOADPATH.$currentUser -> user['login'].'/projects';
                    if (!is_dir($projectDirectory)) {
                        EfrontDirectory :: createDirectory($projectDirectory);
                    }
                    $filesystem = new FileSystemTree($projectDirectory);
                    $uploadedFile = $filesystem -> uploadFile('filename', $projectDirectory);
                    $fields_update = array("filename"         => $uploadedFile['id'],
                                           "upload_timestamp" => time());
                    eF_updateTableData("users_to_projects", $fields_update, "users_LOGIN='".$currentUser -> user['login']."' AND projects_ID=".$_GET['view_project']);
                    header("location:".basename($_SERVER['PHP_SELF'])."?ctg=projects&view_project=".$_GET['view_project']."&message=".urlencode(_FILEUPLOADED)."&message_type=success");
                } catch (EfrontFileException $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            } elseif ($currentProject -> expired) {
                $message      = _PROJECTEXPIRED;
                $message_type = 'failure';
            }

            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

            $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
            $form -> setRequiredNote(_REQUIREDNOTE);
            $form -> accept($renderer);

            $smarty -> assign('T_UPLOAD_PROJECT_FORM', $renderer -> toArray());
            $smarty -> assign("T_PROJECT", $currentProject);
            $smarty -> assign("T_PROJECT_USER_INFO", $projectUser);

        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    } else {
        $currentProjects = array();
        $passedProjects  = array();

        foreach ($projects as &$project) {
            $remainTime                = eF_convertIntervalToTimeFull($project['deadline'] - time());
            $project['time_remaining'] = $remainTime['string'];
            $project['deadline'] > time() ? $currentProjects[] = $project : $passedProjects[] = $project;
        }
        unset($project);

        $smarty -> assign("T_CURRENT_PROJECTS", $currentProjects);
        $smarty -> assign("T_ACTIVE_COUNT", sizeof($currentProjects));

        $smarty -> assign("T_EXPIRED_PROJECTS", $passedProjects);
        $smarty -> assign("T_INACTIVE_COUNT", sizeof($passedProjects));
    }
}

/*
Content is the page where the student views the lesson content.
*/
elseif ($ctg == 'content') {
    $loadScripts[] = 'drag-drop-folder-tree';
    if ($configuration['math_content']) {
		$loadScripts[] = 'ASCIIMathML';
	}
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';
    $loadScripts[] = 'scriptaculous/sidebar_extra';

    $smarty -> assign("T_LESSON_NAME", $currentLesson -> lesson['name']);

    if (!$currentUnit) {
        if ($_GET['type'] == 'tests') {
            $iterator = new EfrontTestsFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1))));
        } else if ($_GET['type'] == 'theory') {
            $iterator = new EfrontTheoryFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1))));
        } else if ($_GET['type'] == 'examples') {
            $iterator = new EfrontExampleFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1))));
        }

        $smarty  -> assign("T_THEORY_TREE", $currentContent -> toHTML($iterator, 'dhtmlContentTree'));
    } else {
            try {
                !isset($currentUnit) ? $currentUnit = $currentContent -> getFirstNode() : null;                                               //If a unit is not specified, then consider the first content unit by default
                $visitableAndEmptyIterator = new EfrontVisitableAndEmptyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)));
                $smarty  -> assign("T_CONTENT_TREE", $currentContent -> toHTML($visitableAndEmptyIterator, 'dhtmlContentTree', array('truncateNames' => 25, 'selectedNode' => $currentUnit['id'])));
                
                if ($currentUnit['ctg_type'] == 'scorm' || $currentUnit['ctg_type'] == 'scorm_test') {
                    $scorm_unit = true;
                    $smarty -> assign("T_SCORM", $scorm_unit);
                }
                if ($currentLesson -> options['glossary']) {
                    $currentUnit['data'] = eF_applyGlossary($currentUnit['data']);        //If glossary is activated, transform content data accordingly
                }
                $currentUnit['data'] = str_replace("##EFRONTINNERLINK##", $_SESSION['s_type'], $currentUnit['data']);    //Replace inner links. Inner links are created when linking from one unit to another, so they must point either to professor.php or student.php, depending on the user viewing the content

                $visitableIterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)));                
                $smarty -> assign("T_UNIT",          $currentUnit);
                $smarty -> assign("T_NEXT_UNIT",     $currentContent -> getNextNode($currentUnit, $visitableIterator));
                $smarty -> assign("T_PREVIOUS_UNIT", $currentContent -> getPreviousNode($currentUnit, $visitableIterator));        //Next and previous units are needed for navigation buttons
                $smarty -> assign("T_PARENT_LIST",   $currentContent -> getNodeAncestors($currentUnit));       //Parents are needed for printing the titles

                $smarty -> assign("T_COMMENTS",      eF_getComments($_SESSION['s_lessons_ID'], false, $currentUnit['id']));        //Retrieve any comments regarding this unit
                $smarty -> assign("T_SHOW_TOOLS",    true);                                                    //Tools is the right upper corner table box, that lists tools such as 'upload files', 'copy content' etc
                
                if ($currentLesson -> options['tracking'] && (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change')) {
                    $userProgress = EfrontStats :: getUsersLessonStatus($currentLesson, $currentUser -> user['login']);
                    $userProgress = $userProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']];
                    $seenContent  = EfrontStats :: getStudentsSeenContent($currentLesson -> lesson['id'], $currentUser -> user['login']);
                    $seenContent  = $seenContent[$currentLesson -> lesson['id']][$currentUser -> user['login']];

                    $smarty -> assign("T_SEEN_UNIT", in_array($currentUnit['id'], array_keys($seenContent)));    //Notify smarty whether the student has seen the current unit
                    $ruleCheck = $currentContent -> checkRules($currentUnit['id'], $seenContent);

                    if ($ruleCheck !== true) {
                         $message      = $ruleCheck;
                         $message_type = false;
                         $smarty -> assign("T_RULE_CHECK_FAILED", true);
                    }

                    $smarty -> assign("T_USER_PROGRESS", $userProgress);

                    if ($currentUnit['options']['complete_question'] && !in_array($currentUnit['id'], array_keys($seenContent))) {
                        $loadScripts[] = 'scriptaculous/effects';
                        $lessonQuestions = $currentLesson -> getQuestions();
                        if (in_array($currentUnit['options']['complete_question'], array_keys($lessonQuestions))) {
                            $question = QuestionFactory::factory($currentUnit['options']['complete_question']);
                            $smarty -> assign("T_QUESTION", $question -> toHTML(new HTML_QuickForm()));
                            if (sizeof($_POST) > 0) {
                                try {
                                    //$question = QuestionFactory::factory($unitQuestions[key($_POST['question'])]);
                                    $question -> setDone($_POST['question'][$question -> question['id']]);
                                    $results  = $question -> correct();
                                    if ($results['score'] > 0.5) {                                        //50% is considered success
                                        $currentUser -> setSeenUnit($currentUnit, $currentLesson, true);
                                        echo 'correct';
                                    }
                                } catch (Exception $e) {
                                    header("HTTP/1.0 500 ");
                                    echo $e -> getMessage().' ('.$e -> getCode().')';
                                }
                                exit;
                            }
                        } else {
                            //Remove non-existant question
                            $currentUnit -> options['complete_question'] = false;
                            $currentUnit -> persist();
                        }
                    }
                    
                    if (isset($_GET['ajax'])) {
                        try {
                            $currentUser -> setSeenUnit($currentUnit, $currentLesson, $_GET['set_seen']);
                        } catch (Exception $e) {
                            header("HTTP/1.0 500 ");
                            echo $e -> getMessage().' ('.$e -> getCode().')';                            
                        }
                        exit;
                    }

                }
            } catch (Exception $e) {
                $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                $message = _ERRORLOADINGCONTENT.": ".$_SESSION['s_lessons_ID'].": ".$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            }

    }
}
/*
Tests page is responsible for displaying and performing tests
Th student sees a list of tests and may choose to take one.
The code below is responsible for displaying tests and corresponding questions.
How the tests work:
1. Create a form with all the question types
2. At the same time, if the user has already done this test, then assign the stored question answers to form defaults and FREEZE the form
3. Correct the test, no matter if the student has just submited the test, or he sees an old test. The only difference is that in the latter case, there will be no inserts to the database
*/
elseif ($ctg == 'tests') {
    $loadScripts[] = 'drag-drop-folder-tree';
    if ($configuration['math_content']) {
		$loadScripts[] = 'ASCIIMathML';
	}
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/sidebar_extra';
    $loadScripts[] = 'scriptaculous/effects';

    try {
        $seenContent  = EfrontStats :: getStudentsSeenContent($currentLesson -> lesson['id'], $currentUser -> user['login']);
        $seenContent  = $seenContent[$currentLesson -> lesson['id']][$currentUser -> user['login']];
        $ruleCheck    = $currentContent -> checkRules($currentUnit['id'], $seenContent);

        if (isset($_GET['view_unit']) && eF_checkParameter($_GET['view_unit'], 'id') && (!($GLOBALS['currentLesson'] -> options['rules']) || $ruleCheck === true)) {
            $visitableIterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)));
            $smarty -> assign("T_CONTENT_TREE",  $currentContent -> toHTML(false, 'dhtmlContentTree', array('truncateNames' => 25, 'selectedNode' => $currentUnit['id'])));
            $smarty -> assign("T_UNIT",          $currentUnit);
            $smarty -> assign("T_NEXT_UNIT",     $currentContent -> getNextNode($currentUnit, $visitableIterator));
            $smarty -> assign("T_PREVIOUS_UNIT", $currentContent -> getPreviousNode($currentUnit, $visitableIterator));        //Next and previous units are needed for navigation buttons
            $smarty -> assign("T_PARENT_LIST",   $currentContent -> getNodeAncestors($currentUnit));       //Parents are needed for printing the titles

            $test   = new EfrontTest($currentUnit['id'], true);
            $status = $test -> getStatus($currentUser, $_GET['show_solved_test']);

            $form    = new HTML_QuickForm("test_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=tests&view_unit='.$_GET['view_unit'], "", null, true);
            switch ($status['status']) {
                case 'incomplete':
                    $testInstance  = unserialize($status['completedTest']['test']);
                    if ($testInstance -> time['pause'] && isset($_GET['resume'])) {
                        $testInstance -> time['pause']  = 0;
                        $testInstance -> time['resume'] = time();
                        //unset($testInstance -> currentQuestion);
                        $testInstance -> save();
                    }
                    $remainingTime = $testInstance -> options['duration'] - $testInstance -> time['spent'] - (time() - $testInstance -> time['resume']);
                    $testString    = $testInstance -> toHTMLQuickForm($form);
                    $testString    = $testInstance -> toHTML($testString, $remainingTime);
                    break;
                case 'completed':case 'passed':case 'failed':
                    $testInstance = unserialize($status['completedTest']['test']);
                    //$url          = basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$_GET['view_unit'];
                    $testString   = $testInstance -> toHTMLQuickForm($form, false, true);
                    $testString   = $testInstance -> toHTMLSolved($testString, false);

                    if (isset($_GET['test_analysis'])) {
                        require_once 'charts/php-ofc-library/open-flash-chart.php';

                        list($parentScores, $analysisCode) = $testInstance -> analyseTest();

                        $smarty -> assign("T_CONTENT_ANALYSIS", $analysisCode);
                        $smarty -> assign("T_TEST_DATA", $testInstance);

                        $status = $testInstance -> getStatus($currentUser -> user['login']);
                        $smarty -> assign("T_TEST_STATUS", $status);

                        if (isset($_GET['display_chart'])) {
                            $url = basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$currentUnit['id'].'&test_analysis=1&selected_unit='.$_GET['selected_unit'].'&show_chart=1';
                            echo $testInstance -> displayChart($url);
                            exit;
                        } elseif (isset($_GET['show_chart'])) {
                            echo $testInstance -> calculateChart($parentScores);
                            exit;
                        }
                    }

                    break;
                default:
                    if (isset($_GET['confirm'])) {
                        $testInstance = $test -> start($currentUser -> user['login']);
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=tests&view_unit=".$_GET['view_unit']);
                    } else {
                        $testInstance = $test;
                        $test  -> getQuestions();                                    //This way the test's questions are populated, and we will be needing this information
                        $testInstance -> options['random_pool'] && $testInstance -> options['random_pool'] >= sizeof($testIn) ? $questionsNumber = $testInstance -> options['random_pool'] : $questionsNumber = sizeof($testInstance -> questions);
                    }
                    break;
            }
            if (isset($_GET['ajax'])) {
                $testInstance -> handleAjaxActions();
            }

            //Calculate total questions. If it's already set, then we are visiting an unsolved test, and the questions number is already calculated (and may be different that the $testInstance -> questions size)
            if (!isset($questionsNumber)) {
                $questionsNumber = sizeof($testInstance -> questions);
            }
            //$smarty -> assign("T_REMAINING_TIME", $remainingTime);
            $smarty -> assign("T_TEST_QUESTIONS_NUM", $questionsNumber);
            $smarty -> assign("T_TEST_DATA", $testInstance);
            $smarty -> assign("T_TEST", $testString);
            $smarty -> assign("T_TEST_STATUS", $status);

            if (!$status['status'] || ($status['status'] == 'incomplete' && $testInstance -> time['pause'])) {          //If the user hasn't confirmed he wants to do the test, display confirmation buttons
                $smarty -> assign("T_SHOW_CONFIRMATION", true);
            } else {                                                                                     //The user confirmed he wants to do the test, so display it
                $form   -> addElement('hidden', 'time_start', $timeStart);                                       //This element holds the time the test started, so we know the remaining time even if the user left the system
                $form   -> addElement('submit', 'submit_test', _SUBMITTEST, 'class = "flatButton" onclick = "if (typeof(checkedQuestions) != \'undefined\' && (unfinished = checkQuestions())) return confirm(\''._YOUHAVENOTCOMPLETEDTHEFOLLOWINGQUESTIONS.': \'+unfinished+\'. '._AREYOUSUREYOUWANTTOSUBMITTEST.'\');"');
                if ($testInstance -> options['pause_test']) {
                    $form -> addElement('submit', 'pause_test', _PAUSETEST, 'class = "flatButton"');
                }

                if ($form -> isSubmitted() && $form -> validate()) {
                    $values = $form -> exportValues();
                    if (isset($values['pause_test'])) {
                        $testInstance -> pause($values['question'], $_POST['goto_question']);
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=content&type=tests");
                    } else {
                        //Set the unit as "seen"
                        $currentUser  -> setSeenUnit($currentUnit, $currentLesson, 1);
                        $testInstance -> complete($values['question']);
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=tests&view_unit=".$_GET['view_unit']);
                    }
                }

                $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
                $form   -> accept($renderer);
                $smarty -> assign('T_TEST_FORM', $renderer -> toArray());
            }

        } else {                                                                            //The user sees the list of tests
            $visitableIterator = new EfrontTestsFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST))));

            $smarty -> assign("T_CONTENT_TREE",  $currentContent -> toHTML($iterator, 'dhtmlContentTree', array('truncateNames' => 25, 'selectedNode' => $currentUnit['id'])));
            $smarty -> assign("T_UNIT",          $currentUnit);
            $smarty -> assign("T_NEXT_UNIT",     $currentContent -> getNextNode($currentUnit, $visitableIterator));
            $smarty -> assign("T_PREVIOUS_UNIT", $currentContent -> getPreviousNode($currentUnit, $visitableIterator));        //Next and previous units are needed for navigation buttons
            $smarty -> assign("T_PARENT_LIST",   $currentContent -> getNodeAncestors($currentUnit));       //Parents are needed for printing the titles
            $smarty -> assign("T_NO_TEST", true);
            if ($ruleCheck !== true) {
                $message      = $ruleCheck;
                $message_type = false;
                $smarty -> assign("T_RULE_CHECK_FAILED", true);
            }
        }
    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = _ERRORLOADINGCONTENT.": ".$_SESSION['s_lessons_ID'].": ".$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    }

}
/*
*/
elseif ($ctg == 'calendar') {
    if ($currentUser -> coreAccess['calendar'] != 'hidden') {
        include_once "calendar.php";
    } else {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
}
/*
The glossary page is responsible for viewing and manipulating glossary words
*/
elseif ($ctg == 'glossary') {
    if ($currentUser -> coreAccess['content'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    $glossary_words = eF_getTableData("glossary_words", "name,info", "lessons_ID=".$_SESSION['s_lessons_ID']);

    $words = eF_getAllGlossaryWords($glossary_words);
    $smarty -> assign("T_GLOSSARY", $words);
}

/*
This is the page that has to do with surveys
*/
elseif ($ctg == 'survey') {
    if ($currentUser -> coreAccess['surveys'] == 'hidden') {
        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
    $load_editor=true;
    include_once "module_surveys.php";
}
/*
The student statistics depend entirely on module_statisics.php
*/
elseif ($ctg == 'statistics') {
    if (isset($_GET['show_solved_test']) && eF_checkParameter($_GET['show_solved_test'], 'id') && isset($_GET['lesson']) && eF_checkParameter($_GET['lesson'], 'id')) {
        try {
            //pr($_GET['lesson']);pr($currentUser -> getLessons());
            if (in_array($_GET['lesson'], array_keys($currentUser -> getLessons()))) {
                $result   = eF_getTableData("done_tests, tests, content", "done_tests.tests_ID, done_tests.users_LOGIN", "content.id=tests.content_ID and content.lessons_ID=".$_GET['lesson']." and tests.id = done_tests.tests_ID and done_tests.users_LOGIN = '".$currentUser -> user['login']."' and done_tests.id=".$_GET['show_solved_test']);
                if (sizeof($result) > 0) {
                    $showTest = new EfrontTest($result[0]['tests_ID']);
                    //Set "show answers" and "show given answers" to true, since if it is not the student that sees the test
                    if ($currentUser -> user['user_type'] != 'student') {
                        $showTest -> options['answers']       = 1;
                        $showTest -> options['given_answers'] = 1;
                    }
                    $showTest -> setDone($result[0]['users_LOGIN']);
                    $smarty   -> assign("T_CURRENT_TEST", $showTest -> test);
                    $smarty   -> assign("T_SOLVED_TEST_DATA", $showTest -> doneInfo);
                    $smarty   -> assign("T_TEST_SOLVED", $showTest -> toHTMLQuickForm(new HTML_Quickform(), false, true));
                } else {
                    $message      = _USERHASNOTDONETEST;
                    $message_type = 'failure';
                }
            } else {
                $message      = _USERHASNOTTHISLESSON;
                $message_type = 'failure';
            }
        } catch (Exception $e) {
            $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
            $message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
            $message_type = 'failure';
        }
    } else {
        /**The statistics funtions*/
        if ($currentUser -> coreAccess['statistics'] != 'hidden') {
            require_once "module_statistics.php";
        } else {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
    }
}
/*
The lessons page is the page where the user chooses which lesson to view
*/
elseif ($ctg == 'lessons') {
    if (isset($_GET['op']) && $_GET['op'] == 'tests') {
        if (isset($_GET['solve_test'])) {

            if (isset($_GET['confirm'])) {
                $form    = new HTML_QuickForm("test_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lessons&op=tests&solve_test='.$_GET['solve_test'].'&confirm=1', "", null, true);

                if ($form -> isSubmitted() && $form -> validate()) {

                    // The skillgap test has been solved and submitted
                    $result = eF_getTableData("completed_tests", "*", "tests_id = '".$_GET['solve_test']."' AND users_LOGIN = '".$currentUser -> user['login']."'");
                    $testInstance = unserialize($result[0]['test']);

                    $testString    = $testInstance -> toHTMLQuickForm($form);
                    $testString    = $testInstance -> toHTML($testString, $remainingTime);

                    $values = $form -> exportValues();
                    //Set the unit as "seen"
                    //$currentUser  -> setSeenUnit($currentUnit, $currentLesson, 1);
                    $testInstance -> completedTest['status'] = 'completed';

                    $testInstance -> complete($values['question']);

                    eF_updateTableData("users_to_skillgap_tests", array("solved" => "1"), "tests_ID = '".$_GET['solve_test']."' AND users_LOGIN = '".$currentUser -> user['login']."'");

                    // Check if you should automatically assign lessons and courses to the student
                    if ($testInstance -> options['automatic_assignment']) {
                        $analysisResults = $testInstance -> analyseSkillGapTest();

                        foreach ($analysisResults['lessons'] as $lesson) {
                            $currentUser -> addLessons($lesson['lesson_ID']);
                        }
                        foreach ($analysisResults['courses'] as $course) {
                            $currentUser -> addCourses($course['courses_ID']);

                        }
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=lessons&op=tests&message=". _SKILLGAPTESTCOMPLETEDSUCCESSFULLYANDTHECORRESPONDING . " " . sizeof($analysisResults['lessons'] ) . " " . _LESSONS . " " . _AND . " ". sizeof($analysisResults['courses']). " " . _COURSES . " " . _HAVEBEENASSIGNED . "&message_type=success");
                    } else {
                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=lessons&op=tests&message=". _SKILLGAPTESTCOMPLETEDSUCCESSFULLY . ". " . _YOURRESULTSHAVEBEENSENTTOYOURPROFESSORSWHOWILLASSIGNLESSONSACCORDINGTOYOURNEEDS . "&message_type=success");
                    }

                    exit;
                }

//HACK to remove incomplete tests
                eF_deleteTableData("completed_tests", "tests_id = '".$_GET['solve_test']."' AND users_LOGIN = '".$currentUser -> user['login']."'");
                $test   = new EfrontTest($_GET['solve_test']);
                $testInstance = $test -> start($currentUser -> user['login']);

                // Hard coded to disallow pause test
                $testInstance -> options['pause_test'] = 0;

                $testString    = $testInstance -> toHTMLQuickForm($form);
                $testString    = $testInstance -> toHTML($testString, $remainingTime);

                $form   -> addElement('hidden', 'time_start', $timeStart);                                       //This element holds the time the test started, so we know the remaining time even if the user left the system
                $form   -> addElement('submit', 'submit_test', _SUBMITTEST, 'class = "flatButton" onclick = "if (typeof(checkedQuestions) != \'undefined\' && (unfinished = checkQuestions())) return confirm(\''._YOUHAVENOTCOMPLETEDTHEFOLLOWINGQUESTIONS.': \'+unfinished+\'. '._AREYOUSUREYOUWANTTOSUBMITTEST.'\');"');
                if ($testInstance -> options['pause_test']) {
                    $form -> addElement('submit', 'pause_test', _PAUSETEST, 'class = "flatButton"');
                }

                $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
                $form   -> accept($renderer);

                $smarty -> assign('T_TEST_FORM', $renderer -> toArray());
//                        header("location:".basename($_SERVER['PHP_SELF'])."?ctg=lessons&op=tests&");
            } else {
                $form    = new HTML_QuickForm("test_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=lessons&op=tests', "", null, true);
                $test   = new EfrontTest($_GET['solve_test']);
                $testInstance = $test;
                $test  -> getQuestions();                                    //This way the test's questions are populated, and we will be needing this information
                $testInstance -> options['random_pool'] && $testInstance -> options['random_pool'] >= sizeof($testIn) ? $questionsNumber = $testInstance -> options['random_pool'] : $questionsNumber = sizeof($testInstance -> questions);
                $smarty -> assign("T_SHOW_CONFIRMATION", true);
            }

            if (isset($_GET['ajax'])) {
                $testInstance -> handleAjaxActions();
            }

            //Calculate total questions. If it's already set, then we are visiting an unsolved test, and the questions number is already calculated (and may be different that the $testInstance -> questions size)
            if (!isset($questionsNumber)) {
                $questionsNumber = sizeof($testInstance -> questions);
            }

            //$smarty -> assign("T_REMAINING_TIME", $remainingTime);
            $smarty -> assign("T_TEST_QUESTIONS_NUM", $questionsNumber);
            $smarty -> assign("T_TEST_DATA", $testInstance);
            $smarty -> assign("T_TEST", $testString);
            $smarty -> assign("T_TEST_STATUS", $status);

        } else {

            $tests     = $currentUser -> getSkillgapTests();

            $test_array = array();
            foreach ($tests as $test) {
                if ($test['solved']) {
                    $test_array[] = array('text' => $test['name'],  'image' => "32x32/checks.png",      'href' => 'javascript:void(0);');
                } else {
                    $test_array[] = array('text' => $test['name'],  'image' => "32x32/edit32x32.png",           'href' => $_SESSION['s_type'] . ".php?ctg=lessons&op=tests&solve_test=" . $test['id']);
                }

            }

            // Present a list of tests
            if (!empty($test_array)) {
                $smarty -> assign("T_TESTS", $test_array);

            }
        }

    } else {
        $directionsTree = new EfrontDirectionsTree();

        $userLessons        = $currentUser -> getLessons(true);
        $userLessonProgress = EfrontStats :: getUsersLessonStatus($userLessons, $currentUser -> user['login']);
 //pr($userLessonProgress);       
		$userCourses        = $currentUser -> getCourses(true);
        $userCourseProgress = EfrontStats :: getUsersCourseStatus($userCourses, $currentUser -> user['login']);
        /*Assign progress in a per-lesson fashion*/
        $temp = array();
        foreach ($userLessonProgress as $lessonId => $user) {
            $temp[$lessonId] = $user[$currentUser -> user['login']];
        }
        $userProgress['lessons'] = $temp;
        /*Assign progress in a per-course fashion*/
        $temp = array();
        foreach ($userCourseProgress as $courseId => $user) {
            $temp[$courseId] = $user[$currentUser -> user['login']];
        }
        $userProgress['courses'] = $temp;

        $options      = array('lessons_link' => '#user_type#.php?lessons_ID=',
                              'courses_link' => false);

        if (sizeof ($userLessons) > 0 || sizeof($userCourses) > 0) {
            $smarty -> assign("T_DIRECTIONS_TREE", $directionsTree -> toHTML(false, $userLessons, $userCourses, $userProgress, $options));
        }


        // Find all unsolved user skillgap tests
        $userSkillgapTests = $currentUser -> getSkillgapTests();
        foreach($userSkillgapTests as $skid => $skillGap) {
            if ($skillGap['solved']) {
                unset($userSkillgapTests[$skid]);
                $only_found_solved = 1;
            }
        }

        if (!empty($userSkillgapTests)) {
            $labelText = _NEWSKILLGAPTESTS . ":&nbsp;";
            if (sizeof($userSkillgapTests) > 1) {
                $labelText .= "<br>";
            }

            foreach($userSkillgapTests as $skillGap) {
                $labelText .= $skillGap['name'];
            }

            $smarty -> assign("T_SKILLGAP_TESTS", $labelText);
        } else if ($only_found_solved) {
            $smarty -> assign("T_SKILLGAP_TESTS_SOLVED", 1);
        }

    }

    if (isset($_GET['export']) && $_GET['export'] == 'rtf') {
        if (isset($currentUser -> coreAccess['lessons']) && $currentUser -> coreAccess['lessons'] != 'change') {
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
            exit;
        }
        $result = eF_getTableData("users_to_courses", "*", "users_LOGIN = '".$_GET['user']."' and courses_ID = '".$_GET['course']."' limit 1");
        if (sizeof($result) == 1 || isset($_GET['preview'])) {
            $course = new EfrontCourse($_GET['course']);
            if (!isset($_GET['preview'])){
                $certificate_tpl_id = $course -> course['certificate_tpl_id'];
                if ($certificate_tpl_id <= 0) {
                    $cfile = new EfrontFile(G_CERTIFICATETEMPLATEPATH."certificate1.rtf");
                } else {
                    $cfile = new EfrontFile($certificate_tpl_id);
                }
                $template_data = file_get_contents($cfile['path']);
                $issued_data = unserialize($result[0]['issued_certificate']);
                $certificate = $template_data;
                if (sizeof($issued_data) > 1){
                    $certificate   = $template_data;
                    $certificate   = str_replace("#organization#", utf8ToUnicode($issued_data['organization']), $certificate);
                    $certificate   = str_replace("#user_name#", utf8ToUnicode($issued_data['user_name']), $certificate);
                    $certificate   = str_replace("#user_surname#", utf8ToUnicode($issued_data['user_surname']), $certificate);
                    $certificate   = str_replace("#course_name#", utf8ToUnicode($issued_data['course_name']), $certificate);
                    $certificate   = str_replace("#grade#", utf8ToUnicode($issued_data['grade']), $certificate);
                    $certificate   = str_replace("#date#", utf8ToUnicode($issued_data['date']), $certificate);
                }
            }
            else {
                $certificateDirectory = G_CERTIFICATETEMPLATEPATH;
                $selectedCertificate  = $_GET['certificate_tpl'];
                $certificate          = file_get_contents($certificateDirectory.$selectedCertificate);
            }
            $filename = "certificate_".$_GET['user'].".rtf";
            header("Content-type: application/rtf");
            header("Content-disposition: inline; filename=$filename");
            header("Content-length: " . strlen($certificate));
            echo $certificate;
            exit(0);
        }
    }
}
/*
From this page the student may access and alter its personal information,
as well as join new lessons and courses
*/
elseif ($ctg == 'personal') {
    $login = $_SESSION['s_login'];

    /**This part is used to display the user's personal information*/
    include "module_personal.php";

    $log_comments = 1;                                              //The $log_comments variable is used at the log entry.
}
/**************/
/* MODULE HCD */
/**************/
elseif ($ctg == 'module_hcd' && MODULE_HCD_INTERFACE) {
   include "module_hcd.php";
}

/*
///MODULES3: Include the module code
 */
elseif ($ctg == 'module') {

//    $loadScripts = array_merge($loadScripts, array('scriptaculous/prototype', 'scriptaculous/scriptaculous'));
    $className = $_GET['op'];
    if (isset($loadedModules[$className])) {

        // Get top title navigational links
        $nav_links = $loadedModules[$className] -> getNavigationLinks();
        if ($nav_links) {
            $links_size = sizeof($nav_links);
            $count = 0;
            $module_nav_links = "";
            foreach ($nav_links as $link) {
                $module_nav_links .= '<a class="titleLink" href ="'.$link['link'].'">'.$link['title'].'</a>';
                $count++;
                if ($count < $links_size) {
                    $module_nav_links .= '&nbsp;&raquo;&nbsp;';
                }
            }
        } else {
            $module_nav_links = '<a class="titleLink" href ="'.$loadedModules[$className] -> moduleBaseUrl.'">'.$loadedModules[$className] -> getName().'</a>';
        }
        $smarty -> assign("T_MODULE_NAVIGATIONAL_LINKS", $module_nav_links);

        // Get link to highlight on the left sidebar
        $highlight = $loadedModules[$className] -> getLinkToHighlight();
        if ($highlight) {
            $highlight = $loadedModules[$className] -> className . "_" . $highlight;
        } else {
            $highlight = $loadedModules[$className] -> className;
        }
        $smarty -> assign("T_MODULE_HIGHLIGHT", $highlight);

        // Get module html - two ways: pure HTML or PHP+smarty
        // If no smarty file is defined then false will be returned
        if ($module_smarty_file = $loadedModules[$className] -> getSmartyTpl()) {
            // Execute the php code
            $loadedModules[$className] -> getModule();
            // Let smarty know to include the module smarty file
            $smarty -> assign("T_MODULE_SMARTY", $module_smarty_file);
        } else {
            // Present the pure HTML code
            $smarty -> assign("T_MODULE_PAGE", $loadedModules[$className] -> getModule());
        }
    } else {
        $message = _ERRORLOADINGMODULE;
        $message_type = "failure";
    }

}


/*
Emails is the page that is used to send email to system users.
*/
elseif ($ctg == "emails" && MODULE_HCD_INTERFACE) {
   include "emails.php";
}
/*
Users is the page that concerns EMPLOYEE administration for users with supervisor rights. It uses module_personal.php to perform most of the update functions,
since the same functions need to be performed from the professor and student as well (for themseleves)
There are 5 sub options in this page, denoted by an extra link part:
- &add_user=1                   When we are adding a new user
- &delete_user=<login>          When we want to delete user <login>
- &edit_user=<login>            When we want to edit user <login>
- &deactivate_user=<login>      When we deactivate user <login>
- &activate_user=<login>        When we activate user <login>
*/
elseif ($ctg == 'users' && MODULE_HCD_INTERFACE) {
    $currentUser -> aspects['hcd'] = EfrontEmployeeFactory :: factory($currentUser -> login);
    $currentEmployee = $currentUser -> aspects['hcd'];
    $unprivileged = false;                          //This variable is used to check whether the current user is elegible (based on his role) to access this area
    if ($currentUser -> getType() != "administrator" && $currentEmployee -> getType() != _SUPERVISOR) {
        $message      = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = "failure";
        header("location:".$_SERVER['HTTP_REFERER']."&message=".$message."&message_type=".$message_type);
        //header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=chart&message=".$message."&message_type=".$message_type);
        exit;
    } else {

        if (isset($_GET['delete_user']) && eF_checkParameter($_GET['delete_user'], 'login') && !$unprivileged) {    //The administrator asked to delete a user
            if (eF_deleteUser($_GET['delete_user'])) {

                /** MODULE HCD: Delete the employee relevant information **/
                if ($module_hcd_interface) {
                    eF_deleteTableData("module_hcd_employees", "users_login='".$_GET['delete_user']."'");
                    eF_deleteTableData("module_hcd_employee_has_skill", "users_login='".$_GET['delete_user']."'");
                    eF_deleteTableData("module_hcd_employee_has_job_description", "users_login='".$_GET['delete_user']."'");
                    eF_deleteTableData("module_hcd_employee_works_at_branch", "users_login='".$_GET['delete_user']."'");

                    // Register user's firing into the event log
                    eF_insertTableData("module_hcd_events", array("event_code"    => $MODULE_HCD_EVENTS['FIRED'],
                                                                  "users_login"   => $_GET['delete_user'],
                                                                  "specification" => _FIRED,
                                                                  "timestamp"     => time()));
                    $message      = _EMPLOYEEDELETED;
                } else {
                    $message      = _USERDELETED;
                }
                $message_type = 'success';
            } else {
                $message      = _SOMEORALLOFTHEUSERELEMENTSCOULDNOTBEDELETED;
                $message_type = "failure";
            }
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=users&message=".$message."&message_type=".$message_type);
        } elseif (isset($_GET['deactivate_user']) && eF_checkParameter($_GET['deactivate_user'], 'login') && !$unprivileged) {      //The administrator asked to deactivate a user
            if (eF_updateTableData("users", array('active' => 0), "login='".$_GET['deactivate_user']."'")) {
                $message      = _USERDEACTIVATED;
                $message_type = 'success';
            } else {
                $message      = _SOMEPROBLEMEMERGED;
                $message_type = "failure";
            }
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=users&message=".$message."&message_type=".$message_type);
        } elseif (isset($_GET['activate_user']) && eF_checkParameter($_GET['activate_user'], 'login') && !$unprivileged) {          //The administrator asked to activate a user
            if (eF_updateTableData("users", array('active' => 1, 'pending' => 0), "login='".$_GET['activate_user']."'")) {
                $message      = _USERACTIVATED;
                $message_type = 'success';
            } else {
                $message      = _SOMEPROBLEMEMERGED;
                $message_type = "failure";
            }
            header("location:".basename($_SERVER['PHP_SELF'])."?ctg=users&message=".$message."&message_type=".$message_type);
        } elseif (isset($_GET['add_user']) || (isset($_GET['edit_user']) && $login = eF_checkParameter($_GET['edit_user'], 'login')) && !$unprivileged) {   //The administrator asked to add a new user or to edit a user
            $smarty -> assign("T_PERSONAL", true);
            /**Include the personal settings file*/
            include "module_personal.php";                      //User addition and manipulation is done through module_personal.

        } else {                                                //The professor just asked to view the users
    //dddddddddddddddddd
            if (!$module_hcd_interface) {
                $result_with_lessons    = eF_getTableData("users_to_lessons, users","count( * ) AS lessons_num, users.*", "users_LOGIN = login AND users_LOGIN IN ( SELECT login FROM users) GROUP BY login");
                $result_without_lessons = eF_getTableData("users","*", "login NOT IN ( SELECT DISTINCT (users_LOGIN) FROM users_to_lessons)");
                $result = array_merge($result_with_lessons, $result_without_lessons);  //right is this: SELECT name, login, count( lessons_ID )FROM usersLEFT OUTER JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN GROUP BY login
                for ($i = 0; $i < sizeof($result); $i++) {
                    foreach ($result[$i] as $key => $value) {
                        if ($key == 'user_type') {
                            $result[$i][$key] = $TRANSLATION[$value];
                        }
                    }
                }
                $smarty -> assign("T_USERS", $result);

            } else {

                $_GET['op'] = "employees";
                include "module_hcd.php";
            }
        }
   }
}
/*
At this point, we apply module functionality
*/
elseif (sizeof($modules) > 0 && in_array($ctg, array_keys($module_ctgs))) {
    $module_mandatory = eF_getTableData("modules", "mandatory", "name = '".$ctg."'");
    if ($module_mandatory[0]['mandatory'] != 'false' || ($GLOBALS['currentLesson'] -> options[$ctg])) {
        include(G_MODULESPATH.$ctg.'/module.php');
        $smarty -> assign("T_CTG_MODULE", $module_ctgs[$ctg]);
    }
}

$fields_log = array ('users_LOGIN' => $_SESSION['s_login'],         //This is the log entry array
                     'timestamp'   => time(),
                     'session_ip'  => eF_encodeIP($_SERVER['REMOTE_ADDR']));

if (isset($log_comments)) {                                         //If there is a $log_comments variable, it indicates the current action (i.e. the unit that the user saw)
    $fields_log['action']   = $ctg;
    $fields_log['comments'] = $log_comments;
    ($_SESSION['s_lessons_ID']) ? $fields_log['lessons_ID'] = $_SESSION['s_lessons_ID'] : $fields_log['lessons_ID'] = 0;
    eF_insertTableData("logs", $fields_log);
} else {                                                            //Any other move, that has not set the $log_comments variable, is considered a 'lastmove' action
    $fields_log['action']   = "lastmove";
    $fields_log['comments'] = "";
    ($_SESSION['s_lessons_ID']) ? $fields_log['lessons_ID'] = $_SESSION['s_lessons_ID'] : $fields_log['lessons_ID'] = 0;
    eF_deleteTableData("logs", "users_LOGIN='".$_SESSION['s_login']."' AND action='lastmove'"); //Only one lastmove action interests us, so delete any other
    eF_insertTableData("logs", $fields_log);
}

$smarty -> assign("T_HEADER_EDITOR", $load_editor);                 //Specify whether we need to load the editor

if (isset($_GET['refresh'])) {
    $smarty -> assign("T_REFRESH_SIDE","true");
}

///MODULES5
$smarty -> assign("T_MODULE_CSS", $module_css_array);
$smarty -> assign("T_MODULE_JS", $module_js_array);
foreach ($loadedModules as $module) {
    $loadScripts = array_merge($loadScripts, $module -> addScripts());
}

if ($message) {
    $loadScripts[] = 'scriptaculous/scriptaculous';
    $loadScripts[] = 'scriptaculous/effects';
}

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($loadScripts));

$smarty -> assign("T_CURRENT_CTG", $ctg);
$smarty -> assign("T_MENUCTG", $ctg);
$smarty -> assign("T_MENU", eF_getMenu());

$smarty -> assign("T_QUERIES", $numberOfQueries);

$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_SEARCH_MESSAGE", $search_message);

$smarty -> assign("T_CONFIGURATION", $configuration);       //Assign global configuration values to smarty
$smarty -> assign("T_CURRENT_USER", $currentUser);
$smarty -> assign("T_CURRENT_LESSON", $currentLesson);

$smarty -> load_filter('output', 'eF_template_formatTimestamp');
$smarty -> load_filter('output', 'eF_template_formatLogins');

$debug_timeBeforeSmarty = microtime(true) - $debug_TimeStart;
$smarty -> load_filter('output', 'eF_template_setInnerLinks');
$smarty -> display('student.tpl');
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





?>