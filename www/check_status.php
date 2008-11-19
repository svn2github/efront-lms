<?php

    $php_version       = explode('.', phpversion());                    //Get PHP version
    $webserver         = explode(' ',$_SERVER['SERVER_SOFTWARE']);      //GET Server information from $_SERVER
    $webserver_type    = explode('/', $webserver[0]);                   //Extract server type from server information (e.g. "apache")
    $webserver_version = explode('.', $webserver_type[1]);              //Extract server version from server information (e.g. "2.2.4")

    $software['system'] = array('name'        => 'Platform', 
                                'installed'   => $webserver[1] ? substr($webserver[1], 1, -1) : 'Unknown',
                                'recommended' => 'Any',
                                'status'      => true,
                                'help'        => 'eFront may be installed on systems running Microsoft Windows and on most Unix and Linux systems');
    $software['PHP']    = array('name'        => 'PHP', 
                                'installed'   => phpversion(),
                                'recommended' => '5.2.0 or newer',
                                'status'      => isset($php_version[0]) && $php_version[0] <= 4 ? false : true,
                                'help'        => 'eFront is designed using PHP version 5.2.0 or higher. Usage of PHP version < 5.1 is not recommended. PHP 4 is not supported');
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
                                   'help'    => 'This extension is mandatory since the system is designed to use UTF-8 character settings');
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
                                   'help'    => 'The system requires MySQL support');
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

    $settings['register_globals']    = array('value'       => $ini_settings['register_globals']['local_value'] ? 'ON' : 'OFF', 
                                             'recommended' => 'OFF',
                                             'status'      => $ini_settings['register_globals']['local_value'] ? 0 : 1,
                                             'name'        => 'register_globals',
                                             'help'        => 'For security reasons, register_globals must be set to OFF');
    $settings['safe_mode']           = array('value'       => $ini_settings['safe_mode']['local_value'] ? 'ON' : 'OFF', 
                                             'recommended' => 'OFF',
                                             'status'      => $ini_settings['safe_mode']['local_value'] ? 0 : 1,
                                             'name'        => 'safe_mode',
                                             'help'        => 'safe_mode should be set to OFF in order for the platform to work correctly');
    $settings['file_uploads']        = array('value'       => $ini_settings['file_uploads']['local_value'] ? 'ON' : 'OFF', 
                                             'recommended' => 'ON',
                                             'status'      => $ini_settings['file_uploads']['local_value'] ? 1 : 0,
                                             'name'        => 'file_uploads',
                                             'help'        => 'File uploads should be turned on');
    $settings['upload_max_filesize'] = array('value'       => $ini_settings['upload_max_filesize']['local_value'], 
                                             'recommended' => '1M - 100M',
                                             'status'      => substr($ini_settings['upload_max_filesize']['local_value'], 0, -1) >= 1 && substr($ini_settings['upload_max_filesize']['local_value'], 0, -1) <= 100 ? 1 : 0,
                                             'name'        => 'upload_max_filesize',
                                             'help'        => 'Uploading maximum file size can be set to the most suitable value');
    $settings['post_max_size']       = array('value'       => $ini_settings['post_max_size']['local_value'], 
                                             'recommended' => 'same or larger than upload_max_file_size',
                                             'status'      => $ini_settings['post_max_size']['local_value'] >= $ini_settings['upload_max_filesize']['local_value'] ? 1 : 0,
                                             'name'        => 'post_max_size',
                                             'help'        => 'Post_max_size should be set at least equal to upload_max_filesize');
    $settings['max_execution_time']  = array('value'       => $ini_settings['max_execution_time']['local_value'], 
                                             'recommended' => '>120',
                                             'status'      => $ini_settings['max_execution_time']['local_value'] >= 120 ? 1 : 0,
                                             'name'        => 'max_execution_time',
                                             'help'        => 'Maximum script execution time can be set to the most suitable value');
    $settings['memory_limit']        = array('value'       => $ini_settings['memory_limit']['local_value'], 
                                             'recommended' => '>32M',
                                             'status'      => substr($ini_settings['memory_limit']['global_value'], 0, -1) >= 32 ? 1 : 0,
                                             'name'        => 'memory_limit',
                                             'help'        => 'Memory limit must be set to a high value, at least 32MB, in order for efront to run');
    $settings['zlib.output_handler'] = array('value'       => $ini_settings['zlib.output_handler']['local_value'], 
                                             'recommended' => 'Off',
                                             'status'      => $ini_settings['zlib.output_handler']['local_value'] ? 0 : 1,
                                             'name'        => 'zlib.output_handler',
                                             'help'        => 'Zlib output handler must not be enabled in order to transparently compress files at run-time');
    $settings['zlib.output_compression'] = array('value'   => $ini_settings['zlib.output_compression']['local_value'], 
                                             'recommended' => 'Off',
                                             'status'      => $ini_settings['zlib.output_compression']['local_value'] ? 0 : 1,
                                             'name'        => 'zlib.output_compression',
                                             'help'        => 'Zlib output compression must not be enabled in order to transparently compress files at run-time');

    $smarty -> assign("T_SETTINGS", $settings);


    $permissions['www/content']                 = array('writable' => is_writable($path.'../www/content') && is_writable($path.'../www/content/lessons') && is_writable($path.'../www/content/admin'),
                                                        'help'     => 'This is the directory where the lesson content resides, and should be writable along with any subfolders');
    $permissions['www/css/custom_css']          = array('writable' => is_writable($path.'../www/css/custom_css'),
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

 /*   $greek_tags = array('greek', 'el_EL', 'el_el', 'Greek', 'gr_gr', 'el_gr', 'el-el', 'el-EL', 'el-gr', 'gr-gr', 'en_US.utf8');
    while (!setlocale(LC_ALL, $tag = $greek_tags[0])) {
        array_shift($greek_tags);
    }
    $locale['greek']   = array('language' => 'greek',                               //To see the system installed locales, type locale-a in command prompt (linux/unix). 
                               'locale'   => sizeof($greek_tags) > 0 ? (setlocale(LC_ALL, $tag)) : '',
                               'help'     => 'Your system should support Greek language in order to use Greek language in the system');
    if ($tag) {
        $file = file_get_contents($path."language/lang-greek.php.inc");
        $file = preg_replace("/(define\(\"_HEADERLANGUAGETAG\",\").*(\"\);)/", '$1'.$tag.'$2', $file);
        file_put_contents($path."language/lang-greek.php.inc", $file);
    }

    $english_tags = array('english', 'en_US', 'en_us', 'English', 'en_EN', 'en_en', 'en-us', 'en-US', 'en-en', 'en-EN', 'en_US.utf8');
    while (!setlocale(LC_ALL, $tag = $english_tags[0])) {
        array_shift($english_tags);
    }
    $locale['english'] = array('language' => 'english',                             
                               'locale'   => sizeof($english_tags) > 0 ? (setlocale(LC_ALL, $tag)) : '',
                               'help'     => 'Your system should support English language in order to use the system');
    if ($tag) {
        $file = file_get_contents($path."language/lang-english.php.inc");
        $file = preg_replace("/(define\(\"_HEADERLANGUAGETAG\",\").*(\"\);)/", '$1'.$tag.'$2', $file);
        file_put_contents($path."language/lang-english.php.inc", $file);
    }
*/
	$languages = array("english","arabic","bulgarian","chinese_traditional","chinese_simplified","croatian","czech","danish","dutch","finnish","french","german","greek","hindi","italian","japanese","norwegian","polish","portuguese","romanian","russian","spanish","swedish");
	foreach ($languages as $value){
		if ($value == "chinese_traditional" || $value == "chinese_simplified") {
			$languagesArray[$value] = "chinese";
		} else {
			$languagesArray[$value] = $value;
		}
	}

	foreach ($languagesArray as $key => $value) {
		$locale[$key]   = array('language' => $key,                               //To see the system installed locales, type locale-a in command prompt (linux/unix).
								'locale'   => (setlocale(LC_ALL, $value)), 
								'help'     => (setlocale(LC_ALL, $value) === false) ? _YOUSHOULDCHANGEHEADERLANGUAGETAG.'&nbsp;'. $key.'&nbsp;'._LANGUAGEFILE : _YOURSYSTEMSUPPORTS .'&nbsp;'.$key);
	}
	
	
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


?>