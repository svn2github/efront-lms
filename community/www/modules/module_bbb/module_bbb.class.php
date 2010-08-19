<?php

class module_BBB extends EfrontModule {


    // Mandatory functions required for module function
    public function getName() {
        return _BBB;
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
        eF_executeNew("drop table if exists module_BBB");
        $a = eF_executeNew("CREATE TABLE module_BBB (
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
                          PRIMARY KEY (id)
                        ) DEFAULT CHARSET=utf8;");
        eF_executeNew("drop table if exists module_BBB_users_to_meeting ");
        $b = eF_executeNew("CREATE TABLE module_BBB_users_to_meeting (
                        users_LOGIN varchar(255) NOT NULL,
                        meeting_ID int(11) NOT NULL,
                        KEY (users_LOGIN,meeting_ID)
                       ) DEFAULT CHARSET=utf8;");


        if (!($c = eF_executeNew("INSERT INTO configuration VALUES ('module_BBB_server','http://yourserver.com/');"))) {
            $c = eF_executeNew("UPDATE configuration SET value = 'http://yourserver.com/' WHERE name = 'module_BBB_server';");
        }

        if (!($d = eF_executeNew("INSERT INTO configuration VALUES ('module_BBB_salt','29ae87201c1d23f7099f3dfb92f63578');"))) {
            $d = eF_executeNew("UPDATE configuration SET value = '29ae87201c1d23f7099f3dfb92f63578' WHERE name = 'module_BBB_salt';");
        }

        return $a && $b && $c && $d;
    }

    // And on deleting the module
    public function onUninstall() {
        $a = eF_executeNew("DROP TABLE module_BBB;");
        $b = eF_executeNew("DROP TABLE module_BBB_users_to_meeting;");
        $c = eF_executeNew("DELETE FROM configuration WHERE name='module_BBB_server';");
  $d = eF_executeNew("DELETE FROM configuration WHERE name='module_BBB_salt';");

        return $a && $b && $c && $d;
    }

    // On exporting a lesson
    public function onDeleteLesson($lessonId) {
        $meetings_to_del = eF_getTableDataFlat("module_BBB", "id","lessons_ID='".$lessonId."'");
        eF_deleteTableData("module_BBB", "lessons_ID='".$lessonId."'");
        $delmeet = implode($meetings_to_del['id'],"','");
        eF_deleteTableData("module_BBB_users_to_meeting", "meeting_ID IN ('".$delmeet ."')");

        return true;
    }

    // On exporting a lesson
    public function onExportLesson($lessonId) {
        $data = array();
        $data['meetings'] = eF_getTableData("module_BBB", "*","lessons_ID=".$lessonId);
        $data['users_to_meetings'] = eF_getTableData("module_BBB_users_to_meeting JOIN module_BBB ON module_BBB.id = module_BBB_users_to_meeting.meeting_ID", "module_BBB_users_to_meeting.*","lessons_ID=".$lessonId);
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
            $new_meeting_id = eF_insertTableData("module_BBB",$meeting_record);

            if ($new_meeting_id != $old_meeting_id) {
                $changed_ids[$old_meeting_id] = $new_meeting_id;
            }
        }

        foreach ($data['users_to_meetings'] as $users_to_meetings_record) {

            if (isset($changed_ids[$users_to_meetings_record['meeting_ID']])) {
                $users_to_meetings_record['meeting_ID'] = $changed_ids[$users_to_meetings_record['meeting_ID']];
            }
            eF_insertTableData("module_BBB_users_to_meeting",$users_to_meetings_record);
        }
        return true;
    }

    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
            return array('title' => _BBB,
                         'image' => $this -> moduleBaseDir . 'images/bbb32.png',
                         'link' => $this -> moduleBaseUrl);
        }
    }


    public function getCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getType() == "administrator") {
            return array('title' => _BBB,
                         'image' => $this -> moduleBaseDir . 'images/bbb32.png',
                         'link' => $this -> moduleBaseUrl);
        }
    }

    public function getNavigationLinks() {

        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole() == "administrator") {
            $basicNavArray = array (array ('title' => _HOME, 'link' => "administrator.php?ctg=control_panel"),
                                    array ('title' => _BBB, 'link' => $this -> moduleBaseUrl));

        } else {
            $basicNavArray = array (
                                    array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($this -> getCurrentLesson()).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
                                    array ('title' => _HOME, 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
                                    array ('title' => _BBB, 'link' => $this -> moduleBaseUrl));
         if (isset($_GET['edit_BBB'])) {
             $basicNavArray[] = array ('title' => _BBB_MANAGEMENT, 'link' => $this -> moduleBaseUrl . "&edit_BBB=". $_GET['edit_BBB']);
         } else if (isset($_GET['add_BBB'])) {
             $basicNavArray[] = array ('title' => _BBB_MANAGEMENT, 'link' => $this -> moduleBaseUrl . "&add_BBB=1");
         }
        }
        return $basicNavArray;

    }

    public function getSidebarLinkInfo() {

        $link_of_menu_clesson = array (array ('id' => 'BBB_link_id1',
                                              'title' => _BBB,
                                              'image' => $this -> moduleBaseDir . 'images/bbb16',
                                              'eFrontExtensions' => '1',
                                              'link' => $this -> moduleBaseUrl));

        return array ( "current_lesson" => $link_of_menu_clesson);

    }

    public function getLinkToHighlight() {
        return 'BBB_link_id1';
    }


    private $BBB_server_host = false;
    private function getBBBServer() {
        if (!$this -> BBB_server_host) {
            $BBB_server = eF_getTableData("configuration", "value", "name = 'module_BBB_server'");
            $this -> BBB_server_host = $BBB_server[0]['value'];
        }

        return $this -> BBB_server_host;
    }

 // Function to return the security salt
    private $BBB_security_salt = false;
    private function getBBBSalt() {
        if (!$this -> BBB_security_salt) {
            $BBB_salt = eF_getTableData("configuration", "value", "name = 'module_BBB_salt'");
            $this -> BBB_security_salt = $BBB_salt[0]['value'];
        }
         return $this -> BBB_security_salt;
    }


 /* This will help us handle the XML response from the BBB server after the 'create' call.

	 * Shamelessly stolen from the BBB PHP API available in the project's code repository.

	 * I couldn't make the whole thing work with smarty, so I had to take bits and pieces.

	 */
  function bbb_wrap_simplexml_load_file($url)
  {
   return (simplexml_load_file($url));
  }
    /*

     * Function used to create the BBB module URL

     * Parses the options stored for the meeting in the DB and retuns the correct

     * URL according to role of the user, whether the meeting has started or 

     * whether that incomprehensible flag by the guy who did dimdim is true or false.

     */
    private function createBBBUrl($currentUser, $meeting_info, $always_joining = false) {
  // These are common in all cases
  $BBB_server = $this -> getBBBServer();
  $securitySalt = $this -> getBBBSalt();
  if ($BBB_server[strlen($BBB_server)-1] == '/') {
   $BBB_serverPath = $BBB_server."bigbluebutton/api/";
  } else {
   $BBB_serverPath = $BBB_server."/bigbluebutton/api/";
  }
  //echo "always_joining".$always_joining."meetinginfostatus".$meeting_info['status'];
  //Here we create the room, and give back the URL to join as admin after that.
  if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor" && $meeting_info['status'] == 0 && $meeting_info.mayStart) {
   $conferenceNameAndID = urlencode(utf8_decode($meeting_info['name']));
   $moderatorPassword = "M97f15B7113G";
   $attendeePassword = "Ow2D75JE160B";
   $optionString = 'meetingID='.$conferenceNameAndID.'&name='.$conferenceNameAndID.'&moderatorPW='.$moderatorPassword.'&attendeePW='.$attendeePassword;
   $saltedHash = sha1($optionString.$securitySalt);
   $BBBurl = $BBB_serverPath.'create?'.$optionString.'&checksum='.$saltedHash;

   //We parsed the creation URL, let's see what the server has to say.
   //It would be really nice to handle this reply in the future,
   //but it would require a radical rewrite of the whole smarty connection button thing...
   $xml = $this -> bbb_wrap_simplexml_load_file($BBBurl);

   // Returning the join URL when all's gone well....
   if ($xml && $xml->returncode == 'SUCCESS') {

    $fullName = urlencode(utf8_decode($currentUser -> user['name']))."_". urlencode(utf8_decode($currentUser -> user['surname']));
    $conferenceNameAndID = urlencode(utf8_decode($meeting_info['name']));

    $optionString = 'fullName='.$fullName.'&meetingID='.$conferenceNameAndID.'&password='.$moderatorPassword;

    $saltedHash = sha1($optionString.$securitySalt);
    $BBBurl = $BBB_serverPath.'join?'.$optionString.'&checksum='.$saltedHash;

    eF_updateTableData("module_BBB", array('status' => '1'), "id=".$meeting_info[id]);
   } else {
   //...or the professor page if it hasn't.
    $BBBurl = "professorpage.php";
   }
  } else {

   $fullName = urlencode(utf8_decode($currentUser -> user['name']))."_". urlencode(utf8_decode($currentUser -> user['surname']));
   $conferenceNameAndID = urlencode(utf8_decode($meeting_info['name']));

   // Checking the privilege level of the attendee
   if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
    $password = "M97f15B7113G";
   } else {
    $password = "Ow2D75JE160B";
   }

   $optionString = 'fullName='.$fullName.'&meetingID='.$conferenceNameAndID.'&password='.$password;

   $saltedHash = sha1($optionString.$securitySalt);
   $BBBurl = $BBB_serverPath.'join?'.$optionString.'&checksum='.$saltedHash;

  }

/*		//Testing echoes

		echo "BBB_server".$BBB_server;

		echo "strlenofsalt".(strlen($securitySalt));

		echo "BBB_serverPath".$BBB_serverPath;

		echo "BBBurl".$BBBurl;

*/
  return $BBBurl;
 }
    /* MAIN-INDEPENDENT MODULE PAGES */
    public function getModule() {
        $currentUser = $this -> getCurrentUser();
        // Get smarty global variable
        $smarty = $this -> getSmartyVar();
        $userRole = $currentUser -> getRole($this -> getCurrentLesson());
        if ($currentUser -> getType() == "administrator") {
            $form = new HTML_QuickForm("BBB_server_entry_form", "post", $_SERVER['REQUEST_URI'], "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('text', 'server', null, 'class = "inputText" id="server_input"');
            $form -> addRule('server', _BBBTHEFIELDNAMEISMANDATORY, 'required', null, 'client');
            $form -> addElement('text', 'salt', null, 'class = "inputText" id="salt_input"');
            $form -> addElement('submit', 'submit_BBB_server', _SUBMIT, 'class = "flatButton"');


            if ($form -> isSubmitted() && $form -> validate()) {
                $server_name = $form -> exportValue('server');
    $salt_string = $form -> exportValue('salt');
                if ($server_name[strlen($server_name)-1] == "/") {
                    $server_name = substr($server_name, 0, strlen($server_name)-1);
                }
                eF_updateTableData("configuration", array("value" => $server_name), "name = 'module_BBB_server'");
    eF_updateTableData("configuration", array("value" => $salt_string), "name = 'module_BBB_salt'");
                $this -> setMessageVar(_BBB_SUCCESFULLYCHANGEDSERVER, "success");
            }

            $form -> setDefaults(array('server' => $this -> getBBBServer()));
   $form -> setDefaults(array('salt' => $this -> getBBBSalt()));

            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);

            $smarty -> assign('T_BBB_FORM', $renderer -> toArray());
        }



        /*** Ajax Methods - Add/remove skills/jobs***/
        if (isset($_GET['postAjaxRequest'])) {
            /** Post skill - Ajax skill **/
            if ($_GET['insert'] == "true") {
                eF_insertTableData("module_BBB_users_to_meeting", array('users_LOGIN' => $_GET['user'], 'meeting_ID' => $_GET['edit_BBB']));
            } else if ($_GET['insert'] == "false") {
                eF_deleteTableData("module_BBB_users_to_meeting", "users_LOGIN = '". $_GET['user'] . "' AND meeting_ID = '".$_GET['edit_BBB']."'");
            } else if (isset($_GET['addAll'])) {
                $users = eF_getTableData("users JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN LEFT OUTER JOIN module_BBB_users_to_meeting ON users.login = module_BBB_users_to_meeting.users_LOGIN","users.login, users.name, users.surname, meeting_ID","users_to_lessons.lessons_ID = '".$_SESSION['s_lessons_ID']."' AND (meeting_ID <> '".$_GET['edit_BBB']."' OR meeting_ID IS NULL)");

                $users_attending = eF_getTableDataFlat("users JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN LEFT OUTER JOIN module_BBB_users_to_meeting ON users.login = module_BBB_users_to_meeting.users_LOGIN","users.login","users_to_lessons.lessons_ID = '".$_SESSION['s_lessons_ID']."' AND meeting_ID = '".$_GET['edit_BBB']."'");

                isset($_GET['filter']) ? $users = eF_filterData($users, $_GET['filter']) : null;
                $users_attending = $users_attending['login'];

                foreach ($users as $user) {
                    if (!in_array($user['login'], $users_attending)) {
                        eF_insertTableData("module_BBB_users_to_meeting", array('users_LOGIN' => $user['login'], 'meeting_ID' => $_GET['edit_BBB']));
                        $users_attending[] = $user['login'];
                    }
                }
            } else if (isset($_GET['removeAll'])) {
                $users_attending = eF_getTableData("users JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN LEFT OUTER JOIN module_BBB_users_to_meeting ON users.login = module_BBB_users_to_meeting.users_LOGIN","users.login","users_to_lessons.lessons_ID = '".$_SESSION['s_lessons_ID']."' AND meeting_ID = '".$_GET['edit_BBB']."'");
                //$users_attending = $users_attending['login'];
                isset($_GET['filter']) ? $users_attending = eF_filterData($users_attending, $_GET['filter']) : null;

                $users_to_delete = array();
                foreach($users_attending as $user) {
                    $users_to_delete[] = $user['login'];
                }
                eF_deleteTableData("module_BBB_users_to_meeting", "meeting_ID = '".$_GET['edit_BBB']."' AND users_LOGIN IN ('".implode("','", $users_to_delete)."')");
            } else if (isset($_GET['mail_users']) && $_GET['mail_users'] == 1) {
                $currentLesson = $this ->getCurrentLesson();
                $meeting_users = eF_getTableData("module_BBB_users_to_meeting JOIN users ON module_BBB_users_to_meeting.users_LOGIN = users.login", "users.login, users.name, users.surname, users.email", "meeting_ID = ".$_GET['edit_BBB'] . " AND users.login <> '". $currentUser -> user['login'] ."'");

                isset($_GET['filter']) ? $meeting_users = eF_filterData($meeting_users , $_GET['filter']) : null;

                $meeting_info = eF_getTableData("module_BBB", "*", "id = ".$_GET['edit_BBB']);

                $subject = _BBB_MEETING;
                $count = 0;
                foreach ($meeting_users as $user) {

                    $body = _BBB_DEAR . " " . $user['name']. ",\n\n" ._BBB_YOUHAVEBEENINVITEDBYPROFESSOR . " " . $currentUser -> user['name']. " " . $currentUser -> user['surname'] . " " . _BBB_TOATTENDACONFERENCE . " \"". $meeting_info[0]['name'] . "\" " . _BBB_FORLESSON. " \"" . $currentLesson -> lesson['name'] . "\" " . _BBB_SCHEDULEDFOR . "\n\n". date("D d.m.y, g:i a", $meeting_info[0]['timestamp']). "\n\n" ._BBBYOUCANJOINTHEMEETINGDIRECTLYBYCLICKINGTHEFOLLOWINGLINKAFTERITSTARTS . ":\n\n";

                    $userObject = EfrontUserFactory::factory($user['login']);

                    //$body .= $this -> createBBBUrl($userObject, $meeting_info[0], true);
                    $body .= "\n\n" ._BBB_SINCERELY . ",\n" . $currentUser -> user['name']." ".$currentUser -> user['surname'];

                    $my_email = $currentUser -> user['email'];
                    $user_mail = $user['email'];
                    $header = array ('From' => $GLOBALS['configuration']['system_email'],
                                     'To' => $user_mail,
                                     'Subject' => $subject,
                                     'Content-type' => 'text/plain;charset="UTF-8"', // if content-type is text/html, the message cannot be received by mail clients for Registration content
                                     'Content-Transfer-Encoding' => '7bit');
                    $smtp = Mail::factory('smtp', array('auth' => $GLOBALS['configuration']['smtp_auth'] ? true : false,
                                                         'host' => $GLOBALS['configuration']['smtp_host'],
                                                         'password' => $GLOBALS['configuration']['smtp_pass'],
                                                         'port' => $GLOBALS['configuration']['smtp_port'],
                                                         'username' => $GLOBALS['configuration']['smtp_user'],
                                                         'timeout' => $GLOBALS['configuration']['smtp_timeout']));

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

            $BBB_server = $this -> getBBBServer();
            if ($BBB_server != "") {

                $BBB = eF_getTableData("module_BBB", "*", "id=".$_GET['start_meeting']);

                if ($BBB[0]['status'] != 2) {

                    $BBBUrl = $this -> createBBBUrl($currentUser, $BBB[0]);
     $smarty -> assign("T_BBB_CREATEMEETINGURL", $BBBurl); // TESTING

                    if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor" && $meeting_info['status'] == 0) {
                        eF_updateTableData("module_BBB", array('status' => '1'), "id=".$_GET['start_meeting']);
                    }

                    //echo $BBBUrl."<BR>";
                    header("location:".$BBBUrl);

                } else {
                    $this -> setMessageVar(_BBBMEETINGHASFINISHED, "failure");
                }
            } else {
                $this -> setMessageVar(_BBB_NOBBBSERVERDEFINED, "failure");
            }
        }


        if (isset($_GET['finished_meeting']) && eF_checkParameter($_GET['finished_meeting'], 'id')) {
            if ($userRole == "professor") {
                eF_updateTableData("module_BBB", array('status' => '2'), "id=".$_GET['finished_meeting']);
            }

            $currentLesson = $this -> getCurrentLesson();
            $_SESSION['previousSideUrl'] = G_SERVERNAME ."new_sidebar.php?new_lesson_id=" . $currentLesson -> lesson['id'] ;
            $_SESSION['previousMainUrl'] = G_SERVERNAME . $currentUser -> getType() . ".php?ctg=control_panel";
            header("location:". $currentUser -> getType() . "page.php");
        }

        if (isset($_GET['delete_BBB']) && eF_checkParameter($_GET['delete_BBB'], 'id') && $userRole == "professor") {
            eF_deleteTableData("module_BBB", "id=".$_GET['delete_BBB']);
            eF_deleteTableData("module_BBB_users_to_meeting", "meeting_ID=".$_GET['delete_BBB']);
            header("location:". $this -> moduleBaseUrl ."&message=".urlencode(_BBB_SUCCESFULLYDELETEDBBBENTRY)."&message_type=success");
        } else if ($userRole == "professor" && (isset($_GET['add_BBB']) || (isset($_GET['edit_BBB']) && eF_checkParameter($_GET['edit_BBB'], 'id')))) {

            // Create ajax enabled table for meeting attendants
            if (isset($_GET['edit_BBB'])) {
                if (isset($_GET['ajax']) && $_GET['ajax'] == 'BBBUsersTable') {
                    isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

                    if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                        $sort = $_GET['sort'];
                        isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                    } else {
                        $sort = 'login';
                    }

                    $users = eF_getTableData("users JOIN users_to_lessons ON users.login = users_to_lessons.users_LOGIN
                                                    JOIN module_BBB ON module_BBB.lessons_ID = users_to_lessons.lessons_ID
                                                    LEFT OUTER JOIN module_BBB_users_to_meeting ON module_BBB.id = module_BBB_users_to_meeting.meeting_ID AND users.login = module_BBB_users_to_meeting.users_LOGIN",
                                                    "users.login, users.name, users.surname, users.email, meeting_ID",
                                                    "users_to_lessons.lessons_ID = '".$_SESSION['s_lessons_ID']."' AND users.login <> '".$currentUser -> user['login'] . "' AND module_BBB.id = '".$_GET['edit_BBB']."'");

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
                                                    JOIN module_BBB ON module_BBB.lessons_ID = users_to_lessons.lessons_ID
                                                    LEFT OUTER JOIN module_BBB_users_to_meeting ON module_BBB.id = module_BBB_users_to_meeting.meeting_ID AND users.login = module_BBB_users_to_meeting.users_LOGIN",
                                                    "users.login, users.name, users.surname, meeting_ID",
                                                    "users_to_lessons.lessons_ID = '".$_SESSION['s_lessons_ID']."' AND users.login <> '".$currentUser -> user['login'] . "' AND module_BBB.id = '".$_GET['edit_BBB']."'");


                    $smarty -> assign("T_USERS", $users);
                }
            }

            $form = new HTML_QuickForm("BBB_entry_form", "post", $_SERVER['REQUEST_URI']. "&tab=users", "", null, true);
            $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter
            $form -> addElement('text', 'name', null, 'class = "inputText"');
            $form -> addRule('name', _BBBTHEFIELDNAMEISMANDATORY, 'required', null, 'client');

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

            $form -> addElement('select', 'lobby' , _BBBUSELOBBYROOM, array("0" => _YES,"1" => _NO), 'id="lobbyId"');
            $form -> addElement('select', 'presenterAV' , _BBBPRESENTERAV, array("0" => _BBBAUDIOVIDEO, "1" => _BBBAUDIOONLY), 'id="presenterAvID"');


            $currentLesson = $this -> getCurrentLesson();
            $students = eF_getTableData("users_to_lessons", "count(users_LOGIN) as total_students", "lessons_ID = '".$currentLesson -> lesson['id']."'");

            $total_students = $students[0]['total_students'];
            $students_count = array();
            for ($i = 1; $i <= $total_students; $i++) {
                $students_count[$i] = $i;
            }
            $form -> addElement('select', 'maxParticipants', _BBBMAXPARTICIPANTS, $students_count, '');
            $form -> addElement('select', 'maxMics', _BBBMAXMICS, $students_count, '');
            $form -> addElement('submit', 'submit_BBB', _SUBMIT, 'class = "flatButton"');


            if (isset($_GET['edit_BBB'])) {
                $BBB_entry = eF_getTableData("module_BBB", "*", "id=".$_GET['edit_BBB']);
                $timestamp_info = getdate($BBB_entry[0]['timestamp']);
                $form -> setDefaults(array('name' => $BBB_entry[0]['name'],
                                           'presenterAV'=> $BBB_entry[0]['confType'],
                                           'maxParticipants'=> $BBB_entry[0]['maxParticipants'],
                                           'maxMics'=> $BBB_entry[0]['maxMics'],
                                           'lobby'=> $BBB_entry[0]['lobby'],
                                           'lessons_ID' => $BBB_entry[0]['lessons_ID']));
            } else {
                $timestamp_info = getdate(time());
                $timestamp_info['minutes'] = $timestamp_info['minutes'] - ($timestamp_info['minutes'] % 15);
            }

            $form -> setDefaults(array('day' => $timestamp_info['mday'],
                                       'month' => $timestamp_info['mon'],
                                       'year' => $timestamp_info['year'],
                                       'hour' => $timestamp_info['hours'],
                                       'minute' => $timestamp_info['minutes'],
                                       'maxParticipants' => ($BBB_entry[0]['maxParts'] >0 && $BBB_entry[0]['maxParts'] < $total_students)?$BBB_entry[0]['maxParts']:$total_students,
                                       'maxMics' => ($BBB_entry[0]['maxMics']> 0 && $BBB_entry[0]['maxMics'] < $total_students)?$BBB_entry[0]['maxMics']:$total_students));


            if ($form -> isSubmitted() && $form -> validate()) {

                if (eF_checkParameter($form -> exportValue('name'), 'text')) {
                    $smarty = $this -> getSmartyVar();
                    $currentLesson = $this -> getCurrentLesson();

                    $timestamp = mktime($form -> exportValue('hour'), $form -> exportValue('minute'), 0, $form -> exportValue('month'), $form -> exportValue('day'), $form -> exportValue('year'));

                    $fields = array('name' => $form -> exportValue('name'),
                                    'timestamp' => $timestamp,
                                    'lessons_ID' => $currentLesson -> lesson['id'],
                                    'durationHours' => $form -> exportValue('duration_hours'),
                                    'durationMinutes' => $form -> exportValue('duration_minutes'),
                                    'confType' => $form -> exportValue('presenterAV'),
                                    'maxParts' => ($form -> exportValue('maxParticipants')>0) ?$form -> exportValue('maxParticipants'):20,
                                    'maxMics' => $form -> exportValue('maxMics'),
                                    'lobby' => $form -> exportValue('lobby'));


                    if (isset($_GET['edit_BBB'])) {
                        if (eF_updateTableData("module_BBB", $fields, "id=".$_GET['edit_BBB'])) {
                            header("location:".$this -> moduleBaseUrl."&message=".urlencode(_BBB_SUCCESFULLYUPDATEDBBBENTRY)."&message_type=success");
                        } else {
                            header("location:".$this -> moduleBaseUrl."&message=".urlencode(_BBB_PROBLEMUPDATINGBBBENTRY)."&message_type=failure");
                        }
                    } else {
                        // The key will be the current time when the event was set concatenated with the initial timestamp for the meeting
                        // If the latter changes after an event editing the key will not be changed
                        $fields['confKey'] = $currentLesson -> lesson['id'] . time() . $timestamp;
                        if ($result = eF_insertTableData("module_BBB", $fields)) {
                            header("location:".$this -> moduleBaseUrl."&edit_BBB=".$result."&message=".urlencode(_BBB_SUCCESFULLYINSERTEDBBBENTRY)."&message_type=success&tab=users");
                        } else {
                            header("location:".$this -> moduleBaseUrl."&message=".urlencode(_BBB_PROBLEMINSERTINGBBBENTRY)."&message_type=failure");
                        }
                    }
                } else {
                    header("location:".$this -> moduleBaseUrl."&message=".urlencode(_BBB_PROBLEMINSERTINGBBBENTRY)."&message_type=failure");
                }
            }
            $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);

            $smarty -> assign('T_BBB_FORM', $renderer -> toArray());
        } else {
            $currentUser = $this -> getCurrentUser();
            $currentLesson = $this -> getCurrentLesson();


            if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
                $BBB = eF_getTableData("module_BBB", "*", "lessons_ID = '".$currentLesson -> lesson['id']."'");
                $smarty -> assign("T_BBB_CURRENTLESSONTYPE", "professor");
            } else {
                $BBB = eF_getTableData("module_BBB_users_to_meeting JOIN module_BBB ON id = meeting_ID", "*", "lessons_ID = '".$currentLesson -> lesson['id']."' AND users_LOGIN='".$currentUser -> user['login']."'");
                $smarty -> assign("T_BBB_CURRENTLESSONTYPE", "student");
            }

            $now = time();
            foreach ($BBB as $key => $meeting) {
                if ($meeting['timestamp'] < $now) {
                    $BBB[$key]['mayStart'] = 1;
                    $BBB[$key]['joiningUrl'] = $this -> createBBBUrl($currentUser, $meeting, true);
     $smarty -> assign("T_BBB_CREATEMEETINGURL", $BBB[$key]['joiningUrl']); // TESTING
                } else {
                    $BBB[$key]['mayStart'] = 0;
                }
            }

            $smarty -> assign("T_BBB", $BBB);
            $smarty -> assign("T_USERINFO",$currentUser -> user);
        }

        return true;

    }

    public function addScripts() {
        if (isset($_GET['edit_BBB'])) {
            return array("scriptaculous/prototype", "scriptaculous/effects");
        } else {
            return array();
        }
    }

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_BBB_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_BBB_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_BBB_MODULE_BASELINK" , $this -> moduleBaseLink);

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
                $BBB = eF_getTableData("module_BBB_users_to_meeting JOIN module_BBB ON id = meeting_ID", "*", "lessons_ID = '".$currentLesson -> lesson['id']."' AND users_LOGIN='".$currentUser -> user['login']."'", "timestamp DESC");
                $smarty -> assign("T_BBB_CURRENTLESSONTYPE", "student");
                $now = time();

                $BBB_server = eF_getTableData("configuration", "value", "name = 'module_BBB_server'");
                foreach ($BBB as $key => $meeting) {
                    $BBB[$key]['time_remaining'] = eF_convertIntervalToTime(time() - $meeting['timestamp'], true). ' '._AGO;
                    $BBB[$key]['joiningUrl'] = $this -> createBBBUrl($currentUser, $meeting, true);
     $smarty -> assign("T_BBB_CREATEMEETINGURL", $BBB[$key]['joiningUrl']); // TESTING					
                }
            } else {
                $BBB = eF_getTableData("module_BBB", "*", "lessons_ID = '".$currentLesson -> lesson['id']."'", "timestamp DESC");
                $smarty -> assign("T_BBB_CURRENTLESSONTYPE", "professor");
                $now = time();
                foreach ($BBB as $key => $meeting) {
                    if ($meeting['timestamp'] < $now) {
                        $BBB[$key]['mayStart'] = 1;
                        // always start_meeting = 1 url so that only one professor might start the meeting

      $BBB_meeting_creation_URL = $this -> createBBBUrl($currentUser, $meeting, FALSE);
      $smarty -> assign("T_BBB_CREATEMEETINGURL", $BBB_meeting_creation_URL);
     } else {
                        $BBB[$key]['mayStart'] = 0;
                    }

                    $BBB[$key]['time_remaining'] = eF_convertIntervalToTime(time() - $meeting['timestamp'], true). ' '._AGO;
                }
            }

            $smarty -> assign("T_MODULE_BBB_INNERTABLE_OPTIONS", array(array('text' => _BBB_BBBLIST, 'image' => $this -> moduleBaseLink."images/go_into.png", 'href' => $this -> moduleBaseUrl)));
            $smarty -> assign("T_BBB_INNERTABLE", $BBB);
            return true;
        } else {
            return false;
        }

    }

    public function getLessonSmartyTpl() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) != "administrator") {
            $smarty = $this -> getSmartyVar();
            $smarty -> assign("T_BBB_MODULE_BASEDIR" , $this -> moduleBaseDir);
            $smarty -> assign("T_BBB_MODULE_BASEURL" , $this -> moduleBaseUrl);
            $smarty -> assign("T_BBB_MODULE_BASELINK" , $this -> moduleBaseLink);
            return $this -> moduleBaseDir . "module_InnerTable.tpl";
        } else {
            return false;
        }
    }
}
?>
