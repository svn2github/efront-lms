<?php

unserialize($editedUser -> user['additional_accounts']) ? $additionalAccounts = unserialize($editedUser -> user['additional_accounts']) : $additionalAccounts = array();
$smarty -> assign("T_ADDITIONAL_ACCOUNTS", $additionalAccounts);







if (isset($_GET['ajax']) && $_GET['ajax'] == 'additional_accounts') {
 try {
  if (isset($_GET['fb_login'])) {



  } else {
   if (isset($_GET['delete'])) {
    unset($additionalAccounts[array_search($_GET['login'], $additionalAccounts)]);
   } else {
    if ($_GET['login'] == $_SESSION['s_login']){
     throw new Exception(_CANNOTMAPSAMEACCOUNT);
    }

    if (in_array($_GET['login'], $additionalAccounts)) {
     throw new Exception(_ADDITIONALACCOUNTALREADYEXISTS);
    }
    $newAccount = EfrontUserFactory::factory($_GET['login'], EfrontUser::createPassword($_GET['pwd']));
    $additionalAccounts[] = $newAccount -> user['login'];

    unserialize($newAccount -> user['additional_accounts']) ? $additionalAccounts2 = unserialize($newAccount -> user['additional_accounts']) : $additionalAccounts2 = array();
    $additionalAccounts2[] = $editedUser -> user['login'];
    $newAccount -> user['additional_accounts'] = serialize(array_unique($additionalAccounts2));
    $newAccount -> persist();
   }
   $editedUser -> user['additional_accounts'] = serialize(array_unique($additionalAccounts));
   $editedUser -> persist();
  }
 } catch (Exception $e) {
  handleAjaxExceptions($e);
 }
 exit;
}
