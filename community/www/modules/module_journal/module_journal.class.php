<?php

class module_journal extends EfrontModule{

 public function getName(){
  return _JOURNAL_NAME;
 }

 public function getPermittedRoles(){
  return array("student", "professor", "administrator");
 }

 public function getModule(){
  return true;
 }

 public function getSmartyTpl(){

  $currentUser = $this->getCurrentUser();
  $rules = $this->getRules();
  $smarty = $this->getSmartyVar();

  if($currentUser->getRole($this->getCurrentLesson()) == 'professor' || $currentUser->getRole($this->getCurrentLesson()) == 'student'){

   $currentLesson = $this->getCurrentLesson();
   $currentLessonID = $currentLesson->lesson['id'];

   if(!isset($_SESSION['module_journal_dimension']) ||
    (count($_GET) == 2 && $_GET['ctg'] == 'module' && $_GET['op'] == 'module_journal') ||
    (count($_GET) == 3 && $_GET['ctg'] == 'module' && $_GET['op'] == 'module_journal' &&
    $_GET['new_lesson_id'] == $currentLessonID)){

    $_SESSION['module_journal_dimension'] = 'small';
   }

   if(!isset($_SESSION['module_journal_entries_from']) ||
    (count($_GET) == 2 && $_GET['ctg'] == 'module' && $_GET['op'] == 'module_journal') ||
    (count($_GET) == 3 && $_GET['ctg'] == 'module' && $_GET['op'] == 'module_journal' &&
    $_GET['new_lesson_id'] == $currentLessonID)){

    $_SESSION['module_journal_entries_from'] = '-1';
   }

   if(isset($_SESSION['module_journal_scroll_position']))
    $smarty->assign("T_JOURNAL_SCROLL_POSITION", $_SESSION['module_journal_scroll_position']);

   $smarty->assign("T_JOURNAL_DIMENSIONS", $_SESSION['module_journal_dimension']);
   $smarty->assign("T_JOURNAL_ENTRIES_FROM", $_SESSION['module_journal_entries_from']);
   $entries = $this->getEntries($currentUser->user['login'], $_SESSION['module_journal_entries_from']);

   global $popup;
   (isset($popup) && $popup == 1) ? $popup_ = '&popup=1' : $popup_ = '';
  }

  $smarty->assign("T_JOURNAL_BASEURL", $this->moduleBaseUrl);
  $smarty->assign("T_JOURNAL_BASELINK", $this->moduleBaseLink);

  if(isset($_GET['edit_allow_export']) && $_GET['edit_allow_export'] == '1' && isset($_GET['allow'])){

   try{
    $object = eF_getTableData("module_journal_settings", "id", "name='export'");
    eF_updateTableData("module_journal_settings", array("value" => $_GET['allow']), "id=".$object[0]['id']);
   }
   catch(Exception $e){
    handleAjaxExceptions($e);
   }

   exit;
  }

  if(isset($_GET['edit_professor_preview']) && $_GET['edit_professor_preview'] == '1' && isset($_GET['preview'])){

   try{
    $object = eF_getTableData("module_journal_settings", "id", "name='preview'");
    eF_updateTableData("module_journal_settings", array("value" => $_GET['preview']), "id=".$object[0]['id']);
   }
   catch(Exception $e){
    handleAjaxExceptions($e);
   }

   exit;
  }

  if(isset($_GET['dimension']) && eF_checkParameter($_GET['dimension'], 'string')){

   $smarty->assign("T_JOURNAL_DIMENSIONS", $_GET['dimension']);
   $_SESSION['module_journal_dimension'] = $_GET['dimension'];
  }

  if(isset($_GET['entries_from'])){

   $smarty->assign("T_JOURNAL_ENTRIES_FROM", $_GET['entries_from']);
   $_SESSION['module_journal_entries_from'] = $_GET['entries_from'];
  }

  if(isset($_GET['delete_rule']) && eF_checkParameter($_GET['delete_rule'], 'id') && in_array($_GET['delete_rule'], array_keys($rules))){

   try{
    eF_deleteTableData("module_journal_rules", "id=".$_GET['delete_rule']);
   }
   catch(Exception $e){
    handleAjaxExceptions($e);
   }

   exit;
  }

  if(isset($_GET['deactivate_rule']) && eF_checkParameter($_GET['deactivate_rule'], 'id') &&
        in_array($_GET['deactivate_rule'], array_keys($rules))){

   eF_updateTableData("module_journal_rules", array('active' => 0), "id=".$_GET['deactivate_rule']);
  }

  if(isset($_GET['activate_rule']) && eF_checkParameter($_GET['activate_rule'], 'id') &&
        in_array($_GET['activate_rule'], array_keys($rules))){

   eF_updateTableData("module_journal_rules", array('active' => 1), "id=".$_GET['activate_rule']);
  }

  if(isset($_GET['delete_entry']) && eF_checkParameter($_GET['delete_entry'], 'id') &&
       in_array($_GET['delete_entry'], array_keys($entries))){

   $object = eF_getTableData("module_journal_entries", "users_LOGIN", "id=".$_GET['delete_entry']);

   if($object[0]['users_LOGIN'] != $_SESSION['s_login']){

    eF_redirect($this->moduleBaseUrl."&message=".urlencode(_JOURNAL_NOACCESS).$popup_);
    exit;
   }

   eF_deleteTableData("module_journal_entries", "id=".$_GET['delete_entry']);
  }

  if(isset($_GET['saveas']) && $_GET['saveas'] == 'pdf'){

   $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
   $pdf->SetCreator(PDF_CREATOR);
   $pdf->SetAuthor(PDF_AUTHOR);
   $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
   $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
   $pdf->setFontSubsetting(false);
   $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
   $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
   $pdf->setHeaderFont(Array('Freeserif', 'I', 11));
   $pdf->setFooterFont(Array('Freeserif', '', 8));
   $pdf->setHeaderData('', '', '', _JOURNAL_NAME);
   $pdf->AliasNbPages();
   $pdf->AddPage();
   $pdf->SetFont('Freeserif', '', 10);
   $pdf->SetTextColor(0, 0, 0);

   foreach($entries as $entry){

    $pdf->Cell(0, 0, $entry['entry_date_formatted'], 0, 1, L, 0);
    $pdf->writeHTML('<br/>', true, false, true, false, '');
    $pdf->writeHTML($entry['entry_body'], true, false, true, false, '');
    $pdf->writeHTML('<div style="height: 5px;"></div>', true, false, true, false, '');
    $pdf->writeHTML('<hr>', true, false, true, false, '');
   }

   $fileNamePdf = "journal.pdf";
   header("Content-type: application/pdf");
   header("Content-disposition: attachment; filename=".$fileNamePdf);
   echo $pdf->Output('', 'S');
   exit(0);
  }

  if(isset($_GET['saveas']) && $_GET['saveas'] == 'doc'){

   include(dirname(__FILE__)."/classes/html_to_doc.inc.php");

   $entriesHTML = '';

   foreach($entries as $entry){

    $entriesHTML .= $entry['entry_date_formatted'];
    $entriesHTML .= $entry['entry_body'];
    $entriesHTML .= '<hr><br/>';
   }

   $htmltodoc = new HTML_TO_DOC();
   $htmltodoc->createDoc($entriesHTML, "journal", true);

   exit(0);
  }

  if(isset($_GET['saveas']) && $_GET['saveas'] == 'txt'){

   include(dirname(__FILE__)."/classes/html2text.inc");

   header('Content-Type: text/plain');
   header('Content-Disposition: attachment; filename="journal.txt"');

   $entriesHTML = '';

   foreach($entries as $entry){

    $entriesHTML .= $entry['entry_date_formatted'];
    $entriesHTML .= $entry['entry_body'];
    $entriesHTML .= '<p></p>';
    $entriesHTML .= '_______________________________________________________';
    $entriesHTML .= '<p></p>';
   }

   $htmlToText = new Html2Text($entriesHTML, 100);
   $entriesHTMLtext = $htmlToText->convert();
   echo $entriesHTMLtext;

   exit(0);
  }

  if(isset($_GET['check_students_journals']) && $_GET['check_students_journals'] == '1'){

   $professorJournalLessons = $this->getProfessorJournalLessons($currentUser);
   $journalLessonsStudents = $this->getJournalLessonsStudents($professorJournalLessons);

   $smarty->assign("T_JOURNAL_STUDENTS", $journalLessonsStudents);
  }

  if(isset($_GET['preview_journal']) && $_GET['preview_journal'] == '1' &&
   isset($_GET['student']) && eF_checkParameter($_GET['student'], 'login')){

   $userLogin = $_GET['student'];

   $professorJournalLessons = $this->getProfessorJournalLessons($currentUser);
   $studentEntries = $this->getStudentEntries($userLogin, $professorJournalLessons);

   $smarty->assign("T_JOURNAL_STUDENT_ENTRIES", $studentEntries);
  }

  if(isset($_REQUEST['autosave']) && $_REQUEST['autosave'] == "1" && isset($_REQUEST['entry_body']) && isset($_REQUEST['edit_entry'])){

   if($_REQUEST['edit_entry'] != "-1"){

    $object = eF_getTableData("module_journal_entries", "lessons_ID", "id=".$_GET['edit_entry']);

    $fields = array(
     "entry_body" => $_REQUEST['entry_body'],
     "entry_date" => date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'),
     "lessons_ID" => $object[0]['lessons_ID'],
     "users_LOGIN" => $currentUser->user['login'],
    );

    eF_updateTableData("module_journal_entries", $fields, "id=".$_REQUEST['edit_entry']);
   }
   else{
    $fields = array(
     "entry_body" => $_REQUEST['entry_body'],
     "entry_date" => date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s'),
     "lessons_ID" => $currentLessonID,
     "users_LOGIN" => $currentUser->user['login'],
    );

    $id = eF_insertTableData("module_journal_entries", $fields);

    if($id){

     if(isset($_SESSION['module_journal_autosave_entry'])){

      $this->deleteAutoSaveEntry();
      $_SESSION['module_journal_autosave_entry'] = $id;
     }
     else{
      $_SESSION['module_journal_autosave_entry'] = $id;
     }
    }
   }

   exit(0);
  }

  if(isset($_REQUEST['show_right']) && $_REQUEST['show_right'] == "1" && isset($_REQUEST['entry_body']) && $_REQUEST['entry_body'] != ""
       && isset($_REQUEST['edit']) && isset($_REQUEST['edit_entry'])){

   if(isset($_SESSION['module_journal_show_right_entry']))
    unset($_SESSION['module_journal_show_right_entry']);

   $_SESSION['module_journal_show_right_entry'] = $_REQUEST['entry_body'];
  }

  if(isset($_REQUEST['hide_right']) && $_REQUEST['hide_right'] == "1" && isset($_REQUEST['entry_body']) && $_REQUEST['entry_body'] != ""
       && isset($_REQUEST['edit']) && isset($_REQUEST['edit_entry'])){

   if(isset($_SESSION['module_journal_hide_right_entry']))
    unset($_SESSION['module_journal_hide_right_entry']);

   $_SESSION['module_journal_hide_right_entry'] = $_REQUEST['entry_body'];
  }

  if(isset($_REQUEST['hide_left']) && $_REQUEST['hide_left'] == "1" && isset($_REQUEST['entry_body']) && $_REQUEST['entry_body'] != ""
       && isset($_REQUEST['edit']) && isset($_REQUEST['edit_entry'])){

   if(isset($_SESSION['module_journal_hide_left_entry']))
    unset($_SESSION['module_journal_hide_left_entry']);

   $_SESSION['module_journal_hide_left_entry'] = $_REQUEST['entry_body'];
  }

  if(isset($_REQUEST['scroll_position']) && eF_checkParameter($_REQUEST['scroll_position'], 'id')){

   $_SESSION['module_journal_scroll_position'] = $_REQUEST['scroll_position'];
  }

  if(isset($_GET['add_rule']) ||
   (isset($_GET['edit_rule']) && eF_checkParameter($_GET['edit_rule'], 'id') && in_array($_GET['edit_rule'], array_keys($rules)))){

   if($_SESSION['s_type'] != "administrator")
    eF_redirect($this->moduleBaseUrl."&message=".urlencode(_JOURNAL_NOACCESS));

   isset($_GET['add_rule']) ? $postTarget = "&add_rule=1" : $postTarget = "&edit_rule=".$_GET['edit_rule'];

   global $load_editor;
   $load_editor = true;

   $form = new HTML_QuickForm("add_edit_rule_form", "post", $this->moduleBaseUrl.$postTarget, "", null, true);
   $form->addElement('text', 'title', _TITLE, 'class="inputText" style="width:498px;"');
   $form->addRule('title', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
   $form->addElement('textarea', 'description', _DESCRIPTION,
         'class="inputContentTextarea simpleEditor" style="width:500px;height:20em;"');
   $form->addElement('submit', 'submit', _SUBMIT, 'class="flatButton"');

   if(isset($_GET['edit_rule'])){
    $editRule = $rules[$_GET['edit_rule']];
    $form->setDefaults($editRule);
   }

   if($form->isSubmitted() && $form->validate()){

    $values = $form->exportValues();
    $fields = array(
      "title" => $values['title'],
      "description" => $values['description']
     );

    if($values['description'] == ''){

     $message = _JOURNAL_EMPTY_RULE_DESCRIPTION;

     if(isset($_GET['add_rule']))
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure&add_rule=1");
     else
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure&edit_rule=".$_GET['edit_rule']);
    }

    if(isset($_GET['add_rule'])){

     if(eF_insertTableData("module_journal_rules", $fields)){

      $message = _JOURNAL_RULE_SUCCESSFULLY_ADDED;
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=success");
     }
     else{
      $message = _JOURNAL_RULE_ADD_PROBLEM;
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure");
     }
    }
    else{
     if(eF_updateTableData("module_journal_rules", $fields, "id=".$_GET['edit_rule'])){

      $message = _JOURNAL_RULE_SUCCESSFULLY_EDITED;
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=success");
     }
     else{
      $message = _JOURNAL_RULE_EDIT_PROBLEM;
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure");
     }
    }
   }

   $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
   $form->setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
   $form->setRequiredNote(_REQUIREDNOTE);
   $form->accept($renderer);
   $smarty->assign('T_JOURNAL_ADD_EDIT_RULE_FORM', $renderer->toArray());
  }
  else{
   $rules = $this->getRules();
   $smarty->assign("T_JOURNAL_RULES", $rules);

   $object = eF_getTableData("module_journal_settings", "value", "name='export'");
   $smarty->assign("T_JOURNAL_ALLOW_EXPORT", $object[0]['value']);

   $object = eF_getTableData("module_journal_settings", "value", "name='preview'");
   $smarty->assign("T_JOURNAL_ALLOW_PROFESSOR_PREVIEW", $object[0]['value']);

   if($currentUser->getRole($this->getCurrentLesson()) == 'professor' || $currentUser->getRole($this->getCurrentLesson()) == 'student'){

    $activeRules = $this->getRules(true);
    $smarty->assign("T_JOURNAL_ACTIVE_RULES", $activeRules);

    $entries = $this->getEntries($currentUser->user['login'], $_SESSION['module_journal_entries_from']);
    $smarty->assign("T_JOURNAL_ENTRIES", $entries);

    $journalLessons = $this->getJournalLessons($currentUser->user['login']);
    $smarty->assign("T_JOURNAL_LESSONS", $journalLessons);

       /*					*/
    global $load_editor;
    $load_editor = true;

    if(isset($_GET['edit_entry']) && $_GET['edit_entry'] != '-1')
     $postTarget = "&edit_entry=".$_GET['edit_entry'];
    else
     $postTarget = "&add_entry=1";

    if(isset($_GET['hide_right']) && $_GET['hide_right'] == '1'){

     $editorStyle = array(
        'small' => 'width:588px; height:320px;',
        'medium' => 'width:673px; height:375px;',
        'large' => 'width:759px; height:430px;'
       );
    }
    else{
     $editorStyle = array(
        'small' => 'width:300px; height:320px;',
        'medium' => 'width:344px; height:375px;',
        'large' => 'width:388px; height:430px;'
       );
    }

    $form = new HTML_QuickForm("add_edit_entry_form", "post", $this->moduleBaseUrl.$postTarget, "", null, true);
    $form->addElement('textarea', 'entry_body', _DESCRIPTION,
      'class="inputContentTextarea simpleEditor" style="'.$editorStyle[$_SESSION['module_journal_dimension']].'"');

    if(isset($_GET['edit_entry']) && $_GET['edit_entry'] != '-1')
     $form->addElement('submit', 'submit', _UPDATE.' '._JOURNAL_ENTRY, 'class="flatButton"');
    else
     $form->addElement('submit', 'submit', _SAVE.' '._JOURNAL_ENTRY, 'class="flatButton"');

    if(isset($_GET['edit_entry']) && $_GET['edit_entry'] != '-1'){

     $editEntry = $entries[$_GET['edit_entry']];
     $form->setDefaults($editEntry);

     if(!in_array($_GET['edit_entry'], array_keys($entries)))
      eF_redirect($this->moduleBaseUrl."&message=".urlencode(_JOURNAL_NOACCESS).$popup_);

     $object = eF_getTableData("module_journal_entries", "lessons_ID, users_LOGIN, entry_date","id=".$_GET['edit_entry']);

     if($object[0]['users_LOGIN'] != $_SESSION['s_login'])
      eF_redirect($this->moduleBaseUrl."&message=".urlencode(_JOURNAL_NOACCESS).$popup_);
    }

    if(isset($_GET['show_left']) && $_GET['show_left'] == '1' && isset($_GET['edit']) && isset($_GET['edit_entry'])){

     if(isset($_SESSION['module_journal_hide_left_entry'])){

      $form->setDefaults(array("entry_body" => $_SESSION['module_journal_hide_left_entry']));
      unset($_SESSION['module_journal_hide_left_entry']);
     }
    }

    if(isset($_GET['show_right']) && $_GET['show_right'] == '1' && isset($_GET['edit']) && isset($_GET['edit_entry'])){

     if(isset($_SESSION['module_journal_show_right_entry'])){

      $form->setDefaults(array("entry_body" => $_SESSION['module_journal_show_right_entry']));
      unset($_SESSION['module_journal_show_right_entry']);
     }
    }

    if(isset($_GET['hide_right']) && $_GET['hide_right'] == '1' && isset($_GET['edit']) && isset($_GET['edit_entry'])){

     if(isset($_SESSION['module_journal_hide_right_entry'])){

      $form->setDefaults(array("entry_body" => $_SESSION['module_journal_hide_right_entry']));
      unset($_SESSION['module_journal_hide_right_entry']);
     }
    }

    if($form->isSubmitted() && $form->validate()){

     $values = $form->exportValues();
     isset($_GET['add_entry']) ? $lessonID = $currentLessonID : $lessonID = $object[0]['lessons_ID'];

     if(isset($_GET['add_entry']))
      $date = date('Y').'-'.date('m').'-'.date('d').' '.date('H').':'.date('i').':'.date('s');
     else
      $date = $object[0]['entry_date'];

     $fields = array(
       "entry_body" => $values['entry_body'],
       "entry_date" => $date,
       "lessons_ID" => $lessonID,
       "users_LOGIN" => $currentUser->user['login'],
      );

     if($values['entry_body'] == ''){
      $message = _JOURNAL_EMPTY_ENTRY_BODY;
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure".$popup_);
     }

     if(isset($_GET['add_entry'])){

      if(eF_insertTableData("module_journal_entries", $fields)){

       if(isset($_SESSION['module_journal_autosave_entry']))
        $this->deleteAutoSaveEntry();

       $message = _JOURNAL_ENTRY_SUCCESSFULLY_ADDED;
       eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=success".$popup_);
      }
      else{
       $message = _JOURNAL_ENTRY_ADD_PROBLEM;
       eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure".$popup_);
      }
     }
     else{
      if(eF_updateTableData("module_journal_entries", $fields, "id=".$_GET['edit_entry'])){

       $message = _JOURNAL_ENTRY_SUCCESSFULLY_EDITED;
       eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=success".$popup_);
      }
      else{
       $message = _JOURNAL_ENTRY_EDIT_PROBLEM;
       eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure".$popup_);
      }
     }
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form->setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
    $form->setRequiredNote(_REQUIREDNOTE);
    $form->accept($renderer);
    $smarty->assign('T_JOURNAL_ADD_ENTRY_FORM', $renderer->toArray());

    if($currentUser->getRole($this->getCurrentLesson()) == 'professor'){

     $popupInfo[] = array(
       'text' => _JOURNAL_POPUP_INFO,
       'image' => $this->moduleBaseLink.'images/info.png',
       'href' => $this->moduleBaseUrl.'&popup_info=1&popup=1',
       'onClick' => "eF_js_showDivPopup('"._JOURNAL_POPUP_INFO."', 2)",
       'target' => 'POPUP_FRAME',
       'id' => 'popup_info'
      );

     $smarty->assign("T_JOURNAL_POPUP_INFO", $popupInfo);
    }
   }
  }

  if($currentUser->getType() == 'administrator')
   return $this->moduleBaseDir."module_journal_admin.tpl";

  else if($currentUser->getRole($this->getCurrentLesson()) == 'professor' || $currentUser->getRole($this->getCurrentLesson()) == 'student'){

   if(isset($_GET['hide_left']) && $_GET['hide_left'] == '1')
    return $this->moduleBaseDir."module_journal_user_right.tpl";

   if(isset($_GET['hide_right']) && $_GET['hide_right'] == '1')
    return $this->moduleBaseDir."module_journal_user_left.tpl";

   return $this->moduleBaseDir."module_journal_user.tpl";
  }
 }

 public function isLessonModule(){
  return true;
 }

 public function onInstall(){

  eF_executeNew("DROP TABLE IF EXISTS `module_journal_rules`");
  $t1 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_journal_rules` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `title` varchar(255) NOT NULL,
     `description` text NOT NULL,
     `active` tinyint(1) NOT NULL DEFAULT '1',
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  eF_executeNew("DROP TABLE IF EXISTS `module_journal_entries`");
  $t2 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_journal_entries` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `entry_body` text NOT NULL,
     `entry_date` datetime NOT NULL,
     `lessons_ID` int(11) NOT NULL,
     `users_LOGIN` varchar(255) NOT NULL,
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  eF_executeNew("DROP TABLE IF EXISTS `module_journal_settings`");
  $t3 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_journal_settings` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `name` varchar(45) NOT NULL,
     `value` tinyint(1) NOT NULL,
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  eF_insertTableData("module_journal_settings", array('name' => 'export', 'value' => 1));
  eF_insertTableData("module_journal_settings", array('name' => 'preview', 'value' => 1));

  return($t1 && $t2 && $t3);
 }

 public function onUninstall(){

  $t1 = eF_executeNew("DROP TABLE IF EXISTS `module_journal_rules`");
  $t2 = eF_executeNew("DROP TABLE IF EXISTS `module_journal_entries`");
  $t3 = eF_executeNew("DROP TABLE IF EXISTS `module_journal_settings`");

  return($t1 && $t2 && $t3);
 }

 public function onUpgrade(){

  $t1 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_journal_settings` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `name` varchar(45) NOT NULL,
     `value` tinyint(1) NOT NULL,
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  $resultNew = eF_getTableData("module_journal_settings", "*");

  if(count($resultNew) == 0){

   $result = eF_getTableData("module_journal_allow_export", "*");

   eF_insertTableData("module_journal_settings", array('name' => 'export', 'value' => $result[0]['allow']));
   eF_insertTableData("module_journal_settings", array('name' => 'preview', 'value' => 1));
  }

  $t2 = eF_executeNew("DROP TABLE IF EXISTS `module_journal_allow_export`");

  return($t1 && $t2);
 }

 public function getCenterLinkInfo(){

  return array(
   'title' => _JOURNAL_NAME,
   'image' => $this->moduleBaseDir.'images/journal_logo.png',
   'link' => $this->moduleBaseUrl
  );
 }

 public function getLessonCenterLinkInfo(){

  return array(
   'title' => _JOURNAL_NAME,
   'image' => $this->moduleBaseDir.'images/journal_logo.png',
   'link' => $this->moduleBaseUrl
  );
 }

 public function getSidebarLinkInfo(){

  $currentLessonMenu = array(array(
      'id' => 'journal_link_1',
      'title' => _JOURNAL_NAME,
      'image' => $this->moduleBaseDir.'images/journal_logo16',
      'eFrontExtensions' => '1',
      'link' => $this->moduleBaseUrl)
     );

  return array("current_lesson" => $currentLessonMenu);
 }

 public function getLinkToHighlight(){

  return 'journal_link_1';
 }

 public function getNavigationLinks(){

  $currentUser = $this->getCurrentUser();

  if($currentUser->getType() == 'administrator'){

   if(isset($_GET['add_rule'])){

    return array(
     array('title' => _HOME, 'link' => $currentUser->getType().".php?ctg=control_panel"),
     array('title' => _JOURNAL_NAME, 'link' => $this->moduleBaseUrl),
     array('title' => _JOURNAL_ADD_RULE2, 'link' => $_SERVER['REQUEST_URI'])
    );
   }
   else if(isset($_GET['edit_rule'])){

    return array(
     array('title' => _HOME, 'link' => $currentUser->getType().".php?ctg=control_panel"),
     array('title' => _JOURNAL_NAME, 'link' => $this->moduleBaseUrl),
     array('title' => _JOURNAL_EDIT_RULE, 'link' => $_SERVER['REQUEST_URI'])
    );
   }
   else{
    return array(
     array('title' => _HOME, 'link' => $currentUser->getType().".php?ctg=control_panel"),
     array('title' => _JOURNAL_NAME, 'link' => $this->moduleBaseUrl)
    );
   }
  }
  else{
   $currentLesson = $this->getCurrentLesson();
   $currentUserRole = $currentUser->getRole($currentLesson);
   $onClick = "location='".$currentUserRole.".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();";

   if(isset($_GET['check_students_journals'])){

    return array(
     array('title' => _MYCOURSES, 'onclick' => $onClick),
     array('title' => $currentLesson->lesson['name'], 'link' => $currentUser->getType().".php?ctg=control_panel"),
     array('title' => _JOURNAL_NAME, 'link' => $this->moduleBaseUrl),
     array('title' => _JOURNAL_STUDENTS_JOURNAL, 'link' => $_SERVER['REQUEST_URI'])
    );
   }
   else{

    return array(
     array('title' => _MYCOURSES, 'onclick' => $onClick),
     array('title' => $currentLesson->lesson['name'], 'link' => $currentUser->getType().".php?ctg=control_panel"),
     array('title' => _JOURNAL_NAME, 'link' => $this->moduleBaseUrl)
    );
   }
  }
 }

 public function getModuleCSS(){

  if(isset($_GET['hide_left']) && $_GET['hide_left'] == '1')
   return $this->moduleBaseDir.'css/journal_right.css';

  if(isset($_GET['hide_right']) && $_GET['hide_right'] == '1')
   return $this->moduleBaseDir.'css/journal_left.css';

  return $this->moduleBaseDir.'css/journal.css';
 }

 // Inner Functions

 private function getRules($active=null){

  ($active == null) ? $where = "" : $where = "active=1";

  $result = eF_getTableData("module_journal_rules", "*", $where, "id");
  $rules = array();

  foreach($result as $value)
   $rules[$value['id']] = $value;

  return $rules;
 }

 private function getEntries($userLogin, $filter){

  if($filter == '-1')
   $where = "users_LOGIN='".$userLogin."'";
  else
   $where = "users_LOGIN='".$userLogin."' and lessons_ID=".$filter;

  $result = eF_getTableData("module_journal_entries", "*", $where, "id");
  $entries = array();
  $prev_datestamp = '';

  foreach($result as $value){

   $date_ = explode(' ', $value['entry_date']);
   $datestamp = $date_[0];
   $timestamp = $date_[1];
   $datestamp = explode('-', $datestamp);
   $timestampMktime = explode(':', $timestamp);
   $dateFormatted = formatTimestamp(mktime($timestampMktime[0], $timestampMktime[1], $timestampMktime[2],
            $datestamp[1], $datestamp[2], $datestamp[0]), 'time');
   $datestampFormatted = formatTimestamp(mktime(0, 0, 0, $datestamp[1], $datestamp[2], $datestamp[0]), 'date');
   $datestamp = $datestamp[2].'/'.$datestamp[1].'/'.$datestamp[0];

   if($prev_datestamp == '' || $prev_datestamp != $datestamp)
    $value['date_first'] = 1;
   else
    $value['date_first'] = 0;

   $value['entry_date'] = $datestamp.' '.$timestamp;
   $value['entry_date_formatted'] = $dateFormatted;
   $value['entry_datestamp'] = $datestamp;
   $value['entry_datestamp_formatted'] = $datestampFormatted;
   $value['entry_timestamp'] = $timestamp;
   $entries[$value['id']] = $value;

   $prev_datestamp = $datestamp;
  }

  return $entries;
 }

 function deleteAutoSaveEntry(){

  $autosaveEntry = $_SESSION['module_journal_autosave_entry'];
  eF_deleteTableData("module_journal_entries", "id=".$autosaveEntry);
  unset($_SESSION['module_journal_autosave_entry']);
 }

 function getJournalLessons($userLogin){

  $lessons = array();
  $ids = array();

  $result = eF_getTableData("module_journal_entries", "lessons_ID", "users_LOGIN='".$userLogin."'");

  foreach($result as $value)
   array_push($ids, $value['lessons_ID']);

  $ids = array_unique($ids, SORT_NUMERIC);
  $lessons[-1] = array("id" => -1, "name" => _JOURNAL_ALL_LESSONS);

  foreach($ids as $key => $value){

   $lesson = new EfrontLesson($value);
   $lessons[$value] = array("id" => $value, "name" => $lesson->lesson['name']);
  }

  return $lessons;
 }

 function getProfessorJournalLessons($currentUser){

  $userLessons = $currentUser->getLessons(false, 'professor');
  $lessons = array();

  foreach($userLessons as $key => $value){

   $lesson = new EfrontLesson($key);
   $installed = $lesson->getOptions(array('module_journal'));

   if(count($installed) != 0 && $installed['module_journal'] == 1)
    array_push($lessons, $key);
  }

  return $lessons;
 }

 function getJournalLessonsStudents($professorJournalLessons){

  $students = array();

  foreach($professorJournalLessons as $lessonID){

   $lesson = new EfrontLesson($lessonID);
   $lessonStudents = $lesson->getUsers('student');

   foreach($lessonStudents as $userLogin => $value){

    if(!in_array($userLogin, array_keys($students)))
     $students[$userLogin] = array('login' => $userLogin);
   }
  }

  return $students;
 }

 function getStudentEntries($userLogin, $professorJournalLessons){

  $where = "users_LOGIN='".$userLogin."' and (";

  for($count = 0; $count < count($professorJournalLessons); $count++){

   if($count != count($professorJournalLessons) -1)
    $where .= "lessons_ID=".$professorJournalLessons[$count]." OR ";
   else
    $where .= "lessons_ID=".$professorJournalLessons[$count];
  }

  $where .= ")";

  $result = eF_getTableData("module_journal_entries", "*", $where, "id");
  $entries = array();

  foreach($result as $value){

   $date_ = explode(' ', $value['entry_date']);
   $datestamp = $date_[0];
   $timestamp = $date_[1];
   $datestamp = explode('-', $datestamp);
   $timestampMktime = explode(':', $timestamp);
   $dateFormatted = formatTimestamp(mktime($timestampMktime[0], $timestampMktime[1], $timestampMktime[2],
            $datestamp[1], $datestamp[2], $datestamp[0]), 'time');
   $value['entry_date_formatted'] = $dateFormatted;

   $lesson = new EfrontLesson($value['lessons_ID']);
   $value['lesson'] = $lesson->lesson['name'];

   $entries[$value['id']] = $value;
  }

  return $entries;
 }
}

?>
