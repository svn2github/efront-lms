<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$php_version = explode('.', phpversion()); //Get PHP version
$webserver = explode(' ',$_SERVER['SERVER_SOFTWARE']); //GET Server information from $_SERVER
$webserver_type = explode('/', $webserver[0]); //Extract server type from server information (e.g. "apache")
$webserver_version = explode('.', $webserver_type[1]); //Extract server version from server information (e.g. "2.2.4")

$software['system'] = array('name' => 'Platform',
                            'installed' => $webserver[1] ? substr($webserver[1], 1, -1) : 'Unknown',
                            'recommended' => 'Any',
                            'status' => true,
                            'help' => 'The platform may be installed on systems running Microsoft Windows and on most Unix and Linux systems');

$software['PHP'] = array('name' => 'PHP',
                            'installed' => phpversion(),
                            'recommended' => '5.2.0 or newer',
                            'status' => version_compare(phpversion(), "5.2.0") < 0 ? false : true,
                            'help' => 'The platform is designed using PHP version 5.2.0 or higher. Usage of PHP version < 5.1 is not recommended. PHP 4 is not supported');
$software['apache'] = array('name' => 'Web server',
                            'installed' => $webserver[0],
                            'recommended' => 'Apache 2.x or newer',
                            'status' => !strcasecmp($webserver_type[0], 'apache') && $webserver_version[0] >= 2 ? true : false,
                            'help' => 'The platform is tested and known to work well with Apache 2 web server');
/*

 $software['MySQL']  = array('name'        => 'MySQL DBMS',

 'installed'   => print_r(mysql_connect()),

 'recommended' => '4.1.x, 5.x or newer',

 'status'      => false,

 'help'        => 'The platform is designed for optimal performance with MySQL 5, but it will work with MySQL 4 as well. No other databases are supported.');

 */
$smarty -> assign("T_SOFTWARE", $software); //Software variables are Optional
$extensions = get_loaded_extensions();
$mandatory['mbstring'] = array('enabled' => in_array('mbstring', $extensions),
                               'name' => 'MultiByte (UTF) Support',
                               'help' => 'This extension is mandatory since the system is designed to use UTF-8 character settings');
$mandatory['session'] = array('enabled' => in_array('session', $extensions),
                               'name' => 'Session Support',
                               'help' => 'Sessions are mandatory in order for the system to operate');
$mandatory['iconv'] = array('enabled' => in_array('iconv', $extensions),
                               'name' => 'Iconv Functions',
                               'help' => 'Iconv extension is needed in order to perform localization conversions');
$mandatory['pcre'] = array('enabled' => in_array('pcre', $extensions),
                               'name' => 'POSIX Regular expressions',
                               'help' => 'Regular expressions are used thoroughly throughout the system');
$mandatory['mysql'] = array('enabled' => in_array('mysql', $extensions),
                               'name' => 'MySQL support',
                               'help' => 'The system requires MySQL support');
$mandatory['zip'] = array('enabled' => in_array('zip', $extensions),
                               'name' => 'ZIP extension',
                               'help' => 'ZIP extension is needed in order to use built-in compression functions');
foreach ($mandatory as $key => $value) {
    if (isset($exclude_normal) && $exclude_normal && $value['enabled']) { //Use $exclude_normal in order to not list sections without problem
        unset($mandatory[$key]);
    }
}
$smarty -> assign("T_MANDATORY", $mandatory);







$optional['gd'] = array('enabled' => in_array('gd', $extensions),
                          'name' => 'GD Libraries functions',
                          'help' => 'GD Libraries extension is needed for advanced image manipulation');
$optional['soap'] = array('enabled' => in_array('soap', $extensions),
                          'name' => 'SOAP libraries',
                          'help' => 'SOAP libraries are used for creating PDF certificates using phplivedocx');
$optional['openssl'] = array('enabled' => in_array('openssl', $extensions),
                          'name' => 'OpenSSL libraries',
                          'help' => 'OpenSSL libraries are used for creating PDF certificates using phplivedocx');
foreach ($optional as $key => $value) {
    if (isset($exclude_normal) && $exclude_normal && $value['enabled']) { //Use $exclude_normal in order to not list sections without problem
        unset($optional[$key]);
    }
}
$smarty -> assign("T_OPTIONAL", $optional);

$ini_settings = ini_get_all();
$settings_mandatory['session.save_path'] = array('value' => $ini_settings['session.save_path']['local_value'],
                                                 'recommended' => _NOTEMPTY,
                                                 'status' => $ini_settings['session.save_path']['local_value'] != "" ? 1 : 0,
                                                 'name' => 'session.save_path',
                                                 'help' => 'session.save_path must be set');
$ini_settings['register_globals']['local_value'] && strtolower($ini_settings['register_globals']['local_value']) != 'off' ? $registerGlobals = false : $registerGlobals = true;
$settings_mandatory['register_globals'] = array('value' => $registerGlobals ? 'OFF' : 'ON',
                                                 'recommended' => 'OFF',
                                                 'status' => $registerGlobals,
                                                 'name' => 'register_globals',
                                                 'help' => 'For security reasons, register_globals must be set to OFF');
$ini_settings['magic_quotes_gpc']['local_value'] && strtolower($ini_settings['magic_quotes_gpc']['local_value']) != 'off' ? $magicQuotes = false : $magicQuotes = true;
$settings_mandatory['magic_quotes_gpc'] = array('value' => $magicQuotes ? 'OFF' : 'ON',
                                                 'recommended' => 'OFF',
                                                 'status' => $magicQuotes,
                                                 'name' => 'magic_quotes_gpc',
                                                 'help' => 'magic_quotes_gpc is deprecated and must be set to OFF in order for the platform  to work');
$ini_settings['magic_quotes_runtime']['local_value'] && strtolower($ini_settings['magic_quotes_runtime']['local_value']) != 'off' ? $magicQuotesRuntime = false : $magicQuotesRuntime = true;
$settings['magic_quotes_runtime'] = array('value' => $magicQuotesRuntime ? 'OFF' : 'ON',
                                          'recommended' => 'OFF',
                                          'status' => $magicQuotesRuntime,
                                          'name' => 'magic_quotes_runtime',
                                          'help' => 'magic_quotes_runtime is deprecated and must be set to OFF in order for the platform  to work');
$settings['file_uploads'] = array('value' => $ini_settings['file_uploads']['local_value'] ? 'ON' : 'OFF',
                                         'recommended' => 'ON',
                                         'status' => $ini_settings['file_uploads']['local_value'] ? 1 : 0,
                                         'name' => 'file_uploads',
                                         'help' => 'File uploads should be turned on');
$settings['upload_max_filesize'] = array('value' => $ini_settings['upload_max_filesize']['local_value'],
                                         'recommended' => '50M',
                                         'status' => substr($ini_settings['upload_max_filesize']['local_value'], 0, -1) >= 1 ? 1 : 0,
                                         'name' => 'upload_max_filesize',
                                         'help' => 'Uploading maximum file size can be set to the most suitable value');
$settings['post_max_size'] = array('value' => $ini_settings['post_max_size']['local_value'],
                                         'recommended' => $ini_settings['upload_max_filesize']['local_value'],
                                         'status' => mb_substr($ini_settings['post_max_size']['local_value'], 0, -1) >= mb_substr($ini_settings['upload_max_filesize']['local_value'], 0, -1) ? 1 : 0,
                                         'name' => 'post_max_size',
                                         'help' => 'Post_max_size should be set at least equal to upload_max_filesize');
$settings['max_execution_time'] = array('value' => $ini_settings['max_execution_time']['local_value'],
                                         'recommended' => '>120',
                                         'status' => $ini_settings['max_execution_time']['local_value'] >= 120 ? 1 : 0,
                                         'name' => 'max_execution_time',
                                         'help' => 'Maximum script execution time can be set to the most suitable value');
$settings['memory_limit'] = array('value' => $ini_settings['memory_limit']['local_value'],
                                         'recommended' => '128M',
                                         'status' => (substr($ini_settings['memory_limit']['local_value'], 0, -1) >= 32) || ($ini_settings['memory_limit']['local_value'] == -1) ? 1 : 0,
                                         'name' => 'memory_limit',
                                         'help' => 'Memory limit must be set to a high value, at least 32MB, in order for the system to run');
$settings['zlib.output_handler'] = array('value' => $ini_settings['zlib.output_handler']['local_value'],
                                         'recommended' => 'Off',
                                         'status' => $ini_settings['zlib.output_handler']['local_value'] ? 0 : 1,
                                         'name' => 'zlib.output_handler',
                                         'help' => 'Zlib output handler must not be enabled in order to transparently compress files at run-time');
$settings['zlib.output_compression'] = array('value' => $ini_settings['zlib.output_compression']['local_value'],
                                         'recommended' => 'Off',
                                         'status' => $ini_settings['zlib.output_compression']['local_value'] ? 0 : 1,
                                         'name' => 'zlib.output_compression',
                                         'help' => 'Zlib output compression must not be enabled in order to transparently compress files at run-time');
$settings['allow_url_fopen'] = array('value' => $ini_settings['allow_url_fopen']['local_value'],
                                         'recommended' => 'On',
                                         'status' => $ini_settings['allow_url_fopen']['local_value'] ? 1 : 0,
                                         'name' => 'allow_url_fopen',
                                         'help' => 'allow_url_fopen must be enabled in order to use phplivedocx to create PDF certificates');
foreach ($settings_mandatory as $key => $value) {
    if (isset($exclude_normal) && $exclude_normal && $value['status']) { //Use $exclude_normal in order to not list sections without problem
        unset($settings_mandatory[$key]);
    }
}

$smarty -> assign("T_SETTINGS_MANDATORY", $settings_mandatory);
foreach ($settings as $key => $value) {
    if (isset($exclude_normal) && $exclude_normal && $value['status']) { //Use $exclude_normal in order to not list sections without problem
        unset($settings[$key]);
    }
}
$smarty -> assign("T_SETTINGS", $settings);

$permissions['www/content'] = array('writable' => is_writable($path.'../www/content'),
                                                    'help' => 'This is the directory where the lesson content resides, and should be writable along with any subfolders');
$permissions['www/themes'] = array('writable' => is_writable($path.'../www/themes') && local_checkThemesWritable(),
                                                    'help' => 'This directory is where custom Themes are uploaded');
$permissions['www/modules'] = array('writable' => is_writable($path.'../www/modules'),
                                                    'help' => 'This is the directory where the modules are installed');
$permissions['www/editor/tiny_mce'] = array('writable' => is_writable($path.'../www/editor/tiny_mce'),
                                                    'help' => 'This is the directory where compressor writes a zip file');
$permissions['www/phplivedocx/samples/mail-merge/convert'] = array('writable' => is_writable($path.'../www/phplivedocx/samples/mail-merge/convert'),
                                                    'help' => 'This is the directory where the pdf certificates are temporarily written');
$permissions['libraries'] = array('writable' => is_writable($path),
                                                    'help' => 'libraries directory should be writable only during the installation process');
$permissions['libraries/language'] = array('writable' => is_writable($path.'language'),
                                                    'help' => 'This directory needs to be writable, in order to be able to upload new language files or modify existing ones');
$permissions['libraries/smarty/themes_cache']= array('writable' => is_writable($path.'smarty/themes_cache'),
                                                    'help' => 'This directory needs to writable, in order for smarty to compile templates');
/*

$permissions['libraries/language/lang-english.php.inc'] = array('writable' => is_writable($path.'language/lang-english.php.inc'),

                                                    'help'     => 'This file needs to writable, in order to set locales correctly');

*/
$permissions['backups'] = array('writable' => is_writable($path.'../backups'),
                                                    'help' => 'In this directory all the system backups are stored');
$permissions['upload'] = array('writable' => is_writable($path.'../upload'),
                                                    'help' => 'This is the directory where user related files are stored');
$permissions['session path'] = array('writable' => is_writable($ini_settings['session.save_path']['local_value']),
              'help' => 'This is the directory where session variables are stored');
if (file_exists($path.'phplivedocx_config.php')) {
 $permissions['libraries/phplivedocx_config.php'] = array('writable' => is_writable($path.'phplivedocx_config.php'),
                                                    'help' => 'This file needs to be writable, in order to save phplivedocx account');
}
foreach ($permissions as $key => $value) {
    if (isset($exclude_normal) && $exclude_normal && $value['writable']) { //Use $exclude_normal in order to not list sections without problem
        unset($permissions[$key]);
    }
}
$smarty -> assign("T_PERMISSIONS", $permissions);
$pear['PEAR.php'] = array('exists' => ($f = fopen ('PEAR.php', 'r', true)) ? true : false,
                                                         'help' => 'PEAR libraries are mandatory in order for the system to function');
$pear['HTML/QuickForm.php'] = array('exists' => ($f = fopen ('HTML/QuickForm.php', 'r', true)) ? true : false,
                                                         'help' => 'This PEAR package is mandatory and the system will not work without it');
$pear['HTML/QuickForm/Renderer/ArraySmarty.php'] = array('exists' => ($f = fopen ('HTML/QuickForm/Renderer/ArraySmarty.php', 'r', true)) ? true : false,
                                                         'help' => 'This PEAR package is mandatory and the system will not work without it');
$pear['Mail.php'] = array('exists' => ($f = fopen ('Mail.php', 'r', true)) ? true : false,
                                                         'help' => 'This PEAR Mail package is is needed in order for the system to be able to send emails');
$pear['Net/SMTP.php'] = array('exists' => ($f = fopen ('Net/SMTP.php', 'r', true)) ? true : false,
                                                         'help' => 'This PEAR Net_SMTP package is is needed in order for the system to be able to send emails');
$pear['Net/Socket.php'] = array('exists' => ($f = fopen ('Net/Socket.php', 'r', true)) ? true : false,
                                                         'help' => 'This PEAR Net_Socket package is is needed in order for the system to be able to send emails');
fclose($f);

foreach ($pear as $key => $value) {
    if (isset($exclude_normal) && $exclude_normal && $value['exists']) { //Use $exclude_normal in order to not list sections without problem
        unset($pear[$key]);
    }
}
$smarty -> assign("T_PEAR", $pear);
/*

$languages = array("english","arabic","bulgarian","chinese_traditional","chinese_simplified","croatian","czech","danish","dutch","finnish","french","german","greek","hindi","italian","japanese","norwegian","polish","portuguese","romanian","russian","spanish","swedish",

				   "albanian","catalan","brazilian","filipino","galician","georgian","hebrew","hungarian","indonesian","latvian","lithuanian","persian","serbian","slovak","slovenian","thai","turkish","ukrainian","vietnamese");

sort($languages);

foreach ($languages as $value){

	if ($value == "chinese_traditional" || $value == "chinese_simplified") {

		$languagesArray[$value] = "chinese";

	} elseif ($value == "brazilian") {

		$languagesArray[$value] = "portuguese";

	} elseif ($value == "turkish") {

			$languagesArray[$value] = "english";

	} else {

		$languagesArray[$value] = $value;

	}

}



foreach ($languagesArray as $key => $value) {

	$languageFileContents = file_get_contents($path."language/lang-$key.php.inc");

	preg_match('#.*"_HEADERLANGUAGETAG","(.*)".*#', $languageFileContents, $matches);

	$value = $matches[1];



	$locale[$key]   = array('language' => $key,                               //To see the system installed locales, type locale-a in command prompt (linux/unix).

							'locale'   => (setlocale(LC_ALL, $value)),

							'help'     => (setlocale(LC_ALL, $value) === false) ? _YOUSHOULDCHANGEHEADERLANGUAGETAG.'&nbsp;'. $key.'&nbsp;'._LANGUAGEFILE : _YOURSYSTEMSUPPORTS .'&nbsp;'.$key);

}

//setlocale(LC_ALL, _HEADERLANGUAGETAG);



$correctLocale = $incorrectLocale = array();

foreach ($locale as $key => $value) {

    $value['locale'] ? $correctLocale[$key] = $locale[$key] : $incorrectLocale[$key] = $locale[$key];

}

$smarty -> assign("T_CORRECT_LOCALE", $correctLocale);

$smarty -> assign("T_INCORRECT_LOCALE", $incorrectLocale);

*/
$install = true; //The install variable will be used to check whether any mandatory setting is not met.
foreach ($mandatory as $key => $value) { //Check mandatory PHP extensions
    if (!$value['enabled']) {
        $install = false;
    }
}
foreach ($permissions as $key => $value) { //Check filesystem permissions
    if (!$value['writable']) {
        $install = false;
    }
}
foreach ($pear as $key => $value) { //Check PEAR packages
    if (!$value['exists']) {
        $install = false;
    }
}
if ($php_version[0] <= 4) { //PHP 4 will not run
    $install = false;
}
function local_checkThemesWritable() {
    $writable = true;
    if (class_exists('FileSystemTree')) {
     $fs = new FileSystemTree(G_ROOTPATH."www/themes/", true);
     foreach ($fs -> iterator as $key => $value) {
         if (!$value -> isWritable() || (is_dir($key."/external/") && !local_checkWritableRecursive($key."/external/") )) {
             $writable = false;
         }
     }
    }
    return $writable;
}
function local_checkWritableRecursive($path) {
    $writable = true;
    if (!is_writable($path)) {
        $writable = false;
    } else {
     $fs = new FileSystemTree($path);
     $fs -> iterator -> rewind();
     while ($fs -> iterator -> valid() && $writable) {
         if (!$fs -> iterator -> current() -> isWritable()) {
             $writable = false;
         }
         $fs -> iterator -> next();
     }
    }
    return $writable;
}
