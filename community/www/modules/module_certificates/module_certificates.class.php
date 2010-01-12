<?php

class module_certificates extends EfrontModule {


    // Mandatory functions required for module function
    public function getName() {
        return _CERTIFICATES;
    }

    public function getPermittedRoles() {
        return array("professor","student");
    }

	public function isLessonModule() {
		return true;
	}

    // Optional functions
    // What should happen on installing the module
    public function onInstall() {
        $res1 = eF_executeNew("CREATE TABLE if not exists module_certificates (
                          lessons_ID int(11) not null,
                          certificate_id int(11) not null,
						  auto_certificate tinyint(1) default '0',
                          PRIMARY KEY  (lessons_ID)
                        ) DEFAULT CHARSET=utf8;");
        $res2 = eF_executeNew("CREATE TABLE if not exists module_certificates_users (
                          lessons_ID int(11) not null,
                          users_LOGIN varchar(255) not null,
                          issued_certificate text,
                          PRIMARY KEY  (lessons_ID, users_LOGIN)
                        ) DEFAULT CHARSET=utf8;");
        return ($res1 && $res2);
    }

    // And on deleting the module
    public function onUninstall() {
        $res1 = eF_executeNew("DROP TABLE module_certificates;");
        $res2 = eF_executeNew("DROP TABLE module_certificates_users;");
        return ($res1 && $res2);
    }

    // On deleting a lesson
    public function onDeleteLesson($lessonId) {
        $res1 = eF_deleteTableData("module_certificates", "lessons_ID=".$lessonId);
        $res2 = eF_deleteTableData("module_certificates_users", "lessons_ID=".$lessonId);
        return ($res1 && $res2);
    }

    // On exporting a lesson
    public function onExportLesson($lessonId) {
        $data['certificates'] = eF_getTableData("module_certificates", "*","lessons_ID=".$lessonId);
        $data['certificates_users'] = eF_getTableData("module_certificates_users", "*","lessons_ID=".$lessonId);
        return serialize($data);
    }

    // On importing a lesson
    public function onImportLesson($lessonId, $data) {
        $data = unserialize($data);
        foreach ($data['certificates'] as $record) {
            $record['lessons_ID'] = $lessonId;
            eF_insertTableData("module_certificates", $record);
        }
        foreach ($data['certificates_users'] as $record) {
            $record['lessons_ID'] = $lessonId;
            eF_insertTableData("module_certificates_users", $record);
        }
        return true;
    }

    public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        //if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
            return array('title' => _CERTIFICATES_CERTIFICATES,
                         'image' => $this -> moduleBaseDir.'images/certificate32.png',
                         'link'  => $this -> moduleBaseUrl);
       // }
    }


    public function getSidebarLinkInfo() {
        $link_of_menu_clesson = array (array ('id' => 'other_link_id1',
                                                  'title' => _CERTIFICATES_CERTIFICATES,
                                                  'image' => $this -> moduleBaseDir . 'images/certificate16',
                                                  'eFrontExtensions' => '1',
                                                  'link'  => $this -> moduleBaseUrl));

        return array ( "current_lesson" => $link_of_menu_clesson);
    }

    public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();
		$currentLesson = $this -> getCurrentLesson();
		
        if ($_GET['modop'] != 'format_certificate'){
            return array (	array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
							array ('title' => $currentLesson -> lesson['name'], 'link'  => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
							array ('title' => _CERTIFICATES_CERTIFICATES, 'link'  => $this -> moduleBaseUrl));    
        }
        else{
            return array (	array ('title' => _MYLESSONS, 'onclick'  => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
							array ('title' => $currentLesson -> lesson['name'], 'link'  => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
							array ('title' => _CERTIFICATES_CERTIFICATES, 'link'  => $this -> moduleBaseUrl), 
							array ('title' => _FORMATCERTIFICATE, 'link'  => $this -> moduleBaseUrl."&modop=format_certificate")
                      );    
        }
    }

    public function getLinkToHighlight() {
        return 'other_link_id1';
    }
    
        public function issueCertificate($login, $certificate, $lesson_id) {
        if (eF_checkParameter($login, 'login')) {
            eF_insertTableData("module_certificates_users", 
                array("issued_certificate" => $certificate, "users_LOGIN" => $login, "lessons_ID" => $lesson_id)
                );
            return true;
        } else {
            throw new EfrontUserException(_INVALIDLOGIN.': '.$login, EfrontUserException :: INVALID_LOGIN);
        }
    }
    
    public function prepareCertificate($login, $currentLesson) {
        if (eF_checkParameter($login, 'login')) {
            $data = array();
            $lessonUser  = EfrontUserFactory :: factory($login);
            $userStats   = EfrontStats::getUsersLessonStatus($currentLesson -> lesson['id'], $login);
            $data['organization'] = $GLOBALS['configuration']['site_name'];
            $data['lesson_name']  = $currentLesson -> lesson['name'];
            $data['user_surname'] = $lessonUser -> user['surname'];
            $data['user_name']    = $lessonUser -> user['name'];
            $data['grade']        = $userStats[$currentLesson -> lesson['id']][$login]['score'];
            $data['date']         = formatTimestamp(time());
            $data = serialize($data);
            return $data;
        } else {
            throw new EfrontUserException(_INVALIDLOGIN.': '.$login, EfrontUserException :: INVALID_LOGIN);
        }
    }
    
    public function revokeCertificate($login, $lesson_id) {
        if (eF_checkParameter($login, 'login')) {
            eF_deleteTableData("module_certificates_users", "users_LOGIN='".$login."' and lessons_ID=".$lesson_id);
            return true;
        } else {
            throw new EfrontUserException(_INVALIDLOGIN.': '.$login, EfrontUserException :: INVALID_LOGIN);
        }
    }

    /* MAIN-INDEPENDENT MODULE PAGES */
    public function getModule() {
        // Get smarty variable
        $smarty        = $this -> getSmartyVar();
        $currentLesson = $this -> getCurrentLesson();
        $currentUser   = $this -> getCurrentUser();
        $smarty -> assign("T_MODOP", $_GET['modop']);
        
        try {
            $role = $currentUser -> getRole($this -> getCurrentLesson());
        }
        catch (Exception $e){
            $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
            $role = $currentUser -> getRole($this -> getCurrentLesson());
        }
        
        if (isset($_GET['export']) && $_GET['export'] == 'rtf') {
            $result = eF_getTableData("module_certificates_users", "*", 
            "users_LOGIN = '".$_GET['user']."' and lessons_ID = '".$currentLesson -> lesson['id']."' limit 1");
            if (sizeof($result) == 1 || isset($_GET['preview'])) {
                if (!isset($_GET['preview'])){
                    $certificate_id = 0;
                    $data = ef_getTableData("module_certificates", "*", "lessons_id=".$currentLesson -> lesson['id']);
                    if (sizeof($data) > 0){
                        $certificate_id = $data[0]['certificate_id'];
                    }
                    if ($certificate_id <= 0) {
                        $cfile = new EfrontFile($this -> moduleBaseDir."templates/certificate1.rtf");
                    } else {
                        $cfile = new EfrontFile($certificate_id);
                    }
                    $template_data = file_get_contents($cfile['path']);
                    $issued_data   = unserialize($result[0]['issued_certificate']);
                    if (sizeof($issued_data) > 1){
                        $certificate   = $template_data;
                        $certificate   = str_replace("#organization#", utf8ToUnicode($issued_data['organization']), $certificate);
                        $certificate   = str_replace("#user_name#", utf8ToUnicode($issued_data['user_name']), $certificate);
                        $certificate   = str_replace("#user_surname#", utf8ToUnicode($issued_data['user_surname']), $certificate);
                        $certificate   = str_replace("#lesson_name#", utf8ToUnicode($issued_data['lesson_name']), $certificate);
                        $certificate   = str_replace("#grade#", utf8ToUnicode($issued_data['grade']), $certificate);
                        $certificate   = str_replace("#date#", utf8ToUnicode($issued_data['date']), $certificate);
                    }                        
                }
                else {
                    $certificateDirectory = $this -> moduleBaseDir."templates/";
                    $selectedCertificate  = $_GET['certificate_tpl'];
                    $certificate          = file_get_contents($certificateDirectory.$selectedCertificate);
                }   
				$filenameRtf = "certificate_".$_GET['user'].".rtf";
				$filenamePdf = G_ROOTPATH."www/phplivedocx/samples/mail-merge/convert/certificate_".$_GET['user'].".pdf";
				file_put_contents(G_ROOTPATH."www/phplivedocx/samples/mail-merge/convert/certificate_".$_GET['user'].".rtf", $certificate);
				$RetValues = file(G_SERVERNAME."phplivedocx/samples/mail-merge/convert/convert-document.php?filename=certificate_".$_GET['user']);
		
				if ($RetValues[0] == "true") {
					header("Content-type: application/pdf");
					header("Content-disposition: inline; filename=$filename");
					$filePdf = file_get_contents($filenamePdf);
					header("Content-length: " . strlen($filePdf));
					echo $filePdf;
					exit(0);
				} else {
					header("Content-type: application/rtf");
					header("Content-disposition: inline; filename=$filenameRtf");
					header("Content-length: " . strlen($certificate));
					echo $certificate;
					exit(0);
				}	
            }
        }
        
        if ($role == "professor") {
            $smarty -> assign("T_CERTIFICATES_PROFESSOR", "1");
            if ($_GET['modop'] == 'format_certificate') {
                $certificate_id   = 0;
                $certificate_data = ef_getTableData("module_certificates", "*", "lessons_ID = ".$currentLesson -> lesson['id']);
                if (sizeof($certificate_data) > 0)
                {
                    $certificate_id = $certificate_data[0]['certificate_id'];
                    if ($certificate_id > 0){
                        $certificateFile = new EfrontFile($certificate_id);
                        $dname           = $certificateFile -> offsetGet('name');    
                    }
                }
                
                try {
                    $certificateFileSystemTree = new FileSystemTree($this -> moduleBaseDir."templates/");
                    foreach (new EfrontFileTypeFilterIterator(new EfrontFileOnlyFilterIterator(new EfrontNodeFilterIterator(new RecursiveIteratorIterator($certificateFileSystemTree -> tree, RecursiveIteratorIterator :: SELF_FIRST))), array('rtf')) as $key => $value) {
                        $existingCertificates[basename($key)] = basename($key);
                    }
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                }
    
                $form = new HTML_QuickForm("edit_lessons_certificate_form", "post", basename($_SERVER['PHP_SELF']).'?ctg=module&op=module_certificates&modop=format_certificate', "", null, true);
                $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter');                   //Register this rule for checking user input with our function, eF_checkParameter
                $form -> addElement('file', 'file_upload', _CERTIFICATETEMPLATE, 'class = "inputText"');
                $form -> addElement('select', 'existing_certificate', _ORSELECTONEFROMLIST, $existingCertificates, "id = 'select_certificate'");
                $form -> addElement('button', 'preview', _PREVIEW,
                'class = "flatButton" onclick = "javascript:window.open(\''.basename($_SERVER['PHP_SELF']).'?ctg=module&op=module_certificates&export=rtf&preview=1&certificate_tpl=\'+document.forms[0].existing_certificate.value)"
                title = "'._VIEWCERTIFICATE.'"');
                $form -> addElement('submit', 'submit_certificate', _SAVE, 'class = "flatButton"');
                $form -> setDefaults(array('existing_certificate' => $dname));
                $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024);
    
                if ($form -> isSubmitted() && $form -> validate()) {
                    $certificateDirectory = $this -> moduleBaseDir."templates/";
                    if (!is_dir($certificateDirectory)) {
                        mkdir($certificateDirectory);
                    }
                    $logoid = 0;
                    try {
                        if ($_FILES['file_upload']['size'] > 0) {
                            $filesystem    = new FileSystemTree($certificateDirectory);
                            $uploadedFile  = $filesystem -> uploadFile('file_upload', $certificateDirectory);
                            $certificateid = $uploadedFile['id'];
                        } else {
                            $selectedCertificate = $form -> exportValue('existing_certificate');
                            $certificateFile     = new EfrontFile($this -> moduleBaseDir."templates/".$selectedCertificate);
                            if ($certificateFile['id'] < 0) { //if the file doesn't exist, then import it 
                                $selectedCertificate = $certificateFileSystemTree -> seekNode($this -> moduleBaseDir."templates/".$selectedCertificate);    
                                $newList             = FileSystemTree :: importFiles($selectedCertificate['path']);
                                $certificateid       = key($newList);
                            }
                            else {
                                $certificateid = $certificateFile['id'];
                            }
                        }
                        
                        $certificate_data = ef_getTableData("module_certificates", "*", "lessons_ID = ".$currentLesson -> lesson['id']);
                        if (sizeof($certificate_data) > 0){
                            $update['certificate_id'] = $certificateid;
                            ef_updateTableData("module_certificates", $update, "lessons_ID = ".$currentLesson -> lesson['id']);
                        }
                        else{
                            $insert['certificate_id'] = $certificateid;
                            $insert['lessons_ID']     = $currentLesson -> lesson['id'];
                            ef_insertTableData("module_certificates", $insert);
                        }
                        eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=module&op=module_certificates&message=".urlencode(_SUCCESFULLYUPDATEDCERTIFICATE)."&message_type=success");
                    } catch (Exception $e) {
                        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                        $message      = _SOMEPROBLEMEMERGED.': '.$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                        $message_type = 'failure';
                    }
                }
    
                $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    
                $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
                $form -> setRequiredNote(_REQUIREDNOTE);
                $form -> accept($renderer);
                $smarty -> assign('T_CERTIFICATE_FORM', $renderer -> toArray());
            }
            
            if (isset($_GET['issue_certificate'])) {
                try {
                    $certificate = $this -> prepareCertificate($_GET['issue_certificate'], $currentLesson);
                    $this -> issueCertificate($_GET['issue_certificate'], $certificate, $currentLesson -> lesson['id']);
                    eF_redirect(''.basename($_SERVER['PHP_SELF']).'?ctg=module&op=module_certificates&message='.urlencode(_STUDENTSTATUSCHANGED).'&message_type=success');
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _PROBLEMISSUINGCERTIFICATE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
            } else if (isset($_GET['revoke_certificate'])) {
                try {
                    $this -> revokeCertificate($_GET['revoke_certificate'], $currentLesson -> lesson['id']);
                    eF_redirect(''.basename($_SERVER['PHP_SELF']).'?ctg=module&op=module_certificates&message='.urlencode(_CERTIFICATEREVOKED).'&message_type=success');
                } catch (Exception $e) {
                    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
                    $message      = _PROBLEMREVOKINGCERTIFICATE.': '.$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
                    $message_type = 'failure';
                }
			} 
	
            $certificate_data = eF_getTableData("module_certificates", "*", "lessons_ID = ".$currentLesson -> lesson['id']);
			if (sizeof($certificate_data) > 0) {
				$smarty -> assign("T_SHOW_AUTO", 1);
			}
			//echo $currentLesson -> lesson['id'];
			//pr($certificate_data);
			
			if ($_GET['modop'] == 'auto_certificate') {
			    if ($certificate_data[0]['auto_certificate'] == 1) {
                    $certificate_data[0]['auto_certificate'] = 0;
                } else {
                    $certificate_data[0]['auto_certificate'] = 1;
                }
                eF_updateTableData("module_certificates", array('auto_certificate' => $certificate_data[0]['auto_certificate']), "lessons_ID = ".$currentLesson -> lesson['id']);
			}
	//pr($certificate_data);
			$smarty -> assign("T_CERTIFICATE_DATA", $certificate_data);
            //Get users list through ajax
            if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
                $users = EfrontStats::getUsersLessonStatus($currentLesson);
                $users = $users[$currentLesson -> lesson['id']];
                foreach ($users as $key => $user) {
                    if ($user['user_type'] != 'student') {
                        unset($users[$key]);
                    }
                }
    
                isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;
    
                if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                    $sort = $_GET['sort'];
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                } else {
                    $sort = 'login';
                }
                $users = eF_multiSort($users, $sort, $order);
                $smarty -> assign("T_USERS_SIZE", sizeof($users));
                if (isset($_GET['filter'])) {
                    $users = eF_filterData($users, $_GET['filter']);
                }
                if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                    isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                    $users = array_slice($users, $offset, $limit);
                }
     
                foreach ($users as $key => $value) {
                    $data = ef_getTableData("module_certificates_users", "*", "users_LOGIN='$key' and lessons_ID=".$currentLesson -> lesson['id']);
                    if (sizeof($data) > 0){
                        $users[$key]['issued_certificate'] = 1;
                    }
                    else{
                        $users[$key]['issued_certificate'] = null;
                    }
                }
                $smarty -> assign("T_USERS_PROGRESS", $users);
            }
            
            return true;
        }
        else {
			$certificate_data 	= eF_getTableData("module_certificates", "auto_certificate", "lessons_ID = ".$currentLesson -> lesson['id']);
			$completed 			= eF_getTableData("users_to_lessons","completed","lessons_ID=".$currentLesson -> lesson['id']." and users_LOGIN='".$currentUser -> user['login']."'");
			//pr($completed);
			if (isset($certificate_data[0]["auto_certificate"]) && $certificate_data[0]["auto_certificate"] == 1 && $completed[0]['completed'] == 1) {
				$certificate = $this -> prepareCertificate($currentUser -> user['login'], $currentLesson);
				$this -> issueCertificate($currentUser -> user['login'], $certificate, $currentLesson -> lesson['id']);
			} 
            $data = ef_getTableData("module_certificates_users", "*", "users_LOGIN='".$currentUser -> user['login']."' and lessons_ID=".$currentLesson -> lesson['id']);
            if (sizeof($data) > 0){
                $smarty -> assign("T_USERLESSON_CERTIFICATE_EXISTS", "1");
                $smarty -> assign("T_CERTIFICATES_USERLOGIN", $currentUser -> user['login']);
            }
            return true;
        }
    }
	public function onCompleteLesson($lessonId, $login) {
		$currentLesson = $this -> getCurrentLesson();
        //$currentUser   = $this -> getCurrentUser();
			$certificate_data 	= eF_getTableData("module_certificates", "auto_certificate", "lessons_ID = ".$lessonId);
			$completed 			= eF_getTableData("users_to_lessons","completed","lessons_ID=".$lessonId." and users_LOGIN='".$login."'");
			//pr($completed);
			if (isset($certificate_data[0]["auto_certificate"]) && $certificate_data[0]["auto_certificate"] == 1 && $completed[0]['completed'] == 1) {
				$certificate = $this -> prepareCertificate($login, $currentLesson);
				$this -> issueCertificate($login, $certificate, $lessonId);
			} 
            $data = ef_getTableData("module_certificates_users", "*", "users_LOGIN='".$login."' and lessons_ID=".$lessonId);
            return true;
	} 

    public function getSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_CERTIFICATES_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_CERTIFICATES_MODULE_BASEURL" , $this -> moduleBaseUrl);
		$smarty -> assign("T_CERTIFICATES_MODULE_BASELINK" , $this -> moduleBaseLink);
        $smarty -> assign("T_CERTIFICATES_CURRENTLESSON", $this -> getCurrentLesson());
        return $this -> moduleBaseDir . "module.tpl";
    }

    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getLessonModule() {
        return false;
    }

    public function getControlPanelSmartyTpl() {
        return false;
    }

    /* CURRENT-LESSON ATTACHED MODULE PAGES */
    public function getControlPanelModule() {
        return false;
    }

    public function getLessonSmartyTpl() {
        return false;
    }
}
?>