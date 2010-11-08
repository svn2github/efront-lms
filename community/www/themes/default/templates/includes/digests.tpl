{* Smarty template for digests.php *}

<script>



  var isenterprise = false;

</script>

{if isset($smarty.get.add_notification) || isset($smarty.get.edit_notification)}
 <script>
 var noRecipientsHaveBeenSelected = "{$smarty.const._NORECIPIENTSHAVEBEENSELECTED}";
 var theFieldConst = "{$smarty.const._THEFIELD}";
 var subjectConst = "{$smarty.const._SUBJECT}";
 var isMandatoryConst = "{$smarty.const._ISMANDATORY}";
 var basicTemplated = '{$T_BASIC_TEMPLATED}';
 var basicEventRecipients = '{$T_BASIC_EVENT_RECIPIENTS}';

 var allLessonEventRecipients = "{$T_LESSON_EVENT_RECIPIENTS.alllesson}";
 var allLessonUsersConst = "{$smarty.const._ALLLESSONUSERS}";
 var lessonProf = "{$T_LESSON_EVENT_RECIPIENTS.lessonprof}";
 var lessonProfessorsConst = "{$smarty.const._LESSONPROFESSORS}";
 var lessonNotCompleted = "{$T_LESSON_EVENT_RECIPIENTS.lessonnotcompleted}";
 var lessonNotCompletedConst = "{$smarty.const._LESSONUSERSNOTCOMPLETED}";
 var expicitlySelected = "{$T_LESSON_EVENT_RECIPIENTS.explicitlyselected}";
 var expicitlySelectedConst = "{$smarty.const._EXPLICITLYSELECTED}";

 var courseProf = "{$T_COURSE_EVENT_RECIPIENTS.courseprof}";
 var courseProfessorsConst = "{$smarty.const._COURSEPROFESSORS}";
 var allCourseEventRecipients = "{$T_COURSE_EVENT_RECIPIENTS.allcourse}";
 var allCourseUsersConst = "{$smarty.const._ALLCOURSEUSERS}";

 var lessonsNameConst = "{$smarty.const._LESSONNAME}";
 var courseNameConst = "{$smarty.const._COURSENAME}";
 var testNameConst = "{$smarty.const._TESTNAME}";
 var announcementTitleConst = "{$smarty.const._ANNOUNCEMENTTITLE}";
 var announcementBodyConst = "{$smarty.const._ANNOUNCEMENTBODY}";
 var unitNameConst = "{$smarty.const._UNITNAME}";
 var unitContentConst = "{$smarty.const._UNITCONTENT}";
 var surveyNameConst = "{$smarty.const._SURVEYNAME}";
 var surveyIdConst = "{$smarty.const._SURVEYID}";
 var surveyMessageConst = "{$smarty.const._SURVEYMESSAGE}";

 var branchNameConst = "{$smarty.const._BRANCHNAME}";
 var jobNameConst = "{$smarty.const._JOBDESCRIPTIONNAME}";

 var timeAfterEvent = "{$smarty.const._TIMEAFTEREVENT}";
 var timeBeforeEvent = "{$smarty.const._TIMEBEFOREEVENT}";
 var everyConst = "{$smarty.const._EVERY}";
 var startingFrom = "{$smarty.const._STARTINGFROM}";
 var onConst = "{$smarty.const._ON}";

 var trigUserNameConst = "{$smarty.const._TRIGGERINGUSERSNAME}";
 var trigUserSurnConst = "{$smarty.const._TRIGGERINGUSERSSURNAME}";
 var trigUserLogiConst = "{$smarty.const._TRIGGERINGUSERSLOGIN}";
 var trigUserTypeConst = "{$smarty.const._TRIGGERINGUSERSTYPE}";
 var trigUserEmailConst = "{$smarty.const._TRIGGERINGUSERSEMAIL}";

 var addEditNotification = true;
 </script>
 {capture name="t_main_digests_page"}
         {$T_DIGESTS_FORM.javascript}
         <form {$T_DIGESTS_FORM.attributes}>
             {$T_DIGESTS_FORM.hidden}
                        <div id="event_recipients_div" {if !isset($T_EVENT_FORM)}style="display:none"{/if}>
       <table class="formElements">
                       <tr><td class = "fixedLabelCell" >{$T_DIGESTS_FORM.event_recipients.label}:&nbsp;</td>
                           <td style="white-space:nowrap;">{$T_DIGESTS_FORM.event_recipients.html}</td><td id="explicitlySelectedHelp" {if $T_SHOW_EXPLICITLY_HELP != 1}style="display:none"{/if}><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, 'explicitly_selected_info', event)"><div id = 'explicitly_selected_info' onclick = "eF_js_showHideDiv(this, 'explicitly_selected_info', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:450px;height:100px;position:absolute;display:none">{$smarty.const._EXPLICITLYSELECTEDINFO}</div></td></tr>
                   </table>
      </div>
      </fieldset>
            <fieldset class = "fieldsetSeparator">
            <legend>{$smarty.const._MESSAGE}</legend>
      <table class="formElements">
       <tr>
        <td class = "fixedLabelCell">{$T_DIGESTS_FORM.header.label}:&nbsp;</td>
        <td colspan="2">{$T_DIGESTS_FORM.header.html}</td>
        <td align="right"><table><tr><td>{$T_DIGESTS_FORM.html_message.label}</td><td>{$T_DIGESTS_FORM.html_message.html}</td></tr></table></td>
       </tr>
       {if $T_DIGESTS_FORM.header.error}
       <tr>
        <td></td>
        <td colspan="3" class = "formError">{$T_DIGESTS_FORM.header.error}</td>
       </tr>
       {/if}
       <tr><td></td><td>
        <span>
         <img style="vertical-align:middle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
         <a href = "javascript:toggleEditor('messageBody','simpleEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
        </span>
       </td></tr>
       <tr>
        <td class = "fixedLabelCell">{$T_DIGESTS_FORM.message.label}:&nbsp;</td>
        <td colspan="3">{$T_DIGESTS_FORM.message.html}</td>
       </tr>
       <tr>
        <td class = "fixedLabelCell">{$T_DIGESTS_FORM.templ_add.label}:&nbsp;</td>
        <td width="5%">{$T_DIGESTS_FORM.templ_add.html}</td>
        <td align="left"><a href = "javascript:void(0)" onClick = "addTemplatizedText($('template_add'))"><img src = "images/16x16/add.png" alt = "{$smarty.const._ADDTEXTTEMPLATE}" title = "{$smarty.const._ADDTEXTTEMPLATE}" border =0></a></td>
        <td></td>
       </tr>
       {if !$T_CONFIGURATION.onelanguage}
       <tr>
        <td class = "fixedLabelCell">{$T_DIGESTS_FORM.languages_NAME.label}:&nbsp;</td>
        <td width="5%">{$T_DIGESTS_FORM.languages_NAME.html}</td>
        <td align="left"><table><tr><td><img src = "images/16x16/help.png" alt = "{$smarty.const._HELP}" title = "{$smarty.const._HELP}" onclick = "eF_js_showHideDiv(this, 'language_set_info', event)"><div id = 'language_set_info' onclick = "eF_js_showHideDiv(this, 'language_set_info', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:450px;height:100px;position:absolute;display:none">{$smarty.const._NOTIFICATIONLANGUAGETEMPLATEINFO}</div></td></tr></table></td>
        <td></td>
       </tr>
       {/if}
       <tr><td colspan = 4>&nbsp;</td></tr>
       <tr><td></td><td colspan=4>{$T_DIGESTS_FORM.submit_digest.html}</td></tr>
      </table>
      </fieldset>
  </form>
  {/capture}
  {eF_template_printBlock title = $smarty.const._EMAILDIGESTS data = $smarty.capture.t_main_digests_page image = '32x32/notifications.png'}
     <script language = "JavaScript" type = "text/javascript">
  var eventForm = "{$T_EVENT_FORM}";
  var eventCategory = "{$T_EVENT_CATEGORY}";
  var recipientsCategory = "{$T_RECIPIENTS_CATEGORY}";
     </script>
{else}
    <script language = "JavaScript" type = "text/javascript">
 var addEditNotification = false;
    </script>
 {if isset($smarty.get.op) && $smarty.get.op == "preview"}
  {if isset($T_SENT_NOTIFICATION_PREVIEW)}
   {capture name = 't_sent_notification_preview'}
   <table width="100%">
    <tr><td>{$T_SENT_NOTIFICATION_PREVIEW.subject}</td><td align="right">#filter:timestamp_time-{$T_SENT_NOTIFICATION_PREVIEW.timestamp}#</td></tr>
    <tr><td colspan="2">&nbsp</td></tr>
    <tr><td colspan="2">
    {$T_SENT_NOTIFICATION_PREVIEW.body}
    </td></tr>
   </table>
   {/capture}
   {eF_template_printBlock title = $smarty.const._PREVIEW data = $smarty.capture.t_sent_notification_preview image = '32x32/notifications.png'}
  {/if}
 {else}
     <script language = "JavaScript" type = "text/javascript">
  var deactivateConst = "{$smarty.const._DEACTIVATE}";
  var activateConst = "{$smarty.const._ACTIVATE}";
  var recipientsCategory = "{$T_RECIPIENTS_CATEGORY}";
  var sessionType = "{$smarty.session.s_type}";
     </script>
  {capture name = 't_notifications_code'}
            <table border = "0" width = "100%" class = "sortedTable">
                <tr class = "topTitle">
                    <td class = "topTitle">{$smarty.const._WHEN}</td>
                    <td class = "topTitle">{$smarty.const._EVENT}</td>
                    <td class = "topTitle">{$smarty.const._EVENTAPPLIESTO}</td>
                    <td class = "topTitle">{$smarty.const._RECIPIENTS}</td>
                    <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                    {*<td class = "topTitle">{$smarty.const._SUBJECT}</td>*}
                    {if $_change_}
                    <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
                    {/if}
                </tr>
       {if isset($T_NOTIFICATIONS)}
                {foreach name = 'notification_list' key = 'key' item = 'notification' from = $T_NOTIFICATIONS}
             <tr id = "notification_row_{$notification.id}_{if isset($notification.is_event)}1{else}0{/if}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$notification.active}deactivatedTableElement{/if}">
                    <td >{$notification.when}</td>
                    <td >{$notification.event}</td>
                    <td >{if $notification.event_notification_recipients != ""}{$notification.recipients}{/if}</td>
                    <td >{if $notification.event_notification_recipients != ""}{$notification.event_notification_recipients}{else}{$notification.recipients}{/if}</td>
                    <td class = "centerAlign">
                     <span id = "notification_status_{$notification.id}_{if isset($notification.is_event)}1{else}0{/if}" style="display:none">
                      {if $notification.active == 1}1{else}0{/if}</span>
         <a href = "javascript:void(0);" {if $_change_ && $notification.event_type != 7 && $notification.event_type != 4}onclick = "activateNotification(this, '{$notification.id}', '{$notification.is_event}')"{/if}>
                   {if $notification.active == 1}
                       <img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0">
                   {else}
                       <img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0">
                   {/if}
                  </a>
     </td>
                    {*<td >{$notification.subject|eF_truncate:40}</td>*}
                    {if $_change_}
                    <td class = "centerAlign">
      <a href = "{$smarty.server.PHP_SELF}?ctg=digests&edit_notification={$notification.id}{if isset($notification.is_event)}&event=1{/if}" class = "editLink"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
      {if $notification.event_type != 7 && $notification.event_type != 4}
                         <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if(confirm('{$smarty.const._AREYOUSUREYOUWANTTOREMOVETHATNOTIFICATION}')) deleteNotification(this, '{$notification.id}', '{$notification.is_event}')"/>
                        {/if}
                    </td>
     {/if}
                </tr>
                {/foreach}
       {else}
          <tr><td colspan=4>
          <table width = "100%">
              <tr><td class = "emptyCategory">{$smarty.const._NONOTIFICATIONSHAVEBEENREGISTERED}</td></tr>
          </table>
          </td></tr>
       {/if}
   </table>
        {/capture}
        {capture name = 't_queue_messages_code'}
            {if $smarty.session.s_type == "administrator" && (!isset($T_CURRENT_USER->coreAccess.notifications) || $T_CURRENT_USER->coreAccess.notifications == 'change')}
            <div class = "headerTools">
             <span>
              <img src="images/16x16/go_into.png" title="{$smarty.const._SENDNEXTQUEUEMESSAGES}" alt="{$smarty.const._SENDNEXTQUEUEMESSAGES}">
     <a href="javascript:void(0)" onclick = "sendQueueMessages(this)">{$smarty.const._SENDNEXTQUEUEMESSAGES}</a>
             </span>
             <span>
              <img src="images/16x16/error_delete.png" title="{$smarty.const._CLEARQUEUEMESSAGES}" alt="{$smarty.const._CLEARQUEUEMESSAGES}">
     <a href="javascript:void(0)" onclick = "clearQueueMessages(this)">{$smarty.const._CLEARQUEUEMESSAGES}</a>
             </span>
            </div>
            {/if}
<!--ajax:msgQueueTable-->
            <table style = "width:100%" class = "sortedTable" size = "{$T_MESSAGE_QUEUE_SIZE}" sortBy = "0" id = "msgQueueTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "administrator.php?ctg=digests&">
                <tr class = "topTitle">
                    <td class = "topTitle" name="timestamp" width = "35%">{$smarty.const._DATE}</td>
                    <td name="recipients" class = "topTitle">{$smarty.const._RECIPIENTS}</td>
                    <td name="subject" class = "topTitle">{$smarty.const._SUBJECT}</td>
                    <td class = "topTitle noSort centerAling">{$smarty.const._OPERATIONS}</td>
                </tr>
          {foreach name = 'queue_message_list' key = 'key' item = 'queue_message' from = $T_QUEUE_MSGS}
          <tr class = "{cycle values = "oddRowColor, evenRowColor"}  {if $queue_message.timestamp && $queue_message.timestamp > $T_TIMESTAMP_NOW}deactivatedTableElement{/if}">
              <td >{if $queue_message.timestamp}#filter:timestamp_time-{$queue_message.timestamp}#{else}{$smarty.const._TOBESENTIMMEDIATELY}{/if}</td>
              <td >{$queue_message.recipients} {if isset($queue_message.recipients_count)}({$queue_message.recipients_count}){/if}</td>
              <td >{$queue_message.subject|eF_truncate:40}</td>
              <td class = "centerAlign">
                     <img class ="ajaxHandle" src = "images/16x16/mail.png" title = "{$smarty.const._SEND}" alt = "{$smarty.const._SEND}" onclick = "sendQueueMessage(this, '{$queue_message.id}');"/>
                        <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTOREMOVETHATNOTIFICATION}')) clearQueueMessage(this, '{$queue_message.id}');"/>
              </td>
          </tr>
          {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
          {/foreach}
     </table>
<!--/ajax:msgQueueTable-->
        {/capture}
        {capture name = 't_sent_messages_code'}
            <table style = "width:100%" class = "sortedTable">
                <tr class = "topTitle">
                    <td class = "topTitle" width = "15%">{$smarty.const._DATE}</td>
                    <td class = "topTitle">{$smarty.const._RECIPIENT}</td>
                    <td class = "topTitle">{$smarty.const._SUBJECT}</td>
                    <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                </tr>
          {foreach name = 'recent_messages_list' key = 'key' item = 'recent_message' from = $T_RECENTLY_SENT_NOTIFICATIONS}
          <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
              <td >#filter:timestamp_time-{$recent_message.timestamp}#</td>
              <td >{$recent_message.recipient}</td>
              <td >{$recent_message.subject}</td>
              <td class = "centerAlign">
                     <img class ="ajaxHandle" src = "images/16x16/mail.png" title = "{$smarty.const._RESEND}" alt = "{$smarty.const._RESEND}" onclick = "sendSentMessage(this, '{$recent_message.id}');"/>
                        <a title="{$smarty.const._PREVIEW}" href = "{$smarty.server.PHP_SELF}?ctg=digests&op=preview&sent_id={$recent_message.id}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', 3)" target = "POPUP_FRAME" style = "vertical-align:middle">
                <img src="images/16x16/search.png" class="handle" title="{$smarty.const._PREVIEW}" alt="{$smarty.const._PREVIEW}" />
               </a>
              </td>
          </tr>
          {foreachelse}
            <tr><td colspan=4>
            <table width = "100%">
                <tr><td class = "emptyCategory">{$smarty.const._NOMESSAGESFOUND}</td></tr>
            </table>
            </td></tr>
          {/foreach}
     </table>
        {/capture}
       {capture name = "t_configuration_form_code"}
   {$T_NOTIFICATION_VARIABLES_FORM.javascript}
            <form {$T_NOTIFICATION_VARIABLES_FORM.attributes}>
                {$T_NOTIFICATION_VARIABLES_FORM.hidden}
                <table style = "width:100%">
                    <tr><td class = "labelCell">{$T_NOTIFICATION_VARIABLES_FORM.notifications_use_cron.label}:&nbsp;</td>
                        <td class = "elementCell"><table><tr><td>{$T_NOTIFICATION_VARIABLES_FORM.notifications_use_cron.html}</td><td align="left"><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, 'use_cron_info', event)"><div id = 'use_cron_info' onclick = "eF_js_showHideDiv(this, 'use_cron_info', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:425px;height:175px;position:absolute;display:none"><table width="425px" height="175px" style="white-space: wrap;"><tr><td>{$smarty.const._USECRONINFO}</td></tr></table></div></td></tr></table></td></tr>
                    <tr><td class = "labelCell">{$T_NOTIFICATION_VARIABLES_FORM.notifications_pageloads.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_NOTIFICATION_VARIABLES_FORM.notifications_pageloads.html}</td></tr>
                    {if $T_NOTIFICATION_VARIABLES_FORM.notifications_pageloads.error}<tr><td></td><td class = "formError">{$T_NOTIFICATION_VARIABLES_FORM.notifications_pageloads.error}</td></tr>{/if}
                    <tr><td class = "labelCell">{$T_NOTIFICATION_VARIABLES_FORM.notifications_maximum_inter_time.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_NOTIFICATION_VARIABLES_FORM.notifications_maximum_inter_time.html}</td></tr>
                    {if $T_NOTIFICATION_VARIABLES_FORM.notifications_maximum_inter_time.error}<tr><td></td><td class = "formError">{$T_NOTIFICATION_VARIABLES_FORM.notifications_maximum_inter_time.error}</td></tr>{/if}
     <tr><td>&nbsp</td></tr>
                    <tr><td class = "labelCell">{$T_NOTIFICATION_VARIABLES_FORM.notifications_messages_per_time.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_NOTIFICATION_VARIABLES_FORM.notifications_messages_per_time.html}</td></tr>
                    {if $T_NOTIFICATION_VARIABLES_FORM.notifications_messages_per_time.error}<tr><td></td><td class = "formError">{$T_NOTIFICATION_VARIABLES_FORM.notifications_messages_per_time.error}</td></tr>{/if}
                    <tr><td class = "labelCell">{$T_NOTIFICATION_VARIABLES_FORM.notifications_max_sent_messages.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_NOTIFICATION_VARIABLES_FORM.notifications_max_sent_messages.html}</td></tr>
                    {if $T_NOTIFICATION_VARIABLES_FORM.notifications_max_sent_messages.error}<tr><td></td><td class = "formError">{$T_NOTIFICATION_VARIABLES_FORM.notifications_max_sent_messages.error}</td></tr>{/if}
                    <tr><td></td><td class = "submitCell">{$T_NOTIFICATION_VARIABLES_FORM.submit_variables.html}</td></tr>
                </table>
            </form>
       {/capture}
  {capture name="t_notifications"}
  <div class="tabber" >
   {eF_template_printBlock tabber = "registered" title = $smarty.const._REGISTERED data = $smarty.capture.t_notifications_code image = "32x32/notifications.png" options = $T_TABLE_OPTIONS}
   {eF_template_printBlock tabber = "messages_queue" title = $smarty.const._MESSAGESQUEUE data = $smarty.capture.t_queue_messages_code image = "32x32/notifications.png"}
   {eF_template_printBlock tabber = "recent_messages" title = $smarty.const._RECENTLYSENT data = $smarty.capture.t_sent_messages_code image = "32x32/notifications.png"}
   {eF_template_printBlock tabber = "config_tab" title = $smarty.const._CONFIGURATIONOPTIONS data = $smarty.capture.t_configuration_form_code image = "32x32/notifications.png"}
        </div>
        {/capture}
        {eF_template_printBlock title = $smarty.const._EMAILDIGESTS data = $smarty.capture.t_notifications image = '32x32/notifications.png' help = 'Notifications'}
 {/if}
{/if}
