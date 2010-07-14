<?php

/**

 * Cron job script

 *

 * This script is used by a cron manager to periodically send the top X unsent email messages

 * from the notifications table.



 * @package eFront

 * @version 3.6.0

 */
//This is needed in order to make cron jobs able to run the file
$dir = getcwd();
chdir(dirname(__FILE__));
$debug_TimeStart = microtime(true); //Debugging timer - initialization
session_cache_limiter('none'); //Initialize session
session_start();
$path = "../libraries/"; //Define default path
/** The configuration file.*/
require_once $path."configuration.php";
$debug_InitTime = microtime(true) - $debug_TimeStart; //Debugging timer - time spent on file inclusion
//Set headers in order to eliminate browser cache (especially IE's)
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
//debug();
$lowest_possible_time = time() - 21600; // last acceptable time - pending 6 hours in the queue to be sent
eF_deleteTableData("notifications", "timestamp != 0 AND timestamp <" . $lowest_possible_time);

//echo G_SERVERNAME;
if (isset($_GET['notification_id'])) {

 try {
  $notification = new EfrontNotification($_GET['notification_id']);

     // Try to send all messages of this notification

     // Get message recipients: one or more
     $recipients = $notification -> getRecipients();
     $sent_messages = 0;
     ////pr ($recipients);
     foreach ($recipients as $login => $recipient) {
      // Send message
      if ($notification -> sendTo($recipient)) { // no limit here
          $sent_messages++;
      }
     }

     // Check if the notification is periodical - if so  arrange (insert) the next notification
     // Note here: generated single recipient notifications should never have a send interval
     if ($notification -> notification['send_interval']) {
      $notification -> scheduleNext();
     } else {
      // Pop this notification - delete it
      eF_deleteTableData("notifications", "id = '". $notification -> notification['id']."'");
     }

 } catch (EfrontNotificationException $e) {
     $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
     $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
 }
} else if (isset($_GET['sent_notification_id'])) {
 $sent_notification = eF_getTableData("sent_notifications", "*", "id = " . $_GET['sent_notification_id']);
 if (!empty ($sent_notification)) {
  $notification = $sent_notification[0];
  // Get recipient's email
  $recipient = substr($notification['recipient'], 0, strpos($notification['recipient'], " "));

  // Check the format of the email
  if (substr($notification['body'],0,5) == "<html>") {
   $onlyText = false;
  } else {
   $onlyText = true;
  }
  if (eF_mail($GLOBALS['configuration']['system_email'], $recipient, $notification['subject'], $notification['body'], false, $onlyText)) {
   $sent_messages = 1;
  } else {
   $sent_messages = 0;
  }
 }

} else {
 //debug();
 $sent_messages = EfrontNotification::sendNextNotifications($GLOBALS['configuration']['notifications_messages_per_time']);
}
//pr($sent_messages);
//debug(false);
if ($GLOBALS['configuration']['notifications_maximum_inter_time'] > 0) {
    EfrontConfiguration::setValue('notifications_last_send_timestamp', time());
}

if ($sent_messages) {
 EfrontNotification::clearSentMessages();
}


if ((!isset($hide_messages) || !$hide_messages) && !isset($_GET['ajax']) && (basename($_SERVER['PHP_SELF']) != 'crontab_notifications.php')) {
 if ($sent_messages) {
  $message = $sent_messages . " notification emails sent successfully";
  $message_type = "success";

 } else {
  $message = "No notification emails have been sent";
  $message_type = "failure";
 }
 eF_redirect($_SESSION['s_type'] .".php?ctg=digests&message=$message&message_type=$message_type&tab=messages_queue");
} else {
 if (!isset($message)) {
  $message = '';
 }
 echo $message. "sent";
}


chdir($dir);

//debug(false);
exit;
?>
