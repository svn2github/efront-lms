<?php
/**
* @package eFront
* @version 1.0
*/

//General initialization and parameters
session_cache_limiter('none');
session_start();

$path = "../libraries/";

/** Configuration file.*/
include_once $path."configuration.php";
if ($configuration['math_content']) {
	$loadScripts[] = 'ASCIIMathML';
}
if (!eF_checkUser($_SESSION['s_login'], $_SESSION['s_password']) || !isset($_SESSION['s_lessons_ID'])) {
    header("location:index.php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    exit;
}

try {
    $lesson = new EfrontLesson($_SESSION['s_lessons_ID']);
    $lessonUsers = $lesson -> getUsers();
    if (!in_array($_SESSION['s_login'], array_keys($lessonUsers))) {
        throw new Exception();
    } else {
        $currentUser = EfrontUserFactory :: factory($_SESSION['s_login'], false, $_SESSION['s_type']);
    }
    if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['news'] == 'hidden') {
        throw new Exception();
    }

    $content = new EfrontContentTree($lesson);

    $iterator = new EfrontVisitableFilterIterator(new EfrontNoSCORMFilterIterator(new EfrontContentFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($content -> tree), RecursiveIteratorIterator :: SELF_FIRST)))));
    foreach ($iterator as $key => $unit) {
        $parents = array();
        foreach ($content -> getNodeAncestors($unit) as $key2 => $value2) {
            $parents[] = $value2['name'];
        }

        $parentsList[$key] = implode("&nbsp;&raquo;&nbsp;", $parents);
		$contentList[$key] = mb_ereg_replace("<script.*?>.*?</script>", "", $unit['data']);
        $contentList[$key] = strip_tags($contentList[$key],'<img><applet><iframe><div><br><p><ul><li>');
    }

    if (isset($_GET['content_ID']) && eF_checkParameter($_GET['content_ID'], 'id')) {                                                                                          //The user asked for a specific unit
        if (!in_array($_GET['content_ID'], array_keys($contentList))) {
            throw new Exception();
        } else {
            $parentsList = array($parentsList[$_GET['content_ID']]);
            $contentList = array($contentList[$_GET['content_ID']]);
        }
    }


    $smarty -> assign("T_CONTENT", $contentList);
    $smarty -> assign("T_PARENT_LIST", $parentsList);       //Parents are needed for printing the titles

} catch (Exception $e) {
	header("location:index.php?message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    exit;
}
$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($loadScripts));
$smarty -> display("show_print_friendly.tpl");
?>

    