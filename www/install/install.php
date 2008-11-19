<?php
error_reporting(E_ERROR);
//define("G_MAXIMUMQUERYSIZE", 1000000);                                    //Maximum query size is 1M. Lower it in case of query problems

//(int)ini_get("memory_limit") < 128 ? ini_set("memory_limit", "128M") : null;
ini_set("memory_limit", "-1");
ini_get("max_execution_time") < 600 ? ini_set("max_execution_time", "600") : null;
$path = "../../libraries/";
include_once "insert_languages.php";

ini_set('include_path', $path.'../PEAR/');
$languages = array("english","arabic","bulgarian","chinese_traditional","chinese_simplified","croatian","czech","danish","dutch","finnish","french","german","greek","hindi","italian","japanese","norwegian","polish","portuguese","romanian","russian","spanish","swedish");
foreach ($languages as $value){
	$languagesArray[$value] = $value;
}
foreach ($languages as $value){
	if ($value == "chinese_traditional" || $value == "chinese_simplified") {
		$languagesArrayLC[$value] = "chinese";
	} else {
		$languagesArrayLC[$value] = $value;
	}
}
if (!is_writable($path.'smarty/')) {
    echo "Directory <b>".realpath($path.'smarty/')."</b> must be writable by the server in order to continue";
    exit;
}

if (is_file($path."smarty/smarty_config.php") && is_file($path."language/lang-english.php.inc")) {                        //Check if smarty and language file exist, and halt program execution if it is not present
    /**The smarty libraries*/
    require_once $path."smarty/smarty_config.php";
    require_once $path."language/lang-english.php.inc";
} else {
    echo "Mandatory files not found!";
    exit;
}
isset($_GET['upgrade']) ? $upgrade = '&upgrade=1' : $upgrade = '';

$message = '';$message_type = '';                                       //Initialize message variables

if (isset($_GET['step']) && $_GET['step'] >= 2) {                       //This inclusions are made only after we have made sure that they will work; that is, they are made after step 1
    /** HTML_QuickForm Class*/
    require_once 'HTML/QuickForm.php';
    /** HTML_QuickForm Smarty renderer class*/
    require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
    /**ADODB database abstraction class*/
    require_once($path.'adodb/adodb.inc.php');
    /**ADODB exceptions class*/
    require_once($path.'adodb/adodb-exceptions.inc.php');
    /**Various tools*/
    require_once($path.'tools.php');

    $ADODB_CACHE_DIR = $path."adodb/cache";                             //Initialize ADODB
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
}

if (is_file($path.'configuration.php')) {
    $file_contents = file_get_contents($path.'configuration.php');
    preg_match('/define\("G_VERSION_NUM",\s"(.*)"\);/',   $file_contents, $version_num);
    if (isset($version_num[1])) {
        $version_num = $version_num[1];
    }
}
$d = dir ($path.'../backups/');                                         //Check if there is a failed installation folder left over
while (false !== ($entry = $d -> read())) {
    if (is_file($path.'../backups/'.$entry) && preg_match('/(.+)_\d{10}.zip/', $entry, $matches)) {
        $failed_upgrades[] = array('name' => $matches[1], 'date' => filemtime($path.'../backups/'.$entry), 'file' => $entry);
        $smarty -> assign("T_FAILED_UPGRADES", $failed_upgrades);
    } elseif (is_file($path.'../backups/'.$entry) && preg_match('/.zip/', $entry, $matches)) {
		$failed_upgrades_others[] = array('name' => $entry, 'date' => filemtime($path.'../backups/'.$entry), 'file' => $entry);
		$smarty -> assign("T_FAILED_UPGRADES_OTHERS", $failed_upgrades_others);
	}
}
$d -> close();

//Read the current version
$file_contents = file_get_contents('sample_config.php');
preg_match('/define\("G_VERSION_NUM",\s"(.*)"\);/', $file_contents, $matches);
isset($matches[1]) ? $smarty -> assign("T_VERSION", $matches[1]) : null;


/*
Restore data for unfinished upgrades.
During upgrade, an automatic backup is done. If an unexpected/unrecoverable error occurs (i.e. the user closes the browser)
the system might be in an error state. In this case, a message is displayed at the installation first page, where the user
may recover the previous data.
*/
if (isset($_GET['restore']) && is_file($path.'../backups/'.$_GET['restore'])) {
    /**Include database functions*/
    require_once($path."configuration.php");

    $file = new EfrontFile(G_BACKUPPATH.$_GET['restore']);
    EfrontSystem :: restore($file);

    $message = 'The restore process for '.$match[1].' database to previous version is complete and the configuration file was updated accordingly. You are strongly recommended to perform an upgrade whenever possible, since the system might have become unstable.';
} else if (isset($_GET['delete_backup']) && is_file($path.'../backups/'.$_GET['delete_backup']) && strpos(realpath($path.'../backups/'.$_GET['delete_backup']), realpath($path.'../backups/')) !== false) {
    unlink($_GET['delete_backup']);
    header("location:install.php?message=".urlencode('Backup data deleted successfully')."&message_type=success");
}

/*
The first step performs all the checks necessary to validate that efront may be installed correctly
There are 2 kinds of checks: For optional (recommended) settings, and for mandatory settings.
The former allow continuation of the installation process, while the latter may halt the installation,
if they are not met. The following checks are performed
- Software settings: Recomendation for installed software
- PHP Extensions: The Mandatory and Optional extensions
- PHP ini settings: Optional ini settings for optimal performance
- Permissions: The directories where eFront requires write access (Mandatory)
- PEAR packages: The PEAR packages that eFront relies upon are mandatory
- Local settings: Check for presence of language encodings (Optional)
*/
if (isset($_GET['step']) && $_GET['step'] == 1) {

    $php_version       = explode('.', phpversion());                    //Get PHP version
    $webserver         = explode(' ',$_SERVER['SERVER_SOFTWARE']);      //GET Server information from $_SERVER
    $webserver_type    = explode('/', $webserver[0]);                   //Extract server type from server information (e.g. "apache")
    $webserver_version = explode('.', $webserver_type[1]);              //Extract server version from server information (e.g. "2.2.4")

    $software['system'] = array('name'        => 'Platform',
                                'installed'   => $webserver[1] ? mb_substr($webserver[1], 1, -1) : 'Unknown',
                                'recommended' => 'Any',
                                'status'      => true,
                                'help'        => 'eFront may be installed on systems running Microsoft Windows and on most Unix and Linux systems');
    $software['PHP']    = array('name'        => 'PHP',
                                'installed'   => phpversion(),
                                'recommended' => '5.2.0 or newer',
                                'status'      => isset($php_version[0]) && $php_version[0] <= 4 ? false : true,
                                'help'        => 'eFront is designed using PHP version 5.2.0. Usage of PHP version < 5.1 is not recommended, butt will work. PHP 4 is not supported');
    $software['apache'] = array('name'        => 'Web server',
                                'installed'   => $webserver[0],
                                'recommended' => 'Apache 2.x or newer',
                                'status'      => !strcasecmp($webserver_type[0], 'apache') && $webserver_version[0] >= 2 ? true : false,
                                'help'        => 'eFront is tested and known to work well with Apache 2 web server');
/*
    $software['MySQL']  = array('name'        => 'MySQL DBMS',
                                'installed'   => print_r(mysql_connect()),
                                'recommended' => '4.1.x, 5.x or newer',
                                'status'      => false,
                                'help'        => 'eFront is designed for optimal performance with MySQL 5, but it will work with MySQL 4 as well. No other databases are supported.');
*/

    $smarty -> assign("T_SOFTWARE", $software);                         //Software variables are Optional

    $extensions   = get_loaded_extensions();

    $mandatory['mbstring'] = array('enabled' => in_array('mbstring', $extensions),
                                   'name'    => 'MultiByte (UTF) Support',
                                   'help'    => 'This extension is mandatory since the system is designed to use UTF-8 character settings. If using windows, make sure that php_mbstring.dll extension is loaded inside the php.ini file. If using linux, make sure PHP is compiled with Multibyte support');
    $mandatory['session']  = array('enabled' => in_array('session', $extensions),
                                   'name'    => 'Session Support',
                                   'help'    => 'Sessions are mandatory in order for the system to operate');
    $mandatory['iconv']    = array('enabled' => in_array('iconv', $extensions),
                                   'name'    => 'Iconv Functions',
                                   'help'    => 'Iconv extension is needed in order to perform localization conversions');
    $mandatory['pcre']     = array('enabled' => in_array('pcre', $extensions),
                                   'name'    => 'POSIX Regular expressions',
                                   'help'    => 'Regular expressions are used thoroughly throughout the system');
    $mandatory['mysql']    = array('enabled' => in_array('mysql', $extensions),
                                   'name'    => 'MySQL support',
                                   'help'    => 'The system requires MySQL support. If using windows, make sure that php_mysql.dll extension is loaded inside the php.ini file. If using linux, make sure PHP is compiled with MySQL support');
    $mandatory['zip']      = array('enabled' => in_array('zip', $extensions),
                                   'name'    => 'ZIP extension',
                                   'help'    => 'ZIP extension is needed in order to use built-in compression functions');

    $optional['ldap'] = array('enabled' => in_array('ldap', $extensions),
                              'name'    => 'LDAP functions',
                              'help'    => 'LDAP extension is needed in case LDAP interoperability is needed');
    $optional['gd']   = array('enabled' => in_array('gd', $extensions),
                              'name'    => 'GD Libraries functions',
                              'help'    => 'GD Libraries extension is needed for advanced image manipulation');

    $smarty -> assign("T_MANDATORY", $mandatory);
    $smarty -> assign("T_OPTIONAL", $optional);

    $ini_settings = ini_get_all();

    $settings['register_globals']    = array('value'       => $ini_settings['register_globals']['global_value'] ? 'ON' : 'OFF',
                                             'recommended' => 'OFF',
                                             'status'      => $ini_settings['register_globals']['global_value'] ? 0 : 1,
                                             'name'        => 'register_globals',
                                             'help'        => 'For security reasons, register_globals must be set to OFF');
    $settings['safe_mode']           = array('value'       => $ini_settings['safe_mode']['global_value'] ? 'ON' : 'OFF',
                                             'recommended' => 'OFF',
                                             'status'      => $ini_settings['safe_mode']['global_value'] ? 0 : 1,
                                             'name'        => 'safe_mode',
                                             'help'        => 'safe_mode should be set to OFF in order for the platform to work correctly');
    $settings['file_uploads']        = array('value'       => $ini_settings['file_uploads']['global_value'] ? 'ON' : 'OFF',
                                             'recommended' => 'ON',
                                             'status'      => $ini_settings['file_uploads']['global_value'] ? 1 : 0,
                                             'name'        => 'file_uploads',
                                             'help'        => 'File uploads should be turned on');
    $settings['upload_max_filesize'] = array('value'       => $ini_settings['upload_max_filesize']['global_value'],
                                             'recommended' => '1M - 100M',
                                             'status'      => mb_substr($ini_settings['upload_max_filesize']['global_value'], 0, -1) >= 1 && mb_substr($ini_settings['upload_max_filesize']['global_value'], 0, -1) <= 100 ? 1 : 0,
                                             'name'        => 'upload_max_filesize',
                                             'help'        => 'Uploading maximum file size can be set to the most suitable value');
    $settings['post_max_size']       = array('value'       => $ini_settings['post_max_size']['global_value'],
                                             'recommended' => 'same or larger than upload_max_file_size',
                                             'status'      => $ini_settings['post_max_size']['global_value'] >= $ini_settings['upload_max_filesize']['global_value'] ? 1 : 0,
                                             'name'        => 'post_max_size',
                                             'help'        => 'Post_max_size should be set at least equal to upload_max_filesize');
    $settings['max_execution_time']  = array('value'       => $ini_settings['max_execution_time']['global_value'],
                                             'recommended' => '>120',
                                             'status'      => $ini_settings['max_execution_time']['global_value'] >= 120 ? 1 : 0,
                                             'name'        => 'max_execution_time',
                                             'help'        => 'Maximum script execution time can be set to the most suitable value');
    $settings['memory_limit']        = array('value'       => $ini_settings['memory_limit']['global_value'],
                                             'recommended' => '>32M',
                                             'status'      => mb_substr($ini_settings['memory_limit']['global_value'], 0, -1) >= 32 ? 1 : 0,
                                             'name'        => 'memory_limit',
                                             'help'        => 'Memory limit must be set to a high value, at least 32MB, in order for efront to run');
    $settings['zlib.output_handler'] = array('value'       => $ini_settings['zlib.output_handler']['global_value'],
                                             'recommended' => 'Off',
                                             'status'      => $ini_settings['zlib.output_handler']['global_value'] ? 0 : 1,
                                             'name'        => 'zlib.output_handler',
                                             'help'        => 'Zlib output handler must not be enabled in order to transparently compress files at run-time');
    $settings['zlib.output_compression'] = array('value'   => $ini_settings['zlib.output_compression']['global_value'],
                                             'recommended' => 'Off',
                                             'status'      => $ini_settings['zlib.output_compression']['global_value'] ? 0 : 1,
                                             'name'        => 'zlib.output_compression',
                                             'help'        => 'Zlib output compression must not be enabled in order to transparently compress files at run-time');

    $smarty -> assign("T_SETTINGS", $settings);


    $permissions['www/content']                 = array('writable' => is_writable($path.'../www/content') && is_writable($path.'../www/content/lessons') && is_writable($path.'../www/content/admin'),
                                                        'help'     => 'This is the directory where the lesson content resides, and should be writable along with any subfolders');
    $permissions['www/css']                     = array('writable' => is_writable($path.'../www/css'),
                                                        'help'     => 'This directory is where custom CSS stylesheets are uploaded');
    $permissions['www/images/avatars']          = array('writable' => is_writable($path.'../www/images/avatars'),
                                                        'help'     => 'This is the directory where the user avatars are created');
    $permissions['www/images/logo']             = array('writable' => is_writable($path.'../www/images/logo'),
                                                        'help'     => 'This is the directory where custom site logos are uploaded');
    $permissions['www/modules']                 = array('writable' => is_writable($path.'../www/modules'),
                                                        'help'     => 'This is the directory where the modules are installed');
    $permissions['libraries']                   = array('writable' => is_writable($path),
                                                        'help'     => 'libraries directory should be writable only during the installation process');
    $permissions['libaries/language']           = array('writable' => is_writable($path.'language'),
                                                        'help'     => 'This directory needs to writable, in order to be able to upload new language files or modify existing ones');
    $permissions['libaries/smarty/templates_c'] = array('writable' => is_writable($path.'smarty/templates_c'),
                                                        'help'     => 'This is the template caching directory');
    $permissions['libaries/smarty/cache']       = array('writable' => is_writable($path.'smarty/cache'),
                                                        'help'     => 'This is the template caching directory');
    $permissions['backups']                     = array('writable' => is_writable($path.'../backups'),
                                                        'help'     => 'In this directory all the system backups are stored');
    $permissions['upload']                      = array('writable' => is_writable($path.'../upload'),
                                                        'help'     => 'This is the directory where user related files are stored');

    $smarty -> assign("T_PERMISSIONS", $permissions);

    $pear['PEAR.php']                                = array('exists' => ($f = fopen ('PEAR.php', 'r', true))                                ? true : false,
                                                             'help'   => 'PEAR libraries are mandatory in order for the system to function');
    $pear['HTML/QuickForm.php']                      = array('exists' => ($f = fopen ('HTML/QuickForm.php', 'r', true))                      ? true : false,
                                                             'help'   => 'This PEAR package is mandatory and the system will not work without it');
    $pear['HTML/QuickForm/Renderer/ArraySmarty.php'] = array('exists' => ($f = fopen ('HTML/QuickForm/Renderer/ArraySmarty.php', 'r', true)) ? true : false,
                                                             'help'   => 'This PEAR package is mandatory and the system will not work without it');
    $pear['Mail.php']                                = array('exists' => ($f = fopen ('Mail.php', 'r', true)) ? true : false,
                                                             'help'   => 'This PEAR Mail package is is needed in order for the system to be able to send emails');
    $pear['Net/SMTP.php']                            = array('exists' => ($f = fopen ('Net/SMTP.php', 'r', true)) ? true : false,
                                                             'help'   => 'This PEAR Net_SMTP package is is needed in order for the system to be able to send emails');
    $pear['Net/Socket.php']                          = array('exists' => ($f = fopen ('Net/Socket.php', 'r', true)) ? true : false,
                                                             'help'   => 'This PEAR Net_Socket package is is needed in order for the system to be able to send emails');
    fclose($f);

    $smarty -> assign("T_PEAR", $pear);

//    $languages = array_values(array_filter(scandir($path.'language/'), create_function('$a', 'return strpos($a, "lang") !== false;')));     //Get the language files that reside inside the 'language' directory

	foreach ($languagesArrayLC as $key => $value) {
		$locale[$key]   = array('language' => $key,                               //To see the system installed locales, type locale-a in command prompt (linux/unix).
								'locale'   => (setlocale(LC_ALL, $value)),
								'help'     => (setlocale(LC_ALL, $value) === false) ? 'You should change _HEADERLANGUAGETAG in '.$key.' language file after installation' : 'Your system supports '.$key.' language');
	}
	
 /*   $greek_tags = array('greek', 'el_EL', 'el_el', 'Greek', 'gr_gr', 'el_gr', 'el-el', 'el-EL', 'el-gr', 'gr-gr', 'en_US.utf8');
    while (!setlocale(LC_ALL, $tag = $greek_tags[0])) {
        array_shift($greek_tags);
    }
    $locale['greek']   = array('language' => 'greek',                               //To see the system installed locales, type locale-a in command prompt (linux/unix).
                               'locale'   => sizeof($greek_tags) > 0 ? (setlocale(LC_ALL, $tag)) : '',
                               'help'     => 'Your system should support Greek language in order to use Greek language in the system');
    if ($tag) {
        $file = file_get_contents($path."language/lang-greek.php");
        $file = preg_replace("/(define\(\"_HEADERLANGUAGETAG\",\").*(\"\);)/", '$1'.$tag.'$2', $file);
        file_put_contents($path."language/lang-greek.php", $file);
    } 

    $english_tags = array('english', 'en_US', 'en_us', 'English', 'en_EN', 'en_en', 'en-us', 'en-US', 'en-en', 'en-EN', 'en_US.utf8');

    while (!setlocale(LC_ALL, $tag = $english_tags[0])) {
        array_shift($english_tags);
    }
    $locale['english'] = array('language' => 'english',
                               'locale'   => sizeof($english_tags) > 0 ? (setlocale(LC_ALL, $tag)) : '',
                               'help'     => 'Your system should support English language in order to use the system');
   /* if ($tag) {
        $file = file_get_contents($path."language/lang-english.php");
        $file = preg_replace("/(define\(\"_HEADERLANGUAGETAG\",\").*(\"\);)/", '$1'.$tag.'$2', $file);
        file_put_contents($path."language/lang-english.php", $file);
    } */


    $smarty -> assign("T_LOCALE", $locale);

    $install = true;                                            //The install variable will be used to check whether any mandatory setting is not met.
    foreach ($mandatory as $key => $value) {                    //Check mandatory PHP extensions
        if (!$value['enabled']) {
            $install = false;
        }
    }
    foreach ($permissions as $key => $value) {                  //Check filesystem permissions
        if (!$value['writable']) {
            $install = false;
        }
    }
    foreach ($pear as $key => $value) {                         //Check PEAR packages
        if (!$value['exists']) {
            $install = false;
        }
    }
    if ($php_version[0] <= 4) {                                 //PHP 4 will not run
        $install = false;
    }
    if (!$settings['memory_limit']['status']) {                 //Memory size must be above a specific threshold
        $install = false;
    }

    if (!$upgrade && !$migrate) {
        $smarty -> assign("T_CONFIGURATION_EXISTS", is_file($path.'configuration.php'));

        require_once($path.'tree.class.php');
        require_once($path.'filesystem3.class.php');

        if (sizeof($lesson_folders) > 0 || sizeof($user_folders) > 0) {
            $smarty -> assign("T_NON_EMPTY_FOLDERS", true);
        }
    }
    $smarty -> assign("T_INSTALL", $install);

    //Check if we are installing over an existing installation and display appropriate message


}

/*
The second step has to do with database settings and creation. The user inserts database parameters,
checks if they are correct and then creates the database itself. since we got here from the previous step,
we can be sure that all mandatory elements are present, so we may use them (for example adodb, quickform etc)
There are 3 different cases in this step. The first one concerns the clean installation, and the user
only specifies the database connection settings. The second case is when the user is performin an upgrade.
In this case, the database connection settings are read from the existing configuration file, and the
Old database is deleted. The third case is when the user is migrating. In this case, he fills in both
the existing database connection settings, as well as the new database connection settings. Yhe new database
is created and then all values from the old database are copied to the new one.
*/
else if (isset($_GET['step']) && $_GET['step'] == 2) {

    $form = new HTML_QuickForm("step2_form", "post", "install/install.php?step=2".$upgrade, "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter

    if (isset($_GET['upgrade'])) {
        $form -> addElement('select', 'db_type', null, array('mysql' => 'MySQL'));
        $form -> addRule('db_type', 'The database type is mandatory', 'required', null, 'client');                //The database type can only be string and is mandatory
        $form -> addRule('db_type', 'Invalid database type', 'checkParameter', 'string');                //The database type can only be string and is mandatory

        $form -> addElement('text', 'db_host', null, 'class = "inputText"');
        $form -> addRule('db_host', 'The database host is mandatory', 'required', null, 'client');                //The database type can only be string and is mandatory
        $form -> addRule('db_host', 'Invalid database host', 'checkParameter', 'alnum_general');         //The database host can only be string and is mandatory

        $form -> addElement('text', 'db_user', null, 'class = "inputText"');
        $form -> addRule('db_user', 'The database user is mandatory', 'required', null, 'client');                //The database type can only be string and is mandatory
        $form -> addRule('db_user', 'Invalid database user', 'checkParameter', 'alnum_general');                //The database user can only be string

        $form -> addElement('password', 'db_password', null, 'class = "inputText"');

        $form -> addElement('text', 'db_name', null, 'class = "inputText"');
        $form -> addRule('db_name', 'The database name is mandatory', 'required', null, 'client');                //The database type can only be string and is mandatory
        $form -> addRule('db_name', 'Invalid database name', 'checkParameter', 'alnum_general');        //The database name can only be string

        $form -> addElement('text', 'new_db_name', null, 'class = "inputText"');
        $form -> addRule('new_db_name', 'Invalid database name', 'checkParameter', 'alnum_general');        //The database name can only be string

        $form -> addElement('submit', 'check_database', 'Check Database settings', 'class = "flatButton"');
        $form -> addElement('submit', 'create_database', 'Create database', 'class = "flatButton"');
        $form -> addElement('submit', 'create_tables', 'Create database tables', 'class = "flatButton"');
        $form -> addElement('submit', 'rollback', 'Delete database and retry', 'class = "flatButton" onclick = "return confirm(\'This way all existing data in the new database will be lost. Are you sure?\')"');
        $form -> addElement('submit', 'step2_submit', 'Continue to next step', 'class = "flatButton"');

        if (is_file($path.'configuration.php')) {
            $file_contents = file_get_contents($path.'configuration.php');                            //Load existing configuration file

            preg_match('/define\("G_DBHOST", "(.*)"\);/',   $file_contents, $host);
            preg_match('/define\("G_DBUSER", "(.*)"\);/',   $file_contents, $user);
            preg_match('/define\("G_DBPASSWD", "(.*)"\);/', $file_contents, $password);
            preg_match('/define\("G_DBNAME", "(.*)"\);/',   $file_contents, $name);
            $form -> setDefaults(array('db_host'     => $host[1],
                                       'db_user'     => $user[1],
                                       'db_password' => $password[1],
                                       'db_name'     => $name[1]));
        }

        /*
        Perform the DBMS functions. Below, when we refer to "DBMS" we mean the software (e.g. MySQL)
        and by saying "database" we mean the specific database, e.g. "efront"
        */
        if ($form -> isSubmitted() && $form -> validate()) {
            $values = $form -> exportValues();
            $db     = ADONewConnection($form -> exportValue('db_type'));                                    //Set Connection parameter to "mysql"

            if (isset($values['create_tables']) || isset($values['rollback'])) {
                if (isset($values['rollback'])) {
                    try {
                        if ($values['new_db_name'] && $values['new_db_name'] != $values['db_name']) {
                            $db -> NConnect($values['db_host'], $values['db_user'], $values['db_password']);
                            $db -> Execute("drop database ".$values['new_db_name']);
                        }
                    } catch (Exception $e) {
                        $message      = $e -> msg."<br/>";
                        $message_type = 'failure';
                    }
                }

                showProgressBar();    //Script for displaying progress bar

                try {
                    if (($values['new_db_name']) && $values['new_db_name'] != $values['db_name']) {
                        $db -> NConnect($values['db_host'], $values['db_user'], $values['db_password']);
                        $db -> Execute("SET NAMES 'UTF8'");
                        $db -> Execute("create database ".$values['new_db_name']." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");                          //Create the new database
                    }

                    $db -> NConnect($values['db_host'], $values['db_user'], $values['db_password'], $values['db_name']);
                    $db -> Execute("SET NAMES 'UTF8'");

                    $file_contents          = file_get_contents('sample_config.php');                    //Load sample configuration file
                    $existing_file_contents = file_get_contents($path.'configuration.php');              //Load existing configuration file

                    preg_match('/define\("G_SERVERNAME", "(.*)"\);/', $existing_file_contents, $servername);        //Get the server name, port and root path from the existing configuration
                    preg_match('/define\("G_SERVERPORT", "(.*)"\);/', $existing_file_contents, $serverport);
                    preg_match('/define\("G_ROOTPATH", "(.*)"\);/',   $existing_file_contents, $rootpath);

                    $patterns = array('/(define\("G_DBTYPE", ").*("\);)/',
                                      '/(define\("G_DBHOST", ").*("\);)/',
                                      '/(define\("G_DBUSER", ").*("\);)/',
                                      '/(define\("G_DBPASSWD", ").*("\);)/',
                                      '/(define\("G_DBNAME", ").*("\);)/',
                                      '/(define\("G_SERVERNAME", ").*("\);)/',
                                      '/(define\("G_SERVERPORT", ").*("\);)/',
                                      '/(define\("G_ROOTPATH", ").*("\);)/');

                    $ending = mb_substr($servername[1], -2);
					if (strcmp($ending,"//") ==0) {
						$servername[1] = mb_substr($servername[1], 0, -2)."/";
					} 	
					$replacements = array('${1}'.$values['db_type'].'${2}',
                                          '${1}'.$values['db_host'].'${2}',
                                          '${1}'.$values['db_user'].'${2}',
                                          '${1}'.$values['db_password'].'${2}',
                                          '${1}'.($values['new_db_name'] ? $values['new_db_name'] : $values['db_name']).'${2}',
                                          '${1}'.$servername[1].'${2}',
                                          '${1}'.$serverport[1].'${2}',
                                          '${1}'.$rootpath[1].'${2}');

                    $new_file_contents = preg_replace($patterns, $replacements, $file_contents, -1, $count);    //Replace sample settings with current settings

                    file_put_contents($path.'configuration_temp.php', $new_file_contents);                      //Create a temporary configuration file, which will be used throughout the rest of the installation

                    //Get all the database tables, except for the temporary installation tables
                    $result = $db -> Execute("show table status");                                              //Get the database tables
                    while (!$result->EOF) {
                        if (strpos($result -> fields['Name'], 'install_') !== 0) {
                            $tables[] = $result -> fields['Name'];
                        }
                        $result -> MoveNext();
                    }

                    advanceProgressBar(1, 'Initializing');

                    /**Include the new configuration*/
                    require_once($path."configuration_temp.php");

                    /**Include all other functions*/
                    require_once($path."database.php");
                    if ($values['new_db_name']) {
                        $db -> NConnect($values['db_host'], $values['db_user'], $values['db_password'], $values['db_name']); //Connect to the old database, because inclusion of database.php connected us to the new database
                    }
                    $db -> Execute("SET NAMES 'UTF8'");
                    /**Include all other functions*/
                    require_once($path."globals.php");
                    ini_set("memory_limit", "-1");

                    advanceProgressBar(1, 'Backing up database');
                    $backupFile = EfrontSystem :: backup($values['db_name'].'_'.time().'.zip');    //Auto backup database

                    //Convert deprecated "system_announcements" table to news table
                    if (($key = array_search('system_announcements', $tables)) !== false) {
                        $result = eF_getTableData("system_announcements");
                        foreach ($result as $value) {
                            unset($value['id']);
                            $value['lessons_ID'] = 0;
                            eF_insertTableData("news", $value);
                        }

                        unset ($tables[$key]);
                        $tables = array_values($tables);    //reindex tables so that indices are 0..n
                    }

                    advanceProgressBar(1, 'Creating temporary database tables');

                    foreach ($tables as $table) {
                        if (!$values['new_db_name']) {
                            try {
                                $result = $db -> Execute("drop table install_$table");            //Delete temporary installation tables, if such exist
                            } catch (Exception $e) {}    //If the table could not be deleted, it doesn't exist
                        }
                    }

                    $file_contents = trim(file_get_contents("sql_".$values['db_type'].".txt"));         //Get the sql queries text, trimmed
                    $file_contents = explode(';', $file_contents);                                      //Form the sql queries, by splitting each CREATE statement
                    if (!end($file_contents)) {
                        array_pop($file_contents);                                                      //Remove last element, if it is an empty array (which is usually the case)
                    }

                    //Convert old files to new file system
                    $result = eF_getTableData("files", "count(*)");
                    if (sizeof($result[0]['count(*)']) == 0) {
                        $must_update_files = true;                                       //This switch notifies the system that filesystem needs upgrading. It will be used later.
                    }

                    if ($values['new_db_name']) {
                        $db -> NConnect($values['db_host'], $values['db_user'], $values['db_password'], $values['new_db_name']);
                        $db -> Execute("SET NAMES 'UTF8'");
                    }

                    unset($tables);                                                    //This way modules and any custom tables are leaved untouched
                    foreach ($file_contents as $query) {                                                //Run queries
                        preg_match('/CREATE TABLE (\w+) .*/', $query, $matches);
                        $queryCreate = preg_replace('/(.*CREATE TABLE )(\w+)( .*)/', '$1 if not exists $2 $3', $query);
                        $table = $matches[1];
                        if (!$values['new_db_name']) {
                            $queryCreateTemp = preg_replace('/(.*CREATE TABLE )(\w+)( .*)/', '$1 install_$2 $3', $query);   //Use temporary installation tables

                            $db -> Execute("drop table if exists install_$table");
                            $db -> Execute($queryCreateTemp);
                            $db -> Execute($queryCreate);
                        } else {
                            $db -> Execute($queryCreate);
                        }
                        $tables[] = $table;
                    }

                    //Special handling of modules table
                    if ($values['new_db_name']) {
                        $db -> NConnect($values['db_host'], $values['db_user'], $values['db_password'], $values['db_name']);
                        $db -> Execute("SET NAMES 'UTF8'");

                        $existingTables = $db -> GetCol("show tables");
                        $moduleTables  = array_diff($existingTables, $tables);
                        foreach ($moduleTables as $table) {
                            $result = $db -> execute("show create table ".$table);
                            $moduleTableQueries[$table] = $result -> getAll();
                            $tables[] = $table;
                        }

                        $db -> NConnect($values['db_host'], $values['db_user'], $values['db_password'], $values['new_db_name']);
                        $db -> Execute("SET NAMES 'UTF8'");

                        foreach ($moduleTableQueries as $query) {
                            $db -> execute($query[0]['Create Table']);
                        }
                    }

                    foreach ($tables as $table) {
                        advanceProgressBar(1, 'Upgrading table '.$table);

                        if (!$values['new_db_name']) {
                            updateDBTable($table, "install_".$table);
                        } else {
                            $oldDB = array('db_host' => $values['db_host'], 'db_user' => $values['db_user'], 'db_password' => $values['db_password'], 'db_name' => $values['db_name']);
                            $newDB = array('db_host' => $values['db_host'], 'db_user' => $values['db_user'], 'db_password' => $values['db_password'], 'db_name' => $values['new_db_name']);

                            updateDBTable($table, $table, $oldDB, $newDB);
                        }
                    }

                    advanceProgressBar(1, 'Setting up new tables');

                    if (!$values['new_db_name']) {
                        foreach ($tables as $table) {
                            $db -> Execute("drop table $table");
                            $db -> Execute("RENAME TABLE install_$table TO $table");
                        }
                    } else {
                        $GLOBALS['db'] -> NConnect($newDB['db_host'], $newDB['db_user'], $newDB['db_password'], $newDB['db_name']);
                    }

                    advanceProgressBar(1, 'Performing special upgrade tasks');

                    //Update Tests
                    EfrontTest :: upgrade($version_num);

					                                                         //Insert languages to corresponding table                                                                                    //If we are migrating, we read configuration values from the database
					addLanguagesDB();
					
						
					
                    //Create skills category if missing
                    if (isset($createSkillsCategory)) {
                        eF_insertTableData("module_hcd_skill_categories", array('id' => 1, 'description' => 'Default Category'));
                    }

                    $result = eF_getTableData("user_profile", "*");
                    for ($i = 0; $i < sizeof($result); $i++) {
                        $result[$i]['mandatory']     ? $mandatory = "NOT NULL"                                 : $mandatory = "NULL";
                        $result[$i]['default_value'] ? $default   = $result[$i]['default_value'] : $default   = false;
                        try {
                            $db -> Execute("ALTER TABLE users ADD ".$result[$i]['name']." varchar(255) ".$mandatory." DEFAULT '".$default."'");
                        } catch (Exception $e) {
                            $failed_updates[] = $e -> msg;
                        }
                    }

                    if (isset($failed_updates)) {
                        $message      .= implode(', ', $failed_updates)."<br/>";
                        $message_type = 'failure';
                        $errors       = true;
                    }
                    try {                                                                                   //Insert configuration values that are absent from previous versions of efront
                        eF_updateTableData("configuration", array("value" => 1), "value='yes'");            //Convert values from 'yes' and 'no' to 1 and 0
                        eF_updateTableData("configuration", array("value" => 0), "value='no'");                        
                    } catch (Exception $e) {
                        $message      .= $e -> msg."<br/>";
                        $message_type = 'failure';
                        $errors       = true;
                    }

                    if (version_compare($version_num, '3.5.1') <= 0) {
                        try {
                            //Add the 'simple' layout as default;
                            eF_insertTableData("configuration", array('index_positions' => 'a:4:{s:8:"leftList";a:0:{}s:10:"centerList";a:1:{i:0;s:5:"login";}s:9:"rightList";a:0:{}s:6:"layout";s:6:"simple";}'));	
                        } catch (Exception $e) {}                        
                    }                  
                    
                    if ($must_update_files) {                                                   //Convert specific old files to new format
                        $project_files = eF_getTableDataFlat("users_to_projects", "*");
                        foreach ($project_files['filename'] as $key => $file) {
                            if (is_file(G_ROOTPATH.'upload/'.$project_files['users_LOGIN'][$key].'/projects/'.$file)) {
                                $fields = array('path'          => G_ROOTPATH.'upload/'.$project_files['users_LOGIN'][$key].'/projects/'.$file,
                                                'users_LOGIN'   => $project_files['users_LOGIN'][$key],
                                                'timestamp'     => $project_files['upload_timestamp'][$key],
                                                'description'   => $file);
                                $file_id = eF_insertTableData("files", $fields);
                                eF_updateTableData("users_to_projects", array('filename' => $file_id), "users_LOGIN = '".$project_files['users_LOGIN'][$key]."' && projects_ID=".$project_files['projects_ID'][$key]);
                            }
                        }

                        $done_tests = eF_getTableDataFlat("users_to_done_tests", "*");
                        foreach ($done_tests['tests_ID'] as $key => $test_id) {
                            try {
                                $currentDir = new FileSystemTree(G_ROOTPATH.'upload/'.$done_tests['users_LOGIN'][$key].'/tests/'.$test_id);
                                $iterator  = new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new ArrayIterator($currentDir -> tree)));
                                foreach ($iterator as $key => $value) {
                                    $fields = array('path'          => $key,
                                                    'users_LOGIN'   => $done_tests['users_LOGIN'][$key],
                                                    'timestamp'     => time(),
                                                    'description'   => basename($key));
                                    $file_id = eF_insertTableData("files", $fields);
                                }
                            } catch (Exception $e) {}
                        }

                        $message_attachments = eF_getTableDataFlat("f_personal_messages", "*");
                        foreach ($message_attachments['attachments'] as $key => $attachments) {
                            $attachments = unserialize($attachments);
                            foreach ($attachments as $file) {
                                if (is_file($file)) {
                                    $fields = array('path'          => $file,
                                                    'users_LOGIN'   => $message_attachments['users_LOGIN'][$key],
                                                    'timestamp'     => time(),
                                                    'description'   => basename($file));
                                    $file_id = eF_insertTableData("files", $fields);
                                }
                            }
                        }

                  }


                  // Skillgap tests related code
                    // Two conditions must be fulfilled:
                    // - every lesson offers a lesson specific skill [I](Knowledge of lesson: xxx) (and every course the same [II])
                    // - every question is automatically linked to the skill of the lesson is belongs to [III]

                    // [I] Check and addition of all existing lesson related skills
                    $lessons = eF_getTableData("lessons","*","");
                    $lesson_skills = eF_getTableDataFlat("module_hcd_skills NATURAL JOIN module_hcd_lesson_offers_skill", "*", "categories_ID = -1");
                    foreach($lessons as $lesson) {
                        // If the lesson is not provided only through a course - where the course skill applies
                        if ($lesson['course_only'] == 0) {
                            // If the lesson's skill is not currently logged to the table of lesson-skills
                            if (!in_array($lesson['id'], $lesson_skills['lesson_ID'])) {
                                $new_skill_id = eF_insertTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFLESSON . " ". $lesson['name'], "categories_ID" => -1));
                                if (!$to_add_to_lesson_offers) {
                                    $to_add_to_lesson_offers = "('".$lesson['id'] . "','". $new_skill_id . "')";
                                } else {
                                    $to_add_to_lesson_offers .= ",('".$lesson['id'] . "','". $new_skill_id . "')";
                                }
                            }
                        }

                    }
                    if (isset($to_add_to_lesson_offers)) {
                        eF_execute("INSERT INTO module_hcd_lesson_offers_skill (lesson_ID,skill_ID) VALUES " . $to_add_to_lesson_offers);
                    }

                    // [II] Check and addition of all existing course related skills
                    $courses = eF_getTableData("courses","*","");
                    $course_skills = eF_getTableDataFlat("module_hcd_skills NATURAL JOIN module_hcd_course_offers_skill", "*", "categories_ID = -1");

                    foreach($courses as $course) {
                        // If the course is not provided only through a course - where the course skill applies
                        if ($course['course_only'] == 0) {
                            // If the course's skill is not currently logged to the table of course-skills
                            if (!in_array($course['id'], $course_skills['courses_ID'])) {
                                $new_skill_id = eF_insertTableData("module_hcd_skills", array("description" => _KNOWLEDGEOFCOURSE. " ". $course['name'], "categories_ID" => -1));
                                if (!$to_add_to_course_offers) {
                                    $to_add_to_course_offers = "('".$course['id'] . "','". $new_skill_id . "')";
                                } else {
                                    $to_add_to_course_offers .= ",('".$course['id'] . "','". $new_skill_id . "')";
                                }
                            }
                        }

                    }

                    if (isset($to_add_to_course_offers)) {
                        eF_execute("INSERT INTO module_hcd_course_offers_skill (courses_ID,skill_ID) VALUES " . $to_add_to_course_offers);
                    }

                    /// [III] Each question should offer the skill of the lesson it belongs or of the course its lesson belongs
                    // ATTENTION: The following works correctly because it succeeds the code where all lessons have a corresponding skill - otherwise problem


                    $questions = eF_getTableData("questions LEFT OUTER JOIN questions_to_skills ON questions.id = questions_to_skills.questions_ID JOIN lessons ON lessons.id = questions.lessons_ID","questions.id, lessons.course_only, questions.lessons_ID, questions_to_skills.skills_ID", "questions.lessons_ID <> 0");

                    // This returns a 1-1 table: 1 lesson to its 1 corresponding skill
                    $result = eF_getTableData("module_hcd_lesson_offers_skill JOIN module_hcd_skills ON module_hcd_skills.skill_ID = module_hcd_lesson_offers_skill.skill_ID", "module_hcd_lesson_offers_skill.*", "module_hcd_skills.categories_ID = -1");

                    //$skills = eF_getTableData("questions LEFT OUTER JOIN (questions_to_skills JOIN module_hcd_lesson_offers_skill ON questions_to_skills.skills_ID = module_hcd_lesson_offers_skill.skill_ID) ON questions.id = questions_to_skills.questions_ID JOIN lessons ON lessons.id = questions.lessons_ID WHERE questions.lessons_ID <> 0", "questions.id, questions.lessons_ID, module_hcd_lesson_offers_skill.lesson_ID,lessons.course_only", "");

                    $lesson_to_skill = array();
                    foreach ($result as $rid => $skill) {
                        $lesson_to_skill[$skill['lesson_ID']] = $skill['skill_ID'];
                    }

                    $lessons_only_from_courses = array();
                    // DB Insertion inside a loop - well only once...
                    foreach ($questions as $qid => $question) {
                        //  The question belongs to a lesson outside a course with a skill_ID that is among the lesson related skill IDs or NULL and not equal to the skill of the specific lesson skill, then insert it
                        if ($question['course_only'] == 0) {
                            if ($question['skills_ID'] != $lesson_to_skill[$question['lessons_ID']] && (!$question['skills_ID'] || in_array($question['skills_ID'], $lesson_to_skill))) {
                                eF_insertTableData("questions_to_skills", array("questions_ID" => $question['id'], "skills_ID" => $lesson_to_skill[$question['lessons_ID']], "relevance" => 2));
                            }
                            unset($questions[$qid]);
                        } else {
                            $lessons_only_from_courses[] = $question['lessons_ID'];
                        }
                    }

                    // Now correlate questions to the skills of courses that have course_only lessons with those questions
                    // This returns a 1-1 table: 1 course to its 1 corresponding skill
                    $result = eF_getTableData("module_hcd_course_offers_skill JOIN module_hcd_skills ON module_hcd_skills.skill_ID = module_hcd_course_offers_skill.skill_ID", "module_hcd_course_offers_skill.*", "module_hcd_skills.categories_ID = -1 AND module_hcd_course_offers_skill.courses_ID IN ('". implode("','", $lessons_only_from_courses) ."')");
                    $course_to_skill = array();
                    foreach ($result as $rid => $skill) {
                        $course_to_skill[$skill['courses_ID']] = $skill['skill_ID'];
                    }
//pr($course_to_skill);
                    $courses_list = eF_getTableData("lessons_to_courses", "*", "lessons_ID IN ('". implode("','", $lessons_only_from_courses) ."')");
                    // DB Insertion inside a loop - well only once...
                    foreach ($questions as $question) {
                        //  The remaining questions are for course only related lessons
                        if ($question['skills_ID'] != $course_to_skill[$question['courses_ID']] && (!$question['skills_ID'] || in_array($question['skills_ID'], $course_to_skill))) {
                            eF_insertTableData("questions_to_skills", array("questions_ID" => $question['id'], "skills_ID" => $course_to_skill[$question['courses_ID']], "relevance" => 2));
                        }
                    }


                    advanceProgressBar(100, 'Finalizing upgrade');

                    if (!isset($errors) || !$errors) {
                        $file_contents     = file_get_contents($path.'configuration_temp.php');                            //Load existing configuration file
                        $new_file_contents = preg_replace('/\/\/(require_once\("globals\.php"\);)/', '$1', $file_contents);    //Replace sample settings with current settings
                        file_put_contents($path.'configuration_temp.php', $new_file_contents);
                    } else {
                        $smarty -> assign('T_ERROR', true);
                        if (!$values['new_db_name']) {                                      //If we are upgrading over the existing database, smarty must know, so that it does not display the options "delete database and try again"
                            $smarty -> assign('UPGRADE_SINGLE_DB', true);
                        }
                    }

                } catch (Exception $e) {
                    $message      .= $e -> msg."<br/>";
                    $message_type = 'failure';
                    $errors       = true;
                }

                if (isset($failed_insertions)) {
                    $message      .= implode(', ', $failed_insertions)."<br/>";
                    $message_type = 'failure';
                    $errors       = true;
                }

                if ($errors) {
                    $smarty -> assign('T_ERROR', true);
                    if (!$values['new_db_name']) {                                      //If we are upgrading over the existing database, smarty must know, so that it does not display the options "delete database and try again"
                        $smarty -> assign('UPGRADE_SINGLE_DB', true);
                    }
                } else {
                    echo "<script>document.location='".G_SERVERNAME."/install/install.php?finish=1&upgrade=1'</script>";
                }
            } elseif (isset($values['step2_submit'])) {
                $file_contents     = file_get_contents($path.'configuration_temp.php');                            //Load existing configuration file
                $new_file_contents = preg_replace('/\/\/(require_once\("globals\.php"\);)/', '$1', $file_contents);    //Replace sample settings with current settings
                file_put_contents($path.'configuration_temp.php', $new_file_contents);

                echo "<script>document.location='".G_SERVERNAME."/install/install.php?finish=1&upgrade=1'</script>";
            }

        }

    } else {
        $form -> addElement('select', 'db_type', null, array('mysql' => 'MySQL'));
        $form -> addRule('db_type', 'The database type is mandatory', 'required', null, 'client');                //The database type can only be string and is mandatory
        $form -> addRule('db_type', 'Invalid database type', 'checkParameter', 'string');                //The database type can only be string and is mandatory
        $form -> setDefaults(array('db_type' => 'mysql'));
        $form -> freeze(array('db_type'));                                                               //Freeze this element, since it can't change for now

        $form -> addElement('text', 'db_host', null, 'class = "inputText"');
        $form -> addRule('db_host', 'The database host is mandatory', 'required', null, 'client');                //The database type can only be string and is mandatory
        $form -> addRule('db_host', 'Invalid database host', 'checkParameter', 'alnum_general');         //The database host can only be string and is mandatory

        $form -> addElement('text', 'db_user', null, 'class = "inputText"');
        $form -> addRule('db_user', 'The database user is mandatory', 'required', null, 'client');                //The database type can only be string and is mandatory
        $form -> addRule('db_user', 'Invalid database user', 'checkParameter', 'alnum_general');                //The database user can only be string

        $form -> addElement('password', 'db_password', null, 'class = "inputText"');

        $form -> addElement('text', 'db_name', null, 'class = "inputText"');
        $form -> addRule('db_name', 'The database name is mandatory', 'required', null, 'client');                //The database type can only be string and is mandatory
        $form -> addRule('db_name', 'Invalid database name', 'checkParameter', 'alnum_general');        //The database name can only be string

        $form -> addElement('submit', 'create_tables', 'Create database tables', 'class = "flatButton"');
        $form -> addElement('submit', 'rollback', 'Delete database and retry', 'class = "flatButton"', 'onclick = "return confirm(\'This way all existing data in database will be lost. Are you sure?\')"');
        $form -> addElement('submit', 'step2_submit', 'Continue to next step', 'class = "flatButton"');

        $form -> setDefaults(array('db_host'     => 'localhost',
                                   'db_user'     => 'root',
                                   'db_password' => '',
                                   'db_name'     => 'efront'));
        /*
        Perform the DBMS functions. Below, when we refer to "DBMS" we mean the software (e.g. MySQL)
        and by saying "database" we mean the specific database, e.g. "efront"
        */
        if ($form -> isSubmitted()) {
            if ($form -> validate()) {
                $db = ADONewConnection($form -> exportValue('db_type'));                                    //Set Connection parameter to "mysql"
                $values = $form -> exportValues();

                if (isset($values['create_tables']) || isset($values['rollback'])) {
                    if (isset($values['rollback'])) {
                        try {
                            if ($values['db_name']) {
                                $db -> NConnect($values['db_host'], $values['db_user'], $values['db_password']);
                                $db -> Execute("drop database ".$values['db_name']);
                            }
                        } catch (Exception $e) {
                            $message      = $e->msg;
                            $message_type = 'failure';
                        }
                    }

                    try {
                        $db -> NConnect($values['db_host'], $values['db_user'], $values['db_password']);
                        $db -> Execute("SET NAMES 'UTF8'");
                        $db -> Execute("create database ".$values['db_name']." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");                          //Create the new database
                    } catch (Exception $e) {}

                    try {
                        $db -> NConnect($values['db_host'], $values['db_user'], $values['db_password'], $values['db_name']);
                        $db -> Execute("SET NAMES 'UTF8'");
                        $file_contents = trim(file_get_contents("sql_".$values['db_type'].".txt"));                                  //Get the sql queries text
                        $file_contents = explode(';',$file_contents);                                       //Form the sql queries, by splitting each CREATE statement
                        if (!end($file_contents)) {
                            array_pop($file_contents);                                                      //Remove last element, if it is an empty array (which is usually the case)
                        }
                        foreach ($file_contents as $query) {                                                //Run queries
                            preg_match('/CREATE TABLE (\w+) .*/', $query, $matches);
                            try {
                                $db -> Execute($query);
                            } catch (Exception $e) {
                                $failed_tables[] = $e->msg;                                                 //Each failed query will not halt the execution, but will be recorded to this table
                            }
                        }
                        if (isset($failed_tables)) {                                                        //If there were any errors, assign them to smarty to be displayed
                            $smarty -> assign('T_FAILED_TABLES', implode('<br/>', $failed_tables));
                        }
                        if (is_file($path."configuration_temp.php")) {
                            unlink($path."configuration_temp.php");                                         //Delete any temporary configuration file left from previous attempt
                        }
                        $file_contents = file_get_contents('sample_config.php');                            //Load sample configuration file
                        $patterns = array('/(define\("G_DBTYPE", ").*("\);)/',
                                          '/(define\("G_DBHOST", ").*("\);)/',
                                          '/(define\("G_DBUSER", ").*("\);)/',
                                          '/(define\("G_DBPASSWD", ").*("\);)/',
                                          '/(define\("G_DBNAME", ").*("\);)/');
                        $replacements = array('${1}'.$values['db_type'].'${2}', '${1}'.$values['db_host'].'${2}', '${1}'.$values['db_user'].'${2}', '${1}'.$values['db_password'].'${2}', '${1}'.$values['db_name'].'${2}');
                        $new_file_contents = preg_replace($patterns, $replacements, $file_contents, -1, $count);    //Replace sample settings with current settings
                        file_put_contents($path.'configuration_temp.php', $new_file_contents);

                        if (isset($failed_tables)) {                                                        //If there were any errors, assign them to smarty to be displayed
                            $smarty -> assign('T_ERROR', true);
                            $message      = implode(', ', $failed_tables);
                            $message_type = 'failure';
                        } else {
                            header("location:install.php?step=3");
                        }
                    } catch (Exception $e) {
                        $message      = $e->msg;
                        $message_type = 'failure';
                    }
                } elseif (isset($values['step2_submit'])) {
                    header("location:install.php?step=3");
                }
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $renderer->setRequiredTemplate(
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}'
        );

    $form -> setJsWarnings('The following errors occured:', 'Please correct the above errors');
    $form -> setRequiredNote('* Denotes mandatory fields');
    $form -> accept($renderer);

    $smarty -> assign('T_DATABASE_FORM', $renderer -> toArray());


}
/*
The third step is used to create administrator account, and to insert default values to database
These values are configuration values, as well as sample accounts and lessons. Furthermore,
the administrator may import users and lessons
*/
else if (isset($_GET['step']) && $_GET['step'] == 3) {
    $_GET['bypass_language'] = 'english';                                       //This is used to make sure the script will continue working, even if the configuration file is not formed as it should
    /**Include the newly created configuration file*/
    require_once($path."configuration_temp.php");
    /**Include the database functions*/
    require_once($path."database.php");
    $db -> Execute("SET NAMES 'UTF8'");

    $form = new HTML_QuickForm("step3_form", "post", "install/install.php?step=3".$migrate, "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter

    //Core server settings
    $form -> addElement('select', 'conf[default_language]', null, $languagesArray, 'class = "inputSelect"');
    $form -> addElement('text',   'server_name',      null, 'class = "inputText"');
    $form -> addElement('text',   'server_port',      null, 'class = "inputText"');
    $form -> addElement('text',   'root_path',        null, 'class = "inputText"');
    $form -> addElement('advcheckbox', 'conf[caching]', null, null, null, array(0,1));
    $form -> addElement('text', 'conf[cache_timeout]',null, 'class = "inputText"');

    $form -> addRule('server_name', 'The server name is mandatory', 'required', null, 'client');
    $form -> addRule('server_port', 'The server port is mandatory', 'required', null, 'client');
    $form -> addRule('root_path',   'The root path is mandatory',   'required', null, 'client');
    $form -> addRule('conf[server_port]', 'The server port must be numeric', 'numeric', null, 'client');
    $form -> addRule('conf[cache_timeout]', 'The cache timeout must be numeric', 'numeric', null, 'client');


    //Additional server settings
    $form -> addElement('advcheckbox', 'conf[activation]', null, null, null, array(0,1));
    $form -> addElement('advcheckbox', 'conf[onelanguage]', null, null, null, array(0,1));
    $form -> addElement('advcheckbox', 'conf[signup]', null, null, null, array(0,1));
    $form -> addElement('advcheckbox', 'conf[show_footer]', null, null, null, array(0,1));
    $form -> addElement('text',     'conf[ip_white_list]',   null, 'class = "inputText"');
    $form -> addElement('text',     'conf[ip_black_list]',   null, 'class = "inputText"');
    $form -> addElement('text',     'conf[file_white_list]', null, 'class = "inputText"');
    $form -> addElement('text',     'conf[file_black_list]', null, 'class = "inputText"');
    $form -> addElement('text',     'conf[max_file_size]',   null, 'class = "inputText"');
    //Mail server settings
    $form -> addElement('text',     'conf[smtp_host]', null, 'class = "inputText"');
    $form -> addElement('text',     'conf[smtp_user]', null, 'class = "inputText"');
    $form -> addElement('password', 'conf[smtp_pass]', null, 'class = "inputText"');
    $form -> addElement('text',     'conf[smtp_port]', null, 'class = "inputText"');
    $form -> addElement('advcheckbox', 'conf[smtp_auth]', null, null, null, array(0,1));
    //LDAP server settings
    $form -> addElement('advcheckbox', 'conf[activate_ldap]', null, null, 'class = "inputCheckBox" onclick = "eF_js_activateElements(\'ldap\')"', array(0,1));
    $form -> addElement('advcheckbox', 'conf[only_ldap]',     null, null, 'class = "inputCheckBox" readonly = "readonly" style = "color:#808080"', array(0,1));
    $form -> addElement('text',     'conf[ldap_server]',   null, 'class = "inputText" readonly = "readonly" style = "color:#808080"');
    $form -> addElement('text',     'conf[ldap_port]',     null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    $form -> addRule('conf[ldap_port]', 'The server port must be numeric', 'numeric', null, 'client');
    $form -> addElement('text',     'conf[ldap_base_dn]',  null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    $form -> addElement('text',     'conf[ldap_bind_dn]',  null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    $form -> addElement('password', 'conf[ldap_password]', null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    $form -> addElement('text',     'conf[ldap_protocol]', null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    //LDAP attribute mapping
    $form -> addElement('text', 'conf[ldap_preferredlanguage]', null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    $form -> addElement('text', 'conf[ldap_telephonenumber]',   null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    $form -> addElement('text', 'conf[ldap_mail]',              null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    $form -> addElement('text', 'conf[ldap_postaladdress]',     null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    $form -> addElement('text', 'conf[ldap_l]',                 null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    $form -> addElement('text', 'conf[ldap_cn]',                null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');
    $form -> addElement('text', 'conf[ldap_uid]',               null, 'class = "inputText" readonly = "readonly"  style = "color:#808080"');

    $form -> addElement('submit', 'step3_submit', 'Continue', 'class = "flatButton"');
    $form -> addElement('submit', 'rollback', 'Rollback', 'class = "flatButton"');
    $form -> addElement('submit', 'continue_anyway', 'Continue anyway', 'class = "flatButton"');

    /**Include configuration class*/
    require_once($path."configuration.class.php");


    $form -> setDefaults(array('server_name'   => str_replace("install/install.php", "", $_SERVER['PHP_SELF']),
                               'server_port'   => $_SERVER['SERVER_PORT'],
                               'root_path'     => str_replace('\\','/',dirname(realpath('..'))).'/',
                               'conf[caching]' => '1',
                               'conf[cache_timeout]' => '3600'));

    $form -> freeze(array('server_name'));
    $form -> setDefaults(array('conf' => EfrontConfiguration :: getDefaultValues()));

    if ($form -> isSubmitted()) {
        if ($form -> validate()) {
            $values = $form -> exportValues();                                  //Get all the form values

            if (isset($values['step3_submit'])) {
                foreach ($values['conf'] as $key => $value) {                   //Get all the configuration values
                    try {
                        EfrontConfiguration :: setValue($key, $value);
                    } catch (Exception $e) {
                        $failed_insertions[] = $e -> msg;                       //Catch any error messages, to be shown later
                    }
                }
                                                                          //Insert languages to corresponding table
                if (!$migrate) {                                                                                     //If we are migrating, we read configuration values from the database
					addLanguagesDB();
				}
                
                if (isset($failed_insertions)) {                                //This means we experienced problems
                    $smarty -> assign("T_FAILED_INSERTIONS", implode("<br/>", $failed_insertions));
                } else {
                    $file_contents = file_get_contents($path.'configuration_temp.php');                            //Load configuration file
                    $patterns      = array('/(define\("G_SERVERNAME", "http:\/\/"\.\$_SERVER\["HTTP_HOST"\]\."\/).*("\);)/',
                                           '/(define\("G_SERVERPORT", ").*("\);)/',
                                           '/(define\("G_ROOTPATH", ").*("\);)/',
                                           '/\/\/(require_once\("globals\.php"\);)/');
                    
                    $values['server_name'] != '/' ? $values['server_name'] = trim($values['server_name'], "/")."/" : $values['server_name'] = '';
                    $replacements      = array('${1}'.$values['server_name'].'${2}', '${1}'.$values['server_port'].'${2}', '${1}'.$values['root_path'].'${2}', '${1}');       //${1} and ${2} is needed in port, because port is a number
                    $new_file_contents = preg_replace($patterns, $replacements, $file_contents, -1, $count);    //Replace sample settings with current settings
                    file_put_contents($path.'configuration_temp.php', $new_file_contents);

                    $migrate ? header("location:install.php?finish=1") : header("location:install.php?step=4");             //Redirect anyway, even if errors occured during the configuration values insertion
                }
            } elseif (isset($values['rollback'])) {                             //If the user asked to rollback, empty the configuration table
                try {
                    $db -> Execute("truncate table configuration");
                    $db -> Execute("truncate table languages");
                    $smarty -> assign("T_ROLLBACK_PROBLEM", false);             //Catch any error messages durng the table truncation
                } catch (Exception $e) {
                    $smarty -> assign("T_ROLLBACK_PROBLEM", $e -> msg);         //Catch any error messages durng the table truncation
                }
            } elseif (isset($values['continue_anyway'])) {
                $file_contents = file_get_contents($path.'configuration_temp.php');                            //Load configuration file
                $patterns = array('/(define\("G_SERVERNAME", "http:\/\/"\.\$_SERVER\["HTTP_HOST"\]\."\/).*("\);)/',
                                  '/(define\("G_SERVERPORT", ").*("\);)/',
                                  '/(define\("G_ROOTPATH", ").*("\);)/',
                                  '/\/\/(require_once\("globals\.php"\);)/');
                $ending = mb_substr($values['server_name'], -2);
				if (strcmp($ending,"//") ==0) {
					$values['server_name'] = mb_substr($values['server_name'], 0, -2)."/";
				} 
				$replacements = array('${1}'.$values['server_name'].'${2}', '${1}'.'80'.'${2}', '${1}'.$values['root_path'].'${2}', '${1}');
                $new_file_contents = preg_replace($patterns, $replacements, $file_contents);    //Replace sample settings with current settings
                file_put_contents($path.'configuration_temp.php', $new_file_contents);

                $migrate ? header("location:install.php?finish=1") : header("location:install.php?step=4");             //Redirect anyway, even if errors occured during the configuration values insertion
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $renderer->setRequiredTemplate(
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}'
        );

    $renderer->setErrorTemplate(
       '{$html}{if $error}
            <span class = "formError">{$error}</span>
        {/if}'
        );
    $form -> setJsWarnings('The following errors occured:', 'Please correct the above errors');
    $form -> setRequiredNote('* Denotes mandatory fields');
    $form -> accept($renderer);

    $smarty -> assign('T_DATABASE_FORM', $renderer -> toArray());
}
/*
In this step the user creates Administrator and default user accounts
*/
else if (isset($_GET['step']) && $_GET['step'] == 4) {
    /**Include the newly created configuration file*/
    require_once($path."configuration_temp.php");
    /**Include the database functions*/
    require_once($path."database.php");
    $db -> Execute("SET NAMES 'UTF8'");
    /**Include the configuration class*/
    require_once($path."configuration.class.php");

    $configuration = EfrontConfiguration :: getValues();

    $form = new HTML_QuickForm("step4_form", "post", "install/install.php?step=4".$upgrade, "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter

    //Administrator account settings
    $form -> addElement('text', 'admin_user',      null, 'class = "inputText"');
    $form -> addElement('password', 'admin_password',  null, 'class = "inputText"');
    $form -> addElement('password', 'admin_repeat_pwd',null, 'class = "inputText"');
    $form -> addElement('text', 'admin_name',      null, 'class = "inputText"');
    $form -> addElement('text', 'admin_surname',   null, 'class = "inputText"');
    $form -> addElement('text', 'admin_email',     null, 'class = "inputText"');
    $form -> addElement('select', 'admin_language', null, $languagesArray, 'class = "inputSelect"');

    $form -> addRule('admin_user',      'The administrator login is mandatory',    'required',       null, 'client');
    $form -> addRule('admin_password',  'The administrator password is mandatory', 'required',       null, 'client');
    $form -> addRule('admin_repeat_pwd','You must repeat password',                'required',       null, 'client');
    $form -> addRule('admin_name',      'The administrator name is mandatory',     'required',       null, 'client');
    $form -> addRule('admin_surname',   'The administrator surname is mandatory',  'required',       null, 'client');
    $form -> addRule('admin_email',     'The administrator email is mandatory',    'required',       null, 'client');
    $form -> addRule('admin_email',     'Invalid administrator email',             'checkParameter', 'email');
    $form -> addRule(array('admin_password', 'admin_repeat_pwd'), 'The passwords do not match', 'compare', null, 'client');

    $form -> setDefaults(array('admin_user'     => 'admin',
                               'admin_language' => $configuration['default_language']));
    $form -> freeze(array('admin_language'));

    //Professor sample account settings
    $form -> addElement('text', 'prof_user',      null, 'class = "inputText"');
    $form -> addElement('text', 'prof_password',  null, 'class = "inputText" onclick = "this.value = \'\';this.type=\'password\'"');
    $form -> addElement('text', 'prof_repeat_pwd',null, 'class = "inputText" onclick = "this.value = \'\';this.type=\'password\'"');
    $form -> addElement('text', 'prof_name',      null, 'class = "inputText"');
    $form -> addElement('text', 'prof_surname',   null, 'class = "inputText"');
    $form -> addElement('text', 'prof_email',     null, 'class = "inputText"');
    $form -> addElement('select', 'prof_language', null, $languagesArray, 'class = "inputSelect"');

    $form -> addRule('prof_user',      'The professor login is mandatory',    'required',       null, 'client');
    $form -> addRule('prof_password',  'The professor password is mandatory', 'required',       null, 'client');
    $form -> addRule('prof_repeat_pwd','You must repeat password',            'required',       null, 'client');
    $form -> addRule('prof_name',      'The professor name is mandatory',     'required',       null, 'client');
    $form -> addRule('prof_surname',   'The professor surname is mandatory',  'required',       null, 'client');
    $form -> addRule('prof_email',     'The professor email is mandatory',    'required',       null, 'client');
    $form -> addRule('prof_email',     'Invalid professor email',             'checkParameter', 'email');
    $form -> addRule(array('prof_password', 'prof_repeat_pwd'), 'The passwords do not match', 'compare', null, 'client');

    $form -> setDefaults(array('prof_user'       => 'professor',
                               'prof_password'   => 'professor',
                               'prof_repeat_pwd' => 'professor',
                               'prof_name'       => 'Professor',
                               'prof_surname'    => 'eFront',
                               'prof_email'      => 'professor@example.com',
                               'prof_language'   => $configuration['default_language']));
    $form -> freeze(array('prof_language'));

    //Student sample account settings
    $form -> addElement('text', 'stud_user',      null, 'class = "inputText"');
    $form -> addElement('text', 'stud_password',  null, 'class = "inputText" onclick = "this.value = \'\';this.type=\'password\'"');
    $form -> addElement('text', 'stud_repeat_pwd',null, 'class = "inputText" onclick = "this.value = \'\';this.type=\'password\'"');
    $form -> addElement('text', 'stud_name',      null, 'class = "inputText"');
    $form -> addElement('text', 'stud_surname',   null, 'class = "inputText"');
    $form -> addElement('text', 'stud_email',     null, 'class = "inputText"');
    $form -> addElement('select', 'stud_language', null, $languagesArray, 'class = "inputSelect"');

    $form -> addRule('stud_user',      'The student login is mandatory',    'required',       null, 'client');
    $form -> addRule('stud_password',  'The student password is mandatory', 'required',       null, 'client');
    $form -> addRule('stud_repeat_pwd','You must repeat password',          'required',       null, 'client');
    $form -> addRule('stud_name',      'The student name is mandatory',     'required',       null, 'client');
    $form -> addRule('stud_surname',   'The student surname is mandatory',  'required',       null, 'client');
    $form -> addRule('stud_email',     'The student email is mandatory',    'required',       null, 'client');
    $form -> addRule('stud_email',     'Invalid student email',             'checkParameter', 'email');
    $form -> addRule(array('stud_password', 'stud_repeat_pwd'), 'The passwords do not match', 'compare', null, 'client');

    $form -> setDefaults(array('stud_user'       => 'student',
                               'stud_password'   => 'student',
                               'stud_repeat_pwd' => 'student',
                               'stud_name'       => 'Student',
                               'stud_surname'    => 'eFront',
                               'stud_email'      => 'student@example.com',
                               'stud_language'   => $configuration['default_language']));
    $form -> freeze(array('stud_language'));

    $form -> addElement('checkbox', 'create_lesson', null, null, 'id = "activate_stud" class = "inputCheckBox"');
    $form -> addElement('checkbox', 'activate_prof', null, null, 'id = "activate_prof" class = "inputCheckBox" onclick = "eF_js_activateElements(\'prof\')"');
    $form -> addElement('checkbox', 'activate_stud', null, null, 'id = "activate_stud" class = "inputCheckBox" onclick = "eF_js_activateElements(\'stud\')"');
    $form -> setDefaults(array('create_lesson' => true,
                               'activate_prof' => true,
                               'activate_stud' => true));

    $form -> addElement('submit', 'step4_submit', 'Continue', 'class = "flatButton"');
    $form -> addElement('submit', 'try_again', 'Try Again', 'class = "flatButton"');
    $form -> addElement('submit', 'continue_anyway', 'Continue anyway', 'class = "flatButton"');

    if ($form -> isSubmitted()) {
        if ($form -> validate()) {
            $values = $form -> exportValues();                                  //Get all the form values
            try {
                if (isset($values['continue_anyway'])) {
                    header("location:install.php?finish=1");
                }
                if (isset($values['try_again'])) {
                    $user = EfrontUserFactory :: factory($values['admin_user']);
                    $user -> delete();
                    if (isset($values['activate_prof'])) {
                        $user = EfrontUserFactory :: factory($values['prof_user']);
                        $user -> delete();
                    }
                    if (isset($values['activate_stud'])) {
                        $user = EfrontUserFactory :: factory($values['stud_user']);
                        $user -> delete();
                    }
                }
            } catch (Exception $e) {
                $errors[] = $e -> getMessage();
            }

            $admin_values = array('login'    => $values['admin_user'],
                                  'password' => $values['admin_password'],
                                  'email'    => $values['admin_email'],
                                  'name'     => $values['admin_name'],
                                  'surname'  => $values['admin_surname'],
                                  'languages_NAME' => $values['admin_language'],
                                  'active'   => '1',
                                  'user_type'=> 'administrator');

            try {
                EfrontUser :: createUser($admin_values);
                if (MODULE_HCD_INTERFACE) {
                    EfrontHcdUser :: createUser(array("users_login" => $values['admin_user']));
                }
            } catch (Exception $e) {
                $errors[] = 'The administrator account was not created: '.$e -> msg;
            }

            try {
                EfrontConfiguration :: setValue('system_email', $values['admin_email']);
            } catch (Exception $e) {
                $errors[] = 'The system email could not be set: '.$e -> getMessage();
            }

            if (isset($values['activate_prof'])) {
                $smarty -> assign("T_CREATE_PROF", true);
                $prof_values = array('login'    => $values['prof_user'],
                                     'password' => $values['prof_password'],
                                     'email'    => $values['prof_email'],
                                     'name'     => $values['prof_name'],
                                     'surname'  => $values['prof_surname'],
                                     'languages_NAME' => $values['prof_language'],
                                     'active'   => '1',
                                     'user_type'=> 'professor');
                try {
                    EfrontUser :: createUser($prof_values);
                    if (MODULE_HCD_INTERFACE) {
                        EfrontHcdUser :: createUser(array("users_login" => $values['prof_user']));
                    }
                } catch (Exception $e) {
                    $errors[] = 'The professor account was not created: '.$e -> getMessage();
                }

            }

            if (isset($values['activate_stud'])) {
                $smarty -> assign("T_CREATE_STUD", true);
                $stud_values = array('login'    => $values['stud_user'],
                                     'password' => $values['stud_password'],
                                     'email'     => $values['stud_email'],
                                     'name'     => $values['stud_name'],
                                     'surname'  => $values['stud_surname'],
                                     'languages_NAME' => $values['stud_language'],
                                     'active'   => '1',
                                     'user_type'=> 'student');

                try {
                    EfrontUser :: createUser($stud_values);
                    if (MODULE_HCD_INTERFACE) {
                        EfrontHcdUser :: createUser(array("users_login" => $values['stud_user']));
                    }
                } catch (Exception $e) {
                    $errors[] = 'The student account was not created: '.$e -> getMessage();
                }

            }

            if (isset($values['create_lesson'])) {
                $default_lessons = array('Greedy algorithms' => 'Greedy Algorithms.zip', 'Maya civilization' => 'maya_civilization.tar.gz');
                $result          = eF_getTableData("directions", "id", "name='Default direction'");      //Check if the direction was created in a previous attempt
                if (sizeof($result) > 0) {
                    $direction_id = $result[0]['id'];
                } else {
                    $direction_id = eF_insertTableData("directions", array('name' => 'Default direction', 'active' => 1));
                }

                if ($direction_id) {
                    $result = eF_getTableDataFlat("lessons", "id, name");                                 //Check if a lessons were created in a previous attempt
                    if (sizeof($result) > 0) {
                        foreach ($result['id'] as $id) {
                            EfrontLesson::deleteLesson($id);
                        }
                    }
                    $fields = array('directions_ID'  => $direction_id,
                                    'active'         => 1,
                                    'languages_NAME' => $configuration['default_language']);
                    $users = eF_getTableDataFlat("users", "login, user_type", "user_type = 'student' or user_type = 'professor'");
                    foreach ($default_lessons as $name => $filename) {

                        try {
                            $file   = new EfrontFile(EfrontDirectory :: normalize(getcwd()).'/'.$filename);
                            $lesson = EfrontLesson :: createLesson(array_merge(array('name' => $name), $fields));
                            $file   = $file -> copy($lesson -> getDirectory());
                            $lesson -> import($file);
                            $lesson -> addUsers($users['login'], $users['user_type']);
                        } catch (Exception $e) {
                            $errors[] = $e -> getMessage();
                        }
                    }
                } else {
                    $smarty -> assign("T_ERROR", "The direction could not be created");
                }

            }
            //Create default forum
            $fields_forum = array('title'       => "General Forum",
                                  'lessons_ID'  => 0,
                                  'parent_id'   => 0,
                                  'status'      => 'public',
                                  'users_LOGIN' => 'admin',
                                  'comments'    => '');
            eF_insertTableData("f_forums", $fields_forum);

            $smarty -> assign("T_CREATE_ERRORS", implode("<br/>", $errors));

            if (!isset($errors)) {
                header("location:install.php?finish=1");
            }
        }
    }

    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);

    $renderer->setRequiredTemplate(
       '{$html}{if $required}
            &nbsp;<span class = "formRequired">*</span>
        {/if}'
        );

    $renderer->setErrorTemplate(
       '{$html}{if $error}
            <span class = "formError">{$error}</span>
        {/if}'
        );
    $form -> setJsWarnings('The following errors occured:', 'Please correct the above errors');
    $form -> setRequiredNote('* Denotes mandatory fields');
    $form -> accept($renderer);

    $smarty -> assign('T_DATABASE_FORM', $renderer -> toArray());

}

/*
Final step
*/
elseif (isset($_GET['finish'])) {
    if (is_file($path."configuration.php") && is_file($path."configuration_temp.php")) {
        if (unlink ($path."configuration.php")) {
            rename($path."configuration_temp.php", $path."configuration.php");
        } else {
            $smarty -> assign("T_ERROR", 'The existing configuration file could not be replaced with the new one. Please do so manually, renaming the file libraries/configuration_temp.php to libraries/configuration.php');
        }
    } elseif (is_file($path."configuration_temp.php")) {
        rename($path."configuration_temp.php", $path."configuration.php");
    }
    $smarty -> clear_all_cache();
    $smarty -> clear_compiled_tpl();

}

$smarty -> assign("T_SERVERNAME", dirname(dirname('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'])).'/');
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> load_filter('output', 'eF_template_formatTimestamp');
if (preg_match("/compatible; MSIE 6/", $_SERVER['HTTP_USER_AGENT']) && !preg_match("/compatible; MSIE 7/", $_SERVER['HTTP_USER_AGENT'])) {
    $smarty -> assign("T_BROWSER", 'IE6');
    $smarty -> load_filter('output', 'eF_template_replacePng');
}


$smarty -> display ("install/install.tpl");




function updateDBTable($table, $newTable, $oldDB = false, $newDB = false) {
    if ($oldDB && $newDB) {
        $GLOBALS['db'] -> NConnect($oldDB['db_host'], $oldDB['db_user'], $oldDB['db_password'], $oldDB['db_name']);
        $GLOBALS['db'] -> Execute("SET NAMES 'UTF8'");
    }

    try {
        $data   = eF_getTableData($table, "count(*)");
    } catch (Exception $e) {
        $limit = 0;
    }
    $unfold = 2000;
    $limit  = ceil($data[0]['count(*)'] / $unfold);
    if ($table != $newTable) {
        try {
            $GLOBALS['db'] -> Execute("truncate table $newTable");
        } catch (Exception $e) {}
    }
    for ($i = 0; $i < $limit; $i++) {
        if ($oldDB && $newDB) {
            $GLOBALS['db'] -> NConnect($oldDB['db_host'], $oldDB['db_user'], $oldDB['db_password'], $oldDB['db_name']);
            $GLOBALS['db'] -> Execute("SET NAMES 'UTF8'");
        }
        $data = eF_getTableData($table, "*", "", "'' limit $unfold offset ".($i*$unfold));
        $data = updateDBData($table, $data);
        if (sizeof($data) > 0) {
            $data = array_values($data);            //Reindex array, in case some values where removed
        }

        if ($oldDB && $newDB) {
            $GLOBALS['db'] -> NConnect($newDB['db_host'], $newDB['db_user'], $newDB['db_password'], $newDB['db_name']);
            $GLOBALS['db'] -> Execute("SET NAMES 'UTF8'");
        }
        eF_insertTableDataMultiple($newTable, $data);
    }
}


function updateDBData($table, $data) {
    if ($table == 'users_to_lessons') {                                //users_to_lessons table requires special handling when upgrading from 3.1.4 to 3.5
        $result = eF_getTableDataFlat("users", "login, user_type");
        $temp   = array_combine($result['login'], $result['user_type']);
    }

    $table_fields = $GLOBALS['db'] -> GetCol("describe $table");
    $table_size   = sizeof($data);
    for ($i = 0; $i < $table_size; $i++) {
        if ($table == 'users') {
            //Remove deprecated users fields (by keeping only the needed ones)
            $fields['login']          = $data[$i]['login'];
            $fields['password']       = $data[$i]['password'];
            $fields['email']          = $data[$i]['email'];
            $fields['languages_NAME'] = $data[$i]['languages_NAME'];
            $fields['name']           = $data[$i]['name'];
            $fields['surname']        = $data[$i]['surname'];
            $fields['active']         = $data[$i]['active'];
            $fields['comments']       = $data[$i]['comments'];
            $fields['user_type']      = $data[$i]['user_type'];
            $fields['timestamp']      = $data[$i]['timestamp'];
            $fields['avatar']         = $data[$i]['avatar'];
            $fields['user_types_ID']  = $data[$i]['user_types_ID'];

            //convert old avatars to new ones.
            if (!eF_checkParameter($fields['avatar'], 'id')) {
                if (!is_file($fields['avatar']) && is_file($rootpath[1].'www/images/avatars/'.$fields['avatar'])) {
                    preg_match("/\d{10}_prefix_(.*)\.\w{3}/", $fields['avatar'], $matches);
                    if ($matches[1] == $fields['login']) {
                        if (is_dir($rootpath[1].'upload/'.$fields['login'])) {
                            if (!is_dir($rootpath[1].'upload/'.$fields['login'].'/avatars')) {
                                mkdir($rootpath[1].'upload/'.$fields['login'].'/avatars', 0755);
                            }
                            if (is_dir($rootpath[1].'upload/'.$fields['login'].'/avatars')) {
                                rename($rootpath[1].'www/images/avatars/'.$fields['avatar'], $rootpath[1].'upload/'.$fields['login'].'/avatars/'.basename($fields['avatar']));
                            }
                            $fields['avatar'] = $rootpath[1].'upload/'.$fields['login'].'/avatars/'.basename($fields['avatar']);
                        }
                    } else {
                        $fields['avatar'] = $rootpath[1].'www/images/avatars/'.$fields['avatar'];
                    }
                } else {
                    $fields['avatar'] = '';
                }
            }

            $data[$i] = $fields;

        } elseif ($table == 'lessons') {                                        //Remove deprecated lesson fields
            //Tracking is always 1 now
            $options = unserialize($data[$i]['options']);
            if (is_array($options) && sizeof($options) > 0 && !$options['tracking']) {
                $options['tracking'] = 1;
                $data[$i]['options'] = serialize($options);
            }
            unset($data[$i]['auto_certificate']);
            unset($data[$i]['auto_complete']);
            unset($data[$i]['publish']);
        } elseif ($table == 'f_personal_messages') {
            $data[$i]['viewed'] == 'yes' ? $data[$i]['viewed'] = 1 : '';
            $data[$i]['viewed'] == 'no'  ? $data[$i]['viewed'] = 0 : '';
        } elseif ($table == 'languages') {
            !isset($data[$i]['translation']) ? $data[$i]['translation'] = ucfirst($data[$i]['name']) : '';
        } elseif ($table == 'courses') {                                                        //Convert old 3.1.x courses to new format
            if ($data[$i]['directions_ID'] == 0) {                                      //Assign a random direction to courses not having one
                $data[$i]['directions_ID'] = $data['directions'][0]['id'];
            }
            if ($lessons = unserialize($data[$i]['lessons'])) {
                foreach ($lessons as $lesson) {
                    $data['lessons_to_courses'][] = array('courses_ID' => $data[$i]['id'], 'lessons_ID' => $lesson);
                }
            }
            unset($data[$i]['lessons']);
            unset($data[$i]['certificate_border']);
            unset($data[$i]['logo_position']);
            unset($data[$i]['logo_id']);
            unset($data[$i]['font_size']);
            unset($data[$i]['font_bold']);
            unset($data[$i]['font_italics']);
            unset($data[$i]['font_underlined']);
        } elseif ($table == 'lessons_to_courses') {                                             //Update lessons_to_courses so that default value is set correctly
            !$data[$i]['previous_lessons_ID'] ? $data[$i]['previous_lessons_ID'] = 0 : null;
        } elseif ($table == 'content') {                                                //Update content so that absolute paths become relative
            $patterns     = array('/\/view_file.php/','/\/content\/lessons\//');
            $replacements = array('view_file.php','content/lessons/');
            $data[$i]['data'] = preg_replace($patterns, $replacements, $data[$i]['data'], -1, $count);

            if (!isset($data[$i]['options'])) {
                if ($data[$i]['ctg_type'] == 'scorm' || $data[$i]['ctg_type'] == 'scorm_test') {
                    $data[$i]['options'] = serialize(array('hide_complete_unit' => 1));
                } else {
                    $data[$i]['options'] = '';
                }
            }
        } elseif ($table == 'users_to_lessons') {
            if (isset($temp[$data[$i]['users_LOGIN']]) && $data[$i]['user_type'] == '') {       //If the users_to_lessons does not have user_type information, add the default user_type
                $data[$i]['user_type'] = $temp[$data[$i]['users_LOGIN']];
            }

            if (isset($usersToLessons[$data[$i]['users_LOGIN']]) && $usersToLessons[$data[$i]['users_LOGIN']] == $data[$i]['lessons_ID']) {
                unset($data[$i]);
                $changeArrayIndices = true;
            } else {
                $usersToLessons[$data[$i]['users_LOGIN']] = $data[$i]['lessons_ID'];
            }
        } elseif ($table == 'users_to_courses') {
            $issued = serialize($data[$i]['issued_certificate']);
            if (sizeof($issued) <= 1){
                $data[$i]['issued_certificate'] = null;
            }
        }
        elseif ($table == 'files') {
            if (isset($data[$i]['original_name'])) {
                if ($data[$i]['type'] == 'directory') {
                    unset($data[$i]);
                } else {
                    $newName = str_replace(G_ROOTPATH, '', dirname($data[$i]['file']).'/'.EfrontFile :: encode($data[$i]['original_name']));
                    $newName = preg_replace("#(.*)www/content/lessons/#", "www/content/lessons/", $newName);
                    $data[$i]['path'] = $newName;
                    if ($data[$i]['original_name'] != basename($data[$i]['file'])) {
                        if (!is_file(G_ROOTPATH.$newName)) {
                            rename($data[$i]['file'], G_ROOTPATH.$newName);
                        } else {
                            unlink($data[$i]['file']);
                        }
                    }
                    unset($data[$i]['file']);
                    unset($data[$i]['directory']);
                    unset($data[$i]['original_name']);
                    unset($data[$i]['physical_name']);
                    unset($data[$i]['type']);
                }
            }
        } elseif ($table == 'module_hcd_employee_has_skill') {
            //3.1.x branch did not have author_login column; add appropriate
            if (!isset($data[$i]['author_login'])) {
                if (!isset($admin)) {
                    $count = 0;
                    while (isset($data['users'][$count]) && $data['users'][$count]['user_type'] != 'administrator') {
                        $count++;
                    }
                    if (isset($data['users'][$count])) {
                        $admin = $data['users'][$count]['login'];
                    } else {
                        $admin = '';
                    }
                }
                $data[$i]['author_login'] = $admin;
            }
        } elseif ($table == 'module_hcd_employees') {
            //Replace old 3.1.4 (incompatible) NULL values with zeros
            if ($data[$i]['candidate'] == '') {
                $data[$i]['candidate'] = 0;
            }
        } elseif ($table == 'module_hcd_job_description') {
            //Replace old 3.1.4 (incompatible) NULL values with the default '1'
            if ($data[$i]['employees_needed'] == '') {
                $data[$i]['employees_needed'] = 1;
            }
        } elseif ($table == 'module_hcd_skills') {
            //Add absent categories_ID and module_hcd_skill_categories table if needed
            if (!isset($data[$i]['categories_ID'])) {
                $data[$i]['categories_ID'] = 1;
                $createSkillsCategory = true;
            }
        } else if ($table == 'user_types') {
			if ($data[$i]['user_type'] != "") {
				$data[$i]['name']        = $data[$i]['user_type'];
				$data[$i]['core_access'] = $data[$i]['characteristics'];
				unset($data[$i]['user_type']);
				unset($data[$i]['characteristics']);
			}
        } else if ($table == 'questions') {
            unset($data[$i]['timestamp']);
            if (!isset($data[$i]['lessons_ID'])) {
                $data[$i]['lessons_ID'] = 0;
            }
        } else if ($table == 'tests') {            
            $new_options = unserialize($data[$i]['options']);
            
            $data[$i]['options'] = serialize(array('duration' => ($data[$i]['duration'])?$data[$i]['duration']:$new_options['duration'],
                                                   'redoable' => ($data[$i]['redoable'])?$data[$i]['redoable']:$new_options['redoable'],
                                                   'onebyone' => ($data[$i]['onebyone'])?$data[$i]['onebyone']:$new_options['onebyone'],
                                                   'answers'  => ($data[$i]['answers'])?$data[$i]['answers']:$new_options['answers'],
                                                   'given_answers'     => ($data[$i]['given_answers'])?$data[$i]['given_answers']:$new_options['given_answers'],
                                                   'shuffle_questions' => ($data[$i]['shuffle_questions'])?$data[$i]['shuffle_questions']:$new_options['shuffle_questions'],
                                                   'shuffle_answers'   => ($data[$i]['shuffle_answers'])?$data[$i]['shuffle_answers']:$new_options['shuffle_answers'],
                                                   'general_threshold'   => $new_options['general_threshold'],
                                                   'assign_to_new'   => $new_options['assign_to_new'],
                                                   'automatic_assignment'   => $new_options['automatic_assignment']                        
            ));
            unset($data[$i]['duration']);
            unset($data[$i]['redoable']);
            unset($data[$i]['onebyone']);
            unset($data[$i]['answers']);
            unset($data[$i]['given_answers']);
            unset($data[$i]['shuffle_questions']);
            unset($data[$i]['shuffle_answers']);
                     
        }

        $obsolete_fields = array_diff(array_keys($data[$i]), $table_fields);            //Remove missing fields (usually deprecated fields no longer existing in current version)
        foreach ($obsolete_fields as $value) {
            unset($data[$i][$value]);
        }

        if (isset($data[$i])) {
            foreach ($data[$i] as $key => $value) {
                $data[$i][$key] = addslashes($value);                  //There is a chance that quotes ' are present into the content. we must escape them in order to avoid database errors. We used to have array_walk here, but it caused memory overuse
            }
        }
    }

    return $data;
}

function advanceProgressBar($steps, $message) {
    echo "<script>advanceProgress(".$steps.", '".$message."')</script>";
//    ob_flush();
//    flush();
}

function showProgressBar() {
    echo '
<base href = "'.dirname(dirname('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'])).'/'.'" />
<script src = "js/eFrontScripts.js" ></script>
<link rel = "stylesheet" type = "text/css" href = "css/css_global.php" />
<table id = "popup_table" class = "divPopup" style = "display:none;">
    <tr class = "defaultRowHeight">
        <td class = "topTitle" id = "popup_title"></td>
        <td class = "topTitle" style = "width:1%;"><img src = "images/16x16/error.png" alt = "'._CLOSE.'" name = "" id = "popup_close" title = "'._CLOSE.'" onclick = "eF_js_showDivPopup(\'\', \'\', this.name)"/>
    </td></tr>
    <tr><td colspan = "2" id = "popup_data" style = "vertical-align:top;"></td></tr>
    <tr><td colspan = "2" id = "frame_data" style = "width:100%;height:100%">
        <iframe name = "POPUP_FRAME" id = "popup_frame" src = "about:blank" style = "border-width:0px;width:100%;height:100%;padding:0px 0px 0px 0px">Sorry, but your browser needs to support iframes to see this</iframe>
    </td></tr>
</table>

<div id="dimmer" class = "dimmerDiv" style="display:none;"></div>
<div id = "progress_table" style = "display:none">
<table style = "margin-left:100px;margin-top:20px;" align = "left">
    <tr><td>
        <span id = "border"   style = "position:absolute;text-align:center;width:100px;border:1px solid #d3d3d3;vertical-align:middle;z-index:2;margin-left:auto;margin-right:auto">0%</span>
        <span id = "progress" style = "background-color:#A0BDEF;width:0px;border:1px dotted #d3d3d3;position:absolute;margin-left:auto;margin-right:auto">&nbsp;</span>
    </td></tr>
    <tr><td style = "padding-top:30px"><span id = "message"></span></td></tr>
</table>
</div>
<script>eF_js_showDivPopup("Progress", new Array("300px", "100px", "string"), "progress_table");</script>
<script>
function advanceProgress(x, msg) {
    if (x > 0 && x < 100) {
        obj1 = document.getElementById("progress");
        obj2 = document.getElementById("border");
        current_progress = parseInt(obj1.style.width);
        if (current_progress < 100) {
            obj1.style.width = parseInt(obj1.style.width)+x+"px";
            obj2.innerHTML = parseInt(obj2.innerHTML)+x+"%";
        }
    } else if (x == 100) {
        document.getElementById("progress").style.width = 100 + "px";
        document.getElementById("border").innerHTML     = 100 + "%";
    }
    document.getElementById("message").innerHTML = msg+"...<span style = \'text-decoration:blink\'>.</span>";
}
</script>
';
    ob_flush();
    flush();
    sleep(1);   //use this to make sure that the script initializes correctly, otherwise it may not work
}


?>

