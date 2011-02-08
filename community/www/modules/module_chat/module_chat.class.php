

<?php

include_once ("../PEAR/Spreadsheet/Excel/Writer.php");

class module_chat extends eFrontModule{


 public function getName() {
  return "Chat Module";
 }

 public function getPermittedRoles() {
   return array("administrator", "professor", "student");
 }

    public function getModuleJS() {
  if (strpos(decryptUrl($_SERVER['REQUEST_URI']), $this -> moduleBaseUrl) !== false) {
   return $this->moduleBaseDir."js/admin.js";
  }
    }


 public function onInstall(){

  eF_executeNew("drop table if exists module_chat");
  $res1 = eF_executeNew("CREATE TABLE module_chat (
       id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
       from_user VARCHAR(255) NOT NULL DEFAULT '',
       to_user VARCHAR(255) NOT NULL DEFAULT '',
       message TEXT NOT NULL,
       sent DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
       isLesson INTEGER UNSIGNED NOT NULL DEFAULT 0,
       PRIMARY KEY (id)
       )"
       );

  eF_executeNew("drop table if exists module_chat_users");
  $res2 = eF_executeNew("CREATE TABLE module_chat_users (username VARCHAR(100) NOT NULL,
       timestamp_ TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       UNIQUE (username)
       )"
       );

  eF_executeNew("drop table if exists module_chat_config");
  $res3 = eF_executeNew("CREATE TABLE module_chat_config (status INT NOT NULL DEFAULT  '1',
       chatHeartbeatTime INT NOT NULL DEFAULT '1500',
       refresh_rate INT NOT NULL DEFAULT '60000'
       )"
       );
  $res4 = eF_executeNew("INSERT INTO  module_chat_config
       (status, chatHeartbeatTime, refresh_rate) VALUES
       ('1', '2000', '30000')"
       );


  return ($res1 && $res2 && $res3 &&res4);
 }

 public function onUninstall() {
            $res1 = eF_executeNew("DROP TABLE module_chat;");
   $res2 = eF_executeNew("DROP TABLE module_chat_users;");
   $res3 = eF_executeNew("DROP TABLE module_chat_config;");

   return ($res1 && $res2 && $res3);
    }

 public function getCenterLinkInfo() {
        $optionArray = array('title' => 'Chat',
                             'image' => $this -> moduleBaseDir.'img/chat.png',
                             'link' => $this -> moduleBaseUrl);
        $centerLinkInfo = $optionArray;

        return $centerLinkInfo;
    }

 public function getModule(){
  return true;
 }

 /*public function getSidebarLinkInfo () {

	//echo("holaaaaaaa");

        $currentUser = $this -> getCurrentUser();

    	// professors should see a link in the lessons menu



			$link_of_menu_lessons = array (

                              'id' => 'chat_module1',

                              'title' => "chat module",

                              'image' => $this -> moduleBaseLink . 'img/16x16/chat',

                              'eFrontExtensions' => '1',      //no extension provided up

                              'link'  => $this -> moduleBaseUrl

							  );



           return array ('tools' => array ('links'=>$link_of_menu_lessons));



// and admins should see a link in the users menu and in a newly defined menu



}

	*/
 // Get module css
    public function getModuleCSS() {
        return $this->moduleBaseDir."css/screen.css";
    }
 private function calculateCommonality($user){
  $currentUserLessons = array();
  $commonality = array();
  $common_lessons = array();
  $all_users = array();
  $users_lessons ;
  $result = eF_executeNew ("SELECT lessons_ID FROM users_to_lessons where archive=0 and users_LOGIN='$user'");
  foreach ($result as $value) {
   $currentUserLessons[] = ($value["lessons_ID"]);
  }
  $result = eF_executeNew ("SELECT login FROM users");
  foreach ($result as $value) {
   if ($value["login"] != $user){
    $all_users[] = ($value["login"]);
    $rate = 0;
    $result2 = eF_executeNew ("SELECT lessons_ID FROM users_to_lessons WHERE archive=0 and users_LOGIN='".$value['login']."'");
    foreach ($result2 as $value2){
     $users_lessons[] = $value2["lessons_ID"];
    }
    $common_lessons[$value["login"]] = array_intersect($users_lessons, $currentUserLessons);
    $rate = sizeof($common_lessons[$value["login"]]);
    $commonality[$value["login"]] = $rate;
    unset($users_lessons); // unset array for the next user
   }
  }
  $_SESSION['commonality'] = $commonality;
 }
 public function curPageURL() {
  $pageURL = 'http';
  if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
   $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
   $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  }
  else {
   $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  return $pageURL;
 }
 public function addScripts() {
  return array("scriptaculous/effects",
      "scriptaculous/controls");
 }

 //public function getSmartyTpl() {
 public function onPageFinishLoadingSmartyTpl() {

  if (!isset($_SESSION['lesson_rooms']))
   $_SESSION['lesson_rooms'] = array();

  $smarty = $this -> getSmartyVar();

  $mainScripts = array_merge(array('../modules/module_chat/js/jquery', '../modules/module_chat/js/chat'),getMainScripts());
  $smarty -> assign("T_HEADER_MAIN_SCRIPTS", implode(",", $mainScripts));

  $page = $this->curPageURL();

  if ($this->contains($page,"popup=1")){
   $smarty -> assign("T_CHAT_MODULE_STATUS", "OFF");
  }
  else{
   $smarty -> assign("T_CHAT_MODULE_STATUS", "ON");
  }

  if (!$_SESSION['chatter']){
   $currentUser = $this -> getCurrentUser();
   $_SESSION['chatter'] = $currentUser -> login;
   $_SESSION['utype'] = $currentUser -> getType();
   $this -> calculateCommonality($currentUser -> login);
   eF_executeNew("INSERT IGNORE INTO module_chat_users (username ,timestamp_) VALUES ('".$_SESSION['chatter']."', CURRENT_TIMESTAMP);");
  }
  else{
   $currentUser = $this -> getCurrentUser();
   if ($_SESSION['chatter'] != $currentUser -> login){
    $_SESSION['chatter'] = $currentUser -> login;
    $_SESSION['utype'] = $currentUser -> getType();
    $this -> calculateCommonality($currentUser -> login);
    eF_executeNew("INSERT IGNORE INTO module_chat_users (username ,timestamp_) VALUES ('".$_SESSION['chatter']."', CURRENT_TIMESTAMP);");
   }
  }

        $smarty -> assign("T_CHAT_MODULE_BASEURL", $this -> moduleBaseUrl);
        $smarty -> assign("T_CHAT_MODULE_BASELINK", $this -> moduleBaseLink);
  $smarty -> assign("T_CHAT_MODULE_BASEDIR", $this -> moduleBaseDir);

  $onlineUsers = EfrontUser :: getUsersOnline();


  $smarty -> assign("T_CHAT_MODULE_ONLINEUSERS", $onlineUsers);

  return $this -> moduleBaseDir . "module_chat.tpl";
 }

 public function getSmartyTpl() {

  $smarty = $this -> getSmartyVar();

  $smarty->assign('T_CHAT_ERROR_RATE', "");
  $smarty->assign('T_CHAT_ERROR2_RATE', "");

   if (isset($_POST['rate']) && isset($_POST['rate2'])){

    $ok = true;
    if ($_POST['rate'] < 1){
     $smarty->assign('T_CHAT_ERROR_RATE', " New Rate must be greater or equal to 1.");
     $ok = false;
    }
    if ($_POST['rate2'] < 1){
     $smarty->assign('T_CHAT_ERROR2_RATE', " New Rate must be greater or equal to 1.");
     $ok = false;
    }

    if ($ok){
     $this -> setChatHeartbeat($_POST['rate']*1000);
     $this -> setRefresh_rate($_POST['rate2']*1000);
    }
   }

   $r = $this->getChatHeartbeat();
   $r2 = $this->getRefresh_rate();

   $smarty->assign('T_CHAT_CURRENT_RATE', $r/1000);


   $form = new HTML_QuickForm("change_chatheartbeat_form", "post", $this->moduleBaseUrl."&setChatHeartBeat=1", "", null, true);
   $form->addElement('text', 'rate', "rate", 'class="inputText" value="'.($r/1000).'" style="width:100px;"');
   $form->addRule('rate', _THEFIELD.' "Rate" '._ISMANDATORY, 'required', null, 'client');
   $form->addRule('rate', "Non numeric Value", 'numeric', null, 'client');
   $form->addRule('rate', "Rate must be greater than 1", 'callback', create_function('$rate', 'return ($rate >= 1);'));

   $form->addElement('text', 'rate2', "rate2", 'class="inputText" value="'.($r2/1000).'" style="width:100px;"');
   $form->addRule('rate2', _THEFIELD.' "Rate" '._ISMANDATORY, 'required', null, 'client');
   $form->addRule('rate2', "Non numeric Value", 'numeric', null, 'client');

   $form->addElement('submit', 'submit1', _SUBMIT, 'class="flatButton"');


   $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
   $form->setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
   $form->setRequiredNote("mesh");
   $form->accept($renderer);
   $smarty->assign('T_CHAT_CHANGE_CHATHEARTBEAT_FORM', $renderer->toArray());

   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

   /*$smarty->assign('T_CHAT_ERROR2_RATE', "");

			

			if (isset($_POST['rate2'])){

				if ($_POST['rate2'] >= 1)

					$this -> setRefresh_rate($_POST['rate2']*1000);

				else

					$smarty->assign('T_CHAT_ERROR2_RATE', " New Rate must be greater or equal to 1.");

			}



			$r2 = $this->getRefresh_rate();



			$smarty->assign('T_CHAT_CURRENT_REFRESH_RATE', $r2/1000);



			$form = new HTML_QuickForm("change_refreshrate_form", "post", $this->moduleBaseUrl."&setRefresh_rate=1", "", null, true);

			$form->addElement('text', 'rate2', "rate2", 'class="inputText" value="'.($r2/1000).'" style="width:100px;"');

			$form->addRule('rate2', _THEFIELD.' "New Rate" '._ISMANDATORY, 'required', null, 'client');

			$form->addRule('rate2', "Non numeric Value", 'numeric', null, 'client');

			$form->addElement('submit', 'submit2', _SUBMIT, 'class="flatButton"');

			$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

			$form->setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);

			$form->setRequiredNote("mesh");

			$form->accept($renderer);

			$smarty->assign('T_CHAT_CHANGE_REFRESHRATE_FORM', $renderer->toArray());*/
   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   //$lessons = $this -> getLessonsCatalogue();
   //$smarty->assign('T_CHAT_LESSONS', $lessons);
   $textfieldcontent = "";
   if (isset($_POST['lessontitle'])){
    $textfieldcontent = $_POST['lessontitle'];
    $l = strip_tags($_POST['lessontitle']);
    $l2 = substr($l, strpos($l, '→')+5);
    $log = $this->createLessonHistory($l2,
           $_POST['from']['Y'].'-'.$_POST['from']['M'].'-'.$_POST['from']['d'].' '."00:00:00" ,
           $_POST['until']['Y'].'-'.$_POST['until']['M'].'-'.$_POST['until']['d'].' '."23:59:59"
           );
    $smarty->assign('T_LOG', $log);
    $smarty->assign('T_CHAT_LESSON_TITLE', $l2);
   }
   $form = new HTML_QuickForm("create_log_form", "post", $this->moduleBaseUrl."&createLog=1", "", null, true);
   $date_from = $form->addElement('date', 'from', 'From Date:', array('format' => 'dMY', 'minYear' => 2010, 'maxYear' => date('Y')));
   $date_until = $form->addElement('date', 'until', 'Until Date:', array('format' => 'dMY', 'minYear' => 2010, 'maxYear' => date('Y')));
   $form->addElement('text', 'lessontitle', "lessontitle", 'maxlength="100" size="100" class="autoCompleteTextBox" id="autocomplete" value="'.$textfieldcontent.'"');
   $form->addRule('lessontitle', _THEFIELD.' "Lesson Title" '._ISMANDATORY, 'required', null, 'client');
   $week_ago = $this->subtractDaysFromToday(7);
   $form->setDefaults(array('until' => array('d' => date('d'), 'M' => date('m'), 'Y' => date('Y')),
          'from' => $week_ago
          ));
   $form->addElement('submit', 'submit', "Create Log", 'class="flatButton"');
   $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
   $form->setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
   $form->setRequiredNote("mesh");
   $form->accept($renderer);
   $smarty->assign('T_CHAT_CREATE_LOG_FORM', $renderer->toArray());
////////
  return $this -> moduleBaseDir . "control_panel.tpl";
 }
 public function getNavigationLinks() {
  $currentUser = $this -> getCurrentUser();
        /*if ($currentUser -> getType() == 'administrator') {

			if (isset($_GET['setChatHeartBeat'])){

				return array (array ('title' => _HOME, 'link'  => $currentUser -> getType() . ".php?ctg=control_panel"),

								array ('title' => "Chat Module", 'link'  => $this -> moduleBaseUrl),

								array ('title' => "Chat Engine Rate", 'link' => ($this -> moduleBaseUrl)."&setChatHeartBeat=1"));

			}

			else if (isset($_GET['setRefresh_rate'])){

				return array (array ('title' => _HOME, 'link'  => $currentUser -> getType() . ".php?ctg=control_panel"),

								array ('title' => "Chat Module", 'link'  => $this -> moduleBaseUrl),

								array ('title' => "User List Refresh Rate", 'link' => ($this -> moduleBaseUrl)."&setRefresh_rate=1"));

			}

			else if (isset($_GET['createLog'])){

				return array (array ('title' => _HOME, 'link'  => $currentUser -> getType() . ".php?ctg=control_panel"),

								array ('title' => "Chat Module", 'link'  => $this -> moduleBaseUrl),

								array ('title' => "Create History Log", 'link' => ($this -> moduleBaseUrl)."&createLog=1"));

			}

			else{*/
             return array (array ('title' => _HOME, 'link' => $currentUser -> getType() . ".php?ctg=control_panel"),
        array ('title' => "Chat Module", 'link' => $this -> moduleBaseUrl));
   //}
        //}
 }
 public function checkRate($rate){
  if ($rate<1)
   return false;
  return true;
 }
 private function contains($str, $content){
  $str = strtolower($str);
  $content = strtolower($content);
  if (strpos($str,$content))
   return true;
  else
   return false;
 }
 private function getChatHeartbeat(){
  $rate = eF_getTableData("module_chat_config", "chatHeartbeatTime", "1");
  foreach( $rate as $r ){
   return $r['chatHeartbeatTime'];
  }
 }
 private function getRefresh_rate(){
  $rate = eF_getTableData("module_chat_config", "refresh_rate", "1");
  foreach( $rate as $r ){
   return $r['refresh_rate'];
  }
 }
 private function setChatheartBeat($rate){
  $sql = "update module_chat_config set chatHeartbeatTime = '".$rate."' where 1";
  $query = mysql_query($sql);
 }
 private function setRefresh_rate($rate){
  $sql = "update module_chat_config set refresh_rate = '".$rate."' where 1";
  $query = mysql_query($sql);
 }
 private function getLessonsCatalogue(){
  $lsn = eF_getTableData("lessons", "name", "1");
  $lessons = array();
  foreach ($lsn as $lesson){
   $lessons[] = $lesson['name'];
  }
  return $lessons;
 }
 private function createLessonHistory($lesson, $from, $until){
  $lesson = str_replace(' ','_',$lesson);
  //if (time() > strtotime($from)){
  //	$sql = "select * from module_chat where (module_chat.to_user = '".$lesson."') order by id ASC";
  //}
  //else{
   $sql = "select * from module_chat where (module_chat.to_user = '".$lesson."' AND module_chat.sent >='".$from."' AND module_chat.sent <= '".$until."') order by id ASC";
  //}
  $query = mysql_query($sql);
  /*$data = array();

		$workbook = new Spreadsheet_Excel_Writer();

		$workbook->setVersion(8);





		$worksheet =& $workbook->addWorksheet($lesson.' ');

		$worksheet->setInputEncoding('utf-8');

		$worksheet->setColumn(1,1,50);

		$worksheet->setColumn(0,0,15);

		$worksheet->setColumn(2,2,18);



		$format_title =& $workbook->addFormat();

		$format_title->setBold();

		$format_title->setAlign('center');

		$format_title->setFgColor('000000');

		$format_title->setBgColor('000000');

		$format_title->setColor('white');

		$format_title->setPattern(1);



		$multipleLineDataFormat = &$workbook->addFormat( array('Border'=> 1, 'Align' => 'left' ) );

		$multipleLineDataFormat->setTextWrap();



		$format_user =& $workbook->addFormat();

		$format_user->setAlign('center');

		$format_user->setBorder(1);



		$format_date = $workbook->addFormat();

		$format_date->setBorder(1);





		$worksheet->write(0, 0, 'FROM USER', $format_title);

		$worksheet->write(0, 1, 'MESSAGE', $format_title);

		$worksheet->write(0, 2, 'SENT AT', $format_title);

		*/
  $i = 1;
  $log = "<br>"."<table class=\"sortedTable\" width=\"100%\">";
  $log .= "<tr><td class = \"topTitle\">From</td><td class = \"topTitle alignCenter\">Message</td><td class = \"topTitle\">Date/Time<td></tr>";
  while ($chat = mysql_fetch_array($query)) {
   /*$worksheet->write($i, 0, $chat["from_user"], $format_user);

			$worksheet->write($i, 1, $chat["message"], $multipleLineDataFormat);

			$worksheet->write($i, 2, $chat["sent"], $format_date);*/
   if ($i%2==0)
    $log .= "<tr class=\"oddRowColor\"><td class=\"sender\">".$chat["from_user"].":</td><td class=\"alignCenter chatmsg\">".$chat["message"]."</td><td class=\"alignLeft date\">".$chat["sent"]."</td></td>";
   else
    $log .= "<tr class=\"evenRowColor\"><td class=\"sender\">".$chat["from_user"].":</td><td class=\"alignCenter chatmsg\">".$chat["message"]."</td><td class=\"alignLeft date\">".$chat["sent"]."</td></td>";
   $i++;
  }
  $log.= "</table>";
  return $log;
  //$workbook->send($lesson);
  //$workbook->close();
 }
 function subtractDaysFromToday($number_of_days)
 {
     $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
     $subtract = $today - (86400 * $number_of_days);
     //choice a date format here
     return date("d-M-Y", $subtract);
 }
}
?>
