<?php
/**
 *  This module implements the external survey procedure.
 *  Author: Nick "shadukan" Mpallas
 *  Contact: nmpallas@efront.gr
 *  efront ver: 3.*
 * 
*/
$path = "../libraries/"; //Define default path
$load_editor="true";
/** The configuration file.*/
require_once $path."configuration.php";

if(!isset($_GET['username']) || !eF_checkParameter($_GET['username'],'login') || !isset($_GET['coupon']) || !isset($_GET['surveys_ID'])){
    $smarty -> assign("T_ACCESS","-1");
}else{
    $valid_coupon = hash('md5',$_GET['username'].$_GET['surveys_ID'].G_MD5KEY);
    if($valid_coupon != $_GET['coupon']){
        $smarty -> assign("T_ACCESS","-1");
    }else{
       if(!isset($_GET['screen'])){
                $smarty -> assign("T_SCREEN","1");
       }else{
            $smarty -> assign("T_SCREEN",$_GET['screen']);
       }
       if( isset($_GET['screen']) && $_GET['screen'] == '2'){
           $load_editor=true;
       }
       if (isset($_GET['surveys_ID']) && eF_checkParameter($_GET['surveys_ID'],'id')) {
            $surveys_ID = $_GET['surveys_ID'];
       }
       $survey_data = eF_getTableData("surveys","id,survey_name,survey_info,start_text,end_text,status","id=".$surveys_ID);
       $survey_questions = eF_getSurveyQuestions($surveys_ID);
       $survey_name = eF_getTableData("surveys","survey_name","id=".$surveys_ID);
       $smarty -> assign("T_SURVEYNAME",$survey_name[0]['survey_name']);
       //$email='"'.$_GET['email'].'"';
       //$user = eF_getTableData("users","login","email=".$email);
       $user_done_survey = eF_getTableData('users_to_done_surveys','done','surveys_ID='.$surveys_ID.' AND users_LOGIN="'.$_GET['username'].'"');

       if ($user_done_survey[0]['done'] == 1 || $survey_data[0]['status'] == 0) {
           $smarty -> assign("T_ACCESS","-1");
       }else{
           $smarty -> assign("T_SURVEY_INFO",$survey_data);
           $smarty -> assign("T_SURVEY_INFOTEXT",$survey_data[0]['survey_info']);
           $smarty -> assign("T_SURVEY_STARTTEXT",$survey_data[0]['start_text']);
           $smarty -> assign("T_SURVEY_QUESTIONS",$survey_questions);
           if (isset($_GET['op']) && strcmp($_GET['op'],"survey_store") == 0) {
               $survey_data = eF_getTableData("surveys","id,end_text","id=".$surveys_ID);
               $smarty -> assign("T_SURVEY_INFO",$survey_data);
            if (isset($_POST['surveys_ID']) && !eF_checkParameter($_POST['surveys_ID'],'id')) {
        $smarty -> assign("T_ACCESS","-1");
            } else {
                 for ($i = 0 ; $i < sizeof(eF_getSurveyQuestions($_POST['surveys_ID'])) ; $i++) {
                     $answers = array('users_LOGIN' => $_GET['username'],
                             'surveys_ID' => $surveys_ID,
                             'question_ID' => $_POST['question_ID'][$i],
                             'user_answers' => serialize($_POST['answer'][$i]),
                             'submited' =>(string)time());
                     eF_insertTableData("survey_questions_done",$answers);
                 }
                 eF_insertTableData("users_to_done_surveys",array('surveys_ID' => $surveys_ID,'users_LOGIN' => $_GET['username'],'done' => 1));
           }
        }
        }
    }
}
//Main scripts, such as prototype
$mainScripts = getMainScripts();
$smarty -> assign("T_HEADER_MAIN_SCRIPTS", implode(",", $mainScripts));
$smarty -> display('external_survey.tpl');

?>
