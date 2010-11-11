<?php

require_once ('../../../../libraries/configuration.php');
require_once ('../class_includer.php');

set_include_path('../../../../libraries/../PEAR/' . PATH_SEPARATOR .
     '../libraries/includes/' . PATH_SEPARATOR .
     '../libraries/' . PATH_SEPARATOR .
     '.' . PATH_SEPARATOR .
     '/usr/lib/php' . PATH_SEPARATOR .
     '/usr/local/lib/php' . PATH_SEPARATOR .
     get_include_path());

$response = array();
$response['success'] = false;
$response['error'] = 'Some error occured while trying to save this application, please try again later!';

$protocol = 'http';
if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
 $protocol = 'https';
}
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . '://' . $host;
if (substr($baseUrl, -1)=='/') {
 $baseUrl = substr($baseUrl, 0, strlen($baseUrl)-1);
}
$cv_path = $baseUrl.$_SERVER['REQUEST_URI'];
$cv_path = substr($cv_path, 0, stripos($cv_path, 'public'));
$cv_path .= 'uploads/';

if (isset($_POST) && isset($_FILES)) {
 $form_error = $_FILES['mod_jam_form_file']['error'];
 if ($form_error == 0) {
  $_SESSION['s_login'] = 'admin';
  // We add a timestamp to the filename
  $_FILES['mod_jam_form_file']['name'] = strtotime(date('Y-m-d H:i:s')) . '_' . $_FILES['mod_jam_form_file']['name'];
  $_FILES['mod_jam_form_file']['name'] = preg_replace('/\s+/', '_', $_FILES['mod_jam_form_file']['name']);
  $_FILES['mod_jam_form_file']['name'] = preg_replace('/\'+/', '_', $_FILES['mod_jam_form_file']['name']);
  $_FILES['mod_jam_form_file']['name'] = preg_replace('/"+/', '_', $_FILES['mod_jam_form_file']['name']);
  $filename = $_FILES['mod_jam_form_file']['name'];
  $settings = new Settings();
  $upload_path = $settings->getUploadPathLocal() . $filename;
  $res = @move_uploaded_file($_FILES['mod_jam_form_file']['tmp_name'], $upload_path);
  if ($res) {
   // All went well with uploading the file, need to create the app entry now.
   $job = false;
   $job_id = $_POST['mod_jam_form_job_id'];
   try {
    $job = new Job($job_id);
   }
   catch (Exception $e) {
    // There's no such job, we cannot proceed.
    $job = false;
    // Uploaded file is getting removed.
    @unlink($settings->getUploadPathLocal().$filename);
   }
   if ($job) {
    $args = array();
    $args ['name'] = $_POST['mod_jam_form_name'];
    $args ['email'] = $_POST['mod_jam_form_email'];
    $args ['phone'] = $_POST['mod_jam_form_phone'];
    $args ['city'] = $_POST['mod_jam_form_city'];
    $args ['country'] = $_POST['mod_jam_form_country'];
    $args ['cover'] = $_POST['mod_jam_form_cover'];
    $args ['cv_filename'] = $filename;
    $args ['job_id'] = $job_id;
    if ($app = $job->addApplication($args)) {
     $response['success'] = true;
     // Sending the email notifications and the reply to applicant here.
     $email_content = $settings->getEmailContent();
     $email_content = str_ireplace('###NAME###',$app->getName(),$email_content);
     $email_content = str_ireplace('###CV###','<a href="'.$cv_path.$app->getCvFilename().'" target="_blank">CV</a>',$email_content);
     $email_content = str_ireplace('###JOB_TITLE###',$job->getTitle(),$email_content);
     $email_content = str_ireplace('###EMAIL###',$app->getEmail(),$email_content);
     $email_content = str_ireplace('###COVER###',$app->getCover(),$email_content);
     $reply_content = $settings->getConfirmationEmailContent();
     $reply_content = str_ireplace('###NAME###',$app->getName(),$reply_content);
     $reply_content = str_ireplace('###CV###','###CV LOCATION NOT DISCLOSED###',$reply_content);
     $reply_content = str_ireplace('###JOB_TITLE###',$job->getTitle(),$reply_content);
     $reply_content = str_ireplace('###EMAIL###',$app->getEmail(),$reply_content);
     $reply_content = str_ireplace('###COVER###',$app->getCover(),$reply_content);
     $reply_text = $reply_content;//'Text version of email';
     $reply_html = '<html><body>'.$reply_content.'</body></html>';
     $crlf = "\n";
     $reply_hdrs = array(
                   'From' => $GLOBALS['configuration']['system_email'],
                   'Subject' => 'Thank you'
                   );
     $mime = new Mail_mime($crlf);
     $mime->setTXTBody($reply_text);
     $mime->setHTMLBody($reply_html);
     //do not ever try to call these next two lines in reverse order
     /**

     * Builds the multipart message from the list ($this->_parts) and

     * returns the mime content.

     *

     * @param array $build_params Build parameters that change the way the email

     *                             is built. Should be associative. Can contain:

     *                head_encoding  -  What encoding to use for the headers. 

     *                                  Options: quoted-printable or base64

     *                                  Default is quoted-printable

     *                text_encoding  -  What encoding to use for plain text

     *                                  Options: 7bit, 8bit,

     *                                  base64, or quoted-printable

     *                                  Default is 7bit

     *                html_encoding  -  What encoding to use for html

     *                                  Options: 7bit, 8bit,

     *                                  base64, or quoted-printable

     *                                  Default is quoted-printable

     *                7bit_wrap      -  Number of characters before text is

     *                                  wrapped in 7bit encoding

     *                                  Default is 998

     *                html_charset   -  The character set to use for html.

     *                                  Default is iso-8859-1

     *                text_charset   -  The character set to use for text.

     *                                  Default is iso-8859-1

     *                head_charset   -  The character set to use for headers.

     *                                  Default is iso-8859-1

     *

     * @return string The mime content

     * @access public

     */
     $params = array('html_charset'=>'utf-8', 'text_charset'=>'utf-8', 'head_charset'=>'utf-8', 'html_encoding'=>'base64', 'text_encoding'=>'base64');
     $reply_body = $mime->get($params);
     $reply_hdrs = $mime->headers($reply_hdrs);
     $options = array();
     $options ['auth'] = ($GLOBALS['configuration']['smtp_auth'] ? true : false);
     $options ['host'] = $GLOBALS['configuration']['smtp_host'];
     $options ['password'] = $GLOBALS['configuration']['smtp_pass'];
     $options ['port'] = $GLOBALS['configuration']['smtp_port'];
     $options ['username'] = $GLOBALS['configuration']['smtp_user'];
     $options ['timeout'] = $GLOBALS['configuration']['smtp_timeout'];
     $smtp = Mail::factory('smtp', $options);
     $result = $smtp -> send($args ['email'], $reply_hdrs, $reply_body);
     unset($mime);
     $set_emails = $settings->getSetEmails();
     $to = '';
     foreach ($set_emails AS $email) {
      $to .= $email . ',';
     }
     $to = substr($to,0,-1);
     $alert_text = $email_content;//'Text version of email';
     $alert_html = '<html><body>'.$email_content.'</body></html>';
     $crlf = "\n";
     $alert_hdrs = array(
                   'From' => $GLOBALS['configuration']['system_email'],
                   'Subject' => 'New Job Application'
                   );
     $mime = new Mail_mime($crlf);
     $mime->setTXTBody($alert_text);
     $mime->setHTMLBody($alert_html);
     //do not ever try to call these next two lines in reverse order
     $alert_body = $mime->get($params);
     $alert_hdrs = $mime->headers($alert_hdrs);
     $options = array();
     $options ['auth'] = ($GLOBALS['configuration']['smtp_auth'] ? true : false);
     $options ['host'] = $GLOBALS['configuration']['smtp_host'];
     $options ['password'] = $GLOBALS['configuration']['smtp_pass'];
     $options ['port'] = $GLOBALS['configuration']['smtp_port'];
     $options ['username'] = $GLOBALS['configuration']['smtp_user'];
     $options ['timeout'] = $GLOBALS['configuration']['smtp_timeout'];
     $smtp = Mail::factory('smtp', $options);
     $result = $smtp -> send($to, $alert_hdrs, $alert_body);
    }
    else {
     // Couldn't create the new app
     // Uploaded file is getting removed.
     @unlink($settings->getUploadPathLocal().$filename);
    }
   }
  }
 }
}
$encoded_response = json_encode($response);
die($encoded_response);
