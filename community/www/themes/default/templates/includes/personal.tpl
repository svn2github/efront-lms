{* Smarty template for includes/personal.php *}
<script>{if $T_BROWSER == 'IE6'}{assign var='globalImageExtension' value='gif'}var globalImageExtension = 'gif';{else}{assign var='globalImageExtension' value='png'}var globalImageExtension = 'png';{/if}</script>
<script>

 var areYouSureYouWantToCancelConst ='{$smarty.const._AREYOUSUREYOUWANTTOCANCELJOB}';
 var sessionType ='{$smarty.session.s_type}';
 var editUserLogin ='{$smarty.get.edit_user}';
 var operationCategory ='{$smarty.get.op}';
 var jobAlreadyAssignedConst ='{$smarty.const._JOBALREADYASSIGNED}';
 var jobDoesNotExistConst ='{$smarty.const._JOBDOESNOTEXIST}';
 var noPlacementsAssigned ='{$smarty.const._NOPLACEMENTSASSIGNEDYET}';
 var onlyImageFilesAreValid ='{$smarty.const._ONLYIMAGEFILESAREVALID}';

 var userHasLesson ='{$smarty.const._USERHASTHELESSON}';
 var serverName ='{$smarty.const.G_SERVERNAME}';

 var msieBrowser ='{$smarty.const.MSIE_BROWSER}';
 var sessionLogin ='{$smarty.session.s_login}';
 var clickToChangeStatus ='{$smarty.const._CLICKTOCHANGESTATUS}';
 var youHaventSetAdditionalAccounts ='{$smarty.const._MAPPEDACCOUNTSUCCESSFULLYDELETED}';
 var openFacebookSession ='{$T_OPEN_FACEBOOK_SESSION}';
 var currentOperation ='{$T_OP}';
var isInfoToolDisabled = {$T_CONFIGURATION.disable_tooltip != 1};

var jobsRows = new Array();
var branchesValues = new Array();
var jobValues = new Array();
var branchPositionValues = new Array();

var tabberLoadingConst = "{$smarty.const._LOADINGDATA}";
var enableMyJobSelect = false;
</script>


{************************************************** My Account **********************************************}
{******* contains: my Settings|my Profile, mapped accounts, HCD tabs, my Payments ***************************}
{if $smarty.get.add_user || $T_OP == "account"}

 {*** User settings ***}
 {capture name = 't_personal_data_code'}
  {$T_PERSONAL_DATA_FORM.javascript}
  <form {$T_PERSONAL_DATA_FORM.attributes}>
   {$T_PERSONAL_DATA_FORM.hidden}

   {if !(isset($smarty.get.add_user))}
   <fieldset class = "fieldsetSeparator">
   <legend>{$T_TITLES.account.edituser}</legend>
   {/if}

   <table class = "formElements" width="90%">

   {* enterprise edition: Insert a second column - new table *}





    {if (isset($smarty.get.add_user))}

     <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.new_login.label}:&nbsp;</td>
      <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.new_login.html}</td></tr>
      <tr><td></td><td class = "infoCell">{$smarty.const._ONLYALLOWEDCHARACTERSLOGIN}</td></tr>
     {if $T_PERSONAL_DATA_FORM.new_login.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.new_login.error}</td></tr>{/if}
     <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.password_.label}:&nbsp;</td>
      <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.password_.html}</td></tr>
     <tr><td></td><td class = "infoCell">{$smarty.const._PASSWORDMUSTBE6CHARACTERS|replace:"%x":$T_CONFIGURATION.password_length}</td></tr>
     {if $T_PERSONAL_DATA_FORM.password_.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.password_.error}</td></tr>{/if}

     <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.passrepeat.label}:&nbsp;</td>
      <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.passrepeat.html}</td></tr>
     {if $T_PERSONAL_DATA_FORM.passrepeat.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.passrepeat.error}</td></tr>{/if}
    {else}
     {if !$T_LDAP_USER}
      <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.password_.label}:&nbsp;</td>
       <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.password_.html}</td></tr>
      <tr><td></td><td class = "infoCell">{$smarty.const._PASSWORDMUSTBE6CHARACTERS|replace:"%x":$T_CONFIGURATION.password_length}</td></tr>
      {if $T_PERSONAL_DATA_FORM.password_.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.password_.error}</td></tr>{/if}

      <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.passrepeat.label}:&nbsp;</td>
       <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.passrepeat.html}</td></tr>
      {if $T_PERSONAL_DATA_FORM.passrepeat.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.passrepeat.error}</td></tr>{/if}
     {else}
      <tr><td class = "labelCell">{$smarty.const._PASSWORD}:&nbsp;</td>
       <td style="white-space:nowrap;">{$smarty.const._LDAPUSER}</td></tr>
     {/if}
    {/if}
    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.name.label}:&nbsp;</td>
     <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.name.html}</td></tr>
    {if $T_PERSONAL_DATA_FORM.name.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.name.error}</td></tr>{/if}

    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.surname.label}:&nbsp;</td>
     <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.surname.html}</td></tr>
    {if $T_PERSONAL_DATA_FORM.surname.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.surname.error}</td></tr>{/if}
    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.email.label}:&nbsp;</td>
     <td>{$T_PERSONAL_DATA_FORM.email.html}</td></tr>
    {if $T_PERSONAL_DATA_FORM.email.error && $smarty.const.G_VERSIONTYPE != 'enterprise'}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.email.error}</td></tr>{/if}
    {if ($smarty.session.s_type == "administrator" || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_CTG != "personal"))}
      <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.group.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.group.html}</td></tr>
     {* if $T_CURRENTUSERROLEID == 0*} <!-- Removed in order to allowed to subadmins to change user type -->
      <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.user_type.label}:&nbsp;</td>
      <td>{$T_PERSONAL_DATA_FORM.user_type.html}</td></tr>
      {if $T_PERSONAL_DATA_FORM.user_type.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.user_type.error}</td></tr>{/if}
     {*/if*}
    {/if}
    {if $T_PERSONAL_DATA_FORM.languages_NAME.label != ""}
     <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.languages_NAME.label}:&nbsp;</td>
      <td>{$T_PERSONAL_DATA_FORM.languages_NAME.html}</td></tr>
      {if $T_PERSONAL_DATA_FORM.languages_NAME.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.languages_NAME.error}</td></tr>{/if}
    {/if}
    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.timezone.label}:&nbsp;</td>
          <td>{$T_PERSONAL_DATA_FORM.timezone.html}</td></tr>
    {if ($smarty.session.s_type == "administrator" || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_CTG != "personal"))}
     <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.active.label}:&nbsp;</td>
      <td>{$T_PERSONAL_DATA_FORM.active.html}</td></tr>
      {if $T_PERSONAL_DATA_FORM.active.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.active.error}</td></tr>{/if}
    {/if}
    {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_FIELDS }
     <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.$item.label}:&nbsp;</td>
      <td class = "elementCell">{$T_PERSONAL_DATA_FORM.$item.html}</td></tr>
     {if $T_PERSONAL_DATA_FORM.$item.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.$item.error}</td></tr>{/if}
    {/foreach}
    {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_DATES }
     <tr><td class = "labelCell">{$item.name}:&nbsp;</td>
      <td class = "elementCell">{eF_template_html_select_date prefix=$item.prefix emptyvalues="1" time=$item.value start_year="-80" end_year="+10" field_order = $T_DATE_FORMATGENERAL}</td></tr>
    {/foreach}
    {if (!isset($smarty.get.add_user))}
    <tr><td class = "labelCell">{$smarty.const._REGISTRATIONDATE}:&nbsp;</td>
     <td>#filter:timestamp-{$T_REGISTRATION_DATE}#</td></tr>
      {/if}
    {* enterprise version: If no module then submit button here, else insert the second column of data and submit will be inserted later elsewhere *}
     <tr><td></td><td class = "submitCell" style = "text-align:left">
        {$T_PERSONAL_DATA_FORM.submit_personal_details.html}</td></tr>
   </table>
  </form>
  {if !(isset($smarty.get.add_user))}
   {*** User profile ***}
   {if (isset($T_PERSONAL_CTG) || ($smarty.session.s_type == "administrator" || $smarty.session.employee_type == $smarty.const._SUPERVISOR) ) && isset($T_SOCIAL_INTERFACE)}
   {/if}
   <fieldset class = "fieldsetSeparator">
   <legend>{$T_TITLES.account.profile}</legend>
   {$T_AVATAR_FORM.javascript}
   <form {$T_AVATAR_FORM.attributes}>
    {$T_AVATAR_FORM.hidden}
    <table class = "formElements">
     {if isset($T_SOCIAL_INTERFACE)}
      {if ($smarty.get.personal) || ($smarty.get.edit_user == $smarty.session.s_login)}
       {*@TODO: FILE UPLOAD MISSING HERE*}
      {/if}
      <tr><td></td>
       <td><span>
        <img style="vertical-align:middle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
        <a href = "javascript:toggleEditor('short_description','simpleEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
       </span></td></tr>
      <tr><td class = "labelCell">{$T_AVATAR_FORM.short_description.label}:&nbsp;</td>
       <td class = "elementCell">{$T_AVATAR_FORM.short_description.html}</td></tr>
      <tr><td colspan = "2">&nbsp;</td></tr>
     {/if}
     <tr><td class = "labelCell">{$smarty.const._CURRENTAVATAR}:&nbsp;</td>
      <td class = "elementCell"><img src = "view_file.php?file={$T_AVATAR}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if} /></td></tr>
    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
     <tr><td class = "labelCell">{$T_AVATAR_FORM.delete_avatar.label}:&nbsp;</td>
      <td class = "elementCell">{$T_AVATAR_FORM.delete_avatar.html}</td></tr>
     <tr><td class = "labelCell">{$T_AVATAR_FORM.file_upload.label}:&nbsp;</td>
      <td class = "elementCell">{$T_AVATAR_FORM.file_upload.html}</td></tr>
     <tr><td></td>
      <td class = "infoCell">{$smarty.const._FILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILE_SIZE}</b> {$smarty.const._KB}</td></tr>
     <tr><td class = "labelCell">{$T_AVATAR_FORM.system_avatar.label}:&nbsp;</td>
      <td class = "elementCell">{$T_AVATAR_FORM.system_avatar.html}&nbsp;(<a href = "{$smarty.server.PHP_SELF}?{if $smarty.get.ctg=='personal'}ctg=personal{elseif $smarty.get.edit_user}ctg=users&edit_user={$smarty.get.edit_user}{/if}&show_avatars_list=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._VIEWLIST}', 2)">{$smarty.const._VIEWLIST}</a>)</td></tr>
     <tr><td colspan = "2">&nbsp;</td></tr>
     <tr><td></td>
      <td class = "elementCell">{$T_AVATAR_FORM.submit_upload_file.html}</td></tr>
    {/if}
    </table>
   </form>
   </fieldset>
  {/if}
 {/capture}
{/if}
{if $T_OP == "account"}
 {*** Mapped accounts ***}
 {if isset($T_ADDITIONAL_ACCOUNTS) && $T_CONFIGURATION.mapped_accounts == 0 || ($T_CONFIGURATION.mapped_accounts == 1 && $T_CURRENT_USER->user.user_type != 'student') || ($T_CONFIGURATION.mapped_accounts == 2 && $T_CURRENT_USER->user.user_type == 'administrator')}
 {capture name = "t_additional_accounts_code"}
  <div class = "headerTools">
   <span>
    <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDACCOUNT}" title = "{$smarty.const._ADDACCOUNT}">
    <a href = "javascript:void(0)" onclick = "$('add_account').show();">{$smarty.const._ADDACCOUNT}</a>
   </span>
  </div>
  <div id = "add_account" style = "display:none">
   {$smarty.const._LOGIN}: <input type = "text" name = "account_login" id = "account_login">
   {$smarty.const._PASSWORD}: <input type = "password" name = "account_password" id = "account_password">
   <img class = "ajaxHandle" src = "images/16x16/success.png" alt = "{$smarty.const._ADD}" title = "{$smarty.const._ADD}" onclick = "addAccount(this)">
   <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._CANCEL}" title = "{$smarty.const._CANCEL}" onclick = "$('add_account').hide();">
  </div>
  <br/>
  <fieldset class = "fieldsetSeparator">
   <legend>{$smarty.const._ADDITIONALACCOUNTS}</legend>
   <table id = "additional_accounts">
   {foreach name = 'additional_accounts_list' item = "item" key = "key" from = $T_ADDITIONAL_ACCOUNTS}
    <tr><td>#filter:login-{$item}#&nbsp;</td>
     <td><img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEACCOUNT}" title = "{$smarty.const._DELETEACCOUNT}" onclick = "deleteAccount(this, '{$item}')"></td>
   {foreachelse}
   <tr id = "empty_accounts"><td class = "emptyCategory">{$smarty.const._YOUHAVENTSETADDITIONALACCOUNTS}</td></tr>
   {/foreach}
   </table>
  </fieldset>
  {if $T_FACEBOOK_ENABLED}
  <fieldset class = "fieldsetSeparator" id = "facebook_accounts">
   <legend>{$smarty.const._FACEBOOKMAPPEDACCOUNT}</legend>
   {if $T_FB_ACCOUNT}
   <div>{$T_FB_ACCOUNT.fb_name} <img style = "vertical-align:middle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEACCOUNT}" title = "{$smarty.const._DELETEACCOUNT}" onclick = "deleteFacebookAccount(this, '{$T_FB_ACCOUNT.users_LOGIN}')"></div>
   {else}
   <div class = "emptyCategory" id = "empty_fb_accounts">{$smarty.const._YOUHAVENTSETFACEBOOKACCOUNT}</div>
   {/if}
  </fieldset>
  {/if}
  <script>
  {if $smarty.get.ctg == 'personal'}var additionalAccountsUrl = '{$smarty.server.PHP_SELF}?ctg=personal';{else}var additionalAccountsUrl = '{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}';{/if}
  </script>
 {/capture}
 {/if}
{/if}
{*---------------------------------- My Status ----------------------------------*}
{*------- contains: my Lessons, my Courses, my Groups, my Certifications -------*}
{if $T_OP == "status"}
 {if ($smarty.session.s_type == "administrator") || $T_IS_SUPERVISOR}
  {assign var = "courses_url" value = "`$smarty.server.PHP_SELF`?ctg=users&edit_user=`$smarty.get.edit_user`&op=`$smarty.get.op`&lessons=1&"}
  {assign var = "_change_handles_" value = $_change_}
 {else}
  {assign var = "courses_url" value = "`$smarty.server.PHP_SELF`?ctg=personal&op=`$smarty.get.op`&lessons=1&"}
  {assign var = "_change_handles_" value = false}
 {/if}
 {capture name = "t_courses_list_code"}
  <script>
   translationsToJS['_USERACCESSGRANTED'] = '{$smarty.const._USERACCESSGRANTED}';
   translationsToJS['_APPLICATIONPENDING'] = '{$smarty.const._APPLICATIONPENDING}';
  </script>
  {include file = "includes/common/courses_list.tpl"}
 {/capture}
 {capture name = "t_lessons_code"}
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'lessonsTable'}
<!--ajax:lessonsTable-->
  <table id = "lessonsTable" size = "{$T_TABLE_SIZE}" class = "sortedTable" useAjax = "1" url = "{$courses_url}">
  {$smarty.capture.lessons_list}
  </table>
<!--/ajax:lessonsTable-->
 {/if}
 {/capture}
 {*** User groups ***}
 {capture name = 't_users_to_groups_code'}
<!--ajax:groupsTable-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "groupsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?{if $T_CTG != 'personal' || $smarty.session.s_type == "administrator"}ctg=users&edit_user={$smarty.get.edit_user}{else}ctg=personal{/if}&op=status&">
   <tr class = "topTitle">
    <td class = "topTitle" name = "name" width="30%">{$smarty.const._NAME}</td>
    <td class = "topTitle" name = "description" width="50%">{$smarty.const._DESCRIPTION}</td>
    <td class = "topTitle centerAlign" name = "partof" width="20%">{$smarty.const._CHECK}</td>
   </tr>
  {foreach name = 'users_to_groups_list' key = 'key' item = 'group' from = $T_DATA_SOURCE}
   <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$group.active}deactivatedTableElement{/if}">
    <td>
     {if $_admin_}
      <a href = "{$smarty.server.PHP_SELF}?ctg=user_groups&edit_user_group={$group.id}" class = "editLink">{$group.name}</a>
     {else}
      {$group.name}
     {/if}
    </td>
    <td>{$group.description}</td>
    <td class = "centerAlign">
    {if ($smarty.get.ctg == "personal" && $smarty.session.s_type != 'administrator') || (isset($T_CURRENT_USER->coreAccess.users) && $T_CURRENT_USER->coreAccess.users != 'change')}
     {if $group.partof == 1}
      <img src = "images/16x16/success.png" alt = "{$smarty.const._PARTOFTHISGROUP}" title = "{$smarty.const._PARTOFTHISGROUP}" />
     {/if}
    {else}
     <input class = "inputCheckBox" type = "checkbox" id = "group_{$group.id}" name = "{$group.id}" onclick ="ajaxUserPost('group', '{$group.id}', this);" {if $group.partof == 1}checked{/if}>
    {/if}
    </td>
   </tr>
  {foreachelse}
   <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "3">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
  </table>
<!--/ajax:groupsTable-->
 {/capture}
 {*** User form ***}
{/if}
{if $T_OP == "dashboard"}
 {if $T_SOCIAL_INTERFACE}
  {capture name = "t_status_change_interface"}
   <table class = "horizontalBlock">
    <tr><td>
   {if $smarty.session.s_type != "administrator"}
      <span class = "rightOption smallHeader">
       <img class = "ajaxHandle" src = "images/32x32/catalog.png" title = "{$smarty.const._MYCOURSES}" alt = "{$smarty.const._MYCOURSES}">
       <a class = "titleLink" href = "{$smarty.server.PHP_SELF}?ctg=lessons" title = "{$smarty.const._MYCOURSES}">{$smarty.const._MYCOURSES}</a>
      </span>
   {else}
      <span class = "rightOption smallHeader">
       <img class = "ajaxHandle" src = "images/32x32/home.png" title = "{$smarty.const._HOME}" alt = "{$smarty.const._HOME}">
       <a class = "titleLink" href = "{$smarty.server.PHP_SELF}?ctg=control_panel" title = "{$smarty.const._HOME}">{$smarty.const._HOME}</a>
      </span>
   {/if}
      <span class = "leftOption">#filter:login-{$T_USER.login}#&nbsp;</span>
     </td>
    </tr>
   </table>
  {/capture}
 {/if}
{/if}
{if (isset($smarty.get.add_evaluation) || isset($smarty.get.edit_evaluation))}
{*** Employee edit evaluations ***}
{capture name = 't_evaluations_code'}
   {$T_EVALUATIONS_FORM.javascript}
   <table width = "75%">
    <tr>
     <td width="70%">
       <form {$T_EVALUATIONS_FORM.attributes}>
       {$T_EVALUATIONS_FORM.hidden}
        <table class = "formElements">
        <tr><td></td>
        <td><span>
         <img style="vertical-align:middle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
         <a href = "javascript:toggleEditor('specification','simpleEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
        </span></td></tr>
         <tr>
          <td class = "labelCell">{$T_EVALUATIONS_FORM.specification.label}:&nbsp;</td>
          <td style="white-space:nowrap;">{$T_EVALUATIONS_FORM.specification.html}</td>
         </tr>
         {if $T_EVALUATIONS_FORM.specification.error}<tr><td></td><td class = "formError">{$T_EVALUATIONS_FORM.specification.error}</td></tr>{/if}
         <tr><td colspan = "2">&nbsp;</td></tr>
         <tr><td></td><td class = "submitCell" style = "text-align:left">
          {$T_EVALUATIONS_FORM.submit_evaluation_details.html}</td>
         </tr>
      </table>
     </form>
    </td>
   </tr>
  </table>
  {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location.toString()+'&tab=evaluations';</script>
  {/if}
{/capture}
{/if}
{*----------------------------------------- PRESENTATION SETUP ACCORDING TO TYPE OF MANAGEMENT ----------------------------------------------*}
{capture name = 't_user_code'}
 {****** ADD USER PAGE ******}
 {if isset($smarty.get.add_user)}
  {$smarty.capture.t_personal_data_code}
 {****** PERSONAL MANAGEMENT PAGE ******}
 {elseif $T_PERSONAL_CTG}
  {*** Dashboard ***}
  {if !$T_OP || $T_OP == "dashboard"}
   {include file = "social.tpl"}
  {*** Account ***}
  {elseif $T_OP == "account"}
   <div class="tabber">
    <div class="tabbertab" title="{$T_TITLES.account.edituser}">
     {eF_template_printBlock title = $T_TITLES.account.edituser data = $smarty.capture.t_personal_data_code image = '32x32/profile.png'}
    </div>
    {if isset($T_ADDITIONAL_ACCOUNTS) && $T_CONFIGURATION.mapped_accounts == 0 || ($T_CONFIGURATION.mapped_accounts == 1 && $T_CURRENT_USER->user.user_type != 'student') || ($T_CONFIGURATION.mapped_accounts == 2 && $_admin_)}
    <div class="tabbertab{if ($smarty.get.tab == "mapped_accounts")} tabbertabdefault {/if}" title = "{$T_TITLES.account.mapped}">
     {eF_template_printBlock title = $T_TITLES.account.mapped data = $smarty.capture.t_additional_accounts_code image = '32x32/users.png'}
    </div>
    {/if}
   </div>
  {*** Status ***}
  {elseif $T_OP == "status"}
   <div class="tabber">
   {if !$_admin_}
    {eF_template_printBlock tabber="courses" title = $T_TITLES.status.courses data = $smarty.capture.t_courses_list_code image = '32x32/courses.png'}
    {if $T_CONFIGURATION.lesson_enroll}
     {eF_template_printBlock tabber="lessons" title = $T_TITLES.status.lessons data = $smarty.capture.t_lessons_code image = '32x32/lessons.png'}
    {/if}
   {/if}
   {eF_template_printBlock tabber="groups" title = $T_TITLES.status.groups data = $smarty.capture.t_users_to_groups_code image = '32x32/users.png'}
    {if ($T_SHOW_USER_FORM)}
    <div class="tabbertab {if $smarty.get.tab=='user_form'}tabbertabdefault{/if}" title="{$smarty.const._MYEMPLOYEEFORM}">
     {eF_template_printBlock title = $smarty.const._USERFORM titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
    </div>
    {/if}
   </div>
  {/if}
 {****** USER MANAGEMENT BY THIRD PARTIES ******}
 {else}
  {*** Account ***}
  {if $T_OP == "account"}
  <div class="tabber">
   {eF_template_printBlock tabber = "personal" title = $T_TITLES.account.edituser data = $smarty.capture.t_personal_data_code image = '32x32/profile.png'}
  </div>
  {*** Status ***}
  {elseif $T_OP == "status"}
  <div class="tabber">
   {if $T_EDITEDUSER->user.user_type != 'administrator'}
    {eF_template_printBlock tabber="courses" title = $T_TITLES.status.courses data = $smarty.capture.t_courses_list_code image = '32x32/courses.png'}
    {if $T_CONFIGURATION.lesson_enroll}
     {eF_template_printBlock tabber="lessons" title = $T_TITLES.status.lessons data = $smarty.capture.t_lessons_code image = '32x32/lessons.png'}
    {/if}
   {/if}
   {eF_template_printBlock tabber="groups" title = $T_TITLES.status.groups data = $smarty.capture.t_users_to_groups_code image = '32x32/users.png'}
  </div>
  {/if}
 {/if}
{/capture}
{*------------------------------------------------------- ACTUAL PRESENTATION ---------------------------------------------------------------*}
{*** Evaluations popup (maybe this should leave from here) ***}
{if (isset($smarty.get.add_evaluation) || isset($smarty.get.edit_evaluation))}
 {eF_template_printBlock title = $smarty.const._EVALUATIONOFEMPLOYEE|cat:'&nbsp;'|cat:$smarty.get.edit_user data = $smarty.capture.t_evaluations_code image = '32x32/catalog.png'}
{*** System avatars popup (maybe this should leave from here) ***}
{elseif $smarty.get.show_avatars_list}
 <table width = "100%" cellpadding = "5" class = "filemanagerBlock">
  <tr>{foreach name = "avatars_list" item = "item" key = "key" from = $T_SYSTEM_AVATARS}
    <td align = "center"><a href = "javascript:void(0)" onclick = "parent.document.getElementById('select_avatar').selectedIndex = {$smarty.foreach.avatars_list.index}{if $T_SOCIAL_INTERFACE}+1{/if};parent.document.getElementById('popup_close').onclick();window.close();"><img src = "{$smarty.const.G_SYSTEMAVATARSURL}{$item}" border = "0" / ><br/>{$item}</a></td>
    {if $smarty.foreach.avatars_list.iteration % 4 == 0}</tr><tr>{/if}
   {/foreach}
  </tr>
 </table>
{elseif $smarty.get.printable}
 {eF_template_printBlock title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
{else}
{*** The user page appearance ***}
 {if isset($smarty.get.add_user)}
  {eF_template_printBlock title = $smarty.const._NEWUSER data = $smarty.capture.t_user_code image = '32x32/user.png'}
 {elseif $T_PERSONAL_CTG}
   {* Change user status interface *}
   {if $T_SOCIAL_INTERFACE}
   {$smarty.capture.t_status_change_interface}
   {/if}
  {eF_template_printBlock title = $smarty.const._PERSONALDATA data = $smarty.capture.t_user_code image = '32x32/profile.png' main_options = $T_TABLE_OPTIONS help = 'Dashboard'}
 {else}
  {if $smarty.get.print_preview == 1}
   {eF_template_printBlock title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
  {elseif $smarty.get.print == 1}
   {eF_template_printBlock title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
  {else}
   {eF_template_printBlock title = "`$smarty.const._USEROPTIONSFOR`<span class = 'innerTableName'>&nbsp;&quot;#filter:login-`$T_EDITEDUSER->user.login`#&quot;</span>" data = $smarty.capture.t_user_code image = '32x32/profile.png' main_options = $T_TABLE_OPTIONS options = $T_STATISTICS_LINK}
  {/if}
 {/if}
{/if}
