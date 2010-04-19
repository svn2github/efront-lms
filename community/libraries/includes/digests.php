<?php
/**

* eFront email digests

*

* This page is used for the functionalities eFront email digests functionalities

* @package eFront

* @version 3.6.0

*/
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
if (isset($currentUser -> coreAccess['notifications']) && $currentUser -> coreAccess['notifications'] == 'hidden') {
    eF_redirect("".basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
    exit;
}
!isset($currentUser -> coreAccess['notifications']) || $currentUser -> coreAccess['notifications'] == 'change' ? $_change_ = 1 : $_change_ = 0;
$smarty -> assign("_change_", $_change_);
$loadScripts[] = "administrator/digests";
 if (isset($_GET['add_default']) && $_GET['add_default'] == 1) {
  EfrontNotification::addDefaultNotifications();
 }

    if (isset($_GET['activate_notification'])) {
        if (isset($_GET['event']) && $_GET['event']) {
            EfrontNotification::activateEventNotification($_GET['activate_notification']);
        } else {
            $notification = new EfrontNotification($_GET['activate_notification']);
            $notification -> activate();
        }
        exit;
    } else if (isset($_GET['deactivate_notification'])) {
        if (isset($_GET['event']) && $_GET['event']) {
            EfrontNotification::deactivateEventNotification($_GET['deactivate_notification']);
        } else {
            $notification = new EfrontNotification($_GET['deactivate_notification']);
            $notification -> deactivate();
        }
        exit;
    }

    if (isset($_GET['delete_notification']) ) {
        if (isset($_GET['event'])) {
            EfrontNotification::deleteEventNotification($_GET['delete_notification']);
        } else {
            $notification = new EfrontNotification($_GET['delete_notification']);
            $notification -> delete();
        }

        $message = _NOTIFICATIONDELETEDSUCCESSFULLY;
        $message_type = 'success';
        eF_redirect("".$_SESSION['s_type'].".php?ctg=digests&message=". $message . "&message_type=" . $message_type);

    }

    if ($_GET['op'] == "preview" && eF_checkParameter($_GET['sent_id'], 'id') ) {
     $sent_notification = eF_getTableData("sent_notifications", "*", "id = " . $_GET['sent_id']);
     $sent_notification = $sent_notification[0];
     $sent_notification = str_replace("\n", "<br>", $sent_notification);
     $smarty -> assign("T_SENT_NOTIFICATION_PREVIEW", $sent_notification);

    } else {

    if (isset($_GET['add_notification']) || isset($_GET['edit_notification'])) {

        if (isset($_GET['add_notification'])) {
            $form = new HTML_QuickForm("digests_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=digests&add_notification=1", "", null, true);
        } else {
            if ($_GET['event']) {
                $form = new HTML_QuickForm("digests_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=digests&edit_notification=" . $_GET['edit_notification']. "&event=1", "", null, true);
            } else {
                $form = new HTML_QuickForm("digests_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=digests&edit_notification=" . $_GET['edit_notification'], "", null, true);
            }
        }
        $form -> addElement('select', 'type' , _SENDNOTIFICATION, array(0 => _ONDATE, 1 => _ONEVENT, 2 => _AFTEREVENT, 3 => _BEFOREEVENT), "id = 'type_when' class = 'inputSelectMed' onChange = 'changeMessageType(this, true)'");
        $form -> addElement('select', 'when' , _FREQUENCY, array(0 => _ONCE, 1 => _PERIODICALLY), "id = 'message_frequency' class = 'inputSelectMed' onChange = 'changeMessageFrequency()'");


        // Create the event_types selects
        $all_event_types = EfrontEvent::getEventTypes();

        $events = array();
        $events_after = array();
        $events_before = array();

        // @TODO optimize with one pass...
        foreach ($all_event_types as $key => $event_type) {
            if ($event_type['priority'] == 1 && $event_type['category'] != 'personal' && $event_type['category'] != 'social') {
                $events[$key . "_" . $event_type['category']] = $event_type['text'];

                if ($event_type["afterEvent"]) {
                    $events_after[$key . "_" . $event_type['category']] = $event_type['text'];
                    if ($event_type['canBeNegated']) {
                        $events_after["-" . $key . "_" . $event_type['category']] = $event_type['canBeNegated']; // the text is stored in the 'canBeNegated' field
                    }
                }

                if ($event_type['canBePreceded']) {
                    $events_before["-" . $key . "_" . $event_type['category']] = $event_type['text'];
                }
            }
        }

        foreach ($all_event_types as $key => $event_type) {
            if ($event_type['priority'] != 1 && $event_type['category'] != 'personal' && $event_type['category'] != 'social') {
                $events[$key . "_" . $event_type['category']] = $event_type['text'];

                if ($event_type["afterEvent"]) {
                    $events_after[$key . "_" . $event_type['category']] = $event_type['text'];
                    if ($event_type['canBeNegated']) {
                        $events_after["-" . $key . "_" . $event_type['category']] = $event_type['canBeNegated'];
                    }
                }

                if ($event_type['canBePreceded']) {
                    $events_before["-" . $key . "_" . $event_type['category']] = $event_type['text'];
                }
            }
        }

        $form -> addElement('select', 'event_types' , _EVENT, $events, "id = 'event_types'  class = 'inputSelectMed'  onChange = 'changeEventCategory(this)'");
        $form -> addElement('select', 'event_types_after' , NULL, $events_after, "id = 'event_types_after'  class = 'inputSelectMed'  onChange = 'changeEventCategory(this)'");
        $form -> addElement('select', 'event_types_before' , NULL, $events_before, "id = 'event_types_before'  class = 'inputSelectMed'  onChange = 'changeEventCategory(this)'");


        // Create the date select
        $formatDate = eF_dateFormat();
        $options = array(
            'format' => $formatDate.', H:i',
            'minYear' => date("Y"),
            'maxYear' => date('Y') + 1,
            'id' => "dateSelects"
        );
        $form -> addElement('date', 'timestamp', _ON, $options);

        // Create the inteval select: 1 day, 3 days, 1 week, 1 month
        $day_seconds = 86400;
        $durations = array( $day_seconds/4 => "6 " . _HOURS,
                            $day_seconds/2 => "12 " . _HOURS,
                            $day_seconds => "1 "._DAYLOWER);
        for ($i = 2; $i <= 60; $i++) {
            $day_seconds += 86400;
            $durations[$day_seconds] = $i ." ". _DAYS;
        }

        $form -> addElement('select', 'send_interval' , _EVERY, $durations, "id = 'message_interval' class = 'inputSelectMed' ");

        // Create the templates values - the exact same fields should be used during substitution in the eF_formulateTemplateMessage function

        $hostname = G_SERVERNAME;
        if ($hostname[strlen($hostname)-1] == "/") {
            $hostname = substr($hostname,0,strlen($hostname)-1);
        }
        $basic_templates_array = array( "users_name" => _RECIPIENTSUSERNAME,
                                        "users_surname" => _RECIPIENTSSURNAME,
                                        "users_login" => _RECIPIENTSLOGIN,
                                        "user_type" => _RECIPIENTSUSERTYPE,
                                        "users_email" => _RECIPIENTSEMAIL,
                                        "users_comments"=> _RECIPIENTSCOMMENTS,
                                        "users_language"=> _RECIPIENTSLANGUAGE,
                                        "date" => _EVENTDATE,
                                        "timestamp" => _EVENTTIMESTAMP,
                                        "host_name" => _HOSTSYSTEMURL . " (".$hostname.")",
                                        "site_name" => _SITENAME,
                                        "site_motto" => _SITEMOTO,
                                        "md5("._WRITETEXTORENTERTEMPLATETOBEENCODED.")" => _MD5ENCODINGOF);

        $smarty -> assign("T_BASIC_TEMPLATED", sizeof($basic_templates_array));

        if ($_GET['edit_notification'] && $_GET['event'] == 1) {

            $basic_templates_array["triggering_users_name"] = _TRIGGERINGUSERSNAME;
            $basic_templates_array["triggering_users_surname"] = _TRIGGERINGUSERSSURNAME;
            $basic_templates_array["triggering_users_login"] = _TRIGGERINGUSERSLOGIN;
            $basic_templates_array["triggering_user_type"] = _TRIGGERINGUSERSTYPE;
            $basic_templates_array["triggering_users_email"] = _TRIGGERINGUSERSEMAIL;

            $event_notification = eF_getTableData("event_notifications", "*", "id = '".$_GET['edit_notification']."'");

            //$event_notification[0]['event_type']  = abs($event_notification[0]['event_type']);
            $mode = $all_event_types[abs($event_notification[0]['event_type'])]['category'];
            if ($mode != "system" && $mode != "branch" && $mode != "job") {
                $basic_templates_array["lessons_name"] = _LESSONNAME;
            }
            if ($mode == "test") {
                $basic_templates_array["tests_name"] = _TESTNAME;
            } else if ($mode == "news") {
                $basic_templates_array["announcement_title"] = _ANNOUNCEMENTTITLE;
                $basic_templates_array["announcement_body"] = _ANNOUNCEMENTBODY;
            } else if ($mode == "content") {
                $basic_templates_array["unit_title"] = _UNITNAME;
                $basic_templates_array["unit_content"] = _UNITCONTENT;
            } else if ($mode == "survey") {
                $basic_templates_array["survey_name"] = _SURVEYNAME;
                $basic_templates_array["survey_id"] = _SURVEYID;
                $basic_templates_array["survey_message"] = _SURVEYMESSAGE;
            }
        }
        $form -> addElement('select', 'templ_add' , _ADDTEMPLATIZEDTEXT, $basic_templates_array, "id = 'template_add' class = 'inputSelectMed' onChange= 'addTemplatizedText(this)'");
        if ($GLOBALS['configuration']['onelanguage']) {
            $form -> addElement('hidden', 'languages_NAME', $GLOBALS['configuration']['default_language']);
        } else {
            $form -> addElement('select', 'languages_NAME', _LANGUAGE, EfrontSystem :: getLanguages(true) , 'class = "inputSelectMed" onchange="addLanguageTag(this)"');
            // Set default values for new users
            $form -> setDefaults(array('languages_NAME' => $GLOBALS['configuration']['default_language']));
        }
        $form -> addElement('text', 'header', _SUBJECT, 'class = "inputText" id="messageSubject" onFocus="myActiveElement=\'messageSubject\';" ');
        $form -> addRule('header', _THEFIELD.' '._SUBJECT.' '._ISMANDATORY, 'required', null, 'client');
        $form -> addRule('header', _INVALIDFIELDDATA, 'checkParameter', 'text');
        $load_editor = true;
        $form -> addElement('textarea', 'message', _BODY, 'class = "digestEditor" id="messageBody" onActivate="myActiveElement=\'\';" style = "width:100%;height:200px"');
        // Get available lessons
        $lessons = eF_getTableDataFlat("lessons", "id,name", "archive=0", "name");
        sizeof($lessons) > 0 ? $av_lessons = array_combine(array_merge(array("0"), $lessons['id']), array_merge(array(_ANYLESSON), $lessons['name'])): $av_lessons = array(0 => _ANYLESSON);
        sizeof($lessons) > 0 ? $lessons = array_combine($lessons['id'], $lessons['name']) : $lessons = array();
        // Get available courses
        $courses = eF_getTableDataFlat("courses", "id,name", "", "name");
        sizeof($courses) > 0 ? $courses = array_combine($courses['id'], $courses['name']) : $courses = array();
        $smarty -> assign("T_COURSES", $courses);
        // Get available tests
        $tests = eF_getTableDataFlat("tests", "id,name", "", "name");
        $tests['id'] = array_merge(array("0"), $tests['id']);
        $tests['name'] = array_merge(array(_ANYTEST), $tests['name']);
        sizeof($tests) > 0 ? $tests = array_combine($tests['id'], $tests['name']) : $tests = array("0" => _ANYTEST);
        $smarty -> assign("T_TESTS", $tests);
        /*

        // User groups in any case

        $groups = eF_getTableData("groups", "id, name", "active=1");

        $groups_list = array();

        if (!empty($groups)) {

            foreach ($groups as $group) {

                $log = $group['id'];

                $groups_list["$log"] = $group['name'];

            }

        } else {

            $groups_list["0"] = _NOGROUPSDEFINED;

            $disable_groups = "disabled=\"disabled\"";

        }*/
        /*

        $units = eF_getTableDataFlat("content", "id, name", "");

        $units['id'] = array_merge(array("0"), $units['id']);

        $units['name'] = array_merge(array(_ANYUNIT), $units['name']);

        sizeof($units) > 0 ? $units = array_merge(array("0" => _ANYUNIT) , array_combine($units['id'], $units['name'])) : $units = array("0" => _ANYUNIT);

        */






        $form -> addElement('select', 'available_lessons', _LESSON, $av_lessons, 'id = "available_lessons" class = "inputSelectMed"');
        $form -> addElement('select', 'available_courses', _COURSE, array_merge(array(0=> _ANYCOURSE), $courses), 'id = "available_courses" class = "inputSelectMed"');
        $form -> addElement('select', 'available_tests', _TEST, $tests, 'id = "available_tests" class = "inputSelectMed"');
        //$form -> addElement('select', 'available_content',    _CONTENT, $units,       'id = "available_content" class = "inputSelectMed"');


        /*

         *

         * ALL RECIPIENTS CATEGORIES

         *

         *

         */
        //$lessons    = eF_getTableDataFlat("lessons", "id,name", "", "name");
        //sizeof($lessons) > 0 ? $lessons = array_combine($lessons['id'], $lessons['name']) : $lessons = array();
        $smarty -> assign("T_LESSONS", $lessons);
        //$courses    = eF_getTableDataFlat("courses", "id,name", "", "name");
        //sizeof($courses) > 0 ? $courses = array_combine($courses['id'], $courses['name']) : $courses = array();
        $smarty -> assign("T_COURSES", $courses);
        $roles = EfrontUser :: getRoles(true);
        // Main categories
        //$form -> addElement('radio', 'recipients', null, null, 'only_specific_users', 'id = "only_specific_users" onclick = "eF_js_selectRecipients(\'only_specific_users\')"');
        $form -> addElement('radio', 'recipients', null, null, 'active_users', 'id = "active_users" onclick = "eF_js_selectRecipients(\'active_users\')"');
        $form -> addElement('radio', 'recipients', null, null, 'specific_course', 'onclick = "eF_js_selectRecipients(\'specific_course\')"');
        $form -> addElement('select', 'specific_course', null, $courses, 'id = "course_recipients" class = "inputSelectMed" disabled = "disabled"');
        $form -> addElement('advcheckbox', 'specific_course_completed', _COMPLETED, null, 'class = "inputCheckbox" id="specific_course_completed_check" style="visibility:hidden" checked=""');

        $form -> addElement('radio', 'recipients', null, null, 'specific_lesson', 'onclick = "eF_js_selectRecipients(\'specific_lesson\')"');
        $form -> addElement('select', 'lesson', null, $lessons, 'id = "lesson_recipients" class = "inputSelectMed" disabled = "disabled"');
        $form -> addRule('lesson', _INVALIDFIELDDATA, 'checkParameter', 'id');

        $form -> addElement('radio', 'recipients', null, null, 'specific_lesson_professor', 'onclick = "eF_js_selectRecipients(\'specific_lesson_professor\')"');
        $form -> addElement('select', 'professor', null, $lessons, 'id = "lesson_professor_recipients" class = "inputSelectMed" disabled = "disabled"');
        $form -> addRule('lesson', _INVALIDFIELDDATA, 'checkParameter', 'id');

        $form -> addElement('radio', 'recipients', null, null, 'specific_type', 'onclick = "eF_js_selectRecipients(\'specific_type\')"');
        $form -> addElement('select', 'user_type', null, $roles, 'id = "user_type_recipients" class = "inputSelectMed" disabled = "disabled"');
        $form -> addRule('user_type', _INVALIDFIELDDATA, 'checkParameter', 'text');


        $basic_event_recipients = array(EfrontNotification::TRIGGERINGUSER => _USERTRIGGERINGTHEEVENT,
                                        EfrontNotification::ALLSYSTEMUSERS => _ALLSYSTEMUSERS,
                                        EfrontNotification::SYSTEMADMINISTRATOR => _SYSTEMADMINISTRATOR,
                                        EfrontNotification::EXPLICITLYSEL => _EXPLICITLYSELECTED);






        $smarty -> assign("T_BASIC_EVENT_RECIPIENTS" , sizeof($basic_event_recipients));

        if ($_GET['edit_notification'] && $_GET['event'] == "1") {
            // $mode variable set before
            if ($mode == "courses") {
             $basic_event_recipients[EfrontNotification::COURSEPROFESSORS] = _COURSEPROFESSORS;
            } else if ($mode != "system") {
                $basic_event_recipients[EfrontNotification::ALLLESSONUSERS] = _ALLLESSONUSERS;
                $basic_event_recipients[EfrontNotification::LESSONUSERSNOTCOMPLETED] = _LESSONUSERSNOTCOMPLETED;
                $basic_event_recipients[EfrontNotification::LESSONPROFESSORS] = _LESSONPROFESSORS;
            }
        }

        $smarty -> assign("T_LESSON_EVENT_RECIPIENTS",array( "alllesson" => EfrontNotification::ALLLESSONUSERS, "lessonprof" => EfrontNotification::LESSONPROFESSORS, "lessonnotcompleted" => EfrontNotification::LESSONUSERSNOTCOMPLETED));
        $smarty -> assign("T_COURSE_EVENT_RECIPIENTS",array( "courseprof" => EfrontNotification::COURSEPROFESSORS));

        $form -> addElement('select', 'event_recipients', _RECIPIENTS, $basic_event_recipients, 'class="inputSelectMed" id = "event_recipients"');

        $form -> addElement('advcheckbox', 'send_immediately', _SENDIMMEDIATELY, null, 'class = "inputCheckbox" id="send_immediately"');
        $form -> addElement('advcheckbox', 'html_message', _SENDMESSAGEASHTML, null, 'id = "html_message_id" ');
        //$form -> addElement('advcheckbox', 'recipients_mass_mail', null, null, 'active_users', 'id = "event_recipients" onclick = "eF_js_showMassMailOption(\'active_users\')"');


        // User groups
        $groups = eF_getTableData("groups", "id, name", "active=1");
        $groups_list = array();
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $log = $group['id'];
                $groups_list["$log"] = $group['name'];
            }
        } else {
            $groups_list["0"] = _NOGROUPSDEFINED;
            $disable_groups = "disabled=\"disabled\"";
        }

        $form -> addElement('radio', 'recipients', null, null, 'specific_group', $disable_groups . ' onclick = "eF_js_selectRecipients(\'specific_group\')"');
        $form -> addElement('select', 'group_recipients', null, $groups_list, 'id = "group_recipients" class = "inputSelectMed" disabled = "disabled"');


        // And categories for HCD
        /*

#ifdef ENTERPRISE

            $branches = eF_getTableData("module_hcd_branch", "branch_ID, name, father_branch_ID","");

            if (!empty($branches)) {

                $branches_list = array();

                include ("../libraries/module_hcd_tools.php");

                $branches_list = eF_createBranchesTreeSelect($branches,1);

            } else {

                $branches_list = array("0" => _NOBRANCHESHAVEBEENREGISTERED);

                $disable_branches = "disabled=\"disabled\"";

            }



            $job_descriptions = eF_getTableData("module_hcd_job_description", "distinct description","");

            if (!empty($job_descriptions)) {

                $job_description_list = array("0" => _ANYJOBDESCRIPTION);

                foreach ($job_descriptions as $job_description) {

                    $log = $job_description['description'];

                    $job_description_list["$log"] = $job_description['description'];

                }

            } else {

                $job_description_list["0"] = _NOJOBDESCRIPTIONSSHAVEBEENREGISTERED;

                $disable_job_descriptions = "disabled=\"disabled\"";

            }



            $skills = eF_getTableData("module_hcd_skills", "skill_ID, description","");

            $skills_list = array();

            if (!empty($skills)) {

                foreach ($skills as $skill) {

                    $log = $skill['skill_ID'];

                    $skills_list["$log"] = $skill['description'];

                }

            } else {

                $skills_list["0"] = _NOSKILLSHAVEBEENREGISTERED;

                $disable_skills = "disabled=\"disabled\"";

            }



            $form -> addElement('radio', 'recipients', null, null, 'specific_branch_job_description', $disable_branches . ' onclick = "eF_js_selectRecipients(\'specific_branch_job_description\')"');

            $form -> addElement('select', 'branch_recipients', null, $branches_list, 'id = "branch_recipients" class = "inputSelectMed" disabled = "disabled"');

            $form -> addElement('advcheckbox', 'include_subbranches', _INCLUDESUBBRANCHES, null, 'class = "inputCheckbox" id="include_subbranches" style="visibility:hidden" checked=""');



            $form -> addElement('radio', 'recipients', null, null, 'specific_job_description', $disable_job_descriptions . ' onclick = "eF_js_selectRecipients(\'specific_job_description\')"');

            $form -> addElement('select', 'job_description_recipients',null, $job_description_list, 'id = "job_description_recipients" class = "inputSelectMed" disabled = "disabled"');



            $form -> addElement('radio', 'recipients', null, null, 'specific_skill', $disable_skills . ' onclick = "eF_js_selectRecipients(\'specific_skill\')"');

            $form -> addElement('select', 'skill_recipients', null, $skills_list, 'id = "skill_recipients" class = "inputSelectMed" disabled = "disabled"');

#endif

        */
        $form -> addElement('submit', 'submit_digest', _SUBMIT, 'class = "flatButton"');
        // Set default values to the form elements
        // These values should be there either by default or if the user decides to change categories
        $form -> setDefaults(array('recipients' => 'active_users'));
        $form -> setDefaults(array('timestamp' => time()+3600));
        if (isset($_GET['edit_notification'])) {
            if ($_GET['event'] == 1) {
                $event = $event_notification;
            } else {
                $event = eF_getTableData("notifications", "*", "id = '".$_GET['edit_notification']."'");
            }
            if (sizeof($event)) {
                $event = $event[0];
            }
            // Set default values
            $timestamp = $event['timestamp'];
            // On event and after event types
            if (isset($event['event_type'])) {
                // The category of the event: lessons, tests etc
                if ($event['event_type'] > 0) {
                    $event_category = $all_event_types[$event['event_type']]['category'];
                } else {
                    $event_category = $all_event_types[((-1)*$event['event_type'])]['category'];
                }
                $smarty -> assign("T_EVENT_CATEGORY", $event_category);
                if ($event['after_time'] > 0) {
                    $type = "2";
                    $form -> setDefaults(array('event_types_after' => $event['event_type'] ."_". $event_category,
                                               'send_interval' => $event['after_time'],
                                               'send_immediately' => $event['send_immediately'],
                                               'event_recipients' => $event['send_recipients'],
                                               'html_message' => $event['html_message']));
                } else if ($event['after_time'] < 0) {
                    $type = "3";
                    $form -> setDefaults(array('event_types_before' => $event['event_type'] ."_". $event_category,
                                               'send_interval' => (-1)*$event['after_time'],
                             'send_immediately' => $event['send_immediately'],
                                               'event_recipients' => $event['send_recipients'],
                                               'html_message' => $event['html_message']));
                } else {
                    $type = "1";
                    $form -> setDefaults(array('event_types' => $event['event_type'] ."_". $event_category,
                             'send_immediately' => $event['send_immediately'],
                                               'event_recipients' => $event['send_recipients'],
                                               'html_message' => $event['html_message']));
                }
                // The type of the form: 1 on event, 2 after event
                $smarty -> assign("T_EVENT_FORM", $type);
                // Get condition
                $send_conditions = unserialize($event['send_conditions']);
                if ($event_category == "lessons") {
                    $condition = $send_conditions["lessons_ID"];
                } else if ($event_category == "tests") {
                    $condition = $send_conditions["tests_ID"];
                } else if ($event_category == "content") {
                    $condition = $send_conditions["unit_ID"];
                } else if ($event_category == "forum") {
                    $condition = $send_conditions["forums_ID"];
                } else {
                    $condition = false;
                }
                if ($condition) {
                    $condition_category = 'available_' . $event_category;
                    $form -> setDefaults(array($condition_category => $condition));
                }
                // The specific lesson, test, unit etc
                $smarty -> assign("T_CONDITION", $condition);
            } else {
                // On date type
                $type = "0";
                if ($event['send_interval']) {
                    $frequency = "1";
                    $send_interval = $event['send_interval'];
                    $smarty -> assign("T_SHOW_SEND_INTERVAL", "1");
                } else {
                    $frequency = "0";
                    $send_interval = 0;
                }
                if ($event['send_conditions']) {
                    $condition_category = unserialize($event['send_conditions']);
                    if (isset($condition_category['lessons_ID'])) {
                        if (isset($condition_category['user_type'])) {
                            $form -> setDefaults(array('professor' => $condition_category['lessons_ID'],
                                                       'recipients' => 'specific_lesson_professor'));
                        } else {
                            $form -> setDefaults(array('lesson' => $condition_category['lessons_ID'],
                                                       'recipients' => 'specific_lesson'));
                        }
                    } else if (isset($condition_category['courses_ID'])) {
                        $form -> setDefaults(array('specific_course_completed' => $condition_category['completed'],
                                                   'recipients' => 'specific_course'));
                    } else if (isset($condition_category['user_type'])) {
                        $form -> setDefaults(array('user_type' => $condition_category['user_type'],
                                                   'recipients' => 'specific_type'));
                    } else if (isset($condition_category['groups_ID'])) {
                        $form -> setDefaults(array('group_recipients' => $condition_category['groups_ID'],
                                                   'recipients' => 'specific_group'));
                    }
                }
                $form -> setDefaults(array('when' => $frequency,
                                           'send_interval' => $send_interval));
            }
            $form -> setDefaults(array('timestamp' => $timestamp,
                                       'type' => $type,
                                       'header' => $event['subject'],
                                       'message' => $event['message']));
        }
        $smarty -> assign("T_RECIPIENTS_CATEGORY", $form -> exportValue('recipients'));
        /************

         *

         *  On form submission

         *

         */
        if ($form -> isSubmitted()) {
            if ($form -> validate()) {
                // Common information for date/event notifications
                $subject = $form->exportValue('header');
                $message = $form->exportValue('message');
                $html_message = $form -> exportValue('html_message');
                if ($html_message) {
                    $message = str_replace("\n", "<br>", $message);
                }
                $notification_type = $form->exportValue('type');
                // Notification on date
                if ($notification_type == "0") {
                    $message_frequency = $form->exportValue('when');
                    $date = $form->exportValue('timestamp');
                    $timestamp = mktime($date['H'], $date['i'], 0, $date['m'], $date['d'], $date['Y']);
                    // Set recipients condition
                    $condition_category = $form -> exportValue('recipients');
                    // Keep recipients' information
                    if ($condition_category == "specific_lesson") {
                        $condition = array("lessons_ID" => $form -> exportValue('lesson'));
                    } else if ($condition_category == "specific_lesson_professor") {
                        $condition = array("lessons_ID" => $form -> exportValue('professor'),
                                           "user_type" => "professor");
                    } else if ($condition_category == "specific_course") {
                        $condition = array("courses_ID" => $form -> exportValue('specific_course'));
                        if ($form -> exportValue('specific_course_completed')) {
                            $condition['completed'] = 1;
                        }
                    } else if ($condition_category == "specific_type") {
                        $condition = array("user_type" => $form -> exportValue('user_type'));
                    } else if ($condition_category == "specific_group") {
                        $condition = array("groups_ID" => $form -> exportValue('group_recipients'));
                    } else {
                        $condition = NULL;
                    }
                    // Timestamp should be in the future
                    if (isset($_GET['add_notification'])) {
                        if (false) {
                            //if (time() > $timestamp) {
                            $message = _CANNOTSCHEDULEMESSAGEFORPASTDATE;
                            $message_type = 'failure';
                        } else {
                            // Notification on a specific time, once
                            if ($message_frequency == "0") {
                                EfrontNotification::addNotification($timestamp, $subject, $message, $condition, $html_message);
                            // Notification periodically starting from a specific date
                            } else if ($message_frequency == "1") {
                                EfrontNotification::addNotification($timestamp, $subject, $message, $condition, $html_message, $form -> exportValue('send_interval'));
                            }
                            $message = _NOTIFICATIONSETUPSUCCESSFULLY;
                            $message_type = 'success';
                        }
                    } else {
                        // If we changed notification category from event -> simple, then delete in event_notifications and add in notifications
                        if ($_GET['event'] == 1) {
                            eF_deleteTableData("event_notifications", "id = '".$_GET['edit_notification']."'");
                            if ($message_frequency == "0") {
                                EfrontNotification::addNotification($timestamp, $subject, $message, $condition, $html_message);
                            // Notification periodically starting from a specific date
                            } else if ($message_frequency == "1") {
                                EfrontNotification::addNotification($timestamp, $subject, $message, $condition, $html_message, $form -> exportValue('send_interval'));
                            }
                        } else {
                            // Notification on a specific time, once
                            if ($message_frequency == "0") {
                                EfrontNotification::editNotification($_GET['edit_notification'], $timestamp, $subject, $message, $condition, $html_message);
                            // Notification periodically starting from a specific date
                            } else if ($message_frequency == "1") {
                                EfrontNotification::editNotification($_GET['edit_notification'], $timestamp, $subject, $message, $condition, $html_message, $form -> exportValue('send_interval'));
                            }
                        }
                        $message = _NOTIFICATIONSETUPSUCCESSFULLY;
                        $message_type = 'success';
                    }
                } else {
                    $send_immediately = false;
                    // Notification on event
                    if ($notification_type == 1) {
                        $event_type = explode("_", $form -> exportValue('event_types'));
                        $send_immediately = $form -> exportValue('send_immediately');
                        $after_time = false;
                    } else if ($notification_type == 2) {
                        $event_type = explode("_", $form -> exportValue('event_types_after'));
                        $after_time = $form -> exportValue('send_interval');
                    } else if ($notification_type == 3) {
                        $event_type = explode("_", $form -> exportValue('event_types_before'));
                        $after_time = (-1) * $form -> exportValue('send_interval');
                    }
                    // The value of the select is in the form  eventType_eventCategory
                    $event_category = $event_type[1];
                    $events_type = $event_type[0];
                    //$condition = $form -> exportValue('available_' . $event_category);
                    if ($event_category == "lessons") {
                        $condition = $form -> exportValue('available_' . $event_category);
                        $condition = array("lessons_ID" => $condition);
                    } else if ($event_category == "courses") {
                        $condition = $form -> exportValue('available_' . $event_category);
                        $condition = array("courses_ID" => $condition);
                    } else if ($event_category == "tests") {
                        $condition = $form -> exportValue('available_' . $event_category);
                        $condition = array("tests_ID" => $condition);
                    } else if ($event_category == "forum" || $event_category == "content") {
                        $condition = array("lessons_ID" => $form -> exportValue('available_lessons'));
                    } else {
                        $condition = array();
                    }
                    if (isset($_GET['add_notification'])) {

                        EfrontNotification::addEventNotification($events_type, $subject, $message, $condition, $_POST['event_recipients'], $html_message, $after_time, $send_immediately);
                    } else {
                        // if we changed from simple notification event -> on/after event notification
                        if (!isset($_GET['event'])) {
                            eF_deleteTableData("notifications", "id = '".$_GET['edit_notification']."'");

                            //$notification = array ("event_type"        => $events_type, "send_conditions" => serialize($condition),"send_recipients" => $_POST['event_recipients'], "message"          => $message,"subject"       => $subject);
                            EfrontNotification::addEventNotification($events_type, $subject, $message, $condition, $_POST['event_recipients'], $html_message, $after_time, $send_immediately);

                        } else {
                            EfrontNotification::editEventNotification($_GET['edit_notification'], $events_type, $subject, $message, $condition, $_POST['event_recipients'], $html_message, $after_time, $send_immediately);
                        }
                    }

                    $message = _NOTIFICATIONSETUPSUCCESSFULLY;
                    $message_type = 'success';
                }


                eF_redirect("".$_SESSION['s_type'].".php?ctg=digests&message=". $message . "&message_type=" . $message_type);
            }
        }

        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty); //Create a smarty renderer

        $renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');

        if (isset($currentUser -> coreAccess['notifications']) && $currentUser -> coreAccess['notifications'] != 'change') {
            $form -> freeze();
        }

        $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
        $form -> setRequiredNote(_REQUIREDNOTE);
        $form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created

        $smarty -> assign('T_DIGESTS_FORM', $renderer -> toArray()); //Assign the form to the template
    } else {

        // Getting first the messages' queue table, because it is ajaxed
        $smarty -> assign("T_TIMESTAMP_NOW", time());
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'msgQueueTable') {
            isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

            if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
                $sort = $_GET['sort'];
                // @TODO fix
                if ($sort == "timestamp") {
                    $order = "desc";
                } else {
                    isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
                }
            } else {
                $sort = 'timestamp';
                $order = 'asc';
            }

            // ** Get queue messages **
            $sending_queue_msgs = eF_getTableData("notifications", "*", "active = 1", "timestamp ASC");
            // Create the corresponding info per message
            foreach ($sending_queue_msgs as $key => $sending_queue_msg) {

                // recipients
                if ($sending_queue_msg['send_conditions'] == "N;") {
                    $sending_queue_msgs[$key]['recipients'] = _ALLUSERS;
                } else {
                    $sending_queue_msg['send_conditions'] = unserialize($sending_queue_msg['send_conditions']);


                    if (is_array($sending_queue_msg['send_conditions'])) {
                        if (isset($sending_queue_msg['send_conditions']['lessons_ID'])) {
                            if ($sending_queue_msg['send_conditions']['lessons_ID'] != 0) {
                                $lesson = new EfrontLesson($sending_queue_msg['send_conditions']['lessons_ID']);

                                if (isset($sending_queue_msg['send_conditions']['user_type'])) {
                                    $sending_queue_msgs[$key]['recipients'] = _PROFESSORSOFLESSON . ": " . $lesson -> lesson['name'];
                                } else {
                                    $sending_queue_msgs[$key]['recipients'] = _LESSON . ": " . $lesson -> lesson['name'];
                                }
                            } else {
                                $sending_queue_msgs[$key]['recipients'] = _ANYLESSON;
                            }
                        } else if (isset($sending_queue_msg['send_conditions']['tests_ID'])) {
                            if ($sending_queue_msg['send_conditions']['tests_ID'] != 0) {
                                $test = new EfrontTest($sending_queue_msg['send_conditions']['tests_ID']);
                                $sending_queue_msgs[$key]['recipients'] = _TEST . ": " . $test -> test['name'];
                            } else {
                                $sending_queue_msgs[$key]['recipients'] = _ANYTEST;

                            }
                        } else if (isset($sending_queue_msg['send_conditions']['unit_ID'])) {


                        } else if (isset($sending_queue_msg['send_conditions']['forum_ID'])) {

                        } else if (isset($sending_queue_msg['send_conditions']['entity_ID'])) {
                            $sending_queue_msgs[$key]['recipients'] = _SELECTEDUSERS;
                        } else if (isset($sending_queue_msg['send_conditions']['groups_ID'])) {
                            if ($sending_queue_msg['send_conditions']['groups_ID'] != 0) {
                                $group = new EfrontGroup($sending_queue_msg['send_conditions']['groups_ID']);
                                $sending_queue_msgs[$key]['recipients'] = _GROUP . ": " . $group -> group['name'];
                            } else {
                                $sending_queue_msgs[$key]['recipients'] = _ANYCOURSE;
                            }
                        } else if (isset($sending_queue_msg['send_conditions']['courses_ID'])) {
                            if ($sending_queue_msg['send_conditions']['courses_ID'] != 0) {
                                $course = new EfrontCourse($sending_queue_msg['send_conditions']['courses_ID']);
                                if (isset($sending_queue_msg['send_conditions']['user_type'])) {
                                 $sending_queue_msgs[$key]['recipients'] = _PROFESSORSOFCOURSE . ": " . $course -> course['name'];
                                } else {
                                 $sending_queue_msgs[$key]['recipients'] = _COURSE . ": " . $course -> course['name'];
                                 if (isset($sending_queue_msg['send_conditions']['completed'])) {
                                     $sending_queue_msgs[$key]['recipients'] .= " " . _COMPLETED;
                                 }
                                }
                            } else {
                                $sending_queue_msgs[$key]['recipients'] = _ANYCOURSE;
                            }
                        } else if (isset($sending_queue_msg['send_conditions']['user_type'])) {
                            $user_type = $sending_queue_msg['send_conditions']['user_type'];
                            if ($user_type == "administrator") {
                                $user_type_name = _ADMINISTRATOR;
                            } else if ($user_type == "professor") {
                                $user_type_name = _PROFESSOR;
                            } else if ($user_type == "student") {
                                $user_type_name = _STUDENT;
                            } else {
                                $user_type = eF_getTableData("user_types", "name", "id = '" . $user_type . "'");
                                $user_type_name = $user_type[0]['name'];
                            }
                            $sending_queue_msgs[$key]['recipients'] = _USERTYPE . ": " . $user_type_name;
                        } else if (isset($sending_queue_msg['send_conditions']['users_login']) && isset($sending_queue_msg['send_conditions']['supervisor'])) {
                         $sending_queue_msgs[$key]['recipients'] = _USERSUPERVISORS;
                        } else {
                            $sending_queue_msgs[$key]['recipients'] = _ALLUSERS;
                        }
                    } else {

                        $user = eF_getTableData("users", "name, surname, email", "login = '".$sending_queue_msgs[$key]['recipient']."'");

                        $sending_queue_msgs[$key]['recipients'] = $user[0]['name'] . " ". $user[0]['surname'];
                        if ($user[0]['email'] != "") {
                            $sending_queue_msgs[$key]['recipients'] .= " (".$user[0]['email'] . ")";
                        } else {
                            $sending_queue_msgs[$key]['recipients'] .= " ("._NOUSEREMAILFOUND . ")";
                        }

                    }
                }
            }

            $sending_queue_msgs = eF_multiSort($sending_queue_msgs, $sort, $order);
            $smarty -> assign("T_MESSAGE_QUEUE_SIZE", sizeof($sending_queue_msgs));
            if (isset($_GET['filter'])) {
                $sending_queue_msgs = eF_filterData($sending_queue_msgs, $_GET['filter']);
            }
            if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
                isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
                $sending_queue_msgs = array_slice($sending_queue_msgs, $offset, $limit);
            }

            // This is almost buggy - cannot filter according to recipients count - significant optimization however

            foreach ($sending_queue_msgs as $key => $message) {
                $notification = new EfrontNotification($message);
                if ($notification -> notification['recipient'] == "") {
                    $sending_queue_msgs[$key]['recipients_count'] = sizeof($notification -> getRecipients());
                }
            }

            if (!empty($sending_queue_msgs)) {
                $smarty -> assign("T_QUEUE_MSGS", $sending_queue_msgs);
            }

            $smarty -> display('administrator.tpl');
            exit;
        } else {
            $sending_queue_msgs = eF_getTableData("notifications", "*", "", "timestamp ASC");
            $smarty -> assign("T_QUEUE_MSGS", $sending_queue_msgs);

        }




        $notifications = EfrontNotification::getAllNotifications();
        $events_table = EfrontEvent::getEventTypes(false);

        if (sizeof($notifications) > 0) {

            foreach ($notifications as $key => $notification) {
                // when
                if (isset($notification['event'])) {
                    $notifications[$key]['is_event'] = 1;
                    if ($notification['send_interval'] > 0) {

                        $days = $notification['send_interval'] / 86400;
                        if ($days < 1.0) {
                            $hours = $days * 24;
                            // The span is used for correct sorting of the table
                            $notifications[$key]['when'] = "<span style='display:none'>$days</span>" . $hours . " " . _HOURSAFTEREVENT;
                        } else {
                            $notifications[$key]['when'] = "<span style='display:none'>$days</span>" . $days . " " . _DAYSAFTEREVENT;
                        }
                    } else if ($notification['send_interval'] < 0) {
                        $days = (-1)*$notification['send_interval'] / 86400;
                        if ($days < 1.0) {
                            $hours = $days * 24;
                            $notifications[$key]['when'] = "<span style='display:none'>$days</span>" . $hours . " " . _HOURSBEFOREEVENT;
                        } else {
                            $notifications[$key]['when'] = "<span style='display:none'>$days</span>" . $days . " " . _DAYSBEFOREEVENT;
                        }
                    } else {
                        $notifications[$key]['when'] = "<span style='display:none'>0.00</span>" . _ONEVENT;
                    }
                } else {
                    $timestamp = getdate($notification['timestamp']);
                    $subj_size = strlen($notification['subject']);
                    if ($notification['send_interval']) {
                        $notifications[$key]['when'] = "<span style='display:none'>0.00</span>" . _FROM . " #filter:timestamp_time-" . $notification['timestamp'] . "# " . _EVERY . " " . ($notification['send_interval'] / (24*3600)) . " " . _DAYS;

                        $notifications[$key]['event'] = ($subj_size > 30)?substr($notification['subject'],0, 20) . "..." : $notification['subject'];

                        //$notifications[$key]['timestamp'] = _FROM . " " . $timestamp['mday']. "." . $timestamp['mon']. "." . $timestamp['year']. " " . $timestamp['hours']. ":" . $timestamp['minutes'] . " " . _EVERY . " " . ($notification['send_interval'] / (24*3600)) . " " . _DAYS;
                    } else {
                        $notifications[$key]['when'] = "<span style='display:none'>0.00</span>" . _ON . " #filter:timestamp_time-" . $notification['timestamp'] . "# ";
                        $notifications[$key]['event'] = ($subj_size > 30)?substr($notification['subject'],0, 20) . "..." : $notification['subject'];
                    }
                }

                // recipients
                $notification['send_conditions'] = unserialize($notification['send_conditions']);

                if (isset($notification['send_conditions']['lessons_ID'])) {
                    if ($notification['send_conditions']['lessons_ID'] != 0) {
                        $lesson = new EfrontLesson($notification['send_conditions']['lessons_ID']);

                        if (isset($notification['send_conditions']['user_type'])) {
                            $notifications[$key]['recipients'] = _PROFESSORSOFLESSON . ": " . $lesson -> lesson['name'];
                        } else {
                            $notifications[$key]['recipients'] = _LESSON . ": " . $lesson -> lesson['name'];
                        }
                    } else {
                        $notifications[$key]['recipients'] = _ANYLESSON;
                    }
                } else if (isset($notification['send_conditions']['tests_ID'])) {
                    if ($notification['send_conditions']['tests_ID'] != 0) {
                        $test = new EfrontTest($notification['send_conditions']['tests_ID']);
                        $notifications[$key]['recipients'] = _TEST . ": " . $test -> test['name'];
                    } else {
                        $notifications[$key]['recipients'] = _ANYTEST;

                    }
                } else if (isset($notification['send_conditions']['unit_ID'])) {
                    if ($notification['send_conditions']['tests_ID'] != 0) {
                        $test = new EfrontTest($notification['send_conditions']['tests_ID']);
                        $notifications[$key]['recipients'] = _TEST . ": " . $test -> test['name'];
                    } else {
                        $notifications[$key]['recipients'] = _ANYTEST;

                    }

                } else if (isset($notification['send_conditions']['forum_ID'])) {

                } else if (isset($notification['send_conditions']['groups_ID'])) {
                    if ($notification['send_conditions']['groups_ID'] != 0) {
                        $group = new EfrontGroup($notification['send_conditions']['groups_ID']);
                        $notifications[$key]['recipients'] = _GROUP . ": " . $group -> group['name'];
                    } else {
                        $notifications[$key]['recipients'] = _ANYCOURSE;
                    }
                } else if (isset($notification['send_conditions']['courses_ID'])) {

                    if ($notification['send_conditions']['courses_ID'] != 0) {
                        $course = new EfrontCourse($notification['send_conditions']['courses_ID']);

                        if (isset($notification['send_conditions']['user_type'])) {
                         $notifications[$key]['recipients'] = _PROFESSORSOFCOURSE . ": " . $course -> course['name'];
                        } else {

                         $notifications[$key]['recipients'] .= _COURSE . ": " . $course -> course['name'];

                         if (isset($notification['send_conditions']['completed'])) {
                             $notifications[$key]['recipients'] .= " " . _COMPLETED;
                         }
                        }
                    } else {
                        $notifications[$key]['recipients'] = _ANYCOURSE;
                    }
                } else if (isset($notification['send_conditions']['user_type'])) {
                    $user_type = $notification['send_conditions']['user_type'];
                    if ($user_type == "administrator") {
                        $user_type_name = _ADMINISTRATOR;
                    } else if ($user_type == "professor") {
                        $user_type_name = _PROFESSOR;
                    } else if ($user_type == "student") {
                        $user_type_name = _STUDENT;
                    } else {
                        $user_type = eF_getTableData("user_types", "name", "id = '" . $user_type . "'");
                        $user_type_name = $user_type[0]['name'];
                    }
                    $notifications[$key]['recipients'] = _USERTYPE . ": " . $user_type_name;
                } else {
                    // the event_type is always returned positive from the getAllNotifications method
                    if ($events_table[$notification['event_type']]['category'] == "system" || $events_table[$notification['event_type']]['category'] == "branch" || $events_table[$notification['event_type']]['category'] == "job" || $notification['event_type'] == EfrontEvent::NEW_SYSTEM_ANNOUNCEMENT) {
                        $notifications[$key]['recipients'] = _ALLUSERS;
                    } else {
                        $notifications[$key]['recipients'] = _ALLLESSONUSERS;
                    }

                }
            }

            $smarty -> assign("T_NOTIFICATIONS", $notifications);
        }


        // Notifications configuration form
        $config_form = new HTML_QuickForm("configuration_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=digests&tab=config_tab", "", null, true);

        $config_form -> addElement('advcheckbox', 'notifications_use_cron', _USECRON, null, 'class = "inputCheckbox" id="notifications_use_cron" onClick="onUseCron();" checked=""');

        $config_form -> addElement('text', 'notifications_pageloads', _PAGELOADSBEFORESENDINGNEXTNOTIFICATIONS, 'class = "inputText" id="notificatoins_pageloads"');
        $config_form -> addRule('notifications_pageloads', _POSITIVENUMBERREQUIRED, 'callback', create_function('$a', 'return ($a > 0);')); //The score must be between 0 and 100
        $config_form -> addRule('notifications_pageloads', _THEFIELD.' "'._PAGELOADSBEFORESENDINGNEXTNOTIFICATIONS.'" '._MUSTBENUMERIC, 'numeric', null, 'client');

        $config_form -> addElement('text', 'notifications_maximum_inter_time', _MAXIMUMTIMEBETWEENNOTIFICATIONS, 'class = "inputText" id="notificatoins_maximum_inter_time"');
        $config_form -> addRule('notifications_maximum_inter_time', _POSITIVENUMBERREQUIRED, 'callback', create_function('$a', 'return ($a > 0);')); //The score must be between 0 and 100
        $config_form -> addRule('notifications_maximum_inter_time', _THEFIELD.' "'._MAXIMUMTIMEBETWEENNOTIFICATIONS.'" '._MUSTBENUMERIC, 'numeric', null, 'client');

        $config_form -> addElement('text', 'notifications_messages_per_time', _MESSAGESTOSENDEVERYTIME, 'class = "inputText"');
        $config_form -> addRule('notifications_messages_per_time', _POSITIVENUMBERREQUIRED, 'callback', create_function('$a', 'return ($a > 0);')); //The score must be between 0 and 100
        $config_form -> addRule('notifications_messages_per_time', _THEFIELD.' "'._MESSAGESTOSENDEVERYTIME.'" '._MUSTBENUMERIC, 'numeric', null, 'client');
        $config_form -> addRule('notifications_messages_per_time', _THEFIELD.' "'._MESSAGESTOSENDEVERYTIME.'" '._ISMANDATORY, 'required', null, 'client');

        $config_form -> addElement('text', 'notifications_max_sent_messages', _MAXIMUMSENTMESSAGESSTORED, 'class = "inputText"');
        $config_form -> addRule('notifications_max_sent_messages', _POSITIVENUMBERREQUIRED, 'callback', create_function('$a', 'return ($a > 0);')); //The score must be between 0 and 100
        $config_form -> addRule('notifications_max_sent_messages', _THEFIELD.' "'._MAXIMUMSENTMESSAGESSTORED.'" '._MUSTBENUMERIC, 'numeric', null, 'client');
        $config_form -> addRule('notifications_max_sent_messages', _THEFIELD.' "'._MAXIMUMSENTMESSAGESSTORED.'" '._ISMANDATORY, 'required', null, 'client');

        $config_form -> addElement('submit', 'submit_variables', _SUBMIT, 'class = "flatButton"');

        $notification_configurations = array('notifications_use_cron', 'notifications_pageloads', 'notifications_maximum_inter_time', 'notifications_messages_per_time', 'notifications_max_sent_messages');

        foreach ($notification_configurations as $conf_option) {
            $config_form ->setDefaults(array($conf_option => $GLOBALS['configuration'][$conf_option]));
        }

        if ($config_form -> isSubmitted()) {
            foreach ($notification_configurations as $conf_option) {
                EfrontConfiguration :: setValue($conf_option, $config_form -> exportValue($conf_option));
            }

            // Clear the stored sent messages according to the new limitations
            EfrontNotification::clearSentMessages();

            $message = _NOTIFICATIONCONFIGURATIONSUPDATEDSUCCESSFULLY;
            $message_type = 'success';
            eF_redirect("".$_SESSION['s_type'].".php?ctg=digests&message=". urlencode($message) . "&message_type=" . $message_type);

        }
        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty); //Create a smarty renderer

        $renderer -> setRequiredTemplate (
           '{$html}{if $required}
                &nbsp;<span class = "formRequired">*</span>
            {/if}');

        if (isset($currentUser -> coreAccess['notifications']) && $currentUser -> coreAccess['notifications'] != 'change') {
            $config_form -> freeze();
        }

        $config_form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
        $config_form -> setRequiredNote(_REQUIREDNOTE);
        $config_form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created

        $smarty -> assign('T_NOTIFICATION_VARIABLES_FORM', $renderer -> toArray()); //Assign the form to the template


        // Get recently sent messages - do that after submitting the new global variables
        $smarty -> assign("T_RECENTLY_SENT_NOTIFICATIONS", EfrontNotification::getRecentlySent());

        $options = array(array('image' => '16x16/go_into.png', 'text' => _RESTOREDEFAULTNOTIFICATIONS, 'href' => 'administrator.php?ctg=digests&add_default=1'));
        $smarty -> assign("T_TABLE_OPTIONS", $options);

    }

    }
?>
