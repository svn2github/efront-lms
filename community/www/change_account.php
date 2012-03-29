<?php
session_cache_limiter('none');
session_start();

$path = "../libraries/";

include_once $path."configuration.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

try {
    if (isset($_GET['login']) && $_SESSION['s_login']) {
        $currentUser = EfrontUserFactory::factory($_SESSION['s_login']);
        $additionalAccounts = unserialize($currentUser -> user['additional_accounts']);

        if (in_array($_GET['login'], $additionalAccounts)) {
            $newUser = EfrontUserFactory::factory($_GET['login']);
   $lessonID = $_SESSION['s_lessons_ID'];
   $courseID = $_SESSION['s_courses_ID'];
   $currentUser -> logout(session_id());
   $newUser -> login($newUser -> user['password'], true);
   if ($_SESSION['s_type'] != 'administrator' && $lessonID) {
    if ($courseID) {
                 setcookie('c_request', $_SESSION['s_type'].'.php?lessons_ID='.$lessonID."&from_course=".$courseID, time() + 300, false, false, false, true);
    } else {
     setcookie('c_request', $_SESSION['s_type'].'.php?lessons_ID='.$lessonID, time() + 300);
    }
            }
   unset($_SESSION['referer']);
   $redirectPage = $GLOBALS['configuration']['login_redirect_page'];
   if ($redirectPage == "user_dashboard" && $newUser -> user['user_type'] != "administrator") {
    echo 'userpage.php?ctg=personal';
   }elseif (strpos($redirectPage, "module") !== false) {
    echo 'userpage.php?ctg=landing_page';
   } else {
    echo 'userpage.php';
   }
        }
    }

} catch (Exception $e) {
 handleAjaxExceptions($e);
}

?>
