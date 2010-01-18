<?php

class module_dimdim extends EfrontModule {


    // Mandatory functions required for module function
    public function getName() {
        return _DIMDIM;
    }

    public function getPermittedRoles() {
        return array("administrator","professor","student");
    }

    public function isLessonModule() {
        return true;
    }

    // Optional functions
    // What should happen on installing the module
    public function onInstall() {
        eF_executeNew("drop table if exists module_dimdim");
        $a = eF_executeNew("CREATE TABLE module_dimdim (
                          id int(11) NOT NULL auto_increment,
                          name varchar(255) NOT NULL,
                          timestamp int(11) NOT NULL,
                          lessons_ID int(11) NOT NULL,
                          confKey varchar(255) NOT NULL,
                          durationHours int(1) NOT NULL,
                          durationMinutes int(2),
                          confType tinyint(1) default 0,
                          maxParts int(3) default 20,
                          maxMics int(3) default 20,
                          lobby tinyint(1) default 0,
                          status int(1) default 0,
                          PRIMARY KEY  (id)
                        ) DEFAULT CHARSET=utf8;");
        eF_executeNew("drop table if exists module_dimdim_users_to_meeting ");
        $b = eF_executeNew("CREATE TABLE module_dimdim_users_to_meeting (
                        users_LOGIN varchar(255) NOT NULL,
                        meeting_ID int(11) NOT NULL,
                        KEY (users_LOGIN,meeting_ID)
                       ) DEFAULT CHARSET=utf8;");


        if (!($c = eF_executeNew("INSERT INTO configuration VALUES ('module_dimdim_server','http://www1.dimdim.com');"))) {
            $c = eF_executeNew("UPDATE configuration SET value = 'http://www1.dimdim.com' WHERE name = 'module_dimdim_server';");
        }

        return $a && $b && $c;
    }

    // And on deleting the module
    public function onUninstall() {
        $a = eF_executeNew("DROP TABLE module_dimdim;");
        $b = eF_executeNew("DROP TABLE module_dimdim_users_to_meeting;");
        $c = eF_executeNew("DELETE FROM configuration WHERE name='module_dimdim_server';");

        return $a && $b && $c;
    }

    // On exporting a lesson
    public function onDeleteLesson($lessonId) {
        $meetings_to_del = eF_getTableDataFlat("module_dimdim", "id","lessons_ID='".$lessonId."'");
        eF_deleteTableData("module_dimdim", "lessons_ID='".$lessonId."'");
        $delmeet = implode($meetings_to_del['id'],"','");
        eF_deleteTableData("module_dimdim_users_to_meeting", "meeting_ID IN ('".$delmeet ."')");

        return true;
    }

    // On exporting a lesson
    public function onExportLesson($lessonId) {
        $data = array();
        $data['meetings'] = eF_getTableData("module_dimdim", "*","lessons_ID=".$lessonId);
        $data['users_to_meetings'] = eF_getTableData("module_dimdim_users_to_meeting JOIN module_dimdim ON module_dimdim.id = module_dimdim_users_to_meeting.meeting_ID", "module_dimdim_users_to_meeting.*","lessons_ID=".$lessonId);
        return $data;
    }

    // On importing a lesson
    public function onImportLesson($lessonId, $data) {
        $changed_ids = array();

        foreach ($data['meetings'] as $meeting_record) {

            // Keep the old id
            $old_meeting_id = $meeting_record['id'];
            unset($meeting_record['id']);
            $meeting_record['lessons_ID'] = $lessonId;
            $new_meeting_id = eF_insertTableData("module_dimdim",$meeting_record);

            if ($new_meeting_id != $old_meeting_id) {
                $changed_ids[$old_meeting_id] = $new_meeting_id;
            }
        }

        foreach ($data['users_to_meetings'] as $users_to_meetings_record) {

            if (isset($changed_ids[$users_to_meetings_record['meeting_ID']])) {
                $users_to_meetings_record['meeting_ID'] = $changed_ids[$users_to_meetings_record['meeting_ID']];
            }
            eF_insertTableData("module_dimdim_users_to_meeting",$users_to_meetings_record);
        }
        return true;
    }

    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
            return array('title' => _DIMDIM,
                         'image' => $this -> moduleBaseDir . 'images/dimdim32.png',
                         'link'  => $this -> moduleBaseUrl);
        }
    }


    public function getCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "administrator") {
            return array('title' => _DIMDIM,
                         'image' => $this -> moduleBaseDir . 'images/dimdim32.png',
                         'link'  => $this -> moduleBaseUrl);
        }
    }

    public function getNavigationLinks() {

        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole() == "administrator") { 
            $basicNavArray = array (array ('title' => _HOME, 'link' => "administrator.php?ctg=control_panel"),
                                    array ('title' => _DIMDIM, 'link'  => $this -> moduleBaseUrl));            

        } else {
            $basicNavArray = array (
                                    array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($this -> getCurrentLesson()).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
                                    array ('title' => _HOME, 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
                                    array ('title' => _DIMDIM, 'link'  => $this -> moduleBaseUrl));
	        if (isset($_GET['edit_dimdim'])) {
	            $basicNavArray[] = array ('title' => _DIMDIM_MANAGEMENT, 'link'  => $this -> moduleBaseUrl . "&edit_dimdim=". $_GET['edit_dimdim']);
	        } else if (isset($_GET['add_dimdim'])) {
	            $basicNavArray[] = array ('title' => _DIMDIM_MANAGEMENT, 'link'  => $this -> moduleBaseUrl . "&add_dimdim=1");
	        }
        }
        return $basicNavArray;

    }

    public function getSidebarLinkInfo() {

        $link_of_menu_clesson = array (array ('id' => 'dimdim_link_id1',
                                              'title' => _DIMDIM,
                                              'image' => $this -> moduleBaseDir . 'images/dimdim16',
                                              'eFrontExtensions' => '1',
                                              'link'  => $this -> moduleBaseUrl));

        return array ( "current_lesson" => $link_of_menu_clesson);

    }

    public function getLinkToHighlight() {
        return 'dimdim_link_id1';
    }


    private $dimdim_server_host = false;
    private function getDimdimServer() {
        if (!$this -> dimdim_server_host) {
            $dimdim_server = eF_getTableData("configuration", "value", "name = 'module_dimdim_server'");
            $this -> dimdim_server_host = $dimdim_server[0]['value'];
        } 
            
        return $this -> dimdim_server_host;
    }

    /*
     * Function used to create the DimDim module URL
     * Parses the options stored for the meeting in the DB and retuns the correct
     * URL according to role of the user, whether the meeting has started or 
     * wheter
     */
    private function createDimdimUrl($currentUser, $meeting_info, $always_joining = false) {
        if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor" && $meeting_info['status'] == 0 && !$always_joining) {
            $meeting_options = array(
                "action"             => "host",
                "email"              => $currentUser -> user['email'],
                "meetingRoomName"    => $meeting_info['confKey'],
                "displayName"        => urlencode(utf8_decode($currentUser -> user['name']))."_". urlencode(utf8_decode($currentUser -> user['surname'])),
                "confName"           => $meeting_info['name'],
                "lobby"              => ($meeting_info['lobby'])?"false":"true",
                "networkProfile"     => 2,
                "meetingHours"       => $meeting_info['durationHours'],
                "meetingMinutes"     => $meeting_info['durationMinutes'],
                "maxParticipants"    => $meeting_info['maxParts'],
                "maxAttendeeMikes"   => $meeting_info['maxMics'],
                "presenterAV"        => ($meeting_info['confType'])?"audio":"av",
                "returnUrl"          => $this -> moduleBaseLink."module_dimdim_finished.php?finished_meeting=".$_GET['start_meeting'],
                "attendeePassCode"   => "atpwd_".$meeting_info['confKey']
            );


        } else {
            $meeting_options = array(
                "action"             => "join",
                "email"              => $currentUser -> user['email'],
                "meetingRoomName"    => $meeting_info['confKey'],
                "displayName"        => urlencode(utf8_decode($currentUser -> user['name']))."_". urlencode(utf8_decode($currentUser -> user['surname'])),
                "returnUrl"          => $this -> moduleBaseLink."module_dimdim_finished.php?finished_meeting=".$_GET['start_meeting'],
                "attendeePwd"        => "atpwd_".$meeting_info['confKey']
            );
        }

        $url = "";
        foreach ($meeting_options as $key => $option) {
            if ($url != "") {
                $url .= "&" . $key . "=" . str_replace(" ", "_", $option);
            } else {
                $url = $key . "="  . str_replace(" ", "_", $option);
            }
        }

        $dimdim_server = $this -> getDimdimServer();
        if($dimdim_server == "http://www1.dimdim.com" || $dimdim_server == "http://www1.dimdim.com/") {
            $server_host = "http://www1.dimdim.com/dimdim";
        } else {
            $server_host = $dimdim_server;

        }

        $dimdimUrl .= $server_host . "/html/envcheck/connect.action?".$url;
        
        return $dimdimUrl;
    }

    
    
    /* MAIN-INDEPENDENT MODULE PAGES */
    public function getModule() {
        $currentUser = $this -> getCurrentUser();
        // Get smarty global variable
        $smarty = $this -> getSmartyVar();

        $userRole = $currentUser -> getRole($this -> getCurrentLesson());

        if ($currentUser -> getType() == "administrator") {

            $form = new HTML_QuickForm("dimdim_server_entry_form", "post", $_SERVER['REQUEST_URI'], "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('text', 'server', null, 'class = "inputText" id="server_input"');
            $form -> addRule('server', _DIMDIMTHEFIELDNAMEISMANDATORY, 'required', null, 'client');
            $form -> addElement('submit', 'submit_dimdim_server', _SUBMIT, 'class = "flatButton"');

            if ($form -> isSubmitted() && $form -> validate()) {
                $server_name = $form -> exportValue('server');
                if ($server_name[strlen($server_name)-1] == "/") {
                    $server_name = substr($server_name, 0, strlen($server_name)-1);
                }
                eF_updateTableData("configuration", array("value" => $server_name), "name = 'module_dimdim_server'");
                $this -> setMessageVar(_DIMDIM_SUCCESFULLYCHANGEDSERVER, "success");
            }

            
            $form -> setDefaults(array('server'       => $this -> getDimdimServer()));


            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);

            $smarty -> assign('T_DIMDIM_FORM', $renderer -> toArray());
        }



        /*** Ajax Methods - Add/remove skills/jobs***/
        if (isset($_GET['postAjaxRequest'])) {
            /** Post skill - Ajax skill **/
            if ($_GET['insert'] == "true") {
                eF_insertTableData("module_dimdim_users_to_meeting", array('users_LOGIN' => $_GET['user'], 'meeting_ID' => $_GET['edit_dimdim']));
            } else if ($_GET['insert'] == "false") {
                eF_deleteTableData("module_dimdim_users_to_meeting", "users_LOGIN = '". $_GET['user'] . "' AND meeting_ID = '".$_GET['edit_dimdim']."'");
            } else if (isset($_GET['addAll'])) {
                $users = eF_getTableData("users JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN LEFT OUTER JOIN module_dimdim_users_to_meeting ON users.login = module_dimdim_users_to_meeting.users_LOGIN","users.login, users.name, users.surname, meeting_ID","users_to_lessons.lessons_ID = '".$_SESSION['s_lessons_ID']."' AND (meeting_ID <> '".$_GET['edit_dimdim']."' OR meeting_ID IS NULL)");

                $users_attending = eF_getTableDataFlat("users JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN LEFT OUTER JOIN module_dimdim_users_to_meeting ON users.login = module_dimdim_users_to_meeting.users_LOGIN","users.login","users_to_lessons.lessons_ID = '".$_SESSION['s_lessons_ID']."' AND meeting_ID = '".$_GET['edit_dimdim']."'");

                isset($_GET['filter']) ? $users = eF_filterData($users, $_GET['filter']) : null;
                $users_attending = $users_attending['login'];

                foreach ($users as $user) {
                    if (!in_array($user['login'], $users_attending)) {
                        eF_insertTableData("module_dimdim_users_to_meeting", array('users_LOGIN' => $user['login'], 'meeting_ID' => $_GET['edit_dimdim']));
                        $users_attending[] = $user['login'];
                    }
                }
            } else if (isset($_GET['removeAll'])) {
                $users_attending = eF_getTableData("users JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN LEFT OUTER JOIN module_dimdim_users_to_meeting ON users.login = module_dimdim_users_to_meeting.users_LOGIN","users.login","users_to_lessons.lessons_ID = '".$_SESSION['s_lessons_ID']."' AND meeting_ID = '".$_GET['edit_dimdim']."'");
                //$users_attending = $users_attending['login'];
                isset($_GET['filter']) ? $users_attending = eF_filterData($users_attending, $_GET['filter']) : null;

                $users_to_delete = array();
                foreach($users_attending as $user) {
                    $users_to_delete[] = $user['login'];
                }
                eF_deleteTableData("module_dimdim_users_to_meeting", "meeting_ID = '".$_GET['edit_dimdim']."' AND users_LOGIN IN ('".implode("','", $users_to_delete)."')");
            } else if (isset($_GET['mail_users']) && $_GET['mail_users'] == 1) {
                $currentLesson = $this ->getCurrentLesson();
                $meeting_users = eF_getTableData("module_dimdim_users_to_meeting JOIN users ON module_dimdim_users_to_meeting.users_LOGIN = users.login", "users.login, users.name, users.surname, users.email", "meeting_ID = ".$_GET['edit_dimdim'] . " AND users.login <> '". $currentUser -> user['login'] ."'");

                isset($_GET['filter']) ? $meeting_users  = eF_filterData($meeting_users , $_GET['filter']) : null;

                $meeting_info = eF_getTableData("module_dimdim", "*", "id = ".$_GET['edit_dimdim']);

                $subject = _DIMDIM_MEETING;
                $count = 0;
                foreach ($meeting_users as $user) {

                    $body = _DIMDIM_DEAR . " " . $user['name']. ",\n\n" ._DIMDIM_YOUHAVEBEENINVITEDBYPROFESSOR . " " . $currentUser -> user['name']. " " . $currentUser -> user['surname'] . " " . _DIMDIM_TOATTENDACONFERENCE . " \"". $meeting_info[0]['name'] . "\" " . _DIMDIM_FORLESSON. " \""  . $currentLesson -> lesson['name'] . "\" " . _DIMDIM_SCHEDULEDFOR . "\n\n". date("D d.m.y, g:i a", $meeting_info[0]['timestamp']). "\n\n" ._DIMDIMYOUCANJOINTHEMEETINGDIRECTLYBYCLICKINGTHEFOLLOWINGLINKAFTERITSTARTS . ":\n\n";
                    
                    $userObject = EfrontUserFactory::factory($user['login']);
                    
                    $body .= $this -> createDimdimUrl($userObject, $meeting_info[0], true);
                    $body .= "\n\n" ._DIMDIM_SINCERELY . ",\n" . $currentUser -> user['surname']." ".$currentUser -> user['name'];

                    $my_email = $currentUser -> user['email'];
                    $user_mail = $user['email'];
                    $header = array ('From'                      => $GLOBALS['configuration']['system_email'],
                                     'To'                        => $user_mail,
                                     'Subject'                   => $subject,
                                     'Content-type'              => 'text/plain;charset="UTF-8"',                       // if content-type is text/html, the message cannot be received by mail clients for Registration content
                                     'Content-Transfer-Encoding' => '7bit');
                    $smtp = Mail::factory('smtp', array('auth'      => $GLOBALS['configuration']['smtp_auth'] ? true : false,
                                                         'host'      => $GLOBALS['configuration']['smtp_host'],
                                                         'password'  => $GLOBALS['configuration']['smtp_pass'],
                                                         'port'      => $GLOBALS['configuration']['smtp_port'],
                                                         'username'  => $GLOBALS['configuration']['smtp_user'],
                                                         'timeout'   => $GLOBALS['configuration']['smtp_timeout']));

                    if ($smtp -> send($user_mail, $header, $body)) {
                        $count++;
                    }

                }
                echo $count;
                exit;
            }
        }

        // The form with all students clicked or not is posted


//pr($_GET);
        if (isset($_GET['start_meeting']) && eF_checkParameter($_GET['start_meeting'], 'id')) {
            
            $dimdim_server = $this -> getDimdimServer();
            if ($dimdim_server != "") {

                $dimdim = eF_getTableData("module_dimdim", "*", "id=".$_GET['start_meeting']);

                if ($dimdim[0]['status'] != 2) {

                    $dimdimUrl = $this -> createDimdimUrl($currentUser, $dimdim[0]);
                    if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor" && $meeting_info['status'] == 0) {
                        eF_updateTableData("module_dimdim", array('status' => '1'), "id=".$_GET['start_meeting']);
                    }
                    
                    //echo $dimdimUrl."<BR>";
                    header("location:".$dimdimUrl);

                } else {
                    $this -> setMessageVar(_DIMDIMMEETINGHASFINISHED, "failure");
                }
            } else {
                $this -> setMessageVar(_DIMDIM_NODIMDIMSERVERDEFINED, "failure");
            }
        }


        if (isset($_GET['finished_meeting']) && eF_checkParameter($_GET['finished_meeting'], 'id')) {
            if ($userRole == "professor") {
                eF_updateTableData("module_dimdim", array('status' => '2'), "id=".$_GET['finished_meeting']);
            }
            
            $currentLesson = $this -> getCurrentLesson();
            $_SESSION['previousSideUrl'] = G_SERVERNAME ."new_sidebar.php?new_lesson_id=" . $currentLesson -> lesson['id'] ;
            $_SESSION['previousMainUrl'] = G_SERVERNAME . $currentUser -> getType() . ".php?ctg=control_panel";
            header("location:". $currentUser -> getType() . "page.php");
        }

        if (isset($_GET['delete_dimdim']) && eF_checkParameter($_GET['delete_dimdim'], 'id') && $userRole == "professor") {
            eF_deleteTableData("module_dimdim", "id=".$_GET['delete_dimdim']);
            eF_deleteTableData("module_dimdim_users_to_meeting", "meeting_ID=".$_GET['delete_dimdim']);
            header("location:". $this -> moduleBaseUrl ."&message=".urlencode(_DIMDIM_SUCCESFULLYDELETEDDIMDIMENTRY)."&message_type=success");
        } else if ($userRole == "professor" && (isset($_GET['add_dimdim']) || (isset($_GET['edit_dimdim']) && eF_checkParameter($_GET['edit_dimdim'], 'id')))) {

            // Create ajax enabled table for meeting attendants
            if (isset($_GET['edit_dimdim'])) {
                if (isset($_GET['ajax']) && $_GET['ajax'] == 'dimdimUsersTable') {
                    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                        $sort = $_GET['sort'];
                        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                    } else {
                        $sort = 'login';
                    }

                    $users = eF_getTableData("users JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN
                                                    JOIN module_dimdim ON module_dimdim.lessons_ID = users_to_lessons.lessons_ID
                                                    LEFT OUTER JOIN module_dimdim_users_to_meeting ON module_dimdim.id = module_dimdim_users_to_meeting.meeting_ID AND users.login = module_dimdim_users_to_meeting.users_LOGIN",
                                                    "users.login, users.name, users.surname, users.email, meeting_ID",
                                                    "users_to_lessons.lessons_ID = '".$_SESSION['s_lessons_ID']."' AND users.login <> '".$currentUser -> user['login'] . "' AND module_dimdim.id = '".$_GET['edit_dimdim']."'");

                    $users = eF_multiSort($users, $_GET['sort'], $order);
                    if (isset($_GET['filter'])) {
                        $users = eF_filterData($users , $_GET['filter']);
                    }

                    $smarty -> assign("T_USERS_SIZE", sizeof($users));

                    if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                        isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                        $users = array_slice($users, $offset, $limit);
                    }

                    $smarty -> assign("T_USERS", $users);
                    $smarty -> display($this -> getSmartyTpl());
                    exit;

                } else {

                    $users = eF_getTableData("users JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN
                                                    JOIN module_dimdim ON module_dimdim.lessons_ID = users_to_lessons.lessons_ID
                                                    LEFT OUTER JOIN module_dimdim_users_to_meeting ON module_dimdim.id = module_dimdim_users_to_meeting.meeting_ID AND users.login = module_dimdim_users_to_meeting.users_LOGIN",
                                                    "users.login, users.name, users.surname, meeting_ID",
                                                    "users_to_lessons.lessons_ID = '".$_SESSION['s_lessons_ID']."' AND users.login <> '".$currentUser -> user['login'] . "' AND module_dimdim.id = '".$_GET['edit_dimdim']."'");


                    $smarty -> assign("T_USERS", $users);
                }
            }

            $form = new HTML_QuickForm("dimdim_entry_form", "post", $_SERVER['REQUEST_URI']. "&tab=users", "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('text', 'name', null, 'class = "inputText"');
            $form -> addRule('name', _DIMDIMTHEFIELDNAMEISMANDATORY, 'required', null, 'client');

            // Dates
            $days = array();
            for ($i = 1; $i < 32; $i++) {
                $days[$i] = $i;
            }

            $months = array();
            for ($i = 1; $i <= 12; $i++) {
                $months[$i] = $i;
            }

            $years = array();
            for ($i = 2008; $i < 2015; $i++) {
                $years[$i] = $i;
            }

            $hours = array();
            for ($i = 0; $i <= 9; $i++) {
                $hours[$i] = "0".$i;
            }
            for ($i = 10; $i <= 23; $i++) {
                $hours[$i] = $i;
            }

            $minutes = array();
            $minutes[0] = "00";
            for ($i = 15; $i < 60; $i+=15) {
                $minutes[$i] = $i;
            }


            $duration_hours = array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5);

            $form -> addElement('select', 'day' , null, $days ,'id="day"');
            $form -> addElement('select', 'month' , null, $months,'id="month"');
            $form -> addElement('select', 'year' , null, $years,'id="year"');

            $form -> addElement('select', 'hour' , null, $hours,'id="hour"');
            $form -> addElement('select', 'minute' , null, $minutes,'id="minute"');

            $form -> addElement('select', 'duration_hours' , null, $duration_hours,'id="duration_hours"');
            $form -> addElement('select', 'duration_minutes' , null, $minutes,'id="duration_minute"');

            $form -> addElement('select', 'lobby' , _DIMDIMUSELOBBYROOM, array("0" => _YES,"1" => _NO), 'id="lobbyId"');
            $form -> addElement('select', 'presenterAV' , _DIMDIMPRESENTERAV, array("0" => _DIMDIMAUDIOVIDEO, "1" => _DIMDIMAUDIOONLY), 'id="presenterAvID"');

            
            $currentLesson = $this -> getCurrentLesson();
            $students = eF_getTableData("users_to_lessons", "count(users_LOGIN) as total_students", "lessons_ID = '".$currentLesson -> lesson['id']."'");
            
            $total_students = $students[0]['total_students'];
            $students_count = array();
            for ($i = 1; $i <= $total_students; $i++) {
                $students_count[$i] = $i;
            }
            $form -> addElement('select', 'maxParticipants', _DIMDIMMAXPARTICIPANTS, $students_count, '');
            $form -> addElement('select', 'maxMics', _DIMDIMMAXMICS,  $students_count, '');
            $form -> addElement('submit', 'submit_dimdim', _SUBMIT, 'class = "flatButton"');


            if (isset($_GET['edit_dimdim'])) {
                $dimdim_entry = eF_getTableData("module_dimdim", "*", "id=".$_GET['edit_dimdim']);
                $timestamp_info = getdate($dimdim_entry[0]['timestamp']);
                $form -> setDefaults(array('name'       => $dimdim_entry[0]['name'],
                                           'presenterAV'=> $dimdim_entry[0]['confType'],
                                           'maxParticipants'=> $dimdim_entry[0]['maxParticipants'],
                                           'maxMics'=> $dimdim_entry[0]['maxMics'],
                                           'lobby'=> $dimdim_entry[0]['lobby'],
                                           'lessons_ID' => $dimdim_entry[0]['lessons_ID']));
            } else {
                $timestamp_info = getdate(time());
                $timestamp_info['minutes'] = $timestamp_info['minutes'] - ($timestamp_info['minutes'] % 15);
            }

            $form -> setDefaults(array('day'       => $timestamp_info['mday'],
                                       'month'     => $timestamp_info['mon'],
                                       'year'      => $timestamp_info['year'],
                                       'hour'      => $timestamp_info['hours'],
                                       'minute'    => $timestamp_info['minutes'],
                                       'maxParticipants' => ($dimdim_entry[0]['maxParts'] >0 && $dimdim_entry[0]['maxParts'] < $total_students)?$dimdim_entry[0]['maxParts']:$total_students,
                                       'maxMics'         => ($dimdim_entry[0]['maxMics']> 0 && $dimdim_entry[0]['maxMics'] < $total_students)?$dimdim_entry[0]['maxMics']:$total_students));
            

            if ($form -> isSubmitted() && $form -> validate()) {

                if (eF_checkParameter($form -> exportValue('name'), 'text')) {
                    $smarty = $this -> getSmartyVar();
                    $currentLesson = $this -> getCurrentLesson();

                    $timestamp = mktime($form -> exportValue('hour'), $form -> exportValue('minute'), 0, $form -> exportValue('month'), $form -> exportValue('day'), $form -> exportValue('year'));

                    $fields = array('name'            => $form -> exportValue('name'),
                                    'timestamp'       => $timestamp,
                                    'lessons_ID'      => $currentLesson -> lesson['id'],
                                    'durationHours'   => $form -> exportValue('duration_hours'),
                                    'durationMinutes' => $form -> exportValue('duration_minutes'),
                                    'confType'        => $form -> exportValue('presenterAV'),
                                    'maxParts'        => ($form -> exportValue('maxParticipants')>0) ?$form -> exportValue('maxParticipants'):20,
                                    'maxMics'         => $form -> exportValue('maxMics'),
                                    'lobby'           => $form -> exportValue('lobby'));


                    if (isset($_GET['edit_dimdim'])) {
                        if (eF_updateTableData("module_dimdim", $fields, "id=".$_GET['edit_dimdim'])) {
                            header("location:".$this -> moduleBaseUrl."&message=".urlencode(_DIMDIM_SUCCESFULLYUPDATEDDIMDIMENTRY)."&message_type=success");
                        } else {
                            header("location:".$this -> moduleBaseUrl."&message=".urlencode(_DIMDIM_PROBLEMUPDATINGDIMDIMENTRY)."&message_type=failure");
                        }
                    } else {
                        // The key will be the current time when the event was set concatenated with the initial timestamp for the meeting
                        // If the latter changes after an event editing the key will not be changed
                        $fields['confKey'] = $currentLesson -> lesson['id'] . time() . $timestamp;
                        if ($result = eF_insertTableData("module_dimdim", $fields)) {
                            header("location:".$this -> moduleBaseUrl."&edit_dimdim=".$result."&message=".urlencode(_DIMDIM_SUCCESFULLYINSERTEDDIMDIMENTRY)."&message_type=success&tab=users");
                        } else {
                            header("location:".$this -> moduleBaseUrl."&message=".urlencode(_DIMDIM_PROBLEMINSERTINGDIMDIMENTRY)."&message_type=failure");
                        }
                    }
                } else {
                    header("location:".$this -> moduleBaseUrl."&message=".urlencode(_DIMDIM_PROBLEMINSERTINGDIMDIMENTRY)."&message_type=failure");
                }
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);

            $smarty -> assign('T_DIMDIM_FORM', $renderer -> toArray());
        } else {
            $currentUser = $this -> getCurrentUser();
            $currentLesson = $this -> getCurrentLesson();
            
            
            if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
                $dimdim = eF_getTableData("module_dimdim", "*", "lessons_ID = '".$currentLesson -> lesson['id']."'");
                $smarty -> assign("T_DIMDIM_CURRENTLESSONTYPE", "professor");
            } else {
                $dimdim = eF_getTableData("module_dimdim_users_to_meeting JOIN module_dimdim ON id = meeting_ID", "*", "lessons_ID = '".$currentLesson -> lesson['id']."' AND users_LOGIN='".$currentUser -> user['login']."'");
                $smarty -> assign("T_DIMDIM_CURRENTLESSONTYPE", "student");
            }

            $now = time();
            foreach ($dimdim as $key => $meeting) {
                if ($meeting['timestamp'] < $now) {
                    $dimdim[$key]['mayStart'] = 1;
                    $dimdim[$key]['joiningUrl'] = $this -> createDimdimUrl($currentUser, $meeting, true);
                } else {
                    $dimdim[$key]['mayStart'] = 0;
                }
            }

            $smarty -> assign("T_DIMDIM", $dimdim);
            $smarty -> assign("T_USERINFO",$currentUser -> user);
        }

        return true;

    }

    public function addScripts() {
        if (isset($_GET['edit_dimdim'])) {
            return array("scriptaculous/prototype", "scriptaculous/effects");
        } else {
            return array();
        }
    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_DIMDIM_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_DIMDIM_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_DIMDIM_MODULE_BASELINK" , $this -> moduleBaseLink);

        return $this -> moduleBaseDir . "module.tpl";
    }

    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getLessonModule() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) != "administrator") {
            // Get smarty variable
            $smarty = $this -> getSmartyVar();
            $currentLesson = $this -> getCurrentLesson();
            if ($currentUser -> getRole($this -> getCurrentLesson()) == "student") {
                $dimdim = eF_getTableData("module_dimdim_users_to_meeting JOIN module_dimdim ON id = meeting_ID", "*", "lessons_ID = '".$currentLesson -> lesson['id']."' AND users_LOGIN='".$currentUser -> user['login']."'", "timestamp DESC");
                $smarty -> assign("T_DIMDIM_CURRENTLESSONTYPE", "student");
                $now = time();
                
                $dimdim_server = eF_getTableData("configuration", "value", "name = 'module_dimdim_server'");
                foreach ($dimdim as $key => $meeting) {
                    $dimdim[$key]['time_remaining'] = eF_convertIntervalToTime(time() - $meeting['timestamp'], true). ' '._AGO;
                    $dimdim[$key]['joiningUrl'] = $this -> createDimdimUrl($currentUser, $meeting, true);
                }
            } else {
                $dimdim = eF_getTableData("module_dimdim", "*", "lessons_ID = '".$currentLesson -> lesson['id']."'", "timestamp DESC");
                $smarty -> assign("T_DIMDIM_CURRENTLESSONTYPE", "professor");
                $now = time();
                foreach ($dimdim as $key => $meeting) {
                    if ($meeting['timestamp'] < $now) {
                        $dimdim[$key]['mayStart'] = 1;
                        // always start_meeting = 1 url so that only one professor might start the meeting
                    } else {
                        $dimdim[$key]['mayStart'] = 0;
                    }

                    $dimdim[$key]['time_remaining'] = eF_convertIntervalToTime(time() - $meeting['timestamp'], true). ' '._AGO;
                }
            }

            $smarty -> assign("T_MODULE_DIMDIM_INNERTABLE_OPTIONS", array(array('text' => _DIMDIM_DIMDIMLIST,   'image' => $this -> moduleBaseLink."images/go_into.png", 'href' => $this -> moduleBaseUrl)));
            $smarty -> assign("T_DIMDIM_INNERTABLE", $dimdim);
            return true;
        } else {
            return false;
        }

    }

    public function getLessonSmartyTpl() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) != "administrator") {
            $smarty = $this -> getSmartyVar();
            $smarty -> assign("T_DIMDIM_MODULE_BASEDIR" , $this -> moduleBaseDir);
            $smarty -> assign("T_DIMDIM_MODULE_BASEURL" , $this -> moduleBaseUrl);
            $smarty -> assign("T_DIMDIM_MODULE_BASELINK" , $this -> moduleBaseLink);
            return $this -> moduleBaseDir . "module_InnerTable.tpl";
        } else {
            return false;
        }
    }
}
?>