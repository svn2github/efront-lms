<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
$load_editor = true;
    if ($GLOBALS['configuration']['disable_news'] == 1 || (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] == 'hidden')) {
	    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
	}
    //Create shorthands for user access rights, to avoid long variable names	
	!isset($currentUser -> coreAccess['news']) || $currentUser -> coreAccess['news'] == 'change' ? $_change_ = 1 : $_change_ = 0;
	
	if ($_admin_) {
	    $news = news :: getNews(0);
	} else if ($_professor_) {
	    $news = news :: getNews(0, true) + news :: getNews($currentLesson -> lesson['id'], false);
	} else if ($_student_) {
	    $news = news :: getNews(0, true) + news :: getNews($currentLesson -> lesson['id'], true);
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
	    $smarty -> assign("T_FROM_TIMESTAMP", time());
	    $smarty -> assign("T_TO_TIMESTAMP", time() + 3600*24*30);

	    include("entity.php");
	}
?>