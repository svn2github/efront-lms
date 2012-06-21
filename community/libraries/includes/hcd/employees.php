<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}
$loadScripts[] = 'includes/users';

/* Check permissions: Only admins and supervisors may see employee lists - each of them a different list */
if (isset($_SESSION['s_login']) && ($_SESSION['s_type'] == 'administrator' || $currentEmployee -> isSupervisor())) {

 /****************************************************

	 SHOW EMPLOYEES

	 *****************************************************/
 if ($currentEmployee -> isSupervisor()) {
  $filter_branches = array();
  foreach ($currentEmployee->supervisesBranches as $value) {
   $filter_branches[$value]['branch_ID'] = $value;
  }
  $smarty -> assign("T_BRANCHES_FILTER", eF_createBranchesFilterSelect($filter_branches));
 } else {
  $smarty -> assign("T_BRANCHES_FILTER", eF_createBranchesFilterSelect());
 }
 $smarty -> assign("T_JOBS_FILTER", eF_createJobFilterSelect());
 // Create ajax enabled table for employees
 if (isset($_GET['ajax'])) {
  if (isset($_GET['archive_user']) && eF_checkParameter($_GET['archive_user'], 'login')) { //The administrator asked to delete a user
   try {
    if (isset($currentUser -> coreAccess['users']) && $currentUser -> coreAccess['users'] != 'change') {
     throw new Exception(_UNAUTHORIZEDACCESS);
    }
    $user = EfrontUserFactory :: factory($_GET['archive_user']);
    if (G_VERSIONTYPE == 'enterprise') {
     //$user -> aspects['hcd'] -> delete();
    }
    $user -> archive();
   } catch (Exception $e) {
    handleAjaxExceptions($e);
   }
   exit;
  }
  $smarty -> assign("T_ROLES", EfrontUser :: getRoles(true));

  isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

  if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
   $sort = $_GET['sort'];
   isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
  } else {
   $sort = 'login';
  }

  $smarty -> assign("T_LANGUAGES", EfrontSystem :: getLanguages(true));
  if ($_GET['ajax'] == "unattachedUsersTable" && $currentEmployee -> isSupervisor()) {
   // Supervisors are allowed to see only the data of the employees that work in the braches they supervise

   $unattached_employee = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN LEFT OUTER JOIN module_hcd_employee_works_at_branch ON users.login = module_hcd_employee_works_at_branch.users_LOGIN","users.*" , " users.user_type <> 'administrator' AND users.archive = 0 AND (EXISTS (select module_hcd_employees.users_login from module_hcd_employees LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.users_login = module_hcd_employees.users_login where users.login=module_hcd_employees.users_login AND module_hcd_employee_works_at_branch.branch_ID IS NULL)) and users.active=1 GROUP BY login", "login");

   $result = eF_getTableDataFlat("logs", "users_LOGIN, timestamp", "action = 'login'", "timestamp");
   $lastLogins = array_combine($result['users_LOGIN'], $result['timestamp']);
   foreach ($unattached_employee as $key => $value) {
    $unattached_employee[$key]['last_login'] = $lastLogins[$value['login']];
   }
   $smarty -> assign("T_UNATTACHED_EMPLOYEES_SIZE", sizeof($unattached_employee));

   $unattached_employee = eF_multiSort($unattached_employee, $_GET['sort'], $order);
   if (isset($_GET['filter'])) {
    $unattached_employee = eF_filterData($unattached_employee , $_GET['filter']);
   }

   $smarty -> assign("T_UNATTACHED_EMPLOYEES_SIZE", sizeof($unattached_employee));
   if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
    $unattached_employee = array_slice($unattached_employee, $offset, $limit);
   }

   $smarty -> assign("T_UNATTACHED_EMPLOYEES", $unattached_employee);
   $smarty -> display($_SESSION['s_type'].'.tpl');

  } else {

   // Supervisors are allowed to see only the data of the employees that work in the braches they supervise
   if ($currentEmployee -> isSupervisor()) {
    $tree = new EfrontBranchesTree();
    $branchPaths = $tree -> toPathString();
    $supervisedEmployees = $currentEmployee -> getSupervisedEmployees();
    $supervisedEmployees[] = $currentEmployee -> login;
    $smarty -> assign("T_SUPERVISED_EMPLOYEES", $supervisedEmployees);

    $constraints = array('archive' => false, 'supervisor' => true) + createConstraintsFromSortedTable();
    $employees = EfrontEmployee::getUsers($constraints);
    $totalEntries = EfrontEmployee::countUsers($constraints);

   } else if ($_SESSION['s_type'] == 'administrator') {
    $constraints = array('archive' => false) + createConstraintsFromSortedTable();
    $employees = EfrontEmployee::getUsers($constraints);
    $totalEntries = EfrontEmployee::countUsers($constraints);
   }

   $tableName = $_GET['ajax'];
   $alreadySorted = 1;
   $smarty -> assign("T_TABLE_SIZE", $totalEntries);
   $dataSource = $employees;

   include ("sorted_table.php");
  }
  exit;
 }
} else {
 eF_redirect("" . $_SESSION['s_type'] . ".php?ctg=control_panel&message=".urlencode(_SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION)."&message_type=failure");
 exit;
}
