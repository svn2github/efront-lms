<?php

class module_lessonstats extends EfrontModule {

    // Mandatory functions required for module function
    public function getName() {
        return _LESSONSTATS;
    }

    public function getPermittedRoles() {
        return array("professor", "student");
    }

    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "professor") {
            return array('title' => _LESSONSTATS,
                     'image' => $this -> moduleBaseDir . 'images/stats30.png',
                     'link'  => $this -> moduleBaseUrl);
        }
    }

    public function isLessonModule() {
      return true;
    }
    
    public function getSidebarLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "professor") {

            $link_of_menu_clesson = array (array ('id' => 'other_link_id1',
                                                  'title' => _LESSONSTATS,
                                                  'image' => $this -> moduleBaseDir . 'images/stats16',
                                                  'eFrontExtensions' => '1',
                                                  'link'  => $this -> moduleBaseUrl));

            return array ( "current_lesson" => $link_of_menu_clesson);
        } else if ($currentUser -> getType() == "student"){
            $link_of_menu_clesson = array (array ('title' => _LESSONSTATS,
                                                 'image' => $this -> moduleBaseDir . 'images/stats16',
                                                 'eFrontExtensions' => '1',
                                                 'link'  => $this -> moduleBaseUrl));

            return array ( "current_lesson" => $link_of_menu_clesson);
        }
    }

    public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();
		$currentLesson = $this -> getCurrentLesson();
		
        return array (	array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
						array ('title' => $currentLesson -> lesson['name'], 'link'  => $currentUser -> getType() . ".php?ctg=control_panel"),
						array ('title' => _LESSONSTATS, 'link'  => $this -> moduleBaseUrl));
    }

    /* MAIN-INDEPENDENT MODULE PAGES */
    public function getModule() {
        $smarty = $this -> getSmartyVar();
        $currentLesson = $this -> getCurrentLesson();
        $inner_table_options = array(array('text' => _LESSONSTATS,  
         'image' => $this -> moduleBaseDir . 'images/stats16', 'href' => $this -> moduleBaseUrl));
                $currentLesson = $this -> getCurrentLesson();
        $data = ef_getTableData("logs", "*", "action in ('login', 'logout', 'lesson')", "id desc");
        $cnt = 0;
        $logins = array();
        for ($i = 0; ( ($i < sizeof($data)) && $cnt < 20); $i++){
            if ( ($data[$i]['action'] == 'lesson') && $data[$i]['lessons_ID'] == $currentLesson -> lesson['id'])
            {
                $logins[$cnt] = array();
                $logins[$cnt]['users_LOGIN'] = $data[$i]['users_LOGIN'];
                $logins[$cnt]['timestamp'] = $data[$i]['timestamp'];
                $logins[$cnt]['log_id'] = $data[$i]['id'];
                $logins[$cnt]['data_index'] = $i;
                $logins[$cnt]['time']['hours'] = 0;
                $logins[$cnt]['time']['minutes'] = 0;
                $logins[$cnt]['time']['seconds'] = 0;
                $cnt++;
            }
        }
        
        for ($i = 0; $i < sizeof($logins); $i++){
            //find the total login time for each login
            $time_diff = 0;
            for ($j = ($logins[$i]['data_index'] - 1); $j >= 0; $j--)
            {
                if ($data[$j]['action'] == 'login' || $data[$j]['action'] == 'logout' || $data[$j]['action'] == 'lesson'){
                    $time_diff = $data[$j]['timestamp'] - $logins[$i]['timestamp'];
                    if ($time_diff > 3600){
                        $time_diff = 3600;
                    }                    
                    $logins[$i]['time'] = eF_convertIntervalToTime($time_diff);
                    break;
                }
            }
        }
        $smarty -> assign("T_USERLOGINS", $logins);
        return true;
    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_LESSONSTATS_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_LESSONSTATS_BASEURL", $this -> moduleBaseUrl);
		$smarty -> assign("T_LESSONSTATS_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
    }

    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getLessonModule() {
        $smarty = $this -> getSmartyVar();
        $currentLesson = $this -> getCurrentLesson();
        $inner_table_options = array(array('text' => _LESSONSTATS_GOTOLESSONSTATSPAGE,   
        'image' => $this -> moduleBaseLink."images/redo.png", 'href' => $this -> moduleBaseUrl));
        $currentLesson = $this -> getCurrentLesson();
        $data = ef_getTableData("logs", "*", "action in ('login', 'logout', 'lesson')", "id desc");
        $cnt = 0;
        $logins = array();
        for ($i = 0; ( ($i < sizeof($data)) && $cnt < 5); $i++){
            if ( ($data[$i]['action'] == 'lesson') && $data[$i]['lessons_ID'] == $currentLesson -> lesson['id'])
            {
                $logins[$cnt] = array();
                $logins[$cnt]['users_LOGIN'] = $data[$i]['users_LOGIN'];
                $logins[$cnt]['timestamp'] = $data[$i]['timestamp'];
                $logins[$cnt]['log_id'] = $data[$i]['id'];
                $logins[$cnt]['data_index'] = $i;
                $logins[$cnt]['time']['hours'] = 0;
                $logins[$cnt]['time']['minutes'] = 0;
                $logins[$cnt]['time']['seconds'] = 0;
                $cnt++;
            }
        }
        
        for ($i = 0; $i < sizeof($logins); $i++){
            //find the total login time for each login
            $time_diff = 0;
            for ($j = ($logins[$i]['data_index'] - 1); $j >= 0; $j--)
            {
                if ($data[$j]['action'] == 'login' || $data[$j]['action'] == 'logout' || $data[$j]['action'] == 'lesson'){
                    $time_diff = $data[$j]['timestamp'] - $logins[$i]['timestamp'];
                    if ($time_diff > 3600){
                        $time_diff = 3600;
                    }                    
                    $logins[$i]['time'] = eF_convertIntervalToTime($time_diff);
                    break;
                }
            }
        }
        $smarty -> assign("T_USERLOGINS", $logins);
        $smarty -> assign("T_LESSONSTATS_INNERTABLE_OPTIONS", $inner_table_options);
        return true;
    }


    public function getLessonSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_LESSONSTATS_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_LESSONSTATS_BASEURL" , $this -> moduleBaseUrl);
		$smarty -> assign("T_LESSONSTATS_BASELINK" , $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }
}
?>