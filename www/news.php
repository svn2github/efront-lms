<?php
/**
* Announcements popup
* 
* This page is used as a popup to show, update, insert or delete an announcement.
* @package eFront
* @version 1.0
*/

session_cache_limiter('none');
session_start();

$path = "../libraries/";

include_once $path."configuration.php";

$message = '';$message_type = '';

//error_reporting(E_ALL);
//pr($_GET);pr($_POST);

if (isset($_GET['op']) && (eF_checkUser($_SESSION['s_login'], $_SESSION['s_password']) == "student")) {         //Only a professor/administrator may perform operations (insert, change, delete)
    eF_printMessage(_UNPRIVILEGEDATTEMPT);
    exit;
} elseif (!eF_checkUser($_SESSION['s_login'], $_SESSION['s_password'])) {                                       //Any logged-in user may view an announcement
    eF_printMessage("You must login to access this page");
    exit;
}
try {
    $currentUser = EfrontUserFactory :: factory($_SESSION['s_login'], false, 'professor');
    
    if (isset($currentUser -> coreAccess['news']) && $currentUser -> coreAccess['news'] != 'change') {
        throw new Exception();
    }
} catch (Exception $e) {
    header("location:index.php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    exit;
}

if (isset($_GET['id'])) {
    $id = eF_checkParameter($_GET['id'], 'uint');
    if ($id === false) {
        eF_printMessage(_INVALIDID);
        exit;
    }
}

if (isset($_GET['op']) && $_GET['op'] == "delete") {
    eF_deleteTableData("news", "id=".$id);
    EfrontSearch :: removeText('news', $id, 'title');	
    EfrontSearch :: removeText('news', $id, 'data');	
    $message      = _SUCCESFULLYDELETEDANNOUNCEMENTWINDOWCLOSE5SECONDS;
    $message_type = 'success';
} elseif(isset($_GET['op']) && ($_GET['op'] == 'insert' || $_GET['op'] == 'change')) {
    $load_editor = true;
    
    if (isset($_GET['op']) && $_GET['op'] == 'change' && isset($id)) {
        $form = new HTML_QuickForm("change_news_form", "post", basename($_SERVER['PHP_SELF'])."?op=change&id=$id", "", null, true);
    } else {
        $form = new HTML_QuickForm("add_news_form", "post", basename($_SERVER['PHP_SELF'])."?op=insert", "", null, true);
    }
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter

    $form -> addElement('text', 'title', _ANNOUNCEMENTTITLE, 'class = "inputText"');    
    $form -> addRule('title', _THEFIELD.' '._ISMANDATORY, 'required', null, 'client');
    $form -> addRule('title', _INVALIDFIELDDATA, 'checkParameter', 'text');

    $form -> addElement('textarea', 'data', _ANNOUNCEMENTBODY, 'class = "simpleEditor inputTextarea"');
    $form -> addElement('checkbox', 'email', _SENDASEMAILALSO, null, 'class = "inputCheckBox"');
    $form -> addElement('submit', 'submit_news', _ANNOUNCEMENTADD, 'class = "flatButton"');

    if (isset($_GET['op']) && $_GET['op'] == 'change' && isset($id)) {
        $news_content = eF_getTableData("news", "*", "id=".$id);
        $smarty -> assign('T_FROM_TIMESTAMP', $news_content[0]['timestamp']);
        $form -> setDefaults(array('title' => $news_content[0]['title'], 'data' => $news_content[0]['data']));
    } else {
        $smarty -> assign('T_FROM_TIMESTAMP', time());

    }

    if ($form -> isSubmitted()) {
        if ($form -> validate()) {
            $from_timestamp = mktime($_POST['from_Hour'], $_POST['from_Minute'], 0, $_POST['from_Month'], $_POST['from_Day'], $_POST['from_Year']);
            if (isset($_GET['op']) && $_GET['op'] == 'change' && isset($id)) {
                
                $news_content = array("title" => eF_addSlashes($form -> exportValue('title'), false),
                                      "data"  => eF_addSlashes($form -> exportValue('data'), false),
                                      "timestamp"  => $from_timestamp);

                if (eF_updateTableData("news", $news_content, "id=".$id)) {
                	EfrontSearch :: removeText('news', $id, 'data');									
					EfrontSearch :: insertText($news_content['data'], $id, "news", "data");
					EfrontSearch :: removeText('news', $id, 'title');									
					EfrontSearch :: insertText($news_content['title'], $id, "news", "title");
                	if(isset($_POST['email'])){
						 $ok = eF_emailNews($id);
						 if ($ok){
						 	$message      = _SUCCESFULLYUPDATEDANNOUNCEMENTWINDOWCLOSE5SECONDS;
                    		$message_type = 'success';
						 }else{
						 	$message      = _SOMEPROBLEMEMERGED;
                    		$message_type = 'failure';
						 }
					}
                    $message      = _SUCCESFULLYUPDATEDANNOUNCEMENTWINDOWCLOSE5SECONDS;
                    $message_type = 'success';
                } else {
                    $message      = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';
                }
            } elseif (isset($_GET['op']) && $_GET['op'] == 'insert') {
                $news_content = array("title"       => eF_addSlashes($form -> exportValue('title'), false),
                                      "data"        => eF_addSlashes($form -> exportValue('data'), false),
                                      "timestamp"   => $from_timestamp,
                                      "lessons_ID"  => isset($_SESSION['s_lessons_ID']) && $_SESSION['s_lessons_ID'] ? $_SESSION['s_lessons_ID'] : 0,
                                      "users_LOGIN" => $_SESSION['s_login']);

                $news_id = eF_insertTableData("news", $news_content);
				if ($news_id) {
				EfrontSearch :: insertText($news_content['title'], $news_id, "news", "title");
				EfrontSearch :: insertText($news_content['data'], $news_id, "news", "data");
                	if(isset($_POST['email'])){
						$ok = eF_emailNews($news_id);
						if ($ok){
							$message      = _SUCCESFULLYUPDATEDANNOUNCEMENTWINDOWCLOSE5SECONDS;
                    		$message_type = 'success';
						}else{
							$message      = _SOMEPROBLEMEMERGED;
                  	  		$message_type = 'failure';
						}					
					}
                    $message      = _SUCCESFULLYADDEDANNOUNCEMENTWINDOWCLOSE5SECONDS;
                    $message_type = 'success';
                } else {
                    $message      = _SOMEPROBLEMEMERGED;
                    $message_type = 'failure';
                }
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);

    $smarty -> assign('T_NEWS_FORM', $renderer -> toArray());    
} else {
    $announcement = eF_getTableData("news", "*", "id=".$id);
    if (sizeof($announcement) > 0) {
        $smarty -> assign("T_ANNOUNCEMENT", $announcement[0]);
        $announcements_options = array(
                                    array('text' => $announcement[0]["users_LOGIN"], 'image' => "16x16/user1.png", 'href' => "javascript:void(0)"),
                                    array('text' => "#filter:timestamp-".$announcement[0]["timestamp"]."#", 'image' => "16x16/calendar.png", 'href' => "javascript:void(0)")
                                );
        $smarty -> assign("T_NEWS_OPTIONS", $announcements_options);            

    } else {
        $message      = 'This announcement does not exist';
        $message_type = 'failure';
    }
}

if ($_SESSION['s_type'] != 'administrator') {
    try{
        $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);   
        $lesson_name = $currentLesson -> lesson['name'];
    }
    catch (Exception $e){
        $lesson_name = "";
    }
    $smarty -> assign("T_LESSON_NAME", $lesson_name);
}

$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array());
$smarty -> assign("T_HEADER_EDITOR", $load_editor);
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> load_filter('output', 'eF_template_formatTimestamp');
$smarty -> display("news.tpl");

?>