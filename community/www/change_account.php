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
   $currentUser -> logout(false);
   $newUser -> login($newUser -> user['password'], true);
   if ($_SESSION['s_type'] != 'administrator' && $lessonID) {
                setcookie('c_request', $_SESSION['s_type'].'.php?lessons_ID='.$lessonID, time() + 300);
            }
   unset($_SESSION['referer']);
   if ($GLOBALS['configuration']['login_redirect_page'] == "user_dashboard" && $newUser -> user['user_type'] != "administrator") {
    echo $newUser -> user['user_type'].'page.php?ctg=personal';
   } else {
    echo $newUser -> user['user_type'].'page.php';
   }
        }
    }

} catch (Exception $e) {
    header("HTTP/1.0 500");
    echo _UNAUTHORIZEDACCESS;
    exit;
}

?>
