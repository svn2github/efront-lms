{capture name = "t_personal_code"}
 {if $T_OP == 'dashboard'}
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
       <span class = "leftOption">#filter:login-{$T_CURRENT_USER->user.login}#&nbsp;</span>







      </td>
     </tr>
    </table>
   {/capture}
   {$smarty.capture.t_status_change_interface}
  {/if}

  {include file = "social.tpl"}
 {elseif $T_OP == 'profile' || $T_OP == 'user_groups' || $T_OP == 'mapped_accounts' || $T_OP == 'payments'}
  <div class = "tabber">
  {if in_array('profile', $T_ACCOUNT_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='profile'}tabbertabdefault{/if}" title = "{$smarty.const._PERSONALDATA}">{include file = "includes/personal/profile.tpl"}</div>
  {/if}
  {if in_array('user_groups', $T_ACCOUNT_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='user_groups'}tabbertabdefault{/if}" title = "{$smarty.const._GROUPS}">{include file = "includes/personal/user_groups.tpl"}</div>
  {/if}
  {if in_array('mapped_accounts', $T_ACCOUNT_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='mapped_accounts'}tabbertabdefault{/if}" title = "{$smarty.const._MAPPEDACCOUNTS}">{include file = "includes/personal/mapped_accounts.tpl"}</div>
  {/if}
  {if in_array('payments', $T_ACCOUNT_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='payments'}tabbertabdefault{/if}" title = "{$smarty.const._PAYMENTS}">{include file = "includes/personal/payments.tpl"}</div>
  {/if}
  </div>
 {elseif $T_OP == 'user_courses' || $T_OP == 'user_lessons' || $T_OP == 'certificates' || $T_OP == 'user_form'}
  <div class = "tabber">
  {if in_array('user_courses', $T_LEARNING_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='user_courses'}tabbertabdefault{/if}" title = "{$smarty.const._COURSES}">{include file = "includes/personal/user_courses.tpl"}</div>
  {/if}
  {if in_array('user_lessons', $T_LEARNING_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='user_lessons'}tabbertabdefault{/if}" title = "{$smarty.const._LESSONS}">{include file = "includes/personal/user_lessons.tpl"}</div>
  {/if}
  {if in_array('certificates', $T_LEARNING_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='certificates'}tabbertabdefault{/if}" title = "{$smarty.const._CERTIFICATES}">{include file = "includes/personal/certificates.tpl"}</div>
  {/if}
  {if in_array('user_form', $T_LEARNING_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='user_form'}tabbertabdefault{/if}" title = "{$smarty.const._USERFORM}">{include file = "includes/personal/user_form.tpl"}</div>
  {/if}
  </div>
 {elseif $T_OP == 'placements' || $T_OP == 'history' || $T_OP == 'skills' || $T_OP == 'evaluations' || $T_OP == 'org_form'}
  <div class = "tabber">
   <div class = "tabbertab {if $T_OP=='org_form'}tabbertabdefault{/if}" title = "{$smarty.const._ORGANIZATIONALDATA}">{include file = "includes/personal/org_form.tpl"}</div>
   <div class = "tabbertab {if $T_OP=='placements'}tabbertabdefault{/if}" title = "{$smarty.const._PLACEMENTS}">{include file = "includes/personal/placements.tpl"}</div>
   <div class = "tabbertab {if $T_OP=='skills'}tabbertabdefault{/if}" title = "{$smarty.const._SKILLS}">{include file = "includes/personal/skills.tpl"}</div>
   <div class = "tabbertab {if $T_OP=='evaluations'}tabbertabdefault{/if}" title = "{$smarty.const._EVALUATIONS}">{include file = "includes/personal/evaluations.tpl"}</div>
   <div class = "tabbertab {if $T_OP=='history'}tabbertabdefault{/if}" title = "{$smarty.const._HISTORY}">{include file = "includes/personal/history.tpl"}</div>
  </div>
 {elseif $T_OP == 'files' && ($T_EDITEDUSER->user.login==$T_CURRENT_USER->user.login || $T_CURRENT_USER->user.user_type=='administrator')}
  {include file = "includes/personal/files.tpl"}
 {/if}
{/capture}
{if $smarty.get.show_avatars_list}
 <table width = "100%" >
  <tr>
  {foreach name = "avatars_list" item = "item" key = "key" from = $T_SYSTEM_AVATARS}
   {if $smarty.foreach.avatars_list.first}{assign var="item" value = "unknown_small.png"}{/if}
   <td class = "centerAlign ">
    <img src = "{$smarty.const.G_SYSTEMAVATARSURL}{$item}" class = "ajaxHandle" alt = "{$item}" title = "{$item}" onclick = "parent.$('select_avatar').selectedIndex = '{$smarty.foreach.avatars_list.index}';parent.$('popup_close').onclick();window.close();"/>
    <br/>{$item}
   </td>
   {if $smarty.foreach.avatars_list.iteration % 4 == 0}</tr><tr>{/if}
  {/foreach}
  </tr>
 </table>
{elseif $smarty.get.add_placement || $smarty.get.edit_placement}
 {include file = "includes/personal/placements.tpl"}
{elseif $smarty.get.add_evaluation || $smarty.get.edit_evaluation}
 {include file = "includes/personal/evaluations.tpl"}
{elseif $T_OP == 'user_form' && $smarty.get.printable}
 {include file = "includes/personal/user_form.tpl"}
{else}
 {eF_template_printBlock title = $smarty.const._PERSONALDATA data = $smarty.capture.t_personal_code image = '32x32/user.png' main_options = $T_TABLE_OPTIONS}
{/if}




{if 0}
<script>{if $T_BROWSER == 'IE6'}{assign var='globalImageExtension' value='gif'}var globalImageExtension = 'gif';{else}{assign var='globalImageExtension' value='png'}var globalImageExtension = 'png';{/if}</script>
<script>

 var areYouSureYouWantToCancelConst ='{$smarty.const._AREYOUSUREYOUWANTTOCANCELJOB}';
 var sessionType ='{$smarty.session.s_type}';
 var editUserLogin ='{$smarty.get.user}';
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
{capture name = 't_user_code'}
 {if isset($smarty.get.add_user)}
  {$smarty.capture.t_personal_data_code}
 {elseif $T_PERSONAL_CTG}
  {if !$T_OP || $T_OP == "dashboard"}
   {include file = "social.tpl"}
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
{/if}
