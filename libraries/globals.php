<?php
/**
* File includes and configuration options
*
* This file is used to perform configuration and inclusion tasks.
* @package eFront
* @todo The $TRANSLATIONS nad $MONTH variables should go... they are a source of inconsistency
* @todo Write down where do all these constants and variables appear
*/

error_reporting( E_ERROR );
//error_reporting( E_ALL );
//ini_set("display_errors", true);
ini_set('include_path', $path.'../PEAR/');
//ini_set('include_path', ';'.G_ROOTPATH.'PEAR');

/**The key used for password hashing*/
define("G_MD5KEY", 'cDWQR#$Rcxsc');
define("G_PRIVATEKEY", 'eF%@$%^#@oNt&^!%(&q!2W');
$VERSIONTYPES = array('educational' => 'Educational', 'enterprise' => 'Enterprise', 'unregistered' => 'Unregistered', 'standard' => 'Standard');

define("G_VERSION_NUM", '3.5.2');
define("G_BUILD", 3060);
define("SQLREPORT", 0);

$MODULE_HCD_EVENTS['HIRED'] = 1;
$MODULE_HCD_EVENTS['NEW'] = 2;
$MODULE_HCD_EVENTS['JOB'] = 3;
$MODULE_HCD_EVENTS['WAGE_CHANGE'] = 4;
$MODULE_HCD_EVENTS['SKILL'] = 5;
$MODULE_HCD_EVENTS['SEMINAR'] = 6;
$MODULE_HCD_EVENTS['FIRED'] = 7;
$MODULE_HCD_EVENTS['LEFT'] = 8;

define("G_MAX_SKILLS_TABLE", "100");       //Default table size for skills table (for the forms)

if (!isset($_SERVER['REQUEST_URI']) || !$_SERVER[ 'REQUEST_URI' ]) { //Sets $_SERVER['REQUEST_URI'] for IIS       
	if (!( $_SERVER[ 'REQUEST_URI' ] = @$_SERVER['PHP_SELF']))	{
		$_SERVER[ 'REQUEST_URI' ] = $_SERVER['SCRIPT_NAME'];					
	}
	if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
		$_SERVER[ 'REQUEST_URI' ] .= '?' . $_SERVER[ 'QUERY_STRING' ];
    }
}
	
	
if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
   define("MSIE_BROWSER", 1);
} else {
   define("MSIE_BROWSER", 0);
}

/** Database handling functions*/
require_once $path."database.php";
if (version_compare(G_VERSION_NUM, '3.1') > -1) {                   //Set names to utf8 only if current version is 3.1 or higher
    $db -> Execute("SET NAMES 'UTF8'");
}
/** General purpose functions */
require_once $path."tools.php";
require_once $path."scorm.php";

//Language settings
if (isset($_GET['bypass_language']) && eF_checkParameter($_GET['bypass_language'], 'filename') && is_file($path."language/lang-".$_GET['bypass_language'].".php.inc")) {
    require_once $path."language/lang-".$_GET['bypass_language'].".php.inc";
    $setLanguage = $_GET['bypass_language'];
} else {
    if (isset($_SESSION['s_language'])) {
        require_once $path."language/lang-".$_SESSION['s_language'].".php.inc";
        $setLanguage = $_SESSION['s_language'];
    } elseif (sizeof($result = eF_getTableData("configuration", "value", "name='default_language'")) > 0) {
        require_once $path."language/lang-".$result[0]['value'].".php.inc";
        $setLanguage = $result[0]['value'];
    } else {
        require_once $path."language/lang-english.php.inc";
        $setLanguage = "english";
    }
}
$global_languagesymbols_array = array(  "arabic"    => "ar",
                                        "bulgarian" => "bg",
                                        "czech"     => "cz",
                                        "danizh"    => "da",
                                        "german"    => "de",
                                        "greek"     => "el",
                                        "spanish"   => "es",
                                        "english"   => "en",
                                        "finnish"   => "fi",
                                        "french"    => "fr",
                                        "hindi"     => "hi",
                                        "croatian"  => "hr",
                                        "italian"   => "it",
                                        "japanese"  => "ja",
                                        "dutch"     => "nl",
                                        "norwegian" => "no",
                                        "polish"    => "pl",
                                        "portuguese" => "pt",
                                        "romanian"   => "ro",
                                        "russian"   => "ru",
                                        "swedish"   => "sv",
                                        "chinese_traditional" => "ch_td",
                                        "chinese_simplified" => "ch_si");
/*Define default encoding to be utf-8*/
mb_internal_encoding('utf-8');
/*Set locale settings*/
setlocale(LC_CTYPE, _HEADERLANGUAGETAG);
setlocale(LC_TIME, _HEADERLANGUAGETAG);

/** The full URL to the folder containing the lessons*/
define("G_LESSONSLINK", G_SERVERNAME."content/lessons/");
/** The full URL to the folder containing the content*/
define("G_CONTENTLINK", G_SERVERNAME."content/");
/** The relative path (URL) to the lessons folder*/
define("G_RELATIVELESSONSLINK", "content/lessons/");
/** The relative path (URL) to the content folder*/
define("G_RELATIVECONTENTLINK", "content/");
/** The full filesystem path of the lessons directory*/
define("G_LESSONSPATH", G_ROOTPATH."www/content/lessons/");
/** The relative path (URL) to the admin folder*/
define("G_RELATIVEADMINLINK", G_SERVERNAME."content/admin/");
/** The full filesystem path of the admin directory*/
define("G_ADMINPATH", G_ROOTPATH."www/content/admin/");
/** The link to the admin files*/
define("G_ADMINLINK", G_SERVERNAME."content/admin/");
/** The full filesystem path of the content directory*/
define("G_CONTENTPATH", G_ROOTPATH."www/content/");
/** The full filesystem path of the images directory*/
define("G_IMAGESPATH", G_ROOTPATH."www/images/");
/** The backup directory, must be outside the server root for security reasons, and must have proper permissions*/
define("G_BACKUPPATH", G_ROOTPATH."backups/");
/** The users upload directory*/
define("G_USERSPATH", G_ROOTPATH."user_space/");
/** The messages attachments upload directory*/
define ("G_UPLOADPATH", G_ROOTPATH."upload/");
/** The users upload directory*/
define("G_AVATARSPATH", G_IMAGESPATH."avatars/");
/** The modules path */
define("G_MODULESPATH", G_ROOTPATH."www/modules/");
/** The smarty templates path*/
define("G_SMARTYPATH", G_ROOTPATH."libraries/smarty/templates/");
/**The directory where scorm files are uploaded*/
define("G_SCORMPATH", G_LESSONSPATH."scorm_uploaded_files/");
/** The custom css directory*/
define("G_CUSTOMCSSPATH", G_ROOTPATH."www/css/custom_css/");
/** The custom css link*/
define("G_CUSTOMCSSLINK", G_SERVERNAME."css/custom_css/");
/** The logo path*/
define("G_LOGOPATH", G_IMAGESPATH."logo/");
/** The course certificate logo paths*/
define("G_CERTIFICATELOGOPATH", G_IMAGESPATH."certificate_logos/");
/** The course certificate template paths*/
define("G_CERTIFICATETEMPLATEPATH", G_ROOTPATH."www/certificate_templates/");

$HCDEMPLOYEECATEGORIES = array('wage','hired_on','left_on' ,'address' ,'city'    ,'country' ,'father'  ,'homephone','mobilephone','sex','birthday','birthplace'              ,'birthcountry','mother_tongue'           ,'nationality' ,'company_internal_phone'  ,'office'      ,'doy'         ,'afm'         ,'police_id_number'        ,'driving_licence'         ,'work_permission_data'    ,'national_service_completed','employement_type'        ,'bank'        ,'bank_account','marital_status'          ,          'transport'   ,           'way_of_working');

/** Module abstract class inclusion **/
require_once 'module.class.php';

/** PEAR HTML_QuickForm Class files*/
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

/** PEAR Mail Class files*/
require_once "Mail.php";
require_once "Mail/mime.php";
/** System-specific classes*/
require_once $path."system.class.php";

/** Projects classes*/
require_once $path."project.class.php";
/** Tests classes*/
require_once $path."test.class.php";
/** Statistics-specific classes*/
require_once $path."tree.class.php";
/** Statistics-specific classes*/
require_once $path."statistics.class.php";

/** Metadata/information classes*/
require_once $path."metadata.class.php";
/** Search classes*/
require_once $path."search.class.php";
/** Paypal classes*/
if (is_file($path.'paypal.class.php')) {
    require_once $path."paypal.class.php";
}
/** Users-specific classes*/
require_once $path."user.class.php";
/** Content-specific classes*/
require_once $path."content.class.php";
/** Lesson class*/
require_once $path."lesson.class.php";
/** Course class*/
require_once $path."course.class.php";
/** Direction class*/
require_once $path."direction.class.php";
/** Group class*/
require_once $path."group.class.php";
/** Manifest class*/
require_once $path."manifest.class.php";
/** Filesystem class*/
require_once $path."filesystem3.class.php";
/** Emails functions*/
require_once $path."emails.php";
/** Content related functions*/
require_once $path."deprecated.php";
/** PEAR class for manipulating TAR files*/
require_once "Archive/Tar.php";
/** PEAR class for manipulating ZIP files*/
require_once $path."external/Zip.php";
/** TCPDF class for generating PDF files*/
require_once($path."external/tcpdf/tcpdf.php");


/**The smarty libraries*/
require_once $path."smarty/smarty_config.php";

/**The personal messages class*/
require_once($path."PersonalMessage.class.php");
/**Scorm functions*/
require_once($path."scorm.class.php");

/**The configuration class*/
require_once($path."configuration.class.php");
$configuration = EfrontConfiguration :: getValues();
$configuration['memory_limit']       ? ini_set('memory_limit',       $configuration['memory_limit'].'M')   : null;
$configuration['max_execution_time'] ? ini_set('max_execution_time', $configuration['max_execution_time']) : null;
$configuration['gz_handler']         ? ob_start ("ob_gzhandler")                                           : null;

if (!isset($_SESSION['s_version_type']) || !$_SESSION['s_version_type']) {
    $versionDetails = eF_checkVersionKey($configuration['version_key']);
    if (array_key_exists($versionDetails['type'], $VERSIONTYPES)) {
        define("G_VERSIONTYPE", $VERSIONTYPES[$versionDetails['type']]);
    } else {
        define("G_VERSIONTYPE", 'Open Source');
    }
    $_SESSION['s_version_type'] = G_VERSIONTYPE;
} else {
    define("G_VERSIONTYPE", $_SESSION['s_version_type']);
}

define("MODULE_HCD_INTERFACE", $configuration['version_hcd'] ? ($configuration['version_hcd'] == 1) : 0);
define("MODULE_PAYPAL", $configuration['version_paypal'] ? ($configuration['version_paypal'] == 1) : 0);

/** HCD Users-specific classes*/
if (MODULE_HCD_INTERFACE) {
    require_once $path."hcd_user.class.php";
    require_once $path."hcd.class.php";
}

//$configuration['display_errors']   ? ini_set('display_errors',     $configuration['display_errors'])     : null;
//pr($configuration);pr($_SERVER['REMOTE_ADDR']);

if (!eF_checkIP()) {
    eF_printMessage('You cannot access this page due to an IP ban');
    exit;
}

/** Maximum file size (in bytes). Attention! it must be: memory_limit > post_max_size > upload_max_filesize > G_MAXFILESIZE*/
define("G_MAXFILESIZE", 3000000);
/**Session timeout is set to 20 min */
define("G_SESSION_TIMEOUT", 3600);
/**The avatar max file size*/
define("G_AVATARMAXFILESIZE", 51200);
/** Avatar image height*/
define("G_AVATAR_HEIGHT", 100);
/** Avatar image width*/
define("G_AVATAR_WIDTH", 100);

/** Maximum number of messages held in the system **/
define("G_QUOTA_NUM_OF_MESSAGES", 2000);
/** Maximum quota of messages in KB: 100MB **/
define("G_QUOTA_KB", 102400);
/** Maximum folders number **/
define("G_MAX_MESSAGES_FOLDERS", 16);


define("G_LOGOMAXFILESIZE", 50000);

define("G_DEFAULT_TABLE_SIZE", "20");       //Default table size for sorted table

define("G_MAX_USERSFREE", 3);
define("G_MAX_USERSBASIC", 80);
define("G_MAX_USERSSERVER", 300);

define("G_MAX_LESSONSFREE", 2);

define("G_MAX_LESSONSBASIC", 8);

define("G_MAX_LESSONSSERVER", 30);
//Set debugging parameter
if (isset($_GET['debug'])) {
    ini_set("display_errors", true);
    error_reporting(E_ALL);
    $db -> debug = true;
    /** Debug mode*/
    define("G_DEBUG", 1);
} else {
    /** Debug mode*/
    define("G_DEBUG", 0);
    //$db -> debug = true;
}

//Translations are used when we need to translate a variable
$TRANSLATION['student']         = _STUDENT;
$TRANSLATION['reseller']        = _INTERMEDIATESELLER;
$TRANSLATION['professor']       = _PROFESSOR;
$TRANSLATION['administrator']   = _ADMINSTRATOR;
$TRANSLATION['control']         = _CONTROLCENTER;
$TRANSLATION['lessons']         = _LESSONS;
$TRANSLATION['theory']          = _THEORY;
$TRANSLATION['examples']        = _EXAMPLES;
$TRANSLATION['exercises']       = _PROJECTS;
$TRANSLATION['tests']           = _TESTS;
$TRANSLATION['users']           = _USERS;
$TRANSLATION['messages']        = _MESSAGES;
$TRANSLATION['forum']           = _FORUM;
$TRANSLATION['personal']        = _OPTIONS;
$TRANSLATION['statistics']      = _STATISTICS;
$TRANSLATION['nocontent']       = _NOCONTENT;
$TRANSLATION['inactive']        = _INACTIVE;
$TRANSLATION['content']         = _CONTENTMANAGEMENT;
$TRANSLATION['current_content'] = _SCHEDULING;
$TRANSLATION['raw_text']        = _OFDEVELOPMENT;
$TRANSLATION['multiple_one']    = _MULTIPLECHOICEONESELECTION;
$TRANSLATION['multiple_many']   = _MULTIPLECHOICEMULTIPLESELECTIONS;
$TRANSLATION['match']           = _OFMATCH;
$TRANSLATION['empty_spaces']    = _OFEMPTYSPACES;
$TRANSLATION['true_false']      = _TRUEFALSE;
$TRANSLATION['low']             = _LOW;
$TRANSLATION['medium']          = _MEDIUM;
$TRANSLATION['high']            = _HIGH;
$TRANSLATION['comments']        = _COMMENTS;
$TRANSLATION['online']          = _ONLINEUSERS;
$TRANSLATION['dynamic_periods'] = _PERIODSPERSTUDENT;
$TRANSLATION['netmeeting']      = _NETMEETINGSUPPORT;
$TRANSLATION['hasnot_seen']     = _IFSTUDENTHASNOTSEEN;
$TRANSLATION['hasnot_passed']   = _IFSTUDENTHASNOTPASSED;
$TRANSLATION['always']          = _STUDENTALLWAYS;
$TRANSLATION['rules']           = _ACCESSRULES;
$TRANSLATION['emails']          = _EMAILS;
$TRANSLATION['chat']            = _CHAT;
$TRANSLATION['digital_library'] = _DIGITALLIBRARY;
$TRANSLATION['login']           = _ENTER;
$TRANSLATION['contact']         = _CONTACT;
$TRANSLATION['signup']          = _REGISTER;
$TRANSLATION['scorm']           = _SCORM;
$TRANSLATION['glossary']        = _GLOSSARY;
$TRANSLATION['tracking']        = _TRACKING;
$TRANSLATION['calendar']        = _CALENDAR;
$TRANSLATION['survey']          = _SURVEY;
$TRANSLATION['new_content']     = _NEWCONTENT;

// Translation of months... since it appears in just 3 function inside tools.php, maybe it should go
$MONTH['01'] = _OFJANUARY;
$MONTH['02'] = _OFFEBRUARY;
$MONTH['03'] = _OFMARCH;
$MONTH['04'] = _OFAPRIL;
$MONTH['05'] = _OFMAY;
$MONTH['06'] = _OFJUNE;
$MONTH['07'] = _OFJULY;
$MONTH['08'] = _OFAUGUST;
$MONTH['09'] = _OFSEPTEMBER;
$MONTH['10'] = _OFOCTOBER;
$MONTH['11'] = _OFNOVEMBER;
$MONTH['12'] = _OFDECEMBER;
$MONTH2['01'] = _JANUARY;
$MONTH2['02'] = _FEBRUARY;
$MONTH2['03'] = _MARCH;
$MONTH2['04'] = _APRIL;
$MONTH2['05'] = _MAY;
$MONTH2['06'] = _JUNE;
$MONTH2['07'] = _JULY;
$MONTH2['08'] = _AUGUST;
$MONTH2['09'] = _SEPTEMBER;
$MONTH2['10'] = _OCTOBER;
$MONTH2['11'] = _NOVEMBER;
$MONTH2['12'] = _DECEMBER;
$smarty -> load_filter('output', 'eF_template_formatTimestamp');
$smarty -> load_filter('output', 'eF_template_formatLogins');
$smarty -> load_filter('output', 'eF_template_includeScripts');

//$smarty -> load_filter('output', 'gzip');
if (preg_match("/compatible; MSIE 6/", $_SERVER['HTTP_USER_AGENT']) && !preg_match("/compatible; MSIE 7/", $_SERVER['HTTP_USER_AGENT'])) {
    $smarty -> assign("T_BROWSER", 'IE6');
    $smarty -> load_filter('output', 'eF_template_replacePng');
} else if(preg_match("/compatible; MSIE 7/", $_SERVER['HTTP_USER_AGENT'])) {
    $smarty -> assign("T_BROWSER", 'IE7');
} else if(preg_match("/Chrome/", $_SERVER['HTTP_USER_AGENT'])) {
    $smarty -> assign("T_BROWSER", 'Chrome');
} else if(preg_match("/Safari/", $_SERVER['HTTP_USER_AGENT'])) {
    $smarty -> assign("T_BROWSER", 'Safari');
} else {
    $smarty -> assign("T_BROWSER", 'Firefox');
}

$smarty -> load_filter('output', 'eF_template_formatScore');

$CURRENCYSYMBOLS = array('USD' => '$',          'EUR' => '&euro;', 'JPY' => '&yen;',        'GBP' => '&pound;',        'CAD' => '$',                'AUD' => '$' );
$CURRENCYNAMES   = array('USD' => 'US Dollars', 'EUR' => 'Euros',  'JPY' => 'Japanese Yen', 'GBP' => 'British Pounds', 'CAD' => 'Canadian Dollars', 'AUD' => 'Australian Dollars');

// Chatroom errors - also copied to js/print-script.php to avoid including the entire file
define("_CHATROOMDOESNOTEXIST_ERROR", "-2");
define("_CHATROOMISNOTENABLED_ERROR", "-3");
$smarty -> assign("T_CURRENCYSYMBOLS", $CURRENCYSYMBOLS);
$smarty -> assign("T_CONFIGURATION", $configuration);       //Assign global configuration values to smarty

//$db -> debug = true;
//pr($matches);
//pr($_SERVER);
?>
