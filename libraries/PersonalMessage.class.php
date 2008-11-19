<?php
/**
* eF_PersonalMessage Class file
*
* @package eFront
* @version 1.0
*/

/**
* eF_PersonalMessage class
*
* This class is used to send personal messages to system users and optionally email them
* @author Venakis Periklis <pvenakis@efront.gr>
* @version 1.0
*/
class eF_PersonalMessage
{
    /**
     * The personal message subject
     *
     * @since 1.0
     * @var string
     * @access private
     */
    private $subject = '';

    /**
     * The personal message body
     *
     * @since 1.0
     * @var string
     * @access private
     */
    private $body = '';

    /**
     * The personal message sender
     *
     * @since 1.0
     * @var string
     * @access private
     */
    private $sender = '';

    /**
     * The personal message recipients
     *
     * @since 1.0
     * @var array
     * @access private
     */
    private $recipients = array();

    /**
     * The personal message attachments
     *
     * @since 1.0
     * @var array
     * @access private
     */
    private $attachments = array();

    /**
     * The users data, such as email, login, message folder ids, etc
     *
     * @since 1.0
     * @var array
     * @access private
     */
    private $userData = array();

    /**
     * The forum configuration variables
     *
     * @since 1.0
     * @var array
     * @access private
     */
    private $config = array();

    /**
     * The class error message
     *
     * @since 1.0
     * @var string
     * @access public
     */
    public $errorMessage = '';

    /**
    * Class constructor
    *
    * This function is used to instantiate class variables to the message attributes:
    * Sender, recipients, subject and body. The $recipients variable may either be a
    * user login, or an array of logins.
    * If either the sender or any of the recipients are not valid system users, the constructor
    * fails.
    * <br/>Example:
    * <code>
    * $pm = new eF_PersonalMessage("professor", array("professor", "student", "admin"), 'Test subject', 'Test personal message body');
    * </code>
    *
    * @param string $sender The personal message sender
    * @param mixed $recipients An array of recipients
    * @param string $subject The personal message subject
    * @param string $body The personal message body
    * @since 1.0
    * @access public
    */
    public function __construct($sender, $recipients, $subject = '', $body = '') {
        $this -> getUsersData();                                                                        //Retrive data for the system users, such as messages folders, emails etc
        $this -> getConfiguration();

        if ($this -> checkRecipient($sender)) {                                                         //Check if the sender is valid
            $this -> sender = $sender;
        } else {
            return false;
        }
        if (!is_array($recipients) && $this -> checkRecipient($recipients)) {                           //If it is a single -valid- login, convert it to array
            $this -> recipients = array($recipients);
        } elseif (is_array($recipients)) {
            foreach ($recipients as $recipient) {                                                       //Check each recipient if it is valid
                if (!$this -> checkRecipient($recipient)) {
                    return false;
                }
            }
            $this -> recipients = $recipients;
        } else {                                                                                        //A single login was given, but it wasn't valid
            return false;
        }

        $this -> subject = $subject ? addslashes($subject) : _NOSUBJECT;                                //If a subject is not specified, give it _NOSUBJECT subject
        $this -> body    = addslashes($body);

    }

    /**
    * Send a personal message
    *
    * This function is used to send the personal message. If $email is specified,
    * the message is also emailed to the recipients
    * <br/>Example:
    * <code>
    * $pm = new eF_PersonalMessage("professor", array("professor", "student", "admin"), 'Test subject', 'Test personal message body');
    * $pm -> send();
    * </code>
    *
    * @param boolean If true, the personal message will be send as an email as well
    * @return true on success, false on error
    * @since 1.0
    * @access public
    */
    public function send($email = false) {
        if (sizeof($this -> recipients) == 0) {
            $this -> errorMessage = _INVALIDRECIPIENT;
            return false;
        }

        $timestamp = time();
       
        if ($email) {									//Check if the messag should be sent as an email also. This will be sent no matter the user quotas
        	$recipientsMail = array();
        	foreach ($this -> recipients as $recipient) { 
				$recipientsMail[] = $this -> userData[$recipient]['email'];
			}
			$recipientsList = implode(",", $recipientsMail);
			if (($result = eF_mail($this -> userData[$this -> sender]['email'], $recipientsList, $this -> subject, $this -> body, $this -> attachments)) !== true) {
                    $this -> errorMessage .= _THEMESSAGEWASNOTSENTASEMAIL.'<br/>';
                }
		}
        
        foreach ($this -> recipients as $recipient) {
            if ($this -> checkUserQuota($recipient)) {
                $fields_insert  = array("users_LOGIN" => $recipient,                                //This message belongs to $recipient
                                        "recipient"   => implode(", ", $this -> recipients),        //It was sent to $recipients
                                        "sender"      => $this -> sender,                           //It was sent by $sender
                                        "timestamp"   => $timestamp,
                                        "title"       => $this -> subject,
                                        "body"        => $this -> body,
                                        "f_folders_ID"=> $this -> userData[$recipient]['folders']['Incoming'],      //Deliver it to the incoming folder
                                        "viewed"      => 0);                                                //It is not viewed yet
                if ($this->attachments[0]) {
                    $attachment = new EfrontFile($this -> sender_attachment_fileId);
                    $recipient_dir = G_UPLOADPATH.$recipient.'/message_attachments/Incoming/'.$timestamp.'/';
                    mkdir($recipient_dir,0755);
                    $newFile    = $attachment -> copy($recipient_dir, false, true);
                    $fields_insert["attachments"] = $newFile['id'];
                }

                $id = eF_insertTableData("f_personal_messages", $fields_insert);
                EfrontSearch :: insertText($fields_insert['body'], $id, "f_personal_messages", "data");
                EfrontSearch :: insertText($fields_insert['title'], $id, "f_personal_messages", "title");
            } else {
                $this -> errorMessage .= _YOURMESSAGETO.' '.$recipient.' '._COULDNOTBEDELIVERED.' '._BECAUSEHISMESSAGEBOXISFULL.'<br/>';
            }
        }

        if ($this -> checkUserQuota($this -> sender)) {

            $fields_insert  = array("users_LOGIN" => $this -> sender,                                   //Create the message for the sender, and put it in his Sent messages folder
                                    "recipient"   => implode(", ", $this -> recipients),
                                    "sender"      => $this -> sender,
                                    "timestamp"   => $timestamp,
                                    "title"       => $this -> subject,
                                    "body"        => $this -> body,
                                    "f_folders_ID"=> $this -> userData[$this -> sender]['folders']['Sent'],
                                    "viewed"      => 0);
            if ($this->attachments[0]) {
                $attachment = new EfrontFile($this -> sender_attachment_fileId);
                $fields_insert["attachments"] = $this -> sender_attachment_fileId;
            }

            $id = eF_insertTableData("f_personal_messages", $fields_insert);
            EfrontSearch :: insertText($fields_insert['body'], $id, "f_personal_messages", "data");
            EfrontSearch :: insertText($fields_insert['title'], $id, "f_personal_messages", "title");
        } else {
            $this -> errorMessage .= _COULDNOTBECOPIEDTOYOURSENTBOX.' '._BECAUSEYOURMESSAGEBOXISFULL.'<br />';
        }

        if ($this -> errorMessage) {
            return false;
        } else {
            return true;
        }
    }

    /**
    *
    */
    public function setAttachment($filename) {
        $this -> attachments[] = $filename;
    }

    /**
    * Check if the Recipient is valid
    *
    * This function is used to check the validity of a personal message
    * recipient (or sender). it first checks if the login is well formed,
    * and then whether the user actually exists.
    *
    * @param string $recipient The login to check validity for
    * @return boolean true if it is a valid user, false otherwise
    * @since 1.0
    * @access private
    */
    private function checkRecipient($recipient) {
        if (!eF_checkParameter($recipient, 'login')) {                                          //Is it a well-formed login
            return false;
        } else {
            if (!in_array($recipient, array_keys($this -> userData))) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
    * Get users data
    *
    * This function retrieves and builds an array with user information that is used
    * throughout the class. This information is the users logins, emails and the
    * message folders ids
    *
    * @since 1.0
    * @access private
    */
    private function getUsersData() {
        $result_folders  = eF_getTableData("f_folders", "*");                                                //Get all user message folders
        $result_users    = eF_getTableData("users", "login, email, user_type");                              //Get all user user information
        $result_messages = eF_getTableDataFlat("f_personal_messages", "users_LOGIN");
        $messages        = array_count_values($result_messages['users_LOGIN']);                              //Count the number of messages for each user. Nice alternative to looping queries

        foreach ($result_users as $user) {
            $this -> userData[$user['login']] = $user;
            $this -> userData[$user['login']]['messages'] = $messages[$user['login']];
        }

        foreach ($result_folders as $folder) {
            $this -> userData[$folder['users_LOGIN']]['folders'][$folder['name']] = $folder['id'];
        }
    }

    /**
    * Get configuration values
    *
    * This function is used to read forum configuration values
    * and assign them to the $config array, in name/value pairs
    *
    * @since 1.0
    * @access private
    */
    private function getConfiguration() {
        $result = eF_getTableDataFlat("f_configuration", "*");
        sizeof($result) > 0 ? $this -> config = array_combine($result['name'], $result['value']) : $this -> config = array();
    }


    /**
    * Check a user's message quota
    *
    * This function returns true if a user doesn't exceed his messages
    * quotas (which apply only to students)
    *
    * @param string $login The user to check quotas for
    * @param boolean $check_attachment Whether to check for attachment quota as well
    * @return boolean True if quotas are not exceeded
    * @since 1.0
    * @access private
    */
    private function checkUserQuota($login, $check_attachment = false) {
        if ($check_attachment) {
            $total_files = eF_diveIntoDir(G_UPLOADPATH.$login.'/message_attachments/');
            if ($this -> config['pm_attach_quota'] && $total_files[2] > $this -> config['pm_attach_quota'] * 1024) {
                return false;
            }
        }

        if ($this -> userData[$login]['user_type'] != 'student') {
            return true;
        } elseif ($this -> config['pm_quota'] && $this -> userData[$login]['messages'] > $this -> config['pm_quota']) {
            return false;
        } else {
            return true;
        }

    }


}

?>