<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
 exit;
}

if ($currentUser->user['login'] == $editedUser->user['login'] && $currentUser -> user['user_type'] != 'administrator') {
 $_change_certificates_ = false;
} else if ($currentUser -> coreAccess['users'] == 'view') {
 $_change_certificates_ = false;
} else {
 $_change_certificates_ = true;
}
$smarty -> assign("_change_certificates_", $_change_certificates_);

$certificates = $editedUser->getIssuedCertificates();
$smarty -> assign("T_USER_TO_CERTIFICATES", $certificates);
