<?php
/**

 * EfrontConfiguration Class file

 *

 * @package eFront

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/**

 * EfrontConfiguration class

 *

 * This class is used to provide a means of accessing configuration variables. It consists only of static methods.

 * @package eFront

 * @since 3.0

*/
class EfrontConfiguration
{
    /**

     * Array containing the default configuration options

     * @since 3.0

     * @var array

     * @access private

     */
    private static $defaultOptions = array(
        //System settings - security
        'system_email' => 'admin@example.com',
     'file_black_list' => 'php,php3,jsp,asp,cgi,pl,exe,com,bat',
        'file_white_list' => '',
        'ip_black_list' => '',
        'ip_white_list' => '*.*.*.*',
     'logout_redirect' => '',
        'password_length' => 6,
  'autologout_time' => 30,
        'encrypt_url' => 0, //0: don't encrypt. 1: encrypt
        //System settings - language
        'onelanguage' => 0,
        'default_language' => 'english',
     'file_encoding' => 'UTF-8', //UTF7-IMAP for Windows servers who want to support international file names uploading
        //System settings - user activation/registration
        'activation' => 1,
        'mail_activation' => 0,
     'supervisor_mail_activation' => 0,
        'signup' => 1,
        'show_license_note' => 0,
     'insert_group_key' => 1, // 0 means 'no', 1 means 'yes'
  'default_type' => 'student',
        //System settings - 3rd party tools
        'license_server' => 'http://keys.efrontlearning.net/list.php',
        'api' => 1,
  'math_content' => 0,
  'math_server' => 'http://www.imathas.com/cgi-bin/mimetex.cgi',
  'math_images' => 0,
  'phplivedocx_server' => 'https://api.livedocx.com/1.1/mailmerge.asmx?WSDL',
  'phplivedocx_username' => '',
  'phplivedocx_password' => '',
        //Appearance
        'site_name' => _EFRONTNAME,
        'site_motto' => _THENEWFORMOFADDITIVELEARNING,
  'motto_on_header' => 1,
     'lessons_directory' => 1, //0 means 'no', 1 means 'yes', and 2 means 'only after login'
     'collapse_catalog' => 0, // 0 means 'no', 1 means 'yes' and  2 means 'only for lessons'
        'logo' => '',
        'favicon' => '',
        'username_format' => '#surname# #n#. (#login#)', //Possible values: #name#, #n#, #surname#, #login#
        'username_format_resolve'=> 1, //If 2 formatted usernames are the same, include the login too
        'display_empty_blocks' => 1, //0 means 'no', 1 means 'yes'
        //LDAP
        'activate_ldap' => 0,
        'only_ldap' => 0,
        'ldap_base_dn' => '',
        'ldap_bind_dn' => '',
        'ldap_protocol' => 3,
        'ldap_server' => 'ldap://localhost',
        'ldap_password' => '',
        'ldap_port' => 389,
        'ldap_cn' => 'cn',
        'ldap_l' => 'l',
        'ldap_mail' => 'mail',
        'ldap_postaladdress' => 'postaladdress',
        'ldap_preferredlanguage' => 'referredlanguage',
        'ldap_telephonenumber' => 'telephonenumber',
        'ldap_uid' => 'uid',
        //SMTP
        'smtp_host' => 'localhost',
        'smtp_user' => '',
        'smtp_pass' => '',
        'smtp_port' => '25',
        'smtp_auth' => 0,
        'smtp_timeout' => 3,
        //Locale
        'decimal_point' => '.',
        'thousands_sep' => ',',
        'date_format' => 'DD/MM/YYYY',
        'location' => 'Greece',
        'time_zone' => '',
        //PHP
        'max_file_size' => 50000,
        'gz_handler' => 1,
        //Multiple logins
        'multiple_logins' => '',
        'mapped_accounts' => 0, //0: Enabled, 1: disabled for students, 2: disabled for students and professors, 3: disabled for all
        //Global disable
  'disable_projects' => 0,
  'disable_bookmarks' => 0,
  'disable_comments' => 0,
  'disable_online_users' => 0,
  'disable_glossary' => 0,
  'disable_calendar' => 0,
  'disable_surveys' => 0,
  'disable_news' => 0,
  'disable_messages' => 0,
  'disable_forum' => 0,
  'disable_tests' => 0,
  'disable_tooltip' => 0,
  'disable_help' => 0,
        //Social - Facebook settings
        'facebook_api_key' => '',
        'facebook_secret' => '',
        'social_modules_activated' => 63,

        //Payments settings    
        'currency' => 'EUR',
  'currency_order' => 1,
     'paypalbusiness' => '',
        'paypalmode' => 'normal',
        'paypaldebug' => 0,
        'enable_balance' => 1,
     'total_discount' => 0,
        'voucher_discount' => 0,
        'voucher' => '',
        'discount_period' => '',
        'discount_start' => '',

        //Invisible
     'lock_down' => 0,
        'chat_enabled' => 1,
        'zip_method' => 'php',
        'version_key' => '',
     'theme' => 1, //Default theme id, in a clean install this is 1
        'database_version' => G_VERSION_NUM,
  'help_url' => 'http://docs.efrontlearning.net/index.php',

        //Notifications
        'notifications_pageloads' => 10,
        'notifications_messages_per_time' => 5,
        'notifications_max_sent_messages' => 100,

        //Unclassified - deprecated
        'cms_page' => '',
        'css' => '',
     'smarty_cache' => 1, //Whether to cache smarty templates
     'smarty_cache_timeout' => 60 //Default caching time for smarty templates
        );

    /**

    * Get configuration values

    *

    * This function is used to retrieve configuration values.

    * Furthermore, it compares the keys of the $defaultOptions array with

    * The name/value pairs stored in the database. If a default name/value

    * pair is not present in the database, it is created using its default

    * value (unless the whole table is empty, in which case nothing is done)

    * <br>Example:

    * <code>

    * $defaultConfig = EfrontConfiguration :: getValues();

    * </code>

    *

    * @return array The configuration options in name/value pairs

    * @access public

    * @since 3.0

    * @static

    */
    public static function getValues() {
        $options = eF_getTableDataFlat("configuration", "*");
        sizeof($options) > 0 ? $options = array_combine($options['name'], $options['value']) : $options = array();
        foreach (EfrontConfiguration :: $defaultOptions as $key => $value) {
            if (!isset($options[$key])) {
                EfrontConfiguration::setValue($key, $value);
                $options[$key] = $value;
            }
        }
        return $options;
    }
    /**

    * Get default configuration values

    *

    * This function is used to retrieve default configuration values.

    * <br/>Example:

    * <code>

    * $defaultConfig = EfrontConfiguration :: getDefaultValues();

    * </code>

    *

    * @return array The default configuration options

    * @access public

    * @since 3.0

    * @static

    */
    public static function getDefaultValues() {
        return EfrontConfiguration :: $defaultOptions;
    }
    /**

    * Set configuration value

    *

    * This function is used to set a configuration value. Given a name/value pair,

    * this function first checks if it exists in the 'configuration' database table.

    * If so, it updates the variable with the new value, otherwise it inserts a new

    * entry.

    * <br/>Example:

    * <code>

    * $defaultConfig = EfrontConfiguration :: setValue('smtp_host', 'localhost');			//Set the configuration parameter 'smtp_host' to 'localhost'

    * </code>

    * 

    * @param string $name The variable name

    * @param string $value The variable value

    * @return boolean The query result

    * @access public

    * @since 3.0

    * @static

    */
    public static function setValue($name, $value) {
        $result = eF_getTableData("configuration", "value", "name = '$name'");
        if (sizeof($result) > 0) {
            $result = eF_updateTableData("configuration", array('value' => $value), "name = '$name'");
        } else {
            $result = eF_insertTableData("configuration", array('name' => $name, 'value' => $value), "name = '$name'");
        }
        $GLOBALS['configuration'][$name] = $value;
        if ($result) {
         $GLOBALS['configuration'][$name] = $value; //Reset existing value
        }
        return $result;
    }
}
?>
