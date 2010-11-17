<?php

define("_FUZE_MEETINGS","FUZE conferences");

// ADMIN INTERFACE STUFF
define("_FUZE_ADMIN_NOT_REGISTERED_YET","You are not registered with the FUZE Conferencing service yet. Please press the button below to register.");
define("_FUZE_ADMIN_USER_AMOUNT","FUZE accounts found on the system");
define("_FUZE_ADMIN_USER_SELECT_DEFAULT","Please select a professor");
define("_FUZE_ADMIN_USER_NOT_FOUND","This user does not have a FUZE account.");
define("_FUZE_ADMIN_USER_NOT_SUSPENDED","This FUZE account could not be suspended.");
define("_FUZE_ADMIN_USER_DATA_EMAIL","User FUZE email address");
define("_FUZE_ADMIN_USER_DATA_PASSWORD","User FUZE password");
define("_FUZE_ADMIN_USER_DATA_DATE_ADDED","FUZE account created");

define("_FUZE_ADMIN_REGISTER_BTN","Register");
define("_FUZE_ADMIN_USER_CREATE_BTN","Create a trial account for this user");
define("_FUZE_ADMIN_USER_SUSPEND_BTN","Suspend this FUZE account");
define("_FUZE_ADMIN_USER_LOGIN_BTN","Login on FUZE as this user");


define("_FUZE_ADMIN_ERROR_REGISTRATION_UNKNOWN","An unknown error occured during registration, please try again later.");
define("_FUZE_ADMIN_ERROR_COULDNT_GET_USER_DATA","Could not fetch user data currently, please try again later.");


// PROFESSOR INTERFACE STUFF
define("_FUZE_PROF_ACCOUNT_CREATE","Create a FUZE account now!");
define("_FUZE_PROF_ACCOUNT_BULLET_1","Use your FUZE meetings account to schedule online meetings, share your screen or make a presentation.");
define("_FUZE_PROF_ACCOUNT_BULLET_2","Your account will automatically be mapped with your new FUZE meetings account. You are just one click away!");
define("_FUZE_PROF_ACCOUNT_BULLET_3","The trial includes free meetings for 30 days. You can upgrade directly on the FUZE website.");
define("_FUZE_PROF_ACCOUNT_SUSPENDED","Your FUZE account has temporarily been suspended by the system administrator. Please contact your administrator for further details.");
define("_FUZE_PROF_MEETING_HOST","Host a meeting now");
define("_FUZE_PROF_MEETING_SCHEDULE","Schedule a meeting");
define("_FUZE_PROF_MEETING_EDIT","Edit meetings");
define("_FUZE_PROF_MEETING_ALL","All meetings");
define("_FUZE_PROF_MEETING_AMOUNT_PREFIX","You have scheduled");
define("_FUZE_PROF_MEETING_AMOUNT_NEXT","Next meeting");
define("_FUZE_PROF_MEETING_AMOUNT_MEETING","meeting");
define("_FUZE_PROF_MEETING_AMOUNT_MEETINGS","meetings");
define("_FUZE_PROF_MEETING_AMOUNT_MEETING_NONE", "You have no meetings planned currently.");
define("_FUZE_PROF_MEETING_CPANEL_TABLE_TITLE", "Meeting name");
define("_FUZE_PROF_MEETING_CPANEL_TABLE_WHEN", "When");
define("_FUZE_PROF_MEETING_CPANEL_TABLE_LINK", "Link");

// PROFESSOR SCHEDULE A MEETING PAGE
define("_FUZE_PROF_SCHEDULE_MEETING_NAME","Meeting name");
define("_FUZE_PROF_SCHEDULE_MEETING_SCHEDULE","Schedule");
define("_FUZE_PROF_SCHEDULE_MEETING_LESSON","Related lesson");
define("_FUZE_PROF_SCHEDULE_MEETING_PARTICIPANTS","Participants");
define("_FUZE_PROF_SCHEDULE_MEETING_LOGIN","Login");
define("_FUZE_PROF_SCHEDULE_MEETING_PARTICIPANT_NAME","Name");
define("_FUZE_PROF_SCHEDULE_MEETING_PARTICIPANT_SURNAME","Surname");
define("_FUZE_PROF_SCHEDULE_MEETING_PARTICIPANT_SELECTED","Selected");
define("_FUZE_PROF_SCHEDULE_MEETING_SEND_EMAIL", "Send invites to selected participants");
define("_FUZE_PROF_SCHEDULE_MEETING_EDIT_SEND_EMAIL", "Notify selected participants of changes");
define("_FUZE_PROF_SCHEDULE_MEETING_CALENDAR", "Add a calendar event to selected participants");
define("_FUZE_PROF_SCHEDULE_MEETING_BTN", "Schedule meeting");
define("_FUZE_PROF_SCHEDULE_MEETING_EDIT_BTN", "Edit meeting");
define("_FUZE_PROF_SCHEDULE_MEETING_SELECT", "Please select a lesson for this meeting");

define("_FUZE_PROF_SCHEDULE_ALL_FIELDS_MANDATORY", "Please fill in all fields.");
define("_FUZE_PROF_SCHEDULE_NO_STUDENTS_ERROR", "You have not selected any students for this meeting.");
define("_FUZE_PROF_SCHEDULE_DATE_TIME_ERROR", "Please check the provided date and time.");

// PROFESSOR HOST MEETING
define("_FUZE_PROF_HOST_MEETING_BTN", "Start the meeting");

define("_FUZE_PROF_CPANEL_GO_TO_MEETING", "Start");
define("_FUZE_STUDENT_CPANEL_GO_TO_MEETING", "Join");

define("_FUZE_EMAIL_MEETING_NOTIFICATION_SUBJECT","You are invited to a new meeting [###MEETING_NAME###]");
define("_FUZE_EMAIL_MEETING_NOTIFICATION_CONTENT_TEXT", "A meeting has been arranged and you're invited.\r\n The meeting is titled \"###MEETING_NAME###\"\r\n Further details on the lesson's home.");
define("_FUZE_EMAIL_MEETING_NOTIFICATION_CONTENT_HTML", "<HTML><HEAD></HEAD><BODY><p>A meeting has been arranged and you're invited.</p><p>The meeting is titled \"###MEETING_NAME###\"</p><p>Further details on the lesson's home.</p></BODY></HTML>");

// PROFESSOR VIEW ALL PAGE
define("_FUZE_PROF_VIEW_ALL_TITLE", "All your meetings");
define("_FUZE_PROF_VIEW_ALL_TITLE_SUBJECT", "Meeting name");
define("_FUZE_PROF_VIEW_ALL_TITLE_LESSON_NAME", "Lesson name");
define("_FUZE_PROF_VIEW_ALL_TITLE_WHEN", "When");
define("_FUZE_PROF_VIEW_ALL_TITLE_LINK", "Link");
define("_FUZE_PROF_VIEW_ALL_TITLE_TOOLS", "Tools");
define("_FUZE_PROF_VIEW_ALL_CONFIRM_REMOVE", "Are you sure you want to remove this meeting? This operation is final and cannot be reversed.");
define("_FUZE_PROF_VIEW_ALL_REMOVE_SUCCESS", "The chosen meeting is now successfully removed. All calendar entries have been removed and all attendees have been notified.");
define("_FUZE_PROF_VIEW_ALL_REMOVE_FAILURE", "This meeting cannot be removed currently, please try again later.");

// STUDENT STUFF HERE
define("_FUZE_STUDENT_CPANEL_NO_MEETINGS", "There is no meetings planned for this lesson to which you are invited currently.");


// TIME PERIODS STUFF
define("_FUZE_TIME_CPANEL_NOW", "NOW!");
define("_FUZE_TIME_IN_FUTURE", "in");
define("_FUZE_TIME_IN_PAST", "ago");
define("_FUZE_TIME_IN_OVER", "over");
define("_FUZE_TIME_ABOUT", "about");
define("_FUZE_TIME_A_FEW", "a few");
define("_FUZE_TIME_IN_IS", "is");
define("_FUZE_TIME_IN_WAS", "was");
define("_FUZE_TIME_TOMORROW", "tomorrow");
define("_FUZE_TIME_NOW", "right now");
define("_FUZE_TIME_AND", "and");
define("_FUZE_TIME_YEAR", "year");
define("_FUZE_TIME_YEARS", "years");
define("_FUZE_TIME_MONTH", "month");
define("_FUZE_TIME_MONTHS", "months");
define("_FUZE_TIME_WEEK", "week");
define("_FUZE_TIME_WEEKS", "weeks");
define("_FUZE_TIME_DAY", "day");
define("_FUZE_TIME_DAYS", "days");
define("_FUZE_TIME_HOUR", "hour");
define("_FUZE_TIME_HOURS", "hours");
define("_FUZE_TIME_MINUTE", "minute");
define("_FUZE_TIME_MINUTES", "minutes");
define("_FUZE_TIME_NEXT_MEETING", "Next meeting");


// EXCEPTIONS AND TRANSPORT LAYER ERROR MESSAGES
define("_MOD_FUZE_REQUEST_HTTP_OVER_400","Some error occured on the app server [OVER_400]");
define("_MOD_FUZE_REQUEST_ERROR_UNSUPPORTED_PROTOCOL", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_UNSUPPORTED_PROTOCOL]");
define("_MOD_FUZE_REQUEST_ERROR_FAILED_INIT", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_FAILED_INIT]");
define("_MOD_FUZE_REQUEST_ERROR_URL_MALFORMAT", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_URL_MALFORMAT]");
define("_MOD_FUZE_REQUEST_ERROR_URL_MALFORMAT_USER", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_URL_MALFORMAT_USER]");
define("_MOD_FUZE_REQUEST_ERROR_COULDNT_RESOLVE_PROXY", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_COULDNT_RESOLVE_PROXY]");
define("_MOD_FUZE_REQUEST_ERROR_COULDNT_RESOLVE_HOST", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_COULDNT_RESOLVE_HOST]");
define("_MOD_FUZE_REQUEST_ERROR_COULDNT_CONNECT", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_COULDNT_CONNECT]");
define("_MOD_FUZE_REQUEST_ERROR_FTP_WEIRD_SERVER_REPLY", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_FTP_WEIRD_SERVER_REPLY]");
define("_MOD_FUZE_REQUEST_ERROR_REMOTE_ACCESS_DENIED", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_REMOTE_ACCESS_DENIED]");
define("_MOD_FUZE_REQUEST_ERROR_REMOTE_UNKNOWN", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_REMOTE_UNKNOWN]");
define("_MOD_FUZE_REQUEST_ERROR_RESPONSE_MALFORMAT", "Some communication error occured [_MOD_FUZE_REQUEST_ERROR_RESPONSE_MALFORMAT]");
define("_MOD_FUZE_AUTHENTICATION_SIGNATURE_ERROR", "Request authentication error [_MOD_FUZE_AUTHENTICATION_SIGNATURE_ERROR]");
define("_MOD_FUZE_AUTHENTICATION_TIMESTAMP_ERROR", "Request authentication error [_MOD_FUZE_AUTHENTICATION_TIMESTAMP_ERROR]: You need to set your system clock.");

// OTHER ERRORS
define("_FUZE_PROF_CREATE_USER_ERROR", "This account cannot be created currently.");
define("_FUZE_ADMIN_CREATE_USER_ERROR", "This account cannot be created currently.");
define("_FUZE_PROF_SCHEDULE_ERROR", "This meeting cannot be arranged currently.");
define("_FUZE_PROF_HOST_ERROR", "This meeting cannot be arranged currently.");
define("_FUZE_PROF_LAUNCH_ERROR", "This meeting cannot be launched currently.");
define("_FUZE_PROF_ERROR_REMOVE_AUTHORISATION", "You are not permitted to remove this meeting.");
define("_FUZE_PROF_ERROR_REMOVE_HAPPENING_NOW", "You are not permitted to remove a meeting that is due for now.");
define("_FUZE_PROF_MEETING_EDIT_ERROR", "You are not permitted to edit this meeting or this meeting could not be found in the system.");

## EMAIL & CALENDAR NOTIFICATIONS
// PREPARING VARIABLES
$email_site_name = trim($GLOBALS['configuration']['site_name']);
$email_site_motto = trim($GLOBALS['configuration']['site_motto']);
$email_timestamp = date('r', time());
$email_host = G_SERVERNAME; //$GLOBALS['HTTP_ENV_VARS']['HTTP_HOST'];
$email_host_address = $email_host; //'http://' . $email_host . '/www/';
$email_contact_address = $email_host_address . 'index.php?ctg=contact';
$html_wrapper_prefix = '<HTML><HEAD></HEAD><BODY>';
$html_wrapper_suffix = '</BODY></HTML>';

$content = "Conference invitation (###MEETING_NAME###)";
define("_FUZE_EMAIL_MEETING_NOTIFICATION_NEW_SUBJECT",$content);
$content = $html_wrapper_prefix;
$content .= "<p>Dear ###USER_NAME###,</p>";
$content .= "<p>You are invited to a video conference session (details below)</p>";
$content .= "Conference title: ###MEETING_NAME###</br>";
$content .= "Date and time: ###MEETING_STARTTIME###</br>";
$content .= "Lesson: ###LESSON_NAME###</br>";
$content .= "Moderator: ###PROFESSOR_NAME###</br>";
$content .= "<p>This is an automated email sent to you by: {$email_host} @ {$email_timestamp}<br/>";
$content .= "For further infomration you may contact the system administrator through the following URL: {$email_contact_address}</p>";
$content .= "------<br/>The administrator group<br/>{$email_site_name}<br/>{$email_site_motto}";
$content .= $html_wrapper_suffix;
define("_FUZE_EMAIL_MEETING_NOTIFICATION_NEW_CONTENT", $content);

$content = "Conference modified (###MEETING_NAME###)";
define("_FUZE_EMAIL_MEETING_NOTIFICATION_MODIFIED_SUBJECT",$content);
$content = $html_wrapper_prefix;
$content .= "<p>Dear ###USER_NAME###,</p>";
$content .= "<p>This is to let you know that a conference you were previously invited to is now modified (details below)</p>";
$content .= "Conference title: ###MEETING_NAME###</br>";
$content .= "Date and time: ###MEETING_STARTTIME###</br>";
$content .= "Lesson: ###LESSON_NAME###</br>";
$content .= "Moderator: ###PROFESSOR_NAME###</br>";
$content .= "<p>This is an automated email sent to you by: {$email_host} @ {$email_timestamp}<br/>";
$content .= "For further infomration you may contact the system administrator through the following URL: {$email_contact_address}</p>";
$content .= "------<br/>The administrator group<br/>{$email_site_name}<br/>{$email_site_motto}";
$content .= $html_wrapper_suffix;
define("_FUZE_EMAIL_MEETING_NOTIFICATION_MODIFIED_CONTENT", $content);

// FOR STUDENTS THAT ARE NO LONGER INVITED
$content = "Conference modified (###MEETING_NAME###)";
define("_FUZE_EMAIL_MEETING_NOTIFICATION_MODIFIED_NOT_INVITED_SUBJECT",$content);
$content = $html_wrapper_prefix;
$content .= "<p>Dear ###USER_NAME###,</p>";
$content .= "<p>This is to let you know that an invitation you had previously received about a conference is no longer valid (details below)</p>";
$content .= "Conference title: ###MEETING_NAME###</br>";
$content .= "Date and time: ###MEETING_STARTTIME###</br>";
$content .= "Lesson: ###LESSON_NAME###</br>";
$content .= "Moderator: ###PROFESSOR_NAME###</br>";
$content .= "<p>This is an automated email sent to you by: {$email_host} @ {$email_timestamp}<br/>";
$content .= "For further infomration you may contact the system administrator through the following URL: {$email_contact_address}</p>";
$content .= "------<br/>The administrator group<br/>{$email_site_name}<br/>{$email_site_motto}";
$content .= $html_wrapper_suffix;
define("_FUZE_EMAIL_MEETING_NOTIFICATION_MODIFIED_NOT_INVITED_CONTENT", $content);


$content = "Conference cancelation (###MEETING_NAME###)";
define("_FUZE_EMAIL_MEETING_NOTIFICATION_CANCELLED_SUBJECT",$content);
$content = $html_wrapper_prefix;
$content .= "<p>Dear ###USER_NAME###,</p>";
$content .= "<p>This is to let you know that a conference you were previously invited to is now cancelled (details below)</p>";
$content .= "Conference title: ###MEETING_NAME###</br>";
$content .= "Date and time: ###MEETING_STARTTIME###</br>";
$content .= "Lesson: ###LESSON_NAME###</br>";
$content .= "Moderator: ###PROFESSOR_NAME###</br>";
$content .= "<p>This is an automated email sent to you by: {$email_host} @ {$email_timestamp}<br/>";
$content .= "For further infomration you may contact the system administrator through the following URL: {$email_contact_address}</p>";
$content .= "------<br/>The administrator group<br/>{$email_site_name}<br/>{$email_site_motto}";
$content .= $html_wrapper_suffix;
define("_FUZE_EMAIL_MEETING_NOTIFICATION_CANCELLED_CONTENT", $content);

define("_FUZE_CALENDAR_MEETING_NOTIFICATION", "You are invited to a meeting.");

// NAVIGATION LINKS
define("_FUZE_MEETINGS_NAV_TITLE_SCHEDULE", "Schedule meeting");
define("_FUZE_MEETINGS_NAV_TITLE_HOST", "Host a meeting");
define("_FUZE_MEETINGS_NAV_TITLE_EDIT", "Edit a meeting");


?>
