<?php
/**
 * Sidebar frame
 *
 * This page is used as the leftmost frame, which is the menu bar
 * @package eFront
 * @version 1.0
 */

//---------------------------------------------Initialization-------------------------------------------------
session_cache_limiter('none');
session_start();

$path = "../libraries/";

/** Configuration file.*/
require_once $path."configuration.php";
require_once $path."menu.class.php";

// Share the hcd value with smarty
$module_hcd_interface = MODULE_HCD_INTERFACE;
$smarty -> assign("T_MODULE_HCD_INTERFACE", $module_hcd_interface);

/*Check the user type. If the user is not valid, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
        $smarty -> assign("T_CURRENT_USER", $currentUser);

        if (MODULE_HCD_INTERFACE) {
            $currentUser -> aspects['hcd'] = EfrontEmployeeFactory :: factory($_SESSION['s_login']);
            $employee = $currentUser -> aspects['hcd'];
        }

        if ($_SESSION['s_lessons_ID'] && ($currentUser instanceof EfrontLessonUser)) {
            $userLessons = $currentUser -> getLessons();
            $currentUser -> applyRoleOptions($userLessons[$_SESSION['s_lessons_ID']]);                //Initialize user's role options for this lesson
            $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
        } else {
            $currentUser -> applyRoleOptions();                //Initialize user's role options for this lesson
        }

    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        header("location:index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    header("location:index.php?message=".urlencode(_RESOURCEREQUESTEDREQUIRESLOGIN)."&message_type=failure");
    exit;
}

if (strlen($configuration['css']) > 0 && is_file(G_CUSTOMCSSPATH.$configuration['css'])) {
    $smarty -> assign("T_CUSTOM_CSS", $configuration['css']);
}
//---------------------------------------------End of Initialization-------------------------------------------------


///MODULE1
$modules = $currentUser -> getModules();
// Include module languages
foreach ($modules as $module) {
    // The $setLanguage variable is defined in globals.php
    $mod_lang_file = $module -> getLanguageFile($setLanguage);
    if (is_file ($mod_lang_file)) {
        require_once $mod_lang_file;
    }
}

/***Check if the chat configuration exists - if not create it***/
$results = eF_getTableData("configuration", "value", "name ='chat_enabled'");
if (empty($results)) {
    eF_insertTableData("configuration", array("name" => "chat_enabled", "value" => 1));    
    $chat_enabled = 1;
} else {
    $chat_enabled = (int)$results[0]['value'];
}    

/***** TOP MENU WITH AVATAR AND NAME *****/
try {
    $avatar = new EfrontFile($currentUser -> user['avatar']);
    $smarty -> assign ("T_AVATAR", $currentUser -> user['avatar']);

    // Get current dimensions
    list($width, $height) = getimagesize($avatar['path']);
    if ($width > 200 || $height > 100) {
        // Get normalized dimensions
        list($newwidth, $newheight) = eF_getNormalizedDims($avatar['path'], 200, 100);

        // The template will check if they are defined and normalize the picture only if needed
        $smarty -> assign("T_NEWWIDTH", $newwidth);
        $smarty -> assign("T_NEWHEIGHT", $newheight);
    }

} catch (Exception $e) {
    $smarty -> assign ("T_AVATAR", G_AVATARSPATH."system_avatars/unknown_small.png");
}

if ($currentUser -> user['name'] != '') {
    $realname = substr($currentUser -> user['name'],0,1) .".&nbsp;" . $currentUser -> user[0]['surname'] . "<br>"; //get the initial letter
    $smarty -> assign("T_RESULT", $currentUser -> user);
}

$efront_type = "<b><i>" . $_SESSION['s_login'] . "</i></b><br>";
$roleNames = EfrontUser :: getRoles(true);
if ($_SESSION['s_type'] == 'administrator') {
    $efront_type .= "<b>" . _TYPEOFUSER . "</b>:<br>";
} else if ($_SESSION['s_type'] == 'student') {
    $efront_type .= "<b>" . _EDUCATIONALROLE . "</b>:<br>";
} else {
    $efront_type .= "<b>" . _EDUCATIONALROLE . "</b>:<br>";
}
if ($currentUser -> user['user_types_ID']) {
    $_SESSION['s_lessons_ID'] ? $efront_type .= $roleNames[$userLessons[$_SESSION['s_lessons_ID']]] : $efront_type .= $roleNames[$currentUser -> user['user_types_ID']];
} else {
    $efront_type .= EfrontUser :: $basicUserTypesTranslations[$_SESSION['s_type']];
}

$efront_type .= "<br>";

if (MODULE_HCD_INTERFACE && $employee -> getType() == _SUPERVISOR) {
    $efront_type .= "<b>" . _ORGANIZATIONALROLE . "</b>:<br>" . _SUPERVISOR . "<br>";
}

if (MODULE_HCD_INTERFACE && $currentUser -> getType() != "administrator") {
    $efront_type .= "<b>" . _JOBDESCRIPTIONS. "</b>:<br>";
    $jobs = $employee -> getJobs();
    foreach ($jobs as $job) {
        $efront_type .= $job['description']. " ". _ATBRANCH . " \"" . $job['name']."\"<br>";
    }

}
/***** FOR SEARCHING *****/
/**Search module is used to display the search field and perform the searches*/
include "module_search.php";


/***** MENU *****/
$newMenu = new EfrontMenu();
$active_menu = 1; // initialized here, might change later
// SYSTEM MENU - ADMINISTRATOR ONLY
if ($_SESSION['s_type'] == 'administrator') {
    $systemMenu = array();
    $systemMenu[0] = array("id" => "control_panel_a", "image" => "home", "link" => "administrator.php?ctg=control_panel", "title" => _CONTROLCENTER);
    if (!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden') {
        $systemMenu[1] = array("id" => "forum_a",         "image" => "messages", "link" => "forum/forum_index.php", "title" => _FORUM);
    }
    if (!isset($GLOBALS['currentUser'] -> coreAccess['configuration']) || $GLOBALS['currentUser'] -> coreAccess['configuration'] != 'hidden') {
        $systemMenu[2] = array("id" => "cms_a", "image" => "document_text", "link" => "administrator.php?ctg=cms", "title" => _CMS);
    }
    if (!isset($GLOBALS['currentUser'] -> coreAccess['chat']) || $GLOBALS['currentUser'] -> coreAccess['chat'] != 'hidden') {
        $systemMenu[3] = array("id" => "chat_a", "image" => "user1_message", "link" => "chat/chat_index.php", "title" => _CHAT);
    }
    if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
        $systemMenu[4] = array("id" => "statistics_system_a", "image" => "chart", "link" => "administrator.php?ctg=statistics&option=system", "title" => _SYSTEMSTATISTICS);
    }

    // Get system menu modules
    $moduleMenus = eF_getModuleMenu($modules, "system");
    foreach ($moduleMenus as $moduleMenu) {
        $systemMenu[] = $moduleMenu;
    }
    $newMenu -> insertMenuOption($systemMenu, false, _SYSTEM);

}

//pr($_GET);

// LESSON MENU
if (isset($_GET['new_lesson_id'])) {

    $_SESSION['s_lessons_ID'] = $_GET['new_lesson_id'];

    $lessonMenu   = eF_getMenu();
    $lessons      = eF_getTableData("users_to_lessons, lessons", "lessons.name","users_to_lessons.users_LOGIN='".$_SESSION['s_login']."' AND users_to_lessons.active=1 AND lessons.id=users_to_lessons.lessons_ID AND lessons.active=1 AND lessons.id = '".$_GET['new_lesson_id']."'");
    $lessonMenuId = $newMenu -> createMenu( array("title" => $lessons[0][name], "image" => "lback_lessons.png", "link" => "new_sidebar.php?sbctg=lessons"));          //onclick="top.mainframe.location='{$smarty.session.s_type}.php?ctg=lessons';"

    // Get current lesson menu modules

    $moduleMenus = eF_getModuleMenu($modules, "current_lesson");
    foreach ($moduleMenus as $moduleMenu) {
        $lessonMenu['lesson'][] = $moduleMenu;
    }
    $newMenu -> insertMenuOption($lessonMenu['lesson'], $lessonMenuId);


    // Insert blank option
    $newMenu -> insertMenuOptionAsRawHtml("<table height='8px'></table>", $lessonMenuId);
	$userType = eF_getTableData("users", "user_type", "login='".$_SESSION['s_login']."'");
	$_SESSION['s_type'] = $userType[0]['user_type'];
    $newMenu -> insertMenuOptionAsRawHtml("<a href=\"new_sidebar.php?sbctg=lessons&last_lessons_id=".$currentLesson -> lesson['id']."\" onclick=\"top.mainframe.location='".$userType[0]['user_type'].".php?ctg=lessons';\"><img style=\"border:0; float: left;\" src=\"images/16x16/back_lessons.png\" />"._CHANGELESSON."</a>", $lessonMenuId);

    if ($chat_enabled == 1 && $currentLesson ->options['chat'] == 1 && $currentUser -> coreAccess['chat'] != 'hidden') {
	    // Add the user to this chatroom - if somehow he is already in then the database will not allow a second copy
	    $currentLesson -> addChatroomUser($currentUser); 
    }       
	    
    $smarty -> assign("T_ACTIVE_ID","lesson_main");
} else {

    $_SESSION['s_lessons_ID'] = "";
    $lessonMenuId = $newMenu -> createMenu( array("title" => _LESSONS));
    if ($_SESSION['s_type'] == "administrator") {
        if (!isset($GLOBALS['currentUser'] -> coreAccess['lessons']) || $GLOBALS['currentUser'] -> coreAccess['lessons'] != 'hidden') {
            $newMenu -> insertMenuOption(array("id" => "lessons_a", "image" => "lessons", "link" => "administrator.php?ctg=lessons", "title" => _LESSONS), $lessonMenuId);
            $newMenu -> insertMenuOption(array("id" => "directions_a", "image" => "kdf",  "link" => "administrator.php?ctg=directions", "title" => _DIRECTIONS) , $lessonMenuId);
            $newMenu -> insertMenuOption(array("id" => "courses_a", "image" => "books", "link" => "administrator.php?ctg=courses", "title" => _COURSES), $lessonMenuId);
            if ($_SESSION['s_version_type'] == 'Educational' || $_SESSION['s_version_type'] == 'Enterprise') {                
                if (!isset($currentUser -> coreAccess['skillgaptests']) || $currentUser -> coreAccess['skillgaptests'] != 'hidden') {
                    $newMenu -> insertMenuOption(array("id" => "tests_a", "image" => "pda_write", "link" => "administrator.php?ctg=tests", "title" => _SKILLGAPTESTS), $lessonMenuId);
                }
            }    
        }
//        $newMenu -> insertMenuOption(array("id" => "search_courses_a", "image" => "book_open2", "link" => "administrator.php?ctg=search_courses", "title" => _SEARCHCOURSEUSERS), $lessonMenuId);
        if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
            $newMenu -> insertMenuOption(array("id" => "statistics_lesson_a", "image" => "chart", "link" => "administrator.php?ctg=statistics&option=lesson", "title" => _LESSONSTATISTICS),  $lessonMenuId);
            $newMenu -> insertMenuOption(array("id" => "statistics_test_a", "image" => "edit", "link" => "administrator.php?ctg=statistics&option=test", "title" => _TESTSTATISTICS),  $lessonMenuId);
        }

        // Get lessons menu modules
        $moduleMenus = eF_getModuleMenu($modules, "lessons");
        foreach ($moduleMenus as $moduleMenu) {
            $newMenu -> insertMenuOption($moduleMenu,  $lessonMenuId);
        }

        $smarty -> assign("T_ACTIVE_ID","control_panel");
    } else {
        
        // Remove users from previous lesson chat rooms - Any other previous lesson cleanup actions can take place here
        if ($chat_enabled == 1 && $currentUser -> coreAccess['chat'] != 'hidden') {
	        if (isset($_GET['last_lessons_id']) && $_GET['last_lessons_id'] > 0) {
		       $previousLesson = new EfrontLesson($_GET['last_lessons_id']);
		       $previousLesson -> removeChatroomUser($currentUser -> user ['login']);
		    }
        }    
	            
        $newMenu -> insertMenuOption(array("id" => "lessons_a", "image" => "lessons", "link" => $_SESSION['s_type'].".php?ctg=lessons", "title" => _MYLESSONS), $lessonMenuId);

        if ($currentUser -> getType() == "student") {
            $userSkillgapTests = $currentUser -> getSkillgapTests();
            foreach($userSkillgapTests as $skid => $skillGap) {
	            if ($skillGap['solved']) {
	                unset($userSkillgapTests[$skid]);
	            }
	        }            
            if (!empty($userSkillgapTests)) {            
                $newMenu -> insertMenuOption(array("id" => "tests_a", "image" => "pda_write", "link" => $_SESSION['s_type'].".php?ctg=lessons&op=tests", "title" => _SKILLGAPTESTS), $lessonMenuId);
            }
        }

        // Get lessons menu modules
        $moduleMenus = eF_getModuleMenu($modules, "lessons");
        foreach ($moduleMenus as $moduleMenu) {
            $newMenu -> insertMenuOption($moduleMenu,  $lessonMenuId);
        }


        $userLessons = eF_getTableData("users_to_lessons, lessons", "lessons.name","users_to_lessons.users_LOGIN='".$_SESSION['s_login']."' AND users_to_lessons.active=1 AND lessons.id=users_to_lessons.lessons_ID AND lessons.languages_NAME='".$_SESSION['s_language']."' AND lessons.active=1");
        if (empty($userLessons)) {
            if (MODULE_HCD_INTERFACE == 1) {
                if ($employee -> isSupervisor()) {
                    $active_menu = 3;
                } else {
                    $active_menu = 2;
                }
            } else {
                $active_menu = 2;
            }

        }
        $smarty -> assign("T_ACTIVE_ID","personal");

    }
}

// In case of reload, select the correct menu
if (isset($_GET['sbctg'])) {
    $smarty -> assign("T_ACTIVE_ID",$_GET['sbctg']);
    if ($_GET['sbctg'] == "personal") {
        if (MODULE_HCD_INTERFACE == 1) {
            if ($_SESSION['s_type'] == "administrator") {
                $active_menu = 5;
            } else if ($employee -> isSupervisor()) {
                $active_menu = 3;
            } else {
                $active_menu = 2;
            }
        } else {
            ($_SESSION['s_type'] == "administrator") ? $active_menu = 4: $active_menu = 2;
        }
	} 

}

$smarty -> assign ("T_ACTIVE_MENU", $active_menu);


// USERS MENU - ADMINISTRATOR ONLY
if ($_SESSION['s_type'] == 'administrator') {
    $usersMenu = array();
    if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
        $usersMenu[0] = array("id" => "users_a", "image" => "user1", "link" => "administrator.php?ctg=users", "title" => _USERS);
    }
    if (!isset($currentUser -> coreAccess['configuration']) || $currentUser -> coreAccess['configuration'] != 'hidden') {
        $usersMenu[1] = array("id" => "user_types_a",         "image" => "users_family", "link" => "administrator.php?ctg=user_types", "title" => _ROLES);
    }
    if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
        $usersMenu[2] = array("id" => "user_groups_a", "image" => "users3", "link" => "administrator.php?ctg=user_groups", "title" => _GROUPS);
    }
    if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
        $usersMenu[3] = array("id" => "statistics_user_a", "image" => "chart", "link" => "administrator.php?ctg=statistics&option=user", "title" => _USERSTATISTICS);
    }

    // Get users menu modules
    $moduleMenus = eF_getModuleMenu($modules, "users");
    foreach ($moduleMenus as $moduleMenu) {
        $usersMenu[] = $moduleMenu;
    }

    $newMenu-> insertMenuOption($usersMenu, false, _USERS);
}

// ORGANIZATION MENU
if (MODULE_HCD_INTERFACE == 1) {
    include_once $path."module_hcd_tools.php";
    if ($employee -> getType() != _EMPLOYEE) {
        $menuHCD = ef_getHcdMenu();

        if ($currentUser -> getType() == 'administrator') {
            $menuHCD[] = array("id" => "search_employee_a", "image" => "book_red", "link" => "administrator.php?ctg=module_hcd&op=reports", "title" => _SEARCHEMPLOYEE);
        }

        // Get hcd menu modules
        $moduleMenus = eF_getModuleMenu($modules, "organization");
        foreach ($moduleMenus as $moduleMenu) {
            $menuHCD[] = $moduleMenu;
        }

        $newMenu -> insertMenuOption ($menuHCD, false, _ORGANIZATION);
    }
}

// TOOLS MENU
$toolsMenuId = $newMenu -> createMenu( array("title" => _PERSONALOPTIONS));
//$newMenu -> insertMenuOption(array("id" => "forum_a", "image" => "messages", "link" => "forum/forum_index.php", "title" => _ALLFORUMS), $toolsMenuId);
if ($_SESSION['s_type'] == 'administrator') {
    $newMenu -> insertMenuOption(array("id" => "personal_a", "image" => "index", "link" => "administrator.php?ctg=users&edit_user=".$_SESSION['s_login'], "title" => _PERSONALDATA), $toolsMenuId);
    if ($currentUser -> coreAccess['calendar'] != 'hidden') {
        $newMenu -> insertMenuOption(array("id" => "calendar_a", "image" => "calendar", "link" => "administrator.php?ctg=calendar", "title" => _CALENDAR), $toolsMenuId);
    }
    if (MODULE_HCD_INTERFACE == 1) {
        $newMenu -> insertMenuOption(array("id" => "file_manager_a", "image" => "folder_view", "link" => "administrator.php?ctg=users&edit_user=".$_SESSION['s_login']."&tab=file_record", "title" => _PERSONALFILES), $toolsMenuId);
    }
} else {
    $newMenu -> insertMenuOption(array("id" => "personal_a", "image" => "index", "link" => $_SESSION['s_type'].".php?ctg=personal", "title" => _PERSONALDATA), $toolsMenuId);
    if ($currentUser -> coreAccess['calendar'] != 'hidden') {
        $newMenu -> insertMenuOption(array("id" => "calendar_a", "image" => "calendar", "link" => $_SESSION['s_type'].".php?ctg=calendar", "title" => _CALENDAR), $toolsMenuId);
    }
    if (MODULE_HCD_INTERFACE == 1) {
        $newMenu -> insertMenuOption(array("id" => "file_manager_a", "image" => "folder_view", "link" => $_SESSION['s_type'].".php?ctg=personal&tab=file_record", "title" => _PERSONALFILES), $toolsMenuId);
        if ($employee -> getType() == _EMPLOYEE) {
            $newMenu -> insertMenuOption(array("id" => "file_manager_a", "image" => "folder_view", "link" => $_SESSION['s_type'].".php?ctg=personal&tab=file_record", "title" => _PERSONALFILES), $toolsMenuId);
            $newMenu -> insertMenuOption(array('id' => 'chart_a',  'image' => "cubes", 'title' => _ORGANISATIONCHART,                       'target' => "mainframe",    'link' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=chart"), $toolsMenuId);

        }
    }
    if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
        $newMenu -> insertMenuOption(array("id" => "statistics_a", "image" => "chart", "link" => $_SESSION['s_type'].".php?ctg=statistics", "title" => _STATISTICS), $toolsMenuId);
    }
	
	if (!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden') {
		$newMenu -> insertMenuOption(array("id" => "forum_general_a",         "image" => "messages", "link" => "forum/forum_index.php", "title" => _FORUMS), $toolsMenuId);
    }
}
if (!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') {
    $newMenu -> insertMenuOption(array("id" => "messages_a", "image" => "mail2", "link" => "forum/messages_index.php", "title" => _MESSAGES), $toolsMenuId);
}
// Get tools menu modules
$moduleMenus = eF_getModuleMenu($modules, "tools");
foreach ($moduleMenus as $moduleMenu) {
    $newMenu -> insertMenuOption($moduleMenu,  $toolsMenuId);
}

// Insert raw html for messages handling
if (!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') {
    $newMenu -> insertMenuOptionAsRawHtml("<table width = '100%'><tr><td align = 'center' id = 'new_chat_message'></td></tr></table><table width = '100%'><tr><td align = 'center' id = 'new_private_message'></td></tr></table>", $toolsMenuId);
}
// MODULES MENU
$other_menus = array();
foreach ($modules as $key => $module) {
    $sidebarLinks = $module -> getSidebarLinkInfo();

    $sidebarLinks = $sidebarLinks["other"];

    $menuTitle = $sidebarLinks["menuTitle"];
    // Get the title set for this other menu
    // If this menu does not exist create it
    if ($menuTitle && !isset($other_menus["'".$menuTitle."'"])) {
        $other_menus["'".$menuTitle."'"] = array();
    }

    foreach ($sidebarLinks["links"] as $mod_link) {
            $other_menus["'".$menuTitle."'"][] = array("id" => $module -> className . (($mod_link['id'])? "_".$mod_link['id']:""),
                                                       "image" => eF_getRelativeModuleImagePath($mod_link['image']),
                                                       "link" => $mod_link['link'],
                                                       "title" => $mod_link['title'],
                                                       "moduleLink" => "1",
                                                       "eFrontExtensions" => $mod_link['eFrontExtensions']);
    }

}

// If more than 8 new menus exist, then all will be put under the same menu called MODULES
if (sizeof($other_menus) > 8) {
    $massModulesMenuId = $newMenu -> createMenu( array("title" => _MODULES));
    foreach ($other_menus as $other_module_menu) {
        $newMenu -> insertMenuOption($other_module_menu, $massModulesMenu);
    }
} else {
// Otherwise a new menu will be put for each of them
    foreach ($other_menus as $title => $other_module_menu) {
        $newMenu -> insertMenuOption($other_module_menu, false, substr($title,1,strlen($title)-2));
    }
}

/*
$chatMenuId = $newMenu -> createMenu( array("title" => _CHAT));
$newMenu -> insertMenuOptionAsRawHtml('             
					<table border = "0" width = "98%" style="height:100%;">
							<tr><td>'._CHATROOMS.'</td></tr>
							<tr><td valign = "top" style="height:100%;">
								<iframe name = "test" frameborder = "no" style="border: 1px solid #DDDDDD; " scrolling="auto" id="glu" width="100%" src = "chat/blank.php" />'._SORRYNEEDIFRAME.'</iframe>
								</td>
							</tr>
					</table>
', $chatMenuId);
*/
// ONLINE USERS MENU
if (isset($_SESSION['s_lessons_ID'])){
    try{
        $lesson_name = $currentLesson -> lesson['name'];
    }
    catch (Exception $e){
        $lesson_name = "";
    }
}
else{
    $lesson_name = "";
}

// CHAT MENU
$_SESSION['last_id'] = 0; // Each time the sidebar reloads you need to get the five last minuites
if ($chat_enabled == 1 && $currentUser -> coreAccess['chat'] != 'hidden') {
	$rooms  = eF_getTableData("chatrooms LEFT OUTER JOIN users_to_chatrooms ON users_to_chatrooms.chatrooms_ID = chatrooms.id", "chatrooms.id, chatrooms.name, count(users_to_chatrooms.users_LOGIN) as users", "chatrooms.active=1 group by id");
	$smarty -> assign("T_CHATROOMS", $rooms);
	// Set here the default chat - general if no lesson is selected, or the lesson's chat room instead
	if (isset($_GET['new_lesson_id'])) {
	    $smarty -> assign("T_CHATROOMS_ID", $currentLesson -> getChatroom());
	} else {
	    $current_room = eF_getTableData("users_to_chatrooms JOIN chatrooms ON chatrooms_ID = id", "chatrooms_ID, chatrooms.users_LOGIN", "users_to_chatrooms.users_LOGIN = '".$currentUser -> user['login']."'");
	    if (empty($current_room)) {        
	        $smarty -> assign("T_CHATROOMS_ID",0);
	    } else {
	        $smarty -> assign("T_CHATROOMS_ID",$current_room[0]['chatrooms_ID']);
	        if ($current_room[0]['users_LOGIN'] == $currentUser -> user['login']) {
	            $smarty -> assign("T_CHATROOM_OWNED",1);
	        }
	    }
	}
	$smarty -> assign("T_CHATENABLED", 1);
	if ($currentUser -> coreAccess['chat'] == 'view') {
	    $smarty -> assign("T_ONLY_VIEW_CHAT", 1);    
    }
} else {
   	$smarty -> assign("T_CHATENABLED", 0);
}

if ((($GLOBALS['currentLesson'] -> options['online']) && $GLOBALS['currentLesson'] -> options['online'] == 1) || $_SESSION['s_type']=='administrator' ){
    //$currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
    $onlineUsers = EfrontUser :: getUsersOnline();
    $size = sizeof($onlineUsers);
    if ($size) {
        $smarty -> assign("T_ONLINE_USERS_COUNT", $size);
    }
    $smarty -> assign("T_ONLINE_USERS_LIST", $onlineUsers);
}

//pr($newMenu);


$smarty -> assign("T_MENU",$newMenu -> menu);
// HACK to include the chat box... @todo: bring it here 
if ($chat_enabled == 1 && $currentUser -> coreAccess['chat'] != 'hidden') {
    $smarty -> assign("T_MENUCOUNT", $newMenu -> menuCount);
} else {
    if ($currentUser -> getType() != "administrator" && !isset($currentLesson)) {
        $smarty -> assign("T_MENUCOUNT", $newMenu -> menuCount-1);
    } else {
        $smarty -> assign("T_MENUCOUNT", $newMenu -> menuCount);
    }
            
}

if (!$currentUser -> coreAccess['personal_messages'] || $currentUser -> coreAccess['personal_messages'] != 'hidden') {
    $smarty -> assign("T_UNREAD_MESSAGES", eF_getUnreadMessagesNumber());
} else {
    $smarty -> assign("T_NO_MESSAGES", true);
}

$initwidth = eF_getTableData("configuration", "value", "name = 'sidebar_width'");
if (empty($initwidth)) {
    $sideframe_width = 175;
} else {
    $sideframe_width = $initwidth[0]['value'];
}


$smarty -> load_filter('output', 'eF_template_formatTimestamp');
$smarty -> load_filter('output', 'eF_template_formatLogins');

// We calculated the size of the input message bar as a linear function y=ax+b
// for experimental extreme values(sidebar width, textbox size)->(175,27) and (450,82)
// we got y=0.2x-8
 //echo (int)(0.2 * $sideframe_width - 8);
$smarty -> assign("T_CHATINPUT_SIZE", (int)(0.2 * $sideframe_width - 8)); 
$smarty -> assign("T_SIDEBARWIDTH", $sideframe_width);
$smarty -> assign("T_REALNAME", $realname);
$smarty -> assign("T_SB_CTG", $_GET['sbctg']);
$smarty -> assign("T_TYPE", $efront_type);
$smarty -> display('new_sidebar.tpl');


/*
$debug_timeAfterSmarty = microtime(true) - $debug_TimeStart;

$debug_TotalTime = microtime(true) - $debug_TimeStart;

    echo "
    <div onclick = 'this.style.display=\"none\"' style = 'position:absolute;top:0px;right:0px;background-color:lightblue;border:1px solid black' >
    <table>
        <tr><th colspan = '100%'>Benchmarking info (click to remove)</th></tr>
        <tr><td>Initialization time: </td><td>".round($debug_InitTime, 5)." sec</td></tr>
        <tr><td>Time up to smarty: </td><td>".round($debug_timeBeforeSmarty, 5)." sec</td></tr>
        <tr><td>Database time: </td><td>".round($databaseTime, 5)." sec</td></tr>
        <tr><td>Smarty overhead: </td><td>".round($debug_timeAfterSmarty - $debug_timeBeforeSmarty, 5)." sec</td></tr>
        <tr><td colspan = \"2\" class = \"horizontalSeparator\"></td></tr>
        <tr><td>Total execution time: </td><td>".round($debug_TotalTime, 5)." sec</td></tr>
        <tr><td>Execution time for this script is: </td><td>".round($debug_TotalTime - $debug_InitTime - ($debug_timeAfterSmarty - $debug_timeBeforeSmarty), 5)." sec</td></tr>
    </table>
    </div>";

*/

?>