<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
$load_editor = true;
    if ($GLOBALS['configuration']['disable_news'] == 1 || (isset($currentUser -> coreAccess['news']) && $currentUser -> coreAccess['content'] == 'news')) {
     eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
 }
    //Create shorthands for user access rights, to avoid long variable names
 !isset($currentUser -> coreAccess['news']) || $currentUser -> coreAccess['news'] == 'change' ? $_change_ = 1 : $_change_ = 0;

 if (isset($_GET['lessons_ID'])) {
  if ($currentUser -> user['user_type'] != 'administrator') {
      $eligibleLessons = $currentUser -> getEligibleLessons();
  }
     if (in_array($_GET['lessons_ID'], array_keys($eligibleLessons)) || $currentUser -> user['user_type'] == 'administrator') {
         $lessonId = $_GET['lessons_ID'];
     } else {
         $lessonId = array_keys($eligibleLessons);
     }
 } else {
     $lessonId = $currentLesson -> lesson['id'];
 }


 if ($_admin_) {
     $news = news :: getNews(0);
 } else if ($_professor_ || $_student_) {
  $news = news :: getNews(0, true);
  if ($_professor_) {
   $lessonNews = news :: getNews($lessonId, false);
  } else if ($_student_) {
   $lessonNews = news :: getNews($lessonId, true);
  }
  if ($_SESSION['s_type'] != 'administrator' && $_SESSION['s_current_branch']) { //this applies to branch urls
   $currentBranch = new EfrontBranch($_SESSION['s_current_branch']);
   $branchTreeUsers = array_keys($currentBranch->getBranchTreeUsers());
   foreach ($lessonNews as $key => $value) {
    if ($value['type'] != 'global' && !in_array($value['users_LOGIN'], $branchTreeUsers)) {
     unset($lessonNews[$key]);
    }
   }
  }
  $news = $news + $lessonNews;
 }


 $smarty -> assign("T_NEWS", $news);

 //An array of legal ids for editing entries
 $legalValues = array();

 foreach ($news as $value) {
     if ($value['users_LOGIN'] == $GLOBALS['currentUser'] -> user['login'] || $GLOBALS['currentUser'] -> user['user_type'] == 'administrator') {
         $legalValues[] = $value['id'];
     }
 }

 if ($_GET['view']) {
     $smarty -> assign("T_NEWS", $news[$_GET['view']]);
 } else {
     $entityName = 'news';
  if ($_GET['edit']) {
   $smarty -> assign("T_FROM_TIMESTAMP", $news[$_GET['edit']]['timestamp']);
   $smarty -> assign("T_TO_TIMESTAMP", $news[$_GET['edit']]['expire']);
  } else {
   $smarty -> assign("T_FROM_TIMESTAMP", time());
   $smarty -> assign("T_TO_TIMESTAMP", time() + 3600*24*30);
  }

     include("entity.php");
 }
