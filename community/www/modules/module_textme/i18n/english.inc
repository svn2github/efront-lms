<?php

define('_TEXTME', 'TextMe');

/* admin_lessons.tpl && admin_lessons.tpl */
define('_TEXTME_LESSONSACCOUNTSTAB', 'Lessons accounts');
define('_TEXTME_SENDERID', 'Sender ID');
define('_TEXTME_CREDITS', 'Credits');
define('_TEXTME_CREDITSREMAINING', 'Credits remaining');
define('_TEXTME_CREDITSSPENT', 'Credits spent');
define('_TEXTME_UNLIMITEDCREDITS', 'unlimited');
define('_TEXTME_MODULEDISABLED', 'module disabled');
define('_TEXTME_EDITLESSONACCOUNT', 'Edit lesson account');
define('_TEXTME_NOLESSONSACCOUNTS', 'No lessons');
define('_TEXTME_BACKTOLESSONSACCOUNTS', 'Back to Lessons accounts');
define('_TEXTME_LEAVEBLANKFORUNLIMITEDCREDITS', 'Leave blank for unlimited credits');
define('_TEXTME_CREDITSFIELDBLANKORNUMBER', 'Field "Credits" should be left blank or have a numeric value');
define('_TEXTME_LESSONACCOUNUPDATED', 'Lesson account updated');

/* admin_providers.tpl && admin_providers.tpl */
define('_TEXTME_SMSGATEWAYSTAB', 'Sms gateways');
define('_TEXTME_SMSGATEWAYURL', 'Gateway');
define('_TEXTME_PARAMETERS', 'Parameters');
define('_TEXTME_ADDSMSGATEWAY', 'Add sms gateway');
define('_TEXTME_EDITSMSGATEWAY', 'Edit sms gateway');
define('_TEXTME_DELETESMSGATEWAY', 'Delete sms gateway');
define('_TEXTME_TESTSMSGATEWAY', 'Test sms gateway');
define('_TEXTME_NOSMSGATEWAYS', 'No sms gateways');
define('_TEXTME_BACKTOSMSGATEWAYS', 'Back to Sms gateways');
define('_TEXTME_PARAMETERSEXPLANATION', 'Parameters format example:<br/>username:efront<br/>password:efront<br/>mobile:306900123456789');
define('_TEXTME_SMSGATEWAYADDED', 'Sms gateway added');
define('_TEXTME_SMSGATEWAYUPDATED', 'Sms gateway updated');
define('_TEXTME_SMSGATEWAYDELETED', 'Sms gateway deleted');
define('_TEXTME_TESTMESSAGESENT', 'A test message was sent to %s');
define('_TEXTME_NOSMSGATEWAYS', 'No sms gateways');
define('_TEXTME_NOMOBILEPARAMETERDEFINED', 'Message could not be sent. You need to define a mobile parameter for this gateway.');
define('_TEXTME_NOTIFICATIONSONLYLOCALLY', 'You have not set a default Sms gateway yet. TextMe alerts are not being routed to any mobile networks.');
define('_TEXTME_THEFOLLOWINGPARAMETERSAREMANDATORYFORGATEWAY', 'The following parameters are mandatory for this gateway: %s');

/* cpanel_widget.tpl */
define('_TEXTME_SMSNOTIFICATIONSENABLEDANDROUTED', 'Sms alerts are being routed using <strong style="vertical-align: top;">%s\'s</strong> services. (account: %s)');
define('_TEXTME_SMSNOTIFICATIONNOTSENABLEDANDROUTED', 'You have not set a default Sms gateway yet.<br/>TextMe alerts are not being routed to any mobile networks.<br/>Click <a href="%s" >here</a> to resolve this problem.');
define('_TEXTME_LESSONSTHATHAVEENABLEDTEXTME', '%s out of %s lessons have <strong style="vertical-align: top;">enabled</strong> TextMe\'s sms alerts.');
define('_TEXTME_USERSTHATHAVEENABLEDTEXTME', '%s users have <strong style="vertical-align: top;">subscribed</strong> to TextMe\'s sms alerts.');

/* GENERAL */
define('_TEXTME_ANERROROCCURED', 'An error occured');

/* inbox.tpl && inbox.php */

define('_TEXTME_INBOX', 'Inbox');
define('_TEXTME_COMPOSEMESSAGE', 'Compose Message');
define('_TEXTME_MESSAGE', 'Message');
define('_TEXTME_DELETEMESSAGE', 'Delete message');
define('_TEXTME_UNDOSEND', 'Undo send');
define('_TEXTME_ACCOUNTBALANCE', 'You have %s credits left.');
define('_TEXTME_RECIPIENTS', 'Recipients');
define('_TEXTME_EVERYONE', 'Everyone');
define('_TEXTME_PROFESSORSONLY', 'Professors only');
define('_TEXTME_STUDENTSONLY', 'Students only');
define('_TEXTME_SELECT', 'Select...');
define('_TEXTME_NOW', 'Now');
define('_TEXTME_SELECTDATE', 'Select date...');
define('_TEXTME_USERS', 'Users');
define('_TEXTME_GROUPS', 'Groups');
define('_TEXTME_BACKTOINBOX', 'Back to Inbox');
define('_TEXTME_RECIPIENT', 'Recipient');
define('_TEXTME_VIEWMESSAGE', 'View Message');
define('_TEXTME_SMSSTATUS', 'Sms status');
define('_TEXTME_LOCALSTATUS', 'Local status');
define('_TEXTME_NOTROUTED', 'Not routed');
define('_TEXTME_MESSAGEDELIVERED', 'Message delivered');
define('_TEXTME_MESSAGEPENDING', 'Message delivery pending');
define('_TEXTME_MESSAGEFAILED', 'Message delivery failed');
define('_TEXTME_MESSAGEUNREAD', 'Message read');
define('_TEXTME_MESSAGEREAD', 'Message not read yet');
define('_TEXTME_MESSAGEDELETED', 'Message deleted');
define('_TEXTME_MESSAGESENT', 'Message sent');
define('_TEXTME_MESSAGESENTPROBLEM', 'An error occured.Message may only be available locally for some recipients.');
define('_TEXTME_NOMESSAGES', 'No messages');
define('_TEXTME_NORECIPIENTS', 'No recipients');
define('_TEXTME_INVALIDDATE', 'Invalid date or date in past');

/* professor_users.tpl && professor_users.php */

define('_TEXTME_USERSANDGROUPS', 'Users and Groups');
define('_TEXTME_USER', 'User');
define('_TEXTME_GROUP', 'Group');
define('_TEXTME_ADDGROUP', 'Add group');
define('_TEXTME_EDITGROUP', 'Edit group');
define('_TEXTME_DELETEGROUP', 'Delete group');
define('_TEXTME_BACKTOUSERSANDGROUPS', 'Back to users and groups');
define('_TEXTME_ALERTSSTATUS', 'TextMe alerts status');
define('_TEXTME_ROLE', 'Role');
define('_TEXTME_HASENABLEDALERTS', 'User has enabled alerts for this lesson');
define('_TEXTME_HASNOTENABLEDALERTS', 'User has not enabled alerts for this lesson yet');
define('_TEXTME_NOUSERS', 'No users');
define('_TEXTME_NOGROUPS', 'No groups');
define('_TEXTME_NUMBEROFUSERS', 'Number of users');
define('_TEXTME_INGROUPUSERS', 'In group users');
define('_TEXTME_OUTOFGROUPUSERS', 'Out of group users');
define('_TEXTME_GROUPDELETED', 'Group deleted');
define('_TEXTME_GROUPUPDATED', 'Group updated');
define('_TEXTME_GROUPADDED', 'Group added');

/* professor_configuration.tpl && professor_configuration.php */
define('_TEXTME_CONFIGURATION', 'Configuration');
define('_TEXTME_SENDERIDUPDATED', 'Sender ID updated');
define('_TEXTME_SENDERIDINVALIDCHARACTERS', 'The field "Sender ID" can only contain capital latin letters and digits.<br/>First character must be a letter.');

/* subscribe.tpl && subscribe.php */

define('_TEXTME_SUBSCRIBE', 'Subscribe');
define('_TEXTME_UNSUBSCRIBE', 'Unsubscribe');
define('_TEXTME_CHANGEMOBILE', 'Change mobile');
define('_TEXTME_REQUESTVERIFICATIONCODE', 'Request verification code');
define('_TEXTME_MOBILE', 'Mobile');
define('_TEXTME_BACK', 'Back');
define('_TEXTME_NOLESSONS', 'No lessons');
define('_TEXTME_VERIFICATIONCODEWASSENT', 'A Verification code was sent to %s');
define('_TEXTME_VERIFICATIONCODEWASNOTSENT', 'Verification code could not be sent at the moment. Please try later.');
define('_TEXTME_YOUCANSUBSCRIBETOTHEFOLLOWINGLESSONS', 'You can subscribe to the following lessons for sms alerts');
define('_TEXTME_VERIFICATIONCODE', 'Verification code');
define('_TEXTME_INVALIDVERIFICATIONCODE', 'Invalid verification code');
define('_TEXTME_WHATTEXTMEIS', 'Textme is an sms alerts system.<br/>It keeps you informed on important updates of the lessons you are attending.<br/>');
define('_TEXTME_THEFOLLOWINGLESSONSPROVIDEALERTS', 'The following lessons provide sms alerts to their users.');
define('_TEXTME_YOUHAVENOTYETSUBSCRIBED', 'You have not yet subscribed to Textme\'s sms alerts.<br/>Please provide us with your mobile phone number.');
define('_TEXTME_MOBILEDESCRIPTION', 'You must prefix your mobile number with the international calling code of your country.<br/>eg. for Greece if your mobile is 1234567890 then enter 301234567890.');
define('_TEXTME_YOURMOBILEIS', 'Your mobile number is <strong>%s</strong>');
define('_TEXTME_VERIFICATIONCODEDESCRIPTION', 'In a few moments you will receive an sms with a <strong>verification code.</strong><br/>Please enter the verification code to the form below.<br/>This is required in order to verify that you are the owner of the provided mobile number.');
define('_TEXTME_AREYOUSUREYOUWANTTOUNSUBSCRIBE', 'Are you sure you want to unsubscribe from TextMe alerts?');
define('_TEXTME_AREYOUSUREYOUWANTTOCHANGEMOBILE', 'Are you sure you want to change your mobile?');
define('_TEXTME_RESENDCODE', 'Are you sure you want us to resend you your verification code?');

define('_TEXTME_GOTOPAGE', 'Go to TextMe');


/* lesson_widget.tpl && lesson_widget.php */
define('_TEXTME_WARNING', 'Warning');
define('_TEXTME_YOUCANNOTRECEIVEALERTS', 'You cannot receive sms alert.');
define('_TEXTME_YOUHAVENOTYETSUBSCRIBEDTOTEXTME', 'You need to provide a mobile number.');
define('_TEXTME_YOUHAVENOTYETVERIFIEDYOURMOBILE', 'You need to verify your mobile number.');
define('_TEXTME_YOUDONTHAVEANYMESSAGES', 'You do not have unread messages.');
define('_TEXTME_YOUHAVEMESSAGES', 'You have <a href="%s%s">%s unread</a> messages.');

?>
