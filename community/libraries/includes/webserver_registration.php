<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if ($GLOBALS['configuration']['webserver_registration']) {

 eval('$usernameVar='.$GLOBALS['configuration']['username_variable'].';');
 $user_data = array("login" => $usernameVar,
        "password" => $usernameVar, //same password by default
        "name" => 'sample',
        "surname" => 'user',
        "email" => $GLOBALS['configuration']['system_email'],
        "pending" => 0,
        "active" => 1,
        "languages_NAME" => $GLOBALS['configuration']['default_language']);
 //Check the user_type. If it's an id, it means that it's not one of the basic user types; so derive the basic user type and populate the user_types_ID field
 $defaultUserType = $GLOBALS['configuration']['default_type'];
 if (is_numeric($defaultUserType)) {
  $result = eF_getTableData("user_types", "id, basic_user_type", "id=".$defaultUserType);
  if (sizeof($result) > 0) {
   $user_data['user_type'] = $result[0]['basic_user_type'];
   $user_data['user_types_ID'] = $result[0]['id'];
  } else {
   $user_data['user_type'] = 'student';
  }
 } else {
  $user_data['user_type'] = $defaultUserType;
  $user_data['user_types_ID'] = 0;
 }

 //Must set the user object finally!
 $user = EfrontUser :: createUser($user_data);

}

?>
