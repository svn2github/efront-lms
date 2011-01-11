{if isset($T_SHOW_ROOM)}

    {capture name = "moduleShowRoom"}
                            <tr><td class = "moduleCell" >
       {assign var = 't_show_side_menu' value = true}
       {assign var = "room_title" value = $T_ROOM_TITLE|eF_truncate:30:"...":true}
       {assign var = "title" value ='&nbsp;&raquo;&nbsp;'|cat:$room_title}
                            {capture name = 't_input_code'}
                                    <table border = "0">
                                {if $smarty.get.standalone}
                                        <tr><td align = "center">{$smarty.const._ROOM}: <span class = "boldFont">{$T_ROOM_TITLE}</span></td></tr>
                                {/if}
                                        <tr>
                                            <form name = "chat_form" action = "javascript:sendMessage(document.chat_form.chat_message.value,'{$T_CHATROOMS_ID}');" method = "post">
                                            <td nowrap>{$smarty.const._MESSAGE}: <input type = "text" name = "chat_message" size = "40" valign = "middle" onpaste = "javascript: document.chat_form.submit.disabled = false;" onKeyup = "javascript:enableButton();" onMouseup = "javascript:enableButton();"/>
                                                <input type = "submit" name = "submit" value = "{$smarty.const._SEND}" class = "flatButton"/>
                                                <input type = "hidden" name = "hidden_chat_room_id" value = "{$T_CHATROOMS_ID}"/>
                                            </td>
                                            </form>
                                            <td>
                                                <a href = "smilies.php" onclick = "eF_js_showDivPopup('{$smarty.const._SMILIES}', 1)" target = "POPUP_FRAME"><img src = "images/smilies/icon_smile.gif" border = "0"/></a>
                                            </td>
                                        </tr>
                                    </table>
                            {/capture}

                            <script type="text/javascript">
                            {literal}
                            <!--
                                /*The user navigated away from the page, so log him out*/
                                function chat_logout()
                                {
                            {/literal}
                                    logout_window = window.open("{$smarty.server.PHP_SELF}?ctg=chat&chat_room_options=1&popup=1&logout=1&chatrooms_ID={$T_CHATROOMS_ID}", "chat_logout", "scrollbars=no, resizable=no, toolbar=no, menubar=no, status=no, location=no, height=5, width=5");
                            {literal}
                                    logout_window.blur();
                                }

                                /*Resize the windows so that the new one appears below the parent window*/
                                function resizeChild()
                                {
                                    parent_width = parent.document.body.clientWidth;
                                    parent_height = parent.document.body.clientHeight;
                            {/literal}
                                    new_window = window.open('{$smarty.server.PHP_SELF}?ctg=chat&chatrooms_ID={$T_CHATROOMS_ID}&standalone=1', 'standalone', 'resizable=yes, scrollbars=yes, width='+parent_width+', height=150');
                            {literal}
                                    parent.resizeTo(parent_width, parent_height - 50);
                                    parent.moveTo(1, 1);
                                    new_window.moveTo(1, parent_height - 50);
                                }
                            //-->
                            {/literal}
                            </script>
    <script language="JavaScript">
     {literal}
    <!--
    function resize_iframe()
    {

     var height=window.innerWidth;//Firefox
     if (document.body.clientHeight)
     {
      height=document.body.clientHeight-100;//IE
     }
     //resize the iframe according to the size of the
     //window (all these should be on the same line)
     document.getElementById("glu").style.height=parseInt(height-
     document.getElementById("glu").offsetTop-8)+"px";
    }

    // this will resize the iframe every
    // time you change the size of the window.
    window.onresize=resize_iframe;

    //Instead of using this you can use:
    //	<BODY onresize="resize_iframe()">


    //-->
     {/literal}
    </script>

                                    <table border = "0" width = "100%" style="height:100%;" cellpadding = "2">
                                     <tr><td>
                                            {$smarty.capture.t_input_code}
                                        </td></tr>
                                        <tr>
      <td valign = "top" style="height:100%;">
       <iframe name = "test" frameborder = "no" style="border: 1px solid #DDDDDD; " scrolling="auto" id="glu" width="100%" onload="resize_iframe()" src = "chat_blank.php" />{$smarty.const._SORRYNEEDIFRAME}</iframe>
      </td>
     </tr>

                                    </table>

                            </td></tr>
    {/capture}
{/if}

{if isset($T_SHOW_ROOM)}
    {capture name = "sideRoomOptions"}
                            {capture name = 't_functions_code'}
                                    <table>
                                        <tr><td><span class = "counter">{counter}.</span> <a href = "{$smarty.server.PHP_SELF}?ctg=chat&logout=1">{$smarty.const._EXITCHAT}</a></td></tr>
                                        {*<tr><td><span class = "counter">{counter}.</span> <a href = "javascript:void(0)" onclick = "resizeChild()">{$smarty.const._ADJUSTTOPAGE}</a></td></tr>*}
                            {if !$T_CURRENT_USER->coreAccess.chat || $T_CURRENT_USER->coreAccess.chat == 'change'}
                                {if $smarty.session.s_type != 'student'}
                                        <tr><td><span class = "counter">{counter}.</span> <a href = "{$smarty.server.PHP_SELF}?ctg=chat&chat_room_options=1&popup=1&new_public_room=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CREATEPUBLICROOM}', 1)" >{$smarty.const._CREATEPUBLICROOM}</a></td></tr>
                                {/if}
                                        <tr><td><span class = "counter">{counter}.</span> <a href = "{$smarty.server.PHP_SELF}?ctg=chat&chat_room_options=1&popup=1&new_private_room=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CREATEPRIVATEROOM}', 1)" >{$smarty.const._CREATEPRIVATEROOM}</a></td></tr>
                            {/if}
                                        <tr><td><span class = "counter">{counter}.</span> <a href = "{$smarty.server.PHP_SELF}?ctg=chat">{$smarty.const._GOTOCHATROOMSLIST}</a></td></tr>
                                    </table>
                            {/capture}

                                            {assign var = 't_rooms_span' value = '<span id = "rooms_list"></span>'}
                                            {eF_template_printSide title=$smarty.const._OPENROOMS data=$t_rooms_span id = 'open_rooms'}
                                            {eF_template_printSide title=$smarty.const._OPTIONS data=$smarty.capture.t_functions_code id = 'functions_list'}
                                            {assign var = 't_users_span' value = '<span id = "users_list"></span>'}
                                            {eF_template_printSide title=$smarty.const._OTHERUSERSINROOM data=$t_users_span id = 'other_users'}
                                            <span id = "notice"></span>
    {/capture}
{/if}

{if isset($smarty.get.chat_room_options)}
{capture name = 'chatRoomOptions'}
 <tr><td class = "moduleCell">
 {if $T_MESSAGE_TYPE == 'success'}
     <script>
         re = /\?/;
         !re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';
     </script>
 {/if}

 {if $smarty.get.new_public_room}
  {capture name = "t_new_public_room_code"}
         <form name = "new_room_form" method = "post" action = "{$smarty.server.PHP_SELF}?ctg=chat&chat_room_options=1&new_public_room=1">
         <table class = "formElements">
             <tr><td class = "labelCell">{$smarty.const._ROOMNAME}:&nbsp;</td>
                 <td class = "elementCell"><input type = "text" name = "chat_room_name" /></td></tr>
             <tr><td></td>
              <td class = "submitCell"><input class = "flatButton" type = "submit" name = "chat_room_submit" value = "{$smarty.const._CREATE}"/></td></tr>
         </table>
         <input type = "hidden" name = "chat_room_type" value = "{$T_ROOM_TYPE}" />
         </form>
  {/capture}
  {eF_template_printBlock title=$smarty.const._NEWPUBLICROOM data=$smarty.capture.t_new_public_room_code image='32x32/chat.png'}
 {/if}

 {if $smarty.get.past_messages}
  {capture name = "t_past_messages_code"}
         <form name = "chat_show_messages_form" method = "post" action = "{$smarty.server.PHP_SELF}?ctg=chat&chat_room_options=1&past_messages={$smarty.get.past_messages}{if isset($smarty.get.chat_room)}&chat_room={$smarty.get.chat_room}{/if}&popup=1">
         <table>
             <tr><td class = "labelCell">{$smarty.const._SHOWCONVERSATIONSFORROOM}:&nbsp;</td>
                 <td class = "elementCell">{if isset($T_SINGLE_ROOM_NAME)}&nbsp;<b>{$T_SINGLE_ROOM_NAME}</b>{/if}
                  <select name = "select_chat_room" {if isset($T_SINGLE_ROOM_NAME)}style="display:none"{/if}>
         {section name = 'chatroom_list' loop = $T_CHATROOMS}
                         <option value = "{$T_CHATROOMS[chatroom_list].id}" {if $smarty.post.select_chat_room == $T_CHATROOMS[chatroom_list].id}selected{/if}>{$T_CHATROOMS[chatroom_list].name}</option>
         {/section}
                     </select>
             </td></tr>
             <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
                 <td class = "elementCell">{eF_template_html_select_date prefix="from_date_" time = $T_DAY_BEFORE start_year="-5" field_order = $T_DATE_FORMATGENERAL}, {html_select_time prefix="from_time_"}</td>
             <tr><td class = "labelCell">{$smarty.const._TOCAPITAL}:&nbsp;</td>
                 <td class = "elementCell">{eF_template_html_select_date prefix="to_date_" start_year="-5" field_order = $T_DATE_FORMATGENERAL}, {html_select_time prefix="to_time_"}
             </td></tr>
             <tr style="display:none">
              <td class = "labelCell">{$smarty.const._ANDTHEMESSAGESOFUSER}:</td>
                 <td class = "elementCell"><select name = "select_user" id="select_user_id" {if $smarty.const.MSIE_BROWSER == 1}onChange="restoreSelection(this);"{/if}>
                         <option value = "0">{$smarty.const._ALLUSERS}</option>
                         {eF_template_printUsersList data = $T_USERS selected = $smarty.post.select_user}
                     </select>
             </td></tr>
             <tr><td></td>
     <td class = "submitCell">
      <input class = "flatButton" type = "submit" name = "chat_submit_show_messages" value = "{$smarty.const._SHOW}" />
          {if !isset($T_SINGLE_ROOM_NAME)} {*This is the mode for professors and students - no deletions allowed*}
               <input class = "flatButton" type = "submit" name = "chat_submit_delete_messages" value = "{$smarty.const._DELETE}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUTODELETETHISCONVERSATION}');" />
          {/if}
            <input class = "flatButton" type = "submit" name = "chat_submit_export_messages" value = "{$smarty.const._EXPORT}" />
              </td></tr>
         </table>
         {*Disappearing them here instead of changing the eF_template is used also in news.tpl*}
    {literal}
          <script>
           document.getElementsByName("from_time_Second")[0].style.display = "none";
           document.getElementsByName("to_time_Second")[0].style.display = "none";
          </script>
          {/literal}
         </form>
  {/capture}
  {eF_template_printBlock title=$smarty.const._EXPORTCHATCONVERSATIONS data=$smarty.capture.t_past_messages_code image='32x32/chat.png'}

  {if $smarty.post.chat_submit_show_messages}
         <table border = "0" align = "left">

         {foreach name = 'messages_list' item = 'message' from = $T_MESSAGES}
             {if $message.users_LOGIN == $smarty.session.s_login}
                 {assign var = 'span_class' value = 'chatMyMessages'}
             {elseif $message.users_USER_TYPE == 'student'}
                 {assign var = 'span_class' value = 'chatStudentMessages'}
             {elseif $message.users_USER_TYPE == 'professor'}
                 {assign var = 'span_class' value = 'chatProfessorMessages'}
             {elseif $message.users_USER_TYPE == 'administrator'}
                 {assign var = 'span_class' value = 'chatAdministratorMessages'}
             {else}
                 {assign var = 'span_class' value = ''}
             {/if}
             <tr><td nowrap class = "{$span_class}">#filter:timestamp_time-{$message.timestamp}#, <span class = "boldFont">#filter:login-{$message.users_LOGIN}#</span>: </td><td class = "{$span_class}">{$message.content}</td></tr>
         {foreachelse}
             <tr><td align = "center" class = "emptyCategory">{$smarty.const._NOMESSAGESFOUNDFORTHISINTERVALANDUSER}</td></tr>
         {/foreach}
         </table>
  {/if}

 {/if}

 {if $smarty.get.change_sidebar_width}
      <script>
      {literal}
      function checkNewSidebar() {
       if(!document.getElementById('sidebar_width').value.match(/^\d*$/)) {
        alert("{/literal}{$smarty.const._VALUESUBMITTEDISNOTNUMERICAL}{literal}");
        return false;
       } else {

        new_value = parseInt(document.getElementById('sidebar_width').value);
        if (new_value < 175 || new_value > 450) {
         alert("{/literal}{$smarty.const._SIDEBARVALUESMUSTBEBETWEEN} 175 {$smarty.const._AND} 450{literal}");
         return false;
        }

       }
       return true;
      }
      {/literal}
      </script>
         <form name = "change_sidebar_form" method = "post" action = "" target="_parent">
         <table>
             <tr>
              <td>{$smarty.const._SIDEBARWIDTH}:</td>
     <td><input size="32" width ="200px" type="text" name="sidebar_width" value="{$T_INITWIDTH}" id="sidebar_width" /></td>
    </tr>
    <tr>
     <td>&nbsp;</td>
     <td><input type="submit" name="new_sidebar_width" input class = "flatButton" value = "{$smarty.const._SUBMIT}" onClick="return checkNewSidebar();" /></td>
    </tr>
         </table>

         </form>
 {/if}

 {if $smarty.get.show_users}
         <table border = "0" align = "center">
             <td align = "center" class = "horizontalSeparator"><span class = "boldFont">{$T_CHATROOM_NAME}</span></td>
         {section name = "users_list" loop = $T_USERS_LIST}
             <tr><td align = "center">{$T_USERS_LIST[users_list]}</td></tr>
         {/section}
         </table>
 {/if}

</td></tr>
{/capture}

{else}
    {capture name = "moduleRoomsList"}
 <tr><td class = "moduleCell">
     {capture name = 't_public_rooms_code'}
         {if $smarty.session.s_type != 'student' && (!$T_CURRENT_USER->coreAccess.chat || $T_CURRENT_USER->coreAccess.chat == 'change')}
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDROOM}" title = "{$smarty.const._ADDROOM}"/>
     <a href = "{$smarty.server.PHP_SELF}?ctg=chat&chat_room_options=1&popup=1&new_public_room=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDROOM}', 0)">{$smarty.const._ADDROOM}</a>
    </span>
    <span>
     <img src = "images/16x16/generic.png" alt = "{$smarty.const._MANAGEPASTCOVERSATIONS}" title = "{$smarty.const._MANAGEPASTCOVERSATIONS}"/>
     <a href = "{$smarty.server.PHP_SELF}?ctg=chat&chat_room_options=1&popup=1&past_messages=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._MANAGEPASTCOVERSATIONS}', 2)">{$smarty.const._MANAGEPASTCOVERSATIONS}</a>
    </span>
    <span>
    {if $T_CHAT_ENABLED}
     <img src = "images/16x16/chat.png" alt = "{$smarty.const._DEACTIVATEEFRONTCHAT}" title = "{$smarty.const._DEACTIVATEEFRONTCHAT}"/>
           <a href = "{$smarty.server.PHP_SELF}?ctg=chat&activate_system_chat=0">{$smarty.const._DEACTIVATEEFRONTCHAT}</a>
             {else}
     <img src = "images/16x16/chat.png" class = "inactiveImage" alt = "{$smarty.const._ACTIVATEEFRONTCHAT}" title = "{$smarty.const._ACTIVATEEFRONTCHAT}"/>
              <a href = "{$smarty.server.PHP_SELF}?ctg=chat&activate_system_chat=1">{$smarty.const._ACTIVATEEFRONTCHAT}</a>
              {assign var = "T_PUBLIC_ROOMS" value = ""}{*Disable viewing of chat rooms*}
             {/if}
    </span>
   </div>
         {/if}
   <table class = "sortedTable" style = "width:100%">
    <tr class = "topTitle">
     <td>{$smarty.const._NAME}</td>
     <td>{$smarty.const._DATE}</td>
     <td class = "centerAlign">{$smarty.const._PARTICIPANTS}</td>
     <td class = "centerAlign">{$smarty.const._OPERATIONS}</td></tr>
         {section name = 'public_rooms_list' loop = $T_PUBLIC_ROOMS}
             <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
                 <td>
             {if $T_PUBLIC_ROOMS[public_rooms_list].active}
                 {capture name = 't_assign'}<img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" class = "handle">{/capture}
                  {$T_PUBLIC_ROOMS[public_rooms_list].name}
             {else}
                 {capture name = 't_assign'}<img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" class = "handle"/>{/capture}
                  <span title="{$T_PUBLIC_ROOMS[public_rooms_list].name}">{$T_PUBLIC_ROOMS[public_rooms_list].name}</span>
             {/if}
              </td>
              <td>#filter:timestamp-{$T_PUBLIC_ROOMS[public_rooms_list].create_timestamp}#</td>
              <td class = "centerAlign">{$T_PUBLIC_ROOMS[public_rooms_list].num_of_users_in_room}</td>
             {if (!$T_CURRENT_USER->coreAccess.chat || $T_CURRENT_USER->coreAccess.chat == 'change')}
      <td class = "centerAlign">
     {if $T_PUBLIC_ROOMS[public_rooms_list].lessons_ID == 0}
      <a class = "deleteLink" href = "{$smarty.server.PHP_SELF}?ctg=chat&id={$T_PUBLIC_ROOMS[public_rooms_list].id}&delete=1">
       <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" class = "handle"/></a>
     {/if}
      <a class = "activateLink" href = "{$smarty.server.PHP_SELF}?ctg=chat&id={$T_PUBLIC_ROOMS[public_rooms_list].id}&activate={if $T_PUBLIC_ROOMS[public_rooms_list].active}0{else}1{/if}">{$smarty.capture.t_assign}</a>
                 {if $T_PUBLIC_ROOMS[public_rooms_list].active}
                     <a class = "inviteLink" href = "{$smarty.server.PHP_SELF}?ctg=messages&add=1&chat_invite={$T_PUBLIC_ROOMS[public_rooms_list].id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._INVITEUSERS}', 3)">
                      <img src = "images/16x16/mail.png" alt = "{$smarty.const._INVITEUSERS}" title = "{$smarty.const._INVITEUSERS}" class = "handle"/></a>
                 {/if}
             {/if}
    {if (isset($T_PUBLIC_ROOMS[public_rooms_list].exit))}
      <a class = "exitLink" href = "{$smarty.server.PHP_SELF}?ctg=chat&logout=1&chatrooms_ID={$T_PUBLIC_ROOMS[public_rooms_list].id}">
       <img src = "images/16x16/arrow_left.png" alt = "{$smarty.const._EXIT}" title = "{$smarty.const._EXIT}" class = "handle"/></a>
    {/if}
      </td></tr>
   {sectionelse}
           <tr class = "oddRowColorDefaultRowHeight"><td colspan = "4" class = "emptyCategory">{$smarty.const._NOROOMSFOUND}</td></tr>
         {/section}
   </table>
  {/capture}
  {eF_template_printBlock title=$smarty.const._ROOMS data=$smarty.capture.t_public_rooms_code image='32x32/chat.png'}
    </td></tr>
    {/capture}
{/if}
