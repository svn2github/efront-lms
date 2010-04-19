<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/**

 * This function is called by the installation script. Add here calls to any functions you wish to be ran 

 * right after the installation finishes

 * 

 * @since 3.6.2

 */
function runPostInstallationFunctions() {
 //addRestrictedAdministrator();
}
function addRestrictedAdministrator() {
 $values['core_access'] = array("configuration" => 'hidden',
            "user_types" => 'hidden',
           "languages" => 'hidden',
           "version_key" => 'hidden',
           "maintenance" => 'hidden',
           "modules" => 'hidden');
 $fields = array("name" => 'Siteadmin',
     "basic_user_type" => 'administrator',
     "core_access" => serialize($values['core_access']));
 eF_insertTableData("user_types", $fields);
}
?>
