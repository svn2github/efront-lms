<?php
class module_crossword extends EfrontModule {

 public function getName() {
  return _CROSSWORD_CROSSWORD;
 }

 public function getPermittedRoles() {
  return array("student","professor","administrator");
 }

 public function getModule() {
  $smarty = $this -> getSmartyVar();
  $currentLesson = $this -> getCurrentLesson();
  $currentUser = $this -> getCurrentUser();
  try {
   $currentContent = new EfrontContentTree($_SESSION['s_lessons_ID']); //Initialize content
  } catch (Exception $e) {
   $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
   $message = _ERRORLOADINGCONTENT.": ".$_SESSION['s_lessons_ID'].": ".$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
  }
  //pr($currentUser);exit;
  $roles = EfrontUser :: getRoles();
  //pr($roles);
  if ($roles[$currentUser ->lessons[$_SESSION['s_lessons_ID']]] == "professor") {
   if (isset($_GET['view_list']) && eF_checkParameter($_GET['view_list'],'id')) {
    $list = $currentContent -> seekNode($_GET['view_list']);
    $questions = $list -> getQuestions(true);
    $crosslists = array();
    $possibleCrosslistsIds = array();
    foreach ($questions as $key => $value) {
     if ($value -> question['type'] == 'empty_spaces'){
      $crosslists[] = $value;
      $possibleCrosslistsIds[] = $value->question['id'];
     }
    }
    $questions = $crosslists;
    //pr($questions);
    foreach ($questions as $qid => $question) {
     $questions[$qid]->question['text'] = str_replace('#','_',strip_tags($question->question['text'])); //If we ommit this line, then the questions list is html formatted, images are displayed etc, which is *not* the intended behaviour
     //$questions[$qid]->question['answer']           = unserialize($question->question['answer']);
    }
    $res = eF_getTableData("module_crossword_words", "crosslists,options", "content_ID=".$_GET['view_list']);
    $resCrosslists = unserialize($res[0]['crosslists']);
    $smarty -> assign("T_CROSSWORD_LIST_WORDS", $resCrosslists);

    $post_target = $this -> moduleBaseUrl.'&view_list='.$_GET['view_list']."&tab=options";
    //Create form elements
    $form = new HTML_QuickForm("list_options", "post", $post_target, "", null, true);
    $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');
    $form -> addElement('advcheckbox', 'active',_CROSSWORD_ACTIVE, null, 'class = "inputCheckbox"', array(0, 1));
    $form -> addElement("text", "max_word", _LOW,'size = "5"');
    $form -> addRule('max_word', _INVALIDFIELDDATA.":"._LOW, 'checkParameter', 'id');

    $form -> addElement('advcheckbox', 'reveal_answer',_CROSSWORD_SHOWANSWERFIRST, null, 'class = "inputCheckbox"', array(0, 1));
    $form -> addElement('advcheckbox', 'save_pdf', _CROSSWORD_SAVEPDF, null, 'class = "inputCheckbox"', array(0, 1));
    $form -> addElement('submit', 'submit_options', _SAVECHANGES,'onclick ="return optionSubmit();" class = "flatButton"'); //The submit content button

    $options = unserialize($res[0]['options']);

    $form -> setDefaults(array( 'active' => $options['active'],
           'reveal_answer' => $options['reveal_answer'],
           'save_pdf' => $options['save_pdf'],
           'max_word' => $options['max_word']));

    if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
     $values = $form -> exportValues();
     unset($values['submit_options']);
     $options = serialize($values);
     if (sizeof($res) != 0) {
      $ok = eF_updateTableData("module_crossword_words", array('options' => $options),"content_ID=".$_GET['view_list']);
     } else {
      $fields = array ('content_ID' => $_GET['view_list'],
          'options' => $options);
      $ok = eF_insertTableData("module_crossword_words", $fields);
     }
     if ($ok !== false) {
      $message = _CROSSWORD_SUCCESSFULLY;
      $message_type = 'success';
     } else {
      $message = _CROSSWORD_PROBLEMOCCURED;
      $message_type = 'failure';
     }
     eF_redirect("".$this -> moduleBaseUrl."&view_list=".$_GET['view_list']."&tab=options&message=".urlencode($message)."&message_type=".$message_type);
    }

    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty); //Create a smarty renderer

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created
    $smarty -> assign('T_CROSSWORD_OPTIONS', $renderer -> toArray()); //Assign the form to the template

    if (isset($_GET['postAjaxRequest'])) {
     try {
      $result = eF_getTableData("module_crossword_words", "crosslists", "content_ID=".$_GET['view_list']);
      //pr($result);exit;
      $crosslistsArray = unserialize($result[0]['crosslists']);
      if (isset($_GET['id']) && eF_checkParameter($_GET['id'], 'id')) {
       if (!in_array($_GET['id'], array_values($crosslistsArray))) {
        $crosslistsArray[] = $_GET['id'];
        $crosslists = serialize($crosslistsArray);
        if (sizeof($result) != 0) {
         $fields = array('crosslists' => $crosslists);
         eF_updateTableData("module_crossword_words", $fields, "content_ID=".$_GET['view_list']);
        } else {
         $fields = array ('content_ID' => $_GET['view_list'],
             'crosslists' => $crosslists);
         eF_insertTableData("module_crossword_words", $fields);
        }
       }
       elseif (in_array($_GET['id'], array_values($crosslistsArray))) {
        unset($crosslistsArray[array_search($_GET['id'], $crosslistsArray)]);
        if (!empty($crosslistsArray)) {
         $crosslists = serialize($crosslistsArray);
         $fields = array('crosslists' => $crosslists);
         eF_updateTableData("module_crossword_words", $fields,"content_ID=".$_GET['view_list']);
        } else {
         eF_deleteTableData("module_crossword_words", "content_ID=".$_GET['view_list']);
        }
       }
      } else if (isset($_GET['addAll'])) {
       $crosslists = serialize($possibleCrosslistsIds);
       if (sizeof($result) != 0) {
        $fields = array('crosslists' => $crosslists);
        eF_updateTableData("module_crossword_words", $fields,"content_ID=".$_GET['view_list']);
       } else {
        $fields = array ('content_ID' => $_GET['view_list'],
            'crosslists' => $crosslists);
        eF_insertTableData("module_crossword_words", $fields);
       }
      } else if (isset($_GET['removeAll'])) {
       $fields = array('crosslists' => "");
       eF_updateTableData("module_crossword_words", $fields,"content_ID=".$_GET['view_list']);

      }
     } catch (Exception $e) {
      header("HTTP/1.0 500 ");
      echo $e -> getMessage().' ('.$e -> getCode().')';
     }
     exit;
    }

    $smarty -> assign("T_CROSSWORD_CROSSLISTS", $crosslists);
    $smarty -> assign("T_CROSSWORD_CROSSLISTS_SIZE", sizeof($crosslists));
   } else {
    $listsArray = array();
    $iterator = new EfrontContentFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($currentContent -> tree, RecursiveIteratorIterator :: SELF_FIRST)));
    foreach ($iterator as $key => $value) {
     $questions = $value -> getQuestions(true);
     $crosslists = array();
     foreach ($questions as $key2 => $value2) {
      if ($value2 -> question['type'] == 'empty_spaces'){
       $crosslists[] = $value2;
      }
     }

     if (sizeof($crosslists) > 0) {
      $listsArray[$value['id']] = array('id' => $value['id'],
              'name' => $value['name'],
              'questions' => sizeof($crosslists));
     }
    }
    if (!empty($listsArray)) {
     $str = implode(",",array_keys($listsArray));
     $lists = eF_getTableDataFlat("module_crossword_words","*","content_ID IN (".$str.")");
     $listsTemp = array_combine(array_values($lists['content_ID']) , array_values($lists['options']));
     $listsTemp2 = array_combine(array_values($lists['content_ID']) , array_values($lists['crosslists']));
     foreach ($listsArray as $key => $value) {
      $listsArray[$value['id']]['options'] = unserialize($listsTemp[$key]);
      $crosslistsTemp = unserialize($listsTemp2[$key]);
      $listsArray[$value['id']]['num_crosslists'] = empty($crosslistsTemp) ? 0 : sizeof($crosslistsTemp);
     }
    }

    $smarty -> assign("T_CROSSWORD_WORDS", $listsArray);
   }
  } elseif ($roles[$currentUser->lessons[$_SESSION['s_lessons_ID']]] == "student") {
   if (isset($_GET['restart_list']) && eF_checkParameter($_GET['restart_list'],'id')) {
    eF_deleteTableData("module_crossword_users", "users_LOGIN='".$_SESSION['s_login']."' AND content_ID=".$_GET['restart_list']);
   }
   if (isset($_GET['restart_lists'])) {
    eF_deleteTableData("module_crossword_users", "users_LOGIN='".$_SESSION['s_login']."'");
   }
   if ($_GET['answer'] == "true") {
    $resUserCard = eF_getTableData("module_crossword_users", "*", "crosslists_ID=".$_GET['view_card']." and users_LOGIN='".$_SESSION['s_login']."'");
    if (sizeof($resUserCard) == 0) {
     $fields = array('users_LOGIN' => $_SESSION['s_login'],
         'content_ID' => $_GET['view_list'],
         'crosslists_ID' => $_GET['view_card'],
         'success' => '1');
     eF_insertTableData("module_crossword_users", $fields);
    } else {
     $success = $resUserCard[0]['success'] + 1;
     eF_updateTableData("module_crossword_users",array('success' => $success),"crosslists_ID=".$_GET['view_card']." and users_LOGIN='".$_SESSION['s_login']."'");
    }
   } elseif($_GET['answer'] == "false") {
    $resUserCard = eF_getTableData("module_crossword_users", "*", "crosslists_ID=".$_GET['view_card']." and users_LOGIN='".$_SESSION['s_login']."'");
    $currentListTemp = eF_getTableData("module_crossword_words","options","content_ID=".$_GET['view_list']);
    $listTemp = unserialize($currentListTemp[0]['options']);
    if ($listTemp['wrong'] == 1 && sizeof($resUserCard) != 0 && $resUserCard[0]['success'] != 0) {
     $success = $resUserCard[0]['success'] - 1;
     eF_updateTableData("module_crossword_users",array('success' => $success),"crosslists_ID=".$_GET['view_card']." and users_LOGIN='".$_SESSION['s_login']."'");
    }
   }
   $listsArray = array();
   $iterator = new EfrontContentFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($currentContent -> tree, RecursiveIteratorIterator :: SELF_FIRST)));
   foreach ($iterator as $key => $value) {
    $listsArray[$value['id']] = array( 'id' => $value['id'],
              'name' => $value['name']);
   }
   if (empty($listsArray)) {
    $smarty -> assign("T_CROSSWORD_WORDSNAMES", $listsArray);
    return true;
   }
   $str = implode(",",array_keys($listsArray));
   $lists = eF_getTableData("module_crossword_words","*","content_ID IN (".$str.")");
   $mastery = eF_getTableDataFlat("module_crossword_users","*","content_ID IN (".$str.")");
   $masteryArray = array_combine(array_values($mastery['crosslists_ID']) , array_values($mastery['success']));
   $questionsDiff = eF_getTableDataFlat("questions","*","content_ID IN (".$str.")");
   $questionsDiffArray = array_combine(array_values($questionsDiff['id']) , array_values($questionsDiff['difficulty']));
   $validLists = array();
   foreach ($lists as $key => $value) {
    $opt = unserialize($value['options']);
    $crosslists = unserialize($value['crosslists']);
    if ($opt['active'] == 1 && !empty($crosslists)) {
     $value['number_crosslists'] = (empty($crosslists) ? 0 : sizeof($crosslists));
     $validLists[$value['content_ID']] = $value;
     $validLists[$value['content_ID']]['options'] = $opt;
     $finishedCrosslists = 0;
     foreach ($crosslists as $index => $item) {
      if($masteryArray[$item] == $opt[$questionsDiffArray[$item]]) {
       $finishedCrosslists++;
      }
     }
     $conid = $validLists[$value['content_ID']]['content_ID'];
     $validLists[$value['content_ID']]['non_finished'] = $value['number_crosslists'] - $finishedCrosslists;
     $validLists[$value['content_ID']]['mastery'] = ((float)$finishedCrosslists/sizeof($crosslists)*100);
     $respoints = eF_getTableDataFlat("module_crossword_users","*","content_ID = '$conid' and users_LOGIN='".$_SESSION['s_login']."'");
     $validLists[$value['content_ID']]['points'] = round($respoints['points'][0]/$respoints['totallength'][0]*100);
     $validLists[$value['content_ID']]['crosstime'] = $respoints['wordtime'][0];
    }
   }

   //print_r($validLists);
   $smarty -> assign("T_CROSSWORD_WORDS", $validLists);
   $smarty -> assign("T_CROSSWORD_WORDSNAMES", $listsArray);

   if(isset($_GET['view_list']) && !isset($_GET['pdf'])) {
    $resunit = eF_getTableData("content", "name", "id=".$_GET['view_list']);
    $smarty -> assign("T_CROSSWORD_UNITNAME", $resunit[0]['name']);

    $_SESSION['contentid'] = $_GET['view_list'];
    if(isset($_POST) && !empty($_POST['crosstime'])){
     $userlist = eF_getTableData("module_crossword_users","*","users_LOGIN='".$_SESSION['s_login']."' and content_ID=".$_GET['view_list']."");
     if(count($userlist)==0){
      $fields = array('users_LOGIN' => $_SESSION['s_login'],
          'content_ID' => $_GET['view_list'],
          'points' => $_POST['points'],
       'totallength' => $_SESSION['WORDLEN'],
          'wordtime' => $_POST['crosstime']);
      eF_insertTableData("module_crossword_users", $fields);
     }else{
      $fields = array('points' => $_POST['points'],
        'totallength' => $_SESSION['WORDLEN'],
          'wordtime' => $_POST['crosstime']);
      eF_updateTableData("module_crossword_users", $fields, "content_ID=".$_GET['view_list']." and users_LOGIN='".$_SESSION['s_login']."'");
     }
     $message_type = 'success';
     $message = _CROSSWORD_GAME_SUCCESSFULLY;
     eF_redirect($this -> moduleBaseUrl."&message=".urlencode($message)."&message_type=".$message_type);
    }
    $contentid = $_GET['view_list'];
    $res = eF_getTableData("module_crossword_words", "crosslists,options", "content_ID=".$_GET['view_list']);
    $reswords = unserialize($res[0]['crosslists']);
    $maxwords = unserialize($res[0]['options']);
    $maxwords1 = $maxwords['max_word'];
    $smarty -> assign("T_CROSSWORD_REVEALANSWER", $maxwords['reveal_answer']);
    $smarty -> assign("T_CROSSWORD_MAXWORD", $maxwords1+1);
    $_SESSION['CROSSWORD_MAXWORD']=$maxwords1;
    require_once('init.php');
    $rowquesans = "";
    foreach($reswords as $rowques){
     $rowquesans .= $rowques.",";
    }
    $quesids = mb_substr($rowquesans,0,-1);
    $quesans = eF_getTableData("questions","text,answer","id IN($quesids) order by rand() limit $maxwords1");
    $value = array();
    foreach($quesans as $row){
     $answer = unserialize($row['answer']);
     $answer1 = explode("|",$answer['0']);
     $value[]= array('ANSWER'=>$answer1['0'],'QUESTION'=>$row['text']);
    }
    if(!empty($value)){
   //pr($value);exit;	
     $success = $pc->generateFromWords($value);
     if(!$success){
      $message_type = 'failure';
      $message = _CROSSWORD_UNABLEGENERATECROSSWORD;
      eF_redirect($this -> moduleBaseUrl."&message=".urlencode($message)."&message_type=".$message_type);
     }else{
      $words = $pc->getWords();
      $wordlen = "";
      foreach($words as $rowwords){
       $wordlen = $wordlen+$rowwords['wordlength'];
      }
      $_SESSION['WORDLEN']=$wordlen;
      $smarty -> assign("T_CROSSWORD_LENGTH", $_SESSION['WORDLEN']);
      $smarty -> assign("T_CROSSWORD_ANSWERS", $words);
     }

    }


    $post_target = $this -> moduleBaseUrl."&view_list=".$_GET['view_list']."";
    $form = new HTML_QuickForm("crossword_game", "post", $post_target, "", null, true);
    $form -> addElement('submit', 'submit_crossword', 'SUBMIT', 'class = "flatButton"'); //The submit content button
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty); //Create a smarty renderer

    $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
    $form -> setRequiredNote(_REQUIREDNOTE);
    $form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created
    $smarty -> assign('T_CROSSWORD_SUBMIT', $renderer -> toArray()); //Assign the form to the template
    $message = "";
    //$message_type = 'success';
    // eF_redirect("".$this -> moduleBaseUrl."&popup=1&finish=1&message=".$message."&message_type=".$message_type);

   }else if (isset($_GET['view_list']) && isset($_GET['pdf']) && $_GET['pdf'] == 'cross') {
    $resunit = eF_getTableData("content", "name,lessons_ID", "id=".$_GET['view_list']);
    $reslesson = eF_getTableData("lessons", "name", "id=".$resunit[0]['lessons_ID']);
    $res = eF_getTableData("module_crossword_words", "crosslists,options", "content_ID=".$_GET['view_list']);
    $reswords = unserialize($res[0]['crosslists']);
    $maxwords = unserialize($res[0]['options']);
    $maxwords1 = $maxwords['max_word'];
    $_SESSION['CROSSWORD_MAXWORD']=$maxwords1;

    require_once('init.php');
    $rowquesans = "";
    foreach($reswords as $rowques){
     $rowquesans .= $rowques.",";
    }
    $quesids = mb_substr($rowquesans,0,-1);
    $quesans = eF_getTableData("questions","text,answer","id IN($quesids) order by rand() limit $maxwords1");
    $value = array();
    foreach($quesans as $row){
     $answer = unserialize($row['answer']);
     $answer1 = explode("|",$answer['0']);
     $value[]= array('ANSWER'=>$answer1['0'],'QUESTION'=>$row['text']);
    }
    $success = $pc->generateFromWords($value);
    if(!$success){
     $message_type = 'failure';
     $message = _CROSSWORD_UNABLEGENERATECROSSWORD;
     eF_redirect($this -> moduleBaseUrl."&message=".urlencode($message)."&message_type=".$message_type);
    }else{
     $currentlesson = $reslesson[0]['name'];

     $words = $pc->getWords();
     $answor = array();
     $html1 = array();
     $html2 = array();
     $html1[] = $currentlesson;
     $html1[] .= $resunit[0]['name'];
     $html1[] .= _CROSSWORD_ACROSS;
     $html2[] = _CROSSWORD_DOWN;
     $k=1;
  //pr($words);		
     foreach($words as $row){
      if($row['axis']==1){
       $html1[] .= $k.'. '.$row['question'];
      }else{
       $html2[] .= $k.'. '.$row['question'];
      }
      $k++;

     }
  //pr($html1);
  //pr($html2);
  //exit;			
     $answor[] = array_merge($html1,$html2);
   //pr($answor); exit;

     $dd = $pc->getHTML($answor);
     exit;
    }

   }
  }
  return true;
 }

 public function getSmartyTpl(){
  $smarty = $this -> getSmartyVar();
  $smarty -> assign("T_MODULE_CROSSWORD_BASEDIR" , $this -> moduleBaseDir);
  $smarty -> assign("T_MODULE_CROSSWORD_BASEURL" , $this -> moduleBaseUrl);
  $smarty -> assign("T_MODULE_CROSSWORD_BASELINK", $this -> moduleBaseLink);
  return $this -> moduleBaseDir . "module.tpl";
 }

 public function getLessonCenterLinkInfo() {
  $currentUser = $this -> getCurrentUser();
  return array('title' => _CROSSWORD_CROSSWORD,
                         'image' => $this -> moduleBaseDir.'images/crossword32.png',
                         'link' => $this -> moduleBaseUrl);
 }

 public function getSidebarLinkInfo() {

  $currentUser = $this -> getCurrentUser();
  $link_of_menu_system = array (array ('id' => 'crossword_link_id1',
                                               'title' => _CROSSWORD_CROSSWORD,
                                               'image' => $this -> moduleBaseDir.'images/crossword16',
                                               'eFrontExtensions' => '1',
                                               'link' => $this -> moduleBaseUrl));

  return array ("current_lesson" => $link_of_menu_system);
 }

 public function getNavigationLinks() {
  $currentUser = $this -> getCurrentUser();
  $currentLesson = $this -> getCurrentLesson();
  if (isset($_GET['view_list'])){
   $res = eF_getTableData("content","name","id=".$_GET['view_list']);
   return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
   array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
   array ('title' => _CROSSWORD_CROSSWORD, 'link' => $this -> moduleBaseUrl),
   array ('title' => $res[0]['name'], 'link' => $this -> moduleBaseUrl."&view_list=".$_GET['view_list']));
  } else{
   return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
   array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($currentLesson).".php?ctg=control_panel"),
   array ('title' => _CROSSWORD_CROSSWORD, 'link' => $this -> moduleBaseUrl));
  }

 }

 public function getLinkToHighlight() {
  return 'crossword_link_id1';
 }

 public function onInstall() {
  eF_executeNew("drop table if exists module_crossword_words");
  $res1 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_crossword_words` (
        `content_ID` int(10) unsigned NOT NULL,
        `crosslists` text,
        `options` text
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

  eF_executeNew("drop table if exists module_crossword_users");
  $res2 = eF_executeNew("CREATE TABLE `module_crossword_users` (
        `users_LOGIN` VARCHAR( 100 ) NOT NULL ,
        `content_ID` MEDIUMINT( 11 ) NOT NULL ,
        `crosslists_ID` MEDIUMINT( 11 ) NOT NULL default 0,
        `success` MEDIUMINT( 11 ) NOT NULL DEFAULT '0',
        `points` VARCHAR( 50 ) NOT NULL,
        `totallength` VARCHAR( 50 ) NOT NULL,
        `wordtime` VARCHAR( 50 ) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

  eF_executeNew("drop table if exists words");
  $res3 = eF_executeNew("CREATE TABLE IF NOT EXISTS `words` (
    `groupid` varchar(10) collate utf8_general_ci NOT NULL default '''lt''',
    `word` varchar(20) collate utf8_general_ci NOT NULL default '',
    `question` text collate utf8_general_ci NOT NULL,
    PRIMARY KEY (`word`,`groupid`),
    KEY `groupid` (`groupid`),
    FULLTEXT KEY `word_3` (`word`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  return ($res1 && $res2 && $res3);
 }


 public function onUninstall() {
  $res1 = eF_executeNew("DROP TABLE module_crossword_users;");
  $res2 = eF_executeNew("DROP TABLE module_crossword_words;");
  $res3 = eF_executeNew("DROP TABLE words;");
  return ($res1 && $res2 && $res3 && $res4);
 }

 public function getModuleCSS (){
  return $this->moduleBaseDir.'css/base.css';
 }


 public function isLessonModule() {
  return true;
 }
}
?>
