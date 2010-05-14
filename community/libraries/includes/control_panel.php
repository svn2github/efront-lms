<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$loadScripts[] = 'scriptaculous/dragdrop';
$loadScripts[] = 'includes/control_panel';
try {
    // Insert a record into the logs table, if a lesson has been selected
    if (!$_admin_ && isset($_SESSION['s_lessons_ID'])) {
        $fields_log = array ('users_LOGIN' => $_SESSION['s_login'], //This is the log entry array
                          'timestamp' => time(),
                          'action' => 'lesson',
                          'comments' => 0,
                          'session_ip' => eF_encodeIP($_SERVER['REMOTE_ADDR']),
                          'lessons_ID' => $_SESSION['s_lessons_ID']);
        eF_deleteTableData("logs", "users_LOGIN='".$_SESSION['s_login']."' AND action='lastmove'"); //Only one lastmove action interests us, so delete any other
        eF_insertTableData("logs", $fields_log);
    }
    if (isset($_GET['op']) && $_GET['op'] == 'search') {
        /**Functions to perform searches*/
        require_once "module_search.php";
    } else if (isset($_GET['op']) && in_array($_GET['op'], array_keys($module_ctgs))) {
        $module_mandatory = eF_getTableData("modules", "mandatory", "name = '".$_GET['op']."'");
        if ($module_mandatory[0]['mandatory'] != 'false' || isset($currentLesson -> options[$_GET['op']]) || $_admin_) {
            include(G_MODULESPATH.$_GET['op'].'/module.php');
            $smarty -> assign("T_OP_MODULE", $module_ctgs[$_GET['op']]);
        }
    } else {
        $headerOptions = $controlPanelOptions = array();
        //Personal messages block (Common block)(
        if ((!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') && $GLOBALS['configuration']['disable_messages'] != 1) {
            $personal_messages = eF_getTableData("f_personal_messages pm, f_folders ff", "pm.title, pm.id, pm.timestamp, pm.sender", "pm.users_LOGIN='".$currentUser -> user['login']."' and f_folders_ID=ff.id and ff.name='Incoming' and viewed='no'", "pm.timestamp desc limit 10"); //Get unseen messages in Incoming folder
            $smarty -> assign("T_PERSONAL_MESSAGES", $personal_messages);
            $personal_message_options = array(
            array('text' => _MESSAGES, 'image' => "16x16/add.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=messages&add=1&popup=1", 'onClick' => "eF_js_showDivPopup('"._NEWMESSAGE."', 2)", 'target' => 'POPUP_FRAME'),
            array('text' => _GOTOINBOX, 'image' => "16x16/go_into.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=messages")
            );
            $smarty -> assign("T_PERSONAL_MESSAGES_OPTIONS", $personal_message_options);
            $smarty -> assign("T_PERSONAL_MESSAGES_LINK", basename($_SERVER['PHP_SELF'])."?ctg=messages");
        }
        //News block (Common block)
        if ($GLOBALS['configuration']['disable_news'] != 1 && ($_admin_ || $currentLesson -> options['news'])) {
            $news = news :: getNews(0, true);
            if (!$_admin_) {
                //Get lesson news as well
                $news = array_merge($news, news :: getNews($currentLesson -> lesson['id'], true));
            }
            if (!$_student_ && !isset($currentUser -> coreAccess['news']) || $currentUser -> coreAccess['news'] == 'change') {
                $newsOptions[] = array('text' => _ANNOUNCEMENTADD, 'image' => "16x16/add.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=news&add=1&popup=1", 'onClick' => "eF_js_showDivPopup('"._ANNOUNCEMENTADD."', 1)", 'target' => 'POPUP_FRAME');
            }
            $newsOptions[] = array('text' => _ANNOUNCEMENTGO, 'image' => "16x16/go_into.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=news");
            $smarty -> assign("T_NEWS", $news);
            $smarty -> assign("T_NEWS_OPTIONS", $newsOptions);
            $smarty -> assign("T_NEWS_LINK", basename($_SERVER['PHP_SELF'])."?ctg=news");
        }
        //Calendar block (Common block)
        if (!isset($currentUser -> coreAccess['calendar']) || $currentUser -> coreAccess['calendar'] != 'hidden') {
            $today = getdate(time()); //Get current time in an array
            $today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']); //Create a timestamp that is today, 00:00. this will be used in calendar for displaying today
            isset($_GET['view_calendar']) && eF_checkParameter($_GET['view_calendar'], 'timestamp') ? $view_calendar = $_GET['view_calendar'] : $view_calendar = $today; //If a specific calendar date is not defined in the GET, set as the current day to be today
            $calendarOptions = array();
            if (!$_student_ && (!isset($currentUser -> coreAccess['calendar']) || $currentUser -> coreAccess['content'] == 'change')) {
                $calendarOptions[] = array('text' => _ADDCALENDAR, 'image' => "16x16/add.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=calendar&add_calendar=1&view_calendar=".$view_calendar."&popup=1", "onClick" => "eF_js_showDivPopup('"._ADDCALENDAR."', 2)", "target" => "POPUP_FRAME", "id" => "add_new_event_link");
            }
            $calendarOptions[] = array('text' => _GOTOCALENDAR, 'image' => "16x16/go_into.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=calendar");
            $smarty -> assign("T_CALENDAR_OPTIONS", $calendarOptions);
            $smarty -> assign("T_CALENDAR_LINK", basename($_SERVER['PHP_SELF'])."?ctg=calendar");
            isset($_GET['add_another']) ? $smarty -> assign('T_ADD_ANOTHER', "1") : null;
                 $events = eF_getCalendar(false, 1);
            $smarty -> assign("T_CALENDAR_EVENTS", $events); //Assign events and specific day timestamp to smarty, to be used from calendar
            $smarty -> assign("T_VIEW_CALENDAR", $view_calendar);
        }
        //Admin specific blocks
        if ($_admin_) {
            //New users block (Admin block)
            if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
                $users = eF_getTableData("users", "login, surname, name, timestamp", "pending=1", "timestamp DESC"); //Find every user that is not active... new way
                $smarty -> assign("T_INACTIVE_USERS", $users); //Assign them to smarty, to be displayed at the first page
                $smarty -> assign("T_INACTIVE_USERS_LINK", basename($_SERVER['PHP_SELF'])."?ctg=users");
            }
            //New lessons block (Admin block)
            if (!isset($currentUser -> coreAccess['lessons']) || $currentUser -> coreAccess['lessons'] != 'hidden') {
                $lessons = eF_getTableData("users_to_lessons ul, lessons l", "DISTINCT users_LOGIN,  count(lessons_ID) AS count", "ul.archive=0 and l.archive=0 and ul.lessons_ID = l.id and l.course_only = 0 and ul.from_timestamp=0", "", "users_LOGIN"); //Get the new lesson registrations
                $smarty -> assign("T_NEW_LESSONS", $lessons); //Assign the list to smarty, to be displayed at the first page
                $constraints = array('archive' => false, 'active' => true);
                $courses = EfrontCourse :: getCoursesWithPendingUsers($constraints);
                $smarty -> assign("T_NEW_COURSES", $courses); //Assign the list to smarty, to be displayed at the first page
            }
        }
        //Professor and student common blocks
        if ($_professor_ || $_student_) {
            //Projects block
            if ($currentLesson -> options['projects']) {
                if ($_professor_) {
                    $result = eF_getTableData("users_to_projects as up,projects as p", "p.title,p.id,up.users_LOGIN,up.upload_timestamp", "p.lessons_ID=".$_SESSION['s_lessons_ID']." and p.id=up.projects_ID and filename!=''","up.upload_timestamp desc");
                    foreach ($result as $value) {
                        $projects[$value['id']] = $value;
                    }
                } else {
                    $projects = $currentLesson -> getProjects(false, $currentUser -> user['login'], true);
                }
                $smarty -> assign("T_PROJECTS", $projects);
                $projectOptions = array(array('text' => _GOTOPROJECTS, 'image' => "16x16/go_into.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=projects"));
                $smarty -> assign("T_PROJECTS_OPTIONS",$projectOptions);
                $smarty -> assign("T_PROJECTS_LINK",basename($_SERVER['PHP_SELF'])."?ctg=projects");
            }
            //New forum messages block
            if ((!isset($currentUser -> coreAccess['forum']) || $currentUser -> coreAccess['forum'] != 'hidden') && $GLOBALS['configuration']['disable_forum'] != 1) {
                //changed  l.name as show_lessons_name to l.name as lessons_name
    $forum_messages = eF_getTableData("f_messages fm JOIN f_topics ft JOIN f_forums ff LEFT OUTER JOIN lessons l ON ff.lessons_ID = l.id", "fm.title, fm.id, ft.id as topic_id, fm.users_LOGIN, fm.timestamp, l.name as lessons_name, lessons_id as show_lessons_id", "ft.f_forums_ID=ff.id AND fm.f_topics_ID=ft.id AND ff.lessons_ID = '".$currentLesson -> lesson['id']."'", "fm.timestamp desc LIMIT 5");
                $forum_lessons_ID = eF_getTableData("f_forums", "id", "lessons_ID=".$_SESSION['s_lessons_ID']);
                $smarty -> assign("T_FORUM_MESSAGES", $forum_messages);
                $smarty -> assign("T_FORUM_LESSONS_ID", $forum_lessons_ID[0]['id']);
                $forumOptions = array();
                if ($forum_lessons_ID[0]['id']) {
                    if (!isset($currentUser -> coreAccess['forum']) || $currentUser -> coreAccess['forum'] == 'change') {
                        $forumOptions[] = array('text' => _SENDMESSAGEATFORUM, 'image' => "16x16/add.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=forum&add=1&type=topic&forum_id=".$forum_lessons_ID[0]['id']."&popup=1", 'onclick' => "eF_js_showDivPopup('"._NEWMESSAGE."', 2)", 'target' => 'POPUP_FRAME');
                    }
                }
                $forumOptions[] = (array('text' => _GOTOFORUM, 'image' => "16x16/go_into.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=forum"));
                $smarty -> assign("T_FORUM_OPTIONS", $forumOptions);
                $smarty -> assign("T_FORUM_LINK", basename($_SERVER['PHP_SELF'])."?ctg=forum&forum=".$forum_lessons_ID[0]['id']);
            }
            //Comments block
            if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] != 'hidden') {
                $comments = comments :: getComments(false, false, false, 5);
                $smarty -> assign("T_COMMENTS", $comments);
            }
        }
        //Professor specific blocks
        if ($_professor_) {
            //Completed tests list
            if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] != 'hidden') {
                $testIds = $currentLesson -> getTests(false, true);
                if (sizeof($testIds) > 0) {
                    $result = eF_getTableData("completed_tests ct, tests t", "ct.*, ct.id, ct.users_LOGIN, ct.timestamp, ct.status, t.name", "ct.status != 'deleted' and ct.pending=1 and ct.status != 'incomplete' and ct.archive = 0 and ct.tests_ID = t.id and ct.tests_ID in (".implode(",", $testIds).")", "", "ct.timestamp DESC limit 10");
                    $smarty -> assign("T_COMPLETED_TESTS", $result);
                }
            }
        }
        //Student specific blocks
        if ($_student_) {
            $currentContent = new EfrontContentTree($currentLesson);
            $currentContent -> markSeenNodes($currentUser);
            //Content tree block
            if ($GLOBALS['configuration']['disable_tests'] != 1) {
                $iterator = new EfrontVisitableAndEmptyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)));
                $firstNodeIterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1)));
            } else {
                $iterator = new EfrontTheoryFilterIterator(new EfrontVisitableAndEmptyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1))));
                $firstNodeIterator = new EfrontTheoryFilterIterator(new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST), array('active' => 1))));
            }
            if ($currentLesson -> options['content_tree']) {
                $smarty -> assign("T_CONTENT_TREE", $currentContent -> toHTML($iterator, false, array('truncateNames' => 60)));
            }
            //Progress, status and start/continue block
            if (!$currentLesson -> options['tracking'] || $currentUser -> coreAccess['content'] == 'hidden') {
                $currentLesson -> options['lesson_info'] ? $controlPanelOptions[] = array('text' => _LESSONINFORMATION, 'image' => '32x32/information.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg=lesson_information', 'onClick' => "eF_js_showDivPopup('"._LESSONINFORMATION."', 2)", 'target' => 'POPUP_FRAME') : null;
            } else {
                $userProgress = EfrontStats :: getUsersLessonStatus($currentLesson, $currentUser -> user['login']);
                $userProgress = $userProgress[$currentLesson -> lesson['id']][$currentUser -> user['login']];
                $seenContent = EfrontStats::getStudentsSeenContent($currentLesson, $currentUser);
                $seenContent = $seenContent[$currentLesson -> lesson['id']][$currentUser -> user['login']];
                $result = eF_getTableData("users_to_lessons", "current_unit", "users_LOGIN = '".$currentUser -> user['login']."' and lessons_ID = ".$currentLesson -> lesson['id']);
                sizeof($result) > 0 ? $userProgress['current_unit'] = $result[0]['current_unit'] : $userProgress['current_unit'] = false;
                if ($userProgress['lesson_passed'] && !$userProgress['completed']) {
                    if (!$userProgress['completed'] && $currentLesson -> options['auto_complete']) {
                        $userProgress['tests_avg_score'] ? $avgScore = $userProgress['tests_avg_score'] : $avgScore = 100;
                        $timestamp = _AUTOCOMPLETEDAT.': '.date("Y/m/d, H:i:s");
                        $currentUser -> completeLesson($currentLesson, $avgScore, $timestamp);
                        $userProgress['completed'] = 1;
                        $userProgress['score'] = $avgScore;
                        $userProgress['comments'] = $timestamp;
                    } else {
                        $headerOptions[] = array('text' => _YOUHAVEMETCONDITIONS, 'image' => '32x32/semi_success.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg=lesson_information&popup=1', 'onClick' => "eF_js_showDivPopup('"._LESSONINFORMATION."', 2)", 'target' => 'POPUP_FRAME');
                    }
                }
                //Separate if because it might have just been set completed, from the previous if
                if ($userProgress['completed']) {
                    $smarty -> assign("T_LESSON_COMPLETED", $userProgress['completed']);
                    $headerOptions[] = array('text' => _LESSONCOMPLETE, 'image' => '32x32/success.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg=progress&popup=1', 'onclick' => "eF_js_showDivPopup('"._LESSONINFORMATION."', 2)", 'target' => 'POPUP_FRAME');
                }
                if ($userProgress['current_unit']) { //If there exists a value within the 'current_unit' attribute, it means that the student was in the lesson before. Seek the first unit that he hasn't seen yet
                    $firstUnseenUnit = $currentContent -> getFirstNode();
                    //Get to the first unseen unit
                    while ($firstUnseenUnit && (!$firstUnseenUnit['active'] || in_array($firstUnseenUnit['id'], array_keys($seenContent)))) {
                        $firstUnseenUnit = $currentContent -> getNextNode($firstUnseenUnit, $firstNodeIterator);
                    }
                    if (!$firstUnseenUnit) {
                        $firstUnseenUnit = $currentContent -> getFirstNode();
                    }
                    if ($currentLesson -> options['start_resume'] && !$userProgress['completed'] && !$userProgress['lesson_passed']) {
                        $headerOptions[] = array('text' => _RESUMELESSON, 'image' => '32x32/continue.png', 'href' => basename($_SERVER['PHP_SELF']).'?view_unit='.$firstUnseenUnit['id']);
                    }
                    $smarty -> assign("T_CURRENT_UNIT", $firstUnseenUnit);
                } else {
                    $iterator = new EfrontVisitableFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($currentContent -> tree), RecursiveIteratorIterator :: SELF_FIRST)));
                    $iterator -> next();
                    $firstUnseenUnit = $firstUnit = $iterator -> current();
                    if ($firstUnit && $currentLesson -> options['start_resume'] && !$userProgress['completed'] && !$userProgress['lesson_passed']) {
                        $headerOptions[] = array('text' => _STARTLESSON, 'image' => '32x32/start.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg=content&view_unit='.$firstUnit['id']);
                    }
                }
                if (isset($currentLesson -> options['show_dashboard']) && !$currentLesson -> options['show_dashboard']) {
                    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=content&view_unit=".$firstUnseenUnit['id']);
                }
                $currentLesson -> options['lesson_info'] ? $headerOptions[] = array('text' => _LESSONINFORMATION, 'image' => '32x32/information.png', 'href' => basename($_SERVER['PHP_SELF']).'?ctg=lesson_information&popup=1', 'onClick' => "eF_js_showDivPopup('"._LESSONINFORMATION."', 2)", 'target' => 'POPUP_FRAME') : null;
            }
            //Digital library mini file manager block
            if ($currentLesson -> options['digital_library'] && $currentUser -> coreAccess['content'] != 'hidden') { //If the lesson digital library is enabled
    $result = eF_getTableData("files", "*", "shared=".$currentLesson -> lesson['id']);
    foreach ($result as $value) {
     $sharedFiles[G_ROOTPATH.$value['path']] = new EfrontFile($value['id']);
    }
                if (sizeof($sharedFiles) > 0) {
                    $basedir = $currentLesson -> getDirectory();
                    $options = array('share' => false, 'zip' => false, 'folders' => false, 'delete' => false, 'edit' => false, 'create_folder' => false, 'upload' => false);
                    $url = basename($_SERVER['PHP_SELF']).'?ctg=control_panel';
                    $filesystem = new FileSystemTree($basedir, true);
     //changed to take account subfolders in efficient way
                    $filesystemIterator = new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new EfrontDBOnlyFilterIterator(new EfrontFileOnlyFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($sharedFiles), RecursiveIteratorIterator :: SELF_FIRST))), array('shared' => $currentLesson -> lesson['id'])));
     $smarty -> assign("T_FILES_LIST_OPTIONS", array(array('text' => _SHAREDFILES, 'image' => "16x16/go_into.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=digital_library")));
                    $smarty -> assign("T_FILE_LIST_LINK", basename($_SERVER['PHP_SELF'])."?ctg=digital_library");
                    /**The file manager*/
                    include ("file_manager.php");
                }
            }
        }
        //This is a notifier for cookies handling the show/hide status of inner tables. It affects only control panel and is considered inside printInnerTable smarty plugin
        $innerTableIdentifier = $currentUser -> user['user_type'].'_cpanel';
        //Calculate element positions, so they can be rearreanged accordingly to the user selection
        if ($_admin_) {
            $elementPositions = eF_getTableData("configuration", "value", "name='".$_SESSION['s_login']."_positions'");
            $elementPositions[0]['positions'] = $elementPositions[0]['value'];
        } else {
            $elementPositions = eF_getTableData("users_to_lessons", "positions", "lessons_ID=".$currentLesson -> lesson['id']." AND users_LOGIN='".$currentUser -> user['login']."'");
            if ($_student_ && sizeof($elementPositions) == 0 && $currentLesson -> options['default_positions']) {
                $elementPositions[0]['positions'] = $currentLesson -> options['default_positions'];
            }
        }
        if (sizeof($elementPositions) > 0) {
            $elementPositions = unserialize($elementPositions[0]['positions']); //Get the inner tables positions, stored by the user.
            !is_array($elementPositions['first']) ? $elementPositions['first'] = array() : null;
            !is_array($elementPositions['second']) ? $elementPositions['second'] = array() : null;
            $smarty -> assign("T_POSITIONS_FIRST", $elementPositions['first']); //Assign element positions to smarty
            $smarty -> assign("T_POSITIONS_SECOND", $elementPositions['second']);
            $smarty -> assign("T_POSITIONS_VISIBILITY", $elementPositions['visibility']);
            $smarty -> assign("T_POSITIONS", array_merge($elementPositions['first'], $elementPositions['second']));
            if ($_student_ && $elementPositions['update']) {
                foreach ($_COOKIE['innerTables'] as $key => $value) {
                    setcookie("innerTables[$key]", "", time()-86400, "/");
                }
                unset($elementPositions['update']);
                eF_updateTableData("users_to_lessons", array("positions" => serialize($elementPositions)), "lessons_ID=".$currentLesson -> lesson['id']." AND users_LOGIN='".$currentUser -> user['login']."'");
          $cacheKey = "user_lesson_status:lesson:".$currentLesson -> lesson['id']."user:".$currentUser -> user['login'];
          Cache::resetCache($cacheKey);
            }
        } else {
            $smarty -> assign("T_POSITIONS", array());
        }
        $controlPanelOptions = array();
        //Set control panel elemenets for administrator
        if ($_admin_) {
                if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
                    $controlPanelOptions[] = array('text' => _USERS, 'image' => "32x32/user.png", 'href' => "administrator.php?ctg=users");
                }
            if (!isset($currentUser -> coreAccess['lessons']) || $currentUser -> coreAccess['lessons'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _LESSONS, 'image' => "32x32/lessons.png", 'href' => "administrator.php?ctg=lessons");
                $controlPanelOptions[] = array('text' => _COURSES, 'image' => "32x32/courses.png", 'href' => "administrator.php?ctg=courses");
                $controlPanelOptions[] = array('text' => _DIRECTIONS, 'image' => "32x32/categories.png", 'href' => "administrator.php?ctg=directions");
            }
            if (!isset($currentUser -> coreAccess['user_types']) || $currentUser -> coreAccess['user_types'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _ROLES, 'image' => "32x32/user_types.png", 'href' => "administrator.php?ctg=user_types");
            }
            if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _GROUPS, 'image' => "32x32/users.png", 'href' => "administrator.php?ctg=user_groups");
            }
            if (!isset($currentUser -> coreAccess['configuration']) || $currentUser -> coreAccess['configuration'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _CONFIGURATIONVARIABLES, 'image' => "32x32/tools.png", 'href' => "administrator.php?ctg=system_config");
            }
            if (!isset($currentUser -> coreAccess['themes']) || $currentUser -> coreAccess['themes'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _THEMES, 'image' => "32x32/themes.png", 'href' => "administrator.php?ctg=themes&theme=".$GLOBALS['currentTheme'] -> {$currentTheme -> entity}['id']);
            }
            if (!isset($currentUser -> coreAccess['notifications']) || $currentUser -> coreAccess['notifications'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _EMAILDIGESTS, 'image' => "32x32/notifications.png", 'href' => "administrator.php?ctg=digests");
            }
            if ((!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') && $GLOBALS['configuration']['disable_messages'] != 1) {
                $controlPanelOptions[] = array('text' => _MESSAGES, 'image' => "32x32/mail.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=messages");
            }
            if (!isset($currentUser -> coreAccess['logout_user']) || $currentUser -> coreAccess['logout_user'] == 'view') {
                $controlPanelOptions[] = array('text' => _LOGOUTUSER, 'image' => "32x32/logout.png", 'href' => "administrator.php?ctg=logout_user&popup=1", 'onClick' => "eF_js_showDivPopup('"._LOGOUTUSER."', 0)", 'target' => 'POPUP_FRAME');
            }
            if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _EXPORTIMPORTDATA, 'image' => "32x32/import_export.png", 'href' => "administrator.php?ctg=import_export");
            }
            if (!isset($currentUser -> coreAccess['languages']) || $currentUser -> coreAccess['languages'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _LANGUAGES, 'image' => "32x32/languages.png", 'href' => "administrator.php?ctg=languages");
            }
            if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _STATISTICS, 'image' => "32x32/reports.png", 'href' => "administrator.php?ctg=statistics");
            }
            if (!isset($currentUser -> coreAccess['backup']) || $currentUser -> coreAccess['backup'] != 'hidden') {
                $controlPanelOptions['backup'] = array('text' => _BACKUP." - "._RESTORE, 'image' => "32x32/backup_restore.png", 'href' => "administrator.php?ctg=backup");
            }
            if (!isset($currentUser -> coreAccess['maintenance']) || $currentUser -> coreAccess['maintenance'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _MAINTENANCE, 'image' => "32x32/maintenance.png", 'href' => "administrator.php?ctg=maintenance");
            }
            if ((!isset($currentUser -> coreAccess['forum']) || $currentUser -> coreAccess['forum'] != 'hidden') && $GLOBALS['configuration']['disable_forum'] != 1) {
                $controlPanelOptions[] = array('text' => _FORUM, 'image' => "32x32/forum.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=forum");
            }
            if ($GLOBALS['configuration']['chat_enabled']) {
             if (!isset($currentUser -> coreAccess['chat']) || $currentUser -> coreAccess['chat'] != 'hidden') {
                 $controlPanelOptions[] = array('text' => _CHAT, 'image' => "32x32/chat.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=chat");
             }
            }
            if (!isset($currentUser -> coreAccess['modules']) || $currentUser -> coreAccess['modules'] != 'hidden') {
                $controlPanelOptions[] = array('text' => _MODULES, 'image' => "32x32/addons.png", 'href' => "administrator.php?ctg=modules");
            }
        }
        //Set control panel elements for professor
        else if ($_professor_) {
            $currentContent = new EfrontContentTree($currentLesson);
            if ($currentUser -> coreAccess['content'] != 'hidden') {
                $currentLesson -> options['lesson_info'] ? $controlPanelOptions[0] = array('text' => _LESSONINFORMATION, 'image' => "32x32/information.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=lesson_information") : null;
                if ($currentContent && $currentContent -> getFirstNode()){
                    $controlPanelOptions[1] = array('text' => _CONTENTMANAGEMENT, 'image' => "32x32/content.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=content&view_unit=".$currentContent -> getFirstNode() -> offsetGet('id'));
                } else {
                    $controlPanelOptions[1] = array('text' => _CONTENTMANAGEMENT, 'image' => "32x32/content.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=content");
                }
                if (!isset($currentUser -> coreAccess['content']) || $currentUser -> coreAccess['content'] == 'change') {
                    $controlPanelOptions[5] = array('text' => _CONTENTTREEMANAGEMENT, 'image' => "32x32/content_reorder.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=order");
                    $controlPanelOptions[7] = array('text' => _COPYFROMANOTHERLESSON, 'image' => "32x32/lesson_copy.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=copy");
                }
                ($currentLesson -> options['projects'] && $GLOBALS['configuration']['disable_projects'] != 1) ? $controlPanelOptions[2] = array('text' => _PROJECTS, 'image' => "32x32/projects.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=projects") : null;
                ($currentLesson -> options['tests'] && $GLOBALS['configuration']['disable_tests'] != 1) ? $controlPanelOptions[3] = array('text' => _TESTS, 'image' => "32x32/tests.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=tests") : null;
                $currentLesson -> options['rules'] ? $controlPanelOptions[10] = array('text' => _ACCESSRULES, 'image' => "32x32/rules.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=rules") : null;
    $currentLesson -> options['scorm'] ? $controlPanelOptions[18] = array('text' => _SCORM, 'image' => "32x32/scorm.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=scorm") : null;
                $currentLesson -> options ? $controlPanelOptions[22] = array('text' => _IMS, 'image' => "32x32/autocomplete.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=ims") : null;
            }
            if ($currentUser -> coreAccess['glossary'] != 'hidden' && $GLOBALS['configuration']['disable_glossary'] != 1) {
                $currentLesson -> options['glossary'] ? $controlPanelOptions[11] = array('text' => _GLOSSARY, 'image' => "32x32/glossary.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=glossary") : null;
            }
            if ($currentUser -> coreAccess['statistics'] != 'hidden') {
                $controlPanelOptions[14] = array('text' => _STATISTICS, 'image' => "32x32/reports.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=statistics");
            }
            if ($currentUser -> coreAccess['settings'] != 'hidden') {
                $controlPanelOptions[13] = array('text' => _SCHEDULING, 'image' => "32x32/schedule.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=scheduling");
            }
            if ($currentUser -> coreAccess['files'] != 'hidden') {
                $controlPanelOptions[17] = array('text' => _FILES, 'image' => "32x32/file_explorer.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=file_manager");
            }
            if ($currentUser -> coreAccess['settings'] != 'hidden') {
                $controlPanelOptions[20] = array('text' => _LESSONSETTINGS, 'image' => "32x32/tools.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=settings");
            }
   if ($currentUser -> coreAccess['feedback'] != 'hidden' && $GLOBALS['configuration']['disable_feedback'] != 1) {
                    $currentLesson -> options['feedback'] ? $controlPanelOptions[9] = array('text' => _FEEDBACK, 'image' => "32x32/surveys.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=feedback") : null;
                }
            if ($currentUser -> coreAccess['progress'] != 'hidden') {
                $controlPanelOptions[12] = array('text' => _USERSPROGRESS, 'image' => "32x32/status.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=progress");
            }
            if ($currentUser -> coreAccess['forum'] != 'hidden' && $GLOBALS['configuration']['disable_forum'] != 1) {
                $resultForum = eF_getTableData("f_forums","id","lessons_ID=".$_SESSION['s_lessons_ID']);
                $currentLesson -> options['forum'] ? $controlPanelOptions[19] = array('text' => _FORUM, 'image' => "32x32/forum.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=forum&forum=".$resultForum[0]['id']) : null;
            }
            if ((!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') && $GLOBALS['configuration']['disable_messages'] != 1) {
                $controlPanelOptions[6] = array('text' => _MESSAGES, 'image' => "32x32/mail.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=messages");
            }
            ksort($controlPanelOptions);
//pr(array_keys($controlPanelOptions));
        } else {
            $controlPanelOptions = $headerOptions;
            if ($currentUser -> coreAccess['glossary'] != 'hidden' && $GLOBALS['configuration']['disable_glossary'] != 1 && $currentLesson -> options['glossary']) {
                $option = array('text' => _GLOSSARY, 'image' => "32x32/glossary.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=glossary");
                $controlPanelOptions[] = $option;
                $headerOptions[] = $option;
            }
            if ($currentUser -> coreAccess['statistics'] != 'hidden' && $currentLesson -> options['reports']) {
                $option = array('text' => _STATISTICS, 'image' => "32x32/reports.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=statistics");
                $controlPanelOptions[] = $option;
                $headerOptions[] = $option;
            }
            if ($currentUser -> coreAccess['forum'] != 'hidden' && $GLOBALS['configuration']['disable_forum'] != 1 && $currentLesson -> options['forum']) {
                $resultForum = eF_getTableData("f_forums","id","lessons_ID=".$_SESSION['s_lessons_ID']);
                $option = array('text' => _FORUM, 'image' => "32x32/forum.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=forum&forum=".$resultForum[0]['id']);
                $controlPanelOptions[] = $option;
                $headerOptions[] = $option;
            }
            if ((!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') && $GLOBALS['configuration']['disable_messages'] != 1) {
                $option = array('text' => _MESSAGES, 'image' => "32x32/mail.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=messages");
                $controlPanelOptions[] = $option;
                $headerOptions[] = $option;
            }
            if ($currentLesson -> options['show_student_cpanel']) {
                $headerOptions = array();
            } else {
                $controlPanelOptions = array();
            }
        }
        ///Create control panel sidelinks and innertable
        $innertable_modules = array();
        foreach ($loadedModules as $module) {
            if ($_admin_ || isset($currentLesson -> options[$module -> className]) && $currentLesson -> options[$module -> className] == 1) {
                unset($InnertableHTML);
                if ($_admin_) {
                    $centerLinkInfo = $module -> getCenterLinkInfo();
                    $InnertableHTML = $module -> getControlPanelModule();
                    $InnertableHTML ? $module_smarty_file = $module -> getControlPanelSmartyTpl() : $module_smarty_file = false;
                } else {
                    $centerLinkInfo = $module -> getLessonCenterLinkInfo();
                    $InnertableHTML = $module -> getLessonModule();
                    $InnertableHTML ? $module_smarty_file = $module -> getLessonSmartyTpl() : $module_smarty_file = false;
                }
                if ($centerLinkInfo) {
                    $controlPanelOptions[] = array('text' => $centerLinkInfo['title'], 'image' => eF_getRelativeModuleImagePath($centerLinkInfo['image']), 'href' => $centerLinkInfo['link']);
                }
                // If the module has a lesson innertable
                if ($InnertableHTML) {
                    // Get module html - two ways: pure HTML or PHP+smarty
                    // If no smarty file is defined then false will be returned
                    if ($module_smarty_file) {
                        // Execute the php code -> The code has already been executed by above (**HERE**)
                        // Let smarty know to include the module smarty file
                        $innertable_modules[$module->className] = array('smarty_file' => $module_smarty_file);
                    } else {
                        // Present the pure HTML cod
                        $innertable_modules[$module->className] = array('html_code' => $InnertableHTML);
                    }
                }
            }
        }
        if (!empty($innertable_modules)) {
            $smarty -> assign("T_INNERTABLE_MODULES", $innertable_modules);
        }
        $smarty -> assign("T_CONTROL_PANEL_OPTIONS", $controlPanelOptions);
        $smarty -> assign("T_HEADER_OPTIONS", $headerOptions);
    }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = _SOMEPROBLEMOCCURED.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
