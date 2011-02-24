<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

try{
 /* Check permissions: only admins have add/edit privileges. supervisors may only see skills */
 if ($currentUser -> getType() != 'administrator') {
  $message = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
  $message_type = 'failure';
  eF_redirect(basename($_SERVER['PHP_SELF'])."&message=".urlencode($message)."&message_type=".$message_type);
  exit;
 }

 /*****************************************************

	 ON DELETING A SKILL CATEGORY

	 **************************************************** */
 if (isset($_GET['del_skill_cat'])) { //The administrator asked to delete a skill
  eF_updateTableData("module_hcd_skills",array("categories_ID" => 0), "categories_ID = '". $_GET['del_skill_cat'] ."'");
  eF_deleteTableData("module_hcd_skill_categories", "id = '".$_GET['del_skill_cat']."'");
  $message = _SKILLCATEGORYDELETED;
  $message_type = 'success';
  eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=module_hcd&op=skills&message=".$message."&message_type=".$message_type);
  exit;
 } else if (isset($_GET['add_skill_cat']) || (isset($_GET['edit_skill_cat']) && eF_checkParameter($_GET['edit_skill_cat'], 'id'))) {
  if (isset($_GET['add_skill_cat'])) {
   $form = new HTML_QuickForm("skill_cat_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module_hcd&op=skill_cat&add_skill_cat=1", "",null, true);
  } else {
   $form = new HTML_QuickForm("skill_cat_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=module_hcd&op=skill_cat&edit_skill_cat=" . $_GET['edit_skill_cat'] , "", null, true);
   $skill_cat = eF_getTableData("module_hcd_skill_categories","description", "id ='".$_GET['edit_skill_cat']."'");
  }

  $form -> addElement('text', 'skill_cat_description', _SKILLCATEGORY, 'id="skill_cat_description" class = "inputText" tabindex="1"');
  $form -> addElement('submit', 'submit_skill_details', _SUBMIT, 'class = "flatButton" tabindex="2"');
  $form -> addRule('skill_cat_description', _THEFIELD.' '._SKILLCATEGORY.' '._ISMANDATORY, 'required', null, 'client');

  if (isset($_GET['edit_skill_cat'])) {
   $form -> setDefaults(array( 'skill_cat_description' => $skill_cat[0]['description']));
  }

  try {
   if ($form -> isSubmitted() && $form -> validate()) {
    $skill_cat_content = array('description' => $form->exportValue('skill_cat_description'));
    if (isset($_GET['add_skill_cat'])) {
     eF_insertTableData("module_hcd_skill_categories", $skill_cat_content);
    } elseif (isset($_GET['edit_skill_cat'])) {
     eF_updateTableData("module_hcd_skill_categories", $skill_cat_content , "id = '".$_GET['edit_skill_cat']."'");
    }
    $message = _OPERATIONCOMPLETEDSUCCESSFULLY;
    $message_type = 'success';
   }
  } catch (EfrontSkillException $e) {
   handleNormalFlowExceptions($e);
  }

  $smarty -> assign('T_SKILL_CAT_FORM', $form -> toArray());
 }
} catch (EfrontSkillException $e) {
 handleNormalFlowExceptions($e);
}
