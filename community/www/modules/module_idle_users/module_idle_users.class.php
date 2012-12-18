<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/*

 * Class defining the new module

 * The name must match the one provided in the module.xml file

 */
class module_idle_users extends EfrontModule {
 /**

	 * Get the module name, for example "Demo module"

	 *

	 * @see libraries/EfrontModule#getName()

	 */
    public function getName() {
     //This is a language tag, defined in the file lang-<your language>.php
        return "Idle users";
    }
 /**

	 * Return the array of roles that will have access to this module

	 * You can return any combination of 'administrator', 'student' or 'professor'

	 *

	 * @see libraries/EfrontModule#getPermittedRoles()

	 */
    public function getPermittedRoles() {
        return array("administrator","professor","student"); //This module will be available to administrators
    }
    public function getModuleJs() {
     if (strpos(decryptUrl($_SERVER['REQUEST_URI']), $this -> moduleBaseUrl) !== false) {
      return $this->moduleBaseDir."module_idle_users.js";
     }
    }
    public function addScripts() {
     return array("scriptaculous/effects", "scriptaculous/controls");
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getCenterLinkInfo()

     */
    public function getCenterLinkInfo() {
     return array('title' => $this -> getName(),
                     'image' => $this -> moduleBaseLink . 'img/logo.png',
                     'link' => $this -> moduleBaseUrl);
    }
    public function getToolsLinkInfo() {
    }


    /**

     * The main functionality

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getModule()

     */
    public function getModule() {
     $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASELINK" , $this -> moduleBaseLink);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $currentUser = $this -> getCurrentUser();
        if ($currentUser->user['user_type'] != 'administrator') {
         $currentEmployee = $this -> getCurrentUser() -> aspects['hcd'];
         if (!$currentEmployee || !$currentEmployee -> isSupervisor()) {
          throw new Exception("You cannot access this module");
         }
        }
        $form = new HTML_QuickForm("user_activity_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_idle_users&tab=user_activity", "", null, true);
        $form -> addElement('date', 'idle_from_timestamp', _MODULE_IDLE_USERS_SHOWINACTIVEUSERSSINCE, array('minYear' => 2005, 'maxYear' => date("Y")));
        $form -> addElement("static", "", '<a href = "javascript:void(0)" onclick = "setFormDate('.date("Y").','.date("m").','.(date("d")-7).')">'._LASTWEEK.'</a> - <a href = "javascript:void(0)" onclick = "setFormDate('.date("Y").','.(date("m")-1).','.date("d").')">'._LASTMONTH.'</a> - <a href = "javascript:void(0)" onclick = "setFormDate('.date("Y").','.(date("m")-3).','.date("d").')">'._MODULE_IDLE_USERS_LAST3MONTHS.'</a>');
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
   if ($currentEmployee) {
    $result = eF_getTableData("(select login,name,surname,active,max(l.timestamp) as last_action from users u left outer join logs l on u.login=l.users_LOGIN where u.archive=0 group by login) r join module_hcd_employee_works_at_branch ewb on ewb.users_login=r.login", "*", "ewb.branch_ID in (".implode(',', $currentEmployee->supervisesBranches).") and r.last_action is null or r.last_action <= ".$_SESSION['timestamp_from']);
   } else {
    $result = eF_getTableData("(select login,name,surname,active,max(l.timestamp) as last_action from users u left outer join logs l on u.login=l.users_LOGIN where u.archive=0 group by login) r", "*", "r.last_action is null or r.last_action <= ".$_SESSION['timestamp_from']);
   }
   $users = array();
   foreach ($result as $value) {
    if ($value['last_action']) {
     $value['last_action_since'] = eF_convertIntervalToTime(time()-$value['last_action'], true);
    } else {
     $value['last_action_since'] = null;
    }
    $users[$value['login']] = $value;
   }
   foreach ($users as $key => $value) {
    if (isset($_COOKIE['toggle_active'])) {
     if (($_COOKIE['toggle_active'] == 1 && !$value['active']) || ($_COOKIE['toggle_active'] == -1 && $value['active'])) {
      unset($users[$key]);
     }
    }
   }
   if (isset($_GET['excel'])) {
    $export_users[] = array(_USER, _MODULE_IDLE_USERS_LASTACTION, _STATUS);
    foreach ($users as $key=>$value) {
     $value['last_action'] ? $last_action = formatTimestamp($value['last_action']) : $last_action = _NEVER;
     $value['active'] ? $status = _ACTIVE : $status = _INACTIVE;
     $export_users[] = array(formatLogin($value['login']), $last_action, $status);
    }
    EfrontSystem :: exportToCsv($export_users, true);
    exit;
   }

   if ($_GET['ajax'] == 'idleUsersTable') {
    list($tableSize, $users) = filterSortPage($users);
    $smarty -> assign("T_SORTED_TABLE", $_GET['ajax']);
    $smarty -> assign("T_TABLE_SIZE", $tableSize);
    $smarty -> assign("T_DATA_SOURCE", $users);
   }

   if (isset($_GET['ajax']) && isset($_GET['archive_user'])) {
    if (isset($users[$_GET['archive_user']])) {
     $user = EfrontUserFactory :: factory($_GET['archive_user']);
     $user -> archive();
    }
    exit;
   } else if (isset($_GET['ajax']) && isset($_GET['archive_all_users'])) {
    //eF_updateTableData("users", array("archive" => 1, "active" => 0), "login in (select login from (select login,max(l.timestamp) as last_action from users u left outer join logs l on u.login=l.users_LOGIN where u.archive=0 and u.login != '".$_SESSION['s_login']."' group by login) r where r.last_action <= ".$_SESSION['timestamp_from']." or r.last_action is null)");
    foreach ($users as $value) {
     eF_updateTableData("users", array("archive" => 1, "active" => 0), "login='".$value['login']."'");
    }
    exit;
   } else if (isset($_GET['ajax']) && isset($_GET['toggle_user'])) {
    if (isset($users[$_GET['toggle_user']])) {
     $user = EfrontUserFactory :: factory($_GET['toggle_user']);
     if ($user -> user['active']) {
      $user -> deactivate();
     } else {
      $user -> activate();
     }
     echo json_encode(array('status' => 1, 'active' => $user -> user['active']));
    }
    exit;
   } else if (isset($_GET['ajax']) && isset($_GET['deactivate_all_users'])) {
    //eF_updateTableData("users", array("active" => 0), "login in (select login from (select login,max(l.timestamp) as last_action from users u left outer join logs l on u.login=l.users_LOGIN where u.archive=0 and u.login != '".$_SESSION['s_login']."' group by login) r where r.last_action <= ".$_SESSION['timestamp_from']." or r.last_action is null)");
    foreach ($users as $value) {
     eF_updateTableData("users", array("active" => 0), "login='".$value['login']."'");
    }

    exit;
   }

  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }

        return true;
    }


    /**

     * Specify which file to include for template

     *

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getSmartyTpl()

     */
    public function getSmartyTpl() {
     return $this -> moduleBaseDir."module.tpl";
    }
    /**

	 * (non-PHPdoc)

	 * @see libraries/EfrontModule#getNavigationLinks()

     */
    public function getNavigationLinks() {
        return array (array ('title' => _HOME, 'link' => $_SERVER['PHP_SELF']),
                      array ('title' => $this -> getName(), 'link' => $this -> moduleBaseUrl));
    }
}
