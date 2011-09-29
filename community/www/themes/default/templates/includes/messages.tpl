
{capture name = "t_folders_code"}
 <table width="100%">
  {foreach name = "folders_loop" key = "id" item = "folder" from = $T_FOLDERS}
      <tr id = "div_folder_id_{$id}">
       <td>
              <span class = "counter">{$smarty.foreach.folders_loop.iteration}.</span>
              <a href = "{$smarty.server.PHP_SELF}?ctg=messages&folder={$id}" {if $id == $T_FOLDER}class = "selectedLink"{/if}>{$folder.name}&nbsp;({$folder.messages_num} {if $folder.messages_num == 1}{$smarty.const._MESSAGE}{else}{$smarty.const._MESSAGES}{/if}, {$folder.filesize}{$smarty.const._KB})</a>
          </td>
          <td>
          {if $smarty.foreach.folders_loop.iteration > 3}
     <a href = "{$smarty.server.PHP_SELF}?ctg=messages&folders=1&edit={$id}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 2)" target = "POPUP_FRAME"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
     <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSURETODELETEFOLDER}&quot;{$folder.name}&quot;{$smarty.const._ANDALLCONTENTS}')) deleteFolder(this, '{$id}');"/>
    {/if}
          </td>
      </tr>
      {assign var = "folders_options" value = $folders_options|cat:'<option value = "'|cat:$id|cat:'">'|cat:$folder.name|cat:'</option>'} {*This builds an <options> list containing the name of the available folders*}
  {/foreach}
  <tr><td colspan = "2">&nbsp;</td></tr>
  <tr><td colspan = "2">
   {$smarty.const._TOTAL}: {$T_TOTAL_MESSAGES} {if $T_TOTAL_MESSAGES == 1}{$smarty.const._MESSAGE}{else}{$smarty.const._MESSAGES}{/if}, {if $T_TOTAL_SIZE > $T_CONFIGURATION.pm_space*1024 && $T_CONFIGURATION.pm_space != ''}<span class="failure">{$T_TOTAL_SIZE}</span>{else}{$T_TOTAL_SIZE}{/if} {$smarty.const._KB}<br />
   {if $T_CONFIGURATION.pm_space != ''}{if $T_TOTAL_SIZE > $T_CONFIGURATION.pm_space*1024 && $T_CONFIGURATION.pm_space != ''}<span class="failure">{else}<span class="success">{/if}{$smarty.const._MAXIMUMPMUSAGESPACE} : {math equation="x*y" x=1024 y=$T_CONFIGURATION.pm_space} KB</span>{/if}
  </td></tr>
 </table>
{/capture}


{capture name = "moduleMessagesPage"}
 <tr><td class = "moduleCell">
 {if $smarty.get.folders}
     {capture name = 't_add_code'}
   {$T_ENTITY_FORM.javascript}
   <form {$T_ENTITY_FORM.attributes}>
       {$T_ENTITY_FORM.hidden}
       <table class = "formElements">
           <tr><td class = "labelCell">{$T_ENTITY_FORM.name.label}:&nbsp;</td>
               <td class = "elementCell">{$T_ENTITY_FORM.name.html}</td></tr>
           {if $T_ENTITY_FORM.name.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.name.error}</td></tr>{/if}
           <tr><td></td>
               <td class = "submitCell">{$T_ENTITY_FORM.submit.html}</td></tr>
       </table>
   </form>
  {if $T_MESSAGE_TYPE == 'success'}
     <script>
         //re = /\?/;
         parent.location = parent.location//!re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';
     </script>
  {/if}
  {/capture}

  {eF_template_printBlock title = $smarty.const._NEWFOLDER data = $smarty.capture.t_add_code image = '32x32/folders.png'}

 {elseif $smarty.get.add}
  {capture name = 't_recipients_code'}
         <script>
          var hiderecipients = '{$smarty.const._HIDERECIPIENTSCATEGORIES}';
          var showrecipients = '{$smarty.const._SHOWRECIPIENTSCATEGORIES}';
          var norecipients = '{$smarty.const._NORECIPIENTSHAVEBEENSELECTED}';
          var thefield = '{$smarty.const._THEFIELD}';
          var subject = '{$smarty.const._SUBJECT}';
          var ismandatory = '{$smarty.const._ISMANDATORY}';
          var enterprise = 0;



         </script>
            <table class = "statisticsSelectList">
                <tr><td class = "labelCell">{$smarty.const._RECIPIENTS}:</td>
                    <td class = "elementCell">
                        {*<input type = "text" id = "autocomplete" class = "autoCompleteTextBox"/>*}
                        {$T_ADD_MESSAGE_FORM.recipient.html}
                        <img id = "busy" src = "images/16x16/clock.png" style = "display:none;" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}"/>
                        <a href = "javascript:void(0);" onclick = "show_hide_additional_recipients()">
                         <img id = "arrow_down" src = "images/16x16/navigate_down.png" alt = "{$smarty.const._SHOWRECIPIENTSCATEGORIES}" title = "{$smarty.const._SHOWRECIPIENTSCATEGORIES}"/>
                        </a>
                        <div id = "autocomplete_choices" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            <tr>
              <td class = "labelCell">{$smarty.const._UNDISCLOSEDRECIPIENTS}:&nbsp;</td>
            <td class = "elementCell">{$T_ADD_MESSAGE_FORM.bcc.html}</td>
             </tr>
                <tr><td></td>
                    <td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}. {$smarty.const._SEPARATEMULTIPLEUSERS}</td>
             </tr>
            </table>

            <div id = "additional_recipients_categories" style="display:none;">
                <div>
                    <table>
                         {* Regular eFront selects *}
                         <tr style="display:none;"><td>{$T_ADD_MESSAGE_FORM.recipients.only_specific_users.html} </td><td>{$smarty.const._ONLYRECIPIENTSDEFINEDBELOW}</td></tr>
                         <tr {if $smarty.session.s_type != "administrator"}style="display:none;"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.active_users.html} </td><td>{$smarty.const._ALLACTIVESYSTEMUSERS}</td></tr>
       {* Available for all types*}
                         <tr {if !$T_COURSES}style = "display:none"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.specific_course.html}</td><td width="27%">{$smarty.const._USERSCONNECTEDTOSPECIFICCOURSE}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.specific_course.html}</td><td>{$T_ADD_MESSAGE_FORM.specific_course_completed.html}</td><td id="specific_course_completed_label" style="visibility:hidden">{$T_ADD_MESSAGE_FORM.specific_course_completed.label}</td></tr>
                         <tr {if !$T_LESSONS}style = "display:none"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.specific_lesson.html}</td><td>{$smarty.const._USERSCONNECTEDTOSPECIFICLESSON}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.lesson.html}</td></tr>
                         <tr {if !$T_LESSONS}style = "display:none"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.specific_lesson_professor.html}</td><td>{$smarty.const._PROFESSORSOFLESSON}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.professor.html}</td></tr>
                         {* Admin and supservisors *}
                         <tr {if !$T_FULL_ACCESS}style = "display:none"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.specific_type.html} </td><td>{$smarty.const._SPECIFICTYPEUSERS}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.user_type.html}</td></tr>
                         {* Groups: Available for all types*}
                         <tr><td>{$T_ADD_MESSAGE_FORM.recipients.specific_group.html} </td><td>{$smarty.const._USERSINGROUP}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.group_recipients.html}</td></tr>
                    </table>
                </div>
            </div>
  {/capture}
        {capture name = "t_new_message_code"}
            {if $smarty.post.preview}
                <table border = "0" cellpadding = "3" width = "100%">
                    <tr height = "30"><td valign = "top" width = "10%"><b>{$smarty.const._PREVIEW}</b>:</td>
                        <td class = "previewPane" colspan = "2">{$T_BODY_PREVIEW}</td></tr>
                    </tr>
                </table>
                <br/>
            {/if}
            <table class = "formElements">
                <tr><td class = "labelCell">{$smarty.const._SUBJECT}:&nbsp;</td>
                    <td class = "elementCell">{$T_ADD_MESSAGE_FORM.subject.html}&nbsp;<span class="formRequired">*</span></td></tr>
                <tr><td class = "labelCell">{$smarty.const._SENDASEMAILALSO}:&nbsp;</td>
                    <td class = "elementCell">{$T_ADD_MESSAGE_FORM.email.html}</td></tr>
                    {if $T_ADD_MESSAGE_FORM.email.error}<tr><td></td><td class = "formError">{$T_ADD_MESSAGE_FORM.email.error}</td></tr>{/if}
                <tr><td></td><td>
      <span>
       <img onclick = "toggledInstanceEditor = 'body';javascript:toggleEditor('body','simpleEditor');" class = "handle" style="vertical-align:middle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
       <a href = "javascript:void(0)" onclick = "toggledInstanceEditor = 'body';javascript:toggleEditor('body','simpleEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
      </span></td></tr>
    <tr><td class = "labelCell">{$smarty.const._BODY}:&nbsp;</td>
                    <td class = "elementCell">{$T_ADD_MESSAGE_FORM.body.html}</td></tr>
                    {if $T_ADD_MESSAGE_FORM.body.error}<tr><td></td><td class = "formError">{$T_ADD_MESSAGE_FORM.body.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._ATTACHMENTS}:&nbsp;</td>
                    <td class = "elementCell">{$T_ADD_MESSAGE_FORM.attachment.0.html}</td></tr>
                    {if $T_ADD_MESSAGE_FORM.attachment.0.error}<tr><td></td><td class = "formError">{$T_ADD_MESSAGE_FORM.attachment.0.error}</td></tr>{/if}
    <tr><td></td>
     <td class = "infoCell">{$smarty.const._FILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILE_SIZE}</b> {$smarty.const._KB}</td></tr>
                <tr><td></td>
                 <td class = "submitCell">{*{$T_ADD_MESSAGE_FORM.submit_preview_message.html}&nbsp;*}{$T_ADD_MESSAGE_FORM.submit_send_message.html}</td></tr>
            </table>
  {if $T_MESSAGE_TYPE == 'success'}
     <script>
         //re = /\?/;
         parent.location = parent.location//!re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';
     </script>
  {/if}
        {/capture}
        {$T_ADD_MESSAGE_FORM.javascript}
        <form {$T_ADD_MESSAGE_FORM.attributes} onSubmit = "return eF_js_checkRecipients()">
        {$T_ADD_MESSAGE_FORM.hidden}
            {eF_template_printBlock title = $smarty.const._RECIPIENTSSELECTION data = $smarty.capture.t_recipients_code image = '32x32/directory.png' help = 'Messages'}
            {eF_template_printBlock title = $smarty.const._MESSAGEBODY data = $smarty.capture.t_new_message_code image = '32x32/mail.png' help = 'Messages'}
        </form>
 {elseif $smarty.get.view}
  {capture name = "t_messagesbody_code"}
   <div class = "messagesTable" >
    <div class = "messageInfo">
     <div><span>{$smarty.const._SENT}:</span> #filter:timestamp_time-{$T_PERSONALMESSAGE.timestamp}#</div>
     <div><span>{$smarty.const._SENDER}:</span> #filter:login-{$T_PERSONALMESSAGE.sender}#</div>
     <div>
      <span>{$smarty.const._RECIPIENT}:</span>
      {if $T_PERSONALMESSAGE.bcc}
       {$smarty.const._UNDISCLOSEDRECIPIENTS}
      {elseif sizeof($T_PERSONALMESSAGE.recipient) <= 10}
       {$T_PERSONALMESSAGE.recipient|@implode:",&nbsp;"}
      {else}
      {strip}
       {foreach name = "recipients_list" item = "item" key = "key" from = $T_PERSONALMESSAGE.recipient}
        {if $smarty.foreach.recipients_list.iteration < 10}
         {$item},&nbsp;
        {elseif $smarty.foreach.recipients_list.iteration == 10}
         <a href = "javascript:void(0)" style = "" onclick = "Element.extend(this).hide();$('more_recipients').show()">{$T_PERSONALMESSAGE.recipient|@sizeof} more users</a><span id = "more_recipients" style = "font-weight:inherit;display:none">{$item}
        {elseif $smarty.foreach.recipients_list.iteration == $T_PERSONALMESSAGE.recipient|@sizeof}
         </div>
        {else}
         ,&nbsp;{$item}
        {/if}
       {/foreach}
      {/strip}
      {/if}
     </div>
    {if $T_ATTACHMENT}
     <div><span>{$smarty.const._ATTACHMENTS}:</span> <a href = "view_file.php?file={$T_ATTACHMENT.id}&action=download">{$T_ATTACHMENT.name}</a></div>
    {/if}
    </div>
    <div class = "messageBody">
     {$T_PERSONALMESSAGE.body}
    </div>
    <div class = "topTitle messageTools">
    {if $T_NEXT_MESSAGE}
     <a style = "float:right" href = "{$smarty.server.PHP_SELF}?ctg=messages&view={$T_NEXT_MESSAGE}" title = "{$smarty.const._NEXT} &raquo;">
      <img class = "handle" src = "images/16x16/navigate_right.png" title = "{$smarty.const._NEXT} &raquo;" alt = "{$smarty.const._NEXT} &raquo;" /></a>
    {/if}
    {if $T_PREVIOUS_MESSAGE}
     <a style = "float:right" href = "{$smarty.server.PHP_SELF}?ctg=messages&view={$T_PREVIOUS_MESSAGE}" title = "&laquo; {$smarty.const._PREVIOUS}">
      <img class = "handle" src = "images/16x16/navigate_left.png" title = "&laquo; {$smarty.const._PREVIOUS}" alt = "&laquo; {$smarty.const._PREVIOUS}" /></a>
    {/if}
    {if $_change_}
     {if $smarty.session.s_type != 'student' || $T_CONFIGURATION.disable_messages_student == 0}
      <a href = "{$smarty.server.PHP_SELF}?ctg=messages&add=1" title = "{$smarty.const._NEWMESSAGE}">
       <img class = "handle" src = "images/16x16/add.png" title = "{$smarty.const._NEWMESSAGE}" alt = "{$smarty.const._NEWMESSAGE}" /></a>
                     <a href = "{$smarty.server.PHP_SELF}?ctg=messages&add=1&reply={$T_PERSONALMESSAGE.id}" title = "{$smarty.const._REPLY}">
                      <img class = "handle" src = "images/16x16/mail.png" title = "{$smarty.const._REPLY}" alt = "{$smarty.const._REPLY}" ></a>
                     <a href = "{$smarty.server.PHP_SELF}?ctg=messages&add=1&forward={$T_PERSONALMESSAGE.id}" title = "{$smarty.const._FORWARD}">
                      <img class = "handle" src = "images/16x16/arrow_right.png" title = "{$smarty.const._FORWARD}" alt = "{$smarty.const._FORWARD}" ></a>
                 {/if}
    {/if}
                    <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSURETODELETEMESSAGE}')) deleteMessage(this, '{$T_PERSONALMESSAGE.id}');">
                    <img class = "ajaxHandle" src = "images/16x16/file_explorer.png" title = "{$smarty.const._MOVETOFOLDER}" alt = "{$smarty.const._MOVETOFOLDER}" onclick = "moveMessage(this, '{$T_PERSONALMESSAGE.id}')"/>
                    <select id = "target_message_folder">{$folders_options}</select>
    </div>
   </div>
  {/capture}
  {eF_template_printBlock title = $T_PERSONALMESSAGE.title data = $smarty.capture.t_messagesbody_code image = "32x32/mail.png"}
  {capture name = "moduleSideOperations"}
   <tr>
    <td id = "sideColumn">
    {eF_template_printBlock title = $smarty.const._SPACEUSAGE data = $smarty.capture.t_volume_code image = "32x32/status.png" }
    {eF_template_printBlock title = $smarty.const._FOLDERS data = $smarty.capture.t_folders_code image = "32x32/folders.png" options = $T_FOLDERS_OPTIONS}
    </td>
   </tr>
  {/capture}
 {else}
  {capture name = "t_messages_code"}
   <div class = "headerTools">
    {if $_change_}
     {if $smarty.session.s_type != 'student' || $T_CONFIGURATION.disable_messages_student == 0}
     <span>
      <img src = "images/16x16/add.png" title = "{$smarty.const._NEWMESSAGE}" alt = "{$smarty.const._NEWMESSAGE}" />
      <a href = "{$smarty.server.PHP_SELF}?ctg=messages&add=1" title = "{$smarty.const._NEWMESSAGE}">{$smarty.const._NEWMESSAGE}</a>
     </span>
     {/if}
    {/if}
   </div>
<!--ajax:messagesTable-->
            <table class = "sortedTable" width = "100%" sortBy = "0" useAjax = "1" id = "messagesTable" url="{$smarty.server.PHP_SELF}?ctg=messages&folder={$T_FOLDER}&">
                <tr class = "defaultRowHeight">
                {if !isset($smarty.get.minimal_view)}
                    <td class = "topTitle centerAlign" name = "priority" style = "width:10%">{$smarty.const._PRIORITY}</td>
                {/if}
                    <td class = "topTitle" name = "title" style = "width:40%">{$smarty.const._SUBJECT}</td>
                {if $T_SENT_FOLDER == $smarty.get.folder}
                 <td class = "topTitle" name="recipient" style = "width:20%">{$smarty.const._TOFORUM}</td>
                {else}
                    <td class = "topTitle" name = "sender" style = "width:20%">{$smarty.const._FROM}</td>
                {/if}
                    <td class = "topTitle" name="timestamp" style = "width:20%">{$smarty.const._DATE}</td>
                {if !isset($smarty.get.minimal_view)}
                    <td class = "topTitle centerAlign noSort" style = "width:10%">{$smarty.const._OPERATIONS}</td>
                {/if}
                 </tr>
   {foreach name = "messages_list" item = "message" key = "key" from = $T_MESSAGES}
                 <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$message.viewed}unreadMessage{/if}" id="row_of_message_{$message.id}">
                    {* Set email priority *}
                {if !isset($smarty.get.minimal_view)}
                     <td class = "centerAlign"><span style = "display:none">{$message.priority}</span> {*For sorting purposes*}
                 {if !$message.priority}
                             <img class = "ajaxHandle" src = "images/16x16/flag_green.png" alt = "{$smarty.const._NORMAL}" title = "{$smarty.const._SETHIGHPRIORITY}" onclick = "flag_unflag(this, '{$message.id}')"/>
                 {else}
                             <img class = "ajaxHandle" src = "images/16x16/flag_red.png" alt = "{$smarty.const._HIGH}" title = "{$smarty.const._SETNORMALPRIORITY}" onclick = "flag_unflag(this, '{$message.id}')"/>
                 {/if}
                    </td>
                {/if}
                    <td>
                {if $message.attachments}
      <img class = "ajaxHandle" src = "images/16x16/attachment.png" alt = "{$smarty.const._ATTACHMENT}" title = "{$smarty.const._ATTACHMENT}" onclick = "downloadAttachment(this, '{$message.id}')"/>
    {/if}
                        <a href = "{$smarty.server.PHP_SELF}?ctg=messages&view={$message.id}">{$message.title}</a>
                    </td>
                {if $T_SENT_FOLDER == $smarty.get.folder}
                 <td>{if $message.bcc}{$smarty.const._UNDISCLOSEDRECIPIENTS}{else}{$message.recipient|eF_truncate:30}{/if}</td>
                {else}
                    <td>#filter:login-{$message.sender}#</td>
                {/if}
                    <td><span style = "display:none">{$message.timestamp}</span>#filter:timestamp_time_nosec-{$message.timestamp}#</td>
                {if !isset($smarty.get.minimal_view)}
                  <td class = "centerAlign" >
{*
                 {if !isset($smarty.get.minimal_view)}
                      <img class = "ajaxHandle" id="{$message.id}" src = "images/16x16/file_explorer.png" alt="{$smarty.const._DRAGTOMOVEMAILTOFOLDER}" title="{$smarty.const._DRAGTOMOVEMAILTOFOLDER}"/>
                 {/if}
*}
      <img class = "ajaxHandle" src = "images/16x16/error_delete.png" onclick = "if (confirm('{$smarty.const._AREYOUSURETODELETEMESSAGE}')) deleteMessage(this, '{$message.id}');" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}"/>
      <input style = "vertical-align:middle" class = "inputCheckbox" type = "checkbox" id = "check_{$message.id}" value = "{$message.id}"/>
                    </td>
                {/if}
                </tr>
{*
                {if $smarty.const.MSIE_BROWSER == 1}
                    <img style="display:none" src="images/16x16/question_type_free_text.png" onLoad="javascript:_current_folder = '{$T_FOLDER}';new Draggable('{$message.id}', {literal}{revert:true}{/literal});" />
                {else}
                    <script type="text/javascript">_current_folder = "{$T_FOLDER}";new Draggable('{$message.id}', {literal}{revert:true}{/literal})</script>
                {/if}
*}
   {foreachelse}
                <tr class = "oddRowColor defaultRowHeight"><td colspan = "6" class = "emptyCategory">{$smarty.const._NOMESSAGESINFOLDER}</td></tr>
   {/foreach}
           </table>
<!--/ajax:messagesTable-->
          <div class = "horizontalSeparatorAbove">
             <span style = "vertical-align:middle">{$smarty.const._WITHSELECTED}:</span>
                <img src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETESELECTED}" alt = "{$smarty.const._DELETESELECTED}" class = "ajaxHandle" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteSelectedMessages(this)">
             </div>
  {/capture}
  {eF_template_printBlock title = $smarty.const._PERSONALMESSAGES data = $smarty.capture.t_messages_code image = "32x32/mailbox.png" help = 'Messages'}
  {capture name = "moduleSideOperations"}
   <tr>
    <td id = "sideColumn">
    {eF_template_printBlock title = $smarty.const._SPACEUSAGE data = $smarty.capture.t_volume_code image = "32x32/status.png"}
    {eF_template_printBlock title = $smarty.const._FOLDERS data = $smarty.capture.t_folders_code image = "32x32/folders.png" options = $T_FOLDERS_OPTIONS}
    </td>
   </tr>
  {/capture}
 {/if}
 </td></tr>
{/capture}
