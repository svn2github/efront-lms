
{**************** DASHBOARD PAGE ********************}
{if $T_OP == 'dashboard'}
       {if $T_CURRENT_USER->coreAccess.calendar != 'hidden' && $T_CONFIGURATION.disable_calendar != 1}
        {capture name = "moduleCalendar"}
                                   <tr><td class = "moduleCell">
                                           {capture name='t_calendar_code'}
                                                    {if $smarty.session.s_type == "administrator"}
                                             {assign var="calendar_ctg" value = "users&edit_user=`$smarty.get.edit_user`"}
                                            {else}
                                             {assign var="calendar_ctg" value = "personal"}
                                            {/if}

                                               {eF_template_printCalendar ctg=$calendar_ctg events=$T_CALENDAR_EVENTS timestamp=$T_VIEW_CALENDAR}

                                           {/capture}
                                           {assign var="calendar_title" value = `$smarty.const._CALENDAR`&nbsp;(#filter:timestamp-`$T_VIEW_CALENDAR`#)}

                                          {eF_template_printBlock title=$calendar_title data=$smarty.capture.t_calendar_code image='32x32/calendar.png' options=$T_CALENDAR_OPTIONS link=$T_CALENDAR_LINK}

                                   </td></tr>
                                {/capture}
                            {/if}

       {if $T_FACEBOOK_ENABLED}
        {capture name = "moduleFacebook"}
                                   <tr><td class = "moduleCell">
                                           {capture name='t_facebook_code'}
                                              {if isset($T_FB_INFORMATION)}
                                              <table>
                                               <tr>
                                                <td><img src="{$T_FB_INFORMATION.pic}" /></td>
                                                <td width="10px"></td>
                                                <td>{$T_FB_INFORMATION.first_name}&nbsp;{$T_FB_INFORMATION.last_name}</td>
                                               </tr>
                                              </table>
                                              {else}
                                              <table width="100%"><tr><td class="emptyCategory">{$smarty.const._YOUARENOTCONNECTEDTOFACEBOOK} <a href="javascript:void(0);" onclick="FB.Connect.requireSession(function(){literal} { top.location='{/literal}{$smarty.session.s_type}{literal}page.php?fb_authenticated=1'; }{/literal}); return false;">{$smarty.const._HERE}</a></td></tr></table>
                                              {/if}
                                           {/capture}
                                          {eF_template_printBlock title=$smarty.const._FACEBOOKPROFILE data=$smarty.capture.t_facebook_code image='/32x32/facebook.png' options=$T_FB_OPTIONS}
                                   </td></tr>
                                {/capture}
                            {/if}

       {if isset($T_FORUM_MESSAGES) && $T_CONFIGURATION.disable_forum != 1}
        {capture name = "moduleForumList"}
                                   <tr><td class = "moduleCell">
                                           {capture name='t_forum_messages_code'}
                                               {eF_template_printForumMessages data=$T_FORUM_MESSAGES forum_lessons_ID = $T_FORUM_LESSONS_ID limit = 5}
                                           {/capture}
                                           {eF_template_printBlock title=$smarty.const._RECENTMESSAGESATFORUM data=$smarty.capture.t_forum_messages_code image='32x32/forum.png' options=$T_FORUM_OPTIONS link=$T_FORUM_LINK}
                                   </td></tr>
                                {/capture}
                            {/if}
       {if isset($T_ALL_PROJECTS) && $T_CONFIGURATION.disable_projects != 1}
        {capture name = "moduleProjectsList"}
                                   <tr><td class = "moduleCell">
                                           {capture name='t_projects_code'}
                                               {eF_template_printProjects data=$T_ALL_PROJECTS limit=5}
                                           {/capture}
                                           {eF_template_printBlock title=$smarty.const._PROJECTS data=$smarty.capture.t_projects_code image='32x32/projects.png' options=$T_PROJECTS_OPTIONS link=$T_PROJECTS_LINK}
                                   </td></tr>
                                {/capture}
                            {/if}
                            {if $T_NEWS && $T_CURRENT_USER->coreAccess.news != 'hidden' && $T_CONFIGURATION.disable_news != 1}
        {capture name = "moduleNewsList"}
                                   <tr><td class = "moduleCell">
                                           {capture name='t_news_code'}
                                         <table class = "cpanelTable">
                                         {foreach name = 'news_list' item = "item" key = "key" from = $T_NEWS}
                                          <tr><td>{$smarty.foreach.news_list.iteration}. <a title = "{$item.title}" href = "{$smarty.server.PHP_SELF}?ctg=news&view={$item.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ANNOUNCEMENT}', 1);">{$item.title}</a></td>
                                           <td class = "cpanelTime">#filter:user_login-{$item.users_LOGIN}#, <span title = "#filter:timestamp_time-{$item.timestamp}#">{$item.time_since}</span></td></tr>
                                         {foreachelse}
                                          <tr><td class = "emptyCategory">{$smarty.const._NOANNOUNCEMENTSPOSTED}</td></tr>
                                         {/foreach}
                                         </table>
                                           {/capture}
                                             {eF_template_printBlock title=$smarty.const._ANNOUNCEMENTS data=$smarty.capture.t_news_code image='32x32/announcements.png' array=$T_NEWS options = $T_NEWS_OPTIONS link = $T_NEWS_LINK}
                                   </td></tr>
                                {/capture}
                            {/if}
                            {if $T_LESSON_COMMENTS && $T_CONFIGURATION.disable_comments != 1}
        {capture name = "moduleCommentsList"}
                                   <tr><td class = "moduleCell">
                                           {capture name='t_lesson_comments_code'}
                                               {eF_template_printComments data = $T_LESSON_COMMENTS}
                                           {/capture}
                                             {eF_template_printBlock title=$smarty.const._RECENTCOMMENTS data=$smarty.capture.t_lesson_comments_code image='32x32/note.png'}
                                   </td></tr>
                                {/capture}
                            {/if}
                            {if isset($T_COMMENTS)}
        {capture name = "moduleWall"}
                                            <tr><td class = "moduleCell">

                                          {capture name = 't_my_wall'}

                         <table width="100%">
                                {foreach key = key item = comment from = $T_COMMENTS}
                                    <tr>
                                     <td class = "elementCell">
                                       <img src = "view_file.php?file={$comment.avatar}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" width = "{$comment.avatar_width}" height = "{$comment.avatar_height}" style="vertical-align:middle" />
                                     </td>
                                     <td width="100%" >&nbsp;<a href = "{$smarty.session.s_type}.php?ctg=social&op=show_profile&user={$comment.authors_LOGIN}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._USERPROFILE}', 1)" target = "POPUP_FRAME"><b>{$comment.name} {$comment.surname}</b></a>: {$comment.data|replace:"<p>":""|replace:"</p>":""} <span class="timeago">{$comment.time_ago}</span> </td>
                                     {*<td>{if $smarty.session.s_login == $comment.authors_LOGIN}<a href = "{$smarty.session.s_type}.php?ctg=social&op=comments&action=change&id={$comment.id}" onclick= "eF_js_showDivPopup('{$smarty.const._EDITCOMMENT}', 1);" target = "POPUP_FRAME"><img src="images/16x16/edit.png" title="{$smarty.const._EDIT}" alt="{$smarty.const._EDIT}" border = 0 style="vertical-align:middle" /> </a>{/if}</td>*}
                                     <td><a href = "{$smarty.session.s_type}.php?ctg=social&op=comments&action=delete&id={$comment.id}" onclick="return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETETHISCOMMENT}');"><img src="images/16x16/error_delete.png" title="{$smarty.const._DELETE}" alt="{$smarty.const._DELETE}" border = 0 style="vertical-align:middle" /> </a></td>
                                    </tr>
                                {foreachelse}
                                 <tr>
                                     <td class = "emptyCategory">{$smarty.const._NOMESSAGESHAVEBEENPOSTEDONYOURWALLYET}</td>
                                    </tr>
                             {/foreach}
                             </table>
                                          {/capture}



                                      </td></tr>
                                {/capture}
                            {/if}
       {if isset($T_MY_RELATED_USERS)}
        {capture name = "moduleRelatedPeople"}
                                         <tr><td class = "moduleCell">
                                          {capture name = 't_relatedPeople'}
                                           <table width="100%">
                                              {foreach key = key item = user from = $T_MY_RELATED_USERS}
                                               <tr>
                                                <td class = "elementCell">
                                                  <img src = "view_file.php?file={$user.avatar}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" width = "{$user.avatar_width}" height = "{$user.avatar_height}" style="vertical-align:middle" />
                                                </td>
                                                <td width="100%" >&nbsp;<a href = "{$smarty.session.s_type}.php?ctg=social&op=show_profile&user={$user.login}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._USERPROFILE}', 1)" target = "POPUP_FRAME"><b>#filter:login-{$user.login}#</b></a>{if isset($user.status) && $user.status != ''} {$user.status}{/if}</td>
                                                </tr>
                               {foreachelse}
                                <tr>
                                    <td class = "emptyCategory">{$smarty.const._NOUSERSARECURRENTLYRELATEDTOYOU}</td>
                                   </tr>
                            {/foreach}
                                           </table>
                                          {/capture}







                                         </td></tr>
                                {/capture}
                            {/if}

       {if isset($T_EVENTS)}
        {capture name = "moduleEventsList"}
                                         <tr><td class = "moduleCell">
                                         {capture name = 't_timeline'}
                                             {foreach key = key item = event from = $T_EVENTS}
                               {$event.message} <span class="timeago">{$event.time}</span> <br/>
                           {/foreach}
                                         {/capture}



                                         </td></tr>
                                {/capture}
                            {/if}
        {capture name = "moduleMessagesList"}
                                         <tr><td class = "moduleCell">
                                          {capture name = 't_messages'}
<!--ajax:messagesTable-->
                                            <table class = "sortedTable" width = "100%" height="40px" noFooter="true" size = "{$T_MESSAGES_SIZE}" sortBy = "0" useAjax = "1" id = "messagesTable" rowsPerPage="20" limit="100" url="{$smarty.server.PHP_SELF}?ctg=messages&folder={$T_FOLDER}&p_message={$T_VIEWINGMESSAGE}&minimal_view=1&" style="white-space:nowrap;">
                                                <tr class = "defaultRowHeight">
                                                 <td class = "topTitle" name="priority" style = "width:7%;text-align:center;">{$smarty.const._FLAG}</td>
                                                    <td class = "topTitle" name="viewed" style = "width:7%;text-align:center;">{$smarty.const._STATUS}</td>
                                                    <td class = "topTitle" name="title" >{$smarty.const._SUBJECT}</td>
                                                    <td class = "topTitle" name="sender" style="width:11%">{$smarty.const._FROM}</td>
                                                    <td class = "topTitle" name="timestamp" style = "width:13%">{$smarty.const._DATE}</td>
                                                    <td class = "topTitle centerAlign noSort" style="width:10%">{$smarty.const._OPERATIONS}</td>
                                                 </tr>
                                            </table>
<!--/ajax:messagesTable-->

                                          {/capture}
                                      {if $T_CONFIGURATION.disable_messages != 1}
            {eF_template_printBlock title = $smarty.const._RECENTINCOMINGMESSAGES data = $smarty.capture.t_messages image = "32x32/mail.png" options=$T_MY_INCOMING_MESSAGES_OPTIONS}
                                         {/if}
           </td></tr>
                                {/capture}

                        <div id="sortableList">
                            <div style="float: right; width:49%;height: 100%;">
                                <ul class="sortable" id="secondlist" style="height:100%;width:100%;">
 {foreach name=positions_first key=key item=module from=$T_POSITIONS_SECOND}
                     <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="secondlist_{$module}">
                         <table class = "singleColumnData">
                             {$smarty.capture.$module}
                         </table>
                     </li>
 {/foreach}

 {if !in_array('moduleCalendar', $T_POSITIONS) && $smarty.capture.moduleCalendar && $T_CONFIGURATION.disable_calendar != 1}
                     <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="secondlist_moduleCalendar">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleCalendar}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleForumList', $T_POSITIONS) && $smarty.capture.moduleForumList}
                     <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="secondlist_moduleForumList">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleForumList}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleProjectsList', $T_POSITIONS) && $smarty.capture.moduleProjectsList && $T_CONFIGURATION.disable_projects != 1}
                     <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="firstlist_moduleProjectsList">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleProjectsList}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleNewsList', $T_POSITIONS) && $smarty.capture.moduleNewsList}
                     <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="secondlist_moduleNewsList">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleNewsList}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleCommentsList', $T_POSITIONS) && $smarty.capture.moduleCommentsList && $T_CONFIGURATION.disable_comments != 1}
                     <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="secondlist_moduleCommentsList">
                         <table class = "singleColumnData">
                          {$smarty.capture.moduleCommentsList}
                      </table>
                     </li>
 {/if}
                                    <li id = "second_empty" style = "display:none;height:5px;border:1px dashed gray"></li>
                                </ul>
                            </div>

                            {****** Left column ******}
                            <div style="width:50%; height:100%;margin-left:1px;">
                                <ul class="sortable" id="firstlist" style="height:100%;width:100%;">
    {foreach name=positions_first key=key item=module from=$T_POSITIONS_FIRST}
                        <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="firstlist_{$module}">
                            <table class = "singleColumnData">
                                {$smarty.capture.$module}
                            </table>
                        </li>
    {/foreach}

 {if !in_array('moduleWall', $T_POSITIONS) && $smarty.capture.moduleWall}
                     <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="secondlist_moduleWall">
                         <table class = "singleColumnData">
                          {$smarty.capture.moduleWall}
                      </table>
                     </li>
 {/if}
 {if !in_array('moduleRelatedPeople', $T_POSITIONS) && $smarty.capture.moduleRelatedPeople}
                     <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="secondlist_moduleRelatedPeople">
                         <table class = "singleColumnData">
                          {$smarty.capture.moduleRelatedPeople}
                      </table>
                     </li>
 {/if}
 {if !in_array('moduleEventsList', $T_POSITIONS) && $smarty.capture.moduleEventsList}
                     <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="secondlist_moduleEventsList">
                         <table class = "singleColumnData">
                          {$smarty.capture.moduleEventsList}
                      </table>
                     </li>
 {/if}
 {if !in_array('moduleMessagesList', $T_POSITIONS) && $smarty.capture.moduleMessagesList}
                     <li onmousedown = "showBorders(event)" onmouseup = "hideBorders(event)" id="secondlist_moduleMessagesList">
                         <table class = "singleColumnData">
                          {$smarty.capture.moduleMessagesList}
                      </table>
                     </li>
 {/if}

         <li id = "first_empty" style = "display:none;height:5px;border:1px dashed gray"></li>
                                </ul>
                            </div>


                        </div>

    {**************** SHOW PROFILE POPUP ********************}
                {elseif $T_OP == "show_profile"}
     {capture name = "t_show_profile_code"}
                 <table width="100%">
                  <tr>
                   <td><img src = "view_file.php?file={$T_PROFILE_TO_SHOW.avatar}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" border = "0" /></td>
                   <td width= "100%" align="left"> <b>#filter:login-{$T_PROFILE_TO_SHOW.login}#</b>{if $T_PROFILE_TO_SHOW.status != ''} {$T_PROFILE_TO_SHOW.status}{/if}</td>
       {if $T_CONFIGURATION.disable_messages != 1}
        <td valign="top">
         <a class = "inviteLink" href = "{$smarty.server.PHP_SELF}?ctg=messages&add=1&recipient={$T_PROFILE_TO_SHOW.login}">
          <img src= "images/16x16/mail.png" alt="{$smarty.const._SENDMESSAGE}" title="{$smarty.const._SENDMESSAGE}" border="0"/></a>
        </td>
       {/if}
       {if $T_COMMENTS_ENABLED}
                   <td valign="top">
                       <a href = "{$smarty.session.s_type}.php?ctg=social&op=comments&action=insert&user={$T_PROFILE_TO_SHOW.login}">
                     <img src= "images/16x16/edit.png" alt="{$smarty.const._ADDCOMMENT}" title="{$smarty.const._ADDCOMMENT}" border="0"/></a>
                   </td>
                   {/if}

        {if isset($T_CURRENT_CHATROOM)}
                   <td valign="top">
                    <a class = "inviteLink" href = "{$smarty.server.PHP_SELF}?ctg=messages&add=1&chat_invite={$T_CURRENT_CHATROOM}&recipient={$T_PROFILE_TO_SHOW.login}" >
                     <img height="14" src= "images/16x16/chat.png" alt = "{$smarty.const._INVITETOCHATINCURRENTROOM}" title = "{$smarty.const._INVITETOCHATINCURRENTROOM}" border="0"/></a>
                   </td>
                   {/if}
                  </tr>
                  <tr>
                   <td colspan="2">{$T_PROFILE_TO_SHOW.short_description}</td>
                  </tr>
                 </table>


       {if isset($T_COMMENTS)}
       <br />
       <hr />
                 <table width="100%">
                        {foreach key = key item = comment from = $T_COMMENTS}
                            <tr>
                             <td class = "elementCell">
                               <img src = "view_file.php?file={$comment.avatar}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" width = "{$comment.avatar_width}" height = "{$comment.avatar_height}" style="vertical-align:middle" />
                             </td>
                             <td width="100%" >&nbsp;<a href = "{$smarty.session.s_type}.php?ctg=social&op=show_profile&user={$comment.authors_LOGIN}&popup=1" ><b>#filter:login-{$comment.login}#</b></a>: {$comment.data|replace:"<p>":""|replace:"</p>":""} <span class="timeago">{$comment.time_ago}</span> </td>
                            </tr>
                     {/foreach}
                     </table>
       {/if}
    {/capture}
     {eF_template_printBlock title = $smarty.const._USERPROFILE data = $smarty.capture.t_show_profile_code image = '32x32/profile.png'}

    {*************** PROFILE COMMENTS PAGE ********************}
    {elseif $T_OP == "comments"}


     {capture name = "t_add_user_comment"}
      {$T_COMMENTS_FORM.javascript}
      <form {$T_COMMENTS_FORM.attributes}>
          {$T_COMMENTS_FORM.hidden}
          <table class = "formElements">
              <tr><td class = "labelCell">{$T_COMMENTS_FORM.data.label}:&nbsp;</td>
               <td class = "elementCell">{$T_COMMENTS_FORM.data.html}</td></tr>
              {if $T_COMMENTS_FORM.data.error}<tr><td></td><td class = "formError">{$T_COMMENTS_FORM.data.error}</td></tr>{/if}
              <tr><td></td>
               <td class = "submitCell">{$T_COMMENTS_FORM.submit_comments.html}</td></tr>
          </table>
 {*
          <table width="100%" align="center" height="300px">
              <tr><td align="center">{$T_COMMENTS_FORM.data.label}:&nbsp;</td></tr>
              <tr><td align="center">{$T_COMMENTS_FORM.data.html}</td></tr>
              {if $T_COMMENTS_FORM.data.error}<tr><td></td><td class = "formError">{$T_COMMENTS_FORM.data.error}</td></tr>{/if}
        <tr><td>&nbsp;</td></tr>
              <tr><td align="center">
                      {$T_COMMENTS_FORM.submit_comments.html}</td></tr>
          </table>
 *}
      </form>
      {if $T_MESSAGE_TYPE == 'success'}
          <script>parent.location = parent.location;</script>
      {/if}
     {/capture}
     {eF_template_printBlock title = $smarty.const._ADDCOMMENT data = $smarty.capture.t_add_user_comment image = '32x32/billboard.png'}

                {*************** PEOPLE PAGE ********************}
    {elseif $T_OP == "people"}
     {capture name = 't_people'}

<!--ajax:peopleTable-->
                <table class = "sortedTable" style = "width:100%" size = "{$T_MY_RELATED_USERS_SIZE}" sortBy = "2" id = "peopleTable" useAjax = "1" rowsPerPage = "10" url = "{$smarty.server.PHP_SELF}?ctg=social&op=people{if isset($smarty.get.display)}&display={$smarty.get.display}{/if}&">
                    <tr style="display:none" class = "topTitle">
                        <td class = "topTitle noSort" name="description" width="35%">{$smarty.const._SKILL}</td>
                        <td class = "topTitle" name="surname" width="*">{$smarty.const._SPECIFICATION}</td>
                        <td class = "topTitle" name="timestamp" width="1">{$smarty.const._TIMESTAMP}</td>
                        <td class = "topTitle" name="common_lessons" width="*">{$smarty.const._COMMONLESSONS}</td>
                    </tr>

            {if isset($T_MY_RELATED_USERS)}
                {foreach name = 'people_list' key = 'key' item = 'user' from = $T_MY_RELATED_USERS}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
            <td class = "elementCell">
              <img src = "view_file.php?file={$user.avatar}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" width = "{$user.avatar_width}" height = "{$user.avatar_height}" style="vertical-align:middle" />
            </td>
            <td width="{if $smarty.session.s_type == "administrator"}100%{else}80%{/if}" >&nbsp;<a href = "{$smarty.session.s_type}.php?ctg=social&op=show_profile&user={$user.login}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._USERPROFILE}', 1)" target = "POPUP_FRAME"><b>#filter:login-{$user.login}#</b></a>

             {if isset($user.status) && $user.status != ''} {$user.status}{/if} {if isset($user.time_ago)} <span class="timeago">{$user.time_ago}</span>{/if}
            </td>
            <td style="display:none">
             {$user.timestamp}
            </td>
            {*<td width="*" align="right" style="white-space:nowrap">{if $smarty.session.s_type != "administrator"}{$user.common_lessons} <a href="javascript:void(0)" class="info nonEmptyLesson" onmouseover="updatePeopleInformation(this, '{$smarty.session.s_login}', '{$user.login}')" >{if $user.common_lessons>1}{$smarty.const._COMMONLESSONS}{else}{$smarty.const._COMMONLESSON}{/if}<img class = "tooltip" border = "0" src = "images/others/tooltip_arrow.gif"/><span class = "tooltipSpan" style="left:-80px;height:auto;{if $T_BROWSER == 'IE6'}width{else}min-width{/if}:140px;"});"></span></a>{/if}</td>*}
            <td width="*" align="right" style="white-space:nowrap">
            <div style="position: relative;">
        <div style="position: absolute;display: none;">
           THIS IS THE INFORMATION I WANT
        </div>
      </div>


            {if $smarty.session.s_type != "administrator"}{$user.common_lessons}
             <a href="javascript:void(0)" class="info nonEmptyLesson" onmouseover="updatePeopleInformation(this, '{$smarty.session.s_login}', '{$user.login}')" >
             {if $user.common_lessons>1}{$smarty.const._COMMONLESSONS}{else}{$smarty.const._COMMONLESSON}{/if}
             <img class = "tooltip" border = "0" src = "images/others/tooltip_arrow.gif"/>
             <span class = "tooltipSpan" style="left:-80px;height:auto;{if $T_BROWSER == 'IE6'}width{else}min-width{/if}:140px;"});"></span>
             </a>
            {/if}
            </td>
                    </tr>
                {/foreach}
                </table>
<!--/ajax:peopleTable-->
            {else}
                    <tr><td colspan = 3>
                        <table width = "100%">
                            <tr><td class = "emptyCategory">{$smarty.const._NORELATEDPEOPLEFOUND}</td></tr>
                        </table>
                        </td>
                    </tr>
                </table>
<!--/ajax:peopleTable-->
   {/if}

<!--
<BR><BR><BR><BR><BR><BR><BR><Br><Br>
<table border=1 width="100%"><tr>
 <td width="20%">&nbsp;</td>
 <td>
    <div class="bubbleInfo" id = 'bubbler1'>

        <div class="trigger" id = 'trigger1'>
         <img src="http://localhost/branches/360/www/themes/default/images/others/logo.jpg" id="download" />
        </div>

        <div id="dpop1" class="bubblePopup" ajax="ask_information.php?lessons_ID=1"></div>
    </div>
 </td>
 <td width="40%">&nbsp;</td>
 <td>
    <div class="bubbleInfo" id = 'bubbler2'>

        <div class="trigger" id = 'trigger2'>
         <img src="http://localhost/branches/360/www/themes/default/images/others/logo.jpg" id="download" />
        </div>

        <div id="dpop2" class="bubblePopup" ajax="ask_information.php?lessons_ID=2"></div>
    </div>
 </td>
</tr>
</table>
    <BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>
   -->
    {/capture}
             {eF_template_printBlock title = $smarty.const._USERSWITHCOMMONLESSONS data = $smarty.capture.t_people image = "32x32/users.png" main_options=$T_TABLE_OPTIONS}

                {*************** TIMELINES PAGE ********************}
    {elseif $T_OP == "timeline"}

     {********* TIMELINES FOR CURRENT LESSON **********}
     {if isset($smarty.get.lessons_ID)}


         {*The eF_template_printBlock for the following capture will be used only
           in the full page mode, namely where all=1 *}
         {capture name = 't_lessons_timeline_code'}


        {*The following should be always displayed *}
<!--ajax:lessonTimelineTable-->
                    <table class = "sortedTable" style = "width:100%" {if !isset($smarty.get.all)}noFooter = "true"{/if} size = "{$T_TIMELINE_EVENTS_SIZE}" sortBy = "0" id = "lessonTimelineTable" useAjax = "1" rowsPerPage = "10" limit="10" url = "{$smarty.server.PHP_SELF}?ctg=social&op=timeline&lessons_ID={$smarty.session.s_lessons_ID}&{if isset($smarty.get.all)}all=1&{/if}{if isset($smarty.get.topics_ID)}&topics_ID={$smarty.get.topics_ID}{/if}&">
                        <tr style="display:none" class = "topTitle">
                            <td class = "topTitle noSort" name="description">{$smarty.const._SKILL}</td>
                            <td class = "topTitle noSort" name="surname" >{$smarty.const._SPECIFICATION}</td>
                            <td class = "topTitle noSort" name="timestamp" >{$smarty.const._TIMESTAMP}</td>
                        </tr>

                {if isset($T_TIMELINE_EVENTS)}
                    {foreach name = 'events_list' key = 'key' item = 'event' from = $T_TIMELINE_EVENTS}
                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                <td class = "elementCell">
                  <img src = "view_file.php?file={$event.avatar}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" width = "{$event.avatar_width}" height = "{$event.avatar_height}" style="vertical-align:middle" />
                </td>
                <td width="1px">&nbsp;</td>
                <td width="100%">{if isset($event.editlink) || isset($event.deletelink)}<table width="100%"><tr><td width="97%">{/if}{$event.message} <span class="timeago">{$event.time}</span>{if isset($event.editlink)}</td><td>{$event.editlink}</td>{/if}{if isset($event.deletelink)}</td><td>{$event.deletelink}</td></tr></table>{/if}{if !isset($event.editlink) && !isset($event.deletelink)}<br/>{/if}</td>

                        </tr>
                    {/foreach}
                    </table>
<!--/ajax:lessonTimelineTable-->
                {else}
                        <tr><td colspan = 3>
                            <table width = "100%">
                                <tr><td class = "emptyCategory">{$smarty.const._NORELATEDEVENTSFOUND}</td></tr>
                            </table>
                            </td>
                        </tr>
                    </table>
<!--/ajax:lessonTimelineTable-->
       {/if}



      {/capture}



     {********* TIMELINES FOR ENTIRE SYSTEM **********}
     {else}

      {capture name = "t_system_timeline_code"}
        {*The following should be always displayed *}
<!--ajax:systemTimelineTable-->
                    <table class = "sortedTable" style = "width:100%" size = "{$T_TIMELINE_EVENTS_SIZE}" sortBy = "0" id = "systemTimelineTable" useAjax = "1" rowsPerPage = "10" url = "{$smarty.server.PHP_SELF}?ctg=social&op=timeline&">
                        <tr style="display:none" class = "topTitle">
                            <td class = "topTitle noSort" name="description">{$smarty.const._SKILL}</td>
                            <td class = "topTitle noSort" name="surname" >{$smarty.const._SPECIFICATION}</td>
                            <td class = "topTitle noSort" name="timestamp" >{$smarty.const._TIMESTAMP}</td>
                        </tr>

                {if isset($T_TIMELINE_EVENTS)}
                    {foreach name = 'events_list' key = 'key' item = 'event' from = $T_TIMELINE_EVENTS}
                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                <td class = "elementCell">
                  <img src = "view_file.php?file={$event.avatar}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" width = "{$event.avatar_width}" height = "{$event.avatar_height}" style="vertical-align:middle" />
                </td>
                <td width="1px">&nbsp;</td>
                <td width="100%">{if isset($event.editlink) || isset($event.deletelink)}<table width="100%"><tr><td width="97%">{/if}{$event.message} <span class="timeago">{$event.time}</span>{if isset($event.editlink)}</td><td>{$event.editlink}</td>{/if}{if isset($event.deletelink)}</td><td>{$event.deletelink}</td></tr></table>{/if}{if !isset($event.editlink) && !isset($event.deletelink)}<br/>{/if}</td>

                        </tr>
                    {/foreach}
                    </table>
<!--/ajax:systemTimelineTable-->
                {else}
                        <tr><td colspan = 3>
                            <table width = "100%">
                                <tr><td class = "emptyCategory">{$smarty.const._NORELATEDEVENTSFOUND}</td></tr>
                            </table>
                            </td>
                        </tr>
                    </table>
<!--/ajax:systemTimelineTable-->
       {/if}


      {/capture}




     {/if}



    {/if}

<script>
// Translations
var noMessageInFolderConst = "{$smarty.const._NOMESSAGESINFOLDER}";
var phpSelf = "{$smarty.server.PHP_SELF}";
var currentOperation ='{$T_OP}';
</script>
