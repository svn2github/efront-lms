<?php

/*

 * Class defining the new module

 * Its name should be the same as the one provided in the module.xml file

 */
class module_administrator_tools extends EfrontModule {
 /*

	 * Mandatory function returning the name of the module

	 * @return string the name of the module

	 */
 public function getName() {
  return _MODULE_ADMINISTRATOR_TOOLS;
 }
 /*

	 * Mandatory function returning an array of permitted roles from the set {"administrator", "professor", "student"}

	 *

	 * @return array of eFront user roles that this module applies for

	 */
 public function getPermittedRoles() {
  return array("administrator");
 }
 public function getModuleJs() {
  if (strpos(decryptUrl($_SERVER['REQUEST_URI']), $this -> moduleBaseUrl) !== false) {
   return $this->moduleBaseDir."module_administrator_tools.js";
  }
 }
 public function addScripts() {
  return array("scriptaculous/effects", "scriptaculous/controls");
 }
 /*

	 * Function to be executed when the module is installed to an eFront system

	 * Example implementation:

	 *

	 * public function onInstall() {

	 *   return eF_executeNew("CREATE TABLE module_mymodule (

	 *                    id int(11) NOT NULL auto_increment,

	 *                    name text not null,

	 *                    PRIMARY KEY  (id)

	 *                   ) DEFAULT CHARSET=utf8;");

	 * }

	 * @return the result (true/false) of any module installation operations

	 */
 public function onInstall() {
  return true;
 }
 /*

	 * Function to be executed when the module is removed from an eFront system

	 * Example implementation:

	 *

	 * public function onUninstall() {

	 *   return eF_executeNew("DROP TABLE module_mymodule;");

	 * }

	 *

	 * @return the result (true/false) of any removal operations

	 */
 public function onUninstall() {
  //eF_executeNew("DROP TABLE ;");
 }
 /*

	 * Get Navigational links for the top of the independent module page(s)

	 * Get information in an array of sub-arrays with fields:

	 * 'title': the title to appear on the link

	 * 'image': the image to appear (if image inside module folder then use ($this -> moduleBaseDir) . 'imageFileName' -TODO

	 * 'link': the url of the page to be from this link

	 * Each sub-array represents a different link. Between them the "&raquo;" character is automatically inserted by the system

	 * Example implementation:

	 *

	 *  public function getNavigationLinks() {

	 *          $currentUser = $this -> getCurrentUser();

	 *          return array (array ('title' => _HOME, 'link'  => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),

	 *                       array ('title' => _FAQ, 'link'  => $this -> moduleBaseUrl));

	 *  }

	 *

	 * @return array describing the header navigational links for the module pages

	 */
 public function getNavigationLinks() {
  $currentUser = $this -> getCurrentUser();
  return array (array ('title' => _HOME, 'link' => $currentUser -> getRole() . ".php"),
  array ('title' => _MODULE_ADMINISTRATOR_TOOLS, 'link' => $this -> moduleBaseUrl));
 }
 public function getCenterLinkInfo() {
  $currentUser = $this -> getCurrentUser();
  if ($currentUser -> getType() == "administrator") {
   return array('title' => _MODULE_ADMINISTRATOR_TOOLS,
                         'image' => $this -> moduleBaseDir . 'images/tools.png',
                         'link' => $this -> moduleBaseUrl);
  }
 }
 /*

	 * This is the function for the php code of the MAIN module pages (namely the ones

	 * called from the url:    $this->moduleBaseUrl . "&...."

	 *

	 * The global smarty variable may also be used here and in conjunction

	 * with the getSmartyTpl() function, thus using php+smarty to display the page

	 *

	 * Rules:

	 * - You are not allowed to use the $_GET['ctg'] and $_GET['op'] variables

	 * - You should use the $this -> moduleBaseUrl variable to reference the module basic url

	 * - You should use the $this -> moduleBaseDir variable to reference the module basic directory

	 *

	 * Tips:

	 * - Use the $this -> getSmartyVar() function to utilize the global smarty variable.

	 * - Use the $this -> setMessageVar($message, $message_type) function to export information to eFront users with header messages

	 *

	 * @return the result of any module operations in boolean form (true/false)

	 */
 public function getModule() {
  try {
   $smarty = $this -> getSmartyVar();
   $currentUser = $this -> getCurrentUser();
   if ($currentUser->user['user_types_ID']) {
    eF_redirect("administrator.php?message="._MODULE_ADMINISTRATOR_TOOLS_YOUCANNOTACCESSTHISSECTION);
   }
   $options = array(
    0 => array('image' => '16x16/users.png', 'title' => _USERS, 'link' => $this -> moduleBaseUrl.'&do=user', 'selected' => !isset($_GET['do']) || $_GET['do'] == 'user' ? true : false),
    1 => array('image' => '16x16/courses.png', 'title' => _LEARNING, 'link' => $this -> moduleBaseUrl.'&do=learning', 'selected' => $_GET['do'] == 'learning' ? true : false),
    2 => array('image' => '16x16/tools.png', 'title' => _SYSTEM, 'link' => $this -> moduleBaseUrl.'&do=system', 'selected' => $_GET['do'] == 'system' ? true : false)
   );
   $smarty -> assign("T_TABLE_OPTIONS", $options);
   if ($_GET['do'] == 'learning') {
    $this->doGlobalLessonSettings();
    $this->doCourseLessonUsers();
    $this->doSynchronizeCourseLessons();
   } else if ($_GET['do'] == 'system') {
    $this->doChangeFileEncoding();
    $this->doSqlInterface();
   } else if ($_GET['do'] == 'enterprise') {
   } else {
    $this->doImpersonate();
    $this->doChangeUserType();
    $this->doChangeLogin();
   }
  } catch (Exception $e) {
   $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
   $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
   $message_type = 'failure';
   $this -> setMessageVar($message, $message_type);
  }
 }
    public function getModuleIcon() {
        return $this -> moduleBaseLink . 'images/tools.png';
    }
 private function doChangeLogin() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();
  $form = new HTML_QuickForm("change_login_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_administrator_tools&do=user", "", null, true);
  $form -> addElement('static', 'sidenote', '<img id = "module_administrator_tools_busy" src = "images/16x16/clock.png" style="display:none;" alt = "'._LOADING.'" title = "'._LOADING.'"/>');
  $form -> addElement('text', 'selection_user', _MODULE_ADMINISTRATOR_TOOLS_SELECTUSERTOCHANGELOGINFOR, 'id = "module_administrator_tools_autocomplete_users" class = "autoCompleteTextBox" style = "width:400px"' );
  $form -> addElement('static', 'autocomplete_note', _STARTTYPINGFORRELEVENTMATCHES);
  $form -> addElement('text', 'new_login', _MODULE_ADMINISTRATOR_TOOLS_NEWLOGIN, 'class = "inputText"');
  $form -> addElement('hidden', 'users_LOGIN', '' , 'id="module_administrator_tools_users_LOGIN"');
  $form -> addRule('selection_user', _THEFIELD.' "'._USER.'" '._ISMANDATORY, 'required', null, 'client');
  $form -> addRule('users_LOGIN', _MODULE_ADMINISTRATOR_TOOLS_THISUSERWASNOTFOUND, 'required', null, 'client');
  $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
  if ($form -> isSubmitted() && $form -> validate()) {
   try {
    $values = $form -> exportValues();
    if (!$values['new_login']) {
     throw new Exception(_MODULE_ADMINISTRATOR_TOOLS_YOUMUSTDEFINEUSER);
    }
    $user = EfrontUserFactory::factory($values['users_LOGIN']);
    try {
     $existingUser = true;
     if (strcasecmp($values['new_login'], $values['users_LOGIN']) === 0) { //Allow changing same user, for case conversions etc
      $existingUser = false;
     } else {
      $newUser = EfrontUserFactory::factory($values['new_login']);
     }
    } catch (Exception $e) {
     $existingUser = false;
    }
    if ($existingUser) {
     throw new Exception(_MODULE_ADMINISTRATOR_TOOLS_USERALREADYEXISTS);
    }
    $existingTables = $GLOBALS['db'] -> GetCol("show tables");
    $views = $GLOBALS['db'] -> GetCol("show tables like '%_view'");
    $errors = array();
    foreach ($existingTables as $table) {
     try {
      if (!in_array($table, $views)) {
       $this -> changeLogin($table, $values['users_LOGIN'], $values['new_login']);
      }
     } catch (Exception $e) {
      $errors[] = $e -> getMessage();
     }
    }
    if (function_exists('apc_delete')) {
     apc_delete(G_DBNAME.':_usernames');
    }
    if (empty($errors)) {
     $message = _OPERATIONCOMPLETEDSUCCESSFULLY;
     $message_type= 'success';
    } else {
     $message = _MODULE_ADMINISTRATOR_TOOLS_OPERATIONCOMPLETEDSUCCESSFULLYBUTHEFOLLOWINGTABLESCOULDNOTBEUPDATED.': <br>'.implode("<br>", $errors);
     $message_type= 'failure';
    }
   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
   }
   $this -> setMessageVar($message, $message_type);
  }
  $smarty -> assign("T_TOOLS_FORM", $form -> toArray());
  try {
   if (isset($_GET['ajax']) && isset($_GET['user']) && eF_checkParameter($_GET['user'], 'login')) {
    $user = EfrontUserFactory::factory($_GET['user']);
    echo json_encode(array('status' => 1, 'supervisors' => $supervisors, 'supervisor_names' => $supervisorNames));
    exit;
   } elseif (isset($_GET['ajax']) && $_GET['ajax'] == 'fix_case') {
    $existingTables = $GLOBALS['db'] -> GetCol("show tables");
    $views = $GLOBALS['db'] -> GetCol("show tables like '%_view'");
    $users = eF_getTableDataFlat("users", "login");
    $errors = array();
    foreach ($existingTables as $table) {
     $t = microtime(true);
     try {
      if (!in_array($table, $views)) {
       $fields = $GLOBALS['db'] -> GetCol("describe $table");
       foreach ($users['login'] as $key => $login) {
        foreach ($fields as $value) {
         if (stripos($value, 'login') !== false) {
          eF_executeNew("update $table set $value='$login' where $value='$login'");
         }
        }
        if ($table == 'f_personal_messages') {
         eF_updateTableData($table, array("sender" => $login), "sender = '".$login."'");
        }
        if ($table == 'notifications' || $table == 'sent_notifications') {
         eF_updateTableData($table, array("recipient" => $login), "recipient = '".$login."'");
        }
        if ($table == 'surveys' || $table == 'module_hcd_events') {
         eF_updateTableData($table, array("author" => $login), "author = '".$login."'");
        }
       }
      }
     } catch (Exception $e) {
      $errors[] = $e -> getMessage();
     }
     //pr("Time for $table: ".(microtime(true)-$t));flush();ob_flush();
    }
    if (function_exists('apc_delete')) {
     apc_delete(G_DBNAME.':_usernames');
    }
    echo json_encode(array('status' => 1));
    exit;
   }
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
 }
 private function doImpersonate() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();
  if (isset($_POST['submit_impersonate'])) {
   try {
    $user = EfrontUserFactory::factory($_POST['autocomplete_impersonate_user']);
    if ($user->user['user_type'] == 'administrator') {
     throw new Exception(_MODULE_ADMINISTRATOR_TOOLS_YOUCANTIMPERSONATEADMIN);
    } elseif (!$user->user['active'] || $user->user['archive']) {
     throw new Exception(_MODULE_ADMINISTRATOR_TOOLS_YOUCANTIMPERSONATEINACTIVEUSER);
    } else {
     $user->login($user->user['password'], true);
     eF_redirect("userpage.php");
    }
   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
    $this -> setMessageVar($message, $message_type);
   }
  }
 }
 private function doGlobalLessonSettings() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();
  $lessonSettings = $this -> getLessonSettings();
  $smarty -> assign("T_LESSON_SETTINGS", $lessonSettings);
  $smarty -> assign("T_LESSON_SETTINGS_GROUPS", array(1 => _LESSONOPTIONS, 2 => _LESSONMODULES, 3 => _MODULES));
  try {
   if (isset($_GET['ajax']) && isset($_GET['activate']) && in_array($_GET['activate'], array_keys($lessonSettings))) {
    $this -> toggleSetting($_GET['activate'], 1);
    exit;
   } elseif (isset($_GET['ajax']) && isset($_GET['deactivate']) && in_array($_GET['deactivate'], array_keys($lessonSettings))) {
    $this -> toggleSetting($_GET['deactivate'], 0);
    exit;
   } elseif ($_GET['tab'] == "global_settings" && isset($_GET['lessons_ID']) && eF_checkParameter($_GET['lessons_ID'], 'id') && isset($_GET['copy_block_order'])) {
    $res = eF_getTableData("lessons","id,options", "id=".$_GET['lessons_ID']);
    $options = unserialize($res[0]["options"]);
    $order = unserialize($options['default_positions']);
    //pr($order);exit;
    $result = eF_getTableData("lessons","id,options");
    foreach ($result as $key => $value) {
     $temp = unserialize($value["options"]);
     $temp['default_positions'] = $options['default_positions'];
     eF_updateTableData("lessons", array('options' => serialize($temp)), "id=".$value['id']);
    }
    //$this -> setMessageVar(urlencode(_MODULE_ADMINISTRATOR_TOOLS_BLOCKORDERCOPIED), 'success');
    eF_redirect($this -> moduleBaseUrl."&do=learning&tab=global_settings&message_type=success&message=".urlencode(_MODULE_ADMINISTRATOR_TOOLS_BLOCKORDERCOPIED));
   }
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
  $this -> setMessageVar($message, $message_type);
 }
 private function doSqlInterface() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();
  $sqlForm = new HTML_QuickForm("sql_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_administrator_tools&tab=sql&do=system", "", null, true);
  $sqlForm -> addElement('text', 'sql_command', _MODULE_ADMINISTRATOR_TOOLS_SQLCOMMAND, 'style = "width:600px"' );
  $sqlForm -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
  if ($sqlForm -> isSubmitted() && $sqlForm -> validate()) {
   try {
    $values = $sqlForm -> exportValues();
    try {
     $result = array();
     $recordSet = $GLOBALS['db'] -> Execute($values['sql_command']);
     if (($affectedRows = $GLOBALS['db'] -> Affected_Rows()) !== false) {
      $smarty -> assign("T_SQL_AFFECTED_ROWS", $affectedRows);
     }
     while (!$recordSet->EOF) {
      if (empty($result)) {
       $result[] = array_keys($recordSet -> fields);
      }
      $result[] = $recordSet->fields;
      $recordSet->MoveNext();
     }
     $smarty -> assign("T_SQL_RESULT", $result);
    } catch (Exception $e) {
     $smarty -> assign("T_SQL_RESULT", $e->getMessage());
    }
   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
   }
  }
  $smarty -> assign("T_SQL_FORM", $sqlForm -> toArray());
  $this -> setMessageVar($message, $message_type);
 }
 private function doCourseLessonUsers() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();

  if (isset($_GET['lessons_ID'])) {
   $currentLesson = new EfrontLesson($_GET['lessons_ID']);
   $smarty -> assign("T_CURRENT_LESSON", $currentLesson);
   $roles = EfrontUser::getRoles(true);
   $smarty -> assign("T_ROLES", $roles);
   try {
    if ($_GET['ajax'] == 'usersTable') {

     $constraints = array('archive' => false, 'active' => 1, 'return_objects' => false) + createConstraintsFromSortedTable();
     $dataSource = $currentLesson -> getLessonUsersIncludingUnassigned($constraints);
     $totalEntries = $currentLesson -> countLessonUsersIncludingUnassigned($constraints);

     $smarty -> assign("T_SORTED_TABLE", $_GET['ajax']);
     $smarty -> assign("T_TABLE_SIZE", $totalEntries);
     $smarty -> assign("T_DATA_SOURCE", $dataSource);

    }

    if (isset($_GET['ajax']) && isset($_GET['reset_user'])) {
     $user = EfrontUserFactory :: factory($_GET['reset_user']);
     $user -> resetProgressInLesson($currentLesson);
     exit;
    }

    if (isset($_GET['postAjaxRequest'])) {
     if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
      isset($_GET['user_type']) && in_array($_GET['user_type'], array_keys($roles)) ? $userType = $_GET['user_type'] : $userType = 'student';

      $result = eF_getTableData("users_to_lessons", "*", "archive = 0 and users_LOGIN='".$_GET['login']."' and lessons_ID=".$currentLesson -> lesson['id']);
      if (sizeof($result) == 0) {
       $currentLesson -> addUsers($_GET['login'], $userType);
      } elseif ($result[0]['user_type'] != $userType) {
       $currentLesson -> setRoles($_GET['login'], $userType);
      } else {
       $currentLesson -> removeUsers($_GET['login']);
      }
     } else if (isset($_GET['addAll'])) {
      $constraints = array('archive' => false, 'active' => 1, 'has_lesson' => 0, 'return_objects' => false) + createConstraintsFromSortedTable();
      $dataSource = $currentLesson -> getLessonUsersIncludingUnassigned($constraints);
      $userTypes = array();
      foreach ($dataSource as $user) {
       $user['user_types_ID'] ? $userTypes[] = $user['user_types_ID'] : $userTypes[] = $user['user_type'];
      }

      $currentLesson -> addUsers($dataSource, $userTypes);
     } else if (isset($_GET['removeAll'])) {
      $constraints = array('archive' => false, 'active' => 1, 'has_lesson' => 1, 'return_objects' => false) + createConstraintsFromSortedTable();
      $dataSource = $currentLesson -> getLessonUsersIncludingUnassigned($constraints);
      $currentLesson -> archiveLessonUsers($dataSource);
     }
     exit;
    }
   } catch (Exception $e) {
    handleAjaxExceptions($e);
   }
  }

  $this -> setMessageVar($message, $message_type);
 }

 private function doSynchronizeCourseLessons() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();

  $courses = array();
  foreach (EfrontCourse::getCourses() as $value) {
   $courses[$value['id']] = $value['name'];
  }
  $form = new HTML_QuickForm("file_encodings_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_administrator_tools&tab=sync_course_lessons&do=learning", "", null, true);
  $form -> addElement('static', '', _MODULE_ADMINISTRATOR_TOOLS_THISWILLPROPAGATECOMPLETIONSTATUSFROMACOURSETOITSLESSONSFORALLUSERS);
  $form -> addElement('select', 'course', _COURSE, $courses);
  $form -> addElement('advcheckbox', 'set_completed', _MODULE_ADMINISTRATOR_TOOLS_UPDATECOMPLETEDLESSONS.'</span>');
  $form -> addElement('static', '', _MODULE_ADMINISTRATOR_TOOLS_SETCOMPLETEDLESSONSTOTHESAMEDATE);
  $form -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');
  if ($form -> isSubmitted() && $form -> validate()) {
   try {
    $updates = 0;
    $stats = EfrontStats::getUsersCourseStatus($form->exportValue('course'));
    foreach ($stats[$form->exportValue('course')] as $value) {
     if ($value['completed']) {
      foreach ($value['lesson_status'] as $lesson_id => $lesson_status) {
       if (!$lesson_status['completed'] || $form->exportValue('set_completed')) {
        eF_updateTableData("users_to_lessons", array('completed' => 1, 'to_timestamp' => $value['completion_date'], 'score' => $value['score']), "lessons_ID=$lesson_id and users_LOGIN='".$value['login']."'");
        $updates++;
       }
      }
     }
    }
    $message = str_replace("%x", $updates, _MODULE_ADMINISTRATOR_TOOLS_PERFORMEDXUPDATES);
    $message_type = 'success';
   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
   }
  }
  $smarty -> assign("T_SYNC_COURSE_LESSONS_FORM", $form -> toArray());

  $this -> setMessageVar($message, $message_type);
 }

 private function doChangeFileEncoding() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();

  $fileEncodingsForm = new HTML_QuickForm("file_encodings_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_administrator_tools&tab=files_encoding&do=system", "", null, true);
  $fileEncodingsForm -> addElement('select', 'encoding', _MODULE_ADMINISTRATOR_TOOLS_SELECTENCODINGCONVERSION, array('UTF-8 => UTF7-IMAP', 'UTF7-IMAP => UTF-8'));
  $fileEncodingsForm -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');

  if ($fileEncodingsForm -> isSubmitted() && $fileEncodingsForm -> validate()) {
   try {
    $values = $fileEncodingsForm -> exportValues();
    if ($values['encoding'] == 0) {
     $from = 'UTF-8';
     $to = 'UTF7-IMAP';
    } else if ($values['encoding'] == 1) {
     $from = 'UTF7-IMAP';
     $to = 'UTF-8';
    }
    $filesystem = new FileSystemTree(G_CONTENTPATH);
    $convertedCounter = 0;
    foreach (new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($filesystem->tree), RecursiveIteratorIterator :: SELF_FIRST))) as $key => $value) {
     if (mb_check_encoding(basename($key), $from) && ($converted = mb_convert_encoding(basename($key), $to, $from)) != basename($key)) {
      $value->rename(dirname($value['path'])."/".$converted);
      $convertedCounter++;
      $contentLink = htmlspecialchars(str_replace(G_ROOTPATH.'www/', '', $value['path']));
      $convertedContentLink = htmlspecialchars(dirname($contentLink)."/".$converted);

      $result = eF_getTableData("content", "id, data", "data like '%".$contentLink."%'");
      foreach ($result as $value) {
       $newData = str_replace($contentLink, $convertedContentLink, $value['data']);
       eF_updateTableData("content", array('data' => $newData), "id=".$value['id']);
      }

      $result = eF_getTableData("projects", "id, data", "data like '%".$contentLink."%'");
      foreach ($result as $value) {
       $newData = str_replace($contentLink, $convertedContentLink, $value['data']);
       eF_updateTableData("projects", array('data' => $newData), "id=".$value['id']);
      }

      $result = eF_getTableData("tests", "id, description", "description like '%".$contentLink."%'");
      foreach ($result as $value) {
       $newData = str_replace($contentLink, $convertedContentLink, $value['data']);
       eF_updateTableData("tests", array('description' => $newData), "id=".$value['id']);
      }

      $result = eF_getTableData("questions", "id, text", "text like '%".$contentLink."%'");
      foreach ($result as $value) {
       $newData = str_replace($contentLink, $convertedContentLink, $value['data']);
       eF_updateTableData("questions", array('text' => $newData), "id=".$value['id']);
      }

     }
    }
    $message = "Converted $convertedCounter files";
    $message_type='success';
   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
   }
  }
  $smarty -> assign("T_FILE_ENCODINGS_FORM", $fileEncodingsForm -> toArray());

  $this -> setMessageVar($message, $message_type);
 }

 private function doChangeUserType() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();

  $userTypes = EfrontUser::getRoles(true);
  $userTypesMapping = EfrontUser::getRoles();
  $changeUserTypeForm = new HTML_QuickForm("change_user_type_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_administrator_tools&tab=change_user_type", "", null, true);
  $changeUserTypeForm -> addElement('select', 'from_type', _MODULE_ADMINISTRATOR_TOOLS_SELECTSOURCEUSERTYPE, $userTypes);
  $changeUserTypeForm -> addElement('select', 'to_type', _MODULE_ADMINISTRATOR_TOOLS_SELECTTARGETUSERTYPE, $userTypes);
  $changeUserTypeForm -> addElement('checkbox', 'change_courses', _MODULE_ADMINISTRATOR_TOOLS_CHANGETYPEINCOURSESASWELL);
  $changeUserTypeForm -> addElement('submit', 'submit', _SUBMIT, 'class = "flatButton"');

  if ($changeUserTypeForm -> isSubmitted() && $changeUserTypeForm -> validate()) {
   try {
    $values = $changeUserTypeForm -> exportValues();
    if ($userTypesMapping[$values['from_type']] == $userTypesMapping[$values['to_type']]) {
     eF_updateTableData("users", array("user_type" => $values['to_type']), "user_type='".$values['from_type']."'");
     if ($values['change_courses']) {
      eF_updateTableData("users_to_lessons", array("user_type" => $values['to_type']), "user_type='".$values['from_type']."'");
      eF_updateTableData("users_to_courses", array("user_type" => $values['to_type']), "user_type='".$values['from_type']."'");
     }
     $message = _OPERATIONCOMPLETEDSUCCESSFULLY;
     $message_type='success';
    } else {
     $message = _MODULE_ADMINISTRATOR_TOOLS_BASICTYPESMUSTMATCH;
     $message_type='failure';
    }
    $this -> setMessageVar($message, $message_type);

   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
    $this -> setMessageVar($message, $message_type);

   }
  }
  $smarty -> assign("T_CHANGE_USER_TYPE_FORM", $changeUserTypeForm -> toArray());
 }

 private function doIdleUsers() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();

  $form = new HTML_QuickForm("user_activity_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_administrator_tools&tab=user_activity", "", null, true);
  $form -> addElement('date', 'idle_from_timestamp', _MODULE_ADMINISTRATOR_TOOLS_SHOWINACTIVEUSERSSINCE, array('minYear' => 2005, 'maxYear' => date("Y")));
  $form -> addElement("static", "", '<a href = "javascript:void(0)" onclick = "setFormDate('.date("Y").','.date("m").','.(date("d")-7).')">'._LASTWEEK.'</a> - <a href = "javascript:void(0)" onclick = "setFormDate('.date("Y").','.(date("m")-1).','.date("d").')">'._LASTMONTH.'</a> - <a href = "javascript:void(0)" onclick = "setFormDate('.date("Y").','.(date("m")-3).','.date("d").')">'._MODULE_ADMINISTRATOR_TOOLS_LAST3MONTHS.'</a>');
  $form -> addElement("submit", "submit", _SUBMIT, 'class = "flatButton"');
  if (!isset($_SESSION['timestamp_from'])) {
   $_SESSION['timestamp_from'] = time()-86400*30;
  }
  $form -> setDefaults(array("idle_from_timestamp" => $_SESSION['timestamp_from']));
  if ($form -> isSubmitted() && $form -> validate()) {
   $values = $form -> exportValues();
   $_SESSION['timestamp_from'] = mktime(0, 0, 0, $values['idle_from_timestamp']['M'], $values['idle_from_timestamp']['d'], $values['idle_from_timestamp']['Y']);
  }

  $smarty -> assign("T_IDLE_USER_FORM", $form->toArray());

  try {
   if ($_GET['ajax'] == 'idleUsersTable') {
    $users = eF_getTableData("(select login,name,surname,active,max(l.timestamp) as last_action from users u left outer join logs l on u.login=l.users_LOGIN where u.archive=0 group by login) r", "*", "r.last_action is null or r.last_action <= ".$_SESSION['timestamp_from']);
    list($tableSize, $users) = filterSortPage($users);
    $smarty -> assign("T_SORTED_TABLE", $_GET['ajax']);
    $smarty -> assign("T_TABLE_SIZE", $tableSize);
    $smarty -> assign("T_DATA_SOURCE", $users);
   }

   if (isset($_GET['ajax']) && isset($_GET['archive_user'])) {
    $user = EfrontUserFactory :: factory($_GET['archive_user']);
    $user -> archive();
    exit;
   } else if (isset($_GET['ajax']) && isset($_GET['archive_all_users'])) {
    eF_updateTableData("users", array("archive" => 1, "active" => 0), "login in (select login from (select login,max(l.timestamp) as last_action from users u left outer join logs l on u.login=l.users_LOGIN where u.archive=0 and u.login != '".$_SESSION['s_login']."' group by login) r where r.last_action <= ".$_SESSION['timestamp_from']." or r.last_action is null)");
    exit;
   } else if (isset($_GET['ajax']) && isset($_GET['toggle_user'])) {
    $user = EfrontUserFactory :: factory($_GET['toggle_user']);
    if ($user -> user['active']) {
     $user -> deactivate();
    } else {
     $user -> activate();
    }
    echo json_encode(array('status' => 1, 'active' => $user -> user['active']));
    exit;
   } else if (isset($_GET['ajax']) && isset($_GET['deactivate_all_users'])) {
    eF_updateTableData("users", array("active" => 0), "login in (select login from (select login,max(l.timestamp) as last_action from users u left outer join logs l on u.login=l.users_LOGIN where u.archive=0 and u.login != '".$_SESSION['s_login']."' group by login) r where r.last_action <= ".$_SESSION['timestamp_from']." or r.last_action is null)");
    exit;
   }

  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }

 }

 private function doUnenrollUsers() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();

  if ($_GET['type'] == 'job') {
   foreach (EfrontJob::getAllJobs() as $key => $value) {
    $entities[$value['job_description_ID']] = $value['description'];
   }
   if ($_GET['entry']) {
    $entity = new EfrontJob($_GET['entry']);
    $courses = $entity -> getJobCourses(array('archive' => false));
    $users = $entity -> getEmployees();
   }
  } elseif ($_GET['type'] == 'branch') {
   foreach (EfrontBranch::getAllBranches() as $key => $value) {
    $entities[$value['branch_ID']] = $value['name'];
   }
   if ($_GET['entry']) {
    $entity = new EfrontBranch($_GET['entry']);
    $courses = $entity -> getBranchCourses(array('archive' => false));
    $users = $entity -> getEmployees();
   }
  } elseif ($_GET['type'] == 'group') {
   foreach (EfrontGroup::getGroups() as $key => $value) {
    $entities[$value['id']] = $value['name'];
   }
   if ($_GET['entry']) {
    $entity = new EfrontGroup($_GET['entry']);
    $courses = $entity -> getGroupCourses(array('archive' => false));
    $users = $entity -> getGroupUsers();
   }
  }

  if ($_GET['ajax'] && $_GET['remove_users_from_courses']) {
   try {
    foreach ($courses as $course) {
     $course->removeUsers($users);
    }
    exit;
   } catch (Exception $e) {
    handleAjaxExceptions($e);
   }
  }
  $smarty -> assign("T_ENTITIES_LIST", $entities);

  $this -> setMessageVar($message, $message_type);
 }

 private function doCategoryReports() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();

  $directionsTree = new EfrontDirectionsTree();
  $directionPaths = $directionsTree -> toPathString();

  $form = new HTML_QuickForm("category_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_administrator_tools&tab=category_reports&do=enterprise", "", null, true);
  $form -> addElement('select', 'category', _CATEGORY, $directionPaths);
  $form -> addElement('checkbox', 'incomplete', _MODULE_ADMINISTRATOR_TOOLS_SHOWINCOMPLETE);
  $form -> addElement('checkbox', 'inactive', _MODULE_ADMINISTRATOR_TOOLS_SHOWINACTIVECOURSES);
  $form -> addElement('date', 'from_timestamp', _MODULE_ADMINISTRATOR_TOOLS_COMPLETEDFROM, array('minYear' => 1970, 'maxYear' => date("Y")));
  $form -> addElement('date', 'to_timestamp', _MODULE_ADMINISTRATOR_TOOLS_COMPLETEDTO, array('minYear' => 1970, 'maxYear' => date("Y")));
  $form -> addElement("submit", "submit", _SUBMIT, 'class = "flatButton"');

  $form -> setDefaults(array("from_timestamp" => mktime(0,0,0,date("m")-1,date("d"), date("Y")), "to_timestamp" => time()));

  if ($form -> isSubmitted() && $form -> validate()) {
   $values = $form -> exportValues();
   $_SESSION['from_timestamp'] = mktime(0, 0, 0, $_POST['from_timestamp']['M'], $_POST['from_timestamp']['d'], $_POST['from_timestamp']['Y']);
   $_SESSION['to_timestamp'] = mktime(23, 59, 59, $_POST['to_timestamp']['M'], $_POST['to_timestamp']['d'], $_POST['to_timestamp']['Y']);

   $_SESSION['category'] = $values['category'];
   $_SESSION['incomplete'] = $values['incomplete'];
   $_SESSION['inactive'] = $values['inactive'];
   $smarty -> assign("T_SHOW_TABLE", true);
  }
  if (isset($_GET['ajax']) && $_GET['ajax'] == 'categoryUsersTable' || $_GET['ajax'] == 'xls' || $_GET['ajax'] == 'show_xls') {
   $smarty -> assign("T_SHOW_TABLE", true);
   $smarty -> assign("T_DIRECTIONS_TREE", $directionPaths);

   $branchesTree = new EfrontBranchesTree();
   $branchesPaths = $branchesTree -> toPathString();
   $category = new EfrontDirection($_SESSION['category']);

   $directionsTree = new EfrontDirectionsTree();
   $children = $directionsTree -> getNodeChildren($_SESSION['category']);
   foreach (new EfrontAttributeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($children)), array('id')) as $value) {
    $siblings[] = $value;
   }
   $result = eF_getTableDataFlat("courses", "id", "archive = 0 && directions_ID in (".implode(",", $siblings).")");
   $categoryCourses = $result['id'];

   $resultCourses = eF_getTableDataFlat("users_to_courses uc, courses c", "distinct c.id", 'c.id=uc.courses_ID '.(!$_SESSION['inactive'] ? 'and c.active=1' : '').' and uc.archive=0 and uc.completed=1 and uc.to_timestamp >= '.$_SESSION['from_timestamp'].' and uc.to_timestamp <= '.$_SESSION['to_timestamp']);
   $resultEvents = eF_getTableDataFlat("events e, courses c", "distinct c.id", 'c.id=e.lessons_ID '.(!$_SESSION['inactive'] ? 'and c.active=1' : '').' and e.type=54 and e.timestamp >= '.$_SESSION['from_timestamp'].' and e.timestamp <= '.$_SESSION['to_timestamp']);
   if (empty($resultEvents)) {
    $resultEvents['id'] = array();
   }
   $result = array_unique(array_merge($resultCourses['id'], $resultEvents['id']));
   $categoryCourses = array_intersect(array_unique($categoryCourses), $result); //count only courses that have users completed them

   if ($_SESSION['incomplete']) {
    $constraints = array('archive' => false, 'condition' => '(to_timestamp is null OR to_timestamp = 0 OR (to_timestamp >= '.$_SESSION['from_timestamp'].' and to_timestamp <= '.$_SESSION['to_timestamp'].'))');
   } else {
    $constraints = array('archive' => false, 'condition' => 'completed=1 and to_timestamp >= '.$_SESSION['from_timestamp'].' and to_timestamp <= '.$_SESSION['to_timestamp']);
   }

   foreach ($categoryCourses as $courseId) {
    $course = new EfrontCourse($courseId);
    foreach ($course -> getCourseUsers($constraints) as $value) {
     $userBranches = $value -> aspects['hcd'] -> getBranches();
     $userSupervisors = $value -> aspects['hcd'] -> getSupervisors();
     $userSupervisor = end($userSupervisors);
     $value -> user['course_active']= $course->course['active'];
     $value -> user['course_id']= $course->course['id'];
     $value -> user['category'] = $directionPaths[$course->course['directions_ID']];
     $value -> user['course'] = $course->course['name'];
     $value -> user['directions_ID'] = $course->course['directions_ID'];
     $value -> user['branch'] = $branchesPaths[current($userBranches['employee'])];
     $value -> user['branch_ID'] = current($userBranches['employee']);
     $value -> user['supervisor'] = $userSupervisor;
     $value -> user['historic'] = false;
     $unique = md5($value -> user['to_timestamp'].$value->user['course_id'].$value->user['login']);
     $courseUsers[$unique] = $value -> user;
    }

    $result = eF_getTableData("events", "*", 'type=54 and lessons_ID='.$courseId.' and timestamp >= '.$_SESSION['from_timestamp'].' and timestamp <= '.$_SESSION['to_timestamp']);

    //exit;
    foreach ($result as $entry) {
     try {
      $value = EfrontUserFactory::factory($entry['users_LOGIN']);
      if (!$value->user['archive']) {
       $userBranches = $value -> aspects['hcd'] -> getBranches();
       $userSupervisors = $value -> aspects['hcd'] -> getSupervisors();//pr($entry['users_LOGIN']);pr($userSupervisors);pr(current($userSupervisors));
       $userSupervisor = current($userSupervisors);
       $value -> user['course_active']= $course->course['active'];
       $value -> user['course_id']= $course->course['id'];
       $value -> user['category'] = $directionPaths[$course->course['directions_ID']];
       $value -> user['course'] = $course->course['name'];
       $value -> user['directions_ID'] = $course->course['directions_ID'];
       $value -> user['branch'] = $branchesPaths[current($userBranches['employee'])];
       $value -> user['branch_ID'] = current($userBranches['employee']);
       $value -> user['supervisor'] = $userSupervisor;

       $value -> user['to_timestamp'] = $entry['timestamp'];
       $value -> user['completed'] = 1;
       $value -> user['score'] = '';
       $value -> user['historic'] = true;

       $unique = md5($value -> user['to_timestamp'].$value->user['course_id'].$value->user['login']);
       if (!isset($courseUsers[$unique])) {
        $courseUsers[$unique] = $value -> user;
      }
      }
     } catch (Exception $e) {/*Bypass non-existing users*/}
    }
   }


   if ($_GET['ajax'] == 'xls') {
    $xlsFilePath = $currentUser -> getDirectory().'category_report.xls';
    unlink($xlsFilePath);
    $_GET['limit'] = sizeof($courseUsers);
    $_GET['sort'] = 'category';
    list($tableSize, $courseUsers) = filterSortPage($courseUsers);

    $header = array('category' => _CATEGORY,
         'course' => _NAME,
         'login' => _USER,
         'to_timestamp' => _COMPLETED,
         'score' => _SCORE,
         'supervisor' => _SUPERVISOR,
         'branch' => _BRANCH,
         'historic' => _MODULE_ADMINISTRATOR_TOOLS_HISTORICENTRY);

    foreach ($courseUsers as $value) {
     $rows[] = array(_CATEGORY => str_replace("&nbsp;&rarr;&nbsp;", " -> ", $value['category']),
     _COURSE => $value['course'],
     _USER => formatLogin($value['login']),
     _COMPLETED => formatTimestamp($value['to_timestamp']),
     _SCORE => $value['historic'] ? '' : formatScore($value['score']).'%',
     _SUPERVISOR => formatLogin($value['supervisor']),
     _BRANCH => str_replace("&nbsp;&rarr;&nbsp;", " -> ", $value['branch']),
     _MODULE_ADMINISTRATOR_TOOLS_HISTORICENTRY => $value['historic'] ? _YES : _NO);
    }

    EfrontSystem :: exportToXls($rows, $xlsFilePath);

    exit;
   } else if ($_GET['ajax'] == 'show_xls') {
    $xlsFilePath = $currentUser -> getDirectory().'category_report.xls';
    $file = new EfrontFile($xlsFilePath);
    $file -> sendFile(true);
    exit;
   } else {
    list($tableSize, $courseUsers) = filterSortPage($courseUsers);
    $smarty -> assign("T_SORTED_TABLE", $_GET['ajax']);
    $smarty -> assign("T_TABLE_SIZE", $tableSize);
    $smarty -> assign("T_DATA_SOURCE", $courseUsers);
   }
  }
  $smarty -> assign("T_CATEGORY_FORM", $form->toArray());

  $this -> setMessageVar($message, $message_type);
 }

 private function doJobCourses() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();

  $result = eF_getTableData("module_hcd_job_description j left outer join module_hcd_course_to_job_description cj on cj.job_description_ID=j.job_description_ID", "j.job_description_ID,description,branch_ID,count(courses_ID) as total_courses", "", "", "j.job_description_ID");
  $branchesTree = new EfrontBranchesTree();
  $branchesTreePaths = $branchesTree -> toPathString();
  $jobs = array();
  foreach ($result as $value) {
   $jobsArray[$value['job_description_ID']] = $value;
   $jobs[$value['job_description_ID']] = $branchesTreePaths[$value['branch_ID']]."&nbsp;&rarr;&nbsp;".$value['description']." (".$value['total_courses'].")";
  }

  $form = new HTML_QuickForm("job_courses_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_administrator_tools&tab=job_courses&do=enterprise", "", null, true);
  $form -> addElement('select', 'job', _JOBDESCRIPTION, $jobs);
  $form -> addElement("submit", "submit", _MODULE_ADMINISTRATOR_TOOLS_COPYCOURSESELECTION, 'class = "flatButton"');
  $form -> addElement("static", "", _MODULE_ADMINISTRATOR_TOOLS_COPYCOURSESELECTIONTOALLSIMILARJOBS);
  $form -> setDefaults(array("idle_from_timestamp" => $_SESSION['timestamp_from']));
  if ($form -> isSubmitted() && $form -> validate()) {
   $GLOBALS['currentEmployee'] = $currentUser -> aspects['hcd'];
   $job = new EfrontJob($form -> exportValue('job'));
   $courses = $job -> getJobCourses();
   foreach ($sameJobs = $job -> getSameDescriptions() as $value) {
    eF_deleteTableData("module_hcd_course_to_job_description", "job_description_id = '". $value."'");
   }
   $job -> associateCoursesToJob($courses, true);
   $message = str_replace(array("%x", "%y"), array(sizeof($sameJobs), sizeof($courses)), _MODULE_ADMINISTRATOR_TOOLS_SUCCESSFULLYASSIGNEDCOURSESTOJOBS);
   $message_type = 'success';
  }
  $smarty -> assign("T_JOB_COURSES_FORM", $form->toArray());

  $this -> setMessageVar($message, $message_type);
 }

 private function doMultiplePlacements() {
  $smarty = $this -> getSmartyVar();
  $currentUser = $this -> getCurrentUser();

  try {
   if ($_GET['ajax'] == 'multiplePlacementsTable') {
    $users = eF_getTableData("module_hcd_employee_works_at_branch wb, users u", "wb.users_login, sum(assigned) as placements, u.active", "wb.users_login=u.login and wb.assigned = 1 and u.archive=0", "", "users_login");
    foreach ($users as $key => $value) {
     if ($value['placements'] <= 1) {
      unset($users[$key]);
     }
    }
    list($tableSize, $users) = filterSortPage($users);
    $smarty -> assign("T_SORTED_TABLE", $_GET['ajax']);
    $smarty -> assign("T_TABLE_SIZE", $tableSize);
    $smarty -> assign("T_DATA_SOURCE", $users);
   }

  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }

 }


 private function changeLogin($table, $oldLogin, $newLogin) {
  $fields = $GLOBALS['db'] -> GetCol("describe $table");
  foreach ($fields as $value) {
   if (stripos($value, 'login') !== false) {
    eF_updateTableData($table, array($value => $newLogin), "$value = '".$oldLogin."'");
   }
  }
  if ($table == 'f_personal_messages') {
   //@todo:recipient
   eF_updateTableData($table, array("sender" => $newLogin), "sender = '".$oldLogin."'");
  }
  if ($table == 'notifications' || $table == 'sent_notifications') {
   eF_updateTableData($table, array("recipient" => $newLogin), "recipient = '".$oldLogin."'");
  }
  if ($table == 'surveys' || $table == 'module_hcd_events') {
   eF_updateTableData($table, array("author" => $newLogin), "author = '".$oldLogin."'");
  }
 }

 private function toggleSetting($setting, $enable) {
  $result = eF_getTableData("lessons", "id, options");
  foreach ($result as $value) {
   $options = unserialize($value['options']);
   $enable ? $options[$setting] = 1 : $options[$setting] = 0;
   eF_updateTableData("lessons", array("options" => serialize($options)), "id=".$value['id']);
  }
 }

 private function getLessonSettings() {
  $lessonSettings['theory'] = array('text' => _THEORY, 'image' => "32x32/theory.png", 'onClick' => 'activate(this, \'theory\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  $lessonSettings['examples'] = array('text' => _EXAMPLES, 'image' => "32x32/examples.png", 'onClick' => 'activate(this, \'examples\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  if ($GLOBALS['configuration']['disable_projects'] != 1) {
   $lessonSettings['projects'] = array('text' => _PROJECTS, 'image' => "32x32/projects.png", 'onClick' => 'activate(this, \'projects\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  }
  if ($GLOBALS['configuration']['disable_tests'] != 1) {
   $lessonSettings['tests'] = array('text' => _TESTS, 'image' => "32x32/tests.png", 'onClick' => 'activate(this, \'tests\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  }





  if ($GLOBALS['configuration']['disable_feedback'] != 1) {
   $lessonSettings['feedback'] = array('text' => _FEEDBACK, 'image' => "32x32/feedback.png", 'onClick' => 'activate(this, \'feedback\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  }

  $lessonSettings['rules'] = array('text' => _ACCESSRULES, 'image' => "32x32/rules.png", 'onClick' => 'activate(this, \'rules\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  if ($GLOBALS['configuration']['disable_forum'] != 1) {
   $lessonSettings['forum'] = array('text' => _FORUM, 'image' => "32x32/forum.png", 'onClick' => 'activate(this, \'forum\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  }
  if ($GLOBALS['configuration']['disable_comments'] != 1) {
   $lessonSettings['comments'] = array('text' => _COMMENTS, 'image' => "32x32/note.png", 'onClick' => 'activate(this, \'comments\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  }
  if ($GLOBALS['configuration']['disable_news'] != 1) {
   $lessonSettings['news'] = array('text' => _ANNOUNCEMENTS, 'image' => "32x32/announcements.png", 'onClick' => 'activate(this, \'news\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  }







  $lessonSettings['scorm'] = array('text' => _SCORM, 'image' => "32x32/scorm.png", 'onClick' => 'activate(this, \'scorm\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  $lessonSettings['ims'] = array('text' => _IMS, 'image' => "32x32/autocomplete.png", 'onClick' => 'activate(this, \'ims\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');


  $lessonSettings['digital_library'] = array('text' => _SHAREDFILES, 'image' => "32x32/file_explorer.png", 'onClick' => 'activate(this, \'digital_library\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  if ($GLOBALS['configuration']['disable_calendar'] != 1) {
   $lessonSettings['calendar'] = array('text' => _CALENDAR, 'image' => "32x32/calendar.png", 'onClick' => 'activate(this, \'calendar\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  }
  if ($GLOBALS['configuration']['disable_glossary'] != 1) {
   $lessonSettings['glossary'] = array('text' => _GLOSSARY, 'image' => "32x32/glossary.png", 'onClick' => 'activate(this, \'glossary\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  }
  $lessonSettings['auto_complete'] = array('text' => _AUTOCOMPLETE, 'image' => "32x32/autocomplete.png", 'onClick' => 'activate(this, \'auto_complete\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['content_tree'] = array('text' => _CONTENTTREEFIRSTPAGE, 'image' => "32x32/content_tree.png", 'onClick' => 'activate(this, \'content_tree\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['lesson_info'] = array('text' => _LESSONINFORMATION, 'image' => "32x32/information.png", 'onClick' => 'activate(this, \'lesson_info\')', 'title' => _CLICKTOTOGGLE, 'group' => 2, 'class' => 'inactiveImage');
  if ($GLOBALS['configuration']['disable_bookmarks'] != 1) {
   $lessonSettings['bookmarking'] = array('text' => _BOOKMARKS, 'image' => "32x32/bookmark.png", 'onClick' => 'activate(this, \'bookmarking\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  }

  $lessonSettings['reports'] = array('text' => _STATISTICS, 'image' => "32x32/reports.png", 'onClick' => 'activate(this, \'reports\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['content_report'] = array('text' => _CONTENTREPORT, 'image' => "32x32/warning.png", 'onClick' => 'activate(this, \'content_report\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['print_content'] = array('text' => _PRINTCONTENT, 'image' => "32x32/printer.png", 'onClick' => 'activate(this, \'print_content\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['start_resume'] = array('text' => _STARTRESUME, 'image' => "32x32/continue.png", 'onClick' => 'activate(this, \'start_resume\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['show_percentage'] = array('text' => _COMPLETIONPERCENTAGEBLOCK, 'image' => "32x32/percent.png", 'onClick' => 'activate(this, \'show_percentage\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['show_content_tools'] = array('text' => _UNITOPTIONSBLOCK, 'image' => "32x32/options.png", 'onClick' => 'activate(this, \'show_content_tools\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['show_right_bar'] = array('text' => _RIGHTBAR, 'image' => "32x32/hide_right.png", 'onClick' => 'activate(this, \'show_right_bar\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['show_left_bar'] = array('text' => _LEFTBAR, 'image' => "32x32/hide_left.png", 'onClick' => 'activate(this, \'show_left_bar\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['show_student_cpanel'] = array('text' => _STUDENTCPANEL, 'image' => "32x32/options.png", 'onClick' => 'activate(this, \'show_student_cpanel\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  $lessonSettings['show_dashboard'] = array('text' => _DASHBOARD, 'image' => "32x32/generic.png", 'onClick' => 'activate(this, \'show_dashboard\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  if ($GLOBALS['currentTheme'] -> options['sidebar_interface'] == 1 || $GLOBALS['currentTheme'] -> options['sidebar_interface'] == 2) {
   $lessonSettings['show_horizontal_bar'] = array('text' => _SHOWHORIZONTALBAR, 'image' => "32x32/export.png", 'onClick' => 'activate(this, \'show_horizontal_bar\')', 'title' => _CLICKTOTOGGLE, 'group' => 1, 'class' => 'inactiveImage');
  }






  foreach (eF_loadAllModules(true) as $module) {
   if ($module -> isLessonModule()) {
    // The $setLanguage variable is defined in globals.php
    if (!in_array("administrator", $module -> getPermittedRoles())) {
     $mod_lang_file = $module -> getLanguageFile($setLanguage);
     if (is_file ($mod_lang_file)) {
      require_once $mod_lang_file;
     }
    }
    // The $setLanguage variable is defined in globals.php
    if (!in_array("administrator", $module -> getPermittedRoles())) {
     $mod_lang_file = $module -> getLanguageFile($setLanguage);
     if (is_file ($mod_lang_file)) {
      require_once $mod_lang_file;
     }
    }
    $lessonSettings[$module -> className] = array('text' => $module -> getName(), 'image' => "32x32/addons.png", 'onClick' => 'activate(this, \''.$module -> className.'\')', 'title' => _CLICKTOTOGGLE, 'group' => 3, 'class' => 'inactiveImage');
   }
  }

  $lessonSettings[$key]['onClick'] = 'activate(this, \''.$key.'\')';
  $lessonSettings[$key]['style'] = 'color:inherit';

  return $lessonSettings;

 }

 /*

	 * This function is used to define a smarty template for the main module pages

	 *

	 * Attention: DO NOT define this function if you do not want to use smarty (and want to just create html with the php

	 * code of the getModule() function)

	 *

	 * Example implementation:

	 *

	 *    public function getSmartyTpl() {

	 *         // It is a good idea to define the two following smarty variables for inclusion of module images, libraries etc

	 *         $smarty = $this -> getSmartyVar();

	 *         $smarty -> assign("T_MYMODULE_MODULE_BASEDIR" , $this -> moduleBaseDir);

	 *         $smarty -> assign("T_MYMODULE_MODULE_BASEURL" , $this -> moduleBaseUrl);

	 *         return $this -> moduleBaseDir . "module.tpl";

	 *     }

	 * @return false or the string of the filename of the smarty template file for the module main pages

	 */
 public function getSmartyTpl() {
  $smarty = $this -> getSmartyVar();
  $smarty -> assign("T_MODULE_ADMINISTRATOR_TOOLS_BASEDIR" , $this -> moduleBaseDir);
  $smarty -> assign("T_MODULE_ADMINISTRATOR_TOOLS_BASEURL" , $this -> moduleBaseUrl);
  $smarty -> assign("T_MODULE_ADMINISTRATOR_TOOLS_BASELINK", $this -> moduleBaseLink);
  return $this -> moduleBaseDir . "module.tpl";
 }
}
?>
