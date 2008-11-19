<?php

/**
* Get rights for HCD module
* 3 rights: administrator, _SUPERVISOR, _EMPLOYEE
* administrator: has access everywhere
* _SUPERVISOR  : has administrator rights to the branches he supervises and
*                to the employees and job descriptions of that branch
* _EMPLOYEE    : no administrator rights whatsoever
* The function returns the flat array with all branches the _SUPERVISOR type supervises
* If the user is not a _SUPERVISOR then an empty array is returned
*/
function eF_getRights() {
    // The administrator is a supervisor to all branches
    if( $_SESSION['s_type'] != "administrator") {

        // Find if the non-administrator employee supervises any branches
        $supervisor_at_branches = eF_getTableDataFlat("module_hcd_employee_works_at_branch", "branch_ID","users_login = '" .$_SESSION['s_login']. "' AND supervisor='1'");

        if (!empty($supervisor_at_branches)) {
             $_SESSION['employee_type'] = _SUPERVISOR;
             $_SESSION['supervises_branches'] = implode(',',$supervisor_at_branches['branch_ID']);
        } else {
            $_SESSION['employee_type'] = _EMPLOYEE;
        }
        return $supervisor_at_branches;
    } else {
        $_SESSION['employee_type'] = "administrator";
    }
    return array();
}


/**
* Delete employee
*
* This function deletes the designated user
* @param string $login The user to delete
* @return bool true if the user was deleted
* @todo Ask if to delete forum data
* @version 1.0
*/
function eF_deleteEmployee($login) {
    if (eF_deleteUser($login)) {
    // The rest are deleted by the deleteUser function
        eF_deleteTableData("module_hcd_employees", "users_login='".$login."'");
        eF_deleteTableData("module_hcd_employee_has_skill", "users_login='".$login."'");
        eF_deleteTableData("module_hcd_employee_has_job_description", "users_login='".$login."'");
        eF_deleteTableData("module_hcd_employee_works_at_branch", "users_login='".$login."'");
        eF_deleteTableData("module_hcd_events","users_login='".$login."'");
        return true;
    } else {
        return false;
    }
}




/**
* Register an employee user with the system
* Register his user values
* If everything ok, register his employee attributes
*/
function eF_registerEmployee($values, $is_automatic_activation, $is_ldap_user = false, $type="employee")
{

   if (eF_registerUser($values, $is_automatic_activation, $is_ldap_user, $type)) {
    eF_insertTableData("module_hcd_employees",$values);
    return true;
   } else {
    eF_deleteTableData("users", "login='".$values['login']."'");            //Delete inserted user in case of failure
    eF_deleteTableData("module_hcd_employees", "user_login='".$values['login']."'");            //Delete inserted user in case of failure
    return false;
   }

}


/**
* Delete job description
*
* This function deletes the job_Description with job_description_ID
* @job_description_ID = the job_description_ID of the branch
*/
function eF_deleteJobDescription($job_description_ID , $message, $message_type) {

    $employees_with_job = eF_getTableData("module_hcd_employee_has_job_description join module_hcd_job_description ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID =  module_hcd_job_description.branch_ID AND module_hcd_employee_has_job_description.users_login = module_hcd_employee_works_at_branch.users_login", "module_hcd_employee_works_at_branch.users_login, module_hcd_employee_works_at_branch.supervisor,module_hcd_employee_works_at_branch.branch_ID", "module_hcd_job_description.job_description_ID = '" . $job_description_ID . "'");

    foreach ($employees_with_job as $employee) {
        eF_releaseJobDescription($employee['users_login'],$job_description_ID, $employee['branch_ID'], $employee['supervisor'], &$message, &$message_type);
    }

    eF_deleteTableData("module_hcd_job_description", "job_description_ID = '" . $job_description_ID . "'");
    return true;

}




/**
* Delete branch - TO BE OPTIMIZED
*
* This function deletes a branch - all job descriptions of the branch must be deleted
* This means that user rights may change... So job descriptions must be removed with the
* special function defined above one by one
* @branch_ID = the branch_ID of the branch
* @father = the father branch of the branch
* @branches = all branches;
* @version 1.0
*/
function eF_deleteBranch($branch_ID, $father) {

    if (eF_deleteTableData("module_hcd_branch" , "branch_ID = '" . $branch_ID . "'")) {

        $jobs_of_branch = eF_getTableData("module_hcd_job_description","job_description_ID","branch_ID ='".$branch_ID."'");

        foreach ($jobs_of_branch as $job) {
            eF_deleteJobDescription ($job['job_description_ID'], &$message, &$message_type);
        }

        // TODO - svisto Logika peritto - twra praktika...
        eF_deleteTableData("module_hcd_employee_works_at_branch", "branch_ID = '" . $branch_ID . "'");

        /*Set for all children branches this $branch_IDs father branch as father branch*/
        //eF_updateTableData("module_hcd_branch", array("father_branch_ID" => $father), "father_branch_ID = '" .  $branch_ID . "'");

        // TODO - optimize aytin tin aidia - Delete all subbranches
        $subbranches = eF_getTableData("module_hcd_branch", "branch_ID", "father_branch_ID = '".$branch_ID."'");
        foreach($subbranches as $subbranch) {
           eF_deleteBranch($subbranch['branch_ID'], $branch_ID);
        }
        return true;
    } else {
        return false;
    }
}


/*
* Function find subbranches
* searches recursively all subbranches of $root
* all "possible" subbranches are given with the $branches arguement
* and an array with all subbranches is returned
*/
function eF_subBranches($root, $branches) {
    $subbranches = array();

    foreach ($branches as $branch) {
        // If the candidate subbranch has for father the root branch
        if ($branch['father_branch_ID'] == $root) {
            array_push($subbranches, $branch['branch_ID']);
            $subbranches = array_merge($subbranches, eF_subBranches($branch['branch_ID'], $branches));
        }
    }
    return $subbranches;
}

/*
* Function is subbranch
* checks if $branch is a subbranch of $root
* by traversing the entire branch tree from $branch upwards
* if father_ID == $root is encountered -> true
* else if father_ID == "0" (HEAD BRANCH) is encounterred -> false
* (note here that branch_IDs are automatically assigned as integers starting from 1)
*/
function eF_isSubbranch($subbranch, $root, $branch_tree) {
    foreach ($branch_tree as $branch) {
        // First check if the subbranch is the root of the tree
        if ($subbranch == 0) {
            return false;
        }
        // Find the branch's father
        if ($branch['branch_ID'] == $subbranch) {
            // And check whether he is the node you are looking for, the root branch or nothing (so keep scanning)
            if ($branch['father_branch_ID'] == $root) {
                return true;
            } else if ($branch['father_branch_ID'] == 0) {
                return false;
            }
            else {
                return eF_isSubbranch($branch['father_branch_ID'], $root, $branch_tree);
            }
        }
    }
}


/*
* Function eF_sendDeliverable: sends a deliverable to all recipients
* creates a new report from user with users_login $author to
* all users with users_login inside the $recipient array
* with $subject as subject and $body as the text of the report
*/
function eF_sendDeliverable($author, $recipients, $subject, $body) {

    $size = sizeof($recipients);
    if ($size) {
        $sql_query = "INSERT INTO module_hcd_deliverables (recipient_users_login,timestamp,subject,body,author_users_login) VALUES ";
        $timestamp = time();
        for ($k = 0; $k<$size; $k++) {
            $sql_query = $sql_query . "('". $recipients[$k] . "','" . $timestamp . "','" .$subject ."','".$body."','".$author."')";
            if ($k != $size-1) {
                $sql_query = $sql_query . ",";
            }
        }
        $ok = eF_execute($sql_query);
    }
    return $ok;
}

/*
 * Function eF_getBranchRoots: finds all branches that have
 * no father branch - are at the top of the branch hierarchy
 */

function eF_getBranchRoots($branch_tree) {
    $branch_roots = array();
    $index = 0;
    foreach ($branch_tree as $branch) {
        // Check if the subbranch is a root of the tree
        if ($branch['father_branch_ID'] == 0) {
            $branch_roots[$index++] = $branch;
        }
    }
    return $branch_roots;
}


/*
 * Function eF_createSubBranchTree: finds all branches that have
 * $root as father branch, prints them and their subbranches and then prints
 * the employees that work in this branch
 */
function eF_createSubBranchTree($root, $branches, $employees_at_branches, $employees_having_job_descriptions, $id, $branch_employees_num, $branch_and_sub_employees, $supervisor_at_branches) {
   $sub_tree = "<ul>";
   $init_sub_tree = $sub_tree;
   // Find all branch employees
   $branch_employees_num = 0 ;
   if ($_SESSION['s_type'] == "administrator" || ($_SESSION['employee_type'] == _SUPERVISOR && in_array($root['branch_ID'], $supervisor_at_branches['branch_ID']))) {
       $admin_rights = 1;
   }

   $branch_and_sub_employees = array();
   foreach($employees_at_branches as $employee) {
        if ($employee['branch_ID'] == $root['branch_ID']) {
            $sub_tree .= "<li class=\"employee\" id=\"dhtml_employees_node" . $id++ ."\" noDrag=\"true\" noDelete=\"true\" noRename=\"true\">";
            // No links if there are no admin-supervisor rights
            if ($admin_rights) {
                $sub_tree .= "<a href=\"".$_SESSION['s_type'].".php?ctg=";
                if ($_SESSION['s_type'] != "administrator" && $_SESSION['s_login'] == $employee['login']) {
                    $sub_tree .= "personal\" class = \"info nonEmptyLesson\">&nbsp;" . $employee['surname'] ." ". $employee['name'] . "<img class = 'tooltip' border = '0' src='images/others/tooltip_arrow.gif'/><span class = 'tooltipSpan'>" ;
                } else {
                    $sub_tree .= "users&edit_user=".$employee['login']."\" class = \"info nonEmptyLesson\">&nbsp;" . $employee['surname'] ." ". $employee['name'] . "<img class = 'tooltip' border = '0' src='images/others/tooltip_arrow.gif'/><span class = 'tooltipSpan'>" ;
                }
            } else {
                $sub_tree .= "<a href=\"javascript:void(0);\" class = \"info nonEmptyLesson\">&nbsp;" . $employee['surname'] ." ". $employee['name'] . "<img class = 'tooltip' border = '0' src='images/others/tooltip_arrow.gif'/><span class = 'tooltipSpan'>" ;
            }
//
            if ($employee['supervisor'] == 1) {
                $sub_tree = $sub_tree . "<b><u>"._SUPERVISOR."</u></b><br>";
            } else {
                $sub_tree = $sub_tree . "<b><u>"._EMPLOYEE."</u></b><br>";
            }

            foreach ($employees_having_job_descriptions as $employee_jd) {
                    if ($employee_jd['branch_ID'] == $root['branch_ID'] && $employee_jd['login'] == $employee['login']) {
                        $sub_tree = $sub_tree . $employee_jd['description'] . "<br>";
                    }
            }

            $sub_tree = $sub_tree . "</span></a>&nbsp;<a style='vertical-align=middle;' href='forum/new_message.php?recipient=".$employee['login']."' onclick='eF_js_showDivPopup(\"\", new Array(\"750px\", \"450px\"))' target='POPUP_FRAME'><img src='images/12x12/mail_icon.png' border='0' /></a></li>\n";

            $login = $employee['login'];
            $branch_and_sub_employees[$login] = $login;
            $branch_employees_num++;
        }
   }

    // Find all subbranches
    foreach ($branches as $branch) {
        // Check if the father branch of this branch is $root
        if ($branch['father_branch_ID'] == $root['branch_ID']) {
            $sub_branches = eF_createSubBranchTree($branch, $branches, $employees_at_branches,$employees_having_job_descriptions, &$id, &$this_branch_employees, &$sub_branch_employees);

            // The same employees will be overwritten
            foreach ($sub_branch_employees as $login => $employee) {
                $branch_and_sub_employees[$login] = $employee;
            }

            // Add this node's link
            if ($admin_rights) {
                $sub_tree = $sub_tree . "<li class=\"branchLarge\" id=\"dthml_subbranches_node" . $id++ ."\" noDrag=\"true\" noDelete=\"true\" noRename=\"true\"><a href=\"".$_SESSION['s_type'].".php?ctg=module_hcd&op=branches&edit_branch=".$branch['branch_ID']."\"  class = \"info nonEmptyLesson\">&nbsp;" .$branch['name']."<img class = 'tooltip' border = '0' src='images/others/tooltip_arrow.gif'/><span class = 'tooltipSpan'><b><u>".$branch['name']."</u></b>";
            } else {
                $sub_tree = $sub_tree . "<li class=\"branchLarge\" id=\"dthml_subbranches_node" . $id++ ."\" noDrag=\"true\" noDelete=\"true\" noRename=\"true\"><a href=\"javascript:void(0);\"  class = \"info nonEmptyLesson\">&nbsp;" .$branch['name']."<img class = 'tooltip' border = '0' src='images/others/tooltip_arrow.gif'/><span class = 'tooltipSpan'><b><u>".$branch['name']."</u></b>";
            }

            $sub_tree .= " " . $this_branch_employees;
            if (sizeof($sub_branch_employees) != 0) {
                $sub_tree .= " (".sizeof($sub_branch_employees).")";
            }

            if ($branch['address'] != '') {
                $sub_tree .= "<br>" ._ADDRESS.": " . $branch['address'];
                if  ($branch['city'] != '' || $branch['country'] != '') {
                   $sub_tree .= ", ";
                }
            }

            if ($branch['city'] != '') {
                $sub_tree .= $branch['city'];
                if  ($branch['country'] != '') {
                   $sub_tree .= ", ";
                }
            }

            if ($branch['country'] != '') {
               $sub_tree .= $branch['country'];
            }

            if ($branch['telephone'] != '') {
                $sub_tree .= "<br>"._TELEPHONE.": " . $branch['telephone'];
            }

            if ($branch['email'] != '') {
                $sub_tree .= "<br>"._EMAIL.": " . $branch['email'];
            }

            $sub_tree .= "</span></a>\n";


            $sub_tree = $sub_tree . $sub_branches;
            $sub_tree = $sub_tree . "</li>";
        }
    }

    if ($sub_tree != $init_sub_tree) {
       return $sub_tree . "</ul>";
    } else {
       return "";
    }
}


/*
* Function eF_createBranchesTree: creates a string that has the html
* code for the organization chart branch tree
*/
function eF_createBranchesTree($branches, $employees_at_branches, $employees_having_job_descriptions) {
    global $loadScripts;
    $loadScripts[] = 'drag-drop-folder-tree';


    global $currentUser;
    $currentEmployee = $currentUser -> aspects['hcd'];

    $tree = "<ul id=\"dhtmlgoodies_branches_tree\" class=\"dhtmlgoodies_tree\">\n";

    $root_nodes = eF_getBranchRoots($branches);

    $id = 0; // this variable is increased for every new node of the tree
    foreach ($root_nodes as $root) {

        $all_subbranches_employees = array();
        // First all subbranches will be created, in order to get the sum of employees
        $sub_tree = eF_createSubBranchTree($root, $branches, $employees_at_branches, $employees_having_job_descriptions, &$id, &$all_employees, &$all_subbranches_employees, $currentEmployee -> supervisesBranches);
        // Create the span that will appear on hover
        $tree = $tree . "<li class=\"branchLarge\" id=\"dhtml_branch_node" . $id++ ."\" noDrag=\"true\" noDelete=\"true\" noRename=\"true\">";

        if ($currentUser -> getType() == "administrator" || ($currentEmployee -> getType() == _SUPERVISOR && in_array($root['branch_ID'], $currentEmployee -> supervisesBranches))) {
            $admin_rights = 1;
        }

        if ($admin_rights) {
            $tree .= "<a href=\"".$_SESSION['s_type'].".php?ctg=module_hcd&op=branches&edit_branch=".$root['branch_ID']."\"  class = \"info nonEmptyLesson\">&nbsp;" .$root['name']."<img class = 'tooltip' border = '0' src='images/others/tooltip_arrow.gif'/><span class = 'tooltipSpan'><b><u>".$root['name']. "</u></b>";
        } else {
            $tree .= "<a href=\"javascript:void(0);\"  class = \"info nonEmptyLesson\">&nbsp;" .$root['name']."<img class = 'tooltip' border = '0' src='images/others/tooltip_arrow.gif'/><span class = 'tooltipSpan'><b><u>".$root['name']. "</u></b>";
        }


        $tree .= " " . $all_employees;
        if (sizeof($all_subbranches_employees) != 0) {
            $tree .= " (".sizeof($all_subbranches_employees).")";
        }

        if ($root['address'] != '') {
            $tree .= "<br>" . _ADDRESS.": ". $root['address'];
            if  ($root['city'] != '' || $root['country'] != '') {
               $tree .= ", ";
            }
        }

        if ($root['city'] != '') {
            $tree .= $root['city'];
            if  ($root['country'] != '') {
               $tree .= ", ";
            }
        }

        if ($root['country'] != '') {
            $tree .= $root['country'];
        }

        if ($root['telephone'] != '') {
            $tree .= "<br>"._TELEPHONE.": " . $root['telephone'];
        }

        if ($root['email'] != '') {
            $tree .= "<br>"._EMAIL.": " . $root['email'];
        }

        $tree .= "</span>";

        $tree .= "</a>\n";


        $tree = $tree . $sub_tree;
        $tree = $tree . "</li>\n";
    }
    return $tree . "</ul>";
}


/*
* Function eF_promoteEmployee: sets an employee to supervisor in a branch and all subbranches
* $employee: the employee to be set as supervisor
* $branch: the branch where that he will supervise (and all below it)
*/
function eF_promoteEmployee($employee, $branch) {

    /* We need only select the branches the supervisor can manage - together with their father_IDs, because the supervisor cannot employ sb to a higher position than his */
     if ($_SESSION['s_type'] == 'administrator') {
        $all_possible_subbranches = eF_getTableData("module_hcd_branch", "branch_ID, father_branch_ID","");
    } else {
        $all_possible_subbranches = eF_getTableData("module_hcd_branch", "branch_ID, father_branch_ID", "branch_ID IN (" . $_SESSION['supervises_branches'] . ")");
    }
    /* Now select the ones that are below the branch this_employee was set to supervise */
    $branches_to_supervise = eF_subBranches($branch, $all_possible_subbranches);

    // The employee might be assigned to some of the branches he will be set as a supervisor due to his new rights
    $subbranches_already_working = eF_getTableDataFlat("module_hcd_employee_works_at_branch", "branch_ID", "users_login= '".$employee."' AND assigned = '1' AND branch_ID IN (". implode(",",$branches_to_supervise) . ")");

    if (!empty($subbranches_already_working)) {
        eF_deleteTableData("module_hcd_employee_works_at_branch", "users_login= '".$employee."' AND branch_ID IN (". implode(",",$subbranches_already_working['branch_ID']) . ")");
    }
    if (!empty($branches_to_supervise)) {
        $string_to_insert = "(";
        $size = sizeof($branches_to_supervise);

        if ($size) {
            for ($i = 0 ; $i < $size; $i++) {
                if (in_array($branches_to_supervise[$i], $subbranches_already_working['branch_ID'])) {
                    $string_to_insert = $string_to_insert . "'" . $employee . "', '1', '1', '" . $branches_to_supervise[$i] . "')";
                }else {
                    $string_to_insert = $string_to_insert . "'" . $employee . "', '1', '0', '" . $branches_to_supervise[$i] . "')";
                }

                if ($i != ($size-1)) {
                    $string_to_insert = $string_to_insert . ", (";
                }
            }
            /* We insert multiple values and therefore we cannot use insertTableData */
            eF_execute("INSERT INTO module_hcd_employee_works_at_branch VALUES " . $string_to_insert);
        }
    }
}

 /*
* Function eF_demoteEmployee: sets an employee from supervisor to employee in a branch and all subbranches
* $employee: the employee to be set as supervisor
* $branch: the branch where that he will supervise (and all below it)
*/
function eF_demoteEmployee($employee, $branch) {

    $employee_supervises_other_branches = eF_getTableDataFlat("module_hcd_employee_works_at_branch", "branch_ID", "users_login = '".$employee. "' AND supervisor = '1' AND branch_ID <> '".$branch."'");

    /* If we reach this point then all privileges to the branches below need to be recalled */
    $all_branches = eF_getTableData("module_hcd_branch", "branch_ID, father_branch_ID", "");

    $branches_to_recall_supervision = eF_subBranches($branch, $all_branches);

    $strictly_supervises_branches = eF_getTableDataFlat("module_hcd_employee_works_at_branch", "branch_ID", "supervisor = '1' and assigned = '1' and users_login = '".$employee."'");
    $i = 0;
    $branches_to_keep = array();
    foreach($strictly_supervises_branches['branch_ID'] as $strict_branch) {
        if($strict_branch != $branch && eF_isSubbranch($strict_branch, $branch, $all_branches)) {
            $results = eF_subBranches($strict_branch, $all_branches);
            $branches_to_keep[$i++] = $strict_branch;
            foreach ($results as $another_branch_to_keep) {
                $branches_to_keep[$i++] = $another_branch_to_keep;
            }
        }
    }

    $branches_to_recall_supervision_list = eF_makeListIDs($branches_to_recall_supervision);
    $branches_to_keep_list = eF_makeListIDs($branches_to_keep);

    // FINALLY: delete all subbranches that exist in the branches_to_recall_supervision list and that do not exist in the branches_to_keep_list list
    $string_to_insert = "DELETE FROM module_hcd_employee_works_at_branch WHERE supervisor='1' AND assigned = '0' AND users_login = '".$employee."'";
    if (!empty($branches_to_recall_supervision)) {
        $string_to_insert = $string_to_insert . " AND branch_ID IN (" . $branches_to_recall_supervision_list . ")";
    }
    if (!empty($branches_to_keep)) {
        $string_to_insert = $string_to_insert . " AND branch_ID NOT IN (" . $branches_to_keep_list . ")";
    }

    //echo  $string_to_insert. "<br>";
    /* We delete multiple values and therefore we cannot use deleteTableData */
    eF_execute($string_to_insert);


}

/*
* Function eF_changeEmployeeBranchRights: changes the employee rights at a branch
* $employee: the employee whose rights will be changed
* $branch: the top branch where all rights will change (from there and below)
* $position: 0 for employee/ 1 for supervisor
*/
function eF_changeEmployeeBranchRights($employee, $branch, $position) {
    $insert_branch = array('users_login' => $employee, 'supervisor' => $position, 'assigned' => '1', 'branch_ID' => $branch);

    $current_supervisor_rights = eF_getTableData("module_hcd_employee_works_at_branch", "supervisor", "users_login='".$employee . "' AND branch_ID = '".$branch ."'");
    if (!empty($current_supervisor_rights)) {
        $assigned_to_branch = 1;
        if ($current_is_supervisor_rights[0]['supervisor'] == 1) {
            $is_supervisor = 1;
        } else {
            $is_supervisor = 0;
        }
    }  else {
       $assigned_to_branch = 0;
    }

    // If the employee is not currently assigned to this branch
    if ($assigned_to_branch == 0) {
        eF_insertTableData("module_hcd_employee_works_at_branch", $insert_branch);
    } else {
        eF_updateTableData("module_hcd_employee_works_at_branch", array("assigned" => '1') , "users_login='".$employee."' AND branch_ID ='". $branch ."'");
    }

    if ($position == '1' && $is_supervisor == 0) {
        eF_promoteEmployee($employee, $branch);
    } else if($position == '0' && $is_supervisor == 1) {
        // Check if the father is supervised
        $employee_supervises_father = eF_getTableDataFlat("module_hcd_employee_works_at_branch, module_hcd_branch", "branch_ID", "users_login = '".$employee. "' AND supervisor = '1' AND module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.father_branch_ID AND module_hcd_branch.branch_ID = '".$branch ."'");
        if (empty($employee_supervises_father)) {
            eF_demoteEmployee($employee, $branch);
        }
    }
}


/*
* Function eF_assignJobDescription: assigns a job to an employee
* $employee: the employee to take the job
* $job_description: the job description ID of the job to be taken
* $branch: the branch of the job to be taken
* $position: 0 for employee/ 1 for supervisor
* $message*: the message to be returned
* Special care is taken for the supervisor rights
*/
function eF_assignJobDescription($employee, $job_description, $branch, $position , $message, $message_type) {
   $has_job = eF_getTableData("module_hcd_employee_has_job_description", "job_description_ID", "users_login='". $employee . "' AND job_description_ID='".  $job_description . "'");
   if (empty($has_job)) {
       if ($job_description != "" && $job_description != "0") {
           $insert_job = array('users_login' => $employee, 'job_description_ID' => $job_description);

           eF_changeEmployeeBranchRights($employee, $branch, $position);

           if($ok = eF_insertTableData("module_hcd_employee_has_job_description", $insert_job)) {
               $message      = _OPERATIONCOMPLETEDSUCCESFULLY;
               $message_type = 'success';
           } else {
               $message      = _PLACEMENTCOULDNOTBEASSIGNED.": ".$ok;
               $message_type = 'failure';
           }
       }
    } else {
       $message      = _JOBALREADYASSIGNED;
       $message_type = 'failure';
    }
}

/*
* Function eF_releaseJobDescription: releases a job from an employee
* $employee: the employee to take the job from
* $job_description: the job description ID of the job to be taken
* $branch: the branch of the job to be taken
* $position: 0 for employee/ 1 for supervisor
* $message*: the message to be returned
* Special care is taken for the supervisor rights
*/
function eF_releaseJobDescription($employee,$job_description, $branch, $position, $message, $message_type) {
    /* Check whether permissions need to be changed*/
    if($position == '1') {

        // TODO: possible optimization here - move the following code into the demoteEmployee -ooooooooooooooooo
        /* Check if the employee works is a supervisor in the father branch -> no need to change permissions */
        $employee_supervises_other_branches = eF_getTableDataFlat("module_hcd_employee_works_at_branch", "branch_ID", "users_login = '".$employee. "' AND supervisor = '1' AND branch_ID <> '".$branch."'");
        $father_ID = eF_getTableData("module_hcd_branch", "father_branch_ID", "branch_ID = '".$branch."'");
        if (in_array($father_ID[0]['father_branch_ID'],$employee_supervises_other_branches['branch_ID'])) {
            $supervises_father = 1;
        }

        /* Check if the employee works at another position in the branch */
        $other_placements = eF_getTableData("module_hcd_employee_has_job_description NATURAL JOIN module_hcd_job_description NATURAL JOIN module_hcd_branch NATURAL JOIN module_hcd_employee_works_at_branch", "branch_ID, supervisor","users_login = '". $employee . "' and branch_ID = '" .$branch ."' and job_description_ID <> '".$job_description ."' and assigned = '1'");
        if (!empty($other_placements)) {
            foreach ($other_placements as $placement) {
                if ($placement['supervisor'] == 1) {
                    /* This means that the employee is a supervisor in another position in the same branch, so that we don't need to change permissions */
                    $supervises_twice = 1;
                } else {
                    /* This means that the employee works at another position in the branch */
                    $other_non_supervisor_placement = 1;
                }
            }
        } else {
            $no_other_placement_in_branch = 1;
        }
        if (!isset($supervises_twice)) {
            if (isset($supervises_father)) {
                eF_updateTableData("module_hcd_employee_works_at_branch", array("assigned" => "0") , "users_login='".$employee."' AND branch_ID ='". $branch  ."' AND supervisor = '1' AND assigned = '1'" );
            } else {
                eF_deleteTableData("module_hcd_employee_works_at_branch", "users_login='".$employee."' AND branch_ID ='". $branch  ."' AND supervisor = '1'" );
                if (!isset($other_non_supervisor_placement)) {
                    eF_deleteTableData("module_hcd_employee_works_at_branch", "users_login='".$employee."' AND branch_ID ='". $branch  ."'");
                }
            }
            if (!isset($supervises_father)) {
            // Recall that employee's privileges for all subbranches
                eF_demoteEmployee($employee, $branch);
            }
        }

    } else {
        $other_placements = eF_getTableData("module_hcd_employee_has_job_description NATURAL JOIN module_hcd_job_description NATURAL JOIN module_hcd_branch NATURAL JOIN module_hcd_employee_works_at_branch", "branch_ID, supervisor","users_login = '". $employee . "' and branch_ID = '" .$branch ."' and job_description_ID <> '".$job_description ."' and assigned = '1'");
        if (empty($other_placements)) {
            eF_deleteTableData("module_hcd_employee_works_at_branch", "users_login='".$employee."' AND branch_ID ='". $branch  ."'" );
        }
    }

    /* Deleting the job selected */
    if (eF_deleteTableData("module_hcd_employee_has_job_description", "users_login='".$employee."' AND job_description_ID ='".$job_description."'" )) {
        /* And checking whether the employee still works at the branch */
        //$any_jobs_left = eF_getTableData("module_hcd_employee_has_job_description NATURAL JOIN module_hcd_job_description NATURAL JOIN module_hcd_employee_works_at_branch", "branch_ID", "users_login = '" . $employee. "' AND branch_ID='" .$branch ."'");

        //if (empty($any_jobs_left)) {
        //    eF_deleteTableData("module_hcd_employee_works_at_branch", "users_login='".$employee."' AND branch_ID ='".$branch  ."'" );
        //}
        $message      = _EMPLOYEERELEASEDFROMJOB;
        $message_type = 'success';

        // Register job release into the event log
        $job_data = eF_getTableData("module_hcd_job_description JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID", "description, name", "job_description_ID='".  $job_description . "'");
        eF_insertTableData("module_hcd_events", array("event_code"    => $MODULE_HCD_EVENTS['JOB'],
                                                      "users_login"   => $employee,
                                                      "author"        => $_SESSION['s_login'],
                                                      "specification" => _EMPLOYEEWASRELEASEDFROMDJOB .": " .$job_data[0]["description"]. " ". _ATBRANCH . " " .$job_data[0]["name"],
                                                      "timestamp"     => time()));
    } else {
        $message      = _SOMEPROBLEMEMERGED;
        $message_type = "failure";
    }
}



function eF_getHcdMenu()
{

    $menuHCD = array();

    $menuHCD['module_hcd']          = array('title' => _CONTROLPANEL,   'image' => "factory", 'target' => "mainframe", 'id' => 'hcd_control_panel_a' ,  'link' => $_SESSION['s_type'].'.php?ctg=module_hcd');

    if ($_SESSION['employee_type'] != _EMPLOYEE) {
        $menuHCD[0]  = array('title' => _BRANCHES,                'image' => "cube_yellow",  'id' => 'branches_a',    'target' => "mainframe",        'link' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=branches");
        // The administrator handles employees from the users menu - not the HCD
        if ($_SESSION['s_type'] != 'administrator') {
            $menuHCD[1]  = array('title' => _EMPLOYEES,           'image' => "user1",        'id' => 'employees_a',   'target' => "mainframe",        'link' => $_SESSION['s_type']  . ".php?ctg=users");
        }
        $menuHCD[2]  = array('title' => _JOBDESCRIPTIONS,         'image' => "note",         'id' => 'job_descriptions_a' , 'target' => "mainframe",  'link' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=job_descriptions");
    }

    if ($_SESSION['s_type'] == 'administrator') {
        $menuHCD[3]  = array('title' => _SKILLS,                  'image' => "wrench",       'id' => 'skills_a',       'target' => "mainframe", 'link' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=skills");
        //$menuHCD[4]  = array('title' => _FILEMANAGER,             'image' => "folder_view",  'id' => 'file_manager_a', 'target' => "mainframe", 'link' => $_SESSION['s_type'] . ".php?ctg=users&edit_user=".$_SESSION['s_login']."&tab=file_record");
    }
    $menuHCD[5]  = array('title' => _ORGANISATIONCHART,           'image' => "cubes",        'id' => 'chart_a',     'target' => "mainframe",    'link' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=chart");

    // TODO: eventually $hcdOptions[6]  = array('title' => _CANDIDATES,            'image' => "businessman_add",   'target' => "mainframe",    'link' => $_SESSION['s_type'] . ".php?ctg=module_hcd&op=candidates");

    return $menuHCD;
}


/*
 * Function eF_fatherExistsInArray: a version of in_array to find whether
 * the father branch of a branch exists in the branch_tree array.
 * More general than in_array()
 */

function eF_fatherExistsInArray($father_id, $branch_tree) {

    foreach ($branch_tree as $branch) {
        if ($branch['branch_ID'] == $father_id) {
            return true;
        }
    }
    return false;
}


/*
 * Function eF_getArrayTopBranches: finds all branches that have
 * no father branch or whose father branch does not exist in the array
 */

function eF_getArrayTopBranches($branch_tree) {

    $branch_topbranches = array();
    $index = 0;

    foreach ($branch_tree as $branch) {
        // Check if the subbranch is a root of the tree or if the subbranch's father does not exist in the array
        if ($branch['father_branch_ID'] == 0 || !eF_fatherExistsInArray($branch['father_branch_ID'], $branch_tree)) {
            $branch_topbranches[$index++] = array("branch_ID" => $branch['branch_ID'],"name" => $branch['name']);
        }
    }

    return $branch_topbranches;
}



/*
 * Function eF_createSubBranchTreeSelect: finds all branches that have
 * $root as father branch, and creates the value for the select pair
 * and prints them with $level * "  " + >>
 */
function eF_createSubBranchTreeSelect($root, $branches, $level) {

   $select_subtree = array();
   // Find all subbranches
    foreach ($branches as $branch) {
        // Check if the father branch of this branch is $root
        if ($branch['father_branch_ID'] == $root['branch_ID']) {

            // Add this node's link
                $log = $branch['branch_ID'];
                $string = "&#160;";
                for ($i = 1; $i < $level; $i++) {
                    $string .= "&#160;&#160;";
                    //echo "--";
                }
                //echo " eimai o " . $log . " kai tha enwthw me tous<br>";
                $select_subtree["'".$log."'"] = $string . "&#187;&#160;" . $branch['name'];

                $select_subtree = array_merge($select_subtree, eF_createSubBranchTreeSelect($branch, $branches, $level + 1));

        }
    }

    return $select_subtree;
}

/*
* Function eF_createBranchesTreeSelect: creates the array for the select boxes which show branch hierarchy
* $only_existing parameter:
*       0 then then first selection is '0'=>''
*       1 then then first selection is the first branch
*       2 then then first selection is '0'=>'' and we also have and 'all'=>_ALLBRANCHES option
*       3 then then first selection is we have the 'all'=>_ALLBRANCHES option
*       4 then then first selection is '0'=> _ANYBRANCH
* the $branches array must contain 'branch_ID','name' and 'father_branch_ID'
*/
function eF_createBranchesTreeSelect($branches, $only_existing ) {

   $top_nodes = eF_getArrayTopBranches($branches); // this is used instead of getRootBranches because some branches selects
                                                    // might not include root branches
   $select_tree = array();
    if ($only_existing == 0 || $only_existing == 2) {
        $select_tree["'0'"] = "";
    }

    if ($only_existing == 4) {
        $select_tree["'0'"] = _ANYBRANCH;
    } else if ($only_existing >= 2) {
        $select_tree["'all'"] = _ALLBRANCHES;
    }

    foreach ($top_nodes as $top) {
        $log = $top['branch_ID'];
        $select_tree["'".$log."'"] = $top['name'];

        $select_tree = array_merge($select_tree, eF_createSubBranchTreeSelect($top, $branches, 1));
    }

    // To delete the '' needed for correct array merging
    $keys = array_keys($select_tree);
    $i = 0;
    foreach ($select_tree as $select) {
        $new_key = substr($keys[$i],1,strlen($keys[$i++])-2) ;
        $final_select["$new_key"] = $select;
    }
    return $final_select;
}


/*
* Function eF_insertJobDescriptionAll: inserts a job description to all existing branches
* $job_description: the description of the job to be inserted
* $job_role_description: the analytic description of the job
* $branches: an array with all existing branches
* $needed: the number of vacancies for the posito
*/
function eF_insertJobDescriptionAll($job_description, $job_role_description, $needed, $branches) {
    $sql_query = "INSERT INTO module_hcd_job_description (employees_needed, description, job_role_description, branch_ID) VALUES ";
    $size = sizeof($branches);

    if ($size) {
        for ($k = 0; $k<$size; $k++) {
            $sql_query .= "('". $needed . "','" . $job_description . "','" . $job_role_description . "','" .$branches[$k]['branch_ID'] ."')";
            if ($k != $size-1) {
                $sql_query = $sql_query . ",";
            }
        }

        $ok = eF_execute($sql_query);
    }

    return $ok;
}


/*
* Function eF_updateJobDescriptionAll: updates a job description to all existing branches
* $job_description_id: the id of the job description with the previous job description name
* $job_role_description: the analytic description of the job
* $job_description: the new description of the job
* $needed: the number of vacancies for the posito
*/
function eF_updateJobDescriptionAll($id, $job_description, $job_role_description, $needed) {
    $previous_description = eF_getTableData("module_hcd_job_description","description","job_description_ID = '".$id."'");
    return eF_updateTableData("module_hcd_job_description", array("description" => $job_description, "employees_needed" => $needed, "job_role_description" => $job_role_description), "description = '".$previous_description[0]['description']."'");
}



/*
* Function eF_getBranchEmployees: finds all employees that the current user is permitted to see in a branch
* $branch: the branch to search
*/
function eF_getBranchEmployees($branch) {
    /* Find all employees of this branches - AISXOS DEVELOPMENT - just working... */
    if ($_SESSION['employee_type'] == _SUPERVISOR) {
        // Find employees at the branch
        $employees_at_branch =  eF_getTableData("users JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description  ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active, module_hcd_job_description.description, module_hcd_job_description.job_description_ID, module_hcd_job_description.branch_ID, module_hcd_employee_works_at_branch.supervisor", "module_hcd_job_description.branch_ID IN (".$_SESSION['supervises_branches'].") AND module_hcd_job_description.branch_ID = '". $branch . "' AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login AND users.user_type != 'administrator'" ,"","");

        $size = sizeof($employees_at_branch);
        if ($size != 0) {
            // Create a list for all employees working at the branch
            $list = "";
            for ($k = 0; $k < $size; $k++) {
               $list = $list . "'". $employees_at_branch[$k][login] . "'";
               if($k != $size -1) {
                   $list = $list . ",";
               }

            }

            // Get all employees that are not at the branch according to that list (the ones not in the list)
            $employees_not_at_branch =  eF_getTableData("users LEFT OUTER JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description  ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active","(module_hcd_job_description.branch_ID IS NULL OR (module_hcd_job_description.branch_ID <> '". $branch . "' AND module_hcd_employee_works_at_branch.branch_ID IN (" .$_SESSION['supervises_branches'] .") AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login)) AND users.login NOT IN (".$list.") AND (module_hcd_employee_works_at_branch.branch_ID IS NULL OR (module_hcd_job_description.branch_ID <> '". $branch . "' AND module_hcd_employee_works_at_branch.branch_ID IN (" .$_SESSION['supervises_branches'] ."))) AND users.user_type != 'administrator'","","");

            if (!empty($employees_not_at_branch)) {
                $employees = array_merge($employees_at_branch, $employees_not_at_branch);
            } else {
                $employees = $employees_at_branch;
            }
        } else {
            $employees =  eF_getTableData("users LEFT OUTER JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description  ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active","(module_hcd_job_description.branch_ID IS NULL OR (module_hcd_job_description.branch_ID <> '". $branch . "' AND module_hcd_employee_works_at_branch.branch_ID IN (" .$_SESSION['supervises_branches'] .") AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login)) AND users.login NOT IN (".$list.") AND (module_hcd_employee_works_at_branch.branch_ID IS NULL OR (module_hcd_job_description.branch_ID <> '". $branch . "' AND module_hcd_employee_works_at_branch.branch_ID IN (" .$_SESSION['supervises_branches'] ."))) AND users.user_type != 'administrator'","","");

        }
    } else if ($_SESSION['s_type'] == "administrator") {
        $employees_at_branch =  eF_getTableData("users JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description  ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active, module_hcd_job_description.description, module_hcd_job_description.job_description_ID, module_hcd_job_description.branch_ID, module_hcd_employee_works_at_branch.supervisor", "module_hcd_job_description.branch_ID = '". $branch . "' AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login" ,"","");
        $size = sizeof($employees_at_branch);
        if ($size != 0) {
            // Create a list for all employees working at the branch
            $list = "";
            for ($k = 0; $k < $size; $k++) {
               $list = $list . "'". $employees_at_branch[$k][login] . "'";
               if($k != $size -1) {
                   $list = $list . ",";
               }

            }

            // Get all employees that are not at the branch according to that list (the ones not in the list)
            $employees_not_at_branch =  eF_getTableData("users LEFT OUTER JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description  ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active", "(module_hcd_job_description.branch_ID IS NULL OR (module_hcd_job_description.branch_ID <> '". $branch . "' AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login)) AND users.login NOT IN (".$list.")" ,"","");

            if (!empty($employees_not_at_branch)) {
                $employees = array_merge($employees_at_branch, $employees_not_at_branch);
            } else {
                $employees = $employees_at_branch;
            }
        } else {
            $employees = eF_getTableData("users LEFT OUTER JOIN (module_hcd_employee_has_job_description JOIN module_hcd_job_description ON module_hcd_employee_has_job_description.job_description_ID = module_hcd_job_description.job_description_ID JOIN module_hcd_branch ON module_hcd_job_description.branch_ID = module_hcd_branch.branch_ID JOIN module_hcd_employee_works_at_branch ON module_hcd_employee_works_at_branch.branch_ID = module_hcd_branch.branch_ID) ON users.login = module_hcd_employee_has_job_description.users_login", "distinct users.login, users.name, users.surname, users.pending, users.active", "module_hcd_job_description.branch_ID IS NULL OR (module_hcd_job_description.branch_ID <> '". $branch . "' AND  users.login = module_hcd_employee_works_at_branch.users_login AND users.login = module_hcd_employee_has_job_description.users_login) " ,"","");
        }

    }

    return $employees;
}

/*
 * Function eF_getJobDescriptionId: gets as arguments a job description and a branch
 * If such a record exists for that description-branch combination then its ID is returned
 * Otherwise, a new record is created for this combination and is returned
 * Very useful for job assignments without job_description_ID knowledge
 */
function eF_getJobDescriptionId($description, $branch) {
//echo $description ."**<BR>";
//    $description = imap_utf7_encode($description);
//echo $description ."**<BR>";
    $job_exists = eF_getTableData("module_hcd_job_description", "job_description_ID", "description = '".$description."' AND branch_ID = '".$branch."'");
    if (empty($job_exists)) {
 //   echo $description . "--<br>";
 //       $description = '"'.$description.'"';
 //       echo $description . "---<br>";
        $new_job_description_ID = eF_insertTableData("module_hcd_job_description", array('description' => $description, 'branch_ID' => $branch));
    } else {
        $new_job_description_ID = $job_exists[0]['job_description_ID'];
    }
    return $new_job_description_ID;
}


/*
 * Function eF_getBranchAncestors: gets all branches that are
 * ancestors of the $branch. The $branches array contains all branches to be searched
 * with at least the 'branch_ID', 'father_branch_ID' fields
 */
function eF_getBranchAncestors($branch, $branches) {

    if ($branch['father_branch_ID'] && $branch['father_branch_ID'] != 0) {
        foreach ($branches as $candidate_father) {
            if ($branch['father_branch_ID'] == $candidate_father['branch_ID']) {
                $father_ancestors = eF_getBranchAncestors($candidate_father, $branches);
                if ($father_ancestors) {
                    $result = array_merge($father_ancestors, array($candidate_father['branch_ID'] => $candidate_father['branch_ID']));
                } else {
                    $result = array($candidate_father['branch_ID'] => $candidate_father['branch_ID']);
                }

                return $result;
            }

        }
    } else {
        return $false;
    }
}

?>