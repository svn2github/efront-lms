 {*moduleReports: Show employees satisfying some criteria *}
  <script>
  var searchSkillTemplate = '{$T_REPORT_FORM.search_skill_template.html|replace:"\n":""}';
  var youShouldEitherProvideExistingOrNewGroup = '{$smarty.const._YOUSHOULDPROVIDEEXISTINGORNEWGROUP}';
  </script>

    {capture name = 't_reports_code'}
   <table class = "formElements">
   <tr><td>{$T_REPORT_FORM.criteria.all_criteria.html} </td><td>{$smarty.const._SATISFYALLCRITERIA}</td></tr>
   <tr><td>{$T_REPORT_FORM.criteria.any_criteria.html} </td><td>{$smarty.const._SATISFYANYCRITERIA}</td></tr>
    {if isset($smarty.get.all) && $smarty.get.all=="false" }
     {literal}
     <script>document.getElementById('any_criteria').checked = true;</script>
     {/literal}
    {else}
     {literal}
     <script>document.getElementById('all_criteria').checked = true;</script>
     {/literal}
    {/if}
   <tr><td>&nbsp;</td></tr>
   <table>
   <br>

   <table><!--style="visibility:hidden"-->
   <tr><td>{$T_REPORT_FORM.search_branch.label}:&nbsp;</td><td>{$T_REPORT_FORM.search_branch.html}</td><td>{$T_REPORT_FORM.include_subbranches.html}</td><td id="include_subbranches_label">({$T_REPORT_FORM.include_subbranches.label})</td></tr>
    {if isset($smarty.get.branch_ID) && $smarty.get.branch_ID != 0}
     {literal}
     <script>
     var branch_select = document.getElementById('search_branch');
     for (i = 0; i < branch_select.options.length; i++) {
      if (branch_select.options[i].value == '{/literal}{$smarty.get.branch_ID}{literal}') {
        branch_select.options[i].selected = true;
        break;
      }
     }
     </script>
     {/literal}
    {/if}
   <tr><td>{$T_REPORT_FORM.search_job_description.label}:&nbsp;</td><td>{$T_REPORT_FORM.search_job_description.html}</td></tr>
    {if isset($smarty.get.job_description_ID) && $smarty.get.job_description_ID != ""}
     {literal}
     <script>
     var a;
     var job_description_select = document.getElementById('search_job_description');
     for (i = 0; i < job_description_select.options.length; i++) {
      a = new String("{/literal}{$smarty.get.job_description_ID}{literal}");
      if (job_description_select.options[i].value.toString() == a) {
        job_description_select.options[i].selected = true;
      } else if (job_description_select.options[i].value == "__emptybranch_name" || job_description_select.options[i].value == "__emptyother_branch") {
       job_description_select.options[i].disabled = true;
      }
     }
     </script>
     {/literal}
    {/if}

   <tr><td>{$T_REPORT_FORM.search_skill.label}:&nbsp;</td><td>{$T_REPORT_FORM.search_skill.html}</td>
    <td><a href="javascript:void(0);" onclick="add_new_criterium_row()"><img src="images/16x16/add.png" title="{$smarty.const._NEWSEARCHCRITERIUM}" alt="{$smarty.const._NEWSEARCHCRITERIUM}" border="0"/></a></td></tr>
    {if isset($smarty.get.skill_ID) && $smarty.get.skill_ID != 0}
     {literal}
     <script>
     var skill_select = document.getElementById('search_skill');
     for (i = 0; i < skill_select.options.length; i++) {
      if (skill_select.options[i].value == '{/literal}{$smarty.get.skill_ID}{literal}') {
        skill_select.options[i].selected = true;
        break;
      }
     }
     </script>
     {/literal}
    {/if}
   <tr><td></td><td colspan=2><table border = "0" id="criteriaTable"></table></td></tr>

   <tr><td>&nbsp;</td></tr>
   </table>
    {/capture}

    {* Form with all advanced search criteria *}
    {capture name = 't_reports_advanced_search'}
   <table class = "formElements">
    <tr><td width = "33%">
     <table>
      <tr><td class = "labelCell">{$T_REPORT_FORM.new_login.label}:&nbsp;</td><td>{$T_REPORT_FORM.new_login.html}</td></tr>
      <tr><td class = "labelCell">{$T_REPORT_FORM.name.label}:&nbsp;</td><td>{$T_REPORT_FORM.name.html}</td></tr>
      <tr><td class = "labelCell">{$T_REPORT_FORM.surname.label}:&nbsp;</td><td>{$T_REPORT_FORM.surname.html}</td></tr>
      <tr><td class = "labelCell">{$T_REPORT_FORM.email.label}:&nbsp;</td><td>{$T_REPORT_FORM.email.html}</td></tr>
      <tr><td class = "labelCell">{$T_REPORT_FORM.user_type.label}:&nbsp;</td><td>{$T_REPORT_FORM.user_type.html}</td></tr>
      <tr><td class = "labelCell">{$T_REPORT_FORM.active.label}:&nbsp;</td><td>{$T_REPORT_FORM.active.html}</td></tr>
      <script>
      // Comma separated fields that required special handling by Javascript search
      var customProfileSearchCriteria = "{$T_USER_PROFILE_FIELDS_CRITERIA}";
      var datesSearchCriteria = "{$T_DATES_SEARCH_CRITERIA}";
      </script>
      {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_FIELDS }
       <tr><td class = "labelCell">{$T_REPORT_FORM.$item.label}:&nbsp;</td>
        <td class = "elementCell">{$T_REPORT_FORM.$item.html}</td></tr>
       {if $T_REPORT_FORM.$item.error}<tr><td></td><td class = "formError">{$T_REPORT_FORM.$item.error}</td></tr>{/if}
      {/foreach}
      {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_DATES }
       <tr><td class = "labelCell">{$item.name}:&nbsp;</td>
        <td class = "elementCell" NOWRAP>{eF_template_html_select_date searchtype=1 prefix=$item.prefix emptyvalues="1" time=0 start_year="-45" end_year="+10" field_order = $T_DATE_FORMATGENERAL onChange="javascript:onDateUpdated(this)"}</td></tr>
      {/foreach}
      <tr><td class = "labelCell">{$T_REPORT_FORM.registration.label}:&nbsp;</td><td NOWRAP>{eF_template_html_select_date searchtype=1 prefix="timestamp" emptyvalues="1" time=0 start_year="-10" end_year="+10" field_order = $T_DATE_FORMATGENERAL onChange="javascript:onDateUpdated(this)"}</td></tr>
     </td>
    </tr>
   </table> {* And of main table of class = formelements *}
   {* Print the new centrally aligned submit button - This table is closed </table> by the closing tab of the main table of the eFront normal interface *}
   <table width ="66%">
    <tr><td>&nbsp;</td></tr>
    <tr><td class = "submitCell" style = "text-align:center" align="center">{$T_REPORT_FORM.submit_personal_details.html}</td></tr>
     </table>
    {/capture}
  {*moduleShowemployees: Show employees*}
  {capture name = 't_employees_code'}
{*222222222222*}
  <div class = "headerTools" id = "groupUsersMsgId">
            <span>
                <img src = "images/16x16/add.png" title = "{$smarty.const._SETFOUNDEMPLOYEESINTOGROUP}" alt = "{$smarty.const._SETFOUNDEMPLOYEESINTOGROUP}">
                <a href = "javascript:void(0)" onClick = "eF_js_showDivPopup('{$smarty.const._SETFOUNDEMPLOYEESINTOGROUP}', 0, 'insert_into_group')" target = 'POPUP_FRAME'>{$smarty.const._SETFOUNDEMPLOYEESINTOGROUP}</a>
            </span>
        </div>
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'foundEmployees'}
<!--ajax:foundEmployees-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "foundEmployees" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.session.s_type}.php?ctg=search_users&">
   <tr class = "topTitle">
    <td class = "topTitle" name="login">{$smarty.const._USER}</td>
    <td class = "topTitle" name="languages_NAME">{$smarty.const._LANGUAGE}</td>
    <td class = "topTitle noSort" align="center">{$smarty.const._SENDMESSAGE}</td>
    <td class = "topTitle noSort" align="center">{$smarty.const._STATISTICS}</td>
    <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
   </tr>
    {if $T_TABLE_SIZE > 0}
   {foreach name = 'users_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
   <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
   <td>
    {if ($user.pending == 1)}
    <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">#filter:login-{$user.login}#</a>
    {elseif ($user.active == 1)}
    <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">#filter:login-{$user.login}#</a>
    {else}
    #filter:login-{$user.login}#
    {/if}
   </td>
   <td>{$user.languages_NAME}</td>
   <td align="center"><a style="" href="{$smarty.server.PHP_SELF}?ctg=messages&add=1&recipient={$user.login}&popup=1" onclick='eF_js_showDivPopup("{$smarty.const._SENDMESSAGE}", 2)' target="POPUP_FRAME"><img src="images/16x16/mail.png" border="0"></a></td>
   <td align="center"><a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
   <td class="centerAlign">
    {if 1}
     <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
    {else}
     <img border = "0" src = "images/16x16/edit.png" class = "inactiveImage" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
    {/if}
    {if $smarty.session.s_login != $user.login}
     <a href = "{$smarty.session.s_type}.php?ctg=users&op=users_data&delete_user={$user.login}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTOFIREEMPLOYEE}')" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._FIRE}" alt = "{$smarty.const._FIRE}" /></a>
    {else}
     <a href = "javascript:void(0);" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" class = "inactiveImage" title = "{$smarty.const._FIRE}" alt = "{$smarty.const._FIRE}" /></a>
    {/if}
   </td>
   </tr>
   {/foreach}
     <tr style="display:none"><td><input type="hidden" id="usersFound" value="{$T_SENDALLMAIL_URL}" /></td></tr>
     {if $smarty.const.MSIE_BROWSER == 1}
      <img style="display:none" src="images/16x16/question_type_free_text.png" onLoad="javascript:new Effect.Appear('sendToAllId');" />
     {else}
      <script>
      new Effect.Appear('groupUsersId');
      new Effect.Appear('groupUsersMsgId');
      new Effect.Appear('sendToAllId');
      </script>
     {/if}
  {else}
     <tr><td colspan ="8" class = "emptyCategory">{$smarty.const._NOEMPLOYEESFULFILLTHESPECIFIEDCRITERIA}</td></tr>
     <tr style="display:none"><td><input type="hidden" id="usersFound" value="{$T_SENDALLMAIL_URL}" /></td></tr>
     {if $smarty.const.MSIE_BROWSER == 1}
     <img style="display:none" src="images/16x16/question_type_free_text.png" onload="if (document.getElementById('sendToAllId')) {ldelim}document.getElementById('sendToAllId').style.display='none';{rdelim}" />
     {else}
      <script>
      document.getElementById('groupUsersId').style.display = 'none';
      document.getElementById('groupUsersMsgId').style.display = 'none';
      document.getElementById('sendToAllId').style.display = 'none';
      </script>
     {/if}
  {/if}
  </table>
<!--/ajax:foundEmployees-->
{/if}
  {/capture}
 {*moduleShowemployees: Show courses to be assigned*}
 {capture name = 't_courses_to_be_assigned_code'}
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'coursesTable'}
<!--ajax:coursesTable-->
 <table style = "width:100%" size = "{$T_TABLE_SIZE}" id = "coursesTable" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=search_users&op={$smarty.get.op}&courses=1&">
  <tr class = "topTitle">
   <td class = "topTitle" name = "name" style = "width:{if $smarty.get.ctg == "users"}35%{else}60%{/if}">{$smarty.const._NAME}</td>
   <td class = "topTitle" name = "directions_ID" style = "width:30%">{$smarty.const._PARENTDIRECTIONS}</td>
   {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
   <td class = "topTitle centerAlign noSort" style = "width:5%">{$smarty.const._ASSIGNMENT}</td>
   {/if}
  </tr>
  {foreach name = 'users_to_courses_list' key = 'key' item = 'course' from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$course.active}deactivatedTableElement{/if}">
   <td>{strip}
    {if $course.has_instances}
     <img src = "images/16x16/plus.png" class = "ajaxHandle" alt = "{$smarty.const._COURSEINSTANCES}" title = "{$smarty.const._COURSEINSTANCES}" onclick = "toggleSubSection(this, '{$course.id}', 'instancesTable')"/>
    {/if}
    {$course.name}
   {/strip}</td>
   <td>{$T_DIRECTION_PATHS[$course.directions_ID]}</td>
   <td class = "centerAlign">
    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
     <img class = "ajaxHandle" src = "images/16x16/goto_student.png" alt = "{$smarty.const._SELECTION}" title = "{$smarty.const._SELECTION}" onclick = "assignCourseToUsers(this, '{$course.id}')">
    {/if}
   </td>
  </tr>
  {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "10">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
 </table>
<!--/ajax:coursesTable-->
 {/if}
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'instancesTable'}
<div id = "filemanager_div" style = "display:none;">
<!--ajax:instancesTable-->
 <table style = "width:100%" no_auto = "1" size = "{$T_TABLE_SIZE}" id = "instancesTable" class = "sortedTable subSection" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=search_users&op={$smarty.get.op}&courses=1&">
  <tr class = "topTitle">
   <td class = "topTitle" name = "name" style = "width:{if $smarty.get.ctg == "users"}35%{else}60%{/if}">{$smarty.const._NAME}</td>
   <td class = "topTitle" name = "directions_ID" style = "width:30%">{$smarty.const._PARENTDIRECTIONS}</td>
   {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
   <td class = "topTitle centerAlign noSort" style = "width:5%">{$smarty.const._ASSIGNMENT}</td>
   {/if}
  </tr>
  {foreach name = 'users_to_courses_list' key = 'key' item = 'course' from = $T_DATA_SOURCE}
   <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$course.active}deactivatedTableElement{/if}">
   <td>{$course.name}</td>
   <td>{$T_DIRECTION_PATHS[$course.directions_ID]}</td>
   <td class = "centerAlign">
    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
     <img class = "ajaxHandle" src = "images/16x16/goto_student.png" alt = "{$smarty.const._SELECTION}" title = "{$smarty.const._SELECTION}" onclick = "assignCourseToUsers(this, '{$course.id}')">
    {/if}
   </td>
  </tr>
  {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "10">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
 </table>
<!--/ajax:instancesTable-->
</div>
 {/if}
 {/capture}
 {*moduleShowemployees: Show courses to be assigned*}
 {capture name = 't_custom_group_stats_code'}
 <div class = "statisticsDiv" id = "statsDivCustomGroup">
<!--ajax:customGroupStats-->
  <span>
  {if isset($T_USER_TRAFFIC)}
                <table class = "statisticsGeneralInfo">
                    <tr><td class = "topTitle" colspan = "2">{$smarty.const._GROUPUSERTRAFFIC}</td></tr>
                    <tr class = "oddRowColor">
                     <td class = "labelCell">{$smarty.const._TOTALLOGINS}: </td>
                     <td class = "elementCell">{$T_USER_TRAFFIC.total_logins}</td></tr>
                    <tr class = "evenRowColor">
                     <td class = "labelCell">{$smarty.const._LESSONACCESS}: </td>
                     <td class = "elementCell">{$T_USER_TRAFFIC.total_access}</td></tr>
                </table>
    <br/>
                <table class = "statisticsTools">
                    <tr><td>{$smarty.const._ACCESSPERLESSON}</td></tr>
                </table>
                <table class = "statisticsTools">
                    <tr>
                        <td class = "topTitle">{$smarty.const._LESSON}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._ACCESSNUMBER}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._TOTALACCESSTIME}</td>
                        {*<td class = "topTitle noSort centerAlign">{$smarty.const._OPTIONS}</td>*}
                    </tr>
                    {foreach name = 'lesson_traffic_list' key = "id" item = "lesson" from = $T_USER_TRAFFIC.lessons}
                        <tr class = "{cycle name = 'lessontraffic' values = 'oddRowColor, evenRowColor'} {if !$lesson.active}deactivatedTableElement{/if}">
                            <td>{$lesson.name}</td>
                            <td class = "centerAlign">{$lesson.accesses}</td>
                            <td class = "centerAlign">
                             <span style="display:none">{$lesson.total_seconds}</span>
                                {if $lesson.total_seconds}
                                 {if $lesson.hours}{$lesson.hours}{$smarty.const._HOURSSHORTHAND} {/if}
                                 {if $lesson.minutes}{$lesson.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}
                                 {if $lesson.seconds}{$lesson.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}
                                {else}
                                 {$smarty.const._NOACCESSDATA}
                                {/if}
                            </td>
                            {*
                            <td class = "centerAlign">
                                <a href = "display_chart.php?id=10&from={$T_FROM_TIMESTAMP}&to={$T_TO_TIMESTAMP}&login={$T_USER_LOGIN}&lesson_id={$id}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2)" target = "POPUP_FRAME">
                                 <img src = "images/16x16/reports.png" title = "{$smarty.const._ACCESSSTATISTICS}" alt = "{$smarty.const._ACCESSSTATISTICS}"/></a>
                            </td>
                            *}
                        </tr>
                    {foreachelse}
                     <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
                </table>
        </span>
  {else}
  <span class = "emptyCategory">{$smarty.const._NOUSERSFOUND}</span>
  {/if}
<!--/ajax:customGroupStats-->
 </div>
 {/capture}
   {* **************************************************************
    DISPLAYING THE CAPTURED TABLES
    ************************************************************** *}
{capture name = 't_search_all'}
 {$T_REPORT_FORM.javascript}
  <form {$T_REPORT_FORM.attributes}>
   {$T_REPORT_FORM.hidden}
     {eF_template_printBlock title = $smarty.const._SEARCHCRITERIA data = $smarty.capture.t_reports_advanced_search image = '32x32/search.png'}
    <br>
    <div class="tabber">
     <div class="tabbertab" title="{$smarty.const._SEARCHRESULTS}">
      {eF_template_printBlock title = $smarty.const._EMPLOYEESFULFILLINGCRITERIA data = $smarty.capture.t_employees_code image = '32x32/user.png' options = $T_SENDALLMAIL_LINK}
     </div>
     <div class="tabbertab" title="{$smarty.const._ASSIGNCOURSES}">
      {eF_template_printBlock title = $smarty.const._MASSCOURSESASSIGNMENTINFO data = $smarty.capture.t_courses_to_be_assigned_code image = '32x32/courses.png'}
     </div>
     <div class="tabbertab">
      <h3>{$smarty.const._USERSTRAFFIC}</h3>
      {eF_template_printBlock title = $smarty.const._USERSTRAFFIC data = $smarty.capture.t_custom_group_stats_code image = '32x32/search.png'}
     </div>
    </div>
  </form>
{/capture}
    {eF_template_printBlock title = $smarty.const._FINDEMPLOYEES data = $smarty.capture.t_search_all image = '32x32/scorm.png' main_options = $T_TABLE_OPTIONS}
<div id='insert_into_group' style="display:none">
{capture name = 't_insert_into_group_code'}
 {$T_INSERT_INTO_GROUP_POPUP_FORM.javascript}
  <form {$T_INSERT_INTO_GROUP_POPUP_FORM.attributes}>
   {$T_INSERT_INTO_GROUP_POPUP_FORM.hidden}
   <table class = "formElements" width="100%">
   <tr><td class = "labelCell">{$T_INSERT_INTO_GROUP_POPUP_FORM.existing_group.label}:&nbsp;</td><td>{$T_INSERT_INTO_GROUP_POPUP_FORM.existing_group.html}</td></tr>
   <tr><td class = "labelCell">{$T_INSERT_INTO_GROUP_POPUP_FORM.new_group.label}:&nbsp;</td><td>{$T_INSERT_INTO_GROUP_POPUP_FORM.new_group.html}</td></tr>
   <tr><td>&nbsp;</td></tr>
   <tr><td></td><td><input type="button" class="flatButton" onclick="insertFoundUsersIntoGroup(this)" value="{$smarty.const._SUBMIT}" /><input type="button" class="flatButton" onclick="insertFoundUsersIntoGroupAndGotoIt(this)" value="{$smarty.const._SUBMITANDEDITGROUP}" /></td></tr>
   </table>
  </form>
{/capture}
{eF_template_printBlock title = $smarty.const._INSERTINTOGROUP data = $smarty.capture.t_insert_into_group_code image = '32x32/users.png'}
</div>
