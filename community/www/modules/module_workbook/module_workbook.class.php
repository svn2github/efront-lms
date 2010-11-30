<?php

class module_workbook extends EfrontModule{

 public function getName(){
  return _WORKBOOK_NAME;
 }

 public function getPermittedRoles(){
  return array("student", "professor");
 }

 public function getModule(){

  $smarty = $this->getSmartyVar();
  $currentUser = $this->getCurrentUser();
  $currentLesson = $this->getCurrentLesson();
  $currentLessonID = $currentLesson->lesson['id'];
  $currentLessonName = $currentLesson->lesson['name'];

  if($currentUser->getRole($this->getCurrentLesson()) == 'professor'){

   $result = eF_getTableData("module_workbook_settings", "id", "lessons_ID=".$currentLessonID);

   if(sizeof($result) == 0){

    eF_insertTableData("module_workbook_settings", array('lessons_ID' => $currentLessonID, 'lesson_name' => $currentLessonName));
    $workbookLessonName = _WORKBOOK_NAME.' ['.$this->getWorkbookLessonName($currentLessonID).']';
    $smarty->assign("T_WORKBOOK_LESSON_NAME", $workbookLessonName);
   }
  }

  return true;
 }

 public function getSmartyTpl(){

  $smarty = $this->getSmartyVar();
  $currentUser = $this->getCurrentUser();
  $currentLesson = $this->getCurrentLesson();
  $currentLessonID = $currentLesson->lesson['id'];

  if($currentUser->getRole($this->getCurrentLesson()) == 'professor' || $currentUser->getRole($this->getCurrentLesson()) == 'student'){ // XXX

   $workbookLessonName = _WORKBOOK_NAME.' ['.$this->getWorkbookLessonName($currentLessonID).']';
   $smarty->assign("T_WORKBOOK_LESSON_NAME", $workbookLessonName);

   $lessonQuestions = $this->getLessonQuestions($currentLessonID);
   $workbookLessons = $this->isWorkbookInstalledByUser($currentUser, $currentUser->getRole($this->getCurrentLesson()),
            $currentLessonID);
   $workbookItems = $this->getWorkbookItems($currentLessonID);

   $nonOptionalQuestionsNr = $this->getNonOptionalQuestionsNr($workbookItems);

   if($nonOptionalQuestionsNr != 0){
    $questionPercentage = (float)(100/$nonOptionalQuestionsNr);
    $questionPercentage = round($questionPercentage, 2);
   }

   $isWorkbookPublished = $this->isWorkbookPublished($currentLessonID);
  }

  if($currentUser->getRole($this->getCurrentLesson()) == 'student'){

   $workbookSettings = $this->getWorkbookSettings($currentLessonID);
   $smarty->assign("T_WORKBOOK_SETTINGS", $workbookSettings);
  }

  $smarty->assign("T_WORKBOOK_BASEURL", $this->moduleBaseUrl);
  $smarty->assign("T_WORKBOOK_BASELINK", $this->moduleBaseLink);

  global $popup;
  (isset($popup) && $popup == 1) ? $popup_ = '&popup=1' : $popup_ = '';

  if(isset($_REQUEST['question_preview']) && $_REQUEST['question_preview'] == '1' &&
        isset($_REQUEST['question_id']) && eF_checkParameter($_REQUEST['question_id'], 'id')){

   $id = $_REQUEST['question_id'];

   if(!in_array($id, array_keys($lessonQuestions))){ // reused item

    $reusedQuestion = $this->getReusedQuestionDetails($id);
    $type = $reusedQuestion['type'];
   }
   else
    $type = $lessonQuestions[$id]['type'];

   echo $this->questionToHtml($id, $type);
   exit;
  }

  if(isset($_REQUEST['get_progress']) && $_REQUEST['get_progress'] == '1'){

   $isWorkbookCompleted = $this->isWorkbookCompleted($currentUser->user['login'], $currentLessonID);
   $studentProgress = $this->getStudentProgress($currentUser->user['login'], $currentLessonID);

   if($isWorkbookCompleted['is_completed'] == 1){

    $unitToComplete = $workbookSettings['unit_to_complete'];

    if($unitToComplete != -1)
     $currentUser->setSeenUnit($unitToComplete, $currentLessonID, true);
   }

   echo $studentProgress.'-'.$isWorkbookCompleted['id'];
   exit;
  }

  if(isset($_GET['edit_settings']) && $_GET['edit_settings'] == '1'){

   if($_SESSION['s_type'] != 'professor'){
    $message = _WORKBOOK_NOACCESS;
    $message_type = 'failure';
    $this->setMessageVar($message, $message_type);
   }

   $content = new EfrontContentTree($currentLessonID);
   $iterator = new EfrontNodeFilterIterator(new RecursiveIteratorIterator(new RecursiveArrayIterator($content->tree),
        RecursiveIteratorIterator::SELF_FIRST), array('ctg_type' => 'theory'));

   $contentOptions = $content->toHTMLSelectOptions($iterator);
   $contentOptions = array(-1 => '-------------') + $contentOptions;

   $workbookSettings = $this->getWorkbookSettings($currentLessonID);

   if($isWorkbookPublished == 1){

    $contentOptions[$workbookSettings['unit_to_complete']] = str_replace('&nbsp;', '',
            $contentOptions[$workbookSettings['unit_to_complete']]);
    $contentOptions[$workbookSettings['unit_to_complete']] = str_replace('&raquo;', '',
            $contentOptions[$workbookSettings['unit_to_complete']]);
   }

   $form = new HTML_QuickForm("edit_settings_form", "post", $this->moduleBaseUrl."&edit_settings=1", "", null, true);
   $form->addElement('text', 'lesson_name', _WORKBOOK_LESSON_NAME, 'class="inputText"');
   $form->addRule('lesson_name', _THEFIELD.' "'._WORKBOOK_LESSON_NAME.'" '._ISMANDATORY, 'required', null, 'client');
   $form->addElement('advcheckbox', 'allow_print', _WORKBOOK_ALLOW_PRINT, null, 'class="inputCheckBox"', array(0, 1));
   $form->addElement('advcheckbox', 'allow_export', _WORKBOOK_ALLOW_EXPORT, null, 'class="inputCheckBox"', array(0, 1));
   $form->addElement('select', 'unit_to_complete', _WORKBOOK_UNIT_TO_COMPLETE, $contentOptions);
   $form->addElement('submit', 'submit', _UPDATE, 'class="flatButton"');

   if($isWorkbookPublished == 1)
    $form->freeze('unit_to_complete');

   $form->setDefaults($workbookSettings);

   if($form->isSubmitted() && $form->validate()){

    $values = $form->exportValues();
    $fields = array(
      "lesson_name" => $values['lesson_name'],
      "allow_print" => $values['allow_print'],
      "allow_export" => $values['allow_export'],
      "unit_to_complete" => $values['unit_to_complete']
     );

    if(eF_updateTableData("module_workbook_settings", $fields, "id=".$workbookSettings['id'])){

     $smarty->assign("T_WORKBOOK_MESSAGE", _WORKBOOK_SETTINGS_SUCCESSFULLY_EDITED);
     $smarty->assign("T_WORKBOOK_MESSAGE_TYPE", 'success');
    }
    else{
     $smarty->assign("T_WORKBOOK_MESSAGE", _WORKBOOK_SETTINGS_EDIT_PROBLEM);
     $smarty->assign("T_WORKBOOK_MESSAGE_TYPE", 'failure');
    }
   }

   $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
   $renderer->setRequiredTemplate('{$html}{if $required}&nbsp;<span class="formRequired">*</span>{/if}');
   $form->setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
   $form->setRequiredNote(_REQUIREDNOTE);
   $form->accept($renderer);
   $smarty->assign('T_WORKBOOK_EDIT_SETTINGS_FORM', $renderer->toArray());
  }

  if(isset($_GET['reuse_item']) && $_GET['reuse_item'] == '1'){

   if($_SESSION['s_type'] != 'professor'){
    $message = _WORKBOOK_NOACCESS;
    $message_type = 'failure';
    $this->setMessageVar($message, $message_type);
   }

   $form = new HTML_QuickForm("reuse_item_form", "post", $this->moduleBaseUrl."&reuse_item=1", "", null, true);
   $form->addElement('text', 'item_id', _WORKBOOK_ITEM_ID, 'class="inputText"');
   $form->addRule('item_id', _THEFIELD.' "'._WORKBOOK_ITEM_ID.'" '._ISMANDATORY, 'required', null, 'client');
   $form->addElement('submit', 'submit', _WORKBOOK_REUSE_ITEM, 'class="flatButton"');

   if($form->isSubmitted() && $form->validate()){

    $values = $form->exportValues();
    $existingIDs = $this->getItemsUniqueIDs();

    if(!in_array($values['item_id'], $existingIDs)){

     $message = _WORKBOOK_INVALID_UNIQUE_ID;
     $message_type = 'failure';
     $this->setMessageVar($message, $message_type);
    }
    else{
     $item = $this->getItemByUniqueID($values['item_id']);

     $fields = array(
       "item_title" => $item['item_title'],
       "item_text" => $item['item_text'],
       "item_question" => $item['item_question'],
       "question_text" => $item['question_text'],
       "check_answer" => $item['check_answer'],
       "lessons_ID" => $currentLessonID,
       "unique_ID" => $this->generateItemID(),
       "position" => $this->itemPosition($currentLessonID)
      );

     if(eF_insertTableData("module_workbook_items", $fields)){

      $smarty->assign("T_WORKBOOK_MESSAGE", _WORKBOOK_ITEM_SUCCESSFULLY_ADDED);
      $smarty->assign("T_WORKBOOK_MESSAGE_TYPE", 'success');
     }
     else{
      $smarty->assign("T_WORKBOOK_MESSAGE", _WORKBOOK_ITEM_ADD_PROBLEM);
      $smarty->assign("T_WORKBOOK_MESSAGE_TYPE", 'failure');
     }
    }
   }

   $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
   $renderer->setRequiredTemplate('{$html}{if $required}&nbsp;<span class="formRequired">*</span>{/if}');
   $form->setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
   $form->setRequiredNote(_REQUIREDNOTE);
   $form->accept($renderer);
   $smarty->assign('T_WORKBOOK_REUSE_ITEM_FORM', $renderer->toArray());
  }

  if(isset($_GET['move_item']) && eF_checkParameter($_GET['move_item'], 'id') && in_array($_GET['move_item'], array_keys($workbookItems))){

   if($_SESSION['s_type'] != 'professor'){
    $message = _WORKBOOK_NOACCESS;
    $message_type = 'failure';
    $this->setMessageVar($message, $message_type);
   }

   $smarty->assign("T_WORKBOOK_ITEMS_COUNT", count($workbookItems));
   $itemPosition = $workbookItems[$_GET['move_item']]['position'];
   $availablePositions = array();

   foreach($workbookItems as $key => $value){

    if($value['position'] != $itemPosition)
     $availablePositions[$value['position']] = $value['position'];
   }

   $form = new HTML_QuickForm("move_item_form", "post", $this->moduleBaseUrl."&move_item=".$_GET['move_item'], "", null, true);
   $form->addElement('select', 'item_position', _WORKBOOK_ITEM_NEW_POSITION, $availablePositions, '');
   $form->addElement('submit', 'submit', _WORKBOOK_MOVE_ITEM, 'class="flatButton"');

   if($form->isSubmitted() && $form->validate()){

    $values = $form->exportValues();
    $newPosition = $values['item_position'];

    if($newPosition > $itemPosition){

     foreach($workbookItems as $key => $value){

      if($value['position'] > $itemPosition && $value['position'] <= $newPosition)
       eF_updateTableData("module_workbook_items", array('position' => $value['position'] - 1), "id=".$key);
     }
    }
    else{
     foreach($workbookItems as $key => $value){

      if($value['position'] < $itemPosition && $value['position'] >= $newPosition)
       eF_updateTableData("module_workbook_items", array('position' => $value['position'] + 1), "id=".$key);
     }
    }

    eF_updateTableData("module_workbook_items", array('position' => $newPosition), "id=".$_GET['move_item']);

    $smarty->assign("T_WORKBOOK_MESSAGE", _WORKBOOK_ITEM_SUCCESSFULLY_MOVED);
    $smarty->assign("T_WORKBOOK_MESSAGE_TYPE", 'success');
   }

   $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
   $renderer->setRequiredTemplate('{$html}{if $required}&nbsp;<span class="formRequired">*</span>{/if}');
   $form->setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
   $form->setRequiredNote(_REQUIREDNOTE);
   $form->accept($renderer);
   $smarty->assign('T_WORKBOOK_MOVE_ITEM_FORM', $renderer->toArray());
  }

  if(isset($_GET['delete_item']) && eF_checkParameter($_GET['delete_item'], 'id') &&
           in_array($_GET['delete_item'], array_keys($workbookItems))){

   $item_id = $_GET['delete_item'];
   $itemPosition = $workbookItems[$item_id]['position'];

   foreach($workbookItems as $key => $value){

    if($value['position'] > $itemPosition)
     eF_updateTableData("module_workbook_items", array('position' => $value['position'] - 1), "id=".$key);
   }

   eF_deleteTableData("module_workbook_items", "id=".$item_id);
  }

  if(isset($_GET['switch_lesson']) && eF_checkParameter($_GET['switch_lesson'], 'id') &&
         in_array($_GET['switch_lesson'], array_keys($workbookLessons))){

   $lessonID = $_GET['switch_lesson'];
   eF_redirect("location:".$this->moduleBaseUrl."&lessons_ID=".$lessonID.$popup_);
  }

  if((isset($_GET['add_item']) && $_GET['add_item'] == '1') ||
  (isset($_GET['edit_item']) && eF_checkParameter($_GET['edit_item'], 'id') && in_array($_GET['edit_item'], array_keys($workbookItems)))){

   if($_SESSION['s_type'] != "professor")
    eF_redirect($this->moduleBaseUrl."&message=".urlencode(_WORKBOOK_NOACCESS).$popup_);

   global $load_editor;
   $load_editor = true;
   $questionsText = array();
   $questionsText[-1] = "-----------------------";

   foreach($lessonQuestions as $key => $value)
    $questionsText[$key] = $this->truncateText(strip_tags($value['text']), 70);

   if(isset($_GET['edit_item'])){

    $editItemID = $_GET['edit_item'];
    $editItemQuestion = $workbookItems[$editItemID]['item_question'];

    if($editItemQuestion != '-1' && !in_array($editItemQuestion, array_keys($questionsText))){ // reused item

     $reusedQuestion = $this->getReusedQuestionDetails($editItemQuestion);
     $questionsText[$editItemQuestion] = $this->truncateText(strip_tags($reusedQuestion['text']), 70);
    }
   }

   isset($_GET['add_item']) ? $postTarget = "&add_item=1" : $postTarget = "&edit_item=".$_GET['edit_item'];

   $form = new HTML_QuickForm("add_edit_item_form", "post", $this->moduleBaseUrl.$postTarget, "", null, true);
   $form->addElement('text', 'item_title', _WORKBOOK_ITEM_TITLE, 'class="inputText" style="width:500px;"');
   $form->addElement('textarea', 'item_text', _WORKBOOK_ITEM_TEXT,
     'class="mceEditor" style="width:99%;height:300px;" id="editor_content_data"');
   $form->addElement('select', 'item_question', _WORKBOOK_ITEM_QUESTION, $questionsText, 'onchange="questionPreview(this)"');
   $form->addElement('advcheckbox', 'check_answer', _WORKBOOK_ITEM_GRADE_ANSWER, null, 'class="inputCheckBox"', array(0, 1));

   if(isset($_GET['add_item']))
    $form->addElement('submit', 'submit', _WORKBOOK_ADD_ITEM, 'class="flatButton"');
   else
    $form->addElement('submit', 'submit', _WORKBOOK_UPDATE_ITEM, 'class="flatButton"');

   if(isset($_GET['edit_item'])){

    $editItem = $workbookItems[$_GET['edit_item']];
    $form->setDefaults($editItem);

    if($isWorkbookPublished == '1'){

     $editItem['question_title'] = $questionsText[$editItem['item_question']];

     if($editItem['check_answer'] == '1')
      $editItem['check_answer_text'] = _YES;
     else
      $editItem['check_answer_text'] = _NO;
    }

    $smarty->assign('T_WORKBOOK_EDIT_ITEM_DETAILS', $editItem);
   }

   if($form->isSubmitted() && $form->validate()){

    $values = $form->exportValues();

    isset($_GET['add_item']) ? $lessonID = $currentLessonID : $lessonID = $editItem['lessons_ID'];
    isset($_GET['add_item']) ? $uniqueID = $this->generateItemID() : $uniqueID = $editItem['unique_ID'];
    isset($_GET['add_item']) ? $position = $this->itemPosition($currentLessonID) : $position = $editItem['position'];

    if($values['item_question'] != '-1'){

     $id = $values['item_question'];

     if(!in_array($id, array_keys($lessonQuestions))){ // edit reused item
      $reusedQuestion = $this->getReusedQuestionDetails($id);
      $type = $reusedQuestion['type'];
     }
     else
      $type = $lessonQuestions[$id]['type'];

     $questionText = $this->questionToHtml($id, $type);
    }
    else
     $questionText = '';

    $fields = array(
      "item_title" => $values['item_title'],
      "item_text" => $values['item_text'],
      "item_question" => $values['item_question'],
      "question_text" => $questionText,
      "check_answer" => $values['check_answer'],
      "lessons_ID" => $lessonID,
      "unique_ID" => $uniqueID,
      "position" => $position
     );

    if($values['item_title'] == '' && $values['item_text'] == '' && $values['item_question'] == '-1'){

     $message = _WORKBOOK_ITEM_EMPTY_FIELDS;

     if(isset($_GET['add_item']))
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure&add_item=1".$popup_);
     else{
      $itemID = $_GET['edit_item'];
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure&edit_item=".$itemID.$popup_);
     }
    }

    if(isset($_GET['add_item'])){

     if(eF_insertTableData("module_workbook_items", $fields)){

      $message = _WORKBOOK_ITEM_SUCCESSFULLY_ADDED;
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=success".$popup_);
     }
     else{
      $message = _WORKBOOK_ITEM_ADD_PROBLEM;
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure".$popup_);
     }
    }
    else{
     if(eF_updateTableData("module_workbook_items", $fields, "id=".$_GET['edit_item'])){

      $message = _WORKBOOK_ITEM_SUCCESSFULLY_EDITED;
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=success".$popup_);
     }
     else{
      $message = _WORKBOOK_ITEM_EDIT_PROBLEM;
      eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure".$popup_);
     }
    }
   }

   $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
   $form->setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
   $form->setRequiredNote(_REQUIREDNOTE);
   $form->accept($renderer);
   $smarty->assign('T_WORKBOOK_ADD_EDIT_ITEM_FORM', $renderer->toArray());

   $basedir = $currentLesson->getDirectory();
   $options = array('lessons_ID' => $currentLessonID, 'metadata' => 0);
   $url = $_SERVER['REQUEST_URI'];
   $extraFileTools = array(array('image' => 'images/16x16/arrow_right.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
   include "file_manager.php";
  }

  if(isset($_GET['publish_workbook']) && $_GET['publish_workbook'] == '1'){

   $result = eF_getTableData("module_workbook_publish", "publish", "lessons_ID=".$currentLessonID);

   if(count($result) == 0){

    if(eF_insertTableData("module_workbook_publish", array('lessons_ID' => $currentLessonID, 'publish' => 1))){

     $message = _WORKBOOK_SUCCESSFULLY_PUBLISHED;
     eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=success".$popup_);
    }
    else{
     $message = _WORKBOOK_PUBLISH_PROBLEM;
     eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure".$popup_);
    }
   }
   else{
    if(eF_updateTableData("module_workbook_publish", array('publish' => 1), "lessons_ID=".$currentLessonID)){

     $message = _WORKBOOK_SUCCESSFULLY_PUBLISHED;
     eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=success".$popup_);
    }
    else{
     $message = _WORKBOOK_PUBLISH_PROBLEM;
     eF_redirect($this->moduleBaseUrl."&message=".$message."&message_type=failure".$popup_);
    }
   }
  }

  if(isset($_GET['reset_workbook_professor']) && $_GET['reset_workbook_professor'] == '1'){

   eF_updateTableData("module_workbook_publish", array('publish' => 0), "lessons_ID=".$currentLessonID);

   foreach($workbookItems as $key => $value){

    eF_deleteTableData("module_workbook_answers", "item_id=".$key);
    eF_deleteTableData("module_workbook_autosave", "item_id=".$key);
    eF_deleteTableData("module_workbook_progress", "lessons_ID=".$currentLessonID);
   }
  }

  if(isset($_GET['reset_workbook_student']) && eF_checkParameter($_GET['reset_workbook_student'], 'id')){

   $id = $_GET['reset_workbook_student'];
   $result = eF_getTableData("module_workbook_progress", "users_LOGIN", "id=".$id);

   if($result[0]['users_LOGIN'] != $currentUser->user['login'])
    eF_redirect($this->moduleBaseUrl."&message=".urlencode(_WORKBOOK_NOACCESS).$popup_);

   eF_deleteTableData("module_workbook_progress", "id=".$id);

   foreach($workbookItems as $key => $value)
    eF_deleteTableData("module_workbook_answers", "item_id=".$key." AND users_LOGIN='".$currentUser->user['login']."'");

   $unitToComplete = $workbookSettings['unit_to_complete'];

   if($unitToComplete != -1)
    $currentUser->setSeenUnit($unitToComplete, $currentLessonID, false);
  }

  if(isset($_GET['download_as']) && $_GET['download_as'] == 'doc'){

   include(dirname(__FILE__)."/classes/html_to_doc.inc.php");

   $workbookAnswers = $this->getWorkbookAnswers($currentUser->user['login'], array_keys($workbookItems));
   $workbookHTML = '';

   foreach($workbookItems as $key => $value){

    $workbookHTML .= '<div style="width:98%;float:left;border:1px dotted #808080;padding: 5px 10px;">';
    $workbookHTML .= '<div style="background-color: #EAEAEA;border: 1px solid #AAAAAA;padding: 2px;font-weight: bold;">';
    $workbookHTML .= _WORKBOOK_ITEMS_COUNT.$value['position'];

    if($value['item_title'] != '')
     $workbookHTML .= '&nbsp;-&nbsp;'.$value['item_title'];

    $workbookHTML .= '</div><br/>';

    if($value['item_text'] != '')
     $workbookHTML .= '<div>'.$value['item_text'].'</div><br/>';

    if($value['item_question'] != '-1'){

     $questionType = $lessonQuestions[$value['item_question']]['type'];

     if($workbookAnswers[$value['id']] == ''){

      if($questionType == 'drag_drop'){

       $dragDrop = eF_getTableData("questions", "options, answer, text", "id=".$value['item_question']);
       $options = unserialize($dragDrop[0]['options']);
       $answer = unserialize($dragDrop[0]['answer']);
       shuffle($options);
       shuffle($answer);

       $workbookHTML .= $dragDrop[0]['text'];

       for($i = 0; $i < count($options); $i++){

        $workbookHTML .= '<div>'.$options[$i].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        $workbookHTML .= $answer[$i].'</div>';
       }
      }
      else{
       $workbookHTML .= '<div>'.$value['question_text'].'</div>';
      }
     }
     else
      $workbookHTML .= '<div>'.$workbookAnswers[$value['id']].'</div>';
    }

    $workbookHTML .= '</div><br/>';
   }

   $workbookHTML = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $workbookHTML);

   $htmltodoc = new HTML_TO_DOC();
   $fileName = _WORKBOOK_NAME.'_'.$this->getWorkbookLessonName($currentLessonID);
   $fileName = preg_replace('/[\s]+/', '_', $fileName);
   $htmltodoc->createDoc($workbookHTML, $fileName, true);

   exit(0);
  }

  if(isset($_GET['download_as']) && $_GET['download_as'] == 'pdf'){

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
   $pdf->setHeaderData('', '', '', $workbookLessonName);
   $pdf->AliasNbPages();
   $pdf->AddPage();
   $pdf->SetFont('Freeserif', '', 10);
   $pdf->SetTextColor(0, 0, 0);

   $workbookAnswers = $this->getWorkbookAnswers($currentUser->user['login'], array_keys($workbookItems));
   $workbookHTML = '';

   $itemLogo = new EfrontFile(G_DEFAULTIMAGESPATH."32x32/unit.png");
   $itemLogoUrl = $itemLogo['path'];

   foreach($workbookItems as $key => $value){

    $workbookHTML .= '<div style="width:98%;float:left;border:1px dotted #808080;">';
    $workbookHTML .= '<div style="background-color: #EAEAEA;font-weight: bold;">';
    $workbookHTML .= '<img src="'.$itemLogoUrl.'"/>&nbsp;'._WORKBOOK_ITEMS_COUNT.$value['position'];

    if($value['item_title'] != '')
     $workbookHTML .= '&nbsp;-&nbsp;'.$value['item_title'];

    $workbookHTML .= '</div>';

    if($value['item_text'] != '')
     $workbookHTML .= '<div>'.$value['item_text'].'</div>';

    if($value['item_question'] != '-1'){

     $questionType = $lessonQuestions[$value['item_question']]['type'];

     if($workbookAnswers[$value['id']] == ''){

      if($questionType == 'drag_drop'){

       $dragDrop = eF_getTableData("questions", "options, answer, text", "id=".$value['item_question']);
       $options = unserialize($dragDrop[0]['options']);
       $answer = unserialize($dragDrop[0]['answer']);
       shuffle($options);
       shuffle($answer);

       $workbookHTML .= $dragDrop[0]['text'];

       for($i = 0; $i < count($options); $i++){

        $workbookHTML .= '<div>'.$options[$i].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        $workbookHTML .= $answer[$i].'</div>';
       }
      }
      else{
       $workbookHTML .= '<div>'.$value['question_text'].'</div>';
      }
     }
     else
      $workbookHTML .= '<div>'.$workbookAnswers[$value['id']].'</div>';
    }

    $workbookHTML .= '</div><br/>';
   }

   $workbookHTML = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $workbookHTML);

   $pdf->writeHTML($workbookHTML, true, false, true, false, '');

   $fileName = _WORKBOOK_NAME.'_'.str_replace(' ', '_', $this->getWorkbookLessonName($currentLessonID)).'.pdf';
   header("Content-type: application/pdf");
   header("Content-disposition: attachment; filename=".$fileName);
   echo $pdf->Output('', 'S');
   exit(0);
  }

  if(isset($_GET['check_workbook_progress']) && $_GET['check_workbook_progress'] == '1'){

   $lessonStudents = $currentLesson->getUsers('student');
   $workbookStudents = array();

   foreach($lessonStudents as $userLogin => $value){

    if($nonOptionalQuestionsNr != 0){

     $studentProgress = $this->getStudentProgress($userLogin, $currentLessonID);
     $studentProgress .= '%';
    }
    else{
     $studentProgress = '-';
    }

    $workbookStudents[$userLogin] = array('login' => $userLogin, 'progress' => $studentProgress);
   }

   $smarty->assign("T_WORKBOOK_STUDENTS", $workbookStudents);
  }

  if(isset($_GET['preview_workbook']) && $_GET['preview_workbook'] == '1' &&
   isset($_GET['student']) && eF_checkParameter($_GET['student'], 'login')){

   $userLogin = $_GET['student'];

   $studentProgress = $this->getStudentProgress($userLogin, $currentLessonID);
   $smarty->assign("T_WORKBOOK_PREVIEW_STUDENT_PROGRESS", $studentProgress);

   $workbookAnswers = $this->getWorkbookAnswers($userLogin, array_keys($workbookItems));
   $smarty->assign("T_WORKBOOK_PREVIEW_ANSWERS", $workbookAnswers);
  }

  if(isset($_GET['item_submitted'])){

   $itemID = $_GET['item_submitted'];
   $questionID = $workbookItems[$itemID]['item_question'];
   $checkAnswer = $workbookItems[$itemID]['check_answer'];

   if(!in_array($questionID, array_keys($lessonQuestions))){ // reused item

    $reusedQuestion = $this->getReusedQuestionDetails($questionID);
    $questionType = $reusedQuestion['type'];
   }
   else
    $questionType = $lessonQuestions[$questionID]['type'];

   $question = QuestionFactory::factory($questionID);
   $question->setDone($_GET['question'][$questionID]);
   $results = $question->correct();

   if($questionType != 'raw_text' && $results['score'] != 1)
    print '-1';
   else{
    $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);

    $fields = array(
      'item_id' => $itemID,
      'html_solved' => $question->toHTMLSolved($form),
      'users_LOGIN' => $currentUser->user['login'],
     );

    eF_insertTableData("module_workbook_answers", $fields);

    if($checkAnswer == '1')
     $this->updateStudentProgress($currentUser->user['login'], $currentLessonID,
           $questionPercentage, $nonOptionalQuestionsNr);
    echo $question->toHTMLSolved($form);
   }

   eF_deleteTableData("module_workbook_autosave", "item_id=".$itemID." AND users_LOGIN='".$currentUser->user['login']."'");
   exit(0);
  }

  if(isset($_GET['item_submitted_autosave'])){

   $itemID = $_GET['item_submitted_autosave'];
   $questionID = $workbookItems[$itemID]['item_question'];

   $question = QuestionFactory::factory($questionID);
   $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);
   $form->setDefaults($_GET);

   $fields = array(
     'item_id' => $itemID,
     'autosave_text' => $question->toHTML($form),
     'users_LOGIN' => $currentUser->user['login'],
    );

   eF_deleteTableData("module_workbook_autosave", "item_id=".$itemID." AND users_LOGIN='".$currentUser->user['login']."'");
   eF_insertTableData("module_workbook_autosave", $fields);

   exit(0);
  }

  else{
   if($currentUser->getRole($this->getCurrentLesson()) == 'professor' || $currentUser->getRole($this->getCurrentLesson()) == 'student'){

    $workbookItems = $this->getWorkbookItems($currentLessonID);
    $smarty->assign("T_WORKBOOK_ITEMS", $workbookItems);
    $smarty->assign("T_WORKBOOK_LESSONS", $workbookLessons);

    $isWorkbookPublished = $this->isWorkbookPublished($currentLessonID);
    $smarty->assign("T_WORKBOOK_IS_PUBLISHED", $isWorkbookPublished);

    $smarty->assign("T_WORKBOOK_NON_OPTIONAL_QUESTIONS_NR", $nonOptionalQuestionsNr);
   }

   if($currentUser->getRole($this->getCurrentLesson()) == 'professor'){

    $workbookOptions[] = array(
      'text' => _SETTINGS,
      'image' => $this->moduleBaseLink.'images/settings.png',
      'href' => $this->moduleBaseUrl.'&edit_settings=1&popup=1',
      'onClick' => "eF_js_showDivPopup('"._SETTINGS."', 0)",
      'target' => 'POPUP_FRAME',
      'id' => 'edit_settings'
     );

    $workbookOptions[] = array(
      'text' => _WORKBOOK_POPUP_INFO,
      'image' => $this->moduleBaseLink.'images/info.png',
      'href' => $this->moduleBaseUrl.'&popup_info=1&popup=1',
      'onClick' => "eF_js_showDivPopup('"._WORKBOOK_POPUP_INFO."', 2)",
      'target' => 'POPUP_FRAME',
      'id' => 'popup_info'
     );

    $smarty->assign("T_WORKBOOK_OPTIONS", $workbookOptions);
   }
   else if($currentUser->getRole($this->getCurrentLesson()) == 'student'){

    $workbookAnswers = $this->getWorkbookAnswers($currentUser->user['login'], array_keys($workbookItems));
    $smarty->assign("T_WORKBOOK_ANSWERS", $workbookAnswers);

    $autoSaveAnswers = $this->getAutoSaveAnswers($currentUser->user['login'], array_keys($workbookItems));
    $smarty->assign("T_WORKBOOK_AUTOSAVE_ANSWERS", $autoSaveAnswers);

    $studentProgress = $this->getStudentProgress($currentUser->user['login'], $currentLessonID);
    $smarty->assign("T_WORKBOOK_STUDENT_PROGRESS", $studentProgress);

    $isWorkbookCompleted = $this->isWorkbookCompleted($currentUser->user['login'], $currentLessonID);
    $smarty->assign("T_WORKBOOK_IS_COMPLETED", $isWorkbookCompleted);
   }
  }

  if($currentUser->getRole($this->getCurrentLesson()) == 'professor')
   return $this->moduleBaseDir."module_workbook_professor.tpl";

  else if($currentUser->getRole($this->getCurrentLesson()) == 'student')
   return $this->moduleBaseDir."module_workbook_student.tpl";
 }

 public function isLessonModule(){
  return true;
 }

 public function onInstall(){

  eF_executeNew("DROP TABLE IF EXISTS `module_workbook_settings`");
  $t1 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_workbook_settings` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `lessons_ID` int(11) NOT NULL,
     `lesson_name` varchar(255) NOT NULL,
     `allow_print` tinyint(1) NOT NULL DEFAULT '1',
     `allow_export` tinyint(1) NOT NULL DEFAULT '1',
     `unit_to_complete` int(11) NOT NULL DEFAULT '-1',
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  eF_executeNew("DROP TABLE IF EXISTS `module_workbook_items`");
  $t2 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_workbook_items` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `item_title` varchar(255) DEFAULT NULL,
     `item_text` text,
     `item_question` int(11) NOT NULL,
     `question_text` longtext,
     `check_answer` tinyint(1) NOT NULL,
     `lessons_ID` int(11) NOT NULL,
     `unique_ID` varchar(50) NOT NULL,
     `position` int(11) NOT NULL,
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  eF_executeNew("DROP TABLE IF EXISTS `module_workbook_answers`");
  $t3 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_workbook_answers` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `item_id` int(11) NOT NULL,
     `html_solved` text,
     `users_LOGIN` varchar(255) NOT NULL,
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  eF_executeNew("DROP TABLE IF EXISTS `module_workbook_progress`");
  $t4 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_workbook_progress` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `lessons_ID` int(11) NOT NULL,
     `users_LOGIN` varchar(255) NOT NULL,
     `progress` float(5,2) NOT NULL,
     `non_optional` int(11) NOT NULL,
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  eF_executeNew("DROP TABLE IF EXISTS `module_workbook_publish`");
  $t5 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_workbook_publish` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `lessons_ID` int(11) NOT NULL,
     `publish` tinyint(1) NOT NULL,
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  eF_executeNew("DROP TABLE IF EXISTS `module_workbook_autosave`");
  $t6 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_workbook_autosave` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `item_id` int(11) NOT NULL,
     `autosave_text` longtext NOT NULL,
     `users_LOGIN` varchar(255) NOT NULL,
     PRIMARY KEY (`id`)
     ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

  return($t1 && $t2 && $t3 && $t4 && $t5 && $t6);
 }

 public function onUninstall(){

  $t1 = eF_executeNew("DROP TABLE IF EXISTS `module_workbook_settings`");
  $t2 = eF_executeNew("DROP TABLE IF EXISTS `module_workbook_items`");
  $t3 = eF_executeNew("DROP TABLE IF EXISTS `module_workbook_answers`");
  $t4 = eF_executeNew("DROP TABLE IF EXISTS `module_workbook_progress`");
  $t5 = eF_executeNew("DROP TABLE IF EXISTS `module_workbook_publish`");
  $t6 = eF_executeNew("DROP TABLE IF EXISTS `module_workbook_autosave`");

  return($t1 && $t2 && $t3 && $t4 && $t5 && $t6);
 }

 public function onUpgrade(){

  $columns = mysql_query("show columns from `module_workbook_settings`");
  $alter = true;
  $found = false;

  if($columns){

   while(($col = mysql_fetch_assoc($columns))){

    if($col['Field'] == 'unit_to_complete'){

     $found = true;
     break;
    }
   }

   if($found == false) // field does not exist
    $alter = eF_executeNew("ALTER TABLE `module_workbook_settings` ADD COLUMN `unit_to_complete` INT(11) NOT NULL DEFAULT '-1' AFTER `allow_export`");
  }

  return($columns && $alter);
 }

 public function getLessonCenterLinkInfo(){

  return array(
   'title' => _WORKBOOK_NAME,
   'image' => $this->moduleBaseDir.'images/workbook_logo.png',
   'link' => $this->moduleBaseUrl
  );
 }

 public function getSidebarLinkInfo(){

  $currentLessonMenu = array(array(
      'id' => 'workbook_link_1',
      'title' => _WORKBOOK_NAME,
      'image' => $this->moduleBaseDir.'images/workbook_logo16',
      'eFrontExtensions' => '1',
      'link' => $this->moduleBaseUrl)
     );

  return array("current_lesson" => $currentLessonMenu);
 }

 public function getLinkToHighlight(){

  return 'workbook_link_1';
 }

 public function getNavigationLinks(){

  $currentUser = $this->getCurrentUser();
  $currentLesson = $this->getCurrentLesson();
  $currentUserRole = $currentUser->getRole($currentLesson);
  $onClick = "location='".$currentUserRole.".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();";

  if(isset($_GET['add_item'])){

   return array(
    array('title' => _MYCOURSES, 'onclick' => $onClick),
    array('title' => $currentLesson->lesson['name'], 'link' => $currentUser->getType().".php?ctg=control_panel"),
    array('title' => _WORKBOOK_NAME, 'link' => $this->moduleBaseUrl),
    array('title' => _WORKBOOK_ADD_ITEM, 'link' => $_SERVER['REQUEST_URI'])
   );
  }
  else if(isset($_GET['edit_item'])){

   return array(
    array('title' => _MYCOURSES, 'onclick' => $onClick),
    array('title' => $currentLesson->lesson['name'], 'link' => $currentUser->getType().".php?ctg=control_panel"),
    array('title' => _WORKBOOK_NAME, 'link' => $this->moduleBaseUrl),
    array('title' => _WORKBOOK_EDIT_ITEM, 'link' => $_SERVER['REQUEST_URI'])
   );
  }
  else if(isset($_GET['check_workbook_progress'])){

   return array(
    array('title' => _MYCOURSES, 'onclick' => $onClick),
    array('title' => $currentLesson->lesson['name'], 'link' => $currentUser->getType().".php?ctg=control_panel"),
    array('title' => _WORKBOOK_NAME, 'link' => $this->moduleBaseUrl),
    array('title' => _WORKBOOK_CHECK_PROGRESS, 'link' => $_SERVER['REQUEST_URI'])
   );
  }
  else{
   return array(
    array('title' => _MYCOURSES, 'onclick' => $onClick),
    array('title' => $currentLesson->lesson['name'], 'link' => $currentUser->getType().".php?ctg=control_panel"),
    array('title' => _WORKBOOK_NAME, 'link' => $this->moduleBaseUrl)
   );
  }
 }

 public function getModuleCSS(){

  return $this->moduleBaseDir.'css/workbook.css';
 }

 public function addScripts(){

  return array("scriptaculous/dragdrop", "includes/tests");
 }

 public function onExportLesson($lessonId){

  $data = eF_getTableData("module_workbook_items", "*", "lessons_ID=".$lessonId);
  return $data;
 }

 public function onImportLesson($lessonId, $data){

  foreach($data as $record){

   unset($record['id']);
   $record['lessons_ID'] = $lessonId;
   eF_insertTableData("module_workbook_items", $record);
  }

  return true;
 }

 public function onDeleteLesson($lessonId){

  $lessonQuestions = array();
  $itemsToDelete = array();
  $result = eF_getTableData("module_workbook_items", "item_question", "lessons_ID=".$lessonId);

  foreach($result as $value)
   array_push($lessonQuestions, $value['item_question']);

  for($i = 0; $i < count($lessonQuestions); $i++){

   $items = eF_getTableData("module_workbook_items", "id, lessons_ID", "item_question=".$lessonQuestions[$i]);

   foreach($items as $item)
    $itemsToDelete[$item['id']] = $item;
  }

  foreach($itemsToDelete as $key => $value){

   $lessonItems = $this->getWorkbookItems($value['lessons_ID']);

   foreach($lessonItems as $key2 => $value2){

    eF_deleteTableData("module_workbook_answers", "item_id=".$key2);
    eF_deleteTableData("module_workbook_autosave", "item_id=".$key2);
    eF_deleteTableData("module_workbook_progress", "lessons_ID=".$value2['lessons_ID']);
   }

   eF_deleteTableData("module_workbook_publish", "lessons_ID=".$value['lessons_ID']);
   $itemPosition = $lessonItems[$key]['position'];

   foreach($lessonItems as $key2 => $value2){

    if($value2['position'] > $itemPosition)
     eF_updateTableData("module_workbook_items", array('position' => $value2['position'] - 1), "id=".$key2);
   }

   eF_deleteTableData("module_workbook_items", "id=".$key);
  }

  return true;
 }

 // Inner Functions

 function getWorkbookLessonName($lessonID){

  $result = eF_getTableData("module_workbook_settings", "lesson_name", "lessons_ID=".$lessonID);
  //$lessonName = _WORKBOOK_NAME.' ['.$result[0]['lesson_name'].']';

  return $result[0]['lesson_name'];
 }

 function getWorkbookSettings($lessonID){

  $result = eF_getTableData("module_workbook_settings", "*", "lessons_ID=".$lessonID);

  $settings = array(
    'id' => $result[0]['id'],
    'lesson_name' => $result[0]['lesson_name'],
    'allow_print' => $result[0]['allow_print'],
    'allow_export' => $result[0]['allow_export'],
    'unit_to_complete' => $result[0]['unit_to_complete']
   );

  return $settings;
 }

 function getLessonQuestions($lessonID){

  $type = "(type='multiple_one' OR type='multiple_many' OR type='raw_text' OR type='empty_spaces' ";
  $type .= "OR type='match' OR type='true_false' OR type='drag_drop') AND ";
  $result = eF_getTableData("questions", "id, text, type", $type."lessons_ID=".$lessonID);
  $questions = array();

  foreach($result as $value)
   $questions[$value['id']] = $value;

  return $questions;
 }

 function getReusedQuestionDetails($questionID){

  $result = eF_getTableData("questions", "text, type", "id=".$questionID);

  return array('id' => $questionID, 'text' => $result[0]['text'], 'type' => $result[0]['type']);
 }

 function truncateText($string, $length=80, $etc='...', $middle=false){

  if($length == 0)
   return '';

  if(mb_strlen($string) > $length){

   $length -= mb_strlen($etc);

   if(!$middle)
    return mb_substr($string, 0, $length).$etc;
   else
    return mb_substr($string, 0, round($length/2)).$etc.mb_substr($string, -round($length/2));
  }
  else
   return $string;
 }

 function generateItemID(){

  $existingIDs = array();
  $uniqueID = uniqid();
  $result = eF_getTableData("module_workbook_items", "unique_ID");

  foreach($result as $value)
   array_push($existingIDs, $value['unique_ID']);

  while(in_array($uniqueID, $existingIDs))
   $uniqueID = uniqid();

  return $uniqueID;
 }

 function itemPosition($lessonID){

  $result = eF_getTableData("module_workbook_items", "position", "lessons_ID=".$lessonID);

  if(count($result) == 0)
   return 1;
  else{
   $positions = array();

   foreach($result as $value)
    array_push($positions, $value['position']);

   $max = max($positions);

   return ($max + 1);
  }
 }

 function getItemsUniqueIDs(){

  $existingIDs = array();
  $result = eF_getTableData("module_workbook_items", "unique_ID");

  foreach($result as $value)
   array_push($existingIDs, $value['unique_ID']);

  return $existingIDs;
 }

 function getItemByUniqueID($uniqueID){

  $result = eF_getTableData("module_workbook_items", "*", "unique_ID='".$uniqueID."'");
  return $result[0];
 }

 function getWorkbookItems($lessonID){

  $result = eF_getTableData("module_workbook_items", "*", "lessons_ID=".$lessonID, "position");
  $items = array();

  foreach($result as $value)
   $items[$value['id']] = $value;

  return $items;
 }

 function questionToHtml($questionID, $questionType){

  if($questionType == 'multiple_one')
   $question = new MultipleOneQuestion($questionID);

  else if($questionType == 'multiple_many')
   $question = new MultipleManyQuestion($questionID);

  else if($questionType == 'raw_text')
   $question = new RawTextQuestion($questionID);

  else if($questionType == 'empty_spaces')
   $question = new EmptySpacesQuestion($questionID);

  else if($questionType == 'match'){
   $question = new MatchQuestion($questionID);
   $question->shuffle();
  }

  else if($questionType == 'true_false')
   $question = new TrueFalseQuestion($questionID);

  else if($questionType == 'drag_drop')
   $question = new DragDropQuestion($questionID);

  $form = new HTML_QuickForm("questionForm", "post", "", "", null, true);
  return $question->toHTML($form);
 }

 function isWorkbookInstalledByUser($currentUser, $currentUserRole, $currentLessonID){

  $userLessons = $currentUser->getLessons(false, $currentUserRole);
  $lessons = array();

  unset($userLessons[$currentLessonID]); // do not use current lesson
  $lessons[-1] = array("id" => -1, "name" => _WORKBOOK_SWITCH_TO);
  $lessons[-2] = array("id" => -2, "name" => '-------------');

  foreach($userLessons as $key => $value){

   $lesson = new EfrontLesson($key);
   $installed = $lesson->getOptions(array('module_workbook'));

   if(count($installed) != 0 && $installed['module_workbook'] == 1)
    $lessons[$key] = array("id" => $key, "name" => $lesson->lesson['name']);
  }

  return $lessons;
 }

 function getWorkbookAnswers($userLogin, $itemIDs){

  $answers = array();

  for($i = 0; $i < count($itemIDs); $i++){

   $result = eF_getTableData("module_workbook_answers", "html_solved", "item_id=".$itemIDs[$i]." AND users_LOGIN='".$userLogin."'");

   if(count($result) != 0)
    $answers[$itemIDs[$i]] = $result[0]['html_solved'];
   else
    $answers[$itemIDs[$i]] = '';
  }

  return $answers;
 }

 function getNonOptionalQuestionsNr($workbookItems){

  $nr = 0;

  foreach($workbookItems as $key => $value){

   if($value['item_question'] != '-1' && $value['check_answer'] == '1')
    $nr++;
  }

  return $nr;
 }

 function getStudentProgress($userLogin, $lessonID){

  $result = eF_getTableData("module_workbook_progress", "progress", "lessons_ID=".$lessonID." AND users_LOGIN='".$userLogin."'");

  if(count($result) == 0)
   return 0;
  else{
   $progress = $result[0]['progress'];
   $tmp = explode('.', $progress);

   if(count($tmp) > 1 && $tmp[1] == '00')
    return $tmp[0];
   else
    return $progress;
  }
 }

 function updateStudentProgress($userLogin, $lessonID, $percentage, $nonOptionalQuestionsNr){

  $result = eF_getTableData("module_workbook_progress", "id, progress, non_optional",
           "lessons_ID=".$lessonID." AND users_LOGIN='".$userLogin."'");
  if(count($result) == 0){

   $fields = array(
     'lessons_ID' => $lessonID,
     'users_LOGIN' => $userLogin,
     'progress' => $percentage,
     'non_optional' => $nonOptionalQuestionsNr - 1
   );

   eF_insertTableData("module_workbook_progress", $fields);
  }
  else{
   $progress = $result[0]['progress'] + $percentage;
   $nonOptional = $result[0]['non_optional'] - 1;

   if($progress > 100.00)
    $progress = 100.00;

   if($nonOptional == 0)
    $progress = 100.00;

   eF_updateTableData("module_workbook_progress", array('progress'=>$progress, 'non_optional'=>$nonOptional), "id=".$result[0]['id']);
  }
 }

 function isWorkbookCompleted($userLogin, $lessonID){

  $result = eF_getTableData("module_workbook_progress", "id, non_optional", "lessons_ID=".$lessonID." AND users_LOGIN='".$userLogin."'");

  if(count($result) != 0 && $result[0]['non_optional'] == '0')
   return array('id' => $result[0]['id'], 'is_completed' => 1);
  else
   return array('id' => $result[0]['id'], 'is_completed' => 0);
 }

 function isWorkbookPublished($lessonID){

  $result = eF_getTableData("module_workbook_publish", "publish", "lessons_ID=".$lessonID);

  if(count($result) == 0)
   return 0;
  else
   return $result[0]['publish'];
 }

 function getAutoSaveAnswers($userLogin, $itemIDs){

  $answers = array();

  for($i = 0; $i < count($itemIDs); $i++){

   $result = eF_getTableData("module_workbook_autosave", "autosave_text", "item_id=".$itemIDs[$i]." AND users_LOGIN='".$userLogin."'");

   if(count($result) != 0)
    $answers[$itemIDs[$i]] = $result[0]['autosave_text'];
   else
    $answers[$itemIDs[$i]] = '';
  }

  return $answers;
 }
}

?>
