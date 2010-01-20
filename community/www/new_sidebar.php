<?php
/**
 * Sidebar frame
 *
 * This page is used as the leftmost frame, which is the menu bar
 * @package eFront
 * @version 1.0
 */

//---------------------------------------------Initialization-------------------------------------------------
error_reporting(E_ERROR);
if (!$horizontal_inframe_version) {
 session_cache_limiter('none');
 session_start();
}

$path = "../libraries/";

/** Configuration file.*/
require_once $path."configuration.php";
require_once $path."menu.class.php";


/*Check the user type. If the user is not valid, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
        $smarty -> assign("T_CURRENT_USER", $currentUser);
        if ($_SESSION['s_lessons_ID'] && ($currentUser instanceof EfrontLessonUser)) {
            $userLessons = $currentUser -> getLessons();
            $currentUser -> applyRoleOptions($userLessons[$_SESSION['s_lessons_ID']]); //Initialize user's role options for this lesson
            $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
        } else {
            $currentUser -> applyRoleOptions(); //Initialize user's role options for this lesson
        }
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        eF_redirect("index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    eF_redirect("index.php?message=".urlencode(_RESOURCEREQUESTEDREQUIRESLOGIN)."&message_type=failure");
    exit;
}
if (!isset($horizontal_inframe_version) || !$horizontal_inframe_version) {
 if (!isset($_GET['ajax']) && !isset($_GET['postAjaxRequest'])) {
     $_SESSION['previousSideUrl'] = $_SERVER['REQUEST_URI'];
 }
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
if (!isset($horizontal_inframe_version) || !$horizontal_inframe_version) {
 /***** TOP MENU WITH AVATAR AND NAME *****/
 try {
  if (isset($_SESSION['facebook_details']['pic'])) {
   $avatar['path'] = $_SESSION['facebook_details']['pic'];
   $smarty -> assign("T_ABSOLUTE_AVATAR_PATH", 1);
   $smarty -> assign ("T_AVATAR", $_SESSION['facebook_details']['pic']);
  } else {
      $avatar = new EfrontFile($currentUser -> user['avatar']);
      $smarty -> assign ("T_AVATAR", $currentUser -> user['avatar']);
  }
     // Get current dimensions
     list($width, $height) = getimagesize($avatar['path']);
     if ($width > 200 || $height > 100) {
         // Get normalized dimensions
         list($newwidth, $newheight) = eF_getNormalizedDims($avatar['path'], 200, 100);
         // The template will check if they are defined and normalize the picture only if needed
         $width = $newwidth;
         $height = $newheight;
     }
 } catch (Exception $e) {
     $width = 64;
     $height = 64;
 }
 $smarty -> assign("T_NEWWIDTH", $width);
 $smarty -> assign("T_NEWHEIGHT", $height);
}
//pr($_SESSION);
if (isset($_SESSION['facebook_user'])) {
 //pr($_SESSION);
 //$facebook = new EfrontFacebook();
 //$fb_details = $facebook->api_client->fql_query("SELECT first_name, last_name, pic FROM user WHERE uid = " . $_SESSION['facebook_user']);
//	//unset($_SESSION['facebook_details']);
 //$realname = substr($_SESSION['facebook_details']['first_name'],0,1).".&nbsp;" . $_SESSION['facebook_details']['last_name']. "<br>"; //get the initial letter
 $smarty -> assign("T_FB_STATUS", $_SESSION['facebook_details']['status']['message']);
} else {
 if ($currentUser -> user['name'] != '') {
  //$realname = substr($currentUser -> user['name'],0,1) .".&nbsp;" . $currentUser -> user['surname']; //get the initial letter
     $smarty -> assign("T_RESULT", $currentUser -> user);
 }
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
/***** FOR SEARCHING *****/
/**Search module is used to display the search field and perform the searches*/
include "module_search.php";
/***** MENU - only for interfaces 0:vertical and 1: horizontal *****/
if (isset($GLOBALS['currentTheme'] -> options['sidebar_interface']) && $GLOBALS['currentTheme'] -> options['sidebar_interface'] < 2) {
 $newMenu = new EfrontMenu();
 $active_menu = 1; // initialized here, might change later
 // SYSTEM MENU - ADMINISTRATOR ONLY
 if ($_SESSION['s_type'] == 'administrator') {
     $systemMenu = array();
     $systemMenu[0] = array("id" => "control_panel_a", "image" => "home", "link" => "administrator.php?ctg=control_panel", "title" => _CONTROLCENTER);
     if (!$GLOBALS['configuration']['disable_forum'] && (!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden')) {
         $systemMenu[1] = array("id" => "forum_a", "image" => "message", "link" => "administrator.php?ctg=forum", "title" => _FORUM);
     }
/*
	    if (!isset($GLOBALS['currentUser'] -> coreAccess['configuration']) || $GLOBALS['currentUser'] -> coreAccess['configuration'] != 'hidden') {

	        $systemMenu[2] = array("id" => "cms_a", "image" => "unit", "link" => "administrator.php?ctg=themes&tab=external", "title" => _CMS);

	    }
*/
     if ($GLOBALS['configuration']['chat_enabled']) {
      if (!isset($GLOBALS['currentUser'] -> coreAccess['chat']) || $GLOBALS['currentUser'] -> coreAccess['chat'] != 'hidden') {
          $systemMenu[3] = array("id" => "chat_a", "image" => "chat", "link" => $_SESSION['s_type'].".php?ctg=chat", "title" => _CHAT);
      }
  }
     if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
         $systemMenu[4] = array("id" => "statistics_system_a", "image" => "reports", "link" => "administrator.php?ctg=statistics&option=system", "title" => _SYSTEMSTATISTICS);
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
 if (isset($_GET['new_lesson_id']) && $_GET['new_lesson_id']) {
     // This is a lesson specific menu
     $_SESSION['s_lessons_ID'] = $_GET['new_lesson_id'];
     if (!isset($currentLesson)) {
         $currentLesson = new EfrontLesson($_GET['new_lesson_id']);
     }
     $lessonMenu = eF_getMenu();
     $lessons = eF_getTableData("users_to_lessons ul, lessons l", "l.name","ul.users_LOGIN='".$_SESSION['s_login']."' AND ul.active=1 AND l.id=ul.lessons_ID AND l.active=1 AND l.id = '".$_GET['new_lesson_id']."'");
     $lessonMenuId = $newMenu -> createMenu( array("title" => $lessons[0][name], "image" => "go_back.png", "link" => "new_sidebar.php?sbctg=lessons")); //onclick="top.mainframe.location='{$smarty.session.s_type}.php?ctg=lessons';"
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
     if (!(isset($GLOBALS['configuration']['hide_sidebar_images']) && $GLOBALS['configuration']['hide_sidebar_images'] == 1)) {
         $newMenu -> insertMenuOptionAsRawHtml("<a href=\"javascript:void(0);\" onclick=\"top.mainframe.location='".$userType[0]['user_type'].".php?ctg=lessons';hideAllLessonSpecific();\"><img style=\"border:0; float: left;\" src=\"images/16x16/go_back.png\" />"._CHANGELESSON."</a>", $lessonMenuId);
     } else {
         $newMenu -> insertMenuOptionAsRawHtml("<a href=\"javascript:void(0);\" onclick=\"top.mainframe.location='".$userType[0]['user_type'].".php?ctg=lessons';hideAllLessonSpecific();\">"._CHANGELESSON."</a>", $lessonMenuId);
     }
     //$newMenu -> insertMenuOption(array("id" => "change_lesson_a", "image" => "back_lessons", "link" => "professor.php?ctg=lessons", "title" => _CHANGELESSON, "target" => "mainframe"), $lessonMenuId);
     if ($GLOBALS['configuration']['chat_enabled'] && $currentLesson ->options['chat'] == 1 && $currentUser -> coreAccess['chat'] != 'hidden') {
         // Add the user to this chatroom - if somehow he is already in then the database will not allow a second copy
         $currentLesson -> addChatroomUser($currentUser);
     }
     $smarty -> assign("T_ACTIVE_ID","lesson_main");
     $smarty -> assign("T_SPECIFIC_LESSON",1);
     // For the second hidden div
 // Remove users from previous lesson chat rooms - Any other previous lesson cleanup actions can take place here
         /*

	        if ($GLOBALS['configuration']['chat_enabled'] && $currentUser -> coreAccess['chat'] != 'hidden') {

	            if (isset($_GET['last_lessons_id']) && $_GET['last_lessons_id'] > 0) {

	               $previousLesson = new EfrontLesson($_GET['last_lessons_id']);

	               $previousLesson -> removeChatroomUser($currentUser -> user ['login']);

	            }

	        }

	          */
      // baltas: why was this commented out? is needed to be hidden behind lesson specific options so that change lesson does not trigger sidebar reloading 
         $newMenu -> insertMenuOption(array("id" => "lessons_a", "image" => "lessons", "link" => $_SESSION['s_type'].".php?ctg=lessons", "title" => _MYLESSONS), $lessonMenuId);
         // Get lessons menu modules
         $moduleMenus = eF_getModuleMenu($modules, "lessons");
         foreach ($moduleMenus as $moduleMenu) {
             $newMenu -> insertMenuOption($moduleMenu, $lessonMenuId);
         }
 //pr($newMenu);
 } else {
     $_SESSION['s_lessons_ID'] = "";
     $lessonMenuId = $newMenu -> createMenu( array("title" => _LESSONS));
     if ($_SESSION['s_type'] == "administrator") {
         if (!isset($GLOBALS['currentUser'] -> coreAccess['lessons']) || $GLOBALS['currentUser'] -> coreAccess['lessons'] != 'hidden') {
             $newMenu -> insertMenuOption(array("id" => "lessons_a", "image" => "lessons", "link" => "administrator.php?ctg=lessons", "title" => _LESSONS), $lessonMenuId);
             $newMenu -> insertMenuOption(array("id" => "directions_a", "image" => "categories", "link" => "administrator.php?ctg=directions", "title" => _DIRECTIONS) , $lessonMenuId);
             $newMenu -> insertMenuOption(array("id" => "courses_a", "image" => "courses", "link" => "administrator.php?ctg=courses", "title" => _COURSES), $lessonMenuId);
         }
 //        $newMenu -> insertMenuOption(array("id" => "search_courses_a", "image" => "book_open2", "link" => "administrator.php?ctg=search_courses", "title" => _SEARCHCOURSEUSERS), $lessonMenuId);
         if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
             $newMenu -> insertMenuOption(array("id" => "statistics_lesson_a", "image" => "reports", "link" => "administrator.php?ctg=statistics&option=lesson", "title" => _LESSONSTATISTICS), $lessonMenuId);
             $newMenu -> insertMenuOption(array("id" => "statistics_course_a", "image" => "reports", "link" => "administrator.php?ctg=statistics&option=course", "title" => _COURSESTATISTICS), $lessonMenuId);
         }
         // Get lessons menu modules
         $moduleMenus = eF_getModuleMenu($modules, "lessons");
         foreach ($moduleMenus as $moduleMenu) {
             $newMenu -> insertMenuOption($moduleMenu, $lessonMenuId);
         }
         $smarty -> assign("T_ACTIVE_ID","control_panel");
     } else {
         // Remove users from previous lesson chat rooms - Any other previous lesson cleanup actions can take place here
         if ($GLOBALS['configuration']['chat_enabled'] && (!isset($currentUser -> coreAccess['chat']) || $currentUser -> coreAccess['chat'] != 'hidden')) {
             if (isset($_GET['last_lessons_id']) && $_GET['last_lessons_id'] > 0) {
                $previousLesson = new EfrontLesson($_GET['last_lessons_id']);
                $previousLesson -> removeChatroomUser($currentUser -> user ['login']);
             }
         }
         $newMenu -> insertMenuOption(array("id" => "lessons_a", "image" => "lessons", "link" => $_SESSION['s_type'].".php?ctg=lessons", "title" => _MYLESSONS), $lessonMenuId);
         // Get lessons menu modules
         $moduleMenus = eF_getModuleMenu($modules, "lessons");
         foreach ($moduleMenus as $moduleMenu) {
             $newMenu -> insertMenuOption($moduleMenu, $lessonMenuId);
         }
         $userLessons = $currentUser -> getLessons();
         if (empty($userLessons)) {
                 $active_menu = 1;
         }
         $smarty -> assign("T_ACTIVE_ID","lessons");
     }
 }
 // USERS MENU - ADMINISTRATOR ONLY
 if ($_SESSION['s_type'] == 'administrator') {
     $usersMenu = array();
     if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
         $usersMenu[0] = array("id" => "users_a", "image" => "user", "link" => "administrator.php?ctg=users", "title" => _USERS);
     }
     if (!isset($currentUser -> coreAccess['configuration']) || $currentUser -> coreAccess['configuration'] != 'hidden') {
         $usersMenu[1] = array("id" => "user_types_a", "image" => "user_types", "link" => "administrator.php?ctg=user_types", "title" => _ROLES);
     }
     if (!isset($currentUser -> coreAccess['users']) || $currentUser -> coreAccess['users'] != 'hidden') {
         $usersMenu[2] = array("id" => "user_groups_a", "image" => "users", "link" => "administrator.php?ctg=user_groups", "title" => _GROUPS);
         if (G_VERSIONTYPE == 'educational') {
             $usersMenu[3] = array("id" => "search_employee_a", "image" => "search", "link" => "administrator.php?ctg=search_courses", "title" => _SEARCHUSER);
         }
     }
     if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
         $usersMenu[4] = array("id" => "statistics_user_a", "image" => "reports", "link" => "administrator.php?ctg=statistics&option=user", "title" => _USERSTATISTICS);
     }
     // Get users menu modules
     $moduleMenus = eF_getModuleMenu($modules, "users");
     foreach ($moduleMenus as $moduleMenu) {
         $usersMenu[] = $moduleMenu;
     }
     $newMenu-> insertMenuOption($usersMenu, false, _USERS);
 }
 // ORGANIZATION MENU
 // TOOLS MENU
 $toolsMenuId = $newMenu -> createMenu( array("title" => _PERSONALOPTIONS));
 //$newMenu -> insertMenuOption(array("id" => "forum_a", "image" => "messages", "link" => basename($_SERVER['PHP_SELF'])."?ctg=forum", "title" => _ALLFORUMS), $toolsMenuId);
 if ($_SESSION['s_type'] == 'administrator') {
     $newMenu -> insertMenuOption(array("id" => "personal_a", "image" => "user", "link" => "administrator.php?ctg=users&edit_user=".$_SESSION['s_login'], "title" => _PERSONALDATA), $toolsMenuId);
     if ($GLOBALS['configuration']['disable_calendar'] != 1 && (!isset($currentUser -> coreAccess['calendar']) || $currentUser -> coreAccess['calendar'] != 'hidden')) {
         $newMenu -> insertMenuOption(array("id" => "calendar_a", "image" => "calendar", "link" => "administrator.php?ctg=calendar", "title" => _CALENDAR), $toolsMenuId);
     }
 } else {
     $newMenu -> insertMenuOption(array("id" => "personal_a", "image" => "user", "link" => $_SESSION['s_type'].".php?ctg=personal", "title" => _PERSONALDATA), $toolsMenuId);
     if ($GLOBALS['configuration']['disable_calendar'] != 1 && (!isset($currentUser -> coreAccess['calendar']) || $currentUser -> coreAccess['calendar'] != 'hidden')) {
         $newMenu -> insertMenuOption(array("id" => "calendar_a", "image" => "calendar", "link" => $_SESSION['s_type'].".php?ctg=calendar", "title" => _CALENDAR), $toolsMenuId);
     }
     if (!isset($currentUser -> coreAccess['statistics']) || $currentUser -> coreAccess['statistics'] != 'hidden') {
         $newMenu -> insertMenuOption(array("id" => "statistics_a", "image" => "reports", "link" => $_SESSION['s_type'].".php?ctg=statistics", "title" => _STATISTICS), $toolsMenuId);
     }
     if (!$GLOBALS['configuration']['disable_forum'] && (!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden')) {
         $newMenu -> insertMenuOption(array("id" => "forum_general_a", "image" => "message", "link" => $_SESSION['s_type'].".php?ctg=forum", "title" => _FORUMS), $toolsMenuId);
     }
 }
 if (!$GLOBALS['configuration']['disable_messages'] && (!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden')) {
     $newMenu -> insertMenuOption(array("id" => "messages_a", "image" => "mail", "link" => $_SESSION['s_type'].".php?ctg=messages", "title" => _MESSAGES), $toolsMenuId);
 }
 // Get tools menu modules
 $moduleMenus = eF_getModuleMenu($modules, "tools");
 foreach ($moduleMenus as $moduleMenu) {
     $newMenu -> insertMenuOption($moduleMenu, $toolsMenuId);
 }
 // Insert raw html for messages handling
 //if (!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') {
 //   $newMenu -> insertMenuOptionAsRawHtml("<table width = '100%'><tr><td align = 'center' id = 'new_chat_message'></td></tr></table><table width = '100%'><tr><td align = 'center' id = 'new_private_message'></td></tr></table>", $toolsMenuId);
 //}
 // MODULES MENU
 $other_menus = array();
 foreach ($modules as $key => $module) {
     $sidebarLinks = $module -> getSidebarLinkInfo();
     isset($sidebarLinks["other"]) ? $sidebarLinks = $sidebarLinks["other"] : $sidebarLinks = array();
     isset($sidebarLinks["menuTitle"]) ? $menuTitle = $sidebarLinks["menuTitle"] : $menuTitle = '';
     // Get the title set for this other menu
     // If this menu does not exist create it
     if ($menuTitle && !isset($other_menus["'".$menuTitle."'"])) {
         $other_menus["'".$menuTitle."'"] = array();
     }
     if (isset($sidebarLinks["links"])) {
      foreach ($sidebarLinks["links"] as $mod_link) {
             $other_menus["'".$menuTitle."'"][] = array("id" => $module -> className . (($mod_link['id'])? "_".$mod_link['id']:""),
                                                        "image" => eF_getRelativeModuleImagePath($mod_link['image']),
                                                        "link" => $mod_link['link'],
                                                        "title" => $mod_link['title'],
                                                        "moduleLink" => "1",
                                                        "eFrontExtensions" => $mod_link['eFrontExtensions'],
                          "target" => isset($mod_link['target'])?$mod_link['target']:"mainframe");
      }
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
 // ONLINE USERS MENU
 if (isset($_SESSION['s_lessons_ID']) && isset($currentLesson)) {
     try{
         $lesson_name = $currentLesson -> lesson['name'];
     } catch (Exception $e){
         $lesson_name = "";
     }
 } else {
     $lesson_name = "";
 }
 // In case of reload, select the correct menu
 if (isset($_GET['sbctg'])) {
     $smarty -> assign("T_ACTIVE_ID",$_GET['sbctg']);
     /*

	    if ($_GET['sbctg'] == "personal") {

#ifdef ENTERPRISE

	            if ($_SESSION['s_type'] == "administrator") {

	                $active_menu = 5;

	            } else if ($employee -> isSupervisor()) {

	                $active_menu = 3;

	            } else {

	                $active_menu = 2;

	            }

#else

	            ($_SESSION['s_type'] == "administrator") ? $active_menu = 4: $active_menu = 2;

#endif

	    } else {

	

	

	    }

	      */
     $active_menu = $newMenu -> getCategoryMenu($_GET['sbctg']);
 //    echo $active_menu;
 }
 $smarty -> assign ("T_ACTIVE_MENU", $active_menu);
 // CHAT MENU
 $_SESSION['last_id'] = 0; // Each time the sidebar reloads you need to get the five last minuites	
 if ($GLOBALS['configuration']['chat_enabled'] && (!isset($currentUser -> coreAccess['chat']) || $currentUser -> coreAccess['chat'] != 'hidden')) {
     $rooms = eF_getTableData("chatrooms c LEFT OUTER JOIN users_to_chatrooms uc ON uc.chatrooms_ID = c.id", "c.id, c.name, count(uc.users_LOGIN) as users", "c.active=1 group by id");
     $smarty -> assign("T_CHATROOMS", $rooms);
     // Set here the default chat - general if no lesson is selected, or the lesson's chat room instead
     if (isset($_GET['new_lesson_id']) && $_GET['new_lesson_id']) {
         $smarty -> assign("T_CHATROOMS_ID", $currentLesson -> getChatroom());
     } else {
         $current_room = eF_getTableData("users_to_chatrooms uc JOIN chatrooms c ON chatrooms_ID = id", "chatrooms_ID, c.users_LOGIN", "uc.users_LOGIN = '".$currentUser -> user['login']."'");
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
     if (isset($currentUser -> coreAccess['chat']) && $currentUser -> coreAccess['chat'] == 'view') {
         $smarty -> assign("T_ONLY_VIEW_CHAT", 1);
     }
     if ($GLOBALS['configuration']['disable_messages'] == 1) {
      $smarty -> assign ("T_INVITE_DISABLED", 1);
     }
 } else {
     $smarty -> assign("T_CHATENABLED", 0);
 }
 //pr($newMenu);
 $smarty -> assign("T_MENU",$newMenu -> menu);
 // HACK to include the chat box... @todo: bring it here
 if ($GLOBALS['configuration']['chat_enabled'] && (!isset($currentUser -> coreAccess['chat']) || $currentUser -> coreAccess['chat'] != 'hidden')) {
     $smarty -> assign("T_MENUCOUNT", $newMenu -> menuCount);
 } else {
     if ($currentUser -> getType() != "administrator" && !isset($currentLesson)) {
         $smarty -> assign("T_MENUCOUNT", $newMenu -> menuCount-1);
     } else {
         $smarty -> assign("T_MENUCOUNT", $newMenu -> menuCount);
     }
 }
}
if ((isset($GLOBALS['currentTheme'] -> options['sidebar_interface']) && $GLOBALS['currentTheme'] -> options['sidebar_interface'] < 2) ||
 ($GLOBALS['currentTheme'] -> options['sidebar_interface'] == 2 && $GLOBALS['currentTheme'] -> options['show_header'] == 2)) {
 if (((isset($GLOBALS['currentLesson']) && $GLOBALS['currentLesson'] -> options['online']) && $GLOBALS['currentLesson'] -> options['online'] == 1) || $_SESSION['s_type'] == 'administrator' ){
     //$currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
     $onlineUsers = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);
     if (!$_SESSION['s_login']) {
         eF_redirect("index.php?message=".rawurlencode(_INACTIVITYLOGOUT));
     }
     $size = sizeof($onlineUsers);
     if ($size) {
         $smarty -> assign("T_ONLINE_USERS_COUNT", $size);
     }
     $smarty -> assign("T_ONLINE_USERS_LIST", $onlineUsers);
 }
}
if (!isset($horizontal_inframe_version) || !$horizontal_inframe_version) {
 if (!isset($currentUser -> coreAccess['personal_messages']) || $currentUser -> coreAccess['personal_messages'] != 'hidden') {
     $unreadMessages = $messages = eF_getTableData("f_personal_messages pm, f_folders ff", "count(*)", "pm.users_LOGIN='".$_SESSION['s_login']."' and viewed='no' and f_folders_ID=ff.id and ff.name='Incoming'");
     $smarty -> assign("T_UNREAD_MESSAGES", $messages[0]['count(*)']);
 } else {
     $smarty -> assign("T_NO_MESSAGES", true);
 }
 $initwidth = eF_getTableData("configuration", "value", "name = 'sidebar_width'");
 if (empty($initwidth)) {
     $sideframe_width = 175;
 } else {
     $sideframe_width = $initwidth[0]['value'];
 }
}
if ($GLOBALS['configuration']['social_modules_activated'] & FB_FUNC_CONNECT) {
 $smarty -> assign("T_FACEBOOK_API_KEY", $GLOBALS['configuration']['facebook_api_key']);
 $smarty -> assign("T_OPEN_FACEBOOK_SESSION", "1");
 if (!isset($_SESSION['facebook_user'])) {
  $smarty -> assign("T_PROMPT_FB_CONNECTION", 1);
 }
}
if (unserialize($currentUser -> user['additional_accounts'])) {
 $accounts = unserialize($currentUser -> user['additional_accounts']);
 $queryString = "'".implode("','", array_values($accounts))."'";
 $result = eF_getTableData("users", "login, user_type", "login in (".$queryString.")");
    $smarty -> assign("T_BAR_ADDITIONAL_ACCOUNTS", $result);
}
$smarty -> load_filter('output', 'eF_template_formatTimestamp');
$smarty -> load_filter('output', 'eF_template_formatLogins');
$loadScripts[] = 'EfrontScripts';
$loadScripts[] = 'print-script';
$loadScripts[] = 'scriptaculous/prototype';
$loadScripts[] = 'scriptaculous/effects';
$loadScripts[] = 'efront_ajax';
$loadScripts[] = 'sidebar';
//array('EfrontScripts', 'print-script', 'scriptaculous/prototype', 'scriptaculous/effects', 'sidebar');
$smarty -> assign("T_HEADER_LOAD_SCRIPTS", implode(",", array_unique($loadScripts))); //array_unique, so it doesn't send duplicate entries
if (preg_match("/compatible; MSIE 6/", $_SERVER['HTTP_USER_AGENT']) && !preg_match("/compatible; MSIE 7/", $_SERVER['HTTP_USER_AGENT'])) {
    $smarty -> assign("globalImageExtension", "gif");
} else {
    $smarty -> assign("globalImageExtension", "png");
}
if (!(isset($GLOBALS['currentTheme'] -> options['images_displaying']) && $GLOBALS['currentTheme'] -> options['images_displaying'] != 0)) {
    $smarty -> assign ("T_SHOW_SIDEBAR_IMAGES", 1);
}
/**** FOR USER STATUS ****/
if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_USERSTATUS) {
    $smarty -> assign("T_SHOW_USER_STATUS",1);
}
// We calculated the size of the input message bar as a linear function y=ax+b
// for experimental extreme values(sidebar width, textbox size)->(175,27) and (450,82)
// we got y=0.2x-8
 //echo (int)(0.2 * $sideframe_width - 8);
$smarty -> assign("T_CHATINPUT_SIZE", (int)(0.2 * $sideframe_width - 8));
$smarty -> assign("T_SIDEBARWIDTH", $sideframe_width);
//$smarty -> assign("T_REALNAME", $realname);
$smarty -> assign("T_SB_CTG", isset($_GET['sbctg']) ? $_GET['sbctg'] : false);
$smarty -> assign("T_TYPE", $efront_type);
if (!isset($horizontal_inframe_version) || !$horizontal_inframe_version) {
 $smarty -> assign("T_NO_HORIZONTAL_MENU", 1);
 $smarty -> display('new_sidebar.tpl');
}
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
