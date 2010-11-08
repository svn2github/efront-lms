<?php
/**

* Module for search

*

* This file is included when a user wants to search for a string

* @package eFront

* @version 2.0

* Last change: 3/25/2008

* Changes from version 1.0 to 2.0:

* - Made new forum compatible

* - Added a wealth of new features

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
$result_command = array();
//error_reporting(E_ALL);
//echo "<pre>";print_r($_POST);print_r($_GET);echo"</pre>";
//associative array with commands and relative urls
//pr($_SESSION);
if ($_SESSION['s_type'] == "administrator") {
 $command_array = array("add user" => G_SERVERNAME."administrator.php?ctg=users&add_user=1",
      "add lesson" => G_SERVERNAME."administrator.php?ctg=lessons&add_lesson=1",
      "edit lesson" => G_SERVERNAME."administrator.php?ctg=lessons&edit_lesson=",
      "administration lesson" => G_SERVERNAME."administrator.php?ctg=lessons&lesson_settings=",
      "edit user" => G_SERVERNAME."administrator.php?ctg=users&edit_user=",
      "add category" => G_SERVERNAME."administrator.php?ctg=directions&add_direction=1",
      "edit category" => G_SERVERNAME."administrator.php?ctg=directions&edit_direction=",
      "add course" => G_SERVERNAME."administrator.php?ctg=courses&add_course=1",
      "edit course" => G_SERVERNAME."administrator.php?ctg=courses&edit_course=",
      "reports system" => G_SERVERNAME."administrator.php?ctg=statistics&option=system",
      "system reports" => G_SERVERNAME."administrator.php?ctg=statistics&option=system"
      );
} else if ($_SESSION['s_type'] == "professor") {
 $command_array = array("edit unit" => G_SERVERNAME."professor.php?ctg=content&edit_unit=",
      "edit project" => G_SERVERNAME."professor.php?ctg=projects&edit_project=",
      "score project" => G_SERVERNAME."professor.php?ctg=projects&project_results=",
      "edit test" => G_SERVERNAME."professor.php?ctg=tests&edit_test=",
      "preview test" => G_SERVERNAME."professor.php?ctg=tests&view_unit=",
      "edit question" => G_SERVERNAME."professor.php?ctg=tests&edit_question=");
 if ($_SESSION['s_lessons_ID']) {
  $command_array["add unit"] = G_SERVERNAME."professor.php?ctg=content&add_unit=1";
  $command_array["add project"] = G_SERVERNAME."professor.php?ctg=projects&add_project=1";
  $command_array["add test"] = G_SERVERNAME."professor.php?ctg=tests&add_test=1";
  $command_array["add rule"] = G_SERVERNAME."professor.php?ctg=rules&add_rule=1";
  //$command_array["add glossary"] = G_SERVERNAME."add_definition.php?add=1";
  $command_array["add glossary"] = G_SERVERNAME."professor.php?ctg=glossary";
  $command_array["add question one"] = G_SERVERNAME."professor.php?ctg=tests&add_question=1&question_type=multiple_one";
  $command_array["add question empty"] = G_SERVERNAME."professor.php?ctg=tests&add_question=1&question_type=empty_spaces";
  $command_array["add question many"] = G_SERVERNAME."professor.php?ctg=tests&add_question=1&question_type=multiple_many";
  $command_array["add question"] = G_SERVERNAME."professor.php?ctg=tests&add_question=1&question_type=multiple_many";
  $command_array["add question dev"] = G_SERVERNAME."professor.php?ctg=tests&add_question=1&question_type=raw_text";
  $command_array["add question match"] = G_SERVERNAME."professor.php?ctg=tests&add_question=1&question_type=match";
  $command_array["add question true"] = G_SERVERNAME."professor.php?ctg=tests&add_question=1&question_type=true_false";
  $command_array["add question drag"] = G_SERVERNAME."professor.php?ctg=tests&add_question=1&question_type=drag_drop";
  $command_array["upload file"] = G_SERVERNAME."professor.php?ctg=content&op=file_manager";
 } else {
  $command_array["add unit"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add project"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add test"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add rule"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add glossary"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add question one"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add question empty"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add question many"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add question"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add question dev"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add question match"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add question true"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["add question drag"] = G_SERVERNAME."professor.php?ctg=lessons";
  $command_array["upload file"] = G_SERVERNAME."professor.php?ctg=lessons";
 }
}
if ($_SESSION['s_type'] == "professor" || $_SESSION['s_type'] == "administrator") {
 $command_array["reports lesson"] = G_SERVERNAME.$_SESSION['s_type'].".php?ctg=statistics&option=lesson&tab=users&sel_lesson=";
 $command_array["reports user"] = G_SERVERNAME.$_SESSION['s_type'].".php?ctg=statistics&tab=lessons&option=user&sel_user=";
 $command_array["reports test"] = G_SERVERNAME.$_SESSION['s_type'].".php?ctg=statistics&option=test&sel_test=";
}
if ($_SESSION['s_type'] == "professor" || $_SESSION['s_type'] == "student") {
 $command_array["select lesson"]= G_SERVERNAME.$_SESSION['s_type'].".php?ctg=control_panel&lessons_ID=";
}
$command_array["send message"]= G_SERVERNAME.basename($_SERVER['PHP_SELF'])."?ctg=messages";


$command_array_values = array_values($command_array);
$command_array_keys = array_keys($command_array);

if (isset($_POST['search_text']) && mb_strlen(trim($_POST['search_text'])) <= 3) {
        $message = _SEARCHTEXTMUSTBENONEMPTYANDMORETHAN;
        if (sizeof(explode("?", $_POST['current_location'])) > 1) { //Check if there is a query string after the url, so we can append the message using a '&' or a '?'
            eF_redirect($_POST['current_location']."&message=".urlencode($message));
   exit;
        } else {
            eF_redirect($_POST['current_location']."?message=".urlencode($message));
   exit;
        }
}


if (isset($_POST['search_text'])) {
 $right_key = '';
 $query_array[0] = $_POST['search_text']; // check if each command is contained in search text
 foreach (array_keys($command_array) as $key => $command){

  $index = strpos($_POST['search_text'], $command);
  //pr($index);
  if($index !== false){
   $right_key = $key;
   //pr($right_key);
  }
 }
 //echo $right_key;
 if(isset($right_key)){
 //pr($query_array[0]);pr($command_array_keys[$right_key]);return;
  if (strcmp($query_array[0], $command_array_keys[$right_key]) == 0){
   eF_redirect("".$command_array_values[$right_key]);return;
  }else{

   $argument = mb_substr($query_array[0], -(mb_strlen($query_array[0])- mb_strlen($command_array_keys[$right_key]))+1);
   $opcode = $command_array_keys[$right_key];

   if(strpos($opcode,"lesson")!== false){
    $result_command = eF_getTableData("lessons","id,name","name like'%".$argument."%'");
    if(sizeof($result_command) == 1){
      eF_redirect("".$command_array_values[$right_key].$result_command[0]['id']);return;
    }else if(sizeof($result_command) > 1){
     $smarty -> assign("T_SEARCH_COMMAND", $result_command);
     $smarty -> assign("T_SEARCH_COMMAND_LOCATION", $command_array_values[$right_key]);
     $smarty -> assign("T_SEARCH_COMMAND_KEY1", "id");
     $smarty -> assign("T_SEARCH_COMMAND_KEY2", "name");
     //pr($result_command);pr($command_array_values[$right_key]);return;
    }
   }elseif(strpos($opcode,"user")!== false){
    $result_command = eF_getTableData("users","login","login like'%".$argument."%'");
    if(sizeof($result_command) == 1){
      eF_redirect("".$command_array_values[$right_key].$result_command[0]['login']);return;
    }else if(sizeof($result_command) > 1){
     $smarty -> assign("T_SEARCH_COMMAND", $result_command);
     $smarty -> assign("T_SEARCH_COMMAND_LOCATION", $command_array_values[$right_key]);
     $smarty -> assign("T_SEARCH_COMMAND_KEY1", "login");
     $smarty -> assign("T_SEARCH_COMMAND_KEY2", "login");
    }
   }elseif(strpos($opcode,"category")!== false){
    $result_command = eF_getTableData("directions","id,name","name like'%".$argument."%'");
     if(sizeof($result_command) == 1){
      eF_redirect("".$command_array_values[$right_key].$result_command[0]['id']);return;
    }else if(sizeof($result_command) > 1){
     $smarty -> assign("T_SEARCH_COMMAND", $result_command);
     $smarty -> assign("T_SEARCH_COMMAND_LOCATION", $command_array_values[$right_key]);
     $smarty -> assign("T_SEARCH_COMMAND_KEY1", "id");
     $smarty -> assign("T_SEARCH_COMMAND_KEY2", "name");
    }
   }elseif(strpos($opcode,"course")!== false){
    $result_command = eF_getTableData("courses","id,name","name like'%".$argument."%'");
    if(sizeof($result_command) == 1){
      eF_redirect("".$command_array_values[$right_key].$result_command[0]['id']);return;
    }else if(sizeof($result_command) > 1){
     $smarty -> assign("T_SEARCH_COMMAND", $result_command);
     $smarty -> assign("T_SEARCH_COMMAND_LOCATION", $command_array_values[$right_key]);
     $smarty -> assign("T_SEARCH_COMMAND_KEY1", "id");
     $smarty -> assign("T_SEARCH_COMMAND_KEY2", "name");
    }
   }elseif(strpos($opcode,"test")!== false){
    $result_command = eF_getTableData("tests,content","tests.id,content.name,content.lessons_ID","tests.content_ID=content.id and content.name like'%".$argument."%'");
     if(sizeof($result_command) == 1){
      eF_redirect("".$command_array_values[$right_key].$result_command[0]['id']."&lessons_ID=".$result_command[0]['lessons_ID']);return;
     }else if(sizeof($result_command) > 1){
     $smarty -> assign("T_SEARCH_COMMAND", $result_command);
     $smarty -> assign("T_SEARCH_COMMAND_LOCATION", $command_array_values[$right_key]);
     $smarty -> assign("T_SEARCH_COMMAND_KEY1", "id");
     $smarty -> assign("T_SEARCH_COMMAND_KEY2", "name");
     $smarty -> assign("T_SEARCH_COMMAND_CHANGELESSON", true);
    }
   }elseif(strpos($opcode,"unit")!== false){
    $result_command = eF_getTableData("content","id,name,lessons_ID","ctg_type!='tests' and name like'%".$argument."%'");
    if(sizeof($result_command) == 1){
      eF_redirect("".$command_array_values[$right_key].$result_command[0]['id']."&lessons_ID=".$result_command[0]['lessons_ID']);return;
    }else if(sizeof($result_command) > 1){
     $smarty -> assign("T_SEARCH_COMMAND", $result_command);
     $smarty -> assign("T_SEARCH_COMMAND_LOCATION", $command_array_values[$right_key]);
     $smarty -> assign("T_SEARCH_COMMAND_KEY1", "id");
     $smarty -> assign("T_SEARCH_COMMAND_KEY2", "name");
     $smarty -> assign("T_SEARCH_COMMAND_CHANGELESSON", true);
    }
   }elseif(strpos($opcode,"project")!== false){
    $result_command = eF_getTableData("projects","id,title,lessons_ID","title like'%".$argument."%'");
    //pr($result_command);
    if(sizeof($result_command) == 1){
     eF_redirect("".$command_array_values[$right_key].$result_command[0]['id']."&lessons_ID=".$result_command[0]['lessons_ID']);return;
    }else if(sizeof($result_command) > 1){
     $smarty -> assign("T_SEARCH_COMMAND", $result_command);
     $smarty -> assign("T_SEARCH_COMMAND_LOCATION", $command_array_values[$right_key]);
     $smarty -> assign("T_SEARCH_COMMAND_KEY1", "id");
     $smarty -> assign("T_SEARCH_COMMAND_KEY2", "title");
     $smarty -> assign("T_SEARCH_COMMAND_CHANGELESSON", true);
    }
   }elseif(strpos($opcode,"question")!== false){
    $result_command = eF_getTableData("questions","id,text,type,lessons_ID","text like'%".$argument."%'");
     if(sizeof($result_command) == 1){
      eF_redirect("".$command_array_values[$right_key].$result_command[0]['id']."&question_type=".$result[0]['type']."&lessons_ID=".$result_command[0]['lessons_ID']);return;
    }else if(sizeof($result_command) > 1){
     $smarty -> assign("T_SEARCH_COMMAND", $result_command);
     $smarty -> assign("T_SEARCH_COMMAND_LOCATION", $command_array_values[$right_key]);
     $smarty -> assign("T_SEARCH_COMMAND_KEY1", "id");
     $smarty -> assign("T_SEARCH_COMMAND_KEY2", "text");
     $smarty -> assign("T_SEARCH_COMMAND_KEY3", "type");
     $smarty -> assign("T_SEARCH_COMMAND_CHANGELESSON", true);
    }
   }
  }

  //pr($command_array_values[$right_key]);
  //eF_redirect("".$command_array_values[$right_key]);return;
 }

    $search_results_data = array();
    $search_results_forum = array();
    $search_results_pmsgs = array();
    $search_results_lessons = array();
    $search_results_current_lesson = array();
    $lesson_names = array();
    $tmp_data = array();
    $results = array();
 $search_results_files = array();

    $cr = explode(" ", $_POST['search_text']);
 $crTemp = array(); //since search is done by words with length>3, criteria must be also remove other words.This must go in search.class.php methods in future
    for ($i = 0; $i < sizeof($cr); $i++) {
  if (mb_strlen($cr[$i]) > 3)
  $crTemp[] = $cr[$i];
 }
 $cr = $crTemp;

    $results = EfrontSearch :: searchFull($_POST['search_text']);
//pr($results);exit;
    $lessons_have = $courses_have = null;
    $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']); //Get active lessons of this user
 $currentUser -> applyRoleOptions();

    if ($currentUser instanceOf EfrontLessonUser) {
        $smarty -> assign("T_CURRENT_USER", $currentUser);
        $userLessons = $currentUser -> getLessons(true);
        foreach ($userLessons as $key => $value){
            if (!$value -> lesson['active']) {
                unset($userLessons[$key]);
            } else {
                $lessons_have[] = $value->lesson['id'];
            }
        }
        $userCourses = $currentUser -> getUserCourses();
        $courses_have = array_keys($userCourses);

    }

    $have_results = false;

    if ($currentUser -> user['user_type'] == 'administrator') {
        $results_users = EfrontSearch :: searchUsers($_POST['search_text']);
        if (sizeof($results_users)>0) {
            $search_results_data[] = $results_users;
        }
    }
//pr($results);
    if ($results) {
        for ($i = 0; $i < sizeof($results); $i++) {
            if ($results[$i]['table_name'] == "comments") {
                $res1 = eF_getTableData("content,comments", "content.name AS name,content.id AS id,content.lessons_ID AS lessons_ID", "comments.content_ID=content.id AND comments.id=".$results[$i]['foreign_ID']);
                $type_str = _COMMENTS;
            } elseif ($results[$i]['table_name'] == "news") {
                $res1 = eF_getTableData($results[$i]['table_name'], "id,title AS name,lessons_ID, data", "id=".$results[$i]['foreign_ID']);
                $type_str = _ANNOUNCEMENTS;
            } elseif ($results[$i]['table_name'] == "content") {
                $res1 = eF_getTableData($results[$i]['table_name'], "id,name,lessons_ID,ctg_type, data", "id=".$results[$i]['foreign_ID']);
                $type_str = _LESSONCONTENT;
            } elseif ($results[$i]['table_name'] == "f_messages") {
                $res1 = eF_getTableData("f_messages, f_topics, f_forums", "f_forums.id as category_id, f_forums.lessons_ID, f_messages.id, f_messages.title, f_messages.body, f_messages.f_topics_ID, f_topics.title as topic_title", "f_topics_ID = f_topics.id and f_forums.id = f_forums_ID and f_messages.id=".$results[$i]['foreign_ID']);
                $type_str = _MESSAGESATFORUM;
            } elseif ($results[$i]['table_name'] == "f_personal_messages") {
                $res1 = eF_getTableData("f_personal_messages, f_folders", "f_personal_messages.id, f_personal_messages.title, f_personal_messages.users_LOGIN, f_personal_messages.body, f_personal_messages.sender, f_personal_messages.recipient , f_folders.name, f_folders.id as folder_id", "f_personal_messages.f_folders_ID = f_folders.id and f_personal_messages.id=".$results[$i]['foreign_ID']);
                $type_str = _MESSAGESATFORUM;
            } elseif ($results[$i]['table_name'] == "lessons") {
                $res1 = eF_getTableData($results[$i]['table_name'], "id as lessons_ID,name", "id=".$results[$i]['foreign_ID']." and active=1");
                $type_str = _LESSON;
            } elseif ($results[$i]['table_name'] == "courses") {
                $res1 = eF_getTableData($results[$i]['table_name'], "id as courses_ID,name", "id=".$results[$i]['foreign_ID']." and active=1");
                $type_str = _LESSON;
            } /*elseif ($results[$i]['table_name'] == "f_topics") {  changed my makriria to exclude topics 2008/11/4

                $res1     = eF_getTableData("f_messages, f_topics, f_forums", "f_forums.id as category_id, f_forums.lessons_ID, f_messages.id, f_messages.title, f_messages.f_topics_ID, f_topics.title as topic_title", "f_topics_ID = f_topics.id and f_forums.id = f_forums_ID and f_topics.id=".$results[$i]['foreign_ID']);

                $type_str = _MESSAGESATFORUM;

            }*/ elseif ($results[$i]['table_name'] == "f_forums") {
                $res1 = eF_getTableData("f_forums", "f_forums.id as category_id,lessons_ID", "id=".$results[$i]['foreign_ID']);
                $type_str = _MESSAGESATFORUM;
   } elseif ($results[$i]['table_name'] == "files") {
                $res1 = eF_getTableData("files", "*", "id=".$results[$i]['foreign_ID']);
                $type_str = _FILES;
   } elseif ($results[$i]['table_name'] == "questions") {
    $res1 = eF_getTableData("questions", "id,text as name, type, lessons_ID", "id=".$results[$i]['foreign_ID']);
    $type_str = _QUESTIONS;
   } elseif($results[$i]['table_name'] == "glossary") {
    $res1 = eF_getTableData("glossary", "id,name, info, lessons_ID", "id=".$results[$i]['foreign_ID']);
    $type_str = _GLOSSARY;
   }
            if (sizeof($res1) > 0) {
                $results[$i]['position'] == 0 ? $position_str = _TITLE : $position_str = _TEXT;
                if ((isset($res1[0]['lessons_ID']) && in_array($res1[0]['lessons_ID'], $lessons_have) || $res1[0]['lessons_ID'] == '0') || ($results[$i]['table_name'] == "f_messages" && $_SESSION['s_type'] == "administrator") || ($results[$i]['table_name'] == "f_topics" && $_SESSION['s_type'] == "administrator") || (isset($res1[0]['lessons_ID']) && $_SESSION['s_type'] == "administrator") || (isset($res1[0]['courses_ID']) && (in_array($res1[0]['courses_ID'], $courses_have) || $_SESSION['s_type'] == "administrator"))) {
                    if ($res1[0]['lessons_ID']) {
                        $lesson = eF_getTableData("lessons", "name,id", "id=".$res1[0]['lessons_ID']);
                    }
                    if (strlen($lesson[0]['name']) < 2) {
                        $lesson[0]['name'] = _ALL;
                    }
                    if ($results[$i]['table_name'] == 'courses') {
                        $search_results_courses[] = array('id' => $res1[0]['courses_ID'],
                                                          'score' => $results[$i]['score'] * 100,
                                                          'name' => $res1[0]['name']);
                    } elseif ($results[$i]['table_name'] != 'f_messages' && $results[$i]['table_name'] != 'f_topics' && $results[$i]['table_name'] != 'f_forums') {
                        if($results[$i]['table_name'] == "lessons"){
                            $basic_user_type = eF_getUserBasicType(false, $res1[0]['lessons_ID']);
                                $tmp_data = array('id' => $res1[0]['id'],
                                                               'name' => EfrontSearch :: highlightText($res1[0]['name'],$cr, 'resultsTitleBold'),
                                                               'table_name' => $results[$i]['table_name'],
                                                               'lessons_ID' => $res1[0]['lessons_ID'],
                                                               'lesson_name' => EfrontSearch :: highlightText($lesson[0]['name'],$cr, 'resultsTitleBold'),
                                                               'score' => $results[$i]['score'] * 100,
                                                               'type' => $type_str,
                                                               'user_type' => $basic_user_type,
                                                               'position' => $position_str);
                                $search_results_data[] = $tmp_data;
                                if ($res1[0]['lessons_ID'] != $_SESSION['s_lessons_ID']) {
                                    $search_results_lessons[$res1[0]['lessons_ID']][] = $tmp_data;
                                    $lesson_names[$res1[0]['lessons_ID']]['name'] = $lesson[0]['name'];
                                } else {
                                    $search_results_current_lesson[$res1[0]['lessons_ID']][] = $tmp_data;
                                    $current_lesson_name = $lesson[0]['name'];
                                }
                        } else if ($results[$i]['table_name'] == "glossary") {
                            $basic_user_type = eF_getUserBasicType(false, $res1[0]['lessons_ID']);
       $stripedContent = EfrontSearch :: resultsTextLimit(preg_replace("#<script.*?>.*?</script>#", "", $res1[0]['info']), $cr, 'resultsText');
       if (strcmp($stripedContent, "...") == 0) {
                             $stripedContent = _SEARCHTEXTWASINSCRIPT;
       }
                                $tmp_data = array('id' => $res1[0]['id'],
                                                               'name' => EfrontSearch :: highlightText($res1[0]['name'],$cr, 'resultsTitleBold'),
                                                               'table_name' => $results[$i]['table_name'],
                                                               'lessons_ID' => $res1[0]['lessons_ID'],
                                                               'lesson_name' => EfrontSearch :: highlightText($lesson[0]['name'],$cr, 'resultsTitleBold'),
                                                               'content' => $stripedContent,
                                                               'score' => $results[$i]['score'] * 100,
                                                               'type' => $type_str,
                                                               'user_type' => $basic_user_type,
                                                               'position' => $position_str);
                                $search_results_glossary[] = $tmp_data;
                                /*if ($res1[0]['lessons_ID'] != $_SESSION['s_lessons_ID']) {

                                    $search_results_glossary[$res1[0]['lessons_ID']][] = $tmp_data;

                                    $lesson_names[$res1[0]['lessons_ID']]['name'] = $lesson[0]['name'];

                                } else {

                                    $search_results_current_lesson[$res1[0]['lessons_ID']][] = $tmp_data;

                                    $current_lesson_name = $lesson[0]['name'];

                                }*/
                        } elseif ($results[$i]['table_name'] != "lessons" && $results[$i]['table_name'] != "questions" /*&& eF_isDoneContent($res1[0]['id'])*/) {
                            $basic_user_type = eF_getUserBasicType(false, $res1[0]['lessons_ID']);
       $stripedContent = EfrontSearch :: resultsTextLimit(preg_replace("#<script.*?>.*?</script>#", "", $res1[0]['data']), $cr, 'resultsText');
       if (strcmp($stripedContent, "...") == 0) {
                             $stripedContent = _SEARCHTEXTWASINSCRIPT;
       }
                                $tmp_data = array('id' => $res1[0]['id'],
                                                               'name' => EfrontSearch :: highlightText($res1[0]['name'],$cr, 'resultsTitleBold'),
                                                               'table_name' => $results[$i]['table_name'],
                                                               'lessons_ID' => $res1[0]['lessons_ID'],
                                                               'lesson_name' => EfrontSearch :: highlightText($lesson[0]['name'],$cr, 'resultsTitleBold'),
                                                               'ctg_type' => $res1[0]['ctg_type'],
                                                               'content' => $stripedContent,
                                                              // 'content'   => EfrontSearch :: highlightText(EfrontSearch :: wordLimiter(mb_substr(strip_tags($res1[0]['data']),strpos(strip_tags($res1[0]['data']), $cr[0]),2000), 40), $cr, 'resultsText'),
                                                               //'content1'  => strpos(strip_tags($res1[0]['data']), $cr[0]).EfrontSearch :: highlightText(strip_tags($res1[0]['data']), $cr, 'resultsText'),
                                                               'score' => $results[$i]['score'] * 100,
                                                               'type' => $type_str,
                                                               'user_type' => $basic_user_type,
                                                               'position' => $position_str);
                                $search_results_data[] = $tmp_data;
                                if ($res1[0]['lessons_ID'] != $_SESSION['s_lessons_ID']) {
                                    $search_results_lessons[$res1[0]['lessons_ID']][] = $tmp_data;
                                    $lesson_names[$res1[0]['lessons_ID']]['name'] = $lesson[0]['name'];
                                } else {
                                    $search_results_current_lesson[$res1[0]['lessons_ID']][] = $tmp_data;
                                    $current_lesson_name = $lesson[0]['name'];
                                }
                        } else if ($results[$i]['table_name'] == "questions" && $_SESSION['s_type'] == 'professor') {
                            $basic_user_type = eF_getUserBasicType(false, $res1[0]['lessons_ID']);
       $stripedContent = EfrontSearch :: resultsTextLimit(preg_replace("#<script.*?>.*?</script>#", "", $res1[0]['name']), $cr, 'resultsText');
       if (strcmp($stripedContent, "...") == 0) {
                             $stripedContent = _SEARCHTEXTWASINSCRIPT;
       }
                                $tmp_data = array('id' => $res1[0]['id'],
                                                               'name' => EfrontSearch :: highlightText($res1[0]['name'],$cr, 'resultsTitleBold'),
                                                               'table_name' => $results[$i]['table_name'],
                                                               'lessons_ID' => $res1[0]['lessons_ID'],
                                                               'lesson_name' => EfrontSearch :: highlightText($lesson[0]['name'],$cr, 'resultsTitleBold'),
                                                               'question_type'=> $res1[0]['type'],
                                                               'content' => $stripedContent,
                                                               'score' => $results[$i]['score'] * 100,
                                                               'type' => $type_str,
                                                               'user_type' => $basic_user_type,
                                                               'position' => $position_str);
                                $search_results_data[] = $tmp_data;
                                if ($res1[0]['lessons_ID'] != $_SESSION['s_lessons_ID']) {
                                    $search_results_lessons[$res1[0]['lessons_ID']][] = $tmp_data;
                                    $lesson_names[$res1[0]['lessons_ID']]['name'] = $lesson[0]['name'];
                                } else {
                                    $search_results_current_lesson[$res1[0]['lessons_ID']][] = $tmp_data;
                                    $current_lesson_name = $lesson[0]['name'];
                                }
                        }
     } elseif ($results[$i]['table_name'] != 'f_topics') { //it was simple else : changed my makriria to exclude topics 2008/11/4
      if (!isset($GLOBALS['currentUser'] -> coreAccess['forum']) || $GLOBALS['currentUser'] -> coreAccess['forum'] != 'hidden') {
       if ($lesson[0]['id'] != "") {
        $lessonTemp = new EfrontLesson($lesson[0]['id']);
       } else {
        $lesson[0]['id'] = 0;
       }
       $forumTitle = eF_getTableData("f_forums","title","id=".$res1[0]['category_id']);
       if ($_SESSION['s_type'] != "student" || $lessonTemp -> options['forum'] != 0 || $lesson[0]['id'] == 0) {
        $f_messageBody = EfrontSearch :: resultsTextLimit($res1[0]['body'], $cr, 'resultsText');
        $search_results_forum[] = array('category_id' => $res1[0]['category_id'],
               'lesson_name' => $forumTitle[0]['title'],
               'topic_subject' => $res1[0]['topic_title'],
               'topic_id' => $res1[0]['f_topics_ID'],
               'message_subject' => $res1[0]['title'],
               'body' => $f_messageBody,
               'message_id' => $res1[0]['id'],
               'table_name' => $results[$i]['table_name'],
               'position' => $position_str);
       }
       //pr($search_results_forum);
      }
     }
                } elseif ($results[$i]['table_name'] == 'f_personal_messages' && $_SESSION['s_login'] == $res1[0]['users_LOGIN']) {
     if (!isset($GLOBALS['currentUser'] -> coreAccess['personal_messages']) || $GLOBALS['currentUser'] -> coreAccess['personal_messages'] != 'hidden') {
      $search_results_pmsgs[] = array('message_subject' => EfrontSearch :: highlightText($res1[0]['title'],$cr,'resultsTitleBold'),
                                                    'message_id' => $res1[0]['id'],
                                                    'folder_name' => $res1[0]['name'],
                                                    'folder_id' => $res1[0]['folder_id'],
                                                    'body' => EfrontSearch :: highlightText($res1[0]['body'],$cr,'resultsTitleBold'),
                                                    'recipient' => $res1[0]['recipient'],
                                                    'sender' => $res1[0]['sender'],
                                                    'position' => $position_str);
     }
    } elseif ($results[$i]['table_name'] == 'files') {
     $pos1 = strpos($res1[0]['path'], '/content/lessons/'); //echo $pos1;
     if ($pos1 !== false) {
      $pos2 = strpos($res1[0]['path'], '/', $pos1+mb_strlen('/content/lessons/')); //echo $pos2;
      $lessonID = mb_substr($res1[0]['path'] , $pos1+mb_strlen('/content/lessons/'), $pos2-$pos1-mb_strlen('/content/lessons/')); //echo $lessonID;
     } else {
      $lessonID = 0;
     }
     try {
         $file = new EfrontFile($res1[0]['id']);
         $fileIcon = $file -> getTypeImage();
    //echo $res1[0]['shared'];
         if ($_SESSION['s_type'] == 'student' && in_array($lessonID, $lessons_have) && $res1[0]['shared'] != 0) {
          $search_results_files[] = array ('id' => $res1[0]['id'],
                  'path' => $res1[0]['path'],
                  'login' => $res1[0]['users_LOGIN'],
                  'date' => formatTimestamp($res1[0]['timestamp'], 'time_nosec'),
                  'name' => $file['name'],
                  'extension' => $file['extension'],
                  'icon' => $fileIcon);
         } elseif ($_SESSION['s_type'] == 'professor' && in_array($lessonID, $lessons_have)) {
          $search_results_files[] = array ('id' => $res1[0]['id'],
                  'path' => $res1[0]['path'],
                  'login' => $res1[0]['users_LOGIN'],
                  'date' => formatTimestamp($res1[0]['timestamp'], 'time_nosec'),
                  'name' => $file['name'],
                  'extension' => $file['extension'],
                  'icon' => $fileIcon);
         } elseif ($_SESSION['s_type'] == 'administrator') {
          $search_results_files[] = array ('id' => $res1[0]['id'],
                  'path' => $res1[0]['path'],
                  'login' => $res1[0]['users_LOGIN'],
                  'date' => formatTimestamp($res1[0]['timestamp'], 'time_nosec'),
                  'name' => $file['name'],
                  'extension' => $file['extension'],
                  'icon' => $$fileIcon);
         }
     } catch (Exception $e) {/*Do nothing, just skip the file*/}
    }
   }
        }
    }
    //sort results by score
    foreach ($search_results_lessons as $key => $value) {
  $search_results_lessons[$key] = eF_multiSort($search_results_lessons[$key], 'score', 'asc', true);
 }
 foreach ($search_results_current_lesson as $key => $value) {
  $search_results_current_lesson[$key] = eF_multiSort($search_results_current_lesson[$key], 'score', 'asc', true);
 }
 foreach ($search_results_courses as $key => $value) {
  $search_results_courses[$key] = eF_multiSort($search_results_courses[$key], 'score', 'asc', true);
 }
 $search_results_forum = eF_multiSort($search_results_forum, 'body', 'desc');
//pr($search_results_lessons);
 //highlight_search(word_limiter(substr($text,strpos($text, "Breathing"),1000), 20), $cr);
    $smarty -> assign("T_SEARCH_RESULTS_USERS", $results_users);
    $smarty -> assign("T_SEARCH_RESULTS", $search_results_data);
    $smarty -> assign("T_SEARCH_RESULTS_LESSONS", $search_results_lessons);
    $smarty -> assign("T_LESSON_NAMES", $lesson_names);
    $smarty -> assign("T_SEARCH_RESULTS_CURRENT_LESSON", $search_results_current_lesson);
    $smarty -> assign("T_CURRENT_LESSON_NAME", $current_lesson_name);
    $smarty -> assign("T_SEARCH_RESULTS_FORUM", $search_results_forum);
    $smarty -> assign("T_SEARCH_RESULTS_PERSONAL_MESSAGES", $search_results_pmsgs);
    $smarty -> assign("T_SEARCH_RESULTS_COURSES", $search_results_courses);
 $smarty -> assign("T_SEARCH_RESULTS_FILES", $search_results_files);
 $smarty -> assign("T_SEARCH_RESULTS_GLOSSARY",$search_results_glossary);

    if (!$search_results_data AND !$search_results_glossary AND !$search_results_forum AND !$search_results_pmsgs AND !$search_results_courses AND !$result_command AND !$search_results_files) {
        $message = _NOSEARCHRESULTSFOUND;
        if (sizeof(explode("?", $_POST['current_location'])) > 1) { //Check if there is a query string after the url, so we can append the message using a '&' or a '?'
            eF_redirect($_POST['current_location']."&message=".urlencode($message));
        } else {
            eF_redirect($_POST['current_location']."?message=".urlencode($message));
        }
    }
}
