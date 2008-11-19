<?php
/***
 * This script manipulates the creation,publication and modification of the surveys
 * package efront
 * Author: Nikos "shadukan" Mpallas
 * created: -/05/2007
 * ver 3.*
 * Issues:
 *          - Problem with the javascript of the html_quick form when combined with the editor.
 */ 
    //$db -> debug = true;
    //$user_type = eF_getTableDataFlat("users","user_type",'login=\''.$_SESSION['s_login'].'\'');
    $smarty -> assign("T_USER",$_SESSION['s_type']);
    $smarty -> assign("T_CTG","survey");
    switch($_SESSION['s_type']){
    case 'administrator':
        $smarty -> assign("T_CATEGORY","survey");
        $smarty -> assign("T_CTG","survey");
        break;
    case 'professor':
        //Some basic initializations needed for the surveys and their procedures.
        $load_editor = true;
        $smarty -> assign("T_CATEGORY","survey");
        $smarty -> assign("T_CTG","survey");
        $smarty -> assign("T_EDITOR",$load_editor);
        $lesson_id = $_SESSION['s_lessons_ID'];
        $smarty -> assign("T_LESSON_ID",$lesson_id);
        if( !isset($_GET['screen_survey']) ) { $survey = eF_getSurveyInfo($lesson_id); }
        if(sizeof($survey['survey_info']) != 0){
            $smarty -> assign("T_SURVEY_INFO",$survey['survey_info']);
        }else{
            $smarty -> assign("T_SURVEY_INFO","0");
        }
        if(sizeof($survey['survey_questions']) == 0){
            $smarty ->assign("T_SURVEY_QUESTIONS","0");
        }else{
            $smarty -> assign("T_SURVEY_QUESTIONS",$survey['survey_questions']);
        }
        /**
         * Here is implemented the creation of the form that creates a new survey,using the quick_form pear module.
        */
        if( isset($_GET['action']) && strcmp($_GET['action'],"create_survey") == 0 ){
          
            if( isset($_GET['survey_action']) && strcmp($_GET['survey_action'],"update") == 0 ){
                $survey_data = eF_getTableData("surveys","*","id=".$_GET['surveys_ID']);
            }
            
            if( isset($_GET['survey_action']) && strcmp($_GET['survey_action'],"create") == 0 ){
                $form = new Html_QuickForm("createSurvey","post","professor.php?ctg=survey&action=create_survey&survey_action=save","",null,true);
            }else{
                $form = new Html_QuickForm("createSurvey","post","professor.php?ctg=survey&action=create_survey&survey_action=store_update&surveys_ID=".$_GET['surveys_ID'],"",null,true);
            }
            
            if( isset($_GET['survey_action']) && strcmp($_GET['survey_action'],"create") == 0 ){
                $form -> addElement('text','surveyCode',null,'class="inputText"');
                $form -> addRule('surveyCode', _THEFIELD.' '._SURVEYCODE.' '._ISMANDATORY, 'required', null, 'client');
            }else{
                $form -> addElement('text','surveyCode',null,'class="inputText"');
                $form -> setDefaults(array('surveyCode'=> $survey_data[0]['survey_code']));
            }
            if( isset($_GET['survey_action']) && strcmp($_GET['survey_action'],"create") == 0 ){
                $form -> addElement('text','surveyName',null,'class="inputText inputText_QuestionChoice"');
                $form -> addRule('surveyName', _THEFIELD.' '._SURVEYNAME.' '._ISMANDATORY, 'required', null, 'client');
                
            }else{
                $form -> addElement('text','surveyName',null,'class="inputText inputText_QuestionChoice"');
                $form -> setDefaults(array('surveyName' => $survey_data[0]['survey_name']));
            }
            if( isset($_GET['survey_action']) && strcmp($_GET['survey_action'],"create") == 0 ){
                $form -> addElement('text','surveyInfo',null,'class="inputText inputText_QuestionChoice"');
                $form -> addRule('surveyInfo', _THEFIELD.' '._SURVEYINFO.' '._ISMANDATORY, 'required', null, 'client');
                
            }else{
                $form -> addElement('text','surveyInfo',null,'class="inputText inputText_QuestionChoice"');
                $form -> setDefaults(array('surveyInfo' => $survey_data[0]['survey_info']));
            }
            if( isset($_GET['survey_action']) && strcmp($_GET['survey_action'],"create") == 0 ){
                $form -> addElement('textarea','surveyIntro',null,'class="simpleEditor rows="5" cols="60""');
            }else{
                $form -> addElement('textarea','surveyIntro',null,'class="simpleEditor" rows="5" cols="60"');
                $form -> setDefaults(array('surveyIntro' => $survey_data[0]['start_text']));
            }
            if( isset($_GET['survey_action']) && strcmp($_GET['survey_action'],"create") == 0 ){
                $form -> addElement('textarea','surveyEnd',null,'class="simpleEditor" rows="5" cols="60"');
            }else{
                $form -> addElement('textarea','surveyEnd',null,'class="simpleEditor" rows="5" cols="60"');
                $form -> setDefaults(array('surveyEnd' => $survey_data[0]['end_text']));
            }
        if( isset($_GET['survey_action']) && strcmp($_GET['survey_action'],"update") == 0){
            $smarty -> assign("T_START_DATE",$survey_data[0]['start_date']);
            $smarty -> assign("T_END_DATE",$survey_data[0]['end_date']);
        }
            if($form -> isSubmitted()){
                if($form -> validate()){
                    $survey_info = array('survey_code' => $form -> exportValue('surveyCode'),
                             'survey_name' => $form -> exportValue('surveyName'),
                             'survey_info' => $form -> exportValue('surveyInfo'),
                             'author'      => $_SESSION['s_login'],
                             'lang'        => $_SESSION['s_language'],
                             'start_date'  => (string) mktime(0,0,0,$_POST['Date_Month1'],$_POST['Date_Day1'],$_POST['Date_Year1']), 
                             'end_date'    => (string)mktime(0,0,0,$_POST['Date_Month2'],$_POST['Date_Day2'],$_POST['Date_Year2']),
                             'lessons_ID'  => $_SESSION['s_lessons_ID'],
                             'status'      => $_POST['status'] == ''? 0 : $_POST['status'],
                             'start_text'  => $form -> exportValue('surveyIntro'),
                             'end_text'    => $form -> exportValue('surveyEnd'));
            
                    if( strcmp($_GET['survey_action'],"save") == 0 ){
                        if (eF_insertTableData("surveys",$survey_info)) {
                            header("location:professor.php?ctg=survey&t_enter_create=1");
                        } else {
                            header("location:professor.php?ctg=survey&t_enter_create=-1");
                        }
                    }
                    if( strcmp($_GET['survey_action'],"store_update") == 0 ){
            if(eF_updateTableData("surveys",$survey_info,'id='.intval($_GET['surveys_ID']))){
                            header("location:professor.php?ctg=survey&t_enter_update=1");
                        }else{
                            header("location:professor.php?ctg=survey&t_enter_update=-1");
                        }
                    }
                }
            }
            
            
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);
            $smarty -> assign('T_CREATE_SURVEY', $renderer -> toArray());

        }
        
        if( isset($_GET['action']) && strcmp($_GET['action'],"delete") == 0 ){
            $smarty -> assign("T_SURVEY_OPERATION","delete");
            if( eF_deleteTableData('surveys','id='.$_GET['surveys_ID']) && eF_deleteTableData('users_to_done_surveys','surveys_ID='.$_GET['surveys_ID']) && eF_deleteTableData('users_to_surveys','surveys_ID='.$_GET['surveys_ID']) && eF_deleteTableData('questions_to_surveys','surveys_ID='.$_GET['surveys_ID']) && eF_deleteTableData('survey_questions_done','surveys_ID='.$_GET['surveys_ID']) ){
                header("location:professor.php?ctg=survey&t_enter_delete=1");
            }else{
                header("location:professor.php?ctg=survey&t_enter_delete=-1");
            }
        }
    if( isset($_GET['action']) && strcmp($_GET['action'],"change_status") == 0 ){
        if( isset($_GET['survey_action']) && strcmp($_GET['survey_action'],"deactivate_survey") == 0 ){
            eF_updateTableData("surveys",array('status' => 0),"id=".$_GET['surveys_ID']);
            header("location:professor.php?ctg=survey&t_activate=-1");
        }
        if( isset($_GET['survey_action']) && strcmp($_GET['survey_action'],"activate_survey") == 0 ){
            eF_updateTableData("surveys",array('status' => 1),"id=".$_GET['surveys_ID']);
            header("location:professor.php?ctg=survey&t_activate=1");
        }
    }
    /**
     *This fragment of code implements the publish procedure of a survey.
     */
    if( isset($_GET['action']) && strcmp($_GET['action'],"publish") == 0 ){
        if( isset($_GET['publish_action']) && strcmp($_GET['publish_action'],"true") == 0 ){
            $s_users = eF_getTableDataFlat("users_to_surveys","users_LOGIN","surveys_ID=".$_GET['surveys_ID']);
            //pr($s_users);
            $survey_users = eF_getTableDataFlat("users_to_done_surveys","users_LOGIN","surveys_ID=".$_GET['surveys_ID']);
            foreach($_POST['user_login'] as $key => $value){
                if( strcmp($_POST['selection'][$key],"on") == 0 ){
                    if( in_array($_POST['user_login'][$key],$survey_users['users_LOGIN']) == FALSE ){
                        $active_survey_users[$i] = $value;
                        $coupon = hash('md5',$_POST['user_email'][$key].$_GET['surveys_ID'].G_MD5KEY);
                        $users_data = array('surveys_ID' => $_GET['surveys_ID'],
                                            'users_LOGIN' => $_POST['user_login'][$key],
                                            'last_access' => NULL,
                                            'last_post' => NULL);
                        $inbox = eF_getTableData("f_folders","id",'users_LOGIN="'.$_POST['user_login'][$key].'" AND name="Incoming"');
                        $survey_name = eF_getTableData("surveys","survey_name","id=".intVal($_GET['surveys_ID']));
                        $message_array = array('users_LOGIN' => $_POST['user_login'][$key],
                                               'recipient' => $_POST['user_login'][$key],
                                               'sender' => $_SESSION['s_login'],
                                               'timestamp' => (string)time(),
                                               'attachments' => null,
                                               'title' => _NEWSURVEYPUBLISHED.':'.$survey_name[0]['survey_name'],
                                               'body' => $_POST['email_intro'].'<br>'._USER.':'.$_POST['user_login'][$key].'<br><br> <a href="student.php?ctg=survey&surveys_ID='.$_GET['surveys_ID'].'&screen_survey=1">'._DOSURVEY.'</a><br>'._THANKYOU,
                                               'f_folders_ID' => intVal($inbox[0]['id']),
                                               'viewed' => 'no');
                        if( !in_array($_POST['user_login'][$key],$s_users['users_LOGIN']) ){
                            if( isset($_POST['send_email']) && $_POST['send_email'] == '1'){
                                $professor = '"'.$_SESSION['s_login'].'"';
                                $from = eF_getTableData("users","email","login=".$professor);
                                $subject_nonutf = _NEWSURVEYPUBLISHED.':'.$survey_name[0]['survey_name'];
                                $subject = mb_convert_encoding($subject_nonutf,'UTF-8');
                                $body = $_POST['email_intro']."\n"._EMAIL.':'.$_POST['user_email'][$key]."\n".'<a href="'.G_SERVERNAME.'external_survey.php?email='.$_POST['user_email'][$key].'&coupon='.$coupon.'&surveys_ID='.$_GET['surveys_ID'].'">'._DOSURVEY."</a>\n".G_SERVERNAME.'external_survey.php?email='.$_POST['user_email'][$key].'&coupon='.$coupon.'&surveys_ID='.$_GET['surveys_ID']."\n"._THANKYOU;
                                eF_mail($from[0]['email'],$_POST['user_email'][$key],$subject,$body,false,true);
                            }
                            if(eF_insertTableData("users_to_surveys",$users_data) &&  eF_insertTableData("f_personal_messages",$message_array)){
                                header("location:professor.php?ctg=survey&published=1");
                            }else{
                                header("location:professor.php?ctg=survey&published=-1");
                            }
                        }else{
                            if( isset($_POST['send_email']) && $_POST['send_email'] == '1'){
                                $professor = '"'.$_SESSION['s_login'].'"';
                                $from = eF_getTableData("users","email","login=".$professor);
                                $subject_nonutf = _NEWSURVEYPUBLISHED.':'.$survey_name[0]['survey_name'];
                                $subject = mb_convert_encoding($subject_nonutf,'UTF-8');
                                $body = $_POST['email_intro']."\n"._EMAIL.':'.$_POST['user_email'][$key]."\n".'<a href="'.G_SERVERNAME.'external_survey.php?email='.$_POST['user_email'][$key].'&coupon='.$coupon.'&surveys_ID='.$_GET['surveys_ID'].'">'._DOSURVEY."</a>\n".G_SERVERNAME.'external_survey.php?email='.$_POST['user_email'][$key].'&coupon='.$coupon.'&surveys_ID='.$_GET['surveys_ID']."\n"._THANKYOU;
                                eF_mail($from[0]['email'],$_POST['user_email'][$key],$subject,$body,false,true);
                            }
                            if(eF_insertTableData("f_personal_messages",$message_array)){
                                header("location:professor.php?ctg=survey&published=1");
                            }else{
                                header("location:professor.php?ctg=survey&published=-1");
                            }
                        }
                    }
                }
            }
            eF_updateTableData("surveys",array('status' => 1),"id=".$_GET['surveys_ID']);
        }else{
            $student='"student"';
            $survey_users = eF_getTableData("users_to_surveys","users_LOGIN","surveys_ID=".$_GET['surveys_ID']);
            $lesson_users = eF_getTableData("users,users_to_lessons","users.email,users.login,users.name,users.surname","users_to_lessons.lessons_ID=".$_SESSION['s_lessons_ID']." AND users.login = users_to_lessons.users_LOGIN AND users_to_lessons.user_type=".$student);
			$lesson_users2 = eF_getTableData("users,users_to_lessons,user_types","users.email,users.login,users.name,users.surname","users_to_lessons.lessons_ID=".$_SESSION['s_lessons_ID']." AND users.login = users_to_lessons.users_LOGIN AND users_to_lessons.user_type=user_types.id AND user_types.basic_user_type=".$student);

			if (!empty($lesson_users2)) {
				$lesson_users = array_merge($lesson_users, $lesson_users2);
			}
            for($i = 0 ; $i < sizeof($survey_users) ; $i +=1){
                for($j = 0 ; $j < sizeof($lesson_users) ; $j +=1){
                    if($survey_users[$i]['users_LOGIN'] == $lesson_users[$j]['login']){
                        $exists[$j] = "true";
                        break;
                    }else{
                        continue;
                    }   
                }
            }
            $survey_name = eF_getTableData("surveys","survey_name","id=".$_GET['surveys_ID']);
            $smarty -> assign("T_SURVEYNAME",$survey_name[0]['survey_name']);
            $smarty -> assign("T_EXISTS",$exists);
            $smarty -> assign("T_LESSON_USERS",$lesson_users);
            $form = new Html_QuickForm("createSurvey","post","professor.php?ctg=survey&surveys_ID=".$_GET['surveys_ID']."&action=publish&publish_action=true","",null,true);
            $form -> addElement("textarea","email_intro",null,'class="simpleEditor" rows="5" cols="40"');
            $form -> addElement("checkbox","send_email",null,null);
            $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
            $form -> accept($renderer);
            $smarty -> assign('T_PUBLISH_FORM', $renderer -> toArray());
        }
    }
    if(isset($_GET['action'])  && strcmp($_GET['action'],"view_users") == 0 ){
        $survey_name = eF_getTableData("surveys","survey_name","id=".$_GET['surveys_ID']);
        $smarty -> assign("T_SURVEYNAME",$survey_name[0]['survey_name']);
        if( isset($_GET['preview_action']) && strcmp($_GET['preview_action'],"delete_user") == 0){
                $user= $_GET['user'];
                if(eF_deleteTableData('users_to_done_surveys','users_LOGIN="'.$user.'" AND surveys_ID='.$_GET['surveys_ID']) && eF_deleteTableData('survey_questions_done','users_LOGIN="'.$user.'" AND surveys_ID='.$_GET['surveys_ID'])){
                    header("location:professor.php?ctg=survey&action=view_users&t_delete=1&surveys_ID=".$_GET['surveys_ID']);
                }else{
                    header("location:professor.php?ctg=survey&action=view_users&t_delete=-1&surveys_ID=".$_GET['surveys_ID']);
                }
        }
        $survey_users = eF_getTableData("users_to_surveys uts,users u","uts.users_LOGIN,u.name,u.surname","u.login = uts.users_LOGIN AND surveys_ID=".$_GET['surveys_ID']);
        $survey_users_done = eF_getTableData("users_to_done_surveys","users_LOGIN","surveys_ID=".$_GET['surveys_ID']);
        for($i = 0 ; $i < sizeof($survey_users_done) ; $i +=1){
            for($j = 0 ; $j < sizeof($survey_users) ; $j +=1){
                if($survey_users_done[$i]['users_LOGIN'] == $survey_users[$j]['users_LOGIN']){
                    $done_survey[$j]="true";
                    break;
                }else{
                    continue;
                }
            }
        }
        $smarty -> assign("T_DONE_SURVEY",$done_survey);
        $smarty -> assign("T_SURVEY_USERS",$survey_users);
        $smarty -> assign("T_SIZEOF_USERS",sizeof($survey_users));
        $smarty -> assign("T_SURVEY_USERS_DONE",$survey_users_done);
    }
    /**
     * The following section implements the statistics module of the surveys.
     */
    if(isset($_GET['action'])  && strcmp($_GET['action'],"statistics") == 0 ){
        $survey_name = eF_getTableData("surveys","survey_name","id=".$_GET['surveys_ID']);
        $smarty -> assign("T_SURVEYNAME",$survey_name[0]['survey_name']);
        if(isset($_GET['surveys_ID'])) { $surveys_ID = $_GET['surveys_ID']; }
        else { $surveys_ID = $_POST['surveys_ID']; }
        $survey_users = eF_getTableData("users_to_surveys","count(*)","surveys_ID=".$surveys_ID);//Counting survey users
        $survey_done_users = eF_getTableData("users_to_done_surveys","count(*)","surveys_ID=".$surveys_ID);//Done users
        $smarty -> assign("T_TOTAL_DONE_USERS",$survey_users[0]['count(*)']);
        $survey_statistics = eF_getSurveyStatistics($surveys_ID);//Getting survey statistics.
        
        /** Getting the questions of the survey */
        for($i = 0 ; $i < sizeof($survey_statistics['questions']) ; $i ++){
            $question_choices[$i] = unserialize($survey_statistics['questions'][$i]['answers']);
            //pr($question_choices[$i]);
        }
        /** Gathering information about the survey in general */
        $survey_info = eF_getTableData("surveys","survey_code,survey_name,survey_info,start_date,end_date"," id=".$surveys_ID);
        $smarty -> assign("T_SURVEY_INFO",$survey_info);
        /**
        *   Doing stuff to calculate the percentage of the answers, used along with the above function.
        *   Maybe it could be a part of the eF_getSurveyStatistics function(Under consideration)
        */
        
        $vote = array();
        for($i = 0 ; $i < sizeof($survey_statistics['questions']) ; $i ++){
            $general = unserialize($survey_statistics['questions'][$i]['answers']);
            $type = array_keys($general);
            $num_choices = array_keys($general[$type[0]]);
            for($j = 0 ; $j < sizeof($num_choices) ; $j ++)
                $vote[$i][$j] = 0;
            
            for($j = 0 ; $j < sizeof($survey_statistics['votes']) ; $j ++){
                if($type[0] == 'multiple_many'){
                    $keys = array_keys($survey_statistics['votes'][$j][$i]);
                    for($k = 0 ; $k < sizeof($survey_statistics['votes'][$j][$i]) ; $k++){
                        $vote[$i][$keys[$k]] +=1;
                    }
                }else{
                    $vote[$i][$survey_statistics['votes'][$j][$i]] +=1;
                }
            }
        }
        //Calculating the overall percentage of a choice.
        for($i = 0 ; $i < sizeof($vote) ; $i ++){
            $keys = array_keys($vote[$i]);
            for($j = 0 ; $j < sizeof($keys) ; $j++){
                $value = $vote[$i][$keys[$j]]/intVal($survey_done_users[0]['count(*)']);
                $percentage[$i][$j] = round($value*100,2);
            }
        }
        
        //Assigning the appropriate smarty variables with values.
        $users_all = eF_getTableData("users_to_surveys","count(*)","surveys_ID=".$surveys_ID);
        $users_done = eF_getTableData("users_to_done_surveys","count(*)","surveys_ID=".$surveys_ID);
        $smarty -> assign("T_USERS_OVERALL",$users_all[0]['count(*)']);
        $smarty -> assign("T_USERS_DONE",$users_done[0]['count(*)']);
        $smarty -> assign("T_SURVEY_ANSWER_RATE",$percentage);
        $smarty -> assign("T_SURVEY_QUESTIONS",$survey_statistics['questions']);
        $smarty -> assign("T_SURVEY_QUESTIONS_CHOICES",$question_choices);
        $smarty -> assign("T_SURVEY_VOTES",$vote);
        if( isset($_GET['statistics_action']) && strcmp($_GET['statistics_action'],"export") == 0 ){
            //Including the required headers for exporting using excel format.
            require_once 'Spreadsheet/Excel/Writer.php';
            
            $exportSheet = new Spreadsheet_Excel_Writer();
            $exportSheet -> setVersion(8);
            $exportSheet -> send(_SURVEYSTATISTICS.'-'.$survey_name[0]['survey_name'].'.xls');
            
            
            $eF_formatExcelHeaders = & $exportSheet -> addFormat();
            $eF_formatExcelHeaders ->setSize(14);
            $eF_formatExcelHeaders ->setBold();
            $eF_formatExcelHeaders ->setHAlign('left');
            
            $eF_formatFields = & $exportSheet -> addFormat();
            $eF_formatFields -> setBold();
            $eF_formatFields ->setItalic();
            $eF_formatFields ->setHAlign('left');
            
            
            $eF_formatContent = & $exportSheet -> addFormat();
            $eF_formatContent ->setHAlign('left');
            $eF_formatContent ->setVAlign('bottom');
            
            $survey_statistics = eF_getSurveyStatistics($surveys_ID);//Getting survey statistics.
        
            /** Getting the questions of the survey */
            for($i = 0 ; $i < sizeof($survey_statistics['questions']) ; $i ++){
                $question_choices[$i] = unserialize($survey_statistics['questions'][$i]['answers']);
            }
            /**
            *   Doing stuff to calculate the percentage of the answers, used along with the above function.
            *   Maybe it could be a part of the eF_getSurveyStatistics function(Under consideration)
            */
        
            $vote = array();
            for($i = 0 ; $i < sizeof($survey_statistics['questions']) ; $i ++){
                $general = unserialize($survey_statistics['questions'][$i]['answers']);
                $type = array_keys($general);
                $num_choices = array_keys($general[$type[0]]);
                for($j = 0 ; $j < sizeof($num_choices) ; $j ++)
                    $vote[$i][$j] = 0;
                
                for($j = 0 ; $j < sizeof($survey_statistics['votes']) ; $j ++){
                    if($type[0] == 'multiple_many'){
                        $keys = array_keys($survey_statistics['votes'][$j][$i]);
                        for($k = 0 ; $k < sizeof($survey_statistics['votes'][$j][$i]) ; $k++){
                            $vote[$i][$keys[$k]] +=1;
                        }
                    }else{
                        $vote[$i][$survey_statistics['votes'][$j][$i]] +=1;
                    }
                }
            }
            //Calculating the overall percentage of a choice.
            for($i = 0 ; $i < sizeof($vote) ; $i ++){
                $keys = array_keys($vote[$i]);
                for($j = 0 ; $j < sizeof($keys) ; $j++){
                    $value = $vote[$i][$keys[$j]]/intVal($survey_done_users[0]['count(*)']);
                    $percentage[$i][$j] = round($value*100,2);
                }
            }
            
            $statsWorkSheet = & $exportSheet->addWorksheet(_SURVEYSTATISTICS.'-'.$survey_name[0]['survey_name']);
            $statsWorkSheet -> setInputEncoding('UTF-8');
            $statsWorkSheet -> write(0,0,_SURVEYNAME,$eF_formatFields);
            $statsWorkSheet -> write(0,1,$survey_name[0]['survey_name'],$eF_formatContent);
            $statsWorkSheet -> write(1,0,_SUBTITLE,$eF_formatFields);
            $statsWorkSheet -> write(1,1,$survey_info[0]['survey_info'],$eF_formatContent);
            $statsWorkSheet -> write(2,0,_USERS,$eF_formatFields);
            $statsWorkSheet -> write(2,1,$users_all[0]['count(*)'],$eF_formatContent);
            
            $survey_questions = eF_getTableData("questions_to_surveys","type,question,answers,father_ID","surveys_ID=".$_GET['surveys_ID'],"father_ID ASC");
            
            $write_row = 5;
            $write_column = 2;
            $statsWorkSheet -> write(4,1,_QUESTIONNUMBER,$eF_formatFields);
            $statsWorkSheet -> write(5,0,_USERS,$eF_formatFields);
            $writer_row = 5;
            $writer_column = 1;
            for($i = 0 ; $i < sizeof($survey_questions) ; $i ++){
                $key = array_keys(unserialize($survey_questions[$i]['answers']));
                $choice_array = unserialize($survey_questions[$i]['answers']);
                $choices = sizeof($choice_array[$key[0]]);
                if($key[0] == 'multiple_many'){
                    for($j = 0 ; $j  < $choices ; $j ++){
                        $k = $j +1;
                        $statsWorkSheet -> write($writer_row,$writer_column,$survey_questions[$i]['father_ID'].','.$k,$eF_formatContent);
                        $writer_column++;
                    }
                }else{
                    $statsWorkSheet -> write($writer_row,$writer_column,$survey_questions[$i]['father_ID'],$eF_formatContent);
                    $writer_column++;
                }
            }
            
            $writer_row = 6;
            $writer_column = 0;
            
            $survey_users = eF_getTableData("users_to_done_surveys","users_LOGIN","surveys_ID=".$_GET['surveys_ID']);
            for($i = 0 ; $i < sizeof($survey_users) ; $i ++){
                $login = '"'.$survey_users[$i]['users_LOGIN'].'"';
                $answers = eF_getTableData("survey_questions_done sqd,questions_to_surveys qts","sqd.user_answers,qts.type,sqd.question_ID","sqd.question_ID=qts.id AND sqd.surveys_ID=".$_GET['surveys_ID']." AND qts.surveys_ID=".$_GET['surveys_ID']." AND sqd.users_LOGIN=".$login,"qts.father_ID ASC");
                $answers_keys = array_keys($answers);
                $statsWorkSheet -> write($writer_row,$writer_column,$survey_users[$i]['users_LOGIN'],$eF_formatContent);
                $writer_column++;
                
                for($j = 0 ; $j < sizeof($answers_keys) ; $j ++){
                    $answer = unserialize($answers[$answers_keys[$j]]['user_answers']);
                    $question_choices= eF_getTableData("questions_to_surveys","answers","surveys_ID=".$_GET['surveys_ID']." AND id=".$answers[$answers_keys[$j]]['question_ID']);            
                    $tmp_array = unserialize($question_choices[0]['answers']);
                    $tmp_keys = array_keys($tmp_array);
                    $new_write = sizeof($tmp_array[$tmp_keys[0]]);
                    if($answers[$answers_keys[$j]]['type'] == 'multiple_many'){
                        $answer = unserialize($answers[$answers_keys[$j]]['user_answers']);
                        $m_keys = array_keys($answer);
                        for($k = 0 ; $k < sizeof($answer) ; $k ++){
                            $column = $writer_column+intval($m_keys[$k]);
                            $statsWorkSheet -> write($writer_row,$column,"x",$eF_formatContent);
                        }
                        $writer_column +=$new_write;
                    }else{
                        $answer = unserialize($answers[$answers_keys[$j]]['user_answers']);
                        if($answers[$answers_keys[$j]]['type'] == 'development'){
                            $statsWorkSheet -> write($writer_row,$writer_column,strip_tags($answer),$eF_formatContent);
                        }else{
                                $question_answers = unserialize($question_choices[0]['answers']);
                                $answer_keys = array_keys($question_answers);
                                $key = array_search($answer,$question_answers[$answer_keys[0]]);
                                $choice = $key+1;
                                $statsWorkSheet -> write($writer_row,$writer_column,$choice,$eF_formatContent);
                        }
                        $writer_column++;
                    }
               }
               $writer_column = 0;
               $writer_row ++;
            }
            $writer_column = 0 ;
            $writer_row +=2;
            $survey_questions = eF_getSurveyQuestions($_GET['surveys_ID']);
            for($i = 0 ; $i < sizeof($survey_questions) ; $i ++) {
                $writer_column = 0;
                $statsWorkSheet -> write($writer_row,$writer_column,_QUESTION,$eF_formatFields);
                $writer_column++;
                $statsWorkSheet -> write($writer_row,$writer_column,$survey_questions[$i]['father_ID'],$eF_formatContent);
                $writer_column++;
                $statsWorkSheet -> write($writer_row,$writer_column,strip_tags($survey_questions[$i]['question']),$eF_formatContent);
                $writer_row++;
                $writer_column=0;
                $statsWorkSheet -> write($writer_row,$writer_column,_QUESTIONTYPE,$eF_formatFields);
                $writer_column+=2;
                if($survey_questions[$i]['type'] == 'yes_no'){
                    $statsWorkSheet -> write($writer_row,$writer_column,_YES_NO,$eF_formatContent);
                }
                if($survey_questions[$i]['type'] == 'development'){
                    $statsWorkSheet -> write($writer_row,$writer_column,_DEVELOPMENT,$eF_formatContent);
                }
                if($survey_questions[$i]['type'] == 'dropdown'){
                    $statsWorkSheet -> write($writer_row,$writer_column,_DROPDOWN,$eF_formatContent);
                }
                if($survey_questions[$i]['type'] == 'multiple_one'){
                    $statsWorkSheet -> write($writer_row,$writer_column,_MULTIPLEONE,$eF_formatContent);
                }
                if($survey_questions[$i]['type'] == 'multiple_many'){
                    $statsWorkSheet -> write($writer_row,$writer_column,_MULTIPLEMANY,$eF_formatContent);
                }
                $writer_column++;
                $statsWorkSheet -> write($writer_row,$writer_column,_VOTES,$eF_formatFields);
                $writer_column++;
                $statsWorkSheet -> write($writer_row,$writer_column,_PERCENTAGE."(%)",$eF_formatFields);
                $writer_row++;
                $writer_column-=4;
                $statsWorkSheet -> write($writer_row,$writer_column,_SELECTION,$eF_formatFields);
                $writer_column = 1;
                $question_answers = unserialize($survey_questions[$i]['answers']);
                $key = array_keys($question_answers);
                for($j = 0 ; $j < sizeof($question_answers[$key[0]]) ; $j ++){
                    $statsWorkSheet -> write($writer_row,$writer_column,$j+1,$eF_formatContent);
                    $writer_column++;
                    $statsWorkSheet -> write(($writer_row),$writer_column,trim($question_answers[$key[0]][$j]),$eF_formatContent);
                    $writer_column++;
                    $statsWorkSheet -> write(($writer_row),$writer_column,$vote[$i][$j],$eF_formatContent);
                    $writer_column++;
                    $statsWorkSheet -> write(($writer_row),$writer_column,$percentage[$i][$j],$eF_formatContent);
                    $writer_row++;
                    $writer_column-=3;
                }
                $writer_row++;
            }
            
            $exportSheet -> close();
        }
    }
    
    //The preview of the survey as it would be seen by the users of the survey for the professor account.
    if(isset($_GET['action'])  && strcmp($_GET['action'],"preview") == 0){
        $survey_name = eF_getTableData("surveys","survey_name","id=".$_GET['surveys_ID']);
        $smarty -> assign("T_SURVEYNAME",$survey_name[0]['survey_name']);
        $survey_data = eF_getTableData("surveys","id,survey_code,survey_name,survey_info,start_date,end_date,end_text","id=".$_GET['surveys_ID']);
        $survey_questions = eF_getSurveyQuestions($_GET['surveys_ID']);
        $smarty -> assign("T_SURVEY_INFO",$survey_data);
        $smarty -> assign("T_SURVEY_QUESTIONS",$survey_questions);
        $smarty -> assign("T_SIZEOF_QUESTIONS",sizeof($survey_questions));
    }
    //The preview of a survey for a specific user.
    if(isset($_GET['action']) && strcmp($_GET['action'],"survey_preview") == 0){
        $survey_name = eF_getTableData("surveys","survey_name","id=".$_GET['surveys_ID']);
        $smarty -> assign("T_SURVEYNAME",$survey_name[0]['survey_name']);
        $survey_data = eF_getTableData("surveys","id,survey_code,survey_name,survey_info,start_date,end_date,end_text","id=".$_GET['surveys_ID']);
        $survey_questions = eF_getSurveyQuestions($_GET['surveys_ID']);
        $users_LOGIN='"'.$_GET['user'].'"';
        $user_answers = eF_getTableData("survey_questions_done sqd,questions_to_surveys qts","qts.type,sqd.user_answers","sqd.question_ID = qts.id AND sqd.surveys_ID=".$_GET['surveys_ID']." AND sqd.users_LOGIN=".$users_LOGIN,"qts.father_ID ASC");
        $smarty -> assign("T_SURVEY_INFO",$survey_data);
        $smarty -> assign("T_SURVEY_QUESTIONS",$survey_questions);
        $smarty -> assign("T_USER_ANSWERS",$user_answers);
    }
    //The second screen with the questions of the survey.Here we load the data we need to display the screen.
    if(isset($_GET['screen_survey']) && $_GET['screen_survey'] == '2'){
        $survey_questions_info = eF_getSurveyQuestions($_GET['surveys_ID']);
        $survey_name = eF_getTableData("surveys","survey_name","id=".$_GET['surveys_ID']);
        $smarty -> assign("T_SURVEYNAME",$survey_name[0]['survey_name']);
        $survey_questions = array();
            
        for($i = 0 ; $i < sizeof($survey_questions_info) ; $i ++){
            $survey_questions_info[$i]['question'] = strip_tags($survey_questions_info[$i]['question']);
        }
        
        for($i = 0 ; $i < sizeof($survey_questions_info) ; $i ++){
            $data = unserialize($survey_questions_info[$i]['answers']);
            $keys = array_keys($data);
            $survey_questions[$i] =  sizeof($data[$keys[0]]);
        }

            if($survey_questions_info == 0){
                $smarty -> assign("T_NOQUESTIONSFORSURVEY",_NOQUESTIONSFORSURVEY);
            }else{
                $smarty -> assign("T_SURVEY_QUESTIONS_INFO",$survey_questions_info);
                $smarty -> assign("T_SURVEY_QUESTIONS",$survey_questions);
            }
        }
        /**
         * Here we manipulate the questions of the survey and the actions we can have on them
         * The creator can edit/delete/preview and change the place of a question.
         * package efront
         * ver 3.*
        */
       
        //Deleting a question from the current survey and updating the father_ids
        if(isset($_GET['action']) && strcmp($_GET['action'],"delete_question") == 0){
            $deleted_father_ID = eF_getTableData("questions_to_surveys","father_ID","id=".$_GET['question_ID']);
            if(eF_deleteTableData("questions_to_surveys","id=".intVal($_GET['question_ID'])) && eF_deleteTableData("survey_questions_done","question_ID=".intVal($_GET['question_ID']))){
                $father_IDs = eF_getTableData("questions_to_surveys","id,father_ID","surveys_ID=".$_GET['surveys_ID']." AND father_ID > ".intVal($deleted_father_ID[0]['father_ID']),"father_ID asc");
                for($i = 0 ; $i < (sizeof($father_IDs[0])/2)+1 ; $i ++){
                    if(intVal($father_IDs[0]['father_ID']) > intVal($deleted_father_ID[0]['father_ID'])){
                        $new_father_ID = intVal($father_IDs[$i]['father_ID']) - 1;
                        eF_updateTableData("questions_to_surveys",array('father_ID' => $new_father_ID),"id=".$father_IDs[$i]['id']);
                    }else{
                        continue;
                    }
                }
                header("location:professor.php?ctg=survey&screen_survey=2&question_deleted=1&surveys_ID=".$_GET['surveys_ID']);
            }else{
                header("location:professor.php?ctg=survey&screen_survey=2&question_deleted=-1&surveys_ID=".$_GET['surveys_ID']);
            }
        }
        //Changing the sorting of questions
        if(isset($_GET['action']) && strcmp($_GET['action'],"swap_question") == 0){
            $survey_name = eF_getTableData("surveys","survey_name","id=".$_GET['surveys_ID']);
            $smarty -> assign("T_SURVEYNAME",$survey_name[0]['survey_name']);
            if(strcmp($_GET['swap_action'],"move_up") == 0){
                if( strcmp($_GET['father_ID'],"1") == 0 && sizeof(eF_getSurveyQuestions($_GET['surveys_ID'])) <= 1 ){
                    header("location:professor.php?ctg=survey&screen_survey=2&question_swap=-1&surveys_ID=".$_GET['surveys_ID']);
                }else{
                    $new_id = intVal($_GET['father_ID'])-1;
                    $previous_question_id = eF_getTableData("questions_to_surveys","id","father_ID=".$new_id." AND surveys_ID=".$_GET['surveys_ID']);
                    $swap_id = intVal($_GET['father_ID']);
                    if(eF_updateTableData("questions_to_surveys",array('father_ID' => $new_id),"id=".$_GET['question_ID']." AND surveys_ID=".$_GET['surveys_ID']) && eF_updateTableData("questions_to_surveys",array('father_ID' => $swap_id),"id=".$previous_question_id[0]['id']." AND surveys_ID=".intVal($_GET['surveys_ID']))){
                        header("location:professor.php?ctg=survey&screen_survey=2&question_swap=1&surveys_ID=".$_GET['surveys_ID']);
                    }else{
                        header("location:professor.php?ctg=survey&screen_survey=2&question_swap=-1&surveys_ID=".$_GET['surveys_ID']);
                    }
                }
            }else if(strcmp($_GET['swap_action'],"move_down") == 0){
                if( strcmp($_GET['father_ID'],"1") == 0 && sizeof(eF_getSurveyQuestions($_GET['surveys_ID'])) <= 1){
                    header("location:professor.php?ctg=survey&screen_survey=2&question_swap=-1&surveys_ID=".$_GET['surveys_ID']);
                }else{
                    $new_id = intVal($_GET['father_ID'])+1;
                    $next_question_id = eF_getTableData("questions_to_surveys","id","father_ID=".$new_id." AND surveys_ID=".$_GET['surveys_ID']);
                    $swap_id = intVal($_GET['father_ID']);
                    if(eF_updateTableData("questions_to_surveys",array('father_ID' => $new_id),"id=".$_GET['question_ID']." AND surveys_ID=".$_GET['surveys_ID']) && eF_updateTableData("questions_to_surveys",array('father_ID' => $swap_id),"id=".$next_question_id[0]['id']." AND surveys_ID=".intVal($_GET['surveys_ID']))){
                        header("location:professor.php?ctg=survey&screen_survey=2&question_swap=1&surveys_ID=".$_GET['surveys_ID']);
                    }else{
                        header("location:professor.php?ctg=survey&screen_survey=2&question_swap=-1&surveys_ID=".$_GET['surveys_ID']);
                    }
                }
            }else{
                header("location:professor.php?ctg=survey&screen_survey=2&question_swap=-2&surveys_ID=".$_GET['surveys_ID']);
            }
        }
        //Adding a question to the current survey
        if(isset($_GET['action']) && strcmp($_GET['action'],"question_create") == 0){
        $survey_name = eF_getTableData("surveys","survey_name","id=".$_GET['surveys_ID']);
        $smarty -> assign("T_SURVEYNAME",$survey_name[0]['survey_name']);
        //Here we define the appropriate action,meaning that is an update or a creation of a question.
        if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"update_question") == 0){
            $question_form = new Html_QuickForm("addQuestion","post","professor.php?ctg=survey&action=question_create&question_action=store_update","",null,true);
        }else{
            $question_form = new Html_QuickForm("addQuestion","post","professor.php?ctg=survey&action=question_create&question_action=save","",null,true);
        }
        
        //If i have an update operation i obtain here the data for the specific question.
        if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"update_question") == 0){
            $question_data = eF_getTableData("questions_to_surveys","*","id=".$_GET['question_ID']);
        }
        //Determine the question_type.
        if( isset($_GET['question_type']) ){
            $question_type = $_GET['question_type'];
        }else{
            $question_type = $question_data[0]['type'];
        }
        //Loading in the fields the appropriate information according to the action.
        if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"update_question") == 0){
            $question_form -> addElement('textarea','question_text',null,'class = "mceEditor" style = "width:100%;height:20em;"');
            $question_form -> setDefaults(array('question_text' => $question_data[0]['question']));
        }else{
            $question_form -> addElement('textarea','question_text',null,'class = "mceEditor" style = "width:100%;height:20em;"');
        }
        //Performing operations depending upon the type of the question.
        if( strcmp($question_type,"yes_no") == 0 ){
            if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"update_question") == 0){
                $yes_no = unserialize($question_data[0]['answers']);
                $question_form -> addElement('text','yes_no[0]',null,null);
                $question_form -> setDefaults(array('yes_no[0]' => $yes_no[0]));
                $question_form -> addElement('text','yes_no[1]',null,null);
                $question_form -> setDefaults(array('yes_no[1]' => $yes_no[1]));
            }else{
                $question_form -> addElement('text','yes_no[0]',null,null);
                $question_form -> setDefaults(array('yes_no[0]' => _YES));
                $question_form -> addElement('text','yes_no[1]',null,null);
                $question_form -> setDefaults(array('yes_no[1]' => _NO));
            }
                }
                if( strcmp($question_type,"dropdown") == 0 ){
            if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"update_question") == 0 ){
                $drop_down = unserialize($question_data[0]['answers']);
                for($i = 0 ; $i < sizeof($drop_down['drop_down']) ; $i ++){
                    $question_form -> addElement('text','drop_down['.$i.']',null,'class = "inputText inputText_QuestionChoice"');
                    $question_form -> setDefaults(array('drop_down['.$i.']' => $drop_down['drop_down'][$i]));
                    if($i == 0 || $i == 1)
                        $question_form -> addRule('drop_down['.$i.']', _THEFIELD._ISMANDATORY, 'required', null, 'client');
                }
            }else{
                $question_form -> addElement('text','drop_down[0]',null,'class = "inputText inputText_QuestionChoice"');
                $question_form -> addRule('drop_down[0]', _THEFIELD._ISMANDATORY, 'required', null, 'client');
                $question_form -> addElement('text','drop_down[1]',null,'class = "inputText inputText_QuestionChoice"');
                $question_form -> addRule('drop_down[1]', _THEFIELD._ISMANDATORY, 'required', null, 'client');
            }
                }
        
                if( strcmp($question_type,"multiple_one") == 0 ){
            if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"update_question") == 0 ){
                $multiple_one = unserialize($question_data[0]['answers']);
                for($i = 0 ; $i < sizeof($multiple_one['multiple_one']) ; $i ++){
                    $question_form -> addElement('text','multiple_one['.$i.']',null,'class = "inputText inputText_QuestionChoice"');
                    $question_form -> setDefaults(array('multiple_one['.$i.']' => $multiple_one['multiple_one'][$i]));
                    if($i == 0 || $i == 1)
                        $question_form -> addRule('multiple_one['.$i.']', _THEFIELD._ISMANDATORY, 'required', null, 'client');
                }
            }else{
                $question_form -> addElement('text','multiple_one[0]',null,'class = "inputText inputText_QuestionChoice"');
                $question_form -> addRule('multiple_one[0]', _THEFIELD._ISMANDATORY, 'required', null, 'client');
                $question_form -> addElement('text','multiple_one[1]',null,'class = "inputText inputText_QuestionChoice"');
                $question_form -> addRule('multiple_one[1]', _THEFIELD._ISMANDATORY, 'required', null, 'client');
            }
                }
        
                if( strcmp($question_type,"multiple_many") == 0 ){
            if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"update_question") == 0 ){
                $multiple_many = unserialize($question_data[0]['answers']);
                for($i = 0 ; $i < sizeof($multiple_many['multiple_many']) ; $i ++){
                    $question_form -> addElement('text','multiple_many['.$i.']',null,'class = "inputText inputText_QuestionChoice"');
                    $question_form -> setDefaults(array('multiple_many['.$i.']' => $multiple_many['multiple_many'][$i]));
                    if($i == 0 || $i == 1)
                        $question_form -> addRule('multiple_many['.$i.']', _THEFIELD._ISMANDATORY, 'required', null, 'client');
                }
            }else{
                $question_form -> addElement('text','multiple_many[0]',null,'class = "inputText inputText_QuestionChoice"');
                $question_form -> addRule('multiple_many[0]', _THEFIELD._ISMANDATORY, 'required', null, 'client');
                $question_form -> addElement('text','multiple_many[1]',null,'class = "inputText inputText_QuestionChoice"');
                $question_form -> addRule('multiple_many[1]', _THEFIELD._ISMANDATORY, 'required', null, 'client');
            }
                }
                //Performing action of storing a question from a creation or an update that the professor did.
                if($question_form -> isSubmitted()){
                    if($question_form -> validate()){
            
            //Determine the question_type.
            if( isset($_POST['question_type']) ){
                $question_type = $_POST['question_type'];
            }else{
                $question_type = $question_data[0]['type'];
            }
            if( strcmp($question_type,"yes_no") == 0 ){
                if( isset($_GET['question_action']) && strcmp($_GET['question_action'],"store_update") != 0 ){
                    $father = eF_getTableData("questions_to_surveys","max(father_ID)","surveys_ID=".intval($_POST['surveys_ID']));
                    if(sizeof($father[0]['max(father_ID)']) == 0) { $father_ID = 1; }
                    else { $father_ID = intval($father[0]['max(father_ID)']) + 1;}
                }else{
                    $father_ID = $_POST['father_ID'];
                }
                $keys = array_keys($_POST);
                $index = sizeof($keys) -1;
                $answers_array = array_slice($_POST,$index);
                            $surveyQuestionData = array('surveys_ID' => $_POST['surveys_ID'],
                                'type' => $_POST['question_type'],
                                'question' => $_POST['question_text'],
                                'answers' => serialize($answers_array),
                                'created' => time(),
                                'info' => null,
                                'father_ID' => $father_ID);
                        }
            if( strcmp($question_type,"development") == 0){
                            if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"store_update") != 0 ){
                $father = eF_getTableData("questions_to_surveys","max(father_ID)","surveys_ID=".intval($_POST['surveys_ID']));
                if(sizeof($father[0]['max(father_ID)']) == 0) { $father_ID = 1; }
                else { $father_ID = intval($father[0]['max(father_ID)']) + 1;}  
                }else{
                    $father_ID = $_POST['father_ID'];
                }
                $keys = array_keys($_POST);
                $index = sizeof($keys) -1;
                $answers_array = array_slice($_POST,$index);
                
                            $surveyQuestionData = array('surveys_ID' => $_POST['surveys_ID'],
                                'type' => $_POST['question_type'],
                                'question' => $_POST['question_text'],
                                'answers' => $_POST['question_answer'],
                                'created' => time(),
                                'info' => null,
                                'father_ID' => $father_ID);
                        }
            if( strcmp($question_type,"dropdown") == 0 ){
                            if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"store_update") != 0){
                $father = eF_getTableData("questions_to_surveys","max(father_ID)","surveys_ID=".intval($_POST['surveys_ID']));
                if(sizeof($father[0]['max(father_ID)']) == 0) { $father_ID = 1; }
                else { $father_ID = intval($father[0]['max(father_ID)']) + 1;}  
                }else{
                   $father_ID = $_POST['father_ID'];
                }
                //Determining the size of the answer array and extracting it from the $_POST array.
                $keys = array_keys($_POST);
                $index = sizeof($keys) -1;
                $answers_array = array_slice($_POST,$index);

                            //The data array.
                            $surveyQuestionData = array('surveys_ID' => $_POST['surveys_ID'],
                                'type' => $_POST['question_type'],
                                'question' => $_POST['question_text'],
                                'answers' =>  serialize($answers_array),
                                'created' => time(),
                                'info' => null,
                                'father_ID' => $father_ID);
            }
            if( strcmp($question_type,"multiple_one") == 0 ){
                           if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"store_update") != 0 ){
                $father = eF_getTableData("questions_to_surveys","max(father_ID)","surveys_ID=".intval($_POST['surveys_ID']));
                if(sizeof($father[0]['max(father_ID)']) == 0) { $father_ID = 1; }
                else { $father_ID = intval($father[0]['max(father_ID)']) + 1;}  
                }else{
                    $father_ID = $_POST['father_ID'];
                }
                //Determining the size of the answer array and extracting it from the $_POST array.
                $keys = array_keys($_POST);
                $index = sizeof($keys) -1;
                $answers_array = array_slice($_POST,$index);

                            //The data array.
                            $surveyQuestionData = array('surveys_ID' => $_POST['surveys_ID'],
                                'type' => $_POST['question_type'],
                                'question' => $_POST['question_text'],
                                'answers' => serialize($answers_array),
                                'created' => time(),
                                'info' => null,
                                'father_ID' => $father_ID);
            }
            if( strcmp($question_type,"multiple_many") == 0){
                           if(isset($_GET['question_action']) && strcmp($_GET['question_action'],"save") == 0){
                $father = eF_getTableData("questions_to_surveys","max(father_ID)","surveys_ID=".intval($_POST['surveys_ID']));
                if(sizeof($father[0]['max(father_ID)']) == 0) { $father_ID = 1; }
                else { $father_ID = intval($father[0]['max(father_ID)']) + 1;}  
                }else{
                    $father_ID = $_POST['father_ID'];
                }
                
                            //Determining the size of the answer array and extracting it from the $_POST array.
                $keys = array_keys($_POST);
                $index = sizeof($keys) -1;
                $answers_array = array_slice($_POST,$index);

                            //The data array.
                            $surveyQuestionData = array('surveys_ID' => $_POST['surveys_ID'],
                                'type' => $_POST['question_type'],
                                'question' => $_POST['question_text'],
                                'answers' => serialize($answers_array),
                                'created' => time(),
                                'info' => null,
                                'father_ID' => $father_ID);
            }
            
            if( isset($_GET['question_action']) && strcmp($_GET['question_action'],"store_update") == 0 ){
                if(eF_updateTableData("questions_to_surveys",$surveyQuestionData,"id=".$_POST['question_ID'])){
                    header("location:professor.php?ctg=survey&screen_survey=2&question_updated=1&surveys_ID=".$_POST['surveys_ID']);
                }else{
                    header("location:professor.php?ctg=survey&screen_survey=2&question_updated=-1&surveys_ID=".$_POST['surveys_ID']);
                }
            }
            if( isset($_GET['question_action']) && strcmp($_GET['question_action'],"save") == 0 ){
                if(eF_insertTableData("questions_to_surveys",$surveyQuestionData)){
                    header("location:professor.php?ctg=survey&screen_survey=2&question_added=1&surveys_ID=".$_POST['surveys_ID']);
                }else{
                    header("location:professor.php?ctg=survey&screen_survey=2&question_added=-1&surveys_ID=".$_POST['surveys_ID']);
                }
            }
                    }
                }
        
        $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
        $question_form -> accept($renderer);

        $smarty -> assign('T_ADD_QUESTION', $renderer -> toArray());
        }
        break;
    case 'student':
       $smarty -> assign("T_CATEGORY","survey");
       $smarty -> assign("T_CTG","survey");
       $survey_name = eF_getTableData("surveys","survey_name","id=".$_GET['surveys_ID']);
       $smarty -> assign("T_SURVEYNAME",$survey_name[0]['survey_name']); 
       if (isset($_GET['surveys_ID'])) {
            $surveys_ID = $_GET['surveys_ID'];
       } else {
            $surveys_ID = $_POST['surveys_ID'];
       }
       $user_done_survey = eF_getTableData('users_to_done_surveys','done','surveys_ID='.$surveys_ID.' AND users_LOGIN="'.$_SESSION['s_login'].'"');
       $survey_data = eF_getTableData("surveys","id,survey_name,survey_info,start_text,end_text,status","id=".$surveys_ID);
       $survey_questions = eF_getSurveyQuestions($_GET['surveys_ID']); 
       $smarty -> assign("T_CTG","survey");
       if ($user_done_survey[0]['done'] == 1  || $survey_data[0]['status'] == 0) {
           $smarty -> assign("T_DO_TEST","-1");
       } 
       else {
        //$user_type = eF_getTableDataFlat("users","user_type",'login=\''.$_SESSION['s_login'].'\'');
        $smarty -> assign("T_USER",$_SESSION['s_type']);
        $smarty -> assign("T_SURVEY_INFO",$survey_data);
        $smarty -> assign("T_SURVEY_INFOTEXT",$survey_data[0]['survey_info']);
        $smarty -> assign("T_SURVEY_STARTTEXT",$survey_data[0]['start_text']);
        $smarty -> assign("T_SURVEY_QUESTIONS",$survey_questions);
        if (isset($_GET['op']) && strcmp($_GET['op'],"survey_store") == 0) {
            $survey_data = eF_getTableData("surveys","id,end_text","id=".$surveys_ID);
            $smarty -> assign("T_SURVEY_INFO",$survey_data);
            for ($i = 0 ; $i < sizeof(eF_getSurveyQuestions($_POST['surveys_ID'])) ; $i++) {
                $answers = array('users_LOGIN' => $_SESSION['s_login'],
                         'surveys_ID' => $surveys_ID,
                         'question_ID' => $_POST['question_ID'][$i],
                         'user_answers' => serialize($_POST['answer'][$i]),
                         'submited' =>(string)time());
                eF_insertTableData("survey_questions_done",$answers);
        }
            eF_insertTableData("users_to_done_surveys",array('surveys_ID' => $surveys_ID,'users_LOGIN' => $_SESSION['s_login'],'done' => 1));
        }
       }
        break;
    }
?>
