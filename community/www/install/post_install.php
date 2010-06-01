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
 addRestrictedAdministrator();
 customizeSite();
 ReplaceProfessorUser();
 removeRssEntry();
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
function customizeSite() {
 EfrontConfiguration :: setValue('site_name', 'ACME');
 EfrontConfiguration :: setValue('site_motto', 'Corporate Learning Portal');
 EfrontConfiguration :: setValue('disable_help', '1');
}
function ReplaceProfessorUser() {
 $professorData = array('login' => 'coursemanager',
                           'password' => 'coursemanager',
                           'email' => $GLOBALS['configuration']['system_email'],
                           'name' => 'Default',
                           'surname' => 'Course manager',
                           'languages_NAME' => 'english',
                           'active' => '1',
                           'user_type'=> 'professor',
                           'additional_accounts' => serialize(array('admin', 'student')));
 $professor = EfrontUser :: createUser($professorData);
 $oldUser = EfrontUserFactory::factory('professor');
 $oldUser -> delete();
}
function removeRssEntry() {
 eF_executeNew("drop table if exists module_rss_feeds");
 eF_executeNew("CREATE TABLE module_rss_feeds(id int(11) not null auto_increment primary key, title varchar(255), url text not null, active int(11) not null default 1, only_summary int(11) default 0, lessons_ID int(11) default -1)");
 eF_updateTableData("modules", array("active" => 0), "className='module_rss'");
}
?>
