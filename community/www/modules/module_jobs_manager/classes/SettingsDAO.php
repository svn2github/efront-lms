<?php

class SettingsDAO extends AbstractDAO {

 private $_db;
 private $_set_emails;

 public function __construct($controller) {
  parent::__construct($controller);
  $this->_db = $GLOBALS['db'];
  $this->_init();
 }

 public function __destruct() {
  if ($this->_to->isChanged()) {
   // Fixing $GLOBALS-in-destructor bug
   $GLOBALS['db'] = $this->_db;
   // Fixing the recipient emails
   $set_emails = $this->extractFromTO('set_emails');
   if ($set_emails <> $this->_set_emails) {
    // Deleting all old email recipients
    eF_deleteTableData('`mod_jam_recipients`','`id` > 0');
    $insert_data = array();
    foreach ($set_emails AS $email) {
     if (!in_array($email, $insert_data)) {
      $insert_data [] = array('`email`' => $email);
     }
    }
    if (count($insert_data)) {
     eF_insertTableDataMultiple('`mod_jam_recipients`', $insert_data);
    }
   }
   // Fixing the settings table with page_background, list_location etc.
   $args = array();
   $args ['`logo_filename`'] = $this->extractFromTO('logo_filename');
   $args ['`list_location`'] = $this->extractFromTO('list_location');
   $args ['`list_type`'] = $this->extractFromTO('list_type');
   $args ['`about_content`'] = $this->extractFromTO('about_content');
   $args ['`email_content`'] = $this->extractFromTO('email_content');
   $args ['`send_name`'] = $this->extractFromTO('send_name');
   $args ['`send_email`'] = $this->extractFromTO('send_email');
   $args ['`send_phone`'] = $this->extractFromTO('send_phone');
   $args ['`send_cv_url`'] = $this->extractFromTO('send_cv_url');
   $args ['`confirmation_email_content`'] = $this->extractFromTO('confirmation_email_content');
   eF_updateTableData('`mod_jam_settings`', $args, '1 = 1'); // update all entries - just one present.
  }
 }

 /**

	 * Retrieving contents of the `mod_jam_job` table so as to initialise 

	 * the state of the job manager.

	 * 

	 * @access public

	 */
 public function _init() {
  // Retrieving the list of all administrators
  $user_array = array();
  $res = eF_getTableData('users',"`id`,`email`,`name`,`surname`", "`user_type`='administrator'");
  if (is_array($res) && count($res)) {
   foreach ($res AS $key => $user) {
    $user_data = array();
    $user_data['full_name'] = $user['surname'] . ', ' . $user['name'];
    $user_data['email'] = $user['email'];
    $user_array[$user['id']] = $user_data;
   }
  }
  $this->_to->set('all_emails', $user_array);
  // Retrieving the list of emails that have been set to receive alerts
  $email_array = array();
  $res = eF_getTableData('`mod_jam_recipients`','`email`');
  if (is_array($res) && count($res)) {
   foreach ($res AS $key => $email) {
    $email_array [] = $email ['email'];
   }
  }
  $this->_to->set('set_emails',$email_array);
  $this->_set_emails = $email_array;
  // Retrieving the rest of the settings
  $logo_filename = false;
  $about_content = false;
  $list_location = false;
  $list_type = false;
  $email_content = false;
  $confirmation_email_content = false;
  $send_name = false;
  $send_email = false;
  $send_phone = false;
  $send_cv_url = false;
  $res = eF_getTableData('`mod_jam_settings`','`send_email`,`send_name`,`send_phone`,`send_cv_url`,`confirmation_email_content`,`logo_filename`,`about_content`,`list_location`,`list_type`,`email_content`');
  if (is_array($res) && count($res)) {
   $logo_filename = $res [0]['logo_filename'];
   $list_location = $res [0]['list_location'];
   $list_type = $res [0]['list_type'];
   $about_content = $res [0]['about_content'];
   $email_content = $res [0]['email_content'];
   $send_name = $res [0]['send_name'];
   $send_email = $res [0]['send_email'];
   $send_phone = $res [0]['send_phone'];
   $send_cv_url = $res [0]['send_cv_url'];
   $confirmation_email_content = $res [0]['confirmation_email_content'];
  }
  $this->_to->set('logo_filename',$logo_filename);
  $this->_to->set('list_location',$list_location);
  $this->_to->set('list_type',$list_type);
  $this->_to->set('about_content',$about_content);
  $this->_to->set('email_content',$email_content);
  $this->_to->set('send_name',$send_name);
  $this->_to->set('send_email',$send_email);
  $this->_to->set('send_phone',$send_phone);
  $this->_to->set('send_cv_url',$send_cv_url);
  $this->_to->set('confirmation_email_content',$confirmation_email_content);
 }
}
