<?php


class module_chat extends eFrontModule{


 public function getName() {
  return "Chat Module";
 }

 public function getPermittedRoles() {
   return array("administrator", "professor", "student");
 }

 public function onInstall(){

  eF_executeNew("drop table if exists module_chat");
  $res1 = eF_executeNew("CREATE TABLE module_chat (
       id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
       from_user VARCHAR(255) NOT NULL DEFAULT '',
       to_user VARCHAR(255) NOT NULL DEFAULT '',
       message TEXT NOT NULL,
       sent DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
       recd INTEGER UNSIGNED NOT NULL DEFAULT 0,
       PRIMARY KEY (id)
       )"
       );

  eF_executeNew("drop table if exists module_chat_users");
  $res2 = eF_executeNew("CREATE TABLE module_chat_users (username VARCHAR(100) NOT NULL,
       timestamp_ TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       UNIQUE (username)
       )"
       );
  return ($res1 && $res2);
 }

 public function onUninstall() {
            $res1 = eF_executeNew("DROP TABLE module_chat;");
   $res2 = eF_executeNew("DROP TABLE module_chat_users;");

   return ($res1 && $res2);
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
/*	// Get module javascript code

	public function getModuleJs() {

  		return $this->moduleBaseDir."js/jquery.js";

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
    //print_r($common_lessons);
    //echo ('</br>-'.$rate.'-</br>');
    //unset($commonality);
    unset($users_lessons); // unset array for the next user
   }
  }
  $_SESSION['commonality'] = $commonality;
  //print_r($_SESSION['commonality']);
 }
 //public function getSmartyTpl() {
 public function onPageFinishLoadingSmartyTpl() {
  $smarty = $this -> getSmartyVar();
  if (!$_SESSION['chatter']){
   $currentUser = $this -> getCurrentUser();
   $_SESSION['chatter'] = $currentUser -> login;
   $_SESSION['utype'] = $currentUser -> getType();
   $this -> calculateCommonality($currentUser -> login);
   eF_executeNew("INSERT IGNORE INTO module_chat_users (username ,timestamp_) VALUES ('".$_SESSION['chatter']."', CURRENT_TIMESTAMP);");
  }
        $smarty -> assign("T_CHAT_MODULE_BASEURL", $this -> moduleBaseUrl);
        $smarty -> assign("T_CHAT_MODULE_BASELINK", $this -> moduleBaseLink);
  $smarty -> assign("T_CHAT_MODULE_BASEDIR", $this -> moduleBaseDir);
  $onlineUsers = EfrontUser :: getUsersOnline();
  //$onlineUsers[] = $onlineUsers['login'];
  //echo($onlineUsers[0]['login']." ".count($onlineUsers));
  $smarty -> assign("T_CHAT_MODULE_ONLINEUSERS", $onlineUsers);
  return $this -> moduleBaseDir . "module_chat.tpl";
 }
}
?>
