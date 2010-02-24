   {$smarty.capture.t_status_change_interface}
   {/if}
  {eF_template_printBlock title = $smarty.const._PERSONALDATA data = $smarty.capture.t_user_code image = '32x32/profile.png' main_options = $T_TABLE_OPTIONS}
 {else}
        {if $smarty.get.print_preview == 1}
            {eF_template_printBlock alt= $T_USERNAME title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
        {elseif $smarty.get.print == 1}
            {eF_template_printBlock alt= $T_USERNAME title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
            {if $smarty.const.MSIE_BROWSER == 0}
            <script>window.print();</script>
            {/if}
        {else}
   {eF_template_printBlock title = "`$smarty.const._USEROPTIONSFOR`<span class = 'innerTableName'>&nbsp;&quot;`$T_SIMPLEUSERNAME`&quot;</span>" data = $smarty.capture.t_user_code image = '32x32/profile.png' main_options = $T_TABLE_OPTIONS}
  {/if}
 {/if}
{/if}
a>';
        $message_type = failure;
    }
} else {
    $smarty -> assign("T_CART", cart :: prepareCart());
}
if ($GLOBALS['currentTheme'] -> options['sidebar_interface'] == 2 && $GLOBALS['currentTheme'] -> options['show_header'] == 2) {
 if (isset ($_SESSION['s_login'])) {
  try {
   $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
   if (isset($currentUser)) {
    if (unserialize($currentUser -> user['additional_accounts'])) {
     $accounts = unserialize($currentUser -> user['additional_accounts']);
     $queryString = "'".implode("','", array_values($accounts))."'";
     $result = eF_getTableData("users", "login, user_type", "login in (".$queryString.")");
        $smarty -> assign("T_BAR_ADDITIONAL_ACCOUNTS", $result);
    }
   }
  } catch (Exception $e) {
  }
 }
 if (((isset($GLOBALS['currentLesson']) && $GLOBALS['currentLesson'] -> options['online']) && $GLOBALS['currentLesson'] -> options['online'] == 1) || $_SESSION['s_type'] == 'administrator' ){
  $loadScripts[] = 'sidebar';
     //$currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
     $onlineUsers = EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60);
     $size = sizeof($onlineUsers);
     if ($size) {
         $smarty -> assign("T_ONLINE_USERS_COUNT", $size);
     }
     $smarty -> assign("T_ONLINE_USERS_LIST", $onlineUsers);
 }
}
if ($_SESSION['s_login']) { //This way, logged in users that stay on index.php are not logged out
    $loadScripts[] = 'sidebar';
}
$smarty -> assign("T_MESSAGE", $message);
$smarty -> assign("T_MESSAGE_TYPE", $message_type);
$smarty -> assign("T_SEARCH_MESSAGE", $search_message);
if (!$smarty -> is_cached('index.tpl', $cacheId) || !$GLOBALS['configuration']['smarty_caching']) {
 //Main scripts, such as prototype
 $mainScripts = array('scriptaculous/prototype',
       'scriptaculous/scriptaculous',
       'scriptaculous/effects',
                      'EfrontScripts',
       'efront_ajax',
                      'includes/events');
 $smarty -> assign("T_HEADER_MAIN_SCRIPTS", implode(",", $mainScripts));
 //Operation/file specific scripts
 $loadScripts = array_diff($loadScripts, $mainScripts); //Clear out duplicates
 $smarty -> assign("T_HEADER_LOAD_SCRIPTS", implode(",", array_unique($loadScripts))); //array_unique, so it doesn't send duplicate entries
 $smarty -> assign("T_NEWS", news :: getNews(0, true));
 $smarty -> assign("T_ONLINE_USERS_LIST", EfrontUser :: getUsersOnline($GLOBALS['configuration']['autologout_time'] * 60));
 $smarty -> display('index.tpl');
} else {
 $smarty -> display('index.tpl');
}
$benchmark -> set('smarty');
$benchmark -> stop();
if (G_DEBUG) {
 echo $benchmark -> display();
}
?>
