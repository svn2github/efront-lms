<?php

//General initialization and parameters
session_cache_limiter('none');
session_start();

$path = "../libraries/";

/** Configuration file.*/
require_once $path."configuration.php";

//configuration variables
$timeout 	   = 100;
$defaultLesson = 22;
$allowedIps    = array();
debug();
pr($_GET);
if (isset($_GET['query']) && isset($_GET['msisdn'])) {
	
	header("Content-Type: text/plain");
	if (!eF_checkParameter($_GET['msisdn'], 'id')) {
		echo '4,The msisdn must be numeric';
	} else if (sizeof($allowedIps) > 0 && !in_array(ip2long($_SERVER['REMOTE_ADDR']), $allowedIps)) {
		echo '3,Your IP is not white-listed';
	} else {
		echo '0,'.(time()+$_GET['msisdn']);
	}
	
	exit;
}


try {
	isset($_GET['msisdn']) && !isset($_GET['login']) ? $_GET['login'] = $_GET['msisdn'] : null;
	isset($_GET['companyname']) && !isset($_GET['company']) ? $_GET['company'] = $_GET['companyname'] : null;
	
	//@todo: For login, consider lock down, ldap
	if ($_GET['login'] && eF_checkParameter($_GET['login'], 'login') && $_GET['login'] != $_SESSION['s_login']) {
		if (!$result = checkKey($_GET['login'], $_GET['key'])) {
			throw new Exception("Invalid key");
		} 
		try {
			$currentUser = EfrontUserFactory :: factory($_GET['login']);			
			$currentUser -> login($currentUser -> user['login']);
		} catch (EfrontUserException $e) {
			if ($e -> getCode() == EfrontUserException :: USER_NOT_EXISTS) {
				$newUserFields = array('login' 	  => $_GET['login'],
									   'active'	  => 1,
									   'name' 	  => isset($_GET['name'])    && eF_checkParameter($_GET['name'], 'text')    ? urldecode($_GET['name'])    : 'sample',
									   'surname'  => isset($_GET['surname']) && eF_checkParameter($_GET['surname'], 'text') ? urldecode($_GET['surname']) : 'sample',
									   'company'  => isset($_GET['company']) && eF_checkParameter($_GET['company'], 'text') ? urldecode($_GET['company']) : '',
									   'email'	  => isset($_GET['email'])   && eF_checkParameter($_GET['email'], 'email')  ? urldecode($_GET['email'])   : '');
				$currentUser = EfrontUser :: createUser($newUserFields);
				$currentUser -> login($currentUser -> user['login'], true);
			} else {
				throw ($e);
			}
		}
	} else if ($_SESSION['s_login'] && $_SESSION['s_password']) {
		$currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
	} else {
		throw new Exception("Authorization needed to view content on this page");
	}
	
	if (isset($_GET['content']) && eF_checkParameter($_GET['content'], 'id')) {
		$currentUnit   = new EfrontUnit($_GET['content']);
		$currentLesson = new EfrontLesson($currentUnit['lessons_ID']);
		if (!in_array($currentLesson -> lesson['id'], array_keys($currentUser -> getLessons()))) {
			$currentUser -> addLessons($currentLesson -> lesson['id'], 'student');
		}
		$_SESSION['s_lessons_ID'] = $currentLesson -> lesson['id'];		
	
		$currentContent = new EfrontContentTree($currentLesson);
		$currentUnit    = $currentContent -> seekNode($_GET['content']);
		
		//Set the unit as seen
		$currentUser -> setSeenUnit($currentUnit, $currentLesson, true);
	} else {
		$currentLesson = new EfrontLesson($defaultLesson);
		if (!in_array($currentLesson -> lesson['id'], array_keys($currentUser -> getLessons()))) {
			$currentUser -> addLessons($currentLesson -> lesson['id'], 'student');
		}
		$_SESSION['s_lessons_ID'] = $currentLesson -> lesson['id'];		
	
		$currentContent = new EfrontContentTree($currentLesson);
		$currentUnit    = $currentContent -> getFirstNode();		
	}

	$smarty -> assign("T_UNIT", $currentUnit);	
} catch (Exception $e) {
	pr($e);
	//$smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
	//$message      = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
	//$message_type = failure;
	$message      = $e -> getMessage().' ('.$e -> getCode().')';
}

$loadScripts = array('scriptaculous/prototype', 'EfrontScripts');
$smarty -> assign("T_HEADER_LOAD_SCRIPTS", array_unique($loadScripts));                    //array_unique, so it doesn't send duplicate entries

$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);

$smarty -> assign("T_CURRENT_USER", $currentUser);
$smarty -> assign("T_CURRENT_LESSON", $currentLesson);
$smarty -> assign("T_CURRENT_LESSON_PATH", G_LESSONSLINK.($currentLesson -> lesson['id']).'/');

$smarty -> display('sso.tpl');

function checkKey($login, $key) {
	 return (time()+$login - $key < $GLOBALS['timeout']);
}

?>