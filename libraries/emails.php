<?php
/**
* @package eFront
*/

/**
* Send an email
*
* This function is a custom wrapper function for PEAR::Mail class.
* <br>Example:
* <code>
* eF_mail('admin@efront.gr', 'Test email', 'Hello world!');
* </code>
* @param string $sender The email sender
* @param string $recipient The email recipient. In case of multiple recipients, these are specified with a comma separated list
* @param string $subject The email subject.
* @param string $content The email content.
* @return mixed It propagates the PEAR Mail result, which is true on success or PEAR_ERROR instance on failure
* @version 4.0
* Changes from version 3.0 to version 4.0:
* - Rewritten in order to use $GLOBALS['configuration'],
* - Fixed buggy behaviour
* - Fixed return results
*/

function eF_mail($sender, $recipient, $subject, $body, $attachments = false, $onlyText = false) {
    $hdrs = array('From'    => $sender,
                  'Subject' => $subject,
                  'To'      => $recipient);

    $params = array("text_charset" => "UTF-8",
                    "html_charset" => "UTF-8",
                    "head_charset" => "UTF-8");

    $mime = new Mail_mime("\n");
    $mime -> setTXTBody($body);
    if (!$onlyText) {
        $mime -> setHTMLBody($body);
    }
    if ($attachments) {
        $file = new EfrontFile($attachments[0]);
        $mime -> addAttachment($file['path'], $file['mime_type'], $file['physical_name']);
    }

    $body = $mime -> get($params);
    $hdrs = $mime -> headers($hdrs);

    $smtp =& Mail::factory('smtp', array('auth'      => $GLOBALS['configuration']['smtp_auth'] ? true : false,
                                         'host'      => $GLOBALS['configuration']['smtp_host'],
                                         'password'  => $GLOBALS['configuration']['smtp_pass'],
                                         'port'      => $GLOBALS['configuration']['smtp_port'],
                                         'username'  => $GLOBALS['configuration']['smtp_user'],
                                         'timeout'   => $GLOBALS['configuration']['smtp_timeout']));

    $result = $smtp -> send($recipient, $hdrs, $body);

    return $result;
}

/**
* email a new announcement to students.
*
* This functon is used to send a new announcement as email to students. It accepts the announcement id as an argument.
* <br />Example:
* <code>
* $news_id = 4;
* eF_emailNews($news_id);
* </code>
* @param int $news_id The announcement id.
* @return bool true if the mail was sent
* @see eF_mail()
* @version 3.0
*/
function eF_emailNews($news_id)
{
    $res1 = eF_getTableData("news", "title,data,lessons_ID", "id=$news_id");
    $res2 = eF_getTableData("lessons", "name", "id=".$res1[0]['lessons_ID']);
    $res3 = eF_getTableData("users,users_to_lessons", "email", "users.active=1 AND users_to_lessons.active=1 AND users.user_type='student' AND users.login=users_to_lessons.users_LOGIN AND users_to_lessons.lessons_ID=".$res1[0]['lessons_ID']);
    for ($i = 0; $i < sizeof($res3); $i++) {
        $emails[] = $res3[$i]['email'];
    }

    if (sizeof($emails) > 0) {
        $title   = $res1[0]['title'];
        $data    = nl2br($res1[0]['data']);
        $lesson  = $res2[0]['name'];
        $to      = implode(", ", $emails);
        $subject = _LESSONANNOUNCEMENT." ".$lesson;

        $content = _EMAILSENTFROM.' '.G_SERVERNAME.' '._TO.' '._STUDENTSOFLESSONSMALL.' '.$res2[0]['name'].
                   ' '._ON.' '.formatTimestamp(time()).''."\n".$data;

        if (eF_mail($GLOBALS['configuration']['system_email'], $to, $subject, $content, false, true) === true) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
* Send reset password confirmation email
*
* This function is used to send an email to a user, which will be used to verify this email address, in order to
* be sent to it a new password later.
*
* @param string $email The recipient email
* @param string $login The user login
* @param string $name the user name
* @return bool true if everything went ok.
* @vesion 1.0
*/
function eF_emailPasswordConfirmation($email, $login, $name)
{
    $subject = _PASSWORDRECOVERY;

    $content = _DEARUSER." ".$name.",\r\n\r\n".
               _THISISANAUTOMATEDEMAILSENTFROM." ".G_SERVERNAME." "._BECAUSEYOUASKEDTORECOVERPASSWORD." "._PLEASECLICKTHECONFIRMATIONLINKBELOW."\r\n\r\n"
               .G_SERVERNAME.'index.php?ctg=reset_pwd&login='.$login.'&id='.md5($login.G_MD5KEY)."\r\n\r\n"
               ._ALTERNATIVELYCOPYANDPASTEBROWSER.".\r\n"._CLIKCINGONTHELINKWILLCONFIRM." \r\n"._FORFURTHERCONTACTADMINAT." ".$GLOBALS['configuration']['system_email']."\r\n\r\n"._KINDREGARDSEFRONT."\r\n\r\n"
               ._AUTOMATEDEMAILSENTFROM." ".G_SERVERNAME." "._ON." ".formatTimestamp(time())."\r\n\r\n";

//echo "<pre>".$content."</pre>";
    if (eF_mail($GLOBALS['configuration']['system_email'], $email, $subject, $content, false, true) === true) {
        return true;
    } else {
        return false;
    }

}

/**
* Send new password
*
* This function is used to send to a user a new password.
*
* @param string $email the recipient email
* @param string $name the user name
* @param string $passsword The new password
* @return bool true if the mail was sent.
* @version 1.0
*/
function eF_emailNewPassword($email, $name, $password)
{
    $subject = _PASSWORDRECOVERY;

    $content = "** "._DEARUSER." ".$name.",\r\n\r\n"._THISISANAUTOMATEDEMAILSENTFROM." ".G_SERVERNAME." "._WITHTHENEWPASSWORD." \r\n"._THENEWPASSWORDIS."\r\n\r\n".$password."\r\n
               \r\n"._FORFURTHERCONTACTADMINAT." "."\r\n\r\n"._KINDREGARDSEFRONT."\r\n\r\n"
               ._AUTOMATEDEMAILSENTFROM." ".G_SERVERNAME." "._ON." ".formatTimestamp(time());

//echo "<pre>".$content."</pre>";
    if (eF_mail($GLOBALS['configuration']['system_email'], $email, $subject, $content, false, true) === true) {
        return true;
    } else {
        return false;
    }

}

/**
* Send an email when a new user is registered
*
* This function constructs an email containing the information with which a new user registered to the system.
*
* @param string $email the user email
* @param array $personal_data The new user personal information
* @param array $lessons_selection The lessons that the new user selected
* @param bool $automatic_activation Whether the system is set to automatically activate new users
* @return bool true if the mail was sent.
* @version 1.0
* v3  From now on there is no lessons selection during sign up ....18/6/2007
*/
function eF_mailRegister($email, $personal_data, $lessons_selection, $automatic_activation)
{
    $subject = _REGISTRATIONEMAIL;

   /* for ($i = 0; $i < sizeof($lessons_selection); $i++) {  // no lessons selection
        $lessons_str .= $lessons_selection[$i]['name'].' ('.$lessons_selection[$i]['price'].' '._CURRENCYSYMBOL.')<br/>';
    }*/

    $content = '** '._DEARUSER.' '.$personal_data['name'].",\r\n\r\n"
               ._WELCOMETO.' '._ELEARNINGPLATFORM.' '._EFRONT."\r\n"._ACCOUNTACTIVATEDWITHPERSONALINFORMATION."\r\n\r\n".
               _LOGIN   .': '.$personal_data['login']         ."\r\n".
               _NAME    .': '.$personal_data['name']          ."\r\n".
               _SURNAME .': '.$personal_data['surname']		  ."\r\n".
               _ADDRESS .': '.$personal_data['address']       ."\r\n".
               _POSTCODE.': '.$personal_data['post_code']     ."\r\n".
               _CITY    .': '.$personal_data['city']          ."\r\n".
               _HOMEPHONE   .': '.$personal_data['phone']     ."\r\n".
               _EMAILADDRESS.': '.$personal_data['email']     ."\r\n".
               _LANGUAGE.': '.$personal_data['languages_NAME']."\r\n".
               _COMMENTS.': '.$personal_data['comments']      ."\r\n\r\n";
            /*   "\r\n"._YOUHAVEAPPLIEDFORTHELESSONS."\r\n\r\n".
               $lessons_str.
               "\r\n\r\n"._YOUMAYSTARTUSINGFREEIMMEDIATELYBUTNOTNONFREE."\r\n\r\n"._YOUMAYALTERPERSONALINFORMATIONFROMSETTINGS."\r\n\r\n"*/
    $content .= _AUTOMATEDEMAILSENTFROM.' '.G_SERVERNAME.' '._ON.' '.formatTimestamp(time())."\r\n";

    $automatic_activation ? $content .= _YOUMAYLOGINIMMEDIATELY : $content .= _YOUMAYLOGINWHENADMINACTIVATESYOU;

    $content .= "\r\n\r\n"._FORFURTHERCONTACTADMINAT.' '.$GLOBALS['configuration']['system_email']."\r\n\r\n"._KINDREGARDSEFRONT."\r\n\r\n";


    if (eF_mail($GLOBALS['configuration']['system_email'], $email, $subject, $content, false, true)) {
        return true;
    } else {
        return false;
    }
}



?>