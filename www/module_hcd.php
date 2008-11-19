<?php
/**
* HUMAN CAPITAL MODULE
*
* This page performs all hcd module functions
* @package eFront
* @version 1.0
*/

// Include language files
include "../libraries/module_hcd_tools.php";

/******************** DISCOVER EMPLOYEE ROLE IN THE HIERARCHY ********/
$supervisor_at_branches = eF_getRights();

// Debug: Print rights
//echo $currentEmployee -> getType() . "<br>";
//echo "Supervisese branches: " . $_SESSION['supervises_branches'] ." <br>";
//pr $supervisor_at_branches;


if (MODULE_HCD_INTERFACE) {
    $currentEmployee = $currentUser -> aspects['hcd'];
    $_SESSION['employee_type'] = $currentEmployee -> getType();
}

/******************************************************************************************************/
/************************************** EMPLOYEES *****************************************************/
/******************************************************************************************************/
if (isset($_GET['op']) && $_GET['op'] == 'employees') {
    /* Check permissions: Only admins and supervisors may see employee lists - each of them a different list */
    if (isset($_SESSION['s_login']) && ($_SESSION['s_type'] == 'administrator' || $currentEmployee -> isSupervisor())) {

        /****************************************************
         SHOW EMPLOYEES
         *****************************************************/

        // Create ajax enabled table for employees
        $load_scripts = array_merge($load_scripts, array('scriptaculous/prototype'));
        if (isset($_GET['ajax'])) {
            isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

            if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                $sort = $_GET['sort'];
                isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
            } else {
                $sort = 'login';
            }

            // Supervisors are allowed to see only the data of the employees that work in the braches they supervise
            if ($currentEmployee -> getType() == _SUPERVISOR) {
                $employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN LEFT OUTER JOIN module_hcd_employee_works_at_branch ON users.login = module_hcd_employee_works_at_branch.users_LOGIN","users.*, count(job_description_ID) as jobs_num"," users.user_type <> 'administrator' AND ((module_hcd_employee_works_at_branch.branch_ID IN (" . $_SESSION['supervises_branches'] ." ) AND module_hcd_employee_works_at_branch.assigned='1') OR EXISTS (SELECT module_hcd_employees.users_login FROM module_hcd_employees LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.users_login = module_hcd_employees.users_login WHERE users.login=module_hcd_employees.users_login AND module_hcd_employee_works_at_branch.branch_ID IS NULL)) GROUP BY login", "login");
            } else if ($_SESSION['s_type'] == 'administrator') {
                $employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN", "users.*, count(job_description_ID) as jobs_num","","","login");
            }
            $employees = eF_multiSort($employees, $_GET['sort'], $order);
            if (isset($_GET['filter'])) {
                $employees = eF_filterData($employees , $_GET['filter']);
            }

            $smarty -> assign("T_EMPLOYEES_SIZE", sizeof($employees));

            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $employees = array_slice($employees, $offset, $limit);
            }

            $smarty -> assign("T_EMPLOYEES", $employees);
            $smarty -> display($_SESSION['s_type'].'.tpl');
            exit;
        } else {

            // Supervisors are allowed to see only the data of the employees that work in the braches they supervise
            if ($currentEmployee -> getType() == _SUPERVISOR) {
                $employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN LEFT OUTER JOIN module_hcd_employee_works_at_branch ON users.login = module_hcd_employee_works_at_branch.users_LOGIN","users.*, count(job_description_ID) as jobs_num"," users.user_type <> 'administrator' AND ((module_hcd_employee_works_at_branch.branch_ID IN (" . $_SESSION['supervises_branches'] ." ) AND module_hcd_employee_works_at_branch.assigned='1') OR EXISTS (select module_hcd_employees.users_login from module_hcd_employees LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.users_login = module_hcd_employees.users_login where users.login=module_hcd_employees.users_login AND module_hcd_employee_works_at_branch.branch_ID IS NULL)) GROUP BY login", "login");
            } else if ($_SESSION['s_type'] == 'administrator') {
                $employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN", "users.*, count(job_description_ID) as jobs_num","","","login limit ".G_DEFAULT_TABLE_SIZE);
            }
            $smarty -> assign("T_EMPLOYEES_SIZE", sizeof($employees));
            // Always one employee - administrator
            $smarty -> assign("T_EMPLOYEES", $employees);
        }
    } else {
        $message      = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = 'failure';
        header("location:" . $_SESSION['s_type'] . ".php?ctg=control_panel&message=$message&message_type=$message_type");
        exit;
    }

/******************************************************************************************************/
/************************************** BRANCHES ******************************************************/
/******************************************************************************************************/
} else if (isset($_GET['op']) && $_GET['op'] == 'branches') {

    try {
    /* Check permissions: only admins and supervisors can see branches - the supervisors only their own */
    if(isset($_GET['delete_branch'])) {
        $currentBranch = new EfrontBranch($_GET['delete_branch']);
    } else if (isset($_GET['edit_branch'])) {
        $currentBranch = new EfrontBranch($_GET['edit_branch']);
    }

    if ($currentUser -> getType() != "administrator" && (($currentEmployee -> getType() != _SUPERVISOR) ||(isset($currentBranch) && !$currentEmployee -> supervisesBranch($currentBranch->branch['branch_ID']) ))) {
        $message      = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = 'failure';
        header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=chart&message=".$message."&message_type=".$message_type);
        exit;
    }
    } catch (EfrontBranchException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
    }


    /*****************************************************
     ON AJAX REQUESTING the branch's job descriptions select
     **************************************************** */
    if (isset($_GET['postAjaxRequest']) && isset($_GET['getJobSelect'])) {

        $ar= $currentBranch -> createJobDescriptionsSelect($attributes);
        foreach ($ar as $val=>$element) {
            echo $val."<option>".$element."<option>";
        }

        exit;
    }

    /*****************************************************
     ON DELETING A branch
     **************************************************** */
    if (isset($_GET['delete_branch'])) {    //The administrator asked to delete a branch
        try {
        $currentBranch -> delete();
        $message      = _BRANCHDELETED;
        $message_type = 'success';
        } catch (EfrontBranchException $e) {
            $message = $e -> getMessage().' ('.$e -> getCode().')';
        }
        header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=branches&message=".$message."&message_type=".$message_type);

    /*****************************************************
     ON INSERTING OR EDITING A BRANCH
     **************************************************** */
    } else if (isset($_GET['add_branch']) || isset($_GET['edit_branch'])) {
        try {
        if (isset($_GET['add_branch'])) {
            $form = new HTML_QuickForm("branch_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=branches&add_branch=1", "", null, true);
        } else {
            $form = new HTML_QuickForm("branch_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=branches&edit_branch=" . $_GET['edit_branch'] , "", null, true);

            // First job is to assign the jobs Assign jobs
            if ($_GET['postAjaxRequest']) {

                // Find all employees having this skill
                if ($_GET['insert'] == "true") {
                    $editedUser = EfrontUserFactory :: factory($_GET['add_employee']);
                    $editedEmployee = $editedUser -> aspects['hcd'];

                    if ($_GET['default_job'] != '') {

                        if ($_GET['default_job'] != $_GET['add_job'] || $_GET['default_position'] != $_GET['add_position']) {
                            $old_job_description_ID = eF_getJobDescriptionId($_GET['default_job'], $_GET['edit_branch']);
                            $editedEmployee = $editedEmployee -> removeJob ($old_job_description_ID);
                        }
                    }

                    $new_job_description_ID = eF_getJobDescriptionId($_GET['add_job'], $_GET['edit_branch']);
                    $editedEmployee = $editedEmployee -> addJob ($editedUser, $new_job_description_ID, $_GET['edit_branch'], $_GET['add_position']);
                } else if ($_GET['insert'] == "false") {

                    $editedUser = EfrontUserFactory :: factory($_GET['add_employee']);
                    $editedEmployee = $editedUser -> aspects['hcd'];
                    $old_job_description_ID = eF_getJobDescriptionId($_GET['add_job'], $_GET['edit_branch']);
                    $editedEmployee = $editedEmployee -> removeJob ($old_job_description_ID);

                } else if (isset($_GET['addAll'] )) {
                    $employees = $currentBranch -> getEmployeesWithJobs();
                    $jobs = $currentBranch -> getJobDescriptions();

                    foreach ($employees as $employee) {
                        if ($employee['job_description_ID'] == "") {
                            $editedUser = EfrontUserFactory :: factory($employee['login']);
                            $editedEmployee = $editedUser -> aspects['hcd'];
                            if ($editedUser -> getType() != "administrator") {
                                $editedEmployee = $editedEmployee -> addJob ($editedUser, $jobs[0]['job_description_ID'], $_GET['edit_branch'], 0);
                            }

                        }
                    }
                } else if (isset($_GET['removeAll'] )) {
                    $employees = $currentBranch -> getEmployeesWithJobs();
                    foreach ($employees as $employee) {
                        if ($employee['job_description_ID'] != "") {
                            $editedUser = EfrontUserFactory :: factory($employee['login']);
                            $editedEmployee = $editedUser -> aspects['hcd'];

                            $employee_jobs = $editedEmployee -> getJobs();
                            $jobs = $currentBranch -> getJobDescriptions();
                            foreach ($jobs as $job) {
                                if (in_array($job['job_description_ID'], array_keys($employee_jobs))) {
                                    $editedEmployee = $editedEmployee -> removeJob ($job['job_description_ID'], $_GET['edit_branch'], $employee_jobs['role']);
                                }
                            }


                        }
                    }
                }
                $_GET['ajax'] = 1;
            }

            // Create ajax enabled table for employees
            $load_scripts = array_merge($load_scripts, array('scriptaculous/prototype'));
            if (isset($_GET['ajax'])) {
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }

                if ($currentBranch -> createEmployeeJobsHtml()) {
                    //$employees = $currentBranch -> employees;
                    //$employees_jobsTable = $employees;   // Keep two copies

                    if ($_GET['ajax'] == "branchJobsTable") {
                        $employees = $currentBranch -> employees;
                    } else {
                        if ($currentEmployee -> getType() == _SUPERVISOR) {
                            $sbranchesList = implode("','", $currentEmployee -> supervisesBranches);
                            $employees = eF_getTableData("users JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active, module_hcd_job_description.description, module_hcd_job_description.job_description_ID, module_hcd_job_description.branch_ID, module_hcd_employee_works_at_branch.supervisor", "module_hcd_job_description.branch_ID IN ('". $sbranchesList ."') AND module_hcd_job_description.branch_ID = '". $currentBranch -> branch['branch_ID'] . "' AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login AND users.user_type != 'administrator'" ,"","");
                        } else {
                            $employees = eF_getTableData("users JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description  ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active, module_hcd_job_description.description, module_hcd_job_description.job_description_ID, module_hcd_job_description.branch_ID, module_hcd_employee_works_at_branch.supervisor", "module_hcd_job_description.branch_ID = '". $currentBranch -> branch['branch_ID'] . "' AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login" ,"","");
                        }
                    }
//pr($employees);
                    $employees = eF_multiSort($employees, $_GET['sort'], $order);
                    if (isset($_GET['filter'])) {
                        $employees = eF_filterData($employees , $_GET['filter']);
                    }
                    $smarty -> assign("T_EMPLOYEES_SIZE", sizeof($employees));

                    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                        $employees = array_slice($employees, $offset, $limit);
                    }

                    $smarty -> assign("T_EMPLOYEES", $employees);

                } else {
                    $smarty -> assign("T_EMPLOYEES_SIZE", 0);
                    $smarty -> assign("T_NOBRANCHJOBSERROR", 1);
                }
                $smarty -> display($_SESSION['s_type'].'.tpl');
                exit;
            } else {
                if ($currentBranch -> createEmployeeJobsHtml()) {
                    $employees = $currentBranch -> employees;
                    $smarty -> assign("T_EMPLOYEES", $currentBranch -> employees);
                    $smarty -> assign("T_EMPLOYEES_SIZE", sizeof($employees));
                } else {
                    $employees = array();
//$employees = "";
                    $smarty -> assign("T_EMPLOYEES", $employees);
                    $smarty -> assign("T_NOBRANCHJOBSERROR", 1);
$smarty -> assign("T_EMPLOYEES_SIZE", 0);
                }


            }

        }

        // Hidden for maintaining the previous_url value
        $form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');
        $previous_url = basename(getenv('HTTP_REFERER'));
        if ($position = strpos($previous_url, "&message")) {
            $previous_url = substr($previous_url, 0, $position);
        }
        $form -> setDefaults(array( 'previous_url'     =>  $previous_url));

        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter
        $form -> addElement('text', 'branch_name', _BRANCHNAME, 'class = "inputText"');

        // Dates
        $days = array();
        $days['0'] = _DAY;
        for ($i = 1; $i < 32; $i++) {
            $days[$i] = $i;
        }

        $months = array();
        $months['0'] = _MONTH;
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = $i;
        }

        $years = array();
        $years['0'] = _YEAR;
        for ($i = 2008; $i < 2015; $i++) {
            $years[$i] = $i;
        }

        $form -> addRule('branch_name', _THEFIELD.' '._BRANCHNAME.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addElement('text', 'address', _ADDRESS, 'class = "inputText"');
        $form -> addElement('text', 'city', _CITY, 'class = "inputText"');
        $form -> addElement('text', 'country', _COUNTRY, 'class = "inputText"');
        $form -> addElement('text', 'telephone', _TELEPHONE, 'class = "inputText"');
        $form -> addElement('text', 'email', _EMAIL, 'class = "inputText"');

        /* Get data */
        if (isset($_GET['edit_branch'])) {
            /* Set the link to the details of the father branch */
            $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $currentBranch -> branch['father_branch_ID'] . "\"";
            $smarty -> assign("T_BRANCH_NAME", $currentBranch -> branch['name']);
        }

        // Variable used to forbid the appearance of the link appearing for the lense;
        $forbidden_link = "";
        /* Select or possible father branches (the ones this supervisor manages) or all (if user is administrator)*/
        if ($currentEmployee -> getType() == _SUPERVISOR) {
            $father_branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","branch_ID IN (" . implode(",",$currentEmployee -> supervisesBranches). ")");

            // Show only existing branches
            $only_existing = 1;
            if ($currentBranch && !$currentEmployee -> supervisesBranch($currentBranch -> branch['father_branch_ID'])) {
                if ($currentBranch -> branch['father_branch_ID'] == 0) {
                    $only_existing = 0;
                }

                $father_branches[] = array('branch_ID' => $currentBranch -> branch['father_branch_ID'], 'name' => $currentBranch -> branch['father_name'], 'father_branch_ID' => '');
                $forbidden_link = $currentBranch -> branch['father_branch_ID'];
                $smarty -> assign ("T_FORBID_LINK", 1);
            }


        }
        else {
            $father_branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");
            // Show all branches
            $only_existing = 0;
        }

        if (!empty($father_branches)) {
            if (isset($_GET['edit_branch'])) {
                $smarty -> assign("T_FATHER_BRANCH_ID", $currentBranch -> branch['father_branch_ID']);

                $smarty -> assign("T_FATHER_BRANCH_INFO", $details_link);
            }

            $smarty -> assign("T_SHOWFATHER", 1);


            $form -> addElement('select', 'fatherBranch' , _FATHERBRANCH, eF_createBranchesTreeSelect($father_branches,$only_existing),'class = "inputText"  id="fatherBranch" onchange="javascript:change_branch(\'fatherBranch\',\'details_link\',\''.$forbidden_link.'\')"');

            // If add_branch request coming from another branch subbranches menu, pre-enter the fatherBranch form
            if (isset($_GET['add_branch'])) {
                if (isset($_GET['add_branch_to'])) {
                    $form -> setDefaults(array( 'fatherBranch'     =>  $_GET['add_branch_to']));
                    $smarty -> assign("T_FATHER_BRANCH_ID", $_GET['add_branch_to']);
                    $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $_GET['add_branch_to'] . "\"";
                    $smarty -> assign("T_FATHER_BRANCH_INFO", $details_link);
                }
                else if ($currentEmployee -> getType() == _SUPERVISOR) {
                    $_GET['add_branch_to'] = $father_branches[0]['branch_ID']; // keep the $_GET variable for checking at the smarty side
                    $form -> setDefaults(array( 'fatherBranch'     =>  $_GET['add_branch_to']));
                    $smarty -> assign("T_FATHER_BRANCH_ID", $_GET['add_branch_to']);
                    $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $_GET['add_branch_to'] . "\"";
                    $smarty -> assign("T_FATHER_BRANCH_INFO", $details_link);
                }
            }
        } else {
            $first_branch = 1;
        }

        if (isset($_GET['edit_branch'])) {
            $subbranches = $currentBranch -> getSubbranches();
            if(!empty($subbranches)) {
                $smarty -> assign("T_SUBBRANCHES", $subbranches);
            }

            // Get job descriptions with skills
            $job_descriptions = $currentBranch -> getJobDescriptions(true);
            if(!empty($job_descriptions)) {
                $smarty -> assign("T_JOB_DESCRIPTIONS", $job_descriptions);
            }

            $delete_link = array(
               array('text' => _DELETE, 'image' => "16x16/delete.png", 'href' => $_SESSION['s_type'].".php?ctg=module_hcd&op=branches&delete_branch=".$_GET['edit_branch']."&father_ID=".$original_father, 'onClick' => "return confirm('"._AREYOUSUREYOUWANTTODISMISSTHEBRANCH."')", 'target' => '_self')
            );

            $smarty -> assign ("T_DELETE_LINK", $delete_link);
        }

        /************************* TELOS EPILOGIS ***************************/

        $form -> addElement('submit', 'submit_branch_details', _SUBMIT, 'class = "flatButton"');

        if (isset($_GET['edit_branch'])) {
            $form -> setDefaults(array( 'branch_name'     => $currentBranch -> branch['name'],
                                        'address'         => $currentBranch -> branch['address'],
                                        'city'            => $currentBranch -> branch['city'],
                                        'country'         => $currentBranch -> branch['country'],
                                        'telephone'       => $currentBranch -> branch['telephone'],
                                        'email'           => $currentBranch -> branch['email']));
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $renderer -> setRequiredTemplate(
            '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
    } catch (EfrontBranchException $e) {
        $message = $e -> getMessage().' ('.$e -> getCode().')';
    }
        /*****************************************************
         BRANCH DATA SUBMISSION
         **************************************************** */
        if ($form -> isSubmitted()) {

            if ($form -> validate()) {
                $branch_content = array('name'           => $form->exportValue('branch_name'),
                                        'address'        => $form->exportValue('address')  ,
                                        'city'           => $form->exportValue('city')     ,
                                        'country'        => $form->exportValue('country')  ,
                                        'telephone'      => $form->exportValue('telephone'),
                                        'email'          => $form->exportValue('email'));
                try {
                    if (isset($_GET['add_branch'])) {
                        if ($first_branch != 1) {
                            $branch_content['father_branch_ID'] = $form -> exportValue('fatherBranch');
                        }

                        EfrontBranch :: createBranch($branch_content);
                        $message      = _SUCCSSFULLYCREATEDBRANCH;
                        $message_type = 'success';

                    } elseif (isset($_GET['edit_branch'])) {

                        $branch_content['father_branch_ID'] = $form->exportValue('fatherBranch');
                        $currentBranch -> updateBranchData($branch_content);
                        $message      = _BRANCHDATAUPDATED;
                        $message_type = 'success';

                    }
                } catch (EfrontBranchException $e) {
                    $message = $e -> getMessage().' ('.$e -> getCode().')';
                }

                // Instead of going back to the branches go the previous link
                if (isset($_GET['add_branch'])) {
                    header("location:".basename($form->exportValue('previous_url'))."&message=". $message . "&message_type=" . $message_type . "&tab=subbranches");
                } else {
                    header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=branches&edit_branch=".$_GET['edit_branch']."&message=". $message . "&message_type=" . $message_type);
                }
                exit;
            }
        }

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);
        $smarty -> assign('T_BRANCH_FORM', $renderer -> toArray());

    } else {

        /*****************************************************
         SHOW BRANCHES
         **************************************************** */        /*Select branches-father branches and employees number sorted by father_branch_ID (so that the ones with no father ID will be on the top)*/
        try {
        if ($_SESSION['s_type'] == "administrator") {
            $permission_to_change = 1;
            $smarty -> assign("T_CHANGE_RIGHTS", $permission_to_change);
        } else if ($currentEmployee -> getType() == _SUPERVISOR) {
            $permission_to_change = 1;
            $smarty -> assign("T_CHANGE_RIGHTS", $permission_to_change);
        }

        $load_scripts = array_merge($load_scripts, array('scriptaculous/prototype'));
        if (isset($_GET['ajax'])) {
            isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

            if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                $sort = $_GET['sort'];
                isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
            } else {
                $sort = 'name';
            }

            if ($_SESSION['s_type'] == "administrator") {
                $branches = eF_getTableData("(module_hcd_branch LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_branch.branch_ID = module_hcd_employee_works_at_branch.branch_ID AND module_hcd_employee_works_at_branch.assigned = '1') LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID GROUP BY module_hcd_branch.branch_ID ORDER BY branch1.branch_ID", "module_hcd_branch.branch_ID, module_hcd_branch.name, module_hcd_branch.city, module_hcd_branch.address,  count(users_login) as employees,  branch1.branch_ID as father_ID, branch1.name as father, supervisor","");
            } else {
                $branches = eF_getTableData("(module_hcd_branch LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_branch.branch_ID = module_hcd_employee_works_at_branch.branch_ID AND module_hcd_employee_works_at_branch.assigned = '1') LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID WHERE module_hcd_branch.branch_ID IN (".$_SESSION['supervises_branches'].") GROUP BY module_hcd_branch.branch_ID ORDER BY branch1.branch_ID", "module_hcd_branch.name, module_hcd_branch.city, module_hcd_branch.address,  count(users_login) as employees,  module_hcd_branch.branch_ID, branch1.branch_ID as father_ID, branch1.name as father","");
            }

            if ($currentEmployee -> getType() == _SUPERVISOR) {
                $count = 0;
                for ($count = 0; $count < sizeof($branches); $count++) {
                    if (in_array($branches[$count]['branch_ID'], $supervisor_at_branches['branch_ID'])) {
                        $branches[$count]["supervisor"] = 1;
                    } else {
                        $branches[$count]["supervisor"] = 0;
                    }

                    if (in_array($branches[$count]['father_ID'], $supervisor_at_branches['branch_ID'])) {
                        $branches[$count]["father_supervisor"] = 1;
                    } else {
                        $branches[$count]["father_supervisor"] = 0;
                    }

                }
            }

            $branches = eF_multiSort($branches, $_GET['sort'], $order);
            if (isset($_GET['filter'])) {
                $branches = eF_filterData($branches, $_GET['filter']);
            }

            $smarty -> assign("T_BRANCHES_SIZE", sizeof($branches));
            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $branches = array_slice($branches, $offset, $limit);
            }


            if(!empty($branches)) {
               $smarty -> assign("T_BRANCHES", $branches);
            }
            $smarty -> display($_SESSION['s_type'].'.tpl');
            exit;
        } else {
            if ($_SESSION['s_type'] == "administrator") {
                $branches = eF_getTableData("(module_hcd_branch LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_branch.branch_ID = module_hcd_employee_works_at_branch.branch_ID AND module_hcd_employee_works_at_branch.assigned = '1') LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID GROUP BY module_hcd_branch.branch_ID ORDER BY branch1.branch_ID", "module_hcd_branch.branch_ID, module_hcd_branch.name, module_hcd_branch.city, module_hcd_branch.address,  count(users_login) as employees,  branch1.branch_ID as father_ID, branch1.name as father, supervisor","");
            } else {
                $branches = eF_getTableData("(module_hcd_branch LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_branch.branch_ID = module_hcd_employee_works_at_branch.branch_ID AND module_hcd_employee_works_at_branch.assigned = '1') LEFT OUTER JOIN module_hcd_branch as branch1 ON module_hcd_branch.father_branch_ID = branch1.branch_ID WHERE module_hcd_branch.branch_ID IN (".$_SESSION['supervises_branches'].") GROUP BY module_hcd_branch.branch_ID ORDER BY branch1.branch_ID", "module_hcd_branch.branch_ID, module_hcd_branch.name, module_hcd_branch.city, module_hcd_branch.address,  count(users_login) as employees,  branch1.branch_ID as father_ID, branch1.name as father","");
            }

            if ($currentEmployee -> getType() == _SUPERVISOR) {
                $count = 0;
                for ($count = 0; $count < sizeof($branches); $count++) {
                    if (in_array($branches[$count]['branch_ID'], $supervisor_at_branches['branch_ID'])) {
                        $branches[$count]["supervisor"] = 1;
                    } else {
                        $branches[$count]["supervisor"] = 0;
                    }

                    if (in_array($branches[$count]['father_ID'], $supervisor_at_branches['branch_ID'])) {
                        $branches[$count]["father_supervisor"] = 1;
                    } else {
                        $branches[$count]["father_supervisor"] = 0;
                    }

                }
            }

            $smarty -> assign("T_BRANCHES_SIZE", sizeof($branches));
            if(!empty($branches)) {
               $smarty -> assign("T_BRANCHES", $branches);
            }
        }
        } catch (EfrontBranchException $e) {
            $message = $e -> getMessage().' ('.$e -> getCode().')';
        }

    }

/******************************************************************************************************/
/**************************************** SKILLS ******************************************************/
/******************************************************************************************************/
} else if (isset($_GET['op']) && $_GET['op'] == 'skills') {
try{
    /* Check permissions: only admins have add/edit privileges. supervisors may only see skills */
    if($currentEmployee -> getType() == _EMPLOYEE) {
        $message      = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = 'failure';
        header("location:".$_SESSION['s_type'].".php?ctg=personal&tab=skills&message=".$message."&message_type=".$message_type);
        exit;
    }

    if( (isset($_GET['edit_skill']) && $currentEmployee -> getType() != _SUPERVISOR && $currentUser -> getType() != 'administrator') || (isset($_GET['delete_skill']) && $currentUser -> getType() != 'administrator')){
        $message      = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = 'failure';
        header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=skills&message=".$message."&message_type=".$message_type);
        exit;
    }

    /*****************************************************
     ON DELETING A SKILL
     **************************************************** */
    if (isset($_GET['delete_skill'])) {    //The administrator asked to delete a skill
        $currentSkill = new EfrontSkill($_GET['delete_skill']);
        $currentSkill -> delete();
        $message      = _SKILLDELETED;
        $message_type = 'success';
        header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=skills&message=".$message."&message_type=".$message_type);
    /*****************************************************
     ON INSERTING OR EDITING A SKILL
     **************************************************** */
    } else if (isset($_GET['add_skill']) || isset($_GET['edit_skill'])) {

        if (isset($_GET['add_skill'])) {
            $form = new HTML_QuickForm("skill_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=skills&add_skill=1", "", null, true);
        } else {
            $form = new HTML_QuickForm("skill_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=skills&edit_skill=" . $_GET['edit_skill'] , "", null, true);
            $currentSkill = new EfrontSkill( $_GET['edit_skill']);
        }

        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter
        $form -> addElement('text', 'skill_description', _SKILLDESCRIPTION, 'id="skill_description" class = "inputText" tabindex="1"');
        $form -> addRule('skill_description', _THEFIELD.' '._SKILLDESCRIPTION.' '._ISMANDATORY, 'required', null, 'client');

        $result = eF_getTableData("module_hcd_skill_categories", "id, description", "");
        $skill_categories = array("0" => "");
        foreach($result as $skill_cat) {
            $id = $skill_cat['id'];
            $skill_categories[$id]= $skill_cat['description'];
        }

        $form -> addElement('select', 'category' , _SKILLCATEGORY, $skill_categories , 'class = "inputText"  id="skill_cat" onchange="javascript:change_skill_category(\'skill_cat\')" tabindex="2"');

        /* Get data */
        if (isset($_GET['edit_skill'])) {

            /* Ajax assignments/removals of the skill to employees */
            if ($_GET['postAjaxRequest']) {

                /* Find all employees having this skill */
                if ($_GET['insert'] == "true") {
                    $currentSkill -> assignToEmployee($_GET['add_user'], $_GET['specification']);
                } else if ($_GET['insert'] == "false") {
                    $currentSkill -> removeFromEmployee($_GET['add_user']);
                } else if (isset($_GET['addAll'] )) {
                    $employees = $currentSkill -> getEmployees();
                    foreach ($employees as $employee) {
                        if ($employee['skill_ID'] == "") {
                            $currentSkill -> assignToEmployee($employee['login'], "");
                        }
                    }
                } else if (isset($_GET['removeAll'] )) {
                    $employees = $currentSkill -> getEmployees();
                    foreach ($employees as $employee) {
                        if ($employee['skill_ID'] != "") {
                            $currentSkill -> removeFromEmployee($employee['login']);
                        }
                    }
                }
                $_GET['ajax'] = 1;
            }

            $smarty -> assign("T_SKILL_NAME", $currentSkill -> skill['description']);

            /* Find -updated from previous - all employees having this skill */
            // Create ajax enabled table for employees
            $load_scripts = array_merge($load_scripts, array('scriptaculous/prototype'));
            if (isset($_GET['ajax'])) {

                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }

                // Here we only need existing employees
                $employees = $currentSkill -> getEmployees();

                if (!isset($_GET['show_all'])) {
                    foreach ($employees as $login => $employee) {
                        if ($employee['skill_ID'] != $_GET['edit_skill']) {
                            unset($employees[$login]);
                        }
                    }
                }
                $employees = eF_multiSort($employees, $_GET['sort'], $order);

                if (isset($_GET['filter'])) {
                    $employees = eF_filterData($employees , $_GET['filter']);
                }

                $smarty -> assign("T_EMPLOYEES_SIZE", sizeof($employees));
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;

                    $employees = array_slice($employees, $offset, $limit);
                }

                if(!empty($employees)) {

                   $smarty -> assign("T_EMPLOYEES", $employees);
                }

                $smarty -> display($_SESSION['s_type'].'.tpl');
                exit;
            } else {
                $employees = $currentSkill -> getEmployees();

                $smarty -> assign("T_EMPLOYEES_SIZE", sizeof($employees));
                if(!empty($employees)) {
                    $smarty -> assign("T_EMPLOYEES", $employees);
                }

            }
        }

        // Hidden for maintaining the previous_url value
        $form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');

        $previous_url = getenv('HTTP_REFERER');
        if (!strpos($previous_url, "op=skill_cat") && !strpos($previous_url, "add_skill") && !strpos($previous_url,"administratorpage.php")) {
            if ($position = strpos($previous_url, "&message")) {
                $previous_url = substr($previous_url, 0, $position);
            }
        } else {
            $previous_url = $_SESSION['s_type'].".php?ctg=module_hcd&op=skills";
        }
        $form -> setDefaults(array( 'previous_url'     =>  $previous_url));

        $form -> addElement('submit', 'submit_skill_details', _SUBMIT, 'class = "flatButton" tabindex="3" onClick="if(document.getElementById(\'skill_cat\').value==\'0\'){alert(\''._THEFIELD.' '._SKILLCATEGORY.' '._ISMANDATORY.'\');return false;}" ');
        if (isset($_GET['edit_skill'])) {
            $form -> setDefaults(array( 'skill_description'     => $currentSkill -> skill['description'],
                                        'category'              => $currentSkill -> skill['categories_ID']));
        }
        $smarty -> assign("T_DEFAULT_CATEGORY", $currentSkill -> skill['categories_ID']);

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $renderer -> setRequiredTemplate(
            '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');

        /*****************************************************
         SKILL DATA SUBMISSION
         **************************************************** */
        if ($form -> isSubmitted()) {
            if ($form -> validate()) {
                $skill_content = array('description'     => $form->exportValue('skill_description'),
                                       'categories_ID'     => $form->exportValue('category'));

                if (isset($_GET['add_skill'])) {
                    EfrontSkill :: createSkill($skill_content);
                    $message      = _SUCCESSFULLYCREATEDSKILL;
                    $message_type = 'success';

                } elseif (isset($_GET['edit_skill'])) {
                    $currentSkill -> updateSkillData($skill_content);
                    $message      = _SKILLDATAUPDATED;
                    $message_type = 'success';
                }

                // Return to previous url stored in a hidden - that way, after the insertion we can immediately return to where we were
//echo $form->exportValue('previous_url')."&message=". $message . "&message_type=" . $message_type . "&tab=skills";
                header("location:".basename($form->exportValue('previous_url'))."&message=". urlencode($message) . "&message_type=" . $message_type . "&tab=skills");
                exit;
            }
        }

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);
        $smarty -> assign('T_SKILLS_FORM', $renderer -> toArray());
   } else {
        /****************************************************
         SHOW SKILLS
         *****************************************************/
        $skillset = EfrontSkill :: getAllSkills();

        if(!empty($skillset)) {
            $smarty -> assign("T_SKILLS", $skillset);
        }

   }

    if (isset($_GET['ajax']) || isset($_GET['postAjaxRequest'])) {
        $smarty -> display($_SESSION['s_type'].'.tpl');
        exit;
    }
} catch (EfrontSkillException $e) {
    $message = $e -> getMessage().' ('.$e -> getCode().')';
}
/**************************************************************************************************************/
/**************************************** CATEGORY SKILL ******************************************************/
/**************************************************************************************************************/
} else if (isset($_GET['op']) && $_GET['op'] == 'skill_cat') {
try{
    /* Check permissions: only admins have add/edit privileges. supervisors may only see skills */
    if($currentUser -> getType() != 'administrator') {
        $message      = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = 'failure';
        header("location:".$_SESSION['s_type'].".php?ctg=personal&tab=skills&message=".$message."&message_type=".$message_type);
        exit;
    }

    /*****************************************************
     ON DELETING A SKILL CATEGORY
     **************************************************** */
    if (isset($_GET['del_skill_cat'])) {    //The administrator asked to delete a skill

        eF_updateTableData("module_hcd_skills",array("category_ID" => ""), "category_ID = '". $_GET['del_skill_cat'] ."'");
        eF_deleteTableData("module_hcd_skill_categories", "id = '".$_GET['del_skill_cat']."'");
        $message      = _SKILLCATEGORYDELETED;
        $message_type = 'success';
        header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=skills&message=".$message."&message_type=".$message_type);
        exit;
    /*****************************************************
     ON INSERTING OR EDITING A SKILL CATEGORY
     **************************************************** */
    } else if (isset($_GET['add_skill_cat']) || isset($_GET['edit_skill_cat'])) {

        if (isset($_GET['add_skill_cat'])) {
            $form = new HTML_QuickForm("skill_cat_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=skill_cat&add_skill_cat=1", "",null, true);
        } else {
            $form = new HTML_QuickForm("skill_cat_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=skill_cat&edit_skill_cat=" . $_GET['edit_skill_cat'] , "", null, true);
            $skill_cat = eF_getTableData("module_hcd_skill_categories","description", "id ='".$_GET['edit_skill_cat']."'");
        }

        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter
        $form -> addElement('text', 'skill_cat_description', _SKILLCATEGORY, 'id="skill_cat_description" class = "inputText" tabindex="1"');
        $form -> addRule('skill_cat_description', _THEFIELD.' '._SKILLCATEGORY.' '._ISMANDATORY, 'required', null, 'client');

        // Hidden for maintaining the previous_url value
        $form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');
        $previous_url = getenv('HTTP_REFERER');
        if ($position = strpos($previous_url, "&message")) {
            $previous_url = substr($previous_url, 0, $position);
        }
        $form -> setDefaults(array( 'previous_url'     =>  $previous_url));

        $form -> addElement('submit', 'submit_skill_details', _SUBMIT, 'class = "flatButton" tabindex="2"');

        if (isset($_GET['edit_skill_cat'])) {
            $form -> setDefaults(array( 'skill_cat_description'     => $skill_cat[0]['description']));
        }

        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $renderer -> setRequiredTemplate(
            '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');

        /*****************************************************
         SKILL DATA SUBMISSION
         **************************************************** */
        if ($form -> isSubmitted()) {
            if ($form -> validate()) {
                $skill_cat_content = array('description'     => $form->exportValue('skill_cat_description'));

                if (isset($_GET['add_skill_cat'])) {
                    eF_insertTableData("module_hcd_skill_categories", $skill_cat_content);
                    $message      = _SUCCESSFULLYCREATEDSKILLCATEGORY;
                    $message_type = 'success';

                } elseif (isset($_GET['edit_skill_cat'])) {
                    eF_updateTableData("module_hcd_skill_categories", $skill_cat_content , "id = '".$_GET['edit_skill_cat']."'");
                    $message      = _SKILLCATEGORYDATAUPDATED;
                    $message_type = 'success';
                }

                // Return to previous url stored in a hidden - that way, after the insertion we can immediately return to where we were
                echo "<script>!/\?/.test(parent.location) ? parent.location = '". basename($form->exportValue('previous_url')) ."&message=".urlencode($message)."&message_type=".$message_type."' : parent.location = '".basename($form->exportValue('previous_url')) ."&message=".urlencode($message)."&message_type=".$message_type."';</script>";
                //header("location:".$form->exportValue('previous_url')."&message=". $message . "&message_type=" . $message_type . "&tab=skills");
                exit;
            }
        }

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);
        $smarty -> assign('T_SKILL_CAT_FORM', $renderer -> toArray());
   }
} catch (EfrontSkillException $e) {
    $message = $e -> getMessage().' ('.$e -> getCode().')';
}
/******************************************************************************************************/
/******************************* JOB DESCRIPTIONS *****************************************************/
/******************************************************************************************************/
} else if (isset($_GET['op']) && $_GET['op'] == 'job_descriptions') {
try {
    /* Check permissions: manage only job descriptions of the branches you own */
    if(isset($_GET['delete_job_description'])) {
        $currentJob = new EfrontJob($_GET['delete_job_description']);
    } else if (isset($_GET['edit_job_description'])) {
        $currentJob = new EfrontJob($_GET['edit_job_description']);

    }

    if ($currentUser -> getType() != "administrator" && !($currentEmployee -> getType() == _SUPERVISOR && ((isset($_GET['add_job_description']) || (isset($currentJob) && in_array($currentJob -> job['branch_ID'], $currentEmployee -> supervisesBranches)) || (!isset($currentJob) && !isset($_GET['add_job_description'])))))) {
        $message      = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = 'failure';
        header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&message=".$message."&message_type=".$message_type);
        exit;
    }
    if (isset($_GET['postAjaxRequest'])) {
        try {
            if (isset($_GET['skill'])) {
                if ($_GET['insert'] == 'true') {
                    $currentJob -> assignSkill($_GET['add_skillID'], $_GET['apply_to_all_jd']);
                } else if ($_GET['insert'] == 'false') {
                    $currentJob -> removeSkill($_GET['add_skillID'], $_GET['apply_to_all_jd']);
                } else if (isset($_GET['addAll'] )) {
                    $skills = $currentJob -> getSkills();
                    foreach ($skills as $skill) {
                        if ($skill['job_description_ID'] == "") {
                            $currentJob -> assignSkill($skill['skill_ID'], $_GET['apply_to_all_jd']);
                        }
                    }
                } else if (isset($_GET['removeAll'] )) {
                    $skills = $currentJob -> getSkills();
                    foreach ($skills as $skill) {
                        if ($skill['job_description_ID'] != "") {
                            $currentJob -> removeSkill($skill['skill_ID'], $_GET['apply_to_all_jd']);
                        }
                    }
                }
            } else if (isset($_GET['lesson'])) {
                if ($_GET['insert'] == 'true') {
                    $currentJob -> associateLesson($_GET['add_lessonID'], $_GET['apply_to_all_jd']);
                } else if ($_GET['insert'] == 'false') {
                    $currentJob -> removeLesson($_GET['add_lessonID'], $_GET['apply_to_all_jd']);
                } else if (isset($_GET['addAll'] )) {
                    $lessons = $currentJob -> getLessons();
                    foreach ($lessons as $lesson) {
                        if ($lesson['job_description_ID'] == "") {
                            $currentJob -> associateLesson($lesson['id'], $_GET['apply_to_all_jd']);
                        }
                    }
                } else if (isset($_GET['removeAll'] )) {
                    $lessons = $currentJob -> getLessons();
                    foreach ($lessons as $lesson) {
                        if ($lesson['job_description_ID'] != "") {
                            $currentJob -> removeLesson($lesson['id'], $_GET['apply_to_all_jd']);
                        }
                    }
                }
            } else if (isset($_GET['course'])) {
                if ($_GET['insert'] == 'true') {
                    $currentJob -> associateCourse($_GET['add_courseID'], $_GET['apply_to_all_jd']);
                } else if ($_GET['insert'] == 'false') {
                    $currentJob -> removeCourse($_GET['add_courseID'], $_GET['apply_to_all_jd']);
                } else if (isset($_GET['addAll'] )) {
                    $courses = $currentJob -> getCourses();
                    foreach ($courses as $course) {
                        if ($course['job_description_ID'] == "") {
                            $currentJob -> associateCourse($course['id'], $_GET['apply_to_all_jd']);
                        }
                    }
                } else if (isset($_GET['removeAll'] )) {
                    $courses = $currentJob -> getCourses();
                    foreach ($courses as $course) {
                        if ($course['job_description_ID'] != "") {
                            $currentJob -> removeCourse($course['id'], $_GET['apply_to_all_jd']);
                        }
                    }
                }
            }

            exit;
        } catch (Exception $e) {
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }

    }


    /*****************************************************
     ON DELETING A JOB DESCRIPTION
     **************************************************** */
    if (isset($_GET['delete_job_description'])) {    //The administrator asked to delete a job_description
        $currentJob -> delete();
        header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&message=".$message."&message_type=".$message_type);
    } else if (isset($_GET['export_vacancies_for_job_description'])) {

        //TODO: well, export vacancies...
        /*
        if ($ok = eF_insertTableData("module_hcd_vacancies", array("job_description_ID" => $_GET['export_vacancies_for_job_description'], "available_placements" => $_GET['available_placements']))) {
            $message      = _JOBDESCRIPTIONDELETED;
            $message_type = 'success';
        } else {
            $message      = _THISJOBDESCRIPTIONSELEMENTSCOULDNOTBEDELETED;
            $message_type = 'failure';
        }
        header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&message=".$message."&message_type=".$message_type);
         */
    /*****************************************************
     ON INSERTING OR EDITING A JOB DESCRIPTION
     **************************************************** */
    } else if (isset($_GET['add_job_description']) || isset($_GET['edit_job_description'])) {
        if (isset($_GET['add_job_description'])) {
            $form = new HTML_QuickForm("job_description_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&add_job_description=1", "", null, true);
        } else {
            $form = new HTML_QuickForm("job_description_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&edit_job_description=" . $_GET['edit_job_description'] , "", null, true);
        }

        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');           //Register this rule for checking user input with our function, eF_checkParameter
        $form -> addElement('text', 'job_description_name', _JOBDESCRIPTION, 'class = "inputText"');
        $form -> addElement('textarea', 'job_role_description', _JOBANALYTICALDESCRIPTION, 'class = "inputText"');
        $form -> addRule('job_description_name', _THEFIELD.' '._JOBDESCRIPTION.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('job_description_name', _INVALIDFIELDDATA, 'checkParameter', 'job_description_name'); /*mandatory me if*/
        $form -> addElement('text', 'placements', _VACANCIES, 'class = "inputText"');

        /* Create the branches drop down menu - all for admin, branches you can manage for supervisor */
        $branches = $currentEmployee -> getSupervisedBranches();

        if (!empty($branches)) {
            if (isset($_GET['edit_job_description'])) {
                $only_existing = 3;
            }
            $form -> addElement('select', 'branch' , _BRANCHNAME, eF_createBranchesTreeSelect($branches, 3) , 'class = "inputText"  id="branch" onchange="javascript:change_branch(\'branch\',\'details_link\')"');
        } else {
            $message      = _NOBRANCHESHAVEBEENREGISTERED;
            $message_type = 'failure';
            if (isset($_GET['edit_branch'])) {
                unset($_GET['edit_job_description']);
            } else {
                unset($_GET['add_job_description']);
            }
            header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&message=". $message . "&message_type=failure");
            exit;
        }

        /* Get job description data */
        if (isset($_GET['edit_job_description'])) {

            $smarty -> assign("T_JOB_DESCRIPTION_NAME", $currentJob -> job['job_description']);

            $employees = $currentJob -> getEmployees();

            if(!empty($employees)) {
                $smarty -> assign("T_EMPLOYEES", $employees);
            }

            /* Create the html code for the "view branch details" lense icon on the right of the branches drop down */
            if ($currentJob -> job['branch_ID'] != 0) {
                $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $currentJob -> job['branch_ID']. "\"";
            } else {
                $details_link = "";
            }


            /* Administrators can associate lessons or courses to job descriptions - every employee with that job description will have the lessons */
            if ($currentUser -> getType() == "administrator" || $currentEmployee -> getType() == _SUPERVISOR) {
                $lessons = $currentJob -> getLessons();
                
                // Remove all course_only lessons
                foreach ($lessons as $lid => $lesson) {
                    if ($lesson['course_only']) {
                         unset($lessons[$lid]);   
                    }
                }
                $courses = $currentJob -> getCourses();
                $skills = $currentJob -> getSkills();

                // Get with ajax
                if (isset($_GET['ajax'])) {
                    if ($_GET['tab'] == 'lessons') {
                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
                        if (isset($_GET['sort'])) {
                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                            $lessons = eF_multiSort($lessons, $_GET['sort'], $order);
                        }
                        if (isset($_GET['filter'])) {
                            $lessons = eF_filterData($lessons, $_GET['filter']);
                        }

                        $smarty -> assign("T_LESSONS_SIZE", sizeof($lessons));
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $lessons = array_slice($lessons, $offset, $limit);
                        }
                        $smarty -> assign("T_LESSONS_DATA", $lessons);

                        $smarty -> display($_SESSION['s_type'].'.tpl');
                        exit;
                    }

                    if ($_GET['tab'] == 'courses') {
                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
                        if (isset($_GET['sort'])) {
                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                            $courses = eF_multiSort($courses, $_GET['sort'], $order);
                        }
                        if (isset($_GET['filter'])) {
                            $courses = eF_filterData($courses, $_GET['filter']);
                        }

                        $smarty -> assign("T_COURSES_SIZE", sizeof($courses));
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $courses = array_slice($courses, $offset, $limit);
                        }
                        $smarty -> assign("T_COURSES_DATA", $courses);

                        $smarty -> display($_SESSION['s_type'].'.tpl');
                        exit;
                    }

                    if ($_GET['tab'] == 'skills') {
                        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                        if (isset($_GET['sort'])) {
                            isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';
                            $skills = eF_multiSort($skills, $_GET['sort'], $order);
                        }
                        if (isset($_GET['filter'])) {
                            $skills = eF_filterData($skills, $_GET['filter']);
                        }

                        $smarty -> assign("T_SKILLS_SIZE", sizeof($skills));
                        if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                            isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                            $skills = array_slice($skills, $offset, $limit);
                        }
                        $smarty -> assign("T_SKILLS", $skills);

                        $smarty -> display($_SESSION['s_type'].'.tpl');
                        exit;
                    }
                } else {
                    // Conventional get
                    $smarty -> assign("T_LESSONS_SIZE", sizeof($lessons));
                    if (!empty($lessons)) {
                        $smarty -> assign("T_LESSONS_DATA", $lessons);
                    }

                    $smarty -> assign("T_COURSES_SIZE", sizeof($courses));
                    if (!empty($courses)) {
                        $smarty -> assign("T_COURSES_DATA", $courses);
                    }

                    $smarty -> assign("T_SKILLS_SIZE", sizeof($skills));
                    if (!empty($skills)) {
                        $smarty -> assign("T_SKILLS", $skills);
                    }
                }

            }

        } else {
            $details_link = "";
        }

        /* The details link has the html code for the "view branch details" lense icon on the right of the branches drop down */
        $smarty -> assign("T_BRANCH_INFO", $details_link);

        $form -> addElement('submit', 'submit_job_description_details', _SUBMIT, 'class = "flatButton"');

        /* Set default values */
        if (isset($_GET['edit_job_description'])) {
            $form -> setDefaults(array( 'job_description_name'     => $currentJob -> job['description'],
                                        'placements'               => $currentJob -> job['employees_needed'],
                                        'job_role_description'     => $currentJob -> job['job_role_description']));
        }

        $smarty -> assign("T_BRANCH_ID", $currentJob -> job['branch_ID']);

        /* If add_branch request coming from another branch subbranches menu, pre-enter the fatherBranch form */
        if (isset($_GET['add_job_description']) && isset($_GET['add_to_branch'])) {
            $form -> setDefaults(array( 'branch'     =>  $_GET['add_to_branch']));
            $details_link = "href=\"" . $_SESSION['s_type']. ".php?ctg=module_hcd&op=branches&edit_branch=" . $_GET['add_to_branch'] . "\"";
            $smarty -> assign("T_BRANCH_INFO", $details_link);
        }

        /* Hidden for maintaining the previous_url value, so that you can immediately return after the insertion of a new job description */
        $form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');
        $previous_url = getenv('HTTP_REFERER');
        if ($position = strpos($previous_url, "&message")) {
            $previous_url = substr($previous_url, 0, $position);
        }
        $form -> setDefaults(array( 'previous_url' =>  $previous_url));

        /* Needed for title */
        $smarty -> assign("T_JOB_DESCRIPTION_NAME", $currentJob -> job['description']);
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $renderer -> setRequiredTemplate(
            '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');

        /*****************************************************
         JOB_DESCRIPTION DATA SUBMISSION
         **************************************************** */
        if ($form -> isSubmitted()) {
            if ($form -> validate()) {
                $job_description_content = array('description'                  => $form->exportValue('job_description_name'),
                                                 'branch_ID'                    => $form->exportValue('branch'),
                                                 'job_role_description'         => $form->exportValue('job_role_description'),
                                                 'employees_needed'             => $form->exportValue('placements'));

                if (isset($_GET['add_job_description'])) {
                    /* Either insert the job description to all branches or only to a single one */
                    EfrontJob :: createJob($job_description_content);
                    $message      = _SUCCESSFULLYCREATEDJOBDESCRIPTION;
                    $message_type = 'success';
                    header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=job_descriptions&message=". $message . "&message_type=success");
                } elseif (isset($_GET['edit_job_description'])) {
                    $currentJob -> updateJobData($job_description_content);
                    $message      = _JOBDESCRIPTIONDATAUPDATED;
                    $message_type = 'success';
                }

                /* Instead of going back to the branches go the previous link */
                header("location:".basename($form->exportValue('previous_url'))."&message=". $message . "&message_type=" . $message_type . "&tab=jobs");
                exit;

            }
        }


        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer);
        $smarty -> assign('T_JOB_DESCRIPTIONS_FORM', $renderer -> toArray());
    } else {
        /****************************************************
         SHOW JOB DESCRIPTIONS
         *****************************************************/

        // Create ajax enabled table for job descriptions
        $load_scripts = array_merge($load_scripts, array('scriptaculous/prototype'));
        if (isset($_GET['ajax'])) {
            isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

            if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                $sort = $_GET['sort'];
                isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
            } else {
                $sort = 'description';
            }

            // If changed with better query, change it also to branches
            $job_descriptions = EfrontJob :: getAllJobs();

            $job_descriptions = eF_multiSort($job_descriptions, $_GET['sort'], $order);
            if (isset($_GET['filter'])) {
                $job_descriptions = eF_filterData($job_descriptions , $_GET['filter']);
            }

            $smarty -> assign("T_JOB_DESCRIPTIONS_SIZE", sizeof($job_descriptions));

            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $job_descriptions = array_slice($job_descriptions, $offset, $limit);
            }

            if(!empty($job_descriptions)) {
                $smarty -> assign("T_JOB_DESCRIPTIONS", $job_descriptions);
            }
            $smarty -> display($_SESSION['s_type'].'.tpl');
            exit;
        } else {

            $job_descriptions = EfrontJob :: getAllJobs();

            $smarty -> assign("T_JOB_DESCRIPTIONS_SIZE", sizeof($job_descriptions));
            if(!empty($job_descriptions)) {
                 $smarty -> assign("T_JOB_DESCRIPTIONS", $job_descriptions);
            }
        }


   }
} catch (EfrontJobException $e) {
    $message = $e -> getMessage().' ('.$e -> getCode().')';
    $message_type = 'failure';
    header("location:".basename($form->exportValue('previous_url'))."&message=". $message . "&message_type=" . $message_type . "&tab=jobs");
    exit;
}
/*******************************************************************************************************/
/**************************************** REPORTS ******************************************************/
/*******************************************************************************************************/
} else if (isset($_GET['op']) && $_GET['op'] == 'reports') {
    /* Reports are at this point developed as "search employee" module. They report which employee(s) fulfill some criteria */

    /* Check permissions: only administrator and supervisors can see the reports - the supervisors for the employees that work in the branches they supervise */
    if ($currentUser -> getType() != "administrator" && $currentEmployee -> getType() != _SUPERVISOR) {
        $message      = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = 'failure';
        header("location:".$_SESSION['s_type'].".php?ctg=module_hcd&op=reports&message=".$message."&message_type=".$message_type);
        exit;
    }

    /* Create the link to the search for course user page */
    if ($currentUser -> getType() == "administrator") {
        $options = array(array('image' => '16x16/book_red.png',   'title' => _SEARCHFOREMPLOYEE,  'link' => $_SESSION['s_type'].'.php?ctg=module_hcd&op=reports' , 'selected' => true),
                         array('image' => '16x16/book_open2.png', 'title' => _SEARCHCOURSEUSERS,  'link' => 'administrator.php?ctg=search_courses',                'selected' => false));
        $smarty -> assign("T_TABLE_OPTIONS", $options);

/*
        $search_course_user = array(
           array('text' => _SEARCHCOURSEUSERS, 'image' => "16x16/", 'href' => "", 'title' => _SEARCHCOURSEUSERS));
        $smarty -> assign ("T_SEARCH_COURSE_USER", $search_course_user);
*/
    }

    /* Create the selection criteria form */
    $form = new HTML_QuickForm("reports_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=reports&search=1&branch_ID=".$_GET['branch_ID']."&job_description_ID=".$_GET['job_description_ID']."&skill_ID=".$_GET['skill_ID'], "", null, true);
    $form -> addElement('radio', 'criteria', null, null, 'all_criteria', 'checked = "checked" id="all_criteria" onclick="javascript:refreshResults()"');
    $form -> addElement('radio', 'criteria', null, null, 'any_criteria', 'id="any_criteria" onclick="javascript:refreshResults()"');

    /* Get data for creating the selects */

    /* Braches (in hierarchical form) */
    $branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");
    $branches_list = eF_createBranchesTreeSelect($branches, 4);
    $branches_list[0] = _DONTTAKEINTOACCOUNT;
    $form -> addElement('select', 'search_branch', _WORKINGATBRANCH, $branches_list, 'id = "search_branch" class = "inputSelect" onchange="javascript:refreshResults()"');

    // If a branch is selected then the form will reload on clicking the checkbox.
    if (isset($_GET['branch_ID'])) {
        $onclick_event = ' onclick = "" ';
    } else {
        $onclick_event = '';
    }

    $form -> addElement('advcheckbox', 'include_subbranches', _INCLUDESUBBRANCHES, null, 'class = "inputCheckbox" id="include_subbranchesId" onClick="javascript:includeSubbranches()"');
    // Check or not the include subbranches checkbox
    if ($_GET['include_sb'] == "true" || $_POST['include_subbranches']) {
        $form -> setDefaults(array('include_subbranches'                  => '1'));
        $include_sb = 1;
    } else {
        $form -> setDefaults(array('include_subbranches'                  => '0'));
        $include_sb = 0;
    }

    /* Job descriptions (all different job descriptions irrespective of the branch they belong to) */
    if (isset($_GET['branch_ID']) && $_GET['branch_ID']!="" && $_GET['branch_ID']!="0") {
        $activeBranch = new EfrontBranch($_GET['branch_ID']);

        $job_description_list = $activeBranch -> createJobDescriptionsSelect();
        $job_description_list[0] = _DONTTAKEINTOACCOUNT;
    } else {
        $job_descriptions = eF_getTableData("module_hcd_job_description", "distinct description","");
        $job_description_list = array("0" => _DONTTAKEINTOACCOUNT);
        foreach ($job_descriptions as $job_description) {
            $log = $job_description['description'];
            $job_description_list["$log"] = $job_description['description'];
        }
    }
    $form -> addElement('select', 'search_job_description', _WITHJOBDESCRIPTION, $job_description_list, 'id = "search_job_description" class = "inputSelect" onchange="javascript:refreshResults()"');

    /* Skills */
    $skills = eF_getTableData("module_hcd_skills", "skill_ID, description","");
    $skills_list = array("0" => _DONTTAKEINTOACCOUNT);
    foreach ($skills as $skill) {
        $log = $skill['skill_ID'];
        $skills_list["$log"] = $skill['description'];
    }

    $form -> addElement('select', 'search_skill', _WITHSKILL, $skills_list, 'id = "search_skill" class = "inputSelect" onchange="javascript:refreshResults()"');
    $form -> addElement('submit', 'submit_report', _SUBMIT, 'class = "flatButton"');

    /* For advanced search form: All information that regard employees (taken from the main form) */
    $form -> addElement('text', 'new_login', _LOGIN, 'class = "inputText" id="new_login" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'name', _NAME, 'class = "inputText" id="name" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'surname', _SURNAME, 'class = "inputText" id="surname" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'email', _EMAILADDRESS, 'class = "inputText" id="email" onChange="javascript:setAdvancedCriterion(this);"');
    $roles = eF_getTableDataFlat("user_types", "user_type", "active=1");

    $roles_array['']              = "";
    $roles_array['student']       = _STUDENT;
    $roles_array['professor']     = _PROFESSOR;
    $roles_array['administrator'] = _ADMINISTRATOR;

    for ($k = 0; $k < sizeof($roles['user_type']); $k++){
        $roles_array[$roles['user_type'][$k]] = $roles['user_type'][$k];
    }
    $form -> addElement('select', 'user_type', _USERTYPE, $roles_array, 'id="user_types" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('advcheckbox', 'active', _ACTIVE, null, ' id ="active" class = "inputCheckbox" onChange="javascript:setAdvancedCriterion(this);"');

    $form -> addElement('text', 'registration', _REGISTRATIONDATE, 'class = "inputText" id="registration" onChange="javascript:setAdvancedCriterion(this);"');
    // Permanent data of personal records of employees
    $form -> addElement('text', 'father', _FATHERNAME, 'class = "inputText" id="father" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('select', 'sex' , _GENDER, array("" => "", "0" => _MALE, "1" => _FEMALE), 'class = "inputText" id="sex" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'birthday', _BIRTHDAY, 'class = "inputText" id="birthday" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'birthplace',_BIRTHPLACE , 'class = "inputText" id="birthplace" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'birthcountry', _BIRTHCOUNTRY, 'class = "inputText" id="birthcountry" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'mother_tongue', _MOTHERTONGUE, 'class = "inputText" id="mother_tongue" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'nationality', _NATIONALITY, 'class = "inputText" id="nationality" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'address', _ADDRESS, 'class = "inputText" id="address" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'city', _CITY, 'class = "inputText" id="city" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'country', _COUNTRY, 'class = "inputText" id="country" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'homephone', _HOMEPHONE, 'class = "inputText" id="homephone" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'mobilephone', _MOBILEPHONE, 'class = "inputText" id="mobilephone" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'office', _OFFICE, 'class = "inputText" id="office" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'company_internal_phone', _COMPANYINTERNALPHONE, 'class = "inputText" id="company_internal_phone" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'afm', _VATREGNUMBER, 'class = "inputText" id="afm" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'doy', _TAXOFFICE, 'class = "inputText" id="doy" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'police_id_number', _POLICEIDNUMBER, 'class = "inputText" id="police_id_number" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'passport_data', _PASSPORTDATA, 'class = "inputText" id="passport_data" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('advcheckbox', 'driving_licence', _DRIVINGLICENSE, null, 'class = "inputCheckbox" id="driving_licence" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'work_permission_data', _WORKPERMISSIONDATA, 'class = "inputText" id="work_permission_data" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('advcheckbox', 'national_service_completed', _NATIONALSERVICECOMPLETED, null, 'class = "inputCheckbox" id="national_service_completed" onChange="javascript:setAdvancedCriterion(this);"');
    // Non permanent data
    $form -> addElement('text', 'employement_type', _EMPLOYMENTTYPE, 'class = "inputText" id="employement_type" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'hired_on', _HIREDON, 'class = "inputText" id="hired_on" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'left_on', _LEFTON, 'class = "inputText" id="left_on" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'wage', _WAGE, 'class = "inputText" id="wage" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('select', 'marital_status', _MARITALSTATUS, array("" => "", "0" => _SINGLE, "1" => _MARRIED),'class = "inputText" id="marital_status" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('advcheckbox', 'transport', _TRANSPORTMEANS, null, ' id ="transport" class = "inputCheckbox" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'bank', _BANK, 'class = "inputText" id="bank" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('text', 'bank_account', _BANKACCOUNT, 'class = "inputText" id="bank_account" onChange="javascript:setAdvancedCriterion(this);"');
    $form -> addElement('select', 'way_of_working', _WAYOFWORKING,  array("" => "", "0" => _FULLTIME, "1" => _PARTTIME),'class = "inputText" id="way_of_working" onChange="javascript:setAdvancedCriterion(this);"');

 //   $form -> addElement('submit', 'submit_personal_details', _REGISTERADVANCEDSEARCHFIELDS, 'class = "flatButton"');

    /* The default values are either posted ($POST array) when the submit button 'submit_personal_details' is used, or gotten ($GET array) on page
       reload, which occurs every time each of the branches,jobs,skills selects changes its value    */
    $form -> setDefaults(array(                   'new_login'                  => $_POST['new_login']?$_POST['new_login']:$_GET['new_login'],
                                                  'name'                       => $_POST['name']?$_POST['name']:$_GET['name'],
                                                  'surname'                    => $_POST['surname']?$_POST['surname']:$_GET['surname'],
                                                  'email'                      => $_POST['email']?$_POST['email']:$_GET['email'],
                                                  'active'                     => $_POST['active']?$_POST['active']:$_GET['active'],
                                                  'registration'               => $_POST['registration']?$_POST['registration']:$_GET['registration'],
                                                  'wage'                       => $_POST['wage']?$_POST['wage']:$_GET['wage'],
                                                  'hired_on'                   => $_POST['hired_on']?$_POST['hired_on']:$_GET['hired_on'],
                                                  'left_on'                    => $_POST['left_on']?$_POST['left_on']:$_GET['left_on'],
                                                  'address'                    => $_POST['address']?$_POST['address']:$_GET['address'],
                                                  'city'                       => $_POST['city']?$_POST['city']:$_GET['city'],
                                                  'country'                    => $_POST['country']?$_POST['country']:$_GET['country'],
                                                  'father'                     => $_POST['father']?$_POST['father']:$_GET['father'],
                                                  'homephone'                  => $_POST['homephone']?$_POST['homephone']:$_GET['homephone'],
                                                  'mobilephone'                => $_POST['mobilephone']?$_POST['mobilephone']:$_GET['mobilephone'],
                                                  'sex'                        => $_POST['sex']?$_POST['sex']:$_GET['sex'],
                                                  'birthday'                   => $_POST['birthday']?$_POST['birthday']:$_GET['birthday'],
                                                  'birthplace'                 => $_POST['birthplace']?$_POST['birthplace']:$_GET['birthplace'],
                                                  'birthcountry'               => $_POST['birthcountry']?$_POST['birthcountry']:$_GET['birthcountry'],
                                                  'mother_tongue'              => $_POST['mother_tongue']?$_POST['mother_tongue']:$_GET['mother_tongue'],
                                                  'nationality'                => $_POST['nationality']?$_POST['nationality']:$_GET['nationality'],
                                                  'company_internal_phone'     => $_POST['company_internal_phone']?$_POST['company_internal_phone']:$_GET['company_internal_phone'],
                                                  'office'                     => $_POST['office']?$_POST['office']:$_GET['office'],
                                                  'doy'                        => $_POST['doy']?$_POST['doy']:$_GET['doy'],
                                                  'afm'                        => $_POST['afm']?$_POST['afm']:$_GET['afm'],
                                                  'police_id_number'           => $_POST['police_id_number']?$_POST['police_id_number']:$_GET['police_id_number'],
                                                  'driving_licence'            => $_POST['driving_licence']?$_POST['driving_licence']:$_GET['driving_licence'],
                                                  'work_permission_data'       => $_POST['work_permission_data']?$_POST['work_permission_data']:$_GET['work_permission_data'],
                                                  'national_service_completed' => $_POST['national_service_completed']?$_POST['national_service_completed']:$_GET['national_service_completed'],
                                                  'employement_type'           => $_POST['employement_type']?$_POST['employement_type']:$_GET['employement_type'],
                                                  'bank'                       => $_POST['bank']?$_POST['bank']:$_GET['bank'],
                                                  'bank_account'               => $_POST['bank_account']?$_POST['bank_account']:$_GET['bank_account'],
                                                  'marital_status'             => $_POST['marital_status']?$_POST['marital_status']:$_GET['marital_status'],
                                                  'transport'                  => $_POST['transport']?$_POST['transport']:$_GET['transport'],
                                                  'way_of_working'             => $_POST['way_of_working']?$_POST['way_of_working']:$_GET['way_of_working'],
                                                  'user_type'                  => $_POST['user_type']?$_POST['user_type']:$_GET['user_type']));


    $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $renderer -> setRequiredTemplate(
            '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');
    //pr($_GET);
//echo "post<br>";
//pr($_POST);
    /*****************************************************
     GET EMPLOYEES FILLING THE CRITERIA
     **************************************************** */
    if (isset($_GET['search']) && ((isset($_GET['branch_ID']) && $_GET['branch_ID']!="" && $_GET['branch_ID']!="0") || (isset($_GET['job_description_ID']) &&  $_GET['job_description_ID'] !="0" && $_GET['job_description_ID']!="") || (isset($_GET['skill_ID']) && $_GET['skill_ID']!= "" && $_GET['skill_ID']!=0))) {
        /* branch_ID equals zero when ANY is selected */
        if ($_GET['branch_ID'] != 0) {
            if ($include_sb) {
                $branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");
                $subbranches = eF_subBranches($_GET['branch_ID'], $branches);

                $subbranches[] = $_GET['branch_ID'];
                $branches_list = implode("','",$subbranches);

                $where_part = "module_hcd_employee_works_at_branch.branch_ID IN ('" . $branches_list . "')";
            } else {
                $where_part = "module_hcd_employee_works_at_branch.branch_ID = '" . $_GET['branch_ID'] . "'";
            }
//echo $where_part."<Br>";
            $employees_data1 = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN LEFT OUTER JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.users_login = users.login AND module_hcd_employee_works_at_branch.assigned = '1' LEFT OUTER JOIN module_hcd_job_description ON module_hcd_job_description.job_description_ID = module_hcd_employee_has_job_description.job_description_ID", "users.*", $where_part,"","login");
            foreach ($employees_data1 as $empl1) {
                $log = $empl1['login'];
                $employees1[$log] = $empl1;
            }
        }

        if ($_GET['job_description_ID'] != "0") {
            $where_part = "module_hcd_job_description.description = '" . $_GET['job_description_ID'] . "'";
            $employees_data2 = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN LEFT OUTER JOIN module_hcd_job_description ON module_hcd_job_description.job_description_ID = module_hcd_employee_has_job_description.job_description_ID", "users.*", $where_part,"","login");
            foreach ($employees_data2 as $empl2) {
                $log = $empl2['login'];
                $employees2[$log] = $empl2;
            }
        }


        if ($_GET['skill_ID'] != 0) {
            $where_part = "skill_ID= '" . $_GET['skill_ID'] . "'";
            $employees_data3 = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN JOIN module_hcd_employee_has_skill ON module_hcd_employee_has_skill.users_login = users.login", "users.*", $where_part,"","login");
            foreach ($employees_data3 as $empl3) {
                $log = $empl3['login'];
                $employees3[$log] = $empl3;
            }
        }

        if ($_GET['all'] == "false") {
            if ($employees1) {
                $employees = $employees1;
            }
            if ($employees2) {
                if (!$employees1) {
                    $employees = $employees2;
                } else {
                    $employees = array_merge($employees1,$employees2);
                }
            }
            if ($employees3) {
                if (!$employees1 && !$employees2) {
                    $employees = $employees3;
                } else {
                    $employees = array_merge($employees,$employees3);
                }
            }

        } else {
            if ($employees1) {
                $employees = $employees1;
            } else {
                // No employee was found while one should => return empty array
                if ($_GET['branch_ID'] != 0) {
                    $employees2 = 0;
                    $employees3 = 0;
                }
            }

            if ($employees2) {
                if ($_GET['branch_ID'] == 0) {
                    $employees = $employees2;
                } else {
                    $employees = array_intersect_assoc($employees1,$employees2);
                }
            } else {
                // No employee was found while one should => return empty array
                if ($_GET['job_description_ID'] != "0") {
                    $employees = array();
                    $employees3 = 0;
                }
            }
            if ($employees3) {
                if ($_GET['branch_ID'] == 0 && $_GET['job_description_ID'] == "0") {
                    $employees = $employees3;
                } else {
                    $employees = array_intersect_assoc($employees,$employees3);
                }
            } else {
                // No employee was found while one should => return empty array
                if ($_GET['skill_ID'] != "0") {
                    $employees = array();
                }
            }

        }


    } else if (isset($_GET['new_login']) || isset($_POST['new_login'])) {
        $employees = eF_getTableData("users LEFT OUTER JOIN module_hcd_employee_has_job_description ON users.login = module_hcd_employee_has_job_description.users_LOGIN", "users.*","","","login");
    }

//echo "employees<Br>";
//pr($employees);

    /* Filter those data according to whether all or some of the criteria need to be fulfilled */
    if ($_GET['all'] == "false") {
        $preposition = " OR ";
    } else {
        $preposition = " AND ";
    }

    /* If advanced criteria are enabled */
    if (isset($_GET['new_login']) || isset($_POST['new_login'])) {
        $size = sizeof($employees);
        if ($size > 0) {
            $list = "users.login IN (";
            $k = 0;

            foreach ($employees as $employee) {
                $list = $list . "'" . $employee['login'] . "'" ;

                if ($k++ != $size - 1) {
                    $list = $list . ",";
                }

            }
            $list = $list . ") ";
        }

        /* Get all employees fulfilling the "advanced criteria" */
        $formvalues = $form -> exportValues();
        $sql_query = $list;
        $found_field = 0;
        foreach ($formvalues as $field => $value) {


            if (($value || $field == "sex" || $field =="marital_status" || $field == "way_of_working") && $field != "search_branch" && $field != "search_job_description" && $field != "search_skill" && $field != "criteria" && $field != "submit_personal_details" && $field != "include_subbranches") {
                if ($field == "new_login") {
                    $field = "login";
                }

                if ($value != '') {
                    if ($sql_query != $list) {
                        $sql_query .= $preposition . " ($field LIKE '%$value%') ";
                    } else {
                        $sql_query .= $preposition . " (($field LIKE '%$value%') ";
                    }
                    $found_field = 1;
                }
            }
        }
/*
        $sql_query = $list . " (login LIKE '%". $form->exportValue('new_login') ."%' ".$preposition."name  LIKE '%". $form->exportValue('name') ."%' " .$preposition . "surname LIKE '%". $form->exportValue('surname') ."%' " .$preposition . "email LIKE '%". $form->exportValue('email') ."%' " .$preposition . "active LIKE '%". $form->exportValue('active') ."%' " .$preposition . "timestamp LIKE '%". $form->exportValue('registration') ."%' " .$preposition . "(wage IS NULL OR wage LIKE '%". $form->exportValue('wage') ."%') " .$preposition . "(hired_on IS NULL OR hired_on LIKE '%". $form->exportValue('hired_on') ."%') " .$preposition . "(address IS NULL OR address LIKE '%". $form->exportValue('address') ."%') " .$preposition . "(city IS NULL OR city LIKE '%". $form->exportValue('city') ."%') " .$preposition . "(country IS NULL OR country LIKE '%". $form->exportValue('country') ."%') " .$preposition . "(father IS NULL OR father LIKE '%". $form->exportValue('father') ."%') " .$preposition . "(homephone IS NULL OR homephone LIKE '%". $form->exportValue('homephone') ."%') " .$preposition . "(mobilephone IS NULL OR mobilephone LIKE '%". $form->exportValue('mobilephone') ."%') ".$preposition . "(sex IS NULL OR sex LIKE '%". $form->exportValue('sex') ."%') " .$preposition . "(birthday IS NULL OR birthday LIKE '%". $form->exportValue('birthday') ."%') " .$preposition . "(birthplace IS NULL OR birthplace LIKE '%". $form->exportValue('birthplace') ."%') " .$preposition . "(birthcountry IS NULL OR birthcountry LIKE '%". $form->exportValue('birthcountry') ."%') " .$preposition . "(mother_tongue IS NULL OR mother_tongue LIKE '%". $form->exportValue('mother_tongue') ."%') " .$preposition . "(nationality IS NULL OR nationality LIKE '%". $form->exportValue('nationality') ."%') " .$preposition . "(company_internal_phone IS NULL OR company_internal_phone LIKE '%". $form->exportValue('company_internal_phone') ."%') " .$preposition . "(office IS NULL OR office LIKE '%". $form->exportValue('office') ."%') " .$preposition . "(doy IS NULL OR doy LIKE '%". $form->exportValue('doy') ."%') " .$preposition . "(afm IS NULL OR afm LIKE '%". $form->exportValue('afm') ."%') " .$preposition . "(police_id_number IS NULL OR police_id_number LIKE '%". $form->exportValue('police_id_number') ."%') " .$preposition . "(driving_licence IS NULL OR driving_licence LIKE '%". $form->exportValue('driving_licence') ."%') " .$preposition . "(work_permission_data IS NULL OR work_permission_data LIKE '%". $form->exportValue('work_permission_data') ."%') " .$preposition . "(national_service_completed IS NULL OR national_service_completed LIKE '%". $form->exportValue('national_service_completed') ."%') " .$preposition . "(employement_type IS NULL OR employement_type LIKE '%". $form->exportValue('employement_type') ."%') " .$preposition . "(bank IS NULL OR bank LIKE '%". $form->exportValue('bank') ."%') " .$preposition . "(bank_account IS NULL OR bank_account LIKE '%". $form->exportValue('bank_account') ."%') " .$preposition . "(marital_status IS NULL OR marital_status LIKE '%". $form->exportValue('marital_status') ."%') " .$preposition . "(transport IS NULL OR transport LIKE '%". $form->exportValue('transport') ."%') " .$preposition . "(way_of_working IS NULL OR way_of_working LIKE '%". $form->exportValue('way_of_working') ."%') " . $preposition . " (left_on IS NULL OR left_on LIKE '%". $form->exportValue('left_on') ."%')";
        if ($form->exportValue('user_type') != '') {
            $sql_query .= $preposition . "(user_type IS NULL OR user_type ='". $form->exportValue('user_type') ."'))";
        } else {
            $sql_query .= ")";
        }
*/
        if ($found_field) {
            $sql_query .= ")";
        }
//echo $sql_query."<Br>";
        $result = eF_getTableDataFlat("users LEFT OUTER JOIN module_hcd_employees ON users.login = module_hcd_employees.users_login","login", $sql_query );
//echo "result<br>";
//pr($result);
        $k = 0;
        /* Get the intersection of the two arrays */
        foreach ($employees as $key => $employee) {
            if (!in_array($employee['login'], $result['login'])) {
                unset($employees[$key]);
            }
            $k++;
        }
    }

    /* Get employee jobs */
    $recipients_array = array();
    foreach ($employees as $key => $employee) {
        $recipients_array[] = $employee['login'];
        $temp_employee = EfrontEmployeeFactory :: factory($employee['login']);
        $employees[$key]['jobs'] = $temp_employee -> getJobs();
        $employees[$key]['jobs_num'] = sizeof($employees[$key]['jobs']);
//pr($employees[$key]['jobs']);
        // Calculate the size of the div for this employee
        $maxlen = 0;
        foreach ($employees[$key]['jobs'] as $job) {
            if (($tempsump = strlen($job['description']) + strlen($job['name'])) > $maxlen) {
                $maxlen = $tempsum;
            }
        }
        $employees[$key]['div_size'] = ($maxlen + strlen(_ATBRANCH) + 2) * 15 ; // length of _ATBRANCH + 2 spaces - formula chars*size_per_char=20 / 2
        if ($employees[$key]['div_size'] > 400) {
            $employees[$key]['div_size'] = 400;
        }
    }

    // Management of the 'send email to all found' link icon on the top right of the table
    // During page load create the item
    if (!isset($_GET['ajax'])) {
        $sendmail_link = array(
             array('id' => 'sendToAllId', 'text' => _SENDMESSAGETOALLFOUNDEMPLOYEES, 'image' => "12x12/mail_icon.png", 'href' => "javascript:void(0);".implode($recipients_array, ";"), "onClick" => "this.href=document.getElementById('sendAllRecipients').value;eF_js_showDivPopup('"._SENDMESSAGE."', new Array('750px', '450px'))", 'target' => 'POPUP_FRAME')
        );
        $smarty -> assign("T_SENDALLMAIL_LINK", $sendmail_link);
    } else {
    // During ajax refresh
        $smarty -> assign("T_SENDALLMAIL_URL", "forum/new_message.php?recipient=".implode($recipients_array, ";"));
    }

    $smarty -> assign("T_EMPLOYEES_SIZE",sizeof($employees));
    $smarty -> assign("T_EMPLOYEES", $employees);
    //}
    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer);
    $smarty -> assign('T_REPORT_FORM', $renderer -> toArray());
/******************************************************************************************************/
/**************************************** PLACEMENTS **************************************************/
/******************************************************************************************************/
} else if (isset($_GET['op']) && $_GET['op'] == 'placements') {

        $employees_placements = eF_getTableData("module_hcd_employee_has_job_description NATURAL JOIN module_hcd_job_description NATURAL JOIN module_hcd_branch NATURAL JOIN module_hcd_employee_works_at_branch", "distinct description, name, job_description_ID,branch_ID, supervisor,father_branch_ID","users_login = '".$_SESSION['s_login']."' and module_hcd_employee_works_at_branch.assigned = '1'");
        if(!empty($employees_placements)) {
            $smarty -> assign("T_PLACEMENTS_SIZE", sizeof($employees_placements));
            $smarty -> assign("T_PLACEMENTS", $employees_placements);
        } else {
            $smarty -> assign("T_PLACEMENTS_SIZE", 0);
        }

/******************************************************************************************************/
/**************************************** ORGANIZATION CHART ******************************************/
/******************************************************************************************************/
} else if (isset($_GET['op']) && $_GET['op'] == 'chart') {

   /* No permissions checking - All employees may see the organization chart */

   /* The chart will be created by the eF_createBranchesTree with arguments the data gathered */
   $smarty -> assign('T_CHART_TREE', EfrontBranch :: createBranchesTree());

/**********************************************************************************************************/
/************************************ CONTROL PANEL *******************************************************/
/**********************************************************************************************************/
} else {
    /* Functions list */
    if ($currentEmployee -> getType() != _EMPLOYEE) {
        $hcdOptions[0]  = array('text' => _BRANCHES,                'image' => "32x32/cube_yellow.png",           'href' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=branches");
        $hcdOptions[1]  = array('text' => _EMPLOYEES,               'image' => "32x32/user1.png",                 'href' => $_SESSION['s_type']  . ".php?ctg=users");
        $hcdOptions[2]  = array('text' => _JOBDESCRIPTIONS,         'image' => "32x32/note.png",                  'href' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=job_descriptions");
    }

    if ($_SESSION['s_type'] == 'administrator') {
        $hcdOptions[3]  = array('text' => _SKILLS,                  'image' => "32x32/wrench.png",                'href' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=skills");
        $hcdOptions[4]  = array('text' => _FILEMANAGER,             'image' => "32x32/folder_view.png",           'href' => $_SESSION['s_type'] . ".php?ctg=users&edit_user=".$_SESSION['s_login']."&tab=file_record");
    }
    $hcdOptions[5]  = array('text' => _ORGANISATIONCHART,           'image' => "32x32/cubes.png",                 'href' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=chart");

    // TODO: eventually $hcdOptions[6]  = array('text' => _CANDIDATES,            'image' => "32x32/businessman_add.png",       'href' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=candidates");

    $smarty -> assign("T_ADMIN_OPTIONS", $hcdOptions);                    //Use the above array to build the icons table
}

// Create the message
/*
if ($message == '') {

    $message = $_GET['message'];
    $message_type = $_GET['message_type'];
}
*/
?>



