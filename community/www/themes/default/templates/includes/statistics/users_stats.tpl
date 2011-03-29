
{assign var = "courses_url" value = "`$smarty.server.PHP_SELF`?ctg=statistics&option=user&sel_user=`$smarty.get.sel_user`&"}
{assign var = "_change_handles_" value = false}
{capture name = "t_courses_list_code"}
 {include file = "includes/common/courses_list.tpl"}
{/capture}
{if $T_CONFIGURATION.lesson_enroll}
 {capture name = "t_lessons_list_code"}
  {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'lessonsTable'}
<!--ajax:lessonsTable-->
  <table id = "lessonsTable" size = "{$T_TABLE_SIZE}" class = "sortedTable subSection" useAjax = "1" url = "{$courses_url}">
   {$smarty.capture.lessons_list}
  </table>
<!--/ajax:lessonsTable-->
  {/if}
 {/capture}
{/if}

{capture name = "t_moreinfo_code"}
 <table class = "statisticsGeneralInfo">
  <tr><td class = "topTitle" colspan = "2">{$smarty.const._GENERALUSERINFO}</td></tr>
  <tr class = "{cycle name = 'general_user_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._LANGUAGE}:</td>
    <td class = "elementCell">{$T_USER_INFO.general.language}</td>
  </tr>
  <tr class = "{cycle name = 'general_user_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._ACTIVE}:</td>
    <td class = "elementCell">{$T_USER_INFO.general.active_str}</td>
  </tr>
  <tr class = "{cycle name = 'general_user_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._JOINED}:</td>
    <td class = "elementCell">#filter:timestamp-{$T_USER_INFO.general.joined}#</td>
  </tr>
  <tr><td class = "topTitle" colspan = "2">{$smarty.const._USERCOMMUNICATIONINFO}</td></tr>
  {if $T_CONFIGURATION.disable_forum != 1}
   <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._FORUMPOSTS}:</td>
    <td class = "elementCell">{$T_USER_INFO.communication.forum_messages|@sizeof}</td>
   </tr>
   <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._FORUMLASTMESSAGE}:</td>
    <td class = "elementCell">#filter:timestamp-{$T_USER_INFO.communication.last_message.timestamp}#</td>
   </tr>
  {/if}
  {if $T_CONFIGURATION.disable_messages != 1}
   <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._PERSONALMESSAGES}:</td>
    <td class = "elementCell">{$T_USER_INFO.communication.personal_messages|@sizeof}</td>
   </tr>
   <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._MESSAGESFOLDERS}:</td>
    <td class = "elementCell">{$T_USER_INFO.communication.personal_folders|@sizeof}</td>
   </tr>
  {/if}
  <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell" >{$smarty.const._FILES}:</td>
   <td class = "elementCell">{$T_USER_INFO.communication.files|@sizeof}</td>
  </tr>
  <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._FOLDERS}:</td>
   <td class = "elementCell">{$T_USER_INFO.communication.folders|@sizeof}</td>
  </tr>
  <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._TOTALSIZE}:</td>
   <td class = "elementCell">{$T_USER_INFO.communication.total_size}KB</td>
  </tr>

  {if $T_CONFIGURATION.chat_enabled == 1}
  <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._CHATMESSAGES}:</td>
   <td class = "elementCell">{$T_USER_INFO.communication.chat_messages|@sizeof}</td>
  </tr>
  <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._CHATLASTMESSAGE}:</td>
   <td class = "elementCell">#filter:timestamp-{$T_USER_INFO.communication.last_chat.timestamp}#</td>
  </tr>
  {/if}
  {if $T_CONFIGURATION.disable_comments != 1}
   <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._COMMENTS}:</td>
    <td class = "elementCell">{$T_USER_INFO.communication.comments|@sizeof}</td>
   </tr>
  {/if}
  <tr><td class = "topTitle" colspan = "2">{$smarty.const._USERUSAGEINFO}</td></tr>
  <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._LASTLOGIN}:</td>
   <td class = "elementCell">#filter:timestamp-{$T_USER_INFO.usage.last_login.timestamp}#</td>
  </tr>
  <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._TOTALLOGINS}:</td>
   <td class = "elementCell">{$T_USER_INFO.usage.logins|@sizeof}</td>
  </tr>
  <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._MONTHLOGINS}:</td>
   <td class = "elementCell">{$T_USER_INFO.usage.month_logins|@sizeof}</td>
  </tr>
  <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._WEEKLOGINS}:</td>
   <td class = "elementCell">{$T_USER_INFO.usage.week_logins|@sizeof}</td>
  </tr>
  <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._MEANDURATION}:</td>
   <td class = "elementCell">{$T_USER_INFO.usage.meanDuration.time_string}</td>
  </tr>
  <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._MONTHMEANDURATION}:</td>
   <td class = "elementCell">{$T_USER_INFO.usage.monthmeanDuration.time_string}</td>
  </tr>
   <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._WEEKMEANDURATION}:</td>
   <td class = "elementCell">{$T_USER_INFO.usage.weekmeanDuration.time_string}</td>
  </tr>
  <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
   <td class = "labelCell">{$smarty.const._LASTIPUSED}:</td>
   <td class = "elementCell">{$T_USER_INFO.usage.last_ip}</td>
  </tr>
 </table>
{/capture}
 {if $T_BASIC_TYPE == 'administrator' || $T_BASIC_TYPE == 'professor' || isset($T_USER_INFO.general.supervised_by_user)}
{capture name = t_traffic_code}
  <form name = "period">
  <table class = "statisticsSelectDate">
  <!-- <tr><td class = "labelCell">{$smarty.const._SETPERIOD}:&nbsp;</td>
    <td class = "elementCell">
     <select id="predefined_periods" onChange="setPeriod(this)">
      {foreach name = 'predefined_periods' key = "id" item = "period" from = $T_PREDEFINED_PERIODS}
       <option value = "{$period.value}" {if $smarty.get.predefined !="" && $smarty.get.predefined == $period.value}selected{/if}>{$period.name}</option>
      {/foreach}
     </select>
     </td></tr> -->
   <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
    <td class = "elementCell">{eF_template_html_select_date prefix = "from_" time = $T_FROM_TIMESTAMP start_year = "-5" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $T_FROM_TIMESTAMP display_seconds = false}</td></tr>
   <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
    <td class = "elementCell">{eF_template_html_select_date prefix = "to_" time = $T_TO_TIMESTAMP start_year = "-5" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="to_" time = $T_TO_TIMESTAMP display_seconds = false}</td></tr>
   <tr><td class = "labelCell">{$smarty.const._ANALYTICLOG}:</td>
    <td class = "elementCell"><input class = "inputCheckbox" type = checkbox id = "showLog" {if (isset($T_USER_LOG))}checked{/if}></td></tr>
   <tr><td class = "labelCell"></td>
    <td class = "elementCell"><a href = "javascript:void(0)" onclick = "showStats('day')">{$smarty.const._LAST24HOURS}</a> - <a href = "javascript:void(0)" onclick = "showStats('week')">{$smarty.const._LASTWEEK}</a> - <a href = "javascript:void(0)" onclick = "showStats('month')">{$smarty.const._LASTMONTH}</a></td></tr>

   <tr><td></td>
    <td class = "elementCell"><input type = "button" class = "flatButton" value = "{$smarty.const._SHOW}" onclick = "document.location='{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$T_USER_LOGIN}&tab=usertraffic&from_year='+document.period.from_Year.value+'&from_month='+document.period.from_Month.value+'&from_day='+document.period.from_Day.value+'&from_hour='+document.period.from_Hour.value+'&from_min='+document.period.from_Minute.value+'&to_year='+document.period.to_Year.value+'&to_month='+document.period.to_Month.value+'&to_day='+document.period.to_Day.value+'&to_hour='+document.period.to_Hour.value+'&to_min='+document.period.to_Minute.value+'&showlog='+document.period.showLog.checked"></td>
   </tr>
  </table>
  </form>

  <table class = "statisticsTools">
   <tr><td id = "right">
     <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2, 'graph_table');showGraph($('proto_chart'), 'graph_access');">{$smarty.const._ACCESSSTATISTICS}:</a>
     <img class = "handle" src = "images/16x16/reports.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2, 'graph_table');showGraph($('proto_chart'), 'graph_access');"/>
    </td></tr>
  </table>
  <div id = "graph_table" style = "display:none"><div id = "proto_chart" class = "proto_graph"></div></div>
  <table class = "statisticsGeneralInfo">
   <tr><td class = "topTitle" colspan = "2">{$smarty.const._USERTRAFFIC}</td></tr>
   <tr class = "oddRowColor">
    <td class = "labelCell">{$smarty.const._TOTALLOGINS}: </td>
    <td class = "elementCell">{$T_USER_TRAFFIC.total_logins}</td></tr>
  </table>

  {if $T_REPORTS_USER->user.user_type != 'administrator'}
  <br/>
  <table class = "statisticsTools">
   <tr><td>{$smarty.const._LESSONTIMES}</td></tr>
  </table>
  <table class = "sortedTable" style = "width:100%">
   <tr>
    <td class = "topTitle">{$smarty.const._LESSON}</td>
    <td class = "topTitle centerAlign">{$smarty.const._TOTALACCESSTIME}</td>
    <td class = "topTitle centerAlign">{$smarty.const._COMPLETED}</td>
    <td class = "topTitle noSort centerAlign">{$smarty.const._OPTIONS}</td>
   </tr>
   {foreach name = 'lesson_traffic_list' key = "id" item = "lesson" from = $T_USER_TRAFFIC.lessons}
    <tr class = "{cycle name = 'lessontraffic' values = 'oddRowColor, evenRowColor'} {if !$lesson.active}deactivatedTableElement{/if}">
     <td>{$lesson.name}</td>
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
     <td class = "centerAlign">
      <span style="display:none">{$lesson.completed}</span>
      {if $lesson.completed}<img src = "images/16x16/success.png" alt = "{$smart.const._YES}" title = "{$smarty.const._COMPLETEDON} #filter:timestamp_time-{$lesson.to_timestamp}#">{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">{/if}
     </td>
     <td class = "centerAlign">
      <img class = "handle" src = "images/16x16/reports.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2, 'graph_table');showGraph($('proto_chart'), 'graph_lesson_access', '{$id}');"/>
     </td>
    </tr>
   {foreachelse}
    <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
  </table>
  {/if}

  <br/>
  {if isset($T_USER_LOG)}
  <table class = "statisticsTools">
   <tr><td>{$smarty.const._ANALYTICLOG}</td></tr>
  </table>
  <table style = "width:100%">
   <tr>
    <td class = "topTitle">{$smarty.const._LESSON}</td>
    <td class = "topTitle">{$smarty.const._UNIT}</td>
    <td class = "topTitle">{$smarty.const._ACTION}</td>
    <td class = "topTitle">{$smarty.const._TIME}</td>
    <td class = "topTitle">{$smarty.const._IPADDRESS}</td>
   </tr>
   {foreach name = 'user_log_loop' key = "key" item = "info" from = $T_USER_LOG}
   <tr class = "{cycle name = 'user_log_list' values = 'oddRowColor, evenRowColor'}">
    <td>{$info.lesson_name}</td>
    <td>{$info.content_name}</td>
    <td>{$T_ACTIONS[$info.action]}</td>
    <td>#filter:timestamp_time-{$info.timestamp}#</td>
    <td>{$info.session_ip|eF_decodeIp}</td>
   </tr>
   {foreachelse}
   <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
  </table>
  {/if}
{/capture}
 {/if}


{capture name = 'user_statistics'}
 {if !$T_SINGLE_USER}
  <table class = "statisticsSelectList">
   <tr><td class = "labelCell">{$smarty.const._CHOOSEUSER}:</td>
    <td class = "elementCell">
     <input type = "text" id = "autocomplete" class = "autoCompleteTextBox"/>
     <img id = "busy" src = "images/16x16/clock.png" style = "display:none;" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}"/>
     <div id = "autocomplete_users" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
    </td>
   </tr>
   <tr><td></td>
    <td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td>
   </tr>
  </table>
 {/if}

 {if $smarty.get.sel_user}
  <table class = "statisticsTools">
   <tr><td id = "right">
     {$smarty.const._EXPORTSTATS}
     <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$T_USER_LOGIN}&excel=user">
      <img src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}"/>
     </a>
     <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$T_USER_LOGIN}&pdf=user">
      <img src = "images/file_types/pdf.png" title = "{$smarty.const._PDFFORMAT}" alt="{$smarty.const._PDFFORMAT}"/>
     </a>
    </td></tr>
  </table>
  <br/>
  <table class = "statisticsGeneralInfo">
   <tr><td id = "userAvatar">
     <img src = "view_file.php?file={$T_AVATAR}" title = "{$smarty.const._USERAVATAR}" alt = "{$smarty.const._USERAVATAR}"></td>
    <td>
     <table>
      <tr class = "{cycle name = 'common_user_info' values = 'oddRowColor, evenRowColor'}">
       <td class = "labelCell">{$smarty.const._USERNAME}:</td>
       <td class = "elementCell">{$T_USER_INFO.general.fullname}</td>
      </tr>
      <tr class = "{cycle name = 'common_user_info' values = 'oddRowColor, evenRowColor'}">
       <td class = "labelCell">{$smarty.const._USERTYPE}:</td>
       <td class = "elementCell">{if $T_USER_INFO.general.user_type == 'administrator'}{$smarty.const._ADMINISTRATOR}{elseif $T_USER_INFO.general.user_type == 'professor'}{$smarty.const._PROFESSOR}{else}{$smarty.const._STUDENT}{/if}</td>
      </tr>
     {if $T_USER_INFO.general.user_types_ID}
      <tr class = "{cycle name = 'common_user_info' values = 'oddRowColor, evenRowColor'}">
       <td class = "labelCell">{$smarty.const._USERROLE}:</td>
       <td class = "elementCell">{$T_ROLES[$T_USER_INFO.general.user_types_ID]}</td>
      </tr>
     {/if}
      <tr class = "{cycle name = 'common_user_info' values = 'oddRowColor, evenRowColor'}">
       <td class = "labelCell">{$smarty.const._TOTALLOGINTIME}:</td>
       <td class = "elementCell">
        {if $T_USER_INFO.general.total_login_time.hours || $T_USER_INFO.general.total_login_time.minutes || $T_USER_INFO.general.total_login_time.seconds}
         {if $T_USER_INFO.general.total_login_time.hours}{$T_USER_INFO.general.total_login_time.hours}{$smarty.const._HOURSSHORTHAND} {/if}
         {if $T_USER_INFO.general.total_login_time.minutes}{$T_USER_INFO.general.total_login_time.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}
         {if $T_USER_INFO.general.total_login_time.seconds}{$T_USER_INFO.general.total_login_time.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}
        {else}
         {$smarty.const._NOACCESSDATA}
        {/if}
       </td>
      </tr>
     </table>
   </td></tr>
  </table>
  <div class = "tabber">
  {if $T_REPORTS_USER->user.user_type != 'administrator'}
   {eF_template_printBlock tabber = "courses" title =$smarty.const._COURSES data = $smarty.capture.t_courses_list_code image = '32x32/courses.png'}
   {eF_template_printBlock tabber = "lessons" title =$smarty.const._LESSONS data = $smarty.capture.t_lessons_list_code image = '32x32/lessons.png'}
  {/if}
   {eF_template_printBlock tabber = "moreinfo" title =$smarty.const._MOREINFO data = $smarty.capture.t_moreinfo_code image = '32x32/information.png'}
  {if $smarty.capture.t_traffic_code}
   {eF_template_printBlock tabber = "usertraffic" title =$smarty.const._TRAFFIC data = $smarty.capture.t_traffic_code image = '32x32/generic.png'}
  {/if}
  </div>
 {/if}
{/capture}

{capture name = "t_specific_lesson_info_code"}
 <table class = "informationTable">
  <tr><td colspan = "2" class = "topSubtitle"><b>{$smarty.const._GENERICLESSONINFO}</b></td></tr>
  <tr>
   <td>{$smarty.const._TIMEINLESSON}:</td>
   <td>{$T_USER_STATUS_IN_LESSON->lesson.time_in_lesson.time_string}</td>
  </tr>
  <tr><td colspan = "2">&nbsp;</td></tr>
  <tr>
   <td colspan = "2" class="topSubtitle"><b>{$smarty.const._OVERALL}</b></td>
  </tr>
  <tr>
   <td>{$smarty.const._PROGRESS}:</td>
   <td class = "progressCell" style = "vertical-align:top;">
    <span class = "progressNumber">#filter:score-{$T_USER_STATUS_IN_LESSON->lesson.overall_progress.percentage}#%</span>
    <span class = "progressBar" style = "width:{$T_USER_STATUS_IN_LESSON->lesson.overall_progress.percentage}px;">&nbsp;</span>
   </td>
  </tr>

  <tr><td colspan = "2">&nbsp;</td></tr>
  <tr><td colspan = "2" class = "topSubtitle"><b>{$smarty.const._TESTS}</b></td></tr>
 {if $T_USER_STATUS_IN_LESSON->lesson.test_status.total && !$T_CONFIGURATION.disable_tests}
  <tr>
   <td>{$smarty.const._USERAVERAGESCOREFORTESTS}:</td>
   <td class = "progressCell" style = "vertical-align:top;">
    <span class = "progressNumber">#filter:score-{$T_USER_STATUS_IN_LESSON->lesson.test_status.mean_score}#%</span>
    <span class = "progressBar" style = "width:{$T_USER_STATUS_IN_LESSON->lesson.test_status.mean_score}px;">&nbsp;</span>
   </td>
  </tr>
  {foreach name = 'done_tests_list' key = "test_id" item = "test" from = $T_USER_DONE_TESTS}
   <tr>
    <td {if !$test.active}class = "deactivatedElement"{/if}>{$test.name}:</td>
    <td class = "progressCell" style = "vertical-align:top;white-space:nowrap">
     <span class = "progressNumber">#filter:score-{$test.active_score}#%</span>
     <span class = "progressBar" style = "width:{$test.active_score}px;">&nbsp;</span>
     <span style = "margin-left:120px">(#filter:timestamp_time-{$test.timestamp}#)</span>
    </td>
   </tr>
  {/foreach}
  {foreach name = 'pending_tests_list' key = "test_id" item = "test" from = $T_USER_PENDING_TESTS}
   <tr>
    <td {if !$test->test.active}class = "deactivatedElement"{/if}>{$test->test.name}:</td>
    <td class = "emptyCategory" style = "white-space:nowrap" colspan = "2">{$smarty.const._USERNOTCOMPLETEDTEST}</td>
   </tr>
  {/foreach}
 {else}
  <tr><td class = "emptyCategory" colspan = "2">{$smarty.const._THEUSERHASNOTDONEANYTESTSINTHISLESSON}</td></tr>
 {/if}

  <tr><td colspan = "2">&nbsp;</td></tr>
  <tr><td colspan = "2" class = "topSubtitle"><b>{$smarty.const._PROJECTS}</b></td></tr>
 {if !empty($T_USER_STATUS.assigned_projects)}
  <tr>
   <td >{$smarty.const._PROJECTAVERAGESCOREFORLESSON}:</td>
   <td class = "progressCell" style = "vertical-align:top;">
    <span class = "progressNumber">{$T_USER_STATUS_IN_LESSON->lesson.project_status.mean_score}%</span>
    <span class = "progressBar" style = "width:{$T_USER_STATUS_IN_LESSON->lesson.project_status.mean_score}px;">&nbsp;</span>
     </td>
  </tr>
 {/if}
 {if ($T_USER_STATUS.assigned_projects|@sizeof)}
  {foreach name = 'done_projects_list' item = project from = $T_USER_STATUS.assigned_projects}
   <tr>
    <td>{$project.title}:</td>
    {if $project.grade != ''}
     <td class = "progressCell" style = "vertical-align:top;white-space:nowrap">
      <span class = "progressNumber">{$project.grade}%</span>
      <span class = "progressBar" style = "width:{$project.grade}px;">&nbsp;</span>
      {if $project.upload_timestamp > 0}
       <span style = "margin-left:120px">{$smarty.const._FILEUPLOADEDON}: #filter:timestamp_time-{$project.upload_timestamp}#</span>
      {else}
       <span style = "margin-left:120px">{$smarty.const._NOFILEUPLOADED}</span>
      {/if}
     </td>
    {else}
     <td class = "emptyCategory" style = "white-space:nowrap" colspan = "2">{$smarty.const._PROJECTPENDING}</td>
    {/if}
   </tr>
  {/foreach}
 {else}
  <tr>
   <td style = "text-align:left" class = "emptyCategory" width="100%" colspan = "2">{$smarty.const._THEUSERHASNOTBEENASSIGNEDANYPROJECT}</td>
  </tr>
 {/if}
 </table>
{/capture}

{capture name = "t_specific_course_info_code"}
 <table class = "statisticsGeneralInfo">
  <tr>
   <td class = "topTitle" > {$smarty.const._LESSON} </td>
   <td class = "topTitle centerAlign" > {$smarty.const._CONTENT} </td>
   {if !$T_CONFIGURATION.disable_tests}
   <td class = "topTitle centerAlign" > {$smarty.const._TESTS} </td>
   {/if}
   {if !$T_CONFIGURATION.disable_projects}
   <td class = "topTitle centerAlign" > {$smarty.const._PROJECTS} </td>
   {/if}
   <td class = "topTitle centerAlign" > {$smarty.const._COMPLETED} </td>
   <td class = "topTitle centerAlign" > {$smarty.const._SCORE} </td>
  </tr>
  {foreach name = 'lesson_list' key = 'lesson_id' item = 'lesson' from = $T_USER_STATUS_IN_COURSE_LESSONS}
  <tr class = "{cycle name = "user_lessons_list" values = "oddRowColor, evenRowColor"}">
   <td>{$lesson->lesson.name}</td>
   <td class = "progressCell">
    <span class = "progressNumber">#filter:score-{$lesson->lesson.overall_progress.percentage}#%</span>
    <span class = "progressBar" style = "width:{$lesson->lesson.overall_progress.percentage}px;">&nbsp;</span>
   </td>
   {if !$T_CONFIGURATION.disable_tests}
   <td class = "progressCell">
    <span class = "progressNumber">#filter:score-{$lesson->lesson.test_status.percentage}#%</span>
    <span class = "progressBar" style = "width:{$lesson->lesson.test_status.percentage}px;">&nbsp;</span>
   </td>
   {/if}
   {if !$T_CONFIGURATION.disable_projects}
   <td class = "progressCell">
    <span class = "progressNumber">#filter:score-{$lesson->lesson.project_status.percentage}#%</span>
    <span class = "progressBar" style = "width:{$lesson->lesson.project_status.percentage}px;">&nbsp;</span>
   </td>
   {/if}
   <td class = "centerAlign">
    {if $lesson->lesson.completed}<img src = "images/16x16/success.png" alt = "{$smart.const._YES}" title = "{$smarty.const._COMPLETEDON} #filter:timestamp_time-{$lesson->lesson.to_timestamp}#">{else}<img src = "images/16x16/error_delete.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">{/if}
   </td>
   <td class = "centerAlign">
    {if $lesson->lesson.completed}#filter:score-{$lesson->lesson.score}#%{/if}
   </td>
  </tr>
  {/foreach}
 </table>
{/capture}

 {if $smarty.get.specific_lesson_info}
  {eF_template_printBlock title = $smarty.const._DETAILS data = $smarty.capture.t_specific_lesson_info_code image = '32x32/lessons.png' help = 'Reports'}
 {elseif $smarty.get.specific_course_info}
  {eF_template_printBlock title = $smarty.const._DETAILS data = $smarty.capture.t_specific_course_info_code image = '32x32/courses.png' help = 'Reports'}
 {elseif $T_REPORTS_USER}
  {eF_template_printBlock title = "`$smarty.const._REPORTSFORUSER` <span class='innerTableName'>&quot;#filter:login-`$T_REPORTS_USER->user.login`#&quot;</span>" data = $smarty.capture.user_statistics image = '32x32/users.png' help = 'Reports' options=$T_EDIT_USER_LINK}
 {else}
  {eF_template_printBlock title = $smarty.const._USERSTATISTICS data = $smarty.capture.user_statistics image = '32x32/users.png' help = 'Reports'}
 {/if}
