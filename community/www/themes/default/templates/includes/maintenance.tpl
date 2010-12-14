{*moduleCleanup: Clean up old data*}
{capture name = "moduleCleanup"}
 <tr><td class = "moduleCell">

 {capture name = 't_cleanup_code'}
     <table>
         <tr><td class = "labelCell">{$smarty.const._ORPHANUSERFOLDERSCHECK}:&nbsp;</td>
             <td class = "elementCell">
             {if $T_ORPHAN_USER_FOLDERS}
                 <img src = "images/16x16/warning.png" title = "{$smarty.const._PROBLEM}" alt = "{$smarty.const._PROBLEM}"/>&nbsp;
                 <img src = "images/16x16/help.png" title = "{$smarty.const._INFO}" alt = "{$smarty.const._INFO}" onclick = "eF_js_showDivPopup('{$smarty.const._ORPHANUSERFOLDERSCHECK}', 0, 'orphan_user_folders')"/>&nbsp;
                 <img src = "images/16x16/error_delete.png" title = "{$smarty.const._CLEANUP}" alt = "{$smarty.const._CLEANUP}" onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGFOLDERS}:\n\n{$T_ORPHAN_USER_FOLDERS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&cleanup=orphan_user_folders'"/>
             {else}
                 <img src = "images/16x16/success.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
             {/if}
             </td></tr>
         <tr><td class = "labelCell">{$smarty.const._USERSWITHOUTFOLDERSCHECK}:&nbsp;</td>
             <td class = "elementCell">
             {if $T_ORPHAN_USERS}
                 <img src = "images/16x16/warning.png" title = "{$smarty.const._PROBLEM}" alt = "{$smarty.const._PROBLEM}"/>&nbsp;
                 <img src = "images/16x16/help.png" title = "{$smarty.const._INFO}" alt = "{$smarty.const._INFO}" onclick = "eF_js_showDivPopup('{$smarty.const._USERSWITHOUTFOLDERSCHECK}', 0, 'users_without_folders')"/>&nbsp;
                 <img src = "images/16x16/error_delete.png" title = "{$smarty.const._CLEANUP}" alt = "{$smarty.const._CLEANUP}" onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGUSERS}:\n\n{$T_ORPHAN_USERS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&cleanup=users_without_folders'"/>&nbsp;
                 <img src = "images/16x16/folders.png" title = "{$smarty.const._CREATEFOLDER}" alt = "{$smarty.const._CREATEFOLDER}" onclick = "if (confirm('{$smarty.const._CREATEFOLLOWINGUSERFOLDERS}:\n\n{$T_ORPHAN_USERS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&create=user_folders'"/>
             {else}
                 <img src = "images/16x16/success.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
             {/if}
             </td></tr>
         <tr><td class = "labelCell">{$smarty.const._ORPHANLESSONFOLDERSCHECK}:&nbsp;</td>
             <td class = "elementCell">
             {if $T_ORPHAN_LESSON_FOLDERS}
                 <img src = "images/16x16/warning.png" title = "{$smarty.const._PROBLEM}" alt = "{$smarty.const._PROBLEM}"/>&nbsp;
                 <img src = "images/16x16/help.png" title = "{$smarty.const._INFO}" alt = "{$smarty.const._INFO}" onclick = "eF_js_showDivPopup('{$smarty.const._ORPHANLESSONFOLDERSCHECK}', 0, 'orphan_lesson_folders')"/>&nbsp;
                 <img src = "images/16x16/error_delete.png" title = "{$smarty.const._CLEANUP}" alt = "{$smarty.const._CLEANUP}" onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGFOLDERS}:{$T_ORPHAN_LESSON_FOLDERS|@eF_truncate:30}{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&cleanup=orphan_lesson_folders'"/>
             {else}
                 <img src = "images/16x16/success.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
             {/if}
         </td></tr>
         <tr><td class = "labelCell">{$smarty.const._LESSONSWITHOUTFOLDERSCHECK}:&nbsp;</td>
             <td class = "elementCell">
             {if $T_ORPHAN_LESSONS}
                 <img src = "images/16x16/warning.png" title = "{$smarty.const._PROBLEM}" alt = "{$smarty.const._PROBLEM}"/>&nbsp;
                 <img src = "images/16x16/help.png" title = "{$smarty.const._INFO}" alt = "{$smarty.const._INFO}" onclick = "eF_js_showDivPopup('{$smarty.const._LESSONSWITHOUTFOLDERSCHECK}', 0, 'lessons_without_folders')"/>&nbsp;
                 <img src = "images/16x16/error_delete.png" title = "{$smarty.const._CLEANUP}" alt = "{$smarty.const._CLEANUP}" onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGLESSONS}:\n\n{$T_ORPHAN_LESSONS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&cleanup=lessons_without_folders'"/>&nbsp;
                 <img src = "images/16x16/folders.png" title = "{$smarty.const._CREATEFOLDER}" alt = "{$smarty.const._CREATEFOLDER}" onclick = "if (confirm('{$smarty.const._CREATEFOLLOWINGLESSONFOLDERS}:\n\n{$T_ORPHAN_LESSONS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup&create=lesson_folders'"/>
             {else}
                 <img src = "images/16x16/success.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
             {/if}
         </td></tr>
         <tr><td></td>
          <td class = "submitCell"><input class = "flatButton" type = "button" value = "{$smarty.const._CHECKAGAIN}" onclick = "location = '{$smarty.server.PHP_SELF}?ctg=maintenance&tab=cleanup'"></td></tr>
     </table>
     <div id = "orphan_user_folders" style = "display:none;">
     {capture name = 't_orphan_user_folders_code'}
         {$T_ORPHAN_USER_FOLDERS}
     {/capture}
     {eF_template_printBlock title=$smarty.const._FOLDERSWITHOUTAUSERASSOCIATED data=$smarty.capture.t_orphan_user_folders_code image='32x32/cleanup.png'}
     </div>
     <div id = "users_without_folders" style = "display:none;">
     {capture name = 't_orphan_users_code'}
         {$T_ORPHAN_USERS}
     {/capture}
     {eF_template_printBlock title=$smarty.const._USERSWITHOUTAFOLDER data=$smarty.capture.t_orphan_users_code image='32x32/cleanup.png'}
     </div>
     <div id = "orphan_lesson_folders" style = "display:none;">
     {capture name = 't_orphan_lesson_folders_code'}
         {$T_ORPHAN_LESSON_FOLDERS}
     {/capture}
     {eF_template_printBlock title=$smarty.const._FOLDERSWITHOUTALESSONASSOCIATED data=$smarty.capture.t_orphan_lesson_folders_code image='32x32/cleanup.png'}
     </div>
     <div id = "lessons_without_folders" style = "display:none;">
     {capture name = 't_lessons_without_folders_code'}
         {$T_ORPHAN_LESSONS}
     {/capture}
     {eF_template_printBlock title=$smarty.const._LESSONSWITHOUTAFOLDER data=$smarty.capture.t_lessons_without_folders_code image='32x32/cleanup.png'}
     </div>

  {$T_CLEANUP_FORM.javascript}
  <form {$T_CLEANUP_FORM.attributes}>
   {$T_CLEANUP_FORM.hidden}
   <fieldset class = "fieldsetSeparator">
    <legend>{$smarty.const._PURGELOGS}</legend>
   <table>
    <tr><td class="labelCell">{$smarty.const._LOGSSIZE}:&nbsp;</td>
     <td class="elementCell">{$T_LOG_SIZE} {$smarty.const._ENTRIES}</td></tr>
    <tr><td class="labelCell">{$smarty.const._OLDESTLOG}:&nbsp;</td>
     <td class="elementCell">#filter:timestamp-{$T_LAST_LOG_ENTRY}#</td></tr>
          <tr><td class = "labelCell">{$smarty.const._PURGELOGSOLDERTHAN}:&nbsp;</td>
              <td class = "elementCell">{eF_template_html_select_date prefix="purge_" time=$T_LAST_LOG_ENTRY start_year="-1" end_year="+5" field_order = $T_DATE_FORMATGENERAL}</td></tr>
    <tr><td></td>
     <td class = "submitCell">{$T_CLEANUP_FORM.submit.html}</td></tr>
   </table>
   </fieldset>
  </form>

  {$T_CLEANUP_NOTIFICATIONS_FORM.javascript}
  <form {$T_CLEANUP_NOTIFICATIONS_FORM.attributes}>
   {$T_CLEANUP_NOTIFICATIONS_FORM.hidden}
   <fieldset class = "fieldsetSeparator">
    <legend>{$smarty.const._PURGENOTIFICATIONS}</legend>
   <table>
    <tr><td class="labelCell">{$smarty.const._NOTIFICATIONSSIZE}:&nbsp;</td>
     <td class="elementCell">{$T_NOTIFICATIONS_SIZE} {$smarty.const._ENTRIES}</td></tr>
    <tr><td class="labelCell">{$smarty.const._OLDESTNOTIFICATION}:&nbsp;</td>
     <td class="elementCell">#filter:timestamp-{$T_LAST_NOTIFICATIONS_ENTRY}#</td></tr>
          <tr><td class = "labelCell">{$smarty.const._PURGENOTIFICATIONSOLDERTHAN}:&nbsp;</td>
              <td class = "elementCell">{eF_template_html_select_date prefix="purge_" time=$T_LAST_NOTIFICATIONS_ENTRY start_year="-1" end_year="+5" field_order = $T_DATE_FORMATGENERAL}</td></tr>
    <tr><td></td>
     <td class = "submitCell">{$T_CLEANUP_NOTIFICATIONS_FORM.submit.html}</td></tr>
   </table>
   </fieldset>
  </form>

  {$T_CLEANUP_EVENTS_FORM.javascript}
  <form {$T_CLEANUP_EVENTS_FORM.attributes}>
   {$T_CLEANUP_EVENTS_FORM.hidden}
   <fieldset class = "fieldsetSeparator">
    <legend>{$smarty.const._PURGEEVENTS}</legend>
   <table>
    <tr><td class="labelCell">{$smarty.const._EVENTSSIZE}:&nbsp;</td>
     <td class="elementCell">{$T_EVENTS_SIZE} {$smarty.const._ENTRIES}</td></tr>
    <tr><td class="labelCell">{$smarty.const._OLDESTEVENT}:&nbsp;</td>
     <td class="elementCell">#filter:timestamp-{$T_LAST_EVENTS_ENTRY}#</td></tr>
          <tr><td class = "labelCell">{$smarty.const._PURGEEVENTSOLDERTHAN}:&nbsp;</td>
              <td class = "elementCell">{eF_template_html_select_date prefix="purge_" time=$T_LAST_EVENTS_ENTRY start_year="-1" end_year="+5" field_order = $T_DATE_FORMATGENERAL}</td></tr>
    <tr><td></td>
     <td class = "submitCell">{$T_CLEANUP_EVENTS_FORM.submit.html}</td></tr>
   </table>
   </fieldset>
  </form>

 {/capture}
 {capture name = "t_maintenance_code"}
 <table class = "formElements">
  <tr><td class = "labelCell">{$smarty.const._VERSION}:&nbsp;</td>
   <td class = "elementCell">{$smarty.const.G_VERSION_NUM} {$T_VERSION_TYPES[$smarty.const.G_VERSIONTYPE_CODEBASE]}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._DATABASEVERSION}:&nbsp;</td>
   <td class = "elementCell">{$T_CONFIGURATION.database_version} {$T_VERSION_TYPES[$T_CONFIGURATION.version_type]}</td></tr>
  {if $T_DIFFERENT_VERSIONS}
  <tr><td></td>
   <td class = "infoCell" style = "vertical-align:middle"><img src = "images/16x16/warning.png" class = "ajaxHandle" title = "{$smarty.const._WARNING}" alt = "{$smarty.const._WARNING}"/><span style = "vertical-align:middle"> {$smarty.const._DIFFERENTVERSIONSUPGRADENEEDED|replace:"%link":"<a href = 'install/install.php?step=1&upgrade=1' style = 'vertical-align:middle'>`$smarty.const._UPGRADE`</a>"}</span></td></tr>
  {/if}
  <tr><td class = "labelCell">{$smarty.const._BUILD}:&nbsp;</td>
   <td class = "elementCell">{$smarty.const.G_BUILD}</td></tr>
 </table>
 <div class = "tabber">
     <div class = "tabbertab">
         <h3>{$smarty.const._ENVIRONMENTALCHECK}</h3>
         {include file = 'includes/check_status.tpl'}
     </div>
  <div class = "tabbertab {if $smarty.get.tab=='phpinfo'}tabbertabdefault{/if}" title = "{$smarty.const._PHPINFO}">
    {capture name = 't_php_info_code'}
  <div class = "phpinfodisplay">{$T_PHPINFO}</div>
 {/capture}
 {eF_template_printBlock title=$smarty.const._PHPINFO data=$smarty.capture.t_php_info_code image='32x32/php.png'}
 </div>

    <div class = "tabbertab {if $smarty.get.tab=='lock_down'}tabbertabdefault{/if}">
        <h3>{$smarty.const._LOCKDOWN}</h3>
  {capture name = 't_lock_down_code'}
         {$T_LOCKDOWN_FORM.javascript}
         <form {$T_LOCKDOWN_FORM.attributes}>
       {$T_LOCKDOWN_FORM.hidden}
       <table class = "formElements">
           {if $T_CONFIGURATION.lock_down}
           <tr><td class = "labelCell severeWarning">{$smarty.const._THESYSTEMISCURRENTLYLOCKED}&nbsp;</td>
               <td class = "elementCell">{$T_LOCKDOWN_FORM.submit_unlock.html}</td></tr>
           {else}
           <tr><td class = "labelCell">{$smarty.const._LOCKDOWNMESSAGE}:&nbsp;</td>
               <td class = "elementCell">{$T_LOCKDOWN_FORM.lock_message.html}</td>
           <tr><td class = "labelCell">{$smarty.const._LOGOUTUSERS}:&nbsp;</td>
               <td class = "elementCell">{$T_LOCKDOWN_FORM.logout_users.html}</td>
           <tr><td colspan = "2">&nbsp;</td></tr>
           <tr><td class = "labelCell"></td>
               <td class = "elementCell">{$T_LOCKDOWN_FORM.submit_lockdown.html}</td></tr>
           {/if}
          </table>
         </form>
  {/capture}
  {eF_template_printBlock title=$smarty.const._LOCKDOWN data=$smarty.capture.t_lock_down_code image='32x32/key.png'}
    </div>

    {capture name = 't_permissions_code'}
        <table>
      <tr><td class = "labelCell">{$smarty.const._CLICKHERETOCHECKPERMISSIONS}:&nbsp;</td>
          <td class = "submitCell"><input type = "button" class = "flatButton" value = "{$smarty.const._CHECKPERMISSIONS}" onclick = "setPermissions(this, 'check')"/></td></tr>
   <tr><td></td>
          <td class = "infoCell">{$smarty.const._CHECKPERMISSIONSINSTRUCTIONS}</td></tr>
      <tr><td class = "labelCell">{$smarty.const._CLICKHERETOSETPERMISSIONS}:&nbsp;</td>
          <td class = "submitCell"><input type = "button" class = "flatButton" value = "{$smarty.const._SETPERMISSIONS}" onclick = "setPermissions(this, 'set')"/></td></tr>
   <tr><td></td>
          <td class = "infoCell">{$smarty.const._SETPERMISSIONSINSTRUCTIONS}</td></tr>
      <tr><td class = "labelCell">{$smarty.const._CLICKHERETOUNSETPERMISSIONS}:&nbsp;</td>
          <td class = "submitCell"><input type = "button" class = "flatButton" value = "{$smarty.const._UNSETPERMISSIONS}" onclick = "setPermissions(this, 'unset')"/></td></tr>
   <tr><td></td>
          <td class = "infoCell">{$smarty.const._UNSETPERMISSIONSINSTRUCTIONS}</td></tr>
      <tr><td class = "labelCell">{$smarty.const._OPERATIONOUTCOME}:&nbsp;</td>
       <td class = "elementCell" id = "failed_permissions">{$smarty.const._NOOPERATIONPERFORMEDYET}</td></tr>
        </table>

    {/capture}
    {capture name = 't_reindex_code'}
        <table>
      <tr><td class = "labelCell">{$smarty.const._CLICKHERETOREINDEX}:&nbsp;</td>
          <td class = "submitCell"><input type = "button" class = "flatButton" value = "{$smarty.const._RECREATE}" onclick = "reIndex(this)"/></td></tr>
        </table>
    {/capture}
    {capture name = 't_clear_cache_code'}
        <table>
      <tr><td class = "labelCell">{$smarty.const._CLEARTEMPLATESCACHE}:&nbsp;</td>
          <td class = "submitCell"><input class = "flatButton" type = "button" value = "{$smarty.const._CLEAR}" onclick = "clearCache(this, 'templates')"/></td></tr>
      <tr><td class = "labelCell">{$smarty.const._CLEARTESTSCACHE}:&nbsp;</td>
          <td class = "submitCell"><input class = "flatButton" type = "button" value = "{$smarty.const._CLEAR}" onclick = "clearCache(this, 'tests')"/></td></tr>
      <tr><td class = "labelCell">{$smarty.const._CLEARQUERYCACHE}:&nbsp;</td>
          <td class = "submitCell"><input class = "flatButton" type = "button" value = "{$smarty.const._CLEAR}" onclick = "clearCache(this, 'query')"/></td></tr>
      <tr><td class = "labelCell">{$smarty.const._CLEAROPCODECACHE}:&nbsp;</td>
          <td class = "submitCell"><input class = "flatButton" type = "button" value = "{$smarty.const._CLEAR}" onclick = "clearCache(this, 'apc')"/></td></tr>
        </table>
 {/capture}

 {capture name = "t_cleanup_div_code"}
  <div class = "tabber">
  {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
          {eF_template_printBlock tabber = "cleanup" title=$smarty.const._CLEANUP data=$smarty.capture.t_cleanup_code image='32x32/cleanup.png'}
          {eF_template_printBlock tabber = "reindex" title=$smarty.const._RECREATESEARCHTABLE data=$smarty.capture.t_reindex_code image='32x32/import_export.png'}
          {eF_template_printBlock tabber = "permissions" title=$smarty.const._PERMISSIONS data=$smarty.capture.t_permissions_code image='32x32/generic.png'}
  {/if}
          {eF_template_printBlock tabber = "clear_cache" title=$smarty.const._CLEARCACHE data=$smarty.capture.t_clear_cache_code image='32x32/error_delete.png'}
  </div>
 {/capture}

  {eF_template_printBlock tabber = "cleanup" title=$smarty.const._CLEANUP data=$smarty.capture.t_cleanup_div_code image='32x32/cleanup.png'}
  <div class = "tabbertab {if $smarty.get.tab=='auto_login'}tabbertabdefault{/if}">
   <h3>{$smarty.const._AUTOLOGIN}</h3>
   {capture name = 't_auto_login_code'}
   <img src = "images/16x16/help.png" title = "{$smarty.const._INFO}" alt = "{$smarty.const._INFO}" style="vertical-align:middle"/>&nbsp;{$smarty.const._AUTOLOGINITHLINK}:&nbsp;{$smarty.const.G_SERVERNAME}index.php?autologin=&lt;{$smarty.const._ACCESSLINK}&gt;
   <br /><br />
<!--ajax:usersTable-->
            <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=maintenance&autologin=1&">
                <tr class = "topTitle">
                    <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                    <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                    <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
     <td class = "topTitle centerAlign" name = "access_link">{$smarty.const._ACCESSLINK}</td>
                    <td class = "topTitle centerAlign" name = "autologin">{$smarty.const._CHECK}</td>

                </tr>
                {foreach name = 'autologin_list' key = 'key' item = 'user' from = $T_ALL_USERS}
                <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                    <td>#filter:login-{$user.login}#</td>
                    <td>{$user.name}</td>
                    <td>{$user.surname}</td>
     <td class = "centerAlign"><span id="link_{$user.login}">{$user.autologin}</span></td>
                    <td class = "centerAlign">
                        <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this);" {if $user.autologin != ""}checked = "checked"{/if} />{if $user.autologin != ""}<span style = "display:none">checked</span>{/if} {*Text for sorting*}
                    </td>
                </tr>
                {foreachelse}
                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                {/foreach}
            </table>
<!--/ajax:usersTable-->
   {/capture}
   {eF_template_printBlock title=$smarty.const._AUTOLOGIN data=$smarty.capture.t_auto_login_code image='32x32/keys.png'}
  </div>


 </div>
 {/capture}
 {eF_template_printBlock title=$smarty.const._MAINTENANCE data=$smarty.capture.t_maintenance_code image='32x32/maintenance.png' help = 'Maintenance'}

</td></tr>
{/capture}
