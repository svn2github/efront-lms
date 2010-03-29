{assign var = "current_forum" value = $smarty.get.forum}

{if !$T_CURRENT_USER->coreAccess.forum || $T_CURRENT_USER->coreAccess.forum == 'change'}
 {assign var = "_change_" value = 1}
{/if}
 {capture name = "moduleForum"}
     <tr><td class = "moduleCell">

{if $smarty.get.type == 'forum' && ($smarty.get.add || $smarty.get.edit)}
 {capture name = 't_add_forum_code'}
     {$T_ENTITY_FORM.javascript}
     <form {$T_ENTITY_FORM.attributes}>
     {$T_ENTITY_FORM.hidden}
         <table class = "formElements">
             <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.title.html}</td></tr>
                 {if $T_ENTITY_FORM.title.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.title.error}</td></tr>{/if}
     {if !$smarty.get.forum_id}
             <tr><td class = "labelCell">{$smarty.const._ACCESSIBLEBYUSERSOFLESSON}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.lessons_ID.html}</td></tr>
                 {if $T_ENTITY_FORM.lessons_ID.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.lessons_ID.error}</td></tr>{/if}
     {/if}
  {if $smarty.session.s_type != 'student'}
             <tr><td class = "labelCell">{$T_ENTITY_FORM.status.label}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.status.html}</td></tr>
                 {if $T_ENTITY_FORM.status.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.status.error}</td></tr>{/if}
     {/if}

             <tr><td class = "labelCell">{$smarty.const._COMMENTS}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.comments.html}</td></tr>
                 {if $T_ENTITY_FORM.comments.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.comments.error}</td></tr>{/if}
             <tr><td colspan = "2">&nbsp;</td></tr>
             <tr><td></td><td class = "submitCell">{$T_ENTITY_FORM.submit_add_forum.html}</td></tr>
         </table>
     </form>
 {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location;</script>
 {/if}
 {/capture}
 {eF_template_printBlock title = $smarty.const._FORUMPROPERTIES data = $smarty.capture.t_add_forum_code image = '32x32/forum.png'}
{elseif $smarty.get.type == 'topic' && ($smarty.get.add || $smarty.get.edit)}
 {capture name = 't_add_topic_code'}
     {$T_ENTITY_FORM.javascript}
     <form {$T_ENTITY_FORM.attributes}>
     {$T_ENTITY_FORM.hidden}
         <table class = "formElements" >
             <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.title.html}</td></tr>
                 {if $T_ENTITY_FORM.title.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.title.error}</td></tr>{/if}
         {if !$_student_}
             <tr><td class = "labelCell">{$smarty.const._STATUS}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.status.html}</td></tr>
                 {if $T_ENTITY_FORM.status.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.status.error}</td></tr>{/if}
         {/if}
 {if !$smarty.get.edit}
             <tr><td class = "labelCell">{$smarty.const._MESSAGE}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.message.html}</td></tr>
                 {if $T_ENTITY_FORM.message.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.message.error}</td></tr>{/if}
 {/if}
             <tr><td></td><td class = "submitCell">{$T_ENTITY_FORM.submit_add_topic.html}</td></tr>
         </table>
     </form>
 {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location;</script>
 {/if}
 {/capture}
 {eF_template_printBlock title = $smarty.const._TOPICPROPERTIES data = $smarty.capture.t_add_topic_code image = '32x32/forum.png'}
{elseif $smarty.get.type == 'poll' && ($smarty.get.add || $smarty.get.edit)}
 {capture name = 't_add_poll_code'}
  <script>var twooptionsminimum = '{$smarty.const._TWOOPTIONSATMINIMUMREQUIRED}'; var removechoice= '{$smarty.const._REMOVECHOICE}';</script>
     {$T_ENTITY_FORM.javascript}
     <form {$T_ENTITY_FORM.attributes}>
     {$T_ENTITY_FORM.hidden}
         <table class = "formElements">
             <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.poll_subject.html}</td></tr>
                 {if $T_ENTITY_FORM.poll_subject.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.poll_subject.error}</td></tr>{/if}
             <tr><td class = "labelCell">{$smarty.const._BODY}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.poll_text.html}</td></tr>
                 {if $T_ENTITY_FORM.poll_text.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.poll_text.error}</td></tr>{/if}
             <tr><td class = "labelCell">{$smarty.const._AVAILABLEFROM}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.from.html}</td></tr>
                 {if $T_ENTITY_FORM.from.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.from.error}</td></tr>{/if}
             <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.to.html}</td></tr>
                 {if $T_ENTITY_FORM.to.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.to.error}</td></tr>{/if}
             <tr><td class = "labelCell">{$smarty.const._INSERTMULTIPLEQUESTIONS}:</td>
                 <td><table>
     {foreach name = 'multiple_one_list' key = key item = item from = $T_ENTITY_FORM.options}
                         <tr><td>{$item.html}</td>
         {if $smarty.foreach.multiple_one_list.iteration > 2} {*The if smarty.iteration is put here, so that the user cannot remove the first 2 rows *}
                             <td><a href = "javascript:void(0)" onclick = "removeImgNode(this, 'options')">
                                     <img src = "images/16x16/error_delete.png" border = "no" alt = "{$smarty.const._REMOVECHOICE}" title = "{$smarty.const._REMOVECHOICE}" />
                                 </a></td>
         {/if}
                         </tr>
     {/foreach}
                         <tr id = "last_node"></tr>
                     </table>
                 </td></tr>
             <tr><td class = "labelCell">
                     <a href = "javascript:void(0)" onclick = "addAdditionalChoice()"><img src = "images/16x16/add.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
                 </td><td>
                     <a href = "javascript:void(0)" onclick = "addAdditionalChoice()">{$smarty.const._ADDOPTION}</a>
                 </td></tr>
             <tr><td colspan = "2">&nbsp;</td></tr>
             <tr><td></td><td class = "submitCell">{$T_ENTITY_FORM.submit_add_poll.html}</td></tr>
         </table>
     </form>
 {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location;</script>
 {/if}
 {/capture}
 {eF_template_printBlock title = $smarty.const._POLLPROPERTIES data = $smarty.capture.t_add_poll_code image = '32x32/forum.png'}
{elseif $smarty.get.type == 'message' && ($smarty.get.add || $smarty.get.edit)}
 {capture name = 't_add_message_code'}
     {$T_ENTITY_FORM.javascript}
     <form {$T_ENTITY_FORM.attributes}>
     {$T_ENTITY_FORM.hidden}
         <table class = "formElements" style = "width:99%">
             <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.title.html}</td></tr>
                 {if $T_ENTITY_FORM.title.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.title.error}</td></tr>{/if}
             <tr><td class = "labelCell">{$smarty.const._BODY}:&nbsp;</td>
                 <td class = "elementCell">{$T_ENTITY_FORM.body.html}</td></tr>
                 {if $T_ENTITY_FORM.body.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.body.error}</td></tr>{/if}
             <tr><td colspan = "2">&nbsp;</td></tr>
             <tr><td></td><td class = "submitCell">{$T_ENTITY_FORM.submit_add_message.html}</td></tr>
         </table>
     </form>
 {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location;</script>
 {/if}
 {/capture}
 {eF_template_printBlock title = $smarty.const._MESSAGEPROPERTIES data = $smarty.capture.t_add_message_code image = '32x32/forum.png'}
{elseif $smarty.get.config}
{capture name = 't_configuration_panel_code'}
 {$T_CONFIGURATION_FORM.javascript}
 <form {$T_CONFIGURATION_FORM.attributes}>
 {$T_CONFIGURATION_FORM.hidden}
     <table class = "formElements">
         <tr><td class = "labelCell">{$smarty.const._ALLOWHTMLFPM}:&nbsp;</td>
             <td class = "elementCell">{$T_CONFIGURATION_FORM.allow_html.html}</td></tr>
             {if $T_CONFIGURATION_FORM.allow_html.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.allow_html.error}</td></tr>{/if}
         <tr><td class = "labelCell">{$smarty.const._ACTIVATEPOLLS}:&nbsp;</td>
             <td class = "elementCell">{$T_CONFIGURATION_FORM.polls.html}</td></tr>
             {if $T_CONFIGURATION_FORM.polls.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.polls.error}</td></tr>{/if}
 {* <tr><td class = "labelCell">{$smarty.const._ALLOWATTACHMENTSINF}:&nbsp;</td>
             <td class = "elementCell">{$T_CONFIGURATION_FORM.forum_attachments.html}</td></tr>
             {if $T_CONFIGURATION_FORM.forum_attachments.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.forum_attachments.error}</td></tr>{/if}
 *}
         <tr><td class = "labelCell">{$smarty.const._USERSMAYADDFORUMS}:&nbsp;</td>
             <td class = "elementCell">{$T_CONFIGURATION_FORM.students_add_forums.html}</td></tr>
             {if $T_CONFIGURATION_FORM.students_add_forums.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.students_add_forums.error}</td></tr>{/if}
 {* <tr><td class = "labelCell">{$smarty.const._PMQUOTA}:&nbsp;</td>
             <td class = "elementCell">{$T_CONFIGURATION_FORM.pm_quota.html}</td></tr>
         <tr><td></td><td class = "infoCell">{$smarty.const._BLANKFORUNLIMITED}</td></tr>
             {if $T_CONFIGURATION_FORM.pm_quota.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.pm_quota.error}</td></tr>{/if}
         <tr><td class = "labelCell">{$smarty.const._PMATTACHMENTSQUOTA}:&nbsp;</td>
             <td class = "elementCell">{$T_CONFIGURATION_FORM.pm_attach_quota.html}</td></tr>
         <tr><td></td><td class = "infoCell">{$smarty.const._BLANKFORUNLIMITED}</td></tr>
             {if $T_CONFIGURATION_FORM.pm_attach_quota.error}<tr><td></td><td class = "formError">{$T_CONFIGURATION_FORM.pm_attach_quota.error}</td></tr>{/if}
 *}
   <tr><td colspan = "2">&nbsp;</td></tr>
             <td></td><td class = "submitCell">{$T_CONFIGURATION_FORM.submit_settings.html}</td></tr>
     </table>
 </form>
{/capture}
{eF_template_printBlock title = $smarty.const._FORUMCONFIGURATIONPANEL data = $smarty.capture.t_configuration_panel_code image = '32x32/edit.png'}

{else}
  {if $smarty.get.topic}
         {capture name = 't_topic_code'}
                {if $_change_}
                <div class = "headerTools">
                 <span>
                  <img src = "images/16x16/add.png" alt = "{$smarty.const._NEWMESSAGE}" title = "{$smarty.const._NEWMESSAGE}"/>
                    {if $T_TOPIC.status == '2'}
                  <a href = "javascript:void(0)" onclick = "alert('{$smarty.const._NONEWMESSAGELOCKED}')" class = "inactiveLink" >{$smarty.const._NEWMESSAGE}</a>
                    {else}
                  <a href = "{$smarty.server.PHP_SELF}?ctg=forum&add=1&type=message&topic_id={$smarty.get.topic}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._NEWMESSAGE}', 1)" target = "POPUP_FRAME" >{$smarty.const._NEWMESSAGE}</a>
                    {/if}
     </span>
    </div>
                {/if}
    <table class = "forumMessageTable">
                {section name = 'messages_list' loop = $T_POSTS}
                    {assign var = "message_user" value = $T_POSTS[messages_list].users_LOGIN}
     <tr class = "{cycle values = "oddRowColorNoHover, evenRowColorNoHover"}">
                     <td>
                      <div class = "blockHeader">{$T_POSTS[messages_list].title}</div>
                      <div class = "forumMessageInfo">{$smarty.const._POSTEDBY}<span> #filter:user_loginNoIcon-{$T_POSTS[messages_list].users_LOGIN}# </span>{$smarty.const._ON} #filter:timestamp_time-{$T_POSTS[messages_list].timestamp}# {if $T_POSTS[messages_list].replyto}{$smarty.const._INREPLYTO}: <a href = "{$smarty.server.PHP_SELF}?ctg=forum&topic={$smarty.get.topic}&view_message={$T_POSTS[messages_list].replyto}#{$T_POSTS[messages_list].replyto}">{$T_POSTS[messages_list].reply_title}</a>{/if}</div>
                      <p>{$T_POSTS[messages_list].body}</p>
                  {if $_change_}
                      <div class = "forumMessageTools">
                {if $T_TOPIC.status != '2'}
                          <a href = "{$smarty.server.PHP_SELF}?ctg=forum&add=1&type=message&topic_id={$smarty.get.topic}&replyto={$T_POSTS[messages_list].id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._REPLY}', 2)"><img class = "handle" src = "images/16x16/message.png" title = "{$smarty.const._REPLY}" alt = "{$smarty.const._REPLY}"/></a>
                          <a href = "{$smarty.server.PHP_SELF}?ctg=forum&add=1&type=message&topic_id={$smarty.get.topic}&replyto={$T_POSTS[messages_list].id}&quote=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._REPLYWITHQUOTE}', 2)"><img class = "handle" src = "images/16x16/forums.png" title = "{$smarty.const._REPLYWITHQUOTE}" alt = "{$smarty.const._REPLYWITHQUOTE}"/></a>
                   {/if}
                   {if $smarty.session.s_type == 'administrator' || $smarty.session.s_login == $T_POSTS[messages_list].users_LOGIN}
                          <a href = "{$smarty.server.PHP_SELF}?ctg=forum&edit={$T_POSTS[messages_list].id}&type=message&topic_id={$smarty.get.topic}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 2)" class = "editLink"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" /></a>
                          <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWNATTODELETEMESSAGE} {$T_POSTS[messages_list].title}')) deleteForumMessage(this, '{$T_POSTS[messages_list].id}', 'message')"/>
                   {/if}
                      </div>
                  {/if}
                     </td>
                     <td class = "forumMessageCreator">
                      <div><img src = {if $T_POSTS[messages_list].avatar}"view_file.php?file={$T_POSTS[messages_list].avatar}"{else}"images/avatars/system_avatars/unknown_small.png"{/if} title = "{$T_POSTS[messages_list].users_LOGIN}" alt = "{$T_POSTS[messages_list].users_LOGIN}"/></div>
                         <div>#filter:user_loginNoIcon-{$T_POSTS[messages_list].users_LOGIN}#</div>
       {assign var = "current_userrole" value = $T_POSTS[messages_list].user_type}
                         <div>{$smarty.const._POSITION}: {$T_USERROLES.$current_userrole}</div>
                         <div>{$smarty.const._JOINED}: #filter:timestamp-{$T_POSTS[messages_list].timestamp}#</div>
                         <div>{$smarty.const._POSTS}: {$T_USER_POSTS.$message_user}</div>
                     </td></tr>
    {sectionelse}
                 <tr><td colspan = "8" class = "{cycle values = "oddRowColor, evenRowColor"}">{$smarty.const._NOMESSAGESFOUNDINTHISTOPIC}</td></tr>
             {/section}
          </table>
   {/capture}
         {eF_template_printBlock title = $smarty.const._TOPICS data = $smarty.capture.t_topic_code image = '32x32/forum.png'}
  {elseif $smarty.get.poll}
   {capture name = 't_poll_code'}
       {if $T_ACTION == 'view' || !$T_POLL.isopen}
           <table>
               <tr><td class = "blockHeader" colspan = "100%">{$T_POLL.title}</td></tr>
               <tr><td>&nbsp;</td></tr>
               <tr><td style = "text-align:left" colspan = "100%"><b>{$T_POLL.question}</b></td></tr>
               <tr><td>&nbsp;</td></tr>
               {section name = 'votes_list' loop = $T_POLL_VOTES}
                   <tr><td style = "text-align:left" width="20%">{$T_POLL_VOTES[votes_list].text}</td>
                   <td style="text-align=left" width="30%">
                       <img src="images/others/bar.jpg" width="{$T_POLL_VOTES[votes_list].width}" height="15"/>
                   </td>
                   <td style="text-align=left">{$T_POLL_VOTES[votes_list].perc*100}% </td>
                   </tr>
               {/section}
           </table>
       {else}
           {$T_POLL_FORM.javascript}
           <form {$T_POLL_FORM.attributes}>
           {$T_POLL_FORM.hidden}
           <table class = "formElements" style = "width:100%">
               <tr><td class = "blockHeader" >{$T_POLL.title}</td></tr>
               <tr><td class = "">{$T_POLL.question}</td></tr>
               <tr><td class = "elementCell">{$T_POLL_FORM.options.html}</td></tr>
               <tr><td class = "submitCell">{$T_POLL_FORM.submit_poll.html}</td></tr>
               <tr><td>{$smarty.const._TOTALVOTES}: {$T_POLL_TOTALVOTES}</td></tr>
               <tr><td><a href="{$smarty.server.PHP_SELF}?ctg=forum&poll={$smarty.get.poll}&action=view">{$smarty.const._VIEWRESULTS}</a></td></tr>
           </table>
           </form>
       {/if}
   {/capture}
   {eF_template_printBlock title = $smarty.const._POLL data = $smarty.capture.t_poll_code image = '32x32/polls.png'}
 {else}
  {capture name = 't_forums_code'}
      {assign var = "current_forum" value = $smarty.get.forum}
   <table>
       <tr><td class = "blockHeader">{$T_FORUMS.$current_forum.title}</td></tr>
       {if $T_FORUMS.$current_forum.comments}<tr><td class = "infoCell" style = "padding-bottom:5px">{$T_FORUMS.$current_forum.comments}</td></tr>{/if}
   </table>
   {if $_change_}
    <div class = "headerTools">
     <span>
             {if $smarty.get.forum}
     {if $T_FORUMS.$current_forum.status != '1'}
      <img src = "images/16x16/add.png" alt = "{$smarty.const._NEWTOPIC}" title = "{$smarty.const._NEWTOPIC}"/>
      <a href = "javascript:void(0)" onclick = "alert('{$smarty.const._NONEWMESSAGELOCKED}')" class = "inactiveLink" >{$smarty.const._NEWTOPIC}</a>
                 {else}
      <img src = "images/16x16/add.png" alt = "{$smarty.const._NEWTOPIC}" title = "{$smarty.const._NEWTOPIC}"/>
      <a href = "{$smarty.server.PHP_SELF}?ctg=forum&add=1&type=topic&forum_id={$T_PARENT_FORUM}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._NEWTOPIC}', 1)" target = "POPUP_FRAME">{$smarty.const._NEWTOPIC}</a>
     {/if}
     </span>
     {if !isset($T_FORUM_CONFIG.polls) || $T_FORUM_CONFIG.polls}
     <span>
      {if $T_FORUMS.$current_forum.status != '1'}
      <img src = "images/16x16/add.png" alt = "{$smarty.const._NEWPOLL}" title = "{$smarty.const._NEWPOLL}"/>
            <a href = "javascript:void(0)" onclick = "alert('{$smarty.const._NONEWPOLLLOCKED}')" class = "inactiveLink" >{$smarty.const._NEWPOLL}</a>
      {else}
      <img src = "images/16x16/add.png" alt = "{$smarty.const._NEWPOLL}" title = "{$smarty.const._NEWPOLL}"/>
            <a href = "{$smarty.server.PHP_SELF}?ctg=forum&add=1&type=poll&forum_id={$T_PARENT_FORUM}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._NEWPOLL}', 2)" target = "POPUP_FRAME">{$smarty.const._NEWPOLL}</a>
      {/if}
     </span>
     {/if}
             {/if}
           {if $smarty.session.s_type == 'administrator'}
                    <span>
                        <img src = "images/16x16/edit.png" title = "{$smarty.const._FORUMCONFIGURATIONPANEL}" alt = "{$smarty.const._FORUMCONFIGURATIONPANEL}"/ >
                        <a href = "{$smarty.server.PHP_SELF}?ctg=forum&config=1&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._FORUMCONFIGURATIONPANEL}', 1)" target = "POPUP_FRAME">{$smarty.const._FORUMCONFIGURATIONPANEL}</a>
                    </span>
            {/if}

          </div>
   {/if}

   {if $smarty.get.forum}
          {capture name = 'subforums_list_code'}
              {foreach name = "subforums_list" item = "subforum" key = "key2" from = $T_FORUMS}
                  {if $subforum.parent_id == $T_FORUMS.$current_forum.id && $subforum.status != '3'}
                      {assign var = "has_subforums" value = true}
                                         <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
                                             <td>
             <img class = "forumIcon" src = "images/32x32/forum.png" alt = "{$smarty.const._FORUM}" title = "{$smarty.const._FORUM}"/>
                                                 <div>
                                                  <a href = "{$smarty.server.PHP_SELF}?ctg=forum&forum={$subforum.id}" >{$subforum.title}</a>
                                                  <p>{$subforum.comments}</p>
                                                 </div>
                                             </td>
            <td>{$subforum.topics} {$smarty.const._TOPICS}, {$subforum.messages} {$smarty.const._MESSAGES}{if $subforum.polls !=0}, {$subforum.polls} {$smarty.const._POLLS}{/if}</td>
            <td>
                             {if $subforum.last_post}
                                             #filter:timestamp_time-{$subforum.last_post.timestamp}#
                                             <br/> {$smarty.const._BY} #filter:user_loginNoIcon-{$subforum.last_post.users_LOGIN}#
                                             <a href = "{$smarty.server.PHP_SELF}?ctg=forum&topic={$subforum.last_post.f_topics_ID}&view_message={$subforum.last_post.id}">&raquo;</a>
                             {else}
                                             {$smarty.const._NOFORUMPOSTSYET}
                             {/if}
                                             </td>
            <td class = "centerAlign">
                             {if $subforum.status == '2'}
                <img src = "images/16x16/lock.png" alt = "{$smarty.const._LOCKED}" title = "{$smarty.const._LOCKED}"/><span style = "display:none">{$smarty.const._LOCKED}</span> {*span is used for sorting*}
                             {elseif $subforum.status == '3'}
                <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._INVISIBLE}" title = "{$smarty.const._INVISIBLE}"/><span style = "display:none">{$smarty.const._INVISIBLE}</span>
                             {else}
                <img src = "images/16x16/unlocked.png" alt = "{$smarty.const._PUBLIC}" title = "{$smarty.const._PUBLIC}"/><span style = "display:none">{$smarty.const._PUBLIC}</span>
                             {/if}
                                             </td>
      {if ($_change_) && !$_student_}
            <td class = "centerAlign">
                         {if $_admin_ || ($_professor_ && $smarty.session.s_login == $subforum.users_LOGIN)}
                               <a href = "{$smarty.server.PHP_SELF}?ctg=forum&edit={$subforum.id}&type=forum&parent_forum_id={$T_PARENT_FORUM}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 1)" target = "POPUP_FRAME"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                  <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "deleteForumEntity(this, '{$subforum.id}', 'forum');"/>
                         {/if}
                                             </td>
                     {/if}
                                         </tr>
                         {/if}
                     {foreachelse}
                                         <tr class = "oddRowColor defaultRowHeight"><td colspan = "4" class = "emptyCategory">{$smarty.const._NOSUBFORUMSFOUND}</td></tr>
                     {/foreach}
                 {/capture}
                 {if $has_subforums}
                                     <table class = "forumTable">
                                         <tr>
                                             <td class = "topTitle" style = "width:50%">{$smarty.const._SUBFORUMS}</td>
                                             <td class = "topTitle" style = "width:20%">{$smarty.const._ACTIVITY}</td>
                                             <td class = "topTitle" style = "width:20%">{$smarty.const._LASTPOST}</td>

           {if $smarty.session.s_type != 'student'}
            <td class = "topTitle centerAlign" style = "width:5%">{$smarty.const._STATUS}</td>
                                         {else}
            <td class = "topTitle centerAlign" style = "width:5%">{$smarty.const._STATUS}</td>
           {/if}
           <td class = "topTitle centerAlign noSort" style = "width:5%">{if !$_student_}{$smarty.const._OPERATIONS}{/if}</td>
                                         </tr>
                                         {$smarty.capture.subforums_list_code}
                                     </table>
                                     <br/>
                 {/if}
                 {if $T_FORUM_POLLS}
                                     {*Polls list*}
                                     <table class = "forumTable">
                                         <tr>
                                             <td class = "topTitle firstColumn">{$smarty.const._POLLS}</td>
                                             <td class = "topTitle secondColumn">{$smarty.const._AUTHOR}</td>
                                             <td class = "topTitle thirdColumn" >{$smarty.const._ISVALID}</td>
                                             <td class = "topTitle toolsColumn" >{$smarty.const._VOTES}</td>
                                          <td class = "topTitle toolsColumn noSort">
                                         {if $_change_}
                                          {$smarty.const._OPERATIONS}
                                         {/if}
                                          </td>
                                         </tr>
                     {foreach name = "polls_list" item = "poll" key = "key2" from = $T_FORUM_POLLS}
                                         <tr class = "{cycle name = "polls" values = "oddRowColor,evenRowColor"}">
                                             <td>
                                              <img class = "forumIcon" src = "images/32x32/polls.png" alt = "{$smarty.const._POLL}" title = "{$smarty.const._POLL}"/>
                                                 <div>
                                                  <a href = "{$smarty.server.PHP_SELF}?ctg=forum&poll={$poll.id}" class = "smallHeader" style = "white-space:normal">{$poll.title}</a>
                                                  <p>{$poll.question|eF_truncate:50}</p>
                                                 </div>
                                             </td>
                                             <td>#filter:user_loginNoIcon-{$poll.users_LOGIN}#</td>
                                             <td>{$smarty.const._FROM} <b>#filter:timestamp_time-{$poll.timestamp_start}#</b> <br/>{$smarty.const._TO} <b>#filter:timestamp_time-{$poll.timestamp_end}#</b></td>
                                             <td class = "centerAlign">{$poll.votes}</td>
      {if $_change_}
                                             <td class = "centerAlign">
                         {if $smarty.session.s_type == 'administrator' || $smarty.session.s_login == $poll.users_LOGIN}
                         <a href = "{$smarty.server.PHP_SELF}?ctg=forum&type=poll&edit={$poll.id}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 2)" class = "editLink" target = "POPUP_FRAME"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                         <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if(confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteForumEntity(this, '{$poll.id}', 'poll')"/>
                         {/if}
                                             </td>
                     {/if}
                                         </tr>
                     {foreachelse}
                                         <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NOPOLLSSFOUNDINTHISFORUM}</td></tr>
                     {/foreach}
                                     </table>
                                     <br/>
                 {/if}
                 {if $T_FORUM_TOPICS || (!$T_HAS_SUBFORUMS && !$T_FORUM_POLLS)} {*Do not display topics block, unless nothing else exists*}
                                     <table class = "forumTable">
                                         <tr>
                                             <td class = "topTitle firstColumn">{$smarty.const._TOPICS}</td>
                                             <td class = "topTitle secondColumn">{$smarty.const._MESSAGES}</td>
                                             <td class = "topTitle thirdColumn">{$smarty.const._LASTPOST}</td>
                                             <td class = "topTitle toolsColumn">{$smarty.const._STATUS}</td>
                                             <td class = "topTitle toolsColumn noSort">{if $_change_ && !$_student_}{$smarty.const._OPERATIONS}{/if}</td>
                                         </tr>
      {assign var = "novisible" value = 0}
                     {foreach name = "topics_list" item = "topic" key = "key2" from = $T_FORUM_TOPICS}
                         {if $smarty.session.s_type == 'administrator' || $topic.status != '3' || $topic.users_LOGIN == $smarty.session.s_login}
                                         <tr class = "{cycle name = "topics" values = "oddRowColor,evenRowColor"}">
                                             <td>
                                              <img class = "forumIcon" src = "images/32x32/message.png" alt = "{$smarty.const._TOPIC}" title = "{$smarty.const._TOPIC}"/>
                                              <div>
                                                  <a href = "{$smarty.server.PHP_SELF}?ctg=forum&topic={$topic.id}" class = "smallHeader" style = "white-space:normal">{$topic.title}</a>
                                                  <p>{$topic.first_message|eF_truncate:50}</p>
                                                 </div>
                                             </td>
                                             <td>{$topic.messages}&nbsp;{$smarty.const._POSTS}</td>
                                             <td>{if $topic.last_post}
                #filter:timestamp_time-{$topic.last_post.timestamp}#
                <br/> {$smarty.const._BY} #filter:user_loginNoIcon-{$topic.last_post.users_LOGIN}#
                <a href = "{$smarty.server.PHP_SELF}?ctg=forum&topic={$topic.last_post.id}&view_message={$topic.last_post.id}">&raquo;</a>
                {/if}
                                             </td>
                                             <td class = "centerAlign">
                             {if $topic.status == '2'}
                <img src = "images/16x16/lock.png" alt = "{$smarty.const._LOCKED}" title = "{$smarty.const._LOCKED}"/><span style = "display:none">{$smarty.const._LOCKED}</span> {*span is used for sorting*}
                             {elseif $topic.status == '3'}
                <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._INVISIBLE}" title = "{$smarty.const._INVISIBLE}"/><span style = "display:none">{$smarty.const._INVISIBLE}</span>
                             {else}
                <img src = "images/16x16/unlocked.png" alt = "{$smarty.const._PUBLIC}" title = "{$smarty.const._PUBLIC}"/><span style = "display:none">{$smarty.const._PUBLIC}</span>
                             {/if}
                                             </td>
                                             <td class = "centerAlign">
                         {if $_change_}
                             {if $smarty.session.s_type == 'administrator' || ($smarty.session.s_login == $topic.users_LOGIN && $T_FORUMS.$current_forum.status != '2')}
                <a href = "{$smarty.server.PHP_SELF}?ctg=forum&type=topic&edit={$topic.id}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 1)" class = "editLink" target = "POPUP_FRAME"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteForumEntity(this, '{$topic.id}', 'topic')"/>
                             {/if}
                         {/if}
                                             </td>
                                         </tr>
                         {else}
         {assign var = "novisible" value = $novisible+1}
       {/if}
                     {foreachelse}
                                         <tr class = "oddRowColor defaultRowHeight"><td colspan = "6" class = "emptyCategory">{$smarty.const._NOTOPICSFOUNDINTHISFORUM}</td></tr>
                     {/foreach}
       {if $novisible == $smarty.foreach.topics_list.total && $smarty.foreach.topics_list.total != 0}
         <tr class = "oddRowColor defaultRowHeight"><td colspan = "5" class = "emptyCategory">{$smarty.const._NOVISIBLETOPICSFOUNDINTHISFORUM}</td></tr>

       {/if}
                             </table>
                 {/if}

             {else}
                 <table class = "forumTable">
                     <tr>
                         <td class = "topTitle firstColumn">{$smarty.const._FORUMS}</td>
                         <td class = "topTitle secondColumn">{$smarty.const._ACTIVITY}</td>
                         <td class = "topTitle thirdColumn">{$smarty.const._LASTPOST}</td>
       <td class = "topTitle toolsColumn">{$smarty.const._STATUS}</td>
       {if $_change_ && $_admin_}
        <td class = "topTitle toolsColumn noSort">{$smarty.const._OPERATIONS}</td>
       {/if}
                     </tr>
                    {foreach name = "subforums_list" item = "subforum" key = "key2" from = $T_FORUMS}
                        {if $subforum.parent_id == 0 && ($subforum.status != '3' || $smarty.session.s_type == 'administrator')}
                        <tr class = "{cycle name = $key values = "oddRowColor,evenRowColor"}">
                            <td>
                             <img class = "forumIcon" src = "images/32x32/forum.png" alt = "{$smarty.const._FORUM}" title = "{$smarty.const._FORUM}" />
                             <div>
                                 <a href = "{$smarty.server.PHP_SELF}?ctg=forum&forum={$subforum.id}">{$subforum.title}</a>
                                 <p>{$subforum.comments}</p>
                                </div>
                            </td>
                            <td>
        {$subforum.topics} {$smarty.const._TOPICS}, {$subforum.messages} {$smarty.const._MESSAGES}{if $subforum.polls},{$subforum.polls} {$smarty.const._POLLS}{/if}
       </td>
       <td>
                      {if $subforum.last_post}
                       #filter:timestamp_time-{$subforum.last_post.timestamp}#
                             <br/> {$smarty.const._BY} #filter:user_loginNoIcon-{$subforum.last_post.users_LOGIN}#
                             <a href = "{$smarty.server.PHP_SELF}?ctg=forum&topic={$subforum.last_post.f_topics_ID}&view_message={$subforum.last_post.id}">&raquo;</a></td>
                      {else}
                       {$smarty.const._NOFORUMPOSTSYET}
                      {/if}
       </td>
       <td class = "centerAlign">
                      {if $subforum.status == '2'}
                       <img src = "images/16x16/lock.png" alt = "{$smarty.const._LOCKED}" title = "{$smarty.const._LOCKED}"/><span style = "display:none">{$smarty.const._LOCKED}</span> {*span is used for sorting*}
                      {elseif $subforum.status == '3'}
                             <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._INVISIBLE}" title = "{$smarty.const._INVISIBLE}"/><span style = "display:none">{$smarty.const._INVISIBLE}</span>
                      {else}
                             <img src = "images/16x16/unlocked.png" alt = "{$smarty.const._PUBLIC}" title = "{$smarty.const._PUBLIC}"/><span style = "display:none">{$smarty.const._PUBLIC}</span>
                      {/if}
                         </td>
                      {if $_change_ && $_admin_}
                         <td class = "centerAlign">
                          <a href = "{$smarty.server.PHP_SELF}?ctg=forum&edit={$subforum.id}&type=forum&parent_forum_id={$T_PARENT_FORUM}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 1)" target = "POPUP_FRAME"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                             <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "deleteForumEntity(this, '{$subforum.id}', 'forum');"/>
                         </td>
                         {/if}
                     </tr>
                        {/if}
                    {foreachelse}
                     <tr class = "oddRowColor defaultRowHeight"><td colspan = "5" class = "emptyCategory">{$smarty.const._NOSUBFORUMSFOUND}</td></tr>
                 {/foreach}
                 </table>
             {/if}
         {/capture}

         {eF_template_printBlock title = $smarty.const._FORUMS data = $smarty.capture.t_forums_code image = '32x32/forum.png' options = $T_FORUM_OPTIONS}
 {/if}

{/if}

</td></tr>
     {/capture}
