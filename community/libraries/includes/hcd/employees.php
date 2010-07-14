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
 // Create ajax enabled table for employees
 if (isset($_GET['ajax'])) {
  isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

  if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
   $sort = $_GET['sort'];
   isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
  } else {
   $sort = 'login';
  }

  $smarty -> assign("T_LANGUAGES", EfrontSystem :: getLanguages(true));
  if ($_GET['ajax'] == "unattachedUsersTable" && $currentEmployee -> getType() == _SUPERVISOR) {
   // Supervisors are allowed to see only the data of the employees that work in the braches they supervise

   $unattached_employee = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN LEFT OUTER JOIN module_hcd_employee_works_at_branch ON users.login = module_hcd_employee_works_at_branch.users_LOGIN","users.*" , " users.user_type <> 'administrator' AND users.archive = 0 AND (EXISTS (select module_hcd_employees.users_login from module_hcd_employees LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.users_login = module_hcd_employees.users_login where users.login=module_hcd_employees.users_login AND module_hcd_employee_works_at_branch.branch_ID IS NULL)) GROUP BY login", "login");

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
   if ($currentEmployee -> getType() == _SUPERVISOR) {
    $employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN LEFT OUTER JOIN module_hcd_employee_works_at_branch ON users.login = module_hcd_employee_works_at_branch.users_LOGIN","users.*, count(job_description_ID) as jobs_num"," users.user_type <> 'administrator' AND ((module_hcd_employee_works_at_branch.branch_ID IN (" . $_SESSION['supervises_branches'] ." ) AND module_hcd_employee_works_at_branch.assigned='1') OR EXISTS (SELECT module_hcd_employees.users_login FROM module_hcd_employees LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.users_login = module_hcd_employees.users_login WHERE users.login=module_hcd_employees.users_login AND module_hcd_employee_works_at_branch.branch_ID IS NULL)) GROUP BY login", "login");
    foreach ($employees as $key => $value) {
     if (!$value['active'] || $value['archive'] || !$value['jobs_num']) {
      unset($employees[$key]);
     }
    }
    //$employees = eF_getTableData("users $branchFilterExtraTable LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN $jobFilterExtraTable LEFT OUTER JOIN module_hcd_employee_works_at_branch ON users.login = module_hcd_employee_works_at_branch.users_LOGIN","users.*, count(module_hcd_employee_has_job_description.job_description_ID) as jobs_num"," users.user_type <> 'administrator' AND users.archive = 0 AND ((module_hcd_employee_works_at_branch.branch_ID IN (" . $_SESSION['supervises_branches'] ." ) $branchFilterCondition $jobFilterCondition AND module_hcd_employee_works_at_branch.assigned='1')) GROUP BY login", "login");

   } else if ($_SESSION['s_type'] == 'administrator') {
    $employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN", "users.*, count(module_hcd_employee_has_job_description.job_description_ID) as jobs_num","users.archive = 0","","login");
   }
   $result = eF_getTableDataFlat("logs", "users_LOGIN, timestamp", "action = 'login'", "timestamp");
   $lastLogins = array_combine($result['users_LOGIN'], $result['timestamp']);
   foreach ($employees as $key => $value) {
    $employees[$key]['last_login'] = $lastLogins[$value['login']];
   }

   $tableName = "usersTable";
   $dataSource = $employees;

   include ("sorted_table.php");
  }
  exit;
 }
} else {
 $message = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
 $message_type = 'failure';
 eF_redirect("" . $_SESSION['s_type'] . ".php?ctg=control_panel&message=".urlencode($message)."&message_type=$message_type");
 exit;
}

?>
