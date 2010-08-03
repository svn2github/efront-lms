<?php
/**

$LastChangedRevision$

* File includes and configuration options

*

* This file is used to perform configuration and inclusion tasks.

* @package eFront

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
//Used for debugging purposes only
$debug_TimeStart = microtime(true);
/**

 * Set debugging level:

 * 0: no error reporting

 * 1: E_WARNING

 * 2: E_ALL

 * 4: verbose database

 * 8: time panel

 * 16: override system setting

 */
$debugMode = 0;
//Set the default content type to be utf-8, as everything in the system
header('Content-Type: text/html; charset=utf-8');
error_reporting( E_ERROR );
//error_reporting( E_ALL );ini_set("display_errors", true);define("NO_OUTPUT_BUFFERING", true);        //Uncomment this to get a full list of errors
//Prepend the include path with efront folders
set_include_path($path.'../PEAR/'
                . PATH_SEPARATOR . $path.'includes/'
                . PATH_SEPARATOR . $path
                . PATH_SEPARATOR . get_include_path());
//Set global defines for the system
setDefines();
//Fix IIS bug by setting the request URI
setRequestURI();
//Set default exception handler to be defaultExceptionHandler() function
set_exception_handler('defaultExceptionHandler');
register_shutdown_function('shutdownFunction');
/** General tools for system */
require_once("tools.php");
/** Database manipulation functions*/
require_once("database.php");
/** General class representing an entity*/
require_once("entity.class.php");
//Get configuration values
$configuration = EfrontConfiguration :: getValues();
//Set debugging parameter
if (isset($_GET['debug']) && $configuration['debug_mode']) {
    debug();
 define("NO_OUTPUT_BUFFERING", 1);
    define("G_DEBUG", 1);
} elseif ($configuration['debug_mode']) {
 define("G_DEBUG", 1);
} else {
 define("G_DEBUG", 0);
}
//Turn on compressed output buffering, unless NO_OUTPUT_BUFFERING is defined or it's turned off from the configuration
!defined('NO_OUTPUT_BUFFERING') && $configuration['gz_handler'] ? ob_start ("ob_gzhandler") : null;
//Set the memory_limit and max_execution_time PHP settings, but only if system-specific values are greater than global
isset($configuration['memory_limit']) && $configuration['memory_limit'] && str_replace("M", "", ini_get('memory_limit')) < $configuration['memory_limit'] ? ini_set('memory_limit', $configuration['memory_limit'].'M') : null;
isset($configuration['max_execution_time']) && $configuration['max_execution_time'] && ini_get('max_execution_time') < $configuration['max_execution_time'] ? ini_set('max_execution_time', $configuration['max_execution_time']) : null;
//Set the time zone
isset($GLOBALS['configuration']['time_zone']) && isset($GLOBALS['configuration']['time_zone']) ? date_default_timezone_set($GLOBALS['configuration']['time_zone']) : null;
ini_set('magic_quotes_runtime', false); // check http://www.smarty.net/forums/viewtopic.php?t=4936
//handleSEO();

//Setup the current version
setupVersion();

//query decryption
//Input sanitization
foreach ($_GET as $key => $value) {
    if (is_string($value)) {
        $_GET[$key] = strip_tags($value);
    }
}
if ($GLOBALS['configuration']['eliminate_post_xss']) {
 foreach ($_POST as $key => $value) {
     if (is_string($value)) {
         $_POST[$key] = strip_script_tags($value);
     }
 }
}
//Language settings. $GLOBALS['loadLanguage'] can be used to exclude language files from loading, for example during certain ajax calls
if (!isset($GLOBALS['loadLanguage']) || $GLOBALS['loadLanguage']) {
    if (isset($_GET['bypass_language']) && eF_checkParameter($_GET['bypass_language'], 'filename') && is_file($path."language/lang-".$_GET['bypass_language'].".php.inc")) {
        /** We can bypass the current language any time by specifing 'bypass_language=<lang>' in the query string*/
        require_once $path."language/lang-".$_GET['bypass_language'].".php.inc";
        $setLanguage = $_GET['bypass_language'];
    } else {
        if (isset($_SESSION['s_language']) && is_file($path."language/lang-".$_SESSION['s_language'].".php.inc")) {
            /** If there is a current language in the session, use that*/
            require_once $path."language/lang-".$_SESSION['s_language'].".php.inc";
            $setLanguage = $_SESSION['s_language'];
        } elseif ($GLOBALS['configuration']['default_language'] && is_file($path."language/lang-".$GLOBALS['configuration']['default_language'].".php.inc")) {
            /** If there isn't a language in the session, use the default system language*/
            require_once $path."language/lang-".$GLOBALS['configuration']['default_language'].".php.inc";
            $setLanguage = $GLOBALS['configuration']['default_language'];
        } else {
            //If there isn't neither a session language, or a default language in the configuration, use english by default
            require_once $path."language/lang-english.php.inc";
            $setLanguage = "english";
        }
    }
}
//Set locale settings
setlocale(LC_CTYPE, _HEADERLANGUAGETAG);
setlocale(LC_TIME, _HEADERLANGUAGETAG);
//Define theme-related constants and setup the default theme
setupThemes();
/**The smarty libraries -- must be below themes!*/
require_once $path."smarty/smarty_config.php";
//Assign the configuration variables to smarty
$smarty -> assign("T_CONFIGURATION", $configuration); //Assign global configuration values to smarty
//Initialize languages and notify smarty on weather we have an RTL language
$languages = EfrontSystem :: getLanguages();
!$languages[$setLanguage]['rtl'] OR $smarty -> assign("T_RTL", 1);
//Instantiate current theme
try {
    $currentTheme = new themes(G_CURRENTTHEME);
    $smarty -> assign("T_THEME_SETTINGS", $currentTheme);
    try {
        $logoFile = new EfrontFile($configuration['logo']);
        $smarty -> assign("T_LOGO", 'images/logo/'.$logoFile['physical_name']);
    } catch (EfrontFileException $e) {
        $logoFile = new EfrontFile($currentTheme -> options['logo']);
        $smarty -> assign("T_LOGO", 'images/'.$logoFile['physical_name']);
    }
} catch (EfrontFileException $e) {
    $smarty -> assign("T_LOGO", "images/logo.png");
}
try {
    try {
        $faviconFile = new EfrontFile($configuration['favicon']);
        $smarty -> assign("T_FAVICON", 'images/logo/'.$faviconFile['physical_name']);
    } catch (Exception $e) {
        $faviconFile = new EfrontFile($currentTheme -> options['favicon']);
        $smarty -> assign("T_FAVICON", 'images/'.$faviconFile['physical_name']);
    }
} catch (EfrontFileException $e) {
    $smarty -> assign("T_FAVICON", "images/favicon.png");
}
/**Initialize valid currencies

 * @todo: remove from here, move to a function or class*/
require_once $path."includes/currencies.php";
//Load filters if smarty is set
if (isset($smarty)) {
    //Convert normal images to css sprites
    $smarty -> load_filter('output', 'eF_template_applyImageMap');
    //Convert plain urls to theme-specific urls
    $smarty -> load_filter('output', 'eF_template_applyThemeToImages');
    //Format the timestamps according to system settings
    $smarty -> load_filter('output', 'eF_template_formatTimestamp');
    //Convert logins to personal-message enabled clickable links
    $smarty -> load_filter('output', 'eF_template_loginToMessageLink');
    //Format logins according to system settings
    $smarty -> load_filter('output', 'eF_template_formatLogins'); //Warning: To be put always after loginToMessageLink!
    //Format scores according to system settings
    $smarty -> load_filter('output', 'eF_template_formatScore');
    //Selectively include some javascripts based on whether they are actually needed
    $smarty -> load_filter('output', 'eF_template_includeScripts');
    $browser = detectBrowser();
    if ($browser == 'ie6') {
        define("MSIE_BROWSER", 1);
        $browser = 'IE6'; //For compatibility reasons, since it used to set it explicitly to IE6 or IE7
    } elseif ($browser == 'ie') {
        define("MSIE_BROWSER", 1);
        $browser = 'IE7';
    } else {
        define("MSIE_BROWSER", 0);
    }
    $smarty -> assign("T_BROWSER", $browser);
    $smarty -> assign("T_VERSION_TYPE", $GLOBALS['versionTypes'][G_VERSIONTYPE]);
    $smarty -> assign("T_DATE_FORMATGENERAL", eF_dateFormat(false));
}
// eFront social activation codes
define("SOCIAL_FUNC_EVENTS", 1);
define("SOCIAL_FUNC_SYSTEM_TIMELINES", 2);
define("SOCIAL_FUNC_LESSON_TIMELINES", 4);
define("SOCIAL_FUNC_PEOPLE", 8);
define("SOCIAL_FUNC_COMMENTS", 16);
define("SOCIAL_FUNC_USERSTATUS", 32);
define("FB_FUNC_DATA_ACQUISITION", 64);
define("FB_FUNC_LOGGING", 128);
define("FB_FUNC_CONNECT", 256);
//define("SOCIAL_FUNC_LESSON_PEOPLE", 64);
define("SOCIAL_MODULES_ALL", 9); // number of social module options
$HCDEMPLOYEECATEGORIES = array('wage','hired_on','left_on' ,'address' ,'city' ,'country' ,'father' ,'homephone','mobilephone','sex','birthday','birthplace' ,'birthcountry','mother_tongue' ,'nationality' ,'company_internal_phone' ,'office' ,'doy' ,'afm' ,'police_id_number' ,'driving_licence' ,'work_permission_data' ,'national_service_completed','employement_type' ,'bank' ,'bank_account','marital_status' , 'transport' , 'way_of_working');
$MODULE_HCD_EVENTS['HIRED'] = 1;
$MODULE_HCD_EVENTS['NEW'] = 2;
$MODULE_HCD_EVENTS['JOB'] = 3;
$MODULE_HCD_EVENTS['WAGE_CHANGE'] = 4;
$MODULE_HCD_EVENTS['SKILL'] = 5;
$MODULE_HCD_EVENTS['SEMINAR'] = 6;
$MODULE_HCD_EVENTS['FIRED'] = 7;
$MODULE_HCD_EVENTS['LEFT'] = 8;
/**

 * Setup version

 *

 * This function sets up the version, unlocking specific

 * functionality

 *

 * @since 3.6.0

 */
function setupVersion() {
 define("G_VERSIONTYPE_CODEBASE", 'community');
 //Set the specific version parameters
    $GLOBALS['versionTypes'] = array('educational' => 'Educational',
                          'enterprise' => 'Enterprise',
                          //'unregistered' => 'Unregistered',
                          'standard' => 'Community++',
                          'community' => 'Community');
    //If we have set a version, it is stored in the configuration file
    if (isset($GLOBALS['configuration']['version_type']) && in_array($GLOBALS['configuration']['version_type'], array_keys($GLOBALS['versionTypes']))) {
        define("G_VERSIONTYPE", $GLOBALS['configuration']['version_type']);
    }
    //If we haven't set a version, then it is the community edition
    else {
        define("G_VERSIONTYPE", 'community');
        //define("G_VERSIONTYPE", "community");
    }
}
/**

 * Setup constants

 *

 * This function serves only as a convenient bundle for

 * all the required defines that must be made during initialization

 *

 * @since 3.6.0

 */
function setDefines() {
    /*Get the build number*/
    preg_match("/(\d+)/", '$LastChangedRevision$', $matches);
    $build = 8128;
    defined("G_BUILD") OR define("G_BUILD", $build);
    /*Define default encoding to be utf-8*/
    mb_internal_encoding('utf-8');
    /** The full filesystem path of the lessons directory*/
    define("G_LESSONSPATH", G_ROOTPATH."www/content/lessons/");
    is_dir(G_LESSONSPATH) OR mkdir(G_LESSONSPATH, 0755);
    /** The full URL to the folder containing the lessons*/
    define("G_LESSONSLINK", G_SERVERNAME."content/lessons/");
    /** The relative path (URL) to the lessons folder*/
    define("G_RELATIVELESSONSLINK", "content/lessons/");
    /** The backup directory, must be outside the server root for security reasons, and must have proper permissions*/
    define("G_BACKUPPATH", G_ROOTPATH."backups/");
    is_dir(G_BACKUPPATH) OR mkdir(G_BACKUPPATH, 0755);
    /** The users upload directory*/
    define ("G_UPLOADPATH", G_ROOTPATH."upload/");
    is_dir(G_UPLOADPATH) OR mkdir(G_UPLOADPATH, 0755);
    /** The modules path */
    define("G_MODULESPATH", G_ROOTPATH."www/modules/");
    is_dir(G_MODULESPATH) OR mkdir(G_MODULESPATH, 0755);
    /** The modules url */
    define("G_MODULESURL", G_SERVERNAME."modules/");
    //If G_DBPREFIX is not defined, it should be set to the empty string
    defined('G_DBPREFIX') OR define('G_DBPREFIX', "");
    /**The salt used for password hashing*/
    define("G_MD5KEY", 'cDWQR#$Rcxsc');
    /** The themes path*/
    define("G_THEMESPATH", G_ROOTPATH."www/themes/");
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? $protocol = 'https' : $protocol = 'http';
    /** The protocol currently used*/
    define ("G_PROTOCOL", $protocol);
    /** @deprecated The relative path (URL) to the content folder*/
    define("G_RELATIVECONTENTLINK", "content/");
    /** @deprecated The relative path (URL) to the admin folder*/
    define("G_RELATIVEADMINLINK", G_SERVERNAME."content/admin/");
    /** @deprecated The full filesystem path of the admin directory*/
    define("G_ADMINPATH", G_ROOTPATH."www/content/admin/");
    is_dir(G_ADMINPATH) || mkdir(G_ADMINPATH, 0755);
    /** @deprecated The full filesystem path of the content directory*/
    define("G_CONTENTPATH", G_ROOTPATH."www/content/");
    /** @deprecated The directory where scorm files are uploaded*/
    define("G_SCORMPATH", G_LESSONSPATH."scorm_uploaded_files/");
    /** @deprecated The course certificate template paths*/
    define("G_CERTIFICATETEMPLATEPATH", G_ROOTPATH."www/certificate_templates/");
    /** @deprecated */
    define("_CHATROOMDOESNOTEXIST_ERROR", "-2");
    /** @deprecated */
    define("_CHATROOMISNOTENABLED_ERROR", "-3");
    /** @deprecated Maximum file size (in bytes). Attention! it must be: memory_limit > post_max_size > upload_max_filesize > G_MAXFILESIZE*/
    define("G_MAXFILESIZE", 3000000);
    /** @deprecated Maximum number of messages held in the system **/
    define("G_QUOTA_NUM_OF_MESSAGES", 2000);
    /** @deprecated Maximum quota of messages in KB: 100MB **/
    define("G_QUOTA_KB", 102400);
    /** @deprecated*/
    define("G_DEFAULT_TABLE_SIZE", "20"); //Default table size for sorted table
}
/**

 * Setup themes

 *

 * This function sets up all the required constants and initiates objects

 * accordingly, to initialize the current theme

 *

 * @since 3.6.0

 */
function setupThemes() {
    /** The default theme path*/
    define("G_DEFAULTTHEMEPATH", G_THEMESPATH."default/");
    /** The default theme url*/
    define("G_DEFAULTTHEMEURL", "themes/default/");
    try {
        $currentTheme = new themes($GLOBALS['configuration']['theme']);
    } catch (Exception $e) {
        try {
            $result = eF_getTableData("themes", "*", "name = 'default'");
            if (sizeof($result) == 0) {
                throw new Exception(); //To be caught right below. This way, the catch() code gets executed either if the result is empty or if there is a db error
            }
        } catch (Exception $e) {
            $file = new EfrontFile(G_DEFAULTTHEMEPATH."theme.xml");
            themes :: create(themes :: parseFile($file));
        }
        $currentTheme = new themes('default');
    }
    $allThemes = themes :: getAll();
    $browser = detectBrowser();
    foreach ($allThemes as $value) {
        if (isset($value['options']['browsers'][$browser])) {
            try {
                $browserTheme = new themes($value['id']);
                $currentTheme = $browserTheme;
            } catch (Exception $e) {}
        }
    }
    if (isset($_GET['preview_theme'])) {
        try {
            $currentTheme = new themes($_GET['preview_theme']);
        } catch (Exception $e) {}
    }
//pr($currentTheme);
    $currentThemeName = $currentTheme -> {$currentTheme -> entity}['name'];
    /**The current theme*/
    define("G_CURRENTTHEME", $currentThemeName);
    /** The current theme path*/
    define("G_CURRENTTHEMEPATH", !isset($currentTheme -> remote) || !$currentTheme -> remote ? G_THEMESPATH.$currentTheme -> {$currentTheme -> entity}['path'] : $currentTheme -> {$currentTheme -> entity}['path']);
    /** The current theme url*/
    define("G_CURRENTTHEMEURL", !isset($currentTheme -> remote) || !$currentTheme -> remote ? "themes/".$currentTheme ->themes['path'] : $currentTheme -> {$currentTheme -> entity}['path']);
    /** The external pages path*/
    define("G_EXTERNALPATH", rtrim(G_CURRENTTHEMEPATH, '/')."/external/");
    is_dir(G_EXTERNALPATH) OR mkdir(G_EXTERNALPATH, 0755);
    /** The external pages link*/
    define("G_EXTERNALURL", rtrim(G_CURRENTTHEMEURL, '/')."/external/");
    if ($fp = fopen(G_CURRENTTHEMEPATH."css/css_global.css", 'r')) {
        /** The current theme's css*/
        define("G_CURRENTTHEMECSS", G_CURRENTTHEMEURL."css/css_global.css?build=".G_BUILD);
        fclose($fp);
    } else {
        /** The current theme's css*/
        define("G_CURRENTTHEMECSS", G_DEFAULTTHEMEURL."css/css_global.css?build=".G_BUILD);
    }
    /** The folder where the template compiled and cached files are kept*/
    define("G_THEMECACHE", G_ROOTPATH."libraries/smarty/themes_cache/");
    /** The folder of the current theme's compiled files*/
    define("G_CURRENTTHEMECACHE", G_THEMECACHE.$currentThemeName."/");
    /** The full filesystem path of the images directory*/
    define("G_IMAGESPATH", G_CURRENTTHEMEPATH."images/");
    /** The full filesystem path of the images directory, in the default theme*/
    define("G_DEFAULTIMAGESPATH", G_DEFAULTTHEMEPATH."images/");
    /** The users' avatars directory*/
    define("G_AVATARSPATH", G_IMAGESPATH."avatars/");
    if (is_dir(G_AVATARSPATH."system_avatars/")) {
        /*system avatars path*/
        define("G_SYSTEMAVATARSPATH", G_AVATARSPATH."system_avatars/");
        /*system avatars URL*/
        define("G_SYSTEMAVATARSURL", G_CURRENTTHEMEURL."images/avatars/system_avatars/");
    } else {
        /*system avatars path*/
        define("G_SYSTEMAVATARSPATH", G_DEFAULTTHEMEPATH."images/avatars/system_avatars/");
        /*system avatars URL*/
        define("G_SYSTEMAVATARSURL", G_DEFAULTTHEMEURL."images/avatars/system_avatars/");
    }
    /** The logo path*/
    define("G_LOGOPATH", G_DEFAULTIMAGESPATH."logo/");
}
/**

 * Default exception handler

 *

 * This function serves as the default exception handler,

 * called automatically when an exception is not caught.

 * The default behaviour is set to display the exception's

 * error message in a message box, at the index page.

 *

 * @param $e The uncaught exception

 * @since 3.5.4

 */
function defaultExceptionHandler($e) {
    //@todo: Database exceptions are not caught if thrown before smarty
    $tplFile = str_replace(".php", ".tpl", basename($_SERVER['PHP_SELF']));
    is_file($GLOBALS['smarty'] -> template_dir.$tplFile) ? $displayTpl = $tplFile : $displayTpl = 'index.tpl';
    if ($GLOBALS['smarty']) {
     $GLOBALS['smarty'] -> assign("T_MESSAGE", $e -> getMessage().' ('.$e -> getCode().')');
     $GLOBALS['smarty'] -> display($displayTpl);
    } else {
        echo EfrontSystem :: printErrorMessage($e -> getMessage().' ('.$e -> getCode().')');
    }
}
/**

 * Shutdown function

 * This function gets executed whenever the script ends, normally or unexpectedly.

 * We implement this in order to catch fatal errors (E_ERROR) level and display

 * an appropriate message

 *

 * @since 3.6.6

 */
function shutDownFunction() {
 if (function_exists('error_get_last')) {
  $error = error_get_last();
  if ($error['type'] == 1) {
   echo EfrontSystem :: printErrorMessage($error['message'].' in '.$error['file'].' line '.$error['line']);
  }
 }
}
/**

 * This function sets the REQUEST_URI in the $_SERVER variable,

 * which may not be set when using IIS

 *

 * @since 3.5

 */
function setRequestURI() {
    //Sets $_SERVER['REQUEST_URI'] for IIS
    if (!isset($_SERVER['REQUEST_URI']) || !$_SERVER['REQUEST_URI']) {
        if (!($_SERVER['REQUEST_URI'] = @$_SERVER['PHP_SELF'])) {
            $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
        }
        if (isset( $_SERVER['QUERY_STRING'])) {
            $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
        }
    }
}
function handleSEO() {
    if (!$GLOBALS['configuration']['seo'] && $_SERVER['PATH_INFO']) {
        $parts = explode("/", trim($_SERVER['PATH_INFO'], "/"));
        for ($i = 0; $i < sizeof($parts); $i+=2) {
            eval('$'.$parts[$i].' = "'.$parts[$i+1].'";');
        }
        //unset($parts);unset($i);
        foreach (get_defined_vars() as $key => $value) {
            $_GET[$key] = $value;
        }
    }
}
/**

 * Autoload files

 *

 * This function includes files on-demand, based on the class name that we tried to access

 *

 * @param string $className the name of the class requested

 * @since 3.5.4

 */
function __autoload($className) {
    $className = strtolower($className);
    if (strpos($className, "efrontmodule") !== false) {
        require_once("module.class.php");
    } else if (strpos($className, "quickform") !== false) {
        require_once("HTML/QuickForm.php");
        require_once("HTML/QuickForm/Renderer/ArraySmarty.php");
    } else if (strpos($className, "mail") !== false) {
        require_once("Mail.php");
        require_once("Mail/mime.php");
    } else if (strpos($className, "efrontsystem") !== false) {
        require_once("system.class.php");
    } else if (strpos($className, "efrontproject") !== false) {
        require_once("project.class.php");
    } else if (strpos($className, "efrontstats") !== false) {
        require_once("statistics.class.php");
    } else if (strpos($className, "efrontsearch") !== false) {
        require_once("search.class.php");
    } else if (strpos($className, "efrontcourse") !== false) {
        require_once("course.class.php");
    } else if (strpos($className, "efrontdirection") !== false) {
        require_once("direction.class.php");
    } else if (strpos($className, "efrontgroup") !== false) {
        require_once("group.class.php");
    } else if (strpos($className, "efrontmanifest") !== false) {
        require_once("manifest.class.php");
    } else if (strpos($className, "ef_personalmessage") !== false) {
        require_once("PersonalMessage.class.php");
    } else if (strpos($className, "efrontconfiguration") !== false) {
        require_once("configuration.class.php");
    } else if (strpos($className, "cache") !== false) {
        require_once("cache.class.php");
    } else if (strpos($className, "efrontmenu") !== false) {
        require_once("menu.class.php");
    } else if (strpos($className, "efrontimport") !== false ||
               strpos($className, "efrontimportcsv") !== false) {
        require_once("import_export.class.php");
    } else if (strpos($className, "tcpdf") !== false) {
        require_once("external/tcpdf/tcpdf.php");
    } else if (strpos($className, "efrontcontenttreescorm") !== false || strpos($className, "navigation") !== false) {
    } else if (strpos($className, "efrontfile") !== false ||
               strpos($className, "efrontdirectory") !== false ||
               strpos($className, "filesystemtree") !== false ||
               strpos($className, "efrontrefilter") !== false ||
               strpos($className, "efrontdbonly") !== false) {
        require_once("filesystem.class.php");
    } else if (strpos($className, "efrontcontent") !== false ||
               strpos($className, "efrontunit") !== false ||
               strpos($className, "content") !== false ||
               strpos($className, "efrontvisitable") !== false ||
               strpos($className, "efrontscormfilter")!== false ||
               strpos($className, "efrontnoscorm") !== false ||
               strpos($className, "efronttests") !== false ||
               strpos($className, "efronttheory") !== false ||
               strpos($className, "efrontexample") !== false ||
               strpos($className, "efrontremovedata") !== false ||
               strpos($className, "efrontinarray") !== false) {
        require_once("content.class.php");
    } else if (strpos($className, "efrontuser") !== false ||
               strpos($className, "efrontadministrator") !== false ||
               strpos($className, "efrontprofessor") !== false ||
               strpos($className, "efrontstudent") !== false ||
               strpos($className, "efrontlessonuser") !== false) {
        require_once("user.class.php");
    } else if (strpos($className, "efrontinformation") !== false ||
               strpos($className, "dublincoremetadata") !== false ||
               strpos($className, "learningobjectinformation") !== false) {
        require_once("metadata.class.php");
    } else if (strpos($className, "efronttree") !== false ||
               strpos($className, "efrontattributesonly") !== false ||
               strpos($className, "efrontattribute") !== false ||
               strpos($className, "efrontnode") !== false) {
        require_once("tree.class.php");
    } else if (strpos($className, "efronttest") !== false ||
               strpos($className, "efrontcompletedtest") !== false ||
               strpos($className, "question") !== false ||
               strpos($className, "testfilter") !== false) {
        require_once("test.class.php");
    } else if (strpos($className, "efrontscorm") !== false) {
        require_once("scorm.class.php");
 } else if (strpos($className, "efrontims") !== false) {
        require_once("ims.class.php");
    } else if (strpos($className, "efrontlesson") !== false) {
        require_once("lesson.class.php");
        require_once("deprecated.php");
    } else if (strpos($className, "smarty") !== false) {
        require_once "smarty/libs/Smarty.class.php";
    } else if (strpos($className, "efrontbenchmark") !== false) {
        require_once "benchmark.class.php";
    } else if (strpos($className, "efrontform") !== false) {
        require_once "form.class.php";
    } else if (strpos($className, "event") !== false) {
        /** Events class */
        require_once "events.class.php";
    } else if (strpos($className, "notification") !== false) {
        /** Notifications class */
        require_once "notification.class.php";
    } else if (strpos($className, "payments") !== false || strpos($className, "cart") !== false) {
        /** Payments class */
        require_once "payments.class.php";
    } else if (strpos($className, "curriculums") !== false) {
        /**curriculums class*/
        require_once "curriculums.class.php";
    } else if (strpos($className, "coupons") !== false) {
        /**coupons class*/
        require_once "coupons.class.php";
    } else if (strpos($className, "news") !== false) {
        /**News (announcements) class*/
        require_once "news.class.php";
    } else if (strpos($className, "f_forums") !== false || strpos($className, "f_topics") !== false || strpos($className, "f_poll") !== false || strpos($className, "f_messages") !== false) {
        /**Forum class*/
        require_once "forum.class.php";
    } else if (strpos($className, "f_personal_messages") !== false) {
        /**Forum class*/
        require_once "messages.class.php";
    } else if (strpos($className, "themes") !== false) {
        /**Forum class*/
        require_once "themes.class.php";
    } else if (strpos($className, "comments") !== false) {
        /**Comments class*/
        require_once "comments.class.php";
    } else if (strpos($className, "bookmarks") !== false) {
        /**Comments class*/
        require_once "bookmarks.class.php";
    } else if (strpos($className, "glossary") !== false) {
        /**Glossary class*/
        require_once "glossary.class.php";
    } else if (strpos($className, "graph") !== false) {
     require_once "graph.class.php";
    } else if (strpos($className, "sso") !== false) {
        require_once "sso.class.php";
    } else if (strpos($className, "sumtotal") !== false) {
        require_once "versions/sso/sumtotal.class.php";
    } else if (strpos($className, "efrontfacebook") !== false) {
    }
}
?>
