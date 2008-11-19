<?php
/**
* Chat functionality page
*
* This page implements the chat functionality. It is the iframe's target. 
*
* @package eFront
* @version 0.1
* @todo Limited users per room
* @todo Limited rooms
* @todo να ρυθμίζει ο ίδιος ο χρήστης το refresh
*/

session_cache_limiter('none');
session_start();

$path = "../../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";

/*Check the user type. If the user is not valid, he cannot access this page, so exit*/
if (isset($_SESSION['s_login']) && $_SESSION['s_password']) {
    try {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);

        if (MODULE_HCD_INTERFACE) {
            $currentUser -> aspects['hcd'] = EfrontEmployeeFactory :: factory($_SESSION['s_login']);
            $employee = $currentUser -> aspects['hcd'];
        }
        
        if ($_SESSION['s_lessons_ID']) {
            $userLessons = $currentUser -> getLessons();
            $currentUser -> applyRoleOptions($userLessons[$_SESSION['s_lessons_ID']]);                //Initialize user's role options for this lesson
            $currentLesson = new EfrontLesson($_SESSION['s_lessons_ID']);
        } else {
            $currentUser -> applyRoleOptions();                //Initialize user's role options for this lesson                   
        }
        $smarty -> assign("T_CURRENT_USER", $currentUser);
        
        if ($currentUser -> coreAccess['chat'] == 'hidden') {
            header("location:".G_SERVERNAME.$_SESSION['s_type'].".php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
        }
    } catch (EfrontException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
        header("location:index.php?message=".urlencode($message)."&message_type=failure");
        exit;
    }
} else {
    header("location:index.php?message=".urlencode(_YOUCANNOTACCESSTHISPAGE)."&message_type=failure");
    exit;
}


$initwidth = eF_getTableData("configuration", "value", "name='sidebar_width'");

if (empty($initwidth)) {
    $sideframe_width = 175;
} else {
    $sideframe_width = $initwidth[0]['value'];
}

$smarty -> assign("T_SIDEBAR_WIDTH_MINUS5", $sideframe_width-5);
$smarty -> assign("T_SIDEBAR_WIDTH_MINUS7", $sideframe_width-7);

/**This part is used at the page header*/
$css = eF_getTableData("configuration", "value", "name='css'");
if ($css && eF_checkParameter($css[0]['value'], 'filename') && is_file(G_ROOTPATH.'www/css/custom_css/'.$css[0]['value'])) {
    $smarty -> assign("T_HEADER_CSS", $css[0]['value']);
} else {
    $smarty -> assign("T_HEADER_CSS", "normal.css");
}
$smarty -> assign("T_CURRENT_CTG", $ctg);
$smarty -> assign("T_HEADER_EDITOR", $load_editor);

$smarty -> display("chat/blank.tpl");
?>
