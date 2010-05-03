<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

try{
 /* Check permissions: only admins have add/edit privileges. supervisors may only see skills */
 if($currentUser -> getType() != 'administrator') {
  $message = _SORRYYOUDONOTHAVEPERMISSIONTOPERFORMTHISACTION;
  $message_type = 'failure';
  eF_redirect("".$_SESSION['s_type'].".php?ctg=personal&tab=skills&message=".$message."&message_type=".$message_type);
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
  eF_redirect("".$_SESSION['s_type'].".php?ctg=module_hcd&op=skills&message=".$message."&message_type=".$message_type);
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
  $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
  $form -> addElement('text', 'skill_cat_description', _SKILLCATEGORY, 'id="skill_cat_description" class = "inputText" tabindex="1"');
  $form -> addRule('skill_cat_description', _THEFIELD.' '._SKILLCATEGORY.' '._ISMANDATORY, 'required', null, 'client');
  // Hidden for maintaining the previous_url value
  $form -> addElement('hidden', 'previous_url', null, 'id="previous_url"');
  $previous_url = getenv('HTTP_REFERER');
  if ($position = strpos($previous_url, "&message")) {
   $previous_url = substr($previous_url, 0, $position);
  }
  $form -> setDefaults(array( 'previous_url' => $previous_url));

  $form -> addElement('submit', 'submit_skill_details', _SUBMIT, 'class = "flatButton" tabindex="2"');

  if (isset($_GET['edit_skill_cat'])) {
   $form -> setDefaults(array( 'skill_cat_description' => $skill_cat[0]['description']));
  }

  $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
  $renderer -> setRequiredTemplate(
            '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');

  /*****************************************************

		 SKILL DATA SUBMISSION

		 **************************************************** */
  if ($form -> isSubmitted()) {
   if ($form -> validate()) {
    $skill_cat_content = array('description' => $form->exportValue('skill_cat_description'));
    if (isset($_GET['add_skill_cat'])) {
     eF_insertTableData("module_hcd_skill_categories", $skill_cat_content);
     $message = _SUCCESSFULLYCREATEDSKILLCATEGORY;
     $message_type = 'success';
    } elseif (isset($_GET['edit_skill_cat'])) {
     eF_updateTableData("module_hcd_skill_categories", $skill_cat_content , "id = '".$_GET['edit_skill_cat']."'");
     $message = _SKILLCATEGORYDATAUPDATED;
     $message_type = 'success';
    }

    // Return to previous url stored in a hidden - that way, after the insertion we can immediately return to where we were
    echo "<script>!/\?/.test(parent.location) ? parent.location = '". basename($form->exportValue('previous_url')) ."&message=".urlencode($message)."&message_type=".$message_type."' : parent.location = '".basename($form->exportValue('previous_url')) ."&message=".urlencode($message)."&message_type=".$message_type."';</script>";
    //eF_redirect("".$form->exportValue('previous_url')."&message=". $message . "&message_type=" . $message_type . "&tab=skills");
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

?>
