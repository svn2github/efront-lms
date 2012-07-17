<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

$_change_ = true;
if ((isset($currentUser -> coreAccess['organization']) && $currentUser -> coreAccess['organization'] == 'view') || (!$currentEmployee->isSupervisor() && $currentUser -> getType() != "administrator")) {
 $_change_ = false;
}
$smarty -> assign("_change_", $_change_);

try {
    /* Check permissions: only admins have add/edit privileges. supervisors may only see skills */
    if ($currentEmployee -> getType() == _EMPLOYEE) {
        $message = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = 'failure';
        eF_redirect(basename($_SERVER['PHP_SELF'])."&message=".urlencode($message)."&message_type=".$message_type);
        exit;
    }

    if ((isset($_GET['edit_skill']) && $currentEmployee -> getType() != _SUPERVISOR && $currentUser -> getType() != 'administrator') || (isset($_GET['delete_skill']) && $currentUser -> getType() != 'administrator')) {
        $message = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
        $message_type = 'failure';
        eF_redirect($_SESSION['s_type'].".php?ctg=module_hcd&op=skills&message=".urlencode($message)."&message_type=".$message_type);
        exit;
    }

    if (isset($_GET['delete_skill'])) {
     try {
         $currentSkill = new EfrontSkill($_GET['delete_skill']);
         $currentSkill -> delete();
     } catch (Exception $e) {
      handleAjaxExceptions($e);
     }
     exit;
    } else if (isset($_GET['remove_user_skill']) && eF_checkParameter($_GET['remove_user_skill'], 'id')) {
     try {
      $currentSkill = new EfrontSkill($_GET['remove_user_skill']);
      if (in_array($_GET['user'], array_keys($currentSkill -> getEmployees())) && eF_checkParameter($_GET['user'], 'login')) {
       $currentSkill -> removeFromEmployee($_GET['user']);
      }
     } catch (Exception $e) {
      handleAjaxExceptions($e);
     }
     exit;
    } else if (isset($_GET['add_skill']) || isset($_GET['edit_skill'])) {

        if (isset($_GET['add_skill'])) {
            $form = new HTML_QuickForm("skill_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=skills&add_skill=1", "", null, true);
        } else {
            $form = new HTML_QuickForm("skill_form", "post", $_SESSION['s_type'].".php?ctg=module_hcd&op=skills&edit_skill=".$_GET['edit_skill'] , "", null, true);
            $currentSkill = new EfrontSkill( $_GET['edit_skill']);
        }

        $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
        $form -> addElement('text', 'skill_description', _SKILLDESCRIPTION, 'id="skill_description" class = "inputText" tabindex="1"');
        $form -> addRule('skill_description', _THEFIELD.' '._SKILLDESCRIPTION.' '._ISMANDATORY, 'required', null, 'client');

        $result = eF_getTableData("module_hcd_skill_categories", "id, description", "");
        $skillCategories = array("0" => _SELECTSKILLCATEGORY);
        foreach($result as $value) {
            $skillCategories[$value['id']]= $value['description'];
        }
        $form -> addElement('select', 'category' , _SKILLCATEGORY, $skillCategories , 'class = "inputText" id="skill_cat" onchange="javascript:change_skill_category(\'skill_cat\')" tabindex="2"');

        if (isset($_GET['edit_skill'])) {
         if ($_GET['postAjaxRequest']) {
          try {
           if ($_GET['insert'] == "true") {
            $currentSkill -> assignToEmployee($_GET['add_user'], $_GET['specification'], $_GET['score']);
           } else if ($_GET['insert'] == "false") {
            $currentSkill -> removeFromEmployee($_GET['add_user']);
           } else if (isset($_GET['addAll'] )) {
            $employees = $currentSkill -> getEmployees();
            isset($_GET['filter']) ? $employees = eF_filterData($employees,$_GET['filter']) : null;
            foreach ($employees as $employee) {
             if ($employee['skill_ID'] == "") {
              $currentSkill -> assignToEmployee($employee['login'], "");
             }
            }
           } else if (isset($_GET['removeAll'] )) {
            $employees = $currentSkill -> getEmployees();
            isset($_GET['filter']) ? $employees = eF_filterData($employees,$_GET['filter']) : null;
            foreach ($employees as $employee) {
             if ($employee['skill_ID'] != "") {
              $currentSkill -> removeFromEmployee($employee['login']);
             }
            }
           }
          } catch (Exception $e) {
           handleAjaxExceptions($e);
          }
          exit;
         }

            $smarty -> assign("T_SKILL_NAME", $currentSkill -> skill['description']);

            if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersSkillsTable' || $_GET['ajax'] == 'skillEmployeesTable') {
                $employees = $currentSkill -> getEmployees();
                if (!isset($_GET['show_all'])) {
                    foreach ($employees as $login => $employee) {
                        if ($employee['skill_ID'] != $_GET['edit_skill']) {
                            unset($employees[$login]);
                        }
                    }
                }
          $dataSource = $employees;
    $tableName = $_GET['ajax'];
    include("sorted_table.php");
            }
            $form -> setDefaults(array('skill_description' => $currentSkill -> skill['description'],
                                       'category' => $currentSkill -> skill['categories_ID']));
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
        $form -> setDefaults(array('previous_url' => $previous_url));

        $form -> addElement('submit', 'submit_skill_details', _SUBMIT, 'class = "flatButton" tabindex="3" onClick="if(document.getElementById(\'skill_cat\').value==\'0\'){alert(\''._THEFIELD.' '._SKILLCATEGORY.' '._ISMANDATORY.'\');return false;}" ');
        $smarty -> assign("T_DEFAULT_CATEGORY", $currentSkill -> skill['categories_ID']);

        if ($form -> isSubmitted() && $form -> validate()) {
         $skill_content = array('description' => $form->exportValue('skill_description'),
                                   'categories_ID' => $form->exportValue('category'));
         if (isset($_GET['add_skill'])) {
          EfrontSkill :: createSkill($skill_content);
          $message = _SUCCESSFULLYCREATEDSKILL;
          $message_type = 'success';
         } elseif (isset($_GET['edit_skill'])) {
          $currentSkill -> updateSkillData($skill_content);
          $message = _SKILLDATAUPDATED;
          $message_type = 'success';
         }

         // Return to previous url stored in a hidden - that way, after the insertion we can immediately return to where we were
         //eF_redirect(basename($form->exportValue('previous_url'))."&message=". urlencode($message) . "&message_type=success&tab=skills");
        }

        $renderer = prepareFormRenderer($form);
        $smarty -> assign('T_SKILLS_FORM', $renderer -> toArray());
   } else {

        if (isset($_GET['ajax']) && $_GET['ajax'] == 'skillsTable') {
         $skillset = EfrontSkill :: getAllSkills();
         $dataSource = $skillset;
   $tableName = $_GET['ajax'];
   include("sorted_table.php");
        }

   }

    if (isset($_GET['ajax']) || isset($_GET['postAjaxRequest'])) {
        $smarty -> display($_SESSION['s_type'].'.tpl');
        exit;
    }
} catch (Exception $e) {
    handleNormalFlowExceptions($e);
}
