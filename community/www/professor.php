<?php
/**

* Professor main page

*

* This page performs all professor functions

* @package eFront

* @version 3.6.0

*/
session_cache_limiter('none'); //Initialize session
session_start();
$path = "../libraries/"; //Define default path
/** The configuration file.*/
require_once $path."configuration.php";
$benchmark = new EfrontBenchmark($debug_TimeStart);
$benchmark -> set('init');
//Set headers in order to eliminate browser cache (especially IE's)'
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
//If the page is shown as a popup, make sure it remains in such mode
if (!isset($_GET['reset_popup']) && (isset($_GET['popup']) || isset($_POST['popup']) || (isset($_SERVER['HTTP_REFERER']) && strpos(strtolower($_SERVER['HTTP_REFERER']), 'popup') !== false && strpos(strtolower($_SERVER['HTTP_REFERER']), 'reset_popup') === false))) {
    output_add_rewrite_var('popup', 1);
    $smarty -> assign("T_POPUP_MODE", true);
    $popup = 1;
}
$message = '';$message_type = ''; //Initialize messages, because if register_globals is turned on, some messages will be displayed twice
try {
 $currentUser = EfrontUser :: checkUserAccess(false, 'professor');
 if ($currentUser -> user['user_type'] == 'administrator') {
  throw new Exception(_ADMINISTRATORCANNOTACCESSLESSONPAGE, EfrontUserException :: RESTRICTED_USER_TYPE);
 }
 $smarty -> assign("T_CURRENT_USER", $currentUser);
} catch (Exception $e) {
 if ($e -> getCode() == EfrontUserException :: USER_NOT_LOGGED_IN) {
  setcookie('c_request', htmlspecialchars_decode(basename($_SERVER['REQUEST_URI'])), time() + 300);
 }
 eF_redirect("index.php?ctg=expired");
 exit;
}

if (!isset($_GET['ajax']) && !isset($_GET['postAjaxRequest']) && !isset($popup) && !isset($_GET['tabberajax'])) {
 $_SESSION['previousMainUrl'] = $_SERVER['REQUEST_URI'];
}

if (isset($_COOKIE['c_request']) && $_COOKIE['c_request']) {
 setcookie('c_request', '', time() - 86400);
    if (mb_strpos($_COOKIE['c_request'], '.php') !== false) {
     $urlParts = parse_url($_COOKIE['c_request']);
     if (basename($urlParts['path']) == 'professor.php') {
         eF_redirect($_COOKIE['c_request']);
     }
    } else {
        eF_redirect($_SESSION['s_type'].'.php?'.$_COOKIE['c_request']);
    }
}
$roles = EfrontLessonUser :: getLessonsRoles();

try {
 if (isset($_GET['view_unit']) || isset($_GET['package_ID'])) {
  if ($_GET['view_unit']) {
   $unit = new EfrontUnit($_GET['view_unit']);
  } elseif ($_GET['package_ID']) {
   $unit = new EfrontUnit($_GET['package_ID']);
  }
  $currentLesson = new EfrontLesson($unit['lessons_ID']);
  $_SESSION['s_lessons_ID'] = $currentLesson -> lesson['id'];
 }
} catch (Exception $e) {
 unset($_GET['view_unit']);
 $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
 $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
 $message_type = 'failure';
}

/* This is used to allow users to enter directly internal lesson specific pages from external pages*/
if (isset($_GET['new_lessons_ID']) && eF_checkParameter($_GET['new_lessons_ID'], 'id')) {
  if ($_GET['new_lessons_ID'] != $_SESSION['s_lessons_ID']) {
  $_SESSION['s_lessons_ID'] = $_GET['new_lessons_ID'];
  if (isset($_GET['sbctg'])) {
   $smarty -> assign("T_SPECIFIC_LESSON_CTG", $_GET['sbctg']);
  }
  $smarty -> assign("T_REFRESH_SIDE","true");
  } else if ($_GET['new_lessons_ID'] == $_SESSION['s_lessons_ID']) {

        $smarty -> assign("T_SHOW_LOADED_LESSON_OPTIONS", 1);
    }
}

/*This is the first time the professor enters this lesson, so register the lesson id to the session*/
if (isset($_GET['lessons_ID']) && eF_checkParameter($_GET['lessons_ID'], 'id')) {
    if (!isset($_SESSION['s_lessons_ID']) || $_GET['lessons_ID'] != $_SESSION['s_lessons_ID']) {
     unset($_SESSION['s_courses_ID']);
        $userLessons = $currentUser -> getLessons();
     if (isset($_GET['course']) || isset($_GET['from_course'])) {
            if ($_GET['course']) {
          $course = new EfrontCourse($_GET['course']);
            } else {
             $course = new EfrontCourse($_GET['from_course']);
            }
/*

            $eligibility = $course -> checkRules($_SESSION['s_login']);



            if ($eligibility[$_GET['lessons_ID']] == 0){

                unset($_GET['lessons_ID']);

                $message      = _YOUCANNOTACCESSTHISLESSONBECAUSEOFCOURSERULES;

                eF_redirect("student.php?ctg=lessons&message=".urlencode($message)."&message_type=failure");

            }

*/
            $_SESSION['s_courses_ID'] = $course -> course['id'];
        }
        if (in_array($_GET['lessons_ID'], array_keys($userLessons))) {
            $_SESSION['s_lessons_ID'] = $_GET['lessons_ID'];
            $_SESSION['s_type'] = $roles[$userLessons[$_GET['lessons_ID']]];
            $smarty -> assign("T_CHANGE_LESSON", "true");
            $smarty -> assign("T_REFRESH_SIDE", "true");
        } else {
            unset($_GET['lessons_ID']);
            $message = _YOUCANNOTACCESSTHISLESSONORITDOESNOTEXIST;
            $message_type = 'failure';
            $_GET['ctg'] = 'personal';
        }
    } else if ($_GET['lessons_ID'] == $_SESSION['s_lessons_ID']) {
        $smarty -> assign("T_SHOW_LOADED_LESSON_OPTIONS", 1);
    }
}
if (isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID'] && $_GET['ctg'] != 'lessons') { //Check validity of current lesson
    $userLessons = $currentUser -> getLessons();
    if ($_GET['ctg'] != 'personal' && (!isset($userLessons[$_SESSION['s_lessons_ID']]) || $roles[$userLessons[$_SESSION['s_lessons_ID']]] != 'professor')) {
        eF_redirect("student.php?ctg=lessons"); //redirect to student's lessons page
        exit;
    }
    try {
        $currentUser -> applyRoleOptions($userLessons[$_SESSION['s_lessons_ID']]); //Initialize user's role options for this lesson
        $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']); //Initialize lesson
  $_SESSION['s_lesson_user_type'] = $roles[$userLessons[$_SESSION['s_lessons_ID']]]; //needed for outputfilter.eF_template_setInnerLinks
        $smarty -> assign("T_TITLE_BAR", $currentLesson -> lesson['name']);
    } catch (Exception $e) {
        unset($_SESSION['s_lessons_ID']);
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        eF_redirect("".basename($_SERVER['PHP_SELF'])."?message=".urlencode($message)."&message_type=failure");
    }
}
//@todo: remove package_ID from $_SESSION, beware package_ID is needed in lms_commit
if (isset($_SESSION['package_ID']) && !$_GET['commit_lms']) {
    unset($_SESSION['package_ID']);
}
try {
    if (isset($_GET['view_unit']) && eF_checkParameter($_GET['view_unit'], 'id')) {
        $currentContent = new EfrontContentTree($currentLesson); //Initialize content
        if ($currentUser -> coreAccess['content'] == 'hidden') {
            eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
        if (!$currentLesson || !$currentContent) {
            eF_redirect("".basename($_SERVER['PHP_SELF']));
        }
        $currentUnit = $currentContent -> seekNode($_GET['view_unit']); //Initialize current unit
        //The content tree does not hold data, so assign this unit its data
        $unitData = new EfrontUnit($_GET['view_unit']);
        $currentUnit['data'] = $unitData['data'];
        if (!$_GET['ctg']) {
            $_GET['ctg'] = 'content';
        }
    } elseif (isset($_GET['package_ID']) && $currentContent) {
        $_GET['ctg'] = 'content';
    }
} catch (Exception $e) {
    unset($_GET['view_unit']);
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
/*Ajax call to enter group and get group lessons */
if (isset($_GET['ajax']) && isset($_GET['group_key'])) {
 try {
  if (!eF_checkParameter($_GET['group_key'], 'alnum_general')) {
   throw new Exception(_INVALIDDATA.': '.$_GET['group_key']);
  }
  $result = eF_getTableData("groups", "*", "unique_key = '" . $_GET['group_key'] . "'");
  $group = new EfrontGroup($result[0]);
  $group -> useKeyForUser($currentUser);
 } catch (Exception $e) {
  handleAjaxExceptions($e);
 }
 exit;
}
try {
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
  // Run onNewPageLoad code of the module (if such is defined)
  $module -> onNewPageLoad();
 }
} catch (Exception $e) {
 handleNormalFlowExceptions($e);
}
if (isset($_GET['bookmarks']) && $GLOBALS['configuration']['disable_bookmarks'] != 1) {
    try {
        $bookmarks = bookmarks :: getBookmarks($currentUser, $currentLesson);
        if ($_GET['bookmarks'] == 'remove' && in_array($_GET['id'], array_keys($bookmarks))) {
            $bookmark = new bookmarks($_GET['id']);
            $bookmark -> delete();
        } elseif ($_GET['bookmarks'] == 'add') {
            foreach ($bookmarks as $value) {
                $urls[] = $value['url'];
            }
            if (!in_array($_SERVER['PHP_SELF']."?view_unit=".$currentUnit['id'], $urls)) {
             $fields = array('users_LOGIN' => $currentUser -> user['login'],
                          'lessons_ID' => $currentLesson -> lesson['id'],
                          'name' => $currentUnit['name'],
                          'url' => $_SERVER['PHP_SELF']."?view_unit=".$currentUnit['id']);
             bookmarks :: create($fields);
            }
        } else {
            echo json_encode($bookmarks);
        }
    } catch (Exception $e) {
     handleAjaxExceptions($e);
    }
    exit;
}
/*Added Session variable for search results*/
$_SESSION['referer'] = $_SERVER['REQUEST_URI'];
/*Horizontal menus*/
$onlineUsers = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);
if ($GLOBALS['currentTheme'] -> options['sidebar_interface']) {
 $smarty -> assign("T_ONLINE_USERS_LIST", $onlineUsers);
 if ($accounts = unserialize($currentUser -> user['additional_accounts'])) {
  $result = eF_getTableData("users", "login, user_type", 'login in ("'.implode('","', array_values($accounts)).'")');
     $smarty -> assign("T_MAPPED_ACCOUNTS", $result);
 }
} else {
    $smarty -> assign("T_NO_HORIZONTAL_MENU", 1);
}
refreshLogin();//Important: It must be called AFTER EfrontUser :: getUsersOnline
!isset($_GET['ctg']) ? $ctg = "control_panel" : $ctg = $_GET['ctg'];
if (!$_SESSION['s_lessons_ID'] && ($ctg != 'personal' && $ctg != 'statistics') && ($ctg == 'control_panel' && $_GET['op'] != "search")) { //If there is not a lesson in the session, then the user just logged into the system. Redirect him to lessons page, except for the case he is viewing his personal information 2007/07/27 added search control. It was a problem when user had not choose a lesson.
    $ctg = 'lessons';
}
$smarty -> assign("T_CTG", $ctg); //As soon as we derive the current ctg, assign it to smarty.
$smarty -> assign("T_OP", isset($_GET['op']) ? $_GET['op'] : false);
//Create shorthands for user type, to avoid long variable names
$_student_ = $_professor_ = $_admin_ = 0;
if ($_SESSION['s_lesson_user_type'] == 'student' || (!isset($_SESSION['s_lesson_user_type']) && $_SESSION['s_type'] == 'student')) {
    $_student_ = 1;
} else if ($_SESSION['s_lesson_user_type'] == 'professor' || (!isset($_SESSION['s_lesson_user_type']) && $_SESSION['s_type'] == 'professor')) {
    $_professor_ = 1;
} else {
    $_admin_ = 1;
}
$smarty -> assign("_student_", $_student_);
$smarty -> assign("_professor_", $_professor_);
$smarty -> assign("_admin_", $_admin_);
try {
 if ($ctg == 'control_panel') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once ("control_panel.php");
 }
 elseif ($ctg == 'landing_page') {
     /***/
     require_once ("landing_page.php");
 }
 elseif ($ctg == 'content') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     if (isset($_GET['commit_lms'])) {
         /***/
         require_once("lms_commit.php");
         exit;
     } else {
      /***/
      require_once("common_content.php");
     }
 }
 elseif ($ctg == 'metadata') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("metadata.php");
 }
 elseif ($ctg == 'comments') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once ("comments.php");
 }
 else if ($ctg == 'facebook') {
     /***/
     require_once "module_facebook.php";
 }
 elseif ($ctg == 'copy') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("copy.php");
 }
 elseif ($ctg == 'order') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("order.php");
 }
 elseif ($ctg == 'scheduling') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("scheduling.php");
 }
 elseif ($ctg == 'projects') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /**The file that handles the projects*/
     require_once("projects.php");
 }
 elseif ($ctg == 'tests') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
  if ($GLOBALS['configuration']['disable_tests'] == 1) {
      eF_redirect("".basename($_SERVER['PHP_SELF']));
  }
     if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden') {
         eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
     }
  if ($configuration['math_content'] && $configuration['math_images']) {
   $loadScripts[] = 'ASCIIMath2Tex';
  } elseif ($configuration['math_content']) {
   $loadScripts[] = 'ASCIIMathML';
  }
     $loadScripts[] = 'scriptaculous/dragdrop';
     /**The tests module file*/
     require_once ('module_tests.php');
 }
 elseif ($ctg == 'feedback') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
  if ($GLOBALS['configuration']['disable_feedback'] == 1) {
      eF_redirect("".basename($_SERVER['PHP_SELF']));
  }
     if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden') {
         eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
     }
  if ($configuration['math_content'] && $configuration['math_images']) {
   $loadScripts[] = 'ASCIIMath2Tex';
  } elseif ($configuration['math_content']) {
   $loadScripts[] = 'ASCIIMathML';
  }
     $loadScripts[] = 'scriptaculous/dragdrop';
     /**The tests module file*/
     require_once ('module_tests.php');
 }
 elseif ($ctg == 'file_manager') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     if (isset($_GET['folder'])) {
         $basedir = G_CONTENTPATH . $_GET['folder']. "/";
         if (!is_dir($basedir)) {
             mkdir($basedir, 0755);
         }
     } else {
      if ($currentLesson) {
       $basedir = $currentLesson -> getDirectory();
      } else {
       eF_redirect(basename($_SERVER['PHP_SELF']));
      }
     }
     if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
         $options = array('lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 1);
     } else {
         $options = array('delete' => false, 'edit' => false, 'share' => false, 'upload' => false, 'create_folder' => false, 'zip' => false, 'lessons_ID' => $currentLesson -> lesson['id'], 'metadata' => 1);
     }
     if (isset($_GET['folder'])) {
         $url = basename($_SERVER['PHP_SELF']).'?ctg=file_manager&folder=' .$_GET['folder'];
     } else {
         $url = basename($_SERVER['PHP_SELF']).'?ctg=file_manager';
     }
     include "file_manager.php";
 }
 elseif ($ctg == 'rules') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("rules.php");
 }
 elseif ($ctg == 'statistics') {
     if ($currentUser -> coreAccess['statistics'] != 'hidden') {
         require_once "statistics.php";
     } else {
         eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    }
 }
 elseif ($ctg == 'module') {
     /***/
     require_once("module.php");
 }
 elseif ($ctg == 'survey') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     if ($currentUser -> coreAccess['surveys'] == 'hidden' && $GLOBALS['configuration']['disable_surveys'] != 1) {
         eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");exit;
     }
     /**This file handles surveys*/
     require_once "module_surveys.php";
 }
 elseif ($ctg == "social") {
     require_once "social.php";
 }
 elseif ($ctg == 'glossary') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("glossary.php");
 }
 elseif ($ctg == 'calendar') {
  if ($GLOBALS['configuration']['disable_calendar'] == 1) {
      eF_redirect("".basename($_SERVER['PHP_SELF']));
  }
     if ($currentUser -> coreAccess['calendar'] != 'hidden') {
         require_once "calendar.php";
     } else {
         eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
     }
 }
 elseif ($ctg == 'settings') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     if (isset($currentUser -> coreAccess['settings']) && $currentUser -> coreAccess['settings'] == 'hidden') {
         eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
     }
     $baseUrl = 'ctg=settings';
     $smarty -> assign("T_BASE_URL", $baseUrl);
     require_once "lesson_settings.php";
 }
 /*

	The personal page is used to display the professor's personal information

	and provides the means to edit this information

	*/
 elseif ($ctg == 'personal') {
     /**This part is used to display the user's personal information*/
     include "includes/personal.php";
 }
 /*

	At this point, we apply module functionality

	*/
 elseif (sizeof($modules) > 0 && in_array($ctg, array_keys($module_ctgs))) {
     $module_mandatory = eF_getTableData("modules", "mandatory", "name = '".$ctg."'");
     if ($module_mandatory[0]['mandatory'] != 'false' || isset($currentLesson -> options[$ctg])) {
         include(G_MODULESPATH.$ctg.'/module.php');
         $smarty -> assign("T_CTG_MODULE", $module_ctgs[$ctg]);
     }
 }
 elseif ($ctg == 'lessons') {
     /***/
     require_once("lessons_list.php");
 }
 elseif ($ctg == 'forum') {
     /***/
     require_once("forum.php");
 }
 elseif ($ctg == 'messages') {
     /***/
     require_once("messages.php");
 }
 elseif ($ctg == 'chat') {
     /***/
     require_once("includes/chat.php");
 }
 elseif ($ctg == 'import') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("import.php");
 }
 elseif ($ctg == 'scorm') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("scorm.php");
 }
 elseif ($ctg == 'ims') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("ims.php");
 }
 elseif ($ctg == 'lesson_information') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("lesson_information.php");
 }
 elseif ($ctg == 'news') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     include ("news.php");
 }
 elseif ($ctg == 'progress') {
  $_SESSION['s_lessons_ID'] OR eF_redirect(basename($_SERVER['PHP_SELF']));
     /***/
     require_once("progress.php");
 }
} catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
}
$smarty -> assign("T_HEADER_EDITOR", $load_editor); //Specify whether we need to load the editor
if (isset($_GET['refresh']) || isset($_GET['refresh_side'])) {
    $smarty -> assign("T_REFRESH_SIDE","true");
}
/*

 * Check if you should input the JS code to

 * trigger sending the next notificatoin emails

 * Since 3.6.0

 */
if (EfrontNotification::shouldSendNextNotifications()) {
 $smarty -> assign("T_TRIGGER_NEXT_NOTIFICATIONS_SEND", 1);
 $_SESSION['send_next_notifications_now'] = 0; // the msg that triggered the immediate send should be sent now
}
$smarty -> assign("T_MODULE_CSS", $module_css_array);
$smarty -> assign("T_MODULE_JS", $module_js_array);
foreach ($loadedModules as $module) {
    $loadScripts = array_merge($loadScripts, $module -> addScripts());
}
//Main scripts, such as prototype
$mainScripts = getMainScripts();
$smarty -> assign("T_HEADER_MAIN_SCRIPTS", implode(",", $mainScripts));
//Operation/file specific scripts
$loadScripts = array_diff($loadScripts, $mainScripts); //Clear out duplicates
$smarty -> assign("T_HEADER_LOAD_SCRIPTS", implode(",", array_unique($loadScripts))); //array_unique, so it doesn't send duplicate entries
$smartyClosingFiles = array();
foreach ($loadedModules as $module) {
 if ($smartyClosingFile = $module -> onPageFinishLoadingSmartyTpl()) {
  $smartyClosingFiles[] = $smartyClosingFile;
 }
}
$smarty -> assign("T_PAGE_FINISH_MODULES", $smartyClosingFiles);
$smarty -> assign("T_CURRENT_CTG", $ctg);
$smarty -> assign("T_MENUCTG", $ctg);
//$smarty -> assign("T_MENU", eF_getMenu());
$smarty -> assign("T_QUERIES", $numberOfQueries);
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_SEARCH_MESSAGE", $search_message);
$smarty -> assign("T_CURRENT_USER", $currentUser);
$smarty -> assign("T_CURRENT_LESSON", $currentLesson);
if (isset($currentLesson)) {
 $directions = new EfrontDirectionsTree();
 $paths = $directions -> toPathString();
 $categoryPath = $paths[$currentLesson->lesson["directions_ID"]];
 //$categoryPath = str_replace("&rarr", "&raquo", $categoryPath);
 $smarty -> assign("T_CURRENT_CATEGORY_PATH", $categoryPath);
 if ($currentLesson -> lesson['course_only'] == 1 && $_SESSION['s_courses_ID']) {
  $currentCourse = new EfrontCourse($_SESSION['s_courses_ID']);
  $smarty -> assign("T_CURRENT_COURSE_NAME", htmlspecialchars($currentCourse->course['name'], ENT_QUOTES));
  $smarty -> assign("T_CURRENT_COURSE_ID", $currentCourse->course['id']);
 }
}
if ((!isset($_GET['edit']) && $_GET['ctg'] == 'content') && !isset($_GET['edit_project']) && !isset($_GET['edit_question']) && !isset($_GET['edit_test'])) { // when updating a unit we must preserve the innerlink
 $smarty -> load_filter('output', 'eF_template_setInnerLinks');
}
$benchmark -> set('script');
$smarty -> display('professor.tpl');
$benchmark -> set('smarty');
$benchmark -> stop();
$output = $benchmark -> display();
if (G_DEBUG) {
 echo $output;
}
?>
