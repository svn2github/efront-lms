<?php

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if (isset($currentUser->coreAccess['organization']) && $currentUser->coreAccess['organization'] != 'change') {
 $_change_skills_ = false;
} else if ($currentUser -> user['user_type'] == 'administrator') {
 $_change_skills_ = true;
} else if ($currentUser -> user['login'] == $editedUser -> user['login']) {
 $_change_skills_ = false;
} else if (!$currentEmployee -> isSupervisor()) {
 $_change_skills_ = false;
} else if ($currentEmployee -> supervisesEmployee($editedUser->user['login'])) {
 $_change_skills_ = true;
} else {
 $_change_skills_ = false;
}
$smarty -> assign("_change_skills_", $_change_skills_);


if (isset($_GET['ajax']) && isset($_GET['delete_skill']) && $_change_skills_) {
 try {
  $editedEmployee -> removeSkills($_GET['delete_skill']);
 } catch (Exception $e) {
  handleAjaxExceptions($e);
 }
} else {
 if (isset($_GET['ajax']) && $_GET['ajax'] == 'skillsTable') {
  $skills = eF_getTableData("module_hcd_skills LEFT OUTER JOIN module_hcd_skill_categories ON module_hcd_skill_categories.id = module_hcd_skills.categories_ID LEFT OUTER JOIN module_hcd_employee_has_skill ON (module_hcd_employee_has_skill.skill_ID = module_hcd_skills.skill_ID AND module_hcd_employee_has_skill.users_login='".$editedUser->user['login']."') LEFT JOIN users ON module_hcd_employee_has_skill.author_login = users.login", "users_login, module_hcd_skills.description, module_hcd_skill_categories.description as category, specification, score, module_hcd_skills.skill_ID, categories_ID, users.surname, users.name","");

  $dataSource = $skills;
  $tableName = "skillsTable";
  include "sorted_table.php";
 }
}

if (isset($_GET['postAjaxRequest']) && $_change_skills_) {
 try {
  $_GET['specification'] = urldecode($_GET['specification']);
  if (isset($_GET['add_skill'])) {
   if ($_GET['insert'] == "true") {
    $editedEmployee -> addSkills($_GET['add_skill'], $_GET['specification'], $_GET['score']);
   } else if ($_GET['insert'] == "false") {
    $editedEmployee -> removeSkills($_GET['add_skill']);
   } else if (isset($_GET['addAll']) || isset($_GET['removeAll'])) {
    $skills = array_keys($editedEmployee -> getSkills());
    $allSkills = EfrontSkill::getAllSkills();
    isset($_GET['filter']) ? $allSkills = eF_filterData($allSkills, $_GET['filter']) : null;
    foreach ($allSkills as $skill) {
     if (isset($_GET['removeAll'])) {
      if (in_array($skill['skill_ID'], $skills)) {
       $editedEmployee -> removeSkills($skill['skill_ID']);
      }
     } else {
      if (!in_array($skill['skill_ID'], $skills)) {
       $editedEmployee -> addSkills($skill['skill_ID'], "");
      }
     }
    }
   } else if (isset($_GET['from_skillgap_test'])) {
    $skillsToAdd = array();
    foreach ($_GET as $getkey => $getvalue) {
     if (strpos($getkey,"skill") === 0) {
      $skillId = substr($getkey,5);
      if ($_GET['succeed'.$skillId]) {
       $skillsToAdd[$skillId] = _SUCCEEDEDINASKILLGAPTESTLCWITHASCORE . " $getvalue";
      } else {
       $skillsToAdd[$skillId] = _FAILEDINASKILLGAPTESTLCWITHASCORE . " $getvalue";
      }
     }
    }
    foreach ($skillsToAdd as $skillId => $skillDescription) {
     // The last arguement is set to append and not replace existing skill descriptions
     $editedEmployee -> addSkills($skillId, $skillDescription, $skillScore, true);
    }
   }
   exit;
  }

 } catch (Exception $e) {
  handleAjaxExceptions($e);
 }
}
