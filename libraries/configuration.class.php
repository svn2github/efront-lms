<?php
/**
 * EfrontConfiguration Class file
 *
 * @package eFront
*/

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
        'activation'             => 1,
        'mail_activation'        => 0,
        'signup'                 => 1,
        'onelanguage'            => 0,
        'default_language'       => 'english',
        'file_black_list'        => 'php,php3,jsp,asp,cgi,pl,exe,com,bat',
        'file_white_list'        => '',
        'ip_black_list'          => '',
        'ip_white_list'          => '*.*.*.*',
        'max_file_size'          => 50000,
        'api'                    => 1,
		'math_content'           => 1,
		'lessons_directory'      => 1,
        'system_email'           => 'admin@example.com',
        'logo'                   => '',
    	'file_encoding'		     => 'UTF7-IMAP',
        'site_name'				 => _EFRONT,
        'site_moto'				 => _THENEWFORMOFADDITIVELEARNING,
    	'logout_redirect'	     => '',
    
        'cms_page'               => '',
        'css'                    => '',
        'show_footer'            => 1,

        'smtp_host'              => 'localhost',
        'smtp_user'              => '',
        'smtp_pass'              => '',
        'smtp_port'              => '25',
        'smtp_auth'              => 0,
        'smtp_timeout'           => 3,

        'activate_ldap'          => 0,
        'only_ldap'              => 0,
        'ldap_base_dn'           => '',
        'ldap_bind_dn'           => '',
        'ldap_protocol'          => 3,
        'ldap_server'            => 'ldap://localhost',
        'ldap_password'          => '',
        'ldap_port'              => 389,
        'ldap_cn'                => 'cn',
        'ldap_l'                 => 'l',
        'ldap_mail'              => 'mail',
        'ldap_postaladdress'     => 'postaladdress',
        'ldap_preferredlanguage' => 'referredlanguage',
        'ldap_telephonenumber'   => 'telephonenumber',
        'ldap_uid'               => 'uid',      
        'currency'               => 'EUR',
        'decimal_point'          => '.',
        'thousands_sep'          => ',',
        'date_format'            => 'DD/MM/YYYY',
        'location'               => 'Greece',
        'time_zone'              => 'GMT+2',
        'memory_limit'           => '-1'        
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
        $options = array_combine($options['name'], $options['value']);
        
        foreach (EfrontConfiguration :: $defaultOptions as $key => $value) {
            if (!isset($options[$key]) && sizeof($options) > 0) {
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
        $value  = addslashes($value);
        $result = eF_getTableData("configuration", "value", "name = '$name'");
        if (sizeof($result) > 0) {
            $result = eF_updateTableData("configuration", array('value' => $value), "name = '$name'");
        } else {
            $result = eF_insertTableData("configuration", array('name' => $name, 'value' => $value), "name = '$name'");
        }
        
        return $result;
    }
    
    
}
?>