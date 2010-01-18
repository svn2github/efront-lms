<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}


$loadScripts[] = 'includes/course_settings';

$options = array(array('image' => '16x16/information.png', 'title' => _INFORMATION, 'link' => $_GET['op'] != 'course_info' ? basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_info' : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_info' ? false : true));
$options[] = array('image' => '16x16/autocomplete.png', 'title' => _COMPLETION, 'link' => $_GET['op'] != 'course_certificate' ? basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_certificates' : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_certificates' ? false : true);
$options[] = array('image' => '16x16/rules.png', 'title' => _RULES, 'link' => $_GET['op'] != 'course_rules' ? basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_rules' : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_rules' ? false : true);
$options[] = array('image' => '16x16/order.png', 'title' => _ORDER, 'link' => $_GET['op'] != 'course_order' ? basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_order' : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_order' ? false : true);
$options[] = array('image' => '16x16/calendar.png', 'title' => _SCHEDULING, 'link' => $_GET['op'] != 'course_scheduling' ? basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_scheduling' : 'javascript:void(0)', 'selected' => $_GET['op'] != 'course_scheduling' ? false : true);
$options[] = array('image' => '16x16/export.png', 'title' => _EXPORT, 'link' => $_GET['op'] != 'export_course' ? basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=export_course' : 'javascript:void(0)', 'selected' => $_GET['op'] != 'export_course' ? false : true);
$options[] = array('image' => '16x16/import.png', 'title' => _IMPORT, 'link' => $_GET['op'] != 'import_course' ? basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=import_course' : 'javascript:void(0)', 'selected' => $_GET['op'] != 'import_course' ? false : true);
//pr($options);
$smarty -> assign("T_TABLE_OPTIONS", $options);

if ($currentUser -> user['user_type'] == 'administrator') {
    $smarty -> assign("T_COURSE_OPTIONS", array(array('text' => _EDITCOURSE, 'image' => "16x16/edit.png", 'href' => basename($_SERVER['PHP_SELF'])."?ctg=courses&edit_course=".$_GET['course'])));
}

if ($_GET['op'] == 'course_info') {
    $form = new HTML_QuickForm("empty_form", "post", null, null, null, true);

    $courseInformation = unserialize($currentCourse -> course['info']);
    $information = new LearningObjectInformation($courseInformation);
    $smarty -> assign("T_COURSE_INFO_HTML", $information -> toHTML($form, false));

    $courseMetadata = unserialize($currentCourse -> course['metadata']);
    $metadata = new DublinCoreMetadata($courseMetadata);
    $smarty -> assign("T_COURSE_METADATA_HTML", $metadata -> toHTML($form));

    if (isset($_GET['postAjaxRequest'])) {
        if (in_array($_GET['dc'], array_keys($information -> metadataAttributes))) {
            if ($_GET['value']) {
                $courseInformation[$_GET['dc']] = urldecode($_GET['value']);
            } else {
                unset($courseInformation[$_GET['dc']]);
            }
            $currentCourse -> course['info'] = serialize($courseInformation);
        } elseif (in_array($_GET['dc'], array_keys($metadata -> metadataAttributes))) {
            if ($_GET['value']) {
                $courseMetadata[$_GET['dc']] = urldecode($_GET['value']);
            } else {
                unset($courseMetadata[$_GET['dc']]);
            }
            $currentCourse -> course['metadata'] = serialize($courseMetadata);
        }

        $currentCourse -> persist();
        echo urldecode($_GET['value']);
        exit;
    }

} else if ($_GET['op'] == 'course_certificates') {

  $users = EfrontStats::getUsersCourseStatus($currentCourse);
  $users = $users[$currentCourse -> course['id']];
  if (isset($_GET['CertificateAll'])) {
   foreach ($users as $key => $value) {
    if ($value['completed'] == 1 && $value['issued_certificate'] == "") {
     $certificate = $currentCourse -> prepareCertificate($key);
     $currentCourse -> issueCertificate($key, $certificate);
    }
   }
   eF_redirect(basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_certificates');
   exit;
  }
  if (isset($_GET['edit_user']) && in_array($_GET['edit_user'], array_keys($users))) {
   $userStats = $users[$_GET['edit_user']];
   $form = new HTML_QuickForm("edit_user_complete_course_form", "post", basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_certificates&edit_user='.$_GET['edit_user'].'&popup=1', "", null, true);
   $form -> registerRule('checkParameter', 'callback', 'eF_checkParameter'); //Register this rule for checking user input with our function, eF_checkParameter

   $form -> addElement('advcheckbox', 'completed', _COMPLETED, null, 'class = "inputCheckbox"'); //Whether the user has completed the course
   $form -> addElement('text', 'score', _SCORE, 'class = "inputTextScore"'); //The user course score
   $form -> addRule('score', _THEFIELD.' "'._SCORE.'" '._ISMANDATORY, 'required', null, 'client');
   $form -> addRule('score', _THEFIELD.' "'._SCORE.'" '._MUSTBENUMERIC, 'numeric', null, 'client'); //The score must be numeric
   $form -> addRule('score', _RATEMUSTBEBETWEEN0100, 'callback', create_function('$a', 'return ($a >= 0 && $a <= 100);')); //The score must be between 0 and 100

   $form -> addElement('textarea', 'comments', _COMMENTS, 'class = "inputContentTextarea simpleEditor" style = "width:100%;height:5em;"'); //Comments on student's performance
   $form -> addElement('submit', 'submit_course_complete', _SUBMIT, 'class = "flatButton"'); //The submit button

   $totalScore = 0;
   foreach ($userStats['lesson_status'] as $stat) {
    $totalScore += $stat['score'] / sizeof($userStats['lesson_status']);
   }

   $form -> setDefaults(array("completed" => $userStats['completed'],
           "score" => $userStats['completed'] ? $userStats['score'] : round($totalScore),
           "comments" => $userStats['comments']));

   if ($form -> isSubmitted() && $form -> validate()) {
    if ($form -> exportValue('completed')) {
     //$fields = array("completed" 	=> 1,
     //                   "score"     => $form -> exportValue('score'),
     //                   "comments"  => $form -> exportValue('comments'));
     $courseUser = EfrontUserFactory :: factory($_GET['edit_user']);
     $courseUser -> completeCourse($currentCourse -> course['id'], $form -> exportValue('score'), $form -> exportValue('comments'));
    } else {
     $fields = array("completed" => 0,
          "score" => 0,
          "issued_certificate" => '',
          "to_timestamp" => null,
          "comments" => '');
     eF_updateTableData("users_to_courses", $fields, "users_LOGIN = '".$_GET['edit_user']."' and courses_ID=".$currentCourse -> course['id']);
     if ($userStats['issued_certificate'] != "") {
      EfrontEvent::triggerEvent(array("type" => EfrontEvent::COURSE_CERTIFICATE_REVOKE, "users_LOGIN" => $_GET['edit_user'], "lessons_ID" => $currentCourse -> course['id'], "lessons_name" => $currentCourse -> course['name']));
     }
    }
    //eF_updateTableData("users_to_courses", $fields, "users_LOGIN = '".$_GET['edit_user']."' and courses_ID=".$currentCourse -> course['id']);

    $message = _STUDENTSTATUSCHANGED;
    $message_type = 'success';
   }

   $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

   $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
   $form -> setRequiredNote(_REQUIREDNOTE);
   $form -> accept($renderer);

   $smarty -> assign('T_COMPLETE_COURSE_FORM', $renderer -> toArray());
   $smarty -> assign("T_USER_PROGRESS", $userStats);
  } else if (isset($_GET['issue_certificate']) && in_array($_GET['issue_certificate'], array_keys($users))) {
   try {
    $certificate = $currentCourse -> prepareCertificate($_GET['issue_certificate']);
    $currentCourse -> issueCertificate($_GET['issue_certificate'], $certificate);
    eF_redirect(''.basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_certificates&reset_popup=1&&message='.urlencode(_CERTIFICATEISSUEDSUCCESFULLY).'&message_type=success');
   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = _PROBLEMISSUINGCERTIFICATE.': '.$e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
   }
  } else if (isset($_GET['revoke_certificate']) && in_array($_GET['revoke_certificate'], array_keys($users))) {
   try {
    $currentCourse -> revokeCertificate($_GET['revoke_certificate']);
    eF_redirect(''.basename($_SERVER['PHP_SELF']).'?'.$baseUrl.'&op=course_certificates&reset_popup=1&message='.urlencode(_CERTIFICATEREVOKED).'&message_type=success');
   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = _PROBLEMREVOKINGCERTIFICATE.': '.$e -> getMessage().' &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
   }
  } else if (isset($_GET['auto_complete'])) {
   try {
    if ($currentCourse -> course['auto_complete']) {
     $currentCourse -> course['auto_complete'] = 0;
     $currentCourse -> course['auto_certificate'] = 0;
    } else {
     $currentCourse -> course['auto_complete'] = 1;
    }
    $currentCourse -> persist();
    echo $currentCourse -> course['auto_complete'];
   } catch (Exception $e) {
    header("HTTP/1.0 500");
    echo $e -> getMessage().' ('.$e -> getCode().')';
   }
   exit;
  } else if (isset($_GET['auto_certificate'])) {
   try {
    if ($currentCourse -> course['auto_certificate']) {
     $currentCourse -> course['auto_certificate'] = 0;
    } else {
     $currentCourse -> course['auto_certificate'] = 1;
    }
    $currentCourse -> persist();
    echo $currentCourse -> course['auto_certificate'];
   } catch (Exception $e) {
    header("HTTP/1.0 500");
    echo $e -> getMessage().' ('.$e -> getCode().')';
   }
   exit;
  }
  $roles = EfrontLessonUser :: getLessonsRoles();
  if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
   foreach ($users as $key => $user) {
    if ($roles[$user['user_type']] != 'student') {
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
    $users[$key]['issued_certificate'] = $value['issued_certificate'];
    $expire_certificateTimestamp = "";
    if ($value['issued_certificate'] != "") {
     $issuedData = unserialize($value['issued_certificate']);
     $users[$key]['serial_number'] = $issuedData['serial_number'];

     $dateFormat = eF_dateFormat();
     if (eF_checkParameter($issuedData['date'], 'timestamp')) {
      $expire_certificateTimestamp = $currentCourse -> course['certificate_expiration'] + $issuedData['date'];
      $dateExpire = date($dateFormat, $expire_certificateTimestamp);
     } else {
      $expire_certificateTimestamp = $currentCourse -> course['certificate_expiration'] + strtotime($issuedData['date']);
      $dateExpire = date($dateFormat, $expire_certificateTimestamp);
     }

     if(isset($currentCourse -> course['certificate_expiration']) && $currentCourse -> course['certificate_expiration'] != 0) {
      $users[$key]['expire_certificate'] = $dateExpire;
     }

    }
   }
   $smarty -> assign("T_USERS_PROGRESS", $users);
   $smarty -> display('includes/course_settings.tpl');
   exit;
  }

  if (isset($_GET['export']) && $_GET['export'] == 'rtf') {
   $result = eF_getTableData("users_to_courses", "*", "users_LOGIN = '".$_GET['user']."' and courses_ID = '".$_GET['course']."' limit 1");
   if (sizeof($result) == 1 || isset($_GET['preview'])) {
    $course = new EfrontCourse($_GET['course']);
    if (!isset($_GET['preview'])){
     $certificate_tpl_id = $course -> course['certificate_tpl_id'];
     if ($certificate_tpl_id <= 0) {
      $cfile = new EfrontFile(G_CERTIFICATETEMPLATEPATH."certificate1.rtf");
     } else {
      $cfile = new EfrontFile($certificate_tpl_id);
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

} else if ($_GET['op'] == 'format_certificate') {
} else if ($_GET['op'] == 'course_rules') {
    $courseLessons = $currentCourse -> getLessons();
    $rules_form = new HTML_QuickForm("course_rules_form", "post", basename($_SERVER['PHP_SELF'])."?".$baseUrl."&op=course_rules", "", null, true);
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
                //eF_redirect("".basename($_SERVER['PHP_SELF'])."?".$baseUrl."&op=course_rules&message=".urlencode(_SUCCESFULLYSETORDER)."&message_type=success");
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
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $rules_form -> accept($renderer);
    $smarty -> assign('T_COURSE_RULES_FORM', $renderer -> toArray());
    $smarty -> assign("T_COURSE_RULES", $currentCourse -> rules);
    $smarty -> assign('T_COURSE_LESSONS', $courseLessons);
    $smarty -> assign('T_COURSE', $currentCourse -> course);
    $smarty -> assign('T_COURSE_LESSONS', $courseLessons);
} else if ($_GET['op'] == 'course_order') {
    $courseLessons = $currentCourse -> getLessons();
    $smarty -> assign('T_COURSE', $currentCourse -> course);
    $smarty -> assign('T_COURSE_LESSONS', $courseLessons);
    if (isset($_GET['ajax']) && isset($_GET['order'])) {
        try {
            $order = explode(",", $_GET['order']);
            $previous = 0;
            foreach ($order as $value) {
                $result = explode("-", $value);
                if (in_array($value, array_keys($courseLessons))) {
                    eF_updateTableData("lessons_to_courses", array("previous_lessons_ID" => $previous), "courses_ID=".$currentCourse -> course['id']." and lessons_ID=".$result[0]);
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
    $courseLessons = $currentCourse -> getLessons();
    if (isset($_GET['set_schedule']) && in_array($_GET['set_schedule'], array_keys($courseLessons))) {
        try {
            $lesson = new EfrontLesson($_GET['set_schedule']);
            $fromTimestamp = mktime($_GET['from_Hour'], $_GET['from_Minute'], 0, $_GET['from_Month'], $_GET['from_Day'], $_GET['from_Year']);
            $toTimestamp = mktime($_GET['to_Hour'], $_GET['to_Minute'], 0, $_GET['to_Month'], $_GET['to_Day'], $_GET['to_Year']);
            if ($fromTimestamp < $toTimestamp) {
                $lesson -> lesson['from_timestamp'] = $fromTimestamp;
                $lesson -> lesson['to_timestamp'] = $toTimestamp;
                //                        $lesson -> lesson['shift']          = $form -> exportValue('shift') ? 1 : 0;
                $lesson -> persist();
                eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_START . "_" . $lesson -> lesson['id']. "'");
                eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_EXPIRY . "_" . $lesson -> lesson['id']. "'");
                EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_PROGRAMMED_START, "timestamp" => $lesson -> lesson['from_timestamp'], "lessons_ID" => $lesson -> lesson['id'], "lessons_name" => $lesson -> lesson['name']));
                EfrontEvent::triggerEvent(array("type" => EfrontEvent::LESSON_PROGRAMMED_EXPIRY, "timestamp" => $lesson -> lesson['to_timestamp'], "lessons_ID" => $lesson -> lesson['id'], "lessons_name" => $lesson -> lesson['name']));
                echo _FROM.' '.formatTimestamp($fromTimestamp, 'time_nosec').' '._TO.' '.formatTimestamp($toTimestamp, 'time_nosec').'&nbsp;';
            } else {
                header("HTTP/1.0 500");
                echo _ENDDATEMUSTBEBEFORESTARTDATE;
            }
        } catch (Exception $e) {
            header("HTTP/1.0 500");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    } else if (isset($_GET['delete_schedule']) && in_array($_GET['delete_schedule'], array_keys($courseLessons))) {
        try {
            $lesson = new EfrontLesson($_GET['delete_schedule']);
            $lesson -> lesson['from_timestamp'] = null;
            $lesson -> lesson['to_timestamp'] = null;
            $lesson -> lesson['shift'] = 0;
            $lesson -> persist();
            // @TODO maybe proper class internal invalidation
            eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_START . "_" . $lesson -> lesson['id']. "'");
            eF_deleteTableData("notifications", "id_type_entity LIKE '%_". (-1) * EfrontEvent::LESSON_PROGRAMMED_EXPIRY . "_" . $lesson -> lesson['id']. "'");
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;
    }
    $smarty -> assign("T_COURSE_LESSONS", $courseLessons);
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
?>
