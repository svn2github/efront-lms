<?php

//require_once ($_SERVER['DOCUMENT_ROOT'] . '/../libraries/configuration.php');
require_once ('../../../../libraries/configuration.php');
require_once ('../class_includer.php');

// Taking care of the localisation here
$language = $GLOBALS['configuration']['default_language'];
$default_localisation_path = 'lang/lang-english.php';
$localisation_path = 'lang/lang-'.$language.'.php';

if (!include($localisation_path)) {
 include($default_localisation_path);
}
// END localisation

$response = array();
$response['success'] = false;

if (isset($_POST['id']) && is_numeric($_POST['id']) && !($_POST['id']%1) && $_POST['id'] > 0) {
 $job_id = $_POST['id'];
 try {
  $job = new Job($job_id);
  if ($job->isActive()) {
   $response['success'] = true;
   $job_data = array();
   $job_data['job_id'] = $job_id;
   $job_data['code_title'] = _MOD_JAM_PUBLIC_PAGE_JOB_CODE;
   $job_data['code'] = $job->getCode();
   $job_data['title_title'] = _MOD_JAM_PUBLIC_PAGE_JOB_TITLE;
   $job_data['title'] = $job->getTitle();
   $job_data['desc_title'] = _MOD_JAM_PUBLIC_PAGE_JOB_DESC;
   $job_data['desc'] = $job->getDescription();
   $job_data['type_title'] = _MOD_JAM_PUBLIC_PAGE_JOB_TYPE;
   $job_data['type'] = $job->getType();
   $job_data['remuneration_title'] = _MOD_JAM_PUBLIC_PAGE_JOB_REMUNERATION;
   $job_data['remuneration'] = $job->getRemuneration();
   $job_data['experience_title'] = _MOD_JAM_PUBLIC_PAGE_JOB_EXPERIENCE;
   $job_data['experience'] = $job->getExperience();
   $functions = $job->getFunctions();
   $functions = explode('::',$functions);
   $job_functions = '';
   foreach ($functions AS $function) {
    $job_functions .= $function . '<br/>';
   }
   $job_data['functions'] = $job_functions;
   $job_data['functions_title'] = _MOD_JAM_PUBLIC_PAGE_JOB_FUNCTIONS;
   $job_data['company_desc_title'] = _MOD_JAM_PUBLIC_PAGE_JOB_COMPANY_DESC;
   $job_data['company_desc'] = $job->getCompanyDesc();
   $job_data['skills_title'] = _MOD_JAM_PUBLIC_PAGE_JOB_SKILLS;
   $job_data['skills'] = $job->getSkills();
   $job_data['date_added_title'] = _MOD_JAM_PUBLIC_PAGE_DATE_CREATED;
   $job_data['date_added'] = date('Y-m-d',strtotime($job->getDateAdded()));
   $job_data['apply_for_job'] = _MOD_JAM_PUBLIC_PAGE_FORM_APPLY;

   $response['job_data'] = $job_data;
  }
 }
 catch (Exception $e) { /* DO NOTHING */ }
}

$i18n['please_wait'] = _MOD_JAM_PUBLIC_PAGE_JS_PLEASE_WAIT;
$i18n['submit'] = _MOD_JAM_PUBLIC_PAGE_JS_SUBMIT;
$i18n['thanks'] = _MOD_JAM_PUBLIC_PAGE_JS_THANKS;
$i18n['all_fields'] = _MOD_JAM_PUBLIC_PAGE_JS_ALL_FIELDS;
$i18n['valid_email'] = _MOD_JAM_PUBLIC_PAGE_JS_VALID_EMAIL;
$response['i18n'] = $i18n;

if (!$job) {
 $response['error_msg'] = _MOD_JAM_PUBLIC_PAGE_JOB_NOT_FOUND;
}

$encoded_response = json_encode($response);
die ($encoded_response);
