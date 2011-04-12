<?php
/**

* eFront social

*

* This page is used for the functionalities of the eFront social infrastructure

* @package eFront

* @version 3.6.0

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}
 $loadScripts[] = 'includes/social';
 $loadScripts[] = 'scriptaculous/dragdrop';
 if ($GLOBALS['configuration']['social_modules_activated'] > 0) {
  $smarty -> assign("T_SOCIAL_INTERFACE", 1);
 }
 if ($_GET['op'] == "dashboard") {
  if (isset($_GET['ajax']) && isset($_GET['postAjaxRequest']) && isset($_GET['setStatus'])) {
   try {
    $editedUser -> setStatus($_GET['setStatus']);
    exit;
   } catch (Exception $e) {
    handleAjaxExceptions($e);
   }
  }
  $smarty -> assign("T_USER_STATUS", $editedUser -> user['status']);
  if ($currentUser -> coreAccess['dashboard'] == 'hidden') {
   eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=personal");
  }
  //Calculate element positions, so they can be rearreanged accordingly to the user selection
  //$elementPositions = eF_getTableData("users_to_lessons", "positions", "lessons_ID=".$currentLesson -> lesson['id']." AND users_LOGIN='".$currentUser -> user['login']."'");
  $elementPositions = $currentUser -> user['dashboard_positions'];
  if (sizeof($elementPositions) > 0) {
   $elementPositions = unserialize($elementPositions); //Get the inner tables positions, stored by the user.
   !is_array($elementPositions['first']) ? $elementPositions['first'] = array() : null;
   !is_array($elementPositions['second']) ? $elementPositions['second'] = array() : null;
   $smarty -> assign("T_POSITIONS_FIRST", $elementPositions['first']); //Assign element positions to smarty
   $smarty -> assign("T_POSITIONS_SECOND", $elementPositions['second']);
   $smarty -> assign("T_POSITIONS_VISIBILITY", $elementPositions['visibility']);
   $smarty -> assign("T_POSITIONS", array_merge($elementPositions['first'], $elementPositions['second']));
  } else {
   $smarty -> assign("T_POSITIONS", array());
  }
  // Get *eligible* lessons of interest to this user if he is not administrator
  if ($currentUser -> getType() != "administrator" ) {
   $eligibleLessons = $currentUser -> getEligibleLessons();
   $lessons_list = array_keys($eligibleLessons);
  }

  /*Projects list - Users get only projects for their lessons while administrators none*/
  if ($GLOBALS['configuration']['disable_projects'] != 1 && !empty($lessons_list)) {
   if ($currentUser -> getType() == "student") {
    // See projects assigned to you
    $not_expired_projects = eF_getTableData("projects p, users_to_projects up, lessons", "p.*, up.grade, up.comments, up.filename, lessons.name as show_lessons_name, lessons.id as show_lessons_id", "up.users_LOGIN = '$login' AND up.projects_ID = p.id AND p.lessons_ID = lessons.id AND lessons.id IN ('" . implode("','", $lessons_list). "') AND p.deadline > ".time()." ORDER BY p.deadline ASC LIMIT 5");
   } else {
    // See projects related to your lessons
    $not_expired_projects = eF_getTableData("projects p, lessons", "p.*, lessons.name as show_lessons_name, lessons.id as show_lessons_id", "p.lessons_ID = lessons.id AND lessons.id IN ('" . implode("','", $lessons_list). "') AND p.deadline > ".time()." ORDER BY p.deadline ASC LIMIT 5");
   }

   if (!empty($not_expired_projects)) {
    $smarty -> assign("T_ALL_PROJECTS", $not_expired_projects);
   }

  }

  /*Forum messages list*/
  // Users see forum messages from the system forum and their own lessons while administrators for all
  if (!empty($lessons_list)) {
   $forum_messages = eF_getTableData("f_messages fm JOIN f_topics ft JOIN f_forums ff LEFT OUTER JOIN lessons l ON ff.lessons_ID = l.id", "fm.title, fm.id, ft.id as topic_id, fm.users_LOGIN, fm.timestamp, l.name as show_lessons_name, lessons_id as show_lessons_id", "ft.f_forums_ID=ff.id AND fm.f_topics_ID=ft.id AND ff.lessons_ID IN ('0', '".implode("','", $lessons_list)."')", "fm.timestamp desc LIMIT 5");
  } elseif ($_SESSION['s_type'] == 'administrator') { //This is the admin speaking
   $forum_messages = eF_getTableData("f_messages fm JOIN f_topics ft JOIN f_forums ff LEFT OUTER JOIN lessons l ON ff.lessons_ID = l.id", "fm.title, fm.id, ft.id as topic_id, fm.users_LOGIN, fm.timestamp, l.name as show_lessons_name, lessons_id as show_lessons_id", "ft.f_forums_ID=ff.id AND fm.f_topics_ID=ft.id", "fm.timestamp desc LIMIT 5");
  }
  $smarty -> assign("T_FORUM_MESSAGES", $forum_messages); //Assign forum messages and categoru information to smarty

  if (isset($forum_lessons_ID[0]['id'])) { //If there is a forum category associated to this lesson (and the user is eligible to use it), display corresponding links
   $smarty -> assign("T_FORUM_LESSONS_ID", $forum_lessons_ID[0]['id']);
   $smarty -> assign("T_FORUM_LINK", basename($_SERVER['PHP_SELF'])."?ctg=forum&forum=".$forum_lessons_ID[0]['id']);
   $forum_options = array(
     array('text' => _GOTOFORUM, 'image' => "16x16/go_into.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=forum"),
     array('text' => _SENDMESSAGEATFORUM, 'image' => "16x16/add.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=forum&add=1&type=topic&category=".$forum_lessons_ID[0]['id'], 'onClick' => "eF_js_showDivPopup('"._NEWMESSAGE."', new Array('650px', '450px'));", 'target' => 'POPUP_FRAME')
     );
  } else { //If there isn't a forum caegory associated to this lesson, only display a link to forum
   $smarty -> assign("T_FORUM_LINK", basename($_SERVER['PHP_SELF'])."?ctg=forum");
   $forum_options = array(
     array('text' => _GOTOFORUM, 'image' => "16x16/go_into.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=forum")
     );
  }
  $smarty -> assign("T_FORUM_OPTIONS", $forum_options); //Assign forum options to smarty

  /*Lesson announcements list*/
  if (!isset($currentUser -> coreAccess['news']) || $currentUser -> coreAccess['news'] != 'hidden') {
   if (!empty($lessons_list)) {
    if ($currentUser -> getType() == "student") {
     //See only non-expired news
     $news = news :: getNews(0, true) + news :: getNews($lessons_list, true);
    } else {
     //See both expired and non-expired news
     $news = news :: getNews(0, true) + news :: getNews($lessons_list, false);
    }
   } else {
    //Administrator news, he doesn't have to see lesson news (since he can't actually access them)
    $news = news :: getNews(0, true);
   }
   $news = array_slice($news, 0, 10, true);
   $smarty -> assign("T_NEWS", $news); //Assign announcements to smarty
  }

  /*Comments list*/
  if (!empty($lessons_list)) {
   $comments = eF_getTableData("comments cm JOIN content c JOIN lessons l ON c.lessons_ID = l.id", "cm.id AS id, cm.data AS data, cm.users_LOGIN AS users_LOGIN, cm.timestamp AS timestamp, c.name AS content_name, c.id AS content_ID, c.ctg_type AS content_type, l.name as show_lessons_name, l.id as show_lessons_id", "c.lessons_ID IN ('".implode("','", $lessons_list)."') AND cm.content_ID=c.id AND c.active=1 AND cm.active=1 AND cm.private=0", "cm.timestamp DESC LIMIT 5");
   $smarty -> assign("T_LESSON_COMMENTS", $comments); //Assign to smarty
  }

  /* Calendar */
  if (!isset($currentUser -> coreAccess['calendar']) || $currentUser -> coreAccess['calendar'] != 'hidden') {
   $today = getdate(time()); //Get current time in an array
   $today = mktime(0, 0, 0, $today['mon'], $today['mday'], $today['year']); //Create a timestamp that is today, 00:00. this will be used in calendar for displaying today
   isset($_GET['view_calendar']) && eF_checkParameter($_GET['view_calendar'], 'timestamp') ? $view_calendar = $_GET['view_calendar'] : $view_calendar = $today; //If a specific calendar date is not defined in the GET, set as the current day to be today

   $calendarOptions = array();
   if (!isset($currentUser -> coreAccess['calendar']) || $currentUser -> coreAccess['calendar'] == 'change') {
    $calendarOptions[] = array('text' => _ADDCALENDAR, 'image' => "16x16/add.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=calendar&add=1&view_calendar=".$view_calendar."&popup=1", "onClick" => "eF_js_showDivPopup('"._ADDCALENDAR."', 2)", "target" => "POPUP_FRAME");
   }
   $calendarOptions[] = array('text' => _GOTOCALENDAR, 'image' => "16x16/go_into.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=calendar");

   $smarty -> assign("T_CALENDAR_OPTIONS", $calendarOptions);
   $smarty -> assign("T_CALENDAR_LINK", basename($_SERVER['PHP_SELF'])."?ctg=calendar");
   isset($_GET['add_another']) ? $smarty -> assign('T_ADD_ANOTHER', "1") : null;

   $events = calendar :: getCalendarEventsForUser($currentUser);
   $events = calendar :: sortCalendarEventsByTimestamp($events);

   $smarty -> assign("T_CALENDAR_EVENTS", $events); //Assign events and specific day timestamp to smarty, to be used from calendar
   $smarty -> assign("T_VIEW_CALENDAR", $view_calendar);
  }

  /********** Facebook profile ******/
  if ($GLOBALS['configuration']['social_modules_activated'] & FB_FUNC_DATA_ACQUISITION) {

   if (isset($_SESSION['facebook_user']) && $_SESSION['facebook_user']) {
    $smarty -> assign("T_FB_INFORMATION", $_SESSION['facebook_details']);
   } else {

    $smarty -> assign("T_PREVIOUSMAINURL", $_SESSION['previousMainUrl'] );
    $smarty -> assign("T_OPEN_FACEBOOK_SESSION",1);
    $smarty -> assign("T_FACEBOOK_API_KEY", $GLOBALS['configuration']['facebook_api_key']);
   }
  }
  //-----------------------------------------

  $innertable_modules = array();
  foreach ($loadedModules as $module) {
   unset($InnertableHTML);
   $InnertableHTML = $module -> getDashboardModule();
   $InnertableHTML ? $module_smarty_file = $module -> getDashboardSmartyTpl() : $module_smarty_file = false;

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

  if (!empty($innertable_modules)) {
   $smarty -> assign("T_INNERTABLE_MODULES", $innertable_modules);
  }

  if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_COMMENTS) {
   $my_info_options = array(array('text' => _ADDCOMMENTTOMYPROFILE, 'image' => "16x16/edit.png", 'href' => $_SESSION['s_type'].".php?ctg=social&op=comments&action=insert&popup=1&user=". $currentUser -> user['login'], 'onClick' => "eF_js_showDivPopup('"._USERPROFILE."', 1)", 'target' => 'POPUP_FRAME'));

   $comments = $currentUser -> getProfileComments();
   if (sizeof($comments) > 0) {
       foreach ($comments as $id => $comment) {
        try {
      $file = new EfrontFile($comment['avatar']);
      list($comments[$id]['avatar_width'], $comments[$id]['avatar_height']) = eF_getNormalizedDims($file['path'],25,25);
     } catch (EfrontFileException $e) {
      $comments[$id]['avatar'] = G_SYSTEMAVATARSPATH."unknown_small.png";
      $comments[$id]['avatar_width'] = 25;
      $comments[$id]['avatar_height'] = 25;
     }
     $comments[$id]['time_ago'] = eF_convertIntervalToTime(time() - $comment['timestamp'], true). ' '._AGO;
       }
    $smarty -> assign("T_COMMENTS", $comments);
   } else {
    $smarty -> assign("T_COMMENTS", array());
   }

   $smarty -> assign("T_MY_INFO_OPTIONS", $my_info_options );
  }
  // Generally needed for the next social modules
  $all_related_users = $currentUser ->getRelatedUsers();

  /* My six people */
  if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_PEOPLE) {

   $related_users_count = sizeof($all_related_users);

   $max_related_users_to_show = 5;
   if ($related_users_count > $max_related_users_to_show) {
    $my_six_related_users_keys = array_rand($all_related_users, $max_related_users_to_show);

    $my_six_related_users = array();
    foreach ($my_six_related_users_keys as $key) {
     $my_six_related_users[] = $all_related_users[$key];
    }

    $related_users = $my_six_related_users;
   } else {
    $related_users = $all_related_users;
   }

   $my_related_users = eF_getTableData("users", "login, name, surname, avatar, status", "archive=0 AND login IN ('".implode("','", $related_users)."')");
   foreach ($my_related_users as $key => $user) {
    try {
     $file = new EfrontFile($user['avatar']);
     list($my_related_users[$key]['avatar_width'], $my_related_users[$key]['avatar_height']) = eF_getNormalizedDims($file['path'],50,50);
    } catch (EfrontFileException $e) {
     $my_related_users[$key]['avatar'] = G_SYSTEMAVATARSPATH."unknown_small.png";
     $my_related_users[$key]['avatar_width'] = 50;
     $my_related_users[$key]['avatar_height'] = 50;
    }
   }

   $smarty -> assign("T_MY_RELATED_USERS", $my_related_users);
   $my_related_people_options = array(array('text' => _GOTOPEOPLELIST, 'image' => "16x16/go_into.png", 'href' => $_SESSION['s_type']. ".php?ctg=social&op=people"));

   $smarty -> assign("T_MY_RELATED_PEOPLE_OPTIONS", $my_related_people_options );


   $smarty -> assign("T_MY_INCOMING_MESSAGES_OPTIONS", array(array('text' => _GOTOMYMESSAGES, 'image' => "16x16/go_into.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=messages")));
  }

  /* Timeline for the 10 most recent system events */
  if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_SYSTEM_TIMELINES) {
   $events = array();
   $myEvents = EfrontEvent::getEvents($all_related_users, true, 5);
   $allModules = eF_loadAllModules();
   $eventMessages = array();

   foreach ($myEvents as $key => $event) {

    if ($myEvents[$key] -> createMessage($allModules)) {
     if (strpos($myEvents[$key] ->event['time'], "-") === false) { // Added this to prevent events that changed time in the future as project expiration

      $new_event = array("time" => $myEvents[$key] -> event['time'], "message" => $myEvents[$key] ->event['message']);

      if (isset($myEvents[$key] -> event['editlink']) && $myEvents[$key] -> event['editlink']) {
       $new_event['editlink'] = $myEvents[$key] -> event['editlink'];
      }
      if (isset($myEvents[$key] -> event['deletelink']) && $myEvents[$key] -> event['deletelink']) {
       $new_event['deletelink'] = $myEvents[$key] -> event['deletelink'];
      }
      $events[] = $new_event;
     }
    }
   }

   $my_timeline_options = array(array('text' => _GOTOCOMPLETESYSTEMTIMELINE, 'image' => "16x16/go_into.png", 'href' => $_SESSION['s_type']. ".php?ctg=social&op=timeline"));

   $smarty -> assign("T_MY_TIMELINE_OPTIONS", $my_timeline_options );
   $smarty -> assign ("T_EVENTS", $events);
  }
 /********************* SHOW PROFILE POPUP ******************/
 } else if ($_GET['op'] == "show_profile") {
  if (isset($_GET['user'])) {
   $shownUser = EfrontUserFactory::factory($_GET['user']);

   // If chat is enabled
   if ($GLOBALS['configuration']['chat_enabled']) {
    $current_room = eF_getTableData("users_to_chatrooms" ,"chatrooms_ID","users_LOGIN = '".$shownUser -> user['login']."'");
    if (!empty($current_room)) {
     $smarty -> assign("T_CURRENT_CHATROOM", $current_room[0]['chatrooms_ID']);
    } else {
     // else the user is in the main room
     $smarty -> assign("T_CURRENT_CHATROOM", 0);
    }
   }

   try {
    $avatarfile = new EfrontFile($shownUser -> user['avatar']);
   } catch (EfrontFileException $ex) {
    $shownUser -> user['avatar'] = G_SYSTEMAVATARSPATH."unknown_small.png";
   }

   $smarty -> assign("T_PROFILE_TO_SHOW", $shownUser -> user);

   if ($GLOBALS['configuration']['social_modules_activated'] & SOCIAL_FUNC_COMMENTS) {
    $smarty -> assign("T_COMMENTS_ENABLED",1);
   }

   $comments = $shownUser -> getProfileComments();

   if (sizeof($comments) > 0) {
    foreach ($comments as $id => $comment) {
     try {
      $file = new EfrontFile($comment['avatar']);
      list($comments[$id]['avatar_width'], $comments[$id]['avatar_height']) = eF_getNormalizedDims($file['path'],25,25);
     } catch (EfrontFileException $e) {
      $comments[$id]['avatar'] = G_SYSTEMAVATARSPATH."unknown_small.png";
      $comments[$id]['avatar_width'] = 25;
      $comments[$id]['avatar_height'] = 25;
     }
     $comments[$id]['time_ago'] = eF_convertIntervalToTime(time() - $comment['timestamp'], true). ' '._AGO;
    }
    $smarty -> assign("T_COMMENTS", $comments);
   } else {

    $smarty -> assign("T_COMMENTS", array());
   }
  }
 /********************* PROFILE COMMENTS POPUP ******************/
 } else if ($_GET['op'] == "comments") {

    if (isset($_GET['action']) && $_GET['action'] == "delete") {
   // Only allowed to delete comments referring to you
   if (sizeof(eF_getTableData("profile_comments", "*", "id=".$_GET['id']." and users_LOGIN='".$_SESSION['s_login']."'")) > 0) {
    eF_deleteTableData("profile_comments", "id=".$_GET['id']);
    //eF_deleteTableData("search_keywords", "foreign_ID=".$id." AND table_name='comments'");
    $message = _COMMENTDELETED;
    $message_type = 'success';

    // Timelines add event
    EfrontEvent::triggerEvent(array("type" => EfrontEvent::DELETE_PROFILE_COMMENT_FOR_SELF, "users_LOGIN" => $_SESSION['s_login'], "users_name" => $currentUser -> user['name'], "users_surname" => $currentUser -> user['surname']));

    eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=personal&user=".$currentUser->user['login']."&op=dashboard&message=".urlencode($message)."&message_type=".$message_type);
    exit;

   }
  } elseif(isset($_GET['action']) && ($_GET['action'] == 'insert' || $_GET['action'] == 'change') && isset($_GET['user'])) {
   $load_editor = true;

   if (isset($_GET['action']) && $_GET['action'] == 'change' && isset($id)) {
    $form = new HTML_QuickForm("change_comments_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=social&op=comments&action=change&id=$id", "", null, true);
   } else{
    $form = new HTML_QuickForm("add_comments_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=social&op=comments&action=insert&user=" .$_GET['user'], "", null, true);
   }
   $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter


   $form -> addElement('textarea', 'data', _ADDYOURCOMMENT, 'class = "simpleEditor inputTextArea" style="width:35em;height:10em;"');
   $form -> addElement('submit', 'submit_comments', _COMMENTADD, 'class = "flatButton"');

   if (isset($_GET['action']) && $_GET['action'] == 'change' && isset($id)) {
    $comments_content = eF_getTableData("profile_comments", "*", "id=".$id);
    $form -> setDefaults(array('data' => $comments_content[0]['data']));
   }

   if ($form -> isSubmitted()) {
    if ($form -> validate()) {
     if (isset($_GET['action']) && $_GET['action'] == 'change' && isset($id)) {
      $comments_content = array("data" => $form -> exportValue('data'));

      if (eF_updateTableData("profile_comments", $comments_content, "id=".$id)) {
       $message = _SUCCESFULLYUPDATEDCOMMENT;
       $message_type = 'success';
      } else {
       $message = _SOMEPROBLEMEMERGED;
       $message_type = 'failure';
      }
     } elseif (isset($_GET['action']) && $_GET['action'] == 'insert') {
      $comments_content = array("data" => $form -> exportValue('data'),
              "timestamp" => time(),
              "authors_LOGIN" => $_SESSION['s_login'],
              "users_LOGIN" => $_GET['user']);

      if (eF_insertTableData("profile_comments", $comments_content)) {

       // Timelines add event
       if ($_SESSION['s_login'] == $_GET['user']) {
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_PROFILE_COMMENT_FOR_SELF, "users_LOGIN" => $_SESSION['s_login'], "users_name" => $currentUser -> user['name'], "users_surname" => $currentUser -> user['surname']));
       } else {
        $commentedUser = EfrontUserFactory::factory($_GET['user']);
        EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_PROFILE_COMMENT_FOR_OTHER, "users_LOGIN" => $_SESSION['s_login'], "users_name" => $currentUser -> user['name'], "users_surname" => $currentUser -> user['surname'], "entity_ID" => $_GET['user'], "entity_name" => $commentedUser -> user['name'] . " " . $commentedUser -> user['surname']));
       }

       $message = _SUCCESFULLYADDEDCOMMENT;
       $message_type = 'success';
      } else {
       $message = _SOMEPROBLEMEMERGED;
       $message_type = 'failure';
      }



     }
    }
   }

   $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

   $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
   $form -> setRequiredNote(_REQUIREDNOTE);
   $form -> accept($renderer);

   $smarty -> assign('T_COMMENTS_FORM', $renderer -> toArray());


   $smarty -> assign("T_HEADER_LOAD_SCRIPTS", array());
   $smarty -> assign("T_HEADER_EDITOR", $load_editor);
   $smarty -> assign("T_MESSAGE", $message);
   $smarty -> assign("T_MESSAGE_TYPE", $message_type);

  }
 /********************* PEOPLE PAGE ******************/
 } else if ($_GET['op'] == "people") {

  if (isset($_GET['ajax'])) {
   isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = 10;

   if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
    $sort = $_GET['sort'];
    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
   } else {
    $sort = 'timestamp';
    $order = 'asc';
   }

   if ($_GET['display'] == 2) {
    $all_related_users = $currentLesson ->getUsers();
    $all_related_users = array_keys($all_related_users);
    foreach ($all_related_users as $key => $user) {
     if ($user == $currentUser -> user['login']) {
      unset($all_related_users[$key]);
      break;
     }
    }
   } else {
    $all_related_users = $currentUser ->getRelatedUsers();
   }

   $temp_related_users = eF_getTableData("users", "login, name, surname, avatar, status", "login IN ('".implode("','", $all_related_users)."')");
   $my_related_users = array();
   foreach ($temp_related_users as $user) {
    $key = $user['login'];
    $my_related_users[$key] = $user;
    try {
     $file = new EfrontFile($user['avatar']);
     list($my_related_users[$key]['avatar_width'], $my_related_users[$key]['avatar_height']) = eF_getNormalizedDims($file['path'],50,50);
    } catch (EfrontFileException $e) {
     $my_related_users[$key]['avatar'] = G_SYSTEMAVATARSPATH."unknown_small.png";
     $my_related_users[$key]['avatar_width'] = 50;
     $my_related_users[$key]['avatar_height'] = 50;
    }
   }
   $related_users_events = eF_getTableData("events", "users_LOGIN, timestamp", "users_LOGIN IN ('".implode("','", $all_related_users)."') AND type IN ('".EfrontEvent::PROFILE_CHANGE."', '".EfrontEvent::AVATAR_CHANGE."', '".EfrontEvent::STATUS_CHANGE."')", "timestamp DESC");
   foreach ($related_users_events as $events) {
    $login = $events['users_LOGIN'];
    // The first value will be the one to set - the most recent - the rest will be disregarded
    if (!isset($my_related_users[$login]['timestamp'])) {
     $my_related_users[$login]['timestamp'] = $events['timestamp'];
    }
   }


   $my_related_users = eF_multiSort($my_related_users, $_GET['sort'], $order);

   if (isset($_GET['filter'])) {
    $my_related_users = eF_filterData($my_related_users , $_GET['filter']);
   }
 //	   $this -> event['time'] =

   $filtered_users_array = array();
   foreach ($my_related_users as $login => $user) {
    if ($user['timestamp']) {
     $my_related_users[$login]['time_ago'] = eF_convertIntervalToTime(time() - $user['timestamp'], true). ' '._AGO;
     $filtered_users_array[] = $login;
    } else {
     // For the most recently changed display, remove the ones that have not changed their display
     if ($sort == 'timestamp' && $_GET['display'] != "2") {
      unset($my_related_users[$login]);
     } else {
      $filtered_users_array[] = $login;
     }
    }
   }

   $common_lessons_result = eF_getTableData("users_to_lessons as ul1, users_to_lessons as ul2", "ul2.users_LOGIN, count(ul1.users_LOGIN) as common_lessons", "ul1.archive=0 and ul2.archive=0 and ul1.users_LOGIN = '".$currentUser->user['login']."' AND ul2.users_LOGIN IN ('".implode("','", $filtered_users_array) ."') AND ul1.lessons_ID = ul2.lessons_ID", "" , "ul2.users_LOGIN");
   foreach ($common_lessons_result as $common_lessons) {
    $my_related_users[$common_lessons['users_LOGIN']]['common_lessons'] = $common_lessons['common_lessons'];
   }
   $count = sizeof($my_related_users);
   $smarty -> assign("T_MY_RELATED_USERS_SIZE", $count);

   if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
    $my_related_users = array_slice($my_related_users, $offset, $limit);
   }

   if ($count) {
    $smarty -> assign("T_MY_RELATED_USERS", $my_related_users);
   }
   $smarty -> display($_SESSION['s_type'].'.tpl');
   exit;
  } else {

   // Light version to avoid avatar overhead"
   if ($_GET['display'] == 2) {
    $all_related_users = $currentLesson ->getUsers();
    $all_related_users = array_keys($all_related_users);
    foreach ($all_related_users as $key => $user) {
     if ($user == $currentUser -> user['login']) {
      unset($all_related_users[$key]);
      break;
     }
    }
    //pr($all_related_users);
   } else {
    $all_related_users = $currentUser ->getRelatedUsers();
   }
   //$my_related_users = eF_getTableData("users", "login, name, surname, avatar, status", "login IN ('".implode("','", $all_related_users)."')");
   /*

			foreach ($my_related_users as $key => $user) {

				if ($user['avatar'] != "") {

					$file = new EfrontFile($user['avatar']);

					list($my_related_users[$key]['avatar_width'], $my_related_users[$key]['avatar_height']) = eF_getNormalizedDims($file['path'],50,50);

				} else {

					$my_related_users[$key]['avatar'] = G_SYSTEMAVATARSPATH."unknown_small.png";

					$my_related_users[$key]['avatar_width']  = 50;

					$my_related_users[$key]['avatar_height'] = 50;

			}   }

			}

			*/
   $smarty -> assign("T_MY_RELATED_USERS", $all_related_users);
  }
  $options = array(
        0 => array('image' => '16x16/refresh.png', 'title' => _RECENTLYCHANGED, 'link' => 'javascript:void(0)', 'onClick' => 'changePeopleDisplay(\'recently_changed\', this)', 'selected' => true),
        1 => array('image' => '16x16/user_types.png', 'title' => _EVERYONE, 'link' => 'javascript:void(0)', 'onClick' => 'changePeopleDisplay(\'all\', this)', 'selected' => false ));
  if (isset($currentLesson)) {
   $options[] = array('image' => '16x16/lessons.png', 'title' => _RELATEDTOCURRENTLESSON, 'link' => 'javascript:void(0)', 'onClick' => 'changePeopleDisplay(\'current_lesson\', this)', 'selected' => false);
  }
  if (isset($_GET['display']) && $_GET['display'] == 2) {
   $options[0]['selected'] = false;
   $options[2]['selected'] = true;
  }
  //Reindex options so that indices are serial starting from 0 (this way they display correctly)
  $options = array_values($options);
 //pr($options);
  $smarty -> assign("T_TABLE_OPTIONS", $options);
 /********************* TIMELINES: Lesson and System ******************/
 } else if ($_GET['op'] == "timeline") {
  /******************* TIMELINE FOR CURRENT LESSON *****************/
  if (isset ($_GET['lessons_ID'])) {
   if ($currentLesson -> lesson['lesson_ID'] == $_GET['lessons_ID']) {
    $editedLesson = $currentLesson;
   } else {
    try {
     $editedLesson = new EfrontLesson($_GET['lessons_ID']);
    } catch (EfrontLessonException $e) {
     $message = $e -> getMessage().' ('.$e -> getCode().')';
     $message_type = 'failure';
    }
   }
   if (isset($_GET['post_topic'])) {
    /* Check permissions: everyone is allowed to post topic */
    //$form = new HTML_QuickForm("topic_form", "post", $_SESSION['s_type'].".php?ctg=social&op=timeline&lessons_ID=".$_SESSION['s_lessons_ID']."&post_topic=1", "",null, true);
      if (isset($_GET['action']) && $_GET['action'] == "delete") {
     // Only allowed to delete comments referring to you
     $result = eF_getTableData("lessons_timeline_topics_data", "*", "id=".$_GET['id']." and users_LOGIN='".$_SESSION['s_login']."'");
     if (sizeof($result) > 0) {
      $result = eF_getTableData("lessons_timeline_topics", "title", "id = " . $result[0]['topics_ID']);
      $topic_title = $result[0]['title'];
      eF_deleteTableData("lessons_timeline_topics_data", "id=".$_GET['id']);
      //eF_deleteTableData("search_keywords", "foreign_ID=".$id." AND table_name='comments'");
      $message = _COMMENTDELETED;
      $message_type = 'success';
      // Timelines add event
      EfrontEvent::triggerEvent(array("type" => EfrontEvent::DELETE_POST_FROM_LESSON_TIMELINE, "entity_ID" => $_GET['post_topic'], "entity_name" => $topic_title, "users_LOGIN" => $_SESSION['s_login'], "users_name" => $currentUser -> user['name'], "users_surname" => $currentUser -> user['surname']));
      eF_redirect("". $currentUser -> getType() . ".php?ctg=social&op=timeline&lessons_ID=".$_GET['lessons_ID']."&all=1&message=$message&message_type=$message_type");
      exit;
     } else {
      $message = _TOPICPOSTDOESNOTEXISTANYMORE;
      $message_type = 'failure';
      eF_redirect("". $currentUser -> getType() . ".php?ctg=social&op=timeline&lessons_ID=".$_GET['lessons_ID']."&all=1&message=$message&message_type=$message_type");
      exit;

     }
    } elseif(isset($_GET['action']) && ($_GET['action'] == 'insert' || $_GET['action'] == 'change')) {
     $load_editor = true;

     $result = eF_getTableData("lessons_timeline_topics", "title" , "id = " . $_GET['post_topic']);
     if ($result[0]['title'] == "") {
      // @todo problem
      echo "No such topic id";
     } else {
      $topic_name = $result[0]['title'];
     }

     if (isset($_GET['action']) && $_GET['action'] == 'change' && isset($_GET['id'])) {
      $id = $_GET['id'];
      $form = new HTML_QuickForm("change_topics_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=social&op=timeline&lessons_ID=".$_GET['lessons_ID']."&post_topic=".$_GET['post_topic']."&topics_ID=".$_GET['post_topic']."&action=change&id=$id", "", null, true);
      $smarty -> assign("T_POST_TOPIC_TIMELINE_TITLE", _EDITMESSAGEFORLESSONTIMELINETOPIC . " \"" . $topic_name . "\"");
     } else{
      $form = new HTML_QuickForm("add_topics_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=social&op=timeline&lessons_ID=".$_GET['lessons_ID']."&post_topic=".$_GET['post_topic']."&topics_ID=".$_GET['post_topic']."&action=insert", "", null, true);
         $smarty -> assign("T_POST_TOPIC_TIMELINE_TITLE", _ADDPOSTFORLESSONTOPIC . " \"" . $topic_name . "\"");

     }
     $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter

     $form -> addElement('textarea', 'data', _MESSAGE, 'class = "simpleEditor inputTextArea" style="width:40em;height:10em;"');
     $form -> addElement('submit', 'submit_topics', _SUBMIT, 'class = "flatButton"');

     if (isset($_GET['action']) && $_GET['action'] == 'change' && isset($id)) {
      $topics_content = eF_getTableData("lessons_timeline_topics_data", "*", "id=".$id);
      $form -> setDefaults(array('data' => $topics_content[0]['data']));
     }

     if ($form -> isSubmitted()) {
      if ($form -> validate()) {
       if (isset($_GET['action']) && $_GET['action'] == 'change' && isset($id)) {
        $topics_content = array("data" => $form -> exportValue('data'));

        if (eF_updateTableData("lessons_timeline_topics_data", $topics_content, "id=".$id)) {
         $_GET['topics_ID'] = $_GET['post_topic'];

         $message = _SUCCESFULLYUPDATEDTOPIC;
         $message_type = 'success';
        } else {
         $message = _SOMEPROBLEMEMERGED;
         $message_type = 'failure';
        }
       } elseif (isset($_GET['action']) && $_GET['action'] == 'insert') {
        $topics_content = array("data" => $form -> exportValue('data'),
              "topics_ID" => $_GET['post_topic'],
              "users_LOGIN" => $currentUser -> user['login']);

        if ($id = eF_insertTableData("lessons_timeline_topics_data", $topics_content)) {
         // Timelines add event
         EfrontEvent::triggerEvent(array("type" => EfrontEvent::NEW_POST_FOR_LESSON_TIMELINE_TOPIC, "entity_ID" => $_GET['post_topic'], "entity_name" => serialize(array("post_id" => $id, "data" => $form -> exportValue('data'), "topic_title" => $topic_name)), "lessons_ID" => $currentLesson -> lesson['id'], "lessons_name" => $currentLesson -> lesson['name'], "users_LOGIN" => $_SESSION['s_login'], "users_name" => $currentUser -> user['name'], "users_surname" => $currentUser -> user['surname']));

         $message = _SUCCESFULLYADDEDTOPICPOST;
         $message_type = 'success';
        } else {
         $message = _SOMEPROBLEMEMERGED;
         $message_type = 'failure';
        }
       }
      }
     }

     $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

     $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
     $form -> setRequiredNote(_REQUIREDNOTE);
     $form -> accept($renderer);

     $smarty -> assign('T_POST_TIMELINE_TOPICS_FORM', $renderer -> toArray());


     $smarty -> assign("T_HEADER_LOAD_SCRIPTS", array());
     $smarty -> assign("T_HEADER_EDITOR", $load_editor);
     $smarty -> assign("T_MESSAGE", $message);
     $smarty -> assign("T_MESSAGE_TYPE", $message_type);

    }


   }

   if (isset($_GET['add_topic']) || isset($_GET['del_topic']) || isset($_GET['edit_topic'])) {

    /* Check permissions: only professors are allowed to manage topics */
    if($currentUser -> getType() != 'professor') {
     eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=personal&message=".urlencode(_SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION)."&message_type=failure");
     exit;
    }

    // ON DELETING A LESSONTIMELINE TOPIC
    if (isset($_GET['del_topic'])) { //The administrator asked to delete a skill

     //@todo: delete events too?
     //eF_deleteTableData("lessons_timeline_topics", "type = " . . "  AND lessons_ID = ". ." AND entity_ID = '".$_GET['del_topic']."'");
     eF_deleteTableData("lessons_timeline_topics_data", "topics_ID = '".$_GET['del_topic']."'");
     eF_deleteTableData("lessons_timeline_topics", "id = '".$_GET['del_topic']."'");
     $message = _LESSONTIMELINETOPICDELETED;
     $message_type = 'success';
     eF_redirect("".$_SESSION['s_type'].".php?ctg=social&op=timeline&lessons_ID=".$_GET['lessons_ID']."&all=1&message=".$message."&message_type=".$message_type);
     exit;
    //ON INSERTING OR EDITING A LESSONTIMELINE TOPIC
    } else if (isset($_GET['add_topic']) || isset($_GET['edit_topic'])) {

     if (isset($_GET['add_topic'])) {
      $form = new HTML_QuickForm("topic_form", "post", $_SESSION['s_type'].".php?ctg=social&op=timeline&lessons_ID=".$_SESSION['s_lessons_ID']."&add_topic=1", "",null, true);
     } else {
      $form = new HTML_QuickForm("topic_form", "post", $_SESSION['s_type'].".php?ctg=social&op=timeline&lessons_ID=".$_SESSION['s_lessons_ID']."&edit_topic=" . $_GET['edit_topic'] , "", null, true);
      $topic = eF_getTableData("lessons_timeline_topics","title", "id ='".$_GET['edit_topic']."'");
     }

     $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
     $form -> addElement('text', 'topic_description', _LESSONTIMELINETOPIC, 'id="topic_description" class = "inputText" tabindex="1"');
     $form -> addRule('topic_description', _THEFIELD.' '._LESSONTIMELINETOPIC.' '._ISMANDATORY, 'required', null, 'client');

     // Hidden for maintaining the previous_url value
     $form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');
     $previous_url = getenv('HTTP_REFERER');
     if ($position = strpos($previous_url, "&message")) {
      $previous_url = substr($previous_url, 0, $position);
     }
     $form -> setDefaults(array( 'previous_url' => $previous_url));

     $form -> addElement('submit', 'submit_topic_details', _SUBMIT, 'class = "flatButton" tabindex="2"');

     if (isset($_GET['edit_topic'])) {
      $form -> setDefaults(array( 'topic_description' => $topic[0]['title']));
     }

     $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
     $renderer -> setRequiredTemplate(
      '{$html}{if $required}
       &nbsp;<span class = "formRequired">*</span>
      {/if}');

     //LESSONTIMELINE DATA SUBMISSION
     if ($form -> isSubmitted()) {
      if ($form -> validate()) {
       $topic_content = array('title' => $form->exportValue('topic_description'),
               'lessons_ID' => $currentLesson -> lesson['id']);

       if (isset($_GET['add_topic'])) {
        eF_insertTableData("lessons_timeline_topics", $topic_content);
        $message = _SUCCESSFULLYCREATEDLESSONTIMELINETOPIC;
        $message_type = 'success';

       } elseif (isset($_GET['edit_topic'])) {
        eF_updateTableData("lessons_timeline_topics", $topic_content , "id = '".$_GET['edit_topic']."'");
        $message = _LESSONTIMELINETOPICDATAUPDATED;
        $message_type = 'success';
       }

       // Return to previous url stored in a hidden - that way, after the insertion we can immediately return to where we were
       echo "<script>!/\?/.test(parent.location) ? parent.location = '". basename($form->exportValue('previous_url')) ."&message=".urlencode($message)."&message_type=".$message_type."' : parent.location = '".basename($form->exportValue('previous_url')) ."&message=".urlencode($message)."&message_type=".$message_type."';</script>";
       //eF_redirect("".$form->exportValue('previous_url')."&message=". $message . "&message_type=" . $message_type . "&tab=skills");
       exit;
      }
     }

     $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
     $form -> setRequiredNote(_REQUIREDNOTE);
     $form -> accept($renderer);
     $smarty -> assign('T_LESSONTIMELINE_TOPIC_FORM', $renderer -> toArray());
      }

     }

   // The main lesson timeline page contains also the form for inserting new topical timelines
   if (isset($_GET['all'])) {
    $form = new HTML_QuickForm("timeline_form", "post", $_SESSION['s_type'].".php?ctg=social&op=timeline&lessons_ID=".$_GET['lessons_ID'] . "&all=1", "", null, true);
    $result = eF_getTableData("lessons_timeline_topics", "id, title", "lessons_ID = " . $editedLesson -> lesson['id']);
    $topics = array("0" => _ANYTOPIC);
    foreach($result as $topic) {
     $id = $topic['id'];
     $topics[$id]= $topic['title'];
    }

    $form -> addElement('select', 'topic' , _SELECTTIMELINETOPIC, $topics , 'class = "inputText"  id="timeline_topic" onchange="javascript:change_topic(\'timeline_topic\')"');

    if (isset($_GET['topics_ID'])) {
     $form -> setDefaults(array('topic' => $_GET['topics_ID']));
     $smarty -> assign("T_TOPIC_TITLE", $topics[$_GET['topics_ID']]);
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form -> accept($renderer);
    $smarty -> assign('T_TIMELINE_FORM', $renderer -> toArray());

    if ($currentUser -> getRole($editedLesson) == "student") {
     $smarty -> assign("T_STUDENT", 1);
    }
   }

   /// Ajax getting lesson timeline events
   $loadScripts = array_merge($loadScripts, array('scriptaculous/prototype'));
   if (isset($_GET['ajax'])) {
    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = 10;

    // No sorting needed: getEvents returns sorted results according to time
    if(isset($_GET['all'])) {
     $avatarNormalDims = 50;
    } else {
     $avatarNormalDims = 25; // innertable avatars smaller
    }

    if(!isset($_GET['topics_ID']) || $_GET['topics_ID'] == 0) {
     if (isset($_GET['all'])) {
      $related_events = $editedLesson -> getEvents(false,true, $avatarNormalDims);
     } else {
      $related_events = $editedLesson -> getEvents(false,true, $avatarNormalDims, 5);
     }
    } else {
     $related_events = $editedLesson -> getEvents($_GET['topics_ID'] ,true, $avatarNormalDims, 5);
    }

    $allModules = eF_loadAllModules();

    $events = array();

    foreach ($related_events as $key => $event) {
     if ($related_events[$key] -> createMessage($allModules)) {
      if (strpos($related_events[$key] ->event['time'], "-") === false) { // Added this to prevent events that changed time in the future as project expiration

       $new_event = array("avatar" => $related_events[$key] ->event['avatar'],"avatar_width" => $related_events[$key] ->event['avatar_width'], "avatar_height" => $related_events[$key] ->event['avatar_height'], "time" => $related_events[$key] ->event['time'], "message" => $related_events[$key] ->event['message']);
       if ($related_events[$key] ->event['editlink']) {
        $new_event['editlink'] = $related_events[$key] ->event['editlink'];
       }
       if ($related_events[$key] ->event['deletelink']) {
        $new_event['deletelink'] = $related_events[$key] ->event['deletelink'];
       }

       if ($new_event['message'] != "" ){
        $events[] = $new_event;
       }
      }
     }

    }

    if (isset($_GET['filter'])) {
     $events = eF_filterData($events , $_GET['filter']);
    }
    $count = sizeof($events);
    $smarty -> assign("T_TIMELINE_EVENTS_SIZE", $count);

    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
     isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
     $events = array_slice($events, $offset, $limit);
    }

    if ($count) {
     $smarty -> assign("T_TIMELINE_EVENTS", $events);
    }

    $smarty -> display($_SESSION['s_type'].'.tpl');
    exit;

   }

  /******************* TIMELINE FOR ENTIRE SYSTEM*****************/
  } else {

      /// Ajax getting lesson timeline events
   if (isset($_GET['ajax'])) {
    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = 10;
   }

   if ($_SESSION['s_type'] != 'administrator') {
    $all_related_users = $currentUser ->getRelatedUsers();
    if (isset($_GET['ajax'])) {
     $result = eF_getTableData("users", "login, avatar", "login IN ('".implode("','", $all_related_users). "')");
     $users_avatars = array();
     foreach($result as $avatar) {
      $users_avatars[$avatar['login']] = $avatar['avatar'];
     }
    }
    $myEvents = EfrontEvent::getEvents($all_related_users, true, 1000);
   } else {
    if (isset($_GET['ajax'])) {
     $result = eF_getTableData("users", "login, avatar");
     $users_avatars = array();
     foreach($result as $avatar) {
      $users_avatars[$avatar['login']] = $avatar['avatar'];
     }
    }
    $myEvents = EfrontEvent::getEventsForAllUsers(true, 1000);
   }

   $allModules = eF_loadAllModules();
   $eventMessages = array();
   foreach ($myEvents as $key => $event) {

    if ($myEvents[$key] -> createMessage($allModules)) {
     if (strpos($myEvents[$key]->event['time'], "-") === false) { // Added this to prevent events that changed time in the future as project expiration
      $new_event = array("time" => $myEvents[$key] ->event['time'], "message" => $myEvents[$key] ->event['message']);

      if ($myEvents[$key] ->event['editlink']) {
       $new_event['editlink'] = $myEvents[$key] ->event['editlink'];
      }
      if ($myEvents[$key] ->event['deletelink']) {
       $new_event['deletelink'] = $myEvents[$key] ->event['deletelink'];
      }

      // Keep that for the avatar searching after the filtering
      $new_event['users_LOGIN'] = $event -> event['users_LOGIN'];
      $events[] = $new_event;
     }
    }
   }


   if (isset($_GET['filter'])) {
    $events = eF_filterData($events , $_GET['filter']);
   }


   if (isset($_GET['ajax'])) {
    foreach ($events as $key => $event) {

     $events[$key]['avatar'] = $users_avatars[$event['users_LOGIN']];
     try {
      $file = new EfrontFile($events[$key]['avatar']);
      list($events[$key]['avatar_width'], $events[$key]['avatar_height']) = eF_getNormalizedDims($file['path'],50,50);
     } catch (EfrontFileException $e) {
      $events[$key]['avatar'] = G_SYSTEMAVATARSPATH."unknown_small.png";
      $events[$key]['avatar_width'] = 50;
      $events[$key]['avatar_height'] = 50;
     }
    }
   }

   $count = sizeof($events);
   $smarty -> assign("T_TIMELINE_EVENTS_SIZE", $count);

   if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
    $events = array_slice($events, $offset, $limit);
   }

   if ($count) {
    $smarty -> assign("T_TIMELINE_EVENTS", $events);
   }

   if (isset($_GET['ajax'])) {
    $smarty -> display($_SESSION['s_type'].'.tpl');
    exit;
   }

  }

 }
