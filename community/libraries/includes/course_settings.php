<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if (isset($currentUser -> coreAccess['course_settings']) && $currentUser -> coreAccess['course_settings'] == 'hidden') {
 eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}


$loadScripts[] = 'scriptaculous/controls';
$loadScripts[] = 'includes/course_settings';

$autocompleteImage = '16x16/certificate.png';

 $autocompleteImage = '16x16/autocomplete.png';

$options = array();
$options['information'] = array('image' => '16x16/information.png', 'title' => _INFORMATION, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_info', 'selected' => $_GET['op'] != 'course_info' ? false : true);
$options['completion'] = array('image' => $autocompleteImage, 'title' => _COMPLETION, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_certificates', 'selected' => ($_GET['op'] != 'course_certificates' && $_GET['op'] != 'format_certificate' && $_GET['op'] != 'format_certificate_docx') ? false : true);
$options['rules'] = array('image' => '16x16/rules.png', 'title' => _RULES, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_rules', 'selected' => $_GET['op'] != 'course_rules' ? false : true);
$options['order'] = array('image' => '16x16/order.png', 'title' => _ORDER, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_order', 'selected' => $_GET['op'] != 'course_order' ? false : true);
$options['schedule'] = array('image' => '16x16/calendar.png', 'title' => _SCHEDULING, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_scheduling', 'selected' => $_GET['op'] != 'course_scheduling' ? false : true);
if (!isset($currentUser -> coreAccess['course_settings']) || $currentUser -> coreAccess['course_settings'] == 'change') {
 $options['export'] = array('image' => '16x16/export.png', 'title' => _EXPORT, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=export_course', 'selected' => $_GET['op'] != 'export_course' ? false : true);
 $options['import'] = array('image' => '16x16/import.png', 'title' => _IMPORT, 'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=import_course', 'selected' => $_GET['op'] != 'import_course' ? false : true);
}
$moduleTabPages = array();
foreach ($currentUser -> getModules() as $module) {
 if ($moduleTabPage = $module -> getTabPageSmartyTpl('course_settings')) {
  $moduleTabPages[] = $moduleTabPage;
  $options[$moduleTabPage['tab_page']] = array('image' => $moduleTabPage['image'],
               'title' => $moduleTabPage['title'],
               'link' => basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op='.$moduleTabPage['tab_page'],
               'selected' => $_GET['op'] != $moduleTabPage['tab_page'] ? false : true,
               'absoluteImagePath' => 1);
 }
 if ($_GET['op'] == $moduleTabPage['tab_page']) {
  $smarty -> assign("T_MODULE_TABPAGE", $moduleTabPage);
 }
}
$smarty -> assign("T_MODULE_COURSE_SETTINGS_TABPAGES", $moduleTabPages);

$smarty -> assign("T_TABLE_OPTIONS", $options);

if ($currentUser -> user['user_type'] == 'administrator') {
 $smarty -> assign("T_COURSE_OPTIONS", array(array('text' => _EDITCOURSE, 'image' => "16x16/edit.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=courses&edit_course=".$_GET['course'])));
}

if ($_GET['op'] == 'course_info') {
 $form = new HTML_QuickForm("empty_form", "post", null, null, null, true);

 $courseInformation = unserialize($currentCourse -> course['info']);
 $information = new LearningObjectInformation($courseInformation);
 if (!isset($currentUser -> coreAccess['course_settings']) || $currentUser -> coreAccess['course_settings'] == 'change') {
  $smarty -> assign("T_COURSE_INFO_HTML", $information -> toHTML($form, false));
 } else {
  $smarty -> assign("T_COURSE_INFO_HTML", $information -> toHTML($form, false, false));
 }

 $courseMetadata = unserialize($currentCourse -> course['metadata']);
 $metadata = new DublinCoreMetadata($courseMetadata);
 if (!isset($currentUser -> coreAccess['course_settings']) || $currentUser -> coreAccess['course_settings'] == 'change') {
  $smarty -> assign("T_COURSE_METADATA_HTML", $metadata -> toHTML($form));
 } else {
  $smarty -> assign("T_COURSE_METADATA_HTML", $metadata -> toHTML($form, true, false));
 }

 if (isset($_POST['postAjaxRequest'])) {
  if (in_array($_POST['dc'], array_keys($information -> metadataAttributes))) {
   if ($_POST['value']) {
    $courseInformation[$_POST['dc']] = urldecode($_POST['value']);
   } else {
    unset($courseInformation[$_POST['dc']]);
   }
   $currentCourse -> course['info'] = serialize($courseInformation);
  } elseif (in_array($_POST['dc'], array_keys($metadata -> metadataAttributes))) {
   if ($_POST['value']) {
    $courseMetadata[$_POST['dc']] = urldecode($_POST['value']);
   } else {
    unset($courseMetadata[$_POST['dc']]);
   }
   $currentCourse -> course['metadata'] = serialize($courseMetadata);
  }

  $currentCourse -> persist();
  $value = htmlspecialchars(rawurldecode($_POST['value']));
  $value = str_replace ("\n","<br />", $value);
  echo $value;
  exit;
 }

} else if ($_GET['op'] == 'course_certificates') {
 $load_editor = 1;
 $defaultConstraints = array('active' => true, 'instance' => false);
 //$users = $currentCourse -> getCourseUsers($constraints);

 $smarty->assign('T_CERTIFICATE_EXPORT_METHOD', $currentCourse->options['certificate_export_method']);
 /*
		$users = EfrontStats::getUsersCourseStatus($currentCourse);
		$users = $users[$currentCourse -> course['id']];
		*/
 $rolesBasic = EfrontLessonUser :: getLessonsRoles();
 $studentRoles = array();
 foreach ($rolesBasic as $key => $role) {
  $role != 'student' OR $studentRoles[] = $key;
 }
 if (isset($_GET['edit_user']) && in_array($_GET['edit_user'], array_keys($users = $currentCourse -> getCourseUsers($defaultConstraints)))) {
  //$userStats = EfrontStats::getUsersCourseStatus($currentCourse, $_GET['edit_user']);
  $user = $users[$_GET['edit_user']];
  //pr($user -> getUserLessons());exit;
  $form = new HTML_QuickForm("edit_user_complete_course_form", "post", basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_certificates&edit_user='.$_GET['edit_user'].'&popup=1', "", null, true);
  $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter

  $form -> addElement('advcheckbox', 'completed', _COMPLETED, null, 'class = "inputCheckbox"'); //Whether the user has completed the course
  $form -> addElement('text', 'score', _SCORE, 'class = "inputTextScore"'); //The user course score
  $form -> addRule('score', _THEFIELD.' "'._SCORE.'" '._ISMANDATORY, 'required', null, 'client');
  $form -> addRule('score', _THEFIELD.' "'._SCORE.'" '._MUSTBENUMERIC, 'numeric', null, 'client'); //The score must be numeric
  $form -> addRule('score', _RATEMUSTBEBETWEEN0100, 'callback', create_function('$a', 'return ($a >= 0 && $a <= 100);')); //The score must be between 0 and 100

  $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputContentTextarea simpleEditor" style = "width:100%;height:5em;"'); //Comments on student's performance
  $form -> addElement('submit', 'submit_course_complete', _SUBMIT, 'class = "flatButton"'); //The submit button

  //pr($currentCourse -> getCourseLessons());exit;
  $userCourseLessonsStatus = $user -> getUserStatusInCourseLessons($currentCourse);
  $totalScore = 0;
  foreach ($userCourseLessonsStatus as $lesson) {
   $totalScore += $lesson -> lesson['score'] / sizeof($userCourseLessonsStatus);
  }
  $smarty -> assign("T_USER_COURSE_LESSON_STATUS", $userCourseLessonsStatus);
  $smarty -> assign("T_USER_COURSE", $user);

  $form -> setDefaults(array("completed" => $user -> user['completed'],
            "score" => $user -> user['completed'] ? $user -> user['score'] : round($totalScore),
            "comments" => $user -> user['comments']));
  if ($user -> user['to_timestamp']) {
      $smarty -> assign("T_TO_TIMESTAMP", $user -> user['to_timestamp']);
  } else {
      $smarty -> assign("T_TO_TIMESTAMP", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
  }

  if ($form -> isSubmitted() && $form -> validate()) {
   $toTimestamp = mktime($_POST['completion_Hour'], $_POST['completion_Minute'], 0, $_POST['completion_Month'], $_POST['completion_Day'], $_POST['completion_Year']);
   if ($form -> exportValue('completed')) {
    $courseUser = EfrontUserFactory :: factory($_GET['edit_user'], false, 'student');
    $courseUser -> completeCourse($currentCourse -> course['id'], $form -> exportValue('score'), $form -> exportValue('comments'), $toTimestamp);
   } else {
    $fields = array("completed" => 0,
         "score" => 0,
         "issued_certificate" => '',
         "to_timestamp" => null,
         "comments" => '');

    $where = "users_LOGIN = '".$_GET['edit_user']."' and courses_ID=".$currentCourse -> course['id'];
    EfrontCourse::persistCourseUsers($fields, $where, $currentCourse -> course['id'], $_GET['edit_user']);
    if ($user -> user['issued_certificate'] != "") {
     EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_CERTIFICATE_REVOKE, "users_LOGIN" => $_GET['edit_user'], "lessons_ID" => $currentCourse -> course['id'], "lessons_name" => $currentCourse -> course['name']));
    }
   }

   $message = _STUDENTSTATUSCHANGED;
   $message_type = 'success';
  }
  $renderer = prepareFormRenderer($form);
  $smarty -> assign('T_COMPLETE_COURSE_FORM', $renderer -> toArray());

 } else if (isset($_GET['issue_certificate']) && in_array($_GET['issue_certificate'], array_keys($users = $currentCourse -> getCourseUsers($defaultConstraints)))) {
  try {
   $certificate = $currentCourse -> prepareCertificate($_GET['issue_certificate']);
   $currentCourse -> issueCertificate($_GET['issue_certificate'], $certificate);
   eF_redirect(''.basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_certificates&reset_popup=1&&message='.urlencode(_CERTIFICATEISSUEDSUCCESFULLY).'&message_type=success');
  } catch (Exception $e) {
   $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
   $message = _PROBLEMISSUINGCERTIFICATE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
   $message_type = 'failure';
  }
 } else if (isset($_GET['revoke_certificate']) && in_array($_GET['revoke_certificate'], array_keys($users = $currentCourse -> getCourseUsers($defaultConstraints)))) {
  try {
   $currentCourse -> revokeCertificate($_GET['revoke_certificate']);
   eF_redirect(''.basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_certificates&reset_popup=1&message='.urlencode(_CERTIFICATEREVOKED).'&message_type=success');
  } catch (Exception $e) {
   $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
   $message = _PROBLEMREVOKINGCERTIFICATE.': '.$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
   $message_type = 'failure';
  }
 } else if (isset($_GET['reset_keep']) && in_array($_GET['reset_keep'], array_keys($users = $currentCourse -> getCourseUsers($defaultConstraints)))) {
  try {
   $user = EfrontUserFactory :: factory($_GET['reset_keep']);
   $user -> resetProgressInCourse($currentCourse, true, true);
   eF_redirect(''.basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_certificates&reset_popup=1&message='.urlencode(_PROGRESSRESETSUCCESSFULLY).'&message_type=success');
  } catch (Exception $e) {
   $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
   $message = _PROBLEMRESETINGPROGRESS.': '.$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
   $message_type = 'failure';
  }
 } else if (isset($_GET['change_key']) && in_array($_GET['login'], array_keys($users = $currentCourse -> getCourseUsers($defaultConstraints)))) {
  try {
   $result = eF_getTableData("users_to_courses", "users_LOGIN,issued_certificate", "courses_ID = ".$currentCourse -> course['id']. " and users_LOGIN = '".$_GET['login']."'");
   $issued_certificate = unserialize($result[0]['issued_certificate']);
   if ($issued_certificate) {
    $issued_certificate['serial_number'] = $_GET['change_key'];
    eF_updateTableData("users_to_courses", array('issued_certificate' => serialize($issued_certificate)), "courses_ID = ".$currentCourse -> course['id']. " and users_LOGIN = '".$_GET['login']."'");
    echo json_encode(array('key' => $_GET['change_key']));
   }
   exit;
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
 } else if (isset($_GET['auto_complete'])) {
  try {
   if ($currentCourse -> options['auto_complete']) {
    $currentCourse -> options['auto_complete'] = 0;
    $currentCourse -> options['auto_certificate'] = 0;
   } else {
    $currentCourse -> options['auto_complete'] = 1;
   }
   $currentCourse -> persist();
   foreach ($currentCourse -> getCourseLessons() as $lesson) {
    $lesson -> options['auto_complete'] = $currentCourse -> options['auto_complete'];
    $lesson -> persist();
   }
   $ajaxResult = new AjaxResultObject($currentCourse -> options['auto_complete'], _OPERATIONCOMPLETEDSUCCESFULLY);
   $ajaxResult -> display();
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
  exit;
 } else if (isset($_GET['auto_certificate'])) {
  try {
   if ($currentCourse -> options['auto_certificate']) {
    $currentCourse -> options['auto_certificate'] = 0;
   } else {
    $currentCourse -> options['auto_certificate'] = 1;
   }
   $currentCourse -> persist();
   echo $currentCourse -> options['auto_certificate'];
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
  exit;
 } else if (isset($_GET['CertificateAll'])) {
  try {
   $users = $currentCourse -> getCourseUsers($defaultConstraints);
   foreach ($users as $key => $value) {
    if ($value -> user['completed'] && !$value -> user['issued_certificate']) {
     $certificate = $currentCourse -> prepareCertificate($key);
     $currentCourse -> issueCertificate($key, $certificate);
    }
   }
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
  exit;
 } else if (isset($_GET['revoke_all_expired'])) {
 } else if (isset($_GET['set_all_completed'])) {
  try {
   $constraints = array('archive' => false, 'active' => true) + createConstraintsFromSortedTable();
   $constraints['condition'] = "uc.user_type in ('".implode("','", $studentRoles)."')";
   $users = $currentCourse -> getCourseUsers($constraints);
   foreach ($users as $user) {
    if (!$user -> user['completed']) {
     $user -> completeCourse($currentCourse, 100);
    }
   }
   echo json_encode(array('status' => true));
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
  exit;
 } else if (isset($_GET['reset_all'])) {
  try {
   $constraints = array('archive' => false, 'active' => true) + createConstraintsFromSortedTable();
   $constraints['condition'] = "uc.user_type in ('".implode("','", $studentRoles)."')";
   $users = $currentCourse -> getCourseUsers($constraints);
   foreach ($users as $user) {
    $user -> resetProgressInCourse($currentCourse, true);
   }
   echo json_encode(array('status' => true));
  } catch (Exception $e) {
   handleAjaxExceptions($e);
  }
  exit;
 }
 $smarty -> assign("T_BASIC_ROLES_ARRAY", $rolesBasic);
 if (isset($_GET['ajax']) && $_GET['ajax'] == 'courseUsersTable') {
  //pr($studentRoles);
  $smarty -> assign("T_DATASOURCE_COLUMNS", array('login', 'active_in_course', 'completed', 'to_timestamp', 'score', 'issued_certificate', 'expire_certificate', 'operations'));
  $smarty -> assign("T_DATASOURCE_SORT_BY", 0);
  $constraints = array('archive' => false, 'active' => true) + createConstraintsFromSortedTable();
  $constraints['condition'] = "uc.user_type in ('".implode("','", $studentRoles)."')";
  $users = $currentCourse -> getCourseUsers($constraints);
  $totalEntries = $currentCourse -> countCourseUsers($constraints);
  $smarty -> assign("T_TABLE_SIZE", $totalEntries);
  foreach ($users as $key => $value) {
   $users[$key] -> user['issued_certificate'] = $value -> user['issued_certificate'];
   $expire_certificateTimestamp = "";
   if ($value -> user['issued_certificate']) {
    $issuedData = unserialize($value -> user['issued_certificate']);
    $users[$key] -> user['serial_number'] = $issuedData['serial_number'];
    //$dateFormat = eF_dateFormat();
    if (eF_checkParameter($issuedData['date'], 'timestamp')) {
     //$expire_certificateTimestamp = $currentCourse -> course['certificate_expiration'] + $issuedData['date'];
     //$dateExpire = date($dateFormat, $expire_certificateTimestamp);
     $expirationArray = convertTimeToDays($currentCourse -> course['certificate_expiration']);
     $expire_certificateTimestamp = getCertificateExpirationTimestamp($issuedData['date'], $expirationArray);
    } else {
     $expire_certificateTimestamp = $currentCourse -> course['certificate_expiration'] + strtotime($issuedData['date']);
     //$dateExpire = date($dateFormat, $expire_certificateTimestamp);
    }
    if (isset($currentCourse -> course['certificate_expiration']) && $currentCourse -> course['certificate_expiration'] != 0) {
     $users[$key] -> user['expire_certificate'] = $expire_certificateTimestamp;
    }
   }
  }
  $users = EfrontCourse :: convertUserObjectsToArrays($users);
  $dataSource = $users;
  $tableName = $_GET['ajax'];
  $alreadySorted = true;
  include("sorted_table.php");
 }
 if (isset($_GET['export']) && $_GET['export'] == 'rtf' && eF_checkParameter($_GET['course'], 'id') ) {
  if (eF_checkParameter($_GET['user'], 'login')) {
   $result = eF_getTableData("users_to_courses", "*", "users_LOGIN = '".$_GET['user']."' and courses_ID = '".$_GET['course']."' limit 1");
  } else {
   $result = array();
  }
  if (sizeof($result) == 1 || isset($_GET['preview'])) {
   $course = new EfrontCourse($_GET['course']);
   if (!isset($_GET['preview'])){
    $certificate_tpl_id_rtf = $course -> options['certificate_tpl_id_rtf'];
    if ($certificate_tpl_id_rtf <= 0) {
     $cfile = new EfrontFile(G_CERTIFICATETEMPLATEPATH."certificate1.rtf");
    } else {
     $cfile = new EfrontFile($certificate_tpl_id_rtf);
    }
    $template_data = file_get_contents($cfile['path']);
    $issued_data = unserialize($result[0]['issued_certificate']);
    if (sizeof($issued_data) > 1){
     $certificate = $template_data;
     $certificate = str_replace("#organization#", utf8ToUnicode($issued_data['organization']), $certificate);
     $certificate = str_replace("#user_name#", utf8ToUnicode($issued_data['user_name']), $certificate);
     $certificate = str_replace("#user_surname#", utf8ToUnicode($issued_data['user_surname']), $certificate);
     $certificate = str_replace("#course_name#", utf8ToUnicode($issued_data['course_name']), $certificate);
     $certificate = str_replace("#grade#", utf8ToUnicode($issued_data['grade']), $certificate);
     if (eF_checkParameter($issued_data['date'], 'timestamp')) {
      $issued_data['date'] = formatTimestamp($issued_data['date']);
     }
     $certificate = str_replace("#date#", utf8ToUnicode($issued_data['date']), $certificate);
     $certificate = str_replace("#serial_number#", utf8ToUnicode($issued_data['serial_number']), $certificate);
    }
    $filename = "certificate_".$_GET['user'].".rtf";
   } else {
    $certificateDirectory = G_CERTIFICATETEMPLATEPATH;
    $selectedCertificate = $_GET['certificate_tpl'];
    $certificate = file_get_contents($certificateDirectory.$selectedCertificate);
    $filename = $_GET['certificate_tpl'];
   }
   $webserver = explode(' ',$_SERVER['SERVER_SOFTWARE']); //GET Server information from $_SERVER
   $webserver_type = explode('/', $webserver[0]);
   $filenameRtf = "certificate_".$_GET['user'].".rtf";
   $filenamePdf = G_ROOTPATH."www/phplivedocx/samples/mail-merge/convert/certificate_".$_GET['user'].".pdf";
   file_put_contents(G_ROOTPATH."www/phplivedocx/samples/mail-merge/convert/certificate_".$_GET['user'].".rtf", $certificate);
   if (mb_stripos($webserver_type[0], "IIS") === false) { //because of note here http://php.net/manual/en/function.file.php
    $retValues = file(G_SERVERNAME."phplivedocx/samples/mail-merge/convert/convert-document.php?filename=certificate_".$_GET['user']);
   } else {
    $retValues[0] == "false";
   }
   if ($retValues[0] == "true") {
    header("Content-type: application/pdf");
    header("Content-disposition: inline; filename=$filenamePdf");
    $filePdf = file_get_contents($filenamePdf);
    header("Content-length: " . strlen($filePdf));
    echo $filePdf;
    exit(0);
   } else {
    header("Content-type: application/rtf");
    header("Content-disposition: inline; filename=$filenameRtf");
    if (stristr($webserver_type[0], "IIS") === false) { //for IIS 7.x
     header("Content-length: " . strlen($certificate));
    }
    echo $certificate;
    exit(0);
   }
  }
 }
 if (isset($_GET['export']) && $_GET['export'] == 'xml' && eF_checkParameter($_GET['course'], 'id') ) {
  if (eF_checkParameter($_GET['user'], 'login')) {
   $result = eF_getTableData("users_to_courses", "*", "users_LOGIN = '".$_GET['user']."' and courses_ID = '".$_GET['course']."' limit 1");
  } else {
   $result = array();
  }
  if(sizeof($result) == 1 || isset($_GET['preview'])){
   $course = new EfrontCourse($_GET['course']);
   if(!isset($_GET['preview'])){
    $certificate_tpl_id = $course->options['certificate_tpl_id'];
    if($certificate_tpl_id <= 0){
     $mainTemplate = eF_getTableData("certificate_templates", "id",
          "certificate_name='".CERTIFICATES_MAIN_TEMPLATE_NAME."'"); // XXX
     $certificate_tpl_id = $mainTemplate[0]['id'];
    }
    $issued_data = unserialize($result[0]['issued_certificate']);
    $templateData = eF_getTableData("certificate_templates", "certificate_xml", "id=".$certificate_tpl_id);
    $userName = $issued_data['user_name'];
    $userSurName = $issued_data['user_surname'];
    $courseName = $issued_data['course_name'];
    $courseGrade = $issued_data['grade'];
    $serialNumber = $issued_data['serial_number'];
    if (eF_checkParameter($issued_data['date'], 'timestamp'))
    $certificateDate = formatTimestamp($issued_data['date']);
    if ($course -> course['certificate_expiration'] != 0) {
     $expirationArray = convertTimeToDays($course -> course['certificate_expiration']);
     $expire_certificateTimestamp = getCertificateExpirationTimestamp($issued_data['date'], $expirationArray);
     $expireDate = formatTimestamp($expire_certificateTimestamp);
    }
    $xmlExport = new XMLExport($templateData[0]['certificate_xml']);
    $creator = $xmlExport->getCreator();
    $author = $xmlExport->getAuthor();
    $subjct = $xmlExport->getSubject($userName.' '.$userSurName);
    $keywrd = $xmlExport->getKeywords();
    $orientation = $xmlExport->getOrientation();
    $pdf = new TCPDF($orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator($creator);
    $pdf->SetAuthor($author);
    $pdf->SetTitle($subjct);
    $pdf->SetSubject($subjct);
    $pdf->SetKeywords($keywrd);
    $xmlExport->setBackground($pdf);
    $pdf->SetAutoPageBreak(false);
    $pdf->setFontSubsetting(false);
    $pdf->AddPage();
    //				$pdf->AddFont('Garamond','', K_PATH_FONTS.'gara.php');
    //				$pdf->AddFont('Garamond','B', K_PATH_FONTS.'garabd.php');
    $xmlExport->drawLines($pdf);
    $xmlExport->showLabels($pdf);
    if (extension_loaded('gd')) {
     $xmlExport->showImages($pdf);
     $xmlExport->showLogo($pdf);
    }
    $xmlExport->showOrganization($pdf);
    $xmlExport->showDate($pdf, $certificateDate);
    $xmlExport->showSerialNumber($pdf, $serialNumber);
    $xmlExport->showStudentName($pdf, $userName.' '.$userSurName);
    $xmlExport->showCourseName($pdf, $courseName);
    $xmlExport->showGrade($pdf, $courseGrade);
    if ($course -> course['certificate_expiration'] != 0) {
     $xmlExport->showExpireDate($pdf, $expireDate);
    }
    if ($course -> options['custom1'] != '') {
     $course -> options['custom1'] = replaceCustomFieldsCertificate($course -> options['custom1'], $issued_data['date'], $_GET['user'], $course -> course['ceu'], $course ->options['training_hours']);
     $xmlExport->showCustomOne($pdf, $course -> options['custom1']);
    }
    if ($course -> options['custom2'] != '') {
     $course -> options['custom2'] = replaceCustomFieldsCertificate($course -> options['custom2'], $issued_data['date'], $_GET['user'], $course -> course['ceu'], $course ->options['training_hours']);
     $xmlExport->showCustomTwo($pdf, $course -> options['custom2']);
    }
    if ($course -> options['custom3'] != '') {
     $course -> options['custom3'] = replaceCustomFieldsCertificate($course -> options['custom3'], $issued_data['date'], $_GET['user'], $course -> course['ceu'], $course ->options['training_hours']);
     $xmlExport->showCustomThree($pdf, $course -> options['custom3']);
    }
    //				$fileNamePdf = "certificate_".$_GET['user'].".pdf";
    //				$pdf->Output($fileNamePdf, 'D');
    $fileNamePdf = $course -> course['name']."_".$_GET['user'].".pdf";
   }
   else{
    $tmp = explode('-', $_GET['certificate_tpl']);
    $certificate_tpl_id = $tmp[0];
    $templateData = eF_getTableData("certificate_templates", "certificate_xml", "id=".$certificate_tpl_id);
    $xmlExport = new XMLExport($templateData[0]['certificate_xml']);
    $creator = $xmlExport->getCreator();
    $author = $xmlExport->getAuthor();
    $subjct = $xmlExport->getSubject();
    $keywrd = $xmlExport->getKeywords();
    $orientation = $xmlExport->getOrientation();
    $pdf = new TCPDF($orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator($creator);
    $pdf->SetAuthor($author);
    $pdf->SetTitle($subjct);
    $pdf->SetSubject($subjct);
    $pdf->SetKeywords($keywrd);
    $xmlExport->setBackground($pdf);
    $pdf->SetAutoPageBreak(false);
    $pdf->setFontSubsetting(false);
    $pdf->AddPage();
    $xmlExport->drawLines($pdf);
    $xmlExport->showLabels($pdf);
    $xmlExport->showImages($pdf);
    $xmlExport->showLogo($pdf);
    $xmlExport->showOrganization($pdf);
    $xmlExport->showDate($pdf, formatTimestamp(time()));
    $xmlExport->showSerialNumber($pdf, 'Serial Number');
    $xmlExport->showStudentName($pdf, 'Student Name');
    $xmlExport->showCourseName($pdf, 'Course Name');
    $xmlExport->showGrade($pdf, 'Grade');
    if ($course -> options['custom1'] != '') {
     $course -> options['custom1'] = replaceCustomFieldsCertificate($course -> options['custom1'], $issued_data['date']);
     $xmlExport->showCustomOne($pdf, $course -> options['custom1']);
    }
    if ($course -> options['custom2'] != '') {
     $course -> options['custom2'] = replaceCustomFieldsCertificate($course -> options['custom2'], $issued_data['date']);
     $xmlExport->showCustomTwo($pdf, $course -> options['custom2']);
    }
    if ($course -> options['custom3'] != '') {
     $course -> options['custom3'] = replaceCustomFieldsCertificate($course -> options['custom3'], $issued_data['date']);
     $xmlExport->showCustomThree($pdf, $course -> options['custom3']);
    }
    //				$fileNamePdf = "certificate_preview.pdf";
    //				$pdf->Output($fileNamePdf, 'D');
    $fileNamePdf = "certificate_preview.pdf";
   }
   $output = $pdf->Output('', 'S');
   file_put_contents($currentUser->getDirectory().$fileNamePdf, $output);
   $file = new EfrontFile($currentUser->getDirectory().$fileNamePdf, $output);
   $file -> sendFile();
  }
 }
} else if ($_GET['op'] == 'format_certificate'){
 if($currentCourse->options['certificate_export_method'] == 'rtf' && !isset($_GET['switch']))
 eF_redirect(basename($_SERVER['PHP_SELF'])."?".$baseUrl."&op=format_certificate_docx");
} else if ($_GET['op'] == 'format_certificate_docx') {
} else if($_GET['op'] == 'add_certificate_template' || $_GET['op'] == 'edit_certificate_template'){
} else if($_GET['op'] == 'rename_certificate_template'){
} else if($_GET['op'] == 'clone_certificate_template'){
} else if($_GET['op'] == 'delete_certificate_template'){
} else if ($_GET['op'] == 'course_rules') {
 $courseLessons = $currentCourse -> getCourseLessons();
 if ($currentCourse -> course['depends_on']) {
  try {
   $dependsOn = new EfrontCourse($currentCourse -> course['depends_on']);
   $smarty -> assign("T_DEPENDSON_COURSE", $dependsOn->course['name']);
  } catch (Exception $e) {}
 }
 $rules_form = new HTML_QuickForm("course_rules_form", "post", basename($_SERVER['PHP_SELF'])."?".$baseUrl."&op=course_rules", "", null, true);
 if (isset($currentUser -> coreAccess['course_settings']) && $currentUser -> coreAccess['course_settings'] != 'change') {
  $rules_form -> freeze();
 } else {
  $rules_form -> addElement('submit', 'submit_rule', _SUBMIT, 'class = "flatButton"');
  if ($rules_form -> isSubmitted() && $rules_form -> validate()) {
   foreach ($_POST['rules'] as $rule_lesson) {
    if (sizeof(array_unique($rule_lesson['lesson'])) != sizeof($rule_lesson['lesson'])) {
     $duplicate = true;
    }
   }
   if (!isset($duplicate)) {
    try {
     $currentCourse -> rules = $_POST['rules'];
     $currentCourse -> persist();
     eF_redirect("".basename($_SERVER['PHP_SELF'])."?".$baseUrl."&op=course_rules&message=".urlencode(_SUCCESFULLYSETORDER)."&message_type=success");
    } catch (Exception $e) {
     $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
     $message = _PROBLEMSETTINGORDER.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
     $message_type = 'failure';
    }
   } else {
    $message = _DUPLICATESARENOTALLOWED;
    $message_type = 'failure';
   }
  }
  if (isset($_GET['ajax']) && isset($_GET['inter_course_rule'])) {
   try {
    if ($_GET['inter_course_rule']) {
     $dependsOn = new EfrontCourse($_GET['inter_course_rule']);
     if ($dependsOn -> course['id'] != $currentCourse -> course['id']) {
      $currentCourse -> course['depends_on'] = $dependsOn -> course['id'];
     } else {
      throw new Exception(_YOUCANNOTSETSAMECOURSEASRULE);
     }
    } else {
     $currentCourse -> course['depends_on'] = 0;
    }
    $currentCourse -> persist();
   } catch (Exception $e) {
    handleAjaxExceptions($e);
   }
  }
 }
 $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
 $rules_form -> accept($renderer);
 $smarty -> assign('T_COURSE_RULES_FORM', $renderer -> toArray());
 $smarty -> assign("T_COURSE_RULES", $currentCourse -> rules);
 $smarty -> assign('T_COURSE', $currentCourse -> course);
 $smarty -> assign("T_COURSE_LESSONS", EfrontCourse::convertLessonObjectsToArrays($courseLessons));
} else if ($_GET['op'] == 'course_order') {
 $courseLessons = $currentCourse -> getCourseLessons();
 $smarty -> assign('T_COURSE', $currentCourse -> course);
 $smarty -> assign('T_COURSE_LESSONS', EfrontCourse::convertLessonObjectsToArrays($courseLessons));
 if (isset($_GET['ajax']) && isset($_GET['order'])) {
  try {
   $order = explode(",", $_GET['order']);
   $previous = 0;
   foreach ($order as $value) {
    $result = explode("-", $value);
    if (in_array($value, array_keys($courseLessons))) {
     $fields = array("previous_lessons_ID" => $previous);
     $where = "courses_ID=".$currentCourse -> course['id']." and lessons_ID=".$result[0];
     EfrontCourse::persistCourseLessons($fields, $where);
    }
    $previous = $result[0];
   }
   echo _TREESAVEDSUCCESSFULLY;
  } catch (Exception $e) {
   header("HTTP/1.0 500");
   echo $e -> getMessage().' ('.$e -> getCode().')';
  }
  exit;
 }
} else if ($_GET['op'] == 'course_scheduling') {
 $courseLessons = $currentCourse -> getCourseLessons();
 $smarty -> assign("T_CURRENT_COURSE", $currentCourse);
 try {
  if (isset($_GET['set_schedule']) && in_array($_GET['set_schedule'], array_keys($courseLessons))) {
   $lesson = new EfrontLesson($_GET['set_schedule']);
   $fromTimestamp = mktime($_GET['from_Hour'], $_GET['from_Minute'], 0, $_GET['from_Month'], $_GET['from_Day'], $_GET['from_Year']);
   $toTimestamp = mktime($_GET['to_Hour'], $_GET['to_Minute'], 0, $_GET['to_Month'], $_GET['to_Day'], $_GET['to_Year']);
   if ($fromTimestamp < $toTimestamp) {
    $currentCourse -> setLessonScheduleInCourse($lesson, $fromTimestamp, $toTimestamp);
    /*
				 $lesson -> lesson['from_timestamp'] = $fromTimestamp;
				 $lesson -> lesson['to_timestamp']   = $toTimestamp;
				 $lesson -> persist();

				 eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_START . "_" . $lesson -> lesson['id']. "'");
				 eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_EXPIRY . "_" . $lesson -> lesson['id']. "'");

				 EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_PROGRAMMED_START,  "timestamp" => $lesson -> lesson['from_timestamp'], "lessons_ID" => $lesson -> lesson['id'], "lessons_name" => $lesson -> lesson['name']));
				 EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_PROGRAMMED_EXPIRY, "timestamp" => $lesson -> lesson['to_timestamp'],   "lessons_ID" => $lesson -> lesson['id'], "lessons_name" => $lesson -> lesson['name']));
				 */
    echo _FROM.' '.formatTimestamp($fromTimestamp, 'time_nosec').' '._TO.' '.formatTimestamp($toTimestamp, 'time_nosec').'&nbsp;';
   } else {
    header("HTTP/1.0 500");
    echo _ENDDATEMUSTBEBEFORESTARTDATE;
   }
   exit;
  } else if (isset($_GET['delete_schedule']) && in_array($_GET['delete_schedule'], array_keys($courseLessons))) {
   $lesson = new EfrontLesson($_GET['delete_schedule']);
   $currentCourse -> unsetLessonScheduleInCourse($lesson);
   /*
			 $lesson = new EfrontLesson($_GET['delete_schedule']);
			 $lesson -> lesson['from_timestamp'] = null;
			 $lesson -> lesson['to_timestamp']   = null;
			 $lesson -> lesson['shift']          = 0;

			 $lesson -> persist();

			 // @TODO maybe proper class internal invalidation
			 eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_START . "_" . $lesson -> lesson['id']. "'");
			 eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_EXPIRY . "_" . $lesson -> lesson['id']. "'");
			 */
   exit;
  } else if (isset($_GET['set_schedule']) && $_GET['set_schedule'] == 0) {
   $previous_start_date = $currentCourse -> course['start_date'];
   $previous_end_date = $currentCourse -> course['end_date'];
   $fromTimestamp = mktime($_GET['from_Hour'], $_GET['from_Minute'], 0, $_GET['from_Month'], $_GET['from_Day'], $_GET['from_Year']);
   $toTimestamp = mktime($_GET['to_Hour'], $_GET['to_Minute'], 0, $_GET['to_Month'], $_GET['to_Day'], $_GET['to_Year']);
   if ($fromTimestamp < $toTimestamp) {
    $currentCourse -> course['start_date'] = $fromTimestamp;
    $currentCourse -> course['end_date'] = $toTimestamp;
    $currentCourse -> persist();
    if ($previous_start_date != $currentCourse -> course['start_date']) {
     eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::COURSE_PROGRAMMED_START . "_" . $currentCourse -> course['id']. "'");
     EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_PROGRAMMED_START, "timestamp" => $currentCourse -> course['start_date'], "lessons_ID" => $currentCourse -> course['id'], "lessons_name" => $currentCourse -> course['name']));
    }
    if ($previous_end_date != $currentCourse -> course['end_date']) {
     eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::COURSE_PROGRAMMED_EXPIRY . "_" . $currentCourse -> course['id']. "'");
     EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_PROGRAMMED_EXPIRY, "timestamp" => $currentCourse -> course['end_date'], "lessons_ID" => $currentCourse -> course['id'], "lessons_name" => $currentCourse -> course['name']));
    }
    echo _FROM.' '.formatTimestamp($fromTimestamp, 'time_nosec').' '._TO.' '.formatTimestamp($toTimestamp, 'time_nosec').'&nbsp;';
   } else {
    header("HTTP/1.0 500");
    echo _ENDDATEMUSTBEBEFORESTARTDATE;
   }
   exit;
  } else if (isset($_GET['delete_schedule']) && $_GET['delete_schedule'] == 0) {
   $currentCourse -> course['start_date'] = '';
   $currentCourse -> course['end_date'] = '';
   $currentCourse -> persist();
   eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::COURSE_PROGRAMMED_START . "_" . $currentCourse -> course['id']. "'");
   eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::COURSE_PROGRAMMED_EXPIRY . "_" . $currentCourse -> course['id']. "'");
  }
 } catch (Exception $e) {
  handleAjaxExceptions($e);
 }
 $smarty -> assign("T_COURSE_LESSONS", EfrontCourse::convertLessonObjectsToArrays($courseLessons));
 //pr($courseLessons);
} else if ($_GET['op'] == 'export_course') {
 if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
  eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
 }
 /* Export part */
 $form = new HTML_QuickForm("export_course_form", "post", basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=export_course', "", null, true);
 $form -> addElement('submit', 'submit_export_course', _EXPORT, 'class = "flatButton"');
 try {
  $currentExportedFile = new EfrontFile($currentUser -> user['directory'].'/temp/'.EfrontFile :: encode($currentCourse -> course['name']).'.zip');
  $smarty -> assign("T_EXPORTED_FILE", $currentExportedFile);
 } catch (Exception $e) {}
 if ($form -> isSubmitted() && $form -> validate()) {
  try {
   $file = $currentCourse -> export();
   $smarty -> assign("T_NEW_EXPORTED_FILE", $file);
   $message = _COURSEEXPORTEDSUCCESFULLY;
   $message_type = 'success';
  } catch (Exception $e) {
   $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
   $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
  }
 }
 $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
 $form -> accept($renderer);
 $smarty -> assign('T_EXPORT_COURSE_FORM', $renderer -> toArray());
} else if ($_GET['op'] == 'import_course') {
 if (isset($currentUser -> coreAccess['content']) && $currentUser -> coreAccess['content'] != 'change') {
  eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
 }
 /* Import part */
 $form = new HTML_QuickForm("import_course_form", "post", basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=import_course', "", null, true);
 $form -> addElement('file', 'file_upload', null, 'class = "inputText"'); //Lesson file
 $form -> setMaxFileSize(FileSystemTree :: getUploadMaxSize() * 1024); //getUploadMaxSize returns size in KB
 $form -> addElement('submit', 'submit_import_course', _SUBMIT, 'class = "flatButton"');
 $smarty -> assign("T_MAX_FILESIZE", FileSystemTree :: getUploadMaxSize());
 if ($form -> isSubmitted() && $form -> validate()) {
  try {
   $userTempDir = $GLOBALS['currentUser'] -> user['directory'].'/temp';
   if (!is_dir($userTempDir)) { //If the user's temp directory does not exist, create it
    $userTempDir = EfrontDirectory :: createDirectory($userTempDir, false);
   } else {
    $userTempDir = new EfrontDirectory($userTempDir);
   }
   $filesystem = new FileSystemTree($userTempDir);
   $uploadedFile = $filesystem -> uploadFile('file_upload', $userTempDir);
   $currentCourse -> import($uploadedFile);
   $message = _COURSEIMPORTEDSUCCESFULLY;
   $message_type = 'success';
  } catch (Exception $e) {
   $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
   $message = _PROBLEMIMPORTINGFILE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
   $message_type = 'failure';
  }
 }
 $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
 $form -> accept($renderer);
 $smarty -> assign('T_IMPORT_COURSE_FORM', $renderer -> toArray());
}
